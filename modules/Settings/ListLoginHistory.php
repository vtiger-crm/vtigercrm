<?php

/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('modules/Users/LoginHistory.php');
require_once('modules/Users/Users.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');


global $app_strings;
global $mod_strings;
global $app_list_strings;
global $current_language, $current_user, $adb;
$current_module_strings = return_module_language($current_language, 'Settings');

global $list_max_entries_per_page;
global $urlPrefix;

$log = LoggerManager::getLogger('login_list');

global $currentModule;

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$focus = new LoginHistory();

$smarty = new vtigerCRM_Smarty;

$category = getParenttab();

$oUser = new Users($id);


$user_list = getUserslist(false);

$smarty->assign("CMOD", $mod_strings);
$smarty->assign("MOD", $current_module_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("USERLIST", $user_list);
$smarty->assign("CATEGORY",$category);

$smarty->display("ListLoginHistory.tpl");
?>

