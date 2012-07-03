<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/


/**
 * Description of ListViewController
 *
 * @author MAK
 */
class ListViewController {
	/**
	 *
	 * @var QueryGenerator
	 */
	private $queryGenerator;
	/**
	 *
	 * @var PearDatabase
	 */
	private $db;
	private $nameList;
	private $typeList;
	private $ownerNameList;
	private $user;
	private $picklistValueMap;
	private $picklistRoleMap;
	private $headerSortingEnabled;
	public function __construct($db, $user, $generator) {
		$this->queryGenerator = $generator;
		$this->db = $db;
		$this->user = $user;
		$this->nameList = array();
		$this->typeList = array();
		$this->ownerNameList = array();
		$this->picklistValueMap = array();
		$this->picklistRoleMap = array();
		$this->headerSortingEnabled = true;
	}

	public function isHeaderSortingEnabled() {
		return $this->headerSortingEnabled;
	}

	public function setHeaderSorting($enabled) {
		$this->headerSortingEnabled = $enabled;
	}

	public function setupAccessiblePicklistValueList($name) {
		$isRoleBased = vtws_isRoleBasedPicklist($name);
		$this->picklistRoleMap[$name] = $isRoleBased;
		if ($this->picklistRoleMap[$name]) {
			$this->picklistValueMap[$name] = getAssignedPicklistValues($name,$this->user->roleid, $this->db);
		}
	}

	public function fetchNameList($field, $result) {
		$referenceFieldInfoList = $this->queryGenerator->getReferenceFieldInfoList();
		$fieldName = $field->getFieldName();
		$rowCount = $this->db->num_rows($result);

		$idList = array();
		for ($i = 0; $i < $rowCount; $i++) {
			$id = $this->db->query_result($result, $i, $field->getColumnName());
			if (!isset($this->nameList[$fieldName][$id])) {
				$idList[$id] = $id;
			}
		}

		$idList = array_keys($idList);
		if(count($idList) == 0) {
			return;
		}
		$moduleList = $referenceFieldInfoList[$fieldName];
		foreach ($moduleList as $module) {
			$meta = $this->queryGenerator->getMeta($module);
			if ($meta->isModuleEntity()) {
				if($module == 'Users') {
					$nameList = getOwnerNameList($idList);
				} else {
					//TODO handle multiple module names overriding each other.
					$nameList = getEntityName($module, $idList);
				}
			} else {
				$nameList = vtws_getActorEntityName($module, $idList);
			}
			$entityTypeList = array_intersect(array_keys($nameList), $idList);
			foreach ($entityTypeList as $id) {
				$this->typeList[$id] = $module;
			}
			if(empty($this->nameList[$fieldName])) {
				$this->nameList[$fieldName] = array();
			}
			foreach ($entityTypeList as $id) {
				$this->typeList[$id] = $module;
				$this->nameList[$fieldName][$id] = $nameList[$id];
			}
		}
	}

