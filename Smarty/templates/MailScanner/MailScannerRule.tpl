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
		<input type='hidden' name='mode' value='ruleedit'>
		<input type='hidden' name='scannername' value='{$SCANNERINFO.scannername}'>
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
				<td class="big" width="70%"><strong>{$MOD.LBL_RULES} {$MOD.LBL_FOR} {$MOD.LBL_MAIL_SCANNER} [{$SCANNERINFO.scannername}]</strong></td>
				<td width="30%" nowrap align="right">
					<input type="button" class="crmbutton small cancel" value="{$APP.LBL_BACK}" 
						onclick="location.href='index.php?module=Settings&action=MailScanner&parenttab=Settings'" />
					<input type="submit" class="crmbutton small create" onclick="this.form.mode.value='ruleedit'" value="{$APP.LBL_ADD_NEW} {$MOD.LBL_RULE}" />
				</td>
				</tr>
				</table>
				
				<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
				<tr>
	         	    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">

						{assign var="PREV_RULEID" value=""}
						{foreach item=SCANNERRULE key=RULEINDEX from=$SCANNERRULES}
							{assign var="NEXT_RULEID" value=""}
							{if $RULEINDEX neq (count($SCANNERRULES)-1)}
								{assign var="RULEINDEX1" value=$RULEINDEX+1}
								{assign var="NEXT_RULEID" value=$SCANNERRULES.$RULEINDEX1->ruleid}
							{/if}
						<tr>
							<td nowrap class="small cellLabel">
								<strong>{$MOD.LBL_PRIORITY}</strong>
								<span style='margin-left: 100px;'>
								{if $NEXT_RULEID}
<a href="index.php?module=Settings&action=MailScanner&parenttabl=Settings&mode=rulemove_down&scannername={$SCANNERINFO.scannername}&targetruleid={$NEXT_RULEID}&ruleid={$SCANNERRULE->ruleid}" title="{$MOD.LBL_MOVE} {$MOD.LBL_DOWN}"><img src="{'arrow_down.gif'|@vtiger_imageurl:$THEME}" border=0></a>
								{/if}
								{if $PREV_RULEID}
<a href="index.php?module=Settings&action=MailScanner&parenttabl=Settings&mode=rulemove_up&scannername={$SCANNERINFO.scannername}&targetruleid={$PREV_RULEID}&ruleid={$SCANNERRULE->ruleid}" title="{$MOD.LBL_MOVE} {$MOD.LBL_UP}"><img src="{'arrow_up.gif'|@vtiger_imageurl:$THEME}" border=0></a>
								{/if}
								</span>
							</td>
							<td nowrap class="small cellLabel" align=right colspan=2>
								<a href="index.php?module=Settings&action=MailScanner&parenttab=Settings&mode=ruleedit&scannername={$SCANNERINFO.scannername}&ruleid={$SCANNERRULE->ruleid}">{$APP.LBL_EDIT}</a> |
								<a href="index.php?module=Settings&action=MailScanner&parenttab=Settings&mode=ruledelete&scannername={$SCANNERINFO.scannername}&ruleid={$SCANNERRULE->ruleid}" onclick="return confirm('Are you sure to delete this Rule?');">{$APP.LBL_DELETE}</a>
							</td>
						</tr>
						<tr>
                            <td nowrap class="small cellLabel" width="20%"><strong>{$MOD.LBL_FROM}</strong></td>
                            <td nowrap class="small cellText" width="80%" colspan=2>
								{$SCANNERRULE->fromaddress}
							</td>
						</tr>
						<tr>
                            <td nowrap class="small cellLabel" width="20%"><strong>{$MOD.LBL_TO}</strong></td>
                            <td nowrap class="small cellText" width="80%" colspan=2>
								{$SCANNERRULE->toaddress}
							</td>
						</tr>
						<tr>
                            <td nowrap class="small cellLabel" width="20%"><strong>{$MOD.LBL_SUBJECT}</strong></td>
                            <td nowrap class="small cellText" width="10%">{$SCANNERRULE->subjectop}</td>
                            <td nowrap class="small cellText" width="70%">
								{$SCANNERRULE->subject}
							</td>
						</tr>
						<tr>
                            <td nowrap class="small cellLabel" width="20%"><strong>{$MOD.LBL_BODY}</strong></td>
                            <td nowrap class="small cellText" width="10%">{$SCANNERRULE->bodyop}</td>
                            <td nowrap class="small cellText" width="70%">
								{$SCANNERRULE->body}
							</td>
						</tr>
						<tr>
                            <td nowrap class="small cellLabel" width="20%"><strong>{$MOD.LBL_MATCH}</strong></td>
                            <td nowrap class="small cellText" width="80%" colspan=2>
								{if $SCANNERRULE->matchusing eq 'OR'}{$MOD.LBL_ANY} {$MOD.LBL_CONDITION}
								{else} {$MOD.LBL_ALL} {$MOD.LBL_CONDITION} {/if}
							</td>
						</tr>
						<tr>
                            <td nowrap class="small cellLabel" width="20%"><strong>{$MOD.LBL_ACTION}</strong></td>
                            <td nowrap class="small cellText" width="80%" colspan=2>
								{if $SCANNERRULE->useaction->actiontext eq 'CREATE,HelpDesk,FROM'} {$MOD.LBL_CREATE} {$MOD.LBL_TICKET}
								{elseif $SCANNERRULE->useaction->actiontext eq 'UPDATE,HelpDesk,SUBJECT'} {$MOD.LBL_UPDATE} {$MOD.LBL_TICKET}
								{elseif $SCANNERRULE->useaction->actiontext eq 'LINK,Contacts,FROM'}{$MOD.LBL_ADD} {$MOD.LBL_TO} {$MOD.LBL_CONTACT} [{$MOD.LBL_FROM_CAPS}]
								{elseif $SCANNERRULE->useaction->actiontext eq 'LINK,Contacts,TO'}{$MOD.LBL_ADD} {$MOD.LBL_TO} {$MOD.LBL_CONTACT} [{$MOD.LBL_TO_CAPS}]
								{elseif $SCANNERRULE->useaction->actiontext eq 'LINK,Accounts,FROM'}{$MOD.LBL_ADD} {$MOD.LBL_TO} {$MOD.LBL_ACCOUNT} [{$MOD.LBL_FROM_CAPS}]
								{elseif $SCANNERRULE->useaction->actiontext eq 'LINK,Accounts,TO'}{$MOD.LBL_ADD} {$MOD.LBL_TO} {$MOD.LBL_ACCOUNT} [{$MOD.LBL_TO_CAPS}]
								{/if}
							</td>
						</tr>
						{if $NEXT_RULEID}
							<tr><td colspan=3 class="small cellText">&nbsp;</td></tr>
						{/if}
						{assign var="PREV_RULEID" value=$SCANNERRULE->ruleid}
					{/foreach}
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
