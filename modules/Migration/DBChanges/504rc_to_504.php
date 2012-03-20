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


//5.0.4 RC to 5.0.4 database changes

//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.0.4rc to 5.0.4 -------- Starts \n\n");

//Increased the size of salution field for Leads module
ExecuteQuery("alter table vtiger_leaddetails modify column salutation varchar(50)");

//Added to handle the crypt_type in users table. From 5.0.4 onwards the default crypt type will be MD5. But for existing users crypt type will be empty untill they change their password. Once the existing users change the password then their crypt type will be set as MD5
ExecuteQuery("alter table vtiger_users add column crypt_type varchar(20) not null default 'MD5'");
ExecuteQuery("update vtiger_users set crypt_type=''");

//In 503 to 504rc release we have included the role based picklist migration but the sequence tables for corresponding picklists are not handled. Now we are handling the sequence tables
//Popullating arry with picklist field names
$picklist_arr = array('leadsource'=>'leadsourceid','accounttype'=>'accounttypeid','industry'=>'industryid','leadstatus'=>'leadstatusid','rating'=>'rating_id','opportunity_type'=>'opptypeid','salutationtype'=>'salutationid','sales_stage'=>'sales_stage_id','ticketstatus'=>'ticketstatus_id','ticketpriorities'=>'ticketpriorities_id','ticketseverities'=>'ticketseverities_id','ticketcategories'=>'ticketcategories_id','eventstatus'=>'eventstatusid','taskstatus'=>'taskstatusid','taskpriority'=>'taskpriorityid','manufacturer'=>'manufacturerid','productcategory'=>'productcategoryid','faqcategories'=>'faqcategories_id','usageunit'=>'usageunitid','glacct'=>'glacctid','quotestage'=>'quotestageid','carrier'=>'carrierid','faqstatus'=>'faqstatus_id','invoicestatus'=>'inovicestatusid','postatus'=>'postatusid','sostatus'=>'sostatusid','campaigntype'=>'campaigntypeid','campaignstatus'=>'campaignstatusid','expectedresponse'=>'expectedresponseid');

$custom_result = $adb->query("select fieldname from vtiger_field where (uitype=15 or uitype=33) and fieldname like '%cf_%'");
$numrow = $adb->num_rows($custom_result);
for($i=0; $i < $numrow; $i++)
{
	$fieldname=$adb->query_result($custom_result,$i,'fieldname');
	$picklist_arr[$fieldname] = $adb->query_result($custom_result,$i,'fieldname')."id";
}

foreach($picklist_arr as $picklistname => $picklistidname)
{
	$result = $adb->query("select max(".$picklistidname.") as id from vtiger_".$picklistname);
	$max_count = 1;
	if ($adb->num_rows($result) > 0) {
		$max_count = $adb->query_result($result,0,'id');
		if ($max_count <= 0) $max_count = 1;
	}
	$adb->query("drop table if exists vtiger_".$picklistname."_seq");
	$adb->query("create table vtiger_".$picklistname."_seq (id integer(11))");
	$adb->query("insert into vtiger_".$picklistname."_seq (id) values(".$max_count.")");

	//In 5.0.3 to 5.0.4 RC migration, for some utf8 character picklist values, picklist_valueid is set as 0 because of query instead of pquery
	$result = $adb->query("select * from vtiger_$picklistname where picklist_valueid=0");
	$numrow = $adb->num_rows($result);
	for($i=0; $i < $numrow; $i++)
	{
		$picklist_array_values[$picklistname][] = decode_html($adb->query_result($result,$i,$picklistname));
	}

	//we have retrieved the picklist values to which the picklist_valueid is 0. So we can delete those entries
	$adb->query("delete from vtiger_$picklistname where picklist_valueid=0");

	$temp_array = $picklist_array_values[$picklistname];
	if(is_array($temp_array))
	foreach($temp_array as $ind => $picklist_value)
	{
		$picklist_autoincrementid = $adb->getUniqueID($picklistname);//auto increment for each picklist table
		$picklist_valueid = getUniquePicklistID();//unique value id for each picklist value

		$picklistquery = "insert into vtiger_$picklistname values(?,?,?,?) ";
		$adb->pquery($picklistquery, array($picklist_autoincrementid, $picklist_value, 1, $picklist_valueid));

		//get the picklist's unique id from vtiger_picklist table
		$res = $adb->query("select * from vtiger_picklist where name='$picklistname'");
		$picklistid = $adb->query_result($res, 0, 'picklistid');

		//we have to insert the picklist value in vtiger_role2picklist table for each available roles
		$sql="select roleid from vtiger_role";
		$role_result = $adb->query($sql);
		$numrows = $adb->num_rows($role_result);

		for($k=0; $k < $numrows; $k++)
		{
			$roleid = $adb->query_result($role_result,$k,'roleid');

			//get the max sortid for each picklist
			$res = $adb->query("select max(sortid)+1 sortid from vtiger_role2picklist where roleid = '$roleid' and picklistid ='$picklistid'");
			$sortid = $adb->query_result($res, 0, 'sortid');

			$query = "insert into vtiger_role2picklist values(?,?,?,?)";
			$adb->pquery($query, array($roleid, $picklist_valueid, $picklistid, $sortid));
		}
	}
}

