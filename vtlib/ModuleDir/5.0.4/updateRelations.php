<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('include/database/PearDatabase.php');
@include_once('user_privileges/default_module_view.php');

global $adb, $singlepane_view, $currentModule;
$idlist            = $_REQUEST['idlist'];
$destinationModule = $_REQUEST['destination_module'];
$parenttab         = $_REQUEST['parenttab'];

$forCRMRecord = $_REQUEST['parentid'];
$mode = $_REQUEST['mode'];

// Split the string of ids
if($mode == 'delete') {
	$ids = explode (";",trim($idlist,";"));
	if(function_exists('checkFileAccess')) {
		checkFileAccess("modules/$currentModule/$currentModule.php");
	}
	require_once("modules/$currentModule/$currentModule.php");
	$focus = new $currentModule();
	if(method_exists($focus, 'delete_related_module')) {
		$focus->delete_related_module($currentModule, $forCRMRecord, $destinationModule, $ids);
	}
	if($singlepane_view == 'true') {
		header("Location: index.php?module=$currentModule&record=$forCRMRecord&action=DetailView&parenttab=$parenttab");
	} else {
		header("Location: index.php?module=$currentModule&record=$forCRMRecord&action=CallRelatedList&parenttab=$parenttab");
	}
	exit;
}

if(!empty($_REQUEST['idlist'])) {
	$ids = explode (";",trim($idlist,";"));
	if(function_exists('checkFileAccess')) {
		checkFileAccess("modules/$currentModule/$currentModule.php");
	}
	require_once("modules/$currentModule/$currentModule.php");
	$focus = new $currentModule();
	if(method_exists($focus, 'save_related_module')) {
		$focus->save_related_module($currentModule, $forCRMRecord, $destinationModule, $ids);
	}
	if($singlepane_view == 'true') {
		header("Location: index.php?module=$currentModule&record=$forCRMRecord&action=DetailView&parenttab=$parenttab");
	} else {
		header("Location: index.php?module=$currentModule&record=$forCRMRecord&action=CallRelatedList&parenttab=$parenttab");
	}
} else if(!empty($_REQUEST['entityid'])){
	// TODO: Handle this case
}
?>
