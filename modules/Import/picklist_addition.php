<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *******************************************************************************/

global $adb;
$tabid = getTabid($_REQUEST['module']);

//First we have to collect all available picklist and their values from the corresponding picklist tables
$picklist_result = $adb->pquery("select fieldname from vtiger_field where uitype in ('15') and tabid=? and vtiger_field.presence in (0,2)", array($tabid));
$no_of_picklists = $adb->num_rows($picklist_result);
for($i=0;$i<$no_of_picklists;$i++)
{
	$fieldname = $adb->query_result($picklist_result, $i, 'fieldname');
	$tablename = "vtiger_".$fieldname;
	$picklist_result2 = $adb->query("select * from $tablename");
	$available_picklists[] = $fieldname;
	//Now get all picklist values
	for($j=0;$j<$adb->num_rows($picklist_result2);$j++)
	{
		$table_picklist[$fieldname][$j]=$adb->query_result($picklist_result2, $j, $fieldname);
		$converted_table_picklist_values[$fieldname][$j] = strtolower($adb->query_result($picklist_result2, $j, $fieldname));
	}
}

$csv_picklist_values = array();
//Collect all picklist values from csv file
foreach($field_to_pos as $fieldname => $ind)
{
	if(in_array($fieldname,$available_picklists))
	{
		$adb->println("Picklist - $fieldname is mapped.");
		$picklist_pos[$fieldname] = $ind;
		for($i=1;$i<count($datarows);$i++)
		{
			$csv_picklist_values[$fieldname][] = $datarows[$i][$ind];
		}
		//Remove the repeated entries and make this array with unique entries
		
		$csv_picklist_values[$fieldname] = array_unique($csv_picklist_values[$fieldname]);
	}
	
}

//Now we have to add the CSV picklists in the picklist table if it is not exist
foreach($csv_picklist_values as $fieldname => $temp_array)
{
	$tablename = "vtiger_$fieldname";
	
	foreach($temp_array as $ind => $picklist_value)
	{
		$pick_val = strtolower($picklist_value);	
		//Check whether $picklist_value is exist in the array of available picklist entries
		if(!in_array($pick_val, $converted_table_picklist_values[$fieldname]))
		{

			//Not exist, so we have to add this $picklist_value in $fieldname(picklist name) table
			$picklist_value = addslashes($picklist_value);

			$adb->println("$picklist_value has to be added in the table $tablename");

			$cfId=$adb->getUniqueID($tablename);
			$unique_picklist_value = getUniquePicklistID();
			$qry="insert into $tablename values(?,?,?,?)";
			$adb->pquery($qry, array($cfId,$picklist_value,1,$unique_picklist_value));
			//added to fix ticket#4492
			$picklistId_qry = "select picklistid from vtiger_picklist where name=?";
			$picklistId_res = $adb->pquery($picklistId_qry,array($fieldname));
			$picklist_Id = $adb->query_result($picklistId_res,0,'picklistid');

			$role_id = $current_user->roleid;
			$sort_qry = "select max(sortid)+1 as sortid from vtiger_role2picklist where picklistid=? and roleid=?";
			$sort_qry_res = $adb->pquery($sort_qry,array($picklist_Id,$role_id));
			$sort_id = $adb->query_result($sort_qry_res,0,'sortid');
			$role_picklist = "insert into vtiger_role2picklist values (?,?,?,?)";
			$adb->pquery($role_picklist,array($role_id,$unique_picklist_value,$picklist_Id,$sort_id));
		}


	}
	
}

?>
