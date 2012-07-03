<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('Smarty_setup.php');
require_once('modules/Products/Products.php');
require_once('include/utils/utils.php');

global $app_strings,$mod_strings,$current_language,$theme,$log,$current_user,$default_charset,$adb;
$current_module_strings = return_module_language($current_language, 'Products');

$pricebook_id = vtlib_purify($_REQUEST['pricebook_id']);
$currency_id = vtlib_purify($_REQUEST['currency_id']);
if ($currency_id == null) $currency_id = fetchCurrency($current_user->id);
$parenttab = getParentTab();

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('modules/Vtiger/layout_utils.php');

if(getFieldVisibilityPermission('Products',$current_user->id,'unit_price') != '0'){
	echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>$app_strings[LBL_UNIT_PRICE_NOT_PERMITTED]</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>								   						     </td>
		</tr>
		</tbody></table>
		</div>";
	echo "</td></tr></table>";
	exit();
}

$pricebookname = getPriceBookName($pricebook_id);

$smarty= new vtigerCRM_Smarty;
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);

$focus = new Products();

if (isset($_REQUEST['order_by']))
	$order_by = $adb->sql_escape_string($_REQUEST['order_by']);

$url_string = ''; // assigning http url string
$sorder = 'ASC';  // Default sort order
if(isset($_REQUEST['sorder']) && $_REQUEST['sorder'] != '')
	$sorder = $adb->sql_escape_string($_REQUEST['sorder']);


//Retreive the list of Products
$list_query = getListQuery("Products");

if(isset($order_by) && $order_by != '')
{
	$list_query .= ' and vtiger_products.discontinued<>0  ORDER BY '.$order_by.' '.$sorder;
}

$list_query .=  " and vtiger_products.discontinued<>0 group by vtiger_crmentity.crmid";
$list_result = $adb->query($list_query);
$num_rows = $adb->num_rows($list_result);

$record_string= "Total No of Product Available : ".$num_rows;

//Retreiving the array of already releated products
$sql1 = "select productid from vtiger_pricebookproductrel
		 INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_pricebookproductrel.productid
		 WHERE vtiger_crmentity.setype='Products' AND vtiger_crmentity.deleted=0 AND pricebookid=?";
$res1 = $adb->pquery($sql1, array($pricebook_id));
$num_prod_rows = $adb->num_rows($res1);
$prod_array = Array();
for($i=0; $i<$num_prod_rows; $i++)
{
	$prodid=$adb->query_result($res1,$i,"productid");
	$prod_array[$prodid] = $prodid;
}


//Buttons Add To PriceBook and Cancel
$other_text = '
	<table width="95%" border="0" cellpadding="1" cellspacing="0" align="center">
	<form name="addToPB" method="POST" id="addToPB">
	   <tr>
		<td align="center">&nbsp;
			<input name="pricebook_id" type="hidden" value="'.$pricebook_id.'">
			<input name="idlist" type="hidden">
			<input name="viewname" type="hidden">
	';

	//we should not display the Add to PriceBook button if there is no products to associate
	if($num_rows != $num_prod_rows && $num_rows > 0)
	        $other_text .='<input class="crmbutton small save" type="submit" value="'.$mod_strings[LBL_ADD_PRICEBOOK_BUTTON_LABEL].'" onclick="return addtopricebook()"/>';

$other_text .='&nbsp;<input title="'.$app_strings[LBL_CANCEL_BUTTON_TITLE].'" accessKey="'.$app_strings[LBL_CANCEL_BUTTON_KEY].'" class="crmbutton small cancel" onclick="window.history.back()" type="button" name="button" value="'.$app_strings[LBL_CANCEL_BUTTON_LABEL].'"></td>';

$other_text .='
	   </tr>
	</table>';

$smarty->assign("PRODUCTLISTHEADER", get_form_header($current_module_strings['LBL_LIST_FORM_TITLE'], $other_text, false ));

//Retreive the List View Table Header

