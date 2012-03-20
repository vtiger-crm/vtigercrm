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
				<input type="hidden" name="mode" value="create">
				<input type="hidden" name="parenttab" value="Settings">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'ico-groups.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_GROUPS}" width="48" height="48" border=0 title="{$MOD.LBL_GROUPS}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$CMOD.LBL_GROUPS}</b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_GROUP_DESC}</td>
				</tr>
				</table>
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>

				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$MOD.LBL_GROUP_LIST}</strong></td>
						<td class="small" align=right>{$CMOD.LBL_TOTAL} {$GRPCNT} {$CMOD.LBL_GROUPS} </td>
					</tr>
					</table>
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTableTopButtons">

					<tr>
					     <td class=small align=right>
						<input title="{$CMOD.LBL_NEW_GROUP}" class="crmButton create small" type="submit" name="New" value="{$CMOD.LBL_NEW_GROUP}"/>
					     </td>
					</tr>
					</table>
						
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTable">
					<tr>
						<td class="colHeader small" valign=top width=2%>#</td>
						<td class="colHeader small" valign=top width=8%>{$LIST_HEADER.0}</td>
						<td class="colHeader small" valign=top width=30%>{$LIST_HEADER.1}</td>
						<td class="colHeader small" valign=top width=60%>{$LIST_HEADER.2}</td>
					  </tr>
						{foreach name=grouplist item=groupvalues from=$LIST_ENTRIES}
					  <tr>
						<td class="listTableRow small" valign=top>{$smarty.foreach.grouplist.iteration}</td>
						<td class="listTableRow small" valign=top nowrap>
							  	<a href="index.php?module=Settings&action=createnewgroup&returnaction=listgroups&parenttab=Settings&mode=edit&groupId={$groupvalues.groupid}"><img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LNK_EDIT}" title="{$APP.LNK_EDIT}" border="0" align="absmiddle"></a>&nbsp;|	
								<a href="#" onClick="deletegroup(this,'{$groupvalues.groupid}')";><img src="{'delete.gif'|@vtiger_imageurl:$THEME}" alt="{$LNK_DELETE}" title="{$APP.LNK_DELETE}" border="0" align="absmiddle"></a>
						</td>
						<td class="listTableRow small" valign=top><strong>
                              				<a href="index.php?module=Settings&action=GroupDetailView&parenttab=Settings&groupId={$groupvalues.groupid}">{$groupvalues.groupname}</a></strong>
						</td>
						<td class="listTableRow small" valign=top>{$groupvalues.description}</td>
					  </tr>
					{/foreach}
					</table>
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

<div id="tempdiv" style="display:block;position:absolute;left:350px;top:200px;"></div>
<script>
function deletegroup(obj,groupid)
{ldelim}
	$("status").style.display="inline";
        new Ajax.Request(
                'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                        method: 'post',
                        postBody:'module=Users&action=UsersAjax&file=GroupDeleteStep1&groupid='+groupid,
                        onComplete: function(response) {ldelim}
                                $("status").style.display="none";
                                $("tempdiv").innerHTML=response.responseText;
								fnvshobj(obj,"tempdiv");
                        {rdelim}
                {rdelim}
        );
{rdelim}

</script>
