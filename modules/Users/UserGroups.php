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
require_once('include/utils/GetUserGroups.php');
require_once('Smarty_setup.php');


$user_id = $_REQUEST['record'];
global $current_user;
global $mod_strings;
$smarty = new vtigerCRM_Smarty;
$oGetUserGroups = new GetUserGroups();
$oGetUserGroups->getAllUserGroups($user_id);
$user_group_info = Array();
foreach($oGetUserGroups->user_groups as $groupid)
{
	$user_group_info[$groupid] = getGroupDetails($groupid);
}
$smarty->assign("IS_ADMIN",is_admin($current_user));
$smarty->assign("GROUPLIST",$user_group_info);
$smarty->assign("UMOD", $mod_strings);
$smarty->display("UserGroups.tpl");
?>
