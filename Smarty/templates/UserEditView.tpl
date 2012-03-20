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
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>

<script language="JavaScript" type="text/javascript">

 	var cp2 = new ColorPicker('window');
	
function pickColor(color)
{ldelim}
	ColorPicker_targetInput.value = color;
        ColorPicker_targetInput.style.backgroundColor = color;
{rdelim}	

function openPopup(){ldelim}
		window.open("index.php?module=Users&action=UsersAjax&file=RolePopup&parenttab=Settings","roles_popup_window","height=425,width=640,toolbar=no,menubar=no,dependent=yes,resizable =no");
	{rdelim}	
</script>	

<script language="javascript">
function check_duplicate()
{ldelim}
	var user_name = window.document.EditView.user_name.value;
	var status = CharValidation(user_name,'name');
	
	VtigerJS_DialogBox.block();
	
        if(status)
	{ldelim}
	new Ajax.Request(
                'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                        method: 'post',
                        postBody: 'module=Users&action=UsersAjax&file=Save&ajax=true&dup_check=true&userName='+user_name,
                        onComplete: function(response) {ldelim}
							if(response.responseText.indexOf('SUCCESS') > -1)
							{ldelim}
							//	$('user_status').disabled = false;
						                document.EditView.submit();
							{rdelim}
			       				else {ldelim}
			       						VtigerJS_DialogBox.unblock();
						                alert(response.responseText);
						        {rdelim}
			            {rdelim}
                {rdelim}
        );
	{rdelim}
	else
            alert(alert_arr.NO_SPECIAL+alert_arr.IN_USERNAME)
{rdelim}

