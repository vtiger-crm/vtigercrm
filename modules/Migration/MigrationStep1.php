<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

global $current_user;
if($current_user->is_admin != 'on')
{
	die("<br><br><center>".$app_strings['LBL_PERMISSION']." <a href='javascript:window.history.back()'>".$app_strings['LBL_GO_BACK'].".</a></center>");
}

//To get the Current installed MySQL path
include("connection.php");
$vtiger_home = $_ENV["VTIGER_HOME"];
$mysqldir = $mysql_dir;

if(is_file($mysqldir."/bin/mysqldump"))
{
	$installed_mysql_path = $mysqldir."/bin/mysqldump";
	$getmysqlpath = 0;
}
elseif(is_file($vtiger_home."/mysql/bin/mysqldump"))
{
	$installed_mysql_path = $vtiger_home."/mysql/bin/mysqldump";
	$getmysqlpath = 0;
}
elseif(substr($_ENV["OS"],0,3) == "Win")
{
	if(is_file($vtiger_home.'\mysql\bin\mysql.exe'))
	{
		$installed_mysql_path = $vtiger_home.'\mysql\bin\mysqldump.exe';
		@session_unregister('set_server_mysql_path');
		$_SESSION['set_server_mysql_path'] = $vtiger_home.'\mysql\bin';
		$getmysqlpath = 0;
	}
	else
	{
		$getmysqlpath = 1;
	}
}
else
{
	$getmysqlpath = 1;

	if($_REQUEST['migration_option'] == 'alter_db_details')
		$showmysqlpath = 'none';
	else
		$showmysqlpath = 'block';
}



require_once('Smarty_setup.php');

global $app_strings,$app_list_strings,$mod_strings,$theme,$currentModule;

$smarty = new vtigerCRM_Smarty();


$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("MODULE","Migration");

$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

$smarty->assign("DB_DETAILS_CHECKED", 'checked');
$smarty->assign("SHOW_DB_DETAILS", 'block');

//Based on this $getmysqlpath variable we should get the mysql path from the user
$smarty->assign("GET_MYSQL_PATH",$getmysqlpath);
$smarty->assign("SHOW_MYSQL_PATH",$showmysqlpath);

//this is to set the entered values when we could not proceed the migration and return to step1
if($_REQUEST['migration_option'] != '')
{
	if($_REQUEST['migration_option'] == 'db_details')
	{
		if($_REQUEST['old_host_name'] != '')
			$smarty->assign("OLD_HOST_NAME", $_REQUEST['old_host_name']);
		if($_REQUEST['old_port_no'] != '')
			$smarty->assign("OLD_PORT_NO", $_REQUEST['old_port_no']);
		if($_REQUEST['old_mysql_username'] != '')
			$smarty->assign("OLD_MYSQL_USERNAME", $_REQUEST['old_mysql_username']);
		if($_REQUEST['old_mysql_password'] != '')
			$smarty->assign("OLD_MYSQL_PASSWORD", $_REQUEST['old_mysql_password']);
		if($_REQUEST['old_dbname'] != '')
			$smarty->assign("OLD_DBNAME", $_REQUEST['old_dbname']);

		if($_REQUEST['server_mysql_path'] != '')
			$smarty->assign("SERVER_MYSQL_PATH", $_REQUEST['server_mysql_path']);
	}
	elseif($_REQUEST['migration_option'] == 'dump_details')
	{
		$smarty->assign("DUMP_DETAILS_CHECKED", 'checked');
		$smarty->assign("DB_DETAILS_CHECKED", '');

		$smarty->assign("SHOW_DUMP_DETAILS", 'block');
		$smarty->assign("SHOW_DB_DETAILS", 'none');
	}
	else
	{
		if($_REQUEST['alter_old_host_name'] != '')
			$smarty->assign("ALTER_OLD_HOST_NAME", $_REQUEST['alter_old_host_name']);
		if($_REQUEST['alter_old_port_no'] != '')
			$smarty->assign("ALTER_OLD_PORT_NO", $_REQUEST['alter_old_port_no']);
		if($_REQUEST['alter_old_mysql_username'] != '')
			$smarty->assign("ALTER_OLD_MYSQL_USERNAME", $_REQUEST['alter_old_mysql_username']);
		if($_REQUEST['alter_old_mysql_password'] != '')
			$smarty->assign("ALTER_OLD_MYSQL_PASSWORD", $_REQUEST['alter_old_mysql_password']);
		if($_REQUEST['alter_old_dbname'] != '')
			$smarty->assign("ALTER_OLD_DBNAME", $_REQUEST['alter_old_dbname']);

		if($_REQUEST['server_mysql_path'] != '')
			$smarty->assign("SERVER_MYSQL_PATH", $_REQUEST['server_mysql_path']);

		$smarty->assign("DB_DETAILS_CHECKED", '');
		$smarty->assign("DUMP_DETAILS_CHECKED", '');
		$smarty->assign("ALTER_DB_DETAILS_CHECKED", 'checked');

		$smarty->assign("SHOW_DB_DETAILS", 'none');
		$smarty->assign("SHOW_DUMP_DETAILS", 'none');
		$smarty->assign("SHOW_ALTER_DB_DETAILS", 'block');
	}
}

$smarty->display("MigrationStep1.tpl");

?>