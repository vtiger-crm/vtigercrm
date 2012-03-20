<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
global $adb,$mod_strings;

$local_log =& LoggerManager::getLogger('AccountsAjax');
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
	     $modObj->retrieve_entity_info($crmid,"Accounts");
	     $modObj->column_fields[$fieldname] = $fieldvalue;
	     if($fieldname=='accountname'){
	     	$value = $fieldvalue;
			$query = "SELECT accountname FROM vtiger_account,vtiger_crmentity WHERE accountname =? and vtiger_account.accountid = vtiger_crmentity.crmid and vtiger_crmentity.deleted != 1";
			$params = array($value);
			if(isset($crmid) && $crmid !='') {
				$query .= " and vtiger_account.accountid != ?";
				array_push($params, $crmid);
			}
			$result = $adb->pquery($query, $params);
		    if($adb->num_rows($result) > 0)
			{
				echo ":#:ERR".$mod_strings['LBL_ACCOUNT_EXIST'];
				return false;
			}
	     }
	     if($fieldname=='accountname'){
	     	$value = $fieldvalue;
			$query = "SELECT accountname FROM vtiger_account,vtiger_crmentity WHERE accountname =? and vtiger_account.accountid = vtiger_crmentity.crmid and vtiger_crmentity.deleted != 1";
			$params = array($value);
			if(isset($crmid) && $crmid !='') {
				$query .= " and vtiger_account.accountid != ?";
				array_push($params, $crmid);
			}
			$result = $adb->pquery($query, $params);
		    if($adb->num_rows($result) > 0)
			{
				echo ":#:ERR".$mod_strings['LBL_ACCOUNT_EXIST'];
				return false;
			}
	     }
	     if($fieldname == 'annual_revenue')//annual revenue converted to dollar value while saving
	     {
		     $modObj->column_fields[$fieldname] = getConvertedPrice($fieldvalue);
	     }	     
	     $modObj->id = $crmid;
  	     $modObj->mode = "edit";
       	 $modObj->save("Accounts");

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
}elseif($ajaxaction == "LOADRELATEDLIST" || $ajaxaction == "DISABLEMODULE"){
	require_once 'include/ListView/RelatedListViewContents.php';
}

?>
