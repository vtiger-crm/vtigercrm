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


{assign var="Firstrow" value=$FIRSTROW}
<table border="0" class="small cellLabel" cellpadding="0" cellspacing="1" width="100%">
	{foreach name=iter item=row from=$Firstrow}
	{assign var="counter" value=$smarty.foreach.iter.iteration}
	<tr>
		<td class="small" align="center" height="30">
			{$SELECTFIELD[$counter]}
		</td>
	</tr>
	{/foreach}
</table>

