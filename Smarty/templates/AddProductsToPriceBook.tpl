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
<script language="javascript">
var parenttab = "{$CATEGORY}";
function updateAllListPrice()
{ldelim}
        var unitprice_array = new Array({$UNIT_PRICE_ARRAY});
        var fieldname_array = new Array({$FIELD_NAME_ARRAY});

	var n=unitprice_array.length;	
	var unitprice,fieldname;
	var id;
	var fieldinfo;
	var checkid;

	for(j=0; j<unitprice_array.length; j++)
	{ldelim}
		fieldinfo = fieldname_array[j].split("_");
		id = fieldinfo[0];
		checkid = "check_"+id;

		unitprice=unitprice_array[j];
		fieldname=fieldname_array[j];	
		updateListPrice(unitprice,fieldname,document.getElementById(checkid));
	{rdelim}
{rdelim}

</script>
<script language="javascript" src="modules/PriceBooks/PriceBooks.js"></script>
<BR>
<BR>
{$PRODUCTLISTHEADER}
<table border="0" cellpadding="0" cellspacing="0" class="FormBorder" width="100%">
  <tr height="20"> 
    <td >
	 <table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>&nbsp;{$RECORD_COUNTS}</td>
			   {$NAVIGATION}
		</tr>
	 </table>
    </td>
   </tr>
   <tr><td >
   <table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="3" cellspacing="1" width="90%" align="center">
   {$LISTHEADER}
   {$LISTENTITY}
   </table>
   </td></tr>
   <tr><td>&nbsp;</td></tr>
   </form>
</table>

</form>
</table>


