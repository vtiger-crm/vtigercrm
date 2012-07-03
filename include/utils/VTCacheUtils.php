<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * Class to handle Caching Mechanism and re-use information.
 */
class VTCacheUtils {
	
	/** Tab information caching */
	static $_tabidinfo_cache = array();
	static function lookupTabid($module) {
		$flip_cache = array_flip(self::$_tabidinfo_cache);
		
		if(isset($flip_cache[$module])) {
			return $flip_cache[$module];
		}
		return false;
	}
	
	static function lookupModulename($tabid) {
		if(isset(self::$_tabidinfo_cache[$tabid])) {
			return self::$_tabidinfo_cache[$tabid];
		}
		return false;
	}
	
	static function updateTabidInfo($tabid, $module) {
		if(!empty($tabid) && !empty($module)) {
			self::$_tabidinfo_cache[$tabid] = $module;
		}
	}
	
	/** All tab information caching */
	static $_alltabrows_cache = false;
	static function lookupAllTabsInfo() {
		return self::$_alltabrows_cache; 
	}
	static function updateAllTabsInfo($tabrows) {
		self::$_alltabrows_cache = $tabrows;
	}
	
	/** Field information caching */
	static $_fieldinfo_cache = array();
	static function updateFieldInfo($tabid, $fieldname, $fieldid, $fieldlabel, 
		$columnname, $tablename, $uitype, $typeofdata, $presence) {
			
		self::$_fieldinfo_cache[$tabid][$fieldname] = array(
			'tabid'     => $tabid,
			'fieldid'   => $fieldid,
			'fieldname' => $fieldname,
			'fieldlabel'=> $fieldlabel,
			'columnname'=> $columnname,
			'tablename' => $tablename,
			'uitype'    => $uitype,
			'typeofdata'=> $typeofdata,
			'presence'  => $presence,
		);
	}
	static function lookupFieldInfo($tabid, $fieldname) {
		if(isset(self::$_fieldinfo_cache[$tabid]) && isset(self::$_fieldinfo_cache[$tabid][$fieldname])) {
			return self::$_fieldinfo_cache[$tabid][$fieldname];
		}
		return false;
	}	
	static function lookupFieldInfo_Module($module, $presencein = array('0', '2')) {
		$tabid = getTabid($module);
		$modulefields = false;
		
		if(isset(self::$_fieldinfo_cache[$tabid])) {
			$modulefields = array();
			
			$fldcache = self::$_fieldinfo_cache[$tabid];
			foreach($fldcache as $fieldname=>$fieldinfo) {
				if(in_array($fieldinfo['presence'], $presencein)) {
					$modulefields[] = $fieldinfo;
				}
			}
		}
		return $modulefields;
	}
	
	static function lookupFieldInfoByColumn($tabid, $columnname) {
		if(isset(self::$_fieldinfo_cache[$tabid])) {
			foreach(self::$_fieldinfo_cache[$tabid] as $fieldname=>$fieldinfo) {
				if($fieldinfo['columnname'] == $columnname) {
					return $fieldinfo;
				}
			}
		}
		return false;
	}
	
	/** Module active column fields caching */
	static $_module_columnfields_cache = array();
	static function updateModuleColumnFields($module, $column_fields) {
		self::$_module_columnfields_cache[$module] = $column_fields;
	}
	static function lookupModuleColumnFields($module) {
		if(isset(self::$_module_columnfields_cache[$module])) {
			return self::$_module_columnfields_cache[$module];
		}
		return false;
	}
	
	/** User currency id caching */
	static $_usercurrencyid_cache = array();
	static function lookupUserCurrenyId($userid) {
		global $current_user;		
		if(isset($current_user) && $current_user->id == $userid) {
			return array(
				'currencyid' => $current_user->column_fields['currency_id']
			);
		}
		
		if(isset(self::$_usercurrencyid_cache[$userid])) {
			return self::$_usercurrencyid_cache[$userid];
		}
		
		return false;
	}
	static function updateUserCurrencyId($userid, $currencyid) {
		self::$_usercurrencyid_cache[$userid] = array(
			'currencyid' => $currencyid
		);
	}
	
	/** Currency information caching */
	static $_currencyinfo_cache = array();
	static function lookupCurrencyInfo($currencyid) {
		if(isset(self::$_currencyinfo_cache[$currencyid])) {
			return self::$_currencyinfo_cache[$currencyid];
		}
		return false;
	}
	static function updateCurrencyInfo($currencyid, $name, $code, $symbol, $rate) {
		self::$_currencyinfo_cache[$currencyid] = array(
			'currencyid' => $currencyid,
			'name'       => $name,
			'code'       => $code,
			'symbol'     => $symbol,
			'rate'       => $rate
		);
	}
	
	
	/** ProfileId information caching */
	static $_userprofileid_cache = array();
	static function updateUserProfileId($userid, $profileid) {
		self::$_userprofileid_cache[$userid] = $profileid;
	}
	static function lookupUserProfileId($userid) {
		if(isset(self::$_userprofileid_cache[$userid])) {
			return self::$_userprofileid_cache[$userid];
		}
		return false;
	}
	
