<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/database/Postgres8.php');
require_once('include/ComboUtil.php'); //new
require_once('include/utils/CommonUtils.php'); //new

$column_array=array('accountid','contact_id','product_id','campaignid','quoteid','vendorid','potentialid','salesorderid','vendor_id','contactid');
$table_col_array=array('vtiger_account.accountname','vtiger_contactdetails.firstname,vtiger_contactdetails.lastname','vtiger_products.productname','vtiger_campaign.campaignname','vtiger_quotes.subject','vtiger_vendor.vendorname','vtiger_potential.potentialname','vtiger_salesorder.subject','vtiger_vendor.vendorname','vtiger_contactdetails.firstname,vtiger_contactdetails.lastname');

/**This function is used to get the list view header values in a list view during search
*Param $focus - module object
*Param $module - module name
*Param $sort_qry - sort by value
*Param $sorder - sorting order (asc/desc)
*Param $order_by - order by
*Param $relatedlist - flag to check whether the header is for listvie or related list
*Param $oCv - Custom view object
*Returns the listview header values in an array
*/

function getSearchListHeaderValues($focus, $module,$sort_qry='',$sorder='',$order_by='',$relatedlist='',$oCv='')
{
	global $log;
	$log->debug("Entering getSearchListHeaderValues(".(is_object($focus)? get_class($focus):'').",". $module.",".$sort_qry.",".$sorder.",".$order_by.",".$relatedlist.",".(is_object($oCV)? get_class($oCV):'').") method ...");
        global $adb;
        global $theme;
        global $app_strings;
        global $mod_strings,$current_user;

        $arrow='';
        $qry = getURLstring($focus);
        $theme_path="themes/".$theme."/";
        $image_path=$theme_path."images/";
        $search_header = Array();

        //Get the vtiger_tabid of the module
        //require_once('include/utils/UserInfoUtil.php')
        $tabid = getTabid($module);
        //added for vtiger_customview 27/5
        if($oCv)
        {
                if(isset($oCv->list_fields))
		{
                        $focus->list_fields = $oCv->list_fields;
                }
        }
	//Added to reduce the no. of queries logging for non-admin vtiger_users -- by Minnie-start
	$field_list = array();
	$j=0;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	foreach($focus->list_fields as $name=>$tableinfo)
	{
		$fieldname = $focus->list_fields_name[$name];
		if($oCv)
		{
			if(isset($oCv->list_fields_name))
			{
				$fieldname = $oCv->list_fields_name[$name];
			}
		}
		if($fieldname == "accountname" && $module !="Accounts")
			$fieldname = "account_id";

		if($fieldname == "productname" && $module =="Campaigns")
			$fieldname = "product_id";

		if($fieldname == "lastname" && $module !="Leads" && $module !="Contacts")
		{
			$fieldname = "contact_id";
		}
		if($fieldname == 'folderid' && $module == 'Documents'){
			$fieldname = 'foldername';
		}
		array_push($field_list, $fieldname);
		$j++;
	}
	//Getting the Entries from Profile2 vtiger_field vtiger_table
	if($is_admin == false)
	{
		$profileList = getCurrentUserProfileList();
		//changed to get vtiger_field.fieldname
		$query  = "SELECT vtiger_profile2field.*,vtiger_field.fieldname FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN (". generateQuestionMarks($profileList) .") AND vtiger_field.fieldname IN (". generateQuestionMarks($field_list) .") and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
 		if( $adb->dbType == "pgsql")
 		    $query = fixPostgresQuery( $query, $log, 0);
		$result = $adb->pquery($query, array($tabid, $profileList, $field_list));
		$field=Array();
		for($k=0;$k < $adb->num_rows($result);$k++)
		{
			$field[]=$adb->query_result($result,$k,"fieldname");
		}

		//if this field array is empty and the user don't have any one of the admin, view all, edit all permissions then the search picklist options will be empty and we cannot navigate the users list - js error will thrown in function getListViewEntries_js in Smarty\templates\Popup.tpl
		if($module == 'Users' && empty($field))
			$field = Array("last_name","email1");
	}

	// Remove fields which are made inactive
	$focus->filterInactiveFields($module);

	    //modified for vtiger_customview 27/5 - $app_strings change to $mod_strings
        foreach($focus->list_fields as $name=>$tableinfo)
        {
                //added for vtiger_customview 27/5
                if($oCv)
                {
                        if(isset($oCv->list_fields_name))
			{
				if( $oCv->list_fields_name[$name] == '')
					$fieldname = 'crmid';
				else
					$fieldname = $oCv->list_fields_name[$name];

                        }else
                        {
				if( $focus->list_fields_name[$name] == '')
					$fieldname = 'crmid';
				else
					$fieldname = $focus->list_fields_name[$name];

                        }
			if($fieldname == "lastname" && $module !="Leads" && $module !="Contacts")
				$fieldname = "contact_id";
			if($fieldname == "accountname" && $module !="Accounts")
				$fieldname = "account_id";
			if($fieldname == "productname" && $module =="Campaigns")
				$fieldname = "product_id";


                }
		else
                {
			if( $focus->list_fields_name[$name] == '')
				$fieldname = 'crmid';
			else
				$fieldname = $focus->list_fields_name[$name];

			if($fieldname == "lastname" && $module !="Leads" && $module !="Contacts")
                                $fieldname = "contact_id";
		}
                if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] ==0 || in_array($fieldname,$field))
		{
			if($fieldname!='parent_id')
			{
				$fld_name=$fieldname;
				if($fieldname == 'contact_id' && $module !="Contacts")
				$name = $app_strings['LBL_CONTACT_LAST_NAME'];
				elseif($fieldname == 'contact_id' && $module =="Contacts")
					$name = $mod_strings['Reports To']." - ".$mod_strings['LBL_LIST_LAST_NAME'];
				//assign the translated string
				//added to fix #5205
				//Added condition to hide the close column in calendar search header
				if($name != $app_strings['Close'])
					$search_header[$fld_name] = getTranslatedString($name);
			}
		}
		if($module == 'HelpDesk' && $fieldname == 'crmid')
		{
                        $fld_name=$fieldname;
                        $search_header[$fld_name] = getTranslatedString($name);
                }
	}
	$log->debug("Exiting getSearchListHeaderValues method ...");
        return $search_header;

}

/**This function is used to get the where condition for search listview query along with url_string
*Param $module - module name
*Returns the where conditions and url_string values in string format
*/

function Search($module, $input = '')
{
	global $log,$default_charset;

	if(empty($input)) {
		$input = $_REQUEST;
	}
	
    $log->debug("Entering Search(".$module.") method ...");
	$url_string='';
	if(isset($input['search_field']) && $input['search_field'] !="") {
		$search_column=vtlib_purify($input['search_field']);
	}
	if(isset($input['search_text']) && $input['search_text']!="") {
		// search other characters like "|, ?, ?" by jagi
		$search_string = $input['search_text'];
		$stringConvert = function_exists(iconv) ? @iconv("UTF-8",$default_charset,$search_string) : $search_string;
		$search_string=trim($stringConvert);
	}
	if(isset($input['searchtype']) && $input['searchtype']!="") {
        $search_type=vtlib_purify($input['searchtype']);
    	if($search_type == "BasicSearch") {
            $where=BasicSearch($module,$search_column,$search_string,$input);
    	} else if ($search_type == "AdvanceSearch") {
    	} else { //Global Search
		}
		$url_string = "&search_field=".$search_column."&search_text=".urlencode($search_string)."&searchtype=BasicSearch";
		if(isset($input['type']) && $input['type'] != '')
			$url_string .= "&type=".vtlib_purify($input['type']);
		$log->debug("Exiting Search method ...");
		return $where."#@@#".$url_string;
	}
}

/**This function is used to get user_id's for a given user_name during search
*Param $table_name - vtiger_tablename
*Param $column_name - columnname
*Param $search_string - searchstring value (username)
*Returns the where conditions for list query in string format
*/

