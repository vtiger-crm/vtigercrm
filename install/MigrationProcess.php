<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

global $php_max_execution_time;
set_time_limit($php_max_execution_time);

session_start();

$auth_key = $_REQUEST['auth_key'];
if($_SESSION['authentication_key'] != $auth_key) {
	die($installationStrings['ERR_NOT_AUTHORIZED_TO_PERFORM_THE_OPERATION']);
}

if(isset($_REQUEST['selected_modules'])) {
	$_SESSION['migration_info']['selected_optional_modules'] = $_REQUEST['selected_modules'];
}

Migration_Utils::copyRequiredFiles($_SESSION['migration_info']['source_directory'], $_SESSION['migration_info']['root_directory']);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_MIGRATION']?></title>
	<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>		
	<script type="text/javascript" src="include/js/general.js"></script>
	<link href="themes/softed/style.css" rel="stylesheet" type="text/css">
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
</head>

<?php
if($_REQUEST['migration_start'] != 'true') {
?>	
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
							<td width=80% valign=top class="cwContentDisplay" align=center colspan=2>
							<br>
							</td>
						</tr>
						<tr>
							<td width=80% valign=top class="cwContentDisplay" align=center colspan=2>
							<!-- Right side tabs -->
							    <table cellspacing=0 cellpadding=10 width=95% align=center class='level3'>
									<tr>
										<td align=center>
											<iframe class='licence' id='triggermigration_iframe' frameborder=0 src='' marginwidth=20 scrolling='auto'>
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
							</td>
							<td width=50% valign=top align=right>
											<br>
								<div id='Mig_Close' style='display:none;'>
									<form action="install.php" method="post" name="form" id="form">
										<input type="hidden" name="file" value="MigrationComplete.php" />	
								        <input type="submit" class="button" value="<?php echo $installationStrings['LBL_NEXT']; ?>&nbsp;&#155;&#155;" title="<?php echo $installationStrings['LBL_NEXT']; ?>" />
							    	</form>
							    </div>
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
	    
	    <script type='text/javascript'>
	    var auth_key = '<?php echo $auth_key; ?>';
	    if(typeof('Event') != 'undefined') {
	    	Event.observe(window, 'load', function() {
	    		VtigerJS_DialogBox.progress();
	    		document.getElementById('triggermigration_iframe').src = 'install.php?file=MigrationProcess.php&migration_start=true&auth_key='+auth_key;
	    	});
	    }
	    function Migration_Complete() {
	    	$('Mig_Close').style.display = 'block';
	    }
	    </script>
	    
	    <!-- Prefetch image to display later for Screen blocker -->
		<img style="display: none;" src="include/install/images/loading.gif">
	    <img src="themes/softed/images/layerPopupBg.gif" style="display: none;"/>
<?php 
} else {
	// Start the migration now	
	echo '<body onload="window.parent.VtigerJS_DialogBox.hideprogress();">';
	
	require_once('include/utils/utils.php');
	require_once('include/logging.php');
	$migrationlog = & LoggerManager::getLogger('MIGRATION');	
	
	if($_SESSION['authentication_key']==$_REQUEST['auth_key']) {		
		$completed = Migration_Utils::migrate($_SESSION['migration_info']);
		if ($completed == true) {
			echo "<script type='text/javascript'>window.parent.Migration_Complete();</script>";
		}
	}
}
?>
	</body>
</html>	