<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once("include/database/PearDatabase.php");
require_once("modules/Users/Users.php");
global $current_user;
$displayname=$_REQUEST['displayname'];
$userid = $current_user->id;
$email=$_REQUEST['email'];
$account_name=$_REQUEST['account_name'];
$mailprotocol=$_REQUEST['mailprotocol'];
$server_username = $_REQUEST['server_username'];
$server_password = $_REQUEST['server_password'];
$mail_servername = $_REQUEST['mail_servername'];
$box_refresh = $_REQUEST['box_refresh'];
$mails_per_page = $_REQUEST['mails_per_page'];
$ssltype = $_REQUEST["ssltype"];
$sslmeth = $_REQUEST["sslmeth"];

if($mails_per_page == '') $mails_per_page='0';

if(isset($_REQUEST['record']) && $_REQUEST['record']!='') {
	$id=$_REQUEST['record'];
}

$focus = new Users();
$encrypted_password=$focus->changepassword($_REQUEST['server_password']);
if(isset($_REQUEST['edit']) && $_REQUEST['edit'] && $_REQUEST['record']!='') {
	$sql="update vtiger_mail_accounts set display_name = ?, mail_id = ?, account_name = ?, mail_protocol = ?, mail_username = ?";
	$params = array($displayname, $email, $account_name, $mailprotocol, $server_username);
	if($server_password != '*****') {
		$sql.=", mail_password=?";
		array_push($params, $encrypted_password);
	}
	$sql.=", mail_servername=?,  box_refresh=?,  mails_per_page=?, ssltype=? , sslmeth=?, int_mailer=? where user_id = ?";
	array_push($params, $mail_servername, $box_refresh, $mails_per_page, $ssltype, $sslmeth, $_REQUEST["int_mailer"], $id);
} else {
	$account_id = $adb->getUniqueID("vtiger_mail_accounts");
	$sql="insert into vtiger_mail_accounts(account_id, user_id, display_name, mail_id, account_name, mail_protocol, mail_username, mail_password, mail_servername, box_refresh, mails_per_page, ssltype, sslmeth, int_mailer, status, set_default) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	$params = array($account_id, $current_user->id, $displayname, $email, $account_name, $mailprotocol, $server_username, $encrypted_password, $mail_servername, $box_refresh, $mails_per_page, $ssltype, $sslmeth, $_REQUEST["int_mailer"],'1','0');
}

$adb->pquery($sql, $params);

$return_module = vtlib_purify($_REQUEST['return_module']);
if(empty($return_module)) $return_module = 'Webmails';

$return_action = vtlib_purify($_REQUEST['return_action']);
if(empty($return_action)) $return_action = 'index';

header("Location:index.php?module=$return_module&action=$return_action&mailbox=INBOX&parenttab=My Home Page");
?>