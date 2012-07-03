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
require_once('Smarty_setup.php');
require_once("modules/Reports/ReportRun.php");
require_once("modules/Reports/Reports.php");

global $app_strings;
global $mod_strings;
$oPrint_smarty=new vtigerCRM_Smarty;
$reportid = vtlib_purify($_REQUEST["record"]);
$oReport = new Reports($reportid);
$filtercolumn = $_REQUEST["stdDateFilterField"];
$filter = $_REQUEST["stdDateFilter"];
$oReportRun = new ReportRun($reportid);

$startdate = DateTimeField::convertToDBFormat($_REQUEST["startdate"]);//Convert the user date format to DB date format
$enddate = DateTimeField::convertToDBFormat($_REQUEST["enddate"]);//Convert the user date format to DB date format
$filterlist = $oReportRun->RunTimeFilter($filtercolumn,$filter,$startdate,$enddate);

$arr_values = $oReportRun->GenerateReport("PRINT",$filterlist);
$total_report = $oReportRun->GenerateReport("PRINT_TOTAL",$filterlist);
$oPrint_smarty->assign("COUNT",$arr_values[1]);
$oPrint_smarty->assign("APP",$app_strings);
$oPrint_smarty->assign("MOD",$mod_strings);
$oPrint_smarty->assign("REPORT_NAME",$oReport->reportname);
$oPrint_smarty->assign("PRINT_CONTENTS",$arr_values[0]);
$oPrint_smarty->assign("TOTAL_HTML",$total_report);
$oPrint_smarty->display("PrintReport.tpl");
?>
