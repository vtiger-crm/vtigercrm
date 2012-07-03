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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Contacts/Contacts.php,v 1.70 2005/04/27 11:21:49 rank Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
include_once('config.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Campaigns/Campaigns.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Emails/Emails.php');
require_once('modules/HelpDesk/HelpDesk.php');
require_once('user_privileges/default_module_view.php');


// Contact is used to store customer information.
class Contacts extends CRMEntity {
	var $log;
	var $db;

	var $table_name = "vtiger_contactdetails";
	var $table_index= 'contactid';
	var $tab_name = Array('vtiger_crmentity','vtiger_contactdetails','vtiger_contactaddress','vtiger_contactsubdetails','vtiger_contactscf','vtiger_customerdetails');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_contactdetails'=>'contactid','vtiger_contactaddress'=>'contactaddressid','vtiger_contactsubdetails'=>'contactsubscriptionid','vtiger_contactscf'=>'contactid','vtiger_customerdetails'=>'customerid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_contactscf', 'contactid');

	var $column_fields = Array();

	var $sortby_fields = Array('lastname','firstname','title','email','phone','smownerid','accountname');

	var $list_link_field= 'lastname';

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
	'Last Name' => Array('contactdetails'=>'lastname'),
	'First Name' => Array('contactdetails'=>'firstname'),
	'Title' => Array('contactdetails'=>'title'),
	'Account Name' => Array('account'=>'accountid'),
	'Email' => Array('contactdetails'=>'email'),
	'Office Phone' => Array('contactdetails'=>'phone'),
	'Assigned To' => Array('crmentity'=>'smownerid')
	);

	var $range_fields = Array(
		'first_name',
		'last_name',
		'primary_address_city',
		'account_name',
		'account_id',
		'id',
		'email1',
		'salutation',
		'title',
		'phone_mobile',
		'reports_to_name',
		'primary_address_street',
		'primary_address_city',
		'primary_address_state',
		'primary_address_postalcode',
		'primary_address_country',
		'alt_address_city',
		'alt_address_street',
		'alt_address_city',
		'alt_address_state',
		'alt_address_postalcode',
		'alt_address_country',
		'office_phone',
		'home_phone',
		'other_phone',
		'fax',
		'department',
		'birthdate',
		'assistant_name',
		'assistant_phone');


	var $list_fields_name = Array(
	'Last Name' => 'lastname',
	'First Name' => 'firstname',
	'Title' => 'title',
	'Account Name' => 'account_id',
	'Email' => 'email',
	'Office Phone' => 'phone',
	'Assigned To' => 'assigned_user_id'
	);

	var $search_fields = Array(
	'Name' => Array('contactdetails'=>'lastname'),
	'Title' => Array('contactdetails'=>'title'),
	'Account Name'=>Array('contactdetails'=>'account_id'),
	'Assigned To'=>Array('crmentity'=>'smownerid'),
		);

	var $search_fields_name = Array(
	'Name' => 'lastname',
	'Title' => 'title',
	'Account Name'=>'account_id',
	'Assigned To'=>'assigned_user_id'
	);

	// This is the list of vtiger_fields that are required
	var $required_fields =  array("lastname"=>1);

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('assigned_user_id','lastname','createdtime' ,'modifiedtime','imagename');

	//Default Fields for Email Templates -- Pavani
	var $emailTemplate_defaultFields = array('firstname','lastname','salutation','title','email','department','phone','mobile','support_start_date','support_end_date');

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'lastname';
	var $default_sort_order = 'ASC';

	// For Alphabetical search
	var $def_basicsearch_col = 'lastname';

	function Contacts() {
		$this->log = LoggerManager::getLogger('contact');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Contacts');
	}

	// Mike Crowe Mod --------------------------------------------------------Default ordering for us
	/** Function to get the number of Contacts assigned to a particular User.
	*  @param varchar $user name - Assigned to User
	*  Returns the count of contacts assigned to user.
	*/
	function getCount($user_name)
	{
		global $log;
		$log->debug("Entering getCount(".$user_name.") method ...");
		$query = "select count(*) from vtiger_contactdetails  inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid where user_name=? and vtiger_crmentity.deleted=0";
		$result = $this->db->pquery($query,array($user_name),true,"Error retrieving contacts count");
		$rows_found =  $this->db->getRowCount($result);
		$row = $this->db->fetchByAssoc($result, 0);


		$log->debug("Exiting getCount method ...");
		return $row["count(*)"];
	}

	// This function doesn't seem to be used anywhere. Need to check and remove it.
	/** Function to get the Contact Details assigned to a given User ID which has a valid Email Address.
	* @param varchar $user_name - User Name (eg. Admin)
	* @param varchar $email_address - Email Addr of each contact record.
	* Returns the query.
	*/
  	function get_contacts1($user_name,$email_address)
	{
		global $log;
		$log->debug("Entering get_contacts1(".$user_name.",".$email_address.") method ...");
		$query = "select vtiger_users.user_name, vtiger_contactdetails.lastname last_name,vtiger_contactdetails.firstname first_name,vtiger_contactdetails.contactid as id, vtiger_contactdetails.salutation as salutation, vtiger_contactdetails.email as email1,vtiger_contactdetails.title as title,vtiger_contactdetails.mobile as phone_mobile,vtiger_account.accountname as account_name,vtiger_account.accountid as account_id   from vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid  left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid  where user_name='" .$user_name ."' and vtiger_crmentity.deleted=0  and vtiger_contactdetails.email like '". formatForSqlLike($email_address) ."' limit 50";

		$log->debug("Exiting get_contacts1 method ...");
		return $this->process_list_query1($query);
	}

	// This function doesn't seem to be used anywhere. Need to check and remove it.
	/** Function to get the Contact Details assigned to a particular User based on the starting count and the number of subsequent records.
	*  @param varchar $user_name - Assigned User
	*  @param integer $from_index - Initial record number to be displayed
	*  @param integer $offset - Count of the subsequent records to be displayed.
	*  Returns Query.
	*/
    function get_contacts($user_name,$from_index,$offset)
    {
	global $log;
	$log->debug("Entering get_contacts(".$user_name.",".$from_index.",".$offset.") method ...");
      $query = "select vtiger_users.user_name,vtiger_groups.groupname,vtiger_contactdetails.department department, vtiger_contactdetails.phone office_phone, vtiger_contactdetails.fax fax, vtiger_contactsubdetails.assistant assistant_name, vtiger_contactsubdetails.otherphone other_phone, vtiger_contactsubdetails.homephone home_phone,vtiger_contactsubdetails.birthday birthdate, vtiger_contactdetails.lastname last_name,vtiger_contactdetails.firstname first_name,vtiger_contactdetails.contactid as id, vtiger_contactdetails.salutation as salutation, vtiger_contactdetails.email as email1,vtiger_contactdetails.title as title,vtiger_contactdetails.mobile as phone_mobile,vtiger_account.accountname as account_name,vtiger_account.accountid as account_id, vtiger_contactaddress.mailingcity as primary_address_city,vtiger_contactaddress.mailingstreet as primary_address_street, vtiger_contactaddress.mailingcountry as primary_address_country,vtiger_contactaddress.mailingstate as primary_address_state, vtiger_contactaddress.mailingzip as primary_address_postalcode,   vtiger_contactaddress.othercity as alt_address_city,vtiger_contactaddress.otherstreet as alt_address_street, vtiger_contactaddress.othercountry as alt_address_country,vtiger_contactaddress.otherstate as alt_address_state, vtiger_contactaddress.otherzip as alt_address_postalcode  from vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid left join vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id where user_name='" .$user_name ."' and vtiger_crmentity.deleted=0 limit " .$from_index ."," .$offset;

	$log->debug("Exiting get_contacts method ...");
      return $this->process_list_query1($query);
    }


    /** Function to process list query for a given query
    *  @param $query
    *  Returns the results of query in array format
    */
    function process_list_query1($query)
    {
	global $log;
	$log->debug("Entering process_list_query1(".$query.") method ...");

        $result =& $this->db->query($query,true,"Error retrieving $this->object_name list: ");
        $list = Array();
        $rows_found =  $this->db->getRowCount($result);
        if($rows_found != 0)
        {
		   $contact = Array();
               for($index = 0 , $row = $this->db->fetchByAssoc($result, $index); $row && $index <$rows_found;$index++, $row = $this->db->fetchByAssoc($result, $index))

             {
                foreach($this->range_fields as $columnName)
                {
                    if (isset($row[$columnName])) {

                        $contact[$columnName] = $row[$columnName];
                    }
                    else
                    {
                            $contact[$columnName] = "";
                    }
	     }
// TODO OPTIMIZE THE QUERY ACCOUNT NAME AND ID are set separetly for every vtiger_contactdetails and hence
// vtiger_account query goes for ecery single vtiger_account row

                    $list[] = $contact;
                }
        }

        $response = Array();
        $response['list'] = $list;
        $response['row_count'] = $rows_found;
        $response['next_offset'] = $next_offset;
        $response['previous_offset'] = $previous_offset;


	$log->debug("Exiting process_list_query1 method ...");
        return $response;
    }


    /** Function to process list query for Plugin with Security Parameters for a given query
    *  @param $query
    *  Returns the results of query in array format
    */
    function plugin_process_list_query($query)
    {
          global $log,$adb,$current_user;
          $log->debug("Entering process_list_query1(".$query.") method ...");
          $permitted_field_lists = Array();
          require('user_privileges/user_privileges_'.$current_user->id.'.php');
          if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
          {
              $sql1 = "select columnname from vtiger_field where tabid=4 and block <> 75 and vtiger_field.presence in (0,2)";
			  $params1 = array();
          }else
          {
              $profileList = getCurrentUserProfileList();
              $sql1 = "select columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=4 and vtiger_field.block <> 6 and vtiger_field.block <> 75 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";
			  $params1 = array();
			  if (count($profileList) > 0) {
			  	 $sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			  	 array_push($params1, $profileList);
			  }
          }
          $result1 = $this->db->pquery($sql1, $params1);
          for($i=0;$i < $adb->num_rows($result1);$i++)
          {
              $permitted_field_lists[] = $adb->query_result($result1,$i,'columnname');
          }

          $result =& $this->db->query($query,true,"Error retrieving $this->object_name list: ");
          $list = Array();
          $rows_found =  $this->db->getRowCount($result);
          if($rows_found != 0)
          {
              for($index = 0 , $row = $this->db->fetchByAssoc($result, $index); $row && $index <$rows_found;$index++, $row = $this->db->fetchByAssoc($result, $index))
              {
                  $contact = Array();

		  $contact[lastname] = in_array("lastname",$permitted_field_lists) ? $row[lastname] : "";
		  $contact[firstname] = in_array("firstname",$permitted_field_lists)? $row[firstname] : "";
		  $contact[email] = in_array("email",$permitted_field_lists) ? $row[email] : "";


                  if(in_array("accountid",$permitted_field_lists))
                  {
                      $contact[accountname] = $row[accountname];
                      $contact[account_id] = $row[accountid];
                  }else
		  {
                      $contact[accountname] = "";
                      $contact[account_id] = "";
		  }
                  $contact[contactid] =  $row[contactid];
                  $list[] = $contact;
              }
          }

          $response = Array();
          $response['list'] = $list;
          $response['row_count'] = $rows_found;
          $response['next_offset'] = $next_offset;
          $response['previous_offset'] = $previous_offset;
          $log->debug("Exiting process_list_query1 method ...");
          return $response;
    }


	/** Returns a list of the associated opportunities
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	*/
	function get_opportunities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_opportunities(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_action.value=\"updateRelations\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query ='select case when (vtiger_users.user_name not like "") then '.$userNameSql.' else vtiger_groups.groupname end as user_name,
		vtiger_contactdetails.accountid, vtiger_contactdetails.contactid , vtiger_potential.potentialid, vtiger_potential.potentialname,
		vtiger_potential.potentialtype, vtiger_potential.sales_stage, vtiger_potential.amount, vtiger_potential.closingdate,
		vtiger_potential.related_to, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_account.accountname
		from vtiger_contactdetails
		left join vtiger_contpotentialrel on vtiger_contpotentialrel.contactid=vtiger_contactdetails.contactid
		left join vtiger_potential on (vtiger_potential.potentialid = vtiger_contpotentialrel.potentialid or vtiger_potential.related_to=vtiger_contactdetails.contactid)
		inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_potential.potentialid
		left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid
		left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
		left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
		where vtiger_contactdetails.contactid ='.$id.'
		and (vtiger_contactdetails.accountid = vtiger_potential.related_to or vtiger_contactdetails.contactid=vtiger_potential.related_to)
		and vtiger_crmentity.deleted=0';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_opportunities method ...");
		return $return_value;
	}


	/** Returns a list of the associated tasks
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	*/
	function get_activities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_activities(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/Activity.php");
		$other = new Activity();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		$button .= '<input type="hidden" name="activity_mode">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				if(getFieldVisibilityPermission('Calendar',$current_user->id,'contact_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
				}
				if(getFieldVisibilityPermission('Events',$current_user->id,'contact_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Events\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_EVENT', $related_module) ."'>";
				}
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name," .
				" vtiger_contactdetails.lastname, vtiger_contactdetails.firstname,  vtiger_activity.activityid ," .
				" vtiger_activity.subject, vtiger_activity.activitytype, vtiger_activity.date_start, vtiger_activity.due_date," .
				" vtiger_activity.time_start,vtiger_activity.time_end, vtiger_cntactivityrel.contactid, vtiger_crmentity.crmid," .
				" vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime, vtiger_recurringevents.recurringtype," .
				" case when (vtiger_activity.activitytype = 'Task') then vtiger_activity.status else vtiger_activity.eventstatus end as status, " .
				" vtiger_seactivityrel.crmid as parent_id " .
				" from vtiger_contactdetails " .
				" inner join vtiger_cntactivityrel on vtiger_cntactivityrel.contactid = vtiger_contactdetails.contactid" .
				" inner join vtiger_activity on vtiger_cntactivityrel.activityid=vtiger_activity.activityid" .
				" inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_cntactivityrel.activityid " .
				" left join vtiger_seactivityrel on vtiger_seactivityrel.activityid = vtiger_cntactivityrel.activityid " .
				" left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid" .
				" left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid" .
				" left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid" .
				" where vtiger_contactdetails.contactid=".$id." and vtiger_crmentity.deleted = 0" .
						" and ((vtiger_activity.activitytype='Task' and vtiger_activity.status not in ('Completed','Deferred'))" .
						" or (vtiger_activity.activitytype Not in ('Emails','Task') and  vtiger_activity.eventstatus not in ('','Held')))";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}
	/**
	* Function to get Contact related Task & Event which have activity type Held, Completed or Deferred.
	* @param  integer   $id      - contactid
	* returns related Task or Event record in array format
	*/
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.status
			, vtiger_activity.eventstatus,vtiger_activity.activitytype, vtiger_activity.date_start,
			vtiger_activity.due_date,vtiger_activity.time_start,vtiger_activity.time_end,
			vtiger_contactdetails.contactid, vtiger_contactdetails.firstname,
			vtiger_contactdetails.lastname, vtiger_crmentity.modifiedtime,
			vtiger_crmentity.createdtime, vtiger_crmentity.description,
			case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
				from vtiger_activity
				inner join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				inner join vtiger_contactdetails on vtiger_contactdetails.contactid= vtiger_cntactivityrel.contactid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
                left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
				where (vtiger_activity.activitytype != 'Emails')
				and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred' or (vtiger_activity.eventstatus = 'Held' and vtiger_activity.eventstatus != ''))
				and vtiger_cntactivityrel.contactid=".$id."
                                and vtiger_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php
		$log->debug("Entering get_history method ...");
		return getHistory('Contacts',$query,$id);
	}
	/**
	* Function to get Contact related Tickets.
	* @param  integer   $id      - contactid
	* returns related Ticket records in array format
	*/
	function get_tickets($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_tickets(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'parent_id', 'readwrite') == '0') {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "select case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_crmentity.crmid, vtiger_troubletickets.title, vtiger_contactdetails.contactid, vtiger_troubletickets.parent_id,
				vtiger_contactdetails.firstname, vtiger_contactdetails.lastname, vtiger_troubletickets.status, vtiger_troubletickets.priority,
				vtiger_crmentity.smownerid, vtiger_troubletickets.ticket_no
				from vtiger_troubletickets inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.ticketid
				left join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_troubletickets.parent_id
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				where vtiger_crmentity.deleted=0 and vtiger_contactdetails.contactid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_tickets method ...");
		return $return_value;
	}

	  /**
	  * Function to get Contact related Quotes
	  * @param  integer   $id  - contactid
	  * returns related Quotes record in array format
	  */
	 function get_quotes($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_quotes(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "select case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,vtiger_crmentity.*, vtiger_quotes.*,vtiger_potential.potentialname,vtiger_contactdetails.lastname,vtiger_account.accountname from vtiger_quotes inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_quotes.quoteid left outer join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_quotes.contactid left outer join vtiger_potential on vtiger_potential.potentialid=vtiger_quotes.potentialid  left join vtiger_account on vtiger_account.accountid = vtiger_quotes.accountid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where vtiger_crmentity.deleted=0 and vtiger_contactdetails.contactid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_quotes method ...");
		return $return_value;
	  }
	/**
	 * Function to get Contact related SalesOrder
 	 * @param  integer   $id  - contactid
	 * returns related SalesOrder record in array format
	 */
	 function get_salesorder($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_salesorder(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "select case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,vtiger_crmentity.*, vtiger_salesorder.*, vtiger_quotes.subject as quotename, vtiger_account.accountname, vtiger_contactdetails.lastname from vtiger_salesorder inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_salesorder.salesorderid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left outer join vtiger_quotes on vtiger_quotes.quoteid=vtiger_salesorder.quoteid left outer join vtiger_account on vtiger_account.accountid=vtiger_salesorder.accountid left outer join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_salesorder.contactid left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where vtiger_crmentity.deleted=0 and vtiger_salesorder.contactid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_salesorder method ...");
		return $return_value;
	 }
	 /**
	 * Function to get Contact related Products
	 * @param  integer   $id  - contactid
	 * returns related Products record in array format
	 */
	 function get_products($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_products(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$query = 'SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.productcode,
		 		  vtiger_products.commissionrate, vtiger_products.qty_per_unit, vtiger_products.unit_price,
				  vtiger_crmentity.crmid, vtiger_crmentity.smownerid,vtiger_contactdetails.lastname
				FROM vtiger_products
				INNER JOIN vtiger_seproductsrel
					ON vtiger_seproductsrel.productid=vtiger_products.productid and vtiger_seproductsrel.setype="Contacts"
				INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid = vtiger_products.productid
				INNER JOIN vtiger_contactdetails
					ON vtiger_contactdetails.contactid = vtiger_seproductsrel.crmid
				LEFT JOIN vtiger_users
					ON vtiger_users.id=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			   WHERE vtiger_contactdetails.contactid = '.$id.' and vtiger_crmentity.deleted = 0';

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_products method ...");
		return $return_value;
	 }

	/**
	 * Function to get Contact related PurchaseOrder
 	 * @param  integer   $id  - contactid
	 * returns related PurchaseOrder record in array format
	 */
	 function get_purchase_orders($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_purchase_orders(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "select case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,vtiger_crmentity.*, vtiger_purchaseorder.*,vtiger_vendor.vendorname,vtiger_contactdetails.lastname from vtiger_purchaseorder inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_purchaseorder.purchaseorderid left outer join vtiger_vendor on vtiger_purchaseorder.vendorid=vtiger_vendor.vendorid left outer join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_purchaseorder.contactid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where vtiger_crmentity.deleted=0 and vtiger_purchaseorder.contactid=".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_purchase_orders method ...");
		return $return_value;
	 }

	/** Returns a list of the associated emails
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	*/
	function get_emails($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_emails(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		$button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."' accessyKey='F' class='crmbutton small create' onclick='fnvshobj(this,\"sendmail_cont\");sendmail(\"$this_module\",$id);' type='button' name='button' value='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."'></td>";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "select case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name," .
				" vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.activitytype, vtiger_crmentity.modifiedtime," .
				" vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_activity.date_start, vtiger_seactivityrel.crmid as parent_id " .
				" from vtiger_activity, vtiger_seactivityrel, vtiger_contactdetails, vtiger_users, vtiger_crmentity" .
				" left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid" .
				" where vtiger_seactivityrel.activityid = vtiger_activity.activityid" .
				" and vtiger_contactdetails.contactid = vtiger_seactivityrel.crmid and vtiger_users.id=vtiger_crmentity.smownerid" .
				" and vtiger_crmentity.crmid = vtiger_activity.activityid  and vtiger_contactdetails.contactid = ".$id." and" .
						" vtiger_activity.activitytype='Emails' and vtiger_crmentity.deleted = 0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_emails method ...");
		return $return_value;
	}

	/** Returns a list of the associated Campaigns
	  * @param $id -- campaign id :: Type Integer
	  * @returns list of campaigns in array format
	  */

	function get_campaigns($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_campaigns(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		$button .= '<input type="hidden" name="email_directing_module"><input type="hidden" name="record">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."' accessyKey='F' class='crmbutton small create' onclick='fnvshobj(this,\"sendmail_cont\");sendmail(\"$this_module\",$id);' type='button' name='button' value='". getTranslatedString('LBL_ADD_NEW')." ". getTranslatedString($singular_modname)."'></td>";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
					vtiger_campaign.campaignid, vtiger_campaign.campaignname, vtiger_campaign.campaigntype, vtiger_campaign.campaignstatus,
					vtiger_campaign.expectedrevenue, vtiger_campaign.closingdate, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
					vtiger_crmentity.modifiedtime from vtiger_campaign
					inner join vtiger_campaigncontrel on vtiger_campaigncontrel.campaignid=vtiger_campaign.campaignid
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_campaign.campaignid
					left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
					left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
					where vtiger_campaigncontrel.contactid=".$id." and vtiger_crmentity.deleted=0";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_campaigns method ...");
		return $return_value;
	}

	/**
	* Function to get Contact related Invoices
	* @param  integer   $id      - contactid
	* returns related Invoices record in array format
	*/
	function get_invoices($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_invoices(".$id.") method ...");
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/$related_module.php");
		$other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'contact_id', 'readwrite') == '0') {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
			}
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				$button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
			vtiger_crmentity.*,
			vtiger_invoice.*,
			vtiger_contactdetails.lastname,vtiger_contactdetails.firstname,
			vtiger_salesorder.subject AS salessubject
			FROM vtiger_invoice
			INNER JOIN vtiger_crmentity
				ON vtiger_crmentity.crmid = vtiger_invoice.invoiceid
			LEFT OUTER JOIN vtiger_contactdetails
				ON vtiger_contactdetails.contactid = vtiger_invoice.contactid
			LEFT OUTER JOIN vtiger_salesorder
				ON vtiger_salesorder.salesorderid = vtiger_invoice.salesorderid
			LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users
				ON vtiger_crmentity.smownerid = vtiger_users.id
			WHERE vtiger_crmentity.deleted = 0
			AND vtiger_contactdetails.contactid = ".$id;

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_invoices method ...");
		return $return_value;
	}

	/** Function to export the contact records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Contacts Query.
	*/
        function create_export_query($where)
        {
		global $log;
		global $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Contacts", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT vtiger_contactdetails.salutation as 'Salutation',$fields_list,case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
                                FROM vtiger_contactdetails
                                inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
                                LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id and vtiger_users.status='Active'
                                LEFT JOIN vtiger_account on vtiger_contactdetails.accountid=vtiger_account.accountid
				left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid
				left join vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid=vtiger_contactdetails.contactid
			        left join vtiger_contactscf on vtiger_contactscf.contactid=vtiger_contactdetails.contactid
			        left join vtiger_customerdetails on vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
	                        LEFT JOIN vtiger_groups
                        	        ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_contactdetails vtiger_contactdetails2
					ON vtiger_contactdetails2.contactid = vtiger_contactdetails.reportsto";
		$query .= getNonAdminAccessControlQuery('Contacts',$current_user);
		$where_auto = " vtiger_crmentity.deleted = 0 ";

                if($where != "")
                   $query .= "  WHERE ($where) AND ".$where_auto;
                else
                   $query .= "  WHERE ".$where_auto;

		$log->info("Export Query Constructed Successfully");
		$log->debug("Exiting create_export_query method ...");
		return $query;
        }


