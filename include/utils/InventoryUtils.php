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

/**
 * This function returns the Product detail block values in array format.
 * Input Parameter are $module - module name, $focus - module object, $num_of_products - no.of vtiger_products associated with it  * $associated_prod = associated product details
 * column vtiger_fields/
 */

function getProductDetailsBlockInfo($mode,$module,$focus='',$num_of_products='',$associated_prod='')
{
	global $log;
	$log->debug("Entering getProductDetailsBlockInfo(".$mode.",".$module.",".$num_of_products.",".$associated_prod.") method ...");
	
	$productDetails = Array();
	$productBlock = Array();
	if($num_of_products=='')
	{
		$num_of_products = getNoOfAssocProducts($module,$focus);
	}
	$productDetails['no_products'] = $num_of_products;
	if($associated_prod=='')
        {
		$productDetails['product_details'] = getAssociatedProducts($module,$focus);
	}
	else
	{
		$productDetails['product_details'] = $associated_prod;
	}
	if($focus != '')
	{
		$productBlock[] = Array('mode'=>$focus->mode);
		$productBlock[] = $productDetails['product_details'];
		$productBlock[] = Array('taxvalue' => $focus->column_fields['txtTax']);
		$productBlock[] = Array('taxAdjustment' => $focus->column_fields['txtAdjustment']);
		$productBlock[] = Array('hdnSubTotal' => $focus->column_fields['hdnSubTotal']);
		$productBlock[] = Array('hdnGrandTotal' => $focus->column_fields['hdnGrandTotal']);
	}
	else
	{
		$productBlock[] = Array(Array());
		
	}
	$log->debug("Exiting getProductDetailsBlockInfo method ...");
	return $productBlock;
}

/**
 * This function updates the stock information once the product is ordered.
 * Param $productid - product id
 * Param $qty - product quantity in no's
 * Param $mode - mode type
 * Param $ext_prod_arr - existing vtiger_products 
 * Param $module - module name
 * return type void
 */

function updateStk($product_id,$qty,$mode,$ext_prod_arr,$module)
{
	global $log;
	$log->debug("Entering updateStk(".$product_id.",".$qty.",".$mode.",".$ext_prod_arr.",".$module.") method ...");
	global $adb;
	global $current_user;

	$log->debug("Inside updateStk function, module=".$module);
	$log->debug("Product Id = $product_id & Qty = $qty");

	$prod_name = getProductName($product_id);
	$qtyinstk= getPrdQtyInStck($product_id);
	$log->debug("Prd Qty in Stock ".$qtyinstk);
	
	$upd_qty = $qtyinstk-$qty;
	sendPrdStckMail($product_id,$upd_qty,$prod_name,$qtyinstk,$qty,$module);
	
	$log->debug("Exiting updateStk method ...");
}

/**
 * This function sends a mail to the handler whenever the product reaches the reorder level.
 * Param $product_id - product id
 * Param $upd_qty - updated product quantity in no's
 * Param $prod_name - product name
 * Param $qtyinstk - quantity in stock 
 * Param $qty - quantity  
 * Param $module - module name
 * return type void
 */

function sendPrdStckMail($product_id,$upd_qty,$prod_name,$qtyinstk,$qty,$module)
{
	global $log;
	$log->debug("Entering sendPrdStckMail(".$product_id.",".$upd_qty.",".$prod_name.",".$qtyinstk.",".$qty.",".$module.") method ...");
	global $current_user;
	global $adb;
	$reorderlevel = getPrdReOrderLevel($product_id);
	$log->debug("Inside sendPrdStckMail function, module=".$module);
	$log->debug("Prd reorder level ".$reorderlevel);
	if($upd_qty < $reorderlevel)
	{
		//send mail to the handler
		$handler=getPrdHandler($product_id);
		$handler_name = getUserName($handler);
		$to_address= getUserEmail($handler);

		//Get the email details from database;
		if($module == 'SalesOrder')
		{
			$notification_table = 'SalesOrderNotification';
			$quan_name = '{SOQUANTITY}';
		}
		if($module == 'Quotes')
		{
			$notification_table = 'QuoteNotification';
			$quan_name = '{QUOTEQUANTITY}';
		}
		if($module == 'Invoice')
		{
			$notification_table = 'InvoiceNotification';
		}
		$query = "select * from vtiger_inventorynotification where notificationname=?";
		$result = $adb->pquery($query, array($notification_table));

		$subject = $adb->query_result($result,0,'notificationsubject');
		$body = $adb->query_result($result,0,'notificationbody');
		$status = $adb->query_result($result,0,'status');
		
		if($status == 0 || $status == '')
				return false;
	
		$subject = str_replace('{PRODUCTNAME}',$prod_name,$subject);
		$body = str_replace('{HANDLER}',$handler_name,$body);	
		$body = str_replace('{PRODUCTNAME}',$prod_name,$body);	
		if($module == 'Invoice')
		{
			$body = str_replace('{CURRENTSTOCK}',$upd_qty,$body);	
			$body = str_replace('{REORDERLEVELVALUE}',$reorderlevel,$body);
		}
		else
		{
			$body = str_replace('{CURRENTSTOCK}',$qtyinstk,$body);	
			$body = str_replace($quan_name,$qty,$body);	
		}
		$body = str_replace('{CURRENTUSER}',$current_user->user_name,$body);	

		$mail_status = send_mail($module,$to_address,$current_user->user_name,$current_user->email1,decode_html($subject),nl2br(to_html($body)));
	}
	$log->debug("Exiting sendPrdStckMail method ...");
}

/**This function is used to get the quantity in stock of a given product
*Param $product_id - product id
*Returns type numeric
*/
function getPrdQtyInStck($product_id)
{
	global $log;
	$log->debug("Entering getPrdQtyInStck(".$product_id.") method ...");
	global $adb;
	$query1 = "SELECT qtyinstock FROM vtiger_products WHERE productid = ?";
	$result=$adb->pquery($query1, array($product_id));
	$qtyinstck= $adb->query_result($result,0,"qtyinstock");
	$log->debug("Exiting getPrdQtyInStck method ...");
	return $qtyinstck;
}

