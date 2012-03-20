<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

global $mod_strings;
global $app_strings;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

if(isset($_REQUEST['return_module']) && $_REQUEST['return_module']=="PriceBooks")
{
	$pricebook_id = vtlib_purify($_REQUEST['pricebook_id']);
	$product_id = vtlib_purify($_REQUEST['record']);
	$listprice = vtlib_purify($_REQUEST['listprice']);
	$return_action = "CallRelatedList";
	$return_id = vtlib_purify($_REQUEST['pricebook_id']);
}
else
{
	$product_id = vtlib_purify($_REQUEST['record']);
	$pricebook_id = vtlib_purify($_REQUEST['pricebook_id']);
	$listprice = getListPrice($product_id,$pricebook_id);
	$return_action = "CallRelatedList";
	$return_id = vtlib_purify($_REQUEST['pricebook_id']);
}
$output='';
$output ='<div id="roleLay" style="display:block;" class="layerPopup">
	<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
	<tr>
		<td class=layerPopupHeading " align="left">'.$mod_strings["LBL_EDITLISTPRICE"].'</td>
		<td align="right" class="small"><img src="'. vtiger_imageurl('close.gif', $theme) .'" border=0 alt="'.$app_strings["LBL_CLOSE"].'" title="'.$app_strings["LBL_CLOSE"].'" style="cursor:pointer" onClick="document.getElementById(\'editlistprice\').style.display=\'none\';"></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
<tr>
	<td class="small">
	<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
	<tr>
		<td width="50%" class="cellLabel small"><b>'.$mod_strings["LBL_EDITLISTPRICE"].'</b></td>
		<td width="50%" class="cellText small"><input class="dataInput" type="text" id="list_price" name="list_price" value="'.$listprice.'" /></td>
	</tr>
	</table>
	</td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
<tr>
	<td colspan="3" align="center" class="small">
	<input title="'.$app_strings["LBL_SAVE_BUTTON_LABEL"].'" class="crmbutton small save" onClick="if(verify_data() == true) gotoUpdateListPrice('.$return_id.','.$pricebook_id.','.$product_id.'); else document.getElementById(\'roleLay\').style.display=\'inline\'; return false;" type="button" name="button" value="'.$app_strings["LBL_SAVE_BUTTON_LABEL"].'">
	<input title="'.$app_strings["LBL_CANCEL_BUTTON_LABEL"].'" class="crmbutton small cancel" onClick="document.getElementById(\'editlistprice\').style.display=\'none\';" type="button" name="button" value="'.$app_strings["LBL_CANCEL_BUTTON_LABEL"].'">
	</td>
</tr>
</table>
</div>';

echo $output;

?>