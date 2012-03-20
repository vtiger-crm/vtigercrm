<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  Defines the English language pack for the Account module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


$mod_strings = Array(
'LBL_IMPORT_MODULE_NO_DIRECTORY'=>'The directory ',
'LBL_IMPORT_MODULE_NO_DIRECTORY_END'=>' does not exist or is not writable',
'LBL_IMPORT_MODULE_ERROR_NO_UPLOAD'=>'File was not uploaded successfully, try again',
'LBL_IMPORT_MODULE_ERROR_LARGE_FILE'=>'File is too large. Max:',
'LBL_IMPORT_MODULE_ERROR_LARGE_FILE_END'=>'Bytes. Change $upload_maxsize in config.php',
'LBL_MODULE_NAME'=>'Import',
'LBL_TRY_AGAIN'=>'Try Again',
'LBL_ERROR'=>'Error:',
'ERR_MULTIPLE'=>'Multiple columns have been defined with the same field name.',
'ERR_MISSING_REQUIRED_FIELDS'=>'Missing required fields:',
'ERR_SELECT_FULL_NAME'=>'You cannot select Full Name when First Name and Last Name are selected.',
'ERR_SELECT_FILE'=>'Select a file to upload.',
'LBL_SELECT_FILE'=>'Select file:',
'LBL_CUSTOM'=>'Custom',
'LBL_DONT_MAP'=>'-- Do not map this field --',
'LBL_STEP_1_TITLE'=>'Step 1 of 4: Select Data Source',
'LBL_WHAT_IS'=>'Please select a data source from the following:',
'LBL_MICROSOFT_OUTLOOK'=>'Microsoft Outlook',
'LBL_ACT'=>'Act!',
'LBL_SALESFORCE'=>'Salesforce.com',
'LBL_MY_SAVED'=>'My Saved Sources:',
'LBL_PUBLISH'=>'publish',
'LBL_DELETE'=>'delete',
'LBL_PUBLISHED_SOURCES'=>'Published Sources:',
'LBL_UNPUBLISH'=>'un-publish',
'LBL_NEXT'=>'Next',
'LBL_BACK'=>'Back',
'LBL_STEP_2_TITLE'=>'Step 2 of 4: Upload Export File',
'LBL_HAS_HEADER'=>'Has Header',

'LBL_NUM_1'=>'1.',
'LBL_NUM_2'=>'2.',
'LBL_NUM_3'=>'3.',
'LBL_NUM_4'=>'4.',
'LBL_NUM_5'=>'5.',
'LBL_NUM_6'=>'6.',
'LBL_NUM_7'=>'7.',
'LBL_NUM_8'=>'8.',
'LBL_NUM_9'=>'9.',
'LBL_NUM_10'=>'10.',
'LBL_NUM_11'=>'11.',
'LBL_NUM_12'=>'12.',
'LBL_NOW_CHOOSE'=>'Now choose that file to import:',
'LBL_IMPORT_OUTLOOK_TITLE'=>'Microsoft Outlook 98 and 2000 can export data in the <b>Comma Separated Values</b> format which can be used to import data into the system. To export your data from Outlook, follow the steps below:',
'LBL_OUTLOOK_NUM_1'=>'Start <b>Outlook</b>',
'LBL_OUTLOOK_NUM_2'=>'Select the <b>File</b> menu, then the <b>Import and Export ...</b> menu option',
'LBL_OUTLOOK_NUM_3'=>'Choose <b>Export to a file</b> and click Next',
'LBL_OUTLOOK_NUM_4'=>'Choose <b>Comma Separated Values (Windows)</b> and click <b>Next</b>.<br>  Note: You may be prompted to install the export component',
'LBL_OUTLOOK_NUM_5'=>'Select the <b>Contacts</b> folder and click <b>Next</b>. You can select different contacts folders if your contacts are stored in multiple folders',
'LBL_OUTLOOK_NUM_6'=>'Choose a filename and click <b>Next</b>',
'LBL_OUTLOOK_NUM_7'=>'Click <b>Finish</b>',
'LBL_IMPORT_ACT_TITLE'=>'Act! can export data in the <b>Comma Separated Values</b> format which can be used to import data into the system. To export your data from Act!, follow the steps below:',
'LBL_ACT_NUM_1'=>'Launch <b>ACT!</b>',
'LBL_ACT_NUM_2'=>'Select the <b>File</b> menu, the <b>Data Exchange</b> menu option, then the <b>Export...</b> menu option',
'LBL_ACT_NUM_3'=>'Select the file type <b>Text-Delimited</b>',
'LBL_ACT_NUM_4'=>'Choose a filename and location for the exported data and click <b>Next</b>',
'LBL_ACT_NUM_5'=>'Select <b>Contacts records only</b>',
'LBL_ACT_NUM_6'=>'Click the <b>Options...</b> button',
'LBL_ACT_NUM_7'=>'Select <b>Comma</b> as the field separator character',
'LBL_ACT_NUM_8'=>'Check the <b>Yes, export field names</b> checkbox and click <b>OK</b>',
'LBL_ACT_NUM_9'=>'Click <b>Next</b>',
'LBL_ACT_NUM_10'=>'Select <b>All Records</b> and then Click <b>Finish</b>',

'LBL_IMPORT_SF_TITLE'=>'Salesforce.com can export data in the <b>Comma Separated Values</b> format which can be used to import data into the system. To export your data from Salesforce.com, follow the steps below:',
'LBL_SF_NUM_1'=>'Open your browser, go to http://www.salesforce.com, and login with your email address and password',
'LBL_SF_NUM_2'=>'Click on the <b>Reports</b> tab on the top menu',
'LBL_SF_NUM_3'=>'To export Accounts:</b> Click on the <b>Active Accounts</b> link<br><b>To export Contacts:</b> Click on the <b>Mailing List</b> link',
'LBL_SF_NUM_4'=>'On <b>Step 1: Select your report type</b>, select <b>Tabular Report</b>click <b>Next</b>',
'LBL_SF_NUM_5'=>'On <b>Step 2: Select the report columns</b>, choose the columns you want to export and click <b>Next</b>',
'LBL_SF_NUM_6'=>'On <b>Step 3: Select the information to summarize</b>, just click <b>Next</b>',
'LBL_SF_NUM_7'=>'On <b>Step 4: Order the report columns</b>, just click <b>Next</b>',
'LBL_SF_NUM_8'=>'On <b>Step 5: Select your report criteria</b>, under <b>Start Date</b>, choose a date far enough in the past to include all your Accounts. You can also export a subset of Accounts using more advanced criteria. When you are done, click <b>Run Report</b>',
'LBL_SF_NUM_9'=>'A report will be generated, and the page should display <b>Report Generation Status: Complete.</b> Now click <b>Export to Excel</b>',
'LBL_SF_NUM_10'=>'On <b>Export Report:</b>, for <b>Export File Format:</b>, choose <b>Comma Delimited .csv</b>. Click <b>Export</b>.',
'LBL_SF_NUM_11'=>'A dialog will pop up for you to save the export file to your computer.',
'LBL_IMPORT_CUSTOM_TITLE'=>'Many applications will allow you to export data into a <b>Comma Delimited text file (.csv)</b>. Generally most applications follow these general steps:',
'LBL_CUSTOM_NUM_1'=>'Launch the application and Open the data file',
'LBL_CUSTOM_NUM_2'=>'Select the <b>Save As...</b> or <b>Export...</b> menu option',
'LBL_CUSTOM_NUM_3'=>'Save the file in a <b>CSV</b> or <b>Comma Separated Values</b> format',

'LBL_STEP_3_TITLE'=>'Step 3 of 4: Confirm Fields and Import',
'LBL_STEP_1'=>'Step 1 of 3 : ',
'LBL_STEP_1_TITLE'=>'Select the .CSV File',
'LBL_STEP_1_TEXT'=> ' vtiger CRM supports importing records from .csv (<b> Comma Separated Values</b> ) files. To start import, browse to locate the .CSV file and click on the Next button to Continue.',

'LBL_SELECT_FIELDS_TO_MAP'=>'In the list below, select the fields in your import file that should be imported into each field in the system. When you are finished, click <b>Import Now</b>',

'LBL_DATABASE_FIELD'=>'Database Field',
'LBL_HEADER_ROW'=>'Header Row',
'LBL_ROW'=>'Row',
'LBL_SAVE_AS_CUSTOM'=>'Save as Custom Mapping :',
'LBL_CONTACTS_NOTE_1'=>'Either Last Name or Full Name must be mapped.',
'LBL_CONTACTS_NOTE_2'=>'If Full Name is mapped, then First Name and Last Name are ignored.',
'LBL_CONTACTS_NOTE_3'=>'If Full Name is mapped, then the data in Full Name will be split into First Name and Last Name when inserted into the database.',
'LBL_CONTACTS_NOTE_4'=>'Fields ending in Address Street 2 and Address Street 3 are concatenated together with the main Address Street Field when inserted into the database.',
'LBL_ACCOUNTS_NOTE_1'=>'Account Name must be mapped.',
'LBL_ACCOUNTS_NOTE_2'=>'Fields ending in Address Street 2 and Address Street 3 are concatenated together with the main Address Street Field when inserted into the database.',
'LBL_POTENTIALS_NOTE_1'=>'Potential Name, Account Name, Date Closed, and Sales Stage are required fields.',
'LBL_OPPORTUNITIES_NOTE_1'=>'Opportunity Name, Account Name, Date Closed, and Sales Stage are required fields.',
'LBL_LEADS_NOTE_1'=>'Last Name must be mapped.',
'LBL_LEADS_NOTE_2'=>'Company Name must be mapped.',
'LBL_IMPORT_NOW'=>'Import Now',
'LBL_'=>'',
'LBL_CANNOT_OPEN'=>'Cannot open the imported file for reading',
'LBL_NOT_SAME_NUMBER'=>'There were not the same number of fields per line in your file',
'LBL_NO_LINES'=>'There were no lines in your import file',
'LBL_FILE_ALREADY_BEEN_OR'=>'The import file has already been processed or does not exist',
'LBL_SUCCESS'=>'Success! ',
'LBL_SUCCESSFULLY'=>'Succesfully Imported',
'LBL_LAST_IMPORT_UNDONE'=>'Your Last Import Was Undone',
'LBL_NO_IMPORT_TO_UNDO'=>'There was no import to undo.',
'LBL_FAIL'=>'Fail:',
'LBL_RECORDS_SKIPPED'=>'records skipped because they were missing one or more required fields',
'LBL_IDS_EXISTED_OR_LONGER'=>'records skipped because the id\'s either existed or where longer than 36 characters',
'LBL_RESULTS'=>'Results',
'LBL_IMPORT_MORE'=>'Import More',
'LBL_FINISHED'=>'Finished',
'LBL_UNDO_LAST_IMPORT'=>'Undo Last Import',

'LBL_SUCCESS_1' => 'No. of Records Successfully Imported : ',
'LBL_SKIPPED_1' => 'No. of Records Skipped as they were missing one or more required fields : ',

//Added for patch2 - Products Import Notes
'LBL_PRODUCTS_NOTE_1'=>'Product Name must be mapped',
'LBL_PRODUCTS_NOTE_2'=>'Before import please check whether a single column has been mapped twice',

//Added for version 5
'LBL_FILE_LOCATION'=>'File Location :',
'LBL_STEP_2_3'=>'Step 2 of 3 :',
'LBL_LIST_MAPPING'=>'List & Mapping',
'LBL_STEP_2_MSG'=>'The following tables shows the imported',
'LBL_STEP_2_MSG1'=>'and other details.',
'LBL_STEP_2_TXT'=>'To map the fields, select the corresponding in combo boxes for each',
'LBL_USE_SAVED_MAPPING'=>'Use Saved Mapping :',
'LBL_MAPPING'=>'Mapping',
'LBL_HEADERS'=>'Headers :',
'LBL_ERROR_MULTIPLE'=>'Same fields may be mapped twice. Please check the mapped fields.',
'LBL_STEP_3_3'=>'Step 3 of 3 : ',
'LBL_MAPPING_RESULTS'=>'Mapping Results ',
'LBL_LAST_IMPORTED'=>'Last Imported',
//Added for sript alerts
'PLEASE_CHECK_MAPPING' => "' is mapped more than once. Please check the mapping.",
'MAP_MANDATORY_FIELD' => 'Please map the mandatory field "',
'ENTER_SAVEMAP_NAME' => 'Please Enter Save Map Name',

//Added for 5.0.3
'to'=>'to',
'of'=>'of',
'are_imported_succesfully'=>'are imported successfully',

// Added after 5.0.4 GA

//added for duplicate handling 
'LBL_LAST_IMPORT'=>'Last Imported',
'Select_Criteria_For_Duplicate' => 'Select Criteria For Duplicate Records Handling',
'Manual_Merging' => 'Manual Merging',
'Auto_Merging' => 'Auto Merging',
'Ignore_Duplicate' => 'Ignore the duplicate import records',
'Overwrite_Duplicate' => 'Overwrite the duplicate records',
'Duplicate_Records_Skipped_Info' => 'No. of Records Skipped as they were duplicates : ',
'Duplicate_Records_Overwrite_Info' => 'No. of Records Overwritten as they were duplicates : ',
'LBL_STEP_4_4' => 'Step 4 of 4 : ',
'LBL_STEP_3_4'=>'Step 3 of 4 :',
'LBL_STEP_2_4'=>'Step 2 of 4 :',
'LBL_STEP_1_4'=>'Step 1 of 4 : ',

'LBL_DELIMITER' => 'Delimiter:',
'LBL_FORMAT' => 'Format:',
);