/**This function is used to get the reorder level of a product
*Param $product_id - product id
*Returns type numeric
*/

function getPrdReOrderLevel($product_id)
{
	global $log;
	$log->debug("Entering getPrdReOrderLevel(".$product_id.") method ...");
	global $adb;
	$query1 = "SELECT reorderlevel FROM vtiger_products WHERE productid = ?";
	$result=$adb->pquery($query1, array($product_id));
	$reorderlevel= $adb->query_result($result,0,"reorderlevel");
	$log->debug("Exiting getPrdReOrderLevel method ...");
	return $reorderlevel;
}

/**This function is used to get the handler for a given product
*Param $product_id - product id
*Returns type numeric
*/

function getPrdHandler($product_id)
{
	global $log;
	$log->debug("Entering getPrdHandler(".$product_id.") method ...");
	global $adb;
	$query1 = "SELECT handler FROM vtiger_products WHERE productid = ?";
	$result=$adb->pquery($query1, array($product_id));
	$handler= $adb->query_result($result,0,"handler");
	$log->debug("Exiting getPrdHandler method ...");
	return $handler;
}

/**	function to get the taxid
 *	@param string $type - tax type (VAT or Sales or Service)
 *	return int   $taxid - taxid corresponding to the Tax type from vtiger_inventorytaxinfo vtiger_table
 */
function getTaxId($type)
{
	global $adb, $log;
	$log->debug("Entering into getTaxId($type) function.");

	$res = $adb->pquery("SELECT taxid FROM vtiger_inventorytaxinfo WHERE taxname=?", array($type));
	$taxid = $adb->query_result($res,0,'taxid');

	$log->debug("Exiting from getTaxId($type) function. return value=$taxid");
	return $taxid;
}

/**	function to get the taxpercentage
 *	@param string $type       - tax type (VAT or Sales or Service)
 *	return int $taxpercentage - taxpercentage corresponding to the Tax type from vtiger_inventorytaxinfo vtiger_table
 */
function getTaxPercentage($type)
{
	global $adb, $log;
	$log->debug("Entering into getTaxPercentage($type) function.");

	$taxpercentage = '';

	$res = $adb->pquery("SELECT percentage FROM vtiger_inventorytaxinfo WHERE taxname = ?", array($type));
	$taxpercentage = $adb->query_result($res,0,'percentage');

	$log->debug("Exiting from getTaxPercentage($type) function. return value=$taxpercentage");
	return $taxpercentage;
}

/**	function to get the product's taxpercentage
 *	@param string $type       - tax type (VAT or Sales or Service)
 *	@param id  $productid     - productid to which we want the tax percentage
 *	@param id  $default       - if 'default' then first look for product's tax percentage and product's tax is empty then it will return the default configured tax percentage, else it will return the product's tax (not look for default value)
 *	return int $taxpercentage - taxpercentage corresponding to the Tax type from vtiger_inventorytaxinfo vtiger_table
 */
