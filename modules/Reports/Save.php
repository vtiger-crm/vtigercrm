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
require_once("include/Zend/Json.php");
require_once 'modules/Reports/ReportUtils.php';
require_once('modules/Reports/CustomReportUtils.php');

global $adb;
global $log,$current_user;
$reportid = vtlib_purify($_REQUEST["record"]);

//<<<<<<<selectcolumn>>>>>>>>>
$selectedcolumnstring = $_REQUEST["selectedColumnsString"];
//<<<<<<<selectcolumn>>>>>>>>>

//<<<<<<<reportsortcol>>>>>>>>>
$sort_by1 = decode_html(vtlib_purify($_REQUEST["Group1"]));
$sort_order1 = vtlib_purify($_REQUEST["Sort1"]);
$sort_by2 =decode_html(vtlib_purify($_REQUEST["Group2"]));
$sort_order2 = vtlib_purify($_REQUEST["Sort2"]);
$sort_by3 = decode_html(vtlib_purify($_REQUEST["Group3"]));
$sort_order3 = vtlib_purify($_REQUEST["Sort3"]);

//<<<<<<<reportgrouptime>>>>>>>
$groupTime1 = vtlib_purify($_REQUEST['groupbytime1']);
$groupTime2 = vtlib_purify($_REQUEST['groupbytime2']);
$groupTime3 = vtlib_purify($_REQUEST['groupbytime3']);
//<<<<<<<reportgrouptime>>>>>>>

//<<<<<<<reportsortcol>>>>>>>>>
$selectedcolumns = explode(";",$selectedcolumnstring);
if(!in_array($sort_by1,$selectedcolumns)){
	$selectedcolumns[] = $sort_by1;
}
if(!in_array($sort_by2,$selectedcolumns)){
	$selectedcolumns[] = $sort_by2;
}
if(!in_array($sort_by3,$selectedcolumns)){
	$selectedcolumns[] = $sort_by3;
}
//<<<<<<<reportmodules>>>>>>>>>
$pmodule = vtlib_purify($_REQUEST["primarymodule"]);
$smodule = vtlib_purify($_REQUEST["secondarymodule"]);
//<<<<<<<reportmodules>>>>>>>>>

//<<<<<<<report>>>>>>>>>
$reportname = vtlib_purify($_REQUEST["reportName"]);
$reportdescription = vtlib_purify($_REQUEST["reportDesc"]);
$reporttype = vtlib_purify($_REQUEST["reportType"]);
$folderid = vtlib_purify($_REQUEST["folder"]);
//<<<<<<<report>>>>>>>>>

//<<<<<<<standarfilters>>>>>>>>>
$stdDateFilterField = vtlib_purify($_REQUEST["stdDateFilterField"]);
$stdDateFilter = vtlib_purify($_REQUEST["stdDateFilter"]);
$startdate = $_REQUEST["startdate"];
$enddate = $_REQUEST["enddate"];
$dbCurrentDateTime = new DateTimeField(date('Y-m-d H:i:s'));
if(!empty($startdate)) {
	$startDateTime = new DateTimeField($startdate.' '. $dbCurrentDateTime->getDisplayTime());
	$startdate = $startDateTime->getDBInsertDateValue();
}
if(!empty($enddate)) {
	$endDateTime = new DateTimeField($enddate.' '. $dbCurrentDateTime->getDisplayTime());
	$enddate = $endDateTime->getDBInsertDateValue();
}
//<<<<<<<standardfilters>>>>>>>>>

//<<<<<<<shared entities>>>>>>>>>
$sharetype = vtlib_purify($_REQUEST["stdtypeFilter"]);
$shared_entities = vtlib_purify($_REQUEST["selectedColumnsStr"]);
//<<<<<<<shared entities>>>>>>>>>

//<<<<<<<columnstototal>>>>>>>>>>
$allKeys = array_keys($_REQUEST);
for ($i=0;$i<count($allKeys);$i++)
{
   $string = substr($allKeys[$i], 0, 3);
   if($string == "cb:")
   {
	   $columnstototal[] = $allKeys[$i];
   }
}
//<<<<<<<columnstototal>>>>>>>>>

