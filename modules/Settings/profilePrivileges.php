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

global $app_strings;
global $mod_strings;
global $current_user, $currentModule;
global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$profileId=vtlib_purify($_REQUEST['profileid']);
$profileName='';
$profileDescription='';

if(!empty($profileId)) {
	if(!profileExists($profileId) || !is_numeric($profileId)) {
		die(getTranslatedString('ERR_INVALID_PROFILE_ID', $currentModule));
	}
} elseif($_REQUEST['mode'] !='create') {
	die(getTranslatedString('ERR_INVALID_PROFILE_ID', $currentModule));
}

$parentProfileId=vtlib_purify($_REQUEST['parentprofile']);
if($_REQUEST['mode'] =='create' && $_REQUEST['radiobutton'] != 'baseprofile')
	$parentProfileId = '';


$smarty = new vtigerCRM_Smarty;
if(isset($_REQUEST['selected_tab']) && $_REQUEST['selected_tab']!='')
	$smarty->assign("SELECTED_TAB", vtlib_purify($_REQUEST['selected_tab']));
else
	$smarty->assign("SELECTED_TAB", "global_privileges");

if(isset($_REQUEST['selected_module']) && $_REQUEST['selected_module']!='')
	$smarty->assign("SELECTED_MODULE", vtlib_purify($_REQUEST['selected_module']));
else
	$smarty->assign("SELECTED_MODULE", "field_Leads");

$smarty->assign("PARENTPROFILEID", $parentProfileId);
$smarty->assign("RADIOBUTTON", vtlib_purify($_REQUEST['radiobutton']));

$secondaryModule='';
$mode='';
$output ='';
$output1 ='';
$smarty->assign("PROFILEID", $profileId);
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("CMOD", $mod_strings);
if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != '')
	$smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));


if(isset($_REQUEST['profile_name']) && $_REQUEST['profile_name'] != '' && $_REQUEST['mode'] == 'create')
{
	$profileName=$_REQUEST['profile_name'];
	$smarty->assign("PROFILE_NAME", to_html($profileName));
}
else
{
	$profileName=getProfileName($profileId);
	$smarty->assign("PROFILE_NAME", $profileName);

}

//$smarty->assign("PROFILE_NAME", to_html($profileName));

if(isset($_REQUEST['profile_description']) && $_REQUEST['profile_description'] != '' && $_REQUEST['mode'] == 'create')
	
	$profileDescription = vtlib_purify($_REQUEST['profile_description']);
else
{
	if($profileId != null)
	{
		$profileDescription = getProfileDescription($profileId);
	}
}

$smarty->assign("PROFILE_DESCRIPTION", $profileDescription);

if(isset($_REQUEST['mode']) && $_REQUEST['mode'] != '') {
	$mode = vtlib_purify($_REQUEST['mode']);
	$smarty->assign("MODE", $mode);
}


//Initially setting the secondary selected vtiger_tab
if($mode == 'create')
{
	$smarty->assign("ACTION",'SaveProfile');
}
elseif($mode == 'edit')
{
	$smarty->assign("ACTION",'UpdateProfileChanges');
}


//Global Privileges

if($mode == 'view')
{
	$global_per_arry = getProfileGlobalPermission($profileId);
	$view_all_per = $global_per_arry[1];
	$edit_all_per = $global_per_arry[2];
	$privileges_global[]=getGlobalDisplayValue($view_all_per,1);
	$privileges_global[]=getGlobalDisplayValue($edit_all_per,2); 
}
elseif($mode == 'edit')
{
	$global_per_arry = getProfileGlobalPermission($profileId);
	$view_all_per = $global_per_arry[1];
	$edit_all_per = $global_per_arry[2];
	$privileges_global[]=getGlobalDisplayOutput($view_all_per,1);
	$privileges_global[]=getGlobalDisplayOutput($edit_all_per,2);
}
elseif($mode == 'create')
{
	if($parentProfileId != '')
	{
		$global_per_arry = getProfileGlobalPermission($parentProfileId);
		$view_all_per = $global_per_arry[1];
		$edit_all_per = $global_per_arry[2];
		$privileges_global[]=getGlobalDisplayOutput($view_all_per,1);
		$privileges_global[]=getGlobalDisplayOutput($edit_all_per,2);
	}
	else
	{
		$privileges_global[]=getGlobalDisplayOutput(0,1);
		$privileges_global[]=getGlobalDisplayOutput(0,2);
	}

}

