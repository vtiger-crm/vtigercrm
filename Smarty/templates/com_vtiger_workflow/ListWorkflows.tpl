{include file='com_vtiger_workflow/Header.tpl'}
<script src="modules/{$module->name}/resources/jquery-1.2.6.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/json2.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/workflowlistscript.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	fn.addStylesheet('modules/{$module->name}/resources/style.css');
</script>
<!--New workflow popup-->
<div id="new_workflow_popup" class="layerPopup" style="display:none;">
	<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerHeadingULine">
		<tr>
			<td width="80%" align="left" class="layerPopupHeading">
				{$MOD.LBL_CREATE_WORKFLOW}
				</td>
			<td width="20%" align="right">
				<a href="javascript:void(0);" id="new_workflow_popup_close">
					<img border="0" align="middle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/>
				</a>
			</td>
		</tr>
	</table>
	
	<form action="index.php" method="post" accept-charset="utf-8" onsubmit="VtigerJS_DialogBox.block();">
		<div class="popup_content">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr align="left">
					<td><input type="radio" name="source" value="from_module" checked="true" class="workflow_creation_mode">
						{$MOD.LBL_FOR_MODULE}</td>
					<td><input type="radio" name="source" value="from_template" class="workflow_creation_mode">
						{$MOD.LBL_FROM_TEMPLATE}</td>
				</tr>
			</table>
			<table width="100%" cellpadding="5" cellspacing="0" border="0">
				<tr align="left">
					<td width='10%' nowrap="nowrap">{$MOD.LBL_CREATE_WORKFLOW_FOR}</td>
					<td>
						<select name="module_name" id="module_list" class="small">
{foreach item=moduleName from=$moduleNames}
							<option value="{$moduleName}" {if $moduleName eq $listModule}selected="selected"{/if}>
								{$moduleName|@getTranslatedString:$moduleName}
							</option>
{/foreach}
						</select>
					</td>
				</tr>
				<tr align="left" id="template_select_field" style="display:none;">
					<td>{$MOD.LBL_CHOOSE_A_TEMPLATE}</td>
					<td>
						<span id="template_list_busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
						<span id="template_list_foundnone" style='display:none;'><b>{$MOD.LBL_NO_TEMPLATES}</b></span>
						<select id="template_list" name="template_id" class="small"></select>						
					</td>
				</tr>
			</table>
			<input type="hidden" name="save_type" value="new" id="save_type_new">
			<input type="hidden" name="module" value="{$module->name}" id="save_module">
			<input type="hidden" name="action" value="editworkflow" id="save_action">
			<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">
				<tr><td align="center">
					<input type="submit" class="crmButton small save" value="{$APP.LBL_CREATE_BUTTON_LABEL}" name="save" id='new_workflow_popup_save'/> 
					<input type="button" class="crmButton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL} " name="cancel" id='new_workflow_popup_cancel'/>
				</td></tr>
			</table>
		</div>
	</form>
</div>
<!--Done Popups-->

{include file='SetMenu.tpl'}
<div id="view">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
	<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td class="big" nowrap="nowrap">
				<strong><span id="module_info"></span></strong>
			</td>
			<td class="small" align="right">
				<form action="index.php" method="get" accept-charset="utf-8" id="filter_modules" onsubmit="VtigerJS_DialogBox.block();" style="display: inline;">
					<b>{$MOD.LBL_SELECT_MODULE}: </b>
					<select class="importBox" name="list_module" id='pick_module'>
						<option value="All">{$APP.LBL_ALL}</a>
							<option value="All" disabled="disabled" >-----------------------------</a>
{foreach  item=moduleName from=$moduleNames}
						<option value="{$moduleName}" {if $moduleName eq $listModule}selected="selected"{/if}>
							{$moduleName|@getTranslatedString:$moduleName}
						</option>
{/foreach}
					</select>
					<input type="hidden" name="module" value="{$module->name}">
					<input type="hidden" name="action" value="workflowlist">
				</form>

			</td>
		</tr>
	</table>

	<table class="listTableTopButtons" width="100%" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td class="small"> <span id="status_message"></span> </td>
			<td class="small" align="right">
				<input type="button" class="crmButton create small" 
					value="{$MOD.LBL_NEW_WORKFLOW}" id='new_workflow'/>
			</td>
		</tr>
	</table>
	<table class="listTable" width="100%" border="0" cellspacing="0" cellpadding="5" id='expressionlist'>
		<tr>
			<td class="colHeader small" width="20%">
				Module
			</td>
			<td class="colHeader small" width="65">
				Description
			</td>
			<td class="colHeader small" width="15%">
				Tools
			</td>
		</tr>
{foreach item=workflow from=$workflows}
		<tr>
			<td class="listTableRow small">{$workflow->moduleName|@getTranslatedString:$workflow->moduleName}</td>
			<td class="listTableRow small">{$workflow->description|@to_html}</td>
			<td class="listTableRow small">
				<a href="{$module->editWorkflowUrl($workflow->id)}">
					<img border="0" title="Edit" alt="Edit" \
						style="cursor: pointer;" id="expressionlist_editlink_{$workflow->id}" \
						src="{'editfield.gif'|@vtiger_imageurl:$THEME}"/>
				</a>
				<a href="{$module->deleteWorkflowUrl($workflow->id)}" onclick="return confirm('{$APP.SURE_TO_DELETE}');">
					<img border="0" title="Delete" alt="Delete"\
			 			src="{'delete.gif'|@vtiger_imageurl:$THEME}" \
						style="cursor: pointer;" id="expressionlist_deletelink_{$workflow->id}" />
				</a>
			</td>
		</tr>
{/foreach}
	</table>
</div>
{include file='com_vtiger_workflow/Footer.tpl'}

