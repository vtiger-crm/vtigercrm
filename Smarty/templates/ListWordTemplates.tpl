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
<script>
function massDelete()
{ldelim}
	if(typeof(document.massdelete.selected_id) == 'undefined')
		return false;
        x = document.massdelete.selected_id.length;
        idstring = "";

        if ( x == undefined)
        {ldelim}

                if (document.massdelete.selected_id.checked)
               {ldelim}
                        document.massdelete.idlist.value=document.massdelete.selected_id.value+';';
			xx=1;
                {rdelim}
                else
                {ldelim}
                        alert("{$APP.SELECT_ATLEAST_ONE}");
                        return false;
                {rdelim}
        {rdelim}
        else
        {ldelim}
                xx = 0;
                for(i = 0; i < x ; i++)
                {ldelim}
                        if(document.massdelete.selected_id[i].checked)
                        {ldelim}
                                idstring = document.massdelete.selected_id[i].value +";"+idstring
                        xx++
                        {rdelim}
                {rdelim}
                if (xx != 0)
                {ldelim}
                        document.massdelete.idlist.value=idstring;
                {rdelim}
               else
                {ldelim}
                        alert("{$APP.SELECT_ATLEAST_ONE}");
                        return false;
                {rdelim}
       {rdelim}
		if(confirm("{$APP.DELETE_CONFIRMATION}"+xx+"{$APP.RECORDS}"))
		{ldelim}
	        	document.massdelete.action.value= "deletewordtemplate";
		{rdelim}
		else
		{ldelim}
			return false;
		{rdelim}

{rdelim}
</script>
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
	               		<form  name="massdelete" method="POST" onsubmit="VtigerJS_DialogBox.block();">
	    			<input name="idlist" type="hidden">
    				<input name="module" type="hidden" value="Settings">
    				<input name="parenttab" type="hidden" value="Settings">
    				<input name="action" type="hidden">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'mailmarge.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MODULE_NAME}" width="48" height="48" border=0 title="{$MOD.LBL_MODULE_NAME}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$UMOD.LBL_WORD_TEMPLATES} </b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_MAIL_MERGE_DESC}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$UMOD.LBL_WORD_TEMPLATES}</strong></td>
						<td class="small" align=right>&nbsp;
						</td>
					</tr>
					</table>
					
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTableTopButtons">
					<tr>
						<td class=small><input type="submit" value="{$UMOD.LBL_DELETE}" onclick="return massDelete();" class="crmButton delete small"></td>
						<td class=small align=right><input class="crmButton create small" type="submit" value="{$UMOD.LBL_ADD_TEMPLATE}" name="profile"  onclick="this.form.action.value='upload'; this.form.parenttab.value='Settings'">&nbsp;&nbsp;</td>
					</tr>
					</table>
						
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTable">
					<tr>
						<td width=2% class="colHeader small">#</td>
						<td width=3% class="colHeader small">{$UMOD.LBL_LIST_SELECT}</td>
						<td width=20% class="colHeader small">{$UMOD.LBL_TEMPLATE_FILE}</td>
						<td width=50% class="colHeader small">{$UMOD.LBL_DESCRIPTION}</td>
				        <td width=15% class="colHeader small">{$UMOD.LBL_MODULENAMES}</td>
				        <td width=15% class="colHeader small">{$UMOD.LBL_LIST_TOOLS}</td>
			          </tr>
					{foreach item=template name=mailmerge from=$WORDTEMPLATES}
					<tr>
						<td class="listTableRow small" valign=top>{$smarty.foreach.mailmerge.iteration}</td>
						<td class="listTableRow small" valign=top><input type="checkbox" class=small name="selected_id" value="{$template.templateid}"></td>
						<td class="listTableRow small" valign=top><b>{$template.filename}</b></a></td>
						<td class="listTableRow small" valign=top>{$template.description}&nbsp;</td>
				        <td class="listTableRow small" valign=top>{$template.module}</td>
				        <td class="listTableRow small" valign=top><a href="index.php?module=Settings&action=mailmergedownloadfile&record={$template.templateid}">{$UMOD.LBL_DOWNLOAD}</a></td>
			          </tr>
					{/foreach}
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
	</form>
	</table>
		
	</div>
</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</table>
