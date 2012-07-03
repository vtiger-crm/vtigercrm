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
 ********************************************************************************/

require_once('include/utils/utils.php');
require_once('include/logging.php');
require_once("modules/Potentials/Charts.php");
require_once("modules/Dashboard/Forms.php");
global $app_list_strings, $current_language, $tmp_dir, $currentModule, $action, $theme;
$current_module_strings = return_module_language($current_language, 'Dashboard');
require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
require('user_privileges/user_privileges_'.$current_user->id.'.php');

$log = LoggerManager::getLogger('outcome_by_month');

if (isset($_REQUEST['obm_refresh'])) { $refresh = $_REQUEST['obm_refresh']; }
else { $refresh = false; }

// added for auto refresh
$refresh = true;
//

$date_start = array();
$datax = array();
//get the dates to display
//added to fix the issue4307
if(isset($_REQUEST['obm_date_start']) && $_REQUEST['obm_date_start'] == '')
{
	$_SESSION['obm_date_start'] = ""; 
}
elseif(isset($_REQUEST['obm_date_start']) && $_REQUEST['obm_date_start'] != '')
	$_SESSION['obm_date_start'] = $_REQUEST['obm_date_start'];
if(isset($_REQUEST['obm_date_end']) && $_REQUEST['obm_date_end'] == '')
{
	$_SESSION['obm_date_end'] = ""; 
}
elseif(isset($_REQUEST['obm_date_end']) && $_REQUEST['obm_date_end'] != '')
	$_SESSION['obm_date_end'] = $_REQUEST['obm_date_end'];
if (isset($_SESSION['obm_date_start']) && $_SESSION['obm_date_start'] != '' && !isset($_REQUEST['obm_date_start'])) {
	$date_start = $_SESSION['obm_date_start'];
	$log->debug("_SESSION['obm_date_start'] is:");
	$log->debug($_SESSION['obm_date_start']);
}
elseif (isset($_REQUEST['obm_date_start']) && $_REQUEST['obm_date_start'] != '') {
	$date_start = $_REQUEST['obm_date_start'];
	$current_user->setPreference('obm_date_start', $_REQUEST['obm_date_start']);
	$log->debug("_REQUEST['obm_date_start'] is:");
	$log->debug($_REQUEST['obm_date_start']);
	$log->debug("_SESSION['obm_date_start'] is:");
	$log->debug($_SESSION['obm_date_start']);
}
else {
	$date_start = '2000-01-01';
}

if (isset($_SESSION['obm_date_end']) && $_SESSION['obm_date_end'] != '' && !isset($_REQUEST['obm_date_end'])) {
	$date_end = $_SESSION['obm_date_end'];
	$log->debug("_SESSION['obm_date_end'] is:");
	$log->debug($_SESSION['obm_date_end']);
}
elseif (isset($_REQUEST['obm_date_end']) && $_REQUEST['obm_date_end'] != '') {
	$date_end = $_REQUEST['obm_date_end'];
	$current_user->setPreference('obm_date_end', $_REQUEST['obm_date_end']);
	$log->debug("_REQUEST['obm_date_end'] is:");
	$log->debug($_REQUEST['obm_date_end']);
	$log->debug("_SESSION['obm_date_end'] is:");
	$log->debug($_SESSION['obm_date_end']);
}
else {
	$date_end = '2100-01-01';
}

$ids = array();
//get list of user ids for which to display data
if (isset($_SESSION['obm_ids']) && count($_SESSION['obm_ids']) != 0 && !isset($_REQUEST['obm_ids'])) {
	$ids = $_SESSION['obm_ids'];
	$log->debug("_SESSION['obm_ids'] is:");
	$log->debug($_SESSION['obm_ids']);
}
elseif (isset($_REQUEST['obm_ids']) && count($_REQUEST['obm_ids']) > 0) {
	$ids = $_REQUEST['obm_ids'];
	$current_user->setPreference('obm_ids', $_REQUEST['obm_ids']);
	$log->debug("_REQUEST['obm_ids'] is:");
	$log->debug($_REQUEST['obm_ids']);
	$log->debug("_SESSION['obm_ids'] is:");
	$log->debug($_SESSION['obm_ids']);
}
else {
	$ids = get_user_array(false);
	$ids = array_keys($ids);
}

//create unique prefix based on selected vtiger_users for image vtiger_files
$id_hash = '';
if (isset($ids)) {
	sort($ids);
	$id_hash = crc32(implode('',$ids));
}
$log->debug("ids is:");
$log->debug($ids);

