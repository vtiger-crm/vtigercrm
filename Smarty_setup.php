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

require('Smarty/libs/Smarty.class.php');
class vtigerCRM_Smarty extends Smarty{
	
	/** Cache the tag cloud display information for re-use */
	static $_tagcloud_display_cache = array();
	
	static function lookupTagCloudView($userid) {
		if(!isset(self::$_tagcloud_display_cache[$userid])) {
			self::$_tagcloud_display_cache[$userid] = getTagCloudView($userid);
		}
		return self::$_tagcloud_display_cache[$userid];
	}
	/** END */
	
	/**This function sets the smarty directory path for the member variables	
	*/
	function vtigerCRM_Smarty()
	{
		global $CALENDAR_DISPLAY, $WORLD_CLOCK_DISPLAY, $CALCULATOR_DISPLAY, $CHAT_DISPLAY, $current_user;

		$this->Smarty();
		$this->template_dir = 'Smarty/templates';
		$this->compile_dir = 'Smarty/templates_c';
		$this->config_dir = 'Smarty/configs';
		$this->cache_dir = 'Smarty/cache';

		//$this->caching = true;
	        //$this->assign('app_name', 'Login');
		$this->assign('CALENDAR_DISPLAY', $CALENDAR_DISPLAY); 
 		$this->assign('WORLD_CLOCK_DISPLAY', $WORLD_CLOCK_DISPLAY); 
 		$this->assign('CALCULATOR_DISPLAY', $CALCULATOR_DISPLAY); 
 		$this->assign('CHAT_DISPLAY', $CHAT_DISPLAY);
		$this->assign('CURRENT_USER_ID',$current_user->id);
 		
 		// Query For TagCloud only when required
 		if(isset($_REQUEST) && $_REQUEST['action'] == 'DetailView') {
			//Added to provide User based Tagcloud
            $this->assign('TAG_CLOUD_DISPLAY', self::lookupTagCloudView($current_user->id) );
 		}
	}
}

?>
