<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Users/Save.php,v 1.14 2005/03/17 06:37:39 rank Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/Users/Users.php');
require_once('include/logging.php');
require_once('include/utils/UserInfoUtil.php');
$log =& LoggerManager::getLogger('index');


global $adb;
$user_name = $_REQUEST['userName'];
if(isset($_REQUEST['status']) && $_REQUEST['status'] != '')
	$_REQUEST['status']=$_REQUEST['status'];
else
	$_REQUEST['status']='Active';

if(isset($_REQUEST['dup_check']) && $_REQUEST['dup_check'] != '')
{
        $user_query = "SELECT user_name FROM vtiger_users WHERE user_name =?";
        $user_result = $adb->pquery($user_query, array($user_name));
        $group_query = "SELECT groupname FROM vtiger_groups WHERE groupname =?";
        $group_result = $adb->pquery($group_query, array($user_name));
        
        if($adb->num_rows($user_result) > 0) {
		echo $mod_strings['LBL_USERNAME_EXIST'];
		die;
		} elseif($adb->num_rows($group_result) > 0) {
			echo $mod_strings['LBL_GROUPNAME_EXIST'];
			die;
		} else {
	        echo 'SUCCESS';
	        die;
	}
}
if((empty($_SESSION['Users_FORM_TOKEN']) || $_SESSION['Users_FORM_TOKEN']
		!== (int)$_REQUEST['form_token']) && $_REQUEST['deleteImage'] != 'true' &&
		$_REQUEST['changepassword'] != 'true') {
	header("Location: index.php?action=Error&module=Users&error_string=".
			urlencode($app_strings['LBL_PERMISSION']));
	die;
}

if (isset($_POST['record']) && !is_admin($current_user) && $_POST['record'] != $current_user->id) echo ("Unauthorized access to user administration.");
elseif (!isset($_POST['record']) && !is_admin($current_user)) echo ("Unauthorized access to user administration.");

$focus = new Users();
if(isset($_REQUEST["record"]) && $_REQUEST["record"] != '')
{
    $focus->mode='edit';
	$focus->id = $_REQUEST["record"];
}
else
{
    $focus->mode='';
}    


if($_REQUEST['deleteImage'] == 'true') {
	$focus->id = $_REQUEST['recordid'];
	$focus->deleteImage();
	echo "SUCCESS";
	exit;
}

