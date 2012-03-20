<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is FOSS Labs.
 * Portions created by FOSS Labs are Copyright (C) FOSS Labs.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

include('config.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
require_once('modules/Webmails/Webmails.php');
require_once('modules/Webmails/MailBox.php');

global $MailBox, $mod_strings,$theme;
$theme_path="themes/".$theme."/style.css";

$MailBox = new MailBox($_REQUEST["mailbox"]);

$mailid=vtlib_purify($_REQUEST["mailid"]);
$num=vtlib_purify($_REQUEST["num"]);

$email = new Webmails($MailBox->mbox,$mailid);
$attach_tab = Array();
$email->loadMail($attach_tab);
echo "<html><head><title>".$mod_strings['LBL_ATTACHMENTS']."</title>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$email->charsets."\">\n";
echo "<script src='modules/Webmails/Webmails.js' type='text/javascript'></script>";
echo "<link REL='SHORTCUT ICON' HREF='include/images/vtigercrm_icon.ico'>";	
echo "<style type='text/css'>@import url('$theme_path');</style>";
echo "</head><body>";

echo "<table class='small' width='100%' cellspacing='1' cellpadding='0' border='0' style='font-size:18px'>";
echo "<tr><td><table border=0 cellspacing=0 cellpadding=0 width=100% class='mailClientWriteEmailHeader'><tr><td >".$mod_strings['LBL_ATTACHMENTS']."</td></tr></table></td></tr>";

if(count($email->attname) <= 0)
	echo "<tr align='center'><td nowrap>".$mod_strings['LBL_NO_ATTACHMENTS']."</td></tr>";
else{
	for($i=0;$i<count($email->attname);$i++){
        	$attachment_links .= "&nbsp;&nbsp;&nbsp;&nbsp;".$email->anchor_arr[$i].$email->attname[$i]."</a></br>";
	}
	echo "<tr><td><table class='small' width='100%' cellspacing='1' cellpadding='0' border='0' style='font-size:13px'><tr><td width='90%'>".$mod_strings['LBL_THERE_ARE']." ".count($email->attname)." ".$mod_strings['LBL_ATTACHMENTS_TO_CHOOSE'].":</td></tr><br>";
	echo "<tr><td width='100%'>".$attachment_links."</div></td></tr>";
	echo "</td></tr></table>";
}

echo "</table>";

?>