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
require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
require_once 'include/events/include.inc';
include_once 'vtlib/Vtiger/Cron.php';

//5.2.1 to 5.3.0RC database changes

$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

global $migrationlog;

$migrationlog->debug("\n\nDB Changes from 5.3.0 to 5.4.0RC -------- Starts \n\n");

$tabIdsResult = $adb->pquery('SELECT tabid, name FROM vtiger_tab', array());
$noOfTabs = $adb->num_rows($tabIdsResult);
$tabIdsList = array();

for ($i = 0; $i < $noOfTabs; ++$i) {
	$tabIdsList[$adb->query_result($tabIdsResult, $i, 'name')] = $adb->query_result($tabIdsResult, $i, 'tabid');
}
$leadTab = $tabIdsList['Leads'];
$accountTab = $tabIdsList['Accounts'];
$contactTab = $tabIdsList['Contacts'];
$potentialTab = $tabIdsList['Potentials'];
$usersTab = $tabIdsList['Users'];

$productsTabId = $tabIdsList['Products'];
$servicesTabId = $tabIdsList['Services'];
$documentsTabId = $tabIdsList['Documents'];

$skipForModules = array('ModComments');

$result = $adb->pquery("SELECT presence,quickcreate,masseditable,tabid,block FROM vtiger_field WHERE fieldname=?", array('createdtime'));
$rows = $adb->num_rows($result);
for($i=0; $i<$rows; $i++){
	$tabId = $adb->query_result($result,$i,'tabid');
	$blockId = $adb->query_result($result,$i,'block');
	$presence = $adb->query_result($result,$i,'presence');
	$quickcreate = $adb->query_result($result,$i,'quickcreate');
	$massedit = $adb->query_result($result,$i,'massedit');
	$moduleName = getTabModuleName($tabId);
	if(in_array($moduleName, $skipForModules)) continue;

	$moduleInstance = Vtiger_Module::getInstance($moduleName);
	$blockInstance = Vtiger_Block::getInstance($blockId,$moduleInstance);

	$field = new Vtiger_Field();
	$field->name = 'modifiedby';
	$field->label= 'Last Modified By';
	$field->table = 'vtiger_crmentity';
	$field->column = 'modifiedby';
	$field->uitype = 52;
	$field->displaytype = 3;
	$field->presence = $presence;
	$field->quickcreate = $quickcreate;
	$field->masseditable = $massedit;
	$blockInstance->addField($field);
}

$moduleInstance = Vtiger_Module::getInstance('Home');
$moduleInstance->addLink(
		'HEADERSCRIPT', 'Help Me', 'modules/Home/js/HelpMeNow.js'
);

ExecutePQuery("UPDATE vtiger_blocks SET sequence = ? WHERE blocklabel = ? AND tabid = ? ", array(2, 'LBL_FILE_INFORMATION', $documentsTabId));
ExecutePQuery("UPDATE vtiger_blocks SET sequence = ? WHERE blocklabel = ? AND tabid = ?", array(3, 'LBL_DESCRIPTION', $documentsTabId));

// Adding 'from_portal' field to Trouble tickets module, to track the tickets created from customer portal
$moduleInstance = Vtiger_Module::getInstance('HelpDesk');
$block = Vtiger_Block::getInstance('LBL_TICKET_INFORMATION', $moduleInstance);

$field = new Vtiger_Field();
$field->name = 'from_portal';
$field->label = 'From Portal';
$field->table = 'vtiger_troubletickets';
$field->column = 'from_portal';
$field->columntype = 'varchar(3)';
$field->typeofdata = 'C~O';
$field->uitype = 56;
$field->displaytype = 3;
$field->presence = 0;
$block->addField($field);

// Register Entity Methods
$emm = new VTEntityMethodManager($adb);

// Register Entity Method for Customer Portal Login details email notification task
$emm->addEntityMethod("Contacts", "SendPortalLoginDetails", "modules/Contacts/ContactsHandler.php", "Contacts_sendCustomerPortalLoginDetails");

// Register Entity Method for Email notification on ticket creation from Customer portal
$emm->addEntityMethod("HelpDesk", "NotifyOnPortalTicketCreation", "modules/HelpDesk/HelpDeskHandler.php", "HelpDesk_nofifyOnPortalTicketCreation");

// Register Entity Method for Email notification on ticket comment from Customer portal
$emm->addEntityMethod("HelpDesk", "NotifyOnPortalTicketComment", "modules/HelpDesk/HelpDeskHandler.php", "HelpDesk_notifyOnPortalTicketComment");

