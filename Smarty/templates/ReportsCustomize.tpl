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
<!-- Customized Reports Table Starts Here  -->
	<form>
	{if $DEL_DENIED neq ""}
	<span id="action_msg_status" class="small" align="left"><font color=red><b>{$MOD.LBL_PERM_DENIED} {$DEL_DENIED}</b> </font></span>
	{/if}
	<input id="folder_ids" name="folderId" type="hidden" value='{$FOLDE_IDS}'>
	{assign var=poscount value=0}
	{foreach item=reportfolder from=$REPT_CUSFLDR}	
	{assign var=poscount value=$poscount+1}
		<table class="reportsListTable" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">		
		<tr>
		<td class="mailSubHeader" align="left" colspan="3" style="font-weight:bold;">
		<span id='folder{$reportfolder.id}'> {$reportfolder.name}</span>
		<i><font color='#C0C0C0'>
			{if $reportfolder.description neq ''}
				 - {$reportfolder.description}
			{/if}
		</font></i>
		
		</td>
		</tr>
		<tr>
			<td  class="hdrNameBg" colspan="3" style="padding: 5px;" align="right" >
				<!-- Custom Report Group's Buttons -->
				<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
		<td  id="repposition{$poscount}" width="5%" align="right"><input name="newReportInThisModule" value="{$MOD.LBL_CREATE_REPORT}..." class="crmButton small create" onclick="gcurrepfolderid={$reportfolder.id};fnvshobj(this,'reportLay')" type="button"></td>
			<td  width="75%" align="right">
					<input type="button" name="Edit" value=" {$MOD.LBL_RENAME_FOLDER} " class="crmbutton small edit" onClick='EditFolder("{$reportfolder.id}","{$reportfolder.fname}","{$reportfolder.fdescription}"),fnvshobj(this,"orgLay");'>&nbsp;
			</td>
			<td align="right">
					<input type="button" name="delete" value=" {$MOD.LBL_DELETE_FOLDER} " class="crmbutton small delete" onClick="DeleteFolder('{$reportfolder.id}');">
			</td>
		</tr>
		</table>
			</td>
			</tr>
		<tr>
		<td colspan="3">
		<table  border="0" cellpadding="3" cellspacing="1" width="100%">
			<tbody>
			<tr>
			<td class="lvtCol" width="5%">
				<input type="checkbox" name="selectall" onclick='toggleSelect(this.checked,"selected_id{$reportfolder.id}")' value="checkbox" />
			</td>
			<td class="lvtCol" align="left" width="35%">{$MOD.LBL_REPORT_NAME}</td>
			<td class="lvtCol" align="left" width="50%">{$MOD.LBL_DESCRIPTION}</td>
			<td class="lvtCol" width="10%">{$MOD.LBL_TOOLS}</td>
			</tr>
			{foreach name=reportdtls item=reportdetails from=$reportfolder.details}
			<tr class="lvtColData" onmouseover="this.className='lvtColDataHover'" onmouseout="this.className='lvtColData'" bgcolor="white">
			<td>
				{if $reportdetails.customizable eq '1' && $reportdetails.editable eq 'true'}
					<input name="selected_id{$reportfolder.id}" value="{$reportdetails.reportid}" onclick='toggleSelectAll(this.name,"selectall")' type="checkbox">
				{/if}
			</td>
			<td align="left"><a href="index.php?module=Reports&action=SaveAndRun&record={$reportdetails.reportid}&folderid={$reportfolder.id}">{$reportdetails.reportname}</a>
			{if $reportdetails.sharingtype eq 'Shared'}
				<img src="{'Meetings.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border=0 height=12 width=12 /> 
			{/if}
			</td>
			<td align="left">{$reportdetails.description}</td>
			<td align="right" nowrap>
			{if $reportdetails.customizable eq '1' && $reportdetails.editable eq 'true'}
				<a href="javascript:;" onClick="editReport('{$reportdetails.reportid}');"><img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" title="Customize..." border="0"></a>
			{/if}
			{if $reportdetails.state neq 'SAVED' && $reportdetails.editable eq 'true'}
			&nbsp;| &nbsp;<a href="javascript:;" onClick="DeleteReport('{$reportdetails.reportid}');"><img src="{'delete.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" title="Delete..." border="0"></a>
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
	{foreachelse}
	<div align="center" style="position:relative;width:50%;height:30px;border:1px dashed #CCCCCC;background-color:#FFFFCC;padding:10px;">
	<a href="javascript:;" onclick="fnvshobj(this,'orgLay');">{$MOD.LBL_CLICK_HERE}</a>&nbsp;{$MOD.LBL_TO_ADD_NEW_GROUP}
	</div>
	{/foreach}
	</form>
	<!-- Customized Reports Table Ends Here  -->

<div style="display: none;left:193px;top:106px;width:155px;" id="folderLay" onmouseout="fninvsh('folderLay')" onmouseover="fnvshNrm('folderLay')">
<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr><td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;" align="left"><b>{$MOD.LBL_MOVE_TO} :</b></td></tr>
	<tr>
	<td align="left">
	{foreach item=folder from=$REPT_FOLDERS}
	<a href="javascript:;" onClick='MoveReport("{$folder.id}","{$folder.fname}");' class="drop_down">- {$folder.name}</a>
	{/foreach}
	</td>
	</tr>
</table>
</div>
