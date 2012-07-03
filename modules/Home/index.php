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
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Home/index.php,v 1.28 2005/04/20 06:57:47 samk Exp $
 * Description:  Main file for the Home module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
require_once('include/home.php');
require_once('Smarty_setup.php');
require_once('modules/Home/HomeBlock.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/CommonUtils.php');
require_once('include/freetag/freetag.class.php');
require_once 'modules/Home/HomeUtils.php';

global $app_strings, $app_list_strings, $mod_strings;
global $adb, $current_user;
global $theme;
global $current_language;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$homeObj=new Homestuff;

// Performance Optimization
$tabrows = vtlib_prefetchModuleActiveInfo();
// END

//$query="select name,tabid from vtiger_tab where tabid in (select distinct(tabid) from vtiger_field where tabid <> 29 and tabid <> 16 and tabid <>10) order by name";

// Performance Optimization: Re-written to ignore extension and inactive modules
$modulenamearr = Array();
foreach($tabrows as $resultrow) {
	if($resultrow['isentitytype'] != '0') {
		// Eliminate: Events, Emails
		if($resultrow['tabid'] == '16' || $resultrow['tabid'] == '10' || $resultrow['name'] == 'Webmails') {
			continue;
		}
		$modName=$resultrow['name'];
		if(isPermitted($modName,'DetailView') == 'yes' && vtlib_isModuleActive($modName)){
			$modulenamearr[$modName]=array($resultrow['tabid'],$modName);
		}	
	}
}
ksort($modulenamearr); // We avoided ORDER BY in Query (vtlib_prefetchModuleActiveInfo)!
// END


//Security Check done for RSS and Dashboards
$allow_rss='no';
$allow_dashbd='no';
$allow_report='no';
if(isPermitted('Rss','DetailView') == 'yes' && vtlib_isModuleActive('Rss')){
	$allow_rss='yes';
}	
if(isPermitted('Dashboard','DetailView') == 'yes' && vtlib_isModuleActive('Dashboard')){
	$allow_dashbd='yes';
}

if(isPermitted('Reports','DetailView') == 'yes' && vtlib_isModuleActive('Reports')){
	$allow_report='yes';
}

$homedetails = $homeObj->getHomePageFrame();
$maxdiv = sizeof($homedetails)-1;
$user_name = $current_user->column_fields['user_name'];
$buttoncheck['Calendar'] = isPermitted('Calendar','index');
$freetag = new freetag();
$numberofcols = getNumberOfColumns();

$smarty->assign("CHECK",$buttoncheck);
if(vtlib_isModuleActive('Calendar')){
	$smarty->assign("CALENDAR_ACTIVE","yes");
}
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("MODULE",'Home');
$smarty->assign("CATEGORY",getParenttab('Home'));
$smarty->assign("CURRENTUSER",$user_name);
$smarty->assign("ALL_TAG",$freetag->get_tag_cloud_html("",$current_user->id));
$smarty->assign("MAXLEN",$maxdiv);
$smarty->assign("ALLOW_RSS",$allow_rss);
$smarty->assign("ALLOW_DASH",$allow_dashbd);
$smarty->assign("ALLOW_REPORT",$allow_report);
$smarty->assign("HOMEFRAME",$homedetails);
$smarty->assign("MODULE_NAME",$modulenamearr);
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("LAYOUT", $numberofcols);
$widgetBlockSize = PerformancePrefs::getBoolean('HOME_PAGE_WIDGET_GROUP_SIZE', 12);
$smarty->assign('widgetBlockSize', $widgetBlockSize);

// First time login check
include_once 'modules/Users/LoginHistory.php';
$accept_login_delay_seconds = 5*60; // (use..5*60 for 5 min) to overcome redirection post authentication
$smarty->assign('FIRST_TIME_LOGIN', LoginHistory::firstTimeLoggedIn($current_user->user_name, $accept_login_delay_seconds));
// End

$smarty->display("Home/Homestuff.tpl");

?>
