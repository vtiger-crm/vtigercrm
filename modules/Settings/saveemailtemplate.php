<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/utils/utils.php');

global $log;
$db = PearDatabase::getInstance();
$folderName = vtlib_purify($_REQUEST["foldername"]);
$templateName = from_html($_REQUEST["templatename"]);
$templateid = vtlib_purify($_REQUEST["templateid"]);
$description = from_html($_REQUEST["description"]);
$subject = from_html($_REQUEST["subject"]);
$body = fck_from_html($_REQUEST["body"]);

if(isset($templateid) && $templateid !='')
{
	$log->info("the templateid is set");  
	$sql = "update vtiger_emailtemplates set foldername =?, templatename =?, subject =?, description =?, body =? where templateid =?";
	$params = array($folderName, $templateName, $subject, $description, $body, $templateid);
	$adb->pquery($sql, $params);
 
	$log->info("about to invoke the detailviewemailtemplate file");  
	header("Location:index.php?module=Settings&action=detailviewemailtemplate&parenttab=Settings&templateid=".$templateid);
}
else
{
	$templateid = $db->getUniqueID('vtiger_emailtemplates');
	$sql = "insert into vtiger_emailtemplates values (?,?,?,?,?,?,?)";
	$params = array($folderName, $templateName, $subject, $description, $body, 0, $templateid);
	$adb->pquery($sql, $params);

	 $log->info("added to the db the emailtemplate");
	header("Location:index.php?module=Settings&action=detailviewemailtemplate&parenttab=Settings&templateid=".$templateid);
}
?>