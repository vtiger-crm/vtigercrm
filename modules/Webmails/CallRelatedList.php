<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is FOSS Labs.
 * Portions created by FOSS Labs are Copyright (C) FOSS Labs.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

global $current_user;
require_once('Smarty_setup.php');
require_once('modules/Leads/Leads.php');
require_once('include/utils/utils.php');
require_once('include/utils/UserInfoUtil.php');
require_once('data/Tracker.php');
require_once('include/upload_file.php');
require_once('modules/Webmails/Webmails.php');
require_once('modules/Webmails/MailParse.php');

global $log;
global $app_strings;
global $mod_strings;

if($_REQUEST["record"]) {$mailid=vtlib_purify($_REQUEST["record"]);} else {$mailid=vtlib_purify($_REQUEST["mailid"]);}

$mailInfo = getMailServerInfo($current_user);
$temprow = $adb->fetch_array($mailInfo);
$imapServerAddress=$temprow["mail_servername"];
$start_message=vtlib_purify($_REQUEST["start_message"]);
$box_refresh=$temprow["box_refresh"];
$mails_per_page=$temprow["mails_per_page"];

if($_REQUEST["mailbox"] && $_REQUEST["mailbox"] != "") {$mailbox=vtlib_purify($_REQUEST["mailbox"]);} else {$mailbox="INBOX";}

global $mbox;
$mbox = getImapMbox($mailbox,$temprow);

$email = new Webmails($mbox, $mailid);
$from = $email->from;
$subject=$email->subject;
$date=$email->date;
$to=$email->to;
$cc_list=$email->cc_list;
$reply_to=$email->replyTo;


$block["Leads"]= "";
global $adb;
if($email->relationship != 0 && $email->relationship["type"] == "Leads") {
	$q = "SELECT vtiger_leaddetails.firstname, vtiger_leaddetails.lastname, vtiger_leaddetails.email, vtiger_leaddetails.company, vtiger_crmentity.smownerid from vtiger_leaddetails left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid WHERE vtiger_leaddetails.leadid=?";
	$rs = $adb->pquery($q, array($email->relationship["id"]));
	$block["Leads"]["header"]= array("0"=>"First Name","1"=>"Last Name","2"=>"Company Name","3"=>"Email Address","4"=>"Assigned To");
	$block["Leads"]["entries"]= array("0"=>array($adb->query_result($rs,0,'firstname'),"1"=>$adb->query_result($rs,0,'lastname'),2=>$adb->query_result($rs,0,'company'),3=>$adb->query_result($rs,0,'email'),4=>$adb->query_result($rs,0,'smownerid')));
}
$block["Contacts"]= "";
if($email->relationship != 0 && $email->relationship["type"] == "Contacts") {
	$q = "SELECT vtiger_contactdetails.firstname, vtiger_contactdetails.lastname, vtiger_contactdetails.email, vtiger_contactdetails.title, vtiger_crmentity.smownerid from vtiger_contactdetails left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid WHERE vtiger_contactdetails.contactid=?";
	$rs = $adb->pquery($q, array($email->relationship["id"]));
	$block["Contacts"]["header"]= array("0"=>"First Name","1"=>"Last Name","2"=>"Title","3"=>"Email Address","4"=>"Assigned To");
	$block["Contacts"]["entries"]= array("0"=>array($adb->query_result($rs,0,'firstname'),"1"=>$adb->query_result($rs,0,'lastname'),2=>$adb->query_result($rs,0,'title'),3=>$adb->query_result($rs,0,'email'),4=>$adb->query_result($rs,0,'smownerid')));
}
$block["Accounts"]= "";
if($email->relationship != 0 && $email->relationship["type"] == "Accounts") {
	$q = "SELECT acccount.accountname, vtiger_account.email1, vtiger_account.website, vtiger_account.industry, vtiger_crmentity.smownerid from vtiger_account left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid WHERE vtiger_account.accountid=?";
	$rs = $adb->pquery($q, array($email->relationship["id"]));
	$block["Accounts"]["header"]= array("0"=>"Account Name","1"=>"Email","2"=>"Web Site","3"=>"Industry","4"=>"Assigned To");
	$block["Accounts"]["entries"]= array("0"=>array($adb->query_result($rs,0,'accountname'),"1"=>$adb->query_result($rs,0,'email'),2=>$adb->query_result($rs,0,'website'),3=>$adb->query_result($rs,0,'industry'),4=>$adb->query_result($rs,0,'smownerid')));
}

global $mod_strings;
global $app_strings;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$smarty->assign("CATEGORY","My Home Page");
$smarty->assign("id",vtlib_purify($_REQUEST["record"]));
$smarty->assign("NAME","From: ".$from);
$smarty->assign("RELATEDLISTS", $block);
$smarty->assign("SINGLE_MOD","Webmails");
$smarty->assign("MODULE", "Webmails");
$smarty->assign("ID",vtlib_purify($_REQUEST["record"]));
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);
$smarty->display("RelatedLists.tpl");
?>