//When we change the ticket description from troubletickets table to crmentity table we have handled in customview but missed in reports - #4968
ExecuteQuery("update vtiger_selectcolumn set columnname='vtiger_crmentity:description:HelpDesk_Description:description:V' where columnname='vtiger_troubletickets:description:HelpDesk_Description:description:V'");
ExecuteQuery("update vtiger_relcriteria set columnname='vtiger_crmentityHelpDesk:description:HelpDesk_Description:description:V' where columnname='vtiger_troubletickets:description:HelpDesk_Description:description:V'");
ExecuteQuery("update vtiger_reportsortcol set columnname='vtiger_crmentityHelpDesk:description:HelpDesk_Description:description:V' where columnname='vtiger_troubletickets:description:HelpDesk_Description:description:V'");

//Some fields in customview related tables are changed in latest version but not handled in migration
//Array format is -- oldvalue => newvalue - old values will be updated with new values
//customview related tables to be changed - cvcolumnlist, cvadvfilter
$cv_fields_list = Array(
			//campaigns module
			"vtiger_campaign:product_id:product_id:Campaigns_Product:V"=>"vtiger_products:productname:productname:Campaigns_Product:V",
			"vtiger_campaign:targetsize:targetsize:Campaigns_TargetSize:N"=>"vtiger_campaign:targetsize:targetsize:Campaigns_TargetSize:I",
			"vtiger_campaign:budgetcost:budgetcost:Campaigns_Budget_Cost:I"=>"vtiger_campaign:budgetcost:budgetcost:Campaigns_Budget_Cost:N",
			"vtiger_campaign:actualcost:actualcost:Campaigns_Actual_Cost:I"=>"vtiger_campaign:actualcost:actualcost:Campaigns_Actual_Cost:N",
			"vtiger_campaign:expectedrevenue:expectedrevenue:Campaigns_Expected_Revenue:I"=>"vtiger_campaign:expectedrevenue:expectedrevenue:Campaigns_Expected_Revenue:N",
			"vtiger_campaign:expectedsalescount:expectedsalescount:Campaigns_Expected_Sales_Count:N"=>"vtiger_campaign:expectedsalescount:expectedsalescount:Campaigns_Expected_Sales_Count:I",
			"vtiger_campaign:actualsalescount:actualsalescount:Campaigns_Actual_Sales_Count:N"=>"vtiger_campaign:actualsalescount:actualsalescount:Campaigns_Actual_Sales_Count:I",
			//calendar module
			"vtiger_recurringevents:recurringtype:recurringtype:Calendar_Recurrence:V"=>"vtiger_activity:recurringtype:recurringtype:Calendar_Recurrence:O",
			"vtiger_activity:time_start::Calendar_Start_Time:V"=>"vtiger_activity:time_start::Calendar_Start_Time:I",
			"vtiger_activity:time_end:time_end:Calendar_End_Time:V"=>"vtiger_activity:time_end:time_end:Calendar_End_Time:T",
			"activity:date_start:date_start:Activities_Start_Date_&_Time:DT"=>"vtiger_activity:date_start:date_start:Calendar_Start_Date_&_Time:DT",
			//Calendar Module
			"vtiger_activity:activitytype:activitytype:Calendar_Activity_Type:C"=>"vtiger_activity:activitytype:activitytype:Calendar_Activity_Type:V",
			//Campaign Module
			"vtiger_campaign:product_id:product_id:Campaigns_Product:I"=>"vtiger_products:productname:productname:Campaigns_Product:V",
			"vtiger_campaign:expectedresponsecount:expectedresponsecount:Campaigns_Expected_Response_Count:N"=>"vtiger_campaign:expectedresponsecount:expectedresponsecount:Campaigns_Expected_Response_Count:I",
			"vtiger_campaign:actualresponsecount:actualresponsecount:Campaigns_Actual_Response_Count:N"=>"vtiger_campaign:actualresponsecount:actualresponsecount:Campaigns_Actual_Response_Count:I",
			//Contacts Module
			"vtiger_contactsubdetails:birthday:birthday:Contacts_Birthdate:V"=>"vtiger_contactsubdetails:birthday:birthday:Contacts_Birthdate:D",
			//Leads Module
			"vtiger_leaddetails:noofemployees:noofemployees:Leads_No_Of_Employees:V"=>"vtiger_leaddetails:noofemployees:noofemployees:Leads_No_Of_Employees:I",
			//Potentials Module
			"vtiger_potential:campaignid:campaignid:Potentials_Campaign_Source:N"=>"vtiger_potential:campaignid:campaignid:Potentials_Campaign_Source:V",
			//FAQ Module
			"vtiger_faq:product_id:product_id:Faq_Product_Name:I"=>"vtiger_faq:product_id:product_id:Faq_Product_Name:V",
			//Products Module
			"vtiger_products:qtyinstock:qtyinstock:Products_Qty_In_Stock:I"=>"vtiger_products:qtyinstock:qtyinstock:Products_Qty_In_Stock:NN",
			"vtiger_products:handler:assigned_user_id:Products_Handler:I"=>"vtiger_products:handler:assigned_user_id:Products_Handler:V",
			//Vendors Module
			"vtiger_vendor:email:email:Vendors_Email:E"=>"vtiger_vendor:email:email:Vendors_Email:V",
			//Price Books Module
			"vtiger_pricebook:active:active:PriceBooks_Active:V"=>"vtiger_pricebook:active:active:PriceBooks_Active:C",
			//Quotes Module
			"vtiger_quotes:potentialid:potential_id:Quotes_Potential_Name:I"=>"vtiger_quotes:potentialid:potential_id:Quotes_Potential_Name:V",
			"vtiger_quotes:inventorymanager:assigned_user_id1:Quotes_Inventory_Manager:I"=>"vtiger_quotes:inventorymanager:assigned_user_id1:Quotes_Inventory_Manager:V",
		  );

