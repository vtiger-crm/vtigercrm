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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Emails/Save.php,v 1.27 2005/04/29 08:54:38 rank Exp $
 * Description:  Saves an Account record and then redirects the browser to the 
 * defined return URL.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
 require_once("include/Zend/Json.php");
 
 //check for mail server configuration thro ajax
if(isset($_REQUEST['server_check']) && $_REQUEST['server_check'] == 'true')
{
	$sql="select * from vtiger_systems where server_type = ?";
	$records=$adb->num_rows($adb->pquery($sql, array('email')),0,"id");
	if($records != '')
		echo 'SUCCESS';
	else
		echo 'FAILURE';	
	die;	
}

//Added on 09-11-2005 to avoid loading the webmail vtiger_files in Email process
if($_REQUEST['smodule'] != '')
{
	define('SM_PATH','modules/squirrelmail-1.4.4/');
	/* SquirrelMail required vtiger_files. */
	require_once(SM_PATH . 'functions/strings.php');
	require_once(SM_PATH . 'functions/imap_general.php');
	require_once(SM_PATH . 'functions/imap_messages.php');
	require_once(SM_PATH . 'functions/i18n.php');
	require_once(SM_PATH . 'functions/mime.php');
	require_once(SM_PATH .'include/load_prefs.php');
	//require_once(SM_PATH . 'class/mime/Message.class.php');
	require_once(SM_PATH . 'class/mime.class.php');
	sqgetGlobalVar('key',       $key,           SQ_COOKIE);
	sqgetGlobalVar('username',  $username,      SQ_SESSION);
	sqgetGlobalVar('onetimepad',$onetimepad,    SQ_SESSION);
	$mailbox = 'INBOX';
}

