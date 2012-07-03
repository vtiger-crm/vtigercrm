/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
var typeofdata = new Array();
typeofdata['V'] = ['e','n','s','ew','c','k'];
typeofdata['N'] = ['e','n','l','g','m','h'];
typeofdata['T'] = ['e','n','l','g','m','h','bw','b','a'];
typeofdata['I'] = ['e','n','l','g','m','h'];
typeofdata['C'] = ['e','n'];
typeofdata['D'] = ['e','n','l','g','m','h','bw','b','a'];
typeofdata['DT'] = ['e','n','l','g','m','h','bw','b','a'];
typeofdata['NN'] = ['e','n','l','g','m','h'];
typeofdata['E'] = ['e','n','s','ew','c','k'];

var fLabels = new Array();
fLabels['e'] = alert_arr.EQUALS;
fLabels['n'] = alert_arr.NOT_EQUALS_TO;
fLabels['s'] = alert_arr.STARTS_WITH;
fLabels['ew'] = alert_arr.ENDS_WITH;
fLabels['c'] = alert_arr.CONTAINS;
fLabels['k'] = alert_arr.DOES_NOT_CONTAINS;
fLabels['l'] = alert_arr.LESS_THAN;
fLabels['g'] = alert_arr.GREATER_THAN;
fLabels['m'] = alert_arr.LESS_OR_EQUALS;
fLabels['h'] = alert_arr.GREATER_OR_EQUALS;
fLabels['bw'] = alert_arr.BETWEEN;
fLabels['b'] = alert_arr.BEFORE;
fLabels['a'] = alert_arr.AFTER;

var noneLabel;

var advft_column_index_count = -1;
var advft_group_index_count = 0;
var column_index_array = [];
var group_index_array = [];

function trimfValues(value) {
    var string_array;
    string_array = value.split(":");
    return string_array[4];
}

function updatefOptions(sel, opSelName) {
    var selObj = document.getElementById(opSelName);
    var fieldtype = null ;

    var currOption = selObj.options[selObj.selectedIndex];
    var currField = sel.options[sel.selectedIndex];    

    if(currField.value != null && currField.value.length != 0) {
		fieldtype = trimfValues(currField.value);
		ops = typeofdata[fieldtype];
		var off = 0;
		if(ops != null) {
	
			var nMaxVal = selObj.length;
			for(nLoop = 0; nLoop < nMaxVal; nLoop++) {
				selObj.remove(0);
			}
			selObj.options[0] = new Option ('None', '');
			if (currField.value == '') {
				selObj.options[0].selected = true;
			}
			off = 1;
			for (var i = 0; i < ops.length; i++) {
				var label = fLabels[ops[i]];
				if (label == null) continue;
				var option = new Option (fLabels[ops[i]], ops[i]);
				selObj.options[i + off] = option;
				if (currOption != null && currOption.value == option.value) {
					option.selected = true;
				}
			}
		}
    } else {
		if (currField.value == '') {
			selObj.options[0].selected = true;
		}
    }
}

function showHideDivs(showdiv, hidediv) {
	if(document.getElementById(showdiv)) 
		document.getElementById(showdiv).style.display = "block";
	
	if(document.getElementById(hidediv)) 
		document.getElementById(hidediv).style.display = "none";
}

function hideAllElementsByName(name) {
	var allElements = document.getElementsByTagName('div');
	for(var i=0; i<allElements.length; ++i) {
		var element = allElements[i];
		if (element.getAttribute('name') == name)
			element.style.display='none';
	}
	return true;
}

function removeElement(elementId) {
	var element = document.getElementById(elementId);
	if(element) {
		var parent = element.parentNode;
		if(parent) {
			parent.removeChild(element);
		} else {
			element.remove();
		}
	}
}

function deleteGroup(groupIndex) {
	removeElement('conditiongroup_'+groupIndex);
	
	var keyOfTheGroup = group_index_array.indexOf(groupIndex);
	var isLastElement = true;
	
	for(var i=keyOfTheGroup; i<group_index_array.length; ++i) {
		var nextGroupIndex = group_index_array[i];
		var nextGroupBlockId = "conditiongroup_"+nextGroupIndex;
		if(document.getElementById(nextGroupBlockId)) {
			isLastElement = false;
			break;
		}
	}
	
	
	if(isLastElement) {
		for(var i=keyOfTheGroup-1; i>=0; --i) {
			var prevGroupIndex = group_index_array[i];
			var prevGroupGlueId = "gpcon"+prevGroupIndex;
			if(document.getElementById(prevGroupGlueId)) {
				removeElement(prevGroupGlueId);
				break;
			}
		}
	}	
}

