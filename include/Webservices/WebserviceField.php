<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

class WebserviceField{
	private $fieldId;
	private $uitype;
	private $blockId;
	private $blockName;
	private $nullable;
	private $default;
	private $tableName;
	private $columnName;
	private $fieldName;
	private $fieldLabel;
	private $editable;
	private $fieldType;
	private $displayType;
	private $mandatory;
	private $massEditable;
	private $tabid;
	private $presence;
	/**
	 *
	 * @var PearDatabase
	 */
	private $pearDB;
	private $typeOfData;
	private $fieldDataType;
	private $dataFromMeta;
	private static $tableMeta = array();
	private static $fieldTypeMapping = array();
	private $referenceList;
	private $defaultValuePresent;
	private $explicitDefaultValue;
	
	private $genericUIType = 10;
	
	private function __construct($adb,$row){
		$this->uitype = $row['uitype'];
		$this->blockId = $row['block'];
		$this->blockName = null;
		$this->tableName = $row['tablename'];
		$this->columnName = $row['columnname'];
		$this->fieldName = $row['fieldname'];
		$this->fieldLabel = $row['fieldlabel'];
		$this->displayType = $row['displaytype'];
		$this->massEditable = ($row['masseditable'] === 1)? true: false;
		$typeOfData = $row['typeofdata'];
		$this->presence = $row['presence'];
		$this->typeOfData = $typeOfData;
		$typeOfData = explode("~",$typeOfData);
		$this->mandatory = ($typeOfData[1] == 'M')? true: false;
		if($this->uitype == 4){
			$this->mandatory = false;
		}
		$this->fieldType = $typeOfData[0];
		$this->tabid = $row['tabid'];
		$this->fieldId = $row['fieldid'];
		$this->pearDB = $adb;
		$this->fieldDataType = null;
		$this->dataFromMeta = false;
		$this->defaultValuePresent = false;
		$this->referenceList = null;
		$this->explicitDefaultValue = false;
	}
	
	public static function fromQueryResult($adb,$result,$rowNumber){
		 return new WebserviceField($adb,$adb->query_result_rowdata($result,$rowNumber));
	}
	
	public static function fromArray($adb,$row){
		return new WebserviceField($adb,$row);
	}
	
	public function getTableName(){
		return $this->tableName;
	}
	
	public function getFieldName(){
		return $this->fieldName;
	}
	
	public function getFieldLabelKey(){
		return $this->fieldLabel;
	}
	
	public function getFieldType(){
		return $this->fieldType;
	}
	
	public function isMandatory(){
		return $this->mandatory;
	}
	
	public function getTypeOfData(){
		return $this->typeOfData;
	}
	
	public function getDisplayType(){
		return $this->displayType;
	}
	
	public function getMassEditable(){
		return $this->massEditable;
	}
	
	public function getFieldId(){
		return $this->fieldId;
	}
	
	public function getDefault(){
		if($this->dataFromMeta !== true && $this->explicitDefaultValue !== true){
			$this->fillColumnMeta();
		}
		return $this->default;
	}
	
	public function getColumnName(){
		return $this->columnName;
	}
	
	public function getBlockId(){
		return $this->blockId;
	}
	
	public function getBlockName(){
		if(empty($this->blockName)) {
			$this->blockName = getBlockName($this->blockId);
		}
		return $this->blockName;
	}

	public function isNullable(){
		if($this->dataFromMeta !== true){
			$this->fillColumnMeta();
		}
		return $this->nullable;
	}
	
	public function hasDefault(){
		if($this->dataFromMeta !== true && $this->explicitDefaultValue !== true){
			$this->fillColumnMeta();
		}
		return $this->defaultValuePresent;
	}
	
	public function getUIType(){
		return $this->uitype;
	}
	
	private function setNullable($nullable){
		$this->nullable = $nullable;
	}
	
	public function setDefault($value){
		$this->default = $value;
		$this->explicitDefaultValue = true;
		$this->defaultValuePresent = true;
	}
	
	public function setFieldDataType($dataType){
		$this->fieldDataType = $dataType;
	}
	
	public function setReferenceList($referenceList){
		$this->referenceList = $referenceList;
	}
	
