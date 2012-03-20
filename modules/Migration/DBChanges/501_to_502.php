<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/


//5.0.2 database changes - added on 27-10-06
//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.0.1 to 5.0.2 -------- Starts \n\n");

//Query added to show Manufacturer field in Products module
ExecuteQuery("update vtiger_field set displaytype=1,block=31 where tabid=14 and block=1");
ExecuteQuery("update vtiger_field set block=23,displaytype=1 where block=1 and displaytype=23 and tabid=10");
ExecuteQuery("update vtiger_field set block=22,displaytype=1 where block=1 and displaytype=22 and tabid=10");

//Added to rearange the attachment in HelpDesk
ExecuteQuery(" update vtiger_field set block=25,sequence=12 where tabid=13 and fieldname='filename'");

//Query added to as entityname,its tablename,its primarykey are saved in a table
ExecuteQuery(" CREATE TABLE `vtiger_entityname` (
	`tabid` int(19) NOT NULL default '0',
	`modulename` varchar(50) NOT NULL,
	`tablename` varchar(50) NOT NULL,
	`fieldname` varchar(150) NOT NULL,
	`entityidfield` varchar(150) NOT NULL,
	PRIMARY KEY (`tabid`),
	KEY `entityname_tabid_idx` (`tabid`)
)");

//Data Populated for the existing modules
ExecuteQuery("insert into vtiger_entityname values(7,'Leads','vtiger_leaddetails','lastname,firstname','leadid')");
ExecuteQuery("insert into vtiger_entityname values(6,'Accounts','vtiger_account','accountname','accountid')");
ExecuteQuery("insert into vtiger_entityname values(4,'Contacts','vtiger_contactdetails','lastname,firstname','contactid')");
ExecuteQuery("insert into vtiger_entityname values(2,'Potentials','vtiger_potential','potentialname','potentialid')");
ExecuteQuery("insert into vtiger_entityname values(8,'Notes','vtiger_notes','title','notesid')");
ExecuteQuery("insert into vtiger_entityname values(13,'HelpDesk','vtiger_troubletickets','title','ticketid')");
ExecuteQuery("insert into vtiger_entityname values(9,'Calendar','vtiger_activity','subject','activityid')");
ExecuteQuery("insert into vtiger_entityname values(10,'Emails','vtiger_activity','subject','activityid')");
ExecuteQuery("insert into vtiger_entityname values(14,'Products','vtiger_products','productname','productid')");
ExecuteQuery("insert into vtiger_entityname values(29,'Users','vtiger_users','last_name,first_name','id')");
ExecuteQuery("insert into vtiger_entityname values(23,'Invoice','vtiger_invoice','subject','invoiceid')");
ExecuteQuery("insert into vtiger_entityname values(20,'Quotes','vtiger_quotes','subject','quoteid')");
ExecuteQuery("insert into vtiger_entityname values(21,'PurchaseOrder','vtiger_purchaseorder','subject','purchaseorderid')");
ExecuteQuery("insert into vtiger_entityname values(22,'SalesOrder','vtiger_salesorder','subject','salesorderid')");
ExecuteQuery("insert into vtiger_entityname values(18,'Vendors','vtiger_vendor','vendorname','vendorid')");
ExecuteQuery("insert into vtiger_entityname values(19,'PriceBooks','vtiger_pricebook','bookname','pricebookid')");
ExecuteQuery("insert into vtiger_entityname values(26,'Campaigns','vtiger_campaign','campaignname','campaignid')");
ExecuteQuery("insert into vtiger_entityname values(15,'Faq','vtiger_faq','question','id')");

//added quantity in stock in product default listview - All
$res = $adb->query("select vtiger_cvcolumnlist.cvid from vtiger_cvcolumnlist inner join vtiger_customview on vtiger_cvcolumnlist.cvid=vtiger_customview.cvid where entitytype='Products' and viewname='All'");
if($adb->num_rows != 0)
{
	$cvid = $adb->query_result($res,0,'cvid');
	$adb->query("insert into vtiger_cvcolumnlist values($cvid,5,'vtiger_products:qtyinstock:qtyinstock:Products_Quantity_In_Stock:V')");
}


//echo "<br><font color='red'>&nbsp; 5.0/5.0.1 ==> 5.0.2 Database changes has been done.</font><br>";

$migrationlog->debug("\n\nDB Changes from 5.0.1 to 5.0.2 -------- Ends \n\n");

?>
