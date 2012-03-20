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
	<form action="index.php?module=Settings&action=add2db" method="post" name="index" enctype="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
 	<input type="hidden" name="return_module" value="Settings">
 	<input type="hidden" name="parenttab" value="Settings">
    	<input type="hidden" name="return_action" value="OrganizationConfig">
	<div align=center>
			{include file="SetMenu.tpl"}	
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'company.gif'|@vtiger_imageurl:$THEME}" width="48" height="48" border=0 ></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_EDIT} {$MOD.LBL_COMPANY_DETAILS} </b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_COMPANY_DESC}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$MOD.LBL_COMPANY_DETAILS} </strong>
						{$ERRORFLAG}<br>
						</td>
						<td class="small" align=right>
							<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="return verify_data(form,'{$MOD.LBL_ORGANIZATION_NAME}');" >
							<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmButton small cancel" onclick="window.history.back()" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
						</td>
					</tr>
					</table>
					
					<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
					<tr>
						<td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
                          <tr>
                            <td width="20%" class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_ORGANIZATION_NAME}</strong></td>
                            <td width="80%" class="small cellText">
				<input type="text" name="organization_name" class="detailedViewTextBox small" value="{$ORGANIZATIONNAME}">
				<input type="hidden" name="org_name" value="{$ORGANIZATIONNAME}">
			    </strong></td>
                          </tr>
                          <tr valign="top">
                            <td class="small cellLabel"><strong>{$MOD.LBL_ORGANIZATION_LOGO}</strong></td>
			    {if $ORGANIZATIONLOGONAME neq ''}	
                            <td class="small cellText" style="background-image: url(test/logo/{$ORGANIZATIONLOGONAME}); background-position: left; background-repeat: no-repeat;" width="48" height="48" border="0" >
			    {else}
                            <td class="small cellText" style="background-image: url(include/images/noimage.gif); background-position: left; background-repeat: no-repeat;" width="48" height="48" border="0" >
			     {/if}	
				<br><br><br><br>
                             {$MOD.LBL_SELECT_LOGO} 
				<INPUT TYPE="HIDDEN" NAME="MAX_FILE_SIZE" VALUE="800000">
		                <INPUT TYPE="HIDDEN" NAME="PREV_FILE" VALUE="{$ORGANIZATIONLOGONAME}">	 
                                <input type="file" name="binFile" class="small" value="{$ORGANIZATIONLOGONAME}" onchange="validateFilename(this);">[{$ORGANIZATIONLOGONAME}]
                                <input type="hidden" name="binFile_hidden" value="{$ORGANIZATIONLOGONAME}" />
			      </td>
                          </tr>
                          <tr>
                            <td class="small cellLabel"><strong>{$MOD.LBL_ORGANIZATION_ADDRESS}</strong></td>
                            <td class="small cellText"><input type="text" name="organization_address" class="detailedViewTextBox small" value="{$ORGANIZATIONADDRESS}"></td>
                          </tr>
                          <tr> 
                            <td class="small cellLabel"><strong>{$MOD.LBL_ORGANIZATION_CITY}</strong></td>
                            <td class="small cellText"><input type="text" name="organization_city" class="detailedViewTextBox small" value="{$ORGANIZATIONCITY}"></td>
                          </tr>
                          <tr>
                            <td class="small cellLabel"><strong>{$MOD.LBL_ORGANIZATION_STATE}</strong></td>
                            <td class="small cellText"><input type="text" name="organization_state" class="detailedViewTextBox small" value="{$ORGANIZATIONSTATE}"></td>
                          </tr>
                          <tr>
                            <td class="small cellLabel"><strong>{$MOD.LBL_ORGANIZATION_CODE}</strong></td>
                            <td class="small cellText"><input type="text" name="organization_code" class="detailedViewTextBox small" value="{$ORGANIZATIONCODE}"></td>
                          </tr>
                          <tr>
                            <td class="small cellLabel"><strong>{$MOD.LBL_ORGANIZATION_COUNTRY}</strong></td>
                            <td class="small cellText"><input type="text" name="organization_country" class="detailedViewTextBox small" value="{$ORGANIZATIONCOUNTRY}"></td>

                          </tr>
                          <tr>
                            <td class="small cellLabel"><strong>{$MOD.LBL_ORGANIZATION_PHONE}</strong></td>
                            <td class="small cellText"><input type="text" name="organization_phone" class="detailedViewTextBox small" value="{$ORGANIZATIONPHONE}"></td>
                          </tr>
                          <tr>
                            <td class="small cellLabel"><strong>{$MOD.LBL_ORGANIZATION_FAX}</strong></td>
                            <td class="small cellText"><input type="text" name="organization_fax" class="detailedViewTextBox small" value="{$ORGANIZATIONFAX}"></td>
                          </tr>
                          <tr>
                            <td class="small cellLabel"><strong>{$MOD.LBL_ORGANIZATION_WEBSITE}</strong></td>
                            <td class="small cellText"><input type="text" name="organization_website" class="detailedViewTextBox small" value="{$ORGANIZATIONWEBSITE}"></td>
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
	</table>
	</div>
	</form>		
</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</table>
{literal}
<script>
function verify_data(form,company_name)
{
	if (form.organization_name.value == "" )
	{
		{/literal}
                alert(company_name +"{$APP.CANNOT_BE_NONE}");
                form.organization_name.focus();
                return false;
                {literal}
	}
	else if (form.organization_name.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{
	{/literal}
                alert(company_name +"{$APP.CANNOT_BE_EMPTY}");
                form.organization_name.focus();
                return false;
                {literal}
	}
	else if (! upload_filter("binFile","jpg|jpeg|JPG|JPEG"))
        {
                form.binFile.focus();
                return false;
        }
	else
	{
		return true;
	}
}
</script>
{/literal}
