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
$workbook = &new writeexcel_workbook($fname);
$worksheet =& $workbook->addworksheet();

# Set the column width for columns 1, 2, 3 and 4
$worksheet->set_column(0, 3, 15);

# Create a format for the column headings
$header =& $workbook->addformat();
$header->set_bold();
$header->set_size(12);
$header->set_color('blue');

# Write out the data
$reportid = vtlib_purify($_REQUEST["record"]);
$oReport = new Reports($reportid);
$filtercolumn = $_REQUEST['stdDateFilterField'];
$filter = $_REQUEST['stdDateFilter'];
$startdate = getDBInsertDateValue($_REQUEST['startdate']);
$enddate = getDBInsertDateValue($_REQUEST['enddate']);

$oReportRun = new ReportRun($reportid);
$filterlist = $oReportRun->RunTimeFilter($filtercolumn,$filter,$startdate,$enddate);
$arr_val = $oReportRun->GenerateReport("PDF",$filterlist);
$totalxls = $oReportRun->GenerateReport("TOTALXLS",$filterlist);

if(isset($arr_val))
{
	foreach($arr_val[0] as $key=>$value)
	{
		$worksheet->write(0, $count, $key , $header);
		$count = $count + 1;
	}
	$rowcount=1;
	foreach($arr_val as $key=>$array_value)
	{
		$dcount = 0;
		foreach($array_value as $hdr=>$value)
		{
			//$worksheet->write($key+1, $dcount, iconv("UTF-8", "ISO-8859-1", $value));
			$value = decode_html($value);
			$worksheet->write($key+1, $dcount, utf8_decode($value));
			$dcount = $dcount + 1;
		}
		$rowcount++; 
	}

	$rowcount++;
	$count=1;
	if(is_array($totalxls[0])) {
		foreach($totalxls[0] as $key=>$value)
		{
				$chdr=substr($key,-3,3);
			$worksheet->write($rowcount, $count, $mod_strings[$chdr]);
			$count = $count + 1;
		}
	}
	$rowcount++;
	foreach($totalxls as $key=>$array_value)
	{
			$dcount = 1;
			foreach($array_value as $hdr=>$value)
			{
					//$worksheet->write($key+1, $dcount, iconv("UTF-8", "ISO-8859-1", $value));
					if ($dcount==1)
							$worksheet->write($key+$rowcount, 0, utf8_decode(substr($hdr,0,strlen($hdr)-4)));
				$value = decode_html($value);
					$worksheet->write($key+$rowcount, $dcount, utf8_decode($value));
					$dcount = $dcount + 1;
			}
	} 
}

$workbook->close();

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
