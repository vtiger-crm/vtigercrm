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

require_once 'include/db_backup/Targets/Target.php';

/**
 * This class is used when the target for backup operation is a database.
 *
 * @author MAK
 */
class Database extends Target {
	/**
	 *
	 * @var ADOConnection
	 */
	private $connection = null;

	/**
	 *
	 * @var DatabaseConfig
	 */
	private $dbConfig = null;

	private $supportUTF8 = true;
	private $createTarget = false;

	public function __construct($dbConfig , $createTarget = false, $supportUTF8 = true) {
		$this->dbConfig = $dbConfig;
		$this->supportUTF8 = $supportUTF8;
		$this->createTarget = $createTarget;
	}

	function  getCreateTarget() {
		return $this->createTarget;
	}

	function isType($type) {
		$type = strtolower($type);
		return (strcmp($this->dbConfig->getDBType(), $type) === 0);
	}

	function setup() {
		if($this->isType('mysql')) {
			if($this->getCreateTarget() == true) {
				$this->connection = &NewADOConnection($this->dbConfig->getDBType());
				$ok = $this->connection->NConnect($this->dbConfig->getHostName(),
					$this->dbConfig->getRootUserName(),	$this->dbConfig->getRootPassword());
				if(!$ok){
					throw new DatabaseBackupException(DatabaseBackupErrorCode::$DB_CONNECT_ERROR,
						DatabaseBackup::$langString['DestConnectFailed']);
				}
				// Drop database if already exists
				$sql = "drop database IF EXISTS ".$this->dbConfig->getDatabaseName();
				$result = $this->connection->Execute($sql);
				$this->checkError($result,$sql);

				$sql = 'create database '.$this->dbConfig->getDatabaseName();
				if( $this->supportUTF8 == true){
					$sql .= " default character set utf8 default collate utf8_general_ci";
				}
				$result = $this->connection->Execute($sql);
				$this->checkError($result,$sql);
				$this->connection->Close();
			}
			$this->connection = &NewADOConnection($this->dbConfig->getDBType());
			$ok = $this->connection->NConnect($this->dbConfig->getHostName(),
				$this->dbConfig->getUserName(), $this->dbConfig->getPassword(),
				$this->dbConfig->getDatabaseName());
			if(!$ok){
				throw new DatabaseBackupException(DatabaseBackupErrorCode::$DB_CONNECT_ERROR,
					DatabaseBackup::$langString['DestConnectFailed']);
			}
			$result = $this->connection->_Execute("SET interactive_timeout=28800",false);
			$result = $this->connection->_Execute("SET wait_timeout=28800",false);
			$result = $this->connection->_Execute("SET net_write_timeout=900",false);
		}
	}

	public function startBackup() {
		if($this->isType('mysql')){
			$this->setup();
			$sql = "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0";
			$result = $this->connection->_Execute($sql,false);
			$this->checkError($result,$sql);
			$sql = "SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';";
			$result = $this->connection->_Execute($sql,false);
			$this->checkError($result,$sql);
			$sql = 'SET NAMES utf8';
			$result = $this->connection->_Execute($sql,false);
			$this->checkError($result,$sql);
		}
	}

	public function startTableBackup($data) {
		//Nothing to do here
	}

	public function processTableCreateStatement($data) {
		$tableName = $data[0];
		$stmt = $data[1];
		$this->processStatement(array($stmt));
	}

	public function processStatement($data) {
		$stmt = $data[0];
		if(empty($stmt)) {
			return;
		}
		$result = $this->connection->_Execute($stmt,false);
		$this->checkError($result,$stmt);
	}

	public function finishTableBackup($data) {
		//Nothing to do here
	}

	public function finishBackup() {
		if( $this->isType('mysql')) {
			$sql = "SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS";
			$result = $this->connection->_Execute($sql,false);
			$this->checkError($result,$sql);
			$sql = "SET SQL_MODE=@OLD_SQL_MODE;";
			$result = $this->connection->_Execute($sql,false);
			$this->checkError($result,$sql);
		}
	}

	function checkError($result,$sql){
		if($result === false){
			throw new DatabaseBackupException(DatabaseBackupErrorCode::$SQL_EXECUTION_ERROR,
				DatabaseBackup::$langString['SqlExecutionError']."\n<br>\n".$sql.
					"\n<br>\n".$this->connection->ErrorNo()."\n<br>\n".
					$this->connection->ErrorMsg());
		}
	}

	function isUTF8SupportEnabled() {
		return $this->supportUTF8;
	}

}
?>