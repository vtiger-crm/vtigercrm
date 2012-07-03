/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/**
 * this function is used to get the picklist values using ajax
 * it does not accept any parameters but calculates the modulename and roleid from the document
 */
function changeModule(){
	$("status").style.display="inline";
	var oModulePick = $('pickmodule')
	var module=oModulePick.options[oModulePick.selectedIndex].value;
	var oRolePick = $('pickid');
	var role=oRolePick.options[oRolePick.selectedIndex].value;
	
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=PickListAjax&module=PickList&directmode=ajax&file=PickList&moduleName='+encodeURIComponent(module)+'&roleid='+role,
			onComplete: function(response) {
				$("status").style.display="none";
				$("picklist_datas").innerHTML=response.responseText;
			}
		}
		);
	fnhide('actiondiv');
}

/**
 * this function is used to assign picklist values to role
 * @param string module - the module name
 * @param string fieldname - the name of the field
 * @param string fieldlabel - the label for the field
 */
function assignPicklistValues(module,fieldname,fieldlabel){
	var elem = $('pickid');
	var role=elem.options[elem.selectedIndex].value;

	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=PickListAjax&module=PickList&file=AssignValues&moduleName='+encodeURIComponent(module)+'&fieldname='+encodeURIComponent(fieldname)+'&fieldlabel='+encodeURIComponent(fieldlabel)+'&roleid='+role,
			onComplete: function(response) {
				$("status").style.display="none";
				$("actiondiv").style.display="block";
				$("actiondiv").innerHTML=response.responseText;
				placeAtCenter($('actiondiv'));
			}
		}
		);
}

/**
 * this function is used to select the value from select box of picklist to the edit box
 */
function selectForEdit(){
	var node = document.getElementById('edit_availPickList');
	if(node.selectedIndex >=0){
		var value = node.options[node.selectedIndex].text;
		document.getElementById('replaceVal').value = value;
		$('replaceVal').focus();
	}
}

/**
 * this function checks if the edited value already exists; 
 * if not it pushes the edited value back to the picklist
 */
function pushEditedValue(e){
	var node = document.getElementById('edit_availPickList');
	if(typeof e.keyCode != 'undefined'){
		var keyCode = e.keyCode;
		//check if escape key is being pressed:: if yes then substitue the original value
		if(keyCode == 27){
			node.options[node.selectedIndex].text = node.options[node.selectedIndex].value;
			$('replaceVal').value = node.options[node.selectedIndex].value;
			return;
		}
	}
	
	var newVal = trim(document.getElementById('replaceVal').value);
	for(var i=0;i<node.length;i++){
		if(node[i].text.toLowerCase() == newVal.toLowerCase() && node.options[node.selectedIndex].text.toLowerCase() != newVal.toLowerCase()){
			alert(alert_arr.LBL_DUPLICATE_VALUE_EXISTS);
			return false;
		}
	}
	
	var nonEdit = document.getElementsByClassName('nonEditablePicklistValues');
	if(nonEdit){
		for(var i=0;i<nonEdit.length;i++){
			var val = trim(nonEdit[i].innerHTML);
			if(val.toLowerCase() == newVal.toLowerCase()){
				alert(alert_arr.LBL_DUPLICATE_VALUE_EXISTS);
				return false;
			}
		}
	}
	
	if(node.selectedIndex >=0){
		node.options[node.selectedIndex].text = newVal;
	}
}

/**
 * this function is used to show the delete div for a picklist
 */
function showDeleteDiv(){
	var moduleElem = $('pickmodule');
	var module = moduleElem.options[moduleElem.selectedIndex].value;
	
	var oModPick = $('allpick');
	var fieldName=oModPick.options[oModPick.selectedIndex].value;
	var fieldLabel=oModPick.options[oModPick.selectedIndex].text;
	
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=PickListAjax&module=PickList&mode=delete&file=ShowActionDivs&moduleName='+encodeURIComponent(module)+'&fieldname='+encodeURIComponent(fieldName)+'&fieldlabel='+encodeURIComponent(fieldLabel),
			onComplete: function(response) {
				$("status").style.display="none";
				$("actiondiv").style.display ='block';
				$("actiondiv").innerHTML=response.responseText;
				placeAtCenter($('actiondiv'));
			}
		}
		);
}

