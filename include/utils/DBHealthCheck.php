<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

class DBHealthCheck {
	var $db;	
	var $dbType;
	var $dbName;
	var $dbHostName;
	var $recommendedEngineType = 'InnoDB';
	
	function DBHealthCheck($db) {
		$this->db = $db;
		$this->dbType = $db->databaseType;
		$this->dbName = $db->databaseName;
		$this->dbHostName = $db->host;
	}
	 
	function isMySQL() { return (stripos($this->dbType ,'mysql') === 0);}
    function isOracle() { return $this->dbType=='oci8'; }
    function isPostgres() { return $this->dbType=='pgsql'; }
    
	function isDBHealthy() {
		$tablesList = $this->getUnhealthyTablesList();
		if (count($tablesList) > 0) {
			return false;
		}
		return true;
	}
	
	function getUnhealthyTablesList() {
		$tablesList = array();
		if($this->isMySql()) {
			$tablesList = $this->_mysql_getUnhealthyTables();
		}
		return $tablesList;
	}
	
	function updateTableEngineType($tableName) {
		if($this->isMySql()) {
			$this->_mysql_updateEngineType($tableName);
		}
	}
	
	function updateAllTablesEngineType() {
		if($this->isMySql()) {
			$this->_mysql_updateEngineTypeForAllTables();
		}
	}
	
	function _mysql_getUnhealthyTables() {
		$tablesResult = $this->db->_Execute("SHOW TABLE STATUS FROM `$this->dbName`");
		$noOfTables = $tablesResult->NumRows($tablesResult);
		$unHealthyTables = array();
		$i=0;
		for($j=0; $j<$noOfTables; ++$j) {
			$tableInfo = $tablesResult->GetRowAssoc(0);
			$isHealthy = false;
			// If already InnoDB type, skip it.
			if ($tableInfo['engine'] == 'InnoDB') {
				$isHealthy = true;
			}
			// If table is a sequence table, then skip it.
			$tableNameParts = explode("_",$tableInfo['name']);
			$tableNamePartsCount = count($tableNameParts);
			if ($tableNameParts[$tableNamePartsCount-1] == 'seq') {
				$isHealthy = true;
			}
			
			if(!$isHealthy) {
				$unHealthyTables[$i]['name'] = $tableInfo['name'];
				$unHealthyTables[$i]['engine'] = $tableInfo['engine'];
				$unHealthyTables[$i]['autoincrementValue'] = $tableInfo['auto_increment'];
				$tableCollation = $tableInfo['collation'];
				$unHealthyTables[$i]['characterset'] = substr($tableCollation, 0, strpos($tableCollation,'_'));
				$unHealthyTables[$i]['collation'] = $tableCollation;
				$unHealthyTables[$i]['createOptions'] = $tableInfo['create_options'];
				++$i;
			}
			$tablesResult->MoveNext();
		}
		return $unHealthyTables;
	}
	
	function _mysql_updateEngineType($tableName) {
		$this->db->_Execute("ALTER TABLE $tableName ENGINE=$this->recommendedEngineType");
	}
	
	function _mysql_updateEngineTypeForAllTables() {
		$unHealthyTables = $this->_mysql_getUnhealthyTables();
		$noOfTables = count($unHealthyTables);
		for($i=0; $i<$noOfTables; ++$i) {
			$tableName = $unHealthyTables[$i]['name'];
			$this->db->_Execute("ALTER TABLE $tableName ENGINE=$this->recommendedEngineType");
		}		
	}
}
?>