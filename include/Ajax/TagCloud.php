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
$ajaxaction = $_REQUEST["ajxaction"];
global $current_user;
global $default_charset;

$crmid = vtlib_purify($_REQUEST["recordid"]);
$module = vtlib_purify($_REQUEST["module"]);
$userid = $current_user->id;
if($ajaxaction == "SAVETAG")
{
	
	require_once('include/freetag/freetag.class.php');
	$tagfields=function_exists(iconv) ? @iconv("UTF-8",$default_charset,$_REQUEST['tagfields']) : $_REQUEST['tagfields'];
	$tagfields =str_replace(array("'",'"'),'',$tagfields);
	if($tagfields != "")
	{
    		$freetag = new freetag();
		if (isset($_REQUEST["tagfields"]) && trim($_REQUEST["tagfields"]) != "")
		{
			$freetag->tag_object($userid,$crmid,$tagfields,$module);
			$tagcloud = $freetag->get_tag_cloud_html($module,$userid,$crmid);
			echo $tagcloud;
		}
	}
	else
	{
		echo ":#:FAILURE";
	}
}
elseif($ajaxaction == 'GETTAGCLOUD')
{
	require_once('include/freetag/freetag.class.php');
	$freetag = new freetag();
	if(trim($module) != "")
	{
		$tagcloud = $freetag->get_tag_cloud_html($module,$userid,$crmid);
		echo $tagcloud;
	}else
	{
		$tagcloud = $freetag->get_tag_cloud_html("",$userid);
		echo $tagcloud;
	}
}elseif($ajaxaction == 'DELETETAG')
{
	if(is_numeric($_REQUEST['tagid']))
	{
		$tagid = $_REQUEST['tagid']; 
		global $adb;
		$query="delete from vtiger_freetagged_objects where tag_id=? and object_id=?";
		$result=$adb->pquery($query, array($tagid, $crmid));
		echo 'SUCCESS';
	}else
	{
		 die("An invalid tagid to delete.");
	}
	
}
?>
