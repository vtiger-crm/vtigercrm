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
require_once('include/utils/CommonUtils.php');
require_once('include/utils/UserInfoUtil.php');
require_once('modules/Calendar/CalendarCommon.php');


/**
 *  Function creates HTML to display Events and  Todos div tags
 *  @param array    $param_arr      - collection of objects and strings
 *  @param string   $viewBox        - string 'listview' or 'hourview' or may be empty. if 'listview' means get Events ListView.if 'hourview' means gets Events HourView. if empty means get Todos ListView
 *  @param string   $subtab         - string 'todo' or 'event'. if 'todo' means Todos View else Events View
 */
function calendar_layout(& $param_arr,$viewBox='',$subtab='')
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering calendar_layout() method");
	$cal_header = array ();
	if (isset($param_arr['size']) && $param_arr['size'] == 'small')
		$param_arr['calendar']->show_events = false;

	$cal_header['view'] = $param_arr['view'];
	$cal_header['IMAGE_PATH'] = $param_arr['IMAGE_PATH'];
        $cal_header['calendar'] = $param_arr['calendar'];
	$eventlabel = $mod_strings['LBL_EVENTS'];
	$todolabel = $mod_strings['LBL_TODOS'];
	//if $param_arr['size'] is set to 'small', get small(mini) calendar
	if(isset($param_arr['size']) && $param_arr['size'] == 'small')
	{
		get_mini_calendar($param_arr);
	}
	else
	{
		// User Select Customization
		$onlyForUserParam = "onlyforuser=" . calendarview_getSelectedUserId();
		// END

		//To differentiate selected subtab from unselected one - Starts
		if($subtab == 'event')
		{
			$eventtab_class = 'dvtSelectedCell';
			$todotab_class = 'dvtUnSelectedCell';
		        $event_anchor = $eventlabel;
		//	Added for User Based CustomView for Calendar module
			$todo_anchor = "<a href='index.php?module=Calendar&action=index&view=".$cal_header['view']."".$cal_header['calendar']->date_time->get_date_str()."&viewOption=".$viewBox."&subtab=todo&parenttab=".$category."&$onlyForUserParam'>".$todolabel."</a>";
					
		}
		elseif($subtab == 'todo')
		{
			$eventtab_class = 'dvtUnSelectedCell';
			$todotab_class = 'dvtSelectedCell';
		//	Added User Based CustomView for Calendar module
			$event_anchor = "<a href='index.php?module=Calendar&action=index&view=".$cal_header['view']."".$cal_header['calendar']->date_time->get_date_str()."&viewOption=".$viewBox."&subtab=event&parenttab=".$category."&$onlyForUserParam'>".$eventlabel."</a>";
			$todo_anchor = $todolabel;
		}
		//Ends
		//To get calendar header and its links(like Day,Week,Month,Year and etc.)
		get_cal_header_tab($cal_header,$viewBox,$subtab);
		$subheader = "";
		$subheader .=<<<EOQ
			<tr>
				<td colspan="8" class="calBorder">
					<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td>
							
								<table class="small" border="0" cellpadding="3" cellspacing="0" width="100%">
									<tr>
										<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
										<td class="$eventtab_class" id="pi" align="center" nowrap="nowrap" width="75">$event_anchor</td>
										<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
										<td class="$todotab_class" style="width:100px;" id="mi" align="center" nowrap="nowrap">$todo_anchor</td>
										<td class="dvtTabCache" nowrap="nowrap">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="calInnerBorder" align="left" bgcolor="#ffffff" valign="top">
EOQ;
		echo $subheader;
		if($viewBox == 'hourview' && $subtab == 'event')
		{
			get_cal_header_data($param_arr,$viewBox,$subtab);
			getHourView($param_arr);
		}
		elseif($viewBox == 'listview' && $subtab == 'event')
		{
			get_cal_header_data($param_arr,$viewBox,$subtab);
			getEventListView($param_arr);
		}
		elseif($subtab == 'todo')
		{
			$todo_list = "";
			$todo_list .= getTodosListView($param_arr,'',$subtab);
			$todo_list .= '</td></tr></table></td></tr></table><br>';
			echo $todo_list;
		}
	}
	
	$cal_log->debug("Exiting calendar_layout() method");	
	
}

/**
 * Function creates HTML to display small(mini) Calendar 
 * @param array   $cal    - collection of objects and strings
 */
function get_mini_calendar(& $cal){
	global $current_user,$adb,$cal_log,$mod_strings,$theme;
	$category = getParentTab();
	$cal_log->debug('Entering get_mini_calendar() method...');
	$count = 0;
	//To decide number of rows(weeks) in a month
	if ($cal['calendar']->month_array[$cal['calendar']->slices[35]]->start_time->month != $cal['calendar']->date_time->month) {
		$rows = 5;
	} else {
		$rows = 6;
	}
	$minical = "";
	$minical .= "<table class='mailClient ' bgcolor='white' border='0' cellpadding='2' cellspacing='0' width='98%'>
				<tr>
					<td class='calHdr'>&nbsp;</td>
					<td style='padding:5px' colspan='6' class='calHdr' align='center'>".get_previous_cal($cal)."&nbsp;";
					$minical .= "<a style='text-decoration: none;' href='index.php?module=Calendar&action=index&view=".$cal['view']."".$cal['calendar']->date_time->get_date_str()."&parenttab=".$category."'><b>".display_date($cal['view'],$cal['calendar']->date_time)."</b></a>&nbsp;".get_next_cal($cal)."</td>";
					$minical .= "<td class='calHdr' align='right'><a href='javascript:ghide(\"miniCal\");'><img src='". vtiger_imageurl('close.gif', $theme). "' align='right' border='0'></a>
				</td></tr>";
	$minical .= "<tr class='hdrNameBg'>";
	//To display days in week 
	$minical .= '<th width="12%">'.$mod_strings['LBL_WEEK'].'</th>';
	for ($i = 0; $i < 7; $i ++){
		$weekday = $mod_strings['cal_weekdays_short'][$i];
		$minical .= '<th width="12%">'.$weekday.'</th>';
	}
	$minical .= "</tr>";	
	$event_class = '';
	$class = '';
	for ($i = 0; $i < $rows; $i++){
		$minical .= "<tr>";
		
		//calculate blank days for first week
		for ($j = 0; $j < 7; $j ++){
			$cal['slice'] = $cal['calendar']->month_array[$cal['calendar']->slices[$count]];
			$class = dateCheck($cal['slice']->start_time->get_formatted_date());
			if($j == 0){
				$minical .= "<td style='text-align:center' ><a href='index.php?module=Calendar&action=index&view=week".$cal['slice']->start_time->get_date_str()."&parenttab=".$category."'>".$cal['slice']->start_time->week."</td>";
			}
			
			//To differentiate day having events from other days
			if(count($cal['slice']->activities) != 0 && ($cal['slice']->start_time->get_formatted_date() == $cal['slice']->activities[0]->start_time->get_formatted_date())){
				$event_class = 'class="eventDay"';
			}else{
				$event_class = '';
			}
			//To differentiate current day from other days
			if($class != '' ){
				$class = 'class="'.$class.'"';
			}else{
				$class = $event_class;
			}
			
			//To display month dates
			if ($cal['slice']->start_time->getMonth() == $cal['calendar']->date_time->getMonth()){
				$minical .= "<td ".$class." style='text-align:center' >";
				$minical .= "<a href='index.php?module=Calendar&action=index&view=".$cal['slice']->getView()."".$cal['slice']->start_time->get_date_str()."&parenttab=".$category."'>";
				$minical .= $cal['slice']->start_time->get_Date()."</a></td>";
			}else{
				$minical .= "<td style='text-align:center' ></td>";
			}
			$count++;
		}
		$minical .= '</tr>';
	}
	$minical .= "</table>";
	echo $minical;
	$cal_log->debug("Exiting get_mini_calendar() method...");
}

/**
 * Function creates HTML to display Calendar Header and its Links
 * @param array    $header   - collection of objects and strings
 * @param string   $viewBox  - string 'listview' or 'hourview' or may be empty. if 'listview' means Events ListView.if 'hourview' means Events HourView. if empty means get Todos ListView
 * @param string   $subtab   - string 'todo' or 'event'. if 'todo' means Todos View else Events View
 */
function get_cal_header_tab(& $header,$viewBox,$subtab)
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering get_cal_header_tab() method...");
	$tabhtml = "";
	$count = 1;
	include_once 'modules/Calendar/addEventUI.php';
	include_once 'modules/Calendar/header.php';
	$eventlabel = $mod_strings['LBL_EVENTS'];
	$todolabel = $mod_strings['LBL_TODOS'];
	$div = "<div id='miniCal' style='width:300px; position:absolute; display:none; left:100px; top:100px; z-index:100000; background-color:white'></div>
		<div id='calSettings' class='layerPopup calSettings' style='display:none;width:500px;' align=center ></div>
		<div id='dataArray'></div>
		";
	echo $div;
	$tabhtml .= "<table class='small calHdr' align='center' border='0' cellpadding='5' cellspacing='0' width='100%'><tr>";
        $links = array ('day','week','month','year');
	//To differentiate the selected link from unselected links
	foreach ($links as $link)
	{
		if ($header['view'] == $link)
		{
			$class = 'calSel';
			$anchor = $mod_strings["LBL_".$header['calendar']->getCalendarView($link)];
		}
		else
		{
			$class = 'calUnSel';
			$anchor = "<a href='index.php?module=Calendar&action=index&view=".$link."".$header['calendar']->date_time->get_date_str()."&viewOption=".$viewBox."&subtab=".$subtab."&parenttab=".$category."'>".$mod_strings["LBL_".$header['calendar']->getCalendarView($link)]."</a>";
		}
	
		if($count == 1)
			$tabhtml .= "<!-- day week month buttons --> <td style='border-left: 1px solid #666666;' class=".$class.">".$anchor."</td>";
		else
			$tabhtml .= "<td class=".$class.">".$anchor."</td>";
		$count++;
	}
	//To get Navigation(next&previous) links and display Date info
	$tabhtml .= "<td width='30%'>
			<table border='0' cellpadding='0' cellspacing='0'>
			<tr>
				<td>".get_previous_cal($header,$viewBox,$subtab)."
				</td>";
	$tabhtml .= "<td class='calendarNav'>".display_date($header['view'],$header['calendar']->date_time)."</td>";
	$tabhtml .= "<td>".get_next_cal($header,$viewBox,$subtab)."
		     </td></tr>
		    </table>
		</td>";
		$tabhtml .= "<td width='2%'><img onClick='fnvshobj(this,\"miniCal\"); getMiniCal(\"view=".$header['calendar']->view."".$header['calendar']->date_time->get_date_str()."&viewOption=".$viewBox."&subtab=".$subtab."&parenttab=".$category."\");' src='".$header['IMAGE_PATH']."btnL3Calendar.gif' alt='".$mod_strings['LBL_OPENCAL']."...' title='".$mod_strings['LBL_OPENCAL']."...' align='absmiddle' border='0'></td>";
		$tabhtml .= "<td width=20% ><img onClick='fnvshobj(this,\"calSettings\"); getCalSettings(\"view=".$header['calendar']->view."".$header['calendar']->date_time->get_date_str()."&viewOption=".$viewBox."&subtab=".$subtab."&parenttab=".$category."\");' src='".$header['IMAGE_PATH']."tbarSettings.gif' alt='".$mod_strings['LBL_SETTINGS']."' title='".$mod_strings['LBL_SETTINGS']."' align='absmiddle' border='0'></td>";
	$tabhtml .= "<td class='calHdr calTopRight componentName'>".$app_strings['Calendar']."</td>";	
	$tabhtml .= "</tr>";
	echo $tabhtml;
	$cal_log->debug("Exiting get_cal_header_tab() method...");
}

/**
 * Function creates HTML to display number of Events, Todos and pending list in calendar under header(Eg:Total Events : 5, 2 Pending / Total To Dos: 4, 1 Pending)
 * @param array  $cal_arr   - collection of objects and strings
 * @param string $viewBox   - string 'listview' or 'hourview'. if 'listview' means Events ListView.if 'hourview' means Events HourView.
 */
