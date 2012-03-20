<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
	require_once("Smarty_setup.php");
	require_once("include/utils/CommonUtils.php");
	require_once("include/events/SqlResultIterator.inc");
	
	require_once("VTTaskManager.inc");
	require_once("VTWorkflowApplication.inc");
require_once("VTWorkflowUtils.php");

	
	function vtDisplayTaskList($adb, $requestUrl, $current_language){
		global $theme, $app_strings;
		$image_path = "themes/$theme/images/";
		
		$util = new VTWorkflowUtils();
		$module = new VTWorkflowApplication("tasklist");
		$mod = return_module_language($current_language, $module->name);
	
		if(!$util->checkAdminAccess()){
			$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NOT_ADMIN']);
			$util->redirectTo($errorUrl, $mod['LBL_ERROR_NOT_ADMIN']);
			return;
		}

		$smarty = new vtigerCRM_Smarty();
		$tm = new VTTaskManager($adb);
		$smarty->assign("tasks", $tm->getTasks());
		$smarty->assign("moduleNames", array("Contacts", "Applications"));
		$smarty->assign("taskTypes", array("VTEmailTask", "VTDummyTask"));
		$smarty->assign("returnUrl", $requestUrl);
		
		$smarty->assign("MOD", return_module_language($current_language,'Settings'));
		$smarty->assign("APP", $app_strings);
		$smarty->assign("THEME", $theme);
		$smarty->assign("IMAGE_PATH",$image_path);
		$smarty->assign("MODULE_NAME", $module->label);
		$smarty->assign("PAGE_NAME", 'Task List');
		$smarty->assign("PAGE_TITLE", 'List available tasks');
		$smarty->assign("moduleName", $moduleName);
		$smarty->display("{$module->name}/ListTasks.tpl");
	}
	vtDisplayTaskList($adb, $_SERVER["REQUEST_URI"], $current_language);
?>