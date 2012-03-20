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
//Constructing the Role Array
$roleDetails=getAllRoleDetails();
//Removing the Organisation role from the role array
unset($roleDetails['H1']);
$output='';

//Constructing the Group Array
$grpDetails=getAllGroupName();
$combovalues='';

global $mod_strings;
global $app_strings;
global $theme;
$theme_path="themes/".$theme."/";
global $adb;

$mode = vtlib_purify($_REQUEST['mode']);
if(isset($_REQUEST['shareid']) && $_REQUEST['shareid'] != '')
{	
	$shareid=vtlib_purify($_REQUEST['shareid']);
	$shareInfo=getSharingRuleInfo($shareid);
	$tabid=$shareInfo[1];
	$sharing_module=getTabModuleName($tabid);

}
else
{
	$sharing_module=vtlib_purify($_REQUEST['sharing_module']);
	$tabid=getTabid($sharing_module);
}

if($mode == 'create')
{
	foreach($roleDetails as $roleid=>$rolename)
	{
		$combovalues .='<option value="roles::'.$roleid.'">'.$mod_strings[LBL_ROLES].'::'.$rolename[0].'</option>';
	}

	foreach($roleDetails as $roleid=>$rolename)
	{
		$combovalues .='<option value="rs::'.$roleid.'">'.$mod_strings[LBL_ROLES_SUBORDINATES].'::'.$rolename[0].'</option>';
	}

	foreach($grpDetails as $groupid=>$groupname)
	{
		$combovalues .='<option value="groups::'.$groupid.'">'.$mod_strings[LBL_GROUP].'::'.$groupname.'</option>';
	}

	$fromComboValues=$combovalues;
	$toComboValues=$combovalues;

}
elseif($mode == 'edit')
{


	//constructing the from combo values
	$fromtype=$shareInfo[3];
	$fromid=$shareInfo[5];


	foreach($roleDetails as $roleid=>$rolename)
	{
		$selected='';

		if($fromtype == 'roles')
		{
			if($roleid == $fromid)
			{
				$selected='selected';	
			}	
		}
		$fromComboValues .='<option value="roles::'.$roleid.'" '.$selected.'>'.$mod_strings[LBL_ROLES].'::'.$rolename[0].'</option>';
	}

	foreach($roleDetails as $roleid=>$rolename)
	{

		$selected='';
		if($fromtype == 'rs')
		{
			if($roleid == $fromid)
			{
				$selected='selected';	
			}	
		}	
	
		$fromComboValues .='<option value="rs::'.$roleid.'" '.$selected.'>'.$mod_strings[LBL_ROLES_SUBORDINATES].'::'.$rolename[0].'</option>';
	}

	foreach($grpDetails as $groupid=>$groupname)
	{
		$selected='';
		if($fromtype == 'groups')
		{
			if($groupid == $fromid)
			{
				$selected='selected';	
			}	
		}	
		

		$fromComboValues .='<option value="groups::'.$groupid.'" '.$selected.'>'.$mod_strings[LBL_GROUP].'::'.$groupname.'</option>';
	}

	//constructing the to combo values
	$totype=$shareInfo[4];
	$toid=$shareInfo[6];


	foreach($roleDetails as $roleid=>$rolename)
	{
		$selected='';

		if($totype == 'roles')
		{
			if($roleid == $toid)
			{
				$selected='selected';	
			}	
		}
		$toComboValues .='<option value="roles::'.$roleid.'" '.$selected.'>'.$mod_strings[LBL_ROLES].'::'.$rolename[0].'</option>';
	}

	foreach($roleDetails as $roleid=>$rolename)
	{

		$selected='';
		if($totype == 'rs')
		{
			if($roleid == $toid)
			{
				$selected='selected';	
			}	
		}	
	
		$toComboValues .='<option value="rs::'.$roleid.'" '.$selected.'>'.$mod_strings[LBL_ROLES_SUBORDINATES].'::'.$rolename[0].'</option>';
	}

	foreach($grpDetails as $groupid=>$groupname)
	{
		$selected='';
		if($totype == 'groups')
		{
			if($groupid == $toid)
			{
				$selected='selected';	
			}	
		}	
		

		$toComboValues .='<option value="groups::'.$groupid.'" '.$selected.'>'.$mod_strings[LBL_GROUP].'::'.$groupname.'</option>';
	}

}



$relatedmodule='';	
$relatedlistscombo='';

