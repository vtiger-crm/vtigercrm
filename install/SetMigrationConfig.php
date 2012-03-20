<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
session_start();
$current_dir = pathinfo(dirname(__FILE__));
$current_dir = $current_dir['dirname']."/";

$cur_dir_path = false;
if (is_file("config.php") && is_file("config.inc.php")) {
	require_once("config.inc.php");	
	$cur_dir_path = true;
	if(!isset($dbconfig['db_hostname']) || $dbconfig['db_status']=='_DB_STAT_') {
		$cur_dir_path = false;
	}
} 

!isset($_SESSION['migration_info']['root_directory']) ? $root_directory = $current_dir : $root_directory = $_SESSION['migration_info']['root_directory'];
!isset($_SESSION['migration_info']['source_directory']) ? $source_directory = "" : $source_directory = $_SESSION['migration_info']['source_directory'];
!isset($_SESSION['migration_info']['user_name']) ? $user_name = "admin" : $user_name = $_SESSION['migration_info']['user_name'];
!isset($_SESSION['migration_info']['user_pwd']) ? $user_pwd = "" : $user_pwd = $_SESSION['migration_info']['user_pwd'];
!isset($_SESSION['migration_info']['new_dbname']) ? $new_dbname = "" : $new_dbname = $_SESSION['migration_info']['new_dbname'];

if(isset($_SESSION['migration_info']['old_version'])) {
	$old_version = $_SESSION['migration_info']['old_version'];
} elseif(isset($_SESSION['VTIGER_DB_VERSION'])) {
	$old_version = $_SESSION['VTIGER_DB_VERSION'];	
} else {
	$old_version = "";
}

include("modules/Migration/versions.php");
$version_sorted = $versions;
uasort($version_sorted,'version_compare');
$version_sorted = array_reverse($version_sorted,true);
$_SESSION['pre_migration'] = false;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_SYSTEM_CONFIGURATION']?></title>

    <link rel='stylesheet' type='text/css' href='themes/softed/style.css'></link>
    <script type="text/javascript" src="include/js/en_us.lang.js"></script>
    <script type="text/javascript" src="include/scriptaculous/prototype.js"></script>
    <script type="text/javascript" src="include/js/general.js"></script>

	<link href="include/install/install.css" rel="stylesheet" type="text/css">
</head>

<body class="small cwPageBg" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

<script type="text/javascript" language="Javascript">

function verify_data(form) {
	var isError = false;
	var errorMessage = "";
	// Here we decide whether to submit the form.
	if (trim(form.source_directory.value) =='') {
		isError = true;
		errorMessage += "\n <?php echo $installationStrings['LBL_PATH']; ?>";
		form.source_directory.focus();
	}
	if (trim(form.user_name.value) =='') {
		isError = true;
		errorMessage += "\n <?php echo $installationStrings['LBL_USERNAME']; ?>";
		form.user_name.focus();
	}
	if (trim(form.new_dbname.value) =='') {
		isError = true;
		errorMessage += "\n <?php echo $installationStrings['LBL_DATABASE_NAME']; ?>";
		form.new_dbname.focus();
	}
	if(form.old_version.value == ""){
		alert("<?php echo $installationStrings['LBL_SELECT_PREVIOUS_INSTALLATION_VERSION']; ?>");
		form.old_version.focus();
		return false;
	}		
	// Here we decide whether to submit the form.
	if (isError == true) {
		alert("<?php echo $installationStrings['LBL_MISSING_REQUIRED_FIELDS']; ?>:" + errorMessage);
		return false;
	}
	return true;
}
</script>

<br>
	<!-- Table for cfgwiz starts -->
