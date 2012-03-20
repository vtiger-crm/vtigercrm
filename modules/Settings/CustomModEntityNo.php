<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/utils/utils.php');
require_once('Smarty_setup.php');

global $app_strings;
global $mod_strings;
global $currentModule;
global $current_language;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$recprefix = vtlib_purify($_REQUEST['recprefix']);
$mode = $_REQUEST['mode'];
$validInput = validateAlphaNumericInput($recprefix);
if(!empty($recprefix) && ! $validInput) {
    $recprefix = '';
    $mode='';
    $STATUSMSG = "<font color='red'>".$mod_strings['LBL_UPDATE']." ".$mod_strings['LBL_FAILED']."</font>";
}
$recnumber = vtlib_purify($_REQUEST['recnumber']);

$module_array=getCRMSupportedModules();
if(count($module_array) <= 0) {
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src= " . vtiger_imageurl('denied.gif', $theme) . " ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
			<span class='genHeaderSmall'>$app_strings[LBL_NO_MODULES_TO_SELECT]</span></td>
		</tr>
		</tbody></table>
		</div>";
	echo "</td></tr></table>";
	exit;	
}

$modulesList = array_keys($module_array);

$selectedModule = vtlib_purify($_REQUEST['selmodule']);
if($selectedModule == '') $selectedModule = $modulesList[0];

if(in_array($selectedModule, $module_array)) {
	$focus = CRMEntity::getInstance($selectedModule);
}
if($mode == 'UPDATESETTINGS') {
	if(isset($focus)) {

		$status = $focus->setModuleSeqNumber('configure', $selectedModule, $recprefix, $recnumber);
		if($status === false) {
			$STATUSMSG = "<font color='red'>".$mod_strings['LBL_UPDATE']." ".$mod_strings['LBL_FAILED']."</font> $recprefix$recnum ".$mod_strings['LBL_IN_USE'];
		} else {
			$STATUSMSG = "<font color='green'>".$mod_strings['LBL_UPDATE']." ".$mod_strings['LBL_DONE']."</font>";
		}
	}
} else if($mode == 'UPDATEBULKEXISTING') {
	if(isset($focus)) {
		$resultinfo = $focus->updateMissingSeqNumber($selectedModule);
		
		if(!empty($resultinfo)) {
			$usefontcolor = 'green';
			if($resultinfo['totalrecords'] != $resultinfo['updatedrecords']) $usefontcolor = 'red';
			
			$STATUSMSG = "<font color='$usefontcolor'>" . 
				$mod_strings['LBL_TOTAL'] . $resultinfo['totalrecords'] . ", " . 
				$mod_strings['LBL_UPDATE'] . ' ' . $mod_strings['LBL_DONE'] . ':' . $resultinfo['updatedrecords'] . 
				"</font>";
		}		
		$seqinfo = $focus->getModuleSeqInfo($selectedModule);
		$recprefix = $seqinfo[0];
		$recnumber = $seqinfo[1];
	}
} else {
	if(isset($focus)) {
		$seqinfo = $focus->getModuleSeqInfo($selectedModule);
		$recprefix = $seqinfo[0];
		$recnumber = $seqinfo[1];
	}
}

$smarty = new vtigerCRM_Smarty;

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);

$smarty->assign("MODULES",$module_array);
$smarty->assign("SELMODULE",$selectedModule);
$smarty->assign("MODNUM_PREFIX",$recprefix);
$smarty->assign("MODNUM", $recnumber);
$smarty->assign("STATUSMSG", $STATUSMSG);

if($_REQUEST['ajax'] == 'true') $smarty->display('Settings/CustomModEntityNoInfo.tpl');
else $smarty->display('Settings/CustomModEntityNo.tpl');

function getCRMSupportedModules()
{
	global $adb;
	$sql="select tabid,name from vtiger_tab where isentitytype = 1 and presence = 0 and tabid in(select distinct tabid from vtiger_field where uitype='4')";
	$result = $adb->query($sql);
	while($moduleinfo=$adb->fetch_array($result))
	{
		$modulelist[$moduleinfo['name']] = $moduleinfo['name'];
	}
	return $modulelist;
}

?>