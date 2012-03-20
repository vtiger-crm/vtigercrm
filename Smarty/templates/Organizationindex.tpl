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
<td class="detailedViewHeader" align="left" colspan=2><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS} </a> > {$MOD.LBL_USER_MANAGEMENT} > {$MOD.LBL_DEFAULT_ORGANIZATION_FIELDS}</b></td></tr>
<tr>
<td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Leads">{$MOD.LBL_LEAD_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Accounts">{$MOD.LBL_ACCOUNT_FIELD_ACCESS}</a></td></tr><tr><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Contacts">{$MOD.LBL_CONTACT_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Potentials">{$MOD.LBL_OPPORTUNITY_FIELD_ACCESS}</a></td></tr><tr><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=HelpDesk">{$MOD.LBL_HELPDESK_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Products">{$MOD.LBL_PRODUCT_FIELD_ACCESS}</a></td></tr><tr><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Documents">{$MOD.LBL_NOTE_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Emails">{$MOD.LBL_EMAIL_FIELD_ACCESS}</a></td></tr><tr><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Calendar">{$MOD.LBL_TASK_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Events">{$MOD.LBL_EVENT_FIELD_ACCESS}</a></td></tr><tr><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Vendor">{$MOD.LBL_VENDOR_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=PriceBook">{$MOD.LBL_PB_FIELD_ACCESS}</a></td></tr><tr><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Quotes">{$MOD.LBL_QUOTE_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Orders">{$MOD.LBL_PO_FIELD_ACCESS}</a></td></tr><tr><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=SalesOrder">{$MOD.LBL_SO_FIELD_ACCESS}</a></td><td>
<a href="index.php?module=Users&action=DefaultFieldPermissions&fld_module=Invoice">{$MOD.LBL_INVOICE_FIELD_ACCESS}</a></td></tr>
</table>
</td>
</tr>
</table>
        {include file='SettingsSubMenu.tpl'}