function get_cal_header_data(& $cal_arr,$viewBox,$subtab)
{
	global $mod_strings,$cal_log,$current_user,$adb,$theme;
	$cal_log->debug("Entering get_cal_header_data() method...");
	global $current_user,$app_strings;
        $date_format = $current_user->date_format;
	$format = $cal_arr['calendar']->hour_format;
	$hour_startat = timeString(array('hour'=>date('H:i',(time()+(5*60))),'minute'=>0),'24');
        $hour_endat = timeString(array('hour'=>date('H:i',(time() + (60 * 60))),'minute'=>0),'24');	
	$time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
	$temp_ts = $cal_arr['calendar']->date_time->ts;
	//To get date in user selected format
        $temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
	
	if($current_user->column_fields['is_admin']=='on')
		$Res = $adb->pquery("select * from vtiger_activitytype",array());
	else
	{
		$roleid=$current_user->roleid;
		$subrole = getRoleSubordinates($roleid);
		if(count($subrole)> 0)
		{
			$roleids = $subrole;
			array_push($roleids, $roleid);
		}
		else
		{	
			$roleids = $roleid;
		}

		if (count($roleids) > 1) {
			$Res=$adb->pquery("select distinct activitytype from  vtiger_activitytype inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_activitytype.picklist_valueid where roleid in (". generateQuestionMarks($roleids) .") and picklistid in (select picklistid from vtiger_activitytype) order by sortid asc", array($roleids));
		} else {
			$Res=$adb->pquery("select distinct activitytype from vtiger_activitytype inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_activitytype.picklist_valueid where roleid = ? and picklistid in (select picklistid from vtiger_activitytype) order by sortid asc", array($roleid));
		}
	}
	
	$eventlist='';
	for($i=0; $i<$adb->num_rows($Res);$i++)
	{
		$eventlist .= $adb->query_result($Res,$i,'activitytype').";";
	}
	
	$headerdata = "";
	$headerdata .="
			<div style='display: block;' id='mnuTab'>
			<form name='EventViewOption' method='POST' action='index.php' style='display:inline;'>
			<input type='hidden' id='complete_view' name='complete_view' value='' />
			<table align='center' border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tr><td colspan='3'>&nbsp;</td></tr>";
			if(isPermitted("Calendar","EditView") == "yes")
			{
			$headerdata .="<tr>
				<td>
				<table><tr><td class='calAddButton' style='cursor:pointer;height:30px' align='center' width='15%' onMouseOver='fnAddEvent(this,\"addEventDropDown\",\"".$temp_date."\",\"".$temp_date."\",\"".$time_arr['starthour']."\",\"".$time_arr['startmin']."\",\"".$time_arr['startfmt']."\",\"".$time_arr['endhour']."\",\"".$time_arr['endmin']."\",\"".$time_arr['endfmt']."\",\"".$viewBox."\",\"".$subtab."\",\"".$eventlist."\");'>
					".$mod_strings['LBL_ADD']."
					<img src='".vtiger_imageurl('menuDnArrow.gif', $theme)."' style='padding-left: 5px;' border='0'>
				</td></tr></table> </td>";
			}
			else
			{
				$headerdata .="<tr><td>&nbsp;</td>";
			}
	$headerdata .="<td align='center' width='43%'><span id='total_activities'>";//USER SELECT CUSTOMIZATION
	$headerdata .= getEventInfo($cal_arr,'listcnt'); 
	$headerdata .= "</span></td>
				<td align='center' width='40%'><table border=0 cellspacing=0 cellpadding=2><tr><td class=small><b>".$mod_strings['LBL_VIEW']." : </b></td><td>";//USER SELECT CUSTOMIZATION
	$view_options = getEventViewOption($cal_arr,$viewBox);

	// User Select Customization
	$view_options .= calendarview_getUserSelectOptions( calendarview_getSelectedUserId() );
	// END

	$headerdata .=$view_options."</td></tr></table>
				</td>
			</tr>
		</table></form>";
	echo $headerdata;	
	$cal_log->debug("Exiting get_cal_header_data() method...");
}

/**
 * Function creates HTML select statement to display View selection box
 * @param array  $cal     - collection of objects and strings 
 * @param string $viewBox - string 'listview' or 'hourview'. if 'listview' means get Events ListView.if 'hourview' means get Events HourView.
 * return string $view   - html selection box
 */
function getEventViewOption(& $cal,$viewBox)
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	if($viewBox == 'listview')
	{
		$list_sel = 'selected';
		$hr_sel = '';
	}
	else
	{
		$list_sel = '';
		$hr_sel = 'selected';
	}
	$cal_log->debug("Entering getEventViewOption() method...");
	$view = "<input type='hidden' name='view' value='".$cal['calendar']->view."'>
			<input type='hidden' name='hour' value='".$cal['calendar']->date_time->hour."'>
			<input type='hidden' name='day' value='".$cal['calendar']->date_time->day."'>
			<input type='hidden' name='week' value='".$cal['calendar']->date_time->week."'>
			<input type='hidden' name='month' value='".$cal['calendar']->date_time->month."'>
			<input type='hidden' name='year' value='".$cal['calendar']->date_time->year."'>
			<input type='hidden' name='parenttab' value='".$category."'>
			<input type='hidden' name='module' value='Calendar'>
			<input type='hidden' name='return_module' value='Calendar'>
			<input type='hidden' name='action' value=''>
			<input type='hidden' name='return_action' value=''>
							 
		        <select name='viewOption' class='importBox' id='view_Option' onChange='fnRedirect();'>";
	if($cal['view'] == 'day')
	{
		$view .="<option value='listview' ".$list_sel.">".$mod_strings['LBL_LISTVIEW']."</option>
			 <option value='hourview' ".$hr_sel.">".$mod_strings['LBL_HRVIEW']."</option>";	
	}
	elseif($cal['view'] == 'week')
	{
		$view .="<option value='listview' ".$list_sel.">".$mod_strings['LBL_LISTVIEW']."</option>
			 <option value='hourview' ".$hr_sel.">".$mod_strings['LBL_WEEKVIEW']."</option>";
	}
	elseif($cal['view'] == 'month')
	{
		$view .="<option value='listview' ".$list_sel.">".$mod_strings['LBL_LISTVIEW']."</option>
			 <option value='hourview' ".$hr_sel.">".$mod_strings['LBL_MONTHVIEW']."</option>";
	}
	elseif($cal['view'] == 'year')
	{
		$view .="<option value='listview' ".$list_sel.">".$mod_strings['LBL_LISTVIEW']."</option>
			 <option value='hourview' ".$hr_sel.">".$mod_strings['LBL_YEARVIEW']."</option>";
	}
	else
		die("view is not defined");
	$view .="</select>";
	$cal_log->debug("Exiting getEventViewOption() method...");
	return $view;
}

/**
 * Function creates HTML anchor tag to get previous-day/week/month/year view
 * @param array  $cal        - collection of objects and strings
 * @param string $viewBox    - string 'listview' or 'hourview' or may be empty. if 'listview' means previous link in Events ListView.if 'hourview' means previous link in Events HourView. if empty means previous link in Todos ListView
 * @param string   $subtab   - string 'todo' or 'event' or may be empty. if 'todo' means Todos View. if 'event' means Events View. if empty means small calendar view. 
 * return string $link       - html tags in string format
 */
function get_previous_cal(& $cal,$viewBox='',$subtab='')
{
	global $mod_strings,$cal_log,$theme;
	$category = getParentTab();
	$cal_log->debug("Entering get_previous_cal() method...");
	if(isset($cal['size']) && $cal['size'] == 'small')
        {
		$link = "<a href='javascript:getMiniCal(\"view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('prev')."&parenttab=".$category."\")'><img src= '". vtiger_imageurl('small_left.gif', $theme)."' border='0' align='absmiddle' /></a>";
	}
	else
	{
		$link = "<a href='index.php?action=index&module=Calendar&view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('prev')."&viewOption=".$viewBox."&subtab=".$subtab."&parenttab=".$category."&onlyforuser=".calendarview_getSelectedUserId()."' onclick='VtigerJS_DialogBox.block();return true;'><img src='".$cal['IMAGE_PATH']."cal_prev_nav.gif' border='0' align='absmiddle' /></a>";
	}
	$cal_log->debug("Exiting get_previous_cal() method...");
	return $link;
}

/**
 * Function creates HTML anchor tag to get next-day/week/month/year view
 * @param array  $cal        - collection of objects and strings
 * @param string $viewBox    - string 'listview' or 'hourview' or may be empty. if 'listview' means next link in Events ListView.if 'hourview' means next link in Events HourView. if empty means next link in Todos ListView
 * @param string $subtab     - string 'todo' or 'event' or may be empty. if 'todo' means Todos View. if 'event' means Events View. if empty means small calendar view. 
 * return string $link       - html tags in string format
 */
function get_next_cal(& $cal,$viewBox='',$subtab='')
{
	global $mod_strings,$cal_log,$theme;
	$category = getParentTab();
	$cal_log->debug("Entering get_next_cal() method...");
	if(isset($cal['size']) && $cal['size'] == 'small')
	{
		$link = "<a href='javascript:getMiniCal(\"view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('next')."&parenttab=".$category."\")'  ><img src='". vtiger_imageurl('small_right.gif', $theme)."' border='0' align='absmiddle' /></a>";
	}
	else
	{
		$link = "<a href='index.php?action=index&module=Calendar&view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('next')."&viewOption=".$viewBox."&subtab=".$subtab."&parenttab=".$category."&onlyforuser=".calendarview_getSelectedUserId()."' onclick='VtigerJS_DialogBox.block();return true;'><img src='".$cal['IMAGE_PATH']."cal_next_nav.gif' border='0' align='absmiddle' /></a>";
	}
	$cal_log->debug("Exiting get_next_cal() method...");
	return $link;

}

/**
 * Function to get date info depending upon on the calendar view(Eg: 21 July 2000)
 * @param string  $view        - calendar view(day/week/month/year)
 * @param array   $date_time   - contains DateTime object
 * return string  $label       - date info(Eg for dayview : 13 July 2000)
 */
function display_date($view,$date_time)
{
	global $cal_log;
	$cal_log->debug("Entering display_date() method...");
	if ($view == 'day')
        {
		//$label = $date_time->getdayofWeek()." ";
		$label = $date_time->get_Date()." ";
		$label .= $date_time->getmonthName()." ";
		$label .= $date_time->year;
		$cal_log->debug("Exiting display_date() method...");
		return $label;
        }
	elseif ($view == 'week')
        {
                $week_start = $date_time->getThisweekDaysbyIndex(0);
                $week_end = $date_time->getThisweekDaysbyIndex(6);
                $label = $week_start->get_Date()." ";
                $label .= $week_start->getmonthName()." ";
                $label .= $week_start->year;
                $label .= " - ";
                $label .= $week_end->get_Date()." ";
                $label .= $week_end->getmonthName()." ";
                $label .= $week_end->year;
		$cal_log->debug("Exiting display_date() method...");
		return $label;
        }

	elseif ($view == 'month')
	{
		$label = $date_time->getmonthName()." ";
		$label .= $date_time->year;
		$cal_log->debug("Exiting display_date() method...");
		return $label;
        }
	elseif ($view == 'year')
	{
		$cal_log->debug("Exiting display_date() method...");
		return $date_time->year;
        }

}
/**
 *  Function to get css class name for date
 *  @param   string  $slice_date    - date
 *  returns  string                 - css class name or empty string 
 */
function dateCheck($slice_date)
{
	global $cal_log;
	$cal_log->debug("Entering dateCheck() method...");
	$today = date('Y-m-d');
	if($today == $slice_date)
	{
		$cal_log->debug("Exiting dateCheck() method...");
		//css class for day having event(s)
		return 'currDay';
	}
	else
	{
		$cal_log->debug("Exiting dateCheck() method...");
		return '';
	}
}

/**
 * Function to construct respective calendar layout depends on the calendar view
 * @param  array     $view      -  collection of objects and strings
 */
function getHourView(& $view)
{
	global $cal_log,$theme;
	$hourview_layout = '';
	$cal_log->debug("Entering getHourView() method...");
	$hourview_layout .= '<br /><!-- HOUR VIEW LAYER STARTS HERE -->
		<div id="hrView" align=center>';
		
	if($view['view'] == 'day')
		$hourview_layout .= getDayViewLayout($view);
	elseif($view['view'] == 'week')
		$hourview_layout .= getWeekViewLayout($view);
	elseif($view['view'] == 'month')
		 $hourview_layout .= getMonthViewLayout($view);
	elseif($view['view'] == 'year')
		 $hourview_layout .= getYearViewLayout($view);
	else
		die("view:".$view['view']." is not defined");
		
	$hourview_layout .= '<br></div>
		</div>';
	$hourview_layout .= '<br></td></tr></table></td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</div>
		</td>
	        <td valign=top><img src="'.vtiger_imageurl('showPanelTopRight.gif', $theme).'"></td>
		</tr>
		   </table>
	<br>';
	echo $hourview_layout;
	$cal_log->debug("Exiting getHourView() method...");
}

/**
 * Fuction constructs Events ListView depends on the view
 * @param   array  $cal            - collection of objects and strings
 * @param   string $mode           - string 'listcnt' or empty. if empty means get Events ListView else get total no. of events and no. of pending events Info.
 * returns  string $activity_list  - total no. of events and no. of pending events Info(Eg: Total Events : 2, 1 Pending).
 */
