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
<table width="100%"  border="0" cellspacing="0" cellpadding="0" >
	<tr><td colspan="2" class="mailSubHeader" height=25><b>{$MOD.LBL_FEEDS_LIST} {$TITLE}</b></td></tr>
	<tr class="hdrNameBg">
		<td valign=top height=25><input type="button" name="delete" value=" {$MOD.LBL_DELETE_BUTTON} " class="crmbutton small delete" onClick="DeleteRssFeeds('{$ID}');"/></td>
		<td align="right"><input type="button" name="setdefault" value=" {$MOD.LBL_SET_DEFAULT_BUTTON}  " class="crmbutton small create" onClick="makedefaultRss('{$ID}');"/>
		</td>
	</tr>
	<tr><td colspan="2" align="left"><div id="rssScroll">{$RSSDETAILS}</div></td></tr>
</table>
