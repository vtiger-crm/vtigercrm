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
<div id="pickListDependencyList">
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
		<tr>
			<td class="small" width="70%" nowrap>
				<strong>{$MOD.LBL_SELECT_MODULE}</strong>&nbsp;
				<select name="pickmodule" id="pickmodule" class="small" onChange="changeDependencyPicklistModule();">
					<option value="">{$APP.LBL_ALL}</option>
				{foreach key=modulelabel item=module from=$MODULE_LISTS}
					<option value="{$module}" {if $MODULE eq $module} selected {/if}>
						{$modulelabel|@getTranslatedString:$module}
					</option>
				{/foreach}
				</select>
			</td>
			<td class=small align=right>
				<input title="{$MOD_PICKLIST.LBL_NEW_DEPENDENCY}" class="crmButton create small" type="button" name="New" value="{$MOD_PICKLIST.LBL_NEW_DEPENDENCY}" onclick="addNewDependencyPicklist();"/>
			</td>
		</tr>
	</table>
						
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTable">
		<tr>
			<td class="colHeader small" valign=top width=5%>#</td>
			<td class="colHeader small" valign=top width=25%>{$APP.LBL_MODULE}</td>
			<td class="colHeader small" valign=top width=30%>{$MOD_PICKLIST.LBL_SOURCE_FIELD}</td>
			<td class="colHeader small" valign=top width=30%>{$MOD_PICKLIST.LBL_TARGET_FIELD}</td>
			<td class="colHeader small" valign=top width=10%>{$MOD_PICKLIST.LBL_TOOLS}</td>
		</tr>
		{foreach name=dependencylist item=dependencyvalues from=$DEPENDENT_PICKLISTS}
		{assign var="FIELD_MODULE" value=$dependencyvalues.module}
		<tr>
			<td class="listTableRow small" valign=top>{$smarty.foreach.dependencylist.iteration}</td>
			<td class="listTableRow small" valign=top>{$FIELD_MODULE|@getTranslatedString:$FIELD_MODULE}</td>
			<td class="listTableRow small" valign=top>{$dependencyvalues.sourcefieldlabel|@getTranslatedString:$FIELD_MODULE}</td>
			<td class="listTableRow small" valign=top>{$dependencyvalues.targetfieldlabel|@getTranslatedString:$FIELD_MODULE}</td>
			<td class="listTableRow small" valign=top nowrap>
	  			<a href="javascript:void(0);" onclick="editDependencyPicklist('{$FIELD_MODULE}','{$dependencyvalues.sourcefield}','{$dependencyvalues.targetfield}');"><img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EDIT}" title="{$APP.LBL_EDIT}" border="0" align="absmiddle"></a>&nbsp;|
				<a href="javascript:void(0);" onClick="deleteDependencyPicklist('{$FIELD_MODULE}','{$dependencyvalues.sourcefield}','{$dependencyvalues.targetfield}','{'NTC_DELETE_CONFIRMATION'|@getTranslatedString}');"><img src="{'delete.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_DELETE}" title="{$APP.LBL_DELETE}" border="0" align="absmiddle"></a>
			</td>
  		</tr>
		{/foreach}
	</table>
</div>
