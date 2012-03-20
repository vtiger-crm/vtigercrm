<?php 
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $mod_strings;
global $app_strings;
global $theme;
$charset = $app_strings['LBL_CHARSET'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<meta content="text/html; charset=<?php echo $charset;?>" http-equiv="content-type"/>
<meta name="author" content="rolosworld@gmail.com"/>
<meta http-equiv="expires" content="-1"/>
<meta http-equiv="pragma" content="no-cache"/>

<title><?php echo $mod_strings['TITLE_AJAX_CSS_POPUP_CHAT'];?></title>

<!-- NEEDED SCRIPTS  -->
<script type="text/javascript" charset="<?php echo $charset?>" src="include/js/general.js"></script>
<script type="text/javascript" charset="<?php echo $charset?>" src="modules/Home/js/ajax.js"></script>
<script type="text/javascript" charset="ISO-8859-1" src="modules/Home/js/dom-drag_p.js"></script>
<script type="text/javascript" charset="ISO-8859-1" src="modules/Home/js/css-window_p.js"></script>
<script type="text/javascript" charset="<?php echo $charset?>" src="modules/Home/js/chat.js"></script>
<!-- /NEEDED SCRIPTS -->


<script type="text/javascript">
<!--
function showPopup()
{
  var conf = new Array();
  conf["dt"] = 1000;
  conf["width"] = "400px";
  conf["height"] = "300px";
  conf["ulid"] = "uli";
  conf["pchatid"] = "chat";


  // USED TO INITIALIZE THE SESSION, I SUGGEST CALLING THIS ON BODY onload
  //   Chat(<conf array>);
  // NOTICE THE ChatStuff IS THE NAME OF THE ABOVE FUNCTION!!!
  var mychat = new Chat(conf);

}

-->
</script>

<!-- CSS classes for the popups -->
<link rel="stylesheet" type="text/css" href="themes/<?php echo $theme;?>/chat.css"/>

</head>

<body onload="showPopup();" style="background-image:url(themes/<?php echo $theme;?>/images/site_bg.gif);color:#ffffff;">


<!-- THIS IS NEEDED FOR THE USERS LIST TO APPEAR, -->
<!-- THE id CAN BE CHANGED, BUT HAS TO BE PASSED TO UList() -->
<!-- <table>
<tr>	
	<td rowspan="2">
		
		<div id="chat"></div>
	</td>
	<td class="chatuserlist">User List</td>

</tr>
<tr>
	<td valign="top">
		<ul id="uli"></ul>
	</td>	
</tr>
</table> -->
<!-- THIS IS NEEDED FOR DEBUG MSG'S TO APPEAR -->

<table width="550" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
			<table width="150" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="pchathead"></td>
					<td class="pchathead1"><b><?php echo $mod_strings['User List']; ?></b></td>
					<td class="pchathead2"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="pchatbody"></td>
					<td class="pchatbody1" style="height: 300px;text-align:left;vertical-align:top;">
						<div class="chatbox" style="overflow-y:auto;overflow-x:hidden; width: 100%; height: 100%;">
							<span id="uli"></span>
						</div>
					</td>
					<td class="pchatbody2"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="pchatfoot"></td>
					<td class="pchatfoot1"></td>
					<td class="pchatfoot2"></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
		<td width="400" valign="top">
			<div id="chat"></div>
		</td>
	</tr>
</table>



<div id="debug"></div>

</body>
</html>