/**
 * this function is used to show the add div for a picklist
 */
function showAddDiv(){
	var moduleElem = $('pickmodule');
	var module = moduleElem.options[moduleElem.selectedIndex].value;
	
	var oModPick = $('allpick');
	var fieldName=oModPick.options[oModPick.selectedIndex].value;
	var fieldLabel=oModPick.options[oModPick.selectedIndex].text;
	
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=PickListAjax&module=PickList&mode=add&file=ShowActionDivs&moduleName='+encodeURIComponent(module)+'&fieldname='+encodeURIComponent(fieldName)+'&fieldlabel='+encodeURIComponent(fieldLabel),
			onComplete: function(response) {
				$("status").style.display="none";
				$("actiondiv").style.display ='block';
				$("actiondiv").innerHTML=response.responseText;
				placeAtCenter($('actiondiv'));
			}
		}
		);
}

/**
 * this function is used to show the edit div for a picklist
 */
function showEditDiv(){
	var moduleElem = $('pickmodule');
	var module = moduleElem.options[moduleElem.selectedIndex].value;
	
	var oModPick = $('allpick');
	var fieldName=oModPick.options[oModPick.selectedIndex].value;
	var fieldLabel=oModPick.options[oModPick.selectedIndex].text;
	
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=PickListAjax&module=PickList&mode=edit&file=ShowActionDivs&moduleName='+encodeURIComponent(module)+'&fieldname='+encodeURIComponent(fieldName)+'&fieldlabel='+encodeURIComponent(fieldLabel),
			onComplete: function(response) {
				$("status").style.display="none";
				$("actiondiv").style.display ='block';
				$("actiondiv").innerHTML=response.responseText;
				placeAtCenter($('actiondiv'));
			}
		}
		);
}

/**
 * this function validates the add action
 * @param string fieldname - the name of the picklist field
 * @param string module - the name of the module
 */
function validateAdd(fieldname, module){
	var pickArr=new Array();
	var pick_options = document.getElementsByClassName('picklist_existing_options');
	for(var i=0;i<pick_options.length;i++){
		if(pick_options[i].value != ''){
			pickArr[i]=(pick_options[i].innerHTML).replace(/&amp;/i,'&');
		}
	}

	var new_vals = new Array();
	new_vals=trim($("add_picklist_values").value).split('\n');		
	if(new_vals == '' || new_vals.length == 0) {
		alert(alert_arr.LBL_ADD_PICKLIST_VALUE);
		return false;
	}
	for(i=0;i<new_vals.length;i++){
		if (trim(new_vals[i]).search(/(\<|\>|\\|\/)/gi)!=-1) {
			alert(alert_arr.SPECIAL_CHARACTERS+'"<" ">" "\\" "/"'+alert_arr.NOT_ALLOWED);
			return false
		}
	}
	
	var node = document.getElementsByClassName('picklist_noneditable_options');
	var nonEdit = new Array();
	for(var i=0;i<node.length;i++){
		nonEdit[i] = trim(node[i].innerHTML);
	}
	
	pickArr = pickArr.concat(new_vals);
	pickArr = pickArr.concat(nonEdit);
	if(checkDuplicatePicklistValues(pickArr) == true){
		pickAdd(module,fieldname);
	}
}

/**
 * this function is used to check duplicate values in a given picklist values arrays
 * @param array arr - the picklist values array
 * @return boolean - true if no duplicates :: false otherwise
 */
function checkDuplicatePicklistValues(arr){
	var len=arr.length;
	for(i=0;i<len;i++){
		for(j=i+1;j<len;j++){
			if(trim(arr[i]).toLowerCase() == trim(arr[j]).toLowerCase()){
				alert(alert_arr.LBL_DUPLICATE_FOUND+"'"+trim(arr[i])+"'");
				return false;
			}
		}
	}
	return true;
}

/**
 * this function adds a new value to the given picklist
 * @param string module - the module name
 * @param string fieldname - the picklist field name
 */
