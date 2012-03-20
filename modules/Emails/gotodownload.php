<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

$msgno = $_REQUEST['msgno'];
$download = $_REQUEST['download'];
$file = $_REQUEST['file'];

$ServerName = "{localhost/imap:143/notls}INBOX"; // For a IMAP connection    (PORT 143)
$UserName = "user";
$PassWord = "password";
$mbox = imap_open($ServerName, $UserName,$PassWord) or die("Could not open Mailbox - try again later!");

if ($download == "1") 
{
	echo "attribute=";
	print_r($att);
	$strFileName = $att[$file]->parameters[0]->value;
	$strFileType = strrev(substr(strrev($strFileName),0,4));
	$fileContent = imap_fetchbody($mbox,$msgno,$file+2);
}

/**     Function to download the File
 *	@param string $strFileType - File Type
 *	@param string $strFileName - File Name
 *	@param string $fileContents- File contents
**/
function downloadFile($strFileType,$strFileName,$fileContent) 
{
	$ContentType = "application/octet-stream";

	if ($strFileType == ".asf") 
	$ContentType = "video/x-ms-asf";
	if ($strFileType == ".avi")
	$ContentType = "video/avi";
	if ($strFileType == ".doc")
	$ContentType = "application/msword";
	if ($strFileType == ".zip")
	$ContentType = "application/zip";
	if ($strFileType == ".xls")
	$ContentType = "application/vnd.ms-excel";
	if ($strFileType == ".gif")
	$ContentType = "image/gif";
	if ($strFileType == ".jpg" || $strFileType == "jpeg")
	$ContentType = "image/jpeg";
	if ($strFileType == ".wav")
	$ContentType = "audio/wav";
	if ($strFileType == ".mp3")
	$ContentType = "audio/mpeg3";
	if ($strFileType == ".mpg" || $strFileType == "mpeg")
	$ContentType = "video/mpeg";
	if ($strFileType == ".rtf")
	$ContentType = "application/rtf";
	if ($strFileType == ".htm" || $strFileType == "html")
	$ContentType = "text/html";
	if ($strFileType == ".xml") 
	$ContentType = "text/xml";
	if ($strFileType == ".xsl") 
	$ContentType = "text/xsl";
	if ($strFileType == ".css") 
	$ContentType = "text/css";
	if ($strFileType == ".php") 
	$ContentType = "text/php";
	if ($strFileType == ".asp") 
	$ContentType = "text/asp";
	if ($strFileType == ".pdf")
	$ContentType = "application/pdf";

	header ("Content-Type: $ContentType"); 
	header("Cache-Control: private");
	header("Content-Description: PHP Generated Data");
	header ("Content-Disposition: attachment; filename=$strFileName");    
	echo imap_base64($fileContent);
	#echo base64_decode($fileContent);
}
?>
