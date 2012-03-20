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

global $app_strings;
global $mod_strings;
global $theme,$default_charset;
$theme_path="themes/".$theme."/";
$delete_group_id = vtlib_purify($_REQUEST['groupid']);
$delete_group_name = fetchGroupName($delete_group_id);


$output='';
$output ='<div id="DeleteLay" class="layerPopup" style="width:400px;">
<form name="deleteGroupForm" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="module" value="Users">
<input type="hidden" name="action" value="DeleteGroup">
<input type="hidden" name="delete_group_id" value="'.$delete_group_id.'">	
<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
<tr>
	<td class=layerPopupHeading " align="left">'.$mod_strings['LBL_DELETE_GROUP'].'</td>
	<td align="right" class="small"><img src="'. vtiger_imageurl('close.gif', $theme) .'" border=0 alt="'.$app_strings["LBL_CLOSE"].'" title="'.$app_strings["LBL_CLOSE"].'" style="cursor:pointer" onClick="document.getElementById(\'DeleteLay\').style.display=\'none\'";></td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
<tr>
	<td class="small">
	<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
	<tr>
		<td width="50%" class="cellLabel small"><b>'.$mod_strings['LBL_DELETE_GROUPNAME'].'</b></td>
		<td width="50%" class="cellText small"><b>'.htmlentities($delete_group_name,ENT_QUOTES,$default_charset).'</b></td>
	</tr>
	<tr>
		<td align="left" class="cellLabel small" nowrap><b>'.$mod_strings['LBL_TRANSFER_GROUP'].'</b></td>
		<td align="left" class="cellText small">';
		global $adb;	
		$sql = "select groupid,groupname from vtiger_groups";
		$result = $adb->pquery($sql, array());
		$num_groups = $adb->num_rows($result);
	
		$sql1 = "select id,user_name from vtiger_users where deleted=0";
		$result1= $adb->pquery($sql1, array());
		$num_users = $adb->num_rows($result1);
	

		$output.= '<input name="assigntype" checked value="U" onclick="toggleAssignType(this.value)" type="radio">&nbsp;User';
		if($num_groups > 1)
		{
			$output .= '<input name="assigntype"  value="T" onclick="toggleAssignType(this.value)" type="radio">&nbsp;Group';
		}	
	
		$output .= '<span id="assign_user" style="display: block;">';

		$output .= '<select class="select" name="transfer_user_id">';
	

		for($i=0;$i<$num_users;$i++)
		{
			$user_name=$adb->query_result($result1,$i,"user_name");
			$user_id=$adb->query_result($result1,$i,"id");
			
			if(strlen($user_name)>20)
			{
				$user_name=substr($user_name,0,20)."...";
			}
								
	    		$output.='<option value="'.$user_id.'">'.$user_name.'</option>';
		}	
	
		$output .='</select></span>';

		if($num_groups > 1)
		{	
			$output .= '<span id="assign_team" style="display: none;">';
	

			$output.='<select class="select" name="transfer_group_id">';
	
			$temprow = $adb->fetch_array($result);
			do
			{
				$group_name= htmlentities($temprow["groupname"],ENT_QUOTES,$default_charset);
				$group_id=$temprow["groupid"];
				if($delete_group_id 	!= $group_id)
				{
					if(strlen($group_name)>20)
					{
						$group_name=substr($group_name,0,20)."...";
					}
    					$output.='<option value="'.$group_id.'">'.$group_name.'</option>';
	    			}	
			}while($temprow = $adb->fetch_array($result));
			$output.='</select></span>';
		}	

		$output.='</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
<tr>
	<td class="small" align="center"><input type="submit" name="Delete" value="'.$app_strings["LBL_SAVE_BUTTON_LABEL"].'" class="crmbutton small save">
	</td>
</tr>
</table>
</form></div>';

echo $output;
?>
