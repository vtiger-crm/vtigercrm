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
//require_once('modules/Reports/CannedReports.php');
global $adb;

$rptfolder = Array(Array('Account and Contact Reports'=>'Account and Contact Reports'),
		   Array('Lead Reports'=>'Lead Reports'),
	           Array('Potential Reports'=>'Potential Reports'),
		   Array('Activity Reports'=>'Activity Reports'),
		   Array('HelpDesk Reports'=>'HelpDesk Reports'),
		   Array('Product Reports'=>'Product Reports'),
		   Array('Quote Reports' =>'Quote Reports'),
		   Array('PurchaseOrder Reports'=>'PurchaseOrder Reports'),
		   Array('Invoice Reports'=>'Invoice Reports'),
		   Array('SalesOrder Reports'=>'SalesOrder Reports'),
		   Array('Campaign Reports'=>'Campaign Reports')
                  );

$reportmodules = Array(Array('primarymodule'=>'Contacts','secondarymodule'=>'Accounts'),
		       Array('primarymodule'=>'Contacts','secondarymodule'=>'Accounts'),
		       Array('primarymodule'=>'Contacts','secondarymodule'=>'Potentials'),
		       Array('primarymodule'=>'Leads','secondarymodule'=>''),
		       Array('primarymodule'=>'Leads','secondarymodule'=>''),
		       Array('primarymodule'=>'Potentials','secondarymodule'=>''),
		       Array('primarymodule'=>'Potentials','secondarymodule'=>''),
		       Array('primarymodule'=>'Calendar','secondarymodule'=>''),
		       Array('primarymodule'=>'Calendar','secondarymodule'=>''),
		       Array('primarymodule'=>'HelpDesk','secondarymodule'=>'Products'),
		       Array('primarymodule'=>'HelpDesk','secondarymodule'=>''),
  		       Array('primarymodule'=>'HelpDesk','secondarymodule'=>''),
		       Array('primarymodule'=>'Products','secondarymodule'=>''),
		       Array('primarymodule'=>'Products','secondarymodule'=>'Contacts'),
		       Array('primarymodule'=>'Quotes','secondarymodule'=>''),
		       Array('primarymodule'=>'Quotes','secondarymodule'=>''),
		       Array('primarymodule'=>'PurchaseOrder','secondarymodule'=>'Contacts'),
		       Array('primarymodule'=>'PurchaseOrder','secondarymodule'=>''),
		       Array('primarymodule'=>'Invoice','secondarymodule'=>''),
		       Array('primarymodule'=>'SalesOrder','secondarymodule'=>''),
		       Array('primarymodule'=>'Campaigns','secondarymodule'=>'')
		      );

