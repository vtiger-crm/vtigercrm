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
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/ColorPicker2.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/dtlviewajax.js"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<span id="crmspanid" style="display:none;position:absolute;"  onmouseover="show('crmspanid');">
   <a class="link"  align="right" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>

<br>
<!-- Shadow table -->
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tr>
    <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
    <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
    <br>
    <div align=center>
		{if $CATEGORY eq 'Settings'}
			{include file='SetMenu.tpl'}
		{/if}
				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="padTab" align="left">
						<form name="DetailView" method="POST" action="index.php" ENCTYPE="multipart/form-data" id="form" style="margin:0px" onsubmit="VtigerJS_DialogBox.block();">
							<input type="hidden" name="module" value="Users" style="margin:0px">
							<input type="hidden" name="record" id="userid" value="{$ID}" style="margin:0px">
							<input type="hidden" name="isDuplicate" value=false style="margin:0px">
							<input type="hidden" name="action" style="margin:0px">
							<input type="hidden" name="changepassword" style="margin:0px">
							{if $CATEGORY neq 'Settings'}
								<input type="hidden" name="modechk" value="prefview" style="margin:0px">
							{/if}
							<input type="hidden" name="old_password" style="margin:0px">
							<input type="hidden" name="new_password" style="margin:0px">
							<input type="hidden" name="return_module" value="Users" style="margin:0px">
							<input type="hidden" name="return_action" value="ListView"  style="margin:0px">
							<input type="hidden" name="return_id" style="margin:0px">
							<input type="hidden" name="forumDisplay" style="margin:0px">
							<input type="hidden" name="hour_format" id="hour_format" value="{$HOUR_FORMAT}" style="margin:0px">
							<input type="hidden" name="start_hour" id="start_hour" value="{$START_HOUR}" style="margin:0px">
							<input type="hidden" name="form_token" value="{$FORM_TOKEN}">
							{if $CATEGORY eq 'Settings'}
							<input type="hidden" name="parenttab" value="{$PARENTTAB}" style="margin:0px">
							{/if}
							<table width="100%" border="0" cellpadding="0" cellspacing="0" >
							<tr>
								<td colspan=2>
									<!-- Heading and Icons -->
									<table width="100%" cellpadding="5" cellspacing="0" border="0" class="settingsSelUITopLine">
									<tr>
										<td width=50 rowspan="2"><img src="{'ico-users.gif'|@vtiger_imageurl:$THEME}" align="absmiddle"></td>
										<td>
											{if $CATEGORY eq 'Settings'}
											<span class="heading2">
											<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS} </a> &gt; <a href="index.php?module=Administration&action=index&parenttab=Settings"> {$MOD.LBL_USERS} </a>&gt;"{$USERNAME}" </b></span>
											{else}
											<span class="heading2">
											<b>{$APP.LBL_MY_PREFERENCES}</b>
											</span>
											{/if}
											<span id="vtbusy_info" style="display:none;" valign="bottom"><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
										</td>

									</tr>
									<tr>
										<td>{$UMOD.LBL_USERDETAIL_INFO} "{$USERNAME}"</td>
									</tr>
									</table>
								</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td colspan="2" nowrap align="right">
									{if $IS_ADMIN eq 'true'}
									<input type="button" onclick="showAuditTrail();" value="{$MOD.LBL_VIEW_AUDIT_TRAIL}" class="crmButton small save"></input>
									{/if}
									{if $CATEGORY eq 'Settings'}
														{$DUPLICATE_BUTTON}
												{/if}
									{$EDIT_BUTTON}
									{if $CATEGORY eq 'Settings' && $ID neq 1 && $ID neq $CURRENT_USERID}
									<input type="button" onclick="deleteUser({$ID});" class="crmButton small cancel" value="{$UMOD.LBL_DELETE}"></input>
									{/if}
								</td>
							</tr>
							<tr>
								<td colspan="2" align=left>
								<!-- User detail blocks -->
								<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
								<td align="left" valign="top">
									{foreach key=header name=blockforeach item=detail from=$BLOCKS}
									<br>
									<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr>
										{strip}
										 <td class="big">
										<strong>{$smarty.foreach.blockforeach.iteration}. {$header}</strong>
										 </td>
										 <td class="small" align="right">&nbsp;</td>
										{/strip}
									</tr>
									</table>

									<table border="0" cellpadding="5" cellspacing="0" width="100%">
									{foreach item=detail from=$detail}
									<tr >
										{foreach key=label item=data from=$detail}
										   {assign var=keyid value=$data.ui}
										   {assign var=keyval value=$data.value}
										   {assign var=keytblname value=$data.tablename}
										   {assign var=keyfldname value=$data.fldname}
										   {assign var=keyfldid value=$data.fldid}
										   {assign var=keyoptions value=$data.options}
										   {assign var=keysecid value=$data.secid}
										   {assign var=keyseclink value=$data.link}
										   {assign var=keycursymb value=$data.cursymb}
										   {assign var=keysalut value=$data.salut}
										   {assign var=keycntimage value=$data.cntimage}
										   {assign var=keyadmin value=$data.isadmin}

										   {if $label ne ''}
										   <td class="dvtCellLabel" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$label}</td>
											{include file="DetailViewUI.tpl"}
										   {else}
										   <td class="dvtCellLabel" align=right>&nbsp;</td>
										   <td class="dvtCellInfo" align=left >&nbsp;</td>
										   {/if}
										{/foreach}
									</tr>
									{/foreach}
									</table>
									{assign var=list_numbering value=$smarty.foreach.blockforeach.iteration}
									{/foreach}

									<br>
									<!-- Home page components -->
									<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr>
										 <td class="big">
										<strong>{$list_numbering+1}. {$UMOD.LBL_HOME_PAGE_COMP}</strong>
										 </td>
										 <td class="small" align="right"><img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="ShowHidefn('home_comp');"></td>
									</tr>
									</table>

									<div style="float: none; display: none;" id="home_comp">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
									{foreach item=homeitems key=values from=$HOMEORDER}
										<tr><td class="dvtCellLabel" align="right" width="30%">{$UMOD.$values|@getTranslatedString:'Home'}</td>
											{if $homeitems neq ''}
												<td class="dvtCellInfo" align="center" width="5%">
												<img src="{'prvPrfSelectedTick.gif'|@vtiger_imageurl:$THEME}" alt="{$UMOD.LBL_SHOWN}" title="{$UMOD.LBL_SHOWN}" height="12" width="12"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_SHOWN}</td>
												{else}
												<td class="dvtCellInfo" align="center" width="5%">
												<img src="{'no.gif'|@vtiger_imageurl:$THEME}" alt="{$UMOD.LBL_HIDDEN}" title="{$UMOD.LBL_HIDDEN}" height="12" width="12"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDDEN}</td>
											{/if}
										</tr>
									{/foreach}
									</table>
									</div>

									<br>
									<!-- Tag Cloud Display -->
									<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr>
										<td class="big">
										<strong>{$list_numbering+2}. {$UMOD.LBL_TAGCLOUD_DISPLAY}</strong>
										</td>
										<td class="small" align="right"><img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="ShowHidefn('tagcloud_disp');"></td>
									</tr>
									</table>
									<div style="float: none; display: none;" id="tagcloud_disp">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tr><td class="dvtCellLabel" align="right" width="30%">{$UMOD.LBL_TAG_CLOUD}</td>
											{if $TAGCLOUDVIEW eq 'true'}
												<td class="dvtCellInfo" align="center" width="5%">
												<img src="{'prvPrfSelectedTick.gif'|@vtiger_imageurl:$THEME}" alt="{$UMOD.LBL_SHOWN}" title="{$UMOD.LBL_SHOWN}" height="12" width="12"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_SHOWN}</td>
                                                                                                {else}
												<td class="dvtCellInfo" align="center" width="5%">
												<img src="{'no.gif'|@vtiger_imageurl:$THEME}" alt="{$UMOD.LBL_HIDDEN}" title="{$UMOD.LBL_HIDDEN}" height="12" width="12"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDDEN}</td>
											{/if}
										</tr>
                                                                        </table>                                                                                                                   </div>
                                                                                                                                                                                                   <br>
									<!-- My Groups -->
									<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr>
										<td class="big">
										<strong>{$list_numbering+3}. {$UMOD.LBL_MY_GROUPS}</strong>
										 </td>
										 <td class="small" align="right">
										{if $GROUP_COUNT > 0}
										<img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="fetchGroups_js({$ID});">
										{else}
											&nbsp;
										{/if}
										</td>
									</tr>
									</table>

									<table border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr><td align="left"><div id="user_group_cont" style="display:none;"></div></td></tr>
									</table>
									<br>
									<!-- Login History -->
									{if $IS_ADMIN eq 'true'}
									<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
										<tr>
										 <td class="big">
										<strong>{$list_numbering+4}. {$UMOD.LBL_LOGIN_HISTORY}</strong>
										 </td>
										 <td class="small" align="right"><img src="{'showDown.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="fetchlogin_js({$ID});"></td>
										</tr>
									</table>

									<table border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr><td align="left"><div id="login_history_cont" style="display:none;"></div></td></tr>
									</table>
									<br>
									{/if}
								</td>
								</tr>
								</table>
								<!-- User detail blocks ends -->

								</td>
							</tr>
							<tr>
								<td colspan=2 class="small"><div align="right"><a href="#top">{$MOD.LBL_SCROLL}</a></div></td>
							</tr>
							</table>

						</form>

					</td>
				</tr>
				</table>


	</div>
	</td>

