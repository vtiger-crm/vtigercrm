<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
	
/*********************************************************************************
 * $Header: /var/cvs/vtigercrm_aegon/modules/Home/NewBlock.php,v 1.2 2007/03/01 17:47:16 jerrydgeorge Exp $
 * Description:  Main file for the Home module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

global $mod_strings;
global $app_strings;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

require_once('include/home.php');
require_once('Smarty_setup.php');

$stuffid = $_REQUEST[stuffid];
$stufftype = $_REQUEST[stufftype];

$homeObj=new Homestuff();
$smarty=new vtigerCRM_Smarty;

$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$homeselectedframe = $homeObj->getSelectedStuff($stuffid,$stufftype);

$smarty->assign("tablestuff",$homeselectedframe);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->display("Home/MainHomeBlock.tpl");

?>
