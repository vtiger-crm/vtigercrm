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
<div id="pickListContents">
<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
<tr>
	<td class="big" width="20%" nowrap>
		<strong>{$MOD.LBL_SELECT_PICKLIST}</strong>&nbsp;&nbsp;
	</td>
	<td class="cellText" width="40%">
		<select name="avail_picklists" id="allpick" class="small detailedViewTextBox" style="font-weight: normal;">
			{foreach key=fld_nam item=fld_lbl from=$ALL_LISTS}
				<option value="{$fld_nam}">{$fld_lbl|getTranslatedString:$MODULE}</option>
			{/foreach}
		</select>
	</td>
	<td nowrap align="right">
		<input type="button" value="{$APP.LBL_ADD_BUTTON}" name="add" class="crmButton small create" onclick="showAddDiv();">
 		<input type="button" value="{$APP.LBL_EDIT_BUTTON}" name="del" class="crmButton small edit" onclick="showEditDiv();">
 		<input type="button" value="{$APP.LBL_DELETE_BUTTON}" name="del" class="crmButton small delete" onclick="showDeleteDiv();">
 	</td>
</tr>
</table>
{*<!-- vtlib customization: Use translated string only if available -->*}
{assign var="MODULELABEL" value=$MODULE}
{if $APP.$MODULE}
	{assign var="MODULELABEL" value=$MODULE}
{/if}
<table class="tableHeading" border="0" cellpadding="7" cellspacing="0" width="100%">
<tr>
	<td width="40%">
		<strong>
			{$MOD.LBL_PICKLIST_AVAIL} {$MODULELABEL} {$MOD.LBL_FOR} &nbsp;
		</strong>
		<select name="pickrole" id="pickid" class="detailedViewTextBox" onChange="showPicklistEntries('{$MODULE}' );" style="width : auto;">
			{foreach key=roleid item=role from=$ROLE_LISTS}
				{if $SEL_ROLEID eq $roleid}
					<option value="{$roleid}" selected>{$role}</option>
				{else}
					<option value="{$roleid}">{$role}</option>
				{/if}
			{/foreach}
		</select>
	</td>
</tr>
<tr>
	<td class="small">
		<font color="red">* {$MOD_PICKLIST.LBL_DISPLAYED_VALUES}</font>
	</td>
</tr>
</table>

<table border=0 cellspacing=0 cellpadding=0 width=100% class="listTable">
<tr>
<td valign=top width="50%">
	<table width="100%" class="listTable" cellpadding="5" cellspacing="0">
	{foreach item=picklists from=$PICKLIST_VALUES}
	<tr>
		{foreach item=picklistfields from=$picklists}
			{if $picklistfields neq ''}
				<td class="listTableTopButtons small" style="padding-left:20px" valign="top" align="left">
					{if $TEMP_MOD[$picklistfields.fieldlabel] neq ''}	
						<b>{$TEMP_MOD[$picklistfields.fieldlabel]}</b>
					{else}
						<b>{$picklistfields.fieldlabel}</b>
					{/if}
				</td>
				<td class="listTableTopButtons" valign="top">
					<input type="button" value="{$MOD_PICKLIST.LBL_ASSIGN_BUTTON}" class="crmButton small edit" onclick="assignPicklistValues('{$MODULE}','{$picklistfields.fieldname}','{$picklistfields.fieldlabel}');" > 
				</td>
			{else}
				<td class="listTableTopButtons small" colspan="2">&nbsp;</td>
			{/if}
		{/foreach}
	</tr>
	<tr>
		{foreach item=picklistelements from=$picklists}
			{if $picklistelements neq ''}
				<td colspan="2" valign="top">
				<ul style="list-style-type: none;">
					{foreach item=elements from=$picklistelements.value}
						{if $TEMP_MOD[$elements] neq ''}
							<li>{$TEMP_MOD[$elements]}</li>
						{elseif $MOD_PICKLIST[$elements] neq ''}
							<li>{$MOD_PICKLIST[$elements]}</li>
						{else}
							<li>{$elements}</li>
						{/if}
					{/foreach}
				</ul>
				</td>
			{else}
				<td colspan="2">&nbsp;</td>
			{/if}
		{/foreach}
	</tr>
	{/foreach}
	</table> 
</td>
</tr>
</table>
</div>
