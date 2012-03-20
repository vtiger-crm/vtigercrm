<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('modules/CustomView/CustomView.php');
require_once("config.php");
require_once('modules/Reports/Reports.php');
require_once('include/logging.php');
require_once("modules/Reports/ReportRun.php");
require_once('include/utils/utils.php');
require_once('Smarty_setup.php');

global $adb,$mod_strings,$app_strings;

$reportid = vtlib_purify($_REQUEST["record"]);
$folderid = vtlib_purify($_REQUEST["folderid"]);
$filtercolumn = vtlib_purify($_REQUEST["stdDateFilterField"]);
$filter = vtlib_purify($_REQUEST["stdDateFilter"]);
// Added to fix the issue

$sql = "select * from vtiger_report where reportid=?";
$res = $adb->pquery($sql, array($reportid));
$Report_ID = $adb->query_result($res,0,'reportid');
$numOfRows = $adb->num_rows($res);
if($numOfRows > 0)
{
	$startdate = getDBInsertDateValue($_REQUEST["startdate"]);//Convert the user date format to DB date format 
	$enddate = getDBInsertDateValue($_REQUEST["enddate"]);//Convert the user date format to DB date format

	global $primarymodule,$secondarymodule,$orderbylistsql,$orderbylistcolumns,$ogReport;
	//added to fix the ticket #5117
	global $current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');

	$ogReport = new Reports($reportid);
	$primarymodule = $ogReport->primodule;
	$restrictedmodules = array();
	if($ogReport->secmodule!='')
		$rep_modules = split(":",$ogReport->secmodule);
	else
		$rep_modules = array();

	array_push($rep_modules,$primarymodule);
	$modules_permitted = true;
	$modules_export_permitted = true;
	foreach($rep_modules as $mod){
		if(isPermitted($mod,'index')!= "yes" || vtlib_isModuleActive($mod)==false){
			$modules_permitted = false;
			$restrictedmodules[] = $mod;
		}
		if(isPermitted("$mod",'Export','')!='yes')
			$modules_export_permitted = false;
	}
	
	if(isPermitted($primarymodule,'index') == "yes" && $modules_permitted == true)
	{
		$oReportRun = new ReportRun($reportid);
		$filterlist = $oReportRun->RunTimeFilter($filtercolumn,$filter,$startdate,$enddate);
		
		// Performance Optimization: Direct output of the report result
		$list_report_form = new vtigerCRM_Smarty;
				
		//$sshtml = $oReportRun->GenerateReport("HTML",$filterlist);
		//if(is_array($sshtml))$totalhtml = $oReportRun->GenerateReport("TOTALHTML",$filterlist);
		
		$sshtml = array();
		$totalhtml = '';
		$list_report_form->assign("DIRECT_OUTPUT", true);
		$list_report_form->assign_by_ref("__REPORT_RUN_INSTANCE", $oReportRun);
		$list_report_form->assign_by_ref("__REPORT_RUN_FILTER_LIST", $filterlist);
		// END		
		
		$ogReport->getSelectedStandardCriteria($reportid);
		//commented to omit dashboards for vtiger_reports
		//require_once('modules/Dashboard/ReportsCharts.php');
		//$image = get_graph_by_type('Report','Report',$primarymodule,'',$sshtml[2]);
		//$list_report_form->assign("GRAPH", $image);

		$BLOCK1 = getPrimaryStdFilterHTML($ogReport->primodule,$ogReport->stdselectedcolumn);
		$BLOCK1 .= getSecondaryStdFilterHTML($ogReport->secmodule,$ogReport->stdselectedcolumn);
		// Check if selectedcolumn is found in the filters (Fix for ticket #4866)
		$selectedcolumnvalue = '"'. decode_html($ogReport->stdselectedcolumn) . '"';
		if (!$is_admin && isset($ogReport->stdselectedcolumn) && strpos($BLOCK1, $selectedcolumnvalue) === false) {
			$BLOCK1 .= "<option selected value='Not Accessible'>".$app_strings['LBL_NOT_ACCESSIBLE']."</option>";
		}
		$list_report_form->assign("BLOCK1",$BLOCK1);
		$BLOCKJS = $ogReport->getCriteriaJS();
		$list_report_form->assign("BLOCKJS",$BLOCKJS);

		$BLOCKCRITERIA = $ogReport->getSelectedStdFilterCriteria($ogReport->stdselectedfilter);
		$list_report_form->assign("BLOCKCRITERIA",$BLOCKCRITERIA);
		if(isset($ogReport->startdate) && isset($ogReport->enddate))
		{
			$list_report_form->assign("STARTDATE",getDisplayDate($ogReport->startdate));
			$list_report_form->assign("ENDDATE",getDisplayDate($ogReport->enddate));
		}else
		{
			$list_report_form->assign("STARTDATE",$ogReport->startdate);
			$list_report_form->assign("ENDDATE",$ogReport->enddate);	
		}	
		$list_report_form->assign("MOD", $mod_strings);
		$list_report_form->assign("APP", $app_strings);
		$list_report_form->assign("IMAGE_PATH", $image_path);
		$list_report_form->assign("REPORTID", $reportid);
		$list_report_form->assign("IS_EDITABLE", $ogReport->is_editable);
		
		$list_report_form->assign("REPORTNAME", htmlspecialchars($ogReport->reportname,ENT_QUOTES,$default_charset));
		if(is_array($sshtml))$list_report_form->assign("REPORTHTML", $sshtml);
		else $list_report_form->assign("ERROR_MSG", getTranslatedString('LBL_REPORT_GENERATION_FAILED', $currentModule) . "<br>" . $sshtml);
		$list_report_form->assign("REPORTTOTHTML", $totalhtml);
		$list_report_form->assign("FOLDERID", $folderid);
		$list_report_form->assign("DATEFORMAT",$current_user->date_format);
		$list_report_form->assign("JS_DATEFORMAT",parse_calendardate($app_strings['NTC_DATE_FORMAT']));
		if($modules_export_permitted==true){
			$list_report_form->assign("EXPORT_PERMITTED","YES");
		} else {
			$list_report_form->assign("EXPORT_PERMITTED","NO");
		}
		$rep_in_fldr = $ogReport->sgetRptsforFldr($folderid);
		for($i=0;$i<count($rep_in_fldr);$i++){
			$rep_id = $rep_in_fldr[$i]['reportid'];
			$rep_name = $rep_in_fldr[$i]['reportname'];
			$reports_array[$rep_id]=$rep_name;
		}
		if($_REQUEST['mode'] != 'ajax')
		{
			$list_report_form->assign("REPINFOLDER", $reports_array);
			include('themes/'.$theme.'/header.php');
			$list_report_form->display('ReportRun.tpl');
		}
		else
		{
			$list_report_form->display('ReportRunContents.tpl');
		}
	}
	else
	{
		if($_REQUEST['mode'] != 'ajax')
		{
			include('themes/'.$theme.'/header.php');
		}	
		echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$mod_strings['LBL_NO_ACCESS']." : ".implode(",",$restrictedmodules)." </span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>			   	
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>								   		     </td>
		</tr>
		</tbody></table> 
		</div>";
		echo "</td></tr></table>";
	}
}
else
{
		echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";	
		echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$mod_strings['LBL_REPORT_DELETED']."</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>                                                                                </td>
		</tr>
		</tbody></table>
		</div>";
		echo "</td></tr></table>";
}
	/** Function to get the StdfilterHTML strings for the given  primary module 
	 *  @ param $module : Type String
	 *  @ param $selected : Type String(optional)	
	 *  This Generates the HTML Combo strings for the standard filter for the given reports module  
	 *  This Returns a HTML sring
	 */
