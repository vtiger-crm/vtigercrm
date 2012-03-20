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
//Code Added by Minnie -Starts
/**
 * To get the lists of sharedids 
 * @param $id -- The user id :: Type integer
 * @returns $sharedids -- The shared vtiger_users id :: Type Array
 */
if(isset($_REQUEST['fieldval']))
{
        $currtime = date("Y:m:d:H:i:s");
        list($y,$m,$d,$h,$min,$sec) = split(':',$currtime);
        echo "[{YEAR:'".$y."',MONTH:'".$m."',DAY:'".$d."',HOUR:'".$h."',MINUTE:'".$min."'}]";
        die;
}
function getSharedUserId($id)
{
	global $adb;
        $sharedid = Array();
        $query = "SELECT vtiger_users.user_name,vtiger_sharedcalendar.* from vtiger_sharedcalendar left join vtiger_users on vtiger_sharedcalendar.sharedid=vtiger_users.id where userid=?";
        $result = $adb->pquery($query, array($id));
        $rows = $adb->num_rows($result);
        for($j=0;$j<$rows;$j++)
        {

                $id = $adb->query_result($result,$j,'sharedid');
                $sharedname = $adb->query_result($result,$j,'user_name');
                $sharedid[$id]=$sharedname;

        }
	return $sharedid;
}

/**
 * To get the lists of vtiger_users id who shared their calendar with specified user
 * @param $sharedid -- The shared user id :: Type integer
 * @returns $shared_ids -- a comma seperated vtiger_users id  :: Type string
 */
function getSharedCalendarId($sharedid)
{
	global $adb;
	$query = "SELECT * from vtiger_sharedcalendar where sharedid=?";
	$result = $adb->pquery($query, array($sharedid));
	if($adb->num_rows($result)!=0)
	{
		for($j=0;$j<$adb->num_rows($result);$j++)
			$userid[] = $adb->query_result($result,$j,'userid');
		$shared_ids = implode (",",$userid);
	}
	return $shared_ids;
}

/**
 * To get userid and username of all vtiger_users except the current user
 * @param $id -- The user id :: Type integer
 * @returns $user_details -- Array in the following format:
 * $user_details=Array($userid1=>$username, $userid2=>$username,............,$useridn=>$username);
 */
function getOtherUserName($id)
{
	global $adb;
	$user_details=Array();
		$query="select * from vtiger_users where deleted=0 and status='Active' and id!=?";
		$result = $adb->pquery($query, array($id));
		$num_rows=$adb->num_rows($result);
		for($i=0;$i<$num_rows;$i++)
		{
			$userid=$adb->query_result($result,$i,'id');
			$username=$adb->query_result($result,$i,'user_name');
			$user_details[$userid]=$username;
		}
		return $user_details;
}

/**
 * To get userid and username of vtiger_users in hierarchy level
 * @param $id -- The user id :: Type integer
 * @returns $user_details -- Array in the following format:
 * $user_details=Array($userid1=>$username, $userid2=>$username,............,$useridn=>$username);
 */

function getSharingUserName($id)
{
	global $adb,$current_user;
        $user_details=Array();
	$assigned_user_id = $current_user->id;
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	if($is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid('Calendar')] == 3 or $defaultOrgSharingPermission[getTabid('Calendar')] == 0))
	{
		$role_seq = implode($parent_roles, "::");
		$query = "select id as id,user_name as user_name from vtiger_users where id=? and status='Active' union select vtiger_user2role.userid as id,vtiger_users.user_name as user_name from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like ? and status='Active' union select shareduserid as id,vtiger_users.user_name as user_name from vtiger_tmp_write_user_sharing_per inner join vtiger_users on vtiger_users.id=vtiger_tmp_write_user_sharing_per.shareduserid where status='Active' and vtiger_tmp_write_user_sharing_per.userid=? and vtiger_tmp_write_user_sharing_per.tabid=9";
		$params = array($current_user->id, $role_seq."::%", $current_user->id);
		if (!empty($assigned_user_id)) {
			$query .= " OR id=?";
			array_push($params, $assigned_user_id);
		}
		$query .= " order by user_name ASC";
		$result = $adb->pquery($query, $params, true, "Error filling in user array: ");
		while($row = $adb->fetchByAssoc($result))
		{
			$temp_result[$row['id']] = $row['user_name'];
		}
		$user_details = &$temp_result;
		unset($user_details[$id]);
	}
	else
	{
		$user_details = get_user_array(FALSE, "Active", $id);
		unset($user_details[$id]);
	}
	return $user_details;
}

