<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('include/events/SqlResultIterator.inc');
require_once('VTWorkflowManager.inc');
require_once('VTTaskManager.inc');
require_once('VTTaskQueue.inc');
require_once('VTEntityCache.inc');

require_once 'include/Webservices/Utils.php';
require_once("modules/Users/Users.php");
require_once("include/Webservices/VtigerCRMObject.php");
require_once("include/Webservices/VtigerCRMObjectMeta.php");
require_once("include/Webservices/DataTransform.php");
require_once("include/Webservices/WebServiceError.php");
require_once 'include/utils/utils.php';
require_once 'include/Webservices/ModuleTypes.php';
require_once('include/Webservices/Retrieve.php');
require_once('include/Webservices/Update.php');
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';
require_once('VTWorkflowUtils.php');

	/*
	* VTEventHandler 
	*/
	class VTWorkflowEventHandler extends VTEventHandler{
		/**
		 * Push tasks to the task queue if the conditions are true
		 * @param $entityData A VTEntityData object representing the entity.
		 */
		function handleEvent($eventName, $entityData){
			$util = new VTWorkflowUtils();
			$user = $util->adminUser();
			global $adb;
			$isNew = $entityData->isNew();
			$entityCache = new VTEntityCache($user);
			$wsModuleName = $util->toWSModuleName($entityData);
			$wsId = vtws_getWebserviceEntityId($wsModuleName, 
											   $entityData->getId());
			$entityData = $entityCache->forId($wsId);
			$data = $entityData->getData();
			$wfs = new VTWorkflowManager($adb);
			$workflows = $wfs->getWorkflowsForModule($entityData->getModuleName());
			$tm = new VTTaskManager($adb);
			$taskQueue = new VTTaskQueue($adb);
			
			foreach($workflows as $workflow){
				switch($workflow->executionCondition){
					case VTWorkflowManager::$ON_FIRST_SAVE:{
						if($isNew){
							$doEvaluate = true;
						}else{
							$doEvaluate = false;
						}
						break;
					}
					case VTWorkflowManager::$ONCE:{
						$entity_id = vtws_getIdComponents($entityData->getId());
						$entity_id = $entity_id[1];
						$result = $adb->pquery("SELECT * FROM com_vtiger_workflow_activatedonce 
							WHERE entity_id=? and workflow_id=?", array($entity_id, $workflow->id));
						
						//Changes
						$result2=$adb->pquery("SELECT * FROM com_vtiger_workflowtasks 
										INNER JOIN com_vtiger_workflowtask_queue
										ON com_vtiger_workflowtasks.task_id= com_vtiger_workflowtask_queue.task_id
										WHERE workflow_id=? AND entity_id=?",
										array($workflow->id,$entity_id));
						
						if($adb->num_rows($result)===0 && $adb->num_rows($result2)===0){
							$doEvaluate = true;
						}else{
							$doEvaluate = false;
						}
						break;
					}
					case VTWorkflowManager::$ON_EVERY_SAVE:{
						$doEvaluate = true;
						break;
					}
					case VTWorkflowManager::$ON_MODIFY:{
						$doEvaluate = !($isNew);
					    break;
					}
					default:{
						throw new Exception("Should never come here! Execution Condition:".$workflow->executionCondition);
					}
					
				}
				if($doEvaluate && $workflow->evaluate($entityCache, $entityData->getId())){
					if(VTWorkflowManager::$ONCE == $workflow->executionCondition) {
						$entity_id = vtws_getIdComponents($entityData->getId());
						$entity_id = $entity_id[1];						
						$adb->pquery("INSERT INTO com_vtiger_workflow_activatedonce (entity_id, workflow_id)
							VALUES (?,?)", array($entity_id, $workflow->id));
					}
					
					$tasks = $tm->getTasksForWorkflow($workflow->id);
					foreach($tasks as $task){
						if($task->active) {
							$trigger = $task->trigger;
							if($trigger != null){
								$delay = strtotime($data[$trigger['field']])+$trigger['days']*86400;
							}else{
								$delay = 0;
							}
							if($task->executeImmediately==true){
								$task->doTask($entityData);
							}else{
								$taskQueue->queueTask($task->id,$entityData->getId(), $delay);
							}
						}
					}
				}
			}
			$util->revertUser();
		}
	}
?>
