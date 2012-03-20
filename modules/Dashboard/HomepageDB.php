<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once("modules/Dashboard/Entity_charts.php");
require('user_privileges/user_privileges_'.$current_user->id.'.php');

global $current_user,$user_id,$date_start,$end_date,$tmp_dir,$mod_strings,$app_strings;
$type='recordsforuser';

// Performance Optimization: Using new API to generate dashboard chart
/*
$module_arr = Array ('Accounts','Contacts','Leads','Potentials','Quotes','Invoice','PurchaseOrder', 'SalesOrder','Calendar','HelpDesk','Campaigns');
foreach ($module_arr as $key => $mod_name){
	if(isPermitted("$mod_name","index",'') == 'yes'){

		$permitted_mod_list[$key] = $mod_name; 
	}
}

if(is_array($permitted_mod_list)){
	$moduleList = implode("','",$permitted_mod_list);
}

$homepagedb_query = "select * from vtiger_crmentity se left join vtiger_leaddetails le on le.leadid=se.crmid left join vtiger_troubletickets tt on tt.ticketid=se.crmid left join vtiger_activity act on act.activityid=se.crmid left join vtiger_potential pot on pot.potentialid=se.crmid where se.deleted=0 and (le.converted=0 or le.converted is null) and (pot.sales_stage not in('".$app_strings['LBL_CLOSE_WON']."','".$app_strings['LBL_CLOSE_LOST']."') or pot.sales_stage is null) and (tt.status!='Closed' or tt.status is null) and ((act.status!='Completed' and act.status!='Deferred') or act.status is null) and ((act.eventstatus!='Held' and act.eventstatus!='Not Held') or act.eventstatus is null) and setype in ('".$moduleList."') and se.deleted=0 and se.smownerid=".$current_user->id;
$graph_by="setype";
$graph_title=$mod_strings['recordsforuser'].' '.$current_user->user_name;
$module="Home";
$where="";
$query=$homepagedb_query;

//Giving the Cached image name	
$cache_file_name=abs(crc32($current_user->id))."_".$type."_".crc32($date_start.$end_date).".png";
    $html_imagename=$graph_by; //Html image name for the graph
$graph_details=module_Chart($current_user->id,$date_start,$end_date,$query,$graph_by,$graph_title,$where,$module,$type);
*/

// Performance Optimization
$graph_details = module_Chart_HomePageDashboard($current_user);
	
if (!empty($graph_details) && $graph_details[1] != 0) { // END
    $name_val=$graph_details[0];
    $cnt_val=$graph_details[1];
    $graph_title=$graph_details[2];
    $target_val=$graph_details[3];
    $graph_date=$graph_details[4];
    $urlstring=$graph_details[5];
    $cnt_table=$graph_details[6];
	$test_target_val=$graph_details[7];

    $width=560;
    $height=225;
    $top=30;
    $left=140;
    $bottom=120;
    $title=$graph_title;
	$sHTML = render_graph($tmp_dir."vert_".$cache_file_name,$html_imagename."_vert",$cnt_val,$name_val,$width,$height,$left,$right,$top,$bottom,$title,$target_val,"vertical");
	echo $sHTML;
}else{
	echo $mod_strings['LBL_NO_DATA'];	
}

?>
