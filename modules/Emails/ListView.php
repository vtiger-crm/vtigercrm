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
 * ****************************************************************************** */
/* * *******************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Emails/ListView.php,v 1.12 2005/04/18 10:37:49 samk Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ******************************************************************************* */

require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('modules/Emails/Emails.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/CustomView/CustomView.php');
global $current_user;
global $app_strings;
global $mod_strings;

global $list_max_entries_per_page, $adb;

$log = LoggerManager::getLogger('email_list');

global $currentModule;

global $image_path;
global $theme;

$url_string = ''; // assigning http url string

$focus = new Emails();
// Initialize sort by fields
$focus->initSortbyField('Emails');
// END
$smarty = new vtigerCRM_Smarty;
$other_text = Array();

if (ListViewSession::hasViewChanged($currentModule, $viewid)) {
	$_SESSION['EMAILS_ORDER_BY'] = '';
}

//<<<<<<<<<<<<<<<<<<< sorting - stored in session >>>>>>>>>>>>>>>>>>>>
if ($_REQUEST['order_by'] != '')
	$order_by = $adb->sql_escape_string($_REQUEST['order_by']);
else
	$order_by = (($_SESSION['EMAILS_ORDER_BY'] != '') ? ($_SESSION['EMAILS_ORDER_BY']) : ($focus->default_order_by));

if ($_REQUEST['sorder'] != '')
	$sorder = $adb->sql_escape_string($_REQUEST['sorder']);
else
	$sorder = (($_SESSION['EMAILS_SORT_ORDER'] != '') ? ($_SESSION['EMAILS_SORT_ORDER']) : ($focus->default_sort_order));

$_SESSION['EMAILS_ORDER_BY'] = $order_by;
$_SESSION['EMAILS_SORT_ORDER'] = $sorder;
//<<<<<<<<<<<<<<<<<<< sorting - stored in session >>>>>>>>>>>>>>>>>>>>
// focus_list is the means of passing data to a ListView.
global $focus_list;

//<<<<cutomview>>>>>>>
$oCustomView = new CustomView("Emails");
$viewid = $oCustomView->getViewId($currentModule);
$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid);
$viewnamedesc = $oCustomView->getCustomViewByCvid($viewid);

//<<<<<customview>>>>>
// Buttons and View options
if (isPermitted('Emails', 'Delete', '') == 'yes') {
	$other_text['del'] = $app_strings[LBL_MASS_DELETE];
}

if ($viewnamedesc['viewname'] == 'All') {
	$smarty->assign("ALL", 'All');
}

global $email_title;
$display_title = $mod_strings['LBL_LIST_FORM_TITLE'];
if ($email_title)
	$display_title = $email_title;

//to get the search vtiger_field if exists
if (isset($_REQUEST['search']) && $_REQUEST['search'] != '' && $_REQUEST['search_text'] != '') {
	$url_string .= "&search=" . vtlib_purify($_REQUEST['search']) . "&search_field=" . vtlib_purify($_REQUEST['search_field']) . "&search_text=" . vtlib_purify($_REQUEST['search_text']);
	if ($_REQUEST['search_field'] != 'join')
		$where = $adb->sql_escape_string($_REQUEST['search_field']) . " like '" . formatForSqlLike($_REQUEST['search_text']) . "'";
	else
		$where = "(subject like '" . formatForSqlLike($_REQUEST['search_text']) . "' OR vtiger_users.user_name like '" . formatForSqlLike($_REQUEST['search_text']) . "')";
}


//Retreive the list from Database
//<<<<<<<<<customview>>>>>>>>>
if ($viewid != "0") {
	$listquery = getListQuery("Emails");
	$list_query = $oCustomView->getModifiedCvListQuery($viewid, $listquery, "Emails");
} else {
	$list_query = getListQuery("Emails");
}
//<<<<<<<<customview>>>>>>>>>

