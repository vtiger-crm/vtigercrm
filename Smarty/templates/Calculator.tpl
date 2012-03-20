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

<div id="calc" style="z-index:10000002" class="layerPopup" >
	<table  border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr style="cursor:move;" >
			<td class="mailClientBg small" id="calc_Handle"><b>{$APP.LBL_CALCULATOR}</b></td>
			<td align="right"class="mailClientBg small">
			<a href="javascript:;">
			<img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  onClick="fninvsh('calc')" hspace="5" align="absmiddle">
			</a>
			</td>
		</tr>
	</table>
	<table  border="0" cellpadding="0" cellspacing="0" width="100%" class="hdrNameBg">
	</tr>
	<tr><td style="padding:10px;" colspan="2">{$CALC}</td></tr>
	</table>
</div>

<script>

	var cal_Handle = document.getElementById("calc_Handle");
	var cal_Root   = document.getElementById("calc");
	Drag.init(cal_Handle, cal_Root);
</script>	
