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
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/Reports/Reports.php');

global $app_strings;
global $app_list_strings;
global $mod_strings;
$current_module_strings = return_module_language($current_language, 'Reports');

global $list_max_entries_per_page;
global $urlPrefix;

$log = LoggerManager::getLogger('report_list');

global $currentModule;

global $image_path;
global $theme;

// focus_list is the means of passing data to a ListView.
global $focus_list;

$save_report_form=new XTemplate ("modules/Reports/SaveReport.html");
$save_report_form->assign("MOD", $mod_strings);
$save_report_form->assign("APP", $app_strings);
$save_report_form->assign("THEME_PATH",$theme);
$oReport = new Reports();
$reportfolderhtml = $oReport->sgetRptFldrSaveReport();
$save_report_form->assign("REPORT_FOLDER", $reportfolderhtml);
$save_report_form->parse("main");
$save_report_form->out("main");
?>
