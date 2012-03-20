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
$migrationInfo = $_SESSION['migration_info'];
$root_directory = $migrationInfo['root_directory'];
$source_directory = $migrationInfo['source_directory'];
session_destroy();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_FINISH']?></title>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
	<link href="themes/softed/style.css" rel="stylesheet" type="text/css">
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
	<table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
	<tr>
		<td class="small" bgcolor="#4572BE" align=center>
			<!-- Master display -->
			<table border=0 cellspacing=0 cellpadding=0 width=97%>
			<tr>
				<td width=80% valign=top class="cwContentDisplay" align=left>
				<!-- Right side tabs -->
					<table border=0 cellspacing=0 cellpadding=10 width=100%>
					<tr>
						<td align=left class="small paddingTop">
							<span class="bigHeading"><?php echo $installationStrings['LBL_CONFIG_COMPLETED']; ?></span>
							<br>
					  		<hr noshade size=1>
					  	</td>
					</tr>
					<tr>
						<td align=center class="small" style="height:250px;">
<?php
$renameResult = Common_Install_Wizard_Utils::renameInstallationFiles();
$renamefile = $renameResult['renamefile'];
$ins_file_renamed = $renameResult['install_file_renamed'];
$ins_dir_renamed = $renameResult['install_directory_renamed'];

$_SESSION['VTIGER_DB_VERSION']= $vtiger_current_version;
?>
	<table border=0 cellspacing=0 cellpadding=5 align="center" width="80%" class="contentDisplay">
		<tr>
			<td align=center class=small>
				<b><?php echo $installationStrings['LBL_MIGRATION_FINISHED']; ?>. vtigercrm-<?php echo $vtiger_current_version. ' ' .$installationStrings['LBL_ALL_SET_TO_GO']; ?></b>
				<hr noshade size=1>			
				<div style="width:100%;padding:10px;" align=left>
					<ul>
						<?php if($ins_file_renamed==true){ ?>
						<li><?php echo $installationStrings['LBL_INSTALL_PHP_FILE_RENAMED']. ' ' .$renamefile;?>install.php.txt.</li>
						<?php } else { ?>						
						<li><font color='red'><?php echo $installationStrings['WARNING_RENAME_INSTALL_PHP_FILE']; ?>.</font></li>
						<?php } ?>
						
						<?php /*if($mig_file_renamed==true){ ?>
						<li><?php echo $installationStrings['LBL_MIGRATE_PHP_FILE_RENAMED']. ' ' .$renamefile;?>migrate.php.txt.</li>
						<?php } else { ?>						
						<li><font color='red'><?php echo $installationStrings['WARNING_RENAME_MIGRATE_PHP_FILE']; ?>.</font></li>
						<?php } */ ?>
						<?php if($ins_dir_renamed==true){ ?>
						<li><?php echo $installationStrings['LBL_INSTALL_DIRECTORY_RENAMED']. ' ' .$renamefile;?>install.</li> 
						<?php } else { ?>						
						<li><font color='red'><?php echo $installationStrings['WARNING_RENAME_INSTALL_DIRECTORY']; ?>.</font></li>
						<?php } ?>
					</ul>
					<br>
					<ul>
						<li><?php echo $installationStrings['LBL_OLD_VERSION_IS_AT'] . $source_directory;?>.
						<li><?php echo $installationStrings['LBL_CURRENT_SOURCE_PATH_IS'] . $root_directory;?>.
						<li><?php echo $installationStrings['LBL_LOGIN_USING_ADMIN']; ?>.</li>
						<li><?php echo $installationStrings['LBL_SET_OUTGOING_EMAIL_SERVER']; ?></li>						
						<li><?php echo $installationStrings['LBL_RENAME_HTACCESS_FILE']; ?>. <a href="javascript:void(0);" onclick="showhidediv();"><?php echo $installationStrings['LBL_MORE_INFORMATION']; ?></a>
			   				<div id='htaccess_div' style="display:none"><br><br>
				   				<?php echo $installationStrings['MSG_HTACCESS_DETAILS']; ?>
			  			 	</div>
			  			</li>
					</ul>
					<br>
					<ul><b>
						<li><font color='#0000FF'><?php echo $installationStrings['LBL_YOU_ARE_IMPORTANT']; ?></font></li>
						<li><?php echo $installationStrings['LBL_PRIDE_BEING_ASSOCIATED']; ?></li>
						<li><?php echo $installationStrings['LBL_TALK_TO_US_AT_FORUMS']; ?></li>
						<li><?php echo $installationStrings['LBL_DISCUSS_WITH_US_AT_BLOGS']; ?></li>
						<li><?php echo $installationStrings['LBL_WE_AIM_TO_BE_BEST']. '. ' .$installationStrings['LBL_SPACE_FOR_YOU']; ?></li>
					</b></ul>
				</div>
			</td>
		</tr>
	</table>
	<br>		
	<table border=0 cellspacing=0 cellpadding=10 width=100%>
		<tr>
			<td colspan=2 align="center">
				<form action="index.php" method="post" name="form" id="form">
					<input type="hidden" name="default_user_name" value="admin">
			 		<input type="submit" class="button" value="<?php echo $installationStrings['LBL_FINISH']; ?>" title="<?php echo $installationStrings['LBL_FINISH']; ?>" />
				</form>
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