	/**This function generates the List view entries in a list view
	 * Param $focus - module object
	 * Param $result - resultset of a listview query
	 * Param $navigation_array - navigation values in an array
	 * Param $relatedlist - check for related list flag
	 * Param $returnset - list query parameters in url string
	 * Param $edit_action - Edit action value
	 * Param $del_action - delete action value
	 * Param $oCv - vtiger_customview object
	 * Returns an array type
	 */
	function getListViewEntries($focus, $module,$result,$navigationInfo,$skipActions=false) {

		require('user_privileges/user_privileges_'.$this->user->id.'.php');
		global $listview_max_textlength, $theme,$default_charset;
		$fields = $this->queryGenerator->getFields();
		$whereFields = $this->queryGenerator->getWhereFields();
		$meta = $this->queryGenerator->getMeta($this->queryGenerator->getModule());

		$moduleFields = $meta->getModuleFields();
		$accessibleFieldList = array_keys($moduleFields);
		$listViewFields = array_intersect($fields, $accessibleFieldList);

		$referenceFieldList = $this->queryGenerator->getReferenceFieldList();
		foreach ($referenceFieldList as $fieldName) {
			if (in_array($fieldName, $listViewFields)) {
				$field = $moduleFields[$fieldName];
				$this->fetchNameList($field, $result);
			}
		}

		$db = PearDatabase::getInstance();
		$rowCount = $db->num_rows($result);
		$ownerFieldList = $this->queryGenerator->getOwnerFieldList();
		foreach ($ownerFieldList as $fieldName) {
			if (in_array($fieldName, $listViewFields)) {
				$field = $moduleFields[$fieldName];
				$idList = array();
				for ($i = 0; $i < $rowCount; $i++) {
					$id = $this->db->query_result($result, $i, $field->getColumnName());
					if (!isset($this->ownerNameList[$fieldName][$id])) {
						$idList[] = $id;
					}
				}
				if(count($idList) > 0) {
					if(!is_array($this->ownerNameList[$fieldName])) {
						$this->ownerNameList[$fieldName] = getOwnerNameList($idList);
					} else {
						//array_merge API loses key information so need to merge the arrays
						// manually.
						$newOwnerList = getOwnerNameList($idList);
						foreach ($newOwnerList as $id => $name) {
							$this->ownerNameList[$fieldName][$id] = $name;
						}
					}
				}
			}
		}

		foreach ($listViewFields as $fieldName) {
			$field = $moduleFields[$fieldName];
			if(!$is_admin && ($field->getFieldDataType() == 'picklist' ||
					$field->getFieldDataType() == 'multipicklist')) {
				$this->setupAccessiblePicklistValueList($fieldName);
			}
		}

		$useAsterisk = get_use_asterisk($this->user->id);

		$data = array();
		for ($i = 0; $i < $rowCount; ++$i) {
			//Getting the recordId
			if($module != 'Users') {
				$baseTable = $meta->getEntityBaseTable();
				$moduleTableIndexList = $meta->getEntityTableIndexList();
				$baseTableIndex = $moduleTableIndexList[$baseTable];

				$recordId = $db->query_result($result,$i,$baseTableIndex);
				$ownerId = $db->query_result($result,$i,"smownerid");
			}else {
				$recordId = $db->query_result($result,$i,"id");
			}
			$row = array();

			foreach ($listViewFields as $fieldName) {
				$field = $moduleFields[$fieldName];
				$uitype = $field->getUIType();
				$rawValue = $this->db->query_result($result, $i, $field->getColumnName());
				if($module == 'Calendar') {
					$activityType = $this->db->query_result($result, $i, 'activitytype');
				}

				if($uitype != 8){
					$value = html_entity_decode($rawValue,ENT_QUOTES,$default_charset);
				} else {
					$value = $rawValue;
				}

				if($module == 'Documents' && $fieldName == 'filename') {
					$downloadtype = $db->query_result($result,$i,'filelocationtype');
					if($downloadtype == 'I') {
						$ext =substr($value, strrpos($value, ".") + 1);
						$ext = strtolower($ext);
						if($value != ''){
							if($ext == 'bin' || $ext == 'exe' || $ext == 'rpm') {
								$fileicon = "<img src='" . vtiger_imageurl('fExeBin.gif', $theme).
										"' hspace='3' align='absmiddle' border='0'>";
							} elseif($ext == 'jpg' || $ext == 'gif' || $ext == 'bmp') {
								$fileicon = "<img src='".vtiger_imageurl('fbImageFile.gif', $theme).
										"' hspace='3' align='absmiddle' border='0'>";
							} elseif($ext == 'txt' || $ext == 'doc' || $ext == 'xls') {
								$fileicon = "<img src='".vtiger_imageurl('fbTextFile.gif', $theme).
										"' hspace='3' align='absmiddle' border='0'>";
							} elseif($ext == 'zip' || $ext == 'gz' || $ext == 'rar') {
								$fileicon = "<img src='".vtiger_imageurl('fbZipFile.gif', $theme).
										"' hspace='3' align='absmiddle'	border='0'>";
							} else {
								$fileicon = "<img src='".vtiger_imageurl('fbUnknownFile.gif',$theme)
										. "' hspace='3' align='absmiddle' border='0'>";
							}
						}
					} elseif($downloadtype == 'E') {
						if(trim($value) != '' ) {
							$fileicon = "<img src='" . vtiger_imageurl('fbLink.gif', $theme) .
									"' alt='".getTranslatedString('LBL_EXTERNAL_LNK',$module).
									"' title='".getTranslatedString('LBL_EXTERNAL_LNK',$module).
									"' hspace='3' align='absmiddle' border='0'>";
						} else {
							$value = '--';
							$fileicon = '';
						}
					} else {
						$value = ' --';
						$fileicon = '';
					}

					$fileName = $db->query_result($result,$i,'filename');

					$downloadType = $db->query_result($result,$i,'filelocationtype');
					$status = $db->query_result($result,$i,'filestatus');
					$fileIdQuery = "select attachmentsid from vtiger_seattachmentsrel where crmid=?";
					$fileIdRes = $db->pquery($fileIdQuery,array($recordId));
					$fileId = $db->query_result($fileIdRes,0,'attachmentsid');
					if($fileName != '' && $status == 1) {
						if($downloadType == 'I' ) {
							$value = "<a href='index.php?module=uploads&action=downloadfile&".
									"entityid=$recordId&fileid=$fileId' title='".
									getTranslatedString("LBL_DOWNLOAD_FILE",$module).
									"' onclick='javascript:dldCntIncrease($recordId);'>".textlength_check($value).
									"</a>";
						} elseif($downloadType == 'E') {
							$value = "<a target='_blank' href='$fileName' onclick='javascript:".
									"dldCntIncrease($recordId);' title='".
									getTranslatedString("LBL_DOWNLOAD_FILE",$module)."'>".textlength_check($value).
									"</a>";
						} else {
							$value = ' --';
						}
					}
					$value = $fileicon.$value;
				} elseif($module == 'Documents' && $fieldName == 'filesize') {
					$downloadType = $db->query_result($result,$i,'filelocationtype');
					if($downloadType == 'I') {
						$filesize = $value;
						if($filesize < 1024)
							$value=$filesize.' B';
						elseif($filesize > 1024 && $filesize < 1048576)
							$value=round($filesize/1024,2).' KB';
						else if($filesize > 1048576)
							$value=round($filesize/(1024*1024),2).' MB';
					} else {
						$value = ' --';
					}
				} elseif( $module == 'Documents' && $fieldName == 'filestatus') {
					if($value == 1)
						$value=getTranslatedString('yes',$module);
					elseif($value == 0)
						$value=getTranslatedString('no',$module);
					else
						$value='--';
				} elseif( $module == 'Documents' && $fieldName == 'filetype') {
					$downloadType = $db->query_result($result,$i,'filelocationtype');
					if($downloadType == 'E' || $downloadType != 'I') {
						$value = '--';
					}
				} elseif ($field->getUIType() == '27') {
					if ($value == 'I') {
						$value = getTranslatedString('LBL_INTERNAL',$module);
					}elseif ($value == 'E') {
						$value = getTranslatedString('LBL_EXTERNAL',$module);
					}else {
						$value = ' --';
					}
				}elseif ($field->getFieldDataType() == 'picklist') {
					if ($value != '' && !$is_admin && $this->picklistRoleMap[$fieldName] &&
							!in_array($value, $this->picklistValueMap[$fieldName])) {
						$value = "<font color='red'>".getTranslatedString('LBL_NOT_ACCESSIBLE',
								$module)."</font>";
					} else {
						$value = getTranslatedString($value,$module);
						$value = textlength_check($value);
					}
				}elseif($field->getFieldDataType() == 'date' ||
						$field->getFieldDataType() == 'datetime') {
					if($value != '' && $value != '0000-00-00') {
						$date = new DateTimeField($value);
						$value = $date->getDisplayDate();
						if($field->getFieldDataType() == 'datetime') {
							$value .= (' ' . $date->getDisplayTime());
						}
					} elseif ($value == '0000-00-00') {
						$value = '';
					}
				} elseif($field->getFieldDataType() == 'currency') {
					if($value != '') {
						if($field->getUIType() == 72) {
							if($fieldName == 'unit_price') {
								$currencyId = getProductBaseCurrency($recordId,$module);
								$cursym_convrate = getCurrencySymbolandCRate($currencyId);
								$currencySymbol = $cursym_convrate['symbol'];
							} else {
								$currencyInfo = getInventoryCurrencyInfo($module, $recordId);
								$currencySymbol = $currencyInfo['currency_symbol'];
							}
							$value = number_format($value, 2,'.','');
							$currencyValue = CurrencyField::convertToUserFormat($value, null, true);
							$value = CurrencyField::appendCurrencySymbol($currencyValue, $currencySymbol);
						} else {
							//changes made to remove vtiger_currency symbol infront of each
							//vtiger_potential amount
							if ($value != 0) {
								$value = CurrencyField::convertToUserFormat($value);
							}
						}
					}
				} elseif($field->getFieldDataType() == 'url') {
                    $matchPattern = "^[\w]+:\/\/^";
                    preg_match($matchPattern, $rawValue, $matches);
                    if(!empty ($matches[0])){
                        $value = '<a href="'.$rawValue.'" target="_blank">'.textlength_check($value).'</a>';
                    }else{
                        $value = '<a href="http://'.$rawValue.'" target="_blank">'.textlength_check($value).'</a>';
                    }
				} elseif ($field->getFieldDataType() == 'email') {
					if($_SESSION['internal_mailer'] == 1) {
						//check added for email link in user detailview
						$fieldId = $field->getFieldId();
						$value = "<a href=\"javascript:InternalMailer($recordId,$fieldId,".
						"'$fieldName','$module','record_id');\">".textlength_check($value)."</a>";
					}else {
						$value = '<a href="mailto:'.$rawValue.'">'.textlength_check($value).'</a>';
					}
				} elseif($field->getFieldDataType() == 'boolean') {
					if($value == 1) {
						$value = getTranslatedString('yes',$module);
					} elseif($value == 0) {
						$value = getTranslatedString('no',$module);
					} else {
						$value = '--';
					}
				} elseif($field->getUIType() == 98) {
					$value = '<a href="index.php?action=RoleDetailView&module=Settings&parenttab='.
						'Settings&roleid='.$value.'">'.textlength_check(getRoleName($value)).'</a>';
				} elseif($field->getFieldDataType() == 'multipicklist') {
					$value = ($value != "") ? str_replace(' |##| ',', ',$value) : "";
					if(!$is_admin && $value != '') {
						$valueArray = ($rawValue != "") ? explode(' |##| ',$rawValue) : array();
						$notaccess = '<font color="red">'.getTranslatedString('LBL_NOT_ACCESSIBLE',
								$module)."</font>";
						$tmp = '';
						$tmpArray = array();
						foreach($valueArray as $index => $val) {
							if(!$listview_max_textlength ||
									!(strlen(preg_replace("/(<\/?)(\w+)([^>]*>)/i","",$tmp)) >
											$listview_max_textlength)) {
								if (!$is_admin && $this->picklistRoleMap[$fieldName] &&
										!in_array(trim($val), $this->picklistValueMap[$fieldName])) {
									$tmpArray[] = $notaccess;
									$tmp .= ', '.$notaccess;
								} else {
									$tmpArray[] = $val;
									$tmp .= ', '.$val;
								}
							} else {
								$tmpArray[] = '...';
								$tmp .= '...';
							}
						}
						$value = implode(', ', $tmpArray);
						$value = textlength_check($value);
					}
				} elseif ($field->getFieldDataType() == 'skype') {
					$value = ($value != "") ? "<a href='skype:$value?call'>".textlength_check($value)."</a>" : "";
				} elseif ($field->getFieldDataType() == 'phone') {
					if($useAsterisk == 'true') {
						$value = "<a href='javascript:;' onclick='startCall(&quot;$value&quot;, ".
							"&quot;$recordId&quot;)'>".textlength_check($value)."</a>";
					} else {
						$value = textlength_check($value);
					}
				} elseif($field->getFieldDataType() == 'reference') {
					$referenceFieldInfoList = $this->queryGenerator->getReferenceFieldInfoList();
					$moduleList = $referenceFieldInfoList[$fieldName];
					if(count($moduleList) == 1) {
						$parentModule = $moduleList[0];
					} else {
						$parentModule = $this->typeList[$value];
					}
					if(!empty($value) && !empty($this->nameList[$fieldName]) && !empty($parentModule)) {
						$parentMeta = $this->queryGenerator->getMeta($parentModule);
						$value = textlength_check($this->nameList[$fieldName][$value]);
						if ($parentMeta->isModuleEntity() && $parentModule != "Users") {
							$value = "<a href='index.php?module=$parentModule&action=DetailView&".
								"record=$rawValue' title='".getTranslatedString($parentModule, $parentModule)."'>$value</a>";
						}
					} else {
						$value = '--';
					}
				} elseif($field->getFieldDataType() == 'owner') {
					$value = textlength_check($this->ownerNameList[$fieldName][$value]);
				} elseif ($field->getUIType() == 25) {
					//TODO clean request object reference.
					$contactId=$_REQUEST['record'];
					$emailId=$this->db->query_result($result,$i,"activityid");
					$result1 = $this->db->pquery("SELECT access_count FROM vtiger_email_track WHERE ".
							"crmid=? AND mailid=?", array($contactId,$emailId));
					$value=$this->db->query_result($result1,0,"access_count");
					if(!$value) {
						$value = 0;
					}
				} elseif($field->getUIType() == 8){
					if(!empty($value)){
						$temp_val = html_entity_decode($value,ENT_QUOTES,$default_charset);
						$json = new Zend_Json();
						$value = vt_suppressHTMLTags(implode(',',$json->decode($temp_val)));
					}
				} elseif ( in_array($uitype,array(7,9,90)) ) {
					$value = "<span align='right'>".textlength_check($value)."</div>";
				} else {
					$value = textlength_check($value);
				}

                                    $parenttab = getParentTab();
				$nameFields = $this->queryGenerator->getModuleNameFields($module);
				$nameFieldList = explode(',',$nameFields);
				if(in_array($fieldName, $nameFieldList) && $module != 'Emails' ) {
					$value = "<a href='index.php?module=$module&parenttab=$parenttab&action=DetailView&record=".
					"$recordId' title='".getTranslatedString($module, $module)."'>$value</a>";
				} elseif($fieldName == $focus->list_link_field && $module != 'Emails') {
					$value = "<a href='index.php?module=$module&parenttab=$parenttab&action=DetailView&record=".
					"$recordId' title='".getTranslatedString($module, $module)."'>$value</a>";
				}

				// vtlib customization: For listview javascript triggers
				$value = "$value <span type='vtlib_metainfo' vtrecordid='{$recordId}' vtfieldname=".
					"'{$fieldName}' vtmodule='$module' style='display:none;'></span>";
				// END
				$row[] = $value;
			}

			//Added for Actions ie., edit and delete links in listview
			$actionLinkInfo = "";
			if(isPermitted($module,"EditView","") == 'yes'){
				$edit_link = $this->getListViewEditLink($module,$recordId);
				if(isset($navigationInfo['start']) && $navigationInfo['start'] > 1 && $module != 'Emails') {
					$actionLinkInfo .= "<a href=\"$edit_link&start=".
						$navigationInfo['start']."\">".getTranslatedString("LNK_EDIT",
								$module)."</a> ";
				} else {
					$actionLinkInfo .= "<a href=\"$edit_link\">".getTranslatedString("LNK_EDIT",
								$module)."</a> ";
				}
			}

			if(isPermitted($module,"Delete","") == 'yes'){
				$del_link = $this->getListViewDeleteLink($module,$recordId);
				if($actionLinkInfo != "" && $del_link != "")
					$actionLinkInfo .=  " | ";
				if($del_link != "")
					$actionLinkInfo .=	"<a href='javascript:confirmdelete(\"".
						addslashes(urlencode($del_link))."\")'>".getTranslatedString("LNK_DELETE",
								$module)."</a>";
			}
			// Record Change Notification
			if(method_exists($focus, 'isViewed') &&
					PerformancePrefs::getBoolean('LISTVIEW_RECORD_CHANGE_INDICATOR', true)) {
				if(!$focus->isViewed($recordId)) {
					$actionLinkInfo .= " | <img src='" . vtiger_imageurl('important1.gif',
							$theme) . "' border=0>";
				}
			}
			// END
			if($actionLinkInfo != "" && !$skipActions) {
				$row[] = $actionLinkInfo;
			}
			$data[$recordId] = $row;

		}
		return $data;
	}