$smarty->assign("GLOBAL_PRIV",$privileges_global);			

//standard privileges	
if($mode == 'view')
{
	$act_perr_arry = getTabsActionPermission($profileId);	
	foreach($act_perr_arry as $tabid=>$action_array)
	{
		$stand = array();
		$entity_name = getTabModuleName($tabid);
		//Create/Edit Permission
		$tab_create_per_id = $action_array['1'];
		$tab_create_per = getDisplayValue($tab_create_per_id,$tabid,'1');
		//Delete Permission
		$tab_delete_per_id = $action_array['2'];
		$tab_delete_per = getDisplayValue($tab_delete_per_id,$tabid,'2');
		//View Permission
		$tab_view_per_id = $action_array['4'];
		$tab_view_per = getDisplayValue($tab_view_per_id,$tabid,'4');

		$stand[]=$entity_name;
		$stand[]=$tab_create_per;
		$stand[]=$tab_delete_per;
		$stand[]=$tab_view_per;
		$privileges_stand[$tabid]=$stand;
	}
}
if($mode == 'edit')
{
	$act_perr_arry = getTabsActionPermission($profileId);	
	foreach($act_perr_arry as $tabid=>$action_array)
	{
		$stand = array();
		$entity_name = getTabModuleName($tabid);
		//Create/Edit Permission
		$tab_create_per_id = $action_array['1'];
		$tab_create_per = getDisplayOutput($tab_create_per_id,$tabid,'1');
		//Delete Permission
		$tab_delete_per_id = $action_array['2'];
		$tab_delete_per = getDisplayOutput($tab_delete_per_id,$tabid,'2');
		//View Permission
		$tab_view_per_id = $action_array['4'];
		$tab_view_per = getDisplayOutput($tab_view_per_id,$tabid,'4');

		$stand[]=$entity_name;
		$stand[]=$tab_create_per;
		$stand[]=$tab_delete_per;
		$stand[]=$tab_view_per;
		$privileges_stand[$tabid]=$stand;
	}
}
if($mode == 'create')
{
	if($parentProfileId != '')
	{
		$act_perr_arry = getTabsActionPermission($parentProfileId);
		foreach($act_perr_arry as $tabid=>$action_array)
		{
			$stand = array();
			$entity_name = getTabModuleName($tabid);
			//Create/Edit Permission
			$tab_create_per_id = $action_array['1'];
			$tab_create_per = getDisplayOutput($tab_create_per_id,$tabid,'1');
			//Delete Permission
			$tab_delete_per_id = $action_array['2'];
			$tab_delete_per = getDisplayOutput($tab_delete_per_id,$tabid,'2');
			//View Permission
			$tab_view_per_id = $action_array['4'];
			$tab_view_per = getDisplayOutput($tab_view_per_id,$tabid,'4');

			$stand[]=$entity_name;
			$stand[]=$tab_create_per;
			$stand[]=$tab_delete_per;
			$stand[]=$tab_view_per;
			$privileges_stand[$tabid]=$stand;
		}	
	}
	else
	{
		$act_perr_arry = getTabsActionPermission(1);	
		foreach($act_perr_arry as $tabid=>$action_array)
		{
			$stand = array();
			$entity_name = getTabModuleName($tabid);
			//Create/Edit Permission
			$tab_create_per_id = $action_array['1'];
			$tab_create_per = getDisplayOutput(0,$tabid,'1');
			//Delete Permission
			$tab_delete_per_id = $action_array['2'];
			$tab_delete_per = getDisplayOutput(0,$tabid,'2');
			//View Permission
			$tab_view_per_id = $action_array['4'];
			$tab_view_per = getDisplayOutput(0,$tabid,'4');

			$stand[]=$entity_name;
			$stand[]=$tab_create_per;
			$stand[]=$tab_delete_per;
			$stand[]=$tab_view_per;
			$privileges_stand[$tabid]=$stand;
		}
	}

}
$smarty->assign("STANDARD_PRIV",$privileges_stand);			

