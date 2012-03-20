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
<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" height="500" valign="top" width="100%">
	<tbody><tr>
	<td colspan="2">
	<span class="genHeaderGray"> {$MOD.LBL_REPORT_TYPE} </span><br>
	{$MOD.LBL_SELECT_REPORT_TYPE_BELOW}							
	<hr>
	</td>
	</tr>
	<tr>
	<td style="padding-right: 5px;" rowspan="2" align="right" width="25%">
	<img src="{'tabular.gif'|@vtiger_imageurl:$THEME}" align="absmiddle">
	</td>
	<td style="padding-left: 5px;" align="left" height="50" valign="bottom" width="75%">
	{if $REPORT_TYPE eq 'tabular'}
	<input checked type="radio" name="reportType" id="reportType" value="tabular" onChange="hideTabs()">
	{else}
	<input type="radio" name="reportType" id="reportType" value="tabular" onChange="hideTabs()">
	{/if}
	<b> {$MOD.LBL_TABULAR_FORMAT}</b></td>
	</tr><tr><td style="padding-left: 25px;" align="left" valign="top" width="75%">
	 {$MOD.LBL_TABULAR_REPORTS_ARE_SIMPLEST}	
	</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
	<td style="padding-right: 5px;" rowspan="2" align="right" width="25%">
	<img src="{'summarize.gif'|@vtiger_imageurl:$THEME}" align="absmiddle">
	</td>
	<td style="padding-left: 5px;" align="left" height="50" valign="bottom" width="75%">
	{if $REPORT_TYPE eq 'summary'}
	<input type="radio" checked name="reportType" value="summary" onclick="hideTabs()">
	{else}
	<input type="radio" name="reportType" value="summary" onclick="hideTabs()">
	{/if}
	<b> {$MOD.LBL_SUMMARY_REPORT}</b></td>
	</tr><tr><td style="padding-left: 25px;" align="left" valign="top" width="75%">
	 {$MOD.LBL_SUMMARY_REPORT_VIEW_DATA_WITH_SUBTOTALS}
	</td>
	</tr>
	<tr><td colspan="2" height="235">&nbsp;</td></tr>
	</tbody>
</table>
