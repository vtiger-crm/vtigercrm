<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

global $app_strings;
global $currentModule,$image_path,$theme,$adb, $current_user;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

require_once("data/Tracker.php");
require_once('modules/Vtiger/layout_utils.php');
require_once('include/utils/utils.php');

$log = LoggerManager::getLogger('Activity_Reminder');

$cbaction = $_REQUEST['cbaction'];
$cbmodule = $_REQUEST['cbmodule'];
$cbrecord = $_REQUEST['cbrecord'];

if($cbaction == 'POSTPONE') {
	if(isset($cbmodule) && isset($cbrecord)) {
		$reminderid = $_REQUEST['cbreminderid'];
		if(!empty($reminderid) ) {
			unset($_SESSION['next_reminder_time']);
			$reminder_query = "UPDATE vtiger_activity_reminder_popup set status = 0 WHERE reminderid = ? AND semodule = ? AND recordid = ?";
			$adb->pquery($reminder_query, array($reminderid, $cbmodule, $cbrecord));
			echo ":#:SUCCESS";
		} else {
			echo ":#:FAILURE";			
		}		
	}
}

?>
