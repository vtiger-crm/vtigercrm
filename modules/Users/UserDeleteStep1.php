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

require_once('include/utils/utils.php');

global $mod_strings, $app_strings;
global $theme;
$theme_path="themes/".$theme."/";

$delete_user_id = $_REQUEST['record'];
$delete_user_name = getUserName($delete_user_id);


$output='';
$output ='<div id="DeleteLay" class="layerPopup">
<form name="newProfileForm" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="module" value="Users">
<input type="hidden" name="action" value="DeleteUser">
<input type="hidden" name="delete_user_id" value="'.$delete_user_id.'">	
<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
<tr>
	<td class=layerPopupHeading " align="left">'.$mod_strings['LBL_DELETE'].' '.$mod_strings['LBL_USER'].'</td>
	<td align="right" class="small"><img src="'. vtiger_imageurl('close.gif', $theme) .'" border=0 alt="'.$app_strings["LBL_CLOSE"].'" title="'.$app_strings["LBL_CLOSE"].'" style="cursor:pointer" onClick="document.getElementById(\'DeleteLay\').style.display=\'none\'";></td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
<tr>	
	<td class="small">
	<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
	<tr>
	
		<td width="50%" class="cellLabel small"><b>'.$mod_strings['LBL_DELETE_USER'].'</b></td>
		<td width="50%" class="cellText small"><b>'.$delete_user_name.'</b></td>
	</tr>
	<tr>
		<td align="left" class="cellLabel small" nowrap><b>'.$mod_strings['LBL_TRANSFER_USER'].'</b></td>
		<td align="left" class="cellText small">';
           
		$output.='<select class="select" name="transfer_user_id" id="transfer_user_id">';
	     
		global $adb;	
         	$sql = "select * from vtiger_users";
	        $result = $adb->pquery($sql, array());
         	$temprow = $adb->fetch_array($result);
         	do
         	{
         		$user_name=$temprow["user_name"];
			$user_id=$temprow["id"];
		    	if($delete_user_id 	!= $user_id)
		    	{	 
            			$output.='<option value="'.$user_id.'">'.$user_name.'</option>';
		    	}	
         	}while($temprow = $adb->fetch_array($result));

		$output.='</td>
	</tr>
	
	</table>
	</td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
<tr>
	<td align=center class="small"><input type="button" onclick="transferUser('.$delete_user_id.')" name="Delete" value="'.$app_strings["LBL_SAVE_BUTTON_LABEL"].'" class="small">
	</td>
</tr>
</table>
</form></div>';

echo $output;
?>
