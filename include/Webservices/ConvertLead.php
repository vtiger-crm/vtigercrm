<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/

require_once 'include/Webservices/Retrieve.php';
require_once 'include/Webservices/Create.php';

function vtws_convertlead($leadId,$assignedTo,$accountName,$avoidPotential,$potential,$user) {
	global $adb,$log;
	if(empty($assignedTo)){
		$assignedTo = vtws_getWebserviceEntityId('Users',$user->id);
	}
	if(((boolean)$avoidPotential) !== true){
		try{
			if(empty($potential)){
				throw new WebServiceException(WebServiceErrorCode::$INVALID_POTENTIAL_FOR_CONVERT_LEAD,
					"Invalid lead information given for potential");
			}
		}catch(Zend_Json_Exception $e){
			throw new WebServiceException(WebServiceErrorCode::$INVALID_POTENTIAL_FOR_CONVERT_LEAD,
					"Potentail information given is not in valid JSON format");
		}
	}
	$currencyInfo=getCurrencySymbolandCRate($user->currency_id);
	$rate = $currencyInfo['rate'];
	if($potential['amount'] != ''){
		$potential['amount'] = convertToDollar($potential['amount'],$rate);
	}

	$leadObject = VtigerWebserviceObject::fromName($adb,'Leads');
	$handlerPath = $leadObject->getHandlerPath();
	$handlerClass = $leadObject->getHandlerClass();

	require_once $handlerPath;

	$leadHandler = new $handlerClass($leadObject,$user,$adb,$log);
	$leadHandler->getMeta()->retrieveMeta();

	$leadInfo = vtws_retrieve($leadId,$user);
	$sql = "select converted from vtiger_leaddetails where converted = 1 and leadid=?";
	$leadIdComponents = vtws_getIdComponents($leadId);
	$result = $adb->pquery($sql, array($leadIdComponents[1]));
	if($result === false){
		throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
			"Database error while performing required operation");
	}
	$rowCount = $adb->num_rows($result);
	if($rowCount > 0){
		throw new WebServiceException(WebServiceErrorCode::$LEAD_ALREADY_CONVERTED,
			"Lead is already converted");
	}

	$customFieldMapping = vtws_getConvertLeadFieldMapping();

	//check if accountName given in request is empty then default to lead company field.
	if(empty($accountName)){
		$accountName = $leadInfo['company'];
	}
	
	$sql = "select vtiger_account.accountid from vtiger_account
		left join vtiger_crmentity on vtiger_account.accountid = vtiger_crmentity.crmid
		where vtiger_crmentity.deleted=0 and vtiger_account.accountname = ?";
	$result = $adb->pquery($sql, array($accountName));
	if($result === false){
		throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
			"Database error while performing required operation");
	}
	$rowCount = $adb->num_rows($result);
	if($rowCount != 0 && vtlib_isModuleActive('Accounts') === true){
		$crmId = $adb->query_result($result,0,"accountid");

		$status = vtws_getRelatedNotesAttachments($leadIdComponents[1],$crmId);
		if($status === false){
			throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
				"Failed to move related Documents to the Account");
		}
		//Retrieve the lead related products and relate them with this new account
		$status = vtws_saveLeadRelatedProducts($leadIdComponents[1], $crmId, "Accounts");
		if($status === false){
			throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
				"Failed to move related Products to the Account");
		}
		$status = vtws_saveLeadRelations($leadIdComponents[1], $crmId, "Accounts");
		if($status === false){
			throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
				"Failed to move Records to the Account");
		}
	}else{
		//don't create account if no company name is given in input and lead doest not have
		// company field populated, DONE TO RESPECT B2C model.
		if(!empty($accountName)){
			$accountObject = VtigerWebserviceObject::fromName($adb,'Accounts');
			$handlerPath = $accountObject->getHandlerPath();
			$handlerClass = $accountObject->getHandlerClass();

			require_once $handlerPath;

			$accountHandler = new $handlerClass($accountObject,$user,$adb,$log);
			if($accountHandler->getMeta()->hasWriteAccess()){
				$account  = array();
				if(!empty($leadInfo["annualrevenue"])){
					$account['annual_revenue'] = $leadInfo["annualrevenue"];
				}

				if(!empty($leadInfo["noofemployees"])) {
					$account['employees'] = $leadInfo["noofemployees"];
				}
				$account['accountname'] = $accountName;
				$account['industry'] = $leadInfo["industry"];
				$account['phone'] = $leadInfo["phone"];
				$account['fax'] = $leadInfo["fax"];
				$account['rating'] = $leadInfo["rating"];
				$account['email1'] = $leadInfo["email"];
				$account['website'] = $leadInfo["website"];
				$account['bill_city'] = $leadInfo["city"];
				$account['bill_code'] = $leadInfo["code"];
				$account['bill_country'] = $leadInfo["country"];
				$account['bill_state'] = $leadInfo["state"];
				$account['bill_street'] = $leadInfo["lane"];
				$account['bill_pobox'] = $leadInfo["pobox"];
				$account['ship_city'] = $leadInfo["city"];
				$account['ship_code'] = $leadInfo["code"];
				$account['ship_country'] = $leadInfo["country"];
				$account['ship_state'] = $leadInfo["state"];
				$account['ship_street'] = $leadInfo["lane"];
				$account['ship_pobox'] = $leadInfo["pobox"];
				$account['assigned_user_id'] = $assignedTo;
				$account['description'] = $leadInfo['description'];
				$leadFields = $leadHandler->getMeta()->getModuleFields();
				$accountFields = $accountHandler->getMeta()->getModuleFields();
				foreach ($customFieldMapping as $leadFieldId=>$mappingDetails){
					$accountFieldId = $mappingDetails['Accounts'];
					if(empty($accountFieldId)){
						continue;
					}
					$accountField = vtws_getFieldfromFieldId($accountFieldId,$accountFields);
					if($accountField == null){
						//user doesn't have access so continue.TODO update even if user doesn't have access
						continue;
					}
					$leadField = vtws_getFieldfromFieldId($leadFieldId,$leadFields);
					if($leadField == null){
						//user doesn't have access so continue.TODO update even if user doesn't have access
						continue;
					}
					$leadFieldName = $leadField->getFieldName();
					$accountFieldName = $accountField->getFieldName();
					$account[$accountFieldName] = $leadInfo[$leadFieldName];
				}
				$account = vtws_create('Accounts',$account,$user);
				$accountIdComponents = vtws_getIdComponents($account['id']);
				$status = vtws_getRelatedNotesAttachments($leadIdComponents[1],$accountIdComponents[1]);
				if($status === false){
					throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
				"Failed to move related Documents to the Account");
				}
				//Retrieve the lead related products and relate them with this new account
				$status = vtws_saveLeadRelatedProducts($leadIdComponents[1], $accountIdComponents[1], "Accounts");
				if($status === false){
					throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
				"Failed to move related Products to the Account");
				}
				$status = vtws_saveLeadRelations($leadIdComponents[1], $accountIdComponents[1], "Accounts");
				if($status === false){
					throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
				"Failed to move Records to the Account");
				}
			}
		}
	}
	$contactObject = VtigerWebserviceObject::fromName($adb,'Contacts');
	$handlerPath = $contactObject->getHandlerPath();
	$handlerClass = $contactObject->getHandlerClass();

	require_once $handlerPath;

	$contactHandler = new $handlerClass($contactObject,$user,$adb,$log);
	if(!empty($crmId)){
		$accountId = $crmId;
		$webserviceAccountId = vtws_getWebserviceEntityId('Accounts',$crmId);
	}elseif(!empty($accountName)){
		if(count($accountIdComponents)===2){
			$accountId = $accountIdComponents[1];
			$webserviceAccountId = vtws_getId($accountIdComponents[0],$accountIdComponents[1]);
		}
	}else{
		$accountId = '';
		$webserviceAccountId = '';
	}
	if($contactHandler->getMeta()->hasWriteAccess()){
		$contact = array();
		$contact['assigned_user_id'] = $assignedTo;
		$contact['description'] = $leadInfo['description'];
		$contact['account_id'] = $webserviceAccountId;
		$contact['salutationtype'] = $leadInfo["salutationtype"];
		$contact['firstname'] = $leadInfo["firstname"];
		$contact['lastname'] = $leadInfo["lastname"];
		$contact['email'] = $leadInfo["email"];
		$contact['phone'] = $leadInfo["phone"];
		$contact['mobile'] = $leadInfo["mobile"];
		$contact['title'] = $leadInfo["designation"];
		$contact['fax'] = $leadInfo["fax"];
		$contact['yahooid'] = $leadInfo['yahooid'];
		$contact['leadsource'] = $leadInfo['leadsource'];
		$contact['mailingcity'] = $leadInfo["city"];
		$contact['mailingzip'] = $leadInfo["code"];
		$contact['mailingcountry'] = $leadInfo["country"];
		$contact['mailingstate'] = $leadInfo["state"];
		$contact['mailingstreet'] = $leadInfo["lane"];
		$contact['mailingpobox'] = $leadInfo["pobox"];
		$leadFields = $leadHandler->getMeta()->getModuleFields();
		$contactFields = $contactHandler->getMeta()->getModuleFields();
		foreach ($customFieldMapping as $leadFieldId=>$mappingDetails){
			$contactFieldId = $mappingDetails['Contacts'];
			if(empty($contactFieldId)){
				continue;
			}
			$contactField = vtws_getFieldfromFieldId($contactFieldId,$contactFields);
			if($contactField == null){
				//user doesn't have access so continue.TODO update even if user doesn't have access
				continue;
			}
			$leadField = vtws_getFieldfromFieldId($leadFieldId,$leadFields);
			if($leadField == null){
				//user doesn't have access so continue.TODO update even if user doesn't have access
				continue;
			}
			$leadFieldName = $leadField->getFieldName();
			$contactFieldName = $contactField->getFieldName();
			$contact[$contactFieldName] = $leadInfo[$leadFieldName];
		}
		
		$contact = vtws_create('Contacts',$contact,$user);
		$contactIdComponents = vtws_getIdComponents($contact['id']);
		$contactId = $contactIdComponents[1];

		//To convert relates Activites and Email.
		$status = vtws_getRelatedActivities($leadIdComponents[1],$accountId,$contactIdComponents[1]);
		if($status === false){
			throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
				"Failed to move Related Activities to the Contact");
		}
		$status = vtws_getRelatedNotesAttachments($leadIdComponents[1],$contactIdComponents[1]);
		if($status === false){
			throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
				"Failed to move related Documents to the Contact");
		}
		//Retrieve the lead related products and relate them with this new contact
		$status = vtws_saveLeadRelatedProducts($leadIdComponents[1], $contactIdComponents[1], "Contacts");
		if($status === false){
			throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
				"Failed to move related Products to the Contact");
		}
		$status = vtws_saveLeadRelations($leadIdComponents[1], $contactIdComponents[1], "Contacts");
		if($status === false){
			throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
				"Failed to move Records to the Contact");
		}
		//Retrieve the lead related Campaigns and relate them with this new contact --Minnie
		$status = vtws_saveLeadRelatedCampaigns($leadIdComponents[1], $contactIdComponents[1]);
		if($status === false){
			throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
				"Failed to move Related Campaigns to the Contact");
		}
	}
	if((boolean)$avoidPotential != true ){
		$potentialObject = VtigerWebserviceObject::fromName($adb,'Potentials');
		$handlerPath = $potentialObject->getHandlerPath();
		$handlerClass = $potentialObject->getHandlerClass();

		require_once $handlerPath;

		$potentialHandler = new $handlerClass($potentialObject,$user,$adb,$log);
		if($potentialHandler->getMeta()->hasWriteAccess()){
			if(!empty($webserviceAccountId)){
				$relatedTo = $webserviceAccountId;
			}else{
				if(!empty($contactId)){
					$relatedTo = vtws_getWebserviceEntityId('Contacts',$contactId);
				}
			}
			$potential['assigned_user_id'] = $assignedTo;
			$potential['description'] = $leadInfo['description'];
			$potential['related_to'] = $relatedTo;
			$potential['leadsource'] = $leadInfo['leadsource'];
			$leadFields = $leadHandler->getMeta()->getModuleFields();
			$potentialFields = $potentialHandler->getMeta()->getModuleFields();
			foreach ($customFieldMapping as $leadFieldId=>$mappingDetails){
				$potentialFieldId = $mappingDetails['Potentials'];
				if(empty($potentialFieldId)){
					continue;
				}
				$potentialField = vtws_getFieldfromFieldId($potentialFieldId,$potentialFields);
				if($potentialField == null){
					//user doesn't have access so continue.TODO update even if user doesn't have access
					continue;
				}
				$leadField = vtws_getFieldfromFieldId($leadFieldId,$leadFields);
				if($leadField == null){
					//user doesn't have access so continue.TODO update even if user doesn't have access
					continue;
				}
				$leadFieldName = $leadField->getFieldName();
				$potentialFieldName = $potentialField->getFieldName();
				$potential[$potentialFieldName] = $leadInfo[$leadFieldName];
			}
			$potential = vtws_create('Potentials',$potential,$user);
			$potentialIdComponents = vtws_getIdComponents($potential['id']);
			if(!empty($accountId) && !empty($contactId)) {
				$sql ="insert into vtiger_contpotentialrel values(?,?)";
				$result = $adb->pquery($sql, array($contactId, $potentialIdComponents[1]));
				if($result === false){
					throw new WebServiceException(WebServiceErrorCode::$FAILED_TO_CREATE_RELATION,
						"Failed to related Contact with the Potential");
				}
			}
			//Retrieve the lead related products and relate them with this new potential
			$status = vtws_saveLeadRelatedProducts($leadIdComponents[1], $potentialIdComponents[1], "Potentials");
			if($status === false){
				throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
					"Failed to move related Products to the Potential");
			}
			$status = vtws_saveLeadRelations($leadIdComponents[1], $potentialIdComponents[1], "Potentials");
			if($status === false){
				throw new WebServiceException(WebServiceErrorCode::$LEAD_RELATED_UPDATE_FAILED,
					"Failed to move Records to the Potential");
			}
			$potentialId = $potentialIdComponents[1];
		}
	}
	//Updating the converted status
	if($accountId != '' || $contactId != ''){
		$sql = "UPDATE vtiger_leaddetails SET converted = 1 where leadid=?";
		$result = $adb->pquery($sql, array($leadIdComponents[1]));
		if($result === false){
			throw new WebServiceException(WebServiceErrorCode::$FAILED_TO_MARK_CONVERTED,
				"Failed mark lead converted");
		}
		//updating the campaign-lead relation --Minnie
		$sql = "delete from vtiger_campaignleadrel where leadid=?";
		$adb->pquery($sql, array($leadIdComponents[1]));
	}
	$result = array('leadId'=>$leadId);
	if(!empty($webserviceAccountId)){
		$result['accountId'] = $webserviceAccountId;
	}else{
		$result['accountId'] = '';
	}
	if(!empty($contactId)){
		$result['contactId'] = vtws_getWebserviceEntityId('Contacts',$contactId);;
	}else{
		$result['contactId'] = '';
	}
	if(!empty($potentialId)){
		$result['potentialId'] = $potential['id'];
	}else{
		$result['potentialId'] = '';
	}
	return $result;
}

?>