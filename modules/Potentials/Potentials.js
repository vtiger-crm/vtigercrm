/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/


document.write("<script type='text/javascript' src='include/js/Merge.js'></"+"script>");

function set_return(product_id, product_name) {
	if(document.getElementById('from_link').value != '') {
        window.opener.document.QcEditView.parent_name.value = product_name;
        window.opener.document.QcEditView.parent_id.value = product_id;
	} else {
        window.opener.document.EditView.parent_name.value = product_name;
        window.opener.document.EditView.parent_id.value = product_id;
	}
}
function set_return_specific(product_id, product_name) {
        
        var fldName = getOpenerObj("potential_name");
        var fldId = getOpenerObj("potential_id");
        fldName.value = product_name;
        fldId.value = product_id;
}
function add_data_to_relatedlist(entity_id,recordid) 
{
	opener.document.location.href="index.php?module=Emails&action=updateRelations&destination_module=Contacts&entityid="+entity_id+"&parentid="+recordid;
}
function set_return_address(potential_id, potential_name, account_id, account_name, bill_street, ship_street, bill_city, ship_city, bill_state, ship_state, bill_code, ship_code, bill_country, ship_country,bill_pobox,ship_pobox) {

	if(typeof(window.opener.document.EditView.potential_name) != 'undefined')
                window.opener.document.EditView.potential_name.value = potential_name;
        if(typeof(window.opener.document.EditView.potential_id) != 'undefined')
                window.opener.document.EditView.potential_id.value = potential_id;
	if(typeof(window.opener.document.EditView.account_name) != 'undefined')
                window.opener.document.EditView.account_name.value = account_name;
        if(typeof(window.opener.document.EditView.account_id) != 'undefined')
                window.opener.document.EditView.account_id.value = account_id;
        if(typeof(window.opener.document.EditView.bill_street) != 'undefined')
                window.opener.document.EditView.bill_street.value = bill_street;
        if(typeof(window.opener.document.EditView.ship_street) != 'undefined')
                window.opener.document.EditView.ship_street.value = ship_street;
        if(typeof(window.opener.document.EditView.bill_city) != 'undefined')
                window.opener.document.EditView.bill_city.value = bill_city;
        if(typeof(window.opener.document.EditView.ship_city) != 'undefined')
                window.opener.document.EditView.ship_city.value = ship_city;
        if(typeof(window.opener.document.EditView.bill_state) != 'undefined')
                window.opener.document.EditView.bill_state.value = bill_state;
        if(typeof(window.opener.document.EditView.ship_state) != 'undefined')
                window.opener.document.EditView.ship_state.value = ship_state;
        if(typeof(window.opener.document.EditView.bill_code) != 'undefined')
                window.opener.document.EditView.bill_code.value = bill_code;
        if(typeof(window.opener.document.EditView.ship_code) != 'undefined')
                window.opener.document.EditView.ship_code.value = ship_code;
        if(typeof(window.opener.document.EditView.bill_country) != 'undefined')
                window.opener.document.EditView.bill_country.value = bill_country;
        if(typeof(window.opener.document.EditView.ship_country) != 'undefined')
                window.opener.document.EditView.ship_country.value = ship_country;
        if(typeof(window.opener.document.EditView.bill_pobox) != 'undefined')
                window.opener.document.EditView.bill_pobox.value = bill_pobox;
        if(typeof(window.opener.document.EditView.ship_pobox) != 'undefined')
                window.opener.document.EditView.ship_pobox.value = ship_pobox;
}
function set_return_contact(potential_id, potential_name, contact_id, contact_name) {

	if(typeof(window.opener.document.EditView.potential_name) != 'undefined')
                window.opener.document.EditView.potential_name.value = potential_name;
        if(typeof(window.opener.document.EditView.potential_id) != 'undefined')
                window.opener.document.EditView.potential_id.value = potential_id;
	if(typeof(window.opener.document.EditView.contact_name) != 'undefined')
                window.opener.document.EditView.contact_name.value = contact_name;
        if(typeof(window.opener.document.EditView.contact_id) != 'undefined')
                window.opener.document.EditView.contact_id.value = contact_id;
}
function set_return_todo(product_id, product_name) {
	if(document.getElementById('from_link').value != '') {
        window.opener.document.QcEditView.task_parent_name.value = product_name;
        window.opener.document.QcEditView.task_parent_id.value = product_id;
	} else {
        window.opener.document.createTodo.task_parent_name.value = product_name;
        window.opener.document.createTodo.task_parent_id.value = product_id;
	}
}

