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
require_once('include/ComboUtil.php');

// Account is used to store vtiger_account information.
class ImportAccount extends Accounts {
	 var $db;

	// Get _dom arrays from Database
	//$comboFieldNames = Array('accounttype'=>'account_type_dom'
	//                      ,'industry'=>'industry_dom');
	//$comboFieldArray = getComboArray($comboFieldNames);


	// This is the list of vtiger_fields that are required.
	var $required_fields =  array("accountname"=>1);
	
	// This is the list of the functions to run when importing
	var $special_functions =  array(
						"map_member_of","modseq_number",
						//"add_billing_address_streets"
						//,"add_shipping_address_streets"
						//,"fix_website"
				       );

	/*
	function fix_website()
	{
		if ( isset($this->website) &&
			preg_match("/^http:\/\//",$this->website) )
		{
			$this->website = substr($this->website,7);
		}	
	}

	
	function add_industry()
	{
		if ( isset($this->industry) &&
			! isset( $comboFieldArray['industry_dom'][$this->industry]))
		{
			unset($this->industry);
		}	
	}

	function add_type()
	{
		if ( isset($this->type) &&
			! isset($comboFieldArray['account_type_dom'][$this->type]))
		{
			unset($this->type);
		}	
	}

	function add_billing_address_streets() 
	{ 
		if ( isset($this->billing_address_street_2)) 
		{ 
			$this->billing_address_street .= 
				" ". $this->billing_address_street_2; 
		} 

		if ( isset($this->billing_address_street_3)) 
		{  
			$this->billing_address_street .= 
				" ". $this->billing_address_street_3; 
		} 
		if ( isset($this->billing_address_street_4)) 
		{  
			$this->billing_address_street .= 
				" ". $this->billing_address_street_4; 
		}
	}

	function add_shipping_address_streets() 
	{ 
		if ( isset($this->shipping_address_street_2)) 
		{ 
			$this->shipping_address_street .= 
				" ". $this->shipping_address_street_2; 
		} 

		if ( isset($this->shipping_address_street_3)) 
		{  
			$this->shipping_address_street .= 
				" ". $this->shipping_address_street_3; 
		} 

		if ( isset($this->shipping_address_street_4)) 
		{  
			$this->shipping_address_street .= 
				" ". $this->shipping_address_street_4; 
		} 
	}
	*/

	// This is the list of vtiger_fields that are importable.
	// some if these do not map directly to database columns
	/*var $importable_fields = Array(
		"id"=>1
		,"name"=>1
		,"website"=>1
		,"industry"=>1
		,"account_type"=>1
		,"ticker_symbol"=>1
		,"parent_name"=>1
		,"employees"=>1
		,"ownership"=>1
		,"phone_office"=>1
		,"phone_fax"=>1
		,"phone_alternate"=>1
		,"email1"=>1
		,"email2"=>1
		,"rating"=>1
		,"sic_code"=>1
		,"annual_revenue"=>1
		,"billing_address_street"=>1
		,"billing_address_street_2"=>1
		,"billing_address_street_3"=>1
		,"billing_address_street_4"=>1
		,"billing_address_city"=>1
		,"billing_address_state"=>1
		,"billing_address_postalcode"=>1
		,"billing_address_country"=>1
		,"shipping_address_street"=>1
		,"shipping_address_street_2"=>1
		,"shipping_address_street_3"=>1
		,"shipping_address_street_4"=>1
		,"shipping_address_city"=>1
		,"shipping_address_state"=>1
		,"shipping_address_postalcode"=>1
		,"shipping_address_country"=>1
		,"description"=>1
		);
		*/

		var $importable_fields = Array();

		/** Constructor which will set the importable_fields as $this->importable_fields[$key]=1 in this object where key is the fieldname in the field table
		 */
	function ImportAccount() {
		parent::Accounts();
		$this->log = LoggerManager::getLogger('import_account');
		$this->db = PearDatabase::getInstance();
		$this->db->println("IMP ImportAccount");
		$this->initImportableFields("Accounts");
		
		$this->db->println($this->importable_fields);
	}

	/**     function used to map with existing Mamber Of(Account) if the account is map with an member of during import
         */
	function map_member_of()
	{
		global $adb;

		$account_name = $this->column_fields['account_id'];
		$adb->println("Entering map_member_of account_id=".$account_name);

		if ((! isset($account_name) || $account_name == '') )
		{
			$adb->println("Exit map_member_of. Account Name(Member Of) not set for this entity.");
			return; 
		}

		$account_name = trim($account_name);

		//Query to get the available Account which is not deleted
		$query = "select accountid from vtiger_account inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid WHERE vtiger_account.accountname=? and vtiger_crmentity.deleted=0";
		$account_id = $adb->query_result($adb->pquery($query, array($account_name)),0,'accountid');

		if($account_id == '' || !isset($account_id))
			$account_id = 0;

		$this->column_fields['account_id'] = $account_id;

		$adb->println("Exit map_member_of. Fetched Account for '".$account_name."' and the account_id = $account_id");
        }

	// Module Sequence Numbering	
	function modseq_number() {
		$this->column_fields['account_no'] = '';	
	}
	// END

}



?>