//<<<<<<<advancedfilter>>>>>>>>
$json = new Zend_Json();

$advft_criteria = $_REQUEST['advft_criteria'];
$advft_criteria = $json->decode($advft_criteria);

$advft_criteria_groups = $_REQUEST['advft_criteria_groups'];
$advft_criteria_groups = $json->decode($advft_criteria_groups);
//<<<<<<<advancedfilter>>>>>>>>

//<<<<<<<scheduled report>>>>>>>>
$isReportScheduled		= vtlib_purify($_REQUEST['isReportScheduled']);
$selectedRecipients	= vtlib_purify($_REQUEST['selectedRecipientsString']);
$scheduledFormat	= vtlib_purify($_REQUEST['scheduledReportFormat']);
$scheduledInterval	= vtlib_purify($_REQUEST['scheduledIntervalString']);
//<<<<<<<scheduled report>>>>>>>>

if($reportid == "")
{
	$genQueryId = $adb->getUniqueID("vtiger_selectquery");
	if($genQueryId != "")
	{
		$iquerysql = "insert into vtiger_selectquery (QUERYID,STARTINDEX,NUMOFOBJECTS) values (?,?,?)";
		$iquerysqlresult = $adb->pquery($iquerysql, array($genQueryId,0,0));
		$log->info("Reports :: Save->Successfully saved vtiger_selectquery");
		if($iquerysqlresult!=false)
		{
			//<<<<step2 vtiger_selectcolumn>>>>>>>>
			if(!empty($selectedcolumns))
			{
				for($i=0 ;$i<count($selectedcolumns);$i++)
				{
					if(!empty($selectedcolumns[$i])){
						$icolumnsql = "insert into vtiger_selectcolumn (QUERYID,COLUMNINDEX,COLUMNNAME) values (?,?,?)";
						$icolumnsqlresult = $adb->pquery($icolumnsql, array($genQueryId,$i,(decode_html($selectedcolumns[$i]))));
					}
				}
			}
			if($shared_entities != "")
			{
				if($sharetype == "Shared")
				{
					$selectedcolumn = explode(";",$shared_entities);
					for($i=0 ;$i< count($selectedcolumn) -1 ;$i++)
					{
						$temp = split("::",$selectedcolumn[$i]);
						$icolumnsql = "insert into vtiger_reportsharing (reportid,shareid,setype) values (?,?,?)";
						$icolumnsqlresult = $adb->pquery($icolumnsql, array($genQueryId,$temp[1],$temp[0]));
					}
				}
			}
			$log->info("Reports :: Save->Successfully saved vtiger_selectcolumn");
			//<<<<step2 vtiger_selectcolumn>>>>>>>>

			if($genQueryId != "")
			{
				$ireportsql = "insert into vtiger_report (REPORTID,FOLDERID,REPORTNAME,DESCRIPTION,REPORTTYPE,QUERYID,STATE,OWNER,SHARINGTYPE) values (?,?,?,?,?,?,?,?,?)";
				$ireportparams = array($genQueryId, $folderid, $reportname, $reportdescription, $reporttype, $genQueryId,'CUSTOM',$current_user->id,$sharetype);
				$ireportresult = $adb->pquery($ireportsql, $ireportparams);
				$log->info("Reports :: Save->Successfully saved vtiger_report");
				if($ireportresult!=false)
				{
					//<<<<reportmodules>>>>>>>
					$ireportmodulesql = "insert into vtiger_reportmodules (REPORTMODULESID,PRIMARYMODULE,SECONDARYMODULES) values (?,?,?)";
					$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($genQueryId, $pmodule, $smodule));
					$log->info("Reports :: Save->Successfully saved vtiger_reportmodules");
					//<<<<reportmodules>>>>>>>

					//<<<<step3 vtiger_reportsortcol>>>>>>>
					if($sort_by1 != "")
					{
						$sort_by1sql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
						$sort_by1result = $adb->pquery($sort_by1sql, array(1, $genQueryId, $sort_by1, $sort_order1));
						if(CustomReportUtils::IsDateField($sort_by1)){
							$groupByTime1Sql = "INSERT INTO vtiger_reportgroupbycolumn(REPORTID,SORTID,SORTCOLNAME,DATEGROUPBYCRITERIA) values(?,?,?,?)";
							$groupByTime1Res = $adb->pquery($groupByTime1Sql,array($genQueryId,1,$sort_by1,$groupTime1));
						}
					}
					if($sort_by2 != "")
					{
						$sort_by2sql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
						$sort_by2result = $adb->pquery($sort_by2sql, array(2,$genQueryId,$sort_by2,$sort_order2));
						if(CustomReportUtils::IsDateField($sort_by2)){
                            $groupByTime2Sql = "INSERT INTO vtiger_reportgroupbycolumn(REPORTID,SORTID,SORTCOLNAME,DATEGROUPBYCRITERIA) values(?,?,?,?)";
                            $groupByTime2Res = $adb->pquery($groupByTime2Sql,array($genQueryId,2,$sort_by2,$groupTime2));
                        }
					}
					if($sort_by3 != "")
					{
						$sort_by3sql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
						$sort_by3result = $adb->pquery($sort_by3sql, array(3,$genQueryId,$sort_by3,$sort_order3));
						if(CustomReportUtils::IsDateField($sort_by3)){
                            $groupByTime3Sql = "INSERT INTO vtiger_reportgroupbycolumn(REPORTID,SORTID,SORTCOLNAME,DATEGROUPBYCRITERIA) values(?,?,?,?)";
                            $groupByTime3Res = $adb->pquery($groupByTime3Sql,array($genQueryId,3,$sort_by3,$groupTime3));
                        }
					}
					$log->info("Reports :: Save->Successfully saved vtiger_reportsortcol");
					//<<<<step3 vtiger_reportsortcol>>>>>>>

					//<<<<step5 standarfilder>>>>>>>
					$ireportmodulesql = "insert into vtiger_reportdatefilter (DATEFILTERID,DATECOLUMNNAME,DATEFILTER,STARTDATE,ENDDATE) values (?,?,?,?,?)";
					$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($genQueryId, $stdDateFilterField, $stdDateFilter, $startdate, $enddate));
					$log->info("Reports :: Save->Successfully saved vtiger_reportdatefilter");
					//<<<<step5 standarfilder>>>>>>>

					//<<<<step4 columnstototal>>>>>>>
					for ($i=0;$i<count($columnstototal);$i++)
					{
						$ireportsummarysql = "insert into vtiger_reportsummary (REPORTSUMMARYID,SUMMARYTYPE,COLUMNNAME) values (?,?,?)";
						$ireportsummaryresult = $adb->pquery($ireportsummarysql, array($genQueryId, $i, $columnstototal[$i]));
					}
					$log->info("Reports :: Save->Successfully saved vtiger_reportsummary");
					//<<<<step4 columnstototal>>>>>>>

					//<<<<step5 advancedfilter>>>>>>>
					foreach($advft_criteria as $column_index => $column_condition) {

						if(empty($column_condition)) continue;

						$adv_filter_column = $column_condition["columnname"];
						$adv_filter_comparator = $column_condition["comparator"];
						$adv_filter_value = $column_condition["value"];
						$adv_filter_column_condition = $column_condition["columncondition"];
						$adv_filter_groupid = $column_condition["groupid"];

						$column_info = explode(":",$adv_filter_column);
						$moduleFieldLabel = $column_info[2];
						$fieldName = $column_info[3];

						list($module, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
						$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
						$fieldType = null;
						if(!empty($fieldInfo)) {
							$field = WebserviceField::fromArray($adb, $fieldInfo);
							$fieldType = $field->getFieldDataType();
						}

						if($fieldType == 'currency') {
							if($field->getUIType() == '71') {
								$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value, null, true);
							} else {
								$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value);
							}
						}

						$temp_val = explode(",",$adv_filter_value);
						if(($column_info[4] == 'D' || ($column_info[4] == 'T' && $column_info[1] != 'time_start' && $column_info[1] != 'time_end') || ($column_info[4] == 'DT')) && ($column_info[4] != '' && $adv_filter_value != '' ))
						{
							$val = Array();
							for($x=0;$x<count($temp_val);$x++) {
								if(trim($temp_val[$x]) != '') {
									$date = new DateTimeField(trim($temp_val[$x]));
									if($column_info[4] == 'D') {
										$val[$x] = DateTimeField::convertToDBFormat(
												trim($temp_val[$x]));
									} elseif($column_info[4] == 'DT') {
										$val[$x] = $date->getDBInsertDateTimeValue();
									} else {
										$val[$x] = $date->getDBInsertTimeValue();
									}
								}
							}
							$adv_filter_value = implode(",",$val);
						}

						$irelcriteriasql = "insert into vtiger_relcriteria(QUERYID,COLUMNINDEX,COLUMNNAME,COMPARATOR,VALUE,GROUPID,COLUMN_CONDITION) values (?,?,?,?,?,?,?)";
						$irelcriteriaresult = $adb->pquery($irelcriteriasql, array($genQueryId, $column_index, $adv_filter_column, $adv_filter_comparator, $adv_filter_value, $adv_filter_groupid, $adv_filter_column_condition));

						// Update the condition expression for the group to which the condition column belongs
						$groupConditionExpression = '';
						if(!empty($advft_criteria_groups[$adv_filter_groupid]["conditionexpression"])) {
							$groupConditionExpression = $advft_criteria_groups[$adv_filter_groupid]["conditionexpression"];
						}
						$groupConditionExpression = $groupConditionExpression .' '. $column_index .' '. $adv_filter_column_condition;
						$advft_criteria_groups[$adv_filter_groupid]["conditionexpression"] = $groupConditionExpression;
					}

					foreach($advft_criteria_groups as $group_index => $group_condition_info) {

						if(empty($group_condition_info)) continue;
						if(empty($group_condition_info["conditionexpression"])) continue; // Case when the group doesn't have any column criteria

						$irelcriteriagroupsql = "insert into vtiger_relcriteria_grouping(GROUPID,QUERYID,GROUP_CONDITION,CONDITION_EXPRESSION) values (?,?,?,?)";
						$irelcriteriagroupresult = $adb->pquery($irelcriteriagroupsql, array($group_index, $genQueryId, $group_condition_info["groupcondition"], $group_condition_info["conditionexpression"]));
					}
					$log->info("Reports :: Save->Successfully saved vtiger_relcriteria");
					//<<<<step5 advancedfilter>>>>>>>

					//<<<<step7 scheduledReport>>>>>>>
					if($isReportScheduled == 'on' || $isReportScheduled == '1'){
						$scheduleReportSql = 'INSERT INTO vtiger_scheduled_reports (reportid,recipients,schedule,format,next_trigger_time) VALUES (?,?,?,?,?)';
						$adb->pquery($scheduleReportSql, array($genQueryId,$selectedRecipients,$scheduledInterval,$scheduledFormat,date("Y-m-d H:i:s")));
					}
					//<<<<step7 scheduledReport>>>>>>>

				}else
				{
					$errormessage = "<font color='red'><B>Error Message<ul>
						<li><font color='red'>Error while inserting the record</font>
						</ul></B></font> <br>" ;
					echo $errormessage;
					die;
				}
			}
		}else
		{
			$errormessage = "<font color='red'><B>Error Message<ul>
				<li><font color='red'>Error while inserting the record</font>
				</ul></B></font> <br>" ;
			echo $errormessage;
			die;
		}
		echo '<script>window.opener.location.href =window.opener.location.href;self.close();</script>';
	}
}else
{
	if($reportid != "")
	{
		if(!empty($selectedcolumns))
		{
			$idelcolumnsql = "delete from vtiger_selectcolumn where queryid=?";
			$idelcolumnsqlresult = $adb->pquery($idelcolumnsql, array($reportid));
			if($idelcolumnsqlresult != false)
			{
				for($i=0 ;$i<count($selectedcolumns);$i++)
				{
					if(!empty($selectedcolumns[$i])){
						$icolumnsql = "insert into vtiger_selectcolumn (QUERYID,COLUMNINDEX,COLUMNNAME) values (?,?,?)";
						$icolumnsqlresult = $adb->pquery($icolumnsql, array($reportid,$i,(decode_html($selectedcolumns[$i]))));
					}
				}
			}
		}
		$delsharesqlresult = $adb->pquery("DELETE FROM vtiger_reportsharing WHERE reportid=?", array($reportid));
		if($delsharesqlresult != false  && $sharetype=="Shared" && $shared_entities!='')
		{
			$selectedcolumn = explode(";",$shared_entities);
			for($i=0 ;$i< count($selectedcolumn) -1 ;$i++)
			{
				$temp = split("::",$selectedcolumn[$i]);
				$icolumnsql = "INSERT INTO vtiger_reportsharing (reportid,shareid,setype) VALUES (?,?,?)";
				$icolumnsqlresult = $adb->pquery($icolumnsql, array($reportid,$temp[1],$temp[0]));
			}
		}

		//<<<<reportmodules>>>>>>>
		$ireportmodulesql = "UPDATE vtiger_reportmodules SET primarymodule=?,secondarymodules=? WHERE reportmodulesid=?";
		$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($pmodule, $smodule,$reportid));
		$log->info("Reports :: Save->Successfully saved vtiger_reportmodules");
		//<<<<reportmodules>>>>>>>

		$ireportsql = "update vtiger_report set REPORTNAME=?, DESCRIPTION=?, REPORTTYPE=?, SHARINGTYPE=? where REPORTID=?";
		$ireportparams = array($reportname, $reportdescription, $reporttype, $sharetype, $reportid);
		$ireportresult = $adb->pquery($ireportsql, $ireportparams);
		$log->info("Reports :: Save->Successfully saved vtiger_report");

		$idelreportsortcolsql = "delete from vtiger_reportsortcol where reportid=?";
		$idelreportsortcolsqlresult = $adb->pquery($idelreportsortcolsql, array($reportid));
		$delReportGroupTimeSQL = "DELETE FROM vtiger_reportgroupbycolumn WHERE reportid=?";
        $delReportGroupTimeRES = $adb->pquery($delReportGroupTimeSQL,array($reportid));
		
		$log->info("Reports :: Save->Successfully deleted vtiger_reportsortcol");

		if($idelreportsortcolsqlresult!=false)
		{
			//<<<<step3 vtiger_reportsortcol>>>>>>>
			if($sort_by1 != "")
			{
				$sort_by1sql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
				$sort_by1result = $adb->pquery($sort_by1sql, array(1, $reportid, $sort_by1, $sort_order1));
				if(CustomReportUtils::IsDateField($sort_by1)){
                    $groupByTime1Sql = "INSERT INTO vtiger_reportgroupbycolumn(REPORTID,SORTID,SORTCOLNAME,DATEGROUPBYCRITERIA) values(?,?,?,?)";
                    $groupByTime1Res = $adb->pquery($groupByTime1Sql,array($reportid,1,$sort_by1,$groupTime1));
                }
			}
			if($sort_by2 != "")
			{
				$sort_by2sql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
				$sort_by2result = $adb->pquery($sort_by2sql, array(2, $reportid, $sort_by2, $sort_order2));
				if(CustomReportUtils::IsDateField($sort_by2)){
		            $groupByTime2Sql = "INSERT INTO vtiger_reportgroupbycolumn(REPORTID,SORTID,SORTCOLNAME,DATEGROUPBYCRITERIA) values(?,?,?,?)";
		            $groupByTime2Res = $adb->pquery($groupByTime2Sql,array($reportid,2,$sort_by2,$groupTime2));
		        }
			}
			if($sort_by3 != "")
			{
				$sort_by3sql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
				$sort_by3result = $adb->pquery($sort_by3sql, array(3, $reportid, $sort_by3, $sort_order3));
				if(CustomReportUtils::IsDateField($sort_by3)){
                    $groupByTime3Sql = "INSERT INTO vtiger_reportgroupbycolumn(REPORTID,SORTID,SORTCOLNAME,DATEGROUPBYCRITERIA) values(?,?,?,?)";
                    $groupByTime3Res = $adb->pquery($groupByTime3Sql,array($reportid,3,$sort_by3,$groupTime3));
                }
			}
			$log->info("Reports :: Save->Successfully saved vtiger_reportsortcol");
			//<<<<step3 vtiger_reportsortcol>>>>>>>

			$idelreportdatefiltersql = "delete from vtiger_reportdatefilter where datefilterid=?";
			$idelreportdatefiltersqlresult = $adb->pquery($idelreportdatefiltersql, array($reportid));

			//<<<<step5 standarfilder>>>>>>>
			$ireportmodulesql = "insert into vtiger_reportdatefilter (DATEFILTERID,DATECOLUMNNAME,DATEFILTER,STARTDATE,ENDDATE) values (?,?,?,?,?)";
			$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($reportid, $stdDateFilterField, $stdDateFilter, $startdate, $enddate));
			$log->info("Reports :: Save->Successfully saved vtiger_reportdatefilter");
			//<<<<step5 standarfilder>>>>>>>

			//<<<<step4 columnstototal>>>>>>>
			$idelreportsummarysql = "delete from vtiger_reportsummary where reportsummaryid=?";
			$idelreportsummarysqlresult = $adb->pquery($idelreportsummarysql, array($reportid));

			for ($i=0;$i<count($columnstototal);$i++)
			{
				$ireportsummarysql = "insert into vtiger_reportsummary (REPORTSUMMARYID,SUMMARYTYPE,COLUMNNAME) values (?,?,?)";
				$ireportsummaryresult = $adb->pquery($ireportsummarysql, array($reportid, $i, $columnstototal[$i]));
			}
			$log->info("Reports :: Save->Successfully saved vtiger_reportsummary");
			//<<<<step4 columnstototal>>>>>>>


			//<<<<step5 advancedfilter>>>>>>>

			$idelrelcriteriasql = "delete from vtiger_relcriteria where queryid=?";
			$idelrelcriteriasqlresult = $adb->pquery($idelrelcriteriasql, array($reportid));

			$idelrelcriteriagroupsql = "delete from vtiger_relcriteria_grouping where queryid=?";
			$idelrelcriteriagroupsqlresult = $adb->pquery($idelrelcriteriagroupsql, array($reportid));

			foreach($advft_criteria as $column_index => $column_condition) {

				if(empty($column_condition)) continue;

				$adv_filter_column = $column_condition["columnname"];
				$adv_filter_comparator = $column_condition["comparator"];
				$adv_filter_value = $column_condition["value"];
				$adv_filter_column_condition = $column_condition["columncondition"];
				$adv_filter_groupid = $column_condition["groupid"];

				$column_info = explode(":",$adv_filter_column);
				$moduleFieldLabel = $column_info[2];
				$fieldName = $column_info[3];

				list($module, $fieldLabel) = explode('_', $moduleFieldLabel, 2);
				$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
				$fieldType = null;
				if(!empty($fieldInfo)) {
					$field = WebserviceField::fromArray($adb, $fieldInfo);
					$fieldType = $field->getFieldDataType();
				}

				if($fieldType == 'currency') {
					// Some of the currency fields like Unit Price, Total, Sub-total etc of Inventory modules, do not need currency conversion
					if($field->getUIType() == '72') {
						$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value, null, true);
					} else {
						$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value);
					}
				}

				$temp_val = explode(",",$adv_filter_value);
				if(($column_info[4] == 'D' || ($column_info[4] == 'T' && $column_info[1] != 'time_start' && $column_info[1] != 'time_end') || ($column_info[4] == 'DT')) && ($column_info[4] != '' && $adv_filter_value != '' ))
				{
					$val = Array();
					for($x=0;$x<count($temp_val);$x++) {
						if(trim($temp_val[$x]) != '') {
							$date = new DateTimeField(trim($temp_val[$x]));
							if($column_info[4] == 'D') {
								$val[$x] = DateTimeField::convertToDBFormat(
										trim($temp_val[$x]));
							} elseif($column_info[4] == 'DT') {
								$val[$x] = $date->getDBInsertDateTimeValue();
							} else {
								$val[$x] = $date->getDBInsertTimeValue();
							}
						}
					}
					$adv_filter_value = implode(",",$val);
				}

				$irelcriteriasql = "insert into vtiger_relcriteria(QUERYID,COLUMNINDEX,COLUMNNAME,COMPARATOR,VALUE,GROUPID,COLUMN_CONDITION) values (?,?,?,?,?,?,?)";
				$irelcriteriaresult = $adb->pquery($irelcriteriasql, array($reportid, $column_index, $adv_filter_column, $adv_filter_comparator, $adv_filter_value, $adv_filter_groupid, $adv_filter_column_condition));

				// Update the condition expression for the group to which the condition column belongs
				$groupConditionExpression = '';
				if(!empty($advft_criteria_groups[$adv_filter_groupid]["conditionexpression"])) {
					$groupConditionExpression = $advft_criteria_groups[$adv_filter_groupid]["conditionexpression"];
				}
				$groupConditionExpression = $groupConditionExpression .' '. $column_index .' '. $adv_filter_column_condition;
				$advft_criteria_groups[$adv_filter_groupid]["conditionexpression"] = $groupConditionExpression;
			}

			foreach($advft_criteria_groups as $group_index => $group_condition_info) {

				if(empty($group_condition_info)) continue;
				if(empty($group_condition_info["conditionexpression"])) continue; // Case when the group doesn't have any column criteria

				$irelcriteriagroupsql = "insert into vtiger_relcriteria_grouping(GROUPID,QUERYID,GROUP_CONDITION,CONDITION_EXPRESSION) values (?,?,?,?)";
				$irelcriteriagroupresult = $adb->pquery($irelcriteriagroupsql, array($group_index, $reportid, $group_condition_info["groupcondition"], $group_condition_info["conditionexpression"]));
			}
			$log->info("Reports :: Save->Successfully saved vtiger_relcriteria");
			//<<<<step5 advancedfilter>>>>>>>

			//<<<<step7 scheduledReport>>>>>>>
			if($isReportScheduled == 'off' || $isReportScheduled == '0' || $isReportScheduled == '') {
				$deleteScheduledReportSql = "DELETE FROM vtiger_scheduled_reports WHERE reportid=?";
				$adb->pquery($deleteScheduledReportSql, array($reportid));
			} else{
				$checkScheduledResult = $adb->pquery('SELECT 1 FROM vtiger_scheduled_reports WHERE reportid=?', array($reportid));

				if($adb->num_rows($checkScheduledResult) > 0) {
					$scheduledReportSql = 'UPDATE vtiger_scheduled_reports SET recipients=?,schedule=?,format=? WHERE reportid=?';
					$adb->pquery($scheduledReportSql, array($selectedRecipients,$scheduledInterval,$scheduledFormat,$reportid));
				} else {
					$scheduleReportSql = 'INSERT INTO vtiger_scheduled_reports (reportid,recipients,schedule,format,next_trigger_time) VALUES (?,?,?,?,?)';
					$adb->pquery($scheduleReportSql, array($reportid,$selectedRecipients,$scheduledInterval,$scheduledFormat,date("Y-m-d H:i:s")));
				}
			}
			//<<<<step7 scheduledReport>>>>>>>

		}else
		{
			$errormessage = "<font color='red'><B>Error Message<ul>
				<li><font color='red'>Error while inserting the record</font>
				</ul></B></font> <br>" ;
			echo $errormessage;
			die;
		}
	}else
	{
		$errormessage = "<font color='red'><B>Error Message<ul>
			<li><font color='red'>Error while inserting the record</font>
			</ul></B></font> <br>" ;
		echo $errormessage;
		die;
	}
	echo '<script>window.opener.location.href = window.opener.location.href;self.close();</script>';
}
?>