function getEventListView(& $cal,$mode='')
{
	global $cal_log,$theme;
	$list_view = "";
        $cal_log->debug("Entering getEventListView() method...");
	if($cal['calendar']->view == 'day')
	{
		$start_date = $end_date = $cal['calendar']->date_time->get_formatted_date();
	}
	elseif($cal['calendar']->view == 'week')
	{
		$start_date = $cal['calendar']->slices[0];
		$end_date = $cal['calendar']->slices[6];
	}
	elseif($cal['calendar']->view == 'month')
        {
		$start_date = $cal['calendar']->date_time->getThismonthDaysbyIndex(0);
		$end_date = $cal['calendar']->date_time->getThismonthDaysbyIndex($cal['calendar']->date_time->daysinmonth - 1);
		$start_date = $start_date->get_formatted_date();
		$end_date = $end_date->get_formatted_date();
        }
	elseif($cal['calendar']->view == 'year')
        {
		$start_date = $cal['calendar']->date_time->getThisyearMonthsbyIndex(0);
		$end_date = $cal['calendar']->date_time->get_first_day_of_changed_year('increment');
		$start_date = $start_date->get_formatted_date();
		$end_date = $end_date->get_formatted_date();
	}
	else
        {
		die("view:".$cal['calendar']->view." is not defined");
        }
	//if $mode value is empty means get Events list in array format else get the count of total events and pending events in array format.
	if($mode != '')
	{
		$activity_list = getEventList($cal, $start_date, $end_date,$mode);
		$cal_log->debug("Exiting getEventListView() method...");
		return $activity_list;
	}
	else
	{
		$ret_arr = getEventList($cal, $start_date, $end_date,$mode);
	        $activity_list = $ret_arr[0];
	        $navigation_array = $ret_arr[1];
	}
	//To get Events listView
	$list_view .="<br><div id='listView'>";
	$list_view .=constructEventListView($cal,$activity_list,$navigation_array);
	$list_view .="<br></div>
		</div>";
	$list_view .="<br></td></tr></table></td></tr></table>
			</td></tr></table>
		</td></tr></table>
		</div>
		</td></tr></table>
		</td>
		<td valign=top><img src='".vtiger_imageurl('showPanelTopRight.gif', $theme)."'></td>
		</tr>
	</table>
	<br>";
	echo $list_view;
	$cal_log->debug("Exiting getEventListView() method...");
}


/**
 * Fuction constructs Todos ListView depends on the view
 * @param   array  $cal            - collection of objects and strings
 * @param   string $check          - string 'listcnt' or empty. if empty means get Todos ListView else get total no. of Todos and no. of pending todos Info.
 * returns  string $todo_list      - total no. of todos and no. of pending todos Info(Eg: Total Todos : 2, 1 Pending).
 */
function getTodosListView($cal, $check='',$subtab='')
{
	global $cal_log,$theme;
	$list_view = "";
        $cal_log->debug("Entering getTodosListView() method...");
	if($cal['calendar']->view == 'day')
        {
                $start_date = $end_date = $cal['calendar']->date_time->get_formatted_date();
        }
	elseif($cal['calendar']->view == 'week')
        {
                $start_date = $cal['calendar']->slices[0];
                $end_date = $cal['calendar']->slices[6];
        }
        elseif($cal['calendar']->view == 'month')
        {
                $start_date = $cal['calendar']->date_time->getThismonthDaysbyIndex(0);
                $end_date = $cal['calendar']->date_time->getThismonthDaysbyIndex($cal['calendar']->date_time->daysinmonth - 1);
		$start_date = $start_date->get_formatted_date();
		$end_date = $end_date->get_formatted_date();
        }
	elseif($cal['calendar']->view == 'year')
        {
                $start_date = $cal['calendar']->date_time->getThisyearMonthsbyIndex(0);
                $end_date = $cal['calendar']->date_time->get_first_day_of_changed_year('increment');
		$start_date = $start_date->get_formatted_date();
		$end_date = $end_date->get_formatted_date();
        }
        else
        {
                die("view:".$cal['calendar']->view." is not defined");
        }
	//if $check value is empty means get Todos list in array format else get the count of total todos and pending todos in array format.
	if($check != '')
	{
		$todo_list = getTodoList($cal, $start_date, $end_date,$check);
		$cal_log->debug("Exiting getTodosListView() method...");
		return $todo_list;
	}
	else
	{
		$ret_arr = getTodoList($cal, $start_date, $end_date,$check);
		$todo_list = $ret_arr[0];
		$navigation_arr = $ret_arr[1];
	}
	$cal_log->debug("Exiting getTodosListView() method...");
	$list_view .="<div id='mnuTab2' style='background-color: rgb(255, 255, 215); display:block;'>";
	//To get Todos listView
	$list_view .= constructTodoListView($todo_list,$cal,$subtab,$navigation_arr);
	$list_view .="</div></div></td></tr></table></td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</div>
		</td>
		<td valign=top><img src='".vtiger_imageurl('showPanelTopRight.gif', $theme)."'></td>
	</tr>
	</table>

	";
	echo $list_view;
}

/**
 * Function creates HTML to display Calendar DayView
 * @param  array     $cal            - collections of objects and strings.
 * return  string    $dayview_layout - html tags in string format
 */
function getDayViewLayout(& $cal)
{
	global $current_user,$app_strings,$cal_log,$adb;
	$no_of_rows = 1;
	$cal_log->debug("Entering getDayViewLayout() method...");
        $date_format = $current_user->date_format;
	$day_start_hour = $cal['calendar']->day_start_hour;
	$day_end_hour = $cal['calendar']->day_end_hour;
	$format = $cal['calendar']->hour_format;
	$show_complete_view = false;
	if(!empty($_REQUEST['complete_view'])){
		$show_complete_view =true;
	}
	$dayview_layout = '';
	$dayview_layout .= '<!-- Day view layout starts here --> <table border="0" cellpadding="10" cellspacing="0" width="100%">';
	$dayview_layout .= '<tr>
					<td id="mainContent" style="border-top: 1px solid rgb(204, 204, 204);">
					<table border="0" cellpadding="5" cellspacing="0" width="100%">';
	if(!empty($show_complete_view)) {
		$dayview_layout .= '<tr><td width=12% class="lvtCol" bgcolor="blue" valign=top><img onClick="document.EventViewOption.complete_view.value=0;fnRedirect();" src="'.vtiger_imageurl('activate.gif', $theme).'" border="0"></td><td class="dvtCellInfo">&nbsp;</td><td class="dvtCellInfo">&nbsp;</td></tr>';
		$day_start_hour = 0;
		$day_end_hour = 23;
	} else {
		$dayview_layout .= '<tr><td width=12% class="lvtCol" bgcolor="blue" valign=top><img onClick="document.EventViewOption.complete_view.value=1;fnRedirect();" src="'.vtiger_imageurl('inactivate.gif', $theme).'" border="0"></td><td class="dvtCellInfo">&nbsp;</td><td class="dvtCellInfo">&nbsp;</td></tr>';
	}
	for($j=0;$j<24;$j++)
	{
		$slice = $cal['calendar']->slices[$j];
		$act = $cal['calendar']->day_slice[$slice]->activities;
		if(!empty($act))
		{
			$temprows = count($act);
			$no_of_rows = ($no_of_rows>$temprows)?$no_of_rows:$temprows;
		}
	}
	for($i=$day_start_hour;$i<=$day_end_hour;$i++)
	{
		$time = array('hour'=>$i,'minute'=>0);
		$sub_str = timeString($time,$format);
		$y = $i+1;
		$hour_startat = timeString(array('hour'=>$i,'minute'=>0),'24');
		$hour_endat = timeString(array('hour'=>$y,'minute'=>0),'24');
		$time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
		$temp_ts = $cal['calendar']->date_time->ts;
		$sttemp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
		if($i != 23)
		{
			$endtemp_date = $sttemp_date;
		}
		else
		{
			$addday = $temp_ts + (1 * 24 * 60 * 60);
			$endtemp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$addday)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$addday)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $addday)):(''))));
		}

		$js_string = "";
		if(isPermitted("Calendar","EditView") == "yes")
		              $js_string = 'onClick="fnvshobj(this,\'addEvent\'); gshow(\'addEvent\',\'Call\',\''.$sttemp_date.'\',\''.$endtemp_date.'\',\''.$time_arr['starthour'].'\',\''.$time_arr['startmin'].'\',\''.$time_arr['startfmt'].'\',\''.$time_arr['endhour'].'\',\''.$time_arr['endmin'].'\',\''.$time_arr['endfmt'].'\',\'hourview\',\'event\')"';
		$dayview_layout .= '<tr>
					<td style="cursor:pointer;" class="lvtCol" valign=top height="75"  width="10%" '.$js_string.'>'.$sub_str.'</td>';
		//To display events in Dayview
		$dayview_layout .= getdayEventLayer($cal,$cal['calendar']->slices[$i],$no_of_rows);
		$dayview_layout .= '</tr>';
	}
	$dayview_layout .= '</table>
			</td></tr></table>';
	$cal_log->debug("Exiting getDayViewLayout() method...");
	return $dayview_layout;		
}

/**
 * Function creates HTML to display Calendar WeekView
 * @param  array     $cal             - collections of objects and strings.
 * return  string    $weekview_layout - html tags in string format
 */
function getWeekViewLayout(& $cal)
{
	global $current_user,$app_strings,$cal_log,$theme;
	$category = getParentTab();
	$cal_log->debug("Entering getWeekViewLayout() method...");
        $date_format = $current_user->date_format;
	$day_start_hour = $cal['calendar']->day_start_hour;
	$day_end_hour = $cal['calendar']->day_end_hour;
	$format = $cal['calendar']->hour_format;
	$show_complete_view = false;
	if(!empty($_REQUEST['complete_view'])){
		$show_complete_view =true;
	}
	$weekview_layout = '';
    $weekview_layout .= '<table border="0" cellpadding="10" cellspacing="0" width="98%" class="calDayHour" style="background-color: #dadada">';
	for ($col=0;$col<=7;$col++)
    {
    	if($col==0) {
            	$weekview_layout .= '<tr>';
            	if(!empty($show_complete_view)) {
            		$weekview_layout .= '<td width=12% class="lvtCol" bgcolor="blue" valign=top><img onClick="document.EventViewOption.complete_view.value=0;fnRedirect();" src="'.vtiger_imageurl('activate.gif', $theme).'" border="0"></td>';
					$day_start_hour = 0;
					$day_end_hour = 23;
            	} else {
            		$weekview_layout .= '<td width=12% class="lvtCol" bgcolor="blue" valign=top><img onClick="document.EventViewOption.complete_view.value=1;fnRedirect();" src="'.vtiger_imageurl('inactivate.gif', $theme).'" border="0"></td>';
            	}
		} else {
			//To display Days in Week
			$cal['slice'] = $cal['calendar']->week_array[$cal['calendar']->slices[$col-1]];
			$date = $cal['calendar']->date_time->getThisweekDaysbyIndex($col-1);
			$day = $date->getdayofWeek_inshort();
			$weekview_layout .= '<td width=12% class="lvtCol" bgcolor="blue" valign=top>';
			$weekview_layout .= '<a href="index.php?module=Calendar&action=index&view='.$cal['slice']->getView().'&'.$cal['slice']->start_time->get_date_str().'&parenttab='.$category.'">';
			$weekview_layout .= $date->get_Date().' - '.$day;
			$weekview_layout .= "</a>";
			$weekview_layout .= '</td>';
		}
	}
	$weekview_layout .= '</tr></table>';
	$weekview_layout .= '<table border="0" cellpadding="10" cellspacing="1" width="98%" class="calDayHour" style="background-color: #dadada">';
	//To display Hours in User selected format
	for($i=$day_start_hour;$i<=$day_end_hour;$i++)
	{
		$count = $i;
		$hour_startat = timeString(array('hour'=>$i,'minute'=>0),'24');
		$hour_endat = timeString(array('hour'=>($i+1),'minute'=>0),'24');
		$time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
		$weekview_layout .= '<tr>';
		for ($column=1;$column<=1;$column++)
        	{
			$time = array('hour'=>$i,'minute'=>0);
			$sub_str = timeString($time,$format);
			
			$weekview_layout .= '<td style="border-top: 1px solid rgb(239, 239, 239); background-color: rgb(234, 234, 234); height: 40px;" valign="top" width="12%">';
			$weekview_layout .=$sub_str;
	                $weekview_layout .= '</td>';
		}
		for ($column=0;$column<=6;$column++)
		{
			$temp_ts = $cal['calendar']->week_array[$cal['calendar']->slices[$column]]->start_time->ts;
			$sttemp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
			if($i != 23)
				$endtemp_date = $sttemp_date;
			else
			{
				$endtemp_ts = $cal['calendar']->week_array[$cal['calendar']->slices[$column+1]]->start_time->ts;
				$endtemp_date =  (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$endtemp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$endtemp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $endtemp_ts)):(''))));
			}

			$weekview_layout .= '<td class="cellNormal" onMouseOver="cal_show(\'create_'.$sttemp_date.''.$time_arr['starthour'].''.$time_arr['startfmt'].'\')" onMouseOut="fnHide_Event(\'create_'.$sttemp_date.''.$time_arr['starthour'].''.$time_arr['startfmt'].'\')"  style="height: 40px;" bgcolor="white" valign="top" width="12%" align=right vlign=top>';
			$weekview_layout .= '<div id="create_'.$sttemp_date.''.$time_arr['starthour'].''.$time_arr['startfmt'].'" style="visibility: hidden;">';
			if(isPermitted("Calendar","EditView") == "yes")
		                        $weekview_layout .='<img onClick="fnvshobj(this,\'addEvent\'); gshow(\'addEvent\',\'Call\',\''.$sttemp_date.'\',\''.$endtemp_date.'\',\''.$time_arr['starthour'].'\',\''.$time_arr['startmin'].'\',\''.$time_arr['startfmt'].'\',\''.$time_arr['endhour'].'\',\''.$time_arr['endmin'].'\',\''.$time_arr['endfmt'].'\',\'hourview\',\'event\')" src="'.vtiger_imageurl('cal_add.gif', $theme).'" border="0">';
					
                        $weekview_layout .='</div>';
			//To display events in WeekView
			$weekview_layout .=getweekEventLayer($cal,$cal['calendar']->week_hour_slices[$count]);
			$weekview_layout .= '</td>';
			$count = $count+24;
		}
		$weekview_layout .= '</tr>';
	}
	$weekview_layout .= '</table>';
	return $weekview_layout;
	$cal_log->debug("Exiting getWeekViewLayout() method...");
		
}

