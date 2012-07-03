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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Activities/ListView.php,v 1.14 2005/03/26 09:45:00 samk Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('modules/Calendar/Activity.php');
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/utils/utils.php');
require_once('modules/CustomView/CustomView.php');
require_once('modules/Calendar/CalendarCommon.php');

global $app_strings;
global $list_max_entries_per_page;

$log = LoggerManager::getLogger('task_list');

global $currentModule,$image_path,$theme,$adb;

if (isset($_REQUEST['current_user_only'])) $current_user_only = vtlib_purify($_REQUEST['current_user_only']);

$focus = new Activity();
// Initialize sort by fields
$focus->initSortbyField('Calendar');
// END
$smarty = new vtigerCRM_Smarty;
$other_text = Array();

if(!$_SESSION['lvs'][$currentModule])
{
	unset($_SESSION['lvs']);
	$modObj = new ListViewSession();
	$modObj->sorder = $sorder;
	$modObj->sortby = $order_by;
	$_SESSION['lvs'][$currentModule] = get_object_vars($modObj);
}

if($_REQUEST['errormsg'] != '')
{
        $errormsg = vtlib_purify($_REQUEST['errormsg']);
        $smarty->assign("ERROR",$mod_strings["SHARED_EVENT_DEL_MSG"]);
}else
{
        $smarty->assign("ERROR","");
}

if(ListViewSession::hasViewChanged($currentModule,$viewid)) {
	$_SESSION['ACTIVITIES_ORDER_BY'] = '';
}

//<<<<<<< sort ordering >>>>>>>>>>>>>
$sorder = $focus->getSortOrder();
$order_by = $focus->getOrderBy();

$_SESSION['ACTIVITIES_ORDER_BY'] = $order_by;
$_SESSION['ACTIVITIES_SORT_ORDER'] = $sorder;
//<<<<<<< sort ordering >>>>>>>>>>>>>


//<<<<cutomview>>>>>>>
$oCustomView = new CustomView($currentModule);
$viewid = $oCustomView->getViewId($currentModule);
$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid);
$viewnamedesc = $oCustomView->getCustomViewByCvid($viewid);

//Added to handle approving or denying status-public by the admin in CustomView
$statusdetails = $oCustomView->isPermittedChangeStatus($viewnamedesc['status']);
$smarty->assign("CUSTOMVIEW_PERMISSION",$statusdetails);

//To check if a user is able to edit/delete a customview
$edit_permit = $oCustomView->isPermittedCustomView($viewid,'EditView',$currentModule);
$delete_permit = $oCustomView->isPermittedCustomView($viewid,'Delete',$currentModule);
$smarty->assign("CV_EDIT_PERMIT",$edit_permit);
$smarty->assign("CV_DELETE_PERMIT",$delete_permit);

//<<<<<customview>>>>>
if($viewid == 0 )
{
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='<?php echo vtiger_imageurl('close.gif', $theme) ?>'></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
			<span class='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>
		</td>
		</tr>
		</tbody></table>
		</div>";
	echo "</td></tr></table>";
	exit;
}

$changeOwner = getAssignedTo(16);
$userList = $changeOwner[0];
$groupList = $changeOwner[1];

$smarty->assign("CHANGE_USER",$userList);
$smarty->assign("CHANGE_GROUP",$groupList);
$smarty->assign("CHANGE_OWNER",getUserslist());
$smarty->assign("CHANGE_GROUP_OWNER",getGroupslist());
$smarty->assign('MAX_RECORDS', $list_max_entries_per_page);
$where = "";

$url_string = ''; // assigning http url string

if(isset($_REQUEST['query']) && $_REQUEST['query'] == 'true')
{

	list($where, $ustring) = split("#@@#",getWhereCondition($currentModule));
	// we have a query
	$url_string .="&query=true".$ustring;
	$log->info("Here is the where clause for the list view: $where");
	$smarty->assign("SEARCH_URL",$url_string);
}


if($viewnamedesc['viewname'] == 'All')
{
	$smarty->assign("ALL", 'All');
}

