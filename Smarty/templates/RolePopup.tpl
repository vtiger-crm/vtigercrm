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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<style type="text/css">@import url("themes/{$THEME}/style.css");</style>
<style type="text/css">
	a.x {ldelim}
		color:black;
		text-align:center;
		text-decoration:none;
		padding:5px;
		font-weight:bold;
	{rdelim}
	
	a.x:hover {ldelim}
		color:#333333;
		text-decoration:underline;
		font-weight:bold;
	{rdelim}

	li {ldelim}
		background:transparent;
		padding:0px;
		margin:0px 0px 0px 0px;
	{rdelim}

	ul li{ldelim}
		margin-top:5px;
		margin-left:5px;
	{rdelim}

	ul {ldelim}color:black;{rdelim}	 

</style>
<script type="text/javascript" src="include/js/general.js"></script>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
<table width="640" border="0" cellspacing="0" cellpadding="0" class="mailClient mailClientBg">
	<tr>
			<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="moduleName" width="80%" style="padding-left:10px;">{$CMOD.LBL_ASSIGN_ROLE}</td>
					<td  width=30% nowrap class="componentName" align=right>{$APP.VTIGER}</td>
				</tr>
			</table>
			</td>
  </tr>
    </tr>
   <tr>
	    <td style="padding:10px;" valign="top" class="hdrNameBg small">
			<table cellspacing="0" cellpadding="0" style="width:100%;" class="small">
          <tr>
            <td valign="top" align="center">
                <div id="role_popup" >
							{$ROLETREE}
				</div>
                <!-- END OF TREE MENU -->
            </td>
          </tr>
        </table></td>
  </tr>
  <tr>
    <td align="center" style="padding:10px;" class="reportCreateBottom" >&nbsp;</td>
  </tr>
</table>
<script>
function showhide(argg,imgId)
{ldelim}
	var harray=argg.split(",");
	var harrlen = harray.length;
	var i;
	for(i=0; i<harrlen; i++)
	{ldelim}
        	var x=document.getElementById(harray[i]).style;
        	if (x.display=="none")
        	{ldelim}
            		x.display="block";
					document.all[imgId].src = "themes/images/minus.gif";
        	{rdelim}
        	else
			{ldelim}
            			x.display="none";
						document.all[imgId].src = "themes/images/plus.gif";
            {rdelim}
	{rdelim}
{rdelim}

function loadValue(currObj,roleid)
{ldelim}
		window.opener.document.getElementById('role_name').value = convert_lt_gt(document.getElementById(currObj).innerHTML);
		window.opener.document.getElementById('user_role').value = roleid;
		window.close();
{rdelim}
function convert_lt_gt(str)
{ldelim}
	str = str.replace(/(&lt;)/g,'<');
	str = str.replace(/(&gt;)/g,'>');
	str = str.replace(/(&amp;)/g,'&');
	return str;
{rdelim}		
</script>
</body>
</html>
