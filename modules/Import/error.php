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
 *Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('include/utils/utils.php');

/**	function used to show the error message occured during import process
 *	@param	string	$message - Error message to display in the screen, where the passed error message will be displayed in screen using Importerror.tpl file
 */
function show_error_import($message)
{
	global $import_mod_strings;

	global $theme;

	global $log;
	global $mod_strings;
	global $app_strings;

	$theme_path="themes/".$theme."/";

	$image_path=$theme_path."images/";

	$log->info("Upload Error");

	$smarty =  new vtigerCRM_Smarty;
	$smarty->assign("MOD", $mod_strings);
	$smarty->assign("APP", $app_strings);


	if (isset($_REQUEST['return_module'])) $smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));

	if (isset($_REQUEST['return_action'])) $smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));

	$smarty->assign("THEME", $theme);

	$category = getParenttab();
	$smarty->assign("CATEGORY", $category); 

	$smarty->assign("IMAGE_PATH", $image_path);
	$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);

	$smarty->assign("MODULE", vtlib_purify($_REQUEST['module']));
	$smarty->assign("MESSAGE", $message);

	$smarty->display('Importerror.tpl');
}

?>