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
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('modules/Import/ImportLead.php');
require_once('modules/Import/ImportAccount.php');
require_once('modules/Import/ImportContact.php');
require_once('modules/Import/ImportOpportunity.php');
require_once('modules/Import/ImportProduct.php');
require_once('modules/Import/ImportMap.php');
require_once('modules/Import/ImportTicket.php');
require_once('modules/Import/ImportVendors.php');
require_once('modules/Import/UsersLastImport.php');
require_once('modules/Import/parse_utils.php');
require_once('include/ListView/ListView.php');
require_once('include/database/PearDatabase.php');
require_once('modules/Import/ImportSave.php');

function p($str){
	global $adb;
	$adb->println("IMP :".$str);
}

function implode_assoc($inner_delim, $outer_delim, $array){
	$output = array();
	foreach( $array as $key => $item ){
		$output[] = $key . $inner_delim . $item;
	}
	return implode($outer_delim, $output);
}

global $mod_strings;
global $app_list_strings;
global $app_strings;
global $current_user;
global $import_file_name;
global $theme;
global $upload_maxsize;
global $site_URL;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$log->info("Upload Step 3");

include("include/saveMergeCriteria.php");

$delimiter = ',';
// file handle
$count = 0;
$error = "";
$col_pos_to_field = array();
$header_to_field = array();
$field_to_pos = array();
$focus = 0;
$current_bean_type = "";
$id_exists_count = 0;
$broken_ids = 0;

$delimiter = $_SESSION['import_delimiter'];

$has_header = 0;

if(isset( $_REQUEST['has_header']) && $_REQUEST['has_header'] == 'on'){
	$has_header = 1;
}

if($_REQUEST['modulename'] != ''){
	$_REQUEST['module'] = vtlib_purify($_REQUEST['modulename']);
}

$import_object_array = Array(
				"Leads"=>"ImportLead",
				"Accounts"=>"ImportAccount",
				"Contacts"=>"ImportContact",
				"Potentials"=>"ImportOpportunity",
				"Products"=>"ImportProduct",
				"HelpDesk"=>"ImportTicket",
                "Vendors"=>"ImportVendors"
			    );

if(isset($_REQUEST['module']) && $_REQUEST['module'] != ''){
	$current_bean_type = $import_object_array[$_REQUEST['module']];
	// vtlib customization: Hook added to enable import for un-mapped modules
	$module = $_REQUEST['module'];	
	if($current_bean_type == null) {
		checkFileAccess("modules/$module/$module.php");
		require_once("modules/$module/$module.php");
		$current_bean_type = $module;
		$callInitImport = true;		
	}
	// END
}else{
	$current_bean_type = "ImportContact";
}

$focus = new $current_bean_type();
// vtlib customization: Call the import initializer
if($callInitImport) $focus->initImport($module);
// END

//Constructing the custom vtiger_field Array
require_once('include/CustomFieldUtil.php');
$custFldArray = getCustomFieldArray($_REQUEST['module']);
p("IMP 3: custFldArray");
p($custFldArray);

//Initializing  an empty Array to store the custom vtiger_field Column Name and Value
$resCustFldArray = Array();

p("Getting from request");
// loop through all request variables
foreach ($_REQUEST as $name=>$value){
	p("name=".$name." value=".$value);
	// only look for var names that start with "colnum"
	if ( strncasecmp( $name, "colnum", 6) != 0 ){
		continue;
	}
	if ($value == "-1"){
		continue;
	}

	$user_field = $value;
	$pos = substr($name,6);

	if ( isset( $field_to_pos[$user_field]) ){
		show_error_import($mod_strings['LBL_ERROR_MULTIPLE']);
		exit;
	}

	p("user_field=".$user_field." if=".$focus->importable_fields[$user_field]);
	
	if ( isset( $focus->importable_fields[$user_field] ) || isset( $custFldArray[$user_field] )){
		p("user_field SET=".$user_field);
		$field_to_pos[$user_field] = $pos;
		$col_pos_to_field[$pos] = $user_field;
	}
}

p("field_to_pos");
$adb->println($field_to_pos);
p("col_pos_to_field");
$adb->println($col_pos_to_field);

$max_lines = -1;
$ret_value = 0;

if(isset($_REQUEST['tmp_file'])) { 
	$_SESSION['tmp_file'] = vtlib_purify($_REQUEST['tmp_file']); 
} else { 
	$_REQUEST['tmp_file'] = vtlib_purify($_SESSION['tmp_file']); 
} 
// End 

if ($_REQUEST['source'] == 'act'){
    $ret_value = parse_import_act($_REQUEST['tmp_file'],$delimiter,$max_lines,$has_header);
}else{
	$ret_value = parse_import($_REQUEST['tmp_file'],$delimiter,$max_lines,$has_header);
}

