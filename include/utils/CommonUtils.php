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
 * Description:  Includes generic helper functions used throughout the application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 * ****************************************************************************** */

require_once('include/utils/utils.php'); //new
require_once('include/utils/RecurringType.php');
require_once('include/utils/EmailTemplate.php');
require_once 'include/QueryGenerator/QueryGenerator.php';
require_once 'include/ListView/ListViewController.php';

/**
 * Check if user id belongs to a system admin.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function is_admin($user) {
	global $log;
	$log->debug("Entering is_admin(" . $user->user_name . ") method ...");

	if ($user->is_admin == 'on') {
		$log->debug("Exiting is_admin method ...");
		return true;
	} else {
		$log->debug("Exiting is_admin method ...");
		return false;
	}
}

/**
 * THIS FUNCTION IS DEPRECATED AND SHOULD NOT BE USED; USE get_select_options_with_id()
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.
 * param $option_list - the array of strings to that contains the option list
 * param $selected - the string which contains the default value
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_select_options(&$option_list, $selected, $advsearch = 'false') {
	global $log;
	$log->debug("Entering get_select_options (" . $option_list . "," . $selected . "," . $advsearch . ") method ...");
	$log->debug("Exiting get_select_options  method ...");
	return get_select_options_with_id($option_list, $selected, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.  The values is an array of the datas
 * param $option_list - the array of strings to that contains the option list
 * param $selected - the string which contains the default value
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_select_options_with_id(&$option_list, $selected_key, $advsearch = 'false') {
	global $log;
	$log->debug("Entering get_select_options_with_id (" . $option_list . "," . $selected_key . "," . $advsearch . ") method ...");
	$log->debug("Exiting get_select_options_with_id  method ...");
	return get_select_options_with_id_separate_key($option_list, $option_list, $selected_key, $advsearch);
}

function get_select_options_with_value(&$option_list, $selected_key, $advsearch = 'false') {
	global $log;
	$log->debug("Entering get_select_options_with_id (" . $option_list . "," . $selected_key . "," . $advsearch . ") method ...");
	$log->debug("Exiting get_select_options_with_id  method ...");
	return get_select_options_with_value_separate_key($option_list, $option_list, $selected_key, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.
 * The values are the display strings.
 */
function get_select_options_array(&$option_list, $selected_key, $advsearch = 'false') {
	global $log;
	$log->debug("Entering get_select_options_array (" . $option_list . "," . $selected_key . "," . $advsearch . ") method ...");
	$log->debug("Exiting get_select_options_array  method ...");
	return get_options_array_seperate_key($option_list, $option_list, $selected_key, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.  The value is an array of data
 * param $label_list - the array of strings to that contains the option list
 * param $key_list - the array of strings to that contains the values list
 * param $selected - the string which contains the default value
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_options_array_seperate_key(&$label_list, &$key_list, $selected_key, $advsearch = 'false') {
	global $log;
	$log->debug("Entering get_options_array_seperate_key (" . $label_list . "," . $key_list . "," . $selected_key . "," . $advsearch . ") method ...");
	global $app_strings;
	if ($advsearch == 'true')
		$select_options = "\n<OPTION value=''>--NA--</OPTION>";
	else
		$select_options = "";

	//for setting null selection values to human readable --None--
	$pattern = "/'0?'></";
	$replacement = "''>" . $app_strings['LBL_NONE'] . "<";
	if (!is_array($selected_key))
		$selected_key = array($selected_key);

	//create the type dropdown domain and set the selected value if $opp value already exists
	foreach ($key_list as $option_key => $option_value) {
		$selected_string = '';
		// the system is evaluating $selected_key == 0 || '' to true.  Be very careful when changing this.  Test all cases.
		// The vtiger_reported bug was only happening with one of the vtiger_users in the drop down.  It was being replaced by none.
		if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key))) {
			$selected_string = 'selected';
		}

		$html_value = $option_key;

		$select_options .= "\n<OPTION " . $selected_string . "value='$html_value'>$label_list[$option_key]</OPTION>";
		$options[$html_value] = array($label_list[$option_key] => $selected_string);
	}
	$select_options = preg_replace($pattern, $replacement, $select_options);

	$log->debug("Exiting get_options_array_seperate_key  method ...");
	return $options;
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.
 * The values are the display strings.
 */
function get_select_options_with_id_separate_key(&$label_list, &$key_list, $selected_key, $advsearch = 'false') {
	global $log;
	$log->debug("Entering get_select_options_with_id_separate_key(" . $label_list . "," . $key_list . "," . $selected_key . "," . $advsearch . ") method ...");
	global $app_strings;
	if ($advsearch == 'true')
		$select_options = "\n<OPTION value=''>--NA--</OPTION>";
	else
		$select_options = "";

	$pattern = "/'0?'></";
	$replacement = "''>" . $app_strings['LBL_NONE'] . "<";
	if (!is_array($selected_key))
		$selected_key = array($selected_key);

	foreach ($key_list as $option_key => $option_value) {
		$selected_string = '';
		if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key))) {
			$selected_string = 'selected ';
		}

		$html_value = $option_key;

		$select_options .= "\n<OPTION " . $selected_string . "value='$html_value'>$label_list[$option_key]</OPTION>";
	}
	$select_options = preg_replace($pattern, $replacement, $select_options);
	$log->debug("Exiting get_select_options_with_id_separate_key method ...");
	return $select_options;
}

function get_select_options_with_value_separate_key(&$label_list, &$key_list, $selected_key, $advsearch = 'false') {
	global $log;
	$log->debug("Entering get_select_options_with_id_separate_key(" . $label_list . "," . $key_list . "," . $selected_key . "," . $advsearch . ") method ...");
	global $app_strings;
	if ($advsearch == 'true')
		$select_options = "\n<OPTION value=''>--NA--</OPTION>";
	else
		$select_options = "";

	$pattern = "/'0?'></";
	$replacement = "''>" . $app_strings['LBL_NONE'] . "<";
	if (!is_array($selected_key))
		$selected_key = array($selected_key);

	foreach ($key_list as $option_key => $option_value) {
		$selected_string = '';
		if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key))) {
			$selected_string = 'selected ';
		}

		$html_value = $option_key;

		$select_options .= "\n<OPTION " . $selected_string . "value='$label_list[$option_key]'>$label_list[$option_key]</OPTION>";
	}
	$select_options = preg_replace($pattern, $replacement, $select_options);
	$log->debug("Exiting get_select_options_with_id_separate_key method ...");
	return $select_options;
}

/**
 * Converts localized date format string to jscalendar format
 * Example: $array = array_csort($array,'town','age',SORT_DESC,'name');
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function parse_calendardate($local_format) {
	global $log;
	$log->debug("Entering parse_calendardate(" . $local_format . ") method ...");
	global $current_user;
	if ($current_user->date_format == 'dd-mm-yyyy') {
		$dt_popup_fmt = "%d-%m-%Y";
	} elseif ($current_user->date_format == 'mm-dd-yyyy') {
		$dt_popup_fmt = "%m-%d-%Y";
	} elseif ($current_user->date_format == 'yyyy-mm-dd') {
		$dt_popup_fmt = "%Y-%m-%d";
	}
	$log->debug("Exiting parse_calendardate method ...");
	return $dt_popup_fmt;
}

/**
 * Decodes the given set of special character
 * input values $string - string to be converted, $encode - flag to decode
 * returns the decoded value in string fromat
 */
function from_html($string, $encode = true) {
	global $log;
	//$log->debug("Entering from_html(".$string.",".$encode.") method ...");
	global $toHtml;
	//if($encode && is_string($string))$string = html_entity_decode($string, ENT_QUOTES);
	if (is_string($string)) {
		if (preg_match('/(script).*(\/script)/i', $string))
			$string = preg_replace(array('/</', '/>/', '/"/'), array('&lt;', '&gt;', '&quot;'), $string);
		//$string = str_replace(array_values($toHtml), array_keys($toHtml), $string);
	}
	//$log->debug("Exiting from_html method ...");
	return $string;
}

function fck_from_html($string) {
	if (is_string($string)) {
		if (preg_match('/(script).*(\/script)/i', $string))
			$string = str_replace('script', '', $string);
	}
	return $string;
}

/**
 * 	Function used to decodes the given single quote and double quote only. This function used for popup selection
 * 	@param string $string - string to be converted, $encode - flag to decode
 * 	@return string $string - the decoded value in string fromat where as only single and double quotes will be decoded
 */
function popup_from_html($string, $encode = true) {
	global $log;
	$log->debug("Entering popup_from_html(" . $string . "," . $encode . ") method ...");

	$popup_toHtml = array(
		'"' => '&quot;',
		"'" => '&#039;',
	);

	//if($encode && is_string($string))$string = html_entity_decode($string, ENT_QUOTES);
	if ($encode && is_string($string)) {
		$string = addslashes(str_replace(array_values($popup_toHtml), array_keys($popup_toHtml), $string));
	}

	$log->debug("Exiting popup_from_html method ...");
	return $string;
}

/** To get the Currency of the specified user
 * @param $id -- The user Id:: Type integer
 * @returns  vtiger_currencyid :: Type integer
 */
function fetchCurrency($id) {
	global $log;
	$log->debug("Entering fetchCurrency(" . $id . ") method ...");

	// Lookup the information in cache
	$currencyinfo = VTCacheUtils::lookupUserCurrenyId($id);

	if ($currencyinfo === false) {
		global $adb;
		$sql = "select currency_id from vtiger_users where id=?";
		$result = $adb->pquery($sql, array($id));
		$currencyid = $adb->query_result($result, 0, "currency_id");

		VTCacheUtils::updateUserCurrencyId($id, $currencyid);

		// Re-look at the cache for consistency
		$currencyinfo = VTCacheUtils::lookupUserCurrenyId($id);
	}

	$currencyid = $currencyinfo['currencyid'];
	$log->debug("Exiting fetchCurrency method ...");
	return $currencyid;
}

/** Function to get the Currency name from the vtiger_currency_info
 * @param $currencyid -- vtiger_currencyid:: Type integer
 * @returns $currencyname -- Currency Name:: Type varchar
 *
 */
function getCurrencyName($currencyid, $show_symbol = true) {
	global $log;
	$log->debug("Entering getCurrencyName(" . $currencyid . ") method ...");

	// Look at cache first
	$currencyinfo = VTCacheUtils::lookupCurrencyInfo($currencyid);

	if ($currencyinfo === false) {
		global $adb;
		$sql1 = "select * from vtiger_currency_info where id= ?";
		$result = $adb->pquery($sql1, array($currencyid));

		$resultinfo = $adb->fetch_array($result);

		// Update cache
		VTCacheUtils::updateCurrencyInfo($currencyid, $resultinfo['currency_name'], $resultinfo['currency_code'], $resultinfo['currency_symbol'], $resultinfo['conversion_rate']
		);

		// Re-look at the cache now
		$currencyinfo = VTCacheUtils::lookupCurrencyInfo($currencyid);
	}

	$currencyname = $currencyinfo['name'];
	$curr_symbol = $currencyinfo['symbol'];

	$log->debug("Exiting getCurrencyName method ...");
	if ($show_symbol)
		return getTranslatedCurrencyString($currencyname) . ' : ' . $curr_symbol;
	else
		return $currencyname;
	// NOTE: Without symbol the value could be used for filtering/lookup hence avoiding the translation
}

/**
 * Function to fetch the list of vtiger_groups from group vtiger_table
 * Takes no value as input
 * returns the query result set object
 */
function get_group_options() {
	global $log;
	$log->debug("Entering get_group_options() method ...");
	global $adb, $noof_group_rows;
	;
	$sql = "select groupname,groupid from vtiger_groups";
	$result = $adb->pquery($sql, array());
	$noof_group_rows = $adb->num_rows($result);
	$log->debug("Exiting get_group_options method ...");
	return $result;
}

/**
 * Function to get the tabid
 * Takes the input as $module - module name
 * returns the tabid, integer type
 */
function getTabid($module) {
	global $log;
	$log->debug("Entering getTabid(" . $module . ") method ...");

	// Lookup information in cache first
	$tabid = VTCacheUtils::lookupTabid($module);
	if ($tabid === false) {

		if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) {
			include('tabdata.php');
			$tabid = $tab_info_array[$module];

			// Update information to cache for re-use
			VTCacheUtils::updateTabidInfo($tabid, $module);
		} else {
			$log->info("module  is " . $module);
			global $adb;
			$sql = "select tabid from vtiger_tab where name=?";
			$result = $adb->pquery($sql, array($module));
			$tabid = $adb->query_result($result, 0, "tabid");

			// Update information to cache for re-use
			VTCacheUtils::updateTabidInfo($tabid, $module);
		}
	}

	$log->debug("Exiting getTabid method ...");
	return $tabid;
}

/**
 * Function to get the fieldid
 *
 * @param Integer $tabid
 * @param Boolean $onlyactive
 */
function getFieldid($tabid, $fieldname, $onlyactive = true) {
	global $adb;

	// Look up information at cache first
	$fieldinfo = VTCacheUtils::lookupFieldInfo($tabid, $fieldname);
	if ($fieldinfo === false) {
		getColumnFields(getTabModuleName($tabid));
		$fieldinfo = VTCacheUtils::lookupFieldInfo($tabid, $fieldname);
	}

	// Get the field id based on required criteria
	$fieldid = false;

	if ($fieldinfo) {
		$fieldid = $fieldinfo['fieldid'];
		if ($onlyactive && !in_array($fieldinfo['presence'], array('0', '2'))) {
			$fieldid = false;
		}
	}
	return $fieldid;
}

/**
 * Function to get the CustomViewName
 * Takes the input as $cvid - customviewid
 * returns the cvname string fromat
 */
function getCVname($cvid) {
	global $log;
	$log->debug("Entering getCVname method ...");

	global $adb;
	$sql = "select viewname from vtiger_customview where cvid=?";
	$result = $adb->pquery($sql, array($cvid));
	$cvname = $adb->query_result($result, 0, "viewname");

	$log->debug("Exiting getCVname method ...");
	return $cvname;
}

/**
 * Function to get the ownedby value for the specified module
 * Takes the input as $module - module name
 * returns the tabid, integer type
 */
function getTabOwnedBy($module) {
	global $log;
	$log->debug("Entering getTabid(" . $module . ") method ...");

	$tabid = getTabid($module);

	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) {
		include('tabdata.php');
		$tab_ownedby = $tab_ownedby_array[$tabid];
	} else {

		$log->info("module  is " . $module);
		global $adb;
		$sql = "select ownedby from vtiger_tab where name=?";
		$result = $adb->pquery($sql, array($module));
		$tab_ownedby = $adb->query_result($result, 0, "ownedby");
	}
	$log->debug("Exiting getTabid method ...");
	return $tab_ownedby;
}

/**
 * Function to get the tabid
 * Takes the input as $module - module name
 * returns the tabid, integer type
 */
function getSalesEntityType($crmid) {
	global $log;
	$log->debug("Entering getSalesEntityType(" . $crmid . ") method ...");
	$log->info("in getSalesEntityType " . $crmid);
	global $adb;
	$sql = "select setype from vtiger_crmentity where crmid=?";
	$result = $adb->pquery($sql, array($crmid));
	$parent_module = $adb->query_result($result, 0, "setype");
	$log->debug("Exiting getSalesEntityType method ...");
	return $parent_module;
}

