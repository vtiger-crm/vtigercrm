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
<table align="center" border="0" cellpadding="0" cellspacing="0" width="99%"  class="showPanelBg">
	<tbody><tr>
	<td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td valign="top" width="50%" style="padding: 10px;border-right:1px dashed #CCCCCC">
	<!-- Reports Table Starts Here  -->
	{foreach item=reportfolder from=$REPT_FLDR}
	<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="reportsListTable">
		<tr>
		<td class="mailSubHeader"><b>
		
		{if $MOD[$reportfolder.name] neq ''}
			{$MOD[$reportfolder.name]}
		{else}
			{$reportfolder.name}
		{/if}
		</b>
		<i><font color='#C0C0C0'>
			{if $MOD[$reportfolder.description] neq ''}
				 - {$MOD[$reportfolder.description]}
			{else}
				 - {$reportfolder.description}
			{/if}
		</font></i>
		</td>
		</tr>
		<tr>
		<td>
		<table  class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
			<tbody>
			<tr>
			<td class="lvtCol" width="5%">#</td>
			<td class="lvtCol" width="35%">{$MOD.LBL_REPORT_NAME}</td>
			<td class="lvtCol" width="50%">{$MOD.LBL_DESCRIPTION}</td>
			<td class="lvtCol" width="10%">{$MOD.LBL_TOOLS}</td>
			</tr>
			{foreach name=reportdtls item=reportdetails from=$reportfolder.details}
				<tr class="lvtColData" onmouseover="this.className='lvtColDataHover'" onmouseout="this.className='lvtColData'" bgcolor="white">
				<td>{$smarty.foreach.reportdtls.iteration}</td>
				{if $MOD[$reportdetails.reportname] neq ''}
					<td><a href="index.php?module=Reports&action=SaveAndRun&record={$reportdetails.reportid}&folderid={$reportfolder.id}">{$MOD[$reportdetails.reportname]}</a>
					{if $reportdetails.sharingtype eq 'Shared'}
						<img src="{'Meetings.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border=0 height=12 width=12 /> 
					{/if}
					</td>
				{else}
					<td><a href="index.php?module=Reports&action=SaveAndRun&record={$reportdetails.reportid}&folderid={$reportfolder.id}">{$reportdetails.reportname}</a>
					{if $reportdetails.sharingtype eq 'Shared'}
						<img src="{'Meetings.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border=0 height=12 width=12 /> 
					{/if}
					</td>
				{/if}
				{if $MOD[$reportdetails.description] neq ''}
					<td>{$MOD[$reportdetails.description]}</td>
				{else}
					<td>{$reportdetails.description}</td>
				{/if}
				<td align="center" nowrap>
					{if $reportdetails.customizable eq '1' && $reportdetails.editable eq 'true'}
						<a href="javascript:;" onClick="editReport('{$reportdetails.reportid}');"><img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" title="{$MOD.LBL_CUSTOMIZE_BUTTON}..." border="0"></a>
					{/if}
					{if $reportdetails.state neq 'SAVED' && $reportdetails.editable eq 'true'}
						&nbsp;| &nbsp;<a href="javascript:;" onclick="DeleteReport('{$reportdetails.reportid}');"><img src="{'delete.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" title="{$MOD.LBL_DELETE}..." border="0"></a>
					{/if}
				</td>
				</tr>
			{/foreach}

			</tbody>
		</table>
		</td>
		</tr>
	</table>
	<br />
	{/foreach}
	<!-- Reports Table Ends Here  -->
	</td>
	<td style="padding:10px;" valign="top" align="center" width="50%">
	<div id="customizedrep">
		{include file="ReportsCustomize.tpl"}	
	</div>
	</td>
	<td valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
	</tr>
	<tr>
	<td colspan="2" align="center">&nbsp;</td>
	</tr>
	</tbody>
</table>

