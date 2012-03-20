<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Dashboard/index.php,v 1.2 2004/10/06 09:02:05 jack Exp $
 * Description:  Main file for the Home module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>
		    		<table width="20%"  border="0" cellspacing="0" cellpadding="0" align="left">
				<tr>
	 		   		<td rowspan="2"><span class="dash_count">1</span>&nbsp;&nbsp;</td>
				 	<td nowrap><span class="genHeaderSmall"><?php echo $mod_strings['LBL_SALES_STAGE_FORM_TITLE']; ?></span></td>
				 </tr>
				 <tr>
				 	<td><span class="big"><?php echo $mod_strings['LBL_HORZ_BAR_CHART'];?></span> </td>
				</tr>
				</table>
		    	</td>
		</tr>
		<tr>
			<td height="200"><?php include ("modules/Dashboard/Chart_pipeline_by_sales_stage.php");?></td>
		</tr>
		</table>
	</td>	
	<!-- SCEOND CHART  -->
  	<td width="50%">
	  	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	  	<tr>
		    	<td>
				<table width="20%"  border="0" cellspacing="0" cellpadding="0" align="left">
				<tr>
					<td rowspan="2"><span class="dash_count">2</span>&nbsp;&nbsp;</td>
				 	<td nowrap><span class="genHeaderSmall"><?php echo $mod_strings['LBL_MONTH_BY_OUTCOME'];?></span></td>
				</tr>
				<tr>
					<td><span class="big"><?php echo $mod_strings['LBL_VERT_BAR_CHART'];?></span> </td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="200"><?php include ("modules/Dashboard/Chart_outcome_by_month.php"); ?></td>
	  	</tr>
		</table>
	</td>
</tr>	
<tr>
	<td colspan=2><hr noshade="noshade" size="1" /></td>
</tr>
<!-- SECOND ROW FIRST CHART  -->
<tr>
	<td width="50%">
	  	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<table width="20%"  border="0" cellspacing="0" cellpadding="0" align="left">
				<tr>
					<td rowspan="2"><span class="dash_count">3</span>&nbsp;&nbsp;</td>
				 	<td nowrap><span class="genHeaderSmall"><?php echo $mod_strings['LBL_LEAD_SOURCE_BY_OUTCOME'];?></span></td>
				</tr>
				<tr>
				       	<td><span class="big"><?php echo $mod_strings['LBL_HORZ_BAR_CHART'];?></span> </td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="200"><?php include ("modules/Dashboard/Chart_lead_source_by_outcome.php");?></td>

		</tr>
		</table>
	</td> 
	<!-- FOURTH CHART  -->
	<td>
  		<table width="20%"  border="0" cellspacing="0" cellpadding="0" align="left">
		<tr>
			<td>
				<table width="20%"  border="0" cellspacing="0" cellpadding="0" align="left">
				<tr>
					<td rowspan="2"><span class="dash_count">4</span>&nbsp;&nbsp;</td>
				 	<td nowrap><span class="genHeaderSmall"><?php echo $mod_strings['LBL_LEAD_SOURCE_FORM_TITLE'];?></span></td>
				</tr>
				<tr>
			       		<td><span class="big"><?php echo $mod_strings['LBL_PIE_CHART'];?></span> </td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="200"><?php include ("modules/Dashboard/Chart_pipeline_by_lead_source.php") ?></td>
	  	</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan=2><hr noshade="noshade" size="1" /></td>
</tr>
</table>
<script id="dash_script">
	var gdash_display_type = 'MATRIX';
</script>
