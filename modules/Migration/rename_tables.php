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

global $conn;
global $migrationlog;
$prefix = "vtiger_";

$migrationlog->debug("Inside the file rename_tables.php to rename the table names with prefix vtiger_");

//Rename all the tables with prefix vtiger_
$tables_list = $conn->get_tables();
foreach($tables_list as $index => $tablename)
{
	$sql = "rename table $tablename to $prefix$tablename";
	Execute($sql);
}
//Table renaming ends.


//In these following tablename => field we have to add the prefix vtiger_ as they are the tablenames
$change_cols_array = Array(
				"cvcolumnlist"=>"columnname",
				"cvstdfilter"=>"columnname",
				"cvadvfilter"=>"columnname",
				"selectcolumn"=>"columnname",
				"relcriteria"=>"columnname",
				"reportsortcol"=>"columnname",
				"reportdatefilter"=>"datecolumnname",
				"reportsummary"=>"columnname",
				"field"=>"tablename",
			  );

foreach($change_cols_array as $tablename => $columnname)
{
	$result = $conn->query("select $columnname from $prefix$tablename");

	while($row = $conn->fetch_row($result))
	{
		if((!strstr($row[$columnname],$prefix)) && $row[$columnname] !='' && $row[$columnname] != 'none')
		{
			//for reportsummary we should add prefix in second value ie., after first :(semicolon)
			if($tablename == 'reportsummary')
			{
				$queries_list .= "update $prefix$tablename set $columnname=\"".str_replace("cb:","cb:$prefix",$row[$columnname])."\" where $columnname=\"$row[$columnname]\"&&##";
			}
			else
			{
				$queries_list .= "update $prefix$tablename set $columnname=\"$prefix$row[$columnname]\" where $columnname=\"$row[$columnname]\"&&##";
			}
		}
	}
}

$queries_array = explode("&&##",trim($queries_list,"&&##"));

foreach($queries_array as $index => $query)
{
	Execute($query);
}


$migrationlog->debug("End of file rename_tables.php. The table names renamed with prefix vtiger_");
?>
