<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('Smarty_setup.php');
global $mod_strings;
global $app_strings;
global $app_list_strings;

//Display the mail send status
$smarty = new vtigerCRM_Smarty;
if($_REQUEST['mail_error'] != '') {
	require_once("modules/Emails/mail.php");
	$error_msg = strip_tags(parseEmailErrorString($_REQUEST['mail_error']));
	$error_msg = $mod_strings['LBL_MAILSENDERROR'];
	$smarty->assign("ERROR_MSG",$mod_strings['LBL_TESTMAILSTATUS'].' <b><font class="warning">'.$error_msg.'</font></b>');
}

global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$sql="select * from vtiger_systems where server_type = ?";
$result = $adb->pquery($sql, array('email'));
$mail_server = $adb->query_result($result,0,'server');
$mail_server_username = $adb->query_result($result,0,'server_username');
$mail_server_password = $adb->query_result($result,0,'server_password');
$smtp_auth = $adb->query_result($result,0,'smtp_auth');
$from_email_field = $adb->query_result($result, 0, 'from_email_field');
$servername = vtlib_purify($_REQUEST['server_name']);
$username = vtlib_purify($_REQUEST['server_user']);

if(!empty($servername)) {
    $validInput = validateServerName($servername);
if(! $validInput) {
    $servername = '';
 }
    $smarty->assign("MAILSERVER",$servername);
} elseif(isset($mail_server)) {
    $smarty->assign("MAILSERVER",$mail_server);
}

if(!empty($username)) {
    $validInput = validateEmailId($username);
if(! $validInput) {
    $username = '';
 }
	$smarty->assign("USERNAME",$username);
} elseif(isset($mail_server_username)) {
	$smarty->assign("USERNAME",$mail_server_username);
}

if (isset($mail_server_password))
	$smarty->assign("PASSWORD",$mail_server_password);
if(isset($_REQUEST['from_email_field'])){

	$smarty->assign("FROM_EMAIL_FIELD",vtlib_purify($_REQUEST['from_email_field']));
} elseif(isset($from_email_field)) {
	$smarty->assign("FROM_EMAIL_FIELD",$from_email_field);
}
if(isset($_REQUEST['auth_check']))
{
	if($_REQUEST['auth_check'] == 'on')
                $smarty->assign("SMTP_AUTH",'checked');
        else
                $smarty->assign("SMTP_AUTH",'');
}
elseif (isset($smtp_auth))
{
	if($smtp_auth == 'true')
		$smarty->assign("SMTP_AUTH",'checked');
	else
		$smarty->assign("SMTP_AUTH",'');
}

if(isset($_REQUEST['emailconfig_mode']) && $_REQUEST['emailconfig_mode'] != '')
	$smarty->assign("EMAILCONFIG_MODE",vtlib_purify($_REQUEST['emailconfig_mode']));
else
	$smarty->assign("EMAILCONFIG_MODE",'view');

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->display("Settings/EmailConfig.tpl");
?>