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
require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/database/PearDatabase.php');
global $adb;
global $log;
global $mod_strings;
global $app_strings;
global $current_language;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$log->info("Inside Email Template Detail View");

$smarty = new vtigerCRM_smarty;

$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("UMOD", $mod_strings);
$smod_strings = return_module_language($current_language,'Settings');
$smarty->assign("MOD", $smod_strings);
$smarty->assign("MODULE", 'Settings');
$smarty->assign("IMAGE_PATH", $image_path);

if(isset($_REQUEST['templateid']) && $_REQUEST['templateid']!='')
{
	$log->info("The templateid is set");
	$tempid = $_REQUEST['templateid'];
	$sql = "select * from vtiger_emailtemplates where templateid=?";
	$result = $adb->pquery($sql, array($tempid));
	$emailtemplateResult = $adb->fetch_array($result);
}
$smarty->assign("FOLDERNAME", $emailtemplateResult["foldername"]);

$smarty->assign("TEMPLATENAME", $emailtemplateResult["templatename"]);
$smarty->assign("DESCRIPTION", $emailtemplateResult["description"]);
$smarty->assign("TEMPLATEID", $emailtemplateResult["templateid"]);

$smarty->assign("SUBJECT", $emailtemplateResult["subject"]);
$smarty->assign("BODY", decode_html($emailtemplateResult["body"]));

$smarty->display("DetailViewEmailTemplate.tpl");

?>






