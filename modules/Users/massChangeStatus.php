<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/



require_once('include/ComboUtil.php');
require_once('modules/Leads/Leads.php');
global $app_list_strings;
global $app_strings;
global $current_user;

$focus = new Leads();
$idlist = $_POST['idlist'];
//echo $idlist;

// Get _dom arrays from Database
$comboFieldNames = Array('leadstatus'=>'lead_status_dom');
$comboFieldArray = getComboArray($comboFieldNames);

?>
<script language="javascript">
function updateOwner()
{
	var username=document.setLeadOwner.lead_owner.value;
	//alert(username);
	document.setLeadOwner.user_id.value=username
	document.setLeadOwner.action="index.php?module=Users&action=updateLeadDBStatus"
}
function updateStatus()
{
	var leadstatusvalue=document.setLeadStatus.lead_status.value
	//alert(leadstatusvalue);
	document.setLeadStatus.leadval.value=leadstatusvalue;
	document.setLeadStatus.action="index.php?module=Users&action=updateLeadDBStatus"
}
function goBack()
{
	document.setLeadStatus.action="index.php?module=Leads&action=index"
}
function goBack1()
{
	document.setLeadOwner.action="index.php?module=Leads&action=index"
}
</script>
<?php

if(isset($_REQUEST['change_status']) && $_REQUEST['change_status']=='true')
{
   ?>
<form name="setLeadStatus" method="post" onsubmit="VtigerJS_DialogBox.block();">
  <?php
		echo get_module_title($mod_strings['LBL_MODULE_NAME'], "Leads : Change Status", true); 
	?>
  <br>
  <table width="40%" border="0" cellspacing="0" cellpadding="0" class="formOuterBorder"> 
  <tr> 
    <td class="formSecHeader">Status Information</td>
  </tr>
  <tr> 
    <td> <table width="100%" border="0" cellspacing="1" cellpadding="2">
        <tr> 
		  <td class="dataLabel">Select New Status:</td>
          <td><select name='lead_status'>
              <?php
			echo get_select_options_with_id($comboFieldArray['lead_status_dom'], $focus->lead_status);
		?>
            </select></td>
        </tr>
      </table></td>
  </tr>
  </table>
  <br>
  <table width="40%" cellpadding="0" cellspacing="0" border="0">
    <tr> 
      <td> <div align="center"> 
          <input type="submit" name="submit" class="button" value="Update Status" onclick="return updateStatus()">
		  <input type="submit" name="Cancel" class="button" value="Cancel" onclick="return goBack()">
        </div></td>
    </tr>
  </table>
  <input type="hidden" name="leadval">
  <input type="hidden" name="idlist" value="<?php echo $idlist ?>">
</form>
<?php
}
elseif(isset($_REQUEST['change_owner']) && $_REQUEST['change_owner']=='true')
{
	$result=$adb->pquery("select * from vtiger_users", array());
	for($i=0;$i<$adb->num_rows($result);$i++)
	{
		$useridlist[$i]=$adb->query_result($result,$i,'id');
		$usernamelist[$useridlist[$i]]=$adb->query_result($result,$i,'user_name');
	}

?>
<form name="setLeadOwner" method="post">
  <?php
		echo get_module_title($mod_strings['LBL_MODULE_NAME'], "Leads : Change Owner", true); 
	?>
  <br>
  <table width="40%" border="0" cellspacing="0" cellpadding="0" class="formOuterBorder"> 
  <tr> 
    <td class="formSecHeader">Owner Information</td>
  </tr>
  <tr> 
    <td> <table width="100%" border="0" cellspacing="1" cellpadding="2">
        <tr> 
		  <td class="dataLabel">Select New Owner:</td>
          <td><select name='lead_owner'>
              <?php
			echo get_select_options_with_id($usernamelist, $focus->lead_owner);
		?>
            </select></td>
        </tr>
      </table></td>
  </tr>
  </table>
  <br>
  <table width="40%" cellpadding="0" cellspacing="0" border="0">
    <tr> 
      <td> <div align="center"> 
          <input type="submit" name="submit" class="button" value="Update Owner" onclick="return updateOwner()">
		  <input type="submit" name="Cancel" class="button" value="cancel" onclick="return goBack1()">
        </div></td>
    </tr>
  </table>
  <input type="hidden" name="user_id">
  <input type="hidden" name="idlist" value="<?php echo $idlist ?>">
</form>
<?php
}
?>