/**
 * To get hour,minute and format
 * @param $starttime -- The date&time :: Type string
 * @param $endtime -- The date&time :: Type string
 * @param $format -- The format :: Type string
 * @returns $timearr :: Type Array
*/
function getaddEventPopupTime($starttime,$endtime,$format)
{
	$timearr = Array();
	list($sthr,$stmin) = explode(":",$starttime);
	list($edhr,$edmin)  = explode(":",$endtime);
	if($format == 'am/pm')
	{
		$hr = $sthr+0;
		$timearr['startfmt'] = ($hr >= 12) ? "pm" : "am";
		if($hr == 0) $hr = 12;
		$timearr['starthour'] = twoDigit(($hr>12)?($hr-12):$hr);
		$timearr['startmin']  = $stmin;

		$edhr = $edhr+0;
		$timearr['endfmt'] = ($edhr >= 12) ? "pm" : "am";
		if($edhr == 0) $edhr = 12;
		$timearr['endhour'] = twoDigit(($edhr>12)?($edhr-12):$edhr);
		$timearr['endmin']    = $edmin;
		return $timearr;
	}
	if($format == '24')
	{
		$timearr['starthour'] = twoDigit($sthr);
		$timearr['startmin']  = $stmin;
		$timearr['startfmt']  = '';
		$timearr['endhour']   = twoDigit($edhr);
		$timearr['endmin']    = $edmin;
		$timearr['endfmt']    = '';
		return $timearr;
	}
}

/**
 *To construct time select combo box
 *@param $format -- the format :: Type string
 *@param $bimode -- The mode :: Type string
 *constructs html select combo box for time selection
 *and returns it in string format.
 */
function getTimeCombo($format,$bimode,$hour='',$min='',$fmt='',$todocheck=false)
{
	global $mod_strings;
	$combo = '';
	$min = $min - ($min%5);
	if($bimode == 'start' && !$todocheck)
		$jsfn = 'onChange="changeEndtime_StartTime(document.EditView.activitytype.value);"';
	else
		$jsfn = null;
	if($format == 'am/pm')
	{
		$combo .= '<select class=small name="'.$bimode.'hr" id="'.$bimode.'hr" '.$jsfn.'>';
		for($i=0;$i<12;$i++)
		{
			if($i == 0)
			{
				$hrtext= 12;
				$hrvalue = 12;
			}
			else
				$hrvalue = $hrtext = twoDigit($i);
			$hrsel = ($hour == $hrvalue)?'selected':'';	
			$combo .= '<option value="'.$hrvalue.'" '.$hrsel.'>'.$hrtext.'</option>';
		}
		$combo .= '</select>&nbsp;';
		$combo .= '<select name="'.$bimode.'min" id="'.$bimode.'min" class=small '.$jsfn.'>';
		for($i=0;$i<12;$i++)
		{
			$value = $i*5;
			$value = twoDigit($value);
			$minsel = ($min == $value)?'selected':'';
			$combo .= '<option value="'.$value.'" '.$minsel.'>'.$value.'</option>';
		}
		$combo .= '</select>&nbsp;';
		$combo .= '<select name="'.$bimode.'fmt" id="'.$bimode.'fmt" class=small '.$jsfn.'>';
		$amselected = ($fmt == 'am')?'selected':'';
		$pmselected = ($fmt == 'pm')?'selected':'';
		$combo .= '<option value="am" '.$amselected.'>AM</option>';
		$combo .= '<option value="pm" '.$pmselected.'>PM</option>';
		$combo .= '</select>';
		}
		else
		{
			$combo .= '<select name="'.$bimode.'hr" id="'.$bimode.'hr" class=small '.$jsfn.'>';
			for($i=0;$i<=23;$i++)
			{
				$hrvalue = twoDigit($i);
				$hrsel = ($hour == $hrvalue)?'selected':'';
				$combo .= '<option value="'.$hrvalue.'" '.$hrsel.'>'.$hrvalue.'</option>';
			}
			$combo .= '</select>'.$mod_strings[LBL_HR].'&nbsp;';
			$combo .= '<select name="'.$bimode.'min" id="'.$bimode.'min" class=small '.$jsfn.'>';
			for($i=0;$i<12;$i++)
			{
				$value = $i*5;
				$value= twoDigit($value);
				$minsel = ($min == $value)?'selected':'';
				$combo .= '<option value="'.$value.'" '.$minsel.'>'.$value.'</option>';
			}
			$combo .= '</select>&nbsp;'.$mod_strings[LBL_MIN].'<input type="hidden" name="'.$bimode.'fmt" id="'.$bimode.'fmt">';
		}
		return $combo;
}

