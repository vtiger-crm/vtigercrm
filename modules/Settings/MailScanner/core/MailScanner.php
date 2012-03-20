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

require_once('modules/Settings/MailScanner/core/MailBox.php');
require_once('modules/Settings/MailScanner/core/MailAttachmentMIME.php');

/**
 * Mail Scanner provides the ability to scan through the given mailbox
 * applying the rules configured.
 */
class Vtiger_MailScanner {
	// MailScanner information instance
	var $_scannerinfo = false;
	// Reference mailbox to use
	var $_mailbox = false;

	// Ignore scanning the folders always
	var $_generalIgnoreFolders = Array( "INBOX.Trash", "INBOX.Drafts", "[Gmail]/Spam", "[Gmail]/Trash", "[Gmail]/Drafts" );

	/** DEBUG functionality. */
	var $debug = false;	
	function log($message) {
		global $log;
		if($log && $this->debug) { $log->debug($message); }
		else if($this->debug) echo "$message\n";
	}

	/**
	 * Constructor.
	 */
	function __construct($scannerinfo) {
		$this->_scannerinfo = $scannerinfo;
	}

	/**
	 * Get mailbox instance configured for the scan
	 */
	function getMailBox() {
		if(!$this->_mailbox) {
			$this->_mailbox = new Vtiger_MailBox($this->_scannerinfo);
			$this->_mailbox->debug = $this->debug;
		}			
		return $this->_mailbox;
	}

	/**
	 * Start Scanning.
	 */
	function performScanNow() {
		// Check if rules exists to proceed
		$rules = $this->_scannerinfo->rules;

		if(empty($rules)) {
			$this->log("No rules setup for scanner [". $this->_scannerinfo->scannername . "] SKIPING\n");
			return;
		}

		// Build ignore folder list
		$ignoreFolders =  Array() + $this->_generalIgnoreFolders;
		$folderinfoList = $this->_scannerinfo->getFolderInfo();
		foreach($folderinfoList as $foldername=>$folderinfo) {
			if(!$folderinfo[enabled]) $ignoreFolders[] = $foldername;
		}

		// Get mailbox instance to work with
		$mailbox = $this->getMailBox();
		$mailbox->connect();

		/** Loop through all the folders. */
		$folders = $mailbox->getFolders();

		if($folders) $this->log("Folders found: " . implode(',', $folders) . "\n");

		foreach($folders as $lookAtFolder) {
			// Skip folder scanning?
			if(in_array($lookAtFolder, $ignoreFolders)) {
				$this->log("\nIgnoring Folder: $lookAtFolder\n");
				continue; 
			}
			// If a new folder has been added we should avoid scanning it
			if(!isset($folderinfoList[$lookAtFolder])) {
				$this->log("\nSkipping New Folder: $lookAtFolder\n");
				continue;
			}

			// Search for mail in the folder
			$mailsearch = $mailbox->search($lookAtFolder);
			$this->log($mailsearch? "Total Mails Found in [$lookAtFolder]: " . count($mailsearch) : "No Mails Found in [$lookAtFolder]");

			// No emails? Continue with next folder
			if(empty($mailsearch)) continue;

			// Loop through each of the email searched
			foreach($mailsearch as $messageid) {
				// Fetch only header part first, based on account lookup fetch the body.
				$mailrecord = $mailbox->getMessage($messageid, false);
				$mailrecord->debug = $mailbox->debug;
				$mailrecord->log();

				// If the email is already scanned & rescanning is not set, skip it
				if($this->isMessageScanned($mailrecord, $lookAtFolder)) {
					$this->log("\nMessage already scanned [$mailrecord->_subject], IGNORING...\n");
					unset($mailrecord);					
					continue;
				}

				// Apply rules configured for the mailbox
				$crmid = false;
				foreach($rules as $mailscannerrule) {
					$crmid = $this->applyRule($mailscannerrule, $mailrecord, $mailbox, $messageid);
					if($crmid) {
						break; // Rule was successfully applied and action taken
					}				
				}
				// Mark the email message as scanned
				$this->markMessageScanned($mailrecord, $crmid);
				$mailbox->markMessage($messageid);

				/** Free the resources consumed. */
				unset($mailrecord);
			}
			/* Update lastscan for this folder and reset rescan flag */
			// TODO: Update lastscan only if all the mail searched was parsed successfully?
			$rescanFolderFlag = false;
			$this->updateLastScan($lookAtFolder, $rescanFolderFlag);
		}
		// Close the mailbox at end
		$mailbox->close();
	}

	/**
	 * Apply all the rules configured for a mailbox on the mailrecord.
	 */
	function applyRule($mailscannerrule, $mailrecord, $mailbox, $messageid) {
		// If no actions are set, don't proceed
		if(empty($mailscannerrule->actions)) return false;

		// Check if rule is defined for the body
		$bodyrule = $mailscannerrule->hasBodyRule();

		if($bodyrule) {
			// We need the body part for rule evaluation
			$mailrecord->fetchBody($mailbox->_imap, $messageid);
		}

		// Apply rule to check if record matches the criteria
		$matchresult = $mailscannerrule->applyAll($mailrecord, $bodyrule);
		
		// If record matches the conditions fetch body to take action.
		$crmid = false;
		if($matchresult) {
			$mailrecord->fetchBody($mailbox->_imap, $messageid);
			$crmid = $mailscannerrule->takeAction($this, $mailrecord, $matchresult);
		}
		// Return the CRMID
		return $crmid;
	}

