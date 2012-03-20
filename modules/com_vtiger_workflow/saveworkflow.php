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
require_once("include/Zend/Json.php");
require_once("VTWorkflowApplication.inc");
require_once("VTWorkflowManager.inc");
require_once("VTWorkflowUtils.php");

	function vtWorkflowSave($adb, $request){
		$util = new VTWorkflowUtils();
		$module = new VTWorkflowApplication("saveworkflow");
		$mod = return_module_language($current_language, $module->name);

		if(!$util->checkAdminAccess()){
			$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NOT_ADMIN']);
			$util->redirectTo($errorUrl, $mod['LBL_ERROR_NOT_ADMIN']);
			return;
		}

		$description = from_html($request["description"]);
		$moduleName = $request["module_name"];
		$conditions = $request["conditions"];
		$taskId = $request["task_id"];
		$saveType=$request["save_type"];
		$executionCondition = $request['execution_condition'];
		$wm = new VTWorkflowManager($adb);
		if($saveType=='new'){
			$wf = $wm->newWorkflow($moduleName);
			$wf->description = $description;
			$wf->test = $conditions;
			$wf->taskId = $taskId;
			$wf->executionConditionAsLabel($executionCondition);
			$wm->save($wf);
		}else if($saveType=='edit'){
			$wf = $wm->retrieve($request["workflow_id"]);
			$wf->description = $description;
			$wf->test = $conditions;
			$wf->taskId = $taskId;
			$wf->executionConditionAsLabel($executionCondition);
			$wm->save($wf);
		}else{
			throw new Exception();
		}
		if(isset($request["return_url"])){
			$returnUrl=$request["return_url"];
		}else{
			$returnUrl=$module->editWorkflowUrl($wf->id);
		}
		?>
		<script type="text/javascript" charset="utf-8">
			window.location="<?=$returnUrl?>";
		</script>
		<a href="<?=$returnUrl?>">Return</a>
		<?php
		
	}
	
	vtWorkflowSave($adb, $_REQUEST);
?>