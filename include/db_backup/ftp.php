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

function ftpBackupFile($source_file, $ftpserver, $ftpuser, $ftppassword)
{
	global $log;
        $log->debug("Entering ftpBackupFile(".$source_file.", ".$ftpserver.", ".$ftpuser.", ".$ftppassword.") method ...");	
	// set up basic connection
	$conn_id = @ftp_connect($ftpserver);
	if(!$conn_id)
	{
		$log->debug("Exiting ftpBackupFile method ...");
		return;
	}
	

	// login with username and password

	$login_result = @ftp_login($conn_id, $ftpuser, $ftppassword);	

	if(!$login_result)
	{
		ftp_close($conn_id);
		 $log->debug("Exiting ftpBackupFile method ...");
		return;
	}

	// check connection
	/*if ((!$conn_id) || (!$login_result)) {
		echo "FTP connection has failed!";
		echo "Attempted to connect to $ftp_server for user $ftp_user_name";
		exit;
	} else {
		echo "Connected to $ftp_server, for user $ftp_user_name";
	}
	*/

	// upload the file
	$destination_file=$source_file;
	$upload = @ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);

	// check upload status
	if (!$upload) {
		ftp_close($conn_id);
		 $log->debug("Exiting ftpBackupFile method ...");
		return;
	}
	/*
	 else {
		echo "Uploaded $source_file to $ftp_server as $destination_file";
	}*/

	// close the FTP stream
	ftp_close($conn_id);
	 $log->debug("Exiting ftpBackupFile method ...");
}
?>