//tab Privileges

if($mode == 'view')
{
	$tab_perr_array = getTabsPermission($profileId);
	$no_of_tabs =  sizeof($tab_perr_array);
	foreach($tab_perr_array as $tabid=>$tab_perr)
	{
		$tab=array();
		$entity_name = getTabModuleName($tabid);
		$tab_allow_per_id = $tab_perr_array[$tabid];
		$tab_allow_per = getDisplayValue($tab_allow_per_id,$tabid,'');	
		$tab[]=$entity_name;
		$tab[]=$tab_allow_per;
		$privileges_tab[$tabid]=$tab;
	}
}
if($mode == 'edit')
{
	$tab_perr_array = getTabsPermission($profileId);
	$no_of_tabs =  sizeof($tab_perr_array);
	foreach($tab_perr_array as $tabid=>$tab_perr)
	{
		$tab=array();
		$entity_name = getTabModuleName($tabid);
		$tab_allow_per_id = $tab_perr_array[$tabid];
		$tab_allow_per = getDisplayOutput($tab_allow_per_id,$tabid,'');	
		$tab[]=$entity_name;
		$tab[]=$tab_allow_per;
		$privileges_tab[$tabid]=$tab;
	}
}
if($mode == 'create')
{
	if($parentProfileId != '')
	{
		$tab_perr_array = getTabsPermission($parentProfileId);
		$no_of_tabs =  sizeof($tab_perr_array);
		foreach($tab_perr_array as $tabid=>$tab_perr)
		{
			$tab=array();
			$entity_name = getTabModuleName($tabid);
			$tab_allow_per_id = $tab_perr_array[$tabid];
			$tab_allow_per = getDisplayOutput($tab_allow_per_id,$tabid,'');	
			$tab[]=$entity_name;
			$tab[]=$tab_allow_per;
			$privileges_tab[$tabid]=$tab;
		}
	}
	else
	{
		$tab_perr_array = getTabsPermission(1);
		$no_of_tabs =  sizeof($tab_perr_array);
		foreach($tab_perr_array as $tabid=>$tab_perr)
		{
			$tab=array();
			$entity_name = getTabModuleName($tabid);
			$tab_allow_per_id = $tab_perr_array[$tabid];
			$tab_allow_per = getDisplayOutput(0,$tabid,'');	
			$tab[]=$entity_name;
			$tab[]=$tab_allow_per;
			$privileges_tab[$tabid]=$tab;
		}
	}

}
$smarty->assign("TAB_PRIV",$privileges_tab);			
//utilities privileges

