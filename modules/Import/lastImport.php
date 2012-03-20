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
 * Description:  TODO: To be written.
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('modules/Import/ImportContact.php');
require_once('modules/Import/ImportAccount.php');
require_once('modules/Import/ImportOpportunity.php');
require_once('modules/Import/ImportLead.php');
//Pavani: Import this file to Support Imports for Trouble tickets and vendors
require_once('modules/Import/ImportTicket.php');
require_once('modules/Import/ImportVendors.php');
require_once('modules/Import/UsersLastImport.php');
require_once('modules/Import/parse_utils.php');
require_once('include/ListView/ListView.php');
require_once('modules/Contacts/Contacts.php');
require_once('include/utils/utils.php');

global $mod_strings;
global $app_list_strings;
global $app_strings;
global $current_user;
$currentModule = "Import";

if (! isset( $_REQUEST['module']))
{
	$_REQUEST['module'] = 'Home';
}

if (! isset( $_REQUEST['return_id']))
{
	$_REQUEST['return_id'] = '';
}
if (! isset( $_REQUEST['return_module']))
{
	$_REQUEST['return_module'] = '';
}

if (! isset( $_REQUEST['return_action']))
{
	$_REQUEST['return_action'] = '';
}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$log->info("Import Step last");

$parenttab = getParenttab();
//This Buttons_List1.tpl is is called to display the add, search, import and export buttons ie., second level tabs
$smarty = new vtigerCRM_Smarty;

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("IMP", $import_mod_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

$smarty->assign("MODULE", vtlib_purify($_REQUEST['req_mod']));
$smarty->assign("SINGLE_MOD", vtlib_purify($_REQUEST['modulename']));
$smarty->assign("CATEGORY", vtlib_purify($_SESSION['import_parenttab']));

global $limit;
global $list_max_entries_per_page;

$implict_account = false;

$import_modules_array = Array(
				"Leads"=>"Leads",
				"Accounts"=>"Accounts",
				"Contacts"=>"Contacts",
				"Potentials"=>"Potentials",
				"Products"=>"Products",
				"HelpDesk"=>"ImportTicket",
                "Vendors"=>"ImportVendors" 
			     );
			     
if(!empty($_REQUEST['req_mod'])) {
	$req_mod = $_REQUEST['req_mod'];
	checkFileAccess("modules/$req_mod/$req_mod.php");
	require_once("modules/$req_mod/$req_mod.php");
	if(!isset($import_modules_array[$req_mod])) {
		$import_modules_array[$req_mod] = $req_mod;
	}
}

foreach($import_modules_array as $module_name => $object_name)
{

	$seedUsersLastImport = new UsersLastImport();
	$seedUsersLastImport->bean_type = $module_name;
	$list_query = $seedUsersLastImport->create_list_query($o,$w);
	$current_module_strings = return_module_language($current_language, $module_name);

	$object = new $object_name();
	$seedUsersLastImport->list_fields = $object->list_fields;

	$list_result = $adb->query($list_query);
	//Retreiving the no of rows
	$noofrows = $adb->num_rows($list_result);

	if($noofrows < 1) {
		if($module_name == $_REQUEST['req_mod']) {
			echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";
			echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
			echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
		
				<table border='0' cellpadding='5' cellspacing='0' width='98%'>
				<tbody><tr>
				<td rowspan='2' width='11%'><img src='". vtiger_imageurl('empty.jpg', $theme) ."' ></td>
				<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
					<span class='genHeaderSmall'>$app_strings[LBL_NO] $mod_strings[LBL_LAST_IMPORTED] $app_strings[$module_name]</span></td>
				</tr>
				</tbody></table>
				</div>";
			echo "</td></tr></table>";
		}
	} else  {
		if($module_name != 'Accounts')
		{
			$implict_account=true;
		}

		if($module_name == 'Accounts' && $implict_account==true)
			$display_header_msg = "Newly created Accounts";
		else
			$display_header_msg = "".$mod_strings['LBL_LAST_IMPORTED']." ".$app_strings[$module_name]."";
		
		//Display the Header Message	
		echo "
			<table width='100%' border='0' cellpadding='5' cellspacing='0'>
			   <tr>
				<td class='dvtCellLabel' align='left'>
					<b>".$mod_strings['LBL_LAST_IMPORTED']." ".$app_strings[$module_name]." </b>
				</td>
			   </tr>
			</table>
		      ";

		$smarty = new vtigerCRM_Smarty;

		$smarty->assign("MOD", $mod_strings);
		$smarty->assign("APP", $app_strings);
		$smarty->assign("IMAGE_PATH",$image_path);
		$smarty->assign("MODULE",$module_name);
		$smarty->assign("SINGLE_MOD",$module_name);
		$smarty->assign("SHOW_MASS_SELECT",'false');

		//Retreiving the start value from request
		if($module_name == $_REQUEST['nav_module'] && isset($_REQUEST['start']) && $_REQUEST['start'] != '') {
			$start = vtlib_purify($_REQUEST['start']);
		} else {
			$start = 1;
		}

		$info_message='&recordcount='.vtlib_purify($_REQUEST['recordcount']).'&noofrows='.vtlib_purify($_REQUEST['noofrows']).'&message='.vtlib_purify($_REQUEST['message']).'&skipped_record_count='.vtlib_purify($_REQUEST['skipped_record_count']);
		$url_string = '&modulename='.vtlib_purify($_REQUEST['modulename']).'&nav_module='.$module_name.$info_message;
		$viewid = '';

		//Retreive the Navigation array
		$navigation_array = getNavigationValues($start, $noofrows, $list_max_entries_per_page);
		$navigationOutput = getTableHeaderNavigation($navigation_array, $url_string,"Import","ImportSteplast",$viewid);

		//Retreive the List View Header and Entries
		$listview_header = getListViewHeader($object,$module_name);
		$listview_entries = getListViewEntries($object,$module_name,$list_result,$navigation_array,"","","EditView","Delete","");
		//commented to remove navigation buttons from import list view
		//$smarty->assign("NAVIGATION", $navigationOutput);
		$smarty->assign("HIDE_CUSTOM_LINKS", 1);//Added to hide the CustomView links in imported records ListView
		
		// Remove all the links for the list view header as they do not work in this page.
    	for($i=0;$i<count($listview_header);$i++) {
    		$listview_header[$i] = strip_tags($listview_header[$i]);
    	}
		$smarty->assign("LISTHEADER", $listview_header);
		$smarty->assign("LISTENTITY", $listview_entries);
        
		// Include required scripts   
		echo '<link rel="stylesheet" type="text/css" href="'.$theme_path.'/style.css">';
		echo '<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>';
		echo '<script language="JavaScript" type="text/javascript" src="include/js/' . $_SESSION['authenticated_user_language'] . '.lang.js?' . $_SESSION['vtiger_version'] . '"></script>';
		echo '<script language="JavaScript" type="text/javascript" src="modules/'. vtlib_purify($_REQUEST['req_mod']) . '/' . vtlib_purify($_REQUEST['req_mod']) . '.js"></script>';
		echo '<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>';
		
		$smarty->display("ListViewEntries.tpl");
	}
}

?>