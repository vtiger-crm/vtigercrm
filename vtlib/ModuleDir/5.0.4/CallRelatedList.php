<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('Smarty_setup.php');
require_once('user_privileges/default_module_view.php');
require_once("modules/$currentModule/$currentModule.php");

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $singlepane_view;

$category = getParentTab();
$action = $_REQUEST['action'];
$record = $_REQUEST['record'];
$isduplicate = $_REQUEST['isDuplicate'];
$parenttab = $_REQUEST['parenttab'];

if($singlepane_view == 'true' && $action == 'CallRelatedList') {
	header("Location:index.php?action=DetailView&module=$currentModule&record=$record&parenttab=$parenttab");
} else {
	
	$tool_buttons = Button_Check($currentModule);

	$focus = new $currentModule();
	if($record != '') {
	    $focus->retrieve_entity_info($record, $currentModule);
   		$focus->id = $record;
	}

	$smarty = new vtigerCRM_Smarty;

	if($isduplicate == 'true') $focus->id = '';
	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] != ' ') $smarty->assign("OP_MODE",$_REQUEST['mode']);
	if(!$_SESSION['rlvs'][$currentModule]) unset($_SESSION['rlvs']);

	// Identify this module as custom module.
	$smarty->assign('CUSTOM_MODULE', true);

	$smarty->assign('APP', $app_strings);
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('MODULE', $currentModule);
	// TODO: Update Single Module Instance name here.
	$smarty->assign('SINGLE_MOD', $currentModule); 
	$smarty->assign('CATEGORY', $category);
	$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
	$smarty->assign('THEME', $theme);
	$smarty->assign('ID', $focus->id);
	$smarty->assign('MODE', $focus->mode);
	$smarty->assign('CHECK', $tool_buttons);

	$smarty->assign('NAME', $focus->column_fields[$focus->def_detailview_recname]);
	$smarty->assign('UPDATEINFO',updateInfo($focus->id));

	$related_array = getRelatedLists($currentModule, $focus);

	$smarty->assign('RELATEDLISTS', $related_array);
	$smarty->display('RelatedLists.tpl');
}
?>