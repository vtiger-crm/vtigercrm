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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Activities/OpenListView.php,v 1.22 2005/04/19 17:00:30 ray Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

/**
 * Function to get Pending/Upcoming activities
 * @param integer  $mode     - number to differentiate upcoming and pending activities
 * return array    $values   - activities record in array format
 */
function getPendingActivities($mode,$view=''){
	global $log;
	$log->debug("Entering getPendingActivities() method ...");
	require_once('data/Tracker.php');
	require_once('include/utils/utils.php');
	require_once('user_privileges/default_module_view.php');
	
	global $currentModule;
	global $singlepane_view;
	global $theme;
	global $focus;
	global $action;
	global $adb;
	global $app_strings;
	global $current_language;
	global $current_user;
	$current_module_strings = return_module_language($current_language, 'Calendar');

	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	
	if($_REQUEST['activity_view']==''){
		$activity_view='today';
	}else{
		$activity_view=vtlib_purify($_REQUEST['activity_view']);
	}

	$today = date("Y-m-d", time());
	if($view == 'today'){
		$upcoming_condition = " AND (date_start = '$today' OR vtiger_recurringevents.recurringdate = '$today')";
		$pending_condition = " AND (due_date = '$today' OR vtiger_recurringevents.recurringdate = '$today')";
	}else if($view == 'all'){
		$upcoming_condition = " AND (date_start >= '$today' OR vtiger_recurringevents.recurringdate >= '$today')";
		$pending_condition = " AND (due_date <= '$today' OR vtiger_recurringevents.recurringdate <= '$today')";
	}
	
	if($mode != 1){
		$list_query = " select vtiger_crmentity.crmid,vtiger_crmentity.smownerid,vtiger_crmentity.".
		"setype, vtiger_recurringevents.recurringdate, vtiger_activity.activityid, ".
		"vtiger_activity.activitytype, vtiger_activity.date_start, vtiger_activity.due_date,".
		"from vtiger_activity inner join vtiger_crmentity on vtiger_crmentity.crmid=".
		"vtiger_activity.activityid LEFT JOIN vtiger_groups ON vtiger_groups.groupid = ".
		"vtiger_crmentity.smownerid left outer join vtiger_recurringevents on ".
		"vtiger_recurringevents.activityid=vtiger_activity.activityid";
		$list_query .= getNonAdminAccessControlQuery('Calendar',$current_user);
		$list_query .= " WHERE vtiger_crmentity.deleted=0 and vtiger_activity.activitytype not in ".
		"('Emails') AND ( vtiger_activity.status is NULL OR vtiger_activity.status not in".
		"('Completed','Deferred')) and ( vtiger_activity.eventstatus is NULL OR vtiger_activity.".
		"eventstatus not in ('Held','Not Held') )".$upcoming_condition;
		
	}else{
		$list_query = "select vtiger_crmentity.crmid,vtiger_crmentity.smownerid,vtiger_crmentity".
		"setype, vtiger_recurringevents.recurringdate, vtiger_activity.activityid, vtiger_activity".
		".activitytype, vtiger_activity.date_start, vtiger_activity.due_date, from vtiger_activity".
		"inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid ".
		"LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid ".
		"left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=".
		"vtiger_activity.activityid";
		$list_query .= getNonAdminAccessControlQuery('Calendar',$current_user);
		$list_query .= "WHERE vtiger_crmentity.deleted=0 and (vtiger_activity.".
		"activitytype not in ('Emails')) AND (vtiger_activity.status is NULL OR vtiger_activity.".
		"status not in ('Completed','Deferred')) and (vtiger_activity.eventstatus is NULL OR ".
		"vtiger_activity.eventstatus not in ('Held','Not Held')) ".$pending_condition;
	
		$list_query.= " GROUP BY vtiger_activity.activityid";
		$list_query.= " ORDER BY date_start,time_start ASC";
		$res = $adb->query($list_query);
		$noofrecords = $adb->num_rows($res);
		$open_activity_list = array();
		$noofrows = $adb->num_rows($res);
		if (count($res)>0){
			for($i=0;$i<$noofrows;$i++){
				$open_activity_list[] = Array('name' => $adb->query_result($res,$i,'subject'),
						'id' => $adb->query_result($res,$i,'activityid'),
						'type' => $adb->query_result($res,$i,'activitytype'),
						'module' => $adb->query_result($res,$i,'setype'),
						'date_start' => getDisplayDate($adb->query_result($res,$i,'date_start')),
						'due_date' => getDisplayDate($adb->query_result($res,$i,'due_date')),
						'recurringdate' => getDisplayDate($adb->query_result($res,$i,'recurringdate')),
						'priority' => $adb->query_result($res,$i,'priority'), // Armando L�scher 04.07.2005 -> �priority -> Desc: Get vtiger_priority from db
						);
			}
		}
	
	$title=array();
	$title[]=$view;
	$title[]='myUpcoPendAct.gif';
	$title[]='home_myact';
	$title[]='showActivityView';		
	$title[]='MyUpcumingFrm';
	$title[]='activity_view';

	$header=array();
	$header[] =$current_module_strings['LBL_LIST_SUBJECT'];
	$header[] ='Type';

	$return_url="&return_module=$currentModule&return_action=DetailView&return_id=" . ((is_object($focus)) ? $focus->id : "");
	$oddRow = true;
	$entries=array();

	foreach($open_activity_list as $event){
		$recur_date=preg_replace('/--/','',$event['recurringdate']);
		if($recur_date!=""){
			$event['date_start']=$event['recurringdate'];
		}
		$font_color_high = "color:#00DD00;";
		$font_color_medium = "color:#DD00DD;";

		switch ($event['priority']){
			case 'High':
				$font_color=$font_color_high;
				break;
			case 'Medium':
				$font_color=$font_color_medium;
				break;
			default:
				$font_color='';
		}

		if($event['type'] != 'Task' && $event['type'] != 'Emails' && $event['type'] != ''){
			$activity_type = 'Events';
		}else{
			$activity_type = 'Task';
		}
	}
	$entries[$event['id']] = array(
			'0' => '<a href="index.php?action=DetailView&module='.$event["module"].'&activity_mode='.$activity_type.'&record='.$event["id"].''.$return_url.'" style="'.$font_color.';">'.$event["name"].'</a>',
			'IMAGE' => '<IMG src="'.$image_path.$event["type"].'s.gif">',
			);
	}
	$values=Array('noofactivities'=>$noofrecords,'Title'=>$title,'Header'=>$header,'Entries'=>$entries);
	$log->debug("Exiting getPendingActivities method ...");
	return $values;
}

