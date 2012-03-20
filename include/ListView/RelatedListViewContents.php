<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/

/**
 * @author MAK
 */

if($ajaxaction == "LOADRELATEDLIST") {
	global $relationId;
	$relationId =  vtlib_purify($_REQUEST['relation_id']);
	if(!empty($relationId) && ((int)$relationId) > 0) {
		$recordid =  vtlib_purify($_REQUEST['record']);
		$actions = vtlib_purify($_REQUEST['actions']);
		$header = vtlib_purify($_REQUEST['header']);
		$modObj->id = $recordid;
		$relationInfo = getRelatedListInfoById($relationId);
		$relatedModule = getTabModuleName($relationInfo['relatedTabId']);
		$function_name = $relationInfo['functionName'];

		$relatedListData = $modObj->$function_name($recordid, getTabid($currentModule),
				$relationInfo['relatedTabId'], $actions);
		// vtlib customization: Related module could be disabled, check it
		if(is_array($relatedListData)) {
			if( ($relatedModule == "Contacts" || $relatedModule == "Leads" ||
					$relatedModule == "Accounts") && $currentModule == 'Campaigns') {
				//TODO for 5.3 this should be COOKIE not REQUEST, change here else where
				// this logic is used for listview checkbox selection propogation.
				$checkedRecordIdString = $_REQUEST[$relatedModule.'_all'];
				$checkedRecordIdString = rtrim($checkedRecordIdString);
				$checkedRecordIdList = explode(';', $checkedRecordIdString);
				$relatedListData["checked"]=array();
				if (isset($relatedListData['entries'])) {
					foreach($relatedListData['entries'] as $key=>$val) {
						if(in_array($key,$checkedRecordIdList)) {
							$relatedListData["checked"][$key] = 'checked';
						} else {
							$relatedListData["checked"][$key] = '';
						}
					}
				}
			}
		}
		// END
		require_once('include/ListView/RelatedListViewSession.php');
		RelatedListViewSession::addRelatedModuleToSession($relationId,$header);

		require_once('Smarty_setup.php');
		global $theme, $mod_strings, $app_strings;
		$theme_path="themes/".$theme."/";
		$image_path=$theme_path."images/";

		$smarty = new vtigerCRM_Smarty;
		$smarty->assign("MOD", $mod_strings);
		$smarty->assign("APP", $app_strings);
		$smarty->assign("THEME", $theme);
		$smarty->assign("IMAGE_PATH", $image_path);
		$smarty->assign("ID",$recordid);
		$smarty->assign("MODULE",$currentModule);
		$smarty->assign("RELATED_MODULE",$relatedModule);
		$smarty->assign("HEADER",$header);
		$smarty->assign("RELATEDLISTDATA", $relatedListData);

		$smarty->display("RelatedListDataContents.tpl");
	}
}else if($ajaxaction == "DISABLEMODULE"){
	$relationId = vtlib_purify($_REQUEST['relation_id']);
	if(!empty($relationId) && ((int)$relationId) > 0) {
		$header = vtlib_purify($_REQUEST['header']);
		require_once('include/ListView/RelatedListViewSession.php');
		RelatedListViewSession::removeRelatedModuleFromSession($relationId,$header);
	}
	echo "SUCCESS";
}

?>
