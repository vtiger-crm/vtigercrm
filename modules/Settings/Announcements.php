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

global $adb;
global $current_user;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$query="select * from vtiger_announcement where creatorid=?";
$result=$adb->pquery($query, array($current_user->id));
$announcement=$adb->query_result($result,0,'announcement');
$title_prev=$adb->query_result($result,0,'title');
$id=$adb->query_result($result,0,'creatorid');

$smarty = new vtigerCRM_Smarty;

$smarty->assign("ANNOUNCE",$announcement);
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);

$smarty->display("Settings/Announcements.tpl");

?>
