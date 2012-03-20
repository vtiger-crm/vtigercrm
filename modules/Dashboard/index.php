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
 * $Header: 
 * Description:  Main file for the Home module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

global $app_strings;
global $app_list_strings;
global $mod_strings;

global $currentModule;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');
require_once('include/logging.php');

$graph_array = Array(
	  "DashboardHome" => $mod_strings['DashboardHome'],
          "leadsource" => $mod_strings['leadsource'],
          "leadstatus" => $mod_strings['leadstatus'],
          "leadindustry" => $mod_strings['leadindustry'],
          "salesbyleadsource" => $mod_strings['salesbyleadsource'],
          "salesbyaccount" => $mod_strings['salesbyaccount'],
	  "salesbyuser" => $mod_strings['salesbyuser'],
	  "salesbyteam" => $mod_strings['salesbyteam'],
          "accountindustry" => $mod_strings['accountindustry'],
          "productcategory" => $mod_strings['productcategory'],
	  "productbyqtyinstock" => $mod_strings['productbyqtyinstock'],
	  "productbypo" => $mod_strings['productbypo'],
	  "productbyquotes" => $mod_strings['productbyquotes'],
	  "productbyinvoice" => $mod_strings['productbyinvoice'],
          "sobyaccounts" => $mod_strings['sobyaccounts'],
          "sobystatus" => $mod_strings['sobystatus'],
          "pobystatus" => $mod_strings['pobystatus'],
          "quotesbyaccounts" => $mod_strings['quotesbyaccounts'],
          "quotesbystage" => $mod_strings['quotesbystage'],
          "invoicebyacnts" => $mod_strings['invoicebyacnts'],
          "invoicebystatus" => $mod_strings['invoicebystatus'],
          "ticketsbystatus" => $mod_strings['ticketsbystatus'],
          "ticketsbypriority" => $mod_strings['ticketsbypriority'],
	  "ticketsbycategory" => $mod_strings['ticketsbycategory'], 
	  "ticketsbyuser" => $mod_strings['ticketsbyuser'],
	  "ticketsbyteam" => $mod_strings['ticketsbyteam'],
	  "ticketsbyproduct"=> $mod_strings['ticketsbyproduct'],
	  "contactbycampaign"=> $mod_strings['contactbycampaign'],
	  "ticketsbyaccount"=> $mod_strings['ticketsbyaccount'],
	  "ticketsbycontact"=> $mod_strings['ticketsbycontact'],
          );
          
$log = LoggerManager::getLogger('dashboard');
if(isset($_REQUEST['type']) && $_REQUEST['type'] != '')
{
	$dashboard_type = $_REQUEST['type'];
}else
{
	$dashboard_type = 'DashboardHome';
}
?>

<TABLE border=0 cellspacing=0 cellpadding=0 width=100% class=small>
<tr><td style="height:2px"></td></tr>
<tr>
	<td style="padding-left:10px;padding-right:30px" class="moduleName" width="20%" nowrap><?php echo $app_strings['Analytics'];?> &gt; <a class="hdrLink" href="index.php?action=index&parenttab=Analytics&module=Dashboard"><?php echo $app_strings['Dashboard'] ?></a></td>

	<td  nowrap width="8%">
		<table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td class="sep1" style="width:1px;"></td>
			<td class=small>
				<table border=0 cellspacing=0 cellpadding=5>
				<tr>
					<td style="padding-right:0px;padding-left:10px;"><img src="<?php echo vtiger_imageurl('btnL3Add-Faded.gif', $theme) ?>" border=0></td>	
					 <td style="padding-right:10px"><img src="<?php echo vtiger_imageurl('btnL3Search-Faded.gif', $theme) ?>" border=0></td>
				</tr>
				</table>
	</td>
			</tr>
			</table>
	</td>
	<td width="20">&nbsp;</td>
                <td class="small" width="10%" align="left">
				<table border=0 cellspacing=0 cellpadding=5>

				<tr>
