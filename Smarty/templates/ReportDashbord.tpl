{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
-->*}
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
       <td><a name='1'></a><table width=20%  border=0 cellspacing=12 cellpadding=0 align=left>
             <tr>
               <td rowspan=2 valign=top><span class="dash_count">1</span></td>
               <td nowrap><span class=genHeaderSmall>{$REPORTNAME}</span></td>
             </tr>
             <tr>
               <td nowrap><span class=big>{$MOD.LBL_HORZ_BAR_CHART}</span> </td>
             </tr>
            </table>
       </td>
       <td align="right">
            <table cellpadding='0' cellspacing='0' border='0' class='small'>
		<tr>
			<td class='small'>{$MOD.VIEWCHART}:&nbsp;</td>
			<td class='dash_row_sel'>1</td>
			<td class='dash_row_unsel'><a class='dash_href' href='#2'>2</a></td>
			<td class='dash_switch'><a href='#top'><img align='absmiddle' src="{'dash_scroll_up.jpg'|@vtiger_imageurl:$THEME}" border='0'></a></td>
		</tr>
		</table>
        </td>
    <tr>
        <td colspan='2'>{$BARCHART}</td>
    </tr>
    <tr><td colspan='2' class='dash_chart_btm'>&nbsp;</td></tr>
    <tr>
        <td><a name='2'></a><table width=20%  border=0 cellspacing=12 cellpadding=0 align=left>
             <tr>
               <td rowspan=2 valign=top><span class="dash_count">2</span></td>
               <td nowrap><span class=genHeaderSmall>{$graph_title}</span></td>
             </tr>
             <tr>
               <td><span class=big>{$MOD.LBL_PIE_CHART}</span> </td>
             </tr>
            </table>
        </td>
        <td align="right">
            <table cellpadding='0' cellspacing='0' border='0' class='small'>
                <tr>
                        <td class='small'>{$MOD.VIEWCHART}:&nbsp;</td>
                        <td class='dash_row_unsel'><a class='dash_href' href='#1'>1</a></td>
                        <td class='dash_row_sel'>2</td>
                        <td class='dash_switch'><a href='#top'><img align='absmiddle' src="{'dash_scroll_up.jpg'|@vtiger_imageurl:$THEME}" border='0'></a></td>
                </tr>
                </table>
        </td>
    </tr>
    <tr>
        <td colspan='2'>{$PIECHART}</td>
    </tr>
    <tr><td colspan='2' class='dash_chart_btm'>&nbsp;</td></tr>
</table>