$selectcolumns = Array(Array('vtiger_contactdetails:firstname:Contacts_First_Name:firstname:V',
                             'vtiger_contactdetails:lastname:Contacts_Last_Name:lastname:V',
                             'vtiger_contactsubdetails:leadsource:Contacts_Lead_Source:leadsource:V',
                             'vtiger_contactdetails:accountid:Contacts_Account_Name:account_id:V',
			     'vtiger_account:industry:Accounts_industry:industry:V',
			     'vtiger_contactdetails:email:Contacts_Email:email:E'),

		       Array('vtiger_contactdetails:firstname:Contacts_First_Name:firstname:V',
                             'vtiger_contactdetails:lastname:Contacts_Last_Name:lastname:V',
                             'vtiger_contactsubdetails:leadsource:Contacts_Lead_Source:leadsource:V',
                             'vtiger_contactdetails:accountid:Contacts_Account_Name:account_id:V',
                             'vtiger_account:industry:Accounts_industry:industry:V',
                             'vtiger_contactdetails:email:Contacts_Email:email:E'),

		       Array('vtiger_contactdetails:firstname:Contacts_First_Name:firstname:V',
                             'vtiger_contactdetails:lastname:Contacts_Last_Name:lastname:V',
                             'vtiger_contactdetails:accountid:Contacts_Account_Name:account_id:V',
                             'vtiger_contactdetails:email:Contacts_Email:email:E',
                             'vtiger_potential:potentialname:Potentials_Potential_Name:potentialname:V',
                             'vtiger_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V'),

		       Array('vtiger_leaddetails:firstname:Leads_First_Name:firstname:V',
			     'vtiger_leaddetails:lastname:Leads_Last_Name:lastname:V',
			     'vtiger_leaddetails:company:Leads_Company:company:V',
			     'vtiger_leaddetails:email:Leads_Email:email:E',
			     'vtiger_leaddetails:leadsource:Leads_Lead_Source:leadsource:V'),

		       Array('vtiger_leaddetails:firstname:Leads_First_Name:firstname:V',
                             'vtiger_leaddetails:lastname:Leads_Last_Name:lastname:V',
                             'vtiger_leaddetails:company:Leads_Company:company:V',
                             'vtiger_leaddetails:email:Leads_Email:email:E',
			     'vtiger_leaddetails:leadsource:Leads_Lead_Source:leadsource:V',
			     'vtiger_leaddetails:leadstatus:Leads_Lead_Status:leadstatus:V'),

		       Array('vtiger_potential:potentialname:Potentials_Potential_Name:potentialname:V',
                             'vtiger_potential:amount:Potentials_Amount:amount:N',
                             'vtiger_potential:potentialtype:Potentials_Type:opportunity_type:V',
                             'vtiger_potential:leadsource:Potentials_Lead_Source:leadsource:V',
                             'vtiger_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V'),

		       Array('vtiger_potential:potentialname:Potentials_Potential_Name:potentialname:V',
                             'vtiger_potential:amount:Potentials_Amount:amount:N',
                             'vtiger_potential:potentialtype:Potentials_Type:opportunity_type:V',
                             'vtiger_potential:leadsource:Potentials_Lead_Source:leadsource:V',
			     'vtiger_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V'),

		       Array('vtiger_activity:subject:Calendar_Subject:subject:V',
			     'vtiger_contactdetailsCalendar:lastname:Calendar_Contact_Name:contact_id:I',
                             'vtiger_activity:status:Calendar_Status:taskstatus:V',
                             'vtiger_activity:priority:Calendar_Priority:taskpriority:V',
                             'vtiger_usersCalendar:user_name:Calendar_Assigned_To:assigned_user_id:V'),

		       Array('vtiger_activity:subject:Calendar_Subject:subject:V',
                             'vtiger_contactdetailsCalendar:lastname:Calendar_Contact_Name:contact_id:I',
                             'vtiger_activity:status:Calendar_Status:taskstatus:V',
                             'vtiger_activity:priority:Calendar_Priority:taskpriority:V',
                             'vtiger_usersCalendar:user_name:Calendar_Assigned_To:assigned_user_id:V'),

        	       Array('vtiger_troubletickets:title:HelpDesk_Title:ticket_title:V',
                             'vtiger_troubletickets:status:HelpDesk_Status:ticketstatus:V',
                             'vtiger_products:productname:Products_Product_Name:productname:V',
                             'vtiger_products:discontinued:Products_Product_Active:discontinued:V',
                             'vtiger_products:productcategory:Products_Product_Category:productcategory:V',
			     'vtiger_products:manufacturer:Products_Manufacturer:manufacturer:V'),

 		       Array('vtiger_troubletickets:title:HelpDesk_Title:ticket_title:V',
                             'vtiger_troubletickets:priority:HelpDesk_Priority:ticketpriorities:V',
                             'vtiger_troubletickets:severity:HelpDesk_Severity:ticketseverities:V',
                             'vtiger_troubletickets:status:HelpDesk_Status:ticketstatus:V',
                             'vtiger_troubletickets:category:HelpDesk_Category:ticketcategories:V',
                             'vtiger_usersHelpDesk:user_name:HelpDesk_Assigned_To:assigned_user_id:V'),

		       Array('vtiger_troubletickets:title:HelpDesk_Title:ticket_title:V',
                             'vtiger_troubletickets:priority:HelpDesk_Priority:ticketpriorities:V',
                             'vtiger_troubletickets:severity:HelpDesk_Severity:ticketseverities:V',
                             'vtiger_troubletickets:status:HelpDesk_Status:ticketstatus:V',
                             'vtiger_troubletickets:category:HelpDesk_Category:ticketcategories:V',
                             'vtiger_usersHelpDesk:user_name:HelpDesk_Assigned_To:assigned_user_id:V'),

 		       Array('vtiger_products:productname:Products_Product_Name:productname:V',
                             'vtiger_products:productcode:Products_Product_Code:productcode:V',
                             'vtiger_products:discontinued:Products_Product_Active:discontinued:V',
                             'vtiger_products:productcategory:Products_Product_Category:productcategory:V',
                             'vtiger_products:website:Products_Website:website:V',
			     'vtiger_vendorRelProducts:vendorname:Products_Vendor_Name:vendor_id:I',
			     'vtiger_products:mfr_part_no:Products_Mfr_PartNo:mfr_part_no:V'),

		       Array('vtiger_products:productname:Products_Product_Name:productname:V',
                             'vtiger_products:manufacturer:Products_Manufacturer:manufacturer:V',
                             'vtiger_products:productcategory:Products_Product_Category:productcategory:V',
                             'vtiger_contactdetails:firstname:Contacts_First_Name:firstname:V',
                             'vtiger_contactdetails:lastname:Contacts_Last_Name:lastname:V',
                             'vtiger_contactsubdetails:leadsource:Contacts_Lead_Source:leadsource:V'),

		       Array('vtiger_quotes:subject:Quotes_Subject:subject:V',
                             'vtiger_potentialRelQuotes:potentialname:Quotes_Potential_Name:potential_id:I',
                             'vtiger_quotes:quotestage:Quotes_Quote_Stage:quotestage:V',
                             'vtiger_quotes:contactid:Quotes_Contact_Name:contact_id:V',
                             'vtiger_usersRel1:user_name:Quotes_Inventory_Manager:assigned_user_id1:I',
                             'vtiger_accountQuotes:accountname:Quotes_Account_Name:account_id:I'),

		       Array('vtiger_quotes:subject:Quotes_Subject:subject:V',
                             'vtiger_potentialRelQuotes:potentialname:Quotes_Potential_Name:potential_id:I',
                             'vtiger_quotes:quotestage:Quotes_Quote_Stage:quotestage:V',
                             'vtiger_quotes:contactid:Quotes_Contact_Name:contact_id:V',
                             'vtiger_usersRel1:user_name:Quotes_Inventory_Manager:assigned_user_id1:I',
                             'vtiger_accountQuotes:accountname:Quotes_Account_Name:account_id:I',
			     'vtiger_quotes:carrier:Quotes_Carrier:carrier:V',
			     'vtiger_quotes:shipping:Quotes_Shipping:shipping:V'),

		       Array('vtiger_purchaseorder:subject:PurchaseOrder_Subject:subject:V',
			     'vtiger_vendorRelPurchaseOrder:vendorname:PurchaseOrder_Vendor_Name:vendor_id:I',
			     'vtiger_purchaseorder:tracking_no:PurchaseOrder_Tracking_Number:tracking_no:V',
			     'vtiger_contactdetails:firstname:Contacts_First_Name:firstname:V',
			     'vtiger_contactdetails:lastname:Contacts_Last_Name:lastname:V',
			     'vtiger_contactsubdetails:leadsource:Contacts_Lead_Source:leadsource:V',
			     'vtiger_contactdetails:email:Contacts_Email:email:E'),

		       Array('vtiger_purchaseorder:subject:PurchaseOrder_Subject:subject:V',
			     'vtiger_vendorRelPurchaseOrder:vendorname:PurchaseOrder_Vendor_Name:vendor_id:I',
			     'vtiger_purchaseorder:requisition_no:PurchaseOrder_Requisition_No:requisition_no:V',
                             'vtiger_purchaseorder:tracking_no:PurchaseOrder_Tracking_Number:tracking_no:V',
			     'vtiger_contactdetailsPurchaseOrder:lastname:PurchaseOrder_Contact_Name:contact_id:I',
			     'vtiger_purchaseorder:carrier:PurchaseOrder_Carrier:carrier:V',
			     'vtiger_purchaseorder:salescommission:PurchaseOrder_Sales_Commission:salescommission:N',
			     'vtiger_purchaseorder:exciseduty:PurchaseOrder_Excise_Duty:exciseduty:N',
                             'vtiger_usersPurchaseOrder:user_name:PurchaseOrder_Assigned_To:assigned_user_id:V'),

		       Array('vtiger_invoice:subject:Invoice_Subject:subject:V',
			     'vtiger_invoice:salesorderid:Invoice_Sales_Order:salesorder_id:I',
			     'vtiger_invoice:customerno:Invoice_Customer_No:customerno:V',
			     'vtiger_invoice:exciseduty:Invoice_Excise_Duty:exciseduty:N',
			     'vtiger_invoice:salescommission:Invoice_Sales_Commission:salescommission:N',
			     'vtiger_accountInvoice:accountname:Invoice_Account_Name:account_id:I'),

		       Array('vtiger_salesorder:subject:SalesOrder_Subject:subject:V',
			     'vtiger_quotesSalesOrder:subject:SalesOrder_Quote_Name:quote_id:I',
			     'vtiger_contactdetailsSalesOrder:lastname:SalesOrder_Contact_Name:contact_id:I',
			     'vtiger_salesorder:duedate:SalesOrder_Due_Date:duedate:D',
			     'vtiger_salesorder:carrier:SalesOrder_Carrier:carrier:V',
			     'vtiger_salesorder:sostatus:SalesOrder_Status:sostatus:V',
			     'vtiger_accountSalesOrder:accountname:SalesOrder_Account_Name:account_id:I',
			     'vtiger_salesorder:salescommission:SalesOrder_Sales_Commission:salescommission:N',
			     'vtiger_salesorder:exciseduty:SalesOrder_Excise_Duty:exciseduty:N',
			     'vtiger_usersSalesOrder:user_name:SalesOrder_Assigned_To:assigned_user_id:V'),

		       Array('vtiger_campaign:campaignname:Campaigns_Campaign_Name:campaignname:V',
			     'vtiger_campaign:campaigntype:Campaigns_Campaign_Type:campaigntype:V',
			     'vtiger_campaign:targetaudience:Campaigns_Target_Audience:targetaudience:V',
			     'vtiger_campaign:budgetcost:Campaigns_Budget_Cost:budgetcost:I',
			     'vtiger_campaign:actualcost:Campaigns_Actual_Cost:actualcost:I',
			     'vtiger_campaign:expectedrevenue:Campaigns_Expected_Revenue:expectedrevenue:I',
			     'vtiger_campaign:expectedsalescount:Campaigns_Expected_Sales_Count:expectedsalescount:N',
			     'vtiger_campaign:actualsalescount:Campaigns_Actual_Sales_Count:actualsalescount:N',
			     'vtiger_usersCampaigns:user_name:Campaigns_Assigned_To:assigned_user_id:V')
			);

