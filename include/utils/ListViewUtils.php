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
 * $Header: /cvsroot/vtigercrm/vtiger_crm/include/utils/ListViewUtils.php,v 1.32 2006/02/03 06:53:08 mangai Exp $
 * Description:  Includes generic helper functions used throughout the application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php'); //new
require_once('include/utils/CommonUtils.php'); //new
require_once('user_privileges/default_module_view.php'); //new
require_once('include/utils/UserInfoUtil.php');
require_once('include/Zend/Json.php');

/* * This function is used to get the list view header values in a list view
 * Param $focus - module object
 * Param $module - module name
 * Param $sort_qry - sort by value
 * Param $sorder - sorting order (asc/desc)
 * Param $order_by - order by
 * Param $relatedlist - flag to check whether the header is for listvie or related list
 * Param $oCv - Custom view object
 * Returns the listview header values in an array
 */

function getListViewHeader($focus, $module, $sort_qry = '', $sorder = '', $order_by = '', $relatedlist = '', $oCv = '', $relatedmodule = '', $skipActions = false) {
	global $log, $singlepane_view;
	$log->debug("Entering getListViewHeader(" . $module . "," . $sort_qry . "," . $sorder . "," . $order_by . "," . $relatedlist . "," . (is_object($oCv) ? get_class($oCv) : $oCv) . ") method ...");
	global $adb;
	global $theme;
	global $app_strings;
	global $mod_strings;
	global $counter;

	$arrow = '';
	$qry = getURLstring($focus);
	$theme_path = "themes/" . $theme . "/";
	$image_path = $theme_path . "images/";
	$list_header = Array();

	//Get the vtiger_tabid of the module
	$tabid = getTabid($module);
	$tabname = getParentTab();
	global $current_user;
	//added for vtiger_customview 27/5
	if ($oCv) {
		if (isset($oCv->list_fields)) {
			$focus->list_fields = $oCv->list_fields;
		}
	}

	// Remove fields which are made inactive
	$focus->filterInactiveFields($module);

	//Added to reduce the no. of queries logging for non-admin user -- by Minnie-start
	$field_list = array();
	$j = 0;
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	foreach ($focus->list_fields as $name => $tableinfo) {
		$fieldname = $focus->list_fields_name[$name];
		if ($oCv) {
			if (isset($oCv->list_fields_name)) {
				$fieldname = $oCv->list_fields_name[$name];
			}
		}
		if ($fieldname == 'accountname' && $module != 'Accounts') {
			$fieldname = 'account_id';
		}
		if ($fieldname == 'lastname' && ($module == 'SalesOrder' || $module == 'PurchaseOrder' || $module == 'Invoice' || $module == 'Quotes' || $module == 'Calendar' )) {
			$fieldname = 'contact_id';
		}
		if ($fieldname == 'productname' && $module != 'Products') {
			$fieldname = 'product_id';
		}
		array_push($field_list, $fieldname);
		$j++;
	}
	$field = Array();
	if ($is_admin == false) {
		if ($module == 'Emails') {
			$query = "SELECT fieldname FROM vtiger_field WHERE tabid = ? and vtiger_field.presence in (0,2)";
			$params = array($tabid);
		} else {
			$profileList = getCurrentUserProfileList();
			$params = array();

			$query = "SELECT DISTINCT vtiger_field.fieldname
				FROM vtiger_field
				INNER JOIN vtiger_profile2field
					ON vtiger_profile2field.fieldid = vtiger_field.fieldid
				INNER JOIN vtiger_def_org_field
					ON vtiger_def_org_field.fieldid = vtiger_field.fieldid";
			if ($module == "Calendar") {
				$query .=" WHERE vtiger_field.tabid in (9,16) and vtiger_field.presence in (0,2)";
			} else {
				$query .=" WHERE vtiger_field.tabid = ? and vtiger_field.presence in (0,2)";
				array_push($params, $tabid);
			}

			$query.=" AND vtiger_profile2field.visible = 0
				AND vtiger_def_org_field.visible = 0
				AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ")
				AND vtiger_field.fieldname IN (" . generateQuestionMarks($field_list) . ")";

			array_push($params, $profileList, $field_list);
		}
		$result = $adb->pquery($query, $params);
		for ($k = 0; $k < $adb->num_rows($result); $k++) {
			$field[] = $adb->query_result($result, $k, "fieldname");
		}
	}
	//end
	//modified for vtiger_customview 27/5 - $app_strings change to $mod_strings
	foreach ($focus->list_fields as $name => $tableinfo) {
		//added for vtiger_customview 27/5
		if ($oCv) {
			if (isset($oCv->list_fields_name)) {
				$fieldname = $oCv->list_fields_name[$name];
				if ($fieldname == 'accountname' && $module != 'Accounts') {
					$fieldname = 'account_id';
				}
				if ($fieldname == 'lastname' && ($module == 'SalesOrder' || $module == 'PurchaseOrder' || $module == 'Invoice' || $module == 'Quotes' || $module == 'Calendar')) {
					$fieldname = 'contact_id';
				}
				if ($fieldname == 'productname' && $module != 'Products') {
					$fieldname = 'product_id';
				}
			} else {
				$fieldname = $focus->list_fields_name[$name];
			}
		} else {
			$fieldname = $focus->list_fields_name[$name];
			if ($fieldname == 'accountname' && $module != 'Accounts') {
				$fieldname = 'account_id';
			}
			if ($fieldname == 'lastname' && ($module == 'SalesOrder' || $module == 'PurchaseOrder' || $module == 'Invoice' || $module == 'Quotes' || $module == 'Calendar')) {
				$fieldname = 'contact_id';
			}
			if ($fieldname == 'productname' && $module != 'Products') {
				$fieldname = 'product_id';
			}
		}
		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || in_array($fieldname, $field) || $fieldname == '' || ($name == 'Close' && $module == 'Calendar')) {
			if (isset($focus->sortby_fields) && $focus->sortby_fields != '') {
				//Added on 14-12-2005 to avoid if and else check for every list vtiger_field for arrow image and change order
				$change_sorder = array('ASC' => 'DESC', 'DESC' => 'ASC');
				$arrow_gif = array('ASC' => 'arrow_down.gif', 'DESC' => 'arrow_up.gif');
				foreach ($focus->list_fields[$name] as $tab => $col) {
					if (in_array($col, $focus->sortby_fields)) {
						if ($order_by == $col) {
							$temp_sorder = $change_sorder[$sorder];
							$arrow = "&nbsp;<img src ='" . vtiger_imageurl($arrow_gif[$sorder], $theme) . "' border='0'>";
						} else {
							$temp_sorder = 'ASC';
						}
						$lbl_name = getTranslatedString(decode_html($name), $module);
						//added to display vtiger_currency symbol in listview header
						if ($lbl_name == 'Amount') {
							$lbl_name .=' (' . $app_strings['LBL_IN'] . ' ' . $user_info['currency_symbol'] . ')';
						}
						if ($relatedlist != '' && $relatedlist != 'global') {
							$relationURL = '';
							if (!empty($_REQUEST['relation_id'])) {
								$relationURL = '&relation_id=' . vtlib_purify(
												$_REQUEST['relation_id']);
							}
							$actionsURL = '';
							if (!empty($_REQUEST['actions'])) {
								$actionsURL = '&actions=' . vtlib_purify($_REQUEST['actions']);
							}
							if (empty($_REQUEST['header'])) {
								$moduleLabel = getTranslatedString($module, $module);
							} else {
								$moduleLabel = $_REQUEST['header'];
							}
							$moduleLabel = str_replace(' ', '', $moduleLabel);
							$name = "<a href='javascript:void(0);' onClick='loadRelatedListBlock" .
									"(\"module=$relatedmodule&action=" . $relatedmodule . "Ajax&" .
									"file=DetailViewAjax&ajxaction=LOADRELATEDLIST&header=" . $moduleLabel .
									"&order_by=$col&record=$relatedlist&sorder=$temp_sorder$relationURL" .
									"$actionsURL\",\"tbl_" . $relatedmodule . "_$moduleLabel\"," .
									"\"$relatedmodule" . "_$moduleLabel\");' class='listFormHeaderLinks'>" . $lbl_name . "" . $arrow . "</a>";
						} elseif ($module == 'Users' && $name == 'User Name')
							$name = "<a href='javascript:;' onClick='getListViewEntries_js(\"" . $module . "\",\"parenttab=" . $tabname . "&order_by=" . $col . "&start=1&sorder=" . $temp_sorder . "" . $sort_qry . "\");' class='listFormHeaderLinks'>" . getTranslatedString('LBL_LIST_USER_NAME_ROLE', $module) . "" . $arrow . "</a>";
						elseif ($relatedlist == "global")
							$name = $lbl_name;
						else
							$name = "<a href='javascript:;' onClick='getListViewEntries_js(\"" . $module . "\",\"parenttab=" . $tabname . "&order_by=" . $col . "&start=1&sorder=" . $temp_sorder . "" . $sort_qry . "\");' class='listFormHeaderLinks'>" . $lbl_name . "" . $arrow . "</a>";
						$arrow = '';
					}
					else {
						if (stripos($col, 'cf_') === 0) {
							$tablenameArray = array_keys($tableinfo, $col);
							$tablename = $tablenameArray[0];
							$cf_columns = $adb->getColumnNames($tablename);
							if (array_search($col, $cf_columns) != null) {
								$pquery = "select fieldlabel,typeofdata from vtiger_field where tablename = ? and fieldname = ? and vtiger_field.presence in (0,2)";
								$cf_res = $adb->pquery($pquery, array($tablename, $col));
								if (count($cf_res) > 0) {
									$cf_fld_label = $adb->query_result($cf_res, 0, "fieldlabel");
									$typeofdata = explode("~", $adb->query_result($cf_res, 0, "typeofdata"));
									$new_field_label = $tablename . ":" . $col . ":" . $col . ":" . $module . "_" . str_replace(" ", "_", $cf_fld_label) . ":" . $typeofdata[0];
									$name = $cf_fld_label;

									// Update the existing field name in the database with new field name.
									$upd_query = "update vtiger_cvcolumnlist set columnname = ? where columnname like '" . $tablename . ":" . $col . ":" . $col . "%'";
									$upd_params = array($new_field_label);
									$adb->pquery($upd_query, $upd_params);
								}
							}
						} else {
							$name = getTranslatedString($name, $module);
						}
					}
				}
			}
			//added to display vtiger_currency symbol in related listview header
			if ($name == 'Amount' && $relatedlist != '') {
				$name .=' (' . $app_strings['LBL_IN'] . ' ' . $user_info['currency_symbol'] . ')';
			}

			if ($module == "Calendar" && $name == $app_strings['Close']) {
				if (isPermitted("Calendar", "EditView") == 'yes') {
					if ((getFieldVisibilityPermission('Events', $current_user->id, 'eventstatus') == '0') || (getFieldVisibilityPermission('Calendar', $current_user->id, 'taskstatus') == '0')) {
						array_push($list_header, $name);
					}
				}
			} else {
				$list_header[] = $name;
			}
		}
	}

	//Added for Action - edit and delete link header in listview
	if (!$skipActions && (isPermitted($module, "EditView", "") == 'yes' || isPermitted($module, "Delete", "") == 'yes'))
		$list_header[] = $app_strings["LBL_ACTION"];

	$log->debug("Exiting getListViewHeader method ...");
	return $list_header;
}

/* * This function is used to get the list view header in popup
 * Param $focus - module object
 * Param $module - module name
 * Param $sort_qry - sort by value
 * Param $sorder - sorting order (asc/desc)
 * Param $order_by - order by
 * Returns the listview header values in an array
 */

function getSearchListViewHeader($focus, $module, $sort_qry = '', $sorder = '', $order_by = '') {
	global $log;
	$log->debug("Entering getSearchListViewHeader(" . get_class($focus) . "," . $module . "," . $sort_qry . "," . $sorder . "," . $order_by . ") method ...");
	global $adb;
	global $theme;
	global $app_strings;
	global $mod_strings, $current_user;
	$arrow = '';
	$list_header = Array();
	$tabid = getTabid($module);
	if (isset($_REQUEST['task_relmod_id'])) {
		$task_relmod_id = vtlib_purify($_REQUEST['task_relmod_id']);
		$pass_url .="&task_relmod_id=" . $task_relmod_id;
	}
	if (isset($_REQUEST['relmod_id'])) {
		$relmod_id = vtlib_purify($_REQUEST['relmod_id']);
		$pass_url .="&relmod_id=" . $relmod_id;
	}
	if (isset($_REQUEST['task_parent_module'])) {
		$task_parent_module = vtlib_purify($_REQUEST['task_parent_module']);
		$pass_url .="&task_parent_module=" . $task_parent_module;
	}
	if (isset($_REQUEST['parent_module'])) {
		$parent_module = vtlib_purify($_REQUEST['parent_module']);
		$pass_url .="&parent_module=" . $parent_module;
	}
	if (isset($_REQUEST['fromPotential']) && (isset($_REQUEST['acc_id']) && $_REQUEST['acc_id'] != '')) {
		$pass_url .="&parent_module=Accounts&relmod_id=" . vtlib_purify($_REQUEST['acc_id']);
	}

	// vtlib Customization : For uitype 10 popup during paging
	if ($_REQUEST['form'] == 'vtlibPopupView') {
		$pass_url .= '&form=vtlibPopupView&forfield=' . vtlib_purify($_REQUEST['forfield']) . '&srcmodule=' . vtlib_purify($_REQUEST['srcmodule']) . '&forrecord=' . vtlib_purify($_REQUEST['forrecord']);
	}
	// END
	//Added to reduce the no. of queries logging for non-admin user -- by Minnie-start
	$field_list = array();
	$j = 0;
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	foreach ($focus->search_fields as $name => $tableinfo) {
		$fieldname = $focus->search_fields_name[$name];
		array_push($field_list, $fieldname);
		$j++;
	}
	$field = Array();
	if ($is_admin == false && $module != 'Users') {
		if ($module == 'Emails') {
			$query = "SELECT fieldname FROM vtiger_field WHERE tabid = ? and vtiger_field.presence in (0,2)";
			$params = array($tabid);
		} else {
			$profileList = getCurrentUserProfileList();
			$query = "SELECT DISTINCT vtiger_field.fieldname
				FROM vtiger_field
				INNER JOIN vtiger_profile2field
					ON vtiger_profile2field.fieldid = vtiger_field.fieldid
				INNER JOIN vtiger_def_org_field
					ON vtiger_def_org_field.fieldid = vtiger_field.fieldid
				WHERE vtiger_field.tabid = ?
				AND vtiger_profile2field.visible=0
				AND vtiger_def_org_field.visible=0
				AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ")
				AND vtiger_field.fieldname IN (" . generateQuestionMarks($field_list) . ") and vtiger_field.presence in (0,2)";

			$params = array($tabid, $profileList, $field_list);
		}

		$result = $adb->pquery($query, $params);
		for ($k = 0; $k < $adb->num_rows($result); $k++) {
			$field[] = $adb->query_result($result, $k, "fieldname");
		}
	}
	//end
	$theme_path = "themes/" . $theme . "/";
	$image_path = $theme_path . "images/";


	$focus->filterInactiveFields($module);

	foreach ($focus->search_fields as $name => $tableinfo) {
		$fieldname = $focus->search_fields_name[$name];
		$tabid = getTabid($module);

		global $current_user;
		require('user_privileges/user_privileges_' . $current_user->id . '.php');
		if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || in_array($fieldname, $field) || $module == 'Users') {

			if (isset($focus->sortby_fields) && $focus->sortby_fields != '') {
				foreach ($focus->search_fields[$name] as $tab => $col) {
					if (in_array($col, $focus->sortby_fields)) {
						if ($order_by == $col) {
							if ($sorder == 'ASC') {
								$sorder = "DESC";
								$arrow = "<img src ='" . vtiger_imageurl('arrow_down.gif', $theme) . "' border='0'>";
							} else {
								$sorder = 'ASC';
								$arrow = "<img src ='" . vtiger_imageurl('arrow_up.gif', $theme) . "' border='0'>";
							}
						}
						// vtlib customization: If translation is not available use the given name
						$tr_name = getTranslatedString($name, $module);
						$name = "<a href='javascript:;' onClick=\"getListViewSorted_js('" . $module . "','" . $sort_qry . $pass_url . "&order_by=" . $col . "&sorder=" . $sorder . "')\" class='listFormHeaderLinks'>" . $tr_name . "&nbsp;" . $arrow . "</a>";
						// END
						$arrow = '';
					} else {
						// vtlib customization: If translation is not available use the given name
						$tr_name = getTranslatedString($name, $module);
						$name = $tr_name;
						// END
					}
				}
			}
			$list_header[] = $name;
		}
	}
	$log->debug("Exiting getSearchListViewHeader method ...");
	return $list_header;
}

/* * This function generates the navigation array in a listview
 * Param $display - start value of the navigation
 * Param $noofrows - no of records
 * Param $limit - no of entries per page
 * Returns an array type
 */

//code contributed by raju for improved pagination
function getNavigationValues($display, $noofrows, $limit) {
	global $log;
	$log->debug("Entering getNavigationValues(" . $display . "," . $noofrows . "," . $limit . ") method ...");
	$navigation_array = Array();
	global $limitpage_navigation;
	if (isset($_REQUEST['allflag']) && $_REQUEST['allflag'] == 'All') {
		$navigation_array['start'] = 1;
		$navigation_array['first'] = 1;
		$navigation_array['end'] = 1;
		$navigation_array['prev'] = 0;
		$navigation_array['next'] = 0;
		$navigation_array['end_val'] = $noofrows;
		$navigation_array['current'] = 1;
		$navigation_array['allflag'] = 'Normal';
		$navigation_array['verylast'] = 1;
		$log->debug("Exiting getNavigationValues method ...");
		return $navigation_array;
	}
	if ($noofrows != 0) {
		if (((($display * $limit) - $limit) + 1) > $noofrows) {
			$display = floor($noofrows / $limit);
		}
		$start = ((($display * $limit) - $limit) + 1);
	} else {
		$start = 0;
	}

	$end = $start + ($limit - 1);
	if ($end > $noofrows) {
		$end = $noofrows;
	}
	$paging = ceil($noofrows / $limit);
	// Display the navigation
	if ($display > 1) {
		$previous = $display - 1;
	} else {
		$previous = 0;
	}
	if ($noofrows < $limit) {
		$first = '';
	} elseif ($noofrows != $limit) {
		$last = $paging;
		$first = 1;
		if ($paging > $limitpage_navigation) {
			$first = $display - floor(($limitpage_navigation / 2));
			if ($first < 1)
				$first = 1;
			$last = ($limitpage_navigation - 1) + $first;
		}
		if ($last > $paging) {
			$first = $paging - ($limitpage_navigation - 1);
			$last = $paging;
		}
	}
	if ($display < $paging) {
		$next = $display + 1;
	} else {
		$next = 0;
	}
	$navigation_array['start'] = $start;
	$navigation_array['first'] = $first;
	$navigation_array['end'] = $last;
	$navigation_array['prev'] = $previous;
	$navigation_array['next'] = $next;
	$navigation_array['end_val'] = $end;
	$navigation_array['current'] = $display;
	$navigation_array['allflag'] = 'All';
	$navigation_array['verylast'] = $paging;
	$log->debug("Exiting getNavigationValues method ...");
	return $navigation_array;
}

//End of code contributed by raju for improved pagination

/* * This function generates the List view entries in a list view
 * Param $focus - module object
 * Param $list_result - resultset of a listview query
 * Param $navigation_array - navigation values in an array
 * Param $relatedlist - check for related list flag
 * Param $returnset - list query parameters in url string
 * Param $edit_action - Edit action value
 * Param $del_action - delete action value
 * Param $oCv - vtiger_customview object
 * Returns an array type
 */

