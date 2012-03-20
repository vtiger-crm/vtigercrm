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
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<br>
	<div align=center>
			{include file="SetMenu.tpl"}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<form action="index.php" method="post" name="tandc" onsubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="server_type" value="proxy">
				<input type="hidden" name="module" value="Settings">
				<input type="hidden" name="action" value="index">
				<input type="hidden" name="proxy_server_mode">
				<input type="hidden" name="parenttab" value="Settings">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'proxy.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_PROXY}" width="48" height="48" border=0 title="{$MOD.LBL_PROXY}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_PROXY_SERVER_SETTINGS} </b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_PROXY_SERVER_DESC} </td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$MOD.LBL_PROXY_SERVER_SETTINGS}<br>{$ERROR_MSG}</strong></td>
						{if $PROXY_SERVER_MODE neq 'edit'}
						<td class="small" align=right>
							<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmButton small edit" onclick="this.form.action.value='ProxyServerConfig';this.form.proxy_server_mode.value='edit'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">
						</td>
						{else}
						<td class="small" align=right>
							<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="this.form.action.value='Save'; return validate()">&nbsp;&nbsp;
						    <input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmButton small cancel" onclick="javascript:document.location.href='index.php?module=Settings&action=ProxyServerConfig&parenttab=Settings'" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
						</td>
						{/if}
					</tr>
					</table>
				
			{if $PROXY_SERVER_MODE eq 'edit'}	
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
      			    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_SERVER_ADDRESS} </strong></td>
                            <td width="80%" class="small cellText">
				{if $smarty.request.server neq ''}
				<input type="text" class="detailedViewTextBox small" value="{$smarty.request.server|@vtlib_purify}" name="server"></strong>
				{else}
				<input type="text" class="detailedViewTextBox small" value="{$PROXYSERVER}" name="server"></strong>
				{/if}
			    </td>
                          </tr>
			  <tr>
                            <td width="20%" nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_PROXY_PORT} </strong></td>
                            <td width="80%" class="small cellText">
				{if $smarty.request.port neq ''}
                                <input type="text" class="detailedViewTextBox small" value="{$smarty.request.port|@vtlib_purify}" name="port"></strong>
				{else}
                                <input type="text" class="detailedViewTextBox small" value="{$PROXYPORT}" name="port"></strong>
				{/if}
                            </td>
                          </tr>
                          <tr valign="top">

                            <td nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_USERNAME}</strong></td>
                            <td class="small cellText">
				{if $smarty.request.server_username neq ''}
				<input type="text" class="detailedViewTextBox small" value="{$smarty.request.server_username|@vtlib_purify}" name="server_username">
				{else}
				<input type="text" class="detailedViewTextBox small" value="{$PROXYUSER}" name="server_username">
				{/if}
			    </td>
                          </tr>
                          <tr>
                            <td nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_PASWRD}</strong></td>
                            <td class="small cellText">
				<input type="password" class="detailedViewTextBox small" value="{$PROXYPASSWORD}" name="server_password">
			    </td>
                          </tr>
                        </table>
			{else}
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
	         	    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SERVER_ADDRESS} </strong></td>
                            <td width="80%" class="small cellText"><strong>{$PROXYSERVER}&nbsp;</strong></td>
                        </tr>
			<tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_PROXY_PORT}</strong></td>
                            <td class="small cellText">{$PROXYPORT}&nbsp;</td>
                        </tr>
                        <tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong></td>
                            <td class="small cellText">{$PROXYUSER}&nbsp;</td>
                        </tr>
                        <tr>
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_PASWRD}</strong></td>
                            <td class="small cellText">
				{if $PROXYPASSWORD neq ''}
				******
				{/if}&nbsp;
			    </td>
                        </tr>
                        </table>
					
			{/if}				
						</td>
					  </tr>
					</table>
					<!--table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr>
					  <td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
					</tr>
					</table-->
				</td>
				</tr>
				</table>
			
			
			
			</td>
			</tr>
			</table>
		</td>
	</tr>
	</form>
	</table>
		
	</div>
</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</table>
{literal}
<script>
function validate() {
	if (!emptyCheck("server","Proxy Server Name","text")) return false
	if (!emptyCheck("port","Port Number","text")) return false
	if(isNaN(document.tandc.port.value)){
		alert(alert_arr.LBL_ENTER_VALID_PORT);
		return false;
	}
	if (!emptyCheck("server_username","Proxy User Name","text")) return false
	if (!emptyCheck("server_password","Proxy Password","text")) return false
			return true;

}
</script>
{/literal}