<?php 
if($CALENDAR_DISPLAY == 'true') { 
?> 
					<td style="padding-right:0px;padding-left:10px;"><a href="javascript:;" onClick='fnvshobj(this,"miniCal");getMiniCal("parenttab=My Home Page");'><img src="<?php echo $image_path;?>btnL3Calendar.gif" alt="<?php echo $app_strings['LBL_CALENDAR_ALT']; ?>" title="<?php echo $app_strings['LBL_CALENDAR_TITLE']; ?>" border=0></a></a></td>
<?php 
} 
if($WORLD_CLOCK_DISPLAY == 'true') { 
?> 
					<td style="padding-right:0px"><a href="javascript:;"><img src="<?php echo $image_path;?>btnL3Clock.gif" alt="<?php echo $app_strings['LBL_CLOCK_ALT']; ?>" title="<?php echo $app_strings['LBL_CLOCK_TITLE']; ?>" border=0 onClick="fnvshobj(this,'wclock');"></a></a></td>
<?php 
} 
if($CALCULATOR_DISPLAY == 'true') { 
?>
					<td style="padding-right:0px"><a href="#"><img src="<?php echo $image_path;?>btnL3Calc.gif" alt="<?php echo $app_strings['LBL_CALCULATOR_ALT']; ?>" title="<?php echo $app_strings['LBL_CALCULATOR_TITLE']; ?>" border=0 onClick="fnvshobj(this,'calculator_cont');fetch_calc();"></a></td>
<?php 
} 
if($CHAT_DISPLAY == 'true') { 
?> 		
			<td style="padding-right:10px"><a href="javascript:;" onClick='return window.open("index.php?module=Home&action=vtchat","Chat","width=600,height=450,resizable=1,scrollbars=1");'><img src="<?php echo $image_path;?>tbarChat.gif" alt="<?php echo $app_strings['LBL_CHAT_ALT']; ?>" title="<?php echo $app_strings['LBL_CHAT_TITLE']; ?>" border=0></a>
<?php 
} 
?> 
                    </td>	
			        <td style="padding-right: 10px;"><img src="<?php echo $image_path;?>btnL3Tracker.gif" alt="<?php echo $app_strings['LBL_LAST_VIEWED']; ?>" title="<?php echo $app_strings['LBL_LAST_VIEWED']; ?>" onclick="fnvshobj(this,'tracker');" style="cursor:pointer;" border="0"></td>
				</tr>
				</table>
	</td>
	<td width="20">&nbsp;</td>
               <td class="small" align="left" width="5%">
		<table border=0 cellspacing=0 cellpadding=5>
			<tr>
				<td style="padding-right:0px;padding-left:10px;"><img src="<?php echo vtiger_imageurl('tbarImport-Faded.gif', $theme) ?>" border="0"></td>
                <td style="padding-right:10px"><img src="<?php echo vtiger_imageurl('tbarExport-Faded.gif', $theme) ?>" border="0"></td>
			</tr>
		</table>	
	</td>
	<td width="20">&nbsp;</td>
                <td class="small" align="left">	
				<table border=0 cellspacing=0 cellpadding=5>
				<tr>
				<td style="padding-left:10px;"><a href="javascript:;" onmouseout="fninvsh('allMenu');" onClick="fnvshobj(this,'allMenu')"><img src="<?php echo $image_path;?>btnL3AllMenu.gif" alt="<?php echo $app_strings['LBL_ALL_MENU_ALT']; ?>" title="<?php echo $app_strings['LBL_ALL_MENU_TITLE']; ?>" border="0"></a></td>
				</tr>
				</table>
	</td>			
	</tr>
	</table>
	</td>
	
</tr>
<tr><td style="height:2px"></td></tr>
</TABLE>
<br>

