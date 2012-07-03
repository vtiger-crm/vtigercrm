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
<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" height='500' width="100%">
	<tbody><tr>
	<td colspan="2">
	<span class="genHeaderGray">{$MOD.LBL_SPECIFY_GROUPING}</span><br>
	{$MOD.LBL_SELECT_COLUMNS_TO_GROUP_REPORTS} 
	<hr>
	</td>
	</tr>
	<tr>
	<td style="padding-left: 5px;" align="left" width="65%">
	{$MOD.LBL_GROUPING_SUMMARIZE}	
	<select id="Group1" name="Group1" class="txtBox" onchange="getDateFieldGrouping('Group1')">
	<option value="none">{$MOD.LBL_NONE}</option>
	{$BLOCK1}
	</select>
	</td>
    <td style="padding-left: 5px;" align="left" width="25%">
        {$GROUPBYTIME1}
	</td>
	<td style="padding-left: 5px;" align="left" width="35%">
	{$MOD.LBL_GROUPING_SORT}<br>
	<select name="Sort1" class="importBox">
	{$ASCDESC1}
	</select>
	</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
	<td style="padding-left: 35px;" align="left">
	{$MOD.LBL_GROUPING_THEN_BY}<br>
	<select id="Group2" name="Group2" class="txtBox" onchange="getDateFieldGrouping('Group2')">
	<option value="none">{$MOD.LBL_NONE}</option>
	        {$BLOCK2}
	</select>
	</td>
    <td style="padding-left: 40px;" align="left">
        {$GROUPBYTIME2}
    </td>
	<td style="padding-left: 20px;" align="left">
	{$MOD.LBL_GROUPING_SORT}<br>
	<select name="Sort2" class="importBox">
	{$ASCDESC2}
	</select>
	</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
	<td style="padding-left: 65px;" align="left">
	{$MOD.LBL_GROUPING_FINALLY_BY}<br>
	<select id="Group3" name="Group3" class="txtBox" onchange="getDateFieldGrouping('Group3')">
	<option value="none">{$MOD.LBL_NONE}</option>
	            {$BLOCK3}
	</select>
	</td>
    <td style="padding-left: 40px;" align="left">
        {$GROUPBYTIME3}
    </td>
	<td style="padding-left: 40px;" align="left">
	{$MOD.LBL_GROUPING_SORT}<br>
	<select name="Sort3" class="importBox">
	{$ASCDESC3}
	</select>
	</td>
	</tr>
	<tr><td colspan="2" height="305">&nbsp;</td></tr>
	</tbody>
</table>

