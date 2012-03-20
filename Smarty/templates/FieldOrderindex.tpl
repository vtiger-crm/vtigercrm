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
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
        {include file='SettingsMenu.tpl'}
<td width="75%" valign="top">
<table width="99%" cellpadding="2" cellspacing="5" border="0">

<tr>
<td class="detailedViewHeader" align="left" colspan=2><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS} </a> > {$MOD.LBL_STUDIO} > {$MOD.LBL_FIELD_ORDERING} </b></td></tr>
<tr><td>
<a href="index.php?module=Settings&action=EditFieldBlock&tabid=7&fld_module=Leads&parenttab=Settings">{$MOD.LBL_LEAD_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Settings&action=EditFieldBlock&tabid=6&fld_module=Accounts&parenttab=Settings">{$MOD.LBL_ACCOUNT_FIELD_ACCESS}</a></td></tr><tr><td>
<a href="index.php?module=Settings&action=EditFieldBlock&tabid=4&fld_module=Contacts&parenttab=Settings">{$MOD.LBL_CONTACT_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Settings&action=EditFieldBlock&tabid=2&fld_module=Potentials&parenttab=Settings">{$MOD.LBL_OPPORTUNITY_FIELD_ACCESS}</a></td></tr><tr><td>
<a href="index.php?module=Settings&action=EditFieldBlock&tabid=13&fld_module=HelpDesk&parenttab=Settings">{$MOD.LBL_HELPDESK_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Settings&action=EditFieldBlock&tabid=14&fld_module=Products&parenttab=Settings">{$MOD.LBL_PRODUCT_FIELD_ACCESS}</a></td></tr><tr><td>
<a href="index.php?module=Settings&action=EditFieldBlock&tabid=8&fld_module=Documents&parenttab=Settings">{$MOD.LBL_NOTE_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Settings&action=EditFieldBlock&tabid=10&fld_module=Emails&parenttab=Settings">{$MOD.LBL_EMAIL_FIELD_ACCESS}</a></td></tr><tr><td>
<a href="index.php?module=Settings&action=EditFieldBlock&tabid=9&fld_module=Calendar&parenttab=Settings">{$MOD.LBL_TASK_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Settings&action=EditFieldBlock&tabid=16&fld_module=Events&parenttab=Settings">{$MOD.LBL_EVENT_FIELD_ACCESS}</a></td>
</tr>
</table>
</td>
</tr>
</table>
        {include file='SettingsSubMenu.tpl'}