/**
 *Function to construct HTML select combo box
 *@param $fieldname -- the field name :: Type string
 *@param $tablename -- The table name :: Type string
 *constructs html select combo box for combo field
 *and returns it in string format.
 */

function getActFieldCombo($fieldname,$tablename)
{
	global $adb, $mod_strings,$current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	$combo = '';
	$js_fn = '';
	if($fieldname == 'eventstatus')
		$js_fn = 'onChange = "getSelectedStatus();"';
	$combo .= '<select name="'.$fieldname.'" id="'.$fieldname.'" class=small '.$js_fn.'>';
	if($is_admin)
		$q = "select * from ".$tablename;
	else
	{
		$roleid=$current_user->roleid;
		$subrole = getRoleSubordinates($roleid);
		if(count($subrole)> 0)
		{
			$roleids = $subrole;
			array_push($roleids, $roleid);
		}
		else
		{	
			$roleids = $roleid;
		}

		if (count($roleids) > 1) {
			$q="select distinct $fieldname from  $tablename inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = $tablename.picklist_valueid where roleid in (\"". implode($roleids,"\",\"") ."\") and picklistid in (select picklistid from $tablename) order by sortid asc";
		} else {
			$q="select distinct $fieldname from $tablename inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = $tablename.picklist_valueid where roleid ='".$roleid."' and picklistid in (select picklistid from $tablename) order by sortid asc";
		}
	}
	$Res = $adb->query($q);
	$noofrows = $adb->num_rows($Res);

	for($i = 0; $i < $noofrows; $i++)
	{
		$value = $adb->query_result($Res,$i,$fieldname);
		$combo .= '<option value="'.$value.'">'.getTranslatedString($value).'</option>';
	}

	$combo .= '</select>';
	return $combo;
}

/*Fuction to get value for Assigned To field
 *returns values of Assigned To field in array format
*/
function getAssignedTo($tabid)
{
	global $current_user,$noof_group_rows,$adb;
	$assigned_user_id = $current_user->id;
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	if($is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[$tabid] == 3 or $defaultOrgSharingPermission[$tabid] == 0))
	{
		$result=get_current_user_access_groups('Calendar');
	}
	else
	{
		$result = get_group_options();
	}
	if($result) $nameArray = $adb->fetch_array($result);
	
	if($is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[$tabid] == 3 or $defaultOrgSharingPermission[$tabid] == 0))
	{
		$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
	}
	else
	{
		$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
	}
	if($noof_group_rows!=0)
	{
		do
		{
			$groupname=$nameArray["groupname"];
			$group_option[] = array($groupname=>$selected);

		}while($nameArray = $adb->fetch_array($result));
	}
	$fieldvalue[]=$users_combo;
	$fieldvalue[] = $group_option;
	return $fieldvalue;
}

//Code Added by Minnie -Ends
/**
 * Function to get the vtiger_activity details for mail body
 * @param   string   $description       - activity description
 * @param   string   $from              - to differenciate from notification to invitation.
 * return   string   $list              - HTML in string format
 */
