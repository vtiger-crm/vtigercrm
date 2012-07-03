{*<!--
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
-->*}
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>

<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" height='530' width="100%">
	<tr valign='top'>
		<td colspan="1">
			<span class="genHeaderGray">{'LBL_SCHEDULE_EMAIL'|@getTranslatedString:'Reports'}</span>
			<br>
			{'LBL_SCHEDULE_EMAIL_DESCRIPTION'|@getTranslatedString:'Reports'}
			<hr>
		</td>
	</tr>
	<tr valign="top">
		<td>
			<div style="height:448px">
				<table class="small" border="0" cellpadding="5" cellspacing="1" width="100%" valign="top">
					<tr class="small" valign="top">						
						<td width="5%" class="detailedViewHeader" align="center">
							<input type="checkbox" name="isReportScheduled" id="isReportScheduled"
							{if $IS_SCHEDULED eq 'true'} checked {/if}
							>
						</td>
						<td width="90%" class="detailedViewHeader" class="cellText"><strong>{'LBL_SCHEDULE_REPORT'|@getTranslatedString:'Reports'}</strong></td>
					</tr>
					<tr valign="top">
						<td colspan="2">
							<table width="100%" class="small" border="0" cellpadding="5" cellspacing="0" align="top">
								<tr align="left" valign="top">
									<td class="cellBottomDotLinePlain small"><strong>{'LBL_SCHEDULE_FREQUENCY'|@getTranslatedString:'Reports'}</strong></td>
								</tr>
								<tr align="left" valign="top">
									<td valign=top class="small">
										<span id="scheduledTypeSpan">
											<select class="dataInput small" name="scheduledType" id="scheduledType" onchange="javascript: setScheduleOptions();">
												<!-- Hourly doesn't make sense on OD as the cron job is running once in 2 hours -->
												<!-- option id="schtype_1" value="1" {if $schtypeid eq 1}selected{/if}>{'Hourly'|@getTranslatedString:'Reports'}</option -->
												<option id="schtype_2" value="2" {if $schtypeid eq 2}selected{/if}>{'Daily'|@getTranslatedString:'Reports'}</option>
												<option id="schtype_3" value="3" {if $schtypeid eq 3}selected{/if}>{'Weekly'|@getTranslatedString:'Reports'}</option>
												<option id="schtype_4" value="4" {if $schtypeid eq 4}selected{/if}>{'BiWeekly'|@getTranslatedString:'Reports'}</option>
												<option id="schtype_5" value="5" {if $schtypeid eq 5}selected{/if}>{'Monthly'|@getTranslatedString:'Reports'}</option>
												<option id="schtype_6" value="6" {if $schtypeid eq 6}selected{/if}>{'Annually'|@getTranslatedString:'Reports'}</option>
											</select>
										</span>
										<span id="scheduledMonthSpan" style="display: {if $schtypeid eq 6}inline{else}none{/if};">&nbsp;<strong>{'LBL_SCHEDULE_EMAIL_MONTH'|@getTranslatedString:'Reports'}</strong>
											<select class="dataInput small" name="scheduledMonth" id="scheduledMonth">
												{assign var="MONTH_STRINGS" value='MONTH_STRINGS'|@getTranslatedString:'Reports'}
												{foreach key=mid item=month from=$MONTH_STRINGS}
												<option value="{$mid}" {if $schmonth eq $mid}selected{/if}>{$month}</option>
												{/foreach}
											</select>
										</span>

										<!-- day of month (monthly, annually) -->
										<span id="scheduledDOMSpan" style="display: {if $schtypeid eq 5 || $schtypeid eq 6}inline{else}none{/if};">&nbsp;<strong>{'LBL_SCHEDULE_EMAIL_DAY'|@getTranslatedString:'Reports'}</strong>:
											<select class="dataInput small" name="scheduledDOM" id="scheduledDOM">
												{section name=day start=1 loop=32}
												<option value="{$smarty.section.day.iteration}" {if $schday eq $smarty.section.day.iteration}selected{/if}>{$smarty.section.day.iteration}</option>
												{/section}
											</select>
										</span>

										<!-- day of week (weekly/bi-weekly) -->
										<span id="scheduledDOWSpan" style="display: {if $schtypeid eq 3 || $schtypeid eq 4}inline{else}none{/if};">&nbsp;<strong>{'LBL_SCHEDULE_EMAIL_DOW'|@getTranslatedString:'Reports'}</strong>:
											<select class="dataInput small" name="scheduledDOW" id="scheduledDOW">
												{assign var="WEEKDAY_STRINGS" value='WEEKDAY_STRINGS'|@getTranslatedString:'Reports'}
												{foreach key=wid item=week from=$WEEKDAY_STRINGS}
												<option value="{$wid}" {if $schweek eq $wid}selected{/if}>{$week}</option>
												{/foreach}
											</select>
										</span>

										<!-- time (daily, weekly, bi-weekly, monthly, annully) -->
										<span id="scheduledTimeSpan" style="display: {if $schtypeid > 1}inline{else}none{/if};">&nbsp;<strong>{'LBL_SCHEDULE_EMAIL_TIME'|@getTranslatedString:'Reports'}</strong>:
											<input class="dataInput small" type="text" name="scheduledTime" id="scheduledTime" value="{$schtime}" size="5" maxlength="5" /> {'LBL_TIME_FORMAT_MSG'|@getTranslatedString:'Reports'}
										</span>

										<input type="hidden" name="scheduledIntervalString" value="" />
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr align="left" valign="top">
									<td class="cellBottomDotLinePlain small"><strong>{'LBL_REPORT_FORMAT'|@getTranslatedString:'Reports'}</strong>:</td>
								</tr>
								<tr align="left" valign="top">
									<td valign=top class="small">
										{'LBL_SELECT'|@getTranslatedString:'Reports'}:&nbsp;
										<select id="scheduledReportFormat" name="scheduledReportFormat" class="small">
											<option value="pdf" {if $REPORT_FORMAT eq 'pdf'} selected {/if}>{'LBL_REPORT_FORMAT_PDF'|@getTranslatedString:'Reports'}</option>
											<option value="excel" {if $REPORT_FORMAT eq 'excel'} selected {/if}>{'LBL_REPORT_FORMAT_EXCEL'|@getTranslatedString:'Reports'}</option>
											<option value="both" {if $REPORT_FORMAT eq 'both'} selected {/if}>{'LBL_REPORT_FORMAT_BOTH'|@getTranslatedString:'Reports'}</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr align="left" valign="top">
									<td valign=top class="cellBottomDotLinePlain small"><strong>{'LBL_USERS_AVAILABEL'|@getTranslatedString:'Reports'}</strong></td>
								</tr>
								<tr align="left" valign="top">
									<td>
										<table>
											<tr>
												<td width="45%" valign=top class="small">
													{'LBL_SELECT'|@getTranslatedString:'Reports'}:&nbsp;
													<select id="recipient_type" name="recipient_type" class="small" onChange="showRecipientsOptions()">
														<option value="users">{'LBL_USERS'|@getTranslatedString:'Reports'}</option>
														<option value="groups">{'LBL_GROUPS'|@getTranslatedString:'Reports'}</option>
														<option value="roles">{'LBL_ROLES'|@getTranslatedString:'Reports'}</option>
														<option value="rs">{'LBL_ROLES_SUBORDINATES'|@getTranslatedString:'Reports'}</option>
													</select>
													<input type="hidden" name="findStr1" class="small">&nbsp;
												</td>
												<td width="10%">&nbsp;</td>
												<td width="45%" class="small"><strong>{'LBL_USERS_SELECTED'|@getTranslatedString:'Reports'}</strong></td>
											</tr>

											<tr class=small>
												<td valign=top><br>
													<div id="availableRecipientsWrapper">
													</div>
												</td>
												<td width="50">
													<div align="center">
														<input type="button" name="addButton" value="&nbsp;&rsaquo;&rsaquo;&nbsp;" onClick="addOption()" class="crmButton small"/><br /><br />
														<input type="button" name="delButton" value="&nbsp;&lsaquo;&lsaquo;&nbsp;" onClick="delOption()" class="crmButton small"/>
													</div>
												</td>
												<td class="small" valign=top><br>
													<select id="selectedRecipients" name="selectedRecipients" multiple size="10" class="small crmFormList">
													{$SELECTED_RECIPIENTS}
													</select>
													<input type="hidden" name="selectedRecipientsString"/>
												</td>
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
	</tr>
