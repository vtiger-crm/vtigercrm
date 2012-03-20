<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
	
global $adb;
global $log;
$log->debug("Going to update the ListPrice in (modules/Products/UpdateListPrice.php).");
$record = vtlib_purify($_REQUEST['record']);
$pricebook_id = vtlib_purify($_REQUEST['pricebook_id']);
$product_id = vtlib_purify($_REQUEST['product_id']);
$listprice = vtlib_purify($_REQUEST['list_price']);
$return_action = vtlib_purify($_REQUEST['return_action']);
$return_module = vtlib_purify($_REQUEST['return_module']);

$query = "update vtiger_pricebookproductrel set listprice=? where pricebookid=? and productid=?";
$adb->pquery($query, array($listprice, $pricebook_id, $product_id)); 
header("Location: index.php?module=$return_module&action=".$return_module.
		"Ajax&file=$return_action&ajax=true&record=$record");
?>