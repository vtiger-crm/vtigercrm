<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');

class VTUpdateFieldsTask extends VTTask {

	public $executeImmediately = true;

	public function getFieldNames() {
		return array('field_value_mapping');
	}

	public function doTask($entity) {
		global $adb, $current_user, $default_charset;

		$util = new VTWorkflowUtils();
		$util->adminUser();

		$moduleName = $entity->getModuleName();
		$entityId = $entity->getId();
		$recordId = vtws_getIdComponents($entityId);
		$recordId = $recordId[1];

		$moduleHandler = vtws_getModuleHandlerFromName($moduleName, $current_user);
		$handlerMeta = $moduleHandler->getMeta();
		$moduleFields = $handlerMeta->getModuleFields();

		$fieldValueMapping = array();
		if (!empty($this->field_value_mapping)) {
			$fieldValueMapping = Zend_Json::decode($this->field_value_mapping);
		}

		if (!empty($fieldValueMapping) && count($fieldValueMapping) > 0) {
			require_once('data/CRMEntity.php');
			$focus = CRMEntity::getInstance($moduleName);
			$focus->id = $recordId;
			$focus->mode = 'edit';
			$focus->retrieve_entity_info($recordId, $moduleName);
			$focus->clearSingletonSaveFields();

			$util->loggedInUser();
			foreach ($fieldValueMapping as $fieldInfo) {
				$fieldName = $fieldInfo['fieldname'];
				$fieldType = '';
				$fieldValueType = $fieldInfo['valuetype'];
				$fieldValue = trim($fieldInfo['value']);

				$fieldInstance = $moduleFields[$fieldName];

				if ($fieldValueType == 'fieldname') {
					$fieldValue = $focus->column_fields[$fieldValue];
				} elseif ($fieldValueType == 'expression') {
					require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';

					$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($fieldValue)));
					$expression = $parser->expression();
					$exprEvaluater = new VTFieldExpressionEvaluater($expression);
					$fieldValue = $exprEvaluater->evaluate($entity);
				} else {
					if (preg_match('/([^:]+):boolean$/', $fieldValue, $match)) {
						$fieldValue = $match[1];
						if ($fieldValue == 'true') {
							$fieldValue = '1';
						} else {
							$fieldValue = '0';
						}
					}
					if ($fieldInstance->getFieldDataType() === 'date') {
						$date = new DateTimeField($fieldValue);
						$fieldValue = $date->getDisplayDate();
					}
				}

				if ($fieldInstance->getFieldDataType() === 'owner') {
					$userId = getUserId_Ol($fieldValue);
					$groupId = getGrpId($fieldValue);

					if ($userId == 0 && $groupId == 0) {
						$fieldValue = $focus->column_fields[$fieldName];
					} else {
						$fieldValue = ($userId == 0) ? $groupId : $userId;
					}
				}

				$focus->column_fields[$fieldName] = $fieldValue;
			}

			foreach ($focus->column_fields as $fieldName => $fieldValue) {
				$focus->column_fields[$fieldName] = html_entity_decode($fieldValue, ENT_QUOTES, $default_charset);
			}
			$focus->saveentity($moduleName);
			$util->revertUser();
		}
		$util->revertUser();
	}

}

?>