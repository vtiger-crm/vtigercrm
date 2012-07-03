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

{assign var=row value=$UIINFO->getLeadInfo()}

<form name="ConvertLead" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="module" value="Leads">
	<input type="hidden" name="transferToName" value="{$row.company}">
	<input type="hidden" name="record" value="{$UIINFO->getLeadId()}">
	<input type="hidden" name="action">
	<input type="hidden" name="parenttab" value="{$CATEGORY}">
	<input type="hidden" name="current_user_id" value="{$UIINFO->getUserId()}'">

	<div id="orgLay" style="display: block;" class="layerPopup" >

		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
			<tr>
				<td class="genHeaderSmall" align="left"><img src="{'Leads.gif'|@vtiger_imageurl:$THEME}">{'ConvertLead'|@getTranslatedString:$MODULE} : {$row.firstname} {$row.lastname}</td>
				<td align="right"><a href="javascript:fninvsh('orgLay');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0"></a></td>
			</tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
			{if $UIINFO->isModuleActive('Accounts') && $row.company neq '' }
			<tr>
				<td class="small" >
					<table border="0" cellspacing="0" cellpadding="0" width="95%" align="center" bgcolor="white">
						<tr>
							<td colspan="4" class="detailedViewHeader">
								<input type="checkbox" onclick="javascript:showHideStatus('account_block',null,null);" id="select_account" name="entities[]" value="Accounts" {if $row.company neq ''} checked {/if} />
								<b>{'SINGLE_Accounts'|@getTranslatedString:$MODULE}</b>
							</td>
						</tr>
						<tr>
							<td>
								<div id="account_block" {if $row.company neq ''} style="display:block;" {else} style="display:none;" {/if}>
									<table border="0" cellspacing="0" cellpadding="5" width="100%" align="center" bgcolor="white">
										<tr>
											<td align="right" width="50%" class="dvtCellLabel">{if $UIINFO->isMandatory('Accounts','accountname') eq true}<font color="red">*</font>{/if}{'LBL_ACCOUNT_NAME'|@getTranslatedString:$MODULE}</td>
											<td class="dvtCellInfo"><input type="text" name="accountname" class="detailedViewTextBox" value="{$UIINFO->getMappedFieldValue('Accounts','accountname',0)}" readonly="readonly" module="Accounts" {if $UIINFO->isMandatory('Accounts','accountname') eq true}record="true"{/if}></td>
										</tr>
										{if $UIINFO->isActive('industry','Accounts')}
										<tr>
											<td align="right" class="dvtCellLabel">{if $UIINFO->isMandatory('Accounts','industry') eq true}<font color="red">*</font>{/if}{'industry'|@getTranslatedString:$MODULE}</td>
											<td class="dvtCellInfo">
													{assign var=industry_map_value value=$UIINFO->getMappedFieldValue('Accounts','industry',1)}
													<select name="industry" class="small" module="Accounts" {if $UIINFO->isMandatory('Accounts','industry') eq true}record="true"{/if}>
														{foreach item=industry from=$UIINFO->getIndustryList() name=industryloop}
															<option value="{$industry.value}" {if $industry.value eq $UIINFO->getMappedFieldValue('Accounts','industry',1)}selected="selected"{/if}>{$industry.value|@getTranslatedString:$MODULE}</option>
														{/foreach}
													</select>
											</td>
										</tr>
										{/if}
									</table>
								</div>
							<td>
						</tr>
					</table>
				</td>
			</tr>
			{/if}
			{if $UIINFO->isModuleActive('Potentials')}
			<tr>
				<td class="small">
					<table border="0" cellspacing="0" cellpadding="0" width="95%" align="center" bgcolor="white">
						<tr>
							<td colspan="4" class="detailedViewHeader">
								<input type="checkbox" onclick="javascript:showHideStatus('potential_block',null,null);"id="select_potential" name="entities[]" value="Potentials"></input>
								<b>{'SINGLE_Potentials'|@getTranslatedString:$MODULE}</b>
							</td>
						</tr>
						<tr>
							<td>
								<div id="potential_block" style="display:none;">
									<table border="0" cellspacing="0" cellpadding="5" width="100%" align="center" bgcolor="white">
										{if $UIINFO->isActive('potentialname','Potentials')}
										<tr>
											<td align="right" width="50%" class="dvtCellLabel">{if $UIINFO->isMandatory('Potentials','potentialname') eq true}<font color="red">*</font>{/if}{'LBL_POTENTIAL_NAME'|@getTranslatedString:$MODULE}</td>
											<td class="dvtCellInfo"><input  name="potentialname" id="potentialname" {if $UIINFO->isMandatory('Potentials','potentialname') eq true}record="true"{/if} module="Potentials" value="{$UIINFO->getMappedFieldValue('Potentials','potentialname',0)}" class="detailedViewTextBox" /></td>
										</tr>
										{/if}
										{if $UIINFO->isActive('closingdate','Potentials')}
										<tr>
											<td align="right" class="dvtCellLabel">{if $UIINFO->isMandatory('Potentials','closingdate') eq true}<font color="red">*</font>{/if}{'Expected Close Date'|@getTranslatedString:$MODULE}</td>
											<td class="dvtCellInfo">
												<input name="closingdate" {if $UIINFO->isMandatory('Potentials','closingdate') eq true}record="true"{/if} module="Potentials" style="border: 1px solid rgb(186, 186, 186);" id="jscal_field_closedate" type="text" tabindex="4" size="10" maxlength="10" value="{$UIINFO->getMappedFieldValue('Potentials','closingdate',1)}">
												<img src="{'miniCalendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_closedate" >
												<font size=1><em old="(yyyy-mm-dd)">({$DATE_FORMAT})</em></font>
												<script id="conv_leadcal">
													getCalendarPopup('jscal_trigger_closedate','jscal_field_closedate','{$CAL_DATE_FORMAT}')
												</script>
											</td>
										</tr>
										{/if}
										{if $UIINFO->isActive('sales_stage','Potentials')}
										<tr>
											<td align="right" class="dvtCellLabel">{if $UIINFO->isMandatory('Potentials','sales_stage') eq true}<font color="red">*</font>{/if}{'LBL_SALES_STAGE'|@getTranslatedString:$MODULE}</td>
											<td class="dvtCellInfo">
												{assign var=sales_stage_map_value value=$UIINFO->getMappedFieldValue('Potentials','sales_stage',1)}
												<select name="sales_stage" {if $UIINFO->isMandatory('Potentials','sales_stage') eq true}record="true"{/if} module="Potentials" class="small">
													{foreach item=salesStage from=$UIINFO->getSalesStageList() name=salesStageLoop}
														<option value="{$salesStage.value}" {if $salesStage.value eq $sales_stage_map_value}selected="selected"{/if} >{$salesStage.value|@getTranslatedString:$MODULE}</option>
													{/foreach}
												</select>
											</td>
										</tr>
										{/if}
										{if $UIINFO->isActive('amount','Potentials')}
										<tr>
											<td align="right" class="dvtCellLabel">{if $UIINFO->isMandatory('Potentials','amount') eq true}<font color="red">*</font>{/if}{'Amount'|@getTranslatedString:$MODULE}</td>
											<td class="dvtCellInfo"><input type="text" name="amount" class="detailedViewTextBox" {if $UIINFO->isMandatory('Potentials','amount') eq true}record="true"{/if} module="Potentials" value="{$UIINFO->getMappedFieldValue('Potentials','amount',1)}"></input></td>
										</tr>
										{/if}
									</table>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			{/if}
			{if $UIINFO->isModuleActive('Contacts')}
			<tr>
				<td class="small">
					<table border="0" cellspacing="0" cellpadding="0" width="95%" align="center" bgcolor="white">
						<tr>
							<td colspan="4" class="detailedViewHeader">
								<input type="checkbox" checked="checked" onclick="javascript:showHideStatus('contact_block',null,null);" id="select_contact" name="entities[]" value="Contacts"></input>
								<b>{'SINGLE_Contacts'|@getTranslatedString:$MODULE}</b>
							</td>
						</tr>
						<tr>
							<td>
								<div id="contact_block" style="display:block;" >
									<table border="0" cellspacing="0" cellpadding="5" width="100%" align="center" bgcolor="white">
										<tr>
											<td align="right" width="50%" class="dvtCellLabel">{if $UIINFO->isMandatory('Contacts','lastname') eq true}<font color="red">*</font>{/if}{'Last Name'|@getTranslatedString:$MODULE}</td>
											<td class="dvtCellInfo"><input type="text" name="lastname" {if $UIINFO->isMandatory('Contacts','lastname') eq true}record="true"{/if} module="Contacts" class="detailedViewTextBox" value="{$UIINFO->getMappedFieldValue('Contacts','lastname',0)}"></td>
										</tr>
										{if $UIINFO->isActive('firstname','Contacts')}
										<tr>
											<td align="right" width="50%" class="dvtCellLabel">{if $UIINFO->isMandatory('Contacts','firstname') eq true}<font color="red">*</font>{/if}{'First Name'|@getTranslatedString:$MODULE}</td>
											<td class="dvtCellInfo"><input type="text" name="firstname" class="detailedViewTextBox" module="Contacts" value="{$UIINFO->getMappedFieldValue('Contacts','firstname',0)}" {if $UIINFO->isMandatory('Contacts','firstname') eq true}record="true"{/if} ></td>
										</tr>
										{/if}
										{if $UIINFO->isActive('email','Contacts')}
										<tr>
											<td align="right" width="50%" class="dvtCellLabel">{if $UIINFO->isMandatory('Contacts','email') eq true}<font color="red">*</font>{/if}{'SINGLE_Emails'|@getTranslatedString:$MODULE}</td>
											<td class="dvtCellInfo"><input type="text" name="email" class="detailedViewTextBox" value="{$UIINFO->getMappedFieldValue('Contacts','email',0)}" {if $UIINFO->isMandatory('Contacts','email') eq true}record="true"{/if} module="Contacts"></td>
										</tr>
										{/if}
									</table>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					&nbsp;
				</td>
			</tr>
			{/if}
			<tr>
				<td class="small">
					<table border="0" cellspacing="0" cellpadding="5" width="95%" align="center" bgcolor="white">
						<tr>
							<td align="right" class="dvtCellLabel" width="50%" style="border-top:1px solid #DEDEDE;">{'LBL_LIST_ASSIGNED_USER'|@getTranslatedString:$MODULE}</td>
							<td class="dvtCellInfo" width="50%" style="border-top:1px solid #DEDEDE;">
								<input type="radio" name="c_assigntype" value="U" onclick="javascript: c_toggleAssignType(this.value)" {$UIINFO->getUserSelected()} />&nbsp;{'LBL_USER'|@getTranslatedString:$MODULE}
								{if $UIINFO->getOwnerList('group')|@count neq 0}
								<input type="radio" name="c_assigntype" value="T" onclick="javascript: c_toggleAssignType(this.value)" {$UIINFO->getGroupSelected()} />&nbsp;{'LBL_GROUP'|@getTranslatedString:$MODULE}
								{/if}
								<span id="c_assign_user" style="display:{$UIINFO->getUserDisplay()}">
									<select name="c_assigned_user_id" class="detailedViewTextBox">
										{foreach item=user from=$UIINFO->getOwnerList('user') name=userloop}
											<option value="{$user.userid}" {if $user.selected eq true}selected="selected"{/if}>{$user.username}</option>
										{/foreach}
									</select>
								</span>
								<span id="c_assign_team" style="display:{$UIINFO->getGroupDisplay()}">
									{if $UIINFO->getOwnerList('group')|@count neq 0}
									<select name="c_assigned_group_id" class="detailedViewTextBox">
										{foreach item=group from=$UIINFO->getOwnerList('group') name=grouploop}
											<option value="{$group.groupid}" {if $group.selected eq true}selected="selected"{/if}>{$group.groupname}</option>
										{/foreach}
									</select>
									{/if}
								</span>
							</td>
						</tr>
						<tr>
							<td align="right" class="dvtCellLabel" width="50%">{'LBL_TRANSFER_RELATED_RECORDS_TO'|@getTranslatedString:$MODULE}</td>
							<td class="dvtCellInfo" width="50%">
								{if $UIINFO->isModuleActive('Accounts') eq true && $row.company neq ''}<input type="radio" name="transferto" id="transfertoacc" value="Accounts" onclick="selectTransferTo('Accounts')"  {if $UIINFO->isModuleActive('Contacts') neq true}checked="checked"{/if} />{'SINGLE_Accounts'|@getTranslatedString:$MODULE}{/if}
								{if $UIINFO->isModuleActive('Contacts') eq true}<input type="radio" name="transferto" id="transfertocon" value="Contacts" checked="checked" onclick="selectTransferTo('Contacts')"  /> {'SINGLE_Contacts'|@getTranslatedString:$MODULE}{/if}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			</table>
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
			<tr>
					<td align="center">
						<input name="Save" value="{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}" onclick="javascript:this.form.action.value='LeadConvertToEntities'; return verifyConvertLeadData(ConvertLead)" type="submit"  class="crmbutton save small">&nbsp;&nbsp;
						<input type="button" name=" Cancel " value="{'LBL_CANCEL_BUTTON_LABEL'|@getTranslatedString:$MODULE}" onClick="hide('orgLay')" class="crmbutton cancel small">
					</td>
				</tr>
			</table>
	</div>
</form>


