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
require_once('include/database/PearDatabase.php');
global $adb;

$local_log =& LoggerManager::getLogger('ContactsAjax');
global $currentModule;
$modObj = CRMEntity::getInstance($currentModule);

$ajaxaction = $_REQUEST["ajxaction"];
if($ajaxaction == "DETAILVIEW")
{
     $crmid = $_REQUEST["recordid"];
     $tablename = $_REQUEST["tableName"];
     $fieldname = $_REQUEST["fldName"];
     $fieldvalue = utf8RawUrlDecode($_REQUEST["fieldValue"]); 
     if($crmid != "")
	 {
		 $modObj->retrieve_entity_info($crmid,"Contacts");
		 $modObj->column_fields[$fieldname] = $fieldvalue;
		 $modObj->id = $crmid;
		 $modObj->mode = "edit";
		 $modObj->save("Contacts");
		 $email_res = $adb->pquery("select email from vtiger_contactdetails where contactid=?", array($modObj->id));
		 $email = $adb->query_result($email_res,0,'email');

		 $check_available = $adb->pquery("select * from vtiger_portalinfo where id=?", array($modObj->id));
		 $update = '';
		 if($fieldname =='email')
		 {
			 $active = $adb->query_result($check_available,0,'isactive');
			 $update = false;
			 if($active != '' && $active == 1)
			 {
				$sql = "update vtiger_portalinfo set user_name=?,isactive=? where id=?";
				$adb->pquery($sql, array($fieldvalue, $active, $crmid));
				$email = $fieldvalue;
				$result = $adb->pquery("select user_password from vtiger_portalinfo where id=?", array($modObj->id));
				$password = $adb->query_result($result,0,'user_password');
				$update = true;
		 	 }
		 }

		 if($fieldname == "portal")
		 {
			if($email != '')
			{
				$confirm = $adb->query_result($check_available,0,'isactive');
				if($confirm == '' && $fieldvalue == 1)
				{
					$password = makeRandomPassword();
					$sql = "insert into vtiger_portalinfo (id,user_name,user_password,type,isactive) values(?,?,?,?,?)";
					$params = array($modObj->id, $email, $password, 'C', 1);
					$adb->pquery($sql, $params);
					$insert = true;

				}
				elseif($confirm == 0 && $fieldvalue == 1)
				{
					$sql = "update vtiger_portalinfo set user_name=?, isactive=1 where id=?";
					$params = array($email, $modObj->id);
					$adb->pquery($sql, $params);
					
					$result = $adb->pquery("select user_password from vtiger_portalinfo where id=?", array($modObj->id));
					$password = $adb->query_result($result,0,'user_password');
					$update = true;

				}
				elseif($confirm == 1 && $fieldvalue == 0)
				{
					$sql = "update vtiger_portalinfo set isactive=0 where id=?";
					$adb->pquery($sql, array($modObj->id));
				}
			}
		}
			require_once("modules/Emails/mail.php");
			global $current_user;
			$data_array = Array();
			$data_array['first_name'] = $modObj->column_fields['firstname'];
			$data_array['last_name'] = $modObj->column_fields['lastname'];
			$data_array['email'] = $email;
			$data_array['portal_url'] = "<a href=".$PORTAL_URL."/login.php>".$mod_strings['Please Login Here']."</a>";
			$contents = getmail_contents_portalUser($data_array,$password);
		 if($modObj->id != "")
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
} elseif($ajaxaction == "LOADRELATEDLIST" || $ajaxaction == "DISABLEMODULE"){
	require_once 'include/ListView/RelatedListViewContents.php';
}
?>
