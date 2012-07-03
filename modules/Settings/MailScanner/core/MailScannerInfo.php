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
require_once('modules/Settings/MailScanner/core/MailScannerRule.php');

/**
 * Mail Scanner information manager.
 */
class Vtiger_MailScannerInfo {
	// id of this scanner record
	var $scannerid = false;
	// name of this scanner
	var $scannername=false;
	// mail server to connect to
	var $server    = false;
	// mail protocol to use
	var $protocol  = false;
	// username to use
	var $username  = false;
	// password to use
	var $password  = false;
	// notls/tls/ssl
	var $ssltype   = false;
	// validate-certificate or novalidate-certificate
	var $sslmethod = false;
	// last successful connection url to use
	var $connecturl= false;
	// search for type
	var $searchfor = false;
	// post scan mark record as
	var $markas = false;

	// is the scannered enabled?
	var $isvalid   = false;

	// Last scan on the folders.
	var $lastscan  = false;

	// Need rescan on the folders?
	var $rescan    = false;

	// Rules associated with this mail scanner
	var $rules = false;

	/**
	 * Constructor
	 */
	function __construct($scannername, $initialize=true) {
		if($initialize && $scannername) $this->initialize($scannername);		
	}

	/**
	 * Encrypt/Decrypt input.
	 * @access private
	 */
	function __crypt($password, $encrypt=true) {
		require_once('include/utils/encryption.php');
		$cryptobj = new Encryption();
		if($encrypt) return $cryptobj->encrypt(trim($password));
		else return $cryptobj->decrypt(trim($password));
	}

	/**
	 * Initialize this instance.
	 */
	function initialize($scannername) {
		global $adb;
		$result = $adb->pquery("SELECT * FROM vtiger_mailscanner WHERE scannername=?", Array($scannername));

		if($adb->num_rows($result)) {
			$this->scannerid  = $adb->query_result($result, 0, 'scannerid');
			$this->scannername= $adb->query_result($result, 0, 'scannername');
			$this->server     = $adb->query_result($result, 0, 'server');
			$this->protocol   = $adb->query_result($result, 0, 'protocol');
			$this->username   = $adb->query_result($result, 0, 'username');
			$this->password   = $adb->query_result($result, 0, 'password');
			$this->password   = $this->__crypt($this->password, false);
			$this->ssltype    = $adb->query_result($result, 0, 'ssltype');
			$this->sslmethod  = $adb->query_result($result, 0, 'sslmethod');
			$this->connecturl = $adb->query_result($result, 0, 'connecturl');
			$this->searchfor  = $adb->query_result($result, 0, 'searchfor');
			$this->markas     = $adb->query_result($result, 0, 'markas');
			$this->isvalid    = $adb->query_result($result, 0, 'isvalid');

			$this->initializeFolderInfo();
			$this->initializeRules();
		}
	}

	/**
	 * Initialize the folder details
	 */
	function initializeFolderInfo() {
		global $adb;
		if($this->scannerid) {
			$this->lastscan = Array();
			$this->rescan   = Array();
			$lastscanres = $adb->pquery("SELECT * FROM vtiger_mailscanner_folders WHERE scannerid=?",Array($this->scannerid));
			$lastscancount = $adb->num_rows($lastscanres);
			if($lastscancount) {
				for($lsindex = 0; $lsindex < $lastscancount; ++$lsindex) {
					$folder = $adb->query_result($lastscanres, $lsindex, 'foldername');
					$scannedon =$adb->query_result($lastscanres, $lsindex, 'lastscan');
					$nextrescan =$adb->query_result($lastscanres, $lsindex, 'rescan');
					$this->lastscan[$folder] = $scannedon;
					$this->rescan[$folder]   = ($nextrescan == 0)? false : true;
				}
			}
		}
	}

	/**
	 * Delete lastscan details with this scanner
	 */
	function clearLastscan() {
		global $adb;
		$adb->pquery("DELETE FROM vtiger_mailscanner_folders WHERE scannerid=?", Array($this->scannerid));
		$this->lastscan = false;
	}

	/**
	 * Update rescan flag on all folders
	 */
	function updateAllFolderRescan($rescanFlag=false) {
		global $adb;
		$useRescanFlag = $rescanFlag? 1 : 0;
		$adb->pquery("UPDATE vtiger_mailscanner_folders set rescan=? WHERE scannerid=?", 
			Array($rescanFlag, $this->scannerid));
		if($this->rescan) {
			foreach($this->rescan as $folderName=>$oldRescanFlag) {
				$this->rescan[$folderName] = $rescanFlag;
			}
		}
	}

