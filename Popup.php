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
require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/utils/utils.php');
global $app_strings, $default_charset;
global $currentModule, $current_user;
global $theme, $adb;
$url_string = '';
$smarty = new vtigerCRM_Smarty;
if (!isset($where)) $where = "";

$parent_tab=getParentTab();
$smarty->assign("CATEGORY",$parent_tab);

$url = '';
$popuptype = '';
$popuptype = vtlib_purify($_REQUEST["popuptype"]);

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("THEME_PATH",$theme_path);
$smarty->assign("MODULE",$currentModule);

$form = vtlib_purify($_REQUEST['form']);
//added to get relatedto field value for todo, while selecting from the popup list, after done the alphabet or basic search.
if(isset($_REQUEST['maintab']) && $_REQUEST['maintab'] != '')
{
        $act_tab = vtlib_purify($_REQUEST['maintab']);
        $url = "&maintab=".$act_tab;
}
$smarty->assign("MAINTAB",$act_tab);
			
			
switch($currentModule)
{
	case 'Contacts':
		require_once("modules/$currentModule/Contacts.php");
		$focus = new Contacts();
		$log = LoggerManager::getLogger('contact_list');
		$smarty->assign("SINGLE_MOD",'Contact');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		else
			$smarty->assign("RETURN_MODULE",'Emails');
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','lastname','true','basic',$popuptype,"","",$url);
		break;
	case 'Campaigns':
		require_once("modules/$currentModule/Campaigns.php");
		$focus = new Campaigns();
		$log = LoggerManager::getLogger('campaign_list');
		$smarty->assign("SINGLE_MOD",'Campaign');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','campaignname','true','basic',$popuptype,"","",$url);
		break;
	case 'Accounts':
		require_once("modules/$currentModule/Accounts.php");
		$focus = new Accounts();
		$log = LoggerManager::getLogger('account_list');
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		$smarty->assign("SINGLE_MOD",'Account');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		else
			$smarty->assign("RETURN_MODULE",'Emails');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','accountname','true','basic',$popuptype,"","",$url);
		break;
	case 'Leads':
		require_once("modules/$currentModule/Leads.php");
		$focus = new Leads();
		$log = LoggerManager::getLogger('contact_list');
		$smarty->assign("SINGLE_MOD",'Lead');
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		else
			$smarty->assign("RETURN_MODULE",'Emails');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','lastname','true','basic',$popuptype,"","",$url);
		break;
	case 'Potentials':
		require_once("modules/$currentModule/Potentials.php");
		$focus = new Potentials();
		$log = LoggerManager::getLogger('potential_list');
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		$smarty->assign("SINGLE_MOD",'Opportunity');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','potentialname','true','basic',$popuptype,"","",$url);
		break;
	case 'Quotes':
		require_once("modules/$currentModule/Quotes.php");	
		$focus = new Quotes();
		$log = LoggerManager::getLogger('quotes_list');
		$smarty->assign("SINGLE_MOD",'Quote');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','subject','true','basic',$popuptype,"","",$url);
		break;
	case 'Invoice':
		require_once("modules/$currentModule/Invoice.php");
		$focus = new Invoice();
		$smarty->assign("SINGLE_MOD",'Invoice');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','subject','true','basic',$popuptype,"","",$url);
		break;
	case 'Products':
		require_once("modules/$currentModule/$currentModule.php");
		$focus = new $currentModule();
		$smarty->assign("SINGLE_MOD",getTranslatedString('SINGLE_'.$currentModule));
		if(isset($_REQUEST['curr_row']))
		{
			$curr_row = vtlib_purify($_REQUEST['curr_row']);
			$smarty->assign("CURR_ROW", $curr_row);
			$url_string .="&curr_row=".vtlib_purify($_REQUEST['curr_row']);
		}
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');	
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','productname','true','basic',$popuptype,"","",$url);
		break;
	case 'Vendors':
		require_once("modules/$currentModule/Vendors.php");
		$focus = new Vendors();
		$smarty->assign("SINGLE_MOD",'Vendor');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','vendorname','true','basic',$popuptype,"","",$url);
		break;
	case 'SalesOrder':
		require_once("modules/$currentModule/SalesOrder.php");
		$focus = new SalesOrder();
		$smarty->assign("SINGLE_MOD",'SalesOrder');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','subject','true','basic',$popuptype,"","",$url);
		break;
	case 'PurchaseOrder':
		require_once("modules/$currentModule/PurchaseOrder.php");
		$focus = new PurchaseOrder();
		$smarty->assign("SINGLE_MOD",'PurchaseOrder');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','subject','true','basic',$popuptype,"","",$url);
		break;
	case 'PriceBooks':
		require_once("modules/$currentModule/PriceBooks.php");
		$focus = new PriceBooks();
		$smarty->assign("SINGLE_MOD",'PriceBook');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		if(isset($_REQUEST['fldname']) && $_REQUEST['fldname'] !='')
		{
			$smarty->assign("FIELDNAME",vtlib_purify($_REQUEST['fldname']));
			$url_string .="&fldname=".vtlib_purify($_REQUEST['fldname']);
		}
		if(isset($_REQUEST['productid']) && $_REQUEST['productid'] !='')
		{
			$smarty->assign("PRODUCTID",vtlib_purify($_REQUEST['productid']));
			$url_string .="&productid=".vtlib_purify($_REQUEST['productid']);
		}
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','bookname','true','basic',$popuptype,"","",$url);
		break;
	case 'Users':
                require_once("modules/$currentModule/Users.php");
                $focus = new Users();
                $smarty->assign("SINGLE_MOD",'Users');
                if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
                    $smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
                $alphabetical = AlphabeticalSearch($currentModule,'Popup','user_name','true','basic',$popuptype,"","",$url);
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
                break;	
	case 'HelpDesk':
		require_once("modules/$currentModule/HelpDesk.php");
		$focus = new HelpDesk();
		$smarty->assign("SINGLE_MOD",'HelpDesk');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','ticket_title','true','basic',$popuptype,"","",$url);
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		break;

	case 'Documents':
		require_once("modules/$currentModule/Documents.php");
		$focus = new Documents();
		$smarty->assign("SINGLE_MOD",'Document');
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		else
			$smarty->assign("RETURN_MODULE",'Emails');
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		$alphabetical = AlphabeticalSearch($currentModule,'Popup','notes_title','true','basic',$popuptype,"","",$url);
		break;

	// Special case handling (for curr_row value) for Services module
	case 'Services':
		if(isset($_REQUEST['curr_row']))
		{
			$curr_row = vtlib_purify($_REQUEST['curr_row']);
			$smarty->assign("CURR_ROW", $curr_row);
			$url_string .="&curr_row=".vtlib_purify($_REQUEST['curr_row']);
		}
	// vtlib customization: Generic hook for Popup selection
	default:
		require_once("modules/$currentModule/$currentModule.php");
		$focus = new $currentModule();
		$smarty->assign("SINGLE_MOD", $currentModule);		
		if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] !='')
			$smarty->assign("RETURN_MODULE",vtlib_purify($_REQUEST['return_module']));
		$alphabetical = AlphabeticalSearch($currentModule,'Popup',$focus->def_basicsearch_col,'true','basic',$popuptype,"","",$url);
		if (isset($_REQUEST['select'])) $smarty->assign("SELECT",'enable');
		break;
	// END

}
// vtlib customization: Initialize focus to get generic popup
if($_REQUEST['form'] == 'vtlibPopupView') {
	vtlib_setup_modulevars($currentModule, $focus);
}
// END

