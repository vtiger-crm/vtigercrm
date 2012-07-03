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
require_once('Smarty_setup.php');
global $adb;

$deletSQL = "DELETE FROM vtiger_convertleadmapping WHERE editable=1";
$adb->pquery($deletSQL, array());
$insertSQL = "INSERT INTO vtiger_convertleadmapping(leadfid,accountfid,contactfid,potentialfid) VALUES(?,?,?,?)";
$map = vtlib_purify($_REQUEST['map']);
foreach ($map as $mapping) {
	if (!((empty($mapping['Accounts'])) && (empty($mapping['Contacts'])) && (empty($mapping['Potentials'])))) {

		$adb->pquery($insertSQL, array($mapping['Leads'], $mapping['Accounts'], $mapping['Contacts'], $mapping['Potentials']));
	}
}

header("Location: index.php?action=CustomFieldList&module=Settings&parenttab=Settings");
?>