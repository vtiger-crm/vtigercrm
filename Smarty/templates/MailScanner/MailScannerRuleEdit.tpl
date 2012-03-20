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

<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody>
<tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
    <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">

	<form action="index.php" method="post" id="form" onsubmit="VtigerJS_DialogBox.block();">
		<input type='hidden' name='module' value='Settings'>
		<input type='hidden' name='action' value='MailScanner'>
		<input type='hidden' name='mode' value='rulesave'>
		<input type='hidden' name='ruleid' value="{$SCANNERRULE->ruleid}">
		<input type='hidden' name='return_action' value='MailScanner'>
		<input type='hidden' name='return_module' value='Settings'>
		<input type='hidden' name='parenttab' value='Settings'>

        <br>

		<div align=center>
			{include file='SetMenu.tpl'}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'mailScanner.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_MAIL_SCANNER}" width="48" height="48" border=0 title="{$MOD.LBL_MAIL_SCANNER}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_MAIL_SCANNER}</b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_MAIL_SCANNER_DESCRIPTION}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
				<td class="big" width="70%"><strong>{$MOD.LBL_MAIL_SCANNER} {$MOD.LBL_RULE} {$MOD.LBL_INFORMATION}</strong></td>
				</tr>
				</table>
				
				<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
				<tr>
	         	    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
						<tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SCANNER} {$MOD.LBL_NAME}</strong></td>
                            <td width="80%" colspan=2>{$SCANNERINFO.scannername}
								<input type="hidden" name="scannername" class="small" value="{$SCANNERINFO.scannername}" size=50 readonly></td>
                        </tr>
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_FROM}</strong></td>
                            <td width="80%" colspan=2><input type="text" name="rule_from" class="small" value="{$SCANNERRULE->fromaddress}" size=50></td>
                        </tr>
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_TO}</strong></td>
                            <td width="80%" colspan=2><input type="text" name="rule_to" class="small" value="{$SCANNERRULE->toaddress}" size=50></td>
                        </tr>
						<tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SUBJECT}</strong></td>
                            <td width="10%">
								<select name="rule_subjectop" class="small">
									<option value=''>-- {$MOD.LBL_SELECT} {$MOD.LBL_CONDITION} --</option>
									<option value='Contains'    {if $SCANNERRULE->subjectop eq 'Contains'}selected=true{/if}
									>{$MOD.LBL_CONTAINS}</option>
									<option value='Not Contains' {if $SCANNERRULE->subjectop eq 'Not Contains'}selected=true{/if}
									>{$MOD.LBL_NOT} {$MOD.LBL_CONTAINS}</option>
									<option value='Equals'      {if $SCANNERRULE->subjectop eq 'Equals'}selected=true{/if}
									>{$MOD.LBL_EQUALS}</option>
									<option value='Not Equals'  {if $SCANNERRULE->subjectop eq 'Not Equals'}selected=true{/if}
									>{$MOD.LBL_NOT} {$MOD.LBL_EQUALS}</option>
									<option value='Begins With' {if $SCANNERRULE->subjectop eq 'Begins With'}selected=true{/if}
									>{$MOD.LBL_BEGINS} {$MOD.LBL_WITH}</option>
									<option value='Ends With'   {if $SCANNERRULE->subjectop eq 'Ends With'}selected=true{/if}
									>{$MOD.LBL_ENDS} {$MOD.LBL_WITH}</option>
									<option value='Regex'       {if $SCANNERRULE->subjectop eq 'Regex'}selected=true{/if}
									>{$MOD.LBL_REGEX}</option> 
								</select>
							</td>
							<td width="70%">
								<input type="text" name="rule_subject" class="small" value="{$SCANNERRULE->subject}" size="65"/>
							</td>
                        </tr>
						<tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_BODY}</strong></td>
                            <td width="10%">
								<select name="rule_bodyop" class="small">
									<option value=''>-- {$MOD.LBL_SELECT} {$MOD.LBL_CONDITION} --</option>
									<option value='Contains'    {if $SCANNERRULE->bodyop eq 'Contains'}selected=true{/if}
									>{$MOD.LBL_CONTAINS}</option>
									<option value='Not Contains' {if $SCANNERRULE->subjectop eq 'Not Contains'}selected=true{/if}
									>{$MOD.LBL_NOT} {$MOD.LBL_CONTAINS}</option>
									<option value='Equals'      {if $SCANNERRULE->bodyop eq 'Equals'}selected=true{/if}
									>{$MOD.LBL_EQUALS}</option>
									<option value='Not Equals'  {if $SCANNERRULE->bodyop eq 'Not Equals'}selected=true{/if}
									>{$MOD.LBL_NOT} {$MOD.LBL_EQUALS}</option>
									<option value='Begins With' {if $SCANNERRULE->bodyop eq 'Begins With'}selected=true{/if}
									>{$MOD.LBL_BEGINS} {$MOD.LBL_WITH}</option>
									<option value='Ends With'   {if $SCANNERRULE->bodyop eq 'Ends With'}selected=true{/if}
									>{$MOD.LBL_ENDS} {$MOD.LBL_WITH}</option>
									{* TODO: Provide Regex support *}
									{* <option value='Regex'       {if $SCANNERRULE->bodyop eq 'Regex'}selected=true{/if}
									>{$MOD.LBL_REGEX}</option> 
									 *}
								</select>
							</td>
							<td width="70%">
								<textarea name="rule_body" class="small">{$SCANNERRULE->body}</textarea> 
							</td>
                        </tr>
						<tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_MATCH}</strong></td>
                            <td width="70%" colspan=2>
								{if $SCANNERRULE->matchusing eq 'OR'}
									{assign var="rule_match_or" value="checked='true'"}
									{assign var="rule_match_all" value=""}
								{else}
									{assign var="rule_match_or" value=""}
									{assign var="rule_match_all" value="checked='true'"}
								{/if}
								<input type="radio" class="small" name="rule_matchusing" value="AND" {$rule_match_all}> {$MOD.LBL_ALL} {$MOD.LBL_CONDITION}
								<input type="radio" class="small" name="rule_matchusing" value="OR" {$rule_match_or}> {$MOD.LBL_ANY} {$MOD.LBL_CONDITION}
							</td>
                        </tr>
						<tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_ACTION}</strong></td>
                            <td width="70%" colspan=2>
								{assign var="RULEACTIONTEXT" value=""}
								{if $SCANNERRULE->useaction}
									{assign var="RULEACTIONTEXT" value=$SCANNERRULE->useaction->actiontext}
									<input type="hidden" class="small" name="actionid" value="{$SCANNERRULE->useaction->actionid}">
								{else}
									<input type="hidden" class="small" name="actionid" value="">
								{/if}

								<select name="rule_actiontext" class="small">
									{* <option value="">-- None --</option> *}{* EMPTY ACTION NOT SUPPORTED *}
									<option value="CREATE,HelpDesk,FROM" {if $RULEACTIONTEXT eq 'CREATE,HelpDesk,FROM'}selected=true{/if}
									>{$MOD.LBL_CREATE} {$MOD.LBL_TICKET}</option>
									<option value="UPDATE,HelpDesk,SUBJECT" {if $RULEACTIONTEXT eq 'UPDATE,HelpDesk,SUBJECT'}selected=true{/if}
									>{$MOD.LBL_UPDATE} {$MOD.LBL_TICKET}</option>
									<option value="LINK,Contacts,FROM" {if $RULEACTIONTEXT eq 'LINK,Contacts,FROM'}selected=true{/if}
									>{$MOD.LBL_ADD} {$MOD.LBL_TO_SMALL} {$MOD.LBL_CONTACT} [{$MOD.LBL_FROM_CAPS}]</option>
									<option value="LINK,Contacts,TO" {if $RULEACTIONTEXT eq 'LINK,Contacts,TO'}selected=true{/if}
									>{$MOD.LBL_ADD} {$MOD.LBL_TO_SMALL} {$MOD.LBL_CONTACT} [{$MOD.LBL_TO_CAPS}]</option>
									<option value="LINK,Accounts,FROM" {if $RULEACTIONTEXT eq 'LINK,Accounts,FROM'}selected=true{/if}
									>{$MOD.LBL_ADD} {$MOD.LBL_TO_SMALL} {$MOD.LBL_ACCOUNT} [{$MOD.LBL_FROM_CAPS}]</option>
									<option value="LINK,Accounts,TO" {if $RULEACTIONTEXT eq 'LINK,Accounts,TO'}selected=true{/if}
									>{$MOD.LBL_ADD} {$MOD.LBL_TO_SMALL} {$MOD.LBL_ACCOUNT} [{$MOD.LBL_TO_CAPS}]</option>
								</select>
							</td>
                        </tr>
				    </td>
            	</tr>
				<tr>
					<td colspan=3 nowrap align="center">
						<input type="submit" class="crmbutton small save" value="{$APP.LBL_SAVE_LABEL}" />
						<input type="button" class="crmbutton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" 
							onclick="location.href='index.php?module=Settings&action=MailScanner&parenttab=Settings&mode=rule&scannername={$SCANNERINFO.scannername}'"/>
					</td>
				</tr>
				</table>	
				
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
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</form>
</table>

</tr>
</table>

</tr>
</table>
