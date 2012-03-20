<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('include/language/en_us.lang.php');
require_once('include/database/PearDatabase.php');
require_once('include/ComboStrings.php');
require_once('include/ComboUtil.php');
/**
 *  Class which handles the population of the combo values
 * 
 *
 */
class PopulateComboValues
{
	var $app_list_strings;


	/** 
	 * To populate the default combo values for the combo vtiger_tables
	 * @param $values -- values:: Type string array
	 * @param $tableName -- tablename:: Type string 
	 */
	function insertComboValues($values, $tableName,$picklistid)
	{
		global $log;
		$log->debug("Entering insertComboValues(".$values.", ".$tableName.") method ...");
		global $adb;
		//inserting the value in the vtiger_picklistvalues_seq for the getting uniqueID for each picklist values...
		$i=0;
		foreach ($values as $val => $cal)
		{
			$picklist_valueid = getUniquePicklistID();
			$id = $adb->getUniqueID('vtiger_'.$tableName);
			if($val != '')
			{
				$params = array($id, $val, 1, $picklist_valueid);
				$adb->pquery("insert into vtiger_$tableName values(?,?,?,?)", $params);
			}
			else
			{
				$params = array($id, '--None--', 1, $picklist_valueid);
				$adb->pquery("insert into vtiger_$tableName values(?,?,?,?)", $params);
			}

			//Default entries for role2picklist relation has been inserted..

			$sql="select roleid from vtiger_role";
			$role_result = $adb->pquery($sql, array());
			$numrow = $adb->num_rows($role_result);
			for($k=0; $k < $numrow; $k ++)
			{
				$roleid = $adb->query_result($role_result,$k,'roleid');
				$params = array($roleid, $picklist_valueid, $picklistid, $i);
				$adb->pquery("insert into vtiger_role2picklist values(?,?,?,?)", $params);
			}

			$i++;
		}
	

		$log->debug("Exiting insertComboValues method ...");
	}


	/** 
	 * To populate the combo vtiger_tables at startup time
	 */

	function create_tables () 
	{
		global $log;
		$log->debug("Entering create_tables () method ...");
				
		global $app_list_strings,$adb;
		global $combo_strings;
		$comboRes = $adb->query("SELECT distinct fieldname FROM vtiger_field WHERE uitype IN ('15') OR fieldname = 'salutationtype' and vtiger_field.presence in (0,2)");
		$noOfCombos = $adb->num_rows($comboRes);
		for($i=0; $i<$noOfCombos; $i++)
		{
			$comTab = $adb->query_result($comboRes, $i, 'fieldname');
			$picklistid = $adb->getUniqueID("vtiger_picklist");
			$params = array($picklistid, $comTab);
			$picklist_qry = "insert into vtiger_picklist values(?,?)";
			$adb->pquery($picklist_qry, $params);

			$this->insertComboValues($combo_strings[$comTab."_dom"],$comTab,$picklistid);
		}
		
		//we have to decide what are all the picklist and picklist values are non editable
		//presence = 0 means you cannot edit the picklist value
		//presence = 1 means you can edit the picklist value
		$noneditable_tables = Array("ticketstatus","taskstatus","eventstatus","faqstatus","quotestage","postatus","sostatus","invoicestatus","activitytype");
		$noneditable_values = Array(
						"Closed Won"=>"sales_stage",
						"Closed Lost"=>"sales_stage",
					   );
		foreach($noneditable_tables as $picklistname)
		{
			$adb->pquery("update vtiger_".$picklistname." set PRESENCE=0", array());
		}
		foreach($noneditable_values as $picklistname => $value)
		{
			$adb->pquery("update vtiger_$value set PRESENCE=0 where $value=?", array($picklistname));
		}

		$log->debug("Exiting create_tables () method ...");

	}


	function create_nonpicklist_tables ()
	{
		global $log;
		$log->debug("Entering create_nonpicklist_tables () method ...");
				
		global $app_list_strings,$adb;
		global $combo_strings;
		// uitype -> 16 - Non standard picklist, 115 - User status, 83 - Tax Class
		$comboRes = $adb->query("SELECT distinct fieldname FROM vtiger_field WHERE uitype IN ('16','115','83') AND fieldname NOT IN ('hdnTaxType','email_flag') and vtiger_field.presence in (0,2)");
		$noOfCombos = $adb->num_rows($comboRes);
		for($i=0; $i<$noOfCombos; $i++)
		{
			$comTab = $adb->query_result($comboRes, $i, 'fieldname');
			$this->insertNonPicklistValues($combo_strings[$comTab."_dom"],$comTab);
		}
		$log->debug("Exiting create_tables () method ...");
	}
	function insertNonPicklistValues($values, $tableName)
	{
		global $log;
		$log->debug("Entering insertNonPicklistValues(".$values.", ".$tableName.") method ...");
		global $adb;
		$i=0;
		foreach ($values as $val => $cal)
		{
				$id = $adb->getUniqueID('vtiger_'.$tableName);
				if($val != '')
				{
					$params = array($id, $val, $i ,1);
				}
				else
				{
					$params = array($id, '--None--', $i ,1);
				}
				$adb->pquery("insert into vtiger_$tableName values(?,?,?,?)", $params);
				$i++;
		}
		$log->debug("Exiting insertNonPicklistValues method ...");
	}

}
?>