$smarty->assign("RETURN_ACTION",vtlib_purify($_REQUEST['return_action']));

//Retreive the list from Database
if($currentModule == 'PriceBooks')
{
	$productid=$_REQUEST['productid'];
	$currency_id=$_REQUEST['currencyid'];
	if($currency_id == null) $currency_id = fetchCurrency($current_user->id);
	$query = 'select vtiger_pricebook.*, vtiger_pricebookproductrel.productid, vtiger_pricebookproductrel.listprice, ' .
					'vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime ' .
					'from vtiger_pricebook inner join vtiger_pricebookproductrel on vtiger_pricebookproductrel.pricebookid = vtiger_pricebook.pricebookid ' .
					'inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_pricebook.pricebookid ' .
					'where vtiger_pricebookproductrel.productid='.$adb->sql_escape_string($productid).' and vtiger_crmentity.deleted=0 ' .
							'and vtiger_pricebook.currency_id='.$adb->sql_escape_string($currency_id).' and vtiger_pricebook.active=1';
}
else
{
	if(isset($_REQUEST['recordid']) && $_REQUEST['recordid'] != '')
	{
		$smarty->assign("RECORDID",vtlib_purify($_REQUEST['recordid']));
		$url_string .='&recordid='.vtlib_purify($_REQUEST['recordid']);
        	$where_relquery = getRelCheckquery($currentModule,$_REQUEST['return_module'],$_REQUEST['recordid']);
	}
	if(isset($_REQUEST['relmod_id']) || isset($_REQUEST['fromPotential']))
	{
		if($_REQUEST['relmod_id'] !='')
		{
			$mod = vtlib_purify($_REQUEST['parent_module']);
			$id = vtlib_purify($_REQUEST['relmod_id']);
		}
		else if($_REQUEST['fromPotential'] != '')
		{
			$mod = "Accounts";
			$id= vtlib_purify($_REQUEST['acc_id']);
		}

		$smarty->assign("mod_var_name", "parent_module");
		$smarty->assign("mod_var_value", $mod);
		$smarty->assign("recid_var_name", "relmod_id");
		$smarty->assign("recid_var_value",$id);
		$where_relquery.= getPopupCheckquery($currentModule,$mod,$id);
	}
	else if(isset($_REQUEST['task_relmod_id']))
	{
		$smarty->assign("mod_var_name", "task_parent_module");
		$smarty->assign("mod_var_value", vtlib_purify($_REQUEST['task_parent_module']));
		$smarty->assign("recid_var_name", "task_relmod_id");
		$smarty->assign("recid_var_value",vtlib_purify($_REQUEST['task_relmod_id']));
		$where_relquery.= getPopupCheckquery($currentModule,$_REQUEST['task_parent_module'],$_REQUEST['task_relmod_id']);
	}
	if($currentModule == 'Products' && !$_REQUEST['record_id'] && ($popuptype == 'inventory_prod' || $popuptype == 'inventory_prod_po'))
       		$where_relquery .=" and vtiger_products.discontinued <> 0 AND (vtiger_products.productid NOT IN (SELECT crmid FROM vtiger_seproductsrel WHERE setype='Products'))";
	elseif($currentModule == 'Products' && $_REQUEST['record_id'] && ($popuptype == 'inventory_prod' || $popuptype == 'inventory_prod_po'))
        	$where_relquery .=" and vtiger_products.discontinued <> 0 AND (vtiger_products.productid IN (SELECT crmid FROM vtiger_seproductsrel WHERE setype='Products' AND productid=".$adb->sql_escape_string($_REQUEST['record_id'])."))";
	elseif($currentModule == 'Products' && $_REQUEST['return_module'] != 'Products')
       		$where_relquery .=" and vtiger_products.discontinued <> 0";
       		
	if($_REQUEST['return_module'] == 'Products' && $currentModule == 'Products' && $_REQUEST['recordid'])
       	$where_relquery .=" and vtiger_products.discontinued <> 0 AND (vtiger_crmentity.crmid NOT IN (".$adb->sql_escape_string($_REQUEST['recordid']).") AND vtiger_crmentity.crmid NOT IN (SELECT productid FROM vtiger_seproductsrel WHERE setype='Products') AND vtiger_crmentity.crmid NOT IN (SELECT crmid FROM vtiger_seproductsrel WHERE setype='Products' AND productid=".$adb->sql_escape_string($_REQUEST['recordid'])."))";
	
	if($currentModule == 'Services' && $popuptype == 'inventory_service') {
		$where_relquery .=" and vtiger_service.discontinued <> 0";
	}
	 
	//Avoiding Current Record to show up in the popups When editing.
	if($currentModule == 'Accounts' && $_REQUEST['recordid']!=''){
		$where_relquery .=" and vtiger_account.accountid!=".$adb->sql_escape_string($_REQUEST['recordid']);
		$smarty->assign("RECORDID",vtlib_purify($_REQUEST['recordid']));
	}
	
	if($currentModule == 'Contacts' && $_REQUEST['recordid']!=''){
		$where_relquery .=" and vtiger_contactdetails.contactid!=".$adb->sql_escape_string($_REQUEST['recordid']);
		$smarty->assign("RECORDID",vtlib_purify($_REQUEST['recordid']));
	}
	
	if($currentModule == 'Users' && $_REQUEST['recordid']!=''){
		$where_relquery .=" and vtiger_users.id!=".$adb->sql_escape_string($_REQUEST['recordid']);
		$smarty->assign("RECORDID",vtlib_purify($_REQUEST['recordid']));
	}
	
	$query = getListQuery($currentModule,$where_relquery);
}

