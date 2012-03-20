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
require_once('include/database/PearDatabase.php');
require_once('include/CustomFieldUtil.php');
global $mod_strings;
global $app_strings;

$smarty=new vtigerCRM_Smarty;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$smarty->assign("IMAGE_PATH", $image_path);

/**
 * Function to get Account custom fields
 * @param integer $leadid      - lead customfield id
 * @param integer $accountid   - account customfield id
 * return array   $accountcf   - account customfield
 */
function getAccountCustomValues($leadid,$accountid)
{
	global $adb;
	$accountcf=Array();
	$sql="select fieldid,fieldlabel,uitype,typeofdata from vtiger_field,vtiger_tab where vtiger_field.tabid=vtiger_tab.tabid and generatedtype=2 and vtiger_tab.name='Accounts' and vtiger_field.presence in (0,2)";
	$result = $adb->pquery($sql, array());
	$noofrows = $adb->num_rows($result);
	
	for($i=0;$i<$noofrows;$i++)
	{
        	$account_field['fieldid']=$adb->query_result($result,$i,"fieldid");
	        $account_field['fieldlabel']=getTranslatedString($adb->query_result($result,$i,"fieldlabel"),'Accounts');
		$account_field['typeofdata']=$adb->query_result($result,$i,"typeofdata");
		$account_field['fieldtype']=getCustomFieldTypeName($adb->query_result($result,$i,"uitype"));
		if($account_field['fieldid']==$accountid)
			$account_field['selected'] = "selected";
		else
			$account_field['selected'] = "";
		$account_cfelement[]=$account_field;
	}
	$accountcf[$leadid.'_account']=$account_cfelement;
	return $accountcf;
}

/**
 * Function to get contact custom fields
 * @param integer $leadid      - lead customfield id
 * @param integer $contactid   - contact customfield id
 * return array   $contactcf   - contact customfield
 */
function getContactCustomValues($leadid,$contactid)
{	
	global $adb;	
	$contactcf=Array();
	$sql="select fieldid,fieldlabel,uitype,typeofdata from vtiger_field,vtiger_tab where vtiger_field.tabid=vtiger_tab.tabid and generatedtype=2 and vtiger_tab.name='Contacts'and vtiger_field.presence in (0,2)";
	$result = $adb->pquery($sql, array());
	$noofrows = $adb->num_rows($result);
	for($i=0; $i<$noofrows; $i++)
	{
		$contact_field['fieldid']=$adb->query_result($result,$i,"fieldid");
		$contact_field['fieldlabel']=getTranslatedString($adb->query_result($result,$i,"fieldlabel"),'Contacts');
		$contact_field['typeofdata']=$adb->query_result($result,$i,"typeofdata");
		$contact_field['fieldtype']=getCustomFieldTypeName($adb->query_result($result,$i,"uitype"));
	
                if($contact_field['fieldid']==$contactid)
                        $contact_field['selected']="selected";
		else
                        $contact_field['selected'] = "";
		$contact_cfelement[]=$contact_field;
	}
	$contactcf[$leadid.'_contact'] = $contact_cfelement;
        return $contactcf;
}	

/**
 * Function to get potential custom fields
 * @param integer $leadid      - lead customfield id
 * @param integer $potentialid - potential customfield id
 * return array   $potentialcf - potential customfield
 */
function getPotentialCustomValues($leadid,$potentialid)
{
	global $adb;	
	$potentialcf=Array();
	$sql="select fieldid,fieldlabel,uitype,typeofdata from vtiger_field,vtiger_tab where vtiger_field.tabid=vtiger_tab.tabid and generatedtype=2 and vtiger_tab.name='Potentials' and vtiger_field.presence in (0,2)";
	$result = $adb->pquery($sql, array());
	$noofrows = $adb->num_rows($result);
	for($i=0; $i<$noofrows; $i++)
	{
		$potential_field['fieldid']=$adb->query_result($result,$i,"fieldid");
		$potential_field['fieldlabel']=getTranslatedString($adb->query_result($result,$i,"fieldlabel"),'Potentials');
		$potential_field['typeofdata']=$adb->query_result($result,$i,"typeofdata");
		$potential_field['fieldtype']=getCustomFieldTypeName($adb->query_result($result,$i,"uitype"));

		if($potential_field['fieldid']==$potentialid)
			 $potential_field['selected']="selected";
		else
             $potential_field['selected'] = "";
		$potential_cfelement[]=$potential_field;
	}
	$potentialcf[$leadid.'_potential']=$potential_cfelement;
        return $potentialcf;
}

/**
 * Function to get leads mapping custom fields
 * return array   $leadcf - mapping custom fields
 */

function customFieldMappings()
{
	global $adb;

	$convert_sql="select vtiger_convertleadmapping.*,uitype,fieldlabel,typeofdata from vtiger_convertleadmapping left join vtiger_field on vtiger_field.fieldid = vtiger_convertleadmapping.leadfid and vtiger_field.presence in (0,2)";
	$convert_result = $adb->pquery($convert_sql, array());

	$no_rows = $adb->num_rows($convert_result);
	for($j=0; $j<$no_rows; $j++)
	{
		$leadid = $adb->query_result($convert_result,$j,"leadfid");
		$accountid=$adb->query_result($convert_result,$j,"accountfid");
		$contactid=$adb->query_result($convert_result,$j,"contactfid");
		$potentialid=$adb->query_result($convert_result,$j,"potentialfid");
		$lead_field['sno'] = $j+1;
		$lead_field['leadid'] = getTranslatedString($adb->query_result($convert_result,$j,"fieldlabel"),'Leads'); 
		$lead_field['typeofdata']=$adb->query_result($convert_result,$j,"typeofdata");
		$lead_field['fieldtype'] = getCustomFieldTypeName($adb->query_result($convert_result,$j,"uitype"));; 
		$lead_field['account'] = getAccountCustomValues($leadid,$accountid);
		$lead_field['contact'] = getContactCustomValues($leadid,$contactid);
		$lead_field['potential'] = getPotentialCustomValues($leadid,$potentialid);
		$leadcf[]= $lead_field;
	}
	return $leadcf;
}
$module = 'Leads';
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("CUSTOMFIELDMAPPING",customFieldMappings());
$smarty->assign("MODULE",$module);
$smarty->display("CustomFieldMapping.tpl");

?>
