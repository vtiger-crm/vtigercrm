<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once 'modules/PickList/PickListUtils.php';
require_once('Smarty_setup.php');

global $app_strings, $current_language,$adb, $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$smarty->assign("IMAGE_PATH",$image_path);

$fieldName=vtlib_purify($_REQUEST["fieldname"]);
$fieldLabel =vtlib_purify($_REQUEST["fieldlabel"]);
$moduleName=vtlib_purify($_REQUEST["moduleName"]);
$mode=$_REQUEST["mode"];

if($moduleName == 'Events'){
	$temp_module_strings = return_module_language($current_language, 'Calendar');
}else{
	$temp_module_strings = return_module_language($current_language, $moduleName);
}

if(isset($fieldName)){
	$editableValues = getEditablePicklistValues($fieldName, $temp_module_strings, $adb);
	$nonEditableValues = getNonEditablePicklistValues($fieldName, $temp_module_strings, $adb);
}
$temp_label = getTranslatedString($fieldLabel);
$roleDetails=getAllRoleDetails();

// Remove Organization from the list of roles (which is the first one in the list of roles)
array_shift($roleDetails);

$smarty->assign("FIELDNAME",$fieldName);
$smarty->assign("FIELDLABEL",$temp_label);
$smarty->assign('NONEDITPICKLIST',$nonEditableValues);
$smarty->assign("MODULE",$moduleName);
$smarty->assign("PICKVAL",$editableValues);
$smarty->assign("ROLEDETAILS",$roleDetails);
$smarty->assign("MOD", return_module_language($current_language,'PickList'));
$smarty->assign("APP",$app_strings);

if($mode == 'add'){
	$data = $smarty->fetch("modules/PickList/AddPickList.tpl");
}elseif($mode == 'edit'){
	$data = $smarty->fetch("modules/PickList/EditPickList.tpl");
}elseif($mode == 'delete'){
	$data = $smarty->fetch("modules/PickList/DeletePickList.tpl");
}

echo $data;
?>