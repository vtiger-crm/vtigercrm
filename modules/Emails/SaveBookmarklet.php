<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/Webservices/Utils.php';
require_once("include/Zend/Json.php");
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';
require_once("include/Webservices/VtigerCRMObject.php");
require_once("include/Webservices/VtigerCRMObjectMeta.php");
require_once("include/Webservices/WebServiceError.php");
require_once 'include/Webservices/ModuleTypes.php';
require_once("include/Webservices/DescribeObject.php");

global $app_strings;

global $currentModule;
global $theme;

require_once('Smarty_setup.php');

// focus_list is the means of passing data to a ListView.
global $focus_list;

if (!isset($where)) $where = "";

$url_string = '';

$smarty = new vtigerCRM_Smarty;
$smarty->assign("subject",$_REQUEST['subject']);
$smarty->assign("description",$_REQUEST['description']);

Zend_Json::$useBuiltinEncoderDecoder = true;
$json = new Zend_Json();

$elementType = $_REQUEST['module'];

global $log,$adb;
$webserviceObject = VtigerWebserviceObject::fromName($adb,$elementType);
$handlerPath = $webserviceObject->getHandlerPath();
$handlerClass = $webserviceObject->getHandlerClass();

require_once $handlerPath;

$handler = new $handlerClass($webserviceObject,$current_user,$adb,$log);
$meta = $handler->getMeta();
$meta->retrieveMeta();

$types = vtws_listtypes($current_user);
if(!in_array($elementType,$types['types'])){
	throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to perform the operation is denied");
}

$wsFieldDetails = $handler->getField('parent_id');

$moduleEntityNameDetails = array();
$moduleEmailFieldDetails = array();
foreach ($wsFieldDetails['type']['refersTo'] as $type) {
	$referenceModuleHandler = vtws_getModuleHandlerFromName($type, $current_user);
	$referenceModuleMeta = $referenceModuleHandler->getMeta();
	$nameFields = explode(',',$referenceModuleMeta->getNameFields());
	$moduleFields = $referenceModuleMeta->getModuleFields();
	$accessibleFields = array_keys($moduleFields);
	$accessibleNameFields = array_intersect($nameFields, $accessibleFields);
	$moduleEntityNameDetails[$type] = $accessibleNameFields;
	$moduleEmailFieldDetails[$type] = $referenceModuleMeta->getEmailFields();
}

$smarty->assign("types",$wsFieldDetails['type']['refersTo']);
$smarty->assign("entityNameFields",$json->encode($moduleEntityNameDetails));
$smarty->assign("emailFields",$json->encode($moduleEmailFieldDetails));
$smarty->assign("userEmail",$current_user->column_fields['email1']);

$smarty->display("modules/Bookmarklet/Bookmarklet.tpl");
?>