/*$mod_list_strings = Array(
	"id"=>"Contact ID"
	,"first_name"=>"First Name"
	,"last_name"=>"Last Name"
	,"salutation"=>"Salutation"
	,"lead_source"=>"Lead Source"
	,"birthdate"=>"Lead Source"
	,"do_not_call"=>"Do Not Call"
	,"email_opt_out"=>"Email Opt Out"
	,"primary_address_street_2"=>"Primary Address Street 2"
	,"primary_address_street_3"=>"Primary Address Street 3"
	,"alt_address_street_2"=>"Other Address Street 2"
	,"alt_address_street_3"=>"Other Address Street 3"
	,"full_name"=>"Full Name"
	,"account_name"=>"Account Name"
	,"account_id"=>"Account ID"
	,"title"=>"Title"
	,"department"=>"Department"
	,"birthdate"=>"Birthdate"
	,"do_not_call"=>"Do Not Call"
	,"phone_home"=>"Phone (Home)"
	,"phone_mobile"=>"Phone (Mobile)"
	,"phone_work"=>"Phone (Work)"
	,"phone_other"=>"Phone (Other)"
	,"phone_fax"=>"Fax"
	,"email1"=>"Email"
	,"email2"=>"Email (Other)"
	,"yahoo_id"=>"Yahoo! ID"
	,"assistant"=>"Assistant"
	,"assistant_phone"=>"Assistant Phone"
	,"primary_address_street"=>"Primary Address Street"
	,"primary_address_city"=>"Primary Address City"
	,"primary_address_state"=>"Primary Address State"
	,"primary_address_postalcode"=>"Primary Address Postalcode"
	,"primary_address_country"=>"Primary Address Country"
	,"alt_address_street"=>"Other Address Street"
	,"alt_address_city"=>"Other Address City"
	,"alt_address_state"=>"Other Address State"
	,"alt_address_postalcode"=>"Other Address Postalcode"
	,"alt_address_country"=>"Other Address Country"
	,"description"=>"Description"

	),*/
