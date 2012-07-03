<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

require_once 'include/utils/utils.php';

//5.2.1 to 5.3.0RC database changes

$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

global $migrationlog;

$migrationlog->debug("\n\nDB Changes from 5.4.0RC to 5.4.0 -------- Starts \n\n");

updateVtlibModule('MailManager', "packages/vtiger/mandatory/MailManager.zip");

$migrationlog->debug("\n\nDB Changes from 5.4.0RC to 5.4.0 -------- Ends \n\n");

?>