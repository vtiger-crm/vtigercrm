<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

if(isset($_REQUEST['file']) && ($_REQUEST['file'] !='')) {
	checkFileAccess('modules/Settings/'.$_REQUEST['file'].'.php');
	require_once('modules/Settings/'.$_REQUEST['file'].'.php');
}
if(isset($_REQUEST['orgajax']) && ($_REQUEST['orgajax'] !='')) {
	require_once('modules/Settings/CreateSharingRule.php');
} elseif(isset($_REQUEST['announce_save']) && ($_REQUEST['announce_save'] != '')) {
	$date_var = date('Y-m-d H:i:s');
	$announcement = vtlib_purify(from_html($_REQUEST['announcement']));
	//Change ##$## to & (reverse process has done in Smarty/templates/Settings/Announcements.tpl)
	$announcement = str_replace("##$##","&",$announcement);

    $title = vtlib_purify($_REQUEST['title_announcement']);
    $sql="select * from vtiger_announcement where creatorid=?";
    $is_announce=$adb->pquery($sql, array($current_user->id));
    if($adb->num_rows($is_announce) > 0) {
        $query="update vtiger_announcement set announcement=?,time=?,title=? where creatorid=?";
		$params = array($announcement, $adb->formatDate($date_var, true), 'announcement', $current_user->id);
	} else {
        $query="insert into vtiger_announcement values (?,?,?,?)";
		$params = array($current_user->id,$announcement,'announcement',$adb->formatDate($date_var, true));
	}
    $result=$adb->pquery($query, $params);
    echo $announcement;
}
?>