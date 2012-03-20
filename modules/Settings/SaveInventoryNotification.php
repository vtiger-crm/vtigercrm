<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/utils/VtlibUtils.php');

global $adb;
if(isset($_REQUEST['record']) && $_REQUEST['record']!='')
{
	$query="UPDATE vtiger_inventorynotification set notificationsubject=?, notificationbody=?, status=?  where notificationid=?";
	$params = array(vtlib_purify($_REQUEST['notifysubject']), vtlib_purify($_REQUEST['notifybody']), vtlib_purify($_REQUEST['status']), vtlib_purify($_REQUEST['record']) );
	$adb->pquery($query, $params);	
}
$loc = "Location: index.php?action=SettingsAjax&file=listinventorynotifications&module=Settings&directmode=ajax";
header($loc);
?>
