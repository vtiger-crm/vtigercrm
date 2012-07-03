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


define('USD',"$");
define('EURO', chr(128) );

// ************** Begin company information *****************
$imageBlock=array("10","3","0","0");
$pdf->addImage( $logo_name, $imageBlock);

// x,y,width
if($org_phone != '')
  $phone ="\n".$app_strings["Phone"].":".$org_phone;
	
$companyBlockPositions=array( "10","23","62" );
$companyText=$org_address."\n".$org_city.", ".$org_state." ".$org_code." ".$org_country." ".$phone."\n".$org_website;
$pdf->addTextBlock( $org_name, $companyText ,$companyBlockPositions );

// ************** End company information *******************



// ************* Begin Top-Right Header ***************
// title
$titleBlock=array("147","7");
$pdf->title( $app_strings["Invoice"],"", $titleBlock );

$soBubble=array("168","17","12");
$pdf->addBubbleBlock($so_name, $app_strings["SalesOrder"], $soBubble);

$poBubble=array("114","17","12");
$pdf->addBubbleBlock($po_name, $app_strings["PurchaseOrder"], $poBubble);

// page number
$pageBubble=array("147","17",0);
$pdf->addBubbleBlock($page_num, $app_strings["Page"], $pageBubble);
// ************** End Top-Right Header *****************



// ************** Begin Addresses **************
// shipping Address
$shipLocation = array("147","40","61");
if(trim($ship_street)!='')
	$shipText = $ship_street."\n";
if(trim($ship_city) !='')
	$shipText .= $ship_city.", ";
if(trim($ship_state)!='' || trim($ship_code)!= '')
	$shipText .= $ship_state." ".$ship_code."\n";

	$shipText .=$ship_country;

$pdf->addTextBlock( $app_strings["Shipping Address"].":", $shipText, $shipLocation );

// billing Address
$billPositions = array("10","51","61");
if(trim($bill_street)!='')
	$billText = $bill_street."\n";
if(trim($bill_city) !='')
	$billText .= $bill_city.", ";
if(trim($bill_state)!='' || trim($bill_code)!= '')
	$billText .= $bill_state." ".$bill_code."\n";

	$billText .=$bill_country;
$pdf->addTextBlock($app_strings["Billing Address"].":",$billText, $billPositions);
// ********** End Addresses ******************



/*  ******** Begin Invoice Data ************************ */ 
// terms block
$termBlock=array("10","67");
$pdf->addRecBlock($account_name, $app_strings["Customer Name"], $termBlock);

// issue date block
$issueBlock=array("80","37");
$pdf->addRecBlock(DateTimeField::convertToUserFormat(date("Y-m-d")), $app_strings["Issue Date"],$issueBlock);

// due date block
$dueBlock=array("81","52");
$pdf->addRecBlock($valid_till, $app_strings["Due Date"],$dueBlock);

// Contact Name block
$conBlock=array("79","67");
$pdf->addRecBlock($contact_name, $app_strings["Contact Name"],$conBlock);

// vtiger_invoice number block
$invBlock=array("145","67");
$pdf->addRecBlock($invoice_no, $app_strings["Invoice Number"],$invBlock);

/* ************ End Invoice Data ************************ */



?>
