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

//Following variables will be used in include/fpdf/templates/body.php

//where the bottom line starts. If you change this bottom then all related changes will be done automatically

//If the product description is too long and you want to avoid the display clearly then you can change bottom =170 or products_per_page=5 or less.

//Enter values less than 170 if value > 170 then last lines will be displayed in next page
$bottom="130";//"130"

//how may products per page 
$products_per_page="6";//used in modules/{PO/SO/Quotes/Invoice}/CreatePDF.php


//where the top line starts
$body_top="80";

//This is the y position where the nettotal starts, next fields will be displayed in bottom+n(6) pixels
$nettotal_y = $bottom+38;//"168";
$next_y = "6";


//Following variables will be used in modules/{PO/SO/Quotes/Invoice}/pdf_templates/footer.php
$top = $nettotal_y+48;//"216";


//if true then Description and Terms & Conditions will be displayed in all pages, false - displayed only in the last page
$display_desc_tc = 'true';


?>
