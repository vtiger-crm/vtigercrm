{include file='com_vtiger_workflow/Header.tpl'}

{include file='com_vtiger_workflow/EditWorkflowIncludes.tpl'}

{include file='com_vtiger_workflow/WorkflowTemplatePopup.tpl'}

{include file='com_vtiger_workflow/NewTaskPopup.tpl'}
<!--Error message box popup-->
{include file='com_vtiger_workflow/ErrorMessageBox.tpl'}
<!--Done popups-->

{include file='SetMenu.tpl'}
<div id="view">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
	<form name="edit_workflow_form" action="index.php" id="edit_workflow_form" onsubmit="VtigerJS_DialogBox.block();">
		{include file='com_vtiger_workflow/EditWorkflowMeta.tpl'}

		{include file='com_vtiger_workflow/EditWorkflowBasicInfo.tpl'}
		<br>
		{include file='com_vtiger_workflow/EditWorkflowTriggerTypes.tpl'}
		<br>
		{include file='com_vtiger_workflow/ListConditions.tpl'}
	</form>

	{if $saveType eq "edit"}
		<br>
		{include file='com_vtiger_workflow/ListTasks.tpl'}
	{/if}
</div>
<div id="dump" style="display:None;"></div>
{include file='com_vtiger_workflow/Footer.tpl'}