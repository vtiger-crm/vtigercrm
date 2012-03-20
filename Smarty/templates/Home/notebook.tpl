{*<!--/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/-->*}

<div style="width: 100%; overflow: auto; min-height: 240px; padding-left: 2%; padding-right: 3%;text-align: left;" id="notebook_{$NOTEBOOKID}" ondblclick="editContents(this, {$NOTEBOOKID})" title="{$MOD.LBL_NOTEBOOK_TITLE}">
	<span id="notebook_contents_{$NOTEBOOKID}" style="width: 100%; white-space: pre;"><pre>{$NOTEBOOK_CONTENTS}</pre></span>
</div>
<textarea id='notebook_textarea_{$NOTEBOOKID}' onfocus='this.className="detailedViewTextBoxOn"' rows="18" onblur='saveContents(this, {$NOTEBOOKID})' style='display:none;width: 100%; overflow: auto; min-height: 250px; padding-left: 2%; padding-right: 3%;' title="{$MOD.LBL_NOTEBOOK_SAVE_TITLE}"></textarea>
<span class="small" style="padding-left: 10px;display: block;" id="notebook_dbl_click_message">
	<font color="grey">
		{$MOD.LBL_NOTEBOOK_TITLE}
	</font>
</span>
<span class="small" style="padding-left: 10px;display: none;" id="notebook_save_message">
	<font color="grey">
		{$MOD.LBL_NOTEBOOK_SAVE_TITLE}
	</font>
</span>
