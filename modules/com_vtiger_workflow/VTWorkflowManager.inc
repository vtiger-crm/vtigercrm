<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
	require_once("VTJsonCondition.inc");
	require_once 'include/utils/ConfigReader.php';

	class VTWorkflowManager{

		static $ON_FIRST_SAVE = 1;
		static $ONCE = 2;
		static $ON_EVERY_SAVE = 3;
		static $ON_MODIFY = 4;
        // Reserving 5 & 6 for ON_DELETE and ON_SCHEDULED types.
		static $MANUAL = 7;

		function __construct($adb){
			$this->adb = $adb;
		}

		function save($workflow){
			$adb=$this->adb;
			if(isset($workflow->id)){
				$wf=$workflow;
				$adb->pquery("update com_vtiger_workflows set
								module_name=?, summary=?, test=?, execution_condition=?, defaultworkflow=?
								where workflow_id=?",
					array($wf->moduleName, $wf->description, $wf->test, $wf->executionCondition, $wf->defaultworkflow, $wf->id));
			}else{
				$workflowId = $adb->getUniqueID("com_vtiger_workflows");
				$workflow->id = $workflowId;
				$wf=$workflow;

				$result=$adb->getColumnNames("com_vtiger_workflows");
				if(in_array("type",$result)) {
					$adb->pquery("insert into com_vtiger_workflows
									(workflow_id, module_name, summary, test, execution_condition, type, defaultworkflow)
									values (?,?,?,?,?,?,?)",
						array($workflowId, $wf->moduleName, $wf->description, $wf->test,  $wf->executionCondition, $wf->type, $wf->defaultworkflow));
				} else {
					$adb->pquery("insert into com_vtiger_workflows
									(workflow_id, module_name, summary, test, execution_condition, defaultworkflow)
									values (?,?,?,?,?,?)",
						array($workflowId, $wf->moduleName, $wf->description, $wf->test,  $wf->executionCondition, $wf->defaultworkflow));
				}
			}
		}


		function getWorkflows(){
			$adb=$this->adb;

			$result=$adb->getColumnNames("com_vtiger_workflows");
			if(in_array("defaultworkflow",$result)){
				$result = $adb->query("select workflow_id, module_name, summary, test, execution_condition,defaultworkflow, type
									from com_vtiger_workflows ");
			}else{

				$result = $adb->query("select workflow_id, module_name, summary, test, execution_condition, type
									from com_vtiger_workflows");
			}
			return $this->getWorkflowsForResult($result);

		}

		function getWorkflowsForModule($moduleName){
			$adb=$this->adb;

			//my changes
			$result=$adb->getColumnNames("com_vtiger_workflows");
			if(in_array(defaultworkflow,$result)){
				$result = $adb->pquery("select workflow_id, module_name, summary, test, execution_condition, defaultworkflow, type
									from com_vtiger_workflows where module_name=?",array($moduleName));
			}
			else{
				$result = $adb->pquery("select workflow_id, module_name, summary, test, execution_condition, type
									from com_vtiger_workflows where module_name=?",array($moduleName));
			}
			return $this->getWorkflowsForResult($result);
		}

		protected function getWorkflowsForResult($result){
			$adb=$this->adb;

			$it = new SqlResultIterator($adb, $result);
			$workflows=array();
			foreach($it as $row){
				$workflow = $this->getWorkflowInstance($row->type);
				$workflow->setup($row->data);

				if(!is_a($workflow, 'Workflow')) continue;

				$workflows[$workflow->id]=$workflow;
			}
			return $workflows;
		}

		protected function getWorkflowInstance($type='basic') {
			$configReader = new ConfigReader('modules/com_vtiger_workflow/config.inc', 'workflowConfig');
			$workflowTypeConfig = $configReader->getConfig($type);
			$workflowClassPath = $workflowTypeConfig['classpath'];
			$workflowClass = $workflowTypeConfig['class'];

			require_once $workflowClassPath;
			$workflow = new $workflowClass();
			return $workflow;
		}

		/**
		 * Retrieve a workflow from the database
		 *
		 * Returns null if the workflow doesn't exist.
		 *
		 * @param The id of the workflow
		 * @return A workflow object.
		 */
		function retrieve($id){
			$adb=$this->adb;
			$result = $adb->pquery("select workflow_id, module_name, summary, test, execution_condition, type, defaultworkflow
									from com_vtiger_workflows where workflow_id=?",
						array($id));

			if($adb->num_rows($result)){
				$data = $adb->raw_query_result_rowdata($result, 0);
				$workflow = $this->getWorkflowInstance($data['type']);
				$workflow->setup($data);
				return $workflow;
			}else{
				return null;
			}
		}

		function delete($id){
			$adb=$this->adb;
			$adb->pquery("DELETE FROM com_vtiger_workflowtasks WHERE workflow_id IN
							(SELECT workflow_id FROM com_vtiger_workflows WHERE workflow_id=? AND (defaultworkflow IS NULL OR defaultworkflow != 1))",
						array($id));
			$adb->pquery("DELETE FROM com_vtiger_workflows WHERE workflow_id=? AND (defaultworkflow IS NULL OR defaultworkflow != 1)", array($id));
		}

		function newWorkflow($moduleName){
			$workflow = $this->getWorkflowInstance();
			$workflow->moduleName = $moduleName;
			$workflow->executionCondition = self::$ON_EVERY_SAVE;
			$workflow->type = 'basic';
			return $workflow;

		}


		/**
		 * Export a workflow as a json encoded string
		 *
		 * @param $workflow The workflow instance to export.
		 */
		public function serializeWorkflow($workflow){
			$exp = array();
			$exp['moduleName'] = $workflow->moduleName;
			$exp['description'] = $workflow->description;
			$exp['test'] = $workflow->test;
			$exp['executionCondition'] = $workflow->executionCondition;
			$exp['tasks'] = array();
			$tm = new VTTaskManager($this->adb);
			$tasks = $tm->getTasksForWorkflow($workflow->id);
			foreach($tasks as $task){
				unset($task->id);
				unset($task->workflowId);
				$exp['tasks'][] = serialize($task);
			}
			return  Zend_Json::encode($exp);
		}

		/**
		 * Import a json encoded string as a workflow object
		 *
		 * @return The Workflow instance representing the imported workflow.
		 */
		public function deserializeWorkflow($str){
			$data =  Zend_Json::decode($str);
			$workflow = $this->newWorkflow($data['moduleName']);
			$workflow->description = $data['description'];
			$workflow->test = $data['test'];
			$workflow->executionCondition = $data['executionCondition'];
			$this->save($workflow);
			$tm = new VTTaskManager($this->adb);
			$tasks = $data['tasks'];
			foreach($tasks as $taskStr){
				$task = $tm->unserializeTask($taskStr);
				$task->workflowId = $workflow->id;
				$tm->saveTask($task);
			}
			return $workflow;
		}

	}

	class Workflow{
		function __construct(){
			$this->conditionStrategy = new VTJsonCondition();
		}

		function setup($row) {
			$this->id = $row['workflow_id'];
			$this->moduleName = $row['module_name'];
			$this->description = to_html($row['summary']);
			$this->test = $row['test'];
			$this->executionCondition = $row['execution_condition'];

			if($row['defaultworkflow']){
				$this->defaultworkflow=$row['defaultworkflow'];
			}
		}

		function evaluate($entityCache, $id){
			if($this->test==""){
				return true;
			}else{
				$cs = $this->conditionStrategy;
				return $cs->evaluate($this->test,
									 $entityCache, $id);
			}
		}

		function isCompletedForRecord($recordId) {
			global $adb;

			$result = $adb->pquery("SELECT * FROM com_vtiger_workflow_activatedonce
							WHERE entity_id=? and workflow_id=?", array($recordId, $this->id));

			$result2=$adb->pquery("SELECT * FROM com_vtiger_workflowtasks
							INNER JOIN com_vtiger_workflowtask_queue
							ON com_vtiger_workflowtasks.task_id= com_vtiger_workflowtask_queue.task_id
							WHERE workflow_id=? AND entity_id=?",
							array($this->id,$recordId));

			if($adb->num_rows($result)===0 && $adb->num_rows($result2)===0) { // Workflow not done for specified record
				return false;
			} else {
				return true;
			}
		}

		function markAsCompletedForRecord($recordId) {
			global $adb;

			$adb->pquery("INSERT INTO com_vtiger_workflow_activatedonce (entity_id, workflow_id)
				VALUES (?,?)", array($recordId, $this->id));
		}

		function performTasks($entityData) {
			global $adb;
			$data = $entityData->getData();

			require_once('modules/com_vtiger_workflow/VTTaskManager.inc');
			require_once('modules/com_vtiger_workflow/VTTaskQueue.inc');

			$tm = new VTTaskManager($adb);
			$taskQueue = new VTTaskQueue($adb);
			$tasks = $tm->getTasksForWorkflow($this->id);

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

		function executionConditionAsLabel($label=null){
			if($label==null){
				$arr = array('ON_FIRST_SAVE', 'ONCE', 'ON_EVERY_SAVE', 'ON_MODIFY', '', '', 'MANUAL');
				return $arr[$this->executionCondition-1];
			}else{
				$arr = array('ON_FIRST_SAVE'=>1, 'ONCE'=>2, 'ON_EVERY_SAVE'=>3, 'ON_MODIFY'=>4, 'MANUAL'=>7);
				$this->executionCondition = $arr[$label];
			}
		}
	}
?>