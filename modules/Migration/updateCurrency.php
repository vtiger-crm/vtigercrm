<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once("include/database/PearDatabase.php");
$conn = PearDatabase::getInstance();

$ajax_val = $_REQUEST['ajax'];

if($ajax_val == 1)
{
	$crate = $_REQUEST['crate'];
	$conn->println('conversion rate = '.$crate);
	
	$query = "UPDATE vtiger_currency_info SET conversion_rate=? WHERE id=1";
	$result = $conn->pquery($query, array($crate));

	//array should be id || vtiger_fieldname => vtiger_tablename
	$modules_array = Array(
				"accountid||annualrevenue"	=>	"vtiger_account",
				
				"leadid||annualrevenue"		=>	"vtiger_leaddetails",

				"potentialid||amount"		=>	"vtiger_potential",

				"productid||unit_price"		=>	"vtiger_products",

				"salesorderid||salestax"	=>	"vtiger_salesorder",
				"salesorderid||adjustment"	=>	"vtiger_salesorder",
				"salesorderid||total"		=>	"vtiger_salesorder",
				"salesorderid||subtotal"	=>	"vtiger_salesorder",

				"purchaseorderid||salestax"	=>	"vtiger_purchaseorder",
				"purchaseorderid||adjustment"	=>	"vtiger_purchaseorder",
				"purchaseorderid||total"	=>	"vtiger_purchaseorder",
				"purchaseorderid||subtotal"	=>	"vtiger_purchaseorder",

				"quoteid||tax"			=>	"vtiger_quotes",
				"quoteid||adjustment"		=>	"vtiger_quotes",
				"quoteid||total"		=>	"vtiger_quotes",
				"quoteid||subtotal"		=>	"vtiger_quotes",

				"invoiceid||salestax"		=>	"vtiger_invoice",
				"invoiceid||adjustment"		=>	"vtiger_invoice",
				"invoiceid||total"		=>	"vtiger_invoice",
				"invoiceid||subtotal"		=>	"vtiger_invoice",
			      );

	foreach($modules_array as $fielddetails => $table)
	{
		$temp = explode("||",$fielddetails);
		$id_name = $temp[0];
		$fieldname = $temp[1];

		$res = $conn->query("select $id_name, $fieldname from $table");
		$record_count = $conn->num_rows($res);
		
		for($i=0;$i<$record_count;$i++)
		{
			$recordid = $conn->query_result($res,$i,$id_name);
			$old_value = $conn->query_result($res,$i,$fieldname);

			//calculate the new value
			$new_value = $old_value/$crate;//convertToDollar($old_value,$crate);
			$conn->println("old value = $old_value && new value = $new_value");

			$update_query = "update $table set $fieldname='".$new_value."' where $id_name=$recordid";
			$update_result = $conn->query($update_query);
		}
	}
}

?>