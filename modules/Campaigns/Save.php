<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  Saves an Account record and then redirects the browser to the 
 * defined return URL.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/Campaigns/Campaigns.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

$focus = new Campaigns();
 global $current_user;
 $currencyid=fetchCurrency($current_user->id);
 $rate_symbol = getCurrencySymbolandCRate($currencyid);
 $rate = $rate_symbol['rate'];
//added to fix 4600
$search=vtlib_purify($_REQUEST['search_url']);
setObjectValuesFromRequest($focus);

if(isset($_REQUEST['expectedrevenue']))
{
	$value = convertToDollar($_REQUEST['expectedrevenue'],$rate);
	$focus->column_fields['expectedrevenue'] = $value;
}
if(isset($_REQUEST['budgetcost']))
{
	$value = convertToDollar($_REQUEST['budgetcost'],$rate);
	$focus->column_fields['budgetcost'] = $value;
}
if(isset($_REQUEST['actualcost']))
{
	$value = convertToDollar($_REQUEST['actualcost'],$rate);
	$focus->column_fields['actualcost'] = $value;
}
if(isset($_REQUEST['actualroi']))
{
	$value = convertToDollar($_REQUEST['actualroi'],$rate);
	$focus->column_fields['actualroi'] = $value;
}
if(isset($_REQUEST['expectedroi']))
{
	$value = convertToDollar($_REQUEST['expectedroi'],$rate);
	$focus->column_fields['expectedroi'] = $value;
}

if($_REQUEST['assigntype'] == 'U')  {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
} elseif($_REQUEST['assigntype'] == 'T') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
}

$focus->save("Campaigns");
$return_id = $focus->id;

$parenttab = getParentTab();
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != "") $return_module = vtlib_purify($_REQUEST['return_module']);
else $return_module = "Campaigns";
if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != "") $return_action = vtlib_purify($_REQUEST['return_action']);
else $return_action = "DetailView";
if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "") $return_id = vtlib_purify($_REQUEST['return_id']);

header("Location: index.php?action=$return_action&module=$return_module&record=$return_id&parenttab=$parenttab&start=".vtlib_purify($_REQUEST['pagenumber']).$search);
?>