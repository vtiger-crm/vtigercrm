<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

$module_export = $_REQUEST['module_export'];

require_once("vtlib/Vtiger/Package.php");
require_once("vtlib/Vtiger/Module.php");

$package = new Vtiger_Package();
$package->export(Vtiger_Module::getInstance($module_export),'',"$module_export.zip",true);
exit;
?>