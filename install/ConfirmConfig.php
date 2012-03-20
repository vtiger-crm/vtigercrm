<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once('modules/Utilities/Currencies.php');

session_start();

if (isset($_REQUEST['db_hostname'])) $_SESSION['config_file_info']['db_hostname'] = $db_hostname = $_REQUEST['db_hostname'];
if (isset($_REQUEST['db_username'])) $_SESSION['config_file_info']['db_username'] = $db_username = $_REQUEST['db_username'];
if (isset($_REQUEST['db_password'])) $_SESSION['config_file_info']['db_password'] = $db_password = $_REQUEST['db_password'];
if (isset($_REQUEST['db_name'])) $_SESSION['config_file_info']['db_name'] = $db_name = $_REQUEST['db_name'];
if (isset($_REQUEST['db_type'])) $_SESSION['config_file_info']['db_type'] = $db_type = $_REQUEST['db_type'];

if (isset($_REQUEST['site_URL'])) $_SESSION['config_file_info']['site_URL']= $site_URL = $_REQUEST['site_URL'];
if (isset($_REQUEST['root_directory'])) $_SESSION['config_file_info']['root_directory'] = $root_directory = $_REQUEST['root_directory'];

if (isset($_REQUEST['currency_name'])) $_SESSION['config_file_info']['currency_name'] = $currency_name = $_REQUEST['currency_name'];
if (isset($_REQUEST['admin_email'])) $_SESSION['config_file_info']['admin_email']= $admin_email = $_REQUEST['admin_email'];

if (isset($_REQUEST['currency_name'])) $_SESSION['installation_info']['currency_name'] = $currency_name = $_REQUEST['currency_name'];
if (isset($_REQUEST['check_createdb'])) $_SESSION['installation_info']['check_createdb'] = $check_createdb = $_REQUEST['check_createdb'];
if (isset($_REQUEST['root_user'])) $_SESSION['installation_info']['root_user'] = $root_user = $_REQUEST['root_user'];
if (isset($_REQUEST['root_password'])) $_SESSION['installation_info']['root_password'] = $root_password = $_REQUEST['root_password'];
if (isset($_REQUEST['admin_email'])) $_SESSION['installation_info']['admin_email']= $admin_email = $_REQUEST['admin_email'];
if (isset($_REQUEST['admin_password'])) $_SESSION['installation_info']['admin_password'] = $admin_password = $_REQUEST['admin_password'];

if (isset($_REQUEST['create_utf8_db'])) 
	$_SESSION['installation_info']['create_utf8_db'] = $create_utf8_db = 'true';
else 
	$_SESSION['installation_info']['create_utf8_db'] = $create_utf8_db = 'false';

if (isset($_REQUEST['db_populate'])) 
	$_SESSION['installation_info']['db_populate'] = $db_populate = 'true';
else
	$_SESSION['installation_info']['db_populate'] = $db_populate = 'false';

if(isset($currency_name)){
	$_SESSION['installation_info']['currency_code'] = $currencies[$currency_name][0];
	$_SESSION['installation_info']['currency_symbol'] = $currencies[$currency_name][1];
}

$create_db = false;
if(isset($_REQUEST['check_createdb']) && $_REQUEST['check_createdb'] == 'on') $create_db = true;

$dbCheckResult = Installation_Utils::checkDbConnection($db_type, $db_hostname, $db_username, $db_password, $db_name, $create_db, $create_utf8_db, $root_user, $root_password);
$next = $dbCheckResult['flag'];
$error_msg = $dbCheckResult['error_msg'];
$error_msg_info = $dbCheckResult['error_msg_info'];
$db_utf8_support = $dbCheckResult['db_utf8_support'];
$vt_charset = ($db_utf8_support)? "UTF-8" : "ISO-8859-1";
$_SESSION['config_file_info']['vt_charset']= $vt_charset;

