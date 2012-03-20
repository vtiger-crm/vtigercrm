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
require_once('modules/Vendors/Vendors.php');
require_once('modules/Import/UsersLastImport.php');
require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php');

class ImportVendors extends Vendors {
	 var $db;

	// This is the list of the functions to run when importing
	var $special_functions =  array("assign_user","modseq_number");

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

			$result = $this->db->query("select id from vtiger_users where id = '".$ass_user."'");
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
	function ImportVendors() {
		parent::Vendors();
		$this->log = LoggerManager::getLogger('import_vendors');
		$this->db = PearDatabase::getInstance();
		$this->db->println("IMP ImportVendors");
		$this->initImportableFields("Vendors");
		$this->db->println($this->importable_fields);
	}

	//Module Sequence Numbering	
	function modseq_number() {
		$this->column_fields['vendor_no'] = '';
	}
	// END

}
?>