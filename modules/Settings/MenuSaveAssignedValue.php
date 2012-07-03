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
$values = vtlib_purify($_REQUEST['values']);
$values = Zend_Json::decode($values);
MenuEditor :: saveMenuStructure($values);

class MenuEditor {
	static function saveMenuStructure($values){
		global $adb;
		$sql = 'UPDATE vtiger_tab SET tabsequence = ?';
		$adb->pquery($sql, array(-1));
		$ins = 'UPDATE vtiger_tab set tabsequence = ? WHERE tabid = ?';
		foreach($values as $key=>$val){
			$tabid = $val[0];
			$tabsequence = $val[1];
			$adb->pquery($ins, array($tabsequence,$tabid));
		}
	}
}
?>