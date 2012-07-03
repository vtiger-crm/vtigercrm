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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/include/utils/DetailViewUtils.php,v 1.188 2005/04/29 05:5 * 4:39 rank Exp
 * Description:  Includes generic helper functions used throughout the application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php'); //new
require_once('include/utils/CommonUtils.php'); //new
require_once('vtlib/Vtiger/Language.php');
require_once('modules/PickList/PickListUtils.php');

/** This function returns the detail view form vtiger_field and and its properties in array format.
 * Param $uitype - UI type of the vtiger_field
 * Param $fieldname - Form vtiger_field name
 * Param $fieldlabel - Form vtiger_field label name
 * Param $col_fields - array contains the vtiger_fieldname and values
 * Param $generatedtype - Field generated type (default is 1)
 * Param $tabid - vtiger_tab id to which the Field belongs to (default is "")
 * Return type is an array
 */
function getDetailViewOutputHtml($uitype, $fieldname, $fieldlabel, $col_fields, $generatedtype, $tabid='', $module='') {
	global $log;
	$log->debug("Entering getDetailViewOutputHtml(" . $uitype . "," . $fieldname . "," . $fieldlabel . "," . $col_fields . "," . $generatedtype . "," . $tabid . ") method ...");
	global $adb;
	global $mod_strings;
	global $app_strings;
	global $current_user;
	global $theme;
	$theme_path = "themes/" . $theme . "/";
	$image_path = $theme_path . "images/";
	$fieldlabel = from_html($fieldlabel);
	$custfld = '';
	$value = '';
	$arr_data = Array();
	$label_fld = Array();
	$data_fld = Array();
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	require('user_privileges/sharing_privileges_' . $current_user->id . '.php');

	// vtlib customization: New uitype to handle relation between modules
	if ($uitype == '10') {
		$fieldlabel = getTranslatedString($fieldlabel, $module);

		$parent_id = $col_fields[$fieldname];
		if (!empty($parent_id)) {
			$parent_module = getSalesEntityType($parent_id);
			$valueTitle = $parent_module;
			if ($app_strings[$valueTitle])
				$valueTitle = $app_strings[$valueTitle];

			$displayValueArray = getEntityName($parent_module, $parent_id);
			if (!empty($displayValueArray)) {
				foreach ($displayValueArray as $key => $value) {
					$displayValue = $value;
				}
			}
			$label_fld = array($fieldlabel,
				"<a href='index.php?module=$parent_module&action=DetailView&record=$parent_id' title='$valueTitle'>$displayValue</a>");
		} else {
			$moduleSpecificMessage = 'MODULE_NOT_SELECTED';
			if ($mod_strings[$moduleSpecificMessage] != "") {
				$moduleSpecificMessage = $mod_strings[$moduleSpecificMessage];
			}
			$label_fld = array($fieldlabel, '');
		}
	} // END
	else if ($uitype == 99) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
		if ($fieldname == 'confirm_password')
			return null;
	}elseif ($uitype == 116 || $uitype == 117) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = getCurrencyName($col_fields[$fieldname]);
		$pick_query = "select * from vtiger_currency_info where currency_status = 'Active' and deleted=0";
		$pickListResult = $adb->pquery($pick_query, array());
		$noofpickrows = $adb->num_rows($pickListResult);

		//Mikecrowe fix to correctly default for custom pick lists
		$options = array();
		$found = false;
		for ($j = 0; $j < $noofpickrows; $j++) {
			$pickListValue = $adb->query_result($pickListResult, $j, 'currency_name');
			$currency_id = $adb->query_result($pickListResult, $j, 'id');
			if ($col_fields[$fieldname] == $currency_id) {
				$chk_val = "selected";
				$found = true;
			} else {
				$chk_val = '';
			}
			$options[$currency_id] = array($pickListValue => $chk_val);
		}
		$label_fld ["options"] = $options;
	} elseif ($uitype == 13 || $uitype == 104) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
	} elseif ($uitype == 16) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = getTranslatedString($col_fields[$fieldname], $module);

		$fieldname = $adb->sql_escape_string($fieldname);
		$pick_query = "select $fieldname from vtiger_$fieldname order by sortorderid";
		$params = array();
		$pickListResult = $adb->pquery($pick_query, $params);
		$noofpickrows = $adb->num_rows($pickListResult);

		$options = array();
		$count = 0;
		$found = false;
		for ($j = 0; $j < $noofpickrows; $j++) {
			$pickListValue = decode_html($adb->query_result($pickListResult, $j, strtolower($fieldname)));
			$col_fields[$fieldname] = decode_html($col_fields[$fieldname]);

			if ($col_fields[$fieldname] == $pickListValue) {
				$chk_val = "selected";
				$count++;
				$found = true;
			} else {
				$chk_val = '';
			}
			$pickListValue = to_html($pickListValue);
			$options[] = array(getTranslatedString($pickListValue), $pickListValue, $chk_val);
		}

		$label_fld ["options"] = $options;
	} elseif ($uitype == 15) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
		$roleid = $current_user->roleid;

		$valueArr = explode("|##|", $col_fields[$fieldname]);
		$picklistValues = getAssignedPicklistValues($fieldname, $roleid, $adb);

		//Mikecrowe fix to correctly default for custom pick lists
		$options = array();
		$count = 0;
		$found = false;
		if (!empty($picklistValues)) {
			foreach ($picklistValues as $order => $pickListValue) {
				if (in_array(trim($pickListValue), array_map("trim", $valueArr))) {
					$chk_val = "selected";
					$pickcount++;
				} else {
					$chk_val = '';
				}
				if (isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate') {
					$options[] = array(htmlentities(getTranslatedString($pickListValue), ENT_QUOTES, $default_charset), $pickListValue, $chk_val);
				} else {
					$options[] = array(getTranslatedString($pickListValue), $pickListValue, $chk_val);
				}
			}

			if ($pickcount == 0 && !empty($value)) {
				$options[] = array($app_strings['LBL_NOT_ACCESSIBLE'], $value, 'selected');
			}
		}
		$label_fld ["options"] = $options;
	} elseif ($uitype == 115) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = getTranslatedString($col_fields[$fieldname]);

		$pick_query = "select * from vtiger_" . $adb->sql_escape_string($fieldname);
		$pickListResult = $adb->pquery($pick_query, array());
		$noofpickrows = $adb->num_rows($pickListResult);
		$options = array();
		$found = false;
		for ($j = 0; $j < $noofpickrows; $j++) {
			$pickListValue = $adb->query_result($pickListResult, $j, strtolower($fieldname));

			if ($col_fields[$fieldname] == $pickListValue) {
				$chk_val = "selected";
				$found = true;
			} else {
				$chk_val = '';
			}
			$options[] = array($pickListValue => $chk_val);
		}
		$label_fld ["options"] = $options;
	} elseif ($uitype == 33) { //uitype 33 added for multiselector picklist - Jeri
		$roleid = $current_user->roleid;
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = str_ireplace(' |##| ', ', ', $col_fields[$fieldname]);

		$picklistValues = getAssignedPicklistValues($fieldname, $roleid, $adb);

		$options = array();
		$selected_entries = Array();
		$selected_entries = explode(' |##| ', $col_fields[$fieldname]);

		if (!empty($picklistValues)) {
			foreach ($picklistValues as $order => $pickListValue) {
				foreach ($selected_entries as $selected_entries_value) {
					if (trim($selected_entries_value) == trim($pickListValue)) {
						$chk_val = 'selected';
						$pickcount++;
						break;
					} else {
						$chk_val = '';
					}
				}
				if (isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate') {
					$options[] = array(htmlentities(getTranslatedString($pickListValue), ENT_QUOTES, $default_charset), $pickListValue, $chk_val);
				} else {
					$options[] = array(getTranslatedString($pickListValue), $pickListValue, $chk_val);
				}
			}
			if ($pickcount == 0 && !empty($value)) {
				$not_access_lbl = "<font color='red'>" . $app_strings['LBL_NOT_ACCESSIBLE'] . "</font>";
				$options[] = array($not_access_lbl, trim($selected_entries_value), 'selected');
			}
		}
		$label_fld ["options"] = $options;
	} elseif ($uitype == 17) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
        $matchPattern = "^[\w]+:\/\/^";
        $value = $col_fields[$fieldname];
        preg_match($matchPattern, $value, $matches);
        if(!empty ($matches[0])){
            $fieldValue = str_replace($matches, "", $value);
            $label_fld[] = $value;
        }else{
            if($value != null)
                $label_fld[] = 'http://'.$value;
            else
                $label_fld[] = '';
        }
	} elseif ($uitype == 19) {
		if ($fieldname == 'notecontent')
			$col_fields[$fieldname] = decode_html($col_fields[$fieldname]);
		else
			$col_fields[$fieldname] = str_replace("&lt;br /&gt;", "<br>", $col_fields[$fieldname]);
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
	}
	elseif ($uitype == 20 || $uitype == 21 || $uitype == 22 || $uitype == 24) { // Armando LC<scher 11.08.2005 -> B'descriptionSpan -> Desc: removed $uitype == 19 and made an aditional elseif above
		if ($uitype == 20)//Fix the issue #4680
			$col_fields[$fieldname] = $col_fields[$fieldname];
		else
			$col_fields[$fieldname] = nl2br($col_fields[$fieldname]);
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
	}
	elseif ($uitype == 51 || $uitype == 50 || $uitype == 73) {
		$account_id = $col_fields[$fieldname];
		if ($account_id != '') {
			$account_name = getAccountName($account_id);
		}
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $account_name;
		$label_fld["secid"] = $account_id;
		$label_fld["link"] = "index.php?module=Accounts&action=DetailView&record=" . $account_id;
		//Account Name View
	} elseif ($uitype == 52 || $uitype == 77 || $uitype == 101) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$user_id = $col_fields[$fieldname];
		$user_name = getOwnerName($user_id);
		if($user_id != '') {
			$assigned_user_id = $user_id;
		} else {
			$assigned_user_id = $current_user->id;
		}
		if (is_admin($current_user)) {
			$label_fld[] = '<a href="index.php?module=Users&action=DetailView&record=' . $user_id . '">' . $user_name . '</a>';
		} else {
			$label_fld[] = $user_name;
		}
		if ($is_admin == false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module)] == 3 or $defaultOrgSharingPermission[getTabid($module)] == 0)) {
			$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id, 'private'), $assigned_user_id);
		} else {
			$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $user_id), $assigned_user_id);
		}
		$label_fld ["options"] = $users_combo;
	} elseif ($uitype == 11) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
	} elseif ($uitype == 53) {
		global $noof_group_rows, $adb;
		$owner_id = $col_fields[$fieldname];

		$user = 'no';
		$result = $adb->pquery("SELECT count(*) as count from vtiger_users where id = ?", array($owner_id));
		if ($adb->query_result($result, 0, 'count') > 0) {
			$user = 'yes';
		}

		$owner_name = getOwnerName($owner_id);
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $owner_name;

		if (is_admin($current_user)) {
			$label_fld["secid"][] = $owner_id;
			if ($user == 'no') {
				$label_fld["link"][] = "index.php?module=Settings&action=GroupDetailView&groupId=" . $owner_id;
			} else {
				$label_fld["link"][] = "index.php?module=Users&action=DetailView&record=" . $owner_id;
			}
			//$label_fld["secid"][] = $groupid;
			//$label_fld["link"][] = "index.php?module=Settings&action=GroupDetailView&groupId=".$groupid;
		}

		//Security Checks
		if ($fieldname == 'assigned_user_id' && $is_admin == false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0)) {
			$result = get_current_user_access_groups($module_name);
		} else {
			$result = get_group_options();
		}
		if ($result)
			$nameArray = $adb->fetch_array($result);


		global $current_user;
		//$value = $user_id;
		if ($owner_id != '') {
			if ($user == 'yes') {
				$label_fld ["options"][] = 'User';
				$assigned_user_id = $owner_id;
				$user_checked = "checked";
				$team_checked = '';
				$user_style = 'display:block';
				$team_style = 'display:none';
			} else {
				//$record = $col_fields["record_id"];
				//$module = $col_fields["record_module"];
				$label_fld ["options"][] = 'Group';
				$assigned_group_id = $owner_id;
				$user_checked = '';
				$team_checked = 'checked';
				$user_style = 'display:none';
				$team_style = 'display:block';
			}
		} else {
			$label_fld ["options"][] = 'User';
			$assigned_user_id = $current_user->id;
			$user_checked = "checked";
			$team_checked = '';
			$user_style = 'display:block';
			$team_style = 'display:none';
		}


		if ($fieldname == 'assigned_user_id' && $is_admin == false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module)] == 3 or $defaultOrgSharingPermission[getTabid($module)] == 0)) {
			$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $current_user->id, 'private'), $assigned_user_id);
		} else {
			$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $current_user->id), $assigned_user_id);
		}

		if ($noof_group_rows != 0) {
			if ($fieldname == 'assigned_user_id' && $is_admin == false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module)] == 3 or $defaultOrgSharingPermission[getTabid($module)] == 0)) {
				$groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $current_user->id, 'private'), $current_user->id);
			} else {
				$groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $current_user->id), $current_user->id);
			}
		}

		$label_fld ["options"][] = $users_combo;
		$label_fld ["options"][] = $groups_combo;
	} elseif ($uitype == 55 || $uitype == 255) {
		if ($tabid == 4) {
			$query = "select vtiger_contactdetails.imagename from vtiger_contactdetails where contactid=?";
			$result = $adb->pquery($query, array($col_fields['record_id']));
			$imagename = $adb->query_result($result, 0, 'imagename');
			if ($imagename != '') {
				$imgpath = "test/contact/" . $imagename;
				$label_fld[] = getTranslatedString($fieldlabel, $module);
			} else {
				$label_fld[] = getTranslatedString($fieldlabel, $module);
			}
		} else {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
		}
		$value = $col_fields[$fieldname];
		if ($uitype == 255) {
			global $currentModule;
			$fieldpermission = getFieldVisibilityPermission($currentModule, $current_user->id, 'firstname');
		}
		if ($uitype == 255 && $fieldpermission == 0 && $fieldpermission != '') {
			$fieldvalue[] = '';
		} else {
			$roleid = $current_user->roleid;
			$subrole = getRoleSubordinates($roleid);
			if (count($subrole) > 0) {
				$roleids = implode("','", $subrole);
				$roleids = $roleids . "','" . $roleid;
			} else {
				$roleids = $roleid;
			}
			if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
				$pick_query = "select salutationtype from vtiger_salutationtype order by salutationtype";
				$params = array();
			} else {
				$pick_query = "select * from vtiger_salutationtype left join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid=vtiger_salutationtype.picklist_valueid where picklistid in (select picklistid from vtiger_picklist where name='salutationtype') and roleid=? order by salutationtype";
				$params = array($current_user->roleid);
			}
			$pickListResult = $adb->pquery($pick_query, $params);
			$noofpickrows = $adb->num_rows($pickListResult);
			$sal_value = $col_fields["salutationtype"];
			$salcount = 0;
			for ($j = 0; $j < $noofpickrows; $j++) {
				$pickListValue = $adb->query_result($pickListResult, $j, "salutationtype");

				if ($sal_value == $pickListValue) {
					$chk_val = "selected";
					$salcount++;
				} else {
					$chk_val = '';
				}
			}
			if ($salcount == 0 && $sal_value != '') {
				$notacc = $app_strings['LBL_NOT_ACCESSIBLE'];
			}
			$sal_value = $col_fields["salutationtype"];
			if ($sal_value == '--None--') {
				$sal_value = '';
			}
			$label_fld["salut"] = getTranslatedString($sal_value);
			$label_fld["notaccess"] = $notacc;
		}
		$label_fld[] = $value;
	} elseif ($uitype == 56) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$value = $col_fields[$fieldname];
		if ($value == 1) {
			//Since "yes" is not been translated it is given as app strings here..
			$displayValue = $app_strings['yes'];
		} else {
			$displayValue = $app_strings['no'];
		}
		$label_fld[] = $displayValue;
	} elseif ($uitype == 57) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$contact_id = $col_fields[$fieldname];
		if ($contact_id != '') {
			$displayValueArray = getEntityName('Contacts', $contact_id);
			if (!empty($displayValueArray)) {
				foreach ($displayValueArray as $key => $field_value) {
					$contact_name = $field_value;
				}
			} else {
				$contact_name='';
			}
		}
		$label_fld[] = $contact_name;
		$label_fld["secid"] = $contact_id;
		$label_fld["link"] = "index.php?module=Contacts&action=DetailView&record=" . $contact_id;
	} elseif ($uitype == 58) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$campaign_id = $col_fields[$fieldname];
		if ($campaign_id != '') {
			$campaign_name = getCampaignName($campaign_id);
		}
		$label_fld[] = $campaign_name;
		$label_fld["secid"] = $campaign_id;
		$label_fld["link"] = "index.php?module=Campaigns&action=DetailView&record=" . $campaign_id;
	} elseif ($uitype == 59) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$product_id = $col_fields[$fieldname];
		if ($product_id != '') {
			$product_name = getProductName($product_id);
		}
		//Account Name View
		$label_fld[] = $product_name;
		$label_fld["secid"] = $product_id;
		$label_fld["link"] = "index.php?module=Products&action=DetailView&record=" . $product_id;
	} elseif ($uitype == 61) {
		global $adb;
		$label_fld[] = getTranslatedString($fieldlabel, $module);

		if ($tabid == 10) {
			$attach_result = $adb->pquery("select * from vtiger_seattachmentsrel where crmid = ?", array($col_fields['record_id']));
			for ($ii = 0; $ii < $adb->num_rows($attach_result); $ii++) {
				$attachmentid = $adb->query_result($attach_result, $ii, 'attachmentsid');
				if ($attachmentid != '') {
					$attachquery = "select * from vtiger_attachments where attachmentsid=?";
					$attachmentsname = $adb->query_result($adb->pquery($attachquery, array($attachmentid)), 0, 'name');
					if ($attachmentsname != '')
						$custfldval = '<a href = "index.php?module=uploads&action=downloadfile&return_module=' . $col_fields['record_module'] . '&fileid=' . $attachmentid . '&entityid=' . $col_fields['record_id'] . '">' . $attachmentsname . '</a>';
					else
						$custfldval = '';
				}
				$label_fld['options'][] = $custfldval;
			}
		}
		else {
			$attachmentid = $adb->query_result($adb->pquery("select * from vtiger_seattachmentsrel where crmid = ?", array($col_fields['record_id'])), 0, 'attachmentsid');
			if ($col_fields[$fieldname] == '' && $attachmentid != '') {
				$attachquery = "select * from vtiger_attachments where attachmentsid=?";
				$col_fields[$fieldname] = $adb->query_result($adb->pquery($attachquery, array($attachmentid)), 0, 'name');
			}

			//This is added to strip the crmid and _ from the file name and show the original filename
			//$org_filename = ltrim($col_fields[$fieldname],$col_fields['record_id'].'_');
			/* Above line is not required as the filename in the database is stored as it is and doesn't have crmid attached to it.
			  This was the cause for the issue reported in ticket #4645 */
			$org_filename = $col_fields[$fieldname];
			// For Backward Compatibility version < 5.0.4
			$filename_pos = strpos($org_filename, $col_fields['record_id'] . '_');
			if ($filename_pos === 0) {
				$start_idx = $filename_pos + strlen($col_fields['record_id'] . '_');
				$org_filename = substr($org_filename, $start_idx);
			}
			if ($org_filename != '') {
				if ($col_fields['filelocationtype'] == 'E') {
					if ($col_fields['filestatus'] == 1) {//&& strlen($col_fields['filename']) > 7  ){
						$custfldval = '<a target="_blank" href =' . $col_fields['filename'] . ' onclick=\'javascript:dldCntIncrease(' . $col_fields['record_id'] . ');\'>' . $col_fields[$fieldname] . '</a>';
					} else {
						$custfldval = $col_fields[$fieldname];
					}
				} elseif ($col_fields['filelocationtype'] == 'I') {
					if ($col_fields['filestatus'] == 1) {
						$custfldval = '<a href = "index.php?module=uploads&action=downloadfile&return_module=' . $col_fields['record_module'] . '&fileid=' . $attachmentid . '&entityid=' . $col_fields['record_id'] . '" onclick=\'javascript:dldCntIncrease(' . $col_fields['record_id'] . ');\'>' . $col_fields[$fieldname] . '</a>';
					} else {
						$custfldval = $col_fields[$fieldname];
					}
				} else
					$custfldval = '';
			}
			$label_fld[] = $custfldval;
		}
	}
	elseif ($uitype == 28) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$attachmentid = $adb->query_result($adb->pquery("select * from vtiger_seattachmentsrel where crmid = ?", array($col_fields['record_id'])), 0, 'attachmentsid');
		if ($col_fields[$fieldname] == '' && $attachmentid != '') {
			$attachquery = "select * from vtiger_attachments where attachmentsid=?";
			$col_fields[$fieldname] = $adb->query_result($adb->pquery($attachquery, array($attachmentid)), 0, 'name');
		}
		$org_filename = $col_fields[$fieldname];
		// For Backward Compatibility version < 5.0.4
		$filename_pos = strpos($org_filename, $col_fields['record_id'] . '_');
		if ($filename_pos === 0) {
			$start_idx = $filename_pos + strlen($col_fields['record_id'] . '_');
			$org_filename = substr($org_filename, $start_idx);
		}
		if ($org_filename != '') {
			if ($col_fields['filelocationtype'] == 'E') {
				if ($col_fields['filestatus'] == 1) {//&& strlen($col_fields['filename']) > 7  ){
					$custfldval = '<a target="_blank" href =' . $col_fields['filename'] . ' onclick=\'javascript:dldCntIncrease(' . $col_fields['record_id'] . ');\'>' . $col_fields[$fieldname] . '</a>';
				} else {
					$custfldval = $col_fields[$fieldname];
				}
			} elseif ($col_fields['filelocationtype'] == 'I') {
				if ($col_fields['filestatus'] == 1) {
					$custfldval = '<a href = "index.php?module=uploads&action=downloadfile&return_module=' . $col_fields['record_module'] . '&fileid=' . $attachmentid . '&entityid=' . $col_fields['record_id'] . '" onclick=\'javascript:dldCntIncrease(' . $col_fields['record_id'] . ');\'>' . $col_fields[$fieldname] . '</a>';
				} else {
					$custfldval = $col_fields[$fieldname];
				}
			} else
				$custfldval = '';
		}
		$label_fld[] = $custfldval;
	}
	elseif ($uitype == 69) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		if ($tabid == 14) {
			$images = array();
			$query = 'select productname, vtiger_attachments.path, vtiger_attachments.attachmentsid, vtiger_attachments.name,vtiger_crmentity.setype from vtiger_products left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_products.productid inner join vtiger_attachments on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid where vtiger_crmentity.setype="Products Image" and productid=?';
			$result_image = $adb->pquery($query, array($col_fields['record_id']));
			for ($image_iter = 0; $image_iter < $adb->num_rows($result_image); $image_iter++) {
				$image_id_array[] = $adb->query_result($result_image, $image_iter, 'attachmentsid');

				//decode_html  - added to handle UTF-8   characters in file names
				//urlencode    - added to handle special characters like #, %, etc.,
				$image_array[] = urlencode(decode_html($adb->query_result($result_image, $image_iter, 'name')));
				$image_orgname_array[] = decode_html($adb->query_result($result_image, $image_iter, 'name'));

				$imagepath_array[] = $adb->query_result($result_image, $image_iter, 'path');
			}
			if (count($image_array) > 1) {
				if (count($image_array) < 4)
					$sides = count($image_array) * 2;
				else
					$sides=8;

				$image_lists = '<div id="Carousel" style="position:relative;vertical-align: middle;">
					<img src="modules/Products/placeholder.gif" width="571" height="117" style="position:relative;">
					</div><script>var Car_NoOfSides=' . $sides . '; Car_Image_Sources=new Array(';

				for ($image_iter = 0; $image_iter < count($image_array); $image_iter++) {
					$images[] = '"' . $imagepath_array[$image_iter] . $image_id_array[$image_iter] . "_" . $image_array[$image_iter] . '","' . $imagepath_array[$image_iter] . $image_id_array[$image_iter] . "_" . $image_array[$image_iter] . '"';
				}
				$image_lists .=implode(',', $images) . ');</script><script language="JavaScript" type="text/javascript" src="modules/Products/Productsslide.js"></script><script language="JavaScript" type="text/javascript">Carousel();</script>';
				$label_fld[] = $image_lists;
			} elseif (count($image_array) == 1) {
				list($pro_image_width, $pro_image_height) = getimagesize($imagepath_array[0] . $image_id_array[0] . "_" . $image_orgname_array[0]);
				if ($pro_image_width > 450 || $pro_image_height > 300)
					$label_fld[] = '<img src="' . $imagepath_array[0] . $image_id_array[0] . "_" . $image_array[0] . '" border="0" width="450" height="300">';
				else
					$label_fld[] = '<img src="' . $imagepath_array[0] . $image_id_array[0] . "_" . $image_array[0] . '" border="0" width="' . $pro_image_width . '" height="' . $pro_image_height . '">';
			}else {
				$label_fld[] = '';
			}
		}
		if ($tabid == 4) {
			//$imgpath = getModuleFileStoragePath('Contacts').$col_fields[$fieldname];
			$sql = "select vtiger_attachments.*,vtiger_crmentity.setype from vtiger_attachments inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_attachments.attachmentsid where vtiger_crmentity.setype='Contacts Image' and vtiger_seattachmentsrel.crmid=?";
			$image_res = $adb->pquery($sql, array($col_fields['record_id']));
			$image_id = $adb->query_result($image_res, 0, 'attachmentsid');
			$image_path = $adb->query_result($image_res, 0, 'path');


			//decode_html  - added to handle UTF-8   characters in file names
			//urlencode    - added to handle special characters like #, %, etc.,
			$image_name = urlencode(decode_html($adb->query_result($image_res, 0, 'name')));

			$imgpath = $image_path . $image_id . "_" . $image_name;
			if ($image_name != '')
				$label_fld[] = '<img src="' . $imgpath . '" alt="' . $mod_strings['Contact Image'] . '" title= "' . $mod_strings['Contact Image'] . '">';
			else
				$label_fld[] = '';
		}
	}
	elseif ($uitype == 62) {
		$value = $col_fields[$fieldname];
		if ($value != '') {
			$parent_module = getSalesEntityType($value);
			if ($parent_module == "Leads") {
				$label_fld[] = $app_strings['LBL_LEAD_NAME'];
				$displayValueArray = getEntityName($parent_module, $value);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $key => $field_value) {
						$lead_name = $field_value;
					}
				}

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $lead_name . '</a>';
			} elseif ($parent_module == "Accounts") {
				$label_fld[] = $app_strings['LBL_ACCOUNT_NAME'];
				$sql = "select * from  vtiger_account where accountid=?";
				$result = $adb->pquery($sql, array($value));
				$account_name = $adb->query_result($result, 0, "accountname");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $account_name . '</a>';
			} elseif ($parent_module == "Potentials") {
				$label_fld[] = $app_strings['LBL_POTENTIAL_NAME'];
				$sql = "select * from  vtiger_potential where potentialid=?";
				$result = $adb->pquery($sql, array($value));
				$potentialname = $adb->query_result($result, 0, "potentialname");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $potentialname . '</a>';
			} elseif ($parent_module == "Products") {
				$label_fld[] = $app_strings['LBL_PRODUCT_NAME'];
				$sql = "select * from  vtiger_products where productid=?";
				$result = $adb->pquery($sql, array($value));
				$productname = $adb->query_result($result, 0, "productname");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $productname . '</a>';
			} elseif ($parent_module == "PurchaseOrder") {
				$label_fld[] = $app_strings['LBL_PORDER_NAME'];
				$sql = "select * from  vtiger_purchaseorder where purchaseorderid=?";
				$result = $adb->pquery($sql, array($value));
				$pordername = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $pordername . '</a>';
			} elseif ($parent_module == "SalesOrder") {
				$label_fld[] = $app_strings['LBL_SORDER_NAME'];
				$sql = "select * from  vtiger_salesorder where salesorderid=?";
				$result = $adb->pquery($sql, array($value));
				$sordername = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $sordername . '</a>';
			} elseif ($parent_module == "Invoice") {
				$label_fld[] = $app_strings['LBL_INVOICE_NAME'];
				$sql = "select * from  vtiger_invoice where invoiceid=?";
				$result = $adb->pquery($sql, array($value));
				$invoicename = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $invoicename . '</a>';
			} elseif ($parent_module == "Quotes") {
				$label_fld[] = $app_strings['LBL_QUOTES_NAME'];
				$sql = "select * from  vtiger_quotes where quoteid=?";
				$result = $adb->pquery($sql, array($value));
				$quotename = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $quotename . '</a>';
			} elseif ($parent_module == "HelpDesk") {
				$label_fld[] = $app_strings['LBL_HELPDESK_NAME'];
				$sql = "select * from  vtiger_troubletickets where ticketid=?";
				$result = $adb->pquery($sql, array($value));
				$title = $adb->query_result($result, 0, "title");
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $title . '</a>';
			}
		} else {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $value;
		}
	} elseif ($uitype == 105) {//Added for user image
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		//$imgpath = getModuleFileStoragePath('Contacts').$col_fields[$fieldname];
		$sql = "select vtiger_attachments.* from vtiger_attachments left join vtiger_salesmanattachmentsrel on vtiger_salesmanattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid where vtiger_salesmanattachmentsrel.smid=?";
		$image_res = $adb->pquery($sql, array($col_fields['record_id']));
		$image_id = $adb->query_result($image_res, 0, 'attachmentsid');
		$image_path = $adb->query_result($image_res, 0, 'path');
		$image_name = $adb->query_result($image_res, 0, 'name');
		$imgpath = $image_path . $image_id . "_" . $image_name;
		if ($image_name != '') {
			//Added the following check for the image to retain its in original size.
			list($pro_image_width, $pro_image_height) = getimagesize(decode_html($imgpath));
			$label_fld[] = '<a href="' . $imgpath . '" target="_blank"><img src="' . $imgpath . '" width="' . $pro_image_width . '" height="' . $pro_image_height . '" alt="' . $col_fields['user_name'] . '" title="' . $col_fields['user_name'] . '" border="0"></a>';
		} else
			$label_fld[] = '';
	}
	elseif ($uitype == 66) {
		$value = $col_fields[$fieldname];
		if ($value != '') {
			$parent_module = getSalesEntityType($value);
			if ($parent_module == "Leads") {
				$label_fld[] = $app_strings['LBL_LEAD_NAME'];
				$displayValueArray = getEntityName($parent_module, $value);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $key => $field_value) {
						$lead_name = $field_value;
					}
				}
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $lead_name . '</a>';
			} elseif ($parent_module == "Accounts") {
				$label_fld[] = $app_strings['LBL_ACCOUNT_NAME'];
				$sql = "select * from  vtiger_account where accountid=?";
				$result = $adb->pquery($sql, array($value));
				$account_name = $adb->query_result($result, 0, "accountname");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $account_name . '</a>';
			} elseif ($parent_module == "Potentials") {
				$label_fld[] = $app_strings['LBL_POTENTIAL_NAME'];
				$sql = "select * from  vtiger_potential where potentialid=?";
				$result = $adb->pquery($sql, array($value));
				$potentialname = $adb->query_result($result, 0, "potentialname");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $potentialname . '</a>';
			} elseif ($parent_module == "Quotes") {
				$label_fld[] = $app_strings['LBL_QUOTE_NAME'];
				$sql = "select * from  vtiger_quotes where quoteid=?";
				$result = $adb->pquery($sql, array($value));
				$quotename = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $quotename . '</a>';
			} elseif ($parent_module == "PurchaseOrder") {
				$label_fld[] = $app_strings['LBL_PORDER_NAME'];
				$sql = "select * from  vtiger_purchaseorder where purchaseorderid=?";
				$result = $adb->pquery($sql, array($value));
				$pordername = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $pordername . '</a>';
			} elseif ($parent_module == "SalesOrder") {
				$label_fld[] = $app_strings['LBL_SORDER_NAME'];
				$sql = "select * from  vtiger_salesorder where salesorderid=?";
				$result = $adb->pquery($sql, array($value));
				$sordername = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $sordername . '</a>';
			} elseif ($parent_module == "Invoice") {
				$label_fld[] = $app_strings['LBL_INVOICE_NAME'];
				$sql = "select * from  vtiger_invoice where invoiceid=?";
				$result = $adb->pquery($sql, array($value));
				$invoicename = $adb->query_result($result, 0, "subject");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $invoicename . '</a>';
			} elseif ($parent_module == "Campaigns") {
				$label_fld[] = $app_strings['LBL_CAMPAIGN_NAME'];
				$sql = "select * from  vtiger_campaign where campaignid=?";
				$result = $adb->pquery($sql, array($value));
				$campaignname = $adb->query_result($result, 0, "campaignname");
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $campaignname . '</a>';
			} elseif ($parent_module == "HelpDesk") {
				$label_fld[] = $app_strings['LBL_HELPDESK_NAME'];
				$sql = "select * from  vtiger_troubletickets where ticketid=?";
				$result = $adb->pquery($sql, array($value));
				$tickettitle = $adb->query_result($result, 0, "title");
				if (strlen($tickettitle) > 25) {
					$tickettitle = substr($tickettitle, 0, 25) . '...';
				}
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $tickettitle . '</a>';
			}
		} else {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $value;
		}
	} elseif ($uitype == 67) {
		$value = $col_fields[$fieldname];
		if ($value != '') {
			$parent_module = getSalesEntityType($value);
			if ($parent_module == "Leads") {
				$label_fld[] = $app_strings['LBL_LEAD_NAME'];
				$displayValueArray = getEntityName($parent_module, $value);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $key => $field_value) {
						$lead_name = $field_value;
					}
				}
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $lead_name . '</a>';
			} elseif ($parent_module == "Contacts") {
				$label_fld[] = $app_strings['LBL_CONTACT_NAME'];
				$displayValueArray = getEntityName($parent_module, $value);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $key => $field_value) {
						$contact_name = $field_value;
					}
				} else {
					$contact_name='';
				}
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $contact_name . '</a>';
			}
		} else {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $value;
		}
	}
	//added by raju/rdhital for better emails
	elseif ($uitype == 357) {
		$value = $col_fields[$fieldname];
		if ($value != '') {
			$parent_name = '';
			$parent_id = '';
			$myemailid = $_REQUEST['record'];
			$mysql = "select crmid from vtiger_seactivityrel where activityid=?";
			$myresult = $adb->pquery($mysql, array($myemailid));
			$mycount = $adb->num_rows($myresult);
			if ($mycount > 1) {
				$label_fld[] = $app_strings['LBL_RELATED_TO'];
				$label_fld[] = $app_strings['LBL_MULTIPLE'];
			} else {
				$parent_module = getSalesEntityType($value);
				if ($parent_module == "Leads") {
					$label_fld[] = $app_strings['LBL_LEAD_NAME'];
					$displayValueArray = getEntityName($parent_module, $value);
					if (!empty($displayValueArray)) {
						foreach ($displayValueArray as $key => $field_value) {
							$lead_name = $field_value;
						}
					}
					$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $lead_name . '</a>';
				} elseif ($parent_module == "Contacts") {
					$label_fld[] = $app_strings['LBL_CONTACT_NAME'];
					$displayValueArray = getEntityName($parent_module, $value);
					if (!empty($displayValueArray)) {
						foreach ($displayValueArray as $key => $field_value) {
							$contact_name = $field_value;
						}
					} else {
					$contact_name='';
				}
					$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $contact_name . '</a>';
				} elseif ($parent_module == "Accounts") {
					$label_fld[] = $app_strings['LBL_ACCOUNT_NAME'];
					$sql = "select * from  vtiger_account where accountid=?";
					$result = $adb->pquery($sql, array($value));
					$accountname = $adb->query_result($result, 0, "accountname");
					$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $accountname . '</a>';
				}
			}
		} else {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $value;
		}
	}//Code added by raju for better email ends
	elseif ($uitype == 68) {
		$value = $col_fields[$fieldname];
		if ($value != '') {
			$parent_module = getSalesEntityType($value);
			if ($parent_module == "Contacts") {
				$label_fld[] = $app_strings['LBL_CONTACT_NAME'];
				$displayValueArray = getEntityName($parent_module, $value);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $key => $field_value) {
						$contact_name = $field_value;
					}
				} else {
					$contact_name='';
				}
				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $contact_name . '</a>';
			} elseif ($parent_module == "Accounts") {
				$label_fld[] = $app_strings['LBL_ACCOUNT_NAME'];
				$sql = "select * from vtiger_account where accountid=?";
				$result = $adb->pquery($sql, array($value));
				$account_name = $adb->query_result($result, 0, "accountname");

				$label_fld[] = '<a href="index.php?module=' . $parent_module . '&action=DetailView&record=' . $value . '">' . $account_name . '</a>';
			} else {
				$value = '';
				$label_fld[] = getTranslatedString($fieldlabel, $module);
				$label_fld[] = $value;
			}
		} else {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $value;
		}
	} elseif ($uitype == 63) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname] . 'h&nbsp; ' . $col_fields['duration_minutes'] . 'm';
	} elseif ($uitype == 6) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		if ($col_fields[$fieldname] == '0')
			$col_fields[$fieldname] = '';
		if ($col_fields['time_start'] != '') {
			$start_time = $col_fields['time_start'];
		}
		$dateValue = $col_fields[$fieldname];
		if ($col_fields[$fieldname] == '0000-00-00' || empty($dateValue)) {
			$displayValue = '';
		} else {
			if (empty($start_time) && strpos($col_fields[$fieldname], ' ') == false) {
				$displayValue = DateTimeField::convertToUserFormat($col_fields[$fieldname]);
			} else {
				if(!empty($start_time)) {
					$date = new DateTimeField($col_fields[$fieldname].' '.$start_time);
				} else {
					$date = new DateTimeField($col_fields[$fieldname]);
				}
				$displayValue = $date->getDisplayDateTimeValue();
			}
		}
		$label_fld[] = $displayValue;
	} elseif ($uitype == 5 || $uitype == 23 || $uitype == 70) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$dateValue = $col_fields[$fieldname];
		if ($col_fields['time_end'] != '' && ($tabid == 9 || $tabid == 16) && $uitype == 23) {
			$end_time = $col_fields['time_end'];
		}
		if ($dateValue == '0000-00-00' || empty($dateValue)) {
			$displayValue = '';
		} else {
			if (empty($end_time) && strpos($dateValue, ' ') == false) {
				$displayValue = DateTimeField::convertToUserFormat($col_fields[$fieldname]);
			} else {
				if(!empty($end_time)) {
					$date = new DateTimeField($col_fields[$fieldname].' '.$end_time);
				} else {
					$date = new DateTimeField($col_fields[$fieldname]);
				}
				$displayValue = $date->getDisplayDateTimeValue();
			}
		}
		$label_fld[] = $displayValue;
	}
	elseif ($uitype == 71 || $uitype == 72) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$currencyField = new CurrencyField($col_fields[$fieldname]);
		if($uitype == 72) {
			// Some of the currency fields like Unit Price, Total, Sub-total etc of Inventory modules, do not need currency conversion
			if ($fieldname == 'unit_price') {
				$rate_symbol = getCurrencySymbolandCRate(getProductBaseCurrency($col_fields['record_id'], $module));
				$label_fld[] = $currencyField->getDisplayValue(null, true);
				$label_fld["cursymb"] = $rate_symbol['symbol'];
			} else {
				$currency_info = getInventoryCurrencyInfo($module, $col_fields['record_id']);
				$label_fld[] = $currencyField->getDisplayValue(null, true);
				$label_fld["cursymb"] = $currency_info['currency_symbol'];
			}
		} else {
			$label_fld[] = $currencyField->getDisplayValue();
			$label_fld["cursymb"] = $currencyField->getCurrencySymbol();
		}
	} elseif ($uitype == 75 || $uitype == 81) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$vendor_id = $col_fields[$fieldname];
		if ($vendor_id != '') {
			$vendor_name = getVendorName($vendor_id);
		}
		$label_fld[] = $vendor_name;
		$label_fld["secid"] = $vendor_id;
		$label_fld["link"] = "index.php?module=Vendors&action=DetailView&record=" . $vendor_id;
	} elseif ($uitype == 76) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$potential_id = $col_fields[$fieldname];
		if ($potential_id != '') {
			$potential_name = getPotentialName($potential_id);
		}
		$label_fld[] = $potential_name;
		$label_fld["secid"] = $potential_id;
		$label_fld["link"] = "index.php?module=Potentials&action=DetailView&record=" . $potential_id;
	} elseif ($uitype == 78) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$quote_id = $col_fields[$fieldname];
		if ($quote_id != '') {
			$quote_name = getQuoteName($quote_id);
		}
		$label_fld[] = $quote_name;
		$label_fld["secid"] = $quote_id;
		$label_fld["link"] = "index.php?module=Quotes&action=DetailView&record=" . $quote_id;
	} elseif ($uitype == 79) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$purchaseorder_id = $col_fields[$fieldname];
		if ($purchaseorder_id != '') {
			$purchaseorder_name = getPoName($purchaseorder_id);
		}
		$label_fld[] = $purchaseorder_name;
		$label_fld["secid"] = $purchaseorder_id;
		$label_fld["link"] = "index.php?module=PurchaseOrder&action=DetailView&record=" . $purchaseorder_id;
	} elseif ($uitype == 80) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$salesorder_id = $col_fields[$fieldname];
		if ($salesorder_id != '') {
			$salesorder_name = getSoName($salesorder_id);
		}
		$label_fld[] = $salesorder_name;
		$label_fld["secid"] = $salesorder_id;
		$label_fld["link"] = "index.php?module=SalesOrder&action=DetailView&record=" . $salesorder_id;
	} elseif ($uitype == 30) {
		$rem_days = 0;
		$rem_hrs = 0;
		$rem_min = 0;
		$reminder_str = "";
		$rem_days = floor($col_fields[$fieldname] / (24 * 60));
		$rem_hrs = floor(($col_fields[$fieldname] - $rem_days * 24 * 60) / 60);
		$rem_min = ($col_fields[$fieldname] - $rem_days * 24 * 60) % 60;

		$label_fld[] = getTranslatedString($fieldlabel, $module);
		if ($col_fields[$fieldname]) {
			$reminder_str = $rem_days . '&nbsp;' . $mod_strings['LBL_DAYS'] . '&nbsp;' . $rem_hrs . '&nbsp;' . $mod_strings['LBL_HOURS'] . '&nbsp;' . $rem_min . '&nbsp;' . $mod_strings['LBL_MINUTES'] . '&nbsp;&nbsp;' . $mod_strings['LBL_BEFORE_EVENT'];
		}
		$label_fld[] = '&nbsp;' . $reminder_str;
	} elseif ($uitype == 98) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		if (is_admin($current_user))
			$label_fld[] = '<a href="index.php?module=Settings&action=RoleDetailView&roleid=' . $col_fields[$fieldname] . '">' . getRoleName($col_fields[$fieldname]) . '</a>';
		else
			$label_fld[] = getRoleName($col_fields[$fieldname]);
	}elseif ($uitype == 85) { //Added for Skype by Minnie
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
	} elseif ($uitype == 26) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$query = "select foldername from vtiger_attachmentsfolder where folderid = ?";
		$result = $adb->pquery($query, array($col_fields[$fieldname]));
		$folder_name = $adb->query_result($result, 0, "foldername");
		$label_fld[] = $folder_name;
	} elseif ($uitype == 27) {
		if ($col_fields[$fieldname] == 'I') {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $mod_strings['LBL_INTERNAL'];
		} else {
			$label_fld[] = getTranslatedString($fieldlabel, $module);
			$label_fld[] = $mod_strings['LBL_EXTERNAL'];
		}
	} elseif ($uitype == 31) {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $col_fields[$fieldname];
		$options = array();
		$themeList = get_themes();
		foreach ($themeList as $theme) {
			if ($current_user->theme == $theme) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$options[] = array(getTranslatedString($theme), $theme, $selected);
		}
		$label_fld ["options"] = $options;
	} elseif ($uitype == 32) {
		$options = array();
		$languageList = Vtiger_Language::getAll();
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = (isset($languageList[$col_fields[$fieldname]])) ?
				$languageList[$col_fields[$fieldname]] : $col_fields[$fieldname];
		foreach ($languageList as $prefix => $label) {
			if ($current_user->language == $prefix) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$options[] = array(getTranslatedString($label), $prefix, $selected);
		}
		$label_fld ["options"] = $options;
	} else {
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		if ($col_fields[$fieldname] == '0' && $fieldname != 'filedownloadcount' && $fieldname != 'filestatus' && $fieldname != 'filesize')
			$col_fields[$fieldname] = '';

		//code for Documents module :start
		if ($tabid == 8) {
			$downloadtype = $col_fields['filelocationtype'];
			if ($fieldname == 'filename') {
				if ($downloadtype == 'I') {
					//$file_value = $mod_strings['LBL_INTERNAL'];
					$fld_value = $col_fields['filename'];
					$ext_pos = strrpos($fld_value, ".");
					$ext = substr($fld_value, $ext_pos + 1);
					$ext = strtolower($ext);
					if ($ext == 'bin' || $ext == 'exe' || $ext == 'rpm')
						$fileicon = "<img src='" . vtiger_imageurl('fExeBin.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
					elseif ($ext == 'jpg' || $ext == 'gif' || $ext == 'bmp')
						$fileicon = "<img src='" . vtiger_imageurl('fbImageFile.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
					elseif ($ext == 'txt' || $ext == 'doc' || $ext == 'xls')
						$fileicon = "<img src='" . vtiger_imageurl('fbTextFile.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
					elseif ($ext == 'zip' || $ext == 'gz' || $ext == 'rar')
						$fileicon = "<img src='" . vtiger_imageurl('fbZipFile.gif', $theme) . "' hspace='3' align='absmiddle'	border='0'>";
					else
						$fileicon="<img src='" . vtiger_imageurl('fbUnknownFile.gif', $theme) . "' hspace='3' align='absmiddle' border='0'>";
				}
				else {
					$fld_value = $col_fields['filename'];
					$fileicon = "<img src='" . vtiger_imageurl('fbLink.gif', $theme) . "' alt='" . $mod_strings['LBL_EXTERNAL_LNK'] . "' title='" . $mod_strings['LBL_EXTERNAL_LNK'] . "' hspace='3' align='absmiddle' border='0'>";
				}
				$label_fld[] = $fileicon . $fld_value;
			}
			if ($fieldname == 'filesize') {
				if ($col_fields['filelocationtype'] == 'I') {
					$filesize = $col_fields[$fieldname];
					if ($filesize < 1024)
						$label_fld[] = $filesize . ' B';
					elseif ($filesize > 1024 && $filesize < 1048576)
						$label_fld[] = round($filesize / 1024, 2) . ' KB';
					else if ($filesize > 1048576)
						$label_fld[] = round($filesize / (1024 * 1024), 2) . ' MB';
				}
				else {
					$label_fld[] = ' --';
				}
			}
			if ($fieldname == 'filetype' && $col_fields['filelocationtype'] == 'E') {
				$label_fld[] = ' --';
			}
			/* if($fieldname == 'filestatus')
			  {
			  $filestatus = $col_fields[$fieldname];
			  if($filestatus == 0)
			  $label_fld[]=$mod_strings['LBL_ACTIVE'];
			  else
			  $label_fld[]=$mod_strings['LBL_INACTIVE'];
			  } */
		}
		//code for Documents module :end
		$label_fld[] = $col_fields[$fieldname];
	}
	$label_fld[] = $uitype;

	//sets whether the currenct user is admin or not
	if (is_admin($current_user)) {
		$label_fld["isadmin"] = 1;
	} else {
		$label_fld["isadmin"] = 0;
	}

	$log->debug("Exiting getDetailViewOutputHtml method ...");
	return $label_fld;
}

/** This function returns a HTML output of associated vtiger_products for a given entity (Quotes,Invoice,Sales order or Purchase order)
 * Param $module - module name
 * Param $focus - module object
 * Return type string
 */
function getDetailAssociatedProducts($module, $focus) {
	global $log;
	$log->debug("Entering getDetailAssociatedProducts(" . $module . "," . get_class($focus) . ") method ...");
	global $adb;
	global $mod_strings;
	global $theme;
	global $log;
	global $app_strings, $current_user;
	$theme_path = "themes/" . $theme . "/";
	$image_path = $theme_path . "images/";

	if ($module != 'PurchaseOrder') {
		$colspan = '2';
	} else {
		$colspan = '1';
	}

	//Get the taxtype of this entity
	$taxtype = getInventoryTaxType($module, $focus->id);
	$currencytype = getInventoryCurrencyInfo($module, $focus->id);

	$output = '';
	//Header Rows
	$output .= '

	<table width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="crmTable" id="proTab">
	   <tr valign="top">
	   	<td colspan="' . $colspan . '" class="dvInnerHeader"><b>' . $app_strings['LBL_ITEM_DETAILS'] . '</b></td>
		<td class="dvInnerHeader" align="center" colspan="2"><b>' .
			$app_strings['LBL_CURRENCY'] . ' : </b>' . getTranslatedCurrencyString($currencytype['currency_name']) . ' (' . $currencytype['currency_symbol'] . ')
		</td>
		<td class="dvInnerHeader" align="center" colspan="2"><b>' .
			$app_strings['LBL_TAX_MODE'] . ' : </b>' . $app_strings[$taxtype] . '
		</td>
	   </tr>
	   <tr valign="top">
		<td width=40% class="lvtCol"><font color="red">*</font>
			<b>' . $app_strings['LBL_ITEM_NAME'] . '</b>
		</td>';

	//Add Quantity in Stock column for SO, Quotes and Invoice
	if ($module == 'Quotes' || $module == 'SalesOrder' || $module == 'Invoice')
		$output .= '<td width=10% class="lvtCol"><b>' . $app_strings['LBL_QTY_IN_STOCK'] . '</b></td>';

	$output .= '

		<td width=10% class="lvtCol"><b>' . $app_strings['LBL_QTY'] . '</b></td>
		<td width=10% class="lvtCol" align="right"><b>' . $app_strings['LBL_LIST_PRICE'] . '</b></td>
		<td width=12% nowrap class="lvtCol" align="right"><b>' . $app_strings['LBL_TOTAL'] . '</b></td>
		<td width=13% valign="top" class="lvtCol" align="right"><b>' . $app_strings['LBL_NET_PRICE'] . '</b></td>
	   </tr>
	   	';


	// DG 15 Aug 2006
	// Add "ORDER BY sequence_no" to retain add order on all inventoryproductrel items

	if ($module == 'Quotes' || $module == 'PurchaseOrder' || $module == 'SalesOrder' || $module == 'Invoice') {
		$query = "select case when vtiger_products.productid != '' then vtiger_products.productname else vtiger_service.servicename end as productname," .
				" case when vtiger_products.productid != '' then 'Products' else 'Services' end as entitytype," .
				" case when vtiger_products.productid != '' then vtiger_products.unit_price else vtiger_service.unit_price end as unit_price," .
				" case when vtiger_products.productid != '' then vtiger_products.qtyinstock else 'NA' end as qtyinstock, vtiger_inventoryproductrel.* " .
				" from vtiger_inventoryproductrel" .
				" left join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid " .
				" left join vtiger_service on vtiger_service.serviceid=vtiger_inventoryproductrel.productid " .
				" where id=? ORDER BY sequence_no";
	}

	$result = $adb->pquery($query, array($focus->id));
	$num_rows = $adb->num_rows($result);
	$netTotal = '0.00';
	for ($i = 1; $i <= $num_rows; $i++) {
		$sub_prod_query = $adb->pquery("SELECT productid from vtiger_inventorysubproductrel WHERE id=? AND sequence_no=?", array($focus->id, $i));
		$subprodname_str = '';
		if ($adb->num_rows($sub_prod_query) > 0) {
			for ($j = 0; $j < $adb->num_rows($sub_prod_query); $j++) {
				$sprod_id = $adb->query_result($sub_prod_query, $j, 'productid');
				$sprod_name = getProductName($sprod_id);
				$str_sep = "";
				if ($j > 0)
					$str_sep = ":";
				$subprodname_str .= $str_sep . " - " . $sprod_name;
			}
		}
		$subprodname_str = str_replace(":", "<br>", $subprodname_str);

		$productid = $adb->query_result($result, $i - 1, 'productid');
		$entitytype = $adb->query_result($result, $i - 1, 'entitytype');
		$productname = $adb->query_result($result, $i - 1, 'productname');
		if ($subprodname_str != '')
			$productname .= "<br/><span style='color:#C0C0C0;font-style:italic;'>" . $subprodname_str . "</span>";
		$comment = $adb->query_result($result, $i - 1, 'comment');
		$qtyinstock = $adb->query_result($result, $i - 1, 'qtyinstock');
		$qty = $adb->query_result($result, $i - 1, 'quantity');
		$qty = number_format($qty, 2,'.',''); //Convert to 2 decimals
		$unitprice = $adb->query_result($result, $i - 1, 'unit_price');
		$listprice = $adb->query_result($result, $i - 1, 'listprice');
		$total = $qty * $listprice;
		$listprice = number_format($listprice, 2,'.',''); //Convert to 2 decimals

		//Product wise Discount calculation - starts
		$discount_percent = $adb->query_result($result, $i - 1, 'discount_percent');
		$discount_amount = $adb->query_result($result, $i - 1, 'discount_amount');
		$totalAfterDiscount = $total;

		$productDiscount = '0.00';
		if ($discount_percent != 'NULL' && $discount_percent != '') {
			$productDiscount = $total * $discount_percent / 100;
			$productDiscount = number_format($productDiscount, 2,'.','');
			$totalAfterDiscount = $total - $productDiscount;
			//if discount is percent then show the percentage
			$discount_info_message = "$discount_percent % of ".
										CurrencyField::convertToUserFormat($total, null, true)." = ".
										CurrencyField::convertToUserFormat($productDiscount, null, true);
		} elseif ($discount_amount != 'NULL' && $discount_amount != '') {
			$productDiscount = $discount_amount;
			$productDiscount = number_format($productDiscount, 2,'.','');
			$totalAfterDiscount = $total - $productDiscount;
			$discount_info_message = $app_strings['LBL_DIRECT_AMOUNT_DISCOUNT'] . " = ". CurrencyField::convertToUserFormat($productDiscount, null, true);
		} else {
			$discount_info_message = $app_strings['LBL_NO_DISCOUNT_FOR_THIS_LINE_ITEM'];
		}
		//Product wise Discount calculation - ends

		$totalAfterDiscount = number_format($totalAfterDiscount, 2,'.',''); //Convert to 2 decimals
		$netprice = $totalAfterDiscount;
		//Calculate the individual tax if taxtype is individual
		if ($taxtype == 'individual') {
			$taxtotal = '0.00';
			$tax_info_message = $app_strings['LBL_TOTAL_AFTER_DISCOUNT'] . " = ".CurrencyField::convertToUserFormat($totalAfterDiscount, null, true)." \\n";
			$tax_details = getTaxDetailsForProduct($productid, 'all');
			for ($tax_count = 0; $tax_count < count($tax_details); $tax_count++) {
				$tax_name = $tax_details[$tax_count]['taxname'];
				$tax_label = $tax_details[$tax_count]['taxlabel'];
				$tax_value = getInventoryProductTaxValue($focus->id, $productid, $tax_name);

				$individual_taxamount = $totalAfterDiscount * $tax_value / 100;
				$individual_taxamount = number_format($individual_taxamount, 2,'.',''); //Convert to 2 decimals
				$taxtotal = $taxtotal + $individual_taxamount;
				$taxtotal = number_format($taxtotal, 2,'.',''); //Convert to 2 decimals
				$tax_info_message .= "$tax_label : $tax_value % = ".
										CurrencyField::convertToUserFormat($individual_taxamount, null, true).
										" \\n";
			}
			$tax_info_message .= "\\n " . $app_strings['LBL_TOTAL_TAX_AMOUNT'] . " = ". CurrencyField::convertToUserFormat($taxtotal, null, true);
			$netprice = $netprice + $taxtotal;
			$netprice = number_format($netprice, 2,'.',''); //Convert to 2 decimals
		}

		$sc_image_tag = '';
		if ($entitytype == 'Services') {
			$sc_image_tag = '<a href="index.php?module=ServiceContracts&action=EditView&service_id=' . $productid . '&return_module=' . $module . '&return_id=' . $focus->id . '">' .
					'<img border="0" src="' . vtiger_imageurl('handshake.gif', $theme) . '" title="' . getTranslatedString('LBL_ADD_NEW',$module)." ".getTranslatedString('ServiceContracts','ServiceContracts'). '" style="cursor: pointer;" align="absmiddle" />' .
					'</a>';
		}

		//For Product Name
		$output .= '
			   <tr valign="top">
				<td class="crmTableRow small lineOnTop">
					' . $productname . '&nbsp;' . $sc_image_tag . '
					<br>' . $comment . '
				</td>';
		//Upto this added to display the Product name and comment


		if ($module != 'PurchaseOrder') {
			$output .= '<td class="crmTableRow small lineOnTop">' . $qtyinstock . '</td>';
		}
		$output .= '<td class="crmTableRow small lineOnTop">' . $qty . '</td>';
		$output .= '
			<td class="crmTableRow small lineOnTop" align="right">
				<table width="100%" border="0" cellpadding="5" cellspacing="0">
				   <tr>
				   	<td align="right">' . CurrencyField::convertToUserFormat($listprice, null, true) . '</td>
				   </tr>
				   <tr>
					   <td align="right">(-)&nbsp;<b><a href="javascript:;" onclick="alert(\'' . $discount_info_message . '\'); ">' . $app_strings['LBL_DISCOUNT'] . ' : </a></b></td>
				   </tr>
				   <tr>
				   	<td align="right" nowrap>' . $app_strings['LBL_TOTAL_AFTER_DISCOUNT'] . ' : </td>
				   </tr>';
		if ($taxtype == 'individual') {
			$output .= '
				   <tr>
					   <td align="right" nowrap>(+)&nbsp;<b><a href="javascript:;" onclick="alert(\'' . $tax_info_message . '\');">' . $app_strings['LBL_TAX'] . ' : </a></b></td>
				   </tr>';
		}
		$output .= '
				</table>
			</td>';

		$output .= '
			<td class="crmTableRow small lineOnTop" align="right">
				<table width="100%" border="0" cellpadding="5" cellspacing="0">
				   <tr><td align="right">' . CurrencyField::convertToUserFormat($total, null, true) . '</td></tr>
				   <tr><td align="right">' . CurrencyField::convertToUserFormat($productDiscount, null, true) . '</td></tr>
				   <tr><td align="right" nowrap>' . CurrencyField::convertToUserFormat($totalAfterDiscount, null, true) . '</td></tr>';

		if ($taxtype == 'individual') {
			$output .= '<tr><td align="right" nowrap>' . CurrencyField::convertToUserFormat($taxtotal, null, true) . '</td></tr>';
		}

		$output .= '
				</table>
			</td>';
		$output .= '<td class="crmTableRow small lineOnTop" valign="bottom" align="right">' . CurrencyField::convertToUserFormat($netprice, null, true) . '</td>';
		$output .= '</tr>';

		$netTotal = $netTotal + $netprice;
	}

	$output .= '</table>';

	//$netTotal should be equal to $focus->column_fields['hdnSubTotal']
	$netTotal = $focus->column_fields['hdnSubTotal'];
	$netTotal = number_format($netTotal, 2,'.',''); //Convert to 2 decimals

	//Display the total, adjustment, S&H details
	$output .= '<table width="100%" border="0" cellspacing="0" cellpadding="5" class="crmTable">';
	$output .= '<tr>';
	$output .= '<td width="88%" class="crmTableRow small" align="right"><b>' . $app_strings['LBL_NET_TOTAL'] . '</td>';
	$output .= '<td width="12%" class="crmTableRow small" align="right"><b>' . CurrencyField::convertToUserFormat($netTotal, null, true) . '</b></td>';
	$output .= '</tr>';

	//Decide discount
	$finalDiscount = '0.00';
	$final_discount_info = '0';
	//if($focus->column_fields['hdnDiscountPercent'] != '') - previously (before changing to prepared statement) the selected option (either percent or amount) will have value and the other remains empty. So we can find the non selected item by empty check. But now with prepared statement, the non selected option stored as 0
	if ($focus->column_fields['hdnDiscountPercent'] != '0') {
		$finalDiscount = ($netTotal * $focus->column_fields['hdnDiscountPercent'] / 100);
		$finalDiscount = number_format($finalDiscount, 2,'.','');
		$final_discount_info = $focus->column_fields['hdnDiscountPercent'] . " % of ".CurrencyField::convertToUserFormat($netTotal, null, true).
											" = ". CurrencyField::convertToUserFormat($finalDiscount, null, true);
	} elseif ($focus->column_fields['hdnDiscountAmount'] != '0') {
		$finalDiscount = $focus->column_fields['hdnDiscountAmount'];
		$finalDiscount = number_format($finalDiscount, 2,'.','');
		$final_discount_info = CurrencyField::convertToUserFormat($finalDiscount, null, true);
	}

	//Alert the Final Discount amount even it is zero
	$final_discount_info = $app_strings['LBL_FINAL_DISCOUNT_AMOUNT'] . " = $final_discount_info";
	$final_discount_info = 'onclick="alert(\'' . $final_discount_info . '\');"';

	$output .= '<tr>';
	$output .= '<td align="right" class="crmTableRow small lineOnTop">(-)&nbsp;<b><a href="javascript:;" ' . $final_discount_info . '>' . $app_strings['LBL_DISCOUNT'] . '</a></b></td>';
	$output .= '<td align="right" class="crmTableRow small lineOnTop">' . CurrencyField::convertToUserFormat($finalDiscount, null, true) . '</td>';
	$output .= '</tr>';

	if ($taxtype == 'group') {
		$taxtotal = '0.00';
		$final_totalAfterDiscount = $netTotal - $finalDiscount;
		$tax_info_message = $app_strings['LBL_TOTAL_AFTER_DISCOUNT'] . " = ". CurrencyField::convertToUserFormat($final_totalAfterDiscount, null, true)." \\n";
		//First we should get all available taxes and then retrieve the corresponding tax values
		$tax_details = getAllTaxes('available', '', 'edit', $focus->id);
		//if taxtype is group then the tax should be same for all products in vtiger_inventoryproductrel table
		for ($tax_count = 0; $tax_count < count($tax_details); $tax_count++) {
			$tax_name = $tax_details[$tax_count]['taxname'];
			$tax_label = $tax_details[$tax_count]['taxlabel'];
			$tax_value = $adb->query_result($result, 0, $tax_name);
			if ($tax_value == '' || $tax_value == 'NULL')
				$tax_value = '0.00';

			$taxamount = ($netTotal - $finalDiscount) * $tax_value / 100;
			$taxtotal = $taxtotal + $taxamount;
			$tax_info_message .= "$tax_label : $tax_value % = ".
									CurrencyField::convertToUserFormat($taxtotal, null, true) ." \\n";
		}
		$tax_info_message .= "\\n " . $app_strings['LBL_TOTAL_TAX_AMOUNT'] . " = ". CurrencyField::convertToUserFormat($taxtotal, null, true);

		$output .= '<tr>';
		$output .= '<td align="right" class="crmTableRow small">(+)&nbsp;<b><a href="javascript:;" onclick="alert(\'' . $tax_info_message . '\');">' . $app_strings['LBL_TAX'] . '</a></b></td>';
		$output .= '<td align="right" class="crmTableRow small">' . CurrencyField::convertToUserFormat($taxtotal, null, true) . '</td>';
		$output .= '</tr>';
	}

	$shAmount = ($focus->column_fields['hdnS_H_Amount'] != '') ? $focus->column_fields['hdnS_H_Amount'] : '0.00';
	$shAmount = number_format($shAmount, 2,'.',''); //Convert to 2 decimals
	$output .= '<tr>';
	$output .= '<td align="right" class="crmTableRow small">(+)&nbsp;<b>' . $app_strings['LBL_SHIPPING_AND_HANDLING_CHARGES'] . '</b></td>';
	$output .= '<td align="right" class="crmTableRow small">' . CurrencyField::convertToUserFormat($shAmount, null, true) . '</td>';
	$output .= '</tr>';

	//calculate S&H tax
	$shtaxtotal = '0.00';
	//First we should get all available taxes and then retrieve the corresponding tax values
	$shtax_details = getAllTaxes('available', 'sh', 'edit', $focus->id);
	//if taxtype is group then the tax should be same for all products in vtiger_inventoryproductrel table
	$shtax_info_message = $app_strings['LBL_SHIPPING_AND_HANDLING_CHARGE'] . " = ". CurrencyField::convertToUserFormat($shAmount, null, true) ."\\n";
	for ($shtax_count = 0; $shtax_count < count($shtax_details); $shtax_count++) {
		$shtax_name = $shtax_details[$shtax_count]['taxname'];
		$shtax_label = $shtax_details[$shtax_count]['taxlabel'];
		$shtax_percent = getInventorySHTaxPercent($focus->id, $shtax_name);
		$shtaxamount = $shAmount * $shtax_percent / 100;
		$shtaxamount = number_format($shtaxamount, 2,'.','');
		$shtaxtotal = $shtaxtotal + $shtaxamount;
		$shtax_info_message .= "$shtax_label : $shtax_percent % = ". CurrencyField::convertToUserFormat($shtaxamount, null, true) ." \\n";
	}
	$shtax_info_message .= "\\n " . $app_strings['LBL_TOTAL_TAX_AMOUNT'] . " = ". CurrencyField::convertToUserFormat($shtaxtotal, null, true);

	$output .= '<tr>';
	$output .= '<td align="right" class="crmTableRow small">(+)&nbsp;<b><a href="javascript:;" onclick="alert(\'' . $shtax_info_message . '\')">' . $app_strings['LBL_TAX_FOR_SHIPPING_AND_HANDLING'] . '</a></b></td>';
	$output .= '<td align="right" class="crmTableRow small">' . CurrencyField::convertToUserFormat($shtaxtotal, null, true) . '</td>';
	$output .= '</tr>';

	$adjustment = ($focus->column_fields['txtAdjustment'] != '') ? $focus->column_fields['txtAdjustment'] : '0.00';
	$adjustment = number_format($adjustment, 2,'.',''); //Convert to 2 decimals
	$output .= '<tr>';
	$output .= '<td align="right" class="crmTableRow small">&nbsp;<b>' . $app_strings['LBL_ADJUSTMENT'] . '</b></td>';
	$output .= '<td align="right" class="crmTableRow small">' . CurrencyField::convertToUserFormat($adjustment, null, true) . '</td>';
	$output .= '</tr>';

	$grandTotal = ($focus->column_fields['hdnGrandTotal'] != '') ? $focus->column_fields['hdnGrandTotal'] : '0.00';
	$grandTotal = number_format($grandTotal, 2,'.',''); //Convert to 2 decimals
	$output .= '<tr>';
	$output .= '<td align="right" class="crmTableRow small lineOnTop"><b>' . $app_strings['LBL_GRAND_TOTAL'] . '</b></td>';
	$output .= '<td align="right" class="crmTableRow small lineOnTop">' . CurrencyField::convertToUserFormat($grandTotal, null, true) . '</td>';
	$output .= '</tr>';
	$output .= '</table>';

	$log->debug("Exiting getDetailAssociatedProducts method ...");
	return $output;
}

/** This function returns the related vtiger_tab details for a given entity or a module.
 * Param $module - module name
 * Param $focus - module object
 * Return type is an array
 */
function getRelatedListsInformation($module, $focus) {
	global $log;
	$log->debug("Entering getRelatedLists(" . $module . "," . get_class($focus) . ") method ...");
	global $adb;
	global $current_user;
	require('user_privileges/user_privileges_' . $current_user->id . '.php');

	$cur_tab_id = getTabid($module);

	//$sql1 = "select * from vtiger_relatedlists where tabid=? order by sequence";
	// vtlib customization: Do not picklist module which are set as in-active
	$sql1 = "select * from vtiger_relatedlists where tabid=? and related_tabid not in (SELECT tabid FROM vtiger_tab WHERE presence = 1) order by sequence";
	// END
	$result = $adb->pquery($sql1, array($cur_tab_id));
	$num_row = $adb->num_rows($result);
	for ($i = 0; $i < $num_row; $i++) {
		$rel_tab_id = $adb->query_result($result, $i, "related_tabid");
		$function_name = $adb->query_result($result, $i, "name");
		$label = $adb->query_result($result, $i, "label");
		$actions = $adb->query_result($result, $i, "actions");
		$relationId = $adb->query_result($result, $i, "relation_id");
		if ($rel_tab_id != 0) {
			if ($profileTabsPermission[$rel_tab_id] == 0) {
				if ($profileActionPermission[$rel_tab_id][3] == 0) {
					// vtlib customization: Send more information (from module, related module)
					// to the callee
					$focus_list[$label] = $focus->$function_name($focus->id, $cur_tab_id,
									$rel_tab_id, $actions);
					// END
				}
			}
		} else {
			// vtlib customization: Send more information (from module, related module)
			// to the callee
			$focus_list[$label] = $focus->$function_name($focus->id, $cur_tab_id, $rel_tab_id,
							$actions);
			// END
		}
	}
	$log->debug("Exiting getRelatedLists method ...");
	return $focus_list;
}

/** This function returns the related vtiger_tab details for a given entity or a module.
 * Param $module - module name
 * Param $focus - module object
 * Return type is an array
 */
function getRelatedLists($module, $focus) {
	global $log;
	$log->debug("Entering getRelatedLists(" . $module . "," . get_class($focus) . ") method ...");
	global $adb;
	global $current_user;
	require('user_privileges/user_privileges_' . $current_user->id . '.php');

	$cur_tab_id = getTabid($module);

	//$sql1 = "select * from vtiger_relatedlists where tabid=? order by sequence";
	// vtlib customization: Do not picklist module which are set as in-active
	$sql1 = "select * from vtiger_relatedlists where tabid=? and related_tabid not in (SELECT tabid FROM vtiger_tab WHERE presence = 1) order by sequence";
	// END
	$result = $adb->pquery($sql1, array($cur_tab_id));
	$num_row = $adb->num_rows($result);
	for ($i = 0; $i < $num_row; $i++) {
		$rel_tab_id = $adb->query_result($result, $i, "related_tabid");
		$function_name = $adb->query_result($result, $i, "name");
		$label = $adb->query_result($result, $i, "label");
		$actions = $adb->query_result($result, $i, "actions");
		$relationId = $adb->query_result($result, $i, "relation_id");
		if ($rel_tab_id != 0) {
			if ($profileTabsPermission[$rel_tab_id] == 0) {
				if ($profileActionPermission[$rel_tab_id][3] == 0) {
					// vtlib customization: Send more information (from module, related module)
					// to the callee
					$focus_list[$label] = array('related_tabid' => $rel_tab_id, 'relationId' =>
						$relationId, 'actions' => $actions);
					// END
				}
			}
		} else {
			// vtlib customization: Send more information (from module, related module)
			// to the callee
			$focus_list[$label] = array('related_tabid' => $rel_tab_id, 'relationId' =>
				$relationId, 'actions' => $actions);
			// END
		}
	}
	$log->debug("Exiting getRelatedLists method ...");
	return $focus_list;
}

/** This function returns whether related lists is present for this particular module or not
 * Param $module - module name
 * Param $activity_mode - mode of activity
 * Return type true or false
 */
function isPresentRelatedLists($module, $activity_mode='') {
	static $moduleRelatedListCache = array();

	global $adb, $current_user;
	$retval = array();
	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) {
		include('tabdata.php');
	}
	require('user_privileges/user_privileges_' . $current_user->id . '.php');
	$tab_id = getTabid($module);
	// We need to check if there is atleast 1 relation, no need to use count(*)
	$query = "select relation_id,related_tabid, label from vtiger_relatedlists where tabid=? " .
			"order by sequence";
	$result = $adb->pquery($query, array($tab_id));
	$count = $adb->num_rows($result);
	if ($count < 1 || ($module == 'Calendar' && $activity_mode == 'task')) {
		$retval = 'false';
	} else if (empty($moduleRelatedListCache[$module])) {
		for ($i = 0; $i < $count; ++$i) {
			$relatedId = $adb->query_result($result, $i, 'relation_id');
			$relationLabel = $adb->query_result($result, $i, 'label');
			$relatedTabId = $adb->query_result($result, $i, 'related_tabid');
			//check for module disable.
			$permitted = $tab_seq_array[$relatedTabId];
			if ($permitted === 0 || empty($relatedTabId)) {
				if ($is_admin || $profileTabsPermission[$relatedTabId] === 0 || empty($relatedTabId)) {
					$retval[$relatedId] = $relationLabel;
				}
			}
		}
		$moduleRelatedListCache[$module] = $retval;
	}
	return $moduleRelatedListCache[$module];
}

/** This function returns the detailed block information of a record in a module.
 * Param $module - module name
 * Param $block - block id
 * Param $col_fields - column vtiger_fields array for the module
 * Param $tabid - vtiger_tab id
 * Return type is an array
 */
function getDetailBlockInformation($module, $result, $col_fields, $tabid, $block_label) {
	global $log;
	$log->debug("Entering getDetailBlockInformation(" . $module . "," . $result . "," . $col_fields . "," . $tabid . "," . $block_label . ") method ...");
	global $adb;
	global $current_user;
	global $mod_strings;
	$label_data = Array();

	$noofrows = $adb->num_rows($result);
	for ($i = 0; $i < $noofrows; $i++) {

		$fieldtablename = $adb->query_result($result, $i, "tablename");
		$fieldcolname = $adb->query_result($result, $i, "columnname");
		$uitype = $adb->query_result($result, $i, "uitype");
		$fieldname = $adb->query_result($result, $i, "fieldname");
		$fieldid = $adb->query_result($result, $i, "fieldid");
		$fieldlabel = $adb->query_result($result, $i, "fieldlabel");
		$maxlength = $adb->query_result($result, $i, "maximumlength");
		$block = $adb->query_result($result, $i, "block");
		$generatedtype = $adb->query_result($result, $i, "generatedtype");
		$tabid = $adb->query_result($result, $i, "tabid");
		$displaytype = $adb->query_result($result, $i, 'displaytype');
		$readonly = $adb->query_result($result, $i, 'readonly');
		$custfld = getDetailViewOutputHtml($uitype, $fieldname, $fieldlabel, $col_fields, $generatedtype, $tabid, $module);
		if (is_array($custfld)) {
			$label_data[$block][] = array($custfld[0] => array("value" => $custfld[1], "ui" => $custfld[2], "options" => $custfld["options"],
					"secid" => $custfld["secid"], "link" => $custfld["link"], "cursymb" => $custfld["cursymb"],
					"salut" => $custfld["salut"], "notaccess" => $custfld["notaccess"],
					"cntimage" => $custfld["cntimage"], "isadmin" => $custfld["isadmin"],
					"tablename" => $fieldtablename, "fldname" => $fieldname, "fldid" => $fieldid,
					"displaytype" => $displaytype, "readonly" => $readonly));
		}
	}
	foreach ($label_data as $headerid => $value_array) {
		$detailview_data = Array();
		for ($i = 0, $j = 0; $i < count($value_array); $j++) {
			$key2 = null;
			$keys = array_keys($value_array[$i]);
			$key1 = $keys[0];
			if (is_array($value_array[$i + 1]) && ($value_array[$i][$key1][ui] != 19 && $value_array[$i][$key1][ui] != 20)) {
				$keys = array_keys($value_array[$i + 1]);
				$key2 = $keys[0];
			}
			// Added to avoid the unique keys
			$use_key1 = $key1;
			if ($key1 == $key2) {
				$use_key1 = " " . $key1;
			}

			if ($value_array[$i][$key1][ui] != 19 && $value_array[$i][$key1][ui] != 20) {
				$detailview_data[$j] = array($use_key1 => $value_array[$i][$key1], $key2 => $value_array[$i + 1][$key2]);
				$i+=2;
			} else {
				$detailview_data[$j] = array($use_key1 => $value_array[$i][$key1]);
				$i++;
			}
		}
		$label_data[$headerid] = $detailview_data;
	}
	foreach ($block_label as $blockid => $label) {
		if ($label == '') {
			$returndata[getTranslatedString($curBlock, $module)] = array_merge((array) $returndata[getTranslatedString($curBlock, $module)], (array) $label_data[$blockid]);
		} else {
			$curBlock = $label;
			if (is_array($label_data[$blockid]))
				$returndata[getTranslatedString($curBlock, $module)] = array_merge((array) $returndata[getTranslatedString($curBlock, $module)], (array) $label_data[$blockid]);
		}
	}
	$log->debug("Exiting getDetailBlockInformation method ...");
	return $returndata;
}

function VT_detailViewNavigation($smarty, $recordNavigationInfo, $currrentRecordId) {
	$pageNumber = 0;
	foreach ($recordNavigationInfo as $start => $recordIdList) {
		$pageNumber++;
		foreach ($recordIdList as $index => $recordId) {
			if ($recordId === $currrentRecordId) {
				if ($index == 0) {
					$smarty->assign('privrecordstart', $start - 1);
					$smarty->assign('privrecord', $recordNavigationInfo[$start - 1][count($recordNavigationInfo[$start - 1]) - 1]);
				} else {
					$smarty->assign('privrecordstart', $start);
					$smarty->assign('privrecord', $recordIdList[$index - 1]);
				}
				if ($index == count($recordIdList) - 1) {
					$smarty->assign('nextrecordstart', $start + 1);
					$smarty->assign('nextrecord', $recordNavigationInfo[$start + 1][0]);
				} else {
					$smarty->assign('nextrecordstart', $start);
					$smarty->assign('nextrecord', $recordIdList[$index + 1]);
				}
			}
		}
	}
}

function getRelatedListInfoById($relationId) {
	static $relatedInfoCache = array();
	if (isset($relatedInfoCache[$relationId])) {
		return $relatedInfoCache[$relationId];
	}
	$adb = PearDatabase::getInstance();
	$sql1 = "select * from vtiger_relatedlists where relation_id=?";
	$result = $adb->pquery($sql1, array($relationId));
	$rowCount = $adb->num_rows($result);
	$relationInfo = array();
	if ($rowCount > 0) {
		$relationInfo['relatedTabId'] = $adb->query_result($result, 0, "related_tabid");
		$relationInfo['functionName'] = $adb->query_result($result, 0, "name");
		$relationInfo['label'] = $adb->query_result($result, 0, "label");
		$relationInfo['actions'] = $adb->query_result($result, 0, "actions");
		$relationInfo['relationId'] = $adb->query_result($result, 0, "relation_id");
	}
	$relatedInfoCache[$relationId] = $relationInfo;
	return $relatedInfoCache[$relationId];
}

?>