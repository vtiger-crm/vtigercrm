<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

//get php configuration settings.  requires elaborate parsing of phpinfo() output
ob_start();
phpinfo(INFO_GENERAL);
$string = ob_get_contents();
ob_end_clean();

$pieces = explode("<h2", $string);
$settings = array();

require_once('config.inc.php');
$install_permitted = false;
if(!isset($dbconfig['db_hostname']) || $dbconfig['db_status']=='_DB_STAT_') {
	$install_permitted = true;
}

foreach($pieces as $val)
{
   preg_match("/<a name=\"module_([^<>]*)\">/", $val, $sub_key);
   preg_match_all("/<tr[^>]*>
									   <td[^>]*>(.*)<\/td>
									   <td[^>]*>(.*)<\/td>/Ux", $val, $sub);
   preg_match_all("/<tr[^>]*>
									   <td[^>]*>(.*)<\/td>
									   <td[^>]*>(.*)<\/td>
									   <td[^>]*>(.*)<\/td>/Ux", $val, $sub_ext);
   foreach($sub[0] as $key => $val) {
		if (preg_match("/Configuration File \(php.ini\) Path /", $val)) {
	   		$val = preg_replace("/Configuration File \(php.ini\) Path /", '', $val);
			$phpini = strip_tags($val);
	   	}
   }

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_WELCOME']?></title>
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
	<table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
		<tr>
			<td class="small" bgcolor="#4572BE" align=center>
				<!-- Master display -->
				<table border=0 cellspacing=0 cellpadding=0 width=97%>
					<tr>
						<td width=100% valign=top class="cwContentDisplay" align=left>
						<!-- Right side tabs -->
							<table border=0 cellspacing=0 cellpadding=10 width=70% align=center>
								<tr>
									<td class=small align=left colspan=2><img src="include/install/images/welcome.gif" alt="<?php echo $installationStrings['LBL_WELCOME_CONFIG_WIZARD']; ?>" title="<?php echo $installationStrings['LBL_WELCOME_CONFIG_WIZARD']; ?>">
										<br><hr noshade size=1>
									</td>
								</tr>
		
								<tr class='level3'>
									<td valign=top align=left class="small contentDisplay fixedSmallHeight" style="padding-left:20px;" colspan=2>
										<p style='text-align:center;font-weight:bold;'><?php echo $installationStrings['LBL_ABOUT_CONFIG_WIZARD'] . $vtiger_current_version; ?>.</p>
										<p><br><?php echo $installationStrings['LBL_ABOUT_VTIGER']; ?></p>
							  		</td>
								</tr>
								<tr>
									<td colspan=2></span></td>
								</tr>
								<tr>
									<?php
									if($install_permitted == true){
									?>
									<td align=right>
										<form action="install.php" method="post" name="installform" id="form">
								        <input type="hidden" name="file" value="LicenceAgreement.php" />	
								        <input type="hidden" name="install" value="true" />	
										<input type="button" class="button" value='<?php echo $installationStrings['LBL_INSTALL']; ?>' alt="<?php echo $installationStrings['LBL_INSTALL']; ?>" title="<?php echo $installationStrings['LBL_INSTALL']; ?>" onClick="window.document.installform.submit();">
										</form>
									</td>
									<td align=left>
									<?php
									}
									else{
									?>
									<td align=center colspan=2>
									<?php
									}
									?>
										<form action="install.php" method="post" name="Migrateform" id="form">
										<input type="hidden" name="filename" value="SetMigrationConfig.php" />
										<input type="hidden" name="file" value="CheckSystem.php" />	
								        <input type="hidden" name="migrate" value="true" />	
										<input type="button" class="button" value='<?php echo $installationStrings['LBL_MIGRATE']; ?>' alt="<?php echo $installationStrings['LBL_MIGRATE']; ?>" title="<?php echo $installationStrings['LBL_MIGRATE']; ?>" onClick="window.document.Migrateform.submit();">
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
			<td class='cwfooterBg' background="include/install/images/bottomGradient.gif"><br></td>
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