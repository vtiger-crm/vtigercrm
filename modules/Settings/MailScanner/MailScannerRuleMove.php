<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/

require_once('modules/Settings/MailScanner/core/MailScannerRule.php');

$mode = $_REQUEST['mode'];
$targetruleid = $_REQUEST['targetruleid'];
$ruleid = $_REQUEST['ruleid'];
	
if($mode == 'rulemove_up') {
	Vtiger_MailScannerRule::resetSequence($ruleid, $targetruleid);
} else if($mode == 'rulemove_down') {
	Vtiger_MailScannerRule::resetSequence($ruleid, $targetruleid);
}

include('modules/Settings/MailScanner/MailScannerRule.php');

?>
