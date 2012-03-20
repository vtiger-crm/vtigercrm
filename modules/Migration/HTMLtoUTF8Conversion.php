<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
********************************************************************************/

_phpset_memorylimit_MB(32);
global $php_max_execution_time;
set_time_limit($php_max_execution_time);
global $adb,$dbname;

//echo '<div align = "center"><br><b>Started UTF8 Conversion</b><br></div>';

//echo '<table width="98%" border="1px" cellpadding="3" cellspacing="0" height="100%">';
//This function will convert all the html values in the database into utf-8 values
//echo '<tr width="100%"> <th colspan="3">Setting the character set as utf8 and collation as utf8_general_ci for all tables and Databases.</th></tr>';
$query = " ALTER DATABASE ".$dbname." DEFAULT CHARACTER SET utf8";
//echo "<tr><td colspan='3'>".$query."</td></tr>";
$adb->query($query);
$query = "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0";
//echo "<tr><td colspan='3'>".$query."</td></tr>";
$adb->query($query);
$tables_res = $adb->query("show tables");
while($row = $adb->fetch_array($tables_res))
{
	//echo "<tr>";
	$query =" LOCK TABLES `".$row[0]."` WRITE ";
	//echo "<td>LOCKING TABLE</td>";
	$adb->query($query);

	$query =" ALTER TABLE ".$row[0]." CONVERT TO CHARACTER SET  utf8 ";
	//echo "<td>$query</td>";
	$adb->query($query);
	
	$query =" UNLOCK TABLES ";
	//echo "<td>UNLOCKING TABLE</td>";
	$adb->query($query);
	
	//echo "</tr>";
}
$query = " SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS  ";
//echo "<tr><td colspan='3'>".$query."</td></tr>";
$adb->query($query);
//echo '</table>';

convert_html2utf8_db();

/**
* Function to convert html values to its original character available in a database
* This function can called at any time after the migration
* It get all the tables and its VARCHAR/TEXT/LONGTEXT fields from the DB
* Converts the html-values to its original character and restore it. 
**/
function convert_html2utf8_db()
{
	global $adb,$log;
	//Getting all the tables from the current database.
	$alltables = $adb->get_tables();
	$log->debug("Started HTML to UTF-8 Conversion");
	$values=Array();
	//Tables for which conversion to utf8 not required.
	$skip_tables=Array('vtiger_sharedcalendar', 'vtiger_potcompetitorrel', 'vtiger_users2group', 'vtiger_group2grouprel', 'vtiger_group2role', 'vtiger_group2rs', 'vtiger_campaigncontrel', 'vtiger_campaignleadrel', 'vtiger_cntactivityrel', 'vtiger_crmentitynotesrel', 'vtiger_salesmanactivityrel', 'vtiger_vendorcontactrel', 'vtiger_salesmanticketrel', 'vtiger_seactivityrel', 'vtiger_seticketsrel', 'vtiger_senotesrel', 'vtiger_profile2globalpermissions', 'vtiger_profile2standardpermissions', 'vtiger_profile2field', 'vtiger_role2profile', 'vtiger_profile2utility', 'vtiger_activityproductrel', 'vtiger_pricebookproductrel', 'vtiger_activity_reminder', 'vtiger_actionmapping', 'vtiger_org_share_action2tab', 'vtiger_datashare_relatedmodule_permission', 'vtiger_tmp_read_user_sharing_per', 'vtiger_tmp_read_group_sharing_per', 'vtiger_tmp_write_user_sharing_per', 'vtiger_tmp_write_group_sharing_per', 'vtiger_tmp_read_user_rel_sharing_per', 'vtiger_tmp_read_group_rel_sharing_per', 'vtiger_tmp_write_user_rel_sharing_per', 'vtiger_tmp_write_group_rel_sharing_per', 'vtiger_role2picklist', 'vtiger_freetagged_objects', 'vtiger_tab', 'vtiger_blocks', 'vtiger_group2role', 'vtiger_group2rs');

	//echo '<table width="98%" border="1px" cellpadding="3" cellspacing="0" height="100%">';
	//echo '<tr width="100%"> <th colspan="3">Started converting data from HTML to UTF-8</th></tr>';
	
	for($i=0;$i<count($alltables);$i++)
	{
		$table=$alltables[$i];
		if(!in_array($table,$skip_tables))
		{
			//Here selecting all the colums from the table
			$result = $adb->query("SHOW COLUMNS FROM $table");
			while ($row = $adb->fetch_array($result))
			{
				//Getting the primary key column of the table.
				if($row['key'] == 'PRI')
				{
					$values[$table]['key'][]=$row['field'];
				}
				//And Getting columns of type varchar, text and longtext.
				if(stristr($row['type'],'varchar') != '' || stristr($row['type'],'text') != '' || stristr($row['type'],'longtext') != '')
				{
					$values[$table]['columns'][] = $row['field'];
				}
			}
		}
	}

	$final_array=$values;
	foreach($final_array as $tablename=>$value)
	{
		//Going to update values in the table.
		$key = $value['key'];
		$cols = $value['columns'];
		if($cols != "" && $key != "")
		{
			if(count($key) > 1)
				$key_list = implode(", ", $key);
			else
				$key_list = $key[0];
				
			if(count($cols) > 1)
				$col_list = implode(", ", $cols);
			else
				$col_list = $cols[0];
			//Getting the records available in the table.
			$query="SELECT $key_list, $col_list FROM $tablename";
			$res1 = $adb->query($query);
			$val = Array();
			$id = Array();
		//	echo '
		//			<tr width="100%">
		//			<td width="80%">Updating the values in the table <b>'.$tablename.'</b></td>';
			$log->debug("Converting values in the table :".$tablename);
			//Sending the current status to the browser
			for($k=0; $k < $adb->num_rows($res1); $k++)
			{
				$whereStr = "";
				for($l=0; $l < count($key); $l++)
				{
					$id[$l] = $adb->query_result($res1, $k, $key[$l]);
					if($l != 0)
						$whereStr .= " and ";
					$whereStr .= $key[$l]."=?";
				}	
				$updateStr = "";
				for($j=0; $j < count($cols); $j++)
				{
					//Converting the html values to utf8 chars
					//echo "<br>Updating the value of ".$cols[$j]." column with utf8 value";
					$val[$j] = html_to_utf8(decode_html($adb->query_result($res1, $k, $cols[$j])));
					if($j != 0)
						$updateStr .= ", ";
					$updateStr .= $cols[$j]."=?";
				}
				$updateQ = "UPDATE $tablename SET $updateStr where $whereStr";
				$params = array($val, $id);
				$adb->pquery($updateQ, $params);
			}
		//	echo '
		//			<td width="20%">Completed</td>
		//			</tr>';
			//Sending the current status to the browser
		}
        }
//	echo '</table>';
//	echo '<div align = "center"><br><br><b> Conversion completed.</b></div>';
	$log->debug("HTML to UTF-8 Conversion has been completed");
}

?>
