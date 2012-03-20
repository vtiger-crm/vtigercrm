<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.;
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
/*********************************************************************************
 * $Header $
 * Description:
 ********************************************************************************/

require_once('config.php');


class UploadFile 
{

	var $field_name;
	var $stored_file_name;

        function UploadFile ($field_name)
        {
		global $log;
		$log->debug("Entering UploadFile (".$field_name.") method ...");
		$this->field_name = $field_name;
		$log->debug("Exiting UploadFile method ...");
        }
	
	/** Function to get the url of the attachment
	  * @param $stored_file_name -- stored_file_name:: Type string
	  * @param $bean_id -- bean_id:: Type integer
	  * @returns urlstring -- urlstring:: Type string
	  *
	  */
	function get_url($stored_file_name,$bean_id)
	{
		global $log;
		$log->debug("Entering get_url(".$stored_file_name.",".$bean_id.") method ...");
		global $site_URL;
		global $upload_dir;
                //echo $site_URL.'/'.$upload_dir.$bean_id.$stored_file_name;
                //echo $_ENV['HOSTNAME'] .':' .$_SERVER["SERVER_PORT"].'/'.$upload_dir.$bean_id.$stored_file_name;
		$log->debug("Exiting get_url method ...");
                return 'http://'.$_ENV['HOSTNAME'] .':' .$_SERVER["SERVER_PORT"].'/'.$upload_dir.$bean_id.$stored_file_name;
                //return $site_URL.'/'.$upload_dir.$bean_id.$stored_file_name;
	}

	/** Function to duplicate and copy a file to another location
	  * @param $old_id -- old_id:: Type integer
	  * @param $new_id -- new_id:: Type integer
	  * @param $file_name -- filename:: Type string
	  *
	  */

	function duplicate_file($old_id, $new_id, $file_name)
	{
		global $log;
		$log->debug("Entering duplicate_file(".$old_id.", ".$new_id.", ".$file_name.") method ...");
		global $root_directory;
		global $upload_dir;
                $source = $root_directory.'/'.$upload_dir.$old_id.$file_name;
                $destination = $root_directory.'/'.$upload_dir.$new_id.$file_name;
		copy( $source,$destination);
		$log->debug("Exiting duplicate_file method ...");
	}
	
	/** Function to get the status of the file upload
	  * @returns boolean
	  */

	function confirm_upload()
	{
		global $log;
		$log->debug("Eentering confirm_upload() method ...");
		global $root_directory;
		global $upload_dir;
		global $upload_maxsize;
		global $upload_badext;


		if (!is_uploaded_file($_FILES[$this->field_name]['tmp_name']) )
		{
			$log->debug("Exiting confirm_upload method ...");
			return false;
		}
		else if ($_FILES[$this->field_name]['size'] > $upload_maxsize)
		{
			die("ERROR: uploaded file was too big: max filesize:$upload_maxsize");
		}


		if( !is_writable( $root_directory.'/'.$upload_dir))
		{
			die ("ERROR: cannot write to directory: $root_directory/$upload_dir for uploads");
		}

		require_once('include/utils/utils.php');
		$this->stored_file_name = sanitizeUploadFileName($_FILES[$this->field_name]['name'], $upload_badext);
		$log->debug("Exiting confirm_upload method ...");
		return true;
	}

	/** Function to get the stored file name
	  */

	function get_stored_file_name()
	{
		global $log;
		$log->debug("Entering get_stored_file_name() method ...");
		$log->debug("Exiting get_stored_file_name method ...");
		return $this->stored_file_name;
	}

	/** Function is to move a file and store it in given location
	  * @param $bean_id -- $bean_id:: Type integer
	  * @returns boolean
	  *
	  */

	function final_move($bean_id)
	{
		global $log;
		$log->debug("Entering final_move(".$bean_id.") method ...");
		global $root_directory;
		global $upload_dir;

                $file_name = $bean_id.$this->stored_file_name;

                $destination = $root_directory.'/'.$upload_dir.$file_name;

		if (!move_uploaded_file($_FILES[$this->field_name]['tmp_name'], $destination))
                {
			die ("ERROR: can't move_uploaded_file to $destination");
                }
		$log->debug("Exiting final_move method ...");
                return true;


	}

	/** Function deletes a file for a given file name
	  * @param $bean_id -- bean_id:: Type integer
	  * @param $file_name -- file name:: Type string
	  * @returns boolean
	  *
	  */

	function unlink_file($bean_id,$file_name)
        {
		global $log;
		$log->debug("Entering unlink_file(".$bean_id.",".$file_name.") method ...");
                global $root_directory;
		global $upload_dir;
		$log->debug("Exiting unlink_file method ...");
                return unlink($root_directory."/".$upload_dir.$bean_id.$file_name);
        }


}
?>