	/**
	 * Mark the email as scanned.
	 */
	function markMessageScanned($mailrecord, $crmid=false) {
		global $adb;
		if($crmid === false) $crmid = null;
		// TODO Make sure we have unique entry
		$adb->pquery("INSERT INTO vtiger_mailscanner_ids(scannerid, messageid, crmid) VALUES(?,?,?)",
			Array($this->_scannerinfo->scannerid, $mailrecord->_uniqueid, $crmid));
	}

	/**
	 * Check if email was scanned.
	 */
	function isMessageScanned($mailrecord, $lookAtFolder) {
		global $adb;
		$messages = $adb->pquery("SELECT * FROM vtiger_mailscanner_ids WHERE scannerid=? AND messageid=?",
			Array($this->_scannerinfo->scannerid, $mailrecord->_uniqueid));

		$folderRescan = $this->_scannerinfo->needRescan($lookAtFolder);
		$isScanned = false;

		if($adb->num_rows($messages)) {
			$isScanned = true;

			// If folder is scheduled for rescan and earlier message was not acted upon?
			$relatedCRMId = $adb->query_result($messages, 0, 'crmid');

			if($folderRescan && empty($relatedCRMId)) {
				$adb->pquery("DELETE FROM vtiger_mailscanner_ids WHERE scannerid=? AND messageid=?",
					Array($this->_scannerinfo->scannerid, $mailrecord->_uniqueid));
				$isScanned = false;
			}
		}
		return $isScanned;
	}

	/**
	 * Update last scan on the folder.
	 */
	function updateLastscan($folder) {
		$this->_scannerinfo->updateLastscan($folder);
	}

	/**
	 * Convert string to integer value. 
	 * @param $strvalue 
	 * @returns false if given contain non-digits, else integer value
	 */
	function __toInteger($strvalue) {
		$ival = intval($strvalue);
		$intvalstr = "$ival";
		if(strlen($strvalue) == strlen($intvalstr)) {
			return $ival;
		}
		return false;
	}

	/** Lookup functionality. */
	var $_cachedContactIds = Array();
	var $_cachedAccountIds = Array();
	var $_cachedTicketIds  = Array();

	var $_cachedAccounts = Array();
	var $_cachedContacts = Array();
	var $_cachedTickets  = Array();

	/**
	 * Lookup Contact record based on the email given.
	 */
	function LookupContact($email) {
		global $adb;
		if($this->_cachedContactIds[$email]) {
			$this->log("Reusing Cached Contact Id for email: $email");
			return $this->_cachedContactIds[$email];
		}
		$contactid = false;
		$contactres = $adb->pquery("SELECT contactid FROM vtiger_contactdetails inner join vtiger_crmentity on crmid=contactid WHERE deleted=0 and email=?", Array($email));
		if($adb->num_rows($contactres)) {
			$contactid = $adb->query_result($contactres, 0, 'contactid');
			$crmres = $adb->pquery("SELECT deleted FROM vtiger_crmentity WHERE crmid=?", Array($contactid));
			if($adb->num_rows($crmres) && $adb->query_result($crmres, 0, 'deleted')) $contactid = false;
		}
		if($contactid) {
			$this->log("Caching Contact Id found for email: $email");
			$this->_cachedContactIds[$email] = $contactid;
		} else {
			$this->log("No matching Contact found for email: $email");
		}			
		return $contactid;
	}
	/**
	 * Lookup Account record based on the email given.
	 */
	function LookupAccount($email) {
		global $adb;
		if($this->_cachedAccountIds[$email]) {
			$this->log("Reusing Cached Account Id for email: $email");
			return $this->_cachedAccountIds[$email];
		}

		$accountid = false;
		$accountres = $adb->pquery("SELECT accountid FROM vtiger_account inner join vtiger_crmentity on crmid=accountid WHERE deleted=0 and (email1=? OR email2=?)", Array($email, $email));
		if($adb->num_rows($accountres)) {
			$accountid = $adb->query_result($accountres, 0, 'accountid');
			$crmres = $adb->pquery("SELECT deleted FROM vtiger_crmentity WHERE crmid=?", Array($accountid));
			if($adb->num_rows($crmres) && $adb->query_result($crmres, 0, 'deleted')) $accountid = false;
		}
		if($accountid) {
			$this->log("Caching Account Id found for email: $email");
			$this->_cachedAccountIds[$email] = $accountid;
		} else {
			$this->log("No matching Account found for email: $email");
		}			
		return $accountid;
	}
	/**
	 * Lookup Ticket record based on the subject or id given.
	 */
	function LookupTicket($subjectOrId) {
		global $adb;

		$checkTicketId = $this->__toInteger($subjectOrId);
		if(!$checkTicketId) {
			$ticketres = $adb->pquery("SELECT ticketid FROM vtiger_troubletickets WHERE title = ?", Array($subjectOrId));
			if($adb->num_rows($ticketres)) $checkTicketId = $adb->query_result($ticketres, 0, 'ticketid');
		}
		if(!$checkTicketId) return false;

		if($this->_cachedTicketIds[$checkTicketId]) {
			$this->log("Reusing Cached Ticket Id for: $subjectOrId");
			return $this->_cachedTicketIds[$checkTicketId];
		}
		
		$ticketid = false;
		if($checkTicketId) {
			$crmres = $adb->pquery("SELECT setype, deleted FROM vtiger_crmentity WHERE crmid=?", Array($checkTicketId));
			if($adb->num_rows($crmres)) {
				if($adb->query_result($crmres, 0, 'setype') == 'HelpDesk' &&
					$adb->query_result($crmres, 0, 'deleted') == '0') $ticketid = $checkTicketId;
			}
		}
		if($ticketid) {
			$this->log("Caching Ticket Id found for: $subjectOrId");
			$this->_cachedTicketIds[$checkTicketId] = $ticketid;
		} else {
			$this->log("No matching Ticket found for: $subjectOrId");
		}
		return $ticketid;
	}
		
