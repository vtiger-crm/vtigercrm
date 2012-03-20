/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/


document.write("<script type='text/javascript' src='include/js/Mail.js'></"+"script>");
document.write("<script type='text/javascript' src='include/js/Merge.js'></"+"script>");
function verify_data(form) {
	if(! form.createpotential.checked == true){
        if (trim(form.potential_name.value) == ""){
            alert(alert_arr.OPPORTUNITYNAME_CANNOT_BE_EMPTY);
			return false;	
		}
		
		if(form.closingdate_mandatory != null && form.closingdate_mandatory.value == '*'){
			if (form.closedate.value == ""){
	        	alert(alert_arr.CLOSEDATE_CANNOT_BE_EMPTY);
				return false;	
			}
		}
		if (form.closedate.value != "" ){
			var x = dateValidate('closedate','Potential Close Date','DATE');
			if(!x){
				return false;
			}
		}
			
				
		
		if(form.amount_mandatory.value == '*'){
			if (form.potential_amount.value == ""){
	            alert(alert_arr.AMOUNT_CANNOT_BE_EMPTY);
				return false;					
			}
		}	
		intval= intValidate('potential_amount','Potential Amount');
		if(!intval){
			return false;
		}
	}
	else{	
		return true;
	}
	
}

function togglePotFields(form)
{
	if (form.createpotential.checked == true)
	{
		form.potential_name.disabled = true;
		form.closedate.disabled = true;
		form.potential_amount.disabled = true;
		form.potential_sales_stage.disabled = true;
		
	}
	else
	{
		form.potential_name.disabled = false;
		form.closedate.disabled = false;
		form.potential_amount.disabled = false;
		form.potential_sales_stage.disabled = false;
		form.potential_sales_stage.value="";
	}	

}


function set_return(product_id, product_name) {
	if(document.getElementById('from_link').value != '') {
        window.opener.document.QcEditView.parent_name.value = product_name;
        window.opener.document.QcEditView.parent_id.value = product_id;
	} else {
        window.opener.document.EditView.parent_name.value = product_name;
        window.opener.document.EditView.parent_id.value = product_id;
}
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

function set_return_specific(product_id, product_name) {
        //Used for DetailView, Removed 'EditView' formname hardcoding
        var fldName = getOpenerObj("lead_name");
        var fldId = getOpenerObj("lead_id");
        fldName.value = product_name;
        fldId.value = product_id;
}
function add_data_to_relatedlist(entity_id,recordid) {
	
	opener.document.location.href="index.php?module=Emails&action=updateRelations&destination_module=leads&entityid="+entity_id+"&parentid="+recordid;
}
//added by rdhital/Raju for emails
function submitform(id){
		document.massdelete.entityid.value=id;
		document.massdelete.submit();
}	

function searchMapLocation(addressType)
{
        var mapParameter = '';
        if (addressType == 'Main')
        {
		if(fieldname.indexOf('lane') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('lane')]))
	                        mapParameter = document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('lane')]).innerHTML+' ';
		}
		if(fieldname.indexOf('pobox') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('pobox')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('pobox')]).innerHTML+' ';
		}
		if(fieldname.indexOf('city') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('city')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('city')]).innerHTML+' ';
		}
		if(fieldname.indexOf('state') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('state')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('state')]).innerHTML+' ';
		}
		if(fieldname.indexOf('country') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('country')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('country')]).innerHTML+' ';
		}
		if(fieldname.indexOf('code') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('code')]))
	                        mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('code')]).innerHTML+' ';
		}
        }
	mapParameter = removeHTMLFormatting(mapParameter);
        window.open('http://maps.google.com/maps?q='+mapParameter,'goolemap','height=450,width=700,resizable=no,titlebar,location,top=200,left=250');
}


