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

<table class="listTableTopButtons" width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td class="small">
			<input type="hidden" name="module_name" value="{$workflow->moduleName}" id="save_modulename">
			<input type="hidden" name="save_type" value="{$saveType}" id="save_savetype">
			{if $saveType eq "edit"}
			<input type="hidden" name="workflow_id" value="{$workflow->id}">
			{/if}
			<input type="hidden" name="conditions" value="" id="save_conditions_json"/>
			<input type="hidden" name="action" value="saveworkflow" id="some_name">
			<input type="hidden" name="module" value="{$module->name}" id="some_name">
			<span id="status_message"></span>
		</td>
	</tr>
</table>