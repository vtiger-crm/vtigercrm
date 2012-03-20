<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

//5.0.4 to 5.1.0 RC database changes

//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.0.4 to 5.1.0 RC -------- Starts \n\n");

require_once('include/events/include.inc');
$em = new VTEventsManager($adb);
/* For the event api */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_eventhandlers (eventhandler_id int, event_name varchar(100), handler_path varchar(400), handler_class varchar(100), cond text, is_active boolean, primary key(eventhandler_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_eventhandler_module(eventhandler_module_id int, module_name VARCHAR(100), handler_class VARCHAR(100), PRIMARY KEY(eventhandler_module_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

/* Added new column actions to vtiger_relatedlists which tracks the type of actions allowed for that related list */
if(!in_array('actions', $adb->getColumnNames('vtiger_relatedlists'))) {
	ExecuteQuery("alter table vtiger_relatedlists add column actions VARCHAR(50) default ''");
}

$accounts_tab_id = getTabid('Accounts');
$contacts_tab_id = getTabid('Contacts');
$notes_tab_id = getTabid('Documents');
$products_tab_id = getTabid('Products');
$leads_tab_id = getTabid('Leads');
$campaigns_tab_id = getTabid('Campaigns');
$potentials_tab_id = getTabid('Potentials');
$emails_tab_id = getTabid('Emails');
$calendar_tab_id = getTabid('Calendar');
$helpdesk_tab_id = getTabid('HelpDesk');
$quotes_tab_id = getTabid('Quotes');
$so_tab_id = getTabid('SalesOrder');
$po_tab_id = getTabid('PurchaseOrder');
$invoice_tab_id = getTabid('Invoice');
$pb_tab_id = getTabid('PriceBooks');
$vendors_tab_id = getTabid('Vendors');

// Accounts related lists
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add' WHERE tabid=$accounts_tab_id");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$accounts_tab_id AND related_tabid=$notes_tab_id");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='select' WHERE tabid=$accounts_tab_id AND related_tabid=$products_tab_id");

// Leads related lists
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$leads_tab_idAND related_tabid=$notes_tab_id");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add' WHERE tabid=$leads_tab_id AND related_tabid IN ($calendar_tab_id,$emails_tab_id)");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='select' WHERE tabid=$leads_tab_id AND related_tabid IN ($products_tab_id,$campaigns_tab_id)");

// Contacts related list
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add' WHERE tabid=$contacts_tab_id");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$contacts_tab_id AND related_tabid=$notes_tab_id");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='select' WHERE tabid=$contacts_tab_id AND related_tabid IN ($products_tab_id,$campaigns_tab_id)");

// Potentials related list
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add' WHERE tabid=$potentials_tab_id AND related_tabid IN ($calendar_tab_id,$quotes_tab_id,$so_tab_id)");;
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$potentials_tab_id AND related_tabid=$notes_tab_id");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='select' WHERE tabid=$potentials_tab_id AND related_tabid IN ($products_tab_id,$contacts_tab_id)");

// Products related list
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add' WHERE tabid=$products_tab_id AND related_tabid IN ($helpdesk_tab_id,$quotes_tab_id,$so_tab_id,$po_tab_id,$invoice_tab_id,$pb_tab_id)");;
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$products_tab_id AND related_tabid=$notes_tab_id");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='select' WHERE tabid=$products_tab_id AND related_tabid IN ($accounts_tab_id,$contacts_tab_id,$leads_tab_id,$potentials_tab_id)");

// Emails related lists
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='select,bulkmail' WHERE tabid=$emails_tab_id AND related_tabid=$contacts_tab_id");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$emails_tab_id AND related_tabid=$notes_tab_id");

// Trouble Tickets related lists
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$helpdesk_tab_id AND related_tabid IN ($notes_tab_id,$calendar_tab_id)");

// Products related lists
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='select' WHERE tabid=$pb_tab_id AND related_tabid IN ($products_tab_id)");

// Vendors related lists
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add' WHERE tabid=$vendors_tab_id AND related_tabid IN ($emails_tab_id,$po_tab_id)");;
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$vendors_tab_id AND related_tabid=$products_tab_id");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='select' WHERE tabid=$vendors_tab_id AND related_tabid IN ($contacts_tab_id)");

// Quotes related lists
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$quotes_tab_id AND related_tabid IN ($notes_tab_id)");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add' WHERE tabid=$quotes_tab_id AND related_tabid IN ($calendar_tab_id)");

// PO related lists
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$po_tab_id AND related_tabid IN ($notes_tab_id)");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add' WHERE tabid=$po_tab_id AND related_tabid IN ($calendar_tab_id)");

// SO related lists
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$so_tab_id AND related_tabid IN ($notes_tab_id)");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add' WHERE tabid=$so_tab_id AND related_tabid IN ($calendar_tab_id)");

// Invoices related lists
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$invoice_tab_id AND related_tabid IN ($notes_tab_id)");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add' WHERE tabid=$invoice_tab_id AND related_tabid IN ($calendar_tab_id)");

// Campaigns related lists
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select' WHERE tabid=$campaigns_tab_id AND related_tabid IN ($contacts_tab_id,$leads_tab_id)");
ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add' WHERE tabid=$campaigns_tab_id AND related_tabid IN ($calendar_tab_id,$potentials_tab_id)");

require_once("modules/com_vtiger_workflow/include.inc");
require_once("modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc");
require_once("modules/com_vtiger_workflow/VTEntityMethodManager.inc");
$emm = new VTEntityMethodManager($adb);

/* Update the profileid, block id in sequence table, to the current highest value of block id used. */
$tmp = $adb->getUniqueId('vtiger_blocks');
$max_block_id_query = $adb->query("SELECT MAX(blockid) AS max_blockid FROM vtiger_blocks");
if($adb->num_rows($max_block_id_query)>0){
	$max_block_id = $adb->query_result($max_block_id_query,0,"max_blockid");
	ExecuteQuery("UPDATE vtiger_blocks_seq SET id=".($max_block_id));
}

$tmp = $adb->getUniqueId('vtiger_profile');
$max_profile_id_query = $adb->query("SELECT MAX(profileid) AS max_profileid FROM vtiger_profile");
if($adb->num_rows($max_profile_id_query)>0){
	$max_profile_id = $adb->query_result($max_profile_id_query,0,"max_profileid");
	ExecuteQuery("UPDATE vtiger_profile_seq SET id=".($max_profile_id));
}

/* Migration queries to cleanup ui type 15, 16, 111 - 
 * 15 for Standard picklist types,
 * 16 for non-standard picklist types which do not support Role-based picklist */
ExecuteQuery("update vtiger_field set uitype = '15' where uitype='16'");
ExecuteQuery("update vtiger_field set uitype = '15', typeofdata='V~M' where uitype='111'");

ExecuteQuery("update vtiger_field set uitype=16 where fieldname in " .
		"('visibility','duration_minutes','recurringtype','hdnTaxType','recurring_frequency','activity_view','lead_view','date_format','reminder_interval')" .
		" and uitype = '15'");

/* Function to add Field Security for newly added fields */
function addFieldSecurity($tabid, $fieldid, $allow_merge=false) {
	global $adb;
	ExecuteQuery("INSERT INTO vtiger_def_org_field (tabid, fieldid, visible, readonly) VALUES ($tabid, $fieldid, 0, 1)");

	$profile_result = $adb->query("select distinct(profileid) as profileid from vtiger_profile");
	$num_profiles = $adb->num_rows($profile_result);
	for($j=0; $j<$num_profiles; $j++) {
		$profileid = $adb->query_result($profile_result,$j,'profileid');
		ExecuteQuery("INSERT INTO vtiger_profile2field (profileid, tabid, fieldid, visible, readonly) VALUES($profileid, $tabid, $fieldid, 0, 1)");
	}
}

/* Add Total column in default customview of Purchase Order */
$res = $adb->query("select cvid from vtiger_customview where viewname='All' and entitytype='PurchaseOrder'");

if($adb->num_rows($res)>0){
	$po_cvid = $adb->query_result($res, 0, 'cvid');
	$adb->query("update vtiger_cvcolumnlist set columnindex = 5 where columnindex = 4 and cvid = $po_cvid");
	$adb->query("insert into vtiger_cvcolumnlist values ($po_cvid, 4, 'vtiger_purchaseorder:total:hdnGrandTotal:PurchaseOrder_Total:V')");
}
                        


/* To Provide an option to Create Product from Quick Create */
ExecuteQuery("UPDATE vtiger_field SET quickcreate = 0 WHERE tablename='vtiger_products' and columnname='productname'");
ExecuteQuery("UPDATE vtiger_field SET quickcreate = 0 WHERE tablename='vtiger_products' and columnname='discontinued'");
ExecuteQuery("UPDATE vtiger_field SET quickcreate = 0 WHERE tablename='vtiger_products' and columnname='unit_price'");
ExecuteQuery("UPDATE vtiger_field SET quickcreate = 0 WHERE tablename='vtiger_products' and columnname='qtyinstock'");

/* Necessary DB Changes for Restoring the Related information of a Deleted Record */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_relatedlists_rb(entityid int(19), action varchar(50), rel_table varchar(200), rel_column varchar(200), ref_column varchar(200), related_crm_ids text)  ENGINE=InnoDB DEFAULT CHARSET=utf8;");

// Enable Search icon for all profiles by default for Recyclebin module
$profileresult = $adb->query("select * from vtiger_profile");
$countprofiles = $adb->num_rows($profileresult);
for($i=0;$i<$countprofiles;$i++)
{
	$profileid = $adb->query_result($profileresult,$i,'profileid');
	ExecuteQuery("insert into vtiger_profile2utility values($profileid,30,3,0)");
	ExecuteQuery("insert into vtiger_profile2tab values ($profileid,30,0)");
}

/* For Role based customview support */
ExecuteQuery("alter table vtiger_customview add column status int(1) default '3'");
ExecuteQuery("update vtiger_customview set status=0 where viewname='All'");
ExecuteQuery("alter table vtiger_customview add column userid int(19) default '1'");

/* Reminder Popup support for Calendar Events */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_activity_reminder_popup(reminderid int(19) NOT NULL AUTO_INCREMENT,semodule varchar(100) NOT NULL,recordid varchar(100) NOT NULL,date_start DATE,time_start varchar(100) NOT NULL,status int(2) NOT NULL, PRIMARY KEY(reminderid))  ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_reminder_interval(reminder_intervalid int(19) NOT NULL AUTO_INCREMENT,reminder_interval varchar(200) NOT NULL,sortorderid int(19) NOT NULL,presence int(1) NOT NULL, PRIMARY KEY(reminder_intervalid))  ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("alter table vtiger_users add column reminder_interval varchar(100) NOT NULL");
ExecuteQuery("alter table vtiger_users add column reminder_next_time varchar(100)");

ExecuteQuery("INSERT INTO vtiger_reminder_interval values(".$adb->getUniqueId("vtiger_reminder_interval").",'None',0,1)");
ExecuteQuery("INSERT INTO vtiger_reminder_interval values(".$adb->getUniqueId("vtiger_reminder_interval").",'1 Minute',1,1)");
ExecuteQuery("INSERT INTO vtiger_reminder_interval values(".$adb->getUniqueId("vtiger_reminder_interval").",'5 Minutes',2,1)");
ExecuteQuery("INSERT INTO vtiger_reminder_interval values(".$adb->getUniqueId("vtiger_reminder_interval").",'15 Minutes',3,1)");
ExecuteQuery("INSERT INTO vtiger_reminder_interval values(".$adb->getUniqueId("vtiger_reminder_interval").",'30 Minutes',4,1)");
ExecuteQuery("INSERT INTO vtiger_reminder_interval values(".$adb->getUniqueId("vtiger_reminder_interval").",'45 Minutes',5,1)");
ExecuteQuery("INSERT INTO vtiger_reminder_interval values(".$adb->getUniqueId("vtiger_reminder_interval").",'1 Hour',6,1)");
ExecuteQuery("INSERT INTO vtiger_reminder_interval values(".$adb->getUniqueId("vtiger_reminder_interval").",'1 Day',7,1)");
ExecuteQuery("UPDATE vtiger_users SET reminder_interval='5 Minutes', reminder_next_time='".date('Y-m-d H:i')."'");
$user_adv_block_id = $adb->getUniqueID('vtiger_blocks');
ExecuteQuery("insert into vtiger_blocks values (".$user_adv_block_id.",29,'LBL_USER_ADV_OPTIONS',5,0,0,0,0,0)"); //Added a New Block User Image Info in Users Module
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values (29,".$adb->getUniqueID("vtiger_field").",'reminder_interval','vtiger_users',1,'16','reminder_interval','Reminder Interval',1,0,0,100,1,$user_adv_block_id,1,'V~O',1,null,'BAS')");

/* For Duplicate Records Merging feature */
ExecuteQuery("INSERT INTO vtiger_actionmapping values(10,'DuplicatesHandling',0)");
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_user2mergefields (userid int(11) REFERENCES vtiger_users( id ) , tabid int( 19 ) ,fieldid int( 19 ), visible int(2))  ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$tabid = Array(); 
$tab_res = $adb->query("SELECT distinct tabid FROM vtiger_tab"); 
$noOfTabs = $adb->num_rows($tab_res); 
for($i=0;$i<$noOfTabs;$i++) { 
	$tabid[] = $adb->query_result($tab_res,$i,'tabid'); 
}

$profile_sql = $adb->query("select profileid from vtiger_profile"); 
$num_profile = $adb->num_rows($profile_sql);
/*Duplicate merging is supported for 
 * Accounts, Potentials, Contacts, Leads, Products, Vendors, TroubleTickets
 */ 
$dupSupported = array(6, 2, 4, 7, 14, 18, 13);
for($i=0;$i<$num_profile;$i++) { 
	$profile_id = $adb->query_result($profile_sql,$i,'profileid'); 
	for($j=0;$j<$noOfTabs;$j++) {
		if (in_array($tabid[$j], $dupSupported)) {
			ExecuteQuery("insert into vtiger_profile2utility values($profile_id,".$tabid[$j].",10,0)");
		} 
	} 
} 

/* Local Backup Feature */
ExecuteQuery("alter table vtiger_systems add column server_path varchar(256)");

/* Multi-Currency Support in Products, Pricebooks and Other Inventory Modules */

