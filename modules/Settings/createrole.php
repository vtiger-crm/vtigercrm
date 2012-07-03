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

global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$smarty = new vtigerCRM_Smarty;
$profDetails=getAllProfileInfo();
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("APP", $app_strings);
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
elseif(isset($_REQUEST['parent']) && $_REQUEST['parent'] != '')
{
	$mode = 'create';
	$parent=vtlib_purify($_REQUEST['parent']);
}
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("THEME", $theme);

$parentname=getRoleName($parent);
$smarty->assign("RETURN_ACTION",vtlib_purify($_REQUEST['returnaction']));            
$smarty->assign("ROLEID",$roleid);            
$smarty->assign("MODE",$mode);            
$smarty->assign("PARENT",$parent);            
$smarty->assign("PARENTNAME",$parentname);            
$smarty->assign("ROLENAME",$rolename);            

$profile_entries=array();
foreach($profDetails as $profId=>$profName)
{
	$profile_entries[]=$profId;
	$profile_entries[]=$profName;
}
$profile_entries=array_chunk($profile_entries,2);
$smarty->assign("PROFILELISTS",$profile_entries);

if($mode == 'edit')
{
	$selected_profiles = array();
	foreach($roleRelatedProfiles as $relProfId => $relProfName)
	{
		$selected_profiles[]=$relProfId;
		$selected_profiles[]=$relProfName;
	}
	$selected_profiles=array_chunk($selected_profiles,2);
	$smarty->assign("SELPROFILELISTS",$selected_profiles);
}

$smarty->display("RoleEditView.tpl");
?>