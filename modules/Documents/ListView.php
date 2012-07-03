<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('modules/Documents/Documents.php');
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/utils/utils.php');
require_once('modules/CustomView/CustomView.php');
require_once('include/database/Postgres8.php');

global $app_strings,$mod_strings,$list_max_entries_per_page;

$log = LoggerManager::getLogger('note_list');

global $currentModule,$image_path,$theme;
$category = getParentTab();
if(!$_SESSION['lvs'][$currentModule])
{
	unset($_SESSION['lvs']);
	$modObj = new ListViewSession();
	$modObj->sorder = $sorder;
	$modObj->sortby = $order_by;
	$_SESSION['lvs'][$currentModule] = get_object_vars($modObj);
}

//<<<<cutomview>>>>>>>
$oCustomView = new CustomView("Documents");
$viewid = $oCustomView->getViewId($currentModule);
$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid);
$viewnamedesc = $oCustomView->getCustomViewByCvid($viewid);
//<<<<<customview>>>>>
if (!isset($where)) $where = "";
$url_string = ''; // assigning http url string

$focus = new Documents();
// Initialize sort by fields
$focus->initSortbyField('Documents');
// END
$smarty = new vtigerCRM_Smarty;
$other_text = Array();

if($_REQUEST['errormsg'] != '')
{
        $errormsg = vtlib_purify($_REQUEST['errormsg']);
        $smarty->assign("ERROR","The User does not have permission to delete ".$errormsg." ".$currentModule);
}else
{
        $smarty->assign("ERROR","");
}

if(ListViewSession::hasViewChanged($currentModule,$viewid)) {
	$_SESSION['NOTES_ORDER_BY'] = '';
}
//<<<<<<<<<<<<<<<<<<< sorting - stored in session >>>>>>>>>>>>>>>>>>>>
$sorder = $focus->getSortOrder();
$order_by = $focus->getOrderBy();

if(empty($_REQUEST['folderid'])) {
	$_SESSION['NOTES_ORDER_BY'] = $order_by;
	$_SESSION['NOTES_SORT_ORDER'] = $sorder;
}
//<<<<<<<<<<<<<<<<<<< sorting - stored in session >>>>>>>>>>>>>>>>>>>>

if(isPermitted('Documents','Delete','') == 'yes')
{
	$smarty->assign("MASS_DELETE","yes");
	$other_text['del'] = $app_strings['LBL_MASS_DELETE'];
}

if($viewnamedesc['viewname'] == 'All')
{
	$smarty->assign("ALL", 'All');
}

//Added to handle approving or denying status-public by the admin in CustomView
$statusdetails = $oCustomView->isPermittedChangeStatus($viewnamedesc['status']);
$smarty->assign("CUSTOMVIEW_PERMISSION",$statusdetails);

//To check if a user is able to edit/delete a customview
$edit_permit = $oCustomView->isPermittedCustomView($viewid,'EditView',$currentModule);
$delete_permit = $oCustomView->isPermittedCustomView($viewid,'Delete',$currentModule);
$smarty->assign("CV_EDIT_PERMIT",$edit_permit);
$smarty->assign("CV_DELETE_PERMIT",$delete_permit);

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$smarty->assign("CUSTOMVIEW_OPTION",$customviewcombo_html);
$smarty->assign("VIEWID", $viewid);
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("MODULE",$currentModule);
$smarty->assign("SINGLE_MOD",getTranslatedString('SINGLE_'.$currentModule, $currentModule));
$smarty->assign("BUTTONS",$other_text);
$smarty->assign("CATEGORY",$category);
$smarty->assign('MAX_RECORDS', $list_max_entries_per_page);

//Retreive the list from Database
//<<<<<<<<<customview>>>>>>>>>
global $current_user;
$queryGenerator = new QueryGenerator($currentModule, $current_user);
if ($viewid != "0") {
	$queryGenerator->initForCustomViewById($viewid);
} else {
	$queryGenerator->initForDefaultCustomView();
}
//<<<<<<<<customview>>>>>>>>>

$hide_empty_folders = 'no';

