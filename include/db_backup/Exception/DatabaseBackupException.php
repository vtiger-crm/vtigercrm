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

/**
 * Description of DatabaseBackupException
 *
 * @author MAK
 */
class DatabaseBackupException extends Exception{
	public $code;
	public $message;

	function DatabaseBackupException($errCode,$msg){
		$this->code = $errCode;
		$this->message = $msg;
	}
}

class DatabaseBackupErrorCode{
	public static $DB_CONNECT_ERROR = 'CONNECT_ERROR';
	public static $TABLE_NAME_ERROR = 'TABLE_LIST_FETCH_ERROR';
	public static $SQL_EXECUTION_ERROR = 'SQL_EXECUTION_ERROR';
}
?>