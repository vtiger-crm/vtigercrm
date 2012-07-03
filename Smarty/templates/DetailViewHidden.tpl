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
{*//Hidden fields for modules DetailView//  *}
<input type="hidden" name="parenttab" value="{$CATEGORY}">
<input type="hidden"  name="allselectedboxes" id="allselectedboxes">
{if $MODULE eq 'Accounts'}
	<input type="hidden" name="module" value="{$MODULE}">
	<input type="hidden" name="record" value="{$ID}">
	<input type="hidden" name="isDuplicate" value=false>
	<input type="hidden" name="action">
	<input type="hidden" name="return_module">
	<input type="hidden" name="return_action">
	<input type="hidden" name="return_id">
	<input type="hidden" name="contact_id">
	<input type="hidden" name="member_id">
	<input type="hidden" name="opportunity_id">
	<input type="hidden" name="case_id">
	<input type="hidden" name="task_id">
	<input type="hidden" name="meeting_id">
	<input type="hidden" name="call_id">
	<input type="hidden" name="email_id">
	<input type="hidden" name="source_module">
	<input type="hidden" name="entity_id">
	{$HIDDEN_PARENTS_LIST}

{elseif $MODULE eq 'Contacts'}
	<input type="hidden" name="module" value="{$MODULE}">
	<input type="hidden" name="record" value="{$ID}">
	<input type="hidden" name="isDuplicate" value=false>
	<input type="hidden" name="action">
	<input type="hidden" name="return_module">
	<input type="hidden" name="return_action">
	<input type="hidden" name="return_id">
	<input type="hidden" name="reports_to_id">
	<input type="hidden" name="opportunity_id">
	<input type="hidden" name="contact_id" value="{$ID}">
	<input type="hidden" name="parent_id" value="{$ID}">
	<input type="hidden" name="contact_role">
	<input type="hidden" name="task_id">
	<input type="hidden" name="meeting_id">
	<input type="hidden" name="call_id">
	<input type="hidden" name="case_id">
	<input type="hidden" name="new_reports_to_id">
	<input type="hidden" name="email_directing_module">
	{$HIDDEN_PARENTS_LIST}
{elseif $MODULE eq 'Potentials'}
	<input type="hidden" name="module" value="{$MODULE}">
        <input type="hidden" name="record" value="{$ID}">
        <input type="hidden" name="isDuplicate" value=false>
        <input type="hidden" name="action">
        <input type="hidden" name="return_module">
        <input type="hidden" name="return_action">
        <input type="hidden" name="return_id" >
        <input type="hidden" name="contact_id">
        <input type="hidden" name="contact_role">
        <input type="hidden" name="opportunity_id" value="{$ID}">
        <input type="hidden" name="task_id">
        <input type="hidden" name="meeting_id">
        <input type="hidden" name="call_id">
        <input type="hidden" name="email_id">
        <input type="hidden" name="source_module">
        <input type="hidden" name="entity_id">
        <input type="hidden" name="convertmode">
        <input type="hidden" name="account_id" value="{$ACCOUNTID}">
{elseif $MODULE eq 'Leads'}
	<input type="hidden" name="module" value="{$MODULE}">
	<input type="hidden" name="record" value="{$ID}">
	<input type="hidden" name="isDuplicate" value=false>
	<input type="hidden" name="action">
	<input type="hidden" name="return_module">
	<input type="hidden" name="return_action">
	<input type="hidden" name="return_id">
	<input type="hidden" name="lead_id" value="{$ID}">
	<input type="hidden" name="parent_id" value="{$ID}">
	<input type="hidden" name="email_directing_module">
	{$HIDDEN_PARENTS_LIST}
{elseif $MODULE eq 'Products' || $MODULE eq 'Vendors' || $MODULE eq 'PriceBooks' || $MODULE eq 'Services'}
	{if $MODULE eq 'Products'}
		<input type="hidden" name="product_id" value="{$id}">
	{elseif $MODULE eq 'Vendors'}
		<input type="hidden" name="vendor_id" value="{$id}">
	{/if}
	<input type="hidden" name="parent_id" value="{$id}">
	<input type="hidden" name="module" value="{$MODULE}">
        <input type="hidden" name="action">
        <input type="hidden" name="isDuplicate" value=false>
        <input type="hidden" name="mode">
        <input type="hidden" name="record" value="{$ID}">
        <input type="hidden" name="return_module" value="{$RETURN_MODULE}">
        <input type="hidden" name="return_id" value="{$RETURN_ID}">
        <input type="hidden" name="return_action" value="">
{elseif $MODULE eq 'Documents'}
	<input type="hidden" name="module" value="{$MODULE}">
        <input type="hidden" name="record" value="{$ID}">
        <input type="hidden" name="isDuplicate" value=false>
        <input type="hidden" name="action">
        <input type="hidden" name="return_module">
        <input type="hidden" name="return_action">
        <input type="hidden" name="return_id">
{elseif $MODULE eq 'Emails'}
        <input type="hidden" name="module" value="Emails">
        <input type="hidden" name="record" value="{$ID}">
        <input type="hidden" name="isDuplicate" value=false>
        <input type="hidden" name="action">
        <input type="hidden" name="contact_id" value="{$CONTACT_ID}">
        <input type="hidden" name="user_id" value="{$USER_ID}">
        <input type="hidden" name="return_module" value="{$RETURN_MODULE}">
        <input type="hidden" name="return_action" value="{$RETURN_ACTION}">
        <input type="hidden" name="return_id" value="{$RETURN_ID}">
        <input type="hidden" name="source_module">
        <input type="hidden" name="entity_id">
{elseif $MODULE eq 'HelpDesk'}
	<input type="hidden" name="module" value="{$MODULE}">
        <input type="hidden" name="action">
        <input type="hidden" name="mode">
        <input type="hidden" name="record" value="{$ID}">
        <input type="hidden" name="return_module" value="{$RETURN_MODULE}">
        <input type="hidden" name="return_id" value="{$RETURN_ID}">
        <input type="hidden" name="return_action" value="">
        <input type="hidden" name="isDuplicate">
{elseif $MODULE eq 'Faq'}
	<input type="hidden" name="module" value="{$MODULE}">
        <input type="hidden" name="record" value="{$ID}">
        <input type="hidden" name="isDuplicate" value=false>
        <input type="hidden" name="action">
        <input type="hidden" name="return_module">
        <input type="hidden" name="return_action">
        <input type="hidden" name="return_id">
        <input type="hidden" name="source_module">
        <input type="hidden" name="entity_id">
{elseif $MODULE eq 'Quotes'}
	<input type="hidden" name="module" value="{$MODULE}">
        <input type="hidden" name="record" value="{$ID}">
        <input type="hidden" name="isDuplicate" value=false>
        <input type="hidden" name="action">
        <input type="hidden" name="convertmode">
        <input type="hidden" name="return_module">
        <input type="hidden" name="return_action">
        <input type="hidden" name="return_id">
        <input type="hidden" name="member_id">
        <input type="hidden" name="opportunity_id">
        <input type="hidden" name="case_id">
        <input type="hidden" name="task_id">
        <input type="hidden" name="meeting_id">
        <input type="hidden" name="call_id">
        <input type="hidden" name="email_id">
        <input type="hidden" name="source_module">
        <input type="hidden" name="entity_id">
{elseif $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Invoice'}
	<input type="hidden" name="module" value="{$MODULE}">
        <input type="hidden" name="record" value="{$ID}">
        <input type="hidden" name="isDuplicate" value=false>
        <input type="hidden" name="action">
        <input type="hidden" name="return_module">
        <input type="hidden" name="return_action">
        <input type="hidden" name="return_id">
        <input type="hidden" name="member_id">
        <input type="hidden" name="opportunity_id">
        <input type="hidden" name="case_id">
        <input type="hidden" name="task_id">
        <input type="hidden" name="meeting_id">
        <input type="hidden" name="call_id">
        <input type="hidden" name="email_id">
        <input type="hidden" name="source_module">
        <input type="hidden" name="entity_id">
	{if $MODULE eq 'SalesOrder'}
        	<input type="hidden" name="convertmode">
	{/if}
{elseif $MODULE eq 'Calendar'}
	<input type="hidden" name="module" value="Calendar">
        <input type="hidden" name="record" value="{$ID}">
        <input type="hidden" name="activity_mode" value="{$ACTIVITY_MODE}">
        <input type="hidden" name="isDuplicate" value=false>
        <input type="hidden" name="action">
        <input type="hidden" name="return_module">
        <input type="hidden" name="return_action">
        <input type="hidden" name="return_id">
        <input type="hidden" name="user_id" value="{$USER_ID}">
{elseif $MODULE eq 'Campaigns'}
        <input type="hidden" name="module" value="{$MODULE}">
        <input type="hidden" name="record" value="{$ID}">
        <input type="hidden" name="isDuplicate" value=false>
        <input type="hidden" name="action">
        <input type="hidden" name="return_module">
        <input type="hidden" name="return_action">
        <input type="hidden" name="return_id">
{else}
	<input type="hidden" name="module" value="{$MODULE}">
        <input type="hidden" name="record" value="{$ID}">
        <input type="hidden" name="isDuplicate" value=false>
        <input type="hidden" name="action">
        <input type="hidden" name="return_module">
        <input type="hidden" name="return_action">
        <input type="hidden" name="return_id">
{/if}


