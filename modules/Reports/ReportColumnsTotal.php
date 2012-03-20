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
require_once("data/Tracker.php");
require_once('Smarty_setup.php');
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
$report_column_tot=new vtigerCRM_Smarty;
$report_column_tot->assign("MOD", $mod_strings);
$report_column_tot->assign("APP", $app_strings);
$report_column_tot->assign("IMAGE_PATH",$image_path);

if(isset($_REQUEST["record"]) && $_REQUEST['record']!='')
{
        $recordid = vtlib_purify($_REQUEST["record"]);
        $oReport = new Reports($recordid);
		$oRep = new Reports();
		$secondarymodule = '';
		$secondarymodules =Array();
		
		if(!empty($oRep->related_modules[$oReport->primodule])) {
			foreach($oRep->related_modules[$oReport->primodule] as $key=>$value){
				if(isset($_REQUEST["secondarymodule_".$value]))$secondarymodules []= vtlib_purify($_REQUEST["secondarymodule_".$value]);
			}
		}
		$secondarymodule = implode(":",$secondarymodules);
		$oReport->secmodule = $secondarymodule;

  		$BLOCK1 = $oReport->sgetColumntoTotalSelected($oReport->primodule,$oReport->secmodule,$recordid);
		$report_column_tot->assign("BLOCK1",$BLOCK1);
		$report_column_tot->assign("RECORDID",$recordid);
}else
{
        $primarymodule = vtlib_purify($_REQUEST["primarymodule"]);
        $oReport = new Reports();
        $secondarymodule = Array();
        if(!empty($ogReport->related_modules[$primarymodule])) {
			foreach($ogReport->related_modules[$primarymodule] as $key=>$value){
        		$secondarymodule[] = vtlib_purify($_REQUEST["secondarymodule_".$value]);
        	
			}
        }
        $BLOCK1 = $oReport->sgetColumntoTotal($primarymodule,$secondarymodule);
		$report_column_tot->assign("BLOCK1",$BLOCK1);
}
//added to avoid displaying "No data avaiable to total" when using related modules in report.
if(count($BLOCK1[0]) == 0 &&  count($BLOCK1[1])==0)
	$report_column_tot->assign("ROWS_COUNT",0);
else
	$report_column_tot->assign("ROWS_COUNT","-1");

$report_column_tot->display('ReportColumnsTotal.tpl');
?>
