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
global $adb;

$local_log =& LoggerManager::getLogger('HelpDeskAjax');
global $currentModule;
$modObj = CRMEntity::getInstance($currentModule);

$ajaxaction = $_REQUEST["ajxaction"];
if($ajaxaction == "DETAILVIEW")
{
	$crmid = $_REQUEST["recordid"];
	$tablename = $_REQUEST["tableName"];
	$fieldname = $_REQUEST["fldName"];
	$fieldvalue = utf8RawUrlDecode($_REQUEST["fieldValue"]);

	if($crmid != ""){
		$modObj->retrieve_entity_info($crmid,"HelpDesk");
		
		//Added to avoid the comment save, when we edit other fields through ajax edit
		if($fieldname != 'comments')
			$modObj->column_fields['comments'] = '';

		$modObj->column_fields[$fieldname] = $fieldvalue;
		$modObj->id = $crmid;
		$modObj->mode = "edit";

		//Added to construct the update log for Ticket history
		$assigned_group_name = getGroupName($_REQUEST['assigned_group_id']);
		$assigntype = $_REQUEST['assigntype'];

		$fldvalue = $modObj->constructUpdateLog($modObj, $modObj->mode, $assigned_group_name, $assigntype);
		$fldvalue = from_html($fldvalue,($modObj->mode == 'edit')?true:false);
		
		$modObj->save("HelpDesk");
		
		//update the log information for ticket history
		$adb->pquery("update vtiger_troubletickets set update_log=? where ticketid=?", array($fldvalue, $modObj->id));
		
		if($modObj->id != ""){
			if($fieldname == "comments"){
				$comments = $modObj->getCommentInformation($modObj->id);
				echo ":#:SUCCESS".$comments;
			}else{
				echo ":#:SUCCESS";
			}
		}else{
			echo ":#:FAILURE";
		}   
	}else{
		echo ":#:FAILURE";
	}
} elseif($ajaxaction == "LOADRELATEDLIST" || $ajaxaction == "DISABLEMODULE"){
	require_once 'include/ListView/RelatedListViewContents.php';
}
?>
