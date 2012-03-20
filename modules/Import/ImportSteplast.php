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
$req_module=vtlib_purify($_REQUEST['modulename']);

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

// Delete data file used for import 
// http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/5255 
if(isset($_REQUEST['tmp_file'])) { 
	$tmp_file = vtlib_purify($_REQUEST['tmp_file']); 
} else if(isset($_SESSION['tmp_file'])) { 
	$tmp_file = vtlib_purify($_SESSION['tmp_file']); 
} 
if(isset($tmp_file) && file_exists($tmp_file)) unlink($tmp_file); 
// End 
	
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$log->info("Import Step last");

$parenttab = getParenttab();
//This Buttons_List1.tpl is is called to display the add, search, import and export buttons ie., second level tabs
$smarty = new vtigerCRM_Smarty;

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("IMP", $import_mod_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

$smarty->assign("MODULE", vtlib_purify($_REQUEST['modulename']));
$smarty->assign("SINGLE_MOD", vtlib_purify($_REQUEST['modulename']));
$smarty->assign("CATEGORY", vtlib_purify($_SESSION['import_parenttab']));
//@session_unregister("import_parenttab");
if($req_module != 'Accounts' || $req_module != 'Contacts' || $req_module != 'Products' || $req_module != 'Leads' || $req_module != 'HelpDesk' || $req_module != 'Potentials' || $req_module != 'Vendors' )
{
	$smarty->display("Buttons_List1.tpl");
}

if ( isset($_REQUEST['message']))
{
	?>
	<br>

	<table align="center" cellpadding="5" cellspacing="0" width="95%" class="mailClient importLeadUI small">
	   <tr>
		<td height="50" valign="middle" align="left" class="mailClientBg genHeaderSmall">
			 <?php echo $mod_strings['LBL_MODULE_NAME']; ?> <?php echo getTranslatedString($_REQUEST['modulename'],$_REQUEST['modulename']); ?> 
		</td>
	   </tr>
	   <tr><td>&nbsp;</td></tr>
	   <tr>
		<td align="left"  style="padding-left:40px;width:75%;" >
			<?php	if($req_module == 'Contacts' || $req_module == 'Accounts' || $req_module == 'Leads' || $req_module == 'Products' || $req_module == 'HelpDesk' || $req_module == 'Potentials' || $req_module == 'Vendors') 
				{ ?>
					<span class="genHeaderGray"><?php echo $mod_strings['LBL_STEP_4_4']; ?></span>&nbsp; 
			<?php	} 
			        else { ?>
				        <span class="genHeaderGray"><?php echo $mod_strings['LBL_STEP_3_3']; ?></span>&nbsp;		
			<?php	} ?>
			<span class="genHeaderSmall"><?php echo $mod_strings['LBL_MAPPING_RESULTS']; ?></span>
		</td>
	   </tr>	
	   <tr>
		<td style="padding-left:140px;">
			<?php 
				echo vtlib_purify($_REQUEST['message']); 
			?>
		 <br><br><br> 		 </td>
       </tr>
	   <tr>
	     <td class="reportCreateBottom" >
		 <table width="100%" border="0" cellpadding="5" cellspacing="0" >
           <tr>
             <td align="right" valign="top"><form enctype="multipart/form-data" name="Import" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
                 <input type="hidden" name="module" value="<?php echo vtlib_purify($_REQUEST['modulename']); ?>">
                 <input type="hidden" name="action" id="import_action" value="Import">
                 <input type="hidden" name="step" value="1">
                 <input type="hidden" name="return_id" value="<?php echo vtlib_purify($_REQUEST['return_id']); ?>">
                 <input type="hidden" name="return_module" value="<?php echo vtlib_purify($_REQUEST['return_module']); ?>">
                 <input type="hidden" name="return_action" value="<?php echo (($_REQUEST['return_action'] != '')?vtlib_purify($_REQUEST['return_action']):'index'); ?>">
                 <input type="hidden" name="parenttab" id="parenttab" value="<?php echo $parenttab; ?>">
                 <input title="<?php echo $mod_strings['LBL_FINISHED'] ?>" accessKey="" class="crmbutton small save" type="submit" name="button" value="  <?php echo $mod_strings['LBL_FINISHED'] ?>  "  onclick="this.form.action.value=this.form.return_action.value;this.form.return_module.value=this.form.return_module.value;return true;">
                 <input title="<?php echo $mod_strings['LBL_IMPORT_MORE'] ?>" accessKey="" class="crmbutton small save" type="submit" name="button" value="  <?php echo $mod_strings['LBL_IMPORT_MORE'] ?>  "  onclick="this.form.return_module.value=this.form.module.value; return true;">
		<?php 
			//if check added for duplicate records handling -srini	
		if($_REQUEST['dup_type'] == 'manual') { ?>
			 <input name="lastimport" value="<?php echo $mod_strings['LBL_LAST_IMPORT']?>" class="crmbutton small save" type="button" onclick="lastImport('<?php echo $currentModule; ?>','<?php echo $req_module; ?>');">
<?php } ?>
	     </form>
		
		
		
		
		<td align="left">
		 <form name="Import" method="POST" action="index.php">
                 <input type="hidden" name="module" value="<?php echo vtlib_purify($_REQUEST['modulename']); ?>">
                 <input type="hidden" name="action" value="Import">
                 <input type="hidden" name="step" value="undo">
                 <input type="hidden" name="return_module" value="<?php echo vtlib_purify($_REQUEST['return_module']); ?>">
                 <input type="hidden" name="return_id" value="<?php echo vtlib_purify($_REQUEST['return_id']); ?>">
                 <input type="hidden" name="return_action" value="<?php echo vtlib_purify($_REQUEST['return_action']); ?>">
                 <input type="hidden" name="parenttab" value="<?php echo $parenttab; ?>">
                 <input title="<?php echo $mod_strings['LBL_UNDO_LAST_IMPORT']; ?>" accessKey="" class="crmbutton small cancel" type="submit" name="button" value="  <?php echo $mod_strings['LBL_UNDO_LAST_IMPORT'] ?>  ">
             </form></td>
           </tr>
         </table></td>
      </tr>
	</table>
	<?php 
}
//if check added for duplicate records handling -srini
if( $_REQUEST['dup_type'] == 'manual')
{	
	echo "<br>";
	$return_module=vtlib_purify($_REQUEST['modulename']);
	
	$ret_arr=getDuplicateRecordsArr($req_module);
	$fld_values=$ret_arr[0];
	$total_num_group=count($fld_values);
	$fld_name=$ret_arr[1];
	
	$smarty->assign("MODULE",$req_module);
	$smarty->assign("MODULELABEL",getTranslatedString($req_module,$req_module));
	$smarty->assign("NUM_GROUP",$total_num_group);
	$smarty->assign("FIELD_NAMES",$fld_name);
	$smarty->assign("CATEGORY",$parenttab);
	$smarty->assign("ALL_VALUES",$fld_values);
	$smarty->assign("MOD", return_module_language($current_language,$req_module));
	$smarty->assign("IMAGE_PATH",$image_path);
	$smarty->assign("APP", $app_strings);
	$smarty->assign("CMOD", $mod_strings);
	$smarty->assign("MODE",'view'); 
	$smarty->assign("NAVIGATION",$ret_arr["navigation"]);//Added for page navigation
	if(isPermitted($req_module,'Delete','') == 'yes')
		$button_del = $app_strings[LBL_MASS_DELETE];
	$smarty->assign("DELETE",$button_del);
	if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
		$smarty->display("FindDuplicateAjax.tpl");
	else
		$smarty->display('FindDuplicateDisplay.tpl');
}
else
{
	echo "<br><br>";
	$currentModule = "Import";
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

	// vtlib customization: Hook provide to include custom modules
	$module = $_REQUEST['modulename'];
	checkFileAccess("modules/$module/$module.php");
	require_once("modules/$module/$module.php");
	$import_modules_array[$module] = $module;
	// END

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
	
		if($noofrows>=1) 
		{
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
			$smarty->assign("MODULELABEL",getTranslatedString($module_name,$module_name));
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
			
			$smarty->display("ListViewEntries.tpl");
		}
	}
}

unset($_SESSION['import_table_picklist']);
unset($_SESSION['import_converted_picklist_values+95']);
?>