<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php');
$fld_module=vtlib_purify($_REQUEST["fld_module"]);
$tableName=vtlib_purify($_REQUEST["table_name"]);
$fldPickList =  vtlib_purify($_REQUEST['listarea']);
$roleid =  vtlib_purify($_REQUEST['roleid']);
//changed by dingjianting on 2006-10-1 for picklist editor
$fldPickList = utf8RawUrlDecode($fldPickList); 
$uitype = vtlib_purify($_REQUEST['uitype']);
global $adb, $default_charset;

$sql = "select picklistid from vtiger_picklist where name=?";
$picklistid = $adb->query_result($adb->pquery($sql, array($tableName)),0,'picklistid');

//Deleting the already existing values

$qry="select roleid,picklistvalueid from vtiger_role2picklist left join vtiger_$tableName on vtiger_$tableName.picklist_valueid=vtiger_role2picklist.picklistvalueid where roleid=? and picklistid=? and presence=1";
$res = $adb->pquery($qry, array($roleid, $picklistid));
$num_row = $adb->num_rows($res);
for($s=0;$s < $num_row; $s++)
{
	$valid = $adb->query_result($res,$s,'picklistvalueid');
	$sql="delete from vtiger_role2picklist where roleid=? and picklistvalueid=?";
	$adb->pquery($sql, array($roleid, $valid));
}

$pickArray = explode("\n",$fldPickList);
$count = count($pickArray);

$tabname=explode('cf_',$tableName);

if($tabname[1]!='')
       	$custom=true;

/* ticket2369 fixed */
$columnName = $tableName;
 for($i = 0; $i < $count; $i++)
 {
	 $pickArray[$i] = trim(from_html($pickArray[$i]));

	 //if UTF-8 character input given, when configuration is latin1, then avoid the entry which will cause mysql empty object exception in line 101
	 $stringConvert = function_exists(iconv) ? @iconv("UTF-8",$default_charset,$pickArray[$i]) : $pickArray[$i];
	 $pickArray[$i] = trim($stringConvert);
	 
	 if($pickArray[$i] != '')
	 {
		 $picklistcount=0;
		 //This uitype is for non-editable  picklist
		 $sql ="select $tableName from vtiger_$tableName";
		 $res = $adb->pquery($sql, array());
		 $numrow = $adb->num_rows($res);
		 for($x=0;$x < $numrow ; $x++)
		 {
			 $picklistvalues = decode_html($adb->query_result($res,$x,$tableName));

			 // Fix For: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/5129
			 global $current_language;
			 if($current_language != 'en_us') {
				 // Translate the value in database and compare with input.
				 if($fld_module == 'Events') $temp_module_strings = return_module_language($current_language, 'Calendar');
				 else $temp_module_strings = return_module_language($current_language, $fld_module);

				 $mod_picklistvalue = trim($temp_module_strings[$picklistvalues]);
				 if($mod_picklistvalue == $pickArray[$i]) {
					 $pickArray[$i] = $picklistvalues;
				 }
			 }
			 // End
			 
			 if($pickArray[$i] == $picklistvalues)
			 {
				 $picklistcount++;	
			 }

		 }

		 if($picklistcount == 0)
		 {	//Inserting a new pick list value to the corresponding picklist table
			 $picklistvalue_id = getUniquePicklistID();
			 $picklist_id = $adb->getUniqueID("vtiger_".$tableName);
			 $query = "insert into vtiger_".$tableName." values(?,?,?,?)";		
			 $params = array($picklist_id, $pickArray[$i], 1, $picklistvalue_id);
	
			 $adb->pquery($query, $params);

	 	}
	 	$picklistcount =0;
		$sql = "select picklist_valueid from vtiger_$tableName where $tableName=?";
		$pick_valueid = $adb->query_result($adb->pquery($sql, array($pickArray[$i])),0,'picklist_valueid');
		
		 //To get the max sortid for the non editable picklist and the inserting by increasing the sortid for editable values....
		 $sql ="select max(sortid)+1 as sortid from vtiger_role2picklist left join vtiger_$tableName on vtiger_$tableName.picklist_valueid=vtiger_role2picklist.picklistvalueid where roleid=? and picklistid=?  and presence=0";
		 $sortid = $adb->query_result($adb->pquery($sql, array($roleid, $picklistid)),0,'sortid');

		 $sql = "insert into vtiger_role2picklist values(?,?,?,?)";
		 $adb->pquery($sql, array($roleid, $pick_valueid, $picklistid, $sortid));
 	}
} 

header("Location:index.php?action=SettingsAjax&module=Settings&directmode=ajax&file=PickList&fld_module=".$fld_module."&roleid=".$roleid);
?>