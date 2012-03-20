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
require_once('include/database/PearDatabase.php');
require_once('include/utils/UserInfoUtil.php');

global $mod_strings;
global $app_strings;
global $app_list_strings;

global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$sql = "select * from vtiger_profile";
$profileListResult = $adb->pquery($sql, array());
$noofrows = $adb->num_rows($profileListResult);
$list_entries = array($mod_strings['LBL_LIST_NO'],$mod_strings['LBL_LIST_TOOLS'],$mod_strings['LBL_NEW_PROFILE_NAME'],$mod_strings['LBL_DESCRIPTION']);

/** gives the profile list info array 
  * @param $profileListResult -- profile list database result:: Type array
  * @param $noofrows -- no of rows in the $profileListResult:: Type integer 
  * @param $mod_strings -- i18n mod_strings array:: Type array 
  * @returns $return_date -- profile list info array:: Type array
  *
 */
function getStdOutput($profileListResult, $noofrows, $mod_strings)
{
	global $adb;
	$return_data = array();		
	for($i=0; $i<$noofrows; $i++)
	{
		$standCustFld = array();
		$profile_name = $adb->query_result($profileListResult,$i,"profilename");
		$profile_id = $adb->query_result($profileListResult,$i,"profileid");
		$description = $adb->query_result($profileListResult,$i,"description");
		global $current_user;
        $current_profile = fetchUserProfileId($current_user->id);
        if($profile_id != 1  && $profile_id != $current_profile)
			$standCustFld['del_permission']='yes';
		else
			$standCustFld['del_permission']='no';

		$standCustFld['profileid']= $profile_id;	
		$standCustFld['profilename']= $profile_name;
		$standCustFld['description']= $description;
		$return_data[]=$standCustFld;
	}
	return $return_data;
}

$smarty->assign("LIST_HEADER",$list_entries);
$smarty->assign("LIST_ENTRIES",getStdOutput($profileListResult, $noofrows, $mod_strings));
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("PROFILES", $standCustFld);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("COUNT",$noofrows);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);

$smarty->display("UserProfileList.tpl");
?>