/**
 * Function creates HTML to display Calendar MonthView
 * @param  array     $cal            - collections of objects and strings.
 * return  string    $monthview_layout - html tags in string format
 */
function getMonthViewLayout(& $cal)
{
	global $current_user,$app_strings,$cal_log,$theme;
	$category = getParentTab();
	$cal_log->debug("Entering getMonthViewLayout() method...");
	$date_format = $current_user->date_format;
	$count = 0;
	//To get no. of rows(weeks) in month
        if ($cal['calendar']->month_array[$cal['calendar']->slices[35]]->start_time->month != $cal['calendar']->date_time->month) {
                $rows = 5;
        } else {
                $rows = 6;
        }
	$format = $cal['calendar']->hour_format;
	$hour_startat = timeString(array('hour'=>date('H:i'),'minute'=>0),'24');
        $hour_endat = timeString(array('hour'=>date('H:i',(time() + (60 * 60))),'minute'=>0),'24');
	$time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
	$monthview_layout = '';
	$monthview_layout .= '<table class="calDayHour" style="background-color: rgb(218, 218, 218);" border="0" cellpadding="5" cellspacing="1" width="98%"><tr>';
	//To display days in week 
	for ($i = 0; $i < 7; $i ++)
	{
		$first_row = $cal['calendar']->month_array[$cal['calendar']->slices[$i]];
		$weekday = $first_row->start_time->getdayofWeek();
		$monthview_layout .= '<td class="lvtCol" valign="top" width="14%">'.$weekday.'</td>';
	}
	$monthview_layout .= '</tr></table>';
	$monthview_layout .= '<!-- month headers --> <table border=0 cellspacing=1 cellpadding=5 width=98% class="calDayHour" >';
	$cnt = 0;
	for ($i = 0; $i < $rows; $i ++)
	{
	        $monthview_layout .= '<tr>';
		for ($j = 0; $j < 7; $j ++)
                {
			$temp_ts = $cal['calendar']->month_array[$cal['calendar']->slices[$count]]->start_time->ts;
	                $temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
			if($cal['calendar']->day_start_hour != 23)
				$endtemp_date = $temp_date;
			else
			{
				$endtemp_ts = $cal['calendar']->month_array[$cal['calendar']->slices[$count+1]]->start_time->ts;
				$endtemp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$endtemp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$endtemp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $endtemp_ts)):(''))));
			}
			$cal['slice'] = $cal['calendar']->month_array[$cal['calendar']->slices[$count]];
			$monthclass = dateCheck($cal['slice']->start_time->get_formatted_date());
			if($monthclass != '')
				$monthclass = 'calSel';
			else
				$monthclass = 'dvtCellLabel';
			//to display dates in month
			if ($cal['slice']->start_time->getMonth() == $cal['calendar']->date_time->getMonth())
			{
				$monthview_layout .= '<td style="text-align:left;" class="'.$monthclass.'" width="14%" onMouseOver="cal_show(\'create_'.$temp_date.''.$time_arr['starthour'].'\')" onMouseOut="fnHide_Event(\'create_'.$temp_date.''.$time_arr['starthour'].'\')">';
				$monthview_layout .= '<a href="index.php?module=Calendar&action=index&view='.$cal['slice']->getView().''.$cal['slice']->start_time->get_date_str().'&parenttab='.$category.'">';
				$monthview_layout .= $cal['slice']->start_time->get_Date();
				$monthview_layout .= '</a>';
				$monthview_layout .= '<div id="create_'.$temp_date.''.$time_arr['starthour'].'" style="visibility:hidden;">';
				if(isPermitted("Calendar","EditView") == "yes")
                                $monthview_layout .='<a onClick="fnvshobj(this,\'addEvent\'); gshow(\'addEvent\',\'Call\',\''.$temp_date.'\',\''.$endtemp_date.'\',\''.$time_arr['starthour'].'\',\''.$time_arr['startmin'].'\',\''.$time_arr['startfmt'].'\',\''.$time_arr['endhour'].'\',\''.$time_arr['endmin'].'\',\''.$time_arr['endfmt'].'\',\'hourview\',\'event\')" href="javascript:void(0)"><img src="' . vtiger_imageurl('cal_add.gif', $theme). '" border="0"></a>';
                                $monthview_layout .= '  </div></td>';
			}
			else
			{
				$monthview_layout .= '<td class="dvtCellLabel" width="14%">&nbsp;</td>';
			}
			$count++;
		}
		$monthview_layout .= '</tr>';
		$monthview_layout .= '<tr>';
		for ($j = 0; $j < 7; $j ++)
		{
			$monthview_layout .= '<td bgcolor="white" height="90" valign="top" width="200" align=right>';
			$monthview_layout .= getmonthEventLayer($cal,$cal['calendar']->slices[$cnt]);
			$monthview_layout .= '</td>';
			$cnt++;
		}
		$monthview_layout .= '</tr>';
	}
	$monthview_layout .= '</table>';
	return $monthview_layout;
	$cal_log->debug("Exiting getMonthViewLayout() method...");
		
}

/**
 * Function creates HTML to display Calendar YearView
 * @param  array     $cal            - collections of objects and strings.
 * return  string    $yearview_layout - html tags in string format
 */
function getYearViewLayout(& $cal)
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering getYearViewLayout() method...");
	$yearview_layout = '';
	$yearview_layout .= '<table border="0" cellpadding="5" cellspacing="0" width="100%">';
	$count = 0;
	//year view divided as 4 rows and 3 columns
	for($i=0;$i<4;$i++)
	{
		$yearview_layout .= '<tr>';
		for($j=0;$j<3;$j++)
        	{
			$cal['slice'] = $cal['calendar']->year_array[$cal['calendar']->slices[$count]];
			$yearview_layout .= '<td width="33%">
						<table class="mailClient " border="0" cellpadding="2" cellspacing="0" width="98%">
							<tr>
								<td colspan="7" class="calHdr" style="padding:5px">
								<a style="text-decoration: none;" href="index.php?module=Calendar&action=index&view=month&hour=0&day=1&month='.($count+1).'&year='.$cal['calendar']->date_time->year.'&parenttab='.$category.'"><b>
									'.$cal['slice']->start_time->month_inlong.'
									</b></a>
								</td>
							</tr><tr class="hdrNameBg">';
			for($w=0;$w<7;$w++)
			{
				$yearview_layout .= '<th width="14%">'.$mod_strings['cal_weekdays_short'][$w].'</th>';
			}
			$yearview_layout .= '</tr>';
			list($_3rdyear,$_3rdmonth,$_3rddate) = explode("-",$cal['calendar']->month_day_slices[$count][35]);
			list($_2ndyear,$_2ndmonth,$_2nddate) = explode("-",$cal['calendar']->month_day_slices[$count][6]);
			//to get no. of rows(weeks) in month
			if ($_3rdmonth != $_2ndmonth) {
	        	        $rows = 5;
        		} else {
		                $rows = 6;
		        }
			$cnt = 0;
			$date_stack = Array();
			for ($k = 0; $k < 5; $k ++)
        		{
				$yearview_layout .= '<tr>';
				for ($mr = 0; $mr < 7; $mr ++)
				{
					list($_1styear,$_1stmonth,$_1stdate) = explode("-",$cal['calendar']->month_day_slices[$count][$cnt]);
					if(count($cal['slice']->activities) != 0)
					{
						for($act_count = 0;$act_count<count($cal['slice']->activities);$act_count++)
						{
							array_push($date_stack,$cal['slice']->activities[$act_count]->start_time->get_formatted_date());
						}
					}
					if(in_array($cal['calendar']->month_day_slices[$count][$cnt],$date_stack))
						$event_class = 'class="eventDay"'; 
					else
						$event_class = '';
					if($_1stmonth == $_2ndmonth)
						$curclass = dateCheck($cal['calendar']->month_day_slices[$count][$cnt]);
					if($curclass != '')
					{
						$class = 'class="'.$curclass.'"';
						$curclass = '';
					}
					else
					{
						$class = $event_class;
						$event_class = '';
					}
					$date = $_1stdate + 0;
					$month = $_1stmonth + 0;
					$yearview_layout .= '<td '.$class.' style="text-align:center">';
					if($rows == 6 && $k==0)
					{
						list($tempyear,$tempmonth,$tempdate) = explode("-",$cal['calendar']->month_day_slices[$count][35+$mr]);
						if($tempmonth == $_2ndmonth)
							$yearview_layout .= '<a href="index.php?module=Calendar&action=index&view=day&hour=0&day='.$tempdate.'&month='.$tempmonth.'&year='.$tempyear.'&parenttab='.$category.'">'.$tempdate;
					}
					if($_1stmonth == $_2ndmonth)
					{
						$yearview_layout .= '<a href="index.php?module=Calendar&action=index&view=day&hour=0&day='.$date.'&month='.$month.'&year='.$_1styear.'&parenttab='.$category.'">'.$date;
					}
					$yearview_layout .= '</a></td>';
				$cnt++;
				}
	                	$yearview_layout .= '</tr>';
			}
			$yearview_layout .= '
						</table>		
						

						';
			$count++;	
		}
		$yearview_layout .= '</tr>';
	}
	$yearview_layout .= '</table>';
	return $yearview_layout;
	$cal_log->debug("Exiting getYearViewLayout() method...");
        
	
}

/**
 * Function creates HTML To display events in day view
 * @param  array     $cal         - collection of objects and strings
 * @param  string    $slice       - date:time(eg: 2006-07-13:10)
 * returns string    $eventlayer  - hmtl in string format
 */
