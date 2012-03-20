<?php
/*
 * $Header: /cvsroot/nocc/nocc/webmail/get_img.php,v 1.27 2005/05/09 13:32:51 goddess_skuld Exp $
 *
 * Copyright 2001 Nicolas Chalanset <nicocha@free.fr>
 * Copyright 2001 Olivier Cahagne <cahagn_o@epita.fr>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
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
$mailid = $_REQUEST['mail'];
$num = $_REQUEST['num'];
$transfer = $_REQUEST['transfer'];
$mime = $_REQUEST['mime'];
$img = imap_fetchbody($mail,$mailid,$num);
if ($transfer == 'BASE64')
    $img = imap_base64($img);
elseif ($transfer == 'QUOTED-PRINTABLE')
    $img = imap_qprint($img);
imap_close($mail);
header('Content-type: image/'.$mime);
echo $img;
?>