function getActivityDetails($description,$user_id,$from='')
{
	global $log,$current_user,$current_language;
	global $adb;
	require_once 'include/utils/utils.php';
	$mod_strings = return_module_language($current_language, 'Calendar');
	$log->debug("Entering getActivityDetails(".$description.") method ...");
	$updated = $mod_strings['LBL_UPDATED'];
	$created = $mod_strings['LBL_CREATED'];
    $reply = (($description['mode'] == 'edit')?"$updated":"$created");
	if($description['activity_mode'] == "Events")
	{
		$end_date_lable=$mod_strings['End date and time'];
	}
	else
	{
		$end_date_lable=$mod_strings['Due Date'];
	}

	$name = getUserName($user_id);
	
	if($from == "invite")
		$msg = getTranslatedString($mod_strings['LBL_ACTIVITY_INVITATION']);
	else
		$msg = getTranslatedString($mod_strings['LBL_ACTIVITY_NOTIFICATION']);

	$current_username = getUserName($current_user->id);
	$status = getTranslatedString($description['status'],'Calendar');
	$list = $name.',';
	$list .= '<br><br>'.$msg.' '.$reply.'.<br> '.$mod_strings['LBL_DETAILS_STRING'].':<br>';
	$list .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$mod_strings["LBL_SUBJECT"].' : '.$description['subject'];
	$list .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$mod_strings["Start date and time"].' : '.$description['st_date_time'];
	$list .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$end_date_lable.' : '.$description['end_date_time'];
	$list .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$mod_strings["LBL_STATUS"].': '.$status;
	$list .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$mod_strings["Priority"].': '.getTranslatedString($description['taskpriority']);
	$list .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$mod_strings["Related To"].': '.getTranslatedString($description['relatedto']);
	if(!empty($description['contact_name']))
	{
        	$list .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$mod_strings["LBL_CONTACT_LIST"].' '.$description['contact_name'];
	}
	else
		$list .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$mod_strings["Location"].' : '.$description['location'];
			
        $list .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$mod_strings["LBL_APP_DESCRIPTION"].': '.$description['description'];
        $list .= '<br><br>'.$mod_strings["LBL_REGARDS_STRING"].' ,';
        $list .= '<br>'.$current_username.'.';

        $log->debug("Exiting getActivityDetails method ...");
		return $list;
}

function twoDigit( $no ){
	if($no < 10 && strlen(trim($no)) < 2) return "0".$no;
	else return "".$no;
}

function timeString($datetime,$fmt){

	if(is_object($datetime)){
		$hr = $datetime->hour;
		$min = $datetime->minute;
	} else {
		$hr = $datetime['hour'];
		$min = $datetime['minute'];
	}
	$timeStr = "";
	if($fmt != 'am/pm'){
		$timeStr .= twoDigit($hr).":".twoDigit($min);
	}else{
		$am = ($hr >= 12) ? "pm" : "am";
		if($hr == 0) $hr = 12;
		$timeStr .= ($hr>12)?($hr-12):$hr;
		$timeStr .= ":".twoDigit($min);
		$timeStr .= $am;
	}
	return $timeStr;
}
//added to fix Ticket#3068
function getEventNotification($mode,$subject,$desc)
{
	global $current_user,$adb;
	require_once("modules/Emails/mail.php");
	$subject = $mode.' : '.$subject;
	$crmentity = new CRMEntity();
	if(getUserName($desc['user_id']))
	{
		$to_email = getUserEmailId('id',$desc['user_id']);
		$description = getActivityDetails($desc,$desc['user_id']);
		send_mail('Calendar',$to_email,$current_user->user_name,'',$subject,$description);
	}
	else
	{
		$groupid = $desc['user_id'];
		require_once('include/utils/GetGroupUsers.php');
		$getGroupObj=new GetGroupUsers();
		$getGroupObj->getAllUsersInGroup($groupid);
		$userIds=$getGroupObj->group_users;
		if (count($userIds) > 0) {
			$groupqry="select email1,id from vtiger_users where id in(".generateQuestionMarks($userIds).")";
			$groupqry_res=$adb->pquery($groupqry, array($userIds));
			$noOfRows = $adb->num_rows($groupqry_res);
			for($z=0;$z < $noOfRows;$z++)
			{
				$emailadd = $adb->query_result($groupqry_res,$z,'email1');
				$curr_userid = $adb->query_result($groupqry_res,$z,'id');
				$description = getActivityDetails($desc,$curr_userid);
				$mail_status = send_mail('Calendar',$emailadd,getUserName($curr_userid),'',$subject,$description);
	
			}
		}
	}
}

