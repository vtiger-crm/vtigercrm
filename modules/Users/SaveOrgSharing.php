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
global $adb;

$sql2 = "select * from vtiger_def_org_share where editstatus=0";
$result2 = $adb->pquery($sql2, array());
$num_rows = $adb->num_rows($result2);

for($i=0; $i<$num_rows; $i++) {
	$ruleid=$adb->query_result($result2,$i,'ruleid');
	$tabid=$adb->query_result($result2,$i,'tabid');
	$reqval = $tabid.'_per';	
	$permission=$_REQUEST[$reqval];
	$sql7="update vtiger_def_org_share set permission=? where tabid=? and ruleid=?";
	$adb->pquery($sql7, array($permission, $tabid, $ruleid));

	if($tabid == 6) {
		$sql8="update vtiger_def_org_share set permission=? where tabid=4";
		$adb->pquery($sql8, array($permission));
	}

	if($tabid == 9) {
		$sql8="update vtiger_def_org_share set permission=? where tabid=16";
		$adb->pquery($sql8, array($permission));		
	}
}
$loc = "Location: index.php?action=OrgSharingDetailView&module=Settings&parenttab=Settings";
header($loc);
?>