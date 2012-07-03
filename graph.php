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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/graph.php,v 1.1 2004/08/18 12:22:54 gjayakrishnan Exp $
 * Description: Main file and starting point for the application.  Calls the 
 * theme header and footer files defined for the user as well as the module as 
 * defined by the input parameters.
 ********************************************************************************/
 
require_once('config.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');

global $default_language;

$log =& LoggerManager::getLogger('graph');
$log->debug($_REQUEST);

if (($module == 'Users' || $module == 'Home' || $module == 'uploads') && $_REQUEST['parenttab'] 
		!= 'Settings') {
	$skipSecurityCheck = true;
}

require_once('include/utils/UserInfoUtil.php');
if (preg_match('/Ajax/', $action)) {
	if ($_REQUEST['ajxaction'] == 'LOADRELATEDLIST') {
		$now_action = 'DetailView';
	} else {
		$now_action = vtlib_purify($_REQUEST['file']);
	}
} else {
	$now_action = $action;
}

if (isset($_REQUEST['record']) && $_REQUEST['record'] != '') {
	$display = isPermitted($module, $now_action, $_REQUEST['record']);
} else {
	$display = isPermitted($module, $now_action);
}

$currentModule = $module;
$module = $_REQUEST['module'];

$current_user = new Users();

if($use_current_login) {
	//getting the current user info from flat file
	$result = $current_user->retrieveCurrentUserInfoFromFile($_SESSION['authenticated_user_id']);
}

$use_current_login = false;
if (isset($_SESSION["authenticated_user_id"]) && (isset($_SESSION["app_unique_key"]) && 
		$_SESSION["app_unique_key"] == $application_unique_key)) {
	$use_current_login = true;
}

// if the language is not set yet, then set it to the default language.
if (isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] 
		!= '') {
	$current_language = $_SESSION['authenticated_user_language'];
} else {
	if (!empty($current_user->language)) {
		$current_language = $current_user->language;
	} else {
		$current_language = $default_language;
	}
}

if (isset($_SESSION['vtiger_authenticated_user_theme']) && 
		$_SESSION['vtiger_authenticated_user_theme'] != '') {
	$theme = $_SESSION['vtiger_authenticated_user_theme'];
} else {
	if (!empty($current_user->theme)) {
		$theme = $current_user->theme;
	} else {
		$theme = $default_theme;
	}
}

$app_strings = return_application_language($current_language);
$app_list_strings = return_app_list_strings_language($current_language);
$mod_strings = return_module_language($current_language, $currentModule);

if ($display == "no" || $use_current_login !== true) {
	echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='" . vtiger_imageurl('denied.gif', $theme) . "' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>			   	
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>								   						     </td>
		</tr>
		</tbody></table> 
		</div>";
	echo "</td></tr></table>";
}
// vtlib customization: Check if module has been de-activated
else if (!vtlib_isModuleActive($currentModule)) {
	echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='" . vtiger_imageurl('denied.gif', $theme) . "' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>$currentModule $app_strings[VTLIB_MOD_NOT_ACTIVE]</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>			   	
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>								   						     </td>
		</tr>
		</tbody></table> 
		</div>";
	echo "</td></tr></table>";
}

if(isset($_REQUEST['action']) && isset($_REQUEST['module']))
{
	$action = $_REQUEST['action']; 
	$current_module_file = 'modules/'.$_REQUEST['module'].'/'.$action.'.php';
	$current_module = $_REQUEST['module'];
}
elseif(isset($_REQUEST['module']))
{
	$current_module = $_REQUEST['module'];
	$current_module_file = 'modules/'.$_REQUEST['module'].'/Charts.php';
}
else {
    exit();
}

$current_language = $default_language;
if(isset($_REQUEST['current_language']))
{
	$current_language = $_REQUEST['current_language'];
}

// retrieve the translated strings.
$app_strings = return_application_language($current_language);

if(isset($app_strings['LBL_CHARSET']))
{
	$charset = $app_strings['LBL_CHARSET'];
}
else
{
	$charset = $default_charset;	
}

$log->info("current langugage is $current_language");
$log->info("current module is $current_module ");	
$log->info("including $current_module_file");	

checkFileAccessForInclusion($current_module_file);
require_once($current_module_file);
$draw_this = new jpgraph();

if (isset($_REQUEST['graph'])) $graph = $_REQUEST['graph']; 
else $graph = 'default';

if (isset($_REQUEST['flat_array1'])) $flat_array1 = $_REQUEST['flat_array1']; 
else $flat_array1="foo,bar";
if (isset($_REQUEST['flat_array2'])) $flat_array2 = $_REQUEST['flat_array2']; 
else $flat_array2="1,2";
if (isset($_REQUEST['title'])) $title = $_REQUEST['title']; 
else $title="the title";
if (isset($_REQUEST['subtitle'])) $subtitle = $_REQUEST['subtitle']; 
else $subtitle="the subtitle";

$log->debug("draw_this->$graph");
$log->debug("flat_array1 is ".$flat_array1);
$log->debug("flat_array2 is ".$flat_array2);
$log->debug("title is ".$title);
$log->debug("subtitle is ".$subtitle);

$array1 = explode(",", $flat_array1);
$array2 = explode(",", $flat_array2);

$draw_this->$graph($array1, $array2, $title, $subtitle);
?>