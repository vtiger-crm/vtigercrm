<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('Smarty_setup.php');
//Redirecting Header for single page layout
require_once('user_privileges/default_module_view.php');
global $singlepane_view;
$currentmodule = vtlib_purify($_REQUEST['module']);
$RECORD = vtlib_purify($_REQUEST['record']);
$category = getParentTab();
if($singlepane_view == 'true' && $_REQUEST['action'] == 'CallRelatedList' ) {
	header("Location:index.php?action=DetailView&module=$currentmodule&record=$RECORD&parenttab=$category");
} else {
	$focus = CRMEntity::getInstance($currentmodule);
	if(isset($_REQUEST['record']) && $_REQUEST['record']!='') {
	    $focus->retrieve_entity_info($RECORD,$currentmodule);
	    $focus->id = $RECORD;
		$focus->name=$focus->column_fields['bookname'];
		$log->debug("PriceBook id =".$focus->id);
		$log->debug("PriceBook Name =".$focus->name);
	}
	if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
		$focus->id = "";
	}
	
	$related_array=getRelatedLists($currentModule,$focus);
	
	global $mod_strings;
	global $app_strings;
	global $theme, $currentModule;
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	
	$smarty = new vtigerCRM_Smarty;
	
	if(isset($focus->name))
		$smarty->assign("NAME", $focus->name);
	$smarty->assign("CATEGORY",$category);
	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] != ' ') {
		$smarty->assign("OP_MODE",vtlib_purify($_REQUEST['mode']));
	}
	$smarty->assign("TODO_PERMISSION",CheckFieldPermission('parent_id','Calendar'));
	$smarty->assign("EVENT_PERMISSION",CheckFieldPermission('parent_id','Events'));
	$smarty->assign("ID",$focus->id);
	
	// Module Sequence Numbering
	$mod_seq_field = getModuleSequenceField($currentModule);
	if ($mod_seq_field != null) {
		$mod_seq_id = $focus->column_fields[$mod_seq_field['name']];
	} else {
		$mod_seq_id = $focus->id;
	}
	$smarty->assign('MOD_SEQ_ID', $mod_seq_id);
	// END
	
	$smarty->assign("CURRENCY_ID",$focus->column_fields['currency_id']);
	$smarty->assign("MODULE",$currentmodule);
	$smarty->assign("RELATEDLISTS", $related_array);
		
	require_once('include/ListView/RelatedListViewSession.php');
	if(!empty($_REQUEST['selected_header']) && !empty($_REQUEST['relation_id'])) {
		$relationId = vtlib_purify($_REQUEST['relation_id']);
		RelatedListViewSession::addRelatedModuleToSession($relationId,
				vtlib_purify($_REQUEST['selected_header']));
	}
	$open_related_modules = RelatedListViewSession::getRelatedModulesFromSession();
	$smarty->assign("SELECTEDHEADERS", $open_related_modules);
	
	$smarty->assign("SINGLE_MOD",$app_strings['PriceBook']);
	$smarty->assign("UPDATEINFO",updateInfo($focus->id));
	$smarty->assign("MOD",$mod_strings);
	$smarty->assign("APP",$app_strings);
	$smarty->assign("THEME", $theme);
	$smarty->assign("IMAGE_PATH", $image_path);
	
	$check_button = Button_Check($module);
	$smarty->assign("CHECK", $check_button);
	if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
		$smarty->display("RelatedListContents.tpl");
	else
		$smarty->display("RelatedLists.tpl");
}
?>