/**
 * Function to get the AccountName when a vtiger_account id is given
 * Takes the input as $acount_id - vtiger_account id
 * returns the vtiger_account name in string format.
 */
function getAccountName($account_id) {
	global $log;
	$log->debug("Entering getAccountName(" . $account_id . ") method ...");
	$log->info("in getAccountName " . $account_id);

	global $adb;
	if ($account_id != '') {
		$sql = "select accountname from vtiger_account where accountid=?";
		$result = $adb->pquery($sql, array($account_id));
		$accountname = $adb->query_result($result, 0, "accountname");
	}
	$log->debug("Exiting getAccountName method ...");
	return $accountname;
}

/**
 * Function to get the ProductName when a product id is given
 * Takes the input as $product_id - product id
 * returns the product name in string format.
 */
function getProductName($product_id) {
	global $log;
	$log->debug("Entering getProductName(" . $product_id . ") method ...");

	$log->info("in getproductname " . $product_id);

	global $adb;
	$sql = "select productname from vtiger_products where productid=?";
	$result = $adb->pquery($sql, array($product_id));
	$productname = $adb->query_result($result, 0, "productname");
	$log->debug("Exiting getProductName method ...");
	return $productname;
}

/**
 * Function to get the Potentail Name when a vtiger_potential id is given
 * Takes the input as $potential_id - vtiger_potential id
 * returns the vtiger_potential name in string format.
 */
function getPotentialName($potential_id) {
	global $log;
	$log->debug("Entering getPotentialName(" . $potential_id . ") method ...");
	$log->info("in getPotentialName " . $potential_id);

	global $adb;
	$potentialname = '';
	if ($potential_id != '') {
		$sql = "select potentialname from vtiger_potential where potentialid=?";
		$result = $adb->pquery($sql, array($potential_id));
		$potentialname = $adb->query_result($result, 0, "potentialname");
	}
	$log->debug("Exiting getPotentialName method ...");
	return $potentialname;
}

/**
 * Function to get the Contact Name when a contact id is given
 * Takes the input as $contact_id - contact id
 * returns the Contact Name in string format.
 */
function getContactName($contact_id) {
	global $log;
	$log->debug("Entering getContactName(" . $contact_id . ") method ...");
	$log->info("in getContactName " . $contact_id);

	global $adb, $current_user;
	$contact_name = '';
	if ($contact_id != '') {
		$sql = "select * from vtiger_contactdetails where contactid=?";
		$result = $adb->pquery($sql, array($contact_id));
		$firstname = $adb->query_result($result, 0, "firstname");
		$lastname = $adb->query_result($result, 0, "lastname");
		$contact_name = $lastname;
		// Asha: Check added for ticket 4788
		if (getFieldVisibilityPermission("Contacts", $current_user->id, 'firstname') == '0') {
			$contact_name .= ' ' . $firstname;
		}
	}
	$log->debug("Exiting getContactName method ...");
	return $contact_name;
}

/**
 * Function to get the Contact Name when a contact id is given
 * Takes the input as $contact_id - contact id
 * returns the Contact Name in string format.
 */
function getLeadName($lead_id) {
	global $log;
	$log->debug("Entering getLeadName(" . $lead_id . ") method ...");
	$log->info("in getLeadName " . $lead_id);

	global $adb, $current_user;
	$lead_name = '';
	if ($lead_id != '') {
		$sql = "select * from vtiger_leaddetails where leadid=?";
		$result = $adb->pquery($sql, array($lead_id));
		$firstname = $adb->query_result($result, 0, "firstname");
		$lastname = $adb->query_result($result, 0, "lastname");
		$lead_name = $lastname;
		// Asha: Check added for ticket 4788
		if (getFieldVisibilityPermission("Leads", $current_user->id, 'firstname') == '0') {
			$lead_name .= ' ' . $firstname;
		}
	}
	$log->debug("Exiting getLeadName method ...");
	return $lead_name;
}

/**
 * Function to get the Full Name of a Contact/Lead when a query result and the row count are given
 * Takes the input as $result - Query Result, $row_count - Count of the Row, $module - module name
 * returns the Contact Name in string format.
 */
function getFullNameFromQResult($result, $row_count, $module) {
	global $log, $adb, $current_user;
	$log->info("In getFullNameFromQResult(" . print_r($result, true) . " - " . $row_count . "-" . $module . ") method ...");

	$rowdata = $adb->query_result_rowdata($result, $row_count);
	$entity_field_info = getEntityFieldNames($module);
	$fieldsName = $entity_field_info['fieldname'];
	$name = '';
	if ($rowdata != '' && count($rowdata) > 0) {
		$name = getEntityFieldNameDisplay($module, $fieldsName, $rowdata );
	}
	$name = textlength_check($name);
	return $name;
}

function getFullNameFromArray($module, $fieldValues) {
	$entityInfo = getEntityFieldNames($module);
	$fieldsName = $entityInfo['fieldname'];
	$displayName = getEntityFieldNameDisplay($module, $fieldsName, $fieldValues);
	return $displayName;
}

/**
 * Function to get the Campaign Name when a campaign id is given
 * Takes the input as $campaign_id - campaign id
 * returns the Campaign Name in string format.
 */
function getCampaignName($campaign_id) {
	global $log;
	$log->debug("Entering getCampaignName(" . $campaign_id . ") method ...");
	$log->info("in getCampaignName " . $campaign_id);

	global $adb;
	$sql = "select * from vtiger_campaign where campaignid=?";
	$result = $adb->pquery($sql, array($campaign_id));
	$campaign_name = $adb->query_result($result, 0, "campaignname");
	$log->debug("Exiting getCampaignName method ...");
	return $campaign_name;
}

/**
 * Function to get the Vendor Name when a vtiger_vendor id is given
 * Takes the input as $vendor_id - vtiger_vendor id
 * returns the Vendor Name in string format.
 */
function getVendorName($vendor_id) {
	global $log;
	$log->debug("Entering getVendorName(" . $vendor_id . ") method ...");
	$log->info("in getVendorName " . $vendor_id);
	global $adb;
	$sql = "select * from vtiger_vendor where vendorid=?";
	$result = $adb->pquery($sql, array($vendor_id));
	$vendor_name = $adb->query_result($result, 0, "vendorname");
	$log->debug("Exiting getVendorName method ...");
	return $vendor_name;
}

/**
 * Function to get the Quote Name when a vtiger_vendor id is given
 * Takes the input as $quote_id - quote id
 * returns the Quote Name in string format.
 */
function getQuoteName($quote_id) {
	global $log;
	$log->debug("Entering getQuoteName(" . $quote_id . ") method ...");
	$log->info("in getQuoteName " . $quote_id);
	global $adb;
	if ($quote_id != NULL && $quote_id != '') {
		$sql = "select * from vtiger_quotes where quoteid=?";
		$result = $adb->pquery($sql, array($quote_id));
		$quote_name = $adb->query_result($result, 0, "subject");
	} else {
		$log->debug("Quote Id is empty.");
		$quote_name = '';
	}
	$log->debug("Exiting getQuoteName method ...");
	return $quote_name;
}

/**
 * Function to get the PriceBook Name when a vtiger_pricebook id is given
 * Takes the input as $pricebook_id - vtiger_pricebook id
 * returns the PriceBook Name in string format.
 */
function getPriceBookName($pricebookid) {
	global $log;
	$log->debug("Entering getPriceBookName(" . $pricebookid . ") method ...");
	$log->info("in getPriceBookName " . $pricebookid);
	global $adb;
	$sql = "select * from vtiger_pricebook where pricebookid=?";
	$result = $adb->pquery($sql, array($pricebookid));
	$pricebook_name = $adb->query_result($result, 0, "bookname");
	$log->debug("Exiting getPriceBookName method ...");
	return $pricebook_name;
}

/** This Function returns the  Purchase Order Name.
 * The following is the input parameter for the function
 *  $po_id --> Purchase Order Id, Type:Integer
 */
function getPoName($po_id) {
	global $log;
	$log->debug("Entering getPoName(" . $po_id . ") method ...");
	$log->info("in getPoName " . $po_id);
	global $adb;
	$sql = "select * from vtiger_purchaseorder where purchaseorderid=?";
	$result = $adb->pquery($sql, array($po_id));
	$po_name = $adb->query_result($result, 0, "subject");
	$log->debug("Exiting getPoName method ...");
	return $po_name;
}

/**
 * Function to get the Sales Order Name when a vtiger_salesorder id is given
 * Takes the input as $salesorder_id - vtiger_salesorder id
 * returns the Salesorder Name in string format.
 */
function getSoName($so_id) {
	global $log;
	$log->debug("Entering getSoName(" . $so_id . ") method ...");
	$log->info("in getSoName " . $so_id);
	global $adb;
	$sql = "select * from vtiger_salesorder where salesorderid=?";
	$result = $adb->pquery($sql, array($so_id));
	$so_name = $adb->query_result($result, 0, "subject");
	$log->debug("Exiting getSoName method ...");
	return $so_name;
}

/**
 * Function to get the Group Information for a given groupid
 * Takes the input $id - group id and $module - module name
 * returns the group information in an array format.
 */
function getGroupName($groupid) {
	global $adb, $log;
	$log->debug("Entering getGroupName(" . $groupid . ") method ...");
	$group_info = Array();
	$log->info("in getGroupName, entityid is " . $groupid);
	if ($groupid != '') {
		$sql = "select groupname,groupid from vtiger_groups where groupid = ?";
		$result = $adb->pquery($sql, array($groupid));
		$group_info[] = decode_html($adb->query_result($result, 0, "groupname"));
		$group_info[] = $adb->query_result($result, 0, "groupid");
	}
	$log->debug("Exiting getGroupName method ...");
	return $group_info;
}

/**
 * Get the username by giving the user id.   This method expects the user id
 */
function getUserName($userid) {
	global $adb, $log;
	$log->debug("Entering getUserName(" . $userid . ") method ...");
	$log->info("in getUserName " . $userid);

	if ($userid != '') {
		$sql = "select user_name from vtiger_users where id=?";
		$result = $adb->pquery($sql, array($userid));
		$user_name = $adb->query_result($result, 0, "user_name");
	}
	$log->debug("Exiting getUserName method ...");
	return $user_name;
}

/**
 * Get the user full name by giving the user id.   This method expects the user id
 * DG 30 Aug 2006
 */
function getUserFullName($userid) {
	global $log;
	$log->debug("Entering getUserFullName(" . $userid . ") method ...");
	$log->info("in getUserFullName " . $userid);
	global $adb;
	if ($userid != '') {
		$displayValueArray = getEntityName('Users', $userid);
		if (!empty($displayValueArray)) {
			foreach ($displayValueArray as $key => $value) {
				$user_name = $value;
			}
		}
	}
	$log->debug("Exiting getUserFullName method ...");
	return $user_name;
}

/** Fucntion to get related To name with id */
function getParentName($parent_id) {
	global $adb;
	if (empty($parent_id) || $parent_id == 0) {
		return "";
	}

	$seType = getSalesEntityType($parent_id);
	$entityNames = getEntityName($seType, $parent_id);
	return $entityNames[$parent_id];
}

/**
 * Creates and returns database query. To be used for search and other text links.   This method expects the module object.
 * param $focus - the module object contains the column vtiger_fields
 */
function getURLstring($focus) {
	global $log;
	$log->debug("Entering getURLstring(" . get_class($focus) . ") method ...");
	$qry = "";
	foreach ($focus->column_fields as $fldname => $val) {
		if (isset($_REQUEST[$fldname]) && $_REQUEST[$fldname] != '') {
			if ($qry == '')
				$qry = "&" . $fldname . "=" . vtlib_purify($_REQUEST[$fldname]);
			else
				$qry .="&" . $fldname . "=" . vtlib_purify($_REQUEST[$fldname]);
		}
	}
	if (isset($_REQUEST['current_user_only']) && $_REQUEST['current_user_only'] != '') {
		$qry .="&current_user_only=" . vtlib_purify($_REQUEST['current_user_only']);
	}
	if (isset($_REQUEST['advanced']) && $_REQUEST['advanced'] == 'true') {
		$qry .="&advanced=true";
	}

	if ($qry != '') {
		$qry .="&query=true";
	}
	$log->debug("Exiting getURLstring method ...");
	return $qry;
}

/**
 * This function returns the date in user specified format.
 * limitation is that mm-dd-yyyy and dd-mm-yyyy will be considered same by this API.
 * As in the date value is on mm-dd-yyyy and user date format is dd-mm-yyyy then the mm-dd-yyyy
 * value will be return as the API will be considered as considered as in same format.
 * this due to the fact that this API tries to consider the where given date is in user date
 * format. we need a better gauge for this case.
 * @global Users $current_user
 * @param Date $cur_date_val the date which should a changed to user date format.
 * @return Date
 */
function getValidDisplayDate($cur_date_val) {
	global $current_user;
	$dat_fmt = $current_user->date_format;
	if ($dat_fmt == '') {
		$dat_fmt = 'dd-mm-yyyy';
	}
	$date_value = explode(' ', $cur_date_val);
	list($y, $m, $d) = explode('-', $date_value[0]);
	list($fy, $fm, $fd) = explode('-', $dat_fmt);
	if ((strlen($fy) == 4 && strlen($y) == 4) || (strlen($fd) == 4 && strlen($d) == 4)) {
		return "$y-$m-$d";
	}
	$date = new DateTimeField($cur_date_val);
	return $date->getDisplayDate();
}

function getNewDisplayDate() {
	global $log, $current_user;
	$log->debug("Entering getNewDisplayDate() method ...");
	$log->info("in getNewDisplayDate ");

	$date = new DateTimeField(null);
	$log->debug("Exiting getNewDisplayDate method ...");
	return $date->getDisplayDate($current_user);
}

/** This function returns the default vtiger_currency information.
 * Takes no param, return type array.
 */
function getDisplayCurrency() {
	global $log;
	global $adb;
	$log->debug("Entering getDisplayCurrency() method ...");
	$curr_array = Array();
	$sql1 = "select * from vtiger_currency_info where currency_status=? and deleted=0";
	$result = $adb->pquery($sql1, array('Active'));
	$num_rows = $adb->num_rows($result);
	for ($i = 0; $i < $num_rows; $i++) {
		$curr_id = $adb->query_result($result, $i, "id");
		$curr_name = $adb->query_result($result, $i, "currency_name");
		$curr_symbol = $adb->query_result($result, $i, "currency_symbol");
		$curr_array[$curr_id] = $curr_name . ' : ' . $curr_symbol;
	}
	$log->debug("Exiting getDisplayCurrency method ...");
	return $curr_array;
}

/** This function returns the amount converted to dollar.
 * param $amount - amount to be converted.
 * param $crate - conversion rate.
 */
function convertToDollar($amount, $crate) {
	global $log;
	$log->debug("Entering convertToDollar(" . $amount . "," . $crate . ") method ...");
	$log->debug("Exiting convertToDollar method ...");
	return $amount / $crate;
}

/** This function returns the amount converted from dollar.
 * param $amount - amount to be converted.
 * param $crate - conversion rate.
 */
function convertFromDollar($amount, $crate) {
	global $log;
	$log->debug("Entering convertFromDollar(" . $amount . "," . $crate . ") method ...");
	$log->debug("Exiting convertFromDollar method ...");
	return round($amount * $crate, 2);
}

/** This function returns the amount converted from master currency.
 * param $amount - amount to be converted.
 * param $crate - conversion rate.
 */