/**
 * Function creates HTML to display ActivityView selection box
 * @param string   $activity_view                 - activity view 
 * return string   $ACTIVITY_VIEW_SELECT_OPTION   - HTML selection box
 */
function getActivityview($activity_view)	
{	
	global $log;
	$log->debug("Entering getActivityview(".$activity_view.") method ...");
	$today = date("Y-m-d", time());

	if($activity_view == 'Today')
	{	
		$selected1 = 'selected';
	}	
	else if($activity_view == 'This Week')
	{
		$selected2 = 'selected';
	}
	else if($activity_view == 'This Month')
	{	
		$selected3 = 'selected';
	}	
	else if($activity_view == 'This Year')	
	{
		$selected4 = 'selected';
	}

	//constructing the combo values for activities
	$ACTIVITY_VIEW_SELECT_OPTION = '<select class=small name="activity_view" onchange="showActivityView(this)">';
	$ACTIVITY_VIEW_SELECT_OPTION .= '<option value="Today" '.$selected1.'>';
	$ACTIVITY_VIEW_SELECT_OPTION .= 'Today';
	$ACTIVITY_VIEW_SELECT_OPTION .= '</option>';
	$ACTIVITY_VIEW_SELECT_OPTION .= '<option value="This Week" '.$selected2.'>';
	$ACTIVITY_VIEW_SELECT_OPTION .= 'This Week';
	$ACTIVITY_VIEW_SELECT_OPTION .= '</option>';
	$ACTIVITY_VIEW_SELECT_OPTION .= '<option value="This Month" '.$selected3.'>';
	$ACTIVITY_VIEW_SELECT_OPTION .= 'This Month';
	$ACTIVITY_VIEW_SELECT_OPTION .= '</option>';
	$ACTIVITY_VIEW_SELECT_OPTION .= '<option value="This Year" '.$selected4.'>';
	$ACTIVITY_VIEW_SELECT_OPTION .= 'This Year';
	$ACTIVITY_VIEW_SELECT_OPTION .= '</option>';
	$ACTIVITY_VIEW_SELECT_OPTION .= '</select>';
	
	$log->debug("Exiting getActivityview method ...");
	return $ACTIVITY_VIEW_SELECT_OPTION;
}
?>