function sendInvitation($inviteesid,$mode,$subject,$desc)
{
	global $current_user,$mod_strings;
	require_once("modules/Emails/mail.php");
	$invites=$mod_strings['INVITATION'];
	$invitees_array = explode(';',$inviteesid);
	$subject = $invites.' : '.$subject;
	$record = $focus->id;
	foreach($invitees_array as $inviteeid)
	{
		if($inviteeid != '')
		{
			$description=getActivityDetails($desc,$inviteeid,"invite");
			$to_email = getUserEmailId('id',$inviteeid);
			send_mail('Calendar',$to_email,$current_user->user_name,'',$subject,$description);
		}
	}

}

function getActivityMailInfo($return_id,$status,$activity_type)
{
	$mail_data = Array();
	global $adb;
	$qry = "select * from vtiger_activity where activityid=?";
	$ary_res = $adb->pquery($qry, array($return_id));
	$send_notification = $adb->query_result($ary_res,0,"sendnotification");
	$subject = $adb->query_result($ary_res,0,"subject");
	$priority = $adb->query_result($ary_res,0,"priority");
	$st_date = $adb->query_result($ary_res,0,"date_start");
	$st_time = $adb->query_result($ary_res,0,"time_start");
	$end_date = $adb->query_result($ary_res,0,"due_date");
	$end_time = $adb->query_result($ary_res,0,"time_end");
	$location = $adb->query_result($ary_res,0,"location");

	$owner_qry = "select smownerid from vtiger_crmentity where crmid=?";
	$res = $adb->pquery($owner_qry, array($return_id));
	$owner_id = $adb->query_result($res,0,"smownerid");
	
	$usr_res = $adb->pquery("select count(*) as count from vtiger_users where id=?",array($owner_id));
	if($adb->query_result($usr_res, 0, 'count')>0) {
		$assignType = "U";
		$usr_id = $owner_id;
	}
	else {
		$assignType = "T";
		$group_qry = "select groupname from vtiger_groups where groupid=?";
		$grp_res = $adb->pquery($group_qry, array($owner_id));
		$grp_name = $adb->query_result($grp_res,0,"groupname");
	}

	$desc_qry = "select description from vtiger_crmentity where crmid=?";
	$des_res = $adb->pquery($desc_qry, array($return_id));
	$description = $adb->query_result($des_res,0,"description");

		
	$rel_qry = "select case vtiger_crmentity.setype when 'Leads' then vtiger_leaddetails.lastname when 'Accounts' then vtiger_account.accountname when 'Potentials' then vtiger_potential.potentialname when 'Quotes' then vtiger_quotes.subject when 'PurchaseOrder' then vtiger_purchaseorder.subject when 'SalesOrder' then vtiger_salesorder.subject when 'Invoice' then vtiger_invoice.subject when 'Campaigns' then vtiger_campaign.campaignname when 'HelpDesk' then vtiger_troubletickets.title  end as relname from vtiger_seactivityrel inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seactivityrel.crmid left join vtiger_leaddetails on vtiger_leaddetails.leadid = vtiger_seactivityrel.crmid  left join vtiger_account on vtiger_account.accountid=vtiger_seactivityrel.crmid left join vtiger_potential on vtiger_potential.potentialid=vtiger_seactivityrel.crmid left join vtiger_quotes on vtiger_quotes.quoteid= vtiger_seactivityrel.crmid left join vtiger_purchaseorder on vtiger_purchaseorder.purchaseorderid = vtiger_seactivityrel.crmid  left join vtiger_salesorder on vtiger_salesorder.salesorderid = vtiger_seactivityrel.crmid left join vtiger_invoice on vtiger_invoice.invoiceid = vtiger_seactivityrel.crmid  left join vtiger_campaign on vtiger_campaign.campaignid = vtiger_seactivityrel.crmid left join vtiger_troubletickets on vtiger_troubletickets.ticketid = vtiger_seactivityrel.crmid where vtiger_seactivityrel.activityid=?";
	$rel_res = $adb->pquery($rel_qry, array($return_id));
	$rel_name = $adb->query_result($rel_res,0,"relname");


	$cont_qry = "select * from vtiger_cntactivityrel where activityid=?";
	$cont_res = $adb->pquery($cont_qry, array($return_id));
	$cont_id = $adb->query_result($cont_res,0,"contactid");
	$cont_name = '';
	if($cont_id != '')
	{
		$cont_name = getContactName($cont_id);
	}
	$mail_data['mode'] = "edit";
	$mail_data['activity_mode'] = $activity_type;
	$mail_data['sendnotification'] = $send_notification;
	$mail_data['user_id'] = $usr_id;
	$mail_data['subject'] = $subject;
	$mail_data['status'] = $status;
	$mail_data['taskpriority'] = $priority;
	$mail_data['relatedto'] = $rel_name;
	$mail_data['contact_name'] = $cont_name;
	$mail_data['description'] = $description;
	$mail_data['assign_type'] = $assignType;
	$mail_data['group_name'] = $grp_name;
	$value = getaddEventPopupTime($st_time,$end_time,'24');
	$start_hour = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
	if($activity_type != 'Task' )
		$end_hour = $value['endhour'] .':'.$value['endmin'].''.$value['endfmt'];
	$mail_data['st_date_time']=getDisplayDate($st_date)." ".$start_hour;
	$mail_data['end_date_time']=getDisplayDate($end_date)." ".$end_hour;
	$mail_data['location']=$location;
	return $mail_data;


}