foreach($cv_fields_list as $oldval => $newval)
{
	ExecuteQuery("update vtiger_cvcolumnlist set columnname='$newval' where columnname = '$oldval'");
	ExecuteQuery("update vtiger_cvadvfilter set columnname='$newval' where columnname = '$oldval'");
}

//Some fields in report related tables are changed in latest version but not handled in migration
//Report related tables to be changed - selectcolumn, relcriteria, reportsortcol
//Array format is -- oldvalue => newvalue - old values will be updated with new values
$report_fields_list = Array(
			//Calendar module
			"vtiger_recurringevents:recurringtype:Calendar_Recurrence:recurringtype:O"=>"vtiger_activity:recurringtype:Calendar_Recurrence:recurringtype:O",
			//Campaign module
			"vtiger_campaign:targetsize:Campaigns_TargetSize:targetsize:N"=>"vtiger_campaign:targetsize:Campaigns_TargetSize:targetsize:I",
			"vtiger_campaign:budgetcost:Campaigns_Budget_Cost:budgetcost:I"=>"vtiger_campaign:budgetcost:Campaigns_Budget_Cost:budgetcost:N",
			"vtiger_campaign:actualcost:Campaigns_Actual_Cost:actualcost:I"=>"vtiger_campaign:actualcost:Campaigns_Actual_Cost:actualcost:N",
			"vtiger_campaign:expectedrevenue:Campaigns_Expected_Revenue:expectedrevenue:I"=>"vtiger_campaign:expectedrevenue:Campaigns_Expected_Revenue:expectedrevenue:N",
			"vtiger_campaign:expectedsalescount:Campaigns_Expected_Sales_Count:expectedsalescount:N"=>"vtiger_campaign:expectedsalescount:Campaigns_Expected_Sales_Count:expectedsalescount:I",
			"vtiger_campaign:actualsalescount:Campaigns_Actual_Sales_Count:actualsalescount:N"=>"vtiger_campaign:actualsalescount:Campaigns_Actual_Sales_Count:actualsalescount:I",
			"vtiger_campaign:expectedresponsecount:Campaigns_Expected_Response_Count:expectedresponsecount:N"=>"vtiger_campaign:expectedresponsecount:Campaigns_Expected_Response_Count:expectedresponsecount:I",
			"vtiger_campaign:actualresponsecount:Campaigns_Actual_Response_Count:actualresponsecount:N"=>"vtiger_campaign:actualresponsecount:Campaigns_Actual_Response_Count:actualresponsecount:I",
			"vtiger_crmentityRelCalendar:setype:Calendar_Related_To:parent_id:I"=>"vtiger_crmentityRelCalendar:setype:Calendar_Related_To:parent_id:V",
			"vtiger_contactdetailsCalendar:lastname:Calendar_Contact_Name:contact_id:I"=>"vtiger_contactdetailsCalendar:lastname:Calendar_Contact_Name:contact_id:V",
			//Calendar Module
			"activity:date_start:Activities_Start_Date_&_Time:date_start:DT"=>"vtiger_activity:date_start:Calendar_Start_Date_&_Time:date_start:DT",
			"vtiger_activity:activitytype:Calendar_Activity_Type:activitytype:C"=>"vtiger_activity:activitytype:Calendar_Activity_Type:activitytype:V",
			//"vtiger_activity:status:Calendar_Status:taskstatus:V"=>"vtiger_activity:status:Calendar_Status:taskstatus:V",
			//Campaign Module
			"vtiger_campaign:product_id:Campaigns_Product:product_id:I"=>"vtiger_products:productname:Campaigns_Product:productname:V",
			"vtiger_campaign:expectedresponsecount:Campaigns_Expected_Response_Count:expectedresponsecount:N"=>"vtiger_campaign:expectedresponsecount:Campaigns_Expected_Response_Count:expectedresponsecount:I",
			"vtiger_campaign:actualresponsecount:Campaigns_Actual_Response_Count:actualresponsecount:N"=>"vtiger_campaign:actualresponsecount:Campaigns_Actual_Response_Count:actualresponsecount:I",
			//Contacts Module
			"vtiger_contactsubdetails:birthday:Contacts_Birthdate:birthday:V"=>"vtiger_contactsubdetails:birthday:Contacts_Birthdate:birthday:D",
			//Leads Module
			"vtiger_leaddetails:noofemployees:Leads_No_Of_Employees:noofemployees:V"=>"vtiger_leaddetails:noofemployees:Leads_No_Of_Employees:noofemployees:I",
			//Potentials Module
			"vtiger_potential:campaignid:Potentials_Campaign_Source:campaignid:N"=>"vtiger_potential:campaignid:Potentials_Campaign_Source:campaignid:V",
			//FAQ Module
			"vtiger_faq:product_id:Faq_Product_Name:product_id:I"=>"vtiger_faq:product_id:Faq_Product_Name:product_id:V",
			//Products Module
			"vtiger_products:qtyinstock:Products_Qty_In_Stock:qtyinstock:I"=>"vtiger_products:qtyinstock:Products_Qty_In_Stock:qtyinstock:NN",
			"vtiger_products:handler:Products_Handler:assigned_user_id:I"=>"vtiger_products:handler:Products_Handler:assigned_user_id:V",
			//Quotes Module
			"vtiger_quotes:potentialid:Quotes_Potential_Name:potential_id:I"=>"vtiger_quotes:potentialid:Quotes_Potential_Name:potential_id:V",
			"vtiger_quotes:inventorymanager:Quotes_Inventory_Manager:assigned_user_id1:I"=>"vtiger_quotes:inventorymanager:Quotes_Inventory_Manager:assigned_user_id1:V",
			   );

