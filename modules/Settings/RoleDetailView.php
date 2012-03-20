<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');

global $mod_strings;
global $app_strings;
global $app_list_strings;

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$roleid= vtlib_purify($_REQUEST['roleid']);

/** gives the role info, role profile info and role user info details in an array  for the specified role id
  * @param $roleid -- role id:: Type integer
  * @returns $return_data -- array contains role info, role profile info and role user info. This array is used to construct the detail view for the specified role id :: Type varchar
  *
 */
function getStdOutput($roleid)
{
	//Retreiving the related vtiger_profiles
	$roleProfileArr=getRoleRelatedProfiles($roleid);
	//Retreving the related vtiger_users
	$roleUserArr=getRoleUsers($roleid);

	//Constructing the Profile list
	$profileinfo = Array();
	foreach($roleProfileArr as $profileId=>$profileName)
	{
		$profileinfo[]=$profileId;
		$profileinfo[]=$profileName;
		$profileList .= '<a href="index.php?module=Settings&action=profilePrivileges&profileid='.$profileId.'">'.$profileName.'</a>';
	}
	$profileinfo=array_chunk($profileinfo,2);
	
	//Constructing the Users List
	$userinfo = Array();
	foreach($roleUserArr as $userId=>$userName)
	{
		$userinfo[]= $userId;
		$userinfo[]= $userName;
		$userList .= '<a href="index.php?module=Settings&action=DetailView&record='.$userId.'">'.$userName.'</a>';
	}
	$userinfo=array_chunk($userinfo,2);
	
	//Check for Current User
	global $current_user;
	$current_role = fetchUserRole($current_user->id);
	$return_data = Array('profileinfo'=>$profileinfo,'userinfo'=>$userinfo);
	return $return_data;
}

if(isset($_REQUEST['roleid']) && $_REQUEST['roleid'] != '')
{	
	$roleid= vtlib_purify($_REQUEST['roleid']);
	$mode = vtlib_purify($_REQUEST['mode']);
	$roleInfo=getRoleInformation($roleid);
	$thisRoleDet=$roleInfo[$roleid];
	$rolename = $thisRoleDet[0]; 
	$parent = $thisRoleDet[3]; 
	//retreiving the vtiger_profileid
	$roleRelatedProfiles=getRoleRelatedProfiles($roleid);

}
$parentname=getRoleName($parent);
//Retreiving the Role Info
$roleInfoArr=getRoleInformation($roleid);
$rolename=$roleInfoArr[$roleid][0];
$smarty->assign("ROLE_NAME",$rolename);
$smarty->assign("ROLEID",$roleid);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("ROLEINFO",getStdOutput($roleid));
$smarty->assign("PARENTNAME",$parentname);

$smarty->display("RoleDetailView.tpl");
?>