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
			<td align=left><a href="#" onclick="fetchContents('manage');"><img src="{'webmail_settings.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border=0 /></a></td>
			<td class=small align=left><a href="#" onclick="fetchContents('manage');">{$MOD.LBL_MANAGE_SITES}</a>			     </td>
			<td align="right"><input type="button" name="setdefault" value=" {$MOD.LBL_SET_DEFAULT_BUTTON}  " class="crmbutton small create" onClick="defaultMysites(this);"/>
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

<table border=0 cellspacing=0 cellpadding=5 width=100% class="mailSubHeader">
<tr>
<td nowrap align=left>{$MOD.LBL_BOOKMARK_LIST} : </span></td>
<td align=left width=90% >
	<select id="urllist" name="urllist" style="width: 99%;" class="small" onChange="setSite(this);">
	{foreach item=portaldetails key=sno from=$PORTALS}
	{if $portaldetails.set_def eq 1}
		<option selected value="{$portaldetails.portalid}">{$portaldetails.portalname}</option>
	{else}
		<option value="{$portaldetails.portalid}">{$portaldetails.portalname}</option>
	{/if}
	<!--<option value="{$portaldetails.portalurl}">{$portaldetails.portalname}</option>-->
	{/foreach}
	</select>	
</td>
</tr>
<tr>
	<td bgcolor="#ffffff" colspan=2>
		<iframe id="locatesite" src="{$DEFAULT_URL}" frameborder="0" height="1100" scrolling="auto" width="100%"></iframe>
	</td>
</tr>
</table>