function getProductTaxPercentage($type,$productid,$default='')
{
	global $adb, $log;
	$log->debug("Entering into getProductTaxPercentage($type,$productid) function.");

	$taxpercentage = '';

	$res = $adb->pquery("SELECT taxpercentage
			FROM vtiger_inventorytaxinfo
			INNER JOIN vtiger_producttaxrel
				ON vtiger_inventorytaxinfo.taxid = vtiger_producttaxrel.taxid
			WHERE vtiger_producttaxrel.productid = ?
			AND vtiger_inventorytaxinfo.taxname = ?", array($productid, $type));
	$taxpercentage = $adb->query_result($res,0,'taxpercentage');

	//This is to retrive the default configured value if the taxpercentage related to product is empty
	if($taxpercentage == '' && $default == 'default')
		$taxpercentage = getTaxPercentage($type);


	$log->debug("Exiting from getProductTaxPercentage($productid,$type) function. return value=$taxpercentage");
	return $taxpercentage;
}

/**	Function used to add the history entry in the relevant tables for PO, SO, Quotes and Invoice modules
 *	@param string 	$module		- current module name
 *	@param int 	$id		- entity id
 *	@param string 	$relatedname	- parent name of the entity ie, required field venor name for PO and account name for SO, Quotes and Invoice
 *	@param float 	$total		- grand total value of the product details included tax
 *	@param string 	$history_fldval	- history field value ie., quotestage for Quotes and status for PO, SO and Invoice
 */
function addInventoryHistory($module, $id, $relatedname, $total, $history_fldval)
{
	global $log, $adb;
	$log->debug("Entering into function addInventoryHistory($module, $id, $relatedname, $total, $history_fieldvalue)");

	$history_table_array = Array(
					"PurchaseOrder"=>"vtiger_postatushistory",
					"SalesOrder"=>"vtiger_sostatushistory",
					"Quotes"=>"vtiger_quotestagehistory",
					"Invoice"=>"vtiger_invoicestatushistory"
				    );

	$histid = $adb->getUniqueID($history_table_array[$module]);
 	$modifiedtime = $adb->formatDate(date('Y-m-d H:i:s'), true);
 	$query = "insert into $history_table_array[$module] values(?,?,?,?,?,?)";
	$qparams = array($histid,$id,$relatedname,$total,$history_fldval,$modifiedtime);	
	$adb->pquery($query, $qparams);

	$log->debug("Exit from function addInventoryHistory");
}

/**	Function used to get the list of Tax types as a array
 *	@param string $available - available or empty where as default is all, if available then the taxes which are available now will be returned otherwise all taxes will be returned
 *      @param string $sh - sh or empty, if sh passed then the shipping and handling related taxes will be returned
 *      @param string $mode - edit or empty, if mode is edit, then it will return taxes including desabled.
 *      @param string $id - crmid or empty, getting crmid to get tax values..
 *	return array $taxtypes - return all the tax types as a array
 */
function getAllTaxes($available='all', $sh='',$mode='',$id='')
{
	global $adb, $log;
	$log->debug("Entering into the function getAllTaxes($available,$sh,$mode,$id)");
	$taxtypes = Array();
	if($sh != '' && $sh == 'sh')
	{
		$tablename = 'vtiger_shippingtaxinfo';
		$value_table='vtiger_inventoryshippingrel';
	}
	else
	{
		$tablename = 'vtiger_inventorytaxinfo';
		$value_table='vtiger_inventoryproductrel';
	}
	
	if($mode == 'edit' && $id != '' )
	{
		//Getting total no of taxes

		$result_ids=array();
		$result=$adb->pquery("select taxname,taxid from $tablename",array());
		$noofrows=$adb->num_rows($result);

		$inventory_tax_val_result=$adb->pquery("select * from $value_table where id=?",array($id));

		//Finding which taxes are associated with this (SO,PO,Invoice,Quotes) and getting its taxid.
		for($i=0;$i<$noofrows;$i++)
		{

			$taxname=$adb->query_result($result,$i,'taxname');
			$taxid=$adb->query_result($result,$i,'taxid');

			$tax_val=$adb->query_result($inventory_tax_val_result,0,$taxname);
			if($tax_val != '')
			{
				array_push($result_ids,$taxid);
			}

		}
		//We are selecting taxes using that taxids. So It will get the tax even if the tax is disabled.
		$where_ids='';
		if (count($result_ids) > 0)
		{
			$insert_str = str_repeat("?,", count($result_ids)-1);
			$insert_str .= "?";
			$where_ids="taxid in ($insert_str) or";
		}

		$res = $adb->pquery("select * from $tablename  where $where_ids  deleted=0 order by taxid",$result_ids);
	}
	else
	{
		//This where condition is added to get all products or only availble products
		if($available != 'all' && $available == 'available')
		{
			$where = " where $tablename.deleted=0";
		}
	
		$res = $adb->pquery("select * from $tablename $where order by deleted",array());

	}
	
	$noofrows = $adb->num_rows($res);
	for($i=0;$i<$noofrows;$i++)
	{
		$taxtypes[$i]['taxid'] = $adb->query_result($res,$i,'taxid');
		$taxtypes[$i]['taxname'] = $adb->query_result($res,$i,'taxname');
		$taxtypes[$i]['taxlabel'] = $adb->query_result($res,$i,'taxlabel');
		$taxtypes[$i]['percentage'] = $adb->query_result($res,$i,'percentage');
		$taxtypes[$i]['deleted'] = $adb->query_result($res,$i,'deleted');
	}
	$log->debug("Exit from the function getAllTaxes($available,$sh,$mode,$id)");
	
	return $taxtypes;
}


/**	Function used to get all the tax details which are associated to the given product
 *	@param int $productid - product id to which we want to get all the associated taxes
 *	@param string $available - available or empty or available_associated where as default is all, if available then the taxes which are available now will be returned, if all then all taxes will be returned otherwise if the value is available_associated then all the associated taxes even they are not available and all the available taxes will be retruned
 *	@return array $tax_details - tax details as a array with productid, taxid, taxname, percentage and deleted
 */
function getTaxDetailsForProduct($productid, $available='all')
{
	global $log, $adb;
	$log->debug("Entering into function getTaxDetailsForProduct($productid)");
	if($productid != '')
	{
		//where condition added to avoid to retrieve the non available taxes
		$where = '';
		if($available != 'all' && $available == 'available')
		{
			$where = ' and vtiger_inventorytaxinfo.deleted=0';
		}
		if($available != 'all' && $available == 'available_associated')
		{
			$query = "SELECT vtiger_producttaxrel.*, vtiger_inventorytaxinfo.* FROM vtiger_inventorytaxinfo left JOIN vtiger_producttaxrel ON vtiger_inventorytaxinfo.taxid = vtiger_producttaxrel.taxid WHERE vtiger_producttaxrel.productid = ? or vtiger_inventorytaxinfo.deleted=0 GROUP BY vtiger_inventorytaxinfo.taxid";
		}
		else
		{
			$query = "SELECT vtiger_producttaxrel.*, vtiger_inventorytaxinfo.* FROM vtiger_inventorytaxinfo INNER JOIN vtiger_producttaxrel ON vtiger_inventorytaxinfo.taxid = vtiger_producttaxrel.taxid WHERE vtiger_producttaxrel.productid = ? $where";
		}
		$params = array($productid);

		//Postgres 8 fixes
 		if( $adb->dbType == "pgsql")
 		    $query = fixPostgresQuery( $query, $log, 0);
		
		$res = $adb->pquery($query, $params);
		for($i=0;$i<$adb->num_rows($res);$i++)
		{
			$tax_details[$i]['productid'] = $adb->query_result($res,$i,'productid');
			$tax_details[$i]['taxid'] = $adb->query_result($res,$i,'taxid');
			$tax_details[$i]['taxname'] = $adb->query_result($res,$i,'taxname');
			$tax_details[$i]['taxlabel'] = $adb->query_result($res,$i,'taxlabel');
			$tax_details[$i]['percentage'] = $adb->query_result($res,$i,'taxpercentage');
			$tax_details[$i]['deleted'] = $adb->query_result($res,$i,'deleted');
		}
	}
	else
	{
		$log->debug("Product id is empty. we cannot retrieve the associated products.");
	}

	$log->debug("Exit from function getTaxDetailsForProduct($productid)");
	return $tax_details;
}

/**	Function used to delete the Inventory product details for the passed entity
 *	@param int $objectid - entity id to which we want to delete the product details from REQUEST values where as the entity will be Purchase Order, Sales Order, Quotes or Invoice
 *	@param string $return_old_values - string which contains the string return_old_values or may be empty, if the string is return_old_values then before delete old values will be retrieved
 *	@return array $ext_prod_arr - if the second input parameter is 'return_old_values' then the array which contains the productid and quantity which will be retrieved before delete the product details will be returned otherwise return empty
 */
function deleteInventoryProductDetails($focus)
{
	global $log, $adb,$updateInventoryProductRel_update_product_array;
	$log->debug("Entering into function deleteInventoryProductDetails(".$focus->id.").");
	
	$product_info = $adb->pquery("SELECT productid, quantity, sequence_no, incrementondel from vtiger_inventoryproductrel WHERE id=?",array($focus->id));
	$numrows = $adb->num_rows($product_info);
	for($index = 0;$index <$numrows;$index++){
		$productid = $adb->query_result($product_info,$index,'productid');
		$sequence_no = $adb->query_result($product_info,$index,'sequence_no');
		$qty = $adb->query_result($product_info,$index,'quantity');
		$incrementondel = $adb->query_result($product_info,$index,'incrementondel');
		
		if($incrementondel){
			$focus->update_product_array[$focus->id][$sequence_no][$productid]= $qty;
			$sub_prod_query = $adb->pquery("SELECT productid from vtiger_inventorysubproductrel WHERE id=? AND sequence_no=?",array($focus->id,$sequence_no)); 
			if($adb->num_rows($sub_prod_query)>0){
				for($j=0;$j<$adb->num_rows($sub_prod_query);$j++){
					$sub_prod_id = $adb->query_result($sub_prod_query,$j,"productid");
					$focus->update_product_array[$focus->id][$sequence_no][$sub_prod_id]= $qty;
				}
			}
			
		}
	}
	$updateInventoryProductRel_update_product_array = $focus->update_product_array;
    $adb->pquery("delete from vtiger_inventoryproductrel where id=?", array($focus->id));
    $adb->pquery("delete from vtiger_inventorysubproductrel where id=?", array($focus->id));
    $adb->pquery("delete from vtiger_inventoryshippingrel where id=?", array($focus->id));

	$log->debug("Exit from function deleteInventoryProductDetails(".$focus->id.")");
}

function updateInventoryProductRel($entity)
{
	global $log, $adb,$updateInventoryProductRel_update_product_array;
	$entity_id = vtws_getIdComponents($entity->getId());
	$entity_id = $entity_id[1];
	$update_product_array = $updateInventoryProductRel_update_product_array;
	$log->debug("Entering into function updateInventoryProductRel(".$entity_id.").");

	if(!empty($update_product_array)){
		foreach($update_product_array as $id=>$seq){
			foreach($seq as $seq=>$product_info)
			{
				foreach($product_info as $key=>$index){
					$updqtyinstk= getPrdQtyInStck($key);
					$upd_qty = $updqtyinstk+$index;
					updateProductQty($key, $upd_qty);
				}
			}
		}
	}
	$adb->pquery("UPDATE vtiger_inventoryproductrel SET incrementondel=1 WHERE id=?",array($entity_id));
	
	$product_info = $adb->pquery("SELECT productid,sequence_no, quantity from vtiger_inventoryproductrel WHERE id=?",array($entity_id));
	$numrows = $adb->num_rows($product_info);
	for($index = 0;$index <$numrows;$index++){
		$productid = $adb->query_result($product_info,$index,'productid');
		$qty = $adb->query_result($product_info,$index,'quantity');
		$sequence_no = $adb->query_result($product_info,$index,'sequence_no');
		$qtyinstk= getPrdQtyInStck($productid);
		$upd_qty = $qtyinstk-$qty;
		updateProductQty($productid, $upd_qty);
		$sub_prod_query = $adb->pquery("SELECT productid from vtiger_inventorysubproductrel WHERE id=? AND sequence_no=?",array($entity_id,$sequence_no)); 
		if($adb->num_rows($sub_prod_query)>0){
			for($j=0;$j<$adb->num_rows($sub_prod_query);$j++){
				$sub_prod_id = $adb->query_result($sub_prod_query,$j,"productid");
				$sqtyinstk= getPrdQtyInStck($sub_prod_id);
				$supd_qty = $sqtyinstk-$qty;
				updateProductQty($sub_prod_id, $supd_qty);
			}
		}
	}

	$log->debug("Exit from function updateInventoryProductRel(".$entity_id.")");
}

/**	Function used to save the Inventory product details for the passed entity
 *	@param object reference $focus - object reference to which we want to save the product details from REQUEST values where as the entity will be Purchase Order, Sales Order, Quotes or Invoice
 *	@param string $module - module name
 *	@param $update_prod_stock - true or false (default), if true we have to update the stock for PO only
 *	@return void
 */
function saveInventoryProductDetails(&$focus, $module, $update_prod_stock='false', $updateDemand='')
{
	global $log, $adb;
	$id=$focus->id;
	$log->debug("Entering into function saveInventoryProductDetails($module).");
	//Added to get the convertid
	if(isset($_REQUEST['convert_from']) && $_REQUEST['convert_from'] !='')
	{
		$id=$_REQUEST['return_id'];
	}
	else if(isset($_REQUEST['duplicate_from']) && $_REQUEST['duplicate_from'] !='')
	{
		$id=$_REQUEST['duplicate_from'];
	}

	$ext_prod_arr = Array();
	if($focus->mode == 'edit')
	{
		if($_REQUEST['taxtype'] == 'group')
			$all_available_taxes = getAllTaxes('available','','edit',$id);
		$return_old_values = '';
		if($module != 'PurchaseOrder')
		{
			$return_old_values = 'return_old_values';
		}

		//we will retrieve the existing product details and store it in a array and then delete all the existing product details and save new values, retrieve the old value and update stock only for SO, Quotes and Invoice not for PO
		//$ext_prod_arr = deleteInventoryProductDetails($focus->id,$return_old_values);
		deleteInventoryProductDetails($focus);
	}
	else
	{
	if($_REQUEST['taxtype'] == 'group')
		$all_available_taxes = getAllTaxes('available','','edit',$id);
	}
	$tot_no_prod = $_REQUEST['totalProductCount'];
	//If the taxtype is group then retrieve all available taxes, else retrive associated taxes for each product inside loop
	$prod_seq=1;
	for($i=1; $i<=$tot_no_prod; $i++)
	{
		//if the product is deleted then we should avoid saving the deleted products
		if($_REQUEST["deleted".$i] == 1)
			continue;

	    $prod_id = $_REQUEST['hdnProductId'.$i];
		if(isset($_REQUEST['productDescription'.$i]))
			$description = $_REQUEST['productDescription'.$i];
		/*else{
			$desc_duery = "select vtiger_crmentity.description AS product_description from vtiger_crmentity where vtiger_crmentity.crmid=?";
			$desc_res = $adb->pquery($desc_duery,array($prod_id));
			$description = $adb->query_result($desc_res,0,"product_description");
		}	*/
        $qty = $_REQUEST['qty'.$i];
        $listprice = $_REQUEST['listPrice'.$i];
		$comment = $_REQUEST['comment'.$i];

		//we have to update the Product stock for PurchaseOrder if $update_prod_stock is true
		if($module == 'PurchaseOrder' && $update_prod_stock == 'true')
		{
			addToProductStock($prod_id,$qty);
		}
		if($module == 'SalesOrder')
		{
			if($updateDemand == '-')
			{
				deductFromProductDemand($prod_id,$qty);
			}
			elseif($updateDemand == '+')
			{
				addToProductDemand($prod_id,$qty);
			}
		}

		$query ="insert into vtiger_inventoryproductrel(id, productid, sequence_no, quantity, listprice, comment, description) values(?,?,?,?,?,?,?)";
		$qparams = array($focus->id,$prod_id,$prod_seq,$qty,$listprice,$comment,$description);
		$adb->pquery($query,$qparams);
		
		$lineitem_id = $adb->getLastInsertID();

		$sub_prod_str = $_REQUEST['subproduct_ids'.$i];
		if (!empty($sub_prod_str)) {
			$sub_prod = split(":",$sub_prod_str);
			for($j=0;$j<count($sub_prod);$j++){
				$query ="insert into vtiger_inventorysubproductrel(id, sequence_no, productid) values(?,?,?)";
				$qparams = array($focus->id,$prod_seq,$sub_prod[$j]);
				$adb->pquery($query,$qparams);
			}
		}
		$prod_seq++;

		if($module != 'PurchaseOrder')
		{
			//update the stock with existing details
			updateStk($prod_id,$qty,$focus->mode,$ext_prod_arr,$module);
		}

		//we should update discount and tax details
		$updatequery = "update vtiger_inventoryproductrel set ";
		$updateparams = array();

		//set the discount percentage or discount amount in update query, then set the tax values
		if($_REQUEST['discount_type'.$i] == 'percentage')
		{
			$updatequery .= " discount_percent=?,";
			array_push($updateparams, $_REQUEST['discount_percentage'.$i]);
		}
		elseif($_REQUEST['discount_type'.$i] == 'amount')
		{
			$updatequery .= " discount_amount=?,";
			$discount_amount = $_REQUEST['discount_amount'.$i];
			array_push($updateparams, $discount_amount);
		}
		if($_REQUEST['taxtype'] == 'group')
		{
			for($tax_count=0;$tax_count<count($all_available_taxes);$tax_count++)
			{
				$tax_name = $all_available_taxes[$tax_count]['taxname'];
				$tax_val = $all_available_taxes[$tax_count]['percentage'];
				$request_tax_name = $tax_name."_group_percentage";
				if(isset($_REQUEST[$request_tax_name]))
					$tax_val =$_REQUEST[$request_tax_name];
				$updatequery .= " $tax_name = ?,";
				array_push($updateparams,$tax_val);
			}
				$updatequery = trim($updatequery,',')." where id=? and productid=? and lineitem_id = ?";
				array_push($updateparams,$focus->id,$prod_id, $lineitem_id);
		}
		else
		{
			$taxes_for_product = getTaxDetailsForProduct($prod_id,'all');
			for($tax_count=0;$tax_count<count($taxes_for_product);$tax_count++)
			{
				$tax_name = $taxes_for_product[$tax_count]['taxname'];
				$request_tax_name = $tax_name."_percentage".$i;
			
				$updatequery .= " $tax_name = ?,";
				array_push($updateparams, $_REQUEST[$request_tax_name]);
			}
				$updatequery = trim($updatequery,',')." where id=? and productid=? and lineitem_id = ?";
				array_push($updateparams, $focus->id,$prod_id, $lineitem_id);
		}
		// jens 2006/08/19 - protect against empy update queries
 		if( !preg_match( '/set\s+where/i', $updatequery)) {
 		    $adb->pquery($updatequery,$updateparams);
 		}
	}

	//we should update the netprice (subtotal), taxtype, group discount, S&H charge, S&H taxes, adjustment and total
	//netprice, group discount, taxtype, S&H amount, adjustment and total to entity table

	$updatequery  = " update $focus->table_name set ";
	$updateparams = array();
	$subtotal = $_REQUEST['subtotal'];
	$updatequery .= " subtotal=?,";
	array_push($updateparams, $subtotal);

	$updatequery .= " taxtype=?,";
	array_push($updateparams, $_REQUEST['taxtype']);

	//for discount percentage or discount amount
	if($_REQUEST['discount_type_final'] == 'percentage')
	{
		$updatequery .= " discount_percent=?,";
		array_push($updateparams, $_REQUEST['discount_percentage_final']);
	}
	elseif($_REQUEST['discount_type_final'] == 'amount')
	{
		$discount_amount_final = $_REQUEST['discount_amount_final'];
		$updatequery .= " discount_amount=?,";
		array_push($updateparams, $discount_amount_final);
	}
	
	$shipping_handling_charge = $_REQUEST['shipping_handling_charge'];
	$updatequery .= " s_h_amount=?,";
	array_push($updateparams, $shipping_handling_charge);

	//if the user gave - sign in adjustment then add with the value
	$adjustmentType = '';
	if($_REQUEST['adjustmentType'] == '-')
		$adjustmentType = $_REQUEST['adjustmentType'];

	$adjustment = $_REQUEST['adjustment'];
	$updatequery .= " adjustment=?,";
	array_push($updateparams, $adjustmentType.$adjustment);

	$total = $_REQUEST['total'];
	$updatequery .= " total=?";
	array_push($updateparams, $total);

	//$id_array = Array('PurchaseOrder'=>'purchaseorderid','SalesOrder'=>'salesorderid','Quotes'=>'quoteid','Invoice'=>'invoiceid');
	//Added where condition to which entity we want to update these values
	$updatequery .= " where ".$focus->table_index."=?";
	array_push($updateparams, $focus->id);

	$adb->pquery($updatequery,$updateparams);

	//to save the S&H tax details in vtiger_inventoryshippingrel table
	$sh_tax_details = getAllTaxes('all','sh');
	$sh_query_fields = "id,";
	$sh_query_values = "?,";
	$sh_query_params = array($focus->id);
	for($i=0;$i<count($sh_tax_details);$i++)
	{
		$tax_name = $sh_tax_details[$i]['taxname']."_sh_percent";
		if($_REQUEST[$tax_name] != '')
		{
			$sh_query_fields .= $sh_tax_details[$i]['taxname'].",";
			$sh_query_values .= "?,";
			array_push($sh_query_params, $_REQUEST[$tax_name]);
		}
	}
	$sh_query_fields = trim($sh_query_fields,',');
	$sh_query_values = trim($sh_query_values,',');

	$sh_query = "insert into vtiger_inventoryshippingrel($sh_query_fields) values($sh_query_values)";
	$adb->pquery($sh_query,$sh_query_params);

	$log->debug("Exit from function saveInventoryProductDetails($module).");
}


/**	function used to get the tax type for the entity (PO, SO, Quotes or Invoice)
 *	@param string $module - module name
 *	@param int $id - id of the PO or SO or Quotes or Invoice
 *	@return string $taxtype - taxtype for the given entity which will be individual or group
 */
function getInventoryTaxType($module, $id)
{
	global $log, $adb;

	$log->debug("Entering into function getInventoryTaxType($module, $id).");

	$inv_table_array = Array('PurchaseOrder'=>'vtiger_purchaseorder','SalesOrder'=>'vtiger_salesorder','Quotes'=>'vtiger_quotes','Invoice'=>'vtiger_invoice');
	$inv_id_array = Array('PurchaseOrder'=>'purchaseorderid','SalesOrder'=>'salesorderid','Quotes'=>'quoteid','Invoice'=>'invoiceid');
	
	$res = $adb->pquery("select taxtype from $inv_table_array[$module] where $inv_id_array[$module]=?", array($id));

	$taxtype = $adb->query_result($res,0,'taxtype');

	$log->debug("Exit from function getInventoryTaxType($module, $id).");

	return $taxtype;
}

/**	function used to get the price type for the entity (PO, SO, Quotes or Invoice)
 *	@param string $module - module name
 *	@param int $id - id of the PO or SO or Quotes or Invoice
 *	@return string $pricetype - pricetype for the given entity which will be unitprice or secondprice
 */
function getInventoryCurrencyInfo($module, $id)
{
	global $log, $adb;

	$log->debug("Entering into function getInventoryCurrencyInfo($module, $id).");

	$inv_table_array = Array('PurchaseOrder'=>'vtiger_purchaseorder','SalesOrder'=>'vtiger_salesorder','Quotes'=>'vtiger_quotes','Invoice'=>'vtiger_invoice');
	$inv_id_array = Array('PurchaseOrder'=>'purchaseorderid','SalesOrder'=>'salesorderid','Quotes'=>'quoteid','Invoice'=>'invoiceid');
	
	$inventory_table = $inv_table_array[$module];
	$inventory_id = $inv_id_array[$module];
	$res = $adb->pquery("select currency_id, $inventory_table.conversion_rate as conv_rate, vtiger_currency_info.* from $inventory_table
						inner join vtiger_currency_info on $inventory_table.currency_id = vtiger_currency_info.id
						where $inventory_id=?", array($id));

	$currency_info = array();
	$currency_info['currency_id'] = $adb->query_result($res,0,'currency_id');
	$currency_info['conversion_rate'] = $adb->query_result($res,0,'conv_rate');
	$currency_info['currency_name'] = $adb->query_result($res,0,'currency_name');
	$currency_info['currency_code'] = $adb->query_result($res,0,'currency_code');
	$currency_info['currency_symbol'] = $adb->query_result($res,0,'currency_symbol');

	$log->debug("Exit from function getInventoryCurrencyInfo($module, $id).");

	return $currency_info;
}

/**	function used to get the taxvalue which is associated with a product for PO/SO/Quotes or Invoice
 *	@param int $id - id of PO/SO/Quotes or Invoice
 *	@param int $productid - product id
 *	@param string $taxname - taxname to which we want the value
 *	@return float $taxvalue - tax value
 */
function getInventoryProductTaxValue($id, $productid, $taxname)
{
	global $log, $adb;
	$log->debug("Entering into function getInventoryProductTaxValue($id, $productid, $taxname).");
	
	$res = $adb->pquery("select $taxname from vtiger_inventoryproductrel where id = ? and productid = ?", array($id, $productid));
	$taxvalue = $adb->query_result($res,0,$taxname);

	if($taxvalue == '')
		$taxvalue = '0.00';

	$log->debug("Exit from function getInventoryProductTaxValue($id, $productid, $taxname).");

	return $taxvalue;
}

/**	function used to get the shipping & handling tax percentage for the given inventory id and taxname
 *	@param int $id - entity id which will be PO/SO/Quotes or Invoice id
 *	@param string $taxname - shipping and handling taxname
 *	@return float $taxpercentage - shipping and handling taxpercentage which is associated with the given entity
 */
function getInventorySHTaxPercent($id, $taxname)
{
	global $log, $adb;
	$log->debug("Entering into function getInventorySHTaxPercent($id, $taxname)");
	
	$res = $adb->pquery("select $taxname from vtiger_inventoryshippingrel where id= ?", array($id));
	$taxpercentage = $adb->query_result($res,0,$taxname);

	if($taxpercentage == '')
		$taxpercentage = '0.00';

	$log->debug("Exit from function getInventorySHTaxPercent($id, $taxname)");

	return $taxpercentage;
}

/**	Function used to get the list of all Currencies as a array
 *  @param string available - if 'all' returns all the currencies, default value 'available' returns only the currencies which are available for use.
 *	return array $currency_details - return details of all the currencies as a array
 */
function getAllCurrencies($available='available') {
	global $adb, $log;
	$log->debug("Entering into function getAllCurrencies($available)");
	
	$sql = "select * from vtiger_currency_info";
	if ($available != 'all') {
		$sql .= " where currency_status='Active' and deleted=0";
	}
	$res=$adb->pquery($sql, array());
	$noofrows = $adb->num_rows($res);
	
	for($i=0;$i<$noofrows;$i++)
	{
		$currency_details[$i]['currencylabel'] = $adb->query_result($res,$i,'currency_name');
		$currency_details[$i]['currencycode'] = $adb->query_result($res,$i,'currency_code');
		$currency_details[$i]['currencysymbol'] = $adb->query_result($res,$i,'currency_symbol');
		$currency_details[$i]['curid'] = $adb->query_result($res,$i,'id');
		$currency_details[$i]['conversionrate'] = $adb->query_result($res,$i,'conversion_rate');
		$currency_details[$i]['curname'] = 'curname' . $adb->query_result($res,$i,'id');			
	}
	
	$log->debug("Entering into function getAllCurrencies($available)");
	return $currency_details;
	
}

/**	Function used to get all the price details for different currencies which are associated to the given product
 *	@param int $productid - product id to which we want to get all the associated prices
 *  @param decimal $unit_price - Unit price of the product
 *  @param string $available - available or available_associated where as default is available, if available then the prices in the currencies which are available now will be returned, otherwise if the value is available_associated then prices of all the associated currencies will be retruned
 *	@return array $price_details - price details as a array with productid, curid, curname
 */
function getPriceDetailsForProduct($productid, $unit_price, $available='available', $itemtype='Products')
{
	global $log, $adb;
	$log->debug("Entering into function getPriceDetailsForProduct($productid)");
	if($productid != '')
	{
		$product_currency_id = getProductBaseCurrency($productid, $itemtype);
		$product_base_conv_rate = getBaseConversionRateForProduct($productid,'edit',$itemtype);
		// Detail View
		if ($available == 'available_associated') {
			$query = "select vtiger_currency_info.*, vtiger_productcurrencyrel.converted_price, vtiger_productcurrencyrel.actual_price 
					from vtiger_currency_info 
					inner join vtiger_productcurrencyrel on vtiger_currency_info.id = vtiger_productcurrencyrel.currencyid
					where vtiger_currency_info.currency_status = 'Active' and vtiger_currency_info.deleted=0 
					and vtiger_productcurrencyrel.productid = ? and vtiger_currency_info.id != ?";
			$params = array($productid, $product_currency_id);
		} else { // Edit View
			$query = "select vtiger_currency_info.*, vtiger_productcurrencyrel.converted_price, vtiger_productcurrencyrel.actual_price 
					from vtiger_currency_info 
					left join vtiger_productcurrencyrel 
					on vtiger_currency_info.id = vtiger_productcurrencyrel.currencyid and vtiger_productcurrencyrel.productid = ?
					where vtiger_currency_info.currency_status = 'Active' and vtiger_currency_info.deleted=0";
			$params = array($productid);			
		}

		//Postgres 8 fixes
 		if( $adb->dbType == "pgsql")
 		    $query = fixPostgresQuery( $query, $log, 0);

		$res = $adb->pquery($query, $params);
		for($i=0;$i<$adb->num_rows($res);$i++)
		{
			$price_details[$i]['productid'] = $productid;
			$price_details[$i]['currencylabel'] = $adb->query_result($res,$i,'currency_name');
			$price_details[$i]['currencycode'] = $adb->query_result($res,$i,'currency_code');
			$price_details[$i]['currencysymbol'] = $adb->query_result($res,$i,'currency_symbol');
			$currency_id = $adb->query_result($res,$i,'id');
			$price_details[$i]['curid'] = $currency_id;
			$price_details[$i]['curname'] = 'curname' . $adb->query_result($res,$i,'id');
			$cur_value = $adb->query_result($res,$i,'actual_price');
			
			// Get the conversion rate for the given currency, get the conversion rate of the product currency to base currency. 
			// Both together will be the actual conversion rate for the given currency.
			$conversion_rate = $adb->query_result($res,$i,'conversion_rate');
			$actual_conversion_rate = $product_base_conv_rate * $conversion_rate;
			
			if ($cur_value == null || $cur_value == '') {
				$price_details[$i]['check_value'] = false;
				if	($unit_price != null) {
					$cur_value = convertFromMasterCurrency($unit_price, $actual_conversion_rate);
				} else {
					$cur_value = '0';
				}
			} else {
				$price_details[$i]['check_value'] = true;
			}
			$price_details[$i]['curvalue'] = $cur_value;
			$price_details[$i]['conversionrate'] = $actual_conversion_rate;		
			
			$is_basecurrency = false;
			if ($currency_id == $product_currency_id) {
				$is_basecurrency = true;
			}
			$price_details[$i]['is_basecurrency'] = $is_basecurrency;		
		}
	}
	else
	{
		if($available == 'available') { // Create View
			global $current_user;
			
			$user_currency_id = fetchCurrency($current_user->id);
			
			$query = "select vtiger_currency_info.* from vtiger_currency_info 
					where vtiger_currency_info.currency_status = 'Active' and vtiger_currency_info.deleted=0";
			$params = array();
			
			$res = $adb->pquery($query, $params);
			for($i=0;$i<$adb->num_rows($res);$i++)
			{
				$price_details[$i]['currencylabel'] = $adb->query_result($res,$i,'currency_name');
				$price_details[$i]['currencycode'] = $adb->query_result($res,$i,'currency_code');
				$price_details[$i]['currencysymbol'] = $adb->query_result($res,$i,'currency_symbol');
				$currency_id = $adb->query_result($res,$i,'id');
				$price_details[$i]['curid'] = $currency_id;
				$price_details[$i]['curname'] = 'curname' . $adb->query_result($res,$i,'id');
				
				// Get the conversion rate for the given currency, get the conversion rate of the product currency(logged in user's currency) to base currency. 
				// Both together will be the actual conversion rate for the given currency.
				$conversion_rate = $adb->query_result($res,$i,'conversion_rate');
				$user_cursym_convrate = getCurrencySymbolandCRate($user_currency_id);
				$product_base_conv_rate = 1 / $user_cursym_convrate['rate'];
				$actual_conversion_rate = $product_base_conv_rate * $conversion_rate;
				
				$price_details[$i]['check_value'] = false;
				$price_details[$i]['curvalue'] = '0';
				$price_details[$i]['conversionrate'] = $actual_conversion_rate;		
			
				$is_basecurrency = false;
				if ($currency_id == $user_currency_id) {
					$is_basecurrency = true;
				}
				$price_details[$i]['is_basecurrency'] = $is_basecurrency;				
			}
		} else {
			$log->debug("Product id is empty. we cannot retrieve the associated prices.");
		}
	}

	$log->debug("Exit from function getPriceDetailsForProduct($productid)");
	return $price_details;
}

/**	Function used to get the base currency used for the given Product
 *	@param int $productid - product id for which we want to get the id of the base currency
 *  @return int $currencyid - id of the base currency for the given product
 */
function getProductBaseCurrency($productid,$module='Products') {
	global $adb, $log;
	if ($module == 'Services') {
		$sql = "select currency_id from vtiger_service where serviceid=?";		
	} else {
		$sql = "select currency_id from vtiger_products where productid=?";
	}
	$params = array($productid);	
	$res = $adb->pquery($sql, $params);
	$currencyid = $adb->query_result($res, 0, 'currency_id');	
	return $currencyid;	
}

/**	Function used to get the conversion rate for the product base currency with respect to the CRM base currency
 *	@param int $productid - product id for which we want to get the conversion rate of the base currency
 *  @param string $mode - Mode in which the function is called
 *  @return number $conversion_rate - conversion rate of the base currency for the given product based on the CRM base currency
 */
function getBaseConversionRateForProduct($productid, $mode='edit', $module='Products') {
	global $adb, $log, $current_user;
	
	if ($mode == 'edit') {
		if ($module == 'Services') {			
			$sql = "select conversion_rate from vtiger_service inner join vtiger_currency_info 
					on vtiger_service.currency_id = vtiger_currency_info.id where vtiger_service.serviceid=?";
		} else {
			$sql = "select conversion_rate from vtiger_products inner join vtiger_currency_info 
					on vtiger_products.currency_id = vtiger_currency_info.id where vtiger_products.productid=?";
		}
		$params = array($productid);
	} else {
		$sql = "select conversion_rate from vtiger_currency_info where id=?";
		$params = array(fetchCurrency($current_user->id));		
	}
	
	$res = $adb->pquery($sql, $params);
	$conv_rate = $adb->query_result($res, 0, 'conversion_rate');
	
	return 1 / $conv_rate;
}

/**	Function used to get the prices for the given list of products based in the specified currency
 *	@param int $currencyid - currency id based on which the prices have to be provided
 *	@param array $product_ids - List of product id's for which we want to get the price based on given currency
 *  @return array $prices_list - List of prices for the given list of products based on the given currency in the form of 'product id' mapped to 'price value'
 */
function getPricesForProducts($currencyid, $product_ids, $module='Products') {
	global $adb,$log,$current_user;
	
	$price_list = array();
	if (count($product_ids) > 0) {
		if ($module == 'Services') {
			$query = "SELECT vtiger_currency_info.id, vtiger_currency_info.conversion_rate, " .
					"vtiger_service.serviceid AS productid, vtiger_service.unit_price, " .
					"vtiger_productcurrencyrel.actual_price " .
					"FROM (vtiger_currency_info, vtiger_service) " .
					"left join vtiger_productcurrencyrel on vtiger_service.serviceid = vtiger_productcurrencyrel.productid " .
					"and vtiger_currency_info.id = vtiger_productcurrencyrel.currencyid " .
					"where vtiger_service.serviceid in (". generateQuestionMarks($product_ids) .") and vtiger_currency_info.id = ?";
		} else {
			$query = "SELECT vtiger_currency_info.id, vtiger_currency_info.conversion_rate, " .
					"vtiger_products.productid, vtiger_products.unit_price, " .
					"vtiger_productcurrencyrel.actual_price " .
					"FROM (vtiger_currency_info, vtiger_products) " .
					"left join vtiger_productcurrencyrel on vtiger_products.productid = vtiger_productcurrencyrel.productid " .
					"and vtiger_currency_info.id = vtiger_productcurrencyrel.currencyid " .
					"where vtiger_products.productid in (". generateQuestionMarks($product_ids) .") and vtiger_currency_info.id = ?";			
		}
		$params = array($product_ids, $currencyid);
		$result = $adb->pquery($query, $params);
		
		for($i=0;$i<$adb->num_rows($result);$i++)
		{
			$product_id = $adb->query_result($result, $i, 'productid');
			if(getFieldVisibilityPermission($module,$current_user->id,'unit_price') == '0') {
				$actual_price = $adb->query_result($result, $i, 'actual_price');
				
				if ($actual_price == null || $actual_price == '') {
					$unit_price = $adb->query_result($result, $i, 'unit_price');
					$product_conv_rate = $adb->query_result($result, $i, 'conversion_rate');
					$product_base_conv_rate = getBaseConversionRateForProduct($product_id,'edit',$module);
					$conversion_rate = $product_conv_rate * $product_base_conv_rate;
					
					$actual_price = $unit_price * $conversion_rate;
				}
				$price_list[$product_id] = $actual_price;
			} else {
				$price_list[$product_id] = '';
			}
		}
	}
	return $price_list;
}

/**	Function used to get the currency used for the given Price book
 *	@param int $pricebook_id - pricebook id for which we want to get the id of the currency used
 *  @return int $currencyid - id of the currency used for the given pricebook
 */
function getPriceBookCurrency($pricebook_id) {
	global $adb;
	$result = $adb->pquery("select currency_id from vtiger_pricebook where pricebookid=?", array($pricebook_id));
	$currency_id = $adb->query_result($result,0,'currency_id');
	return $currency_id;
}
?>