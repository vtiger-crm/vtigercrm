<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/include/utils/EditViewUtils.php,v 1.188 2005/04/29 05:5 * 4:39 rank Exp  
 * Description:  Includes generic helper functions used throughout the application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php'); //new
require_once('include/utils/CommonUtils.php'); //new

/** This function returns the vtiger_field details for a given vtiger_fieldname.
  * Param $uitype - UI type of the vtiger_field
  * Param $fieldname - Form vtiger_field name
  * Param $fieldlabel - Form vtiger_field label name
  * Param $maxlength - maximum length of the vtiger_field
  * Param $col_fields - array contains the vtiger_fieldname and values
  * Param $generatedtype - Field generated type (default is 1)
  * Param $module_name - module name
  * Return type is an array
  */

function getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields,$generatedtype,$module_name,$mode='', $typeofdata=null)
{
	global $log,$app_strings;
	$log->debug("Entering getOutputHtml(".$uitype.",". $fieldname.",". $fieldlabel.",". $maxlength.",". $col_fields.",".$generatedtype.",".$module_name.") method ...");
	global $adb,$log,$default_charset;
	global $theme;
	global $mod_strings;
	global $app_strings;
	global $current_user;

	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	require('user_privileges/user_privileges_'.$current_user->id.'.php');

	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	$fieldlabel = from_html($fieldlabel);
	$fieldvalue = Array();
	$final_arr = Array();
	$value = $col_fields[$fieldname];
	$custfld = '';
	$ui_type[]= $uitype;
	$editview_fldname[] = $fieldname;

	// vtlib customization: Related type field
	if($uitype == '10') {
		global $adb;
		$fldmod_result = $adb->pquery('SELECT relmodule, status FROM vtiger_fieldmodulerel WHERE fieldid=
			(SELECT fieldid FROM vtiger_field, vtiger_tab WHERE vtiger_field.tabid=vtiger_tab.tabid AND fieldname=? AND name=? and vtiger_field.presence in (0,2))',
			Array($fieldname, $module_name));

		$entityTypes = Array();
		$parent_id = $value;
		for($index = 0; $index < $adb->num_rows($fldmod_result); ++$index) {
			$entityTypes[] = $adb->query_result($fldmod_result, $index, 'relmodule');
		}

		if(!empty($value)) {
			$valueType = getSalesEntityType($value);
			$displayValueArray = getEntityName($valueType, $value);
			if(!empty($displayValueArray)){
				foreach($displayValueArray as $key=>$value){
					$displayValue = $value;
				}
			}
		} else {
			$displayValue='';
			$valueType='';
			$value='';
		}

		$editview_label[] = Array('options'=>$entityTypes, 'selected'=>$valueType, 'displaylabel'=>getTranslatedString($fieldlabel, $module_name));
		$fieldvalue[] = Array('displayvalue'=>$displayValue,'entityid'=>$parent_id);

	} // END
	else if($uitype == 5 || $uitype == 6 || $uitype ==23)
	{	
		$log->info("uitype is ".$uitype);
		if($value=='')
		{
			//modified to fix the issue in trac(http://vtiger.fosslabs.com/cgi-bin/trac.cgi/ticket/1469)
			if($fieldname != 'birthday' && $generatedtype != 2 && getTabid($module_name) !=14)// && $fieldname != 'due_date')//due date is today's date by default
				$disp_value=getNewDisplayDate();

			//Added to display the Contact - Support End Date as one year future instead of today's date -- 30-11-2005
			if($fieldname == 'support_end_date' && $_REQUEST['module'] == 'Contacts')
			{
				$addyear = strtotime("+1 year");
				global $current_user;
				$dat_fmt = (($current_user->date_format == '')?('dd-mm-yyyy'):($current_user->date_format));

				$disp_value = (($dat_fmt == 'dd-mm-yyyy')?(date('d-m-Y',$addyear)):(($dat_fmt == 'mm-dd-yyyy')?(date('m-d-Y',$addyear)):(($dat_fmt == 'yyyy-mm-dd')?(date('Y-m-d', $addyear)):(''))));
			}

			if($fieldname == 'validtill' && $_REQUEST['module'] == 'Quotes')
                        {
                                $disp_value = '';
                        }

		}
		else
		{
			$disp_value = getValidDisplayDate($value);
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$date_format = parse_calendardate($app_strings['NTC_DATE_FORMAT']);
		if($uitype == 6)
		{
			if($col_fields['time_start']!='')
			{
				$curr_time = $col_fields['time_start'];
			}
			else
			{
				$curr_time = date('H:i',(time() + (5 * 60)));
			}
		}
		if($module_name == 'Events' && $uitype == 23)
		{
			if($col_fields['time_end']!='')
			{
				$curr_time = $col_fields['time_end'];
			}
			else
			{
				$endtime = time() + (10 * 60);
				$curr_time = date('H:i',$endtime);
			}
		}
		$fieldvalue[] = array($disp_value => $curr_time) ;
		if($uitype == 5 || $uitype == 23)
		{
			if($module_name == 'Events' && $uitype == 23)
			{
				$fieldvalue[] = array($date_format=>$current_user->date_format.' '.$app_strings['YEAR_MONTH_DATE']);
			}
			else
				$fieldvalue[] = array($date_format=>$current_user->date_format);
		}
		else
		{
			$fieldvalue[] = array($date_format=>$current_user->date_format.' '.$app_strings['YEAR_MONTH_DATE']);
		}
	}
	elseif($uitype == 16) {
		require_once 'modules/PickList/PickListUtils.php';
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		
		$fieldname = $adb->sql_escape_string($fieldname);
		$pick_query="select $fieldname from vtiger_$fieldname order by sortorderid";
		$params = array();		
		$pickListResult = $adb->pquery($pick_query, $params);
		$noofpickrows = $adb->num_rows($pickListResult);

		$options = array();
		$pickcount=0;
		$found = false;
		for($j = 0; $j < $noofpickrows; $j++)
		{
			$value = decode_html($value);
			$pickListValue=decode_html($adb->query_result($pickListResult,$j,strtolower($fieldname)));
			if($value == trim($pickListValue))
			{

				$chk_val = "selected";
				$pickcount++;	
				$found = true;
			}
			else
			{	
				$chk_val = '';
			}
			$pickListValue = to_html($pickListValue);
			if(isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate')
				$options[] = array(htmlentities(getTranslatedString($pickListValue),ENT_QUOTES,$default_charset),$pickListValue,$chk_val );	
			else
				$options[] = array(getTranslatedString($pickListValue),$pickListValue,$chk_val );
		}
		$fieldvalue [] = $options;
	}
	elseif($uitype == 15 || $uitype == 33){
		require_once 'modules/PickList/PickListUtils.php';
		$roleid=$current_user->roleid;
		$picklistValues = getAssignedPicklistValues($fieldname, $roleid, $adb);
		$valueArr = explode("|##|", $value);
		$pickcount = 0;
		
		if(!empty($picklistValues)){
			foreach($picklistValues as $order=>$pickListValue){
				if(in_array(trim($pickListValue),array_map("trim", $valueArr))){
					$chk_val = "selected";
					$pickcount++;
				}else{
					$chk_val = '';
				}
				if(isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate'){
					$options[] = array(htmlentities(getTranslatedString($pickListValue),ENT_QUOTES,$default_charset),$pickListValue,$chk_val );	
				}else{
					$options[] = array(getTranslatedString($pickListValue),$pickListValue,$chk_val );
				}
			}
			
			if($pickcount == 0 && !empty($value)){
				$options[] =  array($app_strings['LBL_NOT_ACCESSIBLE'],$value,'selected');
			}
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = $options;
	}
	elseif($uitype == 17)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = $value;
	}
	elseif($uitype == 85) //added for Skype by Minnie
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = $value;
	}elseif($uitype == 19 || $uitype == 20)
	{
		if(isset($_REQUEST['body']))
		{
			$value = ($_REQUEST['body']);
		}

		if($fieldname == 'terms_conditions')//for default Terms & Conditions
		{
			//Assign the value from focus->column_fields (if we create Invoice from SO the SO's terms and conditions will be loaded to Invoice's terms and conditions, etc.,)
			$value = $col_fields['terms_conditions'];

			//if the value is empty then only we should get the default Terms and Conditions
			if($value == '' && $mode != 'edit')
				$value=getTermsandConditions();
		}

		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = $value;
	}
	elseif($uitype == 21 || $uitype == 24)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = $value;
	}
	elseif($uitype == 22)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $value;
	}
	elseif($uitype == 52 || $uitype == 77)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		global $current_user;
		if($value != '')
		{
			$assigned_user_id = $value;	
		}
		else
		{
			$assigned_user_id = $current_user->id;
		}
		if($uitype == 52)
		{
			$combo_lbl_name = 'assigned_user_id';
		}
		elseif($uitype == 77)
		{
			$combo_lbl_name = 'assigned_user_id1';
		}

		//Control will come here only for Products - Handler and Quotes - Inventory Manager
		if($is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
		{
			$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
		}
		else
		{
			$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
		}
		$fieldvalue [] = $users_combo;
	}
	elseif($uitype == 53)     
	{  
		global $noof_group_rows;
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		//Security Checks
		if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
		{
			$result=get_current_user_access_groups($module_name);
		}
		else
		{ 		
			$result = get_group_options();
		}
		if($result) $nameArray = $adb->fetch_array($result);

		if($value != '' && $value != 0)
			$assigned_user_id = $value;
		else{
			if($value=='0'){
				if (isset($col_fields['assigned_group_info']) && $col_fields['assigned_group_info'] != '') {
					$selected_groupname = $col_fields['assigned_group_info'];
				} else {
					$record = $col_fields["record_id"];
					$module = $col_fields["record_module"];
					$selected_groupname = getGroupName($record, $module);
				}
			}else
				$assigned_user_id = $current_user->id;
		}
		
		if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
		{
			$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
		}
		else
		{
			$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
		}

		if($noof_group_rows!=0)
		{
			if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
			{
				$groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
			}
			else
			{
				$groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
			}
		}
		$fieldvalue[]=$users_combo;  
		$fieldvalue[] = $groups_combo;
	}
	elseif($uitype == 51 || $uitype == 50 || $uitype == 73)
	{
		if($_REQUEST['convertmode'] != 'update_quote_val' && $_REQUEST['convertmode'] != 'update_so_val')
		{
			if(isset($_REQUEST['account_id']) && $_REQUEST['account_id'] != '')
				$value = $_REQUEST['account_id'];	
		}
		if($value != '') {		
			$account_name = getAccountName($value);	
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[]=$account_name;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 54)
	{
		$options = array();
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$pick_query="select * from vtiger_groups";
		$pickListResult = $adb->pquery($pick_query, array());
		$noofpickrows = $adb->num_rows($pickListResult);
		for($j = 0; $j < $noofpickrows; $j++)
		{
			$pickListValue=$adb->query_result($pickListResult,$j,"name");

			if($value == $pickListValue)
			{
				$chk_val = "selected";	
			}
			else
			{	
				$chk_val = '';	
			}
			$options[] = array($pickListValue => $chk_val );
		}
		$fieldvalue[] = $options;

	}
	elseif($uitype == 55 || $uitype == 255){
		require_once 'modules/PickList/PickListUtils.php';
		if($uitype==255){
			$fieldpermission = getFieldVisibilityPermission($module_name, $current_user->id,'firstname');
		}
		if($uitype == 255 && $fieldpermission == '0'){
			$fieldvalue[] = '';
		}else{
			$roleid=$current_user->roleid;
			$picklistValues = getAssignedPicklistValues('salutationtype', $roleid, $adb);
			$pickcount = 0;
			$salt_value = $col_fields["salutationtype"];
			foreach($picklistValues as $order=>$pickListValue){
				if($salt_value == trim($pickListValue)){
					$chk_val = "selected";
					$pickcount++;
				}else{
					$chk_val = '';
				}
				if(isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate'){
					$options[] = array(htmlentities(getTranslatedString($pickListValue),ENT_QUOTES,$default_charset),$pickListValue,$chk_val );	
				}else{
					$options[] = array(getTranslatedString($pickListValue),$pickListValue,$chk_val);
				}
			}
			if($pickcount == 0 && $salt_value != ''){
				$options[] =  array($app_strings['LBL_NOT_ACCESSIBLE'],$salt_value,'selected');
			}
			$fieldvalue [] = $options;
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $value;
	}elseif($uitype == 59){
		if($_REQUEST['module'] == 'HelpDesk')
		{
			if(isset($_REQUEST['product_id']) & $_REQUEST['product_id'] != '')
				$value = $_REQUEST['product_id'];
		}
		elseif(isset($_REQUEST['parent_id']) & $_REQUEST['parent_id'] != '')
			$value = $_REQUEST['parent_id'];

		if($value != '')
		{		
			$product_name = getProductName($value);	
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[]=$product_name;
		$fieldvalue[]=$value;
	}
	elseif($uitype == 63)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		if($value=='')
			$value=1;
		$options = array();
		$pick_query="select * from vtiger_duration_minutes order by sortorderid";
		$pickListResult = $adb->pquery($pick_query, array());
		$noofpickrows = $adb->num_rows($pickListResult);
		$salt_value = $col_fields["duration_minutes"];
		for($j = 0; $j < $noofpickrows; $j++)
		{
			$pickListValue=$adb->query_result($pickListResult,$j,"duration_minutes");

			if($salt_value == $pickListValue)
			{
				$chk_val = "selected";
			}
			else
			{
				$chk_val = '';
			}
			$options[$pickListValue] = $chk_val;
		}
		$fieldvalue[]=$value;
		$fieldvalue[]=$options;
	}
	elseif($uitype == 64)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$date_format = parse_calendardate($app_strings['NTC_DATE_FORMAT']);
		$fieldvalue[] = $value;
	}
	elseif($uitype == 156)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $value;	
		$fieldvalue[] = $is_admin;
	}
	elseif($uitype == 56)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $value;	
	}
	elseif($uitype == 57){
		if($value != ''){
			$contact_name = getContactName($value);
		}elseif(isset($_REQUEST['contact_id']) && $_REQUEST['contact_id'] != ''){
			if($_REQUEST['module'] == 'Contacts' && $fieldname = 'contact_id'){
				$contact_name = '';	
			}else{
				$value = $_REQUEST['contact_id'];
				$contact_name = getContactName($value);		
			}

		}

		//Checking for contacts duplicate

		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $contact_name;
		$fieldvalue[] = $value;
	}

	elseif($uitype == 58)
	{

		if($value != '')
		{
			$campaign_name = getCampaignName($value);
		}
		elseif(isset($_REQUEST['campaignid']) && $_REQUEST['campaignid'] != '')
		{
			if($_REQUEST['module'] == 'Campaigns' && $fieldname = 'campaignid')
			{
				$campaign_name = '';
			}
			else
			{
				$value = $_REQUEST['campaignid'];
				$campaign_name = getCampaignName($value);
			}

		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[]=$campaign_name;
		$fieldvalue[] = $value;
	}
	
	elseif($uitype == 61)
	{
		if($value != '')
		{
			$assigned_user_id = $value;
		}
		else
		{
			$assigned_user_id = $current_user->id;
		}
		if($module_name == 'Emails' && $col_fields['record_id'] != '')
		{
			$attach_result = $adb->pquery("select * from vtiger_seattachmentsrel where crmid = ?", array($col_fields['record_id']));
			//to fix the issue in mail attachment on forwarding mails
			if(isset($_REQUEST['forward']) && $_REQUEST['forward'] != '')
				global $att_id_list;
			for($ii=0;$ii < $adb->num_rows($attach_result);$ii++)
			{
				$attachmentid = $adb->query_result($attach_result,$ii,'attachmentsid');
				if($attachmentid != '')
				{
					$attachquery = "select * from vtiger_attachments where attachmentsid=?";
					$attachmentsname = $adb->query_result($adb->pquery($attachquery, array($attachmentid)),0,'name');
					if($attachmentsname != '')	
						$fieldvalue[$attachmentid] = '[ '.$attachmentsname.' ]';
					if(isset($_REQUEST['forward']) && $_REQUEST['forward'] != '')
						$att_id_list .= $attachmentid.';';
				}

			}
		}else
		{
			if($col_fields['record_id'] != '')
			{
				$attachmentid=$adb->query_result($adb->pquery("select * from vtiger_seattachmentsrel where crmid = ?", array($col_fields['record_id'])),0,'attachmentsid');
				if($col_fields[$fieldname] == '' && $attachmentid != '')
				{
					$attachquery = "select * from vtiger_attachments where attachmentsid=?";
					$value = $adb->query_result($adb->pquery($attachquery, array($attachmentid)),0,'name');
				}
			}
			if($value!='')
				$filename=' [ '.$value. ' ]';
			
			if($filename != '')
				$fieldvalue[] = $filename;
			if($value != '')
				$fieldvalue[] = $value;
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
	}
	elseif($uitype == 28){
		if($col_fields['record_id'] != '')
			{
				$attachmentid=$adb->query_result($adb->pquery("select * from vtiger_seattachmentsrel where crmid = ?", array($col_fields['record_id'])),0,'attachmentsid');
				if($col_fields[$fieldname] == '' && $attachmentid != '')
				{
					$attachquery = "select * from vtiger_attachments where attachmentsid=?";
					$value = $adb->query_result($adb->pquery($attachquery, array($attachmentid)),0,'name');
				}
			}
			if($value!='' && $module_name != 'Documents')
				$filename=' [ '.$value. ' ]';
			elseif($value != '' && $module_name == 'Documents')
				$filename= $value;
			if($filename != '')
				$fieldvalue[] = $filename;
			if($value != '')
				$fieldvalue[] = $value;
				
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);		
	}
	elseif($uitype == 69)
  	{
  		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
 		if( $col_fields['record_id'] != "") 
  		{
 		    //This query is for Products only
 		    if($module_name == 'Products')
 		    {
			    $query = 'select vtiger_attachments.path, vtiger_attachments.attachmentsid, vtiger_attachments.name ,vtiger_crmentity.setype from vtiger_products left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_products.productid inner join vtiger_attachments on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_attachments.attachmentsid where vtiger_crmentity.setype="Products Image" and productid=?';
 		    }
 		    else
		    {
			    	$query="select vtiger_attachments.*,vtiger_crmentity.setype from vtiger_attachments inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_attachments.attachmentsid where vtiger_crmentity.setype='Contacts Image' and vtiger_seattachmentsrel.crmid=?";
 		    }
 		    $result_image = $adb->pquery($query, array($col_fields['record_id']));
 		    for($image_iter=0;$image_iter < $adb->num_rows($result_image);$image_iter++)	
 		    {
			    $image_id_array[] = $adb->query_result($result_image,$image_iter,'attachmentsid');

			    //decode_html  - added to handle UTF-8   characters in file names
			    //urlencode    - added to handle special characters like #, %, etc.,
 			    $image_array[] = urlencode(decode_html($adb->query_result($result_image,$image_iter,'name')));
			    $image_orgname_array[] = decode_html($adb->query_result($result_image,$image_iter,'name'));

 			    $image_path_array[] = $adb->query_result($result_image,$image_iter,'path');	
 		    }
 		    if(is_array($image_array))
 			    for($img_itr=0;$img_itr<count($image_array);$img_itr++)
 			    {
 				    $fieldvalue[] = array('name'=>$image_array[$img_itr],'path'=>$image_path_array[$img_itr].$image_id_array[$img_itr]."_","orgname"=>$image_orgname_array[$img_itr]);
 			    }
 		    else
 			    $fieldvalue[] = '';
  		}
  		else
  			$fieldvalue[] = '';
  	}
	elseif($uitype == 62)
	{
		if(isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != '')
			$value = $_REQUEST['parent_id'];
		if($value != '')
			$parent_module = getSalesEntityType($value);
		if(isset($_REQUEST['account_id']) && $_REQUEST['account_id'] != '')
		{
			$parent_module = "Accounts";
			$value = $_REQUEST['account_id'];
		}
		if($parent_module != 'Contacts')
		{
			if($parent_module == "Leads")
			{
				$parent_name = getLeadName($value);
				$lead_selected = "selected";

			}
			elseif($parent_module == "Accounts")
			{
				$sql = "select * from  vtiger_account where accountid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name = $adb->query_result($result,0,"accountname");
				$account_selected = "selected";

			}
			elseif($parent_module == "Potentials")
			{
				$sql = "select * from  vtiger_potential where potentialid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name = $adb->query_result($result,0,"potentialname");
				$potential_selected = "selected";

			}
			elseif($parent_module == "Products")
			{
				$sql = "select * from  vtiger_products where productid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name= $adb->query_result($result,0,"productname");
				$product_selected = "selected";

			}
			elseif($parent_module == "PurchaseOrder")
			{
				$sql = "select * from  vtiger_purchaseorder where purchaseorderid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name= $adb->query_result($result,0,"subject");
				$porder_selected = "selected";

			}
			elseif($parent_module == "SalesOrder")
			{
				$sql = "select * from  vtiger_salesorder where salesorderid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name= $adb->query_result($result,0,"subject");
				$sorder_selected = "selected";

			}
			elseif($parent_module == "Invoice")
			{
				$sql = "select * from  vtiger_invoice where invoiceid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name= $adb->query_result($result,0,"subject");
				$invoice_selected = "selected";
			}
			elseif($parent_module == "Quotes")
			{
				$sql = "select * from  vtiger_quotes where quoteid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name= $adb->query_result($result,0,"subject");
				$quote_selected = "selected";
			}elseif($parent_module == "HelpDesk")
			{
				$sql = "select * from  vtiger_troubletickets where ticketid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name= $adb->query_result($result,0,"title");
				$ticket_selected = "selected";
			}
		}
		$editview_label[] = array($app_strings['COMBO_LEADS'],
                                          $app_strings['COMBO_ACCOUNTS'],
                                          $app_strings['COMBO_POTENTIALS'],
                                          $app_strings['COMBO_PRODUCTS'],
                                          $app_strings['COMBO_INVOICES'],
                                          $app_strings['COMBO_PORDER'],
                                          $app_strings['COMBO_SORDER'],
					  $app_strings['COMBO_QUOTES'],
					  $app_strings['COMBO_HELPDESK']
                                         );
                $editview_label[] = array($lead_selected,
                                          $account_selected,
					  $potential_selected,
                                          $product_selected,
                                          $invoice_selected,
                                          $porder_selected,
                                          $sorder_selected,
					  $quote_selected,
					  $ticket_selected
                                         );
                $editview_label[] = array("Leads&action=Popup","Accounts&action=Popup","Potentials&action=Popup","Products&action=Popup","Invoice&action=Popup","PurchaseOrder&action=Popup","SalesOrder&action=Popup","Quotes&action=Popup","HelpDesk&action=Popup");
		$fieldvalue[] =$parent_name;
		$fieldvalue[] =$value;

	}
	elseif($uitype == 66)
	{
		if(isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != '')
			$value = $_REQUEST['parent_id'];
		if($value != '')
			$parent_module = getSalesEntityType($value);
		// Check for vtiger_activity type if task orders to be added in select option
		$act_mode = $_REQUEST['activity_mode'];

		if($parent_module != "Contacts")
		{
			if($parent_module == "Leads")
			{
				$parent_name = getLeadName($value);
				$lead_selected = "selected";

			}
			elseif($parent_module == "Accounts")
			{
				$sql = "select * from  vtiger_account where accountid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name = $adb->query_result($result,0,"accountname");
				$account_selected = "selected";

			}
			elseif($parent_module == "Potentials")
			{
				$sql = "select * from  vtiger_potential where potentialid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name = $adb->query_result($result,0,"potentialname");
				$potential_selected = "selected";

			}
			elseif($parent_module == "HelpDesk")
			{
				$sql = "select title from vtiger_troubletickets where ticketid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name = $adb->query_result($result,0,"title");
				$ticket_selected = "selected";
			}

			elseif($act_mode == "Task")
			{
				if($parent_module == "PurchaseOrder")
				{
					$sql = "select * from vtiger_purchaseorder where purchaseorderid=?";
					$result = $adb->pquery($sql, array($value));
					$parent_name = $adb->query_result($result,0,"subject");
					$purchase_selected = "selected";
				}
				if($parent_module == "SalesOrder")
				{
					$sql = "select * from vtiger_salesorder where salesorderid=?";
					$result = $adb->pquery($sql, array($value));
					$parent_name = $adb->query_result($result,0,"subject");
					$sales_selected = "selected";
				}
				if($parent_module == "Invoice")
				{
					$sql = "select * from vtiger_invoice where invoiceid=?";
					$result = $adb->pquery($sql, array($value));
					$parent_name = $adb->query_result($result,0,"subject");
					$invoice_selected = "selected";
				}
				if($parent_module == "Campaigns")
				{
					$sql = "select campaignname from vtiger_campaign where campaignid=?";
					$result = $adb->pquery($sql, array($value));
					$parent_name = $adb->query_result($result,0,"campaignname");
					$campaign_selected = "selected";
				}
				if($parent_module == "Quotes")
				{
					$sql = "select * from  vtiger_quotes where quoteid=?";
					$result = $adb->pquery($sql, array($value));
					$parent_name = $adb->query_result($result,0,"subject");
					$quote_selected = "selected";
				}
			}
			$fieldvalue[] =$parent_name;
			$fieldvalue[] = $value;
		}
		$editview_label[0] = array(
			$app_strings['COMBO_LEADS'],
			$app_strings['COMBO_ACCOUNTS'],
			$app_strings['COMBO_POTENTIALS'],
		);
		$editview_label[1] = array(
			$lead_selected,
			$account_selected,
			$potential_selected
		);
		$editview_label[2] = array(
			"Leads&action=Popup",
			"Accounts&action=Popup",
			"Potentials&action=Popup"
		);

		if($act_mode == "Task")
                {
                        array_push($editview_label[0],
                                $app_strings['COMBO_QUOTES'],
                                $app_strings['COMBO_PORDER'],
                                $app_strings['COMBO_SORDER'],
                                $app_strings['COMBO_INVOICES'],
				$app_strings['COMBO_CAMPAIGNS'],
				$app_strings['COMBO_HELPDESK']
                        );
			array_push($editview_label[1],
                                $quote_selected,
                                $purchase_selected,
                                $sales_selected,
                                $invoice_selected,
				$campaign_selected,
				$ticket_selected
                        );
                        array_push($editview_label[2],"Quotes&action=Popup","PurchaseOrder&action=Popup","SalesOrder&action=Popup","Invoice&action=Popup","Campaigns&action=Popup","HelpDesk&action=Popup");
                }
		elseif($act_mode == "Events")
		{
			array_push($editview_label[0],$app_strings['COMBO_HELPDESK']);
			array_push($editview_label[1],$ticket_selected);
			array_push($editview_label[2],"HelpDesk&action=Popup");
		}
	}
	//added by rdhital/Raju for better email support
	elseif($uitype == 357)
	{
		$pmodule = $_REQUEST['pmodule'];
		if(empty($pmodule))
			$pmodule = $_REQUEST['par_module'];

		if($pmodule == 'Contacts')
		{
			$contact_selected = 'selected';
		}
		elseif($pmodule == 'Accounts')
		{
			$account_selected = 'selected';
		}
		elseif($pmodule == 'Leads')
		{
			$lead_selected = 'selected';
		}
		elseif($pmodule == 'Vendors')
		{
			$vendor_selected = 'selected';
		}
		elseif($pmodule == 'Users')
		{
			$user_selected = 'selected';
		}
		if(isset($_REQUEST['emailids']) && $_REQUEST['emailids'] != '')
		{
			$parent_id = $_REQUEST['emailids'];
			$parent_name='';

			$myids=explode("|",$parent_id);
			for ($i=0;$i<(count($myids)-1);$i++)
			{
				$realid=explode("@",$myids[$i]);
				$entityid=$realid[0];
				$nemail=count($realid);

				if ($pmodule=='Accounts'){
					require_once('modules/Accounts/Accounts.php');
					$myfocus = new Accounts();
					$myfocus->retrieve_entity_info($entityid,"Accounts");
					$fullname=br2nl($myfocus->column_fields['accountname']);
					$account_selected = 'selected';
				}
				elseif ($pmodule=='Contacts'){
					require_once('modules/Contacts/Contacts.php');
					$myfocus = new Contacts();
					$myfocus->retrieve_entity_info($entityid,"Contacts");
					$fname=br2nl($myfocus->column_fields['firstname']);
					$lname=br2nl($myfocus->column_fields['lastname']);
					$fullname=$lname.' '.$fname;
					$contact_selected = 'selected';
				}
				elseif ($pmodule=='Leads'){
					require_once('modules/Leads/Leads.php');
					$myfocus = new Leads();
					$myfocus->retrieve_entity_info($entityid,"Leads");
					$fname=br2nl($myfocus->column_fields['firstname']);
					$lname=br2nl($myfocus->column_fields['lastname']);
					$fullname=$lname.' '.$fname;
					$lead_selected = 'selected';
				}
				for ($j=1;$j<$nemail;$j++){
					$querystr='select columnname from vtiger_field where fieldid=? and vtiger_field.presence in (0,2)';
					$result=$adb->pquery($querystr, array($realid[$j]));
					$temp=$adb->query_result($result,0,'columnname');
					$temp1=br2nl($myfocus->column_fields[$temp]);

					//Modified to display the entities in red which don't have email id
					if(!empty($temp_parent_name) && strlen($temp_parent_name) > 150)
					{
						$parent_name .= '<br>';
						$temp_parent_name = '';
					}

					if($temp1 != '')
					{
						$parent_name .= $fullname.'&lt;'.$temp1.'&gt;; ';
						$temp_parent_name .= $fullname.'&lt;'.$temp1.'&gt;; ';
					}
					else
					{
						$parent_name .= "<b style='color:red'>".$fullname.'&lt;'.$temp1.'&gt;; '."</b>";
						$temp_parent_name .= "<b style='color:red'>".$fullname.'&lt;'.$temp1.'&gt;; '."</b>";
					}

				}
			}
		}
		else
		{
			if($_REQUEST['record'] != '' && $_REQUEST['record'] != NULL)
			{
				$parent_name='';
				$parent_id='';
				$myemailid= $_REQUEST['record'];
				$mysql = "select crmid from vtiger_seactivityrel where activityid=?";
				$myresult = $adb->pquery($mysql, array($myemailid));
				$mycount=$adb->num_rows($myresult);
				if($mycount >0)
				{
					for ($i=0;$i<$mycount;$i++)
					{	
						$mycrmid=$adb->query_result($myresult,$i,'crmid');
						$parent_module = getSalesEntityType($mycrmid);
						if($parent_module == "Leads")
						{
							$sql = "select firstname,lastname,email from vtiger_leaddetails where leadid=?";
							$result = $adb->pquery($sql, array($mycrmid));
							$full_name = getFullNameFromQResult($result,0,"Leads");
							$myemail=$adb->query_result($result,0,"email");
							$parent_id .=$mycrmid.'@0|' ; //make it such that the email adress sent is remebered and only that one is retrived
							$parent_name .= $full_name.'<'.$myemail.'>; ';
							$lead_selected = 'selected';
						}
						elseif($parent_module == "Contacts")
						{
							$sql = "select * from  vtiger_contactdetails where contactid=?";
							$result = $adb->pquery($sql, array($mycrmid));
							$full_name = getFullNameFromQResult($result,0,"Contacts");
							$myemail=$adb->query_result($result,0,"email");
							$parent_id .=$mycrmid.'@0|'  ;//make it such that the email adress sent is remebered and only that one is retrived
							$parent_name .= $full_name.'<'.$myemail.'>; ';
							$contact_selected = 'selected';
						}
						elseif($parent_module == "Accounts")
						{
							$sql = "select * from  vtiger_account where accountid=?";
							$result = $adb->pquery($sql, array($mycrmid));
							$account_name = $adb->query_result($result,0,"accountname");
							$myemail=$adb->query_result($result,0,"email1");
							$parent_id .=$mycrmid.'@0|'  ;//make it such that the email adress sent is remebered and only that one is retrived
							$parent_name .= $account_name.'<'.$myemail.'>; ';
							$account_selected = 'selected';
						}elseif($parent_module == "Users")
						{
							$sql = "select user_name,email1 from vtiger_users where id=?";
							$result = $adb->pquery($sql, array($mycrmid));
							$account_name = $adb->query_result($result,0,"user_name");
							$myemail=$adb->query_result($result,0,"email1");
							$parent_id .=$mycrmid.'@0|'  ;//make it such that the email adress sent is remebered and only that one is retrived
							$parent_name .= $account_name.'<'.$myemail.'>; ';
							$user_selected = 'selected';
						}
						elseif($parent_module == "Vendors")
						{
							$sql = "select * from  vtiger_vendor where vendorid=?";
							$result = $adb->pquery($sql, array($mycrmid));
							$vendor_name = $adb->query_result($result,0,"vendorname");
							$myemail=$adb->query_result($result,0,"email");
							$parent_id .=$mycrmid.'@0|'  ;//make it such that the email adress sent is remebered and only that one is retrived
							$parent_name .= $vendor_name.'<'.$myemail.'>; ';
							$vendor_selected = 'selected';
						}
					}
				}
			}
			$custfld .= '<td width="20%" class="dataLabel">'.$app_strings['To'].'&nbsp;</td>';
			$custfld .= '<td width="90%" colspan="3"><input name="parent_id" type="hidden" value="'.$parent_id.'"><textarea readonly name="parent_name" cols="70" rows="2">'.$parent_name.'</textarea>&nbsp;<select name="parent_type" >';
			$custfld .= '<OPTION value="Contacts" selected>'.$app_strings['COMBO_CONTACTS'].'</OPTION>';
			$custfld .= '<OPTION value="Accounts" >'.$app_strings['COMBO_ACCOUNTS'].'</OPTION>';
			$custfld .= '<OPTION value="Leads" >'.$app_strings['COMBO_LEADS'].'</OPTION>';
			$custfld .= '<OPTION value="Vendors" >'.$app_strings['COMBO_VENDORS'].'</OPTION></select><img src="' . vtiger_imageurl('select.gif', $theme) . '" alt="Select" title="Select" LANGUAGE=javascript onclick=\'$log->debug("Exiting getOutputHtml method ..."); return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&popuptype=set_$log->debug("Exiting getOutputHtml method ..."); return_emails&form=EmailEditView&form_submit=false","test","width=600,height=400,resizable=1,scrollbars=1,top=150,left=200");\' align="absmiddle" style=\'cursor:hand;cursor:pointer\'>&nbsp;<input type="image" src="' . vtiger_imageurl('clear_field.gif', $theme) . '" alt="Clear" title="Clear" LANGUAGE=javascript onClick="this.form.parent_id.value=\'\';this.form.parent_name.value=\'\';$log->debug("Exiting getOutputHtml method ..."); return false;" align="absmiddle" style=\'cursor:hand;cursor:pointer\'></td>';
			$editview_label[] = array(	 
					'Contacts'=>$contact_selected,
					'Accounts'=>$account_selected,
					'Vendors'=>$vendor_selected,
					'Leads'=>$lead_selected,
					'Users'=>$user_selected
					);
			$fieldvalue[] =$parent_name;
			$fieldvalue[] = $parent_id;
		}
	}
	//end of rdhital/Raju
	elseif($uitype == 68)
	{
		if(isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != '')
			$value = $_REQUEST['parent_id'];

		if($value != '')
		{
			$parent_module = getSalesEntityType($value);
			if($parent_module == "Contacts")
			{
				$parent_name = getContactName($value);
				$contact_selected = "selected";

			}
			elseif($parent_module == "Accounts")
			{
				$sql = "select * from  vtiger_account where accountid=?";
				$result = $adb->pquery($sql, array($value));
				$parent_name = $adb->query_result($result,0,"accountname");
				$account_selected = "selected";

			}
			else
			{
				$parent_name = "";
				$value = "";
			}
				
		}
		$editview_label[] = array($app_strings['COMBO_CONTACTS'],
                                        $app_strings['COMBO_ACCOUNTS']
                                        );
                $editview_label[] = array($contact_selected,
                                        $account_selected
                                        );
                $editview_label[] = array("Contacts","Accounts");
		$fieldvalue[] = $parent_name;
		$fieldvalue[] = $value;
	}
	
	elseif($uitype == 71 || $uitype == 72)
	{
		if($col_fields['record_id'] != '' && $fieldname == 'unit_price') {
			$rate_symbol=getCurrencySymbolandCRate(getProductBaseCurrency($col_fields['record_id'],$module_name));
			$fieldvalue[] = $value;			
		} else {
			$currency_id = fetchCurrency($current_user->id);
			$rate_symbol=getCurrencySymbolandCRate($currency_id);
			$rate = $rate_symbol['rate'];
			$fieldvalue[] = convertFromDollar($value,$rate);
		}
        $currency = $rate_symbol['symbol'];
		$editview_label[]=getTranslatedString($fieldlabel, $module_name).': ('.$currency.')';		
	}
	elseif($uitype == 75 || $uitype ==81)
	{
		if($value != '')
		{
			$vendor_name = getVendorName($value);
		}
		elseif(isset($_REQUEST['vendor_id']) && $_REQUEST['vendor_id'] != '')
		{
			$value = $_REQUEST['vendor_id'];
			$vendor_name = getVendorName($value);
		}		 	
		$pop_type = 'specific';
		if($uitype == 81)
		{
			$pop_type = 'specific_vendor_address';
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $vendor_name;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 76)
	{
		if($value != '')
		{
			$potential_name = getPotentialName($value);
		}
		elseif(isset($_REQUEST['potential_id']) && $_REQUEST['potential_id'] != '')
		{
			$value = $_REQUEST['potental_id'];
			$potential_name = getPotentialName($value);
		}		 	
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $potential_name;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 78)
	{
		if($value != '')
		{
			$quote_name = getQuoteName($value);
		}
		elseif(isset($_REQUEST['quote_id']) && $_REQUEST['quote_id'] != '')
		{
			$value = $_REQUEST['quote_id'];
			$potential_name = getQuoteName($value);
		}		 	
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $quote_name;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 79)
	{
		if($value != '')
		{
			$purchaseorder_name = getPoName($value);
		}
		elseif(isset($_REQUEST['purchaseorder_id']) && $_REQUEST['purchaseorder_id'] != '')
		{
			$value = $_REQUEST['purchaseorder_id'];
			$purchaseorder_name = getPoName($value);
		}		 	
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $purchaseorder_name;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 80)
	{
		if($value != '')
		{
			$salesorder_name = getSoName($value);
		}
		elseif(isset($_REQUEST['salesorder_id']) && $_REQUEST['salesorder_id'] != '')
		{
			$value = $_REQUEST['salesorder_id'];
			$salesorder_name = getSoName($value);
		}		 	
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $salesorder_name;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 30)
	{
		$rem_days = 0;
		$rem_hrs = 0;
		$rem_min = 0;
		if($value!='')
			$SET_REM = "CHECKED";
		$rem_days = floor($col_fields[$fieldname]/(24*60));
		$rem_hrs = floor(($col_fields[$fieldname]-$rem_days*24*60)/60);
		$rem_min = ($col_fields[$fieldname]-$rem_days*24*60)%60;
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$day_options = getReminderSelectOption(0,31,'remdays',$rem_days);
		$hr_options = getReminderSelectOption(0,23,'remhrs',$rem_hrs);
		$min_options = getReminderSelectOption(1,59,'remmin',$rem_min);
		$fieldvalue[] = array(array(0,32,'remdays',getTranslatedString('LBL_DAYS'),$rem_days),array(0,24,'remhrs',getTranslatedString('LBL_HOURS'),$rem_hrs),array(1,60,'remmin',getTranslatedString('LBL_MINUTES').'&nbsp;&nbsp;'.getTranslatedString('LBL_BEFORE_EVENT'),$rem_min));
		$fieldvalue[] = array($SET_REM,getTranslatedString('LBL_YES'),getTranslatedString('LBL_NO'));
		$SET_REM = '';
	}
	elseif($uitype == 115)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$pick_query="select * from vtiger_" . $adb->sql_escape_string($fieldname);
		$pickListResult = $adb->pquery($pick_query, array());
		$noofpickrows = $adb->num_rows($pickListResult);

		//Mikecrowe fix to correctly default for custom pick lists
		$options = array();
		$found = false;
		for($j = 0; $j < $noofpickrows; $j++)
		{
			$pickListValue=$adb->query_result($pickListResult,$j,strtolower($fieldname));

			if($value == $pickListValue)
			{
				$chk_val = "selected";	
				$found = true;
			}
			else
			{	
				$chk_val = '';
			}
			$options[] = array(getTranslatedString($pickListValue),$pickListValue,$chk_val );	
		}
		$fieldvalue [] = $options;
		$fieldvalue [] = $is_admin;
	}
	elseif($uitype == 116 || $uitype == 117)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$pick_query="select * from vtiger_currency_info where currency_status = 'Active' and deleted=0";
		$pickListResult = $adb->pquery($pick_query, array());
		$noofpickrows = $adb->num_rows($pickListResult);

		//Mikecrowe fix to correctly default for custom pick lists
		$options = array();
		$found = false;
		for($j = 0; $j < $noofpickrows; $j++)
		{
			$pickListValue=$adb->query_result($pickListResult,$j,'currency_name');
			$currency_id=$adb->query_result($pickListResult,$j,'id');
			if($value == $currency_id)
			{
				$chk_val = "selected";	
				$found = true;
			}
			else
			{	
				$chk_val = '';
			}
			$options[$currency_id] = array($pickListValue=>$chk_val );	
		}
		$fieldvalue [] = $options;
		$fieldvalue [] = $is_admin;
	}
	elseif($uitype ==98)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[]=$value;
        	$fieldvalue[]=getRoleName($value);
		$fieldvalue[]=$is_admin;
	}
	elseif($uitype == 105)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		 if( isset( $col_fields['record_id']) && $col_fields['record_id'] != '') {
			$query = "select vtiger_attachments.path, vtiger_attachments.name from vtiger_contactdetails left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_contactdetails.contactid inner join vtiger_attachments on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid where vtiger_contactdetails.imagename=vtiger_attachments.name and contactid=?";
			$result_image = $adb->pquery($query, array($col_fields['record_id']));
			for($image_iter=0;$image_iter < $adb->num_rows($result_image);$image_iter++)	
			{
				$image_array[] = $adb->query_result($result_image,$image_iter,'name');	
				$image_path_array[] = $adb->query_result($result_image,$image_iter,'path');	
			}
		}
		if(is_array($image_array))
			for($img_itr=0;$img_itr<count($image_array);$img_itr++)
			{
				$fieldvalue[] = array('name'=>$image_array[$img_itr],'path'=>$image_path_array[$img_itr]);
			}
		else
			$fieldvalue[] = '';
	}elseif($uitype == 101)
	{
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
        $fieldvalue[] = getUserName($value);
        $fieldvalue[] = $value;
	}
	elseif($uitype == 26){
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		$folderid=$col_fields['folderid'];
		$foldername_query = 'select foldername from vtiger_attachmentsfolder where folderid = ?';
		$res = $adb->pquery($foldername_query,array($folderid));
		$foldername = $adb->query_result($res,0,'foldername');
		if($foldername != '' && $folderid != ''){
			$fldr_name[$folderid]=$foldername;
			}
		$sql="select foldername,folderid from vtiger_attachmentsfolder order by foldername";
		$res=$adb->pquery($sql,array());
		for($i=0;$i<$adb->num_rows($res);$i++)
			{
				$fid=$adb->query_result($res,$i,"folderid");
				$fldr_name[$fid]=$adb->query_result($res,$i,"foldername");	
			}
		$fieldvalue[] = $fldr_name;
		}
	elseif($uitype == 27){
				if($value == 'I'){
					$internal_selected = "selected";
					$filename = $col_fields['filename'];
				}
				else{
					$external_selected = "selected";
					$filename = $col_fields['filename'];
				}
				$editview_label[] = array(getTranslatedString('Internal'),
                                        getTranslatedString('External')
                                        );
                $editview_label[] = array($internal_selected,
                                        $external_selected
                                        );
                $editview_label[] = array("I","E");
                $editview_label[] = getTranslatedString($fieldlabel, $module_name);
                $fieldvalue[] = $value;
                $fieldvalue[] = $filename;
	}
	else
	{
		//Added condition to set the subject if click Reply All from web mail
		if($_REQUEST['module'] == 'Emails' && $_REQUEST['mg_subject'] != '')
		{
			$value = $_REQUEST['mg_subject'];
		}
		$editview_label[]=getTranslatedString($fieldlabel, $module_name);
		if($uitype == 1 && ($fieldname=='expectedrevenue' || $fieldname=='budgetcost' || $fieldname=='actualcost' || $fieldname=='expectedroi' || $fieldname=='actualroi' ) && ($module_name=='Campaigns'))
		{
			$rate_symbol = getCurrencySymbolandCRate($user_info['currency_id']);
			$fieldvalue[] = convertFromDollar($value,$rate_symbol['rate']);
		}
		elseif($fieldname == 'fileversion'){
			if(empty($value)){
				$value = '';
			}
			else{
				$fieldvalue[] = $value;
			}
		}
		else
			$fieldvalue[] = $value;
	}

	// Mike Crowe Mod --------------------------------------------------------force numerics right justified.
	if ( !preg_match("/id=/i",$custfld) )
		$custfld = preg_replace("/<input/iS","<input id='$fieldname' ",$custfld);

	if ( in_array($uitype,array(71,72,7,9,90)) )
	{
		$custfld = preg_replace("/<input/iS","<input align=right ",$custfld);
	}
	$final_arr[]=$ui_type;
	$final_arr[]=$editview_label;
	$final_arr[]=$editview_fldname;
	$final_arr[]=$fieldvalue;
	$type_of_data  = explode('~',$typeofdata);
	$final_arr[]=$type_of_data[1];
	$log->debug("Exiting getOutputHtml method ...");
	return $final_arr;
}

