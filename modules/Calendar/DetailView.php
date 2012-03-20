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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Activities/DetailView.php,v 1.12 2005/03/17 11:26:49 ray Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/CustomFieldUtil.php');
require_once('include/utils/utils.php');
require_once('modules/Calendar/calendarLayout.php');
include_once 'modules/Calendar/header.php';
require_once 'modules/CustomView/CustomView.php';

global $mod_strings, $currentModule,$adb, $current_user;
if( $_SESSION['mail_send_error']!="")
{
	echo '<b><font color=red>'. $mod_strings{"LBL_NOTIFICATION_ERROR"}.'</font></b><br>';
}
session_unregister('mail_send_error');

$focus = CRMEntity::getInstance($currentModule);
$smarty =  new vtigerCRM_Smarty();
$activity_mode = vtlib_purify($_REQUEST['activity_mode']);
//If activity_mode == null

if($activity_mode =='' || strlen($activity_mode) < 1)
{
	$query = "select activitytype from vtiger_activity where activityid=?";
	$result = $adb->pquery($query, array($_REQUEST['record']));
	$actType = $adb->query_result($result,0,'activitytype');
	if( $actType == 'Task')
	{
		$activity_mode = $actType;	
	}
	elseif($actType != 'Emails')
	{
		$activity_mode = 'Events';
	}		
}	

if($activity_mode == 'Task')
{
        $tab_type = 'Calendar';
	$smarty->assign("SINGLE_MOD",$mod_strings['LBL_TODO']);
}
elseif($activity_mode == 'Events')
{
        $tab_type = 'Events';
	$smarty->assign("SINGLE_MOD",$mod_strings['LBL_EVENT']);
}
$tab_id=getTabid($tab_type);


if(isset($_REQUEST['record']) && isset($_REQUEST['record'])) {
    $focus->retrieve_entity_info($_REQUEST['record'],$tab_type);
    $focus->id = $_REQUEST['record'];	
    $focus->name=$focus->column_fields['subject'];
}

if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = "";
} 

