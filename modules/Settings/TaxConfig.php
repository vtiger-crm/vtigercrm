<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('Smarty_setup.php');
global $mod_strings;
global $app_strings;
global $adb;
global $log;

$smarty = new vtigerCRM_Smarty;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$tax_details = getAllTaxes();
$sh_tax_details = getAllTaxes('all','sh');


//To save the edited value
if($_REQUEST['save_tax'] == 'true')
{
	for($i=0;$i<count($tax_details);$i++)
	{
     		$new_labels[$tax_details[$i]['taxid']] = $_REQUEST[$tax_details[$i]['taxlabel']];
		$new_percentages[$tax_details[$i]['taxid']] = $_REQUEST[$tax_details[$i]['taxname']];
	}
	updateTaxPercentages($new_percentages);
	updateTaxLabels($new_labels);
	$getlist = true;
}
elseif($_REQUEST['sh_save_tax'] == 'true')
{
 
	for($i=0;$i<count($sh_tax_details);$i++)
	{
	  $new_labels[$sh_tax_details[$i]['taxid']] = $_REQUEST[$sh_tax_details[$i]['taxlabel']];
		$new_percentages[$sh_tax_details[$i]['taxid']] = $_REQUEST[$sh_tax_details[$i]['taxname']];
	}
	
	updateTaxPercentages($new_percentages,'sh');
	updateTaxLabels($new_labels,'sh');
	$getlist = true;
}

//To edit
if($_REQUEST['edit_tax'] == 'true')
{
	$smarty->assign("EDIT_MODE", 'true');
}
elseif($_REQUEST['sh_edit_tax'] == 'true')
{
	$smarty->assign("SH_EDIT_MODE", 'true');
}

//To add tax
if($_REQUEST['add_tax_type'] == 'true')
{
	//Add the given tax name and value as a new tax type
	echo addTaxType($_REQUEST['addTaxLabel'],$_REQUEST['addTaxValue']);
	$getlist = true;
}
elseif($_REQUEST['sh_add_tax_type'] == 'true')
{
	echo addTaxType($_REQUEST['sh_addTaxLabel'],$_REQUEST['sh_addTaxValue'],'sh');
	$getlist = true;
}

//To Disable ie., delete or enable
if(($_REQUEST['disable'] == 'true' || $_REQUEST['enable'] == 'true') && $_REQUEST['taxname'] != '')
{
	if($_REQUEST['disable'] == 'true')
		changeDeleted($_REQUEST['taxname'],1);
	else
		changeDeleted($_REQUEST['taxname'],0);
	$getlist = true;
}
elseif(($_REQUEST['sh_disable'] == 'true' || $_REQUEST['sh_enable'] == 'true') && $_REQUEST['sh_taxname'] != '')
{
	if($_REQUEST['sh_disable'] == 'true')
		changeDeleted($_REQUEST['sh_taxname'],1,'sh');
	else
		changeDeleted($_REQUEST['sh_taxname'],0,'sh');
	$getlist = true;
}

//after done save or enable/disable or added new tax the list will be retrieved again from db
if($getlist)
{
	$tax_details = getAllTaxes();
	$sh_tax_details = getAllTaxes('all','sh');
}

$smarty->assign("TAX_COUNT", count($tax_details));
$smarty->assign("SH_TAX_COUNT", count($sh_tax_details));

if(count($tax_details) == 0)
	$smarty->assign("TAX_COUNT", 0);
if(count($sh_tax_details) == 0)
	$smarty->assign("SH_TAX_COUNT", 0);

$smarty->assign("TAX_VALUES", $tax_details);

$smarty->assign("SH_TAX_VALUES", $sh_tax_details);

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("MOD", $mod_strings);
$smarty->display("Settings/TaxConfig.tpl");


/**	Function to update the list of Tax percentages for the passed tax types
 *	@param array $new_percentages - array of tax types and the values like [taxid]=new value ie., [1]=3.56, [2]=11.45
 *      @param string $sh - sh or empty, if sh passed then update will be done in shipping and handling related table
 *      @return void
 */
function updateTaxPercentages($new_percentages, $sh='')
{
	global $adb, $log;
	$log->debug("Entering into the function updateTaxPercentages");

	foreach($new_percentages as $taxid => $new_val)
	{
		if($new_val != '')
		{
			if($sh != '' && $sh == 'sh')
				$query = "update vtiger_shippingtaxinfo set percentage=? where taxid=?";
			else
				$query = "update vtiger_inventorytaxinfo set percentage =? where taxid=?";
			$adb->pquery($query, array($new_val, $taxid));
		}
	}

	$log->debug("Exiting from the function updateTaxPercentages");
}

