<?php
/*+*******************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 * ****************************************************************************** */
/* * *******************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Contacts/EditView.php,v 1.21 2005/03/24 16:18:38 samk Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 * ****************************************************************************** */

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/CustomFieldUtil.php');
require_once('include/utils/utils.php');

global $log, $mod_strings, $app_strings, $theme, $currentModule, $current_user;

//added for contact image
$encode_val = vtlib_purify($_REQUEST['encode_val']);
$decode_val = base64_decode($encode_val);

$saveimage = isset($_REQUEST['saveimage']) ? vtlib_purify($_REQUEST['saveimage']) : "false";
$errormessage = isset($_REQUEST['error_msg']) ? vtlib_purify($_REQUEST['error_msg']) : "false";
$image_error = isset($_REQUEST['image_error']) ? vtlib_purify($_REQUEST['image_error']) : "false";
//end

$focus = CRMEntity::getInstance($currentModule);
$smarty = new vtigerCRM_Smarty;

//added to fix the issue4600
$searchurl = getBasic_Advance_SearchURL();
$smarty->assign("SEARCH", $searchurl);
//4600 ends

if (isset($_REQUEST['record']) && $_REQUEST['record'] != '') {
    $focus->id = $_REQUEST['record'];
    $focus->mode = 'edit';
	$focus->retrieve_entity_info($_REQUEST['record'], "Contacts");
    $log->info("Entity info successfully retrieved for EditView.");
	$focus->firstname = $focus->column_fields['firstname'];
	$focus->lastname = $focus->column_fields['lastname'];
}

if ($image_error == "true") {
	$explode_decode_val = explode("&", $decode_val);
	for ($i = 1; $i < count($explode_decode_val); $i++) {
		$test = $explode_decode_val[$i];
		$values = explode("=", $test);
		$field_name_val = $values[0];
		$field_value = $values[1];
		$focus->column_fields[$field_name_val] = $field_value;
        }
}

if (isset($_REQUEST['account_id']) && $_REQUEST['account_id'] != '' && $_REQUEST['record'] == '') {
        require_once('modules/Accounts/Accounts.php');
        $focus->column_fields['account_id'] = $_REQUEST['account_id'];
        $acct_focus = new Accounts();
	$acct_focus->retrieve_entity_info($_REQUEST['account_id'], "Accounts");
	$focus->column_fields['fax'] = $acct_focus->column_fields['fax'];
	$focus->column_fields['otherphone'] = $acct_focus->column_fields['phone'];
	$focus->column_fields['mailingcity'] = $acct_focus->column_fields['bill_city'];
	$focus->column_fields['othercity'] = $acct_focus->column_fields['ship_city'];
	$focus->column_fields['mailingstreet'] = $acct_focus->column_fields['bill_street'];
	$focus->column_fields['otherstreet'] = $acct_focus->column_fields['ship_street'];
	$focus->column_fields['mailingstate'] = $acct_focus->column_fields['bill_state'];
	$focus->column_fields['otherstate'] = $acct_focus->column_fields['ship_state'];
	$focus->column_fields['mailingzip'] = $acct_focus->column_fields['bill_code'];
	$focus->column_fields['otherzip'] = $acct_focus->column_fields['ship_code'];
	$focus->column_fields['mailingcountry'] = $acct_focus->column_fields['bill_country'];
	$focus->column_fields['othercountry'] = $acct_focus->column_fields['ship_country'];
	$focus->column_fields['mailingpobox'] = $acct_focus->column_fields['bill_pobox'];
	$focus->column_fields['otherpobox'] = $acct_focus->column_fields['ship_pobox'];

	$log->debug("Accountid Id from the request is " . $_REQUEST['account_id']);
}
if (isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
        $focus->id = "";
        $focus->mode = "";
}
if (empty($_REQUEST['record']) && $focus->mode != 'edit') {
        setObjectValuesFromRequest($focus);
}
$disp_view = getView($focus->mode);

$smarty->assign("BLOCKS", getBlocks($currentModule, $disp_view, $mode, $focus->column_fields));

$smarty->assign("OP_MODE", $disp_view);

//needed when creating a new contact with a default vtiger_account value passed in
if (isset($_REQUEST['account_name']) && is_null($focus->account_name)) {
      $focus->account_name = $_REQUEST['account_name'];


}
if (isset($_REQUEST['account_id']) && is_null($focus->account_id)) {
      $focus->account_id = $_REQUEST['account_id'];
}

$theme_path = "themes/" . $theme . "/";
$image_path = $theme_path . "images/";

$log->info("Contact detail view");