function pickAdd(module, fieldname){
	var arr = new Array();
	arr = $("add_picklist_values").value.split("\n");
	var trimmedArr = new Array();
	for(var i=0,j=0;i<arr.length;i++){
		if(trim(arr[i]) != ''){
			trimmedArr[j++] = trim(arr[i]);
		}
	}
	var newValues = JSON.stringify(trimmedArr);
	arr = new Array();
	
	var roles = $("add_availRoles").options;
	var roleValues = '';
	if(roles.selectedIndex > -1){
		for (var i=0,j=0;i<roles.length;i++){
			if(roles[i].selected == true){
				arr[j++] = roles[i].value;
			}
		}
		roleValues = JSON.stringify(arr);
	}
	
	if(trim(roleValues) == '') {
		if(!confirm(alert_arr.LBL_NO_ROLES_SELECTED)){
			return false;
		}
	}
	
	var node = document.getElementById('saveAddButton');
	node.disabled = true;
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=PickListAjax&module=PickList&mode=add&file=PickListAction&fld_module='+encodeURIComponent(module)+'&fieldname='+encodeURIComponent(fieldname)+'&newValues='+encodeURIComponent(newValues)+'&selectedRoles='+encodeURIComponent(roleValues),
			onComplete: function(response) {
				var str = response.responseText;
				if(str=="SUCCESS"){
					changeModule();
					fnhide('actiondiv');
				}else{
					alert(str);
				}						
				$("status").style.display="none";
			}
		}
		);
}

/**
 * this function validates the edit action for a picklist
 * @param string fieldname - the fieldname of the picklist
 * @param string module - the module name
 */
function validateEdit(fieldname, module){
	var newVal = Array();
	var oldVal = Array();
	
	var node = document.getElementById('edit_availPickList');
	for(var i=0;i<node.length;i++){
		newVal[i] = node[i].text;
		if(trim(newVal[i]) == ''){
			alert(alert_arr.LBL_CANNOT_HAVE_EMPTY_VALUE);
			return false;
		}
		if (trim(newVal[i]).search(/(\<|\>|\\|\/)/gi)!=-1) {
			alert(alert_arr.SPECIAL_CHARACTERS+'"<" ">" "\\" "/"'+alert_arr.NOT_ALLOWED);
			return false
		}
		oldVal[i] = node[i].value;
	}
	pickReplace(module, fieldname, JSON.stringify(newVal), JSON.stringify(oldVal));
}

/**
 * this function is used to modify the picklist values
 * @param string module - the module name
 * @param string fieldname - the field name
 * @param array newVal - the new values for the picklist in json encoded string format
 * @param array oldVal - the old values for the picklist in json encoded string format
 */
function pickReplace(module, fieldname, newVal, oldVal){
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=PickListAjax&module=PickList&mode=edit&file=PickListAction&fld_module='+encodeURIComponent(module)+'&fieldname='+encodeURIComponent(fieldname)+'&newValues='+encodeURIComponent(newVal)+'&oldValues='+encodeURIComponent(oldVal),
			onComplete: function(response) {
				var str = response.responseText;
				if(str == "SUCCESS"){
					changeModule();
					fnhide('actiondiv');
				}else{
					alert(str);
				}						
				$("status").style.display="none";
			}
		}
	);
}

/**
 * this function validates the delete action
 * @param string fieldname - the name of the picklist field
 * @param string module - the name of the module
 */