$datarows = $ret_value['rows'];

$ret_field_count = $ret_value['field_count'];

//we have to get all picklist entries and add with the corresponding picklist table
if(isset($datarows) && is_array($datarows)){
	//This file will be included only once at the first time. Will not be included when we redirect from ImportSave
	include("modules/Import/picklist_addition.php");
}

$saved_ids = array();

$firstrow = 0;

if (! isset($datarows)){
	$error = $mod_strings['LBL_FILE_ALREADY_BEEN_OR'];
	$datarows = array();
}

if ($has_header == 1){
	$firstrow = array_shift($datarows);
}

//Mark the last imported records as deleted which are imported by the current user in vtiger_users_last_import vtiger_table
if(!isset($_REQUEST['startval'])){
	$seedUsersLastImport = new UsersLastImport();
	$seedUsersLastImport->mark_deleted_by_user_id($current_user->id);
}
$skip_required_count = 0;

p("processing started ret_field_count=".$ret_field_count);
$adb->println($datarows);

$error = '';
$focus = new $current_bean_type();
$focus->initRequiredFields($module);

// SAVE MAPPING IF REQUESTED
if(isset($_REQUEST['save_map']) && $_REQUEST['save_map'] == 'on' && isset($_REQUEST['save_map_as']) && $_REQUEST['save_map_as'] != ''){
	p("save map");
	$serialized_mapping = '';

	if( $has_header){
		foreach($col_pos_to_field as $pos=>$field_name){
			if ( isset($firstrow[$pos]) &&  isset( $field_name)){
				$header_to_field[ $firstrow[$pos] ] = $field_name;
			}
		}
		$serialized_mapping = implode_assoc("=","&",$header_to_field);
	}else{
		$serialized_mapping = implode_assoc("=","&",$col_pos_to_field);
	}

	$mapping_file_name = $_REQUEST['save_map_as'];
	$mapping_file = new ImportMap();

	$result = $mapping_file->save_map( $current_user->id,
					$mapping_file_name,
					$_REQUEST['module'],
					$has_header,
					$serialized_mapping );

	$adb->println("Save map done");
	$adb->println($result);
}
//save map - ends

if(isset($_SESSION['totalrows']) && $_SESSION['totalrows'] != ''){
	$xrows = $_SESSION['totalrows'];
}else{
	$xrows = $datarows;
}
if(isset($_SESSION['return_field_count'])){
	$ret_field_count = $_SESSION['return_field_count'];
}
if(isset($_SESSION['column_position_to_field'])){
	$col_pos_to_field = $_SESSION['column_position_to_field'];
}
if($xrows != ''){
	$datarows = $xrows;
}
if($_REQUEST['skipped_record_count'] != ''){
	$skipped_record_count = vtlib_purify($_REQUEST['skipped_record_count']);
}else{
	$_REQUEST['skipped_record_count'] = 0;
}

if($_REQUEST['noofrows'] != ''){
	$totalnoofrows = vtlib_purify($_REQUEST['noofrows']);
}else{
	$totalnoofrows = count($datarows);
}

if($_REQUEST['recordcount'] != ''){
        $RECORDCOUNT = vtlib_purify($_REQUEST['recordcount']);
}else{
        $RECORDCOUNT = vtlib_purify($_SESSION['recordcount']);
}
	
if($_REQUEST['startval'] != ''){
	$START = vtlib_purify($_REQUEST['startval']);
}else{
	$START = vtlib_purify($_SESSION['startval']);
}

if(($START+$RECORDCOUNT) > $totalnoofrows){
        $RECORDCOUNT = $totalnoofrows - $START;
}

$loopcount = ($totalnoofrows/$RECORDCOUNT)+1;

$focus->initImportableFields($module);
if($totalnoofrows > $RECORDCOUNT && $START < $totalnoofrows){
	$rows1 = Array();
	for($j=$START;$j<$START+$RECORDCOUNT;$j++){
		$rows1[] = $datarows[$j];
	}

	$res = InsertImportRecords($datarows,$rows1,$focus,$ret_field_count,$col_pos_to_field,$START,$RECORDCOUNT,vtlib_purify($_REQUEST['module']),$totalnoofrows,$skipped_record_count);
	if($START != 0){
		echo '<b>'.$res.'</b>';
	}
	
	$count = vtlib_purify($_REQUEST['count']);
}else{
	if($START == 0){
		$res = InsertImportRecords($datarows,$datarows,$focus,$ret_field_count,$col_pos_to_field,$START,$totalnoofrows,vtlib_purify($_REQUEST['module']),$totalnoofrows,$skipped_record_count);
	}
}

//Display the imported records message
echo "<div align='center' width='100%'><font color='green'><b>".$_SESSION['import_display_message']."</b></font></div>";
?>
