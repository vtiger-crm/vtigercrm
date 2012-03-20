<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

$module_update_step = vtlib_purify($_REQUEST['module_update']);

require_once('Smarty_setup.php');
require_once('vtlib/Vtiger/Package.php');
require_once('vtlib/Vtiger/Language.php');

global $mod_strings,$app_strings,$theme;
$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", "themes/$theme/images/");

global $modulemanager_uploaddir; // Defined in modules/Settings/ModuleManager.php

$target_modulename = $_REQUEST['target_modulename'];

if($module_update_step == 'Step2') {
	if(!is_dir($modulemanager_uploaddir)) mkdir($modulemanager_uploaddir);
	$uploadfile = "usermodule_". time() . ".zip";
	$uploadfilename = "$modulemanager_uploaddir/$uploadfile";	
	checkFileAccess($modulemanager_uploaddir);

	if(!move_uploaded_file($_FILES['module_zipfile']['tmp_name'], $uploadfilename)) {
		$smarty->assign("MODULEUPDATE_FAILED", "true");
	} else {
		$package = new Vtiger_Package();
		$moduleupdate_name = $package->getModuleNameFromZip($uploadfilename);

		if($moduleupdate_name == null) {
			$smarty->assign("MODULEUPDATE_FAILED", "true");
			$smarty->assign("MODULEUPDATE_FILE_INVALID", "true");
		} else if(!$package->isLanguageType() && ($moduleupdate_name != $target_modulename)) {
			$smarty->assign("MODULEUPDATE_FAILED", "true");
			$smarty->assign("MODULEUPDATE_NAME_MISMATCH", "true");
		} else if($package->isLanguageType() && (trim($package->xpath_value('prefix')) != $target_modulename)) {
			$smarty->assign("MODULEUPDATE_FAILED", "true");
			$smarty->assign("MODULEUPDATE_NAME_MISMATCH", "true");
		} else {

			$moduleupdate_dep_vtversion = $package->getDependentVtigerVersion();
			$moduleupdate_license = $package->getLicense();
			$moduleupdate_version = $package->getVersion();

			if(!$package->isLanguageType()) {
				$moduleInstance = Vtiger_Module::getInstance($moduleupdate_name);
				$moduleupdate_exists=($moduleInstance)? "true" : "false";			
				$moduleupdate_dir_name="modules/$moduleupdate_name";				
				$moduleupdate_dir_exists= (is_dir($moduleupdate_dir_name)? "true" : "false");

				$smarty->assign("MODULEUPDATE_CUR_VERSION", ($moduleInstance? $moduleInstance->version : ''));
				$smarty->assign("MODULEUPDATE_NOT_EXISTS", !($moduleupdate_exists));
				$smarty->assign("MODULEUPDATE_DIR", $moduleupdate_dir_name);	
				$smarty->assign("MODULEUPDATE_DIR_NOT_EXISTS", !($moduleupdate_dir_exists));

				// If version is matching, dis-allow migration
				if(version_compare($moduleupdate_version, $moduleInstance->version, '=')) {
					$smarty->assign("MODULEUPDATE_FAILED", "true");
					$smarty->assign("MODULEUPDATE_SAME_VERSION", "true");
				}
			}

			$smarty->assign("MODULEUPDATE_FILE", $uploadfile);
			$smarty->assign("MODULEUPDATE_TYPE", $package->type());
			$smarty->assign("MODULEUPDATE_NAME", $moduleupdate_name);			
			$smarty->assign("MODULEUPDATE_DEP_VTVERSION", $moduleupdate_dep_vtversion);
			$smarty->assign("MODULEUPDATE_VERSION", $moduleupdate_version);
			$smarty->assign("MODULEUPDATE_LICENSE", $moduleupdate_license);
		}
	}
} else if($module_update_step == 'Step3') {
	$uploadfile = $_REQUEST['module_import_file'];
	$uploadfilename = "$modulemanager_uploaddir/$uploadfile";
	checkFileAccess($uploadfilename);

	//$overwritedir = ($_REQUEST['module_dir_overwrite'] == 'true')? true : false;
	$overwritedir = false; // Disallowing overwrites through Module Manager UI

	$updatetype = $_REQUEST['module_update_type'];
	if(strtolower($updatetype) == 'language') {
		$package = new Vtiger_Language();
	} else {
		$package = new Vtiger_Package();
	}
	$Vtiger_Utils_Log = true;
	// NOTE: Import function will be called from Smarty to capture the log cleanly.
	//$package->update($moduleInstance, $uploadfilename);
	//unlink($uploadfilename);
	$smarty->assign("MODULEUPDATE_PACKAGE", $package);
	$smarty->assign("MODULEUPDATE_TARGETINSTANCE", Vtiger_Module::getInstance($target_modulename));
	$smarty->assign("MODULEUPDATE_PACKAGE_FILE", $uploadfilename);
}

$smarty->display("Settings/ModuleManager/ModuleUpdate$module_update_step.tpl");

?>