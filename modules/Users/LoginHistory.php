<?php

/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
* 
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/database/PearDatabase.php');

/** This class is used to store and display the login history of all the Users.
  * An Admin User can view his login history details  and of all the other users as well.
  * StandardUser is allowed to view only his login history details.
**/
class LoginHistory {
	var $log;
	var $db;

	// Stored vtiger_fields
	var $login_id;
	var $user_name;
	var $user_ip;
	var $login_time;
	var $logout_time;
	var $status;
	var $module_name = "Users";

	var $table_name = "vtiger_loginhistory";

	var $object_name = "LoginHistory";
	
	var $new_schema = true;

	var $column_fields = Array("id"
		,"login_id"
		,"user_name"
		,"user_ip"
		,"login_time"
		,"logout_time"
		,"status"
		);
	
	function LoginHistory() {
		$this->log = LoggerManager::getLogger('loginhistory');
		$this->db = PearDatabase::getInstance();
	}
	
	var $sortby_fields = Array('user_name', 'user_ip', 'login_time', 'logout_time', 'status');	 
       	
	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
			'User Name'=>Array('vtiger_loginhistory'=>'user_name'), 
			'User IP'=>Array('vtiger_loginhistory'=>'user_ip'), 
			'Signin Time'=>Array('vtiger_loginhistory'=>'login_time'),
		        'Signout Time'=>Array('vtiger_loginhistory'=>'logout_time'), 
			'Status'=>Array('vtiger_loginhistory'=>'status'),
		);	
	
	var $list_fields_name = Array(
		'User Name'=>'user_name',
		'User IP'=>'user_ip',
		'Signin Time'=>'login_time',
		'Signout Time'=>'logout_time',
		'Status'=>'status'
		);	
	var $default_order_by = "login_time";
	var $default_sort_order = 'DESC';

/**
 * Function to get the Header values of Login History.
 * Returns Header Values like UserName, IP, LoginTime etc in an array format.
**/
	function getHistoryListViewHeader()
	{
		global $log;
		$log->debug("Entering getHistoryListViewHeader method ...");
		global $app_strings;
		
		$header_array = array($app_strings['LBL_LIST_USER_NAME'], $app_strings['LBL_LIST_USERIP'], $app_strings['LBL_LIST_SIGNIN'], $app_strings['LBL_LIST_SIGNOUT'], $app_strings['LBL_LIST_STATUS']);

		$log->debug("Exiting getHistoryListViewHeader method ...");
		return $header_array;
		
	}

