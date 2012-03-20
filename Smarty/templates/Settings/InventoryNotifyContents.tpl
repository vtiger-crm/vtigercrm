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
<table width="100%" cellpadding="5" cellspacing="0" class="listTable" >
	<tr>
	<td class="colHeader small" width="5%">#</td>
	<td class="colHeader small" width="40%">{$CMOD.LBL_NOTIFICATION}</td>
	<td class="colHeader small" width="50%">{$CMOD.LBL_DESCRIPTION}</td>
	<td class="colHeader small" width="10%">{$CMOD.LBL_STATUS}</td>
	<td class="colHeader small" width="5%">{$CMOD.Tools}</td>
	</tr>
	{foreach name=notifyfor item=elements from=$NOTIFICATION}
	<tr>
	<td class="listTableRow small">{$smarty.foreach.notifyfor.iteration}</td>
	<td class="listTableRow small">{$elements.notificationname}</td>
	<td class="listTableRow small">{$elements.label}</td>
	{if $elements.status eq 'Active'}
	<td class="listTableRow small active">{$elements.status}</td>
	{else}
	<td class="listTableRow small inactive">{$elements.status}</td>
	{/if}
	<td class="listTableRow small" align="center" ><img onClick="fnvshobj(this,'editdiv');fetchEditNotify('{$elements.id}');" style="cursor:pointer;" src="{'editfield.gif'|@vtiger_imageurl:$THEME}" title="{$APP.LBL_EDIT}"></td>
	</tr>
	{/foreach}
	</table>

