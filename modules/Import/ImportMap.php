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
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('data/SugarBean.php');

// Contact is used to store customer information.
class ImportMap extends SugarBean 
{
	var $log;
	var $db;

	// Stored vtiger_fields
	var $id;
	var $name;
	var $module;
	var $content;
	var $has_header;
	var $deleted;
	var $date_entered;
	var $date_modified;
	var $assigned_user_id;
	var $is_published;

	var $table_name = "vtiger_import_maps";
	var $table_index= 'id';
	var $object_name = "ImportMap";
	
	var $tab_name_index = Array("import_maps"=>"id");
	var $new_schema = true;

	var $column_fields = Array("id"
		,"name"
		,"module"
		,"content"
		,"has_header"
                ,"deleted"
                ,"date_entered"
                ,"date_modified"
                ,"assigned_user_id"
		,"is_published"
		);

	/**	Constructor
	 */
	function ImportMap() 
	{
		$this->log = LoggerManager::getLogger('file');
		$this->db = PearDatabase::getInstance();
	}

	/**	function used to get the id, name, module and content as string
	 *	@return string Object:ImportMap id=$this->id name=$this->name module=$this->module content=$this->content
	 */
	function toString()
	{
		return "Object:ImportMap id=$this->id name=$this->name module=$this->module content=$this->content";
	}

	/**	function used to save the mapping 
	 *	@param int $owner_id - user id who is the owner for this mapping
	 *	@param string $name - name of the mapping
	 *	@param string $module - module name in which we have saved the mapping
	 *	@param string $has_header - has_header value
	 *	@param string $content - all fields which are concatenated with & symbol
	 *	@return int $result - return 1 if the mapping contents updated
	 */
        function save_map( $owner_id, $name, $module, $has_header,$content )
        {
		$query_arr = array('assigned_user_id'=>$owner_id,'name'=>$name);

		//$this->retrieve_by_string_fields($query_arr, false);

                $result = 1;
                $this->assigned_user_id = $owner_id;

                $this->name = $name;
                $this->module = $module;

		$this->content = "".$this->db->getEmptyBlob()."";
                $this->has_header = $has_header;
                $this->deleted = 0;

		//check whether this map name is already exist, if yes then overwrite the existing one, else create new
	        $res = $this->db->pquery("select id from vtiger_import_maps where name=?", array(trim($name)));
		if($this->db->num_rows($res) > 0)
		{
			$this->id = $this->db->query_result($res,0,'id');
		}

                $returnid = $this->save();

		$this->db->updateBlob($this->table_name,"content","name='". $this->db->sql_escape_string($name)."' and module='".$module."'",$content);

                return $result;
        }

	/**	function used to publish or unpublish the mapping
	 *	@param int $user_id - user id who is publishing the map
	 *	@param string $flag - yes or no
	 *	@return value - if flag is yes then update the db and return 1 otherwise return -1
	 */
        function mark_published($user_id,$flag)
        {
		$other_map = new ImportMap();

		if ($flag == 'yes')
		{
			// if you are trying to publish your map
			// but there's another published map
			// by the same name
			
			$query_arr = array('name'=>$this->name,
					'is_published'=>'yes');
		} 
		else 
		{
			// if you are trying to unpublish a map
			// but you own an unpublished map by the same name
			$query_arr = array('name'=>$this->name,
					'assigned_user_id'=>$user_id,
					'is_published'=>'no');
		}
		$other_map->retrieve_by_string_fields($query_arr, false);

		if ( isset($other_map->id) )
		{
			//.. don't do it!
			return -1;	
		}

                $query = "UPDATE $this->table_name set is_published=?, assigned_user_id=? where id=?";
				$params = array($flag, $user_id, $this->id);
                $this->db->pquery($query,$params,true,"Error marking import map published: ");
		return 1;
        }


	/**	function to retrieve all the column fields and set as properties
	 *	@param array $fields_array - fields array of the corresponding module
	 *	@return array $obj_arr - return an array which contains the retrieved column_field values as properties
	 */
	function retrieve_all_by_string_fields($fields_array) 
	{ 
		$where_clause = $this->get_where($fields_array);
		$query = "SELECT * FROM $this->table_name $where_clause";
		$this->log->debug("Retrieve $this->object_name: ".$query);
		$result = & $this->db->query($query,true," Error: ");
		$obj_arr = array();

		while ($row = $this->db->fetchByAssoc($result,-1,FALSE) )
		{	
			$focus = new ImportMap();

			foreach($this->column_fields as $field) 
			{ 
				if(isset($row[$field])) 
				{ 
					$focus->$field = $row[$field];
				} 
			} 
			array_push($obj_arr,$focus);
		}
		return $obj_arr;
	}

	/**	function used to get the list of saved mappings
	 *	@param string $module - module name which we currently importing
	 *	@return array $map_lists - return the list of mappings in the format of [id]=>name
	 */
	function getSavedMappingsList($module)
	{
		$query = "SELECT * FROM $this->table_name where module=? and deleted=0";
		$result = $this->db->pquery($query,array($module),true," Error: ");
		$map_lists = array();

		while ($row = $this->db->fetchByAssoc($result,-1,FALSE) )
		{	
			$map_lists[$row['id']] = $row['name'];
		}
		return $map_lists;
	}

	/**	function used to retrieve the mapping content for the passed mapid
	 *	@param int $mapid - mapid for the selected map
	 *	@return array $mapping_arr - return the array which contains the mapping_arr[name]=value from the content of the map
	 */
	function getSavedMappingContent($mapid)
	{
		$query = "SELECT * FROM $this->table_name where id=? and deleted=0";
		$result = $this->db->pquery($query,array($mapid),true," Error: ");
		$mapping_arr = array();

		$pairs = split("&",$this->db->query_result($result,0,'content'));
		foreach ($pairs as $pair)
		{
			list($name,$value) = split("=",$pair);
			$mapping_arr["$name"] = $value;
		}

		return $mapping_arr;
	}

}


?>
