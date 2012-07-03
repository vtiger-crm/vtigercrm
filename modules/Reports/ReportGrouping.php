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
require_once("data/Tracker.php");
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/Reports/Reports.php');

global $app_strings, $app_list_strings, $mod_strings;
$current_module_strings = return_module_language($current_language, 'Reports');

global $list_max_entries_per_page, $urlPrefix;

$log = LoggerManager::getLogger('report_type');

global $currentModule, $image_path, $theme;
$report_group=new vtigerCRM_Smarty;
$report_group->assign("MOD", $mod_strings);
$report_group->assign("APP", $app_strings);
$report_group->assign("IMAGE_PATH",$image_path);

if(isset($_REQUEST["record"]) && $_REQUEST['record']!='')
{
	$reportid = vtlib_purify($_REQUEST["record"]);
	$oReport = new Reports($reportid);
	$list_array = $oReport->getSelctedSortingColumns($reportid);

	$oRep = new Reports();
	$secondarymodule = '';
	$secondarymodules =Array();

	if(!empty($oRep->related_modules[$oReport->primodule])) {
		foreach($oRep->related_modules[$oReport->primodule] as $key=>$value){
			if(isset($_REQUEST["secondarymodule_".$value]))$secondarymodules []= vtlib_purify($_REQUEST["secondarymodule_".$value]);
		}
	}
	$secondarymodule = implode(":",$secondarymodules);

	if($secondarymodule!='')
		$oReport->secmodule = $secondarymodule;

	$BLOCK1 = getPrimaryColumns_GroupingHTML($oReport->primodule,$list_array[0]);
	$BLOCK1 .= getSecondaryColumns_GroupingHTML($oReport->secmodule,$list_array[0]);
	$report_group->assign("BLOCK1",$BLOCK1);
	$GROUPBYTIME1 = getGroupByTimeDiv(1,$reportid);
	$report_group->assign("GROUPBYTIME1",$GROUPBYTIME1);

	$BLOCK2 = getPrimaryColumns_GroupingHTML($oReport->primodule,$list_array[1]);
	$BLOCK2 .= getSecondaryColumns_GroupingHTML($oReport->secmodule,$list_array[1]);
	$report_group->assign("BLOCK2",$BLOCK2);

	$GROUPBYTIME2 = getGroupByTimeDiv(2,$reportid);
	$report_group->assign("GROUPBYTIME2",$GROUPBYTIME2);

	$BLOCK3 = getPrimaryColumns_GroupingHTML($oReport->primodule,$list_array[2]);
	$BLOCK3 .= getSecondaryColumns_GroupingHTML($oReport->secmodule,$list_array[2]);
	$report_group->assign("BLOCK3",$BLOCK3);
	$GROUPBYTIME3 = getGroupByTimeDiv(3,$reportid);
	$report_group->assign("GROUPBYTIME3",$GROUPBYTIME3);

	$sortorder = $oReport->ascdescorder;

}else
{
	$primarymodule = vtlib_purify($_REQUEST["primarymodule"]);
	$BLOCK1 = getPrimaryColumns_GroupingHTML($primarymodule);
	$ogReport =  new Reports();
	if(!empty($ogReport->related_modules[$primarymodule])) {
		foreach($ogReport->related_modules[$primarymodule] as $key=>$value){
			$BLOCK1 .= getSecondaryColumns_GroupingHTML($_REQUEST["secondarymodule_".$value]);
		}
	}
	$report_group->assign("BLOCK1",$BLOCK1);
	$report_group->assign("BLOCK2",$BLOCK1);
	$report_group->assign("BLOCK3",$BLOCK1);
	$GROUPBYTIME1 = getGroupByTimeDiv(1);
	$report_group->assign("GROUPBYTIME1",$GROUPBYTIME1);

	$GROUPBYTIME2 = getGroupByTimeDiv(2);
	$report_group->assign("GROUPBYTIME2",$GROUPBYTIME2);

	$GROUPBYTIME3 = getGroupByTimeDiv(3);
	$report_group->assign("GROUPBYTIME3",$GROUPBYTIME3);
}


	/** Function to get the combo values for the Primary module Columns
	 *  @ param $module(module name) :: Type String
	 *  @ param $selected (<selected or ''>) :: Type String
	 *  This function generates the combo values for the columns  for the given module
	 *  and return a HTML string
	 */

function getPrimaryColumns_GroupingHTML($module,$selected="")
{
	global $ogReport, $app_list_strings, $current_language;
	$id_added=false;
	$mod_strings = return_module_language($current_language,$module);

	$block_listed = array();
 	$selected = decode_html($selected);
    foreach($ogReport->module_list[$module] as $key=>$value)
    {
        if(isset($ogReport->pri_module_columnslist[$module][$value]) && !$block_listed[$value])
        {
			$block_listed[$value] = true;
			$shtml .= "<optgroup label=\"".$app_list_strings['moduleList'][$module]." ".getTranslatedString($value, $module)."\" class=\"select\" style=\"border:none\">";
			if($id_added==false){
				$is_selected ='';
				if($selected == "vtiger_crmentity:crmid:".$module."_ID:crmid:I"){
					$is_selected = 'selected';
				}
				$shtml .= "<option value=\"vtiger_crmentity:crmid:".$module."_ID:crmid:I\" {$is_selected}>".
							getTranslatedString($module, $module).' '.getTranslatedString('ID', $module).
						"</option>";
				$id_added=true;
			}
			foreach($ogReport->pri_module_columnslist[$module][$value] as $field=>$fieldlabel)
			{
				if(isset($mod_strings[$fieldlabel]))
				{
					if($selected == decode_html($field))
					{
						$shtml .= "<option selected value=\"".$field."\">".$mod_strings[$fieldlabel]."</option>";
					}else
					{
						$shtml .= "<option value=\"".$field."\">".$mod_strings[$fieldlabel]."</option>";
					}
				}else
				{
					if($selected == decode_html($field))
					{
						$shtml .= "<option selected value=\"".$field."\">".$fieldlabel."</option>";
					}else
					{
						$shtml .= "<option value=\"".$field."\">".$fieldlabel."</option>";
					}

				}
			}
       }
    }
    return $shtml;
}

	/** Function to get the combo values for the Secondary module Columns
	 *  @ param $module(module name) :: Type String
	 *  @ param $selected (<selected or ''>) :: Type String
	 *  This function generates the combo values for the columns for the given module
	 *  and return a HTML string
	 */