/**	Function to update the list of Tax Labels for the taxes
 *	@param array $new_labels - array of tax types and the values like [taxid]=new label ie., [1]=aa, [2]=bb
 *      @param string $sh - sh or empty, if sh passed then update will be done in shipping and handling related table
 *      @return void
 */
function updateTaxLabels($new_labels, $sh='')
{
	global $adb, $log;
	$log->debug("Entering into the function updateTaxPercentages");

	foreach($new_labels as $taxid => $new_val)
	{
		if($new_val != '')
		{
			if($sh != '' && $sh == 'sh')
				$query = "update vtiger_shippingtaxinfo set taxlabel= ? where taxid=?";
			else
				$query = "update vtiger_inventorytaxinfo set taxlabel = ? where taxid=?";
			$adb->pquery($query, array($new_val, $taxid));
		}
	}

	$log->debug("Exiting from the function updateTaxPercentages");
}
/**	Function used to add the tax type which will do database alterations
 *	@param string $taxlabel - tax label name to be added
 *	@param string $taxvalue - tax value to be added
 *      @param string $sh - sh or empty , if sh passed then the tax will be added in shipping and handling related table
 *      @return void
 */
function addTaxType($taxlabel, $taxvalue, $sh='')
{
	global $adb, $log;
	$log->debug("Entering into function addTaxType($taxlabel, $taxvalue, $sh)");

	//First we will check whether the tax is already available or not
	if($sh != '' && $sh == 'sh')
		$check_query = "select taxlabel from vtiger_shippingtaxinfo where taxlabel=?";
	else
		$check_query = "select taxlabel from vtiger_inventorytaxinfo where taxlabel=?";
	$check_res = $adb->pquery($check_query, array($taxlabel));

	if($adb->num_rows($check_res) > 0)
		return "<font color='red'>This tax is already available</font>";

	//if the tax is not available then add this tax.
	//Add this tax as a column in related table	
	if($sh != '' && $sh == 'sh')
	{
		$taxid = $adb->getUniqueID("vtiger_shippingtaxinfo");
		$taxname = "shtax".$taxid;
		$query = "alter table vtiger_inventoryshippingrel add column $taxname decimal(7,3) default NULL";
	}
	else
	{
		$taxid = $adb->getUniqueID("vtiger_inventorytaxinfo");
		$taxname = "tax".$taxid;
		$query = "alter table vtiger_inventoryproductrel add column $taxname decimal(7,3) default NULL";
	}
	$res = $adb->pquery($query, array());

	//if the tax is added as a column then we should add this tax in the list of taxes
	if($res)
	{
		if($sh != '' && $sh == 'sh') 
			$query1 = "insert into vtiger_shippingtaxinfo values(?,?,?,?,?)";
		else
			$query1 = "insert into vtiger_inventorytaxinfo values(?,?,?,?,?)";

		$params1 = array($taxid, $taxname, $taxlabel, $taxvalue, 0);
		$res1 = $adb->pquery($query1, $params1);
	}

	$log->debug("Exit from function addTaxType($taxlabel, $taxvalue)");
	if($res1)
		return '';
	else
		return "There may be some problem in adding the Tax type. Please try again";
}

/**	Function used to Enable or Disable the tax type 
 *	@param string $taxname - taxname to enable or disble
 *	@param int $deleted - 0 or 1 where 0 to enable and 1 to disable
 *	@param string $sh - sh or empty, if sh passed then the enable/disable will be done in shipping and handling tax table ie.,vtiger_shippingtaxinfo  else this enable/disable will be done in Product tax table ie., in vtiger_inventorytaxinfo
 *	@return void
 */
function changeDeleted($taxname, $deleted, $sh='')
{
	global $log, $adb;
	$log->debug("Entering into function changeDeleted($taxname, $deleted, $sh)");

	if($sh == 'sh')
		$adb->pquery("update vtiger_shippingtaxinfo set deleted=? where taxname=?", array($deleted, $taxname));
	else
		$adb->pquery("update vtiger_inventorytaxinfo set deleted=? where taxname=?", array($deleted, $taxname));
	$log->debug("Exit from function changeDeleted($taxname, $deleted, $sh)");
}

?>