// Register Entity Method for Email notification to Record Owner on ticket change, which is not from Customer portal
$emm->addEntityMethod("HelpDesk", "NotifyOwnerOnTicketChange", "modules/HelpDesk/HelpDeskHandler.php", "HelpDesk_notifyOwnerOnTicketChange");

// Register Entity Method for Email notification to Related Customer on ticket change, which is not from Customer portal
$emm->addEntityMethod("HelpDesk", "NotifyParentOnTicketChange", "modules/HelpDesk/HelpDeskHandler.php", "HelpDesk_notifyParentOnTicketChange");

// Creating Default workflows
$workflowManager = new VTWorkflowManager($adb);
$taskManager = new VTTaskManager($adb);

// Contact workflow on creation/modification
$contactWorkFlow = $workflowManager->newWorkFlow("Contacts");
$contactWorkFlow->test = '';
$contactWorkFlow->description = "Workflow for Contact Creation or Modification";
$contactWorkFlow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
$contactWorkFlow->defaultworkflow = 1;
$workflowManager->save($contactWorkFlow);

$task = $taskManager->createTask('VTEntityMethodTask', $contactWorkFlow->id);
$task->active = true;
$task->summary = 'Email Customer Portal Login Details';
$task->methodName = "SendPortalLoginDetails";
$taskManager->saveTask($task);

// Trouble Tickets workflow on creation from Customer Portal
$helpDeskWorkflow = $workflowManager->newWorkFlow("HelpDesk");
$helpDeskWorkflow->test = '[{"fieldname":"from_portal","operation":"is","value":"true:boolean"}]';
$helpDeskWorkflow->description = "Workflow for Ticket Created from Portal";
$helpDeskWorkflow->executionCondition = VTWorkflowManager::$ON_FIRST_SAVE;
$helpDeskWorkflow->defaultworkflow = 1;
$workflowManager->save($helpDeskWorkflow);

$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
$task->active = true;
$task->summary = 'Notify Record Owner and the Related Contact when Ticket is created from Portal';
$task->methodName = "NotifyOnPortalTicketCreation";
$taskManager->saveTask($task);

// Trouble Tickets workflow on ticket update from Customer Portal
$helpDeskWorkflow = $workflowManager->newWorkFlow("HelpDesk");
$helpDeskWorkflow->test = '[{"fieldname":"from_portal","operation":"is","value":"true:boolean"}]';
$helpDeskWorkflow->description = "Workflow for Ticket Updated from Portal";
$helpDeskWorkflow->executionCondition = VTWorkflowManager::$ON_MODIFY;
$helpDeskWorkflow->defaultworkflow = 1;
$workflowManager->save($helpDeskWorkflow);

$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
$task->active = true;
$task->summary = 'Notify Record Owner when Comment is added to a Ticket from Customer Portal';
$task->methodName = "NotifyOnPortalTicketComment";
$taskManager->saveTask($task);

// Trouble Tickets workflow on ticket change, which is not from Customer Portal - Both Record Owner and Related Customer
$helpDeskWorkflow = $workflowManager->newWorkFlow("HelpDesk");
$helpDeskWorkflow->test = '[{"fieldname":"from_portal","operation":"is","value":"false:boolean"}]';
$helpDeskWorkflow->description = "Workflow for Ticket Change, not from the Portal";
$helpDeskWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
$helpDeskWorkflow->defaultworkflow = 1;
$workflowManager->save($helpDeskWorkflow);

$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
$task->active = true;
$task->summary = 'Notify Record Owner on Ticket Change, which is not done from Portal';
$task->methodName = "NotifyOwnerOnTicketChange";
$taskManager->saveTask($task);

$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
$task->active = true;
$task->summary = 'Notify Related Customer on Ticket Change, which is not done from Portal';
$task->methodName = "NotifyParentOnTicketChange";
$taskManager->saveTask($task);

// Events workflow when Send Notification is checked
$eventsWorkflow = $workflowManager->newWorkFlow("Events");
$eventsWorkflow->test = '[{"fieldname":"sendnotification","operation":"is","value":"true:boolean"}]';
$eventsWorkflow->description = "Workflow for Events when Send Notification is True";
$eventsWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
$eventsWorkflow->defaultworkflow = 1;
$workflowManager->save($eventsWorkflow);

