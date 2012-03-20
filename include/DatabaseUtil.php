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

//Added to check database charset and $default_charset are set to UTF8.
//If both are not set to be UTF-8, Then we will show an alert message.
function check_db_utf8_support($conn) {
	global $db_type;
	if($db_type == 'pgsql')
		return true;
	$dbvarRS = &$conn->Execute("show variables like '%_database' ");
	$db_character_set = null;
   	$db_collation_type = null;
	while(!$dbvarRS->EOF) {
		$arr = $dbvarRS->FetchRow();
		$arr = array_change_key_case($arr);
		switch($arr['variable_name']) {
			case 'character_set_database' : $db_character_set = $arr['value']; break;
			case 'collation_database'     : $db_collation_type = $arr['value']; break;
		}
		// If we have all the required information break the loop.
		if($db_character_set != null && $db_collation_type != null) break;
	}
	return (stristr($db_character_set, 'utf8') && stristr($db_collation_type, 'utf8'));
}

function get_db_charset($conn) {
	global $db_type;
	if($db_type == 'pgsql')
		return 'UTF8';
	$dbvarRS = &$conn->query("show variables like '%_database' "); 
	$db_character_set = null; 
	while(!$dbvarRS->EOF) { 
		$arr = $dbvarRS->FetchRow(); 
		$arr = array_change_key_case($arr); 
		if($arr['variable_name'] == 'character_set_database') {
			$db_character_set = $arr['value']; 
			break;
		}
	}	
	return $db_character_set;
}

//Make a count query
function mkCountQuery($query)
{
    // Remove all the \n, \r and white spaces to keep the space between the words consistent. 
    // This is required for proper pattern matching for words like ' FROM ', 'ORDER BY', 'GROUP BY' as they depend on the spaces between the words.
    $query = preg_replace("/[\n\r\s]+/"," ",$query);
    
    //Strip of the current SELECT fields and replace them by "select count(*) as count"
    // Space across FROM has to be retained here so that we do not have a clash with string "from" found in select clause
    $query = "SELECT count(*) AS count ".substr($query, stripos($query,' FROM '),strlen($query));

    //Strip of any "GROUP BY" clause
    if(stripos($query,'GROUP BY') > 0)
	$query = substr($query, 0, stripos($query,'GROUP BY'));

    //Strip of any "ORDER BY" clause
    if(stripos($query,'ORDER BY') > 0)
	$query = substr($query, 0, stripos($query,'ORDER BY'));

    //That's it
    return( $query);
}

//Added for PHP version less than 5
if (!function_exists("stripos"))
{
	function stripos($query,$needle)
	{
		return strpos(strtolower($query),strtolower($needle));
	}
}

?>
