{*<!--
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">

<tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" valign="top" width="100%">
                <div class="small" style="padding: 10px;">
                        <span class="lvtHeaderText">{$MOD.LBL_MY_MAIL_SERVER_DET}</span> <br>
                        <hr noshade="noshade" size="1"><br>

  		<form action="index.php" method="post" name="EditView" id="form" onsubmit="VtigerJS_DialogBox.block();">
			<input type="hidden" name="module" value="Users">
		  	<input type="hidden" name="action">
  			<input type="hidden" name="server_type" value="email">
			<input type="hidden" name="record" value="{$ID}">
		        <input type="hidden" name="edit" value="{$EDIT}">
			<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
			<input type="hidden" name="return_action" value="{$RETURN_ACTION}">
			<input type="hidden" name="changepassword" value="">
</tr>	
		
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
                  <tr>

                        <td>
                            <table class="small" border="0" cellpadding="3" cellspacing="0" width="100%">
                                <tr>
                                    <td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
                                    <td class="dvtSelectedCell" style="width: 100px;" align="center" nowrap="nowrap"><b>{$MOD.LBL_MY_MAIL_SERVER_DET} </b></td>
		                    <td class="dvtTabCache" nowrap="nowrap">&nbsp;</td>
                                </tr>

                            </table>
                        </td>
                </tr>
                <tr>
                        <td align="left" valign="top">

<!-- General Contents for Mail Server Starts Here -->

<table class="dvtContentSpace" border="0" cellpadding="3" cellspacing="0" width="100%">
<tr>
   <td align="left">
     <table border="0" cellpadding="0" cellspacing="0" width="100%">
       <tr>
          <td style="padding: 10px;"><table width="100%"  border="0" cellspacing="0" cellpadding="5">
       <tr>
           <td colspan="3" class="detailedViewHeader"><b>{$MOD.LBL_EMAIL_ID}</b></td>
       </tr>
       <tr>
          <td class="dvtCellLabel" align="right" width="33%">{$MOD.LBL_DISPLAY_NAME}</td>
          <td class="dvtCellInfo" width="33%"><input type="text" name="displayname" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" value="{$DISPLAYNAME}"/></td>
          <td class="dvtCellInfo" width="34%">{$MOD.LBL_NAME_EXAMPLE}</td>
       </tr>
       <tr>
          <td class="dvtCellLabel" align="right"><FONT class="required" color="red">{$APP.LBL_REQUIRED_SYMBOL}</FONT> {$MOD.LBL_EMAIL_ADDRESS} </td>
          <td class="dvtCellInfo"><input type="text" name="email" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" value="{$EMAIL}"/></td>
          <td class="dvtCellInfo">{$MOD.LBL_EMAIL_EXAMPLE}</td>
       </tr>
       <tr><td colspan="3" >&nbsp;</td></tr>
       <tr>
          <td colspan="3"  class="detailedViewHeader"><b>{$MOD.LBL_INCOME_SERVER_SETTINGS}</b></td>
       </tr>
       <tr>
          <td class="dvtCellLabel" align="right"><FONT class="required" color="red">{$APP.LBL_REQUIRED_SYMBOL}</FONT>{$MOD.LBL_MAIL_SERVER_NAME}</td>
          <td class="dvtCellInfo"><input type="text" name="mail_servername" value="{$SERVERNAME}"  class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'"/></td>
          <td class="dvtCellInfo">&nbsp;</td>
       </tr>
       <tr>
           <td class="dvtCellLabel" align="right"><FONT class="required" color="red">{$APP.LBL_REQUIRED_SYMBOL}</FONT>{$APP.LBL_LIST_USER_NAME}</td>
           <td class="dvtCellInfo"><input type="text" name="server_username" value="{$SERVERUSERNAME}"  class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'"/></td>
           <td class="dvtCellInfo">&nbsp;</td>
       </tr>
       <tr>
           <td class="dvtCellLabel" align="right"><FONT class="required" color="red">{$APP.LBL_REQUIRED_SYMBOL}</FONT>{$MOD.LBL_LIST_PASSWORD}</td>
           <td class="dvtCellInfo"> {$CHANGE_PW_BUTTON}</td>
           <td class="dvtCellInfo">&nbsp;</td>
       </tr>
       <tr>
           <td colspan="3" class="dvtCellInfo">&nbsp;</td>
       </tr>
       <tr>
           <td class="dvtCellLabel" align="right">{$MOD.LBL_MAIL_PROTOCOL}</td>
           <td class="dvtCellInfo">
		<!-- <input type="radio" name="mailprotocol" value="pop3" {$POP3}/>&nbsp;{$MOD.LBL_POP} <font color="red">* *</font>&nbsp;
		<input type="radio" name="mailprotocol" value="imap" {$IMAP}/>&nbsp;{$MOD.LBL_IMAP} <font color="red">* *</font>&nbsp; -->
		<input type="radio" name="mailprotocol" value="imap2" {$IMAP2}/>&nbsp;{$MOD.LBL_IMAP2}
		<input type="radio" name="mailprotocol" value="IMAP4" {$IMAP4}/>&nbsp;{$MOD.LBL_IMAP4}
	   </td>	
           <td class="dvtCellInfo">&nbsp;</td>
        </tr>
        <tr>
           <td class="dvtCellLabel" align="right">{$MOD.LBL_SSL_OPTIONS}</td>
           <td class="dvtCellInfo">
		<input type="radio" name="ssltype" value="notls" {$NOTLS} />&nbsp;{$MOD.LBL_NO_TLS}
		<input type="radio" name="ssltype" value="tls" {$TLS} />&nbsp; {$MOD.LBL_TLS}
		<input type="radio" name="ssltype" value="ssl" {$SSL} />&nbsp; {$MOD.LBL_SSL} </td>
           <td class="dvtCellInfo">&nbsp;</td>
       </tr>
       <tr>
           <td class="dvtCellLabel" align="right">{$MOD.LBL_CERT_VAL}</td>
           <td class="dvtCellInfo">
		<input type="radio" name="sslmeth" value="validate-cert" {$VALIDATECERT} />&nbsp;{$MOD.LBL_VAL_SSL_CERT}
		<input type="radio" name="sslmeth" value="novalidate-cert" {$NOVALIDATECERT} />&nbsp;{$MOD.LBL_DONOT_VAL_SSL_CERT}
	   </td>	
           <td class="dvtCellInfo">&nbsp;</td>
       </tr>
       <!--<tr>
           <td class="dvtCellLabel" align="right">{$MOD.LBL_INT_MAILER}</td>
           <td class="dvtCellInfo">
		<input type="radio" name="int_mailer" value="1" {$INT_MAILER_USE} />&nbsp;{$MOD.LBL_INT_MAILER_USE}
		<input type="radio" name="int_mailer" value="0" {$INT_MAILER_NOUSE} />&nbsp;{$MOD.LBL_INT_MAILER_NOUSE}
	   </td>	
           <td class="dvtCellInfo">&nbsp;</td>
       </tr>-->
       <tr>
           <td class="dvtCellLabel" align="right">{$MOD.LBL_REFRESH_TIMEOUT}</td>
           <td class="dvtCellInfo">
		<select value="{$BOX_REFRESH}" name="box_refresh">
			<option value="60000" {$BOX_OPT1}>{$MOD.LBL_1_MIN}</option>
			<option value="120000" {$BOX_OPT2}>{$MOD.LBL_2_MIN}</option>
			<option value="180000" {$BOX_OPT3}>{$MOD.LBL_3_MIN}</option>
			<option value="240000" {$BOX_OPT4}>{$MOD.LBL_4_MIN}</option>
			<option value="300000" {$BOX_OPT5}>{$MOD.LBL_5_MIN}</option>
		</select>
	   </td>
           <td class="dvtCellInfo">&nbsp;</td>
       </tr>
       <tr>
           <td class="dvtCellLabel" align="right">{$MOD.LBL_EMAILS_PER_PAGE}</td>
           <td class="dvtCellInfo"><input type="text" name="mails_per_page" value="{$MAILS_PER_PAGE}" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'"/></td>
           <td class="dvtCellInfo">&nbsp;</td>
	</tr><tr>
	<td colspan='3' align='center'>{$MOD.LBL_MAIL_DISCLAIM}</td>
       </tr>
       <tr><td colspan="3" style="border-bottom:1px dashed #CCCCCC;">&nbsp;</td></tr>
       <tr>
           <td colspan="3" align="center">
		<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.action.value='SaveMailAccount'; return verify_data(EditView)" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " >
			&nbsp;&nbsp;
	        <input title="{$APP.LBL_CANCEL_BUTTON_LABEL}>" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="window.history.back()" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}"></td>
           </td>
       </tr>
       <tr><td colspan="3" style="border-top:1px dashed #CCCCCC;">&nbsp;</td></tr>
       </table>
	   </td>
            </tr>

     </table></td>
     </tr>
</table>
</td></tr>
</table>
</form>
</td></tr>
</table>

{$JAVASCRIPT}