//needed when creating a new task with default values passed in
if (isset($_REQUEST['contactname']) && is_null($focus->contactname)) {
	$focus->contactname = $_REQUEST['contactname'];
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
if (isset($_REQUEST['accountname']) && is_null($focus->parent_name)) {
	$focus->parent_name = $_REQUEST['accountname'];
}
if (isset($_REQUEST['accountid']) && is_null($focus->parent_id)) {
	$focus->parent_id = $_REQUEST['accountid'];
}

$act_data = getBlocks($tab_type,"detail_view",'',$focus->column_fields);

foreach($act_data as $block=>$entry)
{
	foreach($entry as $key=>$value)
	{
		foreach($value as $label=>$field)
		{
			$fldlabel[$field['fldname']] = $label;
			if($field['ui'] == 15 || $field['ui'] == 16)
			{
				foreach($field['options'] as $index=>$arr_val)
				{
					if($arr_val[2] == "selected")
					$finaldata[$field['fldname']] = $arr_val[0];
				}
			}
			else
			{
				$fldvalue = $field['value'];
				if($field['fldname'] == 'description') { $fldvalue = nl2br($fldvalue); }
				$finaldata[$field['fldname']] = $fldvalue;
			}	
			
			$finaldata[$field['fldname'].'link'] = $field['link'];
		}
	}
}

//Start
//To set user selected hour format
if($current_user->hour_format == '')
	$format = 'am/pm';
else
	$format = $current_user->hour_format;
list($stdate,$sttime) = split(' ',$finaldata['date_start']);
list($enddate,$endtime) = split(' ',$finaldata['due_date']);
$time_arr = getaddEventPopupTime($sttime,$endtime,$format);
$data['starthr'] = $time_arr['starthour'];
$data['startmin'] = $time_arr['startmin'];
$data['startfmt'] = $time_arr['startfmt'];
$data['endhr'] = $time_arr['endhour'];
$data['endmin'] = $time_arr['endmin'];
$data['endfmt'] = $time_arr['endfmt'];
$data['record'] = $focus->id;
if(isset($finaldata['sendnotification']) && $finaldata['sendnotification'] == 'yes')
        $data['sendnotification'] = $mod_strings['LBL_YES'];
else
        $data['sendnotification'] = $mod_strings['LBL_NO'];
$data['subject'] = $finaldata['subject'];
$data['date_start'] = $stdate;
$data['due_date'] = $enddate;
$data['assigned_user_id'] = $finaldata['assigned_user_id'];
if($mod_strings[$finaldata['taskpriority']] != '')
	$data['taskpriority'] = $mod_strings[$finaldata['taskpriority']];
else
	$data['taskpriority'] = $finaldata['taskpriority'];
$data['modifiedtime'] = $finaldata['modifiedtime'];
$data['createdtime'] = $finaldata['createdtime'];
$data['parent_name'] = $finaldata['parent_id'];
$data['description'] = $finaldata['description'];
if($activity_mode == 'Task')
{
	if($mod_strings[$finaldata['taskstatus']] != '')
		$data['taskstatus'] = $mod_strings[$finaldata['taskstatus']];
	else
		$data['taskstatus'] = $finaldata['taskstatus'];
	$data['activitytype'] = $activity_mode;
	$data['contact_id'] = $finaldata['contact_id'];
	$data['contact_idlink'] = $finaldata['contact_idlink'];
}
elseif($activity_mode == 'Events')
{
	$data['visibility'] = $finaldata['visibility'];
	if($mod_strings[$finaldata['eventstatus']] != '')
		$data['eventstatus'] = $mod_strings[$finaldata['eventstatus']];
	else
		$data['eventstatus'] = $finaldata['eventstatus'];
	$data['activitytype'] = $finaldata['activitytype'];
	$data['location'] = $finaldata['location'];
	//Calculating reminder time
	$rem_days = 0;
	$rem_hrs = 0;
	$rem_min = 0;
	if(!empty($focus->column_fields['reminder_time']))
	{
		$data['set_reminder'] = $mod_strings['LBL_YES'];
		$data['reminder_str'] = $finaldata['reminder_time'];
	}
	else
		$data['set_reminder'] = $mod_strings['LBL_NO'];
	//To set recurring details
	$query = 'select vtiger_recurringevents.recurringfreq,vtiger_recurringevents.recurringinfo from vtiger_recurringevents where vtiger_recurringevents.activityid = ?';
	$res = $adb->pquery($query, array($focus->id));
	$rows = $adb->num_rows($res);
	if($rows != 0)
	{
		$data['recurringcheck'] = $mod_strings['LBL_YES'];
		$data['repeat_frequency'] = $adb->query_result($res,0,'recurringfreq');
		$recurringinfo =  explode("::",$adb->query_result($res,0,'recurringinfo'));
		$data['recurringtype'] = $recurringinfo[0];
		if($recurringinfo[0] == 'Weekly')
		{
			$weekrpt_str = '';
			if(count($recurringinfo) > 1)
			{
				$weekrpt_str .= 'on ';
				for($i=1;$i<count($recurringinfo);$i++)
				{
					$label = 'LBL_DAY'.$recurringinfo[$i];
					if($i != 1)
						$weekrpt_str .= ', ';
					$weekrpt_str .= $mod_strings[$label];
				}
			}
			$data['repeat_str'] = $weekrpt_str;
		}
		elseif($recurringinfo[0] == 'Monthly')
		{   
			$monthrpt_str = '';
			$data['repeatMonth'] = $recurringinfo[1];  
			if($recurringinfo[1] == 'date')
			{
				$data['repeatMonth_date'] = $recurringinfo[2];
				$monthrpt_str .= $mod_strings['on'].'&nbsp;'.$recurringinfo[2].'&nbsp;'.$mod_strings['day of the month'];
			}
			else 
			{ 
				$data['repeatMonth_daytype'] = $recurringinfo[2];
				$data['repeatMonth_day'] = $recurringinfo[3];
				switch($data['repeatMonth_day'])
				{
					case 0 :
						$day = $mod_strings['LBL_DAY0'];
						break;
					case 1 :
						$day = $mod_strings['LBL_DAY1'];
						break;
					case 2 :
						$day = $mod_strings['LBL_DAY2'];
						break;
					case 3 :
						$day = $mod_strings['LBL_DAY3'];
						break;
					case 4 :
						$day = $mod_strings['LBL_DAY4'];
						break;
					case 5 :
						$day = $mod_strings['LBL_DAY5'];
						break;
					case 6 :
						$day = $mod_strings['LBL_DAY6'];
						break;
				}

				$monthrpt_str .= 'on '.$mod_strings[$recurringinfo[2]].' '.$day;
			}
			$data['repeat_str'] = $monthrpt_str;
		}
	}
	else 
	{   
		$data['recurringcheck'] = $mod_strings['LBL_NO'];
		$data['repeat_month_str'] = '';
	}
	$sql = 'select vtiger_users.user_name,vtiger_invitees.* from vtiger_invitees left join vtiger_users on vtiger_invitees.inviteeid=vtiger_users.id where activityid=?';
	$result = $adb->pquery($sql, array($focus->id));
	$num_rows=$adb->num_rows($result);
	$invited_users=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$userid=$adb->query_result($result,$i,'inviteeid');
		$username=$adb->query_result($result,$i,'user_name');
		$invited_users[$userid]=$username;
	}
	$smarty->assign("INVITEDUSERS",$invited_users);
	$related_array = getRelatedListsInformation("Calendar", $focus);
	
	$smarty->assign("CONTACTS",$related_array['Contacts']['entries']);
	
	$is_fname_permitted = getFieldVisibilityPermission("Contacts", $current_user->id, 'firstname');
	$smarty->assign("IS_PERMITTED_CNT_FNAME",$is_fname_permitted);


}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$log->info("Calendar-Activities detail view");
$category = getParentTab();
$smarty->assign("CATEGORY",$category);

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("ACTIVITY_MODE", $activity_mode);

