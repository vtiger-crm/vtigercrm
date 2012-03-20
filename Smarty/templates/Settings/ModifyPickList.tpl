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

{if $MODE eq 'delete'}
	<div style="position:relative;display: block;" id="orgLay" class="layerPopup">
		<table border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
			<tr>
				<td class="layerPopupHeading" align="left" width="40%" nowrap>{$MOD.DELETE_PICKLIST_VALUES} - {$FIELDLABEL}</td>
				<td align="right" width="60%"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="middle" border="0" onclick="Myhide('deletediv');"></td>
			</tr>
		</table>
	
		<table border=0 cellspacing=0 cellpadding=5 width=100%>
			<tr><td valign=top align=left width=200px;>
					<select id="delete_availPickList" multiple="multiple" wrap size="20" name="availList" style="width:200px;border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;">
						{foreach item=pick_val from=$PICKVAL}
							<option value="{$pick_val}">{$pick_val}</option>
						{/foreach}
					</select>
				</td>
				<td valign=top align=left>
					<!--img src="{$IMAGE_PATH}movecol_del.gif" onmouseover="this.src='{$IMAGE_PATH}movecol_del_over.gif'" onmouseout="this.src='{$IMAGE_PATH}movecol_del.gif'" onclick="" onmousedown="this.src='{$IMAGE_PATH}movecol_del_down.gif'" align="absmiddle" border="0" -->
					<input type="button" value="{$APP.LBL_APPLY_BUTTON_LABEL}" name="del" class="crmButton small edit" onclick="delPickList(this,'{$MODULE}',{$NONEDIT_FLAG});">
					<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" class="crmButton small cancel" onclick="Myhide('deletediv');">
				</td>			
			</tr>
		
			{if is_array($NONEDITPICKLIST)}
			<tr>
				<td colspan=3>
					<table border=0 cellspacing=0 cellpadding=0 width=100%>
						<tr><td><b><u>{$MOD.LBL_NON_EDITABLE_PICKLIST_ENTRIES} :</u></b></td></tr>
						{foreach item=nonedit from=$NONEDITPICKLIST}
							<tr><td>
								<b>{$nonedit}</b>
							</td></tr>							
						{/foreach}
					</table>
				</td>
			</tr>	
			{/if}
		</table>
	</div>
{elseif $MODE eq 'modify'}
	<div style="position:relative;display: block;" id="orgLay" class="layerPopup">
		<table border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
			<tr>
				<td class="layerPopupHeading" align="left" width="40%" nowrap>{$MOD.EDIT_PICKLIST_VALUE} - {$FIELDLABEL}</td>
				<td align="right" width="60%"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="middle" border="0" onclick="Myhide('modifydiv');"></td>
			</tr>
		</table>
	
		<table border=0 cellspacing=0 cellpadding=5 width=100%>
			<tr><td valign=top align=left width=250px; rowspan="3">
					<select id="edit_availPickList" name="availList" size="10" style="width:200px;border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;">
						{foreach item=pick_val from=$PICKVAL}
							<option value="{$pick_val}">{$pick_val}</option>
						{/foreach}
					</select>
					
					{if is_array($NONEDITPICKLIST)}				
					<table border=0 cellspacing=0 cellpadding=0 width=100%>
						<tr><td><b><u>{$MOD.LBL_NON_EDITABLE_PICKLIST_ENTRIES} :</u></b></td></tr>
						<tr><td><b>
							<div id="nonedit_pl_values">
								{foreach item=nonedit from=$NONEDITPICKLIST}
									{$nonedit}<br>		
								{/foreach}
							</div>							
						</b></td></tr>	
					</table>
					{/if}
				</td>
				<td valign="top" width=300px;>
					<b>{$MOD.LBL_REPLACE_VALUE_WITH}</b>&nbsp;
					<input type="text" id="replaceVal" class="small" /> 
				</td>
			</tr>		
			<tr>				
				<td valign=top align=left width=300px;>
					<b>{$MOD.LBL_SELECT_ROLES} </b><br />
					<select id="edit_availRoles" multiple="multiple" wrap size="5" name="edit_availRoles" style="width:250px;border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;">
						{foreach key=role_id item=role_details from=$ROLEDETAILS}
							<option value="{$role_id}">{$role_details.0}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign=top align=right>
					<input type="button" value="{$APP.LBL_APPLY_BUTTON_LABEL}" name="del" class="crmButton small edit" onclick="validate_new_picklist_value('edit','{$FIELDNAME}','{$MODULE}');">
					<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" class="crmButton small cancel" onclick="Myhide('modifydiv');">
				</td>			
			</tr>
		</table>
	</div>
{elseif $MODE eq 'add'}
	<div style="position:relative;display: block;" id="orgLay" class="layerPopup">
		<table border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
			<tr>
				<td class="layerPopupHeading" align="left" width="40%" nowrap>{$MOD.ADD_PICKLIST_VALUES} - {$FIELDLABEL}</td>
				<td align="right" width="60%"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="middle" border="0" onclick="Myhide('adddiv');"></td>
			</tr>
		</table>
	
		<table border=0 cellspacing=0 cellpadding=5 width=100%>
			<tr>
				<td><b>{$MOD.LBL_EXISTING_PICKLIST_VALUES}</b></td>
				<td colspan="2" align=left>
					<b>{$MOD.LBL_PICKLIST_ADDINFO}</b>
				</td>
			</tr>
			<tr>	
				<td rowspan=3 valign=top align=left width=250px;>	
					<select id="add_availPickList" multiple="multiple" wrap size="15" name="availList" style="width:200px;border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;"> 				
						{if is_array($NONEDITPICKLIST)}
							{foreach item=nonedit from=$NONEDITPICKLIST}
								<option value="{$nonedit}">{$nonedit}</option>					
							{/foreach}
						{/if}
						{foreach item=pick_val from=$PICKVAL}
							<option value="{$pick_val}">{$pick_val}</option>
						{/foreach}
					</select>
				</td>
				
				<td valign=top align=left width=300px;>
					<textarea id="add_picklist_values" class="detailedViewTextBox" align="left" rows="10">
					</textarea>
				</td>
			</tr>
			<tr>
				<td valign=top align=left width=300px;>
					<b>{$MOD.LBL_SELECT_ROLES} </b><br />
					<select id="add_availRoles" multiple="multiple" wrap size="5" name="add_availRoles" style="width:250px;border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;">
						{foreach key=role_id item=role_details from=$ROLEDETAILS}
							<option value="{$role_id}">{$role_details.0}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td valign=top align=right>
					<input type="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" name="save" class="crmButton small edit" onclick="validate_new_picklist_value('add','{$FIELDNAME}','{$MODULE}');">
					<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" class="crmButton small cancel" onclick="Myhide('adddiv');">
				</td>			
			</tr>
		</table>
	</div>
{else}
	<div id="ssignedPick" class="layerPopup">
		{$OUTPUT}
	</div>		
{/if}
