<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
/**
 * Created on 10-Oct-08
 * this file saves the notebook contents to database
 */
Zend_Json::$useBuiltinEncoderDecoder = true;
$status = SaveNotebookContents();
if($status == true) {
    require_once('include/home.php');
    $homeObj=new Homestuff;
    $contents = $homeObj->getNoteBookContents($_REQUEST['notebookid']);
}
$returnvalue = array('status' => $status, 'contents' => $contents);
echo Zend_Json::encode($returnvalue);

function SaveNotebookContents(){
	if(empty($_REQUEST['notebookid'])){
		return false;
	}else{
		$notebookid = $_REQUEST['notebookid'];
	}
	
	global $adb,$current_user;
	
	$contents = $_REQUEST['contents'];
	
	$sql = "update vtiger_notebook_contents set contents=? where userid=? and notebookid=?";
	$adb->pquery($sql, array($contents, $current_user->id, $notebookid));
	return true;
}
?>