if($mode == 'view')
{
	$act_utility_arry = getTabsUtilityActionPermission($profileId);
	foreach($act_utility_arry as $tabid=>$action_array)
	{
		$util=array();
		$entity_name = getTabModuleName($tabid);
		$no_of_actions=sizeof($action_array);
		foreach($action_array as $action_id=>$act_per)
		{
			$action_name = getActionname($action_id);
			$tab_util_act_per = $action_array[$action_id];
			$tab_util_per = getDisplayValue($tab_util_act_per,$tabid,$action_id);
			$util[]=$action_name;
			$util[]=$tab_util_per;
		}
		$util=array_chunk($util,2);
		$util=array_chunk($util,3);
		$privilege_util[$tabid] = $util;
	}
}
elseif($mode == 'edit')
{
	$act_utility_arry = getTabsUtilityActionPermission($profileId);
	foreach($act_utility_arry as $tabid=>$action_array)
	{
		$util=array();
		$entity_name = getTabModuleName($tabid);
		$no_of_actions=sizeof($action_array);
		foreach($action_array as $action_id=>$act_per)
		{
			$action_name = getActionname($action_id);
			$tab_util_act_per = $action_array[$action_id];
			$tab_util_per = getDisplayOutput($tab_util_act_per,$tabid,$action_id);
			$util[]=$action_name;
			$util[]=$tab_util_per;
		}
		$util=array_chunk($util,2);
		$util=array_chunk($util,3);
		$privilege_util[$tabid] = $util;
	}
}
elseif($mode == 'create')
{
	if($parentProfileId != '')
	{
		$act_utility_arry = getTabsUtilityActionPermission($parentProfileId);
		foreach($act_utility_arry as $tabid=>$action_array)
		{
			$util=array();
			$entity_name = getTabModuleName($tabid);
			$no_of_actions=sizeof($action_array);
			foreach($action_array as $action_id=>$act_per)
			{
				$action_name = getActionname($action_id);
				$tab_util_act_per = $action_array[$action_id];
				$tab_util_per = getDisplayOutput($tab_util_act_per,$tabid,$action_id);
				$util[]=$action_name;
				$util[]=$tab_util_per;
			}
			$util=array_chunk($util,2);
			$util=array_chunk($util,3);
			$privilege_util[$tabid] = $util;
		}
	}
	else
	{
		$act_utility_arry = getTabsUtilityActionPermission(1);
		foreach($act_utility_arry as $tabid=>$action_array)
		{
			$util=array();
			$entity_name = getTabModuleName($tabid);
			$no_of_actions=sizeof($action_array);
			foreach($action_array as $action_id=>$act_per)
			{
				$action_name = getActionname($action_id);
				$tab_util_act_per = $action_array[$action_id];
				$tab_util_per = getDisplayOutput(0,$tabid,$action_id);
				$util[]=$action_name;
				$util[]=$tab_util_per;
			}
			$util=array_chunk($util,2);
			$util=array_chunk($util,3);
			$privilege_util[$tabid] = $util;
		}

	}

}
$smarty->assign("UTILITIES_PRIV",$privilege_util);		

//Field privileges		
$modArr=getModuleAccessArray();


$no_of_mod=sizeof($modArr);
for($i=0;$i<$no_of_mod; $i++)
{
	$fldModule=key($modArr);
	$lang_str=$modArr[$fldModule];	
	$privilege_fld[]=$fldModule;
	next($modArr);
}
$smarty->assign("PRI_FIELD_LIST",$privilege_fld);

$disable_field_array = Array();
$sql_disablefield = "select * from vtiger_def_org_field";
$result = $adb->pquery($sql_disablefield, array());
$noofrows=$adb->num_rows($result);
for($i=0; $i<$noofrows; $i++)
{
	$FieldId = $adb->query_result($result,$i,'fieldid');
	$Visible = $adb->query_result($result,$i,'visible');
	$disable_field_array[$FieldId] = $Visible;
}