$mod_list_strings = Array(
'contacts_import_fields' => Array(
	//"id"=>"Contact ID"
	"firstname"=>"First Name"
	,"lastname"=>"Last Name"
	,"salutationtype"=>"Salutation"
	,"leadsource"=>"Lead Source"
	,"birthday"=>"Birthdate"
	,"donotcall"=>"Do Not Call"
	,"emailoptout"=>"Email Opt Out"
	//,"primary_address_street_2"=>"Primary Address Street 2"
	//,"primary_address_street_3"=>"Primary Address Street 3"
	//,"alt_address_street_2"=>"Other Address Street 2"
	//,"alt_address_street_3"=>"Other Address Street 3"
	//,"full_name"=>"Full Name"
	//,"account_name"=>"Account Name"
	,"account_id"=>"Account Name"
	,"title"=>"Title"
	,"department"=>"Department"
	//,"birthdate"=>"Birthdate"
	//,"do_not_call"=>"Do Not Call"
	,"homephone"=>"Phone (Home)"
	,"mobile"=>"Phone (Mobile)"
	,"phone"=>"Phone (Work)"
	,"otherphone"=>"Phone (Other)"
	,"fax"=>"Fax"
	,"email"=>"Email"
	,"otheremail"=>"Email (Other)"
	,"yahooid"=>"Yahoo! ID"
	,"assistant"=>"Assistant"
	,"assistantphone"=>"Assistant Phone"
	,"mailingstreet"=>"Mailing Address Street"
	,"mailingpobox"=>"Mailing Address PO Box"
	,"mailingcity"=>"Mailing Address City"
	,"mailingstate"=>"Mailing Address State"
	,"mailingzip"=>"Mailing Address Postalcode"
	,"mailingcountry"=>"Mailing Address Country"
	,"otherstreet"=>"Other Address Street"
	,"otherpobox"=>"Other Address PO Box"
	,"othercity"=>"Other Address City"
	,"otherstate"=>"Other Address State"
	,"otherzip"=>"Other Address Postalcode"
	,"othercountry"=>"Other Address Country"
	,"description"=>"Description"
	,"assigned_user_id"=>"Assigned To"
	),

'accounts_import_fields' => Array(
	//"id"=>"Account ID",
	"accountname"=>"Account Name",
	"website"=>"Website",
	"industry"=>"Industry",
	"accounttype"=>"Type",
	"tickersymbol"=>"Ticker Symbol",
	"parent_name"=>"Member of",
	"employees"=>"Employees",
	"ownership"=>"Ownership",
	"phone"=>"Phone",
	"fax"=>"Fax",
	"otherphone"=>"Other Phone",
	"email1"=>"Email",
	"email2"=>"Other Email",
	"rating"=>"Rating",
	"siccode"=>"SIC Code",
	"annual_revenue"=>"Annual Revenue",
	"bill_street"=>"Billing Address Street",
	//"billing_address_street_2"=>"Billing Address Street 2",
	//"billing_address_street_3"=>"Billing Address Street 3",
	//"billing_address_street_4"=>"Billing Address Street 4",
	"bill_pobox"=>"Billing Address PO Box",
	"bill_city"=>"Billing Address City",
	"bill_state"=>"Billing Address State",
	"bill_code"=>"Billing Address Postalcode",
	"bill_country"=>"Billing Address Country",
	"ship_street"=>"Shipping Address Street",
	//"shipping_address_street_2"=>"Shipping Address Street 2",
	//"shipping_address_street_3"=>"Shipping Address Street 3",
	//"shipping_address_street_4"=>"Shipping Address Street 4",
	"ship_pobox"=>"Shipping Address PO Box",
	"ship_city"=>"Shipping Address City",
	"ship_state"=>"Shipping Address State",
	"ship_code"=>"Shipping Address Postalcode",
	"ship_country"=>"Shipping Address Country",
	"description"=>"Description",
	"assigned_user_id"=>"Assigned To"
	),

'potentials_import_fields' => Array(
		//"id"=>"Account ID"
                 "potentialname"=>"Potential Name"
                , "account_id"=>"Account Name"
                , "opportunity_type"=>"Potential Type"
                , "leadsource"=>"Lead Source"
                , "amount"=>"Amount"
                , "closingdate"=>"Closing Date"
                , "nextstep"=>"Next Step"
                , "sales_stage"=>"Sales Stage"
                , "probability"=>"Probability"
                , "description"=>"Description"
		,"assigned_user_id"=>"Assigned To"
	),


'leads_import_fields' => Array(
		"salutationtype"=>"Salutation",
		"firstname"=>"First Name",
		"phone"=>"Phone",
		"lastname"=>"Last Name",
		"mobile"=>"Mobile",
		"company"=>"Company",
		"fax"=>"Fax",
		"designation"=>"Designation",
		"email"=>"Email",
		"leadsource"=>"Lead Source",
		"website"=>"Website",
		"industry"=>"Industry",
		"leadstatus"=>"Lead Status",
		"annualrevenue"=>"Annual Revenue",
		"rating"=>"Rating",
		"noofemployees"=>"No Of Employees",
		"assigned_user_id"=>"Assigned To",
		"yahooid"=>"Yahoo Id",		
		"lane"=>"Street",
		"pobox"=>"PO Box",
		"code"=>"Postal Code",
		"city"=>"City",
		"country"=>"Country",
		"state"=>"State",
		"description"=>"Description"
		,"assigned_user_id"=>"Assigned To"
    ),
 
 'products_import_fields' => Array(
 	'productname'=>'Product Name',
 	'productcode'=>'Product Code',
 	'productcategory'=>'Product Category',
 	'manufacturer'=>'Manufacturer',
 	'product_description'=>'Product Description',
 	'qty_per_unit'=>'Quantity Per/Unit',
 	'unit_price'=>'Unit Price',
 	'weight'=>'Weight',
 	'pack_size'=>'Pack Size',
 	'start_date'=>'Start Date',
 	'expiry_date'=>'Expiration Date',
 	'cost_factor'=>'Cost Factor',
 	'commissionmethod'=>'Commission Method',
 	'discontinued'=>'Discontinued',
 	'commissionrate'=>'Commission Rate',
	'sales_start_date'=>'Sales Start Date',
	'sales_end_date'=>'Sales End Date',
	'usageunit'=>'Usage Unit',
	'serialno'=>'Serial No',
	'currency'=>'currency',
	'reorderlevel'=>'Reorder Level',
	'website'=>'Web Site',
	'taxclass'=>'Tax Class',
	'mfr_part_no'=>'Manufacture Part No',
	'vendor_part_no'=>'Vendor Part No',
	'qtyinstock'=>'Quantity in Stock',
	'productsheet'=>'Product Sheet',
	'qtyindemand'=>'Quantity in Demand',
	'glacct'=>'GL Account',
	'assigned_user_id'=>'Assigned To'
	 ),
//Pavani...adding list of import fields for helpdesk and vendors
'helpdesk_import_fields' => Array(
        "ticketid"=>"Ticket Id",
        "priority"=>"Priority",
        "severity"=>"Severity",
        "status"=>"Status",
        "category"=>"Category",
        "title"=>"Title",
        "description"=>"Description",
        "solution"=>"Solution"
        ),

'vendors_import_fields' => Array(
        "vendorid"=>"Vender Id",
        "vendorname"=>"Vendor Name",
        "phone"=>"Phone",
        "email"=>"Email",
        "website"=>"Website",
        "category"=>"Category",
        "street"=>"Street",
        "city"=>"City",
        "state"=>"State",
        "pobox"=>"Post Box",
        "postalcode"=>"Postal Code",
        "country"=>"Country",
        "description"=>"Description"
        )
//Pavani...end list
);

?>