/** Function to get the Columnnames of the Contacts
* Used By vtigerCRM Word Plugin
* Returns the Merge Fields for Word Plugin
*/
function getColumnNames()
{
	global $log, $current_user;
	$log->debug("Entering getColumnNames() method ...");
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
	{
	 $sql1 = "select fieldlabel from vtiger_field where tabid=4 and block <> 75 and vtiger_field.presence in (0,2)";
	 $params1 = array();
	}else
	{
	 $profileList = getCurrentUserProfileList();
	 $sql1 = "select vtiger_field.fieldid,fieldlabel from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=4 and vtiger_field.block <> 75 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";
	 $params1 = array();
	 if (count($profileList) > 0) {
	 	$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .") group by fieldid";
  	 	array_push($params1, $profileList);
	 }
  }
	$result = $this->db->pquery($sql1, $params1);
	$numRows = $this->db->num_rows($result);
	for($i=0; $i < $numRows;$i++)
	{
	$custom_fields[$i] = $this->db->query_result($result,$i,"fieldlabel");
	$custom_fields[$i] = preg_replace("/\s+/","",$custom_fields[$i]);
	$custom_fields[$i] = strtoupper($custom_fields[$i]);
	}
	$mergeflds = $custom_fields;
	$log->debug("Exiting getColumnNames method ...");
	return $mergeflds;
}
//End
/** Function to get the Contacts assigned to a user with a valid email address.
* @param varchar $username - User Name
* @param varchar $emailaddress - Email Addr for each contact.
* Used By vtigerCRM Outlook Plugin
* Returns the Query
*/
function get_searchbyemailid($username,$emailaddress)
{
	global $log;
	global $current_user;
	require_once("modules/Users/Users.php");
	$seed_user=new Users();
	$user_id=$seed_user->retrieve_user_id($username);
	$current_user=$seed_user;
	$current_user->retrieve_entity_info($user_id, 'Users');
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	$log->debug("Entering get_searchbyemailid(".$username.",".$emailaddress.") method ...");
	$query = "select vtiger_contactdetails.lastname,vtiger_contactdetails.firstname,
					vtiger_contactdetails.contactid, vtiger_contactdetails.salutation,
					vtiger_contactdetails.email,vtiger_contactdetails.title,
					vtiger_contactdetails.mobile,vtiger_account.accountname,
					vtiger_account.accountid as accountid  from vtiger_contactdetails
						inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
						inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
						left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid
						left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid
			      LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
	$query .= getNonAdminAccessControlQuery('Contacts',$current_user);
	$query .= "where vtiger_crmentity.deleted=0";
	if(trim($emailaddress) != '') {
		$query .= " and ((vtiger_contactdetails.email like '". formatForSqlLike($emailaddress) .
		"') or vtiger_contactdetails.lastname REGEXP REPLACE('".$emailaddress.
		"',' ','|') or vtiger_contactdetails.firstname REGEXP REPLACE('".$emailaddress.
		"',' ','|'))  and vtiger_contactdetails.email != ''";
	} else {
		$query .= " and (vtiger_contactdetails.email like '". formatForSqlLike($emailaddress) .
		"' and vtiger_contactdetails.email != '')";
	}

	$log->debug("Exiting get_searchbyemailid method ...");
	return $this->plugin_process_list_query($query);
}

