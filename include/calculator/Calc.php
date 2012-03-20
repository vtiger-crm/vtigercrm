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

function get_calc($image_path) {
global $log;
$log->debug("Entering get_calc(".$image_path.") method ...");
$the_calc = <<<EOQ
<table border="0" cellspacing="0" cellpadding="0" style="margin-top:0;margin-left:0;" align="center">
  <tr>
    <td>
	<form name="calculator">
        <table border="0" cellpadding="2" cellspacing="2">
          <tr style="height:5">
            <td></td>
          </tr>
          <tr>
            <td colspan=6><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="10%"><input type="text" class="calcMem" name="mem" value="M" readonly></td>
                        <td width="90%"><input type="text" class="calcResult" name="answer" maxlength="30" onChange="CheckNumber(this.value)" readonly></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td colspan="3"><!-- div align="center"><img src=". vtiger_imageurl('delete.gif', $theme)." width="80" height="17"></div--></td>
            <td width="25"> <input type="button" name="CE" class="calcCancBtn" value="CE" onClick="CECalc(); return false;">
            </td>
            <td width="25"> <input type="reset" name="C" class="calcCancBtn" value="C" onClick="ClearCalc(); return false;">
            </td>
          </tr>
          <tr>
            <td width="25"> <input type="button" name="backspace" class="calcBackBtn" value="" onClick="Backspace(document.calculator.answer.value); return false;"></td>
            <td width="25"> <input type="button" name="recip" class="calcBlackBtn" value="1/x" onClick="RecipButton(); return false;"></td>
            <td width="25"> <input type="button" name="sqrt" class="calcBlackBtn" value="sqrt" onClick="SqrtButton(); return false;"></td>
            <td width="25"> <input type="button" name="negate" class="calcBlackBtn" value="+/-" onClick="NegateButton(); return false;"></td>
            <td width="25"> <input type="button" name="percent" class="calcBlackBtn" value="%" onClick="PercentButton(); return false;"></td>
          </tr>
          <tr>
            <td width="25"> <input type="button" name="MC" class="calcMemBtn" value="MC" onClick="MemoryClear(); return false;"></td>
            <td width="25"> <input type="button" name="calc7" class="calcGreyBtn" value="7" onClick="CheckNumber('7'); return false;"></td>
            <td width="25"> <input type="button" name="calc8" class="calcGreyBtn" value="8" onClick="CheckNumber('8'); return false;"></td>
            <td width="25"> <input type="button" name="calc9" class="calcGreyBtn" value="9" onClick="CheckNumber('9'); return false;"></td>
            <td width="25"> <input type="button" name="divide" class="calcBlackBtn" value="/" onClick="DivButton(1); return false;"></td>
          </tr>
          <tr>
            <td width="25"> <input type="button" name="MR" class="calcMemBtn" value="MR" onClick="MemoryRecall(Memory); return false;"></td>
            <td width="25"> <input type="button" name="calc4" class="calcGreyBtn" value="4" onClick="CheckNumber('4'); return false;"></td>
            <td width="25"> <input type="button" name="calc5" class="calcGreyBtn" value="5" onClick="CheckNumber('5'); return false;"></td>
            <td width="25"> <input type="button" name="calc6" class="calcGreyBtn" value="6" onClick="CheckNumber('6'); return false;"></td>
            <td width="25"> <input type="button" name="multiply" class="calcBlackBtn" value="x" onClick="MultButton(1); return false;"></td>
          </tr>
          <tr>
            <td width="25"> <input type="button" name="MS" class="calcMemBtn" value="M-" onClick="MemorySubtract(document.calculator.answer.value); return false;"></td>
            <td width="25"> <input type="button" name="calc1" class="calcGreyBtn" value="1" onClick="CheckNumber('1'); return false;"></td>
            <td width="25"> <input type="button" name="calc2" class="calcGreyBtn" value="2" onClick="CheckNumber('2'); return false;"></td>
            <td width="25"> <input type="button" name="calc3" class="calcGreyBtn" value="3" onClick="CheckNumber('3'); return false;"></td>
            <td width="25"> <input type="button" name="minus" class="calcBlackBtn" value="-" onClick="SubButton(1); return false;"></td>
          </tr>
          <tr>
            <td width="25"> <input type="button" name="Mplus" class="calcMemBtn" value="M+" onClick="MemoryAdd(document.calculator.answer.value); return false;"></td>
            <td width="25"> <input type="button" name="calc0" class="calcGreyBtn" value="0" onClick="CheckNumber('0'); return false;"></td>
            <td width="25"> <input type="button" name="dot" class="calcGreyBtn" value="." onClick="CheckNumber('.'); return false;"></td>
            <td width="25"> <input type="button" name="equal" class="calcBlackBtn" value="=" onClick="EqualButton(0); return false;"></td>
            <td width="25"> <input type="button" name="plus" class="calcBlackBtn" value="+" onClick="AddButton(1); return false;"></td>
          </tr>
        </table>
   </form>
     </td>
  </tr>
</table>
EOQ;
$log->debug("Exiting get_calc method ...");
return $the_calc;
}
?>
