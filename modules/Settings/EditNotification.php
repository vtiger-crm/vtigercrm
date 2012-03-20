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

require_once('Smarty_setup.php');
global $mod_strings;
global $app_strings;
global $app_list_strings;

global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$smarty = new vtigerCRM_Smarty;
if(isset($_REQUEST['record']) && $_REQUEST['record']!='') 
{
//Added to show the previous selected value when editing
	$id = $_REQUEST['record'];

	$query='select vtiger_emailtemplates.templateid,vtiger_emailtemplates.templatename from vtiger_notificationscheduler inner join vtiger_emailtemplates on vtiger_emailtemplates.templateid=vtiger_notificationscheduler.notificationbody where schedulednotificationid=?';
	$result = $adb->pquery($query, array($id));
	$selected_item=$adb->query_result($result,0,'templateid');

	$sql="select * from vtiger_notificationscheduler where schedulednotificationid = ?";
	$result = $adb->pquery($sql, array($id));
	if($adb->num_rows($result) ==1);
	{
		$notification = Array();
		$notification['active'] = $adb->query_result($result,0,'active');
		$notification['subject'] = $adb->query_result($result,0,'notificationsubject');
		$notification['body'] = $adb->query_result($result,0,'notificationbody');
		$notification['name'] = $mod_strings[$adb->query_result($result,0,'label')];
		$notification['id'] = $adb->query_result($result,0,'schedulednotificationid');
		$notification['type'] = $adb->query_result($result,0,'type');
	}
	
	$smarty->assign("NOTIFY_DETAILS",$notification);
	//Added to show the previous selected value when editing. assigning previously selected id
	$smarty->assign("SEL_ID",$selected_item);
}

//Get all the email template name and id and send it to tpl to generate the combo box
    $query="select templateid,templatename from vtiger_emailtemplates";
    $result = $adb->pquery($query, array());

    $values = Array();
    for($i=0;$i < $adb->num_rows($result);$i++)
    {
	    $temp_id = $adb->query_result($result,$i,'templateid');	    
	    $temp_name = $adb->query_result($result,$i,'templatename');
	    $values[$temp_id]=$temp_name;	    
    }

$smarty->assign("VALUES",$values);



$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("CMOD", $mod_strings);
$smarty->display("Settings/EditEmailNotification.tpl");
?>