$task = $taskManager->createTask('VTEmailTask', $eventsWorkflow->id);
$task->active = true;
$task->summary = 'Send Notification Email to Record Owner';
$task->recepient = "\$(assigned_user_id : (Users) email1)";
$task->subject = "Event :  \$subject";
$task->content = '$(assigned_user_id : (Users) last_name) $(assigned_user_id : (Users) first_name) ,<br/>'
		. '<b>Activity Notification Details:</b><br/>'
		. 'Subject             : $subject<br/>'
		. 'Start date and time : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
		. 'End date and time   : $due_date  $time_end ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
		. 'Status              : $eventstatus <br/>'
		. 'Priority            : $taskpriority <br/>'
		. 'Related To          : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
								. '$(parent_id : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
		. 'Contacts List       : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
		. 'Location            : $location <br/>'
		. 'Description         : $description';
$taskManager->saveTask($task);

// Calendar workflow when Send Notification is checked
$calendarWorkflow = $workflowManager->newWorkFlow("Calendar");
$calendarWorkflow->test = '[{"fieldname":"sendnotification","operation":"is","value":"true:boolean"}]';
$calendarWorkflow->description = "Workflow for Calendar Todos when Send Notification is True";
$calendarWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
$calendarWorkflow->defaultworkflow = 1;
$workflowManager->save($calendarWorkflow);

$task = $taskManager->createTask('VTEmailTask', $calendarWorkflow->id);
$task->active = true;
$task->summary = 'Send Notification Email to Record Owner';
$task->recepient = "\$(assigned_user_id : (Users) email1)";
$task->subject = "Task :  \$subject";
$task->content = '$(assigned_user_id : (Users) last_name) $(assigned_user_id : (Users) first_name) ,<br/>'
		. '<b>Task Notification Details:</b><br/>'
		. 'Subject : $subject<br/>'
		. 'Start date and time : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
		. 'End date and time   : $due_date ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
		. 'Status              : $taskstatus <br/>'
		. 'Priority            : $taskpriority <br/>'
		. 'Related To          : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
		. '$(parent_id         : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
		. 'Contacts List       : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
		. 'Location            : $location <br/>'
		. 'Description         : $description';
$taskManager->saveTask($task);

