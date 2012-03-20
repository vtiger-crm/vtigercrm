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
 * Description:  returns HTML for client-side image map.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/utils/utils.php');
require_once('include/logging.php');
require_once("modules/Potentials/Charts.php");
require_once("modules/Dashboard/Forms.php");
global $app_list_strings, $current_language, $tmp_dir, $currentModule, $action, $current_user, $theme;
$current_module_strings = return_module_language($current_language, 'Dashboard');

$log = LoggerManager::getLogger('my_pipeline_by_sales_stage');

if (isset($_REQUEST['mypbss_refresh'])) { $refresh = $_REQUEST['mypbss_refresh']; }
else { $refresh = false; }

// added for auto refresh
$refresh = true;
//

// Get _dom Arrays from Database
$comboFieldNames = Array('sales_stage'=>'sales_stage_dom');
$comboFieldArray = getComboArray($comboFieldNames);

//get the dates to display
if (isset($_SESSION['mypbss_date_start']) && $_SESSION['mypbss_date_start'] != '' && !isset($_REQUEST['mypbss_date_start'])) {
	$date_start = $_SESSION['mypbss_date_start'];
	$log->debug("_SESSION['mypbss_date_start'] is:");
	$log->debug($_SESSION['mypbss_date_start']);
}
elseif (isset($_REQUEST['mypbss_date_start']) && $_REQUEST['mypbss_date_start'] != '') {
	$date_start = $_REQUEST['mypbss_date_start'];
	$current_user->setPreference('mypbss_date_start', $_REQUEST['mypbss_date_start']);
	$log->debug("_REQUEST['mypbss_date_start'] is:");
	$log->debug($_REQUEST['mypbss_date_start']);
	$log->debug("_SESSION['mypbss_date_start'] is:");
	$log->debug($_SESSION['mypbss_date_start']);
}
else {
	$date_start = date("Y-m-d", time());
}

if (isset($_SESSION['mypbss_date_end']) && $_SESSION['mypbss_date_end'] != '' && !isset($_REQUEST['mypbss_date_end'])) {
	$date_end = $_SESSION['mypbss_date_end'];
	$log->debug("_SESSION['mypbss_date_end'] is:");
	$log->debug($_SESSION['mypbss_date_end']);
}
elseif (isset($_REQUEST['mypbss_date_end']) && $_REQUEST['mypbss_date_end'] != '') {
	$date_end = $_REQUEST['mypbss_date_end'];
	$current_user->setPreference('mypbss_date_end', $_REQUEST['mypbss_date_end']);
	$log->debug("_REQUEST['mypbss_date_end'] is:");
	$log->debug($_REQUEST['mypbss_date_end']);
	$log->debug("_SESSION['mypbss_date_end'] is:");
	$log->debug($_SESSION['mypbss_date_end']);
}
else {
	$date_end = '2100-01-01';
}

$tempx = array();
$datax = array();
//get list of sales stage keys to display
if (isset($_SESSION['mypbss_sales_stages']) && count($_SESSION['mypbss_sales_stages']) > 0 && !isset($_REQUEST['mypbss_sales_stages'])) {
	$tempx = $_SESSION['mypbss_sales_stages'];
	$log->debug("_SESSION['mypbss_sales_stages'] is:");
	$log->debug($_SESSION['mypbss_sales_stages']);
}
elseif (isset($_REQUEST['mypbss_sales_stages']) && count($_REQUEST['mypbss_sales_stages']) > 0) {
	$tempx = $_REQUEST['mypbss_sales_stages'];
	$current_user->setPreference('mypbss_sales_stages', $_REQUEST['mypbss_sales_stages']);
	$log->debug("_REQUEST['mypbss_sales_stages'] is:");
	$log->debug($_REQUEST['mypbss_sales_stages']);
	$log->debug("_SESSION['mypbss_sales_stages'] is:");
	$log->debug($_SESSION['mypbss_sales_stages']);
}

//set $datax using selected sales stage keys 
if (count($tempx) > 0) {
	foreach ($tempx as $key) {
		$datax[$key] = $comboFieldArray['sales_stage_dom'][$key];//$app_list_strings['sales_stage_dom'][$key];
	}
}
else {
	$datax = $comboFieldArray['sales_stage_dom'];//app_list_strings['sales_stage_dom'];
}
$log->debug("datax is:");
$log->debug($datax);

$ids = array($current_user->id);
//create unique prefix based on selected vtiger_users for image vtiger_files
$id_hash = '';
if (isset($ids)) {
	sort($ids);
	$id_hash = crc32(implode('',$ids));
}
$log->debug("ids is:");
$log->debug($ids);

