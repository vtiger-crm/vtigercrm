{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<form name="support_owners" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="module" value="Settings">
	<input type="hidden" name="parenttab" value="Settings">
	<input type="hidden" name="action" value="SettingsAjax">
	<input type="hidden" name="file" value="ListModuleOwners">
<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
<tr>
	<td class="big"><strong>{$MOD.LBL_MODULES_AND_OWNERS}</strong></td>
	<td class="small" align=right>
<div align="right">
{if $MODULE_MODE neq 'edit'}
		<input title="{$APP.LBL_EDIT_BUTTON_LABEL}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmButton small edit" type="button" name="button" value="{$APP.LBL_EDIT_BUTTON_LABEL}" onClick="assignmodulefn('edit');">
{else}
		<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" type="button" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onClick="assignmodulefn('save');" >&nbsp;
						<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmButton small cancel" onclick="this.form.action.value='ListModuleOwners'; this.form.parenttab.value='Settings';" type="submit" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" >
{/if}
</div>
</td>
</tr>
					
</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTable">	
	<tr>
		<td class="colHeader small" width="2%">#</td>
		<td class="colHeader small" width="30%">{$MOD.LBL_MODULE}</td>
		<td class="colHeader small" width="65%">{$MOD.LBL_OWNER}</td>
	</tr>
	{if $MODULE_MODE neq 'edit'}
	{foreach name=modulelists item=modules from=$USER_LIST}
	<tr>
		<td class="listTableRow small" valign="top">{$smarty.foreach.modulelists.iteration}</td>
		<td class="listTableRow small" valign="top">{$APP[$modules.0]}</td>
		<td class="listTableRow small" valign="top"><a href="index.php?module=Users&action=DetailView&record={$modules.1}">{$modules.2}</a></td>	
	</tr>
	{/foreach}
	{else}
	{foreach name=modulelists item=modules from=$USER_LIST}
	<tr>
		<td class="listTableRow small" valign="top">{$smarty.foreach.modulelists.iteration}</td>
		<td class="listTableRow small" valign="top">{$APP[$modules.0]}</td>
		<td class="listTableRow small" valign="top">{$modules.1}</td>	
	</tr>
	{/foreach}
	{/if}
	</table>
</form>
