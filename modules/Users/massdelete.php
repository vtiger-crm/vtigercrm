<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/CommonUtils.php');
$idlist = vtlib_purify($_REQUEST['idlist']);
$viewid = vtlib_purify($_REQUEST['viewname']);
$returnmodule = vtlib_purify($_REQUEST['return_module']);
$return_action = vtlib_purify($_REQUEST['return_action']);
$rstart='';
//Added to fix 4600
$url = getBasic_Advance_SearchURL();

//split the string and store in an array
$storearray = explode(";",$idlist);
array_filter($storearray);
$ids_list = array();
$errormsg = '';
foreach($storearray as $id)
{
        if(isPermitted($returnmodule,'Delete',$id) == 'yes')
        {
			$focus = CRMEntity::getInstance($returnmodule);
			DeleteEntity($returnmodule,$returnmodule,$focus,$id,'');
        }
        else
        {
        	$ids_list[] = $id;
        }
}
if(count($ids_list) > 0) {
	$ret = getEntityName($returnmodule,$ids_list);
	if(count($ret) > 0)
	{
       		$errormsg = implode(',',$ret);
	}
}

if(isset($_REQUEST['smodule']) && ($_REQUEST['smodule']!=''))
{
	$smod = "&smodule=".vtlib_purify($_REQUEST['smodule']);
}
if(isset($_REQUEST['start']) && ($_REQUEST['start']!=''))
{
	$rstart = "&start=".vtlib_purify($_REQUEST['start']);
}
if($returnmodule == 'Emails')
{
	if(isset($_REQUEST['folderid']) && $_REQUEST['folderid'] != '')
	{
		$folderid = vtlib_purify($_REQUEST['folderid']);
	}else
	{
		$folderid = 1;
	}
	header("Location: index.php?module=".$returnmodule."&action=".$returnmodule."Ajax&folderid=".$folderid."&ajax=delete".$rstart."&file=ListView&errormsg=".$errormsg.$url);
}
elseif($return_action == 'ActivityAjax')
{
	$subtab = vtlib_purify($_REQUEST['subtab']);
	header("Location: index.php?module=".$returnmodule."&action=".$return_action."".$rstart."&view=".vtlib_purify($_REQUEST['view'])."&hour=".vtlib_purify($_REQUEST['hour'])."&day=".vtlib_purify($_REQUEST['day'])."&month=".vtlib_purify($_REQUEST['month'])."&year=".vtlib_purify($_REQUEST['year'])."&type=".vtlib_purify($_REQUEST['type'])."&viewOption=".vtlib_purify($_REQUEST['viewOption'])."&subtab=".$subtab.$url);
}
			
elseif($returnmodule!='Faq')
{
	header("Location: index.php?module=".$returnmodule."&action=".$returnmodule."Ajax&ajax=delete".$rstart."&file=ListView&viewname=".$viewid."&errormsg=".$errormsg.$url);
}
else
{
	header("Location: index.php?module=".$returnmodule."&action=".$returnmodule."Ajax&ajax=delete".$rstart."&file=ListView&errormsg=".$errormsg.$url);
}
?>