function getdayEventLayer(& $cal,$slice,$rows)
{
	global $mod_strings,$cal_log,$listview_max_textlength,$adb,$current_user,$theme;
	$category = getParentTab();
	$cal_log->debug("Entering getdayEventLayer() method...");
	$eventlayer = '';
	$arrow_img_name = '';
	$rows = $rows + 1;
	$last_colwidth = 100 / $rows;
	$width = 100 / $rows ;
	$act = $cal['calendar']->day_slice[$slice]->activities;
	if(!empty($act))
	{
		for($i=0;$i<count($act);$i++)
		{
			$rowspan = 1;
			$arrow_img_name = 'event'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i;
			$subject = $act[$i]->subject;
			$id = $act[$i]->record;
			if($listview_max_textlength && (strlen($subject)>$listview_max_textlength))
				$subject = substr($subject,0,$listview_max_textlength)."...";
			$format = $cal['calendar']->hour_format;
			$duration_hour = $act[$i]->duration_hour;
			$duration_min =$act[$i]->duration_minute;
			$user = $act[$i]->owner;
			$priority = $act[$i]->priority;
			if($duration_min != '00')
				$rowspan = $duration_hour+$rowspan;
			elseif($duration_hour != '0')
			{
				$rowspan = $duration_hour;
			}
			$row_cnt = $rowspan;
			$start_hour = timeString($act[$i]->start_time,$format);
			$end_hour = timeString($act[$i]->end_time,$format);
			$account_name = $act[$i]->accountname;
			$eventstatus = $act[$i]->eventstatus;
			$color = $act[$i]->color;
			$image = vtiger_imageurl($act[$i]->image_name, $theme);
			if($act[$i]->recurring)
				$recurring = '<img src="'.vtiger_imageurl($act[$i]->recurring, $theme).'" align="middle" border="0"></img>';
			else
				$recurring = '&nbsp;';
			$height = $rowspan * 75;
			$javacript_str = '';
			$idShared = "normal"; if($act[$i]->shared) $idShared = "shared";	
			/*if($eventstatus != 'Held')
			{*/
			if($idShared == "normal")
			{
				if(isPermitted("Calendar","EditView") == "yes" || isPermitted("Calendar","Delete") == "yes")
					$javacript_str = 'onMouseOver="cal_show(\''.$arrow_img_name.'\');" onMouseOut="fnHide_Event(\''.$arrow_img_name.'\');"';
				$action_str = '<img src="' . vtiger_imageurl('cal_event.jpg', $theme). '" id="'.$arrow_img_name.'" style="visibility: hidden;" onClick="getcalAction(this,\'eventcalAction\','.$id.',\''.$cal['view'].'\',\''.$cal['calendar']->date_time->hour.'\',\''.$cal['calendar']->date_time->get_formatted_date().'\',\'event\');" align="middle" border="0">';
			}
			else
			{
				$javacript_str = '';
				$eventlayer .= '&nbsp;';
			}
			$eventlayer .= '<td class="dvtCellInfo" rowspan="'.$rowspan.'" colspan="1" width="'.$width.'%" >';
			
			$visibility_query=$adb->pquery('SELECT visibility from vtiger_activity where activityid=?',array($id));
			$visibility = $adb->query_result($visibility_query,0,'visibility');
			$user_query = $adb->pquery("SELECT vtiger_crmentity.smownerid,vtiger_users.user_name from vtiger_crmentity,vtiger_users where crmid=? and vtiger_crmentity.smownerid=vtiger_users.id", array($id));
			$userid = $adb->query_result($user_query,0,"smownerid");
			$assigned_role_query=$adb->pquery("select roleid from vtiger_user2role where userid=?",array($userid));
			$assigned_role_id = $adb->query_result($assigned_role_query,0,"roleid");			
			$role_list = $adb->pquery("SELECT * from vtiger_role WHERE parentrole LIKE '". formatForSqlLike($current_user->column_fields['roleid']) . formatForSqlLike($assigned_role_id) ."'",array());
			$is_shared = $adb->pquery("SELECT * from vtiger_sharedcalendar where userid=? and sharedid=?",array($userid,$current_user->id));
			if(($current_user->column_fields['is_admin']!='on' && $adb->num_rows($role_list)==0 && (($adb->num_rows($is_shared)==0 && ($visibility=='Public' || $visibility=='Private')) || $visibility=='Private')) && $current_user->column_fields['user_name']!=$user)
			{
				$eventlayer .= '<div id="event_'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i.'" class="event" style="height:'.$height.'px;">';
			}
			else{
				$eventlayer .= '<div id="event_'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i.'" class="event" style="height:'.$height.'px;" '.$javacript_str.'>';
			}
			$eventlayer .= '<table border="0" cellpadding="1" cellspacing="0" width="100%">
				<tr>
				<td width="10%" align="center"><img src="'.$image.'" align="middle" border="0"></td>
				<td width="90%"><b>'.$start_hour.' - '.$end_hour.'</b></td></tr>';
			$eventlayer .= '<tr><td align="center">'.$recurring.'</td>';
			
			if(($current_user->column_fields['is_admin']!='on' && $adb->num_rows($role_list)==0 && (($adb->num_rows($is_shared)==0 && ($visibility=='Public' || $visibility=='Private')) || $visibility=='Private')) && $current_user->column_fields['user_name']!=$user)
			{
				$eventlayer .= '<td><font color="silver"><b>'.$user.' - '.$mod_strings['LBL_BUSY'].'</b></font></td>'; 
			}else{
				$eventlayer .= '<td><a href="index.php?action=DetailView&module=Calendar&record='.$id.'&activity_mode=Events&viewtype=calendar&parenttab='.$category.'"><span class="orgTab">'.$subject.'</span></a></td>
				</tr>
				<tr><td align="center">';
				if($act[$i]->shared)
					$eventlayer .= '<img src="' . vtiger_imageurl('cal12x12Shared.gif', $theme). '" align="middle" border="0">';
				else
					$eventlayer .= '&nbsp;';
				$eventlayer .= '</td><td>('.$user.' | '.getTranslatedString($eventstatus).' | '.getTranslatedString($priority).')</td></tr><tr><td align="center">'.$action_str.'</td><td>&nbsp;</td></tr>';
			}			$eventlayer .= '</table></div></td>';
		}
		$eventlayer .= '<td class="dvtCellInfo" rowspan="1" width="'.$last_colwidth.'%">&nbsp;</td>';
	}
	else
	{
		$eventlayer .= '<td class="dvtCellInfo" colspan="'.($rows - 1).'" width="'.($last_colwidth * ($rows - 1)).'%" rowspan="1">&nbsp;</td>';
		$eventlayer .= '<td class="dvtCellInfo" rowspan="1" width="'.$last_colwidth.'%">&nbsp;</td>';
	}
	$cal_log->debug("Exiting getdayEventLayer() method...");
	return $eventlayer;
}

/**
 * Function creates HTML To display events in week view
 * @param  array     $cal         - collection of objects and strings
 * @param  string    $slice       - date:time(eg: 2006-07-13:10)
 * returns string    $eventlayer  - hmtl in string format
 */
function getweekEventLayer(& $cal,$slice)
{
	global $mod_strings,$cal_log,$listview_max_textlength,$adb,$current_user,$theme;
	$category = getParentTab();
	$cal_log->debug("Entering getweekEventLayer() method...");
        $eventlayer = '';
        $arrow_img_name = '';
	$act = $cal['calendar']->week_slice[$slice]->activities;
	if(!empty($act))
        {
		for($i=0;$i<count($act);$i++)
                {
			/* fix given by dartagnanlaf START --integrated by Minnie */
			$arrow_img_name = 'weekevent'.$cal['calendar']->week_slice[$slice]->start_time->get_formatted_date().'_'.$act[$i]->start_time->hour.'_'.$i;
			/* fix given by dartagnanlaf END --integrated by Minnie */
			$id = $act[$i]->record;
                        $subject = $act[$i]->subject;
			if($listview_max_textlength && (strlen($subject)>$listview_max_textlength))
				$subject = substr($subject,0,$listview_max_textlength)."...";
			$format = $cal['calendar']->hour_format;
			$start_hour = timeString($act[$i]->start_time,$format);
                        $end_hour = timeString($act[$i]->end_time,$format);
                        $account_name = $act[$i]->accountname;
			$eventstatus = $act[$i]->eventstatus;
			$user = $act[$i]->owner;
			$priority = $act[$i]->priority;
			$image =  vtiger_imageurl($act[$i]->image_name, $theme);
			$idShared = "normal"; if($act[$i]->shared) $idShared = "shared";
			if($act[$i]->recurring)
				$recurring = '<img src="'.vtiger_imageurl($act[$i]->recurring, $theme).'" align="middle" border="0"></img>';
			else
				$recurring = '&nbsp;';
                        $color = $act[$i]->color;
			if($idShared == "normal")
			{
				if(isPermitted("Calendar","EditView") == "yes" || isPermitted("Calendar","Delete") == "yes")
					$javacript_str = 'onMouseOver="cal_show(\''.$arrow_img_name.'\');" onMouseOut="fnHide_Event(\''.$arrow_img_name.'\');"';
				$action_str = '<img src="' . vtiger_imageurl('cal_event.jpg', $theme). '" id="'.$arrow_img_name.'" style="visibility: hidden;" onClick="getcalAction(this,\'eventcalAction\','.$id.',\''.$cal['view'].'\',\''.$cal['calendar']->date_time->hour.'\',\''.$cal['calendar']->date_time->get_formatted_date().'\',\'event\');" align="middle" border="0">';
			}
			else
			{
				$javacript_str = '';
				$eventlayer .= '&nbsp;';
			}
			
			$visibility_query=$adb->pquery('SELECT visibility from vtiger_activity where activityid=?',array($id));
			$visibility = $adb->query_result($visibility_query,0,'visibility');
			$user_query = $adb->pquery("SELECT vtiger_crmentity.smownerid,vtiger_users.user_name from vtiger_crmentity,vtiger_users where crmid=? and vtiger_crmentity.smownerid=vtiger_users.id", array($id));
			$userid = $adb->query_result($user_query,0,"smownerid");
			$assigned_role_query=$adb->pquery("select roleid from vtiger_user2role where userid=?",array($userid));
			$assigned_role_id = $adb->query_result($assigned_role_query,0,"roleid");			
			$role_list = $adb->pquery("SELECT * from vtiger_role WHERE parentrole LIKE '". formatForSqlLike($current_user->column_fields['roleid']) . formatForSqlLike($assigned_role_id) ."'",array());
			$is_shared = $adb->pquery("SELECT * from vtiger_sharedcalendar where userid=? and sharedid=?",array($userid,$current_user->id));
			if(($current_user->column_fields['is_admin']!='on' && $adb->num_rows($role_list)==0 && (($adb->num_rows($is_shared)==0 && ($visibility=='Public' || $visibility=='Private')) || $visibility=='Private')) && $current_user->column_fields['user_name']!=$user)
			{
				$eventlayer .= '<div id="event_'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i.'" class="event" style="height:'.$height.'px;">';
			}
			else{
				$eventlayer .= '<div id="event_'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i.'" class="event" style="height:'.$height.'px;" '.$javacript_str.'>';
			}
												 
			$eventlayer .='<table border="0" cellpadding="1" cellspacing="0" width="100%">
				<tr>
					<td width="10%" align="center"><img src="'.$image.'" align="middle" border="0"></td>
					<td width="90%"><b>'.$start_hour.' - '.$end_hour.'</b></td>
				</tr>
				<tr>
					<td align="center">'.$recurring.'</td>';
					if(($current_user->column_fields['is_admin']!='on' && $adb->num_rows($role_list)==0 && (($adb->num_rows($is_shared)==0 && ($visibility=='Public' || $visibility=='Private')) || $visibility=='Private')) && $current_user->column_fields['user_name']!=$user)
					{	
						$eventlayer .= '<td><font color="silver"><b>'.$user.'-'.$mod_strings['LBL_BUSY'].'</b></font></td>';
					}else{//CALUSER CUST END							
						$eventlayer .= '<td><a href="index.php?action=DetailView&module=Calendar&record='.$id.'&activity_mode=Events&viewtype=calendar&parenttab='.$category.'"><span class="orgTab">'.$subject.'</span></a></td>
							</tr>
							<tr><td align="center">';
						if($act[$i]->shared)
							$eventlayer .= '<img src="' . vtiger_imageurl('cal12x12Shared.gif', $theme). '" align="middle" border="0">';
						else
							$eventlayer .= '&nbsp;';
						$eventlayer .= '</td><td>('.$user.' | '.getTranslatedString($eventstatus).' | '.getTranslatedString($priority).')</td></tr><tr><td align="center">'.$action_str.'</td><td>&nbsp;</td></tr>';
					}
				$eventlayer .= '</table></div><br>';
                }
		$cal_log->debug("Exiting getweekEventLayer() method...");
		return $eventlayer;
	}
			
}

/**
 * Function creates HTML To display events in month view
 * @param  array     $cal         - collection of objects and strings
 * @param  string    $slice       - date(eg: 2006-07-13)
 * returns string    $eventlayer  - hmtl in string format
 */
function getmonthEventLayer(& $cal,$slice)
{
	global $mod_strings,$cal_log,$adb,$current_user,$theme;
	$category = getParentTab();
	$cal_log->debug("Entering getmonthEventLayer() method...");
	$eventlayer = '';
	$arrow_img_name = '';
	$act = $cal['calendar']->month_array[$slice]->activities;
	if(!empty($act))
        {
		$no_of_act = count($act);
		if($no_of_act>2)
		{
			$act_row = 2;
			$remin_list = $no_of_act - $act_row;
		}
		else
		{
			$act_row = $no_of_act;
			$remin_list = null;
		}
                for($i=0;$i<$act_row;$i++)
                {
                        $arrow_img_name = 'event'.$cal['calendar']->month_array[$slice]->start_time->hour.'_'.$i;
						$id = $act[$i]->record;
                        $subject = $act[$i]->subject;
                        if(strlen($subject)>10)
                                $subject = substr($subject,0,10)."...";
						$format = $cal['calendar']->hour_format;
						$start_hour = timeString($act[$i]->start_time,$format);
                        $end_hour = timeString($act[$i]->end_time,$format);
                        $account_name = $act[$i]->accountname;
                        $image = vtiger_imageurl($act[$i]->image_name, $theme);
						$color = $act[$i]->color;
						//Added for User Based Customview for Calendar Module
						$visibility_query=$adb->pquery('SELECT visibility from vtiger_activity where activityid=?',array($id));
						$visibility = $adb->query_result($visibility_query,0,'visibility');
						$user_query = $adb->pquery("SELECT vtiger_crmentity.smownerid,vtiger_users.user_name from vtiger_crmentity,vtiger_users where crmid=? and vtiger_crmentity.smownerid=vtiger_users.id", array($id));
						$userid = $adb->query_result($user_query,0,"smownerid");
						$username = $adb->query_result($user_query,0,"user_name");
						$assigned_role_query=$adb->pquery("select roleid from vtiger_user2role where userid=?",array($userid));
						$assinged_role_id = $adb->query_result($assigned_role_query,0,"roleid");
						$role_list = $adb->pquery("SELECT * from vtiger_role WHERE parentrole LIKE '". formatForSqlLike($current_user->column_fields['roleid']) . formatForSqlLike($assinged_role_id) ."'",array());
						$is_shared = $adb->pquery("SELECT * from vtiger_sharedcalendar where userid=? and sharedid=?",array($userid,$current_user->id));
						
						if(($current_user->column_fields['is_admin']!='on' && $adb->num_rows($role_list)==0 && (($adb->num_rows($is_shared)==0 && ($visibility=='Public' || $visibility=='Private')) || $visibility=='Private')) && $current_user->id != $userid)
						{			
							$eventlayer .='<div class ="event" id="event_'.$cal['calendar']->month_array[$slice]->start_time->hour.'_'.$i.'">
							<nobr><img src="'.$image.'" border="0"></img>&nbsp;'.$username.' - '.$mod_strings["LBL_BUSY"].'</nobr>
    	                    </div><br>';
						}else{			
							$eventlayer .='<div class ="event" id="event_'.$cal['calendar']->month_array[$slice]->start_time->hour.'_'.$i.'">
							<nobr><img src="'.$image.'" border="0"></img>&nbsp;<a href="index.php?action=DetailView&module=Calendar&record='.$id.'&activity_mode=Events&viewtype=calendar&parenttab='.$category.'"><span class="orgTab">'.$start_hour.' - '.$end_hour.'</span></a></nobr>
    	                    </div><br>';
						}
				}
					
			if($remin_list != null)
			{
				$eventlayer .='<div valign=bottom align=right width=10%>
				<a href="index.php?module=Calendar&action=index&view='.$cal['calendar']->month_array[$slice]->getView().'&'.$cal['calendar']->month_array[$slice]->start_time->get_date_str().'&parenttab='.$category.'" class="webMnu">
				+'.$remin_list.'&nbsp;'.$mod_strings['LBL_MORE'].'</a></div>';
			}
			$cal_log->debug("Exiting getmonthEventLayer() method...");
            return $eventlayer;
        }
}

