<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/

/**
 * @author MAK
 */

require_once 'include/utils/utils.php';

//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.2.0 RC to 5.2.0 -------- Starts \n\n");

require_once 'include/utils/CommonUtils.php';
global $adb;
$query=$adb->pquery("select * from vtiger_selectcolumn",array());
$numOfRows=$adb->num_rows($query);
if($numOfRows>0){
	for($i=0;$i<$numOfRows;$i++){
		$columnname=$adb->query_result($query,$i,'columnname');
		preg_match('/&amp;/', $columnname, $matches);
		if(!empty($matches)){
			$columnname1=str_replace('&amp;', '&', $columnname);
			$query1=$adb->pquery("update vtiger_selectcolumn set columnname=? where columnname = ? ",array($columnname1,$columnname));
		}
	}
}
$query2=$adb->pquery("select * from vtiger_reportsortcol",array());
$numOfRows=$adb->num_rows($query2);
if($numOfRows>0){
	for($i=0;$i<$numOfRows;$i++){
		$columnname=$adb->query_result($query2,$i,'columnname');
		preg_match('/&amp;/', $columnname, $matches);
		if(!empty($matches)){
			$columnname1=str_replace('&amp;', '&', $columnname);
			$query3=$adb->pquery("update vtiger_reportsortcol set columnname=? where columnname = ? ",array($columnname1,$columnname));
		}
	}
}

function vt520_updateCurrencyInfo() {
	global $adb;
	include('modules/Utilities/Currencies.php');

	ExecuteQuery("DELETE FROM vtiger_currencies;");
	ExecuteQuery('UPDATE vtiger_currencies_seq set id=1;');
	foreach($currencies as $key=>$value){
		ExecuteQuery("insert into vtiger_currencies values(".$adb->getUniqueID("vtiger_currencies").",'$key','".$value[0]."','".$value[1]."')");
	}
	$cur_result = $adb->query("SELECT * from vtiger_currency_info");
	for($i=0;$i<$adb->num_rows($cur_result);$i++){
		$cur_symbol = $adb->query_result($cur_result,$i,"currency_symbol");
		$cur_code = $adb->query_result($cur_result,$i,"currency_code");
		$cur_name = $adb->query_result($cur_result,$i,"currency_name");
		$cur_id = $adb->query_result($cur_result,$i,"id");
		$currency_exists = $adb->pquery("SELECT * from vtiger_currencies WHERE currency_code=?",array($cur_code));
		if($adb->num_rows($currency_exists)>0){
			$currency_name = $adb->query_result($currency_exists,0,"currency_name");
			ExecuteQuery("UPDATE vtiger_currency_info SET vtiger_currency_info.currency_name = '$currency_name' WHERE id=$cur_id");
		} else {
			ExecuteQuery("insert into vtiger_currencies values(".$adb->getUniqueID("vtiger_currencies").",'$cur_name','$cur_code','$cur_symbol')");
		}
	}
}

vt520_updateCurrencyInfo();

function VT520GA_webserviceMigrate(){
	require_once 'include/Webservices/Utils.php';
	$customWebserviceDetails = array(
		"name"=>"revise",
		"include"=>"include/Webservices/Revise.php",
		"handler"=>"vtws_revise",
		"prelogin"=>0,
		"type"=>"POST"
	);

	$customWebserviceParams = array(
		array("name"=>'element',"type"=>'Encoded')
	);
	echo 'INITIALIZING WEBSERVICE...';
	$operationId = vtws_addWebserviceOperation($customWebserviceDetails['name'],$customWebserviceDetails['include'],
		$customWebserviceDetails['handler'],$customWebserviceDetails['type']);
	if($operationId === null && $operationId > 0){
		echo 'FAILED TO SETUP '.$customWebserviceDetails['name'].' WEBSERVICE';
		die;
	}
	$sequence = 1;
	foreach ($customWebserviceParams as $param) {
		$status = vtws_addWebserviceOperationParam($operationId,$param['name'],$param['type'],$sequence++);
		if($status === false){
			echo 'FAILED TO SETUP '.$customWebserviceDetails['name'].' WEBSERVICE HALFWAY THOURGH';
			die;
		}
	}

	$moduleList = vtws_getModuleNameList();
	foreach ($moduleList as $moduleName) {
		vtws_addDefaultModuleTypeEntity($moduleName);
	}

	ExecuteQuery("delete from vtiger_ws_fieldtype where uitype=116;");
	ExecuteQuery("update vtiger_field set uitype=117 where tabid=29 and fieldname='currency_id';");
}

VT520GA_webserviceMigrate();

ExecuteQuery("UPDATE vtiger_tab SET customized=1 WHERE name='ProjectTeam'");

function VT520GA_picklistMigrate(){
	global $adb;
	$columnList = $adb->getColumnNames('vtiger_invoicestatus');
	//fix the typo, where invoice is spelled as inovice
	if(in_array('inovicestatusid', $columnList)) {
		$sql = 'alter table vtiger_invoicestatus change inovicestatusid invoicestatusid int(19)';
		ExecuteQuery($sql);
	}
}

VT520GA_picklistMigrate();

$migrationlog->debug("\n\nDB Changes from 5.2.0 RC to 5.2.0 -------- Ends \n\n");
?>