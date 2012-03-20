<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Users/ChangePassword.php,v 1.2 2004/10/29 09:55:10 jack Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
// This file is used for all popups on this module
// The popup_picker.html file is used for generating a list from which to find and choose one instance.

global $theme;
require_once('modules/Users/Users.php');
require_once('themes/'.$theme.'/layout_utils.php');
require_once('include/logging.php');

global $app_strings;
global $mod_strings;

$mod_strings['ERR_ENTER_OLD_PASSWORD'];

insert_popup_header($theme);
?>
<script type='text/javascript' src="include/js/general.js"></script>
<script type='text/javascript' language='JavaScript'>

function set_password(form) {
	if (form.is_admin.value == 1 && trim(form.old_password.value) == "") {
		alert("<?php echo $mod_strings['ERR_ENTER_OLD_PASSWORD']; ?>");
		return false;
	}
	if (trim(form.new_password.value) == "") {
		alert("<?php echo $mod_strings['ERR_ENTER_NEW_PASSWORD']; ?>");
		return false;
	}
	if (trim(form.confirm_new_password.value) == "") {
		alert("<?php echo $mod_strings['ERR_ENTER_CONFIRMATION_PASSWORD']; ?>");
		return false;
	}

	if (trim(form.new_password.value) == trim(form.confirm_new_password.value)) {
		if (form.is_admin.value == 1) window.opener.document.DetailView.old_password.value = form.old_password.value;
		window.opener.document.DetailView.new_password.value = form.new_password.value;
		window.opener.document.DetailView.return_module.value = 'Users';
		window.opener.document.DetailView.return_action.value = 'DetailView';
		window.opener.document.DetailView.changepassword.value = 'true';
		window.opener.document.DetailView.return_id.value = window.opener.document.DetailView.record.value;
		window.opener.document.DetailView.action.value = 'Save';
		window.opener.document.DetailView.submit();
		return true;
	}
	else {
		alert("<?php echo $mod_strings['ERR_REENTER_PASSWORDS']; ?>");
		return false;
	}
}
</script>

<form name="ChangePassword" onsubmit="VtigerJS_DialogBox.block();">
<?php echo get_form_header($mod_strings['LBL_CHANGE_PASSWORD'], "", false); ?>

<table width='100%' cellspacing='0' cellpadding='5' border='0' class="small">
<tr>
	<td class="detailedViewHeader" colspan="2"><b><?php echo $mod_strings['LBL_CHANGE_PASSWORD']; ?></b></td>
</tr>
<?php if (!is_admin($current_user)) {
	echo "<tr>";
	echo "<td width='40%' class='dvtCellLabel' align='right'><b> ".$mod_strings['LBL_OLD_PASSWORD']."</b></td>\n";
	echo "<td width='60%' class='dvtCellInfo'><input name='old_password' type='password' tabindex='1' size='15'></td>\n";
	echo "<input name='is_admin' type='hidden' value='1'>";
	echo "</tr><tr>\n";
}
else echo "<input name='old_password' type='hidden'><input name='is_admin' type='hidden' value='0'>";
?>
<td width='40%' class='dvtCellLabel' nowrap align="right"><b><?php echo $mod_strings['LBL_NEW_PASSWORD']; ?></b></td>
<td width='60%' class='dvtCellInfo'><input name='new_password' type='password' tabindex='1' size='15'></td>
</tr><tr>
<td width='40%' class='dvtCellLabel' nowrap align="right"><b><?php echo $mod_strings['LBL_CONFIRM_PASSWORD']; ?></b></td>
<td width='60%' class='dvtCellInfo'><input name='confirm_new_password' type='password' tabindex='1' size='15'></td>
</tr><tr>
<td width='40%' class='dataLabel'></td>
<td width='60%' class='dvtCellInfo'></td>
</td></tr>
</table>
<br>
<table width='100%' cellspacing='0' cellpadding='1' border='0'>
<tr>
<td align='right'><input title='<?php echo $app_strings['LBL_SAVE_BUTTON_TITLE']; ?>' accessKey='<?php echo $app_strings['LBL_SAVE_BUTTON_KEY']; ?>' class='crmbutton small save' LANGUAGE=javascript onclick='if (set_password(this.form)) window.close(); else return false;' type='submit' name='button' value='  <?php echo $app_strings['LBL_SAVE_BUTTON_LABEL']; ?>  '></td>
<td align='left'><input title='<?php echo $app_strings['LBL_CANCEL_BUTTON_TITLE']; ?>' accessyKey='<?php echo $app_strings['LBL_CANCEL_BUTTON_KEY']; ?>' class='crmbutton small cancel' LANGUAGE=javascript onclick='window.close()' type='submit' name='button' value='  <?php echo $app_strings['LBL_CANCEL_BUTTON_LABEL']; ?>  '></td>
</tr>

<script language="JavaScript">
document.ChangePassword.new_password.focus();
</script>
</table>
</form>
<br>