$cache_file_name = $id_hash."_pipeline_".$current_language."_".crc32(implode('',$datax)).$date_start.$date_end.".png";
$log->debug("cache file name is: $cache_file_name");

if (substr(phpversion(), 0, 1) == "5") { // php5 }
	echo "<em>Charts not supported in PHP 5.</em>";
}
else {
$draw_this = new jpgraph();
echo $draw_this->pipeline_by_sales_stage($datax, $date_start, $date_end, $ids, $tmp_dir.$cache_file_name, $refresh);
echo "<P><font size='1'><em>".$current_module_strings['LBL_PIPELINE_FORM_TITLE_DESC']."</em></font></P>";
if (isset($_REQUEST['mypbss_edit']) && $_REQUEST['mypbss_edit'] == 'true') {
	$cal_lang = "en";
	$cal_dateformat = parse_calendardate($app_strings['NTC_DATE_FORMAT']);
	$cal_dateformat = '%Y-%m-%d'; // fix providedd by Jlee for date bug in Dashboard
	

?>
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-<?php echo $cal_lang ?>.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<form name='my_pipeline' action="index.php" method="post" >
<input type="hidden" name="module" value="<?php echo $currentModule;?>">
<input type="hidden" name="action" value="<?php echo $action;?>">
<input type="hidden" name="mypbss_refresh" value="true">
<table cellpadding="2" border="0"><tbody>
<tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_DATE_START']?> <br><em><?php echo $app_strings['NTC_DATE_FORMAT']?></em></td>
<td valign='top' ><input class="text" name="mypbss_date_start" size='12' maxlength='10' id='date_start' value='<?php if (isset($_SESSION['mypbss_date_start'])) echo $_SESSION['mypbss_date_start']?>'>  <img src="<?php echo vtiger_imageurl('calendar.gif', $theme) ?>" id="date_start_trigger"> </td>
</tr><tr>
<tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_DATE_END'];?><br><em><?php echo $app_strings['NTC_DATE_FORMAT']?></em></td>
<td valign='top' ><input class="text" name="mypbss_date_end" size='12' maxlength='10' id='date_end' value='<?php if (isset($_SESSION['mypbss_date_end'])) echo $_SESSION['mypbss_date_end']?>'>  <img src="<?php echo vtiger_imageurl('calendar.gif', $theme) ?>" id="date_end_trigger"> </td>
</tr><tr>

<td valign='top' nowrap><?php echo $current_module_strings['LBL_SALES_STAGES'];?></td>
<td valign='top' ><select name="mypbss_sales_stages[]" multiple size='5'><?php echo get_select_options_with_id($comboFieldArray['sales_stage_dom'],$_SESSION['mypbss_sales_stages']); ?></select></td>
</tr><tr>
<td align="right"><br /> <input class="button" onclick="return verify_chart_data(my_pipeline);" type="submit" title="<?php echo $app_strings['LBL_SELECT_BUTTON_TITLE']; ?>" accessKey="<?php echo $app_strings['LBL_SELECT_BUTTON_KEY']; ?>" value="<?php echo $app_strings['LBL_SELECT_BUTTON_LABEL']?>" /></td>
</tr></table>
</form>
<script type="text/javascript">
Calendar.setup ({
	inputField : "date_start", ifFormat : "<?php echo $cal_dateformat ?>", showsTime : false, button : "date_start_trigger", singleClick : true, step : 1
});
Calendar.setup ({
	inputField : "date_end", ifFormat : "<?php echo $cal_dateformat ?>", showsTime : false, button : "date_end_trigger", singleClick : true, step : 1
});
</script>

<?php } 
else {
	if (file_exists($tmp_dir.$cache_file_name)) {
		$file_date = getDisplayDate(date('Y-m-d H:i', filemtime($tmp_dir.$cache_file_name)));
	}
	else {
		$file_date = '';
	}
?>
<div align=right><FONT size='1'>
<em><?php  echo $current_module_strings['LBL_CREATED_ON'].' '.$file_date; ?> 
</em>[<a href="index.php?module=<?php echo $currentModule;?>&action=<?php echo $action;?>&mypbss_refresh=true"><?php echo $current_module_strings['LBL_REFRESH'];?></a>]
[<a href="index.php?module=<?php echo $currentModule;?>&action=<?php echo $action;?>&mypbss_edit=true"><?php echo $current_module_strings['LBL_EDIT'];?></a>]
</FONT></div>
<?php } 
echo get_validate_chart_js();
} 
?>