/** This function returns the vtiger_invoice object populated with the details from sales order object.
* Param $focus - Invoice object
* Param $so_focus - Sales order focus
* Param $soid - sales order id
* Return type is an object array
*/

function getConvertSoToInvoice($focus,$so_focus,$soid)
{
	global $log,$current_user; 
	$log->debug("Entering getConvertSoToInvoice(".get_class($focus).",".get_class($so_focus).",".$soid.") method ...");
    $log->info("in getConvertSoToInvoice ".$soid);
    $xyz=array('bill_street','bill_city','bill_code','bill_pobox','bill_country','bill_state','ship_street','ship_city','ship_code','ship_pobox','ship_country','ship_state');
	for($i=0;$i<count($xyz);$i++){
		if (getFieldVisibilityPermission('SalesOrder', $current_user->id,$xyz[$i]) == '0'){
			$so_focus->column_fields[$xyz[$i]] = $so_focus->column_fields[$xyz[$i]];
		}
		else
			$so_focus->column_fields[$xyz[$i]] = '';
	}
	$focus->column_fields['salesorder_id'] = $soid;
	$focus->column_fields['subject'] = $so_focus->column_fields['subject'];
	$focus->column_fields['customerno'] = $so_focus->column_fields['customerno'];
	$focus->column_fields['duedate'] = $so_focus->column_fields['duedate'];
	$focus->column_fields['contact_id'] = $so_focus->column_fields['contact_id'];//to include contact name in Invoice
	$focus->column_fields['account_id'] = $so_focus->column_fields['account_id'];
	$focus->column_fields['exciseduty'] = $so_focus->column_fields['exciseduty'];
	$focus->column_fields['salescommission'] = $so_focus->column_fields['salescommission'];
	$focus->column_fields['purchaseorder'] = $so_focus->column_fields['purchaseorder'];
	$focus->column_fields['bill_street'] = $so_focus->column_fields['bill_street'];
	$focus->column_fields['ship_street'] = $so_focus->column_fields['ship_street'];
	$focus->column_fields['bill_city'] = $so_focus->column_fields['bill_city'];
	$focus->column_fields['ship_city'] = $so_focus->column_fields['ship_city'];
	$focus->column_fields['bill_state'] = $so_focus->column_fields['bill_state'];
	$focus->column_fields['ship_state'] = $so_focus->column_fields['ship_state'];
	$focus->column_fields['bill_code'] = $so_focus->column_fields['bill_code'];
	$focus->column_fields['ship_code'] = $so_focus->column_fields['ship_code'];
	$focus->column_fields['bill_country'] = $so_focus->column_fields['bill_country'];
	$focus->column_fields['ship_country'] = $so_focus->column_fields['ship_country'];
	$focus->column_fields['bill_pobox'] = $so_focus->column_fields['bill_pobox'];
	$focus->column_fields['ship_pobox'] = $so_focus->column_fields['ship_pobox'];
	$focus->column_fields['description'] = $so_focus->column_fields['description'];
	$focus->column_fields['terms_conditions'] = $so_focus->column_fields['terms_conditions'];
    $focus->column_fields['currency_id'] = $so_focus->column_fields['currency_id'];
    $focus->column_fields['conversion_rate'] = $so_focus->column_fields['conversion_rate'];

	$log->debug("Exiting getConvertSoToInvoice method ...");
	return $focus;

}

