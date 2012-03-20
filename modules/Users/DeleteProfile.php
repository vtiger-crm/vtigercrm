<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/utils/UserInfoUtil.php');
global $adb;
$del_id =  $_REQUEST['delete_prof_id'];
$tran_id = $_REQUEST['transfer_prof_id'];
//Deleting the Profile
deleteProfile($del_id,$tran_id);
header("Location: index.php?module=Settings&action=ListProfiles&parenttab=Settings");
?>