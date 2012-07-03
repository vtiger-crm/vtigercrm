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
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$APP.LBL_JSCALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script language="JavaScript" type="text/javascript" src="include/calculator/calc.js"></script>

<script type="text/javascript">
	var rel_fields = {$REL_FIELDS};
</script>

{$BLOCKJS_STD}

<table class="small" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" height="532" width="100%" valign="top">
	<tr>
		<td colspan="2" valign="top" height="50">
			<span class="genHeaderGray">{$MOD.LBL_FILTERS}</span><br>
			{$MOD.LBL_SELECT_FILTERS_TO_STREAMLINE_REPORT_DATA}
			<hr>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="left" valign="top">
			<span id='std_filter_div_show' name='std_filter_div_show'>
				<img border="0" align="absmiddle" src={'inactivate.gif'|@vtiger_imageurl:$THEME}  onclick="showHideDivs('std_filter_div','std_filter_div_show');" style="cursor:pointer;" />
				<b>{$MOD.LBL_SHOW_STANDARD_FILTERS}</b>
			</span>
		</td>
	</tr>
	<tr>
		<td colspan="2" valign="top">
			<div id='std_filter_div' name='std_filter_div' style="display:none;height:100;">
				<table class="small" border="0" cellpadding="5" cellspacing="0" width="100%">
					<tr>
						<td class="detailedViewHeader" colspan="4">
							<img border="0" align="absmiddle" src={'activate.gif'|@vtiger_imageurl:$THEME} onclick="showHideDivs('std_filter_div_show','std_filter_div');" style="cursor:pointer;" />
							<b>{$MOD.LBL_STANDARD_FILTER}</b>
						</td>
					</tr>
					<tr>
						<td class="dvtCellLabel" width="30%">{$MOD.LBL_SF_COLUMNS}:</td>
						<td class="dvtCellLabel" width="30%">&nbsp;</td>
						<td class="dvtCellLabel" width="20%">{$MOD.LBL_SF_STARTDATE}:</td>
						<td class="dvtCellLabel" width="20%">{$MOD.LBL_SF_ENDDATE}:</td>
					</tr>
					<tr>
						<td class="dvtCellInfo" width="60%">
							<select name="stdDateFilterField" class="detailedViewTextBox" onchange='standardFilterDisplay();'>
							{$BLOCK1_STD}
							</select>
						</td>
						<td class="dvtCellInfo" width="25%">
							<select name="stdDateFilter" id="stdDateFilter" onchange='showDateRange( this.options[ this.selectedIndex ].value )' class="repBox">
							{$BLOCKCRITERIA_STD}
							</select>
						</td>
						<td class="dvtCellInfo">
							<input name="startdate" id="jscal_field_date_start" style="border: 1px solid rgb(186, 186, 186);" size="10" maxlength="10" value="{$STARTDATE_STD}" type="text"><br>
							<img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_start" >
							<font size="1"><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
							<script type="text/javascript">
                                Calendar.setup ({ldelim}
                                inputField : "jscal_field_date_start", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1
                                {rdelim})
							</script>
						</td>
						<td class="dvtCellInfo">
							<input name="enddate" id="jscal_field_date_end" style="border: 1px solid rgb(186, 186, 186);" size="10" maxlength="10" value="{$ENDDATE_STD}" type="text"><br>
							<img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_end" >
							<font size="1"><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
			                <script type="text/javascript">
                                Calendar.setup ({ldelim}
                                inputField : "jscal_field_date_end", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_end", singleClick : true, step : 1
                                {rdelim})
			                </script>
						</td>
					</tr>					
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2" valign="top">
			{include file='AdvanceFilter.tpl' SOURCE='reports'}
		</td>
	</tr>
</table>
