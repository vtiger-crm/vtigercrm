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
<tbody>
<tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
    <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">

	<form action="index.php" method="post" id="form" onsubmit="VtigerJS_DialogBox.block();">
		<input type='hidden' name='module' value='Settings'>
		<input type='hidden' name='action' value='MailScanner'>
		<input type='hidden' name='mode' value='save'>
		<input type='hidden' name='return_action' value='MailScanner'>
		<input type='hidden' name='return_module' value='Settings'>
		<input type='hidden' name='parenttab' value='Settings'>

        <br>

		<div align=center>
			{include file='SetMenu.tpl'}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'mailScanner.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MAIL_SCANNER}" width="48" height="48" border=0 title="{$MOD.LBL_MAIL_SCANNER}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_MAIL_SCANNER}</b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_MAIL_SCANNER_DESCRIPTION}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>

				{if $CONNECTFAIL neq ''}
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
							<td align="center" width="100%"><font color='red'><b>{$CONNECTFAIL}</b></font></td>
					</tr>
					</table>
				{/if}

				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
				<td class="big" width="70%"><strong>{$MOD.LBL_MAILBOX} {$MOD.LBL_INFORMATION}</strong></td>
				</tr>
				</table>
				
				<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
				<tr>
	         	    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
						<tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SCANNER} {$MOD.LBL_NAME}</strong> <font color="red">*</font></td>
                            <td width="80%">
                            	<input type="hidden" name="hidden_scannername" class="small" value="{$SCANNERINFO.scannername}" readonly>
                            	<input type="text" name="mailboxinfo_scannername" class="small" value="{$SCANNERINFO.scannername}" size=50>
                            </td>
                        </tr>
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SERVER} {$MOD.LBL_NAME}</strong> <font color="red">*</font></td>
                            <td width="80%"><input type="text" name="mailboxinfo_server" class="small" value="{$SCANNERINFO.server}" size=50></td>
                        </tr>
                        <tr>
							<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_PROTOCOL}</strong> <font color="red">*</font></td>
                            <td width="80%">
								{assign var="imapused" value=""}
								{assign var="imap4used" value=""}

								{if $SCANNERINFO.protocol eq 'imap4'}
									{assign var="imap4used" value="checked='true'"}
								{else}
									{assign var="imapused" value="checked='true'"}
								{/if}
							
								<input type="radio" name="mailboxinfo_protocol" class="small" value="imap" {$imapused}> {$MOD.LBL_IMAP2}
								<input type="radio" name="mailboxinfo_protocol" class="small" value="imap4" {$imap4used}> {$MOD.LBL_IMAP4}
							</td>
						</tr>
						<tr>
			                <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong> <font color="red">*</font></td>
                            <td width="80%"><input type="text" name="mailboxinfo_username" class="small" value="{$SCANNERINFO.username}" size=50></td>
                        </tr>
						<tr>
			                <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_PASSWORD}</strong> <font color="red">*</font></td>
                            <td width="80%"><input type="password" name="mailboxinfo_password" class="small" value="{$SCANNERINFO.password}" size=50></td>
                        </tr>
						<tr>
			                <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SSL} {$MOD.LBL_TYPE}</strong></td>
               				<td width="80%" class="small cellText">
								{assign var="notls_type" value=""}
								{assign var="tls_type" value=""}
								{assign var="ssl_type" value=""}

								{if $SCANNERINFO.ssltype eq 'notls'}
									{assign var="notls_type" value="checked='true'"}
								{elseif $SCANNERINFO.ssltype eq 'tls'}
									{assign var="tls_type" value="checked='true'"}
								{elseif $SCANNERINFO.ssltype eq 'ssl'}
									{assign var="ssl_type" value="checked='true'"}
								{/if}

								<input type="radio" name="mailboxinfo_ssltype" class="small" value="notls" {$notls_type}> {$MOD.LBL_NO} {$MOD.LBL_TLS}
								<input type="radio" name="mailboxinfo_ssltype" class="small" value="tls" {$tls_type}> {$MOD.LBL_TLS}
								<input type="radio" name="mailboxinfo_ssltype" class="small" value="ssl" {$ssl_type}> {$MOD.LBL_SSL}
							</td>
                        </tr>
						<tr>
			                <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SSL} {$MOD.LBL_METHOD}</strong></td>
							<td width="80%" class="small cellText">
								{assign var="novalidatecert_type" value=""}
								{assign var="validatecert_type" value=""}

								{if $SCANNERINFO.sslmethod eq 'validate-cert'}
									{assign var="validatecert_type" value="checked='true'"}
								{else}
									{assign var="novalidatecert_type" value="checked='true'"}
								{/if}

								<input type="radio" name="mailboxinfo_sslmethod" class="small" value="validate-cert" {$validatecert_type}> {$MOD.LBL_VAL_SSL_CERT}
								<input type="radio" name="mailboxinfo_sslmethod" class="small" value="novalidate-cert" {$novalidatecert_type}> {$MOD.LBL_DONOT_VAL_SSL_CERT}
							</td>
                        </tr>
						<tr>
			                <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_STATUS}</strong></td>
							<td width="80%" class="small cellText">
								{assign var="mailbox_enable" value=""}
								{assign var="mailbox_disable" value=""}

								{if $SCANNERINFO.isvalid eq false}
									{assign var="mailbox_disable" value="checked='true'"}
								{else}
									{assign var="mailbox_enable" value="checked='true'"}
								{/if}

								<input type="radio" name="mailboxinfo_enable" class="small" value="true" {$mailbox_enable}> {$MOD.LBL_ENABLE}
								<input type="radio" name="mailboxinfo_enable" class="small" value="false" {$mailbox_disable}> {$MOD.LBL_DISABLE}
							</td>
                        </tr>
				    </td>
            	</tr>

				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
				<td class="big" width="70%"><strong>{$MOD.LBL_SCANNING} {$MOD.LBL_INFORMATION}</strong></td>
				<td width="30%" nowrap align="right">&nbsp;</td>
				</tr>
				</table>

				<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
				<tr>
	         	    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
						<tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_LOOKFOR}</strong></td>
                            <td width="80%" class="small cellText"> 
							<select name="mailboxinfo_searchfor" class="small">
								<option value="ALL" {if $SCANNERINFO.searchfor eq 'ALL'}selected=true{/if} 
									onclick="$('mailboxinfo_rescan_folders_span').show()">{$MOD.LBL_ALL}</option>
								<option value="UNSEEN" {if $SCANNERINFO.searchfor eq 'UNSEEN'}selected=true{/if} 
									onclick="this.form.mailboxinfo_rescan_folders.checked=false;$('mailboxinfo_rescan_folders_span').hide()">{$MOD.LBL_UNREAD}</option> 
							</select> {$MOD.LBL_MESSAGES_FROM_LASTSCAN}

							{if $SCANNERINFO.searchfor eq 'ALL'}
								<span id="mailboxinfo_rescan_folders_span">
								{* Disabling Rescanning of messages for later use *}
								{* -- START	
								<input type="checkbox" name="mailboxinfo_rescan_folders" value="true" class="small" {if $SCANNERINFO.requireRescan}checked=true{/if}> {$MOD.LBL_INCLUDE} {$MOD.LBL_SKIPPED}</input>
								-- END *}
								</span>
							{/if}

							</td>
                        </tr>
						<tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_AFTER_SCAN}</strong></td>
                            <td width="80%" class="small cellText">{$MOD.LBL_MARK_MESSAGE_AS}
							<select name="mailboxinfo_markas" class="small">
								<option value=""></option>
								<option value="SEEN" {if $SCANNERINFO.markas eq 'SEEN'}selected=true{/if} >{$MOD.LBL_READ}</option> 
							</select>	
							</td>
                        </tr>
					</td>
				</tr>
				</table>

				<tr>
					<td colspan=2 nowrap align="center">
						<input type="submit" class="crmbutton small save" value="{$APP.LBL_SAVE_LABEL}" onclick="return mailscaneredit_validateform(this.form);" />
						<input type="button" class="crmbutton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="window.location.href='index.php?module=Settings&action=MailScanner&parenttab=Settings'"/>
					</td>
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
	</table>
		
	</div>

</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</form>
</table>

</tr>
</table>

</tr>
</table>
{literal}
<script type="text/javascript">
function mailscaneredit_validateform(form) {
	var scannername = form.mailboxinfo_scannername;
	if(scannername.value == '') {
		alert(alert_arr.VALID_SCANNER_NAME);
		scannername.focus();
		return false;
	}else{		
		var regex=/^[0-9A-Za-z]+$/; 
		if(regex.test(scannername.value)){
			return true;
		}else {
			alert(alert_arr.VALID_SCANNER_NAME);
			scannername.focus();
			return false;
		}		
	}			
}
</script>
{/literal}
