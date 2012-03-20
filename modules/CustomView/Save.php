<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/logging.php');
require_once('include/utils/utils.php');
global $adb;
global $log, $current_user;

$cvid = (int) vtlib_purify($_REQUEST["record"]);
$cvmodule = vtlib_purify($_REQUEST["cvmodule"]);
$parenttab = getParentTab();
$return_action = vtlib_purify($_REQUEST["return_action"]);
if($cvmodule != "") {
	$cv_tabid = getTabid($cvmodule);
	$viewname = vtlib_purify($_REQUEST["viewName"]);
	if(strtolower($default_charset) != 'utf-8')
		$viewname = htmlentities($viewname);

	//setStatus=0(Default);1(Private);2(Pending);3(Public).
	//If status is Private ie. 1, only the user created the customview can see it
	//If status is Pending ie. 2, on approval by the admin, the status will become Public ie. 3 and a user can see the customviews created by him and his sub-ordinates.
	if(isset($_REQUEST['setStatus']) && $_REQUEST['setStatus'] != '' && $_REQUEST['setStatus'] != '1')
		$status = $_REQUEST['setStatus'];
	elseif(isset($_REQUEST['setStatus']) && $_REQUEST['setStatus'] != '' && $_REQUEST['setStatus'] == '1')
		$status = CV_STATUS_PENDING;
	else
		$status = CV_STATUS_PRIVATE;
	
	$userid = $current_user->id;

	if(isset($_REQUEST["setDefault"])) {
	  $setdefault = 1;
	} else {
	  $setdefault = 0;
	}

	if(isset($_REQUEST["setMetrics"])) {
		$setmetrics = 1;
	} else {
		$setmetrics = 0;
	}

 	//$allKeys = array_keys($HTTP_POST_VARS);
	//this is  will cause only the chosen fields to be added to the vtiger_cvcolumnlist table 
	$allKeys = array_keys($_REQUEST); 

	//<<<<<<<columns>>>>>>>>>>
	for ($i=0;$i<count($allKeys);$i++) {
	   $string = substr($allKeys[$i], 0, 6);
	   if($string == "column") {
		   //the contusion, will cause only the chosen fields to be added to the vtiger_cvcolumnlist table 
		   if($_REQUEST[$allKeys[$i]] != "") 
        	   $columnslist[] = $_REQUEST[$allKeys[$i]];
   	   }
	}
	//<<<<<<<columns>>>>>>>>>

	//<<<<<<<standardfilters>>>>>>>>>
	$stdfiltercolumn = $_REQUEST["stdDateFilterField"];
	$std_filter_list["columnname"] = $stdfiltercolumn;
	$stdcriteria = $_REQUEST["stdDateFilter"];
	$std_filter_list["stdfilter"] = $stdcriteria;
	$startdate = $_REQUEST["startdate"];
	$enddate = $_REQUEST["enddate"];
	if($stdcriteria == "custom") {
		$startdate = getDBInsertDateValue($startdate);
		$enddate = getDBInsertDateValue($enddate);
	}
	$std_filter_list["startdate"] = $startdate;
	$std_filter_list["enddate"]=$enddate;
	if(empty($startdate) && empty($enddate))
		unset($std_filter_list);
	//<<<<<<<standardfilters>>>>>>>>>

	//<<<<<<<advancedfilter>>>>>>>>>
	for ($i=0;$i<count($allKeys);$i++) {
	   $string = substr($allKeys[$i], 0, 4);
		if($string == "fcol" && $_REQUEST[$allKeys[$i]] !== null &&
				$_REQUEST[$allKeys[$i]] !== '') {
           	$adv_filter_col[] = $_REQUEST[$allKeys[$i]];
   	   }
	}
	for ($i=0;$i<count($allKeys);$i++) {
	   $string = substr($allKeys[$i], 0, 3);
		if($string == "fop" && $_REQUEST[$allKeys[$i]] !== null &&
				$_REQUEST[$allKeys[$i]] !== '') {
           	$adv_filter_option[] = $_REQUEST[$allKeys[$i]];
   	   }
	}
	for ($i=0;$i<count($allKeys);$i++) {
   	   $string = substr($allKeys[$i], 0, 4);
		if($string == "fval"  && $_REQUEST[$allKeys[$i]] !== null &&
				$_REQUEST[$allKeys[$i]] !== '') {
		   $adv_filter_value[] = trim(vtlib_purify($_REQUEST[$allKeys[$i]]));
   	   }
	}
	//<<<<<<<advancedfilter>>>>>>>>

	if(!$cvid) {
		$genCVid = $adb->getUniqueID("vtiger_customview");
		if($genCVid != "") {
			$customviewsql = "INSERT INTO vtiger_customview(cvid, viewname, setdefault, setmetrics, entitytype, status, userid) 
				VALUES (?,?,?,?,?,?,?)";
			$customviewparams = array($genCVid, $viewname, 0, $setmetrics, $cvmodule, $status, $userid);
			$customviewresult = $adb->pquery($customviewsql, $customviewparams);
			$log->info("CustomView :: Save :: vtiger_customview created successfully");
						
			if($setdefault == 1) {
				$sql_result = $adb->pquery("SELECT * FROM vtiger_user_module_preferences WHERE userid = ? and tabid = ?",array($current_user->id, $cv_tabid));
				if($adb->num_rows($sql_result) > 0) {
					$updatedefaultsql = "UPDATE vtiger_user_module_preferences SET default_cvid = ? WHERE userid = ? and tabid = ?";
					$updatedefaultresult = $adb->pquery($updatedefaultsql, array($genCVid, $current_user->id, $cv_tabid));
				} else {
					$insertdefaultsql = "INSERT INTO vtiger_user_module_preferences(userid, tabid, default_cvid) values (?,?,?)";
					$insertdefaultresult = $adb->pquery($insertdefaultsql, array($userid, $cv_tabid, $genCVid));
				}
			} else {
				$sql_result = $adb->pquery("SELECT * FROM vtiger_user_module_preferences WHERE userid = ? and tabid = ?",array($current_user->id, $cv_tabid));
				if($adb->num_rows($sql_result) > 0) {
					$deletedefaultsql = "DELETE FROM vtiger_user_module_preferences WHERE userid = ? and tabid = ?";
					$deletedefaultresult = $adb->pquery($deletedefaultsql, array($current_user->id, $cv_tabid));
				}
			}
			
			$log->info("CustomView :: Save :: setdefault upated successfully");
			
			if($customviewresult) {
				if(isset($columnslist)) {
					for($i=0;$i<count($columnslist);$i++) {
						$columnsql = "INSERT INTO vtiger_cvcolumnlist (cvid, columnindex, columnname) VALUES (?,?,?)";
						$columnparams = array($genCVid, $i, $columnslist[$i]);
						$columnresult = $adb->pquery($columnsql, $columnparams);
					}
					$log->info("CustomView :: Save :: vtiger_cvcolumnlist created successfully");
					if($std_filter_list["columnname"] !="") {
						$stdfiltersql = "INSERT INTO vtiger_cvstdfilter(cvid,columnname,stdfilter,startdate,enddate) VALUES (?,?,?,?,?)";
						$stdfilterparams = array($genCVid, $std_filter_list["columnname"], $std_filter_list["stdfilter"], $adb->formatDate($std_filter_list["startdate"], true), $adb->formatDate($std_filter_list["enddate"], true));
						$stdfilterresult = $adb->pquery($stdfiltersql, $stdfilterparams);
						$log->info("CustomView :: Save :: vtiger_cvstdfilter created successfully");
					}
					for($i=0;$i<count($adv_filter_col);$i++) {
						$col = explode(":",$adv_filter_col[$i]);
						$temp_val = explode(",",$adv_filter_value[$i]);
						if($col[4] == 'D' || ($col[4] == 'T' && $col[1] != 'time_start' && $col[1] != 'time_end') || $col[4] == 'DT') {
							$val = Array();
							for($x=0;$x<count($temp_val);$x++) {
								//if date and time given then we have to convert the date and leave the time as it is, if date only given then temp_time value will be empty
								list($temp_date,$temp_time) = explode(" ",$temp_val[$x]);
								$temp_date = getDBInsertDateValue(trim($temp_date));
								if(trim($temp_time) != '')
									$temp_date .= ' '.$temp_time;
								$val[$x] = $temp_date;
							}
							$adv_filter_value[$i] = implode(", ",$val);
						}
						$advfiltersql = "INSERT INTO vtiger_cvadvfilter(cvid,columnindex,columnname,comparator,value) VALUES (?,?,?,?,?)";
						$advfilterparams = array($genCVid, $i, $adv_filter_col[$i], $adv_filter_option[$i], $adv_filter_value[$i]);
						$advfilterresult = $adb->pquery($advfiltersql, $advfilterparams);
					}
					$log->info("CustomView :: Save :: vtiger_cvadvfilter created successfully");
				}
			}
			$cvid = $genCVid;
		}
	} else {
		if($is_admin == true || $current_user->id) {
			$updatecvsql = "UPDATE vtiger_customview
					SET viewname = ?, setmetrics = ?, status = ? WHERE cvid = ?";
			$updatecvparams = array($viewname, $setmetrics, $status, $cvid);
			$updatecvresult = $adb->pquery($updatecvsql, $updatecvparams);
			$log->info("CustomView :: Save :: vtiger_customview upated successfully".$cvid);
			
			if($setdefault == 1) {
				$sql_result = $adb->pquery("SELECT * FROM vtiger_user_module_preferences WHERE userid = ? and tabid = ?",array($current_user->id, $cv_tabid));
				if($adb->num_rows($sql_result) > 0) {
					$updatedefaultsql = "UPDATE vtiger_user_module_preferences SET default_cvid = ? WHERE userid = ? and tabid = ?";
					$updatedefaultresult = $adb->pquery($updatedefaultsql, array($cvid, $current_user->id, $cv_tabid));
				} else {
					$insertdefaultsql = "INSERT INTO vtiger_user_module_preferences(userid, tabid, default_cvid) values (?,?,?)";
					$insertdefaultresult = $adb->pquery($insertdefaultsql, array($userid, $cv_tabid, $cvid));
				}
			} else {
				$sql_result = $adb->pquery("SELECT * FROM vtiger_user_module_preferences WHERE userid = ? and tabid = ?",array($current_user->id, $cv_tabid));
				if($adb->num_rows($sql_result) > 0) {
					$deletedefaultsql = "DELETE FROM vtiger_user_module_preferences WHERE userid = ? and tabid = ?";
					$deletedefaultresult = $adb->pquery($deletedefaultsql, array($current_user->id, $cv_tabid));
				}
			}
			$log->info("CustomView :: Save :: setdefault upated successfully".$cvid);
			
			$deletesql = "DELETE FROM vtiger_cvcolumnlist WHERE cvid = ?";
			$deleteresult = $adb->pquery($deletesql, array($cvid));
	
			$deletesql = "DELETE FROM vtiger_cvstdfilter WHERE cvid = ?";
			$deleteresult = $adb->pquery($deletesql, array($cvid));
	
			$deletesql = "DELETE FROM vtiger_cvadvfilter WHERE cvid = ?";
			$deleteresult = $adb->pquery($deletesql, array($cvid));
			$log->info("CustomView :: Save :: vtiger_cvcolumnlist,cvstdfilter,cvadvfilter deleted successfully before update".$genCVid);
	
			$genCVid = $cvid;
			if($updatecvresult) {
				if(isset($columnslist)) {
					for($i=0;$i<count($columnslist);$i++) {
						$columnsql = "INSERT INTO vtiger_cvcolumnlist (cvid, columnindex, columnname) VALUES (?,?,?)";
						$columnparams = array($genCVid, $i, $columnslist[$i]);
						$columnresult = $adb->pquery($columnsql, $columnparams);
					}
					$log->info("CustomView :: Save :: vtiger_cvcolumnlist update successfully".$genCVid);
					if($std_filter_list["columnname"] !="") {
						$stdfiltersql = "INSERT INTO vtiger_cvstdfilter (cvid,columnname,stdfilter,startdate,enddate) VALUES (?,?,?,?,?)";
						$stdfilterparams = array($genCVid, $std_filter_list["columnname"], $std_filter_list["stdfilter"], $adb->formatDate($std_filter_list["startdate"], true), $adb->formatDate($std_filter_list["enddate"], true));
						$stdfilterresult = $adb->pquery($stdfiltersql, $stdfilterparams);
						$log->info("CustomView :: Save :: vtiger_cvstdfilter update successfully".$genCVid);
					}
					for($i=0;$i<count($adv_filter_col);$i++) {
						$col = explode(":",$adv_filter_col[$i]);
						$temp_val = explode(",",$adv_filter_value[$i]);
						if($col[4] == 'D' || ($col[4] == 'T' && $col[1] != 'time_start' && $col[1] != 'time_end') || $col[4] == 'DT') {
							$val = Array();
							for($x=0;$x<count($temp_val);$x++) {
								//if date and time given then we have to convert the date and leave the time as it is, if date only given then temp_time value will be empty
								list($temp_date,$temp_time) = explode(" ",$temp_val[$x]);
								$temp_date = getDBInsertDateValue(trim($temp_date));
								if(trim($temp_time) != '')
									$temp_date .= ' '.$temp_time;
								$val[$x] = $temp_date;
							}
							$adv_filter_value[$i] = implode(", ",$val);	
						}
						$advfiltersql = "INSERT INTO vtiger_cvadvfilter (cvid,columnindex,columnname,comparator,value) VALUES (?,?,?,?,?)";
						$advfilterparams = array($genCVid, $i, $adv_filter_col[$i], $adv_filter_option[$i], $adv_filter_value[$i]);
						$advfilterresult = $adb->pquery($advfiltersql, $advfilterparams);
					}
					$log->info("CustomView :: Save :: vtiger_cvadvfilter update successfully".$cvid);
				}
			}
		}
	}
}

header("Location: index.php?action=$return_action&parenttab=$parenttab&module=$cvmodule&viewname=$cvid");
?>