function getSecondaryColumns_GroupingHTML($module,$selected="")
{
	global $ogReport;
	global $app_list_strings;
	global $current_language;

 	$selected = decode_html($selected);
	if($module != "")
	{
		$secmodule = explode(":",$module);
		for($i=0;$i < count($secmodule) ;$i++)
		{
			$mod_strings = return_module_language($current_language,$secmodule[$i]);
			if(vtlib_isModuleActive($secmodule[$i])){
				$block_listed = array();
				foreach($ogReport->module_list[$secmodule[$i]] as $key=>$value)
				{
					if(isset($ogReport->sec_module_columnslist[$secmodule[$i]][$value]) && !$block_listed[$value]) {
						$block_listed[$value] = true;
						$shtml .= "<optgroup label=\"".$app_list_strings['moduleList'][$secmodule[$i]]." ".getTranslatedString($value)."\" class=\"select\" style=\"border:none\">";
						foreach($ogReport->sec_module_columnslist[$secmodule[$i]][$value] as $field=>$fieldlabel)
						{
							if(isset($mod_strings[$fieldlabel])) {
								if($selected == decode_html($field)) {
									$shtml .= "<option selected value=\"".$field."\">".$mod_strings[$fieldlabel]."</option>";
								} else {
									$shtml .= "<option value=\"".$field."\">".$mod_strings[$fieldlabel]."</option>";
								}
							} else {
								if($selected == decode_html($field)) {
									$shtml .= "<option selected value=\"".$field."\">".$fieldlabel."</option>";
								} else {
									$shtml .= "<option value=\"".$field."\">".$fieldlabel."</option>";
								}
							}
						}
					}
				}
			}
		}
	}
	return $shtml;
}

function getGroupByTimeDiv($sortid,$reportid=''){
	require_once 'include/utils/CommonUtils.php';
	global $adb, $mod_strings;
	$query = "select * from vtiger_reportgroupbycolumn where reportid=? and sortid=?";
	$result = $adb->pquery($query,array($reportid,$sortid));
	$rows = $adb->num_rows($result);
	$yearselected = '';
	$monthselected = '';
	$quarterselected = '';
    $noneselected='';
	if($rows > 0){
		$displaystyle = 'inline';
		$selected_groupby = $adb->query_result($result,0,'dategroupbycriteria');
		if($selected_groupby == 'Year'){
			$yearselected = 'selected';
		}
		elseif($selected_groupby == 'Month'){
			$monthselected = 'selected';
		}
		else if($selected_groupby == 'Quarter'){
			$quarterselected = 'selected';
		}
		else if(strtolower($selected_groupby)=='none'){
			$noneselected='selected';
		}
	}
	else{
		$displaystyle = 'none';
		$noneselected = 'selected';
	}
	$divid = 'Group'.$sortid.'time';
	$selectid = 'groupbytime'.$sortid;
	$div = '';
	$div .= "<div id=$divid style='display:$displaystyle'>".$mod_strings['LBL_GROUPING_TIME']."<br>";
	$div .= "<select id=$selectid name=$selectid  class='txtBox'>";
	$div .= "<option value='None' $noneselected>".$mod_strings['LBL_NONE']."</option>";
	$div .= "<option value='Year' $yearselected>".$mod_strings['LBL_YEAR']."</option>";
	$div .= "<option value='Month' $monthselected>".$mod_strings['LBL_MONTH']."</option>";
	$div .= "<option value='Quarter' $quarterselected>".$mod_strings['LBL_QUARTER']."</option>";
	$div .= "</select></div>";
	return $div;
}
if($sortorder[0] != "Descending")
{
$shtml = "<option selected value='Ascending'>".$app_strings['Ascending']."</option>
	 <option value='Descending'>".$app_strings['Descending']."</option>";
}else
{
$shtml = "<option value='Ascending'>".$app_strings['Ascending']."</option>
	  <option selected value='Descending'>".$app_strings['Descending']."</option>";
}
$report_group->assign("ASCDESC1",$shtml);

if($sortorder[1] != "Descending")
{
$shtml = "<option selected value='Ascending'>".$app_strings['Ascending']."</option>
          <option value='Descending'>".$app_strings['Descending']."</option>";
}else
{
$shtml = "<option value='Ascending'>".$app_strings['Ascending']."</option>
          <option selected value='Descending'>".$app_strings['Descending']."</option>";
}
$report_group->assign("ASCDESC2",$shtml);

if($sortorder[2] != "Descending")
{
$shtml = "<option selected value='Ascending'>".$app_strings['Ascending']."</option>
	  <option value='Descending'>".$app_strings['Descending']."</option>";
}else
{
$shtml =  "<option value='Ascending'>".$app_strings['Ascending']."</option>
	   <option selected value='Descending'>".$app_strings['Descending']."</option>";
}
$report_group->assign("ASCDESC3",$shtml);
$report_group->display("ReportGrouping.tpl");

?>