foreach($report_fields_list as $oldval => $newval)
{
	ExecuteQuery("update vtiger_selectcolumn set columnname='$newval' where columnname='$oldval'");
	ExecuteQuery("update vtiger_relcriteria set columnname='$newval' where columnname='$oldval'");
	ExecuteQuery("update vtiger_reportsortcol set columnname='$newval' where columnname='$oldval'");
}


//we have removed the Team field in quotes and added a new custom field for Team. So we can remove that field from reports (we have changed this field name in customview related tables in 503 - 504rc migration)
ExecuteQuery("delete from vtiger_selectcolumn where columnname='vtiger_quotes:team:Quotes_Team:team:V'");
ExecuteQuery("delete from vtiger_relcriteria where columnname='vtiger_quotes:team:Quotes_Team:team:V'");
ExecuteQuery("delete from vtiger_reportsortcol where columnname='vtiger_quotes:team:Quotes_Team:team:V'");

//Update the webmail password with encryption
update_webmail_password();
function update_webmail_password()
{
	global $adb,$migrationlog;
	$migrationlog->debug("\nInside update_webmail_password() function starts\n\n");
	require_once("modules/Users/Users.php");
	$res_set = $adb->query('select * from vtiger_mail_accounts');
	$user_obj = new Users();
	while($row = $adb->fetchByAssoc($res_set))
	{
		$adb->query("update vtiger_mail_accounts set mail_password = '".$user_obj->changepassword($row['mail_password'])."' where mail_username='".$row['mail_username']."'");
	}
	$migrationlog->debug("\nInside update_webmail_password() function ends\n");
}