if(isPermitted("Calendar","Delete",$_REQUEST['record']) == 'yes')
{
	$other_text['del'] = $app_strings[LBL_MASS_DELETE];
}
if(isPermitted('Calendar','EditView','') == 'yes')
{
        $other_text['c_owner'] = $app_strings[LBL_CHANGE_OWNER];
}
global  $task_title;
$title_display = $current_module_strings['LBL_LIST_FORM_TITLE'];
if ($task_title) $title_display= $task_title;

//Retreive the list from Database
//<<<<<<<<<customview>>>>>>>>>
if($viewid != "0")
{
	$listquery = getListQuery("Calendar");
	$list_query = $oCustomView->getModifiedCvListQuery($viewid,$listquery,"Calendar");
}else
{
	$list_query = getListQuery("Calendar");
}
//<<<<<<<<customview>>>>>>>>>

if(isset($where) && $where != '')
{
	if(isset($_REQUEST['from_homepagedb']) && $_REQUEST['from_homepagedb'] == 'true')
		$list_query .= " and ((vtiger_activity.status!='Completed' and vtiger_activity.status!='Deferred') or vtiger_activity.status is null) and ((vtiger_activity.eventstatus!='Held' and vtiger_activity.eventstatus!='Not Held') or vtiger_activity.eventstatus is null) AND ".$where;
	else
		$list_query .= " AND " .$where;
}
if (isset($_REQUEST['from_homepage'])) {
	$dbStartDateTime = new DateTimeField(date('Y-m-d H:i:s'));
	$userStartDate = $dbStartDateTime->getDisplayDate();
	$userStartDateTime = new DateTimeField($userStartDate.' 00:00:00');
	$startDateTime = $userStartDateTime->getDBInsertDateTimeValue();

	$userEndDateTime = new DateTimeField($userStartDate.' 23:59:00');
	$endDateTime = $userEndDateTime->getDBInsertDateTimeValue();

	if ($_REQUEST['from_homepage'] == 'upcoming_activities')
		$list_query .= " AND (vtiger_activity.status is NULL OR vtiger_activity.status not in ('Completed','Deferred')) and (vtiger_activity.eventstatus is NULL OR  vtiger_activity.eventstatus not in ('Held','Not Held')) AND (CAST((CONCAT(date_start,' ',time_start)) AS DATETIME) >= '$startDateTime' OR CAST((CONCAT(vtiger_recurringevents.recurringdate,' ',time_start)) AS DATETIME) >= '$startDateTime')";
	elseif ($_REQUEST['from_homepage'] == 'pending_activities')
		$list_query .= " AND (vtiger_activity.status is NULL OR vtiger_activity.status not in ('Completed','Deferred')) and (vtiger_activity.eventstatus is NULL OR  vtiger_activity.eventstatus not in ('Held','Not Held')) AND (CAST((CONCAT(due_date,' ',time_end)) AS DATETIME) <= '$endDateTime' OR CAST((CONCAT(vtiger_recurringevents.recurringdate,' ',time_start)) AS DATETIME) <= '$endDateTime')";
}

if(isset($order_by) && $order_by != '') {
	if($order_by == 'smownerid') {
		$list_query .= ' ORDER BY user_name '.$sorder;
	} else {
		$tablename = getTableNameForField('Calendar',$order_by);
		$tablename = (($tablename != '')?($tablename."."):'');
		if($order_by == 'lastname')
         	$list_query .= ' ORDER BY vtiger_contactdetails.lastname '.$sorder;
		else
			$list_query .= ' ORDER BY '.$tablename.$order_by.' '.$sorder;
	}
}

//Constructing the list view
$smarty->assign("CUSTOMVIEW_OPTION",$customviewcombo_html);
$smarty->assign("VIEWID", $viewid);
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("MODULE",$currentModule);
$smarty->assign("SINGLE_MOD",getTranslatedString('SINGLE_'.$currentModule, $currentModule));
$smarty->assign("BUTTONS",$other_text);
$smarty->assign("NEW_EVENT",$app_strings['LNK_NEW_EVENT']);
$smarty->assign("NEW_TASK",$app_strings['LNK_NEW_TASK']);

