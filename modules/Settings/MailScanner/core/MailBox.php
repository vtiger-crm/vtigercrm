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

require_once('modules/Settings/MailScanner/core/MailScannerInfo.php');
require_once('modules/Settings/MailScanner/core/MailRecord.php');

/**
 * Class to work with server mailbox. 
 */
class Vtiger_MailBox {
	// Mailbox credential information
	var $_scannerinfo = false;
	// IMAP connection instance
	var $_imap = false;
	// IMAP url to use for connecting
	var $_imapurl = false;
	// IMAP folder currently opened
	var $_imapfolder = false;
	// Should we need to expunge while closing imap connection?
	var $_needExpunge = false;

	// Mailbox crendential information (as a map)
	var $_mailboxsettings = false;

	/** DEBUG functionality. */
	var $debug = false;
	function log($message, $force=false) {
		global $log;
		if($log && ($force || $this->debug)) { $log->debug($message); }
		else if( ($force || $this->debug) ) echo "$message\n";
	}

	/**
	 * Constructor
	 */
	function __construct($scannerinfo) {
		$this->_scannerinfo = $scannerinfo;
		$this->_mailboxsettings = $scannerinfo->getAsMap();

		if($this->_mailboxsettings[ssltype] == '')  $this->_mailboxsettings[ssltype] = 'notls';
		if($this->_mailboxsettings[sslmethod]== '') $this->_mailboxsettings[sslmethod] = 'novalidate-cert';

		if($this->_mailboxsettings[protocol] == 'pop3') { $port = '110'; }
		else {
			if($this->_mailboxsettings[ssltype] == 'tls' || 
				$this->_mailboxsettings[ssltype] == 'ssl') {
					$port = '993';
			}
			else $port = '143';
		}
		$this->_mailboxsettings[port] = $port;
	}

	/**
	 * Connect to mail box folder.
	 */
	function connect($folder='INBOX') {
		$imap = false;
		$mailboxsettings = $this->_mailboxsettings;	

		$isconnected = false;

		// Connect using last successful url
		if($mailboxsettings['connecturl']) {
			$connecturl = $mailboxsettings['connecturl'];
			$this->log("Trying to connect using connecturl $connecturl$folder", true);
			$imap = @imap_open("$connecturl$folder", $mailboxsettings[username], $mailboxsettings[password]);
			if($imap) {
				$this->_imapurl = $connecturl;
				$this->_imapfolder = $folder;
				$isconnected = true;

				$this->log("Successfully connected", true);
			}
		} 
		
		if(!$imap) {
			$connectString = '{'. "$mailboxsettings[server]:$mailboxsettings[port]/$mailboxsettings[protocol]/$mailboxsettings[ssltype]/$mailboxsettings[sslmethod]" ."}";
			$connectStringShort = '{'. "$mailboxsettings[server]/$mailboxsettings[protocol]:$mailboxsettings[port]" ."}";

			$this->log("Trying to connect using $connectString$folder", true);
			if(!$imap = @imap_open("$connectString$folder", $mailboxsettings[username], $mailboxsettings[password])) {
				$this->log("Connect failed using $connectString$folder, trying with $connectStringShort$folder...", true);
				$imap = @imap_open("$connectStringShort$folder", $mailboxsettings[username], $mailboxsettings[password]);
				if($imap) {
					$this->_imapurl = $connectStringShort;
					$this->_imapfolder = $folder;
					$isconnected = true;
					$this->log("Successfully connected", true);
				} else {
					$this->log("Connect failed using $connectStringShort$folder", true);
				}
			} else {
				$this->_imapurl = $connectString;
				$this->_imapfolder = $folder;
				$isconnected = true;
				$this->log("Successfully connected", true);
			}
		}

		$this->_imap = $imap;
		return $isconnected;
	}

	/**
	 * Open the mailbox folder.
	 * @param $folder Folder name to open
	 * @param $reopen set to true for re-opening folder if open (default=false)
	 * @return true if connected, false otherwise
	 */
	function open($folder, $reopen=false) {
		/** Avoid re-opening of the box if not requested. */
		if(!$reopen && ($folder == $this->_imapfolder)) return true; 

		if(!$this->_imap) return $this->connect($folder);

		$mailboxsettings = $this->_mailboxsettings;

		$isconnected = false;
		$connectString = $this->_imapurl;
		$this->log("Trying to open folder using $connectString$folder");
		$imap = @imap_open("$connectString$folder", $mailboxsettings[username], $mailboxsettings[password]);
		if($imap) {

			// Perform cleanup task before re-initializing the connection
			$this->close(); 

			$this->_imapfolder = $folder;
			$this->_imap = $imap;
			$isconnected = true;
		}
		return $isconnected;
	}

	/**
	 * Get the mails based on searchquery.
	 * @param $folder Folder in which mails to be read.
	 * @param $searchQuery IMAP query, (default false: fetches mails newer from lastscan)
	 * @return imap_search records or false
	 */
	function search($folder, $searchQuery=false) {
		if(!$searchQuery) {
			$lastscanOn = $this->_scannerinfo->getLastscan($folder);
			$searchfor = $this->_scannerinfo->searchfor;

			if($searchfor && $lastscanOn) {				
				if($searchfor == 'ALL') {
					$searchQuery = "SINCE $lastscanOn";
				} else {
					$searchQuery = "$searchfor SINCE $lastscanOn";
				}
			} else {
				$searchQuery = $lastscanOn? "SINCE $lastscanOn" : "BEFORE ". date('d-M-Y');
			}
		}
		if($this->open($folder)) {
			$this->log("Searching mailbox[$folder] using query: $searchQuery");
			return imap_search($this->_imap, $searchQuery);
		}
		return false;
	}

	/**
	 * Get folder names (as list) for the given mailbox connection
	 */
	function getFolders() {
		$folders = false;
		if($this->_imap) { 
			$imapfolders = imap_list($this->_imap, $this->_imapurl, '*'); 
			if($imapfolders) {
				foreach($imapfolders as $imapfolder) {
					$folders[] = substr($imapfolder, strlen($this->_imapurl));
				}
			}
		}
		return $folders;
	}

	/**
	 * Fetch the email based on the messageid.
	 * @param $messageid messageid of the email
	 * @param $fetchbody set to false to defer fetching the body, (default: true)
	 */
	function getMessage($messageid, $fetchbody=true) {
		return new Vtiger_MailRecord($this->_imap, $messageid, $fetchbody);
	}

	/**
	 * Mark the message in the mailbox.
	 */
	function markMessage($messageid) {
		$markas = $this->_scannerinfo->markas;
		if($this->_imap && $markas) {
			if(strtoupper($markas) == 'SEEN') $markas = "\\Seen";
			imap_setflag_full($this->_imap, $messageid, $markas);
		}
	}
	
	/**
	 * Close the open IMAP connection.
	 */
	function close() {
		if($this->_needExpunge) {
			imap_expunge($this->_imap);
		}
		$this->_needExpunge = false;
		if($this->_imap) { 
			imap_close($this->_imap); 
			$this->_imap = false; 
		}
	}
}

?>
