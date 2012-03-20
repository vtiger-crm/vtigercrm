{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/ *}
<script language="JavaScript" type="text/javascript" src="include/js/customview.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>

		<div align=center>
			{include file='SetMenu.tpl'}
			<!-- DISPLAY -->
				<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr align="left">
					<td rowspan="2" valign="top" width="50"><img src="{'custom.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}" border="0" height="48" width="48"></td>
					<td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&action=ModuleManager&parenttab=Settings">{$MOD.VTLIB_LBL_MODULE_MANAGER}&gt;</a><a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule=Leads&parenttab=Settings">{$MODULE}</a> &gt; {$MOD.LBL_CUSTOM_FIELD_SETTINGS}</b></td>
				</tr>

				<tr align="left">
					<td class="small" valign="top">{$MOD.LEADS_CUSTOM_FIELD_MAPPING}</td>
				</tr>
				</table>
				
				<br>
				<form action="index.php?module=Settings&action=SaveConvertLead" method="post" name="index" onsubmit="VtigerJS_DialogBox.block();">
				<table class="listTableTopButtons" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td class="big" align="left"><strong>{$MOD.LBL_EDIT_FIELD_MAPPING}</strong> </td>
					<td class="small">&nbsp;</td>
					<td class="small" align="right">&nbsp;&nbsp;
					<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="crmButton small save" type="submit" onclick ="return validateCustomFieldAccounts();">
                     <input title="{$APP.LBL_CANCEL_BUTTON_LABEL}>" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " onclick = "window.history.back()"  class="crmButton small cancel" type="button">
				</tr>
				</table>
				<table class="listTable" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td rowspan="2" class="colHeader small" width="2%">#</td>
					<td rowspan="2" class="colHeader small" width="15%">{$MOD.FieldLabel}</td>
					<td rowspan="2" class="colHeader small" width="15%">{$MOD.FieldType}</td>
					<td colspan="3" class="colHeader small" valign="top"><div align="center">{$MOD.LBL_MAPPING_OTHER_MODULES}</div></td>
				</tr>
				<tr>
					<td class="colHeader small" valign="top" width="23%">{$APP.Accounts}</td>
					<td class="colHeader small" valign="top" width="23%">{$APP.Contacts}</td>
					<td class="colHeader small" valign="top" width="24%">{$APP.Potentials}</td>
				</tr>
				{foreach key=leadcf item=cfarray name=cfarray from=$CUSTOMFIELDMAPPING}
				<tr>
					<td class="listTableRow small">{$cfarray.sno}</td>
					<td class="listTableRow small">{$cfarray.leadid}</td>
					<td class="listTableRow small">{$cfarray.fieldtype}</td>
					{foreach item=acc_cf key=fldnameacc from=$cfarray.account}
					<td class="listTableRow small">
						<select name='{$fldnameacc}' id='{$fldnameacc}' onChange='return validateTypeforCFMapping("{$cfarray.fieldtype}","{$cfarray.typeofdata}","{$fldnameacc}",this);' >
						<option value='None'>{$APP.LBL_NONE}</option>
						{foreach item=element from=$acc_cf}
							<option value="{$element.fieldid}" {$element.selected}>{$element.fieldlabel}</option>
						{/foreach}
						</select>
						{if $smarty.foreach.cfarray.index eq 0}
							{foreach item=element from=$acc_cf}
								<input type='hidden' name='{$element.fieldid}_type' id='{$element.fieldid}_type' value='{$element.fieldtype}'>
								<input type='hidden' name='{$element.fieldid}_typeofdata' id='{$element.fieldid}_typeofdata' value='{$element.typeofdata}'>
							{/foreach}
						{/if}
					</td>
					{/foreach}
					{foreach item=con_cf key=fldnamecon from=$cfarray.contact}
                    <td class="listTableRow small">
                        <select name='{$fldnamecon}' id='{$fldnamecon}' onChange='return validateTypeforCFMapping("{$cfarray.fieldtype}","{$cfarray.typeofdata}","{$fldnamecon}",this);'>
						<option value='None'>{$APP.LBL_NONE}</option>
						{foreach item=element from=$con_cf}
							<option value="{$element.fieldid}" {$element.selected}>{$element.fieldlabel}</option>
						{/foreach}
                        </select>
					{if $smarty.foreach.cfarray.index eq 0}
						{foreach item=element from=$con_cf}
                                                	<input type='hidden' name='{$element.fieldid}_type' id='{$element.fieldid}_type' value='{$element.fieldtype}' >
                                                	<input type='hidden' name='{$element.fieldid}_typeofdata' id='{$element.fieldid}_typeofdata' value='{$element.typeofdata}'>
						{/foreach}
                                        {/if}
					</td>
				  	{/foreach}
					{foreach item=pot_cf key=fldnamepot from=$cfarray.potential}
					<td class="listTableRow small">
						<select name='{$fldnamepot}' id='{$fldnamepot}' onChange='return validateTypeforCFMapping("{$cfarray.fieldtype}","{$cfarray.typeofdata}","{$fldnamepot}",this);'>
						<option value='None'>{$APP.LBL_NONE}</option>
						{foreach item=element from=$pot_cf}
							<option value="{$element.fieldid}" {$element.selected}>{$element.fieldlabel}</option>
						{/foreach}
                        </select>
					{if $smarty.foreach.cfarray.index eq 0}
						{foreach item=element from=$pot_cf}
	                                        	<input type='hidden' name='{$element.fieldid}_type' id='{$element.fieldid}_type' value='{$element.fieldtype}'>
        	                                        <input type='hidden' name='{$element.fieldid}_typeofdata' id='{$element.fieldid}_typeofdata' value='{$element.typeofdata}'>
						{/foreach}
                                        {/if}
					</td>
				    {/foreach}
				</tr>
				{/foreach}
				</table>
				<table border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td class="small">
		        	<strong>{$APP.LBL_NOTE}: </strong> {$MOD.LBL_CUSTOM_MAPP_INFO}
					</td>
				</tr>
				</table>
				<table border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
		  			<td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td>
				</tr>
				</table>
				</form>
				<br>
		</td>
		</tr>
		</table>
        </td>
        </tr>
        </table>
        </div>

        </td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
        </tr>
</tbody>
</table>
<script>
	var alertmessage = new Array("{$MOD.LBL_TYPEALERT_1}","{$MOD.LBL_WITH}","{$MOD.LBL_TYPEALERT_2}","{$MOD.LBL_LENGTHALERT}","{$MOD.LBL_DECIMALALERT}");
</script>
