<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once('Smarty_setup.php');
require_once('include/database/PearDatabase.php');
require_once 'include/utils/utils.php';
require_once 'modules/PickList/PickListUtils.php';
require_once 'modules/PickList/DependentPickListUtils.php';

global $app_strings, $app_list_strings, $current_language, $currentModule, $theme, $current_user;

if(!is_admin($current_user)) {
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
			<table border='0' cellpadding='5' cellspacing='0' width='98%'>
				<tr>
					<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) . "' ></td>
					<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
				</tr>
				<tr>
					<td class='small' align='right' nowrap='nowrap'>
						<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>
					</td>
				</tr>
			</table>
		</div>";
	echo "</td></tr></table>";
	die;
}

$modules = Vtiger_DependencyPicklist::getDependentPickListModules();
if(!empty($_REQUEST['moduleName'])) {
	$fld_module = vtlib_purify($_REQUEST['moduleName']);
}

$smarty = new vtigerCRM_Smarty;

if($fld_module == 'Events') {
	$temp_module_strings = return_module_language($current_language, 'Calendar');
}else {
	$temp_module_strings = return_module_language($current_language, $fld_module);
}

$smarty->assign("MODULE_LISTS",$modules);

$smarty->assign("APP", $app_strings);		//the include language files
$smarty->assign("MOD", return_module_language($current_language,'Settings'));	//the settings module language file
$smarty->assign("MOD_PICKLIST", return_module_language($current_language,'PickList'));	//the picklist module language files

$smarty->assign("MODULE",$fld_module);
$smarty->assign("PICKLIST_MODULE",'PickList');
$smarty->assign("THEME",$theme);

$smarty->assign("SUBMODE",vtlib_purify($_REQUEST['submode']));

if($_REQUEST['directmode'] == 'ajax') {
	$subMode = vtlib_purify($_REQUEST['submode']);

	if($subMode == 'getpicklistvalues') {
		$fieldName = vtlib_purify($_REQUEST['fieldname']);
		$fieldValues = getAllPickListValues($fieldName);
		$picklistValues = array();
		for($i=0;$i<count($fieldValues);++$i) {
			$picklistValues[$fieldValues[$i]] = getTranslatedString($fieldValues[$i], $fld_module);
		}
		$json = new Zend_Json();
		echo $json->encode($picklistValues);

	} elseif($subMode == 'editdependency') {
		$sourceField = vtlib_purify($_REQUEST['sourcefield']);
		$targetField = vtlib_purify($_REQUEST['targetfield']);

		$cyclicDependencyExists = Vtiger_DependencyPicklist::checkCyclicDependency($fld_module, $sourceField, $targetField);

		if($cyclicDependencyExists) {
			$smarty->assign('RETURN_URL', 'index.php?module=PickList&action=PickListDependencySetup&parenttab=Settings&moduleName='.$fld_module);
			$smarty->display("modules/PickList/PickListDependencyCyclicError.tpl");

		} else {
			$available_module_picklist = Vtiger_DependencyPicklist::getAvailablePicklists($fld_module);
			$smarty->assign("ALL_LISTS",$available_module_picklist);
			$dependencyMap = array();
			if(!empty($sourceField) && !empty($targetField)) {

				$sourceFieldValues = array();
				$targetFieldValues = getAllPickListValues($targetField);

				foreach (getAllPickListValues($sourceField) as $key => $value) {
					$sourceFieldValues[htmlentities($value,ENT_QUOTES,'UTF-8')] = $value;
				}

				$smarty->assign("SOURCE_VALUES", $sourceFieldValues);
				$smarty->assign("TARGET_VALUES", $targetFieldValues);

				$dependentPicklists = Vtiger_DependencyPicklist::getDependentPicklistFields($fld_module);
				$smarty->assign("DEPENDENT_PICKLISTS",$dependentPicklists);

				$dependencyMap = Vtiger_DependencyPicklist::getPickListDependency($fld_module, $sourceField, $targetField);
			}
			$smarty->assign("DEPENDENCY_MAP", $dependencyMap);

			$smarty->display("modules/PickList/PickListDependencyContents.tpl");
		}

	} else {

		if($subMode == 'savedependency') {
			$dependencyMapping = vtlib_purify($_REQUEST['dependencymapping']);
			$json = new Zend_Json();
			$dependencyMappingData = $json->decode($dependencyMapping);
			Vtiger_DependencyPicklist::savePickListDependencies($fld_module, $dependencyMappingData);

		} elseif($subMode == 'deletedependency') {
			$sourceField = vtlib_purify($_REQUEST['sourcefield']);
			$targetField = vtlib_purify($_REQUEST['targetfield']);
			Vtiger_DependencyPicklist::deletePickListDependencies($fld_module, $sourceField, $targetField);
		}
		$dependentPicklists = Vtiger_DependencyPicklist::getDependentPicklistFields($fld_module);
		$smarty->assign("DEPENDENT_PICKLISTS",$dependentPicklists);
		$smarty->display("modules/PickList/PickListDependencyList.tpl");
	}
} else {
	$dependentPicklists = Vtiger_DependencyPicklist::getDependentPicklistFields($fld_module);
	$smarty->assign("DEPENDENT_PICKLISTS",$dependentPicklists);
	$smarty->display("modules/PickList/PickListDependencySetup.tpl");
}

?>