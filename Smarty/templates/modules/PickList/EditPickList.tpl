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
<div style="position:relative; display:block; padding:10px" class="layerPopup">
	<table border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
		<tr>
			<td class="layerPopupHeading" align="left" nowrap>
				{$MOD.EDIT_PICKLIST_VALUE} - {$FIELDLABEL}
			</td>
		</tr>
	</table>

	<table border=0 cellspacing=0 cellpadding=5>
		<tr>
		<td valign=top align=left>
			<b>{$MOD.LBL_SELECT_TO_EDIT}</b>
			<br>
			<select id="edit_availPickList" name="availList" size="10" style="width:250px;border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;" onchange="selectForEdit();">
				{foreach item=pick_val key=pick_key from=$PICKVAL}
					<option value="{$pick_key}">{$pick_val}</option>
				{/foreach}
			</select>

			{if is_array($NONEDITPICKLIST)}
			<table border=0 cellspacing=0 cellpadding=0 width=100%>
				<tr><td><b>{$MOD.LBL_NON_EDITABLE_PICKLIST_ENTRIES} :</b></td></tr>
				<tr><td><b>
					<div id="nonedit_pl_values">
						{foreach item=nonedit from=$NONEDITPICKLIST}
							<span class="nonEditablePicklistValues">
								{$nonedit}
							</span><br>
						{/foreach}
					</div>
				</b></td></tr>
			</table>
			{/if}
		</td>
		</tr>
		<tr>
			<td>
				<b>{$MOD.LBL_EDIT_HERE}</b>&nbsp;
				<input type="text" id="replaceVal" class="small" style="width: 60%" onchange="pushEditedValue(event)"/>
			</td>
		</tr>
		<tr>
			<td valign=top>
				<input type="button" value="{$APP.LBL_APPLY_BUTTON_LABEL}" name="apply" class="crmButton small edit" onclick="validateEdit('{$FIELDNAME}','{$MODULE}');">
				<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" class="crmButton small cancel" onclick="fnhide('actiondiv');">
			</td>
		</tr>
	</table>
</div>
