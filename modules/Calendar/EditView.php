<?php
/*********************************************************************************
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
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Activities/EditView.php,v 1.11 2005/03/24 16:18:38 samk Exp $
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/CustomFieldUtil.php');
require_once('include/utils/utils.php');
require_once('include/FormValidationUtil.php');
require_once('modules/Calendar/calendarLayout.php');
require_once("modules/Emails/mail.php");
include_once 'modules/Calendar/header.php';
global $app_strings;
global $mod_strings,$current_user;
// Unimplemented until jscalendar language vtiger_files are fixed

$focus = CRMEntity::getInstance($currentModule);
$smarty =  new vtigerCRM_Smarty();
//added to fix the issue4600
$searchurl = getBasic_Advance_SearchURL();
$smarty->assign("SEARCH", $searchurl);
//4600 ends

$activity_mode = vtlib_purify($_REQUEST['activity_mode']);
if($activity_mode == 'Task')
{
	$tab_type = 'Calendar';
	$taskcheck = true;
	$smarty->assign("SINGLE_MOD",$mod_strings['LBL_TODO']);
}
elseif($activity_mode == 'Events')
{
	$tab_type = 'Events';
	$taskcheck = false;
	$smarty->assign("SINGLE_MOD",$mod_strings['LBL_EVENT']);
}

if(isset($_REQUEST['record']) && $_REQUEST['record']!='') {
    $focus->id = vtlib_purify($_REQUEST['record']);
    $focus->mode = 'edit';
    $focus->retrieve_entity_info($_REQUEST['record'],$tab_type);
    $focus->name=$focus->column_fields['subject'];
    $sql = 'select vtiger_users.*,vtiger_invitees.* from vtiger_invitees left join vtiger_users on vtiger_invitees.inviteeid=vtiger_users.id where activityid=?';
    $result = $adb->pquery($sql, array($focus->id));
    $num_rows=$adb->num_rows($result);
    $invited_users=Array();
    for($i=0;$i<$num_rows;$i++)
    {
	    $userid=$adb->query_result($result,$i,'inviteeid');
	    $username = getFullNameFromQResult($result, $i, 'Users');
	    $invited_users[$userid]=$username;
    }
    $smarty->assign("INVITEDUSERS",$invited_users);
    $smarty->assign("UPDATEINFO",updateInfo($focus->id));
    $related_array = getRelatedListsInformation("Calendar", $focus);
    $cntlist = $related_array['Contacts']['entries'];

	$entityIds = array_keys($cntlist);
	$cnt_namelist = array();
	$displayValueArray = getEntityName('Contacts', $entityIds);
	if (!empty($displayValueArray)) {
		foreach ($displayValueArray as $key => $field_value) {
			$cnt_namelist[$key] =  $field_value;
		}
	}
	$cnt_idlist = array_keys($cnt_namelist);
    $smarty->assign("CONTACTSID",  implode(';', $cnt_idlist));
    $smarty->assign("CONTACTSNAME",$cnt_namelist);
    $query = 'SELECT vtiger_recurringevents.*, vtiger_activity.date_start, vtiger_activity.time_start, vtiger_activity.due_date, vtiger_activity.time_end
				FROM vtiger_recurringevents
					INNER JOIN vtiger_activity ON vtiger_activity.activityid = vtiger_recurringevents.activityid
					WHERE vtiger_recurringevents.activityid = ?';
	$res = $adb->pquery($query, array($focus->id));
    $rows = $adb->num_rows($res);
    if($rows > 0) {
		$recurringObject = RecurringType::fromDBRequest($adb->query_result_rowdata($res, 0));

	    $value['recurringcheck'] = 'Yes';
	    $value['repeat_frequency'] = $recurringObject->getRecurringFrequency();
		$value['eventrecurringtype'] = $recurringObject->getRecurringType();
		$recurringInfo = $recurringObject->getUserRecurringInfo();

		if($recurringObject->getRecurringType() == 'Weekly') {
			$noOfDays = count($recurringInfo['dayofweek_to_repeat']);
			for ($i = 0; $i < $noOfDays; ++$i) {
				$value['week'.$recurringInfo['dayofweek_to_repeat'][$i]] = 'checked';
			}

		   } elseif ($recurringObject->getRecurringType() == 'Monthly') {
			$value['repeatMonth'] = $recurringInfo['repeatmonth_type'];
			if ($recurringInfo['repeatmonth_type'] == 'date') {
				$value['repeatMonth_date'] = $recurringInfo['repeatmonth_date'];
			} else {
				$value['repeatMonth_daytype'] = $recurringInfo['repeatmonth_daytype'];
				$value['repeatMonth_day'] = $recurringInfo['dayofweek_to_repeat'][0];
			}
		}
    } else {
	    $value['recurringcheck'] = 'No';
    }

}else
{
	if(isset($_REQUEST['contact_id']) && $_REQUEST['contact_id']!=''){
		$contactId = vtlib_purify($_REQUEST['contact_id']);
		$entityIds = array($contactId);
		$displayValueArray = getEntityName('Contacts', $entityIds);
		if (!empty($displayValueArray)) {
			foreach ($displayValueArray as $key => $field_value) {
				$cnt_namelist[$key] =  $field_value;
			}
		}
		$cnt_idlist = array_keys($cnt_namelist);
		$smarty->assign("CONTACTSID",  implode(';', $cnt_idlist));
		$smarty->assign("CONTACTSNAME",$cnt_namelist);
		
		$account_id = vtlib_purify($_REQUEST['account_id']);
		$account_name = getAccountName($account_id);
	}
}
if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = "";
    	$focus->mode = '';
}
if(empty($_REQUEST['record']) && $focus->mode != 'edit'){
	setObjectValuesFromRequest($focus);
}
$userDetails=getOtherUserName($current_user->id);
$to_email = getUserEmailId('id',$current_user->id);
$smarty->assign("CURRENTUSERID",$current_user->id);

$disp_view = getView($focus->mode);
if($disp_view == 'edit_view')
{
	$act_data = getBlocks($tab_type,$disp_view,$mode,$focus->column_fields);
}
else
{
	$act_data = getBlocks($tab_type,$disp_view,$mode,$focus->column_fields,'BAS');
}
$smarty->assign("BLOCKS",$act_data);
foreach($act_data as $header=>$blockitem)
{
	foreach($blockitem as $row=>$data)
	{
		foreach($data as $key=>$maindata)
		{
			$uitype[$maindata[2][0]] = $maindata[0][0];
			$fldlabel[$maindata[2][0]] = $maindata[1][0];
			$fldlabel_sel[$maindata[2][0]] = $maindata[1][1];
			$fldlabel_combo[$maindata[2][0]] = $maindata[1][2];
			$value[$maindata[2][0]] = $maindata[3][0];
			$secondvalue[$maindata[2][0]] = $maindata[3][1];
			$thirdvalue[$maindata[2][0]] = $maindata[3][2];
		}
	}
}
// jread.topik. patch account_id for create contact
if (strlen($account_name) > 0)
{
	$fldlabel_sel['parent_id'][1]='selected';
	$secondvalue['parent_id'] = $account_id;
	$value['parent_id'] = $account_name;
}

$format = ($current_user->hour_format == '')?'am/pm':$current_user->hour_format;
$stdate = key($value['date_start']);
$enddate = key($value['due_date']);
$sttime = $value['date_start'][$stdate];
$endtime = $value['due_date'][$enddate];
$time_arr = getaddEventPopupTime($sttime,$endtime,$format);
$value['starthr'] = $time_arr['starthour'];
$value['startmin'] = $time_arr['startmin'];
$value['startfmt'] = $time_arr['startfmt'];
$value['endhr'] = $time_arr['endhour'];
$value['endmin'] = $time_arr['endmin'];
$value['endfmt'] = $time_arr['endfmt'];
$smarty->assign("STARTHOUR",getTimeCombo($format,'start',$time_arr['starthour'],$time_arr['startmin'],$time_arr['startfmt'],$taskcheck));
$smarty->assign("ENDHOUR",getTimeCombo($format,'end',$time_arr['endhour'],$time_arr['endmin'],$time_arr['endfmt']));
$smarty->assign("FOLLOWUP",getTimeCombo($format,'followup_start',$time_arr['endhour'],$time_arr['endmin'],$time_arr['endfmt']));
$smarty->assign("ACTIVITYDATA",$value);
$smarty->assign("LABEL",$fldlabel);
$smarty->assign("secondvalue",$secondvalue);
$smarty->assign("thirdvalue",$thirdvalue);
$smarty->assign("fldlabel_combo",$fldlabel_combo);
$smarty->assign("fldlabel_sel",$fldlabel_sel);
$smarty->assign("OP_MODE",$disp_view);
$smarty->assign("ACTIVITY_MODE",$activity_mode);
$smarty->assign("HOURFORMAT",$format);
$smarty->assign("USERSLIST",$userDetails);
$smarty->assign("USEREMAILID",$to_email);
$smarty->assign("MODULE",$currentModule);
$smarty->assign("DATEFORMAT",parse_calendardate($app_strings['NTC_DATE_FORMAT']));

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$log->info("Activity edit view");

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);

if (isset($focus->name))
$smarty->assign("NAME", $focus->name);
else
$smarty->assign("NAME", "");

if($focus->mode == 'edit')
{
        $smarty->assign("MODE", $focus->mode);
}
$smarty->assign('CREATEMODE', vtlib_purify($_REQUEST['createmode']));

$category = getParentTab();
$smarty->assign("CATEGORY",$category);

// Unimplemented until jscalendar language vtiger_files are fixed
$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);
$smarty->assign("CALENDAR_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));

$isContactIdEditable = getFieldVisibilityPermission($tab_type, $current_user->id, 'contact_id', 'readwrite');
$smarty->assign("IS_CONTACTS_EDIT_PERMITTED", (($isContactIdEditable == '0')? true : false));

if (isset($_REQUEST['return_module']))
	$smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
if (isset($_REQUEST['return_action']))
	$smarty->assign("RETURN_ACTION",vtlib_purify( $_REQUEST['return_action']));
if (isset($_REQUEST['return_id']))
	$smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));
if (isset($_REQUEST['ticket_id']))
	$smarty->assign("TICKETID", vtlib_purify($_REQUEST['ticket_id']));
if (isset($_REQUEST['product_id']))
	$smarty->assign("PRODUCTID", vtlib_purify($_REQUEST['product_id']));
if (isset($_REQUEST['return_viewname']))
	$smarty->assign("RETURN_VIEWNAME", vtlib_purify($_REQUEST['return_viewname']));
if(isset($_REQUEST['view']) && $_REQUEST['view']!='')
	$smarty->assign("view",vtlib_purify($_REQUEST['view']));
if(isset($_REQUEST['hour']) && $_REQUEST['hour']!='')
	$smarty->assign("hour",vtlib_purify($_REQUEST['hour']));
if(isset($_REQUEST['day']) && $_REQUEST['day']!='')
	$smarty->assign("day",vtlib_purify($_REQUEST['day']));
if(isset($_REQUEST['month']) && $_REQUEST['month']!='')
	$smarty->assign("month",vtlib_purify($_REQUEST['month']));
if(isset($_REQUEST['year']) && $_REQUEST['year']!='')
	$smarty->assign("year",vtlib_purify($_REQUEST['year']));
if(isset($_REQUEST['viewOption']) && $_REQUEST['viewOption']!='')
	$smarty->assign("viewOption",vtlib_purify($_REQUEST['viewOption']));
if(isset($_REQUEST['subtab']) && $_REQUEST['subtab']!='')
	$smarty->assign("subtab",vtlib_purify($_REQUEST['subtab']));
if(isset($_REQUEST['maintab']) && $_REQUEST['maintab']!='')
	$smarty->assign("maintab",vtlib_purify($_REQUEST['maintab']));


$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
$smarty->assign("ID", $focus->id);

 $tabid = getTabid($tab_type);
 $validationData = getDBValidationData($focus->tab_name,$tabid);
 $data = split_validationdataArray($validationData);

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);
$smarty->assign("DUPLICATE",vtlib_purify($_REQUEST['isDuplicate']));

if ($activity_mode == 'Task') {
	$custom_fields_data = getCalendarCustomFields(getTabid('Calendar'),'edit',$focus->column_fields);
} else {
	$custom_fields_data = getCalendarCustomFields(getTabid('Events'),'edit',$focus->column_fields);
}
$smarty->assign("CUSTOM_FIELDS_DATA", $custom_fields_data);

$smarty->assign("REPEAT_LIMIT_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));

$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($tab_type);
$smarty->assign("PICKIST_DEPENDENCY_DATASOURCE", Zend_Json::encode($picklistDependencyDatasource));

// Gather the help information associated with fields
$smarty->assign('FIELDHELPINFO', vtlib_getFieldHelpInfo($currentModule));
// END

$smarty->display("ActivityEditView.tpl");

?>