function getPrimaryStdFilterHTML($module,$selected="")
{

	global $app_list_strings;
	global $ogReport;
	global $current_language;
	$ogReport->oCustomView=new CustomView();
	$result = $ogReport->oCustomView->getStdCriteriaByModule($module);
	$mod_strings = return_module_language($current_language,$module);
	if(isset($result))
	{
		foreach($result as $key=>$value)
		{
			if(isset($mod_strings[$value]))
			{
				if($key == $selected)
				{
					$shtml .= "<option selected value=\"".$key."\">".getTranslatedString($module,$module)." - ".$mod_strings[$value]."</option>";
				}else
				{
					$shtml .= "<option value=\"".$key."\">".getTranslatedString($module,$module)." - ".$mod_strings[$value]."</option>";
				}
			}else
			{
				if($key == $selected)
				{
					$shtml .= "<option selected value=\"".$key."\">".getTranslatedString($module,$module)." - ".$value."</option>";
				}else
				{
					$shtml .= "<option value=\"".$key."\">".getTranslatedString($module,$module)." - ".$value."</option>";
				}
			}
		}
	}

	return $shtml;
}

	/** Function to get the StdfilterHTML strings for the given secondary module 
	 *  @ param $module : Type String
	 *  @ param $selected : Type String(optional)	
	 *  This Generates the HTML Combo strings for the standard filter for the given reports module  
	 *  This Returns a HTML sring
	 */
function getSecondaryStdFilterHTML($module,$selected="")
{
	global $app_list_strings;
	global $ogReport;
	global $current_language;
	$ogReport->oCustomView=new CustomView();

	if($module != "")
        {
        	$secmodule = explode(":",$module);
        	for($i=0;$i < count($secmodule) ;$i++)
        	{
			$result =  $ogReport->oCustomView->getStdCriteriaByModule($secmodule[$i]);
			$mod_strings = return_module_language($current_language,$secmodule[$i]);
        		if(isset($result))
        		{
                		foreach($result as $key=>$value)
                		{
                        		if(isset($mod_strings[$value]))
                                        {
						if($key == $selected)
						{
							$shtml .= "<option selected value=\"".$key."\">".getTranslatedString($secmodule[$i],$secmodule[$i])." - ".$mod_strings[$value]."</option>";
						}else
						{
							$shtml .= "<option value=\"".$key."\">".getTranslatedString($secmodule[$i],$secmodule[$i])." - ".$mod_strings[$value]."</option>";
						}
					}else
					{
						if($key == $selected)
						{
							$shtml .= "<option selected value=\"".$key."\">".getTranslatedString($secmodule[$i],$secmodule[$i])." - ".$value."</option>";
						}else
						{
							$shtml .= "<option value=\"".$key."\">".getTranslatedString($secmodule[$i],$secmodule[$i])." - ".$value."</option>";
						}
					}
                		}
        		}
		
		}
	}
	return $shtml;
}
	/** Function to get the reports under a report folder 
	 *  @ param $folderid : Type Integer 
	 *  This Returns $reports_array in the following format 
	 *  		$reports_array = array ($reportid=>$reportname,$reportid=>$reportname1,.............,$reportidn=>$reportname)
	 */
function getReportsinFolder($folderid)
{
	global $adb;
	$query = 'select reportid,reportname from vtiger_report where folderid=?';
	$result = $adb->pquery($query, array($folderid));
	$reports_array = Array();
	for($i=0;$i < $adb->num_rows($result);$i++)	
	{
		$reportid = $adb->query_result($result,$i,'reportid');
		$reportname = $adb->query_result($result,$i,'reportname');
		$reports_array[$reportid] = $reportname; 
	}
	if(count($reports_array) > 0)
		return $reports_array;
	else
		return false;
}
?>
