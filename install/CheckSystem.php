<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

ob_start();
eval ("phpinfo();");
$info = ob_get_contents();
ob_end_clean();

foreach (explode("\n", $info) as $line) {
	if (strpos($line, "Client API version") !== false)
		$mysql_version = trim(str_replace("Client API version", "", strip_tags($line)));
}

ob_start();
phpinfo(INFO_GENERAL);
$string = ob_get_contents();
ob_end_clean();

$pieces = explode("<h2", $string);
$settings = array ();
foreach ($pieces as $val) {
	preg_match("/<a name=\"module_([^<>]*)\">/", $val, $sub_key);
	preg_match_all("/<tr[^>]*>
										   <td[^>]*>(.*)<\/td>
										   <td[^>]*>(.*)<\/td>/Ux", $val, $sub);
	preg_match_all("/<tr[^>]*>
										   <td[^>]*>(.*)<\/td>
										   <td[^>]*>(.*)<\/td>
										   <td[^>]*>(.*)<\/td>/Ux", $val, $sub_ext);
	foreach ($sub[0] as $key => $val) {
		if (preg_match("/Configuration File \(php.ini\) Path /", $val)) {
			$val = preg_replace("/Configuration File \(php.ini\) Path /", '', $val);
			$phpini = strip_tags($val);
		}
	}
}

if (isset ($_REQUEST['filename'])) {
	$file_name = htmlspecialchars($_REQUEST['filename']);
}

$failed_permissions = Common_Install_Wizard_Utils::getFailedPermissionsFiles();
$gd_info_alternate = Common_Install_Wizard_Utils::$gdInfoAlternate;
$directive_recommended = Common_Install_Wizard_Utils::getRecommendedDirectives();
$directive_array = Common_Install_Wizard_Utils::getCurrentDirectiveValue();
$check_mysql_extension = Common_Install_Wizard_Utils::check_mysql_extension();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_INSTALLATION_CHECK']?></title>
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
				    				<td align=left class="paddingTop">
				    					<span class="bigHeading"><?php echo $installationStrings['LBL_PRE_INSTALLATION_CHECK']; ?></span>
				    					<br>
				    				</td>
									<td align=right valign="middle" class="paddingTop">
										<form action="install.php" method="post" name="form" id="form">
											<input type="hidden" name="filename" value="<?php echo $file_name?>" />
							                <input type="hidden" name="file" value="CheckSystem.php" />	
									        <input type="button" class="refreshButton" value='<?php echo $installationStrings['LBL_CHECK_AGAIN']; ?>' alt="<?php echo $installationStrings['LBL_CHECK_AGAIN']; ?>" title="<?php echo $installationStrings['LBL_REFRESH']; ?>" style="cursor:pointer;" onClick="submit();">
										</form>
									</td>  
								</tr>
								<tr><td colspan=2><hr noshade size=1></td></tr>
				    			<tr>
				    				<td colspan=2 align="left">
				    					<table cellpadding="0" cellspacing="1" align=right width="100%" class="level3">
				    						<tr>
								    			<td width=50%  valign=top >
													<table align=right width="100%" border="0">
														<tr>
															<td  valign=top align=left width=100%>
																<table cellpadding="2" cellspacing="1" align=right width="100%" border="0" class="level1">
																	<tr class='level1'>
																		<td valign=top ><?php echo $installationStrings['LBL_PHP_VERSION_GT_5']; ?></td>
																		<td  valign=top><?php $php_version = phpversion(); 
																							echo (version_compare($php_version, '5.2.0') == -1) ?
																								"<strong><font color=\"Red\">{$installationStrings['LBL_NO']}</strong></font>" : 
																								"<strong><font color=\"#46882B\">$php_version</strong></font>";
																						?>
																		</td>
																	</tr>
																	<tr class='level1'>
																		<td valign=top ><?php echo $installationStrings['LBL_IMAP_SUPPORT']; ?></td>
										        						<td valign=top><?php echo function_exists('imap_open') ? 
																							"<strong><font color=\"#46882B\">{$installationStrings['LBL_YES']}</strong></font>" : 
																							"<strong><font color=\"#FF0000\">{$installationStrings['LBL_NO']}</strong></font>";
																						?>
																		</td>
																	</tr>
																	<tr class='level1'>
																		<td valign=top ><?php echo $installationStrings['LBL_ZLIB_SUPPORT']; ?></td>
										        						<td valign=top><?php echo function_exists('gzinflate') ? 
																						"<strong><font color=\"#46882B\">{$installationStrings['LBL_YES']}</strong></font>" : 
																						"<strong><font color=\"#FF0000\">{$installationStrings['LBL_NO']}</strong></font>";
																					?>
																		</td>
																	</tr>
																	<tr class='level1'>
																		<td valign=top ><?php echo $installationStrings['LBL_GD_LIBRARY']; ?></td>
																		<td valign=top><?php				
																			if (!extension_loaded('gd')) {
																			echo "<strong><font size=-1 color=\"#FF0000\">{$installationStrings['LBL_NOT_CONFIGURED']}.</strong></font>";
																			} else {
																				if (!function_exists('gd_info')) {
																				eval ($gd_info_alternate);
																			}
																			$gd_info = gd_info();
																			
																			if (isset ($gd_info['GD Version'])) {
																			$gd_version = $gd_info['GD Version'];
																			$gd_version = preg_replace('%[^0-9.]%', '', $gd_version);
																			echo "<strong><font color=\"#46882B\">{$installationStrings['LBL_YES']}</strong></font>";
																			} else {
																				echo "<strong><font size=-1 color=\"#FF0000\">{$installationStrings['LBL_NO']}</font>";
																				}
																			}
																		?>
																		</td>
																	</tr>
																	<tr class="level1">
																		<td valign=top><?php echo $installationStrings['LBL_DATABASE_EXTENSION'];?></td>
																		<td valign=top><?php
																			if($check_mysql_extension == false) {
																				echo "<strong><font size=-1 color=\"#FF0000\">{$installationStrings['LBL_NO']}</strong></font>";
																			}
																			else {
																				echo "<strong><font color=\"#46882B\">{$installationStrings['LBL_YES']}</strong></font>";
																			}
																		?>
																		</td>
																	</tr>
																</table>  
															</td>
														</tr>
														<tr><td class="small" colspan=2><br></td></tr>
														<tr><td class="small" colspan=2><strong><?php echo $installationStrings['LBL_RECOMMENDED_PHP_SETTINGS']; ?>:</strong></td></tr>
														<?php
														$all_directive_recommended_value = true;
														if (!empty ($directive_array)) {
															$all_directive_recommended_value = false;
														?>
														<tr><td align=left width=100%>
							   	   							<!-- Recommended Settings -->
															<table cellpadding="2" cellspacing="1"  width="100%" border="0" class="level1">
										    					<tr>
										    						<td valign=top ><strong><?php echo $installationStrings['LBL_DIRECTIVE']; ?></strong></td>
										    						<td><strong><?php echo $installationStrings['LBL_RECOMMENDED']; ?></strong></td>
										    						<td nowrap><strong><?php echo $installationStrings['LBL_PHP_INI_VALUE']; ?></strong></td>
										    					</tr>
														    	<?php
																foreach ($directive_array as $index => $value) {
																?>
										   						<tr> 
										    						<td valign=top ><?php echo $index; ?></td>
										    						<td><?php echo $directive_recommended[$index]; ?></td>
										    						<td><strong><font color = red><?php echo $value; ?></font></strong></td>
										    					</tr>
										    					<?php
																}
																?>
															</table>
														</td></tr>
														<?php
														} else {
															echo "<tr><td class='small' colspan=2>{$installationStrings['LBL_PHP_DIRECTIVES_HAVE_RECOMMENDED_VALUES']}</td>";
														}
														?>
													</table>
								    			</td>
												<td align=left width=50% valign=top>
													<table cellpadding="2" cellspacing="1" align=right width="100%" border="0" class="level1">
														<?php
														if (!empty ($failed_permissions)) {
														?>
														<tr class='level1'><td colspan=2><strong><span style="color:Black;"><?php echo $installationStrings['LBL_READ_WRITE_ACCESS']; ?></span></strong></td></tr>
														<?php
															foreach ($failed_permissions as $index => $value) {
														?>
														<tr class='level1'>
															<td valign=top ><?php echo $index; ?> (<?php echo str_replace("./","",$value); ?>)</td>
							        						<td valign=top><font color="red"><strong><?php echo $installationStrings['LBL_NO']; ?></strong></font></td>
														</tr>
														<?php					
															}
														}
														?>
				       								</table>
													<br>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr valign=top>
									<td align=left >
										<input type="button" class="button" value="&#139;&#139;&nbsp;<?php echo $installationStrings['LBL_BACK']; ?>" title="<?php echo $installationStrings['LBL_BACK']; ?>" onClick="window.history.back();">
										
										</td>
									<td align=right>
										<form action="install.php" method="post" name="form" id="form">
											<input type="hidden" name="file" value="<?php echo $file_name?>" />
											<input type="submit" class="button" value="<?php echo $installationStrings['LBL_NEXT']; ?>&nbsp;&#155;&#155;" title="<?php echo $installationStrings['LBL_NEXT']; ?>" onClick="return isPermitted();">
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
    	  	
<script language="javascript">

function isPermitted(){
<?php

if (!empty ($failed_permissions)) {
	echo "alert('{$installationStrings['MSG_PROVIDE_READ_WRITE_ACCESS_TO_PROCEED']}');";
	echo "return false;";
} else {
	if (!$all_directive_recommended_value) { ?>
		if(confirm('<?php echo $installationStrings['WARNING_PHP_DIRECTIVES_NOT_RECOMMENDED_STILL_WANT_TO_PROCEED']; ?>')) {
			return true;
		} else {
			return false;
		}
	<?php
	}
	echo "return true;";
}
?>
}
</script>
    	
</body>
</html>	