/** Function to get the Contacts associated with the particular User Name.
*  @param varchar $user_name - User Name
*  Returns query
*/

function get_contactsforol($user_name)
{
	global $log,$adb;
	global $current_user;
	require_once("modules/Users/Users.php");
	$seed_user=new Users();
	$user_id=$seed_user->retrieve_user_id($user_name);
	$current_user=$seed_user;
	$current_user->retrieve_entity_info($user_id, 'Users');
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
  {
    $sql1 = "select tablename,columnname from vtiger_field where tabid=4 and vtiger_field.presence in (0,2)";
	$params1 = array();
  }else
  {
    $profileList = getCurrentUserProfileList();
    $sql1 = "select tablename,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=4 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";
	$params1 = array();
	if (count($profileList) > 0) {
		$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
		array_push($params1, $profileList);
	}
  }
  $result1 = $adb->pquery($sql1, $params1);
  for($i=0;$i < $adb->num_rows($result1);$i++)
  {
      $permitted_lists[] = $adb->query_result($result1,$i,'tablename');
      $permitted_lists[] = $adb->query_result($result1,$i,'columnname');
      if($adb->query_result($result1,$i,'columnname') == "accountid")
      {
        $permitted_lists[] = 'vtiger_account';
        $permitted_lists[] = 'accountname';
      }
  }
	$permitted_lists = array_chunk($permitted_lists,2);
	$column_table_lists = array();
	for($i=0;$i < count($permitted_lists);$i++)
	{
	   $column_table_lists[] = implode(".",$permitted_lists[$i]);
  }

	$log->debug("Entering get_contactsforol(".$user_name.") method ...");
	$query = "select vtiger_contactdetails.contactid as id, ".implode(',',$column_table_lists)." from vtiger_contactdetails
						inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
						inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
						left join vtiger_customerdetails on vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
						left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid
						left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid
						left join vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid
                        left join vtiger_campaigncontrel on vtiger_contactdetails.contactid = vtiger_campaigncontrel.contactid
                        left join vtiger_campaignrelstatus on vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaigncontrel.campaignrelstatusid
			      LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
						where vtiger_crmentity.deleted=0 and vtiger_users.user_name='".$user_name."'";
  $log->debug("Exiting get_contactsforol method ...");
	return $query;
}


	/** Function to handle module specific operations when saving a entity
	*/
	function save_module($module)
	{
		$this->insertIntoAttachment($this->id,$module);
	}

	/**
	 *      This function is used to add the vtiger_attachments. This will call the function uploadAndSaveFile which will upload the attachment into the server and save that attachment information in the database.
	 *      @param int $id  - entity id to which the vtiger_files to be uploaded
	 *      @param string $module  - the current module name
	*/
	function insertIntoAttachment($id,$module)
	{
		global $log, $adb;
		$log->debug("Entering into insertIntoAttachment($id,$module) method.");

		$file_saved = false;
		//This is to added to store the existing attachment id of the contact where we should delete this when we give new image
		$old_attachmentid = $adb->query_result($adb->pquery("select vtiger_crmentity.crmid from vtiger_seattachmentsrel inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seattachmentsrel.attachmentsid where  vtiger_seattachmentsrel.crmid=?", array($id)),0,'crmid');
		foreach($_FILES as $fileindex => $files)
		{
			if($files['name'] != '' && $files['size'] > 0)
			{
				$files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
				$file_saved = $this->uploadAndSaveFile($id,$module,$files);
			}
		}

		//This is to handle the delete image for contacts
		if($module == 'Contacts' && $file_saved)
		{
			if($old_attachmentid != '')
			{
				$setype = $adb->query_result($adb->pquery("select setype from vtiger_crmentity where crmid=?", array($old_attachmentid)),0,'setype');
				if($setype == 'Contacts Image')
				{
					$del_res1 = $adb->pquery("delete from vtiger_attachments where attachmentsid=?", array($old_attachmentid));
					$del_res2 = $adb->pquery("delete from vtiger_seattachmentsrel where attachmentsid=?", array($old_attachmentid));
				}
			}
		}

		$log->debug("Exiting from insertIntoAttachment($id,$module) method.");
	}

	/**
	 * Move the related records of the specified list of id's to the given record.
	 * @param String This module name
	 * @param Array List of Entity Id's from which related records need to be transfered
	 * @param Integer Id of the the Record to which the related records are to be moved
	 */
	function transferRelatedRecords($module, $transferEntityIds, $entityId) {
		global $adb,$log;
		$log->debug("Entering function transferRelatedRecords ($module, $transferEntityIds, $entityId)");

		$rel_table_arr = Array("Potentials"=>"vtiger_contpotentialrel","Activities"=>"vtiger_cntactivityrel","Emails"=>"vtiger_seactivityrel",
				"HelpDesk"=>"vtiger_troubletickets","Quotes"=>"vtiger_quotes","PurchaseOrder"=>"vtiger_purchaseorder",
				"SalesOrder"=>"vtiger_salesorder","Products"=>"vtiger_seproductsrel","Documents"=>"vtiger_senotesrel",
				"Attachments"=>"vtiger_seattachmentsrel","Campaigns"=>"vtiger_campaigncontrel");

		$tbl_field_arr = Array("vtiger_contpotentialrel"=>"potentialid","vtiger_cntactivityrel"=>"activityid","vtiger_seactivityrel"=>"activityid",
				"vtiger_troubletickets"=>"ticketid","vtiger_quotes"=>"quoteid","vtiger_purchaseorder"=>"purchaseorderid",
				"vtiger_salesorder"=>"salesorderid","vtiger_seproductsrel"=>"productid","vtiger_senotesrel"=>"notesid",
				"vtiger_seattachmentsrel"=>"attachmentsid","vtiger_campaigncontrel"=>"campaignid");

		$entity_tbl_field_arr = Array("vtiger_contpotentialrel"=>"contactid","vtiger_cntactivityrel"=>"contactid","vtiger_seactivityrel"=>"crmid",
				"vtiger_troubletickets"=>"parent_id","vtiger_quotes"=>"contactid","vtiger_purchaseorder"=>"contactid",
				"vtiger_salesorder"=>"contactid","vtiger_seproductsrel"=>"crmid","vtiger_senotesrel"=>"crmid",
				"vtiger_seattachmentsrel"=>"crmid","vtiger_campaigncontrel"=>"contactid");

		foreach($transferEntityIds as $transferId) {
			foreach($rel_table_arr as $rel_module=>$rel_table) {
				$id_field = $tbl_field_arr[$rel_table];
				$entity_id_field = $entity_tbl_field_arr[$rel_table];
				// IN clause to avoid duplicate entries
				$sel_result =  $adb->pquery("select $id_field from $rel_table where $entity_id_field=? " .
						" and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
						array($transferId,$entityId));
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0) {
					for($i=0;$i<$res_cnt;$i++) {
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->pquery("update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?",
							array($entityId,$transferId,$id_field_value));
					}
				}
			}
		}
		$log->debug("Exiting transferRelatedRecords...");
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule){
		$query = $this->getRelationQuery($module,$secmodule,"vtiger_contactdetails","contactid");
		$query .= " left join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid  and vtiger_crmentityContacts.deleted=0
			left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
			left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
			left join vtiger_customerdetails on vtiger_customerdetails.customerid = vtiger_contactdetails.contactid
			left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
			left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid
			left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid
			left join vtiger_groups as vtiger_groupsContacts on vtiger_groupsContacts.groupid = vtiger_crmentityContacts.smownerid
			left join vtiger_users as vtiger_usersContacts on vtiger_usersContacts.id = vtiger_crmentityContacts.smownerid
            left join vtiger_users as vtiger_lastModifiedByContacts on vtiger_lastModifiedByContacts.id = vtiger_crmentityContacts.modifiedby ";

		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array (
			"Calendar" => array("vtiger_cntactivityrel"=>array("contactid","activityid"),"vtiger_contactdetails"=>"contactid"),
			"HelpDesk" => array("vtiger_troubletickets"=>array("parent_id","ticketid"),"vtiger_contactdetails"=>"contactid"),
			"Quotes" => array("vtiger_quotes"=>array("contactid","quoteid"),"vtiger_contactdetails"=>"contactid"),
			"PurchaseOrder" => array("vtiger_purchaseorder"=>array("contactid","purchaseorderid"),"vtiger_contactdetails"=>"contactid"),
			"SalesOrder" => array("vtiger_salesorder"=>array("contactid","salesorderid"),"vtiger_contactdetails"=>"contactid"),
			"Products" => array("vtiger_seproductsrel"=>array("crmid","productid"),"vtiger_contactdetails"=>"contactid"),
			"Campaigns" => array("vtiger_campaigncontrel"=>array("contactid","campaignid"),"vtiger_contactdetails"=>"contactid"),
			"Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_contactdetails"=>"contactid"),
			"Accounts" => array("vtiger_contactdetails"=>array("contactid","accountid")),
			"Invoice" => array("vtiger_invoice"=>array("contactid","invoiceid"),"vtiger_contactdetails"=>"contactid"),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink all the dependent entities of the given Entity by Id
	function unlinkDependencies($module, $id) {
		global $log;

		//Deleting Contact related Potentials.
		$pot_q = 'SELECT vtiger_crmentity.crmid FROM vtiger_crmentity
			INNER JOIN vtiger_potential ON vtiger_crmentity.crmid=vtiger_potential.potentialid
			LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_potential.related_to
			WHERE vtiger_crmentity.deleted=0 AND vtiger_potential.related_to=?';
		$pot_res = $this->db->pquery($pot_q, array($id));
		$pot_ids_list = array();
		for($k=0;$k < $this->db->num_rows($pot_res);$k++)
		{
			$pot_id = $this->db->query_result($pot_res,$k,"crmid");
			$pot_ids_list[] = $pot_id;
			$sql = 'UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid = ?';
			$this->db->pquery($sql, array($pot_id));
		}
		//Backup deleted Contact related Potentials.
		$params = array($id, RB_RECORD_UPDATED, 'vtiger_crmentity', 'deleted', 'crmid', implode(",", $pot_ids_list));
		$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES(?,?,?,?,?,?)', $params);

		//Backup Contact-Trouble Tickets Relation
		$tkt_q = 'SELECT ticketid FROM vtiger_troubletickets WHERE parent_id=?';
		$tkt_res = $this->db->pquery($tkt_q, array($id));
		if ($this->db->num_rows($tkt_res) > 0) {
			$tkt_ids_list = array();
			for($k=0;$k < $this->db->num_rows($tkt_res);$k++)
			{
				$tkt_ids_list[] = $this->db->query_result($tkt_res,$k,"ticketid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_troubletickets', 'parent_id', 'ticketid', implode(",", $tkt_ids_list));
			$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with Trouble Tickets
		$this->db->pquery('UPDATE vtiger_troubletickets SET parent_id=0 WHERE parent_id=?', array($id));

		//Backup Contact-PurchaseOrder Relation
		$po_q = 'SELECT purchaseorderid FROM vtiger_purchaseorder WHERE contactid=?';
		$po_res = $this->db->pquery($po_q, array($id));
		if ($this->db->num_rows($po_res) > 0) {
			$po_ids_list = array();
			for($k=0;$k < $this->db->num_rows($po_res);$k++)
			{
				$po_ids_list[] = $this->db->query_result($po_res,$k,"purchaseorderid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_purchaseorder', 'contactid', 'purchaseorderid', implode(",", $po_ids_list));
			$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with PurchaseOrder
		$this->db->pquery('UPDATE vtiger_purchaseorder SET contactid=0 WHERE contactid=?', array($id));

		//Backup Contact-SalesOrder Relation
		$so_q = 'SELECT salesorderid FROM vtiger_salesorder WHERE contactid=?';
		$so_res = $this->db->pquery($so_q, array($id));
		if ($this->db->num_rows($so_res) > 0) {
			$so_ids_list = array();
			for($k=0;$k < $this->db->num_rows($so_res);$k++)
			{
				$so_ids_list[] = $this->db->query_result($so_res,$k,"salesorderid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_salesorder', 'contactid', 'salesorderid', implode(",", $so_ids_list));
			$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with SalesOrder
		$this->db->pquery('UPDATE vtiger_salesorder SET contactid=0 WHERE contactid=?', array($id));

		//Backup Contact-Quotes Relation
		$quo_q = 'SELECT quoteid FROM vtiger_quotes WHERE contactid=?';
		$quo_res = $this->db->pquery($quo_q, array($id));
		if ($this->db->num_rows($quo_res) > 0) {
			$quo_ids_list = array();
			for($k=0;$k < $this->db->num_rows($quo_res);$k++)
			{
				$quo_ids_list[] = $this->db->query_result($quo_res,$k,"quoteid");
			}
			$params = array($id, RB_RECORD_UPDATED, 'vtiger_quotes', 'contactid', 'quoteid', implode(",", $quo_ids_list));
			$this->db->pquery('INSERT INTO vtiger_relatedlists_rb VALUES (?,?,?,?,?,?)', $params);
		}
		//removing the relationship of contacts with Quotes
		$this->db->pquery('UPDATE vtiger_quotes SET contactid=0 WHERE contactid=?', array($id));
		//remove the portal info the contact
		$this->db->pquery('DELETE FROM vtiger_portalinfo WHERE id = ?', array($id));
		$this->db->pquery('UPDATE vtiger_customerdetails SET portal=0,support_start_date=NULL,support_end_date=NULl WHERE customerid=?', array($id));
		parent::unlinkDependencies($module, $id);
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Accounts') {
			$sql = 'UPDATE vtiger_contactdetails SET accountid = ? WHERE contactid = ?';
			$this->db->pquery($sql, array(null, $id));
		} elseif($return_module == 'Potentials') {
			$sql = 'DELETE FROM vtiger_contpotentialrel WHERE contactid=? AND potentialid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Campaigns') {
			$sql = 'DELETE FROM vtiger_campaigncontrel WHERE contactid=? AND campaignid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Products') {
			$sql = 'DELETE FROM vtiger_seproductsrel WHERE crmid=? AND productid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} elseif($return_module == 'Vendors') {
			$sql = 'DELETE FROM vtiger_vendorcontactrel WHERE vendorid=? AND contactid=?';
			$this->db->pquery($sql, array($return_id, $id));
		} else {
			$sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
			$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
			$this->db->pquery($sql, $params);
		}
	}

	//added to get mail info for portal user
	//type argument included when when addin customizable tempalte for sending portal login details
	public static function getPortalEmailContents($entityData, $password, $type='') {
        require_once 'config.inc.php';
		global $PORTAL_URL;

		$adb = PearDatabase::getInstance();
		$moduleName = $entityData->getModuleName();

		$portalURL = '<a href="'.$PORTAL_URL.'" style="font-family:Arial, Helvetica, sans-serif;font-size:12px; font-weight:bolder;text-decoration:none;color: #4242FD;">'.getTranslatedString('Please Login Here', $moduleName).'</a>';

		//here id is hardcoded with 5. it is for support start notification in vtiger_notificationscheduler
		$query='SELECT vtiger_emailtemplates.subject,vtiger_emailtemplates.body
					FROM vtiger_notificationscheduler
						INNER JOIN vtiger_emailtemplates ON vtiger_emailtemplates.templateid=vtiger_notificationscheduler.notificationbody
					WHERE schedulednotificationid=5';

		$result = $adb->pquery($query, array());
		$body=$adb->query_result($result,0,'body');
		$contents=$body;
		$contents = str_replace('$contact_name$',$entityData->get('firstname')." ".$entityData->get('lastname'),$contents);
		$contents = str_replace('$login_name$',$entityData->get('email'),$contents);
		$contents = str_replace('$password$',$password,$contents);
		$contents = str_replace('$URL$',$portalURL,$contents);
		$contents = str_replace('$support_team$',getTranslatedString('Support Team', $moduleName),$contents);
		$contents = str_replace('$logo$','<img src="cid:logo" />',$contents);

		if($type == "LoginDetails") {
			$temp=$contents;
			$value["subject"]=$adb->query_result($result,0,'subject');
			$value["body"]=$temp;
			return $value;
		}
		return $contents;
	}

	function save_related_module($module, $crmid, $with_module, $with_crmids) {
		$adb = PearDatabase::getInstance();

		if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
		foreach($with_crmids as $with_crmid) {
			if($with_module == 'Products') {
				$adb->pquery("insert into vtiger_seproductsrel values (?,?,?)", array($crmid, $with_crmid, 'Contacts'));

			} elseif($with_module == 'Campaigns') {
				$adb->pquery("insert into vtiger_campaigncontrel values(?,?,1)", array($with_crmid, $crmid));

			} elseif($with_module == 'Potentials') {
				$adb->pquery("insert into vtiger_contpotentialrel values(?,?)", array($crmid, $with_crmid));

			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

	function getListButtons($app_strings) {
		$list_buttons = Array();

		if(isPermitted('Contacts','Delete','') == 'yes') {
			$list_buttons['del'] = $app_strings[LBL_MASS_DELETE];
		}
		if(isPermitted('Contacts','EditView','') == 'yes') {
			$list_buttons['mass_edit'] = $app_strings[LBL_MASS_EDIT];
			$list_buttons['c_owner'] = $app_strings[LBL_CHANGE_OWNER];
		}
		if(isPermitted('Emails','EditView','') == 'yes'){
			$list_buttons['s_mail'] = $app_strings[LBL_SEND_MAIL_BUTTON];
		}
		return $list_buttons;
	}
}

?>