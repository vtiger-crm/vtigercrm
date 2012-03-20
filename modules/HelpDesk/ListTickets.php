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

/**	Function to get the list of tickets for the currently loggedin user
**/
 
function getMyTickets($maxval,$calCnt)
{
	global $log;
	$log->debug("Entering getMyTickets() method ...");
	global $current_user;
	global $theme;
	global $current_language;
	global $adb;
	$current_module_strings = return_module_language($current_language, 'HelpDesk');
	$theme_path="themes/".$theme."/";
	$image_path="themes/images/";

	$search_query  = "SELECT vtiger_troubletickets.*, vtiger_crmentity.*
		FROM vtiger_troubletickets 
		INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_troubletickets.ticketid 
		INNER JOIN vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
		where vtiger_crmentity.smownerid = ? and vtiger_crmentity.deleted = 0 and ".
		"vtiger_troubletickets.ticketid > 0 and vtiger_troubletickets.status <> 'Closed' ".
		"AND vtiger_crmentity.setype='HelpDesk' ORDER BY createdtime DESC";

	$search_query .= " LIMIT 0," . $adb->sql_escape_string($maxval);
	
	
	if($calCnt == 'calculateCnt') {
		$list_result_rows = $adb->pquery(mkCountQuery($search_query), array($current_user->id));
		return $adb->query_result($list_result_rows, 0, 'count');
	}
	
	$tktresult = $adb->pquery($search_query, array($current_user->id));
	if($adb->num_rows($tktresult))
	{
		$title=array();
		$title[]='myTickets.gif';
		$title[]=$current_module_strings['LBL_MY_TICKETS'];
		$title[]='home_mytkt';

		$header=array();
		$header[]=$current_module_strings['LBL_SUBJECT'];
		$header[]=$current_module_strings['Related To'];

		$noofrows = $adb->num_rows($tktresult);
		for ($i=0; $i<$adb->num_rows($tktresult); $i++)
		{
			$value=array();
			$ticketid = $adb->query_result($tktresult,$i,"ticketid");
			$viewstatus = $adb->query_result($tktresult,$i,"viewstatus");
			if($viewstatus == 'Unread')
				$value[]= '<a style="color:red;" href="index.php?action=DetailView&module=HelpDesk&record='.substr($adb->query_result($tktresult,$i,"ticketid"),0,20).'">'.$adb->query_result($tktresult,$i,"title").'</a>';
			elseif($viewstatus == 'Marked')
				$value[]= '<a style="color:yellow;" href="index.php?action=DetailView&module=HelpDesk&record='.substr($adb->query_result($tktresult,$i,"ticketid"),0,20).'">'.$adb->query_result($tktresult,$i,"title").'</a>';
			else
				$value[]= '<a href="index.php?action=DetailView&module=HelpDesk&record='.substr($adb->query_result($tktresult,$i,"ticketid"),0,20).'">'.substr($adb->query_result($tktresult,$i,"title"),0,20).'</a>';

			$parent_id = $adb->query_result($tktresult,$i,"parent_id");
			$parent_name = '';
			if($parent_id != '' && $parent_id != NULL)
			{
				$parent_name = getParentLink($parent_id);
			}

			$value[]=$parent_name;
			$entries[$ticketid]=$value;
		}
		
		$search_qry = "&query=true&Fields0=ticketstatus&Condition0=n&Srch_value0=closed&Fields1=assigned_user_id&Condition1=e&Srch_value1=".$current_user->column_fields['user_name']."&searchtype=advance&search_cnt=2&matchtype=all";
		
		$values=Array('ModuleName'=>'HelpDesk','Title'=>$title,'Header'=>$header,'Entries'=>$entries,'search_qry'=>$search_qry);
		if ( ($display_empty_home_blocks && $noofrows == 0 ) || ($noofrows>0) )	
		{
			$log->debug("Exiting getMyTickets method ...");
			return $values;
		}
	}
	$log->debug("Exiting getMyTickets method ...");
}

/**	Function to get the parent (Account or Contact) link
  *	@param int $parent_id -- parent id of the ticket (accountid or contactid)
  *	return string $parent_name -- return the parent name as a link
**/
function getParentLink($parent_id)
{
	global $log;
	$log->debug("Entering getParentLink(".$parent_id.") method ...");
	global $adb;
	
	// Static caching
	static $__cache_listtickets_parentlink = Array();
	if(isset($__cache_listtickets_parentlink[$parent_id])) {
		return $__cache_listtickets_parentlink[$parent_id];
	}

	$sql = "select setype from vtiger_crmentity where crmid=?";
	$parent_module = $adb->query_result($adb->pquery($sql, array($parent_id)),0,'setype');

	if($parent_module == 'Contacts')
	{
		$sql = "select firstname,lastname from vtiger_contactdetails where contactid=?";
		$res = $adb->pquery($sql, array($parent_id));
		$parentname = $adb->query_result($res,0,'firstname');
		$parentname .= ' '.$adb->query_result($res,0,'lastname');
	        $parent_name = '<a href="index.php?action=DetailView&module='.$parent_module.'&record='.$parent_id.'">'.$parentname.'</a>';
	}
	if($parent_module == 'Accounts')
	{
		$sql = "select accountname from vtiger_account where accountid=?";
		$parentname = $adb->query_result($adb->pquery($sql, array($parent_id)),0,'accountname');
	        $parent_name = '<a href="index.php?action=DetailView&module='.$parent_module.'&record='.$parent_id.'">'.$parentname.'</a>';
	}

	// Add to cache
	$__cache_listtickets_parentlink[$parent_id] = $parent_name;
	
	$log->debug("Exiting getParentLink method ...");
	return $parent_name;
}
?>