<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<a name="top"></a>
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
     <tr>
        <td valign=top><img src="<?php echo vtiger_imageurl('showPanelTopLeft.gif', $theme) ?>"></td>

	<td class="showPanelBg" valign="top" width=100% style="padding:10px;">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="100%">
			<!-- DASHBOARD DEGINS HERE -->
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="small">
			<tr>
				<td class="dash_top" colspan="3">
				<!-- TOP SELECT OPTION -->
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="3%">&nbsp;</td>
						<td align="left">
							<table border="0" cellpadding="0" cellspacing="5" class="dashSelectBg">
							<tr>
								<td>
								<select name="dashordlists" id="dashboard_combo" onChange="loadDashBoard(this);">
								 <?php 									 
						                foreach($graph_array as $key=>$value)   
                 						{
									if($dashboard_type == $key)
									{
										$dash_board_title = $value;
											?><option selected value="<?php echo $key;?>"><?php echo $value;?></option><?php
									}else
									{
										?><option value="<?php echo $key;?>"><?php echo $value;?></option>
                 						<?php   }
								} ?>
								</select>
								</td>
							</tr>
							</table>
						
						</td>
						<td align="right" class="dashHeading"><?php echo $mod_strings['LBL_DASHBOARD'] ?></td>
						<td width="3%">&nbsp;</td>

									</tr>
								</table>
							<!-- END OF TOP SELECTION -->
						</td>
					</tr>
					<tr>
						<td class="dash_border" width="1%"><img src="<?php echo vtiger_imageurl('dash_screw.gif', $theme) ?>" border="0" align="absmiddle"></td>
						<td class="hdrNameBg small" style="height: 12px;" width="98%">&nbsp;</td>
						<td class="dash_border" width="1%"><img src="<?php echo vtiger_imageurl('dash_screw.gif', $theme) ?>" border="0" align="absmiddle"></td>

					</tr>
					<tr>
						<td class="dash_border">&nbsp;</td>
						<td class="dash_white genHeaderBig dash_bdr_btm">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="90%" nowrap>
							<?php echo $app_strings['Dashboard']; ?> &gt; <?php echo $app_strings['Home'];?> &gt; <span id="dashTitle_div"><?php echo $dash_board_title; ?></span>
							</td>
							<td align="right" width="10%"><img alt="<?php echo $mod_strings['NORMALVIEW'];?>" title="<?php echo $mod_strings['NORMALVIEW'];?>" style="cursor:pointer;" onClick="changeView('NORMAL');" src="<?php echo $image_path;?>dboardNormalView.gif" align="absmiddle" border="0">&nbsp;|&nbsp;<img alt="<?php echo $mod_strings['GRIDVIEW'];?>" title="<?php echo $mod_strings['GRIDVIEW'];?>" style="cursor:pointer;" onClick="changeView('MATRIX');" src="<?php echo $image_path;?>dboardMatrixView.gif" align="absmiddle" border="0"></td>
						</tr>
						</table>
						</td>
						<td class="dash_border">&nbsp;</td>
					</tr>

					<tr>
						<td class="dash_border">&nbsp;</td>
						<td class="dash_white"  style="height:500px;"><div id="dashChart">
							<!-- NAVIGATION TABLE -->
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>

									<td width="45%" align="right">&nbsp;
									</td>
								</tr>

							</table>
							<!-- END OF NAVIGATION -->
							<!-- CHART ONE TABLE -->
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td height="300">
								
								<?php 
									if(!isset($_REQUEST['type']))
									{
										if(isset($_REQUEST['display_view']) && $_REQUEST['display_view'] == 'MATRIX')
										{
											require_once('modules/Dashboard/DashboardHome_matrix.php'); 
										}else
										{
											require_once('modules/Dashboard/DashboardHome.php'); 
										}
									}else
									{
										require_once('modules/Dashboard/loadDashBoard.php'); 
									}
								?>	
								&nbsp;</td>
							</tr>
							</table>
							<!-- End of CHART 1 -->
							
						</div></td>
						<td class="dash_border">&nbsp;</td>
					</tr>

					<tr>
						<td class="dash_border" width="1%"><img src="<?php echo vtiger_imageurl('dash_screw.gif', $theme) ?>" border="0" align="absmiddle"></td>
						<td class="dash_white" width="98%">&nbsp;</td>
						<td class="dash_border" width="1%"><img src="<?php echo vtiger_imageurl('dash_screw.gif', $theme) ?>" border="0" align="absmiddle"></td>
					</tr>
					<tr>
						<td colspan="3" class="dash_bottom">
						<!-- BOTTOM NAVICATION -->
							<table width="100%" cellpadding="0" cellspacing="0" border="0">

									<tr>
										<td width="3%">&nbsp;</td>
										<td align="left">									  <table border="0" cellpadding="0" cellspacing="5" class="dashSelectBg">
                                          <tr>
                                            <td><select name="dashordlists" id="dashboard_combo1" onChange="loadDashBoard(this);">
                                                <?php 									 
						                foreach($graph_array as $key=>$value)   
                 						{
									if($dashboard_type == $key)
									{
										$dash_board_title = $value;
											?>
                                                <option selected value="<?php echo $key;?>"><?php echo $value;?></option>
                                                <?php
									}else
									{
										?>
                                                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                                <?php   }
								} ?>
                                              </select>
                                            </td>
                                          </tr>
                                        </table></td>
										<td align="right">&nbsp;</td>
										<td width="3%">&nbsp;</td>
									</tr>
								</table>
						<!-- END OF BOTTOM NAVIGATION -->
						</td>
					</tr>
					<tr>

						<td colspan="3">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="112"><img src="<?php echo vtiger_imageurl('dash_btm_left.jpg', $theme) ?>" border="0" align="absmiddle"></td>
									<td width="100%" class="dash_btm">&nbsp;</td>
									<td width="129"><img src="<?php echo vtiger_imageurl('dash_btm_right.jpg', $theme) ?>" border="0" align="absmiddle"></td>
								</tr>
							</table>
						</td>

					</tr>
				</table>
			<!-- END -->
		</td>
	</tr>
