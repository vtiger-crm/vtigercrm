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


<script type="text/javascript" src="modules/{$MODULE}/{$SINGLE_MOD}.js"></script>

<!-- header - level 2 tabs -->
{include file='Buttons_List1.tpl'}	

<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%" class="small">
   <tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}" /></td>
	<td class="showPanelBg" valign="top" width="100%">

		<table  cellpadding="0" cellspacing="0" width="100%" border=0>
		   <tr>
			<td width="50%" valign=top>
				<form enctype="multipart/form-data" name="SelectExports" method="POST" action="modules/Accounts/MailerExport.php">
				<input type="hidden" name="module" value="{$MODULE}">
				<input type="hidden" name="step" value="export">
				<input type="hidden" name="action" value="MailerExport">
				<input type="hidden" name="exportwhere" value="{$EXPORTWHERE}">
				<input type="hidden" name="from" value="{$FROM}">
				<input type="hidden" name="fieldlist" value="{$FIELDLIST}">
				<input type="hidden" name="typelist" value="{$TYPELIST}">

				<br />
				<table align="center" cellpadding="5" cellspacing="0" width="80%" class="mailClient importLeadUI small" border="0">
				   <tr>
					<td colspan="2" height="50" valign="middle" align="left" class="mailClientBg  genHeaderSmall">{$MOD.LBL_MAILER_EXPORT}</td>
				   </tr>
				   <tr >
					<td colspan="2" align="left" valign="top" style="padding-left:40px;">
					<br>
						<span class="genHeaderGray">{$MOD.LBL_MAILER_EXPORT_CONTACTS_TYPE}</span>&nbsp; 
						<span class="genHeaderSmall">{$MOD.LBL_MAILER_EXPORT_CONTACTS_DESCR}</span> 
					</td>
				   </tr>
				   {foreach from=$QUERYFIELDS name=querysel item=myVal}
				     {if $smarty.foreach.querysel.index % 2 == 0}
				       <tr>
				     {/if}
				     <td align="left" valign="top" width="25%" class=small  style="padding-left:40px;">
				     {if $myVal.uitype == 1}
					<input type=text name={$myVal.columnname} size=13>
					{elseif $myVal.uitype == 15 || $myVal.uitype == 56}
				       		{html_options name=$myVal.columnname  options=$myVal.value}
				     {/if}
			            &nbsp;<b>{$myVal.fieldlabel}</b></td>
				     {if $smarty.foreach.querysel.index % 2 > 0}
				       </tr>
				     {/if}
				   {/foreach}
				   <input type="hidden" name="query" value="{$fieldList}">
				   <tr ><td align="left" valign="top" colspan="2">&nbsp;</td></tr>
          <tr >
					<td colspan="2" align="left" valign="top" style="padding-left:40px;">
					<br>
						<span class="genHeaderGray">{$MOD.LBL_MAILER_EXPORT_RESULTS_TYPE}</span>&nbsp; 
						<span class="genHeaderSmall">{$MOD.LBL_MAILER_EXPORT_RESULTS_DESCR}</span> 
					</td>
				   </tr>
				   <tr >
					<td align="right" valign="top" width="50%" class=small><b>{$MOD.LBL_EXPORT_RESULTS_EMAIL} </b></td>
					<td align="left" valign="top" width="50%" style="padding-left:40px;">
          <input type="radio" name="export_type" checked value="email">
					</td>
				   </tr>
				   <tr >
					<td align="right" valign="top" width="50%" class=small style="padding-left:40px;"><b>{$MOD.LBL_EXPORT_RESULTS_EMAIL_CORP} </b></td>
					<td align="left" valign="top" width="50%" style="padding-left:40px;">
				        <input type="radio" name="export_type" value="emailplus"> </td>
				   </tr>
				   <tr >
					<td align="right" valign="top" width="50%" class=small style="padding-left:40px;"><b>{$MOD.LBL_EXPORT_RESULTS_FULL} </b></td>
					<td align="left" valign="top" width="50%" style="padding-left:40px;">
          <input type="radio" name="export_type" value="full"></td>
				   </tr>
			   <tr ><td colspan="2" height="50" style="padding-left:40px;">&nbsp;</td></tr>
				    <tr >
						<td colspan="2" align="right" style="padding-right:40px;" class="reportCreateBottom">
							<input title="{$MOD.LBL_EXPORT_RESULTS_GO}" accessKey="" class="crmButton small save" type="submit" name="button" value="  {$MOD.LBL_EXPORT_RESULTS_GO} &rsaquo; ">
						</td>
						<!-- ADDED FOR 5.0.4 GA; STARTS-->
						<td colspan="2" align="right" style="padding-right:40px;" class="reportCreateBottom">
							<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="window.history.back()" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
						</td>
						<!-- ADDED FOR 5.0.4 GA ; ENDS-->
				   </tr>				</form>
				 </table>
				<br>
			</td>
		   </tr>
		</table>

	</td>
	<td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}" /></td>
   </tr>
</table>