$cache_file_name = $id_hash."_outcome_by_month_".$current_language."_".crc32($date_start.$date_end).".png";
$log->debug("cache file name is: $cache_file_name");

if(isPermitted('Potentials','index')=="yes")
{
$draw_this = new jpgraph();
$width = 850;
$height = 500;
if(isset($_REQUEST['display_view']) && $_REQUEST['display_view'] == 'MATRIX')
{
	$width = 350;
	$height = 250;
}

echo $draw_this->outcome_by_month($date_start, $date_end, $ids, $tmp_dir.$cache_file_name, $refresh,$width,$height);
echo "<P><font size='1'><em>".$current_module_strings['LBL_MONTH_BY_OUTCOME_DESC']."</em></font></P>";
if (isset($_REQUEST['obm_edit']) && $_REQUEST['obm_edit'] == 'true') {
	$cal_lang = "en";
	$cal_dateformat = parse_calendardate($app_strings['NTC_DATE_FORMAT']);
	$cal_dateformat = '%Y-%m-%d'; // fix providedd by Jlee for date bug in Dashboard

?>
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-<?php echo $cal_lang ?>.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<form name="outcome_by_month" action="index.php" method="post" >
<input type="hidden" name="module" value="<?php echo $currentModule;?>">
<input type="hidden" name="action" value="<?php echo $action;?>">
<input type="hidden" name="display_view" value="<?php echo vtlib_purify($_REQUEST['display_view'])?>">
<input type="hidden" name="obm_refresh" value="true">
<table cellpadding="2" border="0"><tbody>
<tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_DATE_START']?> <br><em><?php echo $app_strings['NTC_DATE_FORMAT']?></em></td>
<td valign='top' ><input class="text" name="obm_date_start" size='12' maxlength='10' id='date_start'  value='<?php if (isset($_SESSION['obm_date_start'])) echo $_SESSION['obm_date_start']?>'>  <img src="<?php echo vtiger_imageurl('calendar.gif', $theme) ?>" id="date_start_trigger"> </td>
</tr><tr>
<tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_DATE_END'];?><br><em><?php echo $app_strings['NTC_DATE_FORMAT']?></em></td>
<td valign='top' ><input class="text" name="obm_date_end" size='12' maxlength='10' id='date_end' value='<?php if (isset($_SESSION['obm_date_end'])) echo $_SESSION['obm_date_end']?>'>  <img src="<?php echo vtiger_imageurl('calendar.gif', $theme) ?>" id="date_end_trigger"> </td>
</tr><tr>
<td nowrap><?php echo $current_module_strings['LBL_USERS'];?></td>
<?php if($is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid('Potentials')] == 3 or $defaultOrgSharingPermission[getTabid('Potentials')] == 0)) { ?>
	<td valign='top' ><select name="obm_ids[]" multiple size='3'><?php echo get_select_options_with_id(get_user_array(FALSE, "Active", $current_user->id,'private'),$_SESSION['obm_ids']); ?></select></td>
<?php } else { ?>
	<td valign='top' ><select name="obm_ids[]" multiple size='3'><?php echo get_select_options_with_id(get_user_array(FALSE, "Active",$current_user->id),$_SESSION['obm_ids']); ?></select></td>
<?php } ?>
</tr><tr>
<td align="right"><br /> <input class="button" onclick="return verify_chart_data(outcome_by_month);" type="submit" title="<?php echo $app_strings['LBL_SELECT_BUTTON_TITLE']; ?>" accessKey="<?php echo $app_strings['LBL_SELECT_BUTTON_KEY']; ?>" value="<?php echo $app_strings['LBL_SELECT_BUTTON_LABEL']?>" /></td>
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
		$date = new DateTimeField(date('Y-m-d H:i', filemtime($tmp_dir.$cache_file_name)));
		$file_date = $date->getDBInsertDateValue();
	}
	else {
		$file_date = '';
	}
?>
<div align=right><FONT size='1'>
<em><?php  echo $current_module_strings['LBL_CREATED_ON'].' '.$file_date; ?> 
</em>[<a href="javascript:;" onClick="changeView('<?php echo vtlib_purify($_REQUEST['display_view']);?>');"><?php echo $current_module_strings['LBL_REFRESH'];?></a>]
[<a href="index.php?module=<?php echo $currentModule;?>&action=index&obm_edit=true&display_view=<?php echo vtlib_purify($_REQUEST['display_view']);?>"><?php echo $current_module_strings['LBL_EDIT'];?></a>]
</FONT></div>
<?php } 
}
else
{
	echo $mod_strings['LBL_NO_PERMISSION'];	
}
echo get_validate_chart_js();
?>