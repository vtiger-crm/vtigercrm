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
 * $Header$
 * Description:  generic list view class.
 ********************************************************************************/
require_once('include/logging.php');
require_once('include/ListView/ListViewSession.php');

class ListView {
	
	var $local_theme = null;
	var  $local_app_strings= null;
	var  $local_image_path = null;
	var  $local_current_module = null;
	var $local_mod_strings = null;
	var  $records_per_page = 20;
	var  $xTemplate = null;
	var $xTemplatePath = null;
	var $seed_data = null;
	var $query_where = null;
	var $query_limit = -1;
	var $query_orderby = null;
	var $header_title = "";
	var $header_text = "";
	var $log = null;
	var $initialized = false;
	var $querey_where_has_changed = false;
	var $display_header_and_footer = true;


/**initializes ListView
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
*/
 function ListView(){
	global $log;
	$log->debug("Entering ListView() method ...");
 	
 	
	if(!$this->initialized){
		global $list_max_entries_per_page;
		$this->records_per_page = $list_max_entries_per_page + 0;
		$this->initialized = true;
		global $theme, $app_strings, $image_path, $currentModule;
		$this->local_theme = $theme;
		$this->local_app_strings = &$app_strings;
		$this->local_image_path = $image_path;
		$this->local_current_module = $currentModule;

		if(empty($this->local_image_path)){
			$this->local_image_path = 'themes/'.$theme.'/images';
		}
		$this->log = LoggerManager::getLogger('listView_'.$this->local_current_module);
		$log->debug("Exiting ListView method ...");
	}	
}
/**sets the header title */
 function setHeaderTitle($value){
	global $log;
	$log->debug("Entering setHeaderTitle(".$value.") method ...");
	$this->header_title = $value;	
	$log->debug("Exiting setHeaderTitle method ...");
}
/**sets the header text this is text thats appended to the header vtiger_table and is usually used for the creation of buttons
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
*/
 function setHeaderText($value){
	global $log;
	$log->debug("Entering setHeaderText(".$value.") method ...");
	$this->header_text = $value;	
	$log->debug("Exiting setHeaderText method ...");
}
/**sets the parameters dealing with the db
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
*/
 function setQuery($where, $limit, $orderBy, $varName, $allowOrderByOveride= true){
	global $log;
	$log->debug("Entering setQuery(".$where.",". $limit.",". $orderBy.",". $varName.",". $allowOrderByOveride.") method ...");
	$this->query_where = $where;
	if($this->getSessionVariable("query", "where") != $where){
		$this->querey_where_has_changed = true;
		$this->setSessionVariable("query", "where", $where);
	}
	
	$this->query_limit = $limit;
	if(!$allowOrderByOveride){
		$this->query_orderby = $orderBy;
		$log->debug("Exiting setQuery method ...");
		return;
 	}
	$sortBy = $this->getSessionVariable($varName, "ORDER_BY") ;

	if(empty($sortBy)){
		$this->setUserVariable($varName, "ORDER_BY", $orderBy);
		$sortBy = $orderBy;
	}else{
		$this->setUserVariable($varName, "ORDER_BY", $sortBy);	
	}
	if($sortBy == 'amount'){
		$sortBy = 'amount*1';	
	}

	$desc = false;
	$desc = $this->getSessionVariable($varName, $sortBy."_desc");
	
	if(empty($desc))
		$desc = false;
	if(isset($_REQUEST[$this->getSessionVariableName($varName,  "ORDER_BY")]))
		$last = $this->getSessionVariable($varName, "ORDER_BY_LAST");
		if(!empty($last) && $last == $sortBy){
			$desc = !$desc;
		}else {
			$this->setSessionVariable($varName, "ORDER_BY_LAST", $sortBy);	
		}	
	$this->setSessionVariable($varName, $sortBy."_desc", $desc);
	if(!empty($sortBy)){
	if(substr_count(strtolower($sortBy), ' desc') == 0 && substr_count(strtolower($sortBy), ' asc') == 0){
		if($desc){
			$this->query_orderby = $sortBy.' desc';
		}else{ 
			$this->query_orderby = $sortBy.' asc';
		}
	}
	else{
		$this->query_orderby = $sortBy;	
	}
	}else {
		$this->query_orderby = "";	
	}
	$log->debug("Exiting setQuery method ...");
	
	
	
	
}

/**sets the theme used only use if it is different from the global
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
*/
 function setTheme($theme){
	global $log;
	$log->debug("Entering setTheme(".$theme.") method ...");
	$this->local_theme = $theme;
	if(isset($this->xTemplate))$this->xTemplate->assign("THEME", $this->local_theme );
	$log->debug("Exiting setTheme method ...");
}

/**sets the AppStrings used only use if it is different from the global
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
*/
 function setAppStrings(&$app_strings){
	global $log;
	$log->debug("Entering setAppStrings(".$app_strings.") method ...");
	unset($this->local_app_strings);
	$this->local_app_strings = $app_strings;
	if(isset($this->xTemplate))$this->xTemplate->assign("APP", $this->local_app_strings );
	$log->debug("Exiting setAppStrings method ...");
}

/**sets the ModStrings used 
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
*/
 function setModStrings(&$mod_strings){
	global $log;
	$log->debug("Entering setModStrings(".$mod_strings.") method ...");
	unset($this->local_module_strings);
	$this->local_mod_strings = $mod_strings;
	if(isset($this->xTemplate))$this->xTemplate->assign("MOD", $this->local_mod_strings );
	$log->debug("Exiting setModStrings method ...");
}

/**sets the ImagePath used
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
*/
 function setImagePath($image_path){
	global $log;
	$log->debug("Entering setImagePath(".$image_path.") method ...");
	$this->local_image_path = $image_path;
	if(empty($this->local_image_path)){
		$this->local_image_path = 'themes/'.$this->local_theme.'/images';
	}
	if(isset($this->xTemplate))$this->xTemplate->assign("IMAGE_PATH", $this->local_image_path );
	$log->debug("Exiting setImagePath method ...");
}

/**sets the currentModule only use if this is different from the global
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
*/
 function setCurrentModule($currentModule){
	global $log;
	$log->debug("Entering setCurrentModule(".$currentModule.") method ...");
	unset($this->local_current_module);
	$this->local_current_module = $currentModule;
	$this->log = LoggerManager::getLogger('listView_'.$this->local_current_module);
	if(isset($this->xTemplate))$this->xTemplate->assign("MODULE_NAME", $this->local_current_module );
	$log->debug("Exiting setCurrentModule method ...");

}


/**INTERNAL FUNCTION sets a session variable
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
*/
 function setSessionVariable($localVarName,$varName, $value){
		global $log;
		$log->debug("Entering setSessionVariable(".$localVarName.",".$varName.",". $value.") method ...");
		$_SESSION[$this->local_current_module."_".$localVarName."_".$varName] = $value;
		$log->debug("Exiting setSessionVariable method ...");
}

function setUserVariable($localVarName,$varName, $value){
		global $log;
		$log->debug("Entering setUserVariable(".$localVarName.",".$varName.",". $value.") method ...");
		global $current_user;
		$current_user->setPreference($this->local_current_module."_".$localVarName."_".$varName, $value);
		$log->debug("Exiting setUserVariable method ...");
}

/**INTERNAL FUNCTION returns a session variable first checking the querey for it then checking the session
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
*/
 function getSessionVariable($localVarName,$varName){
		global $log;
		$log->debug("Entering getSessionVariable(".$localVarName.",".$varName.") method ...");
		if(isset($_REQUEST[$this->getSessionVariableName($localVarName, $varName)])){
			$this->setSessionVariable($localVarName,$varName,vtlib_purify($_REQUEST[$this->getSessionVariableName($localVarName, $varName)])); 		
		}
		 if(isset($_SESSION[$this->getSessionVariableName($localVarName, $varName)])){
			$log->debug("Exiting getSessionVariable method ...");
		 	return $_SESSION[$this->getSessionVariableName($localVarName, $varName)];	
		 }
		 $log->debug("Exiting getSessionVariable method ...");
		 return "";
}

/**

 * @return void
 * @param unknown $localVarName
 * @param unknown $varName
 * @desc INTERNAL FUNCTION returns the session/query variable name
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function getSessionVariableName($localVarName,$varName){
	global $log;
	$log->debug("Entering getSessionVariableName(".$localVarName.",".$varName.") method ...");
	$log->debug("Exiting getSessionVariableName method ...");
	return $this->local_current_module."_".$localVarName."_".$varName;
}


}

?>