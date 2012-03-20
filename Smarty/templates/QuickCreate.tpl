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
<body class=small>
{include file='QuickCreateHidden.tpl'}
<table border="0" align="center" cellspacing="0" cellpadding="0" width="90%" class="mailClient mailClientBg">
<tr>
<td>
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class=small>
	<tr>
		<td width="90%" class="mailSubHeader" background="{'qcBg.gif'|@vtiger_imageurl:$THEME}"><b >{$APP.LBL_CREATE_BUTTON_LABEL} {$QCMODULE}</b></td>
		<td nowrap class="mailSubHeader moduleName" align=right><i>{$APP.LBL_QUICK_CREATE}</i></td></tr>
	</table>

	<table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
	<tr>
		<td>
		
			<!-- quick create UI starts -->
			<table border="0" cellspacing="0" cellpadding="5" width="100%" class="small" bgcolor="white" >
			{assign var="fromlink" value="qcreate"}
			{foreach item=subdata from=$QUICKCREATE}
				<tr>
					{foreach key=mainlabel item=maindata from=$subdata}
						{include file='EditViewUI.tpl'}										
					{/foreach}
				</tr>
			{/foreach}
						
			</table>	
		
		<!-- save cancel buttons -->
		<table border="0" cellspacing="0" cellpadding="5" width="100%" class=qcTransport>
			<tr>
				{if $MODULE eq 'Accounts'}
				<td width=50% align=right><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.action.value='Save';  if(getFormValidate())AjaxDuplicateValidate('Accounts','accountname',this.form);" type="button" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" ></td>
				{else}
				<td width=50% align=right><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}" style="width:70px" ></td>
				{/if}

				<td width="50%" align="left">
					<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="hide('qcform'); $('qccombo').options.selectedIndex=0;" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
				</td>
			</tr>
		</table>

		</td>
	</tr>
	</table>
</td>
</tr>
</table>
{if $QCMODULE eq 'Event'}
<SCRIPT id="qcvalidate">
        var qcfieldname = new Array('subject','date_start','time_start','eventstatus','activitytype','due_date','time_end','due_date','time_end');
        var qcfieldlabel = new Array('Subject','Start Date & Time','Start Date & Time','Status','Activity Type','End Date & Time','End Date & Time','End Date & Time','End Date & Time');
        var qcfielddatatype = new Array('V~M','DT~M~time_start','T~O','V~O','V~O','D~M~OTH~GE~date_start~Start Date & Time','T~M','DT~M~time_end','T~O~OTH~GE~time_start~Start Date & Time');
</SCRIPT>
{elseif $QCMODULE eq 'Todo'}
<SCRIPT id="qcvalidate">
	var qcfieldname = new Array('subject','date_start','time_start','taskstatus');
        var qcfieldlabel = new Array('Subject','Start Date & Time','Start Date & Time','Status');
        var qcfielddatatype = new Array('V~M','DT~M~time_start','T~O','V~O');
</SCRIPT>
{else}
<SCRIPT id="qcvalidate">
        var qcfieldname = new Array({$VALIDATION_DATA_FIELDNAME});
        var qcfieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
        var qcfielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
</SCRIPT>
{/if}
</form>
</body>
