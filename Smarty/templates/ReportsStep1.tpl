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
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
	<title>{$MOD.TITLE_VTIGERCRM_CREATE_REPORT}</title>
	<link href="{$THEME_PATH}style.css" rel="stylesheet" type="text/css">
	<script language="JavaScript" type="text/javascript" src="include/js/json.js"></script>
	<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
	<script language="JavaScript" type="text/javascript" src="include/js/{php} echo $_SESSION['authenticated_user_language'];{/php}.lang.js?{php} echo $_SESSION['vtiger_version'];{/php}"></script>
	<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>
	<script language="JavaScript" type="text/javascript" src="modules/Reports/Reports.js"></script>
{$DATE_FORMAT}
</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
<!-- Master Table -->
<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="mailClient mailClientBg">
<tr>
	<td>
		<form name="NewReport" method="POST" ENCTYPE="multipart/form-data" action="index.php" style="margin:0px" onsubmit="VtigerJS_DialogBox.block();">
		<input type="hidden" name='modulesString' value=''/>
		<input type="hidden" name='primarymodule' value="{$PRI_MODULE}"/>
		<input type="hidden" name='secondarymodule' value="{$SEC_MODULE}"/>
		<input type="hidden" name='record' value="{$REPORT_ID}"/>
		<input type="hidden" name='module' value='Reports'/>
		<input type="hidden" name='reload' value='true'/>
		<input type="hidden" name='action' value='Save'/>
		<input type="hidden" name='file' value=''/>
		<input type="hidden" name='reportName' value="{$REPORT_NAME}"/>
		<input type="hidden" name='reportDesc' value="{$REPORT_DESC}"/>
		<input type="hidden" name='folder' value="{$FOLDERID}"/>
		<!-- Heading -->
		<table width="100%" border="0" cellspacing="0" cellpadding="5" >
			<tr>
				<td  class="moduleName" width="80%">{$MOD.LBL_CREATE_REPORT} </td>
				<td  width=30% nowrap class="componentName" align=right>{$MOD.LBL_CUSTOM_REPORTS}</td>
			</tr>
		</table>

		<table width="100%" border="0" cellspacing="0" cellpadding="5" class="homePageMatrixHdr"> 
		<tr>
		<td>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" > 
					<tr>
					<td width="25%" valign="top">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
							<tr><td class="settingsTabList" style="padding-left:10px;">1. {$MOD.LBL_REPORT_DETAILS}</td></tr>
							<tr><td class="settingsTabList" style="padding-left:10px;">2. {$MOD.LBL_RELATIVE_MODULE} </td></tr>
							<tr><td id="step1label" class="settingsTabSelected" style="padding-left:10px;">3. {$MOD.LBL_REPORT_TYPE} </td></tr>
							<tr><td id="step2label" class="settingsTabList" style="padding-left:10px;">4. {$MOD.LBL_SELECT_COLUMNS}</td></tr>
							<tr><td id="step3label" class="settingsTabList" style="padding-left:10px;">5. {$MOD.LBL_SPECIFY_GROUPING}</td></tr>
							<tr><td id="step4label" class="settingsTabList" style="padding-left:10px;">6. {$MOD.LBL_CALCULATIONS}</td></tr>
							<tr><td id="step5label" class="settingsTabList" style="padding-left:10px;">7. {$MOD.LBL_FILTERS} </td></tr>
							<tr><td id="step6label" class="settingsTabList" style="padding-left:10px;">8. {$MOD.LBL_SHARING} </td></tr>
						</table>
					</td>
					<td width="75%" valign="top" bgcolor=white>
						<!-- Step 1 -->
						<div id="step1" style="display:block;">
						<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="100%">
							<tbody><tr>
							<td colspan="2">
									{php}include("modules/Reports/ReportType.php");{/php}
							</td></tr>		
							</tbody>
						</table>
						</div>	
	
						<!-- Step 2 -->
						<div id="step2" style="display:none;">
						<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="100%">
							<tbody><tr>
							<td colspan="2">
									{php}include("modules/Reports/ReportColumns.php");{/php}
							</td></tr>
							</tbody>
						</table>
						</div>
	
						<!-- Step 3 -->
						<div id="step3" style="display:none;">
						<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="100%">
							<tbody><tr>
							<td colspan="2">
									{php}include("modules/Reports/ReportGrouping.php");{/php}
							</td></tr>
							</tbody>
						</table>
						</div>	

						<!-- Step 4 -->
						<div id="step4" style="display:none;">
						<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="100%">
							<tbody><tr>
							<td colspan="2">
									{php}include("modules/Reports/ReportColumnsTotal.php");{/php}
							</td></tr>
							</tbody>
						</table>
						</div>	
	
						<!-- Step 5 -->
						<div id="step5" style="display:none;">
						<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="100%">
							<tbody><tr>
							<td colspan="2">
									{php}include("modules/Reports/ReportFilters.php");{/php}
							</td></tr>
							</tbody>
						</table>
						</div>	
						
						<div id="step6" style="display:none;">
						<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="100%">
							<tbody><tr>
							<td colspan="2">
									{php}include("modules/Reports/ReportSharing.php");{/php}
							</td></tr>
							</tbody>
						</table>
						</div>	

					</td>
					</tr>
					</table>
				
					<table width=100% cellspacing=0 cellpadding=0 class="reportCreateBottom">
					<tr>
						<td>&nbsp;</td>
						<td align="right" style="padding:10px;" >
						<input type="button" id="back_rep" name="back_rep" value=" &nbsp;&lt;&nbsp;{$APP.LBL_BACK}&nbsp; " class="crmbutton small cancel" onClick="changeStepsback1();">&nbsp;
						<input type="button" id="next" name="next" value=" &nbsp;{$APP.LNK_LIST_NEXT}&nbsp;&rsaquo;&nbsp; " class="crmbutton small save" onClick="changeSteps1();">
						&nbsp;<input type="button" name="cancel" value=" &nbsp;{$APP.LBL_CANCEL_BUTTON_LABEL}&nbsp; " class="crmbutton small cancel" onClick="self.close();">
						</td>
					</tr>
					</table>
		</td>
		</tr></form>	
		</table>
</td>
</tr>
</table>

</body>
</html>
<script>
var finish_text = '  {$APP.LBL_FINISH}   ' 
var next_text = '  {$APP.LNK_LIST_NEXT}  ';
{literal}
setObjects();
hideTabs();
</script>
{/literal}
<script>
{if $BACK_WALK neq 'true'}
	document.getElementById('back_rep').disabled = true;
	var backwalk_flag = false;
{else}
	var backwalk_flag = true;
{/if}
</script>
