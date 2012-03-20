<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('modules/CustomView/CustomView.php');
global $adb,$log;

$cvid = $_REQUEST["record"];
$status = $_REQUEST["status"];
$module = $_REQUEST["dmodule"];
$now_action = $_REQUEST["action"];
if(isset($cvid) && $cvid != '')
{
	$oCustomView = new CustomView($module);
	if($oCustomView->isPermittedCustomView($cvid,$now_action,$oCustomView->customviewmodule) == 'yes')
	{
		$updateStatusSql = "update vtiger_customview set status=? where cvid=? and entitytype=?";
		$updateresult = $adb->pquery($updateStatusSql, array($status, $cvid, $module));
		if(!$updateresult)
			echo ':#:FAILURE:#:';
		else 
			echo ':#:SUCCESS:#:' . $module . ':#:';
	}
	else
	{
		global $theme, $app_strings;
		echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

			<table border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tbody><tr>
			<td rowspan='2' width='11%'><img src='".vtiger_imageurl('denied.gif', $theme)."' ></td>
			<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
				<span class='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
			</tr>
			<tr>
			<td class='small' align='right' nowrap='nowrap'>
			<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>
			</td>
			</tr>
			</tbody></table>
			</div>";
		echo "</td></tr></table>";
		exit;
	}
}
?>