if (isset($focus->name)) 
$smarty->assign("NAME", $focus->name);
else 
$smarty->assign("NAME", "");
$smarty->assign("UPDATEINFO",updateInfo($focus->id));
if (isset($_REQUEST['return_module'])) 
$smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
if (isset($_REQUEST['return_action'])) 
$smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));
if (isset($_REQUEST['return_id'])) 
$smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string'].'&activity_mode='.$activity_mode);
$smarty->assign("ID", $focus->id);
$smarty->assign("NAME", $focus->name);
$smarty->assign("BLOCKS", $act_data);
$smarty->assign("LABEL", $fldlabel);
$smarty->assign("VIEWTYPE", vtlib_purify($_REQUEST['viewtype']));
$smarty->assign("CUSTOMFIELD", $cust_fld);
$smarty->assign("ACTIVITYDATA", $data);
$smarty->assign("ID", vtlib_purify($_REQUEST['record']));

//get Description Information
if(isPermitted("Calendar","EditView",$_REQUEST['record']) == 'yes')
	$smarty->assign("EDIT_DUPLICATE","permitted");

if(isPermitted("Calendar","Delete",$_REQUEST['record']) == 'yes')
	$smarty->assign("DELETE","permitted");
  
$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

 $tabid = getTabid($tab_type);
 $validationData = getDBValidationData($focus->tab_name,$tabid);
 $data2 = split_validationdataArray($validationData);

 $smarty->assign("VALIDATION_DATA_FIELDNAME",$data2['fieldname']);
 $smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$data2['datatype']);
 $smarty->assign("VALIDATION_DATA_FIELDLABEL",$data2['fieldlabel']);

$smarty->assign("MODULE",$currentModule);
$smarty->assign("EDIT_PERMISSION",isPermitted($currentModule,'EditView',$_REQUEST['record']));

if(PerformancePrefs::getBoolean('DETAILVIEW_RECORD_NAVIGATION', true) && isset($_SESSION[$currentModule.'_listquery'])){
	$recordNavigationInfo = ListViewSession::getListViewNavigation($focus->id);
	VT_detailViewNavigation($smarty,$recordNavigationInfo,$focus->id);
}

// Gather the custom link information to display
include_once('vtlib/Vtiger/Link.php');
$customlink_params = Array('MODULE'=>$currentModule, 'RECORD'=>$focus->id, 'ACTION'=>vtlib_purify($_REQUEST['action']));
$smarty->assign('CUSTOM_LINKS', Vtiger_Link::getAllByType(getTabid($currentModule), Array('DETAILVIEWBASIC','DETAILVIEW','DETAILVIEWWIDGET'), $customlink_params));
// END

$custom_fields_data = getCalendarCustomFields($tabid,'detail_view',$focus->column_fields);
$smarty->assign("CUSTOM_FIELDS_DATA", $custom_fields_data);

$smarty->display("ActivityDetailView.tpl");

?>