//parameter added for vtiger_customview $oCv 27/5
function getListViewEntries($focus, $module, $list_result, $navigation_array, $relatedlist = '', $returnset = '', $edit_action = 'EditView', $del_action = 'Delete', $oCv = '', $page = '', $selectedfields = '', $contRelatedfields = '', $skipActions = false) {
	global $log;
	global $mod_strings;
	$log->debug("Entering getListViewEntries(" . get_class($focus) . "," . $module . "," . $list_result . "," . $navigation_array . "," . $relatedlist . "," . $returnset . "," . $edit_action . "," . $del_action . "," . (is_object($oCv) ? get_class($oCv) : $oCv) . ") method ...");
	$tabname = getParentTab();
	global $adb, $current_user;
	global $app_strings;
	$noofrows = $adb->num_rows($list_result);
	$list_block = Array();
	global $theme;
	$evt_status = '';
	$theme_path = "themes/" . $theme . "/";
	$image_path = $theme_path . "images/";
	//getting the vtiger_fieldtable entries from database
	$tabid = getTabid($module);

	//added for vtiger_customview 27/5
	if ($oCv) {
		if (isset($oCv->list_fields)) {
			$focus->list_fields = $oCv->list_fields;
		}
	}
	if (is_array($selectedfields) && $selectedfields != '') {
		$focus->list_fields = $selectedfields;
	}

	// Remove fields which are made inactive
	$focus->filterInactiveFields($module);

	//Added to reduce the no. of queries logging for non-admin user -- by minnie-start
	$field_list = array();
	$j = 0;
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	foreach ($focus->list_fields as $name => $tableinfo) {
		$fieldname = $focus->list_fields_name[$name];
		if ($oCv) {
			if (isset($oCv->list_fields_name)) {
				$fieldname = $oCv->list_fields_name[$name];
			}
		}
		if ($fieldname == 'accountname' && $module != 'Accounts') {
			$fieldname = 'account_id';
		}
		if ($fieldname == 'lastname' && ($module == 'SalesOrder' || $module == 'PurchaseOrder' || $module == 'Invoice' || $module == 'Quotes' || $module == 'Calendar'))
			$fieldname = 'contact_id';

		if ($fieldname == 'productname' && $module != 'Products') {
			$fieldname = 'product_id';
		}

		array_push($field_list, $fieldname);
		$j++;
	}
	$field = Array();
	if ($is_admin == false) {
		if ($module == 'Emails') {
			$query = "SELECT fieldname FROM vtiger_field WHERE tabid = ? and vtiger_field.presence in (0,2)";
			$params = array($tabid);
		} else {
			$profileList = getCurrentUserProfileList();
			$params = array();
			$query = "SELECT DISTINCT vtiger_field.fieldname
				FROM vtiger_field
				INNER JOIN vtiger_profile2field
					ON vtiger_profile2field.fieldid = vtiger_field.fieldid
				INNER JOIN vtiger_def_org_field
					ON vtiger_def_org_field.fieldid = vtiger_field.fieldid";

			if ($module == "Calendar")
				$query .=" WHERE vtiger_field.tabid in (9,16) and vtiger_field.presence in (0,2)";
			else {
				$query .=" WHERE vtiger_field.tabid = ? and vtiger_field.presence in (0,2)";
				array_push($params, $tabid);
			}

			$query .=" AND vtiger_profile2field.visible = 0
					AND vtiger_profile2field.visible = 0
					AND vtiger_def_org_field.visible = 0
					AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ")
					AND vtiger_field.fieldname IN (" . generateQuestionMarks($field_list) . ")";

			array_push($params, $profileList, $field_list);
		}

		$result = $adb->pquery($query, $params);
		for ($k = 0; $k < $adb->num_rows($result); $k++) {
			$field[] = $adb->query_result($result, $k, "fieldname");
		}
	}
	//constructing the uitype and columnname array
	$ui_col_array = Array();

	$params = array();
	$query = "SELECT uitype, columnname, fieldname FROM vtiger_field ";

	if ($module == "Calendar")
		$query .=" WHERE vtiger_field.tabid in (9,16) and vtiger_field.presence in (0,2)";
	else {
		$query .=" WHERE vtiger_field.tabid = ? and vtiger_field.presence in (0,2)";
		array_push($params, $tabid);
	}
	$query .= " AND fieldname IN (" . generateQuestionMarks($field_list) . ") ";
	array_push($params, $field_list);

	$result = $adb->pquery($query, $params);
	$num_rows = $adb->num_rows($result);
	for ($i = 0; $i < $num_rows; $i++) {
		$tempArr = array();
		$uitype = $adb->query_result($result, $i, 'uitype');
		$columnname = $adb->query_result($result, $i, 'columnname');
		$field_name = $adb->query_result($result, $i, 'fieldname');
		$tempArr[$uitype] = $columnname;
		$ui_col_array[$field_name] = $tempArr;
	}
	//end
	if ($navigation_array['start'] != 0)
		for ($i = 1; $i <= $noofrows; $i++) {
			$list_header = Array();
			//Getting the entityid
			if ($module != 'Users') {
				$entity_id = $adb->query_result($list_result, $i - 1, "crmid");
				$owner_id = $adb->query_result($list_result, $i - 1, "smownerid");
			} else {
				$entity_id = $adb->query_result($list_result, $i - 1, "id");
			}
			// Fredy Klammsteiner, 4.8.2005: changes from 4.0.1 migrated to 4.2
			// begin: Armando Lüscher 05.07.2005 -> §priority
			// Code contri buted by fredy Desc: Set Priority color
			$priority = $adb->query_result($list_result, $i - 1, "priority");

			$font_color_high = "color:#00DD00;";
			$font_color_medium = "color:#DD00DD;";
			$P_FONT_COLOR = "";
			switch ($priority) {
				case 'High':
					$P_FONT_COLOR = $font_color_high;
					break;
				case 'Medium':
					$P_FONT_COLOR = $font_color_medium;
					break;
				default:
					$P_FONT_COLOR = "";
			}
			//end: Armando Lüscher 05.07.2005 -> §priority
			foreach ($focus->list_fields as $name => $tableinfo) {
				$fieldname = $focus->list_fields_name[$name];

				//added for vtiger_customview 27/5
				if ($oCv) {
					if (isset($oCv->list_fields_name)) {
						$fieldname = $oCv->list_fields_name[$name];
						if ($fieldname == 'accountname' && $module != 'Accounts') {
							$fieldname = 'account_id';
						}
						if ($fieldname == 'lastname' && ($module == 'SalesOrder' || $module == 'PurchaseOrder' || $module == 'Invoice' || $module == 'Quotes' || $module == 'Calendar' )) {
							$fieldname = 'contact_id';
						}
						if ($fieldname == 'productname' && $module != 'Products') {
							$fieldname = 'product_id';
						}
					} else {
						$fieldname = $focus->list_fields_name[$name];
					}
				} else {
					$fieldname = $focus->list_fields_name[$name];
					if ($fieldname == 'accountname' && $module != 'Accounts') {
						$fieldname = 'account_id';
					}
					if ($fieldname == 'lastname' && ($module == 'SalesOrder' || $module == 'PurchaseOrder' || $module == 'Invoice' || $module == 'Quotes' || $module == 'Calendar')) {
						$fieldname = 'contact_id';
					}
					if ($fieldname == 'productname' && $module != 'Products') {
						$fieldname = 'product_id';
					}
				}
				if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || in_array($fieldname, $field) || $fieldname == '' || ($name == 'Close' && $module == 'Calendar')) {
					if ($fieldname == '') {
						$table_name = '';
						$column_name = '';
						foreach ($tableinfo as $tablename => $colname) {
							$table_name = $tablename;
							$column_name = $colname;
						}
						$value = $adb->query_result($list_result, $i - 1, $colname);
					} else {
						if ($module == 'Calendar') {
							$act_id = $adb->query_result($list_result, $i - 1, "activityid");
							$activitytype = $adb->query_result($list_result, $i - 1, 'activitytype');
							if (empty($activitytype)) {
								$cal_sql = "select activitytype from vtiger_activity where activityid=?";
								$cal_res = $adb->pquery($cal_sql, array($act_id));
								if ($adb->num_rows($cal_res) >= 0)
									$activitytype = $adb->query_result($cal_res, 0, "activitytype");
							}
						}
						if (($module == 'Calendar' || $module == 'Emails' || $module == 'HelpDesk' || $module == 'Invoice' || $module == 'Leads' || $module == 'Contacts') && (($fieldname == 'parent_id') || ($name == 'Contact Name') || ($name == 'Close') || ($fieldname == 'firstname'))) {
							if ($module == 'Calendar') {
								if ($fieldname == 'status') {
									if ($activitytype == 'Task') {
										$fieldname = 'taskstatus';
									} else {
										$fieldname = 'eventstatus';
									}
								}
								if ($activitytype == 'Task') {
									if (getFieldVisibilityPermission('Calendar', $current_user->id, $fieldname) == '0') {
										$has_permission = 'yes';
									} else {
										$has_permission = 'no';
									}
								} else {
									if (getFieldVisibilityPermission('Events', $current_user->id, $fieldname) == '0') {
										$has_permission = 'yes';
									} else {
										$has_permission = 'no';
									}
								}
							}
							if ($module != 'Calendar' || ($module == 'Calendar' && $has_permission == 'yes')) {
								if ($fieldname == 'parent_id') {
									$value = getRelatedTo($module, $list_result, $i - 1);
								}
								if ($name == 'Contact Name') {
									$contact_id = $adb->query_result($list_result, $i - 1, "contactid");
									$contact_name = getFullNameFromQResult($list_result, $i - 1, "Contacts");
									$value = "";
									//Added to get the contactname for activities custom view - t=2190
									if ($contact_id != '' && !empty($contact_name)) {
										$displayValueArray = getEntityName('Contacts', $contact_id);
										if (!empty($displayValueArray)) {
											foreach ($displayValueArray as $key => $field_value) {
												$contact_name = $field_value;
											}
										}
									}
									if (($contact_name != "") && ($contact_id != 'NULL')) {
										// Fredy Klammsteiner, 4.8.2005: changes from 4.0.1 migrated to 4.2
										$value = "<a href='index.php?module=Contacts&action=DetailView&parenttab=" . $tabname . "&record=" . $contact_id . "' style='" . $P_FONT_COLOR . "'>" . textlength_check($contact_name) . "</a>"; // Armando Lüscher 05.07.2005 -> §priority -> Desc: inserted style="$P_FONT_COLOR"
									}
								}
								if ($fieldname == "firstname") {
									$first_name = textlength_check($adb->query_result($list_result, $i - 1, "firstname"));

									$value = '<a href="index.php?action=DetailView&module=' . $module . '&parenttab=' . $tabname . '&record=' . $entity_id . '">' . $first_name . '</a>';
								}

								if ($name == 'Close') {
									$status = $adb->query_result($list_result, $i - 1, "status");
									$activityid = $adb->query_result($list_result, $i - 1, "activityid");
									if (empty($activityid)) {
										$activityid = $adb->query_result($list_result, $i - 1, "tmp_activity_id");
									}
									if ($activitytype != 'Task' && $activitytype != 'Emails') {
										$eventstatus = $adb->query_result($list_result, $i - 1, "eventstatus");
										if (isset($eventstatus)) {
											$status = $eventstatus;
										}
									}
									if ($status == 'Deferred' || $status == 'Completed' || $status == 'Held' || $status == '') {
										$value = "";
									} else {
										if ($activitytype == 'Task')
											$evt_status = '&status=Completed';
										else
											$evt_status = '&eventstatus=Held';
										if (isPermitted("Calendar", 'EditView', $activityid) == 'yes') {
											if ($returnset == '') {
												$returnset = '&return_module=Calendar&return_action=ListView&return_id=' . $activityid . '&return_viewname=' . $oCv->setdefaultviewid;
											}
											// Fredy Klammsteiner, 4.8.2005: changes from 4.0.1 migrated to 4.2
											$value = "<a href='index.php?action=Save&module=Calendar&record=" . $activityid . "&parenttab=" . $tabname . "&change_status=true" . $returnset . $evt_status . "&start=" . $navigation_array['current'] . "' style='" . $P_FONT_COLOR . "'>X</a>"; // Armando Lüscher 05.07.2005 -> §priority -> Desc: inserted style="$P_FONT_COLOR"
										} else {
											$value = "";
										}
									}
								}
							} else {
								$value = "";
							}
						} elseif ($module == "Documents" && ($fieldname == 'filelocationtype' || $fieldname == 'filename' || $fieldname == 'filesize' || $fieldname == 'filestatus' || $fieldname == 'filetype')) {
							$value = $adb->query_result($list_result, $i - 1, $fieldname);
							if ($fieldname == 'filelocationtype') {
								if ($value == 'I')
									$value = getTranslatedString('LBL_INTERNAL', $module);
								elseif ($value == 'E')
									$value = getTranslatedString('LBL_EXTERNAL', $module);
								else
									$value = ' --';
							}
							if ($fieldname == 'filename') {
								$downloadtype = $adb->query_result($list_result, $i - 1, 'filelocationtype');
								if ($downloadtype == 'I') {
									$fld_value = $value;
									$ext_pos = strrpos($fld_value, ".");
									$ext = substr($fld_value, $ext_pos + 1);
									$ext = strtolower($ext);
									if ($value != '') {
										if ($ext == 'bin' || $ext == 'exe' || $ext == 'rpm')
											$fileicon = "<img src='" . vtiger_imageurl('fExeBin.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
										elseif ($ext == 'jpg' || $ext == 'gif' || $ext == 'bmp')
											$fileicon = "<img src='" . vtiger_imageurl('fbImageFile.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
										elseif ($ext == 'txt' || $ext == 'doc' || $ext == 'xls')
											$fileicon = "<img src='" . vtiger_imageurl('fbTextFile.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
										elseif ($ext == 'zip' || $ext == 'gz' || $ext == 'rar')
											$fileicon = "<img src='" . vtiger_imageurl('fbZipFile.gif', $theme) . "' hspace='3' align='absmiddle'	border='0'>";
										else
											$fileicon = "<img src='" . vtiger_imageurl('fbUnknownFile.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
									}
								} elseif ($downloadtype == 'E') {
									if (trim($value) != '') {
										$fld_value = $value;
										$fileicon = "<img src='" . vtiger_imageurl('fbLink.gif', $theme) . "' alt='" . getTranslatedString('LBL_EXTERNAL_LNK', $module) . "' title='" . getTranslatedString('LBL_EXTERNAL_LNK', $module) . "' hspace='3' align='absmiddle' border='0'>";
									} else {
										$fld_value = '--';
										$fileicon = '';
									}
								} else {
									$fld_value = ' --';
									$fileicon = '';
								}

								$file_name = $adb->query_result($list_result, $i - 1, 'filename');
								$notes_id = $adb->query_result($list_result, $i - 1, 'crmid');
								$folder_id = $adb->query_result($list_result, $i - 1, 'folderid');
								$download_type = $adb->query_result($list_result, $i - 1, 'filelocationtype');
								$file_status = $adb->query_result($list_result, $i - 1, 'filestatus');
								$fileidQuery = "select attachmentsid from vtiger_seattachmentsrel where crmid=?";
								$fileidres = $adb->pquery($fileidQuery, array($notes_id));
								$fileid = $adb->query_result($fileidres, 0, 'attachmentsid');
								if ($file_name != '' && $file_status == 1) {
									if ($download_type == 'I') {
										$fld_value = "<a href='index.php?module=uploads&action=downloadfile&entityid=$notes_id&fileid=$fileid' title='" . getTranslatedString("LBL_DOWNLOAD_FILE", $module) . "' onclick='javascript:dldCntIncrease($notes_id);'>" . textlength_check($fld_value) . "</a>";
									} elseif ($download_type == 'E') {
										$fld_value = "<a target='_blank' href='$file_name' onclick='javascript:dldCntIncrease($notes_id);' title='" . getTranslatedString("LBL_DOWNLOAD_FILE", $module) . "'>" . textlength_check($fld_value) . "</a>";
									} else {
										$fld_value = ' --';
									}
								}
								$value = $fileicon . $fld_value;
							}
							if ($fieldname == 'filesize') {
								$downloadtype = $adb->query_result($list_result, $i - 1, 'filelocationtype');
								if ($downloadtype == 'I') {
									$filesize = $value;
									if ($filesize < 1024)
										$value = $filesize . ' B';
									elseif ($filesize > 1024 && $filesize < 1048576)
										$value = round($filesize / 1024, 2) . ' KB';
									else if ($filesize > 1048576)
										$value = round($filesize / (1024 * 1024), 2) . ' MB';
								} else {
									$value = ' --';
								}
							}
							if ($fieldname == 'filestatus') {
								$filestatus = $value;
								if ($filestatus == 1)
									$value = getTranslatedString('yes', $module);
								elseif ($filestatus == 0)
									$value = getTranslatedString('no', $module);
								else
									$value = ' --';
							}
							if ($fieldname == 'filetype') {
								$downloadtype = $adb->query_result($list_result, $i - 1, 'filelocationtype');
								$filetype = $adb->query_result($list_result, $i - 1, 'filetype');
								if ($downloadtype == 'E' || $downloadtype != 'I') {
									$value = ' --';
								} else
									$value = $filetype;
							}
							if ($fieldname == 'notecontent') {
								$value = decode_html($value);
								$value = textlength_check($value);
							}
						} elseif ($module == "Products" && $name == "Related to") {
							$value = getRelatedTo($module, $list_result, $i - 1);
							$value = textlength_check($value);
						} elseif ($name == 'Contact Name' && ($module == 'SalesOrder' || $module == 'Quotes' || $module == 'PurchaseOrder')) {
							if ($name == 'Contact Name') {
								$contact_id = $adb->query_result($list_result, $i - 1, "contactid");
								$contact_name = getFullNameFromQResult($list_result, $i - 1, "Contacts");
								$value = "";
								if (($contact_name != "") && ($contact_id != 'NULL'))
									$value = "<a href='index.php?module=Contacts&action=DetailView&parenttab=" . $tabname . "&record=" . $contact_id . "' style='" . $P_FONT_COLOR . "'>" . textlength_check($contact_name) . "</a>";
							}
						} elseif ($name == 'Product') {
							$product_id = textlength_check($adb->query_result($list_result, $i - 1, "productname"));
							$value = $product_id;
						} elseif ($name == 'Account Name') {
							//modified for vtiger_customview 27/5
							if ($module == 'Accounts') {
								$account_id = $adb->query_result($list_result, $i - 1, "crmid");
								//$account_name = getAccountName($account_id);
								$account_name = textlength_check($adb->query_result($list_result, $i - 1, "accountname"));
								// Fredy Klammsteiner, 4.8.2005: changes from 4.0.1 migrated to 4.2
								$value = '<a href="index.php?module=Accounts&action=DetailView&record=' . $account_id . '&parenttab=' . $tabname . '" style="' . $P_FONT_COLOR . '">' . $account_name . '</a>'; // Armando Lüscher 05.07.2005 -> §priority -> Desc: inserted style="$P_FONT_COLOR"
							} elseif ($module == 'Potentials' || $module == 'Contacts' || $module == 'Invoice' || $module == 'SalesOrder' || $module == 'Quotes') { //Potential,Contacts,Invoice,SalesOrder & Quotes  records   sort by Account Name
								$accountname = textlength_check($adb->query_result($list_result, $i - 1, "accountname"));
								$accountid = $adb->query_result($list_result, $i - 1, "accountid");
								if (empty($accountname))
									$accountname = getAccountName($accountid);
								$value = '<a href="index.php?module=Accounts&action=DetailView&record=' . $accountid . '&parenttab=' . $tabname . '" style="' . $P_FONT_COLOR . '">' . $accountname . '</a>';
							} else {
								$account_id = $adb->query_result($list_result, $i - 1, "accountid");
								$account_name = getAccountName($account_id);
								$acc_name = textlength_check($account_name);
								// Fredy Klammsteiner, 4.8.2005: changes from 4.0.1 migrated to 4.2
								$value = '<a href="index.php?module=Accounts&action=DetailView&record=' . $account_id . '&parenttab=' . $tabname . '" style="' . $P_FONT_COLOR . '">' . $acc_name . '</a>'; // Armando Lüscher 05.07.2005 -> §priority -> Desc: inserted style="$P_FONT_COLOR"
							}
						} elseif (( $module == 'HelpDesk' || $module == 'PriceBook' || $module == 'Quotes' || $module == 'PurchaseOrder' || $module == 'Faq') && $name == 'Product Name') {
							if ($module == 'HelpDesk' || $module == 'Faq')
								$product_id = $adb->query_result($list_result, $i - 1, "product_id");
							else
								$product_id = $adb->query_result($list_result, $i - 1, "productid");

							if ($product_id != '')
								$product_name = getProductName($product_id);
							else
								$product_name = '';

							$value = '<a href="index.php?module=Products&action=DetailView&parenttab=' . $tabname . '&record=' . $product_id . '">' . textlength_check($product_name) . '</a>';
						} elseif (($module == 'Quotes' && $name == 'Potential Name') || ($module == 'SalesOrder' && $name == 'Potential Name')) {
							$potential_id = $adb->query_result($list_result, $i - 1, "potentialid");
							$potential_name = getPotentialName($potential_id);
							$value = '<a href="index.php?module=Potentials&action=DetailView&parenttab=' . $tabname . '&record=' . $potential_id . '">' . textlength_check($potential_name) . '</a>';
						} elseif ($module == 'Emails' && $relatedlist != '' && ($name == 'Subject' || $name == 'Date Sent' || $name == 'To')) {
							$list_result_count = $i - 1;
							$tmp_value = getValue($ui_col_array, $list_result, $fieldname, $focus, $module, $entity_id, $list_result_count, "list", "", $returnset, $oCv->setdefaultviewid);
							$value = '<a href="javascript:;" onClick="ShowEmail(\'' . $entity_id . '\');">' . textlength_check($tmp_value) . '</a>';
							if ($name == 'Date Sent') {
								$sql = "select email_flag from vtiger_emaildetails where emailid=?";
								$result = $adb->pquery($sql, array($entity_id));
								$email_flag = $adb->query_result($result, 0, "email_flag");
								if ($email_flag != 'SAVED')
									$value = getValue($ui_col_array, $list_result, $fieldname, $focus, $module, $entity_id, $list_result_count, "list", "", $returnset, $oCv->setdefaultviewid);
								else
									$value = '';
							}
						} elseif ($module == 'Calendar' && ($fieldname != 'taskstatus' && $fieldname != 'eventstatus')) {
							if ($activitytype == 'Task') {
								if (getFieldVisibilityPermission('Calendar', $current_user->id, $fieldname) == '0') {
									$list_result_count = $i - 1;
									$value = getValue($ui_col_array, $list_result, $fieldname, $focus, $module, $entity_id, $list_result_count, "list", "", $returnset, $oCv->setdefaultviewid);
								} else {
									$value = '';
								}
							} else {
								if (getFieldVisibilityPermission('Events', $current_user->id, $fieldname) == '0') {
									$list_result_count = $i - 1;
									$value = getValue($ui_col_array, $list_result, $fieldname, $focus, $module, $entity_id, $list_result_count, "list", "", $returnset, $oCv->setdefaultviewid);
								} else {
									$value = '';
								}
							}
						} else {
							$list_result_count = $i - 1;
							$value = getValue($ui_col_array, $list_result, $fieldname, $focus, $module, $entity_id, $list_result_count, "list", "", $returnset, $oCv->setdefaultviewid);
						}
					}

					// vtlib customization: For listview javascript triggers
					$value = "$value <span type='vtlib_metainfo' vtrecordid='{$entity_id}' vtfieldname='{$fieldname}' vtmodule='$module' style='display:none;'></span>";
					// END

					if ($module == "Calendar" && $name == $app_strings['Close']) {
						if (isPermitted("Calendar", "EditView") == 'yes') {
							if ((getFieldVisibilityPermission('Events', $current_user->id, 'eventstatus') == '0') || (getFieldVisibilityPermission('Calendar', $current_user->id, 'taskstatus') == '0')) {
								array_push($list_header, $value);
							}
						}
					}
					else
						$list_header[] = $value;
				}
			}
			$varreturnset = '';
			if ($returnset == '')
				$varreturnset = '&return_module=' . $module . '&return_action=index';
			else
				$varreturnset = $returnset;


			if ($module == 'Calendar') {
				$actvity_type = $adb->query_result($list_result, $list_result_count, 'activitytype');
				if ($actvity_type == 'Task')
					$varreturnset .= '&activity_mode=Task';
				else
					$varreturnset .= '&activity_mode=Events';
			}

			//Added for Actions ie., edit and delete links in listview
			$links_info = "";
			if (!(is_array($selectedfields) && $selectedfields != '')) {
				if (isPermitted($module, "EditView", "") == 'yes') {
					$edit_link = getListViewEditLink($module, $entity_id, $relatedlist, $varreturnset, $list_result, $list_result_count);
					if (isset($_REQUEST['start']) && $_REQUEST['start'] > 1 && $module != 'Emails')
						$links_info .= "<a href=\"$edit_link&start=" . vtlib_purify($_REQUEST['start']) . "\">" . $app_strings["LNK_EDIT"] . "</a> ";
					else
						$links_info .= "<a href=\"$edit_link\">" . $app_strings["LNK_EDIT"] . "</a> ";
				}


				if (isPermitted($module, "Delete", "") == 'yes') {
					$del_link = getListViewDeleteLink($module, $entity_id, $relatedlist, $varreturnset);
					if ($links_info != "" && $del_link != "")
						$links_info .= " | ";
					if ($del_link != "")
						$links_info .= "<a href='javascript:confirmdelete(\"" . addslashes(urlencode($del_link)) . "\")'>" . $app_strings["LNK_DELETE"] . "</a>";
				}
			}
			// Record Change Notification
			if (method_exists($focus, 'isViewed') && PerformancePrefs::getBoolean('LISTVIEW_RECORD_CHANGE_INDICATOR', true)) {
				if (!$focus->isViewed($entity_id)) {
					$links_info .= " | <img src='" . vtiger_imageurl('important1.gif', $theme) . "' border=0>";
				}
			}
			// END
			if ($links_info != "" && !$skipActions)
				$list_header[] = $links_info;
			$list_block[$entity_id] = $list_header;
		}
	$log->debug("Exiting getListViewEntries method ...");
	return $list_block;
}

/* * This function generates the List view entries in a popup list view
 * Param $focus - module object
 * Param $list_result - resultset of a listview query
 * Param $navigation_array - navigation values in an array
 * Param $relatedlist - check for related list flag
 * Param $returnset - list query parameters in url string
 * Param $edit_action - Edit action value
 * Param $del_action - delete action value
 * Param $oCv - vtiger_customview object
 * Returns an array type
 */

function getSearchListViewEntries($focus, $module, $list_result, $navigation_array, $form = '') {
	global $log;
	$log->debug("Entering getSearchListViewEntries(" . get_class($focus) . "," . $module . "," . $list_result . "," . $navigation_array . ") method ...");

	global $adb, $app_strings, $theme, $current_user, $list_max_entries_per_page;
	$noofrows = $adb->num_rows($list_result);

	$list_header = '';
	$theme_path = "themes/" . $theme . "/";
	$image_path = $theme_path . "images/";
	$list_block = Array();

	//getting the vtiger_fieldtable entries from database
	$tabid = getTabid($module);
	require('user_privileges/user_privileges_' . $current_user->id . '.php');

	//Added to reduce the no. of queries logging for non-admin user -- by Minnie-start
	$field_list = array();
	$j = 0;
	foreach ($focus->search_fields as $name => $tableinfo) {
		$fieldname = $focus->search_fields_name[$name];
		array_push($field_list, $fieldname);
		$j++;
	}

	$field = Array();
	if ($is_admin == false && $module != 'Users') {
		if ($module == 'Emails') {
			$query = "SELECT fieldname FROM vtiger_field WHERE tabid = ? and vtiger_field.presence in (0,2)";
			$params = array($tabid);
		} else {
			$profileList = getCurrentUserProfileList();
			$query = "SELECT DISTINCT vtiger_field.fieldname
				FROM vtiger_field
				INNER JOIN vtiger_profile2field
					ON vtiger_profile2field.fieldid = vtiger_field.fieldid
				INNER JOIN vtiger_def_org_field
					ON vtiger_def_org_field.fieldid = vtiger_field.fieldid
				WHERE vtiger_field.tabid = ?
				AND vtiger_profile2field.visible = 0
				AND vtiger_def_org_field.visible = 0
				AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ")
				AND vtiger_field.fieldname IN (" . generateQuestionMarks($field_list) . ") and vtiger_field.presence in (0,2)";
			$params = array($tabid, $profileList, $field_list);
		}

		$result = $adb->pquery($query, $params);

		for ($k = 0; $k < $adb->num_rows($result); $k++) {
			$field[] = $adb->query_result($result, $k, "fieldname");
		}
	}
	//constructing the uitype and columnname array
	$ui_col_array = Array();

	$query = "SELECT uitype, columnname, fieldname
		FROM vtiger_field
		WHERE tabid=?
		AND fieldname IN (" . generateQuestionMarks($field_list) . ") and vtiger_field.presence in (0,2)";
	$result = $adb->pquery($query, array($tabid, $field_list));
	$num_rows = $adb->num_rows($result);
	for ($i = 0; $i < $num_rows; $i++) {
		$tempArr = array();
		$uitype = $adb->query_result($result, $i, 'uitype');
		$columnname = $adb->query_result($result, $i, 'columnname');
		$field_name = $adb->query_result($result, $i, 'fieldname');
		$tempArr[$uitype] = $columnname;
		$ui_col_array[$field_name] = $tempArr;
	}

	//end
	if ($navigation_array['end_val'] > 0) {
		for ($i = 1; $i <= $noofrows; $i++) {

			//Getting the entityid
			if ($module != 'Users') {
				$entity_id = $adb->query_result($list_result, $i - 1, "crmid");
			} else {
				$entity_id = $adb->query_result($list_result, $i - 1, "id");
			}

			$list_header = Array();

			foreach ($focus->search_fields as $name => $tableinfo) {
				$fieldname = $focus->search_fields_name[$name];

				if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || in_array($fieldname, $field) || $module == 'Users') {
					if ($fieldname == '') {
						$table_name = '';
						$column_name = '';
						foreach ($tableinfo as $tablename => $colname) {
							$table_name = $tablename;
							$column_name = $colname;
						}
						$value = $adb->query_result($list_result, $i - 1, $colname);
					} else {
						if (($module == 'Calls' || $module == 'Tasks' || $module == 'Meetings' || $module == 'Emails') && (($name == 'Related to') || ($name == 'Contact Name') || ($name == 'Vendor Name'))) {
							if ($name == 'Related to')
								$value = getRelatedTo($module, $list_result, $i - 1);
							if ($name == 'Contact Name') {
								$contact_id = $adb->query_result($list_result, $i - 1, "contactid");
								$contact_name = getFullNameFromQResult($list_result, $i - 1, "Contacts");
								$value = "";
								if (($contact_name != "") && ($contact_id != 'NULL'))
									$value = "<a href='index.php?module=Contacts&action=DetailView&record=" . $contact_id . "'>" . $contact_name . "</a>";
							}
						}
						elseif (($module == 'Faq' || $module == 'Documents') && $name == 'Related to') {
							$value = getRelatedToEntity($module, $list_result, $i - 1);
						} elseif ($name == 'Account Name' && ($module == 'Potentials' || $module == 'SalesOrder' || $module == 'Quotes')) {
							$account_id = $adb->query_result($list_result, $i - 1, "accountid");
							$account_name = getAccountName($account_id);
							$value = textlength_check($account_name);
						} elseif ($name == 'Quote Name' && $module == 'SalesOrder') {
							$quote_id = $adb->query_result($list_result, $i - 1, "quoteid");
							$quotename = getQuoteName($quote_id);
							$value = textlength_check($quotename);
						} elseif ($name == 'Account Name' && $module == 'Contacts') {
							$account_id = $adb->query_result($list_result, $i - 1, "accountid");
							$account_name = getAccountName($account_id);
							$value = textlength_check($account_name);
						}
						// vtlib customization: Generic popup handling
						elseif (isset($focus->popup_fields) && in_array($fieldname, $focus->popup_fields)) {
							global $default_charset;
							$forfield = htmlspecialchars($_REQUEST['forfield'], ENT_QUOTES, $default_charset);
							$list_result_count = $i - 1;
							$value = getValue($ui_col_array, $list_result, $fieldname, $focus, $module, $entity_id, $list_result_count, "search", $focus->popup_type);
							if (isset($forfield) && $forfield != '' && $focus->popup_type != 'detailview') {
								$value1 = strip_tags($value);
								$value = htmlspecialchars(addslashes(html_entity_decode(strip_tags($value), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset); // Remove any previous html conversion
								$count = counterValue();
								$value = "<a href='javascript:window.close();' onclick='return vtlib_setvalue_from_popup($entity_id, \"$value\", \"$forfield\")' id =$count >$value1</a>";
							}
						}
						// END
						else {
							$list_result_count = $i - 1;
							$value = getValue($ui_col_array, $list_result, $fieldname, $focus, $module, $entity_id, $list_result_count, "search", $focus->popup_type, $form);
						}
					}
					$list_header[] = $value;
				}
			}

			if ($module == 'Products' && ($focus->popup_type == 'inventory_prod' || $focus->popup_type == 'inventory_prod_po')) {
				global $default_charset;
				require('user_privileges/user_privileges_' . $current_user->id . '.php');
				$row_id = $_REQUEST['curr_row'];

				//To get all the tax types and values and pass it to product details
				$tax_str = '';
				$tax_details = getAllTaxes();
				for ($tax_count = 0; $tax_count < count($tax_details); $tax_count++) {
					$tax_str .= $tax_details[$tax_count]['taxname'] . '=' . $tax_details[$tax_count]['percentage'] . ',';
				}
				$tax_str = trim($tax_str, ',');
				$rate = $user_info['conv_rate'];
				if (getFieldVisibilityPermission($module, $current_user->id, 'unit_price') == '0') {
					$unitprice = $adb->query_result($list_result, $list_result_count, 'unit_price');
					if ($_REQUEST['currencyid'] != null) {
						$prod_prices = getPricesForProducts($_REQUEST['currencyid'], array($entity_id));
						$unitprice = $prod_prices[$entity_id];
					}
				} else {
					$unitprice = '';
				}
				$sub_products = '';
				$sub_prod = '';
				$sub_prod_query = $adb->pquery("SELECT vtiger_products.productid,vtiger_products.productname from vtiger_products INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid INNER JOIN vtiger_seproductsrel on vtiger_seproductsrel.crmid=vtiger_products.productid WHERE vtiger_seproductsrel.productid=? and vtiger_seproductsrel.setype='Products'", array($entity_id));
				for ($k = 0; $k < $adb->num_rows($sub_prod_query); $k++) {
					//$sub_prod=array();
					$id = $adb->query_result($sub_prod_query, $k, "productid");
					$str_sep = '';
					if ($k > 0)
						$str_sep = ":";
					$sub_products .= $str_sep . $id;
					$sub_prod .= $str_sep . " - " . $adb->query_result($sub_prod_query, $k, "productname");
				}

				$sub_det = $sub_products . "::" . str_replace(":", "<br>", $sub_prod);
				$qty_stock = $adb->query_result($list_result, $list_result_count, 'qtyinstock');

				$slashes_temp_val = popup_from_html(getProductName($entity_id));
				$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);
				$description = $adb->query_result($list_result, $list_result_count, 'description');
				$slashes_desc = htmlspecialchars($description, ENT_QUOTES, $default_charset);

				$sub_products_link = '<a href="index.php?module=Products&action=Popup&html=Popup_picker&return_module=' . vtlib_purify($_REQUEST['return_module']) . '&record_id=' . vtlib_purify($entity_id) . '&form=HelpDeskEditView&select=enable&popuptype=' . $focus->popup_type . '&curr_row=' . vtlib_purify($row_id) . '&currencyid=' . vtlib_purify($_REQUEST['currencyid']) . '" > Sub Products</a>';

				if (!isset($_REQUEST['record_id'])) {
					$sub_products_query = $adb->pquery("SELECT * from vtiger_seproductsrel WHERE productid=? AND setype='Products'", array($entity_id));
					if ($adb->num_rows($sub_products_query) > 0)
						$list_header[] = $sub_products_link;
					else
						$list_header[] = $app_strings['LBL_NO_SUB_PRODUCTS'];
				}
			}

			if ($module == 'Services' && $focus->popup_type == 'inventory_service') {
				global $default_charset;
				require('user_privileges/user_privileges_' . $current_user->id . '.php');
				$row_id = $_REQUEST['curr_row'];

				//To get all the tax types and values and pass it to product details
				$tax_str = '';
				$tax_details = getAllTaxes();
				for ($tax_count = 0; $tax_count < count($tax_details); $tax_count++) {
					$tax_str .= $tax_details[$tax_count]['taxname'] . '=' . $tax_details[$tax_count]['percentage'] . ',';
				}
				$tax_str = trim($tax_str, ',');
				$rate = $user_info['conv_rate'];
				if (getFieldVisibilityPermission($module, $current_user->id, 'unit_price') == '0') {
					$unitprice = $adb->query_result($list_result, $list_result_count, 'unit_price');
					if ($_REQUEST['currencyid'] != null) {
						$prod_prices = getPricesForProducts($_REQUEST['currencyid'], array($entity_id), $module);
						$unitprice = $prod_prices[$entity_id];
					}
				} else {
					$unitprice = '';
				}

				$slashes_temp_val = popup_from_html($adb->query_result($list_result, $list_result_count, 'servicename'));
				$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);
				$description = $adb->query_result($list_result, $list_result_count, 'description');
				$slashes_desc = htmlspecialchars($description, ENT_QUOTES, $default_charset);
			}
			$list_block[$entity_id] = $list_header;
		}
	}
	$list = $list_block;
	$log->debug("Exiting getSearchListViewEntries method ...");
	return $list;
}

/* * This function generates the value for a given vtiger_field namee
 * Param $field_result - vtiger_field result in array
 * Param $list_result - resultset of a listview query
 * Param $fieldname - vtiger_field name
 * Param $focus - module object
 * Param $module - module name
 * Param $entity_id - entity id
 * Param $list_result_count - list result count
 * Param $mode - mode type
 * Param $popuptype - popup type
 * Param $returnset - list query parameters in url string
 * Param $viewid - custom view id
 * Returns an string value
 */

function getValue($field_result, $list_result, $fieldname, $focus, $module, $entity_id, $list_result_count, $mode, $popuptype, $returnset = '', $viewid = '') {
	global $log, $listview_max_textlength, $app_strings, $current_language, $currentModule;
	$log->debug("Entering getValue(" . $field_result . "," . $list_result . "," . $fieldname . "," . get_class($focus) . "," . $module . "," . $entity_id . "," . $list_result_count . "," . $mode . "," . $popuptype . "," . $returnset . "," . $viewid . ") method ...");
	global $adb, $current_user, $default_charset;

	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	$tabname = getParentTab();
	$tabid = getTabid($module);
	$current_module_strings = return_module_language($current_language, $module);
	$uicolarr = $field_result[$fieldname];
	foreach ($uicolarr as $key => $value) {
		$uitype = $key;
		$colname = $value;
	}
	//added for getting event status in Custom view - Jaguar
	if ($module == 'Calendar' && ($colname == "status" || $colname == "eventstatus")) {
		$colname = "activitystatus";
	}
	//Ends
	$field_val = $adb->query_result($list_result, $list_result_count, $colname);
	if ($uitype != 8) {
		$temp_val = html_entity_decode($field_val, ENT_QUOTES, $default_charset);
	} else {
		$temp_val = $field_val;
	}
	// vtlib customization: New uitype to handle relation between modules
	if ($uitype == '10') {
		$parent_id = $field_val;
		if (!empty($parent_id)) {
			$parent_module = getSalesEntityType($parent_id);
			$valueTitle = $parent_module;
			if ($app_strings[$valueTitle])
				$valueTitle = $app_strings[$valueTitle];

			$displayValueArray = getEntityName($parent_module, $parent_id);
			if (!empty($displayValueArray)) {
				foreach ($displayValueArray as $key => $value) {
					$value = $value;
				}
			}
			$value = "<a href='index.php?module=$parent_module&action=DetailView&record=$parent_id' title='$valueTitle'>" . textlength_check($value) . "</a>";
		} else {
			$value = '';
		}
	} // END
	else if ($uitype == 53) {
		$value = $adb->query_result($list_result, $list_result_count, 'user_name');
		// When Assigned To field is used in Popup window
		if ($value == '') {
			$user_id = $adb->query_result($list_result, $list_result_count, 'smownerid');
			if ($user_id != null && $user_id != '') {
				$value = getOwnerName($user_id);
				$value = textlength_check($value);
			}
		}
	} elseif ($uitype == 52) {
		$value = getOwnerName($adb->query_result($list_result, $list_result_count, $colname));
		$value = textlength_check($value);
	} elseif ($uitype == 51) {//Accounts - Member Of
		$parentid = $adb->query_result($list_result, $list_result_count, "parentid");
		if ($module == 'Accounts')
			$entity_name = textlength_check(getAccountName($parentid));
		elseif ($module == 'Products')
			$entity_name = textlength_check(getProductName($parentid));
		$value = '<a href="index.php?module=' . $module . '&action=DetailView&record=' . $parentid . '&parenttab=' . $tabname . '" style="' . $P_FONT_COLOR . '">' . $entity_name . '</a>';
	}
	elseif ($uitype == 77) {
		$value = getOwnerName($adb->query_result($list_result, $list_result_count, 'inventorymanager'));
		$value = textlength_check($value);
	} elseif ($uitype == 5 || $uitype == 6 || $uitype == 23 || $uitype == 70) {
		$temp_val = trim($temp_val);
		$timeField = 'time_start';
		if ($fieldname == 'due_date') {
			$timeField = 'time_end';
		}
		if ($temp_val != '' && $module == 'Calendar' && ($uitype == 23 || $uitype == 6) &&
				$timeField != '' && ($fieldname == 'date_start' || $fieldname == 'due_date' )) {
			$time = $adb->query_result($list_result, $list_result_count, $timeField);
			if (empty($time)) {
				$time = getSingleFieldValue('vtiger_activity', $timeField, 'activityid', $entity_id);
			}
		}
		if ($temp_val == '0000-00-00' || empty($temp_val)) {
			$value = '';
		} else {
			if (empty($time) && strpos($temp_val, ' ') == false) {
				$value = DateTimeField::convertToUserFormat($temp_val);
			} else {
				if (!empty($time)) {
					$date = new DateTimeField($temp_val . ' ' . $time);
					$value = $date->getDisplayDate();
				} else {
					$date = new DateTimeField($temp_val);
					$value = $date->getDisplayDateTimeValue();
				}
			}
		}
	} elseif ($uitype == 15 || ($uitype == 55 && $fieldname == "salutationtype")) {
		$temp_val = decode_html($adb->query_result($list_result, $list_result_count, $colname));
		if (($is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1) && $temp_val != '') {
			$temp_acttype = $adb->query_result($list_result, $list_result_count, 'activitytype');
			if (($temp_acttype != 'Task') && $fieldname == "taskstatus")
				$temptable = "eventstatus";
			else
				$temptable = $fieldname;
			$roleid = $current_user->roleid;
			$roleids = Array();
			$subrole = getRoleSubordinates($roleid);
			if (count($subrole) > 0)
				$roleids = $subrole;
			array_push($roleids, $roleid);

			//here we are checking wheather the table contains the sortorder column .If  sortorder is present in the main picklist table, then the role2picklist will be applicable for this table...

			$sql = "select * from vtiger_$temptable where $temptable=?";
			$res = $adb->pquery($sql, array(decode_html($temp_val)));
			$picklistvalueid = $adb->query_result($res, 0, 'picklist_valueid');
			if ($picklistvalueid != null) {
				$pick_query = "select * from vtiger_role2picklist where picklistvalueid=$picklistvalueid and roleid in (" . generateQuestionMarks($roleids) . ")";
				$res_val = $adb->pquery($pick_query, array($roleids));
				$num_val = $adb->num_rows($res_val);
			}
			if ($num_val > 0 || ($temp_acttype == 'Task' && $fieldname == 'activitytype'))
				$temp_val = $temp_val;
			else
				$temp_val = "<font color='red'>" . $app_strings['LBL_NOT_ACCESSIBLE'] . "</font>";
		}
		$value = ($current_module_strings[$temp_val] != '') ? $current_module_strings[$temp_val] : (($app_strings[$temp_val] != '') ? ($app_strings[$temp_val]) : $temp_val);
		if ($value != "<font color='red'>" . $app_strings['LBL_NOT_ACCESSIBLE'] . "</font>") {
			$value = textlength_check($value);
		}
	} elseif ($uitype == 16) {
		$value = getTranslatedString($temp_val, $currentModule);
		$value = textlength_check($value);
	} elseif ($uitype == 71 || $uitype == 72) {
		if ($temp_val != '') {
			// Some of the currency fields like Unit Price, Total, Sub-total etc of Inventory modules, do not need currency conversion
			if ($uitype == 72) {
				if ($fieldname == 'unit_price') {
					$currency_id = getProductBaseCurrency($entity_id, $module);
					$cursym_convrate = getCurrencySymbolandCRate($currency_id);
					$currency_symbol = $cursym_convrate['symbol'];
				} else {
					$currency_info = getInventoryCurrencyInfo($module, $entity_id);
					$currency_symbol = $currency_info['currency_symbol'];
				}
				$currencyValue = CurrencyField::convertToUserFormat($temp_val, null, true);
				$value = CurrencyField::appendCurrencySymbol($currencyValue, $currency_symbol);
			} else {
				//changes made to remove vtiger_currency symbol infront of each vtiger_potential amount
				if ($temp_val != 0)
					$value = CurrencyField::convertToUserFormat($temp_val);
				else
					$value = $temp_val;
			}
		}
		else {
			$value = '';
		}
	} elseif ($uitype == 17) {
		$matchPattern = "^[\w]+:\/\/^";
		preg_match($matchPattern, $field_val, $matches);
		if (!empty($matches[0])) {
			$value = '<a href="' . $field_val . '" target="_blank">' . textlength_check($temp_val) . '</a>';
		} else {
			$value = '<a href="http://' . $field_val . '" target="_blank">' . textlength_check($temp_val) . '</a>';
		}
	} elseif ($uitype == 13 || $uitype == 104 && ($_REQUEST['action'] != 'Popup' && $_REQUEST['file'] != 'Popup')) {
		if ($_SESSION['internal_mailer'] == 1) {
			//check added for email link in user detailview
			if ($module == 'Calendar') {
				if (getActivityType($entity_id) == 'Task') {
					$tabid = 9;
				} else {
					$tabid = 16;
				}
			} else {
				$tabid = getTabid($module);
			}
			$fieldid = getFieldid($tabid, $fieldname);
			if (empty($popuptype)) {
				$value = '<a href="javascript:InternalMailer(' . $entity_id . ',' . $fieldid . ',\'' . $fieldname . '\',\'' . $module . '\',\'record_id\');">' . textlength_check($temp_val) . '</a>';
			} else {
				$value = $temp_val;
				$value = textlength_check($value);
			}
		}
		else
			$value = '<a href="mailto:' . $field_val . '">' . textlength_check($temp_val) . '</a>';
	}
	elseif ($uitype == 56) {
		if ($temp_val == 1) {
			$value = $app_strings['yes'];
		} elseif ($temp_val == 0) {
			$value = $app_strings['no'];
		} else {
			$value = '';
		}
	} elseif ($uitype == 57) {
		if ($temp_val != '') {
			$sql = "SELECT * FROM vtiger_contactdetails WHERE contactid=?";
			$result = $adb->pquery($sql, array($temp_val));
			$value = '';
			if ($adb->num_rows($result)) {
				$name = getFullNameFromQResult($result, 0, "Contacts");
				$value = '<a href=index.php?module=Contacts&action=DetailView&record=' . $temp_val . '>' . textlength_check($name) . '</a>';
			}
		}
		else
			$value = '';
	}
	//Added by Minnie to get Campaign Source
	elseif ($uitype == 58) {
		if ($temp_val != '') {
			$sql = "SELECT * FROM vtiger_campaign WHERE campaignid=?";
			$result = $adb->pquery($sql, array($temp_val));
			$campaignname = $adb->query_result($result, 0, "campaignname");
			$value = '<a href=index.php?module=Campaigns&action=DetailView&record=' . $temp_val . '>' . textlength_check($campaignname) . '</a>';
		}
		else
			$value = '';
	}
	//End
	//Added By *Raj* for the Issue ProductName not displayed in CustomView of HelpDesk
	elseif ($uitype == 59) {
		if ($temp_val != '') {
			$value = getProductName($temp_val);
		} else {
			$value = '';
		}
	}
	//End
	elseif ($uitype == 61) {
		$attachmentid = $adb->query_result($adb->pquery("SELECT * FROM vtiger_seattachmentsrel WHERE crmid = ?", array($entity_id)), 0, 'attachmentsid');
		$value = '<a href = "index.php?module=uploads&action=downloadfile&return_module=' . $module . '&fileid=' . $attachmentid . '&filename=' . $temp_val . '">' . textlength_check($temp_val) . '</a>';
	} elseif ($uitype == 62) {
		$parentid = $adb->query_result($list_result, $list_result_count, "parent_id");
		$parenttype = $adb->query_result($list_result, $list_result_count, "parent_type");

		if ($parenttype == "Leads") {
			$tablename = "vtiger_leaddetails";
			$fieldname = "lastname";
			$idname = "leadid";
		}
		if ($parenttype == "Accounts") {
			$tablename = "vtiger_account";
			$fieldname = "accountname";
			$idname = "accountid";
		}
		if ($parenttype == "Products") {
			$tablename = "vtiger_products";
			$fieldname = "productname";
			$idname = "productid";
		}
		if ($parenttype == "HelpDesk") {
			$tablename = "vtiger_troubletickets";
			$fieldname = "title";
			$idname = "ticketid";
		}
		if ($parenttype == "Invoice") {
			$tablename = "vtiger_invoice";
			$fieldname = "subject";
			$idname = "invoiceid";
		}


		if ($parentid != '') {
			$sql = "SELECT * FROM $tablename WHERE $idname = ?";
			$fieldvalue = $adb->query_result($adb->pquery($sql, array($parentid)), 0, $fieldname);

			$value = '<a href=index.php?module=' . $parenttype . '&action=DetailView&record=' . $parentid . '&parenttab=' . urlencode($tabname) . '>' . textlength_check($fieldvalue) . '</a>';
		}
		else
			$value = '';
	}
	elseif ($uitype == 66) {
		$parentid = $adb->query_result($list_result, $list_result_count, "parent_id");
		$parenttype = $adb->query_result($list_result, $list_result_count, "parent_type");

		if ($parenttype == "Leads") {
			$tablename = "vtiger_leaddetails";
			$fieldname = "lastname";
			$idname = "leadid";
		}
		if ($parenttype == "Accounts") {
			$tablename = "vtiger_account";
			$fieldname = "accountname";
			$idname = "accountid";
		}
		if ($parenttype == "HelpDesk") {
			$tablename = "vtiger_troubletickets";
			$fieldname = "title";
			$idname = "ticketid";
		}
		if ($parentid != '') {
			$sql = "SELECT * FROM $tablename WHERE $idname = ?";
			$fieldvalue = $adb->query_result($adb->pquery($sql, array($parentid)), 0, $fieldname);

			$value = '<a href=index.php?module=' . $parenttype . '&action=DetailView&record=' . $parentid . '&parenttab=' . urlencode($tabname) . '>' . textlength_check($fieldvalue) . '</a>';
		}
		else
			$value = '';
	}
	elseif ($uitype == 67) {
		$parentid = $adb->query_result($list_result, $list_result_count, "parent_id");
		$parenttype = $adb->query_result($list_result, $list_result_count, "parent_type");

		if ($parenttype == "Leads") {
			$tablename = "vtiger_leaddetails";
			$fieldname = "lastname";
			$idname = "leadid";
		}
		if ($parenttype == "Contacts") {
			$tablename = "vtiger_contactdetails";
			$fieldname = "contactname";
			$idname = "contactid";
		}
		if ($parentid != '') {
			$sql = "SELECT * FROM $tablename WHERE $idname = ?";
			$fieldvalue = $adb->query_result($adb->pquery($sql, array($parentid)), 0, $fieldname);

			$value = '<a href=index.php?module=' . $parenttype . '&action=DetailView&record=' . $parentid . '&parenttab=' . urlencode($tabname) . '>' . textlength_check($fieldvalue) . '</a>';
		}
		else
			$value = '';
	}
	elseif ($uitype == 68) {
		$parentid = $adb->query_result($list_result, $list_result_count, "parent_id");
		$parenttype = $adb->query_result($list_result, $list_result_count, "parent_type");

		if ($parenttype == '' && $parentid != '')
			$parenttype = getSalesEntityType($parentid);

		if ($parenttype == "Contacts") {
			$tablename = "vtiger_contactdetails";
			$fieldname = "contactname";
			$idname = "contactid";
		}
		if ($parenttype == "Accounts") {
			$tablename = "vtiger_account";
			$fieldname = "accountname";
			$idname = "accountid";
		}
		if ($parentid != '') {
			$sql = "SELECT * FROM $tablename WHERE $idname = ?";
			$fieldvalue = $adb->query_result($adb->pquery($sql, array($parentid)), 0, $fieldname);

			$value = '<a href=index.php?module=' . $parenttype . '&action=DetailView&record=' . $parentid . '&parenttab=' . urlencode($tabname) . '>' . textlength_check($fieldvalue) . '</a>';
		}
		else
			$value = '';
	}
	elseif ($uitype == 78) {
		if ($temp_val != '') {

			$quote_name = getQuoteName($temp_val);
			$value = '<a href=index.php?module=Quotes&action=DetailView&record=' . $temp_val . '&parenttab=' . urlencode($tabname) . '>' . textlength_check($quote_name) . '</a>';
		}
		else
			$value = '';
	}
	elseif ($uitype == 79) {
		if ($temp_val != '') {

			$purchaseorder_name = getPoName($temp_val);
			$value = '<a href=index.php?module=PurchaseOrder&action=DetailView&record=' . $temp_val . '&parenttab=' . urlencode($tabname) . '>' . textlength_check($purchaseorder_name) . '</a>';
		}
		else
			$value = '';
	}
	elseif ($uitype == 80) {
		if ($temp_val != '') {

			$salesorder_name = getSoName($temp_val);
			$value = "<a href=index.php?module=SalesOrder&action=DetailView&record=$temp_val&parenttab=" . urlencode($tabname) . ">" . textlength_check($salesorder_name) . '</a>';
		}
		else
			$value = '';
	}
	elseif ($uitype == 75 || $uitype == 81) {

		if ($temp_val != '') {

			$vendor_name = getVendorName($temp_val);
			$value = '<a href=index.php?module=Vendors&action=DetailView&record=' . $temp_val . '&parenttab=' . urlencode($tabname) . '>' . textlength_check($vendor_name) . '</a>';
		}
		else
			$value = '';
	}
	elseif ($uitype == 98) {
		$value = '<a href="index.php?action=RoleDetailView&module=Settings&parenttab=Settings&roleid=' . $temp_val . '">' . textlength_check(getRoleName($temp_val)) . '</a>';
	} elseif ($uitype == 33) {
		$value = ($temp_val != "") ? str_ireplace(' |##| ', ', ', $temp_val) : "";
		if (!$is_admin && $value != '') {
			$value = ($field_val != "") ? str_ireplace(' |##| ', ', ', $field_val) : "";
			if ($value != '') {
				$value_arr = explode(',', trim($value));
				$roleid = $current_user->roleid;
				$subrole = getRoleSubordinates($roleid);
				if (count($subrole) > 0) {
					$roleids = $subrole;
					array_push($roleids, $roleid);
				} else {
					$roleids = $roleid;
				}

				if (count($roleids) > 0) {
					$pick_query = "select distinct $fieldname from vtiger_$fieldname inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$fieldname.picklist_valueid where roleid in (" . generateQuestionMarks($roleids) . ") and picklistid in (select picklistid from vtiger_$fieldname) order by $fieldname asc";
					$params = array($roleids);
				} else {
					$pick_query = "select distinct $fieldname from vtiger_$fieldname inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$fieldname.picklist_valueid where picklistid in (select picklistid from vtiger_$fieldname) order by $fieldname asc";
					$params = array();
				}
				$pickListResult = $adb->pquery($pick_query, $params);
				$picklistval = Array();
				for ($i = 0; $i < $adb->num_rows($pickListResult); $i++) {
					$picklistarr[] = $adb->query_result($pickListResult, $i, $fieldname);
				}
				$value_temp = Array();
				$string_temp = '';
				$str_c = 0;
				foreach ($value_arr as $ind => $val) {
					$notaccess = '<font color="red">' . $app_strings['LBL_NOT_ACCESSIBLE'] . "</font>";
					if (!$listview_max_textlength || !(strlen(preg_replace("/(<\/?)(\w+)([^>]*>)/i", "", $string_temp)) > $listview_max_textlength)) {
						$value_temp1 = (in_array(trim($val), $picklistarr)) ? $val : $notaccess;
						if ($str_c != 0)
							$string_temp .= ' , ';
						$string_temp .= $value_temp1;
						$str_c++;
					}
					else
						$string_temp .='...';
				}
				$value = $string_temp;
			}
		}
	}
	elseif ($uitype == 85) {
		$value = ($temp_val != "") ? "<a href='skype:{$temp_val}?call'>{$temp_val}</a>" : "";
	} elseif ($uitype == 116) {
		$value = ($temp_val != "") ? getCurrencyName($temp_val) : "";
	} elseif ($uitype == 117) {
		// NOTE: Without symbol the value could be used for filtering/lookup hence avoiding the translation
		$value = ($temp_val != "") ? getCurrencyName($temp_val, false) : "";
	} elseif ($uitype == 26) {
		$sql = "select foldername from vtiger_attachmentsfolder where folderid = ?";
		$res = $adb->pquery($sql, array($temp_val));
		$foldername = $adb->query_result($res, 0, 'foldername');
		$value = $foldername;
	}
	//added for asterisk integration
	elseif ($uitype == 11) {
		// Fix added for Trac Id: 6139
		if (vtlib_isModuleActive('PBXManager')) {
			$value = "<a href='javascript:;' onclick='startCall(&quot;$temp_val&quot;, &quot;$entity_id&quot;)'>" . textlength_check($temp_val) . "</a>";
		} else {
			$value = $temp_val;
		}
	}
	//asterisk changes end here
	//Added for email status tracking
	elseif ($uitype == 25) {
		$contactid = $_REQUEST['record'];
		$emailid = $adb->query_result($list_result, $list_result_count, "activityid");
		$result = $adb->pquery("SELECT access_count FROM vtiger_email_track WHERE crmid=? AND mailid=?", array($contactid, $emailid));
		$value = $adb->query_result($result, 0, "access_count");
		if (!$value) {
			$value = 0;
		}
	} elseif ($uitype == 8) {
		if (!empty($temp_val)) {
			$temp_val = html_entity_decode($temp_val, ENT_QUOTES, $default_charset);
			$json = new Zend_Json();
			$value = vt_suppressHTMLTags(implode(',', $json->decode($temp_val)));
		}
	}
	//end email status tracking
	else {
		if ($fieldname == $focus->list_link_field) {
			if ($mode == "search") {
				if ($popuptype == "specific" || $popuptype == "toDospecific") {
					// Added for get the first name of contact in Popup window
					if ($colname == "lastname" && $module == 'Contacts') {
						$temp_val = getFullNameFromQResult($list_result, $list_result_count, "Contacts");
					}

					$slashes_temp_val = popup_from_html($temp_val);
					$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);

					//Added to avoid the error when select SO from Invoice through AjaxEdit
					if ($module == 'SalesOrder') {
						$count = counterValue();
						$value = '<a href="javascript:window.close();" onclick=\'set_return_specific("' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '","' . $_REQUEST['form'] . '");\' id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
					} elseif ($module == 'Contacts') {
						require_once('modules/Contacts/Contacts.php');
						$cntct_focus = new Contacts();
						$cntct_focus->retrieve_entity_info($entity_id, "Contacts");
						$slashes_temp_val = popup_from_html($temp_val);
						//ADDED TO CHECK THE FIELD PERMISSIONS FOR
						$xyz = array('mailingstreet', 'mailingcity', 'mailingzip', 'mailingpobox', 'mailingcountry', 'mailingstate', 'otherstreet', 'othercity', 'otherzip', 'otherpobox', 'othercountry', 'otherstate');
						for ($i = 0; $i < 12; $i++) {
							if (getFieldVisibilityPermission($module, $current_user->id, $xyz[$i]) == '0') {
								$cntct_focus->column_fields[$xyz[$i]] = $cntct_focus->column_fields[$xyz[$i]];
							}
							else
								$cntct_focus->column_fields[$xyz[$i]] = '';
						}
						// For ToDo creation the underlying form is not named as EditView
						$form = !empty($_REQUEST['form']) ? $_REQUEST['form'] : '';
						if (!empty($form))
							$form = htmlspecialchars($form, ENT_QUOTES, $default_charset);
						$count = counterValue();
						$value = '<a href="javascript:window.close();" onclick=\'set_return_contact_address("' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '", "' . popup_decode_html($cntct_focus->column_fields['mailingstreet']) . '", "' . popup_decode_html($cntct_focus->column_fields['otherstreet']) . '", "' . popup_decode_html($cntct_focus->column_fields['mailingcity']) . '", "' . popup_decode_html($cntct_focus->column_fields['othercity']) . '", "' . popup_decode_html($cntct_focus->column_fields['mailingstate']) . '", "' . popup_decode_html($cntct_focus->column_fields['otherstate']) . '", "' . popup_decode_html($cntct_focus->column_fields['mailingzip']) . '", "' . popup_decode_html($cntct_focus->column_fields['otherzip']) . '", "' . popup_decode_html($cntct_focus->column_fields['mailingcountry']) . '", "' . popup_decode_html($cntct_focus->column_fields['othercountry']) . '","' . popup_decode_html($cntct_focus->column_fields['mailingpobox']) . '", "' . popup_decode_html($cntct_focus->column_fields['otherpobox']) . '","' . $form . '");\'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
					}

					else
					if ($popuptype == 'toDospecific') {
						$count = counterValue();
						$value = '<a href="javascript:window.close();" onclick=\'set_return_toDospecific("' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '");\'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
					} else {
						$count = counterValue();
						$value = '<a href="javascript:window.close();" onclick=\'set_return_specific("' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '");\'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
					}
				} elseif ($popuptype == "detailview") {
					if ($colname == "lastname" && ($module == 'Contacts' || $module == 'Leads')) {
						$temp_val = getFullNameFromQResult($list_result, $list_result_count, $module);
					}

					$slashes_temp_val = popup_from_html($temp_val);
					$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);

					$focus->record_id = $_REQUEST['recordid'];
					$popupMode = $_REQUEST['popupmode'];
					$callBack = $_REQUEST['callback'];
					if ($_REQUEST['return_module'] == "Calendar") {
						$count = counterValue();
						$value = '<a href="javascript:window.close();" id="calendarCont' . $entity_id . '" LANGUAGE=javascript onclick=\'add_data_to_relatedlist_incal("' . $entity_id . '","' . decode_html($slashes_temp_val) . '");\'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
					} else {
						$count = counterValue();
						if (empty($callBack)) {
							$value = '<a style="cursor:pointer;" onclick=\'add_data_to_relatedlist("' . $entity_id . '","' . $focus->record_id . '","' . $module . '","' . $popupMode . '");\'>' . textlength_check($temp_val) . '</a>';
						} else {
							$value = '<a style="cursor:pointer;" onclick=\'add_data_to_relatedlist("' . $entity_id . '","' . $focus->record_id . '","' . $module . '","' . $popupMode . '",' . $callBack . ');\'>' . textlength_check($temp_val) . '</a>';
						}
					}
				} elseif ($popuptype == "formname_specific") {
					$slashes_temp_val = popup_from_html($temp_val);
					$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);
					$count = counterValue();
					$value = '<a href="javascript:window.close();" onclick=\'set_return_formname_specific("' . $_REQUEST['form'] . '", "' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '");\'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
				} elseif ($popuptype == "inventory_prod") {
					$row_id = $_REQUEST['curr_row'];

					//To get all the tax types and values and pass it to product details
					$tax_str = '';
					$tax_details = getAllTaxes();
					for ($tax_count = 0; $tax_count < count($tax_details); $tax_count++) {
						$tax_str .= $tax_details[$tax_count]['taxname'] . '=' . $tax_details[$tax_count]['percentage'] . ',';
					}
					$tax_str = trim($tax_str, ',');
					$rate = $user_info['conv_rate'];
					if (getFieldVisibilityPermission('Products', $current_user->id, 'unit_price') == '0') {
						$unitprice = $adb->query_result($list_result, $list_result_count, 'unit_price');
						if ($_REQUEST['currencyid'] != null) {
							$prod_prices = getPricesForProducts($_REQUEST['currencyid'], array($entity_id));
							$unitprice = $prod_prices[$entity_id];
						}
					} else {
						$unitprice = '';
					}
					$sub_products = '';
					$sub_prod = '';
					$sub_prod_query = $adb->pquery("SELECT vtiger_products.productid,vtiger_products.productname,vtiger_products.qtyinstock,vtiger_crmentity.description from vtiger_products INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid INNER JOIN vtiger_seproductsrel on vtiger_seproductsrel.crmid=vtiger_products.productid WHERE vtiger_seproductsrel.productid=? and vtiger_seproductsrel.setype='Products'", array($entity_id));
					for ($i = 0; $i < $adb->num_rows($sub_prod_query); $i++) {
						//$sub_prod=array();
						$id = $adb->query_result($sub_prod_query, $i, "productid");
						$str_sep = '';
						if ($i > 0)
							$str_sep = ":";
						$sub_products .= $str_sep . $id;
						$sub_prod .= $str_sep . " - " . $adb->query_result($sub_prod_query, $i, "productname");
					}

					$sub_det = $sub_products . "::" . str_replace(":", "<br>", $sub_prod);
					$qty_stock = $adb->query_result($list_result, $list_result_count, 'qtyinstock');

					//fix for T6943

					$slashes_temp_val = popup_from_html($field_val);
					$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);
					$description = popup_from_html($adb->query_result($list_result, $list_result_count, 'description'));
					$slashes_temp_desc = decode_html(htmlspecialchars($description, ENT_QUOTES, $default_charset));

					$slashes_desc = str_replace(array("\r", "\n"), array('\r', '\n'), $slashes_temp_desc);
					$tmp_arr = array("entityid" => $entity_id, "prodname" => "" . stripslashes(decode_html(nl2br($slashes_temp_val))) . "", "unitprice" => "$unitprice", "qtyinstk" => "$qty_stock", "taxstring" => "$tax_str", "rowid" => "$row_id", "desc" => "$slashes_desc", "subprod_ids" => "$sub_det");
					require_once('include/Zend/Json.php');
					$prod_arr = Zend_Json::encode($tmp_arr);
					$value = '<a href="javascript:window.close();" id=\'popup_product_' . $entity_id . '\' onclick=\'set_return_inventory("' . $entity_id . '", "' . decode_html(nl2br($slashes_temp_val)) . '", "' . $unitprice . '", "' . $qty_stock . '","' . $tax_str . '","' . $row_id . '","' . $slashes_desc . '","' . $sub_det . '");\' vt_prod_arr=\'' . $prod_arr . '\' >' . textlength_check($temp_val) . '</a>';
				}
				elseif ($popuptype == "inventory_prod_po") {
					$row_id = $_REQUEST['curr_row'];

					//To get all the tax types and values and pass it to product details
					$tax_str = '';
					$tax_details = getAllTaxes();
					for ($tax_count = 0; $tax_count < count($tax_details); $tax_count++) {
						$tax_str .= $tax_details[$tax_count]['taxname'] . '=' . $tax_details[$tax_count]['percentage'] . ',';
					}
					$tax_str = trim($tax_str, ',');
					$rate = $user_info['conv_rate'];

					if (getFieldVisibilityPermission($module, $current_user->id, 'unit_price') == '0') {
						$unitprice = $adb->query_result($list_result, $list_result_count, 'unit_price');
						if ($_REQUEST['currencyid'] != null) {
							$prod_prices = getPricesForProducts($_REQUEST['currencyid'], array($entity_id), $module);
							$unitprice = $prod_prices[$entity_id];
						}
					} else {
						$unitprice = '';
					}
					$sub_products = '';
					$sub_prod = '';
					$sub_prod_query = $adb->pquery("SELECT vtiger_products.productid,vtiger_products.productname,vtiger_products.qtyinstock,vtiger_crmentity.description from vtiger_products INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid INNER JOIN vtiger_seproductsrel on vtiger_seproductsrel.crmid=vtiger_products.productid WHERE vtiger_seproductsrel.productid=? and vtiger_seproductsrel.setype='Products'", array($entity_id));
					for ($i = 0; $i < $adb->num_rows($sub_prod_query); $i++) {
						//$sub_prod=array();
						$id = $adb->query_result($sub_prod_query, $i, "productid");
						$str_sep = '';
						if ($i > 0)
							$str_sep = ":";
						$sub_products .= $str_sep . $id;
						$sub_prod .= $str_sep . " - $id." . $adb->query_result($sub_prod_query, $i, "productname");
					}

					$sub_det = $sub_products . "::" . str_replace(":", "<br>", $sub_prod);

					$slashes_temp_val = popup_from_html($field_val);
					$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);
					$description = popup_from_html($adb->query_result($list_result, $list_result_count, 'description'));
					$slashes_temp_desc = decode_html(htmlspecialchars($description, ENT_QUOTES, $default_charset));

					$slashes_desc = str_replace(array("\r", "\n"), array('\r', '\n'), $slashes_temp_desc);
					$tmp_arr = array("entityid" => $entity_id, "prodname" => "" . stripslashes(decode_html(nl2br($slashes_temp_val))) . "", "unitprice" => "$unitprice", "qtyinstk" => "$qty_stock", "taxstring" => "$tax_str", "rowid" => "$row_id", "desc" => "$slashes_desc", "subprod_ids" => "$sub_det");
					require_once('include/Zend/Json.php');
					$prod_arr = Zend_Json::encode($tmp_arr);
					$value = '<a href="javascript:window.close();" id=\'popup_product_' . $entity_id . '\' onclick=\'set_return_inventory_po("' . $entity_id . '", "' . decode_html(nl2br($slashes_temp_val)) . '", "' . $unitprice . '", "' . $tax_str . '","' . $row_id . '","' . $slashes_desc . '","' . $sub_det . '"); \'  vt_prod_arr=\'' . $prod_arr . '\' >' . textlength_check($temp_val) . '</a>';
				}
				elseif ($popuptype == "inventory_service") {
					$row_id = $_REQUEST['curr_row'];

					//To get all the tax types and values and pass it to product details
					$tax_str = '';
					$tax_details = getAllTaxes();
					for ($tax_count = 0; $tax_count < count($tax_details); $tax_count++) {
						$tax_str .= $tax_details[$tax_count]['taxname'] . '=' . $tax_details[$tax_count]['percentage'] . ',';
					}
					$tax_str = trim($tax_str, ',');
					$rate = $user_info['conv_rate'];
					if (getFieldVisibilityPermission('Services', $current_user->id, 'unit_price') == '0') {
						$unitprice = $adb->query_result($list_result, $list_result_count, 'unit_price');
						if ($_REQUEST['currencyid'] != null) {
							$prod_prices = getPricesForProducts($_REQUEST['currencyid'], array($entity_id), $module);
							$unitprice = $prod_prices[$entity_id];
						}
					} else {
						$unitprice = '';
					}

					$slashes_temp_val = popup_from_html($field_val);
					$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);
					$description = popup_from_html($adb->query_result($list_result, $list_result_count, 'description'));
					$slashes_temp_desc = decode_html(htmlspecialchars($description, ENT_QUOTES, $default_charset));

					$slashes_desc = str_replace(array("\r", "\n"), array('\r', '\n'), $slashes_temp_desc);
					$tmp_arr = array("entityid" => $entity_id, "prodname" => "" . stripslashes(decode_html(nl2br($slashes_temp_val))) . "", "unitprice" => "$unitprice", "taxstring" => "$tax_str", "rowid" => "$row_id", "desc" => "$slashes_desc");
					require_once('include/Zend/Json.php');
					$prod_arr = Zend_Json::encode($tmp_arr);

					$value = '<a href="javascript:window.close();" id=\'popup_product_' . $entity_id . '\' onclick=\'set_return_inventory("' . $entity_id . '", "' . decode_html(nl2br($slashes_temp_val)) . '", "' . $unitprice . '", "' . $tax_str . '","' . $row_id . '","' . $slashes_desc . '");\'  vt_prod_arr=\'' . $prod_arr . '\' >' . textlength_check($temp_val) . '</a>';
				} elseif ($popuptype == "inventory_pb") {

					$prod_id = $_REQUEST['productid'];
					$flname = $_REQUEST['fldname'];
					$listprice = getListPrice($prod_id, $entity_id);

					$temp_val = popup_from_html($temp_val);
					$count = counterValue();
					$value = '<a href="javascript:window.close();" onclick=\'set_return_inventory_pb("' . $listprice . '", "' . $flname . '"); \'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
				} elseif ($popuptype == "specific_account_address") {
					require_once('modules/Accounts/Accounts.php');
					$acct_focus = new Accounts();
					$acct_focus->retrieve_entity_info($entity_id, "Accounts");
					$slashes_temp_val = popup_from_html($temp_val);
					$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);
					$xyz = array('bill_street', 'bill_city', 'bill_code', 'bill_pobox', 'bill_country', 'bill_state', 'ship_street', 'ship_city', 'ship_code', 'ship_pobox', 'ship_country', 'ship_state');
					for ($i = 0; $i < 12; $i++) {
						if (getFieldVisibilityPermission($module, $current_user->id, $xyz[$i]) == '0') {
							$acct_focus->column_fields[$xyz[$i]] = $acct_focus->column_fields[$xyz[$i]];
						}
						else
							$acct_focus->column_fields[$xyz[$i]] = '';
					}
					$bill_street = str_replace(array("\r", "\n"), array('\r', '\n'), popup_decode_html($acct_focus->column_fields['bill_street']));
					$ship_street = str_replace(array("\r", "\n"), array('\r', '\n'), popup_decode_html($acct_focus->column_fields['ship_street']));
					$count = counterValue();
					$value = '<a href="javascript:window.close();" onclick=\'set_return_address("' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '", "' . $bill_street . '", "' . $ship_street . '", "' . popup_decode_html($acct_focus->column_fields['bill_city']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_city']) . '", "' . popup_decode_html($acct_focus->column_fields['bill_state']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_state']) . '", "' . popup_decode_html($acct_focus->column_fields['bill_code']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_code']) . '", "' . popup_decode_html($acct_focus->column_fields['bill_country']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_country']) . '","' . popup_decode_html($acct_focus->column_fields['bill_pobox']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_pobox']) . '");\'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
				}
				elseif ($popuptype == "specific_contact_account_address") {
					require_once('modules/Accounts/Accounts.php');
					$acct_focus = new Accounts();
					$acct_focus->retrieve_entity_info($entity_id, "Accounts");

					$slashes_temp_val = popup_from_html($temp_val);
					$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);

					$bill_street = str_replace(array("\r", "\n"), array('\r', '\n'), popup_decode_html($acct_focus->column_fields['bill_street']));
					$ship_street = str_replace(array("\r", "\n"), array('\r', '\n'), popup_decode_html($acct_focus->column_fields['ship_street']));
					$count = counterValue();
					$value = '<a href="javascript:window.close();" onclick=\'set_return_contact_address("' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '", "' . $bill_street . '", "' . $ship_street . '", "' . popup_decode_html($acct_focus->column_fields['bill_city']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_city']) . '", "' . popup_decode_html($acct_focus->column_fields['bill_state']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_state']) . '", "' . popup_decode_html($acct_focus->column_fields['bill_code']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_code']) . '", "' . popup_decode_html($acct_focus->column_fields['bill_country']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_country']) . '","' . popup_decode_html($acct_focus->column_fields['bill_pobox']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_pobox']) . '");\'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
				} elseif ($popuptype == "specific_potential_account_address") {
					$slashes_temp_val = popup_from_html($temp_val);
					$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);

					// For B2C support, Potential was enabled to be linked to Contacts also.
					// Hence we need case handling for it.
					$relatedid = $adb->query_result($list_result, $list_result_count, "related_to");
					$relatedentity = getSalesEntityType($relatedid);
					if ($relatedentity == 'Accounts') {
						require_once('modules/Accounts/Accounts.php');
						$acct_focus = new Accounts();
						$acct_focus->retrieve_entity_info($relatedid, "Accounts");
						$account_name = getAccountName($relatedid);

						$slashes_account_name = popup_from_html($account_name);
						$slashes_account_name = htmlspecialchars($slashes_account_name, ENT_QUOTES, $default_charset);

						$xyz = array('bill_street', 'bill_city', 'bill_code', 'bill_pobox', 'bill_country', 'bill_state', 'ship_street', 'ship_city', 'ship_code', 'ship_pobox', 'ship_country', 'ship_state');
						for ($i = 0; $i < 12; $i++) {
							if (getFieldVisibilityPermission('Accounts', $current_user->id, $xyz[$i]) == '0') {
								$acct_focus->column_fields[$xyz[$i]] = $acct_focus->column_fields[$xyz[$i]];
							}
							else
								$acct_focus->column_fields[$xyz[$i]] = '';
						}
						$bill_street = str_replace(array("\r", "\n"), array('\r', '\n'), popup_decode_html($acct_focus->column_fields['bill_street']));
						$ship_street = str_replace(array("\r", "\n"), array('\r', '\n'), popup_decode_html($acct_focus->column_fields['ship_street']));
						$count = counterValue();
						$value = '<a href="javascript:window.close();" onclick=\'set_return_address("' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '", "' . $relatedid . '", "' . nl2br(decode_html($slashes_account_name)) . '", "' . $bill_street . '", "' . $ship_street . '", "' . popup_decode_html($acct_focus->column_fields['bill_city']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_city']) . '", "' . popup_decode_html($acct_focus->column_fields['bill_state']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_state']) . '", "' . popup_decode_html($acct_focus->column_fields['bill_code']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_code']) . '", "' . popup_decode_html($acct_focus->column_fields['bill_country']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_country']) . '","' . popup_decode_html($acct_focus->column_fields['bill_pobox']) . '", "' . popup_decode_html($acct_focus->column_fields['ship_pobox']) . '");\'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
					} else if ($relatedentity == 'Contacts') {

						require_once('modules/Contacts/Contacts.php');
						$displayValueArray = getEntityName('Contacts', $relatedid);
						if (!empty($displayValueArray)) {
							foreach ($displayValueArray as $key => $field_value) {
								$contact_name = $field_value;
							}
						} else {
							$contact_name = '';
						}

						$slashes_contact_name = popup_from_html($contact_name);
						$slashes_contact_name = htmlspecialchars($slashes_contact_name, ENT_QUOTES, $default_charset);
						$count = counterValue();
						$value = '<a href="javascript:window.close();" onclick=\'set_return_contact("' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '", "' . $relatedid . '", "' . nl2br(decode_html($slashes_contact_name)) . '");\'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
					} else {
						$value = $temp_val;
					}
				}
				//added by rdhital/Raju for better emails
				elseif ($popuptype == "set_return_emails") {
					if ($module == 'Accounts') {
						$name = $adb->query_result($list_result, $list_result_count, 'accountname');
						$accid = $adb->query_result($list_result, $list_result_count, 'accountid');
						if (CheckFieldPermission('email1', $module) == "true") {
							$emailaddress = $adb->query_result($list_result, $list_result_count, "email1");
							$email_check = 1;
						}
						else
							$email_check = 0;
						if ($emailaddress == '') {
							if (CheckFieldPermission('email2', $module) == 'true') {
								$emailaddress2 = $adb->query_result($list_result, $list_result_count, "email2");
								$email_check = 2;
							} else {
								if ($email_check == 1)
									$email_check = 4;
								else
									$email_check = 3;
							}
						}
						$querystr = "SELECT fieldid,fieldlabel,columnname FROM vtiger_field WHERE tabid=? and uitype=13 and vtiger_field.presence in (0,2)";
						$queryres = $adb->pquery($querystr, array(getTabid($module)));
						//Change this index 0 - to get the vtiger_fieldid based on email1 or email2
						$fieldid = $adb->query_result($queryres, 0, 'fieldid');

						$slashes_name = popup_from_html($name);
						$slashes_name = htmlspecialchars($slashes_name, ENT_QUOTES, $default_charset);
						$count = counterValue();

						$value = '<a href="javascript:window.close();" onclick=\'return set_return_emails(' . $entity_id . ',' . $fieldid . ',"' . decode_html($slashes_name) . '","' . $emailaddress . '","' . $emailaddress2 . '","' . $email_check . '"); \'id = ' . $count . '>' . textlength_check($name) . '</a>';
					}elseif ($module == 'Vendors') {
						$name = $adb->query_result($list_result, $list_result_count, 'vendorname');
						$venid = $adb->query_result($list_result, $list_result_count, 'vendorid');
						if (CheckFieldPermission('email', $module) == "true") {
							$emailaddress = $adb->query_result($list_result, $list_result_count, "email");
							$email_check = 1;
						}
						else
							$email_check = 0;
						$querystr = "SELECT fieldid,fieldlabel,columnname FROM vtiger_field WHERE tabid=? and uitype=13 and vtiger_field.presence in (0,2)";
						$queryres = $adb->pquery($querystr, array(getTabid($module)));
						//Change this index 0 - to get the vtiger_fieldid based on email1 or email2
						$fieldid = $adb->query_result($queryres, 0, 'fieldid');

						$slashes_name = popup_from_html($name);
						$slashes_name = htmlspecialchars($slashes_name, ENT_QUOTES, $default_charset);
						$count = counterValue();
						$value = '<a href="javascript:window.close();" onclick=\'return set_return_emails(' . $entity_id . ',' . $fieldid . ',"' . decode_html($slashes_name) . '","' . $emailaddress . '","' . $emailaddress2 . '","' . $email_check . '"); \'id = ' . $count . '>' . textlength_check($name) . '</a>';
					}elseif ($module == 'Contacts' || $module == 'Leads') {
						$name = getFullNameFromQResult($list_result, $list_result_count, $module);
						if (CheckFieldPermission('email', $module) == "true") {
							$emailaddress = $adb->query_result($list_result, $list_result_count, "email");
							$email_check = 1;
						}
						else
							$email_check = 0;
						if ($emailaddress == '') {
							if (CheckFieldPermission('secondaryemail', $module) == 'true') {
								$emailaddress2 = $adb->query_result($list_result, $list_result_count, "secondaryemail");
								$email_check = 2;
							} else {
								if ($email_check == 1)
									$email_check = 4;
								else
									$email_check = 3;
							}
						}

						$querystr = "SELECT fieldid,fieldlabel,columnname FROM vtiger_field WHERE tabid=? and uitype=13 and vtiger_field.presence in (0,2)";
						$queryres = $adb->pquery($querystr, array(getTabid($module)));
						//Change this index 0 - to get the vtiger_fieldid based on email or secondaryemail
						$fieldid = $adb->query_result($queryres, 0, 'fieldid');

						$slashes_name = popup_from_html($name);
						$slashes_name = htmlspecialchars($slashes_name, ENT_QUOTES, $default_charset);
						$count = counterValue();
						$value = '<a href="javascript:window.close();" onclick=\'return set_return_emails(' . $entity_id . ',' . $fieldid . ',"' . decode_html($slashes_name) . '","' . $emailaddress . '","' . $emailaddress2 . '","' . $email_check . '"); \'id = ' . $count . '>' . $name . '</a>';
					}else {
						$name = getFullNameFromQResult($list_result, $list_result_count, $module);
						$emailaddress = $adb->query_result($list_result, $list_result_count, "email1");

						$slashes_name = popup_from_html($name);
						$slashes_name = htmlspecialchars($slashes_name, ENT_QUOTES, $default_charset);
						$email_check = 1;
						$count = counterValue();
						$value = '<a href="javascript:window.close();" onclick=\'return set_return_emails(' . $entity_id . ',-1,"' . decode_html($slashes_name) . '","' . $emailaddress . '","' . $emailaddress2 . '","' . $email_check . '"); \'id = ' . $count . '>' . textlength_check($name) . '</a>';
					}
				} elseif ($popuptype == "specific_vendor_address") {
					require_once('modules/Vendors/Vendors.php');
					$acct_focus = new Vendors();
					$acct_focus->retrieve_entity_info($entity_id, "Vendors");

					$slashes_temp_val = popup_from_html($temp_val);
					$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);
					$xyz = array('street', 'city', 'postalcode', 'pobox', 'country', 'state');
					for ($i = 0; $i < 6; $i++) {
						if (getFieldVisibilityPermission($module, $current_user->id, $xyz[$i]) == '0') {
							$acct_focus->column_fields[$xyz[$i]] = $acct_focus->column_fields[$xyz[$i]];
						}
						else
							$acct_focus->column_fields[$xyz[$i]] = '';
					}
					$bill_street = str_replace(array("\r", "\n"), array('\r', '\n'), popup_decode_html($acct_focus->column_fields['street']));
					$count = counterValue();
					$value = '<a href="javascript:window.close();" onclick=\'set_return_address("' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '", "' . $bill_street . '", "' . popup_decode_html($acct_focus->column_fields['city']) . '", "' . popup_decode_html($acct_focus->column_fields['state']) . '", "' . popup_decode_html($acct_focus->column_fields['postalcode']) . '", "' . popup_decode_html($acct_focus->column_fields['country']) . '","' . popup_decode_html($acct_focus->column_fields['pobox']) . '");\'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
				}
				elseif ($popuptype == "specific_campaign") {
					$slashes_temp_val = popup_from_html($temp_val);
					$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);
					$count = counterValue();
					$value = '<a href="javascript:window.close();" onclick=\'set_return_specific_campaign("' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '");\'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
				} else {
					if ($colname == "lastname") {
						$temp_val = getFullNameFromQResult($list_result, $list_result_count, $module);
					} elseif ($module == 'Users' && $fieldname == 'last_name') {
						$temp_val = getFullNameFromQResult($list_result, $list_result_count, $module);
					}
					$slashes_temp_val = popup_from_html($temp_val);
					$slashes_temp_val = htmlspecialchars($slashes_temp_val, ENT_QUOTES, $default_charset);

					$log->debug("Exiting getValue method ...");
					if ($_REQUEST['maintab'] == 'Calendar') {
						$count = counterValue();
						$value = '<a href="javascript:window.close();" onclick=\'set_return_todo("' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '");\'id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
					} else {
						$value = '<a href="javascript:window.close();" onclick=\'set_return("' . $entity_id . '", "' . nl2br(decode_html($slashes_temp_val)) . '");\'';
						if (empty($_REQUEST['forfield']) && $focus->popup_type != 'detailview') {
							$count = counterValue();
							$value .= " id='$count' ";
						}
						$value .= '>' . textlength_check($temp_val) . '</a>';
					}
				}
			} else {
				if (($module == "Leads" && $colname == "lastname") || ($module == "Contacts" && $colname == "lastname")) {
					$count = counterValue();
					$value = '<a href="index.php?action=DetailView&module=' . $module . '&record=' . $entity_id . '&parenttab=' . $tabname . '" id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
				} elseif ($module == "Calendar") {
					$actvity_type = $adb->query_result($list_result, $list_result_count, 'activitytype');
					$actvity_type = ($actvity_type != '') ? $actvity_type : $adb->query_result($list_result, $list_result_count, 'type');
					if ($actvity_type == "Task") {
						$count = counterValue();
						$value = '<a href="index.php?action=DetailView&module=' . $module . '&record=' . $entity_id . '&activity_mode=Task&parenttab=' . $tabname . '" id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
					} else {
						$count = counterValue();
						$value = '<a href="index.php?action=DetailView&module=' . $module . '&record=' . $entity_id . '&activity_mode=Events&parenttab=' . $tabname . '" id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
					}
				} elseif ($module == "Vendors") {
					$count = counterValue();

					$value = '<a href="index.php?action=DetailView&module=Vendors&record=' . $entity_id . '&parenttab=' . $tabname . '" id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
				} elseif ($module == "PriceBooks") {
					$count = counterValue();
					$value = '<a href="index.php?action=DetailView&module=PriceBooks&record=' . $entity_id . '&parenttab=' . $tabname . '" id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
				} elseif ($module == "SalesOrder") {

					$count = counterValue();
					$value = '<a href="index.php?action=DetailView&module=SalesOrder&record=' . $entity_id . '&parenttab=' . $tabname . '" id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
				} elseif ($module == 'Emails') {
					$value = $temp_val;
				} elseif (($module == "Users" && $colname == "last_name")) {
					$temp_val = getFullNameFromQResult($list_result, $list_result_count, $module);
					$value = '<a href="index.php?action=DetailView&module=' . $module . '&record=' . $entity_id . '&parenttab=' . $tabname . '">' . textlength_check($temp_val) . '</a>';
				} else {
					$count = counterValue();
					$value = '<a href="index.php?action=DetailView&module=' . $module . '&record=' . $entity_id . '&parenttab=' . $tabname . '" id = ' . $count . '>' . textlength_check($temp_val) . '</a>';
				}
			}
		} elseif ($module == 'Calendar' && ($fieldname == 'time_start' ||
				$fieldname == 'time_end')) {
			$dateField = 'date_start';
			if ($fieldname == 'time_end') {
				$dateField = 'due_date';
			}
			$type = $adb->query_result($list_result, $list_result_count, 'activitytype');
			if (empty($type)) {
				$type = $adb->query_result($list_result, $list_result_count, 'type');
			}
			if ($type == 'Task' && $fieldname == 'time_end') {
				$value = '--';
			} else {
				$date_val = $adb->query_result($list_result, $list_result_count, $dateField);
				$date = new DateTimeField($date_val . ' ' . $temp_val);
				$value = $date->getDisplayTime();
				$value = textlength_check($value);
			}
		} else {
			$value = $temp_val;
			$value = textlength_check($value);
		}
	}

	// Mike Crowe Mod --------------------------------------------------------Make right justified and vtiger_currency value
	if (in_array($uitype, array(71, 72, 7, 9, 90))) {
		$value = '<span align="right">' . $value . '</div>';
	}
	$log->debug("Exiting getValue method ...");
	return $value;
}

