<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/
ini_set("include_path", "../");

require('send_mail.php');
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/language/en_us.lang.php');
global $app_strings;
// Email Setup
$emailresult = $adb->pquery("SELECT email1 from vtiger_users", array());
$emailid = $adb->fetch_array($emailresult);
$emailaddress = $emailid[0];
$mailserveresult = $adb->pquery("SELECT server,server_username,server_password,smtp_auth FROM vtiger_systems where server_type = ?", array('email'));
$mailrow = $adb->fetch_array($mailserveresult);
$mailserver = $mailrow[0];
$mailuname = $mailrow[1];
$mailpwd = $mailrow[2];
$smtp_auth = $mailrow[3];
// End Email Setup


//query the vtiger_notificationscheduler vtiger_table and get data for those notifications which are active
$sql = "select active from vtiger_notificationscheduler where schedulednotificationid=1";
$result = $adb->pquery($sql, array());

$activevalue = $adb->fetch_array($result);

if($activevalue[0] == 1)
{
	//Delayed Tasks Notification

	//get all those activities where the status is not completed even after the passing of 24 hours
	$today = date("Ymd"); 
	$result = $adb->pquery("select vtiger_activity.status,vtiger_activity.activityid,subject,(vtiger_activity.date_start +1),vtiger_crmentity.smownerid from vtiger_activity inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid where vtiger_crmentity.deleted=0 and vtiger_activity.status <> 'Completed' and activitytype='Task' and ".$today." > (vtiger_activity.date_start+1)", array());

	while ($myrow = $adb->fetch_array($result))
	{
		$status=$myrow[0];
		$subject = (strlen($myrow[2]) > 30)?substr($myrow[2],0,27).'...':$myrow[2];
		$user_id = $myrow[4];
		if($user_id != '')
		{
			$user_res = $adb->pquery('select user_name from vtiger_users where id=?',array($user_id));
			$assigned_user = $adb->query_result($user_res,0,'user_name');
		}
		$mail_body = $app_strings['Dear_Admin_tasks_not_been_completed']." ".$app_strings['LBL_SUBJECT'].": ".$subject."<br> ".$app_strings['LBL_ASSIGNED_TO'].": ".$assigned_user."<br><br>".$app_strings['Task_sign'];
	 	sendmail($emailaddress,$emailaddress,$app_strings['Task_Not_completed'].': '.$subject,$mail_body,$mailserver,$mailuname,$mailpwd,"",$smtp_auth);
	}
}

//Big Deal Alert
$sql = "select active from vtiger_notificationscheduler where schedulednotificationid=2";
$result = $adb->pquery($sql, array());

$activevalue = $adb->fetch_array($result);
if($activevalue[0] == 1)
{
	$result = $adb->pquery("SELECT sales_stage,amount,potentialid,potentialname FROM vtiger_potential inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_potential.potentialid where vtiger_crmentity.deleted=0 and sales_stage='Closed Won' and amount > 10000",array());
	while ($myrow = $adb->fetch_array($result))
	{
		$pot_id = $myrow['potentialid'];
		$pot_name = $myrow['potentialname'];
		$body_content = $app_strings['Dear_Team'].$app_strings['Dear_Team_Time_to_Party']."<br><br>".$app_strings['Potential_Id']." ".$pot_id;
		$body_content .= $app_strings['Potential_Name']." ".$pot_name."<br><br>";
		sendmail($emailaddress,$emailaddress,$app_strings['Big_Deal_Closed_Successfully'],$body_content,$mailserver,$mailuname,$mailpwd,"",$smtp_auth);
	}
}
//Pending tickets
$sql = "select active from vtiger_notificationscheduler where schedulednotificationid=3";
$result = $adb->pquery($sql, array());

$activevalue = $adb->fetch_array($result);
if($activevalue[0] == 1)
{
	$result = $adb->pquery("SELECT vtiger_troubletickets.status,ticketid FROM vtiger_troubletickets INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_troubletickets.ticketid WHERE vtiger_crmentity.deleted='0' AND vtiger_troubletickets.status <> 'Completed' AND vtiger_troubletickets.status <> 'Closed'", array());

	while ($myrow = $adb->fetch_array($result))
	{
		$ticketid = $myrow['ticketid'];
		sendmail($emailaddress,$emailaddress,$app_strings['Pending_Ticket_notification'],$app_strings['Kind_Attention'].$ticketid .$app_strings['Thank_You_HelpDesk'],$mailserver,$mailuname,$mailpwd,"",$smtp_auth);	
	}
}

//Too many tickets related to a particular vtiger_account/company causing concern
$sql = "select active from vtiger_notificationscheduler where schedulednotificationid=4";
$result = $adb->pquery($sql, array());

$activevalue = $adb->fetch_array($result);
if($activevalue[0] == 1)
{
	$result = $adb->pquery("SELECT count(*) as count FROM vtiger_troubletickets INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_troubletickets.ticketid WHERE vtiger_crmentity.deleted='0' AND vtiger_troubletickets.status <> 'Completed' AND vtiger_troubletickets.status <> 'Closed'", array());
$count = $adb->query_result($result,0,'count');
//changes made to get too many tickets notification only when tickets count is greater than or equal to 5
	if($count >= 5)
	{
		sendmail($emailaddress,$emailaddress,$app_strings['Too_many_pending_tickets'],$app_strings['Dear_Admin_too_ many_tickets_pending'],$mailserver,$mailuname,$mailpwd,"",$smtp_auth);
	}
}

//Support Starting
$sql = "select active from vtiger_notificationscheduler where schedulednotificationid=5";
$result = $adb->pquery($sql, array());

$activevalue = $adb->fetch_array($result);
if($activevalue[0] == 1)
{
	$result = $adb->pquery("SELECT vtiger_products.productname FROM vtiger_products inner join vtiger_crmentity on vtiger_products.productid = vtiger_crmentity.crmid where vtiger_crmentity.deleted=0 and start_date like ?", array(date('Y-m-d'). "%"));
	while ($myrow = $adb->fetch_array($result))
	{
		$productname=$myrow[0];
		sendmail($emailaddress,$emailaddress,$app_strings['Support_starting'],$app_strings['Hello_Support'].$productname ."\n ".$app_strings['Congratulations'],$mailserver,$mailuname,$mailpwd,"",$smtp_auth);	
	}
}

//Support ending
$sql = "select active from vtiger_notificationscheduler where schedulednotificationid=6";
$result = $adb->pquery($sql, array());

$activevalue = $adb->fetch_array($result);
if($activevalue[0] == 1)
{
	$result = $adb->pquery("SELECT vtiger_products.productname from vtiger_products inner join vtiger_crmentity on vtiger_products.productid = vtiger_crmentity.crmid where vtiger_crmentity.deleted=0 and expiry_date like ?", array(date('Y-m-d') ."%"));
	while ($myrow = $adb->fetch_array($result))
	{
		$productname=$myrow[0];
		sendmail($emailaddress,$emailaddress,$app_strings['Support_Ending_Subject'],$app_strings['Support_Ending_Content'].$productname.$app_strings['kindly_renew'],$mailserver,$mailuname,$mailpwd,"",$smtp_auth);
	}
}

?>