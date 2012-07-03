<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

require_once('include/logging.php');
require_once('modules/CustomView/CustomView.php');
require_once('include/utils/utils.php');
require_once('Smarty_setup.php');
global $mod_strings, $current_language, $default_charset;

require_once('modules/Home/language/'.$current_language.'.lang.php');

$total_record_count = 0;

$query_string = trim($_REQUEST['query_string']);
$curModule = vtlib_purify($_REQUEST['module']);
$search_tag  = vtlib_purify($_REQUEST['search_tag']);

if(isset($query_string) && $query_string != ''){
	// Was the search limited by user for specific modules?
	$search_onlyin = $_REQUEST['search_onlyin'];
	if(!empty($search_onlyin) && $search_onlyin != '--USESELECTED--') {
		$search_onlyin = explode(',', $search_onlyin);
	} else if($search_onlyin == '--USESELECTED--') {
		$search_onlyin = $_SESSION['__UnifiedSearch_SelectedModules__'];
	} else {
		$search_onlyin = array();
	}
	// Save the selection for futur use (UnifiedSearchModules.php)
	$_SESSION['__UnifiedSearch_SelectedModules__'] = $search_onlyin;
	// END
	
	$object_array = getSearchModules($search_onlyin);

	global $adb;
	global $current_user;
	global $theme;
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	
	$search_val = $query_string;
	$search_module = $_REQUEST['search_module'];
	
	if($curModule=='Home') {
		getSearchModulesComboList($search_module);
	}
	$i = 0;
	$moduleRecordCount = array();
	foreach($object_array as $module => $object_name){
		if ($curModule == 'Home' || ($curModule == $module && !empty($_REQUEST['ajax']))) {
			$focus = CRMEntity::getInstance($module);
			if(isPermitted($module,"index") == "yes"){
				$smarty = new vtigerCRM_Smarty;
	
				if(!file_exists("modules/$module/language/".$current_language.".lang.php")) $current_language = 'en_us';
				
				require_once("modules/$module/language/".$current_language.".lang.php");
				global $mod_strings;
				global $app_strings;
	
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				$smarty->assign("THEME", $theme);
				$smarty->assign("IMAGE_PATH",$image_path);
				$smarty->assign("MODULE",$module);
				$smarty->assign("TAG_SEARCH",$search_tag);
				$smarty->assign("SEARCH_MODULE",vtlib_purify($_REQUEST['search_module']));
				$smarty->assign("SINGLE_MOD",$module);
				$smarty->assign("SEARCH_STRING",htmlentities($search_val, ENT_QUOTES, $default_charset));
		
				$listquery = getListQuery($module);
				$oCustomView = '';
	
				$oCustomView = new CustomView($module);
				//Instead of getting current customview id, use cvid of All so that all entities will be found
				//$viewid = $oCustomView->getViewId($module);
				$cv_res = $adb->pquery("select cvid from vtiger_customview where viewname='All' and entitytype=?", array($module));
				$viewid = $adb->query_result($cv_res,0,'cvid');
				$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid);
				
				$listquery = $oCustomView->getModifiedCvListQuery($viewid,$listquery,$module);
	            if ($module == "Calendar"){
	                if (!isset($oCustomView->list_fields['Close'])){
	                	$oCustomView->list_fields['Close']=array ( 'activity' => 'status' );
	                }
	                if (!isset($oCustomView->list_fields_name['Close'])){
	                	$oCustomView->list_fields_name['Close']='status';
	                }
	            }
				
				if($search_module != '' || $search_tag != ''){//This is for Tag search
					$where = getTagWhere($search_val,$current_user->id);					
					$search_msg =  $app_strings['LBL_TAG_SEARCH'];
					$search_msg .=	"<b>".to_html($search_val)."</b>";
				}else{			//This is for Global search
					$where = getUnifiedWhere($listquery,$module,$search_val);
					$search_msg = $app_strings['LBL_SEARCH_RESULTS_FOR'];
					$search_msg .=	"<b>".htmlentities($search_val, ENT_QUOTES, $default_charset)."</b>";
				}
	
				if($where != ''){
					$listquery .= ' and ('.$where.')';
				}
				
				if(!(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')) {
					$count_result = $adb->query($listquery);
					$noofrows = $adb->num_rows($count_result);
				} else {
					$noofrows = vtlib_purify($_REQUEST['recordCount']);
				}
				$moduleRecordCount[$module]['count'] = $noofrows;
				
				global $list_max_entries_per_page;
				if(!empty($_REQUEST['start'])){
					$start = $_REQUEST['start'];
					if($start == 'last'){
						$count_result = $adb->query( mkCountQuery($listquery));
						$noofrows = $adb->query_result($count_result,0,"count");
						if($noofrows > 0){		
							$start = ceil($noofrows/$list_max_entries_per_page);
						}
					}
					if(!is_numeric($start)){
						$start = 1;
					} elseif($start < 0){
						$start = 1;
					}
					$start = ceil($start);
				}else{
					$start = 1;
				}
				
				$navigation_array = VT_getSimpleNavigationValues($start, $list_max_entries_per_page, $noofrows);
				$limitStartRecord = ($navigation_array['start'] - 1) * $list_max_entries_per_page;
				
				if( $adb->dbType == "pgsql"){
					$listquery = $listquery. " OFFSET $limitStartRecord LIMIT $list_max_entries_per_page";
				}else{
				    $listquery = $listquery. " LIMIT $limitStartRecord, $list_max_entries_per_page";
				}
				$list_result = $adb->query($listquery);
				
				$moduleRecordCount[$module]['recordListRangeMessage'] = getRecordRangeMessage($list_result, $limitStartRecord, $noofrows);

				$info_message='&recordcount='.$_REQUEST['recordcount'].'&noofrows='.$_REQUEST['noofrows'].'&message='.$_REQUEST['message'].'&skipped_record_count='.$_REQUEST['skipped_record_count'];
				$url_string = '&modulename='.$_REQUEST['modulename'].'&nav_module='.$module_name.$info_message;
				$viewid = '';
				
				$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string,$module,"UnifiedSearch",$viewid);
				$listview_header = getListViewHeader($focus,$module,"","","","global",$oCustomView);
				$listview_entries = getListViewEntries($focus,$module,$list_result,$navigation_array,"","","","",$oCustomView);
	
				//Do not display the Header if there are no entires in listview_entries
				if(count($listview_entries) > 0){
					$display_header = 1;
				}else{
					$display_header = 0;
				}
				$smarty->assign("NAVIGATION", $navigationOutput);
				$smarty->assign("LISTHEADER", $listview_header);
				$smarty->assign("LISTENTITY", $listview_entries);
				$smarty->assign("DISPLAYHEADER", $display_header);
				$smarty->assign("HEADERCOUNT", count($listview_header));
				$smarty->assign("ModuleRecordCount", $moduleRecordCount);
	
				$total_record_count = $total_record_count + $noofrows;
				
				$smarty->assign("SEARCH_CRITERIA","( $noofrows )".$search_msg);
				$smarty->assign("MODULES_LIST", $object_array);
				$smarty->assign("CUSTOMVIEW_OPTION",$customviewcombo_html);
				
				if(($i != 0 && empty($_REQUEST['ajax'])) || !(empty($_REQUEST['ajax'])))
					$smarty->display("UnifiedSearchAjax.tpl");
				else
					$smarty->display('UnifiedSearchDisplay.tpl');
				unset($_SESSION['lvs'][$module]);
				$i++;
			}
		}
	}
	//Added to display the Total record count
	if(empty($_REQUEST['ajax'])) {
?>
	<script>
document.getElementById("global_search_total_count").innerHTML = " <?php echo $app_strings['LBL_TOTAL_RECORDS_FOUND'] ?><b><?php echo $total_record_count; ?></b>";
	</script>
<?php
	}
}
else {
	echo "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>".$mod_strings['ERR_ONE_CHAR']."</em>";
}