// Enabling Module Search
$url_string = '';
if($_REQUEST['query'] == 'true') {
	$queryGenerator->addUserSearchConditions($_REQUEST);
	$ustring = getSearchURL($_REQUEST);
	$url_string .= "&query=true$ustring";
	$smarty->assign('SEARCH_URL', $url_string);
}

$query = $queryGenerator->getQuery();
$where = $queryGenerator->getConditionalWhere();
if(isset($where) && $where != '') {
	$_SESSION['export_where'] = $where;
} else {
	unset($_SESSION['export_where']);
}

$focus->query = $query;

if($viewid ==0)
{
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme)."' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span clas
		s='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
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

//Retreive the List View Table Header
if($viewid !='')
$url_string .="&viewname=".$viewid;

$controller = new ListViewController($adb, $current_user, $queryGenerator);
$listview_header_search = $controller->getBasicSearchFieldInfoList();
$smarty->assign("SEARCHLISTHEADER",$listview_header_search);

$smarty->assign("SELECT_SCRIPT", $view_script);

$start = Array();
$request_folderid = '';

if($_REQUEST['action'] == 'DocumentsAjax' && isset($_REQUEST['folderid']))
{
	$request_folderid = vtlib_purify($_REQUEST['folderid']);
	$start[$request_folderid] = vtlib_purify($_REQUEST['start']);
}
$focus->del_create_def_folder($focus->query);

$dbQuery = "select * from vtiger_attachmentsfolder";
$result = $adb->pquery($dbQuery,array());
$foldercount = $adb->num_rows($result);
$folders = Array();
$emptyfolders = Array();
if($foldercount > 0 )
{
	for($i=0;$i<$foldercount;$i++)
	{
		$query = '';
		$displayFolder='';
		$query = $focus->query;
		$list_query = '';
		$list_query = $focus->query;
		$folder_id = $adb->query_result($result,$i,"folderid");
		$query .= " and vtiger_notes.folderid = $folder_id";
		$sorder = $focus->getSortOrderForFolder($folder_id);
		if(!is_array($_SESSION['NOTES_FOLDER_SORT_ORDER'])) {
			$_SESSION['NOTES_FOLDER_SORT_ORDER'] = array();
		}
		$_SESSION['NOTES_FOLDER_SORT_ORDER'][$folder_id] = $sorder;
		$order_by = $focus->getOrderByForFolder($folder_id);
		if(!is_array($_SESSION['NOTES_FOLDER_ORDER_BY'])) {
			$_SESSION['NOTES_FOLDER_ORDER_BY'] = array();
		}
		$_SESSION['NOTES_FOLDER_ORDER_BY'][$folder_id] = $order_by;
		if($folder_id != $request_folderid)
		{
			$start[$folder_id] = 1;
		}


		if(isset($order_by) && $order_by != '')
		{
			$tablename = getTableNameForField('Documents',$order_by);
			$tablename = (($tablename != '')?($tablename."."):'');

			if( $adb->dbType == "pgsql")
			{
 	    		$query .= ' GROUP BY '.$tablename.$order_by;
 	    		$list_query .= ' GROUP BY '.$tablename.$order_by;
 	    		$focus->additional_query .= ' GROUP BY '.$tablename.$order_by;
			}

        	$query .= ' ORDER BY '.$tablename.$order_by.' '.$sorder;
        	$list_query .= ' ORDER BY '.$tablename.$order_by.' '.$sorder;
        	$focus->additional_query .= ' ORDER BY '.$tablename.$order_by.' '.$sorder;
		}
		//Retreiving the no of rows
		$count_result = $adb->query( mkCountQuery( $query));
		$num_records = $adb->query_result($count_result,0,"count");
		if($num_records > 0){
			$displayFolder=true;
		}
		//navigation start
		$max_entries_per_page = $list_max_entries_per_page;
		//Postgres 8 fixes
		if( $adb->dbType == "pgsql")
			$list_query = fixPostgresQuery( $query, $log, 0);

		if($folder_id == $request_folderid){
			$start[$folder_id] = 1;
			if(!empty($_REQUEST['start'])){
				$start[$folder_id] = ListViewSession::getRequestStartPage();
				if($start[$folder_id] == 'last'){
					if($num_records > 0){
						$start[$folder_id] = ceil($num_records/$max_entries_per_page);
					}
				}
				if(!is_numeric($start[$folder_id])){
					$start[$folder_id] = 1;
				}
			}
		}

		$navigation_array = VT_getSimpleNavigationValues($start[$folder_id],$max_entries_per_page,$num_records);
		if($folder_id == $request_folderid){
			if(!is_array($_SESSION['lvs'][$currentModule]['start'])){
				$_SESSION['lvs'][$currentModule]['start'] = array();
			}
			$_SESSION['lvs'][$currentModule]['start'][$folder_id] = $start[$folder_id];
		}
		$limit_start_rec = ($start[$folder_id]-1) * $max_entries_per_page;

		if( $adb->dbType == "pgsql")
			$list_result = $adb->pquery($query. " OFFSET $limit_start_rec LIMIT $max_entries_per_page", array());
		else
			$list_result = $adb->pquery($query. " LIMIT $limit_start_rec, $max_entries_per_page", array());
		//navigation end

		$folder_details=Array();
		$folderid = $adb->query_result($result,$i,"folderid");
		$folder_details['folderid']=$folderid;
		$folder_details['foldername']=$adb->query_result($result,$i,"foldername");
		$foldername = $folder_details['foldername'];
		$folder_details['description']=$adb->query_result($result,$i,"description");
		$folder_url_string = $url_string . "&folderid=$folderid";
		$folder_details['header'] = $controller->getListViewHeader($focus,$currentModule,
				$folder_url_string,$sorder, $order_by);
		$folder_files = $controller->getListViewEntries($focus,$currentModule,$list_result,
			$navigation_array);
		$folder_details['entries']= $folder_files;
		$folder_details['navigation'] = getTableHeaderSimpleNavigation($navigation_array, $url_string,"Documents",$folder_id,$viewid);
		$folder_details['recordListRange'] = getRecordRangeMessage($list_result, $limit_start_rec,
				$num_records);
		if ($displayFolder == true) {
			$folders[$foldername] = $folder_details;
		} else{
			$emptyfolders[$foldername] = $folder_details;
		}
		if ($folderid == 1) $default_folder_details = $folder_details;
	}
	if (count($folders) == 0) $folders[$default_folder_details['foldername']] = $default_folder_details;
}
else
{
	$smarty->assign("NO_FOLDERS","yes");
}

