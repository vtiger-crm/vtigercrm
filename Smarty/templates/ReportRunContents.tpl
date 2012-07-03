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

<script language="javascript" type="text/javascript" src="include/scriptaculous/effects.js"></script>
<script type='text/javascript' src='include/jquery/jquery-1.6.2.min.js'></script>
<script type="text/javascript">
  jQuery.noConflict();
</script>
<br>
<table align="center" border="0" cellpadding="5" cellspacing="0" width="100%" class="mailSubHeader">
	<tbody><tr>
	{if $SHOWCHARTS eq 'true'}
		<td align="right"  width="100%"><input class="crmbutton small create" style="background:#E85313" id="viewcharts1" name="viewcharts1" value="{'LBL_VIEW_CHARTS'|@getTranslatedString:$MODULE}" type="button" onClick="window.location.href = '#viewcharts'" title="{'LBL_VIEW_CHARTS'|@getTranslatedString:$MODULE}"></td>
	{/if}
	</tr>
	</tbody>
</table>

<table style="border: 1px solid rgb(0, 0, 0);" align="center" cellpadding="0" cellspacing="0" width="100%">
	<tbody><tr>
	<td style="background-repeat: repeat-y;" background="{'report_btn.gif'|@vtiger_imageurl:$THEME}" width="16"></td>

	<td style="padding: 5px;" valign="top">
	<table cellpadding="0" cellspacing="0" width="100%">
		<tbody><tr>
		<td align="left" width="75%">
		<span class="genHeaderGray">
		{if $MOD.$REPORTNAME neq ''}
			{$MOD.$REPORTNAME}
		{else}
			{$REPORTNAME}
		{/if}
		</span><br>
		</td>
		<td align="right" width="25%">
		<span class="genHeaderGray">{$APP.LBL_TOTAL} : <span id='_reportrun_total'>{$REPORTHTML.1}</span>  {$APP.LBL_RECORDS}</span>
		</td>
		</tr>
		<tr><td id="report_info" align="left" colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td colspan="2">
		{* Performance Optimization: Direct result output *}
		{if $DIRECT_OUTPUT eq true}		
			{if isset($__REPORT_RUN_INSTANCE)}
				{php}
					$__oReportRun = $this->_tpl_vars['__REPORT_RUN_INSTANCE'];
					$__filterSql = $this->_tpl_vars['__REPORT_RUN_FILTER_SQL'];
					$__oReportRunReturnValue = $__oReportRun->GenerateReport("HTML", $__filterSql, true);
				{/php}
			{/if}		
		{elseif $ERROR_MSG eq ''}
			{$REPORTHTML.0}
		{else}
			{$ERROR_MSG}
		{/if}
		{* END *}
		</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2">
		{* Performance Optimization: Direct result output *}
		{if $DIRECT_OUTPUT eq true}
			{php}
				if(is_array($__oReportRunReturnValue)) { $__oReportRun->GenerateReport("TOTALHTML", $__filterSql, true); }
			{/php}
		{else}			
			{$REPORTTOTHTML}
		{/if}
		{* END *}
		</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		</tbody>
	</table>
	</td>
	<td style="background-repeat: repeat-y;" background="{'report_btn.gif'|@vtiger_imageurl:$THEME}" width="16"></td>
	</tr>

	</tbody>
</table>
<br><br>
{if $SHOWCHARTS eq 'true'}
<div name="viewcharts" id="viewcharts">
<table style="border: 1px solid rgb(0, 0, 0);" align="center" cellpadding="0" cellspacing="0" width="100%">
	<tbody>
		<tr>
			<td style="background-repeat: repeat-y;" background="{'report_btn.gif'|@vtiger_imageurl:$THEME}" width="16"></td>
			<td>
				<table border=0 cellspacing=1 cellpadding=0 width="100%" class="lvtBg">
                    <tr>
						<td> {$PIECHART} </td>
						<td> {$BARCHART} </td>
					</tr>
				</table>
			</td>
			<td style="background-repeat: repeat-y;" background="{'report_btn.gif'|@vtiger_imageurl:$THEME}" width="16"></td>
		</tr>
	</tbody>
</table>
</div>
{/if}
<table align="center" border="0" cellpadding="5" cellspacing="0" width="100%" class="mailSubHeader">
	<tbody><tr>
	{if $SHOWCHARTS eq 'true'}
		<td align="right"  width="100%"><input class="crmbutton small create" style="background:#E85313" id="addChartstodashboard" name="addChartstodashboard" value="{'LBL_ADD_CHARTS'|@getTranslatedString:$MODULE}" type="button" onClick="showAddChartPopup();" title="{'LBL_ADD_CHARTS'|@getTranslatedString:$MODULE}"></td>
	{/if}	
	</tr>
	</tbody>
</table>

<div id="addcharttoHomepage"  class="layerPopup" style="z-index:2000; display:none; width: 400px;">
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
    <tr>
        <td align="left" id="divHeader" class="layerPopupHeading" width="80%"><b>Add ReportCharts</b></td>
        <td align="right">
                <a onclick="fnhide('addcharttoHomepage');" href="javascript:;">
                <img border="0" align="absmiddle" src="{'close.gif'|@vtiger_imageurl:$THEME}"></a>
        </td>
    </tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
<tr>
    <td class="small">
        <table border="0" cellspacing="0" cellpadding="3" width="100%" align="center" bgcolor="white">
            <tr>
                <td class="dvtCellLabel"  width="110" align="right">{'LBL_HOME_WINDOW_TITLE'|@getTranslatedString:$MODULE}<font color='red'>*</font></td>
                <td class="dvtCellInfo" colspan="2" width="300" align="left"><input type="text" name="windowtitle" id="windowtitle_id" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" style="width:57%"></td>
            </tr>
            <tr>
                <td class="dvtCellLabel"  width="110" align="right">{'LBL_HOME_REPORT_NAME'|@getTranslatedString:$MODULE}</td>
                <td id="selReportName" class="dvtCellInfo" colspan="2" width="300" align="left">{$REPORTNAME}</td>
            </tr>
            <tr>
                <td class="dvtCellLabel"  width="110" align="right">{'LBL_HOME_REPORT_TYPE'|@getTranslatedString:$MODULE}</td>
                <td id="selReportType" class="dvtCellInfo" width="300" colspan="2" align="left">
                        <select name="selreporttype" id="selreportcharttype_id" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" style="width:60%">
                                <option value="horizontalbarchart">{'LBL_HOME_HORIZONTAL_BARCHART'|@getTranslatedString:$MODULE}</option>
                                <option value="verticalbarchart">{'LBL_HOME_VERTICAL_BARCHART'|@getTranslatedString:$MODULE}</option>
                                <option value="piechart">{'LBL_HOME_PIE_CHART'|@getTranslatedString:$MODULE}</option>
                        </select>
                </td>
            </tr>
        </table>
      </td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=95% align="center">
    <tr>
        <td align="right">
            <input type="button" name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " id="savebtn" class="crmbutton small save" onclick="addChartsToHomepage({$REPORTID})"></td>
            <td align="left"><input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmbutton small cancel" onclick="fnhide('addcharttoHomepage');">
        </td>
    </tr>
 </table>
</div>

<div name="widgetmessage" id="widgetsuccess" style="display:none;background-color:#E0ECFF;width:150px;top:600px;right:481px;position:absolute">
    <table cellpadding="10" cellspacing="0" border="0" width="100%" class="vtResultPop small">
        <tr>
            <td align="center">
               {'LBL_WIDGET_ADDED'|@getTranslatedString:$MODULE}
            </td>
        </tr>
    </table>
</div>
