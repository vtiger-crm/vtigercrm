<table width="100%" cellspacing="0" cellpadding="5" border="0" class="settingsSelUITopLine"><tbody>
	<tr>
		<td width="50" valign="top" rowspan="2">
			<img width="48" height="48" border="0" src="{'settingsWorkflow.png'|@vtiger_imageurl:$THEME}"/>
		</td>
		<td valign="bottom" class="heading2">
			<b><a href="index.php?module=Settings&amp;action=index&amp;parenttab=Settings">{'Settings'|@getTranslatedString:$MODULE}</a> >
				<a href="index.php?module={$module->name}&amp;action=workflowlist&amp;parenttab=Settings">{$MODULE_NAME|@getTranslatedString:$MODULE_NAME}</a> > {$PAGE_NAME} </b>
		</td>
	</tr>
	<tr>
		<td valign="top" class="small">{$PAGE_TITLE}</td>
		{if $CRON_TASK neq ''}
		<td  align="right" class="small" width='40%'>
			<b>
			{if $CRON_TASK->isDisabled() }{'LBL_DISABLED'|@getTranslatedString:$MODULE}{/if}
			{if $CRON_TASK->isRunning() }{'LBL_RUNNING'|@getTranslatedString:$MODULE}{/if}
			{if $CRON_TASK->isEnabled()}
				{if $CRON_TASK->hadTimedout}
					{'LBL_LAST_SCAN_TIMED_OUT'|@getTranslatedString:$MODULE}.
				{elseif $CRON_TASK->getLastEndDateTime() neq ''}
					{'LBL_LAST_SCAN_AT'|@getTranslatedString:$MODULE}
					{$CRON_TASK->getLastEndDateTime()}
					&
					{'LBL_TIME_TAKEN'|@getTranslatedString:$MODULE}:
					{$CRON_TASK->getTimeDiff()}
					{'LBL_SHORT_SECONDS'|@getTranslatedString:$MODULE}
				{else}
				{/if}
			{/if}
			</b>
		</td>
		{/if}
	</tr>
</tbody></table>
<br>
