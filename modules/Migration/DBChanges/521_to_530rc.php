<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

require_once 'include/utils/utils.php';

//5.2.1 to 5.3.0RC database changes

$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.2.1 to 5.3.0RC -------- Starts \n\n");

create_tab_data_file();

// Take away the ability to disable entity name fields
$sql = "SELECT tabid, modulename, fieldname, tablename FROM vtiger_entityname;";
$params = array();
$result = $adb->pquery($sql, $params);
$it = new SqlResultIterator($adb, $result);
foreach ($it as $row) {
	$tabId = $row->tabid;
	$column = "'$row->fieldname'";
	$columnArray = explode(',', $column);
	$tableName = $row->tablename;
	$sql = "UPDATE vtiger_field,vtiger_def_org_field
					SET presence=0,
						vtiger_def_org_field.visible=0
					WHERE vtiger_field.tabid=$tabId and columnname in "."(".implode(',',$columnArray).")
						AND tablename='$tableName' AND vtiger_field.fieldid=vtiger_def_org_field.fieldid";
	ExecuteQuery($sql);
}

// Adding email field type to vtiger_ws_fieldtype
function vt530_addEmailFieldTypeInWs(){
	$db = PearDatabase::getInstance();
	$checkQuery = "SELECT * FROM vtiger_ws_fieldtype WHERE fieldtype=?";
	$params = array ("email");
	$checkResult = $db->pquery($checkQuery,$params);
	if($db->num_rows($checkResult) <= 0) {
		$fieldTypeId = $db->getUniqueID('vtiger_ws_fieldtype');
		$sql = "INSERT INTO vtiger_ws_fieldtype(uitype,fieldtype) VALUES ('13','email')";
		ExecuteQuery($sql);
	}
}

function vt530_addFilterToListTypes() {
	$db = PearDatabase::getInstance();
	$query = "SELECT operationid FROM vtiger_ws_operation WHERE name=?";
	$parameters = array("listtypes");
	$result = $db->pquery($query,$parameters);
	if($db->num_rows($result) > 0){
		$operationId = $db->query_result($result,0,'operationid');
		$operationName = 'fieldTypeList';
		$checkQuery = 'SELECT 1 FROM vtiger_ws_operation_parameters where operationid=? and name=?';
		$operationResult = $db->pquery($checkQuery,array($operationId,$operationName));
		if($db->num_rows($operationResult) <=0 ){
			$status = vtws_addWebserviceOperationParam($operationId,$operationName,
						'Encoded',0);
			if($status === false){
				echo 'FAILED TO SETUP listypes WEBSERVICE HALFWAY THOURGH';
			}
		}
	}
}

function vt530_registerVTEntityDeltaApi() {
	$db = PearDatabase::getInstance();

	$em = new VTEventsManager($db);
	$em->registerHandler('vtiger.entity.beforesave', 'data/VTEntityDelta.php', 'VTEntityDelta');
	$em->registerHandler('vtiger.entity.aftersave', 'data/VTEntityDelta.php', 'VTEntityDelta');
}

function vt530_addDependencyColumnToEventHandler() {
	$db = PearDatabase::getInstance();
	$columnNames = $db->getColumnNames('vtiger_eventhandlers');
	if(!in_array('dependent_on',$columnNames)){
		ExecuteQuery("ALTER TABLE vtiger_eventhandlers ADD COLUMN dependent_on VARCHAR(255) NOT NULL DEFAULT '[]'");
	}
}