/** This function returns the vtiger_invoice object populated with the details from quote object.
* Param $focus - Invoice object
* Param $quote_focus - Quote order focus
* Param $quoteid - quote id
* Return type is an object array
*/


function getConvertQuoteToInvoice($focus,$quote_focus,$quoteid)
{
	global $log,$current_user;
	$log->debug("Entering getConvertQuoteToInvoice(".get_class($focus).",".get_class($quote_focus).",".$quoteid.") method ...");
        $log->info("in getConvertQuoteToInvoice ".$quoteid);
    $xyz=array('bill_street','bill_city','bill_code','bill_pobox','bill_country','bill_state','ship_street','ship_city','ship_code','ship_pobox','ship_country','ship_state');
	for($i=0;$i<12;$i++){
		if (getFieldVisibilityPermission('Quotes', $current_user->id,$xyz[$i]) == '0'){
			$quote_focus->column_fields[$xyz[$i]] = $quote_focus->column_fields[$xyz[$i]];
		}
		else
			$quote_focus->column_fields[$xyz[$i]] = '';
	}
	$focus->column_fields['subject'] = $quote_focus->column_fields['subject'];
	$focus->column_fields['account_id'] = $quote_focus->column_fields['account_id'];
	$focus->column_fields['bill_street'] = $quote_focus->column_fields['bill_street'];
	$focus->column_fields['ship_street'] = $quote_focus->column_fields['ship_street'];
	$focus->column_fields['bill_city'] = $quote_focus->column_fields['bill_city'];
	$focus->column_fields['ship_city'] = $quote_focus->column_fields['ship_city'];
	$focus->column_fields['bill_state'] = $quote_focus->column_fields['bill_state'];
	$focus->column_fields['ship_state'] = $quote_focus->column_fields['ship_state'];
	$focus->column_fields['bill_code'] = $quote_focus->column_fields['bill_code'];
	$focus->column_fields['ship_code'] = $quote_focus->column_fields['ship_code'];
	$focus->column_fields['bill_country'] = $quote_focus->column_fields['bill_country'];
	$focus->column_fields['ship_country'] = $quote_focus->column_fields['ship_country'];
	$focus->column_fields['bill_pobox'] = $quote_focus->column_fields['bill_pobox'];
	$focus->column_fields['ship_pobox'] = $quote_focus->column_fields['ship_pobox'];
	$focus->column_fields['description'] = $quote_focus->column_fields['description'];
	$focus->column_fields['terms_conditions'] = $quote_focus->column_fields['terms_conditions'];
    $focus->column_fields['currency_id'] = $quote_focus->column_fields['currency_id'];
    $focus->column_fields['conversion_rate'] = $quote_focus->column_fields['conversion_rate'];

	$log->debug("Exiting getConvertQuoteToInvoice method ...");
	return $focus;

}

