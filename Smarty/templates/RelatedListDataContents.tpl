{*<!--
/*+********************************************************************************
  * The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  *********************************************************************************/
-->*}
<table border=0 cellspacing=0 cellpadding=0 width=100% class="small" 
	style="border-bottom:1px solid #999999;padding:5px; background-color: #eeeeff;">
	<tr>
		<td align="left">
			{$RELATEDLISTDATA.navigation.0}
			{if $MODULE eq 'Campaigns' && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 
				'Leads' || $RELATED_MODULE eq 'Accounts') && $RELATEDLISTDATA.entries|@count > 0}
			{/if}
		</td>
		<td align="center">{$RELATEDLISTDATA.navigation.1} </td>
		<td align="right">
			{$RELATEDLISTDATA.CUSTOM_BUTTON}

			{if $HEADER eq 'Contacts' && $MODULE neq 'Campaigns' && $MODULE neq 'Accounts' && $MODULE neq 'Potentials' && $MODULE neq 'Products' && $MODULE neq 'Vendors'}
				{if $MODULE eq 'Calendar'}
					<input alt="{$APP.LBL_SELECT_CONTACT_BUTTON_LABEL}" title="{$APP.LBL_SELECT_CONTACT_BUTTON_LABEL}" accessKey="" class="crmbutton small edit" value="{$APP.LBL_SELECT_BUTTON_LABEL} {$APP.Contacts}" LANGUAGE=javascript onclick='return window.open("index.php?module=Contacts&return_module={$MODULE}&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid={$ID}{$search_string}","test","width=640,height=602,resizable=0,scrollbars=0");' type="button"  name="button"></td>
				{elseif $MODULE neq 'Services'}
					<input title="{$APP.LBL_ADD_NEW} {$APP.Contact}" accessyKey="F" class="crmbutton small create" onclick="this.form.action.value='EditView';this.form.module.value='Contacts'" type="submit" name="button" value="{$APP.LBL_ADD_NEW} {$APP.Contact}"></td>
				{/if}
			{elseif $HEADER eq 'Users' && $MODULE eq 'Calendar'}
				<input title="Change" accessKey="" tabindex="2" type="button" class="crmbutton small edit" value="{$APP.LBL_SELECT_USER_BUTTON_LABEL}" name="button" LANGUAGE=javascript onclick='return window.open("index.php?module=Users&return_module=Calendar&return_action={$return_modname}&activity_mode=Events&action=Popup&popuptype=detailview&form=EditView&form_submit=true&select=enable&return_id={$ID}&recordid={$ID}","test","width=640,height=525,resizable=0,scrollbars=0")';>
            {/if}
            
		</td>
	</tr>
</table>

<table border=0 cellspacing=1 cellpadding=3 width=100% style="background-color:#eaeaea;" class="small">
	<tr style="height:25px" bgcolor=white>
        {if $MODULE eq 'Campaigns' && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads' || $RELATED_MODULE eq 'Accounts')
			&& $RELATEDLISTDATA.entries|@count > 0}
		<td class="lvtCol">
			<input name ="{$RELATED_MODULE}_selectall" id="{$MODULE}_{$RELATED_MODULE}_selectCurrentPageRec" onclick="rel_toggleSelect(this.checked,'{$MODULE}_{$RELATED_MODULE}_selected_id','{$RELATED_MODULE}');"  type="checkbox">
		</td>
        {/if}
		{foreach key=index item=_HEADER_FIELD from=$RELATEDLISTDATA.header}
		<td class="lvtCol">{$_HEADER_FIELD}</td>
		{/foreach}
	</tr>
	{if $MODULE eq 'Campaigns'}
	<tr>
		<td id="{$MODULE}_{$RELATED_MODULE}_linkForSelectAll" class="linkForSelectAll" style="display:none;" colspan=10>
			<span id="{$MODULE}_{$RELATED_MODULE}_selectAllRec" class="selectall" style="display:inline;" onClick="rel_toggleSelectAll_Records('{$MODULE}','{$RELATED_MODULE}',true,'{$MODULE}_{$RELATED_MODULE}_selected_id')">{$APP.LBL_SELECT_ALL} <span id={$RELATED_MODULE}_count class="folder"> </span> {$APP.LBL_RECORDS_IN} {$RELATED_MODULE|@getTranslatedString:$RELATED_MODULE} {$APP.LBL_RELATED_TO_THIS} {$APP.SINGLE_Campaigns}</span>
			<span id="{$MODULE}_{$RELATED_MODULE}_deSelectAllRec" class="selectall" style="display:none;" onClick="rel_toggleSelectAll_Records('{$MODULE}','{$RELATED_MODULE}',false,'{$MODULE}_{$RELATED_MODULE}_selected_id')">{$APP.LBL_DESELECT_ALL} {$RELATED_MODULE|@getTranslatedString:$RELATED_MODULE} {$APP.LBL_RELATED_TO_THIS} {$APP.SINGLE_Campaigns}</span>
		</td>
	</tr>
	{/if}
	{foreach key=_RECORD_ID item=_RECORD from=$RELATEDLISTDATA.entries}
		<tr bgcolor=white>
        	{if $MODULE eq 'Campaigns' && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads' || $RELATED_MODULE eq 'Accounts')}
			<td><input name="{$MODULE}_{$RELATED_MODULE}_selected_id" id="{$_RECORD_ID}" value="{$_RECORD_ID}" onclick="rel_check_object(this,'{$RELATED_MODULE}');" type="checkbox"  {$RELATEDLISTDATA.checked.$_RECORD_ID}></td>
        	{/if}
			{foreach key=index item=_RECORD_DATA from=$_RECORD}
				 {* vtlib customization: Trigger events on listview cell *}
                 <td onmouseover="vtlib_listview.trigger('cell.onmouseover', $(this))" onmouseout="vtlib_listview.trigger('cell.onmouseout', $(this))">{$_RECORD_DATA}</td>
                 {* END *}
			{/foreach}
		</tr>
	{foreachelse}
		<tr style="height: 25px;" bgcolor="white"><td><i>{$APP.LBL_NONE_INCLUDED}</i></td></tr>
	{/foreach}
</table>
{if $MODULE eq 'Campaigns' && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads' || $RELATED_MODULE eq 'Accounts')
			&& $RELATEDLISTDATA.entries|@count > 0 && $RESET_COOKIE eq 'true'}
			<script type='text/javascript'>set_cookie('{$RELATED_MODULE}_all', '');</script>
{/if}