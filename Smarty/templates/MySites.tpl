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

<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="modules/Portal/Portal.js"></script>

{include file="Buttons_List1.tpl"}

<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
<tr>
	<td valign=top align=right width=8><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" valign="top" width="100%" align=center >	

	<!-- MySites UI Starts -->
	<br>
	<table border="0" cellpadding="0" cellspacing="0" width="98%" align="center" class="mailClient mailClientBg">
	<tbody>
	
	<tr>
	<td colspan="4">
	
	
	<!-- BOOKMARK PAGE -->
	<div id="portalcont" style="padding:0px 10px 10px 10px; overflow: hidden; width: 98%;">
		{include file="MySitesContents.tpl"}
	</div>
	
	
	</td>
	</tr>
	</tbody></table>
	<br><br>
	<div id="editportal_cont" style="z-index:100001;position:absolute;width:510px;"></div>
	
	</td>
	<td valign=top align=right width=8><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>			
	</tr>
	</table>
