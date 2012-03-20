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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
<title>{$MOD.TITLE_VTIGERCRM_MAIL}</title>
<link REL="SHORTCUT ICON" HREF="themes/images/vtigercrm_icon.ico">	
<style type="text/css">@import url("themes/{$THEME}/style.css");</style>
<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>
<body marginheight="0" marginwidth="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
   <tr>
	<td colspan="3">
		<table border=0 cellspacing=0 cellpadding=0 width=100% class="mailClientWriteEmailHeader">
		<tr>
		<td >{$MOD.LBL_DETAILVIEW_EMAIL}</td>
		</tr>
		</table>

	</td>
   </tr> 
   {foreach item=row from=$BLOCKS}
   {foreach item=elements key=title from=$row}
   {if $elements.fldname eq 'subject'}	
   	<tr>
	<td class="lvtCol" width="15%" height="70px" style="padding: 5px;" align="right"><b>{$MOD.LBL_TO}</b></td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$TO_MAIL}</td>
	<td class="dvtCellLabel" width="20%" rowspan="4"><div id="attach_cont" class="addEventInnerBox" style="overflow:auto;height:140px;width:100%;position:relative;left:0px;top:0px;"></td>
   	</tr>
	   {if 'ccmail'|@emails_checkFieldVisiblityPermission eq '0'}
	   <tr>
		<td class="lvtCol" style="padding: 5px;" align="right"><b>{$MOD.LBL_CC}</b></td>
		<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$CC_MAIL}</td>
	   </tr>
	   {/if}
	   {if 'bccmail'|@emails_checkFieldVisiblityPermission eq '0'}
	   <tr>
		<td class="lvtCol" style="padding: 5px;" align="right"><b>{$MOD.LBL_BCC}</b></td>
		<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$BCC_MAIL}</td>
	   </tr>
	   {/if}
	<tr>
	<td class="lvtCol" style="padding: 5px;" align="right"><b>{$MOD.LBL_SUBJECT}</b></td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$elements.value}</td>
   	</tr>
   	<tr>
	<td colspan="3" class="dvtCellLabel" style="padding: 10px;" align="center"><input type="button" name="forward" value=" {$MOD.LBL_FORWARD_BUTTON} " alt="{$MOD.LBL_FORWARD_BUTTON}" title="{$MOD.LBL_FORWARD_BUTTON}" class="crmbutton small edit" onClick=OpenCompose('{$ID}','forward')>&nbsp;
	<input type="button" title="{$APP.LBL_EDIT}" alt="{$APP.LBL_EDIT}" name="edit" value=" {$APP.LBL_EDIT} " class="crmbutton small edit" onClick=OpenCompose('{$ID}','edit')>&nbsp;

	<input name="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " title="{$APP.LBL_CANCEL_BUTTON_LABEL}" alt="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmbutton small cancel" type="button" onClick="window.close()">
	&nbsp;
	<input type="button" title="{$MOD.LBL_PRINT_EMAIL}" name="{$MOD.LBL_PRINT_EMAIL}" value="{$MOD.LBL_PRINT_EMAIL}" class="crmbutton small edit" onClick="OpenCompose('{$ID}', 'print')">&nbsp;
	</td>
	</tr>
   {elseif $elements.fldname eq 'description'}
   	<tr>
	<td style="padding: 5px;" colspan="3" valign="top"><div style="overflow:auto;height:415px;width:100%;">{$elements.value}</div></td>

   	</tr>
   {elseif $elements.fldname eq 'filename'}
   	<tr><td colspan="3">
   	<div id="attach_temp_cont" style="display:none;">
		<table class="small" width="100% ">
		{foreach item=attachments from=$elements.options}
			<tr><td width="90%">{$attachments}</td></tr>	
		{/foreach}	
		</table>	
	</div>	
   	</td></tr>	
   {/if}	
   {/foreach}
   {/foreach}
   <tr>
	<td colspan="3" class="dvtCellLabel" style="padding: 10px;" align="center"><input type="button" name="forward" value=" {$MOD.LBL_FORWARD_BUTTON} " alt="{$MOD.LBL_FORWARD_BUTTON}" title="{$MOD.LBL_FORWARD_BUTTON}" class="crmbutton small edit" onClick=OpenCompose('{$ID}','forward')>&nbsp;
	<input type="button" title="{$APP.LBL_EDIT}" alt="{$APP.LBL_EDIT}" name="edit" value=" {$APP.LBL_EDIT} " class="crmbutton small edit" onClick=OpenCompose('{$ID}','edit')>&nbsp;

	<input name="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " title="{$APP.LBL_CANCEL_BUTTON_LABEL}" alt="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmbutton small cancel" type="button" onClick="window.close()">
	&nbsp;
	<input type="button" title="{$MOD.LBL_PRINT_EMAIL}" name="{$MOD.LBL_PRINT_EMAIL}" value="{$MOD.LBL_PRINT_EMAIL}" class="crmbutton small edit" onClick="OpenCompose('{$ID}', 'print')"> &nbsp;
	</td>
   </tr>


</table>		
<script>
$('attach_cont').innerHTML = $('attach_temp_cont').innerHTML;
</script>
