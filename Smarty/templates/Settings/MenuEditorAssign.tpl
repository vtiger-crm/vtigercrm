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
	<table border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine" align="center">
		<tr>
			<td align="left">
				<span class="layerPopupHeading" nowrap>{'LBL_MENUS_TO_SHOW'|@getTranslatedString}</span>
				<br>
				<span class="small">{'LBL_MENUS_TO_SHOW_DESCRIPTION'|@getTranslatedString}</span>
			</td>
		</tr>
	</table>

	<table border="0" cellspacing="0" cellpadding="5" width="100%" id="assignPicklistTable" align="center">
	<tbody>
		<tr>
			<td width="auto;">
				<b>{$MOD.LBL_ALL_MODULES}</b>
				<select multiple id="availList" name="availList" class="small crmFormList" style="overflow:auto; height: 150px;width:200px;border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;" >
					{foreach key = number item = menus from = $ALLMENUS name=pname}
                                        {assign var="modulelabel" value=$menus[0]}
                                        {assign var="modulename" value=$menus[1]|@getTranslatedString:$menus[0]}
                                        {assign var="tabid" value=$menus[2]}
						<option value="{$tabid}">{$modulename}</option>
                                        {/foreach}
				</select>
			</td>
			<td align="center" width="25px;">
				<img border="0" title="right" alt="Move Right" onclick="copySelectedOptions('availList','selectedColumns');" style="cursor: pointer" src="{'arrow_right.png'|@vtiger_imageurl:$THEME}"/>
				<img border="0" title="left" alt="Remove" onclick="removeSelectedOptions('selectedColumns');" style="cursor: pointer" src="{'arrow_left.png'|@vtiger_imageurl:$THEME}"/>
			</td>
			<td width="auto;">
				<b>{$MOD.LBL_SELECTED_MODULES}</b>
				<select multiple id="selectedColumns" name="selectedColumns" class="small crmFormList" style="overflow:auto; height: 150px;width:200px;border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;" size="11">
					{foreach key = number item = menus from = $ASSIGNED_VALUES name=pname}
                                        {assign var="modulelabel" value=$menus[0]}
                                        {assign var="modulename" value=$menus[1]|@getTranslatedString:$menus[0]}
                                        {assign var="tabid" value=$menus[2]}
						<option value="{$tabid}">{$modulename}</option>
                                    {/foreach}
                               </select>
			</td>
			<td align="center">
				<img border="0" title="up" alt="Move Up" onclick="MenuEditorJs.moveUp();" style="cursor: pointer" src="{'arrow_up.png'|@vtiger_imageurl:$THEME}"/>
				<img border="0" title="down" alt="Move Down" onclick="MenuEditorJs.moveDown();" style="cursor: pointer" src="{'arrow_down.png'|@vtiger_imageurl:$THEME}"/>
			</td>
		</tr>
		<tr>

			<td colspan="3" valign="top" align="center" nowrap>
				<input type="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" name="save" class="crmButton small edit" onclick="MenuEditorJs.saveAssignedValues();">
				<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" name="cancel" onclick="window.history.back()" class="crmButton small cancel" >
			</td>
		</tr>
	</tbody>
	</table>
</div>
