<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/** Load the configuration file common to cron tasks. */
require_once('cron/config.cron.php');
global $VTIGER_CRON_CONFIGURATION;

/** 
 * To make sure we can work with command line and direct browser invocation.
 */
if($argv) {
	if(!isset($_REQUEST)) $_REQUEST = Array();

	for($index = 0; $index < count($argv); ++$index) {
		$value = $argv[$index];
		if(strpos($value, '=') === false) continue;

		$keyval = explode('=', $value);
		if(!isset($_REQUEST[$keyval[0]])) {
			$_REQUEST[$keyval[0]] = $keyval[1];
		}
	}
	
	/* If app_key is not set, pick the value from cron configuration */
	if(empty($_REQUEST['app_key'])) $_REQUEST['app_key'] = $VTIGER_CRON_CONFIGURATION['app_key'];
}

/** All service invocation needs have valid app_key parameter sent */
require_once('config.inc.php');

/** Verify the script call is from trusted place. */
global $application_unique_key;
if($_REQUEST['app_key'] != $application_unique_key) {
	echo "Access denied!";
	exit;
}

/** Include the service file */
$service = $_REQUEST['service'];
if($service == 'MailScanner') {
	include_once('cron/MailScanner.service');
}
if($service == 'RecurringInvoice') {
	include_once('cron/modules/SalesOrder/RecurringInvoice.service');
}

if($service == 'com_vtiger_workflow'){
	include_once('cron/modules/com_vtiger_workflow/com_vtiger_workflow.service');
}

if($service == 'VtigerBackup'){
	include_once('cron/modules/VtigerBackup/VtigerBackup.service');
}

?>