if($currentModule == 'Products' && $_REQUEST['record_id'] && ($popuptype == 'inventory_prod' || $popuptype == 'inventory_prod_po'))
{
	$product_name = getProductName($_REQUEST['record_id']);
	$smarty->assign("PRODUCT_NAME", $product_name);
	$smarty->assign("RECORD_ID", vtlib_purify($_REQUEST['record_id']));
}
$listview_header_search=getSearchListHeaderValues($focus,"$currentModule",$url_string,$sorder,$order_by);
$smarty->assign("SEARCHLISTHEADER", $listview_header_search);
$smarty->assign("ALPHABETICAL", $alphabetical);

if(isset($_REQUEST['query']) && $_REQUEST['query'] == 'true')
{
	list($where, $ustring) = split("#@@#",getWhereCondition($currentModule));
	$url_string .="&query=true".$ustring;
}

if(isset($where) && $where != '')
{
        $query .= ' and '.$where;
}
//Added to fix the issue #2307 

$order_by = (isset($_REQUEST['order_by'])) ? $adb->sql_escape_string($_REQUEST['order_by']) : $focus->default_order_by;
$sorder = (isset($_REQUEST['sorder']) && $_REQUEST['sorder'] != '') ? $adb->sql_escape_string($_REQUEST['sorder']) : $focus->default_sort_order;