function get_usersid($table_name,$column_name,$search_string)
{

	global $log;
	$log->debug("Entering get_usersid(".$table_name.",".$column_name.",".$search_string.") method ...");
	global $adb;
	$concatSql = getSqlForNameInDisplayFormat(array('last_name'=>'vtiger_users.last_name', 'first_name'=>'vtiger_users.first_name'), 'Users');
	$where.="(trim($concatSql) like '". formatForSqlLike($search_string) .
			"' or vtiger_groups.groupname like '". formatForSqlLike($search_string) ."')";
	$log->debug("Exiting get_usersid method ...");
	return $where;
}

/**This function is used to get where conditions for a given vtiger_accountid or contactid during search for their respective names
*Param $column_name - columnname
*Param $search_string - searchstring value (username)
*Returns the where conditions for list query in string format
*/


function getValuesforColumns($column_name,$search_string,$criteria='cts',$input='')
{
	global $log, $current_user;
	$log->debug("Entering getValuesforColumns(".$column_name.",".$search_string.") method ...");
	global $column_array,$table_col_array;
	
	if(empty($input)) {
		$input = $_REQUEST;
	}

	if($input['type'] == "entchar")
		$criteria = "is";

	for($i=0; $i<count($column_array);$i++)
	{
		if($column_name == $column_array[$i])
		{
			$val=$table_col_array[$i];
			$explode_column=explode(",",$val);
			$x=count($explode_column);
			if($x == 1 )
			{
				$where=getSearch_criteria($criteria,$search_string,$val);
			}
			else
			{
				if($column_name == "contact_id" && $input['type'] == "entchar") {
					$concatSql = getSqlForNameInDisplayFormat(array('lastname'=>'vtiger_contactdetails.lastname', 'firstname'=>'vtiger_contactdetails.firstname'), 'Contacts');
					$where = "$concatSql = '$search_string'";
				}
				else {
					$where="(";
					for($j=0;$j<count($explode_column);$j++)
					{
						$where .=getSearch_criteria($criteria,$search_string,$explode_column[$j]);
						if($j != $x-1)
						{
							if($criteria == 'dcts' || $criteria == 'isn')
								$where .= " and ";
							else
								$where .= " or ";
						}
					}
					$where.=")";
				}
			}
			break 1;
		}
	}
	$log->debug("Exiting getValuesforColumns method ...");
	return $where;
}

/**This function is used to get where conditions in Basic Search
*Param $module - module name
*Param $search_field - columnname/field name in which the string has be searched
*Param $search_string - searchstring value (username)
*Returns the where conditions for list query in string format
*/

function BasicSearch($module,$search_field,$search_string,$input=''){

	global $log,$mod_strings,$current_user;
	$log->debug("Entering BasicSearch(".$module.",".$search_field.",".$search_string.") method ...");
	global $adb;
	$search_string = ltrim(rtrim($adb->sql_escape_string($search_string)));
	global $column_array,$table_col_array;

	if(empty($input)) {
		$input = $_REQUEST;
	}

	if($search_field =='crmid'){
		$column_name='crmid';
		$table_name='vtiger_crmentity';
		$where="$table_name.$column_name like '". formatForSqlLike($search_string) ."'";
	}elseif($search_field =='currency_id' && ($module == 'PriceBooks' || $module == 'PurchaseOrder' || $module == 'SalesOrder' || $module == 'Invoice' || $module == 'Quotes')){
		$column_name='currency_name';
		$table_name='vtiger_currency_info';
		$where="$table_name.$column_name like '". formatForSqlLike($search_string) ."'";
	}elseif($search_field == 'folderid' && $module == 'Documents'){
		$column_name='foldername';
		$table_name='vtiger_attachmentsfolder';
		$where="$table_name.$column_name like '". formatForSqlLike($search_string) ."'";
	}else{
		//Check added for tickets by accounts/contacts in dashboard
		$search_field_first = $search_field;
		if($module=='HelpDesk'){
			if($search_field == 'contactid'){
				$where = "(vtiger_contactdetails.contact_no like '". formatForSqlLike($search_string) ."')";
				return $where;
			}elseif($search_field == 'account_id'){
				$search_field = "parent_id";
			}
		}
		//Check ends

		//Added to search contact name by lastname
		if(($module == "Calendar" || $module == "Invoice" || $module == "Documents" || $module == "SalesOrder" || $module== "PurchaseOrder") && ($search_field == "contact_id")){
			$module = 'Contacts';
			$search_field = 'lastname';
		}
		if($search_field == "accountname" && $module != "Accounts"){
			$search_field = "account_id";
		}
		if($search_field == 'productname' && $module == 'Campaigns'){
			$search_field = "product_id";
		}

		$qry="select vtiger_field.columnname,tablename from vtiger_tab inner join vtiger_field on vtiger_field.tabid=vtiger_tab.tabid where vtiger_tab.name=? and (fieldname=? or columnname=?)";
		$result = $adb->pquery($qry, array($module, $search_field, $search_field));
		$noofrows = $adb->num_rows($result);
		if($noofrows!=0)
		{
			$column_name=$adb->query_result($result,0,'columnname');

			//Check added for tickets by accounts/contacts in dashboard
			if ($column_name == 'parent_id')
			{
				if ($search_field_first	== 'account_id') $search_field_first = 'accountid';
				if ($search_field_first	== 'contactid') $search_field_first = 'contact_id';
				$column_name = $search_field_first;
			}

			//Check ends
			$table_name=$adb->query_result($result,0,'tablename');
			$uitype=getUItype($module,$column_name);

			//Added for Member of search in Accounts
			if($column_name == "parentid" && $module == "Accounts")
			{
				$table_name = "vtiger_account2";
				$column_name = "accountname";
			}
			if($column_name == "parentid" && $module == "Products")
			{
				$table_name = "vtiger_products2";
				$column_name = "productname";
			}
			if($column_name == "reportsto" && $module == "Contacts")
			{
				$table_name = "vtiger_contactdetails2";
				$column_name = "lastname";
			}
			if($column_name == "inventorymanager" && $module = "Quotes")
			{
				$table_name = "vtiger_usersQuotes";
				$column_name = "user_name";
			}
			//Added to support user date format in basic search
			if($uitype == 5 || $uitype == 6 || $uitype == 23 || $uitype == 70)
			{
				if ($search_string != '' && $search_string != '0000-00-00') {
					$date = new DateTimeField($search_string);
					$value = $date->getDisplayDate();
					if(strpos($search_string, ' ') > -1) {
						$value .= (' ' . $date->getDisplayTime());
					}
				} else {
					$value = $search_string;
				}
			}
			// Added to fix errors while searching check box type fields(like product active. ie. they store 0 or 1. we search them as yes or no) in basic search.
			if ($uitype == 56)
			{
				if(strtolower($search_string) == 'yes')
					$where="$table_name.$column_name = '1'";
				elseif(strtolower($search_string) == 'no')
					$where="$table_name.$column_name = '0'";
				else
					$where="$table_name.$column_name = '-1'";

			}
			elseif ($uitype == 15 || $uitype == 16)
			{
				if(is_uitype($uitype, '_picklist_'))
				{
					// Get all the keys for the for the Picklist value
					$mod_keys = array_keys($mod_strings, $search_string);
					if(sizeof($mod_keys) >= 1)
					{
						// Iterate on the keys, to get the first key which doesn't start with LBL_      (assuming it is not used in PickList)
						foreach($mod_keys as $mod_idx=>$mod_key)
						{
							$stridx = strpos($mod_key, 'LBL_');
							// Use strict type comparision, refer strpos for more details
							if ($stridx !== 0)
							{
								$search_string = $mod_key;
								if($input['operator'] == 'e' && getFieldVisibilityPermission("Calendar", $current_user->id,'taskstatus') == '0' && ($column_name == "status" || $column_name == "eventstatus")){
									$where="(vtiger_activity.status ='". $search_string ."' or vtiger_activity.eventstatus ='". $search_string ."')";
								}else if(getFieldVisibilityPermission("Calendar", $current_user->id,'taskstatus') == '0' && ($column_name == "status" || $column_name == "eventstatus"))
								{
									$where="(vtiger_activity.status like '". formatForSqlLike($search_string) ."' or vtiger_activity.eventstatus like '". formatForSqlLike($search_string) ."')";
								}
								else
									$where="$table_name.$column_name like '". formatForSqlLike($search_string) ."'";
								break;
							}
							else //if the mod strings cointains LBL , just return the original search string. Not the key
								$where="$table_name.$column_name like '". formatForSqlLike($search_string) ."'";
						}
					}
					else
					{
						if(getFieldVisibilityPermission("Calendar", $current_user->id,'taskstatus') == '0' && ($table_name == "vtiger_activity" && ($column_name == "status" || $column_name == "eventstatus")))
						{
								$where="(vtiger_activity.status like '". formatForSqlLike($search_string) ."' or vtiger_activity.eventstatus like '". formatForSqlLike($search_string) ."')";
						}
						else
							$where="$table_name.$column_name like '". formatForSqlLike($search_string) ."'";
					}
				}
			}
			elseif($table_name == "vtiger_crmentity" && $column_name == "smownerid")
			{
				$where = get_usersid($table_name,$column_name,$search_string);
			}
			elseif($table_name == "vtiger_crmentity" && $column_name == "modifiedby")
			{
				$concatSql = getSqlForNameInDisplayFormat(array('last_name'=>'vtiger_users2.last_name', 'first_name'=>'vtiger_users2.first_name'), 'Users');
				$where.="(trim($concatSql) like '". formatForSqlLike($search_string) .
							"' or vtiger_groups2.groupname like '". formatForSqlLike($search_string) ."')";
			}
			else if(in_array($column_name,$column_array))
			{
				$where = getValuesforColumns($column_name,$search_string,'cts',$input);
			}
			else if($input['type'] == 'entchar')
			{
				$where="$table_name.$column_name = '". $search_string ."'";
			}
			else
			{
				$where="$table_name.$column_name like '". formatForSqlLike($search_string) ."'";
			}
		}
	}
	if(stristr($where,"like '%%'"))
	{
		$where_cond0=str_replace("like '%%'","like ''",$where);
		$where_cond1=str_replace("like '%%'","is NULL",$where);
		if($module == "Calendar")
			$where = "(".$where_cond0." and ".$where_cond1.")";
		else
			$where = "(".$where_cond0." or ".$where_cond1.")";
	}
	// commented to support searching "%" with the search string.
	if($input['type'] == 'alpbt'){
	        $where = str_replace_once("%", "", $where);
	}

	//uitype 10 handling
	if($uitype == 10){
		$where = array();
		$sql = "select fieldid from vtiger_field where tabid=? and fieldname=?";
		$result = $adb->pquery($sql, array(getTabid($module), $search_field));

		if($adb->num_rows($result)>0){
			$fieldid = $adb->query_result($result, 0, "fieldid");
			$sql = "select * from vtiger_fieldmodulerel where fieldid=?";
			$result = $adb->pquery($sql, array($fieldid));
			$count = $adb->num_rows($result);
			$searchString = formatForSqlLike($search_string);

			for($i=0;$i<$count;$i++){
				$relModule = $adb->query_result($result, $i, "relmodule");
				$relInfo = getEntityField($relModule);
				$relTable = $relInfo["tablename"];
				$relField = $relInfo["fieldname"];

				if(strpos($relField, 'concat') !== false){
					$where[] = "$relField like '$searchString'";
				}else{
					$where[] = "$relTable.$relField like '$searchString'";
				}

			}
			$where = implode(" or ", $where);
		}
		$where = "($where) ";
	}

	$log->debug("Exiting BasicSearch method ...");
	return $where;
}

