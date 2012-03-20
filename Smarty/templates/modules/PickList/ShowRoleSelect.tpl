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
<b>{$MOD.LBL_SELECT_ROLES}</b><br>
<select multiple id="roleselect" name="roleselect" class="small crmFormList" style="overflow:auto; height: 80px;width:200px;border:1px solid #666666;font-family:Arial, Helvetica, sans-serif;font-size:11px;">
	{foreach item=rolename key=roleid from=$ROLES}
		<option value="{$roleid}">{$rolename}</option>
	{/foreach}
</select>
