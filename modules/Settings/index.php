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
require_once 'include/utils/utils.php';
global $mod_strings;
global $app_strings;
global $app_list_strings;

global $theme, $adb;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$smarty->assign("APP", $app_strings);
$smarty->assign("MODULE", 'Settings');
$smarty->assign("CATEGORY", 'Settings');
$smarty->assign("MOD", $mod_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("IMAGES", "themes/images/");
$smarty->assign("BLOCKS", getSettingsBlocks());
$smarty->assign("FIELDS", getSettingsFields());
$smarty->assign("NUMBER_OF_COLUMNS", 4);	//this is the number of columns in the settings page

$smarty->display("Settings.tpl");
?>