/**This function is used to get where conditions in Advance Search
*Param $module - module name
*Returns the where conditions for list query in string format
*/

function getAdvSearchfields($module)
{
	global $log;
        $log->debug("Entering getAdvSearchfields(".$module.") method ...");
	global $adb;
	global $current_user;
	global $mod_strings,$app_strings;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');

	$tabid = getTabid($module);
        if($tabid==9)
                $tabid="9,16";

	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
	{
		$sql = "select * from vtiger_field ";
		$sql.= " where vtiger_field.tabid in(?) and";
		$sql.= " vtiger_field.displaytype in (1,2,3) and vtiger_field.presence in (0,2)";
		if($tabid == 13 || $tabid == 15)
		{
			$sql.= " and vtiger_field.fieldlabel != 'Add Comment'";
		}
		if($tabid == 14)
		{
			$sql.= " and vtiger_field.fieldlabel != 'Product Image'";
		}
		if($tabid == 9 || $tabid==16)
		{
			$sql.= " and vtiger_field.fieldname not in('notime','duration_minutes','duration_hours')";
		}
		if($tabid == 4)
		{
			$sql.= " and vtiger_field.fieldlabel != 'Contact Image'";
		}
		if($tabid == 13 || $tabid == 10)
		{
			$sql.= " and vtiger_field.fieldlabel != 'Attachment'";
		}
		$sql.= " group by vtiger_field.fieldlabel order by block,sequence";

		$params = array($tabid);
	}
	else
	{
		$profileList = getCurrentUserProfileList();
		$sql = "select * from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid ";
		$sql.= " where vtiger_field.tabid in(?) and";
		$sql.= " vtiger_field.displaytype in (1,2,3) and vtiger_field.presence in (0,2) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0";

		$params = array($tabid);

		if (count($profileList) > 0) {
			$sql.= "  and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params, $profileList);
		}

		if($tabid == 13 || $tabid == 15)
		{
			$sql.= " and vtiger_field.fieldlabel != 'Add Comment'";
		}
		if($tabid == 14)
		{
			$sql.= " and vtiger_field.fieldlabel != 'Product Image'";
		}
		if($tabid == 9 || $tabid==16)
		{
			$sql.= " and vtiger_field.fieldname not in('notime','duration_minutes','duration_hours')";
		}
		if($tabid == 4)
		{
			$sql.= " and vtiger_field.fieldlabel != 'Contact Image'";
		}
		if($tabid == 13 || $tabid == 10)
		{
			$sql.= " and vtiger_field.fieldlabel != 'Attachment'";
		}
		$sql .= " group by vtiger_field.fieldlabel order by block,sequence";
	}

	$result = $adb->pquery($sql, $params);
	$noofrows = $adb->num_rows($result);
	$block = '';
	$select_flag = '';

	for($i=0; $i<$noofrows; $i++)
	{
		$fieldtablename = $adb->query_result($result,$i,"tablename");
		$fieldcolname = $adb->query_result($result,$i,"columnname");
		$fieldname = $adb->query_result($result,$i,"fieldname");
		$block = $adb->query_result($result,$i,"block");
		$fieldtype = $adb->query_result($result,$i,"typeofdata");
		$fieldtype = explode("~",$fieldtype);
		$fieldtypeofdata = $fieldtype[0];
		if($fieldcolname == 'account_id' || $fieldcolname == 'accountid' || $fieldcolname == 'product_id' || $fieldcolname == 'vendor_id' || $fieldcolname == 'contact_id' || $fieldcolname == 'contactid' || $fieldcolname == 'vendorid' || $fieldcolname == 'potentialid' || $fieldcolname == 'salesorderid' || $fieldcolname == 'quoteid' || $fieldcolname == 'parentid' || $fieldcolname == "recurringtype" || $fieldcolname == "campaignid" || $fieldcolname == "inventorymanager" ||  $fieldcolname == "currency_id")
			$fieldtypeofdata = "V";
		if($fieldcolname == "discontinued" || $fieldcolname == "active")
			$fieldtypeofdata = "C";
		$fieldlabel = $mod_strings[$adb->query_result($result,$i,"fieldlabel")];

		// Added to display customfield label in search options
		if($fieldlabel == "")
			$fieldlabel = $adb->query_result($result,$i,"fieldlabel");

		if($fieldlabel == "Related To")
		{
			$fieldlabel = "Related to";
		}
		if($fieldlabel == "Start Date & Time")
		{
			$fieldlabel = "Start Date";
			if($module == 'Activities' && $block == 19)
				$module_columnlist['vtiger_activity:time_start::Activities_Start Time:I'] = 'Start Time';

		}
		//$fieldlabel1 = str_replace(" ","_",$fieldlabel); // Is not used anywhere
		//Check added to search the lists by Inventory manager
                if($fieldtablename == 'vtiger_quotes' && $fieldcolname == 'inventorymanager')
                {
                        $fieldtablename = 'vtiger_usersQuotes';
                        $fieldcolname = 'user_name';
                }
		if($fieldtablename == 'vtiger_contactdetails' && $fieldcolname == 'reportsto')
                {
                        $fieldtablename = 'vtiger_contactdetails2';
                        $fieldcolname = 'lastname';
                }
                if($fieldtablename == 'vtiger_notes' && $fieldcolname == 'folderid'){
                	$fieldtablename = 'vtiger_attachmentsfolder';
                	$fieldcolname = 'foldername';
                }
		if($fieldlabel != 'Related to')
		{
			if ($i==0)
				$select_flag = "selected";

			$mod_fieldlabel = $mod_strings[$fieldlabel];
			if($mod_fieldlabel =="") $mod_fieldlabel = $fieldlabel;

			if($fieldlabel == "Product Code")
				$OPTION_SET .= "<option value=\'".$fieldtablename.":".$fieldcolname.":".$fieldname."::".$fieldtypeofdata."\'".$select_flag.">".$mod_fieldlabel."</option>";
			if($fieldlabel == "Reports To")
				$OPTION_SET .= "<option value=\'".$fieldtablename.":".$fieldcolname.":".$fieldname."::".$fieldtypeofdata."\'".$select_flag.">".$mod_fieldlabel." - ".$mod_strings['LBL_LIST_LAST_NAME']."</option>";
			elseif($fieldcolname == "contactid" || $fieldcolname == "contact_id")
			{
				$OPTION_SET .= "<option value=\'vtiger_contactdetails:lastname:".$fieldname."::".$fieldtypeofdata."\' ".$select_flag.">".$app_strings['LBL_CONTACT_LAST_NAME']."</option>";
				$OPTION_SET .= "<option value=\'vtiger_contactdetails:firstname:".$fieldname."::".$fieldtypeofdata."\'>".$app_strings['LBL_CONTACT_FIRST_NAME']."</option>";
			}
			elseif($fieldcolname == "campaignid")
				$OPTION_SET .= "<option value=\'vtiger_campaign:campaignname:".$fieldname."::".$fieldtypeofdata."\' ".$select_flag.">".$mod_fieldlabel."</option>";
			else
				$OPTION_SET .= "<option value=\'".$fieldtablename.":".$fieldcolname.":".$fieldname."::".$fieldtypeofdata."\' ".$select_flag.">".str_replace("'","`",$fieldlabel)."</option>";
		}
	}
	//Added to include Ticket ID in HelpDesk advance search
	if($module == 'HelpDesk')
	{
		$mod_fieldlabel = $mod_strings['Ticket ID'];
                if($mod_fieldlabel =="") $mod_fieldlabel = 'Ticket ID';

		$OPTION_SET .= "<option value=\'vtiger_crmentity:crmid:".$fieldname."::".$fieldtypeofdata."\'>".$mod_fieldlabel."</option>";
	}
	//Added to include activity type in activity advance search
	if($module == 'Activities')
	{
		$mod_fieldlabel = $mod_strings['Activity Type'];
                if($mod_fieldlabel =="") $mod_fieldlabel = 'Activity Type';

		$OPTION_SET .= "<option value=\'vtiger_activity.activitytype:".$fieldname."::".$fieldtypeofdata."\'>".$mod_fieldlabel."</option>";
	}
	$log->debug("Exiting getAdvSearchfields method ...");
	return $OPTION_SET;
}

