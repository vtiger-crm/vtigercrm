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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/data/SugarBean.php,v 1.70 2005/03/16 10:25:16 shaw Exp $
 * Description:  Defines the base class for all data entities used throughout the 
 * application.  The base class including its methods and variables is designed to 
 * be overloaded with module-specific methods and variables particular to the 
 * module's base entity class. 
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('data/Tracker.php');
require_once('include/utils/utils.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/database/PearDatabase.php');

class SugarBean
{
    /**
     * This method implements a generic insert and update logic for any SugarBean
     * This method only works for subclasses that implement the same variable names.
     * This method uses the presence of an id vtiger_field that is not null to signify and update.
     * The id vtiger_field should not be set otherwise.
     * todo - Add support for vtiger_field type validation and encoding of parameters.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
     */
	

	function save($module_name= '') 
	{
         global $adb; 
          global $current_user;
		$isUpdate = true;

		if(!isset($this->id) || $this->id == "")
		{
			$isUpdate = false;
		}

		if ( $this->new_with_id == true )
		{
			$isUpdate = false;
		}

		//$this->date_modified = $this->db->formatDate(date('YmdHis'));
		$this->date_modified = date('Y-m-d H:i:s');
		if (isset($current_user)) $this->modified_user_id = $current_user->id;
		
		if($isUpdate)
		{
    			$query = "Update ".$this->table_name." set ";
		}
		else
		{
    			//$this->date_entered = $this->db->formatDate(date('YmdHis'));
			$this->date_entered = date('Y-m-d H:i:s');

			if($this->new_schema && 
				$this->new_with_id == false)
			{
                          $this->id = $this->db->getUniqueID("vtiger_users");
			}
                        
			$query = "INSERT into ".$this->table_name;
		}
		// todo - add date modified to the list.

		// write out the SQL statement.
		//$query .= $this->table_name." set ";

		$firstPass = 0;
		$insKeys = '(';
		$insValues = '(';
		$insParams = array();
		$updKeyValues='';
		$updParams = array();
		foreach($this->column_fields as $field)
		{
			// Do not write out the id vtiger_field on the update statement.
			// We are not allowed to change ids.
			if($isUpdate && ('id' == $field))
				continue;

			// Only assign variables that have been set.
			if(isset($this->$field))
			{				
				// Try comparing this element with the head element.
				if(0 == $firstPass)
				{
					$firstPass = 1;
				}
				else
				{
					if($isUpdate)
					{
						$updKeyValues = $updKeyValues.", ";
					}
					else
					{
						$insKeys = $insKeys.", ";
						$insValues = $insValues.", ";
					}
				}
				/*else
					$query = $query.", ";
	
				$query = $query.$field."='".$adb->quote(from_html($this->$field,$isUpdate))."'";
				*/
				if($isUpdate)
				{
					$updKeyValues = $updKeyValues.$field."=?";
					array_push($updParams, from_html($this->$field,$isUpdate));
				}
				else
				{
					$insKeys = $insKeys.$field;
					$insValues = $insValues.'?';
					array_push($insParams, from_html($this->$field,$isUpdate));
				}
			}
		}

		if($isUpdate)
		{
			$query = $query.$updKeyValues." WHERE ID = ?";
			array_push($updParams, $this->id);
			$this->log->info("Update $this->object_name: ".$query);
			$this->db->pquery($query, $updParams, true);
		}
		else
		{
			$query = $query.$insKeys.") VALUES ".$insValues.")";
	        $this->log->info("Insert: ".$query);
			$this->db->pquery($query, $insParams, true);
		}

		// If this is not an update then store the id for later.
		if(!$isUpdate && !$this->new_schema && !$this->new_with_id)
		{
			$this->db->println("Illegal Access - SugarBean");
			//this is mysql specific
	        	$this->id = $this->db->getOne("SELECT LAST_INSERT_ID()" );
		}
	        
		return $this->id;
	}

    
    /**
     * This function retrieves a record of the appropriate type from the DB.
     * It fills in all of the vtiger_fields from the DB into the object it was called on.
     * param $id - If ID is specified, it overrides the current value of $this->id.  If not specified the current value of $this->id will be used.
     * returns this - The object that it was called apon or null if exactly 1 record was not found.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
	 function retrieve($id = -1, $encodeThis=true) {
		if ($id == -1) {
			$id = $this->id;
		}
		if($id == '') {
			return null;
		}
// GS porting vtiger_crmentity
$query = "SELECT * FROM $this->table_name WHERE $this->table_index = '$id'";
//		$query = "SELECT * FROM $this->table_name WHERE ID = '$id'";
		$this->log->debug("Retrieve $this->object_name: ".$query);

		$result =& $this->db->requireSingleResult($query, true, "Retrieving record by id $this->table_name:$id found ");

		if(empty($result))
		{
			return null;
		}
				
		$row = $this->db->fetchByAssoc($result, -1, $encodeThis);

		foreach($this->column_fields as $field)
		{
			if(isset($row[$field]))
			{
				$this->$field = $row[$field];
			}
		}
		return $this;
	}
     */

