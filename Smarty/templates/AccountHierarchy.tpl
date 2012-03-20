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
<link rel="stylesheet" type="text/css" href="{$THEME_PATH}style.css">
<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/{php} echo $_SESSION['authenticated_user_language'];{/php}.lang.js?{php} echo $_SESSION['vtiger_version'];{/php}"></script>
<script language="JavaScript" type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>

{include file='Buttons_List1.tpl'}
		</td>
	</tr>
</table>

{*<!-- Contents -->*}
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
     <tr>
        <td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>

	<td class="showPanelBg" valign="top" width=100% style="padding:10px;">
			<table width="100%" border="0" cellpadding="5" cellspacing="0">
				<tr>
					<td class="moduleName" style="padding-left:10px;">{$APP.LBL_ACCOUNT_HIERARCHY}</td>
					<td align="right"><input type="button" class="crmbutton small cancel" onclick="window.history.back();" value="{$APP.LBL_BACK}" /></td>
				</tr>
			</table>
			
			<div id="ListViewContents">				
			{foreach key=header item=detail from=$ACCOUNT_HIERARCHY}
				{if $header eq 'header'}
				<table border=0 cellspacing=1 cellpadding=3 width=100% style="background-color:#eaeaea;" class="small">
					<tr style="height:25px" bgcolor=white>
					{foreach key=header item=headerfields from=$detail}
						<td class="lvtCol">{$headerfields}</td>
					{/foreach}
					</tr>
				{elseif $header eq 'entries'}
					{foreach key=header item=detail from=$detail}
					<tr bgcolor=white>
						{foreach key=header item=listfields from=$detail}
						<td>{$listfields}</td>
						{/foreach}
					</tr>
					{/foreach}
				</table>
				{/if}
			{/foreach}
			</div>

     </td>
        <td valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</table>