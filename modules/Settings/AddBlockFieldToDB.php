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
require_once('Smarty_setup.php');
global $mod_strings;
$fldmodule=vtlib_purify($_REQUEST['fld_module']);
$fldType=vtlib_purify($_REQUEST['fieldType']);
$parenttab=getParentTab();
$mode=vtlib_purify($_REQUEST['mode']);
$fldlabel = trim($_REQUEST['fldLabel']);
$tabid = getTabid($fldmodule);

if(isset($_REQUEST['field_assignid'])) {
	$blockid = $_REQUEST['blockid'];
	$max_fieldsequence = "select max(sequence) as maxsequence from vtiger_field where block = ? ";
	$res = $adb->pquery($max_fieldsequence,array($blockid));
	$max_seq = $adb->query_result($res,0,'maxsequence');
	$max_seq = $max_seq+1;	
	foreach($_REQUEST['field_assignid'] as $field_id) {
		if($field_id!='') {
			$adb->pquery("update vtiger_field set block=?,sequence = ? WHERE fieldid= ?",array($blockid,$max_seq,$field_id));
		    $max_seq++;
		}
	}
}

header("Location:index.php?module=Settings&action=LayoutBlockList&fld_module=".$fldmodule."&parenttab=".$parenttab."&duplicate=".$dup_error);
?>