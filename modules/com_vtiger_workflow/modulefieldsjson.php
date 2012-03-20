<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
	require_once "include/Zend/Json.php";
	require_once("include/events/VTWSEntityType.inc");
	
	function vtModuleTypeInfoJson($adb, $request){
		$moduleName = $request['module_name'];
		$et = VTWSEntityType::usingGlobalCurrentUser($moduleName);
		echo Zend_Json::encode($et->getFieldLabels());
	}
	vtModuleTypeInfoJson($adb, $_REQUEST);
?>