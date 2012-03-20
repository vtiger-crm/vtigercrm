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

require_once('install/VerifyDBHealth.php');

$migrationInfo = $_SESSION['migration_info'];
$source_directory = $migrationInfo['source_directory'];
require_once($source_directory.'config.inc.php');
$dbHostName = $dbconfig['db_hostname']; 
$dbName = $dbconfig['db_name'];

$newDbForCopy = $newDbName = $migrationInfo['new_dbname'];
if($dbName == $newDbForCopy) {
	$newDbForCopy = '';
}
$_SESSION['pre_migration'] = true;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_PRE_MIGRATION_TOOLS']?></title>
	<link href="themes/softed/style.css" rel="stylesheet" type="text/css">
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="include/js/general.js"></script>
	<script type="text/javascript" src="include/scriptaculous/prototype.js"></script>
	<script type="text/javascript" src="modules/com_vtiger_workflow/resources/jquery-1.2.6.js"></script>
	<script type="text/javascript">
		jQuery.noConflict();
		function fixDBHealth(){
			VtigerJS_DialogBox.progress();
			var value = jQuery('#auth_key').attr('value');
			var url = 'install.php?file=VerifyDBHealth.php&ajax=true&updateTableEngine=true&updateEngineForAllTables=true&auth_key='+value;
			jQuery.post(url,function(data,status){
				fnvshNrm('responsePopupContainer');
				jQuery('#responsePopupContainer').show();
				var element = jQuery('#responsePopup');
				if(status == 'success'){
					if(trim(data) == 'TABLE_TYPE_FIXED'){
						element.attr('innerHTML', '<?php echo $installationStrings['MSG_SUCCESSFULLY_FIXED_TABLE_TYPES']; ?>');
						jQuery('#databaseFixMessageDiv').hide();
					} else {
						element.attr('innerHTML', '<?php echo $installationStrings['ERR_FAILED_TO_FIX_TABLE_TYPES']; ?>');
					}
				}else{
					element.attr('innerHTML', '<?php echo $installationStrings['ERR_FAILED_TO_FIX_TABLE_TYPES']; ?>');
				}
				jQuery('#dbMirrorCopy').hide();
				VtigerJS_DialogBox.hideprogress();
				placeAtCenter(document.getElementById('responsePopupContainer'));
			});			
		}
		
		function viewDBReport(){
			var value = jQuery('#auth_key').attr('value');
			var url = 'install.php?file=VerifyDBHealth.php&ajax=true&viewDBReport=true&auth_key='+value;
			window.open(url,'DBHealthCheck', 'width=700px, height=500px, resizable=1,menubar=0, location=0, toolbar=0,scrollbars=1');			
		}
		
		function getDbDump(){
			var value = jQuery('#auth_key').attr('value');
			var url = 'install.php?file=MigrationDbBackup.php&mode=dump&auth_key='+value;
			window.open(url,'DatabaseDump', 'width=800px, height=600px, resizable=1,menubar=0, location=0, toolbar=0,scrollbars=1');
		}
		
		function doDBCopy(){
			var dbName = jQuery('#newDatabaseName').attr('value');
			if (trim(dbName) == '') {
				alert("<?php echo $installationStrings['ERR_SPECIFY_NEW_DATABASE_NAME']; ?>");
				jQuery('#newDatabaseName').focus();
				return false;
			}
			var rootUserName = jQuery('#rootUserName').attr('value');
			if (trim(rootUserName) == '') {
				alert("<?php echo $installationStrings['ERR_SPECIFY_ROOT_USER_NAME']; ?>");
				jQuery('#rootUserName').focus();
				return false;
			}
			VtigerJS_DialogBox.progress();
			var rootPassword = jQuery('#rootPassword').attr('value');			
			var value = jQuery('#auth_key').attr('value');
			var url = 'install.php?file=MigrationDbBackup.php&mode=copy&auth_key='+value;
			url += ('&newDatabaseName='+dbName+'&rootUserName='+rootUserName+'&rootPassword='+rootPassword+'&createDB=true');
			jQuery.post(url,function(data,status){
				fnvshNrm('responsePopupContainer');
				jQuery('#responsePopupContainer').show();
				var element = jQuery('#responsePopup');
				if(status == 'success'){
					if(data != 'true' && data != true){
						element.attr('innerHTML', '<?php echo $installationStrings['ERR_DATABASE_COPY_FAILED']; ?>.');
					}else{
						element.attr('innerHTML', '<?php echo $installationStrings['MSG_DATABASE_COPY_SUCCEDED']; ?>');
					}
				}else{
					element.attr('innerHTML', '<?php echo $installationStrings['ERR_DATABASE_COPY_FAILED']; ?>.');
				}
				jQuery('#dbMirrorCopy').hide();
				VtigerJS_DialogBox.hideprogress();
				placeAtCenter(document.getElementById('responsePopupContainer'));
			});
		}
		
		function showCopyPopup(){
			fnvshNrm('dbMirrorCopy');
			jQuery('#dbMirrorCopy').show();
			placeAtCenter(document.getElementById('dbMirrorCopy'));
		}
		
	</script>
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
							<table cellspacing=5 cellpadding=2 width=95% align=center>
								<tr>
									<td align="left" colspan="2" class="small paddingTop">
										<span class="bigHeading"><?php echo $installationStrings['LBL_PRE_MIGRATION_TOOLS']; ?></span>
										<br/>
				  						<hr size="1" noshade=""/>
				  					</td>
				  				</tr>
								<?php if($_SESSION[$newDbName.'_'.$dbHostName.'_HealthApproved'] != true) { ?>
								<tr>
									<td colspan=2>
										<div id="databaseFixMessageDiv" class="helpmessagebox paddingPoint5em smallFont" align="left">
											<span class="redColor fontBold"><?php echo $installationStrings['LBL_IMPORTANT']; ?>:</span>
											<hr />											
											<?php echo $installationStrings['ERR_TABLES_NOT_INNODB'] .'. '. $installationStrings['MSG_CHANGE_ENGINE_BEFORE_MIGRATION']; ?>.<br/>
											<br />
											<a href="javascript:void(0)" onclick="fixDBHealth();"><?php echo $installationStrings['LBL_FIX_NOW']; ?></a>&nbsp; | &nbsp;<a href="javascript:void(0)" onclick="viewDBReport();"><?php echo $installationStrings['LBL_VIEW_REPORT']; ?></a>
										</div>
									</td>
								</tr>								
								<?php } ?>
								<tr>
									<td colspan=2>
										<table cellpadding="0" cellspacing="1" align=right width="100%" class="level3">
											<tr >
												<td align=left width=50% valign=top>
													<table cellpadding="5" cellspacing="1" align=right width="100%" border="0">
														<tr>
															<td width="48%" align="left">
																<table width="100%" cellspacing="0" cellpadding="5" border="0">
																	<tr>
																		<td width="50" valign="top" rowspan="2">
																			<input type="image" src="include/install/images/dbDump.gif" alt="<?php echo $installationStrings['LBL_DB_DUMP_DOWNLOAD']; ?>" border="0" title="<?php echo $installationStrings['LBL_DB_DUMP_DOWNLOAD']; ?>" onClick="getDbDump();">
																		</td>
																		<td valign="bottom" class="heading2"><?php echo $installationStrings['LBL_DATABASE_BACKUP']; ?></td>
																	</tr>
																	<tr>
																		<td valign="top" class="mediumLineHeight">
																			<b><?php echo $installationStrings['QUESTION_NOT_TAKEN_BACKUP_YET']; ?></b><br>
																			<?php echo $installationStrings['LBL_CLICK_FOR_DUMP_AND_SAVE']; ?>.<br><br>
																			<div class="helpmessagebox"><b><?php echo $installationStrings['LBL_NOTE']; ?></b>:<br><?php echo $installationStrings['MSG_PROCESS_TAKES_LONGER_TIME_BASED_ON_DB_SIZE']; ?>.</div>
																		</td>
																	</tr>
																</table>
															</td>
															<td height="100%" width="2%" style="border-left:2px dotted #999999;"></td>
															<td width="48%" align="left">
																<table width="100%" cellspacing="0" cellpadding="5" border="0">
																	<tr>
																		<td width="50" valign="top" rowspan="2">
																			<input type="image" src="include/install/images/dbCopy.gif" alt="<?php echo $installationStrings['LBL_DB_COPY']; ?>" border="0" title="<?php echo $installationStrings['LBL_DB_COPY']; ?>" onClick="showCopyPopup();">
																		</td>
																		<td valign="bottom" class="heading2"><?php echo $installationStrings['LBL_DATABASE_COPY']; ?></td>
																	</tr>
																	<tr>
																		<td valign="top" class="mediumLineHeight">
																			<b><?php echo $installationStrings['QUESTION_MIGRATING_TO_NEW_DB']; ?>?</b><br>
																			<?php echo $installationStrings['LBL_CLICK_FOR_NEW_DATABASE']; ?>.																			
																			<br><br>
																			<div class="helpmessagebox"><b><?php echo $installationStrings['LBL_RECOMMENDED']; ?></b>:<br>
																				<?php echo $installationStrings['MSG_USE_OTHER_TOOLS_FOR_DB_COPY']; ?>.
																			</div>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
													<br>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr valign=top>
									<td align=left>
										<form action="install.php" method="post" name="form" id="form">
											<input type="hidden" name="file" value="SetMigrationConfig.php">
											<input type="submit" class="button" value="&#139;&#139;&nbsp;<?php echo $installationStrings['LBL_BACK']; ?>" title="<?php echo $installationStrings['LBL_BACK']; ?>" />
										</form>
									</td>
									<td align=right>
										<form action="install.php" name="migrateform" id="migrateform" method="post">
											<input type="hidden" name="auth_key" id="auth_key" value="<?php echo $_SESSION['authentication_key']; ?>" />
											<input type="hidden" name="file" value="ConfirmMigrationConfig.php" />
											<input type="hidden" name="forceDbCheck" value="true" />											
											<input type="button" class="button" value="<?php echo $installationStrings['LBL_NEXT']; ?>&nbsp;&#155;&#155;" title="<?php echo $installationStrings['LBL_NEXT']; ?>" onClick="migrateform.submit();">
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
<div id="dbMirrorCopy" class="posLayPopup" style="display: none;">
	<div class="floatRightTiny" onmouseover="this.className= 'floatRightTinyOn';" onmouseout="this.className= 'floatRightTiny';"><a href="javascript: void(0);" onClick="fninvsh('dbMirrorCopy');"><img src="themes/images/close.gif" border=0></a></div>
	<div class="paddingPoint5em"><b><?php echo $installationStrings['LBL_COPY_OLD_DB_TO_NEW_DB'] ?></b></div>
	<table cellpadding="5" cellspacing="2" width="100%" border="0">
		<tbody>
			<tr class="dvtCellLabel">
				<td width="25%" nowrap valign='top'><?php echo $installationStrings['LBL_NEW']. ' ' .$installationStrings['LBL_DATABASE_NAME']; ?> <sup><font class="redColor">*</font></sup></td>
				<td>
					<input type='text' class="detailedViewTextBox" name='newDatabaseName' id='newDatabaseName' value='<?php echo $newDbForCopy ?>'>
					<br><?php echo $installationStrings['LBL_IF_DATABASE_EXISTS_WILL_RECREATE'] ?>.			
				</td>								
			</tr>
			<tr class="dvtCellLabel">
				<td width="25%" nowrap valign='top'>Root <?php echo $installationStrings['LBL_USER_NAME'] ?> <sup><font class="redColor">*</font></sup></td>
				<td><input type='text' class="detailedViewTextBox" name='rootUserName' id='rootUserName' value=''>
					<br><?php echo $installationStrings['LBL_SHOULD_BE_PRIVILEGED_USER'] ?>.
				</td>
			</tr>
			<tr class="dvtCellLabel">
				<td width="25%">Root <?php echo $installationStrings['LBL_PASSWORD'] ?></td>
				<td><input type='password' class="detailedViewTextBox" name='rootPassword' id='rootPassword' value=''></td>
			</tr>
			<tr class="dvtCellLabel">
				<td colspan="2" align="center"><input type='button' class='crmbuttom small create' name='copy' value='Copy Now' onclick='doDBCopy();'></td>
			</tr>
		</tbody>
	</table>
	<br>
	<div class="helpmessagebox"><span class='redColor fontBold'><?php echo $installationStrings['LBL_NOTE']; ?>:</span> <?php echo $installationStrings['MSG_PROCESS_TAKES_LONGER_TIME_BASED_ON_DB_SIZE']; ?>.</div>
</div>
<div id='responsePopupContainer' class="posLayPopup" style="display: none;" align="center">
	<div class="floatRightTiny" onmouseover="this.className= 'floatRightTinyOn';" onmouseout="this.className= 'floatRightTiny';"><a href="javascript: void(0);" onClick="fninvsh('responsePopupContainer');"><img src="themes/images/close.gif" border=0></a></div>
	<div id='responsePopup' style="margin-top: 1.1em;" class="fontBold">&nbsp;</div>
</div>
</body>
</html>