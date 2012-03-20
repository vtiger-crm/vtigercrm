<?php
/*
 * $Header: /cvsroot/nocc/nocc/webmail/download.php,v 1.38 2005/12/15 20:10:47 goddess_skuld Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * File for downloading the attachments
 */
require_once('modules/Webmails/MailBox.php');

if(isset($_REQUEST["mailbox"]) && $_REQUEST["mailbox"] != "")
{
	        $mailbox=$_REQUEST["mailbox"];
}
else
{
	        $mailbox="INBOX";
}
$MailBox = new MailBox($mailbox);
$mail = $MailBox->mbox;

if(!isset($HTTP_USER_AGENT))
	$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
$mailid = $_REQUEST['mailid'];
$mime = $_REQUEST['mime'];
$filename = $_REQUEST['filename'];
$transfer = $_REQUEST['transfer'];
$part = $_REQUEST['part'];
$filename = base64_decode($filename);
$filename = preg_replace('/[\\/:\*\?"<>\|;]/', '_', str_replace('&#32;', ' ', $filename));
$isIE = $isIE6 = 0;
// Set correct http headers.
// Thanks to Squirrelmail folks :-)
if (strstr($HTTP_USER_AGENT, 'compatible; MSIE ') !== false &&
  strstr($HTTP_USER_AGENT, 'Opera') === false) {
    $isIE = 1;
}

if (strstr($HTTP_USER_AGENT, 'compatible; MSIE 6') !== false &&
  strstr($HTTP_USER_AGENT, 'Opera') === false) {
    $isIE6 = 1;
}

if ($isIE) {
    $filename=rawurlencode($filename);
    header ("Pragma: public");
    header ("Cache-Control: no-store, max-age=0, no-cache, must-revalidate"); // HTTP/1.1
    header ("Cache-Control: post-check=0, pre-check=0", false);
    header ("Cache-Control: private");

    //set the inline header for IE, we'll add the attachment header later if we need it
    header ("Content-Disposition: inline; filename=$filename");
}

header ("Content-Type: application/octet-stream; name=\"$filename\"");
header ("Content-Disposition: attachment; filename=\"$filename\"");

if ($isIE && !$isIE6) {
    header ("Content-Type: application/download; name=\"$filename\"");
} else {
    header ("Content-Type: application/octet-stream; name=\"$filename\"");
}

$file = imap_fetchbody($mail,$mailid,$part);

if ($transfer == 'BASE64')
    $file = imap_base64($file);
elseif($transfer == 'QUOTED-PRINTABLE')
    $file = imap_qprint($file);

imap_close($mail);

header('Content-Length: ' . strlen($file));
echo ($file);
?>
