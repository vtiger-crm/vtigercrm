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
{php}
	//add the settings page values
	$this->assign("BLOCKS",getSettingsBlocks());
	$this->assign("FIELDS",getSettingsFields());
{/php}

<table border=0 cellspacing=0 cellpadding=20 width="99%" class="settingsUI">
	<tr>
		<td valign=top>
			<table border=0 cellspacing=0 cellpadding=0 width=100%>
				<tr>
					<td valign=top id="settingsSideMenu" width="10%" >
						<!--Left Side Navigation Table-->
						<table border=0 cellspacing=0 cellpadding=0 width="100%">
{foreach key=BLOCKID item=BLOCKLABEL from=$BLOCKS}
	{if $BLOCKLABEL neq 'LBL_MODULE_MANAGER'}
	{assign var=blocklabel value=$BLOCKLABEL|@getTranslatedString:'Settings'}
										<tr>
								<td class="settingsTabHeader" nowrap>
									{$blocklabel}
								</td>
							</tr>
		{foreach item=data from=$FIELDS.$BLOCKID}
			{if $data.link neq ''}
				{assign var=label value=$data.name|@getTranslatedString:'Settings'}
				{if ($smarty.request.action eq $data.action && $smarty.request.module eq $data.module)}
							<tr>
								<td class="settingsTabSelected" nowrap>
									<a href="{$data.link}">
										{$label}
									</a>
								</td>
							</tr>
				{else}
							<tr>
								<td class="settingsTabList" nowrap>
									<a href="{$data.link}">
										{$label}
									</a>
								</td>
							</tr>
				{/if}
			{/if}
		{/foreach}
	{/if}
{/foreach}
						</table>
						<!-- Left side navigation table ends -->
		
					</td>
					<td width="8px" valign="top"> 
						<img src="{'panel-left.png'|@vtiger_imageurl:$THEME}" title="Hide Menu" id="hideImage" style="display:inline;cursor:pointer;" onclick="toggleShowHide_panel('showImage','settingsSideMenu'); toggleShowHide_panel('showImage','hideImage');" />
						<img src="{'panel-right.png'|@vtiger_imageurl:$THEME}" title="Show Menu" id="showImage" style="display:none;cursor:pointer;" onclick="toggleShowHide_panel('settingsSideMenu','showImage'); toggleShowHide_panel('hideImage','showImage');"/>
					</td>
					<td class="small settingsSelectedUI" valign=top align=left>
						<script type="text/javascript">
{literal}
							function toggleShowHide_panel(showid, hideid){
								var show_ele = document.getElementById(showid);
								var hide_ele = document.getElementById(hideid);
								if(show_ele != null){ 
									show_ele.style.display = "";
									}
								if(hide_ele != null) 
									hide_ele.style.display = "none";
							}
{/literal}
						</script>
