<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
require_once('modules/Settings/MailScanner/core/MailScannerInfo.php');
require_once('modules/Settings/MailScanner/core/MailBox.php');
require_once('Smarty_setup.php');

global $app_strings, $mod_strings, $currentModule, $theme, $current_language;

$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH","themes/$theme/images/");

$scannername = $_REQUEST['scannername'];
$scannerinfo = new Vtiger_MailScannerInfo($scannername);

$mailbox = new Vtiger_MailBox($scannerinfo);
$isconnected = $mailbox->connect();
if($isconnected) {
	$folders = $mailbox->getFolders();
	$scannerinfo->updateFolderInfo($folders);
}

$smarty->assign("SCANNERINFO", $scannerinfo->getAsMap());
$smarty->assign("FOLDERINFO", $scannerinfo->getFolderInfo());

$smarty->display('MailScanner/MailScannerFolder.tpl');

?>