if($next == true) {
	$_SESSION['authentication_key'] = md5(microtime());
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_CONFIRM_SETTINGS']?></title>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
</head>

<body class="small cwPageBg" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
	<br>
	<!-- Table for cfgwiz starts -->
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
			<td class="cwHeadBg" align=left><img src="include/install/images/configwizard.gif" alt="<?php echo $installationStrings['LBL_CONFIG_WIZARD']; ?>" hspace="20" title="<?php echo $installationStrings['LBL_CONFIG_WIZARD']; ?>"></td>
			<td class="cwHeadBg1" align=right><img src="include/install/images/vtigercrm5.gif" alt="<?php echo $installationStrings['LBL_VTIGER_CRM_5']; ?>" title="<?php echo $installationStrings['LBL_VTIGER_CRM_5']; ?>"></td>
			<td class="cwHeadBg1" width=2%></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
			<td background="include/install/images/topInnerShadow.gif" align=left><img height="10" src="include/install/images/topInnerShadow.gif" ></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=2 width=80% align=center>
		<tr>
			<td class="small" bgcolor="#4572BE" align=center>
				<!-- Master display -->
				<table border=0 cellspacing=0 cellpadding=0 width=97%>
					<tr>
						<td width=80% valign=top class="cwContentDisplay" align=center>
							<table border=0 cellspacing=0 cellpadding=10 width=100%>
								<tr>
									<td align=left colspan=2 class="small paddingTop">
										<span class="bigHeading"><?php echo $installationStrings['LBL_CONFIRM_CONFIG_SETTINGS']; ?></span>
										<br>
						  				<hr noshade size=1>
						  			</td>
						  		</tr>
								<?php if($error_msg) : ?>
								<tr>
									<td align=left class="small" colspan=2 width=50% style="padding-left:10px">
										<div style="background-color:#ff0000;color:#ffffff;padding:5px">
											<b><?php echo $error_msg ?></b>
										</div>
										<?php if($error_msg_info) : ?>
											<p><?php echo $error_msg_info ?><p>
										<?php endif; ?>
									</td>
								</tr>
								<?php endif; ?>
								<tr>
									<td align=left class="small" width=50% style="padding-left:10px">
										<table width="100%" cellpadding="0" border="0" align=center class="level3" cellspacing="1">
											<tr>
												<td colspan=2><strong><?php echo $installationStrings['LBL_DATABASE_CONFIGURATION']; ?></strong><hr noshade size=1></td>
											</tr>
											<tr>
												<td noWrap width="40%"><?php echo $installationStrings['LBL_DATABASE_TYPE']; ?></td>
												<td align="left" nowrap> <font class="dataInput"><i><?php if (isset($db_type)) echo "$db_type"; ?></i></font></td>
											</tr>
											<tr>
												<td noWrap width="40%"><?php echo $installationStrings['LBL_DATABASE_NAME']; ?></td>
												<td align="left" nowrap> <font class="dataInput"><i><?php if (isset($db_name)) echo "$db_name"; ?></i></font></td>
											</tr>
											<tr>
												<td noWrap width="40%"><?php echo $installationStrings['LBL_DATABASE'].' '.$installationStrings['LBL_UTF8_SUPPORT']; ?></td>
												<td align="left" nowrap> <font class="dataInput"><?php echo ($db_utf8_support)? $installationStrings['LBL_ENABLED'] : "<strong style='color:#DF0000';>{$installationStrings['LBL_NOT_ENABLED']}</strong>" ?></font>&nbsp;<a href="http://www.vtiger.com/products/crm/help/<?php echo $vtiger_current_version; ?>/vtiger_CRM_Database_UTF8Config.pdf" target="_blank"><?php echo $installationStrings['LBL_MORE_INFORMATION']; ?></a></td>
											</tr>
										</table>
									</td>
									<td align=left class="small" width=50% style="padding-left:10px">
										<table width="100%" cellpadding="0" border="0" align=center class="level3" cellspacing="1">
											<tr>
												<td colspan=2 ><strong><?php echo $installationStrings['LBL_SITE_CONFIGURATION']; ?></strong><hr noshade size=1></td>
											</tr>
											<tr>
												<td width="40%"><?php echo $installationStrings['LBL_URL']; ?></td>
												<td align="left"> <i><?php if (isset($site_URL)) echo $site_URL; ?></i></td>
											</tr>
											<tr>
												<td width="40%"><?php echo $installationStrings['LBL_DEFAULT_CHARSET']; ?></td>
												<td align="left"> <i><?php if (isset($vt_charset)) echo $vt_charset; ?></i></td>
											</tr>
											<tr>
												<td width="40%"><?php echo $installationStrings['LBL_CURRENCY_NAME']; ?></td>
												<td align="left"> <i><?php if (isset($currency_name)) echo $currency_name."(".$currencies[$currency_name][1].")"; ?></i></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan=2>	
										<table width="100%" cellpadding="0" border="0" align=center class="level3" cellspacing="1">
											<tr>
												<td colspan=3 ><strong><?php echo $installationStrings['LBL_USER_CONFIGURATION']; ?></strong><hr noshade size=1></td>
											</tr>
											<tr>
												<td width="40%"><?php echo $installationStrings['LBL_USERNAME']; ?></td>
												<td align="left" width="30%"> <i>admin</i></td>
											</tr>
											<tr>
												<td width="40%"><?php echo $installationStrings['LBL_EMAIL']; ?></td>
												<td align="left" width="30%"> <i><?php if (isset($admin_email)) echo $admin_email; ?></i></td>
											</tr>
										</table>
										<br>
										<table width="100%" cellpadding="5" border="0" class="small" >
											<tr>
												<td align="left" valign="bottom">
												<form action="install.php" method="post" name="form" id="form">
													<input type="hidden" name="file" value="SetInstallationConfig.php">
													<input type="submit" class="button" value="&#139;&#139;&nbsp;<?php echo $installationStrings['LBL_CHANGE']; ?>" title="<?php echo $installationStrings['LBL_CHANGE']; ?>" />
												</form>
												</td>
				
												<?php if($next) : ?>
												<td align="right" valign="bottom">
												<form action="install.php" method="post" name="form" id="form">
													<input type="hidden" name="mode" value="installation">
													<input type="hidden" name="file" value="SelectOptionalModules.php">
													<input type="submit" class="button" value="<?php echo $installationStrings['LBL_NEXT']; ?>&nbsp;&#155;&#155;" title="<?php echo $installationStrings['LBL_NEXT']; ?>" />
												</form>
												</td>
												<?php endif ?>
											</tr>
										</table>				
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<!-- Master display stops -->
				<br>
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
			<td background="include/install/images/bottomGradient.gif"><img src="include/install/images/bottomGradient.gif"></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
			<td align=center><img src="include/install/images/bottomShadow.jpg"></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
      	<tr>
        	<td class=small align=center> <a href="http://www.vtiger.com" target="_blank">www.vtiger.com</a></td>
      	</tr>
	</table>	
</body>
</html>