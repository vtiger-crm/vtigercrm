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

$moduleFilepath = 'modules/'.$_REQUEST['module'].'/'.$_REQUEST['file'].'.php';

if(file_exists($moduleFilepath) == false) {

	$moduleFilepath = 'modules/Vtiger/'.$_REQUEST['file'].'.php';

}

checkFileAccessForInclusion($moduleFilepath);

require_once $moduleFilepath;
?>
