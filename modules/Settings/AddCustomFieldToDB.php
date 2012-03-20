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
global $current_user;
 
$fldmodule=vtlib_purify($_REQUEST['fld_module']);
$blockid = vtlib_purify($_REQUEST['blockid']);
$fldlabel=vtlib_purify(trim($_REQUEST['fldLabel_'.$blockid]));
$fldType= vtlib_purify($_REQUEST['fieldType_'.$blockid]);

$parenttab=getParentTab();
$mode=vtlib_purify($_REQUEST['mode']);

$tabid = getTabid($fldmodule);
if ($fldmodule == 'Calendar' && isset($_REQUEST['activity_type'])) {
	$activitytype = vtlib_purify($_REQUEST['activity_type']);
	if ($activitytype == 'E') $tabid = '16';
	if ($activitytype == 'T') $tabid = '9';
}

if(get_magic_quotes_gpc() == 1) {
	$fldlabel = stripslashes($fldlabel);
}

//checking if the user is trying to create a custom vtiger_field which already exists  
$dup_check_tab_id = $tabid;
if ($fldmodule == 'Calendar')
	$dup_check_tab_id = array('9', '16');
$checkquery="select * from vtiger_field where tabid in (". generateQuestionMarks($dup_check_tab_id) .") and fieldlabel=?";
$params =  array($dup_check_tab_id, $fldlabel);
if($mode == 'edit' && isset($_REQUEST['fieldid']) && $_REQUEST['fieldid'] != '') {
	$checkquery .= " and fieldid !=?";
	array_push($params, $_REQUEST['fieldid']);
}
$checkresult=$adb->pquery($checkquery,$params);

