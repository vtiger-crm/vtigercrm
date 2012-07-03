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

<script language="JavaScript" type="text/javascript" src="include/js/MenuEditor.js"></script>

<div id="vtlib_modulemanager" style="display:block;position:absolute;width:500px;"></div>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>
	<div align=center>
		{include file='SetMenu.tpl'}
		<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td rowspan="2" valign="top" width="50"><img src="{'menueditor.png'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MENU_EDITOR}" title="{$MOD.LBL_MENU_EDITOR}" border="0" height="48" width="48"></td>
			<td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> &gt; {$MOD.LBL_MENU_EDITOR}</b></td>
		</tr>

		<tr>
			<td class="small" valign="top">{'LBL_MENU_DESC'|@getTranslatedString}</td>
		</tr>
		</table>
		<br>
	</div>
       <!-- Standard modules -->
       <div id='menueditor' align="center">
            {include file="Settings/MenuEditorAssign.tpl"}
        </div>
        </td>
</tr>
</table>