/**
 * Function to get events list scheduled between specified dates
 * @param array   $calendar              -  collection of objects and strings
 * @param string  $start_date            -  date string
 * @param string  $end_date              -  date string
 * @param string  $info                  -  string 'listcnt' or empty string. if 'listcnt' means it returns no. of events and no. of pending events in array format else it returns events list in array format
 * return array  $Entries               -  eventslists in array format
 */
function getEventList(& $calendar,$start_date,$end_date,$info='')
{
	global $log,$theme;
	$Entries = Array();
	$category = getParentTab();
	global $adb,$current_user,$mod_strings,$app_strings,$cal_log,$listview_max_textlength,$list_max_entries_per_page;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
        require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	$cal_log->debug("Entering getEventList() method...");

	//modified query to fix the ticket #5014 & #5180
	$and = "AND (((vtiger_activity.date_start >= ? AND vtiger_activity.due_date <= ?)
		AND (vtiger_recurringevents.activityid is NULL))
		OR (vtiger_recurringevents.recurringdate BETWEEN ? AND ?))";

	$query = "SELECT vtiger_groups.groupname, vtiger_users.user_name,vtiger_crmentity.smownerid, vtiger_crmentity.crmid,
       		vtiger_activity.* FROM vtiger_activity
		INNER JOIN vtiger_crmentity
			ON vtiger_crmentity.crmid = vtiger_activity.activityid
		LEFT JOIN vtiger_groups
			ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_users
	       		ON vtiger_users.id = vtiger_crmentity.smownerid 
		LEFT OUTER JOIN vtiger_recurringevents
			ON vtiger_recurringevents.activityid = vtiger_activity.activityid
		WHERE vtiger_crmentity.deleted = 0
			AND (vtiger_activity.activitytype not in ('Emails','Task')) $and ";

        $list_query = $query." AND vtiger_crmentity.smownerid = "  . $current_user->id;

	// User Select Customization: Changes should made also in (Appointment::readAppointment)
	$query_filter_prefix = calendarview_getSelectedUserFilterQuerySuffix();
	$query .= $query_filter_prefix;
	$count_query .= $query_filter_prefix;
	// END

	$params = $info_params = array($start_date, $end_date, $start_date, $end_date);
	if($info != '')
	{
		$groupids = explode(",", fetchUserGroupids($current_user->id)); // Explode can be removed, once implode is removed from fetchUserGroupids
		if (count($groupids) > 0) {

			$com_q = " AND (vtiger_crmentity.smownerid = ?
					OR vtiger_groups.groupid in (". generateQuestionMarks($groupids) ."))
					GROUP BY vtiger_activity.activityid";
		} else {			
			$com_q = " AND vtiger_crmentity.smownerid = ?
				GROUP BY vtiger_activity.activityid";
		}
			
		$pending_query = $query." AND (vtiger_activity.eventstatus = 'Planned')".$com_q;
		$total_q =  $query."".$com_q;
		array_push($info_params, $current_user->id);
		
		if (count($groupids) > 0) {
			array_push($info_params, $groupids);
		}
		
		$total_res = $adb->pquery($total_q, $info_params);
		$total = $adb->num_rows($total_res);

		$res = $adb->pquery($pending_query, $info_params);
		$pending_rows = $adb->num_rows($res);
		$cal_log->debug("Exiting getEventList() method...");
		return Array('totalevent'=>$total,'pendingevent'=>$pending_rows);
	}
	if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[16] == 3)
	{
		$sec_parameter=getCalendarViewSecurityParameter();
		$query .= $sec_parameter;
	}
	if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'search')
	{
		$search_where = calendar_search_where($_REQUEST['field_name'],$_REQUEST['search_option'],$_REQUEST['search_text']);
		$group_cond .= $search_where;
	}
	$group_cond .= " GROUP BY vtiger_activity.activityid ORDER BY vtiger_activity.date_start,vtiger_activity.time_start ASC";

	//Ticket 6476
	if(PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true){
		$count_result = $adb->pquery( mkCountQuery( $query),$params);
		$noofrows = $adb->query_result($count_result,0,"count");
	}else{
		$noofrows = null;
	}
	global $currentModule;
	$queryMode = (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true');
	//$viewid is used as a key for cache query and other info so pass the dates as viewid
	$viewid = $start_date.$end_date;
	$start = ListViewSession::getRequestCurrentPage($currentModule, $adb->convert2sql($query,
			$params), $viewid, $queryMode);

	$navigation_array = VT_getSimpleNavigationValues($start,$list_max_entries_per_page,$noofrows);

	//end

	$start_rec = ($start-1) * $list_max_entries_per_page;
	$end_rec = $navigation_array['end_val'];
        //print_r($navigation_array);die();
        //echo $end_rec.'val';
	
        $list_query = $adb->convert2Sql($query, $params);
	$_SESSION['Calendar_listquery'] = $list_query;
        
        if($start_rec < 0)
		$start_rec = 0;
	$query .= $group_cond." limit $start_rec,$list_max_entries_per_page";

 	if( $adb->dbType == "pgsql"){
 	    $query = fixPostgresQuery($query, $log, 0);
 	}

	
        
	$result = $adb->pquery($query, $params);
	$rows = $adb->num_rows($result);
	$c = 0;
	if($start > 1)
		$c = ($start-1) * $list_max_entries_per_page;
	for($i=0;$i<$rows;$i++)
	{
		$element = Array();
		$element['no'] = $c+1;
		$image_tag = "";
		$contact_data = "";
		$more_link = "";
		$start_time = $adb->query_result($result,$i,"time_start");
		$end_time = $adb->query_result($result,$i,"time_end");
		$format = $calendar['calendar']->hour_format;
		$value = getaddEventPopupTime($start_time,$end_time,$format);
		$start_hour = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
		$end_hour = $value['endhour'] .':'.$value['endmin'].''.$value['endfmt'];
		$date_start = $adb->query_result($result,$i,"date_start");
		$due_date = $adb->query_result($result,$i,"due_date");
		$element['starttime'] = getDisplayDate($date_start).' '.$start_hour;
		$element['endtime'] = getDisplayDate($due_date).' '.$end_hour;
		$contact_id = $adb->query_result($result,$i,"contactid");
		$id = $adb->query_result($result,$i,"activityid");
		$subject = $adb->query_result($result,$i,"subject");
		$eventstatus = $adb->query_result($result,$i,"eventstatus");
		$assignedto = $adb->query_result($result,$i,"user_name");
		$userid = $adb->query_result($result,$i,"smownerid");
		$idShared = "normal";
		if(!empty($assignedto) && $userid != $current_user->id && $adb->query_result($result,$i,"visibility") == "Public")
		{
			$que = "select * from vtiger_sharedcalendar where sharedid=? and userid=?";
			$row = $adb->pquery($que, array($current_user->id, $userid));
			$no = $adb->getRowCount($row);
			if($no > 0) $idShared = "shared";
			else  $idShared = "normal";
				

		}
                if($listview_max_textlength && (strlen($subject)>$listview_max_textlength))
	                $subject = substr($subject,0,$listview_max_textlength)."...";
		if($contact_id != '')
		{
			$contactname = getContactName($contact_id);
			$contact_data = "<b>".$contactname."</b>,";
		}
		$more_link = "<a href='index.php?action=DetailView&module=Calendar&record=".$id."&activity_mode=Events&viewtype=calendar&parenttab=".$category."' class='webMnu'>[".$mod_strings['LBL_MORE']."...]</a>";
		$type = $adb->query_result($result,$i,"activitytype");
		if($type == 'Call')
			$image_tag = "<img src='" . vtiger_imageurl('Calls.gif', $theme). "' align='middle'>&nbsp;".$app_strings['Call'];
		else if($type == 'Meeting')
			$image_tag = "<img src='" . vtiger_imageurl('Meetings.gif', $theme). "' align='middle'>&nbsp;".$app_strings['Meeting'];
		else
			$image_tag = "&nbsp;".getTranslatedString($type);
        	$element['eventtype'] = $image_tag;
		$element['eventdetail'] = $contact_data." ".$subject."&nbsp;".$more_link;
		/*if(getFieldVisibilityPermission('Events',$current_user->id,'parent_id') == '0')
		{
			$element['relatedto']= getRelatedTo('Calendar',$result,$i);
		}*/
		if($idShared == "normal")
		{
			if(isPermitted("Calendar","EditView") == "yes" || isPermitted("Calendar","Delete")=="yes")
				$element['action'] ="<img onClick='getcalAction(this,\"eventcalAction\",".$id.",\"".$calendar['view']."\",\"".$calendar['calendar']->date_time->hour."\",\"".$calendar['calendar']->date_time->get_formatted_date()."\",\"event\");' src='" . vtiger_imageurl('cal_event.jpg', $theme). "' border='0'>";
		}
		else
		{
			if(isPermitted("Calendar","EditView") == "yes" || isPermitted("Calendar","Delete")=="yes")
				$element['action']="<img onClick=\"alert('".$mod_strings["SHARED_EVENT_DEL_MSG"]."')\"; src='" . vtiger_imageurl('cal_event.jpg', $theme). "' border='0'>";
		}
		if(getFieldVisibilityPermission('Events',$current_user->id,'eventstatus') == '0')
		{
			if(!$is_admin && $eventstatus != '')
			{
				$roleid=$current_user->roleid;
				$roleids = Array();
				$subrole = getRoleSubordinates($roleid);
				if(count($subrole)> 0)
				$roleids = $subrole;
				array_push($roleids, $roleid);

				//here we are checking wheather the table contains the sortorder column .If  sortorder is present in the main picklist table, then the role2picklist will be applicable for this table...

				$sql="select * from vtiger_eventstatus where eventstatus=?";
				$res = $adb->pquery($sql,array(decode_html($eventstatus)));
				$picklistvalueid = $adb->query_result($res,0,'picklist_valueid');
				if ($picklistvalueid != null) {
					$pick_query="select * from vtiger_role2picklist where picklistvalueid=$picklistvalueid and roleid in (". generateQuestionMarks($roleids) .")";
					$res_val=$adb->pquery($pick_query,array($roleids));
					$num_val = $adb->num_rows($res_val);
				}
				if($num_val > 0)
				$element['status'] = getTranslatedString(decode_html($eventstatus));
				else
				$element['status'] = "<font color='red'>".$app_strings['LBL_NOT_ACCESSIBLE']."</font>";


			}else
			$element['status'] = getTranslatedString(decode_html($eventstatus));
		}
		if(!empty($assignedto))
			$element['assignedto'] = $assignedto;
		else
			$element['assignedto'] = $adb->query_result($result,$i,"groupname");
		$element['visibility'] = $adb->query_result($result,$i,"visibility");
		$c++;
		$Entries[] = $element;
	}
	$ret_arr[0] = $Entries;
        $ret_arr[1] = $navigation_array;
	$cal_log->debug("Exiting getEventList() method...");
	return $ret_arr;
}

/**
 * Function to get todos list scheduled between specified dates
 * @param array   $calendar              -  collection of objects and strings
 * @param string  $start_date            -  date string
 * @param string  $end_date              -  date string
 * @param string  $info                  -  string 'listcnt' or empty string. if 'listcnt' means it returns no. of todos and no. of pending todos in array format else it returns todos list in array format
 * return array   $Entries               -  todolists in array format
 */
