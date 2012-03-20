<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is FOSS Labs.
 * Portions created by FOSS Labs are Copyright (C) FOSS Labs.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('modules/Emails/Emails.php');
require_once('modules/Webmails/Webmails.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/CommonUtils.php');
require_once('modules/Webmails/MailParse.php');
require_once('modules/Webmails/MailBox.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Settings/MailScanner/core/MailAttachmentMIME.php');
global $current_user;

$local_log =& LoggerManager::getLogger('index');
$focus = new Emails();

$to_address = explode(";",$_REQUEST['to_list']);
$cc_address = explode(";",$_REQUEST['cc_list']);
$bcc_address = explode(";",$_REQUEST['bcc_list']);

$start_message=vtlib_purify($_REQUEST["start_message"]);
if($_REQUEST["mailbox"] && $_REQUEST["mailbox"] != "") {$mailbox=vtlib_purify($_REQUEST["mailbox"]);} else {$mailbox="INBOX";}

$MailBox = new MailBox($mailbox);
$mail = $MailBox->mbox;
$email = new Webmails($MailBox->mbox, $_REQUEST["mailid"]);
$subject = imap_utf8($email->subject);
$date = $email->date;
$array_tab = Array();
$email->loadMail($array_tab);
$msgData = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">',"",$email->body);
$content['attachtab'] = $email->attachtab;
while ($tmp = array_pop($content['attachtab'])){
	if ((!preg_match('/ATTACHMENT/i', $tmp['disposition'])) && $conf->display_text_attach && (preg_match('/text\/plain/i', $tmp['mime'])))
		$msgData .= '<hr />'.view_part_detail($mail, $mailid, $tmp['number'], $tmp['transfer'], $tmp['charset'], $charset);
}
$focus->column_fields['subject']=$subject;
$focus->column_fields["activitytype"]="Emails";

$ddate = date("Y-m-d",strtotime($date));
$dtime = date("h:m");
$focus->column_fields["assigned_user_id"] = $current_user->id;
$focus->column_fields["date_start"] = $ddate;
$focus->column_fields["time_start"] = $dtime;
//Set the flag as 'Webmails' to show up the sent date
$focus->column_fields["email_flag"] = "WEBMAIL";

//Save the To field information in vtiger_emaildetails
$all_to_ids = $email->to;
$focus->column_fields["saved_toid"] = implode(',',$all_to_ids);

//store the sent date in 'yyyy-mm-dd' format
$user_old_date_format = $current_user->date_format;
$current_user->date_format = 'yyyy-mm-dd';
		
$focus->column_fields["description"]=$msgData;

//to save the email details in vtiger_emaildetails vtiger_tables
$fieldid = $adb->query_result($adb->pquery('select fieldid from vtiger_field where tablename="vtiger_contactdetails" and fieldname="email" and columnname="email" and vtiger_field.presence in (0,2)', array()),0,'fieldid');

if(count($email->relationship) != 0) {
	$focus->column_fields['parent_id']=$email->relationship["id"].'@'.$fieldid.'|';

	$focus->save("Emails");
	if($email->relationship["type"] == "Contacts")
		add_attachment_to_contact($email->relationship["id"],$email,$focus->id);
}else {
	//if relationship is not available create a contact and relate the email to the contact
	require_once('modules/Contacts/Contacts.php');
	$contact_focus = new Contacts();
	//Populate the lastname as emailid if email doesn't have from name 
	if($email->fromname){
		$contact_focus->column_fields['lastname'] =$email->fromname;
	}else{
		$contact_focus->column_fields['lastname'] =$email->from;
	}
	
	$contact_focus->column_fields['email'] = $email->from;
	$contact_focus->column_fields["assigned_user_id"]=$current_user->id;
	$contact_focus->save("Contacts");
	$focus->column_fields['parent_id']=$contact_focus->id.'@'.$fieldid.'|';

	$focus->save("Emails");
	add_attachment_to_contact($contact_focus->id,$email,$focus->id);
}