$smarty->assign("NO_OF_FOLDERS",$foldercount);
$smarty->assign("FOLDERS", $folders);
$smarty->assign("EMPTY_FOLDERS", $emptyfolders);
$smarty->assign("ALL_FOLDERS", array_merge($folders, $emptyfolders));

//Added to select Multiple records in multiple pages
$smarty->assign("SELECTEDIDS", vtlib_purify($_REQUEST['selobjs']));
$smarty->assign("ALLSELECTEDIDS", vtlib_purify($_REQUEST['allselobjs']));

$alphabetical = AlphabeticalSearch($currentModule,'index','notes_title','true','basic',"","","","",$viewid);
$fieldnames = $controller->getAdvancedSearchOptionString();
$criteria = getcriteria_options();
$smarty->assign("CRITERIA", $criteria);
$smarty->assign("FIELDNAMES", $fieldnames);
$smarty->assign("ALPHABETICAL", $alphabetical);
$smarty->assign("NAVIGATION", $navigationOutput);
$smarty->assign("RECORD_COUNTS", $record_string);
$adminuser = is_admin($current_user);
$smarty->assign("IS_ADMIN",$adminuser);

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

ListViewSession::setSessionQuery($currentModule,$focus->query,$viewid);

// Gather the custom link information to display
include_once('vtlib/Vtiger/Link.php');
$customlink_params = Array('MODULE'=>$currentModule, 'ACTION'=>vtlib_purify($_REQUEST['action']), 'CATEGORY'=> $category);
$smarty->assign('CUSTOM_LINKS', Vtiger_Link::getAllByType(getTabid($currentModule), Array('LISTVIEWBASIC','LISTVIEW'), $customlink_params));
// END

if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '' || $_REQUEST['mode'] == 'ajax')
	$smarty->display("DocumentsListViewEntries.tpl");
else
	$smarty->display("DocumentsListView.tpl");
?>