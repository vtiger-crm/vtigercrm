<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/database/PearDatabase.php');

global $adb;

if(isset($_REQUEST['record']))
{
	$recordid = $_REQUEST['record'];
	if(!isset($_REQUEST['starred'])) $starred = 0;
	$starred = $_REQUEST['starred'];
	
	$sSQL = "update vtiger_rss set starred=? where vtiger_rssid=?";
	$result = $adb->pquery($sSQL, array($starred, $recordid));
}
header("Location: index.php?module=Rss&action=index");
?>