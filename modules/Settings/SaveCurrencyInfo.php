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
global $adb;
$db = PearDatabase::getInstance();
$currency_name = $_REQUEST['currency_name'];
$currency_code= $_REQUEST['currency_code'];
$currency_symbol= $_REQUEST['currency_symbol'];
$conversion_rate= $_REQUEST['conversion_rate'];
if(isset($_REQUEST['currency_status']) && $_REQUEST['currency_status'] != '')
	$currency_status= $_REQUEST['currency_status'];
else
	$currency_status= 'Active';
if(isset($_REQUEST['record']) && $_REQUEST['record']!='')
{
	$cur_status_res = $adb->pquery("select currency_status from vtiger_currency_info where id=?", array($_REQUEST['record']));
	$old_cur_status = $adb->query_result($cur_status_res,0,'currency_status');
	
	if($currency_status != $old_cur_status && $currency_status == 'Inactive') {
		$transfer_cur_id = $_REQUEST['transfer_currency_id'];
		if($transfer_cur_id != null) transferCurrency($_REQUEST['record'], $transfer_cur_id);
	}
	
	$sql = "update vtiger_currency_info set currency_name =?, currency_code =?, currency_symbol =?, conversion_rate =?,currency_status=? where id =?";
	$params = array($currency_name, $currency_code, $currency_symbol, $conversion_rate, $currency_status, $_REQUEST['record']);
}
else
{
    $sql = "insert into vtiger_currency_info values(?,?,?,?,?,?,?,?)";
	$params = array($db->getUniqueID("vtiger_currency_info"), $currency_name, $currency_code, $currency_symbol, $conversion_rate, $currency_status,'0','0');
}
$adb->pquery($sql, $params);
$loc = "Location: index.php?module=Settings&action=CurrencyListView&parenttab=".vtlib_purify($_REQUEST['parenttab']);
header($loc);
?>