<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/

global $current_user;
if($current_user->is_admin != 'on')
{
	die("<br><br><center>".$app_strings['LBL_PERMISSION']." <a href='javascript:window.history.back()'>".$app_strings['LBL_GO_BACK'].".</a></center>");
}

include("config.inc.php");
$migration_log = '';

//Added for Migration Log
$migrationlog =& LoggerManager::getLogger('MIGRATION');

//new database values get from the current vtigerCRM's config.php
global $dbconfig;
$new_host_name = $dbconfig['db_hostname'];
$new_mysql_username = $dbconfig['db_username'];
$new_mysql_password = $dbconfig['db_password'];
$new_dbname = $dbconfig['db_name'];

//this is to check whether the user_privileges folder has write permission
if(!is_writable($root_directory."user_privileges/"))
{
	echo "<br><font color='red'><b>Please give read/write permission to user_privileges folder.</b></font>";
	include("modules/Migration/MigrationStep1.php");
	exit;
}

//this is to check whether the mysql path is needed and has been entered or not
if($_REQUEST['getmysqlpath'] == 1 && $_REQUEST['server_mysql_path'] != '')
{
	$mysql_path_found = false;
	$server_mysql_path = $_REQUEST['server_mysql_path'];
	if(is_file($server_mysql_path."/mysqldump"))
	{
		$mysql_path_found = true;
		@session_unregister('set_server_mysql_path');
		$_SESSION['set_server_mysql_path'] = $server_mysql_path;
		$migration_log .='MySQL Dump file has found in ==> '.$server_mysql_path;
	}
	elseif(substr($_ENV["OS"],0,3) == "Win" && is_file($server_mysql_path."\mysqldump.exe"))
	{
		$mysql_path_found = true;
		@session_unregister('set_server_mysql_path');
		$_SESSION['set_server_mysql_path'] = $server_mysql_path;
		$migration_log .='MySQL Dump file has found in ==> '.$server_mysql_path;
	}

	if(!$mysql_path_found && $_REQUEST['migration_option'] != 'alter_db_details')
	{
		//header("Location: index.php?module=Migration&action=MigrationStep1&parenttab=Settings");
		echo '<br><font color="red"><b>MySQL dump file is not exist in the specified MySQL Server Path</b></font>';
		include("modules/Migration/MigrationStep1.php");
		exit;
	}
}
$migrationlog->debug("$migration_log");
//echo '<br>Proceed with migration';


