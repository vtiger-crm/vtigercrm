<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('vtlib/Vtiger/Feed/Parser.php');

global $app_strings, $mod_strings, $theme, $currentModule;

$ftimeout = 60;
$fparser = new Vtiger_Feed_Parser();
$fparser->vt_dofetch('http://www.vtiger.com/products/crm/newsfeed.php', $ftimeout);
$items = $fparser->get_items();
$NEWSLIST = Array();
foreach($items as $item) {
	$NEWSLIST[] = $item;
}

require_once('Smarty_setup.php');
$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", "themes/$theme/images/");
$smarty->assign('NEWSLIST', $NEWSLIST);
$smarty->display("HomeNews.tpl");
?>

