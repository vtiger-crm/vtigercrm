<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************* */

global $current_user, $currentModule, $theme, $app_strings,$log;
$category = getParentTab();

require_once 'include/Webservices/ConvertLead.php';
require_once 'include/utils/VtlibUtils.php';
//Getting the Parameters from the ConvertLead Form
$recordId = vtlib_purify($_REQUEST["record"]);
$leadId = vtws_getWebserviceEntityId('Leads', $recordId);

//make sure that either contacts or accounts is selected
if(!empty($_REQUEST['entities']))
{
	$entities=vtlib_purify($_REQUEST['entities']);

	$assigned_to = vtlib_purify($_REQUEST["c_assigntype"]);
	if ($assigned_to == "U") {
		$assigned_user_id = vtlib_purify($_REQUEST["c_assigned_user_id"]);
		$assignedTo = vtws_getWebserviceEntityId('Users', $assigned_user_id);
	} else {
		$assigned_user_id = vtlib_purify($_REQUEST["c_assigned_group_id"]);
		$assignedTo = vtws_getWebserviceEntityId('Groups', $assigned_user_id);
	}

	$transferRelatedRecordsTo = vtlib_purify($_REQUEST['transferto']);
	if (empty($transferRelatedRecordsTo))
		$transferRelatedRecordsTo = 'Contacts';


	$entityValues=array();

	$entityValues['transferRelatedRecordsTo']=$transferRelatedRecordsTo;
	$entityValues['assignedTo']=$assignedTo;
	$entityValues['leadId']=$leadId;

	if(vtlib_isModuleActive('Accounts')&& in_array('Accounts', $entities)){
		$entityValues['entities']['Accounts']['create']=true;
		$entityValues['entities']['Accounts']['name']='Accounts';
		$entityValues['entities']['Accounts']['accountname'] = vtlib_purify($_REQUEST['accountname']);
		$entityValues['entities']['Accounts']['industry']=vtlib_purify($_REQUEST['industry']);
	}

	if(vtlib_isModuleActive('Potentials')&& in_array('Potentials', $entities)){
		$entityValues['entities']['Potentials']['create']=true;
		$entityValues['entities']['Potentials']['name']='Potentials';
		$entityValues['entities']['Potentials']['potentialname']=  vtlib_purify($_REQUEST['potentialname']);
		$entityValues['entities']['Potentials']['closingdate']=  vtlib_purify($_REQUEST['closingdate']);
		$entityValues['entities']['Potentials']['sales_stage']=  vtlib_purify($_REQUEST['sales_stage']);
		$entityValues['entities']['Potentials']['amount']=  vtlib_purify($_REQUEST['amount']);
	}

	if(vtlib_isModuleActive('Contacts')&& in_array('Contacts', $entities)){
		$entityValues['entities']['Contacts']['create']=true;
		$entityValues['entities']['Contacts']['name']='Contacts';
		$entityValues['entities']['Contacts']['lastname']=  vtlib_purify($_REQUEST['lastname']);
		$entityValues['entities']['Contacts']['firstname']=  vtlib_purify($_REQUEST['firstname']);
		$entityValues['entities']['Contacts']['email']=  vtlib_purify($_REQUEST['email']);
	}

	try{
		$result = vtws_convertlead($entityValues,$current_user);
	}catch(Exception $e){
		showError();
	}
	
	$accountIdComponents = vtws_getIdComponents($result['Accounts']);
	$accountId = $accountIdComponents[1];
	$contactIdComponents = vtws_getIdComponents($result['Contacts']);
	$contactId = $contactIdComponents[1];
	$potentialIdComponents = vtws_getIdComponents($result['Potentials']);
	$potentialId = $potentialIdComponents[1];
}

if (!empty($accountId)) {
    header("Location: index.php?action=DetailView&module=Accounts&record=$accountId&parenttab=$category");
} elseif (!empty($contactId)) {
    header("Location: index.php?action=DetailView&module=Contacts&record=$contactId&parenttab=$category");
} else {
	showError();
}

function showError(){
	require_once 'include/utils/VtlibUtils.php';
	global $current_user, $currentModule, $theme, $app_strings,$log;
    echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";
    echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
    echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='" . vtiger_imageurl('denied.gif', $theme) . "' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
			<span class='genHeaderSmall'>". getTranslatedString('SINGLE_'.$currentModule, $currentModule)." ".
			getTranslatedString('CANNOT_CONVERT', $currentModule)  ."
		<br>
		<ul> ". getTranslatedString('LBL_FOLLOWING_ARE_POSSIBLE_REASONS', $currentModule) .":
			<li>". getTranslatedString('LBL_LEADS_FIELD_MAPPING_INCOMPLETE', $currentModule) ."</li>
			<li>". getTranslatedString('LBL_MANDATORY_FIELDS_ARE_EMPTY', $currentModule) ."</li>
		</ul>
		</span>
		</td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>";

    if (is_admin($current_user)) {
        echo "<a href='index.php?module=Settings&action=CustomFieldList&parenttab=Settings&formodule=Leads'>". getTranslatedString('LBL_LEADS_FIELD_MAPPING', $currentModule) ."</a><br>";
    }

    echo "<a href='javascript:window.history.back();'>". getTranslatedString('LBL_GO_BACK', $currentModule) ."</a><br>";

    echo "</td>
               </tr>
		</tbody></table>
		</div>
                </td></tr></table>";
}
?>
