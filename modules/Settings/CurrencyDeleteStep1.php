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

require_once('include/utils/utils.php');

global $mod_strings;
global $app_strings;
global $theme;
$theme_path="themes/".$theme."/";

$delete_currency_id = $_REQUEST['id'];
$sql = "select * from vtiger_currency_info where id=?";
$result = $adb->pquery($sql, array($delete_currency_id));
$delete_currencyname = $adb->query_result($result,0,"currency_name");


$output='';
$output ='<div id="CurrencyDeleteLay"  class="layerPopup">
<form name="newCurrencyForm" action="index.php" style="margin="0" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="module" value="Settings">
<input type="hidden" name="action" value="CurrencyDelete">
<input type="hidden" name="delete_currency_id" value="'.$delete_currency_id.'">	
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="layerHeadingULine">
<tr>
	<td class="layerPopupHeading"  align="left" width="60%">'.$mod_strings["LBL_DELETE_CURRENCY"].'</td>
	<td align="right" width="40%"><img src="'. vtiger_imageurl('close.gif', $theme).'" border=0 alt="'.$app_strings["LBL_CLOSE"].'" title="'.$app_strings["LBL_CLOSE"].'" style="cursor:pointer;" onClick="document.getElementById(\'CurrencyDeleteLay\').style.display=\'none\'";></td>
</tr>
<table>
<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
	<tr>
		<td class=small >
			<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
				<tr>
					<td width="50%" class="cellLabel small"><b>'.$mod_strings['LBL_CURRDEL'].'</b></td>
					<td width="50%" class="cellText small"><b>'.getTranslatedCurrencyString($delete_currencyname).'</b></td>
				</tr>
				<tr>
					<td class="cellLabel small"><b>'.$mod_strings['LBL_TRANSCURR'].'</b></td>
					<td class="cellText small">';
						   
				$output.='<select class="select small" name="transfer_currency_id" id="transfer_currency_id">';
						 
						 global $adb;	
						 $sql = "select * from vtiger_currency_info where currency_status = ? and deleted=0";
						 $result = $adb->pquery($sql, array('Active'));
						 $temprow = $adb->fetch_array($result);
						 do
						 {
							$currencyname=$temprow["currency_name"];
							$currencyid=$temprow["id"];
							if($delete_currency_id 	!= $currencyid)
							{	 
								$output.='<option value="'.$currencyid.'">'.getTranslatedCurrencyString($currencyname).'</option>';
							}	
						 }while($temprow = $adb->fetch_array($result));
				
				$output.='</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
	<tr>
		<td align="center"><input type="button" onclick="transferCurrency('.$delete_currency_id.')" name="Delete" value="'.$app_strings["LBL_SAVE_BUTTON_LABEL"].'" class="crmbutton small save">
		</td>
	</tr>
</table>
</form></div>';

echo $output;
?>
