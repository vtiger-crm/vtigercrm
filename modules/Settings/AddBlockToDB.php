<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

global $current_language,$log;
require_once('include/database/PearDatabase.php');
checkFileAccess('modules/'.$_REQUEST['fld_module'].'/language/'.$current_language.'.lang.php');
require_once('modules/'.$_REQUEST['fld_module'].'/language/'.$current_language.'.lang.php');
global $mod_strings;

$fldmodule=vtlib_purify($_REQUEST['fld_module']);
$mode=vtlib_purify($_REQUEST['mode']);
$parenttab = getParentTab();

$newblocklabel = vtlib_purify(trim($_REQUEST['blocklabel']));
$after_block = vtlib_purify($_REQUEST['after_blockid']);
	
$tabid = getTabid($fldmodule);
$flag = 0;
$dup_check_query = $adb->pquery("SELECT blocklabel from vtiger_blocks WHERE tabid = ?",array($tabid));	
for($i=0;$i<$adb->num_rows($dup_check_query);$i++){
	$blklbl = $adb->query_result($dup_check_query,$i,'blocklabel'); 
	$blklbl = getTranslatedString($blklbl);
	if($blklbl == $newblocklabel){
		$flag = 1;
		break;
	}
}
if($flag!=1) {
    $sql_seq="select sequence from vtiger_blocks where blockid=?";
	$res_seq= $adb->pquery($sql_seq, array($after_block));
    $row_seq=$adb->fetch_array($res_seq);
	$block_sequence=$row_seq['sequence'];
	$newblock_sequence=$block_sequence+1;
	
	$sql_up="update vtiger_blocks set sequence=sequence+1 where tabid=? and sequence > ?";
	$adb->pquery($sql_up, array($tabid,$block_sequence));
	
	$sql='select max(blockid) as max_id from vtiger_blocks';
	$res=$adb->query($sql);
	$row=$adb->fetch_array($res);
	$max_blockid=$row['max_id']+1;

	$sql="INSERT INTO vtiger_blocks (tabid, blockid, sequence, blocklabel) values (?,?,?,?)";	
	$params = array($tabid,$max_blockid,$newblock_sequence,$newblocklabel);
		$adb->pquery($sql,$params);
} else
	$duplicate='yes';
  
header("Location:index.php?module=Settings&action=LayoutBlockList&fld_module=".$fldmodule."&duplicate=".$duplicate."&parenttab=".$parenttab."&mode".$mode);
?>