	public function getListViewEditLink($module,$recordId, $activityType='') {
		if($module == 'Emails')
	        return 'javascript:;" onclick="OpenCompose(\''.$recordId.'\',\'edit\');';
		if($module != 'Calendar') {
			$return_action = "index";
		} else {
			$return_action = 'ListView';
		}
		//Added to fix 4600
		$url = getBasic_Advance_SearchURL();
		$parent = getParentTab();
		//Appending view name while editing from ListView
		$link = "index.php?module=$module&action=EditView&record=$recordId&return_module=$module".
			"&return_action=$return_action&parenttab=$parent".$url."&return_viewname=".
			$_SESSION['lvs'][$module]["viewname"];

		if($module == 'Calendar') {
			if($activityType == 'Task') {
				$link .= '&activity_mode=Task';
			} else {
				$link .= '&activity_mode=Events';
			}
		}
		return $link;
	}

	public function getListViewDeleteLink($module,$recordId) {
		$parenttab = getParentTab();
		$viewname = $_SESSION['lvs'][$module]['viewname'];
		//Added to fix 4600
		$url = getBasic_Advance_SearchURL();
		if($module == "Calendar")
			$return_action = "ListView";
		else
			$return_action = "index";
		//This is added to avoid the del link in Product related list for the following modules
		$link = "index.php?module=$module&action=Delete&record=$recordId".
			"&return_module=$module&return_action=$return_action".
			"&parenttab=$parenttab&return_viewname=".$viewname.$url;

		// vtlib customization: override default delete link for custom modules
		$requestModule = vtlib_purify($_REQUEST['module']);
		$requestRecord = vtlib_purify($_REQUEST['record']);
		$requestAction = vtlib_purify($_REQUEST['action']);
		$requestFile = vtlib_purify($_REQUEST['file']);
		$isCustomModule = vtlib_isCustomModule($requestModule);

		if($isCustomModule && (!in_array($requestAction, Array('index','ListView')) &&
				($requestAction == $requestModule.'Ajax' && !in_array($requestFile, Array('index','ListView'))))) {

			$link = "index.php?module=$requestModule&action=updateRelations&parentid=$requestRecord";
			$link .= "&destination_module=$module&idlist=$entity_id&mode=delete&parenttab=$parenttab";
		}
		// END
		return $link;
	}

