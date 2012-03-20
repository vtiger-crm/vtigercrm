<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
include_once('adodb/adodb.inc.php');

$langString = array(
	'SourceConnectFailed'=>'Source database connect failed',
	'DestConnectFailed'=>'Destination database connect failed',
	'TableListFetchError'=>'Failed to get Table List for database',
	'SqlExecutionError'=>'Execution of following query failed',
);

class DatabaseConfig{
	private $hostName = null;
	private $username = null;
	private $password = null;
	private $dbName = null;
	private $rootUsername = null;
	private $rootPassword = null;
	private $dbType = null;
	function DatabaseConfig($dbserver, $username, $password,$dbName, $dbType = 'mysql',
			$rootusername='', $rootpassword=''){
		$this->hostName = $dbserver;
		$this->username = $username;
		$this->password = $password;
		$this->dbName = $dbName;
		$this->rootUsername = $rootusername;
		$this->rootPassword = $rootpassword;
		$this->dbType = $dbType;
	}
	
	/**
	 *
	 * @return DatabaseConfig
	 */
	public static function getInstanceFromConfigFile() {
		require 'config.inc.php';
		$config = new DatabaseConfig($dbconfig['db_hostname'], $dbconfig['db_username'],
				$dbconfig['db_password'], $dbconfig['db_name'], $dbconfig['db_type'],
				$dbconfig['db_username'], $dbconfig['db_password']);
		return $config;
	}

	/**
	 *
	 * @param DatabaseConfig $config
	 * @return DatabaseConfig
	 */
	public static function getInstanceFromOtherConfig($config) {
		$newConfig = new DatabaseConfig($config->getHostName(), $config->getUsername(),
				$config->getPassword(), $config->getDatabaseName(), $config->getDBType(),
				$config->getRootUsername(), $config->getRootUsername());
		return $newConfig;
	}

	function getHostName(){
		return $this->hostName;
	}
	
	function getUsername(){
		return $this->username;
	}
	
	function getPassword(){
		return $this->password;
	}
	
	function getRootUsername(){
		return $this->rootUsername;
	}
	
	function getRootPassword(){
		return $this->rootPassword;
	}
	
	function getDatabaseName(){
		return $this->dbName;
	}

	function getDBType() {
		return $this->dbType;
	}

	function setDatabaseName($dbName) {
		$this->dbName = $dbName;
	}

	function setRootUsername($rootUsername) {
		$this->rootUsername = $rootUsername;
	}

	function setRootPassword($rootPassword) {
		$this->rootPassword = $rootPassword;
	}

}

class DatabaseBackup {

	private $source = null;
	private $target = null;
	private $skipStages = null;
	public static $langString = null;
	function DatabaseBackup($source, $target,$skipStages = array()){
		if(!is_array(DatabaseBackup::$langString)){
			DatabaseBackup::$langString = getLanguageStrings();
		}
		$this->skipStages = $skipStages;
		$this->source = $source;
		$this->target = $target;
	}
	
	function setSource($source){
		$this->source = $source;
	}
	
	function setTarget($target){
		$this->target = $target;
	}

	function backup(){
		while($this->source->valid()) {
			$info = $this->source->next();
			if(!in_array($info['stage'],$this->skipStages)) {
				$this->target->addStageData($info['stage'],$info['data']);
			}
		}
	}
	
}

function getLanguageStrings(){
	global $langString;
	return $langString;
}

?>