$list_header = '';
$list_header .= '<tr>';
$list_header .='<td class="lvtCol"><input type="checkbox" name="selectall" onClick=\'toggleSelect(this.checked,"selected_id");updateAllListPrice()\'></td>';
$list_header .= '<td class="lvtCol">'.$mod_strings['LBL_LIST_PRODUCT_NAME'].'</td>';
if(getFieldVisibilityPermission('Products', $current_user->id, 'productcode') == '0')
	$list_header .= '<td class="lvtCol">'.$mod_strings['LBL_PRODUCT_CODE'].'</td>';
if(getFieldVisibilityPermission('Products', $current_user->id, 'unit_price') == '0')
	$list_header .= '<td class="lvtCol">'.$mod_strings['LBL_PRODUCT_UNIT_PRICE'].'</td>';
$list_header .= '<td class="lvtCol">'.$mod_strings['LBL_PB_LIST_PRICE'].'</td>';
$list_header .= '</tr>';

$smarty->assign("LISTHEADER", $list_header);


//if the product is not associated already then we should display that products
$new_prod_array = array();
$unit_price_array=array();
$field_name_array=array();
$entity_id_array =array();
for($i=0; $i<$num_rows; $i++)
{
	$entity_id = $adb->query_result($list_result,$i,"crmid");
	if(! array_key_exists($entity_id, $prod_array))
	{
		$new_prod_array[] = $entity_id;
	}
	$entity_id_array[$entity_id] = $i;
}
$prod_price_list = getPricesForProducts($currency_id, $new_prod_array);

$list_body ='';
for($i=0; $i<count($new_prod_array); $i++)
{
	$log->info("Products :: Showing the List of products to be added in price book");
	$entity_id = $new_prod_array[$i];
	if(isPermitted('Products','EditView',$entity_id) == 'yes') {
		
		$list_body .= '<tr class="lvtColData" onmouseover="this.className=\'lvtColDataHover\'" onmouseout="this.className=\'lvtColData\'" bgcolor="white">';
		$unit_price = $prod_price_list[$entity_id];
		$field_name = $entity_id."_listprice";
		$unit_price_array[]='"'.CurrencyField::convertToUserFormat($unit_price, null, true).'"';
		$field_name_array[]="'".$field_name."'";

		$list_body .= '<td><INPUT type=checkbox NAME="selected_id" id="check_'.$entity_id.'" value= '.$entity_id.' onClick=\'toggleSelectAll(this.name,"selectall");updateListPriceForField("'.$field_name.'",this)\'></td>';
		$list_body .= '<td>'.$adb->query_result($list_result,$entity_id_array[$entity_id],"productname").'</td>';

		if(getFieldVisibilityPermission('Products', $current_user->id, 'productcode') == '0')
			$list_body .= '<td>'.$adb->query_result($list_result,$entity_id_array[$entity_id],"productcode").'</td>';
		if(getFieldVisibilityPermission('Products', $current_user->id, 'unit_price') == '0')
			$list_body .= '<td>'.CurrencyField::convertToUserFormat($unit_price, null, true).'</td>';

		$list_body .='<td>';
		if(isPermitted("PriceBooks","EditView","") == 'yes')
			$list_body .= '<input type="text" name="'.$field_name.'" style="visibility:hidden;" id="'.$field_name.'">';
		else
			$list_body .= '<input type="text" name="'.$field_name.'" style="visibility:hidden;" readonly id="'.$field_name.'">';
		$list_body .= '</td></tr>';
	}
}

$smarty->assign("UNIT_PRICE_ARRAY",implode(",",$unit_price_array));
$smarty->assign("FIELD_NAME_ARRAY",implode(",",$field_name_array));

if($order_by !='')
	$url_string .="&order_by=".$order_by;
if($sorder !='')
	$url_string .="&sorder=".$sorder;

$smarty->assign("LISTENTITY", $list_body);
$smarty->assign("CATEGORY", $parenttab);

$smarty->display("AddProductsToPriceBook.tpl");

?>