/**
  * Function to get the Login History values of the User.
  * @param $navigation_array - Array values to navigate through the number of entries.
  * @param $sortorder - DESC
  * @param $orderby - login_time
  * Returns the login history entries in an array format.
**/
	function getHistoryListViewEntries($username, $navigation_array, $sorder='', $orderby='')
	{
		global $log;
		$log->debug("Entering getHistoryListViewEntries() method ...");
		global $adb, $current_user;	
		
		if($sorder != '' && $order_by != '')
	       		$list_query = "Select * from vtiger_loginhistory where user_name=? order by ".$order_by." ".$sorder;
		else
				$list_query = "Select * from vtiger_loginhistory where user_name=? order by ".$this->default_order_by." ".$this->default_sort_order;

		$result = $adb->pquery($list_query, array($username));
		$entries_list = array();
		
	if($navigation_array['end_val'] != 0)
	{
		for($i = $navigation_array['start']; $i <= $navigation_array['end_val']; $i++)
		{
			$entries = array();
			$loginid = $adb->query_result($result, $i-1, 'login_id');

			$entries[] = $adb->query_result($result, $i-1, 'user_name');
			$entries[] = $adb->query_result($result, $i-1, 'user_ip');
			$entries[] = $adb->query_result($result, $i-1, 'login_time');
			$entries[] = $adb->query_result($result, $i-1, 'logout_time');
			$entries[] = $adb->query_result($result, $i-1, 'status');

			$entries_list[] = $entries;
		}	
		$log->debug("Exiting getHistoryListViewEntries() method ...");
		return $entries_list;
	}	
	}
	
	/** Function that Records the Login info of the User 
	 *  @param ref variable $usname :: Type varchar
	 *  @param ref variable $usip :: Type varchar
	 *  @param ref variable $intime :: Type timestamp
	 *  Returns the query result which contains the details of User Login Info
	*/
	function user_login(&$usname,&$usip,&$intime)
	{
		global $adb;
		//Kiran: Setting logout time to '0000-00-00 00:00:00' instead of null
		$query = "Insert into vtiger_loginhistory (user_name, user_ip, logout_time, login_time, status) values (?,?,?,?,?)";
		$params = array($usname,$usip,'0000-00-00 00:00:00', $this->db->formatDate($intime, true),'Signed in');
		$result = $adb->pquery($query, $params)
                        or die("MySQL error: ".mysql_error());
		
		return $result;
	}
	
	/** Function that Records the Logout info of the User 
	 *  @param ref variable $usname :: Type varchar
	 *  @param ref variable $usip :: Type varchar
	 *  @param ref variable $outime :: Type timestamp
	 *  Returns the query result which contains the details of User Logout Info
	*/
	function user_logout(&$usname,&$usip,&$outtime)
	{
		global $adb;
		$logid_qry = "SELECT max(login_id) AS login_id from vtiger_loginhistory where user_name=? and user_ip=?";
		$result = $adb->pquery($logid_qry, array($usname, $usip));
		$loginid = $adb->query_result($result,0,"login_id");
		if ($loginid == '')
                {
                        return;
                }
		// update the user login info.
		$query = "Update vtiger_loginhistory set logout_time =?, status=? where login_id = ?";
		$result = $adb->pquery($query, array($this->db->formatDate($outtime, true), 'Signed off', $loginid))
                        or die("MySQL error: ".mysql_error());
	}

	/** Function to create list query 
	* @param reference variable - order by is passed when the query is executed
	* @param reference variable - where condition is passed when the query is executed
	* Returns Query.
	*/
  	function create_list_query(&$order_by, &$where)
  	{
		// Determine if the vtiger_account name is present in the where clause.
		global $current_user, $adb;
		$query = "SELECT user_name,user_ip, status,
				".$this->db->getDBDateString("login_time")." AS login_time,
				".$this->db->getDBDateString("logout_time")." AS logout_time
			FROM ".$this->table_name;
		if($where != "")
		{
			if(!is_admin($current_user))
			$where .=" AND user_name = '". $adb->sql_escape_string($current_user->user_name) ."'";
			$query .= " WHERE ($where)";
		}
		else
		{
			if(!is_admin($current_user))
			$query .= " WHERE user_name = '". $adb->sql_escape_string($current_user->user_name) ."'";
		}
		
		if(!empty($order_by))
			$query .= " ORDER BY ". $adb->sql_escape_string($order_by);
        
		return $query;
	}

	/**
	 * Determine if the user has logged-in first
	 * @param accept_delay_seconds Allow the delay (in seconds) between login_time recorded and current time as first time.
	 * This will be helpful if login is performed and client is redirected for home page where this function is invoked.
	 */
	static function firstTimeLoggedIn($user_name, $accept_delay_seconds=10) {		
		$firstTimeLoginStatus = false;
		
		global $adb;
		
		// Search for at-least two records.
		$query = 'SELECT login_time, logout_time FROM vtiger_loginhistory WHERE user_name=? ORDER BY login_id DESC LIMIT 2';
		$result= $adb->pquery($query, array($user_name));
		$recordCount = $result? $adb->num_rows($result) : 0;
		
		if ($recordCount === 0) {
			$firstTimeLoginStatus = true;
		} else {		
			if ($recordCount == 1) { // Only first time?
				$row = $adb->fetch_array($result);
				$login_delay = time() - strtotime($row['login_time']);
				// User not logged out and is within expected delay?			
				if (strcmp('0000-00-00 00:00:00', $row['logout_time']) === 0 && $login_delay < $accept_delay_seconds) {
					$firstTimeLoginStatus = true;
				}				
			}
		}
		return $firstTimeLoginStatus;
	}
}



?>