if($_REQUEST['changepassword'] == 'true')
{
	$focus->retrieve_entity_info($_REQUEST['record'],'Users');
	$focus->id = $_REQUEST['record'];
if (isset($_POST['new_password'])) {
		$new_pass = $_POST['new_password'];
		$new_passwd = $_POST['new_password'];
		$new_pass = md5($new_pass);
		$old_pass = $_POST['old_password'];
		$uname = $_POST['user_name'];
		if (!$focus->change_password($_POST['old_password'], $_POST['new_password'])) {
		
			header("Location: index.php?action=Error&module=Users&error_string=".urlencode($focus->error_string));
		exit;
}
}
	
}	

    
//save user Image
if(! $_REQUEST['changepassword'] == 'true')
{
	if(strtolower($current_user->is_admin) == 'off'  && $current_user->id != $focus->id)
	{
		$log->fatal("SECURITY:Non-Admin ". $current_user->id . " attempted to change settings for user:". $focus->id);
		header("Location: index.php?module=Users&action=Logout");
		exit;
	}
	if(strtolower($current_user->is_admin) == 'off'  && isset($_POST['is_admin']) && strtolower($_POST['is_admin']) == 'on')
	{
		$log->fatal("SECURITY:Non-Admin ". $current_user->id . " attempted to change is_admin settings for user:". $focus->id);
		header("Location: index.php?module=Users&action=Logout");
		exit;
	}
	
	if (!isset($_POST['is_admin'])) $_REQUEST["is_admin"] = 'off';
	//Code contributed by mike crowe for rearrange the home page and tab
	if (!isset($_POST['deleted'])) $_REQUEST["deleted"] = '0';
	if (!isset($_POST['homeorder']) || $_POST['homeorder'] == "" ) $_REQUEST["homeorder"] = 'ILTI,QLTQ,ALVT,PLVT,CVLVT,HLT,OLV,GRT,OLTSO';
	if(isset($_REQUEST['internal_mailer']) && $_REQUEST['internal_mailer'] == 'on')
		$focus->column_fields['internal_mailer'] = 1;
	else
		$focus->column_fields['internal_mailer'] = 0;
	if(isset($_SESSION['internal_mailer']) && $_SESSION['internal_mailer'] != $focus->column_fields['internal_mailer'])
		$_SESSION['internal_mailer'] = $focus->column_fields['internal_mailer'];
	setObjectValuesFromRequest($focus);
	
	// Added for Reminder Popup support
	$query_prev_interval = $adb->pquery("SELECT reminder_interval from vtiger_users where id=?",array($focus->id));
	$prev_reminder_interval = $adb->query_result($query_prev_interval,0,'reminder_interval');
	
	$focus->saveentity("Users");
	//$focus->imagename = $image_upload_array['imagename'];
	$focus->saveHomeStuffOrder($focus->id);
	SaveTagCloudView($focus->id);

	// Added for Reminder Popup support
	$focus->resetReminderInterval($prev_reminder_interval);

	$return_id = $focus->id;

if (isset($_POST['user_name']) && isset($_POST['new_password'])) {
		$new_pass = $_POST['new_password'];
		$new_passwd = $_POST['new_password'];
		$new_pass = md5($new_pass);
		$uname = $_POST['user_name'];
		if (!$focus->change_password($_POST['confirm_new_password'], $_POST['new_password'])) {
		
			header("Location: index.php?action=Error&module=Users&error_string=".urlencode($focus->error_string));
		exit;
}
}  

if(isset($focus->id) && $focus->id != '')
{

  if(isset($_POST['user_role']))
  {
    updateUser2RoleMapping($_POST['user_role'],$focus->id);
  }
  if(isset($_POST['group_name']) && $_POST['group_name'] != '')
  {
    updateUsers2GroupMapping($_POST['group_name'],$focus->id);
  }
}
else
{
  if(isset($_POST['user_role']))
  {
    insertUser2RoleMapping($_POST['user_role'],$focus->id);
  }
  if(isset($_POST['group_name']))
  {
    insertUsers2GroupMapping($_POST['group_name'],$focus->id);
  }
}

//Creating the Privileges Flat File
require_once('modules/Users/CreateUserPrivilegeFile.php');
createUserPrivilegesfile($focus->id);
createUserSharingPrivilegesfile($focus->id);

}
if(isset($_POST['return_module']) && $_POST['return_module'] != "") $return_module = vtlib_purify($_REQUEST['return_module']);
else $return_module = "Users";
if(isset($_POST['return_action']) && $_POST['return_action'] != "") $return_action = vtlib_purify($_REQUEST['return_action']);
else $return_action = "DetailView";
if(isset($_POST['return_id']) && $_POST['return_id'] != "") $return_id = vtlib_purify($_REQUEST['return_id']);
if(isset($_REQUEST['activity_mode']))   $activitymode = '&activity_mode='.vtlib_purify($_REQUEST['activity_mode']);
if(isset($_POST['parenttab'])) $parenttab = getParentTab();

$log->debug("Saved record with id of ".$return_id);

//Asha: Added Check to see if the mode is User Creation and if yes, then sending the email notification to the User with Login details.
if($_REQUEST['mode'] == 'create') {
	global $app_strings, $mod_strings, $default_charset;
	require_once('modules/Emails/mail.php');
    $user_emailid = $focus->column_fields['email1'];
	// send email on Create user only if NOTIFY_OWNER_EMAILS is set to true

	$subject = $mod_strings['User Login Details'];
	$email_body = $app_strings['MSG_DEAR']." ". $focus->column_fields['last_name'] .",<br><br>";
	$email_body .= $app_strings['LBL_PLEASE_CLICK'] . " <a href='" . $site_URL . "' target='_blank'>"
									. $app_strings['LBL_HERE'] . "</a> " . $mod_strings['LBL_TO_LOGIN'] . "<br><br>";
	$email_body .= $mod_strings['LBL_USER_NAME'] . " : " . $focus->column_fields['user_name'] . "<br>";
	$email_body .= $mod_strings['LBL_PASSWORD'] . " : " . $focus->column_fields['user_password'] . "<br>";
	$email_body .= $mod_strings['LBL_ROLE_NAME'] . " : " . getRoleName($_POST['user_role']) . "<br>";
	$email_body .= "<br>" . $app_strings['MSG_THANKS'] . "<br>" . $current_user->user_name;
	$email_body = htmlentities($email_body, ENT_QUOTES, $default_charset);

	$mail_status = send_mail('Users',$user_emailid,$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$email_body);
	if($mail_status != 1) {
		$mail_status_str = $user_emailid."=".$mail_status."&&&";		
		$error_str = getMailErrorString($mail_status_str);
	}
}
$location = "Location: index.php?action=".vtlib_purify($return_action)."&module=".vtlib_purify($return_module)."&record=".vtlib_purify($return_id);

if($_REQUEST['modechk'] != 'prefview') {
	$location .= "&parenttab=".vtlib_purify($parenttab);
}

if ($error_str != '') {	
    $user = $focus->column_fields['user_name'];
	$location .= "&user=$user&$error_str";
}

header($location);

?>