function validateDelete(fieldname, module){
	var node = $('replace_picklistval');
	var replaceVal = node.options[node.selectedIndex].value;
	if(trim(replaceVal) == ''){
		alert(alert_arr.LBL_BLANK_REPLACEMENT);
		return false;
	}
	
	if(!confirm(alert_arr.LBL_WANT_TO_DELETE)){
		return false;
	}
	
	node = document.getElementById('delete_availPickList');
	var arr = new Array();
	for(var i=0;i<node.length;i++){
		if(node.selectedIndex == -1){
			alert(alert_arr.LBL_NO_VALUES_TO_DELETE);
			return false;
		}else{
			for(var j=0, k=0; j<node.length; j++){
				if(node.options[j].selected == true){
					arr[k++] = encodeURIComponent((node.options[j].value).replace(/(")/ig,"\\$1"));
				}
			}
		}
	}

	//check if replacement value is not equal to any deleted value
	for(var i=0; i<arr.length; i++){
		if(replaceVal == arr[i]){
			alert(alert_arr.LBL_PLEASE_CHANGE_REPLACEMENT);
			return false;
		}
	}
	
	var nonEditableLength = 0;
	var nonEditable = $('nonEditablePicklistVal');
	if(typeof nonEditable != 'undefined'){
		nonEditableLength = nonEditable.options.length;
	}
	
	if(arr.length == (node.length+nonEditableLength)){
		alert(alert_arr.LBL_DELETE_ALL_WARNING);
		return false;
	}
	pickDelete(module,fieldname, arr, replaceVal);
}

/**
 * this function deletes the given picklist values
 * @param string module - the module name
 * @param string fieldname - the field name of the picklist
 * @param array arr - the picklist values to delete
 * @param array replaceVal - the replacement value for the deleted value(s)
 */
function pickDelete(module, fieldname, arr, replaceVal){
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=PickListAjax&module=PickList&mode=delete&file=PickListAction&fld_module='+encodeURIComponent(module)+'&fieldname='+encodeURIComponent(fieldname)+'&values='+JSON.stringify(arr)+'&replaceVal='+encodeURIComponent(replaceVal),
			onComplete: function(response) {
				var str = response.responseText;
				if(str == "SUCCESS"){
					changeModule();
					fnhide('actiondiv');
				}else{
					alert(str);
				}						
				$("status").style.display="none";
			}
		}
	);
}

/**
 * this function is used to assign the available picklist values to the assigend picklist values section
 */
function moveRight(){
	var rightElem = $('selectedColumns');
	for (var i=0;i<rightElem.length;i++){
		rightElem.options[i].selected=false;
	}
	
	var leftElem = $('availList');
	
	for (var i=0;i<leftElem.length;i++){
		if(leftElem.options[i].selected==true){            	
			var rowFound=false;
			//check if the value already exists
			for(var j=0;j<rightElem.length;j++){
				if(rightElem.options[j].value==leftElem.options[i].value){
					rowFound=true;
					rightElem.options[j].selected=true;
					break;
				}
			}
			
			//if the value does not exist then create it and set it as selected
			if(rowFound!=true){
				var newColObj=document.createElement("OPTION");
				newColObj.value=leftElem.options[i].value;
				newColObj.innerHTML=leftElem.options[i].innerHTML;
				
				rightElem.appendChild(newColObj);
				leftElem.options[i].selected=false;
				newColObj.selected=true;
			}
		}
	}
}

/**
 * this function is used to remove values from the assigned picklist values section
 */
function removeValue(){
	var elem = $('selectedColumns');
	if(elem.options.selectedIndex>=0){
		for (var i=0;i<elem.options.length;i++){ 
			if(elem.options[i].selected == true){
				elem.removeChild(elem.options[i--]);
			}
		}
	}
}

/**
 * this function is used to move the selected option up in the assigned picklist
 */
function moveUp(){
	var elem = document.getElementById('selectedColumns');
	if(elem.options.selectedIndex>=0){
		for (var i=1;i<elem.options.length;i++){
			if(elem.options[i].selected == true){
				//swap with one up
				var first = elem.options[i-1];
				var second = elem.options[i];
				var temp = new Array();
				
				temp.value = first.value;
				temp.innerHTML = first.innerHTML;
				
				first.value = second.value;
				first.innerHTML = second.innerHTML;
				
				second.value = temp.value;
				second.innerHTML = temp.innerHTML;
				
				first.selected = true;
				second.selected = false;
			}
		}
	}
}

/**
 * this function is used to move the selected option down in the assigned picklist
 */
function moveDown(){
	var elem = document.getElementById('selectedColumns');
	if(elem.options.selectedIndex>=0){
		for (var i=elem.options.length-2;i>=0;i--){
			if(elem.options[i].selected == true){
				//swap with one down
				var first = elem.options[i+1];
				var second = elem.options[i];
				var temp = new Array();
				
				temp.value = first.value;
				temp.innerHTML = first.innerHTML;
				
				first.value = second.value;
				first.innerHTML = second.innerHTML;
				
				second.value = temp.value;
				second.innerHTML = temp.innerHTML;
				
				first.selected = true;
				second.selected = false;
			}
		}
	}
}

/**
 * this function is used to save the assigned picklist values for a given role
 * @param string moduleName - the name of the module
 * @param string fieldName - the name of the field
 * @param string roleid - the id of the given role
 */
function saveAssignedValues(moduleName, fieldName, roleid){
	var node = document.getElementById('selectedColumns');
	if(node.length == 0){
		alert(alert_arr.LBL_DELETE_ALL_WARNING);
		return false;
	}
	var arr = new Array();
	for(var i=0;i<node.length;i++){
		arr[i] = node[i].value;
	}
	
	node = document.getElementById('roleselect');
	var otherRoles = new Array();
	if(node != null){
		if(node.selectedIndex > -1){
			for(var i=0,j=0; i<node.options.length; i++){
				if(node.options[i].selected == true){
					otherRoles[j++] = node.options[i].value;
				}
			}
		}
	}
	otherRoles = JSON.stringify(otherRoles);
	
	var values = JSON.stringify(arr);
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=PickListAjax&module=PickList&file=SaveAssignedValues&moduleName='+encodeURIComponent(moduleName)+'&fieldname='+encodeURIComponent(fieldName)+'&roleid='+roleid+'&values='+encodeURIComponent(values)+'&otherRoles='+encodeURIComponent(otherRoles),
			onComplete: function(response) {
				if(response.responseText == "SUCCESS"){
					$("status").style.display="none";
					$("actiondiv").style.display="none";
					showPicklistEntries();
				}else{
					alert(response.responseText);
				}
			}
		}
	);
}

