<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/database/PearDatabase.php');	
$idlist = vtlib_purify($_POST['idlist']);
$returnmodule=vtlib_purify($_REQUEST['return_module']);
$returnaction=vtlib_purify($_REQUEST['return_action']);
//split the string and store in an array
$storearray = explode(";",$idlist);
foreach($storearray as $id)
{
        $sql="delete from vtiger_sharedcalendar where sharedid=?";
        $result = $adb->pquery($sql, array($id));
}
header("Location:index.php?module=".$returnmodule."&action=".$returnaction);
?>