/** This function returns the sales order object populated with the details from quote object.
* Param $focus - Sales order object
* Param $quote_focus - Quote order focus
* Param $quoteid - quote id
* Return type is an object array
*/

function getConvertQuoteToSoObject($focus,$quote_focus,$quoteid)
{
	global $log,$current_user;
	$log->debug("Entering getConvertQuoteToSoObject(".get_class($focus).",".get_class($quote_focus).",".$quoteid.") method ...");
        $log->info("in getConvertQuoteToSoObject ".$quoteid);
	    $xyz=array('bill_street','bill_city','bill_code','bill_pobox','bill_country','bill_state','ship_street','ship_city','ship_code','ship_pobox','ship_country','ship_state');
		for($i=0;$i<12;$i++){
			if (getFieldVisibilityPermission('Quotes', $current_user->id,$xyz[$i]) == '0'){
				$quote_focus->column_fields[$xyz[$i]] = $quote_focus->column_fields[$xyz[$i]];
			}
			else
				$quote_focus->column_fields[$xyz[$i]] = '';
		}
        $focus->column_fields['quote_id'] = $quoteid;
        $focus->column_fields['subject'] = $quote_focus->column_fields['subject'];
        $focus->column_fields['contact_id'] = $quote_focus->column_fields['contact_id'];
        $focus->column_fields['potential_id'] = $quote_focus->column_fields['potential_id'];
        $focus->column_fields['account_id'] = $quote_focus->column_fields['account_id'];
        $focus->column_fields['carrier'] = $quote_focus->column_fields['carrier'];
        $focus->column_fields['bill_street'] = $quote_focus->column_fields['bill_street'];
        $focus->column_fields['ship_street'] = $quote_focus->column_fields['ship_street'];
        $focus->column_fields['bill_city'] = $quote_focus->column_fields['bill_city'];
        $focus->column_fields['ship_city'] = $quote_focus->column_fields['ship_city'];
        $focus->column_fields['bill_state'] = $quote_focus->column_fields['bill_state'];
        $focus->column_fields['ship_state'] = $quote_focus->column_fields['ship_state'];
        $focus->column_fields['bill_code'] = $quote_focus->column_fields['bill_code'];
        $focus->column_fields['ship_code'] = $quote_focus->column_fields['ship_code'];
        $focus->column_fields['bill_country'] = $quote_focus->column_fields['bill_country'];
        $focus->column_fields['ship_country'] = $quote_focus->column_fields['ship_country'];
        $focus->column_fields['bill_pobox'] = $quote_focus->column_fields['bill_pobox'];
        $focus->column_fields['ship_pobox'] = $quote_focus->column_fields['ship_pobox'];
		$focus->column_fields['description'] = $quote_focus->column_fields['description'];
        $focus->column_fields['terms_conditions'] = $quote_focus->column_fields['terms_conditions'];
        $focus->column_fields['currency_id'] = $quote_focus->column_fields['currency_id'];
        $focus->column_fields['conversion_rate'] = $quote_focus->column_fields['conversion_rate'];

	$log->debug("Exiting getConvertQuoteToSoObject method ...");
        return $focus;

}