function getTodoList(& $calendar,$start_date,$end_date,$info='')
{
	global $log,$app_strings,$theme;
        $Entries = Array();
	$category = getParentTab();
	global $adb,$current_user,$mod_strings,$cal_log,$list_max_entries_per_page;
	$cal_log->debug("Entering getTodoList() method...");
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

   $query = "SELECT vtiger_groups.groupname, vtiger_users.user_name, vtiger_crmentity.crmid, vtiger_cntactivityrel.contactid,
				vtiger_activity.* FROM vtiger_activity
                INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid = vtiger_activity.activityid
                LEFT JOIN vtiger_cntactivityrel
					ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users
					ON vtiger_users.id = vtiger_crmentity.smownerid";
	$query .= getNonAdminAccessControlQuery('Calendar',$current_user);
	$query .= "WHERE vtiger_crmentity.deleted = 0 AND vtiger_activity.activitytype = 'Task'".
					" AND (vtiger_activity.date_start BETWEEN ? AND ?)";

      $list_query = $query." AND vtiger_crmentity.smownerid = "  . $current_user->id;
	// User Select Customization
	/*$only_for_user = calendarview_getSelectedUserId();
	if($only_for_user != 'ALL') {
		$query .= " AND vtiger_crmentity.smownerid = "  . $only_for_user;
		$count_qry .= " AND vtiger_crmentity.smownerid = "  . $only_for_user;
	}*/
	// END
		$params = $info_params = array($start_date, $end_date);
        if($info != '')
		{
			//added to fix #4816
			$groupids = explode(",", fetchUserGroupids($current_user->id));
			if (count($groupids) > 0) {
				$com_q = " AND (vtiger_crmentity.smownerid = ?
					OR vtiger_groups.groupid in (". generateQuestionMarks($groupids) ."))";
			} else {			
				$com_q = " AND vtiger_crmentity.smownerid = ?";
			}
			//end

			$pending_query = $query." AND (vtiger_activity.status != 'Completed')".$com_q;
			$total_q =  $query."".$com_q;
			array_push($info_params, $current_user->id);

			if (count($groupids) > 0) {
				array_push($info_params, $groupids);
			}	

			if( $adb->dbType == "pgsql")
			{
 		    	$pending_query = fixPostgresQuery( $pending_query, $log, 0);
		    	$total_q = fixPostgresQuery( $total_q, $log, 0);
			}
			$total_res = $adb->pquery($total_q, $info_params);
			$total = $adb->num_rows($total_res);
                
			$res = $adb->pquery($pending_query, $info_params);
		        $pending_rows = $adb->num_rows($res);
		
			$cal_log->debug("Exiting getTodoList() method...");
			return Array('totaltodo'=>$total,'pendingtodo'=>$pending_rows);
        }
	
	$group_cond = '';
   	$group_cond .= " ORDER BY vtiger_activity.date_start,vtiger_activity.time_start ASC";
	if(isset($_REQUEST['start']) && $_REQUEST['start'] != '')
		$start = vtlib_purify($_REQUEST['start']);
	else 
		$start = 1;

//T6477 changes
	if(PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true){
	    $count_res = $adb->pquery(mkCountQuery($query), $params);
		  $total_rec_count = $adb->query_result($count_res,0,'count');
	}else{
		$total_rec_count = null;
	}

	$navigation_array = VT_getSimpleNavigationValues($start,$list_max_entries_per_page,$total_rec_count);

	$start_rec = ($start-1) * $list_max_entries_per_page;
	$end_rec = $navigation_array['end_val'];
        
        $list_query = $adb->convert2Sql($query, $params);
	$_SESSION['Calendar_listquery'] = $list_query;

	if($start_rec < 0)
		$start_rec = 0;

	//ends
	$query .= $group_cond." limit $start_rec,$list_max_entries_per_page";

	if( $adb->dbType == "pgsql"){
 	    $query = fixPostgresQuery( $query, $log, 0);
	}

    $result = $adb->pquery($query, $params);
    $rows = $adb->num_rows($result);
	$c=0;
	if($start > 1)
		$c = ($start-1) * $list_max_entries_per_page;
	for($i=0;$i<$rows;$i++)
        {
		
                $element = Array();
		$contact_name = '';
                $element['no'] = $c+1;
                $more_link = "";
                $start_time = $adb->query_result($result,$i,"time_start");
                $format = $calendar['calendar']->hour_format;
		$value = getaddEventPopupTime($start_time,$start_time,$format);
                $element['starttime'] = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
		$date_start = $adb->query_result($result,$i,"date_start");
		$due_date = $adb->query_result($result,$i,"due_date");
		if($calendar['view'] != 'day')
			$element['startdate'] = getDisplayDate($date_start);
		$element['duedate'] = getDisplayDate($due_date);

                $id = $adb->query_result($result,$i,"activityid");
                $subject = $adb->query_result($result,$i,"subject");
		//CHANGE : TO IMPROVE PERFORMANCE
		/*$contact_id = $adb->query_result($result,$i,"contactid");
		if($contact_id!='')
		{
			$contact_name = getContactName($contact_id);
		}*/
		
		$more_link = "<a href='index.php?action=DetailView&module=Calendar&record=".$id."&activity_mode=Task&viewtype=calendar&parenttab=".$category."' class='webMnu'>".$subject."</a>";
		$element['tododetail'] = $more_link;
		/*if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id') == '0')
		{
			$element['task_relatedto'] = getRelatedTo('Calendar',$result,$i);
		}
		if(getFieldVisibilityPermission('Calendar',$current_user->id,'contact_id') == '0')
		{
			$element['task_contact'] = "<a href=\"index.php?module=Contacts&action=DetailView&record=".$contact_id."\">".$contact_name."</a>";
		}*/
		if(getFieldVisibilityPermission('Calendar',$current_user->id,'taskstatus') == '0')
		{
			$taskstatus = $adb->query_result($result,$i,"status");

			if(!$is_admin && $taskstatus != '')
			{
				$roleid=$current_user->roleid;
				$roleids = Array();
				$subrole = getRoleSubordinates($roleid);
				if(count($subrole)> 0)
				$roleids = $subrole;
				array_push($roleids, $roleid);

				//here we are checking wheather the table contains the sortorder column .If  sortorder is present in the main picklist table, then the role2picklist will be applicable for this table...

				$sql="select * from vtiger_taskstatus where taskstatus=?";
				$res = $adb->pquery($sql,array(decode_html($taskstatus)));
				$picklistvalueid = $adb->query_result($res,0,'picklist_valueid');
				if ($picklistvalueid != null) {
					$pick_query="select * from vtiger_role2picklist where picklistvalueid=$picklistvalueid and roleid in (". generateQuestionMarks($roleids) .")";
					$res_val=$adb->pquery($pick_query,array($roleids));
					$num_val = $adb->num_rows($res_val);
				}
				if($num_val > 0)
				$element['status'] = getTranslatedString(decode_html($taskstatus));
				else
				$element['status'] = "<font color='red'>".$app_strings['LBL_NOT_ACCESSIBLE']."</font>";


			}else
			$element['status'] = getTranslatedString(decode_html($taskstatus));
			
			
		}
		if(isPermitted("Calendar","EditView") == "yes" || isPermitted("Calendar","Delete") == "yes")
			$element['action'] ="<img onClick='getcalAction(this,\"taskcalAction\",".$id.",\"".$calendar['view']."\",\"".$calendar['calendar']->date_time->hour."\",\"".$calendar['calendar']->date_time->get_formatted_date()."\",\"todo\");' src='" . vtiger_imageurl('cal_event.jpg', $theme). "' border='0'>";
		$assignedto = $adb->query_result($result,$i,"user_name");
		if(!empty($assignedto))
			$element['assignedto'] = $assignedto;
		else
			$element['assignedto'] = $adb->query_result($result,$i,"groupname");
		$c++;
		$Entries[] = $element;
	}
	$ret_arr[0] = $Entries;
        $ret_arr[1] = $navigation_array;
	$cal_log->debug("Exiting getTodoList() method...");
	return $ret_arr;
}

/**
 * Function to get number of Events and Todos Info
 * @param array    $cal              - collection of objects and strings 
 * @param string   $mode             - string 'listcnt' or may be empty. if empty means get Events/Todos ListView else get total events/todos and no. of pending events/todos Info.
 * return array    $event_todo_info  - collection of events/todos info.
 */
function getEventInfo(& $cal, $mode)
{
	global $mod_strings,$cal_log;
	$cal_log->debug("Entering getEventInfo() method...");
	$event = Array();
	$event['event']=getEventListView($cal, $mode);
	$event_info = "";
	$event_info .= $mod_strings['LBL_TOTALEVENTS']."&nbsp;".$event['event']['totalevent'];
	if($event['event']['pendingevent'] != null)
		 $event_info .= ", ".$event['event']['pendingevent']."&nbsp;".$mod_strings['LBL_PENDING'];
	$cal_log->debug("Exiting getEventInfo() method...");
	
	return $event_info;
}

function getTodoInfo(& $cal, $mode)
{
        global $mod_strings,$cal_log;
        $cal_log->debug("Entering getTodoInfo() method...");
        $todo = Array();
        $todo['todo'] = getTodosListView($cal, $mode);
        $todo_info = "";
        $todo_info .=$mod_strings['LBL_TOTALTODOS']."&nbsp;".$todo['todo']['totaltodo'];
        if($todo['todo']['pendingtodo'] != null)
                $todo_info .= ", ".$todo['todo']['pendingtodo']."&nbsp;".$mod_strings['LBL_PENDING'];
        $cal_log->debug("Exiting getTodoInfo() method...");

        return $todo_info;
}


/**
 * Function creates HTML to display Events ListView
 * @param array  $entry_list    - collection of strings(Event Information)
 * return string $list_view     - html tags in string format
 */
function constructEventListView(& $cal,$entry_list,$navigation_array='')
{
	global $mod_strings,$app_strings,$adb,$cal_log,$current_user,$theme;
	$cal_log->debug("Entering constructEventListView() method...");
	$format = $cal['calendar']->hour_format;
	$date_format = $current_user->date_format;
	$hour_startat = timeString(array('hour'=>date('H:i'),'minute'=>0),'24');
        $hour_endat = timeString(array('hour'=>date('H:i',(time() + (60 * 60))),'minute'=>0),'24');
	$time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
	$temp_ts = $cal['calendar']->date_time->ts;
	//to get date in user selected date format
	$temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
	if($cal['calendar']->day_start_hour != 23)
		$endtemp_date = $temp_date;
	else
	{
		$endtemp_ts = $temp_ts + (1 * 24 * 60 * 60);
		$endtemp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$endtemp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$endtemp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $endtemp_ts)):(''))));
	}
	$list_view = "";
	$start_datetime = $app_strings['LBL_START_DATE_TIME'];
	$end_datetime = $app_strings['LBL_END_DATE_TIME'];
	//Events listview header labels
	$header = Array('0'=>'#',
                        '1'=>$start_datetime,
                        '2'=>$end_datetime,
                        '3'=>$mod_strings['LBL_EVENTTYPE'],
                        '4'=>$mod_strings['LBL_EVENTDETAILS']
			);
	$header_width = Array('0'=>'5%',
			      '1'=>'15%',
			      '2'=>'15%',
			      '3'=>'10%',
			      '4'=>'33%'
		             );
	/*if(getFieldVisibilityPermission('Events',$current_user->id,'parent_id') == '0')
	{
		array_push($header,$mod_strings['LBL_RELATEDTO']);
		array_push($header_width,'15%');
	}*/
	if(isPermitted("Calendar","EditView") == "yes" || isPermitted("Calendar","Delete") == "yes")
	{
		array_push($header,$mod_strings['LBL_ACTION']);
		 array_push($header_width,'10%');
	}
	if(getFieldVisibilityPermission('Events',$current_user->id,'eventstatus') == '0')
	{
		array_push($header,$mod_strings['LBL_STATUS']);
		array_push($header_width,'$10%');
	}
	array_push($header,$mod_strings['LBL_ASSINGEDTO']);
	array_push($header_width,'15%');
	
        $list_view .="<table style='background-color: rgb(204, 204, 204);' class='small' align='center' border='0' cellpadding='5' cellspacing='1' width='98%'>
                        <tr>";
	$header_rows = count($header);

	$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string,"Calendar","index");

	if($navigationOutput != '')
	{
		$list_view .= "<tr width=100% bgcolor=white><td align=center colspan=$header_rows>";
		$list_view .= "<table align=center width='98%'><tr>".$navigationOutput."</tr></table></td></tr>";
	}
	$list_view .= "<tr>";
        for($i=0;$i<$header_rows;$i++)
        {
                $list_view .="<td nowrap='nowrap' class='lvtCol' width='".$header_width[$i]."'>".$header[$i]."</td>";
        }
        $list_view .="</tr>";
	$rows = count($entry_list);
	if($rows != 0)
	{
		for($i=0;$i<count($entry_list);$i++)
		{
			$list_view .="<tr class='lvtColData' onmouseover='this.className=\"lvtColDataHover\"' onmouseout='this.className=\"lvtColData\"' bgcolor='white'>";
	
			foreach($entry_list[$i] as $key=>$entry)
			{	
				$assigned_role_query=$adb->pquery("select vtiger_user2role.roleid,vtiger_user2role.userid from vtiger_user2role INNER JOIN vtiger_users where vtiger_users.id=vtiger_user2role.userid and vtiger_users.user_name=?",array($entry_list[$i]['assignedto']));
				$assigned_user_role_id = $adb->query_result($assigned_role_query,0,"roleid");
				$assigned_user_id = $adb->query_result($assigned_role_query,0,"userid");
				$role_list = $adb->pquery("SELECT * from vtiger_role WHERE parentrole LIKE '". formatForSqlLike($current_user->column_fields['roleid']) . formatForSqlLike($assigned_user_role_id) ."'",array());
				$is_shared = $adb->pquery("SELECT * from vtiger_sharedcalendar where userid=? and sharedid=?",array($assigned_user_id,$current_user->id));
				if($key!='visibility'){
					if(($key=='eventdetail'|| $key=='action') && ($current_user->column_fields['is_admin']!='on' && $adb->num_rows($role_list)==0 && (($adb->num_rows($is_shared)==0 && ($entry_list[$i]['visibility']=='Public' || $entry_list[$i]['visibility']=='Private')) || $entry_list[$i]['visibility']=='Private')) && $current_user->column_fields['user_name']!=$entry_list[$i]['assignedto']){
						if($key=='eventdetail')
							$list_view .="<td nowrap='nowrap'><font color='red'><b>".$entry_list[$i]['assignedto']." - ".$mod_strings['LBL_BUSY']."</b></font></td>";
						else
							$list_view .="<td nowrap='nowrap'><font color='red'>".$app_strings['LBL_NOT_ACCESSIBLE']."</font></td>";
					}else
						$list_view .="<td nowrap='nowrap'>$entry</td>";
				}
			}
			$list_view .="</tr>";
		}
	}
	else
	{
		$list_view .="<tr><td style='background-color:#efefef;height:340px' align='center' colspan='9'>
				";
			$list_view .="<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative; z-index: 5000;'>
					<table border='0' cellpadding='5' cellspacing='0' width='98%'>
						<tr>
							<td rowspan='2' width='25%'>
								<img src='" . vtiger_imageurl('empty.jpg', $theme). "' height='60' width='61'></td>
							<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='75%'><span class='genHeaderSmall'>".$app_strings['LBL_NO']." ".$app_strings['Events']." ".$app_strings['LBL_FOUND']." !</span></td>
						</tr>
						<tr>";
			//checking permission for Create/Edit Operation
			if(isPermitted("Calendar","EditView") == "yes")
                        {
                                $list_view .="<td class='small' align='left' nowrap='nowrap'>".$app_strings['LBL_YOU_CAN_CREATE']."&nbsp;".$app_strings['LBL_AN']."&nbsp;".$app_strings['Event']."&nbsp;".$app_strings['LBL_NOW'].".&nbsp;".$app_strings['LBL_CLICK_THE_LINK'].":<br>
					&nbsp;&nbsp;-<a href='javascript:void(0);' onClick='gshow(\"addEvent\",\"Call\",\"".$temp_date."\",\"".$endtemp_date."\",\"".$time_arr['starthour']."\",\"".$time_arr['startmin']."\",\"".$time_arr['startfmt']."\",\"".$time_arr['endhour']."\",\"".$time_arr['endmin']."\",\"".$time_arr['endfmt']."\",\"listview\",\"event\");'>".$app_strings['LBL_CREATE']."&nbsp;".$app_strings['LBL_AN']."&nbsp;".$app_strings['Event']."</a><br>
					</td>";
			}
			else
			{
				$list_view .="<td class='small' align='left' nowrap='nowrap'>".$app_strings['LBL_YOU_ARE_NOT_ALLOWED_TO_CREATE']."&nbsp;".$app_strings['LBL_AN']."&nbsp;".$app_strings['Event']."<br></td>";
			}
			$list_view .="</tr>
                                        </table>
				</div>";
			$list_view .="</td></tr>";			
	}
	$list_view .="</table>";
	$cal_log->debug("Exiting constructEventListView() method...");
	return $list_view;
}

