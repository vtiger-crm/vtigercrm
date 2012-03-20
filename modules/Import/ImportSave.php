<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

require_once('data/CRMEntity.php');
	
$count = 0;
$skip_required_count = 0;

/**	function used to save the records into database
 *	@param array $rows - array of total rows of the csv file
 *	@param array $rows1 - rows to be saved
 *	@param object $focus - object of the corresponding import module
 *	@param int $ret_field_count - total number of fields(columns) available in the csv file
 *	@param int $col_pos_to_field - field position in the mapped array
 *	@param int $start - starting row count value to import
 *	@param int $recordcount - count of records to be import ie., number of records to import
 *	@param string $module - import module
 *	@param int $totalnoofrows - total number of rows available
 *	@param int $skip_required_count - number of records skipped
 This function will redirect to the ImportStep3 if the available records is greater than the record count (ie., number of records import in a single loop) otherwise (total records less than 500) then it will be redirected to import step last
 */
function InsertImportRecords($rows,$rows1,$focus,$ret_field_count,$col_pos_to_field,$start,$recordcount,$module,$totalnoofrows,$skip_required_count)
{
	global $current_user;
	global $adb;
	global $mod_strings;
	global $dup_ow_count;
	global $process_fields;

	// MWC ** Getting vtiger_users
	$temp = get_user_array(FALSE);
	foreach ( $temp as $key=>$data)
	$users_groups_list[$data] = $key;

	$temp = get_group_array(FALSE);
	foreach ( $temp as $key=>$data)
	$users_groups_list[$data] = $key;

	p(print_r(users_groups_list,1));
	$adb->println("Users List : ");
	$adb->println($users_groups_list);
	$dup_count = 0;
	$count = 0;
	$dup_ow_count = 0;
	$process_fields='false';
	if($start == 0)
	{
		$_SESSION['totalrows'] = $rows;
		$_SESSION['return_field_count'] = $ret_field_count;
		$_SESSION['column_position_to_field'] = $col_pos_to_field;
	}
	$ii = $start;
	// go thru each row, process and save()
	foreach ($rows1 as $row)
	{
		$adb->println("Going to Save the row ".$ii." =====> ");
		$adb->println($row);
		global $mod_strings;

		$do_save = 1;
		//MWC
		$my_userid = $current_user->id;

		//If we want to set default values for some fields for each entity then we have to set here
		if($module == 'Products' || $module == 'Services')//discontinued is not null. if we unmap active, NULL will be inserted and query will fail
		$focus->column_fields['discontinued'] = 'on';

		for($field_count = 0; $field_count < $ret_field_count; $field_count++)
		{
			p("col_pos[".$field_count."]=".$col_pos_to_field[$field_count]);

			if ( isset( $col_pos_to_field[$field_count]) )
			{
				p("set =".$field_count);
				if (! isset( $row[$field_count]) )
				{
					continue;
				}

				p("setting");

				// TODO: add check for user input
				// addslashes, striptags, etc..
				$field = $col_pos_to_field[$field_count];

				//picklist function is added to avoid duplicate picklist entries
				$pick_orginal_val = getPicklist($field,$row[$field_count]);

				if($pick_orginal_val != null)
				{
					$focus->column_fields[$field]=$pick_orginal_val;
				}
				//MWC
				elseif ( $field == "assignedto" || $field == "assigned_user_id" )
				{
					//Here we are assigning the user id in column fields, so in function assign_user (ImportLead.php and ImportProduct.php files) we should use the id instead of user name when query the user
					//or we can use $focus->column_fields['smownerid'] = $users_groups_list[$row[$field_count]];
					$focus->column_fields[$field] = $users_groups_list[trim($row[$field_count])];
					p("setting my_userid=$my_userid for user=".$row[$field_count]);
				}
				else
				{
					//$focus->$field = $row[$field_count];
					$focus->column_fields[$field] = $row[$field_count];
					p("Setting ".$field."=".$row[$field_count]);
				}
					
			}

		}
		if($focus->column_fields['notify_owner'] == '')
		{
			$focus->column_fields['notify_owner'] = '0';
		}
		if($focus->column_fields['reference'] == '')
		{
			$focus->column_fields['reference'] = '0';
		}
		if($focus->column_fields['emailoptout'] == '')
		{
			$focus->column_fields['emailoptout'] = '0';
		}
		if($focus->column_fields['donotcall'] == '')
		{
			$focus->column_fields['donotcall'] = '0';
		}
		if($focus->column_fields['discontinued'] == '')
		{
			$focus->column_fields['discontinued'] = '0';
		}
		if($focus->column_fields['active'] == '')
		{
			$focus->column_fields['active'] = '0';
		}
		p("setting done");

		p("do save before req vtiger_fields=".$do_save);

		$adb->println($focus->required_fields);
		foreach ($focus->required_fields as $field=>$notused)
		{
			$fv = trim($focus->column_fields[$field]);
			if (! isset($fv) || $fv == '')
			{
				// Leads Import does not allow an empty lastname because the link is created on the lastname
				// Without lastname the Lead could not be opened.
				// But what if the import file has only company and telefone information?
				// It would be stupid to skip all the companies which don't have a contact person yet!
				// So we set lastname ="?????" and the user can later enter a name.
				// So the lastname is still mandatory but may be empty.
				if ($field == 'lastname' && $module == 'Leads')
				{
					$focus->column_fields[$field] = '?????';
				}
				else
				{
					p("fv ".$field." not set");
					$do_save = 0;
					$skip_required_count++;
					break;
				}
			}
		}

		if ( ! isset($focus->column_fields["assigned_user_id"]) || $focus->column_fields["assigned_user_id"]==='' || $focus->column_fields["assigned_user_id"]===NULL) {
			$focus->column_fields["assigned_user_id"] = $my_userid;
		}

		//added for duplicate handling
		if(is_record_exist($module,$focus))
		{
			if($do_save != 0)
			{
				$do_save = 0;
				$dup_count++;
			}
		}
		p("do save=".$do_save);

		if ($do_save)
		{
			p("saving..");

			if ( ! isset($focus->column_fields["assigned_user_id"]) || $focus->column_fields["assigned_user_id"]=='')
			{
				//$focus->column_fields["assigned_user_id"] = $current_user->id;
				//MWC
				$focus->column_fields["assigned_user_id"] = $my_userid;
			}
			
			//handle uitype 10
			foreach($focus->importable_fields as $fieldname=>$uitype){
				$uitype = $focus->importable_fields[$fieldname];
				if($uitype == 10){
					//added to handle security permissions for related modules :: for e.g. Accounts/Contacts in Potentials
					if(method_exists($focus, "add_related_to")){
						if(!$focus->add_related_to($module, $fieldname)){
							if(array_key_exists($fieldname, $focus->required_fields)){
								$do_save = 0;
								$skip_required_count++;
								continue 2;
							}
						}
					}
				}
			}
			
			// now do any special processing for ex., map account with contact and potential
			if($process_fields == 'false'){
				$focus->process_special_fields();
			}
			$focus->saveentity($module);
			//$focus->saveentity($module);
			$return_id = $focus->id;

			$last_import = new UsersLastImport();
			$last_import->assigned_user_id = $current_user->id;
			$last_import->bean_type = $_REQUEST['module'];
			$last_import->bean_id = $focus->id;
			$last_import->save();
			$count++;
		}
		$ii++;
	}

	$_REQUEST['count'] = $ii;
	if(isset($_REQUEST['module']))
	$modulename = vtlib_purify($_REQUEST['module']);

	$end = $start+$recordcount;
	$START = $start + $recordcount;
	$RECORDCOUNT = $recordcount;
	$dup_check_type = $_REQUEST['dup_type'];
	$auto_dup_type = $_REQUEST['auto_type'];

	if($end >= $totalnoofrows) {
		$module = 'Import';//$_REQUEST['module'];
		$action = 'ImportSteplast';
		//exit;
		$imported_records = $totalnoofrows - $skip_required_count;
		if($imported_records == $totalnoofrows) {
			$skip_required_count = 0;
		}
		 if($dup_check_type == "auto") {
			 if($auto_dup_type == "ignore") {
			 	$dup_info = $mod_strings['Duplicate_Records_Skipped_Info'].$dup_count;
			 	$imported_records -= $dup_count;
			 }
			 else if($auto_dup_type == "overwrite") {
			 	$dup_info = $mod_strings['Duplicate_Records_Overwrite_Info'].$dup_ow_count;
			 	$imported_records -= $dup_ow_count;
			 }
		 }
		 else
		 	$dup_info = "";
		 
		 if($imported_records < 0) $imported_records = 0;
	
		 $message= urlencode("<b>".$mod_strings['LBL_SUCCESS']."</b>"."<br><br>" .$mod_strings['LBL_SUCCESS_1']."  $imported_records " .$mod_strings['of'].' '.$totalnoofrows."<br><br>" .$mod_strings['LBL_SKIPPED_1']."  $skip_required_count <br><br>".$dup_info );
	} else {
		$module = 'Import';
		$action = 'ImportStep3';
	}
?>

<script>
setTimeout("b()",1000);
function b()
{
	document.location.href="index.php?action=<?php echo $action?>&module=<?php echo $module?>&modulename=<?php echo $modulename?>&startval=<?php echo $end?>&recordcount=<?php echo $RECORDCOUNT?>&noofrows=<?php echo $totalnoofrows?>&message=<?php echo $message?>&skipped_record_count=<?php echo $skip_required_count?>&parenttab=<?php echo vtlib_purify($_SESSION['import_parenttab'])?>&dup_type=<?php echo $dup_check_type?>&auto_type=<?php echo $auto_dup_type?>";
}
</script>

<?php
	$_SESSION['import_display_message'] = '<br>'.$start.' '.$mod_strings['to'].' '.$end.' '.$mod_strings['of'].' '.$totalnoofrows.' '.$mod_strings['are_imported_succesfully'];
	//return $_SESSION['import_display_message'];
}

