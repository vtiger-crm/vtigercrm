<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

//5.1.0 RC to 5.1.0 database changes

//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.1.0 RC to 5.1.0 -------- Starts \n\n");

ExecuteQuery("DELETE vtiger_cvcolumnlist FROM vtiger_cvcolumnlist INNER JOIN vtiger_customview WHERE vtiger_cvcolumnlist.columnname LIKE '%vtiger_notes:filename%' AND vtiger_customview.cvid = vtiger_cvcolumnlist.cvid AND vtiger_customview.entitytype='HelpDesk'");
ExecuteQuery("DELETE vtiger_cvcolumnlist FROM vtiger_cvcolumnlist INNER JOIN vtiger_customview WHERE (vtiger_cvcolumnlist.columnname LIKE '%parent_id%' OR vtiger_cvcolumnlist.columnname LIKE '%vtiger_contactdetails%') AND vtiger_customview.cvid = vtiger_cvcolumnlist.cvid AND vtiger_customview.entitytype='Documents'");

ExecuteQuery("DELETE vtiger_cvadvfilter FROM vtiger_cvadvfilter INNER JOIN vtiger_customview WHERE vtiger_cvadvfilter.columnname LIKE '%vtiger_notes:filename%' AND vtiger_customview.cvid = vtiger_cvadvfilter.cvid AND vtiger_customview.entitytype='HelpDesk'");
ExecuteQuery("DELETE vtiger_cvadvfilter FROM vtiger_cvadvfilter INNER JOIN vtiger_customview WHERE (vtiger_cvadvfilter.columnname LIKE '%parent_id%' OR vtiger_cvadvfilter.columnname LIKE '%vtiger_contactdetails%') AND vtiger_customview.cvid = vtiger_cvadvfilter.cvid AND vtiger_customview.entitytype='Documents'");

// Fixed issue with Calendar duration calculation
ExecuteQuery("ALTER TABLE vtiger_activity MODIFY duration_hours VARCHAR(200)");

$result = $adb->query("SELECT activityid,date_start,due_date, time_start,time_end FROM vtiger_activity WHERE activitytype NOT IN ('Task','Emails')");
$noofrows = $adb->num_rows($result);
for($index=0;$index<$noofrows;$index++){
 	$activityid = $adb->query_result($result,$index,'activityid');
	$date_start = $adb->query_result($result,$index,'date_start');
	$time_start = $adb->query_result($result,$index,'time_start');
	$due_date = $adb->query_result($result,$index,'due_date');
	$time_end = $adb->query_result($result,$index,'time_end');
	
	$start_date = split("-",$date_start);
	$end_date = split("-",$due_date);
	$start_time = split(":",$time_start);
	$end_time = split(":",$time_end);
	
	$start = mktime(intval($start_time[0]),intval($start_time[1]),0,intval($start_date[1]),intval($start_date[2]),intval($start_date[0]));
	$end = mktime(intval($end_time[0]),intval($end_time[1]),0,intval($end_date[1]),intval($end_date[2]),intval($end_date[0]));
	
	$duration_in_minutes = floor(($end-$start)/(60));//get the difference between start time and end time in minutes
	$hours = floor($duration_in_minutes/60);
	$minutes = $duration_in_minutes%60;
	$adb->pquery("UPDATE vtiger_activity SET duration_hours=?, duration_minutes=? WHERE activityid=?",array($hours, $minutes,$activityid));
}

$migrationlog->debug("\n\nDB Changes from 5.1.0 RC to 5.1.0 -------- Ends \n\n");

?>