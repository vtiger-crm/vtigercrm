<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

global $theme, $mod_strings;
$theme_path="themes/".$theme."/";

$productid = vtlib_purify($_REQUEST['productid']);
$rowid = vtlib_purify($_REQUEST['curr_row']);
$product_total = vtlib_purify($_REQUEST['productTotal']);

$tax_details = getTaxDetailsForProduct($productid,'all');//we should pass available instead of all if we want to display only the available taxes.
$associated_tax_count = count($tax_details);


$tax_div = '
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small" id="tax_table'.$rowid.'">
		   <tr>
			<td id="tax_div_title'.$rowid.'" nowrap align="left" ><b>'.$app_strings['LABEL_SET_TAX_FOR'].' : '.$product_total.'</b></td>
			<td>&nbsp;</td>
			<td align="right"><img src="'. vtiger_imageurl('close.gif', $theme).'" border="0" onClick="fnHidePopDiv(\'tax_div'.$rowid.'\')" style="cursor:pointer;"></td>
		   </tr>
	   ';

$net_tax_total = 0.00;
for($i=0,$j=$i+1;$i<count($tax_details);$i++,$j++)
{
	$tax_name = $tax_details[$i]['taxname'];
	$tax_label = $tax_details[$i]['taxlabel'];
	$tax_percentage = $tax_details[$i]['percentage'];
	$tax_name_percentage = $tax_name."_percentage".$rowid;
	$tax_id_name = "hidden_tax".$j."_percentage".$rowid;//used to store the tax name, used in function callTaxCalc
	$tax_name_total = "popup_tax_row".$rowid;//$tax_name."_total".$rowid;
	$tax_total = $product_total*$tax_percentage/100.00;

	$net_tax_total += $tax_total;
	$tax_div .= '
		   <tr>
			<td align="left" class="lineOnTop">
				<input type="text" class="small" size="5" name="'.$tax_name_percentage.'" id="'.$tax_name_percentage.'" value="'.$tax_percentage.'" onBlur="calcCurrentTax(\''.$tax_name_percentage.'\','.$rowid.','.$i.');calcTotal();">&nbsp;%
				<input type="hidden" id="'.$tax_id_name.'" value="'.$tax_name_percentage.'">
			</td>
			<td align="center" class="lineOnTop">'.$tax_label.'</td>
			<td align="right" class="lineOnTop">
				<input type="text" class="small" size="6" name="'.$tax_name_total.'" id="'.$tax_name_total.'" style="cursor:pointer;" value="'.$tax_total.'" readonly>
			</td>
		   </tr>
	    ';
}

$tax_div .= '</table>';

if($associated_tax_count == 0)
{
	$tax_div .= '<div align="left" class="lineOnTop" width="100%">'.$mod_strings['LBL_NO_TAXES_ASSOCIATED'].'.</div>';
}

$tax_div .= '<input type="hidden" id="hdnTaxTotal'.$rowid.'" name="hdnTaxTotal'.$rowid.'" value="'.$net_tax_total.'">';

echo $tax_div;

?>