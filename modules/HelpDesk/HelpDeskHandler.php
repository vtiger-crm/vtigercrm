<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/Emails/mail.php';

class HelpDeskHandler extends VTEventHandler {

	function handleEvent($eventName, $entityData) {
		global $log, $adb;

		if($eventName == 'vtiger.entity.aftersave.final') {
			$moduleName = $entityData->getModuleName();
			if ($moduleName == 'HelpDesk') {
				$ticketId = $entityData->getId();
				$adb->pquery('UPDATE vtiger_troubletickets SET from_portal=0 WHERE ticketid=?', array($ticketId));
			}
		}
	}
}

function HelpDesk_nofifyOnPortalTicketCreation($entityData) {
	$adb = PearDatabase::getInstance();
	$moduleName = $entityData->getModuleName();
	$wsId = $entityData->getId();
	$parts = explode('x', $wsId);
	$entityId = $parts[1];

	$ownerIdInfo = getRecordOwnerId($entityId);
	if(!empty($ownerIdInfo['Users'])) {
		$ownerId = $ownerIdInfo['Users'];
		$to_email = getUserEmailId('id',$ownerId);
	}
	if(!empty($ownerIdInfo['Groups'])) {
		$ownerId = $ownerIdInfo['Groups'];
		$to_email = implode(',', getDefaultAssigneeEmailIds($ownerId));
	}
	$wsParentId = $entityData->get('parent_id');
	$parentIdParts = explode('x', $wsParentId);
	$parentId = $parentIdParts[1];

	$subject = "[From Portal] " .$entityData->get('ticket_no')." [ Ticket ID : $entityId ] ".$entityData->get('ticket_title');
	$contents = ' Ticket No : '.$entityData->get('ticket_no'). '<br> Ticket ID : '.$entityId.'<br> Ticket Title : '.
							$entityData->get('ticket_title').'<br><br>'.$entityData->get('description');

	//get the contact email id who creates the ticket from portal and use this email as from email id in email
	$result = $adb->pquery("SELECT email FROM vtiger_contactdetails WHERE contactid=?", array($parentId));
	$contact_email = $adb->query_result($result,0,'email');
	$from_email = $contact_email;

	//send mail to assigned to user
	$adb->println("Send mail to the user who is the owner of the module about the portal ticket");
	$mail_status = send_mail('HelpDesk',$to_email,'',$from_email,$subject,$contents);

	//send mail to the customer(contact who creates the ticket from portal)
	$adb->println("Send mail to the customer(contact) who creates the portal ticket");
	$mail_status = send_mail('Contacts',$contact_email,'',$from_email,$subject,$contents);
}

function HelpDesk_notifyOnPortalTicketComment($entityData) {
	$adb = PearDatabase::getInstance();
	$moduleName = $entityData->getModuleName();
	$wsId = $entityData->getId();
	$parts = explode('x', $wsId);
	$entityId = $parts[1];

	$ownerIdInfo = getRecordOwnerId($entityId);

	if(!empty($ownerIdInfo['Users'])) {
		$ownerId = $ownerIdInfo['Users'];
		$ownerName = getOwnerName($ownerId);
		$to_email = getUserEmailId('id',$ownerId);
	}
	if(!empty($ownerIdInfo['Groups'])) {
		$ownerId = $ownerIdInfo['Groups'];
		$groupInfo = getGroupName($ownerId);
		$ownerName = $groupInfo[0];
		$to_email = implode(',', getDefaultAssigneeEmailIds($ownerId));
	}
	$wsParentId = $entityData->get('parent_id');
	$parentIdParts = explode('x', $wsParentId);
	$parentId = $parentIdParts[1];

	$entityDelta = new VTEntityDelta();
	$oldComments = $entityDelta->getOldValue($entityData->getModuleName(), $entityId, 'comments');
	$newComments = $entityDelta->getCurrentValue($entityData->getModuleName(), $entityId, 'comments');
	$commentDiff = str_replace($oldComments, '', $newComments);
	$latestComment = strip_tags($commentDiff);

	//send mail to the assigned to user when customer add comment
	$subject = getTranslatedString('LBL_RESPONDTO_TICKETID', $moduleName)."##". $entityId."##". getTranslatedString('LBL_CUSTOMER_PORTAL', $moduleName);
	$contents = getTranslatedString('Dear', $moduleName)." ".$ownerName.","."<br><br>"
						.getTranslatedString('LBL_CUSTOMER_COMMENTS', $moduleName)."<br><br>
						<b>".$latestComment."</b><br><br>"
						.getTranslatedString('LBL_RESPOND', $moduleName)."<br><br>"
						.getTranslatedString('LBL_REGARDS', $moduleName)."<br>"
						.getTranslatedString('LBL_SUPPORT_ADMIN', $moduleName);

	//get the contact email id who creates the ticket from portal and use this email as from email id in email
	$result = $adb->pquery("SELECT lastname, firstname, email FROM vtiger_contactdetails WHERE contactid=?", array($parentId));
	$customername = $adb->query_result($result,0,'firstname').' '.$adb->query_result($result,0,'lastname');
	$customername = decode_html($customername);//Fix to display the original UTF-8 characters in sendername instead of ascii characters
	$from_email = $adb->query_result($result,0,'email');

	//send mail to assigned to user
	$adb->println("Send mail to the user who is the owner of the module about the portal ticket");
	$mail_status = send_mail('HelpDesk',$to_email,'',$from_email,$subject,$contents);
}

