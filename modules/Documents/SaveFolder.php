<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('modules/Documents/Documents.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

global $adb;



	$local_log =& LoggerManager::getLogger('index');
	$folderid = $_REQUEST['record'];
	$foldername = utf8RawUrlDecode($_REQUEST["foldername"]);
	$folderdesc = utf8RawUrlDecode($_REQUEST["folderdesc"]);

	if(isset($_REQUEST['savemode']) && $_REQUEST['savemode'] == 'Save')
	{
		if($folderid == "")
		{
			$params=array();
			$sqlfid="select max(folderid) from vtiger_attachmentsfolder";
			$fid=$adb->query_result($adb->pquery($sqlfid,$params),0,'max(folderid)')+1;
			$params=array();
			$sqlseq="select max(sequence) from vtiger_attachmentsfolder";
			$sequence=$adb->query_result($adb->pquery($sqlseq,$params),0,'max(sequence)')+1;
			$params=array();
			$dbQuery="select * from vtiger_attachmentsfolder";
			$result1=$adb->pquery($dbQuery,array());
			$flag=0;
			for($i=0;$i<$adb->num_rows($result1);$i++)
			{
				$dbfldrname=$adb->query_result($result1,$i,'foldername');
				if($dbfldrname == $foldername)
					$flag = 1;
			}
			if($flag == 0)
			{
				$sql="insert into vtiger_attachmentsfolder (folderid,foldername,description,createdby,sequence)values ($fid,'".$foldername."','".$folderdesc."',".$current_user->id.",$sequence)";
				$result=$adb->pquery($sql,$params);
				if(!$result)
				{
					echo "Failure";
				}
				else
					header("Location: index.php?action=DocumentsAjax&file=ListView&mode=ajax&module=Documents");
			}
			elseif($flag == 1)
				echo "DUPLICATE_FOLDERNAME";
		}
		elseif($folderid != "")
		{			
			$dbQuery="select * from vtiger_attachmentsfolder";
			$result1=$adb->pquery($dbQuery,array());
			$flag=0;
			for($i=0;$i<$adb->num_rows($result1);$i++)
			{
				$dbfldrname=$adb->query_result($result1,$i,'foldername');
				if($dbfldrname == $foldername)
					$flag = 1;
			}			
			if($flag == 0)
			{
				$sql="update vtiger_attachmentsfolder set foldername= ? where folderid= ? ";
				$result=$adb->pquery($sql,array($foldername,$folderid));
				if(!$result)
				{
					echo "Failure";
				}
				else
					echo 'Success';
			}
			elseif($flag == 1)
				echo "DUPLICATE_FOLDERNAME";
		}
	}

?>
