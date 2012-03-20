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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/database/DatabaseConnection.php,v 1.3 2005/01/20 09:35:16 jack Exp $
 * Description:  Creates the runtime database connection.
 ********************************************************************************/

class DatabaseConnection {


	function println($msg)
	{
	require_once('include/logging.php');
	$log1 =& LoggerManager::getLogger('GS');
	if(is_array($msg))
	{
			$log1->fatal("PearDatabse ->".print_r($msg,true));
	}
	else
	{
		$log1->info("PearDatabase ->".$msg);
	}
	return $msg;
	}

}

	$database = new DatabaseConnection;
	$database->println("DatabaseConnection - Illegal Access");

?>