/** Function to get the list query for a module
 * @param $module -- module name:: Type string
 * @param $where -- where:: Type string
 * @returns $query -- query:: Type query
 */
function getListQuery($module, $where = '') {
	global $log;
	$log->debug("Entering getListQuery(" . $module . "," . $where . ") method ...");

	global $current_user;
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	require('user_privileges/sharing_privileges_' . $current_user->id . '.php');
	$tab_id = getTabid($module);
	$userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'vtiger_users.first_name', 'last_name' =>
				'vtiger_users.last_name'), 'Users');
	switch ($module) {
		Case "HelpDesk":
			$query = "SELECT vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
			vtiger_troubletickets.title, vtiger_troubletickets.status,
			vtiger_troubletickets.priority, vtiger_troubletickets.parent_id,
			vtiger_contactdetails.contactid, vtiger_contactdetails.firstname,
			vtiger_contactdetails.lastname, vtiger_account.accountid,
			vtiger_account.accountname, vtiger_ticketcf.*, vtiger_troubletickets.ticket_no
			FROM vtiger_troubletickets
			INNER JOIN vtiger_ticketcf
				ON vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_contactdetails
				ON vtiger_troubletickets.parent_id = vtiger_contactdetails.contactid
			LEFT JOIN vtiger_account
				ON vtiger_account.accountid = vtiger_troubletickets.parent_id
			LEFT JOIN vtiger_users
				ON vtiger_crmentity.smownerid = vtiger_users.id
			LEFT JOIN vtiger_products
				ON vtiger_products.productid = vtiger_troubletickets.product_id";
			$query .= ' ' . getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;

		Case "Accounts":
			//Query modified to sort by assigned to
			$query = "SELECT vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
			vtiger_account.accountname, vtiger_account.email1,
			vtiger_account.email2, vtiger_account.website, vtiger_account.phone,
			vtiger_accountbillads.bill_city,
			vtiger_accountscf.*
			FROM vtiger_account
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_account.accountid
			INNER JOIN vtiger_accountbillads
				ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid
			INNER JOIN vtiger_accountshipads
				ON vtiger_account.accountid = vtiger_accountshipads.accountaddressid
			INNER JOIN vtiger_accountscf
				ON vtiger_account.accountid = vtiger_accountscf.accountid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_account vtiger_account2
				ON vtiger_account.parentid = vtiger_account2.accountid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;

		Case "Potentials":
			//Query modified to sort by assigned to
			$query = "SELECT vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
			vtiger_account.accountname,
			vtiger_potential.related_to, vtiger_potential.potentialname,
			vtiger_potential.sales_stage, vtiger_potential.amount,
			vtiger_potential.currency, vtiger_potential.closingdate,
			vtiger_potential.typeofrevenue,
			vtiger_potentialscf.*
			FROM vtiger_potential
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_potential.potentialid
			INNER JOIN vtiger_potentialscf
				ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid
			LEFT JOIN vtiger_account
				ON vtiger_potential.related_to = vtiger_account.accountid
			LEFT JOIN vtiger_contactdetails
				ON vtiger_potential.related_to = vtiger_contactdetails.contactid
			LEFT JOIN vtiger_campaign
				ON vtiger_campaign.campaignid = vtiger_potential.campaignid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;

		Case "Leads":
			$query = "SELECT vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
			vtiger_leaddetails.firstname, vtiger_leaddetails.lastname,
			vtiger_leaddetails.company, vtiger_leadaddress.phone,
			vtiger_leadsubdetails.website, vtiger_leaddetails.email,
			vtiger_leadscf.*
			FROM vtiger_leaddetails
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
			INNER JOIN vtiger_leadsubdetails
				ON vtiger_leadsubdetails.leadsubscriptionid = vtiger_leaddetails.leadid
			INNER JOIN vtiger_leadaddress
				ON vtiger_leadaddress.leadaddressid = vtiger_leadsubdetails.leadsubscriptionid
			INNER JOIN vtiger_leadscf
				ON vtiger_leaddetails.leadid = vtiger_leadscf.leadid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE vtiger_crmentity.deleted = 0 AND vtiger_leaddetails.converted = 0 " . $where;
			break;
		Case "Products":
			$query = "SELECT vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.description, vtiger_products.*, vtiger_productcf.*
			FROM vtiger_products
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_products.productid
			INNER JOIN vtiger_productcf
				ON vtiger_products.productid = vtiger_productcf.productid
			LEFT JOIN vtiger_vendor
				ON vtiger_vendor.vendorid = vtiger_products.vendor_id
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid";
			if ((isset($_REQUEST["from_dashboard"]) && $_REQUEST["from_dashboard"] == true) && (isset($_REQUEST["type"]) && $_REQUEST["type"] == "dbrd"))
				$query .= " INNER JOIN vtiger_inventoryproductrel on vtiger_inventoryproductrel.productid = vtiger_products.productid";

			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= " WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;
		Case "Documents":
			$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,vtiger_crmentity.crmid, vtiger_crmentity.modifiedtime,
			vtiger_crmentity.smownerid,vtiger_attachmentsfolder.*,vtiger_notes.*
			FROM vtiger_notes
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_notes.notesid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_attachmentsfolder
				ON vtiger_notes.folderid = vtiger_attachmentsfolder.folderid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;
		Case "Contacts":
			//Query modified to sort by assigned to
			$query = "SELECT vtiger_contactdetails.firstname, vtiger_contactdetails.lastname,
			vtiger_contactdetails.title, vtiger_contactdetails.accountid,
			vtiger_contactdetails.email, vtiger_contactdetails.phone,
			vtiger_crmentity.smownerid, vtiger_crmentity.crmid
			FROM vtiger_contactdetails
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			INNER JOIN vtiger_contactaddress
				ON vtiger_contactaddress.contactaddressid = vtiger_contactdetails.contactid
			INNER JOIN vtiger_contactsubdetails
				ON vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid
			INNER JOIN vtiger_contactscf
				ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
			LEFT JOIN vtiger_account
				ON vtiger_account.accountid = vtiger_contactdetails.accountid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_contactdetails vtiger_contactdetails2
				ON vtiger_contactdetails.reportsto = vtiger_contactdetails2.contactid
			LEFT JOIN vtiger_customerdetails
				ON vtiger_customerdetails.customerid = vtiger_contactdetails.contactid";
			if ((isset($_REQUEST["from_dashboard"]) && $_REQUEST["from_dashboard"] == true) &&
					(isset($_REQUEST["type"]) && $_REQUEST["type"] == "dbrd")) {
				$query .= " INNER JOIN vtiger_campaigncontrel on vtiger_campaigncontrel.contactid = " .
						"vtiger_contactdetails.contactid";
			}
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;
		Case "Calendar":

			$query = "SELECT vtiger_activity.activityid as act_id,vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.setype,
		vtiger_activity.*,
		vtiger_contactdetails.lastname, vtiger_contactdetails.firstname,
		vtiger_contactdetails.contactid,
		vtiger_account.accountid, vtiger_account.accountname
		FROM vtiger_activity
		LEFT JOIN vtiger_activitycf
			ON vtiger_activitycf.activityid = vtiger_activity.activityid
		LEFT JOIN vtiger_cntactivityrel
			ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
		LEFT JOIN vtiger_contactdetails
			ON vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
		LEFT JOIN vtiger_seactivityrel
			ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
		LEFT OUTER JOIN vtiger_activity_reminder
			ON vtiger_activity_reminder.activity_id = vtiger_activity.activityid
		LEFT JOIN vtiger_crmentity
			ON vtiger_crmentity.crmid = vtiger_activity.activityid
		LEFT JOIN vtiger_users
			ON vtiger_users.id = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_groups
			ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_users vtiger_users2
			ON vtiger_crmentity.modifiedby = vtiger_users2.id
		LEFT JOIN vtiger_groups vtiger_groups2
			ON vtiger_crmentity.modifiedby = vtiger_groups2.groupid
		LEFT OUTER JOIN vtiger_account
			ON vtiger_account.accountid = vtiger_contactdetails.accountid
		LEFT OUTER JOIN vtiger_leaddetails
	       		ON vtiger_leaddetails.leadid = vtiger_seactivityrel.crmid
		LEFT OUTER JOIN vtiger_account vtiger_account2
	        	ON vtiger_account2.accountid = vtiger_seactivityrel.crmid
		LEFT OUTER JOIN vtiger_potential
	       		ON vtiger_potential.potentialid = vtiger_seactivityrel.crmid
		LEFT OUTER JOIN vtiger_troubletickets
	       		ON vtiger_troubletickets.ticketid = vtiger_seactivityrel.crmid
		LEFT OUTER JOIN vtiger_salesorder
			ON vtiger_salesorder.salesorderid = vtiger_seactivityrel.crmid
		LEFT OUTER JOIN vtiger_purchaseorder
			ON vtiger_purchaseorder.purchaseorderid = vtiger_seactivityrel.crmid
		LEFT OUTER JOIN vtiger_quotes
			ON vtiger_quotes.quoteid = vtiger_seactivityrel.crmid
		LEFT OUTER JOIN vtiger_invoice
	                ON vtiger_invoice.invoiceid = vtiger_seactivityrel.crmid
		LEFT OUTER JOIN vtiger_campaign
		ON vtiger_campaign.campaignid = vtiger_seactivityrel.crmid";

			//added to fix #5135
			if (isset($_REQUEST['from_homepage']) && ($_REQUEST['from_homepage'] ==
					"upcoming_activities" || $_REQUEST['from_homepage'] == "pending_activities")) {
				$query.=" LEFT OUTER JOIN vtiger_recurringevents
			             ON vtiger_recurringevents.activityid=vtiger_activity.activityid";
			}
			//end

			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query.=" WHERE vtiger_crmentity.deleted = 0 AND activitytype != 'Emails' " . $where;
			break;
		Case "Emails":
			$query = "SELECT DISTINCT vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
			vtiger_activity.activityid, vtiger_activity.subject,
			vtiger_activity.date_start,
			vtiger_contactdetails.lastname, vtiger_contactdetails.firstname,
			vtiger_contactdetails.contactid
			FROM vtiger_activity
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_activity.activityid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_seactivityrel
				ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
			LEFT JOIN vtiger_contactdetails
				ON vtiger_contactdetails.contactid = vtiger_seactivityrel.crmid
			LEFT JOIN vtiger_cntactivityrel
				ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
				AND vtiger_cntactivityrel.contactid = vtiger_cntactivityrel.contactid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_salesmanactivityrel
				ON vtiger_salesmanactivityrel.activityid = vtiger_activity.activityid
			LEFT JOIN vtiger_emaildetails
				ON vtiger_emaildetails.emailid = vtiger_activity.activityid
			WHERE vtiger_activity.activitytype = 'Emails'";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "AND vtiger_crmentity.deleted = 0 " . $where;
			break;
		Case "Faq":
			$query = "SELECT vtiger_crmentity.crmid, vtiger_crmentity.createdtime, vtiger_crmentity.modifiedtime,
			vtiger_faq.*
			FROM vtiger_faq
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_faq.id
			LEFT JOIN vtiger_products
				ON vtiger_faq.product_id = vtiger_products.productid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;

		Case "Vendors":
			$query = "SELECT vtiger_crmentity.crmid, vtiger_vendor.*
			FROM vtiger_vendor
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_vendor.vendorid
			INNER JOIN vtiger_vendorcf
				ON vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
			WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;
		Case "PriceBooks":
			$query = "SELECT vtiger_crmentity.crmid, vtiger_pricebook.*, vtiger_currency_info.currency_name
			FROM vtiger_pricebook
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_pricebook.pricebookid
			INNER JOIN vtiger_pricebookcf
				ON vtiger_pricebook.pricebookid = vtiger_pricebookcf.pricebookid
			LEFT JOIN vtiger_currency_info
				ON vtiger_pricebook.currency_id = vtiger_currency_info.id
			WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;
		Case "Quotes":
			//Query modified to sort by assigned to
			$query = "SELECT vtiger_crmentity.*,
			vtiger_quotes.*,
			vtiger_quotesbillads.*,
			vtiger_quotesshipads.*,
			vtiger_potential.potentialname,
			vtiger_account.accountname,
			vtiger_currency_info.currency_name
			FROM vtiger_quotes
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_quotes.quoteid
			INNER JOIN vtiger_quotesbillads
				ON vtiger_quotes.quoteid = vtiger_quotesbillads.quotebilladdressid
			INNER JOIN vtiger_quotesshipads
				ON vtiger_quotes.quoteid = vtiger_quotesshipads.quoteshipaddressid
			LEFT JOIN vtiger_quotescf
				ON vtiger_quotes.quoteid = vtiger_quotescf.quoteid
			LEFT JOIN vtiger_currency_info
				ON vtiger_quotes.currency_id = vtiger_currency_info.id
			LEFT OUTER JOIN vtiger_account
				ON vtiger_account.accountid = vtiger_quotes.accountid
			LEFT OUTER JOIN vtiger_potential
				ON vtiger_potential.potentialid = vtiger_quotes.potentialid
			LEFT JOIN vtiger_contactdetails
				ON vtiger_contactdetails.contactid = vtiger_quotes.contactid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users as vtiger_usersQuotes
			        ON vtiger_usersQuotes.id = vtiger_quotes.inventorymanager";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;
		Case "PurchaseOrder":
			//Query modified to sort by assigned to
			$query = "SELECT vtiger_crmentity.*,
			vtiger_purchaseorder.*,
			vtiger_pobillads.*,
			vtiger_poshipads.*,
			vtiger_vendor.vendorname,
			vtiger_currency_info.currency_name
			FROM vtiger_purchaseorder
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_purchaseorder.purchaseorderid
			LEFT OUTER JOIN vtiger_vendor
				ON vtiger_purchaseorder.vendorid = vtiger_vendor.vendorid
			LEFT JOIN vtiger_contactdetails
				ON vtiger_purchaseorder.contactid = vtiger_contactdetails.contactid
			INNER JOIN vtiger_pobillads
				ON vtiger_purchaseorder.purchaseorderid = vtiger_pobillads.pobilladdressid
			INNER JOIN vtiger_poshipads
				ON vtiger_purchaseorder.purchaseorderid = vtiger_poshipads.poshipaddressid
			LEFT JOIN vtiger_purchaseordercf
				ON vtiger_purchaseordercf.purchaseorderid = vtiger_purchaseorder.purchaseorderid
			LEFT JOIN vtiger_currency_info
				ON vtiger_purchaseorder.currency_id = vtiger_currency_info.id
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;
		Case "SalesOrder":
			//Query modified to sort by assigned to
			$query = "SELECT vtiger_crmentity.*,
			vtiger_salesorder.*,
			vtiger_sobillads.*,
			vtiger_soshipads.*,
			vtiger_quotes.subject AS quotename,
			vtiger_account.accountname,
			vtiger_currency_info.currency_name
			FROM vtiger_salesorder
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_salesorder.salesorderid
			INNER JOIN vtiger_sobillads
				ON vtiger_salesorder.salesorderid = vtiger_sobillads.sobilladdressid
			INNER JOIN vtiger_soshipads
				ON vtiger_salesorder.salesorderid = vtiger_soshipads.soshipaddressid
			LEFT JOIN vtiger_salesordercf
				ON vtiger_salesordercf.salesorderid = vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_currency_info
				ON vtiger_salesorder.currency_id = vtiger_currency_info.id
			LEFT OUTER JOIN vtiger_quotes
				ON vtiger_quotes.quoteid = vtiger_salesorder.quoteid
			LEFT OUTER JOIN vtiger_account
				ON vtiger_account.accountid = vtiger_salesorder.accountid
			LEFT JOIN vtiger_contactdetails
				ON vtiger_salesorder.contactid = vtiger_contactdetails.contactid
			LEFT JOIN vtiger_potential
				ON vtiger_potential.potentialid = vtiger_salesorder.potentialid
			LEFT JOIN vtiger_invoice_recurring_info
				ON vtiger_invoice_recurring_info.salesorderid = vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;
		Case "Invoice":
			//Query modified to sort by assigned to
			//query modified -Code contribute by Geoff(http://forums.vtiger.com/viewtopic.php?t=3376)
			$query = "SELECT vtiger_crmentity.*,
			vtiger_invoice.*,
			vtiger_invoicebillads.*,
			vtiger_invoiceshipads.*,
			vtiger_salesorder.subject AS salessubject,
			vtiger_account.accountname,
			vtiger_currency_info.currency_name
			FROM vtiger_invoice
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_invoice.invoiceid
			INNER JOIN vtiger_invoicebillads
				ON vtiger_invoice.invoiceid = vtiger_invoicebillads.invoicebilladdressid
			INNER JOIN vtiger_invoiceshipads
				ON vtiger_invoice.invoiceid = vtiger_invoiceshipads.invoiceshipaddressid
			LEFT JOIN vtiger_currency_info
				ON vtiger_invoice.currency_id = vtiger_currency_info.id
			LEFT OUTER JOIN vtiger_salesorder
				ON vtiger_salesorder.salesorderid = vtiger_invoice.salesorderid
			LEFT OUTER JOIN vtiger_account
			        ON vtiger_account.accountid = vtiger_invoice.accountid
			LEFT JOIN vtiger_contactdetails
				ON vtiger_contactdetails.contactid = vtiger_invoice.contactid
			INNER JOIN vtiger_invoicecf
				ON vtiger_invoice.invoiceid = vtiger_invoicecf.invoiceid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;
		Case "Campaigns":
			//Query modified to sort by assigned to
			//query modified -Code contribute by Geoff(http://forums.vtiger.com/viewtopic.php?t=3376)
			$query = "SELECT vtiger_crmentity.*,
			vtiger_campaign.*
			FROM vtiger_campaign
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
			INNER JOIN vtiger_campaignscf
			        ON vtiger_campaign.campaignid = vtiger_campaignscf.campaignid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_products
				ON vtiger_products.productid = vtiger_campaign.product_id";
			$query .= getNonAdminAccessControlQuery($module, $current_user);
			$query .= "WHERE vtiger_crmentity.deleted = 0 " . $where;
			break;
		Case "Users":
			$query = "SELECT id,user_name,first_name,last_name,email1,phone_mobile,phone_work,is_admin,status,email2,
					vtiger_user2role.roleid as roleid,vtiger_role.depth as depth
				 	FROM vtiger_users
				 	INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
				 	INNER JOIN vtiger_role ON vtiger_user2role.roleid = vtiger_role.roleid
					WHERE deleted=0 " . $where;
			break;
		default:
			// vtlib customization: Include the module file
			$focus = CRMEntity::getInstance($module);
			$query = $focus->getListQuery($module, $where);
		// END
	}

	if ($module != 'Users') {
		$query = listQueryNonAdminChange($query, $module);
	}
	$log->debug("Exiting getListQuery method ...");
	return $query;
}