if($mode=='view')
{
	$fieldListResult = getProfile2AllFieldList($modArr,$profileId);
	for($i=0; $i<count($fieldListResult);$i++)
	{
		$field_module=array();
		$module_name=key($fieldListResult);
		$module_id = getTabid($module_name);
		$language_strings = return_module_language($current_language,$module_name);
		for($j=0; $j<count($fieldListResult[$module_name]); $j++)
		{
			$field=array();
			if($fieldListResult[$module_name][$j][1] == 0)
			{
				if($fieldListResult[$module_name][$j][3] == 1) {
					$visible = "<img src='".vtiger_imageurl('locked.png', $theme)."'>";
				} else {
					$visible = "<img src='".vtiger_imageurl('unlocked.png', $theme)."'>";
				}
				//$visible = "<img src='".vtiger_imageurl('prvPrfSelectedTick.gif', $theme)."'>";
			}
			else
			{
				$visible = "<img src='".vtiger_imageurl('no.gif', $theme)."'>";
			}
			if($disable_field_array[$fieldListResult[$module_name][$j][4]] == 1)
			{
				$visible = "<img src='".vtiger_imageurl('no.gif', $theme)."'>";
			}
			if($language_strings[$fieldListResult[$module_name][$j][0]] != '')
				$field[]=$language_strings[$fieldListResult[$module_name][$j][0]];
			else
				$field[]=$fieldListResult[$module_name][$j][0];
			$field[]=$visible;
			$field_module[]=$field;
		}
		$privilege_field[$module_id] = array_chunk($field_module,3);
		next($fieldListResult);
	}
}
elseif($mode=='edit')
{
	$fieldListResult = getProfile2AllFieldList($modArr,$profileId);
	for($i=0; $i<count($fieldListResult);$i++)
	{
		$field_module=array();
		$module_name=key($fieldListResult);
		$module_id = getTabid($module_name);
		$language_strings = return_module_language($current_language,$module_name);
		for($j=0; $j<count($fieldListResult[$module_name]); $j++)
		{
			$fldLabel= $fieldListResult[$module_name][$j][0];
			$uitype = $fieldListResult[$module_name][$j][2];
			$displaytype = $fieldListResult[$module_name][$j][5];
			$typeofdata = $fieldListResult[$module_name][$j][6];
			$fieldtype = explode("~",$typeofdata);
			$mandatory = '';
			$readonly = '';
			$field=array();
			$fieldAccessMandatory = false;
			$fieldAccessRestricted = false;
			if($fieldListResult[$module_name][$j][1] == 0)
			{
				$visible = "checked";
			}
			else
			{
				$visible = "";
			}
			if($fieldtype[1] == "M")
			{
				$mandatory = '<font color="red">*</font>';
				$readonly = 'disabled';
				$visible = "checked";
				$fieldAccessMandatory = true;
			}
			if($disable_field_array[$fieldListResult[$module_name][$j][4]] == 1)
			{
				$mandatory = '<font color="blue">*</font>';
				$readonly = 'disabled';
				$visible = "";
				$fieldAccessRestricted = true;
			}
			
			if($language_strings[$fldLabel] != '')
				$field[]=$mandatory.' '.$language_strings[$fldLabel];
			else
				$field[]=$mandatory.' '.$fldLabel;
							
			$field[]='<input id="'.$module_id.'_field_'.$fieldListResult[$module_name][$j][4].'" onClick="selectUnselect(this);" type="checkbox" name="'.$fieldListResult[$module_name][$j][4].'" '.$visible.' '.$readonly.'>';
			
			// Check for Read-Only or Read-Write Access for the field.
			$fieldReadOnlyAccess = $fieldListResult[$module_name][$j][3];
			if($fieldReadOnlyAccess == 1) {
				$display_locked = "inline";
				$display_unlocked = "none";
			} else {
				$display_locked = "none";
				$display_unlocked = "inline";				
			}	
			if(!$fieldAccessMandatory && !$fieldAccessRestricted) {
				$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4].'_readonly" value="'.$fieldReadOnlyAccess.'" />' .
						'<a href="javascript:void(0);" onclick="toogleAccess(\''.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'\');">' .
						'<img id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'_unlocked" src="'.vtiger_imageurl('unlocked.png', $theme).'" style="display:'.$display_unlocked.'" border="0">' .
						'<img id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'_locked" src="'.vtiger_imageurl('locked.png', $theme).'" style="display:'.$display_locked.'" border="0"></a>';
			} elseif($fieldAccessMandatory) {
				$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4].'_readonly" value="0" />' .
							'<img src="'.vtiger_imageurl('blank.gif', $theme).'" style="display:inline" border="0">';
			} else {
				$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4].'_readonly" value="'.$fieldReadOnlyAccess.'" />' .
							'<img src="'.vtiger_imageurl('blank.gif', $theme).'" style="display:inline" border="0">';
			}
			$field_module[]=$field;
		}
		$privilege_field[$module_id] = array_chunk($field_module,3);
		next($fieldListResult);
	}
}
elseif($mode=='create')
{
	if($parentProfileId != '')
	{
		$fieldListResult = getProfile2AllFieldList($modArr,$parentProfileId);
		for($i=0; $i<count($fieldListResult);$i++)
		{
			$field_module=array();
			$module_name=key($fieldListResult);
			$module_id = getTabid($module_name);
			$language_strings = return_module_language($current_language,$module_name);
			for($j=0; $j<count($fieldListResult[$module_name]); $j++)
			{
				$fldLabel= $fieldListResult[$module_name][$j][0];
				$uitype = $fieldListResult[$module_name][$j][2];
				$displaytype = $fieldListResult[$module_name][$j][5];
				$typeofdata = $fieldListResult[$module_name][$j][6];
				$fieldtype = explode("~",$typeofdata);
				$mandatory = '';
				$readonly = '';
				$field=array();

				
				$fieldAccessMandatory = false;
				$fieldAccessRestricted = false;
				if($fieldtype[1] == "M")
				{
					$mandatory = '<font color="red">*</font>';
					$readonly = 'disabled';
					$fieldAccessMandatory = true;
				}	
				if($fieldListResult[$module_name][$j][1] == 0)
				{
					$visible = 'checked';
				}
				else
				{
					$visible = "";
				}
				if($disable_field_array[$fieldListResult[$module_name][$j][4]] == 1)
				{
					$mandatory = '<font color="blue">*</font>';
					$readonly = 'disabled';
					$visible = "";
					$fieldAccessRestricted = true;
				}
				if($language_strings[$fldLabel] != '')
					$field[]=$mandatory.' '.$language_strings[$fldLabel];
				else
					$field[]=$mandatory.' '.$fldLabel;
				$field[]='<input type="checkbox" id="'.$module_id.'_field_'.$fieldListResult[$module_name][$j][4].'" onClick="selectUnselect(this);" name="'.$fieldListResult[$module_name][$j][4].'" '.$visible.' '.$readonly.'>';
							
				// Check for Read-Only or Read-Write Access for the field.
				$fieldReadOnlyAccess = $fieldListResult[$module_name][$j][3];
				if($fieldReadOnlyAccess == 1) {
					$display_locked = "inline";
					$display_unlocked = "none";
				} else {
					$display_locked = "none";
					$display_unlocked = "inline";				
				}	
				if(!$fieldAccessMandatory && !$fieldAccessRestricted) {
					$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4].'_readonly" value="'.$fieldReadOnlyAccess.'" />' .
							'<a href="javascript:void(0);" onclick="toogleAccess(\''.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'\');">' .
							'<img id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'_unlocked" src="'.vtiger_imageurl('unlocked.png', $theme).'" style="display:'.$display_unlocked.'" border="0">' .
							'<img id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'_locked" src="'.vtiger_imageurl('locked.png', $theme).'" style="display:'.$display_locked.'" border="0"></a>';
				} elseif($fieldAccessMandatory) {
					$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4].'_readonly" value="0" />' .
							'<img src="'.vtiger_imageurl('blank.gif', $theme).'" style="display:inline" border="0">';
				} else {
					$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4].'_readonly" value="'.$fieldReadOnlyAccess.'" />' .
							'<img src="'.vtiger_imageurl('blank.gif', $theme).'" style="display:inline" border="0">';
				}
				$field_module[]=$field;
			}
			$privilege_field[$module_id] = array_chunk($field_module,3);
			next($fieldListResult);
		}
	}
	else
	{
		$fieldListResult = getProfile2AllFieldList($modArr,1);
		for($i=0; $i<count($fieldListResult);$i++)
		{
			$field_module=array();
			$module_name=key($fieldListResult);
			$module_id = getTabid($module_name);
			$language_strings = return_module_language($current_language,$module_name);
			for($j=0; $j<count($fieldListResult[$module_name]); $j++)
			{
				$fldLabel= $fieldListResult[$module_name][$j][0];
				$uitype = $fieldListResult[$module_name][$j][2];
				$displaytype = $fieldListResult[$module_name][$j][5];
				$typeofdata = $fieldListResult[$module_name][$j][6];
				$fieldtype = explode("~",$typeofdata);
				$mandatory = '';
				$readonly = '';
				$field=array();

				$fieldAccessMandatory = false;
				$fieldAccessRestricted = false;				
				if($fieldtype[1] == "M")
				{
					$mandatory = '<font color="red">*</font>';
					$readonly = 'disabled';
					$fieldAccessMandatory = true;
				}	
				
				if($disable_field_array[$fieldListResult[$module_name][$j][4]] == 1)
				{
					$mandatory = '<font color="blue">*</font>';
					$readonly = 'disabled';
					$visible = "";
					$fieldAccessRestricted = true;
				}else
				{
					$visible = "checked";
				}
				if($language_strings[$fldLabel] != '')
					$field[]=$mandatory.' '.$language_strings[$fldLabel];
				else
					$field[]=$mandatory.' '.$fldLabel;
				$field[]='<input type="checkbox" id="'.$module_id.'_field_'.$fieldListResult[$module_name][$j][4].'"  onClick="selectUnselect(this);" name="'.$fieldListResult[$module_name][$j][4].'" '.$visible.' '.$readonly.'>';
				
				// Check for Read-Only or Read-Write Access for the field.
				$fieldReadOnlyAccess = $fieldListResult[$module_name][$j][3];
				if($fieldReadOnlyAccess == 1) {
					$display_locked = "inline";
					$display_unlocked = "none";
				} else {
					$display_locked = "none";
					$display_unlocked = "inline";				
				}	
				if(!$fieldAccessMandatory && !$fieldAccessRestricted) {
					$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4].'_readonly" value="'.$fieldReadOnlyAccess.'" />' .
							'<a href="javascript:void(0);" onclick="toogleAccess(\''.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'\');">' .
							'<img id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'_unlocked" src="'.vtiger_imageurl('unlocked.png', $theme).'" style="display:'.$display_unlocked.'" border="0">' .
							'<img id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'_locked" src="'.vtiger_imageurl('locked.png', $theme).'" style="display:'.$display_locked.'" border="0"></a>';
				} elseif($fieldAccessMandatory) {
					$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4].'_readonly" value="0" />' .
							'<img src="'.vtiger_imageurl('blank.gif', $theme).'" style="display:inline" border="0">';
				} else {
					$field[] = '<input type="hidden" id="'.$module_id.'_readonly_'.$fieldListResult[$module_name][$j][4].'" name="'.$fieldListResult[$module_name][$j][4].'_readonly" value="'.$fieldReadOnlyAccess.'" />' .
							'<img src="'.vtiger_imageurl('blank.gif', $theme).'" style="display:inline" border="0">';
				}
				$field_module[]=$field;
			}
			$privilege_field[$module_id] = array_chunk($field_module,3);
			next($fieldListResult);
		}	
	}
}

