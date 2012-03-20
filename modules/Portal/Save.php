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

require_once('modules/Portal/Portal.php');

global $default_charset,$adb;
$conv_pname = function_exists(iconv) ? @iconv("UTF-8",$default_charset, $_REQUEST['portalname']) : $_REQUEST['portalname'];
$conv_purl = function_exists(iconv) ? @iconv("UTF-8",$default_charset, $_REQUEST['portalurl']) : $_REQUEST['portalurl'];
$portlurl =str_replace(array("'",'"'),'',$conv_purl);
$portlname = from_html($conv_pname);
$portlurl = from_html($portlurl);
//added as an enhancement to set default value
if(isset($_REQUEST['check']) && $_REQUEST['check'] =='true')
{
	$updateDefalt ="UPDATE vtiger_portal SET setdefault=1 WHERE portalid=?";
	$set_def = $adb->pquery($updateDefalt, array($_REQUEST['passing_var']));
	$updateZero = "UPDATE vtiger_portal SET setdefault=0 WHERE portalid not in(?)";
	$set_default= $adb->pquery($updateZero, array($_REQUEST['passing_var']));
	exit();
}	
if($portlname != '' && $portlurl != '')
{
	if(isset($_REQUEST['record']) && $_REQUEST['record'] !='')
	{
		$result=UpdatePortal($portlname,"http://".str_replace("#$#$#","&",$portlurl),$_REQUEST['record']);
	}
	else
	{
		$result=SavePortal($portlname,"http://".str_replace("#$#$#","&",$portlurl));
	}
	header("Location: index.php?action=PortalAjax&module=Portal&file=ListView&mode=ajax&datamode=manage");
}else
{
	echo ":#:FAILURE";
}
?>
