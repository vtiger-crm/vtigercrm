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
<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
<script language="JavaScript">
{literal}
function check(){
	var blocklabel = document.getElementById('blocklabel');
	var val = trim(blocklabel.value);
	if(val == "")
	{
		alert(alert_arr.BLOCK_NAME_CANNOT_BE_BLANK);
		return false;
	}
	return true;
}
{/literal}</script>
<script language="javascript">

function getCustomFieldList(customField)
{ldelim}
	var modulename = customField.options[customField.options.selectedIndex].value;
	$('module_info').innerHTML = '{$MOD.LBL_CUSTOM_FILED_IN} "'+modulename+'" {$APP.LBL_MODULE}';
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&fld_module='+modulename+'&parenttab=Settings&ajax=true',
			onComplete: function(response) {ldelim}
				$("cfList").update(response.responseText);
			{rdelim}
		{rdelim}
	);	
{rdelim}

function changeFieldorder(what_to_do,fieldid,blockid,modulename)
{ldelim}
	$('vtbusy_info').style.display = "block";
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module='+modulename+'&parenttab=Settings&what_to_do='+what_to_do+'&fieldid='+fieldid+'&blockid='+blockid+'&ajax=true',
			onComplete: function(response) {ldelim}
				$("cfList").update(response.responseText);
				$('vtbusy_info').style.display = "none";
			{rdelim}
		{rdelim}
	);	
{rdelim}


function changeShowstatus(tabid,blockid,modulename)	
{ldelim}
	var display_status = $('display_status_'+blockid).value;
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module='+modulename+'&parenttab=Settings&what_to_do='+display_status+'&tabid='+tabid+'&blockid='+blockid+'&ajax=true',
			onComplete: function(response) {ldelim}
				$("cfList").update(response.responseText);
			{rdelim}
		{rdelim}
		
	);	
{rdelim}




function changeBlockorder(what_to_do,tabid,blockid,modulename)	
{ldelim}
	$('vtbusy_info').style.display = "block";
		new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module='+modulename+'&parenttab=Settings&what_to_do='+what_to_do+'&tabid='+tabid+'&blockid='+blockid+'&ajax=true',
			onComplete: function(response) {ldelim}
				$("cfList").update(response.responseText);
				$('vtbusy_info').style.display = "none";
			{rdelim}
		{rdelim}
		
	);	
{rdelim}

<!-- end of tanmoy on 6/09/2007-->


{literal}
function deleteCustomField(id, fld_module, colName, uitype)
{
       if(confirm(alert_arr.ARE_YOU_SURE_YOU_WANT_TO_DELETE)){
        $('vtbusy_info').style.display = "block";
			new Ajax.Request(
				'index.php',
				{queue: {position: 'end', scope: 'command'},
					method: 'post',
					postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=deleteCustomField&ajax=true&fld_module='+fld_module+'&fld_id='+id+'&colName='+colName+'&uitype='+uitype,
					onComplete: function(response) {
						$("cfList").update(response.responseText);
						gselected_fieldtype = '';
						$('vtbusy_info').style.display = "none";
					}
				}
			);		
		}else{
		fninvsh('editfield_'+id);
		}
}

function deleteCustomBlock(module,blockid,no){

	if(no > 0){
		alert(alert_arr.PLEASE_MOVE_THE_FIELDS_TO_ANOTHER_BLOCK);
		return false;
	}else{
		if(confirm(alert_arr.ARE_YOU_SURE_YOU_WANT_TO_DELETE_BLOCK)){
			$('vtbusy_info').style.display = "block";
			new Ajax.Request(
				'index.php',
				{queue : {position : 'end', scope: 'command'},
				method : 'post',
				postBody: 'module=Settings&action=SettingsAjax&fld_module='+module+'&file=LayoutBlockList&sub_mode=deleteCustomBlock&ajax=true&blockid='+blockid,
				onComplete: function(response) {
					$("cfList").update(response.responseText);
					$('vtbusy_info').style.display = "none";
				}
				}	
			);	
		}
	}
}


function getCreateCustomBlockForm(modulename,mode)
{
	var checlabel = check();
	if(checlabel == false)
		return false;
	var blocklabel = document.getElementById('blocklabel');
	var val = trim(blocklabel.value);
	var blockid = document.getElementById('after_blockid').value;
	$('vtbusy_info').style.display = "block";
			new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=addBlock&fld_module='+modulename+'&parenttab=Settings&ajax=true&mode='+mode+'&blocklabel='+
			encodeURIComponent(val)+'&after_blockid='+blockid,
			onComplete: function(response) {
				$('vtbusy_info').style.display = "none";
				var str = response.responseText;
				if(str == 'ERROR'){
					alert(alert_arr.LABEL_ALREADY_EXISTS);
					return false;
				}else if(str == 'LENGTH_ERROR'){
					alert(alert_arr.LENGTH_OUT_OF_RANGE);
					return false;
				}else{
					$("cfList").update(str);
				}		
				gselected_fieldtype = '';
			}
		}
	);


}

