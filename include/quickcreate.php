<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

require_once("Smarty_setup.php");
require_once("include/utils/CommonUtils.php");
require_once("include/FormValidationUtil.php");

global $mod_strings,$current_user;
global $app_strings, $currentModule;
global $adb;
global $app_list_strings;
global $theme;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;

$qcreate_array = QuickCreate("$module");
$validationData = $qcreate_array['data'];
$data = split_validationdataArray($validationData);
$smarty->assign("QUICKCREATE", $qcreate_array['form']);
$smarty->assign("THEME",$theme);
$smarty->assign("APP",$app_strings);
$smarty->assign("MOD",$mod_strings);
$smarty->assign("THEME",$theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("ACTIVITY_MODE", vtlib_purify($_REQUEST['activity_mode']));
if($module == 'Calendar')
	$smarty->assign("QCMODULE", getTranslatedString('Todo', 'Calendar'));
elseif($module == "HelpDesk")
	$smarty->assign("QCMODULE", getTranslatedString('Ticket', 'HelpDesk'));
else
	$smarty->assign("QCMODULE",getTranslatedString("SINGLE_".$currentModule, $currentModule));
$smarty->assign("USERID",$current_user->id);
$smarty->assign("VALIDATION_DATA_FIELDNAME",$data['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$data['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL",$data['fieldlabel']);
$smarty->assign("MODULE", $currentModule);
$smarty->assign("CATEGORY",$category);

$smarty->display("QuickCreate.tpl");

?>