<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once 'modules/Users/Users.php';

global $adb;
$del_id =  $_REQUEST['delete_user_id'];
$tran_id = $_REQUEST['transfer_user_id'];

$userObj = new Users();
$userObj->transformOwnerShipAndDelete($del_id, $tran_id);	

//if check to delete user from detail view
if(isset($_REQUEST["ajax_delete"]) && $_REQUEST["ajax_delete"] == 'false')
	header("Location: index.php?action=ListView&module=Users");
else
	header("Location: index.php?action=UsersAjax&module=Users&file=ListView&ajax=true");
?>