<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

//Migration Procedure
//Step : 1 => Take a dump of old database
//Step : 2 => Drop the New Database
//Step : 3 => Create the New Database
//Step : 4 => Put the old dump into the New Database
//Step : 5 => Modify the new database with the new changes

class Migration
{

	var $conn;
	var $oldconn;

	//Old Database Parameters
	var $old_hostname;
	var $old_mysql_port;
	var $old_mysql_username;
	var $old_mysql_password;
	var $old_dbname;

	//New Database Parameters
	var $new_hostname;
	var $new_mysql_port;
	var $new_mysql_username;
	var $new_mysql_password;
	var $new_dbname;

	/**	Constructor with old database and new database connections
	 */
	function Migration($old='',$new='')
	{
		global $migrationlog;
		$migrationlog->debug("Inside the constructor Migration.");
		
		$this->oldconn = $old;
		$this->conn = $new;
		$migrationlog->debug("Database Object has been created.");
	}

	/**	function used to set the Old database parameters in the migration object properties
	 *	@param string $hostname - old database hostname
	 *	@param int $mysql_port - old database mysql port number
	 *	@param string $mysql_username - old database mysql user name
	 *	@param string $mysql_password - old database mysql password
	 *	@param string $dbname - old database name
	 *	@return void
	 */
	function setOldDatabaseParams($hostname,$mysql_port,$mysql_username,$mysql_password,$dbname)
	{
		global $migrationlog;
		$migrationlog->debug("Inside the function setOldDatabaseParams($hostname,$mysql_port,$mysql_username,$mysql_password,$dbname)");
		
		$this->old_hostname = $hostname;
		$this->old_mysql_port = $mysql_port;
		$this->old_mysql_username = $mysql_username;
		$this->old_mysql_password = $mysql_password;
		$this->old_dbname = $dbname;
		
		$migrationlog->debug("Old Database Parameters has been set.");
	}

	/**	function used to set the Current ie., new 5.0 database parameters in the migration object properties
	 *	@param string $hostname - new database hostname
	 *	@param int $mysql_port - new database mysql port number
	 *	@param string $mysql_username - new database mysql user name
	 *	@param string $mysql_password - new database mysql password
	 *	@param string $dbname - new database name
	 *      @return void
	 */
	function setNewDatabaseParams($hostname,$mysql_port,$mysql_username,$mysql_password,$dbname)
	{
		global $migrationlog;
		$migrationlog->debug("Inside the function setNewDatabaseParams($hostname,$mysql_port,$mysql_username,$mysql_password,$dbname)");
		
		$this->new_hostname = $hostname;
		$this->new_mysql_port = $mysql_port;
		$this->new_mysql_username = $mysql_username;
		$this->new_mysql_password = $mysql_password;
		$this->new_dbname = $dbname;
		
		$migrationlog->debug("New Database Parameters has been set.");
	}

	/**	function used to take the database dump
	 *	@param string $host_name - host name in where the database is available
	 *	@param int $mysql_port - mysql port number 
	 *	@param string $mysql_username - mysql user name
	 *	@param string $mysql_password - mysql password
	 *	@param string $dbname - database name
	 *      @return string $dump_filename - return the dump filename
	 */
	function takeDatabaseDump($host_name,$mysql_port,$mysql_username,$mysql_password,$dbname)
	{
		global $migrationlog;
		$migrationlog->debug("Inside the function takeDatabaseDump($host_name,$mysql_port,$mysql_username,$mysql_password,$dbname). Going to take the specified database dump...");

		$dump_filename = 'dump_'.$dbname.'.txt';

		if($mysql_password != '')
		{
			$password_str = " -p".$mysql_password;
		}
		else
		{
			$password_str = '';
		}

		//This if is used when we cannot access mysql from vtiger root directory
		if($_SESSION['set_server_mysql_path'] != '')
		{
			$current_working_dir = getcwd();
			$server_mysql_path = $_SESSION['set_server_mysql_path'];

			$dump_str = "mysqldump -u".$mysql_username.$password_str." -h ".$host_name." --port=".$mysql_port." ".$dbname." >> ".$dump_filename;
			$migrationlog->debug("Server path set. Dump string to execute ==> $dump_str");
			
			chdir ($server_mysql_path);

			exec("echo 'set FOREIGN_KEY_CHECKS = 0;' > ".$dump_filename);
			exec($dump_str);
			exec("echo 'set FOREIGN_KEY_CHECKS = 1;' >> ".$dump_filename);
			
			exec('cp "'.$server_mysql_path.'\\'.$dump_filename.'" "'.$current_working_dir.'\\'.$dump_filename).'"';
			chdir ($current_working_dir);
		}
		else
		{
			$migrationlog->debug("Dump string to execute ==> mysqldump -u".$mysql_username." -h ".$host_name.$password_str." --port=".$mysql_port." ".$dbname." >> ".$dump_filename);

			exec("echo 'set FOREIGN_KEY_CHECKS = 0;' > ".$dump_filename);
			exec("mysqldump -u".$mysql_username." -h ".$host_name.$password_str." --port=".$mysql_port." ".$dbname." >> ".$dump_filename);
			exec("echo 'set FOREIGN_KEY_CHECKS = 1;' >> ".$dump_filename);
		}

		$_SESSION['migration_log'] .= '<br> <b>'.$dbname.'</b> Database Dump has been taken and the file is ==> '.$dump_filename;
		$migrationlog->debug("<br> <b> $dbname </b> Database Dump has been taken and the file is ==> $dump_filename");

		return $dump_filename;
	}

