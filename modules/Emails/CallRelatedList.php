<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('include/utils/utils.php');

$currentmodule = vtlib_purify($_REQUEST['module']);
$focus = CRMEntity::getInstance($currentmodule);
$RECORD = vtlib_purify($_REQUEST['record']);
if(isset($_REQUEST['record']) && isset($_REQUEST['record'])) {
    $focus->retrieve_entity_info($RECORD,$currentmodule);
    $focus->id = $RECORD;
    $focus->name=$focus->column_fields['subject'];
	$log->debug("id is ".$focus->id);
	$log->debug("name is ".$focus->name);
}

global $mod_strings;
global $app_strings;
global $currentModule;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;

if (isset($focus->name)) $smarty->assign("NAME", $focus->name);
$related_array=getRelatedLists($currentModule,$focus);
$category = getParentTab();
$smarty->assign("CATEGORY",$category);
if(isset($_REQUEST['mode']) && $_REQUEST['mode'] != ' ') {
        $smarty->assign("OP_MODE",vtlib_purify($_REQUEST['mode']));
}
$smarty->assign("id",$focus->id);
$smarty->assign("RELATEDLISTS", $related_array);
		
require_once('include/ListView/RelatedListViewSession.php');
if(!empty($_REQUEST['selected_header']) && !empty($_REQUEST['relation_id'])) {
		$relationId = vtlib_purify($_REQUEST['relation_id']);
		RelatedListViewSession::addRelatedModuleToSession($relationId,
				vtlib_purify($_REQUEST['selected_header']));
	}
$open_related_modules = RelatedListViewSession::getRelatedModulesFromSession();
$smarty->assign("SELECTEDHEADERS", $open_related_modules);
	
$smarty->assign("ID",$RECORD );
$smarty->assign("MODULE",$currentmodule);
$smarty->assign("SINGLE_MOD",$app_strings['Email']);
$smarty->assign("UPDATEINFO",updateInfo($focus->id));
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

$smarty->display("RelatedLists.tpl");
?>