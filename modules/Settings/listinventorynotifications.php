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
$query = "SELECT * FROM vtiger_inventorynotification";
$result = $adb->pquery($query, array());
$num_rows = $adb->num_rows($result);
$output = Array();
for($i=0; $i<$num_rows; $i++)
{
	$out = Array();
	$not_id = $adb->query_result($result,$i,'notificationid');
	$not_mod = $adb->query_result($result,$i,'notificationname');	
	$not_des = $adb->query_result($result,$i,'label');
	$not_st = $adb->query_result($result,$i,'status');
	$out ['notificationname'] = $mod_strings[$not_mod];
	$out ['label'] = $mod_strings[$not_des];
	$out ['id'] = $not_id;
	$out ['status'] = $not_st;
	
	if($out['status'] != 1)
		$out['status'] = $mod_strings['LBL_INACTIVE'];
	else
		$out['status'] = $mod_strings['LBL_ACTIVE']; 	
	
	$output [] = $out;
}

$smarty->assign("NOTIFICATION",$output);
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);

if($_REQUEST['directmode'] != '')
	$smarty->display("Settings/InventoryNotifyContents.tpl");
else
	$smarty->display("Settings/InventoryNotify.tpl");

?>
