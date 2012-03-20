<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once("data/Tracker.php");
require_once('Smarty_setup.php');
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/utils/utils.php');
require_once('modules/Rss/Rss.php');
global $app_strings;
global $app_list_strings;
global $mod_strings;

$current_module_strings = return_module_language($current_language, 'Rss');
$log = LoggerManager::getLogger('rss_list');

global $currentModule;
global $image_path;
global $theme;
global $cache_dir;
// focus_list is the means of passing data to a ListView.
global $focus_list;
global $adb;

$oRss = new vtigerRSS();
if(isset($_REQUEST['folders']) && $_REQUEST['folders'] == 'true')
{
	require_once("modules/".$currentModule."/Forms.php");
	echo get_rssfeeds_form();
	die;
}
if(isset($_REQUEST['record']))
{
	$recordid = vtlib_purify($_REQUEST['record']);
}

$rss_form = new vtigerCRM_Smarty;
$rss_form->assign("MOD", $mod_strings);
$rss_form->assign("APP", $app_strings);
$rss_form->assign("THEME",$theme);
$rss_form->assign("IMAGE_PATH",$image_path);
$rss_form->assign("MODULE", $currentModule);
$rss_form->assign("CATEGORY", getParenttab());

//<<<<<<<<<<<<<<lastrss>>>>>>>>>>>>>>>>>>//
//$url = 'http://forums/rss.php?name=forums&file=rss';
//$url = 'http://forums/weblog_rss.php?w=202';
if(isset($_REQUEST[record]))
{
    $recordid = vtlib_purify($_REQUEST['record']);
	$url = $oRss->getRssUrlfromId($recordid);
	if($oRss->setRSSUrl($url))
	{
        	$rss_html = $oRss->getSelectedRssHTML($recordid);
	}else
	{
        	$rss_html = "<strong>".$mod_strings['LBL_ERROR_MSG']."</strong>";
	}
	$rss_form->assign("TITLE",gerRssTitle($recordid));
	$rss_form->assign("ID",$recordid);
}else
{
	$rss_form->assign("TITLE",gerRssTitle());
	$rss_html = $oRss->getStarredRssHTML();
	$query = "select rssid from vtiger_rss where starred=1";
	$result = $adb->pquery($query, array());
	$recordid = $adb->query_result($result,0,'rssid');
	$rss_form->assign("ID",$recordid);
	$rss_form->assign("DEFAULT",'yes');
}
if($currentModule == "Rss")
{
	require_once("modules/".$currentModule."/Forms.php");
	if (function_exists('get_rssfeeds_form'))
	{
		$rss_form->assign("RSSFEEDS", get_rssfeeds_form());
	}
}
$rss_form->assign("RSSDETAILS",$rss_html);
//<<<<<<<<<<<<<<lastrss>>>>>>>>>>>>>>>>>>//
if(isset($_REQUEST['directmode']) && $_REQUEST['directmode'] == 'ajax')
	$rss_form->display("RssFeeds.tpl");
else
	$rss_form->display("Rss.tpl");
?>