/**This function is returns the search criteria options for Advance Search
*takes no parameter
*Returns the criteria option in html format
*/

function getcriteria_options()
{
	global $log,$app_strings;
	$log->debug("Entering getcriteria_options() method ...");
	$CRIT_OPT = "<option value=\'c\'>".str_replace("'","`",$app_strings['contains']).
			"</option><option value=\'k\'>".str_replace("'","`",$app_strings['does_not_contains']).
			"</option><option value=\'e\'>".str_replace("'","`",$app_strings['is']).
			"</option><option value=\'n\'>".str_replace("'","`",$app_strings['is_not']).
			"</option><option value=\'s\'>".str_replace("'","`",$app_strings['begins_with']).
			"</option><option value=\'ew\'>".str_replace("'","`",$app_strings['ends_with']).
			"</option><option value=\'g\'>".str_replace("'","`",$app_strings['greater_than']).
			"</option><option value=\'l\'>".str_replace("'","`",$app_strings['less_than']).
			"</option><option value=\'h\'>".str_replace("'","`",$app_strings['greater_or_equal']).
			"</option><option value=\'m\'>".str_replace("'","`",$app_strings['less_or_equal']).
			"</option>";
	$log->debug("Exiting getcriteria_options method ...");
	return $CRIT_OPT;
}

/**This function is returns the where conditions for each search criteria option in Advance Search
*Param $criteria - search criteria option
*Param $searchstring - search string
*Param $searchfield - vtiger_fieldname to be search for
*Returns the search criteria option (where condition) to be added in list query
*/

function getSearch_criteria($criteria,$searchstring,$searchfield)
{
	global $log;
	$log->debug("Entering getSearch_criteria(".$criteria.",".$searchstring.",".$searchfield.") method ...");
	$searchstring = ltrim(rtrim($searchstring));
	if(($searchfield != "vtiger_troubletickets.update_log") && ($searchfield == "vtiger_crmentity.modifiedtime" || $searchfield == "vtiger_crmentity.createdtime" || stristr($searchfield,'date')))
	{
		if ($search_string != '' && $search_string != '0000-00-00') {
			$date = new DateTimeField($search_string);
			$value = $date->getDisplayDate();
			if(strpos($search_string, ' ') > -1) {
				$value .= (' ' . $date->getDisplayTime());
			}
		} else {
			$value = $search_string;
		}
	}
	if($searchfield == "vtiger_account.parentid")
		$searchfield = "vtiger_account2.accountname";
	if($searchfield == "vtiger_pricebook.currency_id" || $searchfield == "vtiger_quotes.currency_id" || $searchfield == "vtiger_invoice.currency_id"
			|| $searchfield == "vtiger_purchaseorder.currency_id" || $searchfield == "vtiger_salesorder.currency_id")
		$searchfield = "vtiger_currency_info.currency_name";
	$where_string = '';
	switch($criteria)
	{
		case 'cts':
			$where_string = $searchfield." like '". formatForSqlLike($searchstring) ."' ";
			if($searchstring == NULL)
			{
					$where_string = "(".$searchfield." like '' or ".$searchfield." is NULL)";
			}
			break;

		case 'dcts':
			if($searchfield == "vtiger_users.user_name" || $searchfield =="vtiger_groups.groupname")
				$where_string = "(".$searchfield." not like '". formatForSqlLike($searchstring) ."')";
			else
				$where_string = "(".$searchfield." not like '". formatForSqlLike($searchstring) ."' or ".$searchfield." is null)";
			if($searchstring == NULL)
			$where_string = "(".$searchfield." not like '' or ".$searchfield." is not NULL)";
			break;

		case 'is':
			$where_string = $searchfield." = '".$searchstring."' ";
			if($searchstring == NULL)
			$where_string = "(".$searchfield." is NULL or ".$searchfield." = '')";
			break;

		case 'isn':
			if($searchfield == "vtiger_users.user_name" || $searchfield =="vtiger_groups.groupname")
				$where_string = "(".$searchfield." <> '".$searchstring."')";
			else
				$where_string = "(".$searchfield." <> '".$searchstring."' or ".$searchfield." is null)";
			if($searchstring == NULL)
			$where_string = "(".$searchfield." not like '' and ".$searchfield." is not NULL)";
			break;

		case 'bwt':
			$where_string = $searchfield." like '". formatForSqlLike($searchstring, 2) ."' ";
			break;

		case 'ewt':
			$where_string = $searchfield." like '". formatForSqlLike($searchstring, 1) ."' ";
			break;

		case 'grt':
			$where_string = $searchfield." > '".$searchstring."' ";
			break;

		case 'lst':
			$where_string = $searchfield." < '".$searchstring."' ";
			break;

		case 'grteq':
			$where_string = $searchfield." >= '".$searchstring."' ";
			break;

		case 'lsteq':
			$where_string = $searchfield." <= '".$searchstring."' ";
			break;


	}
	$log->debug("Exiting getSearch_criteria method ...");
	return $where_string;
}

/**This function is returns the where conditions for search
*Param $currentModule - module name
*Returns the where condition to be added in list query in string format
*/