// To save mapping between products and its price in different currencies.
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_productcurrencyrel (productid int(11) not null, currencyid int(11) not null, converted_price decimal(25,2) default NULL, actual_price decimal(25, 2) default NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

// Update Product related tables
ExecuteQuery("alter table vtiger_products drop column currency");
ExecuteQuery("alter table vtiger_products add column currency_id int(19) not null default '1'");

// Update Currency related tables
ExecuteQuery("alter table vtiger_currency_info add column deleted int(1) not null default '0'");

// Update Inventory related tables
ExecuteQuery("alter table vtiger_quotes drop column currency");
ExecuteQuery("alter table vtiger_quotes add column currency_id int(19) not null default '1'");
ExecuteQuery("alter table vtiger_quotes add column conversion_rate decimal(10,3) not null default '1.000'");
$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values(20,$field_id,'currency_id','vtiger_quotes','1','117','currency_id','Currency','1','0','1','100','21','51','3','I~O','1',null,'BAS')");
addFieldSecurity(20,$field_id);

$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values(20,$field_id,'conversion_rate','vtiger_quotes','1','1','conversion_rate','Conversion Rate','1','0','1','100','22','51','3','N~O','1',null,'BAS')");
addFieldSecurity(20,$field_id);

ExecuteQuery("alter table vtiger_purchaseorder add column currency_id int(19) not null default '1'");
ExecuteQuery("alter table vtiger_purchaseorder add column conversion_rate decimal(10,3) not null default '1.000'");
$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values(21,$field_id,'currency_id','vtiger_purchaseorder','1','117','currency_id','Currency','1','0','1','100','18','57','3','I~O','1',null,'BAS')");
addFieldSecurity(21,$field_id);

$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values(21,$field_id,'conversion_rate','vtiger_purchaseorder','1','1','conversion_rate','Conversion Rate','1','0','1','100','19','57','3','N~O','1',null,'BAS')");
addFieldSecurity(21,$field_id);

ExecuteQuery("alter table vtiger_salesorder add column currency_id int(19) not null default '1'");
ExecuteQuery("alter table vtiger_salesorder add column conversion_rate decimal(10,3) not null default '1.000'");
$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values(22,$field_id,'currency_id','vtiger_salesorder','1','117','currency_id','Currency','1','0','1','100','19','63','3','I~O','1',null,'BAS')");
addFieldSecurity(22,$field_id);

$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values(22,$field_id,'conversion_rate','vtiger_salesorder','1','1','conversion_rate','Conversion Rate','1','0','1','100','20','63','3','N~O','1',null,'BAS')");
addFieldSecurity(22,$field_id);

ExecuteQuery("alter table vtiger_invoice add column currency_id int(19) not null default '1'");
ExecuteQuery("alter table vtiger_invoice add column conversion_rate decimal(10,3) not null default '1.000'");
$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values(23,$field_id,'currency_id','vtiger_invoice','1','117','currency_id','Currency','1','0','1','100','18','69','3','I~O','1',null,'BAS')");
addFieldSecurity(23,$field_id);

$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values(23,$field_id,'conversion_rate','vtiger_invoice','1','1','conversion_rate','Conversion Rate','1','0','1','100','19','69','3','N~O','1',null,'BAS')");
addFieldSecurity(23,$field_id);

// Update Price Book related tables
ExecuteQuery("alter table vtiger_pricebook drop column description");
ExecuteQuery("alter table vtiger_pricebook add column currency_id int(19) not null default '1'");
ExecuteQuery("alter table vtiger_pricebookproductrel add column usedcurrency int(11) not null default '1'");
$pb_currency_field_id = $adb->getUniqueID('vtiger_field');
$pb_tab_id = getTabid('PriceBooks');
$adb->query("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values($pb_tab_id,$pb_currency_field_id,'currency_id','vtiger_pricebook','1','117','currency_id','Currency','1','0','0','100','5','48','1','I~M','0','3','BAS')");
$adb->query("insert into vtiger_cvcolumnlist values('23','2','vtiger_pricebook:currency_id:currency_id:PriceBooks_Currency:I')");
addFieldSecurity($pb_tab_id,$pb_currency_field_id);

/* Documents module */
$documents_tab_id = getTabid('Documents');
ExecuteQuery("delete from vtiger_cvcolumnlist where columnname like '%Notes_Contact_Name%'");
ExecuteQuery("delete from vtiger_cvcolumnlist where columnname like '%Notes_Related_to%'");

ExecuteQuery("insert into vtiger_def_org_share values (".$adb->getUniqueID('vtiger_def_org_share').",$documents_tab_id,2,0)");

for($i=0;$i<4;$i++)
{
	ExecuteQuery("insert into vtiger_org_share_action2tab values(".$i.",$documents_tab_id)");
}	

ExecuteQuery("alter table vtiger_customview drop foreign key fk_1_vtiger_customview ");
ExecuteQuery("update vtiger_customview set entitytype='Documents' where entitytype='Notes'");
ExecuteQuery("update vtiger_tab set ownedby=0,name='Documents',tablabel='Documents' where tabid=$documents_tab_id");
ExecuteQuery("update vtiger_entityname set modulename='Documents' where tabid=$documents_tab_id");
ExecuteQuery("alter table vtiger_customview add constraint FOREIGN KEY fk_1_vtiger_customview (entitytype) REFERENCES vtiger_tab (name) ON DELETE CASCADE");

ExecuteQuery("UPDATE vtiger_relatedlists SET actions='add,select', related_tabid=$documents_tab_id WHERE name='get_attachments'");
ExecuteQuery("alter table vtiger_notes add(folderid int(19) DEFAULT 1,filetype varchar(50) default NULL,filelocationtype varchar(5) default NULL,filedownloadcount int(19) default NULL,filestatus int(19) default NULL,filesize int(19) NOT NULL default '0',fileversion varchar(50) default NULL)");

ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_attachmentsfolder ( folderid int(19) AUTO_INCREMENT NOT NULL,foldername varchar(200) NOT NULL default '', description varchar(250) default '', createdby int(19) NOT NULL, sequence int(19) default NULL, PRIMARY KEY  (folderid)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

ExecuteQuery("insert into vtiger_attachmentsfolder values (1,'Existing Notes','Contains all Notes migrated from the earlier version',1,1)");

ExecuteQuery("alter table vtiger_senotesrel drop foreign key fk_2_vtiger_senotesrel ");

ExecuteQuery("UPDATE vtiger_crmentity SET setype='Documents' WHERE setype='Notes'");

$attachmentidQuery = 'select vtiger_seattachmentsrel.attachmentsid as attachmentid, vtiger_seattachmentsrel.crmid as id from vtiger_seattachmentsrel INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_seattachmentsrel.crmid WHERE vtiger_crmentity.deleted = 0';
$res = $adb->pquery($attachmentidQuery,array());
global $default_charset;
if($adb->num_rows($res)>0){
	for($index=0;$index<$adb->num_rows($res);$index++){
		$attachmentid = $adb->query_result($res,$index,'attachmentid');
		$crmid = $adb->query_result($res,$index,'id');
		if($attachmentid != ''){	
			 $attachmentInfoQuery = 'select * from vtiger_attachments where attachmentsid = ?';
			 $attachres = $adb->pquery($attachmentInfoQuery,array($attachmentid));
			 if($adb->num_rows($attachres)>0){
				 $filename = $adb->query_result($attachres,0,'name');
				 $filename = $adb->sql_escape_string(html_entity_decode($filename,ENT_QUOTES,$default_charset));
				 $attch_sub = $adb->query_result($attachres,0,'subject');
				 $description = $adb->query_result($attachres,0,'description');
				 $filepath = $adb->query_result($attachres,0,'path');
			 	 $filetype = $adb->query_result($attachres,0,'type');
			 	 if(file_exists($filepath.$attachmentid."_".$filename)) {
				 	$filesize = filesize($filepath.$attachmentid."_".$filename);
				 	$filestatus = "1";
			 	 } else { 
				 	$filesize = "0";
				 	$filestatus = "0";
			 	 }	
				 
				 $noteid_query = $adb->pquery("SELECT notesid FROM vtiger_notes WHERE notesid = ?",array($crmid));
				 if($adb->num_rows($noteid_query)>0) {
				 	$notesid = $adb->query_result($noteid_query,0,"notesid");
				 	ExecuteQuery("update vtiger_notes set folderid = 1,filestatus='$filestatus',filelocationtype='I',filedownloadcount=0,fileversion='',filetype='".$filetype."',filesize='".$filesize."',filename='".$filename."' where notesid = ".$notesid);
				 } else {
					require_once("modules/Documents/Documents.php");
	
				 	$notes_obj = new Documents();
				 	if($attch_sub == '') $attch_sub = $filename;
				 	$notes_obj->column_fields['notes_title'] = decode_html($attch_sub);
				 	$notes_obj->column_fields['notecontent'] = decode_html($description);
				 	$notes_obj->column_fields['assigned_user_id'] = 1;
				 	$notes_obj->save("Documents");
				 	$notesid = $notes_obj->id;
			 		
			 		ExecuteQuery("Update vtiger_notes set folderid=1,filedownloadcount=0, filestatus='$filestatus', fileversion='', filesize = '$filesize', filetype = '$filetype' , filelocationtype = 'I', filename = '$filename' where notesid = $notesid");
					ExecuteQuery("INSERT INTO vtiger_senotesrel VALUES($crmid,$notesid)");
					ExecuteQuery("INSERT INTO vtiger_seattachmentsrel VALUES($notesid,$attachmentid)");
				 }
			 }
		}
		else{
			ExecuteQuery("update vtiger_notes set folderid=1, filestatus=1,filelocationtype='',filedownloadcount='',fileversion='',filetype='',filesize='',filename='' where notesid = ".$notesid);
		}
	}
}

$fieldid = Array();
for($i=0;$i<8;$i++)
{
	$fieldid[$i] = $adb->getUniqueID("vtiger_field");
}
$file_block_id = $adb->getUniqueID('vtiger_blocks');
ExecuteQuery("insert into vtiger_blocks values($file_block_id,$documents_tab_id,'LBL_FILE_INFORMATION',2,0,0,0,0,0)");

$description_block_id_Query = 'select blockid from vtiger_blocks where tabid = '.$documents_tab_id.' and blocklabel = "" ';
$desc_id = $adb->pquery($description_block_id_Query,array());
if($adb->num_rows($desc_id)>0){
	$desc = $adb->query_result($desc_id,0,'blockid');
	$desc_update = 'update vtiger_blocks set blocklabel ="LBL_DESCRIPTION",show_title = 0,sequence = 3 where blockid = ?';
	$desc_block_update = $adb->pquery($desc_update,array($desc));
	ExecuteQuery("update vtiger_field set sequence=1,quickcreate=1,presence=2,block=$desc where tabid=$documents_tab_id and columnname='notecontent'");	
}

ExecuteQuery("update vtiger_field set sequence=1 where tabid=$documents_tab_id and columnname='title'");
ExecuteQuery("update vtiger_field set sequence=8,quickcreate=3 where tabid=$documents_tab_id and columnname='createdtime'");
ExecuteQuery("update vtiger_field set sequence=9,quickcreate=3 where tabid=$documents_tab_id and columnname='modifiedtime'");

ExecuteQuery("update vtiger_field set sequence = 3,quickcreate=3,block = $file_block_id,fieldlabel='File Name',displaytype = 1,uitype=28  where tabid = $documents_tab_id and columnname = 'filename'");

ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values ($documents_tab_id,".$fieldid[0].",'smownerid','vtiger_crmentity',1,53,'assigned_user_id','Assigned To',1,0,0,100,2,17,1,'V~M',0,3,'BAS')");
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values ($documents_tab_id,".$fieldid[1].",'filetype','vtiger_notes',1,1,'filetype','File Type',1,2,0,100,5,$file_block_id,2,'V~O',3,'','BAS')");
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values ($documents_tab_id,".$fieldid[2].",'filesize','vtiger_notes',1,1,'filesize','File Size',1,2,0,100,4,$file_block_id,2,'V~O',3,'','BAS')");
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values ($documents_tab_id,".$fieldid[3].",'filelocationtype','vtiger_notes',1,27,'filelocationtype','Download Type',1,0,0,100,1,$file_block_id,1,'V~O',1,'','BAS')");
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values ($documents_tab_id,".$fieldid[4].",'fileversion','vtiger_notes',1,1,'fileversion','Version',1,2,0,100,6,17,1,'V~O',1,'','BAS')");
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values ($documents_tab_id,".$fieldid[5].",'filestatus','vtiger_notes',1,56,'filestatus','Active',1,2,0,100,2,$file_block_id,1,'V~O',1,'','BAS')");
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values ($documents_tab_id,".$fieldid[6].",'filedownloadcount','vtiger_notes',1,1,'filedownloadcount','Download Count',1,2,0,100,6,$file_block_id,2,'I~O',3,'','BAS')");
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values ($documents_tab_id,".$fieldid[7].",'folderid','vtiger_notes',1,26,'folderid','Folder Name',1,2,0,100,4,17,1,'V~O',2,'2','BAS')");

for($i=0;$i<count($fieldid);$i++)
{
	addFieldSecurity($documents_tab_id,$fieldid[$i]);
}
//Rename Attachments to Documents in relatedlist 
ExecuteQuery("update vtiger_relatedlists set label='Documents' where name = 'get_attachments'");

$dbQuery = "select notesid,contact_id from vtiger_notes";
$dbresult = $adb->query($dbQuery);
$noofrecords = $adb->num_rows($dbresult);
if($noofrecords > 0)
{
    for($i=0;$i<$noofrecords;$i++)
    {
        $contactid = $adb->query_result($dbresult,$i,'contact_id');
        $notesid = $adb->query_result($dbresult,$i,'notesid');
		$dup_check = $adb->pquery("SELECT * from vtiger_senotesrel WHERE  crmid = ? AND notesid = ?",array($contactid,$notesid));
		if($contactid != 0 && $adb->num_rows($dup_check)==0){
           ExecuteQuery("insert into vtiger_senotesrel values (".$contactid.",".$notesid.")");
		}
    }
}

ExecuteQuery("delete from vtiger_field where tabid = 8 and fieldname = 'contact_id'");
ExecuteQuery("delete from vtiger_field where tabid = 8 and fieldname = 'parent_id'");

ExecuteQuery("alter table vtiger_notes drop column contact_id");

ExecuteQuery("update vtiger_cvcolumnlist set columnname='vtiger_notes:filename:filename:Documents_Filename:V' where cvid = 22 and columnindex = 3");
custom_addCustomFilterColumn('Documents','All', 'vtiger_crmentity','smownerid','assigned_user_id','Documents_Assigned_To:V',7);

ExecuteQuery("UPDATE vtiger_field SET columnname='name' WHERE fieldname='filename' AND tablename='vtiger_attachments' AND tabid=".getTabid('Emails'));

//remove filename column from trouble ticket
ExecuteQuery("alter table vtiger_troubletickets drop column filename");
ExecuteQuery("delete from vtiger_field where fieldname='filename' and tablename='vtiger_attachments' AND tabid=".getTabid('HelpDesk'));
//End: Database changes regarding Documents module

/* Home Page Customization */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_homestuff (stuffid int(19) NOT NULL default '0', stuffsequence int(19) NOT NULL default '0', stufftype varchar(100) default NULL, userid int(19) NOT NULL, visible int(10) NOT NULL default '0', stufftitle varchar(100) default NULL, PRIMARY KEY  (stuffid), KEY stuff_stuffid_idx (stuffid), KEY fk_1_vtiger_homestuff (userid))  ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_homedashbd (stuffid int(19) NOT NULL default 0, dashbdname varchar(100) default NULL, dashbdtype varchar(100) default NULL, PRIMARY KEY  (stuffid), KEY stuff_stuffid_idx (stuffid))  ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_homedefault (stuffid int(19) NOT NULL default 0, hometype varchar(30) NOT NULL, maxentries int(19) default NULL, setype varchar(30) default NULL, PRIMARY KEY  (stuffid), KEY stuff_stuffid_idx (stuffid))  ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_homemodule (stuffid int(19) NOT NULL, modulename varchar(100) default NULL, maxentries int(19) NOT NULL, customviewid int(19) NOT NULL, setype varchar(30) NOT NULL, PRIMARY KEY  (stuffid), KEY stuff_stuffid_idx (stuffid))  ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_homemoduleflds (stuffid int(19) default NULL, fieldname varchar(255) default NULL, KEY stuff_stuffid_idx (stuffid))  ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_homerss (stuffid int(19) NOT NULL default 0, url varchar(100) default NULL, maxentries int(19) NOT NULL, PRIMARY KEY  (stuffid), KEY stuff_stuffid_idx (stuffid))  ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 

ExecuteQuery("ALTER TABLE vtiger_homestuff ADD CONSTRAINT fk_1_vtiger_homestuff FOREIGN KEY (userid) REFERENCES vtiger_users (id) ON DELETE CASCADE");
ExecuteQuery("ALTER TABLE vtiger_homedashbd ADD CONSTRAINT fk_1_vtiger_homedashbd FOREIGN KEY (stuffid) REFERENCES vtiger_homestuff (stuffid) ON DELETE CASCADE");
ExecuteQuery("ALTER TABLE vtiger_homedefault ADD CONSTRAINT fk_1_vtiger_homedefault FOREIGN KEY (stuffid) REFERENCES vtiger_homestuff (stuffid) ON DELETE CASCADE");
ExecuteQuery("ALTER TABLE vtiger_homemodule ADD CONSTRAINT fk_1_vtiger_homemodule FOREIGN KEY (stuffid) REFERENCES vtiger_homestuff (stuffid) ON DELETE CASCADE");
ExecuteQuery("ALTER TABLE vtiger_homemoduleflds ADD CONSTRAINT fk_1_vtiger_homemoduleflds FOREIGN KEY (stuffid) REFERENCES vtiger_homemodule (stuffid) ON DELETE CASCADE");
ExecuteQuery("ALTER TABLE vtiger_homerss ADD CONSTRAINT fk_1_vtiger_homerss FOREIGN KEY (stuffid) REFERENCES vtiger_homestuff (stuffid) ON DELETE CASCADE");

//to get the users lists
$query = $adb->pquery('select * from vtiger_users',array());
for($i=0;$i<$adb->num_rows($query);$i++)
{
	$userid = $adb->query_result($query,$i,'id');

	$s1=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s1.",1,'Default',".$userid.",1,'Top Accounts')";
	$res=$adb->pquery($sql,array());

	$s2=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s2.",2,'Default',".$userid.",1,'Home Page Dashboard')";
	$res=$adb->pquery($sql,array());

	$s3=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s3.",3,'Default',".$userid.",1,'Top Potentials')";
	$res=$adb->pquery($sql,array());

	$s4=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s4.",4,'Default',".$userid.",1,'Top Quotes')";
	$res=$adb->pquery($sql,array());

	$s5=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s5.",5,'Default',".$userid.",1,'Key Metrics')";
	$res=$adb->pquery($sql,array());

	$s6=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s6.",6,'Default',".$userid.",1,'Top Trouble Tickets')";
	$res=$adb->pquery($sql,array());

	$s7=$adb->getUniqueID("vtiger_homestuff"); 
	$sql="insert into vtiger_homestuff values(".$s7.",7,'Default',".$userid.",1,'Upcoming Activities')";
	$res=$adb->pquery($sql,array());

	$s8=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s8.",8,'Default',".$userid.",1,'My Group Allocation')";
	$res=$adb->pquery($sql,array());

	$s9=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s9.",9,'Default',".$userid.",1,'Top Sales Orders')";
	$res=$adb->pquery($sql,array());

	$s10=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s10.",10,'Default',".$userid.",1,'Top Invoices')";
	$res=$adb->pquery($sql,array());

	$s11=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s11.",11,'Default',".$userid.",1,'My New Leads')";
	$res=$adb->pquery($sql,array());

	$s12=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s12.",12,'Default',".$userid.",1,'Top Purchase Orders')";
	$res=$adb->pquery($sql,array());

	$s13=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s13.",13,'Default',".$userid.",1,'Pending Activities')";
	$res=$adb->pquery($sql,array());

	$s14=$adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values(".$s14.",14,'Default',".$userid.",1,'My Recent FAQs')";
	$res=$adb->pquery($sql,array());
	
	// Non-Default Home Page widget (no entry is requried in vtiger_homedefault below)
	$tc = $adb->getUniqueID("vtiger_homestuff");
	$sql="insert into vtiger_homestuff values($tc, 15, 'Tag Cloud', $userid, 0, 'Tag Cloud')";
	$adb->query($sql);

	$sql="insert into vtiger_homedefault values(".$s1.",'ALVT',5,'Accounts')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s2.",'HDB',5,'Dashboard')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s3.",'PLVT',5,'Potentials')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s4.",'QLTQ',5,'Quotes')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s5.",'CVLVT',5,'NULL')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s6.",'HLT',5,'HelpDesk')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s7.",'UA',5,'Calendar')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s8.",'GRT',5,'NULL')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s9.",'OLTSO',5,'SalesOrder')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s10.",'ILTI',5,'Invoice')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s11.",'MNL',5,'Leads')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s12.",'OLTPO',5,'PurchaseOrder')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s13.",'PA',5,'Calendar')";
	$adb->pquery($sql,array());

	$sql="insert into vtiger_homedefault values(".$s14.",'LTFAQ',5,'Faq')";
	$adb->pquery($sql,array());
}
for($i=0;$i<$adb->num_rows($query);$i++)
{
	$def_homeorder = $adb->query_result($query,$i,'homeorder');
	$user_id = $adb->query_result($query,$i,'id');
	$def_array = explode(",",$def_homeorder);
	$sql = $adb->pquery("SELECT vtiger_homestuff.stuffid FROM vtiger_homestuff INNER JOIN vtiger_homedefault WHERE vtiger_homedefault.hometype in (". generateQuestionMarks($def_array) . ") AND vtiger_homestuff.stuffid = vtiger_homedefault.stuffid AND vtiger_homestuff.userid = ?",array($def_array,$user_id));
	$stuffid_list = array();
	for($j=0;$j<$adb->num_rows($sql);$j++) {
		$stuffid_list[] = $adb->query_result($sql,$j,'stuffid');
	}
	$adb->pquery("UPDATE vtiger_homestuff SET visible = 0 WHERE stuffid in (". generateQuestionMarks($stuffid_list) .")",array($stuffid_list));
}