if(isset($order_by) && $order_by != '')
{
        $query .= ' ORDER BY '.$order_by.' '.$sorder;
}

// vtlib customization: To override module specific popup query for a given field
$override_query = false;
if(method_exists($focus, 'getQueryByModuleField')) {
	$override_query = $focus->getQueryByModuleField(vtlib_purify($_REQUEST['srcmodule']), vtlib_purify($_REQUEST['forfield']), vtlib_purify($_REQUEST['forrecord']), $query);
	if($override_query) {
		$query = $override_query;
	}
}
// END

if(PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true){
	$count_result = $adb->query( mkCountQuery( $query));
	$noofrows = $adb->query_result($count_result,0,"count");
}else{
	$noofrows = null;
}

//Retreiving the start value from request
if(isset($_REQUEST['start']) && $_REQUEST['start'] != '') {
	$start = vtlib_purify($_REQUEST['start']);
	if($start == 'last'){
		$count_result = $adb->query( mkCountQuery($query));
		$noofrows = $adb->query_result($count_result,0,"count");
		if($noofrows > 0){
			$start = ceil($noofrows/$list_max_entries_per_page);
		}
	}
	if(!is_numeric($start)){
		$start = 1;
	}elseif($start < 1){
		$start = 1;
	}
	$start = ceil($start);
} else {
	$start = 1;
}
$limstart=($start-1)*$list_max_entries_per_page;
$query.=" LIMIT $limstart,$list_max_entries_per_page";
$list_result = $adb->query($query);

//Retreive the Navigation array
$navigation_array = VT_getSimpleNavigationValues($start, $list_max_entries_per_page,$noofrows);

//Retreive the List View Table Header
$focus->initSortbyField($currentModule);
$focus->list_mode="search";
$focus->popup_type=$popuptype;
$url_string .='&popuptype='.$popuptype;
if(isset($_REQUEST['select']) && $_REQUEST['select'] == 'enable')
	$url_string .='&select=enable';
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != '')
	$url_string .='&return_module='.vtlib_purify($_REQUEST['return_module']);

if($popuptype == 'set_return_emails'){
	$tabid = getTabid($currentModule);
	$mail_arr = getMailFields($tabid); 
	
	if(!empty($mail_arr)){
		$tablename = str_replace("vtiger_","",$mail_arr['tablename']);
		$fieldname = $mail_arr['fieldname'];
		$fieldlabel = $mail_arr['fieldlabel'];
		$focus->search_fields[$fieldlabel] = Array($tablename=>$fieldname);
		$focus->search_fields_name[$fieldlabel] = $fieldname;
	}
}

$listview_header = getSearchListViewHeader($focus,"$currentModule",$url_string,$sorder,$order_by);
$smarty->assign("LISTHEADER", $listview_header);
$smarty->assign("HEADERCOUNT",count($listview_header)+1);

$listview_entries = getSearchListViewEntries($focus,"$currentModule",$list_result,$navigation_array,$form); 
$smarty->assign("LISTENTITY", $listview_entries);

$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string,$currentModule,"Popup");
$smarty->assign("NAVIGATION", $navigationOutput);
$smarty->assign("RECORD_COUNTS", $record_string);
$smarty->assign("POPUPTYPE", $popuptype);
$smarty->assign("PARENT_MODULE", vtlib_purify($_REQUEST['parent_module']));


if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
	$smarty->display("PopupContents.tpl");
else
	$smarty->display("Popup.tpl");

?>
