<?php
/********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
* 
 ********************************************************************************/

require_once('config.php');
require_once('include/database/PearDatabase.php');

global $adb;
global $fileId, $default_charset, $app_strings;

$attachmentsid = $_REQUEST['fileid'];
$entityid = $_REQUEST['entityid'];

$returnmodule=$_REQUEST['return_module'];

$deletecheck = false;
if(!empty($entityid)) $deletecheck = $adb->pquery("SELECT deleted FROM vtiger_crmentity WHERE crmid=?", array($entityid));
if(!empty($deletecheck) && $adb->query_result($deletecheck, 0, 'deleted') == 1) {
	
	echo $app_strings['LBL_RECORD_DELETE'];
	
} else {

	$dbQuery = "SELECT * FROM vtiger_attachments WHERE attachmentsid = ?" ;
	
	$result = $adb->pquery($dbQuery, array($attachmentsid)) or die("Couldn't get file list");
	if($adb->num_rows($result) == 1)
	{
		$fileType = @$adb->query_result($result, 0, "type");
		$name = @$adb->query_result($result, 0, "name");
		$filepath = @$adb->query_result($result, 0, "path");
		$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
		$saved_filename = $attachmentsid."_".$name;
		$disk_file_size = filesize($filepath.$saved_filename);
		$filesize = $disk_file_size + ($disk_file_size % 1024);
		$fileContent = fread(fopen($filepath.$saved_filename, "r"), $filesize);
	
		header("Content-type: $fileType");
		header("Pragma: public");
		header("Cache-Control: private");
		header("Content-Disposition: attachment; filename=$name");
		header("Content-Description: PHP Generated Data");
		echo $fileContent;
	}
	else
	{
		echo $app_strings['LBL_RECORD_NOT_FOUND'];
	}
}
?>