function HelpDesk_notifyParentOnTicketChange($entityData) {
	global $HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID;
	$adb = PearDatabase::getInstance();
	$moduleName = $entityData->getModuleName();
	$wsId = $entityData->getId();
	$parts = explode('x', $wsId);
	$entityId = $parts[1];

	$wsParentId = $entityData->get('parent_id');
	$parentIdParts = explode('x', $wsParentId);
	$parentId = $parentIdParts[1];

	$isNew = $entityData->isNew();

	if(!$isNew) {
		$reply = 'Re : ';
	} else {
		$reply = '';
	}

	$subject = $entityData->get('ticket_no') . ' [ '.getTranslatedString('LBL_TICKET_ID', $moduleName)
						.' : '.$entityId.' ] '.$reply.$entityData->get('ticket_title');
	$bodysubject = getTranslatedString('Ticket No', $moduleName) .":<br>" . $entityData->get('ticket_no')
						. "<br>" . getTranslatedString('LBL_TICKET_ID', $moduleName).' : '.$entityId.'<br> '
						.getTranslatedString('LBL_SUBJECT', $moduleName).$entityData->get('ticket_title');

	$emailoptout = 0;

	//To get the emailoptout vtiger_field value and then decide whether send mail about the tickets or not
	if($parentId != '') {
		$parent_module = getSalesEntityType($parentId);
		if($parent_module == 'Contacts') {
			$result = $adb->pquery('SELECT email, emailoptout FROM vtiger_contactdetails WHERE contactid=?',
										array($parentId));
			$emailoptout = $adb->query_result($result,0,'emailoptout');
			$parent_email = $contact_mailid = $adb->query_result($result,0,'email');
			$displayValueArray = getEntityName($parent_module, $parentId);
			if (!empty($displayValueArray)) {
				foreach ($displayValueArray as $key => $field_value) {
					$contact_name = $field_value;
				}
			}
			$parentname = $contactname = $contact_name;

			//Get the status of the vtiger_portal user. if the customer is active then send the vtiger_portal link in the mail
			if($contact_mailid != '') {
				$sql = "SELECT * FROM vtiger_portalinfo WHERE user_name=?";
				$isPortalUser = $adb->query_result($adb->pquery($sql, array($contact_mailid)),0,'isactive');
			}
		}
		if($parent_module == 'Accounts') {
			$result = $adb->pquery("SELECT accountname, emailoptout, email1 FROM vtiger_account WHERE accountid=?",
										array($parentId));
			$emailoptout = $adb->query_result($result,0,'emailoptout');
			$parent_email = $adb->query_result($result,0,'email1');
			$parentname = $adb->query_result($result,0,'accountname');
		}

		//added condition to check the emailoptout(this is for contacts and vtiger_accounts.)
		if($emailoptout == 0) {

			if($isPortalUser == 1){
				$url = "<a href='".$PORTAL_URL."/index.php?module=HelpDesk&action=index&ticketid=".$entityId."&fun=detail'>".$mod_strings['LBL_TICKET_DETAILS']."</a>";
				$email_body = $bodysubject.'<br><br>'.HelpDesk::getPortalTicketEmailContents($entityData);
			}
			else {
				$email_body = HelpDesk::getTicketEmailContents($entityData);
			}
			if($isNew) {
				$mail_status = send_mail('HelpDesk',$parent_email,$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$email_body);
			} else {
				$entityDelta = new VTEntityDelta();
				$statusHasChanged = $entityDelta->hasChanged($entityData->getModuleName(), $entityId, 'ticketstatus');
				$solutionHasChanged = $entityDelta->hasChanged($entityData->getModuleName(), $entityId, 'solution');
				$ownerHasChanged = $entityDelta->hasChanged($entityData->getModuleName(), $entityId, 'assigned_user_id');
				$commentsHasChanged = $entityDelta->hasChanged($entityData->getModuleName(), $entityId, 'comments');
				if(($statusHasChanged && $entityData->get('ticketstatus') == "Closed") || $commentsHasChanged || $solutionHasChanged || $ownerHasChanged) {

					$mail_status = send_mail('HelpDesk',$parent_email,$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$email_body);
				}
			}
			$mail_status_str .= $parent_email."=".$mail_status."&&&";

		} else {
			$adb->println("'".$parentname."' is not want to get the email about the ticket details as emailoptout is selected");
		}

		if ($mail_status != '') {
			$mail_error_status = getMailErrorString($mail_status_str);
		}
	}
}

