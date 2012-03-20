<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/

require_once 'include/events/SqlResultIterator.inc';

/**
 * Description of EmailTemplateUtils
 *
 * @author mak
 */
class EmailTemplate {
	protected $module;
	protected $rawDescription;
	protected $processedDescription;
	protected $recordId;
	protected $processed;
	protected $templateFields;
	protected $user;

	public function __construct($module,$description,$recordId,$user) {
		$this->module = $module;
		$this->recordId = $recordId;
		$this->processed = false;
		$this->user = $user;
		$this->setDescription($description);
		$this->processed = false;
	}

	public function setDescription($description){
		$this->rawDescription = $description;
		$this->processedDescription = $description;
		$templateVariablePair = explode('$',$this->rawDescription);
		$this->templateFields = Array();
		for($i=1;$i < count($templateVariablePair);$i+=2) {
			list($module,$fieldName) = explode('-',$templateVariablePair[$i]);
			$this->templateFields[$module][] = $fieldName;
		}
		$this->processed = false;
	}

	private function getTemplateVariableListForModule($module){
		return $this->templateFields[strtolower($module)];
	}

	public function process(){
		$variableList = $this->getTemplateVariableListForModule($this->module);
		$handler = vtws_getModuleHandlerFromName($this->module, $this->user);
		$meta = $handler->getMeta();
		$referenceFields = $meta->getReferenceFieldDetails();
		$fieldColumnMapping = $meta->getFieldColumnMapping();

		$columnTableMapping = $meta->getColumnTableMapping();

		
		$tableList = array();
		$columnList = array();
		$allColumnList = $meta->getUserAccessibleColumns();

		if(count($variableList) > 0){
			foreach ($variableList as $column) {
				if(in_array($column,$allColumnList)){
					$columnList[] = $column;
				
				}
			}
		
			foreach ($columnList as $column) {
				if(!empty($columnTableMapping[$column])){
					$tableList[$columnTableMapping[$column]]='';
				}
			}
			$tableList = array_keys($tableList);
			$defaultTableList = $meta->getEntityDefaultTableList();
			foreach ($defaultTableList as $defaultTable) {
				if(!in_array($defaultTable,$tableList)){
					$tableList[] = $defaultTable;
				}
			}

			// right now this is will be limited to module type, entities.
			// need to extend it to non-module entities when we have a reliable way of getting
			// record type from the given record id. non webservice id.
			// can extend to non-module entity without many changes as long as the reference field
			// refers to one type of entity, either module entities or non-module entities.
			if(count($tableList) > 0){
				$sql = 'select '.implode(', ', $columnList).' from '.$tableList[0];
				$moduleTableIndexList = $meta->getEntityTableIndexList();
				foreach ($tableList as $index=>$tableName) {
					if($tableName != $tableList[0]){
						$sql .=' INNER JOIN '.$tableName.' ON '.$tableList[0].'.'.
						$moduleTableIndexList[$tableList[0]].'='.$tableName.'.'.
						$moduleTableIndexList[$tableName];
					}
				}
				$sql .= ' WHERE';
				$deleteQuery = $meta->getEntityDeletedQuery();
				if(!empty($deleteQuery)){
					$sql .= ' '.$meta->getEntityDeletedQuery().' AND';
				}
				$sql .= ' '.$tableList[0].'.'.$moduleTableIndexList[$tableList[0]].'=?';
				$params = array($this->recordId);
				$db = PearDatabase::getInstance();
				$result = $db->pquery($sql, $params);
				$it = new SqlResultIterator($db, $result);
				//assuming there can only be one row.
				$values = array();
				foreach ($it as $row) {
					foreach ($columnList as $column) {
						$values[$column] = $row->get($column);
					}
				}
				$moduleFields = $meta->getModuleFields();
				foreach ($moduleFields as $fieldName=>$webserviceField) {
					if(isset($values[$fieldColumnMapping[$fieldName]]) &&
						$values[$fieldColumnMapping[$fieldName]] !== null){
						if(strcasecmp($webserviceField->getFieldDataType(),'reference') === 0){
							$details = $webserviceField->getReferenceList();
							if(count($details)==1){
								$referencedObjectHandler = vtws_getModuleHandlerFromName(
									$details[0],$this->user);
							}else{
								$type = getSalesEntityType(
										$values[$fieldColumnMapping[$fieldName]]);
								$referencedObjectHandler = vtws_getModuleHandlerFromName($type,
										$this->user);
							}
							$referencedObjectMeta = $referencedObjectHandler->getMeta();
							$values[$fieldColumnMapping[$fieldName]] =
								$referencedObjectMeta->getName(vtws_getId(
									$referencedObjectMeta->getEntityId(),
										$values[$fieldColumnMapping[$fieldName]]));
						}elseif(strcasecmp($webserviceField->getFieldDataType(),'owner') === 0){
							$referencedObjectHandler = vtws_getModuleHandlerFromName(
								vtws_getOwnerType($values[$fieldColumnMapping[$fieldName]]),
									$this->user);
							$referencedObjectMeta = $referencedObjectHandler->getMeta();
							$values[$fieldColumnMapping[$fieldName]] =
								$referencedObjectMeta->getName(vtws_getId(
									$referencedObjectMeta->getEntityId(),
										$values[$fieldColumnMapping[$fieldName]]));
						}elseif(strcasecmp($webserviceField->getFieldDataType(),'picklist') === 0){
							$values[$fieldColumnMapping[$fieldName]] = getTranslatedString(
								$values[$fieldColumnMapping[$fieldName]], $this->module);
						}
					}
				}
				foreach ($columnList as $column) {
					$needle = '$'.strtolower($this->module)."-$column$";
					$this->processedDescription = str_replace($needle,
						$values[$column],$this->processedDescription);
				}
			}
		}
		$this->processed = true;
	}

	public function getProcessedDescription(){
		if(!$this->processed){
			$this->process();
		}
		return $this->processedDescription;
	}

}
?>
