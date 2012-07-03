<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class VtigerCRMActorMeta extends EntityMeta {
	protected $pearDB;
	protected static $fieldTypeMapping = array();
	
	function VtigerCRMActorMeta($tableName,$webserviceObject,$adb,$user){
		parent::__construct($webserviceObject,$user);
		$this->baseTable = $tableName;
		$this->idColumn = null;
		$this->pearDB = $adb;
		
		$fieldList = $this->getTableFieldList($tableName);
		$this->moduleFields = array();
		foreach ($fieldList as $field) {
			$this->moduleFields[$field->getFieldName()] = $field;
		}
		
		$this->pearDB = $adb;
		$this->tableList = array($this->baseTable);
		$this->tableIndexList = array($this->baseTable=>$this->idColumn);
		$this->defaultTableList = array();
	}
	
	protected function getTableFieldList($tableName){
		$tableFieldList = array();
		
		$factory = WebserviceField::fromArray($this->pearDB,array('tablename'=>$tableName));
		$dbTableFields = $factory->getTableFields();
		foreach ($dbTableFields as $dbField) {
			if($dbField->primary_key){
				if($this->idColumn === null){
					$this->idColumn = $dbField->name;
				}else{
					throw new WebServiceException(WebServiceErrorCode::$UNKOWNENTITY,
						"Entity table with multi column primary key is not supported");
				}
			}
			$field = $this->getFieldArrayFromDBField($dbField,$tableName);
			$webserviceField = WebserviceField::fromArray($this->pearDB,$field);
			$fieldDataType = $this->getFieldType($dbField,$tableName);
			if($fieldDataType === null){
				$fieldDataType = $this->getFieldDataTypeFromDBType($dbField->type);
			}
			$webserviceField->setFieldDataType($fieldDataType);
			if(strcasecmp($fieldDataType,'reference') === 0){
				$webserviceField->setReferenceList($this->getReferenceList($dbField,$tableName));
			}
			array_push($tableFieldList,$webserviceField);
		}
		return $tableFieldList;
	}
	
	protected function getFieldArrayFromDBField($dbField,$tableName){
		$field = array();
		$field['fieldname'] = $dbField->name;
		$field['columnname'] = $dbField->name;
		$field['tablename'] = $tableName;
		$field['fieldlabel'] = str_replace('_', ' ',$dbField->name);
		$field['displaytype'] = 1;
		$field['uitype'] = 1;
		$fieldDataType = $this->getFieldType($dbField,$tableName);
		if($fieldDataType !== null){
			$fieldType = $this->getTypeOfDataForType($fieldDataType);
		}else{
			$fieldType = $this->getTypeOfDataForType($dbField->type);
		}
		$typeOfData = null;
		if(($dbField->not_null && !$dbField->primary_key) || $dbField->unique_key == 1){
			$typeOfData = $fieldType.'~M';
		}else{
			$typeOfData = $fieldType.'~O';
		}
		$field['typeofdata'] = $typeOfData;
		$field['tabid'] = null;
		$field['fieldid'] = null;
		$field['masseditable'] = 0;
		$field['presence'] = '0';
		return $field;
	}
	
	protected function getReferenceList($dbField, $tableName){
		static $referenceList = array();
		if(isset($referenceList[$dbField->name])){
			return $referenceList[$dbField->name];
		}
		if(!isset(VtigerCRMActorMeta::$fieldTypeMapping[$tableName][$dbField->name])){
			$this->getFieldType($dbField, $tableName);
		}
		$fieldTypeData = VtigerCRMActorMeta::$fieldTypeMapping[$tableName][$dbField->name];
		$referenceTypes = array();
		$sql = "select * from vtiger_ws_entity_referencetype where fieldtypeid=?";
		$result = $this->pearDB->pquery($sql,array($fieldTypeData['fieldtypeid']));
		$numRows = $this->pearDB->num_rows($result);
		for($i=0;$i<$numRows;++$i){
			array_push($referenceTypes,$this->pearDB->query_result($result,$i,"type"));
		}
		$referenceList[$dbField->name] = $referenceTypes;
		return $referenceTypes;
	}
	
	protected function getFieldType($dbField,$tableName){
		
		if(isset(VtigerCRMActorMeta::$fieldTypeMapping[$tableName][$dbField->name])){
			if(VtigerCRMActorMeta::$fieldTypeMapping[$tableName][$dbField->name] === 'null'){
				return null;
			}
			$row = VtigerCRMActorMeta::$fieldTypeMapping[$tableName][$dbField->name];
			return $row['fieldtype'];
		}
		$sql = "select * from vtiger_ws_entity_fieldtype where table_name=? and field_name=?;";
		$result = $this->pearDB->pquery($sql,array($tableName,$dbField->name));
		$rowCount = $this->pearDB->num_rows($result);
		if($rowCount > 0){
			$row = $this->pearDB->query_result_rowdata($result,0);
			VtigerCRMActorMeta::$fieldTypeMapping[$tableName][$dbField->name] = $row;
			return $row['fieldtype'];
		}else{
			VtigerCRMActorMeta::$fieldTypeMapping[$tableName][$dbField->name] = 'null';
			return null;
		}
	}
	
	protected function getTypeOfDataForType($type){
		switch($type){
			case 'email': return 'E';
			case 'password': return 'P';
			case 'date': return 'D';
			case 'datetime': return 'DT';
			case 'timestamp': return 'T';
			case 'int':
			case 'integer': return 'I';
			case 'decimal':
			case 'numeric': return 'N';
			case 'varchar':
			case 'text':
			default: return 'V';
		}
	}
	
	protected function getFieldDataTypeFromDBType($type){
		switch($type){
			case 'date': return 'date';
			case 'datetime': return 'datetime';
			case 'timestamp': return 'time';
			case 'int':
			case 'integer': return 'integer';
			case 'real':
			case 'decimal':
			case 'numeric': return 'double';
			case 'text': return 'text';
			case 'varchar': return 'string';
			default: return $type;
		}
	}
	
	public function hasPermission($operation,$webserviceId){
		if(is_admin($this->user)){
			return true;
		}else{
			if(strcmp($operation,EntityMeta::$RETRIEVE)===0){
				return true;
			}
			return false;
		}
	}
	
	public function hasAssignPrivilege($ownerWebserviceId){
		if(is_admin($this->user)){
			return true;
		}else{
			$idComponents = vtws_getIdComponents($webserviceId);
			$userId=$idComponents[1];
			if($this->user->id === $userId){
				return true;
			}
			return false;
		}
	}
	
	public function hasDeleteAccess(){
		if(is_admin($this->user)){
			return true;
		}else{
			return false;
		}
	}
	
	public function hasAccess(){
		return true;
	}
	
	public function hasReadAccess(){
		return true;
	}
	
	public function hasWriteAccess(){
		if(is_admin($this->user)){
			return true;
		}else{
			return false;
		}
	}
	
	public function getEntityName(){
		return $this->webserviceObject->getEntityName();
	}
	public function getEntityId(){
		return $this->webserviceObject->getEntityId();
	}
	
	function getObjectEntityName($webserviceId){
		
		$idComponents = vtws_getIdComponents($webserviceId);
		$id=$idComponents[1];

		if($this->exists($id)){
			return $this->webserviceObject->getEntityName();
		}
		return null;
	}
	
	function exists($recordId){
		$exists = false;
		$sql = 'select * from '.$this->baseTable.' where '.$this->getObectIndexColumn().'=?';
		$result = $this->pearDB->pquery($sql , array($recordId));
		if($result != null && isset($result)){
			if($this->pearDB->num_rows($result)>0){
				$exists = true;
			}
		}
		return $exists;
	}
	
	public function getNameFields(){
		$query = "select name_fields from vtiger_ws_entity_name where entity_id = ?";
		$result = $this->pearDB->pquery($query, array($this->objectId));
		$fieldNames = '';
		if($result){
			$rowCount = $this->pearDB->num_rows($result);
			if($rowCount > 0){
				$fieldNames = $this->pearDB->query_result($result,0,'name_fields');
			}
		}
		return $fieldNames;
	}
	
	public function getName($webserviceId){
		
		$idComponents = vtws_getIdComponents($webserviceId);
		$entityId = $idComponents[0];
		$id=$idComponents[1];
		
		$nameList = vtws_getActorEntityNameById($entityId, array($id));
		return $nameList[$id];
	}

	public function getEntityAccessControlQuery() {
		return '';
	}

	public function getEntityDeletedQuery() {
		if($this->getEntityName() == 'Currency'){
			return 'vtiger_currency_info.deleted=0';
		}

		return '';
	}

	public function isModuleEntity() {
		return false;
	}
}
?>