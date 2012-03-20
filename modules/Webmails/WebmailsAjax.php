<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is FOSS Labs.
 * Portions created by FOSS Labs are Copyright (C) FOSS Labs.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('include/utils/UserInfoUtil.php');
require_once('modules/Webmails/MailBox.php');
require_once('modules/Webmails/Webmails.php');

global $adb,$current_user;

if($_POST['config_chk'] == 'true')
{
	$MailBox = new MailBox();
	if($MailBox->enabled == 'false') {
		echo 'FAILED';
		exit();
	} else {
		echo 'SUCCESS';
		exit();
	}
	exit();
}
if(isset($_REQUEST['file']) && $_REQUEST['file']!='' && !isset($_REQUEST['ajax'])){
	checkFileAccess("modules/".$_REQUEST['module']."/".$_REQUEST['file'].".php");
	require_once("modules/".$_REQUEST['module']."/".$_REQUEST['file'].".php");
	exit();
}
$mailid = vtlib_purify($_REQUEST["mailid"]);
if(isset($_REQUEST["mailbox"]) && $_REQUEST["mailbox"] != "") {$mailbox=vtlib_purify($_REQUEST["mailbox"]);} else {$mailbox="INBOX";}

$adb->println("Inside WebmailsAjax.php");

if(isset($_POST["file"]) && $_POST["ajax"] == "true") {
	checkFileAccess("modules/".$_REQUEST["module"]."/".$_POST["file"].".php");
	require_once("modules/".$_REQUEST["module"]."/".$_POST["file"].".php");
}

if(isset($_REQUEST["command"]) && $_REQUEST["command"] != "") {
    $command = $_REQUEST["command"];
    if($command == "expunge") {
    	$MailBox = new MailBox($mailbox);
    	imap_expunge($MailBox->mbox);
		$MailBox = new MailBox($mailbox);
		$elist = $MailBox->mailList;
        $num_mails = $elist['count'];
		$start_page = cal_start($num_mails,$MailBox->mails_per_page);
		imap_close($MailBox->mbox);
		echo $start_page;
		flush();
		exit();
    }
    if($command == "delete_msg") {
		$adb->println("DELETE SINGLE WEBMAIL MESSAGE $mailid");
    	$MailBox = new MailBox($mailbox);
        imap_delete($MailBox->mbox,$mailid);
		imap_expunge($MailBox->mbox);
		$email = new Webmails($MailBox->mbox,$mailid);
		$MailBox = new MailBox($mailbox);
		$elist = $MailBox->mailList;
		$num_mails = $elist['count'];
		$start_page = cal_start($num_mails,$MailBox->mails_per_page);
		imap_close($MailBox->mbox);
		echo "start=".$start_page.";";
		echo "id=".$mailid.";";
		flush();
		exit();
    }
    if($command == "delete_multi_msg") {
    	$MailBox = new MailBox($mailbox);
		$tlist = explode(":",$mailid);
		foreach($tlist as $id) {
	        imap_delete($MailBox->mbox,$id);
			$adb->println("DELETE MULTI MESSAGE $id");
			$email = new Webmails($MailBox->mbox,$id);
			$email->delete();
		}
		imap_expunge($MailBox->mbox);
		$MailBox = new MailBox($mailbox);
        $elist = $MailBox->mailList;
        $num_mails = $elist['count'];
		$start_page = cal_start($num_mails,$MailBox->mails_per_page);
		imap_close($MailBox->mbox);
		echo "start=".$start_page.";";
        echo "ids='".$mailid."';";
		flush();
		exit();
    } 
    if($_POST["command"] == "move_msg" && $_POST["ajax"] == "true") {
		$MailBox = new MailBox($mailbox);
        if(isset($_REQUEST["mailid"]) && $_REQUEST["mailid"] != '') {
			$mailids = explode(':',$_REQUEST["mailid"]);
        }
        foreach($mailids as $mailid) {
			imap_mail_move($MailBox->mbox,$mailid,$_REQUEST["mvbox"]);
        }
        imap_expunge($MailBox->mbox);
        imap_close($MailBox->mbox);
        $MailBox = new MailBox($mailbox);
        $elist = $MailBox->mailList;
        $num_mails = $elist['count'];
        $start_page = cal_start($num_mails,$MailBox->mails_per_page);
		imap_close($MailBox->mbox);
		echo $start_page;
        flush();
        exit();
    }

    if($command == "undelete_msg") {
    	$MailBox = new MailBox($mailbox);
		$email = new Webmails($MailBox->mbox,$mailid);
        $email->unDeleteMsg();
		imap_close($MailBox->mbox);
		echo $mailid;
		flush();
		exit();
    }
    if($command == "set_flag") {
    	$MailBox = new MailBox($mailbox);
		$email = new Webmails($MailBox->mbox,$mailid);
        $email->setFlag();
		imap_close($MailBox->mbox);
		echo $mailid;
		flush();
		exit();
    }
    if($command == "clear_flag") {
    	$MailBox = new MailBox($mailbox);
		$email = new Webmails($MailBox->mbox,$mailid);
        $email->delFlag();
		imap_close($MailBox->mbox);
		echo $mailid;
		flush();
		exit();
    }
	imap_close($MailBox->mbox);
	flush();
	exit();
}
function cal_start($num_mails,$mail_per_page) {
	if(isset($_REQUEST['start']) && $_REQUEST['start']!=0) {
        $pre_start = $_REQUEST['start'];
        $cal = (($pre_start-1) * $mail_per_page);
        if($num_mails > $cal)
			$res = $pre_start;
        else
			$res = $pre_start - 1;
    } else
		$res = 0;
	return $res;
}
?>