	public function getListViewHeader($focus, $module,$sort_qry='',$sorder='',$orderBy='',
			$skipActions=false) {
		global $log, $singlepane_view;
		global $theme;

		$arrow='';
		$qry = getURLstring($focus);
		$theme_path="themes/".$theme."/";
		$image_path=$theme_path."images/";
		$header = Array();

		//Get the vtiger_tabid of the module
		$tabid = getTabid($module);
		$tabname = getParentTab();
		global $current_user;

		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		$fields = $this->queryGenerator->getFields();
		$whereFields = $this->queryGenerator->getWhereFields();
		$meta = $this->queryGenerator->getMeta($this->queryGenerator->getModule());

		$moduleFields = $meta->getModuleFields();
		$accessibleFieldList = array_keys($moduleFields);
		$listViewFields = array_intersect($fields, $accessibleFieldList);
		//Added on 14-12-2005 to avoid if and else check for every list
		//vtiger_field for arrow image and change order
		$change_sorder = array('ASC'=>'DESC','DESC'=>'ASC');
		$arrow_gif = array('ASC'=>'arrow_down.gif','DESC'=>'arrow_up.gif');
		foreach($listViewFields as $fieldName) {
			$field = $moduleFields[$fieldName];

			if(in_array($field->getColumnName(),$focus->sortby_fields)) {
				if($orderBy == $field->getColumnName()) {
					$temp_sorder = $change_sorder[$sorder];
					$arrow = "&nbsp;<img src ='".vtiger_imageurl($arrow_gif[$sorder], $theme)."' border='0'>";
				} else {
					$temp_sorder = 'ASC';
				}
				$label = getTranslatedString($field->getFieldLabelKey(), $module);
				//added to display vtiger_currency symbol in listview header
				if($label =='Amount') {
					$label .=' ('.getTranslatedString('LBL_IN', $module).' '.
							$user_info['currency_symbol'].')';
				}
				if($field->getUIType() == '9') {
					$label .=' (%)';
				}
				if($module == 'Users' && $fieldName == 'User Name') {
					$name = "<a href='javascript:;' onClick='getListViewEntries_js(\"".$module.
						"\",\"parenttab=".$tabname."&order_by=".$field->getColumnName()."&sorder=".
						$temp_sorder.$sort_qry."\");' class='listFormHeaderLinks'>".
						getTranslatedString('LBL_LIST_USER_NAME_ROLE',$module)."".$arrow."</a>";
				} else {
					if($this->isHeaderSortingEnabled()) {
						$name = "<a href='javascript:;' onClick='getListViewEntries_js(\"".$module.
							"\",\"parenttab=".$tabname."&foldername=Default&order_by=".$field->getColumnName()."&start=".
							$_SESSION["lvs"][$module]["start"]."&sorder=".$temp_sorder."".
						$sort_qry."\");' class='listFormHeaderLinks'>".$label."".$arrow."</a>";

					} else {
						$name = $label;
					}
				}
				$arrow = '';
			} else {
				$name = getTranslatedString($field->getFieldLabelKey(), $module);
			}
			//added to display vtiger_currency symbol in related listview header
			if($name =='Amount') {
				$name .=' ('.getTranslatedString('LBL_IN').' '.$user_info['currency_symbol'].')';
			}

			$header[]=$name;
		}

		//Added for Action - edit and delete link header in listview
		if(!$skipActions && (isPermitted($module,"EditView","") == 'yes' ||
				isPermitted($module,"Delete","") == 'yes'))
			$header[] = getTranslatedString("LBL_ACTION", $module);
		return $header;
	}

