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
require_once('modules/Settings/Forms.php');

global $mod_strings;
global $app_strings;
global $app_list_strings;
global $current_user;

global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

if(isset($_REQUEST['record']) && $_REQUEST['record']!='')
{
	$sql = "select * from vtiger_mail_accounts where user_id=?";
	$result = $adb->pquery($sql, array($_REQUEST['record']));
	$rowcount = $adb->num_rows($result);
	
	if ($rowcount!=0)
	{
		while($temprow = $adb->fetchByAssoc($result))
		{
			$smarty->assign("DISPLAYNAME", $temprow['display_name']);
			$smarty->assign("ID", $temprow['user_id']);
			$smarty->assign("EMAIL", $temprow['mail_id']);
			$smarty->assign("ACCOUNTNAME", $temprow['account_name']);
			$smarty->assign($temprow['mail_protocol'],$temprow['mail_protocol']);
			$smarty->assign("SERVERUSERNAME", $temprow['mail_username']);
			$smarty->assign("SERVERPASSWORD", $temprow['mail_password']);
			$smarty->assign("SERVERNAME", $temprow['mail_servername']);
			$smarty->assign("RECORD_ID", $temprow['account_id']);
			$smarty->assign("BOX_REFRESH", $temprow['box_refresh']);
			$smarty->assign("MAILS_PER_PAGE", $temprow['mails_per_page']);
			$smarty->assign("EDIT", "TRUE");

			if(strtolower($temprow['mail_protocol']) == "imap")
				$smarty->assign("IMAP", "CHECKED");
			if(strtolower($temprow['mail_protocol']) == "imap2")
				$smarty->assign("IMAP2", "CHECKED");
			if(strtolower($temprow['mail_protocol']) == "imap4")
				$smarty->assign("IMAP4", "CHECKED");
			if(strtolower($temprow['mail_protocol']) == "imap4rev1")
				$smarty->assign("IMAP4R1", "CHECKED");
			if(strtolower($temprow['mail_protocol']) == "pop3")
				$smarty->assign("POP3", "CHECKED");

			if(strtolower($temprow['ssltype']) == "notls")
				$smarty->assign("NOTLS", "CHECKED");
			if(strtolower($temprow['ssltype']) == "tls")
				$smarty->assign("TLS", "CHECKED");
			if(strtolower($temprow['ssltype']) == "ssl")
				$smarty->assign("SSL", "CHECKED");
			if(strtolower($temprow['sslmeth']) == "validate-cert")
				$smarty->assign("VALIDATECERT", "CHECKED");
			if(strtolower($temprow['sslmeth']) == "novalidate-cert")
				$smarty->assign("NOVALIDATECERT", "CHECKED");

			if($temprow['int_mailer'] == "1")
				$smarty->assign("INT_MAILER_USE", "CHECKED");
			else
				$smarty->assign("INT_MAILER_NOUSE", "CHECKED");
			if(strtolower($temprow['box_refresh']) == "60000")
	                        $smarty->assign("BOX_OPT1", " SELECTED");
			if(strtolower($temprow['box_refresh']) == "120000")
			        $smarty->assign("BOX_OPT2", " SELECTED");
			if(strtolower($temprow['box_refresh']) == "180000")
			        $smarty->assign("BOX_OPT3", " SELECTED");
			if(strtolower($temprow['box_refresh']) == "240000")
			        $smarty->assign("BOX_OPT4", " SELECTED");
			if(strtolower($temprow['box_refresh']) == "300000")
			        $smarty->assign("BOX_OPT5", " SELECTED");
		}
	}
}
$qry_res = $adb->pquery("select * from vtiger_mail_accounts where user_id=?", array($current_user->id));
$count = $adb->num_rows($qry_res);
if($count > 0)
	$field = '<input name="server_password" value="*****" class="detailedViewTextBox" onfocus="this.className=\'detailedViewTextBoxOn\'" onblur="this.className=\'detailedViewTextBox\'" type="password">';//"<input title='".$mod_strings['LBL_CHANGE_PASSWORD_BUTTON_TITLE']."' accessKey='".$mod_strings['LBL_CHANGE_PASSWORD_BUTTON_KEY']."' class='crmButton password small' LANGUAGE=javascript onclick='return window.open(\"index.php?module=Users&action=ChangePassword&form=EditView&mail_accounts=true\",\"test\",\"width=320,height=200,resizable=no,scrollbars=0, toolbar=no, titlebar=no, left=200, top=226, screenX=100, screenY=126\");' type='button' name='password' value='".$mod_strings['LBL_CHANGE_PASSWORD_BUTTON_LABEL']."'>";
else
	$field = '<input name="server_password" value="" class="detailedViewTextBox" onfocus="this.className=\'detailedViewTextBoxOn\'" onblur="this.className=\'detailedViewTextBox\'" type="password">';
$smarty->assign('CHANGE_PW_BUTTON',$field);	

$return_module = vtlib_purify($_REQUEST['return_module']);
if(empty($return_module)) $return_module = 'Settings';
else $return_module = htmlspecialchars($return_module, ENT_QUOTES, $default_charset);

$return_action = vtlib_purify($_REQUEST['return_action']);
if(empty($return_action)) $return_action = 'index';
else $return_action = htmlspecialchars($return_action, ENT_QUOTES, $default_charset);

$smarty->assign("RETURN_MODULE",$return_module);
$smarty->assign("RETURN_ACTION",$return_action);
$smarty->assign("JAVASCRIPT", get_validate_record_js());
$smarty->assign("USERID", $current_user->id);

$smarty->display('AddMailAccount.tpl');

?>