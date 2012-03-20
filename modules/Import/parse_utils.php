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

// takes a string and parses it into one record per line,
// one vtiger_field per delimiter, to a maximum number of lines
// some vtiger_files have a header, some dont.
// keeps track of which vtiger_fields are used

/**	function used to parse the file 
 *	@param string $file_name - file name 
 *	@param character $delimiter - delimiter of the csv file
 *	@param int $max_lines - maximum number of lines to parse
 *	@param int $has_header - if the file has header then 1 otherwise 0
 *	@return array $ret_array - return an array which will be "rows"=>&$rows, "field_count"=>$field_count where as &rows is the reference of rows which contains all the parsed rows and $field_count is the number of fields available per row
 */
function parse_import($file_name,$delimiter,$max_lines,$has_header)
{
	$line_count = 0;

	$field_count = 0;

	$rows = array();

	if (! file_exists($file_name))
	{
		return -1;
	}

	$fh = fopen($file_name,"r");

	if (! $fh)
	{
		return -1;
	}

	while ( (( $fields = fgetcsv($fh, 4096, $delimiter) ) !== FALSE) 
		&& ( $max_lines == -1 || $line_count < $max_lines)) 
	{

		if ( count($fields) == 1 && isset($fields[0]) && $fields[0] == '')
		{
			break;
		}
		$this_field_count = count($fields);

		//Added to handle the case where the last value in a row is "" and  
		//the field value in the next row is "" then for some reason these rows 
 		//are getting parsed as same row, this does not happen if the line seperator is  
		// linux line endings. 
		$matches = array();  
		preg_match("/^''\s*''$/",$fields[$field_count - 1],$matches); 
		if(($this_field_count + 1)/2 == $field_count && count($matches) > 0){ 
			$chunks = array_chunk($fields,$field_count); 
			$fields = $chunks[0]; 
			array_push($rows,$chunks[1]); 
			$line_count++; 
		}else{
			$field_count = $this_field_count;
		}

		array_push($rows,$fields);

		$line_count++;

	}

	// got no rows
	if ( count($rows) == 0)
	{
		return -3;
	}

	$ret_array = array(
		"rows"=>&$rows,
		"field_count"=>$field_count
	);

	return $ret_array;

}

/**	function used to parse the act file 
 *	@param string $file_name - file name 
 *	@param character $delimiter - delimiter of the csv file
 *	@param int $max_lines - maximum number of lines to parse
 *	@param int $has_header - if the file has header then 1 otherwise 0
 *	@return array $ret_array - return an array which will be "rows"=>&$rows, "field_count"=>$field_count where as &rows is the reference of rows which contains all the parsed rows and $field_count is the number of fields available per row
 */
function parse_import_act($file_name,$delimiter,$max_lines,$has_header)
{
	$line_count = 0;

	$field_count = 0;

	$rows = array();

	if (! file_exists($file_name))
	{
		return -1;
	}

	$fh = fopen($file_name,"r");

	if (! $fh)
	{
		return -1;
	}

	while ( ($line = fgets($fh, 4096))
                && ( $max_lines == -1 || $line_count < $max_lines) )

	{
		
		$line = trim($line);
		$line = substr_replace($line,"",0,1);
		$line = substr_replace($line,"",-1);
		$fields = explode("\",\"",$line);

		$this_field_count = count($fields);

		if ( $this_field_count > $field_count)
		{
			$field_count = $this_field_count;
		}

		array_push($rows,$fields);

		$line_count++;

	}

	// got no rows
	if ( count($rows) == 0)
	{
		return -3;
	}

	$ret_array = array(
		"rows"=>&$rows,
		"field_count"=>$field_count
	);

	return $ret_array;

}
?>
