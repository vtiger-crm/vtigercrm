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
<div id="EditInv" class="layerPopup">
<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
<tr>
	<td class="layerPopupHeading" align="left">{$NOTIFY_DETAILS.label}</td>
	<td align="right" class="small"><img onClick="hide('editdiv');" style="cursor:pointer;" src="{'close.gif'|@vtiger_imageurl:$THEME}" align="middle" border="0"></td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
<tr>
	<td class="small">
	<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
	<tr>
		<td colspan="2">
			<b><font color="red">*</font>{$CMOD.LBL_NOTE_DO_NOT_REMOVE_INFO}</b>
		</td>
	</tr>
	<tr>
		<td align="right"  class="cellLabel small" width="40%"><b>{$MOD.LBL_STATUS} :</b></td>
	<td align="left"  class="cellText small" width="60%">
		<select class="small" id="notify_status" name="notify_status">
	{if $NOTIFY_DETAILS.status eq 1}
		<option value="1" "selected">{$MOD.LBL_ACTIVE}</option>
		<option value="0">{$MOD.LBL_INACTIVE}</option>
	{else}
		<option value="1">{$MOD.LBL_ACTIVE}</option>
		<option value="0" "selected">{$MOD.LBL_INACTIVE}</option>
	{/if}
	</select>
	</td>
	</tr>
	
	<tr>
		<td align="right" class="cellLabel small"><b>{$MOD.LBL_SUBJECT} : </b></td>
		<td align="left" class="cellText small"><input class="txtBox" id="notifysubject" name="notifysubject" value="{$NOTIFY_DETAILS.subject}" size="40" type="text"></td>
	</tr>
	<tr>
		<td align="right" valign="top" class="cellLabel small"><b>{$MOD.LBL_MESSAGE} : </b></td>
		<td align="left" class="cellText small"><textarea id="notifybody" name="notifybody" class="txtBox" rows="5" cols="40">{$NOTIFY_DETAILS.body}</textarea></td>
	</tr>
	</table>
	</td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
<tr>
	<td align="center" class="small">
		<input name="save" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" type="button" onClick="fetchSaveNotify('{$NOTIFY_DETAILS.id}')">
		<input name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" type="button" onClick="hide('editdiv');">
	</td>
	</tr>
</table>
</div>