$smarty->assign("FIELD_PRIVILEGES",$privilege_field);	
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
if($mode == 'view')
	$smarty->display("ProfileDetailView.tpl");
else
	$smarty->display("EditProfile.tpl");

/** returns html image code based on the input id
  * @param $id -- Role Name:: Type varchar
  * @returns $value -- html image code:: Type varcha:w
  *
 */	
function getGlobalDisplayValue($id,$actionid)
{
	global $image_path;
	if($id == '')
	{
		$value = '&nbsp;';
	}
	elseif($id == 0)
	{
		$value = '<img src="' . vtiger_imageurl('prvPrfSelectedTick.gif', $theme) . '">';
	}
	elseif($id == 1)
	{
		$value = '<img src="' . vtiger_imageurl('no.gif', $theme) . '">';
	}
	else
	{
		$value = '&nbsp;';
	}

	return $value;

}


/** returns html check box code based on the input id
  * @param $id -- Role Name:: Type varchar
  * @returns $value -- html check box code:: Type varcha:w
  *
 */
function getGlobalDisplayOutput($id,$actionid)
{
	if($actionid == '1')
	{
		$name = 'view_all';
	}
	elseif($actionid == '2')
	{

		$name = 'edit_all';
	}

	if($id == '' && $id != 0)
	{
		$value = '';
	}
	elseif($id == 0)
	{
		$value = '<input type="checkbox" id="'.$name.'_chk" onClick="invoke'.$name.'();" name="'.$name.'" checked>';
	}
	elseif($id == 1)
	{
		$value = '<input type="checkbox" id="'.$name.'_chk" onClick="invoke'.$name.'();" name="'.$name.'">';
	}
	return $value;

}