if($mode == 'create')
{
	$sharPerCombo = '<option value="0" selected>'.$mod_strings["Read Only "].'</option>';
        $sharPerCombo .= '<option value="1">'.$mod_strings["Read/Write"].'</option>';
}
elseif($mode == 'edit')
{
	$selected1='';
	$selected2='';
	if($shareInfo[7] == 0)
	{
		$selected1='selected';
	}
	elseif($shareInfo[7] == 1)
	{
		$selected2='selected';
	}

	$sharPerCombo = '<option value="0" '.$selected1.'>'.$mod_strings["Read Only "].'</option>';
        $sharPerCombo .= '<option value="1" '.$selected2.'>'.$mod_strings["Read/Write"].'</option>';	
}

	
$output.='<div class="layerPopup" id="sharingRule"><form name="newGroupForm" action="index.php" method="post" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="module" value="Settings">
<input type="hidden" name="parenttab" value="Settings">	
<input type="hidden" name="action" value="SaveSharingRule">
<input type="hidden" name="sharing_module" value="'.$sharing_module.'">
<input type="hidden" name="shareId" value="'.$shareid.'">
<input type="hidden" name="mode" value="'.$mode.'">
<input type="hidden" id="rel_module_lists" name="rel_module_lists" value="'.$relatedmodule.'">
<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
<tr>';

if($sharing_module == 'Accounts')
{
	$display_module = $app_strings['Accounts'].' & '.$app_strings['Contacts'];	
}
else
{
	$display_module = $app_strings[$sharing_module];	
}
$output .= '<td class=layerPopupHeading " align="left">'.$display_module.' - ';
if($mode == 'edit')
    	$output .=$mod_strings[LBL_EDIT_CUSTOM_RULE].'</td>';
else
	$output .=$mod_strings[LBL_ADD_CUSTOM_RULE].'</td>';
$output .= '<td align="right" class="small"><img src="'. vtiger_imageurl('close.gif', $theme).'" border=0 alt="'.$app_strings["LBL_CLOSE"].'" title="'.$app_strings["LBL_CLOSE"].'" style="cursor:pointer" onClick="hide(\'sharingRule\')";></td>

</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
<tr>
	<td class="small">
	<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
	<tr>
		<td><b>'.$mod_strings[LBL_STEP].' 1 : '.$display_module.' '.$app_strings[LBL_LIST_OF].' </b>('.$mod_strings[LBL_SELECT_ENTITY].')</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td style="padding-left:20px;text-align:left;">';
//combovalues

		$output.='<select id="'.$app_strings[$sharing_module].'_share" name="'.$sharing_module.'_share" onChange="fnwriteRules(\''.$app_strings[$sharing_module].'\',\''.$relatedmodule.'\')";>'.$fromComboValues.'</select>';	
		$output.='</td>

		<td>&nbsp;</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>

		<td style="text-align:left;"><b>'.$mod_strings[LBL_STEP].' 2 : '.$mod_strings[LBL_CAN_BE_ACCESSED_BY].' </b>('.$mod_strings[LBL_SELECT_ENTITY].')</td>
		<td align="left"><b>'.$mod_strings[LBL_PERMISSIONS].'</b></td>
	</tr>
	<tr>
		<td style="padding-left:20px;text-align:left;">

		<select id="'.$app_strings[$sharing_module].'_access" name="'.$sharing_module.'_access" onChange="fnwriteRules(\''.$app_strings[$sharing_module].'\',\''.$relatedmodule.'\')";>';

		$output.=$toComboValues.'</select>

		</td>
		<td>

		<select	id="share_memberType" name="share_memberType" onChange="fnwriteRules(\''.$app_strings[$sharing_module].'\',\''.$relatedmodule.'\')";>';
		$output .= $sharPerCombo;
		$output .= '</select>

		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2" align="left">&nbsp;</td></tr>
	<tr>
		<td colspan="2" class="dvInnerHeader"><b>'.$mod_strings[LBL_RULE_CONSTRUCTION].'</b></td>

	</tr>
	<tr>
		<td  style="white-space:normal;" colspan="2" id="rules">&nbsp;
	</td>
	</tr>
	<tr>
		<td style="white-space:normal;" colspan="2" id="relrules">&nbsp;
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
	<tr>
		<td colspan="2" align="center">
		<input type="submit" class="crmButton small save" name="add" value="'.$mod_strings[LBL_ADD_RULE].'">&nbsp;&nbsp;
	</td>
	</tr>
</table>';

$output.='</form></div>';
echo $output;
?>