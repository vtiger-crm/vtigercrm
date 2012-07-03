{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/ *}
<script language="JavaScript" type="text/javascript" src="include/js/customview.js"></script>
<script language="javascript">
{literal}

function confirmAction(msg){
	return confirm(msg);
}

function deleteForm(formname,address){
	var status=confirmAction(alert_arr["SURE_TO_DELETE_CUSTOM_MAP"]);
	if(!status){
		return false;
	}
	submitForm(formname, address);
		return true;
}

function submitForm(formName,action){
		document.forms[formName].action=action;
		document.forms[formName].submit();
	}
var gselected_fieldtype = '';
function getCustomFieldList(customField)
{
	var modulename = customField.options[customField.options.selectedIndex].value;
	var modulelabel = customField.options[customField.options.selectedIndex].text;
	$('module_info').innerHTML = '{$MOD.LBL_CUSTOM_FILED_IN} "'+modulelabel+'" {$APP.LBL_MODULE}';
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=CustomFieldList&fld_module='+modulename+'&parenttab=Settings&ajax=true',
			onComplete: function(response) {
				$("cfList").innerHTML=response.responseText;
			}
		}
	);	
}

function deleteCustomField(id, fld_module, colName, uitype)
{
	  if(confirm(alert_arr.ARE_YOU_SURE))
        {
                document.form.action="index.php?module=Settings&action=DeleteCustomField&fld_module="+fld_module+"&fld_id="+id+"&colName="+colName+"&uitype="+uitype
                document.form.submit()
        }
}

function getCreateCustomFieldForm(customField,id,tabid,ui)
{
	var modulename = customField;
    //To handle Events and Todo's separately while adding Custom fields
    var activitytype = '';
    var activityobj = document.getElementsByName('activitytype');
    if (activityobj != null) {
    	for(var i=0; i<activityobj.length; i++) {
    		if (activityobj[i].checked == true)
    			activitytype = activityobj[i].value;
    	}
    }
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=CreateCustomField&fld_module='+customField+'&parenttab=Settings&ajax=true&fieldid='+id+'&tabid='+tabid+'&uitype='+ui+'&activity_type='+activitytype,
			onComplete: function(response) {
				$("createcf").innerHTML=response.responseText;
				gselected_fieldtype = '';
			}
		}
	);

}
function makeFieldSelected(oField,fieldid,blockid)
{
    if(gselected_fieldtype != '')
	{
		$(gselected_fieldtype).className = 'customMnu';
	}
	oField.className = 'customMnuSelected';	
	gselected_fieldtype = oField.id;	
	selFieldType(fieldid,'','',blockid)
	document.getElementById('selectedfieldtype_'+blockid).value = fieldid;
}
function CustomFieldMapping()
{
        document.form.action="index.php?module=Settings&action=LeadCustomFieldMapping";
        document.form.submit();
}
var gselected_fieldtype = '';
{/literal}
</script>
<div id="createcf" style="display:block;position:absolute;width:500px;"></div>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>

	<div align=center>
			{include file='SetMenu.tpl'}
			<!-- DISPLAY -->
			{if $MODE neq 'edit'}
			<b><font color=red>{$DUPLICATE_ERROR} </font></b>
			{/if}
			
				<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%" >
					<tbody>
						<tr align="left">
							<td rowspan="2" valign="top" width="50"><img src="{'custom.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}" border="0" height="48" width="48" onmouseover="tooltip.tip(this,'{'LBL_FIELD_SETTINGS'|@getTranslatedString:$MODULE}');" onmouseout="tooltip.untip(true);"></td>
							<td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&action=ModuleManager&parenttab=Settings">{$MOD.VTLIB_LBL_MODULE_MANAGER}</a> &gt; <a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$MODULE}&parenttab=Settings">{$MODULE}</a> &gt; {'LBL_FIELD_SETTINGS'|@getTranslatedString:$MODULE}</b></td>
						</tr>
					</tbody>
				</table>
				
				<br>
				<table border="0" cellpadding="10" cellspacing="0" width="100%">
				<tbody><tr>
				<td>
				{if $MODULE eq 'Leads'}
				<div id="cfList">
                                {include file="'Leads'|@vtlib_getModuleTemplate:'LeadsCustomEntries.tpl'}
                </div>	
                {else}
                <div id="cfList">
                                {include file="'Vtiger'|@vtlib_getModuleTemplate:'CustomFieldEntries.tpl'}
                </div>	
                {/if}
            <table border="0" cellpadding="5" cellspacing="0" width="100%">
			<tr>

		  	<td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
		<!-- End of Display -->
		</div>
		</td>
        </tr>
        <tr>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
        </tr>
</tbody>
</table>
<br>
