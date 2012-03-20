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
global $adb;
$profilename = from_html(decode_html($_REQUEST['profile_name']));
$description= from_html(decode_html($_REQUEST['profile_description']));
$def_module = vtlib_purify($_REQUEST['selected_module']);
$def_tab = vtlib_purify($_REQUEST['selected_tab']);
$profile_id = $adb->getUniqueID("vtiger_profile");
//Inserting values into Profile Table
$sql1 = "insert into vtiger_profile(profileid, profilename, description) values(?,?,?)";
$adb->pquery($sql1, array($profile_id,$profilename, $description));

        //Retreiving the vtiger_profileid
        $sql2 = "select max(profileid) as current_id from vtiger_profile";
        $result2 = $adb->pquery($sql2, array());
        $profileid = $adb->query_result($result2,0,'current_id');


	//Retreiving the vtiger_tabs permission array
	//

	//Retreiving the first profileid
	$prof_query="select profileid from vtiger_profile order by profileid ASC";
	$prof_result = $adb->pquery($prof_query, array());
	$first_prof_id = $adb->query_result($prof_result,0,'profileid');

$tab_perr_result = $adb->pquery("select * from vtiger_profile2tab where profileid=?", array($first_prof_id));
$act_perr_result = $adb->pquery("select * from vtiger_profile2standardpermissions where profileid=?", array($first_prof_id));
$act_utility_result = $adb->pquery("select * from vtiger_profile2utility where profileid=?", array($first_prof_id));
$num_tab_per = $adb->num_rows($tab_perr_result);
$num_act_per = $adb->num_rows($act_perr_result);
$num_act_util_per = $adb->num_rows($act_utility_result);

	//Updating vtiger_profile2global permissons vtiger_table
	$view_all_req=$_REQUEST['view_all'];
	$view_all = getPermissionValue($view_all_req);

	$edit_all_req=$_REQUEST['edit_all'];
	$edit_all = getPermissionValue($edit_all_req);


	$sql4="insert into vtiger_profile2globalpermissions values(?,?,?)";
    $adb->pquery($sql4, array($profileid,1, $view_all));

	$sql4="insert into vtiger_profile2globalpermissions values(?,?,?)";
    $adb->pquery($sql4, array($profileid,2, $edit_all));

	
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
			$sql4="insert into vtiger_profile2tab values(?,?,?)";
            $adb->pquery($sql4, array($profileid, $tab_id, $permission_value));

			if($tab_id ==9)
			{
				$sql4="insert into vtiger_profile2tab values(?,?,?)";
                $adb->pquery($sql4, array($profileid,16, $permission_value));	
			}
		}
	}
	
	//profile2standard permissions	
	for($i=0; $i<$num_act_per; $i++)
	{
		$tab_id = $adb->query_result($act_perr_result,$i,"tabid");
		$action_id = $adb->query_result($act_perr_result,$i,"operation");
		if($tab_id != 16)
		{
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

			$sql7="insert into vtiger_profile2standardpermissions values(?,?,?,?)";
            $adb->pquery($sql7, array($profileid, $tab_id, $action_id, $permission_value));

			if($tab_id ==9)
			{
				$sql7="insert into vtiger_profile2standardpermissions values(?,?,?,?)";
                $adb->pquery($sql7, array($profileid, 16, $action_id, $permission_value));
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

		$sql9="insert into vtiger_profile2utility values(?,?,?,?)";
        $adb->pquery($sql9, array($profileid, $tab_id, $action_id, $permission_value));

	}



	$modArr=getFieldModuleAccessArray();
 

foreach($modArr as $fld_module => $fld_label)
{
	$fieldListResult = getProfile2FieldList($fld_module, $first_prof_id);
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
		$sql11="insert into vtiger_profile2field values(?,?,?,?,?)";
        $adb->pquery($sql11, array($profileid, $tab_id, $fieldid, $visible_value,1));
	}
}
	$loc = "Location: index.php?action=ListProfiles&module=Settings&mode=view&parenttab=Settings&profileid=".vtlib_purify($profileid)."&selected_tab=".vtlib_purify($def_tab)."&selected_module=".vtlib_purify($def_module);
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