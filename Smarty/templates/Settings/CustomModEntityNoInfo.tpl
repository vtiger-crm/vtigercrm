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

<table width="100%"  border="0" cellspacing="0" cellpadding="5">

<tr>
	<td nowrap class="small cellLabel">
		<strong>{$SELMODULE|@getTranslatedString} {$MOD.LBL_MODULE_NUMBERING}</strong>
	</td>
	<td width="100%" class="small cellText">
		 <b>{$STATUSMSG}</b>
	</td>
	<td width="80%" nowrap class="small cellText" align=right>
		<b>{$MOD.LBL_MODULE_NUMBERING_FIX_MISSING}</b>
		<input type="button" class="crmbutton small create" value="{$APP.LBL_APPLY_BUTTON_LABEL}" onclick="updateModEntityExisting(this, this.form);"/>
	</td>
</tr>

<tr>
	<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_USE_PREFIX}</strong></td>
    <td width="80%" colspan=2 class="small cellText">
	<input type="text" name="recprefix" class="small" style="width:30%" value="{$MODNUM_PREFIX}"  />
	</td>
</tr>
<tr>
	<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_START_SEQ}<font color='red'>*</font></strong></td>
	<td width="80%" colspan=2 class="small cellText">
	<input type="text" name="recnumber" class="small" style="width:30%" value="{$MODNUM}"  />
	</td>
</tr>

<tr>
	<td width="20%" nowrap colspan="3" align ="center">
		<input type="button" name="Button" class="crmbutton small save" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="updateModEntityNoSetting(this, this.form);" />
		<input type="button" name="Button" class="crmbutton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="history.back(-1);" /></td>
	</td>
</tr>
</table>

