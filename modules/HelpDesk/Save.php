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
 * $Header: /advent/projects/wesat/vtiger_crm/vtigercrm/modules/HelpDesk/Save.php,v 1.8 2005/04/25 05:21:46 Mickie Exp $
 * Description:  Saves an Account record and then redirects the browser to the 
 * defined return URL.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/HelpDesk/HelpDesk.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

$focus = new HelpDesk();

//added to fix 4600
$search=vtlib_purify($_REQUEST['search_url']);

setObjectValuesFromRequest($focus);
global $adb,$mod_strings;
//Added to update the ticket history
//Before save we have to construct the update log. 
$mode = $_REQUEST['mode'];
if($mode == 'edit')
{
	$usr_qry = $adb->pquery("select * from vtiger_crmentity where crmid=?", array($focus->id));
	$old_user_id = $adb->query_result($usr_qry,0,"smownerid");
}
$grp_name = getGroupName($_REQUEST['assigned_group_id']);

if($_REQUEST['assigntype'] == 'U')  {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
} elseif($_REQUEST['assigntype'] == 'T'){
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
}

$fldvalue = $focus->constructUpdateLog($focus, $mode, $grp_name, $_REQUEST['assigntype']);
$fldvalue = from_html($fldvalue,($mode == 'edit')?true:false);

$focus->save("HelpDesk");

//After save the record, we should update the log
$adb->pquery("update vtiger_troubletickets set update_log=? where ticketid=?", array($fldvalue,$focus->id));

$return_id = $focus->id;

$parenttab = getParentTab();
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != "") $return_module = vtlib_purify($_REQUEST['return_module']);
else $return_module = "HelpDesk";
if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != "") $return_action = vtlib_purify($_REQUEST['return_action']);
else $return_action = "DetailView";
if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "") $return_id = vtlib_purify($_REQUEST['return_id']);

if($_REQUEST['mode'] == 'edit')
	$reply = 'Re : ';
else
	$reply = '';

$subject = $focus->column_fields['ticket_no'] . ' [ '.$mod_strings['LBL_TICKET_ID'].' : '.$focus->id.' ] '.$reply.$_REQUEST['ticket_title'];
$bodysubject = $mod_strings['Ticket No'] .":<br>" . $focus->column_fields['ticket_no'] . "<br>" . $mod_strings['LBL_TICKET_ID'].' : '.$focus->id.'<br> '.$mod_strings['LBL_SUBJECT'].$_REQUEST['ticket_title'];

$emailoptout = 0;

//To get the emailoptout vtiger_field value and then decide whether send mail about the tickets or not
if($focus->column_fields['parent_id'] != '')
{
	$parent_module = getSalesEntityType($focus->column_fields['parent_id']);
	if($parent_module == 'Contacts')
	{
		$result = $adb->pquery("select * from vtiger_contactdetails where contactid=?", array($focus->column_fields['parent_id']));
		$emailoptout = $adb->query_result($result,0,'emailoptout');
		$contactname = $adb->query_result($result,0,'firstname').' '.$adb->query_result($result,0,'lastname');
		$parentname = $contactname;
		$contact_mailid = $adb->query_result($result,0,'email');
	}
	if($parent_module == 'Accounts')
	{
		$result = $adb->pquery("select * from vtiger_account where accountid=?", array($focus->column_fields['parent_id']));
		$emailoptout = $adb->query_result($result,0,'emailoptout');
		$parentname = $adb->query_result($result,0,'accountname');
	}
}

