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

global $mod_strings;
global $app_strings;
global $app_list_strings;

global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$sql="select * from vtiger_inventory_tandc";
$result = $adb->pquery($sql, array());
$inventory_id = $adb->query_result($result,0,'id');
$inventory_type = $adb->query_result($result,0,'type');
$inventory_tandc = $adb->query_result($result,0,'tandc');

if(!isset($_REQUEST['inv_terms_mode']))
	$inventory_tandc = nl2br($inventory_tandc);
	
if (isset($inventory_tandc))
        $smarty->assign("INV_TERMSANDCONDITIONS",$inventory_tandc);
if(isset($_REQUEST['inv_terms_mode']) && $_REQUEST['inv_terms_mode'] != '')
	$smarty->assign("INV_TERMS_MODE",vtlib_purify($_REQUEST['inv_terms_mode']));
else
	$smarty->assign("INV_TERMS_MODE",'view');
	
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->display("Settings/InventoryTerms.tpl");
?>