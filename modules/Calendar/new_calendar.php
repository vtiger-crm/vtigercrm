<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 **********************************************************************************/

global $theme,$current_user,$app_strings;
$theme_path = "themes/".$theme."/";
$image_path = $theme_path."images/";
require_once("modules/Calendar/calendarLayout.php");
require_once("modules/Calendar/Calendar.php");
$mysel= vtlib_purify($_REQUEST['view']);
$subtab = vtlib_purify($_REQUEST['subtab']);
$viewBox = vtlib_purify($_REQUEST['viewOption']);
$viewOptions = array('listview','hourview');
if(empty($viewBox) || !in_array($viewBox, $viewOptions)) {
	$viewBox = 'hourview';
}
if($subtab != 'todo') {
	$subtab = 'event';
}
$calendar_arr = Array();
$calendar_arr['IMAGE_PATH'] = $image_path;
/* fix (for Ticket ID:2259 GA Calendar Default View not working) given by dartagnanlaf START --integrated by Minnie */
$views = array('day','week','month','year');
if(empty($mysel) || !in_array($mysel, $views)) {
	if($current_user->activity_view == "This Year"){
		$mysel = 'year';
	}else if($current_user->activity_view == "This Month"){
		$mysel = 'month';
	}else if($current_user->activity_view == "This Week"){
		$mysel = 'week';
	}else{
		$mysel = 'day';
	}
}
/* fix given by dartagnanlaf END --integrated by Minnie */
$date_data = array();
if (isset($_REQUEST['day']) && is_numeric(vtlib_purify($_REQUEST['day']))) {
	$date_data['day'] = vtlib_purify($_REQUEST['day']);
}
if (isset($_REQUEST['month']) && is_numeric(vtlib_purify($_REQUEST['month']))) {
	$date_data['month'] = vtlib_purify($_REQUEST['month']);
}
if (isset($_REQUEST['week']) && is_numeric(vtlib_purify($_REQUEST['week']))) {
	$date_data['week'] = vtlib_purify($_REQUEST['week']);
}
if (isset($_REQUEST['year']) && is_numeric(vtlib_purify($_REQUEST['year']))) {
	if ($_REQUEST['year'] > 2037 || $_REQUEST['year'] < 1970) {
		print("<font color='red'>".$app_strings['LBL_CAL_LIMIT_MSG']."</font>");
		exit;
	}
	$date_data['year'] = vtlib_purify($_REQUEST['year']);
}


if(empty($date_data)) {
	$dateTimeField = new DateTimeField('');
	$dateValue = $dateTimeField->getDisplayDate();
	$timeValue = $dateTimeField->getDisplayTime();
	$dbDateValue = DateTimeField::convertToDBFormat($dateValue);
	$dateValueArray = explode('-', $dbDateValue);
	$timeValueArray = explode(':', $timeValue);
	$date_data = Array(
		'day'=>$dateValueArray[2],
		'month'=>$dateValueArray[1],
		'year'=>$dateValueArray[0],
		'hour'=>$timeValueArray[0],
		'min'=>$timeValueArray[1],
	);	
}

$calendar_arr['calendar'] = new Calendar($mysel,$date_data);
if($current_user->hour_format != '') 
	$calendar_arr['calendar']->hour_format=$current_user->hour_format;
if ($viewBox == 'hourview' && ($mysel == 'day' || $mysel == 'week' || $mysel == 'month' || $mysel == 'year'))
{
        $calendar_arr['calendar']->add_Activities($current_user);
}
$calendar_arr['view'] = $mysel;
calendar_layout($calendar_arr,$viewBox,$subtab);
?>