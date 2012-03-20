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

<table width="100%" cellpadding="5" cellspacing="0" class="listTable" >
	<tr>
                <td class="colHeader small" width="3%">#</td>
                <td class="colHeader small" width="9%">{$MOD.LBL_CURRENCY_TOOL}</td>
        	<td class="colHeader small" width="23%">{$MOD.LBL_CURRENCY_NAME}</td>
                <td class="colHeader small" width="20%">{$MOD.LBL_CURRENCY_CODE}</td>
                <td class="colHeader small" width="10%">{$MOD.LBL_CURRENCY_SYMBOL}</td>
                <td class="colHeader small" width="20%">{$MOD.LBL_CURRENCY_CRATE}</td>
                <td class="colHeader small" width="15%">{$MOD.LBL_CURRENCY_STATUS}</td>
	</tr>
	{foreach item=currencyvalues name=currlist key=id from=$CURRENCY_LIST}
		<tr>
			<td nowrap class="listTableRow small" valign="top">{$smarty.foreach.currlist.iteration}</td>
			<td nowrap class="listTableRow small" valign="top">{$currencyvalues.tool}</td>
			<td nowrap class="listTableRow small" valign="top"><b>{$currencyvalues.name|@getTranslatedCurrencyString}</b></td>
			<td nowrap class="listTableRow small" valign="top">{$currencyvalues.code}</td>
			<td nowrap class="listTableRow small" valign="top">{$currencyvalues.symbol}</td>
			<td nowrap class="listTableRow small" valign="top">{$currencyvalues.crate}</td>
			{if $currencyvalues.status eq 'Active'}
			<td nowrap class="listTableRow small active" valign="top">{$currencyvalues.status}</td>
			{else}
			<td nowrap class="listTableRow small inactive" valign="top">{$currencyvalues.status}</td>
			{/if}
                 </tr>
        {/foreach}
</table>

