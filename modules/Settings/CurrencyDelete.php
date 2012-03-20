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

$del_id = $_REQUEST['delete_currency_id'];
$tran_id = $_REQUEST['transfer_currency_id'];

// Transfer all the data refering to currency $del_id to currency $tran_id
transferCurrency($del_id, $tran_id);

// Mark Currency as deleted
$sql = "update vtiger_currency_info set deleted=1 where id =?";
$adb->pquery($sql, array($del_id));

header("Location: index.php?action=SettingsAjax&module=Settings&file=CurrencyListView&ajax=true");

?>