/* * Function returns the list of records which an user is entiled to view
 * Param $module - module name
 * Returns a database query - type string
 */

function getReadEntityIds($module) {
	global $log;
	$log->debug("Entering getReadEntityIds(" . $module . ") method ...");
	global $current_user;
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	require('user_privileges/sharing_privileges_' . $current_user->id . '.php');
	$tab_id = getTabid($module);

	if ($module == "Leads") {
		$query = "SELECT vtiger_crmentity.crmid
			FROM vtiger_leaddetails
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
			LEFT JOIN vtiger_groups
                 ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= getNonAdminAccessControlQuery($module, $current_user);
		$query .= "WHERE vtiger_crmentity.deleted = 0
			AND vtiger_leaddetails.converted = 0 ";
	} elseif ($module == "Accounts") {
		//Query modified to sort by assigned to
		$query = "SELECT vtiger_crmentity.crmid
			FROM vtiger_account
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_account.accountid
			LEFT JOIN vtiger_groups
                ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= getNonAdminAccessControlQuery($module, $current_user);
		$query .= "WHERE vtiger_crmentity.deleted = 0 ";
	} elseif ($module == "Potentials") {
		//Query modified to sort by assigned to
		$query = "SELECT vtiger_crmentity.crmid
			FROM vtiger_potential
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_potential.potentialid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= getNonAdminAccessControlQuery($module, $current_user);
		$query .= "WHERE vtiger_crmentity.deleted = 0 ";
	} elseif ($module == "Contacts") {
		//Query modified to sort by assigned to

		$query = "SELECT vtiger_crmentity.crmid
			FROM vtiger_contactdetails
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= getNonAdminAccessControlQuery($module, $current_user);
		$query .= "WHERE vtiger_crmentity.deleted = 0 ";
	} elseif ($module == "Products") {
		$query = "SELECT DISTINCT vtiger_crmentity.crmid
			FROM vtiger_products
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_products.productid
			LEFT JOIN vtiger_seproductsrel
				ON vtiger_seproductsrel.productid = vtiger_products.productid
			WHERE vtiger_crmentity.deleted = 0
			AND (vtiger_seproductsrel.crmid IS NULL
				OR vtiger_seproductsrel.crmid IN (" . getReadEntityIds('Leads') . ")
				OR vtiger_seproductsrel.crmid IN (" . getReadEntityIds('Accounts') . ")
				OR vtiger_seproductsrel.crmid IN (" . getReadEntityIds('Potentials') . ")
				OR vtiger_seproductsrel.crmid IN (" . getReadEntityIds('Contacts') . ")) ";
	} elseif ($module == "PurchaseOrder") {
		//Query modified to sort by assigned to
		$query = "SELECT vtiger_crmentity.crmid
			FROM vtiger_purchaseorder
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_purchaseorder.purchaseorderid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= getNonAdminAccessControlQuery($module, $current_user);
		$query .= "WHERE vtiger_crmentity.deleted = 0 ";
	} elseif ($module == "SalesOrder") {
		//Query modified to sort by assigned to
		$query = "SELECT vtiger_crmentity.crmid
			FROM vtiger_salesorder
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_groups
                ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= getNonAdminAccessControlQuery($module, $current_user);
		$query .= "WHERE vtiger_crmentity.deleted = 0 ";
	} elseif ($module == "Invoice") {
		$query = "SELECT vtiger_crmentity.crmid
			FROM vtiger_invoice
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_invoice.invoiceid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= getNonAdminAccessControlQuery($module, $current_user);
		$query .= "WHERE vtiger_crmentity.deleted = 0 ";
	} elseif ($module == "Quotes") {
		$query = "SELECT vtiger_crmentity.crmid
		        FROM vtiger_quotes
			INNER JOIN vtiger_crmentity
			        ON vtiger_crmentity.crmid = vtiger_quotes.quoteid
			LEFT JOIN vtiger_groups
			        ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= getNonAdminAccessControlQuery($module, $current_user);
		$query .= "WHERE vtiger_crmentity.deleted = 0 ";
	} elseif ($module == "HelpDesk") {
		$query = "SELECT vtiger_crmentity.crmid
			FROM vtiger_troubletickets
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= getNonAdminAccessControlQuery($module, $current_user);
		$query .= "WHERE vtiger_crmentity.deleted = 0 ";
	}

	$log->debug("Exiting getReadEntityIds method ...");
	return $query;
}