	public function getTableFields(){
		$tableFields = null;
		if(isset(WebserviceField::$tableMeta[$this->getTableName()])){
			$tableFields = WebserviceField::$tableMeta[$this->getTableName()];
		}else{
			$dbMetaColumns = $this->pearDB->database->MetaColumns($this->getTableName());
			$tableFields = array();
			foreach ($dbMetaColumns as $key => $dbField) {
				$tableFields[$dbField->name] = $dbField;
			}
			WebserviceField::$tableMeta[$this->getTableName()] = $tableFields;
		}
		return $tableFields;
	}
	public function fillColumnMeta(){
		$tableFields = $this->getTableFields();
		foreach ($tableFields as $fieldName => $dbField) {
			if(strcmp($fieldName,$this->getColumnName())===0){
				$this->setNullable(!$dbField->not_null);
				if($dbField->has_default === true){
					$this->defaultValuePresent = $dbField->has_default;
					$this->setDefault($dbField->default_value);
				}
			}
		}
		$this->dataFromMeta = true;
	}
	
	public function getFieldDataType(){
		if($this->fieldDataType === null){
			$fieldDataType = $this->getFieldTypeFromUIType();
			if($fieldDataType === null){
				$fieldDataType = $this->getFieldTypeFromTypeOfData();
			}
			if($fieldDataType == 'date' || $fieldDataType == 'datetime' || $fieldDataType == 'time') {
				$tableFieldDataType = $this->getFieldTypeFromTable();
				if($tableFieldDataType == 'datetime'){
					$fieldDataType = $tableFieldDataType;
				}
			}
			$this->fieldDataType = $fieldDataType;
		}
		return $this->fieldDataType;
	}
	
	public function getReferenceList(){
		static $referenceList = array();
		if($this->referenceList === null){
			if(isset($referenceList[$this->getFieldId()])){
				$this->referenceList = $referenceList[$this->getFieldId()];
				return $referenceList[$this->getFieldId()];
			}
			if(!isset(WebserviceField::$fieldTypeMapping[$this->getUIType()])){
				$this->getFieldTypeFromUIType();
			}
			$fieldTypeData = WebserviceField::$fieldTypeMapping[$this->getUIType()];
			$referenceTypes = array();
			if($this->getUIType() != $this->genericUIType){
				$sql = "select * from vtiger_ws_referencetype where fieldtypeid=?";
				$params = array($fieldTypeData['fieldtypeid']);
			}else{
				$sql = 'select relmodule as type from vtiger_fieldmodulerel where fieldid=?';
				$params = array($this->getFieldId());
			}
			$result = $this->pearDB->pquery($sql,$params);
			$numRows = $this->pearDB->num_rows($result);
			for($i=0;$i<$numRows;++$i){
				array_push($referenceTypes,$this->pearDB->query_result($result,$i,"type"));
			}
			
			//to handle hardcoding done for Calendar module todo activities.
			if($this->tabid == 9 && $this->fieldName =='parent_id'){
				$referenceTypes[] = 'Invoice';
				$referenceTypes[] = 'Quotes';
				$referenceTypes[] = 'PurchaseOrder';
				$referenceTypes[] = 'SalesOrder';
				$referenceTypes[] = 'Campaigns';
			}
			
			$referenceList[$this->getFieldId()] = $referenceTypes;
			$this->referenceList = $referenceTypes;
			return $referenceTypes;
		}
		return $this->referenceList;
	}
	
	private function getFieldTypeFromTable(){
		$tableFields = $this->getTableFields();
		foreach ($tableFields as $fieldName => $dbField) {
			if(strcmp($fieldName,$this->getColumnName())===0){
				return $dbField->type;
			}
		}
		//This should not be returned if entries in DB are correct.
		return null;
	}
	
	private function getFieldTypeFromTypeOfData(){
		switch($this->fieldType){
			case 'T': return "time";
			case 'D':
			case 'DT': return "date";
			case 'E': return "email";
			case 'N':
			case 'NN': return "double";
			case 'P': return "password";
			case 'I': return "integer";
			case 'V':
			default: return "string";
		}
	}
	
	private function getFieldTypeFromUIType(){
		
		// Cache all the information for futher re-use
		if(empty(self::$fieldTypeMapping)) {
			$result = $this->pearDB->pquery("select * from vtiger_ws_fieldtype", array());
			while($resultrow = $this->pearDB->fetch_array($result)) {
				self::$fieldTypeMapping[$resultrow['uitype']] = $resultrow;
			}
		}
		
		if(isset(WebserviceField::$fieldTypeMapping[$this->getUIType()])){
			if(WebserviceField::$fieldTypeMapping[$this->getUIType()] === false){
				return null;
			}
			$row = WebserviceField::$fieldTypeMapping[$this->getUIType()];
			return $row['fieldtype'];
		} else {
			WebserviceField::$fieldTypeMapping[$this->getUIType()] = false;
			return null;
		}
	}

	function getPresence() {
		return $this->presence;
	}

}

?>