function getWhereCondition($currentModule, $input = '')
{
	global $log,$default_charset,$adb;
	global $column_array,$table_col_array,$mod_strings,$current_user;
	
	$log->debug("Entering getWhereCondition(".$currentModule.") method ...");

	if(empty($input)) {
		$input = $_REQUEST;
	}
	
	if($input['searchtype']=='advance')
	{
		$json = new Zend_Json();
		$advft_criteria = $input['advft_criteria'];
		if(!empty($advft_criteria))	$advft_criteria_decoded = $json->decode($advft_criteria);
		$advft_criteria_groups = $input['advft_criteria_groups'];
		if(!empty($advft_criteria_groups))	$advft_criteria_groups_decoded = $json->decode($advft_criteria_groups);

		$advfilterlist = getAdvancedSearchCriteriaList($advft_criteria_decoded, $advft_criteria_groups_decoded, $currentModule);
		$adv_string = generateAdvancedSearchSql($advfilterlist);
		if(!empty($adv_string)) $adv_string = '('.$adv_string.')';
		$where = $adv_string.'#@@#'.'&advft_criteria='.$advft_criteria.'&advft_criteria_groups='.$advft_criteria_groups.'&searchtype=advance';
	}
	elseif($input['type']=='dbrd')
	{
		$where = getdashboardcondition($input);
	}
	else
	{
 		$where = Search($currentModule, $input);
	}
	$log->debug("Exiting getWhereCondition method ...");
	return $where;

}

function getSearchURL($input) {
	global $log,$default_charset;
	$urlString='';
	if($input['searchtype']=='advance') {
		$advft_criteria = vtlib_purify($input['advft_criteria']);
		if(empty($advft_criteria))	return $urlString;
		$advft_criteria_groups = vtlib_purify($input['advft_criteria_groups']);

		$urlString .= '&advft_criteria='.$advft_criteria.'&advft_criteria_groups='.$advft_criteria_groups.'&searchtype=advance';

	} elseif($input['type']=='dbrd'){
		if(isset($input['leadsource'])) {
			$leadSource = vtlib_purify($input['leadsource']);
			$urlString .= "&leadsource=".$leadSource;
		}
		if(isset($input['date_closed'])) {
			$dateClosed = vtlib_purify($input['date_closed']);
			$urlString .= "&date_closed=".$dateClosed;
		}
		if(isset($input['sales_stage'])) {
			$salesStage = vtlib_purify($input['sales_stage']);
			$urlString .= "&sales_stage=".$salesStage;
		}
		if(!empty($input['closingdate_start']) && !empty($input['closingdate_end'])) {
			$dateClosedStart = vtlib_purify($input['closingdate_start']);
			$dateClosedEnd = vtlib_purify($input['closingdate_end']);
			$urlString .= "&closingdate_start=$dateClosedStart&closingdate_end=".$dateClosedEnd;
		}
		if(isset($input['owner'])) {
			$owner = vtlib_purify($input['owner']);
			$urlString .= "&owner=".$owner;
		}
		if(isset($input['campaignid'])) {
			$campaignId = vtlib_purify($input['campaignid']);
			$urlString .= "&campaignid=".$campaignId;
		}
		if(isset($input['quoteid'])) {
			$quoteId = vtlib_purify($input['quoteid']);
			$urlString .= "&quoteid=".$quoteId;
		}
		if(isset($input['invoiceid'])) {
			$invoiceId = vtlib_purify($input['invoiceid']);
			$urlString .= "&invoiceid=".$invoiceId;
		}
		if(isset($input['purchaseorderid'])) {
			$purchaseOrderId = vtlib_purify($input['purchaseorderid']);
			$urlString .= "&purchaseorderid=".$purchaseOrderId;
		}

		if(isset($input['from_homepagedb']) && $input['from_homepagedb'] != '') {
			$url_string .= "&from_homepagedb=".vtlib_purify($input['from_homepagedb']);
		}
		if(isset($input['type']) && $input['type'] != '') {
			$url_string .= "&type=".vtlib_purify($input['type']);
		}
	} else {
		$value = vtlib_purify($input['search_text']);
		$stringConvert = function_exists(iconv) ? @iconv("UTF-8",$default_charset,$value) :
				$value;
		$value=trim($stringConvert);
		$field=vtlib_purify($input['search_field']);
 		$urlString = "&search_field=$field&search_text=".urlencode($value)."&searchtype=BasicSearch";
		if(!empty($input['type'])) {
			$urlString .= "&type=".vtlib_purify($input['type']);
		}
		if(!empty($input['operator'])) {
			$urlString .= "&operator=".vtlib_purify($input['operator']);
		}
	}
	return $urlString;
}

/**This function is returns the where conditions for dashboard and shows the records when clicked on dashboard graph
*Takes no parameter, process the values got from the html request object
*Returns the search criteria option (where condition) to be added in list query
*/

function getdashboardcondition($input = '')
{
	global $adb;

	if(empty($input)) {
		$input = $_REQUEST;
	}
	
	$where_clauses = Array();
	$url_string = "";

	if (isset($input['leadsource'])) $lead_source = $input['leadsource'];
	if (isset($input['date_closed'])) $date_closed = $input['date_closed'];
	if (isset($input['sales_stage'])) $sales_stage = $input['sales_stage'];
	if (isset($input['closingdate_start'])) $date_closed_start = $input['closingdate_start'];
	if (isset($input['closingdate_end'])) $date_closed_end = $input['closingdate_end'];
	if(isset($input['owner'])) $owner = vtlib_purify($input['owner']);
	if(isset($input['campaignid'])) $campaign = vtlib_purify($input['campaignid']);
	if(isset($input['quoteid'])) $quote = vtlib_purify($input['quoteid']);
	if(isset($input['invoiceid'])) $invoice = vtlib_purify($input['invoiceid']);
	if(isset($input['purchaseorderid'])) $po = vtlib_purify($input['purchaseorderid']);

	if(isset($date_closed_start) && $date_closed_start != "" && isset($date_closed_end) && $date_closed_end != "")
	{
		array_push($where_clauses, "vtiger_potential.closingdate >= ".$adb->quote($date_closed_start)." and vtiger_potential.closingdate <= ".$adb->quote($date_closed_end));
		$url_string .= "&closingdate_start=".$date_closed_start."&closingdate_end=".$date_closed_end;
	}

	if(isset($sales_stage) && $sales_stage!=''){
		if($sales_stage=='Other')
		array_push($where_clauses, "(vtiger_potential.sales_stage <> 'Closed Won' and vtiger_potential.sales_stage <> 'Closed Lost')");
		else
		array_push($where_clauses, "vtiger_potential.sales_stage = ".$adb->quote($sales_stage));
		$url_string .= "&sales_stage=".$sales_stage;
	}
	if(isset($lead_source) && $lead_source != "") {
		array_push($where_clauses, "vtiger_potential.leadsource = ".$adb->quote($lead_source));
		$url_string .= "&leadsource=".$lead_source;
	}
	if(isset($date_closed) && $date_closed != "") {
		array_push($where_clauses, $adb->getDBDateString("vtiger_potential.closingdate")." like ".$adb->quote($date_closed.'%')."");
		$url_string .= "&date_closed=".$date_closed;
	}
	if(isset($owner) && $owner != ""){
		$column = getSqlForNameInDisplayFormat(array('last_name'=>'last_name', 'first_name'=>'first_name'), 'Users');
		$user_qry="select vtiger_users.id from vtiger_users where $column = ?";
		$res = $adb->pquery($user_qry, array($owner));
		$uid = $adb->query_result($res,0,'id');
		array_push($where_clauses, "vtiger_crmentity.smownerid = ".$uid);
		//$url_string .= "&assigned_user_id=".$uid;
		$url_string .= "&owner=".$owner;
	}
	if(isset($campaign) && $campaign != "")
	{
		array_push($where_clauses, "vtiger_campaigncontrel.campaignid = ".$campaign);
                $url_string .= "&campaignid=".$campaign;
	}
	if(isset($quote) && $quote != "")
	{
		array_push($where_clauses, "vtiger_inventoryproductrel.id = ".$quote);
		$url_string .= "&quoteid=".$quote;
	}
	if(isset($invoice) && $invoice != "")
	{
		array_push($where_clauses, "vtiger_inventoryproductrel.id = ".$invoice);
		$url_string .= "&invoiceid=".$invoice;
	}
	if(isset($po) && $po != "")
	{
		array_push($where_clauses, "vtiger_inventoryproductrel.id = ".$po);
		$url_string .= "&purchaseorderid=".$po;
	}
	if(isset($input['from_homepagedb']) && $input['from_homepagedb'] != '') {
		$url_string .= "&from_homepagedb=".vtlib_purify($input['from_homepagedb']);
	}
	if(isset($input['type']) && $input['type'] != '') {
		$url_string .= "&type=".vtlib_purify($input['type']);
	}

	$where = "";
	foreach($where_clauses as $clause)
	{
		if($where != "")
		$where .= " and ";
		$where .= $clause;
	}
	return $where."#@@#".$url_string;
}

