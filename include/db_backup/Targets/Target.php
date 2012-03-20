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

require_once 'include/db_backup/StagedBackup.php';

/**
 * Description of Target
 *
 * @author MAK
 */
abstract class Target extends StagedBackup {
	abstract public function startBackup();
	abstract public function processTableCreateStatement($data);
	abstract public function startTableBackup($tableName);
	abstract public function processStatement($stmt);
	abstract public function finishTableBackup($tableName);
	abstract public function finishBackup();

	function addStageData($stage,$data) {
		switch($stage) {
			case $this->startBackupStage: $this->startBackup();
				break;
			case $this->tableCreateStage: $this->processTableCreateStatement($data);
				break;
			case $this->startTableBackupStage: $this->startTableBackup($data);
				break;
			case $this->processStatementStage: $this->processStatement($data);
				break;
			case $this->finishTableBackupStage: $this->finishTableBackup($data);
				break;
			case $this->finishBackupStage: $this->finishBackup();
				break;
		}
	}

}
?>