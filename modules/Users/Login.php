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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Users/Login.php,v 1.6 2005/01/08 13:15:03 jack Exp $
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
$theme_path="themes/".$theme."/";
$image_path="include/images/";

global $app_language;
//we don't want the parent module's string file, but rather the string file specifc to this subpanel
global $current_language;
$current_module_strings = return_module_language($current_language, 'Users');

 define("IN_LOGIN", true);

include_once('vtlib/Vtiger/Language.php');

// Retrieve username and password from the session if possible.
if(isset($_SESSION["login_user_name"]))
{
	if (isset($_REQUEST['default_user_name']))
		$login_user_name = trim(vtlib_purify($_REQUEST['default_user_name']), '"\'');
	else
		$login_user_name =  trim(vtlib_purify($_REQUEST['login_user_name']), '"\'');
}
else
{
	if (isset($_REQUEST['default_user_name']))
	{
		$login_user_name = trim(vtlib_purify($_REQUEST['default_user_name']), '"\'');
	}
	elseif (isset($_REQUEST['ck_login_id_vtiger'])) {
		$login_user_name = get_assigned_user_name($_REQUEST['ck_login_id_vtiger']);
	}
	else
	{
		$login_user_name = $default_user_name;
	}
	$_session['login_user_name'] = $login_user_name;
}

$current_module_strings['VLD_ERROR'] = base64_decode('UGxlYXNlIHJlcGxhY2UgdGhlIFN1Z2FyQ1JNIGxvZ29zLg==');

// Retrieve username and password from the session if possible.
if(isset($_SESSION["login_password"]))
{
	$login_password = trim(vtlib_purify($_REQUEST['login_password']), '"\'');
}
else
{
	$login_password = $default_password;
	$_session['login_password'] = $login_password;
}

if(isset($_SESSION["login_error"]))
{
	$login_error = $_SESSION['login_error'];
}

?>
<!--Added to display the footer in the login page by Dina-->
<style type="text/css">@import url("themes/<?php echo $theme; ?>/style.css");</style>
<script type="text/javascript" language="JavaScript">
<!-- Begin -->
function set_focus() {
	if (document.DetailView.user_name.value != '') {
		document.DetailView.user_password.focus();
		document.DetailView.user_password.select();
	}
	else document.DetailView.user_name.focus();
}
<!-- End -->
</script>


<br><br>
<div align="center">	
	<table border="0" cellpadding="0" cellspacing="0" width="700">
		<tr>
			<td align="right"><img src="themes/images/honestCRMTop.gif"></td>
		</tr>
	</table>
	<!-- key to check session_out in Ajax key=s18i14i22a19 -->
	<!-- Login Starts -->
	<table border="0" cellspacing="0" cellpadding="0" width=700>
		<tr>
			<td class="bg" width="50%"><img src="themes/images/vtigerName.gif" alt="vtiger CRM" title="vtiger CRM"></td>
			<td class="bg" align="right" width="50%"><img src="themes/images/honestCRM.gif" alt="The honest Open Source CRM" title="The honest Open Source CRM"></td>
		</tr>
		<tr>
			<td class="small z1" align="center">

				<img src="themes/images/bullets.gif">
			</td>
	       		<td class="small z2" align="center">
			<?php
				if (isset($_REQUEST['ck_login_language_vtiger'])) {
					$display_language = vtlib_purify($_REQUEST['ck_login_language_vtiger']);
				}
				else {
					$display_language = $default_language;
				}

				if (isset($_REQUEST['ck_login_theme_vtiger'])) {
					$display_theme = vtlib_purify($_REQUEST['ck_login_theme_vtiger']);
				}
				else {
					$display_theme = $default_theme;
				}
			?>
			<!-- Sign in form -->
				<br>
				<form action="index.php" method="post" name="DetailView" id="form">
				<input type="hidden" name="module" value="Users">
				<input type="hidden" name="action" value="Authenticate">
				<input type="hidden" name="return_module" value="Users">
				<input type="hidden" name="return_action" value="Login">
					<table border="0" cellpadding="0" cellspacing="0" width="80%">
					<tr>
						<td class="signinHdr"><img src="themes/images/signin.gif" alt="<?php echo $app_strings['LBL_SIGN_IN']?>" title="<?php echo $app_strings['LBL_SIGN_IN']?>"></td>
					</tr>
					<tr>
						<td class="small">
						<!-- form elements -->
							<br>
							<table border="0" cellpadding="5" cellspacing="0" width="100%">
							<tr>
								<td class="small" align="right" width="30%"><?php echo $current_module_strings['LBL_USER_NAME'] ?></td>
								<td class="small" align="left" width="70%"><input class="small" type="text" name="user_name" value="<?php echo $login_user_name ?>" tabindex="1"></td>
							</tr>
							<tr>
								<td class="small" align="right" width="30%"><?php echo $current_module_strings['LBL_PASSWORD'] ?></td>
								<td class="small" align="left" width="70%"><input class="small" type="password" size='20' name="user_password" value="<?php echo $login_password ?>" tabindex="2"></td>
							</tr>
							<tr bgcolor="#f5f5f5">
								<td class="small" align="right" width="30%"><?php echo $current_module_strings['LBL_THEME'] ?></td>
								<td class="small" align="left" width="70%"><select class="small" name='login_theme' style="width:70%" tabindex="3">
									<?php echo get_select_options_with_id(get_themes(), $display_theme) ?>
								</select></td>
							</tr>
							<tr bgcolor="#f5f5f5">
								<td class="small" align="right" width="30%"><?php echo $current_module_strings['LBL_LANGUAGE'] ?></td>
								<td class="small" align="left" width="70%"><select class="small" name='login_language' style="width:70%" tabindex="4">
									<!-- vtlib Customization -->
									<? /* php echo get_select_options_with_id(get_languages(), $display_language) */ ?>
									<?php echo get_select_options_with_id(Vtiger_Language::getAll(), $display_language) ?>
								</select></td>		
							</tr>
							<?php
							if( isset($_SESSION['validation'])){
							?>
							<tr>
								<td colspan="2"><font color="Red"> <?php echo $current_module_strings['VLD_ERROR']; ?> </font></td>
							</tr>
							<?php
							}
							else if(isset($login_error) && $login_error != "")
							{
							?>
							<tr>
								<td colspan="2"><b class="small"><font color="Brown">
							<?php echo $login_error ?>
								</font></b></td>
							</tr>
							<?php
							}
							?>
							<tr>
								<td class="small">&nbsp;</td>
								<td class="small"><input title="<?php echo $current_module_strings['LBL_LOGIN_BUTTON_TITLE'] ?>" alt="<?php echo $current_module_strings['LBL_LOGIN_BUTTON_TITLE'] ?>" accesskey="<?php echo $current_module_strings['LBL_LOGIN_BUTTON_TITLE'] ?>" src="themes/images/btnSignInNEW.gif" type="image" name="Login" value="  <?php echo $current_module_strings['LBL_LOGIN_BUTTON_LABEL'] ?>  "  tabindex="5"></td>
							</tr>
							</table>
							<br><br>
						</td>
					</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>
</div>
