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

require_once 'include/db_backup/Source/BackupSource.php';
require_once 'adodb/adodb-lib.inc.php';

/**
 * Description of MysqlSource
 *
 * @author MAK
 */
class MysqlSource extends BackupSource {

	private $currentStage = -1;
	private $connection = null;
	private $dbConfig = null;
	private $tableNameList = null;
	private $result = null;
	private $start = 0;
	private $currentTable;
	private $valueListSize = 60000;

	public function __construct($dbConfig) {
		$this->dbConfig = $dbConfig;
		$this->currentStage = -1;
		$this->tableNameList = null;
		$this->result = null;
		$this->start = 0;
		$this->setup();
	}

	private function setup() {
		$this->connection = &NewADOConnection($this->dbConfig->getDBType());
		$ok = $this->connection->NConnect($this->dbConfig->getHostName(),$this->dbConfig->getUserName(),
			$this->dbConfig->getPassword(),$this->dbConfig->getDatabaseName());
		if(!$ok){
			throw new DatabaseBackupException(DatabaseBackupErrorCode::$DB_CONNECT_ERROR,
				DatabaseBackup::$langString['SourceConnectFailed']);
		}
		$this->connection->_Execute("SET NAMES 'utf8'",false);
		$result = $this->connection->_Execute("SET interactive_timeout=28800",false);
		$result = $this->connection->_Execute("SET wait_timeout=28800",false);
		$result = $this->connection->_Execute("SET net_write_timeout=900",false);
		$result = $this->connection->_Execute("SET net_read_timeout=900",false);
	}

	public function next() {
		$this->currentStage = $this->getNextStage($this->currentStage);
		$data = $this->getStageData($this->currentStage);
		return array('stage'=>$this->currentStage,'data'=>$data);
	}

	public function valid() {
		$nextStage = $this->getNextStage($this->currentStage);
		return ($nextStage !== false);
	}

	public function getNextStage($stage) {
		switch($stage) {
			case -1: return $this->startBackupStage;
			case $this->startBackupStage: return $this->tableCreateStage;
			case $this->tableCreateStage: return $this->startTableBackupStage;
			case $this->startTableBackupStage: return $this->processStatementStage;
				break;
			case $this->processStatementStage: $rowCount = $this->result->RecordCount();
				if($this->start < $rowCount) {
					return $this->processStatementStage;
				}else{
					return $this->finishTableBackupStage;
				}
			case $this->finishTableBackupStage: $index = array_search($this->currentTable,
					$this->tableNameList);
				if($index+1 == count($this->tableNameList)) {
					return $this->finishBackupStage;
				}else{
					return $this->tableCreateStage;
				}
			case $this->finishBackupStage: return false;
		}
	}

	function getStageData($stage) {
		switch($stage) {
			case $this->startBackupStage: $this->initTableList();
				return array(null);
			case $this->tableCreateStage: if(empty($this->currentTable)) {
					$index = -1;
				} else {
					$index = array_search($this->currentTable,
						$this->tableNameList);
				}
				$this->setCurrentTable($this->tableNameList[$index+1]);
				$stmt = $this->getTableCreateStatement($this->currentTable);
					return array($this->currentTable,$stmt);
			case $this->startTableBackupStage: return array($this->currentTable);
				break;
			case $this->processStatementStage: return array(
					$this->getInsertStatement($this->currentTable)
				);
				break;
			case $this->finishTableBackupStage: return array($this->currentTable);
				break;
			case $this->finishBackupStage: return array(null);
		}
	}

	function initTableList() {
		$this->tableNameList = $this->connection->MetaTables('TABLES');
		if($this->tableNameList === false){
			throw new DatabaseBackupException(DatabaseBackupErrorCode::$TABLE_NAME_ERROR,
				DatabaseBackup::$langString['TableListFetchError']);
		}
	}

	function setCurrentTable($tableName) {
		$this->result = null;
		$this->start = 0;
		$this->currentTable = $tableName;
	}

