<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('modules/Rss/Rss.php');
require_once('include/logging.php');
global $mod_strings;

if (isset($_REQUEST["rssurl"])) $newRssUrl = $_REQUEST["rssurl"];

$oRss = new vtigerRSS();
if($oRss->setRSSUrl($newRssUrl)) {
	if($oRss->saveRSSUrl($newRssUrl) == false) {
		echo $mod_strings['UNABLE_TO_SAVE'];
	}
} else {
	echo $mod_strings['INVALID_RSS_URL'];
}
?>