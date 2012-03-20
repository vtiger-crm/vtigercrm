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
require_once('Smarty_setup.php');
$smarty = new vtigerCRM_Smarty;

global $mod_strings;
global $app_strings;
global $app_list_strings;



global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";


//Retreiving the hierarchy
$hquery = "select * from vtiger_role order by parentrole asc";
$hr_res = $adb->pquery($hquery, array());
$num_rows = $adb->num_rows($hr_res);
$hrarray= Array();

for($l=0; $l<$num_rows; $l++)
{
	$roleid = $adb->query_result($hr_res,$l,'roleid');
	$parent = $adb->query_result($hr_res,$l,'parentrole');
	$temp_list = explode('::',$parent);
	$size = sizeof($temp_list);
	$i=0;
	$k= Array();
	$y=$hrarray;
	if(sizeof($hrarray) == 0)
	{
		$hrarray[$temp_list[0]]= Array();
	}
	else
	{
		while($i<$size-1)
		{
			$y=$y[$temp_list[$i]];
			$k[$temp_list[$i]] = $y;
			$i++;

		}
		$y[$roleid] = Array();
		$k[$roleid] = Array();

		//Reversing the Array
		$rev_temp_list=array_reverse($temp_list);
		$j=0;
		//Now adding this into the main array
		foreach($rev_temp_list as $value)
		{
			if($j == $size-1)
			{
				$hrarray[$value]=$k[$value];
			}
			else
			{
				$k[$rev_temp_list[$j+1]][$value]=$k[$value];
			}
			$j++;
		}
	}

}
//Constructing the Roledetails array
$role_det = getAllRoleDetails();
$query = "select * from vtiger_role";
$result = $adb->pquery($query, array());
$num_rows=$adb->num_rows($result);

$roleout ='';
$roleout .= indent($hrarray,$roleout,$role_det);

/** recursive function to construct the role tree ui 
  * @param $hrarray -- Hierarchial role tree array with only the roleid:: Type array
  * @param $roleout -- html string ouput of the constucted role tree ui:: Type varchar 
  * @param $role_det -- Roledetails array got from calling getAllRoleDetails():: Type array 
  * @returns $role_out -- html string ouput of the constucted role tree ui:: Type string
  *
 */

