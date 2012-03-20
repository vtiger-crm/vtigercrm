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
                {include file='Buttons_List1.tpl'} 
<link rel="stylesheet" type="text/css" href="themes/{$THEME}/webmail.css">
<table width="100%" border="0" cellpadding="0" cellspacing="0" height="100%">
   <tr>
	<td valign=top align=right><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" valign="top" width="95%"  style="padding-left:20px; ">
	<br>
	<!-- module Select Table -->
		<table class="settingsUI" width="100%"  border="0" cellspacing="0" cellpadding="0">
		   <tr>
			<td class="mailClientBg" width="7">&nbsp;</td>
			<td class="mailClientBg">

				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                		   <tr>
					<td colspan="3" style="padding:10px;vertical-align:middle;height:2px;">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						   <tr>
							<td width="10%">
								<img src="{'check_mail.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />
								&nbsp;<a href="javascript:;" class="webMnu" onclick="check_for_new_mail('{$MAILBOX}');" >{$MOD.LBL_CHK_MAIL}</a>
							</td>
							<td width="10%">
								<img src="{'compose.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />
								&nbsp;<a href="javascript:;" onclick="OpenComposer('','create');" class="webMnu">{$MOD.LBL_COMPOSE}</a>
							</td>
							<td width="20%" nowrap>
								<img src="{'webmail_settings.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />
								&nbsp;<a href="index.php?module=Users&action=AddMailAccount&record={$USERID}&return_module=Webmails&return_action=index" class="webMnu">{$MOD.LBL_SETTINGS}</a>
							</td>
							<td width="30%">
								<!--<img src="themes/images/webmail_settings.gif" align="absmiddle" />
								&nbsp;<a href="javascript:;" onclick="runEmailCommand('expunge','0');" class="webMnu">{$MOD.LBL_EXPUNGE_MAILBOX}</a>-->
							</td>
							<td>&nbsp;</td>
						   </tr>
						</table>
					</td>
				   </tr>
				   <tr>
					<td colspan=2> 
						<table border=0 width="100%" cellspacing=0 cellspacing=0>
						<tr>
						<td rowspan=6 valign=top class="MatrixLayer1">
							<table  border=0 width="100%" cellspacing=0 cellspacing=0>
							<tr>	
							<td width="20%"  class="big mailSubHeader"><b>{$MOD.LBL_EMAIL_FOLDERS}</b></td>
							</tr>
							<tr>
							<td>
								<img src="{'mymail.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;<span ><b class="txtGreen">{$MOD.LBL_MY_MAILS}</b>&nbsp;&nbsp;<span id="folderOpts" style="position:absolute;display:none">{$MOD.ADD_FOLDER}</span></span>
								<div id="box_list">
								<ul style="list-style-type:none;">

							{foreach item=row from=$BOXLIST}
								{foreach item=row_values from=$row}
									{$row_values}
								{/foreach}
							{/foreach}
							</ul></div> <br />

						<img src="{'sentmail.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;<b class="txtGreen">{$MOD.LBL_SENT_MAILS}</b>
						<ul style="list-style-type:none;">
							<li >
								<img src="{'opened_folder.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;&nbsp;
								<a href="index.php?module=Emails&action=ListView&parenttab=My Home Page&folderid=1&parenttab=My Home Page" class="small">{$MOD.LBL_ALLMAILS}</a>&nbsp;<b></b>
							</li>
							<li >
								<img src="{'opened_folder.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;&nbsp;
								<a href="index.php?module=Emails&action=ListView&folderid=2&parenttab=My Home Page" class="small">{$MOD.LBL_TO_CONTACTS}</a>&nbsp;<b></b>
							</li>
							<li >
								<img src="{'opened_folder.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;&nbsp;
								<a href="index.php?module=Emails&action=ListView&folderid=3&parenttab=My Home Page" class="small">{$MOD.LBL_TO_ACCOUNTS}</a>&nbsp;
							</li>	
							<li >
								<img src="{'opened_folder.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;&nbsp;
								<a href="index.php?module=Emails&action=ListView&folderid=4&parenttab=My Home Page" class="small">{$MOD.LBL_TO_LEADS}</a>&nbsp;
							</li>
							<li >
								<img src="{'opened_folder.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;&nbsp;
								<a href="index.php?module=Emails&action=ListView&folderid=5&parenttab=My Home Page" class="small">{$MOD.LBL_TO_USERS}</a>&nbsp;
							</li>
						</ul><br />
						<!--<img src="themes/images/webmail_trash.gif" align="absmiddle" />&nbsp;<b class="txtGreen">{$MOD.LBL_TRASH}</b>
						<ul style="list-style-type:none;">
							<li>
								<img src="themes/images/folder.gif" align="absmiddle" />&nbsp;&nbsp;
								<a href="#" class="small">{$MOD.LBL_JUNK_MAILS}</a>&nbsp;<b></b>
							</li>
						</ul>-->

						</td>
						</tr>
						</table>
						</td>	
						<td width="1%">&nbsp;</td>
					        <td rowspan=3 valign="top">
						 <table border=0 width="100%" cellspacing=0 cellspacing=0 class="MatrixLayer1">
					 	 <tr>
							<td width="79%" class="big mailSubHeader"><div id="nav"><span style="float:left">{$ACCOUNT} &gt; {$MAILBOX} {if $NUM_EMAILS neq 0}
						 {if $NUM_EMAILS neq 1}
							({$NUM_EMAILS} {$MOD.LBL_MESSAGES})
						 {else}
							({$NUM_EMAILS} {$MOD.LBL_MESSAGE})
						 {/if}
					 {/if}
						</span> <span style="float:right">{$NAVIGATION}</span></div></td>
						 </tr>
						 <tr>	
							<td class="hdrNameBg" style="height:30px;">
							<!-- Table to display Delete, Move To and Search buttons and options - Starts -->
							<table width="100%"  border="0" cellspacing="0" cellpadding="0">
				                	   <tr>
                				        	<td width="45%" id="move_pane">
								<input type="button" name="mass_del" value=" {$MOD.LBL_DELETE} "  class="crmbutton small delete" onclick="mass_delete();"/>
								{$FOLDER_SELECT}
				                        	</td>
								{if $DEGRADED_SERVICE eq 'false'}
			        	                		<td width="50%" align="right" nowrap>
									<font color="#000000">{$APP.LBL_SEARCH}</font>&nbsp;
									<input type="text" name="srch" class="importBox" id="search_input"  value="{$SEARCH_VALUE}"/>&nbsp;
									{$SEARCH_HTML}&nbsp;
									</td>
									<td width="5%">
									<input type="button" name="find" value=" {$APP.LBL_FIND_BUTTON} " class="crmbutton small create" onclick="search_emails();" />
									</td>
								{/if}
                	   				   </tr>
                					</table>
							</td>
						 </tr>
						 <tr>
							<td  align="left" valign="top" style="height:150px;">
							<div id="rssScroll" style="height:220px;">
							<!--div added to show info while moving mails-->
							<div id="show_msg" class="layerPopup" align="center" style="padding: 5px;font-weight:bold;width: 400px;display:none;z-index:10000"></div>
							<!-- Table to display the mails list -	Starts -->
							<form name="massdelete" method="post" onsubmit="VtigerJS_DialogBox.block();">
							<table cellspacing="1" cellpadding="3" border="0" width="100%" id="message_table">
							   <tr>
								<th class='tableHeadBg'><input type="checkbox" name="select_all" value="checkbox"  onclick="toggleSelect(this.checked,'selected_id');"/></th>
								{foreach item=element from=$LISTHEADER}
									{$element}
								{/foreach}
							   </tr>	
								{foreach item=row from=$LISTENTITY}
									{foreach item=row_values from=$row}
                				        			{$row_values}
									{/foreach}
								{/foreach}
							</table>
						 </form>
						 </div>
						 </td>

						 </tr>

						</table>
						</td>
						<td rowspan=9>
							&nbsp;
						</td>
				   		</tr>
				   		<tr>
					<td width="1%">&nbsp;</td>
					</tr>
				   <tr>
					<!-- td style="padding:1px;" align="left" -->
					<td width="1%">&nbsp;</td>
									   </tr>
				   <tr><td colspan="2" style="height:10px;">&nbsp;</td></tr>
				   <tr id="preview1" style="visibility:hidden" class="previewWindow">
					<td width="1%">&nbsp;</td>
					<!--td class="forwardBg"-->
					<td class="forwardBg">
					<!-- Table to display the Qualify, Reply, Forward, etc buttons - Starts -->
			   		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
					   <tr>
						<td width="75%" nowrap>
							&nbsp;<span id="qualify_button"><input type="button" name="Qualify2" value=" {$MOD.LBL_QUALIFY_BUTTON} " class="buttonok" /></span>&nbsp;
							<span id="reply_button"><input type="button" name="reply" value=" {$MOD.LBL_REPLY_TO_SENDER} " class="crmbutton small edit" /></span>&nbsp;
							<span id="reply_button_all"><input type="button" name="reply" value=" {$MOD.LBL_REPLY_ALL} " class="crmbutton small edit" /></span>&nbsp;
							<span id="forward_button"><input type="button" name="forward" value=" {$MOD.LBL_FORWARD_BUTTON} " class="crmbutton small edit" /></span>&nbsp;
							<span id="download_attach_button"><input type="button" name="download" value=" {$MOD.LBL_DOWNLOAD_ATTCH_BUTTON} " class="crmbutton small save" /></span>&nbsp;
						</td>
						<td width="25%" align="right"><span id="delete_button"><input type="button" name="Button" value=" {$APP.LBL_DELETE_BUTTON} "  class="crmbutton small delete" /></span>&nbsp;</td>
					   </tr>
					</table>
					<!-- Table to display the Qualify, Reply, Forward, etc buttons - Ends -->
					</td>
				   </tr>
				   <tr style="visibility:hidden" class="previewWindow">
					<td width="1%">&nbsp;</td>
					<td height="300">
			   		<table width="100%" class="MatrixLayer1" border="0" cellpadding="0" cellspacing="0">
					<tr>
					<td bgcolor="#FFFFFF" valign="top">
					<div id="preview2">
					   <span id="body_area" style="width:95%">
						<iframe id="email_description" width="100%" height="500" scrolling="no" frameBorder="0"></iframe>
					   </span>
					</div>
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
			</tr>
			<td colspan=4>
				&nbsp;
			</td>
		   	</tr>
		</table>
		<br />
	</td>
	<td valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</table>

<!-- END -->