</tr>
</table>

			</td>
			</tr>
			</table>

			</td>
			<td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
			</tr>
			</table>




<br>
{$JAVASCRIPT}
<div id="tempdiv" style="display:block;position:absolute;left:350px;top:200px;"></div>
<!-- added for validation -->
<script language="javascript">
  var gVTModule = '{$smarty.request.module|@vtlib_purify}';
  var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
  var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
  var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
function ShowHidefn(divid)
{ldelim}
	if($(divid).style.display != 'none')
		Effect.Fade(divid);
	else
		Effect.Appear(divid);
{rdelim}
{literal}
function fetchlogin_js(id)
{
	if($('login_history_cont').style.display != 'none')
		Effect.Fade('login_history_cont');
	else
		fetchLoginHistory(id);

}
function fetchLoginHistory(id)
{
        $("status").style.display="inline";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Users&action=UsersAjax&file=ShowHistory&ajax=true&record='+id,
                        onComplete: function(response) {
                                $("status").style.display="none";
                                $("login_history_cont").innerHTML= response.responseText;
				Effect.Appear('login_history_cont');
                        }
                }
        );

}
function fetchGroups_js(id)
{
	if($('user_group_cont').style.display != 'none')
		Effect.Fade('user_group_cont');
	else
		fetchUserGroups(id);
}
function fetchUserGroups(id)
{
        $("status").style.display="inline";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Users&action=UsersAjax&file=UserGroups&ajax=true&record='+id,
                        onComplete: function(response) {
                                $("status").style.display="none";
                                $("user_group_cont").innerHTML= response.responseText;
				Effect.Appear('user_group_cont');
                        }
                }
        );

}

