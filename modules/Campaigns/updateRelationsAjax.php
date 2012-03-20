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

require_once('include/database/PearDatabase.php');

$rel_table = '';
$rel_field = '';
switch($_REQUEST['relatedmodule'])
{
	case 'Accounts' : $rel_table = 'vtiger_campaignaccountrel'; $rel_field = 'accountid';
	break;
	case 'Contacts' : $rel_table = 'vtiger_campaigncontrel'; $rel_field = 'contactid';
	break;
	case 'Leads' : $rel_table = 'vtiger_campaignleadrel'; $rel_field = 'leadid';
	break;
	case 'Potentials' :
	{


	}
	break;
	default:
	{
		echo ":#:FAILURE";
		exit;
	}
	break;
}


$sql = "UPDATE $rel_table SET campaignrelstatusid = ? WHERE campaignid = ? AND $rel_field = ?;";
$params = array($_REQUEST['campaignrelstatusid'], $_REQUEST['campaignid'], $_REQUEST['crmid']);
if($adb->pquery($sql, $params))
{
	echo ":#:SUCCESS";
}
else
{
	echo ":#:FAILURE";
}

exit;

?>