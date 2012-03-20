<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once("modules/Accounts/Accounts.php");
require_once("getCompanyProfile.php");
$variable = vtlib_purify($_REQUEST['tickersymbol']);
$url = "http://moneycentral.msn.com/investor/research/profile.asp?Symbol=".trim($variable);
$data = getComdata($url,trim($variable));
if(is_array($data))
{
	$summary = array_shift($data);
        list($comp, $address, $phone, $fax, $site, $industry, $emp,$desc1,$desc2) = explode(":", $summary);
        $address = str_replace("Company Report","",$address);
        $address = str_replace("Phone","",$address);
        $phone = str_replace("Fax","",$phone);
        $fax = str_replace("http","",$fax);
        $site = str_replace("Industry","",$site);
        $site = str_replace("//","",$site);
        $industry = str_replace("Employees","",$industry);
        $emp = str_replace("Exchange","",$emp);
        $first_array =array_slice($data,0, 8);
        $second_array = array_slice($data,8);
	$output='';
	 $output .= '<table border="0" cellpadding="0" cellspacing="0" width="80%" align=center>
                     <tr><td class="detailedViewHeader" style="cursor:pointer;" onclick=\'just();\'><b>Company Report</b></td></tr>
                     <tr><td>
	                    <div id="company">
				<div id="innerLayer">
	                         <input type="hidden" name="address" value="'.trim($address).'">
	                         <input type="hidden" name="Phone" value="'.trim($phone).'">
	                         <input type="hidden" name="Fax" value="'.trim($fax).'">
	                         <input type="hidden" name="site" value="'.trim($site).'">
	                         <input type="hidden" name="emp" value="'.trim($emp).'">
	                         <table class="small" border="0" cellpadding="0" cellspacing="0" width="60%" align="center">
	        <tr>
	             <td colspan="2" class="detailedViewHeader"><b>'.$comp.'</b></td>
	        </tr>';
        $output .='<tr><td><table><tr><td><table width="185">';
        foreach($first_array as $arr => $val)
        {
                $output .= '<tr>';
                for($j=0;$j<count($val);$j+=2)
                {
                        $output .= '<td class="dvtCellLabel" align="right" width="20%">'.$val[$j].'</td>
                                    <td width="30%" align="left" class="dvtCellInfo">'.$val[$j+1].'</td>';
                }
                $output .= '</tr>';
        }
        $output .='</table></td>';
        $output .='<td><table width="185">';
        array_shift($second_array[0]);
        array_shift($second_array[0]);
        foreach($second_array as $arr => $val)
        {
                $output .= '<tr>';
	        for($j=0;$j<count($val);$j+=2)
	        {
	                $output .= '<td lass="dvtCellLabel" align="right" width="20%">'.$val[$j].'</td>
	                            <td width="30%" align="left" class="dvtCellInfo">'.$val[$j+1].'</td>';
	        }
	        $output .= '</tr>';
        }
        $output .='</table></td><td align=left><a href="http://finance.yahoo.com/q/bc?s='.trim($variable).'&amp;t=1d" target="_blank"><img src="http://ichart.finance.yahoo.com/t?s='.trim($variable).'" width="192" height="96" alt="[Chart]" border="0"></a></td></tr></table></td></tr>';
        $output .= '<tr style="height: 25px;">
       				      <td colspan="2" style="border-top:1px solid #eaeaea;">&nbsp;</td>
                                    </tr></table>';
        $output .= '<table class="small" border="0" cellpadding="0" cellspacing="0" width="60%" align="center" id="conf">
       		   <tr>
		    <td colspan="2" class="detailedViewHeader"><b>BUSINESS SUMMARY</b></td>
        	   </tr>		 
 		    <td width="30%" align="left" class="dvtCellInfo"><textarea id="summary" cols=80 rows=5 readonly>'.$desc1.' '.$desc2.'</textarea></td>
		    </tr>
		    <tr>
		     <td colspan="2" style="padding: 5px;border-top:1px solid #eaeaea;">
		        <div align="center">
		             <input title="Save [Alt+S]" accesskey="S" class="small"  name="button" value="Yes" style="width: 70px;" type="button" onclick=\'fnDown("conf");\' />
			     <input title="Cancel [Alt+X]" accesskey="X" class="small" name="button" value="No" style="width: 70px;" type="button" onclick=\'just();\'/>
		        </div>
		     </td> 
		    </tr>
		  </table>
                </div></div>
        </td></tr></table>';
echo $output;
}
else
{
        $output = '';
        $output .= "<div style='display:block'>";
        $output .= "<b><font color='red'>".$data."</font>";
        $output .= "</div>";
        echo $output;
}
?>