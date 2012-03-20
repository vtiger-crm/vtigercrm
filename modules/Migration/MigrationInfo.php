<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

global $conn;
global $query_count, $success_query_count, $failure_query_count;
global $success_query_array, $failure_query_array;

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

//This file is used to display the migration informations
?>


<table width="100%" border="0" cellpadding="0" cellspacing="0" height="100%">
   <tr>
	<td class="showPanelBg" valign="top" width="95%"  style="padding-left:20px; "><br />
		<span class="lvtHeaderText"> Settings &gt; Migrate from Previous Version </span>
       	        <hr noshade="noshade" size="1" />
	</td>
	<td width="5%" class="showPanelBg">&nbsp;</td>
   </tr>
   <tr>
	<td width="98%" style="padding-left:20px;" valign="top">
		<!-- module Select Table -->
		<table width="95%"  border="0" cellspacing="0" cellpadding="0" align="center" class="mailClient">
		   <tr>
			<td class="mailClientBg" width="7"></td>
			<td class="mailClientBg" style="padding-left:10px;padding-top:10px;vertical-align:top;">
				<table width="100%"  border="0" cellpadding="5" cellspacing="0">
				   <tr>
					   <td width="10%"><img src="<?php echo vtiger_imageurl('migrate.gif', $theme) ?>" align="absmiddle"/></td>
					<td width="90%">
						<span class="genHeaderBig">Migrate From Previous Version</span><br />
						Update your new vtiger CRM 5 database with the data from previous installation<br />
						To Start, follow the instructions below
					</td>
				   </tr>
				   <tr>
					<td colspan="2" bgcolor="#FFFFFF" class="hdrNameBg">
						<span class="genHeaderSmall">Migration Results</span>
				    		<hr /><br /><br />
						<div id="Mig_result" style="position:relative;border:2px solid #8BB73C;width:50%;left:25%;">
							<table>
							   <tr>
								<td><img id="migration_image" src="<?php echo vtiger_imageurl('migration_sucess.jpg', $theme) ?>" align="absmiddle" style="display:none;"/></td>
								<td><span id="migration_message1" class="genHeaderBig"> Migration is in progress. Please wait...</span><br />
								<span id="migration_message2" class="genHeaderGray">Your old data is now moving into new vtiger CRM</span></td>
							   </tr>
							</table>
						</div>
						
				   
						<br />


						<table width="90%" border="0" cellpadding="0" cellspacing="0" align="center">
						   <tr>
							<td width="45%">
								<!-- Display the Destination DB details - starts -->
								<table width="100%" cellpadding="5" cellspacing="0" style="border:3px solid #AAAAAA;">
								   <tr>
									<td colspan="2" class="detailedViewHeader">
										<b>Destination(Current) Database</b>
									</td>
								   </tr>
								   <tr>
									<td width="50%" align="left" class="dvtCellLabel">
										Host Name 
									</td>
									<td width="50%" align="left" class="dvtCellInfo">
										<?php echo $this->new_hostname; ?>
									</td>
								   </tr>
								   <tr>
									<td align="left" class="dvtCellLabel">
										MySQL Port No 
									</td>
									<td align="left" class="dvtCellInfo">
										<?php echo $this->new_mysql_port; ?>
									</td>
								   </tr>
								   <tr>
									<td align="left" class="dvtCellLabel">
										MySQL Username 
									</td>
									<td align="left" class="dvtCellInfo">
										<?php echo $this->new_mysql_username; ?>
									</td>
								   </tr>
								   <tr>
									<td align="left" class="dvtCellLabel">
										MYSQL Password
									</td>
									<td align="left" class="dvtCellInfo">
										<?php echo preg_replace('/./', '*', $this->new_mysql_password); ?>
									</td>
								   </tr>
								   <tr>
									<td align="left" class="dvtCellLabel">
										DB Name 
									</td>
									<td align="left" class="dvtCellInfo">
										<?php echo $this->new_dbname; ?>
									</td>
								   </tr>
								</table>
								<!-- Display the Destination DB details - ends -->
							</td>
							<td width="10%">&nbsp;</td>
							<td width="45%">
								<!-- Table to display the Source DB details - starts -->
								<table width="100%" cellpadding="5" cellspacing="0" style="border:3px solid #AAAAAA;">
								   <tr>
									<td colspan="2" class="detailedViewHeader">
										<b>Source Database</b>
									</td>
								   </tr>
								   <tr>
									<td width="50%" align="left" class="dvtCellLabel">
										Host Name 
									</td>
									<td width="50%" align="left" class="dvtCellInfo">
										<?php echo $this->old_hostname; ?>
									</td>
								   </tr>
								   <tr>
									<td align="left" class="dvtCellLabel">
										MySQL Port No 
									</td>
									<td align="left" class="dvtCellInfo">
										<?php echo $this->old_mysql_port; ?> 
									</td>
								   </tr>
								   <tr>
									<td align="left" class="dvtCellLabel">
										MySQL Username 
									</td>
									<td align="left" class="dvtCellInfo">
										<?php echo $this->old_mysql_username; ?>
									</td>
								   </tr>
								   <tr>
									<td align="left" class="dvtCellLabel">
										MYSQL Password
									</td>
									<td align="left" class="dvtCellInfo">
										<?php echo preg_replace('/./', '*', $this->old_mysql_password); ?>
									</td>
								   </tr>
								   <tr>
									<td align="left" class="dvtCellLabel">
										DB Name 
									</td>
									<td align="left" class="dvtCellInfo">
										<?php echo $this->old_dbname; ?>
									</td>
								   </tr>
								</table> 
								<!-- Table to display the Source DB details - ends -->
							</td>
						   </tr>
					   	</table>

						<br />


						<b>Migration Process Log</b>
						<div id="miLog" style="border:1px solid #666666;width:90%;position:relative;height:100px;overflow:auto;left:5%;top:10px;">
							<!-- we should place the Migration log here -->
							<?php echo $_SESSION['migration_log']; ?>
						</div>

						<br /><br />

						<b style="color:#006600">Migration Queries Log</b>
						<div id="successLog" style="border:1px solid #666666;width:90%;position:relative;height:250px;overflow:auto;left:5%;top:10px;">
							<!-- we should place the All queries executed-->
							<!-- This vtiger_table is designed to display all the queries (before this vtiger_table a div tag is opened. so all these following queries will be put inside the div vtiger_tab as a vtiger_table format) -->
							<style>
								.MigInfo{
									border-collapse:collapse;
									border:0px solid red;
								}
								.MigInfo tr td{
										border:1px solid #CCCCCC;
									      }
							</style>
							<table width="98%" cellpadding="3" cellspacing="0" border="0" class="MigInfo">
							   <tr>
							   	<td colspan=3 width="100%">
									<br>These following queries are executed to modify the 4.2.3/4.2 Patch2 database to 5.0 database.
								</td>
							   </tr>
							   <tr width="100%">
							   	<td width="20%"><b> Status Object </b></td>
								<td width="10%">Suceess/Failure</td>
								<td width="80%"> Query</td>
							   </tr>

							   <?php

					//Now all the queries will be placed inside the above div tag in tr tag

					//This file do the DBChanges between 42Patch2 to 5.0
					include("modules/Migration/DBChanges/42P2_to_50.php");

					//Modified on 05-01-2007
					$migrationlog->debug("Mickie ---- Patch Process ---- Starts \n\n\n");

					//This will be used in PatchApply.php file. if this is not set we can't proceed
					$source_version = '50';
					$continue_42P2 = true;

					//Now we have to include PatchApply.php which will include necessary patch files
					include("modules/Migration/PatchApply.php");

					$migrationlog->debug("Mickie ---- Patch Process ---- Ends \n\n\n");



							   ?>
							</table>							   
						</div>
						<!-- above div is used to display all the queries -->

						<br /><br />

						<b style="color:#FF0000">Failed Queries Log</b>
						<div id="failedLog" style="border:1px solid #666666;width:90%;position:relative;height:200px;overflow:auto;left:5%;top:10px;">
							<!-- we should place the failed queries here -->
							<?php
								foreach($failure_query_array as $failed_query)
								      echo '<br><font color="red">'.$failed_query.'</font>';
							?>
						</div>

						<br /><br />

						<!-- This vtiger_table is to show the total, success and failed queries -->
						<table width="35%" border="0" cellpadding="5" cellspacing="0" align="center" class="small">
						   <tr>
							<td width="75%" align="right" nowrap>
								Total Number of queries executed : 
							</td>
							<td width="25%" align="left">
								<b><?php echo $query_count;?> </b>
							</td>
						   </tr>
						   <tr>
							<td align="right">
								Queries Successed : 
							</td>
							<td align="left">
								<b style="color:#006600;">
									<?php echo $success_query_count;?>
								</b>
							</td>
						   </tr>
						   <tr>
							<td align="right">
								Queries Failed : 
							</td>
							<td align="left">
								<b style="color:#FF0000;">
									<?php echo $failure_query_count ;?>
								</b>
							</td>
						   </tr>
					   	</table>
	

						
						<br />
				
						Note :  Please copy and archive the failed queries log. This may help in future references.<br><br>
					</td>
				   </tr>
				   <tr bgcolor="#FFFFFF"><td colspan="2">&nbsp;</td></tr>
				   
				   <tr>
					<td colspan="2" align="center">
					   <form name="close_migration" method="post" action="index.php">
					   <input type="hidden" name="module" value="Settings">
					   <input type="hidden" name="action" value="index">
						<input type="submit" name="close" value=" &nbsp;Close&nbsp; " class="crmbutton small cancel" />
					   </form>
					</td>
				   </tr>
				</table>
			</td>
			<td class="mailClientBg" width="8"></td>
		   </tr>
		  </table>
		<br />

	</td>
	<td>&nbsp;</td>
   </tr>
</table>