/**
 * this function is used to display the picklist entries for a given module for a given field for a given roleid
 * it accepts the module name as parameter while retrieves other values from DOM
 * @param string module - the module name 
 */
function showPicklistEntries(){
	var moduleNode = $('pickmodule');
	var moduleName = moduleNode.options[moduleNode.selectedIndex].value;
	
	var node = $('pickid');
	var roleid = node.options[node.selectedIndex].value;
	
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=PickListAjax&module=PickList&file=PickList&moduleName='+encodeURIComponent(moduleName)+'&roleid='+roleid+'&directmode=ajax',
			onComplete: function(response) {
				if(response.responseText){
					$("status").style.display="none";
					$('pickListContents').innerHTML = response.responseText;
				}
			}
		}
	);
}

/**
 * this function is used to display the select role div
 * @param string roleid - the roleid of the current role
 */
function showRoleSelectDiv(roleid){
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=PickListAjax&module=PickList&file=ShowRoleSelect&roleid='+roleid,
			onComplete: function(response) {
				if(response.responseText){
					$("status").style.display="none";
					var node = $('assignPicklistTable');
					var tr = document.createElement('tr');
					var td = document.createElement('td');
					td.innerHTML = response.responseText;
					tr.appendChild(td);
					$('addRolesLink').style.display = "none";
					
					var tbody = getChildByTagName(node,'tbody');
					var sibling = getChildByTagName(tbody, "tr");
					sibling = getSiblingByTagName(sibling, "tr");
					tbody.insertBefore(tr,sibling);
					placeAtCenter($('actiondiv'));
				}
			}
		}
	);
	
}

/**
 *
 */
function getSiblingByTagName(elem,tagName){
	var sibling = elem.nextSibling;
	while(sibling.nodeName.toLowerCase()!=tagName.toLowerCase()){
		sibling = sibling.nextSibling;
	}
	return sibling;
}

/**
 *
 */
function getChildByTagName(elem,tagName){
	for(var i=0;elem.childNodes.length;++i){
		if(elem.childNodes[i].nodeName.toLowerCase()==tagName.toLowerCase()){
			break;
		}
	}
	if(i >= elem.childNodes.length){
		return null;
	}
	return elem.childNodes[i];
}