	/**
	 * Update lastscan information on folder (or set for rescan next)
	 */
	function updateLastscan($folderName, $rescanFolder=false) {
		global $adb;

		$scannedOn = date('d-M-Y');

		$needRescan = $rescanFolder? 1 : 0;

		$folderInfo = $adb->pquery("SELECT folderid FROM vtiger_mailscanner_folders WHERE scannerid=? AND foldername=?",
			Array($this->scannerid, $folderName));
		if($adb->num_rows($folderInfo)) {
			$folderid = $adb->query_result($folderInfo, 0, 'folderid');
			$adb->pquery("UPDATE vtiger_mailscanner_folders SET lastscan=?, rescan=? WHERE folderid=?", 
				Array($scannedOn, $needRescan, $folderid));
		} else {
			$enabledForScan = 1; // Enable folder for scan by default
			$adb->pquery("INSERT INTO vtiger_mailscanner_folders(scannerid, foldername, lastscan, rescan, enabled)
			   VALUES(?,?,?,?,?)", Array($this->scannerid, $folderName, $scannedOn, $needRescan, $enabledForScan));
		}
		if(!$this->lastscan) $this->lastscan = Array();
		$this->lastscan[$folderName] = $scannedOn;

		if(!$this->rescan) $this->rescan = Array();
		$this->rescan[$folderName] = $needRescan;
	}

	/**
	 * Get lastscan of the folder.
	 */
	function getLastscan($folderName) {
		if($this->lastscan) return $this->lastscan[$folderName];
		else return false;
	}

	/**
	 * Does the folder need message rescan?
	 */
	function needRescan($folderName) {
		if($this->rescan && isset($this->rescan[$folderName])) {
			return $this->rescan[$folderName];
		}
		// TODO Pick details of rescan flag of folder from database?
		return false;
	}

	/**
	 * Check if rescan is required atleast on a folder?
	 */
	function checkRescan() {
		$rescanRequired = false;
		if($this->rescan) {
			foreach($this->rescan as $folderName=>$rescan) {
				if($rescan) { 
					$rescanRequired = $folderName;
					break;
				}
			}
		}
		return $rescanRequired;
	}

	/**
	 * Get the folder information that has been scanned
	 */
	function getFolderInfo() {
		$folderinfo = false;
		if($this->scannerid) {
			global $adb;
			$fldres = $adb->pquery("SELECT * FROM vtiger_mailscanner_folders WHERE scannerid=?", Array($this->scannerid));
			$fldcount = $adb->num_rows($fldres);
			if($fldcount) {
				$folderinfo = Array();
				for($index = 0; $index < $fldcount; ++$index) {
					$foldername = $adb->query_result($fldres, $index, 'foldername');
					$folderid   = $adb->query_result($fldres, $index, 'folderid');
					$lastscan   = $adb->query_result($fldres, $index, 'lastscan');
					$rescan     = $adb->query_result($fldres, $index, 'rescan');
					$enabled    = $adb->query_result($fldres, $index, 'enabled');
					$folderinfo[$foldername] = Array ('folderid'=>$folderid, 'lastscan'=>$lastscan, 'rescan'=> $rescan, 'enabled'=>$enabled);
				}
			}
		}
		return $folderinfo;
	}

	/**
	 * Update the folder information with given folder names
	 */
	function updateFolderInfo($foldernames, $rescanFolder=false) {
		if($this->scannerid && !empty($foldernames)) {
			global $adb;
			$qmarks = Array();
			foreach($foldernames as $foldername) {
				$qmarks[] = '?';
				$this->updateLastscan($foldername, $rescanFolder);
			}
			// Delete the folder that is no longer present
			$adb->pquery("DELETE FROM vtiger_mailscanner_folders WHERE scannerid=? AND foldername NOT IN
				(". implode(',', $qmarks) . ")", Array($this->scannerid, $foldernames));
		}
	}

	/**
	 * Enable only given folders for scanning
	 */
	function enableFoldersForScan($folderinfo) {
		if($this->scannerid) {
			global $adb;
			$adb->pquery("UPDATE vtiger_mailscanner_folders set enabled=0 WHERE scannerid=?", Array($this->scannerid));
			foreach($folderinfo as $foldername=>$foldervalue) {
				$folderid = $foldervalue[folderid];
				$enabled  = $foldervalue[enabled];
				$adb->pquery("UPDATE vtiger_mailscanner_folders set enabled=? WHERE folderid=? AND scannerid=?",
					Array($enabled,$folderid,$this->scannerid));
			}
		}
	}

	/**
	 * Initialize scanner rule information
	 */
	function initializeRules() {
		global $adb;
		if($this->scannerid) {
			$this->rules = Array();
			$rulesres = $adb->pquery("SELECT * FROM vtiger_mailscanner_rules WHERE scannerid=? ORDER BY sequence",Array($this->scannerid));
			$rulescount = $adb->num_rows($rulesres);
			if($rulescount) {
				for($index = 0; $index < $rulescount; ++$index) {
					$ruleid = $adb->query_result($rulesres, $index, 'ruleid');
					$scannerrule = new Vtiger_MailScannerRule($ruleid);
					$scannerrule->debug = $this->debug;
					$this->rules[] = $scannerrule;
				}
			}
		}
	}	

	/**
	 * Get scanner information as map
	 */
	function getAsMap() {
		$infomap = Array();
		$keys = Array('scannerid', 'scannername', 'server', 'protocol', 'username', 'password', 'ssltype', 
			'sslmethod', 'connecturl', 'searchfor', 'markas', 'isvalid', 'rules');
		foreach($keys as $key) {
			$infomap[$key] = $this->$key; 
		}
		$infomap['requireRescan'] = $this->checkRescan();
		return $infomap;
	}

	/**
	 * Compare this instance with give instance
	 */
	function compare($otherInstance) {
		$checkkeys = Array('server', 'scannername', 'protocol', 'username', 'password', 'ssltype', 'sslmethod', 'searchfor', 'markas');
		foreach($checkkeys as $key) { 
			if($this->$key != $otherInstance->$key) return false;
		}
		return true;
	}

	/**
	 * Create/Update the scanner information in database
	 */
	function update($otherInstance) {
		$mailServerChanged = false;

		// Is there is change in server setup?
		if($this->server != $otherInstance->server || $this->username != $otherInstance->username) {
			$mailServerChanged = true;
			$this->clearLastscan();
			// TODO How to handle lastscan info if server settings switches back in future?
		}

		$this->server    = $otherInstance->server;
		$this->scannername= $otherInstance->scannername;
		$this->protocol  = $otherInstance->protocol;
		$this->username  = $otherInstance->username;
		$this->password  = $otherInstance->password;
		$this->ssltype   = $otherInstance->ssltype;
		$this->sslmethod = $otherInstance->sslmethod;
		$this->connecturl= $otherInstance->connecturl;
		$this->searchfor = $otherInstance->searchfor;
		$this->markas    = $otherInstance->markas;
		$this->isvalid   = $otherInstance->isvalid;

		$useisvalid = ($this->isvalid)? 1 : 0;

		$usepassword = $this->__crypt($this->password);
        
		global $adb;
		if($this->scannerid == false) {
            $adb->pquery("INSERT INTO vtiger_mailscanner(scannername,server,protocol,username,password,ssltype,
				sslmethod,connecturl,searchfor,markas,isvalid) VALUES(?,?,?,?,?,?,?,?,?,?,?)",
				Array($this->scannername,$this->server, $this->protocol, $this->username, $usepassword,
				$this->ssltype, $this->sslmethod, $this->connecturl, $this->searchfor, $this->markas, $useisvalid));
			$this->scannerid = $adb->database->Insert_ID();
        } else { //this record is exist in the data
			$adb->pquery("UPDATE vtiger_mailscanner SET scannername=?,server=?,protocol=?,username=?,password=?,ssltype=?,
				sslmethod=?,connecturl=?,searchfor=?,markas=?,isvalid=? WHERE scannerid=?",
				Array($this->scannername,$this->server,$this->protocol, $this->username, $usepassword, $this->ssltype,
				$this->sslmethod, $this->connecturl,$this->searchfor, $this->markas,$useisvalid, $this->scannerid));
        }
		
		return $mailServerChanged;
	}

	/**
	 * Delete the scanner information from database
	 */
	function delete() {
		global $adb;
		
		// Delete dependencies
		if(!empty($this->rules)) {
			foreach($this->rules as $rule) {
				$rule->delete();
			}
		}
		
		if($this->scannerid) {
			$tables = Array(
				'vtiger_mailscanner',
				'vtiger_mailscanner_ids', 
				'vtiger_mailscanner_folders'
			);
			foreach($tables as $table) {
				$adb->pquery("DELETE FROM $table WHERE scannerid=?", Array($this->scannerid));
			}
			$adb->pquery("DELETE FROM vtiger_mailscanner_ruleactions
				WHERE actionid in (SELECT actionid FROM vtiger_mailscanner_actions WHERE scannerid=?)", Array($this->scannerid));
			$adb->pquery("DELETE FROM vtiger_mailscanner_actions WHERE scannerid=?", Array($this->scannerid));
		}
	}
	
	/**
	 * List all the mail-scanners configured.
	 */
	static function listAll() {
		$scanners = array();
		
		global $adb;
		$result = $adb->pquery("SELECT scannername FROM vtiger_mailscanner", array());
		if($result && $adb->num_rows($result)) {
			while($resultrow = $adb->fetch_array($result)) {
				$scanners[] = new self( decode_html($resultrow['scannername'] ));
			}
		}
		return $scanners;
	}
}
?>
