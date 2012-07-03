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
 * $Header$
 * Description:  Contains a variety of utility functions used to display UI
 * components such as form headers and footers.  Intended to be modified on a per
 * theme basis.
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once("include/utils/utils.php");
require_once("include/calculator/Calc.php");

global $currentModule,$default_charset;
global $app_strings;
global $app_list_strings;
global $moduleList;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$userName = getFullNameFromArray('Users', $current_user->column_fields);
$smarty = new vtigerCRM_Smarty;
$header_array = getHeaderArray();
$smarty->assign("HEADERS",$header_array);
$smarty->assign("THEME",$theme);
$smarty->assign("IMAGEPATH",$image_path);
$smarty->assign("USER",$userName);

$qc_modules = getQuickCreateModules();
$smarty->assign("QCMODULE", $qc_modules);
$smarty->assign("APP", $app_strings);

$cnt = count($qc_modules);
$smarty->assign("CNT", $cnt);

$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
$smarty->assign("MODULE_NAME", $currentModule);
$date = new DateTimeField(null);
$smarty->assign("DATE", $date->getDisplayDateTimeValue());
$smarty->assign("CURRENT_USER_MAIL", $current_user->email1);
$smarty->assign("CURRENT_USER", $current_user->user_name);
$smarty->assign("CURRENT_USER_ID", $current_user->id);
$smarty->assign("MODULELISTS",$app_list_strings['moduleList']);
$smarty->assign("CATEGORY",getParentTab());
$smarty->assign("CALC",get_calc($image_path));
$smarty->assign("MENUSTRUCTURE",getMenuStructure($currentModule));
$smarty->assign("ANNOUNCEMENT",get_announcements());
$smarty->assign("USE_ASTERISK", get_use_asterisk($current_user->id));

if (is_admin($current_user)) $smarty->assign("ADMIN_LINK", "<a href='index.php?module=Settings&action=index'>".$app_strings['LBL_SETTINGS']."</a>");

$module_path="modules/".$currentModule."/";

require_once('include/Menu.php');

//Assign the entered global search string to a variable and display it again
if($_REQUEST['query_string'] != '')
	$smarty->assign("QUERY_STRING",htmlspecialchars($_REQUEST['query_string'],ENT_QUOTES,$default_charset));//BUGIX " Cross-Site-Scripting "
else
	$smarty->assign("QUERY_STRING","$app_strings[LBL_SEARCH_STRING]");

global $module_menu;

require_once('data/Tracker.php');
$tracFocus=new Tracker();
$list = $tracFocus->get_recently_viewed($current_user->id);
$smarty->assign("TRACINFO",$list);

// Gather the custom link information to display
include_once('vtlib/Vtiger/Link.php');
$hdrcustomlink_params = Array('MODULE'=>$currentModule);
$COMMONHDRLINKS = Vtiger_Link::getAllByType(Vtiger_Link::IGNORE_MODULE, Array('ONDEMANDLINK', 'HEADERLINK','HEADERSCRIPT', 'HEADERCSS'), $hdrcustomlink_params);
$smarty->assign('HEADERLINKS', $COMMONHDRLINKS['HEADERLINK']);
$smarty->assign('ONDEMANDLINKS', $COMMONHDRLINKS['ONDEMANDLINK']);
$smarty->assign('HEADERSCRIPTS', $COMMONHDRLINKS['HEADERSCRIPT']);
$smarty->assign('HEADERCSS', $COMMONHDRLINKS['HEADERCSS']);
// END

// Pass on the version information
global $vtiger_current_version;
$smarty->assign('VERSION', $vtiger_current_version);
// END

$sql="select * from vtiger_organizationdetails";
$result = $adb->pquery($sql, array());
//Handle for allowed organation logo/logoname likes UTF-8 Character
$organization_logo = decode_html($adb->query_result($result,0,'logoname'));
$smarty->assign("LOGO",$organization_logo);

$smarty->display("Header.tpl");
?>