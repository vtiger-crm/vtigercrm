<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule;

require_once("modules/$currentModule/$currentModule.php");

$focus = new $currentModule();

$record = $_REQUEST['record'];
$module = $_REQUEST['module'];
$return_module = $_REQUEST['return_module'];
$return_action = $_REQUEST['return_action'];
$parenttab = $_REQUEST['parenttab'];
$return_id = $_REQUEST['return_id'];

DeleteEntity($currentModule, $return_module, $focus, $record, $return_id);

if($_REQUEST['parenttab']) $parenttab = $_REQUEST['parenttab'];

header("Location: index.php?module=$return_module&action=$return_action&record=$return_id&parenttab=$parenttab&relmodule=$module");

?>