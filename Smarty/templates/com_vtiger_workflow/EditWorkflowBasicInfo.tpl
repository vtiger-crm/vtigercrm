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
			<strong>{$MOD.LBL_SUMMARY}</strong>
		</td>
		<td align="right">
			{if $saveType eq "edit"}
			<input type="button" class="crmButton create small" value="{$MOD.LBL_NEW_TEMPLATE}" id="new_template"/>
			{/if}
			<input type="submit" id="save_submit" value="{$APP.LBL_SAVE_LABEL}" class="crmButton small save" style="display:none;">
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