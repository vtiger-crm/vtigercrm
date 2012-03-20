<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('include/utils/utils.php');
require_once('include/Zend/Json.php');
require_once('include/events/include.inc');
require_once('modules/com_vtiger_workflow/include.inc');

/**
 * This is a utility function to load a dumped templates files
 * into vtiger
 * @param $filename The name of the file to load.
 */
function loadTemplates($filename){
	global $adb;
	$str = file_get_contents('fetchtemplates.out');
	$tm = new VTWorkflowTemplateManager($adb);
	$tm->loadTemplates($str);
}
?>