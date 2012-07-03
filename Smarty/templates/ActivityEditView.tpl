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

{*<!-- module header -->*}

<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$CALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="modules/{$MODULE}/Calendar.js"></script>
<script type="text/javascript">
var gVTModule = '{$smarty.request.module|@vtlib_purify}';
</script>
<script type="text/javascript" src="include/js/FieldDependencies.js"></script>
<script type="text/javascript" src="modules/com_vtiger_workflow/resources/jquery-1.2.6.js"></script>
<script type="text/javascript">
	jQuery.noConflict();
</script>
{if $PICKIST_DEPENDENCY_DATASOURCE neq ''}
<script type="text/javascript">
	jQuery(document).ready(function() {ldelim} (new FieldDependencies({$PICKIST_DEPENDENCY_DATASOURCE})).init() {rdelim});
</script>
{/if}

{*<!-- Contents -->*}
<form name="EditView" method="POST" action="index.php"
	{if $ACTIVITY_MODE neq 'Task'} onsubmit="if(check_form()){ldelim} VtigerJS_DialogBox.block(); {rdelim} else {ldelim} return false; {rdelim}"
	{else} onsubmit="maintask_check_form();if(formValidate()) {ldelim} VtigerJS_DialogBox.block(); {rdelim} else {ldelim} return false; {rdelim}" {/if} >
<input type="hidden" name="time_start" id="time_start">
<input type="hidden" name="view" value="{$view}">
<input type="hidden" name="hour" value="{$hour}">
<input type="hidden" name="day" value="{$day}">
<input type="hidden" name="month" value="{$month}">
<input type="hidden" name="year" value="{$year}">
<input type="hidden" name="viewOption" value="{$viewOption}">
<input type="hidden" name="subtab" value="{$subtab}">
<input type="hidden" name="maintab" value="{$maintab}">
<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
        <td>
                <table cellpadding="0" cellspacing="5" border="0">
			{include file='EditViewHidden.tpl'}
                </table>
<table  border="0" cellpadding="5" cellspacing="0" width="100%" >
<tr>
        <td class="lvtHeaderText" style="border-bottom:1px dotted #cccccc">

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr><td>

				{if $OP_MODE eq 'edit_view'}
					<span class="lvtHeaderText"><font color="purple">[ {$ID} ] </font>{$NAME} - {$APP.LBL_EDITING} {$SINGLE_MOD} {$APP.LBL_INFORMATION}</span> <br>
					<span class="small">{$UPDATEINFO}	 </span>
				{/if}
				{if $OP_MODE eq 'create_view'}
					{if $DUPLICATE neq 'true'}
					<span class="lvtHeaderText">{$APP.LBL_CREATING} {$SINGLE_MOD}</span> <br>
					{else}
					<span class="lvtHeaderText">{$APP.LBL_DUPLICATING} "{$NAME}"</span> <br>
					{/if}
				{/if}
			</td></tr>
		</table>
        </td>
</tr>

<tr><td>
<table border="0" cellpadding="5" cellspacing="0" width="100%">
        <tr>
                <td valign=top align=left >
                           <table border=0 cellspacing=0 cellpadding=0 width=100%>
                                <tr>
					<td align=left>
					<!-- content cache -->

					<table border=0 cellspacing=0 cellpadding=0 width=100%>
					  <tr>
					     <td style="padding:10px">
						     <!-- General details -->
						     <table border=0 cellspacing=0 cellpadding=0 width=100% >
						     <tr>
							<td  colspan=4 style="padding:5px">
								<div align="center">
								<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.action.value='Save';"  type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
								<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="window.history.back()" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
								</div>
							</td>
						     </tr>
						     </table>
						     <!-- included to handle the edit fields based on ui types -->
						     {foreach key=header item=data from=$BLOCKS}
							     {if $header neq $APP.LBL_CUSTOM_INFORMATION}
						     <table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
						     <tr>
							<td colspan=4 class="tableHeading">
								<b>{$header}</b>
							</td>
						     </tr>
						     </table>
							     {/if}
						     {/foreach}
						     {if $ACTIVITY_MODE neq 'Task'}
							<input type="hidden" name="time_end" id="time_end">
							<input type="hidden" name="followup_due_date" id="followup_due_date">
							<input type="hidden" name="followup_time_start" id="followup_time_start">
                                                        <input type="hidden" name="followup_time_end" id="followup_time_end">
							<input type=hidden name="inviteesid" id="inviteesid" value="">
							<input type="hidden" name="duration_hours" value="0">
							<input type="hidden" name="duration_minutes" value="0">
							<input type="hidden" name="dateformat" value="{$DATEFORMAT}">
						     <table border=0 cellspacing=0 cellpadding=5 width=100% >
							{if $LABEL.activitytype neq ''}
							<tr>
								<td class="cellLabel" nowrap  width=20% align="right"><b>{$MOD.LBL_EVENTTYPE}</b></td>
								<td class="cellInfo" width=80% align="left">
									<table>
										<tr>