if($adb->num_rows($checkresult) > 0) {
	
	if(isset($_REQUEST['fldLength_'.$blockid])) 	{	
		$fldlength=$_REQUEST['fldLength_'.$blockid];
	} else {
		 $fldlength='';
	}
	if(isset($_REQUEST['fldDecimal_'.$blockid])) {
		$flddecimal=$_REQUEST['fldDecimal_'.$blockid];
	} else {
		$flddecimal='';
	}
	if(isset($_REQUEST['fldPickList_'.$blockid])) {
		$fldPickList=$_REQUEST['fldPickList_'.$blockid];
	} else {
		$fldPickList='';
	}
	
	header("Location:index.php?module=Settings&action=CustomFieldList&fld_module=".$fldmodule."&fldType=".$fldType."&fldlabel=".$fldlabel."&parenttab=".$parenttab."&duplicate=yes");

} else {
	if($_REQUEST['fieldid'] == '') {
		$max_fieldid = $adb->getUniqueID("vtiger_field");
		$columnName = 'cf_'.$max_fieldid;
		$custfld_fieldid=$max_fieldid;
	} else {
		$max_fieldid = $_REQUEST['column'];
		$columnName = $max_fieldid;
		$custfld_fieldid= $_REQUEST['fieldid'];
	}

	//Assigning the vtiger_table Name
	$tableName ='';
	if($fldmodule == 'HelpDesk') {
		$tableName='vtiger_ticketcf';
	} elseif($fldmodule == 'Products') {
		$tableName='vtiger_productcf';
	} elseif($fldmodule == 'Vendors') {
		$tableName='vtiger_vendorcf';
	} elseif($fldmodule == 'PriceBooks') {
		$tableName='vtiger_pricebookcf';
	} elseif($fldmodule == 'Calendar') {
		$tableName='vtiger_activitycf';
	} elseif($fldmodule != '') {
		include_once('data/CRMEntity.php');
		$focus = CRMEntity::getInstance($fldmodule);
		if (isset($focus->customFieldTable)) {
			$tableName=$focus->customFieldTable[0];
		} else {
			$tableName= 'vtiger_'.strtolower($fldmodule).'cf';
		}
	}
	//Assigning the uitype
	$fldlength=$_REQUEST['fldLength_'.$blockid];
	$uitype='';
	$fldPickList='';
	if(isset($_REQUEST['fldDecimal_'.$blockid]) && $_REQUEST['fldDecimal_'.$blockid] != '')
	{
		$decimal=$_REQUEST['fldDecimal_'.$blockid];
	}
	else
	{
		$decimal=0;
	}
	$type='';
	$uichekdata='';
	if($fldType == 'Text')
	{
	$uichekdata='V~O~LE~'.$fldlength;
		$uitype = 1;
		$type = "C(".$fldlength.") default ()"; // adodb type
	}
	elseif($fldType == 'Number')
	{
		$uitype = 7;

		//this may sound ridiculous passing decimal but that is the way adodb wants
		$dbfldlength = $fldlength + $decimal + 1;
 
		$type="N(".$dbfldlength.".".$decimal.")";	// adodb type
	$uichekdata='N~O~'.$fldlength .','.$decimal;
	}
	elseif($fldType == 'Percent')
	{
		$uitype = 9;
		$type="N(5.2)"; //adodb type
		$uichekdata='N~O~2~2';
	}
	elseif($fldType == 'Currency')
	{
		$uitype = 71;
		$dbfldlength = $fldlength + $decimal + 1;
		$type="N(".$dbfldlength.".".$decimal.")"; //adodb type
	$uichekdata='N~O~'.$fldlength .','.$decimal;
	}
	elseif($fldType == 'Date')
	{
	$uichekdata='D~O';
		$uitype = 5;
		$type = "D"; // adodb type
		
	}
	elseif($fldType == 'Email')
	{
		$uitype = 13;
		$type = "C(50) default () "; //adodb type
		$uichekdata='E~O';
	}
	elseif($fldType == 'Phone')
	{
		$uitype = 11;
		$type = "C(30) default () "; //adodb type
		
		$uichekdata='V~O';
	}
	elseif($fldType == 'Picklist')
	{
		$uitype = 15;
		$type = "C(255) default () "; //adodb type
		$uichekdata='V~O';
	}
	elseif($fldType == 'URL')
	{
		$uitype = 17;
		$type = "C(255) default () "; //adodb type
		$uichekdata='V~O';
	}
	elseif($fldType == 'Checkbox')	 
        {	 
                 $uitype = 56;	 
                 $type = "C(3) default 0"; //adodb type	 
                 $uichekdata='C~O';	 
        }
	elseif($fldType == 'TextArea')	 
        {	 
                 $uitype = 21;	 
                 $type = "X"; //adodb type	 
                 $uichekdata='V~O';	 
        }
	elseif($fldType == 'MultiSelectCombo')
	{
		 $uitype = 33;
		 $type = "X"; //adodb type
		 $uichekdata='V~O';
	}
	elseif($fldType == 'Skype')
	{
		$uitype = 85;
		$type = "C(255) default () "; //adodb type
		$uichekdata='V~O';
	}
	// No Decimal Pleaces Handling

   	//1. add the customfield vtiger_table to the vtiger_field vtiger_table as Block4
	//2. fetch the contents of the custom vtiger_field and show in the UI
  
	$custfld_sequece=$adb->getUniqueID("vtiger_customfield_sequence");
    	
	$blockid ='';
        //get the blockid for this custom block
        $blockid = getBlockId($tabid,'LBL_CUSTOM_INFORMATION');

        if(is_numeric($blockid))
        {
		if($mode == "edit" && $_REQUEST['fieldid'] != '')
		{
			$query = "update vtiger_field set fieldlabel=?, typeofdata=? where fieldid=?";
                        $adb->pquery($query, array($fldlabel, $uichekdata, $_REQUEST['fieldid']));
		}
		else if($_REQUEST['fieldid'] == '')
		{
			$query = "insert into vtiger_field (tabid,fieldid,columnname,tablename,generatedtype,uitype,fieldname,fieldlabel,
				readonly,presence,selected,maximumlength,sequence,block,displaytype,typeofdata,quickcreate,quickcreatesequence,info_type) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$qparams = array($tabid,$custfld_fieldid,$columnName,$tableName,2,$uitype,$columnName,$fldlabel,0,0,0,100,$custfld_sequece,$blockid,1,$uichekdata,1,0,'BAS');
			$adb->pquery($query, $qparams);
			$adb->alterTable($tableName, $columnName." ".$type, "Add_Column");
			
			//Inserting values into vtiger_profile2field vtiger_tables
			$sql1 = "select * from vtiger_profile";
			$sql1_result = $adb->pquery($sql1, array());
			$sql1_num = $adb->num_rows($sql1_result);
			for($i=0; $i<$sql1_num; $i++)
			{
				$profileid = $adb->query_result($sql1_result,$i,"profileid");
				$sql2 = "insert into vtiger_profile2field values(?,?,?,?,?)";
				$adb->pquery($sql2, array($profileid, $tabid, $custfld_fieldid, 0, 1));	 	
			}

			//Inserting values into def_org vtiger_tables
			$sql_def = "insert into vtiger_def_org_field values(?,?,?,?)";
			$adb->pquery($sql_def, array($tabid, $custfld_fieldid, 0, 1));

			if($fldType == 'Picklist' || $fldType == 'MultiSelectCombo')
			{
				$columnName = $adb->sql_escape_string($columnName);
				// Creating the PickList Table and Populating Values
				if($_REQUEST['fieldid'] == '')
				{
					$qur = "CREATE TABLE vtiger_".$columnName." (
						".$columnName."id int(19) NOT NULL auto_increment,
						".$columnName." varchar(200) NOT NULL,
						presence int(1) NOT NULL default '1',
						picklist_valueid int(19) NOT NULL default '0',
						PRIMARY KEY  (".$columnName."id)
					)";
					$adb->pquery($qur, array());
				}

				//Adding a  new picklist value in the picklist table
				if($mode != 'edit')
				{
					$picklistid = $adb->getUniqueID("vtiger_picklist");
					$sql="insert into vtiger_picklist values(?,?)";
					$adb->pquery($sql, array($picklistid,$columnName));
				}
				$roleid=$current_user->roleid;
				$qry="select picklistid from vtiger_picklist where  name=?";
				$picklistid = $adb->query_result($adb->pquery($qry, array($columnName)), 0,'picklistid');
				$pickArray = Array();
				$fldPickList =  $_REQUEST['fldPickList_'.$blockid];
				$pickArray = explode("\n",$fldPickList);
				$count = count($pickArray);
				for($i = 0; $i < $count; $i++)
				{
					$pickArray[$i] = trim(from_html($pickArray[$i]));
					if($pickArray[$i] != '')
					{
						$picklistcount=0;
						$sql ="select $columnName from vtiger_$columnName";
						$numrow = $adb->num_rows($adb->pquery($sql, array()));
						for($x=0;$x < $numrow ; $x++)
						{
							$picklistvalues = $adb->query_result($adb->pquery($sql, array()),$x,$columnName);
							if($pickArray[$i] == $picklistvalues)
							{
								$picklistcount++;
							}
						}
						if($picklistcount == 0)
						{
							$picklist_valueid = getUniquePicklistID();
							$query = "insert into vtiger_".$columnName." values(?,?,?,?)";				
							$adb->pquery($query, array($adb->getUniqueID("vtiger_".$columnName),$pickArray[$i],1,$picklist_valueid));
							/*$sql="update vtiger_picklistvalues_seq set id = ?";
							$adb->pquery($sql, array(++$picklist_valueid));*/
						}
						$sql = "select picklist_valueid from vtiger_$columnName where $columnName=?";
						$pick_valueid = $adb->query_result($adb->pquery($sql, array($pickArray[$i])),0,'picklist_valueid');
						$sql = "insert into vtiger_role2picklist select roleid,$pick_valueid,$picklistid,$i from vtiger_role";
						$adb->pquery($sql, array());
					}
				}
			}
			//Inserting into LeadMapping table - Jaguar
			if($fldmodule == 'Leads' && $_REQUEST['fieldid'] == '')
			{
				$sql_def = "insert into vtiger_convertleadmapping (leadfid) values(?)";
				$adb->pquery($sql_def, array($custfld_fieldid));
			}
		}	
	}
	header("Location:index.php?module=Settings&action=CustomFieldList&fld_module=".$fldmodule."&parenttab=".$parenttab);
}
?>