// User Select Customization
/**
 * Function returns the id of the User selected by current user in the picklist of the ListView or Calendar view of Current User
 * return String -  Id of the user that the current user has selected
 */
function calendarview_getSelectedUserId() {
	global $current_user, $default_charset;
	$only_for_user = htmlspecialchars(strip_tags($_REQUEST['onlyforuser']),ENT_QUOTES,$default_charset);
	if($only_for_user == '') $only_for_user = $current_user->id;
	return $only_for_user;
}

function calendarview_getSelectedUserFilterQuerySuffix() {	
	global $current_user, $adb;
	$only_for_user = calendarview_getSelectedUserId();
	$qcondition = '';
	if(!empty($only_for_user)) {
		if($only_for_user != 'ALL') {
			// For logged in user include the group records also.
			if($only_for_user == $current_user->id) {
				$user_group_ids = fetchUserGroupids($current_user->id);
				// User does not belong to any group? Let us reset to non-existent group
				if(!empty($user_group_ids)) $user_group_ids .= ',';
				else $user_group_ids = '';
				$user_group_ids .= $current_user->id;	
				$qcondition = " AND vtiger_crmentity.smownerid IN (" . $user_group_ids .")";
			} else {
				$qcondition = " AND vtiger_crmentity.smownerid = "  . $adb->sql_escape_string($only_for_user);
			}
		} 
	}
	return $qcondition;
}

/**
 * Function returns the data of the user selected by current user in the picklist of the ListView or Calendar view of Current User
 * @param $useridInUse - The Id of the user that the Current User has selected in dropdown picklist in Calendar modules listview or Calendar View
 * return string - The array of the events for the user that the current user has selected
 */
function calendarview_getUserSelectOptions($useridInUse) {
	global $adb, $app_strings, $current_user, $mod_strings;
	$users = $adb->query("SELECT id,user_name FROM vtiger_users WHERE status = 'Active' and deleted = 0");
	$userscount = $adb->num_rows($users);

	$userSelectdata = "<span style='padding-left: 10px; padding-right: 10px;'><b>" . $app_strings['LBL_LIST_OF'] . " : </b>";
    $userSelectdata .="<select class='small' onchange='fnRedirect();' name='onlyforuser'>";
    
    // Providing All option for administrators only
    if(is_admin($current_user)) {
		$userSelectdata .= "<option value='ALL'>" . $app_strings['COMBO_ALL'] . "</option>";
    } 
    
	$userSelectdata .= "<option value='$current_user->id'".(($current_user->id == $useridInUse)? "selected='true'":"").">".$mod_strings['LBL_MINE']."</option>";	
	
	for($index = 0; $index < $userscount; ++$index) {
		$userid = $adb->query_result($users, $index, 'id');
		if($userid == $current_user->id) {
			continue; // We have already taken care of listing at first.
		}		
		$username = $adb->query_result($users, $index, 'user_name');
		$userselect = '';
		if($userid == $useridInUse) $userselect = "selected='true'";
		$userSelectdata .= "<option value='$userid' $userselect>$username</option>";
	}
	$userSelectdata .= "</select></span>";
	return $userSelectdata;
}
// END

?>
