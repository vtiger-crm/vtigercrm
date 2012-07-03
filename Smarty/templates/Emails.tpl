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
<!--  USER  SETTINGS PAGE STARTS HERE -->
		{include file='Buttons_List.tpl'}
<script language="JavaScript" type="text/javascript" src="modules/Emails/Emails.js"></script>
<link rel="stylesheet" type="text/css" href="themes/{$theme}/webmail.css">
<div id="mailconfchk" class="small" style="position:absolute;display:none;left:350px;top:160px;height:27px;white-space:nowrap;z-index:10000007px;"><font color='red'><b>{'LBL_CONFIGURE_MAIL_SETTINGS'|@getTranslatedString:$MODULE}.<br> {$APP.LBL_PLEASE_CLICK} <a href="index.php?module=Users&action=AddMailAccount&record={$USERID}&return_module=Webmails&return_action=index">{$APP.LBL_HERE}</a> {$APP.LBL_TO_CONFIGURE}</b></font></div>
<!-- Shadow starts here -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" height="100%">
	<tr>
		<td valign=top align=right><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>

		<td class="showPanelBg" valign="top" width="95%" align=center >
		<!-- Email Client starts here -->
			<br>
			<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="mailClient">
				<tr>
					<td class="mailClientBg" width="7">&nbsp;</td>
					<td class="mailClientBg">
					<form name="massdelete" method="POST" onsubmit="VtigerJS_DialogBox.block();">
						<table width="100%"  border="0" cellspacing="0" cellpadding="0">
							<!-- Compose, Settings and Name image -->
							<tr>
								<td colspan="3" style="vertical-align:middle;">
									<table border=0 cellspacing=0 cellpadding=0 width=100%>
									<tr>
									<td align=left>
									
										<table cellpadding="5" cellspacing="0" border="0">
											<tr>
												<td nowrap style="padding-left:20px;padding-right:20px" class=small>
													<img src="{'compose.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />
													&nbsp;<a href="javascript:;" onClick="OpenCompose('','create');" >{'LBL_COMPOSE'|@getTranslatedString:$MODULE}</a>
												</td>
												<td nowrap style="padding-left:20px;padding-right:20px" class=small>
													<img src="{'webmail_settings.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />
													&nbsp;<a href="index.php?module=MailManager&action=index&parenttab={$CATEGORY}" >{'LBL_SETTINGS'|@getTranslatedString:$MODULE}</a>
												</td>
											</tr>
											</table>
										</td>
										<td align=right>
											<table >
											<tr>
												<td class="componentName" align=right>{'LBL_VTIGER_EMAIL_CLIENT'|@getTranslatedString:$MODULE}<!-- <img src="{'titleMailClient.gif'|@vtiger_imageurl:$THEME}" align="right"/> --></td>
											</tr>
											</table>
									</td>
									</tr>
									</table>
									
								</td>
							</tr>
							<!-- Columns -->
							<tr>
							<td width="18%" class="big mailSubHeader" ><b>{'LBL_EMAIL_FOLDERS'|@getTranslatedString:$MODULE}</b></td>
							<td>&nbsp;</td>
							<td width="82%" class="big mailSubHeader" align="left"><span id="mail_fldrname"><b>{'LBL_ALLMAILS'|@getTranslatedString:$MODULE}</b></span></td>

							</tr>
							
							<tr>
								<td rowspan="6" class="MatrixLayer1" valign="top" bgcolor="#FFFFFF" style="padding:5px; " align="left" >
								<!-- Mailbox Tree -->
								<!-- Inbox -->
								<img src="{'folder_.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;<b class="txtGreen">{'LBL_INBOX'|@getTranslatedString:$MODULE}</b>
								<ul style="list-style-type:none;margin-left:10px;margin-top:5px;padding:2px">
									<li><img src="{'folder.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;&nbsp;
										<a href="javascript:;" onClick="ShowFolders(6)" class="webMnu">{'LBL_QUAL_CONTACT'|@getTranslatedString:$MODULE}</a>&nbsp;<b></b>
									</li>
									<li><img src="{'mymail.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;&nbsp;
									<a href="index.php?module=MailManager&action=index&parenttab={$CATEGORY}" >{'LBL_MY_MAILS'|@getTranslatedString:$MODULE}</a>&nbsp;<b></b>
									</li>
								</ul>
								{include file="SentMailFolders.tpl"}
								</td>
								<!-- All mails pane -->
								<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td class="hdrNameBg">
									<!-- Command Buttons and Search Email -->
									
									
								</td>
							</tr>
							<!-- Mail Subject Headers list -->
							<tr>
								<td>&nbsp;</td>
								<td align="left">
									<div id="email_con">
									{include file="EmailContents.tpl"}
									</div>
								</td>
							</tr>
							
							<tr>
								<td>&nbsp;</td>
								<td valign="top">
									<div id="EmailDetails"> 
									{include file="EmailDetails.tpl"}
									</div>
								</td>
							</tr>
						</table>
						</form>
						<br>
					</td>
					<td class="mailClientBg" width="7">&nbsp;</td>
				</tr>
				<!-- <tr>
					<td width="7" height="8" style="font-size:1px;font-family:Arial, Helvetica, sans-serif;"><img src="{'bottom_left.jpg'|@vtiger_imageurl:$THEME}" align="bottom"  /></td>
					<td bgcolor="#ECECEC" height="8" style="font-size:1px;" ></td>
					<td width="8" height="8" style="font-size:1px;font-family:Arial, Helvetica, sans-serif;"><img src="{'bottom_right.jpg'|@vtiger_imageurl:$THEME}" align="bottom" /></td>
				</tr>-->
			</table><br/>
		</td>
		<td valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
		
	</tr>
</table>
<!-- END -->

