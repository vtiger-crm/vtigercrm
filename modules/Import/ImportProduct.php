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
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('data/SugarBean.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Emails/Emails.php');
require_once('modules/Accounts/Accounts.php');
require_once('modules/Products/Products.php');
require_once('include/ComboUtil.php');
require_once('modules/Leads/Leads.php');


class ImportProduct extends Products {
	 var $db;

	// This is the list of the functions to run when importing
	var $special_functions =  array(
					"assign_user",
					"map_vendor_name",
					"map_member_of",
					"modseq_number",
				       );

	var $importable_fields = Array();

	/**   function used to set the assigned_user_id value in the column_fields when we map the username during import
         */
	function assign_user()
	{
		global $current_user;
		$ass_user = $this->column_fields["assigned_user_id"];
		$this->db->println("assign_user ".$ass_user." cur_user=".$current_user->id);
		
		if( $ass_user != $current_user->id)
		{
			$this->db->println("searching and assigning ".$ass_user);

			$result = $this->db->pquery("select id from vtiger_users where id = ?", array($ass_user));
			if($this->db->num_rows($result)!=1)
			{
				$this->db->println("not exact records setting current userid");
				$this->column_fields["assigned_user_id"] = $current_user->id;
			}
			else
			{
			
				$row = $this->db->fetchByAssoc($result, -1, false);
				if (isset($row['id']) && $row['id'] != -1)
        	        	{
					$this->db->println("setting id as ".$row['id']);
					$this->column_fields["assigned_user_id"] = $row['id'];
				}
				else
				{
					$this->db->println("setting current userid");
					$this->column_fields["assigned_user_id"] = $current_user->id;
				}
			}
		}
	}

	/** Constructor which will set the importable_fields as $this->importable_fields[$key]=1 in this object where key is the fieldname in the field table
	 */
	function ImportProduct() {
		parent::Products();
		$this->log = LoggerManager::getLogger('import_product');
		$this->db = PearDatabase::getInstance();
		$this->db->println("IMP ImportProduct");
		$this->initImportableFields("Products");
		$this->db->println($this->importable_fields);
	}

	/**     function used to map with existing Vendor if the product is map with an vendor during import
         */
	function map_vendor_name()
	{
		global $adb;

		$vendor_name = $this->column_fields['vendor_id'];
		$adb->println("Entering map_vendor_name vendor_id=".$vendor_name);

		if ((! isset($vendor_name) || $vendor_name == '') )
		{
			$adb->println("Exit map_vendor_name. Vendor Name not set for this entity.");
			return; 
		}

		$vendor_name = trim($vendor_name);

		//Query to get the available Vendor which is not deleted
		$query = "select vendorid from vtiger_vendor inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_vendor.vendorid WHERE vtiger_vendor.vendorname=? and vtiger_crmentity.deleted=0";
		$vendor_id = $adb->query_result($adb->pquery($query, array($vendor_name)),0,'vendorid');

		if($vendor_id == '' || !isset($vendor_id))
			$vendor_id = 0;

		$this->column_fields['vendor_id'] = $vendor_id;

		$adb->println("Exit map_vendor_name. Fetched Vendor for '".$vendor_name."' and the vendorid = $vendor_id");
        }

	/** Function used to map with existing Member Of(Product) if the product is map with an member of during import
    */
	function map_member_of()
	{
		global $adb;

		$product_name = $this->column_fields['product_id'];
		$adb->println("Entering map_member_of product_id=".$product_name);

		if ((! isset($product_name) || $product_name == '') )
		{
			$adb->println("Exit map_member_of. Product Name(Member Of) not set for this entity.");
			return; 
		}

		$product_name = trim($product_name);

		//Query to get the available Product which is not deleted
		$query = "select productid from vtiger_products inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid WHERE vtiger_products.productname=? and vtiger_crmentity.deleted=0";
		$product_id = $adb->query_result($adb->pquery($query, array($product_name)),0,'productid');

		if($product_id == '' || !isset($product_id))
			$product_id = 0;

		$this->column_fields['product_id'] = $product_id;

		$adb->println("Exit map_member_of. Fetched Account for '".$product_name."' and the account_id = $product_id");
    }

	//Module Sequence Numbering	
	function modseq_number() {
		$this->column_fields['product_no'] = '';
	}
	// END
}
?>