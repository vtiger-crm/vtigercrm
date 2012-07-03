{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/ *}
<script language="JavaScript" type="text/javascript" src="include/js/customview.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
			<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
				<br>
				<div align=center>
					{include file='SetMenu.tpl'}
					<!-- DISPLAY -->
					<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%" >
						<tr align="left">
							<td rowspan="2" valign="top" width="50"><img src="{'custom.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}" border="0" height="48" width="48"></td>
							<td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&action=ModuleManager&parenttab=Settings">{$MOD.VTLIB_LBL_MODULE_MANAGER}</a>&gt;<a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule=Leads&parenttab=Settings">{$MODULE}</a> &gt; {'LBL_FIELD_SETTINGS'|@getTranslatedString:$MODULE}</b></td>
						</tr>
						<tr align="left">
							<td class="small" valign="top">{'LBL_FIELD_MAPPING'|@getTranslatedString:$MODULE}</td>
						</tr>
					</table>
					<br>
					<form action="index.php?module=Settings&action=SaveConvertLead" method="post" name="index" onsubmit="VtigerJS_DialogBox.block();">
						<table class="listTableTopButtons" border="0" cellpadding="5" cellspacing="0" width="100%">
							<tr>
								<td class="big" align="left"><strong>{$MOD.LBL_EDIT_FIELD_MAPPING}</strong> </td>
								<td class="small">&nbsp;</td>
								<td class="small" align="right">&nbsp;&nbsp;
									<input title="{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" name="save" value=" &nbsp;{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE}&nbsp; " class="crmButton small save" type="submit" onclick ="return validateCustomFieldAccounts();">
									<input title="{'LBL_CANCEL_BUTTON_LABEL'|@getTranslatedString:$MODULE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" name="cancel" value=" {'LBL_CANCEL_BUTTON_LABEL'|@getTranslatedString:$MODULE} " onclick = "window.history.back()"  class="crmButton small cancel" type="button">
									<input title="{'LBL_ADD_MAPPING'|@getTranslatedString:$MODULE}" type="button" value="{'LBL_ADD_MAPPING'|@getTranslatedString:$MODULE}" onclick="javascript:cloneAndAddLeadMapping('cloneableNode','mapTable')"  class="crmButton small create"></input>
							</tr>
						</table>
						<table class="listTable" id="mapTable" border="0" cellpadding="5" cellspacing="0" width="100%">
							<tr>
								<td rowspan="2" class="colHeader small" width="2%">#</td>
								<td rowspan="2" class="colHeader small" width="15%">{$MOD.FieldLabel}</td>
								<td colspan="3" class="colHeader small" valign="top"><div align="center">{$MOD.LBL_MAPPING_OTHER_MODULES}</div></td>
							</tr>
							<tr>
								<td class="colHeader small" valign="top" width="23%">{$APP.Accounts}</td>
								<td class="colHeader small" valign="top" width="23%">{$APP.Contacts}</td>
								<td class="colHeader small" valign="top" width="24%">{$APP.Potentials}</td>
							</tr>
								{assign var=CNT value=0}
								{foreach item=map key=mapid from=$CUSTOMFIELDMAPPING}
									{if $map.display eq 'true'& $map.editable eq 1}
									{assign var=CNT value=$CNT+1}
							<tr>
								<td>{$CNT}</td>
								<td>
									<select class="small" name=map[{$CNT}][Leads] id=map[{$CNT}][Leads] module="Leads"{if $map.editable neq 1}disabled="disabled"{/if} onChange='return validateMapping("{$CNT}",this,"map[{$CNT}][Leads]")'>
										{foreach item=lead_cf key=lead_cf_index from=$map.lead}
											<option value="{$lead_cf.fieldid}" typeofdata="{$lead_cf.typeofdata}" fieldtype="{$lead_cf.fieldtype}" {if $lead_cf.fieldid eq $map.fieldid}selected="selected"{/if}>{$lead_cf.fieldlabel|@getTranslatedString:$MODULE}</option>
										{/foreach}
									</select>
								</td>
								<td>
									<select class="small" name=map[{$CNT}][Accounts] id=map[{$CNT}][Accounts] module="Accounts" {if $map.editable neq 1}disabled="disabled"{/if} onChange='return validateMapping("{$CNT}",this,"map[{$CNT}][Accounts]")'>
											<option value=''>{'LBL_NONE'|@getTranslatedString:$MODULE}</option>
										{foreach item=acc_cf key=acc_cf_index from=$map.account}
											<option value="{$acc_cf.fieldid}" typeofdata="{$acc_cf.typeofdata}" fieldtype="{$acc_cf.fieldtype}" {$acc_cf.selected}>{$acc_cf.fieldlabel|@getTranslatedString:$MODULE}</option>
										{/foreach}
									</select>
								</td>
								<td>
									<select class="small" name=map[{$CNT}][Contacts] id=map[{$CNT}][Contacts] module="Contacts" {if $map.editable neq 1}disabled="disabled"{/if} onChange='return validateMapping("{$CNT}",this,"map[{$CNT}][Contacts]")'>
										<option value=''>{'LBL_NONE'|@getTranslatedString:$MODULE}</option>
										{foreach item=con_cf key=con_cf_index from=$map.contact}
											<option value="{$con_cf.fieldid}" typeofdata="{$con_cf.typeofdata}" fieldtype="{$con_cf.fieldtype}" {$con_cf.selected}>{$con_cf.fieldlabel|@getTranslatedString:$MODULE}</option>
										{/foreach}
									</select>
								</td>
								<td>
									<select class="small" name=map[{$CNT}][Potentials] id=map[{$CNT}][Potentials] module="Potentials" {if $map.editable neq 1}disabled="disabled"{/if} onChange='return validateMapping("{$CNT}",this,"map[{$CNT}][Potentials]")'>
										<option value=''>{'LBL_NONE'|@getTranslatedString:$MODULE}</option>
										{foreach item=pot_cf key=pot_cf_index from=$map.potential}
											<option value="{$pot_cf.fieldid}" typeofdata="{$pot_cf.typeofdata}" fieldtype="{$pot_cf.fieldtype}" {$pot_cf.selected}>{$pot_cf.fieldlabel|@getTranslatedString:$MODULE}</option>
										{/foreach}
									</select>
								</td>
							</tr>
							{/if}
							{/foreach}
							<tr style="visibility:hidden;" id="cloneableNode" >
								<td><div id="snoDiv">incId</div></td>
								<td>
									<div id="leadCloneDiv">
										<select id="leadClone" name="leadClone" id="leadClone" class="small" module="Leads" onChange='return validateMapping("incId",this,"leadClone")'>
											<option value=''>{'LBL_NONE'|@getTranslatedString:$MODULE}</option>
											{foreach item=field key=field_index from=$CUSTOMFIELDMAPPING[0].lead}
													<option value="{$field.fieldid}" typeofdata="{$field.typeofdata}" fieldtype="{$field.fieldtype}">{$field.fieldlabel|@getTranslatedString:$MODULE}</option>
											{/foreach}
										</select>
									</div>
								</td>
								<td >
									<div id="accountCloneDiv">
										<select id="accountClone" name="accountClone" id="accountClone" class="small" module="Accounts" onChange='return validateMapping("incId",this,"accountClone")'>
												<option value=''>{'LBL_NONE'|@getTranslatedString:$MODULE}</option>
											{foreach item=field key=field_index from=$CUSTOMFIELDMAPPING[0].account}
													<option value="{$field.fieldid}" typeofdata="{$field.typeofdata}" fieldtype="{$field.fieldtype}">{$field.fieldlabel|@getTranslatedString:$MODULE}</option>
											{/foreach}
										</select>
									</div>
								</td>
								<td>
									<div id="contactCloneDiv">
										<select id="contactClone" name="contactClone" id="contactClone" class="small" module="Contacts" onChange='return validateMapping("incId",this,"contactClone")'>
												<option value=''>{'LBL_NONE'|@getTranslatedString:$MODULE}</option>
											{foreach item=field key=field_index from=$CUSTOMFIELDMAPPING[0].contact}
													<option value="{$field.fieldid}" typeofdata="{$field.typeofdata}" fieldtype="{$field.fieldtype}">{$field.fieldlabel|@getTranslatedString:$MODULE}</option>
											{/foreach}
										</select>
									</div>
								</td>
								<td>
									<div id="potentialCloneDiv">
										<select id="potentialClone" name="potentialClone" id="potentialClone" class="small" module="Potentials" onChange='return validateMapping("incId",this,"potentialClone")'>
												<option value=''>{'LBL_NONE'|@getTranslatedString:$MODULE}</option>
											{foreach item=field key=field_index from=$CUSTOMFIELDMAPPING[0].potential}
													<option value="{$field.fieldid}" typeofdata="{$field.typeofdata}" fieldtype="{$field.fieldtype}">{$field.fieldlabel|@getTranslatedString:$MODULE}</option>
											{/foreach}
										</select>
									</div>
								</td>
							</tr>
						</table>
						<table class="listTableTopButtons" border="0" cellpadding="5" cellspacing="0" width="100%">
							<tr>
								<td class="small" align="right">&nbsp;&nbsp;
									<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="crmButton small save" type="submit" onclick ="return validateCustomFieldAccounts();">
									<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " onclick = "window.history.back()"  class="crmButton small cancel" type="button">
									<input title="{'LBL_ADD_MAPPING'|@getTranslatedString:$MODULE}" type="button" value="{'LBL_ADD_MAPPING'|@getTranslatedString:$MODULE}" onclick="javascript:cloneAndAddLeadMapping('cloneableNode','mapTable')"  class="crmButton small create"></input>
								</td>
							</tr>
						</table>
						<table border="0" cellpadding="5" cellspacing="0" width="100%">
							<tr>
								<td class="small">
									<strong>{$APP.LBL_NOTE}: </strong> {$MOD.LBL_CUSTOM_MAPP_INFO}
								</td>
							</tr>
						</table>
						<table border="0" cellpadding="5" cellspacing="0" width="100%">
							<tr>
								<td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td>
							</tr>
						</table>
					</form>
				</div>
			</td>
			<td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
		</tr>
	</tbody>
</table>

<script language="JavaScript" type="text/javascript">
	incId={$CNT+1};
</script>
<script>
	var alertmessage = new Array("{$MOD.LBL_TYPEALERT_1}","{$MOD.LBL_WITH}","{$MOD.LBL_TYPEALERT_2}","{$MOD.LBL_LENGTHALERT}","{$MOD.LBL_DECIMALALERT}");
</script>
