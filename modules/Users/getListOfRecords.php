<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
session_start();
require_once('include/CustomFieldUtil.php');
require_once('Smarty_setup.php');
require_once('include/database/PearDatabase.php');
require_once 'include/utils/ListViewUtils.php';
require_once('modules/CustomView/CustomView.php');

global $mod_strings,$app_strings,$app_list_strings,$theme,$adb,$current_user;
global $list_max_entries_per_page;

$theme_path="themes/".$theme."/";

require_once('modules/Vtiger/layout_utils.php');

$iCurRecord = vtlib_purify($_REQUEST['CurRecordId']);
$sModule = vtlib_purify($_REQUEST['CurModule']);

require_once('data/CRMEntity.php');
$foc_obj = CRMEntity::getInstance($sModule);

$query = $adb->pquery("SELECT tablename,entityidfield, fieldname from vtiger_entityname WHERE modulename = ?",array($sModule));
$table_name = $adb->query_result($query,0,'tablename');
$field_name = $adb->query_result($query,0,'fieldname');
$id_field = $adb->query_result($query,0,'entityidfield');
$fieldname = split(",",$field_name);
$fields_array = array($sModule=>$fieldname);
$id_array = array($sModule=>$id_field);
$tables_array = array($sModule=>$table_name);

$permittedFieldNameList = array();
foreach ($fieldname as $fieldName) {
	$checkForFieldAccess = $fieldName;
	// Handling case where fieldname in vtiger_entityname mismatches fieldname in vtiger_field
	if($sModule == 'HelpDesk' && $checkForFieldAccess == 'title') {
		$checkForFieldAccess = 'ticket_title';	
	} else if($sModule == 'Documents' && $checkForFieldAccess == 'title') {
		$checkForFieldAccess = 'notes_title';	
	}
	// END
	if(getFieldVisibilityPermission($sModule,$current_user->id, $checkForFieldAccess) == '0'){
		$permittedFieldNameList[] = $fieldName;
	}
}

$cv = new CustomView();
$viewId = $cv->getViewId($sModule);
if(!empty($_SESSION[$sModule.'_DetailView_Navigation'.$viewId])){
	$recordNavigationInfo = Zend_Json::decode($_SESSION[$sModule.'_DetailView_Navigation'.$viewId]);
	$recordList = array();
	$recordIndex = null;
	$recordPageMapping = array();
	foreach ($recordNavigationInfo as $start=>$recordIdList){
		foreach ($recordIdList as $index=>$recordId) {
			if(!isRecordExists($recordId)) continue;
			$recordList[] = $recordId;
			$recordPageMapping[$recordId] = $start;
			if($recordId == $iCurRecord){
				$recordIndex = count($recordList)-1;
			}
		}
	}
}else{
	$recordList = array();
}
$output = '<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
			<tr><td width="60%" align="left" style="font-size:12px;font-weight:bold;">Jump to '.$app_strings[$sModule].':</td>
			<td width="5%" align="right"><a href="javascript:fninvsh(\'lstRecordLayout\');"><img src="'. vtiger_imageurl('close.gif', $theme).'" border="0"  align="absmiddle" /></a></td>
			</tr>
			</table><table border=0 cellspacing=0 cellpadding=0 width=100% align=center> 
							<tr>
								<td class=small >
									<table border=0 celspacing=0 cellpadding=0 width=100% align=center >
										<tr><td>';
$output .= '<div style="height:270px;overflow-y:auto;">';
$output .= '<table cellpadding="2">';

if(count($recordList) > 0){
	$displayRecordCount = 10;
	$count = count($recordList);
	$idListEndIndex = ($count < ($recordIndex+$displayRecordCount))? ($count+1) : ($recordIndex+$displayRecordCount+1);
	$idListStartIndex = $recordIndex-$displayRecordCount;
	if($idListStartIndex < 0){
		$idListStartIndex = 0;
	}
	$idsArray = array_slice($recordList,$idListStartIndex,($idListEndIndex - $idListStartIndex));
	
	$selectColString = implode(',',$permittedFieldNameList).', '.$id_array[$sModule];
	$fieldQuery = "SELECT $selectColString from ".$tables_array[$sModule]." WHERE ".$id_array[$sModule]." IN (". generateQuestionMarks($idsArray) .")";
	
	$fieldResult = $adb->pquery($fieldQuery,$idsArray);
	$numOfRows = $adb->num_rows($fieldResult);
	$recordNameMapping = array();
	for($i=0; $i<$numOfRows; ++$i) {
		$recordId = $adb->query_result($fieldResult,$i,$id_array[$sModule]);
		$fieldValue = '';
		foreach ($permittedFieldNameList as $fieldName) {
			$fieldValue .= " ".$adb->query_result($fieldResult,$i,$fieldName);
		}
		$fieldValue = textlength_check($fieldValue);
		$recordNameMapping[$recordId] = $fieldValue;
	}
	foreach ($idsArray as $id) {
		if($id===$iCurRecord){
			$output .= '<tr><td style="text-align:left;font-weight:bold;">'.$recordNameMapping[$id].'</td></tr>';
		}else{
			$output .= '<tr><td style="text-align:left;"><a href="index.php?module='.$sModule.
				'&action=DetailView&parenttab='.vtlib_purify($_REQUEST['CurParentTab']).'&record='.$id.
				'&start='.$recordPageMapping[$id].'">'.$recordNameMapping[$id].'</a></td></tr>';
		}
	}
}
$output .= '</table>';
$output .= '</div></td></tr></table></td></tr></table>';
	
echo $output;
?>