<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('modules/Reports/Reports.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

global $adb;

$local_log =& LoggerManager::getLogger('index');

$primarymodule_req = vtlib_purify($_REQUEST["primarymodule"]);
$sec_module_name = $_REQUEST["primarymodule"]."relatedmodule";
if(isset($_REQUEST[$sec_module_name])) $secondarymodule_req = vtlib_purify($_REQUEST[$sec_module_name]); 

if(count($secondarymodule_req)>0) {
	$secondarymodule_req = implode(":",$secondarymodule_req);
}

if($primarymodule_req!="")
{
	header("Location: index.php?action=NewReport1&module=Reports&primarymodule=$primarymodule_req&secondarymodule=$secondarymodule_req");	
}
?>