/** This function returns the detailed list of vtiger_products associated to a given entity or a record.
* Param $module - module name
* Param $focus - module object
* Param $seid - sales entity id
* Return type is an object array
*/


function getAssociatedProducts($module,$focus,$seid='')
{
	global $log;
	$log->debug("Entering getAssociatedProducts(".$module.",".get_class($focus).",".$seid."='') method ...");
	global $adb;
	$output = '';
	global $theme,$current_user;
	
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	$product_Detail = Array();
	
	// DG 15 Aug 2006
	// Add "ORDER BY sequence_no" to retain add order on all inventoryproductrel items
	
	if($module == 'Quotes' || $module == 'PurchaseOrder' || $module == 'SalesOrder' || $module == 'Invoice')
	{
		$query="SELECT 
					case when vtiger_products.productid != '' then vtiger_products.productname else vtiger_service.servicename end as productname,
 		            case when vtiger_products.productid != '' then vtiger_products.productcode else vtiger_service.service_no end as productcode, 
					case when vtiger_products.productid != '' then vtiger_products.unit_price else vtiger_service.unit_price end as unit_price,									
 		            case when vtiger_products.productid != '' then vtiger_products.qtyinstock else 'NA' end as qtyinstock,
 		            case when vtiger_products.productid != '' then 'Products' else 'Services' end as entitytype,
 		                        vtiger_inventoryproductrel.listprice, 
 		                        vtiger_inventoryproductrel.description AS product_description, 
 		                        vtiger_inventoryproductrel.* 
 	                            FROM vtiger_inventoryproductrel 
 		                        LEFT JOIN vtiger_products 
 		                                ON vtiger_products.productid=vtiger_inventoryproductrel.productid 
 		                        LEFT JOIN vtiger_service 
 		                                ON vtiger_service.serviceid=vtiger_inventoryproductrel.productid 
 		                        WHERE id=?
 		                        ORDER BY sequence_no"; 
			$params = array($focus->id);
	}
	elseif($module == 'Potentials')
	{
		$query="SELECT 
 		                        vtiger_products.productname, 
 		                        vtiger_products.productcode,
 		                        vtiger_products.unit_price, 
 		                        vtiger_products.qtyinstock, 
 		                        vtiger_seproductsrel.*,
 		                        vtiger_crmentity.description AS product_description 
 		                        FROM vtiger_products  
 		                        INNER JOIN vtiger_crmentity 
 		                                ON vtiger_crmentity.crmid=vtiger_products.productid 
 		                        INNER JOIN vtiger_seproductsrel 
 		                                ON vtiger_seproductsrel.productid=vtiger_products.productid 
 		                        WHERE vtiger_seproductsrel.crmid=?";
			$params = array($seid);
	}
	elseif($module == 'Products')
	{
		$query="SELECT 
 		                        vtiger_products.productid, 
 		                        vtiger_products.productcode, 
 		                        vtiger_products.productname, 
 		                        vtiger_products.unit_price, 
 		                        vtiger_products.qtyinstock, 
 		                        vtiger_crmentity.description AS product_description,
 		                        'Products' AS entitytype  
 		                        FROM vtiger_products  
 		                        INNER JOIN vtiger_crmentity 
 		                                ON vtiger_crmentity.crmid=vtiger_products.productid 
 		                        WHERE vtiger_crmentity.deleted=0 
 		                                AND productid=?";
			$params = array($seid);
	}
	elseif($module == 'Services')
	{
		$query="SELECT 
 		                        vtiger_service.serviceid AS productid, 
 		                        'NA' AS productcode, 
 		                        vtiger_service.servicename AS productname, 
 		                        vtiger_service.unit_price AS unit_price, 
 		                        'NA' AS qtyinstock, 
 		                        vtiger_crmentity.description AS product_description, 
 		                       	'Services' AS entitytype
 								FROM vtiger_service  
 		                        INNER JOIN vtiger_crmentity 
 		                                ON vtiger_crmentity.crmid=vtiger_service.serviceid 
 		                        WHERE vtiger_crmentity.deleted=0 
 		                                AND serviceid=?";
			$params = array($seid);
	}

	$result = $adb->pquery($query, $params);
	$num_rows=$adb->num_rows($result);
	for($i=1;$i<=$num_rows;$i++)
	{
		$hdnProductId = $adb->query_result($result,$i-1,'productid');
		$hdnProductcode = $adb->query_result($result,$i-1,'productcode');
		$productname=$adb->query_result($result,$i-1,'productname');
		$productdescription=$adb->query_result($result,$i-1,'product_description');
		$comment=$adb->query_result($result,$i-1,'comment');
		$qtyinstock=$adb->query_result($result,$i-1,'qtyinstock');
		$qty=$adb->query_result($result,$i-1,'quantity');
		$unitprice=$adb->query_result($result,$i-1,'unit_price');
		$listprice=$adb->query_result($result,$i-1,'listprice');
		$entitytype=$adb->query_result($result,$i-1,'entitytype');
		if (!empty($entitytype)) {
			$product_Detail[$i]['entityType'.$i]=$entitytype;
		}

		if($listprice == '')
			$listprice = $unitprice;
		if($qty =='')
			$qty = 1;

		//calculate productTotal
		$productTotal = $qty*$listprice;

		//Delete link in First column
		if($i != 1)
		{
			$product_Detail[$i]['delRow'.$i]="Del";
		}
		if(empty($focus->mode) && $seid!=''){
			$sub_prod_query = $adb->pquery("SELECT crmid as prod_id from vtiger_seproductsrel WHERE productid=? AND setype='Products'",array($seid));
		} else {
			$sub_prod_query = $adb->pquery("SELECT productid as prod_id from vtiger_inventorysubproductrel WHERE id=? AND sequence_no=?",array($focus->id,$i));
		}
		$subprodid_str='';
		$subprodname_str='';
		$subProductArray = array();
		if($adb->num_rows($sub_prod_query)>0){
			for($j=0;$j<$adb->num_rows($sub_prod_query);$j++){
				$sprod_id = $adb->query_result($sub_prod_query,$j,'prod_id');
				$sprod_name = $subProductArray[] = getProductName($sprod_id);
				$str_sep = "";
				if($j>0) $str_sep = ":";
				$subprodid_str .= $str_sep.$sprod_id;
				$subprodname_str .= $str_sep." - ".$sprod_name;
			}
		}

		$subprodname_str = str_replace(":","<br>",$subprodname_str);

		$product_Detail[$i]['subProductArray'.$i] = $subProductArray;
		$product_Detail[$i]['hdnProductId'.$i] = $hdnProductId;
		$product_Detail[$i]['productName'.$i]= from_html($productname);
		/* Added to fix the issue Product Pop-up name display*/
		if($_REQUEST['action'] == 'CreateSOPDF' || $_REQUEST['action'] == 'CreatePDF' || $_REQUEST['action'] == 'SendPDFMail')
			$product_Detail[$i]['productName'.$i]= htmlspecialchars($product_Detail[$i]['productName'.$i]);
		$product_Detail[$i]['hdnProductcode'.$i] = $hdnProductcode;
		$product_Detail[$i]['productDescription'.$i]= from_html($productdescription);
		if($module == 'Potentials' || $module == 'Products' || $module == 'Services') {
			$product_Detail[$i]['comment'.$i]= $productdescription;
		}else {
            $product_Detail[$i]['comment'.$i]= $comment;
		}

		if($module != 'PurchaseOrder' && $focus->object_name != 'Order')
		{
			$product_Detail[$i]['qtyInStock'.$i]=$qtyinstock;
		}
		$product_Detail[$i]['qty'.$i]=$qty;
		$product_Detail[$i]['listPrice'.$i]=$listprice;
		$product_Detail[$i]['unitPrice'.$i]=$unitprice;
		$product_Detail[$i]['productTotal'.$i]=$productTotal;
		$product_Detail[$i]['subproduct_ids'.$i]=$subprodid_str;
		$product_Detail[$i]['subprod_names'.$i]=$subprodname_str;
		$discount_percent=$adb->query_result($result,$i-1,'discount_percent');
		$discount_amount=$adb->query_result($result,$i-1,'discount_amount');
		$discountTotal = '0.00';
		//Based on the discount percent or amount we will show the discount details

		//To avoid NaN javascript error, here we assign 0 initially to' %of price' and 'Direct Price reduction'(for Each Product)
		$product_Detail[$i]['discount_percent'.$i] = 0;
		$product_Detail[$i]['discount_amount'.$i] = 0;

		if($discount_percent != 'NULL' && $discount_percent != '')
		{
			$product_Detail[$i]['discount_type'.$i] = "percentage";
			$product_Detail[$i]['discount_percent'.$i] = $discount_percent;
			$product_Detail[$i]['checked_discount_percent'.$i] = ' checked';
			$product_Detail[$i]['style_discount_percent'.$i] = ' style="visibility:visible"';
			$product_Detail[$i]['style_discount_amount'.$i] = ' style="visibility:hidden"';
			$discountTotal = $productTotal*$discount_percent/100;
		}
		elseif($discount_amount != 'NULL' && $discount_amount != '')
		{
			$product_Detail[$i]['discount_type'.$i] = "amount";
			$product_Detail[$i]['discount_amount'.$i] = $discount_amount;
			$product_Detail[$i]['checked_discount_amount'.$i] = ' checked';
			$product_Detail[$i]['style_discount_amount'.$i] = ' style="visibility:visible"';
			$product_Detail[$i]['style_discount_percent'.$i] = ' style="visibility:hidden"';
			$discountTotal = $discount_amount;
		}
		else
		{
			$product_Detail[$i]['checked_discount_zero'.$i] = ' checked';
		}
		$totalAfterDiscount = $productTotal-$discountTotal;
		$product_Detail[$i]['discountTotal'.$i] = $discountTotal;
		$product_Detail[$i]['totalAfterDiscount'.$i] = $totalAfterDiscount;

		$taxTotal = '0.00';
		$product_Detail[$i]['taxTotal'.$i] = $taxTotal;

		//Calculate netprice
		$netPrice = $totalAfterDiscount+$taxTotal;
		//if condition is added to call this function when we create PO/SO/Quotes/Invoice from Product module
		if($module == 'PurchaseOrder' || $module == 'SalesOrder' || $module == 'Quotes' || $module == 'Invoice')
		{
			$taxtype = getInventoryTaxType($module,$focus->id);
			if($taxtype == 'individual')
			{
				//Add the tax with product total and assign to netprice
				$netPrice = $netPrice+$taxTotal;
			}
		}
		$product_Detail[$i]['netPrice'.$i] = $netPrice;

		//First we will get all associated taxes as array
		$tax_details = getTaxDetailsForProduct($hdnProductId,'all');
		//Now retrieve the tax values from the current query with the name
		for($tax_count=0;$tax_count<count($tax_details);$tax_count++)
		{
			$tax_name = $tax_details[$tax_count]['taxname'];
			$tax_label = $tax_details[$tax_count]['taxlabel'];
			$tax_value = '0.00';

			//condition to avoid this function call when create new PO/SO/Quotes/Invoice from Product module
			if($focus->id != '')
			{
				if($taxtype == 'individual')//if individual then show the entered tax percentage
					$tax_value = getInventoryProductTaxValue($focus->id, $hdnProductId, $tax_name);
				else//if group tax then we have to show the default value when change to individual tax
					$tax_value = $tax_details[$tax_count]['percentage'];
			}
			else//if the above function not called then assign the default associated value of the product
				$tax_value = $tax_details[$tax_count]['percentage'];

			$product_Detail[$i]['taxes'][$tax_count]['taxname'] = $tax_name;
			$product_Detail[$i]['taxes'][$tax_count]['taxlabel'] = $tax_label;
			$product_Detail[$i]['taxes'][$tax_count]['percentage'] = $tax_value;
		}

	}

	//set the taxtype
	$product_Detail[1]['final_details']['taxtype'] = $taxtype;

	//Get the Final Discount, S&H charge, Tax for S&H and Adjustment values
	//To set the Final Discount details
	$finalDiscount = '0.00';
	$product_Detail[1]['final_details']['discount_type_final'] = 'zero';

	$subTotal = ($focus->column_fields['hdnSubTotal'] != '')?$focus->column_fields['hdnSubTotal']:'0.00';
	
	$product_Detail[1]['final_details']['hdnSubTotal'] = $subTotal;
	$discountPercent = ($focus->column_fields['hdnDiscountPercent'] != '')?$focus->column_fields['hdnDiscountPercent']:'0.00';
	$discountAmount = ($focus->column_fields['hdnDiscountAmount'] != '')?$focus->column_fields['hdnDiscountAmount']:'0.00';

	//To avoid NaN javascript error, here we assign 0 initially to' %of price' and 'Direct Price reduction'(For Final Discount)
	$product_Detail[1]['final_details']['discount_percentage_final'] = 0;
	$product_Detail[1]['final_details']['discount_amount_final'] = 0;

	if($focus->column_fields['hdnDiscountPercent'] != '0')
	{
		$finalDiscount = ($subTotal*$discountPercent/100);
		$product_Detail[1]['final_details']['discount_type_final'] = 'percentage';
		$product_Detail[1]['final_details']['discount_percentage_final'] = $discountPercent;
		$product_Detail[1]['final_details']['checked_discount_percentage_final'] = ' checked';
		$product_Detail[1]['final_details']['style_discount_percentage_final'] = ' style="visibility:visible"';
		$product_Detail[1]['final_details']['style_discount_amount_final'] = ' style="visibility:hidden"';
	}
	elseif($focus->column_fields['hdnDiscountAmount'] != '0')
	{
		$finalDiscount = $focus->column_fields['hdnDiscountAmount'];
		$product_Detail[1]['final_details']['discount_type_final'] = 'amount';
		$product_Detail[1]['final_details']['discount_amount_final'] = $discountAmount;
		$product_Detail[1]['final_details']['checked_discount_amount_final'] = ' checked';
		$product_Detail[1]['final_details']['style_discount_amount_final'] = ' style="visibility:visible"';
		$product_Detail[1]['final_details']['style_discount_percentage_final'] = ' style="visibility:hidden"';
	}
	$product_Detail[1]['final_details']['discountTotal_final'] = $finalDiscount;

	//To set the Final Tax values
	//we will get all taxes. if individual then show the product related taxes only else show all taxes
	//suppose user want to change individual to group or vice versa in edit time the we have to show all taxes. so that here we will store all the taxes and based on need we will show the corresponding taxes
	
	$taxtotal = '0.00';
	//First we should get all available taxes and then retrieve the corresponding tax values
	$tax_details = getAllTaxes('available','','edit',$focus->id);

	for($tax_count=0;$tax_count<count($tax_details);$tax_count++)
	{
		$tax_name = $tax_details[$tax_count]['taxname'];
		$tax_label = $tax_details[$tax_count]['taxlabel'];

		//if taxtype is individual and want to change to group during edit time then we have to show the all available taxes and their default values 
		//Also taxtype is group and want to change to individual during edit time then we have to provide the asspciated taxes and their default tax values for individual products
		if($taxtype == 'group')
			$tax_percent = $adb->query_result($result,0,$tax_name);
		else
			$tax_percent = $tax_details[$tax_count]['percentage'];//$adb->query_result($result,0,$tax_name);
		
		if($tax_percent == '' || $tax_percent == 'NULL')
			$tax_percent = '0.00';
		$taxamount = ($subTotal-$finalDiscount)*$tax_percent/100;
		$taxtotal = $taxtotal + $taxamount;
		$product_Detail[1]['final_details']['taxes'][$tax_count]['taxname'] = $tax_name;
		$product_Detail[1]['final_details']['taxes'][$tax_count]['taxlabel'] = $tax_label;
		$product_Detail[1]['final_details']['taxes'][$tax_count]['percentage'] = $tax_percent;
		$product_Detail[1]['final_details']['taxes'][$tax_count]['amount'] = $taxamount;
	}
	$product_Detail[1]['final_details']['tax_totalamount'] = $taxtotal;
	
	//To set the Shipping & Handling charge
	$shCharge = ($focus->column_fields['hdnS_H_Amount'] != '')?$focus->column_fields['hdnS_H_Amount']:'0.00';
	$product_Detail[1]['final_details']['shipping_handling_charge'] = $shCharge;

	//To set the Shipping & Handling tax values
	//calculate S&H tax
	$shtaxtotal = '0.00';
	//First we should get all available taxes and then retrieve the corresponding tax values
	$shtax_details = getAllTaxes('available','sh','edit',$focus->id);
	
	//if taxtype is group then the tax should be same for all products in vtiger_inventoryproductrel table
	for($shtax_count=0;$shtax_count<count($shtax_details);$shtax_count++)
	{
		$shtax_name = $shtax_details[$shtax_count]['taxname'];
		$shtax_label = $shtax_details[$shtax_count]['taxlabel'];
		$shtax_percent = '0.00';
		//if condition is added to call this function when we create PO/SO/Quotes/Invoice from Product module
		if($module == 'PurchaseOrder' || $module == 'SalesOrder' || $module == 'Quotes' || $module == 'Invoice')
		{
			$shtax_percent = getInventorySHTaxPercent($focus->id,$shtax_name);
		}
		$shtaxamount = $shCharge*$shtax_percent/100;
		$shtaxtotal = $shtaxtotal + $shtaxamount;
		$product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['taxname'] = $shtax_name;
		$product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['taxlabel'] = $shtax_label;
		$product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['percentage'] = $shtax_percent;
		$product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['amount'] = $shtaxamount;
	}
	$product_Detail[1]['final_details']['shtax_totalamount'] = $shtaxtotal;

	//To set the Adjustment value
	$adjustment = ($focus->column_fields['txtAdjustment'] != '')?$focus->column_fields['txtAdjustment']:'0.00';
	$product_Detail[1]['final_details']['adjustment'] = $adjustment;

	//To set the grand total
	$grandTotal = ($focus->column_fields['hdnGrandTotal'] != '')?$focus->column_fields['hdnGrandTotal']:'0.00';
	$product_Detail[1]['final_details']['grandTotal'] = $grandTotal;

	$log->debug("Exiting getAssociatedProducts method ...");

	return $product_Detail;

}

