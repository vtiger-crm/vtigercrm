<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
echo header('Pragma: public');
echo header('Expires: 0');
echo header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
echo header('Cache-Control: private', false);
ini_set("include_path","../../");

require_once 'include/utils/utils.php';

include_once('../../config.inc.php');
global $application_unique_key;
if($_REQUEST['app_key'] != $application_unique_key) {
	exit;
}

global $adb;

$crmid = $_REQUEST['record'];
$mailid = $_REQUEST['mailid'];
$adb->pquery("INSERT INTO vtiger_email_access(crmid, mailid, accessdate, accesstime) VALUES(?,?,?,?)", array($crmid, $mailid, date('Y-m-d'), date('H:i:s')));

$result = $adb->pquery("select count(*) as count from vtiger_email_access where crmid=? and mailid=?",array($crmid, $mailid));
$count = $adb->query_result($result,0,'count');

$result = $adb->pquery("select * from vtiger_email_track where crmid=? and mailid=?", array($crmid, $mailid));
if($adb->num_rows($result)>0){
	$adb->pquery("update vtiger_email_track set access_count=? where crmid=? and mailid=?", array($count, $crmid, $mailid));
} else {
	$adb->pquery("insert into vtiger_email_track(crmid,mailid,access_count) values(?,?,?)", array($crmid, $mailid, 1));
}

?>