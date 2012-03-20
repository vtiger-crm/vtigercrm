{include file='com_vtiger_workflow/Header.tpl'}
<script src="modules/{$module->name}/resources/jquery-1.2.6.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/json2.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/fieldvalidator.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/editworkflowscript.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	jQuery.noConflict();
	fn.addStylesheet('modules/{$module->name}/resources/style.css');
	var moduleName = '{$workflow->moduleName}';
{if $workflow->test}
	var conditions = JSON.parse('{$workflow->test}');
{else}
	var conditions = null;
{/if}
	editworkflowscript(jQuery, conditions);
</script>

<!-- A pop to create a new template -->
<div id="new_template_popup" class='layerPopup' style="display:none;">
	<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerHeadingULine">
		<tr>
			<td width="60%" align="left" class="layerPopupHeading">
				{$MOD.LBL_NEW_TEMPLATE}			
			</td>
			<td width="40%" align="right">
				<a href="javascript:void(0);" id="new_template_popup_close">
					<img border="0" align="middle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/>
				</a>
			</td>
		</tr>
	</table>

	<form action="index.php" method="get" accept-charset="utf-8" onsubmit="VtigerJS_DialogBox.block();">
	<div class="popup_content">
		<table width="100%" cellspacing="0" cellpadding="5" border="0" class="small">
		<tr align="left">
			<td width="40px" nowrap="nowrap"><font color="red">*</font> {$APP.LBL_TITLE}</td>
			<td><input type="text" name="title" class='detailedViewTextBox'></td>
		</tr>
		</table> 
		<input type="hidden" name="module_name" value="{$workflow->moduleName}">
		<input type="hidden" name="save_type" value="new" id="save_type_new">
		<input type="hidden" name="module" value="{$module->name}" id="save_module">
		<input type="hidden" name="action" value="savetemplate" id="save_action">
		<input type="hidden" name="return_url" value="{$newTaskReturnUrl}" id="save_return_url">
		<input type="hidden" name="workflow_id" value="{$workflow->id}">
		
		<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">
		<tr><td align="center">
			<input type="submit" class="crmButton small save" value="{$APP.LBL_CREATE_BUTTON_LABEL}" name="save" id='new_template_popup_save'/> 
			<input type="button" class="crmButton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL} " name="cancel" id='new_template_popup_cancel'/>
		</td></tr>
		</table>
	</div>	
	</form>
</div>

<!-- A popup to create a new task-->
<div id="new_task_popup" class='layerPopup' style="display:none;">
	<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerHeadingULine">
		<tr>
			<td width="60%" align="left" class="layerPopupHeading">
				{$MOD.LBL_CREATE_TASK}
				</td>
			<td width="40%" align="right">
				<a href="javascript:void(0);" id="new_task_popup_close">
					<img border="0" align="middle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/>
				</a>
			</td>
		</tr>
	</table>

	<form action="index.php" method="get" accept-charset="utf-8" onsubmit="VtigerJS_DialogBox.block();">
	<div class="popup_content" align="left">
		{$MOD.LBL_CREATE_TASK_OF_TYPE} 
		<select name="task_type" class="small">
	{foreach item=taskType from=$taskTypes}
			<option value='{$taskType}'>
				{$taskType|@getTranslatedString:$module->name}
			</option>
	{/foreach}
		</select>
		<input type="hidden" name="module_name" value="{$workflow->moduleName}">
		<input type="hidden" name="save_type" value="new" id="save_type_new">
		<input type="hidden" name="module" value="{$module->name}" id="save_module">
		<input type="hidden" name="action" value="edittask" id="save_action">
		<input type="hidden" name="return_url" value="{$newTaskReturnUrl}" id="save_return_url">
		<input type="hidden" name="workflow_id" value="{$workflow->id}">
	</div>
	<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">
		<tr><td align="center">
			<input type="submit" class="crmButton small save" value="{$APP.LBL_CREATE_BUTTON_LABEL}" name="save" id='new_task_popup_save'/> 
			<input type="button" class="crmButton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL} " name="cancel" id='new_task_popup_cancel'/>
		</td></tr>
	</table>
	</form>
</div>
<!--Error message box popup-->
{include file='com_vtiger_workflow/ErrorMessageBox.tpl'}
<!--Done popups-->

