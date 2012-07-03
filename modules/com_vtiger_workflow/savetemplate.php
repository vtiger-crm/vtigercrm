<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once "include/utils/CommonUtils.php";
require_once "include/events/SqlResultIterator.inc";
require_once "include/Zend/Json.php";
require_once "VTWorkflowApplication.inc";
require_once "VTWorkflowManager.inc";
require_once "VTWorkflowTemplateManager.inc";
require_once "VTTaskManager.inc";
require_once "VTWorkflowUtils.php";

function vtSaveWorkflowTemplate($adb, $request){
	$util = new VTWorkflowUtils();
	$module = new VTWorkflowApplication("savetemplate");
	$mod = return_module_language($current_language, $module->name);
	
	if(!$util->checkAdminAccess()){
		$errorUrl = $module->errorPageUrl($mod['LBL_ERROR_NOT_ADMIN']);
		$util->redirectTo($errorUrl, $mod['LBL_ERROR_NOT_ADMIN']);
		return;
	}

	$title = $request['title'];
	$workflowId = $request['workflow_id'];
	$wfs = new VTworkflowManager($adb);
	$workflow = $wfs->retrieve($workflowId);
	$tm = new VTWorkflowTemplateManager($adb);
	$tpl = $tm->newTemplate($title, $workflow);
	$tm->saveTemplate($tpl);
	$returnUrl = $request['return_url'];
	?>
		<script type="text/javascript" charset="utf-8">
			 window.location="<?php echo $returnUrl?>";
		</script>
		<a href="<?php echo $returnUrl?>">Return</a>
	<?php
}
vtSaveWorkflowTemplate($adb, $_REQUEST);
?>