	public function getBasicSearchFieldInfoList() {
		$fields = $this->queryGenerator->getFields();
		$whereFields = $this->queryGenerator->getWhereFields();
		$meta = $this->queryGenerator->getMeta($this->queryGenerator->getModule());

		$moduleFields = $meta->getModuleFields();
		$accessibleFieldList = array_keys($moduleFields);
		$listViewFields = array_intersect($fields, $accessibleFieldList);
		$basicSearchFieldInfoList = array();
		foreach ($listViewFields as $fieldName) {
			$field = $moduleFields[$fieldName];
			$basicSearchFieldInfoList[$fieldName] = getTranslatedString($field->getFieldLabelKey(),
					$this->queryGenerator->getModule());
		}
		return $basicSearchFieldInfoList;
	}

	public function getAdvancedSearchOptionString() {
		$module = $this->queryGenerator->getModule();
		$meta = $this->queryGenerator->getMeta($module);

		$moduleFields = $meta->getModuleFields();
		$i =0;
		foreach ($moduleFields as $fieldName=>$field) {
			if($field->getFieldDataType() == 'reference') {
				$typeOfData = 'V';
			} else if($field->getFieldDataType() == 'boolean') {
				$typeOfData = 'C';
			} else {
				$typeOfData = $field->getTypeOfData();
				$typeOfData = explode("~",$typeOfData);
				$typeOfData = $typeOfData[0];
			}
			$label = getTranslatedString($field->getFieldLabelKey(), $module);
			if(empty($label)) {
				$label = $field->getFieldLabelKey();
			}
			if($label == "Start Date & Time") {
				$fieldlabel = "Start Date";
			}
			$selected = '';
			if($i++ == 0) {
				$selected = "selected";
			}

			// place option in array for sorting later
			//$blockName = getTranslatedString(getBlockName($field->getBlockId()), $module);
			$blockName = getTranslatedString($field->getBlockName(), $module);

			$fieldLabelEscaped = str_replace(" ","_",$field->getFieldLabelKey());
			$optionvalue = $field->getTableName().":".$field->getColumnName().":".$fieldName.":".$module."_".$fieldLabelEscaped.":".$typeOfData;

			$OPTION_SET[$blockName][$label] = "<option value=\'$optionvalue\' $selected>$label</option>";

		}
	   	// sort array on block label
	    ksort($OPTION_SET, SORT_STRING);

		foreach ($OPTION_SET as $key=>$value) {
	  		$shtml .= "<optgroup label='$key' class='select' style='border:none'>";
	   		// sort array on field labels
	   		ksort($value, SORT_STRING);
	  		$shtml .= implode('',$value);
	  	}

	    return $shtml;
	}

}
?>