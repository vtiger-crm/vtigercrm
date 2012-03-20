<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

	require_once("include/Webservices/QueryParser.php");
	
	function vtws_query($q,$user){
		
		global $log,$adb;
		$webserviceObject = VtigerWebserviceObject::fromQuery($adb,$q);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		
		require_once $handlerPath;
		
		$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
		$meta = $handler->getMeta();
		
		$types = vtws_listtypes($user);
		if(!in_array($webserviceObject->getEntityName(),$types['types'])){
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to perform the operation is denied");
		}
		
		if(!$meta->hasReadAccess()){
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to read is denied");
		}
		
		$result = $handler->query($q);
		VTWS_PreserveGlobal::flush();
		return $result;
	}
	
?>