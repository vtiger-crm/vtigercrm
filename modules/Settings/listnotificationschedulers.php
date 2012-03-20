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
require_once('Smarty_setup.php');
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$query = "SELECT * FROM vtiger_notificationscheduler order by schedulednotificationid";
$result = $adb->pquery($query, array());
if($adb->num_rows($result) >=1)
{
	$notifiy_array = Array();
	while($result_row = $adb->fetch_array($result))
	{
		$result_data = Array();
		$result_data['active'] = $result_row['active'];
		$result_data['schedulename'] = $mod_strings[$result_row['schedulednotificationname']];
		$result_data['id'] = $result_row['schedulednotificationid'];
	
		if($result_data['active'] != 1)	
			$result_data['active'] = $mod_strings['LBL_INACTIVE'];
		else
			$result_data['active'] = $mod_strings['LBL_ACTIVE'];
			
		$result_data['label'] = $mod_strings[$result_row['label']];
		$notifiy_array []= $result_data;
	}
	$smarty->assign("NOTIFICATION",$notifiy_array);
}	
		
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
if($_REQUEST['directmode'] != '')
	$smarty->display("Settings/EmailNotificationContents.tpl");
else
	$smarty->display("Settings/EmailNotification.tpl");
	
?>		
