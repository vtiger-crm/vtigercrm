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

<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td class="big" nowrap="nowrap">
			<strong>{$MOD.LBL_WHEN_TO_RUN_WORKFLOW}</strong>
		</td>
		<td width="5%" align="right">
			<a href="{$WORKFLOW_TRIGGER_TYPES_HELP_LINK}" target="_blank" style="cursor:pointer;">
				<img border="0" title="" alt="" src="{'help_icon.gif'|@vtiger_imageURL:$THEME}" />
			</a>
		</td>
	</tr>
</table>
{if $workflow->executionConditionAsLabel() eq 'MANUAL'}
	{assign var="DISABLE_TYPE_CHANGE" value="disabled"}
{else}
	{assign var="DISABLE_TYPE_CHANGE" value=""}
{/if}
<table border="0" >
	<tr><td><input type="radio" name="execution_condition" value="ON_FIRST_SAVE"
		{if $workflow->executionConditionAsLabel() eq 'ON_FIRST_SAVE'}checked{/if} {$DISABLE_TYPE_CHANGE} /></td>
		<td>{$MOD.LBL_ONLY_ON_FIRST_SAVE}.</td></tr>
	<tr><td><input type="radio" name="execution_condition" value="ONCE"
		{if $workflow->executionConditionAsLabel() eq 'ONCE'}checked{/if} {$DISABLE_TYPE_CHANGE} /></td>
		<td>{$MOD.LBL_UNTIL_FIRST_TIME_CONDITION_TRUE}.</td></tr>
	<tr><td><input type="radio" name="execution_condition" value="ON_EVERY_SAVE"
		{if $workflow->executionConditionAsLabel() eq 'ON_EVERY_SAVE'}checked{/if} {$DISABLE_TYPE_CHANGE}/></td>
		<td>{$MOD.LBL_EVERYTIME_RECORD_SAVED}.</td></tr>
	<tr><td><input type="radio" name="execution_condition" value="ON_MODIFY"
		{if $workflow->executionConditionAsLabel() eq 'ON_MODIFY'}checked{/if} {$DISABLE_TYPE_CHANGE}/></td>
		<td>{$MOD.LBL_ON_MODIFY}.</td></tr>
	<tr><td><input type="radio" name="execution_condition" value="MANUAL"
		{if $workflow->executionConditionAsLabel() eq 'MANUAL'}checked{/if} disabled /></td>
		<td>{$MOD.LBL_MANUAL}.</td></tr>
</table>
