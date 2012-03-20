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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Users/EditView.php,v 1.16 2005/04/19 14:44:02 ray Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('modules/Users/Users.php');
require_once('include/utils/UserInfoUtil.php');
require_once('modules/Users/Forms.php');
require_once('include/database/PearDatabase.php');
require_once('modules/Calendar/OpenListView.php');
require_once('modules/Leads/ListViewTop.php');

global $app_strings;
global $app_list_strings;
global $mod_strings;
global $currentModule,$default_charset;


$smarty=new vtigerCRM_Smarty;
$focus = new Users();

if(isset($_REQUEST['record']) && isset($_REQUEST['record'])) {
	$smarty->assign("ID",vtlib_purify($_REQUEST['record']));
	$mode='edit';
	if (!is_admin($current_user) && $_REQUEST['record'] != $current_user->id) die ("Unauthorized access to user administration.");
    $focus->retrieve_entity_info($_REQUEST['record'],'Users');
	$smarty->assign("USERNAME",$focus->last_name.' '.$focus->first_name);
}else
{
	$mode='create';
}

if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = "";
	$focus->user_name = "";
	$mode='create';

	//When duplicating the user the password fields should be empty
	$focus->column_fields['user_password']='';
	$focus->column_fields['confirm_password']='';
}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$log->info("User edit view");


$smarty->assign("JAVASCRIPT", get_validate_record_js());
$smarty->assign("UMOD", $mod_strings);
global $current_language;
$smod_strings = return_module_language($current_language,'Settings');
$smarty->assign("MOD", $smod_strings);
$smarty->assign("CURRENT_USERID", $current_user->id);
$smarty->assign("APP", $app_strings);

if (isset($_REQUEST['error_string'])) $smarty->assign("ERROR_STRING", "<font class='error'>Error: ".vtlib_purify($_REQUEST['error_string'])."</font>");
if (isset($_REQUEST['return_module']))
{
        $smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
        $RETURN_MODULE=vtlib_purify($_REQUEST['return_module']);
}
if (isset($_REQUEST['return_action']))
{
        $smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));
        $RETURN_ACTION = vtlib_purify($_REQUEST['return_action']);
}
if ($_REQUEST['isDuplicate'] != 'true' && isset($_REQUEST['return_id']))
{
        $smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));
        $RETURN_ID = vtlib_purify($_REQUEST['return_id']);
}
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
$focus->mode = $mode;
$disp_view = getView($focus->mode);
$smarty->assign("IMAGENAME",$focus->imagename);
$smarty->assign("BLOCKS",getBlocks($currentModule,$disp_view,$mode,$focus->column_fields));	
$smarty->assign("MODULE", 'Settings');
$smarty->assign("MODE",$focus->mode);
$smarty->assign("HOUR_FORMAT",$focus->hour_format);
$smarty->assign("START_HOUR",$focus->start_hour);
if ($_REQUEST['Edit'] == ' Edit ')
{
	$smarty->assign("READONLY", "readonly");
	$smarty->assign("USERNAME_READONLY", "readonly");
	
}	
if(isset($_REQUEST['record']) && $_REQUEST['isDuplicate'] != 'true')
{
	$smarty->assign("USERNAME_READONLY", "readonly");
}

$smarty->assign("HOMEORDER",$focus->getHomeStuffOrder($focus->id));
//Added to provide User based Tagcloud
if($mode == 'create') $smarty->assign("TAGCLOUDVIEW","true"); // While creating user select tag cloud by default 
else $smarty->assign("TAGCLOUDVIEW",getTagCloudView($focus->id));

$smarty->assign("DUPLICATE",vtlib_purify($_REQUEST['isDuplicate']));
$smarty->assign("USER_MODE",$mode);
$smarty->assign('PARENTTAB', getParentTab());
$_SESSION['Users_FORM_TOKEN'] = rand(5, 2000) * rand(2, 7);
$smarty->assign('FORM_TOKEN', $_SESSION['Users_FORM_TOKEN']);

$smarty->display('UserEditView.tpl');

?>