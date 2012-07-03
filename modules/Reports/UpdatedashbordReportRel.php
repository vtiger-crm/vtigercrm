<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once 'include/home.php';
global $adb, $log;

$reportid = vtlib_purify($_REQUEST['reportid']);
$windowtitle = vtlib_purify($_REQUEST['windowtitle']);
$charttype = vtlib_purify($_REQUEST['charttype']);

$homeObj = new Homestuff();
$homeObj->stufftitle = $windowtitle;
$homeObj->stufftype = "ReportCharts";
$homeObj->selreport = $reportid;
$homeObj->selreportcharttype = $charttype;
$homeObj->addStuff();
?>