</table>

<script language="JavaScript" type="text/JavaScript">

function showRecipientsOptions()
{ldelim}
	var option;
	var selectedOption=document.NewReport.recipient_type;

	for(var i=0; i<selectedOption.options.length; i++) {ldelim}
		if (selectedOption.options[i].selected==true) {ldelim}
			option=selectedOption.value;
			break;
		{rdelim}
	{rdelim}

	var availableRecipientsWrapper = document.getElementById('availableRecipientsWrapper');

	if(option == 'users') {ldelim}
		availableRecipientsWrapper.innerHTML = '{$AVAILABLE_USERS}';
	{rdelim} else if(option == 'roles') {ldelim}
		availableRecipientsWrapper.innerHTML = '{$AVAILABLE_ROLES}';
	{rdelim} else if(option == 'rs') {ldelim}
		availableRecipientsWrapper.innerHTML = '{$AVAILABLE_ROLESANDSUB}';
	{rdelim} else if(option == 'groups') {ldelim}
		availableRecipientsWrapper.innerHTML = '{$AVAILABLE_GROUPS}';
	{rdelim}
{rdelim}

function addOption() {ldelim}

	var availableRecipientsObj=getObj("availableRecipients");
	var selectedRecipientsObj=getObj("selectedRecipients");
	
	for (i=0;i<selectedRecipientsObj.length;i++) {ldelim}
		selectedRecipientsObj.options[i].selected=false
	{rdelim}

	for (i=0;i<availableRecipientsObj.length;i++) {ldelim}

		if (availableRecipientsObj.options[i].selected==true) {ldelim}
			var rowFound=false;
			var existingObj=null;
			for (j=0;j<selectedRecipientsObj.length;j++) {ldelim}
				if (selectedRecipientsObj.options[j].value==availableRecipientsObj.options[i].value)
				{ldelim}
					rowFound=true
					existingObj=selectedRecipientsObj.options[j]
					break
				{rdelim}
			{rdelim}

			if (rowFound!=true) {ldelim}
				var newColObj=document.createElement("OPTION")
				newColObj.value=availableRecipientsObj.options[i].value
				if (browser_ie) newColObj.innerText=availableRecipientsObj.options[i].innerText
				else if (browser_nn4 || browser_nn6) newColObj.text=availableRecipientsObj.options[i].text
				selectedRecipientsObj.appendChild(newColObj)
				availableRecipientsObj.options[i].selected=false
				newColObj.selected=true
				rowFound=false
			{rdelim}
			else {ldelim}
				if(existingObj != null) existingObj.selected=true
			{rdelim}
		{rdelim}
	{rdelim}
{rdelim}

function delOption() {ldelim}
	var selectedRecipientsObj=getObj("selectedRecipients");
	for (var i=selectedRecipientsObj.options.length; i>0; i--) {ldelim}
			if (selectedRecipientsObj.options.selectedIndex>=0)
				selectedRecipientsObj.remove(selectedRecipientsObj.options.selectedIndex)
	{rdelim}
{rdelim}

showRecipientsOptions();
setScheduleOptions();
</script>