function indent($hrarray,$roleout,$role_det)
{
	global $theme,$mod_strings,$app_strings;
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	foreach($hrarray as $roleid => $value)
	{
	
		//retreiving the vtiger_role details
		$role_det_arr=$role_det[$roleid];
		$roleid_arr=$role_det_arr[2];
		$rolename = $role_det_arr[0];
		$roledepth = $role_det_arr[1]; 
		$roleout .= '<ul class="uil" id="'.$roleid.'" style="display:block;list-style-type:none;">';
		$roleout .=  '<li ><table border="0" cellpadding="0" cellspacing="0" onMouseOver="fnVisible(\'layer_'.$roleid.'\')" onMouseOut="fnInVisible(\'layer_'.$roleid.'\')">';
		$roleout.= '<tr><td nowrap>';
		if(sizeof($value) >0 && $roledepth != 0)
		{	
			$roleout.='<b style="font-weight:bold;margin:0;padding:0;cursor:pointer;">';
			$roleout .= '<img src="' . vtiger_imageurl('minus.gif', $theme) . '" id="img_'.$roleid.'" border="0"  alt="'.$app_strings['LBL_EXPAND_COLLAPSE'].'" title="'.$app_strings['LBL_EXPAND_COLLAPSE'].'" align="absmiddle" onClick="showhide(\''.$roleid_arr.'\',\'img_'.$roleid.'\')" style="cursor:pointer;">';
		}
		else if($roledepth != 0){
			$roleout .= '<img src="' . vtiger_imageurl('vtigerDevDocs.gif', $theme) . '" id="img_'.$roleid.'" border="0"  alt="'.$app_strings['LBL_EXPAND_COLLAPSE'].'" title="'.$app_strings['LBL_EXPAND_COLLAPSE'].'" align="absmiddle">';	
		}
		else{
			$roleout .= '<img src="' . vtiger_imageurl('menu_root.gif', $theme) . '" id="img_'.$roleid.'" border="0"  alt="'.$app_strings['LBL_ROOT'].'" title="'.$app_strings['LBL_ROOT'].'" align="absmiddle">';
		}	
		if($roledepth == 0 ){
			$roleout .= '&nbsp;<b class="genHeaderGray">'.$rolename.'</b></td>';
			$roleout .= '<td nowrap><div id="layer_'.$roleid.'" class="drag_Element"><a href="index.php?module=Settings&action=createrole&parenttab=Settings&parent='.$roleid.'"><img src="' . vtiger_imageurl('Rolesadd.gif', $theme) . '" align="absmiddle" border="0" alt="'.$mod_strings['LBL_ADD_ROLE'].'" title="'.$mod_strings['LBL_ADD_ROLE'].'"></a></div></td></tr></table>';
		}
		else{
			$roleout .= '&nbsp;<a href="javascript:put_child_ID(\'user_'.$roleid.'\');" class="x" id="user_'.$roleid.'">'.$rolename.'</a></td>';

			$roleout.='<td nowrap><div id="layer_'.$roleid.'" class="drag_Element">
													<a href="index.php?module=Settings&action=createrole&parenttab=Settings&parent='.$roleid.'"><img src="' . vtiger_imageurl('Rolesadd.gif', $theme) .'" align="absmiddle" border="0" alt="'.$mod_strings['LBL_ADD_ROLE'].'" title="'.$mod_strings['LBL_ADD_ROLE'].'"></a>
													<a href="index.php?module=Settings&action=createrole&roleid='.$roleid.'&parenttab=Settings&mode=edit"><img src="' . vtiger_imageurl('RolesEdit.gif', $theme) . '" align="absmiddle" border="0" alt="'.$mod_strings['LBL_EDIT_ROLE'].'" title="'.$mod_strings['LBL_EDIT_ROLE'].'"></a>';

			if($roleid != 'H1'  && $roleid != 'H2')
			{
							
				$roleout .=	'<a href="index.php?module=Settings&action=RoleDeleteStep1&roleid='.$roleid.'&parenttab=Settings"><img src="' . vtiger_imageurl('RolesDelete.gif', $theme) . '" align="absmiddle" border="0" alt="'.$mod_strings['LBL_DELETE_ROLE'].'" title="'.$mod_strings['LBL_DELETE_ROLE'].'"></a>';
			}		
													
		        $roleout .='<a href="javascript:;" class="small" onClick="get_parent_ID(this,\'user_'.$roleid.'\')"><img src="' . vtiger_imageurl('RolesMove.gif', $theme) . '" align="absmiddle" border="0" alt="'.$mod_strings['LBL_MOVE_ROLE'].'" title="'.$mod_strings['LBL_MOVE_ROLE'].'"></a>
												</div></td></tr></table>';
//			$roleout .=	'&nbsp;<a href="index.php?module=Users&action=createrole&parenttab=Settings&parent='.$roleid.'">Add</a> | <a href="index.php?module=Users&action=createrole&roleid='.$roleid.'&parenttab=Settings&mode=edit">Edit</a> | <a href="index.php?module=Users&action=RoleDeleteStep1&roleid='.$roleid.'&parenttab=Settings">Delete</a> | <a href="index.php?module=Users&action=RoleDetailView&parenttab=Settings&roleid='.$roleid.'">View</a>';		


		}
 		$roleout .=  '</li>';
		if(sizeof($value) > 0 )
		{
			$roleout = indent($value,$roleout,$role_det);
		}

		$roleout .=  '</ul>';

	}

	return $roleout;
}
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("ROLETREE", $roleout);

if($_REQUEST['ajax'] == 'true')
{
	$smarty->display("RoleTree.tpl");
}
else
{
	$smarty->display("ListRoles.tpl");
}
?>
