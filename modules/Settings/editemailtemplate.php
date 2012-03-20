<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/database/PearDatabase.php');
require_once('include/CustomFieldUtil.php');

global $mod_strings;
global $app_strings;
global $theme;
global $current_language;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
global $log,$default_charset;

$mode = 'create';

if(isset($_REQUEST['templateid']) && $_REQUEST['templateid']!='')
{
	$mode = 'edit';
	$templateid = $_REQUEST['templateid'];
	 $log->debug("the templateid is set to the value ".$templateid);
}
$sql = "select * from vtiger_emailtemplates where templateid=?";
$result = $adb->pquery($sql, array($templateid));
$emailtemplateResult = str_replace('"','&quot;',$adb->fetch_array($result));
$smod_strings = return_module_language($current_language,'Settings');

//To get Email Template variables -- Pavani
$allOptions=getEmailTemplateVariables();
$smarty = new vtigerCRM_smarty;

$smarty->assign("UMOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme_path);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("MOD", $smod_strings);
$smarty->assign("FOLDERNAME", $emailtemplateResult["foldername"]);
$smarty->assign("TEMPLATENAME", $emailtemplateResult["templatename"]);
$smarty->assign("TEMPLATEID", $emailtemplateResult["templateid"]);
$smarty->assign("DESCRIPTION", $emailtemplateResult["description"]);
$smarty->assign("SUBJECT", $emailtemplateResult["subject"]);
$smarty->assign("BODY", $emailtemplateResult["body"]);
$smarty->assign("MODULE", 'Settings');
$smarty->assign("PARENTTAB", getParentTab());
$smarty->assign("EMODE", $mode);
$smarty->assign("ALL_VARIABLES", $allOptions);

$smarty->display("CreateEmailTemplate.tpl");
?>