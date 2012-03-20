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
global $app_strings;
global $app_list_strings;
global $mod_strings;
$current_module_strings = return_module_language($current_language, 'Reports');

global $list_max_entries_per_page;
global $urlPrefix;

$log = LoggerManager::getLogger('report_type');

global $currentModule;
global $image_path;
global $theme;
$theme_path="themes/".$theme."/";
$report_column=new vtigerCRM_Smarty;
$report_column->assign("MOD", $mod_strings);
$report_column->assign("APP", $app_strings);
$report_column->assign("IMAGE_PATH",$image_path);
$report_column->assign("THEME_PATH",$theme_path);
if(isset($_REQUEST["record"]) && $_REQUEST['record']!='')
{
	$recordid = vtlib_purify($_REQUEST["record"]);
	$oReport = new Reports($recordid);
	$BLOCK1 = getPrimaryColumnsHTML($oReport->primodule);
	
	$oRep = new Reports();
	$secondarymodule = '';
	$secondarymodules =Array();
	if(!empty($oRep->related_modules[$oReport->primodule])) {
		foreach($oRep->related_modules[$oReport->primodule] as $key=>$value){
			if(isset($_REQUEST["secondarymodule_".$value]))$secondarymodules []= $_REQUEST["secondarymodule_".$value];
		}
	}
	$secondarymodule = implode(":",$secondarymodules);
	
	$oReport->secmodule = $secondarymodule;
	$BLOCK1 .= getSecondaryColumnsHTML($oReport->secmodule);	
	$BLOCK2 = $oReport->getSelectedColumnsList($recordid);
	$report_column->assign("BLOCK1",$BLOCK1);
	$report_column->assign("BLOCK2",$BLOCK2);
}else
{
	$primarymodule = vtlib_purify($_REQUEST["primarymodule"]);
	$BLOCK1 = getPrimaryColumnsHTML($primarymodule);
	$ogReport = new Reports();
	if(!empty($ogReport->related_modules[$primarymodule])) {
		foreach($ogReport->related_modules[$primarymodule] as $key=>$value){
			$BLOCK1 .= getSecondaryColumnsHTML($_REQUEST["secondarymodule_".$value]);
		}
	}
		
	$report_column->assign("BLOCK1",$BLOCK1);

}

/** Function to formulate the vtiger_fields for the primary modules 
 *  This function accepts the module name 
 *  as arguments and generates the vtiger_fields for the primary module as
 *  a HTML Combo values
 */

function getPrimaryColumnsHTML($module)
{
	global $ogReport;
	global $app_list_strings;
	global $app_strings;
	global $current_language;
	$id_added=false;
	$mod_strings = return_module_language($current_language,$module);
	$block_listed = array();
	foreach($ogReport->module_list[$module] as $key=>$value)
	{
		if(isset($ogReport->pri_module_columnslist[$module][$value]) && !$block_listed[$value])
		{
			$block_listed[$value] = true;
			$shtml .= "<optgroup label=\"".$app_list_strings['moduleList'][$module]." ".getTranslatedString($value)."\" class=\"select\" style=\"border:none\">";
			if($id_added==false){
				$shtml .= "<option value=\"vtiger_crmentity:crmid:".$module."_ID:crmid:I\">".getTranslatedString(getTranslatedString($module).' ID')."</option>";
				$id_added=true;
			}
			foreach($ogReport->pri_module_columnslist[$module][$value] as $field=>$fieldlabel)
			{
				if(isset($mod_strings[$fieldlabel]))
				{
					$shtml .= "<option value=\"".$field."\">".$mod_strings[$fieldlabel]."</option>";
				}else
				{
					$shtml .= "<option value=\"".$field."\">".$fieldlabel."</option>";
				}
			}
		}
	}
	return $shtml;
}

/** Function to formulate the vtiger_fields for the secondary modules
 *  This function accepts the module name
 *  as arguments and generates the vtiger_fields for the secondary module as
 *  a HTML Combo values
 */


function getSecondaryColumnsHTML($module)
{
	global $ogReport;
	global $app_list_strings,$app_strings;
	global $current_language;

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
					if(isset($ogReport->sec_module_columnslist[$secmodule[$i]][$value]) && !$block_listed[$value])
					{
						$block_listed[$value] = true;
						$shtml .= "<optgroup label=\"".$app_list_strings['moduleList'][$secmodule[$i]]." ".getTranslatedString($value)."\" class=\"select\" style=\"border:none\">";
						foreach($ogReport->sec_module_columnslist[$secmodule[$i]][$value] as $field=>$fieldlabel)
						{
							if(isset($mod_strings[$fieldlabel]))
							{
								$shtml .= "<option value=\"".$field."\">".$mod_strings[$fieldlabel]."</option>";
							}else
							{
								$shtml .= "<option value=\"".$field."\">".$fieldlabel."</option>";
							}
						}
					}
				}
			}
		}
	}
	return $shtml;
}

$report_column->display("ReportColumns.tpl");
?>

