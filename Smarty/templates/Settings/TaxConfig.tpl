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
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/Inventory.js"></script>

{literal}
<style>
	
	.tax_delete{
		text-decoration:none;
	}
	
	.tax_delete td{
			
	}
	
</style>
{/literal}
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody>
   <tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>
	<div align=center>

			{include file='SetMenu.tpl'}
				<!-- DISPLAY -->
{if $EDIT_MODE eq 'true'}
	{assign var=formname value='EditTax'}
	{assign var=shformname value='SHEditTax'}
{else}
	{assign var=formname value='ListTax'}
	{assign var=shformname value='SHListTax'}
{/if}


<!-- This table is used to display the Tax Configuration values-->
<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
   <tr>
	<td width=50 rowspan=2 valign=top><img src="{'taxConfiguration.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" width="48" height="48" border=0 title="{$MOD.LBL_USERS}"></td>
	<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > 
		{if $EDIT_MODE eq 'true'}
			<strong>{$MOD.LBL_EDIT} {$MOD.LBL_TAX_SETTINGS} </strong>
		{else}
			<strong>{$MOD.LBL_TAX_SETTINGS} </strong>
		{/if}
		</b>
	</td>
   </tr>
   <tr>
	<td valign=top class="small">{$MOD.LBL_TAX_DESC}</td>
   </tr>
</table>