/** This function returns the no of vtiger_products associated to the given entity or a record.
* Param $module - module name
* Param $focus - module object
* Param $seid - sales entity id
* Return type is an object array
*/

function getNoOfAssocProducts($module,$focus,$seid='')
{
	global $log;
	$log->debug("Entering getNoOfAssocProducts(".$module.",".get_class($focus).",".$seid."='') method ...");
	global $adb;
	$output = '';
	if($module == 'Quotes')
	{
		$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=?";
		$params = array($focus->id);
	}
	elseif($module == 'PurchaseOrder')
	{
		$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=?";
		$params = array($focus->id);
	}
	elseif($module == 'SalesOrder')
	{
		$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=?";
		$params = array($focus->id);
	}
	elseif($module == 'Invoice')
	{
		$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=?";
		$params = array($focus->id);
	}
	elseif($module == 'Potentials')
	{
		$query="select vtiger_products.productname,vtiger_products.unit_price,vtiger_seproductsrel.* from vtiger_products inner join vtiger_seproductsrel on vtiger_seproductsrel.productid=vtiger_products.productid where crmid=?";
		$params = array($seid);
	}	
	elseif($module == 'Products')
	{
		$query="select vtiger_products.productname,vtiger_products.unit_price, vtiger_crmentity.* from vtiger_products inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid where vtiger_crmentity.deleted=0 and productid=?";
		$params = array($seid);
	}

	$result = $adb->pquery($query, $params);
	$num_rows=$adb->num_rows($result);
	$log->debug("Exiting getNoOfAssocProducts method ...");
	return $num_rows;
}

