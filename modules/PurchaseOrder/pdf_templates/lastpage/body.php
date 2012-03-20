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


// Fun with watermarks :)
$waterMarkPositions=array("60","110");
$waterMarkRotate=array("0","0","0");
$pdf->watermark( $app_strings["Thank You"], $waterMarkPositions, $waterMarkRotate );
$waterMarkPositions=array("30","130");
$pdf->watermark( $app_strings["For Your Business"], $waterMarkPositions, $waterMarkRotate );

// blowing bubbles
$Bubble=array("10","80","170","4");
$pdf->addBubble("", "", $Bubble);

$Bubble=array("168","131","12");
//$pdf->addBubbleBlock("Neat Look", "For A", $Bubble);
$Bubble=array("10","131","12");
//$pdf->addBubbleBlock("The Corners", "Line Up", $Bubble);


/* ************* Begin Totals ************************** */
$totalBlock=array("59","135");
$names=array($app_strings["Subtotal"],$app_strings["Tax"],$app_strings["Adjustment"],$app_strings["Total"]);
$totals=array($price_subtotal,$price_salestax,$price_adjustment,$price_total);
$pdf->addTotalsRec($names,$totals,$totalBlock);
/* ************* End Totals *************************** */

// lets add an image :)
$imageBlock=array("15","95","8","8");
$pdf->addImage( "sale.jpeg", $imageBlock);
$imageBlock=array("185","95","8","8");
$pdf->addImage( "sale.jpeg", $imageBlock);

// descriptions that change sizes!
$descc=count(explode("\n",$description));
$condc=count(explode("\n",$conditions));
if( (strlen($description) > 256) || (strlen($conditions) > 256) || $condc >6 || $descc > 6 )
        $num=255;
else
        $num=150;

/* **************** Begin Description ****************** */
$descBlock=array("10","160","53", $num);
$pdf->addDescBlock($description, $app_strings["Description"], $descBlock);


$termBlock=array("107","160","53", $num);
$pdf->addDescBlock($conditions, $app_strings["Terms & Conditions"], $termBlock);

/* ************** End Terms *********************** */


?>
