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
$Err_msg;
$parentGroupArray=Array();
if(isset($_REQUEST['groupId']) && $_REQUEST['groupId'] != '')
{	
	$mode = 'edit';
	$groupId=vtlib_purify($_REQUEST['groupId']);
	$groupInfo=getGroupInfo($groupId);
	require_once('include/utils/GetParentGroups.php');
	$parGroups = new GetParentGroups();
	$parGroups->parent_groups[]=$groupId;
	$parGroups->getAllParentGroups($groupId);
	$parentGroupArray=$parGroups->parent_groups;	
	

}
else
{
	$mode = 'create';
	if(isset($_REQUEST['error']) && ($_REQUEST['error']=='true'))
	{
		$Err_msg = "<center><font color='red'><b>".$mod_strings['LBL_GROUP_NAME_ERROR']."</b></font></center>";
		$groupInfo[] = vtlib_purify($_REQUEST['groupname']);
		$groupInfo[] = vtlib_purify($_REQUEST['desc']);
	}
}
			

//Constructing the Role Array
$roleDetails=getAllRoleDetails();
$i=0;
$roleIdStr="";
$roleNameStr="";
$userIdStr="";
$userNameStr="";
$grpIdStr="";
$grpNameStr="";

foreach($roleDetails as $roleId=>$roleInfo)
{
	if($i !=0)
	{
		if($i !=1)
		{
			$roleIdStr .= ", ";
			$roleNameStr .= ", ";
		}

		$roleName=$roleInfo[0];
		$roleIdStr .= "'".$roleId."'";
		$roleNameStr .= "'".addslashes(decode_html($roleName))."'"; 
	}
	
	$i++;	
}

//Constructing the User Array
$l=0;
$userDetails=getAllUserName();
foreach($userDetails as $userId=>$userInfo)
{
		if($l !=0)
		{
			$userIdStr .= ", ";
			$userNameStr .= ", ";
		}

		$userIdStr .= "'".$userId."'";
		$userNameStr .= "'".$userInfo."'";
	
	$l++;	
}

//Constructing the Group Array
$m=0;
$grpDetails=getAllGroupName();
foreach($grpDetails as $grpId=>$grpName)
{
	if(! in_array($grpId,$parentGroupArray))
	{
		if($m !=0)
		{
			$grpIdStr .= ", ";
			$grpNameStr .= ", ";
		}

		$grpIdStr .= "'".$grpId."'";
		$grpNameStr .= "'".addslashes(decode_html($grpName))."'";
	
	$m++;
	}	
}
if($mode == 'edit')
{
	$member=array();
	$groupMemberArr=$groupInfo[2];
	foreach($groupMemberArr as $memberType=>$memberValue)
	{
		foreach($memberValue as $memberId)
		{
			if($memberType == 'groups')
			{
				$memberName=fetchGroupName($memberId);
				$memberDisplay="Group::";
			}
			elseif($memberType == 'roles')
			{
				$memberName=getRoleName($memberId);
				$memberDisplay="Roles::";
			}
			elseif($memberType == 'rs')
			{
				$memberName=getRoleName($memberId);
				$memberDisplay="RoleAndSubordinates::";
			}
			elseif($memberType == 'users')
			{
				$memberName=getUserFullName($memberId);
				$memberDisplay="User::";
			}
			$member[]=$memberType.'::'.$memberId;
			$member[]=$memberDisplay.$memberName;
		}
	}	
	$smarty->assign("MEMBER", array_chunk($member,2));
}		
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("ROLEIDSTR",$roleIdStr);
$smarty->assign("ROLENAMESTR",$roleNameStr);
$smarty->assign("USERIDSTR",$userIdStr);
$smarty->assign("USERNAMESTR",$userNameStr);
$smarty->assign("GROUPIDSTR",$grpIdStr);
$smarty->assign("GROUPNAMESTR",$grpNameStr);
$smarty->assign("RETURN_ACTION",vtlib_purify($_REQUEST['returnaction']));
$smarty->assign("GROUPID",$groupId);
$smarty->assign("MODE",$mode);
$smarty->assign("GROUPNAME",$groupInfo[0]);		
$smarty->assign("DESCRIPTION",$groupInfo[1]);				
		
$smarty->display("GroupEditView.tpl");

?>