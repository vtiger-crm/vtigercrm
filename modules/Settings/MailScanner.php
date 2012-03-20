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

require_once('include/utils/utils.php');
require_once('Smarty_setup.php');

$mode = $_REQUEST['mode'];

if($mode == 'Ajax' && !empty($_REQUEST['xmode'])) {
	$mode = $_REQUEST['xmode'];
}

/** Based on the mode include the MailScanner file. */
if($mode == 'scannow') {
	include('vtigercron.php');
} else if($mode == 'edit') {
	include('modules/Settings/MailScanner/MailScannerEdit.php');
} else if($mode == 'save') {
	include('modules/Settings/MailScanner/MailScannerSave.php');

} else if($mode == 'remove') {
	include('modules/Settings/MailScanner/MailScannerRemove.php');
	
} else if($mode == 'rule') {
	include('modules/Settings/MailScanner/MailScannerRule.php');
} else if($mode == 'ruleedit') {
	include('modules/Settings/MailScanner/MailScannerRuleEdit.php');
} else if($mode == 'rulesave') {
	include('modules/Settings/MailScanner/MailScannerRuleSave.php');
} else if($mode == 'rulemove_up' || $mode == 'rulemove_down') {
	include('modules/Settings/MailScanner/MailScannerRuleMove.php');
} else if($mode == 'ruledelete') {
	include('modules/Settings/MailScanner/MailScannerRuleDelete.php');

} else if($mode == 'folder') {
	include('modules/Settings/MailScanner/MailScannerFolder.php');
} else if($mode == 'foldersave') {
	include('modules/Settings/MailScanner/MailScannerFolderSave.php');
} else if($mode == 'folderupdate') {
	include('modules/Settings/MailScanner/MailScannerFolderUpdate.php');
} else {
	include('modules/Settings/MailScanner/MailScannerInfo.php');
}

?>

