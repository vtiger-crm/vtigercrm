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
<script language="JavaScript" type="text/javascript" src="modules/Webmails/Webmails.js"></script>

<table width="100%" border="0" cellpadding="0" cellspacing="0" valign="top">
<tr>
    <td class="hdrNameBg">
  		<table width="100%"  border="0" cellspacing="5" cellpadding="0">
		<tr>
		{if $BLOCKS neq ''}
			<td align="left">
			  	<input type="button" name="forward" value=" {$MOD.LBL_FORWARD_BUTTON} " class="crmbutton small edit" onClick=OpenCompose('{$ID}','forward')>&nbsp;
				{if $EMAIL_FLAG neq 'WEBMAIL'}
			  	<input type="button" name="Send" value=" {$MOD.LBL_SEND} " class="crmbutton small save" onClick=OpenCompose('{$ID}','edit')>&nbsp;	
				<input type="button" name="Reply" value=" {$MOD.LBL_REPLY_BUTTON} " class="crmbutton small edit" onClick=ReplyCompose('{$ID}','reply')>&nbsp;
				<input type="button" title="{$MOD.LBL_PRINT_EMAIL}" name="{$MOD.LBL_PRINT_EMAIL}" value="{$MOD.LBL_PRINT_EMAIL}" class="crmbutton small edit" onClick=OpenCompose('{$ID}','print')> 
				{else}
			  	<input type="button" name="Send" value=" {$MOD.LBL_REPLY_BUTTON} " class="crmbutton small edit" onClick=OpenCompose('{$ID}','edit')>&nbsp;
				{/if}
				{foreach item=row from=$BLOCKS}	
				{foreach item=elements key=title from=$row}	
					{if $elements.fldname eq 'filename' && $elements.value != ''}
						<input type="button" name="download" value=" {$MOD.LBL_DOWNLOAD_ATTCH_BUTTON} " class="crmbutton small save" onclick="fnvshobj(this,'reportLay')"/>
					{/if}
				{/foreach}
				{/foreach}
			</td>
						<td width="25%" align="right"><input type="button" name="Button" value=" {$APP.LBL_DELETE_BUTTON} "  class="crmbutton small delete" onClick="DeleteEmail('{$ID}')"/></td>
						{else}
						<td colspan="2">&nbsp;</td>
						{/if}
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td height="250" bgcolor="#FFFFFF" valign="top" class="MatrixLayer2">
	{foreach item=row from=$BLOCKS}	
	{foreach item=elements from=$row}	
		{if $elements.fldname eq 'subject'}
		<table class="tableHeadBg" width="100%" border="0" cellpadding="0" cellspacing="0">
	{if $EMAIL_FLAG neq 'WEBMAIL'}
	<tr><td width="20%" align="right" valign="top"><b>{$MOD.LBL_FROM}</b></td><td width="2%" align="left">&nbsp;</td><td align="left">{$FROM_MAIL}&nbsp;</td></tr>
	<tr><td width="20%" align="right" valign="top"><b>{$MOD.LBL_TO}</b></td><td width="2%" align="left">&nbsp;</td><td align="left">{$TO_MAIL}&nbsp;</td></tr>
		{if 'ccmail'|@emails_checkFieldVisiblityPermission eq '0'}
		<tr><td align="right" valign="top">{$MOD.LBL_CC}</td><td align="left">&nbsp;</td><td align="left">{$CC_MAIL}&nbsp;</td></tr>
		{/if}
		{if 'bccmail'|@emails_checkFieldVisiblityPermission eq '0'}
		<tr><td align="right" valign="top">{$MOD.LBL_BCC}</td><td align="left">&nbsp;</td><td align="left">{$BCC_MAIL}&nbsp;</td></tr>
		{/if}
	{else}
	<tr><td width="20%" align="right" valign="top"><b>{$MOD.LBL_FROM}</b></td><td width="2%" align="left">&nbsp;</td><td align="left">{$TO_MAIL}&nbsp;</td></tr>
	{/if}
	<tr><td align="right"><b>{$MOD.LBL_SUBJECT}</b></td><td align="left">&nbsp;</td><td align="left">{$elements.value}&nbsp;</td></tr>
			<tr><td align="right" style="border-bottom:1px solid #666666;" colspan="3">&nbsp;</td></tr>
		</table>
		{elseif $elements.fldname eq 'description'}
		<div>
			{$elements.value}
		</div>
		{/if}
	{/foreach}
	{/foreach}
	</td>
</tr>
</table>
{foreach item=row from=$BLOCKS}	
	{foreach item=elements key=title from=$row}	
	{if $elements.fldname eq 'filename'}
	<div id="reportLay" style="width:130px;" onmouseout="fninvsh('reportLay')" onmouseover="fnvshNrm('reportLay')">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF">
		{foreach item=attachments from=$elements.options}
		<tr>
			<td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;">
				{$attachments}
			</td>
		</tr>
		{/foreach}
		</table>
	</div>
	{/if}
	{/foreach}
{/foreach}

