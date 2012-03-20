<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

@include_once('config.db.php');
global $dbconfig, $vtiger_current_version;
$hostname = $_SERVER['SERVER_NAME'];
$web_root = ($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"]:$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
$web_root .= $_SERVER["REQUEST_URI"];
$web_root = str_replace("/install.php", "", $web_root);
$web_root = "http://".$web_root;

$current_dir = pathinfo(dirname(__FILE__));
$current_dir = $current_dir['dirname']."/";
$cache_dir = "cache/";

$newdbname = 'vtigercrm'.str_replace(array('.',' '),array(''),strtolower($vtiger_current_version));

require_once('modules/Utilities/Currencies.php');

session_start();

!isset($_REQUEST['host_name']) ? $host_name= $hostname : $host_name = $_REQUEST['host_name'];

!isset($_SESSION['config_file_info']['db_hostname']) ? $db_hostname = $hostname: $db_hostname = $_SESSION['config_file_info']['db_hostname'];
!isset($_SESSION['config_file_info']['db_type']) ? $db_type = "" : $db_type = $_SESSION['config_file_info']['db_type'];
!isset($_SESSION['config_file_info']['db_username']) ? $db_username = "" : $db_username = $_SESSION['config_file_info']['db_username'];
!isset($_SESSION['config_file_info']['db_password']) ? $db_password = "" : $db_password = $_SESSION['config_file_info']['db_password'];
!isset($_SESSION['config_file_info']['db_name']) ? $db_name = $newdbname : $db_name = $_SESSION['config_file_info']['db_name'];
!isset($_SESSION['config_file_info']['site_URL']) ? $site_URL = $web_root : $site_URL = $_SESSION['config_file_info']['site_URL'];
!isset($_SESSION['config_file_info']['root_directory']) ? $root_directory = $current_dir : $root_directory = $_SESSION['config_file_info']['root_directory'];
!isset($_SESSION['config_file_info']['admin_email']) ? $admin_email = "" : $admin_email = $_SESSION['config_file_info']['admin_email'];
!isset($_SESSION['config_file_info']['currency_name']) ? $currency_name = 'USA, Dollars' : $currency_name = $_SESSION['config_file_info']['currency_name'];

!isset($_SESSION['installation_info']['check_createdb']) ? $check_createdb = "" : $check_createdb = $_SESSION['installation_info']['check_createdb'];
!isset($_SESSION['installation_info']['root_user']) ? $root_user = "" : $root_user = $_SESSION['installation_info']['root_user'];
!isset($_SESSION['installation_info']['root_password']) ? $root_password = "" : $root_password = $_SESSION['installation_info']['root_password'];
!isset($_SESSION['installation_info']['create_utf8_db']) ? $create_utf8_db = "true" : $create_utf8_db = $_SESSION['installation_info']['create_utf8_db'];
!isset($_SESSION['installation_info']['db_populate']) ? $db_populate = "true" : $db_populate = $_SESSION['installation_info']['db_populate'];
!isset($_SESSION['installation_info']['admin_email']) ? $admin_email = "" : $admin_email = $_SESSION['installation_info']['admin_email'];
!isset($_SESSION['installation_info']['admin_password']) ? $admin_password = "admin" : $admin_password = $_SESSION['installation_info']['admin_password'];

$db_options = Installation_Utils::getDbOptions();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_SYSTEM_CONFIGURATION']?></title>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
	<link href="themes/softed/style.css" rel="stylesheet" type="text/css">
</head>

<body class="small cwPageBg" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
<style>
	.hide_tab{display:none;}
	.show_div{}
</style>

<script type="text/javascript" language="Javascript">

function fnShow_Hide(){
	var sourceTag = document.getElementById('check_createdb').checked;
	if(sourceTag){
		document.getElementById('root_user').className = 'show_div';
		document.getElementById('root_pass').className = 'show_div';
		document.getElementById('create_db_config').className = 'show_div';
		document.getElementById('root_user_txtbox').focus();
	}
	else{
		document.getElementById('root_user').className = 'hide_tab';
		document.getElementById('root_pass').className = 'hide_tab';
		document.getElementById('create_db_config').className = 'hide_tab';
	}
}

function trim(s) {
    while (s.substring(0,1) == " ") {
        s = s.substring(1, s.length);
    }
    while (s.substring(s.length-1, s.length) == ' ') {
        s = s.substring(0,s.length-1);
    }
    return s;
}

function verify_data(form) {
	var isError = false;
	var errorMessage = "";
	if (trim(form.db_hostname.value) =='') {
		isError = true;
		errorMessage += "\n <?php echo $installationStrings['LBL_DATABASE'].' '.$installationStrings['LBL_HOST_NAME']; ?>";
		form.db_hostname.focus();
	}
	if (trim(form.db_username.value) =='') {
		isError = true;
		errorMessage += "\n <?php echo $installationStrings['LBL_DATABASE'].' '.$installationStrings['LBL_USER_NAME']; ?>";
		form.db_username.focus();
	}
	if (trim(form.db_name.value) =='') {
		isError = true;
		errorMessage += "\n <?php echo $installationStrings['LBL_DATABASE_NAME']; ?>";
		form.db_name.focus();
	}
	if (trim(form.site_URL.value) =='') {
		isError = true;
		errorMessage += "\n <?php echo $installationStrings['LBL_SITE_URL']; ?>";
		form.site_URL.focus();
	}
	if (trim(form.root_directory.value) =='') {
		isError = true;
		errorMessage += "\n <?php echo $installationStrings['LBL_PATH']; ?>";
		form.root_directory.focus();
	}
	if (trim(form.admin_password.value) =='') {
		isError = true;
		errorMessage += "\n admin <?php echo $installationStrings['LBL_PASSWORD']; ?>";
		form.admin_password.focus();
	}
	if (trim(form.admin_email.value) =='') {
		isError = true;
		errorMessage += "\n admin <?php echo $installationStrings['LBL_EMAIL']; ?>";
		form.admin_email.focus();
	}
	if (trim(form.currency_name.value) =='') {
        isError = true;
        errorMessage += "\n <?php echo $installationStrings['LBL_CURRENCY_NAME']; ?>";
        form.currency_name.focus();
    }

	if(document.getElementById('check_createdb').checked == true) {
		if (trim(form.root_user.value) =='') {
			isError = true;
			errorMessage += "\n <?php echo $installationStrings['LBL_ROOT']. ' ' .$installationStrings['LBL_USER_NAME']; ?>";
			form.root_user.focus();
		}
	}

	if (isError == true) {
		alert("<?php echo $installationStrings['LBL_MISSING_REQUIRED_FIELDS']; ?>:" + errorMessage);
		return false;
	}
	if (trim(form.admin_email.value) != "" && !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(form.admin_email.value)) {
		alert("<?php echo $installationStrings['ERR_ADMIN_EMAIL_INVALID']; ?> - \'"+form.admin_email.value+"\'");
		form.admin_email.focus();
		return false;
	}

	var SiteUrl = form.site_URL.value;
    if(SiteUrl.indexOf("localhost") > -1 && SiteUrl.indexOf("localhost") < 10) {
        if(confirm("<?php echo $installationStrings['WARNING_LOCALHOST_IN_SITE_URL']; ?>")) {
			form.submit();
        } else {
            form.site_URL.select();
            return false;
        }
    } else {
		form.submit();
    }	
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
		<tr>
			<td class="small" bgcolor="#4572BE" align=center>
				<!-- Master display -->
				<table border=0 cellspacing=0 cellpadding=0 width=97%>
					<tr>
						<td width=80% valign=top class="cwContentDisplay" align=center>
							<!-- Right side tabs -->
						    <table border=0 cellspacing=0 cellpadding=2 width=95%>
						    	<tr>
						    		<td align=left colspan=2 class="small paddingTop">
						    			<span class="bigHeading"><?php echo $installationStrings['LBL_SYSTEM_CONFIGURATION']?></span>
						    			<br>
							  			<hr noshade size=1>
							  		</td>
							  	</tr>
							    <tr valign=top >
							    	<form action="install.php" method="post" name="installform" id="form">
							    	<input type="hidden" name="file" value="ConfirmConfig.php" />				    
									<td align=left class="small" width=50% style="padding-left:10px">				
										<table width="100%" cellpadding="0"  cellspacing="1" border="0" align=center class="level3">
											<tr>
												<td colspan=4><strong><?php echo $installationStrings['LBL_DATABASE_INFORMATION']; ?></strong><hr noshade size=1></td>
											</tr>
											<tr>
								               <td width="20%" nowrap ><?php echo $installationStrings['LBL_DATABASE_TYPE']; ?> <sup><font color=red>*</font></sup></td>
								               <td width="30%" align="left">
												<?php if(!$db_options) : ?>
													<?php echo $installationStrings['LBL_NO_DATABASE_SUPPORT']; ?>
												<?php elseif(count($db_options) == 1) : ?>
													<?php list($db_type, $label) = each($db_options); ?>
													<input type="hidden" name="db_type" value="<?php echo $db_type ?>"><?php echo $label ?>
												<?php else : ?>
													<select class="small" length=40 name="db_type">
													<?php foreach($db_options as $db_option_type => $label) : ?>
														<option value="<?php echo $db_option_type ?>" <?php if(isset($db_type) && $db_type == $db_option_type) { echo "SELECTED"; } ?>><?php echo $label ?></option>
													<?php endforeach; ?>
													</select>
												<?php endif; ?>
												</td>
								            </tr>
											<tr>
												<td width="25%" nowrap ><?php echo $installationStrings['LBL_HOST_NAME']; ?> <sup><font color=red>*</font></sup></td>
												<td width="75%" align="left"><input type="text" class="small" name="db_hostname" value="<?php if (isset($db_hostname)) echo "$db_hostname"; ?>" />
										   			&nbsp;<a href="http://www.vtiger.com/products/crm/help/<?php echo $vtiger_current_version; ?>/vtiger_CRM_Database_Hostname.pdf" target="_blank">More...</a></td>
											</tr>
											<tr>
												<td nowrap><?php echo $installationStrings['LBL_USER_NAME']; ?> <sup><font color=red>*</font></sup></td>
												<td align="left"><input type="text" class="small" name="db_username" value="<?php if (isset($db_username)) echo "$db_username"; ?>" /></td>
											</tr>
											<tr>
												<td nowrap><?php echo $installationStrings['LBL_PASSWORD']; ?></td>
												<td align="left"><input type="password" class="small" name="db_password" value="<?php if (isset($db_password)) echo "$db_password"; ?>" /></td>
											</tr>
											<tr>
												<td nowrap><?php echo $installationStrings['LBL_DATABASE_NAME']; ?> <sup><font color=red>*</font></sup></td>
												<td align="left" width='30%'><input type="text" class="small" name="db_name" value="<?php if (isset($db_name)) echo "$db_name"; ?>" />&nbsp;
											</tr>
											<tr>
												<td colspan=2> 
										      		<?php if($check_createdb == 'on')
											       	{?>
											       	<input class="small" name="check_createdb" type="checkbox" id="check_createdb" checked onClick="fnShow_Hide()"/> 
											       	<?php }else{?>
											       		<input class="small" name="check_createdb" type="checkbox" id="check_createdb" onClick="fnShow_Hide()"/> 
											       	<?php } ?>
											       	&nbsp;<?php echo $installationStrings['LBL_CREATE_DATABASE'] . " (". $installationStrings['LBL_DROP_IF_EXISTS'] .")"; ?></td>
		              							</td>
		              						</tr>
									      	<tr id="root_user" class="hide_tab">
											   	<td nowrap="nowrap" width="20%"><?php echo $installationStrings['LBL_ROOT']. ' ' .$installationStrings['LBL_USER_NAME']; ?> <sup><font color="red">*</font></sup></td>
											   	<td align="left" width="30%"><input class="small" name="root_user" id="root_user_txtbox" value="<?php echo $root_user;?>" type="text"></td>
								 	      	</tr>
									      	<tr id="root_pass" class="hide_tab">
											   	<td nowrap="nowrap" width="20%"><?php echo $installationStrings['LBL_ROOT']. ' ' .$installationStrings['LBL_PASSWORD']; ?></td>
											   	<td align="left" width="30%"><input class="small" name="root_password" value="<?php echo $root_password;?>" type="password"></td>
										 	</tr>
								          	<tr id="create_db_config" class="hide_tab">
											   	<td nowrap="nowrap"><?php echo $installationStrings['LBL_UTF8_SUPPORT']; ?></td>
											   	<td align="left" colspan=3><input class="small" type="checkbox" id="create_utf8_db" name="create_utf8_db" <?php if($create_utf8_db == 'true') echo "checked"; ?> /> <!-- DEFAULT CHARACTER SET utf8, DEFAULT COLLATE utf8_general_ci --></td>
									      	</tr>							      	
											<tr>
												<td colspan=2  style="border-top:1px dotted black;">
													<input type="checkbox" class="dataInput" name="db_populate"  <?php if($db_populate == 'true') echo "checked"; ?> />
													&nbsp;<?php echo $installationStrings['LBL_POPULATE_DEMO_DATA']; ?>
												</td>
											</tr>
		              					</table>
										<br>
									</td>			
									<td align=left class="small" width=50% style="padding-left:2em;">
				  						<!-- Web site configuration -->
										<table width="100%" cellpadding="0" border="0" cellspacing="1" align=center class="level3"><tbody>
			            					<tr>
												<td colspan=2><strong><?php echo $installationStrings['LBL_CRM_CONFIGURATION']; ?></strong><hr noshade size=1></td>
			            					</tr>
											<tr>
												<td width="20%" ><?php echo $installationStrings['LBL_URL']; ?> <sup><font color=red>*</font></sup></td>
												<td width="80%" align="left"><input class="small" type="text" name="site_URL"
												value="<?php if (isset($site_URL)) echo $site_URL; ?>" size="40" />
												</td>
											</tr>
											<tr>
												<td nowrap width=20% ><?php echo $installationStrings['LBL_CURRENCY_NAME']; ?> <sup><font color=red>*</font></sup></td>
												<td width=80% align="left">
													<select class="small" id='currency_name' name='currency_name''>
														<?php
															foreach($currencies as $index=>$value){
																if($index==$currency_name){
																	echo "<option value='$index' selected>$index(".$value[1].")</option>";
																}
																else{
																	echo "<option value='$index'>$index(".$value[1].")</option>";
																}
															}
														?>
													</select>
												</td>
											</tr>
											<input type="hidden" name="root_directory" value="<?php if (isset($root_directory)) echo "$root_directory"; ?>" size="40" />
											<input type="hidden" name="cache_dir" size='40' value="<?php if (isset($cache_dir)) echo $cache_dir; ?>" size="40" />
										</table>
										<br>
						
										<!-- Admin Configuration -->
										<table width="100%" cellpadding="0" border="0" align=center class="level3" cellspacing="1" >
											<tr>
												<td colspan=2><strong><?php echo $installationStrings['LBL_USER_CONFIGURATION']; ?></strong><hr noshade size=1></td>
											</tr>
											<tr>
												<td nowrap width=35% ><?php echo $installationStrings['LBL_USERNAME']; ?></td>
												<td width=55% align="left">admin</td>
											</tr>
											<tr>
												<td nowrap><?php echo $installationStrings['LBL_PASSWORD']; ?> <sup><font color=red>*</font></sup></td>
												<td align="left"><input class="small" size=25 type="password" name="admin_password" value="<?php if (isset($admin_password)) echo "$admin_password"; else echo "admin"; ?>"></td>
											</tr>
											<tr>
												<td nowrap><?php echo $installationStrings['LBL_EMAIL']; ?> <sup><font color=red>*</font></sup></td>
												<td align="left"><input class="small" size=25 type="text" name="admin_email" value="<?php if (isset($admin_email)) echo "$admin_email"; ?>"></td>
											</tr>
										</table>		
										<!-- System Configuration -->										
									</td>
									</form>
								</tr>
								
								<tr>
									<td align="left">
										<input type="button" class="button" value="&#139;&#139;&nbsp;<?php echo $installationStrings['LBL_BACK']; ?>" title="<?php echo $installationStrings['LBL_BACK']; ?>" onClick="window.history.back();" />
									</td>
									<td align="right">
										<input type="button" class="button" value="<?php echo $installationStrings['LBL_NEXT']; ?>&nbsp;&#155;&#155;" title="<?php echo $installationStrings['LBL_NEXT']; ?>" onClick="return verify_data(window.document.installform);" />
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
<script>
	fnShow_Hide();
</script>
</body>
</html>