//Postgres 8 fixes
if( $adb->dbType == "pgsql")
	$list_query = fixPostgresQuery( $list_query, $log, 0);

if(PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true){
	$count_result = $adb->query( mkCountQuery( $list_query));
	$noofrows = $adb->query_result($count_result,0,"count");
}else{
	$noofrows = null;
}

$queryMode = (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true');
$start = ListViewSession::getRequestCurrentPage($currentModule, $list_query, $viewid, $queryMode);

$navigation_array = VT_getSimpleNavigationValues($start,$list_max_entries_per_page,$noofrows);

$limit_start_rec = ($start-1) * $list_max_entries_per_page;

if( $adb->dbType == "pgsql")
	$list_result = $adb->pquery($list_query. " OFFSET $limit_start_rec LIMIT $list_max_entries_per_page", array());
else
	$list_result = $adb->pquery($list_query. " LIMIT $limit_start_rec, $list_max_entries_per_page", array());

$recordListRangeMsg = getRecordRangeMessage($list_result, $limit_start_rec,$noofrows);
$smarty->assign('recordListRange',$recordListRangeMsg);

//Retreive the List View Table Header
if($viewid !='')
$url_string .="&viewname=".$viewid;

//Cambiado code to add close button in custom vtiger_field
if (($viewid!=0)&&($viewid!="")){
  if (!isset($oCustomView->list_fields['Close'])) $oCustomView->list_fields['Close']=array ( 'vtiger_activity' => 'eventstatus' );
  if (!isset($oCustomView->list_fields_name['Close'])) $oCustomView->list_fields_name['Close']='eventstatus';
}
$listview_header = getListViewHeader($focus,"Calendar",$url_string,$sorder,$order_by,"",$oCustomView);
$smarty->assign("LISTHEADER", $listview_header);

$listview_header_search=getSearchListHeaderValues($focus,"Calendar",$url_string,$sorder,$order_by,"",$oCustomView);
$smarty->assign("SEARCHLISTHEADER", $listview_header_search);

$listview_entries = getListViewEntries($focus,"Calendar",$list_result,$navigation_array,"","","EditView","Delete",$oCustomView);

$smarty->assign("LISTENTITY", $listview_entries);
$smarty->assign("SELECT_SCRIPT", $view_script);

//Added to select Multiple records in multiple pages
$smarty->assign("SELECTEDIDS", vtlib_purify($_REQUEST['selobjs']));
$smarty->assign("ALLSELECTEDIDS", vtlib_purify($_REQUEST['allselobjs']));
$smarty->assign("CURRENT_PAGE_BOXES", implode(array_keys($listview_entries),";"));

$navigationOutput = getTableHeaderSimpleNavigation($navigation_array,$url_string,"Calendar","ListView",$viewid);
$alphabetical = AlphabeticalSearch($currentModule,'ListView','subject','true','basic',"","","","",$viewid);
$fieldnames = getAdvSearchfields($module);
$criteria = getcriteria_options();
$smarty->assign("CRITERIA", $criteria);
$smarty->assign("FIELDNAMES", $fieldnames);
$smarty->assign("ALPHABETICAL", $alphabetical);
$smarty->assign("NAVIGATION", $navigationOutput);
$category = getParentTab();
$smarty->assign("CATEGORY",$category);

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

$_SESSION[$currentModule.'_listquery'] = $list_query;

// Gather the custom link information to display
include_once('vtlib/Vtiger/Link.php');
$customlink_params = Array('MODULE'=>$currentModule, 'ACTION'=>vtlib_purify($_REQUEST['action']), 'CATEGORY'=> $category);
$smarty->assign('CUSTOM_LINKS', Vtiger_Link::getAllByType(getTabid($currentModule), Array('LISTVIEWBASIC','LISTVIEW'), $customlink_params));
// END

if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
	$smarty->display("ListViewEntries.tpl");
else
	$smarty->display("ActivityListView.tpl");
?>