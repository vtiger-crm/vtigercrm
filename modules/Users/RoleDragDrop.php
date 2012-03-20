<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

require_once('include/utils/UserInfoUtil.php');
$toid=$_REQUEST['parentId'];
$fromid=$_REQUEST['childId'];


global $adb,$mod_strings;
$query = "select * from vtiger_role where roleid=?";
$result=$adb->pquery($query, array($toid));
$parentRoleList=$adb->query_result($result,0,'parentrole');
$replace_with=$parentRoleList;
$orgDepth=$adb->query_result($result,0,'depth');

//echo 'replace with is '.$replace_with;
//echo '<BR>org depth '.$orgDepth;
$parentRoles=explode('::',$parentRoleList);

if(in_array($fromid,$parentRoles))
{
	echo $mod_strings['ROLE_DRAG_ERR_MSG'];
        die;
}


$roleInfo=getRoleAndSubordinatesInformation($fromid);

$fromRoleInfo=$roleInfo[$fromid];
$replaceToStringArr=explode('::'.$fromid,$fromRoleInfo[1]);
$replaceToString=$replaceToStringArr[0];
//echo '<BR>to be replaced string '.$replaceToString;


$stdDepth=$fromRoleInfo['2'];
//echo '<BR> std depth '.$stdDepth;

//Constructing the query
foreach($roleInfo as $mvRoleId=>$mvRoleInfo)
{
	$subPar=explode($replaceToString,$mvRoleInfo[1],2);//we have to spilit as two elements only
	$mvParString=$replace_with.$subPar[1];
	$subDepth=$mvRoleInfo[2];
	$mvDepth=$orgDepth+(($subDepth-$stdDepth)+1);
	$query="update vtiger_role set parentrole=?,depth=? where roleid=?";
	//echo $query;
	$adb->pquery($query, array($mvParString, $mvDepth, $mvRoleId));

	// Invalidate any cached information
	VTCacheUtils::clearRoleSubordinates($roleId);
}



header("Location: index.php?action=SettingsAjax&module=Settings&file=listroles&ajax=true");
?>
