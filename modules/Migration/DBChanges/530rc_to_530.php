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

//5.2.1 to 5.3.0RC database changes

$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.3.0RC to 5.3.0 -------- Starts \n\n");

ExecuteQuery("UPDATE vtiger_field SET quickcreate=0 WHERE fieldname='time_start' AND tabid=". getTabid('Calendar'));

ExecuteQuery("ALTER TABLE vtiger_links ADD COLUMN handler_path VARCHAR(128) DEFAULT null");
ExecuteQuery("ALTER TABLE vtiger_links ADD COLUMN handler_class VARCHAR(32) DEFAULT null");
ExecuteQuery("ALTER TABLE vtiger_links ADD COLUMN handler VARCHAR(32) DEFAULT null");

ExecuteQuery("UPDATE vtiger_organizationdetails SET logoname='vtiger-crm-logo.gif' WHERE logoname='vtiger-crm-logo.jpg'");
ExecuteQuery("UPDATE vtiger_organizationdetails SET organizationname='vtiger Systems Pvt Ltd' WHERE organizationname='vtiger'");

updateVtlibModule('MailManager', "packages/vtiger/mandatory/MailManager.zip");
updateVtlibModule('Mobile', "packages/vtiger/mandatory/Mobile.zip");
updateVtlibModule('ConfigEditor', "packages/vtiger/mandatory/ConfigEditor.zip");
updateVtlibModule('ServiceContracts', "packages/vtiger/mandatory/ServiceContracts.zip");

$migrationlog->debug("\n\nDB Changes from 5.3.0RC to 5.3.0 -------- Ends \n\n");

?>