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

$configFileUtils = new ConfigFile_Utils($_SESSION['config_file_info']);

if (!$configFileUtils->createConfigFile()) {
	die("<strong class='big'><font color='red'>{$installationStrings['ERR_CANNOT_WRITE_CONFIG_FILE']}</font></strong>");
}

require_once('include/utils/utils.php');  // Required - Especially to create adb instance in global scope.

$mode = $_REQUEST['mode'];
if($mode == 'migration') {
	$prev_file_name = 'SetMigrationConfig.php';
	$file_name = 'MigrationProcess.php';
	$optionalModules = Migration_Utils::getInstallableOptionalModules();
} else {
	$prev_file_name = 'SetInstallationConfig.php';
	$file_name = 'CreateTables.php';
	$optionalModules = Installation_Utils::getInstallableOptionalModules();
}
$selectedOptionalModuleNames = array();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_OPTIONAL_MODULES']?></title>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
	<link href="themes/softed/style.css" rel="stylesheet" type="text/css">
	<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>
	<script type="text/javascript" src="include/js/general.js"></script>
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
					<tr>
						<td width=80% valign=top class="cwContentDisplay" align=center>
						<!-- Right side tabs -->
						    <table cellspacing=0 cellpadding=2 width=95% align=center>
							    <tr>
							    	<td align=left colspan=2 class="paddingTop">
							    		<span class="bigHeading"><?php echo $installationStrings['LBL_OPTIONAL_MODULES']?></span>
							    		<br>
							    	</td>
								</tr>
								<tr><td colspan=2><hr noshade size=1></td></tr>
								<tr>
									<td colspan=2>
										<strong class="big"><?php echo $installationStrings['MSG_CONFIG_FILE_CREATED']; ?>.</strong>
									</td>
								</tr>
						    	<tr>
							    	<td colspan=2 align="left">
							    		<table cellpadding="0" cellspacing="1" align=center width="100%" class="level3">
						    				<tr >
												<td align=left width=50% valign=top>
													<table cellpadding="5" cellspacing="1" align=right width="100%" border="0">
													<?php if(count($optionalModules) > 0) {
														foreach($optionalModules as $option=>$modules) {
															if(count($modules) > 0) { ?>															
												    			<tr>
													    			<td colspan=3 style="font-size:13;">
													    				<strong><?php echo $installationStrings['LBL_SELECT_OPTIONAL_MODULES_TO_'.$option]; ?> :</strong>
													    				<hr size="1" noshade=""/>
													    			</td>
												    			</tr>
																<?php foreach($modules as $moduleName=>$moduleDetails) { 
																	$moduleDescription = $moduleDetails['description'];
																	$moduleSelected = $moduleDetails['selected'];
																	$moduleEnabled = $moduleDetails['enabled'];
																	if ($moduleSelected == true) $selectedOptionalModuleNames[] = $moduleName;
																?>
																<tr class='level1'>
									        						<td class='small' width= "5%" valign=top align="right">
									        							<input type="checkbox" id="<?php echo $moduleName; ?>" name="<?php echo $moduleName; ?>" value="<?php echo $moduleName; ?>" 
									        									<?php if ($moduleSelected == true) echo "checked"; ?> 
									        									<?php if ($moduleEnabled == false || $option == 'update') echo "disabled"; ?>
									        									onChange='ModuleSelected("<?php echo $moduleName; ?>");' />
									        						</td>
																	<td class='small' valign=top ><?php echo $moduleName; ?></td>
																	<td class='small' valign=top ><i><?php echo $moduleDescription; ?></i></td>
																</tr>
																<?php
																}
															}
														}
														?>
														<tr  class="level2">
															<td colspan="3" align="right">
																<?php echo $installationStrings['LBL_OPTIONAL_MORE_LANGUAGE_PACK'] ?>
																<a href="http://www.vtiger.com/market-place" target="_NEW">Marketplace</a>
															</td>
														</tr>
													<?php
													} else {
													?>
														<tr><td>
															<div class="fixedSmallHeight textCenter fontBold">
																<div style="padding-top:50px;width:100%;">
																	<span class="genHeaderBig"><?php echo $installationStrings['LBL_NO_OPTIONAL_MODULES_FOUND']; ?> !</span>
																</div>
															</div>
														</td></tr>
													<?php } ?>
						       						</table>
													<br>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr valign=top width="50%">				
									<td align=left style="vertical-align: middle;">
										<form action="install.php" method="post" name="backform" id="backform">
											<input type="hidden" name="file" value="<?php echo $prev_file_name; ?>">
											<input type="submit" class="button" value="&#139;&#139;&nbsp;<?php echo $installationStrings['LBL_BACK']; ?>" title="<?php echo $installationStrings['LBL_BACK']; ?>" />
										</form>
									</td>
									<td align=right style="vertical-align: middle;">
									<form action="install.php" method="post" name="form" id="form">
										<input type="hidden" value="<?php echo implode(":",$selectedOptionalModuleNames)?>" id='selected_modules' name='selected_modules' />  
						                <input type="hidden" name="file" value="<?php echo $file_name; ?>" />
						                <input type="hidden" name="auth_key" value="<?php echo $_SESSION['authentication_key']; ?>" />
										<input type="button" class="button" value="<?php echo $installationStrings['LBL_NEXT']; ?>&nbsp;&#155;&#155;" title="<?php echo $installationStrings['LBL_NEXT']; ?>" onClick="VtigerJS_DialogBox.progress();submit();">
									</form>
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
	
	<!-- Prefetch image to display later for Screen blocker -->	
	<img style="display: none;" src="include/install/images/loading.gif">
	<img src="themes/softed/images/layerPopupBg.gif" style="display: none;"/>
    	
<script language="javascript">
var selected_modules = '<?php echo implode(":",$selectedOptionalModuleNames)?>';

function ModuleSelected(module){
	if(document.getElementById(module).checked == true){
		if(selected_modules==''){
			selected_modules = selected_modules+document.getElementById(module).value;
		} else {
			selected_modules = selected_modules+":"+document.getElementById(module).value;
		}
	} else {
		if(selected_modules.indexOf(":"+module+":")>-1){
			selected_modules = selected_modules.replace(":"+module+":",":")
		} else if(selected_modules.indexOf(module+":")>-1){
			selected_modules = selected_modules.replace(module+":","")
		} else if(selected_modules.indexOf(":"+module)>-1){
			selected_modules = selected_modules.replace(":"+module,"")
		} else {
			selected_modules = selected_modules.replace(module,"")
		}
	}
	document.getElementById('selected_modules').value = selected_modules;
}
</script>
</body>
</html>	