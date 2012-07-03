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

global $adb, $current_user, $default_charset;

$moduleName = $_REQUEST['fld_module'];
$tableName = $_REQUEST['fieldname'];
$tableName = $adb->sql_escape_string($tableName);
$mode = trim($_REQUEST['mode']);
if(empty($mode)){
	echo "action mode is empty";
	exit;
}

if($mode == 'add'){
	$newValues = $_REQUEST['newValues'];
	$selectedRoles = $_REQUEST['selectedRoles'];
	
	$arr = Zend_Json::decode($newValues);
	$roles = Zend_Json::decode($selectedRoles);
	$count = count($arr);
	
	$sql = "select picklistid from vtiger_picklist where name=?";
	$result = $adb->pquery($sql, array($tableName));
	$picklistid = $adb->query_result($result,0,"picklistid");
	
	for($i=0; $i<$count;$i++){
		$val = htmlentities(trim($arr[$i]), ENT_QUOTES, $default_charset);
		if(!empty($val)){
			$id = $adb->getUniqueID("vtiger_$tableName");
			$picklist_valueid = getUniquePicklistID();
			$sql = "insert into vtiger_$tableName values (?,?,?,?)";
			$adb->pquery($sql, array($id, $val, 1, $picklist_valueid));
			
			//add the picklist values to the selected roles
			for($j=0;$j<count($roles);$j++){
				$roleid = $roles[$j];
				
				$sql ="select max(sortid)+1 as sortid from vtiger_role2picklist left join vtiger_$tableName on vtiger_$tableName.picklist_valueid=vtiger_role2picklist.picklistvalueid where roleid=? and picklistid=?";
				$sortid = $adb->query_result($adb->pquery($sql, array($roleid, $picklistid)),0,'sortid');
				
				$sql = "insert into vtiger_role2picklist values(?,?,?,?)";
				$adb->pquery($sql, array($roleid, $picklist_valueid, $picklistid, $sortid));
			}
		}
	}
	echo "SUCCESS";
}elseif($mode == 'edit'){
	$newValues = Zend_Json::decode($_REQUEST['newValues']);
	$oldValues = Zend_Json::decode($_REQUEST['oldValues']);
	if(count($newValues) != count($oldValues)){
		echo "Some error occured";
		exit;
	}
	
	$qry="select tablename,columnname from vtiger_field where fieldname=? and presence in (0,2)";
	$result = $adb->pquery($qry, array($tableName));
	$num = $adb->num_rows($result);

	for($i=0; $i<count($newValues);$i++){
		$newVal = Array('encodedValue'=>htmlentities($newValues[$i], ENT_QUOTES, $default_charset),'rawValue'=>$newValues[$i]);
		$oldVal = $oldValues[$i];

		if($newVal != $oldVal){
			$oldVal = Array('encodedValue'=>htmlentities($oldVal, ENT_QUOTES, $default_charset),'rawValue'=>$oldVal);
			$sql = "UPDATE vtiger_$tableName SET $tableName=? WHERE $tableName=?";
			$adb->pquery($sql, array($newVal['encodedValue'], $oldVal['encodedValue']));
			
			//replace the value of this piclist with new one in all records
			if($num > 0){
				for($n=0;$n<$num;$n++){
					$table_name = $adb->query_result($result,$n,'tablename');
					$columnName = $adb->query_result($result,$n,'columnname');
					
					$sql = "update $table_name set $columnName=? where $columnName=?";
					$adb->pquery($sql, array($newVal['rawValue'], $oldVal['rawValue']));
					
					$sql = "UPDATE vtiger_field SET defaultvalue=? WHERE defaultvalue=? AND tablename=? AND columnname=?";
					$adb->pquery($sql, array($newVal['rawValue'], $oldVal['rawValue'], $table_name, $columnName));

					$sql = "UPDATE vtiger_picklist_dependency SET sourcevalue=? WHERE sourcevalue=? AND sourcefield=? AND tabid=?";
					$adb->pquery($sql, array($newVal['rawValue'], $oldVal['rawValue'], $tableName, getTabid($moduleName)));
				}
			}
		}
	}
	echo "SUCCESS";
}elseif($mode == 'delete'){
	
	$values = Zend_Json::decode($_REQUEST['values']);
	$replaceVal = $_REQUEST['replaceVal'];
	if(!empty($replaceVal)){
		$sql = "select * from vtiger_$tableName where $tableName=?";
		$result = $adb->pquery($sql, array($replaceVal));
		$replacePicklistID = $adb->query_result($result, 0, "picklist_valueid");
	}
	
	for($i=0;$i<count($values);$i++){
		$sql = "select * from vtiger_$tableName where $tableName=?";
		$result = $adb->pquery($sql, array($values[$i]));
		$origPicklistID = $adb->query_result($result, 0, "picklist_valueid");
		
		//give permissions for the new picklist
		if(!empty($replaceVal)){
			$sql = "select * from vtiger_role2picklist where picklistvalueid=?";
			$result = $adb->pquery($sql, array($replacePicklistID));
			$count = $adb->num_rows($result);
			
			if($count == 0){
				$sql = "update vtiger_role2picklist set picklistvalueid=? where picklistvalueid=?";
				$adb->pquery($sql, array($replacePicklistID, $origPicklistID));
			}
		}
		$values[$i] = Array('encodedValue'=>htmlentities($values[$i], ENT_QUOTES, $default_charset),'rawValue'=>$values[$i]);
		$sql = "delete from vtiger_$tableName where $tableName=?";
		$adb->pquery($sql, array($values[$i]['encodedValue']));
		$sql = "delete from vtiger_role2picklist where picklistvalueid=?";
		$adb->pquery($sql, array($origPicklistID));
		$sql = "DELETE FROM vtiger_picklist_dependency WHERE sourcevalue=? AND sourcefield=? AND tabid=?";
		$adb->pquery($sql, array($values[$i]['encodedValue'], $tableName, getTabid($moduleName)));
		
		//replace the value of this piclist with new one in all records
		$qry="select tablename,columnname from vtiger_field where fieldname=? and presence in (0,2)";
		$result = $adb->pquery($qry, array($tableName));
		$num = $adb->num_rows($result);
		if($num > 0){
			for($n=0;$n<$num;$n++){
				$table_name = $adb->query_result($result,$n,'tablename');
				$columnName = $adb->query_result($result,$n,'columnname');
				
				$sql = "update $table_name set $columnName=? where $columnName=?";
				$adb->pquery($sql, array($replaceVal, $values[$i]['rawValue']));
				$sql = "UPDATE vtiger_field SET defaultvalue=? WHERE defaultvalue=? AND tablename=? AND columnname=?";
				$adb->pquery($sql, array($replaceVal, $values[$i]['rawValue'], $table_name, $columnName));
			}
		}
	}
	echo "SUCCESS";
}

?>
