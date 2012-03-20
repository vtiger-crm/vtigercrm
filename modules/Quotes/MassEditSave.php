<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
global $currentModule;

$focus = CRMEntity::getInstance($currentModule);

$idlist= vtlib_purify($_REQUEST['massedit_recordids']);
$viewid = vtlib_purify($_REQUEST['viewname']);
$return_module = vtlib_purify($_REQUEST['massedit_module']);
$return_action = 'index';

global $rstart;
//Added to fix 4600
$url = getBasic_Advance_SearchURL();

if(isset($_REQUEST['start']) && $_REQUEST['start']!=''){
	$rstart = "&start=".vtlib_purify($_REQUEST['start']);
}

if(isset($idlist)) {
	$recordids = explode(';', $idlist);
	for($index = 0; $index < count($recordids); ++$index) {
		$recordid = $recordids[$index];
		if($recordid == '') continue;
		if(isPermitted($currentModule,'EditView',$recordid) == 'yes') {

			// Save each module record with update value.
			$focus->retrieve_entity_info($recordid, $currentModule);
			$focus->mode = 'edit';		
			$focus->id = $recordid;		
			foreach($focus->column_fields as $fieldname => $val)
			{    	
				if(isset($_REQUEST[$fieldname."_mass_edit_check"]))
				{
					if($fieldname == 'assigned_user_id'){
						if($_REQUEST['assigntype'] == 'U')  {
							$value = $_REQUEST['assigned_user_id'];
						} elseif($_REQUEST['assigntype'] == 'T') {
							$value = $_REQUEST['assigned_group_id'];
						}
					} else {
						if(is_array($_REQUEST[$fieldname]))
							$value = $_REQUEST[$fieldname];
						else
							$value = trim($_REQUEST[$fieldname]);
					}
					$focus->column_fields[$fieldname] = $value;
				}
				else{
					$focus->column_fields[$fieldname] = decode_html($focus->column_fields[$fieldname]);
				}
			}
			$focus->save($currentModule);
		}
	}
}

$parenttab = getParentTab();
header("Location: index.php?module=$return_module&action=$return_action&parenttab=$parenttab$rstart");
?>