/**
 * Function creates HTML to display Todos ListView
 * @param array  $todo_list     - collection of strings(Todo Information)
 * @param array  $cal           - collection of objects and strings 
 * return string $list_view     - html tags in string format
 */
function constructTodoListView($todo_list,$cal,$subtab,$navigation_array='')
{
	global $mod_strings,$cal_log,$adb,$theme;
	$cal_log->debug("Entering constructTodoListView() method...");
        global $current_user,$app_strings;
        $date_format = $current_user->date_format;
        $format = $cal['calendar']->hour_format;
	$hour_startat = timeString(array('hour'=>date('H:i'),'minute'=>0),'24');
        $hour_endat = timeString(array('hour'=>date('H:i',(time() + (60 * 60))),'minute'=>0),'24');
        $time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
        $temp_ts = $cal['calendar']->date_time->ts;
	//to get date in user selected date format
        $temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
	if($cal['calendar']->day_start_hour != 23)
		$endtemp_date = $temp_date;
	else
	{
		$endtemp_ts = $temp_ts + (1 * 24 * 60 * 60);
		$endtemp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$endtemp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$endtemp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $endtemp_ts)):(''))));
	}
        $list_view = "";
	//labels of listview header
	if($cal['view'] == 'day')
	{
		$colspan = 9;
		$header = Array('0'=>'#','1'=>$mod_strings['LBL_TIME'],'2'=>$mod_strings['LBL_LIST_DUE_DATE'],
				'3'=>$mod_strings['LBL_TODO']);
		$header_width = Array('0'=>'5%','1'=>'10%','2'=>'10%','3'=>'38%',);		
		/*if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id') == '0')
		{
			array_push($header,$mod_strings['LBL_RELATEDTO']);
			array_push($header_width,'15%');
		}
		if(getFieldVisibilityPermission('Calendar',$current_user->id,'contact_id') == '0')
		{
			array_push($header,$mod_strings['LBL_CONTACT_NAME']);
			array_push($header_width,'15%');
		}*/
		if(getFieldVisibilityPermission('Calendar',$current_user->id,'taskstatus') == '0')
		{
			array_push($header,$mod_strings['LBL_STATUS']);
			array_push($header_width,'10%');
		}

		if(isPermitted("Calendar","EditView") == "yes" || isPermitted("Calendar","Delete") == "yes")
		{
			array_push($header,$mod_strings['LBL_ACTION']);
			array_push($header_width,'10%');
		}
		array_push($header,$mod_strings['LBL_ASSINGEDTO']);
		array_push($header_width,'15%');
	}
	else
	{
		$colspan = 10;
	        $header = Array('0'=>'#',
                        '1'=>$mod_strings['LBL_TIME'],
			'2'=>$mod_strings['LBL_START_DATE'],
			'3'=>$mod_strings['LBL_DUE_DATE'],
                        '4'=>$mod_strings['LBL_TODO']
			);
		$header_width = Array('0'=>'5%',
			'1'=>'10%',
			'2'=>'10%',
			'3'=>'10%',
			'4'=>'28%'
			);
		/*if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id') == '0')
		{
			array_push($header,$mod_strings['LBL_RELATEDTO']);
			array_push($header_width,'15%');
		}
		if(getFieldVisibilityPermission('Calendar',$current_user->id,'contact_id') == '0')
		{
			array_push($header,$mod_strings['LBL_CONTACT_NAME']);
			array_push($header_width,'15%');
		}*/
		if(getFieldVisibilityPermission('Calendar',$current_user->id,'taskstatus') == '0')
		{
			array_push($header,$mod_strings['LBL_STATUS']);
			array_push($header_width,'10%');
		}
		if(isPermitted("Calendar","EditView") == "yes" || isPermitted("Calendar","Delete") == "yes")
		{
			array_push($header,$mod_strings['LBL_ACTION']);
		}
		array_push($header,$mod_strings['LBL_ASSINGEDTO']);
		array_push($header_width,'15%');
		
	}
	if($current_user->column_fields['is_admin']=='on')
		$Res = $adb->pquery("select * from vtiger_activitytype",array());
	else
	{
		$roleid=$current_user->roleid;
		$subrole = getRoleSubordinates($roleid);
		if(count($subrole)> 0)
		{
			$roleids = $subrole;
			array_push($roleids, $roleid);
		}
		else
		{	
			$roleids = $roleid;
		}

		if (count($roleids) > 1) {
			$Res=$adb->pquery("select distinct activitytype from  vtiger_activitytype inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_activitytype.picklist_valueid where roleid in (". generateQuestionMarks($roleids) .") and picklistid in (select picklistid from vtiger_activitytype) order by sortid asc",array($roleids));
		} else {
			$Res=$adb->pquery("select distinct activitytype from vtiger_activitytype inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_activitytype.picklist_valueid where roleid = ? and picklistid in (select picklistid from vtiger_activitytype) order by sortid asc",array($roleid));
		}
	}
	$eventlist='';
	for($i=0; $i<$adb->num_rows($Res);$i++)
	{
		$eventlist .= $adb->query_result($Res,$i,'activitytype').";";
	}
	
	$list_view .="<table align='center' border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tr><td colspan='3'>&nbsp;</td></tr>";
			//checking permission for Create/Edit Operation
			if(isPermitted("Calendar","EditView") == "yes")
			{
			$list_view .="<tr>
				<td class='calAddButton' onMouseOver='fnAddEvent(this,\"addEventDropDown\",\"".$temp_date."\",\"".$endtemp_date."\",\"".$time_arr['starthour']."\",\"".$time_arr['startmin']."\",\"".$time_arr['startfmt']."\",\"".$time_arr['endhour']."\",\"".$time_arr['endmin']."\",\"".$time_arr['endfmt']."\",\"\",\"".$subtab."\",\"".$eventlist."\");'style='border: 1px solid #666666;cursor:pointer;height:30px' align='center' width='10%'>
                                        ".$mod_strings['LBL_ADD']."
                                        <img src='".vtiger_imageurl('menuDnArrow.gif', $theme)."' style='padding-left: 5px;' border='0'>                                                                                                                         </td>";
			}
			else
			{
				$list_view .="<tr><td>&nbsp;</td>";
			}
			$list_view .="<td align='center' width='60%'><span  id='total_activities'>".getTodoInfo($cal,'listcnt')."</span>&nbsp;</td>
				<td align='right' width='28%'>&nbsp;</td>
			</tr>
		</table>

			<br><table style='background-color: rgb(204, 204, 204);' class='small' align='center' border='0' cellpadding='5' cellspacing='1' width='98%'>
                        ";
	$header_rows = count($header);
	$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string,"Calendar","index");

	if($navigationOutput != '')
	{
		$list_view .= "<tr width=100% bgcolor=white><td align=center colspan=$header_rows>";
		$list_view .= "<table align=center width='98%'><tr>".$navigationOutput."</tr></table></td></tr>";
	}
	$list_view .= "<tr>";
        for($i=0;$i<$header_rows;$i++)
        {
                $list_view .="<td class='lvtCol' width='".$header_width[$i]."' nowrap='nowrap'>".$header[$i]."</td>";
        }
        $list_view .="</tr>";
	$rows = count($todo_list);
        if($rows != 0)
        {
                for($i=0;$i<count($todo_list);$i++)
                {
                        $list_view .="<tr style='height: 25px;' bgcolor='white'>";
                        foreach($todo_list[$i] as $key=>$entry)
                        {
                                $list_view .="<td>".$entry."</td>";
                        }
                        $list_view .="</tr>";
                }
        }
        else
        {
		$list_view .="<tr><td style='background-color:#efefef;height:340px' align='center' colspan='".$colspan."'>";
		$list_view .="<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative; z-index: 5000;'>
			<table border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tr>
				<td rowspan='2' width='25%'>
					<img src='" . vtiger_imageurl('empty.jpg', $theme). "' height='60' width='61'></td>
				<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='75%'><span class='genHeaderSmall'>".$app_strings['LBL_NO']." ".$app_strings['Todos']." ".$app_strings['LBL_FOUND']." !</span></td>
			</tr>
			<tr>";
		//checking permission for Create/Edit Operation
		if(isPermitted("Calendar","EditView") == "yes")
		{
			$list_view .="<td class='small' align='left' nowrap='nowrap'>".$app_strings['LBL_YOU_CAN_CREATE']."&nbsp;".$app_strings['LBL_A']."&nbsp;".$app_strings['Todo']."&nbsp;".$app_strings['LBL_NOW'].".&nbsp;".$app_strings['LBL_CLICK_THE_LINK']."&nbsp;:<br>
					&nbsp;&nbsp;-<a href='javascript:void(0);' onClick='gshow(\"createTodo\",\"todo\",\"".$temp_date."\",\"".$temp_date."\",\"".$time_arr['starthour']."\",\"".$time_arr['startmin']."\",\"".$time_arr['startfmt']."\",\"".$time_arr['endhour']."\",\"".$time_arr['endmin']."\",\"".$time_arr['endfmt']."\",\"listview\",\"todo\");'>".$app_strings['LBL_CREATE']." ".$app_strings['LBL_A']." ".$app_strings['Todo']."</a>
					</td>";
		}
		else
		{
			$list_view .="<td class='small' align='left' nowrap='nowrap'>".$app_strings['LBL_YOU_ARE_NOT_ALLOWED_TO_CREATE']."&nbsp;".$app_strings['LBL_A']."&nbsp;".$app_strings['Todo']."<br></td>";
		}
										 
                $list_view .="</tr>
			</table>
			</div>";
		$list_view .="</td></tr>";
        }
	$list_view .="</table><br>";
	$cal_log->debug("Exiting constructTodoListView() method...");
        return $list_view;
}

/**
 * Function returns the list of privileges and permissions of the events that the current user can view the details of.
 * return string - query that is used as secondary parameter to fetch the events that the user can view and the schedule of the users
 */
function getCalendarViewSecurityParameter()
{
		global $current_user;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
        require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
        
		require_once('modules/Calendar/CalendarCommon.php');
		$shared_ids = getSharedCalendarId($current_user->id);
		if(isset($shared_ids) && $shared_ids != '')
			$condition = " or (vtiger_crmentity.smownerid in($shared_ids)) or (vtiger_crmentity.smownerid NOT LIKE ($current_user->id))";// and vtiger_activity.visibility = 'Public')";
		else
			$condition = "or (vtiger_crmentity.smownerid NOT LIKE ($current_user->id))";
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) $condition or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%')";

		if(sizeof($current_user_groups) > 0)
		{
			$sec_query .= " or (vtiger_groups.groupid in (". implode(",", $current_user_groups) ."))";
		}
		$sec_query .= ")";	
		return $sec_query;
}
?>