//Modified to increase the length of the outgoinfg server(smtp) servername, username and password
ExecuteQuery("alter table vtiger_systems change  column server_username server_username varchar(100)");
ExecuteQuery("alter table vtiger_systems change  column server server varchar(100)");
ExecuteQuery("alter table vtiger_systems change  column server_password server_password varchar(100)");

//In our whole product, the picklist table columns and the corresponding picklists storage column in entity tables are changed to varchar(200)
$picklist_query_array = Array(
				"alter table vtiger_account modify account_type varchar(200) default NULL",
				"alter table vtiger_activity modify activitytype varchar(200) default NULL",
				"alter table vtiger_users modify activity_view varchar(200) default NULL",
				"alter table vtiger_campaign modify campaignstatus varchar(200) default NULL",
				"alter table vtiger_campaign modify campaigntype varchar(200) default NULL",
				"alter table vtiger_quotes modify carrier varchar(200) default NULL",
				"alter table vtiger_purchaseorder modify carrier varchar(200) default NULL",
				"alter table vtiger_salesorder modify carrier varchar(200) default NULL",
				"alter table vtiger_users modify date_format varchar(200) default NULL",
				"alter table vtiger_activity modify duration_minutes varchar(200) default NULL",
				"alter table vtiger_activity drop key activity_status_eventstatus_idx, add key activity_status_idx(status)",
				"alter table vtiger_activity modify eventstatus varchar(200) default NULL",
				"alter table vtiger_campaign modify expectedresponse varchar(200) default NULL",
				"alter table vtiger_faqcategories modify faqcategories varchar(200) default NULL",
				"alter table vtiger_faq modify category varchar(200) default NULL",
				"alter table vtiger_faqstatus modify faqstatus varchar(200) default NULL",
				"alter table vtiger_faq modify status varchar(200) default NULL",
				"alter table vtiger_vendor modify glacct varchar(200) default NULL",
				"alter table vtiger_account modify industry varchar(200) default NULL",
				"alter table vtiger_leaddetails modify industry varchar(200) default NULL",
				"alter table vtiger_leaddetails modify leadsource varchar(200) default NULL",
				"alter table vtiger_contactsubdetails modify leadsource varchar(200) default NULL",
				"alter table vtiger_potential modify leadsource varchar(200) default NULL",
				"alter table vtiger_users modify lead_view varchar(200) default NULL",
				"alter table vtiger_products modify manufacturer varchar(200) default NULL",
				"alter table vtiger_potential modify potentialtype varchar(200) default NULL",
				"alter table vtiger_products modify productcategory varchar(200) default NULL",
				"alter table vtiger_account modify rating varchar(200) default NULL",
				"alter table vtiger_leaddetails modify rating varchar(200) default NULL",
				"alter table vtiger_activity modify recurringtype varchar(200) default NULL",
				"alter table vtiger_potential modify sales_stage varchar(200) default NULL",
				"alter table vtiger_leaddetails modify salutation varchar(200) default NULL",
				"alter table vtiger_contactdetails modify salutation varchar(200) default NULL",
				"alter table vtiger_taskpriority modify taskpriority varchar(200) default NULL",
				"alter table vtiger_activity modify priority varchar(200) default NULL",
				"alter table vtiger_taskstatus modify taskstatus varchar(200) default NULL",
				"alter table vtiger_activity modify status varchar(200) default NULL",
				"alter table vtiger_ticketcategories modify ticketcategories varchar(200) default NULL",
				"alter table vtiger_troubletickets modify category varchar(200) default NULL",
				"alter table vtiger_ticketpriorities modify ticketpriorities varchar(200) default NULL",
				"alter table vtiger_troubletickets modify priority varchar(200) default NULL",
				"alter table vtiger_ticketseverities modify ticketseverities varchar(200) default NULL",
				"alter table vtiger_troubletickets modify severity varchar(200) default NULL",
				"alter table vtiger_ticketstatus modify ticketstatus varchar(200) default NULL",
				"alter table vtiger_troubletickets modify status varchar(200) default NULL",
			     );