<table border=0 cellspacing=0 cellpadding=0 width=85% align=center>
	<tr>
		<td class="cwHeadBg" align=left><img src="include/install/images/configwizard.gif" alt="<?php echo $installationStrings['LBL_CONFIG_WIZARD']; ?>" hspace="20" title="<?php echo $installationStrings['LBL_CONFIG_WIZARD']; ?>"></td>
		<td class="cwHeadBg1" align=right><img src="include/install/images/vtigercrm5.gif" alt="<?php echo $installationStrings['LBL_VTIGER_CRM_5']; ?>" title="<?php echo $installationStrings['LBL_VTIGER_CRM_5']; ?>"></td>
		<td class="cwHeadBg1" width=2%></td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=0 width=85% align=center>
	<tr>
		<td background="include/install/images/topInnerShadow.gif" align=left><img height="10" src="include/install/images/topInnerShadow.gif" ></td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=10 width=85% align=center>
	<tr valign="top">
		<td class="small" bgcolor="#4572BE" align=center>
			<!-- Master display -->
			<table border=0 cellspacing=0 cellpadding=10 width=97%>
				<tr>
					<td width=80% valign=top class="cwContentDisplay" align=left>
			    		<table border=0 cellspacing=0 cellpadding=5 width=100%>
			    			<tr>
			    				<td colspan="2" align=left class="paddingTop">
			    					<span class="bigHeading"><?php echo $installationStrings['LBL_SYSTEM_CONFIGURATION']; ?></span>
				  					<br>
				  					<hr noshade size=1>
				  				</td>
				  			</tr>
			    			<tr valign="top">
								<td align=left class="small" style="padding-left:5px" width="60%">
							    	<form action="install.php" method="post" name="installform" id="form">
										<!-- input type="hidden" name="file" value="PreMigrationActions.php" /-->
										<input type="hidden" name="file" value="ConfirmMigrationConfig.php" />
										<table width="100%" cellpadding="4" align=center border="0" cellspacing="0" class="level3"><tbody>
											<tr>
												<td colspan=2><strong><?php echo $installationStrings['LBL_PREVIOUS_INSTALLATION_INFORMATION']; ?></strong><hr size="1" noshade=""/></td>
											</tr>
											<tr>
												<td  nowrap width = 35%><?php echo $installationStrings['LBL_PREVIOUS_INSTALLATION_PATH']; ?> <sup><font color=red>*</font></sup></td>
												<td align="left">
													<?php
													if($cur_dir_path == true){
														echo $root_directory;
													?>					
													<input  class="small" type="hidden" name="source_directory" id="source_directory" value="<?php if (isset($root_directory)) echo "$root_directory"; ?>" size="50" /> 
													<?php } else { ?>					
													<input  class="small" type="text" name="source_directory" id="source_directory" value="<?php if (isset($source_directory)) echo "$source_directory"; ?>" size="50" /> 
													<?php } ?>	
													<input class="dataInput" type="hidden" name="root_directory" id="root_directory" value="<?php if (isset($root_directory)) echo "$root_directory"; ?>" size="40" />			
												</td>
											</tr>
											<tr>
												<td width = 35%><?php echo $installationStrings['LBL_PREVIOUS_INSTALLATION_VERSION']; ?> <sup><font color=red>*</font></sup></td>
												<td align="left">
													<select class="small" name='old_version' id='old_version'>
														<option value='' <?php if($old_version == "") echo "selected"; ?> >--SELECT--</option>
														<?php	
														foreach($version_sorted as $index=>$value){
															if($index == $old_version)
																echo "<option value='$index' selected>$value</option>";
															else
																echo "<option value='$index'>$value</option>"; 
														}
														?>
													</select>
													</select>
												</td>
											</tr>
											<tr>
												<td width = 35% >Admin <?php echo $installationStrings['LBL_USERNAME']; ?> <sup><font color=red>*</font></sup></td>
												<td align="left"><input class="small" type="text" name="user_name" id="user_name" value="<?php if (isset($user_name)) echo $user_name; else echo 'admin';?>" size="50" /> </td>
											</tr>
											<tr>
												<td width = 35%>Admin <?php echo $installationStrings['LBL_PASSWORD']; ?> <sup><font color=red></font></sup></td>
												<td align="left"><input class="small" type="password" name="user_pwd" id="user_pwd" value="<?php if (isset($user_pwd)) echo $user_pwd; else echo '';?>" size="50" /> </td>
											</tr>
											<tr>
												<td width = 35%><?php echo $installationStrings['LBL_MIGRATION_DATABASE_NAME']; ?> <sup><font color=red>*</font></sup></td>
												<td align="left"><input class="small" type="text" name="new_dbname" id="new_dbname" value="<?php if (isset($new_dbname)) echo $new_dbname; else echo '';?>" size="50" /> </td>
											</tr>
										</table>
									</form>
								</td>
								<td class="small" style="padding-left:5px" with="40%" height="100%">
									<table width="100%" cellpadding="0" align=center border="0" cellspacing="0">
										<tr>
											<td>
												<div class="helpmessagebox paddingPoint5em">
													<span class="redColor fontBold"><?php echo $installationStrings['LBL_IMPORTANT_NOTE']; ?>:</span>
													<hr />												
													<ul>
														<li><?php echo $installationStrings['MSG_TAKE_DB_BACKUP']; ?>.</li>
														<li><b><?php echo $installationStrings['QUESTION_MIGRATE_USING_NEW_DB']; ?></b>?<br>
															<ol style='padding: 0; padding-left: 15px;'>
															<li><?php echo $installationStrings['MSG_CREATE_DB_WITH_UTF8_SUPPORT']; ?>.<br>
															<font class='fontBold'><?php echo $installationStrings['LBL_EG']; ?>:</font> CREATE DATABASE <newDatabaseName> DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;</li>
															<li><?php echo $installationStrings['MSG_COPY_DATA_FROM_OLD_DB']; ?>.</li>
															</ol>
														</li>
													</ul>
  												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							
							<tr>
								<td align="left">
									<input type="button" class="button" value="&#139;&#139;&nbsp;<?php echo $installationStrings['LBL_BACK']; ?>" title="<?php echo $installationStrings['LBL_BACK']; ?>" onClick="window.history.back();">
								</td>
								<td align="right">
									<input type="button" class="button" value="<?php echo $installationStrings['LBL_NEXT']; ?>&nbsp;&#155;&#155;" title="<?php echo $installationStrings['LBL_NEXT']; ?>" onClick="if(verify_data(installform) == true) document.installform.submit();">
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
<table border=0 cellspacing=0 cellpadding=0 width=85% align=center>
	<tr>
		<td background="include/install/images/bottomGradient.gif"><img src="include/install/images/bottomGradient.gif"></td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=0 width=85% align=center>
	<tr>
		<td align=center><img src="include/install/images/bottomShadow.jpg"></td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=0 width=85% align=center>
	<tr>
    	<td class=small align=center> <a href="http://www.vtiger.com" target="_blank">www.vtiger.com</a></td>
	</tr>
</table>

<!-- To prefetch the images for blocking the screen -->
<img style="display: none;" src="include/install/images/loading.gif">
<img style="display: none;" src="themes/softed/images/layerPopupBg.gif">
<!-- END -->
</body>
</html>