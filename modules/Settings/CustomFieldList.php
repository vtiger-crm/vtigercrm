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
require_once('include/database/PearDatabase.php');
require_once('include/CustomFieldUtil.php');

global $mod_strings,$app_strings,$theme;

$smarty=new vtigerCRM_Smarty;
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$theme_path="themes/".$theme."/";
$image_path="themes/images/";
$smarty->assign("IMAGE_PATH", $image_path);
$module_array=getCustomFieldSupportedModules();

$cfimagecombo = Array($image_path."text.gif",
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
$image_path."picklist.gif");

$cftextcombo = Array($mod_strings['Text'],
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
$mod_strings['LBL_MULTISELECT_COMBO']
);


$smarty->assign("MODULES",$module_array);
$smarty->assign("CFTEXTCOMBO",$cftextcombo);
$smarty->assign("CFIMAGECOMBO",$cfimagecombo);
if($_REQUEST['fld_module'] !='')
	$fld_module = vtlib_purify($_REQUEST['fld_module']);
elseif($_REQUEST['formodule'] != ''){
	$fld_module = vtlib_purify($_REQUEST['formodule']);
}	
else
	$fld_module = 'Leads';
$smarty->assign("MODULE",$fld_module);
$smarty->assign("CFENTRIES",getCFListEntries($fld_module));
if(isset($_REQUEST["duplicate"]) && $_REQUEST["duplicate"] == "yes")
{
	$error=getTranslatedString('ERR_CUSTOM_FIELD_WITH_NAME','Settings'). vtlib_purify($_REQUEST["fldlabel"]) .getTranslatedString('ERR_ALREADY_EXISTS','Settings') . ' ' .getTranslatedString('ERR_SPECIFY_DIFFERENT_LABEL','Settings');
	$smarty->assign("DUPLICATE_ERROR", $error);
}

if($_REQUEST['mode'] !='')
	$mode = vtlib_purify($_REQUEST['mode']);
$smarty->assign("MODE", $mode);

if($_REQUEST['ajax'] != 'true')
	$smarty->display(vtlib_getModuleTemplate('Calendar','CustomFieldList.tpl'));	
else
	$smarty->display(vtlib_getModuleTemplate('Calendar','CustomFieldEntries.tpl'));

	/**
	* Function to get customfield entries
	* @param string $module - Module name
	* return array  $cflist - customfield entries
	*/
function getCFListEntries($module)
{
	global $adb,$app_strings,$theme,$smarty,$log;
	$tabid = getTabid($module);
	if ($module == 'Calendar') {
		$tabid = array(9, 16);
	}
	$theme_path="themes/".$theme."/";
	$image_path="themes/images/";
	$dbQuery = "select fieldid,columnname,fieldlabel,uitype,displaytype,block,vtiger_convertleadmapping.cfmid,tabid from vtiger_field left join vtiger_convertleadmapping on  vtiger_convertleadmapping.leadfid = vtiger_field.fieldid where tabid in (". generateQuestionMarks($tabid) .") and vtiger_field.presence in (0,2) and generatedtype = 2 order by sequence";
	$result = $adb->pquery($dbQuery, array($tabid));
	$row = $adb->fetch_array($result);
	$count=1;
	$cflist=Array();
	if($row!='')
	{
		do
		{
			$cf_element=Array();
			$cf_element['no']=$count;
			$cf_element['label']=getTranslatedString($row["fieldlabel"],$module);
			$fld_type_name = getCustomFieldTypeName($row["uitype"]);
			$cf_element['type']=$fld_type_name;
			$cf_tab_id = $row["tabid"];
			if($module == 'Leads')
			{
				$mapping_details = getListLeadMapping($row["cfmid"]);
				$cf_element[]= $mapping_details['accountlabel'];
				$cf_element[]= $mapping_details['contactlabel'];
				$cf_element[]= $mapping_details['potentiallabel'];
			}
			if($module == 'Calendar')
			{
				if ($cf_tab_id == '9')
					$cf_element['activitytype'] = getTranslatedString('Task',$module);
				else
					$cf_element['activitytype'] = getTranslatedString('Event',$module);
			}
			if ($module == 'Calendar') {
				$cf_element['tool']='&nbsp;<img style="cursor:pointer;" onClick="deleteCustomField('.$row["fieldid"].',\''.$module.'\', \''.$row["columnname"].'\', \''.$row["uitype"].'\')" src="'. vtiger_imageurl('delete.gif', $theme) .'" border="0"  alt="'.$app_strings['LBL_DELETE_BUTTON_LABEL'].'" title="'.$app_strings['LBL_DELETE_BUTTON_LABEL'].'"/></a>';
			}
			$cflist[] = $cf_element;
			$count++;
		}while($row = $adb->fetch_array($result));
	}
	return $cflist;
}

/**
* Function to Lead customfield Mapping entries
* @param integer  $cfid   - Lead customfield id
* return array    $label  - customfield mapping
*/
function getListLeadMapping($cfid)
{
	global $adb;
	$sql="select * from vtiger_convertleadmapping where cfmid =?";
	$result = $adb->pquery($sql, array($cfid));
	$noofrows = $adb->num_rows($result);
	for($i =0;$i <$noofrows;$i++)
	{
		$leadid = $adb->query_result($result,$i,'leadfid');
		$accountid = $adb->query_result($result,$i,'accountfid');
		$contactid = $adb->query_result($result,$i,'contactfid');
		$potentialid = $adb->query_result($result,$i,'potentialfid');
		$cfmid = $adb->query_result($result,$i,'cfmid');

		$sql2="select fieldlabel from vtiger_field where fieldid =?";
		$result2 = $adb->pquery($sql2, array($accountid));
		$accountfield = $adb->query_result($result2,0,'fieldlabel');
		$label['accountlabel'] = getTranslatedString($accountfield,'Accounts');
		
		$sql3="select fieldlabel from vtiger_field where fieldid =?";
		$result3 = $adb->pquery($sql3, array($contactid));
		$contactfield = $adb->query_result($result3,0,'fieldlabel');
		$label['contactlabel'] = getTranslatedString($contactfield,'Contacts');
		$sql4="select fieldlabel from vtiger_field where fieldid =?";
		$result4 = $adb->pquery($sql4, array($potentialid));
		$potentialfield = $adb->query_result($result4,0,'fieldlabel');
		$label['potentiallabel'] = getTranslatedString($potentialfield,'Potentials');
	}
	return $label;
}

/* function to get the modules supports Custom Fields
*/

function getCustomFieldSupportedModules()
{
	global $adb;
	// vtlib customization: Do not list disabled modules.
	//$sql="select distinct vtiger_field.tabid,name from vtiger_field inner join vtiger_tab on vtiger_field.tabid=vtiger_tab.tabid where vtiger_field.tabid not in(9,10,16,15,8,29)";
	$sql="select distinct vtiger_field.tabid,name from vtiger_field inner join vtiger_tab on vtiger_field.tabid=vtiger_tab.tabid where vtiger_field.tabid not in(10,16,15,8,29) and vtiger_tab.presence != 1"; // 16 is still here as we do not want duplicates for Calendar - Both 9 and 16 point to Calendar itself
	// END
	$result = $adb->pquery($sql, array());
	while($moduleinfo=$adb->fetch_array($result))
	{
		$modulelist[$moduleinfo['name']] = $moduleinfo['name'];
	}
	return $modulelist;
}
?>