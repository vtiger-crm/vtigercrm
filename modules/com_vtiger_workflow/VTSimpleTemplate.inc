<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class VTSimpleTemplate{

	function __construct($templateString){
		$this->template = $templateString;
	}

	function render($entityCache, $entityId){
		$this->cache = $entityCache;
		$this->parent = $this->cache->forId($entityId);
		return $this->parseTemplate();
	}

	private function matchHandler($match){
		preg_match('/\((\w+) : \(([_\w]+)\) (\w+)\)/', $match[1], $matches);
		if(count($matches)==0){
			$fieldname = $match[1];
			$data = $this->parent->getData();
			if($this->useValue($data, $fieldname)){
				$result = $data[$fieldname];
			}else{
				$result ='';
			}
		}else{
			list($full, $referenceField, $referenceModule, $fieldname) = $matches;
			if($referenceModule === '__VtigerMeta__'){
				$result = $this->getMetaValue($fieldname);
			}else{
				$referenceId = $this->parent->get($referenceField);
				if($referenceId==null){
					$result="";
				}else{
					$entity = $this->cache->forId($referenceId);
					if($referenceModule==="Users" && $entity->getModuleName()=="Groups"){
						list($groupEntityId, $groupId) = vtws_getIdComponents($referenceId);

						require_once('include/utils/GetGroupUsers.php');
						$ggu = new GetGroupUsers();
						$ggu->getAllUsersInGroup($groupId);

						$users = $ggu->group_users;
						$parts = Array();
						foreach($users as $userId){
							$refId = vtws_getWebserviceEntityId("Users", $userId);
							$entity = $this->cache->forId($refId);
							$data = $entity->getData();
							if($this->useValue($data, $fieldname)){
								$parts[] = $data[$fieldname];
							}
						}
						$result = implode(",", $parts);

					} elseif($entity->getModuleName()===$referenceModule){
						$data = $entity->getData();
						if($this->useValue($data, $fieldname)){
							$result = $data[$fieldname];
						}else{
							$result = '';
						}
					}else{
						$result = '';
					}
				}
			}
		}
		return $result;

	}

	protected function useValue($data, $fieldname) {
		return !empty($data[$fieldname]);
	}

	function parseTemplate(){
		return preg_replace_callback('/\\$(\w+|\((\w+) : \(([_\w]+)\) (\w+)\)),?/', array($this,"matchHandler"), $this->template);
	}

	function getMetaValue($fieldname){
		require_once 'config.inc.php';
		global $site_URL, $PORTAL_URL, $current_user;
		switch($fieldname){
			case 'date'					:	return getNewDisplayDate();
			case 'time'					:	return date('h:i:s');
			case 'dbtimezone'			:	return DateTimeField::getDBTimeZone();
			case 'crmdetailviewurl'		:	$wsId = $this->parent->getId();
											$parts = explode('x', $wsId);
											$recordId = $parts[1];
											$moduleName = $this->parent->getModuleName();
											return $site_URL.'/index.php?action=DetailView&module='.$moduleName.'&record='.$recordId;
			case 'portaldetailviewurl'	: 	$wsId = $this->parent->getId();
											$parts = explode('x', $wsId);
											$recordId = $parts[1];
											$moduleName = $this->parent->getModuleName();
											$recorIdName='id';
											if($moduleName == 'HelpDesk') $recorIdName = 'ticketid';
											if($moduleName == 'Faq') $recorIdName = 'faqid';
											if($moduleName == 'Products') $recorIdName = 'productid';
											return $PORTAL_URL.'/index.php?module='.$moduleName.'&action=index&'.$recorIdName.'='.$recordId.'&fun=detail';
			case 'siteurl'				: return $site_URL;
			case 'portalurl'			: return $PORTAL_URL;
			default: '';
		}
	}
}
?>
