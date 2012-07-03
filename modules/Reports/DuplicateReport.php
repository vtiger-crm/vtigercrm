<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('modules/Reports/Reports.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once 'include/Zend/Json.php';

global $adb,$mod_strings,$app_strings;

$reportid = vtlib_purify($_REQUEST['record']);
$newreportname = vtlib_purify($_REQUEST['newreportname']);
$newreportdescription = vtlib_purify($_REQUEST['newreportdescription']);
$newreportfolder = vtlib_purify($_REQUEST['newreportfolder']);

$sql = "select * from vtiger_report where reportid=?";
$res = $adb->pquery($sql, array($reportid));
$Report_ID = $adb->query_result($res,0,'reportid');
$numOfRows = $adb->num_rows($res);

$response_array = array();

if($numOfRows > 0) {
	
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
	foreach($rep_modules as $mod) {
		if(isPermitted($mod,'index')!= "yes" || vtlib_isModuleActive($mod)==false) {
			$modules_permitted = false;
			$restrictedmodules[] = $mod;
		}
	}
	
	if(isPermitted($primarymodule,'index') == "yes" && $modules_permitted == true) {
		
		$genQueryId = $adb->getUniqueID("vtiger_selectquery");
		if($genQueryId != "") {			
			$response_array['reportid'] = $genQueryId;
			$response_array['folderid'] = $newreportfolder;
			$response_array['errormessage'] = '';
			
			$iquerysql = "insert into vtiger_selectquery (QUERYID,STARTINDEX,NUMOFOBJECTS) values (?,?,?)";
			$iquerysqlresult = $adb->pquery($iquerysql, array($genQueryId,0,0));
			$log->info("Reports :: Save->Successfully saved vtiger_selectquery");
			
			if($iquerysqlresult != false) {
				
				$adb->pquery("INSERT INTO vtiger_selectcolumn (queryid,columnindex,columnname) 
						SELECT $genQueryId, columnindex, columnname FROM vtiger_selectcolumn WHERE queryid = ?", array($reportid));
				
				$adb->pquery("INSERT INTO vtiger_reportsharing (reportid,shareid,setype)
						SELECT $genQueryId,shareid,setype FROM vtiger_reportsharing WHERE reportid=?", array($reportid));
				
				$owner = $current_user->id;
				$ireportresult = $adb->pquery("INSERT INTO vtiger_report (reportid,folderid,reportname,description,reporttype,queryid,state,owner,sharingtype)
						SELECT $genQueryId,$newreportfolder,'$newreportname','$newreportdescription',reporttype,$genQueryId,state,$owner,sharingtype FROM vtiger_report WHERE reportid=?", 
						array($reportid));
				$log->info("Reports :: Save->Successfully saved vtiger_report");
				if($ireportresult != false) {
				
					$adb->pquery("INSERT INTO vtiger_reportmodules (reportmodulesid,primarymodule,secondarymodules)
							SELECT $genQueryId,primarymodule,secondarymodules FROM vtiger_reportmodules WHERE reportmodulesid=?", array($reportid));
					$log->info("Reports :: Save->Successfully saved vtiger_reportmodules");
				
					$adb->pquery("INSERT INTO vtiger_reportsortcol (sortcolid,reportid,columnname,sortorder)
							SELECT sortcolid,$genQueryId,columnname,sortorder FROM vtiger_reportsortcol WHERE reportid=?", array($reportid));
					$log->info("Reports :: Save->Successfully saved vtiger_reportsortcol");
				
					$adb->pquery("INSERT INTO vtiger_reportdatefilter (datefilterid,datecolumnname,datefilter,startdate,enddate)
							SELECT $genQueryId,datecolumnname,datefilter,startdate,enddate FROM vtiger_reportdatefilter WHERE datefilterid=?", array($reportid));
					$log->info("Reports :: Save->Successfully saved vtiger_reportdatefilter");
				
					$adb->pquery("INSERT INTO vtiger_reportsummary (reportsummaryid,summarytype,columnname)
							SELECT $genQueryId,summarytype,columnname FROM vtiger_reportsummary WHERE reportsummaryid=?", array($reportid));
					$log->info("Reports :: Save->Successfully saved vtiger_reportsummary");
				
					$adb->pquery("INSERT INTO vtiger_relcriteria (queryid,columnindex,columnname,comparator,value,groupid,column_condition)
							SELECT $genQueryId,columnindex,columnname,comparator,value,groupid,column_condition FROM vtiger_relcriteria WHERE queryid=?", array($reportid));
					$log->info("Reports :: Save->Successfully saved vtiger_relcriteria");
				
					$adb->pquery("INSERT INTO vtiger_relcriteria_grouping (groupid,queryid,group_condition,condition_expression)
							SELECT groupid,$genQueryId,group_condition,condition_expression FROM vtiger_relcriteria_grouping WHERE queryid=?", array($reportid));
					$log->info("Reports :: Save->Successfully saved vtiger_relcriteria_grouping");
					
					$advft_criteria = $_REQUEST['advft_criteria'];
					$advft_criteria_groups = $_REQUEST['advft_criteria_groups'];
					if(!empty($advft_criteria) && !empty($advft_criteria_groups)) {
						$json = new Zend_Json();
						$advft_criteria = $json->decode($advft_criteria);
						$advft_criteria_groups = $json->decode($advft_criteria_groups);
						updateAdvancedCriteria($genQueryId,$advft_criteria,$advft_criteria_groups);
					} 

				} else {
					$errormessage = "<font color='red'><B>Error Message<ul>
						<li><font color='red'>Error while inserting the record</font>
						</ul></B></font> <br>" ;
					$response_array['errormessage'] = $errormessage;
				}
			} else {
				$errormessage = "<font color='red'><B>Error Message<ul>
					<li><font color='red'>Error while inserting the record</font>
					</ul></B></font> <br>" ;
				$response_array['errormessage'] = $errormessage;
			}
		}		
		
	} else {

		$errormessage = "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		$errormessage .= "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$mod_strings['LBL_NO_ACCESS']." : ".implode(",",$restrictedmodules)." </span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>			   	
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br></td>
		</tr>
		</tbody></table> 
		</div>";
		$errormessage .= "</td></tr></table>";
					
		$response_array['errormessage'] = $errormessage;
	}
	
} else {
		$errormessage = "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";	
		$errormessage .= "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		$errormessage .= "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 10000000;'>

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
		$errormessage .= "</td></tr></table>";
					
		$response_array['errormessage'] = $errormessage;
}

$json = new Zend_Json();
echo $json->encode($response_array);
?>