/** Function to get alphabetical search links
 * Param $module - module name
 * Param $action - action
 * Param $fieldname - vtiger_field name
 * Param $query - query
 * Param $type - search type
 * Param $popuptype - popup type
 * Param $recordid - record id
 * Param $return_module - return module
 * Param $append_url - url string to be appended
 * Param $viewid - custom view id
 * Param $groupid - group id
 * Returns an string value
 */
function AlphabeticalSearch($module, $action, $fieldname, $query, $type, $popuptype = '', $recordid = '', $return_module = '', $append_url = '', $viewid = '', $groupid = '') {
	global $log;
	$log->debug("Entering AlphabeticalSearch(" . $module . "," . $action . "," . $fieldname . "," . $query . "," . $type . "," . $popuptype . "," . $recordid . "," . $return_module . "," . $append_url . "," . $viewid . "," . $groupid . ") method ...");
	if ($type == 'advanced')
		$flag = '&advanced=true';

	if ($popuptype != '')
		$popuptypevalue = "&popuptype=" . $popuptype;

	if ($recordid != '')
		$returnvalue = '&recordid=' . $recordid;
	if ($return_module != '')
		$returnvalue .= '&return_module=' . $return_module;

	// vtlib Customization : For uitype 10 popup during paging
	if ($_REQUEST['form'] == 'vtlibPopupView') {
		$returnvalue .= '&form=vtlibPopupView&forfield=' . vtlib_purify($_REQUEST['forfield']) . '&srcmodule=' . vtlib_purify($_REQUEST['srcmodule']) . '&forrecord=' . vtlib_purify($_REQUEST['forrecord']);
	}
	// END

	for ($var = 'A', $i = 1; $i <= 26; $i++, $var++)
	// Mike Crowe Mod --------------------------------------------------------added groupid to url
		$list .= '<td class="searchAlph" id="alpha_' . $i . '" align="center" onClick=\'alphabetic("' . $module . '","gname=' . $groupid . '&query=' . $query . '&search_field=' . $fieldname . '&searchtype=BasicSearch&operator=s&type=alpbt&search_text=' . $var . $flag . $popuptypevalue . $returnvalue . $append_url . '","alpha_' . $i . '")\'>' . $var . '</td>';

	$log->debug("Exiting AlphabeticalSearch method ...");
	return $list;
}

