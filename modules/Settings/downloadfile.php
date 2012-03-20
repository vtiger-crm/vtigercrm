<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('config.php');
require_once('include/database/PearDatabase.php');

global $adb;
global $mod_strings, $default_charset;

$dbQuery = "SELECT * FROM vtiger_organizationdetails ";

$result = $adb->pquery($dbQuery, array()) or die("Couldn't get file list");
if($adb->num_rows($result) == 1)
{
	$name = @$adb->query_result($result, 0, "logoname");
	$fileContent = @$adb->query_result($result, 0, "logo");
	$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
	header("Cache-Control: private");
	header("Content-Disposition: attachment; filename=$name");
	header("Content-Description: PHP Generated Data");
	echo base64_decode($fileContent);
} else {
	echo $mod_strings['LBL.RECORD_NOEXIST'];
}
?>