function convertFromMasterCurrency($amount, $crate) {
	global $log;
	$log->debug("Entering convertFromDollar(" . $amount . "," . $crate . ") method ...");
	$log->debug("Exiting convertFromDollar method ...");
	return $amount * $crate;
}

/** This function returns the conversion rate and vtiger_currency symbol
 * in array format for a given id.
 * param $id - vtiger_currency id.
 */
function getCurrencySymbolandCRate($id) {
	global $log;
	$log->debug("Entering getCurrencySymbolandCRate(" . $id . ") method ...");

	// To initialize the currency information in cache
	getCurrencyName($id);

	$currencyinfo = VTCacheUtils::lookupCurrencyInfo($id);

	$rate_symbol['rate'] = $currencyinfo['rate'];
	$rate_symbol['symbol'] = $currencyinfo['symbol'];

	$log->debug("Exiting getCurrencySymbolandCRate method ...");
	return $rate_symbol;
}

/** This function returns the terms and condition from the database.
 * Takes no param and the return type is text.
 */
function getTermsandConditions() {
	global $log;
	$log->debug("Entering getTermsandConditions() method ...");
	global $adb;
	$sql1 = "select * from vtiger_inventory_tandc";
	$result = $adb->pquery($sql1, array());
	$tandc = $adb->query_result($result, 0, "tandc");
	$log->debug("Exiting getTermsandConditions method ...");
	return $tandc;
}

/**
 * Create select options in a dropdown list.  To be used inside
 *  a reminder select statement in a vtiger_activity form.
 * param $start - start value
 * param $end - end value
 * param $fldname - vtiger_field name
 * param $selvalue - selected value
 */
function getReminderSelectOption($start, $end, $fldname, $selvalue = '') {
	global $log;
	$log->debug("Entering getReminderSelectOption(" . $start . "," . $end . "," . $fldname . "," . $selvalue = '' . ") method ...");
	global $mod_strings;
	global $app_strings;

	$def_sel = "";
	$OPTION_FLD = "<SELECT name=" . $fldname . ">";
	for ($i = $start; $i <= $end; $i++) {
		if ($i == $selvalue)
			$def_sel = "SELECTED";
		$OPTION_FLD .= "<OPTION VALUE=" . $i . " " . $def_sel . ">" . $i . "</OPTION>\n";
		$def_sel = "";
	}
	$OPTION_FLD .="</SELECT>";
	$log->debug("Exiting getReminderSelectOption method ...");
	return $OPTION_FLD;
}

/** This function returns the List price of a given product in a given price book.
 * param $productid - product id.
 * param $pbid - vtiger_pricebook id.
 */
function getListPrice($productid, $pbid) {
	global $log;
	$log->debug("Entering getListPrice(" . $productid . "," . $pbid . ") method ...");
	$log->info("in getListPrice productid " . $productid);

	global $adb;
	$query = "select listprice from vtiger_pricebookproductrel where pricebookid=? and productid=?";
	$result = $adb->pquery($query, array($pbid, $productid));
	$lp = $adb->query_result($result, 0, 'listprice');
	$log->debug("Exiting getListPrice method ...");
	return $lp;
}

/** This function returns a string with removed new line character, single quote, and back slash double quoute.
 * param $str - string to be converted.
 */
function br2nl($str) {
	global $log;
	$log->debug("Entering br2nl(" . $str . ") method ...");
	$str = preg_replace("/(\r\n)/", "\\r\\n", $str);
	$str = preg_replace("/'/", " ", $str);
	$str = preg_replace("/\"/", " ", $str);
	$log->debug("Exiting br2nl method ...");
	return $str;
}

/** This function returns a text, which escapes the html encode for link tag/ a href tag
 * param $text - string/text
 */
function make_clickable($text) {
	global $log;
	$log->debug("Entering make_clickable(" . $text . ") method ...");
	$text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1&#058;", $text);
	// pad it with a space so we can match things at the start of the 1st line.
	$ret = ' ' . $text;

	// matches an "xxxx://yyyy" URL at the start of a line, or after a space.
	// xxxx can only be alpha characters.
	// yyyy is anything up to the first space, newline, comma, double quote or <
	$ret = preg_replace("#(^|[\n ])([\w]+?://.*?[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);

	// matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing
	// Must contain at least 2 dots. xxxx contains either alphanum, or "-"
	// zzzz is optional.. will contain everything up to the first space, newline,
	// comma, double quote or <.
	$ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:/[^ \"\t\n\r<]*)?)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);

	// matches an email@domain type address at the start of a line, or after a space.
	// Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
	$ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);

	// Remove our padding..
	$ret = substr($ret, 1);

	//remove comma, fullstop at the end of url
	$ret = preg_replace("#,\"|\.\"|\)\"|\)\.\"|\.\)\"#", "\"", $ret);

	$log->debug("Exiting make_clickable method ...");
	return($ret);
}

/**
 * This function returns the vtiger_blocks and its related information for given module.
 * Input Parameter are $module - module name, $disp_view = display view (edit,detail or create),$mode - edit, $col_fields - * column vtiger_fields/
 * This function returns an array
 */
function getBlocks($module, $disp_view, $mode, $col_fields = '', $info_type = '') {
	global $log;
	$log->debug("Entering getBlocks(" . $module . "," . $disp_view . "," . $mode . "," . $col_fields . "," . $info_type . ") method ...");
	global $adb, $current_user;
	global $mod_strings;
	$tabid = getTabid($module);
	$block_detail = Array();
	$getBlockinfo = "";
	$query = "select blockid,blocklabel,show_title,display_status from vtiger_blocks where tabid=? and $disp_view=0 and visible = 0 order by sequence";
	$result = $adb->pquery($query, array($tabid));
	$noofrows = $adb->num_rows($result);
	$prev_header = "";
	$blockid_list = array();
	for ($i = 0; $i < $noofrows; $i++) {
		$blockid = $adb->query_result($result, $i, "blockid");
		array_push($blockid_list, $blockid);
		$block_label[$blockid] = $adb->query_result($result, $i, "blocklabel");

		$sLabelVal = getTranslatedString($block_label[$blockid], $module);
		$aBlockStatus[$sLabelVal] = $adb->query_result($result, $i, "display_status");
	}
	if ($mode == 'edit') {
		$display_type_check = 'vtiger_field.displaytype = 1';
	} elseif ($mode == 'mass_edit') {
		$display_type_check = 'vtiger_field.displaytype = 1 AND vtiger_field.masseditable NOT IN (0,2)';
	} else {
		$display_type_check = 'vtiger_field.displaytype in (1,4)';
	}

	/* if($non_mass_edit_fields!='' && sizeof($non_mass_edit_fields)!=0){
	  $mass_edit_query = "AND vtiger_field.fieldname NOT IN (". generateQuestionMarks($non_mass_edit_fields) .")";
	  } */

	//retreive the vtiger_profileList from database
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	if ($disp_view == "detail_view") {
		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == "Users" || $module == "Emails") {
			$sql = "SELECT vtiger_field.*, '0' as readonly FROM vtiger_field WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND vtiger_field.displaytype IN (1,2,4) and vtiger_field.presence in (0,2) ORDER BY block,sequence";
			$params = array($tabid, $blockid_list);
		} else {
			$profileList = getCurrentUserProfileList();
			$sql = "SELECT vtiger_field.*, vtiger_profile2field.readonly FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND vtiger_field.displaytype IN (1,2,4) and vtiger_field.presence in (0,2) AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ") GROUP BY vtiger_field.fieldid ORDER BY block,sequence";
			$params = array($tabid, $blockid_list, $profileList);
			//Postgres 8 fixes
			if ($adb->dbType == "pgsql")
				$sql = fixPostgresQuery($sql, $log, 0);
		}
		$result = $adb->pquery($sql, $params);

		// Added to unset the previous record's related listview session values
		if (isset($_SESSION['rlvs']))
			unset($_SESSION['rlvs']);

		$getBlockInfo = getDetailBlockInformation($module, $result, $col_fields, $tabid, $block_label);
	}
	else {
		if ($info_type != '') {
			if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == 'Users' || $module == "Emails") {
				$sql = "SELECT vtiger_field.* FROM vtiger_field WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND $display_type_check AND info_type = ? and vtiger_field.presence in (0,2) ORDER BY block,sequence";
				$params = array($tabid, $blockid_list, $info_type);
			} else {
				$profileList = getCurrentUserProfileList();
				$sql = "SELECT vtiger_field.* FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid  WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND $display_type_check AND info_type = ? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly = 0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ") and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid ORDER BY block,sequence";
				$params = array($tabid, $blockid_list, $info_type, $profileList);
				//Postgres 8 fixes
				if ($adb->dbType == "pgsql")
					$sql = fixPostgresQuery($sql, $log, 0);
			}
		}
		else {
			if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == 'Users' || $module == "Emails") {
				$sql = "SELECT vtiger_field.* FROM vtiger_field WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND $display_type_check  and vtiger_field.presence in (0,2) ORDER BY block,sequence";
				$params = array($tabid, $blockid_list);
			} else {
				$profileList = getCurrentUserProfileList();
				$sql = "SELECT vtiger_field.* FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid  WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND $display_type_check AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly = 0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ") and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid ORDER BY block,sequence";
				$params = array($tabid, $blockid_list, $profileList);
				//Postgres 8 fixes
				if ($adb->dbType == "pgsql")
					$sql = fixPostgresQuery($sql, $log, 0);
			}
		}
		$result = $adb->pquery($sql, $params);
		$getBlockInfo = getBlockInformation($module, $result, $col_fields, $tabid, $block_label, $mode);
	}
	$log->debug("Exiting getBlocks method ...");
	if (count($getBlockInfo) > 0) {
		foreach ($getBlockInfo as $label => $contents) {
			if (empty($getBlockInfo[$label])) {
				unset($getBlockInfo[$label]);
			}
		}
	}
	$_SESSION['BLOCKINITIALSTATUS'] = $aBlockStatus;
	return $getBlockInfo;
}

/**
 * This function is used to get the display type.
 * Takes the input parameter as $mode - edit  (mostly)
 * This returns string type value
 */
function getView($mode) {
	global $log;
	$log->debug("Entering getView(" . $mode . ") method ...");
	if ($mode == "edit")
		$disp_view = "edit_view";
	else
		$disp_view = "create_view";
	$log->debug("Exiting getView method ...");
	return $disp_view;
}

/**
 * This function is used to get the blockid of the customblock for a given module.
 * Takes the input parameter as $tabid - module tabid and $label - custom label
 * This returns string type value
 */
function getBlockId($tabid, $label) {
	global $log;
	$log->debug("Entering getBlockId(" . $tabid . "," . $label . ") method ...");
	global $adb;
	$blockid = '';
	$query = "select blockid from vtiger_blocks where tabid=? and blocklabel = ?";
	$result = $adb->pquery($query, array($tabid, $label));
	$noofrows = $adb->num_rows($result);
	if ($noofrows == 1) {
		$blockid = $adb->query_result($result, 0, "blockid");
	}
	$log->debug("Exiting getBlockId method ...");
	return $blockid;
}

/**
 * This function is used to get the Parent and Child vtiger_tab relation array.
 * Takes no parameter and get the data from parent_tabdata.php and vtiger_tabdata.php
 * This returns array type value
 */
function getHeaderArray() {
	global $log;
	$log->debug("Entering getHeaderArray() method ...");
	global $adb;
	global $current_user;
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	include('parent_tabdata.php');
	include('tabdata.php');
	$noofrows = count($parent_tab_info_array);
	foreach ($parent_tab_info_array as $parid => $parval) {
		$subtabs = Array();
		$tablist = $parent_child_tab_rel_array[$parid];
		$noofsubtabs = count($tablist);

		foreach ($tablist as $childTabId) {
			$module = array_search($childTabId, $tab_info_array);

			if ($is_admin) {
				$subtabs[] = $module;
			} elseif ($profileGlobalPermission[2] == 0 || $profileGlobalPermission[1] == 0 || $profileTabsPermission[$childTabId] == 0) {
				$subtabs[] = $module;
			}
		}

		$parenttab = getParentTabName($parid);
		if ($parenttab == 'Settings' && $is_admin) {
			$subtabs[] = 'Settings';
		}

		if ($parenttab != 'Settings' || ($parenttab == 'Settings' && $is_admin)) {
			if (!empty($subtabs))
				$relatedtabs[$parenttab] = $subtabs;
		}
	}
	$log->debug("Exiting getHeaderArray method ...");
	return $relatedtabs;
}

/**
 * This function is used to get the Parent Tab name for a given parent vtiger_tab id.
 * Takes the input parameter as $parenttabid - Parent vtiger_tab id
 * This returns value string type
 */
function getParentTabName($parenttabid) {
	global $log;
	$log->debug("Entering getParentTabName(" . $parenttabid . ") method ...");
	global $adb;
	if (file_exists('parent_tabdata.php') && (filesize('parent_tabdata.php') != 0)) {
		include('parent_tabdata.php');
		$parent_tabname = $parent_tab_info_array[$parenttabid];
	} else {
		$sql = "select parenttab_label from vtiger_parenttab where parenttabid=?";
		$result = $adb->pquery($sql, array($parenttabid));
		$parent_tabname = $adb->query_result($result, 0, "parenttab_label");
	}
	$log->debug("Exiting getParentTabName method ...");
	return $parent_tabname;
}

/**
 * This function is used to get the Parent Tab name for a given module.
 * Takes the input parameter as $module - module name
 * This returns value string type
 */
function getParentTabFromModule($module) {
	global $log;
	$log->debug("Entering getParentTabFromModule(" . $module . ") method ...");
	global $adb;
	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0) && file_exists('parent_tabdata.php') && (filesize('parent_tabdata.php') != 0)) {
		include('tabdata.php');
		include('parent_tabdata.php');
		$tabid = $tab_info_array[$module];
		foreach ($parent_child_tab_rel_array as $parid => $childArr) {
			if (in_array($tabid, $childArr)) {
				$parent_tabname = $parent_tab_info_array[$parid];
				break;
			}
		}
		$log->debug("Exiting getParentTabFromModule method ...");
		return $parent_tabname;
	} else {
		$sql = "select vtiger_parenttab.* from vtiger_parenttab inner join vtiger_parenttabrel on vtiger_parenttabrel.parenttabid=vtiger_parenttab.parenttabid inner join vtiger_tab on vtiger_tab.tabid=vtiger_parenttabrel.tabid where vtiger_tab.name=?";
		$result = $adb->pquery($sql, array($module));
		$tab = $adb->query_result($result, 0, "parenttab_label");
		$log->debug("Exiting getParentTabFromModule method ...");
		return $tab;
	}
}

/**
 * This function is used to get the Parent Tab name for a given module.
 * Takes no parameter but gets the vtiger_parenttab value from form request
 * This returns value string type
 */
function getParentTab() {
	global $log, $default_charset;
	$log->debug("Entering getParentTab() method ...");
	if (!empty($_REQUEST['parenttab'])) {
		$log->debug("Exiting getParentTab method ...");
		if (checkParentTabExists($_REQUEST['parenttab'])) {
			return vtlib_purify($_REQUEST['parenttab']);
		} else {
			return getParentTabFromModule($_REQUEST['module']);
		}
	} else {
		$log->debug("Exiting getParentTab method ...");
		return getParentTabFromModule($_REQUEST['module']);
	}
}