	function getInsertStatement($tableName) {
		if(empty($this->result)) {
			$sql = "select * from $tableName";
			$this->result = $this->connection->_Execute($sql,false);
			$this->checkError($result,$sql.' '.$this->connection->ErrorMsg().' '.
				$this->connection->ErrorNo());
			$this->start = 0;
		}
		$rowCount = $this->result->RecordCount();
		$i =0;
		$sql = 'INSERT INTO '.$tableName;//
		while($i < $this->valueListSize && $this->start < $rowCount) {
			$row = $this->result->GetRowAssoc(2);
			if($i ==0 ) {
				$sql .= ' ( '.$this->getInsertNames($this->connection, $this->result, $row).' )';
				$sql .= ' VALUES ';
			}
			$values = $this->getInsertValues($this->connection, $this->result, $row);
			if($i > 0) {
				$sql .= ', ';
			}
			$sql .= '( '.$values.' )';
			$i++;
			$this->start++;
			$this->result->MoveNext();
		}
		if($i ==0) {
			return '';
		}
		$sql .= ';';
		return $sql;
	}

	function getTableCreateStatement($tableName) {
		$sql = "show create table $tableName";
		$result = $this->connection->_Execute($sql,false);
		$this->checkError($result,$sql.' '.$this->connection->ErrorMsg().' '.
				$this->connection->ErrorNo());
		$data = $result->FetchRow();
		return $data[1];
	}

	static $cacheRS = false;
	static $cacheSig = 0;
	static $cacheCols;


	function getInsertNames(&$zthis,&$rs,$arrFields,$magicq=false,$force=2) {
		$tableName = '';
		$values = '';
		$fields = '';
		$recordSet = null;
		$arrFields = _array_change_key_case($arrFields);
		$fieldInsertedCount = 0;

		if (is_string($rs)) {
			//ok we have a table name
			//try and get the column info ourself.
			$tableName = $rs;

			//we need an object for the recordSet
			//because we have to call MetaType.
			//php can't do a $rsclass::MetaType()
			$rsclass = $zthis->rsPrefix.$zthis->databaseType;
			$recordSet = new $rsclass(-1,$zthis->fetchMode);
			$recordSet->connection = &$zthis;

			if (is_string(self::$cacheRS) && self::$cacheRS == $rs) {
				$columns =& self::$cacheCols;
			} else {
				$columns = $zthis->MetaColumns( $tableName );
				self::$cacheRS = $tableName;
				self::$cacheCols = $columns;
			}
		} else if (is_subclass_of($rs, 'adorecordset')) {
			if (isset($rs->insertSig) && is_integer(self::$cacheRS) &&
					self::$cacheRS == $rs->insertSig) {
				$columns =& self::$cacheCols;
			} else {
				for ($i=0, $max=$rs->FieldCount(); $i < $max; $i++)
					$columns[] = $rs->FetchField($i);
			self::$cacheRS = self::$cacheSig;
			self::$cacheCols = $columns;
			$rs->insertSig = self::$cacheSig++;
		}
		$recordSet =& $rs;
		} else {
			printf(ADODB_BAD_RS,'GetInsertSQL');
			return false;
		}

		// Loop through all of the fields in the recordset
		foreach( $columns as $field ) {
			$upperfname = strtoupper($field->name);
			if (adodb_key_exists($upperfname,$arrFields,$force)) {
				$bad = false;
				if (strpos($upperfname,' ') !== false)
					$fnameq = $zthis->nameQuote.$upperfname.$zthis->nameQuote;
				else
					$fnameq = $upperfname;

				// Get the name of the fields to insert
				$fields[] = $fnameq;
			}
		}

		$fields = implode(', ',$fields);
		return $fields;
	}

