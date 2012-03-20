<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('vtlib/Vtiger/Utils.php');
include_once('vtlib/Vtiger/Utils/StringTemplate.php');

/**
 * Provides API to handle custom links
 * @package vtlib
 */
class Vtiger_Link {
	var $tabid;
	var $linkid;
	var $linktype;
	var $linklabel;
	var $linkurl;
	var $linkicon;
	var $sequence;
	var $status = false;

	// Ignore module while selection
	const IGNORE_MODULE = -1; 

	/**
	 * Constructor
	 */
	function __construct() {
	}

	/**
	 * Initialize this instance.
	 */
	function initialize($valuemap) {
		$this->tabid  = $valuemap['tabid'];
		$this->linkid = $valuemap['linkid'];
		$this->linktype=$valuemap['linktype'];
		$this->linklabel=$valuemap['linklabel'];
		$this->linkurl  =decode_html($valuemap['linkurl']);
		$this->linkicon =decode_html($valuemap['linkicon']);
		$this->sequence =$valuemap['sequence'];
		$this->status   =$valuemap['status'];
	}

	/**
	 * Get module name.
	 */
	function module() {
		if(!empty($this->tabid)) {
			return getTabModuleName($this->tabid);
		}
		return false;
	}

	/**
	 * Get unique id for the insertion
	 */
	static function __getUniqueId() {
		global $adb;
		return $adb->getUniqueID('vtiger_links');
	}

	/** Cache (Record) the schema changes to improve performance */
	static $__cacheSchemaChanges = Array();

	/**
	 * Initialize the schema (tables)
	 */
	static function __initSchema() {
		if(empty(self::$__cacheSchemaChanges['vtiger_links'])) {
			if(!Vtiger_Utils::CheckTable('vtiger_links')) {
				Vtiger_Utils::CreateTable(
					'vtiger_links',
					'(linkid INT NOT NULL PRIMARY KEY,
					tabid INT, linktype VARCHAR(20), linklabel VARCHAR(30), linkurl VARCHAR(255), linkicon VARCHAR(100), sequence INT, status INT(1) NOT NULL DEFAULT 1)',
					true);
				Vtiger_Utils::ExecuteQuery(
					'CREATE INDEX link_tabidtype_idx on vtiger_links(tabid,linktype)');
			}
			self::$__cacheSchemaChanges['vtiger_links'] = true;
		}
	}

	/**
	 * Add link given module
	 * @param Integer Module ID
	 * @param String Link Type (like DETAILVIEW). Useful for grouping based on pages.
	 * @param String Label to display
	 * @param String HREF value or URL to use for the link
	 * @param String ICON to use on the display
	 * @param Integer Order or sequence of displaying the link
	 */
	static function addLink($tabid, $type, $label, $url, $iconpath='',$sequence=0) {
		global $adb;
		self::__initSchema();
		$checkres = $adb->pquery('SELECT linkid FROM vtiger_links WHERE tabid=? AND linktype=? AND linkurl=? AND linkicon=? AND linklabel=?',
			Array($tabid, $type, $url, $iconpath, $label));
		if(!$adb->num_rows($checkres)) {
			$uniqueid = self::__getUniqueId();
			$adb->pquery('INSERT INTO vtiger_links (linkid,tabid,linktype,linklabel,linkurl,linkicon,sequence) VALUES(?,?,?,?,?,?,?)',
				Array($uniqueid, $tabid, $type, $label, $url, $iconpath, $sequence));
			self::log("Adding Link ($type - $label) ... DONE");
		}
	}

	/**
	 * Delete link of the module
	 * @param Integer Module ID
	 * @param String Link Type (like DETAILVIEW). Useful for grouping based on pages.
	 * @param String Display label
	 * @param String URL of link to lookup while deleting
	 */ 
	static function deleteLink($tabid, $type, $label, $url=false) {
		global $adb;
		self::__initSchema();
		if($url) {
			$adb->pquery('DELETE FROM vtiger_links WHERE tabid=? AND linktype=? AND linklabel=? AND linkurl=?',
				Array($tabid, $type, $label, $url));
			self::log("Deleting Link ($type - $label - $url) ... DONE");
		} else {
			$adb->pquery('DELETE FROM vtiger_links WHERE tabid=? AND linktype=? AND linklabel=?',
				Array($tabid, $type, $label));
			self::log("Deleting Link ($type - $label) ... DONE");
		}
	}

	/**
	 * Delete all links related to module
	 * @param Integer Module ID.
	 */
	static function deleteAll($tabid) {
		global $adb;
		self::__initSchema();
		$adb->pquery('DELETE FROM vtiger_links WHERE tabid=?', Array($tabid));
		self::log("Deleting Links ... DONE");
	}

	/**
	 * Get all the links related to module
	 * @param Integer Module ID.
	 */
	static function getAll($tabid) {
		return self::getAllByType($tabid);
	}

	/**
	 * Get all the link related to module based on type
	 * @param Integer Module ID
	 * @param mixed String or List of types to select 
	 * @param Map Key-Value pair to use for formating the link url
	 */
	static function getAllByType($tabid, $type=false, $parameters=false) {
		global $adb, $current_user;
		self::__initSchema();

		$multitype = false;

		if($type) {
			// Multiple link type selection?
			if(is_array($type)) { 
				$multitype = true;
				if($tabid === self::IGNORE_MODULE) {
					$sql = 'SELECT * FROM vtiger_links WHERE linktype IN ('.
						Vtiger_Utils::implodestr('?', count($type), ',') .') ';
					$params = $type;
					$permittedTabIdList = getPermittedModuleIdList();
					if(count($permittedTabIdList) > 0 && $current_user->is_admin !== 'on') {
						$sql .= ' and tabid IN ('.
							Vtiger_Utils::implodestr('?', count($permittedTabIdList), ',').')';
						$params[] = $permittedTabIdList;
					}
					$result = $adb->pquery($sql, Array($adb->flatten_array($params)));
				} else {
					$result = $adb->pquery('SELECT * FROM vtiger_links WHERE tabid=? AND linktype IN ('.
						Vtiger_Utils::implodestr('?', count($type), ',') .')',
							Array($tabid, $adb->flatten_array($type)));
				}			
			} else {
				// Single link type selection
				if($tabid === self::IGNORE_MODULE) {
					$result = $adb->pquery('SELECT * FROM vtiger_links WHERE linktype=?', Array($type));
				} else {
					$result = $adb->pquery('SELECT * FROM vtiger_links WHERE tabid=? AND linktype=?', Array($tabid, $type));				
				}
			}
		} else {
			$result = $adb->pquery('SELECT * FROM vtiger_links WHERE tabid=?', Array($tabid));
		}

		$strtemplate = new Vtiger_StringTemplate();
		if($parameters) {
			foreach($parameters as $key=>$value) $strtemplate->assign($key, $value);
		}

		$instances = Array();
		if($multitype) {
			foreach($type as $t) $instances[$t] = Array();
		}

		while($row = $adb->fetch_array($result)){
			$instance = new self();
			$instance->initialize($row);
			if($parameters) {
				$instance->linkurl = $strtemplate->merge($instance->linkurl);
				$instance->linkicon= $strtemplate->merge($instance->linkicon);
			}
			if($multitype) {
				$instances[$instance->linktype][] = $instance;
			} else {
				$instances[] = $instance;
			}
		}
		return $instances;
	}

	/**
	 * Helper function to log messages
	 * @param String Message to log
	 * @param Boolean true appends linebreak, false to avoid it
	 * @access private
	 */
	static function log($message, $delimit=true) {
		Vtiger_Utils::Log($message, $delimit);
	}
}
?>
