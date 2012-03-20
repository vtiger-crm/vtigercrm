<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
      
include_once('config.php');
require_once('include/logging.php');
require_once('modules/HelpDesk/HelpDesk.php');
require_once('modules/Import/UsersLastImport.php');
require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php');

class ImportTicket extends HelpDesk {
	 var $db;

	// This is the list of the functions to run when importing
	var $special_functions =  array("assign_user","add_product","empty_relatedto","modseq_number");

	var $importable_fields = Array();

	/**	function used to set the assigned_user_id value in the column_fields when we map the username during import
	 */
	function assign_user()
	{
		global $current_user;
		$ass_user = $this->column_fields["assigned_user_id"];
		$this->db->println("assign_user ".$ass_user." cur_user=".$current_user->id);
		
		if( $ass_user != $current_user->id)
		{
			$this->db->println("searching and assigning ".$ass_user);

			$result = $this->db->pquery("select id from vtiger_users where id = ? union select groupid as id from vtiger_groups where groupid = ?",array($ass_user, $ass_user));
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
	function add_product()
        {
                global $adb,$imported_ids,$current_user;

                $pro_name = $this->column_fields['product_id'];
                if((! isset($pro_name) || $pro_name == '') )
                        return;

                //check if it already exists
                $focus = new Products();
                $query = '';

                //Modified to remove the spaces at first and last in vtiger_product name
                $pro_name = trim($pro_name);

                //Modified the query to get the available product only ie., which is not deleted
                $query = "select vtiger_products.* ,vtiger_crmentity.deleted from vtiger_products,vtiger_crmentity  WHERE productname=? and vtiger_crmentity.crmid = vtiger_products.productid and vtiger_crmentity.deleted=0";
                $result = $adb->pquery($query, array($pro_name));
                $row = $this->db->fetchByAssoc($result, -1, false);
                $adb->println($row);

                // we found a row with that id
                if (isset($row['productid']) && $row['productid'] != -1)
                        $focus->id = $row['productid'];

                $this->column_fields["product_id"] = $focus->id;
        }
	function empty_relatedto()
	{
		global $adb;
		$parent_name = $this->column_fields["parent_id"];
		if($parent_name == '' || $parent_name == NULL)
                        $parent_id = 0;
		else
		{   //get the account
			$relatedTo = explode(':',$parent_name);
			$parent_module = $relatedTo[0]; $parent_module = trim($parent_module," ");
			$parent_name = $relatedTo[3]; $parent_name = trim($parent_name," ");
			$num_rows = 0;
			if($parent_module == 'Contacts')
			{
				$query ="select crmid from vtiger_contactdetails, vtiger_crmentity WHERE concat(lastname,' ',firstname)=? and vtiger_crmentity.crmid =vtiger_contactdetails.contactid and vtiger_crmentity.deleted=0";
				$result = $adb->pquery($query, array($parent_name));
				$num_rows=$adb->num_rows($result);
			}
			else if($parent_module == 'Accounts')
			{
				$query = "select crmid from vtiger_account, vtiger_crmentity WHERE accountname=? and vtiger_crmentity.crmid =vtiger_account.accountid and vtiger_crmentity.deleted=0";
				$result = $adb->pquery($query, array($parent_name));
				$num_rows = $adb->num_rows($result);
			}
			else $num_rows=0;
			if($num_rows == 0) $parent_id = 0;
			else $parent_id = $adb->query_result($result,0,"crmid");
		}
		$this->column_fields['parent_id'] = $parent_id;        
	}
	/** Constructor which will set the importable_fields as $this->importable_fields[$key]=1 in this object where key is the fieldname in the field table
	 */
	function ImportTicket() {
		parent::HelpDesk();
		$this->log = LoggerManager::getLogger('import_ticket');
		$this->db = PearDatabase::getInstance();
		$this->db->println("IMP ImportTicket");
		$this->initImportableFields("HelpDesk");
		$this->db->println($this->importable_fields);
	}

	// Module Sequence Numbering	
	function modseq_number() {
		$this->column_fields['ticket_no'] = '';
	}
	// END
	
}
?>