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
	
			{include file='SetMenu.tpl'}

				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<form action="index.php?module=Settings&action=savewordtemplate" method="post" enctype="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="return_module" value="Settings">
				<input type="hidden" name="parenttab" value="{$PARENTTAB}">
				<input type="hidden" name="MAX_FILE_SIZE" value="{$MAX_FILE_SIZE}">
				<input type="hidden" name="action">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'mailmarge.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MODULE_NAME}" width="48" height="48" border=0 title="{$MOD.LBL_MODULE_NAME}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=listwordtemplates&parenttab=Settings">{$UMOD.LBL_WORD_TEMPLATES}</a> > {$UMOD.LBL_NEW_TEMPLATE} </b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_MAIL_MERGE_DESC}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$UMOD.LBL_NEW_TEMPLATE}</strong><br>{$ERRORFLAG}</br></td>
						<td class="small" align=right>
							<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" type="submit" tabindex="4" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="this.form.action.value='savewordtemplate'; this.form.parenttab.value='Settings'" class="crmButton small save" />&nbsp;
							&nbsp;<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" tabindex="5"  onclick="window.history.back();"  type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" />
						</td>
					</tr>
					</table>
					
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr valign="top">
					  <td nowrap class="cellLabel small"><font color="red">*</font><strong>{$UMOD.LBL_NEW} {$UMOD.LBL_TEMPLATE_FILE}</strong></td>
					  <td class="cellText small"><strong>
					    <input type="file" name="binFile" class="small" onchange="validateFilename(this);" />
					    <input type="hidden" name="binFile_hidden" value="" />
					  </strong></td>
					  </tr>
					<tr>
						<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_DESCRIPTION}</strong></td>
						<td class="cellText small" valign=top><textarea name="txtDescription" class=small style="width:90%;height:50px">{$smarty.request.description|@vtlib_purify}</textarea></td>
					  </tr>
					<tr>
						<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_MODULENAMES}</strong></td>
						<td class="cellText small" valign=top>
						<select name="target_module" size=1 class="small" tabindex="3">
						<option value="Leads" {$LEADS_SELECTED}>{$APP.COMBO_LEADS}</option>
						<option value="Accounts" {$ACCOUNTS_SELECTED}>{$APP.COMBO_ACCOUNTS}</option>
						<option value="Contacts" {$CONTACTS_SELECTED}>{$APP.COMBO_CONTACTS}</option>
						<option value="HelpDesk" {$HELPDESK_SELECTED}>{$APP.COMBO_HELPDESK}</option>
			                      </select>
						</td>
					  </tr>
					</table>
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr>
					  <td class="small" nowrap align=right><a href="#top">{$APP.LBL_SCROLL}</a></td>
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