/* For Layout Editor */
ExecuteQuery("ALTER TABLE vtiger_blocks ADD COLUMN display_status int(1) NOT NULL DEFAULT '1'");

/* Adding Custom Events Migration */
ExecuteQuery("UPDATE vtiger_field SET uitype=15,typeofdata='V~M' WHERE tabid=16 and columnname='activitytype'");
ExecuteQuery("alter table vtiger_activitytype drop column sortorderid");
ExecuteQuery("alter table vtiger_activitytype add column picklist_valueid int(19) NOT NULL default '0'");
$picklist_id = $adb->getUniqueId("vtiger_picklist");
ExecuteQuery("INSERT INTO vtiger_picklist VALUES(".$picklist_id.",'activitytype')");

$query = $adb->pquery("SELECT * from vtiger_activitytype",array());
for($i=0;$i<$adb->num_rows($query);$i++){
	$picklist_valueid = $adb->getUniqueID('vtiger_picklistvalues');
	$activitytypeid = $adb->query_result($query,$i,'activitytypeid');
	$adb->pquery("UPDATE vtiger_activitytype SET picklist_valueid=? , presence=0 WHERE activitytypeid = ? ",array($picklist_valueid,$activitytypeid));
}

$role_query = $adb->query("SELECT * FROM vtiger_role");
for($j=0;$j<$adb->num_rows($role_query);$j++){
	$roleid = $adb->query_result($role_query,$j,'roleid');
	$query = $adb->pquery("SELECT * from vtiger_activitytype",array());
	for($i=0;$i<$adb->num_rows($query);$i++){
		$picklist_valueid = $adb->query_result($query,$i,'picklist_valueid');
		ExecuteQuery("INSERT INTO vtiger_role2picklist VALUES('".$roleid."',".$picklist_valueid.",".$picklist_id.",$i)");
	}
}

$uniqueid = $adb->getUniqueID("vtiger_relatedlists");
$faqtabid = getTabid('Faq');
ExecuteQuery("insert into vtiger_relatedlists values($uniqueid,$faqtabid,$documents_tab_id,'get_attachments',1,'Documents',0,'add,select')");
//CustomEvents Migration Ends

/* Important column renaming to support database porting */
$adb->pquery("ALTER TABLE vtiger_profile2standardpermissions CHANGE Operation testoperation INTEGER", array());
$adb->pquery("ALTER TABLE vtiger_profile2standardpermissions CHANGE testoperation operation INTEGER", array());

$renameArray = array(
		"vtiger_sales_stage",
		"vtiger_faqcategories",
		"vtiger_faqstatus",
		"vtiger_rating",
		"vtiger_ticketcategories",
		"vtiger_ticketpriorities",
		"vtiger_ticketseverities",
		"vtiger_ticketstatus"
);
foreach($renameArray as $tablename) {
	$adb->pquery("ALTER TABLE $tablename CHANGE PRESENCE testpresence INTEGER", array());
	$adb->pquery("ALTER TABLE $tablename CHANGE testpresence presence INTEGER", array());	
}
// Renaming completed

/* Important database schema changes to support database porting */
ExecuteQuery("alter table vtiger_attachments drop index attachments_description_type_attachmentsid_idx");
ExecuteQuery("alter table vtiger_attachments modify column description TEXT");
ExecuteQuery("alter table vtiger_emaildetails modify column idlists TEXT");

/* Product Bundles Feature */
ExecuteQuery("insert into vtiger_relatedlists values(".$adb->getUniqueID('vtiger_relatedlists').",".getTabid("Products").",".getTabid("Products").",'get_products',13,'Product Bundles',0,'add,select')");
ExecuteQuery("insert into vtiger_relatedlists values(".$adb->getUniqueID('vtiger_relatedlists').",".getTabid("Products").",".getTabid("Products").",'get_parent_products',14,'Parent Products',0,'')");

