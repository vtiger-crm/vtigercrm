<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

global $adb;

if($_REQUEST['idstring'] != '')
	$idlist = $_REQUEST['idstring'];
elseif($_REQUEST['idlist'] != '')
	$idlist = $_REQUEST['idlist'];

$selected_array = explode(";",$idlist);
foreach($selected_array as $account_id) {
	if($account_id != '') 	{
		$query = "update vtiger_mail_accounts set status=0 where account_id=?";
		$adb->pquery($query, array($account_id));
	}
}

header("Location:index.php?module=Settings&action=ListMailAccount");

?>