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

$fld_module = vtlib_purify($_REQUEST["fld_module"]);
$id = vtlib_purify($_REQUEST["fld_id"]);
$colName = vtlib_purify($_REQUEST["colName"]);
$uitype = vtlib_purify($_REQUEST["uitype"]);

//Deleting the CustomField from the Custom Field Table
$query='delete from vtiger_field where fieldid=?';
$adb->pquery($query, array($id));

//Deleting from vtiger_profile2field table
$query='delete from vtiger_profile2field where fieldid=?';
$adb->pquery($query, array($id));

//Deleting from vtiger_def_org_field table
$query='delete from vtiger_def_org_field where fieldid=?';
$adb->pquery($query, array($id));

//Drop the column in the corresponding module table
$delete_module_tables = Array(
				"Leads"=>"vtiger_leadscf",
				"Accounts"=>"vtiger_accountscf",
				"Contacts"=>"vtiger_contactscf",
				"Potentials"=>"vtiger_potentialscf",
				"HelpDesk"=>"vtiger_ticketcf",
				"Products"=>"vtiger_productcf",
				"Vendors"=>"vtiger_vendorcf",
				"PriceBooks"=>"vtiger_pricebookcf",
				"PurchaseOrder"=>"vtiger_purchaseordercf",
				"SalesOrder"=>"vtiger_salesordercf",
				"Quotes"=>"vtiger_quotescf",
				"Invoice"=>"vtiger_invoicecf",
				"Campaigns"=>"vtiger_campaignscf",
				"Calendar"=>"vtiger_activitycf",
			     );

// vtlib customization: Hook added to allow action for custom modules too
$cftablename = $delete_module_tables[$fld_module];
if(empty($cftablename)) {
	include_once('data/CRMEntity.php');
	$focus = CRMEntity::getInstance($fld_module);
	$cftablename = $focus->customFieldTable[0];
}

$dbquery = 'alter table '. $cftablename .' drop column '. $adb->sql_escape_string($colName);
$adb->pquery($dbquery, array());

//To remove customfield entry from vtiger_field table
$dbquery = 'delete from vtiger_field where tablename= ? and fieldname=?';
$adb->pquery($dbquery, array($cftablename, $colName));
//we have to remove the entries in customview and report related tables which have this field ($colName)
$adb->pquery("delete from vtiger_cvcolumnlist where columnname like ?", array('%'.$colName.'%'));
$adb->pquery("delete from vtiger_cvstdfilter where columnname like ?", array('%'.$colName.'%'));
$adb->pquery("delete from vtiger_cvadvfilter where columnname like ?", array('%'.$colName.'%'));
$adb->pquery("delete from vtiger_selectcolumn where columnname like ?", array('%'.$colName.'%'));
$adb->pquery("delete from vtiger_relcriteria where columnname like ?", array('%'.$colName.'%'));
$adb->pquery("delete from vtiger_reportsortcol where columnname like ?", array('%'.$colName.'%'));
$adb->pquery("delete from vtiger_reportdatefilter where datecolumnname like ?", array('%'.$colName.'%'));
$adb->pquery("delete from vtiger_reportsummary where columnname like ?", array('%'.$colName.'%'));


//Deleting from convert lead mapping vtiger_table- Jaguar
if($fld_module=="Leads")
{
	$deletequery = 'delete from vtiger_convertleadmapping where leadfid=?';
	$adb->pquery($deletequery, array($id));
}elseif($fld_module=="Accounts" || $fld_module=="Contacts" || $fld_module=="Potentials")
{
	$map_del_id = array("Accounts"=>"accountfid","Contacts"=>"contactfid","Potentials"=>"potentialfid");
	$map_del_q = "update vtiger_convertleadmapping set ".$map_del_id[$fld_module]."=0 where ".$map_del_id[$fld_module]."=?";
	$adb->pquery($map_del_q, array($id));
}

//HANDLE HERE - we have to remove the table for other picklist type values which are text area and multiselect combo box 
if($uitype == 15)
{
	$deltablequery = 'drop table vtiger_'.$adb->sql_escape_string($colName);
	$adb->pquery($deltablequery, array());
}

header("Location:index.php?module=Settings&action=CustomFieldList&fld_module=".$fld_module."&parenttab=Settings");
?>