foreach($picklist_query_array as $query)
{
	ExecuteQuery($query);
}

// Modified to change the comparison datatype from Integer to Varchar for Account name
ExecuteQuery("update vtiger_relcriteria set columnname='vtiger_accountContacts:accountname:Contacts_Account_Name:account_id:V' where columnname='vtiger_accountContacts:accountname:Contacts_Account_Name:account_id:I'");
ExecuteQuery("update vtiger_selectcolumn set columnname='vtiger_accountContacts:accountname:Contacts_Account_Name:account_id:V' where columnname='vtiger_accountContacts:accountname:Contacts_Account_Name:account_id:I'");

// Modified to change the typeofdata for hour_format, start_hour and end_hour to 'V~O' instead of 'I~O'
ExecuteQuery("update vtiger_field set typeofdata = 'V~O' where tablename='vtiger_users' and fieldname in ('hour_format','start_hour','end_hour')");

//Since we don't have field level access for Users and RSS modules we have to delete if there is any entry for these modules in vtiger_profile2field table
$adb->query("delete from vtiger_profile2field where tabid=29");
$adb->query("delete from vtiger_profile2field where tabid=24");

// Modified  the typeofdata for all module email field & custom email field in Custom View & Reports.
typeOfDataChanges();
function typeOfDataChanges()
{
    global $adb,$migrationlog;
    $migrationlog->debug("\nInside typeOfDataChanges() function Starts\n\n");
    
    $field_table_sql="select columnname,fieldname from vtiger_field where uitype=13";
    $result=$adb->query($field_table_sql);        
    $num_rows = $adb->num_rows($result);
    for($k=0; $k < $num_rows; $k++)
    {
	$columnname=$adb->query_result($result,$k,'columnname');
	$fieldname=$adb->query_result($result,$k,'fieldname');
	$tablename_array = array('vtiger_cvcolumnlist','vtiger_cvadvfilter','vtiger_selectcolumn','vtiger_relcriteria','vtiger_reportsortcol');
	foreach($tablename_array as $tablename)
	{
	    $custom_sql="select columnname from  ".$tablename."  where columnname like '%cf%' or columnname like '%email%'";
	    $custom_result = $adb->query($custom_sql);
	    $num_rows2 = $adb->num_rows($custom_result);
 	    for($l=0; $l < $num_rows2; $l++)
 	    {	
		$table_columnname=$adb->query_result($custom_result,$l,'columnname');
		$values = explode(':',$table_columnname);
		if($columnname == $values[1] && $fieldname == $values[2])
		{
			ExecuteQuery("update ".$tablename." set columnname='".$values[0].":".$values[1].":".$values[2].":".$values[3].":E'   where columnname='".$values[0].":".$values[1].":".$values[2].":".$values[3].":V'");
		}
		if($columnname == $values[1] && $fieldname == $values[3])
		{
			ExecuteQuery("update ".$tablename." set columnname='".$values[0].":".$values[1].":".$values[2].":".$values[3].":E'   where columnname='".$values[0].":".$values[1].":".$values[2].":".$values[3].":V'");
		}
	    }
	}
    }
    $migrationlog->debug("\nInside typeOfDataChanges() function Ends\n\n");
}
//Added to remove the unwanted \n characters from inventory notification schedulers
$result=$adb->query("select notificationid,notificationbody from vtiger_inventorynotification");

