<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('include/Webservices/Utils.php');
require_once("include/Webservices/VtigerCRMObject.php");
require_once("include/Webservices/VtigerCRMObjectMeta.php");
require_once("include/Webservices/DataTransform.php");
require_once("include/Webservices/WebServiceError.php");
require_once 'include/Webservices/ModuleTypes.php';
require_once('include/Webservices/Create.php');
require_once 'include/Webservices/DescribeObject.php';
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';

require_once("modules/Users/Users.php");

class VTCreateEventTask extends VTTask{
	public $executeImmediately = true;
	
	public function getFieldNames(){
		return array('eventType', 'eventName', 'description', 'sendNotification', 
								 'startTime', 'startDays', 'startDirection', 'startDatefield', 
								 'endTime','endDays', 'endDirection', 'endDatefield',
								 'status', 'priority','recurringcheck','repeat_frequency',
								 'recurringtype','calendar_repeat_limit_date',
								 'mon_flag','tue_flag','wed_flag','thu_flag','fri_flag','sat_flag','sun_flag',
								 'repeatMonth','repeatMonth_date','repeatMonth_daytype','repeatMonth_day');
	}
	
	function getAdmin(){
		$user = new Users();
		$user->retrieveCurrentUserInfoFromFile(1);
		global $current_user;
		$this->originalUser = $current_user;
		$current_user = $user;
		return $user;
	}
	
	public function doTask($entityData){
		global $adb, $current_user;
		$userId = $entityData->get('assigned_user_id');
		if($userId===null){
			$userId = vtws_getWebserviceEntityId('Users', 1);
		}

		$startDate = $this->calculateDate($entityData, $this->startDays, 
															$this->startDirection, $this->startDatefield);
		$endDate = $this->calculateDate($entityData, $this->endDays, 
															$this->endDirection, $this->endDatefield);
		
		$fields = array(
			'activitytype'=>$this->eventType,
			'description'=>$this->description,
			'subject'=>$this->eventName,
			'taskpriority'=>$this->priority,
			'eventstatus'=>$this->status,
			'assigned_user_id'=>$userId,
			'time_start'=>self::conv12to24hour($this->startTime),
			'date_start'=>$startDate,
			'time_end'=>self::conv12to24hour($this->endTime),
			'due_date'=>$endDate,
			'visibility'=>'all',
			'taskstatus'=>'',
			'duration_hours'=>'0'
		);

		$_REQUEST['date_start'] = getDisplayDate($startDate);
		$_REQUEST['due_date'] = getDisplayDate($endDate);
		$_REQUEST['recurringcheck']=$this->recurringcheck;
		$_REQUEST['repeat_frequency']=$this->repeat_frequency;
		$_REQUEST['recurringtype']=$this->recurringtype;
		$_REQUEST['calendar_repeat_limit_date']=getDisplayDate($this->calendar_repeat_limit_date);
		$_REQUEST['mon_flag']=$this->mon_flag;
		$_REQUEST['tue_flag']=$this->tue_flag;
		$_REQUEST['wed_flag']=$this->wed_flag;
		$_REQUEST['thu_flag']=$this->thu_flag;
		$_REQUEST['fri_flag']=$this->fri_flag;
		$_REQUEST['sat_flag']=$this->sat_flag;
		$_REQUEST['sun_flag']=$this->sun_flag;
		$_REQUEST['repeatMonth']=$this->repeatMonth;
		$_REQUEST['repeatMonth_date']=$this->repeatMonth_date;
		$_REQUEST['repeatMonth_daytype']=$this->repeatMonth_daytype;
		$_REQUEST['repeatMonth_day']=$this->repeatMonth_day;

		$moduleName = $entityData->getModuleName();
		$adminUser = $this->getAdmin();
		$id = $entityData->getId();
		if($moduleName=='Contacts'){
			$fields['contact_id'] = $id;
		}else{ 
			$data = vtws_describe('Calendar', $adminUser);
			$fieldInfo = $data['fields'];
			foreach($fieldInfo as $field){
				if($field['name']=='parent_id'){
					$parentIdField = $field;
				}
			}
			$refersTo = $parentIdField['type']['refersTo'];
			
			if(in_array($moduleName, $refersTo)){
				$fields['parent_id'] = $id;
			}
		}
		
		$event = vtws_create('Events', $fields, $adminUser);
		list($typeId, $id) = vtws_getIdComponents($event['id']);
		$event = CRMEntity::getInstance('Events');
		$event->id = $id;
		$startDate = $entityData->get($this->startDatefield);
		if($this->recurringcheck && !empty($startDate) &&
				($this->calendar_repeat_limit_date)) {
			include_once 'modules/Calendar/RepeatEvents.php';
			Calendar_RepeatEvents::repeat($event);
		}
		global $current_user;
		$current_user = $this->originalUser;
	}

	private function calculateDate($entityData, $days, $direction, $datefield){
		$baseDate = $entityData->get($datefield);
		preg_match('/\d\d\d\d-\d\d-\d\d/', $baseDate, $match);
		$baseDate = strtotime($match[0]);
		$date = strftime('%Y-%m-%d', $baseDate+$days*24*60*60*
										 ($direction=='Before'?-1:1));
		return $date;
	}

	static function conv12to24hour($timeStr){
		$arr = array();
		preg_match('/(\d{1,2}):(\d{1,2})(am|pm)/', $timeStr, $arr);
		if($arr[3]=='am'){
			$hours = ((int)$arr[1]) % 12;
		}else{
			$hours = ((int)$arr[1]) % 12 + 12;
		}
		return str_pad($hours, 2, '0', STR_PAD_LEFT).':'.str_pad($arr[2], 2, '0', STR_PAD_LEFT);
	}
}
?>