/**This function is used to replace only the first occurence of a given string
Param $needle - string to be replaced
Param $replace - string to be replaced with
Param $replace - given string
Return type is string
*/
function str_replace_once($needle, $replace, $haystack)
{
	// Looks for the first occurence of $needle in $haystack
	// and replaces it with $replace.
	$pos = strpos($haystack, $needle);
	if ($pos === false) {
		// Nothing found
		return $haystack;
	}
	return substr_replace($haystack, $replace, $pos, strlen($needle));
}

/**
 * Function to get the where condition for a module based on the field table entries
 * @param  string $listquery  -- ListView query for the module
 * @param  string $module     -- module name
 * @param  string $search_val -- entered search string value
 * @return string $where      -- where condition for the module based on field table entries
 */
function getUnifiedWhere($listquery,$module,$search_val){
	global $adb, $current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');

	$search_val = $adb->sql_escape_string($search_val);
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] ==0){
		$query = "SELECT columnname, tablename FROM vtiger_field WHERE tabid = ? and vtiger_field.presence in (0,2)";
		$qparams = array(getTabid($module));
	}else{
		$profileList = getCurrentUserProfileList();
		$query = "SELECT columnname, tablename FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid = vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid = vtiger_field.fieldid WHERE vtiger_field.tabid = ? AND vtiger_profile2field.visible = 0 AND vtiger_profile2field.profileid IN (". generateQuestionMarks($profileList) . ") AND vtiger_def_org_field.visible = 0 and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
		$qparams = array(getTabid($module), $profileList);
	}
	$result = $adb->pquery($query, $qparams);
	$noofrows = $adb->num_rows($result);

	$where = '';
	for($i=0;$i<$noofrows;$i++){
		$columnname = $adb->query_result($result,$i,'columnname');
		$tablename = $adb->query_result($result,$i,'tablename');

		// Search / Lookup customization
		if($module == 'Contacts' && $columnname == 'accountid') {
			$columnname = "accountname";
			$tablename = "vtiger_account";
		}
		// END

		//Before form the where condition, check whether the table for the field has been added in the listview query
		if(strstr($listquery,$tablename)){
			if($where != ''){
				$where .= " OR ";
			}
			$where .= $tablename.".".$columnname." LIKE '". formatForSqlLike($search_val) ."'";
		}
	}
	return $where;
}

function getAdvancedSearchCriteriaList($advft_criteria, $advft_criteria_groups, $module='') {
	global $currentModule, $current_user;
	if(empty($module)) {
		$module = $currentModule;
	}

	$advfilterlist = array();

	$moduleHandler = vtws_getModuleHandlerFromName($module,$current_user);
	$moduleMeta = $moduleHandler->getMeta();
	$moduleFields = $moduleMeta->getModuleFields();

	foreach($advft_criteria as $column_index => $column_condition) {
		if(empty($column_condition)) continue;

		$adv_filter_column = $column_condition["columnname"];
		$adv_filter_comparator = $column_condition["comparator"];
		$adv_filter_value = $column_condition["value"];
		$adv_filter_column_condition = $column_condition["columncondition"];
		$adv_filter_groupid = $column_condition["groupid"];

		$column_info = explode(":",$adv_filter_column);

		$fieldName = $column_info[2];
		$fieldObj = $moduleFields[$fieldName];
		$fieldType = $fieldObj->getFieldDataType();

		if($fieldType == 'currency') {
			// Some of the currency fields like Unit Price, Total, Sub-total etc of Inventory modules, do not need currency conversion
			if($fieldObj->getUIType() == '72') {
				$adv_filter_value = CurrencyField::convertToDBFormat($adv_filter_value, null, true);
			} else {
				$currencyField = new CurrencyField($adv_filter_value);
				if($module == 'Potentials' && $fieldName == 'amount') {
					$currencyField->setNumberofDecimals(2);
				}
				$adv_filter_value = $currencyField->getDBInsertedValue();
			}
		}

		$criteria = array();
		$criteria['columnname'] = $adv_filter_column;
		$criteria['comparator'] = $adv_filter_comparator;
		$criteria['value'] = $adv_filter_value;
		$criteria['column_condition'] = $adv_filter_column_condition;

		$advfilterlist[$adv_filter_groupid]['columns'][] = $criteria;
	}

	foreach($advft_criteria_groups as $group_index => $group_condition_info) {
		if(empty($group_condition_info)) continue;
		if(empty($advfilterlist[$group_index])) continue;
		$advfilterlist[$group_index]['condition'] = $group_condition_info["groupcondition"];
		$noOfGroupColumns = count($advfilterlist[$group_index]['columns']);
		if(!empty($advfilterlist[$group_index]['columns'][$noOfGroupColumns-1]['column_condition'])) {
			$advfilterlist[$group_index]['columns'][$noOfGroupColumns-1]['column_condition'] = '';
		}
	}
	$noOfGroups = count($advfilterlist);
	if(!empty($advfilterlist[$noOfGroups]['condition'])) {
		$advfilterlist[$noOfGroups]['condition'] = '';
	}
	return $advfilterlist;
}

function generateAdvancedSearchSql($advfilterlist) {
	global $log, $currentModule,$column_array,$current_user;

	$advfiltersql = "";

	foreach($advfilterlist as $groupindex => $groupinfo) {
		$groupcondition = $groupinfo['condition'];
		$groupcolumns = $groupinfo['columns'];

		if(count($groupcolumns) > 0) {

			$advfiltergroupsql = "";
			$advorsql = array();
			foreach($groupcolumns as $columnindex => $columninfo) {
				$fieldcolname = $columninfo["columnname"];
				$comparator = $columninfo["comparator"];
				$value = $columninfo["value"];
				$columncondition = $columninfo["column_condition"];

				$columns = explode(":",$fieldcolname);
				$datatype = (isset($columns[4])) ? $columns[4] : "";

				if($fieldcolname != "" && $comparator != "") {
					$valuearray = explode(",",trim($value));
					if(isset($valuearray) && count($valuearray) > 0 && $comparator != 'bw') {
						for($n=0;$n<count($valuearray);$n++) {
							$advorsql[] = getAdvancedSearchValue($columns[0],$columns[1],$comparator,trim($valuearray[$n]),$datatype);
						}
						//If negative logic filter ('not equal to', 'does not contain') is used, 'and' condition should be applied instead of 'or'
						if($comparator == 'n' || $comparator == 'k' || $comparator == 'h' || $comparator == 'l')
							$advorsqls = implode(" and ",$advorsql);
						else
							$advorsqls = implode(" or ",$advorsql);
						$advfiltersql = " (".$advorsqls.") ";
					}
					elseif($comparator == 'bw' && count($valuearray) == 2) {
						$advfiltersql = "(".$columns[0].".".$columns[1]." between '".getValidDBInsertDateTimeValue(trim($valuearray[0]),$datatype)."' and '".getValidDBInsertDateTimeValue(trim($valuearray[1]),$datatype)."')";
					}
					else {
						//Added for getting vtiger_activity Status -Jaguar
						if($currentModule == "Calendar" && ($columns[1] == "status" || $columns[1] == "eventstatus")) {
							if(getFieldVisibilityPermission("Calendar", $current_user->id,'taskstatus') == '0') {
								$advfiltersql = "case when (vtiger_activity.status not like '') then vtiger_activity.status else vtiger_activity.eventstatus end".getAdvancedSearchComparator($comparator,trim($value),$datatype);
							}
							else
								$advfiltersql = "vtiger_activity.eventstatus".getAdvancedSearchComparator($comparator,trim($value),$datatype);
						}
						elseif($currentModule == "Documents" && $columns[1]=='folderid'){
							$advfiltersql = "vtiger_attachmentsfolder.foldername".getAdvancedSearchComparator($comparator,trim($value),$datatype);
						}
						elseif($currentModule == "Assets") {
							if($columns[1]=='account' ){
								$advfiltersql = "vtiger_account.accountname".getAdvancedSearchComparator($comparator,trim($value),$datatype);
							}
							if($columns[1]=='product'){
								$advfiltersql = "vtiger_products.productname".getAdvancedSearchComparator($comparator,trim($value),$datatype);
							}
							if($columns[1]=='invoiceid'){
								$advfiltersql = "vtiger_invoice.subject".getAdvancedSearchComparator($comparator,trim($value),$datatype);
							}
						}
						else {
							$advfiltersql = getAdvancedSearchValue($columns[0],$columns[1],$comparator,trim($value),$datatype);
						}
					}

					$advfiltergroupsql .= $advfiltersql;
					if(!empty($columncondition)) {
						$advfiltergroupsql .= ' '.$columncondition.' ';
					}
				}
			}

			if (trim($advfiltergroupsql) != "") {
				$advfiltergroupsql =  "( $advfiltergroupsql ) ";
				if(!empty($groupcondition)) {
					$advfiltergroupsql .= ' '. $groupcondition . ' ';
				}

				$advcvsql .= $advfiltergroupsql;
			}
		}
	}
	return $advfiltersql;
}


