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
				<form action="index.php" method="post" name="new" id="form" onsubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="module" value="Settings">
				<input type="hidden" name="action" value="createnewgroup">
				<input type="hidden" name="groupId" value="{$GROUPID}">
				<input type="hidden" name="mode" value="edit">
				<input type="hidden" name="parenttab" value="Settings">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'ico-groups.gif'|@vtiger_imageurl:$THEME}" width="48" height="48" border=0 ></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=listgroups&parenttab=Settings">{$CMOD.LBL_GROUPS}</a> &gt; {$CMOD.LBL_VIEWING} &quot;{$GROUPINFO.0.groupname}&quot; </b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$CMOD.LBL_VIEWING} {$CMOD.LBL_PROPERTIES} &quot;{$GROUPINFO.0.groupname}`&quot; {$CMOD.LBL_GROUP_NAME} </td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td valign=top>
					
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$CMOD.LBL_PROPERTIES} &quot;{$GROUPINFO.0.groupname}&quot; </strong></td>
						<td><div align="right">
					 	    <input value="   {$APP.LBL_EDIT_BUTTON_LABEL}   " title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmButton small edit" type="submit" name="Edit" >
						</div></td>
					  </tr>
					</table>
					<table width="100%"  border="0" cellspacing="0" cellpadding="5">
                      <tr class="small">
                        <td width="15%" class="small cellLabel"><strong>{$CMOD.LBL_GROUP_NAME}</strong></td>
                        <td width="85%" class="cellText" >{$GROUPINFO.0.groupname}</td>
                      </tr>
                      <tr class="small">
                        <td class="small cellLabel"><strong>{$CMOD.LBL_DESCRIPTION}</strong></td>
                        <td class="cellText">{$GROUPINFO.0.description}</td>
                      </tr>
                      <tr class="small">
                        <td valign=top class="cellLabel"><strong>{$CMOD.LBL_MEMBER}</strong></td>
                        <td class="cellText">
						<table width="70%"  border="0" cellspacing="0" cellpadding="5">
                          <tr class="small">
                  		{foreach key=type item=details from=$GROUPINFO.1} 
				{if $details.0 neq ''}		
					{if $type == "User"}
                            		<td colspan="2" class="cellBottomDotLine">
						<div align="left"><strong>{$MOD.LBL_USERS}</strong></div>
					</td>
					{/if}	
					{if $type == "Role"}
                            		<td colspan="2" class="cellBottomDotLine">
						<div align="left"><strong>{$MOD.LBL_ROLES}</strong></div>
					</td>
					{/if}	
					{if $type == "Role and Subordinates"}
                            		<td colspan="2" class="cellBottomDotLine">
						<div align="left"><strong>{$type}</strong></div>
					</td>
					{/if}	
					{if $type == "Group"}
                            		<td colspan="2" class="cellBottomDotLine">
						<div align="left"><strong>{$CMOD.LBL_GROUPS}</strong></div>
					</td>
					{/if}	
                            </tr>
                          <tr class="small">

                            <td width="16"><div align="center"></div></td>
                            <td>
					{foreach item=element from=$details}
						{if $element.memberaction == "GroupDetailView"}
						<a href="index.php?module=Settings&action={$element.memberaction}&{$element.actionparameter}={$element.memberid}">{$element.membername}</a><br />
						{/if}
						{if $element.memberaction == "RoleDetailView"}	
						<a href="index.php?module=Settings&action={$element.memberaction}&{$element.actionparameter}={$element.memberid}">{$element.membername}</a><br />
						{/if}
						{if $element.memberaction == "DetailView"}	
						<a href="index.php?module=Users&action={$element.memberaction}&{$element.actionparameter}={$element.memberid}">{$element.membername}</a><br />
						{/if}
					{/foreach}
			    </td>  	 
                          </tr>
				{/if}
				{/foreach}
                        </table></td>
                      </tr>
                    </table>
					<br>
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr><td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
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
