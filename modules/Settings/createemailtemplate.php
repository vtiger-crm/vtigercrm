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
require_once('database/DatabaseConnection.php');
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');
require_once('data/Tracker.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/CustomFieldUtil.php');

global $app_strings;
global $mod_strings;
global $current_language,$default_charset;

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$smod_strings = return_module_language($current_language,'Settings');

//To get Email Template variables -- Pavani
$allOptions=getEmailTemplateVariables();
$smarty = new vtigerCRM_smarty;

$smarty->assign("APP", $app_strings);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("THEME", $theme);
$smarty->assign("THEME_PATH", $theme_path);
$smarty->assign("UMOD", $mod_strings);
$smarty->assign("PARENTTAB", getParentTab());
$smarty->assign("ALL_VARIABLES", $allOptions);

$smarty->assign("MOD", $smod_strings);
$smarty->assign("MODULE", 'Settings');

$smarty->display("CreateEmailTemplate.tpl");

?>