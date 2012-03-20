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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/graph.php,v 1.1 2004/08/18 12:22:54 gjayakrishnan Exp $
 * Description: Main file and starting point for the application.  Calls the 
 * theme header and footer files defined for the user as well as the module as 
 * defined by the input parameters.
 ********************************************************************************/
 
require_once('config.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');

global $default_language;

$log =& LoggerManager::getLogger('graph');
$log->debug($_REQUEST);

if(isset($_REQUEST['action']) && isset($_REQUEST['module']))
{
	$action = $_REQUEST['action']; 
	$current_module_file = 'modules/'.$_REQUEST['module'].'/'.$action.'.php';
	$current_module = $_REQUEST['module'];
}
elseif(isset($_REQUEST['module']))
{
	$current_module = $_REQUEST['module'];
	$current_module_file = 'modules/'.$_REQUEST['module'].'/Charts.php';
}
else {
    exit();
}

$current_language = $default_language;
if(isset($_REQUEST['current_language']))
{
	$current_language = $_REQUEST['current_language'];
}

// retrieve the translated strings.
$app_strings = return_application_language($current_language);

if(isset($app_strings['LBL_CHARSET']))
{
	$charset = $app_strings['LBL_CHARSET'];
}
else
{
	$charset = $default_charset;	
}

$log->info("current langugage is $current_language");
$log->info("current module is $current_module ");	
$log->info("including $current_module_file");	

checkFileAccess($current_module_file);
require_once($current_module_file);
$draw_this = new jpgraph();

if (isset($_REQUEST['graph'])) $graph = $_REQUEST['graph']; 
else $graph = 'default';

if (isset($_REQUEST['flat_array1'])) $flat_array1 = $_REQUEST['flat_array1']; 
else $flat_array1="foo,bar";
if (isset($_REQUEST['flat_array2'])) $flat_array2 = $_REQUEST['flat_array2']; 
else $flat_array2="1,2";
if (isset($_REQUEST['title'])) $title = $_REQUEST['title']; 
else $title="the title";
if (isset($_REQUEST['subtitle'])) $subtitle = $_REQUEST['subtitle']; 
else $subtitle="the subtitle";

$log->debug("draw_this->$graph");
$log->debug("flat_array1 is ".$flat_array1);
$log->debug("flat_array2 is ".$flat_array2);
$log->debug("title is ".$title);
$log->debug("subtitle is ".$subtitle);

$array1 = explode(",", $flat_array1);
$array2 = explode(",", $flat_array2);

$draw_this->$graph($array1, $array2, $title, $subtitle);
?>