/** This function returns the detail block information of a record for given block id.
* Param $module - module name
* Param $block - block name
* Param $mode - view type (detail/edit/create)
* Param $col_fields - vtiger_fields array
* Param $tabid - vtiger_tab id
* Param $info_type - information type (basic/advance) default ""
* Return type is an object array
*/

function getBlockInformation($module, $result, $col_fields,$tabid,$block_label,$mode)
{
	global $log;
	$log->debug("Entering getBlockInformation(".$module.",". $result.",". $col_fields.",".$tabid.",".$block_label.") method ...");
	global $adb;
	$editview_arr = Array();

	global $current_user,$mod_strings;
	
	$noofrows = $adb->num_rows($result);
	if (($module == 'Accounts' || $module == 'Contacts' || $module == 'Quotes' || $module == 'PurchaseOrder' || $module == 'SalesOrder'|| $module == 'Invoice') && $block == 2)
	{
		 global $log;
                $log->info("module is ".$module);

			$mvAdd_flag = true;
			$moveAddress = "<td rowspan='6' valign='middle' align='center'><input title='Copy billing address to shipping address'  class='button' onclick='return copyAddressRight(EditView)'  type='button' name='copyright' value='&raquo;' style='padding:0px 2px 0px 2px;font-size:12px'><br><br>
				<input title='Copy shipping address to billing address'  class='button' onclick='return copyAddressLeft(EditView)'  type='button' name='copyleft' value='&laquo;' style='padding:0px 2px 0px 2px;font-size:12px'></td>";
	}
	

	for($i=0; $i<$noofrows; $i++)
	{
		$fieldtablename = $adb->query_result($result,$i,"tablename");	
		$fieldcolname = $adb->query_result($result,$i,"columnname");	
		$uitype = $adb->query_result($result,$i,"uitype");	
		$fieldname = $adb->query_result($result,$i,"fieldname");	
		$fieldlabel = $adb->query_result($result,$i,"fieldlabel");
		$block = $adb->query_result($result,$i,"block");
		$maxlength = $adb->query_result($result,$i,"maximumlength");
		$generatedtype = $adb->query_result($result,$i,"generatedtype");				
		$typeofdata = $adb->query_result($result,$i,"typeofdata");
		
		$custfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields,$generatedtype,$module,$mode,$typeofdata);
		$editview_arr[$block][]=$custfld;
		if ($mvAdd_flag == true)
		$mvAdd_flag = false;
		$i++;
		if($i<$noofrows)
		{
			$fieldtablename = $adb->query_result($result,$i,"tablename");	
			$fieldcolname = $adb->query_result($result,$i,"columnname");	
			$uitype = $adb->query_result($result,$i,"uitype");	
			$fieldname = $adb->query_result($result,$i,"fieldname");	
			$fieldlabel = $adb->query_result($result,$i,"fieldlabel");
			$block = $adb->query_result($result,$i,"block");
			$maxlength = $adb->query_result($result,$i,"maximumlength");
			$generatedtype = $adb->query_result($result,$i,"generatedtype");
			$typeofdata = $adb->query_result($result,$i,"typeofdata");
			$custfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields,$generatedtype,$module,$mode,$typeofdata);			
			$editview_arr[$block][]=$custfld;
		}
	}
	foreach($editview_arr as $headerid=>$editview_value)
	{
		$editview_data = Array();
		for ($i=0,$j=0;$i<count($editview_value);$j++)
		{
			$key1=$editview_value[$i];
			if(is_array($editview_value[$i+1]) && ($key1[0][0]!=19 && $key1[0][0]!=20))
			{
				$key2=$editview_value[$i+1];
			}
			else
			{
				$key2 =array();
			}
			if($key1[0][0]!=19 && $key1[0][0]!=20){
				$editview_data[$j]=array(0 => $key1,1 => $key2);
				$i+=2;
			}
			else{
				$editview_data[$j]=array(0 => $key1);
				$i++;
			}
		}
		$editview_arr[$headerid] = $editview_data;
	}
	foreach($block_label as $blockid=>$label)
	{
		if($label == '')
		{
			$returndata[getTranslatedString($curBlock,$module)]=array_merge((array)$returndata[getTranslatedString($curBlock,$module)],(array)$editview_arr[$blockid]);
		}
		else
		{
			$curBlock = $label;
			if(is_array($editview_arr[$blockid]))
				$returndata[getTranslatedString($curBlock,$module)]=array_merge((array)$returndata[getTranslatedString($curBlock,$module)],(array)$editview_arr[$blockid]);
		}
	}
	$log->debug("Exiting getBlockInformation method ...");
	return $returndata;	
	
}