	/**
	 * Get Account record information based on email.
	 */
	function GetAccountRecord($email) {
		require_once('modules/Accounts/Accounts.php');
		$accountid = $this->LookupAccount($email);
		$account_focus = false;
		if($accountid) {			
			if($this->_cachedAccounts[$accountid]) {
				$account_focus = $this->_cachedAccounts[$accountid];
				$this->log("Reusing Cached Account [" . $account_focus->column_fields[accountname] . "]");
			} else {
				$account_focus = new Accounts();
				$account_focus->retrieve_entity_info($accountid, 'Accounts');
				$account_focus->id = $accountid;

				$this->log("Caching Account [" . $account_focus->column_fields[accountname] . "]");
				$this->_cachedAccounts[$accountid] = $account_focus;
			}
		}
		return $account_focus;
	}
	/**
	 * Get Contact record information based on email.
	 */
	function GetContactRecord($email) {
		require_once('modules/Contacts/Contacts.php');
		$contactid = $this->LookupContact($email);
		$contact_focus = false;
		if($contactid) {			
			if($this->_cachedContacts[$contactid]) {
				$contact_focus = $this->_cachedContacts[$contactid];
				$this->log("Reusing Cached Contact [" . $contact_focus->column_fields[lastname] .
				   	'-' . $contact_focus->column_fields[firstname] . "]");
			} else {
				$contact_focus = new Contacts();
				$contact_focus->retrieve_entity_info($contactid, 'Contacts');
				$contact_focus->id = $contactid;

				$this->log("Caching Contact [" . $contact_focus->column_fields[lastname] .
				   	'-' . $contact_focus->column_fields[firstname] . "]");
				$this->_cachedContacts[$contactid] = $contact_focus;
			}
		}
		return $contact_focus;
	}

	/**
	 * Lookup Contact or Account based on from email and with respect to given CRMID
	 */
	function LookupContactOrAccount($fromemail, $checkWithId=false) {
		$recordid = $this->LookupContact($fromemail);
		if($checkWithId && $recordid != $checkWithId) {
			$recordid = $this->LookupAccount($fromemail);
			if($checkWithId && $recordid != $checkWithId) $recordid = false;
		}
		return $recordid;
	}

	/**
	 * Get Ticket record information based on subject or id.
	 */
	function GetTicketRecord($subjectOrId, $fromemail=false) {
		require_once('modules/HelpDesk/HelpDesk.php');
		$ticketid = $this->LookupTicket($subjectOrId);
		$ticket_focus = false;
		if($ticketid) {
			if($this->_cachedTickets[$ticketid]) {
				$ticket_focus = $this->_cachedTickets[$ticketid];
				// Check the parentid association if specified.
				if($fromemail && !$this->LookupContactOrAccount($fromemail, $ticket_focus->column_fields[parent_id])) {
					$ticket_focus = false;
				}
				if($ticket_focus) {
					$this->log("Reusing Cached Ticket [" . $ticket_focus->column_fields[ticket_title] ."]");
				}
			} else {
				$ticket_focus = new HelpDesk();
				$ticket_focus->retrieve_entity_info($ticketid, 'HelpDesk');
				$ticket_focus->id = $ticketid;
				// Check the parentid association if specified.
				if($fromemail && !$this->LookupContactOrAccount($fromemail, $ticket_focus->column_fields[parent_id])) {
					$ticket_focus = false;
				}
				if($ticket_focus) {
					$this->log("Caching Ticket [" . $ticket_focus->column_fields[ticket_title] . "]");
					$this->_cachedTickets[$ticketid] = $ticket_focus;
				}
			}
		}
		return $ticket_focus;
	}
}

?>