//Get the status of the vtiger_portal user. if the customer is active then send the vtiger_portal link in the mail
if($contact_mailid != '')
{
	$sql = "select * from vtiger_portalinfo where user_name=?";
	$isactive = $adb->query_result($adb->pquery($sql, array($contact_mailid)),0,'isactive');
}
if($isactive == 1)
{
	$url = "<a href='".$PORTAL_URL."/index.php?module=HelpDesk&action=index&ticketid=".$focus->id."&fun=detail'>".$mod_strings['LBL_TICKET_DETAILS']."</a>";
	$email_body = $bodysubject.'<br><br>'.getPortalInfo_Ticket($focus->id,$_REQUEST['ticket_title'],$contactname,$url,$_REQUEST['mode']);
}
else
{
	$data['sub']=$_REQUEST['ticket_title'];
	$data['ticketno']=$focus->column_fields['ticket_no'];
	$data['parent_name']=$parentname;
	$data['status']=$focus->column_fields['ticketstatus'];
	$data['category']=$focus->column_fields['ticketcategories'];
	$data['severity'] = $focus->column_fields['ticketseverities'];
	$data['priority']=$focus->column_fields['ticketpriorities'];
	$data['description']=$focus->column_fields['description'];
	$data['solution'] = $focus->column_fields['solution'];
	$data['mode']= $_REQUEST['mode'];
	$email_body = getTicketDetails($focus->id,$data);
}
$_REQUEST['return_id'] = $return_id;

if($_REQUEST['return_module'] == 'Products' & $_REQUEST['product_id'] != '' &&  $focus->id != '')
	$return_id = vtlib_purify($_REQUEST['product_id']);

//send mail to the assigned to user and the parent to whom this ticket is assigned
//only if NOTIFY_OWNER_EMAILS is true
if(PerformancePrefs::getBoolean('NOTIFY_OWNER_EMAILS', true) === true){
	//send mail to the assigned to user and the parent to whom this ticket is assigned
	require_once('modules/Emails/mail.php');
	$user_emailid = getUserEmailId('id',$focus->column_fields['assigned_user_id']);

	if($user_emailid != '')
	{
		if($_REQUEST['mode'] != 'edit')
		{
			$mail_status = send_mail('HelpDesk',$user_emailid,$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$email_body);
		}
		else
		{
			if(($focus->column_fields['ticketstatus'] == $mod_strings["Closed"]) || ($focus->column_fields['comments'] != '') || ($_REQUEST['helpdesk_solution'] != $_REQUEST['solution']) || ($focus->column_fields['assigned_user_id'] != $old_user_id))
			{
				$mail_status = send_mail('HelpDesk',$user_emailid,$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$email_body);
			}

	}

	$mail_status_str = $user_emailid."=".$mail_status."&&&";
	}
	else
	{
		$mail_status_str = "'".$to_email."'=0&&&";
	}
	//added condition to check the emailoptout(this is for contacts and vtiger_accounts.)
	if($emailoptout == 0)
	{
		//send mail to parent
		if($_REQUEST['parent_id'] != '' && $_REQUEST['parent_type'] != '')
        {
        	$parentmodule = $_REQUEST['parent_type'];
            $parentid = $_REQUEST['parent_id'];

			$parent_email = getParentMailId($parentmodule,$parentid);
			if($_REQUEST['mode'] != 'edit')
        	{	
				$mail_status = send_mail('HelpDesk',$parent_email,$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$email_body);
			}
	        else
        	{
				if(( $focus->column_fields['ticketstatus']== $mod_strings["Closed"]) || ($focus->column_fields['comments'] != '' ) || ($_REQUEST['helpdesk_solution'] != $_REQUEST['solution']))
				{
					$mail_status = send_mail('HelpDesk',$parent_email,$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$email_body);
				}
			}
			$mail_status_str .= $parent_email."=".$mail_status."&&&";
        }
	}
	else
	{
		$adb->println("'".$parentname."' is not want to get the email about the ticket details as emailoptout is selected");
	}

	if ($mail_status != '') {
		$mail_error_status = getMailErrorString($mail_status_str);
	}
}
//code added for returning back to the current view after edit from list view
if($_REQUEST['return_viewname'] == '') $return_viewname='0';
if($_REQUEST['return_viewname'] != '')$return_viewname=vtlib_purify($_REQUEST['return_viewname']);
header("Location: index.php?action=$return_action&module=$return_module&parenttab=$parenttab&record=$return_id&$mail_error_status&viewname=$return_viewname&start=".vtlib_purify($_REQUEST['pagenumber']).$search);
?>