/** returns html image code based on the input id
  * @param $id -- Role Name:: Type varchar
  * @returns $value -- html image code:: Type varcha:w
  *
 */
function getDisplayValue($id)
{
	global $image_path;

	if($id == '')
	{
		$value = '&nbsp;';
	}
	elseif($id == 0)
	{
		$value = '<img src="' . vtiger_imageurl('prvPrfSelectedTick.gif', $theme) .'">';
	}
	elseif($id == 1)
	{
		$value = '<img src="' . vtiger_imageurl('no.gif', $theme) .'">';
	}
	else
	{
		$value = '&nbsp;';
	}
	return $value;

}


/** returns html check box code based on the input id
  * @param $id -- Role Name:: Type varchar
  * @returns $value -- html check box code:: Type varcha:w
  *
 */
function getDisplayOutput($id,$tabid,$actionid)
{
	if($actionid == '')
	{
		$name = $tabid.'_tab';
		$ckbox_id = 'tab_chk_com_'.$tabid; 
		$jsfn = 'hideTab('.$tabid.')';
	}
	else
	{
		$temp_name = getActionname($actionid);
		$name = $tabid.'_'.$temp_name;
		$ckbox_id = 'tab_chk_'.$actionid.'_'.$tabid; 
		if($actionid == 1)	
			$jsfn = 'unSelectCreate('.$tabid.')'; 
		elseif($actionid == 4)	
			$jsfn = 'unSelectView('.$tabid.')';
		elseif($actionid == 2)	
			$jsfn = 'unSelectDelete('.$tabid.')';
		else
		{
			$ckbox_id = $tabid.'_field_util_'.$actionid; 
			$jsfn = 'javascript:';
		}	
	}



	if($id == '' && $id != 0)
	{
		$value = '';
	}
	elseif($id == 0)
	{
		$value = '<input type="checkbox" onClick="'.$jsfn.';" id="'.$ckbox_id.'" name="'.$name.'" checked>';
	}
	elseif($id == 1)
	{
		$value = '<input type="checkbox" onClick="'.$jsfn.';" id="'.$ckbox_id.'" name="'.$name.'">';
	}
	return $value;

}

function profileExists($profileId) {
	global $adb;
	
	$result = $adb->pquery('SELECT 1 FROM vtiger_profile WHERE profileid = ?', array($profileId));
	if($adb->num_rows($result) > 0) return true;
	return false;
}
?>