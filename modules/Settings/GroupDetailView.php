<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/utils/UserInfoUtil.php');

global $mod_strings;
global $app_strings;
global $app_list_strings;

$groupId=vtlib_purify($_REQUEST['groupId']);
$groupInfoArr=getGroupInfo($groupId);

$smarty = new vtigerCRM_Smarty;

global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty->assign("GROUPINFO", getStdOutput($groupInfoArr,$groupId, $mod_strings));
$smarty->assign("GROUPID",$groupId);
$smarty->assign("GROUP_NAME",$groupInfoArr[0]);


/** Gives the group info and the group member info array 
  * @param $groupInfoArr -- Group Info Array got by calling getGroupInfo($groupId):: Type array
  * @param $groupID -- group id::Type integer
  * @param $mod_strings -- i18n mod strings array::Type array
  * @returns $returndata:: Type array, Example array format given below
	Array
	(
    	[0] => Array
        	(
            		[groupname] => vtiger grp
            		[description] => 
        	)

    	[1] => Array
        (
            [Role] => Array
                (
                    [0] => Array
                        (
                            [membername] => CEO
                            [memberid] => H2
                            [membertype] => Role
                            [memberaction] => RoleDetailView
                            [actionparameter] => roleid
                        )

                )

            [Role and Subordinates] => Array
                (
                    [0] => Array
                        (
                            [membername] => Vice President
                            [memberid] => H3
                            [membertype] => Role and Subordinates
                            [memberaction] => RoleDetailView
                            [actionparameter] => roleid
                        )

                )

            [User] => Array
                (
                    [0] => Array
                        (
                            [membername] => standarduser
                            [memberid] => 2
                            [membertype] => User
                            [memberaction] => DetailView
                            [actionparameter] => record
                        )

                )

        )

	)
  *
  *
 */
function getStdOutput($groupInfoArr,$groupId, $mod_strings)
{
	global $adb;
    $groupfields['groupname'] = $groupInfoArr[0];    
    $groupfields['description'] = $groupInfoArr[1];

	$row=1;
	$groupMember = $groupInfoArr[2];
	$information = array();
	foreach($groupMember as $memberType=>$memberValue)
	{
		$memberinfo = array();
		foreach($memberValue as $memberId)
		{
			$groupmembers = array();
			if($memberType == 'roles')
			{
				$memberName=getRoleName($memberId);
				$memberAction="RoleDetailView";
				$memberActionParameter="roleid";
				$memberDisplayType="Role";
			}
			elseif($memberType == 'rs')
			{
				$memberName=getRoleName($memberId);
				$memberAction="RoleDetailView";
				$memberActionParameter="roleid";
				$memberDisplayType="Role and Subordinates";
			}
			elseif($memberType == 'groups')
			{
				$memberName=fetchGroupName($memberId);
				$memberAction="GroupDetailView";
				$memberActionParameter="groupId";
				$memberDisplayType="Group";
			}
			elseif($memberType == 'users')
			{
				$memberName=getUserFullName($memberId);
				$memberAction="DetailView";
				$memberActionParameter="record";
				$memberDisplayType="User";
			}
			$groupmembers ['membername'] = $memberName;
			$groupmembers ['memberid'] = $memberId;
			$groupmembers ['membertype'] = $memberDisplayType;
			$groupmembers ['memberaction'] = $memberAction;
			$groupmembers ['actionparameter'] = $memberActionParameter;
			$row++;
			$memberinfo [] = $groupmembers;
		}
		if(sizeof($memberinfo) >0)
			$information[$memberDisplayType] = $memberinfo;
	}
	$returndata=array($groupfields,$information);
	return $returndata;
}

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->display("GroupDetailview.tpl");

?>