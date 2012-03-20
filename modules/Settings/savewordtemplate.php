<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/utils/utils.php');
global $upload_badext;

$uploaddir = $root_directory ."/test/upload/" ;// set this to wherever
// Arbitrary File Upload Vulnerability fix - Philip
if(isset($_REQUEST['binFile_hidden'])) {
	$file = $_REQUEST['binFile_hidden'];
} else {
	$file = $_FILES['binFile']['name'];
}
$binFile = sanitizeUploadFileName($file, $upload_badext);
$_FILES["binFile"]["name"] = $binFile;
$strDescription = vtlib_purify($_REQUEST['txtDescription']);
// Vulnerability fix ends
if(move_uploaded_file($_FILES["binFile"]["tmp_name"],$uploaddir.$_FILES["binFile"]["name"])) 
{
  $binFile = $_FILES['binFile']['name'];
  //$filename = basename($binFile);
  $filename = ltrim(basename(" ".$binFile)); //allowed filenames start with UTF-8 characters 
  $filetype= $_FILES['binFile']['type'];
  $filesize = $_FILES['binFile']['size'];

  $error_flag ="";
  $filetype_array = explode("/",$filetype);

  $file_type_value = strtolower($filetype_array[1]);
  
    if($filesize != 0)	
    {
	    if($file_type_value == "msword" || $file_type_value == "doc" || $file_type_value == "document")
	    {
		    if($result!=false)
	    	    {
			 $savefile="true";	
		    }			 
	    }
	    else
	    {
		    $savefile="false";
		    $error_flag="1";
	    }		    
	    
 		$data = base64_encode(fread(fopen($uploaddir.$binFile, "r"), $filesize));
		$date_entered = date('Y-m-d H:i:s');
		
		//Retreiving the return module and setting the parent type		
		$ret_module = vtlib_purify($_REQUEST['return_module']);
		$parent_type = '';		
		if($_REQUEST['return_module'] == 'Leads') {
			$parent_type = 'Lead';
		} elseif($_REQUEST['return_module'] == 'Accounts') {
			$parent_type = 'Account';
		} elseif($_REQUEST['return_module'] == 'Contacts') {
			$parent_type = 'Contact';
		} elseif($_REQUEST['return_module'] == 'HelpDesk') {
			$parent_type = 'HelpDesk';
		}
	 
		$genQueryId = $adb->getUniqueID("vtiger_wordtemplates");
		if($genQueryId != '') {
			if($result!=false && $savefile=="true") {
				$module = vtlib_purify($_REQUEST['target_module']);
				$sql = "INSERT INTO vtiger_wordtemplates ";
				$sql .= "(templateid,module,date_entered,parent_type,data,description,filename,filesize,filetype) values (?,?,?,?,?,?,?,?,?)";
				$params = array($genQueryId, $module, $adb->formatDate($date_entered, true), $parent_type, $adb->getEmptyBlob(false), $strDescription, $filename, $filesize, $filetype);
				$result = $adb->pquery($sql, $params);

				$result = $adb->updateBlob('vtiger_wordtemplates','data'," filename='". $adb->sql_escape_string($filename) ."'",$data);
			   	deleteFile($uploaddir,$filename);
			   	header("Location: index.php?action=listwordtemplates&module=Settings&parenttab=Settings");	
			} elseif($savefile=="false") {
				$module = vtlib_purify($_REQUEST['target_module']);
			   	header("Location: index.php?action=upload&module=Settings&parenttab=Settings&flag=".$error_flag."&description=".$strDescription."&tempModule=".$module);	
			} else {
				include('themes/'.$theme.'/header.php');
				$errormessage = "<font color='red'><B>Error Message<ul>
				<li><font color='red'>Invalid file OR</font>
				<li><font color='red'>File has no data</font>
				</ul></B></font> <br>" ;
				echo $errormessage;
				deleteFile($uploaddir,$filename);
				include "upload.php";
			}
		}
	}
	//Added for Invaild file path 
	else
        {
		$module = vtlib_purify($_REQUEST['target_module']);
	   	header("Location: index.php?action=upload&module=Settings&parenttab=Settings&flag=2&description=".$strDescription."&tempModule=".$module);	

        }
 
} 
else 
{
	$errorCode =  $_FILES['binFile']['error'];
	if($errorCode == 4)
	{
		include('themes/'.$theme.'/header.php');
		include "upload.php";
		// $errormessage = "<B><font color='red'>Kindly give a valid file for upload!</font></B> <br>" ;
		echo "<script>alert('".$mod_strings['SPECIFY_FILE_TO_MERGE']."')</script>";
	}
	else if($errorCode == 2) 
	{
		include('themes/'.$theme.'/header.php');
		include "upload.php";
		//$errormessage = "<B><font color='red'>Sorry, the uploaded file exceeds the maximum filesize limit. Please try a smaller file</font></B> <br>";
		echo "<script>alert('".$mod_strings['FILESIZE_EXCEEDS_INFO_CONFIG_INC']."')</script>";	
		//echo $errormessage;
		//echo $errorCode;
	}
	elseif($errorCode == 1)
	{
		include('themes/'.$theme.'/header.php');
		include "upload.php";
		echo "<script>alert('".$mod_strings['FILESIZE_EXCEEDS_INFO_PHP_INI']."')</script>";
	}
	else if($errorCode == 3)
	{
		include('themes/'.$theme.'/header.php');
		include "upload.php";
		echo "<script>alert('".$mod_strings['PROBLEMS_IN_FILEUPLOAD']."')</script>";

	}

}

function deleteFile($dir,$filename)
{
   unlink($dir.$filename);	
}
?>