for($i=0;$i<$adb->num_rows($result);$i++)
{
	$body=decode_html($adb->query_result($result,$i,'notificationbody'));
	$body=str_replace('\n','', $body);
	$notificationid=$adb->query_result($result,$i,'notificationid');
	$adb->pquery("update vtiger_inventorynotification set notificationbody=? where notificationid=?", array($body, $notificationid));
}
//In 5.0.4, support start and end date notification scheduler should be defaultly active. If it is inactive previouly then we have to change them as active
ExecuteQuery("update vtiger_notificationscheduler set active=1 where schedulednotificationid in (5,6)");

//Query added to modify the date_format field length in vtiger_users table
ExecuteQuery("alter table vtiger_users modify date_format varchar(200) default NULL");
// Updated the sequence number of taskstatus for the ticket #5027
ExecuteQuery("update vtiger_field set sequence = 8 where columnname = 'status' and tablename = 'vtiger_activity' and fieldname = 'taskstatus' and uitype = 111");

$arr=$adb->getColumnNames("vtiger_users");
if(!in_array("internal_mailer", $arr))
{
	$adb->pquery("alter table vtiger_users add column internal_mailer int(3) NOT NULL default '1'", array());
}

global $dbname;
include("modules/Migration/HTMLtoUTF8Conversion.php");

$migrationlog->debug("\n\nDB Changes from 5.0.4rc to 5.0.4 -------- Ends \n\n");

?>