function add_attachment_to_contact($cid,$email,$emailid) {
	// add vtiger_attachments to contact
	global $adb,$current_user,$default_charset;
	for($j=0;$j<2;$j++) {
	    if($j==0)
	    	$attachments=$email->downloadAttachments();
	    else
	    	$attachments=$email->downloadInlineAttachments();

	    $upload_filepath = decideFilePath();
	    for($i=0,$num_files=count($attachments);$i<$num_files;$i++)
	    {
			$current_id = $adb->getUniqueID("vtiger_crmentity");
			$date_var = $adb->formatDate(date('Y-m-d H:i:s'), true);	
	
			$filename = preg_replace("/[ ()-]+/", "_",$attachments[$i]["filename"]);
			preg_match_all('/=\?([^\?]+)\?([^\?]+)\?([^\?]+)\?=/', $filename, $matches);
			$totalmatches = count($matches[0]);
			
			for($index = 0; $index < $totalmatches; ++$index) {
				$charset = $matches[1][$index];
				$encoding= strtoupper($matches[2][$index]);
				$data    = $matches[3][$index];
				
				if($encoding == 'B') {
					$filename = base64_decode($data);
				} else if($encoding == 'Q') {
					$filename = quoted_printable_decode($data);
				}
				$filename = iconv(str_replace('_','-',$charset),$default_charset,$filename);
			}
			
			$saveasfile = $upload_filepath.'/'.$current_id.'_'.$filename;
	        $filetype = MailAttachmentMIME::detect($saveasfile);
			$filesize = $attachments[$i]["filesize"];

            $query = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?,?,?,?,?,?,?)";
            $qparams = array($current_id, $current_user->id, $current_user->id, 'Contacts Attachment', 'Uploaded from webmail during qualification', $date_var, $date_var);
            $result = $adb->pquery($query, $qparams);

            $sql = "insert into vtiger_attachments (attachmentsid,name,description,type,path) values(?,?,?,?,?)";
            $params = array($current_id, $filename, 'Uploaded '.$filename.' from webmail', $filetype, $upload_filepath);
            $result = $adb->pquery($sql, $params);
				
                if(!empty($result)){
                	
                	// Create document record
					$document = new Documents();
					$document->column_fields['notes_title']      = $filename;
					$document->column_fields['filename']         = $filename;
					$document->column_fields['filesize']		 = $filesize;
					$document->column_fields['filetype']		 = $filetype;
					$document->column_fields['filestatus']       = 1;
					$document->column_fields['filelocationtype'] = 'I';
					$document->column_fields['folderid']         = 1; // Default Folder 
					$document->column_fields['assigned_user_id'] = $current_user->id;
					$document->save('Documents');                	
                	
	                $sql1 = "insert into vtiger_senotesrel values(?,?)";
	                $params1 = array($cid, $document->id);
	                $result = $adb->pquery($sql1, $params1);
	                
	                $sql1 = "insert into vtiger_seattachmentsrel values(?,?)";
	                $params1 = array($document->id, $current_id);
	                $result = $adb->pquery($sql1, $params1); 
	                
	                $sql1 = "insert into vtiger_seattachmentsrel values(?,?)";
	                $params1 = array($emailid, $current_id);
	                $result = $adb->pquery($sql1, $params1); 
                }

		//we have to add attachmentsid_ as prefix for the filename
		$move_filename = $upload_filepath.'/'.$current_id.'_'.$filename;

		$fp = fopen($move_filename, "w") or die("Can't open file");
		fputs($fp, base64_decode($attachments[$i]["filedata"]));
		fclose($fp);
	    }
	}
}
//Display the sent date in logged in user date format
$current_user->date_format = $user_old_date_format;
	
function view_part_detail($mail,$mailid,$part_no, &$transfer, &$msg_charset, &$charset)
{
        $text = imap_fetchbody($mail,$mailid,$part_no);
        if ($transfer == 'BASE64')
                $str = nl2br(imap_base64($text));
        elseif($transfer == 'QUOTED-PRINTABLE')
                $str = nl2br(quoted_printable_decode($text));
        else
                $str = nl2br($text);
        return ($str);
}

$_REQUEST['parent_id'] = $focus->column_fields['parent_id'];

$return_id = vtlib_purify($_REQUEST["mailid"]);
$return_module='Webmails';
$return_action='ListView';

if($_POST["ajax"] != "true")
	header("Location: index.php?action=$return_action&module=$return_module&record=$return_id"); 

return;
?>