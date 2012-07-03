<!--*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->
{literal}
<script language="javascript">
function addtopricebook()
{
	x = document.addToPB.selected_id.length;
	prod_array = new Array(x);
	idstring = "";
	if ( x == undefined)
	{
		if (document.addToPB.selected_id.checked)
		{
			yy = document.addToPB.selected_id.value+"_listprice";
			document.addToPB.idlist.value=document.addToPB.selected_id.value;
			var elem = document.addToPB.elements;
			var ele_len =elem.length;
			var i=0,j=0;
	
			for(i=0; i<ele_len; i++)
			{	
				if(elem[i].name == yy)
				{
					var val = elem[i].value.replace(/^\s+/g, '').replace(/\s+$/g, '');
					if(typeof userCurrencySeparator != 'undefined') {
						while(val.indexOf(userCurrencySeparator) != -1) {
							val = val.replace(userCurrencySeparator,'');
						}
					}
					if(typeof userDecimalSeparator != 'undefined') {
						if(val.indexOf(userDecimalSeparator) != -1) {
							val = val.replace(userDecimalSeparator,'.');
						}
					}
					if (val.length==0)
					{
						alert(alert_arr.LISTPRICE_CANNOT_BE_EMPTY);
			               		return false;	
					}	
					else if(isNaN(val))
                                        {
                                                alert(alert_arr.INVALID_LIST_PRICE);
                                                return false;
                                        }

				}
			}	
			
		}
		else 
		{
			alert(alert_arr.SELECT);
			return false;
		}
	}
	else
	{
		xx = 0;
		for(i = 0; i < x ; i++)
		{
			if(document.addToPB.selected_id[i].checked)
			{
				idstring = document.addToPB.selected_id[i].value +";"+idstring;
				 prod_array[xx] = document.addToPB.selected_id[i].value;
				
				xx++;	
			}
		}
		if (xx != 0)
		{
			document.addToPB.idlist.value=idstring;
			var elem = document.addToPB.elements;
                        var ele_len =elem.length;
                        var i=0,j=0;

                        for(i=0; i<ele_len; i++)
                        {
                                for(j=0; j < xx; j++)
                                {
                                        var xy= prod_array[j]+"_listprice";
                                        if(elem[i].name == xy)
                                        {
						var val = elem[i].value.replace(/^\s+/g, '').replace(/\s+$/g, '');
						if(typeof userCurrencySeparator != 'undefined') {
							while(val.indexOf(userCurrencySeparator) != -1) {
								val = val.replace(userCurrencySeparator,'');
							}
						}
						if(typeof userDecimalSeparator != 'undefined') {
							if(val.indexOf(userDecimalSeparator) != -1) {
								val = val.replace(userDecimalSeparator,'.');
							}
						}
						if (val.length==0)
						{
							alert(alert_arr.LISTPRICE_CANNOT_BE_EMPTY);
			                		return false;	
						}
						else if(isNaN(val)|| val <= 0)
                	                        {
        	                                        alert(alert_arr.INVALID_LIST_PRICE);
                                                	return false;
                                        	}
	
                                        }
                                }
                        }

		}
		else
		{
			alert(alert_arr.SELECT);
			return false;
		}
	}
{/literal}
document.addToPB.action="index.php?module=Products&action=addPbProductRelToDB&return_module={$RETURN_MODULE}&return_action={$RETURN_ACTION}&return_id={$RETURN_ID}&parenttab={$CATEGORY}"
{rdelim}


function updateAllListPrice()
{ldelim}
        var unitprice_array = new Array({$UNIT_PRICE_ARRAY});
        var fieldname_array = new Array({$FIELD_NAME_ARRAY});
        var unitprice,fieldname;
	var id;
	var fieldinfo;
	var checkid;

        for(j=0; j<fieldname_array.length; j++)
        {ldelim}
			fieldinfo = fieldname_array[j].split("_");
			id = fieldinfo[0];
			checkid = "check_"+id;

			unitprice=unitprice_array[j];
            fieldname=fieldname_array[j];
            updateListPrice(unitprice,fieldname,document.getElementById(checkid));
        {rdelim}
{rdelim}

function updateListPriceForField(fieldname,element)
{ldelim}
	var unitprice_array = new Array({$UNIT_PRICE_ARRAY});
	var fieldname_array = new Array({$FIELD_NAME_ARRAY});

	var index = fieldname_array.indexOf(fieldname);
	updateListPrice(unitprice_array[index],fieldname,element);
{rdelim}
</script>
<script language="javascript" src="modules/Products/Products.js"></script>
<table width="95%" border="0" cellpadding="0" cellspacing="0">
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td>&nbsp;</td>
		<td class="showPanelBg">
			{$PRICEBOOKLISTHEADER}
			<table border="0" cellpadding="0" cellspacing="0"  width="100%">
				<tr height="20"> 
					<td  class="listFormHeaderLinks">
				 		<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>&nbsp;{$RECORD_COUNTS}</td>
								{$NAVIGATION}
							</tr>
				 		</table>
			   		</td>
			   	</tr>
			   	<tr>
			   		<td>
						<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="3" cellspacing="1" width="90%" align="center">
							{$LISTHEADER}
							{$LISTENTITY}
						</table>
					</td>
			   	</tr>   
			   	<tr><td>&nbsp;</td></tr>
			   	</form>
				</table>
			
				</form>
			</table>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>


