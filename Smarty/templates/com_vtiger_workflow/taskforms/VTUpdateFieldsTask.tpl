{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}

<script src="modules/{$module->name}/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    var moduleName = '{$entityName}';
    {if $task->field_value_mapping}
        var fieldvaluemapping = JSON.parse('{$task->field_value_mapping|escape:'quotes'}');
    {else}
        var fieldvaluemapping = null;
    {/if}
</script>
<script src="modules/{$module->name}/resources/fieldexpressionpopup.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/updatefieldstaskscript.js" type="text/javascript" charset="utf-8"></script>

<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
        <td class="big" nowrap="nowrap">
            <strong>{$MOD.LBL_SET_FIELD_VALUES}</strong>
        </td>
        <td class="small" align="right">
            <span id="workflow_loading" style="display:none">
                <b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0">
            </span>
            <span id="save_fieldvaluemapping_add-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
            <input type="button" class="crmButton create small"
                   value="{$MOD.LBL_ADD_FIELD}" id="save_fieldvaluemapping_add" style='display: none;'/>
        </td>
    </tr>
</table>
<br>
{include file="com_vtiger_workflow/FieldExpressions.tpl"}
<br>
<input type="hidden" name="field_value_mapping" value="" id="save_fieldvaluemapping_json"/>
<div id="dump" style="display:None;"></div>
<div id="save_fieldvaluemapping"></div>