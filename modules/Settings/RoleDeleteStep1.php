<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/utils/utils.php');
require_once('include/utils/UserInfoUtil.php');
require_once('Smarty_setup.php');

$delete_role_id = vtlib_purify($_REQUEST['roleid']);
$delete_role_name = getRoleName($delete_role_id);
global $app_strings;
global $app_list_strings;
global $mod_strings;
$smarty=new vtigerCRM_Smarty;
$smarty->assign("APP", $app_strings);
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("CMOD", $mod_strings);
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$smarty->assign("IMAGE_PATH",$image_path);

$smarty->assign("ROLEID", $delete_role_id);
$smarty->assign("ROLENAME", $delete_role_name);
$opt = '<a href="javascript:openPopup(\''.$delete_role_id.'\');"><img src="' . vtiger_imageurl('select.gif', $theme) . '" border="0" align="absmiddle"></a>';
$smarty->assign("ROLEPOPUPBUTTON", $opt);
$smarty->display("DeleteRole.tpl");

?>