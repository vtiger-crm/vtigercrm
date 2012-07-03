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
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
<script language="JAVASCRIPT" type="text/javascript" src="include/scriptaculous/scriptaculous.js"></script>
<script language="JAVASCRIPT" type="text/javascript" src="include/js/json.js"></script>
<script language="JAVASCRIPT" type="text/javascript" src="modules/PickList/DependencyPicklist.js"></script>
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
		<tr>
			<td width=50 rowspan=2 valign=top><img src="{'picklist.gif'|@vtiger_imageurl:$THEME}" width="48" height="48" border=0 ></td>
			<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD_PICKLIST.LBL_PICKLIST_DEPENDENCY_SETUP}</b></td>
		</tr>
		<tr>
			<td valign=top class="small">{$MOD_PICKLIST.LBL_PICKLIST_DEPENDENCY_DESCRIPTION}</td>
		</tr>
		</table>

		<table border=0 cellspacing=0 cellpadding=10 width=100% >
		<tr>
			<td valign=top>			
				<div id="picklist_datas">
					{if $SUBMODE eq 'editdependency'}
						{include file='modules/PickList/PickListDependencyContents.tpl'}
					{else}
						{include file='modules/PickList/PickListDependencyList.tpl'}
					{/if}
				</div>

				<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr>
						<td class="small" nowrap align=right>
							<a href="#top">
								{$MOD.LBL_SCROLL}
							</a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
	</div>
	</td>
</tr>
</tbody>
</table>