function getAdvancedSearchComparator($comparator,$value,$datatype = '') {

	global $adb, $default_charset;
	$value=html_entity_decode(trim($value),ENT_QUOTES,$default_charset);
	$value = $adb->sql_escape_string($value);
	if($datatype == 'DT' || $datatype == 'D') {
		$value = getValidDBInsertDateTimeValue($value, $datatype);
	}

	if($comparator == "e") {
		if(trim($value) == "NULL") {
			$rtvalue = " is NULL";
		} elseif(trim($value) != "") {
			$rtvalue = " = ".$adb->quote($value);
		} elseif(trim($value) == "" && ($datatype == "V" || $datatype == "E")) {
			$rtvalue = " = ".$adb->quote($value);
		} else {
			$rtvalue = " is NULL";
		}
	}
	if($comparator == "n") {
		if(trim($value) == "NULL") {
			$rtvalue = " is NOT NULL";
		} elseif(trim($value) != "") {
			$rtvalue = " <> ".$adb->quote($value);
		} elseif(trim($value) == "" && $datatype == "V") {
			$rtvalue = " <> ".$adb->quote($value);
		}elseif(trim($value) == "" && $datatype == "E") {
			$rtvalue = " <> ".$adb->quote($value);
		} else {
			$rtvalue = " is NOT NULL";
		}
	}
	if($comparator == "s") {
		if(trim($value) == "" && ($datatype == "V" || $datatype == "E")) {
			$rtvalue = " like '". formatForSqlLike($value, 3) ."'";
		} else {
			$rtvalue = " like '". formatForSqlLike($value, 2) ."'";
		}
	}
	if($comparator == "ew") {
		if(trim($value) == "" && ($datatype == "V" || $datatype == "E")) {
			$rtvalue = " like '". formatForSqlLike($value, 3) ."'";
		} else {
			$rtvalue = " like '". formatForSqlLike($value, 1) ."'";
		}
	}
	if($comparator == "c") {
		if(trim($value) == "" && ($datatype == "V" || $datatype == "E")) {
			$rtvalue = " like '". formatForSqlLike($value, 3) ."'";
		} else {
			$rtvalue = " like '". formatForSqlLike($value) ."'";
		}
	}
	if($comparator == "k") {
		if(trim($value) == "" && ($datatype == "V" || $datatype == "E")) {
			$rtvalue = " not like ''";
		} else {
			$rtvalue = " not like '". formatForSqlLike($value) ."'";
		}
	}
	if($comparator == "l") {
		$rtvalue = " < ".$adb->quote($value);
	}
	if($comparator == "g") {
		$rtvalue = " > ".$adb->quote($value);
	}
	if($comparator == "m") {
		$rtvalue = " <= ".$adb->quote($value);
	}
	if($comparator == "h") {
		$rtvalue = " >= ".$adb->quote($value);
	}
	if($comparator == "b") {
		$rtvalue = " < ".$adb->quote($value);
	}
	if($comparator == "a") {
		$rtvalue = " > ".$adb->quote($value);
	}

	return $rtvalue;
}

function getAdvancedSearchValue($tablename,$fieldname,$comparator,$value,$datatype) {
	//we have to add the fieldname/tablename.fieldname and the corresponding value (which we want) we can add here. So that when these LHS field comes then RHS value will be replaced for LHS in the where condition of the query
	global $adb, $mod_strings, $currentModule, $current_user;
	//Added for proper check of contact name in advance filter
	if($tablename == "vtiger_contactdetails" && $fieldname == "lastname")
		$fieldname = "contactid";

	$contactid = "vtiger_contactdetails.lastname";
	if ($currentModule != "Contacts" && $currentModule != "Leads" && $currentModule != 'Campaigns') {
		$contactid = getSqlForNameInDisplayFormat(array('lastname'=>'vtiger_contactdetails.lastname', 'firstname'=>'vtiger_contactdetails.firstname'), 'Contacts');
	}
	$change_table_field = Array(

		"product_id"=>"vtiger_products.productname",
		"contactid"=>$contactid,
		"contact_id"=>$contactid,
		"accountid"=>"",//in cvadvfilter accountname is stored for Contact, Potential, Quotes, SO, Invoice
		"account_id"=>"",//Same like accountid. No need to change
		"vendorid"=>"vtiger_vendor.vendorname",
		"vendor_id"=>"vtiger_vendor.vendorname",
		"potentialid"=>"vtiger_potential.potentialname",

		"vtiger_account.parentid"=>"vtiger_account2.accountname",
		"quoteid"=>"vtiger_quotes.subject",
		"salesorderid"=>"vtiger_salesorder.subject",
		"campaignid"=>"vtiger_campaign.campaignname",
		"vtiger_contactdetails.reportsto"=> getSqlForNameInDisplayFormat(array('lastname'=>'vtiger_contactdetails2.lastname', 'firstname'=>'vtiger_contactdetails2.firstname'), 'Contacts'),
		"vtiger_pricebook.currency_id"=>"vtiger_currency_info.currency_name",
		);
	if($fieldname == "smownerid" || $fieldname == 'modifiedby')
    {
		if($fieldname == "smownerid") {
			$tableNameSuffix = '';
		} elseif($fieldname == "modifiedby") {
			$tableNameSuffix = '2';
		}
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users'.$tableNameSuffix.'.first_name', 'last_name'=>'vtiger_users'.$tableNameSuffix.'.last_name'), 'Users');
        $temp_value = "( trim($userNameSql)".getAdvancedSearchComparator($comparator,$value,$datatype);
        $temp_value.= " OR  vtiger_groups$tableNameSuffix.groupname".getAdvancedSearchComparator($comparator,$value,$datatype);
        $value=$temp_value.")";
	}elseif( $fieldname == "inventorymanager")
            {
		$value = $tablename.".".$fieldname.getAdvancedSearchComparator($comparator,getUserId_Ol($value),$datatype);
            }
	elseif($change_table_field[$fieldname] != '')//Added to handle special cases
	{
		$value = $change_table_field[$fieldname].getAdvancedSearchComparator($comparator,$value,$datatype);
	}
	elseif($change_table_field[$tablename.".".$fieldname] != '')//Added to handle special cases
	{
		$tmp_value = '';
		if((($comparator == 'e' || $comparator == 's' || $comparator == 'c') && trim($value) == '') || (($comparator == 'n' || $comparator == 'k') && trim($value) != ''))
		{
			$tmp_value = $change_table_field[$tablename.".".$fieldname].' IS NULL or ';
		}
		$value = $tmp_value.$change_table_field[$tablename.".".$fieldname].getAdvancedSearchComparator($comparator,$value,$datatype);
	}
	elseif(($fieldname == "crmid" && $tablename != 'vtiger_crmentity') || $fieldname == "parent_id" || $fieldname == 'parentid')
	{
		//For crmentity.crmid the control should not come here. This is only to get the related to modules
		$value = getAdvancedSearchParentEntityValue($comparator,$value,$datatype,$tablename,$fieldname);
	}
	else
	{
		//For checkbox type values, we have to convert yes/no as 1/0 to get the values
		$field_uitype = getUItype($currentModule, $fieldname);
		if($field_uitype == 56)
		{
			if(strtolower($value) == 'yes')         $value = 1;
			elseif(strtolower($value) ==  'no')     $value = 0;
		} else if(is_uitype($field_uitype, '_picklist_')) { /* Fix for tickets 4465 and 4629 */
			// Get all the keys for the for the Picklist value
			$mod_keys = array_keys($mod_strings, $value);

			// Iterate on the keys, to get the first key which doesn't start with LBL_      (assuming it is not used in PickList)
			foreach($mod_keys as $mod_idx=>$mod_key) {
				$stridx = strpos($mod_key, 'LBL_');
			// Use strict type comparision, refer strpos for more details
			if ($stridx !== 0) {
				$value = $mod_key;
				break;
				}
			}
		}
		//added to fix the ticket
		if($currentModule == "Calendar" && ($fieldname=="status" || $fieldname=="taskstatus" || $fieldname=="eventstatus"))
		{
			if(getFieldVisibilityPermission("Calendar", $current_user->id,'taskstatus') == '0')
			{
				$value = " (case when (vtiger_activity.status not like '') then vtiger_activity.status else vtiger_activity.eventstatus end)".getAdvancedSearchComparator($comparator,$value,$datatype);
			}
			else
				$value = " vtiger_activity.eventstatus ".getAdvancedSearchComparator($comparator,$value,$datatype);
		} elseif ($comparator == 'e' && (trim($value) == "NULL" || trim($value) == '')) {
			$value = '('.$tablename.".".$fieldname.' IS NULL OR '.$tablename.".".$fieldname.' = \'\')';
		} else {
			$value = $tablename.".".$fieldname.getAdvancedSearchComparator($comparator,$value,$datatype);
		}
		//end
	}
	return $value;
}

