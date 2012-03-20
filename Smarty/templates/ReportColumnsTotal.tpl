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
<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" height="532" width="100%" valign="top">
	<tbody><tr>
	<td colspan="2">
	<span class="genHeaderGray">{$MOD.LBL_CALCULATIONS}</span><br>
	{$MOD.LBL_SELECT_COLUMNS_TO_TOTAL}
	<hr>
	</td>
	</tr>
	<tr><td colspan="2">
	<div style="overflow:auto;height:448px">
	<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="100%" valign="top">
		<tbody>
		<tr>	
		<td class="lvtCol" nowrap width="40%">{$MOD.LBL_COLUMNS}</td>
		<td class="lvtCol" nowrap width="15%">{$MOD.LBL_COLUMNS_SUM}</td>
		<td class="lvtCol" nowrap width="15%">{$MOD.LBL_COLUMNS_AVERAGE}</td>
		<td class="lvtCol" nowrap width="15%">{$MOD.LBL_COLUMNS_LOW_VALUE}</td>
		<td class="lvtCol" nowrap width="15%">{$MOD.LBL_COLUMNS_LARGE_VALUE}</td>
		</tr>
		{foreach item=modules from=$BLOCK1}
		{foreach item=row from=$modules}
		<tr class="lvtColData" onmouseover="this.className='lvtColDataHover'" onmouseout="this.className='lvtColData'" bgcolor="white">
		{if $RECORDID neq ''}
                        <td><b>{$row.label.0}</b></td>
                {else}
                        <td><b>{$row.0}</b></td>
                {/if}
		<td>{$row.1}</td>
		<td>{$row.2}</td>
		<td>{$row.3}</td>
		<td>{$row.4}</td>
		</tr>
		{/foreach}
		{/foreach}

	{if $ROWS_COUNT eq 0}
		<tr class="lvtColData" bgcolor="white"><td colspan="5"><b>{$MOD.NO_COLUMN}</b></td></tr>
	{/if}

		</tbody>
	</table>
	</div>
	</td></tr>
	</tbody>
</table>
