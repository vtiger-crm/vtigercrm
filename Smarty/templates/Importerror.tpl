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

{include file='Buttons_List1.tpl'}	

<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%" class="small">
   <tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}" /></td>
	<td class="showPanelBg" valign="top" width="100%">

		<table  cellpadding="0" cellspacing="0" width="100%">
		   <tr>
			<td width="75%" valign=top>
				<form enctype="multipart/form-data" name="Import" method="POST" action="index.php">
				<input type="hidden" name="module" value="{$MODULE}">
 				<input type="hidden" name="return_module" value="{$MODULE}">
				<input type="hidden" name="return_action" value="index">
				<input type="hidden" name="parenttab" value="{$CATEGORY}">
				<input type="hidden" name="step" value="1">
				<input type="hidden" name="action" value="Import">

				<!-- IMPORT ERROR STARTS HERE  -->
				<br /><br /><br />

				<table align="center" cellpadding="5" cellspacing="0" width="95%" class="mailClient importLeadUI small">
				   <tr>
					<td colspan="2"  height="50" valign="middle" align="left" class="mailClientBg genHeaderSmall">{$MOD.LBL_MODULE_NAME} {$MODULE}</td>
				   </tr>
				   <tr>
					<td colspan="2" align="left" valign="top">&nbsp;</td>
				   </tr>
				   <tr>
					<td colspan="2" align="left" valign="top" style="padding-left:40px;">
						<span class="genHeaderSmall">{$MOD.LBL_MODULE_NAME} {$MOD.LBL_ERROR}</span> 
					</td>
				   </tr>
	
				   <tr><td align="left" valign="top" colspan="2">&nbsp;</td></tr>
				   <tr>
					<td align="left" valign="top" colspan="2" style="padding-left:80px;"><font color="red" size="2px">{$MESSAGE}</font></td>
				   </tr>

				   <tr><td colspan="2" height="50">&nbsp;</td></tr>
				   <tr>
					<td colspan="2" align="right" style="padding-right:40px;" class="reportCreateBottom" >
						<input title="{$MOD.LBL_TRY_AGAIN}" accessKey="" class="crmbutton small save" type="submit" name="button" value=" {$MOD.LBL_TRY_AGAIN} "  >
					</td>
				   </tr>
				</table>

				<br />
				</form>
			</td>
		   </tr>
		</table>

	</td>
	<td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}" /></td>
   </tr>
</table>