function getAdvancedSearchParentEntityValue($comparator,$value,$datatype,$tablename,$fieldname) {
	global $log, $adb;

	$adv_chk_value = $value;
	$value = '(';
	$sql = "select distinct(setype) from vtiger_crmentity c INNER JOIN ".$adb->sql_escape_string($tablename)." t ON t.".$adb->sql_escape_string($fieldname)." = c.crmid";
	$res=$adb->pquery($sql, array());
	for($s=0;$s<$adb->num_rows($res);$s++)
	{
		$modulename=$adb->query_result($res,$s,"setype");
		if($modulename == 'Vendors')
		{
			continue;
		}
		if($s != 0)
             $value .= ' or ';
		if($modulename == 'Accounts')
		{
			//By Pavani : Related to problem in calender, Ticket: 4284 and 4675
			if(($comparator == 'e' || $comparator == 's' || $comparator == 'c') && trim($adv_chk_value) == '')
			{
				if($tablename == 'vtiger_seactivityrel' && $fieldname == 'crmid')
				{
					$value .= 'vtiger_account2.accountname IS NULL or ';
				}
				else{
					$value .= 'vtiger_account.accountname IS NULL or ';
				}
			}
			if($tablename == 'vtiger_seactivityrel' && $fieldname == 'crmid')
				{
					$value .= 'vtiger_account2.accountname';
				}
				else{
					$value .= 'vtiger_account.accountname';
				}
		}
		if($modulename == 'Leads') {
			$concatSql = getSqlForNameInDisplayFormat(array('lastname'=>'vtiger_leaddetails.lastname', 'firstname'=>'vtiger_leaddetails.firstname'), 'Leads');
			if(($comparator == 'e' || $comparator == 's' || $comparator == 'c') && trim($adv_chk_value) == '') {
				$value .= " $concatSql IS NULL or ";
			}
			$value .= " $concatSql";
		}
		if($modulename == 'Potentials')
		{
			if(($comparator == 'e' || $comparator == 's' || $comparator == 'c') && trim($adv_chk_value) == '')
			{

			$value .= ' vtiger_potential.potentialname IS NULL or ';
			}
			$value .= ' vtiger_potential.potentialname';
		}
		if($modulename == 'Products')
		{
			if(($comparator == 'e' || $comparator == 's' || $comparator == 'c') && trim($adv_chk_value) == '')
			{
				$value .= ' vtiger_products.productname IS NULL or ';
			}
			$value .= ' vtiger_products.productname';
		}
		if($modulename == 'Invoice')
		{
			if(($comparator == 'e' || $comparator == 's' || $comparator == 'c') && trim($adv_chk_value) == '')
			{
				$value .= ' vtiger_invoice.subject IS NULL or ';
			}
			$value .= ' vtiger_invoice.subject';
		}
		if($modulename == 'PurchaseOrder')
		{
			if(($comparator == 'e' || $comparator == 's' || $comparator == 'c') && trim($adv_chk_value) == '')
			{
				$value .= ' vtiger_purchaseorder.subject IS NULL or ';
			}
			$value .= ' vtiger_purchaseorder.subject';
		}
		if($modulename == 'SalesOrder')
		{
			if(($comparator == 'e' || $comparator == 's' || $comparator == 'c') && trim($adv_chk_value) == '')
			{
				$value .= ' vtiger_salesorder.subject IS NULL or ';
			}
			$value .= ' vtiger_salesorder.subject';
		}
		if($modulename == 'Quotes')
		{

			if(($comparator == 'e' || $comparator == 's' || $comparator == 'c') && trim($adv_chk_value) == '')
			{
				$value .= ' vtiger_quotes.subject IS NULL or ';
			}
			$value .= ' vtiger_quotes.subject';
		}
		if($modulename == 'Contacts') {
			$concatSql = getSqlForNameInDisplayFormat(array('lastname'=>'vtiger_contactdetails.lastname', 'firstname'=>'vtiger_contactdetails.firstname'), 'Contacts');
			if(($comparator == 'e' || $comparator == 's' || $comparator == 'c') && trim($adv_chk_value) == '') {
				$value .= " $concatSql IS NULL or ";
			}
			$value .= " $concatSql";
		}
		if($modulename == 'HelpDesk')
		{
			if(($comparator == 'e' || $comparator == 's' || $comparator == 'c') && trim($adv_chk_value) == '')
			{
				$value .= ' vtiger_troubletickets.title IS NULL or ';
			}
			$value .= ' vtiger_troubletickets.title';

		}
		if($modulename == 'Campaigns')
		{
			if(($comparator == 'e' || $comparator == 's' || $comparator == 'c') && trim($adv_chk_value) == '')
			{
				$value .= ' vtiger_campaign.campaignname IS NULL or ';
			}
			$value .= ' vtiger_campaign.campaignname';
		}

		$value .= getAdvancedSearchComparator($comparator,$adv_chk_value,$datatype);
	}
	$value .= ")";
            $log->info("in getSalesRelatedName ".$comparator."==".$value."==".$datatype."==".$tablename."==".$fieldname);
	return $value;
}

/**
 * Function to get the Tags where condition
 * @param  string $search_val -- entered search string value
 * @param  string $current_user_id     -- current user id
 * @return string $where      -- where condition with the list of crmids, will like vtiger_crmentity.crmid in (1,3,4,etc.,)
 */
function getTagWhere($search_val,$current_user_id){
	require_once('include/freetag/freetag.class.php');

	$freetag_obj = new freetag();
	$crmid_array = $freetag_obj->get_objects_with_tag_all($search_val,$current_user_id);

	$where = " vtiger_crmentity.crmid IN (";
	if(count($crmid_array) > 0){
		foreach($crmid_array as $index => $crmid){
			$where .= $crmid.',';
		}
		$where = trim($where,',').')';
	}
	//If there are no records has the search tag we need to add the condition like crmid is none. If dont add condition at all search will return all the values.
	// Fix for #5571
	else {
		$where .= '0)';
	}
	return $where;
}

?>