if (isset($where) && $where != '') {
	$list_query .= " AND " . $where;
}
if ($_REQUEST['folderid'] == '2') {
	$list_query .= " AND vtiger_seactivityrel.crmid in (select contactid from vtiger_contactdetails) AND vtiger_emaildetails.email_flag !='WEBMAIL'";
}
if ($_REQUEST['folderid'] == '3') {
	$list_query .= " AND vtiger_seactivityrel.crmid in (select accountid from vtiger_account)";
}
if ($_REQUEST['folderid'] == '4') {
	$list_query .= " AND vtiger_seactivityrel.crmid in (select leadid from vtiger_leaddetails)";
}
if ($_REQUEST['folderid'] == '5') {
	$list_query .= " AND vtiger_salesmanactivityrel.smid in (select id from vtiger_users)";
}
if ($_REQUEST['folderid'] == '6') {
	$list_query .= " AND vtiger_emaildetails.email_flag ='WEBMAIL'";
}
if (isset($order_by) && $order_by != '') {
	$tablename = getTableNameForField('Emails', $order_by);
	$tablename = (($tablename != '') ? ($tablename . ".") : '');
	$list_query .= ' ORDER BY ' . $tablename . $order_by . ' ' . $sorder;
}

if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true) {
	$count_result = $adb->query(mkCountQuery($list_query));
	$noofrows = $adb->query_result($count_result, 0, "count");
} else {
	$noofrows = null;
}

$queryMode = (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true');
$start = ListViewSession::getRequestCurrentPage($currentModule, $list_query, $viewid, $queryMode);

if (!isset($_REQUEST['start']))
	$start = 1;

$navigation_array = VT_getSimpleNavigationValues($start, $list_max_entries_per_page, $noofrows);

$limit_start_rec = ($start - 1) * $list_max_entries_per_page;


if ($adb->dbType == "pgsql")
	$list_result = $adb->pquery($list_query . " OFFSET $limit_start_rec LIMIT $list_max_entries_per_page", array());
else
	$list_result = $adb->pquery($list_query . " LIMIT $limit_start_rec, $list_max_entries_per_page", array());

//Constructing the list view
$smarty->assign("CUSTOMVIEW_OPTION", $customviewcombo_html);
$smarty->assign("VIEWID", $viewid);
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("MODULE", $currentModule);
$smarty->assign("SINGLE_MOD", getTranslatedString('SINGLE_' . $currentModule, $currentModule));
$smarty->assign("BUTTONS", $other_text);
$category = getParentTab();
$smarty->assign("CATEGORY", $category);

if ($viewid != '')
	$url_string .="&viewname=" . $viewid;
if (isset($_REQUEST['folderid']) && $_REQUEST['folderid'] != '')
	$url_string .= "&folderid=" . vtlib_purify($_REQUEST['folderid']);

$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string, $currentModule, $_REQUEST['folderid'], $viewid);
$smarty->assign("NAVIGATION", $navigationOutput);

$listview_header = getListViewHeader($focus, "Emails", $url_string, $sorder, $order_by, "", $oCustomView);
$smarty->assign("LISTHEADER", $listview_header);

$listview_entries = getListViewEntries($focus, "Emails", $list_result, $navigation_array, "", "", "EditView", "Delete", $oCustomView);
//--------------------------added to fix the ticket(4386)------------------------START
foreach ($listview_entries as $key => $value) {
	$sql = "select email_flag from vtiger_emaildetails where emailid=?";
	$result = $adb->pquery($sql, array($key));
	$email_flag = $adb->query_result($result, 0, "email_flag");
	$emailid[$key] = $email_flag;
}
$smarty->assign("EMAILFALG", $emailid);
//--------------------------added to fix the ticket(4386)------------------------END

$smarty->assign("LISTENTITY", $listview_entries);
$smarty->assign("SELECT_SCRIPT", $view_script);

$smarty->assign("USERID", $current_user->id);

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);
$smarty->assign("theme", $theme);
if ($_REQUEST['ajax'] != '')
	$smarty->display("EmailContents.tpl");
else
	$smarty->display("Emails.tpl");
?>