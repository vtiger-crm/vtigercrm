<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('vtlib/Vtiger/Utils.php');
require_once('include/utils/CommonUtils.php');
require_once('Smarty_setup.php');
$module_disable = $_REQUEST['module_disable'];
$module_name = $_REQUEST['module_name'];
$module_enable = $_REQUEST['module_enable'];
global $mod_strings,$app_strings,$theme;
global $log;
require_once('include/utils/CommonUtils.php');
require_once('Smarty_setup.php');
$smarty = new vtigerCRM_Smarty();
$smarty->assign("MOD",$mod_strings);
$smarty->assign("ALLMENUS",getAllMenuModules());
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME",$theme);
$smarty->assign("ASSIGNED_VALUES",getTopMenuModules());
if($_REQUEST['ajax'] == true) {
    $smarty->display("Settings/MenuEditorAssign.tpl");
} else {
    $smarty->display('Settings/MenuEditor.tpl');
}

?>
