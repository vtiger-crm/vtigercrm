<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************
 */

require_once("config.php");
require_once("include/database/PearDatabase.php");
define("dbserver", $dbconfig['db_hostname']);
define("dbuser", $dbconfig['db_username']);
define("dbpass", $dbconfig['db_password']);
define("dbname", $dbconfig['db_name']);

function save_structure($filename, $root_directory) {
		global $log;
		$log->debug("Entering save_structure(".$filename.",".$root_directory.") method ...");

		$dbdump = new DatabaseDump(dbserver, dbuser, dbpass);
		$dumpfile = $root_directory.'/'.$filename;
		$dbdump->save(dbname, $dumpfile) ;
        $log->debug("Exiting save_structure method ...");
}

/**
 * DatabaseDump will save the dump of database to the file specified.
 *
 * The dump file contains series of SQL statements (with some meta information)
 * generated similar to mysqldump command.
 *
 * To restore back the dump you can use the 'source' command in sql.
 * Like:
 * mysql> create database dbname;
 * mysql> use dbname;
 * mysql> source sql_dump_file;
 *
 * @author Prasad
 */
class DatabaseDump {
		private $fhandle;
		function DatabaseDump($dbserver, $username, $password) {
				mysql_connect($dbserver, $username, $password);
		}
		function save($database, $filename) {
			// Connect to database
			$db = mysql_select_db($database);
			$db_charset = mysql_fetch_assoc(mysql_query("SHOW variables LIKe'character_set_database'"));
			if($db_charset['Value']=='utf8'){
				mysql_query("SET NAMES 'utf8'");
			}
			if(empty($db)) {
				return;
			}
			$this->file_open($filename);

			// Write some information regarding database dump and the time first.	
			$this->writeln("SET NAMES 'utf8';");
			$this->writeln("-- $database database dump");
			$this->writeln("-- Date: " . date("D, M j, G:i:s T Y"));
			$this->writeln("-- ----------------------------------");
			$this->writeln("");
	
			// Meta information which helps to import into mysql database.
			$this->writeln("SET FOREIGN_KEY_CHECKS=0;");
			$this->writeln("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
			$this->writeln("");

			// Get all table names from database
			$tcount = 0;
			$trs = mysql_list_tables($database);
			for($tindex = 0; $tindex < mysql_num_rows($trs); $tindex++) {
				$table = mysql_tablename($trs, $tindex);
				if(!empty($table)) {
					$tables[$tcount] = mysql_tablename($trs, $tindex);
					$tcount++;
				}
			}

			// List tables
			$dump = '';
			for($tindex = 0; $tindex < count($tables); $tindex++) {
				// Table Name
				$table = $tables[$tindex];

				$table_create_rs = mysql_query("SHOW CREATE TABLE `$table`");
				$table_create_rows = mysql_fetch_array($table_create_rs);
				$table_create_sql = $table_create_rows[1];
				
				// Our parser used for reading the dump file is very basic
				// hence we will need to remove the new lines
				$table_create_sql = str_replace("\n","",$table_create_sql);

				// Our parser used for reading the dump file is very basic
				// hence we will need to remove the new lines
				$table_create_sql = str_replace("\n","",$table_create_sql);

				// Write table create statement 
				$this->writeln("");
				$this->writeln("--");
				$this->writeln("-- Table structure for table `$table` ");
				$this->writeln("--");
				$this->writeln("");
				$this->writeln("DROP TABLE IF EXISTS `$table`;");
				$this->writeln($table_create_sql . ';');
				$this->writeln("");

				// Write data
				$this->writeln("--");
				$this->writeln("-- Dumping data for table `$table` ");
				$this->writeln("--");
				$this->writeln("");

				$table_query = mysql_query("SELECT * FROM `$table`");
				$num_fields = mysql_num_fields($table_query);
				while($fetch_row = mysql_fetch_array($table_query)) {
					$insert_sql = "INSERT INTO `$table` VALUES(";
					for($n = 1; $n <= $num_fields; $n++) {
							$m = $n -1;
							$field_value = $fetch_row[$m];
							$field_value = str_replace('\"', '"', mysql_escape_string($field_value));
							$insert_sql .= "'". $field_value . "', ";
					}
					$insert_sql = substr($insert_sql,0,-2);
					$insert_sql .= ");";

					if($insert_sql != "") {
						$this->writeln($insert_sql);
					}
				}
			}
			// Meta information reset to original state.
			$this->writeln("SET FOREIGN_KEY_CHECKS=0;");

			$this->file_close();
	}
	function file_open($filename) {	$this->fhandle = fopen($filename, "w+"); }
	function file_close()         { fclose($this->fhandle); }
	function write($string)       { fprintf($this->fhandle, "%s", $string); }
	function writeln($string)     { fprintf($this->fhandle, "%s\r\n", $string); }
};


class createZip  {  

    public $compressedData = array();
    public $centralDirectory = array(); // central directory   
    public $endOfCentralDirectory = "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record
    public $oldOffset = 0;

    /**
     * Function to create the directory where the file(s) will be unzipped
     *
     * @param $directoryName string
     *
     */
    
