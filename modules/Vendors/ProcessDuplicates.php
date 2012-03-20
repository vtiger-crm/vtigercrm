<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once('Smarty_setup.php');
require_once('database/DatabaseConnection.php');
require_once('modules/Users/Users.php');
require_once('include/utils/utils.php');

$module = vtlib_purify($_REQUEST['module']);
$focus = CRMEntity::getInstance($module);

global $mod_strings, $app_strings, $app_list_strings;
global $current_language, $currentModule, $theme;
global $adb;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$mode = $_REQUEST['mergemode'];

if($mode == 'mergesave') {

	$return_module=vtlib_purify($_REQUEST['return_module']);
	$action=vtlib_purify($_REQUEST['action']);
	$return_action=vtlib_purify($_REQUEST['return_action']);
	$parenttab=vtlib_purify($_REQUEST['parent']);
	$merge_id=vtlib_purify($_REQUEST['record']);
	$recordids=vtlib_purify($_REQUEST['pass_rec']);
	
	$result =  $adb->pquery("SELECT count(*) AS count FROM vtiger_crmentity WHERE crmid=? and deleted=0", array($merge_id));
	$count = $adb->query_result($result,0,'count');
	
	if($count > 0)
	{	
		// First, save the primary record
		$focus->mode="edit";
		setObjectValuesFromRequest($focus);
		$focus->save($module);
		$rec_values=$focus->column_fields;
		
		// Remove the id of primary record from the list of records to be deleted.
		$del_value=explode(",",$recordids,-1);
		$offset = array_search($merge_id,$del_value);
		unset($del_value[$offset]);
		
		// Transfer the related lists of the records to be deleted, to the primary record's related list
		if(method_exists($focus, 'transferRelatedRecords')){
			$focus->transferRelatedRecords($module,$del_value,$merge_id);
		} else {
			transferRelatedRecords($module,$del_value,$merge_id);
		}
		
		// Delete the records by id specified in the list
		foreach($del_value as $value)
		{
			DeleteEntity($_REQUEST['module'],$_REQUEST['return_module'],$focus,$value,"");
		}
	}
	
	?>
	<script>
		window.self.close();window.opener.location.href=window.opener.location.href;
	</script>
<?php
} elseif ($mode == 'mergefields') {
	
	$idstring=vtlib_purify($_REQUEST['passurl']);
	$parent_tab=getParentTab();

	$exploded_id=explode(",",$idstring,-1);
	$record_count = count($exploded_id);
	
	$smarty = new vtigerCRM_Smarty;
	$smarty->assign("EDIT_DUPLICATE","");
	if($record_count == 2) {
		if(isPermitted($currentModule,"EditView",$exploded_id[0]) == 'yes' && isPermitted($currentModule,"EditView",$exploded_id[1]) == 'yes'
			 && isPermitted($currentModule,"Delete",$exploded_id[0]) == 'yes' && isPermitted($currentModule,"Delete",$exploded_id[1]) == 'yes')
		$smarty->assign("EDIT_DUPLICATE","permitted");
	}
	else {
		if(isPermitted($currentModule,"EditView",$exploded_id[0]) == 'yes' && isPermitted($currentModule,"EditView",$exploded_id[1]) == 'yes' && isPermitted($currentModule,"EditView",$exploded_id[2]) == 'yes'
			&& isPermitted($currentModule,"Delete",$exploded_id[0]) == 'yes' && isPermitted($currentModule,"Delete",$exploded_id[1]) == 'yes' && isPermitted($currentModule,"Delete",$exploded_id[2]) == 'yes')
		$smarty->assign("EDIT_DUPLICATE","permitted");
	}
	
	$all_values_array=getRecordValues($exploded_id,$module);
	$all_values=$all_values_array[0];
	$js_arr_val=$all_values_array[1];
	$fld_array=$all_values_array[2];
	$js_arr=implode(",",$js_arr_val);
	
	$imported_records = Array();
	$sql="select bean_id from vtiger_users_last_import where bean_type=? and deleted=0";
	$result = $adb->pquery($sql, array($module));
	$num_rows=$adb->num_rows($result);
	$count=0;
	for($i=0; $i<$num_rows;$i++)
	{
		foreach($exploded_id as $value)
			if($value == $adb->query_result($result,$i,"bean_id"))
				$count++;
		array_push($imported_records,$adb->query_result($result,$i,"bean_id"));
	}
	
	if ($record_count == $count)
		$no_existing=1;
	else
		$no_existing=0;
	
	$smarty->assign("MOD", $mod_strings);
	$smarty->assign("APP", $app_strings);
	$smarty->assign("RECORD_COUNT",$record_count);
	$smarty->assign("THEME", $theme);
	$smarty->assign("IMAGE_PATH", $image_path);
	$smarty->assign("MODULENAME", $module);
	$smarty->assign("PARENT_TAB", $parent_tab);
	$smarty->assign("JS_ARRAY", $js_arr);
	$smarty->assign("ID_ARRAY", $exploded_id);
	$smarty->assign("IDSTRING",$idstring);
	$smarty->assign("ALLVALUES", $all_values);
	$smarty->assign("FIELD_ARRAY", $fld_array);
	$smarty->assign("IMPORTED_RECORDS", $imported_records);
	$smarty->assign("NO_EXISTING", $no_existing);
	$smarty->display("MergeFields.tpl");	
}

?>
