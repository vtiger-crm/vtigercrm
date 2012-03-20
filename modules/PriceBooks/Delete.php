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
 * Description:  Deletes an Account record and then redirects the browser to the 
 * defined return URL.
 ********************************************************************************/

require_once('modules/PriceBooks/PriceBooks.php');
global $mod_strings;

require_once('include/logging.php');
$log = LoggerManager::getLogger('product_delete');

$focus = new PriceBooks();

	//Added to fix 4600
	$url = getBasic_Advance_SearchURL();

if(!isset($_REQUEST['record']))
	die($mod_strings['ERR_DELETE_RECORD']);

//Added to delete the pricebook from Product related list
if($_REQUEST['record'] != '' && $_REQUEST['return_id'] != '' && $_REQUEST['module'] == 'PriceBooks' 
	&& ($_REQUEST['return_module'] == 'Products' || $_REQUEST['return_module'] == 'Services'))
{
	$pricebookid = $_REQUEST['record'];
	$productid = $_REQUEST['return_id'];
	$adb->pquery("delete from vtiger_pricebookproductrel where pricebookid=? and productid=?", array($pricebookid, $productid));
}

if($_REQUEST['module'] == $_REQUEST['return_module'])
	$focus->mark_deleted($_REQUEST['record']);

$parenttab = getParentTab();

header("Location: index.php?module=".vtlib_purify($_REQUEST['return_module'])."&action=".vtlib_purify($_REQUEST['return_action'])."&record=".vtlib_purify($_REQUEST['return_id'])."&parenttab=$parenttab$url");

?>