/* * Function to get parent name for a given parent id
 * Param $module - module name
 * Param $list_result- result set
 * Param $rset - result set index
 * Returns an string value
 */

function getRelatedToEntity($module, $list_result, $rset) {
	global $log;
	$log->debug("Entering getRelatedToEntity(" . $module . "," . $list_result . "," . $rset . ") method ...");

	global $adb;
	$seid = $adb->query_result($list_result, $rset, "relatedto");
	$action = "DetailView";

	if (isset($seid) && $seid != '') {
		$parent_module = $parent_module = getSalesEntityType($seid);
		if ($parent_module == 'Accounts') {
			$numrows = $adb->num_rows($evt_result);

			$parent_module = $adb->query_result($evt_result, 0, 'setype');
			$parent_id = $adb->query_result($evt_result, 0, 'crmid');

			if ($numrows > 1) {
				$parent_module = 'Multiple';
				$parent_name = $app_strings['LBL_MULTIPLE'];
			}
			//Raju -- Ends
			$parent_query = "SELECT accountname FROM vtiger_account WHERE accountid=?";
			$parent_result = $adb->pquery($parent_query, array($seid));
			$parent_name = $adb->query_result($parent_result, 0, "accountname");
		}
		if ($parent_module == 'Leads') {
			$parent_query = "SELECT firstname,lastname FROM vtiger_leaddetails WHERE leadid=?";
			$parent_result = $adb->pquery($parent_query, array($seid));
			$parent_name = getFullNameFromQResult($parent_result, 0, "Leads");
		}
		if ($parent_module == 'Potentials') {
			$parent_query = "SELECT potentialname FROM vtiger_potential WHERE potentialid=?";
			$parent_result = $adb->pquery($parent_query, array($seid));
			$parent_name = $adb->query_result($parent_result, 0, "potentialname");
		}
		if ($parent_module == 'Products') {
			$parent_query = "SELECT productname FROM vtiger_products WHERE productid=?";
			$parent_result = $adb->pquery($parent_query, array($seid));
			$parent_name = $adb->query_result($parent_result, 0, "productname");
		}
		if ($parent_module == 'PurchaseOrder') {
			$parent_query = "SELECT subject FROM vtiger_purchaseorder WHERE purchaseorderid=?";
			$parent_result = $adb->pquery($parent_query, array($seid));
			$parent_name = $adb->query_result($parent_result, 0, "subject");
		}
		if ($parent_module == 'SalesOrder') {
			$parent_query = "SELECT subject FROM vtiger_salesorder WHERE salesorderid=?";
			$parent_result = $adb->pquery($parent_query, array($seid));
			$parent_name = $adb->query_result($parent_result, 0, "subject");
		}
		if ($parent_module == 'Invoice') {
			$parent_query = "SELECT subject FROM vtiger_invoice WHERE invoiceid=?";
			$parent_result = $adb->pquery($parent_query, array($seid));
			$parent_name = $adb->query_result($parent_result, 0, "subject");
		}

		$parent_value = "<a href='index.php?module=" . $parent_module . "&action=" . $action . "&record=" . $seid . "'>" . $parent_name . "</a>";
	} else {
		$parent_value = '';
	}
	$log->debug("Exiting getRelatedToEntity method ...");
	return $parent_value;
}

/* * Function to get parent name for a given parent id
 * Param $module - module name
 * Param $list_result- result set
 * Param $rset - result set index
 * Returns an string value
 */

//used in home page listTop vtiger_files
function getRelatedTo($module, $list_result, $rset) {
	global $adb, $log, $app_strings;
	$tabname = getParentTab();
	if ($module == "Documents") {
		$notesid = $adb->query_result($list_result, $rset, "notesid");
		$action = "DetailView";
		$evt_query = "SELECT vtiger_senotesrel.crmid, vtiger_crmentity.setype
					FROM vtiger_senotesrel
					INNER JOIN vtiger_crmentity
					ON  vtiger_senotesrel.crmid = vtiger_crmentity.crmid
				WHERE vtiger_senotesrel.notesid = ?";
		$params = array($notesid);
	} else if ($module == "Products") {
		$productid = $adb->query_result($list_result, $rset, "productid");
		$action = "DetailView";
		$evt_query = "SELECT vtiger_seproductsrel.crmid, vtiger_crmentity.setype
					FROM vtiger_seproductsrel
					INNER JOIN vtiger_crmentity
					ON vtiger_seproductsrel.crmid = vtiger_crmentity.crmid
					WHERE vtiger_seproductsrel.productid =?";
		$params = array($productid);
	} else {
		$activity_id = $adb->query_result($list_result, $rset, "activityid");
		$action = "DetailView";
		$evt_query = "SELECT vtiger_seactivityrel.crmid, vtiger_crmentity.setype
			FROM vtiger_seactivityrel
			INNER JOIN vtiger_crmentity
				ON  vtiger_seactivityrel.crmid = vtiger_crmentity.crmid
			WHERE vtiger_seactivityrel.activityid=?";
		$params = array($activity_id);

		if ($module == 'HelpDesk') {
			$activity_id = $adb->query_result($list_result, $rset, "parent_id");
			if ($activity_id != '') {
				$evt_query = "SELECT crmid, setype FROM vtiger_crmentity WHERE crmid=?";
				$params = array($activity_id);
			}
		}
	}
	//added by raju to change the related to in emails inot multiple if email is for more than one contact
	$evt_result = $adb->pquery($evt_query, $params);
	$numrows = $adb->num_rows($evt_result);

	$parent_module = $adb->query_result($evt_result, 0, 'setype');
	$parent_id = $adb->query_result($evt_result, 0, 'crmid');



	if ($numrows > 1) {
		$parent_module = 'Multiple';
		$parent_name = $app_strings['LBL_MULTIPLE'];
	}
	//Raju -- Ends
	if ($module == 'HelpDesk' && ($parent_module == 'Accounts' || $parent_module == 'Contacts')) {
		global $theme;
		$module_icon = '<img src="themes/images/' . $parent_module . '.gif" alt="' . $app_strings[$parent_module] . '" title="' . $app_strings[$parent_module] . '" border=0 align=center> ';
	}

	$action = "DetailView";
	if ($parent_module == 'Accounts') {
		$parent_query = "SELECT accountname FROM vtiger_account WHERE accountid=?";
		$parent_result = $adb->pquery($parent_query, array($parent_id));
		$parent_name = textlength_check($adb->query_result($parent_result, 0, "accountname"));
	}
	if ($parent_module == 'Leads') {
		$parent_query = "SELECT firstname,lastname FROM vtiger_leaddetails WHERE leadid=?";
		$parent_result = $adb->pquery($parent_query, array($parent_id));
		$parent_name = getFullNameFromQResult($parent_result, 0, "Leads");
	}
	if ($parent_module == 'Potentials') {
		$parent_query = "SELECT potentialname FROM vtiger_potential WHERE potentialid=?";
		$parent_result = $adb->pquery($parent_query, array($parent_id));
		$parent_name = textlength_check($adb->query_result($parent_result, 0, "potentialname"));
	}
	if ($parent_module == 'Products') {
		$parent_query = "SELECT productname FROM vtiger_products WHERE productid=?";
		$parent_result = $adb->pquery($parent_query, array($parent_id));
		$parent_name = $adb->query_result($parent_result, 0, "productname");
	}
	if ($parent_module == 'Quotes') {
		$parent_query = "SELECT subject FROM vtiger_quotes WHERE quoteid=?";
		$parent_result = $adb->pquery($parent_query, array($parent_id));
		$parent_name = $adb->query_result($parent_result, 0, "subject");
	}
	if ($parent_module == 'PurchaseOrder') {
		$parent_query = "SELECT subject FROM vtiger_purchaseorder WHERE purchaseorderid=?";
		$parent_result = $adb->pquery($parent_query, array($parent_id));
		$parent_name = $adb->query_result($parent_result, 0, "subject");
	}
	if ($parent_module == 'Invoice') {
		$parent_query = "SELECT subject FROM vtiger_invoice WHERE invoiceid=?";
		$parent_result = $adb->pquery($parent_query, array($parent_id));
		$parent_name = $adb->query_result($parent_result, 0, "subject");
	}
	if ($parent_module == 'SalesOrder') {
		$parent_query = "SELECT subject FROM vtiger_salesorder WHERE salesorderid=?";
		$parent_result = $adb->pquery($parent_query, array($parent_id));
		$parent_name = $adb->query_result($parent_result, 0, "subject");
	}
	if ($parent_module == 'Contacts' && ($module == 'Emails' || $module == 'HelpDesk')) {
		$parent_query = "SELECT firstname,lastname FROM vtiger_contactdetails WHERE contactid=?";
		$parent_result = $adb->pquery($parent_query, array($parent_id));
		$parent_name = getFullNameFromQResult($parent_result, 0, "Contacts");
	}
	if ($parent_module == 'Vendors' && ($module == 'Emails')) {
		$parent_query = "SELECT vendorname FROM vtiger_vendor WHERE vendorid=?";
		$parent_result = $adb->pquery($parent_query, array($parent_id));
		$parent_name = $adb->query_result($parent_result, 0, "vendorname");
	}
	if ($parent_module == 'HelpDesk') {
		$parent_query = "SELECT title FROM vtiger_troubletickets WHERE ticketid=?";
		$parent_result = $adb->pquery($parent_query, array($parent_id));
		$parent_name = $adb->query_result($parent_result, 0, "title");
		//if(strlen($parent_name) > 25)
		//{
		$parent_name = textlength_check($parent_name);
		//}
	}
	if ($parent_module == 'Campaigns') {
		$parent_query = "SELECT campaignname FROM vtiger_campaign WHERE campaignid=?";
		$parent_result = $adb->pquery($parent_query, array($parent_id));
		$parent_name = $adb->query_result($parent_result, 0, "campaignname");
		//if(strlen($parent_name) > 25)
		//{
		$parent_name = textlength_check($parent_name);
		//}
	}

	//added by rdhital for better emails - Raju
	if ($parent_module == 'Multiple') {
		$parent_value = $parent_name;
	} else {
		$parent_value = $module_icon . "<a href='index.php?module=" . $parent_module . "&action=" . $action . "&record=" . $parent_id . "&parenttab=" . $tabname . "'>" . textlength_check($parent_name) . "</a>";
	}
	//code added by raju ends
	$log->debug("Exiting getRelatedTo method ...");
	return $parent_value;
}

/* * Function to get the table headers for a listview
 * Param $navigation_arrray - navigation values in array
 * Param $url_qry - url string
 * Param $module - module name
 * Param $action- action file name
 * Param $viewid - view id
 * Returns an string value
 */