function addNewConditionGroup(parentNodeId) {
	addConditionGroup(parentNodeId);	
	addConditionRow(advft_group_index_count);
}

function deleteColumnRow(groupIndex, columnIndex) {
	removeElement('conditioncolumn_'+groupIndex+'_'+columnIndex);
	
	var groupColumns = column_index_array[groupIndex];
	var keyOfTheColumn = groupColumns.indexOf(columnIndex);
	var isLastElement = true;
	
	for(var i=keyOfTheColumn; i<groupColumns.length; ++i) {
		var nextColumnIndex = groupColumns[i];
		var nextColumnRowId = 'conditioncolumn_'+groupIndex+'_'+nextColumnIndex;
		if(document.getElementById(nextColumnRowId)) {
			isLastElement = false;
			break;
		}
	}
	
	if(isLastElement) {
		for(var i=keyOfTheColumn-1; i>=0; --i) {
			var prevColumnIndex = groupColumns[i];
			var prevColumnGlueId = "fcon"+prevColumnIndex;
			if(document.getElementById(prevColumnGlueId)) {
				removeElement(prevColumnGlueId);
				break;
			}
		}
	}
}

function addRequiredElements(columnindex) {

	var colObj = document.getElementById('fcol'+columnindex);
	var opObj = document.getElementById('fop'+columnindex);
    var valObj = document.getElementById('fval'+columnindex);   
    
	var currField = colObj.options[colObj.selectedIndex];
    var currOp = opObj.options[opObj.selectedIndex];
    
    var fieldtype = null ;
    if(currField.value != null && currField.value.length != 0) {
		var fieldInfo = currField.value.split(":");
		var tableName = fieldInfo[0];
		var fieldName = fieldInfo[2];
		fieldtype = fieldInfo[4];
		
		switch(fieldtype) {
			case 'D':
			case 'DT':
			case 'T':
				if(fieldtype=='T' && tableName.indexOf('vtiger_crmentity')<0){
					defaultRequiredElements(columnindex);
					break;
				}
				var dateformat = $('jscal_dateformat').value;
						var timeformat = "%H:%M:%S";
						var showtime = true;
						if(fieldtype == 'D' || (tableName == 'vtiger_activity' && fieldName == 'date_start')) {
							timeformat = '';
							showtime = false;
						}						
						
						if(!document.getElementById('jscal_trigger_fval'+columnindex)) { 
							var node = document.createElement('img');
							node.setAttribute('src',$('image_path').value+'btnL3Calendar.gif');
							node.setAttribute('id','jscal_trigger_fval'+columnindex);
							node.setAttribute('align','absmiddle');
							node.setAttribute('width','20');
							node.setAttribute('height','20');
							
	    					var parentObj = valObj.parentNode;						
	    					var nextObj = valObj.nextSibling;
							parentObj.insertBefore(node, nextObj);
						}
						
						Calendar.setup ({
							inputField : 'fval'+columnindex, ifFormat : dateformat+' '+timeformat, showsTime : showtime, button : "jscal_trigger_fval"+columnindex, singleClick : true, step : 1
                        });
                                                
                        if(currOp.value == 'bw') {
                        	if(!document.getElementById('fval_ext'+columnindex)) { 
	                        	var fillernode = document.createElement('br');
	                        	
	                        	var node1 = document.createElement('input');
	                        	node1.setAttribute('class', 'repBox small');
	                        	node1.setAttribute('type', 'text');
	                        	node1.setAttribute('id','fval_ext'+columnindex);
	                        	node1.setAttribute('name','fval_ext'+columnindex);
	                        	
	    						var parentObj = valObj.parentNode;
								parentObj.appendChild(fillernode);
								parentObj.appendChild(node1);
							}
							
							if(!document.getElementById('jscal_trigger_fval_ext'+columnindex)) {
								var node2 = document.createElement('img');
								node2.setAttribute('src',$('image_path').value+'btnL3Calendar.gif');
								node2.setAttribute('id','jscal_trigger_fval_ext'+columnindex);
								node2.setAttribute('align','absmiddle');
								node2.setAttribute('width','20');
								node2.setAttribute('height','20');
							
	    						var parentObj = valObj.parentNode;
							 	parentObj.appendChild(node2);
							 }
							
							if(!document.getElementById('clear_text_ext'+columnindex)) {
								var node3 = document.createElement('img');
								node3.setAttribute('src','themes/images/clear_field.gif');
								node3.setAttribute('id','clear_text_ext'+columnindex);
								node3.setAttribute('align','absmiddle');
								node3.setAttribute('width','20');
								node3.setAttribute('height','20');
								node3.style.cursor = "pointer";
								node3.onclick = function() {
													document.getElementById('fval_ext'+columnindex).value='';
													return false;
												}
								
								var parentObj = valObj.parentNode;
							 	parentObj.appendChild(node3);
							 }
							
							Calendar.setup ({
								inputField : 'fval_ext'+columnindex, ifFormat : dateformat+' '+timeformat, showsTime : showtime, button : "jscal_trigger_fval_ext"+columnindex, singleClick : true, step : 1
	                        });	
                       	} else {
							if(document.getElementById('fval_ext'+columnindex)) removeElement('fval_ext'+columnindex);
							if(document.getElementById('jscal_trigger_fval_ext'+columnindex)) removeElement('jscal_trigger_fval_ext'+columnindex);
							if(document.getElementById('clear_text_ext'+columnindex)) removeElement('clear_text_ext'+columnindex);
                       	}              
                        
                        break;
			default	:defaultRequiredElements(columnindex); 
		}
	}
}
function defaultRequiredElements(columnindex) {
    if(document.getElementById('jscal_trigger_fval'+columnindex)) removeElement('jscal_trigger_fval'+columnindex);
	if(document.getElementById('fval_ext'+columnindex)) removeElement('fval_ext'+columnindex);
	if(document.getElementById('jscal_trigger_fval_ext'+columnindex)) removeElement('jscal_trigger_fval_ext'+columnindex);
	if(document.getElementById('clear_text_ext'+columnindex)) removeElement('clear_text_ext'+columnindex);
}
function checkAdvancedFilter() {
	
	var escapedOptions = new Array('account_id','contactid','contact_id','product_id','parent_id','campaignid','potential_id','assigned_user_id1','quote_id','accountname','salesorder_id','vendor_id','time_start','time_end','lastname');
	
	var conditionColumns = vt_getElementsByName('tr', "conditionColumn");
	var criteriaConditions = [];
	for(var i=0;i < conditionColumns.length ; i++) {
		
		var columnRowId = conditionColumns[i].getAttribute("id");
		var columnRowInfo = columnRowId.split("_");
		var columnGroupId = columnRowInfo[1];
		var columnIndex = columnRowInfo[2];
		
		var columnId = "fcol"+columnIndex;
		var columnObject = getObj(columnId);
		var selectedColumn = trim(columnObject.value);
		var selectedColumnIndex = columnObject.selectedIndex;	
		var selectedColumnLabel = columnObject.options[selectedColumnIndex].text;
		
		var comparatorId = "fop"+columnIndex;
		var comparatorObject = getObj(comparatorId);
		var comparatorValue = trim(comparatorObject.value);
		
		var valueId = "fval"+columnIndex;
		var valueObject = getObj(valueId);
		var specifiedValue = trim(valueObject.value);
		
		var extValueId = "fval_ext"+columnIndex;
		var extValueObject = getObj(extValueId);
		if(extValueObject) {
			extendedValue = trim(extValueObject.value);
		}
		
		var glueConditionId = "fcon"+columnIndex;
		var glueConditionObject = getObj(glueConditionId);
		var glueCondition = '';
		if(glueConditionObject) {
			glueCondition = trim(glueConditionObject.value);
		}
		
		// If only the default row for the condition exists without user selecting any advanced criteria, then skip the validation and return.
		if(conditionColumns.length == 1 && selectedColumn == '' && comparatorValue == '' && specifiedValue == '')
			return true;

		if (!emptyCheck(columnId," Column ","text"))
			return false;
		if (!emptyCheck(comparatorId,selectedColumnLabel+" Option","text"))
			return false;

		var col = selectedColumn.split(":");
        if(escapedOptions.indexOf(col[3]) == -1) {
            if(col[4] == 'T' || col[4] == 'DT') {
                var datime = specifiedValue.split(" ");
                if (specifiedValue.charAt(0) != "$" && specifiedValue.charAt(specifiedValue.length-1) != "$"){
					if(datime.length > 1) {
						if(!re_dateValidate(datime[0],selectedColumnLabel+" (Current User Date Time Format)","OTH")) {
							return false
						}
						if(!re_patternValidate(datime[1],selectedColumnLabel+" (Time)","TIMESECONDS")) {
							return false
						}
					} else if(col[0] == 'vtiger_activity' && col[2] == 'date_start') {
						if(!dateValidate(valueId,selectedColumnLabel+" (Current User Date Format)","OTH"))
							return false
					} else {
						if(!re_patternValidate(datime[0],selectedColumnLabel+" (Time)","TIMESECONDS")) {
							return false
						}
					}
                }

                if(extValueObject) {
                    var datime = extendedValue.split(" ");
					if (extendedValue.charAt(0) != "$" && extendedValue.charAt(extendedValue.length-1) != "$"){
						if(datime.length > 1) {
							if(!re_dateValidate(datime[0],selectedColumnLabel+" (Current User Date Time Format)","OTH")) {
								return false
							}
							if(!re_patternValidate(datime[1],selectedColumnLabel+" (Time)","TIMESECONDS")) {
								return false
							}
						} else if(col[0] == 'vtiger_activity' && col[2] == 'date_start') {
							if(!dateValidate(extValueId,selectedColumnLabel+" (Current User Date Format)","OTH"))
								return false
						} else {
							if(!re_patternValidate(datime[0],selectedColumnLabel+" (Time)","TIMESECONDS")) {
								return false
							}
						}
					}
                }
            }
            else if(col[4] == 'D')
            {
                if (specifiedValue.charAt(0) != "$" && specifiedValue.charAt(specifiedValue.length-1) != "$"){
                    if(!dateValidate(valueId,selectedColumnLabel+" (Current User Date Format)","OTH"))
                        return false
                }
                if(extValueObject) {
                    if(!dateValidate(extValueId,selectedColumnLabel+" (Current User Date Format)","OTH"))
                        return false
                }
            }else if(col[4] == 'I')
            {
                if(!intValidate(valueId,selectedColumnLabel+" (Integer Criteria)"+i))
                    return false
            }else if(col[4] == 'N')
            {
                if (!numValidate(valueId,selectedColumnLabel+" (Number) ","any",true))
                    return false
            }else if(col[4] == 'E')
            {
                if (!patternValidate(valueId,selectedColumnLabel+" (Email Id)","EMAIL"))
                    return false
            }
        }
        
		//Added to handle yes or no for checkbox fields in reports advance filters. 
		if(col[4] == "C") {
			if(specifiedValue == "1")
				specifiedValue = getObj(valueId).value = 'yes';
			else if(specifiedValue =="0")
				specifiedValue = getObj(valueId).value = 'no';
		}
		if (extValueObject && extendedValue != null && extendedValue != '') specifiedValue = specifiedValue +','+ extendedValue;
		
		criteriaConditions[columnIndex] = {"groupid":columnGroupId, 
											"columnname":selectedColumn,
											"comparator":comparatorValue,
											"value":specifiedValue,
											"columncondition":glueCondition
										};
	}

	$('advft_criteria').value = JSON.stringify(criteriaConditions);
	
	var conditionGroups = vt_getElementsByName('div', "conditionGroup");
	var criteriaGroups = [];
	for(var i=0;i < conditionGroups.length ; i++) {
		var groupTableId = conditionGroups[i].getAttribute("id");
		var groupTableInfo = groupTableId.split("_");
		var groupIndex = groupTableInfo[1];
		
		var groupConditionId = "gpcon"+groupIndex;
		var groupConditionObject = getObj(groupConditionId);
		var groupCondition = '';
		if(groupConditionObject) {
			groupCondition = trim(groupConditionObject.value);
		}
		criteriaGroups[groupIndex] = {"groupcondition":groupCondition};
		
	}
	$('advft_criteria_groups').value = JSON.stringify(criteriaGroups);
	
	return true;
}

/**
 * IE has a bug where document.getElementsByName doesnt include result of dynamically created 
 * elements
 */
function vt_getElementsByName(tagName, elementName) {
	var inputs = document.getElementsByTagName( tagName );
	var selectedElements = [];
	for(var i=0;i<inputs.length;i++){
	  if(inputs.item(i).getAttribute( 'name' ) == elementName ){
		selectedElements.push( inputs.item(i) );
	  }
	}
	return selectedElements;
}