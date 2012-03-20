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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
<title>{$MOD.TITLE_VTIGERCRM_MAIL}</title>
<link REL="SHORTCUT ICON" HREF="include/images/vtigercrm_icon.ico">	
<style type="text/css">@import url("themes/{$THEME}/style.css");</style>
	<body  onLoad=window.print()>
		<table>
			<tr>
				<td class="lvtCol" width="15%" height="10px" style="padding: 1px;" align="left"><b>{$MOD.LBL_FROM}</b></td>
				<td class="dvtCellLabel" style="padding: 1px;">&nbsp;{$FROM_MAIL}</td>
			</tr>
			<tr>
				<td class="lvtCol" width="15%" height="10px" style="padding: 1px;" align="left"><b>{$MOD.LBL_TO}</b></td>
				<td class="dvtCellLabel" style="padding: 1px;">&nbsp;{$TO_MAIL}</td>
				
			</tr>
			<tr>
				<td class="lvtCol" width="15%" height="10px" style="padding: 1px;" align="left"><b>{$MOD.LBL_CC}</b></td>
				<td class="dvtCellLabel" style="padding: 1px;">&nbsp;{$CC_MAIL}</td>
			</tr>
			   <tr>
				<td class="lvtCol" style="padding: 1px;" align="left"><b>{$MOD.LBL_BCC}</b></td>
				<td class="dvtCellLabel" style="padding: 1px;">&nbsp;{$BCC_MAIL}</td>
			</tr>
			<tr>
				<td class="lvtCol" style="padding: 1px;" align="left"><b>{$MOD.LBL_SUBJECT}</b></td>
				<td class="dvtCellLabel" style="padding:1px;">&nbsp;{$SUBJECT}</td>
			</tr>
			<tr>
				<td colspan="2" class="dvtCellLabel" style="padding:1px;">&nbsp;{$DESCRIPTION}</td>
			</tr>
	
		</table>
	</body>
</html>