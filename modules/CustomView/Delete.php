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

$cvid = vtlib_purify($_REQUEST["record"]);
$module = vtlib_purify($_REQUEST["dmodule"]);
$smodule = vtlib_purify($_REQUEST["smodule"]);
$parenttab = getParentTab();

if(isset($cvid) && $cvid != '')
{
	$deletesql = "delete from vtiger_customview where cvid =?";
	$deleteresult = $adb->pquery($deletesql, array($cvid));
	$_SESSION['lvs'][$module]["viewname"] = '';
}
if(isset($smodule) && $smodule != '')
{
	$smodule_url = "&smodule=".$smodule;
}

header("Location: index.php?action=ListView&parenttab=$parenttab&module=$module".$smodule_url);
?>