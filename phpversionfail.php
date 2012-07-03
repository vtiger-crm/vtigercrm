<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>vtiger CRM 5 - PHP Version Check</title>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
</head>

<body class="small cwPageBg" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

	<br><br><br>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td class="cwHeadBg" align=left>&nbsp;</td>
		<td class="cwHeadBg" align=right><img src="include/install/images/vtigercrm5.gif" alt="vtiger CRM 5" title="vtiger CRM 5"></td>
	</tr>
	</table>

	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td background="include/install/images/topInnerShadow.gif" align=left><img src="include/install/images/topInnerShadow.gif" ></td>
	</tr>
	</table>

	<table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
	<tr>
		<td class="small" bgcolor="#FFFFFF" align=center>
			PHP 5.2.x or above is required. Your current PHP version is
			<?php
				if(isset($serverPhpVersion)) {
					echo $serverPhpVersion;
				} else  {
					echo '???';
				}
			?> <br/>
			Kindly upgrade the PHP installation, and try again! <br/>
		</td>
	</tr>
	</table>

	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td background="include/install/images/topInnerShadow.gif" align=left><img src="include/install/images/topInnerShadow.gif" ></td>
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