function checkParentTabExists($parenttab) {
	global $adb;

	if (file_exists('parent_tabdata.php') && (filesize('parent_tabdata.php') != 0)) {
		include('parent_tabdata.php');
		if (in_array($parenttab, $parent_tab_info_array))
			return true;
		else
			return false;
	} else {

		$result = "select 1 from vtiger_parenttab where parenttab_label = ?";
		$noofrows = $adb->num_rows($result);
		if ($noofrows > 0)
			return true;
		else
			return false;
	}
}

/**
 * This function is used to get the days in between the current time and the modified time of an entity .
 * Takes the input parameter as $id - crmid  it will calculate the number of days in between the
 * the current time and the modified time from the vtiger_crmentity vtiger_table and return the result as a string.
 * The return format is updated <No of Days> day ago <(date when updated)>
 */
function updateInfo($id) {
	global $log;
	$log->debug("Entering updateInfo(" . $id . ") method ...");

	global $adb;
	global $app_strings;
	$query = 'SELECT modifiedtime, modifiedby FROM vtiger_crmentity WHERE crmid = ?';
	$result = $adb->pquery($query, array($id));
	$modifiedtime = $adb->query_result($result, 0, 'modifiedtime');
	$modifiedby_id = $adb->query_result($result, 0, 'modifiedby');
	$modifiedby = $app_strings['LBL_BY'] . getOwnerName($modifiedby_id);
	$date = new DateTimeField($modifiedtime);
	$modifiedtime = DateTimeField::convertToDBFormat($date->getDisplayDate());
	$current_time = date('Y-m-d H:i:s');
	$values = explode(' ', $modifiedtime);
	$date_info = explode('-', $values[0]);
	$time_info = explode(':', $values[1]);
	$date = $date_info[2] . ' ' . $app_strings[date("M", mktime(0, 0, 0, $date_info[1], $date_info[2], $date_info[0]))] . ' ' . $date_info[0];
	$time_modified = strtotime($modifiedtime);
	$time_now = strtotime($current_time);
	$days_diff = (int) (($time_now - $time_modified) / (60 * 60 * 24));
	if ($days_diff == 0)
		$update_info = $app_strings['LBL_UPDATED_TODAY'] . " (" . $date . ")" . ' ' . $modifiedby;
	elseif ($days_diff == 1)
		$update_info = $app_strings['LBL_UPDATED'] . " " . $days_diff . " " . $app_strings['LBL_DAY_AGO'] . " (" . $date . ")" . ' ' . $modifiedby;
	else
		$update_info = $app_strings['LBL_UPDATED'] . " " . $days_diff . " " . $app_strings['LBL_DAYS_AGO'] . " (" . $date . ")" . ' ' . $modifiedby;

	$log->debug("Exiting updateInfo method ...");
	return $update_info;
}

/**
 * This function is used to get the Product Images for the given Product  .
 * It accepts the product id as argument and returns the Images with the script for
 * rotating the product Images
 */
function getProductImages($id) {
	global $log;
	$log->debug("Entering getProductImages(" . $id . ") method ...");
	global $adb;
	$image_lists = array();
	$script_images = array();
	$script = '<script>var ProductImages = new Array(';
	$i = 0;
	$query = 'select imagename from vtiger_products where productid=?';
	$result = $adb->pquery($query, array($id));
	$imagename = $adb->query_result($result, 0, 'imagename');
	$image_lists = explode('###', $imagename);
	for ($i = 0; $i < count($image_lists); $i++) {
		$script_images[] = '"' . $image_lists[$i] . '"';
	}
	$script .=implode(',', $script_images) . ');</script>';
	if ($imagename != '') {
		$log->debug("Exiting getProductImages method ...");
		return $script;
	}
}

/**
 * This function is used to save the Images .
 * It acceps the File lists,modulename,id and the mode as arguments
 * It returns the array details of the upload
 */
function SaveImage($_FILES, $module, $id, $mode) {
	global $log, $root_directory;
	$log->debug("Entering SaveImage(" . $_FILES . "," . $module . "," . $id . "," . $mode . ") method ...");
	global $adb;
	$uploaddir = $root_directory . "test/" . $module . "/"; //set this to which location you need to give the contact image
	$log->info("The Location to Save the Contact Image is " . $uploaddir);
	$file_path_name = $_FILES['imagename']['name'];
	if (isset($_REQUEST['imagename_hidden'])) {
		$file_name = vtlib_purify($_REQUEST['imagename_hidden']);
	} else {
		//allowed filename like UTF-8 Character
		$file_name = ltrim(basename(" " . $file_path_name)); // basename($file_path_name);
	}
	$image_error = "false";
	$saveimage = "true";
	if ($file_name != "") {

		$log->debug("Contact Image is given for uploading");
		$image_name_val = file_exist_fn($file_name, 0);

		$encode_field_values = "";
		$errormessage = "";

		$move_upload_status = move_uploaded_file($_FILES["imagename"]["tmp_name"], $uploaddir . $image_name_val);
		$image_error = "false";

		//if there is an error in the uploading of image

		$filetype = $_FILES['imagename']['type'];
		$filesize = $_FILES['imagename']['size'];

		$filetype_array = explode("/", $filetype);

		$file_type_val_image = strtolower($filetype_array[0]);
		$file_type_val = strtolower($filetype_array[1]);
		$log->info("The File type of the Contact Image is :: " . $file_type_val);
		//checking the uploaded image is if an image type or not
		if (!$move_upload_status) { //if any error during file uploading
			$log->debug("Error is present in uploading Contact Image.");
			$errorCode = $_FILES['imagename']['error'];
			if ($errorCode == 4) {
				$errorcode = "no-image";
				$saveimage = "false";
				$image_error = "true";
			} else if ($errorCode == 2) {
				$errormessage = 2;
				$saveimage = "false";
				$image_error = "true";
			} else if ($errorCode == 3) {
				$errormessage = 3;
				$saveimage = "false";
				$image_error = "true";
			}
		} else {
			$log->debug("Successfully uploaded the Contact Image.");
			if ($filesize != 0) {
				if (($file_type_val == "jpeg" ) || ($file_type_val == "png") || ($file_type_val == "jpg" ) || ($file_type_val == "pjpeg" ) || ($file_type_val == "x-png") || ($file_type_val == "gif")) { //Checking whether the file is an image or not
					$saveimage = "true";
					$image_error = "false";
				} else {
					$savelogo = "false";
					$image_error = "true";
					$errormessage = "image";
				}
			} else {
				$savelogo = "false";
				$image_error = "true";
				$errormessage = "invalid";
			}
		}
	} else { //if image is not given
		$log->debug("Contact Image is not given for uploading.");
		if ($mode == "edit" && $image_error == "false") {
			if ($module = 'contact')
				$image_name_val = getContactImageName($id);
			elseif ($module = 'user')
				$image_name_val = getUserImageName($id);
			$saveimage = "true";
		}
		else {
			$image_name_val = "";
		}
	}
	$return_value = array('imagename' => $image_name_val,
		'imageerror' => $image_error,
		'errormessage' => $errormessage,
		'saveimage' => $saveimage,
		'mode' => $mode);
	$log->debug("Exiting SaveImage method ...");
	return $return_value;
}

/**
 * This function is used to generate file name if more than one image with same name is added to a given Product.
 * Param $filename - product file name
 * Param $exist - number time the file name is repeated.
 */
function file_exist_fn($filename, $exist) {
	global $log;
	$log->debug("Entering file_exist_fn(" . $filename . "," . $exist . ") method ...");
	global $uploaddir;

	if (!isset($exist)) {
		$exist = 0;
	}
	$filename_path = $uploaddir . $filename;
	if (file_exists($filename_path)) { //Checking if the file name already exists in the directory
		if ($exist != 0) {
			$previous = $exist - 1;
			$next = $exist + 1;
			$explode_name = explode("_", $filename);
			$implode_array = array();
			for ($j = 0; $j < count($explode_name); $j++) {
				if ($j != 0) {
					$implode_array[] = $explode_name[$j];
				}
			}
			$implode_name = implode("_", $implode_array);
			$test_name = $implode_name;
		} else {
			$implode_name = $filename;
		}
		$exist++;
		$filename_val = $exist . "_" . $implode_name;
		$testfilename = file_exist_fn($filename_val, $exist);
		if ($testfilename != "") {
			$log->debug("Exiting file_exist_fn method ...");
			return $testfilename;
		}
	} else {
		$log->debug("Exiting file_exist_fn method ...");
		return $filename;
	}
}

/**
 * This function is used get the User Count.
 * It returns the array which has the total vtiger_users ,admin vtiger_users,and the non admin vtiger_users
 */
function UserCount() {
	global $log;
	$log->debug("Entering UserCount() method ...");
	global $adb;
	$result = $adb->pquery("select * from vtiger_users where deleted =0", array());
	$user_count = $adb->num_rows($result);
	$result = $adb->pquery("select * from vtiger_users where deleted =0 AND is_admin != 'on'", array());
	$nonadmin_count = $adb->num_rows($result);
	$admin_count = $user_count - $nonadmin_count;
	$count = array('user' => $user_count, 'admin' => $admin_count, 'nonadmin' => $nonadmin_count);
	$log->debug("Exiting UserCount method ...");
	return $count;
}

/**
 * This function is used to create folders recursively.
 * Param $dir - directory name
 * Param $mode - directory access mode
 * Param $recursive - create directory recursive, default true
 */
function mkdirs($dir, $mode = 0777, $recursive = true) {
	global $log;
	$log->debug("Entering mkdirs(" . $dir . "," . $mode . "," . $recursive . ") method ...");
	if (is_null($dir) || $dir === "") {
		$log->debug("Exiting mkdirs method ...");
		return FALSE;
	}
	if (is_dir($dir) || $dir === "/") {
		$log->debug("Exiting mkdirs method ...");
		return TRUE;
	}
	if (mkdirs(dirname($dir), $mode, $recursive)) {
		$log->debug("Exiting mkdirs method ...");
		return mkdir($dir, $mode);
	}
	$log->debug("Exiting mkdirs method ...");
	return FALSE;
}

/**
 * This function is used to set the Object values from the REQUEST values.
 * @param  object reference $focus - reference of the object
 */
function setObjectValuesFromRequest($focus) {
	global $log;
	$log->debug("Entering setObjectValuesFromRequest(" . get_class($focus) . ") method ...");
	if (isset($_REQUEST['record'])) {
		$focus->id = $_REQUEST['record'];
	}
	if (isset($_REQUEST['mode'])) {
		$focus->mode = $_REQUEST['mode'];
	}
	foreach ($focus->column_fields as $fieldname => $val) {
		if (isset($_REQUEST[$fieldname])) {
			if (is_array($_REQUEST[$fieldname]))
				$value = $_REQUEST[$fieldname];
			else
				$value = trim($_REQUEST[$fieldname]);
			$focus->column_fields[$fieldname] = $value;
		}
	}
	$log->debug("Exiting setObjectValuesFromRequest method ...");
}

/**
 * Function to write the tabid and name to a flat file vtiger_tabdata.txt so that the data
 * is obtained from the file instead of repeated queries
 * returns null
 */
function create_tab_data_file() {
	global $log;
	$log->debug("Entering create_tab_data_file() method ...");
	$log->info("creating vtiger_tabdata file");
	global $adb;
	//$sql = "select * from vtiger_tab";
	// vtlib customization: Disabling the tab item based on presence
	$sql = "select * from vtiger_tab where presence in (0,2)";
	// END

	$result = $adb->pquery($sql, array());
	$num_rows = $adb->num_rows($result);
	$result_array = Array();
	$seq_array = Array();
	$ownedby_array = Array();

	for ($i = 0; $i < $num_rows; $i++) {
		$tabid = $adb->query_result($result, $i, 'tabid');
		$tabname = $adb->query_result($result, $i, 'name');
		$presence = $adb->query_result($result, $i, 'presence');
		$ownedby = $adb->query_result($result, $i, 'ownedby');
		$result_array[$tabname] = $tabid;
		$seq_array[$tabid] = $presence;
		$ownedby_array[$tabid] = $ownedby;
	}

	//Constructing the actionname=>actionid array
	$actionid_array = Array();
	$sql1 = "select * from vtiger_actionmapping";
	$result1 = $adb->pquery($sql1, array());
	$num_seq1 = $adb->num_rows($result1);
	for ($i = 0; $i < $num_seq1; $i++) {
		$actionname = $adb->query_result($result1, $i, 'actionname');
		$actionid = $adb->query_result($result1, $i, 'actionid');
		$actionid_array[$actionname] = $actionid;
	}

	//Constructing the actionid=>actionname array with securitycheck=0
	$actionname_array = Array();
	$sql2 = "select * from vtiger_actionmapping where securitycheck=0";
	$result2 = $adb->pquery($sql2, array());
	$num_seq2 = $adb->num_rows($result2);
	for ($i = 0; $i < $num_seq2; $i++) {
		$actionname = $adb->query_result($result2, $i, 'actionname');
		$actionid = $adb->query_result($result2, $i, 'actionid');
		$actionname_array[$actionid] = $actionname;
	}

	$filename = 'tabdata.php';


	if (file_exists($filename)) {

		if (is_writable($filename)) {

			if (!$handle = fopen($filename, 'w+')) {
				echo "Cannot open file ($filename)";
				exit;
			}
			require_once('modules/Users/CreateUserPrivilegeFile.php');
			$newbuf = '';
			$newbuf .="<?php\n\n";
			$newbuf .="\n";
			$newbuf .= "//This file contains the commonly used variables \n";
			$newbuf .= "\n";
			$newbuf .= "\$tab_info_array=" . constructArray($result_array) . ";\n";
			$newbuf .= "\n";
			$newbuf .= "\$tab_seq_array=" . constructArray($seq_array) . ";\n";
			$newbuf .= "\n";
			$newbuf .= "\$tab_ownedby_array=" . constructArray($ownedby_array) . ";\n";
			$newbuf .= "\n";
			$newbuf .= "\$action_id_array=" . constructSingleStringKeyAndValueArray($actionid_array) . ";\n";
			$newbuf .= "\n";
			$newbuf .= "\$action_name_array=" . constructSingleStringValueArray($actionname_array) . ";\n";
			$newbuf .= "?>";
			fputs($handle, $newbuf);
			fclose($handle);
		} else {
			echo "The file $filename is not writable";
		}
	} else {
		echo "The file $filename does not exist";
		$log->debug("Exiting create_tab_data_file method ...");
		return;
	}
}

/**
 * Function to write the vtiger_parenttabid and name to a flat file parent_tabdata.txt so that the data
 * is obtained from the file instead of repeated queries
 * returns null
 */