	/**	function used to drop the database
	 *	@param object $conn - adodb object which is connected with the current(new) database 
	 *	@param string $dbname - database name which we want to drop
	 *      @return void
	 */
	function dropDatabase($conn,$dbname)
	{
		global $migrationlog;
		$migrationlog->debug("Inside the function dropDatabase(".get_class($conn).",".$dbname."). Going to drop the database - $dbname");
		$sql = "drop database ".$dbname;
		$conn->query($sql);

		$migrationlog->debug("Database ($dbname) has been dropped.");
		$_SESSION['migration_log'] .= '<br> <b>'.$dbname.'</b> Database has been dropped.';
	}

	/**     function used to create the database
	 *	@param object $conn - adodb object which is connected with the current(new) database
	 *	@param string $dbname - database name which we want to drop
	 *      @return void
	 */
	function createDatabase($conn,$dbname)
	{
		global $migrationlog;
		$migrationlog->debug("Inside the function createDatabase(".get_class($conn).",".$dbname."). Going to create the database - $dbname");
		
		$sql = "create database ".$dbname;
		$conn->query($sql);

		$_SESSION['migration_log'] .= '<br> <b>'.$dbname.'</b> Database has been created.';
		$migrationlog->debug("Database ($dbname) has been dropped.");
		
		//Added to avoid the No Database Selected error when execute the queries
		$conn->connect();
	}

	/**	function used to apply the dump data to a database
	 *	@param string $host_name - host name
	 *	@param int $mysql_port - mysql port number
	 *	@param string $mysql_username - mysql user name
	 *	@param string $mysql_password - mysql password
	 *	@param string $dbname - database name to which we want to apply the dump
	 *	@param string $dumpfile - dump file which contains the data dump of a database
	 *      @return void
	 */
	function applyDumpData($host_name,$mysql_port,$mysql_username,$mysql_password,$dbname,$dumpfile)
	{
		global $migrationlog;
		$migrationlog->debug("Inside the function applyDumpData($host_name,$mysql_port,$mysql_username,$mysql_password,$dbname,$dumpfile).");

		if($mysql_password != '')
		{
			$password_str = " --password=".$mysql_password;
		}
		else
		{
			$password_str = '';
		}

		//This if is used when we cannot access mysql from vtiger root directory
		if($_SESSION['set_server_mysql_path'] != '')
		{
			$current_working_dir = getcwd();
			$server_mysql_path = $_SESSION['set_server_mysql_path'];

			$dump_str = "mysql --user=".$mysql_username.$password_str." -h ".$host_name." --force --port=".$mysql_port." ".$dbname." < ".$dumpfile;
			$migrationlog->debug("MySQL server path set. Dump string to apply ==> $dump_str");

			//exec("path = $server_mysql_path");
			chdir ($server_mysql_path);

			exec($dump_str);
			
			chdir ($current_working_dir);
		}
		else
		{
			exec("mysql --user=".$mysql_username." -h ".$host_name." --force --port=".$mysql_port.$password_str." ".$dbname." < ".$dumpfile);
			$migrationlog->debug("Dump string to apply ==> mysql --user=$mysql_username -h $host_name --force --port=$mysql_port $password_str $dbname < $dumpfile");
		}

		$_SESSION['migration_log'] .= '<br> Database Dump has been applied to the <b>'.$dbname.'</b> Database from <b>'.$dumpfile.'</b>';
		$migrationlog->debug("<br> Database Dump has been applied to the <b> $dbname </b> database from <b> $dumpfile </b>");
	}


