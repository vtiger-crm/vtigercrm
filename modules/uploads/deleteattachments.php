<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/database/PearDatabase.php');

$id=$_REQUEST['record'];

$sql = "delete from vtiger_seattachmentsrel where attachmentsid =?";
$adb->pquery($sql, array($id));

$sql = "delete from vtiger_attachments where attachmentsid =?";
$adb->pquery($sql, array($id));

header("Location:index.php?module=".vtlib_purify($_REQUEST['return_module'])."&action=".vtlib_purify($_REQUEST['return_action'])."&record=".vtlib_purify($_REQUEST['return_id'])."&parenttab=".getParentTab());


?>