require_once('modules/Emails/Emails.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

$local_log =& LoggerManager::getLogger('index');

$focus = new Emails();

global $current_user,$mod_strings,$app_strings;
if(isset($_REQUEST['description']) && $_REQUEST['description'] !='')
	$_REQUEST['description'] = fck_from_html($_REQUEST['description']);

$all_to_ids = $_REQUEST["hidden_toid"];
$all_to_ids .= $_REQUEST["saved_toid"];
$_REQUEST["saved_toid"] = $all_to_ids;
//we always save the email with "save" status and when it is sent it is marked as SENT
$_REQUEST['email_flag'] = 'SAVED';
setObjectValuesFromRequest($focus);
//Check if the file is exist or not.
//$file_name = '';
if(isset($_REQUEST['filename_hidden'])) {
	$file_name = $_REQUEST['filename_hidden'];
} else {
	$file_name = $_FILES['filename']['name'];
}
$errorCode =  $_FILES['filename']['error'];
$errormessage = "";
if($file_name != '' && $_FILES['filename']['size'] == 0)
{
	if($errorCode == 4 || $errorCode == 0)
	{
		 if($_FILES['filename']['size'] == 0)
			 $errormessage = "<B><font color='red'>".$mod_strings['LBL_PLEASE_ATTACH']."</font></B> <br>";
	}
	else if($errorCode == 2)
	{
		  $errormessage = "<B><font color='red'>".$mod_strings['LBL_EXCEED_MAX'].$upload_maxsize.$mod_strings['LBL_BYTES']." </font></B> <br>";
	}
	else if($errorCode == 6)
	{
	     $errormessage = "<B>".$mod_strings['LBL_KINDLY_UPLOAD']."</B> <br>" ;
	}
	else if($errorCode == 3 )
	{
	     if($_FILES['filename']['size'] == 0)
		     $errormessage = "<b><font color='red'>".$mod_strings['LBL_PLEASE_ATTACH']."</font></b><br>";
	}
	else{}
	if($errormessage != ""){
		$ret_error = 1;
		$ret_parentid = $_REQUEST['parent_id'];
		$ret_toadd = $_REQUEST['parent_name'];
		$ret_subject = $_REQUEST['subject'];
		$ret_ccaddress = $_REQUEST['ccmail'];
		$ret_bccaddress = $_REQUEST['bccmail'];
		$ret_description = $_REQUEST['description'];
		echo $errormessage;
        	include("EditView.php");	
		exit();
	}
}


if($_FILES["filename"]["size"] == 0 && $_FILES["filename"]["name"] != '')
{
        $file_upload_error = true;
        $_FILES = '';
}

if((isset($_REQUEST['deletebox']) && $_REQUEST['deletebox'] != null) && $_REQUEST['addbox'] == null)
{
	imap_delete($mbox,$_REQUEST['deletebox']);
	imap_expunge($mbox);
	header("Location: index.php?module=Emails&action=index");
	exit();
}

function checkIfContactExists($mailid)
{
	global $log;
	$log->debug("Entering checkIfContactExists(".$mailid.") method ...");
	global $adb;
	$sql = "select contactid from vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid where vtiger_crmentity.deleted=0 and email= ?";
	$result = $adb->pquery($sql, array($mailid));
	$numRows = $adb->num_rows($result);
	if($numRows > 0)
	{
		$log->debug("Exiting checkIfContactExists method ...");
		return $adb->query_result($result,0,"contactid");
	}
	else
	{
		$log->debug("Exiting checkIfContactExists method ...");
		return -1;
	}
}
//assign the focus values
$focus->filename = $_REQUEST['file_name'];
$focus->parent_id = $_REQUEST['parent_id'];
$focus->parent_type = $_REQUEST['parent_type'];
$focus->column_fields["assigned_user_id"]=$current_user->id;
$focus->column_fields["activitytype"]="Emails";
$focus->column_fields["date_start"]= date(getNewDisplayDate());//This will be converted to db date format in save
$focus->save("Emails");
$return_id = $focus->id;

require_once("modules/Emails/mail.php");
if(isset($_REQUEST['send_mail']) && $_REQUEST['send_mail'] && $_REQUEST['parent_id'] != '') 
{
	$user_mail_status = send_mail('Emails',$current_user->column_fields['email1'],$current_user->user_name,'',$_REQUEST['subject'],$_REQUEST['description'],$_REQUEST['ccmail'],$_REQUEST['bccmail'],'all',$focus->id);
		
	//if block added to fix the issue #3759
	if($user_mail_status != 1){
		$query  = "select crmid,attachmentsid from vtiger_seattachmentsrel where crmid=?";
		$result = $adb->pquery($query, array($email_id));
		$numOfRows = $adb->num_rows($result);
		for($i=0; $i<$numOfRows; $i++)
		{
			$attachmentsid = $adb->query_result($result,0,"attachmentsid");		
			if($attachmentsid > 0)
			{	
				$query1="delete from vtiger_crmentity where crmid=?";
			 	$adb->pquery($query1, array($attachmentsid));
			}

			$crmid=$adb->query_result($result,0,"crmid");
			$query2="delete from vtiger_crmentity where crmid=?";
			$adb->pquery($query2, array($crmid));
		}
			
		$query = "delete from vtiger_emaildetails where emailid=?";	
		$adb->pquery($query, array($focus->id));
        	
		$error_msg = "<font color=red><strong>".$mod_strings['LBL_CHECK_USER_MAILID']."</strong></font>";
	        $ret_error = 1;
		$ret_parentid = $_REQUEST['parent_id'];
	        $ret_toadd = $_REQUEST['parent_name'];
        	$ret_subject = $_REQUEST['subject'];
	        $ret_ccaddress = $_REQUEST['ccmail'];
        	$ret_bccaddress = $_REQUEST['bccmail'];
	        $ret_description = $_REQUEST['description'];
        	echo $error_msg;
	        include("EditView.php");
        	exit();
	}

}

$focus->retrieve_entity_info($return_id,"Emails");

//this is to receive the data from the Select Users button
if($_REQUEST['source_module'] == null)
{
	$module = 'users';
}
//this will be the case if the Select Contact button is chosen
else
{
	$module = $_REQUEST['source_module'];
}

if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != "") 
	$return_module = vtlib_purify($_REQUEST['return_module']);
else 
	$return_module = "Emails";

if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != "") 
	$return_action = vtlib_purify($_REQUEST['return_action']);
else 
	$return_action = "DetailView";

if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "") 
	$return_id = vtlib_purify($_REQUEST['return_id']);

if(isset($_REQUEST['filename']) && $_REQUEST['filename'] != "") 
	$filename = vtlib_purify($_REQUEST['filename']);

$local_log->debug("Saved record with id of ".$return_id);

if(isset($_REQUEST['send_mail']) && $_REQUEST['send_mail'] && $_REQUEST['parent_id'] == ''){
	if($_REQUEST["parent_name"] != '' && isset($_REQUEST["parent_name"])) {
		include("modules/Emails/webmailsend.php");
	}

} elseif( isset($_REQUEST['send_mail']) && $_REQUEST['send_mail'])
	include("modules/Emails/mailsend.php");



if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] == 'mailbox')
	header("Location: index.php?module=$return_module&action=index");
else {
	if($_REQUEST['return_viewname'] == '') $return_viewname='0';
	if($_REQUEST['return_viewname'] != '')$return_viewname=vtlib_purify($_REQUEST['return_viewname']);
	//Added for 4600
	$inputs="<script>window.opener.location.href=window.opener.location.href;window.self.close();</script>";
	echo $inputs;
}
?>