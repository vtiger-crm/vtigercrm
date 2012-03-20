<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('data/Tracker.php');
require_once('Smarty_setup.php');
require_once('include/upload_file.php');
require_once('include/utils/utils.php');
global $app_strings;
global $mod_strings;
global $currentModule;

$focus = CRMEntity::getInstance($currentModule);

if(isset($_REQUEST['record'])) {
   $focus->retrieve_entity_info($_REQUEST['record'],"Documents");
   $focus->id = $_REQUEST['record'];
   $focus->name=$focus->column_fields['notes_title'];
}
if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = "";
}

//needed when creating a new note with default values passed in
if (isset($_REQUEST['contact_name']) && is_null($focus->contact_name)) {
	$focus->contact_name = $_REQUEST['contact_name'];
}
if (isset($_REQUEST['contact_id']) && is_null($focus->contact_id)) {
	$focus->contact_id = $_REQUEST['contact_id'];
}
if (isset($_REQUEST['opportunity_name']) && is_null($focus->parent_name)) {
	$focus->parent_name = $_REQUEST['opportunity_name'];
}
if (isset($_REQUEST['opportunity_id']) && is_null($focus->parent_id)) {
	$focus->parent_id = $_REQUEST['opportunity_id'];
}
if (isset($_REQUEST['account_name']) && is_null($focus->parent_name)) {
	$focus->parent_name = $_REQUEST['account_name'];
}
if (isset($_REQUEST['account_id']) && is_null($focus->parent_id)) {
	$focus->parent_id = $_REQUEST['account_id'];
}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$filename=$focus->column_fields['filename'];
$folderid = $focus->column_fields['folderid'];
$filestatus = $focus->column_fields['filestatus'];
$filelocationtype = $focus->column_fields['filelocationtype'];

$fileattach = "select attachmentsid from vtiger_seattachmentsrel where crmid = ?";
$res = $adb->pquery($fileattach,array($focus->id));
$fileid = $adb->query_result($res,0,'attachmentsid');

if($filelocationtype == 'I'){
	$pathQuery = $adb->pquery("select path from vtiger_attachments where attachmentsid = ?",array($fileid));
	$filepath = $adb->query_result($pathQuery,0,'path');
}
else{
	$filepath = $filename;
}


$smarty->assign("FILEID",$fileid);
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);

$allblocks = getBlocks($currentModule,"detail_view",'',$focus->column_fields);
$smarty->assign("BLOCKS", $allblocks);
$flag = 0;
foreach($allblocks as $blocks)
{
	foreach($blocks as $block_entries)
	{
		if(!empty($block_entries[getTranslatedString('File Name',$currentModule)]['value']))
			$flag = 1;
	}
}
if($flag == 1)
	$smarty->assign("FILE_EXIST","yes");
elseif($flag == 0)
	$smarty->assign("FILE_EXIST","no");

$smarty->assign("UPDATEINFO",updateInfo($focus->id));

if (isset($focus->name)) $smarty->assign("NAME", $focus->name);
else $smarty->assign("NAME", "");

$smarty->assign("FILENAME", $filename);

if (isset($_REQUEST['return_module'])) $smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
if (isset($_REQUEST['return_action'])) $smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));
if (isset($_REQUEST['return_id'])) $smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));

$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
$smarty->assign("ID", $focus->id);

// Module Sequence Numbering
$mod_seq_field = getModuleSequenceField($currentModule);
if ($mod_seq_field != null) {
	$mod_seq_id = $focus->column_fields[$mod_seq_field['name']];
} else {
	$mod_seq_id = $focus->id;
}
$smarty->assign('MOD_SEQ_ID', $mod_seq_id);
// END

$category = getParentTab();
$smarty->assign("CATEGORY",$category);


$smarty->assign("SINGLE_MOD", 'Document');

if(isPermitted("Documents","EditView",$_REQUEST['record']) == 'yes')
	$smarty->assign("EDIT_DUPLICATE","permitted");

if(isPermitted("Documents","Delete",$_REQUEST['record']) == 'yes')
	$smarty->assign("DELETE","permitted");

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

$smarty->assign("IS_REL_LIST",isPresentRelatedLists($currentModule));


$tabid = getTabid("Documents");
 $validationData = getDBValidationData($focus->tab_name,$tabid);
 $data = split_validationdataArray($validationData);

 $smarty->assign("VALIDATION_DATA_FIELDNAME",$data['fieldname']);
 $smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$data['datatype']);
 $smarty->assign("VALIDATION_DATA_FIELDLABEL",$data['fieldlabel']);
 if(is_admin($current_user))
{
 	$smarty->assign("CHECK_INTEGRITY_PERMISSION","yes");
    $smarty->assign("ADMIN","yes");
}
$smarty->assign("FILE_STATUS",$filestatus);
 $smarty->assign("DLD_TYPE",$filelocationtype);
 $smarty->assign("NOTESID",$focus->id);
 $smarty->assign("FOLDERID",$folderid);
 $smarty->assign("DLD_PATH",$filepath);

$smarty->assign("MODULE",$currentModule);
$smarty->assign("EDIT_PERMISSION",isPermitted($currentModule,'EditView',$_REQUEST['record']));

if(PerformancePrefs::getBoolean('DETAILVIEW_RECORD_NAVIGATION', true) && isset($_SESSION[$currentModule.'_listquery'])){
	$recordNavigationInfo = ListViewSession::getListViewNavigation($focus->id);
	VT_detailViewNavigation($smarty,$recordNavigationInfo,$focus->id);
}

// Record Change Notification
$focus->markAsViewed($current_user->id);
// END

// Gather the custom link information to display
include_once('vtlib/Vtiger/Link.php');
$customlink_params = Array('MODULE'=>$currentModule, 'RECORD'=>$focus->id, 'ACTION'=>vtlib_purify($_REQUEST['action']));
$smarty->assign('CUSTOM_LINKS', Vtiger_Link::getAllByType(getTabid($currentModule), Array('DETAILVIEWBASIC','DETAILVIEW','DETAILVIEWWIDGET'), $customlink_params));
// END

$smarty->assign('DETAILVIEW_AJAX_EDIT', PerformancePrefs::getBoolean('DETAILVIEW_AJAX_EDIT', true));

$smarty->display("DetailView.tpl");

?>
