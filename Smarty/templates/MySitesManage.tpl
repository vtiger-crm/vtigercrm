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
<!-- BEGIN: main -->
<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td colwidth=90% align=left class=small>
		<table border=0 cellspacing=0 cellpadding=5>
		<tr>
			<td align=left><a href="#" onclick="fetchContents('data');"><img src="{'webmail_settings.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border=0 /></a></td>
			<td class=small align=left><a href="#" onclick="fetchContents('data');">{$MOD.LBL_MY_SITES}</a></td>
		</tr>
		</table>
			
	</td>
	<td align=right width=10%>
		<table border=0 cellspacing=0 cellpadding=0>
		<tr><td nowrap class="componentName">{$MOD.LBL_MY_SITES}</td></tr>
		</table>
	</td>
</tr>
</table>

<table border="0" cellpadding="5" cellspacing="0" width="100%">
<tr>
<td colspan="3" class="genHeaderSmall" align="left">{$MOD.LBL_MY_BOOKMARKS} <hr></td>
</tr>
<tr>
<td colspan="3" align="left"><input name="bookmark" value=" {$MOD.LBL_NEW_BOOKMARK} " class="crmbutton small create" onclick="fnvshobj(this,'editportal_cont');fetchAddSite('');" type="button"></td>
</tr>
</table>
<table border="0" cellpadding="5" cellspacing="0" width="100%" class="listTable bgwhite"> 
<tr>
<td class="colHeader small" align="left" width="5%"><b>{$MOD.LBL_SNO}</b></td>
<td class="colHeader small" align="left" width="75%"><b>{$MOD.LBL_BOOKMARK_NAME_URL}</b></td>

<td class="colHeader small" align="left" width="20%"><b>{$MOD.LBL_TOOLS}</b></td>
</tr>

{foreach name=portallists item=portaldetails key=sno from=$PORTALS}
<tr><td class="listTableRow small" align="left">{$smarty.foreach.portallists.iteration}</td>
<td class="listTableRow small" align="left">
<b>{$portaldetails.portalname}</b><br>
<span class="big">{$portaldetails.portaldisplayurl}</span>
</td>
<td class="listTableRow small" align="left">
<a href="javascript:;" onclick="fnvshobj(this,'editportal_cont');fetchAddSite('{$portaldetails.portalid}');" class="webMnu">{$APP.LBL_EDIT}</a>&nbsp;|&nbsp;
<a href="javascript:;" onclick="DeleteSite('{$portaldetails.portalid}');"class="webMnu">{$APP.LBL_MASS_DELETE}</a>
</td>
</tr>
{/foreach}
</table>