    public function addDirectory($directoryName) {
        $directoryName = str_replace("\\", "/", $directoryName);  

        $feedArrayRow = "\x50\x4b\x03\x04";
        $feedArrayRow .= "\x0a\x00";    
        $feedArrayRow .= "\x00\x00";    
        $feedArrayRow .= "\x00\x00";    
        $feedArrayRow .= "\x00\x00\x00\x00";

        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("v", strlen($directoryName) );
        $feedArrayRow .= pack("v", 0 );
        $feedArrayRow .= $directoryName; 

        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("V",0);
        $feedArrayRow .= pack("V",0);

        $this -> compressedData[] = $feedArrayRow;
        
        $newOffset = strlen(implode("", $this->compressedData));

        $addCentralRecord = "\x50\x4b\x01\x02";
        $addCentralRecord .="\x00\x00";    
        $addCentralRecord .="\x0a\x00";    
        $addCentralRecord .="\x00\x00";    
        $addCentralRecord .="\x00\x00";    
        $addCentralRecord .="\x00\x00\x00\x00";
        $addCentralRecord .= pack("V",0);
        $addCentralRecord .= pack("V",0);
        $addCentralRecord .= pack("V",0);
        $addCentralRecord .= pack("v", strlen($directoryName) );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $ext = "\x00\x00\x10\x00";
        $ext = "\xff\xff\xff\xff";  
        $addCentralRecord .= pack("V", 16 );

        $addCentralRecord .= pack("V", $this -> oldOffset );
        $this -> oldOffset = $newOffset;

        $addCentralRecord .= $directoryName;  

        $this -> centralDirectory[] = $addCentralRecord;  
    }    
    
    /**
     * Function to add file(s) to the specified directory in the archive
     *
     * @param $directoryName string
     *
     */
    
    public function addFile($data, $directoryName)   {

        $directoryName = str_replace("\\", "/", $directoryName);  
    
        $feedArrayRow = "\x50\x4b\x03\x04";
        $feedArrayRow .= "\x14\x00";    
        $feedArrayRow .= "\x00\x00";    
        $feedArrayRow .= "\x08\x00";    
        $feedArrayRow .= "\x00\x00\x00\x00";

        $uncompressedLength = strlen($data);  
        $compression = crc32($data);  
        $gzCompressedData = gzcompress($data);  
        $gzCompressedData = substr( substr($gzCompressedData, 0, strlen($gzCompressedData) - 4), 2);
        $compressedLength = strlen($gzCompressedData);  
        $feedArrayRow .= pack("V",$compression);
        $feedArrayRow .= pack("V",$compressedLength);
        $feedArrayRow .= pack("V",$uncompressedLength);
        $feedArrayRow .= pack("v", strlen($directoryName) );
        $feedArrayRow .= pack("v", 0 );
        $feedArrayRow .= $directoryName;  

        $feedArrayRow .= $gzCompressedData;  

        $feedArrayRow .= pack("V",$compression);
        $feedArrayRow .= pack("V",$compressedLength);
        $feedArrayRow .= pack("V",$uncompressedLength);

        $this -> compressedData[] = $feedArrayRow;

        $newOffset = strlen(implode("", $this->compressedData));

        $addCentralRecord = "\x50\x4b\x01\x02";
        $addCentralRecord .="\x00\x00";    
        $addCentralRecord .="\x14\x00";    
        $addCentralRecord .="\x00\x00";    
        $addCentralRecord .="\x08\x00";    
        $addCentralRecord .="\x00\x00\x00\x00";
        $addCentralRecord .= pack("V",$compression);
        $addCentralRecord .= pack("V",$compressedLength);
        $addCentralRecord .= pack("V",$uncompressedLength);
        $addCentralRecord .= pack("v", strlen($directoryName) );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("v", 0 );
        $addCentralRecord .= pack("V", 32 );

        $addCentralRecord .= pack("V", $this -> oldOffset );
        $this -> oldOffset = $newOffset;

        $addCentralRecord .= $directoryName;  

        $this -> centralDirectory[] = $addCentralRecord;  
    }

    /**
     * Fucntion to return the zip file
     *
     * @return zipfile (archive)
     */

    public function getZippedfile() {

        $data = implode("", $this -> compressedData);  
        $controlDirectory = implode("", $this -> centralDirectory);  

        return   
            $data.  
            $controlDirectory.  
            $this -> endOfCentralDirectory.  
            pack("v", sizeof($this -> centralDirectory)).     
            pack("v", sizeof($this -> centralDirectory)).     
            pack("V", strlen($controlDirectory)).             
            pack("V", strlen($data)).                
            "\x00\x00";                             
    }

    /**
     *
     * Function to force the download of the archive as soon as it is created
     *
     * @param archiveName string - name of the created archive file
     */

    public function forceDownload($archiveName) {
        $headerInfo = '';
        
        if(ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        // Security checks
        if( $archiveName == "" ) {
            echo "<html><title>Public Photo Directory - Download </title><body><BR><B>ERROR:</B> The download file was NOT SPECIFIED.</body></html>";
            exit;
        }
        elseif ( ! file_exists( $archiveName ) ) {
            echo "<html><title>Public Photo Directory - Download </title><body><BR><B>ERROR:</B> File not found.</body></html>";
            exit;
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=".basename($archiveName).";" );
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($archiveName));
        readfile("$archiveName");
        
     }

} 

class createDirZip extends createZip {
  function get_files_from_folder($directory, $put_into) {
    if ($handle = @opendir($directory)) {
      while (false != ($file = readdir($handle))) {
        if (is_file($directory.$file)) {
          $fileContents = file_get_contents($directory.$file);
          $this->addFile($fileContents, $put_into.$file);
        } elseif ($file != '.' and $file != '..' and is_dir($directory.$file)) {
          $this->addDirectory($put_into.$file.'/');
          $this->get_files_from_folder($directory.$file.'/', $put_into.$file.'/');
        }
      }
    }
    @closedir($handle);
  }
}
?>
