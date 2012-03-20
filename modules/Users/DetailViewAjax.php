<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
	      
require_once('include/logging.php');
require_once('modules/Users/Users.php');
require_once('include/database/PearDatabase.php');
global $adb ,$mod_strings ;

$local_log =& LoggerManager::getLogger('UsersAjax');
$ajaxaction = $_REQUEST["ajxaction"];
if($ajaxaction == "DETAILVIEW")
{
	if(empty($_SESSION['Users_FORM_TOKEN']) || $_SESSION['Users_FORM_TOKEN']
			!== (int)$_REQUEST['form_token']) {
		echo ":#:ERR".($app_strings['LBL_PERMISSION']);
		die;
	}
	$userid = $_REQUEST["recordid"];
	$tablename = $_REQUEST["tableName"];
	$fieldname = $_REQUEST["fldName"];
	$fieldvalue = utf8RawUrlDecode($_REQUEST["fieldValue"]); 
	if($userid != "")
	{
		$userObj = new Users();
		$userObj->retrieve_entity_info($userid,"Users");
		$userObj->column_fields[$fieldname] = $fieldvalue;

                if($fieldname=='asterisk_extension'){
			$query = "select 1 from vtiger_asteriskextensions
                     inner join vtiger_users on vtiger_users.id=vtiger_asteriskextensions.userid
                     where status='Active' and asterisk_extension =?";
			$params = array($fieldvalue);
			
			$result = $adb->pquery($query, $params);
		        if($adb->num_rows($result) > 0)
			{
				echo ":#:ERR".$mod_strings['LBL_ASTERISKEXTENSIONS_EXIST'];
				return false;
			}
	     }
		if($fieldname == 'internal_mailer'){
			
			if(isset($_SESSION['internal_mailer']) && $_SESSION['internal_mailer'] != $userObj->column_fields['internal_mailer'])
				$_SESSION['internal_mailer'] = $userObj->column_fields['internal_mailer'];
		}
		$userObj->id = $userid;
		$userObj->mode = "edit";
		$userObj->save("Users");
		if($userObj->id != "")
		{
			echo ":#:SUCCESS";
		}else
		{
			echo ":#:FAILURE";
		}   
	}else
	{
		echo ":#:FAILURE";
	}
}
?>