function create_parenttab_data_file() {
	global $log;
	$log->debug("Entering create_parenttab_data_file() method ...");
	$log->info("creating parent_tabdata file");
	global $adb;
	$sql = "select parenttabid,parenttab_label from vtiger_parenttab where visible=0 order by sequence";
	$result = $adb->pquery($sql, array());
	$num_rows = $adb->num_rows($result);
	$result_array = Array();
	for ($i = 0; $i < $num_rows; $i++) {
		$parenttabid = $adb->query_result($result, $i, 'parenttabid');
		$parenttab_label = $adb->query_result($result, $i, 'parenttab_label');
		$result_array[$parenttabid] = $parenttab_label;
	}

	$filename = 'parent_tabdata.php';


	if (file_exists($filename)) {

		if (is_writable($filename)) {

			if (!$handle = fopen($filename, 'w+')) {
				echo "Cannot open file ($filename)";
				exit;
			}
			require_once('modules/Users/CreateUserPrivilegeFile.php');
			$newbuf = '';
			$newbuf .="<?php\n\n";
			$newbuf .="\n";
			$newbuf .= "//This file contains the commonly used variables \n";
			$newbuf .= "\n";
			$newbuf .= "\$parent_tab_info_array=" . constructSingleStringValueArray($result_array) . ";\n";
			$newbuf .="\n";


			$parChildTabRelArray = Array();

			foreach ($result_array as $parid => $parvalue) {
				$childArray = Array();
				//$sql = "select * from vtiger_parenttabrel where parenttabid=? order by sequence";
				// vtlib customization: Disabling the tab item based on presence
				$sql = "select * from vtiger_parenttabrel where parenttabid=?
					and tabid in (select tabid from vtiger_tab where presence in (0,2)) order by sequence";
				// END
				$result = $adb->pquery($sql, array($parid));
				$num_rows = $adb->num_rows($result);
				$result_array = Array();
				for ($i = 0; $i < $num_rows; $i++) {
					$tabid = $adb->query_result($result, $i, 'tabid');
					$childArray[] = $tabid;
				}
				$parChildTabRelArray[$parid] = $childArray;
			}
			$newbuf .= "\n";
			$newbuf .= "\$parent_child_tab_rel_array=" . constructTwoDimensionalValueArray($parChildTabRelArray) . ";\n";
			$newbuf .="\n";
			$newbuf .="\n";
			$newbuf .="\n";
			$newbuf .= "?>";
			fputs($handle, $newbuf);
			fclose($handle);
		} else {
			echo "The file $filename is not writable";
		}
	} else {
		echo "The file $filename does not exist";
		$log->debug("Exiting create_parenttab_data_file method ...");
		return;
	}
}

/**
 * This function is used to get the all the modules that have Quick Create Feature.
 * Returns Tab Name and Tablabel.
 */
function getQuickCreateModules() {
	global $log;
	$log->debug("Entering getQuickCreateModules() method ...");
	global $adb;
	global $mod_strings;

	// vtlib customization: Ignore disabled modules.
	//$qc_query = "select distinct vtiger_tab.tablabel,vtiger_tab.name from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid where quickcreate=0 order by vtiger_tab.tablabel";
	$qc_query = "select distinct vtiger_tab.tablabel,vtiger_tab.name from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid where quickcreate=0 and vtiger_tab.presence != 1 order by vtiger_tab.tablabel";
	// END

	$result = $adb->pquery($qc_query, array());
	$noofrows = $adb->num_rows($result);
	$return_qcmodule = Array();
	for ($i = 0; $i < $noofrows; $i++) {
		$tablabel = $adb->query_result($result, $i, 'tablabel');

		$tabname = $adb->query_result($result, $i, 'name');
		$tablabel = getTranslatedString("SINGLE_$tabname", $tabname);
		if (isPermitted($tabname, 'EditView', '') == 'yes') {
			$return_qcmodule[] = $tablabel;
			$return_qcmodule[] = $tabname;
		}
	}
	if (sizeof($return_qcmodule > 0)) {
		$return_qcmodule = array_chunk($return_qcmodule, 2);
	}

	$log->debug("Exiting getQuickCreateModules method ...");
	return $return_qcmodule;
}

/**
 * This function is used to get the Quick create form vtiger_field parameters for a given module.
 * Param $module - module name
 * returns the value in array format
 */
function QuickCreate($module) {
	global $log;
	$log->debug("Entering QuickCreate(" . $module . ") method ...");
	global $adb;
	global $current_user;
	global $mod_strings;

	$tabid = getTabid($module);

	//Adding Security Check
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
		$quickcreate_query = "select * from vtiger_field where quickcreate in (0,2) and tabid = ? and vtiger_field.presence in (0,2) and displaytype != 2 order by quickcreatesequence";
		$params = array($tabid);
	} else {
		$profileList = getCurrentUserProfileList();
		$quickcreate_query = "SELECT vtiger_field.* FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND quickcreate in (0,2) AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly = 0 AND vtiger_def_org_field.visible=0  AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ") and vtiger_field.presence in (0,2) and displaytype != 2 GROUP BY vtiger_field.fieldid ORDER BY quickcreatesequence";
		$params = array($tabid, $profileList);
		//Postgres 8 fixes
		if ($adb->dbType == "pgsql")
			$quickcreate_query = fixPostgresQuery($quickcreate_query, $log, 0);
	}
	$category = getParentTab();
	$result = $adb->pquery($quickcreate_query, $params);
	$noofrows = $adb->num_rows($result);
	$fieldName_array = Array();
	for ($i = 0; $i < $noofrows; $i++) {
		$fieldtablename = $adb->query_result($result, $i, 'tablename');
		$uitype = $adb->query_result($result, $i, "uitype");
		$fieldname = $adb->query_result($result, $i, "fieldname");
		$fieldlabel = $adb->query_result($result, $i, "fieldlabel");
		$maxlength = $adb->query_result($result, $i, "maximumlength");
		$generatedtype = $adb->query_result($result, $i, "generatedtype");
		$typeofdata = $adb->query_result($result, $i, "typeofdata");
		$defaultvalue = $adb->query_result($result, $i, "defaultvalue");
		$col_fields[$fieldname] = $defaultvalue;

		//to get validationdata
		$fldLabel_array = Array();
		$fldLabel_array[getTranslatedString($fieldlabel)] = $typeofdata;
		$fieldName_array[$fieldname] = $fldLabel_array;

		// These fields should not be shown in the UI as they are already shown as part of other fields, but are required for validation.
		if ($fieldname == 'time_start' || $fieldname == 'time_end')
			continue;

		$custfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields, $generatedtype, $module, '', $typeofdata);
		$qcreate_arr[] = $custfld;
	}
	for ($i = 0, $j = 0; $i < count($qcreate_arr); $i = $i + 2, $j++) {
		$key1 = $qcreate_arr[$i];
		if (is_array($qcreate_arr[$i + 1])) {
			$key2 = $qcreate_arr[$i + 1];
		} else {
			$key2 = array();
		}
		$return_data[$j] = array(0 => $key1, 1 => $key2);
	}
	$form_data['form'] = $return_data;
	$form_data['data'] = $fieldName_array;
	$log->debug("Exiting QuickCreate method ..." . print_r($form_data, true));
	return $form_data;
}

function getUserslist($setdefval = true) {
	global $log, $current_user, $module, $adb, $assigned_user_id;
	$log->debug("Entering getUserslist() method ...");
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	require('user_privileges/sharing_privileges_' . $current_user->id . '.php');

	if ($is_admin == false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module)] == 3 or $defaultOrgSharingPermission[getTabid($module)] == 0)) {
		$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $current_user->id, 'private'), $current_user->id);
	} else {
		$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $current_user->id), $current_user->id);
	}
	foreach ($users_combo as $userid => $value) {

		foreach ($value as $username => $selected) {
			if ($setdefval == false) {
				$change_owner .= "<option value=$userid>" . $username . "</option>";
			} else {
				$change_owner .= "<option value=$userid $selected>" . $username . "</option>";
			}
		}
	}

	$log->debug("Exiting getUserslist method ...");
	return $change_owner;
}

function getGroupslist() {
	global $log, $adb, $module, $current_user;
	$log->debug("Entering getGroupslist() method ...");
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	require('user_privileges/sharing_privileges_' . $current_user->id . '.php');

	//Commented to avoid security check for groups
	if ($is_admin == false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module)] == 3 or $defaultOrgSharingPermission[getTabid($module)] == 0)) {
		$result = get_current_user_access_groups($module);
	} else {
		$result = get_group_options();
	}

	if ($result)
		$nameArray = $adb->fetch_array($result);
	if (!empty($nameArray)) {
		if ($is_admin == false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module)] == 3 or $defaultOrgSharingPermission[getTabid($module)] == 0)) {
			$groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $current_user->id, 'private'), $current_user->id);
		} else {
			$groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $current_user->id), $current_user->id);
		}
	}
	if (count($groups_combo) > 0) {
		foreach ($groups_combo as $groupid => $value) {
			foreach ($value as $groupname => $selected) {
				$change_groups_owner .= "<option value=$groupid $selected >" . $groupname . "</option>";
			}
		}
	}
	$log->debug("Exiting getGroupslist method ...");
	return $change_groups_owner;
}

/**
 * 	Function to Check for Security whether the Buttons are permitted in List/Edit/Detail View of all Modules
 * 	@param string $module -- module name
 * 	Returns an array with permission as Yes or No
 * */
function Button_Check($module) {
	global $log;
	$log->debug("Entering Button_Check(" . $module . ") method ...");
	$permit_arr = array('EditView' => '',
		'index' => '',
		'Import' => '',
		'Export' => '',
		'Merge' => '',
		'DuplicatesHandling' => '');

	foreach ($permit_arr as $action => $perr) {
		$tempPer = isPermitted($module, $action, '');
		$permit_arr[$action] = $tempPer;
	}
	$permit_arr["Calendar"] = isPermitted("Calendar", "index", '');
	$permit_arr["moduleSettings"] = isModuleSettingPermitted($module);
	$log->debug("Exiting Button_Check method ...");
	return $permit_arr;
}

/**
 * 	Function to Check whether the User is allowed to delete a particular record from listview of each module using
 * 	mass delete button.
 * 	@param string $module -- module name
 * 	@param array $ids_list -- Record id
 * 	Returns the Record Names of each module that is not permitted to delete
 * */
function getEntityName($module, $ids_list) {
	global $adb, $log;
	$log->debug("Entering getEntityName(" . $module . ") method ...");
	if ($module == 'Events') {
		$module = 'Calendar';
	}
	if ($module != '') {
		if (count($ids_list) <= 0) {
			return array();
		}
		$entityDisplay = array();
		$entity_field_info = getEntityFieldNames($module);
		$tableName = $entity_field_info['tablename'];
		$fieldsName = $entity_field_info['fieldname'];
		$moduleName = $entity_field_info['modulename'];
		$entityIdField = $entity_field_info['entityidfield'];
		$entity_FieldValue = getEntityFieldValues($entity_field_info, $ids_list);

		foreach($entity_FieldValue as $key => $entityInfo) {
			foreach($entityInfo as $key => $entityName) {
				$fieldValues = $entityName;
				$entityDisplay[$key] = getEntityFieldNameDisplay($module, $fieldsName, $fieldValues);
			}
		}
		return $entityDisplay;
	}
	$log->debug("Exiting getEntityName method ...");
}

/* * Function to get all permitted modules for a user with their parent
 */

function getAllParenttabmoduleslist() {
	global $adb;
	global $current_user;
	$resultant_array = Array();

	//$query = 'select name,tablabel,parenttab_label,vtiger_tab.tabid from vtiger_parenttabrel inner join vtiger_tab on vtiger_parenttabrel.tabid = vtiger_tab.tabid inner join vtiger_parenttab on vtiger_parenttabrel.parenttabid = vtiger_parenttab.parenttabid and vtiger_tab.presence order by vtiger_parenttab.sequence, vtiger_parenttabrel.sequence';
	// vtlib customization: Disabling the tab item based on presence
	$query = 'select name,tablabel,parenttab_label,vtiger_tab.tabid from vtiger_parenttabrel inner join vtiger_tab on vtiger_parenttabrel.tabid = vtiger_tab.tabid inner join vtiger_parenttab on vtiger_parenttabrel.parenttabid = vtiger_parenttab.parenttabid and vtiger_tab.presence in (0,2) order by vtiger_parenttab.sequence, vtiger_parenttabrel.sequence';
	// END

	$result = $adb->pquery($query, array());
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	for ($i = 0; $i < $adb->num_rows($result); $i++) {
		$parenttabname = $adb->query_result($result, $i, 'parenttab_label');
		$modulename = $adb->query_result($result, $i, 'name');
		$tablabel = $adb->query_result($result, $i, 'tablabel');
		$tabid = $adb->query_result($result, $i, 'tabid');
		if ($is_admin) {
			$resultant_array[$parenttabname][] = Array($modulename, $tablabel);
		} elseif ($profileGlobalPermission[2] == 0 || $profileGlobalPermission[1] == 0 || $profileTabsPermission[$tabid] === 0) {
			$resultant_array[$parenttabname][] = Array($modulename, $tablabel);
		}
	}

	if ($is_admin) {
		$resultant_array['Settings'][] = Array('Settings', 'Settings');
		$resultant_array['Settings'][] = Array('Settings', getTranslatedString('VTLIB_LBL_MODULE_MANAGER', 'Settings'), 'ModuleManager');
	}
	return $resultant_array;
}

/**
 * 	This function is used to decide the File Storage Path in where we will upload the file in the server.
 * 	return string $filepath  - filepath inwhere the file should be stored in the server will be return
 */
function decideFilePath() {
	global $log, $adb;
	$log->debug("Entering into decideFilePath() method ...");

	$filepath = 'storage/';

	$year = date('Y');
	$month = date('F');
	$day = date('j');
	$week = '';

	if (!is_dir($filepath . $year)) {
		//create new folder
		mkdir($filepath . $year);
	}

	if (!is_dir($filepath . $year . "/" . $month)) {
		//create new folder
		mkdir($filepath . "$year/$month");
	}

	if ($day > 0 && $day <= 7)
		$week = 'week1';
	elseif ($day > 7 && $day <= 14)
		$week = 'week2';
	elseif ($day > 14 && $day <= 21)
		$week = 'week3';
	elseif ($day > 21 && $day <= 28)
		$week = 'week4';
	else
		$week = 'week5';

	if (!is_dir($filepath . $year . "/" . $month . "/" . $week)) {
		//create new folder
		mkdir($filepath . "$year/$month/$week");
	}

	$filepath = $filepath . $year . "/" . $month . "/" . $week . "/";

	$log->debug("Year=$year & Month=$month & week=$week && filepath=\"$filepath\"");
	$log->debug("Exiting from decideFilePath() method ...");

	return $filepath;
}

/**
 * 	This function is used to get the Path in where we store the vtiger_files based on the module.
 * 	@param string $module   - module name
 * 	return string $storage_path - path inwhere the file will be uploaded (also where it was stored) will be return based on the module
 */
function getModuleFileStoragePath($module) {
	global $log;
	$log->debug("Entering into getModuleFileStoragePath($module) method ...");

	$storage_path = "test/";

	if ($module == 'Products') {
		$storage_path .= 'product/';
	}
	if ($module == 'Contacts') {
		$storage_path .= 'contact/';
	}

	$log->debug("Exiting from getModuleFileStoragePath($module) method. return storage_path = \"$storage_path\"");
	return $storage_path;
}

/**
 * 	This function is used to check whether the attached file is a image file or not
 * 	@param string $file_details  - vtiger_files array which contains all the uploaded file details
 * 	return string $save_image - true or false. if the image can be uploaded then true will return otherwise false.
 */
