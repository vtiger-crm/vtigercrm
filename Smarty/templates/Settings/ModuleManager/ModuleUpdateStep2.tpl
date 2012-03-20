<div id="vtlib_modulemanager_update" style="display:block;position:absolute;width:500px;"></div>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
    <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
    <br>

	<div align=center>
		{include file='SetMenu.tpl'}
		
		<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td rowspan="2" valign="top" width="50"><img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}" border="0" height="48" width="48"></td>
			<td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> &gt; {$MOD.VTLIB_LBL_MODULE_MANAGER} &gt; {$MOD.LBL_UPGRADE}</b></td>
		</tr>

		<tr>
			<td class="small" valign="top">{$MOD.VTLIB_LBL_MODULE_MANAGER_DESCRIPTION}</td>
		</tr>
		</table>
				
		<br>
		<table border="0" cellpadding="10" cellspacing="0" width="100%">
		<tr>
			<td>
				<div id="vtlib_modulemanager_update_div">
                	<form method="POST" action="index.php">
						{if $MODULEUPDATE_FAILED neq ''}
							<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
							<tr>
								<td class='big' colspan=2><b>{$MOD.VTLIB_LBL_UPDATE_FAILURE}</b></td>
							</tr>
							</table>
							<table cellpadding=5 cellspacing=0 border=0 width=80%>
							<tr valign=top>
								<td class='cellText small'>
									{if $MODULEUPDATE_FILE_INVALID eq "true"}
										<font color=red><b>{$MOD.VTLIB_LBL_INVALID_FILE}</b></font> {$MOD.VTLIB_LBL_INVALID_IMPORT_TRY_AGAIN}
									{elseif $MODULEUPDATE_NAME_MISMATCH eq "true"}
										<font color=red><b>{$MOD.VTLIB_LBL_MODULENAME_MISMATCH}!</b></font> {$MOD.VTLIB_LBL_TRY_AGAIN}
									{elseif $MODULEUPDATE_SAME_VERSION eq "true"}
										<font color=red><b>{$MOD.VTLIB_LBL_CANNOT_UPGRADE}</b></font> {$MOD.VTLIB_LBL_INST_VERSION} <font color=red><b>{$MODULEUPDATE_CUR_VERSION}</b></font> {$MOD.VTLIB_LBL_MATCHES_PACKAGE_VERSION}
									{else}
										<font color=red>{$MOD.VTLIB_LBL_UNABLE_TO_UPLOAD}</font> {$MOD.VTLIB_LBL_UNABLE_TO_UPLOAD2}
									{/if}
								</td>
							</tr>
							</table>
							<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
							<tr valign=top>
								<td class='cellText small' colspan=2 align=right>
									<input type="hidden" name="module" value="Settings">
									<input type="hidden" name="action" value="ModuleManager">
									<input type="hidden" name="parenttab" value="Settings">						
									<input type="submit" class="crmbutton small delete" value="{$APP.LBL_FINISH}">
								</td>
							</tr>
							</table>
						{else}
							<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
							<tr>
								<td class='big' colspan=2><b>{$MOD.VTLIB_LBL_VERIFY_UPDATE_DETAILS}</b></td>
							</tr>
							</table>
							<table cellpadding=5 cellspacing=0 border=0 width=100%>
							<tr valign=top>
								<td class='cellLabel small' width=20%>
									<b>{$MOD.VTLIB_LBL_MODULE_NAME}</b>
								</td>
								<td class='cellText small'>
									{$MODULEUPDATE_NAME}
									{if $MODULEUPDATE_NOT_EXISTS eq 'true'} <font color=red><b>{$MOD.VTLIB_LBL_NOT_PRESENT}</b></font> {/if}
								</td>
							</tr>
							{if $MODULEUPDATE_DIR}
							<tr valign=top>
								<td class='cellLabel small' width=20%>
									<b>{$MOD.VTLIB_LBL_MODULE_DIR}</b>
								</td>
								<td class='cellText small'>
									{$MODULEUPDATE_DIR}
									{if $MODULEUPDATE_DIR_NOT_EXISTS eq 'true'} <font color=red><b>{$MOD.VTLIB_LBL_NOT_PRESENT}</b></font> 
										{* -- Avoiding File Overwrite
										 <br> Overwrite existing files? <input type="checkbox" name="module_dir_overwrite" value="true"> 
										-- *}
									{/if}
								</td>
							</tr>
							{/if}
							<tr valign=top>
								<td class='cellLabel small' width=20%>
									<b>{$MOD.VTLIB_LBL_PACKAGE_VERSION}</b>
								</td>
								<td class='cellText small'>
									{$MODULEUPDATE_VERSION} {if $MODULEUPDATE_CUR_VERSION neq ''} [<b>{$MOD.VTLIB_LBL_INST_VERSION}</b> {$MODULEUPDATE_CUR_VERSION}]{/if}
								</td>
							</tr>
							<tr valign=top>
								<td class='cellLabel small' width=20%>
									<b>{$MOD.VTLIB_LBL_REQ_VTIGER_VERSION}</b>
								</td>
								<td class='cellText small'>
									{$MODULEUPDATE_DEP_VTVERSION}
								</td>
							</tr>

							{assign var="need_license_agreement" value="false"}

							{if $MODULEUPDATE_LICENSE}
							{assign var="need_license_agreement" value="true"}
							<tr valign=top>
								<td class='cellLabel small' width=20%>
									<b>{$MOD.VTLIB_LBL_LICENSE}</b>
								</td>
								<td class='cellText small'>
									<textarea readonly class='small' style="background-color: #F5F5F5; border: 0; height: 150px; font: 10px 'Lucida Console', 'Courier New', Arial, sans-serif;">{$MODULEUPDATE_LICENSE}</textarea><br>
									{literal}
									<input type="checkbox" onclick="if(this.form.yesbutton){if(this.checked){this.form.yesbutton.disabled=false;}else{this.form.yesbutton.disabled=true;}}"> {/literal} {$MOD.VTLIB_LBL_LICENSE_ACCEPT_AGREEMENT}	
								</td>
							</tr>
							{/if}
							</table>
							<table class='tableHeading' cellpadding=5 cellspacing=0 border=0 width=100%>
							<tr valign=top>
								<td class='cellText small' colspan=2 align=right>
									<input type="hidden" name="module" value="Settings">
									<input type="hidden" name="action" value="ModuleManager">
									<input type="hidden" name="parenttab" value="Settings">
									<input type="hidden" name="module_import_file" value="{$MODULEUPDATE_FILE}">
									<input type="hidden" name="module_update_type" value="{$MODULEUPDATE_TYPE}">
									<input type="hidden" name="module_update" value="Step3">
									<input type="hidden" name="target_modulename" value="{$smarty.request.target_modulename|@vtlib_purify}">
									<input type="hidden" name="module_import_cancel" value="false">

									{if $MODULEUPDATE_NOT_EXISTS eq 'true' || $MODULEUPDATE_DIR_NOT_EXISTS eq 'true'}										
										<input type="submit" class="crmbutton small delete" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" 
												onclick="this.form.module_update.value=''; this.form.module_import_cancel.value='true';">
									{else}
										{$MOD.VTLIB_LBL_PROCEED_WITH_UPDATE}
										<input type="submit" class="crmbutton small edit" value="{$MOD.LBL_YES}" 
											{if $need_license_agreement eq 'true'} disabled=true {/if}	name="yesbutton">
										<input type="submit" class="crmbutton small delete" value="{$MOD.LBL_NO}" 
												onclick="this.form.module_update.value=''; this.form.module_import_cancel.value='true';">
									{/if}
								</td>
							</tr>
							</table>
						{/if}
					</form>
                </div>
			</td>
		</tr>
		</table>
		<!-- End of Display -->
		
		</td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
   </div>

        </td>
        <td valign="top"><img src="{$IMAGE_PATH}showPanelTopRight.gif"></td>
	</tr>
</table>
<br>
