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
ini_set('max_execution_time','1800');
require_once("modules/Reports/ReportRun.php");
require_once("modules/Reports/Reports.php");
//require('include/fpdf/fpdf.php');
require('include/tcpdf/tcpdf.php');
$language = $_SESSION['authenticated_user_language'].'.lang.php';
require_once("include/language/$language");
$reportid = vtlib_purify($_REQUEST["record"]);
$oReport = new Reports($reportid);
//Code given by Csar Rodrguez for Rwport Filter
$filtercolumn = $_REQUEST["stdDateFilterField"];
$filter = $_REQUEST["stdDateFilter"];
$oReportRun = new ReportRun($reportid);

$startdate = ($_REQUEST['startdate']);
$enddate = ($_REQUEST['enddate']);
if(!empty($startdate) && !empty($enddate) && $startdate != "0000-00-00" && 
		$enddate != "0000-00-00" ) {
	$date = new DateTimeField($_REQUEST['startdate']);
	$endDate = new DateTimeField($_REQUEST['enddate']);
	$startdate = $date->getDBInsertDateValue();//Convert the user date format to DB date format
	$enddate = $endDate->getDBInsertDateValue();//Convert the user date format to DB date format
}

$filterlist = $oReportRun->RunTimeFilter($filtercolumn,$filter,$startdate,$enddate);

$pdf = $oReportRun->getReportPDF($filterlist);
$pdf->Output('Reports.pdf','D');

exit();
?>
