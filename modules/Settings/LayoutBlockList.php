<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('include/CustomFieldUtil.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
require_once 'modules/PickList/PickListUtils.php';

global $mod_strings,$app_strings,$log,$theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('modules/Vtiger/layout_utils.php');
$smarty=new vtigerCRM_Smarty;

$subMode = $_REQUEST['sub_mode'];
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("JS_DATEFORMAT",parse_calendardate($app_strings['NTC_DATE_FORMAT']));

if ($subMode == 'updateFieldProperties')
	updateFieldProperties();
elseif($subMode == 'deleteCustomField')
	deleteCustomField();
elseif($subMode == 'changeOrder')
	changeFieldOrder();
elseif($subMode == 'addBlock')
	$duplicate = addblock();
elseif($subMode == 'deleteCustomBlock')
	deleteBlock();
elseif($subMode == 'addCustomField')
	$duplicate = addCustomField();
elseif($subMode == 'movehiddenfields' || $subMode == 'showhiddenfields')
	show_move_hiddenfields($subMode);
elseif($subMode == 'changeRelatedInfoOrder')
	changeRelatedListOrder();

$module_array=getCustomFieldSupportedModules();

$cfimagecombo = Array(
	$image_path."text.gif",
	$image_path."number.gif",
	$image_path."percent.gif",
	$image_path."currency.gif",
	$image_path."date.gif",
	$image_path."email.gif",
	$image_path."phone.gif",
	$image_path."picklist.gif",
	$image_path."url.gif",
	$image_path."checkbox.gif",
	$image_path."text.gif",
	$image_path."picklist.gif",
	$image_path."time.PNG"
	);

$cftextcombo = Array(
	$mod_strings['Text'],
	$mod_strings['Number'],
	$mod_strings['Percent'],
	$mod_strings['Currency'],
	$mod_strings['Date'],
	$mod_strings['Email'],
	$mod_strings['Phone'],
	$mod_strings['PickList'],
	$mod_strings['LBL_URL'],
	$mod_strings['LBL_CHECK_BOX'],
	$mod_strings['LBL_TEXT_AREA'],
	$mod_strings['LBL_MULTISELECT_COMBO'],
	$mod_strings['Time']
	);


$smarty->assign("MODULES",$module_array);
$smarty->assign("CFTEXTCOMBO",$cftextcombo);
$smarty->assign("CFIMAGECOMBO",$cfimagecombo);

if($_REQUEST['formodule'] !='')
	$fld_module = vtlib_purify($_REQUEST['formodule']);
elseif($_REQUEST['fld_module'] != '') {
	$fld_module = vtlib_purify($_REQUEST['fld_module']);
}else
	$fld_module = 'Accounts';

$block_array = getModuleBlocks($fld_module);

$smarty->assign("BLOCKS",$block_array);
$smarty->assign("MODULE",$fld_module);
$smarty->assign("CFENTRIES",getFieldListEntries($fld_module));
$smarty->assign("RELATEDLIST",getRelatedListInfo($fld_module));

if(isset($_REQUEST["duplicate"]) && $_REQUEST["duplicate"] == "yes" || $duplicate == 'yes') {
	echo "ERROR";
	exit;
}
if($duplicate == 'LENGTH_ERROR') {
	echo "LENGTH_ERROR";
	exit;
}
if($_REQUEST['mode'] !='')
	$mode = vtlib_purify($_REQUEST['mode']);

$smarty->assign("MODE", $mode);

if($_REQUEST['ajax'] != 'true') {
	$smarty->display('Settings/LayoutBlockList.tpl');
}
elseif(($subMode == 'getRelatedInfoOrder' || $subMode == 'changeRelatedInfoOrder') &&  $_REQUEST['ajax'] == 'true') {
	$smarty->display('Settings/OrderRelatedList.tpl');
}
else {
	$smarty->display('Settings/LayoutBlockEntries.tpl');
}


function InStrCount($String,$Find,$CaseSensitive = false) {
	global $log;
	$i=0;
	$x=0;
	$substring = '';
	while (strlen($String)>=$i) {
		unset($substring);
		if ($CaseSensitive) {
			$Find=strtolower($Find);
			$String=strtolower($String);
		}
		$substring=substr($String,$i,strlen($Find));
		if ($substring==$Find) $x++;
		$i++;
	}
	$log->debug("In InStrCount function".$String,$Find);
	return $x;
}


/**
 * Function to get customfield entries
 * @param string $module - Module name
 * return array  $cflist - customfield entries
 */
function getFieldListEntries($module) {
	$tabid = getTabid($module);
	global $adb, $smarty,$log,$current_user;
	global $theme;
	$theme_path="themes/".$theme."/";
	$image_path="themes/images/";

	$dbQuery = "select vtiger_blocks.*,vtiger_tab.presence as tabpresence  from vtiger_blocks" .
			" inner join vtiger_tab on vtiger_tab.tabid = vtiger_blocks.tabid" .
			" where vtiger_blocks.tabid=?  and vtiger_tab.presence = 0 order by sequence";
	$result = $adb->pquery($dbQuery, array($tabid));
	$row = $adb->fetch_array($result);

	$focus = CRMEntity::getInstance($module);

	$nonEditableUiTypes = array('4','70');

	// To get reference field names
	require_once('include/Webservices/Utils.php');
	$handler = vtws_getModuleHandlerFromName($module, $current_user);

	$meta = $handler->getMeta();
	$referenceFieldNames = array_keys($meta->getReferenceFieldDetails());

	$cflist=Array();
	$i=0;
	if($row!='') {
		do {
			if($row["blocklabel"] == 'LBL_CUSTOM_INFORMATION' ) {
				$smarty->assign("CUSTOMSECTIONID",$row["blockid"]);
			}
			if($row["blocklabel"] == 'LBL_RELATED_PRODUCTS' ) {
				$smarty->assign("RELPRODUCTSECTIONID",$row["blockid"]);
			}
			if($row["blocklabel"] == 'LBL_COMMENTS' || $row['blocklabel'] == 'LBL_COMMENT_INFORMATION' ) {
				$smarty->assign("COMMENTSECTIONID",$row["blockid"]);
			}
			if($row['blocklabel'] == 'LBL_TICKET_RESOLUTION') {
				$smarty->assign("SOLUTIONBLOCKID",$row["blockid"]);
			}
			if($row['blocklabel'] == '') {
				continue;
			}
			$cflist[$i]['tabpresence']= $row['tabpresence'];
			$cflist[$i]['module'] = $module;
			$cflist[$i]['blocklabel']=getTranslatedString($row["blocklabel"], $module);
			$cflist[$i]['blockid']=$row["blockid"];
			$cflist[$i]['display_status']=$row["display_status"];
			$cflist[$i]['tabid']=$tabid;
			$cflist[$i]['blockselect']=$row["blockid"];
			$cflist[$i]['sequence'] = $row["sequence"];
			$cflist[$i]['iscustom'] = $row["iscustom"];

			if($module!='Invoices' && $module!='Quotes' && $module!='SalesOrder' && $module!='Invoice') {
				$sql_field="select * from  vtiger_field where block=? and vtiger_field.displaytype IN (1,2,4) order by sequence";
				$sql_field_params = array($row["blockid"]);
			}else {
				$sql_field="select * from  vtiger_field where block=? and (vtiger_field.fieldlabel!='Total' and vtiger_field.fieldlabel!='Sub Total' and vtiger_field.fieldlabel!='Tax') and vtiger_field.displaytype IN (1,2,4) order by sequence";
				$sql_field_params = array($row["blockid"]);
			}

			$result_field = $adb->pquery($sql_field,$sql_field_params);
			$row_field= $adb->fetch_array($result_field);
			if($row_field!='') {
				$cf_element=Array();
				$cf_hidden_element=Array();
				$count=0;
				$hiddencount=0;
				do {
					$fieldid = $row_field['fieldid'];
					$presence = $row_field['presence'];
					$fieldname = $row_field['fieldname'];
					$customfieldflag=InStrCount($row_field['fieldname'],'cf_',true);
					$quickcreate = $row_field['quickcreate'];
					$massedit = $row_field['masseditable'];
					$typeofdata = $row_field['typeofdata'];
					$displaytype = $row_field['displaytype'];
					$uitype = $row_field['uitype'];
					$fld_type_name = getCustomFieldTypeName($row_field['uitype']);
					$defaultValue = $row_field['defaultvalue'];
					if(!empty($defaultValue) && ($uitype == '5' || $uitype == '6' || $uitype == '23')) {
						$defaultValue = getValidDisplayDate($defaultValue);
					}

					$fieldlabel = getTranslatedString($row_field['fieldlabel'], $module);

					$defaultPermitted = true;
					$strictlyMandatory = false;
					if(isset($focus->mandatory_fields) && (!empty($focus->mandatory_fields)) && in_array($fieldname, $focus->mandatory_fields)) {
						$strictlyMandatory = true;
						$defaultPermitted = false;
					} elseif (in_array($uitype, $nonEditableUiTypes) || $displaytype == 2) {
						$strictlyMandatory = true;
						$defaultPermitted = false;
					}
					if(in_array($fieldname, $referenceFieldNames)) {
						$defaultPermitted = false;
					}
					$visibility = getFieldInfo($fieldname,$typeofdata,$quickcreate,$massedit,$presence,$strictlyMandatory,$customfieldflag,$displaytype,$uitype);

					$allValues = array();
					if(in_array($uitype, array('15','16','33'))) {
						$allValues = getAllPickListValues($fieldname);
					}

					if ($presence == 0 || $presence == 2) {
						$cf_element[$count]['fieldselect']=$fieldid;
						$cf_element[$count]['blockid']=$row['blockid'];
						$cf_element[$count]['tabid']=$tabid;
						$cf_element[$count]['no']=$count;
						$cf_element[$count]['label']=$fieldlabel;
						$cf_element[$count]['fieldlabel'] = $row_field['fieldlabel'];
						$cf_element[$count]['type']=$fld_type_name;
						$cf_element[$count]['typeofdata']=$typeofdata;
						$cf_element[$count]['uitype']=$uitype;
						$cf_element[$count]['columnname']=$row_field['columnname'];
						$cf_element[$count]['defaultvalue']= array('permitted' => $defaultPermitted, 'value' => $defaultValue, '_allvalues' => $allValues);
						$cf_element[$count] = array_merge($cf_element[$count], $visibility);

						$count++;
					} else {
						$cf_hidden_element[$hiddencount]['fieldselect']=$fieldid;
						$cf_hidden_element[$hiddencount]['blockid']=$row['blockid'];
						$cf_hidden_element[$hiddencount]['tabid']=$tabid;
						$cf_hidden_element[$hiddencount]['no']=$hiddencount;
						$cf_hidden_element[$hiddencount]['label']=$fieldlabel;
						$cf_hidden_element[$hiddencount]['fieldlabel'] = $row_field['fieldlabel'];
						$cf_hidden_element[$hiddencount]['type']=$fld_type_name;
						$cf_hidden_element[$hiddencount]['typeofdata']=$typeofdata;
						$cf_hidden_element[$hiddencount]['uitype']=$uitype;
						$cf_hidden_element[$hiddencount]['columnname']=$row_field['columnname'];
						$cf_hidden_element[$hiddencount]['defaultvalue']= array('permitted' => $defaultPermitted, 'value' => $defaultValue, '_allvalues' => $allValues);
						$cf_hidden_element[$hiddencount] = array_merge($cf_hidden_element[$hiddencount], $visibility);

						$hiddencount++;
					}
				} while($row_field = $adb->fetch_array($result_field));

				$cflist[$i]['no']=$count;
				$cflist[$i]['hidden_count'] = $hiddencount;
			}
			else {
				$cflist[$i]['no']= 0;
			}

			$query_fields_not_in_block ='select fieldid,fieldlabel,block from vtiger_field ' .
					'inner join vtiger_blocks on vtiger_field.block=vtiger_blocks.blockid ' .
					'where vtiger_field.block != ? and vtiger_blocks.blocklabel not in ("LBL_TICKET_RESOLUTION","LBL_COMMENTS","LBL_COMMENT_INFORMATION") ' .
					'AND vtiger_field.tabid = ? and vtiger_field.displaytype IN (1,2,4) order by vtiger_field.sequence';

			$params =array($row['blockid'],$tabid);
			$fields = $adb->pquery($query_fields_not_in_block,$params);
			$row_field= $adb->fetch_array($fields);

			if($row_field != '') {
				$movefields = array();
				$movefieldcount = 0;
				do {
					$movefields[$movefieldcount]['fieldid'] =  $row_field['fieldid'];
					$movefields[$movefieldcount]['fieldlabel'] =  getTranslatedString($row_field['fieldlabel'], $module);
					$movefieldcount++;
				}while($row_field = $adb->fetch_array($fields));
				$cflist[$i]['movefieldcount'] = $movefieldcount;
			}
			else {
				$cflist[$i]['movefieldcount'] = 0 ;
			}

			$cflist[$i]['field']= $cf_element;
			$cflist[$i]['hiddenfield']= $cf_hidden_element;
			$cflist[$i]['movefield'] = $movefields;

			$cflist[$i]['hascustomtable'] = $focus->customFieldTable;
			unset($cf_element);
			unset($cf_hidden_element);
			unset($movefields);
			$i++;
		} while($row = $adb->fetch_array($result));
	}
	return $cflist;
}

/**
 * Function to Lead customfield Mapping entries
 * @param integer  $cfid   - Lead customfield id
 * return array    $label  - customfield mapping
 */
function getListLeadMapping($cfid) {
	global $adb;
	$sql='select * from vtiger_convertleadmapping where cfmid = ?';
	$result = $adb->pquery($sql, array($cfid));
	$noofrows = $adb->num_rows($result);
	for($i =0;$i <$noofrows;$i++) {
		$leadid = $adb->query_result($result,$i,'leadfid');
		$accountid = $adb->query_result($result,$i,'accountfid');
		$contactid = $adb->query_result($result,$i,'contactfid');
		$potentialid = $adb->query_result($result,$i,'potentialfid');
		$cfmid = $adb->query_result($result,$i,'cfmid');

		$sql2="select fieldlabel from vtiger_field where fieldid = ? and vtiger_field.presence in (0,2)";
		$result2 = $adb->pquery($sql2, array($accountid));
		$accountfield = $adb->query_result($result2,0,'fieldlabel');
		$label['accountlabel'] = $accountfield;

		$sql3="select fieldlabel from vtiger_field where fieldid = ? and vtiger_field.presence in (0,2)";
		$result3 = $adb->pquery($sql3, array($contactid));
		$contactfield = $adb->query_result($result3,0,'fieldlabel');
		$label['contactlabel'] = $contactfield;
		$sql4="select fieldlabel from vtiger_field where fieldid = ? and vtiger_field.presence in (0,2)";
		$result4 = $adb->pquery($sql4, array($potentialid));
		$potentialfield = $adb->query_result($result4,0,'fieldlabel');
		$label['potentiallabel'] = $potentialfield;
	}
	return $label;
}

/* function to get the modules supports Custom Fields
*/
function getCustomFieldSupportedModules() {
	global $adb;
	$sql="select distinct vtiger_field.tabid,name from vtiger_field inner join vtiger_tab on vtiger_field.tabid=vtiger_tab.tabid where vtiger_field.tabid not in(9,10,16,15,8,29)";
	$result = $adb->query($sql);
	while($moduleinfo=$adb->fetch_array($result)) {
		$modulelist[$moduleinfo['name']] = $moduleinfo['name'];
	}
	return $modulelist;
}


function getModuleBlocks($module) {
	global $adb;
	$tabid = getTabid($module);
	$blockquery = "select blocklabel,blockid from vtiger_blocks where tabid = ?";
	$blockres = $adb->pquery($blockquery,array($tabid));
	while($blockinfo = $adb->fetch_array($blockres)) {
		$blocklist[$blockinfo['blockid']] = getTranslatedString($blockinfo['blocklabel'],$module);
	}
	return $blocklist;
}

/**
 *
 */
function changeFieldOrder() {
	global $adb,$log,$smarty;
	if(!empty($_REQUEST['what_to_do'])) {
		if($_REQUEST['what_to_do']=='block_down') {
			$sql="select * from vtiger_blocks where blockid=?";
			$result = $adb->pquery($sql, array($_REQUEST['blockid']));
			$row= $adb->fetch_array($result);
			$current_sequence=$row[sequence];

			$sql_next="select * from vtiger_blocks where sequence > ? and tabid=? limit 0,1";
			$result_next = $adb->pquery($sql_next, array($current_sequence,$_REQUEST[tabid]));
			$row_next= $adb->fetch_array($result_next);
			$next_sequence=$row_next[sequence];
			$next_id=$row_next[blockid];


			$sql_up_current="update vtiger_blocks  set sequence=? where blockid=?";
			$result_up_current = $adb->pquery($sql_up_current, array($next_sequence,$_REQUEST['blockid']));


			$sql_up_next="update vtiger_blocks  set sequence=? where blockid=?";
			$result_up_next = $adb->pquery($sql_up_next, array($current_sequence,$next_id));
		}

		if($_REQUEST['what_to_do']=='block_up') {
			$sql="select * from vtiger_blocks where blockid=?";
			$result = $adb->pquery($sql, array($_REQUEST['blockid']));
			$row= $adb->fetch_array($result);
			$current_sequence=$row[sequence];

			$sql_previous="select * from vtiger_blocks where sequence < ? and tabid=?  order by sequence desc limit 0,1";
			$result_previous = $adb->pquery($sql_previous, array($current_sequence,$_REQUEST[tabid]));
			$row_previous= $adb->fetch_array($result_previous);
			$previous_sequence=$row_previous[sequence];
			$previous_id=$row_previous[blockid];


			$sql_up_current="update vtiger_blocks  set sequence=? where blockid=?";
			$result_up_current = $adb->pquery($sql_up_current, array($previous_sequence,$_REQUEST['blockid']));


			$sql_up_previous="update vtiger_blocks  set sequence=? where blockid=?";
			$result_up_previous = $adb->pquery($sql_up_previous, array($current_sequence,$previous_id));
		}

		if($_REQUEST['what_to_do']=='down' || $_REQUEST['what_to_do']=='Right') {
			$sql="select * from vtiger_field where fieldid=? and vtiger_field.presence in (0,2)";
			$result = $adb->pquery($sql, array($_REQUEST['fieldid']));
			$row= $adb->fetch_array($result);
			$current_sequence=$row['sequence'];
			if($_REQUEST['what_to_do']=='down') {
				$sql_next="select * from vtiger_field where sequence > ? and block = ? and vtiger_field.presence in (0,2) order by sequence limit 1,1";
				$sql_next_params = array($current_sequence, $_REQUEST['blockid']);
			}else {
				$sql_next="select * from vtiger_field where sequence > ? and block = ? and vtiger_field.presence in (0,2) order by sequence limit 0,1";
				$sql_next_params = array($current_sequence, $_REQUEST['blockid']);
			}

			$result_next = $adb->pquery($sql_next,$sql_next_params);
			$row_next= $adb->fetch_array($result_next);
			$next_sequence=$row_next['sequence'];
			$next_id=$row_next['fieldid'];

			$sql_up_current="update vtiger_field  set sequence=? where fieldid=?";
			$result_up_current = $adb->pquery($sql_up_current, array($next_sequence,$_REQUEST['fieldid']));

			$sql_up_next="update vtiger_field  set sequence=? where fieldid=?";
			$result_up_next = $adb->pquery($sql_up_next, array($current_sequence,$next_id));
			$smarty->assign("COLORID",vtlib_purify($_REQUEST['fieldid']));
		}

		if($_REQUEST['what_to_do']=='up' || $_REQUEST['what_to_do']=='Left') {
			$sql="select * from vtiger_field where fieldid=? and vtiger_field.presence in (0,2)";
			$result = $adb->pquery($sql, array($_REQUEST['fieldid']));
			$row= $adb->fetch_array($result);
			$current_sequence=$row['sequence'];

			if($_REQUEST['what_to_do']=='up') {
				$sql_previous="select * from vtiger_field where sequence < ? and block=? and vtiger_field.presence in (0,2) order by sequence desc limit 1,1";
				$sql_prev_params = array($current_sequence,$_REQUEST['blockid']);
			}else {
				$sql_previous="select * from vtiger_field where sequence < ? and block=? and vtiger_field.presence in (0,2) order by sequence desc limit 0,1";
				$sql_prev_params = array($current_sequence,$_REQUEST['blockid']);
			}

			$result_previous = $adb->pquery($sql_previous,$sql_prev_params);
			$row_previous= $adb->fetch_array($result_previous);
			$previous_sequence=$row_previous['sequence'];
			$previous_id=$row_previous['fieldid'];

			$sql_up_current="update vtiger_field  set sequence=? where fieldid=?";
			$result_up_current = $adb->pquery($sql_up_current, array($previous_sequence,$_REQUEST['fieldid']));

			$sql_up_previous="update vtiger_field  set sequence=? where fieldid=?";
			$result_up_previous = $adb->pquery($sql_up_previous, array($current_sequence,$previous_id));
			$smarty->assign("COLORID",vtlib_purify($_REQUEST['fieldid']));
		}

		if($_REQUEST['what_to_do']=='show') {
			$sql_up_display="update vtiger_blocks  set display_status='1' where blockid=?";
			$result_up_display = $adb->pquery($sql_up_display, array($_REQUEST['blockid']));
		}

		if($_REQUEST['what_to_do']=='hide') {
			$sql_up_display="update vtiger_blocks  set display_status='0' where blockid=?";
			$result_up_display = $adb->pquery($sql_up_display, array($_REQUEST['blockid']));
		}
	}
}
/**
 *
 */
function getFieldInfo($fieldname,$typeofdata,$quickcreate,$massedit,$presence,$strictlyMandatory,$customfieldflag,$displaytype,$uitype) {
	global $log;

	$fieldtype =  explode("~",$typeofdata);

	if($strictlyMandatory) {//fields without which the CRM Record will be inconsistent
		$mandatory = '0';
	}elseif($fieldtype[1] == "M") {//fields which are made mandatory
		$mandatory = '2';
	}else {
		$mandatory = '1'; //fields not mandatory
	}
	if ($uitype == 4 || $displaytype == 2) {
		$mandatory = '3';
	}


	$visibility = array();
	$visibility['mandatory']	= $mandatory;
	$visibility['quickcreate']	= $quickcreate;
	$visibility['presence']		= $presence;
	$visibility['massedit']		= $massedit;
	$visibility['displaytype']	= $displaytype;
	$visibility['customfieldflag'] = $customfieldflag;
	$visibility['fieldtype'] = $fieldtype[1];
	return $visibility;
}

function updateFieldProperties() {

	global $adb,$smarty,$log;
	$fieldid = $_REQUEST['fieldid'];
	$req_sql = "select * from vtiger_field where fieldid = ? and fieldname not in('salutationtype') and vtiger_field.presence in (0,2)";
	$req_result = $adb->pquery($req_sql, array($fieldid));

	$typeofdata = $adb->query_result($req_result,0,'typeofdata');
	$tabid = $adb->query_result($req_result,0,'tabid');
	$fieldname = $adb->query_result($req_result,0,'fieldname');
	$uitype = $adb->query_result($req_result,0,'uitype');
	$oldfieldlabel = $adb->query_result($req_result,0,'fieldlabel');
	$tablename = $adb->query_result($req_result,0,'tablename');
	$columnname = $adb->query_result($req_result,0,'columnname');
	$oldquickcreate = $adb->query_result($req_result,0,'quickcreate');
	$oldmassedit = $adb->query_result($req_result,0,'masseditable');
	$oldpresence = $adb->query_result($req_result,0,'presence');

	if(!empty($_REQUEST['fld_module'])) {
		$fld_module = vtlib_purify($_REQUEST['fld_module']);
	}else {
		$fld_module = getTabModuleName($tabid);
	}

	$focus = CRMEntity::getInstance($fld_module);

	$fieldtype =  explode("~",$typeofdata);
	$mandatory_checked= $_REQUEST['ismandatory'];
	$quickcreate_checked = $_REQUEST['quickcreate'];
	$presence_check = $_REQUEST['isPresent'];
	$massedit_check = $_REQUEST['massedit'];
	$defaultvalue = vtlib_purify($_REQUEST['defaultvalue']);

	if(!empty($defaultvalue)) {
		if($uitype == 56) {
			if($defaultvalue == 'on' || $defaultvalue == '1') {
				$defaultvalue = '1';
			} elseif($defaultvalue == 'off' || $defaultvalue == '0') {
				$defaultvalue = '0';
			} else {
				$defaultvalue = '';
			}
		} elseif($uitype == 5 || $uitype == 6 || $uitype == 23) {
			$defaultvalue = getValidDBInsertDateValue($defaultvalue);
		}
	}


	if(isset($focus->mandatory_fields) && (!empty($focus->mandatory_fields)) && in_array($fieldname, $focus->mandatory_fields)) {
		$fieldtype[1] = 'M';
	} elseif($mandatory_checked == 'true' || $mandatory_checked == '') {
		$fieldtype[1] = 'M';
	} else {
		$fieldtype[1] = 'O';
	}
	$datatype = implode('~', $fieldtype);
	$maxseq = '';
	if($oldquickcreate != 3) {
		if(($quickcreate_checked == 'true' || $quickcreate_checked == '' )) {
			$qcdata = 2;
			$quickcreateseq_Query = 'select max(quickcreatesequence) as maxseq from vtiger_field where tabid = ?';
			$res = $adb->pquery($quickcreateseq_Query,array($tabid));
			$maxseq = $adb->query_result($res,0,'maxseq');

		}else {
			$qcdata = 1;
		}
	}
	if($oldpresence != 3) {
		if($presence_check == 'true' || $presence_check == '') {
			$presence = 2;
		}else {
			$presence = 1;
		}
	}else {
		$presence =1;
	}

	if($oldmassedit != 3) {
		if(($massedit_check == 'true' || $massedit_check == '')) {
			$massedit = 1;
		}else {
			$massedit = 2;
		}
	}else {
		$massedit=1;
	}

	if(isset($focus->mandatory_fields) && (!empty($focus->mandatory_fields))) {
		$fieldname_list = implode(',',$focus->mandatory_fields);
	}else {
		$fieldname_list = '';
	}

	$mandatory_query = "update vtiger_field set typeofdata=? where fieldid=? and fieldname not in (?) AND displaytype != 2";
	$mandatory_params = array($datatype,$fieldid,$fieldname_list);
	$adb->pquery($mandatory_query, $mandatory_params);

	if(!empty($qcdata)) {
		$quickcreate_query = "update vtiger_field set quickcreate = ? ,quickcreatesequence = ? where fieldid = ? and quickcreate not in (0,3) AND displaytype != 2";
		$quickcreate_params = array($qcdata,$maxseq+1,$fieldid);
		$adb->pquery($quickcreate_query,$quickcreate_params);
	}

	$presence_query = "update vtiger_field set presence = ? where fieldid = ? and presence not in (0,3) and quickcreate != 0";
	$quickcreate_params = array($presence,$fieldid);
	$adb->pquery($presence_query,$quickcreate_params);

	$massedit_query = "update vtiger_field set masseditable = ? where fieldid = ? and masseditable not in (0,3) AND displaytype != 2";
	$massedit_params = array($massedit,$fieldid);
	$adb->pquery($massedit_query,$massedit_params);

	$defaultvalue_query = "update vtiger_field set defaultvalue=? where fieldid = ? and fieldname not in (?) AND displaytype != 2";
	$defaultvalue_params = array($defaultvalue,$fieldid,$fieldname_list);
	$adb->pquery($defaultvalue_query, $defaultvalue_params);

}



function deleteCustomField() {
	global $adb;

	$fld_module = $_REQUEST["fld_module"];
	$id = $_REQUEST["fld_id"];
	$colName = $_REQUEST["colName"];
	$uitype = $_REQUEST["uitype"];

	$fieldquery = 'select * from vtiger_field where fieldid = ?';
	$res = $adb->pquery($fieldquery,array($id));

	$typeofdata = $adb->query_result($res,0,'typeofdata');
	$fieldname = $adb->query_result($res,0,'fieldname');
	$oldfieldlabel = $adb->query_result($res,0,'fieldlabel');
	$tablename = $adb->query_result($res,0,'tablename');
	$columnname = $adb->query_result($res,0,'columnname');
	$fieldtype =  explode("~",$typeofdata);

	//Deleting the CustomField from the Custom Field Table
	$query='delete from vtiger_field where fieldid = ? and vtiger_field.presence in (0,2)';
	$adb->pquery($query, array($id));

	//Deleting from vtiger_profile2field table
	$query='delete from vtiger_profile2field where fieldid=?';
	$adb->pquery($query, array($id));

	//Deleting from vtiger_def_org_field table
	$query='delete from vtiger_def_org_field where fieldid=?';
	$adb->pquery($query, array($id));

	$focus = CRMEntity::getInstance($fld_module);

	$deletecolumnname =$tablename .":". $columnname .":".$fieldname.":".$fld_module. "_" .str_replace(" ","_",$oldfieldlabel).":".$fieldtype[0];
	$column_cvstdfilter = 	$tablename .":". $columnname .":".$fieldname.":".$fld_module. "_" .str_replace(" ","_",$oldfieldlabel);
	$select_columnname = $tablename.":".$columnname .":".$fld_module. "_" . str_replace(" ","_",$oldfieldlabel).":".$fieldname.":".$fieldtype[0];
	$reportsummary_column = $tablename.":".$columnname.":".str_replace(" ","_",$oldfieldlabel);

	$dbquery = 'alter table '. $adb->sql_escape_string($focus->customFieldTable[0]).' drop column '. $adb->sql_escape_string($colName);
	$adb->pquery($dbquery, array());

	//To remove customfield entry from vtiger_field table
	$dbquery = 'delete from vtiger_field where columnname= ? and fieldid=? and vtiger_field.presence in (0,2)';
	$adb->pquery($dbquery, array($colName, $id));
	//we have to remove the entries in customview and report related tables which have this field ($colName)
	$adb->pquery("delete from vtiger_cvcolumnlist where columnname = ? ", array($deletecolumnname));
	$adb->pquery("delete from vtiger_cvstdfilter where columnname = ?", array($column_cvstdfilter));
	$adb->pquery("delete from vtiger_cvadvfilter where columnname = ?", array($deletecolumnname));
	$adb->pquery("delete from vtiger_selectcolumn where columnname = ?", array($select_columnname));
	$adb->pquery("delete from vtiger_relcriteria where columnname = ?", array($select_columnname));
	$adb->pquery("delete from vtiger_reportsortcol where columnname = ?", array($select_columnname));
	$adb->pquery("delete from vtiger_reportdatefilter where datecolumnname = ?", array($column_cvstdfilter));
	$adb->pquery("delete from vtiger_reportsummary where columnname like ?", array('%'.$reportsummary_column.'%'));


	//Deleting from convert lead mapping vtiger_table- Jaguar
	if($fld_module=="Leads") {
		$deletequery = 'delete from vtiger_convertleadmapping where leadfid=?';
		$adb->pquery($deletequery, array($id));
	}elseif($fld_module=="Accounts" || $fld_module=="Contacts" || $fld_module=="Potentials") {
		$map_del_id = array("Accounts"=>"accountfid","Contacts"=>"contactfid","Potentials"=>"potentialfid");
		$map_del_q = "update vtiger_convertleadmapping set ".$map_del_id[$fld_module]."=0 where ".$map_del_id[$fld_module]."=?";
		$adb->pquery($map_del_q, array($id));
	}

	//HANDLE HERE - we have to remove the table for other picklist type values which are text area and multiselect combo box
	if($uitype == 15) {
		$deltablequery = 'drop table vtiger_'.$adb->sql_escape_string($colName);
		$adb->pquery($deltablequery, array());
	}
}


function addblock() {

	global $mod_strings,$log,$adb;
	$fldmodule=$_REQUEST['fld_module'];
	$mode=$_REQUEST['mode'];

	$newblocklabel = trim($_REQUEST['blocklabel']);
	$after_block = $_REQUEST['after_blockid'];

	$tabid = getTabid($fldmodule);
	$flag = 0;
	$dup_check_query = $adb->pquery("SELECT blocklabel from vtiger_blocks WHERE tabid = ?",array($tabid));
	$norows = $adb->num_rows($dup_check_query);
	for($i=0;$i<$norows;$i++) {
		$blklbl = $adb->query_result($dup_check_query,$i,'blocklabel');
		$blklbltran = getTranslatedString($blklbl,$fldmodule);
		if(strtolower($blklbltran) == strtolower($newblocklabel)) {
			$flag = 1;
			$duplicate='yes';
			return $duplicate;
		}
	}
	$length = strlen($newblocklabel);
	if($length > 50) {
		$flag = 1;
		$duplicate='LENGTH_ERROR';
		return $duplicate;
	}

	if($flag!=1) {
		$sql_seq="select sequence from vtiger_blocks where blockid=?";
		$res_seq= $adb->pquery($sql_seq, array($after_block));
		$row_seq=$adb->fetch_array($res_seq);
		$block_sequence=$row_seq['sequence'];
		$newblock_sequence=$block_sequence+1;

		$sql_up="update vtiger_blocks set sequence=sequence+1 where tabid=? and sequence > ?";
		$adb->pquery($sql_up, array($tabid,$block_sequence));

		$max_blockid=$adb->getUniqueID('vtiger_blocks');
		$iscustom = 1;
		$sql="INSERT INTO vtiger_blocks (tabid, blockid, sequence, blocklabel,iscustom) values (?,?,?,?,?)";
		$params = array($tabid,$max_blockid,$newblock_sequence,$newblocklabel,$iscustom);
		$adb->pquery($sql,$params);
	}

}

function deleteBlock() {
	global $adb;
	$blockid = $_REQUEST['blockid'];
	$deleteblock = 'delete from vtiger_blocks where blockid = ? and iscustom = 1';
	$res = $adb->pquery($deleteblock,array($blockid));

}

function addCustomField() {

	global $current_user,$log,$adb;

	$fldmodule=vtlib_purify($_REQUEST['fld_module']);
	$fldlabel=vtlib_purify(trim($_REQUEST['fldLabel']));
	$fldType= vtlib_purify($_REQUEST['fieldType']);
	$parenttab=vtlib_purify($_REQUEST['parenttab']);
	$mode=vtlib_purify($_REQUEST['mode']);
	$blockid = vtlib_purify($_REQUEST['blockid']);

	$tabid = getTabid($fldmodule);
	if ($fldmodule == 'Calendar' && isset($_REQUEST['activity_type'])) {
		$activitytype = $_REQUEST['activity_type'];
		if ($activitytype == 'E') $tabid = '16';
		if ($activitytype == 'T') $tabid = '9';
	}
	if(get_magic_quotes_gpc() == 1) {
		$fldlabel = stripslashes($fldlabel);
	}

	$dup_check_tab_id = $tabid;
	if ($fldmodule == 'Calendar')
		$dup_check_tab_id = array('9', '16');
	$checkquery="select * from vtiger_field where tabid in (". generateQuestionMarks($dup_check_tab_id) .") and fieldlabel=?";
	$params =  array($dup_check_tab_id, $fldlabel);
	$checkresult=$adb->pquery($checkquery,$params);

	if($adb->num_rows($checkresult) > 0 ) {
		$duplicate = 'yes';
		return $duplicate ;
	}
	else {
		$max_fieldid = $adb->getUniqueID("vtiger_field");
		$columnName = 'cf_'.$max_fieldid;
		$custfld_fieldid = $max_fieldid;
		//Assigning the vtiger_table Name
		if($fldmodule != '') {
			$focus = CRMEntity::getInstance($fldmodule);
			if (isset($focus->customFieldTable)) {
				$tableName=$focus->customFieldTable[0];
			} else {
				$tableName= 'vtiger_'.strtolower($fldmodule).'cf';
			}
		}
		//Assigning the uitype
		$fldlength=$_REQUEST['fldLength'];
		$uitype='';
		$fldPickList='';
		if(isset($_REQUEST['fldDecimal']) && $_REQUEST['fldDecimal'] != '') {
			$decimal=$_REQUEST['fldDecimal'];
		}else {
			$decimal=0;
		}
		$type='';
		$uichekdata='';
		if($fldType == 'Text') {
			$uichekdata='V~O~LE~'.$fldlength;
			$uitype = 1;
			$type = "C(".$fldlength.") default ()"; // adodb type
		}elseif($fldType == 'Number') {
			$uitype = 7;
			//this may sound ridiculous passing decimal but that is the way adodb wants
			$dbfldlength = $fldlength + $decimal + 1;
			$type="N(".$dbfldlength.".".$decimal.")";	// adodb type
			// Fix for http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/6363
			$uichekdata='NN~O~'.$fldlength .','.$decimal;
		}elseif($fldType == 'Percent') {
			$uitype = 9;
			$type="N(5.2)"; //adodb type
			$uichekdata='N~O~2~2';
		}elseif($fldType == 'Currency') {
			$uitype = 71;
			$dbfldlength = $fldlength + $decimal + 1;
			$type="N(".$dbfldlength.".".$decimal.")"; //adodb type
			$uichekdata='N~O~'.$fldlength .','.$decimal;
		}elseif($fldType == 'Date') {
			$uichekdata='D~O';
			$uitype = 5;
			$type = "D"; // adodb type
		}elseif($fldType == 'Email') {
			$uitype = 13;
			$type = "C(50) default () "; //adodb type
			$uichekdata='E~O';
		}elseif($fldType == 'Time') {
			$uitype = 14;
			$type = "TIME";
			$uichekdata='T~O';
		}elseif($fldType == 'Phone') {
			$uitype = 11;
			$type = "C(30) default () "; //adodb type
			$uichekdata='V~O';
		}elseif($fldType == 'Picklist') {
			$uitype = 15;
			$type = "C(255) default () "; //adodb type
			$uichekdata='V~O';
		}elseif($fldType == 'URL') {
			$uitype = 17;
			$type = "C(255) default () "; //adodb type
			$uichekdata='V~O';
		}elseif($fldType == 'Checkbox') {
			$uitype = 56;
			$type = "C(3) default 0"; //adodb type
			$uichekdata='C~O';
		}elseif($fldType == 'TextArea') {
			$uitype = 21;
			$type = "X"; //adodb type
			$uichekdata='V~O';
		}elseif($fldType == 'MultiSelectCombo') {
			$uitype = 33;
			$type = "X"; //adodb type
			$uichekdata='V~O';
		}elseif($fldType == 'Skype') {
			$uitype = 85;
			$type = "C(255) default () "; //adodb type
			$uichekdata='V~O';
		}

		if(is_numeric($blockid)) {
			if($_REQUEST['fieldid'] == '') {
				$max_fieldsequence = "select max(sequence) as maxsequence from vtiger_field where block = ? ";
				$res = $adb->pquery($max_fieldsequence,array($blockid));
				$max_seq = $adb->query_result($res,0,'maxsequence');
				if($fldmodule == 'Quotes' || $fldmodule == 'PurchaseOrder' || $fldmodule == 'SalesOrder' || $fldmodule == 'Invoice') {
					$quickcreate = 3;
				}else {
					$quickcreate = 1;
				}
				$query = "insert into vtiger_field (tabid,fieldid,columnname,tablename,generatedtype,uitype,fieldname,fieldlabel,readonly,presence,defaultvalue,maximumlength,sequence,block,displaytype,typeofdata,quickcreate,quickcreatesequence,info_type,masseditable) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				$qparams = array($tabid,$custfld_fieldid,$columnName,$tableName,2,$uitype,$columnName,$fldlabel,0,2,'',100,$max_seq+1,$blockid,1,$uichekdata,$quickcreate,0,'BAS',1);
				$adb->pquery($query, $qparams);
				$adb->alterTable($tableName, $columnName." ".$type, "Add_Column");
				//Inserting values into vtiger_profile2field vtiger_tables
				$sql1 = "select * from vtiger_profile";
				$sql1_result = $adb->pquery($sql1, array());
				$sql1_num = $adb->num_rows($sql1_result);
				for($i=0; $i<$sql1_num; $i++) {
					$profileid = $adb->query_result($sql1_result,$i,"profileid");
					$sql2 = "insert into vtiger_profile2field values(?,?,?,?,?)";
					$adb->pquery($sql2, array($profileid, $tabid, $custfld_fieldid, 0, 0));
				}

				//Inserting values into def_org vtiger_tables
				$sql_def = "insert into vtiger_def_org_field values(?,?,?,?)";
				$adb->pquery($sql_def, array($tabid, $custfld_fieldid, 0, 0));

				if($fldType == 'Picklist' || $fldType == 'MultiSelectCombo') {
					$columnName = $adb->sql_escape_string($columnName);
					// Creating the PickList Table and Populating Values
					if($_REQUEST['fieldid'] == '') {
						$qur = "CREATE TABLE vtiger_".$columnName." (
							".$columnName."id int(19) NOT NULL auto_increment,
							".$columnName." varchar(200) NOT NULL,
							presence int(1) NOT NULL default '1',
							picklist_valueid int(19) NOT NULL default '0',
							PRIMARY KEY  (".$columnName."id)
						)";
						$adb->pquery($qur, array());
					}

					//Adding a  new picklist value in the picklist table
					if($mode != 'edit') {
						$picklistid = $adb->getUniqueID("vtiger_picklist");
						$sql="insert into vtiger_picklist values(?,?)";
						$adb->pquery($sql, array($picklistid,$columnName));
					}
					$roleid=$current_user->roleid;
					$qry="select picklistid from vtiger_picklist where  name=?";
					$picklistid = $adb->query_result($adb->pquery($qry, array($columnName)), 0,'picklistid');
					$pickArray = Array();
					$fldPickList =  $_REQUEST['fldPickList'];
					$pickArray = explode("\n",$fldPickList);
					$count = count($pickArray);
					global $default_charset;
					for($i = 0; $i < $count; $i++) {
						$pickArray[$i] = trim(htmlentities($pickArray[$i],ENT_QUOTES, $default_charset));
						if($pickArray[$i] != '') {
							$picklistcount=0;
							$sql ="select $columnName from vtiger_$columnName";
							$numrow = $adb->num_rows($adb->pquery($sql, array()));
							for($x=0;$x < $numrow ; $x++) {
								$picklistvalues = $adb->query_result($adb->pquery($sql, array()),$x,$columnName);
								if($pickArray[$i] == $picklistvalues) {
									$picklistcount++;
								}
							}
							if($picklistcount == 0) {
								$picklist_valueid = getUniquePicklistID();
								$query = "insert into vtiger_".$columnName." values(?,?,?,?)";
								$adb->pquery($query, array($adb->getUniqueID("vtiger_".$columnName),$pickArray[$i],1,$picklist_valueid));
								/*$sql="update vtiger_picklistvalues_seq set id = ?";
								$adb->pquery($sql, array(++$picklist_valueid));*/
							}
							$sql = "select picklist_valueid from vtiger_$columnName where $columnName=?";
							$pick_valueid = $adb->query_result($adb->pquery($sql, array($pickArray[$i])),0,'picklist_valueid');
							$sql = "insert into vtiger_role2picklist select roleid,$pick_valueid,$picklistid,$i from vtiger_role";
							$adb->pquery($sql, array());
						}
					}
				}
			}
		}
	}
}