	/**	function used to get the tabid
	 *	@param string $module - module to which we want to get the tabid
	 *	@return int $tabid - return the tabid of the module
	 */
	function localGetTabID($module)
	{
		global $conn;

		$sql = "select tabid from vtiger_tab where name='".$module."'";
		$result = $conn->query($sql);
		$tabid=  $conn->query_result($result,0,"tabid");

		return $tabid;
	}

	/**	function used to get the table count of the new database
	 *	@return int $tables - return the number of tables available in the new database
	 */
	function getTablesCountInNewDatabase()
	{
		global $migrationlog;
		$migrationlog->debug("Inside the function getTablesCountInNewDatabase()");
		$newconn = @mysql_connect($this->new_hostname.':'.$this->new_mysql_port,$this->new_mysql_username,$this->new_mysql_password);
		$tables = @mysql_num_rows(mysql_list_tables($this->new_dbname,$newconn));

		$migrationlog->debug("Number of Tables in New Database = $tables");
		return $tables;
	}

	/**	function used to get the table count of the old database
	 *	@return int $tables - return the number of tables available in the old database
	 */
	function getTablesCountInOldDatabase()
	{
		global $migrationlog;
		$migrationlog->debug("Inside the function getTablesCountInOldDatabase()");
		$oldconn = @mysql_connect($this->old_hostname.':'.$this->old_mysql_port,$this->old_mysql_username,$this->old_mysql_password);
		$tables = @mysql_num_rows(mysql_list_tables($this->old_dbname,$oldconn));

		$migrationlog->debug("Number of Tables in Old Database = $tables");
		return $tables;
	}

	/**	function used to modify the database from old version to match with new version
	 *	@param object $conn - adodb object which is connected with the current(new) database 
	 *      @return void
	 */
	function modifyDatabase($conn)
	{
		global $migrationlog;
		$migrationlog->debug("Inside the function modifyDatabase(".get_class($conn).")");
		$migrationlog->debug("\n\n\nMickie ---- Starts");

		$_SESSION['migration_log'] .= "<br>The current database is going to be modified by executing the following queries...<br>";
		
		//Added variables to get the queries list and count
		$query_count = 1;
		$success_query_count = 1;
		$failure_query_count = 1;
		$success_query_array = Array();
		$failure_query_array = Array();

		//To handle the file includes for each and every version
		//Here we have to decide which files should be included, where the files will be added newly for every public release
		//In this included file we have included modules/Migration/DBChanges/42P2_to_50.php which will apply the db changes upto 5.0.
		include("modules/Migration/MigrationInfo.php");

		$migrationlog->debug("Mickie ---- Ends\n\n\n");
	}

