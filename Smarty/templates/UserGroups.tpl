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
<table class="listTable" border="0" cellpadding="5" cellspacing="0" width="100%">
<tr>
<td class="colHeader small" valign="top">#</td>
<td class="colHeader small" valign="top">{$UMOD.LBL_GROUP_NAME}</td>
<td class="colHeader small" valign="top">{$UMOD.LBL_DESCRIPTION}</td>
</tr>
{foreach name=groupiter key=id item=groupname from=$GROUPLIST}
<tr>
<td class="listTableRow small" valign="top" width="5%">{$smarty.foreach.groupiter.iteration}</td>
{if $IS_ADMIN}
<td class="listTableRow small" valign="top"><a href="index.php?module=Settings&action=GroupDetailView&parenttab=Settings&groupId={$id}">{$groupname.1}</a></td>
{else}
<td class="listTableRow small" valign="top">{$groupname.1}</td>
{/if}
<td class="listTableRow small" valign="top" width="65%">{$groupname.2}</td>
</tr>
{/foreach}
</table>