ExecutePQuery("UPDATE com_vtiger_workflows SET defaultworkflow=1 WHERE
			module_name='Invoice' and summary='UpdateInventoryProducts On Every Save'", array());

$em = new VTEventsManager($adb);
// Registering event for HelpDesk - To reset from_portal value
$em->registerHandler('vtiger.entity.aftersave.final', 'modules/HelpDesk/HelpDeskHandler.php', 'HelpDeskHandler');

Vtiger_Cron::register('Workflow', 'cron/modules/com_vtiger_workflow/com_vtiger_workflow.service', 900, 'com_vtiger_workflow', '', '', 'Recommended frequency for Workflow is 15 mins');
Vtiger_Cron::register('RecurringInvoice', 'cron/modules/SalesOrder/RecurringInvoice.service', 43200, 'SalesOrder', '', '', 'Recommended frequency for RecurringInvoice is 12 hours');
Vtiger_Cron::register('SendReminder', 'cron/SendReminder.service', 900, 'Calendar', '', '', 'Recommended frequency for SendReminder is 15 mins');
Vtiger_Cron::register('ScheduleReports', 'cron/modules/Reports/ScheduleReports.service', 900, 'Reports', '', '', 'Recommended frequency for ScheduleReports is 15 mins');
Vtiger_Cron::register('MailScanner', 'cron/MailScanner.service', 900, 'Settings', '', '', 'Recommended frequency for MailScanner is 15 mins');

ExecutePQuery("DELETE FROM vtiger_settings_field WHERE name='LBL_ASSIGN_MODULE_OWNERS'", array());

Vtiger_Utils::AddColumn('vtiger_tab', 'parent','VARCHAR(30)');

ExecuteQuery("update vtiger_tab set parent = 'Sales' where name = 'Accounts'");
ExecuteQuery("update vtiger_tab set parent = 'Tools' where name = 'Calendar'");
ExecuteQuery("update vtiger_tab set parent = 'Sales' where name = 'Contacts'");
ExecuteQuery("update vtiger_tab set parent = 'Analytics' where name = 'Dashboard'");
ExecuteQuery("update vtiger_tab set parent = 'Sales' where name = 'Leads'");
ExecuteQuery("update vtiger_tab set parent = 'Sales' where name = 'Potentials'");
ExecuteQuery("update vtiger_tab set parent = 'Inventory' where name = 'Vendors'");
ExecuteQuery("update vtiger_tab set parent = 'Inventory' where name = 'Products'");
ExecuteQuery("update vtiger_tab set parent = 'Tools' where name = 'Documents'");
ExecuteQuery("update vtiger_tab set parent = 'Tools' where name = 'Emails'");
ExecuteQuery("update vtiger_tab set parent = 'Support' where name = 'HelpDesk'");
ExecuteQuery("update vtiger_tab set parent = 'Support' where name = 'Faq'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'Faq'");
ExecuteQuery("update vtiger_tab set parent = 'Inventory' where name = 'PriceBooks'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'PriceBooks'");
ExecuteQuery("update vtiger_tab set parent = 'Sales' where name = 'SalesOrder'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'SalesOrder'");
ExecuteQuery("update vtiger_tab set parent = 'Sales' where name = 'Quotes'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'Quotes'");
ExecuteQuery("update vtiger_tab set parent = 'Inventory' where name = 'PurchaseOrder'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'PurchaseOrder'");
ExecuteQuery("update vtiger_tab set parent = 'Sales' where name = 'Invoice'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'Invoice'");
ExecuteQuery("update vtiger_tab set parent = 'Tools' where name = 'RSS'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'RSS'");
ExecuteQuery("update vtiger_tab set parent = 'Analytics' where name = 'Reports'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'Reports'");
ExecuteQuery("update vtiger_tab set parent = 'Marketing' where name = 'Campaigns'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'Campaigns'");
ExecuteQuery("update vtiger_tab set parent = 'Tools' where name = 'Portal'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'Portal'");
ExecuteQuery("update vtiger_tab set parent = 'Support' where name = 'ServiceContracts'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'ServiceContracts'");
ExecuteQuery("update vtiger_tab set parent = 'Tools' where name = 'PBX Manager'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'PBX Manager'");
ExecuteQuery("update vtiger_tab set parent = 'Inventory' where name = 'Services'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'Services'");
ExecuteQuery("update vtiger_tab set parent = 'Tools' where name = 'RecycleBin'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'RecycleBin'");
ExecuteQuery("update vtiger_tab set parent = 'Support' where name = 'Assets'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'Assets'");
ExecuteQuery("update vtiger_tab set parent = 'Tools' where name = 'ModComments'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'ModComments'");
ExecuteQuery("update vtiger_tab set parent = 'Support' where name = 'ProjectMilestone'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'ProjectMilestone'");
ExecuteQuery("update vtiger_tab set parent = 'Support' where name = 'ProjectTask'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'ProjectTask'");
ExecuteQuery("update vtiger_tab set parent = 'Support' where name = 'Project'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'Project'");
ExecuteQuery("update vtiger_tab set parent = 'Tools' where name = 'SMSNotifier'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'SMSNotifier'");
ExecuteQuery("update vtiger_tab set parent = 'Tools' where name = 'MailManager'");
ExecuteQuery("update vtiger_tab set tabsequence = -1 where name = 'MailManager'");

$fieldId = $adb->getUniqueId("vtiger_settings_field");
ExecuteQuery("insert into vtiger_settings_field (fieldid,blockid,name,iconpath,description,linkto,sequence,active)
					values ($fieldId," . getSettingsBlockId('LBL_STUDIO') . ",'LBL_MENU_EDITOR','menueditor.png','LBL_MENU_DESC',
					'index.php?module=Settings&action=MenuEditor&parenttab=Settings',4,0)");

$present_module = array();
$result = $adb->query('select tabid,name,tablabel,tabsequence,parent from vtiger_tab where parent is not null and parent!=" "');
for ($i = 0; $i < $adb->num_rows($result); $i++) {
	$modulename = $adb->query_result($result, $i, 'name');
	$modulelabel = $adb->query_result($result, $i, 'tablabel');
	array_push($present_module, $modulelabel);
}
$result = $adb->query("select name,tablabel,parenttab_label,vtiger_tab.tabid
							from vtiger_parenttabrel
							inner join vtiger_tab on vtiger_parenttabrel.tabid = vtiger_tab.tabid
							inner join vtiger_parenttab on vtiger_parenttabrel.parenttabid = vtiger_parenttab.parenttabid
									and vtiger_parenttab.parenttab_label is not null
									and vtiger_parenttab.parenttab_label != ' '");

$skipModules = array("Webmails", "Home");
for ($i = 0; $i < $adb->num_rows($result); $i++) {
	$modulename = $adb->query_result($result, $i, 'name');
	$modulelabel = $adb->query_result($result, $i, 'tablabel');
	$parent = $adb->query_result($result, $i, 'parenttab_label');
	if ((!(in_array($modulelabel, $present_module))) && (!(in_array($modulelabel, $skipModules)))) {
		if ($modulelabel == "MailManager") {
			ExecutePQuery("update vtiger_tab set parent = ? where tablabel = ?", array("Tools", $modulelabel));
			ExecutePQuery("update vtiger_tab set tabsequence = -1 where tablabel = ?", array($modulelabel));
		} else {
			ExecutePQuery("update vtiger_tab set parent = ? where tablabel = ?", array($parent, $modulelabel));
		}
	}
}

ExecuteQuery("ALTER TABLE `vtiger_customerportal_prefs` DROP PRIMARY KEY");
ExecuteQuery("ALTER TABLE `vtiger_customerportal_prefs` ALTER COLUMN prefkey DROP DEFAULT");
ExecuteQuery("ALTER TABLE `vtiger_customerportal_prefs` ADD PRIMARY KEY(tabid,prefkey)");

$query = "INSERT INTO vtiger_customerportal_prefs (
			SELECT tabid, 'defaultassignee', prefvalue FROM vtiger_customerportal_prefs WHERE prefkey='userid'
		)";
ExecutePQuery($query, array());

$fieldMap = array(
	array('industry', 'industry', null, null),
	array('phone', 'phone', 'phone', null),
	array('fax', 'fax', 'fax', null),
	array('rating', 'rating', null, null),
	array('email', 'email1', 'email', null),
	array('website', 'website', null, null),
	array('city', 'bill_city', 'mailingcity', null),
	array('code', 'bill_code', 'mailingcode', null),
	array('country', 'bill_country', 'mailingcountry', null),
	array('state', 'bill_state', 'mailingstate', null),
	array('lane', 'bill_street', 'mailingstreet', null),
	array('pobox', 'bill_pobox', 'mailingpobox', null),
	array('city', 'ship_city', null, null),
	array('code', 'ship_code', null, null),
	array('country', 'ship_country', null, null),
	array('state', 'ship_state', null, null),
	array('lane', 'ship_street', null, null),
	array('pobox', 'ship_pobox', null, null),
	array('description', 'description', 'description', 'description'),
	array('salutationtype', null, 'salutationtype', null),
	array('firstname', null, 'firstname', null),
	array('lastname', null, 'lastname', null),
	array('mobile', null, 'mobile', null),
	array('designation', null, 'title', null),
	array('secondaryemail', null, 'secondaryemail', null),
	array('leadsource', null, 'leadsource', 'leadsource'),
	array('leadstatus', null, null, null),
	array('noofemployees', 'employees', null, null),
	array('annualrevenue', 'annual_revenue', null, null)
);

$mapSql = "INSERT INTO vtiger_convertleadmapping(leadfid,accountfid,contactfid,potentialfid) values(?,?,?,?)";

foreach ($fieldMap as $values) {
	$leadfid = getFieldid($leadTab, $values[0]);
	$accountfid = getFieldid($accountTab, $values[1]);
	$contactfid = getFieldid($contactTab, $values[2]);
	$potentialfid = getFieldid($potentialTab, $values[3]);
	ExecutePQuery($mapSql, array($leadfid, $accountfid, $contactfid, $potentialfid));
}

$delete_empty_mapping = "DELETE FROM vtiger_convertleadmapping WHERE accountfid=0 AND contactfid=0 AND potentialfid=0";
ExecutePQuery($delete_empty_mapping, array());
$alter_vtiger_convertleadmapping = "ALTER TABLE vtiger_convertleadmapping ADD COLUMN editable int default 1";
ExecutePQuery($alter_vtiger_convertleadmapping, array());

$check_mapping = "SELECT 1 FROM vtiger_convertleadmapping WHERE leadfid=? AND accountfid=? AND contactfid=? AND  potentialfid=?";
$insert_mapping = "INSERT INTO vtiger_convertleadmapping(leadfid,accountfid,contactfid,potentialfid,editable) VALUES(?,?,?,?,?)";
$update_mapping = "UPDATE vtiger_convertleadmapping SET editable=0 WHERE leadfid=? AND accountfid=? AND contactfid=? AND potentialfid=?";
$check_res = $adb->pquery($check_mapping, array(getFieldid($leadTab, 'company'), getFieldid($accountTab, 'accountname'), 0, getFieldid($potentialTab, 'potentialname')));
if ($adb->num_rows($check_res) > 0) {
	ExecutePQuery($update_mapping, array(getFieldid($leadTab, 'company'), getFieldid($accountTab, 'accountname'), 0, getFieldid($potentialTab, 'potentialname')));
} else {
	ExecutePQuery($insert_mapping, array(getFieldid($leadTab, 'company'), getFieldid($accountTab, 'accountname'), null, getFieldid($potentialTab, 'potentialname'), 0));
}

$check_res = $adb->pquery($check_mapping, array(getFieldid($leadTab, 'email'), getFieldid($accountTab, 'email1'), getFieldid($contactTab, 'email'), 0));
if ($adb->num_rows($check_res) > 0) {
	ExecutePQuery($update_mapping, array(getFieldid($leadTab, 'email'), getFieldid($accountTab, 'email1'), getFieldid($contactTab, 'email'), 0));
} else {
	ExecutePQuery($insert_mapping, array(getFieldid($leadTab, 'email'), getFieldid($accountTab, 'email1'), getFieldid($contactTab, 'email'), null, 0));
}

$check_res = $adb->pquery($check_mapping, array(getFieldid($leadTab, 'firstname'), 0, getFieldid($contactTab, 'firstname'), 0));
if ($adb->num_rows($check_res) > 0) {
	ExecutePQuery($update_mapping, array(getFieldid($leadTab, 'firstname'), 0, getFieldid($contactTab, 'firstname'), 0));
} else {
	ExecutePQuery($insert_mapping, array(getFieldid($leadTab, 'firstname'), null, getFieldid($contactTab, 'firstname'), null, 0));
}

$check_res = $adb->pquery($check_mapping, array(getFieldid($leadTab, 'lastname'), 0, getFieldid($contactTab, 'lastname'), 0));
if ($adb->num_rows($check_res) > 0) {
	ExecutePQuery($update_mapping, array(getFieldid($leadTab, 'lastname'), 0, getFieldid($contactTab, 'lastname'), 0));
} else {
	ExecutePQuery($insert_mapping, array(getFieldid($leadTab, 'lastname'), null, getFieldid($contactTab, 'lastname'), null, 0));
}

$productInstance = Vtiger_Module::getInstance('Products');
$serviceInstance = Vtiger_Module::getInstance('Services');

/* Replace 'Handler' field with 'Assigned to' field for Products and Services - starts */
ExecuteQuery("UPDATE vtiger_crmentity, vtiger_products SET vtiger_crmentity.smownerid = vtiger_products.handler WHERE vtiger_crmentity.crmid = vtiger_products.productid");
ExecuteQuery("ALTER TABLE vtiger_products DROP COLUMN handler");
ExecutePQuery("UPDATE vtiger_field SET columnname = 'smownerid', tablename = 'vtiger_crmentity', uitype = '53', typeofdata = 'V~M', info_type = 'BAS', quickcreate = 0, quickcreatesequence = 5
				WHERE columnname = 'handler' AND tablename = 'vtiger_products' AND tabid = ?", array($productsTabId));
$oldProductHandlerColumnName = 'vtiger_products:handler:assigned_user_id:Products_Handler:V';
$newProductHandlerColumnName = 'vtiger_crmentity:smownerid:assigned_user_id:Products_Handler:V';
ExecutePQuery("UPDATE vtiger_cvcolumnlist SET columnname=? WHERE columnname=?", array($newProductHandlerColumnName, $oldProductHandlerColumnName));
ExecutePQuery("UPDATE vtiger_cvadvfilter SET columnname=? WHERE columnname=?", array($newProductHandlerColumnName, $oldProductHandlerColumnName));

ExecuteQuery("UPDATE vtiger_crmentity, vtiger_service SET vtiger_crmentity.smownerid = vtiger_service.handler WHERE vtiger_crmentity.crmid = vtiger_service.serviceid");
ExecuteQuery("ALTER TABLE vtiger_service DROP COLUMN handler");
ExecutePQuery("UPDATE vtiger_field SET columnname = 'smownerid', tablename = 'vtiger_crmentity', uitype = '53', typeofdata = 'V~M', info_type = 'BAS', quickcreate = 0, quickcreatesequence = 4
				WHERE columnname = 'handler' AND tablename = 'vtiger_service' AND tabid = ?", array($servicesTabId));
$oldServiceOwnerColumnName = 'vtiger_service:handler:assigned_user_id:Services_Owner:V';
$newServiceOwnerColumnName = 'vtiger_crmentity:smownerid:assigned_user_id:Services_Owner:V';
ExecutePQuery("UPDATE vtiger_cvcolumnlist SET columnname=? WHERE columnname=?", array($newServiceOwnerColumnName, $oldServiceOwnerColumnName));
ExecutePQuery("UPDATE vtiger_cvadvfilter SET columnname=? WHERE columnname=?", array($newServiceOwnerColumnName, $oldServiceOwnerColumnName));

// Allow Sharing access and role-based security for Products and Services
Vtiger_Access::deleteSharing($productInstance);
Vtiger_Access::initSharing($productInstance);
Vtiger_Access::allowSharing($productInstance);
Vtiger_Access::setDefaultSharing($productInstance);

Vtiger_Access::deleteSharing($serviceInstance);
Vtiger_Access::initSharing($serviceInstance);
Vtiger_Access::allowSharing($serviceInstance);
Vtiger_Access::setDefaultSharing($serviceInstance);

Vtiger_Module::syncfile();
/* Replace 'Handler' field with 'Assigned to' field for Products and Services - ends */

ExecutePQuery("UPDATE vtiger_entityname SET fieldname = 'firstname,lastname' WHERE tabid= ? ", array($contactTab));
ExecutePQuery("UPDATE vtiger_entityname SET fieldname = 'firstname,lastname' WHERE tabid= ? ", array($leadTab));
ExecutePQuery("UPDATE vtiger_entityname SET fieldname = 'first_name,last_name' WHERE tabid= ? ", array($usersTab));

require_once 'include/utils/utils.php';

$usersQuery = "SELECT * FROM vtiger_users";
$usersResult = $adb->query($usersQuery);
$usersCount = $adb->num_rows($usersResult);
for($i=0;$i<$usersCount;++$i){
	$userId = $adb->query_result($usersResult,$i,'id');
	$userName = $adb->query_result($usersResult,$i,'user_name');
	$firstName = $adb->query_result($usersResult,$i,'first_name');
	$lastName = $adb->query_result($usersResult,$i,'last_name');
	$fullName = getFullNameFromQResult($usersResult, $i, 'Users');
	$oldFullName = $lastName.' '.$firstName;

	ExecutePQuery("UPDATE vtiger_cvadvfilter SET value=? WHERE columnname LIKE '%:assigned_user_id:%' AND value=?", array($fullName, $oldFullName));
	ExecutePQuery("UPDATE vtiger_cvadvfilter SET value=? WHERE columnname LIKE '%:modifiedby:%' AND value=?", array($fullName, $oldFullName));
	ExecutePQuery("UPDATE vtiger_cvadvfilter SET value=? WHERE columnname LIKE '%:assigned_user_id1:%' AND value=?", array($fullName, $oldFullName));
	ExecutePQuery("UPDATE vtiger_relcriteria SET value=? WHERE columnname LIKE 'vtiger_users%:user_name%' AND value=?", array($fullName, $oldFullName));
	ExecutePQuery("UPDATE vtiger_relcriteria SET value=? WHERE columnname LIKE '%:modifiedby:%' AND value=?", array($fullName, $oldFullName));

	ExecutePQuery("UPDATE vtiger_cvadvfilter SET comparator='c'
						WHERE (columnname LIKE '%:assigned_user_id%:' OR columnname LIKE '%:assigned_user_id1%:' OR columnname LIKE '%:modifiedby%:')
								AND (comparator='s' OR comparator='ew')", array());
	ExecutePQuery("UPDATE vtiger_relcriteria SET comparator='c'
						WHERE (columnname LIKE 'vtiger_users%:user_name%' OR columnname LIKE '%:modifiedby%:')
								AND (comparator='s' OR comparator='ew')", array());
}

$replaceReportColumnsList = array(
	'vtiger_accountAccounts:accountname:Accounts_Member_Of:account_id:V' =>
	'vtiger_account:parentid:Accounts_Member_Of:account_id:V',
	'vtiger_accountContacts:accountname:Contacts_Account_Name:account_id:V' =>
	'vtiger_contactdetails:accountid:Contacts_Account_Name:account_id:V',
	'vtiger_contactdetailsContacts:lastname:Contacts_Reports_To:contact_id:V' =>
	'vtiger_contactdetails:reportsto:Contacts_Reports_To:contact_id:V',
	'vtiger_productsCampaigns:productname:Campaigns_Product:product_id:V' =>
	'vtiger_campaign:product_id:Campaigns_Product:product_id:V',
	'vtiger_productsFaq:productname:Faq_Product_Name:product_id:V' =>
	'vtiger_faq:product_id:Faq_Product_Name:product_id:V',
	'vtiger_contactdetailsInvoice:lastname:Invoice_Contact_Name:contact_id:V' =>
	'vtiger_invoice:contactid:Invoice_Contact_Name:contact_id:V',
	'vtiger_accountInvoice:accountname:Invoice_Account_Name:account_id:V' =>
	'vtiger_invoice:accountid:Invoice_Account_Name:account_id:V',
	'vtiger_campaignPotentials:campaignname:Potentials_Campaign_Source:campaignid:V' =>
	'vtiger_potential:campaignid:Potentials_Campaign_Source:campaignid:V',
	'vtiger_vendorRelProducts:vendorname:Products_Vendor_Name:vendor_id:V' =>
	'vtiger_products:vendor_id:Products_Vendor_Name:vendor_id:V',
	'vtiger_vendorRelPurchaseOrder:vendorname:PurchaseOrder_Vendor_Name:vendor_id:V' =>
	'vtiger_purchaseorder:vendorid:PurchaseOrder_Vendor_Name:vendor_id:V',
	'vtiger_contactdetailsPurchaseOrder:lastname:PurchaseOrder_Contact_Name:contact_id:V' =>
	'vtiger_purchaseorder:contactid:PurchaseOrder_Contact_Name:contact_id:V',
	'vtiger_potentialRelQuotes:potentialname:Quotes_Potential_Name:potential_id:V' =>
	'vtiger_quotes:potentialid:Quotes_Potential_Name:potential_id:V',
	'vtiger_contactdetailsQuotes:lastname:Quotes_Contact_Name:contact_id:V' =>
	'vtiger_quotes:contactid:Quotes_Contact_Name:contact_id:V',
	'vtiger_accountQuotes:accountname:Quotes_Account_Name:account_id:V' =>
	'vtiger_quotes:accountid:Quotes_Account_Name:account_id:V',
	'vtiger_quotesSalesOrder:subject:SalesOrder_Quote_Name:quote_id:V' =>
	'vtiger_salesorder:quoteid:SalesOrder_Quote_Name:quote_id:V',
	'vtiger_contactdetailsSalesOrder:lastname:SalesOrder_Contact_Name:contact_id:V' =>
	'vtiger_salesorder:contactid:SalesOrder_Contact_Name:contact_id:V',
	'vtiger_accountSalesOrder:accountname:SalesOrder_Account_Name:account_id:V' =>
	'vtiger_salesorder:accountid:SalesOrder_Account_Name:account_id:V',
	'vtiger_crmentityRelHelpDesk:setype:HelpDesk_Related_To:parent_id:V' =>
	'vtiger_troubletickets:parent_id:HelpDesk_Related_To:parent_id:V',
	'vtiger_productsRel:productname:HelpDesk_Product_Name:product_id:V' =>
	'vtiger_troubletickets:product_id:HelpDesk_Product_Name:product_id:V',
	'vtiger_crmentityRelCalendar:setype:Calendar_Related_To:parent_id:V' =>
	'vtiger_seactivityrel:crmid:Calendar_Related_To:parent_id:V',
	'vtiger_contactdetailsCalendar:lastname:Calendar_Contact_Name:contact_id:V' =>
	'vtiger_cntactivityrel:contactid:Calendar_Contact_Name:contact_id:V',
);

foreach ($replaceReportColumnsList as $oldName => $newName) {
	ExecutePQuery('UPDATE vtiger_selectcolumn SET columnname=? WHERE columnname=?', array($newName, $oldName));
	ExecutePQuery('UPDATE vtiger_relcriteria SET columnname=? WHERE columnname=?', array($newName, $oldName));
	ExecutePQuery('UPDATE vtiger_reportsortcol SET columnname=? WHERE columnname=?', array($newName, $oldName));
}

// Report Charts - tables creation
ExecutePQuery("CREATE TABLE if not exists vtiger_homereportchart (stuffid int(19) PRIMARY KEY, reportid int(19), reportcharttype varchar(100))", array());
ExecutePQuery("CREATE TABLE vtiger_reportgroupbycolumn(reportid int(19),sortid int(19),sortcolname varchar(250),dategroupbycriteria varchar(250))", array());
ExecutePQuery("ALTER TABLE vtiger_reportgroupbycolumn add constraint fk_1_vtiger_reportgroupbycolumn FOREIGN KEY (reportid) REFERENCES vtiger_report(reportid) ON DELETE CASCADE", array());

ExecutePQuery("DELETE FROM vtiger_time_zone WHERE time_zone = 'Kwajalein'", array());
ExecutePQuery("UPDATE vtiger_users SET time_zone='UTC' WHERE time_zone='Kwajalein'", array());

$serviceContractsInstance = Vtiger_Module::getInstance('ServiceContracts');
$helpDeskInstance = Vtiger_Module::getInstance("HelpDesk");
$helpDeskInstance->setRelatedList($serviceContractsInstance,"Service Contracts",Array('ADD','SELECT'));

ExecutePQuery("UPDATE vtiger_field SET uitype=11 WHERE fieldname IN ('phone_work', 'phone_mobile', 'phone_fax', 'phone_home', 'phone_other')
							AND tabid IN (SELECT tabid FROM vtiger_tab WHERE name='Users')", array());

$migrationlog->debug("\n\nDB Changes from 5.3.0 to 5.4.0RC -------- Ends \n\n");
?>