{*<!--

/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

-->*}

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
<link rel="stylesheet" media="print" href="print.css" type="text/css">
<title>vtiger -  {$MOD.LBL_PRINT_REPORT}</title>
<style>
{literal}
body{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}

body table{
	border-collapse:collapse;
}

body table tr td{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	border:0px solid #000000;
}
																							
.printReport{
	width:100%;
	border:1px solid #000000;
    border-collapse:collapse;
}
.printReport tr td{
	border:1px dotted #000000;
	text-align:left;
}
.printReport tr th{
	border-bottom:2px solid #000000;
	border-left:1px solid #000000;
	border-top:1px solid #000000;
	border-right:1px solid #000000;
}
{/literal}
</style>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" style="text-align:center;" onLoad="JavaScript:window.print()">
	<table width="80%" border="0" cellpadding="5" cellspacing="0" align="center">
	<tr>
		<td align="left" valign="top" style="border:0px solid #000000;">
		<h2>{$MOD.$REPORT_NAME}</h2>
		<font  color="#666666"><div id="report_info"></div></font>
		</td>
		<td align="right" style="border:0px solid #000000;" valign="top"><h3 style="color:#CCCCCC">{$COUNT} {$APP.LBL_RECORDS}</h3></td>
	</tr>
	<tr>
		<td style="border:0px solid #000000;" colspan="2">
		<table width="100%" border="0" cellpadding="5" cellspacing="0" align="center" class="printReport" >
		{$PRINT_CONTENTS}
		</table>
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
	<td colspan="2">
	{$TOTAL_HTML}
	</td>
	<tr>
	</table>
</body>
<script>
document.getElementById('report_info').innerHTML = window.opener.document.getElementById('report_info').innerHTML;
</script>
</html>

