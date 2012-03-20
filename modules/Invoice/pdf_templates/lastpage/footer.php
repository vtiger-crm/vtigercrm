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



// draw a broken line
$width=3;
$area=216;
$pad=2;

for ($i=10;$i<200;$i++) {
	$linePos=array($i,$area,$width);
	$pdf->drawLine($linePos);
	$i = (($i+$width)+$pad)-1;
}

// company addy
if($org_phone != '')
$phone="\n".$app_strings["Phone"].":    ".$org_phone;	
if($org_fax != '')
  $fax ="\nFax:        ".$org_fax;	
$companyBlockPositions=array( "10","220","60" );
$companyText=$org_address."\n".$org_city.", ".$org_state." ".$org_code." ".$org_country." ".$phone." ".$fax."\n".$org_website ;
$pdf->addTextBlock( $org_name, $companyText ,$companyBlockPositions );


// billing Address
$billPositions = array("85","235","60");
if(trim($bill_street)!='')
	$billText = $bill_street."\n";
if(trim($bill_city) !='')
	$billText .= $bill_city.", ";
if(trim($bill_state)!='' || trim($bill_code)!= '')
	$billText .= $bill_state." ".$bill_code."\n";

	$billText .=$bill_country;
$pdf->addTextBlock($app_strings["Billing Address"].":",$billText, $billPositions);

// totals
$totalBlock=array("145","235","10", "110");
$totalText=$app_strings["Subtotal"].":      ".$price_subtotal."\n".
	   $app_strings["Tax"].":              ".$price_salestax."\n".
	   $app_strings["Adjustment"].":  ".$price_adjustment."\n".
	   $app_strings["Total"].":            ".$price_total;
$pdf->addDescBlock($totalText, $app_strings["Total Due"], $totalBlock);

$blurbBlock=array("10","265","150", "60");
$blockText=$app_strings["Detach_Info"];
$pdf->addDescBlock($blockText, $app_strings["Instructions"], $blurbBlock);

?>
