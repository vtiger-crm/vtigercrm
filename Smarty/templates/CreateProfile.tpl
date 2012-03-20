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
				{literal}
				<form action="index.php" method="post" name="profileform" id="form" onSubmit="if(rolevalidate()) { VtigerJS_DialogBox.block();return true;}else{return false;}">
				{/literal}
                                <input type="hidden" name="module" value="Settings">
                                <input type="hidden" name="mode" value="{$MODE}">
                                <input type="hidden" name="action" value="profilePrivileges">
                                <input type="hidden" name="parenttab" value="Settings">
                                <input type="hidden" name="parent_profile" value="{$PARENT_PROFILE}">
                                <input type="hidden" name="radio_button" value="{$RADIO_BUTTON}">
	
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'ico-profile.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_PROFILES}" width="48" height="48" border=0 title="{$MOD.LBL_PROFILES}"></td>
					<td class=heading2 valign=bottom><b> <a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=ListProfiles&parenttab=Settings">{$CMOD.LBL_PROFILE_PRIVILEGES}</a></b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_PROFILE_DESCRIPTION}</td>
				</tr>
				</table>
				
				
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
					<td valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
		                      	<tbody><tr>
                			     <td>
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
			                        <tbody><tr class="small">
		                              <td><img src="{'prvPrfTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
               			               <td class="prvPrfTopBg" width="100%"></td>
		                              <td><img src="{'prvPrfTopRight.gif'|@vtiger_imageurl:$THEME}"></td>

                	            </tr>
                          </tbody></table>
                            <table class="prvPrfOutline" border="0" cellpadding="0" cellspacing="0" width="100%">
                              <tbody>
				<tr>
                                              <td><!-- Module name heading -->
                                                  <table class="small" border="0" cellpadding="2" cellspacing="0">
                                                    <tbody><tr>
                                                      <td valign="top"><img src="{'prvPrfHdrArrow.gif'|@vtiger_imageurl:$THEME}"> </td>
                                                      <td class="prvPrfBigText"><b> {$CMOD.LBL_STEP_1_2} : {$CMOD.LBL_WELCOME_PROFILE_CREATE} </b><br>
                                                          <font class="small"> {$CMOD.LBL_SELECT_CHOICE_NEW_PROFILE} </font> </td>

                                                      <td class="small" style="padding-left: 10px;" align="right"></td>
                                                    </tr>
                                                </tbody></table></td>
                                              <td align="right" valign="bottom">&nbsp;											  </td>
                                            </tr>
				<tr>
                                <td><!-- tabs -->
					<table width="95%" border="0" cellpadding="5" cellspacing="0" align="center">
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td align="right" width="25%" style="padding-right:10px;">
						<b style="color:#FF0000;font-size:16px;">{$APP.LBL_REQUIRED_SYMBOL}</b>&nbsp;<b>{$CMOD.LBL_NEW_PROFILE_NAME} : </b></td>
						<td width="75%" align="left" style="padding-left:10px;">
						<input type="text" name="profile_name" id="pobox" value="{$PROFILENAME}" class="txtBox" /></td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td align="right" style="padding-right:10px;" valign="top"><b>{$CMOD.LBL_DESCRIPTION} : </b></td>
						<td align="left" style="padding-left:10px;"><textarea name="profile_description" class="txtBox">{$PROFILEDESC}</textarea></td>
					</tr>
					<tr><td colspan="2" style="border-bottom:1px dashed #CCCCCC;" height="75">&nbsp;</td></tr>
					<tr>
						<td align="right" width="10%" style="padding-right:10px;">
						{if  $RADIO_BUTTON neq 'newprofile'}
						<input name="radiobutton" checked type="radio" value="baseprofile" />
						{else}
						<input name="radiobutton" type="radio"  value="baseprofile" />
						{/if}
						</td>
						<td width="90%" align="left" style="padding-left:10px;">{$CMOD.LBL_BASE_PROFILE_MESG}</td>
					</tr>
					<tr>
						<td align="right"  style="padding-right:10px;">&nbsp;</td>
						<td align="left" style="padding-left:10px;">{$CMOD.LBL_BASE_PROFILE}
						<select name="parentprofile" class="importBox">
							{foreach item=combo from=$PROFILE_LISTS}
							{if $PARENT_PROFILE eq $combo.1}
								<option  selected value="{$combo.1}">{$combo.0}</option>
							{else}
								<option value="{$combo.1}">{$combo.0}</option>	
							{/if}
							{/foreach}
						</select>
						</td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td align="center" colspan="2"><b>(&nbsp;{$CMOD.LBL_OR}&nbsp;)</b></td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td align="right" style="padding-right:10px;">
						{if  $RADIO_BUTTON eq 'newprofile'}
						<input name="radiobutton" checked type="radio" value="newprofile" />
						{else}
						<input name="radiobutton" type="radio" value="newprofile" />
						{/if}
						</td>
						<td  align="left" style="padding-left:10px;">{$CMOD.LBL_BASE_PROFILE_MESG_ADV}</td>
					</tr>
					<tr><td colspan="2" style="border-bottom:1px dashed #CCCCCC;" height="75">&nbsp;</td></tr>
					<tr>
						<td colspan="2" align="right">
						<input type="button" value=" {$APP.LNK_LIST_NEXT} &rsaquo; " title="{$APP.LNK_LIST_NEXT}" name="Next" class="crmButton small create" onClick="return rolevalidate();"/>&nbsp;&nbsp;
						<input type="button" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " title="{$APP.LBL_CANCEL_BUTTON_TITLE}" name="Cancel" onClick="window.history.back();" class="crmButton small cancel"/>
						</td>
					</tr>
					</table>

                                </td></tr>  	  
                            	<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
                              	<tbody><tr>
                                <td><img src="{'prvPrfBottomLeft.gif'|@vtiger_imageurl:$THEME}"></td>
                                <td class="prvPrfBottomBg" width="100%"></td>
                                <td><img src="{'prvPrfBottomRight.gif'|@vtiger_imageurl:$THEME}"></td>
                              </tr>
                          </tbody></table></td>
                      </tr>
                    </tbody></table>

					<p>&nbsp;</p>
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
					<tbody><tr><td class="small" align="right" nowrap="nowrap"><a href="#top">{$APP.LBL_SCROLL}</a></td></tr>
					</tbody></table>
				
				
					
					
					
					
					
				</td>
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
				</form>
   </tr>
</tbody>
</table>
<script>
var profile_err_msg='{$MOD.LBL_ENTER_PROFILE}';
function rolevalidate()
{ldelim}
    var profilename = document.getElementById('pobox').value;
    profilename = trim(profilename);
    if(profilename != '')
	dup_validation(profilename);
    else
    {ldelim}
        alert(profile_err_msg);
        document.getElementById('pobox').focus();
	return false
    {rdelim}
    return false
{rdelim}


function dup_validation(profilename)
{ldelim}
	//var status = CharValidation(profilename,'namespace');
	//if(status)
	//{ldelim}
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=Users&action=UsersAjax&file=CreateProfile&ajax=true&dup_check=true&profile_name='+profilename,
			onComplete: function(response) {ldelim}
					if(response.responseText.indexOf('SUCCESS') > -1)
						document.profileform.submit();
					else
						alert(response.responseText);
				{rdelim}
		{rdelim}
	);
	//{rdelim}
	//else
	//	alert(alert_arr.NO_SPECIAL+alert_arr.IN_PROFILENAME)
{rdelim}
</script>
