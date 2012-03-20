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
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<br>
	<div align=center>
			{include file='SetMenu.tpl'}
			<!-- DISPLAY -->
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
			<form action="index.php" method="post" name="index" id="form" onsubmit="VtigerJS_DialogBox.block();">
			<input type="hidden" name="module" value="Settings">
			<input type="hidden" name="parenttab" value="{$PARENTTAB}">
			<input type="hidden" name="action" value="index">
			<input type="hidden" name="record" value="{$ID}">
			<tr>
				<td width=50 rowspan=2 valign=top><img src="{'currency.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" width="48" height="48" border=0 title="{$MOD.LBL_USERS}"></td>
				<td class="heading2" valign="bottom" ><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=CurrencyListView&parenttab=Settings">{$MOD.LBL_CURRENCY_SETTINGS}</a> > 
				{if $ID neq ''}
					{$MOD.LBL_EDIT} &quot;{$CURRENCY_NAME}&quot; 
				{else}
					{$MOD.LBL_NEW_CURRENCY}
				{/if}
				</b></td>
			</tr>
			<tr>
				<td valign=top class="small">{$MOD.LBL_CURRENCY_DESCRIPTION}</td>
			</tr>
			</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						{if $ID neq ''}
							<td class="big"><strong>{$MOD.LBL_SETTINGS} {$APP.LBL_FOR} &quot;{$CURRENCY_NAME|@getTranslatedCurrencyString}&quot;  </strong></td>
						{else}
							<td class="big"><strong>&quot;{$MOD.LBL_NEW_CURRENCY}&quot;  </strong></td>
						{/if}
						<td class="small" align=right>
							<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" onclick="this.form.action.value='SaveCurrencyInfo'; return validate()" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" >&nbsp;&nbsp;
							<div id="CurrencyEditLay"  class="layerPopup" style="display:none;width:25%;">
								<table width="100%" border="0" cellpadding="3" cellspacing="0" class="layerHeadingULine">
								<tr>
									<td class="layerPopupHeading"  align="left" width="60%">{$MOD.LBL_TRANSFER_CURRENCY}</td>
									<td align="right" width="40%"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border=0 alt="{$APP.LBL_CLOSE}" title="{$APP.LBL_CLOSE}" style="cursor:pointer;" onClick="document.getElementById('CurrencyEditLay').style.display='none'";></td>
								</tr>
								<table>
								<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
									<tr>
										<td class=small >
											<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
												<tr>
													<td width="50%" class="cellLabel small"><b>{$MOD.LBL_CURRENT_CURRENCY}</b></td>
													<td width="50%" class="cellText small"><b>{$CURRENCY_NAME|@getTranslatedCurrencyString}</b></td>
												</tr>
												<tr>
													<td class="cellLabel small"><b>{$MOD.LBL_TRANSCURR}</b></td>
													<td class="cellText small">
														<select class="select small" name="transfer_currency_id" id="transfer_currency_id">';
														 {foreach key=cur_id item=cur_name from=$OTHER_CURRENCIES}
															 <option value="{$cur_id}">{$cur_name|@getTranslatedCurrencyString}</option>
														 {/foreach}
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
								<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
									<tr>
										<td align="center"><input type="button" onclick="form.submit();" name="Update" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmbutton small save">
										</td>
									</tr>
								</table>
							</div>
							<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmButton small cancel" onclick="window.history.back()" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
						</td>
					</tr>
					</table>
					
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
				<td class="small" valign=top >
				<table width="100%"  border="0" cellspacing="0" cellpadding="5">
				<tr>
					<td width="20%" nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_CURRENCY_NAME}</strong></td>
					<td width="80%" class="small cellText">
						<!-- input type="hidden" class="detailedViewTextBox small" value="" name="currency_name" -->
						<select name="currency_name" id="currency_name" class="small" onChange='updateSymbolAndCode();'>
					{foreach key=header item=currency from=$CURRENCIES}
			        	        {if $header eq $CURRENCY_NAME}
			        	        	<option value="{$header}" selected>{$header|@getTranslatedCurrencyString}({$currency.1})</option>
			        	        {else}
			        	        	<option value="{$header}" >{$header|@getTranslatedCurrencyString}({$currency.1})</option>
			        	        {/if}
   					{/foreach}
 						</select>
 					</td>
				</tr>
				<tr valign="top">
					<td nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_CURRENCY_CODE}</strong></td>
 					<td class="small cellText"><input type="text" readonly class="detailedViewTextBox small" value="{$CURRENCY_CODE}" name="currency_code" id="currency_code"></td>
				</tr>
				<tr valign="top">
					<td nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_CURRENCY_SYMBOL}</strong></td>
					<td class="small cellText"><input type="text" readonly class="detailedViewTextBox small" value="{$CURRENCY_SYMBOL}" name="currency_symbol" id="currency_symbol"></td>
				</tr>
				<tr valign="top">
					<td nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_CURRENCY_CRATE}</strong><br>({$MOD.LBL_BASE_CURRENCY}{$MASTER_CURRENCY|@getTranslatedCurrencyString})</td>
					<td class="small cellText"><input type="text" class="detailedViewTextBox small" value="{$CONVERSION_RATE}" name="conversion_rate"></td>
				</tr>
				<tr>
					<td nowrap class="small cellLabel"><strong>{$MOD.LBL_CURRENCY_STATUS}</strong></td>
					<td class="small cellText">
						<input type="hidden" value="{$CURRENCY_STATUS}" id="old_currency_status" />
							<select name="currency_status" {$STATUS_DISABLE} class="importBox">
									<option value="Active"  {$ACTSELECT}>{$MOD.LBL_ACTIVE}</option>
				        	        <option value="Inactive" {$INACTSELECT}>{$MOD.LBL_INACTIVE}</option>
							</select>
					</td>
				</tr>	
                       </table>
						
						</td>
					  </tr>
					</table>
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr>
					  <td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
					</tr>
					</table>
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
			if (!emptyCheck("currency_name","Currency Name","text")) return false
			if (!emptyCheck("currency_code","Currency Code","text")) return false
			if (!emptyCheck("currency_symbol","Currency Symbol","text")) return false
			if (!emptyCheck("conversion_rate","Conversion Rate","text")) return false
			if (!emptyCheck("currency_status","Currency Status","text")) return false
			if(isNaN(getObj("conversion_rate").value) || eval(getObj("conversion_rate").value) <= 0)
			{
{/literal}
            	alert("{$APP.ENTER_VALID_CONVERSION_RATE}")
                return false
{literal}
			}
			if (getObj("currency_status") != null && getObj("currency_status").value == "Inactive" 
					&& getObj("old_currency_status") != null && getObj("old_currency_status").value == "Active")
			{
				if (getObj("CurrencyEditLay") != null) getObj("CurrencyEditLay").style.display = "block";
				return false;
			} 
			else 
			{
				return true;
			}
        }
{/literal}
var currency_array = {$CURRENCIES_ARRAY}
{literal}
updateSymbolAndCode();
function updateSymbolAndCode(){
	selected_curr = document.getElementById('currency_name').value;
	getObj('currency_code').value = currency_array[selected_curr][0];
	getObj('currency_symbol').value = currency_array[selected_curr][1];
}
</script>
{/literal}
