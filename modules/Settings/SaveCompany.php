<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once("include/database/PearDatabase.php");

$organization_name=$_REQUEST['organization_name'];
$org_name=$_REQUEST['org_name'];
$organization_address=$_REQUEST['organization_address'];
$organization_city=$_REQUEST['organization_city'];
$organization_state=$_REQUEST['organization_state'];
$organization_code=$_REQUEST['organization_code'];
$organization_country=$_REQUEST['organization_country'];
$organization_phone=$_REQUEST['organization_phone'];
$organization_fax=$_REQUEST['organization_fax'];
$organization_website=$_REQUEST['organization_website'];

$sql="select * from vtiger_organizationdetails where organizationname = ?";
$result = $adb->pquery($sql, array($org_name));
$org_name = $adb->query_result($result,0,'organizationname');

if($org_name=='')
{
	$sql="insert into vtiger_organizationdetails values(?,?,?,?,?,?,?,?,?)";
	$params = array($organization_name, $organization_address, $organization_city, $organization_state, $organization_code, $organization_country, $organization_phone, $organization_fax, $organization_website);
}
else
{
	$sql="update vtiger_organizationdetails set organizationname = ?, address = ?, city = ?, state = ?,  code = ?, country = ?,  phone = ?,  fax = ?,  website = ? where organizationname = ?";
	$params = array($organization_name, $organization_address, $organization_city, $organization_state, $organization_code, $organization_country, $organization_phone, $organization_fax, $organization_website, $org_name);
}	
$adb->pquery($sql, $params);

header("Location: index.php?module=Settings&action=OrganizationConfig");
?>