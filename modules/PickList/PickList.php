<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once('Smarty_setup.php');
require_once('include/database/PearDatabase.php');
require_once('database/DatabaseConnection.php');
require_once 'include/utils/CommonUtils.php';
require_once 'modules/PickList/PickListUtils.php';

global $app_strings, $app_list_strings, $current_language, $currentModule, $theme, $current_user;

if(!is_admin($current_user)) {
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
			<table border='0' cellpadding='5' cellspacing='0' width='98%'>
				<tr>
					<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) . "' ></td>
					<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
				</tr>
				<tr>
					<td class='small' align='right' nowrap='nowrap'>
						<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>
					</td>
				</tr>
			</table>
		</div>";
	echo "</td></tr></table>";
	die;
}

$modules = getPickListModules();
if(!empty($_REQUEST['moduleName'])) {
	$fld_module = vtlib_purify($_REQUEST['moduleName']);
}else {
	$module = array_keys($modules);
	$fld_module = $module[0];
}

if(!empty($_REQUEST['roleid'])) {
	$roleid = vtlib_purify($_REQUEST['roleid']);
}else {
	$roleid = 'H2';		//set default to CEO
}

if(!empty($_REQUEST['uitype'])) {
	$uitype = vtlib_purify($_REQUEST['uitype']);
}

$smarty = new vtigerCRM_Smarty;

if((sizeof($picklists_entries) %3) != 0) {
	$value = (sizeof($picklists_entries) + 3 - (sizeof($picklists_entries))%3);
}else {
	$value = sizeof($picklists_entries);
}

if($fld_module == 'Events') {
	$temp_module_strings = return_module_language($current_language, 'Calendar');
}else {
	$temp_module_strings = return_module_language($current_language, $fld_module);
}
$picklists_entries = getUserFldArray($fld_module,$roleid);
$available_module_picklist = array();
$picklist_fields = array();
if(!empty($picklists_entries)) {
	$available_module_picklist = get_available_module_picklist($picklists_entries);
	$picklist_fields = array_chunk(array_pad($picklists_entries,$value,''),3);
}

$smarty->assign("MODULE_LISTS",$modules);
$smarty->assign("ROLE_LISTS",getrole2picklist());
$smarty->assign("ALL_LISTS",$available_module_picklist);

$smarty->assign("APP", $app_strings);		//the include language files
$smarty->assign("MOD", return_module_language($current_language,'Settings'));	//the settings module language file
$smarty->assign("MOD_PICKLIST", return_module_language($current_language,'PickList'));	//the picklist module language files
$smarty->assign("TEMP_MOD", $temp_module_strings);	//the selected modules' language file

$smarty->assign("MODULE",$fld_module);
$smarty->assign("PICKLIST_VALUES",$picklist_fields);
$smarty->assign("THEME",$theme);
$smarty->assign("UITYPE", $uitype);
$smarty->assign("SEL_ROLEID",$roleid);

if($_REQUEST['directmode'] != 'ajax') {
	$smarty->display("modules/PickList/PickList.tpl");
}else {
	$smarty->display("modules/PickList/PickListContents.tpl");
}

?>
