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
<link rel="stylesheet" type="text/css" href="themes/{$theme}/webmail.css">
<form onsubmit="VtigerJS_DialogBox.block();" method="POST" name="massdelete">
<table width="100%"  border="0" cellspacing="0" cellpadding="2">
	<input name="idlist" type="hidden">
	<input id="search_url" type="hidden" value="" name="search_url">
	<input id="excludedRecords" type="hidden" value="" name="excludedRecords">
	<input id="numOfRows" type="hidden" value="" name="numOfRows">
	<input id="allids" type="hidden" value="" name="allids">
	<input id="selectedboxes" type="hidden" value="" name="selectedboxes">
	<input id="allselectedboxes" type="hidden" value="" name="allselectedboxes">
	<input id="current_page_boxes" type="hidden" value="" name="current_page_boxes">
	<tr>
		<td width="10%" align="left">
			<input type="button" name="Button2" value=" {'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE}"  class="crmbutton small delete" onClick="return massDelete();"/>
		</td>
		<td width="40%" align="right" class="small">
			<font color="#000000">{$APP.LBL_SEARCH}</font>&nbsp;<input type="text" name="search_text" id="search_text" class="importBox" >&nbsp;
		</td>
		<td width="20%" align=left class="small">
			<select name="search_field" id="search_field" onChange="Searchfn();" class="importBox">
				<option value='subject'>{$MOD.LBL_IN_SUBJECT}</option>
				<option value='user_name'>{$MOD.LBL_IN_SENDER}</option>
				<option value='join'>{$MOD.LBL_IN_SUBJECT_OR_SENDER}</option>
			</select>&nbsp;
		</td>
		<td width="10%">
			<input name="find" value=" Find " class="crmbutton small create" onclick="Searchfn();" type="button">
		</td>
			{$NAVIGATION}
	</tr>
</table>
<div id="rssScroll">
	<table cellspacing="0" cellpadding="0" width=100%>
        <tr>
			<th width="5%" class='tableHeadBg' align="left"><input type="checkbox"  name="selectall" onClick="toggleSelect(this.checked,'selected_id')"></th>
            <th width="65%" class='tableHeadBg'align="left">{$LISTHEADER.0}</th>
            <th width="15%" class='tableHeadBg'align="left">{$LISTHEADER.1}</th>
            <th width="15%" class='tableHeadBg'align="left">{$LISTHEADER.2}</th>
        </tr>
		{if $LISTENTITY != NULL}
			{foreach key=id item=row from=$LISTENTITY}
			    <tr id="row_{$id}">
				<td>
				<span><input type="checkbox" name="selected_id"  value= '{$id}' onClick=toggleSelectAll(this.name,"selectall")>
				</span></td>
				<td onClick="getEmailContents('{$id}');" style="cursor:pointer;"><b>{$row.0}</b></td>
				<td onClick="getEmailContents('{$id}');" style="cursor:pointer;">{$row.1}</td>
				{if $EMAILFALG.$id eq 'SAVED'}
					<td onClick="getEmailContents('{$id}');" style="cursor:pointer;"></td>
				{else}
					<td onClick="getEmailContents('{$id}');" style="cursor:pointer;">{$row.2}</td>
				{/if}
			        </tr>
			{/foreach}
		{else}
			<tr><td>&nbsp;</td><td align="center" nowrap><b>{$MOD.LBL_NO_RECORDS}</b></td></tr>
		{/if}
    </table>
	</form>
</div>
<div id="EmailDetails"></div>
<SCRIPT>
	if(gselectedrowid != 0)
	{ldelim}
		var rowid = 'row_'+gselectedrowid;
	    getObj(rowid).className = 'emailSelected';
	{rdelim}
</SCRIPT>
