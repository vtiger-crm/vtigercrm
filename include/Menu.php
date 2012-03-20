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
 * Description:  TODO To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

global $mod_strings;
global $app_strings;
global $moduleList;

require_once('include/utils/utils.php');
require_once('include/utils/UserInfoUtil.php');


$module_menu_array = Array('Contacts' => $app_strings['LNK_NEW_CONTACT'],
	                   'Leads'=> $app_strings['LNK_NEW_LEAD'],
	                   'Accounts' => $app_strings['LNK_NEW_ACCOUNT'],
	                   'Potentials' => $app_strings['LNK_NEW_OPPORTUNITY'],
	                   'HelpDesk' => $app_strings['LNK_NEW_HDESK'],
	                   'Faq' => $app_strings['LNK_NEW_FAQ'],
	                   'Products' => $app_strings['LNK_NEW_PRODUCT'],
	                   'Documents' => $app_strings['LNK_NEW_NOTE'],
	                   'Emails' => $app_strings['LNK_NEW_EMAIL'],
			   'Events' => $app_strings['LNK_NEW_EVENT'],
	                   'Tasks' => $app_strings['LNK_NEW_TASK'],
	                   'Vendor' => $app_strings['LNK_NEW_VENDOR'],
	                   'PriceBook' => $app_strings['LNK_NEW_PRICEBOOK'],
			   'Quotes' => $app_strings['LNK_NEW_QUOTE'],	
			   'PurchaseOrder' => $app_strings['LNK_NEW_PO'],	
			   'SalesOrder' => $app_strings['LNK_NEW_SO'],	
			   'Invoice' => $app_strings['LNK_NEW_INVOICE']	
	                    );
$module_menu = Array();
$i= 0;
$add_url = "";
foreach($module_menu_array as $module1 => $label)
{
	$add_url='';
	$curr_action = 'EditView';
	$ret_action = 'DetailView';
	if($module1 == 'Events')
	{
		$module_display = 'Activities';
		$add_url = "&activity_mode=Events";
		$tabid = getTabid($module1);
	}
	elseif($module1 == 'Tasks')
	{
		$module_display = 'Activities';
                $add_url = "&activity_mode=Task";
		$tabid = getTabid("Activities");
	}
	else
	{
		$module_display = $module1;
		$tabid = getTabid($module1);
	}

	if(in_array($module_display, $moduleList))
	{
	
		if(isPermitted($module_display,'EditView') == 'yes')
		{
			$tempArray = Array("index.php?module=".$module_display."&action=".$curr_action."&return_module=".$module_display."&return_action=".$ret_action.$add_url, $label);
			$module_menu[$i] = $tempArray;
			$i++;
		}
	}
	elseif($module_display == 'Faq')
	{
			$tempArray = Array("index.php?module=".$module_display."&action=".$curr_action."&return_module=".$module_display."&return_action=".$ret_action.$add_url, $label);
			$module_menu[$i] = $tempArray;
			$i++;
	}
	
}


?>
