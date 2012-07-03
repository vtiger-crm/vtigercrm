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
global $adb;

$cvid = vtlib_purify($_REQUEST["cvid"]);
$cvmodule = vtlib_purify($_REQUEST["cvmodule"]);
$mode = $_REQUEST["mode"];
$subject = $_REQUEST["subject"];
$body = $_REQUEST["body"];

if($cvid != "")
{
	if($mode == "new")
	{
		$customactionsql = "insert into vtiger_customaction(cvid,subject,module,content) values (?,?,?,?)";
		$customactionparams = array($cvid, $subject, $cvmodule, $body);
		$customactionresult = $adb->pquery($customactionsql, $customactionparams);
		if($customactionresult == false)
		{
			include('modules/Vtiger/header.php');
			$errormessage = "<font color='red'><B>Error Message<ul>
				<li><font color='red'>Error while inserting the record</font>
				</ul></B></font> <br>" ;
			echo $errormessage;

		}

	}elseif($mode == "edit")
	{
		$updatecasql = "update vtiger_customaction set subject=?, content=? where cvid=?";
		$updatecaresult = $adb->pquery($updatecasql, array($subject, $body, $cvid));
		if($updatecaresult == false)
		{
			include('modules/Vtiger/header.php');
			$errormessage = "<font color='red'><B>Error Message<ul>
				<li><font color='red'>Error while inserting the record</font>
				</ul></B></font> <br>" ;
			echo $errormessage;
		}
	}
}
header("Location: index.php?action=index&module=$cvmodule&viewname=$cvid");
?>