	function get_list($order_by = "", $where = "", $row_offset = 0, $limit=-1, $max=-1) {
		$this->log->debug("get_list:  order_by = '$order_by' and where = '$where' and limit = '$limit'");
		
		$query = $this->create_list_query($order_by, $where);
		
		return $this->process_list_query($query, $row_offset, $limit, $max);
	}

	/**
	 * This function returns a full (ie non-paged) list of the current object type.  
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	 */
	function get_full_list($order_by = "", $where = "") {
		$this->log->debug("get_full_list:  order_by = '$order_by' and where = '$where'");
		$query = "SELECT * FROM $this->table_name ";
		
		if($where != "")
			$query .= " where ($where) AND deleted=0";
		else
			$query .= " where deleted=0";

		if(!empty($order_by))
			$query .= " ORDER BY $order_by";

		$result =& $this->db->query($query, false);

		if($this->db->getRowCount($result) > 0){
		
			// We have some data.
			while ($row = $this->db->fetchByAssoc($result)) {
				foreach($this->list_fields as $field)
				{
					if (isset($row[$field])) {
						$this->$field = $row[$field];
						
						$this->log->debug("process_full_list: $this->object_name({$row['id']}): ".$field." = ".$this->$field);
					}
					else {
 	                                                $this->$field = '';   
					}
				}


				$list[] = clone($this);         //added clone tosupport PHP5
			}
		}

		if (isset($list)) return $list;
		else return null;

	}

	function create_list_query($order_by, $where)
	{
		$query = "SELECT * FROM $this->table_name ";
		
		if($where != "")
			$query .= " where ($where) AND deleted=0";
		else
			$query .= " where deleted=0";

		if(!empty($order_by))
			$query .= " ORDER BY $order_by";

		return $query;
	}
	

