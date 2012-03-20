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
		    		<form method="post" action="index.php" name="etemplatedetailview" onsubmit="VtigerJS_DialogBox.block();">  
				<input type="hidden" name="action" value="editemailtemplate">
				<input type="hidden" name="module" value="Settings">
				<input type="hidden" name="templatename" value="{$TEMPLATENAME}">
				<input type="hidden" name="templateid" value="{$TEMPLATEID}">
				<input type="hidden" name="foldername" value="{$FOLDERNAME}">
				<input type="hidden" name="parenttab" value="{$PARENTTAB}">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'ViewTemplate.gif'|@vtiger_imageurl:$THEME}" width="45" height="60" border=0 ></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=listemailtemplates&parenttab=Settings">{$UMOD.LBL_EMAIL_TEMPLATES}</a> &gt; {$MOD.LBL_VIEWING} &quot;{$TEMPLATENAME}&quot; </b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$UMOD.LBL_EMAIL_TEMPLATE_DESC}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$UMOD.LBL_PROPERTIES} &quot;{$TEMPLATENAME}&quot; </strong></td>
						<td class="small" align=right>						  &nbsp;&nbsp;
						  <input class="crmButton edit small" type="submit" name="Button" value="{$APP.LBL_EDIT_BUTTON_LABEL}" onclick="this.form.action.value='editemailtemplate'; this.form.parenttab.value='Settings'">&nbsp;&nbsp;
						</td>
					</tr>
					</table>
					
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr>
						<td width=20% class="small cellLabel"><strong>{$UMOD.LBL_NAME}</strong></td>
						<td width=80% class="small cellText"><strong>{$TEMPLATENAME}</strong></td>
					  </tr>
					<tr>
						<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_DESCRIPTION}</strong></td>
						<td class="cellText small" valign=top>&nbsp;{$DESCRIPTION}</td>
					  </tr>
					<tr>
						<td valign=top class="small cellLabel"><strong>{$UMOD.LBL_FOLDER}</strong></td>
						<td class="cellText small" valign=top>{$FOLDERNAME}</td>
					  </tr>
					
					
					<tr>
					  <td colspan="2" valign=top class="cellText small"><table width="100%"  border="0" cellspacing="0" cellpadding="0" class="thickBorder">
                        <tr>
                          <td valign=top><table width="100%"  border="0" cellspacing="0" cellpadding="5" >
                              <tr>
                                <td colspan="2" valign="top" class="small" style="background-color:#cccccc"><strong>{$UMOD.LBL_EMAIL_TEMPLATE}</strong></td>
                                </tr>
                              <tr>
                                <td width="15%" valign="top" class="cellLabel small">{$UMOD.LBL_SUBJECT}</td>
                                <td width="85%" class="cellText small">{$SUBJECT}</td>
                              </tr>
                              <tr>
                                <td valign="top" class="cellLabel small">{$UMOD.LBL_MESSAGE}</td>
                                <td class="cellText small">{$BODY}</td>
                              </tr>
                          </table></td>
                          
                        </tr>
                      </table></td>
					  </tr>
					</table>
					<br>
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
