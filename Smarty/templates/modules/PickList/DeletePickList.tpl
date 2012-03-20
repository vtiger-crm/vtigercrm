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
<div style="position:relative;display: block;" class="layerPopup">
	<table border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
		<tr>
			<td class="layerPopupHeading" align="left" nowrap>
				{$MOD.DELETE_PICKLIST_VALUES} - {$FIELDLABEL}
			</td>
		</tr>
	</table>

	<table border=0 cellspacing=0 cellpadding=5 >
		<tr><td valign=top align=left>
				<select id="delete_availPickList" multiple="multiple" wrap size="20" name="availList" style="width:250px;border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;">
					{foreach item=pick_val from=$PICKVAL}
						<option value="{$pick_val}">{$pick_val}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td nowrap>
				<b>{$MOD.LBL_REPLACE_WITH}</b>&nbsp;
				<select id="replace_picklistval" name="replaceList" style="border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;">
					<option value=""></option>
					{foreach item=pick_val from=$PICKVAL}
						<option value="{$pick_val}">{$pick_val}</option>
					{/foreach}
					{foreach item=nonedit from=$NONEDITPICKLIST}
						<option value="{$nonedit}">{$nonedit}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td valign=top align=left>
				<input type="button" value="{$APP.LBL_DELETE_BUTTON_LABEL}" name="del" class="crmButton small delete" onclick="validateDelete('{$FIELDNAME}','{$MODULE}');">
				<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" class="crmButton small cancel" onclick="fnhide('actiondiv');">
			</td>			
		</tr>
	
		{if is_array($NONEDITPICKLIST)}
		<tr>
			<td colspan=3>
				<table border=0 cellspacing=0 cellpadding=0 width=100%>
					<tr><td><b>{$MOD.LBL_NON_EDITABLE_PICKLIST_ENTRIES} :</b></td></tr>
					<tr><td>
					<select id="nonEditablePicklistVal" name="nonEditablePicklistVal" multiple="multiple" wrap size="5" style="width: 100%">
					{foreach item=nonedit from=$NONEDITPICKLIST}
						<option value="{$nonedit}" disabled>{$nonedit}</option>							
					{/foreach}
					</select>
				</table>
			</td>
		</tr>	
		{/if}
	</table>
</div>