function getTableHeaderNavigation($navigation_array, $url_qry, $module = '', $action_val = 'index', $viewid = '') {
	global $log, $app_strings;
	$log->debug("Entering getTableHeaderNavigation(" . $navigation_array . "," . $url_qry . "," . $module . "," . $action_val . "," . $viewid . ") method ...");
	global $theme, $current_user;
	$theme_path = "themes/" . $theme . "/";
	$image_path = $theme_path . "images/";
	if ($module == 'Documents') {
		$output = '<td class="mailSubHeader" width="100%" align="center">';
	} else {
		$output = '<td align="right" style="padding: 5px;">';
	}
	$tabname = getParentTab();

	$url_string = '';

	// vtlib Customization : For uitype 10 popup during paging
	if ($_REQUEST['form'] == 'vtlibPopupView') {
		$url_string .= '&form=vtlibPopupView&forfield=' . vtlib_purify($_REQUEST['forfield']) . '&srcmodule=' . vtlib_purify($_REQUEST['srcmodule']) . '&forrecord=' . vtlib_purify($_REQUEST['forrecord']);
	}
	// END

	if ($module == 'Calendar' && $action_val == 'index') {
		if ($_REQUEST['view'] == '') {
			if ($current_user->activity_view == "This Year") {
				$mysel = 'year';
			} else if ($current_user->activity_view == "This Month") {
				$mysel = 'month';
			} else if ($current_user->activity_view == "This Week") {
				$mysel = 'week';
			} else {
				$mysel = 'day';
			}
		}
		$data_value = date('Y-m-d H:i:s');
		preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $data_value, $value);
		$date_data = Array(
			'day' => $value[3],
			'month' => $value[2],
			'year' => $value[1],
			'hour' => $value[4],
			'min' => $value[5],
		);
		$tab_type = ($_REQUEST['subtab'] == '') ? 'event' : vtlib_purify($_REQUEST['subtab']);
		$url_string .= isset($_REQUEST['view']) ? "&view=" . vtlib_purify($_REQUEST['view']) : "&view=" . $mysel;
		$url_string .= isset($_REQUEST['subtab']) ? "&subtab=" . vtlib_purify($_REQUEST['subtab']) : '';
		$url_string .= isset($_REQUEST['viewOption']) ? "&viewOption=" . vtlib_purify($_REQUEST['viewOption']) : '&viewOption=listview';
		$url_string .= isset($_REQUEST['day']) ? "&day=" . vtlib_purify($_REQUEST['day']) : '&day=' . $date_data['day'];
		$url_string .= isset($_REQUEST['week']) ? "&week=" . vtlib_purify($_REQUEST['week']) : '';
		$url_string .= isset($_REQUEST['month']) ? "&month=" . vtlib_purify($_REQUEST['month']) : '&month=' . $date_data['month'];
		$url_string .= isset($_REQUEST['year']) ? "&year=" . vtlib_purify($_REQUEST['year']) : "&year=" . $date_data['year'];
		$url_string .= isset($_REQUEST['n_type']) ? "&n_type=" . vtlib_purify($_REQUEST['n_type']) : '';
		$url_string .= isset($_REQUEST['search_option']) ? "&search_option=" . vtlib_purify($_REQUEST['search_option']) : '';
	}
	if ($module == 'Calendar' && $action_val != 'index') //added for the All link from the homepage -- ticket 5211
		$url_string .= isset($_REQUEST['from_homepage']) ? "&from_homepage=" . vtlib_purify($_REQUEST['from_homepage']) : '';

	if (($navigation_array['prev']) != 0) {
		if ($module == 'Calendar' && $action_val == 'index') {
			//$output .= '<a href="index.php?module=Calendar&action=index&start=1'.$url_string.'" alt="'.$app_strings['LBL_FIRST'].'" title="'.$app_strings['LBL_FIRST'].'"><img src="themes/images/start.gif" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="cal_navigation(\'' . $tab_type . '\',\'' . $url_string . '\',\'&start=1\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			//$output .= '<a href="index.php?module=Calendar&action=index&start='.$navigation_array['prev'].$url_string.'" alt="'.$app_strings['LNK_LIST_PREVIOUS'].'"title="'.$app_strings['LNK_LIST_PREVIOUS'].'"><img src="themes/images/previous.gif" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="cal_navigation(\'' . $tab_type . '\',\'' . $url_string . '\',\'&start=' . $navigation_array['prev'] . '\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} else if ($action_val == "FindDuplicate") {
			$output .= '<a href="javascript:;" onClick="getDuplicateListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=1' . $url_string . '\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getDuplicateListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['prev'] . $url_string . '\');" alt="' . $app_strings['LNK_LIST_PREVIOUS'] . '"title="' . $app_strings['LNK_LIST_PREVIOUS'] . '"><img src="' . vtiger_imageurl('previous.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} elseif ($action_val == 'UnifiedSearch') {
			$output .= '<a href="javascript:;" onClick="getUnifiedSearchEntries_js(\'' . $search_tag . '\',\'' . $module . '\',\'parenttab=' . $tabname . '&start=1' . $url_string . '\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getUnifiedSearchEntries_js(\'' . $search_tag . '\',\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['prev'] . $url_string . '\');" alt="' . $app_strings['LNK_LIST_PREVIOUS'] . '"title="' . $app_strings['LNK_LIST_PREVIOUS'] . '"><img src="' . vtiger_imageurl('previous.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} elseif ($module == 'Documents') {
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=1' . $url_string . '\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['prev'] . $url_string . '&folderid=' . $action_val . '\');" alt="' . $app_strings['LNK_LIST_PREVIOUS'] . '"title="' . $app_strings['LNK_LIST_PREVIOUS'] . '"><img src="' . vtiger_imageurl('previous.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} else {
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=1' . $url_string . '\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['prev'] . $url_string . '\');" alt="' . $app_strings['LNK_LIST_PREVIOUS'] . '"title="' . $app_strings['LNK_LIST_PREVIOUS'] . '"><img src="' . vtiger_imageurl('previous.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		}
	} else {
		$output .= '<img src="' . vtiger_imageurl('start_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
		$output .= '<img src="' . vtiger_imageurl('previous_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
	}

	if ($module == 'Calendar' && $action_val == 'index') {
		$jsNavigate = "cal_navigation('$tab_type','$url_string','&start='+this.value);";
	} else if ($action_val == "FindDuplicate") {
		$jsNavigate = "getDuplicateListViewEntries_js('$module','parenttab=$tabname&start='+this.value+'$url_string');";
	} elseif ($action_val == 'UnifiedSearch') {
		$jsNavigate = "getUnifiedSearchEntries_js('$module','parenttab=$tabname&start='+this.value+'$url_string');";
	} elseif ($module == 'Documents') {
		$jsNavigate = "getListViewEntries_js('$module','parenttab=$tabname&start='+this.value+'$url_string&folderid=$action_val');";
	} else {
		$jsNavigate = "getListViewEntries_js('$module','parenttab=$tabname&start='+this.value+'$url_string');";
	}
	if ($module == 'Documents') {
		$url = '&folderid=' . $action_val;
	} else {
		$url = '';
	}
	$jsHandler = "return VT_disableFormSubmit(event);";
	$output .= "<input class='small' name='pagenum' type='text' value='{$navigation_array['current']}'
		style='width: 3em;margin-right: 0.7em;' onchange=\"$jsNavigate\"
		onkeypress=\"$jsHandler\">";
	$output .= "<span name='" . $module . "_listViewCountContainerName' class='small' style='white-space: nowrap;'>";
	$output .= $app_strings['LBL_LIST_OF'] . ' ' . $navigation_array['verylast'] . '</span>';

	if (($navigation_array['next']) != 0) {
		if ($module == 'Calendar' && $action_val == 'index') {
			//$output .= '<a href="index.php?module=Calendar&action=index&start='.$navigation_array['next'].$url_string.'" alt="'.$app_strings['LNK_LIST_NEXT'].'" title="'.$app_strings['LNK_LIST_NEXT'].'"><img src="themes/images/next.gif" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="cal_navigation(\'' . $tab_type . '\',\'' . $url_string . '\',\'&start=' . $navigation_array['next'] . '\');" alt="' . $app_strings['LNK_LIST_NEXT'] . '" title="' . $app_strings['LNK_LIST_NEXT'] . '"><img src="' . vtiger_imageurl('next.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			//$output .= '<a href="index.php?module=Calendar&action=index&start='.$navigation_array['verylast'].$url_string.'" alt="'.$app_strings['LBL_LAST'].'" title="'.$app_strings['LBL_LAST'].'"><img src="themes/images/end.gif" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="cal_navigation(\'' . $tab_type . '\',\'' . $url_string . '\',\'&start=' . $navigation_array['verylast'] . '\');" alt="' . $app_strings['LBL_LAST'] . '" title="' . $app_strings['LBL_LAST'] . '"><img src="' . vtiger_imageurl('end.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} else if ($action_val == "FindDuplicate") {
			$output .= '<a href="javascript:;" onClick="getDuplicateListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['next'] . $url_string . '\');" alt="' . $app_strings['LNK_LIST_NEXT'] . '" title="' . $app_strings['LNK_LIST_NEXT'] . '"><img src="' . vtiger_imageurl('next.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getDuplicateListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['verylast'] . $url_string . '\');" alt="' . $app_strings['LBL_LAST'] . '" title="' . $app_strings['LBL_LAST'] . '"><img src="' . vtiger_imageurl('end.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} elseif ($action_val == 'UnifiedSearch') {
			$output .= '<a href="javascript:;" onClick="getUnifiedSearchEntries_js(\'' . $search_tag . '\',\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['next'] . $url_string . '\');" alt="' . $app_strings['LNK_LIST_NEXT'] . '" title="' . $app_strings['LNK_LIST_NEXT'] . '"><img src="' . vtiger_imageurl('next.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getUnifiedSearchEntries_js(\'' . $search_tag . '\',\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['verylast'] . $url_string . '\');" alt="' . $app_strings['LBL_LAST'] . '" title="' . $app_strings['LBL_LAST'] . '"><img src="themes/images/end.gif" border="0" align="absmiddle"></a>&nbsp;';
		} elseif ($module == 'Documents') {
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['next'] . $url_string . '&folderid=' . $action_val . '\');" alt="' . $app_strings['LNK_LIST_NEXT'] . '" title="' . $app_strings['LNK_LIST_NEXT'] . '"><img src="' . vtiger_imageurl('next.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['verylast'] . $url_string . '&folderid=' . $action_val . '\');" alt="' . $app_strings['LBL_LAST'] . '" title="' . $app_strings['LBL_LAST'] . '"><img src="' . vtiger_imageurl('end.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} else {
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['next'] . $url_string . '\');" alt="' . $app_strings['LNK_LIST_NEXT'] . '" title="' . $app_strings['LNK_LIST_NEXT'] . '"><img src="' . vtiger_imageurl('next.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['verylast'] . $url_string . '\');" alt="' . $app_strings['LBL_LAST'] . '" title="' . $app_strings['LBL_LAST'] . '"><img src="' . vtiger_imageurl('end.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		}
	} else {
		$output .= '<img src="' . vtiger_imageurl('next_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
		$output .= '<img src="' . vtiger_imageurl('end_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
	}
	$output .= '</td>';
	$log->debug("Exiting getTableHeaderNavigation method ...");
	if ($navigation_array['first'] == '')
		return;
	else
		return $output;
}

function getPopupCheckquery($current_module, $relmodule, $relmod_recordid) {
	global $log, $adb;
	$log->debug("Entering getPopupCheckquery(" . $current_module . "," . $relmodule . "," . $relmod_recordid . ") method ...");
	if ($current_module == "Contacts") {
		if ($relmodule == "Accounts" && $relmod_recordid != '')
			$condition = "and vtiger_account.accountid= " . $relmod_recordid;

		elseif ($relmodule == "Potentials") {
			$query = "select contactid from vtiger_contpotentialrel where potentialid=?";
			$result = $adb->pquery($query, array($relmod_recordid));
			$contact_id = $adb->query_result($result, 0, "contactid");
			if ($contact_id != '' && $contact_id != 0)
				$condition = "and vtiger_contactdetails.contactid= " . $contact_id;
			else {
				$query = "select related_to from vtiger_potential where potentialid=?";
				$result = $adb->pquery($query, array($relmod_recordid));
				$acc_id = $adb->query_result($result, 0, "related_to");
				if ($acc_id != '') {
					$condition = "and vtiger_contactdetails.accountid= " . $acc_id;
				}
			}
		} elseif ($relmodule == "Quotes") {

			$query = "select accountid,contactid from vtiger_quotes where quoteid=?";
			$result = $adb->pquery($query, array($relmod_recordid));
			$contactid = $adb->query_result($result, 0, "contactid");
			if ($contactid != '' && $contactid != 0)
				$condition = "and vtiger_contactdetails.contactid= " . $contactid;
			else {
				$account_id = $adb->query_result($result, 0, "accountid");
				if ($account_id != '')
					$condition = "and vtiger_contactdetails.accountid= " . $account_id;
			}
		}
		elseif ($relmodule == "PurchaseOrder") {
			$query = "select contactid from vtiger_purchaseorder where purchaseorderid=?";
			$result = $adb->pquery($query, array($relmod_recordid));
			$contact_id = $adb->query_result($result, 0, "contactid");
			if ($contact_id != '')
				$condition = "and vtiger_contactdetails.contactid= " . $contact_id;
			else
				$condition = "and vtiger_contactdetails.contactid= 0";
		}
		elseif ($relmodule == "SalesOrder") {
			$query = "select accountid,contactid from vtiger_salesorder where salesorderid=?";
			$result = $adb->pquery($query, array($relmod_recordid));
			$contact_id = $adb->query_result($result, 0, "contactid");
			if ($contact_id != 0 && $contact_id != '')
				$condition = "and vtiger_contactdetails.contactid=" . $contact_id;
			else {
				$account_id = $adb->query_result($result, 0, "accountid");
				if ($account_id != '')
					$condition = "and vtiger_contactdetails.accountid= " . $account_id;
			}
		}
		elseif ($relmodule == "Invoice") {
			$query = "select accountid,contactid from vtiger_invoice where invoiceid=?";
			$result = $adb->pquery($query, array($relmod_recordid));
			$contact_id = $adb->query_result($result, 0, "contactid");
			if ($contact_id != '' && $contact_id != 0)
				$condition = " and vtiger_contactdetails.contactid=" . $contact_id;
			else {
				$account_id = $adb->query_result($result, 0, "accountid");
				if ($account_id != '')
					$condition = " and vtiger_contactdetails.accountid=" . $account_id;
			}
		}
		elseif ($relmodule == "Campaigns") {
			$query = "select contactid from vtiger_campaigncontrel where campaignid =?";
			$result = $adb->pquery($query, array($relmod_recordid));
			$rows = $adb->num_rows($result);
			if ($rows != 0) {
				$j = 0;
				$contactid_comma = "(";
				for ($k = 0; $k < $rows; $k++) {
					$contactid = $adb->query_result($result, $k, 'contactid');
					$contactid_comma.=$contactid;
					if ($k < ($rows - 1))
						$contactid_comma.=', ';
				}
				$contactid_comma.= ")";
			}
			else
				$contactid_comma = "(0)";
			$condition = "and vtiger_contactdetails.contactid in " . $contactid_comma;
		}
		elseif ($relmodule == "Products") {
			$query = "select crmid from vtiger_seproductsrel where productid=? and setype=?";
			$result = $adb->pquery($query, array($relmod_recordid, "Contacts"));
			$rows = $adb->num_rows($result);
			if ($rows != 0) {
				$j = 0;
				$contactid_comma = "(";
				for ($k = 0; $k < $rows; $k++) {
					$contactid = $adb->query_result($result, $k, 'crmid');
					$contactid_comma.=$contactid;
					if ($k < ($rows - 1))
						$contactid_comma.=', ';
				}
				$contactid_comma.= ")";
			}
			else
				$contactid_comma = "(0)";
			$condition = "and vtiger_contactdetails.contactid in " . $contactid_comma;
		}
		elseif ($relmodule == "HelpDesk" || $relmodule == "Trouble Tickets") {
			$query = "select parent_id from vtiger_troubletickets where ticketid =?";
			$result = $adb->pquery($query, array($relmod_recordid));
			$parent_id = $adb->query_result($result, 0, "parent_id");
			if ($parent_id != "") {
				$crmquery = "select setype from vtiger_crmentity where crmid=?";
				$parentmodule_id = $adb->pquery($crmquery, array($parent_id));
				$parent_modname = $adb->query_result($parentmodule_id, 0, "setype");
				if ($parent_modname == "Accounts")
					$condition = "and vtiger_contactdetails.accountid= " . $parent_id;
				if ($parent_modname == "Contacts")
					$condition = "and vtiger_contactdetails.contactid= " . $parent_id;
			}
			else
				$condition = " and vtiger_contactdetails.contactid=0";
		}
	}
	elseif ($current_module == "Potentials") {
		if ($relmodule == 'Accounts' || $relmodule == 'Contacts') {
			if ($relmodule == 'Contacts') {
				$pot_query = "select vtiger_crmentity.crmid,vtiger_contactdetails.contactid,vtiger_potential.potentialid from vtiger_potential inner join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_potential.related_to inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid where vtiger_crmentity.deleted=0 and vtiger_potential.related_to=?";
			} else {
				$pot_query = "select vtiger_crmentity.crmid,vtiger_account.accountid,vtiger_potential.potentialid from vtiger_potential inner join vtiger_account on vtiger_account.accountid=vtiger_potential.related_to inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid where vtiger_crmentity.deleted=0 and vtiger_potential.related_to=?";
			}
			$pot_result = $result = $adb->pquery($pot_query, array($relmod_recordid));
			$rows = $adb->num_rows($pot_result);
			$potids_comma = "";
			if ($rows != 0) {
				$j = 0;
				$potids_comma .= "(";
				for ($k = 0; $k < $rows; $k++) {
					$potential_ids = $adb->query_result($pot_result, $k, 'potentialid');
					$potids_comma.=$potential_ids;
					if ($k < ($rows - 1))
						$potids_comma.=',';
				}
				$potids_comma.= ")";
			}
			else
				$potids_comma = "(0)";
			$condition = "and vtiger_potential.potentialid in " . $potids_comma;
		}
	}
	else if ($current_module == "Products") {
		if ($relmodule == 'Accounts') {
			$pro_query = "select productid from vtiger_seproductsrel where setype='Accounts' and crmid=?";
			$pro_result = $result = $adb->pquery($pro_query, array($relmod_recordid));
			$rows = $adb->num_rows($pro_result);
			if ($rows != 0) {
				$proids_comma = "(";
				for ($k = 0; $k < $rows; $k++) {
					$product_ids = $adb->query_result($pro_result, $k, 'productid');
					$proids_comma .= $product_ids;
					if ($k < ($rows - 1))
						$proids_comma.=',';
				}
				$proids_comma.= ")";
			}
			else
				$proids_comma = "(0)";
			$condition = "and vtiger_products.productid in " . $proids_comma;
		}
		if ($relmodule == 'Vendor' && $relmod_recordid != '')
			$condition = "and vtiger_vendor.vendorid= " . $relmod_recordid;
	}
	else if ($current_module == 'Quotes') {
		if ($relmodule == 'Accounts') {
			$quote_query = "select quoteid from vtiger_quotes where accountid=?";
			$quote_result = $result = $adb->pquery($quote_query, array($relmod_recordid));
			$rows = $adb->num_rows($quote_result);
			if ($rows != 0) {
				$j = 0;
				$qtids_comma = "(";
				for ($k = 0; $k < $rows; $k++) {
					$quote_ids = $adb->query_result($quote_result, $k, 'quoteid');
					$qtids_comma.=$quote_ids;
					if ($k < ($rows - 1))
						$qtids_comma.=',';
				}
				$qtids_comma.= ")";
			}
			else
				$qtids_comma = "(0)";
			$condition = "and vtiger_quotes.quoteid in " . $qtids_comma;
		}
	}
	else if ($current_module == 'SalesOrder') {
		if ($relmodule == 'Accounts') {
			$SO_query = "select salesorderid from vtiger_salesorder where accountid=?";
			$SO_result = $result = $adb->pquery($SO_query, array($relmod_recordid));
			$rows = $adb->num_rows($SO_result);
			if ($rows != 0) {
				$SOids_comma = "(";
				for ($k = 0; $k < $rows; $k++) {
					$SO_ids = $adb->query_result($SO_result, $k, 'salesorderid');
					$SOids_comma.=$SO_ids;
					if ($k < ($rows - 1))
						$SOids_comma.=',';
				}
				$SOids_comma.= ")";
			}
			else
				$SOids_comma = "(0)";
			$condition = "and vtiger_salesorder.salesorderid in " . $SOids_comma;
		}
	}
	else
		$condition = '';
	$where = $condition;
	$log->debug("Exiting getPopupCheckquery method ...");
	return $where;
}

/* * This function return the entity ids that need to be excluded in popup listview for a given record
  Param $currentmodule - modulename of the entity to be selected
  Param $returnmodule - modulename for which the entity is assingned
  Param $recordid - the record id for which the entity is assigned
  Return type string.
 */

function getRelCheckquery($currentmodule, $returnmodule, $recordid) {
	global $log, $adb;
	$log->debug("Entering getRelCheckquery(" . $currentmodule . "," . $returnmodule . "," . $recordid . ") method ...");
	$skip_id = Array();
	$where_relquery = "";
	$params = array();
	if ($currentmodule == "Contacts" && $returnmodule == "Potentials") {
		$reltable = 'vtiger_contpotentialrel';
		$condition = 'WHERE potentialid = ?';
		array_push($params, $recordid);
		$field = $selectfield = 'contactid';
		$table = 'vtiger_contactdetails';
	} elseif ($currentmodule == "Contacts" && $returnmodule == "Vendors") {
		$reltable = 'vtiger_vendorcontactrel';
		$condition = 'WHERE vendorid = ?';
		array_push($params, $recordid);
		$field = $selectfield = 'contactid';
		$table = 'vtiger_contactdetails';
	} elseif ($currentmodule == "Contacts" && $returnmodule == "Campaigns") {
		$reltable = 'vtiger_campaigncontrel';
		$condition = 'WHERE campaignid = ?';
		array_push($params, $recordid);
		$field = $selectfield = 'contactid';
		$table = 'vtiger_contactdetails';
	} elseif ($currentmodule == "Contacts" && $returnmodule == "Calendar") {
		$reltable = 'vtiger_cntactivityrel';
		$condition = 'WHERE activityid = ?';
		array_push($params, $recordid);
		$field = $selectfield = 'contactid';
		$table = 'vtiger_contactdetails';
	} elseif ($currentmodule == "Leads" && $returnmodule == "Campaigns") {
		$reltable = 'vtiger_campaignleadrel';
		$condition = 'WHERE campaignid = ?';
		array_push($params, $recordid);
		$field = $selectfield = 'leadid';
		$table = 'vtiger_leaddetails';
	} elseif ($currentmodule == "Users" && $returnmodule == "Calendar") {
		$reltable = 'vtiger_salesmanactivityrel';
		$condition = 'WHERE activityid = ?';
		array_push($params, $recordid);
		$selectfield = 'smid';
		$field = 'id';
		$table = 'vtiger_users';
	} elseif ($currentmodule == "Campaigns" && $returnmodule == "Leads") {
		$reltable = 'vtiger_campaignleadrel';
		$condition = 'WHERE leadid = ?';
		array_push($params, $recordid);
		$field = $selectfield = 'campaignid';
		$table = 'vtiger_campaign';
	} elseif ($currentmodule == "Campaigns" && $returnmodule == "Contacts") {
		$reltable = 'vtiger_campaigncontrel';
		$condition = 'WHERE contactid = ?';
		array_push($params, $recordid);
		$field = $selectfield = 'campaignid';
		$table = 'vtiger_campaign';
	} elseif ($currentmodule == "Products" && ($returnmodule == "Potentials" || $returnmodule == "Accounts" || $returnmodule == "Contacts" || $returnmodule == "Leads")) {
		$reltable = 'vtiger_seproductsrel';
		$condition = 'WHERE crmid = ? and setype = ?';
		array_push($params, $recordid, $returnmodule);
		$field = $selectfield = 'productid';
		$table = 'vtiger_products';
	} elseif (($currentmodule == "Leads" || $currentmodule == "Accounts" || $currentmodule == "Potentials" || $currentmodule == "Contacts") && $returnmodule == "Products") {//added to fix the issues(ticket 4001,4002 and 4003)
		$reltable = 'vtiger_seproductsrel';
		$condition = 'WHERE productid = ? and setype = ?';
		array_push($params, $recordid, $currentmodule);
		$selectfield = 'crmid';
		if ($currentmodule == "Leads") {
			$field = 'leadid';
			$table = 'vtiger_leaddetails';
		} elseif ($currentmodule == "Accounts") {
			$field = 'accountid';
			$table = 'vtiger_account';
		} elseif ($currentmodule == "Contacts") {
			$field = 'contactid';
			$table = 'vtiger_contactdetails';
		} elseif ($currentmodule == "Potentials") {
			$field = 'potentialid';
			$table = 'vtiger_potential';
		}
	} elseif ($currentmodule == "Products" && $returnmodule == "Vendors") {
		$reltable = 'vtiger_products';
		$condition = 'WHERE vendor_id = ?';
		array_push($params, $recordid);
		$field = $selectfield = 'productid';
		$table = 'vtiger_products';
	} elseif ($currentmodule == "Documents") {
		$reltable = "vtiger_senotesrel";
		$selectfield = "notesid";
		$condition = "where crmid = ?";
		array_push($params, $recordid);
		$table = "vtiger_notes";
		$field = "notesid";
	}
	//end
	if ($reltable != null) {
		$query = "SELECT " . $selectfield . " FROM " . $reltable . " " . $condition;
	} elseif ($currentmodule != $returnmodule && $returnmodule != "") { // If none of the above relation matches, then the relation is assumed to be stored in vtiger_crmentityrel
		$query = "SELECT relcrmid AS relatedid FROM vtiger_crmentityrel WHERE  crmid = ? and module = ? and relmodule = ?
					UNION SELECT crmid AS relatedid FROM vtiger_crmentityrel WHERE relcrmid = ? and relmodule = ? and module = ?";
		array_push($params, $recordid, $returnmodule, $currentmodule, $recordid, $returnmodule, $currentmodule);

		$focus_obj = CRMEntity::getInstance($currentmodule);
		$field = $focus_obj->table_index;
		$table = $focus_obj->table_name;
		$selectfield = 'relatedid';
	}

	if ($query != '') {
		$result = $adb->pquery($query, $params);
		if ($adb->num_rows($result) != 0) {
			for ($k = 0; $k < $adb->num_rows($result); $k++) {
				$skip_id[] = $adb->query_result($result, $k, $selectfield);
			}
			$skipids = implode(",", constructList($skip_id, 'INTEGER'));
			if (count($skipids) > 0) {
				$where_relquery = "and " . $table . "." . $field . " not in (" . $skipids . ")";
			}
		}
	}
	$log->debug("Exiting getRelCheckquery method ...");
	return $where_relquery;
}

/* * This function stores the variables in session sent in list view url string.
 * Param $lv_array - list view session array
 * Param $noofrows - no of rows
 * Param $max_ent - maximum entires
 * Param $module - module name
 * Param $related - related module
 * Return type void.
 */

function setSessionVar($lv_array, $noofrows, $max_ent, $module = '', $related = '') {
	$start = '';
	if ($noofrows >= 1) {
		$lv_array['start'] = 1;
		$start = 1;
	} elseif ($related != '' && $noofrows == 0) {
		$lv_array['start'] = 1;
		$start = 1;
	} else {
		$lv_array['start'] = 0;
		$start = 0;
	}

	if (isset($_REQUEST['start']) && $_REQUEST['start'] != '') {
		$lv_array['start'] = ListViewSession::getRequestStartPage();
		$start = ListViewSession::getRequestStartPage();
	} elseif ($_SESSION['rlvs'][$module][$related]['start'] != '') {

		if ($related != '') {
			$lv_array['start'] = $_SESSION['rlvs'][$module][$related]['start'];
			$start = $_SESSION['rlvs'][$module][$related]['start'];
		}
	}
	if (isset($_REQUEST['viewname']) && $_REQUEST['viewname'] != '')
		$lv_array['viewname'] = vtlib_purify($_REQUEST['viewname']);

	if ($related == '')
		$_SESSION['lvs'][$_REQUEST['module']] = $lv_array;
	else
		$_SESSION['rlvs'][$module][$related] = $lv_array;

	if ($start < ceil($noofrows / $max_ent) && $start != '') {
		$start = ceil($noofrows / $max_ent);
		if ($related == '')
			$_SESSION['lvs'][$currentModule]['start'] = $start;
	}
}

/* * Function to get the table headers for related listview
 * Param $navigation_arrray - navigation values in array
 * Param $url_qry - url string
 * Param $module - module name
 * Param $action- action file name
 * Param $viewid - view id
 * Returns an string value
 */

function getRelatedTableHeaderNavigation($navigation_array, $url_qry, $module, $related_module, $recordid) {
	global $log, $app_strings, $adb;
	$log->debug("Entering getTableHeaderNavigation(" . $navigation_array . "," . $url_qry . "," . $module . "," . $action_val . "," . $viewid . ") method ...");
	global $theme;
	$relatedTabId = getTabid($related_module);
	$tabid = getTabid($module);

	$relatedListResult = $adb->pquery('SELECT * FROM vtiger_relatedlists WHERE tabid=? AND
		related_tabid=?', array($tabid, $relatedTabId));
	if (empty($relatedListResult))
		return;
	$relatedListRow = $adb->fetch_row($relatedListResult);
	$header = $relatedListRow['label'];
	$actions = $relatedListRow['actions'];
	$functionName = $relatedListRow['name'];

	$urldata = "module=$module&action={$module}Ajax&file=DetailViewAjax&record={$recordid}&" .
			"ajxaction=LOADRELATEDLIST&header={$header}&relation_id={$relatedListRow['relation_id']}" .
			"&actions={$actions}&{$url_qry}";

	$formattedHeader = str_replace(' ', '', $header);
	$target = 'tbl_' . $module . '_' . $formattedHeader;
	$imagesuffix = $module . '_' . $formattedHeader;

	$output = '<td align="right" style="padding="5px;">';
	if (($navigation_array['prev']) != 0) {
		$output .= '<a href="javascript:;" onClick="loadRelatedListBlock(\'' . $urldata . '&start=1\',\'' . $target . '\',\'' . $imagesuffix . '\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		$output .= '<a href="javascript:;" onClick="loadRelatedListBlock(\'' . $urldata . '&start=' . $navigation_array['prev'] . '\',\'' . $target . '\',\'' . $imagesuffix . '\');" alt="' . $app_strings['LNK_LIST_PREVIOUS'] . '"title="' . $app_strings['LNK_LIST_PREVIOUS'] . '"><img src="' . vtiger_imageurl('previous.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
	} else {
		$output .= '<img src="' . vtiger_imageurl('start_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
		$output .= '<img src="' . vtiger_imageurl('previous_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
	}

	$jsHandler = "return VT_disableFormSubmit(event);";
	$output .= "<input class='small' name='pagenum' type='text' value='{$navigation_array['current']}'
		style='width: 3em;margin-right: 0.7em;' onchange=\"loadRelatedListBlock('{$urldata}&start='+this.value+'','{$target}','{$imagesuffix}');\"
		onkeypress=\"$jsHandler\">";
	$output .= "<span name='listViewCountContainerName' class='small' style='white-space: nowrap;'>";
	$computeCount = $_REQUEST['withCount'];
	if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true
			|| ((boolean) $computeCount) == true) {
		$output .= $app_strings['LBL_LIST_OF'] . ' ' . $navigation_array['verylast'];
	} else {
		$output .= "<img src='" . vtiger_imageurl('windowRefresh.gif', $theme) . "' alt='" . $app_strings['LBL_HOME_COUNT'] . "'
			onclick=\"loadRelatedListBlock('{$urldata}&withCount=true&start={$navigation_array['current']}','{$target}','{$imagesuffix}');\"
			align='absmiddle' name='" . $module . "_listViewCountRefreshIcon'/>
			<img name='" . $module . "_listViewCountContainerBusy' src='" . vtiger_imageurl('vtbusy.gif', $theme) . "' style='display: none;'
			align='absmiddle' alt='" . $app_strings['LBL_LOADING'] . "'>";
	}
	$output .= '</span>';

	if (($navigation_array['next']) != 0) {
		$output .= '<a href="javascript:;" onClick="loadRelatedListBlock(\'' . $urldata . '&start=' . $navigation_array['next'] . '\',\'' . $target . '\',\'' . $imagesuffix . '\');"><img src="' . vtiger_imageurl('next.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		$output .= '<a href="javascript:;" onClick="loadRelatedListBlock(\'' . $urldata . '&start=' . $navigation_array['verylast'] . '\',\'' . $target . '\',\'' . $imagesuffix . '\');"><img src="' . vtiger_imageurl('end.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
	} else {
		$output .= '<img src="' . vtiger_imageurl('next_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
		$output .= '<img src="' . vtiger_imageurl('end_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
	}
	$output .= '</td>';
	$log->debug("Exiting getTableHeaderNavigation method ...");
	if ($navigation_array['first'] == '')
		return;
	else
		return $output;
}

/** 	Function to get the Edit link details for ListView and RelatedListView
 * 	@param string 	$module 	- module name
 * 	@param int 	$entity_id 	- record id
 * 	@param string 	$relatedlist 	- string "relatedlist" or may be empty. if empty means ListView else relatedlist
 * 	@param string 	$returnset 	- may be empty in case of ListView. For relatedlists, return_module, return_action and return_id values will be passed like &return_module=Accounts&return_action=CallRelatedList&return_id=10
 * 	return string	$edit_link	- url string which cotains the editlink details (module, action, record, etc.,) like index.php?module=Accounts&action=EditView&record=10
 */
function getListViewEditLink($module, $entity_id, $relatedlist, $returnset, $result, $count) {
	global $adb;
	$return_action = "index";
	$edit_link = "index.php?module=$module&action=EditView&record=$entity_id";
	$tabname = getParentTab();
	//Added to fix 4600
	$url = getBasic_Advance_SearchURL();

	//This is relatedlist listview
	if ($relatedlist == 'relatedlist') {
		$edit_link .= $returnset;
	} else {
		if ($module == 'Calendar') {
			$return_action = "ListView";
			$actvity_type = $adb->query_result($result, $count, 'type');
			if ($actvity_type == 'Task')
				$edit_link .= '&activity_mode=Task';
			else
				$edit_link .= '&activity_mode=Events';
		}
		$edit_link .= "&return_module=$module&return_action=$return_action";
	}

	$edit_link .= "&parenttab=" . $tabname . $url;
	//Appending view name while editing from ListView
	$edit_link .= "&return_viewname=" . $_SESSION['lvs'][$module]["viewname"];
	if ($module == 'Emails')
		$edit_link = 'javascript:;" onclick="OpenCompose(\'' . $entity_id . '\',\'edit\');';
	return $edit_link;
}

/** 	Function to get the Del link details for ListView and RelatedListView
 * 	@param string 	$module 	- module name
 * 	@param int 	$entity_id 	- record id
 * 	@param string 	$relatedlist 	- string "relatedlist" or may be empty. if empty means ListView else relatedlist
 * 	@param string 	$returnset 	- may be empty in case of ListView. For relatedlists, return_module, return_action and return_id values will be passed like &return_module=Accounts&return_action=CallRelatedList&return_id=10
 * 	return string	$del_link	- url string which cotains the editlink details (module, action, record, etc.,) like index.php?module=Accounts&action=Delete&record=10
 */
function getListViewDeleteLink($module, $entity_id, $relatedlist, $returnset) {
	$tabname = getParentTab();
	$current_module = vtlib_purify($_REQUEST['module']);
	$viewname = $_SESSION['lvs'][$current_module]['viewname'];

	//Added to fix 4600
	$url = getBasic_Advance_SearchURL();

	if ($module == "Calendar")
		$return_action = "ListView";
	else
		$return_action = "index";

	//This is added to avoid the del link in Product related list for the following modules
	$avoid_del_links = Array("PurchaseOrder", "SalesOrder", "Quotes", "Invoice");

	if (($current_module == 'Products' || $current_module == 'Services') && in_array($module, $avoid_del_links)) {
		return '';
	}

	$del_link = "index.php?module=$module&action=Delete&record=$entity_id";

	//This is added for relatedlist listview
	if ($relatedlist == 'relatedlist') {
		$del_link .= $returnset;
	} else {
		$del_link .= "&return_module=$module&return_action=$return_action";
	}

	$del_link .= "&parenttab=" . $tabname . "&return_viewname=" . $viewname . $url;

	// vtlib customization: override default delete link for custom modules
	$requestModule = vtlib_purify($_REQUEST['module']);
	$requestRecord = vtlib_purify($_REQUEST['record']);
	$requestAction = vtlib_purify($_REQUEST['action']);
	$parenttab = vtlib_purify($_REQUEST['parenttab']);
	$isCustomModule = vtlib_isCustomModule($requestModule);
	if ($requestAction == $requestModule . "Ajax") {
		$requestAction = vtlib_purify($_REQUEST['file']);
	}
	if ($isCustomModule && !in_array($requestAction, Array('index', 'ListView'))) {
		$del_link = "index.php?module=$requestModule&action=updateRelations&parentid=$requestRecord";
		$del_link .= "&destination_module=$module&idlist=$entity_id&mode=delete&parenttab=$parenttab";
	}
	// END

	return $del_link;
}

/* Function to get the Entity Id of a given Entity Name */

function getEntityId($module, $entityName) {
	global $log, $adb;
	$log->info("in getEntityId " . $entityName);

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

	if ($entityName != '') {
		$sql = "select $entityidfield from $tablename INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $tablename.$entityidfield " .
				" WHERE vtiger_crmentity.deleted = 0 and $fieldsname=?";
		$result = $adb->pquery($sql, array($entityName));
		if ($adb->num_rows($result) > 0) {
			$entityId = $adb->query_result($result, 0, $entityidfield);
		}
	}
	if (!empty($entityId))
		return $entityId;
	else
		return 0;
}

/** 	function used to get the parent id for the given input parent name --Pavani * */
function getParentId($parent_name) {
	global $adb;
	if ($parent_name == '' || $parent_name == NULL)
		$parent_id = 0;
	//For now it have conditions only for accounts and contacts, if needed can add more
	$relatedTo = explode(':', $parent_name);
	$parent_module = $relatedTo[0];
	$parent_module = trim($parent_module, " ");
	$parent_name = $relatedTo[3];
	$parent_name = trim($parent_name, " ");
	$num_rows = 0;
	if ($parent_module == 'Contacts') {
		$query = "select crmid from vtiger_contactdetails, vtiger_crmentity WHERE concat(lastname,' ',firstname)=? and vtiger_crmentity.crmid =vtiger_contactdetails.contactid and vtiger_crmentity.deleted=0";
		$result = $adb->pquery($query, array($parent_name));
		$num_rows = $adb->num_rows($result);
	} else if ($parent_module == 'Accounts') {
		$query = "select crmid from vtiger_account, vtiger_crmentity WHERE accountname=? and vtiger_crmentity.crmid =vtiger_account.accountid and vtiger_crmentity.deleted=0";
		$result = $adb->pquery($query, array($parent_name));
		$num_rows = $adb->num_rows($result);
	}
	else
		$num_rows = 0;
	if ($num_rows == 0)
		$parent_id = 0;
	else
		$parent_id = $adb->query_result($result, 0, "crmid");
	return $parent_id;
}

function decode_html($str) {
	global $default_charset;
	// Direct Popup action or Ajax Popup action should be treated the same.
	if ($_REQUEST['action'] == 'Popup' || $_REQUEST['file'] == 'Popup')
		return html_entity_decode($str);
	else
		return html_entity_decode($str, ENT_QUOTES, $default_charset);
}

/**
 * Alternative decoding function which coverts irrespective of $_REQUEST values.
 * Useful incase of Popup (Listview etc...) where if decode_html will not work as expected
 */
function decode_html_force($str) {
	global $default_charset;
	return html_entity_decode($str, ENT_QUOTES, $default_charset);
}

function popup_decode_html($str) {
	global $default_charset;
	$slashes_str = popup_from_html($str);
	$slashes_str = htmlspecialchars($slashes_str, ENT_QUOTES, $default_charset);
	return decode_html(br2nl($slashes_str));
}

//function added to check the text length in the listview.
function textlength_check($field_val) {
	global $listview_max_textlength, $default_charset;
	if ($listview_max_textlength && $listview_max_textlength > 0) {
		$temp_val = preg_replace("/(<\/?)(\w+)([^>]*>)/i", "", $field_val);
		if (function_exists('mb_strlen')) {
			if (mb_strlen($temp_val) > $listview_max_textlength) {
				$temp_val = mb_substr(preg_replace("/(<\/?)(\w+)([^>]*>)/i", "", $field_val), 0, $listview_max_textlength, $default_charset) . '...';
			}
		} elseif (strlen($field_val) > $listview_max_textlength) {
			$temp_val = substr(preg_replace("/(<\/?)(\w+)([^>]*>)/i", "", $field_val), 0, $listview_max_textlength) . '...';
		}
	} else {
		$temp_val = $field_val;
	}
	return $temp_val;
}

/** Function to get permitted fields of current user of a particular module to find duplicate records --Pavani */
function getMergeFields($module, $str) {
	global $adb, $current_user;
	$tabid = getTabid($module);
	if ($str == "available_fields") {
		$result = getFieldsResultForMerge($tabid);
	} else { //if($str == fileds_to_merge)
		$sql = "select * from vtiger_user2mergefields where tabid=? and userid=? and visible=1";
		$result = $adb->pquery($sql, array($tabid, $current_user->id));
	}

	$num_rows = $adb->num_rows($result);

	$user_profileid = fetchUserProfileId($current_user->id);
	$permitted_list = getProfile2FieldPermissionList($module, $user_profileid);

	$sql_def_org = "select fieldid from vtiger_def_org_field where tabid=? and visible=0";
	$result_def_org = $adb->pquery($sql_def_org, array($tabid));
	$num_rows_org = $adb->num_rows($result_def_org);
	$permitted_org_list = Array();
	for ($i = 0; $i < $num_rows_org; $i++)
		$permitted_org_list[$i] = $adb->query_result($result_def_org, $i, "fieldid");

	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	for ($i = 0; $i < $num_rows; $i++) {
		$field_id = $adb->query_result($result, $i, "fieldid");
		foreach ($permitted_list as $field => $data)
			if ($data[4] == $field_id and $data[1] == 0) {
				if ($is_admin == 'true' || (in_array($field_id, $permitted_org_list))) {
					$field = "<option value=\"" . $field_id . "\">" . getTranslatedString($data[0], $module) . "</option>";
					$fields.=$field;
					break;
				}
			}
	}
	return $fields;
}

/**
 * this function accepts a modulename and a fieldname and returns the first related module for it
 * it expects the uitype of the field to be 10
 * @param string $module - the modulename
 * @param string $fieldname - the field name
 * @return string $data - the first related module
 */
function getFirstModule($module, $fieldname) {
	global $adb;
	$sql = "select fieldid, uitype from vtiger_field where tabid=? and fieldname=?";
	$result = $adb->pquery($sql, array(getTabid($module), $fieldname));

	if ($adb->num_rows($result) > 0) {
		$uitype = $adb->query_result($result, 0, "uitype");

		if ($uitype == 10) {
			$fieldid = $adb->query_result($result, 0, "fieldid");
			$sql = "select * from vtiger_fieldmodulerel where fieldid=?";
			$result = $adb->pquery($sql, array($fieldid));
			$count = $adb->num_rows($result);

			if ($count > 0) {
				$data = $adb->query_result($result, 0, "relmodule");
			}
		}
	}
	return $data;
}

function VT_getSimpleNavigationValues($start, $size, $total) {
	$prev = $start - 1;
	if ($prev < 0) {
		$prev = 0;
	}
	if ($total === null) {
		return array('start' => $start, 'first' => $start, 'current' => $start, 'end' => $start, 'end_val' => $size, 'allflag' => 'All',
			'prev' => $prev, 'next' => $start + 1, 'verylast' => 'last');
	}
	if (empty($total)) {
		$lastPage = 1;
	} else {
		$lastPage = ceil($total / $size);
	}

	$next = $start + 1;
	if ($next > $lastPage) {
		$next = 0;
	}
	return array('start' => $start, 'first' => $start, 'current' => $start, 'end' => $start, 'end_val' => $size, 'allflag' => 'All',
		'prev' => $prev, 'next' => $next, 'verylast' => $lastPage);
}

/* * Function to get the simplified table headers for a listview
 * Param $navigation_arrray - navigation values in array
 * Param $url_qry - url string
 * Param $module - module name
 * Param $action- action file name
 * Param $viewid - view id
 * Returns an string value
 */

function getTableHeaderSimpleNavigation($navigation_array, $url_qry, $module = '', $action_val = 'index', $viewid = '') {
	global $log, $app_strings;
	global $theme, $current_user;
	$theme_path = "themes/" . $theme . "/";
	$image_path = $theme_path . "images/";
	if ($module == 'Documents') {
		$output = '<td class="mailSubHeader" width="40%" align="right">';
	} else {
		$output = '<td align="right" style="padding: 5px;">';
	}
	$tabname = getParentTab();
	$search_tag = $_REQUEST['search_tag'];
	$url_string = '';

	// vtlib Customization : For uitype 10 popup during paging
	if ($_REQUEST['form'] == 'vtlibPopupView') {
		$url_string .= '&form=vtlibPopupView&forfield=' . vtlib_purify($_REQUEST['forfield']) . '&srcmodule=' . vtlib_purify($_REQUEST['srcmodule']) . '&forrecord=' . vtlib_purify($_REQUEST['forrecord']);
	}
	// END

	if ($module == 'Calendar' && $action_val == 'index') {
		if ($_REQUEST['view'] == '') {
			if ($current_user->activity_view == "This Year") {
				$mysel = 'year';
			} else if ($current_user->activity_view == "This Month") {
				$mysel = 'month';
			} else if ($current_user->activity_view == "This Week") {
				$mysel = 'week';
			} else {
				$mysel = 'day';
			}
		}
		$data_value = date('Y-m-d H:i:s');
		preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $data_value, $value);
		$date_data = Array(
			'day' => $value[3],
			'month' => $value[2],
			'year' => $value[1],
			'hour' => $value[4],
			'min' => $value[5],
		);
		$tab_type = ($_REQUEST['subtab'] == '') ? 'event' : vtlib_purify($_REQUEST['subtab']);
		$url_string .= isset($_REQUEST['view']) ? "&view=" . vtlib_purify($_REQUEST['view']) : "&view=" . $mysel;
		$url_string .= isset($_REQUEST['subtab']) ? "&subtab=" . vtlib_purify($_REQUEST['subtab']) : '';
		$url_string .= isset($_REQUEST['viewOption']) ? "&viewOption=" . vtlib_purify($_REQUEST['viewOption']) : '&viewOption=listview';
		$url_string .= isset($_REQUEST['day']) ? "&day=" . vtlib_purify($_REQUEST['day']) : '&day=' . $date_data['day'];
		$url_string .= isset($_REQUEST['week']) ? "&week=" . vtlib_purify($_REQUEST['week']) : '';
		$url_string .= isset($_REQUEST['month']) ? "&month=" . vtlib_purify($_REQUEST['month']) : '&month=' . $date_data['month'];
		$url_string .= isset($_REQUEST['year']) ? "&year=" . vtlib_purify($_REQUEST['year']) : "&year=" . $date_data['year'];
		$url_string .= isset($_REQUEST['n_type']) ? "&n_type=" . vtlib_purify($_REQUEST['n_type']) : '';
		$url_string .= isset($_REQUEST['search_option']) ? "&search_option=" . vtlib_purify($_REQUEST['search_option']) : '';
	}
	if ($module == 'Calendar' && $action_val != 'index') //added for the All link from the homepage -- ticket 5211
		$url_string .= isset($_REQUEST['from_homepage']) ? "&from_homepage=" . vtlib_purify($_REQUEST['from_homepage']) : '';

	if (($navigation_array['prev']) != 0) {
		if ($module == 'Calendar' && $action_val == 'index') {
			$output .= '<a href="javascript:;" onClick="cal_navigation(\'' . $tab_type . '\',\'' . $url_string . '\',\'&start=1\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="cal_navigation(\'' . $tab_type . '\',\'' . $url_string . '\',\'&start=' . $navigation_array['prev'] . '\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} else if ($action_val == "FindDuplicate") {
			$output .= '<a href="javascript:;" onClick="getDuplicateListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=1' . $url_string . '\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getDuplicateListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['prev'] . $url_string . '\');" alt="' . $app_strings['LNK_LIST_PREVIOUS'] . '"title="' . $app_strings['LNK_LIST_PREVIOUS'] . '"><img src="' . vtiger_imageurl('previous.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} elseif ($action_val == 'UnifiedSearch') {
			$output .= '<a href="javascript:;" onClick="getUnifiedSearchEntries_js(\'' . $search_tag . '\',\'' . $module . '\',\'parenttab=' . $tabname . '&start=1' . $url_string . '\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getUnifiedSearchEntries_js(\'' . $search_tag . '\',\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['prev'] . $url_string . '\');" alt="' . $app_strings['LNK_LIST_PREVIOUS'] . '"title="' . $app_strings['LNK_LIST_PREVIOUS'] . '"><img src="' . vtiger_imageurl('previous.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} elseif ($module == 'Documents') {
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=1' . $url_string . '\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['prev'] . $url_string . '&folderid=' . $action_val . '\');" alt="' . $app_strings['LNK_LIST_PREVIOUS'] . '"title="' . $app_strings['LNK_LIST_PREVIOUS'] . '"><img src="' . vtiger_imageurl('previous.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} else {
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=1' . $url_string . '\');" alt="' . $app_strings['LBL_FIRST'] . '" title="' . $app_strings['LBL_FIRST'] . '"><img src="' . vtiger_imageurl('start.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['prev'] . $url_string . '\');" alt="' . $app_strings['LNK_LIST_PREVIOUS'] . '"title="' . $app_strings['LNK_LIST_PREVIOUS'] . '"><img src="' . vtiger_imageurl('previous.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		}
	} else {
		$output .= '<img src="' . vtiger_imageurl('start_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
		$output .= '<img src="' . vtiger_imageurl('previous_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
	}
	if ($module == 'Calendar' && $action_val == 'index') {
		$jsNavigate = "cal_navigation('$tab_type','$url_string','&start='+this.value);";
	} else if ($action_val == "FindDuplicate") {
		$jsNavigate = "getDuplicateListViewEntries_js('$module','parenttab=$tabname&start='+this.value+'$url_string');";
	} elseif ($action_val == 'UnifiedSearch') {
		$jsNavigate = "getUnifiedSearchEntries_js('$search_tag','$module','parenttab=$tabname&start='+this.value+'$url_string');";
	} elseif ($module == 'Documents') {
		$jsNavigate = "getListViewEntries_js('$module','parenttab=$tabname&start='+this.value+'$url_string&folderid=$action_val');";
	} else {
		$jsNavigate = "getListViewEntries_js('$module','parenttab=$tabname&start='+this.value+'$url_string');";
	}
	if ($module == 'Documents' && $action_val != 'UnifiedSearch') {
		$url = '&folderid=' . $action_val;
	} else {
		$url = '';
	}
	$jsHandler = "return VT_disableFormSubmit(event);";
	$output .= "<input class='small' name='pagenum' type='text' value='{$navigation_array['current']}'
		style='width: 3em;margin-right: 0.7em;' onchange=\"$jsNavigate\"
		onkeypress=\"$jsHandler\">";
	$output .= "<span name='" . $module . "_listViewCountContainerName' class='small' style='white-space: nowrap;'>";
	if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true) {
		$output .= $app_strings['LBL_LIST_OF'] . ' ' . $navigation_array['verylast'];
	} else {
		$output .= "<img src='" . vtiger_imageurl('windowRefresh.gif', $theme) . "' alt='" . $app_strings['LBL_HOME_COUNT'] . "'
			onclick='getListViewCount(\"" . $module . "\",this,this.parentNode,\"" . $url . "\")'
			align='absmiddle' name='" . $module . "_listViewCountRefreshIcon'/>
			<img name='" . $module . "_listViewCountContainerBusy' src='" . vtiger_imageurl('vtbusy.gif', $theme) . "' style='display: none;'
			align='absmiddle' alt='" . $app_strings['LBL_LOADING'] . "'>";
	}
	$output .='</span>';

	if (($navigation_array['next']) != 0) {
		if ($module == 'Calendar' && $action_val == 'index') {
			$output .= '<a href="javascript:;" onClick="cal_navigation(\'' . $tab_type . '\',\'' . $url_string . '\',\'&start=' . $navigation_array['next'] . '\');" alt="' . $app_strings['LNK_LIST_NEXT'] . '" title="' . $app_strings['LNK_LIST_NEXT'] . '"><img src="' . vtiger_imageurl('next.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="cal_navigation(\'' . $tab_type . '\',\'' . $url_string . '\',\'&start=' . $navigation_array['verylast'] . '\');" alt="' . $app_strings['LBL_LAST'] . '" title="' . $app_strings['LBL_LAST'] . '"><img src="' . vtiger_imageurl('end.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} else if ($action_val == "FindDuplicate") {
			$output .= '<a href="javascript:;" onClick="getDuplicateListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['next'] . $url_string . '\');" alt="' . $app_strings['LNK_LIST_NEXT'] . '" title="' . $app_strings['LNK_LIST_NEXT'] . '"><img src="' . vtiger_imageurl('next.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getDuplicateListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['verylast'] . $url_string . '\');" alt="' . $app_strings['LBL_LAST'] . '" title="' . $app_strings['LBL_LAST'] . '"><img src="' . vtiger_imageurl('end.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} elseif ($action_val == 'UnifiedSearch') {
			$output .= '<a href="javascript:;" onClick="getUnifiedSearchEntries_js(\'' . $search_tag . '\',\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['next'] . $url_string . '\');" alt="' . $app_strings['LNK_LIST_NEXT'] . '" title="' . $app_strings['LNK_LIST_NEXT'] . '"><img src="' . vtiger_imageurl('next.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getUnifiedSearchEntries_js(\'' . $search_tag . '\',\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['verylast'] . $url_string . '\');" alt="' . $app_strings['LBL_LAST'] . '" title="' . $app_strings['LBL_LAST'] . '"><img src="themes/images/end.gif" border="0" align="absmiddle"></a>&nbsp;';
		} elseif ($module == 'Documents') {
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['next'] . $url_string . '&folderid=' . $action_val . '\');" alt="' . $app_strings['LNK_LIST_NEXT'] . '" title="' . $app_strings['LNK_LIST_NEXT'] . '"><img src="' . vtiger_imageurl('next.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['verylast'] . $url_string . '&folderid=' . $action_val . '\');" alt="' . $app_strings['LBL_LAST'] . '" title="' . $app_strings['LBL_LAST'] . '"><img src="' . vtiger_imageurl('end.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		} else {
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['next'] . $url_string . '\');" alt="' . $app_strings['LNK_LIST_NEXT'] . '" title="' . $app_strings['LNK_LIST_NEXT'] . '"><img src="' . vtiger_imageurl('next.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
			$output .= '<a href="javascript:;" onClick="getListViewEntries_js(\'' . $module . '\',\'parenttab=' . $tabname . '&start=' . $navigation_array['verylast'] . $url_string . '\');" alt="' . $app_strings['LBL_LAST'] . '" title="' . $app_strings['LBL_LAST'] . '"><img src="' . vtiger_imageurl('end.gif', $theme) . '" border="0" align="absmiddle"></a>&nbsp;';
		}
	} else {
		$output .= '<img src="' . vtiger_imageurl('next_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
		$output .= '<img src="' . vtiger_imageurl('end_disabled.gif', $theme) . '" border="0" align="absmiddle">&nbsp;';
	}
	$output .= '</td>';
	if ($navigation_array['first'] == '')
		return;
	else
		return $output;
}

function getRecordRangeMessage($listResult, $limitStartRecord, $totalRows = '') {
	global $adb, $app_strings;
	$numRows = $adb->num_rows($listResult);
	$recordListRangeMsg = '';
	if ($numRows > 0) {
		$recordListRangeMsg = $app_strings['LBL_SHOWING'] . ' ' . $app_strings['LBL_RECORDS'] .
				' ' . ($limitStartRecord + 1) . ' - ' . ($limitStartRecord + $numRows);
		if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true) {
			$recordListRangeMsg .= ' ' . $app_strings['LBL_LIST_OF'] . " $totalRows";
		}
	}
	return $recordListRangeMsg;
}

function listQueryNonAdminChange($query, $module, $scope = '') {
	$instance = CRMEntity::getInstance($module);
	return $instance->listQueryNonAdminChange($query, $scope);
}

function html_strlen($str) {
	$chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	return count($chars);
}

function html_substr($str, $start, $length = NULL) {
	if ($length === 0)
		return "";
	//check if we can simply use the built-in functions
	if (strpos($str, '&') === false) { //No entities. Use built-in functions
		if ($length === NULL)
			return substr($str, $start);
		else
			return substr($str, $start, $length);
	}

	// create our array of characters and html entities
	$chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE);
	$html_length = count($chars);
	// check if we can predict the return value and save some processing time
	if (($html_length === 0) or ($start >= $html_length) or (isset($length) and ($length <= -$html_length)))
		return "";

	//calculate start position
	if ($start >= 0) {
		$real_start = $chars[$start][1];
	} else { //start'th character from the end of string
		$start = max($start, -$html_length);
		$real_start = $chars[$html_length + $start][1];
	}
	if (!isset($length)) // no $length argument passed, return all remaining characters
		return substr($str, $real_start);
	else if ($length > 0) { // copy $length chars
		if ($start + $length >= $html_length) { // return all remaining characters
			return substr($str, $real_start);
		} else { //return $length characters
			return substr($str, $real_start, $chars[max($start, 0) + $length][1] - $real_start);
		}
	} else { //negative $length. Omit $length characters from end
		return substr($str, $real_start, $chars[$html_length + $length][1] - $real_start);
	}
}

function counterValue() {
	static $counter = 0;
	$counter = $counter + 1;
	return $counter;
}

?>