function validateImageFile($file_details) {
	global $adb, $log, $app_strings;
	$log->debug("Entering into validateImageFile($file_details) method.");

	$savefile = 'true';
	$file_type_details = explode("/", $file_details['type']);
	$filetype = $file_type_details['1'];

	if (!empty($filetype))
		$filetype = strtolower($filetype);
	if (($filetype == "jpeg" ) || ($filetype == "png") || ($filetype == "jpg" ) || ($filetype == "pjpeg" ) || ($filetype == "x-png") || ($filetype == "gif") || ($filetype == 'bmp')) {
		$saveimage = 'true';
	} else {
		$saveimage = 'false';
		$_SESSION['image_type_error'] .= "<br> &nbsp;&nbsp;<b>" . $file_details[name] . "</b>" . $app_strings['MSG_IS_NOT_UPLOADED'];
		$log->debug("Invalid Image type == $filetype");
	}

	$log->debug("Exiting from validateImageFile($file_details) method. return saveimage=$saveimage");
	return $saveimage;
}

/**
 * 	This function is used to get the Email Template Details like subject and content for particular template.
 * 	@param integer $templateid  - Template Id for an Email Template
 * 	return array $returndata - Returns Subject, Body of Template of the the particular email template.
 */
function getTemplateDetails($templateid) {
	global $adb, $log;
	$log->debug("Entering into getTemplateDetails($templateid) method ...");
	$returndata = Array();
	$result = $adb->pquery("select * from vtiger_emailtemplates where templateid=?", array($templateid));
	$returndata[] = $templateid;
	$returndata[] = $adb->query_result($result, 0, 'body');
	$returndata[] = $adb->query_result($result, 0, 'subject');
	$log->debug("Exiting from getTemplateDetails($templateid) method ...");
	return $returndata;
}

/**
 * 	This function is used to merge the Template Details with the email description
 *  @param string $description  -body of the mail(ie template)
 * 	@param integer $tid  - Id of the entity
 *  @param string $parent_type - module of the entity
 * 	return string $description - Returns description, merged with the input template.
 */
function getMergedDescription($description, $id, $parent_type) {
	global $adb, $log;
	$log->debug("Entering getMergedDescription ...");
	$token_data_pair = explode('$', $description);
	global $current_user;
	$emailTemplate = new EmailTemplate($parent_type, $description, $id, $current_user);
	$description = $emailTemplate->getProcessedDescription();
	$templateVariablePair = explode('$', $description);
	$tokenDataPair = explode('$', $description);
	$fields = Array();
	for ($i = 1; $i < count($token_data_pair); $i+=2) {

		$module = explode('-', $tokenDataPair[$i]);
		$fields[$module[0]][] = $module[1];
	}
	if (is_array($fields['custom']) && count($fields['custom']) > 0) {
		//Puneeth : Added for custom date & time fields
		$description = getMergedDescriptionCustomVars($fields, $description);
	}
	$log->debug("Exiting from getMergedDescription ...");
	return $description;
}

/* Function to merge the custom date & time fields in email templates */

function getMergedDescriptionCustomVars($fields, $description) {
	foreach ($fields['custom'] as $columnname) {
		$token_data = '$custom-' . $columnname . '$';
		$token_value = '';
		switch ($columnname) {
			case 'currentdate': $token_value = date("F j, Y");
				break;
			case 'currenttime': $token_value = date("G:i:s T");
				break;
		}
		$description = str_replace($token_data, $token_value, $description);
	}
	return $description;
}

//End : Custom date & time fields

/** 	Function used to retrieve a single field value from database
 * 	@param string $tablename - tablename from which we will retrieve the field value
 * 	@param string $fieldname - fieldname to which we want to get the value from database
 * 	@param string $idname	 - idname which is the name of the entity id in the table like, inoviceid, quoteid, etc.,
 * 	@param int    $id	 - entity id
 * 	return string $fieldval  - field value of the needed fieldname from database will be returned
 */
function getSingleFieldValue($tablename, $fieldname, $idname, $id) {
	global $log, $adb;
	$log->debug("Entering into function getSingleFieldValue($tablename, $fieldname, $idname, $id)");

	$fieldval = $adb->query_result($adb->pquery("select $fieldname from $tablename where $idname = ?", array($id)), 0, $fieldname);

	$log->debug("Exit from function getSingleFieldValue. return value ==> \"$fieldval\"");

	return $fieldval;
}

/** 	Function used to retrieve the announcements from database
 * 	The function accepts no argument and returns the announcements
 * 	return string $announcement  - List of announments for the CRM users
 */
function get_announcements() {
	global $adb;
	$sql = " select * from vtiger_announcement inner join vtiger_users on vtiger_announcement.creatorid=vtiger_users.id";
	$sql.=" AND vtiger_users.is_admin='on' AND vtiger_users.status='Active' AND vtiger_users.deleted = 0";
	$result = $adb->pquery($sql, array());
	for ($i = 0; $i < $adb->num_rows($result); $i++) {
		$announce = getUserFullName($adb->query_result($result, $i, 'creatorid')) . ' :  ' . $adb->query_result($result, $i, 'announcement') . '   ';
		if ($adb->query_result($result, $i, 'announcement') != '')
			$announcement.=$announce;
	}
	return $announcement;
}

/**
 *  Function to get recurring info depending on the recurring type
 *  return  $recurObj       - Object of class RecurringType
 */
function getrecurringObjValue() {
	$recurring_data = array();
	if (isset($_REQUEST['recurringtype']) && $_REQUEST['recurringtype'] != null && $_REQUEST['recurringtype'] != '--None--') {
		if (!empty($_REQUEST['date_start'])) {
			$startDate = $_REQUEST['date_start'];
		}
		if (!empty($_REQUEST['calendar_repeat_limit_date'])) {
			$endDate = $_REQUEST['calendar_repeat_limit_date'];
		} elseif (isset($_REQUEST['due_date']) && $_REQUEST['due_date'] != null) {
			$endDate = $_REQUEST['due_date'];
		}
		if (!empty($_REQUEST['time_start'])) {
			$startTime = $_REQUEST['time_start'];
		}
		if (!empty($_REQUEST['time_end'])) {
			$endTime = $_REQUEST['time_end'];
		}

		$recurring_data['startdate'] = $startDate;
		$recurring_data['starttime'] = $startTime;
		$recurring_data['enddate'] = $endDate;
		$recurring_data['endtime'] = $endTime;

		$recurring_data['type'] = $_REQUEST['recurringtype'];
		if ($_REQUEST['recurringtype'] == 'Weekly') {
			if (isset($_REQUEST['sun_flag']) && $_REQUEST['sun_flag'] != null)
				$recurring_data['sun_flag'] = true;
			if (isset($_REQUEST['mon_flag']) && $_REQUEST['mon_flag'] != null)
				$recurring_data['mon_flag'] = true;
			if (isset($_REQUEST['tue_flag']) && $_REQUEST['tue_flag'] != null)
				$recurring_data['tue_flag'] = true;
			if (isset($_REQUEST['wed_flag']) && $_REQUEST['wed_flag'] != null)
				$recurring_data['wed_flag'] = true;
			if (isset($_REQUEST['thu_flag']) && $_REQUEST['thu_flag'] != null)
				$recurring_data['thu_flag'] = true;
			if (isset($_REQUEST['fri_flag']) && $_REQUEST['fri_flag'] != null)
				$recurring_data['fri_flag'] = true;
			if (isset($_REQUEST['sat_flag']) && $_REQUEST['sat_flag'] != null)
				$recurring_data['sat_flag'] = true;
		}
		elseif ($_REQUEST['recurringtype'] == 'Monthly') {
			if (isset($_REQUEST['repeatMonth']) && $_REQUEST['repeatMonth'] != null)
				$recurring_data['repeatmonth_type'] = $_REQUEST['repeatMonth'];
			if ($recurring_data['repeatmonth_type'] == 'date') {
				if (isset($_REQUEST['repeatMonth_date']) && $_REQUEST['repeatMonth_date'] != null)
					$recurring_data['repeatmonth_date'] = $_REQUEST['repeatMonth_date'];
				else
					$recurring_data['repeatmonth_date'] = 1;
			}
			elseif ($recurring_data['repeatmonth_type'] == 'day') {
				$recurring_data['repeatmonth_daytype'] = $_REQUEST['repeatMonth_daytype'];
				switch ($_REQUEST['repeatMonth_day']) {
					case 0 :
						$recurring_data['sun_flag'] = true;
						break;
					case 1 :
						$recurring_data['mon_flag'] = true;
						break;
					case 2 :
						$recurring_data['tue_flag'] = true;
						break;
					case 3 :
						$recurring_data['wed_flag'] = true;
						break;
					case 4 :
						$recurring_data['thu_flag'] = true;
						break;
					case 5 :
						$recurring_data['fri_flag'] = true;
						break;
					case 6 :
						$recurring_data['sat_flag'] = true;
						break;
				}
			}
		}
		if (isset($_REQUEST['repeat_frequency']) && $_REQUEST['repeat_frequency'] != null)
			$recurring_data['repeat_frequency'] = $_REQUEST['repeat_frequency'];

		$recurObj = RecurringType::fromUserRequest($recurring_data);
		return $recurObj;
	}
}

/** 	Function used to get the translated string to the input string
 * 	@param string $str - input string which we want to translate
 * 	@return string $str - translated string, if the translated string is available then the translated string other wise original string will be returned
 */
function getTranslatedString($str, $module = '') {
	global $app_strings, $mod_strings, $log, $current_language;
	$temp_mod_strings = ($module != '' ) ? return_module_language($current_language, $module) : $mod_strings;
	$trans_str = ($temp_mod_strings[$str] != '') ? $temp_mod_strings[$str] : (($app_strings[$str] != '') ? $app_strings[$str] : $str);
	$log->debug("function getTranslatedString($str) - translated to ($trans_str)");
	return $trans_str;
}

/**
 * Get translated currency name string.
 * @param String $str - input currency name
 * @return String $str - translated currency name
 */
function getTranslatedCurrencyString($str) {
	global $app_currency_strings;
	if (isset($app_currency_strings) && isset($app_currency_strings[$str])) {
		return $app_currency_strings[$str];
	}
	return $str;
}

/** 	function used to get the list of importable fields
 * 	@param string $module - module name
 * 	@return array $fieldslist - array with list of fieldnames and the corresponding translated fieldlabels. The return array will be in the format of [fieldname]=>[fieldlabel] where as the fieldlabel will be translated
 */
function getImportFieldsList($module) {
	global $adb, $log;
	$log->debug("Entering into function getImportFieldsList($module)");

	$tabid = getTabid($module);

	//Here we can add special cases for module basis, ie., if we want the fields of display type 3, we can add
	$displaytype = " displaytype=1 and vtiger_field.presence in (0,2) ";

	$fieldnames = "";
	//For module basis we can add the list of fields for Import mapping
	if ($module == "Leads" || $module == "Contacts") {
		$fieldnames = " fieldname='salutationtype' ";
	}

	//Form the where condition based on tabid , displaytype and extra fields
	$where = " WHERE tabid=? and ( $displaytype ";
	$params = array($tabid);
	if ($fieldnames != "") {
		$where .= " or $fieldnames ";
	}
	$where .= ")";

	//Get the list of fields and form as array with [fieldname] => [fieldlabel]
	$query = "SELECT fieldname, fieldlabel FROM vtiger_field $where";
	$result = $adb->pquery($query, $params);
	for ($i = 0; $i < $adb->num_rows($result); $i++) {
		$fieldname = $adb->query_result($result, $i, 'fieldname');
		$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
		$fieldslist[$fieldname] = getTranslatedString($fieldlabel, $module);
	}

	$log->debug("Exit from function getImportFieldsList($module)");

	return $fieldslist;
}

/**     Function to get all the comments for a troubleticket
 *     @param int $ticketid -- troubleticket id
 *     return all the comments as a sequencial string which are related to this ticket
 * */
function getTicketComments($ticketid) {
	global $log;
	$log->debug("Entering getTicketComments(" . $ticketid . ") method ...");
	global $adb;

	$moduleName = getSalesEntityType($ticketid);
	$commentlist = '';
	$sql = "select * from vtiger_ticketcomments where ticketid=?";
	$result = $adb->pquery($sql, array($ticketid));
	for ($i = 0; $i < $adb->num_rows($result); $i++) {
		$comment = $adb->query_result($result, $i, 'comments');
		if ($comment != '') {
			$commentlist .= '<br><br>' . $comment;
		}
	}
	if ($commentlist != '')
		$commentlist = '<br><br>' . getTranslatedString("The comments are", $moduleName) . ' : ' . $commentlist;

	$log->debug("Exiting getTicketComments method ...");
	return $commentlist;
}

/**
 * This function is used to get a random password.
 * @return a random password with alpha numeric chanreters of length 8
 */
