<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once 'include/utils/utils.php';
require_once 'modules/PickList/PickListUtils.php';
require_once "include/Zend/Json.php";

global $adb, $current_user;

$moduleName = vtlib_purify($_REQUEST['moduleName']);
$tableName = vtlib_purify($_REQUEST['fieldname']);
$roleid = vtlib_purify($_REQUEST['roleid']);
$values = vtlib_purify($_REQUEST['values']);
$otherRoles = vtlib_purify($_REQUEST['otherRoles']);

if(empty($tableName)){
	echo "Table name is empty";
	exit;
}

$values = Zend_Json::decode($values);

$sql = 'SELECT * FROM vtiger_picklist WHERE name = ?';
$result = $adb->pquery($sql, array($tableName));
if($adb->num_rows($result) > 0){
	$picklistid = $adb->query_result($result, 0, "picklistid");
}

if(!empty($roleid)){
	assignValues($picklistid, $roleid, $values, $tableName);
}

$otherRoles = Zend_Json::decode($otherRoles);
if(!empty($otherRoles)){
	foreach($otherRoles as $role){
		assignValues($picklistid, $role, $values, $tableName);
	}
}

echo "SUCCESS";


function assignValues($picklistid, $roleid, $values, $tableName){
	global $adb;
	$count = count($values);
	//delete older values
	$sql = 'DELETE FROM vtiger_role2picklist WHERE roleid=? AND picklistid=?';
	$adb->pquery($sql, array($roleid,$picklistid));
	
	//insert the new values
	for($i=0;$i<$count;$i++){
		$pickVal = $values[$i];
		$tableName = $adb->sql_escape_string($tableName);
		$sql = "SELECT * FROM vtiger_$tableName WHERE $tableName=?";
		$result = $adb->pquery($sql, array($pickVal));
		if($adb->num_rows($result) > 0){
			$picklistvalueid = $adb->query_result($result, 0, "picklist_valueid");
			$sortid = $i+1;
			$sql = 'INSERT INTO vtiger_role2picklist VALUES (?,?,?,?)';
			$adb->pquery($sql, array($roleid, $picklistvalueid, $picklistid, $sortid));
		}
	}
}

?>