$reports = Array(Array('reportname'=>'Contacts by Accounts',
                       'reportfolder'=>'Account and Contact Reports',
                       'description'=>'Contacts related to Accounts',
                       'reporttype'=>'tabular',
		       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'0'),

		 Array('reportname'=>'Contacts without Accounts',
                       'reportfolder'=>'Account and Contact Reports',
                       'description'=>'Contacts not related to Accounts',
                       'reporttype'=>'tabular',
		       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'1'),

		 Array('reportname'=>'Contacts by Potentials',
                       'reportfolder'=>'Account and Contact Reports',
                       'description'=>'Contacts related to Potentials',
                       'reporttype'=>'tabular',
		       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'2'),

		 Array('reportname'=>'Lead by Source',
                       'reportfolder'=>'Lead Reports',
                       'description'=>'Lead by Source',
                       'reporttype'=>'summary',
		       'sortid'=>'0','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Lead Status Report',
                       'reportfolder'=>'Lead Reports',
                       'description'=>'Lead Status Report',
                       'reporttype'=>'summary',
                       'sortid'=>'1','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Potential Pipeline',
                       'reportfolder'=>'Potential Reports',
                       'description'=>'Potential Pipeline',
                       'reporttype'=>'summary',
                       'sortid'=>'2','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Closed Potentials',
                       'reportfolder'=>'Potential Reports',
                       'description'=>'Potential that have Won',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'3'),

		 Array('reportname'=>'Last Month Activities',
                       'reportfolder'=>'Activity Reports',
                       'description'=>'Last Month Activities',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'0','advfilterid'=>''),

		 Array('reportname'=>'This Month Activities',
                       'reportfolder'=>'Activity Reports',
                       'description'=>'This Month Activities',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'1','advfilterid'=>''),

		 Array('reportname'=>'Tickets by Products',
                       'reportfolder'=>'HelpDesk Reports',
                       'description'=>'Tickets related to Products',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Tickets by Priority',
                       'reportfolder'=>'HelpDesk Reports',
                       'description'=>'Tickets by Priority',
                       'reporttype'=>'summary',
                       'sortid'=>'3','stdfilterid'=>'','advfilterid'=>''),

 		 Array('reportname'=>'Open Tickets',
                       'reportfolder'=>'HelpDesk Reports',
                       'description'=>'Tickets that are Open',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'4'),

		 Array('reportname'=>'Product Details',
                       'reportfolder'=>'Product Reports',
                       'description'=>'Product Detailed Report',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Products by Contacts',
                       'reportfolder'=>'Product Reports',
                       'description'=>'Products related to Contacts',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Open Quotes',
                       'reportfolder'=>'Quote Reports',
                       'description'=>'Quotes that are Open',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'5'),

		 Array('reportname'=>'Quotes Detailed Report',
                       'reportfolder'=>'Quote Reports',
                       'description'=>'Quotes Detailed Report',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'PurchaseOrder by Contacts',
                       'reportfolder'=>'PurchaseOrder Reports',
                       'description'=>'PurchaseOrder related to Contacts',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'PurchaseOrder Detailed Report',
                       'reportfolder'=>'PurchaseOrder Reports',
                       'description'=>'PurchaseOrder Detailed Report',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'Invoice Detailed Report',
                       'reportfolder'=>'Invoice Reports',
                       'description'=>'Invoice Detailed Report',
                       'reporttype'=>'tabular',
		       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

		 Array('reportname'=>'SalesOrder Detailed Report',
                       'reportfolder'=>'SalesOrder Reports',
                       'description'=>'SalesOrder Detailed Report',
                       'reporttype'=>'tabular',
                       'sortid'=>'','stdfilterid'=>'','advfilterid'=>''),

	         Array('reportname'=>'Campaign Expectations and Actuals',
		       'reportfolder'=>'Campaign Reports',
		       'description'=>'Campaign Expectations and Actuals',
		       'reporttype'=>'tabular',
		       'sortid'=>'','stdfilterid'=>'','advfilterid'=>'')

		);

$sortorder = Array(
                   Array(
                         Array('columnname'=>'vtiger_leaddetails:leadsource:Leads_Lead_Source:leadsource:V',
                               'sortorder'=>'Ascending'
                              )
			),
		   Array(
                         Array('columnname'=>'vtiger_leaddetails:leadstatus:Leads_Lead_Status:leadstatus:V',
                               'sortorder'=>'Ascending'
                              )
                        ),
		   Array(
                         Array('columnname'=>'vtiger_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V',
                               'sortorder'=>'Ascending'
                              )
                        ),
		   Array(
                         Array('columnname'=>'vtiger_troubletickets:priority:HelpDesk_Priority:ticketpriorities:V',
                               'sortorder'=>'Ascending'
                              )
                        )
                  );

$stdfilters = Array(Array('columnname'=>'vtiger_crmentity:modifiedtime:modifiedtime:Calendar_Modified_Time',
			  'datefilter'=>'lastmonth',
			  'startdate'=>'2005-05-01',
			  'enddate'=>'2005-05-31'),

		    Array('columnname'=>'vtiger_crmentity:modifiedtime:modifiedtime:Calendar_Modified_Time',
                          'datefilter'=>'thismonth',
                          'startdate'=>'2005-06-01',
                          'enddate'=>'2005-06-30')
		   );

$advfilters = Array(
                      Array(
                            Array('columnname'=>'vtiger_contactdetails:accountid:Contacts_Account_Name:account_id:V',
                                  'comparator'=>'n',
                                  'value'=>''
                                 )
                           ),
		      Array(
                            Array('columnname'=>'vtiger_contactdetails:accountid:Contacts_Account_Name:account_id:V',
                                  'comparator'=>'e',
                                  'value'=>''
                                 )
                           ),
		      Array(
                            Array('columnname'=>'vtiger_potential:potentialname:Potentials_Potential_Name:potentialname:V',
                                  'comparator'=>'n',
                                  'value'=>''
                                 )
                           ),
		      Array(
                            Array('columnname'=>'vtiger_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V',
                                  'comparator'=>'e',
                                  'value'=>'Closed Won'
                                 )
                           ),
		      Array(
                            Array('columnname'=>'vtiger_troubletickets:status:HelpDesk_Status:ticketstatus:V',
                                  'comparator'=>'n',
                                  'value'=>'Closed'
                                 )
                           ),
		      Array(
                            Array('columnname'=>'vtiger_quotes:quotestage:Quotes_Quote_Stage:quotestage:V',
                                  'comparator'=>'n',
                                  'value'=>'Accepted'
                                 ),
			    Array('columnname'=>'vtiger_quotes:quotestage:Quotes_Quote_Stage:quotestage:V',
                                  'comparator'=>'n',
                                  'value'=>'Rejected'
                                 )
                           )
                     );
//quotes:quotestage:Quotes_Quote_Stage:quotestage:V
foreach($rptfolder as $key=>$rptarray)
{
        foreach($rptarray as $foldername=>$folderdescription)
        {
                PopulateReportFolder($foldername,$folderdescription);
                $reportid[$foldername] = $key+1;
        }
}

foreach($reports as $key=>$report)
{
        $queryid = insertSelectQuery();
        insertReports($queryid,$reportid[$report['reportfolder']],$report['reportname'],$report['description'],$report['reporttype']);
        insertSelectColumns($queryid,$selectcolumns[$key]);
        insertReportModules($queryid,$reportmodules[$key]['primarymodule'],$reportmodules[$key]['secondarymodule']);

	if(isset($stdfilters[$report['stdfilterid']]))
	{
		$i = $report['stdfilterid'];
		insertStdFilter($queryid,$stdfilters[$i]['columnname'],$stdfilters[$i]['datefilter'],$stdfilters[$i]['startdate'],$stdfilters[$i]['enddate']);
	}

	if(isset($advfilters[$report['advfilterid']]))
	{
		insertAdvFilter($queryid,$advfilters[$report['advfilterid']]);
	}

	if($report['reporttype'] == "summary")
	{
		insertSortColumns($queryid,$sortorder[$report['sortid']]);
	}
}
$adb->pquery("UPDATE vtiger_report SET sharingtype='Public'",array());
/** Function to store the foldername and folderdescription to database
 *  This function accepts the given folder name and description
 *  ans store it in db as SAVED
 */

function PopulateReportFolder($fldrname,$fldrdescription)
{
	global $adb;
	$sql = "INSERT INTO vtiger_reportfolder (FOLDERNAME,DESCRIPTION,STATE) VALUES(?,?,?)";
	$params = array($fldrname, $fldrdescription, 'SAVED');
	$result = $adb->pquery($sql, $params);
}

/** Function to add an entry in selestquery vtiger_table
 *
 */

function insertSelectQuery()
{
	global $adb;
	$genQueryId = $adb->getUniqueID("vtiger_selectquery");
        if($genQueryId != "")
        {
		$iquerysql = "insert into vtiger_selectquery (QUERYID,STARTINDEX,NUMOFOBJECTS) values (?,?,?)";
		$iquerysqlresult = $adb->pquery($iquerysql, array($genQueryId,0,0));
	}

	return $genQueryId;
}

/** Function to store the vtiger_field names selected for a vtiger_report to a database
 *
 *
 */

function insertSelectColumns($queryid,$columnname)
{
	global $adb;
	if($queryid != "")
	{
		for($i=0;$i < count($columnname);$i++)
		{
			$icolumnsql = "insert into vtiger_selectcolumn (QUERYID,COLUMNINDEX,COLUMNNAME) values (?,?,?)";
			$icolumnsqlresult = $adb->pquery($icolumnsql, array($queryid, $i, $columnname[$i]));
		}
	}
}


/** Function to store the vtiger_report details to database
 *  This function accepts queryid,folderid,reportname,description,reporttype
 *  as arguments and store the informations in vtiger_report vtiger_table
 */

function insertReports($queryid,$folderid,$reportname,$description,$reporttype)
{
	global $adb;
	if($queryid != "")
	{
		$ireportsql = "insert into vtiger_report (REPORTID,FOLDERID,REPORTNAME,DESCRIPTION,REPORTTYPE,QUERYID,STATE) values (?,?,?,?,?,?,?)";
        $ireportparams = array($queryid, $folderid, $reportname, $description, $reporttype, $queryid, 'SAVED');
		$ireportresult = $adb->pquery($ireportsql, $ireportparams);
	}
}

/** Function to store the vtiger_report modules to database
 *  This function accepts queryid,primary module and secondary module
 *  as arguments and store the informations in vtiger_reportmodules vtiger_table
 */


function insertReportModules($queryid,$primarymodule,$secondarymodule)
{
	global $adb;
	if($queryid != "")
	{
		$ireportmodulesql = "insert into vtiger_reportmodules (REPORTMODULESID,PRIMARYMODULE,SECONDARYMODULES) values (?,?,?)";
		$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($queryid, $primarymodule, $secondarymodule));
	}
}


