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
require_once('include/utils/utils.php');

//Redirecting Header for single page layout
require_once('user_privileges/default_module_view.php');
global $singlepane_view;
$currentmodule = vtlib_purify($_REQUEST['module']);
$RECORD = vtlib_purify($_REQUEST['record']);
$category = getParentTab();
if($singlepane_view == 'true' && $_REQUEST['action'] == 'CallRelatedList') {
	header("Location:index.php?action=DetailView&module=$currentmodule&record=$RECORD&parenttab=$category");
} else {
	$focus = CRMEntity::getInstance($currentmodule);
	if(isset($_REQUEST['record']) && $_REQUEST['record']!='') {
	    $focus->retrieve_entity_info($RECORD,$currentmodule);
	    $focus->id = $RECORD;
	    $focus->name=$focus->column_fields['firstname'].' '.$focus->column_fields['lastname'];	
		$log->debug("id is ".$focus->id);	
		$log->debug("name is ".$focus->name);	
	}
	
	global $adb;
	$sql = $adb->pquery('select accountid from vtiger_contactdetails where contactid=?', array($focus->id));
	$accountid = $adb->query_result($sql,0,'accountid');
	if($accountid == 0) $accountid='';
	
	global $mod_strings;
	global $app_strings;
	global $theme;
	global $currentModule;
	global $current_user;
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	
	$smarty = new vtigerCRM_Smarty;
	$smarty->assign("accountid",$accountid);
		
	
	if(isset($_request['isduplicate']) && $_request['isduplicate'] == 'true') {
		$focus->id = "";
	}
	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] != ' ') {
		$smarty->assign("OP_MODE",vtlib_purify($_REQUEST['mode']));
	}
	$parent_email = getEmailParentsList('Contacts',$_REQUEST['record'], $focus);
	        $smarty->assign("HIDDEN_PARENTS_LIST",$parent_email);
	
	if(isset($_REQUEST['record']) && $_REQUEST['record']!='') {
		$userid = $current_user->id;
		$sql = "select fieldname from vtiger_field where uitype = '13' and tabid = 4 and vtiger_field.presence in (0,2)";
		$result = $adb->pquery($sql, array());
		$num_fieldnames = $adb->num_rows($result);
		for($i = 0; $i < $num_fieldnames; $i++) {
			$fieldname = $adb->query_result($result,$i,"fieldname");
			$permit= getFieldVisibilityPermission("Contacts",$userid,$fieldname);
		}
	}
	$smarty->assign("TODO_PERMISSION",CheckFieldPermission('parent_id','Calendar'));
	$smarty->assign("CONTACT_PERMISSION",CheckFieldPermission('contact_id','Calendar'));
	$smarty->assign("EVENT_PERMISSION",CheckFieldPermission('parent_id','Events'));
	$smarty->assign("CATEGORY",$category);
	$smarty->assign("ID",$focus->id);
	$smarty->assign("NAME",$focus->name);
	$smarty->assign("EMAIL",$focus->column_fields['email']);
	$smarty->assign("SECONDARY_EMAIL",$focus->column_fields['secondaryemail']);
	
	// Module Sequence Numbering
	$mod_seq_field = getModuleSequenceField($currentModule);
	if ($mod_seq_field != null) {
		$mod_seq_id = $focus->column_fields[$mod_seq_field['name']];
	} else {
		$mod_seq_id = $focus->id;
	}
	$smarty->assign('MOD_SEQ_ID', $mod_seq_id);
	// END
	
	$related_array = getRelatedLists($currentModule,$focus);
	require_once('include/ListView/RelatedListViewSession.php');
	if(!empty($_REQUEST['selected_header']) && !empty($_REQUEST['relation_id'])) {
		$relationId = vtlib_purify($_REQUEST['relation_id']);
		RelatedListViewSession::addRelatedModuleToSession($relationId,
				vtlib_purify($_REQUEST['selected_header']));
	}
	$open_related_modules = RelatedListViewSession::getRelatedModulesFromSession();
	$smarty->assign("SELECTEDHEADERS", $open_related_modules);
	
	$smarty->assign("RELATEDLISTS", $related_array);
	$smarty->assign("MODULE",$currentmodule);
	$smarty->assign("SINGLE_MOD",$app_strings['Contact']);
	$smarty->assign("UPDATEINFO",updateInfo($focus->id));
	$smarty->assign("MOD",$mod_strings);
	$smarty->assign("APP",$app_strings);
	$smarty->assign("THEME", $theme);
	$smarty->assign("IMAGE_PATH", $image_path);
	
	$check_button = Button_Check($module);
	$smarty->assign("CHECK", $check_button);
	$smarty->display("RelatedLists.tpl");
}
?>