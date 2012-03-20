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
require_once("include/events/VTWSEntityType.inc");

require_once("VTWorkflowApplication.inc");
require_once("VTTaskManager.inc");
require_once("VTWorkflowManager.inc");
require_once("VTWorkflowUtils.php");
	function vtTaskEdit($adb, $request, $current_language, $app_strings){
		global $theme;
		$util = new VTWorkflowUtils();
		$image_path = "themes/$theme/images/";

		$module = new VTWorkflowApplication('edittask');

		$mod = return_module_language($current_language, $module->name);		

		if(!$util->checkAdminAccess()){
			$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NOT_ADMIN']);
			$util->redirectTo($errorUrl, $mod['LBL_ERROR_NOT_ADMIN']);
			return;
		}

		$smarty = new vtigerCRM_Smarty();
		$tm = new VTTaskManager($adb);
		$smarty->assign('edit',isset($request["task_id"]));
		if(isset($request["task_id"])){
			$task = $tm->retrieveTask($request["task_id"]);
			$workflowId=$task->workflowId;
		}else{
			$workflowId = $request["workflow_id"];
			$taskClass = $request["task_type"];
			$task = $tm->createTask($taskClass, $workflowId);
		}

		if($task==null){
			$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NO_TASK']);
			$util->redirectTo($errorUrl, $mod['LBL_ERROR_NO_TASK']);
			return;
		}
		
		$wm = new VTWorkflowManager($adb);
		$workflow = $wm->retrieve($workflowId);
		if($workflow==null){
			$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NO_WORKFLOW']);
			$util->redirectTo($errorUrl, $mod['LBL_ERROR_NO_WORKFLOW']);
			return;
		}

		
		$smarty->assign("workflow", $workflow);
		$smarty->assign("returnUrl", $request["return_url"]);
		$smarty->assign("task", $task);
		$smarty->assign("taskType", $taskClass);
		$smarty->assign("saveType", $request['save_type']);
		$taskClass = get_class($task);
		$smarty->assign("taskTemplate", "{$module->name}/taskforms/$taskClass.tpl");
		$et = VTWSEntityType::usingGlobalCurrentUser($workflow->moduleName);
		$smarty->assign("entityType", $et);
		$smarty->assign('entityName', $workflow->moduleName);
		$smarty->assign("fieldNames", $et->getFieldNames());
		
		$dateFields = array();
		$fieldTypes = $et->getFieldTypes();
		$fieldLabels = $et->getFieldLabels();
		foreach($fieldTypes as $name => $type){
			if($type->type=='Date' || $type->type=='DateTime'){
				$dateFields[$name] = $fieldLabels[$name];
			}
		}
		
		$smarty->assign('dateFields', $dateFields);
		
		
		if($task->trigger!=null){
			$trigger = $task->trigger;
			$days = $trigger['days'];
			if ($days < 0){
				$days*=-1;
				$direction = 'before';
			}else{
				$direction = 'after';
			}
			$smarty->assign('trigger', array('days'=>$days, 'direction'=>$direction, 
			  'field'=>$trigger['field']));
		}
		$curr_date="(general : (__VtigerMeta__) date)";
		$curr_time='(general : (__VtigerMeta__) time)';
				
		$smarty->assign("DATE",$curr_date);
		$smarty->assign("TIME",$curr_time);
		$smarty->assign("MOD", array_merge(
			return_module_language($current_language,'Settings'),
			return_module_language($current_language, 'Calendar'),
			return_module_language($current_language, $module->name)));
		$smarty->assign("APP", $app_strings);
		$smarty->assign("dateFormat", parse_calendardate($app_strings['NTC_DATE_FORMAT']));
		$smarty->assign("IMAGE_PATH",$image_path);
		$smarty->assign("THEME", $theme);
		$smarty->assign("MODULE_NAME", $module->label);
		$smarty->assign("PAGE_NAME", $mod['LBL_EDIT_TASK']);
		$smarty->assign("PAGE_TITLE", $mod['LBL_EDIT_TASK_TITLE']);
		
		$smarty->assign("module", $module);
		$smarty->display("{$module->name}/EditTask.tpl");
	}
	vtTaskEdit($adb, $_REQUEST, $current_language, $app_strings);
?>
