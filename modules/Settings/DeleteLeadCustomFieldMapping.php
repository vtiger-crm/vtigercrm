<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/utils/utils.php');
global $adb;
$id=$_REQUEST['id'];

if($id !='') {
	$sql="update vtiger_convertleadmapping set accountfid ='NULL',contactfid='NULL',potentialfid='NULL' where cfmid=?";
	$result = $adb->pquery($sql, array($id));
}

header("Location: index.php?module=Settings&action=ListLeadCustomFieldMapping&parenttab=Settings");
?>