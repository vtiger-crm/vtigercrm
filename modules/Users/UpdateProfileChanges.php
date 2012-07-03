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
global $adb,$log;
$profileid = vtlib_purify($_REQUEST['profileid']);
$def_module = vtlib_purify($_REQUEST['selected_module']);
$def_tab = vtlib_purify($_REQUEST['selected_tab']);

if(isset($_REQUEST['return_action']) && $_REQUEST['return_action']!= '')
	$return_action =vtlib_purify($_REQUEST['return_action']);
else
	$return_action = 'ListProfiles';

//Retreiving the vtiger_tabs permission array
$tab_perr_result = $adb->pquery("select * from vtiger_profile2tab where profileid=?", array($profileid));
$act_perr_result = $adb->pquery("select * from vtiger_profile2standardpermissions where profileid=?", array($profileid));
$act_utility_result = $adb->pquery("select * from vtiger_profile2utility where profileid=?", array($profileid));
$num_tab_per = $adb->num_rows($tab_perr_result);
$num_act_per = $adb->num_rows($act_perr_result);
$num_act_util_per = $adb->num_rows($act_utility_result);

	//Updating vtiger_profile2global permissons vtiger_table
	$view_all_req=$_REQUEST['view_all'];
	$view_all = getPermissionValue($view_all_req);

	$edit_all_req=$_REQUEST['edit_all'];
	$edit_all = getPermissionValue($edit_all_req);

	$update_query = "update  vtiger_profile2globalpermissions set globalactionpermission=? where globalactionid=1 and profileid=?";
	$adb->pquery($update_query, array($view_all, $profileid));
	$update_query = "update  vtiger_profile2globalpermissions set globalactionpermission=? where globalactionid=2 and profileid=?";
	$adb->pquery($update_query, array($edit_all, $profileid));

	
	//profile2tab permissions
	for($i=0; $i<$num_tab_per; $i++)
	{
		$tab_id = $adb->query_result($tab_perr_result,$i,"tabid");
		$request_var = $tab_id.'_tab';
		if($tab_id != 3 && $tab_id != 16)
		{
			$permission = $_REQUEST[$request_var];
			if($permission == 'on')
			{
				$permission_value = 0;
			}
			else
			{
				$permission_value = 1;
			}
			$update_query = "update vtiger_profile2tab set permissions=? where tabid=? and profileid=?";
			$adb->pquery($update_query, array($permission_value, $tab_id, $profileid));
			if($tab_id ==9)
			{
				$update_query = "update vtiger_profile2tab set permissions=? where tabid=16 and profileid=?";
				$adb->pquery($update_query, array($permission_value, $profileid));
			}
		}
	}
	
	//profile2standard permissions	
	for($i=0; $i<$num_act_per; $i++)
	{
		$tab_id = $adb->query_result($act_perr_result,$i,"tabid");
		if($tab_id != 16)
		{
			$action_id = $adb->query_result($act_perr_result,$i,"operation");
			$action_name = getActionname($action_id);
			if($action_name == 'EditView' || $action_name == 'Delete' || $action_name == 'DetailView')
			{
				$request_var = $tab_id.'_'.$action_name;
			}
			elseif($action_name == 'Save')
			{
				$request_var = $tab_id.'_EditView';
			}
			elseif($action_name == 'index')
			{
				$request_var = $tab_id.'_DetailView';
			}

			$permission = $_REQUEST[$request_var];
			if($permission == 'on')
			{
				$permission_value = 0;
			}
			else
			{
				$permission_value = 1;
			}
			$update_query = "update vtiger_profile2standardpermissions set permissions=? where tabid=? and Operation=? and profileid=?";
			$adb->pquery($update_query, array($permission_value, $tab_id, $action_id, $profileid));
			if($tab_id ==9)
			{
				$update_query = "update vtiger_profile2standardpermissions set permissions=? where tabid=16 and Operation=? and profileid=?";
				$adb->pquery($update_query, array($permission_value, $action_id, $profileid));
			}



		}
	}

	//Update Profile 2 utility
	for($i=0; $i<$num_act_util_per; $i++)
	{
		$tab_id = $adb->query_result($act_utility_result,$i,"tabid");

		$action_id = $adb->query_result($act_utility_result,$i,"activityid");
		$action_name = getActionname($action_id);
		$request_var = $tab_id.'_'.$action_name;


		$permission = $_REQUEST[$request_var];
		if($permission == 'on')
		{
			$permission_value = 0;
		}
		else
		{
			$permission_value = 1;
		}

		$update_query = "update vtiger_profile2utility set permission=? where tabid=? and activityid=? and profileid=?";
		$adb->pquery($update_query, array($permission_value, $tab_id, $action_id, $profileid));


	}


$modArr=getModuleAccessArray(); 

foreach($modArr as $fld_module => $fld_label)
{
	$fieldListResult = getProfile2FieldList($fld_module, $profileid);
	$noofrows = $adb->num_rows($fieldListResult);
	$tab_id = getTabid($fld_module);
	for($i=0; $i<$noofrows; $i++)
	{
		$fieldid =  $adb->query_result($fieldListResult,$i,"fieldid");
		$visible = $_REQUEST[$fieldid];
		if($visible == 'on')
		{
			$visible_value = 0;
		}
		else
		{
			$visible_value = 1;
		}
		$readonlyfieldid = $fieldid.'_readonly';
		$readOnlyValue = $_REQUEST[$readonlyfieldid];
		//Updating the Mandatory vtiger_fields
		$uitype = $adb->query_result($fieldListResult,$i,"uitype");
		$displaytype =  $adb->query_result($fieldListResult,$i,"displaytype");
		$fieldname =  $adb->query_result($fieldListResult,$i,"fieldname");
		$typeofdata = $adb->query_result($fieldListResult,$i,"typeofdata");
		$fieldtype = explode("~",$typeofdata);
		if($fieldtype[1] == 'M')
   		{
			$visible_value = 0;
		}
		//Updating the database
		$update_query = "update vtiger_profile2field set visible=?, readonly=? where fieldid=? and profileid=? and tabid=?";
		$adb->pquery($update_query, array($visible_value, $readOnlyValue, $fieldid, $profileid, $tab_id));

	}
}
	if($return_action == 'profilePrivileges' || $return_action == 'ListProfiles')
	{
		$loc = "Location: index.php?action=".$return_action."&module=Settings&mode=view&parenttab=Settings&profileid=".$profileid."&selected_tab=".$def_tab."&selected_module=".$def_module;
	}
	else
	{
		$loc = "Location: index.php?action=".$return_action."&module=Users&mode=view&parenttab=Settings&profileid=".$profileid."&selected_tab=".$def_tab."&selected_module=".$def_module;
	}
	header($loc);

 /** returns value 0 if request permission is on else returns value 1
  * @param $req_per -- Request Permission:: Type varchar
  * @returns $permission - can have value 0 or 1:: Type integer
  *
 */
function getPermissionValue($req_per)
{
	if($req_per == 'on')
	{
		$permission_value = 0;
	}
	else
	{
		$permission_value = 1;
	}
	return $permission_value;
}

?>