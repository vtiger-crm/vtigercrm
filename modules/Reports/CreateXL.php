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
global $php_max_execution_time;
set_time_limit($php_max_execution_time);

require_once("include/php_writeexcel/class.writeexcel_workbook.inc.php");
require_once("include/php_writeexcel/class.writeexcel_worksheet.inc.php");
require_once("modules/Reports/ReportRun.php");
require_once("modules/Reports/Reports.php");

global $tmp_dir, $root_directory;

$fname = tempnam($root_directory.$tmp_dir, "merge2.xls");

# Write out the data
$reportid = vtlib_purify($_REQUEST["record"]);
$oReport = new Reports($reportid);
$filtercolumn = $_REQUEST['stdDateFilterField'];
$startdate = ($_REQUEST['startdate']);
$enddate = ($_REQUEST['enddate']);
if(!empty($startdate) && !empty($enddate) && $startdate != "0000-00-00" && 
		$enddate != "0000-00-00" ) {
	$filter = $_REQUEST['stdDateFilter'];
	$date = new DateTimeField($_REQUEST['startdate']);
	$endDate = new DateTimeField($_REQUEST['enddate']);
	$startdate = $date->getDBInsertDateValue();//Convert the user date format to DB date format
	$enddate = $endDate->getDBInsertDateValue();//Convert the user date format to DB date format
}
$oReportRun = new ReportRun($reportid);
$filterlist = $oReportRun->RunTimeFilter($filtercolumn,$filter,$startdate,$enddate);

$oReportRun->writeReportToExcelFile($fname, $filterlist);

if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
{
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
}
header("Content-Type: application/x-msexcel");
header("Content-Length: ".@filesize($fname));
header('Content-disposition: attachment; filename="Reports.xls"');
$fh=fopen($fname, "rb");
fpassthru($fh);
//unlink($fname);
?>
