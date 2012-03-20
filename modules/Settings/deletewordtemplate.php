<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

$idlist = $_REQUEST['idlist'];
$id_array=explode(';', $idlist);

for($i=0; $i < count($id_array)-1; $i++) {
	$sql = "delete from vtiger_wordtemplates where templateid=?";
	$adb->pquery($sql, array($id_array[$i]));
}

header("Location:index.php?module=Settings&action=listwordtemplates&parenttab=Settings");

?>