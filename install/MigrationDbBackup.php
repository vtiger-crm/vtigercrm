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
session_start();
if($_SESSION['authentication_key'] != $_REQUEST['auth_key']) {
	die($installationStrings['ERR_NOT_AUTHORIZED_TO_PERFORM_THE_OPERATION']);
}
if(!empty($_REQUEST['rootUserName'])) $_SESSION['migration_info']['root_username'] = $_REQUEST['rootUserName'];

if(!empty($_REQUEST['rootPassword'])) {
	$_SESSION['migration_info']['root_password'] = $_REQUEST['rootPassword'];
} else {
	$_SESSION['migration_info']['root_password'] = '';
}


require_once 'include/db_backup/DatabaseBackup.php';
require_once 'include/db_backup/Targets/Response.php';
require_once 'include/db_backup/Targets/Database.php';
require_once 'include/db_backup/Source/MysqlSource.php';
$mode = $_REQUEST['mode'];

$source_directory = $_SESSION['migration_info']['source_directory'];
require_once $source_directory.'config.inc.php';
$createDB = $_REQUEST['createDB'];
if(empty($createDB)){
	$createDB = false;
}else{
	$createDB = true;
}

$hostName = $dbconfig['db_server'].$dbconfig['db_port'];
$username = $dbconfig['db_username'];
$password = $dbconfig['db_password'];
$dbName = $dbconfig['db_name'];
$sourceConfig = new DatabaseConfig($hostName,$username,$password,$dbName);
$source = new MysqlSource($sourceConfig);
if(strtolower($mode) == 'dump'){
	header('Content-type: text/plain; charset=UTF-8');
	$responseDest = new Response($sourceConfig, true);
	$dbBackup = new DatabaseBackup($source, $responseDest);
	$dbBackup->backup();
}else{
	$targetName = $_REQUEST['newDatabaseName'];
	try{
		if(!empty($targetName) && $dbName != $targetName){
			$rootUserName = $_SESSION['migration_info']['root_username'];
			$rootPassword = $_SESSION['migration_info']['root_password'];
			$destConfig = new DatabaseConfig($hostName,$username,$password,$targetName, 'mysql', 
					$rootUserName,$rootPassword);
			$databaseDest = new Database($destConfig, $createDB, true);
			$dbBackup = new DatabaseBackup($source, $databaseDest);
			$dbBackup->backup();
			$_SESSION['migration_info']['new_dbname'] = $targetName;
			if ($createDB && $databaseDest->isUTF8SupportEnabled()) {
				$_SESSION['config_file_info']['vt_charset'] = "UTF-8";
			} else {
				$_SESSION['config_file_info']['vt_charset'] = "ISO-8859-1";
			}
			echo 'true';
			return;
		}
		echo 'false';
	}catch (DatabaseBackupException $e){
		echo 'false';
	}catch(Exception $e){
		echo 'false';
	}
}

?>