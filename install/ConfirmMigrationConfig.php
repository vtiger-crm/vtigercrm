<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
session_start();
		
if (isset($_REQUEST['source_directory'])) {
	$source_directory = $_REQUEST['source_directory'];
	if(!empty($source_directory)){
		$tmp = strlen($source_directory);
		if($source_directory[$tmp-1]!= "/" && $source_directory[$tmp-1]!= "\\"){
			$source_directory .= "/";
		}
		$_SESSION['migration_info']['source_directory'] = $source_directory;
	}
} else {
	$source_directory = $_SESSION['migration_info']['source_directory'];
}

if (isset($_REQUEST['root_directory'])) {
	$_SESSION['migration_info']['root_directory'] = $root_directory = $_REQUEST['root_directory'];
} else {
	$root_directory = $_SESSION['migration_info']['root_directory'];
}
if (isset($_REQUEST['user_name'])) { 
	$_SESSION['migration_info']['user_name'] = $user_name = $_REQUEST['user_name'];
} else {
	$user_name = $_SESSION['migration_info']['user_name'];
}
if (isset($_REQUEST['user_pwd'])) {
	$_SESSION['migration_info']['user_pwd'] = $user_pwd = $_REQUEST['user_pwd'];
} else {
	$user_pwd = $_SESSION['migration_info']['user_pwd'];
}
if (isset($_REQUEST['old_version'])) { 
	$_SESSION['migration_info']['old_version'] = $old_version = $_REQUEST['old_version'];
} else {
	$old_version = $_SESSION['migration_info']['old_version'];
}
if (isset($_REQUEST['new_dbname'])) { 
	$_SESSION['migration_info']['new_dbname'] = $new_dbname = $_REQUEST['new_dbname'];
} else {
	$new_dbname = $_SESSION['migration_info']['new_dbname'];
}

$dbVerifyResult = Migration_Utils::verifyMigrationInfo($_SESSION['migration_info']);
$next = $dbVerifyResult['flag'];
$error_msg = $dbVerifyResult['error_msg'];
$error_msg_info = $dbVerifyResult['error_msg_info'];

$oldDbName = $dbVerifyResult['old_dbname'];
$configFileInfo = $dbVerifyResult['config_info'];

$dbType = $configFileInfo['db_type'];
$dbHostName = $configFileInfo['db_hostname'];
$newDbName = $configFileInfo['db_name'];

if($next == true) {
	$_SESSION['authentication_key'] = md5(microtime());
	$_SESSION['config_file_info'] = $configFileInfo;

	require_once('install/VerifyDBHealth.php');

	if($_SESSION[$newDbName.'_'.$dbHostName.'_HealthApproved'] != true || $_SESSION['pre_migration'] != true) {
		header("Location:install.php?file=PreMigrationActions.php");
	} else {
		$innodbEngineCheck = true;
	}
	
	if($oldDbName == $newDbName && empty($_REQUEST['forceDbCheck'])) {
		header("Location:install.php?file=PreMigrationActions.php");
	}
}

include("modules/Migration/versions.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_CONFIRM_SETTINGS']?></title>
	<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>		
	<script type="text/javascript" src="include/js/general.js"></script>
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
		<td background="include/install/images/topInnerShadow.gif" colspan=2 align=left><img height="10" src="include/install/images/topInnerShadow.gif" ></td>

	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
		<tr>
			<td class="small" bgcolor="#4572BE" align=center>
				<!-- Master display -->
				<table border=0 cellspacing=0 cellpadding=0 width=97%>
					<tr id="confirmSettingsWindow">
						<td width=80% valign=top class="cwContentDisplay" align=center colspan=2>
							<table width="100%" cellspacing="0" cellpadding="10" border="0">
								<tr>
									<td align="left" colspan="2" class="small paddingTop">
										<span class="bigHeading"><?php echo $installationStrings['LBL_CONFIRM_CONFIG_SETTINGS']; ?></span>
										<br/>
				  						<hr size="1" noshade=""/>
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
									<td width="80%" align="left" style="padding-left: 10%;" class="small">
										<table width="100%" cellspacing="1" cellpadding="0" border="0" align="center" class="level3">
											<tr>
												<td colspan="2"><strong><?php echo $installationStrings['LBL_DATABASE_CONFIGURATION']; ?></strong><hr size="1" noshade=""/></td>
											</tr>
											<tr>
												<td width="40%" nowrap=""><?php echo $installationStrings['LBL_DATABASE_TYPE']; ?></td>
												<td nowrap="" align="left"> <font class="dataInput"><i><?php echo $dbType; ?></i></font></td>
											</tr>
											<tr>
												<td width="40%" nowrap=""><?php echo $installationStrings['LBL_OLD']. ' ' .$installationStrings['LBL_DATABASE_NAME']; ?></td>
													<td nowrap="" align="left"> <font class="dataInput"><i><?php echo $oldDbName; ?></i></font></td>
												</tr>
											<tr>
												<td width="40%" nowrap=""><?php echo $installationStrings['LBL_NEW']. ' ' .$installationStrings['LBL_DATABASE_NAME']; ?></td>
												<td nowrap="" align="left"> <font class="dataInput"><?php echo $newDbName; ?></font></td>
											</tr>
											<tr>
												<td width="40%" nowrap=""><?php echo $installationStrings['LBL_INNODB_ENGINE_CHECK']; ?></td>
												<td nowrap="" align="left">
													<?php if ($innodbEngineCheck == 1) { ?>
													<font class="dataInput"><?php echo $installationStrings['LBL_FIXED']; ?></font>
													<?php } else { ?>
													<font class="dataInput"><span class="redColor"><?php echo $installationStrings['LBL_NOT_FIXED']; ?></span></font></td>
													<?php } ?>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td width="80%" align="left" style="padding-left: 10%;" class="small">
										<table width="100%" cellspacing="1" cellpadding="0" border="0" align="center" class="level3">
											<tr>
												<td colspan="2"><strong><?php echo $installationStrings['LBL_SOURCE_CONFIGURATION']; ?></strong><hr size="1" noshade=""/></td>
											</tr>
											<tr>
												<td width="40%"><?php echo $installationStrings['LBL_PREVIOUS_INSTALLATION_VERSION']; ?></td>
												<td align="left"> <i><?php echo $versions[$old_version]; ?></i></td>
											</tr>
											<tr>
												<td width="40%"><?php echo $installationStrings['LBL_PREVIOUS_INSTALLATION_PATH']; ?></td>
												<td align="left"> <i><?php echo $source_directory; ?></i></td>
											</tr>
											<tr>
												<td width="40%"><?php echo $installationStrings['LBL_NEW_INSTALLATION_PATH']; ?></td>
												<td align="left"> <i><?php echo $root_directory; ?></i></td>
											</tr>
											<tr>
												<td width="40%">Admin <?php echo $installationStrings['LBL_USER_NAME']; ?></td>
												<td align="left"> <i><?php echo $user_name; ?></i></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td align="left">
										<form action="install.php" method="post" name="form" id="form">
											<input type="hidden" name="file" value="SetMigrationConfig.php">
											<input type="submit" class="button" value="&#139;&#139;&nbsp;<?php echo $installationStrings['LBL_CHANGE']; ?>" title="<?php echo $installationStrings['LBL_CHANGE']; ?>" />
										</form>
									</td>
									<?php if($next) : ?>
									<td align="right">
										<form action="install.php" method="post" name="form" id="form">
											<input type="hidden" name="mode" value="migration">
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