	function process_list_query($query, $row_offset, $limit= -1, $max_per_page = -1)
	{
		global $list_max_entries_per_page;
		$this->log->debug("process_list_query: ".$query);
		if(!empty($limit) && $limit != -1){
			$result =& $this->db->limitQuery($query, $row_offset + 0, $limit,true,"Error retrieving $this->object_name list: ");
		}else{
			$result =& $this->db->query($query,true,"Error retrieving $this->object_name list: ");
		}

		$list = Array();
		if($max_per_page == -1){
			$max_per_page 	= $list_max_entries_per_page;
		}
		$rows_found =  $this->db->getRowCount($result);

		$this->log->debug("Found $rows_found ".$this->object_name."s");
                
		$previous_offset = $row_offset - $max_per_page;
		$next_offset = $row_offset + $max_per_page;

		if($rows_found != 0)
		{

			// We have some data.

			for($index = $row_offset , $row = $this->db->fetchByAssoc($result, $index); $row && ($index < $row_offset + $max_per_page || $max_per_page == -99) ;$index++, $row = $this->db->fetchByAssoc($result, $index)){
				foreach($this->list_fields as $field)
				{
					if (isset($row[$field])) {
						$this->$field = $row[$field];
						
						
						$this->log->debug("$this->object_name({$row['id']}): ".$field." = ".$this->$field);
					}
					else 
					{
						$this->$field = "";
					}
				}


				$list[] = clone($this);   //added clone to support PHP5
			}
		}

		$response = Array();
		$response['list'] = $list;
		$response['row_count'] = $rows_found;
		$response['next_offset'] = $next_offset;
		$response['previous_offset'] = $previous_offset;

		return $response;
	}

	
	/**
	 * Track the viewing of a detail record.  This leverages get_summary_text() which is object specific
	 * params $user_id - The user that is viewing the record.
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	 */
	function track_view($user_id, $current_module,$id='')
	{
		$this->log->debug("About to call vtiger_tracker (user_id, module_name, item_id)($user_id, $current_module, $this->id)");

		$tracker = new Tracker();
		$tracker->track_view($user_id, $current_module, $id, '');
	}


	/** This function should be overridden in each module.  It marks an item as deleted.
	* If it is not overridden, then marking this type of item is not allowed
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	*/
	function mark_deleted($id)
	{
		$query = "UPDATE $this->table_name set deleted=1 where id=?";
		$this->db->pquery($query, array($id), true,"Error marking record deleted: ");

		$this->mark_relationships_deleted($id);

		// Take the item off of the recently viewed lists.
		$tracker = new Tracker();
		$tracker->delete_item_history($id);

	}


	/* This is to allow subclasses to fill in row specific columns of a list view form 
	function list_view_parse_additional_sections(&$list_form)
	{
	}
	*/
	/* This function assigns all of the values into the template for the list view 
	function get_list_view_array(){
		$return_array = Array();
		
		foreach($this->list_fields as $field)
		{
			$return_array[strtoupper($field)] = $this->$field;
		}
		
		return $return_array;	
	}
	function get_list_view_data()
	{
		
		return $this->get_list_view_array();
	}

	function get_where(&$fields_array)
	{ 
		$where_clause = "WHERE "; 
		$first = 1; 
		foreach ($fields_array as $name=>$value) 
		{ 
			if ($first) 
			{ 
				$first = 0;
			} 
			else 
			{ 
				$where_clause .= " AND ";
			} 

			$where_clause .= "$name = ".$adb->quote($value)."";
		} 

		$where_clause .= " AND deleted=0";
		return $where_clause;
	}


	function retrieve_by_string_fields($fields_array, $encode=true) 
	{ 
		$where_clause = $this->get_where($fields_array);
		
		$query = "SELECT * FROM $this->table_name $where_clause";
		$this->log->debug("Retrieve $this->object_name: ".$query);
		$result =& $this->db->requireSingleResult($query, true, "Retrieving record $where_clause:");
		if( empty($result)) 
		{ 
		 	return null; 
		} 

		 $row = $this->db->fetchByAssoc($result,-1, $encode);

		foreach($this->column_fields as $field) 
		{ 
			if(isset($row[$field])) 
			{ 
				$this->$field = $row[$field];
			}
		} 
		return $this;
	}

	// this method is called during an import before inserting a bean
	// define an associative array called $special_fields
	// the keys are user defined, and don't directly map to the bean's vtiger_fields
	// the value is the method name within that bean that will do extra
	// processing for that vtiger_field. example: 'full_name'=>'get_names_from_full_name'

	function process_special_fields() 
	{ 
		foreach ($this->special_functions as $func_name) 
		{ 
			if ( method_exists($this,$func_name) ) 
			{ 
				$this->$func_name(); 
			} 
		} 
	}

	*/
}


?>
