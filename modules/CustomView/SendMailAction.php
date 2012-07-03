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
require_once('modules/CustomView/CustomView.php');

global $current_user;
global $adb;

$idlist = vtlib_purify($_POST['idlist']);
$viewid = vtlib_purify($_REQUEST['viewname']);
$camodule=vtlib_purify($_REQUEST['return_module']);
$storearray = explode(";",$idlist);
if(isset($viewid) && trim($viewid) != "")
{
	$oCustomView = new CustomView();
	$CustomActionDtls = $oCustomView->getCustomActionDetails($viewid);
	if(isset($CustomActionDtls))
	{
		$subject = $CustomActionDtls["subject"];
		$contents = $CustomActionDtls["content"];
	}
}

if(trim($subject) != "")
{
	if(isset($storearray) && $camodule != "")
	{
		foreach($storearray as $id)
		{
			if($id == '') continue;
			if($camodule == "Contacts")
			{
				$sql="select * from vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid where vtiger_crmentity.deleted =0 and vtiger_contactdetails.contactid=?";
				$result = $adb->pquery($sql, array($id));
				$camodulerow = $adb->fetch_array($result);
				if(isset($camodulerow))
				{
					$emailid = $camodulerow["email"];
					$otheremailid = $camodulerow["otheremail"];
					$secondaryemail = $camodulerow["secondaryemail"];

					if(trim($emailid) != "")
					{
						SendMailtoCustomView($camodule,$id,$emailid,$current_user->id,$subject,$contents);
					}elseif(trim($otheremailid) != "")
					{
						SendMailtoCustomView($camodule,$id,$otheremailid,$current_user->id,$subject,$contents);
					}elseif(trim($secondaryemail) != "")
					{
						SendMailtoCustomView($camodule,$id,$secondaryemail,$current_user->id,$subject,$contents);
					}
					else
					{
						$adb->println("There is no email id for this Contact. Please give any email id.");
					}
				}

			}elseif($camodule == "Leads")
			{
				$sql="select * from vtiger_leaddetails inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_leaddetails.leadid where vtiger_crmentity.deleted =0 and vtiger_leaddetails.leadid=?";
				$result = $adb->pquery($sql, array($id));
				$camodulerow = $adb->fetch_array($result);
				if(isset($camodulerow))
				{
					$emailid = $camodulerow["email"];
					$secondaryemail = $camodulerow["secondaryemail"];

					if(trim($emailid) != "")
					{
						SendMailtoCustomView($camodule,$id,$emailid,$current_user->id,$subject,$contents);
					}
					elseif($trim($secondaryemail) != "")
					{
						SendMailtoCustomView($camodule,$id,$secondaryemail,$current_user->id,$subject,$contents);
					}
					else
					{
						$adb->println("There is no email id for this Lead. Please give any email id.");
					}
				}
			}elseif($camodule == "Accounts")
			{
				$sql="select * from vtiger_account inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_account.accountid where vtiger_crmentity.deleted =0 and vtiger_account.accountid=?";
				$result = $adb->pquery($sql, array($id));
				$camodulerow = $adb->fetch_array($result);
				if(isset($camodulerow))
				{
					$emailid = $camodulerow["email1"];
					$otheremailid = $camodulerow["email2"];

					if(trim($emailid) != "")
					{
						SendMailtoCustomView($camodule,$id,$emailid,$current_user->id,$subject,$contents);
					}
					elseif(trim($otheremailid) != "")
					{
						SendMailtoCustomView($camodule,$id,$otheremailid,$current_user->id,$subject,$contents);
					}
					else
					{
						$adb->println("There is no email id for this Account. Please give any email id.");
					}
				}	
			}
		}
	}
}

function SendMailtoCustomView($module,$id,$to,$current_user_id,$subject,$contents)
{

	require_once("modules/Emails/class.phpmailer.php");

	$mail = new PHPMailer();

	$mail->Subject = $subject;
	$mail->Body    = nl2br($contents);
	$mail->IsSMTP();

	if($current_user_id != '')
	{
		global $adb;
		$sql = "select * from vtiger_users where id= ?";
		$result = $adb->pquery($sql, array($current_user_id));
		$from = $adb->query_result($result,0,'email1');
		$initialfrom = $adb->query_result($result,0,'user_name');
	}
		global $adb;
		$mailserverresult=$adb->pquery("select * from vtiger_systems where server_type=?", array('email'));
		$mail_server = $adb->query_result($mailserverresult,0,'server');
		$mail_server_username = $adb->query_result($mailserverresult,0,'server_username');
		$mail_server_password = $adb->query_result($mailserverresult,0,'server_password');
		$smtp_auth = $adb->query_result($mailserverresult,0,'smtp_auth');

		$adb->println("Mail Server Details : '".$mail_server."','".$mail_server_username."','".$mail_server_password."'");
		$_REQUEST['server']=$mail_server;

	$mail->Host = $mail_server;
	$mail->SMTPAuth = $smtp_auth;
	$mail->Username = $mail_server_username;
	$mail->Password = $mail_server_password;
	$mail->From = $from;
	$mail->FromName = $initialfrom;

	$mail->AddAddress($to);
	$mail->AddReplyTo($from);
	$mail->WordWrap = 50;

	$mail->IsHTML(true);
	$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

	$adb->println("Mail sending process : To => '".$to."', From => '".$from."'");
	if(!$mail->Send())
	{
		$adb->println("(CustomView/SendMailAction.php) Error in Mail Sending : ".$mail->ErrorInfo);
		$errormsg = "Mail Could not be sent...";
	}
	else
	{
		$adb->println("(CustomView/SendMailAction.php) Mail has been Sent to => ".$to);
	}

}
header("Location: index.php?action=index&module=$camodule&viewname=$viewid");
?>