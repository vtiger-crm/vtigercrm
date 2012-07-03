{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
-->*}
<script src="modules/{$module->name}/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    var moduleName = '{$entityName}';
	var moduleLabel = '{$entityName|@getTranslatedString:$entityName}';
    {if $task->field_value_mapping}
        var fieldvaluemapping = JSON.parse('{$task->field_value_mapping|escape:'quotes'}');
    {else}
        var fieldvaluemapping = null;
    {/if}
	var selectedEntityType = '{$task->entity_type}';
	var createEntityHeaderTemplate = '<input type="button" class="crmButton create small" value="'+"{'LBL_ADD_FIELD'|@getTranslatedString:$MODULE}"+ '" id="save_fieldvaluemapping_add" />';
</script>
<script src="modules/{$module->name}/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/createentitytaskscript.js" type="text/javascript" charset="utf-8"></script>

<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
	<tr valign="top">
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap">{'LBL_ENTITY_TYPE'|@getTranslatedString:$MODULE}</td>
		<td class='dvtCellInfo'>
			<input type="hidden" value='{$task->reference_field}' name='reference_field' id='reference_field' />
			<span id="entity_type-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select name="entity_type" id="entity_type" class="small" style="display:none;">
				<option value=''>{'LBL_SELECT_ENTITY_TYPE'|@getTranslatedString:$MODULE}</option>
			</select>
		</td>
	</tr>
	<tr><td colspan="2"><hr size="1" noshade="noshade" /></td></tr>

    <tr>
        <td class="small" align="right" colspan="2">
            <span id="workflow_loading" style="display:none">
                <b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0">
            </span>
            <span id="save_fieldvaluemapping_add-busyicon" style="display:none"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
            <span id="save_fieldvaluemapping_add_wrapper"></span>
        </td>
    </tr>

	<tr>
		<td class="small" align="center" colspan="2">
			{include file="com_vtiger_workflow/FieldExpressions.tpl"}
			<input type="hidden" name="field_value_mapping" value="" id="save_fieldvaluemapping_json"/>
			<div id="dump" style="display:none;"></div>
			<div id="save_fieldvaluemapping">
				<div style="border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative; z-index: 10000000;">
					<table width="98%" cellspacing="0" cellpadding="5" border="0">
						<tbody>
							<tr>
								<td width="25%"><img width="61" height="60" src="{'empty.jpg'|@vtiger_imageUrl:$THEME}"></td>
								<td width="75%" nowrap="nowrap" style="border-bottom: 1px solid rgb(204, 204, 204);">
									<span class="genHeaderSmall">{'LBL_NO_ENTITIES_FOUND'|@getTranslatedString:$MODULE}</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</td>
	</tr>

	<tr>
		<td style='padding-top: 10px;' colspan="2">
			<span class="helpmessagebox">{'LBL_CREATE_ENTITY_NOTE_ORDER_MATTERS'|@getTranslatedString:$MODULE}</span>
		</td>
	</tr>
</table>
<br>