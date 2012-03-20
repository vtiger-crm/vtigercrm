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
global $current_language, $ids, $tmp_dir;
$current_module_strings = return_module_language($current_language, 'Dashboard');
require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
require('user_privileges/user_privileges_'.$current_user->id.'.php');

// Get _dom arrays from Database
$comboFieldNames = Array('leadsource'=>'lead_source_dom');
$comboFieldArray = getComboArray($comboFieldNames);

$log = LoggerManager::getLogger('CSIM_pipeline_by_lead_source');

if (isset($_REQUEST['pbls_refresh'])) { $refresh = $_REQUEST['pbls_refresh']; }
else { $refresh = false; }

// added for auto refresh
$refresh = true;
//

$tempx = array();
$datax = array();
//get list of sales stage keys to display
if (isset($_SESSION['pbls_lead_sources']) && count($_SESSION['pbls_lead_sources']) > 0 && !isset($_REQUEST['pbls_lead_sources'])) {
	$tempx = $_SESSION['pbls_lead_sources'];
	$log->debug("_SESSION['pbls_lead_sources'] is:");
	$log->debug($_SESSION['pbls_lead_sources']);
}
elseif (isset($_REQUEST['pbls_lead_sources']) && count($_REQUEST['pbls_lead_sources']) > 0) {
	$tempx = $_REQUEST['pbls_lead_sources'];
	$current_user->setPreference('pbls_lead_sources', $_REQUEST['pbls_lead_sources']);
	$log->debug("_REQUEST['pbls_lead_sources'] is:");
	$log->debug($_REQUEST['pbls_lead_sources']);
	$log->debug("_SESSION['pbls_lead_sources'] is:");
	$log->debug($_SESSION['pbls_lead_sources']);
}

//set $datax using selected sales stage keys 
if (count($tempx) > 0) {
	foreach ($tempx as $key) {
		$datax[$key] = $comboFieldArray['lead_source_dom'][$key];
	}
}
else {
	$datax = $comboFieldArray['lead_source_dom'];
}
$log->debug("datax is:");
$log->debug($datax);

$ids = array();
//get list of user ids for which to display data
if (isset($_SESSION['pbls_ids']) && count($_SESSION['pbls_ids']) != 0 && !isset($_REQUEST['pbls_ids'])) {
	$ids = $_SESSION['pbls_ids'];
	$log->debug("_SESSION['pbls_ids'] is:");
	$log->debug($_SESSION['pbls_ids']);
}
elseif (isset($_REQUEST['pbls_ids']) && count($_REQUEST['pbls_ids']) > 0) {
	$ids = $_REQUEST['pbls_ids'];
	$current_user->setPreference('pbls_ids', $ids);
	$log->debug("_REQUEST['pbls_ids'] is:");
	$log->debug($_REQUEST['pbls_ids']);
	$log->debug("_SESSION['pbls_ids'] is:");
	$log->debug($_SESSION['pbls_ids']);
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

$cache_file_name = $id_hash."_pipeline_by_lead_source_".$current_language."_".crc32(implode('',$datax)).".png";
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


echo $draw_this->pipeline_by_lead_source($datax, $ids, $tmp_dir.$cache_file_name, $refresh,$width,$height);
echo "<P><font size='1'><em>".$current_module_strings['LBL_LEAD_SOURCE_FORM_DESC']."</em></font></P>";
if (isset($_REQUEST['pbls_edit']) && $_REQUEST['pbls_edit'] == 'true') {
?>
<form action="index.php" method="post" >
<input type="hidden" name="module" value="<?php echo $currentModule;?>">
<input type="hidden" name="action" value="<?php echo $action;?>">
<input type="hidden" name="display_view" value="<?php echo vtlib_purify($_REQUEST['display_view'])?>">
<input type="hidden" name="pbls_refresh" value="true">
<table cellpadding="2" border="0"><tbody>
<tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_LEAD_SOURCES'];?></td>
<td valign='top'><select name="pbls_lead_sources[]" multiple size='3'><?php echo get_select_options_with_id($comboFieldArray['lead_source_dom'],$_SESSION['pbls_lead_sources']); ?></select></td>
</tr><tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_USERS'];?></td>
<?php if($is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid('Potentials')] == 3 or $defaultOrgSharingPermission[getTabid('Potentials')] == 0)) { ?>
	<td valign='top'><select name="pbls_ids[]" multiple size='3'><?php echo get_select_options_with_id(get_user_array(FALSE, "Active", $current_user->id,'private'),$_SESSION['pbls_ids']); ?></select></td>
<?php } else { ?>
	<td valign='top'><select name="pbls_ids[]" multiple size='3'><?php echo get_select_options_with_id(get_user_array(false, "Active" ,$current_user->id),$_SESSION['pbls_ids']); ?></select></td>
<?php } ?>		
</tr><tr>
<td align="right"><br /> <input class="button" type="submit" title="<?php echo $app_strings['LBL_SELECT_BUTTON_TITLE']; ?>" accessKey="<?php echo $app_strings['LBL_SELECT_BUTTON_KEY']; ?>" value="<?php echo $app_strings['LBL_SELECT_BUTTON_LABEL']?>" /></td>
</tr></table>
</form>
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
</em>[<a href="javascript:void(0)" onClick="changeView('<?php echo vtlib_purify($_REQUEST['display_view']);?>');"><?php echo $current_module_strings['LBL_REFRESH'];?></a>]
[<a href="index.php?module=<?php echo $currentModule;?>&action=index&pbls_edit=true&display_view=<?php echo vtlib_purify($_REQUEST['display_view']);?>"><?php echo $current_module_strings['LBL_EDIT'];?></a>]
</FONT></div>
<?php } 
}
else
{
        echo $mod_strings['LBL_NO_PERMISSION'];
}
?>