	function getInsertValues(&$zthis,&$rs,$arrFields,$magicq=false,$force=2) {
		$tableName = '';
		$values = '';
		$fields = '';
		$recordSet = null;
		$arrFields = _array_change_key_case($arrFields);
		$fieldInsertedCount = 0;

		if (is_string($rs)) {
			//ok we have a table name
			//try and get the column info ourself.
			$tableName = $rs;

			//we need an object for the recordSet
			//because we have to call MetaType.
			//php can't do a $rsclass::MetaType()
			$rsclass = $zthis->rsPrefix.$zthis->databaseType;
			$recordSet = new $rsclass(-1,$zthis->fetchMode);
			$recordSet->connection = &$zthis;

			if (is_string(self::$cacheRS) && self::$cacheRS == $rs) {
				$columns =& self::$cacheCols;
			} else {
				$columns = $zthis->MetaColumns( $tableName );
				self::$cacheRS = $tableName;
				self::$cacheCols = $columns;
			}
		} else if (is_subclass_of($rs, 'adorecordset')) {
			if (isset($rs->insertSig) && is_integer(self::$cacheRS) &&
					self::$cacheRS == $rs->insertSig) {
				$columns =& self::$cacheCols;
			} else {
				for ($i=0, $max=$rs->FieldCount(); $i < $max; $i++)
					$columns[] = $rs->FetchField($i);
			self::$cacheRS = self::$cacheSig;
			self::$cacheCols = $columns;
			$rs->insertSig = self::$cacheSig++;
		}
		$recordSet =& $rs;
		} else {
			printf(ADODB_BAD_RS,'GetInsertSQL');
			return false;
		}

		// Loop through all of the fields in the recordset
		foreach( $columns as $field ) {
			$upperfname = strtoupper($field->name);
			if (adodb_key_exists($upperfname,$arrFields,$force)) {
				$bad = false;
				if (strpos($upperfname,' ') !== false)
					$fnameq = $zthis->nameQuote.$upperfname.$zthis->nameQuote;
				else
					$fnameq = $upperfname;

				$type = $recordSet->MetaType($field->type);

				if (is_null($arrFields[$upperfname])
					|| (empty($arrFields[$upperfname]) && strlen($arrFields[$upperfname]) == 0)
					|| $arrFields[$upperfname] === 'null') {
                    switch ($force) {
                        case 0: // we must always set null if missing
							$bad = true;
							break;

                        case 1:
                            $values  .= "null, ";
							break;

                        case 2:
                            //Set empty
                            $arrFields[$upperfname] = "";
                            $values .= _adodb_column_sql($zthis, 'I', $type, $upperfname, $fnameq,
									$arrFields, $magicq);
							break;
						default:
                        case 3:
                            //Set the value that was given in array, so you can give both null and
							//empty values
							if (is_null($arrFields[$upperfname]) || $arrFields[$upperfname] ===
									'null') {
								$values  .= "null, ";
							} else {
                        		$values .= _adodb_column_sql($zthis, 'I', $type, $upperfname,
										$fnameq, $arrFields, $magicq);
             				}
							break;
             		} // switch
				} else {
					//we do this so each driver can customize the sql for
					//DB specific column types.
					//Oracle needs BLOB types to be handled with a returning clause
					//postgres has special needs as well
					$values .= _adodb_column_sql($zthis, 'I', $type, $upperfname, $fnameq,
												   $arrFields, $magicq);
				}

				if ($bad) continue;
				// Set the counter for the number of fields that will be inserted.
				$fieldInsertedCount++;


				// Get the name of the fields to insert
				$fields .= $fnameq . ", ";
			}
		}

		// If there were any inserted fields then build the rest of the insert query.
		if ($fieldInsertedCount <= 0)  return false;

		// Get the table name from the existing query.
		if (!$tableName) {
			if (!empty($rs->tableName)) $tableName = $rs->tableName;
			else if (preg_match("/FROM\s+".ADODB_TABLE_REGEX."/is", $rs->sql, $tableName))
				$tableName = $tableName[1];
			else
				return false;
		}

		// Strip off the comma and space on the end of both the fields
		// and their values.
		$fields = substr($fields, 0, -2);
		$values = substr($values, 0, -2);
		return $values;
	}

	function checkError($result,$sql){
		if($result === false){
			throw new DatabaseBackupException(DatabaseBackupErrorCode::$SQL_EXECUTION_ERROR,
				DatabaseBackup::$langString['SqlExecutionError'].' '.$sql);
		}
	}

}
?>
