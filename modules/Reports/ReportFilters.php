<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 *****************************************************>***************************/
 
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
global $current_user;

$report_std_filter = new vtigerCRM_Smarty; 
$report_std_filter->assign("MOD", $mod_strings);
$report_std_filter->assign("APP", $app_strings);
$report_std_filter->assign("IMAGE_PATH",$image_path);
$report_std_filter->assign("DATEFORMAT",$current_user->date_format);
$report_std_filter->assign("JS_DATEFORMAT",parse_calendardate($app_strings['NTC_DATE_FORMAT']));

include("modules/Reports/StandardFilter.php");
include("modules/Reports/AdvancedFilter.php");

$report_std_filter->display('ReportFilters.tpl');
?>