</table>
</td>
<td valign=top><img src="<?php echo vtiger_imageurl('showPanelTopRight.gif', $theme) ?>"></td>
   </tr>
</table>

</body>
</html>


<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>
<script language="javascript" type="text/javascript" src="include/scriptaculous/scriptaculous.js"></script>
<script>
function loadDashBoard(oSelect)
{
	Effect.Fade("dashChart");
	var oCombo = $('dashboard_combo');
	var oCombo1 = $('dashboard_combo1');
	oCombo.selectedIndex = oSelect.selectedIndex;
	oCombo1.selectedIndex = oSelect.selectedIndex;
	var type = oSelect.options[oSelect.selectedIndex].value; 
	if(type != 'DashboardHome')
		url = 'module=Dashboard&action=DashboardAjax&display_view='+gdash_display_type+'&file=loadDashBoard&type='+type;
	else	
		url = 'module=Dashboard&action=DashboardAjax&file=DashboardHome&display_view='+gdash_display_type;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: url,
			onComplete: function(response)
			{
				$("dashChart").innerHTML=response.responseText;
				$("dashChart").style.display='none';
				setTimeout('Effect.Appear("dashChart")', 500);
				var dashst = document.getElementById('dash_script');
				eval(dashst.innerHTML);
				$("dashTitle_div").innerHTML = oCombo.options[oCombo.selectedIndex].text;
			}
		}
	);	
}

function changeView(displaytype)
{
	gdash_displaytype = displaytype;
	var oCombo = $('dashboard_combo');
	var type = oCombo.options[oCombo.selectedIndex].value; 
	var currenttime = new Date();
	var time="&time="+currenttime.getTime();
	if(type == 'DashboardHome')
	{
		if(displaytype == 'MATRIX')
			url = 'index.php?module=Dashboard&action=index&display_view=MATRIX';
		else	
			url = 'index.php?module=Dashboard&action=index&display_view=NORMAL';
	}	
	else	
	{
		if(displaytype == 'MATRIX')
			url = 'index.php?module=Dashboard&action=index&display_view=MATRIX&type='+type;
		else
			url = 'index.php?module=Dashboard&action=index&display_view=NORMAL&type='+type;
	}
	window.document.location.href = url+time;

}
</script>
