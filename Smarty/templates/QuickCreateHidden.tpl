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
{if $MODULE eq 'HelpDesk'}
	{literal}
	<form name="QcEditView" onSubmit="if(getFormValidate('qcform')) { VtigerJS_DialogBox.block(); return true;} else { return false; }" method="POST" action="index.php"  ENCTYPE="multipart/form-data">
	{/literal}
{else}
	{literal}
	<form name="QcEditView" onSubmit="if(getFormValidate('qcform')) { VtigerJS_DialogBox.block(); return true;} else { return false; }" method="POST" action="index.php">
	{/literal}
{/if}

{if $MODULE eq 'Calendar'}
	<input type="hidden" name="activity_mode" value="{$ACTIVITY_MODE}">
{elseif $MODULE eq 'Events'}
        <input type="hidden" name="activity_mode" value="{$ACTIVITY_MODE}">
{/if}
	<input type="hidden" name="record" value="">
	<input type="hidden" name="action" value="Save">
	<input type="hidden" name="module" value="{$MODULE}">
