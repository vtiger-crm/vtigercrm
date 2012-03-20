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

{if $MODULE eq 'Emails'}	
	<form name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
        <input type="hidden" name="form">
        <input type="hidden" name="send_mail">
        <input type="hidden" name="contact_id" value="{$CONTACT_ID}">
        <input type="hidden" name="user_id" value="{$USER_ID}">
        <input type="hidden" name="filename" value="{$FILENAME}">
        <input type="hidden" name="old_id" value="{$OLD_ID}">

{elseif $MODULE eq 'Contacts'}
	{$ERROR_MESSAGE}
        <form name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="activity_mode" value="{$ACTIVITY_MODE}">
	<input type="hidden" name="opportunity_id" value="{$OPPORTUNITY_ID}">
	<input type="hidden" name="contact_role">
	<input type="hidden" name="case_id" value="{$CASE_ID}">
	<INPUT TYPE="HIDDEN" NAME="MAX_FILE_SIZE" VALUE="800000">
	<input type="hidden" name="campaignid" value="{$campaignid}">

{elseif $MODULE eq 'Potentials'}
	<form name="EditView" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="contact_id" value="{$CONTACT_ID}">

{elseif $MODULE eq 'Campaigns'}
        <form name="EditView" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">

{elseif $MODULE eq 'Calendar'}
	<input type="hidden" name="activity_mode" value="{$ACTIVITY_MODE}" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="product_id" value="{$PRODUCTID}">

{elseif $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Invoice' || $MODULE eq 'Quotes'}
	<!-- (id="frmEditView") content added to form tag and new hidden field added,  -->
	<form id="frmEditView" name="EditView" method="POST" action="index.php" onSubmit="settotalnoofrows();calcTotal();VtigerJS_DialogBox.block();">
	<input type="hidden" name="hidImagePath" id="hidImagePath" value="{$IMAGE_PATH}"/>
	<!-- End of code added -->

	{if $MODULE eq 'Invoice' || $MODULE eq 'PurchaseOrder' ||  $MODULE eq 'SalesOrder'}
       		 <input type="hidden" name="convertmode">
	{/if}

{elseif $MODULE eq 'HelpDesk'}
	<form name="EditView" method="POST" action="index.php" ENCTYPE="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="old_smownerid" value="{$OLDSMOWNERID}">
	<input type="hidden" name="old_id" value="{$OLD_ID}">

{elseif $MODULE eq 'Leads'}
        <form name="EditView" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
        <input type="hidden" name="campaignid" value="{$campaignid}">

{elseif $MODULE eq 'Accounts' || $MODULE eq 'Faq' || $MODULE eq 'PriceBooks' || $MODULE eq 'Vendors'}
	<form name="EditView" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">

{elseif $MODULE eq 'Documents'}
	<form name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="max_file_size" value="{$MAX_FILE_SIZE}">
	<input type="hidden" name="form">
	<input type="hidden" name="email_id" value="{$EMAILID}">
	<input type="hidden" name="ticket_id" value="{$TICKETID}">
	<input type="hidden" name="fileid" value="{$FILEID}">
	<input type="hidden" name="old_id" value="{$OLD_ID}">
	<input type="hidden" name="parentid" value="{$PARENTID}">

{elseif $MODULE eq 'Products'}
	{$ERROR_MESSAGE}
	<form name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="activity_mode" value="{$ACTIVITY_MODE}">
	<INPUT TYPE="HIDDEN" NAME="MAX_FILE_SIZE" VALUE="800000">
{else}
	{$ERROR_MESSAGE}
	<form name="EditView" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
{/if}

<input type="hidden" name="pagenumber" value="{$smarty.request.start|@vtlib_purify}">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{$MODE}">
<input type="hidden" name="action">
<input type="hidden" name="parenttab" value="{$CATEGORY}">
<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
<input type="hidden" name="return_id" value="{$RETURN_ID}">
<input type="hidden" name="return_action" value="{$RETURN_ACTION}">
<input type="hidden" name="return_viewname" value="{$RETURN_VIEWNAME}">
