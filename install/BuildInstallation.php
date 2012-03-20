<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/

/**
 * @author MAK
 */

@include_once('config.db.php');
global $dbconfig, $vtiger_current_version, $vtconfig;

$hostname = $_SERVER['SERVER_NAME'];
$web_root = ($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"]:$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
$web_root .= $_SERVER["REQUEST_URI"];
$web_root = str_replace("/install.php", "", $web_root);
$web_root = "http://".$web_root;

$current_dir = pathinfo(dirname(__FILE__));
$current_dir = $current_dir['dirname']."/";
$cache_dir = "cache/";

session_start();

!isset($_REQUEST['hostName']) ? $host_name= $dbconfig['db_server'].':'.$dbconfig['db_port'] :
	$host_name = $_REQUEST['hostName'];
!isset($_REQUEST['demoData']) ? $demoData= $vtconfig['demoData'] : $demoData =
	$_REQUEST['demoData'];
!isset($_REQUEST['currencyName']) ? $currencyName = preg_replace('/\s+$/', '',
		$vtconfig['currencyName']) : $currencyName = $_REQUEST['currencyName'];
!isset($_REQUEST['adminEmail']) ? $adminEmail = $vtconfig['adminEmail'] : $adminEmail =
	$_REQUEST['adminEmail'];
!isset($_REQUEST['adminPwd']) ? $adminPwd = $vtconfig['adminPwd'] : $adminPwd =
	$_REQUEST['adminPwd'];
!isset($_REQUEST['standarduserEmail']) ? $standarduserEmail = $vtconfig['standarduserEmail'] :
	$standarduserEmail = $_REQUEST['standarduserEmail'];
!isset($_REQUEST['standarduserPwd']) ? $standarduserPwd = $vtconfig['standarduserPwd'] :
	$standarduserPwd = $_REQUEST['standarduserPwd'];
!isset($_REQUEST['dbUsername']) ? $dbUsername = $dbconfig['db_username'] : $dbUsername =
	$_REQUEST['dbUsername'];
!isset($_REQUEST['dbPassword']) ? $dbPassword = $dbconfig['db_password'] : $dbPassword =
	$_REQUEST['dbPassword'];
!isset($_REQUEST['dbType']) ? $dbType = $dbconfig['db_type'] : $dbType = $_REQUEST['dbType'];
!isset($_REQUEST['dbName']) ? $dbName = $dbconfig['db_name'] : $dbName = $_REQUEST['dbName'];

session_start();
$create_db = false;
if(isset($_REQUEST['check_createdb']) && $_REQUEST['check_createdb'] == 'on') {
	$create_db = true;
}

$_SESSION['config_file_info']['db_hostname'] = $host_name;
$_SESSION['config_file_info']['db_username'] = $dbUsername;
$_SESSION['config_file_info']['db_password'] = $dbPassword;
$_SESSION['config_file_info']['db_name'] = $dbName;
$_SESSION['config_file_info']['db_type'] = $dbType;
$_SESSION['config_file_info']['site_URL']= $web_root;
$_SESSION['config_file_info']['root_directory'] = $current_dir;
$_SESSION['config_file_info']['currency_name'] = $currencyName;
$_SESSION['config_file_info']['admin_email'] = $adminEmail;

$_SESSION['installation_info']['currency_name'] = $currencyName;
$_SESSION['installation_info']['check_createdb'] = $create_db;
if (!isset($_REQUEST['root_user'])) {
	$_SESSION['installation_info']['root_user'] = $dbUsername;
} else {
	$_SESSION['installation_info']['root_user'] = $_REQUEST['root_user'];
}
if (!isset($_REQUEST['root_password'])) {
	$_SESSION['installation_info']['root_password'] = $dbPassword;
} else {
	$_SESSION['installation_info']['root_password'] = $_REQUEST['root_password'];
}
$_SESSION['installation_info']['admin_email']= $adminEmail;
$_SESSION['installation_info']['admin_password'] = $adminPwd;
$_SESSION['installation_info']['standarduser_email']= $standarduserEmail;
$_SESSION['installation_info']['standarduser_password'] = $standarduserPwd;

if (isset($_REQUEST['create_utf8_db'])) {
	$_SESSION['installation_info']['create_utf8_db'] = 'true';
} else {
	$_SESSION['installation_info']['create_utf8_db'] = 'false';
}
$_SESSION['config_file_info']['vt_charset']= 'UTF-8';

if (isset($_REQUEST['db_populate']))
	$_SESSION['installation_info']['db_populate'] = 'true';
else
	$_SESSION['installation_info']['db_populate'] = ($demoData == '1')? 'true': 'false';

require_once('modules/Utilities/Currencies.php');
if(isset($currencyName)){
	$_SESSION['installation_info']['currency_code'] = $currencies[$currencyName][0];
	$_SESSION['installation_info']['currency_symbol'] = $currencies[$currencyName][1];
}

require "install/SelectOptionalModules.php";

?>