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

$sharing_module=$_REQUEST['sharing_module'];
$tabid=getTabid($sharing_module);
$sharedby = explode('::',$_REQUEST[$sharing_module.'_share']);
$sharedto = explode('::',$_REQUEST[$sharing_module.'_access']);
$share_entity_type = $sharedby[0];
$to_entity_type = $sharedto[0];

$share_entity_id= $sharedby[1];
$to_entity_id=$sharedto[1];

$module_sharing_access=$_REQUEST['share_memberType'];

$mode=$_REQUEST['mode'];

$relatedShareModuleArr=getRelatedSharingModules($tabid);
if($mode == 'create')
{
	$shareId=addSharingRule($tabid,$share_entity_type,$to_entity_type,$share_entity_id,$to_entity_id,$module_sharing_access);

	//Adding the Related ModulePermission Sharing
	foreach($relatedShareModuleArr as $reltabid=>$ds_rm_id)
	{
		$reltabname=getTabModuleName($reltabid);
		$relSharePermission=$_REQUEST[$reltabname.'_accessopt'];	
		addRelatedModuleSharingPermission($shareId,$tabid,$reltabid,$relSharePermission);	
	}
	
}
elseif($mode == 'edit')
{
	$shareId=$_REQUEST['shareId'];
	updateSharingRule($shareId,$tabid,$share_entity_type,$to_entity_type,$share_entity_id,$to_entity_id,$module_sharing_access);
	//Adding the Related ModulePermission Sharing
	foreach($relatedShareModuleArr as $reltabid=>$ds_rm_id)
	{
		$reltabname=getTabModuleName($reltabid);
		$relSharePermission=$_REQUEST[$reltabname.'_accessopt'];	
		updateRelatedModuleSharingPermission($shareId,$tabid,$reltabid,$relSharePermission);	
	}	
}

$loc = "Location: index.php?action=OrgSharingDetailView&module=Settings&parenttab=Settings";
header($loc);
?>