/* vtmailscanner customization */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_mailscanner(scannerid INT AUTO_INCREMENT NOT NULL PRIMARY KEY,scannername VARCHAR(30),
	server VARCHAR(100),protocol VARCHAR(10),username VARCHAR(255),password VARCHAR(255),ssltype VARCHAR(10),
sslmethod VARCHAR(30),connecturl VARCHAR(255),searchfor VARCHAR(10),markas VARCHAR(10),isvalid INT(1)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_mailscanner_ids(scannerid INT, messageid TEXT,crmid INT) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_mailscanner_folders(folderid INT AUTO_INCREMENT NOT NULL PRIMARY KEY,scannerid INT,foldername VARCHAR(255),lastscan VARCHAR(30),rescan INT(1), enabled INT(1)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_mailscanner_rules(ruleid INT AUTO_INCREMENT NOT NULL PRIMARY KEY,scannerid INT,fromaddress VARCHAR(255),toaddress VARCHAR(255),subjectop VARCHAR(20),subject VARCHAR(255),bodyop VARCHAR(20),body VARCHAR(255),matchusing VARCHAR(5),sequence INT) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_mailscanner_actions(actionid INT AUTO_INCREMENT NOT NULL PRIMARY KEY,scannerid INT,actiontype VARCHAR(10),module VARCHAR(30),lookup VARCHAR(30),sequence INT) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_mailscanner_ruleactions(ruleid INT,actionid INT) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
// END

/* Recurring Invoice Feature */
$new_block_seq_no = 2;
// Get all the blocks of the same module (SalesOrder), and update their sequence depending on the sequence of the new block added.
$res = $adb->query("SELECT blockid FROM vtiger_blocks WHERE tabid = ". getTabid('SalesOrder') ." AND sequence >= ". $new_block_seq_no);
$no_of_blocks = $adb->num_rows($res);
for ($i=0; $i<$no_of_blocks;$i++) {
	$blockid = $adb->query_result($res, $i, 'blockid');
	ExecuteQuery("UPDATE vtiger_blocks SET sequence = sequence+1 WHERE blockid=$blockid");
}
// Add new block to show recurring invoice information at specified position (sequence of blocks)
$new_block_id = $adb->getUniqueID('vtiger_blocks');
ExecuteQuery("INSERT INTO vtiger_blocks VALUES (".$new_block_id.",".getTabid('SalesOrder').",'Recurring Invoice Information',$new_block_seq_no,0,0,0,0,0,1)");

ExecuteQuery("ALTER TABLE vtiger_salesorder ADD COLUMN enable_recurring INT default 0");
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_invoice_recurring_info(salesorderid INT, recurring_frequency VARCHAR(200), start_period DATE, end_period DATE, last_recurring_date DATE default NULL, " .
		"			payment_duration VARCHAR(200), invoice_status VARCHAR(200)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_recurring_frequency(recurring_frequency_id INT, recurring_frequency VARCHAR(200), sortorderid INT, presence INT) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
// Add default values for the recurring_frequency picklist
ExecuteQuery("INSERT INTO vtiger_recurring_frequency values(".$adb->getUniqueID('vtiger_recurring_frequency').",'--None--',1,1)");
ExecuteQuery("INSERT INTO vtiger_recurring_frequency values(".$adb->getUniqueID('vtiger_recurring_frequency').",'Daily',2,1)");
ExecuteQuery("INSERT INTO vtiger_recurring_frequency values(".$adb->getUniqueID('vtiger_recurring_frequency').",'Weekly',3,1)");
ExecuteQuery("INSERT INTO vtiger_recurring_frequency values(".$adb->getUniqueID('vtiger_recurring_frequency').",'Monthly',4,1)");
ExecuteQuery("INSERT INTO vtiger_recurring_frequency values(".$adb->getUniqueID('vtiger_recurring_frequency').",'Quarterly',5,1)");
ExecuteQuery("INSERT INTO vtiger_recurring_frequency values(".$adb->getUniqueID('vtiger_recurring_frequency').",'Yearly',6,1)");

ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_payment_duration(payment_duration_id INT, payment_duration VARCHAR(200), sortorderid INT, presence INT) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
// Add default values for the vtiger_payment_duration picklist
ExecuteQuery("INSERT INTO vtiger_payment_duration values(".$adb->getUniqueID('vtiger_payment_duration').",'Net 30 days',1,1)");
ExecuteQuery("INSERT INTO vtiger_payment_duration values(".$adb->getUniqueID('vtiger_payment_duration').",'Net 45 days',2,1)");
ExecuteQuery("INSERT INTO vtiger_payment_duration values(".$adb->getUniqueID('vtiger_payment_duration').",'Net 60 days',3,1)");

// Add fields for the Recurring Information block
$salesorder_tabid = getTabid('SalesOrder');
$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values($salesorder_tabid,$field_id,'enable_recurring','vtiger_salesorder',1,'56','enable_recurring','Enable Recurring',1,0,0,100,1,$new_block_id,1,'C~O',3,null,'BAS')");
addFieldSecurity($salesorder_tabid,$field_id);

$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values($salesorder_tabid,$field_id,'recurring_frequency','vtiger_invoice_recurring_info',1,'16','recurring_frequency','Frequency',1,0,0,100,2,$new_block_id,1,'V~O',3,null,'BAS')");
addFieldSecurity($salesorder_tabid,$field_id);

$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values($salesorder_tabid,$field_id,'start_period','vtiger_invoice_recurring_info',1,'5','start_period','Start Period',1,0,0,100,3,$new_block_id,1,'D~O',3,null,'BAS')");
addFieldSecurity($salesorder_tabid,$field_id);

$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values($salesorder_tabid,$field_id,'end_period','vtiger_invoice_recurring_info',1,'5','end_period','End Period',1,0,0,100,4,$new_block_id,1,'D~O~OTH~G~start_period~Start Period',3,null,'BAS')");
addFieldSecurity($salesorder_tabid,$field_id);

$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values($salesorder_tabid,$field_id,'payment_duration','vtiger_invoice_recurring_info',1,'16','payment_duration','Payment Duration',1,0,0,100,5,$new_block_id,1,'V~O',3,null,'BAS')");
addFieldSecurity($salesorder_tabid,$field_id);

$field_id = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type) values($salesorder_tabid,$field_id,'invoice_status','vtiger_invoice_recurring_info',1,'15','invoicestatus','Invoice Status',1,0,0,100,6,$new_block_id,1,'V~O',3,null,'BAS')");
addFieldSecurity($salesorder_tabid,$field_id);

// Add new picklist value 'AutoCreated' for Invoice Status and add the same for all the existing roles.
$picklistRes = $adb->query("SELECT picklistid FROM vtiger_picklist WHERE name='invoicestatus'");
if($adb->num_rows($picklistRes)>0){
	$picklistid = $adb->query_result($picklistRes,0,'picklistid');

	$picklist_valueid = $adb->getUniqueID('vtiger_picklistvalues');
	$max_seq_id_qry = $adb->pquery("SELECT max(inovicestatusid) as maxid from vtiger_invoicestatus",array());
	if($adb->num_rows($max_seq_id_qry)>0) {
		$tmp = $adb->getUniqueID('vtiger_invoicestatus');
		$max_seq_id = $adb->query_result($max_seq_id_qry,0,'maxid');
		$adb->pquery("UPDATE vtiger_invoicestatus_seq SET id=?",array($max_seq_id));
	}
	$id = $adb->getUniqueID('vtiger_invoicestatus');
	
	ExecuteQuery("insert into vtiger_invoicestatus values($id, 'AutoCreated', 1, $picklist_valueid)");
	
	//Default entries for role2picklist relation has been inserted..
	$sql="select roleid from vtiger_role";
	$role_result = $adb->pquery($sql, array());
	$numrow = $adb->num_rows($role_result);
	for($k=0; $k < $numrow; $k ++)
	{
		$roleid = $adb->query_result($role_result,$k,'roleid');
		$params = array($roleid, $picklist_valueid, $picklistid, $id-1);
		$adb->pquery("insert into vtiger_role2picklist values(?,?,?,?)", $params);
	}
}

// Add Event handler for Recurring Invoice
$em->registerHandler('vtiger.entity.aftersave', 'modules/SalesOrder/RecurringInvoiceHandler.php', 'RecurringInvoiceHandler');

/* Workflow Manager - com_vtiger_workflow */
ExecuteQuery("CREATE TABLE IF NOT EXISTS com_vtiger_workflows_seq (id int(11)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("insert into com_vtiger_workflows_seq (id) values(1)");
ExecuteQuery("CREATE TABLE IF NOT EXISTS com_vtiger_workflows (workflow_id int, module_name varchar(100), summary varchar(100), test varchar(400), task_id int(11), exec_date int, execution_condition varchar(50)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery('CREATE TABLE IF NOT EXISTS com_vtiger_workflow_activatedonce (entity_id int, workflow_id int) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

ExecuteQuery("CREATE TABLE IF NOT EXISTS com_vtiger_workflowtasks_seq (id int(11)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("insert into com_vtiger_workflowtasks_seq (id) values(1)");
ExecuteQuery("CREATE TABLE IF NOT EXISTS com_vtiger_workflowtasks (task_id int, workflow_id int, summary varchar(100), task text, primary key(task_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

ExecuteQuery("CREATE TABLE IF NOT EXISTS com_vtiger_workflowtask_queue (task_id int, entity_id varchar(100), do_after int, primary key(task_id, entity_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

ExecuteQuery("CREATE TABLE IF NOT EXISTS com_vtiger_workflowtasks_entitymethod_seq (id int(11)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("insert into  com_vtiger_workflowtasks_entitymethod_seq (id) values(1)");
ExecuteQuery("CREATE TABLE IF NOT EXISTS com_vtiger_workflowtasks_entitymethod (workflowtasks_entitymethod_id int, module_name varchar(100), method_name varchar(100), function_path varchar(400), function_name varchar(100), primary key(workflowtasks_entitymethod_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

ExecuteQuery("CREATE TABLE com_vtiger_workflowtemplates (
  template_id int(11) NOT NULL default '0',
  module_name varchar(100) default NULL,
  title varchar(400) default NULL,
  template text, PRIMARY KEY  (template_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 

$em->registerHandler('vtiger.entity.aftersave', 'modules/com_vtiger_workflow/VTEventHandler.inc', 'VTWorkflowEventHandler');
// com_vtiger_workflow ends

/* Mass Edit Feature */
ExecuteQuery("ALTER TABLE vtiger_field ADD COLUMN masseditable int(11) NOT NULL DEFAULT '1'");
$tab_field_array = array(
	'Accounts'=>array('accountname','account_id'),
	'Contacts'=>array('imagename','portal','contact_id'),
	'Products'=>array('imagename','product_id'),
	'Invoice'=>array('invoice_no','salesorder_id'),
	'SalesOrder'=>array('quote_id','salesorder_no','enable_recurring','recurring_frequency','start_period','end_period','payment_duration','invoicestatus'),
	'PurchaseOrder'=>array('purchaseorder_no'),
	'Quotes'=>array('quote_no'),
	'HelpDesk'=>array('filename'),
);
foreach($tab_field_array as $index=>$value){
	$tabid = getTabid($index);
	$adb->pquery("UPDATE vtiger_field SET masseditable=0 WHERE tabid=? AND fieldname IN (".generateQuestionMarks($value).")",array($tabid,$value));
}

/* Showing Emails in Vendors related list */
ExecuteQuery("insert into vtiger_relatedlists values(".$adb->getUniqueID('vtiger_relatedlists').",".getTabid("Vendors").",".getTabid("Emails").",'get_emails',4,'Emails',0,'add')");

/* Added for module sequence number customization */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_modentity_num (num_id int(19) NOT NULL, semodule varchar(50) NOT NULL, prefix varchar(50) NOT NULL DEFAULT '', start_id varchar(50) NOT NULL, cur_id varchar(50) NOT NULL, active int(2) NOT NULL, PRIMARY KEY(num_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

// Setup module sequence numbering for all modules (except Invoice).
function custom_addInventoryRows($paramArray){
	global $adb;

	$fieldCreateCount = 0;

	for($index = 0; $index < count($paramArray); ++$index) {
		$criteria = $paramArray[$index];

		$semodule = $criteria['semodule'];
		$adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)",array($adb->getUniqueId("vtiger_modentity_num"),$semodule,$criteria['prefix'],$criteria['startid'],$criteria['curid'],1));
	}
}
$modseq = array(
	'Leads'     =>'LEA',
	'Accounts'  =>'ACC',
	'Campaigns' =>'CAM',	
	'Contacts'  =>'CON',
	'Potentials'=>'POT',
	'HelpDesk'  =>'TT',
	'Quotes'    =>'QUO',
	'SalesOrder'=>'SO',
	'PurchaseOrder'=>'PO',
	'Products'  =>'PRO',
	'Vendors'   =>'VEN',
	'PriceBooks'=>'PB',
	'Faq'       =>'FAQ',
	'Documents' =>'DOC'
);
foreach($modseq as $modname => $prefix) {
	custom_addInventoryRows(
		array(
			array('semodule'=>$modname, 'active'=>'1','prefix'=>$prefix,'startid'=>'1','curid'=>'1')
		)
	);
}
// Setup module sequence for Invoice
@include_once('user_privileges/CustomInvoiceNo.php');
// We need to move the existing information of Custom numbering to database
// but in case the previous setting is not available...we are defaulting
if(!isset($inv_str)) $inv_str = 'INV';
if(!isset($inv_no)) $inv_no = '1'; 

custom_addInventoryRows(
		array(
			array('semodule'=>'Invoice', 'active'=>'1','prefix'=>decode_html($inv_str),'startid'=>'1','curid'=>$inv_no)
		)
	);

// Add Module Number Field to UI.						

$blockid = getBlockId(6,'LBL_ACCOUNT_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (6,".$adb->getUniqueID("vtiger_field").",'account_no','vtiger_account',1,'4','account_no','Account No',1,0,0,100,2,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_account ADD COLUMN account_no varchar(100) not null");

$blockid = getBlockId(7,'LBL_LEAD_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (7,".$adb->getUniqueID("vtiger_field").",'lead_no','vtiger_leaddetails',1,'4','lead_no','Lead No',1,0,0,100,3,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_leaddetails ADD COLUMN lead_no varchar(100) not null");

$blockid = getBlockId(4,'LBL_CONTACT_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (4,".$adb->getUniqueID("vtiger_field").",'contact_no','vtiger_contactdetails',1,'4','contact_no','Contact Id',1,0,0,100,3,$blockid,1,'V~O',1,null,'BAS',0)");		
ExecuteQuery("ALTER TABLE vtiger_contactdetails ADD COLUMN contact_no varchar(100) not null");

$blockid = getBlockId(2,'LBL_OPPORTUNITY_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (2,".$adb->getUniqueID("vtiger_field").",'potential_no','vtiger_potential',1,'4','potential_no','Potential No',1,0,0,100,2,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_potential ADD COLUMN potential_no varchar(100) not null");

$blockid = getBlockId(26,'LBL_CAMPAIGN_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (26,".$adb->getUniqueID("vtiger_field").",'campaign_no','vtiger_campaign',1,'4','campaign_no','Campaign No',1,0,0,100,2,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_campaign ADD COLUMN campaign_no varchar(100) not null");

$blockid = getBlockId(13,'LBL_TICKET_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (13,".$adb->getUniqueID("vtiger_field").",'ticket_no','vtiger_troubletickets',1,'4','ticket_no','Ticket No',1,0,0,100,1,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_troubletickets ADD COLUMN ticket_no varchar(100) not null");

$blockid = getBlockId(14,'LBL_PRODUCT_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (14,".$adb->getUniqueID("vtiger_field").",'product_no','vtiger_products',1,'4','product_no','Product No',1,0,0,100,2,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_products ADD COLUMN product_no varchar(100) not null");

$blockid = getBlockId(8,'LBL_NOTE_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (8,".$adb->getUniqueID("vtiger_field").",'note_no','vtiger_notes',1,'4','note_no','Document No',1,0,0,100,7,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_notes ADD COLUMN note_no varchar(100) not null");

$blockid = getBlockId(15,'LBL_FAQ_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (15,".$adb->getUniqueID("vtiger_field").",'faq_no','vtiger_faq',1,'4','faq_no','Faq No',1,0,0,100,2,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_faq ADD COLUMN faq_no varchar(100) not null");

$blockid = getBlockId(18,'LBL_VENDOR_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (18,".$adb->getUniqueID("vtiger_field").",'vendor_no','vtiger_vendor',1,'4','vendor_no','Vendor No',1,0,0,100,2,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_vendor ADD COLUMN vendor_no varchar(100) not null");

$blockid = getBlockId(19,'LBL_PRICEBOOK_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (19,".$adb->getUniqueID("vtiger_field").",'pricebook_no','vtiger_pricebook',1,'4','pricebook_no','PriceBook No',1,0,0,100,3,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_pricebook ADD COLUMN pricebook_no varchar(100) not null");

$blockid = getBlockId(22,'LBL_SO_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (22,".$adb->getUniqueID("vtiger_field").",'salesorder_no','vtiger_salesorder',1,'4','salesorder_no','SalesOrder No',1,0,0,100,3,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_salesorder ADD COLUMN salesorder_no varchar(100) not null");

$blockid = getBlockId(21,'LBL_PO_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (21,".$adb->getUniqueID("vtiger_field").",'purchaseorder_no','vtiger_purchaseorder',1,'4','purchaseorder_no','PurchaseOrder No',1,0,0,100,2,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_purchaseorder ADD COLUMN purchaseorder_no varchar(100) not null");

$blockid = getBlockId(20,'LBL_QUOTE_INFORMATION');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (20,".$adb->getUniqueID("vtiger_field").",'quote_no','vtiger_quotes',1,'4','quote_no','Quote No',1,0,0,100,3,$blockid,1,'V~O',1,null,'BAS',0)");
ExecuteQuery("ALTER TABLE vtiger_quotes ADD COLUMN quote_no varchar(100) not null");

     
$field_result = $adb->query("select tabid, fieldid from vtiger_field where uitype='4'");
$num_fields = $adb->num_rows($field_result);
for($i = 0; $i<$num_fields; $i++)
{
	$tab_id = $adb->query_result($field_result,$i,'tabid');
	$fld_id = $adb->query_result($field_result,$i,'fieldid');
	addFieldSecurity($tab_id, $fld_id, false);
}

ExecuteQuery("update vtiger_field set uitype = '4' where tabid = 23 and columnname = 'invoice_no' ");
ExecuteQuery("update vtiger_field set typeofdata = 'V~O' where tabid = 23 and columnname = 'invoice_no' ");

// ADD COLUMN TO SPECIFIED MODULE CUSTOM VIEW / FILTER.
function custom_addCustomFilterColumn($module, $filtername, $tablename, $columnname, $fieldname, $displayinfo, $columnindex=0) {
	global $adb;

	$result = $adb->query("SELECT * FROM vtiger_customview WHERE entitytype = '".$adb->sql_escape_string($module)."' AND viewname = '".$adb->sql_escape_string($filtername)."'");
	if($adb->num_rows($result) > 0) {
		$cvid = $adb->query_result($result, 0, 'cvid');
	}
	
	if($cvid == null) return;

	// (cvid, columnindex) is combined key so we have to update columnindex suitably
	ExecuteQuery("UPDATE vtiger_cvcolumnlist set columnindex=columnindex+1 WHERE cvid = $cvid AND columnindex >= $columnindex ORDER BY columnindex DESC");

	$cvcolumnname_value = $tablename . ":" . $columnname . ":" . $fieldname . ":" . $displayinfo;
	ExecuteQuery("INSERT INTO vtiger_cvcolumnlist(cvid, columnindex, columnname) VALUES ($cvid, $columnindex, '$cvcolumnname_value')");
}

// REMOVE SPECIFIED COLUMN FROM MODULE FILTER.
function custom_removeCustomFilterColumn($module, $filtername, $tablename, $columnname, $fieldname, $displayinfo) {
	global $adb;

	$result = $adb->query("SELECT * FROM vtiger_customview WHERE entitytype = '".$adb->sql_escape_string($module)."' AND viewname = '".$adb->sql_escape_string($filtername)."'");
	if($adb->num_rows($result) > 0) {
		$cvid = $adb->query_result($result, 0, 'cvid');
	}

	if($cvid == null) return;

	$cvcolumnname_value = $tablename . ":" . $columnname . ":" . $fieldname . ":" . $displayinfo;
	ExecuteQuery("DELETE FROM vtiger_cvcolumnlist where cvid = $cvid and columnname like '$cvcolumnname_value:%' ");
}

custom_addCustomFilterColumn('Leads',      'All', 'vtiger_leaddetails',    'lead_no',      'lead_no',      'Leads_Lead_No:V');
custom_addCustomFilterColumn('Accounts',   'All', 'vtiger_account',        'account_no',   'account_no',   'Accounts_Account_No:V');
custom_addCustomFilterColumn('Campaigns',  'All', 'vtiger_campaign',       'campaign_no',  'campaign_no',  'Campaigns_Campaign_No:V');
custom_addCustomFilterColumn('Contacts',   'All', 'vtiger_contactdetails', 'contact_no',   'contact_no',   'Contacts_Contact_Id:V');
custom_addCustomFilterColumn('Potentials', 'All', 'vtiger_potential',      'potential_no', 'potential_no', 'Potentials_Potential_No:V');

custom_removeCustomFilterColumn('HelpDesk', 'All', 'vtiger_crmentity',      'crmid',     '',          'HelpDesk_Ticket_ID');
custom_addCustomFilterColumn('HelpDesk',    'All', 'vtiger_troubletickets', 'ticket_no', 'ticket_no', 'HelpDesk_Ticket_No:V');

custom_removeCustomFilterColumn('Quotes', 'All', 'vtiger_crmentity', 'crmid',    '',         'Quotes_Quote_No');
custom_addCustomFilterColumn('Quotes',    'All', 'vtiger_quotes',    'quote_no', 'quote_no', 'Quotes_Quote_No:V');

custom_removeCustomFilterColumn('SalesOrder', 'All', 'vtiger_crmentity',  'crmid','','SalesOrder_Order_No');
custom_addCustomFilterColumn('SalesOrder',    'All', 'vtiger_salesorder', 'salesorder_no', 'salesorder_no', 'SalesOrder_SalesOrder_No:V');

custom_removeCustomFilterColumn('PurchaseOrder', 'All', 'vtiger_crmentity', 'crmid', '', 'PurchaseOrder_Order_No');
custom_addCustomFilterColumn('PurchaseOrder',    'All', 'vtiger_purchaseorder', 'purchaseorder_no', 'purchaseorder_no', 'PurchaseOrder_PurchaseOrder_No:V');

custom_addCustomFilterColumn('Products',   'All', 'vtiger_products',  'product_no',   'product_no',   'Products_Product_No:V');
custom_addCustomFilterColumn('Vendors',    'All', 'vtiger_vendor',    'vendor_no',    'vendor_no',    'Vendors_Vendor_No:V');
custom_addCustomFilterColumn('PriceBooks', 'All', 'vtiger_pricebook', 'pricebook_no', 'pricebook_no', 'PriceBooks_PriceBook_No:V');

custom_removeCustomFilterColumn('Faq', 'All', 'vtiger_faq', 'id',     '',       'Faq_FAQ_Id');
custom_addCustomFilterColumn('Faq',    'All', 'vtiger_faq', 'faq_no', 'faq_no', 'Faq_Faq_No:V');

custom_addCustomFilterColumn('Documents',  'All', 'vtiger_notes', 'note_no', 'note_no', 'Notes_Note_No:V');
// Sequence number customization ends

/*asterisk related changes*/
$sql = "drop table if exists vtiger_asteriskextensions";
ExecuteQuery($sql);
$sql = "create table vtiger_asteriskextensions (userid int(11), asterisk_extension varchar(50), use_asterisk varchar(3)) ENGINE=InnoDB DEFAULT CHARSET=utf8";
ExecuteQuery($sql);
$sql = "drop table if exists vtiger_asterisk";
ExecuteQuery($sql);
$sql = "create table vtiger_asterisk (server varchar(30), port varchar(30), username varchar(50), password varchar(50), version varchar(50)) ENGINE=InnoDB DEFAULT CHARSET=utf8";
ExecuteQuery($sql);
$sql = "drop table if exists vtiger_asteriskincomingcalls";
ExecuteQuery($sql);
$sql = "create table vtiger_asteriskincomingcalls (from_number varchar(50) not null, from_name varchar(50) not null, to_number varchar(50) not null, callertype varchar(30), flag int(19), timer int(19)) ENGINE=InnoDB DEFAULT CHARSET=utf8";
ExecuteQuery($sql);
$sql = "drop table if exists vtiger_asteriskoutgoingcalls";
ExecuteQuery($sql);
$sql = "create table vtiger_asteriskoutgoingcalls (userid int(11) not null, from_number varchar(30) not null, to_number varchar(30) not null) ENGINE=InnoDB DEFAULT CHARSET=utf8";
ExecuteQuery($sql);

/*asterisk changes end here*/
/* Updated phone field uitype */
ExecuteQuery("update vtiger_field set uitype='11' where fieldname='mobile' and tabid=".getTabid('Leads'));
ExecuteQuery("update vtiger_field set uitype='11' where fieldname='mobile' and tabid=".getTabid('Contacts'));
ExecuteQuery("update vtiger_field set uitype='11' where fieldname='fax' and tabid=".getTabid('Leads'));
ExecuteQuery("update vtiger_field set uitype='11' where fieldname='fax' and tabid=".getTabid('Contacts'));
ExecuteQuery("update vtiger_field set uitype='11' where fieldname='fax' and tabid=".getTabid('Accounts'));

/* Support to Configure the functionality of Updating Inventory Stock for Invoice/SalesOrder */
ExecuteQuery("ALTER TABLE vtiger_inventoryproductrel ADD COLUMN incrementondel int(11) not null default '0'");
$invoiceids = $adb->pquery("SELECT invoiceid from vtiger_invoice",array());
$noOfRows = $adb->num_rows($invoiceids);
for($i=0;$i<$noOfRows;$i++){
	$adb->pquery("UPDATE vtiger_inventoryproductrel SET incrementondel = 1 WHERE id=?",array($adb->query_result($invoiceids,$i,"invoiceid")));
}

$emm->addEntityMethod("SalesOrder","UpdateInventory","include/InventoryHandler.php","handleInventoryProductRel");//Adding EntityMethod for Updating Products data after creating SalesOrder
$emm->addEntityMethod("Invoice","UpdateInventory","include/InventoryHandler.php","handleInventoryProductRel");//Adding EntityMethod for Updating Products data after creating Invoice

$vtWorkFlow = new VTWorkflowManager($adb);
$invWorkFlow = $vtWorkFlow->newWorkFlow("Invoice");
$invWorkFlow->test = '[{"fieldname":"subject","operation":"does not contain","value":"`!`"}]';
$invWorkFlow->description = "UpdateInventoryProducts On Every Save";
$vtWorkFlow->save($invWorkFlow);

$tm = new VTTaskManager($adb);
$task = $tm->createTask('VTEntityMethodTask', $invWorkFlow->id);
$task->active=true;
$task->methodName = "UpdateInventory";
$tm->saveTask($task);

/* Support to track if a module is of CrmEntity type or not */
ExecuteQuery("ALTER TABLE vtiger_tab ADD COLUMN isentitytype INT NOT NULL DEFAULT 1");
ExecuteQuery("UPDATE vtiger_tab SET isentitytype=0 WHERE name IN ('Home','Dashboard','Rss','Reports','Portal','Users','Recyclebin')");

/* Support for different languages to be stored in database instead of config file - Vtlib */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_language(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50), " .
		"prefix VARCHAR(10), label VARCHAR(30), lastupdated DATETIME, sequence INT, isdefault INT(1), active INT(1)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
		
/* Register default language English. This will automatically register all the other langauges from config file */
require_once('vtlib/Vtiger/Language.php');
$vtlanguage = new Vtiger_Language();
$vtlanguage->register('en_us','US English','English',true,true,true);

/* To store relationship between the modules in a common table */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_crmentityrel (crmid int(11) NOT NULL, module varchar(100) NOT NULL, relcrmid int(11) NOT NULL, relmodule varchar(100) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

/* To store the field to module relationship for uitype 10 */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_fieldmodulerel (fieldid int(11) NOT NULL, module varchar(100) NOT NULL, relmodule varchar(100) NOT NULL,
  					status varchar(10) default NULL, sequence int(11) default NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

/* Making users and groups depends on vtiger_users_seq */
$max_grp_id = $adb->query("SELECT MAX(groupid) as maxid from vtiger_groups");
$maxid = $adb->query_result($max_grp_id,0,"maxid");

$user_result = $adb->query("select max(id) as userid from vtiger_users");
$inc_num = $adb->query_result($user_result,0,"userid");

$adb->getUniqueId("vtiger_users");//Creates vtiger_users_seq table if not exists.
$seq_id = $inc_num+$maxid;
$adb->pquery("UPDATE vtiger_users_seq SET id=?",array($seq_id));

$tab_info = array(
	"vtiger_group2grouprel"=>array("fk_2_vtiger_group2grouprel","(groupid)","vtiger_groups(groupid)"),
	"vtiger_users2group"=>array("fk_1_vtiger_users2group","(groupid)","vtiger_groups(groupid)"),
	"vtiger_group2role"=>array("fk_1_vtiger_group2role","(groupid)","vtiger_groups(groupid)"),
	"vtiger_group2rs"=>array("fk_1_vtiger_group2rs","(groupid)","vtiger_groups(groupid)"),
	"vtiger_datashare_grp2grp"=>array("fk_2_vtiger_datashare_grp2grp","(share_groupid)","vtiger_groups(groupid)"),
	"vtiger_datashare_grp2grp"=>array("fk_3_vtiger_datashare_grp2grp","(to_groupid)","vtiger_groups(groupid)"),
	"vtiger_datashare_grp2role"=>array("fk_2_vtiger_datashare_grp2role","(share_groupid)","vtiger_groups(groupid)"),
	"vtiger_datashare_role2group"=>array("fk_2_vtiger_datashare_role2group","(to_groupid)","vtiger_groups(groupid)"),
	"vtiger_datashare_grp2rs"=>array("fk_2_vtiger_datashare_grp2rs","(share_groupid)","vtiger_groups(groupid)"),
	"vtiger_datashare_rs2grp"=>array("fk_2_vtiger_datashare_rs2grp","(to_groupid)","vtiger_groups(groupid)"),
	"vtiger_tmp_read_group_sharing_per"=>array("fk_1_vtiger_tmp_read_group_sharing_per","(sharedgroupid)","vtiger_groups(groupid)"),
	"vtiger_tmp_write_group_sharing_per"=>array("fk_1_vtiger_tmp_write_group_sharing_per","(sharedgroupid)","vtiger_groups(groupid)"),
);
$drop_key_array = array("vtiger_group2grouprel","vtiger_datashare_grp2grp");

foreach($tab_info as $table=>$value){
	//Update constraints for vtiger_group2grouprel table
	if(in_array($table,$drop_key_array)){
		ExecuteQuery("ALTER TABLE $table DROP FOREIGN KEY ".$value[0]);
	}
	ExecuteQuery("ALTER TABLE $table ADD CONSTRAINT ".$value[0]." FOREIGN KEY ".$value[1]." REFERENCES ".$value[2]." ON DELETE CASCADE ON UPDATE CASCADE");
}

$grp_result = $adb->query("select groupid from vtiger_groups ORDER BY groupid ASC");
$num_grps = $adb->num_rows($grp_result);

for($i=$num_grps-1; $i>=0; $i--) {
	$oldId = $adb->query_result($grp_result,$i,"groupid");
	$newId = $adb->getUniqueId("vtiger_users");
	
	ExecuteQuery("UPDATE vtiger_groups set groupid = $newId where groupid = $oldId");
	ExecuteQuery("UPDATE vtiger_users2group set groupid = $newId where groupid = $oldId");
	ExecuteQuery("UPDATE vtiger_group2grouprel set groupid = $newId where groupid = $oldId");
	ExecuteQuery("UPDATE vtiger_group2role set groupid = $newId where groupid = $oldId");
	ExecuteQuery("UPDATE vtiger_group2rs set groupid = $newId where groupid = $oldId");
	ExecuteQuery("UPDATE vtiger_datashare_grp2grp set share_groupid = $newId where share_groupid = $oldId");
	ExecuteQuery("UPDATE vtiger_datashare_grp2grp set to_groupid = $newId where to_groupid = $oldId");
	ExecuteQuery("UPDATE vtiger_datashare_grp2role set share_groupid = $newId where share_groupid = $oldId");
	ExecuteQuery("UPDATE vtiger_datashare_grp2rs set share_groupid = $newId where share_groupid = $oldId");
	ExecuteQuery("UPDATE vtiger_datashare_role2group set to_groupid = $newId where to_groupid = $oldId");
	ExecuteQuery("UPDATE vtiger_datashare_rs2grp set to_groupid = $newId where to_groupid = $oldId");
	ExecuteQuery("UPDATE vtiger_tmp_read_group_sharing_per set sharedgroupid = $newId where sharedgroupid = $oldId");
	ExecuteQuery("UPDATE vtiger_tmp_write_group_sharing_per set sharedgroupid = $newId where sharedgroupid = $oldId");
}

$sql_result = $adb->query("select crmid,setype from vtiger_crmentity where smownerid=0 order by setype");
$num_rows = $adb->num_rows($sql_result);
$groupTables_array = array (
							'Leads'=>array ('vtiger_leadgrouprelation','leadid'),
							'Accounts'=>array ('vtiger_accountgrouprelation','accountid'),
							'Contacts'=>array ('vtiger_contactgrouprelation','contactid'),
							'Potentials'=>array ('vtiger_potentialgrouprelation','potentialid'),
							'Quotes'=>array ('vtiger_quotegrouprelation','quoteid'),
							'SalesOrder'=>array ('vtiger_sogrouprelation','salesorderid'),
							'Invoice'=>array ('vtiger_invoicegrouprelation','invoiceid'),
							'PurchaseOrder'=>array ('vtiger_pogrouprelation','purchaseorderid'),
							'HelpDesk'=>array ('vtiger_ticketgrouprelation','ticketid'),
							'Campaigns'=>array ('vtiger_campaigngrouprelation','campaignid'),
							'Calendar'=>array ('vtiger_activitygrouprelation','activityid')
                            );
                            
foreach($groupTables_array as $module=>$index){
	$modulereltable = $index[0];
	$modulerelindex = $index[1];
	ExecuteQuery("update vtiger_crmentity INNER JOIN {$modulereltable} ON vtiger_crmentity.crmid = {$modulereltable}.{$modulerelindex} INNER JOIN vtiger_groups ON vtiger_groups.groupname = {$modulereltable}.groupname set smownerid = vtiger_groups.groupid");
	ExecuteQuery("UPDATE vtiger_crmentity SET smownerid=1 WHERE smownerid=0 AND setype='{$module}'");
}
// user-group ends

/* Product Comment was Missing in Inventory PDF's - Fixed this by eliminating column product_description from vtiger_products
 * and referring to description column of vtiger_crmentity wherever required */
ExecuteQuery("UPDATE vtiger_crmentity, vtiger_products SET vtiger_crmentity.description=vtiger_products.product_description
					WHERE vtiger_products.productid = vtiger_crmentity.crmid");		
ExecuteQuery("ALTER TABLE vtiger_products DROP COLUMN product_description");
ExecuteQuery("UPDATE vtiger_field set fieldname='description', columnname='description', tablename='vtiger_crmentity'
					WHERE tablename='vtiger_products' AND fieldname='product_description'");

/* Remove Products from all the Main tabs except for Inventory */
$productTabId = getTabid('Products');

$inventoryTabRes = $adb->query("SELECT parenttabid FROM vtiger_parenttab WHERE parenttab_label='Inventory'");
if($adb->num_rows($inventoryTabRes)>0){
	$inventoryTabId = $adb->query_result($inventoryTabRes, 0, 'parenttabid');
	ExecuteQuery("DELETE FROM vtiger_parenttabrel WHERE tabid=$productTabId AND parenttabid != $inventoryTabId");
}

$adb->query("ALTER TABLE vtiger_producttaxrel DROP FOREIGN KEY fk_1_vtiger_producttaxrel");
$adb->query("ALTER TABLE vtiger_pricebookproductrel DROP FOREIGN KEY fk_2_vtiger_pricebookproductrel");

/* Vtlib Changes - Table added to store different types of links */
/*ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_links (linkid INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    		tabid INT, linktype VARCHAR(20), linklabel VARCHAR(30), linkurl VARCHAR(255), linkicon VARCHAR(100), sequence INT) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("CREATE INDEX link_tabidtype_idx ON vtiger_links(tabid,linktype)");*/

/* Column added to vtiger_tab to track the version of the module */
if(!in_array('version', $adb->getColumnNames('vtiger_tab'))) {
	ExecuteQuery("ALTER TABLE vtiger_tab ADD COLUMN version VARCHAR(10)");
}

/*adding the notebook to vtiger*/
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_notebook_contents (userid int(19) not null, notebookid int(19), contents text) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
/*notbook changes end*/

/* Move Settings Page Information to Database */
// This function moves the settings page to database
function moveSettingsToDatabase($adb){
	$adb->query("drop table if exists vtiger_settings_blocks");
	$adb->query("drop table if exists vtiger_settings_field");
	$adb->query("CREATE TABLE IF NOT EXISTS vtiger_settings_blocks (blockid int(19), label varchar(250), sequence int(19), primary key pk_vtiger_settings_blocks (blockid)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	$adb->query("CREATE TABLE IF NOT EXISTS vtiger_settings_field (fieldid int(19), blockid int(19), name varchar(250), iconpath text, description text, linkto text, sequence int(19), active int(19) default 0,foreign key fk_vtiger_settings_fields (blockid) references vtiger_settings_blocks(blockid) on delete cascade) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	
	//icons for all fields
	$icons = array("ico-users.gif",
				"ico-roles.gif",
				"ico-profile.gif",
				"ico-groups.gif",
				"shareaccess.gif",
				"orgshar.gif",
				"audit.gif",
				"set-IcoLoginHistory.gif",
				"vtlib_modmng.gif",
				"picklist.gif",
				"ViewTemplate.gif",
				"mailmarge.gif",
				"notification.gif",
				"inventory.gif",
				"company.gif",
				"ogmailserver.gif",
				"backupserver.gif",
				"assign.gif",
				"currency.gif",
				"taxConfiguration.gif",
				"system.gif",
				"proxy.gif",
				"announ.gif",
				"set-IcoTwoTabConfig.gif",
				"terms.gif",
				"settingsInvNumber.gif",
				"mailScanner.gif",
				"settingsWorkflow.png");

	//labels for blocks
	$blocks = array('LBL_MODULE_MANAGER',
				'LBL_USER_MANAGEMENT',
				'LBL_STUDIO', 
				'LBL_COMMUNICATION_TEMPLATES', 
				'LBL_OTHER_SETTINGS');

	//field names
	$names = array('LBL_USERS',
				'LBL_ROLES',
				'LBL_PROFILES',
				'USERGROUPLIST',
				'LBL_SHARING_ACCESS',
				'LBL_FIELDS_ACCESS',
				'LBL_AUDIT_TRAIL',
				'LBL_LOGIN_HISTORY_DETAILS',
				'VTLIB_LBL_MODULE_MANAGER',
				'LBL_PICKLIST_EDITOR',
				'EMAILTEMPLATES',
				'LBL_MAIL_MERGE',
				'NOTIFICATIONSCHEDULERS',
				'INVENTORYNOTIFICATION',
				'LBL_COMPANY_DETAILS',
				'LBL_MAIL_SERVER_SETTINGS',
				'LBL_BACKUP_SERVER_SETTINGS',
				'LBL_ASSIGN_MODULE_OWNERS',
				'LBL_CURRENCY_SETTINGS',
				'LBL_TAX_SETTINGS',
				'LBL_SYSTEM_INFO',
				'LBL_PROXY_SETTINGS',
				'LBL_ANNOUNCEMENT',
				'LBL_DEFAULT_MODULE_VIEW',
				'INVENTORYTERMSANDCONDITIONS',
				'LBL_CUSTOMIZE_MODENT_NUMBER',
				'LBL_MAIL_SCANNER',
				'LBL_LIST_WORKFLOWS',);

	$name_blocks = array('LBL_USERS'=>'LBL_USER_MANAGEMENT',
				'LBL_ROLES'=>'LBL_USER_MANAGEMENT',
				'LBL_PROFILES'=>'LBL_USER_MANAGEMENT',
				'USERGROUPLIST'=>'LBL_USER_MANAGEMENT',
				'LBL_SHARING_ACCESS'=>'LBL_USER_MANAGEMENT',
				'LBL_FIELDS_ACCESS'=>'LBL_USER_MANAGEMENT',
				'LBL_AUDIT_TRAIL'=>'LBL_USER_MANAGEMENT',
				'LBL_LOGIN_HISTORY_DETAILS'=>'LBL_USER_MANAGEMENT',
				'VTLIB_LBL_MODULE_MANAGER'=>'LBL_STUDIO',
				'LBL_PICKLIST_EDITOR'=>'LBL_STUDIO',
				'EMAILTEMPLATES'=>'LBL_COMMUNICATION_TEMPLATES',
				'LBL_MAIL_MERGE'=>'LBL_COMMUNICATION_TEMPLATES',
				'NOTIFICATIONSCHEDULERS'=>'LBL_COMMUNICATION_TEMPLATES',
				'INVENTORYNOTIFICATION'=>'LBL_COMMUNICATION_TEMPLATES',
				'LBL_COMPANY_DETAILS'=>'LBL_COMMUNICATION_TEMPLATES',
				'LBL_MAIL_SERVER_SETTINGS'=>'LBL_OTHER_SETTINGS',
				'LBL_BACKUP_SERVER_SETTINGS'=>'LBL_OTHER_SETTINGS',
				'LBL_ASSIGN_MODULE_OWNERS'=>'LBL_OTHER_SETTINGS',
				'LBL_CURRENCY_SETTINGS'=>'LBL_OTHER_SETTINGS',
				'LBL_TAX_SETTINGS'=>'LBL_OTHER_SETTINGS',
				'LBL_SYSTEM_INFO'=>'LBL_OTHER_SETTINGS',
				'LBL_PROXY_SETTINGS'=>'LBL_OTHER_SETTINGS',
				'LBL_ANNOUNCEMENT'=>'LBL_OTHER_SETTINGS',
				'LBL_DEFAULT_MODULE_VIEW'=>'LBL_OTHER_SETTINGS',
				'INVENTORYTERMSANDCONDITIONS'=>'LBL_OTHER_SETTINGS',
				'LBL_CUSTOMIZE_MODENT_NUMBER'=>'LBL_OTHER_SETTINGS',
				'LBL_MAIL_SCANNER'=>'LBL_OTHER_SETTINGS',
				'LBL_LIST_WORKFLOWS'=>'LBL_OTHER_SETTINGS',);

	//description for fields
	$description = array('LBL_USER_DESCRIPTION', 
					'LBL_ROLE_DESCRIPTION', 
					'LBL_PROFILE_DESCRIPTION', 
					'LBL_GROUP_DESCRIPTION', 
					'LBL_SHARING_ACCESS_DESCRIPTION', 
					'LBL_SHARING_FIELDS_DESCRIPTION', 
					'LBL_AUDIT_DESCRIPTION', 
					'LBL_LOGIN_HISTORY_DESCRIPTION', 
					'VTLIB_LBL_MODULE_MANAGER_DESCRIPTION', 
					'LBL_PICKLIST_DESCRIPTION', 
					'LBL_EMAIL_TEMPLATE_DESCRIPTION', 
					'LBL_MAIL_MERGE_DESCRIPTION', 
					'LBL_NOTIF_SCHED_DESCRIPTION', 
					'LBL_INV_NOTIF_DESCRIPTION', 
					'LBL_COMPANY_DESCRIPTION', 
					'LBL_MAIL_SERVER_DESCRIPTION', 
					'LBL_BACKUP_SERVER_DESCRIPTION', 
					'LBL_MODULE_OWNERS_DESCRIPTION',
					'LBL_CURRENCY_DESCRIPTION', 
					'LBL_TAX_DESCRIPTION', 
					'LBL_SYSTEM_DESCRIPTION', 
					'LBL_PROXY_DESCRIPTION', 
					'LBL_ANNOUNCEMENT_DESCRIPTION', 
					'LBL_DEFAULT_MODULE_VIEW_DESC', 
					'LBL_INV_TANDC_DESCRIPTION', 
					'LBL_CUSTOMIZE_MODENT_NUMBER_DESCRIPTION', 
					'LBL_MAIL_SCANNER_DESCRIPTION', 
					'LBL_LIST_WORKFLOWS_DESCRIPTION');

	
	$links = array('index.php?module=Administration&action=index&parenttab=Settings',
				'index.php?module=Settings&action=listroles&parenttab=Settings',
				'index.php?module=Settings&action=ListProfiles&parenttab=Settings',
				'index.php?module=Settings&action=listgroups&parenttab=Settings',
				'index.php?module=Settings&action=OrgSharingDetailView&parenttab=Settings',
				'index.php?module=Settings&action=DefaultFieldPermissions&parenttab=Settings',
				'index.php?module=Settings&action=AuditTrailList&parenttab=Settings',
				'index.php?module=Settings&action=ListLoginHistory&parenttab=Settings',
				'index.php?module=Settings&action=ModuleManager&parenttab=Settings',
				'index.php?module=PickList&action=PickList&parenttab=Settings',
				'index.php?module=Settings&action=listemailtemplates&parenttab=Settings',
				'index.php?module=Settings&action=listwordtemplates&parenttab=Settings',
				'index.php?module=Settings&action=listnotificationschedulers&parenttab=Settings',
				'index.php?module=Settings&action=listinventorynotifications&parenttab=Settings',
				'index.php?module=Settings&action=OrganizationConfig&parenttab=Settings',
				'index.php?module=Settings&action=EmailConfig&parenttab=Settings',
				'index.php?module=Settings&action=BackupServerConfig&parenttab=Settings',
				'index.php?module=Settings&action=ListModuleOwners&parenttab=Settings',
				'index.php?module=Settings&action=CurrencyListView&parenttab=Settings',
				'index.php?module=Settings&action=TaxConfig&parenttab=Settings',
				'index.php?module=System&action=listsysconfig&parenttab=Settings',
				'index.php?module=Settings&action=ProxyServerConfig&parenttab=Settings',
				'index.php?module=Settings&action=Announcements&parenttab=Settings',
				'index.php?module=Settings&action=DefModuleView&parenttab=Settings',
				'index.php?module=Settings&action=OrganizationTermsandConditions&parenttab=Settings',
				'index.php?module=Settings&action=CustomModEntityNo&parenttab=Settings',
				'index.php?module=Settings&action=MailScanner&parenttab=Settings',
				'index.php?module=com_vtiger_workflow&action=workflowlist&parenttab=Settings',);

	//insert settings blocks
	$count = count($blocks);
	for($i=0; $i<$count; $i++){
		$adb->query("insert into vtiger_settings_blocks values (".$adb->getUniqueID('vtiger_settings_blocks').", '$blocks[$i]', $i+1)");
	}
	
	$count = count($icons);
	//insert settings fields
	for($i=0, $seq=1; $i<$count; $i++, $seq++){
		if($i==8 || $i==12 || $i==18) {
			$seq = 1;
		}	
		$adb->query("insert into vtiger_settings_field (fieldid, blockid, name, iconpath, description, linkto, sequence) values (".$adb->getUniqueID('vtiger_settings_field').", ".getSettingsBlockId($name_blocks[$names[$i]]).", '$names[$i]', '$icons[$i]', '$description[$i]', '$links[$i]', $seq)");
	}
	//hide the system details tab for now
	$adb->query("update vtiger_settings_field set active=1 where name='LBL_SYSTEM_INFO'");
}
//move settings page to database starts
moveSettingsToDatabase($adb);
//settings page to database ends
// END


/* Email status tracking*/
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_email_access(crmid INT, mailid INT, accessdate DATE, accesstime TIME) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_email_track(crmid INT, mailid INT, access_count INT, primary key(crmid, mailid)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$fieldid = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) VALUES ('10',".$fieldid.", 'access_count', 'vtiger_email_track', '1', '25', 'access_count', 'Access Count', '1', '0', '0', '100', '6', '21', '3', 'V~O', '1', NULL, 'BAS', 0)");
addFieldSecurity(10, $fieldid, 'false');
// END

/* Reports Revamped */
ExecuteQuery("ALTER TABLE vtiger_report ADD COLUMN owner int(11) NOT NULL");
ExecuteQuery("UPDATE vtiger_field INNER JOIN vtiger_field as vtiger_field1 on vtiger_field1.tabid=vtiger_field.tabid SET vtiger_field.block = vtiger_field1.block WHERE vtiger_field.fieldname='faq_answer' and vtiger_field1.fieldname='question' and vtiger_field.tabid=15");
ExecuteQuery("ALTER TABLE vtiger_report ADD COLUMN sharingtype varchar(200) NOT NULL DEFAULT 'Private'");
ExecuteQuery("UPDATE vtiger_report SET sharingtype='Public', owner=1");
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_reportsharing(reportid int(19) not null,shareid int(19) not null,setype varchar(200) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_reportfilters(filterid int(11) not null,name varchar(200) not null) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("INSERT INTO vtiger_reportfilters values(1,'Private')");
ExecuteQuery("INSERT INTO vtiger_reportfilters values(2,'Public')");
ExecuteQuery("INSERT INTO vtiger_reportfilters values(3,'Shared')");

/* Account Hierarchy */
populateLinks();

/* Product Bundles Revamping */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_inventorysubproductrel(id int(19) NOT NULL, sequence_no INT(10) NOT NULL, productid INT(19) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

/* Support for Calendar Custom Fields */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_activitycf(activityid INT default '0' primary key) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("insert into vtiger_blocks values (".$adb->getUniqueID('vtiger_blocks').",9,'LBL_CUSTOM_INFORMATION',3,0,0,0,0,0,1)");
ExecuteQuery("insert into vtiger_blocks values (".$adb->getUniqueID('vtiger_blocks').",16,'LBL_CUSTOM_INFORMATION',4,0,0,0,0,0,1)");
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable) values (16,".$adb->getUniqueID('vtiger_field').",'contactid','vtiger_cntactivityrel',1,'57','contact_id','Contact Name',1,0,0,100,1,19,1,'I~O',1,null,'BAS',1)");

/* Added new field Help Info for vtiger_field table */
if(!in_array('helpinfo', $adb->getColumnNames('vtiger_field'))) {
	ExecuteQuery("ALTER TABLE vtiger_field ADD COLUMN helpinfo TEXT");
}

/* Add Services and Service Contracts Module */

// Added Hours and Days fields for HelpDesk module.
$helpDeskTabid = getTabid('HelpDesk');
$ttBlockid = getBlockId($helpDeskTabid,'LBL_TICKET_INFORMATION');

$tt_field1 = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) values ($helpDeskTabid,$tt_field1,'hours','vtiger_troubletickets',1,'1','hours','Hours',1,0,0,100,9,$ttBlockid,1,'I~O',1,null,'BAS',1,
		'This gives the estimated hours for the Ticket<br> When the same ticket is added to a Service Contract, based on the Tracking Unit of the Service Contract, Used units is updated whenever a ticket is Closed.')");
addFieldSecurity($helpDeskTabid, $tt_field1);
ExecuteQuery("ALTER TABLE vtiger_troubletickets ADD COLUMN hours VARCHAR(200)");

$tt_field2 = $adb->getUniqueID('vtiger_field');
ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) values ($helpDeskTabid,$tt_field2,'days','vtiger_troubletickets',1,'1','days','Days',1,0,0,100,10,$ttBlockid,1,'I~O',1,null,'BAS',1,
		'This gives the estimated days for the Ticket<br> When the same ticket is added to a Service Contract, based on the Tracking Unit of the Service Contract, Used units is updated whenever a ticket is Closed.')");
addFieldSecurity($helpDeskTabid, $tt_field2);
ExecuteQuery("ALTER TABLE vtiger_troubletickets ADD COLUMN days VARCHAR(200)");
// Adding fields ends here

//layout editor changes
$helpdesktabid = getTabid('HelpDesk');
$invoicetabid = getTabid('Invoice');
$salesordertabid = getTabid('SalesOrder');
$purchaseorder = getTabid('PurchaseOrder');
$faqtabid = getTabid('Faq');
$quotes = getTabid('Quotes');
$contacttabid = getTabid('Contacts');
$campaigntabid = getTabid('Campaigns');
$leadtabid = getTabid('Leads'); 
$potentialtabid = getTabid('Potentials');
$pricebooktabid = getTabid('PriceBooks');
$producttabid = getTabid('Products');
$vendortabid= getTabid('Vendors');
$accounttabid = getTabid('Accounts');

ExecuteQuery("alter table vtiger_blocks add column iscustom int default 0");

ExecuteQuery("update vtiger_field set presence=2");
ExecuteQuery("update vtiger_field set presence=0 where quickcreate=0 or fieldname='createdtime' or fieldname='modifiedtime' or typeofdata like '%M';");
ExecuteQuery("update vtiger_field set presence=0 where fieldname in ('update_log','parent_id','comments','solution') and tabid=$helpdesktabid");
ExecuteQuery("update vtiger_field set presence=0 where fieldname='potentialname'");
ExecuteQuery("update vtiger_field set presence=0 where fieldname = 'comments' and tabid = $faqtabid");
ExecuteQuery("update vtiger_field set typeofdata='I~M',presence=2 where fieldname='account_id' and tabid =$potentialtabid ");
ExecuteQuery("update vtiger_field set typeofdata='I~M',presence=2 where fieldname='account_id' and tabid =$quotes");
ExecuteQuery("update vtiger_field set typeofdata='I~M',presence=2 where fieldname='account_id' and tabid =$salesordertabid");
ExecuteQuery("update vtiger_field set typeofdata='I~M',presence=2 where fieldname='account_id' and tabid =$invoicetabid");

ExecuteQuery("update vtiger_field set presence=0,quickcreate=2 where fieldname='account_id' and tabid = $contacttabid");
ExecuteQuery("update vtiger_field set presence=0,quickcreate=0 where fieldname='taxclass' and tabid=$producttabid");
ExecuteQuery("update vtiger_field set presence = 0,quickcreate=3 where block = $new_block_id "); //for recurring invoice block the fields are always active
ExecuteQuery("update vtiger_field set quickcreate=3 where fieldname='createdtime' or fieldname='modifiedtime'");
ExecuteQuery("update vtiger_field set quickcreate=3 where tabid in ($invoicetabid,$salesordertabid,$purchaseorder,$quotes,$faqtabid)");
ExecuteQuery("update vtiger_field set quickcreate=1 where fieldname in ('subject','account_id','bill_street','ship_street') and tabid= $invoicetabid");
ExecuteQuery("update vtiger_field set quickcreate=1 where fieldname in ('subject','account_id','bill_street','ship_street') and tabid= $salesordertabid");
ExecuteQuery("update vtiger_field set quickcreate=1 where fieldname in ('subject','vendor_id','bill_street','ship_street') and tabid = $purchaseorder");
ExecuteQuery("update vtiger_field set quickcreate=1 where fieldname in ('subject','account_id','bill_street','ship_street') and tabid= $quotes");

ExecuteQuery("update vtiger_field set masseditable=0 where tabid = $documents_tab_id or fieldname ='createdtime' or fieldname = 'modifiedtime'");
ExecuteQuery("update vtiger_field set typeofdata='V~O',presence=0 where uitype=4");
ExecuteQuery("update vtiger_field set quickcreate=3,masseditable=0 where uitype=4 AND displaytype=2");
ExecuteQuery("update vtiger_field set quickcreate=3 where fieldname='imagename'");
ExecuteQuery("update vtiger_field set quickcreate=2,presence=2 where fieldname in('website','phone') and tabid = $accounttabid");
ExecuteQuery("update vtiger_field set quickcreate=2,presence=2 where fieldname in('closingdate','campaigntype','expectedresponse','product_id','campaignstatus') and tabid = $campaigntabid");
ExecuteQuery("update vtiger_field set quickcreate=2,presence=2 where fieldname in('firstname','phone','email') and tabid = $contacttabid");
ExecuteQuery("update vtiger_field set quickcreate=2,presence=2 where fieldname in('firstname','phone','email','company') and tabid = $leadtabid ");
ExecuteQuery("update vtiger_field set quickcreate=2,presence=2 where fieldname in('closingdate','sales_stage','amount','account_id') and tabid = $potentialtabid");
ExecuteQuery("update vtiger_field set quickcreate=2,presence=2 where fieldname in('active') and tabid = $pricebooktabid");
ExecuteQuery("update vtiger_field set quickcreate=2,presence=2 where fieldname in('discontinued','unit_price') and tabid = $producttabid");
ExecuteQuery("update vtiger_field set quickcreate=2,presence=2 where fieldname in('phone','email') and tabid = $vendortabid");
ExecuteQuery("update vtiger_field set quickcreate=2,presence=2 where fieldname in('ticket_title','ticketstatus') and tabid = $helpdesktabid");
$faqbasicblock = getBlockId($faqtabid,'LBL_FAQ_INFORMATION');

ExecuteQuery("update vtiger_field set block = $faqbasicblock ,sequence = 7 where fieldname = 'question' and tabid = $faqtabid");
ExecuteQuery("update vtiger_field set block = $faqbasicblock ,sequence = 8 where fieldname = 'faq_answer' and tabid = $faqtabid");

/* Added support for setting a custom view as default per user basis */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_user_module_preferences (userid int, tabid int, default_cvid int, primary key(userid, tabid), CONSTRAINT fk_1_vtiger_user_module_preferences FOREIGN KEY (userid) REFERENCES vtiger_users (id) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT fk_2_vtiger_user_module_preferences FOREIGN KEY (tabid) REFERENCES vtiger_tab (tabid) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

/* home page related changes */
if(columnExists("homeorder", "vtiger_users")){
	ExecuteQuery("alter table vtiger_users drop column homeorder");
}
if(columnExists("tagcloud_view", "vtiger_users")){
	ExecuteQuery("alter table vtiger_users drop column tagcloud_view");
}
if(columnExists("defhomeview", "vtiger_users")){
	ExecuteQuery("alter table vtiger_users drop column defhomeview");
}
ExecuteQuery("create table vtiger_home_layout (userid int(19), layout int(19)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

/* Add Invoices to the related list of Contacts */
ExecuteQuery("INSERT INTO vtiger_relatedlists VALUES(".$adb->getUniqueID('vtiger_relatedlists').",". getTabid('Contacts').",".getTabid('Invoice').",'get_invoices',12,'Invoice',0, 'add')");

/* For Webservices Support */
require_once 'include/Webservices/Utils.php';
webserviceMigration();

/*adding B2C model support*/
ExecuteQuery("alter table vtiger_potential change accountid related_to int(19)");
$sql = "select fieldid from vtiger_field where tabid=? and columnname=?";
$result = $adb->pquery($sql, array(getTabid('Potentials'), 'accountid'));
$fieldid = $adb->query_result($result,0,"fieldid");
ExecuteQuery("update vtiger_field set uitype='10', typeofdata='V~M', columnname='related_to', fieldname='related_to',fieldlabel='Related To', presence=0 where fieldid=$fieldid");
ExecuteQuery("insert into vtiger_fieldmodulerel (fieldid, module, relmodule, status, sequence) values ($fieldid, 'Potentials', 'Accounts', NULL, 0), ($fieldid, 'Potentials', 'Contacts', NULL, 1)");

ExecuteQuery("update vtiger_cvcolumnlist set columnname='vtiger_potential:related_to:related_to:Potentials_Related_To:V' where columnname='vtiger_account:accountname:accountname:Potentials_Account_Name:V'");
	
// Function to populate Links
function populateLinks() {
	include_once('vtlib/Vtiger/Module.php');
	
	// Links for Accounts module
	$moduleInstance = Vtiger_Module::getInstance('Accounts');
	// Detail View Custom link
	$moduleInstance->addLink(
		'DETAILVIEWBASIC', 'LBL_ADD_NOTE', 
		'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
		'themes/images/bookMark.gif'
	);
	$moduleInstance->addLink('DETAILVIEWBASIC', 'LBL_SHOW_ACCOUNT_HIERARCHY', 'index.php?module=Accounts&action=AccountHierarchy&accountid=$RECORD$');
	
	$moduleInstance2 = Vtiger_Module::getInstance('Leads');
	$moduleInstance2->addLink(
		'DETAILVIEWBASIC', 'LBL_ADD_NOTE', 
		'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
		'themes/images/bookMark.gif'
	);
	
	$moduleInstance3 = Vtiger_Module::getInstance('Contacts');
	$moduleInstance3->addLink(
		'DETAILVIEWBASIC', 'LBL_ADD_NOTE', 
		'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
		'themes/images/bookMark.gif'
	);
}

/* For Webservices Support */
function webserviceMigration(){
	global $adb;
	require_once 'include/utils/CommonUtils.php';
	require_once 'include/Webservices/Utils.php';
	$fieldTypeInfo = array('picklist'=>array(15,16),'text'=>array(19,20,21,24),'autogenerated'=>array(3),'phone'=>array(11),
						'multipicklist'=>array(33),'url'=>array(17),'skype'=>array(85),'boolean'=>array(56,156),
						'owner'=>array(53),'file'=>array(61,28));
	$referenceMapping = array("50"=>array("Accounts"),"51"=>array("Accounts"),"57"=>array("Contacts"),"58"=>array("Campaigns"),
			"73"=>array("Accounts"),"75"=>array("Vendors"),"76"=>array("Potentials"),"78"=>array("Quotes"),
			"80"=>array("SalesOrder"),"81"=>array("Vendors"),"101"=>array("Users"),"52"=>array("Users"),
			"357"=>array("Contacts","Accounts","Leads","Users","Vendors"),"59"=>array("Products"),
			"66"=>array("Leads","Accounts","Potentials","HelpDesk"),"77"=>array("Users"),"68"=>array("Contacts","Accounts"),
			"117"=>array('Currency'),"116"=>array('Currency'),'26'=>array('DocumentFolders'),'10'=>array());
	ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_ws_fieldtype(fieldtypeid integer(19) not null auto_increment,uitype varchar(30)not null,fieldtype varchar(200) not null,PRIMARY KEY(fieldtypeid),UNIQUE KEY uitype_idx (uitype)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_ws_referencetype(fieldtypeid integer(19) not null,type varchar(25) not null,PRIMARY KEY(fieldtypeid,type),  CONSTRAINT `fk_1_vtiger_referencetype` FOREIGN KEY (`fieldtypeid`) REFERENCES `vtiger_ws_fieldtype` (`fieldtypeid`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_ws_userauthtoken(userid integer(19) not null,token varchar(25) not null,expiretime INTEGER(19),PRIMARY KEY(userid,expiretime),UNIQUE KEY userid_idx (userid)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	ExecuteQuery("alter table vtiger_users add column accesskey varchar(36);");
	$fieldid = $adb->getUniqueID("vtiger_field");
	$usersTabId = getTabid("Users");
	$user_adv_block_id = getBlockId($usersTabId,'LBL_USER_ADV_OPTIONS');
	ExecuteQuery("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) values($usersTabId,$fieldid,'accesskey','vtiger_users',1,3,'accesskey','Webservice Access Key',1,0,0,100,2,$user_adv_block_id,2,'V~O',1,null,'BAS',0,'Webservice Access Key');");
	
	foreach($referenceMapping as $uitype=>$referenceArray){
		$success = true;
		$result = $adb->pquery("insert into vtiger_ws_fieldtype(uitype,fieldtype) values(?,?)",array($uitype,"reference"));
		if(!is_object($result)){
			$success=false;
		}
		$result = $adb->pquery("select * from vtiger_ws_fieldtype where uitype=?",array($uitype));
		$rowCount = $adb->num_rows($result);
		for($i=0;$i<$rowCount;$i++){
			$fieldTypeId = $adb->query_result($result,$i,"fieldtypeid");
			foreach($referenceArray as $index=>$referenceType){
				$result = $adb->pquery("insert into vtiger_ws_referencetype(fieldtypeid,type) values(?,?)",array($fieldTypeId,$referenceType));
				if(!is_object($result)){
					echo "failed for: $referenceType, uitype: $fieldTypeId";
					$success=false;
				}
			}
		}
		if(!$success){
			echo "Migration Query Failed";
			break;
		}
	}
	
	foreach($fieldTypeInfo as $type=>$uitypes){
		foreach($uitypes as $uitype){
			$result = $adb->pquery("insert into vtiger_ws_fieldtype(uitype,fieldtype) values(?,?)",array($uitype,$type));
			if(!is_object($result)){
				"Query for fieldtype details($uitype:uitype,$type:fieldtype)";
			}
		}
	}
	
	$sql = "select * from vtiger_users";
	$updateQuery = "update vtiger_users set accesskey=? where id=?";
	$result = $adb->pquery($sql,array());
	$rowCount = $adb->num_rows($result);
	for($i=0;$i<$rowCount;$i++){
		$userId = $adb->query_result($result,$i,"id");
		$insertResult = $adb->pquery($updateQuery,array(vtws_generateRandomAccessKey(16),$userId));
		if(!is_object($insertResult)){
			echo "failed for user: ".$adb->query_result($result,$i,"user_name");
			break;
		}
	}
	ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_ws_entity(id integer(11) not null auto_increment PRIMARY
		KEY,name varchar(25) not null UNIQUE,handler_path varchar(255) NOT NULL,handler_class varchar(64) NOT NULL,
		ismodule int(3) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_ws_entity_name(entity_id integer(11) not null PRIMARY
		KEY,name_fields varchar(50),index_field varchar(50),table_name varchar(50)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	
	$names = vtws_getModuleNameList();
	$moduleHandler = array('file'=>'include/Webservices/VtigerModuleOperation.php',
				'class'=>'VtigerModuleOperation');
	foreach ($names as $tab){
		if(in_array($tab,array('Rss','Webmails','Recyclebin'))){
			continue;
		}
		$entityId = $adb->getUniqueID("vtiger_ws_entity");
		$adb->pquery('insert into vtiger_ws_entity(id,name,handler_path,handler_class,ismodule) values (?,?,?,?,?)',
				array($entityId,$tab,$moduleHandler['file'],$moduleHandler['class'],1));
	}
	$entityId = $adb->getUniqueID("vtiger_ws_entity");
	$adb->pquery('insert into vtiger_ws_entity(id,name,handler_path,handler_class,ismodule) values (?,?,?,?,?)',
		array($entityId,'Events',$moduleHandler['file'],$moduleHandler['class'],1));
	$entityId = $adb->getUniqueID("vtiger_ws_entity");
	$adb->pquery('insert into vtiger_ws_entity(id,name,handler_path,handler_class,ismodule) values (?,?,?,?,?)',
		array($entityId,'Users',$moduleHandler['file'],$moduleHandler['class'],1));
	
	vtws_addDefaultActorTypeEntity('Groups',array('fieldNames'=>'groupname',
		'indexField'=>'groupid','tableName'=>'vtiger_groups'));
	ExecuteQuery("CREATE TABLE IF NOT EXISTS `vtiger_ws_entity_tables` (`webservice_entity_id` int(11) NOT NULL ,`table_name` varchar(50) NOT NULL , PRIMARY KEY  (`webservice_entity_id`,`table_name`), CONSTRAINT `fk_1_vtiger_ws_actor_tables` FOREIGN KEY (`webservice_entity_id`) REFERENCES `vtiger_ws_entity` (`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_ws_entity_fieldtype(fieldtypeid integer(19) not null auto_increment,table_name varchar(50) not null,field_name varchar(50) not null,fieldtype varchar(200) not null,PRIMARY KEY(fieldtypeid),UNIQUE KEY vtiger_idx_1_tablename_fieldname (table_name,field_name)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_ws_entity_referencetype(fieldtypeid integer(19) not null,type varchar(25) not null,PRIMARY KEY(fieldtypeid,type),  CONSTRAINT `vtiger_fk_1_actors_referencetype` FOREIGN KEY (`fieldtypeid`) REFERENCES `vtiger_ws_entity_fieldtype` (`fieldtypeid`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	require_once("include/Webservices/WebServiceError.php");
	require_once 'include/Webservices/VtigerWebserviceObject.php';
	$webserviceObject = VtigerWebserviceObject::fromName($adb,'Groups');
	ExecuteQuery("insert into vtiger_ws_entity_tables(webservice_entity_id,table_name) values ({$webserviceObject->getEntityId()},'vtiger_groups')");
	ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_ws_operation(operationid int(11) not null auto_increment PRIMARY KEY,name varchar(128) 
	not null UNIQUE,handler_path varchar(255),handler_method varchar(64), type varchar(8) not null,prelogin int(3) not null, KEY vtiger_idx_ws_oepration_prelogin (prelogin)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	ExecuteQuery("CREATE TABLE IF NOT EXISTS `vtiger_ws_operation_parameters` (`operationid` int(11) NOT NULL, `name` varchar(128) NOT NULL,
		`type` varchar(64) NOT NULL, sequence int(11) not null,PRIMARY KEY  (`operationid`,`name`), CONSTRAINT 
		`vtiger_fk_1_ws_operation_params` FOREIGN KEY (`operationid`) REFERENCES `vtiger_ws_operation` (`operationid`) 
		ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	$operationMeta = array(
		"login"=>array(
			"include"=>array(
				"include/Webservices/Login.php"
			),
			"handler"=>"vtws_login",
			"params"=>array(
				"username"=>"String",
				"accessKey"=>"String"
			),
			"prelogin"=>1,
			"type"=>"POST"
		),
		"retrieve"=>array(
			"include"=>array(
				"include/Webservices/Retrieve.php"
			),
			"handler"=>"vtws_retrieve",
			"params"=>array(
				"id"=>"String"
			),
			"prelogin"=>0,
			"type"=>"GET"
		),
		"create"=>array(
			"include"=>array(
				"include/Webservices/Create.php"
			),
			"handler"=>"vtws_create",
			"params"=>array(
				"elementType"=>"String",
				"element"=>"encoded"
			),
			"prelogin"=>0,
			"type"=>"POST"
		),
		"update"=>array(
			"include"=>array(
				"include/Webservices/Update.php"
			),
			"handler"=>"vtws_update",
			"params"=>array(
				"element"=>"encoded"
			),
			"prelogin"=>0,
			"type"=>"POST"
		),
		"delete"=>array(
			"include"=>array(
				"include/Webservices/Delete.php"
			),
			"handler"=>"vtws_delete",
			"params"=>array(
				"id"=>"String"
			),
			"prelogin"=>0,
			"type"=>"POST"
		),
		"sync"=>array(
			"include"=>array(
				"include/Webservices/GetUpdates.php"
			),
			"handler"=>"vtws_sync",
			"params"=>array(
				"modifiedTime"=>"DateTime",
				"elementType"=>"String"
			),
			"prelogin"=>0,
			"type"=>"GET"
		),
		"query"=>array(
			"include"=>array(
				"include/Webservices/Query.php"
			),
			"handler"=>"vtws_query",
			"params"=>array(
				"query"=>"String"
			),
			"prelogin"=>0,
			"type"=>"GET"
		),
		"logout"=>array(
			"include"=>array(
				"include/Webservices/Logout.php"
			),
			"handler"=>"vtws_logout",
			"params"=>array(
				"sessionName"=>"String"
			),
			"prelogin"=>0,
			"type"=>"POST"
		),
		"listtypes"=>array(
			"include"=>array(
				"include/Webservices/ModuleTypes.php"
			),
			"handler"=>"vtws_listtypes",
			"params"=>array(),
			"prelogin"=>0,
			"type"=>"GET"
		),
		"getchallenge"=>array(
			"include"=>array(
				"include/Webservices/AuthToken.php"
			),
			"handler"=>"vtws_getchallenge",
			"params"=>array(
				"username"=>"String"
			),
			"prelogin"=>1,
			"type"=>"GET"
		),
		"describe"=>array(
			"include"=>array(
				"include/Webservices/DescribeObject.php"
			),
			"handler"=>"vtws_describe",
			"params"=>array(
				"elementType"=>"String"
			),
			"prelogin"=>0,
			"type"=>"GET"
		),
		"extendsession"=>array(
			"include"=>array(
				"include/Webservices/ExtendSession.php"
			),
			"handler"=>"vtws_extendSession",
			'params'=>array(),
			"prelogin"=>1,
			"type"=>"POST"
		)
	);
	$createOperationQuery = "insert into vtiger_ws_operation(operationid,name,handler_path,handler_method,type,prelogin) 
		values (?,?,?,?,?,?);";
	$createOperationParamsQuery = "insert into vtiger_ws_operation_parameters(operationid,name,type,sequence) 
		values (?,?,?,?);";
	foreach ($operationMeta as $operationName => $operationDetails) {
		$operationId = $adb->getUniqueID("vtiger_ws_operation");
		$result = $adb->pquery($createOperationQuery,array($operationId,$operationName,$operationDetails['include'],
			$operationDetails['handler'],$operationDetails['type'],$operationDetails['prelogin']));
		$params = $operationDetails['params'];
		$sequence = 1;
		foreach ($params as $paramName => $paramType) {
			$result = $adb->pquery($createOperationParamsQuery,array($operationId,$paramName,$paramType,$sequence++));
		}
	}
	
	vtws_addDefaultActorTypeEntity('Currency',array('fieldNames'=>'currency_name',
		'indexField'=>'id','tableName'=>'vtiger_currency_info'));
	require_once 'include/Webservices/VtigerWebserviceObject.php';
	$webserviceObject = VtigerWebserviceObject::fromName($adb,'Currency');
	ExecuteQuery("insert into vtiger_ws_entity_tables(webservice_entity_id,table_name) values ({$webserviceObject->getEntityId()},'vtiger_currency_info')");
	
	vtws_addDefaultActorTypeEntity('DocumentFolders',array('fieldNames'=>'foldername',
		'indexField'=>'folderid','tableName'=>'vtiger_attachmentsfolder'));
	$webserviceObject = VtigerWebserviceObject::fromName($adb,'DocumentFolders');
	ExecuteQuery("insert into vtiger_ws_entity_tables(webservice_entity_id,table_name) values ({$webserviceObject->getEntityId()},'vtiger_attachmentsfolder')");
	
	$success = true;
	$fieldTypeId = $adb->getUniqueID("vtiger_ws_entity_fieldtype");
	$result = $adb->pquery("insert into vtiger_ws_entity_fieldtype(fieldtypeid,table_name,field_name,fieldtype) values(?,?,?,?);",
		array($fieldTypeId,'vtiger_attachmentsfolder','createdby',"reference"));
	if(!is_object($result)){
		echo "failed fo init<br>";
		$success=false;
	}
	$result = $adb->pquery("insert into vtiger_ws_entity_referencetype(fieldtypeid,type) values(?,?)",array($fieldTypeId,'Users'));
	if(!is_object($result)){
		echo "failed for: Users, fieldtypeid: $fieldTypeId";
		$success=false;
	}
	if(!$success){
		echo "Migration Query Failed";
	}
	
}
	ExecuteQuery("ALTER TABLE vtiger_notes MODIFY filename varchar(200)");	

$todoid = getTabid('Calendar');
$eventid = getTabid('Events');
// Assigned To field should always come in quickcreate otherwise smownerid will not be filled
Executequery("UPDATE vtiger_field set quickcreate = 0,quickcreatesequence = 4 WHERE fieldname = 'assigned_user_id' AND tabid = $accounttabid");
Executequery("UPDATE vtiger_field set quickcreate = 0,quickcreatesequence = 6 WHERE fieldname = 'assigned_user_id' AND tabid = $leadtabid");
Executequery("UPDATE vtiger_field set quickcreate = 0,quickcreatesequence = 6 WHERE fieldname = 'assigned_user_id' AND tabid = $contacttabid");
Executequery("UPDATE vtiger_field set quickcreate = 0,quickcreatesequence = 6 WHERE fieldname = 'assigned_user_id' AND tabid = $potentialtabid");
Executequery("UPDATE vtiger_field set quickcreate = 0,quickcreatesequence = 7 WHERE fieldname = 'assigned_user_id' AND tabid = $campaigntabid");
Executequery("UPDATE vtiger_field set quickcreate = 0,quickcreatesequence = 4 WHERE fieldname = 'assigned_user_id' AND tabid = $helpdesktabid");
Executequery("UPDATE vtiger_field set quickcreatesequence = 3 WHERE fieldname = 'ticketpriorities' AND tabid = $helpdesktabid");
Executequery("UPDATE vtiger_field set quickcreatesequence = 2 WHERE fieldname = 'ticketstatus' AND tabid = $helpdesktabid");
Executequery("UPDATE vtiger_field set quickcreate = 0,quickcreatesequence = 4 WHERE fieldname ='assigned_user_id' AND tabid = $todoid");
Executequery("UPDATE vtiger_field set quickcreate = 0,quickcreatesequence = 6 WHERE fieldname ='assigned_user_id' AND tabid = $eventid");


include_once('modules/Utilities/Currencies.php'); 
	 	 
ExecuteQuery("CREATE TABLE vtiger_currencies(currencyid INTEGER(19),currency_name varchar(200),currency_code varchar(50),currency_symbol varchar(11)) ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 
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
Executequery("UPDATE vtiger_products set handler = 1 WHERE handler = 0");

//Emails fields 
$email_Tabid = getTabid('Emails');
$blockid = $adb->getUniqueID('vtiger_blocks');
$adb->query("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) values($email_Tabid,".$adb->getUniqueID("vtiger_field").",'from_email','vtiger_emaildetails',1,12,'from_email','From',1,2,0,100,1,$blockid,3,'V~M',3,NULL,'BAS',0,NULL)");
$adb->query("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) values($email_Tabid,".$adb->getUniqueID("vtiger_field").",'to_email','vtiger_emaildetails',1,8,'saved_toid','To',1,2,0,100,2,$blockid,1,'V~M',3,NULL,'BAS',0,NULL)");
$adb->query("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) values($email_Tabid,".$adb->getUniqueID("vtiger_field").",'cc_email','vtiger_emaildetails',1,8,'ccmail','CC',1,2,0,1000,3,$blockid,1,'V~O',3,NULL,'BAS',0,NULL)");		
$adb->query("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) values($email_Tabid,".$adb->getUniqueID("vtiger_field").",'bcc_email','vtiger_emaildetails',1,8,'bccmail','BCC' ,1,2,0,1000,4,$blockid,1,'V~O',3,NULL,'BAS',0,NULL)");
$adb->query("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) values($email_Tabid,".$adb->getUniqueID("vtiger_field").",'idlists','vtiger_emaildetails',1,1,'parent_id','Parent ID' ,1,2,0,1000,5,$blockid,3,'V~O',3,NULL,'BAS',0,NULL)");		
$adb->query("insert into vtiger_field (tabid, fieldid, columnname, tablename, generatedtype, uitype, fieldname, fieldlabel, readonly, presence, selected, maximumlength, sequence, block, displaytype, typeofdata, quickcreate, quickcreatesequence, info_type, masseditable, helpinfo) values($email_Tabid,".$adb->getUniqueID("vtiger_field").",'email_flag','vtiger_emaildetails',1,16,'email_flag','Email Flag' ,1,2,0,1000,6,$blockid,3,'V~O',3,NULL,'BAS',0,NULL)");		

require_once('include/Zend/Json.php');
$json = new Zend_Json();

$result = $adb->query("SELECT * FROM vtiger_emaildetails");
$rows = $adb->num_rows($result);
for($i=0 ; $i<$rows ;$i++) {
	$emailid = $adb->query_result($result,$i,'emailid');
	$to = $adb->query_result($result,$i,'to_email');
	$cc = $adb->query_result($result,$i,'cc_email');
	$bcc = $adb->query_result($result,$i,'bcc_email');
	
	$to = preg_replace("/###/",",",$to);
	$to = str_replace('&amp;lt;','<',$to);
	$to = str_replace('&amp;gt;','>',$to);
	$to = explode(',',$to);
	$to_json = $json->encode($to);
	
	$cc = str_replace('&amp;lt;','<',$cc);
	$cc = str_replace('&amp;gt;','>',$cc);
	$cc = preg_replace("/###/",",",$cc);
	$cc = explode(',',$cc);
	$cc_json = $json->encode($cc);
	
	$bcc = str_replace('&amp;lt;','<',$bcc);
	$bcc = str_replace('&amp;gt;','>',$bcc);
	$bcc = preg_replace("/###/",",",$bcc);
	$bcc = explode(',',$bcc);
	$bcc_json = $json->encode($bcc);
	
	$adb->pquery("UPDATE vtiger_emaildetails set to_email = ?, cc_email= ?, bcc_email= ? WHERE emailid = ?",array($to_json,$cc_json,$bcc_json,$emailid));
}

//Reports Migration Handling for Older reports - STARTS

updateReportColumns("vtiger_selectcolumn");
updateReportColumns("vtiger_relcriteria");

function updateReportColumns($table){
	global $adb;
	$report_update_array = array(
		"vtiger_campaign"=>"(vtiger_reportmodules.primarymodule='Potentials' OR vtiger_reportmodules.secondarymodules = 'Potentials') AND $table.columnname LIKE '%vtiger_campaign%'",
		"vtiger_vendorRel"=>"$table.columnname LIKE '%vtiger_vendorRel%'",
		"vtiger_potentialRel"=>"$table.columnname LIKE '%vtiger_potentialRel%'",
	);
	
	foreach($report_update_array as $key=>$where){
		$query = "SELECT vtiger_report.reportid as reportid,$table.columnname AS columnname FROM vtiger_report INNER JOIN $table ON $table.queryid = vtiger_report.reportid INNER JOIN vtiger_reportmodules ON vtiger_reportmodules.reportmodulesid = vtiger_report.reportid WHERE $where";
		$result = $adb->query($query);
		if($adb->num_rows($result) > 0){
			for($i=0;$i<$adb->num_rows($result);$i++){
				$reportid = $adb->query_result($result,$i,"reportid");
				$colname = $adb->query_result($result,$i,"columnname");
				$column_array = split(":",$colname);
				$column = split("_",$column_array[2]);
				$mod_name = $column[0];
				$newcolname = str_replace("$key",$key."$mod_name",$colname);
				ExecuteQuery("UPDATE $table SET columnname = '".$newcolname."' WHERE queryid = ".$reportid." AND columnname = '".$colname."'");
			}
		}
	}
	$query = "SELECT vtiger_reportmodules.primarymodule as primarymodule, vtiger_report.reportid as reportid,$table.columnname AS columnname FROM vtiger_report INNER JOIN $table ON $table.queryid = vtiger_report.reportid INNER JOIN vtiger_reportmodules ON vtiger_reportmodules.reportmodulesid = vtiger_report.reportid WHERE $table.columnname LIKE 'vtiger_products%:products_description:%'";
	$result = $adb->query($query);
	if($adb->num_rows($result) > 0){
		for($i=0;$i<$adb->num_rows($result);$i++){
			$pri_module = $adb->query_result($result,$i,"reportid");
			$reportid = $adb->query_result($result,$i,"reportid");
			$colname = $adb->query_result($result,$i,"columnname");
			$column_array = split(":",$colname);
			if($pri_module!="Products"){
				$column_array[0]='vtiger_crmentityProducts';
				$column_array[1]='description';
			} else {
				$column_array[0]='vtiger_crmentity';
				$column_array[1]='description';
			}
			$newcolname = $column_array[0].":".$column_array[1].":".$column_array[2].":".$column_array[3].":".$column_array[4];
			ExecuteQuery("UPDATE $table SET columnname = '".$newcolname."' WHERE queryid = ".$reportid." AND columnname = '".$colname."'");
		}
	}
	$query = "SELECT vtiger_reportmodules.primarymodule as primarymodule, vtiger_report.reportid as reportid,$table.columnname AS columnname FROM vtiger_report INNER JOIN $table ON $table.queryid = vtiger_report.reportid INNER JOIN vtiger_reportmodules ON vtiger_reportmodules.reportmodulesid = vtiger_report.reportid WHERE $table.columnname LIKE 'vtiger_accountPotentials%:accountname:%:account_id:%'";
	$result = $adb->query($query);
	if($adb->num_rows($result) > 0){
		for($i=0;$i<$adb->num_rows($result);$i++){
			$reportid = $adb->query_result($result,$i,"reportid");
			$colname = $adb->query_result($result,$i,"columnname");
			$column_array = split(":",$colname);
			$column_array[0]='vtiger_potential';
			$column_array[1]='related_to';
			$column_array[2]='Potentials_Related_To';
			$column_array[3]='related_to';
			$newcolname = $column_array[0].":".$column_array[1].":".$column_array[2].":".$column_array[3].":".$column_array[4];
			ExecuteQuery("UPDATE $table SET columnname = '".$newcolname."' WHERE queryid = ".$reportid." AND columnname = '".$colname."'");
		}
	}
}
//ENDS

ExecuteQuery("ALTER TABLE vtiger_inventoryproductrel ADD COLUMN lineitem_id int(19) AUTO_INCREMENT UNIQUE");

ExecuteQuery("update vtiger_field set typeofdata='V~M' where fieldname='sales_stage' and tabid =$potentialtabid");

ExecuteQuery("update vtiger_cvcolumnlist set columnname ='vtiger_emaildetails:to_email:saved_toid:Emails_To:V' where columnname ='vtiger_crmentity:smownerid:assigned_user_id:Emails_Sender:V'");

// for Workflow in settings page of every module
	$module_manager_id = getSettingsBlockId('LBL_MODULE_MANAGER');
	$result = $adb->pquery("SELECT max(sequence) AS maxseq FROM vtiger_settings_field WHERE blockid = ?",array($module_manager_id));
	$maxseq = $adb->query_result($result,0,'maxseq');
	if($maxseq < 0 || $maxseq == NULL){
		$maxseq=1;
	}
	$adb->pquery("INSERT INTO vtiger_settings_field (fieldid, blockid, name, iconpath, description, linkto, sequence) VALUES (?,?,?,?,?,?,?)",array($adb->getUniqueID('vtiger_settings_field'), $module_manager_id, 'LBL_WORKFLOW_LIST', 'settingsWorkflow.png', 'LBL_AVAILABLE_WORKLIST_LIST', 'index.php?module=com_vtiger_workflow&action=workflowlist', $maxseq));
	

$migrationlog->debug("\n\nDB Changes from 5.0.4 to 5.1.0 RC -------- Ends \n\n");

?>