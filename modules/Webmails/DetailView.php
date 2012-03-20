<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/utils/UserInfoUtil.php');
require_once("modules/Webmails/Webmails.php");
require_once("modules/Webmails/MailBox.php");

global $app_strings;
global $mod_strings;

if(isset($_REQUEST["mailbox"]) && $_REQUEST["mailbox"] != "") { $mailbox=vtlib_purify($_REQUEST["mailbox"]);} else { $mailbox = "INBOX";}
if(isset($_REQUEST["mailid"]) && $_REQUEST["mailid"] != "") { $mailid=vtlib_purify($_REQUEST["mailid"]);} else { echo "ERROR";flush();exit();}

global $MailBox;
$MailBox = new MailBox($mailbox);

$webmail = new Webmails($MailBox->mbox,$mailid);
$elist = $MailBox->mailList["overview"][($mailid-1)];

echo '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="previewWindow"><tr>';

echo '<td>';

echo '</td></tr>';
$array_tab = Array();
$webmail->loadMail($array_tab);

echo '<tr><td align="center"><iframe src="index.php?module=Webmails&action=body&fullview=true&mailid='.$mailid.'&mailbox='.$mailbox.'" width="100%" height="600" frameborder="0" style="border:1px solid gray">'.$mod_strings['LBL_NO_IFRAMES_SUPPORTED'].'</iframe></td></tr>';

echo '</table>';
?>