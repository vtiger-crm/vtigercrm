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
 * $Header$
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('data/SugarBean.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Emails/Emails.php');
require_once('modules/Accounts/Accounts.php');
require_once('include/ComboUtil.php');

// Account is used to store vtiger_account information.
class ImportOpportunity extends Potentials {
	 var $db;

	// This is the list of the functions to run when importing
	var $special_functions =  array(
						"assign_user",
						"map_campaign_source",
						"modseq_number",
				       );
/**	function used to set the assigned_user_id value in the column_fields when we map the username during import
 */
function assign_user()
{
	global $current_user;
	$ass_user = $this->column_fields["assigned_user_id"];
	$this->db->println("assign_user ".$ass_user." cur_user=".$current_user->id);
	
	if( $ass_user != $current_user->id)
	{
		$this->db->println("searching and assigning ".$ass_user);

		$result = $this->db->pquery("select id from vtiger_users where id = ? union select groupid as id from vtiger_groups where groupid = ?", array($ass_user, $ass_user));
		if($this->db->num_rows($result)!=1)
		{
			$this->db->println("not exact records setting current userid");
			$this->column_fields["assigned_user_id"] = $current_user->id;
		}
		else
		{
		
			$row = $this->db->fetchByAssoc($result, -1, false);
			if (isset($row['id']) && $row['id'] != -1)
                	{
				$this->db->println("setting id as ".$row['id']);
				$this->column_fields["assigned_user_id"] = $row['id'];
			}
			else
			{
				$this->db->println("setting current userid");
				$this->column_fields["assigned_user_id"] = $current_user->id;
			}
		}
	}
}
	/**     function used to map with existing Campaign Source if the potential is map with an campaign during import
         */
	function map_campaign_source(){
		global $adb;

		$campaign_name = $this->column_fields['campaignid'];
		$adb->println("Entering map_campaign_source campaignid=".$campaign_name);

		if ((! isset($campaign_name) || $campaign_name == '') ){
			$adb->println("Exit map_campaign_source. Campaign Name not set for this entity.");
			return; 
		}

		$campaign_name = trim($campaign_name);

		//Query to get the available campaign which is not deleted
		$query = "select campaignid from vtiger_campaign inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_campaign.campaignid WHERE vtiger_campaign.campaignname=? and vtiger_crmentity.deleted=0";

		$campaignid = $adb->query_result($adb->pquery($query, array($campaign_name)),0,'campaignid');

		if($campaignid == '' || !isset($campaignid)){
			$campaignid = 0;
		}

		$this->column_fields['campaignid'] = $campaignid;
		$adb->println("Exit map_campaign_source. Fetched Campaign for '".$campaign_name."' and the campaignid = $campaignid");
    }

	var $importable_fields = Array();

	/** Constructor which will set the importable_fields as $this->importable_fields[$key]=1 in this object where key is the fieldname in the field table
	 */
	function ImportOpportunity() {
		parent::Potentials();
		$this->log = LoggerManager::getLogger('import_opportunity');
		$this->db = PearDatabase::getInstance();

		$this->db->println("IMP ImportOpportunity");
		$this->initImportableFields("Potentials");		
		$this->db->println($this->importable_fields);
	}

	// Module Sequence Numbering	
	function modseq_number() {
		$this->column_fields['potential_no'] = '';
	}
	// END
}
?>