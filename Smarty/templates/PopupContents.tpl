{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<!-- BEGIN: main -->
<form name="selectall" method="POST">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="small">
	<tr>
	{if $SELECT eq 'enable' && ($POPUPTYPE neq 'inventory_prod' && $POPUPTYPE neq 'inventory_prod_po' && $POPUPTYPE neq 'inventory_service')}
		<td style="padding-left:10px;" align="left"><input class="crmbutton small save" type="button" value="{$APP.LBL_SELECT_BUTTON_LABEL} {$MODULE|@getTranslatedString:$MODULE}" onclick="if(SelectAll('{$MODULE}','{$RETURN_MODULE}')) window.close();"/></td>
	{elseif $SELECT eq 'enable' && ($POPUPTYPE eq 'inventory_prod' || $POPUPTYPE eq 'inventory_prod_po')}
		{if $RECORD_ID}
			<td style="padding-left:10px;" align="left" width=10%><input class="crmbutton small save" type="button" value="{$APP.LBL_BACK}" onclick="window.history.back();"/></td>
		{/if}
		<td style="padding-left:10px;" align="left"><input class="crmbutton small save" type="button" value="{$APP.LBL_SELECT_BUTTON_LABEL} {$MODULE|@getTranslatedString:$MODULE}" onclick="if(InventorySelectAll('{$RETURN_MODULE}',image_pth))window.close();"/></td>
	{elseif $SELECT eq 'enable' && $POPUPTYPE eq 'inventory_service'}
		<td style="padding-left:10px;" align="left"><input class="crmbutton small save" type="button" value="{$APP.LBL_SELECT_BUTTON_LABEL} {$MODULE|@getTranslatedString:$MODULE}" onclick="if(InventorySelectAllServices('{$RETURN_MODULE}',image_pth))window.close();"/></td>
	{else}		
		<td>&nbsp;</td>	
	{/if}
	<td style="padding-right:10px;" align="right">{$RECORD_COUNTS}</td></tr>
   	<tr>
	    <td style="padding:10px;" colspan=3>

       	<input name="module" type="hidden" value="{$RETURN_MODULE}">
		<input name="action" type="hidden" value="{$RETURN_ACTION}">
        <input name="pmodule" type="hidden" value="{$MODULE}">
		<input type="hidden" name="curr_row" value="{$CURR_ROW}">	
		<input name="entityid" type="hidden" value="">
		<input name="popuptype" id="popup_type" type="hidden" value="{$POPUPTYPE}">
		<input name="idlist" type="hidden" value="">
		<div style="overflow:auto;height:348px;">
		<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
		<tbody>
		<tr>
			{if $SELECT eq 'enable'}
				<td class="lvtCol" width="3%"><input type="checkbox" name="select_all" value="" onClick=toggleSelect(this.checked,"selected_id")></td>
            {/if}
		    {foreach item=header from=$LISTHEADER}
		        <td class="lvtCol">{$header}</td>
		    {/foreach}
			{if $SELECT eq 'enable' && ($POPUPTYPE eq 'inventory_prod' || $POPUPTYPE eq 'inventory_prod_po')}
				{if !$RECORD_ID}
					<td class="lvtCol">{$APP.LBL_ACTION}</td>
				{/if}
			{/if}
		</tr>
		{foreach key=entity_id item=entity from=$LISTENTITY}
	        <tr bgcolor=white onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'"  >
		   {if $SELECT eq 'enable'}
			<td><input type="checkbox" name="selected_id" value="{$entity_id}" onClick=toggleSelectAll(this.name,"select_all")></td>
		   {/if}
                   {foreach item=data from=$entity}
		        <td>{$data}</td>
                   {/foreach}
		</tr>
		{foreachelse}
                        <tr><td colspan="{$HEADERCOUNT}">
                        <div style="border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 99%;position: relative; z-index: 10000000;">
                        <table border="0" cellpadding="5" cellspacing="0" width="98%">
                                <tr>
                                        <td rowspan="2" width="25%"><img src="{'empty.jpg'|@vtiger_imageurl:$THEME}" height="60" width="61%"></td>
                                        {if $recid_var_value neq '' && $mod_var_value neq '' && $RECORD_COUNTS eq 0 }
					<script>redirectWhenNoRelatedRecordsFound();</script>
                                        <td style="border-bottom: 1px solid rgb(204, 204, 204);" nowrap="nowrap" width="75%"><span class="genHeaderSmall">{$APP.LBL_NO} {$MODULE|@getTranslatedString:$MODULE} {$APP.RELATED} !</td>
                                        {else}
                                        <td style="border-bottom: 1px solid rgb(204, 204, 204);" nowrap="nowrap" width="75%"><span class="genHeaderSmall">{$APP.LBL_NO} {$MODULE|@getTranslatedString:$MODULE} {$APP.LBL_FOUND} !</td>
                                        {/if}
                                </tr>
                        </table>
                        </div>
                        </td></tr>
                {/foreach}
	      	</tbody>
	    	</table>
			<div>
	    </td>
	</tr>

</table>
<table width="100%" align="center" class="reportCreateBottom">
<tr>
	{$NAVIGATION}	
<td width="35%">&nbsp;</td>
</tr>
</table>
</form>