<!--										{foreach key=tyeparrkey item=typearr from=$ACTIVITYDATA.activitytype}
                                                                                {if $typearr[2] eq 'selected' && $typearr[1] eq 'Call'}
                                                                                        {assign var='meetcheck' value=''}
                                                                                        {assign var='callcheck' value='checked'}
                                                                                {elseif $typearr[2] eq 'selected' && $typearr[1] eq 'Meeting'}
                                                                                        {assign var='meetcheck' value='checked'}
                                                                                        {assign var='callcheck' value=''}
                                                                                {else}
																						{assign var='meetcheck' value=''}
                                                                                        {assign var='callcheck' value='checked'}
                                                                                {/if}
                                        {/foreach}-->
	                                    <select name="activitytype" class="small">
											{foreach item=arr from=$ACTIVITYDATA.activitytype}
												{if $arr[0] eq $APP.LBL_NOT_ACCESSIBLE}
												<option value="{$arr[0]}" {$arr[2]}>
													{$arr[0]}
												</option>
												{else}
												<option value="{$arr[1]}" {$arr[2]}>
							                                                {$arr[0]}
							                                        </option>
												{/if}
											{/foreach}
									   </select>
										</tr>
									</table>
								</td>
							</tr>
							{/if}
							<tr>
								<td class="cellLabel" nowrap align="right"><b><font color="red">{$TYPEOFDATA.subject}</font>{$MOD.LBL_EVENTNAME}</b></td>
								<td class="cellInfo" align="left"><input name="subject" type="text" class="textbox" value="{$ACTIVITYDATA.subject}" style="width:50%">&nbsp;&nbsp;&nbsp;
								{if $LABEL.visibility neq ''}
								{foreach key=key_one item=arr from=$ACTIVITYDATA.visibility}
                                                                        {if $arr[1] eq 'Public' && $arr[2] eq 'selected'}
                                                                                {assign var="visiblecheck" value="checked"}
                                                                        {else}
                                                                                {assign var="visiblecheck" value=""}
                                                                        {/if}
                                                                        {/foreach}
                                                                        <input name="visibility" value="Public" type="checkbox" {$visiblecheck}>{$MOD.LBL_PUBLIC}
								{/if}
								</td>
							</tr>
							{if $LABEL.description neq ''}
							<tr>
                        					<td class="cellLabel" valign="top" nowrap align="right"><b><font color="red">{$TYPEOFDATA.description}</font>{$LABEL.description}</b></td>
								<td class="cellInfo" align="left"><textarea style="width:100%; height : 60px;" name="description">{$ACTIVITYDATA.description}</textarea></td>
                					</tr>
							{/if}
							{if $LABEL.location neq ''}
							<tr>
			                     <td class="cellLabel" align="right" valign="top"><b><font color="red">{$TYPEOFDATA.location}</font>{$MOD.LBL_APP_LOCATION}</b></td>
								<td class="cellInfo" align="left"><input name="location" type="text" class="textbox" value="{$ACTIVITYDATA.location}" style="width:50%">
							</tr>
							{/if}

							<tr>
								<td colspan=2 width=80% align="center">
								<table border=0 cellspacing=0 cellpadding=3 width=80%>
									<tr>
										 <td >{if $LABEL.eventstatus neq ''}<b><font color="red">{$TYPEOFDATA.eventstatus}</font>{$LABEL.eventstatus}</b>{/if}</td>
                                                                                <td >{if $LABEL.assigned_user_id != ''}<b>
											{$LABEL.assigned_user_id}</b>
											{/if}</td>
									</tr>
									<tr>
										<td valign=top>
										{if $LABEL.eventstatus neq ''}
                                                                                <select name="eventstatus" id="eventstatus" class=small onChange = "getSelectedStatus();" >
                                                                                        {foreach item=arr from=$ACTIVITYDATA.eventstatus}
											 {if $arr[0] eq $APP.LBL_NOT_ACCESSIBLE}
                                                                                       		 <option value="{$arr[0]}" {$arr[2]}>{$arr[0]}</option>
                                                                                        {else}
                                                                                                <option value="{$arr[1]}" {$arr[2]}>
                                                                                                        {$arr[0]}
                                                                                                </option>
                                                                                        {/if}
                                                                                        {/foreach}
                                                                                </select>
										{/if}
                                                                        	</td>
										<td valign=top rowspan=2>
											{if $ACTIVITYDATA.assigned_user_id != ''}
											{assign var=check value=1}
                                        						{foreach key=key_one item=arr from=$ACTIVITYDATA.assigned_user_id}
                                                					{foreach key=sel_value item=value from=$arr}
                                                        					{if $value ne ''}
                                                                					{assign var=check value=$check*0}
                                                        					{else}
                                                                					{assign var=check value=$check*1}
                                                        					{/if}
                                                					{/foreach}
                                        						{/foreach}

                                        						{if $check eq 0}
                                                						{assign var=select_user value='checked'}
                                                						{assign var=style_user value='display:block'}
                                                						{assign var=style_group value='display:none'}
                                        						{else}
                                                						{assign var=select_group value='checked'}
                                                						{assign var=style_user value='display:none'}
                                                						{assign var=style_group value='display:block'}
                                        						{/if}
                                        						<input type="radio" name="assigntype" {$select_user} value="U" onclick="toggleAssignType(this.value)">&nbsp;{$APP.LBL_USER}
                                        						{if $secondvalue.assigned_user_id neq ''}
                                                					<input type="radio" name="assigntype" {$select_group} value="T" onclick="toggleAssignType(this.value)">&nbsp;{$APP.LBL_GROUP}
                                        						{/if}
											<span id="assign_user" style="{$style_user}">
                                     				           			<select name="assigned_user_id" class="small">
                                                        					{foreach key=key_one item=arr from=$ACTIVITYDATA.assigned_user_id}
                                                                				{foreach key=sel_value item=value from=$arr}
                                                                        				<option value="{$key_one}" {$value}>{$sel_value}</option>
                                                                				{/foreach}
                                                        					{/foreach}
                                                			   			</select>
                                        			       			</span>

                                        						{if $secondvalue.assigned_user_id neq ''}
                                                					<span id="assign_team" style="{$style_group}">
                                                        					<select name="assigned_group_id" class="small">';
                                                                				{foreach key=key_one item=arr from=$secondvalue.assigned_user_id}
                                                                        			{foreach key=sel_value item=value from=$arr}
                                                                                			<option value="{$key_one}" {$value}>{$sel_value}</option>
                                                                        			{/foreach}
                                                                				{/foreach}
                                                        					</select>
                                                					</span>
                                        						{/if}
											{else}
											<input name="assigned_user_id" value="{$CURRENTUSERID}" type="hidden">
											{/if}
											<br>{if $LABEL.sendnotification neq ''}
												{if $ACTIVITYDATA.sendnotification eq 1}

												<input type="checkbox" name="sendnotification" checked>&nbsp;{$LABEL.sendnotification}
												{else}
												<input type="checkbox" name="sendnotification" >&nbsp;{$LABEL.sendnotification}
												{/if}
											{/if}
										</td>
									</tr>
									{if $LABEL.taskpriority neq ''}
									<tr>
										<td valign=top><b>{$LABEL.taskpriority}</b>
										<br>
										<select name="taskpriority" id="taskpriority">
                                                                                        {foreach item=arr from=$ACTIVITYDATA.taskpriority}
											 {if $arr[0] eq $APP.LBL_NOT_ACCESSIBLE}
                                                                                        <option value="{$arr[0]}" {$arr[2]}>{$arr[0]}</option>
                                                                                        {else}
                                                                                                <option value="{$arr[1]}" {$arr[2]}>
                                                                                                        {$arr[0]}
                                                                                                </option>
                                                                                        {/if}
                                                                                        {/foreach}
                                                                                </select>
										</td>

									</tr>
									{/if}
								</table>
							</td></tr>
						     </table>
						     <hr noshade size=1>
						     <table border=0 id="date_table" cellspacing=0 cellpadding=5 width=100% align=center bgcolor="#FFFFFF">
							<tr>
								<td >
									<table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
									<tr><td width=50% id="date_table_firsttd" valign=top style="border-right:1px solid #dddddd">
										<table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
											<tr><td colspan=3 ><b>{$MOD.LBL_EVENTSTAT}</b></td></tr>
											<tr><td colspan=3>{$STARTHOUR}</td></tr>
											<tr><td>
												{foreach key=date_value item=time_value from=$ACTIVITYDATA.date_start}
                                                                                                        {assign var=date_val value="$date_value"}
                                                                                                        {assign var=time_val value="$time_value"}
	                                                                                        {/foreach}
                                                                                                <input type="text" name="date_start" id="jscal_field_date_start" class="textbox" style="width:90px" onChange="dochange('jscal_field_date_start','jscal_field_due_date');" value="{$date_val}"></td><td width=100%><img border=0 src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$MOD.LBL_SET_DATE}" title="{$MOD.LBL_SET_DATE}" id="jscal_trigger_date_start">
													{foreach key=date_fmt item=date_str from=$secondvalue.date_start}
													{assign var=date_vl value="$date_fmt"}
													{/foreach}
													<script type="text/javascript">
														Calendar.setup ({ldelim}
														inputField : "jscal_field_date_start", ifFormat : "{$date_vl}", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1
														{rdelim})
													</script>
											</td></tr>
										</table></td>
										<td width=50% valign=top id="date_table_secondtd">
											<table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
												<tr><td colspan=3><b>{$MOD.LBL_EVENTEDAT}</b></td></tr>
												<tr><td colspan=3>{$ENDHOUR}
												</td></tr>
												<tr><td>
													{foreach key=date_value item=time_value from=$ACTIVITYDATA.due_date}
													{assign var=date_val value="$date_value"}
													{assign var=time_val value="$time_value"}
													{/foreach}
													<input type="text" name="due_date" id="jscal_field_due_date" class="textbox" style="width:90px" value="{$date_val}"></td><td width=100%><img border=0 src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$MOD.LBL_SET_DATE}" title="{$MOD.LBL_SET_DATE}" id="jscal_trigger_due_date">
													{foreach key=date_fmt item=date_str from=$secondvalue.due_date}
													{assign var=date_vl value="$date_fmt"}
                                                                                                        {/foreach}
													<script type="text/javascript">
														Calendar.setup ({ldelim}
														inputField : "jscal_field_due_date", ifFormat : "{$date_vl}", showsTime : false, button : "jscal_trigger_due_date", singleClick : true, step : 1
														{rdelim})
													</script>
												</td></tr>
											</table>
										</td>
										<td width=33% valign=top style="display:none;border-left:1px solid #dddddd" id="date_table_thirdtd">
                                                                                        <table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
                                                                                                <tr><td colspan=3><b><input type="checkbox" name="followup"><b>{$MOD.LBL_HOLDFOLLOWUP}</b></td></tr>
                                                                                                <tr><td colspan=3>{$FOLLOWUP}</td></tr>
                                                                                                <tr><td>
                                                                                                        {foreach key=date_value item=time_value from=$ACTIVITYDATA.due_date}
                                                                                                        {assign var=date_val value="$date_value"}
                                                                                                        {assign var=time_val value="$time_value"}
                                                                                                        {/foreach}
                                                                                                        <input type="text" name="followup_date" id="jscal_field_followup_date" class="textbox" style="width:90px" value="{$date_val}"></td><td width=100%><img border=0 src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$MOD.LBL_SET_DATE}" title="{$MOD.LBL_SET_DATE}" id="jscal_trigger_followup_date">
                                                                                                        {foreach key=date_fmt item=date_str from=$secondvalue.due_date}
                                                                                                        {assign var=date_vl
 value="$date_fmt"}
                                                                                                        {/foreach}
													<script type="text/javascript">
                                                                                                        Calendar.setup ({ldelim}
                                                                                                                inputField : "jscal_field_followup_date", ifFormat : "{$date_vl}", showsTime : false, button : "jscal_trigger_followup_date", singleClick : true, step : 1
                                                                                                                {rdelim})
                                                                                                        </script>
                                                                                                </td></tr>
                                                                                        </table>
                                                                                </td>
									</tr>
								</table></td>
							</tr>
						     </table>

						     {if $CUSTOM_FIELDS_DATA|@count > 0}
	                             <table border=0 cellspacing=0 cellpadding=5 width=100% >
	                             	<tr>{strip}
							     		<td colspan=4 class="tableHeading">
										<b>{$APP.LBL_CUSTOM_INFORMATION}</b>
										</td>{/strip}
						          	</tr>
						          	<tr>
						          		{foreach key=index item=maindata from=$CUSTOM_FIELDS_DATA}
						          			{include file="EditViewUI.tpl"}
											{if ($index+1)% 2 == 0}
												</tr><tr>
											{/if}
							            {/foreach}
							        {if ($index+1)% 2 != 0}
							        	<td width="20%"></td><td width="30%"></td>
							        {/if}
						            </tr>
	                             </table>
                             {/if}
						     <br>
						     <table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
							<tr><td>
								<table border=0 cellspacing=0 cellpadding=3 width=100%>
									<tr>
										<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
										<td id="cellTabInvite" class="dvtSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');gshow('addEventInviteUI','',document.EditView.date_start.value,document.EditView.due_date.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value,document.EditView.endhr.value,document.EditView.endmin.value,document.EditView.endfmt.value);ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$MOD.LBL_INVITE}</a></td>
										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
										{if $LABEL.reminder_time neq ''}
										<td id="cellTabAlarm" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','on');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');gshow('addEventAlarmUI','',document.EditView.date_start.value,document.EditView.due_date.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value,document.EditView.endhr.value,document.EditView.endmin.value,document.EditView.endfmt.value);ghide('addEventInviteUI');ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$MOD.LBL_REMINDER}</a></td>
										{/if}
										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
										{if $LABEL.recurringtype neq ''}
										<td id="cellTabRepeat" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','on');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');ghide('addEventInviteUI');gshow('addEventRepeatUI','',document.EditView.date_start.value,document.EditView.due_date.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value,document.EditView.endhr.value,document.EditView.endmin.value,document.EditView.endfmt.value);ghide('addEventRelatedtoUI');">{$MOD.LBL_REPEAT}</a></td>
										{/if}
										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
										<td id="cellTabRelatedto" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','on');ghide('addEventAlarmUI');ghide('addEventInviteUI');gshow('addEventRelatedtoUI','',document.EditView.date_start.value,document.EditView.due_date.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value,document.EditView.endhr.value,document.EditView.endmin.value,document.EditView.endfmt.value);ghide('addEventRepeatUI');">{$MOD.LBL_RELATEDTO}</a></td>
										<td class="dvtTabCache" style="width:100%">&nbsp;</td>
									</tr>
								</table>
							</td></tr>
							<tr>
								<td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px">
								<!-- Invite UI -->
									<DIV id="addEventInviteUI" style="display:block;width:100%">
									<table border=0 cellspacing=0 cellpadding=2 width=100%>
										<tr>
											<td valign=top>
												<table border=0 cellspacing=0 cellpadding=2 width=100%>
													<tr><td colspan=3>
														<ul style="padding-left:20px">
														<li>{$MOD.LBL_INVITE_INST1}
														<li>{$MOD.LBL_INVITE_INST2}
														</ul>
													</td></tr>
													<tr>
														<td><b>{$MOD.LBL_AVL_USERS}</b></td>
														<td>&nbsp;</td>
														<td><b>{$MOD.LBL_SEL_USERS}</b></td>
													</tr>
													<tr>
														<td width=40% align=center valign=top>
														<select name="availableusers" id="availableusers" class=small size=5 multiple style="height:70px;width:100%">
														{foreach item=username key=userid from=$USERSLIST}
														{if $userid != ''}
														<option value="{$userid}">{$username}</option>
														{/if}
														{/foreach}
														</select>
														</td>
														<td width=20% align=center valign=top>
														<input type=button value="{$MOD.LBL_ADD_BUTTON} >>" class="crm button small save" style="width:100%"  onClick="incUser('availableusers','selectedusers')"><br>
														<input type=button value="<< {$MOD.LBL_RMV_BUTTON} " class="crm button small cancel" style="width:100%" onClick="rmvUser('selectedusers')">
														</td>
														<td width=40% align=center valign=top>
														<select name="selectedusers" id="selectedusers" class=small size=5 multiple style="height:70px;width:100%">
														{foreach item=username key=userid from=$INVITEDUSERS}
														{if $userid != ''}
														<option value="{$userid}">{$username}</option>
                                                                                                                {/if}
                                                                                                                {/foreach}
														</select>
														<div align=left> {$MOD.LBL_SELUSR_INFO}
														</div>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									</DIV>
									<!-- Reminder UI -->
									<DIV id="addEventAlarmUI" style="display:none;width:100%">
									{if $LABEL.reminder_time neq ''}
										<table>
											{assign var=secondval value=$secondvalue.reminder_time}
											{assign var=check value=$secondval[0]}
											{assign var=yes_val value=$secondval[1]}
											{assign var=no_val value=$secondval[2]}

											<tr><td>{$LABEL.reminder_time}</td><td>

										{if $check eq 'CHECKED'}
											{assign var=reminstyle value='style="display:block;width:100%"'}
											<input type="radio" name="set_reminder" value="Yes" {$check} onClick="showBlock('reminderOptions')">&nbsp;{$yes_val}&nbsp;
											<input type="radio" name="set_reminder" value="No" onClick="fnhide('reminderOptions')">&nbsp;{$no_val}&nbsp;

										{else}
											{assign var=reminstyle value='style="display:none;width:100%"'}
											<input type="radio" name="set_reminder" value="Yes" onClick="showBlock('reminderOptions')">&nbsp;{$yes_val}&nbsp;
											<input type="radio" name="set_reminder" value="No" checked onClick="fnhide('reminderOptions')">&nbsp;{$no_val}&nbsp;

										{/if}
											</td></tr>
										</table>
										<DIV id="reminderOptions" {$reminstyle}>
											<table border=0 cellspacing=0 cellpadding=2  width=100%>
												<tr>
													<td nowrap align=right width=20% valign=top><b>{$MOD.LBL_RMD_ON} : </b></td>
													<td width=80%>
														<table border=0>
														<tr>
															<td colspan=2>
															{foreach item=val_arr from=$ACTIVITYDATA.reminder_time}
															{assign var=start value="$val_arr[0]"}
															{assign var=end value="$val_arr[1]"}
															{assign var=sendname value="$val_arr[2]"}
															{assign var=disp_text value="$val_arr[3]"}
															{assign var=sel_val value="$val_arr[4]"}
															<select name="{$sendname}">
															{section name=reminder start=$start max=$end loop=$end step=1 }
															{if $smarty.section.reminder.index eq $sel_val}
															<OPTION VALUE="{$smarty.section.reminder.index}" SELECTED>{$smarty.section.reminder.index}</OPTION>
															{else}
															<OPTION VALUE="{$smarty.section.reminder.index}" >{$smarty.section.reminder.index}</OPTION>
															{/if}
															<!--OPTION VALUE="{$smarty.section.reminder.index}" "{$sel_value}">{$smarty.section.reminder.index}</OPTION-->
															{/section}
															</select>
															&nbsp;{$disp_text}
															{/foreach}
															</td>
														</tr>
														</table>
													</td>
												</tr>
												<!--This is now required as of now, as we aree not allowing to change the email id
	                                        and it is showing logged in User's email id, instead of Assigned to user's email id

												<tr>
													<td nowrap align=right>
														{$MOD.LBL_SDRMD}
													</td>
													<td >
														<input type=text name="toemail" readonly="readonly" class=textbox style="width:90%" value="{$USEREMAILID}">
													</td>
												</tr> -->
											</table>
										</DIV>
									{/if}
									</DIV>
									<!-- Repeat UI -->
									<div id="addEventRepeatUI" style="display:none;width:100%">
									{if $LABEL.recurringtype neq ''}
									<table border=0 cellspacing=0 cellpadding=2  width=100%>
										<tr>
											<td nowrap align=right width=20% valign=top>
												<strong>{$MOD.LBL_REPEAT}</strong>
											</td>
											<td nowrap width=80% valign=top>
												<table border=0 cellspacing=0 cellpadding=0>
												<tr>

													<td width=20>
													{if $ACTIVITYDATA.recurringcheck eq 'Yes'}
														{assign var=rptstyle value='style="display:block"'}
														{if $ACTIVITYDATA.eventrecurringtype eq 'Daily'}
															{assign var=rptmonthstyle value='style="display:none"'}
															{assign var=rptweekstyle value='style="display:none"'}
														{elseif $ACTIVITYDATA.eventrecurringtype eq 'Weekly'}
															{assign var=rptmonthstyle value='style="display:none"'}
															{assign var=rptweekstyle value='style="display:block"'}
														{elseif $ACTIVITYDATA.eventrecurringtype eq 'Monthly'}
															{assign var=rptmonthstyle value='style="display:block"'}
															{assign var=rptweekstyle value='style="display:none"'}
														{elseif $ACTIVITYDATA.eventrecurringtype eq 'Yearly'}
															{assign var=rptmonthstyle value='style="display:none"'}
															{assign var=rptweekstyle value='style="display:none"'}
														{/if}
													<input type="checkbox" name="recurringcheck" onClick="showhide('repeatOptions')" checked>
													{else}
														{assign var=rptstyle value='style="display:none"'}
														{assign var=rptmonthstyle value='style="display:none"'}
														{assign var=rptweekstyle value='style="display:none"'}
													<input type="checkbox" name="recurringcheck" onClick="showhide('repeatOptions')">
													{/if}
													</td>
													<td>{$MOD.LBL_ENABLE_REPEAT}<td>
												</tr>
												<tr>
													<td colspan=2>
													<div id="repeatOptions" {$rptstyle}>
													<table border=0 cellspacing=0 cellpadding=2>
													<tr>
													<td>{$MOD.LBL_REPEAT_ONCE}</td>
													<td>
													<select name="repeat_frequency">
                                                                                                                {section name="repeat" loop=15 start=1 step=1}
                                                                                                                {if $smarty.section.repeat.iteration eq $ACTIVITYDATA.repeat_frequency}
                                                                                                                        {assign var="test" value="selected"}
                                                                                                                {else}                                                                                                                             {assign var="test" value=""}                                                                                                                                                                                                                  {/if}
                                                                                                                <option "{$test}" value="{$smarty.section.repeat.iteration}">{$smarty.section.repeat.iteration}</option>
                                                                                                                {/section}
                                                                                                        </select>
													</td>
													<td><select name="recurringtype" onChange="rptoptDisp(this)">
													<option value="Daily" {if $ACTIVITYDATA.eventrecurringtype eq 'Daily'} selected {/if}>{$MOD.LBL_DAYS}</option>
													<option value="Weekly" {if $ACTIVITYDATA.eventrecurringtype eq 'Weekly'} selected {/if}>{$MOD.LBL_WEEKS}</option>
												<option value="Monthly" {if $ACTIVITYDATA.eventrecurringtype eq 'Monthly'} selected {/if}>{$MOD.LBL_MONTHS}</option>
													<option value="Yearly" {if $ACTIVITYDATA.eventrecurringtype eq 'Yearly'} selected {/if}>{$MOD.LBL_YEAR}</option>
													</select>
													<!-- Repeat Feature Enhanced -->
													<b>{$MOD.LBL_UNTIL}</b> <input type="text" name="calendar_repeat_limit_date" id="calendar_repeat_limit_date" class="textbox" style="width:90px" value="" ></td><td align="left"><img border=0 src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$MOD.LBL_SET_DATE}..." title="{$MOD.LBL_SET_DATE}..." id="jscal_trigger_calendar_repeat_limit_date">
													{literal}
														<script type="text/javascript">
														Calendar.setup ({inputField : "calendar_repeat_limit_date", ifFormat : {/literal}
"{$REPEAT_LIMIT_DATEFORMAT}"
{literal}, showsTime : false, button : "jscal_trigger_calendar_repeat_limit_date", singleClick : true, step : 1})</script>
													{/literal}
													<!-- END -->
													</td>
												</tr>
												</table>
												<div id="repeatWeekUI" {$rptweekstyle}>
												<table border=0 cellspacing=0 cellpadding=2>
												<tr>
													<td><input name="sun_flag" value="sunday" {$ACTIVITYDATA.week0} type="checkbox"></td><td>{$MOD.LBL_SM_SUN}</td>
													<td><input name="mon_flag" value="monday" {$ACTIVITYDATA.week1} type="checkbox"></td><td>{$MOD.LBL_SM_MON}</td>
													<td><input name="tue_flag" value="tuesday" {$ACTIVITYDATA.week2} type="checkbox"></td><td>{$MOD.LBL_SM_TUE}</td>
													<td><input name="wed_flag" value="wednesday" {$ACTIVITYDATA.week3} type="checkbox"></td><td>{$MOD.LBL_SM_WED}</td>
													<td><input name="thu_flag" value="thursday" {$ACTIVITYDATA.week4} type="checkbox"></td><td>{$MOD.LBL_SM_THU}</td>
													<td><input name="fri_flag" value="friday" {$ACTIVITYDATA.week5} type="checkbox"></td><td>{$MOD.LBL_SM_FRI}</td>
													<td><input name="sat_flag" value="saturday" {$ACTIVITYDATA.week6} type="checkbox"></td><td>{$MOD.LBL_SM_SAT}</td>
												</tr>
												</table>
												</div>

												<div id="repeatMonthUI" {$rptmonthstyle}>
												<table border=0 cellspacing=0 cellpadding=2>
												<tr>
													<td>
														<table border=0 cellspacing=0 cellpadding=2>
														<tr>
														<td><input type="radio" checked name="repeatMonth" {if $ACTIVITYDATA.repeatMonth eq 'date'} checked {/if} value="date"></td><td>on</td><td><input type="text" class=textbox style="width:20px" value="{$ACTIVITYDATA.repeatMonth_date}" name="repeatMonth_date" ></td><td>day of the month</td>
														</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td>
														<table border=0 cellspacing=0 cellpadding=2>
														<tr><td>
														<input type="radio" name="repeatMonth" {if $ACTIVITYDATA.repeatMonth eq 'day'} checked {/if} value="day"></td>
														<td>on</td>
														<td>
														<select name="repeatMonth_daytype">
															<option value="first" {if $ACTIVITYDATA.repeatMonth_daytype eq 'first'} selected {/if}>First</option>
															<option value="last" {if $ACTIVITYDATA.repeatMonth_daytype eq 'last'} selected {/if}>Last</option>
														</select>
														</td>
														<td>
														<select name="repeatMonth_day">
															<option value=1 {if $ACTIVITYDATA.repeatMonth_day eq 1} selected {/if}>{$MOD.LBL_DAY1}</option>
															<option value=2 {if $ACTIVITYDATA.repeatMonth_day eq 2} selected {/if}>{$MOD.LBL_DAY2}</option>
															<option value=3 {if $ACTIVITYDATA.repeatMonth_day eq 3} selected {/if}>{$MOD.LBL_DAY3}</option>
															<option value=4 {if $ACTIVITYDATA.repeatMonth_day eq 4} selected {/if}>{$MOD.LBL_DAY4}</option>
															<option value=5 {if $ACTIVITYDATA.repeatMonth_day eq 5} selected {/if}>{$MOD.LBL_DAY5}</option>
															<option value=6 {if $ACTIVITYDATA.repeatMonth_day eq 6} selected {/if}>{$MOD.LBL_DAY6}</option>
														</select>
														</td>
														</tr>
														</table>
													</td>
												</tr>
												</table>
												</div>

											</div>
										</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						{/if}
						</div>
						<div id="addEventRelatedtoUI" style="display:none;width:100%">
						<table width="100%" cellpadding="5" cellspacing="0" border="0">
							{if $LABEL.parent_id neq ''}
							<tr>
								<!--td width="10%"><b>{$MOD.LBL_RELATEDTO}</b></td-->
								<td width="10%"><b><font color="red">{$TYPEOFDATA.relatedto}</font>{$MOD.LBL_RELATEDTO}</b></td>
								<td>
									<input name="parent_id" type="hidden" value="{$secondvalue.parent_id}">
									<input name="del_actparent_rel" type="hidden" >
									<select name="parent_type" class="small" id="parent_type" onChange="document.EditView.parent_name.value='';document.EditView.parent_id.value=''">
									{section name=combo loop=$LABEL.parent_id}
										<option value="{$fldlabel_combo.parent_id[combo]}" {$fldlabel_sel.parent_id[combo]}>{$LABEL.parent_id[combo]}</option>
									{/section}
                                             				</select>
								</td>
								<td>
									<div id="eventrelatedto" align="left">
										<input name="parent_name" readonly type="text" class="calTxt small" value="{$ACTIVITYDATA.parent_id}">
										<input type="button" name="selectparent" class="crmButton small edit" value="{$APP.LBL_SELECT_BUTTON_LABEL}" onclick="return window.open('index.php?module='+document.EditView.parent_type.value+'&action=Popup','test','width=640,height=602,resizable=0,scrollbars=0,top=150,left=200');">
										<input type='button' value='{$APP.LNK_DELETE}' class="crmButton small edit" onclick="document.EditView.del_actparent_rel.value=document.EditView.parent_id.value;document.EditView.parent_id.value='';document.EditView.parent_name.value='';">
									</div>
								</td>
							</tr>
							{/if}
			     			{if $IS_CONTACTS_EDIT_PERMITTED eq 'true'}
							<tr>
								<td><b>{$APP.Contacts}</b></td>
								<td colspan="2">
									<input name="contactidlist" id="contactidlist" value="{$CONTACTSID}" type="hidden">
									<input name="deletecntlist" id="deletecntlist" type="hidden">
									<select name="contactlist" size=5  style="height: 100px;width: 300px"  id="parentid" class="small" multiple>
									{foreach item=contactname key=cntid from=$CONTACTSNAME}
                                    	<option value="{$cntid}">{$contactname}</option>
                                    {/foreach}
									</select>

									<input type="button" onclick="selectContact('true','general',document.EditView);" class="crmButton small edit" name="selectcnt" value="{$APP.LBL_SELECT_CONTACT_BUTTON_LABEL}">
									<input type='button' value='{$APP.LNK_DELETE}' class="crmButton small edit" onclick='removeActContacts();'>

								</td>
							</tr>
							{/if}
						</table>
					</div>
			</td>
		</tr>
		</table>
		<!-- Alarm, Repeat, Invite stops-->
		{else}
		<table border="0" cellpadding="5" cellspacing="0" width="100%">
			<tr>
                        	<td class="cellLabel" width="20%" align="right"><b><font color="red">{$TYPEOFDATA.subject}</font>{$MOD.LBL_TODO}</b></td>
                        	<td class="cellInfo" width="80%" align="left"><input name="subject" value="{$ACTIVITYDATA.subject}" class="textbox" style="width: 70%;" type="text"></td>
           		</tr>

			<tr>
				{if $LABEL.description != ''}
				<td class="cellLabel" align="right"><b><font color="red">{$TYPEOFDATA.description}</font>{$LABEL.description}</b></td>
				<td class="cellInfo" align="left"><textarea style="width: 90%; height: 60px;" name="description">{$ACTIVITYDATA.description}</textarea>
				{/if}

			</tr>
			<tr>
		    		<td colspan="2" align="center" width="100%" style="padding:0px">
					<table border="0" cellpadding="5" cellspacing="1" width="100%">
            					<tr>
							{if $LABEL.taskstatus != ''}
							<td class="cellLabel" width=33% align="left"><b><font color="red">{$TYPEOFDATA.taskstatus}</font>{$LABEL.taskstatus}</b></td>
							{/if}
							{if $LABEL.taskpriority != ''}
              						<td class="cellLabel" width=33% align="left"><b><font color="red">{$TYPEOFDATA.taskpriority}</font>{$LABEL.taskpriority}</b></td>
							{/if}
              						{if $LABEL.assigned_user_id != ''}
							<td class="cellLabel" width=34% align="left"><b>{$LABEL.assigned_user_id}</b></td>
							{/if}
						</tr>
						<tr>
							{if $LABEL.taskstatus != ''}
							<td align="left" valign="top">
								<select name="taskstatus" id="taskstatus" class=small>
                                        			{foreach item=arr from=$ACTIVITYDATA.taskstatus}
									 {if $arr[0] eq $APP.LBL_NOT_ACCESSIBLE}
                                                                                        <option value="{$arr[0]}" {$arr[2]}>{$arr[0]}</option>
                                                                         {else}
                                                                                        <option value="{$arr[1]}" {$arr[2]}>
                                                                                                        {$arr[0]}
                                                                                         </option>
                                                                         {/if}
                                        			{/foreach}
                                				</select>
							</td>
							{/if}
							{if $LABEL.taskpriority != ''}
							<td align="left" valign="top">
								<select name="taskpriority" id="taskpriority" class=small>
        			                                {foreach item=arr from=$ACTIVITYDATA.taskpriority}
								 {if $arr[0] eq $APP.LBL_NOT_ACCESSIBLE}
                                                                                        <option value="{$arr[0]}" {$arr[2]}>{$arr[0]}</option>
                                                                                        {else}
                                                                                                <option value="{$arr[1]}" {$arr[2]}>
                                                                                                        {$arr[0]}
                                                                                                </option>
                                                                                        {/if}
                                        			{/foreach}
                                				</select>
							</td>
							{/if}
							{if $LABEL.assigned_user_id != ''}
							<td align="left" valign="top">
								{assign var=check value=1}
                                        			{foreach key=key_one item=arr from=$ACTIVITYDATA.assigned_user_id}
			                                        {foreach key=sel_value item=value from=$arr}
                        		                              	{if $value ne ''}
                                        		                      	{assign var=check value=$check*0}
                                                        		{else}
                                                                		{assign var=check value=$check*1}
                                                        		{/if}
                                                		{/foreach}
                                        			{/foreach}
								{if $check eq 0}
                                             				{assign var=select_user value='checked'}
                                                			{assign var=style_user value='display:block'}
                                                			{assign var=style_group value='display:none'}
                                        			{else}
                                                			{assign var=select_group value='checked'}
                                                			{assign var=style_user value='display:none'}
                                                			{assign var=style_group value='display:block'}
                                        			{/if}
				                                <input type="radio" name="assigntype" {$select_user} value="U" onclick="toggleAssignType(this.value)">&nbsp;{$APP.LBL_USER}
				                                {if $secondvalue.assigned_user_id neq ''}
                                			        <input type="radio" name="assigntype" {$select_group} value="T" onclick="toggleAssignType(this.value)">&nbsp;{$APP.LBL_GROUP}
                                        			{/if}
                                        			<span id="assign_user" style="{$style_user}">
                                                		<select name="assigned_user_id" class="small">
                                                        	{foreach key=key_one item=arr from=$ACTIVITYDATA.assigned_user_id}
				                                {foreach key=sel_value item=value from=$arr}
                                		                	<option value="{$key_one}" {$value}>{$sel_value}</option>
								{/foreach}
                                                        	{/foreach}
                                                		</select>
								</span>
								{if $secondvalue.assigned_user_id neq ''}
                                                		<span id="assign_team" style="{$style_group}">
                                                        		<select name="assigned_group_id" class="small">';
                                                                		{foreach key=key_one item=arr from=$secondvalue.assigned_user_id}
                                                                       		{foreach key=sel_value item=value from=$arr}
                                                                               		<option value="{$key_one}" {$value}>{$sel_value}</option>
                                                                       		{/foreach}
                                                                		{/foreach}
                                                        		</select>
				                                </span>
                                				{/if}
							</td>
							{else}
								<input name="assigned_user_id" value="{$CURRENTUSERID}" type="hidden">
							{/if}
						</tr>
					</table>
				</td>
			</tr>
			</table>
			<table border="0" cellpadding="0" cellspacing="1" width="100%" align=center>
			<tr><td width=50% valign=top>
				<table border=0 cellspacing=0 cellpadding=2 width=100% align=center >
					<tr><td colspan=3  class="mailSubHeader"><b>{$MOD.LBL_TODODATETIME}</b></td></tr>
					<tr><td colspan=3>{$STARTHOUR}</td></tr>
					<tr><td>
							{foreach key=date_value item=time_value from=$ACTIVITYDATA.date_start}
	                                        		{assign var=date_val value="$date_value"}
								{assign var=time_val value="$time_value"}
                                        		{/foreach}
							<input name="date_start" id="date_start" class="textbox" style="width: 90px;" onChange="dochange('date_start','due_date');" value="{$date_val}" type="text"></td><td width=100%><img src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$MOD.LBL_SET_DATE}" title="{$MOD.LBL_SET_DATE}" id="jscal_trigger_date_start" align="middle" border="0">
							{foreach key=date_fmt item=date_str from=$secondvalue.date_start}
								{assign var=date_vl value="$date_fmt"}
							{/foreach}
							<script type="text/javascript">
								Calendar.setup ({ldelim}
	        	                                	inputField : "date_start", ifFormat : "{$date_vl}", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1
							{rdelim})
							</script>
						</td></tr>
					</table></td>
					<td width=50% valign="top">
                                                <table border="0" cellpadding="2" cellspacing="0" width="100%" align=center>
							<tr><td class="mailSubHeader" colspan=3><b>{$LABEL.due_date}</b></td></tr>
							<tr><td>
								{foreach key=date_value item=time_value from=$ACTIVITYDATA.due_date}
									{assign var=date_val value="$date_value"}
									{assign var=time_val value="$time_value"}
								{/foreach}
								<input name="due_date" id="due_date" class="textbox" style="width: 90px;" value="{$date_val}" type="text"></td><td width=100%><img src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$MOD.LBL_SET_DATE}" title="{$MOD.LBL_SET_DATE}" id="jscal_trigger_due_date" border="0">
								{foreach key=date_fmt item=date_str from=$secondvalue.due_date}
                                                			{assign var=date_vl value="$date_fmt"}
                                        			{/foreach}
				      				<script type="text/javascript">
								Calendar.setup ({ldelim}
	                                        			inputField : "due_date", ifFormat : "{$date_vl}", showsTime : false, button : "jscal_trigger_due_date", singleClick : true, step : 1
					   			{rdelim})
								</script>
        						</td></tr>
						</table></td>
					</tr>
				</table>

			     {if $CUSTOM_FIELDS_DATA|@count > 0}
					<br><br>
                     <table border=0 cellspacing=0 cellpadding=5 width=100% >
                     	<tr>{strip}
				     		<td colspan=4 class="tableHeading">
							<b>{$APP.LBL_CUSTOM_INFORMATION}</b>
							</td>{/strip}
			          	</tr>
			          	<tr>
			          		{foreach key=index item=maindata from=$CUSTOM_FIELDS_DATA}
			          			{include file="EditViewUI.tpl"}
								{if ($index+1)% 2 == 0}
									</tr><tr>
								{/if}
				            {/foreach}
				        {if ($index+1)% 2 != 0}
				        	<td width="20%"></td><td width="30%"></td>
				        {/if}
			            </tr>
                     </table>
                 {/if}
				<br><br>
		{if $LABEL.sendnotification neq '' || ($LABEL.parent_id neq '') || ($LABEL.contact_id neq '') }
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" bgcolor="#FFFFFF">
			<tr>
				<td>
					<table border="0" cellpadding="3" cellspacing="0" width="100%">
						<tr>
							<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
							{if $LABEL.sendnotification neq ''}
                                                                {assign var='class_val' value='dvtUnSelectedCell'}
								<td id="cellTabInvite" class="dvtSelectedCell" align="center" nowrap="nowrap"><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabRelatedto','off');Taskshow('addTaskAlarmUI','todo',document.EditView.date_start.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value);ghide('addTaskRelatedtoUI');">{$MOD.LBL_NOTIFICATION}</a></td>
							{else}
                                                                {assign var='class_val' value='dvtSelectedCell'}
                                                        {/if}
							<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
							{if ($LABEL.parent_id neq '') || ($LABEL.contact_id neq '') }
                                                        <td id="cellTabRelatedto" class={$class_val} align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabRelatedto','on');Taskshow('addTaskRelatedtoUI','todo',document.EditView.date_start.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value);ghide('addTaskAlarmUI');">{$MOD.LBL_RELATEDTO}</a></td>
							{/if}
                                                        <td class="dvtTabCache" style="width:100%">&nbsp;</td>
						</tr>

					</table>
				</td>
			</tr>
			<tr>
				<td class="dvtContentSpace" style="padding: 10px; height: 120px;" align="left" valign="top" width="100%">
			<!-- Reminder UI -->
			<div id="addTaskAlarmUI" style="display: block; width: 100%;">
			{if $LABEL.sendnotification != ''}
				{assign var='vision' value='none'}
                	<table>
				<tr><td><font color="red">{$TYPEOFDATA.sendnotification}</font>{$LABEL.sendnotification}</td>
					{if $ACTIVITYDATA.sendnotification eq 1}
                                        <td>
                                                <input name="sendnotification" type="checkbox" checked>
                                        </td>
                                	{else}
                                        <td>
                                                <input name="sendnotification" type="checkbox">
                                        </td>
                                	{/if}
				</tr>
			</table>
			{else}
                                {assign var='vision' value='block'}
                        {/if}
			</div>
			<div id="addTaskRelatedtoUI" style="display:{$vision};width:100%">
           		     <table width="100%" cellpadding="5" cellspacing="0" border="0">
			     {if $LABEL.parent_id neq ''}
                	     <tr>
                        	     <td><b><font color="red">{$TYPEOFDATA.parent_id}</font>{$MOD.LBL_RELATEDTO}</b></td>
                                     <td>
					<input name="parent_id" type="hidden" value="{$secondvalue.parent_id}">
					<input name="del_actparent_rel" type="hidden" >
                                             <select name="parent_type" class="small" id="parent_type" onChange="document.EditView.parent_name.value='';document.EditView.parent_id.value=''">
							{section name=combo loop=$LABEL.parent_id}
								<option value="{$fldlabel_combo.parent_id[combo]}" {$fldlabel_sel.parent_id[combo]}>{$LABEL.parent_id[combo]}</option>
							{/section}
					     </select>
                                     </td>
                                     <td>
                              	        <div id="taskrelatedto" align="left">
						<input name="parent_name" readonly type="text" class="calTxt small" value="{$ACTIVITYDATA.parent_id}">
						<input type="button" name="selectparent" class="crmButton small edit" value="{$APP.LBL_SELECT}" onclick="return window.open('index.php?module='+document.EditView.parent_type.value+'&action=Popup','test','width=640,height=602,resizable=0,scrollbars=0,top=150,left=200');">
						<input type='button' value='{$APP.LNK_DELETE}' class="crmButton small edit" onclick="document.EditView.del_actparent_rel.value=document.EditView.parent_id.value;document.EditView.parent_id.value='';document.EditView.parent_name.value='';">
					 </div>
                                     </td>
			     </tr>
			     {/if}
			     {if $LABEL.contact_id neq ''}
			     <tr>
                                     <td><b><font color="red">{$TYPEOFDATA.contact_id}</font>{$LABEL.contact_id}</b></td>
				     <td colspan="2">
						<input name="contact_name" id = "contact_name" readonly type="text" class="calTxt" value="{$ACTIVITYDATA.contact_id}"><input name="contact_id"  type="hidden" value="{$secondvalue.contact_id}">&nbsp;
						<input name="deletecntlist"  id="deletecntlist" type="hidden">
						<input type="button" onclick="selectContact('false','task',document.EditView);" class="crmButton small edit" name="selectcnt" value="{$APP.LBL_SELECT}&nbsp;{$APP.SINGLE_Contacts}">
						<input type='button' value='{$APP.LNK_DELETE}' class="crmButton small edit" onclick='document.EditView.deletecntlist.value =document.EditView.contact_id.value;document.EditView.contact_name.value = "";document.EditView.contact_id.value="";'>
				     </td>
                             </tr>
			     {/if}
		</table>
		{/if}
              	</div>
                </td></tr></table>

		{/if}
			</td></tr>
			<tr>
				<td  colspan=4 style="padding:5px">
					<div align="center">
                        	        	<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.action.value='Save'; " type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
						<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="window.history.back()" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
					</div>
				</td>
			</tr></table>
		</td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</td></tr></table>
