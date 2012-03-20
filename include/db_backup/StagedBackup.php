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

require_once 'include/db_backup/Exception/DatabaseBackupException.php';

/**
 * Description of StagedBackup
 *
 * @author MAK
 */
abstract class StagedBackup {
	public $startBackupStage = 0;
	public $tableCreateStage = 1;
	public $startTableBackupStage = 2;
	public $processStatementStage = 3;
	public $finishTableBackupStage = 4;
	public $finishBackupStage = 5;
	public $stageList = null;
	public function __construct() {
		$this->stageList = array($this->startBackupStage,$this->tableCreateStage,
			$this->startTableBackupStage,$this->processStatementStage,$this->finishTableBackupStage,
			$this->finishBackupStage);
	}

	public function getNextStage($stage) {
		//noting to do.
	}

	public function getStageData($stage) {
		//noting to do.
	}
	
	public function addStageData($stage,$data) {
		//noting to do.
	}

}
?>