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

require_once("VTWorkflowManager.inc");
require_once("VTWorkflowApplication.inc");
require_once("VTWorkflowUtils.php");

function vtGetModules($adb){
	$modules_not_supported = array('Documents','Calendar','Emails','Faq','Events','PBXManager','Users'); 
	$sql="select distinct vtiger_field.tabid, name
			from vtiger_field 
			inner join vtiger_tab 
				on vtiger_field.tabid=vtiger_tab.tabid 
			where vtiger_tab.name not in(".generateQuestionMarks($modules_not_supported).") and vtiger_tab.isentitytype=1 and vtiger_tab.presence = 0 ";
	$it = new SqlResultIterator($adb, $adb->pquery($sql,array($modules_not_supported)));
	$modules = array();
	foreach($it as $row){
		$modules[] = $row->name;
	}
	return $modules;
}

function vtDisplayWorkflowList($adb, $request, $requestUrl, $app_strings, $current_language){
	global $theme;
	$image_path = "themes/$theme/images/";

	$module = new VTWorkflowApplication("workflowlist");
	$util = new VTWorkflowUtils();
	$mod = return_module_language($current_language, $module->name);

	if(!$util->checkAdminAccess()){
		$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NOT_ADMIN']);
		$util->redirectTo($errorUrl, $mod['LBL_ERROR_NOT_ADMIN']);
		return;
	}

	$smarty = new vtigerCRM_Smarty();
	$wfs = new VTWorkflowManager($adb);
	$smarty->assign("moduleNames", $util->vtGetModules($adb));
	$smarty->assign("returnUrl", $requestUrl);

	$listModule =$request['list_module'];
	$smarty->assign("listModule", $listModule);
	if($listModule==null || strtolower($listModule)=="all"){
		$smarty->assign("workflows", $wfs->getWorkflows());
	}else{
		$smarty->assign("workflows", $wfs->getWorkflowsForModule($listModule));
	}

	$smarty->assign("MOD",array_merge(
	return_module_language($current_language,'Settings'),
	return_module_language($current_language, $module->name)));
	$smarty->assign("APP", $app_strings);
	$smarty->assign("THEME", $theme);
	$smarty->assign("IMAGE_PATH",$image_path);
	$smarty->assign("MODULE_NAME", $module->label);
	$smarty->assign("PAGE_NAME", $mod['LBL_WORKFLOW_LIST']);
	$smarty->assign("PAGE_TITLE", $mod['LBL_AVAILABLE_WORKLIST_LIST']);
	$smarty->assign("module", $module);
	$smarty->display("{$module->name}/ListWorkflows.tpl");
}
vtDisplayWorkflowList($adb, $_REQUEST, $_SERVER["REQUEST_URI"], $app_strings, $current_language);
?>
