{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}

<table class="listTable" width="100%" border="0" cellspacing="1" cellpadding="5" id='expressionlist'>
	<tr>
		<td class="colHeader small" width="70%">
			{$MOD.LBL_TASK}
		</td>
		<td class="colHeader small" width="15%">
			{$MOD.LBL_STATUS}
		</td>
		<td class="colHeader small" width="15%">
			{$MOD.LBL_LIST_TOOLS}
		</td>
	</tr>
	{foreach item=task from=$tasks}
	<tr>
		<td class="listTableRow small">{$task->summary|@to_html}</td>
		<td class="listTableRow small">{if $task->active}Active{else}Inactive{/if}</td>
		<td class="listTableRow small">
			<a href="{$module->editTaskUrl($task->id)}">
				<img border="0" title="{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE}" alt="{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE}" \
					style="cursor: pointer;" id="expressionlist_editlink_{$task->id}" \
					src="{'editfield.gif'|@vtiger_imageurl:$THEME}"/>
			</a>
			<a href="{$module->deleteTaskUrl($task->id)}" onclick="return confirm('{$APP.SURE_TO_DELETE}');">
				<img border="0" title="{'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE}" alt="{'LBL_DELETE_BUTTON'|@getTranslatedString:$MODULE}"\
					src="{'delete.gif'|@vtiger_imageurl:$THEME}" \
					style="cursor: pointer;" id="expressionlist_deletelink_{$task->id}"/>
			</a>
		</td>
	</tr>
	{/foreach}
</table>