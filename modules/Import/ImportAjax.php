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
include('modules/Import/ImportMap.php');
include('modules/Import/Forms.php');

//This is to delete the map

if($_REQUEST['ajax_action'] == 'check_dup_map_name')
{
	$map_name=$_REQUEST['name'];
	global $adb;
	$query="select * from vtiger_import_maps where deleted=0 and name=?";
	$Result = $adb->pquery($query, array($map_name));
	$noofrows = $adb->num_rows($Result);
	if($noofrows > 0)
		echo "false"; //Map name already exists
	else
		echo "true";
}
else
{

	if($_REQUEST['delete_map'] != '')
	{
		$query = "update vtiger_import_maps set deleted=1 where id = ?";
		$adb->pquery($query, array($_REQUEST['mapping']));
	}

	$mapping_file = new ImportMap();
	$mapping_arr = $mapping_file->getSavedMappingContent($_REQUEST['mapping']);

	$importable_fields = $_SESSION['import_module_object_column_fields'];
	$field_count = $_SESSION['import_module_field_count'];
	$required_fields = $_SESSION['import_module_object_required_fields'];
	$translated_column_fields = $_SESSION['import_module_translated_column_fields'];

	$tablename = '';
	$has_header = $_SESSION['import_has_header'];
	$firstrow = $_SESSION['import_firstrow'];
	$field_map = &$mapping_arr;//$_SESSION['import_field_map'];
	$smarty_array1 = array();

	for($i=0;$i<$field_count;$i++)
	{
		$suggest = '';
		if ($has_header && isset( $field_map[$firstrow[$i]] ) )
		{
			$suggest = $field_map[$firstrow[$i]];
		}
		else if (isset($field_map[$i]))
		{
			$suggest = $field_map[$i];
		}

		$smarty_array1[$i+1] = getFieldSelect(	$importable_fields,
        	                                        $i,
                	                                $required_fields,
                        	                        $suggest,
                                	                $translated_column_fields,
                                        	        $tablename
                                        	      );
	}

	$smarty =  new vtigerCRM_Smarty;
	$smarty->assign("FIRSTROW",$firstrow);
	$smarty->assign("SELECTFIELD",$smarty_array1);

	$smarty->display('ImportMap.tpl');

}

?>
