<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

global $adb;

$summaryReportQuery = "SELECT * FROM vtiger_report
                        INNER JOIN vtiger_reportsortcol ON vtiger_report.reportid = vtiger_reportsortcol.reportid
                        WHERE vtiger_report.reporttype='summary' and vtiger_reportsortcol.columnname!='none'";

$reportRes = $adb->pquery($summaryReportQuery, array());

$selectElement = '<select name=selreportchart id=selreportchart_id class="detailedViewTextBox" onfocus="this.className=\'detailedViewTextBoxOn\'" onblur="this.className=\'detailedViewTextBox\'" style="width:60%">';

$num_rows = $adb->num_rows($reportRes);
for ($i = 0; $i < $num_rows; $i++) {
	$reportId = $adb->query_result($reportRes, $i, 'reportid');
	$reportName = $adb->query_result($reportRes, $i, 'reportname');
	$selectElement .= '<option value="' . $reportId . '">' . $reportName . '</option>';
}
$selectElement.='</select>';
echo $selectElement;
?>
