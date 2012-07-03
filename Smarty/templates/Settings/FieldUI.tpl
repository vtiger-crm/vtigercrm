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
{if $_FIELD_UI_TYPE eq 56}
<input id="{$_FIELD_ELEMENT_ID}" name="{$_FIELD_ELEMENT_ID}" type="checkbox" class="small" {if $_FIELD_SELECTED_VALUE eq 1}checked{/if}>
{elseif $_FIELD_UI_TYPE eq 23 || $_FIELD_UI_TYPE eq 5 || $_FIELD_UI_TYPE eq 6}
<input class="small" id="{$_FIELD_ELEMENT_ID}" name="{$_FIELD_ELEMENT_ID}" type="text" size="11" maxlength="10" value="{if $_FIELD_SELECTED_VALUE neq 0}{$_FIELD_SELECTED_VALUE}{/if}">
<img align="absmiddle" src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_{$_FIELD_ELEMENT_ID}" />
<script type="text/javascript" id='layouteditor_{$_FIELD_ELEMENT_ID}' class='layouteditor_javascript'>
	Calendar.setup ({ldelim}
		inputField : "{$_FIELD_ELEMENT_ID}", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_{$_FIELD_ELEMENT_ID}", singleClick : true, step : 1
	{rdelim})
</script>
{elseif $_FIELD_UI_TYPE eq 15 || $_FIELD_UI_TYPE eq 16 || $_FIELD_UI_TYPE eq 33}
<select id="{$_FIELD_ELEMENT_ID}" name="{$_FIELD_ELEMENT_ID}" class="small">
	{foreach item=_PICKLIST_VALUE from=$_ALL_AVAILABLE_VALUES}
	<option value="{$_PICKLIST_VALUE}"
		{if $_PICKLIST_VALUE eq $_FIELD_SELECTED_VALUE}
		selected
		{/if}
	>{$_PICKLIST_VALUE|@getTranslatedString:$MODULE}
	</option>
	{foreachelse}
	<option value=""></option>
	<option value="" style='color: #777777' disabled>{$APP.LBL_NONE}</option>
	{/foreach}
</select>
{else}
<input id="{$_FIELD_ELEMENT_ID}" name="{$_FIELD_ELEMENT_ID}" type="text" class="detailedViewTextBox" value="{$_FIELD_SELECTED_VALUE}" />
{/if}