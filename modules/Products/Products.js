/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
document.write("<script type='text/javascript' src='modules/Products/multifile.js'></"+"script>");
document.write("<script type='text/javascript' src='include/js/Merge.js'></"+"script>");
function updateListPrice(unitprice,fieldname, oSelect)
{
	if(oSelect.checked == true)
	{
		document.getElementById(fieldname).style.visibility = 'visible';
		document.getElementById(fieldname).value = unitprice;
	}else
	{
		document.getElementById(fieldname).style.visibility = 'hidden';
	}
}

function check4null(form)
{
  var isError = false;
  var errorMessage = "";
  if (trim(form.productname.value) =='') {
			 isError = true;
			 errorMessage += "\n Product Name";
			 form.productname.focus();
  }

  if (isError == true) {
			 alert(alert_arr.MISSING_REQUIRED_FIELDS + errorMessage);
			 return false;
  }
  return true;
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
function set_return_specific(product_id, product_name) {
        //getOpenerObj used for DetailView 

	if(document.getElementById('from_link').value != '')
	{
		var fldName = window.opener.document.QcEditView.product_name;
		var fldId = window.opener.document.QcEditView.product_id;
	}else if(typeof(window.opener.document.DetailView) != 'undefined')
	{
	   var fldName = window.opener.document.DetailView.product_name;
	   var fldId = window.opener.document.DetailView.product_id;
	}else
	{
	   var fldName = window.opener.document.EditView.product_name;
	   var fldId = window.opener.document.EditView.product_id;
	}
        fldName.value = product_name;
        fldId.value = product_id;
}

function set_return_formname_specific(formname,product_id, product_name) {
        window.opener.document.EditView1.product_name.value = product_name;
        window.opener.document.EditView1.product_id.value = product_id;
}
function add_data_to_relatedlist(entity_id,recordid) {

        opener.document.location.href="index.php?module={RETURN_MODULE}&action=updateRelations&smodule={SMODULE}&destination_module=Products&entityid="+entity_id+"&parentid="+recordid;
}

function set_return_inventory(product_id,product_name,unitprice,qtyinstock,taxstr,curr_row,desc,subprod_id) {
	var subprod = subprod_id.split("::");
	window.opener.document.EditView.elements["subproduct_ids"+curr_row].value = subprod[0];
	window.opener.document.getElementById("subprod_names"+curr_row).innerHTML = subprod[1];

	window.opener.document.EditView.elements["productName"+curr_row].value = product_name;
	window.opener.document.EditView.elements["hdnProductId"+curr_row].value = product_id;
	window.opener.document.EditView.elements["listPrice"+curr_row].value = unitprice;
	window.opener.document.EditView.elements["comment"+curr_row].value = desc;
	//getOpenerObj("unitPrice"+curr_row).innerHTML = unitprice;
	getOpenerObj("qtyInStock"+curr_row).innerHTML = qtyinstock;

	// Apply decimal round-off to value
	if(!isNaN(parseFloat(unitprice))) unitprice = roundPriceValue(unitprice);
	window.opener.document.EditView.elements["listPrice"+curr_row].value = unitprice;
	
	var tax_array = new Array();
	var tax_details = new Array();
	tax_array = taxstr.split(',');
	for(var i=0;i<tax_array.length;i++)
	{
		tax_details = tax_array[i].split('=');
	}
	
	window.opener.document.EditView.elements["qty"+curr_row].focus()
}

function set_return_inventory_po(product_id,product_name,unitprice,taxstr,curr_row,desc,subprod_id) {
	var subprod = subprod_id.split("::");
	window.opener.document.EditView.elements["subproduct_ids"+curr_row].value = subprod[0];
	window.opener.document.getElementById("subprod_names"+curr_row).innerHTML = subprod[1];

	window.opener.document.EditView.elements["productName"+curr_row].value = product_name;
	window.opener.document.EditView.elements["hdnProductId"+curr_row].value = product_id;
	window.opener.document.EditView.elements["listPrice"+curr_row].value = unitprice;
	window.opener.document.EditView.elements["comment"+curr_row].value = desc;
	//getOpenerObj("unitPrice"+curr_row).innerHTML = unitprice;
	
	// Apply decimal round-off to value
	if(!isNaN(parseFloat(unitprice))) unitprice = roundPriceValue(unitprice);
	window.opener.document.EditView.elements["listPrice"+curr_row].value = unitprice;
		
	var tax_array = new Array();
	var tax_details = new Array();
	tax_array = taxstr.split(',');
	for(var i=0;i<tax_array.length;i++)
	{
		tax_details = tax_array[i].split('=');
	}
	
	window.opener.document.EditView.elements["qty"+curr_row].focus()
}

function set_return_product(product_id, product_name) {
	if(document.getElementById('from_link').value != '') {
        window.opener.document.QcEditView.parent_name.value = product_name;
        window.opener.document.QcEditView.parent_id.value = product_id;
	} else {
    window.opener.document.EditView.product_name.value = product_name;
    window.opener.document.EditView.product_id.value = product_id;
	}
}
function getImageListBody() {
	if (browser_ie) {
		var ImageListBody=getObj("ImageList")
	} else if (browser_nn4 || browser_nn6) {
		if (getObj("ImageList").childNodes.item(0).tagName=="TABLE") {
			var ImageListBody=getObj("ImageList")
		} else {
			var ImageListBody=getObj("ImageList")
		}
	}
	return ImageListBody;
}

// Function to Round off the Price Value
function roundPriceValue(val) {
   val = parseFloat(val);
   val = Math.round(val*100)/100;
   val = val.toString();
   
   if (val.indexOf(".")<0) {
      val+=".00"
   } else {
      var dec=val.substring(val.indexOf(".")+1,val.length)
      if (dec.length>2)
         val=val.substring(0,val.indexOf("."))+"."+dec.substring(0,2)
      else if (dec.length==1)
         val=val+"0"
   }
   
   return val;
} 
// End