	/**	function used to run the migration process based on the option selected and values given
	 *	@param int $same_databases - 1 if both databases are same otherwise 0
	 *	@param string $option - selected migration option (dbsource or dumpsource)
	 *	@param string $old_dump_file_name - dump file name of the old database which is optional when we use dbsource
	 *      @return void
	 */
	function migrate($same_databases, $option, $old_dump_file_name='')
	{
		//1. Migration Procedure -- when we give the Source Database values
		//Step : 1 => Take a dump of old database
		//Step : 2 => Drop the New Database
		//Step : 3 => Create the New Database
		//Step : 4 => Put the old dump into the New Database
		//Step : 5 => Modify the new database with the new changes

		//2. Migration Procedure -- when we give the Database dump file
		//Step : 1 => Drop the New Database
		//Step : 2 => Create the New Database
		//Step : 3 => Put the dump into the New Database
		//Step : 4 => Modify the new database with the new changes

		global $migrationlog;
		global $conn;
		$migrationlog->debug("Database Migration from Old Database to the Current Database Starts.");
		$migrationlog->debug("Migration Option = $option");

		//Set the old database parameters
		$old_host_name = $this->old_hostname;
		$old_mysql_port = $this->old_mysql_port;
		$old_mysql_username = $this->old_mysql_username;
		$old_mysql_password = $this->old_mysql_password;
		$old_dbname = $this->old_dbname;

		//Set the new database parameters
		$new_host_name = $this->new_hostname;
		$new_mysql_port = $this->new_mysql_port;
		$new_mysql_username = $this->new_mysql_username;
		$new_mysql_password = $this->new_mysql_password;
		$new_dbname = $this->new_dbname;

		//This will be done when we give the Source Database details
		if($option == 'dbsource')
		{
			//Take the dump of the old Database
			$migrationlog->debug("Going to take the old Database Dump.");
			$dump_file = $this->takeDatabaseDump($old_host_name,$old_mysql_port,$old_mysql_username,$old_mysql_password,$old_dbname);

			//check the file size is greater than 10000 bytes (~app) and if yes then continue else goto step1
			if(is_file($dump_file) && filesize($dump_file) > 10000)
			{
				$_SESSION['migration_log'] .= "Source database dump taken successfully.";
			}
			else
			{
				echo '<br><font color="red"><b>The Source database dump taken may not contain all values. So please use other option.</font></b>';
				include("modules/Migration/MigrationStep1.php");
				exit;
			}
		}
		elseif($option == 'dumpsource')
		{
			$dump_file = $old_dump_file_name;
		}

		//if old db and new db are different then take new db dump
		if($old_dbname != $new_dbname)
		{
			//This is to take dump of the new database for backup purpose
			$migrationlog->debug("Going to take the current Database Dump.");
			$new_dump_file = $this->takeDatabaseDump($new_host_name,$new_mysql_port,$new_mysql_username,$new_mysql_password,$new_dbname);

			//check the file size is greater than 10000 bytes (~app) and if yes then continue else goto step1
			if(is_file($new_dump_file) && filesize($new_dump_file) > 10000)
			{
				$_SESSION['migration_log'] .= "Current database dump taken successfully.";
			}
			else
			{
				$_SESSION['migration_log'] .= '<br><font color="red"><b>The Current database dump taken may not contain all values. So may not be reload the current database if any problem occurs in migration. If the migration not completed please rename the install.php and install folder and run the install.php</font></b>';
				//include("modules/Migration/MigrationStep1.php");
				//exit;
			}

		}


		$continue_process = 1;
		if($same_databases == 1)
		{
			$_SESSION['migration_log'] .= '<br> Same databases are used. so skip the process of drop and create the current database.';
		}
		else
		{
			$_SESSION['migration_log'] .= '<br> Databases are different. So drop the Current Database and create. Also apply the dump of Old Database';
			//Drop the current(latest) Database
			$migrationlog->debug("Going to Drop the current Database");
			$this->dropDatabase($conn,$new_dbname);

			//Create the new current(latest) Database
			$migrationlog->debug("Going to Create the current Database");
			$this->createDatabase($conn,$new_dbname);

			//Apply the dump of the old database to the current database
			$migrationlog->debug("Going to apply the old database dump to the new database.");
			$this->applyDumpData($new_host_name,$new_mysql_port,$new_mysql_username,$new_mysql_password,$new_dbname,$dump_file);

			//get the number of tables in new database 
			$new_tables_count = $this->getTablesCountInNewDatabase();

			//get the number of tables in old database 
			$old_tables_count = $this->getTablesCountInOldDatabase();

			//if tables are missing after apply the dump, then alert the user and quit
			if(($new_tables_count != $old_tables_count && $option == 'dbsource') || ($new_tables_count < 180 && $option == 'dumpsource'))
			{
				$migrationlog->debug("New Database tables not equal to Old Database tables count. Reload the current database again and quit.");
				
				$continue_process = 0;
				$msg = "The dump may not be applied correctly. Tables exist in 4.2.3 database : $old_tables_count. Tables exist in current database after apply the dump : $new_tables_count";
			   ?>
				<script language="javascript">
					alert("<?php echo $msg; ?>");
				</script>
			   <?php
			}
		}

		if($continue_process == 1)
		{
			//Modify the database which is now as old database setup
			$migrationlog->debug("Going to modify the current database which is now as old database setup");
			$this->modifyDatabase($conn);
		
			$migrationlog->debug("Database Modifications Ends......");
			$migrationlog->debug("Database Migration from Old Database to the Current Database has been Finished.");
		}
		else
		{
			//Drop the current(latest) Database
			$migrationlog->debug("Problem in migration. so going to Restore the current Database");
			$migrationlog->debug("Going to Drop the current Database");
			$this->dropDatabase($conn,$new_dbname);

			//Create the new current(latest) Database
			$migrationlog->debug("Going to Create the current Database");
			$this->createDatabase($conn,$new_dbname);

			//Reload the new db dump and quit
			$migrationlog->debug("Going to apply the new backup db dump");
			$this->applyDumpData($new_host_name,$new_mysql_port,$new_mysql_username,$new_mysql_password,$new_dbname,$new_dump_file);

			//Return to Step1
			echo '<br><font color="red"><b>Dump could not be applied correctly. so your previous database restored.</b></font>';
			include("modules/Migration/MigrationStep1.php");
		}

		//Now we should recalculate the user and sharing privileges
		RecalculateSharingRules();
	}

}



?>