$migrationlog->debug("Migration Option - ".$_REQUEST['migration_option']);
include("modules/Migration/Migration.php");
if($_REQUEST['migration_option'] == 'db_details')
{
	//Source MySQL database details have been given. 
	//old database values
	$old_host_name = $_REQUEST['old_host_name'];
	$old_mysql_port = $_REQUEST['old_port_no'];
	$old_mysql_username = $_REQUEST['old_mysql_username'];
	$old_mysql_password = $_REQUEST['old_mysql_password'];
	$old_dbname = $_REQUEST['old_dbname'];

	$migrationlog->debug("old host name = ".$old_host_name);
	$migrationlog->debug("old MySQL port = ".$old_mysql_port);
	$migrationlog->debug("old MySQL username = ".$old_mysql_username);
	$migrationlog->debug("old MySQL password = ".$old_mysql_password);
	$migrationlog->debug("old db name = ".$old_dbname);

	//make a connection with the old database
	$oldconn = @mysql_connect($old_host_name.":".$old_mysql_port,$old_mysql_username,$old_mysql_password);

	//make a connection with the new database
	$newconn = @mysql_connect($new_host_name,$new_mysql_username,$new_mysql_password);

	if(!$oldconn)
	{
		echo '<br><font color="red"><b>  Source Database Server cannot be connected</b></font>';
		$continue1 = 0;
	}
	elseif(!$newconn)
	{
		echo '<br><font color="red"><b>  Current working Database Server cannot be connected</b></font>';
		$continue1 = 0;
	}
	else
	{
		$migration_log .= ' Database Servers can be connected. Can proceed with migration';
		$migrationlog->debug("Database Servers can be connected. continue1 = 1");
		$continue1 = 1;
	}

	if($continue1 == 1)
	{
		//check whether the specified databases are exist or not
		$olddb_exist = @mysql_select_db($old_dbname,$oldconn);
		//$newdb_exist = @mysql_select_db($new_dbname,$oldconn);
		if(!$olddb_exist)
		{
			echo '<br><font color="red"><b> Source Database does not exist</b></font>';
			$continue2 = 0;
		}
		//elseif(!$newdb_exist)
		//{
			//	echo '<br> New Database is not exist';
			//	$continue2 = 0;
		//}
		else
		{
			$migration_log .= '<br> Required databases exist';
			$migrationlog->debug("Required databases exist. continue2 = 1");
			$continue2 = 1;
		}
	}

	if($continue2 == 1)
	{
		//Check whether the vtiger_table are exist in the databases or not
		$old_tables = @mysql_num_rows(mysql_list_tables($old_dbname,$oldconn));
		//$new_tables = @mysql_num_rows(mysql_list_tables($new_dbname));

		if(!$old_tables)
		{
			echo '<br><font color="red"><b> Tables do not exist in the Source Database</b></font>';
			$continue3 = 0;
		}
		/*	if(!$new_tables)
		{
			echo '<br> Tables are not exist in New Database';
			$continue3 = 0;
		}
		*/
		else
		{
			$migration_log .= '<br> Tables exist in both the Databases';
			$migrationlog->debug("Tables exist in the database. continue3 = 1");
			$continue3 = 1;
		}
	}

	//To check whether the two databases are same
	if($continue3 == 1)
	{
		$new_host = explode(":",$new_host_name);

		if($old_host_name == $new_host[0] && $old_mysql_port == $new_host[1] && $old_mysql_username == $new_mysql_username && $old_mysql_password == $new_mysql_password && $old_dbname == $new_dbname)
		{
			echo '<br> Both the databases are the same.';
			$continue4 = 1;//change the value to 0 if you don't want to proceed with the same database
			$same_databases = 1;
		}
		else
		{
			$continue4 = 1;
			$same_databases = 0;
		}
	}

	//$continue1 -- Database servers can be connected
	//$continue2 -- Database exists in the servers
	//$continue3 -- Tables are exist in the databases
	//$continue4 -- Two databases are not same

	if($continue1 == 1 && $continue2 == 1 && $continue3 == 1 && $continue4 == 1)
	{
		$migrationlog->debug("Going to migrate...");

		$new_host = explode(":",$new_host_name);

		$conn = new PearDatabase("mysql",$new_host_name,$new_dbname,$new_mysql_username,$new_mysql_password);
		$conn->connect();

		$migrationlog->debug("MICKIE ==> Option = DB details. From the given DB details we will migrate.");
		
		@session_unregister('migration_log');
		$_SESSION['migration_log'] = $migration_log;
		if($conn)
		{
			$migrationlog->debug("Pear Database object created. Going to create migration object.");

			$obj = new Migration('',$conn);
			$obj->setOldDatabaseParams($old_host_name,$old_mysql_port,$old_mysql_username,$old_mysql_password,$old_dbname);
			$obj->setNewDatabaseParams($new_host[0],$new_host[1],$new_mysql_username,$new_mysql_password,$new_dbname);
			$obj->migrate($same_databases,'dbsource');
		}
		else
		{
			echo '<br><font color="red"><b> Cannot make a connection with the current database setup.</b></font>';
			include("modules/Migration/MigrationStep1.php");
		}
	}
	else
	{
		echo '<br><font color="red"><b>ERROR!!!!!!Please check the input values, unable to proceed.</b></font>';
		include("modules/Migration/MigrationStep1.php");
	}


}
elseif($_REQUEST['migration_option'] == 'dump_details')
{
	$old_dump_details = $_FILES['old_dump_filename'];
	$old_dump_filename = $old_dump_details['name'];
	$migrationlog->debug("Dump file name ==> $old_dump_filename");

	//MySQL Dump file details has given
	if($old_dump_details['size'] != 0 && is_file($old_dump_details['tmp_name']))
	{
		$gotostep1 = 0;
		//apply this dump file to the new database
		$checkDumpFileAndApply = 1;
	}
	else
	{
		$gotostep1 = 1;
		if($old_dump_details['error'] == 2 || $old_dump_details['error'] == 1)
		{
			$invalid_dump = 1;
			$errormessage = "Sorry, the uploaded file exceeds the maximum filesize limit. Try other option.";
		}
		elseif($old_dump_details['error'] == 4 || $old_dump_details['size'] == 0 || !is_file($old_dump_details['tmp_name']))
		{
			$invalid_dump = 1;
			$errormessage = "Please enter a valid Dump file.";
		}
		$migrationlog->debug("Dump file error no = ".$old_dump_details['error']);
	}

	if($gotostep1 == 1)
	{
		if($invalid_dump == 1)
		{
			echo "<br><font color='red'><b> $errormessage</b></font>";
		}
		include("modules/Migration/MigrationStep1.php");
		exit;
	}

	if($checkDumpFileAndApply == 1)
	{
		//TODO - Check whether the given file is Dump file and then apply to the new database
		$migration_log .= '<br> Going to apply the Dump file to the new database.';

		//If the dump is valid and going to migrate then we should move the browsed dump file to root directory
		global $root_directory;
		move_uploaded_file($old_dump_details['tmp_name'],$root_directory.$old_dump_details['name']);

		include("config.inc.php");
		global $dbconfig;

		$new_host_name = $dbconfig['db_hostname'];
		$new_dbname = $dbconfig['db_name'];
		$new_mysql_username = $dbconfig['db_username'];
		$new_mysql_password = $dbconfig['db_password'];

		$conn = new PearDatabase("mysql",$new_host_name,$new_dbname,$new_mysql_username,$new_mysql_password);
		$conn->connect();

		$migrationlog->debug("MICKIE ==> Option = Dump File. Selected Dump File will be applied to the new database");
		
		@session_unregister('migration_log');
		$_SESSION['migration_log'] = $migration_log;
		if($conn)
		{
			$migrationlog->debug("Pear Database object created. Going to create migration object.");

			$obj = new Migration('',$conn);

			$new_host = explode(":",$new_host_name);
			$temp_new_host_name = $new_host[0];
			$new_mysql_port = $new_host[1];

			$obj->setNewDatabaseParams($new_host[0],$new_host[1],$new_mysql_username,$new_mysql_password,$new_dbname);
			$obj->migrate(0,'dumpsource',$old_dump_filename);
		}
	}

}
elseif($_REQUEST['migration_option'] == 'alter_db_details')
{
	//old database values
	$old_host_name = $_REQUEST['alter_old_host_name'];
	$old_mysql_port = $_REQUEST['alter_old_port_no'];
	$old_mysql_username = $_REQUEST['alter_old_mysql_username'];
	$old_mysql_password = $_REQUEST['alter_old_mysql_password'];
	$old_dbname = $_REQUEST['alter_old_dbname'];

	$migrationlog->debug("old host name = ".$old_host_name);
	$migrationlog->debug("old MySQL port = ".$old_mysql_port);
	$migrationlog->debug("old MySQL username = ".$old_mysql_username);
	$migrationlog->debug("old MySQL password = ".$old_mysql_password);
	$migrationlog->debug("old db name = ".$old_dbname);

	//make a connection with the old database
	$oldconn = @mysql_connect($old_host_name.":".$old_mysql_port,$old_mysql_username,$old_mysql_password);

	if(!$oldconn)
	{
		echo '<br><font color="red"><b>  Source Database Server cannot be connected</b></font>';
		$continue1 = 0;
	}
	else
	{
		$migration_log .= ' Database Server can be connected. Can proceed with migration';
		$migrationlog->debug("Database server connected. continue1 = 1");
		$continue1 = 1;
	}

	if($continue1 == 1)
	{
		//check whether the specified databases are exist or not
		$olddb_exist = @mysql_select_db($old_dbname,$oldconn);

		if(!$olddb_exist)
		{
			echo '<br><font color="red"><b> Source Database does not exist</b></font>';
			$continue2 = 0;
		}
		else
		{
			$migration_log .= '<br> Required database exist';
			$migrationlog->debug("Database exist. continue2 = 1");
			$continue2 = 1;
		}
	}

	if($continue2 == 1)
	{
		//Check whether the vtiger_table are exist in the databases or not
		$old_tables = @mysql_num_rows(mysql_list_tables($old_dbname,$oldconn));

		if(!$old_tables)
		{
			echo '<br><font color="red"><b> Tables do not exist in the Source Database</b></font>';
			$continue3 = 0;
		}
		else
		{
			$migration_log .= '<br> Tables exist in the Database';
			$migrationlog->debug("Tables exist. continue3 = 1");
			$continue3 = 1;
		}
	}

	//$continue1 -- Database server can be connected
	//$continue2 -- Database exists in the server
	//$continue3 -- Tables are exist in the database

	if($continue1 == 1 && $continue2 == 1 && $continue3 == 1)
	{

		$conn = new PearDatabase("mysql",$old_host_name.":".$old_mysql_port,$old_dbname,$old_mysql_username,$old_mysql_password);
		$conn->connect();

		$migrationlog->debug("MICKIE ==> Option = Alter DB details. From the given DB details we will migrate.");

		@session_unregister('migration_log');
		$_SESSION['migration_log'] = $migration_log;
		if($conn)
		{
			$migrationlog->debug("Database object created. Going to create Migration object");

			$obj = new Migration('',$conn);
			$obj->setOldDatabaseParams($old_host_name,$old_mysql_port,$old_mysql_username,$old_mysql_password,$old_dbname);
			//$obj->migrate($same_databases,'dbsource');
			$obj->modifyDatabase($conn);
		}
		else
		{
			echo '<br><font color="red"><b> Cannot make a connection with the current database setup</b></font>';
			include("modules/Migration/MigrationStep1.php");
		}
	}
	else
	{
		echo '<br><font color="red"><b>ERROR!!!!!!Please check the input values, unable to proceed.</b></font>';
		include("modules/Migration/MigrationStep1.php");
	}
}
else
{
	//Return to the MigrationStep1
	include("modules/Migration/MigrationStep1.php");
	exit;
}

?>
	<script>
		function displayMigrationSuccess()
		{
			document.getElementById("migration_image").style.display = "block";
			document.getElementById("migration_message1").innerHTML = "Migration has been completed";
			document.getElementById("migration_message2").innerHTML = "Your old data is now moved into new vtigerCRM";
		}
	</script>
	<script>
		displayMigrationSuccess();
	</script>

<?php


?>