/** This function returns the data type of the vtiger_fields, with vtiger_field label, which is used for javascript validation.
* Param $validationData - array of vtiger_fieldnames with datatype
* Return type array 
*/

function split_validationdataArray($validationData)
{
	global $log;
	$log->debug("Entering split_validationdataArray(".$validationData.") method ...");
	$fieldName = '';
	$fieldLabel = '';
	$fldDataType = '';
	$rows = count($validationData);
	foreach($validationData as $fldName => $fldLabel_array)
	{
		if($fieldName == '')
		{
			$fieldName="'".$fldName."'";
		}
		else
		{
			$fieldName .= ",'".$fldName ."'";
		}
		foreach($fldLabel_array as $fldLabel => $datatype)
		{
			if($fieldLabel == '')
			{
				$fieldLabel = "'".addslashes($fldLabel)."'";
			}
			else
			{
				$fieldLabel .= ",'".addslashes($fldLabel)."'";
			}
			if($fldDataType == '')
			{
				$fldDataType = "'".$datatype ."'";
			}
			else
			{
				$fldDataType .= ",'".$datatype ."'";
			}
		}
	}
	$data['fieldname'] = $fieldName;
	$data['fieldlabel'] = $fieldLabel;
	$data['datatype'] = $fldDataType;
	$log->debug("Exiting split_validationdataArray method ...");
	return $data;
}


?>
