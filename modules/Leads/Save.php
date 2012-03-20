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

require_once('modules/Leads/Leads.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/UserInfoUtil.php');

$local_log =& LoggerManager::getLogger('index');
global $log,$adb;
$focus = new Leads();
global $current_user;
$currencyid=fetchCurrency($current_user->id);
$rate_symbol = getCurrencySymbolandCRate($currencyid);
$rate = $rate_symbol['rate'];
$curr_symbol=$rate_symbol['symbol'];
//added to fix 4600
$search=vtlib_purify($_REQUEST['search_url']);

if(isset($_REQUEST['record']))
{
	$focus->id = $_REQUEST['record'];
}
if(isset($_REQUEST['mode']))
{
	$focus->mode = $_REQUEST['mode'];
}

//$focus->retrieve($_REQUEST['record']);

foreach($focus->column_fields as $fieldname => $val)
{
    if(isset($_REQUEST[$fieldname])) {
		if(is_array($_REQUEST[$fieldname]))
			$value = $_REQUEST[$fieldname];
		else
			$value = trim($_REQUEST[$fieldname]);	
        $log->info("the value is ".$value);
        $focus->column_fields[$fieldname] = $value;
    }
}
if(isset($_REQUEST['annualrevenue'])) {
    $value = convertToDollar($_REQUEST['annualrevenue'],$rate);
    $focus->column_fields['annualrevenue'] = $value;
}

if($_REQUEST['assigntype'] == 'U') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
} elseif($_REQUEST['assigntype'] == 'T') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
}

$focus->save("Leads");

$return_id = $focus->id;
	 $log->info("the return id is ".$return_id);
$parenttab = getParentTab();
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != "") $return_module = vtlib_purify($_REQUEST['return_module']);
else $return_module = "Leads";
if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != "") $return_action = vtlib_purify($_REQUEST['return_action']);
else $return_action = "DetailView";
if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "") $return_id = vtlib_purify($_REQUEST['return_id']);

$local_log->debug("Saved record with id of ".$return_id);
//code added for returning back to the current view after edit from list view
if($_REQUEST['return_viewname'] == '') $return_viewname='0';
if($_REQUEST['return_viewname'] != '')$return_viewname=vtlib_purify($_REQUEST['return_viewname']);

if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] == "Campaigns")
{
	if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "")
	{
		 $campLeadStatusResult = $adb->pquery("select campaignrelstatusid from vtiger_campaignleadrel where campaignid=? AND leadid=?",array($_REQUEST['return_id'], $focus->id));
		 $leadStatus = $adb->query_result($campLeadStatusResult,0,'campaignrelstatusid');
		 $sql = "delete from vtiger_campaignleadrel where leadid = ?";
		 $adb->pquery($sql, array($focus->id));
		 if(isset($leadStatus) && $leadStatus !=''){
		 $sql = "insert into vtiger_campaignleadrel values (?,?,?)";
		 $adb->pquery($sql, array($_REQUEST['return_id'], $focus->id,$leadStatus));
		 }
		 else{
		 $sql = "insert into vtiger_campaignleadrel values (?,?,1)";
		 $adb->pquery($sql, array($_REQUEST['return_id'], $focus->id));
		}
	}
}
header("Location: index.php?action=$return_action&module=$return_module&record=$return_id&parenttab=$parenttab&viewname=$return_viewname&start=".vtlib_purify($_REQUEST['pagenumber']).$search);
/** Function to save the Lead custom fields info into database
 *  @param integer $entity_id - leadid
*/
function save_customfields($entity_id)
{
	$log->debug("Entering save_customfields(".$entity_id.") method ...");
	 $log->debug("save custom vtiger_field invoked ".$entity_id);
	global $adb;
	$dbquery="select * from customfields where module='Leads'";
	$result = $adb->pquery($dbquery, array());
	$custquery = "select * from leadcf where leadid=?";
    $cust_result = $adb->pquery($custquery, array($entity_id));
	if($adb->num_rows($result) != 0)
	{
		
		$columns='';
		$params = array();
		$update='';
		$noofrows = $adb->num_rows($result);
		for($i=0; $i<$noofrows; $i++)
		{
			$fldName=$adb->query_result($result,$i,"fieldlabel");
			$colName=$adb->query_result($result,$i,"column_name");
			if(isset($_REQUEST[$colName]))
			{
				$fldvalue=$_REQUEST[$colName];
				 $log->info("the columnName is ".$fldvalue);
				if(get_magic_quotes_gpc() == 1)
                		{
                        		$fldvalue = stripslashes($fldvalue);
                		}
			}
			else
			{
				$fldvalue = '';
			}
			if(isset($_REQUEST['record']) && $_REQUEST['record'] != '' && $adb->num_rows($cust_result) !=0)
			{
				//Update Block
				if($i == 0)
				{
					$update = $colName.'=?';
				}
				else
				{
					$update .= ', '.$colName.'=?';
				}
				array_push($params, $fldvalue);
			}
			else
			{
				//Insert Block
				if($i == 0)
				{
					$columns='leadid, '.$colName;
					array_push($params, $entity_id);
				}
				else
				{
					$columns .= ', '.$colName;
				}
				array_push($params, $fldvalue);
			}
			
				
		}
		if(isset($_REQUEST['record']) && $_REQUEST['record'] != '' && $adb->num_rows($cust_result) !=0)
		{
			//Update Block
			$query = 'update leadcf SET '.$update.' where leadid=?'; 
			array_push($params, $entity_id);
			$adb->pquery($query, $params);
		}
		else
		{
			//Insert Block
			$query = 'insert into leadcf ('.$columns.') values('. generateQuestionMarks($params) .')';
			$adb->pquery($query, $params);
		}
		
	}
	$log->debug("Exiting save_customfields method ...");	
}
?>
