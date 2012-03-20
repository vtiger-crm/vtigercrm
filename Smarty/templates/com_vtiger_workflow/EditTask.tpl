{include file='com_vtiger_workflow/Header.tpl'}
<script src="modules/{$module->name}/resources/jquery-1.2.6.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/jquery.timepicker.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/json2.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/fieldvalidator.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/edittaskscript.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	jQuery.noConflict();
	fn.addStylesheet('modules/{$module->name}/resources/style.css');
	var returnUrl = '{$returnUrl}';
	var validator;
	edittaskscript(jQuery);
</script>

<!--Error message box popup-->
{include file='com_vtiger_workflow/ErrorMessageBox.tpl'}
<!--Done popups-->

{include file='SetMenu.tpl'}
<div id="view">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
	<form name="new_task" id="new_task_form" method="post" onsubmit="VtigerJS_DialogBox.block();">
	
		<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td class="big" nowrap="nowrap">
					<strong>{$MOD.LBL_SUMMARY}</strong>
				</td>
				<td class="small" align="right">
					<input type="submit" name="{$APP.LBL_SAVE_LABEL}" class="crmButton small save" value="{$APP.LBL_SAVE_BUTTON_LABEL}" id="save">
					<input type="button" id="edittask_cancel_button" class="crmbutton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
				</td>
			</tr>
		</table>
	
		<table border="0" cellpadding="5" cellspacing="0" width="100%">
			<tr>
				<td class="dvtCellLabel" align=right width=15% nowrap="nowrap"><b><font color="red">*</font> {$MOD.LBL_TASK_TITLE}</b></td>
				<td class="dvtCellInfo" align="left" ><input type="text" class="detailedViewTextBox" name="summary" value="{$task->summary}" id="save_summary"></td>
			</tr>
			<tr>
				<td class="dvtCellLabel" align=right width=15% nowrap="nowrap"><b>{$MOD.LBL_PARENT_WORKFLOW}</b></td>
				<td class="dvtCellInfo" align="left">
					{$workflow->description|@to_html}
					<input type="hidden" name="workflow_id" value="{$workflow->id}" id="save_workflow_id">
				</td>
			</tr>
			<tr>
				<td class="dvtCellLabel" align=right width=15% nowrap="nowrap"><b>{$MOD.LBL_STATUS}</b></td>
				<td class="dvtCellInfo" align="left">
					<select name="active" class="small">
						<option value="true">{$MOD.LBL_ACTIVE}</option>
						<option value="false" {if not $task->active}selected{/if}>{$MOD.LBL_INACTIVE}</option>
					</select> 
				</td>
			</tr>
		</table>
		
		<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
		<tr>
			<td width='15%' nowrap="nowrap"><input type="checkbox" name="check_select_date" value="" id="check_select_date" {if $trigger neq null}checked{/if}> 
			<b>{$MOD.MSG_EXECUTE_TASK_DELAY}</b></td>
			<td>
				<div id="select_date" style="display:none;">
					<input type="text" name="select_date_days" value="{$trigger.days}" id="select_date_days" class="small"> days 
					<select name="select_date_direction" class="small">
						<option {if $trigger.direction eq 'after'}selected{/if} value='after'>{$MOD.LBL_AFTER}</option>
						<option {if $trigger.direction eq 'after'}selected{/if} value='before'>{$MOD.LBL_BEFORE}</option>
					</select> 
					<select name="select_date_field" class="small">
		{foreach key=name item=label from=$dateFields}
						<option value='{$name}' {if $trigger->name eq $name}selected{/if}>
							{$label}
						</option>
		{/foreach}
					</select>					
				</div>				
			</td>
		</tr>
		</table>
		
		<table class="tableHeading" border="0"  width="100%" cellspacing="0" cellpadding="5">
			<tr>
				<td class="big" nowrap="nowrap">
					<strong>{$MOD.LBL_TASK_OPERATIONS}</strong>
				</td>
			</tr>
		</table>
{include file="$taskTemplate"}
		<input type="hidden" name="save_type" value="{$saveType}" id="save_save_type">
{if $edit}
		<input type="hidden" name="task_id" value="{$task->id}" id="save_task_id">
{/if}
		<input type="hidden" name="task_type" value="{$taskType}" id="save_task_type">
		<input type="hidden" name="action" value="savetask" id="save_action">
		<input type="hidden" name="module" value="{$module->name}" id="save_module">
		<input type="hidden" name="return_url" value="{$returnUrl}" id="save_return_url">
	</form>
</div>
{include file='com_vtiger_workflow/Footer.tpl'}