<br>
<table border=0 cellspacing=0 cellpadding=10 width=100%>
   <tr>
	<td style="border-right:1px dotted #CCCCCC;" valign="top">
		<!-- if EDIT_MODE is true then Textbox will be displayed else the value will be displayed-->
		<form name="{$formname}" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
		<input type="hidden" name="module" value="Settings">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="parenttab" value="Settings">
		<input type="hidden" name="save_tax" value="">
		<input type="hidden" name="edit_tax" value="">
		<input type="hidden" name="add_tax_type" value="">

		<!-- Table to display the Product Tax Add and Edit Buttons - Starts -->
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
		   <tr>
			<td class="big" colspan="3"><strong>{$MOD.LBL_PRODUCT_TAX_SETTINGS} </strong></td>
		   </tr>
		   <tr>
			<td>&nbsp;</td>
			<td id="td_add_tax" class="small" colspan="2" align="right" nowrap>
				{if $EDIT_MODE neq 'true'}
					<input title="{$MOD.LBL_ADD_TAX_BUTTON}" accessKey="{$MOD.LBL_ADD_TAX_BUTTON}" onclick="fnAddTaxConfigRow('');" type="button" name="button" value="{$MOD.LBL_ADD_TAX_BUTTON}" class="crmButton small edit">
				{/if}
			</td>
			<td class="small" align=right nowrap>
			{if $EDIT_MODE eq 'true'}	
				<input class="crmButton small save" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}"  onclick="this.form.action.value='TaxConfig'; this.form.save_tax.value='true'; this.form.parenttab.value='Settings'; return validateTaxes('tax_count');" type="submit" name="button2" value=" {$APP.LBL_SAVE_BUTTON_LABEL}  ">&nbsp;
				<input class="crmButton small cancel" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.module.value='Settings'; this.form.save_tax.value='false'; this.form.parenttab.value='Settings';" type="submit" name="button22" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
			{elseif $TAX_COUNT > 0}
				<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.add_tax_type.value=''; this.form.edit_tax.value='true'; this.form.parenttab.value='Settings';" type="submit" name="button" value="  {$APP.LBL_EDIT_BUTTON_LABEL}  " class="crmButton small edit">
			{/if}
			</td>
		   </tr>
		</table>
		<!-- Table to display the Product Tax Add and Edit Buttons - Ends -->

		<!-- Table to display the List of Product Tax values - Starts -->
		<table id="add_tax" border=0 cellspacing=0 cellpadding=5 width=100% class="listRow">
		   {if $TAX_COUNT eq 0}
			<tr><td>{$MOD.LBL_NO_TAXES_AVAILABLE}. {$MOD.LBL_PLEASE} {$MOD.LBL_ADD_TAX_BUTTON}.</td></tr>
		   {else}
			{foreach item=tax key=count from=$TAX_VALUES}

				<!-- To set the color coding for the taxes which are active and inactive-->
				{if $tax.deleted eq 0}
				   <tr><!-- set color to taxes which are active now-->
				{else}
				   <tr><!-- set color to taxes which are disabled now-->
				{/if}
				
				<!--assinging tax label name for javascript validation-->
				{assign var=tax_label value="taxlabel_"|cat:$tax.taxname} 
        
				<td width=35% class="cellLabel small" >
					{if $EDIT_MODE eq 'true'}
						{assign var = pstax value = $tax.taxlabel}
						<input name="{$pstax|bin2hex}" id={$tax_label} type="text" value="{$tax.taxlabel}" class="detailedViewTextBox small">
					{else}
						{$tax.taxlabel}
					{/if}
				</td>
				<td width=55% class="cellText small">
					{if $EDIT_MODE eq 'true'}
						<input name="{$tax.taxname}" id="{$tax.taxname}" type="text" value="{$tax.percentage}" class="detailedViewTextBox small">&nbsp;%
					{else}
						{$tax.percentage}&nbsp;%
					{/if}
				</td>
				<td width=10% class="cellText small">
						{if $tax.deleted eq 0}
							<a href="index.php?module=Settings&action=TaxConfig&parenttab=Settings&disable=true&taxname={$tax.taxname}"><img src="{'enabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_ENABLE}" title="{$MOD.LBL_ENABLE}"></a>
						{else}
							<a href="index.php?module=Settings&action=TaxConfig&parenttab=Settings&enable=true&taxname={$tax.taxname}"><img src="{'disabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_ENABLE}" title="{$MOD.LBL_DISABLE}"></a>
						{/if}
				</td>
			   </tr>
			{/foreach}
			{if $EDIT_MODE eq 'true'}
				<input type="hidden" id="tax_count" value="{$count}">
			{/if}
		   {/if}
		</table>
		<!-- Table to display the List of Product Tax values - Ends -->
		</form>
	</td>

	<!-- Shipping Tax Config Table Starts Here -->
	<td width="50%" valign="top">
		<form name="{$shformname}" method="POST" action="index.php">
		<input type="hidden" name="module" value="Settings">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="parenttab" value="Settings">
		<input type="hidden" name="sh_save_tax" value="">
		<input type="hidden" name="sh_edit_tax" value="">
		<input type="hidden" name="sh_add_tax_type" value="">

		<!-- Table to display the S&H Tax Add and Edit Buttons - Starts -->
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
		   <tr>
        		<td class="big" colspan="3"><strong>{$MOD.LBL_SHIPPING_HANDLING_TAX_SETTINGS}</strong></td>
		   </tr>
		   <tr>
			<td>&nbsp;</td>
        		<td id="td_sh_add_tax" class="small" colspan="2" align="right" nowrap>
				{if $SH_EDIT_MODE neq 'true'}
					<input title="{$MOD.LBL_ADD_TAX_BUTTON}" accessKey="{$MOD.LBL_ADD_TAX_BUTTON}" onclick="fnAddTaxConfigRow('sh');" type="button" name="button" value="  {$MOD.LBL_ADD_TAX_BUTTON}  " class="crmButton small edit">
				{/if}
			</td>
			<td class="small" align=right nowrap>
				{if $SH_EDIT_MODE eq 'true'}
					<input class="crmButton small save" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}"  onclick="this.form.action.value='TaxConfig'; this.form.sh_save_tax.value='true'; this.form.parenttab.value='Settings'; return validateTaxes('sh_tax_count');" type="submit" name="button2" value=" {$APP.LBL_SAVE_BUTTON_LABEL}  ">
					&nbsp;
					<input class="crmButton small cancel" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.module.value='Settings'; this.form.sh_save_tax.value='false'; this.form.parenttab.value='Settings';" type="submit" name="button22" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
				{elseif $SH_TAX_COUNT > 0}
					<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='TaxConfig'; this.form.sh_add_tax_type.value=''; this.form.sh_edit_tax.value='true'; this.form.parenttab.value='Settings';" type="submit" name="button" value="  {$APP.LBL_EDIT_BUTTON_LABEL}  " class="crmButton small edit">
				{/if}
			</td>
		   </tr>
		</table>
		<!-- Table to display the S&H Tax Add and Edit Buttons - Ends -->

		<!-- Table to display the List of S&H Tax Values - Starts -->
		<table id="sh_add_tax" border=0 cellspacing=0 cellpadding=5 width=100% class="listRow">
		   {if $SH_TAX_COUNT eq 0}
			<tr><td>{$MOD.LBL_NO_TAXES_AVAILABLE}. {$MOD.LBL_PLEASE} {$MOD.LBL_ADD_TAX_BUTTON}.</td></tr>
		   {else}
		   	{foreach item=tax key=count from=$SH_TAX_VALUES}

			<!-- To set the color coding for the taxes which are active and inactive-->
			{if $tax.deleted eq 0}
			   <tr><!-- set color to taxes which are active now-->
			{else}
			   <tr><!-- set color to taxes which are disabled now-->
			{/if}

			{assign var=tax_label value="taxlabel_"|cat:$tax.taxname} 
			<td width=35% class="cellLabel small">
			 	{if $SH_EDIT_MODE eq 'true'}
                    {assign var = shtax value = $tax.taxlabel}
					<input name="{$shtax|bin2hex}" id="{$tax_label}" type="text" value="{$tax.taxlabel}" class="detailedViewTextBox small">
			 	{else} 
					{$tax.taxlabel}
				{/if}
			</td>
			<td width=55% class="cellText small">
				{if $SH_EDIT_MODE eq 'true'}
					<input name="{$tax.taxname}" id="{$tax.taxname}" type="text" value="{$tax.percentage}" class="detailedViewTextBox small">
					&nbsp;% 
				{else} 
					{$tax.percentage}&nbsp;% 
				{/if}
			</td>
			<td width=10% class="cellText small"> 
				{if $tax.deleted eq 0}
						<a href="index.php?module=Settings&action=TaxConfig&parenttab=Settings&sh_disable=true&sh_taxname={$tax.taxname}"><img src="{'enabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_ENABLE}" title="{$MOD.LBL_ENABLE}"></a>
					{else}
						<a href="index.php?module=Settings&action=TaxConfig&parenttab=Settings&sh_enable=true&sh_taxname={$tax.taxname}"><img src="{'disabled.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" alt="{$MOD.LBL_DISABLE}" title="{$MOD.LBL_DISABLE}"></a>
					{/if}
			</td>
		   </tr>
		   {/foreach}
		   {if $SH_EDIT_MODE eq 'true'}
			<input type="hidden" id="sh_tax_count" value="{$count}">
		   {/if}
		{/if}
		</table>
		<!-- Table to display the List of S&H Tax Values - Ends -->
	        </form>
	</td>
	<!-- Shipping Tax Ends Here -->
   </tr>
</table>

<table border=0 cellspacing=0 cellpadding=5 width=100% >
		   <tr>
			<td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
		   </tr>
</table>


			
			
			</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	</div>

</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</table>
<script>
	var tax_labelarr = {ldelim}SAVE_BUTTON:'{$APP.LBL_SAVE_BUTTON_LABEL}',
                                CANCEL_BUTTON:'{$APP.LBL_CANCEL_BUTTON_LABEL}',
                                TAX_NAME:'{$APP.LBL_TAX_NAME}',
                                TAX_VALUE:'{$APP.LBL_TAX_VALUE}'{rdelim};
</script>