function show_move_hiddenfields($submode) {
	global $adb,$log;
	$selected_fields = $_REQUEST['selected'];
	$selected = trim($selected_fields,":");
	$sel_arr = array();
	$sel_arr = explode(":",$selected);
	$sequence = $adb->pquery('select max(sequence) as maxseq from vtiger_field where block = ?  and tabid = ?',array($_REQUEST['blockid'],$_REQUEST['tabid']));
	$max = $adb->query_result($sequence,0,'maxseq');
	$max_seq = $max + 1;

	if($submode == 'showhiddenfields') {
		for($i=0; $i< count($sel_arr);$i++) {
			$res = $adb->pquery('update vtiger_field set presence = 2,sequence = ? where block = ? and fieldid = ?', array($max_seq,$_REQUEST['blockid'],$sel_arr[$i]));
			$max_seq++;
		}
	}
	else {
		for($i=0; $i< count($sel_arr);$i++) {
			$res = $adb->pquery('update vtiger_field set sequence = ? , block = ? where fieldid = ?', array($max_seq,$_REQUEST['blockid'],$sel_arr[$i]));
			$max_seq++;
		}
	}
}

function getRelatedListInfo($module) {
	global $adb;
	$tabid = getTabid($module);
	$related_query = 'select * from vtiger_relatedlists ' .
			'inner join vtiger_tab on vtiger_relatedlists.related_tabid = vtiger_tab.tabid and vtiger_tab.presence = 0 where vtiger_relatedlists.tabid = ? order by sequence';
	$relinfo = $adb->pquery($related_query,array($tabid));
	$noofrows = $adb->num_rows($relinfo);
	for($i=0;$i<$noofrows;$i++) {
		$res[$i]['name'] = $adb->query_result($relinfo,$i,'name');
		$res[$i]['sequence'] = $adb->query_result($relinfo,$i,'sequence');
		$label = $adb->query_result($relinfo,$i,'label');
		$relatedModule = getTabname($adb->query_result($relinfo,$i,'related_tabid'));
		$res[$i]['label'] = getTranslatedString($label,$relatedModule);
		$res[$i]['presence'] = $adb->query_result($relinfo,$i,'presence');
		$res[$i]['tabid'] = $tabid;
		$res[$i]['id'] = $adb->query_result($relinfo,$i,'relation_id');
	}
	return $res;
}

