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
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/Rss/Rss.php');

global $mod_strings;
$log = LoggerManager::getLogger('rss_save');

if(isset($_REQUEST["record"]))
{
	global $adb;
	$query = 'update vtiger_rss set starred=0';
	$adb->pquery($query, array());
	$query = 'update vtiger_rss set starred=1 where rssid =?'; 
	$adb->pquery($query, array($_REQUEST["record"]));
	echo vtlib_purify($_REQUEST["record"]);
}
elseif(isset($_REQUEST["rssurl"]))
{
	$newRssUrl = str_replace('##amp##','&',$_REQUEST["rssurl"]);
	$setstarred = 0;
	$oRss = new vtigerRSS();
	if($oRss->setRSSUrl($newRssUrl))
	{
			$result = $oRss->saveRSSUrl($newRssUrl,$setstarred);
        	if($result == false)
        	{
				echo $mod_strings['UNABLE_TO_SAVE'] ;
        	}else
        	{
				echo $result;
        	}
	}else
	{
		echo $mod_strings['NOT_A_VALID'];

	}
}

?>
