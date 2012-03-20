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
<div style="position:relative;display: block;" id="orgLay" class="layerPopup">
	<table border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
		<tr>
			<td class="layerPopupHeading" align="left" width="40%" nowrap>{$MOD.ASSIGN_PICKLIST_VALUES} - {$FIELDLABEL}</td>
		</tr>
	</table>

	<table border="0" cellspacing="0" cellpadding="5" width="100%" id="assignPicklistTable">
	<tbody>
		<tr>	
			<td width="auto;">	
				<b>{$MOD.LBL_PICKLIST_VALUES}</b>
				<select multiple id="availList" name="availList" class="small crmFormList" style="overflow:auto; height: 150px;width:200px;border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;">
					{foreach item=pick_val from=$PICKVAL}
						<option value="{$pick_val}">{$pick_val}</option>
					{/foreach}
				</select>
			</td>
			<td align="center" width="25px;">
				<img border="0" title="right" alt="Move Right" onclick="moveRight();" style="cursor: pointer" src="{'arrow_right.png'|@vtiger_imageurl:$THEME}"/>
				<img border="0" title="left" alt="Remove" onclick="removeValue();" style="cursor: pointer" src="{'arrow_left.png'|@vtiger_imageurl:$THEME}"/>
			</td>
			<td width="auto;">
				<b>{$MOD.LBL_PICKLIST_VALUES_ASSIGNED_TO} {$ROLENAME}</b>
				<select multiple id="selectedColumns" name="selectedColumns" class="small crmFormList" style="overflow:auto; height: 150px;width:200px;border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;">
					{foreach item=val from=$ASSIGNED_VALUES}
						<option value="{$val}">{$val}</option>
					{/foreach}
        	    </select>
			</td>
			<td align="center">
				<img border="0" title="up" alt="Move Up" onclick="moveUp();" style="cursor: pointer" src="{'arrow_up.png'|@vtiger_imageurl:$THEME}"/>
				<img border="0" title="down" alt="Move Down" onclick="moveDown();" style="cursor: pointer" src="{'arrow_down.png'|@vtiger_imageurl:$THEME}"/>
			</td>
		</tr>
		<tr>
			<td>
				<a href='javascript:;' onclick="showRoleSelectDiv('{$ROLEID}')" id="addRolesLink">
					<b>{$MOD.LBL_ADD_TO_OTHER_ROLES}</b>
				</a>
			</td>
			<td colspan="3" valign="top" align="center" nowrap>
				<input type="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" name="save" class="crmButton small edit" onclick="saveAssignedValues('{$MODULE}','{$FIELDNAME}','{$ROLEID}');">
				<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" class="crmButton small cancel" onclick="fnhide('actiondiv');">
			</td>			
		</tr>
	</tbody>
	</table>
</div>