function changeRelatedListOrder() {
	global $adb,$log;
	$tabid = $_REQUEST['tabid'];
	$what_todo = $_REQUEST['what_to_do'];
	if(!empty($_REQUEST['what_to_do'])) {
		if($_REQUEST['what_to_do'] == 'move_up') {
			$currentsequence = $_REQUEST['sequence'];

			$previous_relation = $adb->pquery('select relation_id,sequence from vtiger_relatedlists where sequence < ? and tabid = ? order by sequence desc limit 0,1',array($currentsequence,$tabid));
			$previous_sequence = $adb->query_result($previous_relation,0,'sequence');
			$previous_relationid = $adb->query_result($previous_relation,0,'relation_id');

			$adb->pquery('update vtiger_relatedlists set sequence = ? where relation_id = ? and tabid = ?',array($previous_sequence,$_REQUEST['id'],$tabid));
			$adb->pquery('update vtiger_relatedlists set sequence = ? where tabid = ? and relation_id = ?',array($currentsequence,$tabid,$previous_relationid));
		}elseif($_REQUEST['what_to_do'] == 'move_down') {

			$currentsequence = $_REQUEST['sequence'];

			$next_relation = $adb->pquery('select relation_id,sequence from vtiger_relatedlists where sequence > ? and tabid = ? order by sequence limit 0,1',array($currentsequence,$tabid));
			$next_sequence = $adb->query_result($next_relation,0,'sequence');
			$next_relationid = $adb->query_result($next_relation,0,'relation_id');

			$adb->pquery('update vtiger_relatedlists set sequence = ? where relation_id = ? and tabid = ?',array($next_sequence,$_REQUEST['id'],$tabid));
			$adb->pquery('update vtiger_relatedlists set sequence = ? where tabid = ? and relation_id = ?',array($currentsequence,$tabid,$next_relationid));

		}
	}
}

?>