function is_record_exist($module,$focus)
{
	global $adb;
	global $dup_ow_count;
	$dup_check_type = $_REQUEST['dup_type'];
	$auto_dup_type = "";
	$sec_parameter = "";
	if($dup_check_type == 'auto')
	{
		$auto_dup_type = $_REQUEST['auto_type'];
	}
	if($auto_dup_type == "ignore")
	{
		$sec_parameter = getSecParameterforMerge($module);
		if($module == "Leads")
		{
			$sel_qry = "select count(*) as count from vtiger_leaddetails
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_leaddetails.leadid
			inner join vtiger_leadsubdetails on vtiger_leaddetails.leadid = vtiger_leadsubdetails.leadsubscriptionid
			inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid = vtiger_leaddetails.leadid
			left join vtiger_leadscf on vtiger_leadscf.leadid = vtiger_leaddetails.leadid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			where vtiger_crmentity.deleted = 0 AND vtiger_leaddetails.converted = 0 $sec_parameter";
		}
		else if($module == "Accounts")
		{
			$sel_qry = "SELECT count(*) as count FROM vtiger_account
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid
			INNER JOIN vtiger_accountbillads ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid
			INNER JOIN vtiger_accountshipads ON vtiger_account.accountid = vtiger_accountshipads.accountaddressid
			LEFT JOIN vtiger_accountscf ON vtiger_account.accountid = vtiger_accountscf.accountid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 $sec_parameter";
		}
		else if($module == "Contacts")
		{
			$sel_qry = "SELECT count(*) as count FROM vtiger_contactdetails
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			INNER JOIN vtiger_contactaddress ON vtiger_contactaddress.contactaddressid = vtiger_contactdetails.contactid
			INNER JOIN vtiger_contactsubdetails ON vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid
			LEFT JOIN vtiger_contactscf ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
			LEFT JOIN vtiger_customerdetails ON vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 $sec_parameter";
		}
		else if($module == "Products")
		{
			$sel_qry = "SELECT count(*) as count FROM vtiger_products
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid
				LEFT JOIN vtiger_productcf ON vtiger_productcf.productid = vtiger_products.productid	
				WHERE vtiger_crmentity.deleted = 0 ";
		}
		else if($module == "Vendors")
		{
			$sel_qry = "select count(*) as count from vtiger_vendor
		       		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_vendor.vendorid
		       		LEFT JOIN vtiger_vendorcf ON vtiger_vendorcf.vendorid = vtiger_vendor.vendorid	 
				WHERE vtiger_crmentity.deleted = 0";
		} else {
			$sel_qry = "select count(*) as count from $focus->table_name
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $focus->table_name.$focus->table_index";
			// Consider custom table join as well.
			if(isset($focus->customFieldTable)) {
				$sel_qry .= " INNER JOIN ".$focus->customFieldTable[0]." ON ".$focus->customFieldTable[0].'.'.$focus->customFieldTable[1] .
				      " = $focus->table_name.$focus->table_index";
			}
			$sel_qry .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
			WHERE vtiger_crmentity.deleted = 0 $sec_parameter";
		}
		$sel_qry .= get_where_clause($module,$focus->column_fields);
		$result = $adb->query($sel_qry);
		$cnt = $adb->query_result($result,0,"count");
		if($cnt > 0)
		return true;
		else
		return false;
	}
	else if($auto_dup_type == "overwrite")
	{
		return overwrite_duplicate_records($module,$focus);
	}
	else
	return false;
}
//function to get the where clause for the duplicate - select query
function get_where_clause($module,$column_fields) {
	global $current_user, $dup_ow_count, $adb;
	$where_clause = "";
	$field_values_array=getFieldValues($module);
	$field_values=$field_values_array['fieldnames_list'];
	$tblname_field_arr = explode(",",$field_values);
	$uitype_arr = $field_values_array['fieldname_uitype'];

	$focus = CRMEntity::getInstance($module);

	foreach($tblname_field_arr as $val) {
		list($tbl,$col,$fld) = explode(".",$val);
		$col_name = $tbl ."." . $col;
		$field_value=$column_fields[$fld];

		if($fld == $focus->table_index && $column_fields[$focus->table_index] !=''  && !is_integer($column_fields[$focus->table_index])) {
			$field_value = getEntityId($module, $column_fields[$focus->table_index]);
		}

		if(is_uitype($uitype_arr[$fld],'_users_list_') && $field_value == '') {
			$field_value = $current_user->id;
		}
		$where_clause .= " AND ifnull(". $adb->sql_escape_string($col_name) .",'') = ifnull('". $adb->sql_escape_string($field_value) ."','') ";
	}
	return $where_clause;
}
//function to overwrite the existing duplicate records with the importing record's values
function overwrite_duplicate_records($module,$focus)
{
	global $adb;
	global $dup_ow_count;
	global $process_fields;

	//Fix for 6187 : overwriting records during duplicate merge to handle uitype 10
	//handle uitype 10
	foreach($focus->importable_fields as $fieldname=>$uitype){
		$uitype = $focus->importable_fields[$fieldname];
		if($uitype == 10){
			//added to handle security permissions for related modules :: for e.g. Accounts/Contacts in Potentials
			if(method_exists($focus, "add_related_to")){
				if(!$focus->add_related_to($module, $fieldname)){
					if(array_key_exists($fieldname, $focus->required_fields)){
						$do_save = 0;
						$skip_required_count++;
						continue 2;
					}
				}
			}
		}
	}
	$where_clause = "";
	$where = get_where_clause($module,$focus->column_fields);
	$sec_parameter = getSecParameterforMerge($module);
	if($module == "Leads")
	{
		$sel_qry = "select vtiger_leaddetails.leadid from vtiger_leaddetails
		inner join vtiger_crmentity  on vtiger_crmentity.crmid = vtiger_leaddetails.leadid
		inner join vtiger_leadsubdetails on vtiger_leaddetails.leadid = vtiger_leadsubdetails.leadsubscriptionid
		inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid = vtiger_leaddetails.leadid
		left join vtiger_leadscf on vtiger_leadscf.leadid = vtiger_leaddetails.leadid
		LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
		where vtiger_crmentity.deleted = 0 AND vtiger_leaddetails.converted = 0 $where $sec_parameter order by vtiger_leaddetails.leadid ASC";
	}
	else if($module == "Accounts")
	{
		$sel_qry = "SELECT vtiger_account.accountid FROM vtiger_account
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid
		INNER JOIN vtiger_accountbillads ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid
		INNER JOIN vtiger_accountshipads ON vtiger_account.accountid = vtiger_accountshipads.accountaddressid
		LEFT JOIN vtiger_accountscf ON vtiger_account.accountid = vtiger_accountscf.accountid
		LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
		WHERE vtiger_crmentity.deleted = 0 $where $sec_parameter order by vtiger_account.accountid ASC";
	}
	else if($module == "Contacts")
	{
		$sel_qry = "SELECT vtiger_contactdetails.contactid FROM vtiger_contactdetails
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
		INNER JOIN vtiger_contactaddress ON vtiger_contactaddress.contactaddressid = vtiger_contactdetails.contactid
		INNER JOIN vtiger_contactsubdetails ON vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid
		LEFT JOIN vtiger_contactscf ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
		LEFT JOIN vtiger_customerdetails ON vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
		LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
		WHERE vtiger_crmentity.deleted = 0 $where $sec_parameter order by vtiger_contactdetails.contactid ASC";
	}
	else if($module == "Products")
	{
		$sel_qry = "SELECT vtiger_products.productid FROM vtiger_products
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid		
		LEFT JOIN vtiger_productcf ON vtiger_productcf.productid = vtiger_products.productid
		WHERE vtiger_crmentity.deleted = 0 $where order by vtiger_products.productid ASC";
	}
	else if($module == "Vendors")
	{
		$sel_qry = "SELECT vtiger_vendor.vendorid FROM vtiger_vendor
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_vendor.vendorid
		LEFT JOIN vtiger_vendorcf ON vtiger_vendorcf.vendorid = vtiger_vendor.vendorid
		WHERE vtiger_crmentity.deleted = 0 $where order by vtiger_vendor.vendorid ASC";
	}
	else {
		$sel_qry = "SELECT $focus->table_name.$focus->table_index FROM $focus->table_name
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $focus->table_name.$focus->table_index";
		// Consider custom table join as well.
		if(isset($focus->customFieldTable)) {
			$sel_qry .= " INNER JOIN ".$focus->customFieldTable[0]." ON ".$focus->customFieldTable[0].'.'.$focus->customFieldTable[1] .
			      " = $focus->table_name.$focus->table_index";
		}
		$sel_qry .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
		WHERE vtiger_crmentity.deleted = 0 $where $sec_parameter order by $focus->table_name.$focus->table_index ASC";
	}
	$result = $adb->query($sel_qry);
	$no_rows = $adb->num_rows($result);
	// now do any special processing for ex., map account with contact and potential
	$focus->process_special_fields();
	$process_fields='true';
	$moduleObj = new $module();
	if($no_rows > 0)
	{
		for($i=0;$i<$no_rows;$i++)
		{
			$id_field = $moduleObj->table_index;
			$id_value = $adb->query_result($result,$i,$id_field);
			if($i == 0)
			{
				$moduleObj->mode = "edit";
				$moduleObj->id = $id_value;
				$moduleObj->column_fields = $focus->column_fields;
				$moduleObj->save($module);
			}
			else{
				DeleteEntity($module,$module,$moduleObj,$id_value,"");
			}
		}
		$dup_ow_count = $dup_ow_count+$no_rows;
		return true;
	}
	else
		return false;
}

//picklist function is added to avoid duplicate picklist entries
function getPicklist($field,$value)
{
        global $table_picklist,$converted_table_picklist_values;

        //for the first run these will be defined and for the subsequent redirections
        //pick up from session.
        if(is_array($table_picklist)){
                $_SESSION['import_table_picklist'] = $table_picklist;
        }else{
                $table_picklist = $_SESSION['import_table_picklist'];
        }
        if(is_array($converted_table_picklist_values)){
                $_SESSION['import_converted_picklist_values'] = $converted_table_picklist_values;
        }else{
                $converted_table_picklist_values = $_SESSION['import_converted_picklist_values'];
        }

        $orginal_val = $table_picklist[$field];
        $converted_val = $converted_table_picklist_values[$field];
        $temp_val = strtolower($value);
                if(is_array($converted_val) && in_array($temp_val,$converted_val)) {
                $existkey = array_search($temp_val,$converted_val);
                $correct_val=$orginal_val[$existkey];
                return $correct_val;
        }
        return null;

}

?>