</td></tr>
<input name='search_url' id="search_url" type='hidden' value='{$SEARCH}'>
</form></table>
</td></tr></table>
</td></tr></table>
</td></tr></table>
        </td></tr></table>
        </td></tr></table>
        </div>
        </td>
        <td valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
        </tr>
        </table>
<script>
{if $ACTIVITY_MODE eq 'Task'}
	var fieldname = new Array('subject','date_start','time_start','due_date','taskstatus');
        var fieldlabel = new Array('{$MOD.LBL_LIST_SUBJECT}','{$MOD.LBL_START_DATE}','{$MOD.LBL_TIME}','{$MOD.LBL_DUE_DATE}','{$MOD.LBL_STATUS}');
        var fielddatatype = new Array('V~M','D~M~time_start','T~O','D~M~OTH~GE~date_start~Start Date & Time','V~O');
{else}
	var fieldname = new Array('subject','date_start','time_start','due_date','eventstatus','taskpriority','sendnotification','parent_id','contact_id','reminder_time','recurringtype');
        var fieldlabel = new Array('{$MOD.LBL_LIST_SUBJECT}','{$MOD.LBL_START_DATE}','{$MOD.LBL_TIME_START}','{$MOD.LBL_DUE_DATE}','{$MOD.LBL_STATUS}','{$MOD.Priority}','{$MOD.LBL_SENDNOTIFICATION}','{$MOD.LBL_RELATEDTO}','{$MOD.LBL_CONTACT_NAME}','{$MOD.LBL_SENDREMINDER}','{$MOD.Recurrence}');
        var fielddatatype = new Array('V~M','D~M','T~O','D~M~OTH~GE~date_start~Start Date','V~O','V~O','C~O','I~O','I~O','I~O','O~O');
{/if}
</script>
<script>
	var ProductImages=new Array();
	var count=0;

	function delRowEmt(imagename)
	{ldelim}
		ProductImages[count++]=imagename;
	{rdelim}

	function displaydeleted()
	{ldelim}
		var imagelists='';
		for(var x = 0; x < ProductImages.length; x++)
		{ldelim}
			imagelists+=ProductImages[x]+'###';
		{rdelim}

		if(imagelists != '')
			document.EditView.imagelist.value=imagelists
	{rdelim}

</script>

{if $PICKIST_DEPENDENCY_DATASOURCE neq ''}
<script type="text/javascript">
	Event.observe(window, 'load', function() {ldelim} (new FieldDependencies({$PICKIST_DEPENDENCY_DATASOURCE})).init() {rdelim});
</script>
{/if}