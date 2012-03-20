<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
global $adb, $currentModule;
$idlist = vtlib_purify($_REQUEST['idlist']);
$record = vtlib_purify($_REQUEST["parentid"]);

$storearray = array();
if(!empty($_REQUEST['idlist'])) {
	// Split the string of ids
	$storearray = explode (";",trim($idlist,";"));
} else if(!empty($_REQUEST['entityid'])){
	$storearray = array($_REQUEST['entityid']);
}
foreach($storearray as $id)
{
	if($id != '')
	{			
		$sql = "insert into vtiger_seactivityrel values (?,?)";
		$adb->pquery($sql, array($id, $record));
	}
}
if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '')
{
	$record = $_REQUEST['record'];
	$sql = "insert into vtiger_salesmanactivityrel values (?,?)";
	$adb->pquery($sql, array($_REQUEST["user_id"], $record));	
}

header("Location: index.php?action=CallRelatedList&module=Emails&record=".vtlib_purify($record));

?>