/**	
 * Function to get the the List of Searchable Modules as a combo list which will be displayed in right corner under the Header
 * @param  string $search_module -- search module, this module result will be shown defaultly 
 */
function getSearchModulesComboList($search_module){
	global $object_array;
	global $app_strings;
	global $mod_strings;
	
	?>
		<script>
		function displayModuleList(selectmodule_view){
			<?php
			foreach($object_array as $module => $object_name){
				if(isPermitted($module,"index") == "yes"){
			?>
				   mod = "global_list_"+"<?php echo $module; ?>";
				   if(selectmodule_view.options[selectmodule_view.options.selectedIndex].value == "All")
				   show(mod);
				   else
				   hide(mod);
				<?php
				}
			}
			?>
			
			if(selectmodule_view.options[selectmodule_view.options.selectedIndex].value != "All"){
				selectedmodule="global_list_"+selectmodule_view.options[selectmodule_view.options.selectedIndex].value;
				show(selectedmodule);
			}
		}
		</script>
		 <table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
		     <tr>
		        <td colspan="3" id="global_search_total_count" style="padding-left:30px">&nbsp;</td>
		<td nowrap align="right"><?php echo $app_strings['LBL_SHOW_RESULTS'] ?>&nbsp;
		                <select id="global_search_module" name="global_search_module" onChange="displayModuleList(this);" class="small">
			<option value="All"><?php echo $app_strings['COMBO_ALL'] ?></option>
						<?php
						foreach($object_array as $module => $object_name){
							$selected = '';
							if($search_module != '' && $module == $search_module){
								$selected = 'selected';
							}
							if($search_module == '' && $module == 'All'){
								$selected = 'selected';
							}
							?>
							<?php if(isPermitted($module,"index") == "yes"){
							?> 
							<!-- vtlib customization: Use translation if available -->
							<?php $modulelabel = $module; if($app_strings[$module]) { $modulelabel = $app_strings[$module]; } ?>
							<option value="<?php echo $module; ?>" <?php echo $selected; ?> ><?php echo $modulelabel; ?></option>
							<?php
							}
						}	
						?>
		     		</select>
		        </td>
		     </tr>
		</table>
	<?php
}

/**
 * To get the modules allowed for global search this function returns all the 
 * modules which supports global search as an array in the following structure 
 * array($module_name1=>$object_name1,$module_name2=>$object_name2,$module_name3=>$object_name3,$module_name4=>$object_name4,-----);
 */
function getSearchModules($filter = array()){
	global $adb;
	// vtlib customization: Ignore disabled modules.
	//$sql = 'select distinct vtiger_field.tabid,name from vtiger_field inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid where vtiger_tab.tabid not in (16,29)';
	$sql = 'select distinct vtiger_field.tabid,name from vtiger_field inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid where vtiger_tab.tabid not in (16,29) and vtiger_tab.presence != 1 and vtiger_field.presence in (0,2)';
	// END
	$result = $adb->pquery($sql, array());
	while($module_result = $adb->fetch_array($result)){
		$modulename = $module_result['name'];
		// Do we need to filter the module selection?
		if(!empty($filter) && is_array($filter) && !in_array($modulename, $filter)) {
			continue;
		}
		// END
		if($modulename != 'Calendar'){
			$return_arr[$modulename] = $modulename;
		}else{
			$return_arr[$modulename] = 'Activity';
		}
	}
	return $return_arr;
}

?>