function saveFieldInfo(fieldid,module,sub_mode,typeofdata){
	urlstring = '';
	var mandatory_check = $('mandatory_check_'+fieldid);
	var presence_check = $('presence_check_'+fieldid);
	var quickcreate_check = $('quickcreate_check_'+fieldid);
	var massedit_check = $('massedit_check_'+fieldid);
	var defaultvalue_check = $('defaultvalue_check_'+fieldid);
	
	if(mandatory_check != null){
		urlstring = urlstring+'&ismandatory=' + mandatory_check.checked;
	}
	if(presence_check != null){	
		urlstring = urlstring + '&isPresent=' + presence_check.checked;
	}	
	if(quickcreate_check != null){
		urlstring = urlstring + '&quickcreate=' + quickcreate_check.checked;
	}	
	if(massedit_check != null){
		urlstring = urlstring + '&massedit=' + massedit_check.checked;
	}
	if(defaultvalue_check != null) {
		var defaultvalueelement = document.getElementById('defaultvalue_'+fieldid);
		if(defaultvalueelement != null) {
			var defaultvalue = defaultvalueelement.value;
			if(defaultvalue_check.checked == true) {
				var typeinfo = typeofdata.split('~');
				var inputtype = typeinfo[0];
				if(inputtype == 'C') {
					defaultvalue = (defaultvalueelement.checked == true)?'1':'0';
				}
				if(validateInputData(defaultvalue, alert_arr['LBL_DEFAULT_VALUE_FOR_THIS_FIELD'], typeofdata) == false) {
					document.getElementById('defaultvalue_'+fieldid).focus();
					return false;
				}
			} else {
				defaultvalue = '';
			}
		} else {
			defaultvalue = '';
		}
		
		urlstring = urlstring + '&defaultvalue=' + encodeURIComponent(defaultvalue);
	}
	
	$('vtbusy_info').style.display = "block";
	new Ajax.Request(
			'index.php',
			{queue : {position: 'end',scope:'command'},
				method:'post',
				postBody:'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode='+sub_mode+'&parenttab=Settings'+
					'&fieldid='+fieldid+'&fld_module='+module+'&ajax=true'+urlstring,
				onComplete: function(response) {
					$("cfList").update(response.responseText);
					$('vtbusy_info').style.display = "none";
					fnvshNrm('editfield_+"fieldid"');
				}
			}
	);
}


function enableDisableCheckBox(obj, elementName) {
	
	var ele = $(elementName);
	if (obj == null || ele == null) return;
	if (obj.checked == true) {
		ele.checked = true;
		ele.disabled = true;
	} else {
		ele.disabled = false;
	}	
}

function showHideTextBox(obj, elementName) {
	var ele = $(elementName);
	if (obj == null || ele == null) return;
	if (obj.checked == true) {
		ele.disabled = false;
	} else {
		ele.disabled = true;
	}
}


function getCreateCustomFieldForm(modulename,blockid,mode)
{
   var check = validate(blockid);
   if(check == false)
   return false;
   var type = document.getElementById("fieldType_"+blockid).value;
   var label = document.getElementById("fldLabel_"+blockid).value;
   var fldLength = document.getElementById("fldLength_"+blockid).value;  
   var fldDecimal = document.getElementById("fldDecimal_"+blockid).value;
   var fldPickList = encodeURIComponent(document.getElementById("fldPickList_"+blockid).value);
   VtigerJS_DialogBox.block();
   new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=addCustomField&fld_module='+modulename+'&ajax=true&blockid='+blockid+'&fieldType='+type+'&fldLabel='+label+'&fldLength='+fldLength+'&fldDecimal='+fldDecimal+'&fldPickList='+fldPickList,
			onComplete: function(response) {
				VtigerJS_DialogBox.unblock();
				var str = response.responseText;
				if(str == 'ERROR'){
					alert(alert_arr.LABEL_ALREADY_EXISTS);
					return false;
				}else{
					$("cfList").update(str);
				}	
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
	selFieldType(fieldid,'','',blockid);
	document.getElementById('selectedfieldtype_'+blockid).value = fieldid;
}

function show_move_hiddenfields(modulename,tabid,blockid,sub_mode){
	
	if(sub_mode == 'showhiddenfields'){
	var selectedfields = document.getElementById('hiddenfield_assignid_'+blockid);
	var selectedids_str = '';
	for(var i=0; i<selectedfields.length; i++) {
		if (selectedfields[i].selected == true) {
			selectedids_str = selectedids_str + selectedfields[i].value + ":";
		}
	}
	}else{
		var selectedfields = document.getElementById('movefield_assignid_'+blockid);
		var selectedids_str = '';
		for(var i=0; i<selectedfields.length; i++) {
			if (selectedfields[i].selected == true) {
				selectedids_str = selectedids_str + selectedfields[i].value + ":";
			}
		}
	}
	$('vtbusy_info').style.display = "block";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode='+sub_mode+'&fld_module='+modulename+'&parenttab=Settings&ajax=true&tabid='+tabid+'&blockid='+blockid+'&selected='+selectedids_str,
			onComplete: function(response) {
				$("cfList").update(response.responseText);
				$('vtbusy_info').style.display = "none";
				}
			}
		);
}
	
