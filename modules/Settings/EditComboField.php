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
require_once('Smarty_setup.php');
global $mod_strings, $adb;
global $app_strings;
global $app_list_strings, $current_language;

$tableName=vtlib_purify($_REQUEST["fieldname"]);
$tableName = $adb->sql_escape_string($tableName);
$moduleName=vtlib_purify($_REQUEST["fld_module"]);
$uitype=vtlib_purify($_REQUEST["uitype"]);

if(isset($_REQUEST['parentroleid']) && $_REQUEST['parentroleid']  != '') {
	$roleid = vtlib_purify($_REQUEST['parentroleid']);
} else {
	$roleid = vtlib_purify($_REQUEST["roleid"]);
}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;

//Added to get the strings from language files if present
if($moduleName == 'Events')
	$temp_module_strings = return_module_language($current_language, 'Calendar');
else
	$temp_module_strings = return_module_language($current_language, $moduleName);

//To get the Editable Picklist Values 
$query = "select * from vtiger_".$tableName." inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid=vtiger_$tableName.picklist_valueid where roleid=? and  presence=1 order by sortid"; 
$result = $adb->pquery($query, array($roleid));
$fldVal='';

while($row = $adb->fetch_array($result))
{
	if($temp_module_strings[$row[$tableName]] != '')
		$fldVal .= $temp_module_strings[$row[$tableName]];
	else
		$fldVal .= $row[$tableName];
	$fldVal .= "\n";	
}

if(isset($_REQUEST['parentroleid']) && $_REQUEST['parentroleid']!= '')
{
	echo '<textarea id="picklist" style="display:none;">'.$fldVal.'</textarea>';
	echo '<script>window.opener.document.getElementById("picklist_values").value = document.getElementById("picklist").value;</script>';

	echo '<script>window.close();</script>';
	$roleid = vtlib_purify($_REQUEST['parentroleid']);
	die;
}

//To get the Non Editable Picklist Entries
$qry = "select * from vtiger_".$tableName." inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid=vtiger_$tableName.picklist_valueid where roleid=? and presence=0 order by sortid"; 
$res = $adb->pquery($qry, array($roleid));
if($adb->num_rows($res) > 0)
{
	$nonedit_fldVal='<div id="nonedit_pl">';
	$c = 0;
	while($row = $adb->fetch_array($res))
	{
		if($c != 0)	
			$nonedit_fldVal .= "<br>";
		if($temp_module_strings[$row[$tableName]] != '')
			$nonedit_fldVal .= $temp_module_strings[$row[$tableName]];
		else
			$nonedit_fldVal .= $row[$tableName];
		$c++;
	}
	$nonedit_fldVal .= "</div>";
}	

$query = 'select fieldlabel from vtiger_tab inner join vtiger_field on vtiger_tab.tabid=vtiger_field.tabid where vtiger_tab.name=? and fieldname=? and vtiger_field.presence in (0,2)';
$fieldlabel = $adb->query_result($adb->pquery($query, array($moduleName, $tableName)),0,'fieldlabel'); 

if($nonedit_fldVal == '')
	$smarty->assign("EDITABLE_MODE","edit");
else
	$smarty->assign("EDITABLE_MODE","nonedit");
$smarty->assign("NON_EDITABLE_ENTRIES", $nonedit_fldVal);
$smarty->assign("ENTRIES",$fldVal);
$smarty->assign("MODULE",$moduleName);
$smarty->assign("FIELDNAME",$tableName);
//First look into app_strings and then mod_strings and if not available then original label will be displayed
$temp_label = getTranslatedString($fieldlabel);
$smarty->assign("FIELDLABEL",$temp_label);
$smarty->assign("UITYPE", $uitype);
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("TEMP_MOD", $temp_module_strings);

$smarty->display("Settings/EditPickList.tpl");
?>