function showAuditTrail()
{
	var userid =  document.getElementById('userid').value;
	window.open("index.php?module=Settings&action=SettingsAjax&file=ShowAuditTrail&userid="+userid,"","width=650,height=800,resizable=0,scrollbars=1,left=100");
}

function deleteUser(userid)
{
        $("status").style.display="inline";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'action=UsersAjax&file=UserDeleteStep1&return_action=ListView&return_module=Users&module=Users&parenttab=Settings&record='+userid,
                        onComplete: function(response) {
                                $("status").style.display="none";
                                $("tempdiv").innerHTML= response.responseText;
                        }
                }
        );
}
function transferUser(del_userid)
{
        $("status").style.display="inline";
        $("DeleteLay").style.display="none";
        var trans_userid=$('transfer_user_id').options[$('transfer_user_id').options.selectedIndex].value;
	window.document.location.href = 'index.php?module=Users&action=DeleteUser&ajax_delete=false&delete_user_id='+del_userid+'&transfer_user_id='+trans_userid;
}
{/literal}
</script>
<script>
function getListViewEntries_js(module,url)
{ldelim}
	$("status").style.display="inline";
        new Ajax.Request(
        	'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                	method: 'post',
                        postBody:"module="+module+"&action="+module+"Ajax&file=ShowHistory&record={$ID}&ajax=true&"+url,
			onComplete: function(response) {ldelim}
                        	$("status").style.display="none";
                                $("login_history_cont").innerHTML= response.responseText;
                  	{rdelim}
                {rdelim}
        );
{rdelim}
</script>