function changeRelatedListorder(what_to_do,tabid,sequence,id,module)	
{
	$('vtbusy_info').style.display = "block";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=changeRelatedInfoOrder&sequence='+sequence+'&fld_module='+module+'&parenttab=Settings&what_to_do='+what_to_do+'&tabid='+tabid+'&id='+id+'&ajax=true',
			onComplete: function(response) {
			$("relatedlistdiv").innerHTML=response.responseText;
			$('vtbusy_info').style.display = "none";
			}
		}
		
	);	
}	

function callRelatedList(module){
	$('vtbusy_info').style.display = "block";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=LayoutBlockList&sub_mode=getRelatedInfoOrder&parenttab=Settings&formodule='+module+'&ajax=true',
			onComplete: function(response) {
			$("relatedlistdiv").innerHTML=response.responseText;
			fnvshNrm('relatedlistdiv');
			$('vtbusy_info').style.display = "none";
			}
		}
		
	);
}

function showProperties(field,man,pres,quickc,massed){
	var str='<table class="small" cellpadding="2" cellspacing="0" border="0"><tr><th>'+field+'</th></tr>';
	if (man == 0 || man == 2)
 		str = str+'<tr><td>'+alert_arr.FIELD_IS_MANDATORY+'</td></tr>';
	if (pres == 0 || pres == 2)
 		str = str+'<tr><td>'+alert_arr.FIELD_IS_ACTIVE+'</td></tr>';
	if (quickc == 0 || quickc == 2)
		str = str+'<tr><td>'+alert_arr.FIELD_IN_QCREATE+'</td></tr>';
	if(massed == 0 || massed == 1)
		str = str+'<tr><td>'+alert_arr.FIELD_IS_MASSEDITABLE+'</td></tr>';
	str = str + '</table>';
	return str;
}

var gselected_fieldtype = '';
{/literal}
</script>
<div id = "layoutblock">
<div id="relatedlistdiv" style="display:none; position: absolute; width: 225px; left: 300px; top: 300px;"></div>
<br>


{assign var=entries value=$CFENTRIES}
			{if $CFENTRIES.0.tabpresence eq '0' }
			
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	        <br>	
			<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td rowspan="2" valign="top" width="50"><img src="{'orgshar.gif'|@vtiger_imageurl:$THEME}" alt="Users" title="Users" border="0" height="48" width="48"></td>
					<td class="heading2" valign="bottom">
						<b><a href="index.php?module=Settings&action=ModuleManager&parenttab=Settings">{$MOD.VTLIB_LBL_MODULE_MANAGER}</a> 
						&gt;<a href="index.php?module=Settings&action=ModuleManager&module_settings=true&formodule={$MODULE}&parenttab=Settings">{if $APP.$MODULE } {$APP.$MODULE} {elseif $MOD.$MODULE} {$MOD.$MODULE} {else} {$MODULE} {/if}</a> &gt; 
						{$MOD.LBL_LAYOUT_EDITOR}</b>
					</td>
				</tr>
				<tr>
					<td class="small" valign="top">{$MOD.LBL_LAYOUT_EDITOR_DESCRIPTION}
					</td>
					<td align="right" width="15%"><input type="button" class="crmButton create small" onclick="callRelatedList('{$CFENTRIES.0.module}');fnvshNrm('relatedlistdiv');posLay(this,'relatedlistdiv');" alt="{$MOD.ARRANGE_RELATEDLIST}" title="{$MOD.ARRANGE_RELATEDLIST}" value="{$MOD.ARRANGE_RELATEDLIST}"/>
					</td>
					<td align="right" width="8%"><input type="button" class="crmButton create small" onclick="fnvshobj(this,'addblock');" alt="{$MOD.ADD_BLOCK}" title="{$MOD.ADD_BLOCK}" value="{$MOD.ADD_BLOCK}"/>
					</td>
					&nbsp; <img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" id="vtbusy_info" style="display:none;position:absolute;top:180px;right:100px;" border="0" />
				</tr>
			</table>
				
			<div id="cfList">
                {include file="Settings/LayoutBlockEntries.tpl"}
            </div>	
                
			<table border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
		<!-- End of Display for field -->
{else}

	<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>	
	<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>
	<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src="{'denied.gif'|@vtiger_imageurl:$THEME}" ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>{$APP.LBL_PERMISSION}</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>			   	
		<a href='javascript:window.history.back();'>{$MOD.LBL_GO_BACK}</a><br>								   						     </td>
		</tr>
		</tbody></table> 
		</div>
		</td></tr></table>
{/if}
</div>