	/** Profile2Field information caching */
	static $_profile2fieldpermissionlist_cache = array();
	static function lookupProfile2FieldPermissionList($module, $profileid) {
		$pro2fld_perm = self::$_profile2fieldpermissionlist_cache;
		if(isset($pro2fld_perm[$module]) && isset($pro2fld_perm[$module][$profileid])) {
			return $pro2fld_perm[$module][$profileid];
		}
		return false;
	}
	static function updateProfile2FieldPermissionList($module, $profileid, $value) {
		self::$_profile2fieldpermissionlist_cache[$module][$profileid] = $value;
	}
	
	/** Role information */
	static $_subroles_roleid_cache = array();
	static function lookupRoleSubordinates($roleid) {
		if(isset(self::$_subroles_roleid_cache[$roleid])) {
			return self::$_subroles_roleid_cache[$roleid];
		}
		return false;
	}
	static function updateRoleSubordinates($roleid, $roles) {
		self::$_subroles_roleid_cache[$roleid] = $roles;
	}
	static function clearRoleSubordinates($roleid = false) {
		if($roleid === false) {
			self::$_subroles_roleid_cache = array();
		} else if(isset(self::$_subroles_roleid_cache[$roleid])) {
			unset(self::$_subroles_roleid_cache[$roleid]);
		}
	}
	
	/** Related module information for Report */
	static $_report_listofmodules_cache = false;
	static function lookupReport_ListofModuleInfos() {
		return self::$_report_listofmodules_cache;
	}
	static function updateReport_ListofModuleInfos($module_list, $related_modules) {
		if(self::$_report_listofmodules_cache === false) {
			self::$_report_listofmodules_cache = array(
				'module_list' => $module_list,
				'related_modules' => $related_modules
			);
		}
	}
	
	/** Report module information based on used. */
	static $_reportmodule_infoperuser_cache = array();
	static function lookupReport_Info($userid, $reportid) {
		
		if(isset(self::$_reportmodule_infoperuser_cache[$userid])) {
			if(isset(self::$_reportmodule_infoperuser_cache[$userid][$reportid])) {
				return self::$_reportmodule_infoperuser_cache[$userid][$reportid];
			}
		}
		return false;
	}
	static function updateReport_Info($userid, $reportid, $primarymodule, $secondarymodules, $reporttype,
	$reportname, $description, $folderid, $owner) {
		if(!isset(self::$_reportmodule_infoperuser_cache[$userid])) {
			self::$_reportmodule_infoperuser_cache[$userid] = array();
		}
		if(!isset(self::$_reportmodule_infoperuser_cache[$userid][$reportid])) {
			self::$_reportmodule_infoperuser_cache[$userid][$reportid] = array (
				'reportid'        => $reportid,
				'primarymodule'   => $primarymodule,
				'secondarymodules'=> $secondarymodules,
				'reporttype'      => $reporttype,
				'reportname'      => $reportname,
				'description'     => $description,
				'folderid'        => $folderid,
				'owner'           => $owner			
			);
		}
	}
	
	/** Report module sub-ordinate users information. */
	static $_reportmodule_subordinateuserid_cache = array();
	static function lookupReport_SubordinateUsers($reportid) {
		if(isset(self::$_reportmodule_subordinateuserid_cache[$reportid])) {
			return self::$_reportmodule_subordinateuserid_cache[$reportid];
		}
		return false;
	}
	static function updateReport_SubordinateUsers($reportid, $userids) {
		self::$_reportmodule_subordinateuserid_cache[$reportid] = $userids;
	}

	/** Report module information based on used. */
	static $_reportmodule_scheduledinfoperuser_cache = array();
	static function lookupReport_ScheduledInfo($userid, $reportid) {

		if(isset(self::$_reportmodule_scheduledinfoperuser_cache[$userid])) {
			if(isset(self::$_reportmodule_scheduledinfoperuser_cache[$userid][$reportid])) {
				return self::$_reportmodule_scheduledinfoperuser_cache[$userid][$reportid];
			}
		}
		return false;
	}
	static function updateReport_ScheduledInfo($userid, $reportid, $isScheduled, $scheduledFormat, $scheduledInterval, $scheduledRecipients, $scheduledTime) {
		if(!isset(self::$_reportmodule_scheduledinfoperuser_cache[$userid])) {
			self::$_reportmodule_scheduledinfoperuser_cache[$userid] = array();
		}
		if(!isset(self::$_reportmodule_scheduledinfoperuser_cache[$userid][$reportid])) {
			self::$_reportmodule_scheduledinfoperuser_cache[$userid][$reportid] = array (
				'reportid'				=> $reportid,
				'isScheduled'			=> $isScheduled,
				'scheduledFormat'		=> $scheduledFormat,
				'scheduledInterval'		=> $scheduledInterval,
				'scheduledRecipients'	=> $scheduledRecipients,
				'scheduledTime'			=> $scheduledTime,
			);
		}
	}
}

?>