function makeRandomPassword() {
	global $log;
	$log->debug("Entering makeRandomPassword() method ...");
	$salt = "abcdefghijklmnopqrstuvwxyz0123456789";
	srand((double) microtime() * 1000000);
	$i = 0;
	while ($i <= 7) {
		$num = rand() % 33;
		$tmp = substr($salt, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}
	$log->debug("Exiting makeRandomPassword method ...");
	return $pass;
}

/**
 * Function to get the UItype for a field.
 * Takes the input as $module - module name,and columnname of the field
 * returns the uitype, integer type
 */
function getUItype($module, $columnname) {
	global $log;
	$log->debug("Entering getUItype(" . $module . ") method ...");
	$tabIdList = array();
	//To find tabid for this module
	$tabIdList[] = getTabid($module);
	global $adb;
	if ($module == 'Calendar') {
		$tabIdList[] = getTabid('Events');
	}
	$sql = "select uitype from vtiger_field where tabid IN (" . generateQuestionMarks($tabIdList) .
			") and columnname=?";
	$result = $adb->pquery($sql, array($tabIdList, $columnname));
	$uitype = $adb->query_result($result, 0, "uitype");
	$log->debug("Exiting getUItype method ...");
	return $uitype;
}

// This function looks like not used anymore. May have to be removed
function is_emailId($entity_id) {
	global $log, $adb;
	$log->debug("Entering is_EmailId(" . $entity_id . ") method");

	$module = getSalesEntityType($entity_id);
	if ($module == 'Contacts') {
		$sql = "select email,secondaryemail from vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid where contactid = ?";
		$result = $adb->pquery($sql, array($entity_id));
		$email1 = $adb->query_result($result, 0, "email");
		$emaWil2 = $adb->query_result($result, 0, "secondaryemail");
		if ($email1 != "" || $email2 != "") {
			$check_mailids = "true";
		} else {
			$check_mailids = "false";
		}
	} elseif ($module == 'Leads') {
		$sql = "select email,secondaryemail from vtiger_leaddetails inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_leaddetails.leadid where leadid = ?";
		$result = $adb->pquery($sql, array($entity_id));
		$email1 = $adb->query_result($result, 0, "email");
		$email2 = $adb->query_result($result, 0, "secondaryemail");
		if ($email1 != "" || $email2 != "") {
			$check_mailids = "true";
		} else {
			$check_mailids = "false";
		}
	}
	$log->debug("Exiting is_EmailId() method ...");
	return $check_mailids;
}

/**
 * This function is used to get cvid of default "all" view for any module.
 * @return a cvid of a module
 */
function getCvIdOfAll($module) {
	global $adb, $log;
	$log->debug("Entering getCvIdOfAll($module)");
	$qry_res = $adb->pquery("select cvid from vtiger_customview where viewname='All' and entitytype=?", array($module));
	$cvid = $adb->query_result($qry_res, 0, "cvid");
	$log->debug("Exiting getCvIdOfAll($module)");
	return $cvid;
}

/** gives the option  to display  the tagclouds or not for the current user
 * * @param $id -- user id:: Type integer
 * * @returns true or false in $tag_cloud_view
 * * Added to provide User based Tagcloud
 * */
function getTagCloudView($id = "") {
	global $log;
	global $adb;
	$log->debug("Entering in function getTagCloudView($id)");
	if ($id == '') {
		$tag_cloud_status = 1;
	} else {
		$query = "select visible from vtiger_homestuff where userid=? and stufftype='Tag Cloud'";
		$tag_cloud_status = $adb->query_result($adb->pquery($query, array($id)), 0, 'visible');
	}

	if ($tag_cloud_status == 0) {
		$tag_cloud_view = 'true';
	} else {
		$tag_cloud_view = 'false';
	}

	$log->debug("Exiting from function getTagCloudView($id)");
	return $tag_cloud_view;
}

/** Stores the option in database to display  the tagclouds or not for the current user
 * * @param $id -- user id:: Type integer
 * * Added to provide User based Tagcloud
 * */
function SaveTagCloudView($id = "") {
	global $log;
	global $adb;
	$log->debug("Entering in function SaveTagCloudView($id)");
	$tag_cloud_status = $_REQUEST['tagcloudview'];

	if ($tag_cloud_status == "true") {
		$tag_cloud_view = 0;
	} else {
		$tag_cloud_view = 1;
	}

	if ($id == '') {
		$tag_cloud_view = 1;
	} else {
		$query = "update vtiger_homestuff set visible = ? where userid=? and stufftype='Tag Cloud'";
		$adb->pquery($query, array($tag_cloud_view, $id));
	}

	$log->debug("Exiting from function SaveTagCloudView($id)");
}

/**     function used to change the Type of Data for advanced filters in custom view and Reports
 * *     @param string $table_name - tablename value from field table
 * *     @param string $column_nametable_name - columnname value from field table
 * *     @param string $type_of_data - current type of data of the field. It is to return the same TypeofData
 * *            if the  field is not matched with the $new_field_details array.
 * *     return string $type_of_data - If the string matched with the $new_field_details array then the Changed
 * *	       typeofdata will return, else the same typeofdata will return.
 * *
 * *     EXAMPLE: If you have a field entry like this:
 * *
 * * 		fieldlabel         | typeofdata | tablename            | columnname       |
 * *	        -------------------+------------+----------------------+------------------+
 * *		Potential Name     | I~O        | vtiger_quotes        | potentialid      |
 * *
 * *     Then put an entry in $new_field_details  like this:
 * *
 * *				"vtiger_quotes:potentialid"=>"V",
 * *
 * *	Now in customview and report's advance filter this field's criteria will be show like string.
 * *
 * */
function ChangeTypeOfData_Filter($table_name, $column_name, $type_of_data) {
	global $adb, $log;
	//$log->debug("Entering function ChangeTypeOfData_Filter($table_name,$column_name,$type_of_data)");
	$field = $table_name . ":" . $column_name;
	//Add the field details in this array if you want to change the advance filter field details

	$new_field_details = Array(
		//Contacts Related Fields
		"vtiger_contactdetails:accountid" => "V",
		"vtiger_contactsubdetails:birthday" => "D",
		"vtiger_contactdetails:email" => "V",
		"vtiger_contactdetails:secondaryemail" => "V",
		//Potential Related Fields
		"vtiger_potential:campaignid" => "V",
		//Account Related Fields
		"vtiger_account:parentid" => "V",
		"vtiger_account:email1" => "V",
		"vtiger_account:email2" => "V",
		//Lead Related Fields
		"vtiger_leaddetails:email" => "V",
		"vtiger_leaddetails:secondaryemail" => "V",
		//Documents Related Fields
		"vtiger_senotesrel:crmid" => "V",
		//Calendar Related Fields
		"vtiger_seactivityrel:crmid" => "V",
		"vtiger_seactivityrel:contactid" => "V",
		"vtiger_recurringevents:recurringtype" => "V",
		//HelpDesk Related Fields
		"vtiger_troubletickets:parent_id" => "V",
		"vtiger_troubletickets:product_id" => "V",
		//Product Related Fields
		"vtiger_products:discontinued" => "C",
		"vtiger_products:vendor_id" => "V",
		"vtiger_products:parentid" => "V",
		//Faq Related Fields
		"vtiger_faq:product_id" => "V",
		//Vendor Related Fields
		"vtiger_vendor:email" => "V",
		//Quotes Related Fields
		"vtiger_quotes:potentialid" => "V",
		"vtiger_quotes:inventorymanager" => "V",
		"vtiger_quotes:accountid" => "V",
		//Purchase Order Related Fields
		"vtiger_purchaseorder:vendorid" => "V",
		"vtiger_purchaseorder:contactid" => "V",
		//SalesOrder Related Fields
		"vtiger_salesorder:potentialid" => "V",
		"vtiger_salesorder:quoteid" => "V",
		"vtiger_salesorder:contactid" => "V",
		"vtiger_salesorder:accountid" => "V",
		//Invoice Related Fields
		"vtiger_invoice:salesorderid" => "V",
		"vtiger_invoice:contactid" => "V",
		"vtiger_invoice:accountid" => "V",
		//Campaign Related Fields
		"vtiger_campaign:product_id" => "V",
		//Related List Entries(For Report Module)
		"vtiger_activityproductrel:activityid" => "V",
		"vtiger_activityproductrel:productid" => "V",
		"vtiger_campaigncontrel:campaignid" => "V",
		"vtiger_campaigncontrel:contactid" => "V",
		"vtiger_campaignleadrel:campaignid" => "V",
		"vtiger_campaignleadrel:leadid" => "V",
		"vtiger_cntactivityrel:contactid" => "V",
		"vtiger_cntactivityrel:activityid" => "V",
		"vtiger_contpotentialrel:contactid" => "V",
		"vtiger_contpotentialrel:potentialid" => "V",
		"vtiger_crmentitynotesrel:crmid" => "V",
		"vtiger_crmentitynotesrel:notesid" => "V",
		"vtiger_leadacctrel:leadid" => "V",
		"vtiger_leadacctrel:accountid" => "V",
		"vtiger_leadcontrel:leadid" => "V",
		"vtiger_leadcontrel:contactid" => "V",
		"vtiger_leadpotrel:leadid" => "V",
		"vtiger_leadpotrel:potentialid" => "V",
		"vtiger_pricebookproductrel:pricebookid" => "V",
		"vtiger_pricebookproductrel:productid" => "V",
		"vtiger_seactivityrel:crmid" => "V",
		"vtiger_seactivityrel:activityid" => "V",
		"vtiger_senotesrel:crmid" => "V",
		"vtiger_senotesrel:notesid" => "V",
		"vtiger_seproductsrel:crmid" => "V",
		"vtiger_seproductsrel:productid" => "V",
		"vtiger_seticketsrel:crmid" => "V",
		"vtiger_seticketsrel:ticketid" => "V",
		"vtiger_vendorcontactrel:vendorid" => "V",
		"vtiger_vendorcontactrel:contactid" => "V",
		"vtiger_pricebook:currency_id" => "V",
	);

	//If the Fields details does not match with the array, then we return the same typeofdata
	if (isset($new_field_details[$field])) {
		$type_of_data = $new_field_details[$field];
	}
	//$log->debug("Exiting function with the typeofdata: $type_of_data ");
	return $type_of_data;
}

/** Returns the URL for Basic and Advance Search
 * * Added to fix the issue 4600
 * */
function getBasic_Advance_SearchURL() {

	$url = '';
	if ($_REQUEST['searchtype'] == 'BasicSearch') {
		$url .= (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true') ? '&query=true' : '';
		$url .= (isset($_REQUEST['search_field'])) ? '&search_field=' . vtlib_purify($_REQUEST['search_field']) : '';
		$url .= (isset($_REQUEST['search_text'])) ? '&search_text=' . to_html(vtlib_purify($_REQUEST['search_text'])) : '';
		$url .= (isset($_REQUEST['searchtype'])) ? '&searchtype=' . vtlib_purify($_REQUEST['searchtype']) : '';
		$url .= (isset($_REQUEST['type'])) ? '&type=' . vtlib_purify($_REQUEST['type']) : '';
	}
	if ($_REQUEST['searchtype'] == 'advance') {
		$url .= (isset($_REQUEST['query'])) ? '&query=' . vtlib_purify($_REQUEST['query']) : '';
		$count = $_REQUEST['search_cnt'];
		for ($i = 0; $i < $count; $i++) {
			$url .= (isset($_REQUEST['Fields' . $i])) ? '&Fields' . $i . '=' . stripslashes(str_replace("'", "", vtlib_purify($_REQUEST['Fields' . $i]))) : '';
			$url .= (isset($_REQUEST['Condition' . $i])) ? '&Condition' . $i . '=' . vtlib_purify($_REQUEST['Condition' . $i]) : '';
			$url .= (isset($_REQUEST['Srch_value' . $i])) ? '&Srch_value' . $i . '=' . to_html(vtlib_purify($_REQUEST['Srch_value' . $i])) : '';
		}
		$url .= (isset($_REQUEST['searchtype'])) ? '&searchtype=' . vtlib_purify($_REQUEST['searchtype']) : '';
		$url .= (isset($_REQUEST['search_cnt'])) ? '&search_cnt=' . vtlib_purify($_REQUEST['search_cnt']) : '';
		$url .= (isset($_REQUEST['matchtype'])) ? '&matchtype=' . vtlib_purify($_REQUEST['matchtype']) : '';
	}
	return $url;
}

/** Clear the Smarty cache files(in Smarty/smarty_c)
 * * This function will called after migration.
 * */
function clear_smarty_cache($path = null) {

	global $root_directory;
	if ($path == null) {
		$path = $root_directory . 'Smarty/templates_c/';
	}
	$mydir = @opendir($path);
	while (false !== ($file = readdir($mydir))) {
		if ($file != "." && $file != ".." && $file != ".svn") {
			//chmod($path.$file, 0777);
			if (is_dir($path . $file)) {
				chdir('.');
				clear_smarty_cache($path . $file . '/');
				//rmdir($path.$file) or DIE("couldn't delete $path$file<br />"); // No need to delete the directories.
			} else {
				// Delete only files ending with .tpl.php
				if (strripos($file, '.tpl.php') == (strlen($file) - strlen('.tpl.php'))) {
					unlink($path . $file) or DIE("couldn't delete $path$file<br />");
				}
			}
		}
	}
	@closedir($mydir);
}

/** Get Smarty compiled file for the specified template filename.
 * * @param $template_file Template filename for which the compiled file has to be returned.
 * * @return Compiled file for the specified template file.
 * */
function get_smarty_compiled_file($template_file, $path = null) {

	global $root_directory;
	if ($path == null) {
		$path = $root_directory . 'Smarty/templates_c/';
	}
	$mydir = @opendir($path);
	$compiled_file = null;
	while (false !== ($file = readdir($mydir)) && $compiled_file == null) {
		if ($file != "." && $file != ".." && $file != ".svn") {
			//chmod($path.$file, 0777);
			if (is_dir($path . $file)) {
				chdir('.');
				$compiled_file = get_smarty_compiled_file($template_file, $path . $file . '/');
				//rmdir($path.$file) or DIE("couldn't delete $path$file<br />"); // No need to delete the directories.
			} else {
				// Check if the file name matches the required template fiel name
				if (strripos($file, $template_file . '.php') == (strlen($file) - strlen($template_file . '.php'))) {
					$compiled_file = $path . $file;
				}
			}
		}
	}
	@closedir($mydir);
	return $compiled_file;
}

/** Function to carry out all the necessary actions after migration */
function perform_post_migration_activities() {
	//After applying all the DB Changes,Here we clear the Smarty cache files
	clear_smarty_cache();
	//Writing tab data in flat file
	create_tab_data_file();
	create_parenttab_data_file();
}

/** Function To create Email template variables dynamically -- Pavani */
function getEmailTemplateVariables() {
	global $adb;
	$modules_list = array('Accounts', 'Contacts', 'Leads', 'Users');

	foreach ($modules_list as $index => $module) {
		if ($module == 'Calendar') {
			$focus = new Activity();
		} else {
			$focus = new $module();
		}
		$field = array();
		$tabid = getTabid($module);
		//many to many relation information field campaignrelstatus(this is the column name of the
		//field) has block set to '0', which should be ignored.
		$result = $adb->pquery("select fieldlabel,columnname,displaytype from vtiger_field where tabid=? and vtiger_field.presence in (0,2) and displaytype in (1,2,3) and block !=0", array($tabid));
		$norows = $adb->num_rows($result);
		if ($norows > 0) {
			for ($i = 0; $i < $norows; $i++) {
				$field = $adb->query_result($result, $i, 'fieldlabel');
				$columnname = $adb->query_result($result, $i, 'columnname');
				if ($columnname == 'support_start_date' || $columnname == 'support_end_date') {
					$tabname = 'vtiger_customerdetails';
				}
				$option = array(getTranslatedString($module) . ': ' . getTranslatedString($adb->query_result($result, $i, 'fieldlabel')), "$" . strtolower($module) . "-" . $columnname . "$");
				$allFields[] = $option;
			}
		}

		$allOptions[] = $allFields;
		$allFields = "";
	}
	$option = array('Current Date', '$custom-currentdate$');
	$allFields[] = $option;
	$option = array('Current Time', '$custom-currenttime$');
	$allFields[] = $option;
	$allOptions[] = $allFields;
	return $allOptions;
}

/** Function to get picklist values for the given field that are accessible for the given role.
 *  @ param $tablename picklist fieldname.
 *  It gets the picklist values for the given fieldname
 *  	$fldVal = Array(0=>value,1=>value1,-------------,n=>valuen)
 *  @return Array of picklist values accessible by the user.
 */
function getPickListValues($tablename, $roleid) {
	global $adb;
	$query = "select $tablename from vtiger_$tablename inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$tablename.picklist_valueid where roleid=? and picklistid in (select picklistid from vtiger_picklist) order by sortid";
	$result = $adb->pquery($query, array($roleid));
	$fldVal = Array();
	while ($row = $adb->fetch_array($result)) {
		$fldVal [] = $row[$tablename];
	}
	return $fldVal;
}

/** Function to check the file access is made within web root directory and whether it is not from unsafe directories */
function checkFileAccessForInclusion($filepath) {
	global $root_directory;
	// Set the base directory to compare with
	$use_root_directory = $root_directory;
	if (empty($use_root_directory)) {
		$use_root_directory = realpath(dirname(__FILE__) . '/../../.');
	}

	$unsafeDirectories = array('storage', 'cache', 'test');

	$realfilepath = realpath($filepath);

	/** Replace all \\ with \ first */
	$realfilepath = str_replace('\\\\', '\\', $realfilepath);
	$rootdirpath = str_replace('\\\\', '\\', $use_root_directory);

	/** Replace all \ with / now */
	$realfilepath = str_replace('\\', '/', $realfilepath);
	$rootdirpath = str_replace('\\', '/', $rootdirpath);

	$relativeFilePath = str_replace($rootdirpath, '', $realfilepath);
	$filePathParts = explode('/', $relativeFilePath);

	if (stripos($realfilepath, $rootdirpath) !== 0 || in_array($filePathParts[0], $unsafeDirectories)) {
		die("Sorry! Attempt to access restricted file.");
	}
}

/** Function to check the file access is made within web root directory. */
function checkFileAccess($filepath) {
	if (!isFileAccessible($filepath)) {
		die("Sorry! Attempt to access restricted file.");
	}
}

/**
 * function to return whether the file access is made within vtiger root directory
 * and it exists.
 * @global String $root_directory vtiger root directory as given in config.inc.php file.
 * @param String $filepath relative path to the file which need to be verified
 * @return Boolean true if file is a valid file within vtiger root directory, false otherwise.
 */
function isFileAccessible($filepath) {
	global $root_directory;
	// Set the base directory to compare with
	$use_root_directory = $root_directory;
	if (empty($use_root_directory)) {
		$use_root_directory = realpath(dirname(__FILE__) . '/../../.');
	}

	$realfilepath = realpath($filepath);

	/** Replace all \\ with \ first */
	$realfilepath = str_replace('\\\\', '\\', $realfilepath);
	$rootdirpath = str_replace('\\\\', '\\', $use_root_directory);

	/** Replace all \ with / now */
	$realfilepath = str_replace('\\', '/', $realfilepath);
	$rootdirpath = str_replace('\\', '/', $rootdirpath);

	if (stripos($realfilepath, $rootdirpath) !== 0) {
		return false;
	}
	return true;
}

/** Function to get the ActivityType for the given entity id
 *  @param entityid : Type Integer
 *  return the activity type for the given id
 */
function getActivityType($id) {
	global $adb;
	$quer = "select activitytype from vtiger_activity where activityid=?";
	$res = $adb->pquery($quer, array($id));
	$acti_type = $adb->query_result($res, 0, "activitytype");
	return $acti_type;
}

/** Function to get owner name either user or group */
function getOwnerName($id) {
	global $adb, $log;
	$log->debug("Entering getOwnerName(" . $id . ") method ...");
	$log->info("in getOwnerName " . $id);

	$ownerList = getOwnerNameList(array($id));
	return $ownerList[$id];
}

/** Function to get owner name either user or group */
function getOwnerNameList($idList) {
	global $log;

	if (!is_array($idList) || count($idList) == 0) {
		return array();
	}

	$nameList = array();
	$db = PearDatabase::getInstance();
	$displayValueArray = getEntityName('Users', $idList);
	if (!empty($displayValueArray)) {
		foreach ($displayValueArray as $key => $value) {
			$nameList[$key] = $value;
		}
	}
	$groupIdList = array_diff($idList, array_keys($nameList));
	if (count($groupIdList) > 0) {
		$sql = "select groupname,groupid from vtiger_groups where groupid in (" .
				generateQuestionMarks($groupIdList) . ")";
		$result = $db->pquery($sql, $groupIdList);
		$it = new SqlResultIterator($db, $result);
		foreach ($it as $row) {
			$nameList[$row->groupid] = $row->groupname;
		}
	}
	return $nameList;
}

/**
 * This function is used to get the blockid of the settings block for a given label.
 * @param $label - settings label
 * @return string type value
 */
function getSettingsBlockId($label) {
	global $log, $adb;
	$log->debug("Entering getSettingsBlockId(" . $label . ") method ...");

	$blockid = '';
	$query = "select blockid from vtiger_settings_blocks where label = ?";
	$result = $adb->pquery($query, array($label));
	$noofrows = $adb->num_rows($result);
	if ($noofrows == 1) {
		$blockid = $adb->query_result($result, 0, "blockid");
	}
	$log->debug("Exiting getSettingsBlockId method ...");
	return $blockid;
}

// Function to check if the logged in user is admin
// and if the module is an entity module
// and the module has a Settings.php file within it
function isModuleSettingPermitted($module) {
	if (file_exists("modules/$module/Settings.php") &&
			isPermitted('Settings', 'index', '') == 'yes') {

		return 'yes';
	}
	return 'no';
}

/**
 * this function returns the entity field name for a given module; for e.g. for Contacts module it return concat(lastname, ' ', firstname)
 * @param string $module - the module name
 * @return string $fieldsname - the entity field name for the module
 */
function getEntityField($module) {
	global $adb;
	$data = array();
	if (!empty($module)) {
		$query = "select fieldname,tablename,entityidfield from vtiger_entityname where modulename = ?";
		$result = $adb->pquery($query, array($module));
		$fieldsname = $adb->query_result($result, 0, 'fieldname');
		$tablename = $adb->query_result($result, 0, 'tablename');
		$entityidfield = $adb->query_result($result, 0, 'entityidfield');
		if (!(strpos($fieldsname, ',') === false)) {
			$fieldlists = explode(',', $fieldsname);
			$fieldsname = "concat(";
			$fieldsname = $fieldsname . implode(",' ',", $fieldlists);
			$fieldsname = $fieldsname . ")";
		}
	}
	$data = array("tablename" => $tablename, "fieldname" => $fieldsname);
	return $data;
}

/**
 * this function returns the entity information for a given module; for e.g. for Contacts module
 * it returns the information of tablename, modulename, fieldsname and id gets from vtiger_entityname
 * @param string $module - the module name
 * @return array $data - the entity information for the module
 */
function getEntityFieldNames($module) {
	$adb = PearDatabase::getInstance();
	$data = array();
	if (!empty($module)) {
		$query = "select fieldname,modulename,tablename,entityidfield from vtiger_entityname where modulename = ?";
		$result = $adb->pquery($query, array($module));
		$fieldsName = $adb->query_result($result, 0, 'fieldname');
		$tableName = $adb->query_result($result, 0, 'tablename');
		$entityIdField = $adb->query_result($result, 0, 'entityidfield');
		$moduleName = $adb->query_result($result, 0, 'modulename');
		if (!(strpos($fieldsName, ',') === false)) {
			$fieldsName = explode(',', $fieldsName);
		}
	}
	$data = array("tablename" => $tableName, "modulename" => $moduleName, "fieldname" => $fieldsName, "entityidfield" => $entityIdField);
	return $data;
}

/**
 * this function returns the fieldsname and its values in a array for the given ids
 * @param1 array $entity_field_info - field information having modulename, tablename, fieldname, recordid
 * @param2 array $ids_list - record ids
 * @return array $entity_info - array of fieldname and its value with key as record id
 */
function getEntityFieldValues($entity_field_info, $ids_list) {
	global $adb;
	$tableName = $entity_field_info['tablename'];
	$fieldsName = $entity_field_info['fieldname'];
	$moduleName = $entity_field_info['modulename'];
	$entityIdField = $entity_field_info['entityidfield'];
	if (is_array($fieldsName)) {
		$fieldsNameString = implode(",", $fieldsName);
	} else {
		$fieldsNameString = $fieldsName;
	}
	$query1 = "SELECT $fieldsNameString,$entityIdField FROM $tableName WHERE " .
				"$entityIdField IN (" . generateQuestionMarks($ids_list) . ")";
	if(is_array($ids_list)) {
		$params1 = $ids_list;
	} else {
		$params1 = array($ids_list);
	}
	$result = $adb->pquery($query1, $params1);
	$numrows = $adb->num_rows($result);
	$entity_info = array();
	for ($i = 0; $i < $numrows; $i++) {
		if(is_array($fieldsName)) {
			for($j = 0; $j < count($fieldsName); $j++)  {
				$entity_id = $adb->query_result($result, $i, $entityIdField);
				$entity_info[$i][$entity_id][$fieldsName[$j]] = $adb->query_result($result, $i, $fieldsName[$j]);
			}
		} else {
			$entity_id = $adb->query_result($result, $i, $entityIdField);
			$entity_info[$i][$entity_id][$fieldsName] = $adb->query_result($result, $i, $fieldsName);
		}
	}
	return $entity_info;
}

/**
 * this function returns the entity field name for a given module; for e.g. for Contacts module it return concat(lastname, ' ', firstname)
 * @param1 $module - name of the module
 * @param2 $fieldsName - fieldname with respect to module (ex : 'Accounts' - 'accountname', 'Contacts' - 'lastname','firstname')
 * @param3 $fieldValues - array of fieldname and its value
 * @return string $fieldConcatName - the entity field name for the module
 */
function getEntityFieldNameDisplay($module, $fieldsName, $fieldValues) {
	global $current_user;
	if(!is_array($fieldsName)) {
		return $fieldValues[$fieldsName];
	} else {
		$accessibleFieldNames = array();
		foreach($fieldsName as $field) {
			if($module == 'Users' || getColumnVisibilityPermission($current_user->id, $field, $module) == '0') {
				$accessibleFieldNames[] = $fieldValues[$field];
			}
		}
		if(count($accessibleFieldNames) > 0) {
			return implode(' ', $accessibleFieldNames);
		}
	}
	return '';
}

// vtiger cache utility
require_once('include/utils/VTCacheUtils.php');

// vtlib customization: Extended vtiger CRM utlitiy functions
require_once('include/utils/VtlibUtils.php');

// END

function vt_suppressHTMLTags($string) {
	return preg_replace(array('/</', '/>/', '/"/'), array('&lt;', '&gt;', '&quot;'), $string);
}

function vt_hasRTE() {
	global $FCKEDITOR_DISPLAY, $USE_RTE;
	return ((!empty($FCKEDITOR_DISPLAY) && $FCKEDITOR_DISPLAY == 'true') ||
			(!empty($USE_RTE) && $USE_RTE == 'true'));
}

function getNameInDisplayFormat($input, $dispFormat = "lf") {
	if (empty($dispFormat)) {
		$dispFormat = "lf";
	}
	$dispFormat = strtolower($dispFormat);
	$formattedNameList = array();
	$strLength = strlen($dispFormat);
	for ($i = 0; $i < $strLength; $i++) {
		$pos = strpos($dispFormat, $dispFormat[$i]);
		if ($pos !== false) {
			$formattedNameList[$pos] = $input[$dispFormat[$i]];
		}
	}
	return $formattedNameList;
}

function concatNamesSql($string) {
	$sqlString = "CONCAT(" . $string . ")";
	return $sqlString;
}

function joinName($input, $glue = ' ') {
	$displayName = implode($glue, $input);
	return $displayName;
}

function getSqlForNameInDisplayFormat($input, $module, $glue = ' ') {
	$entity_field_info = getEntityFieldNames($module);
	$tableName = $entity_field_info['tablename'];
	$fieldsName = $entity_field_info['fieldname'];
	if(is_array($fieldsName)) {
		foreach($fieldsName as $key => $value) {
			$formattedNameList[] = $input[$value];
		}
		$formattedNameListString = implode(",'" . $glue . "',", $formattedNameList);
	} else {
		$formattedNameListString = $input[$fieldsName];
	}
	$sqlString = concatNamesSql($formattedNameListString);
	return $sqlString;
}

function getModuleSequenceNumber($module, $recordId) {
	global $adb;
	switch ($module) {
		case "Invoice":
			$res = $adb->query("SELECT invoice_no FROM vtiger_invoice WHERE invoiceid = $recordId");
			$moduleSeqNo = $adb->query_result($res, 0, 'invoice_no');
			break;
		case "PurchaseOrder":
			$res = $adb->query("SELECT purchaseorder_no FROM vtiger_purchaseorder WHERE purchaseorderid = $recordId");
			$moduleSeqNo = $adb->query_result($res, 0, 'purchaseorder_no');
			break;
		case "Quotes":
			$res = $adb->query("SELECT quote_no FROM vtiger_quotes WHERE quoteid = $recordId");
			$moduleSeqNo = $adb->query_result($res, 0, 'quote_no');
			break;
		case "SalesOrder":
			$res = $adb->query("SELECT salesorder_no FROM vtiger_salesorder WHERE salesorderid = $recordId");
			$moduleSeqNo = $adb->query_result($res, 0, 'salesorder_no');
			break;
	}
	return $moduleSeqNo;
}
function getAllMenuModules() {
	global $adb;
	global $current_user;
	$resultant_array = Array();
	$query = 'select tabid,name,tablabel,tabsequence,parent from vtiger_tab where parent is not null and parent!=" " and presence in (0,2) order by tabsequence';
	$result = $adb->pquery($query, array());
	for ($i = 0; $i < $adb->num_rows($result); $i++) {
		$moduleName = $adb->query_result($result, $i, 'name');
		$moduleLabel = $adb->query_result($result, $i, 'tablabel');
		$tabid = $adb->query_result($result, $i, 'tabid');
		$parent = $adb->query_result($result, $i, 'parent');
		if (is_admin($current_user)) {
			$resultant_array[] = Array($moduleName, $moduleLabel, $tabid, $parent);
		}
	}
	return $resultant_array;
}

function getTopMenuModules() {
	global $adb;
	global $current_user;
	$resultant_array = Array();

	$query = 'select tabid,name,tablabel,tabsequence,parent from vtiger_tab where parent is not null and presence in (0,2) and tabsequence != -1 order by tabsequence';
	$result = $adb->pquery($query, array());
	for ($i = 0; $i < $adb->num_rows($result); $i++) {
		$moduleName = $adb->query_result($result, $i, 'name');
		$moduleLabel = $adb->query_result($result, $i, 'tablabel');
		$tabid = $adb->query_result($result, $i, 'tabid');
		$parent = $adb->query_result($result, $i, 'parent');

		if (is_admin($current_user)) {
			$resultant_array[] = Array($moduleName, $moduleLabel, $tabid, $parent);
		}
	}
	return $resultant_array;
}

function getMenuStructure($selectModule = '') {

	global $adb;
	global $current_user;
	$selectedModule = Array();
	$moreModule = Array();
	$tempModule = Array();
	$tempSelected = Array();
	$tempMore = Array();
	$resultant_array = Array();
	$notallowed = -1;
	$query = 'select tabid,name,tablabel,tabsequence,parent from vtiger_tab where parent is not null and parent!=" " and presence in (0,2) order by tabsequence';

	$result = $adb->pquery($query, array());
	require('user_privileges/user_privileges_' . $current_user->id . '.php');

	for ($i = 0; $i < $adb->num_rows($result); $i++) {
		$moduleName = $adb->query_result($result, $i, 'name');
		$moduleLabel = $adb->query_result($result, $i, 'tablabel');
		$tabid = $adb->query_result($result, $i, 'tabid');
		$parent = $adb->query_result($result, $i, 'parent');
		$tabsequence = $adb->query_result($result, $i, 'tabsequence');
		if (is_admin($current_user) || $profileGlobalPermission[2] == 0 || $profileGlobalPermission[1] == 0 || $profileTabsPermission[$tabid] === 0) {
			if ($tabsequence != $notallowed && count($selectedModule) <= 9) {
				if (!(in_array($moduleName, $tempModule))) {
					$selectedModule[] = Array($moduleName, $moduleLabel, $tabid, $parent);
					$tempSelected[] = $moduleName;
					$tempModule[] = $moduleName;
				}
			}

			if (!(in_array($moduleName, $tempModule))) {
				$moreModule[$parent][] = Array($moduleName, $moduleLabel, $tabid, $parent);
				$tempModule[] = $moduleName;
				$tempMore[] = $moduleName;
			}
		}
	}

	if (!in_array($selectModule, $tempSelected)) {
		if ((in_array($selectModule, $tempMore))) {
			array_push($selectedModule, Array($selectModule, $selectModule));
		}
	}

	$resultant_array['top'] = $selectedModule;
	$resultant_array['more'] = $moreModule;
	return $resultant_array;
}


function getReturnPath($host){
	$arr=explode('.',$host);
	unset($arr[0]);
	$domain=implode('.',$arr);
	$Return_Path='noreply@'.$domain;
	return $Return_Path;
}
?>