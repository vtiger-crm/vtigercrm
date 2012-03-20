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
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

global $app_strings;
global $mod_strings;
global $currentModule;
global $current_language;
$current_module_strings = return_module_language($current_language, 'Portal');
global $adb;
	
$query="select * from vtiger_portal";
$result=$adb->pquery($query, array());
//Getting the Default URL Value if any
$default_url = $adb->query_result($result,0,'portalurl');
$no_of_portals=$adb->num_rows($result);
$portal_info=array();
//added as an enhancement to set default
?>
<script language="javascript" type="text/javascript">
var mysitesArray = new Array()
<?php
for($i=0 ; $i<$no_of_portals; $i++)
{
	$portalname = $adb->query_result($result,$i,'portalname');
	$portalurl = $adb->query_result($result,$i,'portalurl');
	//added as an enhancement to set default value
	$portalid = $adb->query_result($result,$i,'portalid');
	$set_default = $adb->query_result($result,$i,'setdefault');
	$portal_array['set_def'] =  $set_default;
	$portal_array['portalid'] =  $portalid;
	if($set_default == 1)
		{
			$def_ault = $portalurl;
		}
	$portal_array['portalname'] = (strlen($portalname) > 100) ? (substr($portalname,0,100).'...') : $portalname;	
	$portal_array['portalurl'] = $portalurl;
	$portal_array['portaldisplayurl'] = (strlen($portalurl) > 100) ? (substr($portalurl,0,100).'...') : $portalurl;
	$portal_info[]=$portal_array;
?>		
		mysitesArray['<?php echo $portalid;?>'] = "<?php echo $portalurl;?>";
<?php		
}
?>
</script>
<?php
if($def_ault == '')
	$def_ault = $adb->query_result($result,0,'portalurl');
$smarty = new vtigerCRM_Smarty;
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("MOD", $mod_strings);
$smarty->assign("DEFAULT_URL", $def_ault);
$smarty->assign("APP", $app_strings);
$smarty->assign("PORTAL_COUNT", count($portal_info));
$smarty->assign("PORTALS", $portal_info);
$smarty->assign("MODULE", $currentModule);
$smarty->assign("DEFAULT",'yes');
$smarty->assign("CATEGORY", getParentTab());
if($_REQUEST['datamode'] == 'data')
	$smarty->display("MySitesContents.tpl");
elseif($_REQUEST['datamode'] == 'manage')
	$smarty->display("MySitesManage.tpl");
else
	$smarty->display("MySites.tpl");
?>
