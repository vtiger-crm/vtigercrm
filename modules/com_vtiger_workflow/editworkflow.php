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
require_once("include/Zend/Json.php");

require_once("include/events/SqlResultIterator.inc");
require_once("include/events/VTWSEntityType.inc");

require_once("VTWorkflowManager.inc");
require_once("VTTaskManager.inc");
require_once("VTWorkflowApplication.inc");
require_once "VTWorkflowTemplateManager.inc";
require_once "VTWorkflowUtils.php";
require_once 'config.help.link.php';

function vtWorkflowEdit($adb, $request, $requestUrl, $current_language, $app_strings){

	global $theme, $helpLinks;
	$util = new VTWorkflowUtils();

	$image_path = "themes/$theme/images/";

	$module = new VTWorkflowApplication("editworkflow");

	$mod = return_module_language($current_language, $module->name);


	if(!$util->checkAdminAccess()){
		$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NOT_ADMIN']);
		$util->redirectTo($errorUrl, $mod['LBL_ERROR_NOT_ADMIN']);
		return;
	}


	$smarty = new vtigerCRM_Smarty();
	if($request['source']=='from_template'){
		$tm = new VTWorkflowTemplateManager($adb);
		$template = $tm->retrieveTemplate($request['template_id']);
		$workflow = $tm->createWorkflow($template);
	}else{
		$wfs = new VTWorkflowManager($adb);
		if(isset($request["workflow_id"])){
			$workflow = $wfs->retrieve($request["workflow_id"]);
		}else{
			$moduleName=$request["module_name"];
			$workflow = $wfs->newWorkflow($moduleName);
		}
	}

	if($workflow==null){
		$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NO_WORKFLOW']);
		$util->redirectTo($errorUrl, $mod['LBL_ERROR_NO_WORKFLOW']);
		return;
	}
	$workflow->test = addslashes($workflow->test);
	$tm = new VTTaskManager($adb);
	$tasks = $tm->getTasksForWorkflow($workflow->id);
	$smarty->assign("tasks", $tasks);
	$taskTypes = $tm->getTaskTypes($workflow->moduleName);
	$smarty->assign("taskTypes", $taskTypes);
	$smarty->assign("newTaskReturnUrl", vtlib_purify($requestUrl));

	$smarty->assign("returnUrl", vtlib_purify($request["return_url"]));
	$smarty->assign("APP", $app_strings);
	$smarty->assign("MOD", array_merge(
	return_module_language($current_language,'Settings'),
	return_module_language($current_language, $module->name)));
	$smarty->assign("THEME", $theme);
	$smarty->assign("IMAGE_PATH", $image_path);
	$smarty->assign("MODULE_NAME", $module->label);
	$smarty->assign("PAGE_NAME", $mod['LBL_EDIT_WORKFLOW']);
	$smarty->assign("PAGE_TITLE", $mod['LBL_EDIT_WORKFLOW_TITLE']);

	$smarty->assign("workflow", $workflow);
	$smarty->assign("saveType", isset($workflow->id)?"edit":"new");
	$smarty->assign("module", $module);

	$smarty->assign("WORKFLOW_TRIGGER_TYPES_HELP_LINK", $helpLinks['WORKFLOW_TRIGGER_TYPES']);

	$smarty->display("{$module->name}/EditWorkflow.tpl");
}
vtWorkflowEdit($adb, $_REQUEST, $_SERVER["REQUEST_URI"], $current_language, $app_strings);
?>