/** Function to store the vtiger_report sortorder to database
 *  This function accepts queryid,sortlists
 *  as arguments and store the informations sort columns and
 *  and sortorder in vtiger_reportsortcol vtiger_table
 */


function insertSortColumns($queryid,$sortlists)
{
	global $adb;
	if($queryid != "")
	{
		foreach($sortlists as $i=>$sort)
                {
			$sort_bysql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
			$sort_byresult = $adb->pquery($sort_bysql, array(($i+1), $queryid, $sort['columnname'], $sort['sortorder']));
		}
	}

}


/** Function to store the vtiger_report sort date details to database
 *  This function accepts queryid,filtercolumn,datefilter,startdate,enddate
 *  as arguments and store the informations in vtiger_reportdatefilter vtiger_table
 */


function insertStdFilter($queryid,$filtercolumn,$datefilter,$startdate,$enddate)
{
	global $adb;
	if($queryid != "")
	{
		$ireportmodulesql = "insert into vtiger_reportdatefilter (DATEFILTERID,DATECOLUMNNAME,DATEFILTER,STARTDATE,ENDDATE) values (?,?,?,?,?)";
		$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($queryid, $filtercolumn, $datefilter, $startdate, $enddate));
	}

}

/** Function to store the vtiger_report conditions to database
 *  This function accepts queryid,filters
 *  as arguments and store the informations in vtiger_relcriteria vtiger_table
 */


function insertAdvFilter($queryid,$filters)
{
	global $adb;
	if($queryid != "")
	{
		$columnIndexArray = array();
		foreach($filters as $i=>$filter)
		{
			$irelcriteriasql = "insert into vtiger_relcriteria(QUERYID,COLUMNINDEX,COLUMNNAME,COMPARATOR,VALUE) values (?,?,?,?,?)";
			$irelcriteriaresult = $adb->pquery($irelcriteriasql, array($queryid,$i,$filter['columnname'],$filter['comparator'],$filter['value']));
			$columnIndexArray[] = $i;
		}
		$conditionExpression = implode(' and ', $columnIndexArray);
		$adb->pquery('INSERT INTO vtiger_relcriteria_grouping VALUES(?,?,?,?)', array(1, $queryid, '', $conditionExpression));
	}
}
?>