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

global $theme, $log;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$currencyid = $_REQUEST['currencyid'];
$products_list = $_REQUEST['productsList'];

$product_ids = explode("::", $products_list);

$price_list = array();

if (count($product_ids) > 0) {
	$prices_for_products = getPricesForProducts($currencyid, $product_ids);
	$prices_for_services = getPricesForProducts($currencyid, $product_ids, 'Services');
}

// To get the Price Values in the same order as the Products
for ($i=0;$i<count($product_ids);++$i) {
	$product_id = $product_ids[$i];
	// Pick the price for the product_id from service prices/ product prices based on which array it is set.
	$price_list[] = empty($prices_for_services[$product_id]) ? $prices_for_products[$product_id] : $prices_for_services[$product_id];
}

$price_values = implode("::", $price_list);
echo "SUCCESS$".$price_values;

?>
