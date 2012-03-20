<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/

global $current_user;
if($current_user->is_admin != 'on')
{
	die("<br><br><center>".$app_strings['LBL_PERMISSION']." <a href='javascript:window.history.back()'>".$app_strings['LBL_GO_BACK'].".</a></center>");
}

// Remove the MigrationStep0.tpl file from Smarty cache
$migration_tpl_file = get_smarty_compiled_file('MigrationStep0.tpl');
if ($migration_tpl_file != null) unlink($migration_tpl_file);

global $adb,$default_charset,$theme;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty();

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("MODULE","Migration");

$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

if($adb->isPostgres())
	$db_status='1';
else
	$db_status=check_db_utf8_support($adb);
	
$config_status=get_config_status();

$smarty->assign("DB_CHARSET", get_db_charset($adb));
$smarty->assign("DB_STATUS", $db_status);
$smarty->assign("CONFIG_CHARSET", $default_charset);
$smarty->assign("CONFIG_STATUS", $config_status);

$data_conversion_msg = array(
						/* php: utf8, d: utf8 */
						'1' => array('msg1'=> '', 'msg2' => '', 'checked' => 'true'), 
						'2' => array('msg1'=> "To have complete UTF-8 support:- <ol><li>Set \$default_charset='UTF-8'; in config.inc.php </li><li>Select the check box below for database charset handling and data conversion.</li></ol>", 
								'msg2' => 'To continue without UTF-8 support, keep the above option unchecked.', 'checked' => 'false'), 
						'3' => array('msg1'=> "To have complete UTF-8 support, we recommend you to set \$default_charset='UTF-8'; in config.inc.php.", 
									'msg2' => "Select the above check box after changing the config file, if you need UTF-8 data conversion (Unicode support).", 
									'checked' => 'false'), 
						'4' => array('msg1'=> "UTF-8 should be enabled for database to have complete unicode support. This will be handled in data conversion to UTF-8.", 
									'msg2' => "De-select the above check box, if you do not need UTF-8 data conversion (Unicode support will be inconsistent).", 
									'checked' => 'true'));
						
$db_migration_status = getMigrationCharsetFlag();

if ($db_migration_status == MIG_CHARSET_PHP_UTF8_DB_UTF8) {
	header("Location: index.php?module=Migration&action=index&parenttab=Settings");
}

$smarty->assign("CONVERSION_MSG", $data_conversion_msg[$db_migration_status]);

$smarty->display("MigrationStep0.tpl");

?>
