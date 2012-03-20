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

<form id="UnifiedSearch_moduleform" name="UnifiedSearch_moduleform">
	<table width="90%" cellspacing="0" cellpadding="0" border="0" align="center" class="mailClient mailClientBg">
	<tr>
		<td>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
			<tr>
				<td height="30px" background="{'qcBg.gif'|@vtiger_imageurl:$THEME}" class="mailSubHeader"><b>{$APP.LBL_SELECT_MODULES_FOR_SEARCH}</b></td>
				<td align=right background="{'qcBg.gif'|@vtiger_imageurl:$THEME}" class="mailSubHeader">
					<a href='javascript:void(0);' onclick="UnifiedSearch_SelectModuleToggle(true);">{$APP.LBL_SELECT_ALL}</a> |
					<a href='javascript:void(0);' onclick="UnifiedSearch_SelectModuleToggle(false);">{$APP.LBL_UNSELECT_ALL}</a>
					
					<a href='javascript:void(0)' onclick="UnifiedSearch_SelectModuleCancel();"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border=0></a>
				</td>
			</tr>
			</table>
			
			<table width="100%" cellspacing="0" cellpadding="5" border="0" class="small">
				{foreach item=SEARCH_MODULEINFO key=SEARCH_MODULENAME from=$ALLOWED_MODULES name=allowed_modulesloop}
				{if $smarty.foreach.allowed_modulesloop.index % 3 == 0}
				<tr valign=top>	
				{/if}
					<td class="dvtCellLabel"><input type='checkbox' name='search_onlyin' class='small' value='{$SEARCH_MODULENAME}'
					{if $SEARCH_MODULEINFO.selected}checked=true{/if}>{$SEARCH_MODULEINFO.label}</td>
				{if $smarty.foreach.allowed_modulesloop.index % 3 == 2}
				</tr>
				{/if}
				{/foreach}
			</table>
		</td>
	</tr>
	<tr>
		<td align="right" height="30px" class="mailSubHeader">
			<input type='button' class='crmbutton small cancel' value='{$APP.LBL_CANCEL_BUTTON_LABEL}' onclick='UnifiedSearch_SelectModuleCancel();'>
			<input type='button' class='crmbutton small create' value='{$APP.LBL_APPLY_BUTTON_LABEL}' onclick='UnifiedSearch_SelectModuleSave();'>
		</td>
	</tr>
	</table>
</form>