function vt530_addDepedencyToVTWorkflowEventHandler(){
	$db = PearDatabase::getInstance();

	$dependentEventHandlers = array('VTEntityDelta');
	$dependentEventHandlersJson = Zend_Json::encode($dependentEventHandlers);
	ExecuteQuery("UPDATE vtiger_eventhandlers SET dependent_on='$dependentEventHandlersJson'
								WHERE event_name='vtiger.entity.aftersave' AND handler_class='VTWorkflowEventHandler'");
}

vt530_addEmailFieldTypeInWs();
vt530_addFilterToListTypes();

vt530_addDependencyColumnToEventHandler();
vt530_registerVTEntityDeltaApi();
vt530_addDepedencyToVTWorkflowEventHandler();

// Workflow changes
if(!in_array('type', $adb->getColumnNames('com_vtiger_workflows'))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD COLUMN type VARCHAR(255) DEFAULT 'basic'");
}

// Read-Only configuration for fields at Profile level
ExecuteQuery("UPDATE vtiger_def_org_field SET readonly=0");
ExecuteQuery("UPDATE vtiger_profile2field SET readonly=0");

// Modify selected column to enable support for setting default values for fields
ExecuteQuery("ALTER TABLE vtiger_field CHANGE COLUMN selected defaultvalue TEXT default ''");
ExecuteQuery("UPDATE vtiger_field SET defaultvalue='' WHERE defaultvalue='0'");

// Scheduled Reports (Email)
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_scheduled_reports(reportid INT, recipients TEXT, schedule TEXT,
									format VARCHAR(10), next_trigger_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(reportid))
				ENGINE=InnoDB DEFAULT CHARSET=utf8;");


// Change Display of User Name from user_name to lastname firstname.
$usersQuery = "SELECT * FROM vtiger_users";
$usersResult = $adb->query($usersQuery);
$usersCount = $adb->num_rows($usersResult);
for($i=0;$i<$usersCount;++$i){
	$userId = $adb->query_result($usersResult,$i,'id');
	$userName = $adb->query_result($usersResult,$i,'user_name');
	$fullName = getFullNameFromQResult($usersResult, $i, 'Users');

	ExecutePQuery("UPDATE vtiger_cvadvfilter SET value=? WHERE columnname LIKE '%:assigned_user_id:%' AND value=?", array($fullName, $userName));
	ExecutePQuery("UPDATE vtiger_cvadvfilter SET value=? WHERE columnname LIKE '%:assigned_user_id1:%' AND value=?", array($fullName, $userName));
	ExecutePQuery("UPDATE vtiger_relcriteria SET value=? WHERE columnname LIKE 'vtiger_users%:user_name%' AND value=?", array($fullName, $userName));

	ExecutePQuery("UPDATE vtiger_cvadvfilter SET comparator='c'
						WHERE (columnname LIKE '%:assigned_user_id%:' OR columnname LIKE '%:assigned_user_id1%:' OR columnname LIKE '%:modifiedby%:')
								AND (comparator='s' OR comparator='ew')", array());
}

// Rename Yahoo Id field to Secondary Email field
function vt530_renameField($fieldInfo){
	global $adb;
	$moduleName = $fieldInfo['moduleName'];
	$tableName = $fieldInfo['tableName'];
	$fieldName = $fieldInfo['fieldName'];
	$fieldLabel = $fieldInfo['fieldLabel'];
	$fieldColumnName = $fieldInfo['columnName'];
	$newFieldName = $fieldInfo['newFieldName'];
	$newFieldLabel = $fieldInfo['newFieldLabel'];
	$newColumnName = $fieldInfo['newColumnName'];
	$columnType = $fieldInfo['columnType'];
	$tabId = getTabid($moduleName);

	ExecuteQuery("UPDATE vtiger_field SET fieldlabel='$newFieldLabel' WHERE fieldlabel='$fieldLabel' AND tabid=$tabId");
	ExecuteQuery("UPDATE vtiger_field SET fieldname='$newFieldName' WHERE fieldname='$fieldName' AND tabid=$tabId");
	ExecuteQuery("UPDATE vtiger_field SET columnname='$newColumnName' WHERE columnname='$fieldColumnName' AND tabid=$tabId");
	ExecuteQuery("ALTER TABLE $tableName CHANGE $fieldColumnName $newColumnName $columnType");

	$searchColumn= $tableName.':'.$fieldName;

	$filter_sql = 'SELECT * FROM vtiger_cvcolumnlist WHERE columnname LIKE ?';
	$res 	 = $adb->pquery($filter_sql,array("%$searchColumn%"));
	$count   = $adb->num_rows($res);
	for($k=0;$k<$count;$k++){
		 $columnName     = $adb->query_result($res,$k,'columnname');
		 $id             = $adb->query_result($res,$k,'cvid');
		 $column_index   = $adb->query_result($res,$k,'columnindex');
		 $pattern_new    = "/$fieldName/";
		 preg_match($pattern_new,$columnName,$matches);
		 if(!empty($matches)){
			 $transformedFieldLabel = str_replace(' ','_',$fieldLabel);
			 $transformedNewFieldLabel = str_replace(' ','_',$fieldLabel);
			 $newColumnName = preg_replace($pattern_new,$newFieldName,$columnName);
			 $newColumnName =  str_replace($module.'_'.$transformedFieldLabel,$module.'_'.$transformedNewFieldLabel,$newColumnName);
			 ExecuteQuery("UPDATE vtiger_cvcolumnlist SET  columnname = '$newColumnName' WHERE cvid = $id AND columnindex = $column_index");
		 }
	}
	$adv_sql = 'SELECT * FROM vtiger_cvadvfilter WHERE columnname LIKE ?';
	$res 	 = $adb->pquery($adv_sql,array("%$searchColumn%"));
	$count   = $adb->num_rows($res);
	for($v=0;$v<$count;$v++){
		 $adv_columnname     = $adb->query_result($res,$v,'columnname');
		 $cvid           	 = $adb->query_result($res,$v,'cvid');
		 $column_index_adv	 = $adb->query_result($res,$v,'columnindex');
		 $pattern_new    	 = "/$fieldName/";
		 preg_match($pattern_new,$adv_columnname,$adv_matches);
		 if(!empty($adv_matches)){
			 $transformedFieldLabel = str_replace(' ','_',$fieldLabel);
			 $transformedNewFieldLabel = str_replace(' ','_',$fieldLabel);
			 $newColumnName  = preg_replace($pattern_new,$newFieldName,$adv_columnname);
			 $newColumnName =  str_replace($module.'_'.$transformedFieldLabel,$module.'_'.$transformedNewFieldLabel,$newColumnName);
			 ExecuteQuery("UPDATE vtiger_cvadvfilter SET  columnname = '$newColumnName' WHERE cvid = $cvid AND columnindex = $column_index_adv");
		 }
	}
	$report_sql = 'SELECT * FROM vtiger_relcriteria WHERE columnname LIKE ?';
	$report_res = $adb->pquery($report_sql,array("%$searchColumn%"));
	$count   = $adb->num_rows($report_res);
	for($l=0;$l<$count;$l++){
		 $adv_columnname     = $adb->query_result($report_res,$l,'columnname');
		 $queryid            = $adb->query_result($report_res,$l,'queryid');
		 $column_index_adv   = $adb->query_result($report_res,$l,'columnindex');
		 $pattern_new    	 = "/$fieldName/";
		 preg_match($pattern_new,$adv_columnname,$adv_matches);
		 if(!empty($adv_matches)){
			 $transformedFieldLabel = str_replace(' ','_',$fieldLabel);
			 $transformedNewFieldLabel = str_replace(' ','_',$fieldLabel);
			 $newColumnName  = preg_replace($pattern_new,$newFieldName,$adv_columnname);
			 $newColumnName =  str_replace($module.'_'.$transformedFieldLabel,$module.'_'.$transformedNewFieldLabel,$newColumnName);
			 ExecuteQuery("UPDATE vtiger_relcriteria SET  columnname = '$newColumnName' WHERE queryid = $queryid");
		 }
	}

	$report_sql = 'SELECT * FROM vtiger_reportsortcol WHERE columnname LIKE ?';
	$report_res = $adb->pquery($report_sql,array("%$searchColumn%"));
	$count   = $adb->num_rows($report_res);
	for($e=0;$e<$count;$e++){
		 $adv_columnname     = $adb->query_result($report_res,$e,'columnname');
		 $sortcolid          = $adb->query_result($report_res,$e,'sortcolid');
		 $report_id          = $adb->query_result($report_res,$e,'reportid');
		 $pattern_new    	 = "/$fieldName/";
		 preg_match($pattern_new,$adv_columnname,$adv_matches);
		 if(!empty($adv_matches)){
			 $transformedFieldLabel = str_replace(' ','_',$fieldLabel);
			 $transformedNewFieldLabel = str_replace(' ','_',$fieldLabel);
			 $newColumnName  = preg_replace($pattern_new,$newFieldName,$adv_columnname);
			 $newColumnName =  str_replace($module.'_'.$transformedFieldLabel,$module.'_'.$transformedNewFieldLabel,$newColumnName);
			 ExecuteQuery("UPDATE vtiger_reportsortcol SET  columnname = '$newColumnName' WHERE sortcolid = $sortcolid AND reportid = $report_id");
		 }
	}

	$report_sql = 'SELECT * FROM vtiger_reportsummary WHERE columnname LIKE ?';
	$report_sum_res 	 = $adb->pquery($report_sql,array("%$searchColumn%"));
	$count   = $adb->num_rows($report_sum_res);
	for($z=0;$z<$count;$z++){
		 $adv_columnname     = $adb->query_result($report_sum_res,$z,'columnname');
		 $rsid               = $adb->query_result($report_sum_res,$z,'reportsummaryid');
		 $summarytype        = $adb->query_result($report_sum_res,$z,'summarytype');
		 $pattern_new    	 = "/$fieldName/";
		 preg_match($pattern_new,$adv_columnname,$adv_matches);
		 if(!empty($adv_matches)){
			 $transformedFieldLabel = str_replace(' ','_',$fieldLabel);
			 $transformedNewFieldLabel = str_replace(' ','_',$fieldLabel);
			 $newColumnName  = preg_replace($pattern_new,$newFieldName,$adv_columnname);
			 $newColumnName =  str_replace($module.'_'.$transformedFieldLabel,$module.'_'.$transformedNewFieldLabel,$newColumnName);
			 ExecuteQuery("UPDATE vtiger_reportsummary SET columnname = '$newColumnName' WHERE reportsummaryid = $rsid AND summarytype = $summarytype");
		 }
	}
	$report_sql = 'SELECT * FROM vtiger_selectcolumn WHERE columnname LIKE ?';
	$report_sum_res 	 = $adb->pquery($report_sql,array("%$searchColumn%"));
	$count   = $adb->num_rows($report_sum_res);
	for($z=0;$z<$count;$z++){
		 $adv_columnname     = $adb->query_result($report_sum_res,$z,'columnname');
		 $queryid               = $adb->query_result($report_sum_res,$z,'queryid');
		 $columnindex        = $adb->query_result($report_sum_res,$z,'columnindex');
		 $pattern_new    	 = "/$fieldName/";
		 preg_match($pattern_new,$adv_columnname,$adv_matches);
		 if(!empty($adv_matches)){
			 $transformedFieldLabel = str_replace(' ','_',$fieldLabel);
			 $transformedNewFieldLabel = str_replace(' ','_',$fieldLabel);
			 $newColumnName  = preg_replace($pattern_new,$newFieldName,$adv_columnname);
			 $newColumnName =  str_replace($module.'_'.$transformedFieldLabel,$module.'_'.$transformedNewFieldLabel,$newColumnName);
			 ExecuteQuery("UPDATE vtiger_selectcolumn SET columnname = '$newColumnName' WHERE queryid = $queryid AND columnindex = $columnindex");
		 }
	}
}

$contactYahooFieldDetails = array('moduleName'=>'Contacts', 'tableName'=>'vtiger_contactdetails', 'columnType'=>'VARCHAR(100)',
								'fieldName'=>'yahooid', 'fieldLabel'=>'Yahoo Id', 'columnName'=>'yahooid',
								'newFieldName'=>'secondaryemail', 'newFieldLabel'=>'Secondary Email', 'newColumnName'=>'secondaryemail');
vt530_renameField($contactYahooFieldDetails);

$leadYahooFieldDetails = array('moduleName'=>'Leads', 'tableName'=>'vtiger_leaddetails', 'columnType'=>'VARCHAR(100)',
								'fieldName'=>'yahooid', 'fieldLabel'=>'Yahoo Id', 'columnName'=>'yahooid',
								'newFieldName'=>'secondaryemail', 'newFieldLabel'=>'Secondary Email', 'newColumnName'=>'secondaryemail');
vt530_renameField($leadYahooFieldDetails);

$userYahooFieldDetails = array('moduleName'=>'Users', 'tableName'=>'vtiger_users', 'columnType'=>'VARCHAR(100)',
								'fieldName'=>'yahoo_id', 'fieldLabel'=>'Yahoo id', 'columnName'=>'yahoo_id',
								'newFieldName'=>'secondaryemail', 'newFieldLabel'=>'Secondary Email', 'newColumnName'=>'secondaryemail');
vt530_renameField($userYahooFieldDetails);


// Adding Organization ID column
$sql = 'ALTER TABLE vtiger_organizationdetails ADD UNIQUE KEY(organizationname);';
ExecuteQuery($sql);

$sql = 'ALTER TABLE vtiger_organizationdetails DROP PRIMARY KEY;';
ExecuteQuery($sql);

$sql = 'ALTER TABLE vtiger_organizationdetails ADD COLUMN organization_id INT(11) PRIMARY KEY';
ExecuteQuery($sql);

$result = $adb->pquery('SELECT organizationname FROM vtiger_organizationdetails', array());
$noOfCompanies = $adb->num_rows($result);
if($noOfCompanies > 0) {
	for($i=0; $i<$noOfCompanies; ++$i) {
		$id = $adb->getUniqueID('vtiger_organizationdetails');
		$organizationName = $adb->query_result($result, $i, 'organizationname');
		ExecuteQuery("UPDATE vtiger_organizationdetails SET organization_id=$id WHERE organizationname='$organizationName'");
	}
} else {
	$id = $adb->getUniqueID('vtiger_organizationdetails');
}

$sql = 'UPDATE vtiger_organizationdetails_seq SET id = (SELECT max(organization_id) FROM vtiger_organizationdetails)';
ExecuteQuery($sql);

// Add Webservice support for Company Details type of entity.
vtws_addActorTypeWebserviceEntityWithName(
		'CompanyDetails',
		'include/Webservices/VtigerCompanyDetails.php',
		'VtigerCompanyDetails',
		array('fieldNames'=>'organizationname','indexField'=>'groupid','tableName'=>'vtiger_organizationdetails'));


$sql = 'CREATE TABLE vtiger_ws_fieldinfo(id varchar(64) NOT NULL PRIMARY KEY,
										property_name VARCHAR(32),
										property_value VARCHAR(64)
										) ENGINE=Innodb DEFAULT CHARSET=utf8;';
ExecuteQuery($sql);

$id = $adb->getUniqueID('vtiger_ws_entity_fieldtype');
$sql = "INSERT INTO vtiger_ws_entity_fieldtype(fieldtypeid,table_name,field_name,fieldtype) VALUES ($id,'vtiger_organizationdetails','logoname','file')";
ExecuteQuery($sql);
$id = $adb->getUniqueID('vtiger_ws_entity_fieldtype');
$sql = "INSERT INTO vtiger_ws_entity_fieldtype(fieldtypeid,table_name,field_name,fieldtype) VALUES ($id,'vtiger_organizationdetails','phone','phone')";
ExecuteQuery($sql);
$id = $adb->getUniqueID('vtiger_ws_entity_fieldtype');
$sql = "INSERT INTO vtiger_ws_entity_fieldtype(fieldtypeid,table_name,field_name,fieldtype) VALUES ($id,'vtiger_organizationdetails','fax','phone')";
ExecuteQuery($sql);
$id = $adb->getUniqueID('vtiger_ws_entity_fieldtype');
$sql = "INSERT INTO vtiger_ws_entity_fieldtype(fieldtypeid,table_name,field_name,fieldtype) VALUES ($id,'vtiger_organizationdetails','website','url')";
ExecuteQuery($sql);

$sql="INSERT INTO vtiger_ws_fieldinfo(id,property_name,property_value) VALUES ('vtiger_organizationdetails.organization_id','upload.path','1')";
ExecuteQuery($sql);

$webserviceObject = VtigerWebserviceObject::fromName($adb, 'CompanyDetails');
$webserviceEntityId = $webserviceObject->getEntityId();
$sql = "INSERT INTO vtiger_ws_entity_tables(webservice_entity_id,table_name) VALUES ($webserviceEntityId,'vtiger_organizationdetails')";
ExecuteQuery($sql);

// Increase the size of User Singature field
ExecuteQuery("ALTER TABLE vtiger_users CHANGE signature signature varchar(1000)");

// New Currencies added
function vt530_updateCurrencyInfo() {
	global $adb;
	include('modules/Utilities/Currencies.php');

	$adb->pquery("DELETE FROM vtiger_currencies;", array());
	$adb->pquery('UPDATE vtiger_currencies_seq SET id=1;', array());
	foreach ($currencies as $key => $value) {
		$adb->pquery("INSERT INTO vtiger_currencies VALUES (?,?,?,?)",
						array($adb->getUniqueID("vtiger_currencies"), $key, $value[0], $value[1]));
	}
	$cur_result = $adb->pquery("SELECT * from vtiger_currency_info", array());
	for ($i = 0; $i < $adb->num_rows($cur_result); $i++) {
		$cur_symbol = $adb->query_result($cur_result, $i, "currency_symbol");
		$cur_code = $adb->query_result($cur_result, $i, "currency_code");
		$cur_name = $adb->query_result($cur_result, $i, "currency_name");
		$cur_id = $adb->query_result($cur_result, $i, "id");
		$currency_exists = $adb->pquery("SELECT * from vtiger_currencies WHERE currency_code=?",
				array($cur_code));
		if ($adb->num_rows($currency_exists) > 0) {
			$currency_name = $adb->query_result($currency_exists, 0, "currency_name");
			ExecuteQuery("UPDATE vtiger_currency_info SET vtiger_currency_info.currency_name='$currency_name' WHERE id=$cur_id");
		} else {
			$currencyId = $adb->getUniqueID("vtiger_currencies");
			ExecuteQuery("INSERT INTO vtiger_currencies VALUES ($currencyId, '$cur_name', '$cur_code', '$cur_symbol')");
		}
	}
}
vt530_updateCurrencyInfo();

// Change Password & Delete User Webservice apis
$operationMeta = array(
	"changePassword"=>array(
		"include"=>array(
			"include/Webservices/ChangePassword.php"
		),
		"handler"=>"vtws_changePassword",
		"params"=>array(
			"id"=>"String",
			"oldPassword"=>"String",
			"newPassword"=>"String",
			'confirmPassword' => 'String'
		),
		"prelogin"=>0,
		"type"=>"POST"
	),
	"deleteUser"=>array(
		"include"=>array(
			"include/Webservices/DeleteUser.php"
		),
		"handler"=>"vtws_deleteUser",
		"params"=>array(
			"id"=>"String",
			"newOwnerId"=>"String"
		),
		"prelogin"=>0,
		"type"=>"POST"
	)
);

foreach ($operationMeta as $operationName => $operationDetails) {
	$operationId = vtws_addWebserviceOperation($operationName,
												$operationDetails['include'],
												$operationDetails['handler'],
												$operationDetails['type'],
												$operationDetails['prelogin']);
	$params = $operationDetails['params'];
	$sequence = 1;
	foreach ($params as $paramName => $paramType) {
		vtws_addWebserviceOperationParam($operationId, $paramName, $paramType, $sequence++);
	}
}

$usersModuleInstance = Vtiger_Module::getInstance('Users');
$blockInstance = Vtiger_Block::getInstance('LBL_MORE_INFORMATION', $usersModuleInstance);

$fieldInstance = new Vtiger_Field();
$fieldInstance->name = 'theme';
$fieldInstance->label = 'Theme';
$fieldInstance->table = 'vtiger_users';
$fieldInstance->column = 'theme';
$fieldInstance->columntype = 'VARCHAR(100)';
$fieldInstance->uitype = 31;
$blockInstance->addField($fieldInstance);

$fieldInstance = new Vtiger_Field();
$fieldInstance->name = 'language';
$fieldInstance->label = 'Language';
$fieldInstance->table = 'vtiger_users';
$fieldInstance->column = 'language';
$fieldInstance->columntype = 'VARCHAR(36)';
$fieldInstance->uitype = 32;
$blockInstance->addField($fieldInstance);

/* Advanced filter ehancement for Custom Filter and Advanced Search */
// Alter vtiger_cvadvfilter table to store groupid and column_condition
ExecuteQuery("ALTER TABLE vtiger_cvadvfilter ADD COLUMN groupid INT DEFAULT 1");
ExecuteQuery("ALTER TABLE vtiger_cvadvfilter ADD COLUMN column_condition VARCHAR(255) DEFAULT 'and'");

// Create table to store Custom Views Advanced Filters Condition Grouping information
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_cvadvfilter_grouping
		(groupid INT NOT NULL, cvid INT, group_condition VARCHAR(255), condition_expression TEXT, PRIMARY KEY(groupid, cvid))
		 ENGINE=Innodb DEFAULT CHARSET=utf8;");

// Migration queries to migrate existing data to the required state (Storing Condition Expression in the newly created table for existing filters)
// Remove all unwanted condition columns added (where column name is empty)
$adb->query("DELETE FROM vtiger_cvadvfilter WHERE (columnname IS NULL OR trim(columnname) = '')");
$maxCvIdResult = $adb->query("SELECT max(cvid) as max_cvid FROM vtiger_customview");
if($adb->num_rows($maxCvIdResult) > 0) {
	$maxCvId = $adb->query_result($maxCvIdResult, 0, 'max_cvid');
	if(!empty($maxCvId) && $maxCvId > 0) {
		for($i=1; $i<=$maxCvId; ++$i) {
			$cvId = $i;
			$relcriteriaResult = $adb->pquery("SELECT * FROM vtiger_cvadvfilter WHERE cvid=?", array($cvId)); // Pick all the conditions of a Custom View
			$noOfConditions = $adb->num_rows($relcriteriaResult);
			if($noOfConditions > 0) {
				$columnIndexArray = array();
				$maxColumnIndex = 0;
				for($j=0;$j<$noOfConditions; $j++) {
					$columnIndex = $adb->query_result($relcriteriaResult, $j, 'columnindex');
					if($maxColumnIndex < $columnIndex) {
						$maxColumnIndex = $columnIndex;
					}
					$columnIndexArray[] = $columnIndex;
				}
				$conditionExpression = implode(' and ', $columnIndexArray);
				ExecuteQuery("INSERT INTO vtiger_cvadvfilter_grouping VALUES(1, $cvId, '', $conditionExpression)");

				ExecuteQuery("UPDATE vtiger_cvadvfilter SET column_condition='' WHERE columnindex=$maxColumnIndex AND cvid=$cvId");
			}
		}
	}
}
/* Advanced filter ehancement for Custom Filter and Advanced Search -- ENDS HERE */

$salesOrderTabId = getTabid('SalesOrder');
ExecuteQuery("UPDATE vtiger_field SET displaytype=1 WHERE tabid=$salesOrderTabId AND (fieldname = 'bill_country' OR fieldname = 'ship_country')");

$quotesTabId = getTabid('Quotes');
ExecuteQuery("UPDATE vtiger_field SET presence = 2 WHERE tabid=$quotesTabId AND fieldname = 'ship_pobox'");

/* Dependent Picklists feature */
ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_picklist_dependency (
					id INT NOT NULL PRIMARY KEY, tabid INT NOT NULL,
					sourcefield VARCHAR(255), targetfield VARCHAR(255),
					sourcevalue VARCHAR(100), targetvalues TEXT, criteria TEXT)
					ENGINE=Innodb DEFAULT CHARSET=utf8;");

$studioBlockRes = $adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label = ?", array('LBL_STUDIO'));
if($adb->num_rows($studioBlockRes) > 0) {
	$blockId = $adb->query_result($studioBlockRes, 0, 'blockid');
	$maxSequenceRes = $adb->pquery("SELECT MAX(sequence) as maxsequence FROM vtiger_settings_field WHERE blockid = ?", array($blockId));
	if($adb->num_rows($maxSequenceRes) > 0){
		$maxSequence = $adb->query_result($maxSequenceRes, 0, 'maxsequence');
		$nextSequence = $maxSequence + 1;
		$settingsFieldId = $adb->getUniqueID('vtiger_settings_field');
		ExecuteQuery("INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence, active)
							VALUES($settingsFieldId, $blockId, 'LBL_PICKLIST_DEPENDENCY_SETUP', 'picklistdependency.gif',
					'LBL_PICKLIST_DEPENDENCY_DESCRIPTION', 'index.php?module=PickList&action=PickListDependencySetup&parenttab=Settings',
					$nextSequence, 0)");
	}
}

// Need to extend the password field size, as the encrypted password, generated by PHP5.3 MD5, is exceeding 30 characters
$query = 'ALTER TABLE vtiger_users MODIFY COLUMN user_password VARCHAR(200)';
ExecuteQuery($query);
$query = 'ALTER TABLE vtiger_users MODIFY COLUMN confirm_password VARCHAR(200)';
ExecuteQuery($query);

function vt530_changeDataType($tableName, $columnName, $fieldName, $dataType, $typeOfData) {
	global $adb;

	$query = "UPDATE vtiger_field SET typeofdata='$typeOfData' WHERE tablename='$tableName' AND fieldname='$fieldName'";
	ExecuteQuery($query);

	$filterSql = 'SELECT cvid, columnname FROM vtiger_cvcolumnlist WHERE columnname LIKE ?';
	$params = array("$tableName:$columnName:$fieldName:%:%");
	$result = $adb->pquery($filterSql, $params);
	$noOfRows = $adb->num_rows($result);
	for($i=0; $i<$noOfRows; ++$i) {
		$cvId = $adb->query_result($result, $i, 'cvid');
		$columnName = $adb->query_result($result, $i, 'columnname');
		$columnNameParts = explode(':', $columnName);
		$length = count($columnNameParts);
		$columnNameParts[$length-1] = $dataType;
		$newColumnName = implode(':', $columnNameParts);
		ExecuteQuery("UPDATE vtiger_cvcolumnlist SET columnname='$newColumnName' WHERE cvid=$cvId AND columnname='$columnName'");
	}

	$advSql = 'SELECT cvid, columnname FROM vtiger_cvadvfilter WHERE columnname LIKE ?';
	$params = array("$tableName:$columnName:$fieldName:%:%");
	$result = $adb->pquery($advSql, $params);
	$noOfRows = $adb->num_rows($result);
	for($i=0; $i<$noOfRows; ++$i) {
		$cvId = $adb->query_result($result, $i, 'cvid');
		$columnName = $adb->query_result($result, $i, 'columnname');
		$columnNameParts = explode(':', $columnName);
		$length = count($columnNameParts);
		$columnNameParts[$length-1] = $dataType;
		$newColumnName = implode(':', $columnNameParts);
		ExecuteQuery("UPDATE vtiger_cvadvfilter SET columnname='$newColumnName' WHERE cvid=$cvId AND columnname='$columnName'");
	}

	$reportSql = 'SELECT queryid, columnname FROM vtiger_relcriteria WHERE columnname LIKE ?';
	$params = array("%:$columnName:%:$fieldName:%");
	$result = $adb->pquery($reportSql, $params);
	$noOfRows = $adb->num_rows($result);
	for($i=0; $i<$noOfRows; ++$i) {
		$queryId = $adb->query_result($result, $i, 'queryid');
		$columnName = $adb->query_result($result, $i, 'columnname');
		$columnNameParts = explode(':', $columnName);
		$length = count($columnNameParts);
		$columnNameParts[$length-1] = $dataType;
		$newColumnName = implode(':', $columnNameParts);
		ExecuteQuery("UPDATE vtiger_relcriteria SET columnname='$newColumnName' WHERE queryid=$queryId AND columnname='$columnName'");
	}

	$reportSql = 'SELECT reportid, columnname FROM vtiger_reportsortcol WHERE columnname LIKE ?';
	$params = array("%:$columnName:%:$fieldName:%");
	$result = $adb->pquery($reportSql, $params);
	$noOfRows = $adb->num_rows($result);
	for($i=0; $i<$noOfRows; ++$i) {
		$queryId = $adb->query_result($result, $i, 'reportid');
		$columnName = $adb->query_result($result, $i, 'columnname');
		$columnNameParts = explode(':', $columnName);
		$length = count($columnNameParts);
		$columnNameParts[$length-1] = $dataType;
		$newColumnName = implode(':', $columnNameParts);
		ExecuteQuery("UPDATE vtiger_reportsortcol SET columnname='$newColumnName' WHERE queryid=$queryId AND columnname='$columnName'");
	}

	$reportSql = 'SELECT queryid, columnname FROM vtiger_selectcolumn WHERE columnname LIKE ?';
	$params = array("%:$columnName:%:$fieldName:%");
	$result = $adb->pquery($reportSql, $params);
	$noOfRows = $adb->num_rows($result);
	for($i=0; $i<$noOfRows; ++$i) {
		$queryId = $adb->query_result($result, $i, 'queryid');
		$columnName = $adb->query_result($result, $i, 'columnname');
		$columnNameParts = explode(':', $columnName);
		$length = count($columnNameParts);
		$columnNameParts[$length-1] = $dataType;
		$newColumnName = implode(':', $columnNameParts);
		ExecuteQuery("UPDATE vtiger_selectcolumn SET columnname='$newColumnName' WHERE queryid=$queryId AND columnname='$columnName'");
	}
}

$moduleInstance = Vtiger_Module::getInstance('Users');
$block = Vtiger_Block::getInstance('LBL_MORE_INFORMATION', $moduleInstance);

$timezone_field = new Vtiger_Field();
$timezone_field->name = 'time_zone';
$timezone_field->label = 'Time Zone';
$timezone_field->table ='vtiger_users';
$timezone_field->column = 'time_zone';
$timezone_field->columntype = 'varchar(200)';
$timezone_field->typeofdata = 'V~O';
$timezone_field->uitype = 16;
$block->addField($timezone_field);

$usertimezonesClass = new UserTimeZones();
$arrayOfSupportedTimeZones = $usertimezonesClass->userTimeZones();
$timezone_field->setPicklistValues($arrayOfSupportedTimeZones);

$timeZone = DateTimeField::getDBTimeZone();
$sql = "UPDATE vtiger_users SET time_zone='$timeZone'";
ExecuteQuery($sql);

$calendarTabId = getTabid('Calendar');
$eventsTabId = getTabid('Events');
ExecuteQuery("UPDATE vtiger_field SET quickcreate=0 WHERE fieldname='time_start' AND (tabid=$calendarTabId OR tabid=$eventsTabId)");

vt530_changeDataType('vtiger_crmentity', 'createdtime', 'createdtime', 'DT', 'DT~O');
vt530_changeDataType('vtiger_crmentity', 'modifiedtime', 'modifiedtime', 'DT', 'DT~O');

$moduleInstance = Vtiger_Module::getInstance('Users');

// Update/Increment the sequence for the succeeding blocks of Users module, with starting sequence 2
$usersTabId = getTabid('Users');
$blocksListResult = ExecuteQuery("UPDATE vtiger_blocks SET sequence = sequence+1 WHERE tabid=$usersTabId AND sequence >= 2");

// Create Currency Configuration block placing at position 2
$currencyBlock = new Vtiger_Block();
$currencyBlock->label = 'LBL_CURRENCY_CONFIGURATION';
$currencyBlock->sequence = 2;
$moduleInstance->addBlock($currencyBlock);

$currencyBlock = Vtiger_Block::getInstance('LBL_CURRENCY_CONFIGURATION', $moduleInstance);

$currencyPattern = new Vtiger_Field();
$currencyPattern->name = 'currency_grouping_pattern';
$currencyPattern->label = 'Digit Grouping Pattern';
$currencyPattern->table ='vtiger_users';
$currencyPattern->column = 'currency_grouping_pattern';
$currencyPattern->columntype = 'varchar(100)';
$currencyPattern->typeofdata = 'V~O';
$currencyPattern->uitype = 16;
$currencyPattern->defaultvalue = '123,456,789';
$currencyPattern->sequence = 2;
$currencyPattern->helpinfo = "<b>Currency - Digit Grouping Pattern</b> <br/><br/>".
								"This pattern specifies the format in which the currency separator will be placed.";
$currencyBlock->addField($currencyPattern);
$currencyPattern->setPicklistValues(array('123,456,789','123456789','123456,789','12,34,56,789'));

$currencyDecimalSeparator = new Vtiger_Field();
$currencyDecimalSeparator->name = 'currency_decimal_separator';
$currencyDecimalSeparator->label = 'Decimal Separator';
$currencyDecimalSeparator->table ='vtiger_users';
$currencyDecimalSeparator->column = 'currency_decimal_separator';
$currencyDecimalSeparator->columntype = 'varchar(2)';
$currencyDecimalSeparator->typeofdata = 'V~O';
$currencyDecimalSeparator->uitype = 16;
$currencyDecimalSeparator->defaultvalue = '.';
$currencyDecimalSeparator->sequence = 3;
$currencyDecimalSeparator->helpinfo = "<b>Currency - Decimal Separator</b> <br/><br/>".
										"Decimal separator specifies the separator to be used to separate ".
										"the fractional values from the whole number part. <br/>".
										"<b>Eg:</b> <br/>".
										". => 123.45 <br/>".
										", => 123,45 <br/>".
										"' => 123'45 <br/>".
										"  => 123 45 <br/>".
										"$ => 123$45 <br/>";
$currencyBlock->addField($currencyDecimalSeparator);
$currencyDecimalSeparator->setPicklistValues(array(".",",","'"," ","$"));

$currencyThousandSeparator = new Vtiger_Field();
$currencyThousandSeparator->name = 'currency_grouping_separator';
$currencyThousandSeparator->label = 'Digit Grouping Separator';
$currencyThousandSeparator->table ='vtiger_users';
$currencyThousandSeparator->column = 'currency_grouping_separator';
$currencyThousandSeparator->columntype = 'varchar(2)';
$currencyThousandSeparator->typeofdata = 'V~O';
$currencyThousandSeparator->uitype = 16;
$currencyThousandSeparator->defaultvalue = ',';
$currencyThousandSeparator->sequence = 4;
$currencyThousandSeparator->helpinfo = "<b>Currency - Grouping Separator</b> <br/><br/>".
										"Grouping separator specifies the separator to be used to group ".
										"the whole number part into hundreds, thousands etc. <br/>".
										"<b>Eg:</b> <br/>".
										". => 123.456.789 <br/>".
										", => 123,456,789 <br/>".
										"' => 123'456'789 <br/>".
										"  => 123 456 789 <br/>".
										"$ => 123$456$789 <br/>";
$currencyBlock->addField($currencyThousandSeparator);
$currencyThousandSeparator->setPicklistValues(array(".",",","'"," ","$"));

$currencySymbolPlacement = new Vtiger_Field();
$currencySymbolPlacement->name = 'currency_symbol_placement';
$currencySymbolPlacement->label = 'Symbol Placement';
$currencySymbolPlacement->table ='vtiger_users';
$currencySymbolPlacement->column = 'currency_symbol_placement';
$currencySymbolPlacement->columntype = 'varchar(20)';
$currencySymbolPlacement->typeofdata = 'V~O';
$currencySymbolPlacement->uitype = 16;
$currencySymbolPlacement->defaultvalue = ',';
$currencySymbolPlacement->sequence = 5;
$currencySymbolPlacement->helpinfo = "<b>Currency - Symbol Placement</b> <br/><br/>".
										"Symbol Placement allows you to configure the position of the ".
										"currency symbol with respect to the currency value.<br/>".
										"<b>Eg:</b> <br/>".
										"$1.0 => $123,456,789.50 <br/>".
										"1.0$ => 123,456,789.50$ <br/>";
$currencyBlock->addField($currencySymbolPlacement);
$currencySymbolPlacement->setPicklistValues(array("$1.0", "1.0$"));

// Update the block and the sequence for Currency field of Users module - Push it to Currency Configuration block
ExecuteQuery("UPDATE vtiger_field SET block=$currencyBlock->id, sequence=1 WHERE tablename='vtiger_users' AND fieldname='currency_id'");

ExecuteQuery("UPDATE vtiger_users SET currency_grouping_pattern='123,456,789',
										currency_decimal_separator='.',
										currency_grouping_separator=',',
										currency_symbol_placement='$1.0'");

ExecuteQuery("UPDATE vtiger_field SET uitype='71' WHERE uitype=1 AND tablename='vtiger_campaign'
							AND fieldname IN ('expectedrevenue', 'actualcost', 'expectedroi', 'actualroi', 'budgetcost')");

ExecuteQuery("UPDATE vtiger_field SET uitype='72' WHERE uitype IN ('1','71')
					AND fieldname IN ('unit_price', 'hdnGrandTotal', 'hdnSubTotal', 'txtAdjustment', 'hdnDiscountAmount', 'hdnS_H_Amount')");

$sql = "INSERT INTO vtiger_ws_fieldtype(uitype,fieldtype) VALUES ('71', 'currency')";
ExecuteQuery($sql);
$sql = "INSERT INTO vtiger_ws_fieldtype(uitype,fieldtype) VALUES ('72', 'currency')";
ExecuteQuery($sql);


installVtlibModule('ConfigEditor', "packages/vtiger/mandatory/ConfigEditor.zip");
installVtlibModule('WSAPP', "packages/vtiger/mandatory/WSAPP.zip");

updateVtlibModule('Mobile', "packages/vtiger/mandatory/Mobile.zip");
updateVtlibModule('Services', 'packages/vtiger/mandatory/Services.zip');
updateVtlibModule('ServiceContracts', 'packages/vtiger/mandatory/ServiceContracts.zip');
updateVtlibModule('PBXManager','packages/vtiger/mandatory/PBXManager.zip');

$migrationlog->debug("\n\nDB Changes from 5.2.1 to 5.3.0RC  -------- Ends \n\n");

?>