$smarty->assign("MOD", $mod_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("APP", $app_strings);
$contact_name = $focus->lastname;
if (getFieldVisibilityPermission($currentModule, $current_user->id, 'firstname') == '0') {
	$contact_name .= ' ' . $focus->firstname;
}
$smarty->assign("NAME", $contact_name);
if (isset($cust_fld)) {
        $smarty->assign("CUSTOMFIELD", $cust_fld);
}
$smarty->assign("ID", $focus->id);
$smarty->assign("MODULE", $currentModule);
$smarty->assign("SINGLE_MOD", 'Contact');

if ($focus->mode == 'edit') {
	$smarty->assign("UPDATEINFO", updateInfo($focus->id));
	$smarty->assign("MODE", $focus->mode);
}
$smarty->assign('CREATEMODE', vtlib_purify($_REQUEST['createmode']));

if (isset($_REQUEST['activity_mode']) && $_REQUEST['activity_mode'] != '')
	$smarty->assign("ACTIVITYMODE", vtlib_purify($_REQUEST['activity_mode']));

// Unimplemented until jscalendar language vtiger_files are fixed
$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);
$smarty->assign("CALENDAR_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));

if (isset($_REQUEST['campaignid']))
	$smarty->assign("campaignid", vtlib_purify($_REQUEST['campaignid']));
if (isset($_REQUEST['return_module']))
 	$smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
if (isset($_REQUEST['return_action']))
 	$smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));
if (isset($_REQUEST['return_id']))
 	$smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));
if (isset($_REQUEST['return_viewname']))
 	$smarty->assign("RETURN_VIEWNAME", vtlib_purify($_REQUEST['return_viewname']));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=" . session_id() . $GLOBALS['request_string']);

$tabid = getTabid("Contacts");
$validationData = getDBValidationData($focus->tab_name, $tabid);
$data = split_validationdataArray($validationData);

$smarty->assign("VALIDATION_DATA_FIELDNAME", $data['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE", $data['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL", $data['fieldlabel']);
$category = getParentTab();
$smarty->assign("CATEGORY", $category);

if ($errormessage == 2) {
	$msg = $mod_strings['LBL_MAXIMUM_LIMIT_ERROR'];
	$errormessage = "<B><font color='red'>" . $msg . "</font></B> <br><br>";
} else if ($errormessage == 3) {
 	        $msg = $mod_strings['LBL_UPLOAD_ERROR'];
	$errormessage = "<B><font color='red'>" . $msg . "</font></B> <br><br>";
} else if ($errormessage == "image") {
	 	        $msg = $mod_strings['LBL_IMAGE_ERROR'];
	$errormessage = "<B><font color='red'>" . $msg . "</font></B> <br><br>";
} else if ($errormessage == "invalid") {
	 	        $msg = $mod_strings['LBL_INVALID_IMAGE'];
	$errormessage = "<B><font color='red'>" . $msg . "</font></B> <br><br>";
} else {
	$errormessage = "";
}
if ($errormessage != "") {
	$smarty->assign("ERROR_MESSAGE", $errormessage);
}

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);
$smarty->assign("DUPLICATE", vtlib_purify($_REQUEST['isDuplicate']));

global $adb;
// Module Sequence Numbering
$mod_seq_field = getModuleSequenceField($currentModule);
if ($focus->mode != 'edit' && $mod_seq_field != null) {
	 	                $autostr = getTranslatedString('MSG_AUTO_GEN_ON_SAVE');
	$mod_seq_string = $adb->pquery("SELECT prefix, cur_id from vtiger_modentity_num where semodule = ? and active=1", array($currentModule));
	$mod_seq_prefix = $adb->query_result($mod_seq_string, 0, 'prefix');
	$mod_seq_no = $adb->query_result($mod_seq_string, 0, 'cur_id');
	if ($adb->num_rows($mod_seq_string) == 0 || $focus->checkModuleSeqNumber($focus->table_name, $mod_seq_field['column'], $mod_seq_prefix . $mod_seq_no))
		echo '<br><font color="#FF0000"><b>' . getTranslatedString('LBL_DUPLICATE') . ' ' . getTranslatedString($mod_seq_field['label'])
		. ' - ' . getTranslatedString('LBL_CLICK') . ' <a href="index.php?module=Settings&action=CustomModEntityNo&parenttab=Settings&selmodule=' . $currentModule . '">' . getTranslatedString('LBL_HERE') . '</a> '
		. getTranslatedString('LBL_TO_CONFIGURE') . ' ' . getTranslatedString($mod_seq_field['label']) . '</b></font>';
	 	        else
		$smarty->assign("MOD_SEQ_ID", $autostr);
} else {
	 	        $smarty->assign("MOD_SEQ_ID", $focus->column_fields[$mod_seq_field['name']]);
}
// END
// Gather the help information associated with fields
$smarty->assign('FIELDHELPINFO', vtlib_getFieldHelpInfo($currentModule));
// END

$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($currentModule);
$smarty->assign("PICKIST_DEPENDENCY_DATASOURCE", Zend_Json::encode($picklistDependencyDatasource));

$smarty->display("salesEditView.tpl");

?>