function HelpDesk_notifyOwnerOnTicketChange($entityData) {
	global $HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID;

	$moduleName = $entityData->getModuleName();
	$wsId = $entityData->getId();
	$parts = explode('x', $wsId);
	$entityId = $parts[1];

	$isNew = $entityData->isNew();

	if(!$isNew) {
		$reply = 'Re : ';
	} else {
		$reply = '';
	}

	$subject = $entityData->get('ticket_no') . ' [ '.getTranslatedString('LBL_TICKET_ID', $moduleName)
						.' : '.$entityId.' ] '.$reply.$entityData->get('ticket_title');

	$email_body = HelpDesk::getTicketEmailContents($entityData);
	if(PerformancePrefs::getBoolean('NOTIFY_OWNER_EMAILS', true) === true){
		//send mail to the assigned to user and the parent to whom this ticket is assigned
		require_once('modules/Emails/mail.php');
		$wsAssignedUserId = $entityData->get('assigned_user_id');
		$userIdParts = explode('x', $wsAssignedUserId);
		$ownerId = $userIdParts[1];
		$ownerType = vtws_getOwnerType($ownerId);

		if($ownerType == 'Users') {
			$to_email = getUserEmailId('id',$ownerId);
		}
		if($ownerType == 'Groups') {
			$to_email = implode(',', getDefaultAssigneeEmailIds($ownerId));
		}
		if($to_email != '') {
			if($isNew) {
				$mail_status = send_mail('HelpDesk',$to_email,$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$email_body);
			} else {
				$entityDelta = new VTEntityDelta();
				$statusHasChanged = $entityDelta->hasChanged($entityData->getModuleName(), $entityId, 'ticketstatus');
				$solutionHasChanged = $entityDelta->hasChanged($entityData->getModuleName(), $entityId, 'solution');
				$ownerHasChanged = $entityDelta->hasChanged($entityData->getModuleName(), $entityId, 'assigned_user_id');
				$commentsHasChanged = $entityDelta->hasChanged($entityData->getModuleName(), $entityId, 'comments');
				if(($statusHasChanged && $entityData->get('ticketstatus') == "Closed") || $commentsHasChanged || $solutionHasChanged || $ownerHasChanged) {

					$mail_status = send_mail('HelpDesk',$to_email,$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$email_body);
				}
			}
			$mail_status_str = $to_email."=".$mail_status."&&&";

		} else {
			$mail_status_str = "'".$to_email."'=0&&&";
		}

		if ($mail_status != '') {
			$mail_error_status = getMailErrorString($mail_status_str);
		}
	}
}

?>