</script>

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>

	<div align=center>
	{if $PARENTTAB eq 'Settings'}
		{include file='SetMenu.tpl'}
	{/if}

		<form name="EditView" method="POST" action="index.php" ENCTYPE="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
		<input type="hidden" name="module" value="Users">
		<input type="hidden" name="record" value="{$ID}">
		<input type="hidden" name="mode" value="{$MODE}">
		<input type='hidden' name='parenttab' value='{$PARENTTAB}'>
		<input type="hidden" name="activity_mode" value="{$ACTIVITYMODE}">
		<input type="hidden" name="action">
		<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
		<input type="hidden" name="return_id" value="{$RETURN_ID}">
		<input type="hidden" name="return_action" value="{$RETURN_ACTION}">			
		<input type="hidden" name="tz" value="Europe/Berlin">			
		<input type="hidden" name="holidays" value="de,en_uk,fr,it,us,">			
		<input type="hidden" name="workdays" value="0,1,2,3,4,5,6,">			
		<input type="hidden" name="namedays" value="">			
		<input type="hidden" name="weekstart" value="1">
		<input type="hidden" name="hour_format" value="{$HOUR_FORMAT}">
		<input type="hidden" name="start_hour" value="{$START_HOUR}">
		<input type="hidden" name="form_token" value="{$FORM_TOKEN}">

	<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="settingsSelUITopLine">
	<tr><td align="left">
		<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td rowspan="2" width="50"><img src="{'ico-users.gif'|@vtiger_imageurl:$THEME}" align="absmiddle"></td>
			<td>	
				<span class="lvtHeaderText">
				{if $PARENTTAB neq ''}	
				<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS} </a> &gt; <a href="index.php?module=Administration&action=index&parenttab=Settings">{$MOD.LBL_USERS}</a> &gt; 
					{if $MODE eq 'edit'}
						{$UMOD.LBL_EDITING} "{$USERNAME}" 
					{else}
						{if $DUPLICATE neq 'true'}
						{$UMOD.LBL_CREATE_NEW_USER}
						{else}
						{$APP.LBL_DUPLICATING} "{$USERNAME}"
						{/if}
					{/if}
					</b></span>
				{else}
                                <span class="lvtHeaderText">
                                <b>{$APP.LBL_MY_PREFERENCES}</b>
                                </span>
                                {/if}
			</td>
			<td rowspan="2" nowrap>&nbsp;
			</td>
	 	</tr>
		<tr>
			{if $MODE eq 'edit'}
				<td><b class="small">{$UMOD.LBL_EDIT_VIEW} "{$USERNAME}"</b>
			{else}
				{if $DUPLICATE neq 'true'}
				<td><b class="small">{$UMOD.LBL_CREATE_NEW_USER}</b>
				{/if}
			{/if}
			</td>
                </tr>
		</table>
	</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td nowrap align="right">
				<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small crmbutton save"  name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  "  onclick="this.form.action.value='Save'; return verify_data(EditView)" style="width: 70px;" type="button" />
				<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small crmbutton cancel" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " onclick="window.history.back()" style="width: 70px;" type="button" />
						
		</td>
	</tr>
	<tr><td class="padTab" align="left">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">

		<tr><td colspan="2">
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="99%">
			<tr>
			    <td align="left" valign="top">
			             <table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr><td align="left">
						{foreach key=header name=blockforeach item=data from=$BLOCKS}
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
						<!-- Handle the ui types display -->
							{include file="DisplayFields.tpl"}
						</table>
						{assign var=list_numbering value=$smarty.foreach.blockforeach.iteration}
					   {/foreach}
				<br>
			    	<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
			    	<tr>
				     <td class="big">	
					<strong>{$list_numbering+1}. {$UMOD.LBL_HOME_PAGE_COMP}</strong>
				     </td>
				     <td class="small" align="right">&nbsp;</td>	
			        </tr>
			    	</table>
			    	<table border="0" cellpadding="5" cellspacing="0" width="100%">
				{foreach item=homeitems key=values from=$HOMEORDER}
					<tr><td class="dvtCellLabel" align="right" width="30%">{$UMOD.$values|@getTranslatedString:'Home'}</td>
					    {if $homeitems neq ''}
					    	<td class="dvtCellInfo" align="center" width="5%">
					   	<input name="{$values}" value="{$values}" checked type="radio"></td><td class="dvtCellInfo" align="left" width="20%">{$UMOD.LBL_SHOW}</td> 		
					    	<td class="dvtCellInfo" align="center" width="5%">
					   	<input name="{$values}" value="" type="radio"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDE}</td> 		
					    {else}	
					    	<td class="dvtCellInfo" align="center" width="5%">
					   	<input name="{$values}" value="{$values}" type="radio"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_SHOW}</td> 		
					    	<td class="dvtCellInfo" align="center" width="5%">
					   	<input name="{$values}" value="" checked type="radio"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDE}</td> 		
					    {/if}	
					</tr>			
				{/foreach}
			    	</table>
				<!-- Added for User Based TagCloud -->
                                <table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
                                <tr>
                                     <td class="big">
                                        <strong>{$list_numbering+2}. {$UMOD.LBL_TAGCLOUD_DISPLAY}</strong>
                                     </td>
                                     <td class="small" align="right">&nbsp;</td>
                                </tr>
                                </table>
				<!-- End of Header -->
				<table border="0" cellpadding="5" cellspacing="0" width="100%">
                                        <tr><td class="dvtCellLabel" align="right" width="30%">{$UMOD.LBL_TAG_CLOUD}</td>
                                            {if $TAGCLOUDVIEW eq 'true'}
                                                <td class="dvtCellInfo" align="center" width="5%">
                                                <input name="tagcloudview" value="true" checked type="radio"></td><td class="dvtCellInfo" align="left" >{$UMOD.LBL_SHOW}</td>
                                                <td class="dvtCellInfo" align="center" width="5%">
                                                <input name="tagcloudview" value="false" type="radio"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDE}</td>
					    {else}
						<td class="dvtCellInfo" align="center" width="5%">
                                                <input name="tagcloudview" value="true" type="radio"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_SHOW}</td>
						<td class="dvtCellInfo" align="center" width="5%">
						<input name="tagcloudview" value="false" checked type="radio"></td><td class="dvtCellInfo" align="left">{$UMOD.LBL_HIDE}</td>
                                            {/if}
					</tr>
				</table>
				<!--end of Added for User Based TagCloud -->
				<br>
				<tr><td colspan=4>&nbsp;</td></tr>
							
					        <tr>
					       		<td colspan=4 align="right">
							<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small crmbutton save"  name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  "  onclick="this.form.action.value='Save'; return verify_data(EditView)" style="width: 70px;" type="button" />
							<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small crmbutton cancel" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " onclick="window.history.back()" style="width: 70px;" type="button" />
							</td>
						</tr>
					    </table>
					 </td></tr>
					</table>
			  	   </td></tr>
				   </table>
				 <br>
				  </td></tr>
				<tr><td class="small"><div align="right"><a href="#top">{$MOD.LBL_SCROLL}</a></div></td></tr>
				</table>
			</td>
			</tr>
			</table>
			</form>	
</td>
</tr>
</table>
</td></tr></table>
<br>
{$JAVASCRIPT}
