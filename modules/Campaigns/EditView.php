<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once 'modules/Vtiger/EditView.php';

if($focus->mode == 'edit') {
	$smarty->assign("OLDSMOWNERID", $focus->column_fields['assigned_user_id']);
}

if(isset($_REQUEST['product_id'])) {
	$smarty->assign("PRODUCTID", vtlib_purify($_REQUEST['product_id']));
}

	$smarty->display("salesEditView.tpl");

?>