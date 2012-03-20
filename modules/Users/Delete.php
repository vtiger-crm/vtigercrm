<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

$sql= 'delete from vtiger_salesmanactivityrel where smid=? and activityid = ?';
$adb->pquery($sql, array($_REQUEST['record'], $_REQUEST['return_id']));

if($_REQUEST['return_module'] == 'Calendar')
	$mode ='&activity_mode=Events';

header("Location: index.php?module=".vtlib_purify($_REQUEST['return_module'])."&action=".vtlib_purify($_REQUEST['return_action']).$mode."&record=".vtlib_purify($_REQUEST['return_id'])."&relmodule=".vtlib_purify($_REQUEST['module']));
?>