{include file='SetMenu.tpl'}
<div id="view">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
	<form name="edit_workflow_form" action="index.php" id="edit_workflow_form" onsubmit="VtigerJS_DialogBox.block();">
		<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td class="big" nowrap="nowrap">
					<strong>{$MOD.LBL_SUMMARY}</strong>
				</td>
				<td align="right">
					{if $saveType eq "edit"}
					<input type="button" class="crmButton create small" value="{$MOD.LBL_NEW_TEMPLATE}" id="new_template"/>
					{/if}
					<input type="submit" id="save_submit" value="{$APP.LBL_SAVE_LABEL}" class="crmButton small save">
					<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" 
						onclick="window.location.href='index.php?module=com_vtiger_workflow&action=workflowlist&parenttab=Settings'">
				</td>
			</tr>
		</table>
		<table border="0" cellpadding="5" cellspacing="0" width="100%">
			<tr>
				<td class="dvtCellLabel" align=right width=20%><b><span style='color:red;'>*</span> {$APP.LBL_UPD_DESC}</b></td>
				<td class="dvtCellInfo" align="left"><input type="text" class="detailedViewTextBox" name="description" id="save_description" value="{$workflow->description}"></td>
			</tr>
			<tr>
				<td class="dvtCellLabel" align=right width=20%><b>{$APP.LBL_MODULE}</b></td>
				<td class="dvtCellInfo" align="left">{$workflow->moduleName|@getTranslatedString:$workflow->moduleName}</td>
			</tr>
		</table>
		<br>
		<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td class="big" nowrap="nowrap">
					<strong>{$MOD.LBL_WHEN_TO_RUN_WORKFLOW}</strong>
				</td>
			</tr>
		</table>
		<table border="0" >
			<tr><td><input type="radio" name="execution_condition" value="ON_FIRST_SAVE" 
				{if $workflow->executionConditionAsLabel() eq 'ON_FIRST_SAVE'}checked{/if}/></td> 
				<td>{$MOD.LBL_ONLY_ON_FIRST_SAVE}.</td></tr>
			<tr><td><input type="radio" name="execution_condition" value="ONCE" 
				{if $workflow->executionConditionAsLabel() eq 'ONCE'}checked{/if} /></td>
				<td>{$MOD.LBL_UNTIL_FIRST_TIME_CONDITION_TRUE}.</td></tr>
			<tr><td><input type="radio" name="execution_condition" value="ON_EVERY_SAVE" 
				{if $workflow->executionConditionAsLabel() eq 'ON_EVERY_SAVE'}checked{/if}/></td>
				<td>{$MOD.LBL_EVERYTIME_RECORD_SAVED}.</td></tr>
			<tr><td><input type="radio" name="execution_condition" value="ON_MODIFY" 
				{if $workflow->executionConditionAsLabel() eq 'ON_MODIFY'}checked{/if}/></td>
				<td>{$MOD.LBL_ON_MODIFY}.</td></tr>
					

<!-- Workflow Conditions -->		
		<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td class="big" nowrap="nowrap">
					<strong>{$MOD.LBL_CONDITIONS}</strong>
				</td>
				<td class="small" align="right">
					<span id="workflow_loading" style="display:none">
					  <b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0">
					</span>
					<input type="button" class="crmButton create small" 
						value="{$MOD.LBL_NEW_CONDITION_BUTTON_LABEL}" id="save_conditions_add" style='display: none;'/>
				</td>
			</tr>
		</table>
		<br>
		<span id="status_message"></span>
		
		<div id="save_conditions"></div>
		
		<input type="hidden" name="module_name" value="{$workflow->moduleName}" id="save_modulename">
		<input type="hidden" name="save_type" value="{$saveType}" id="save_savetype">
		{if $saveType eq "edit"}
		<input type="hidden" name="workflow_id" value="{$workflow->id}">
{/if}
		<input type="hidden" name="conditions" value="" id="save_conditions_json"/>
		<input type="hidden" name="action" value="saveworkflow" id="some_name">
		<input type="hidden" name="module" value="{$module->name}" id="some_name">
	</form>
{if $saveType eq "edit"}
	<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td class="big" nowrap="nowrap">
				<strong>{$MOD.LBL_TASKS}</strong>
			</td>
			<td class="small" align="right">
				<input type="button" class="crmButton create small" value="{$MOD.LBL_NEW_TASK_BUTTON_LABEL}" id='new_task'/>
			</td>
		</tr>
	</table>
	<table class="listTableTopButtons" width="100%" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td class="small"> <span id="status_message"></span> </td>			
		</tr>
	</table>
	<table class="listTable" width="100%" border="0" cellspacing="1" cellpadding="5" id='expressionlist'>
		<tr>
			<td class="colHeader small" width="70%">
				{$MOD.LBL_TASK}
			</td>
			<td class="colHeader small" width="15%">
				{$MOD.LBL_STATUS}
			</td>
			<td class="colHeader small" width="15%">
				{$MOD.LBL_LIST_TOOLS}
			</td>
		</tr>
{foreach item=task from=$tasks}
		<tr>
			<td class="listTableRow small">{$task->summary|@to_html}</td>
			<td class="listTableRow small">{if $task->active}Active{else}Inactive{/if}</td>
			<td class="listTableRow small">
				<a href="{$module->editTaskUrl($task->id)}">
					<img border="0" title="Edit" alt="Edit" \
						style="cursor: pointer;" id="expressionlist_editlink_{$task->id}" \
						src="{'editfield.gif'|@vtiger_imageurl:$THEME}"/>
				</a>
				<a href="{$module->deleteTaskUrl($task->id)}">
					<img border="0" title="Delete" alt="Delete"\
			 			src="{'delete.gif'|@vtiger_imageurl:$THEME}" \
						style="cursor: pointer;" id="expressionlist_deletelink_{$task->id}"/>
				</a>
			</td>
		</tr>
{/foreach}
	</table>
{/if}
</div>
<div id="dump" style="display:None;"></div>
{include file='com_vtiger_workflow/Footer.tpl'}
