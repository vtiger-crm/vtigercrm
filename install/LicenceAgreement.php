<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_INSTALLATION_CHECK']?></title>
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
					<tr>
						<td width=80% valign=top class="cwContentDisplay" align=center colspan=2><br>
						</td>
					</tr>
					<tr>
						<td width=80% valign=top class="cwContentDisplay" align=center colspan=2>
						<!-- Right side tabs -->
						    <table cellspacing=0 cellpadding=10 width=90% align=center class='level3'>
								<tr>
									<td align=center>
										<iframe class='licence' frameborder=0 src='licenceAgreement.html' marginwidth=20 scrolling='auto'>
										</iframe>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width=80% valign=top class="cwContentDisplay" align=center colspan=2>
						<!-- Right side tabs -->
						    <table cellspacing=0 cellpadding=10 width=90% align=center class='cwContentDisplay'>
								<tr>
									<td align=left width=50% valign=top ><br>
										<input type="button" class="button" value="&#139;&#139;&nbsp;<?php echo $installationStrings['LBL_BACK']; ?>" title="<?php echo $installationStrings['LBL_BACK']; ?>" onClick="window.history.back();">
									</td>
									<td width=50% valign=top align=right>
													<br>
										<form action="install.php" method="post" name="form" id="form">
										<input type="hidden" name="filename" value="SetInstallationConfig.php" />
										<input type="hidden" name="file" value="CheckSystem.php" />	
								        <input type="button" class="button" value='<?php echo $installationStrings['LBL_AGREE']; ?>' title="<?php echo $installationStrings['LBL_AGREE']; ?>" onClick="window.document.form.submit();">
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