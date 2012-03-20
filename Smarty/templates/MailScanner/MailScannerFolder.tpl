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
<script type="text/javascript">
{literal}
function vtmailscanner_folders_resetAll_To(checktype) {
	var form = $('form');
	var inputs = form.getElementsByTagName('input');
	for(var index = 0; index < inputs.length; ++index) {
		var input = inputs[index];
		if(input.type == 'checkbox' && input.name.indexOf('folder_') == 0) {
			input.checked = checktype;
		}
	}
}
{/literal}
</script>

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody>
<tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
    <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">

	<form action="index.php" method="post" id="form" onsubmit="VtigerJS_DialogBox.block();">
		<input type='hidden' name='module' value='Settings'>
		<input type='hidden' name='action' value='MailScanner'>
		<input type='hidden' name='scannername' value="{$SCANNERINFO.scannername}">
		<input type='hidden' name='mode' value='foldersave'>
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
				
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
					<td class="big" width="70%"><strong>{$MOD.LBL_MAILBOX} {$MOD.LBL_FOLDER}</strong></td>
					<td align="right">
						<input type="submit" class="crmbutton small create" onclick="this.form.mode.value='folderupdate'" value="{$MOD.LBL_UPDATE}"> 
						<a href='javascript:void(0);' onclick="vtmailscanner_folders_resetAll_To(true);">{$MOD.LBL_SELECT} {$MOD.LBL_ALL}</a> |
						<a href='javascript:void(0);' onclick="vtmailscanner_folders_resetAll_To(false);">{$MOD.LBL_UNSELECT} {$MOD.LBL_ALL}</a>
					</td>
				</tr>
				</table>

				{assign var="FOLDER_COL_LIMIT" value="4"}				
				{assign var="FOLDER_COL_INDEX" value="0"}				
				{assign var="FOLDER_ROW_OPEN" value="false"}

				<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
				<tr valign=top>
	         	    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
						{foreach item=FOLDER key=FOLDERNAME from=$FOLDERINFO}
						{if ($FOLDER_COL_INDEX % $FOLDER_COL_LIMIT) eq 0}
						<tr>
						{assign var="FOLDER_ROW_OPEN" value="true"}
						{/if}
							<td>
								<input type="checkbox" name="folder_{$FOLDER.folderid}" value="{$FOLDERNAME}" {if $FOLDER.enabled}checked="true"{/if}>
								<a href='javascript:void(0)' title='Lastscan: {$FOLDER.lastscan}'>{$FOLDERNAME}</a></td>
						{if ($FOLDER_COL_INDEX % $FOLDER_COL_LIMIT) eq ($FOLDER_COL_LIMIT-1)}
						</tr>
						{assign var="FOLDER_ROW_OPEN" value="false"}
						{/if}
						{assign var="FOLDER_COL_INDEX" value=$FOLDER_COL_INDEX+1}
						{/foreach}
						{if $FOLDER_ROW_OPEN}</tr>{/if}
					</td>
				</tr>
				<tr>
					<td colspan="{$FOLDER_COL_LIMIT}" nowrap align="center">
						<input type="submit" class="crmbutton small save" value="{$APP.LBL_SAVE_LABEL}" />
						<input type="button" class="crmbutton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" 
							onclick="location.href='index.php?module=Settings&action=MailScanner&parenttab=Settings&scannername={$SCANNERINFO.scannername}'"/>
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
