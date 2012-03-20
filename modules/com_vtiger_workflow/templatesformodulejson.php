<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/Zend/Json.php';
require_once 'VTWorkflowTemplateManager.inc';
function vtTemplatesForModuleJson($adb, $request){
	$moduleName = $request['module_name'];
	$tm = new VTWorkflowTemplateManager($adb); 
	$templates = $tm->getTemplatesForModule($moduleName);
	$arr = array();
	foreach($templates as $template){
		$arr[] = array("title"=>$template->title, 'id'=>$template->id);
	}
	echo Zend_Json::encode($arr);
}
vtTemplatesForModuleJson($adb, $_REQUEST);
?>