<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include('adodb/adodb.inc.php');

if(version_compare(phpversion(), '5.0') < 0) {
	$serverPhpVersion = phpversion();
	require_once('phpversionfail.php');
	die();
}

/** Function to  return a string with backslashes stripped off
 * @param $value -- value:: Type string
 * @returns $value -- value:: Type string array
 */
 function stripslashes_checkstrings($value){
 	if(is_string($value)){
 		return stripslashes($value);
 	}
 	return $value;

 }
 if(get_magic_quotes_gpc() == 1){
 	$_REQUEST = array_map("stripslashes_checkstrings", $_REQUEST);
	$_POST = array_map("stripslashes_checkstrings", $_POST);
	$_GET = array_map("stripslashes_checkstrings", $_GET);

}

require_once('include/install/language/en_us.lang.php');
require_once('include/install/resources/utils.php');
require_once('vtigerversion.php');	
global $installationStrings, $vtiger_current_version;
	
@include_once('config.db.php');
global $dbconfig, $vtconfig;
if(empty($_REQUEST['file']) && is_array($vtconfig) && $vtconfig['quickbuild'] == 'true') {
	$the_file = 'BuildInstallation.php';
} elseif (!empty($_REQUEST['file'])) $the_file = $_REQUEST['file'];
else $the_file = "welcome.php";

Common_Install_Wizard_Utils::checkFileAccessForInclusion("install/".$the_file);
include("install/".$the_file);

?>