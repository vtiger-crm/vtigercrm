<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
	
	function vtws_sync($mtime,$elementType,$user){
			
		global $adb, $recordString,$modifiedTimeString;
		
		$ignoreModules = array("");
		$typed = true;
		$dformat = "Y-m-d H:i:s";
		
		$datetime = date($dformat, $mtime);
		
		$setypeArray = array();
		$setypeData = array();
		$setypeHandler = array();
		$setypeNoAccessArray = array();
		
		if(!isset($elementType) || $elementType=='' || $elementType==null){
			$typed=false;
		}
		
		$adb->startTransaction();
		$q= "select crmid,setype from vtiger_crmentity where modifiedtime >? and smownerid=? and deleted=0";
		$params = array($datetime,$user->id);
		if($typed){
			$q = $q." and setype=?";
			array_push($params,$elementType); 
		}
		
		$result = $adb->pquery($q, $params);
		
		do{
			if($arre){
				if(strpos($arre["setype"]," ")===FALSE){
					if($arre["setype"] == 'Calendar'){
						$seType = vtws_getCalendarEntityType($arre['crmid']);
					}else{
						$seType = $arre["setype"];
					}
					if(array_search($seType,$ignoreModules) === FALSE){
						$setypeArray[$arre["crmid"]] = $seType;
						if(!$setypeData[$seType]){
							$webserviceObject = VtigerWebserviceObject::fromName($adb,$seType);
							$handlerPath = $webserviceObject->getHandlerPath();
							$handlerClass = $webserviceObject->getHandlerClass();
							
							require_once $handlerPath;
							
							$setypeHandler[$seType] = new $handlerClass($webserviceObject,$user,$adb,$log);
							$meta = $setypeHandler[$seType]->getMeta();
							$setypeData[$seType] = new VtigerCRMObject(getTabId($meta->getEntityName()),true);
						}
					}
				}
			}
			$arre = $adb->fetchByAssoc($result);
			
		}while($arre);
		
		$output = array();
		
		$output["updated"] = array();
		
		foreach($setypeArray as $key=>$val){
			
			$handler = $setypeHandler[$val];
			$meta = $handler->getMeta();
			
			if(!$meta->hasAccess() || !$meta->hasWriteAccess() || !$meta->hasPermission(EntityMeta::$RETRIEVE,$key)){
				if(!$setypeNoAccessArray[$val]){
					$setypeNoAccessArray[] = $val;
				}
				continue;
			}
			try{
				$error = $setypeData[$val]->read($key);
				if(!$error){
					//Ignore records whose fetch results in an error.
					continue;
				}
				$output["updated"][] = DataTransform::filterAndSanitize($setypeData[$val]->getFields(),$meta);;
			}catch(WebServiceException $e){
				//ignore records the user doesn't have access to.
				continue;
			}catch(Exception $e){
				throw new WebServiceException(WebServiceErrorCode::$INTERNALERROR,"Unknown Error while processing request");
			}
		}
		
		$setypeArray = array();
		$setypeData = array();
		
		$q= "select crmid,setype,modifiedtime from vtiger_crmentity where modifiedtime >? and smownerid=? and deleted=1";
		$params = array($datetime,$user->id);
		if($typed){
			$q = $q." and setype=?";
			array_push($params,$elementType);
		}
		
		$result = $adb->pquery($q, $params);
		
		do{
			if($arre){
				if(strpos($arre["setype"]," ")===FALSE){
					if($arre["setype"] == 'Calendar'){
						$seType = vtws_getCalendarEntityType($arre['crmid']);
					}else{
						$seType = $arre["setype"];
					}
					if(array_search($seType,$ignoreModules) === FALSE){
						$setypeArray[$arre["crmid"]] = $seType;
						if(!$setypeData[$seType]){
							$webserviceObject = VtigerWebserviceObject::fromName($adb,$seType);
							$handlerPath = $webserviceObject->getHandlerPath();
							$handlerClass = $webserviceObject->getHandlerClass();
							
							require_once $handlerPath;
							
							$setypeHandler[$seType] = new $handlerClass($webserviceObject,$user,$adb,$log);
							$meta = $setypeHandler[$seType]->getMeta();
							$setypeData[$seType] = new VtigerCRMObject(getTabId($meta->getEntityName()),true);
						}
					}
				}
			}
			$arre = $adb->fetchByAssoc($result);
			
		}while($arre);
		
		$output["deleted"] = array();
		
		foreach($setypeArray as $key=>$val){
			$handler = $setypeHandler[$val];
			$meta = $handler->getMeta();
			
			if(!$meta->hasAccess() || !$meta->hasWriteAccess() /*|| !$meta->hasPermission(VtigerCRMObjectMeta::$RETRIEVE,$key)*/){
				if(!$setypeNoAccessArray[$val]){
					$setypeNoAccessArray[] = $val;
				}
				continue;
			}
			
			$output["deleted"][] = vtws_getId($meta->getEntityId(), $key);
		}
		
		$q= "select max(modifiedtime) as modifiedtime from vtiger_crmentity where modifiedtime >? and smownerid=?";
		$params = array($datetime,$user->id);
		if($typed){
			$q = $q." and setype=?";
			array_push($params,$elementType);
		}else if(sizeof($setypeNoAccessArray)>0){
			$q = $q." and setype not in ('".generateQuestionMarks($setypeNoAccessArray)."')";
			array_push($params,$setypeNoAccessArray);
		}
		
		$result = $adb->pquery($q, $params);
		$arre = $adb->fetchByAssoc($result);
		$modifiedtime = $arre['modifiedtime'];
		
		if(!$modifiedtime){
			$modifiedtime = $mtime;
		}else{
			$modifiedtime = vtws_getSeconds($modifiedtime);
		}
		if(is_string($modifiedtime)){
			$modifiedtime = intval($modifiedtime);
		}
		$output['lastModifiedTime'] = $modifiedtime;
		
		$error = $adb->hasFailedTransaction();
		$adb->completeTransaction();
		
		if($error){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,"Database error while performing required operation");
		}
		
		VTWS_PreserveGlobal::flush();
		return $output;
	}
	
	function vtws_getSeconds($mtimeString){
		//TODO handle timezone and change time to gmt.
		return strtotime($mtimeString);
	}
	
?>
