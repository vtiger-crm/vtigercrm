/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function VTCreateEntityTask($, fieldvaluemapping){
	var vtinst = new VtigerWebservices("webservice.php");
	var desc = null;

	var format = fn.format;

	function errorDialog(message){
		alert(message);
	}

	function handleError(fn){
		return function(status, result){
			if(status){
				fn(result);
			}else{
				errorDialog('Failure:'+result);
			}
		};
	}

	function implode(sep, arr){
		var out = "";
		$.each(arr, function(i, v){
			out+=v;
			if(i<arr.length-1){
				out+=sep;
			}
		});
		return out;
	}

	function fillOptions(el,options){
		el.empty();
		$.each(options, function(k, v){
			el.append('<option value="'+k+'">'+v+'</option>');
		});
	}

	function removeFieldValueMapping(mappingno){
	  $(format("#save_fieldvalues_%s", mappingno)).remove();
	}

	function map(fn, list){
		var out = [];
		$.each(list, function(i, v){
			out[out.length]=fn(v);
		});
		return out;
	}

	function reduceR(fn, list, start){
		var acc = start;
		$.each(list, function(i, v){
			acc = fn(acc, v);
		});
		return acc;
	}

	function dict(list){
		var out = {};
		$.each(list, function(i, v){
			out[v[0]] = v[1];
		});
		return out;
	}

	function filter(pred, list){
		var out = [];
		$.each(list, function(i, v){
			if(pred(v)){
				out[out.length]=v;
			}
		});
		return out;
	}

	function contains(list, value){
		var ans = false;
		$.each(list, function(i, v){
			if(v==value){
				ans = true;
				return false;
			}
		});
		return ans;
	}

	function diff(reflist, list) {
		var out = [];
		$.each(list, function(i, v) {
			if(contains(reflist, v)) {
				out.push(v);
			}
		});
		return out;
	}

	function concat(lista,listb){
		return lista.concat(listb);
	}

	//Get an array containing the the description of a module and all modules
	//refered to by it. This is passed to callback.
	function getDescribeObjects(accessibleModules, moduleName, callback){
		vtinst.describeObject(moduleName, handleError(function(result){
			var parent = result;
			var fields = parent['fields'];
			var referenceFields = filter(function(e){
				return e['type']['name']=='reference';},
			  fields);
			var referenceFieldModules =
				map(
					function(e){
						return e['type']['refersTo'];
					},
					referenceFields
				);
			function union(a, b){
			  var newfields = filter(function(e){return !contains(a, e);}, b);
			  return a.concat(newfields);
			}
			var relatedModules = reduceR(union, referenceFieldModules, [parent['name']]);

			// Remove modules that is no longer accessible
			relatedModules = diff(accessibleModules, relatedModules);

			function executer(parameters){
				var failures = filter(function(e){return e[0]==false;}, parameters);
				if(failures.length!=0){
					var firstFailure = failures[0];
					callback(false, firstFailure[1]);
				}else{
					var moduleDescriptions = map(function(e){
						return e[1];},
					  parameters);
					var modules = dict(map(function(e){
						  return [e['name'], e];},
						moduleDescriptions));
					callback(true, modules);
				}
			}
			var p = parallelExecuter(executer, relatedModules.length);
			$.each(relatedModules, function(i, v){
				p(function(callback){vtinst.describeObject(v, callback);});
			});
		}));
	}

    function editFieldExpression(fieldValueNode, fieldType) {
		editpopupobj.edit(fieldValueNode.attr('id'), fieldValueNode.attr('value'), fieldType);
    }

	function resetFields(opType, fieldName, mappingno) {
		defaultValue(opType.name)(opType, mappingno);

		var fv = $("#save_fieldvalues_"+mappingno+"_value");
		fv.attr("name", fieldName);
		var fieldLabel = jQuery("#save_fieldvalues_"+mappingno+"_fieldname option:selected").html();
		validator.validateFieldData[fieldName] = {type: opType.name, label: fieldLabel};
	}

	function defaultValue(fieldType){

		function forPicklist(opType, mappingno){
			var value = $("#save_fieldvalues_"+mappingno+"_value");
			var options = implode('',
				map(function (e){return '<option value="'+e.value+'">'+e.label+'</option>';},
					opType['picklistValues'])
			);
			value.replaceWith('<select id="save_fieldvalues_'+mappingno+'_value" class="expressionvalue">'+
												options+'</select>');
			$("#save_fieldvalues_"+mappingno+"_value_type").attr("value", "rawtext");

			$("#save_fieldvalues_"+mappingno+"_modulename").attr("value", $('#entity_type').attr("value"));
			$("#save_fieldvalues_"+mappingno+"_modulename").attr("disabled", "true");
		}
		var functions = {
			string:function(opType, mappingno){
				var value = $(format("#save_fieldvalues_%s_value", mappingno));
				value.replaceWith(format('<input type="text" id="save_fieldvalues_%s_value" '+
													'value="" class="expressionvalue" readonly />', mappingno));

				$("#save_fieldvalues_"+mappingno+"_modulename").attr("disabled", "");
				var fv = $(format("#save_fieldvalues_%s_value", mappingno));
				fv.bind("focus", function() {
					editpopupobj.setModule($(format("#save_fieldvalues_%s_modulename", mappingno)).attr('value'));
					editFieldExpression($(this), opType);
				});
				fv.bind("click", function() {
					editpopupobj.setModule($(format("#save_fieldvalues_%s_modulename", mappingno)).attr('value'));
					editFieldExpression($(this), opType);
				});
				fv.bind("keypress", function() {
					editpopupobj.setModule($(format("#save_fieldvalues_%s_modulename", mappingno)).attr('value'));
					editFieldExpression($(this), opType);
				});
			},
			picklist:forPicklist,
			multipicklist:forPicklist
		};
		var ret = functions[fieldType];
		if(ret==null){
			ret = functions['string'];
		}
		return ret;
	}

	var validateDuplicateFields = {
		init: function(){
		},
		validator: function(){
			jQuery('#duplicate_fields_selected_message').css('display', 'none');
			var result;
			var successResult = [true];
			var failureResult = [false, 'duplicate_fields_selected_message', []];
			var selectedFieldNames = $(".fieldname");
			result = successResult;
			$.each(selectedFieldNames, function(i, ele) {
				var fieldName = $(ele).attr("value");
				var fields = $("[name="+fieldName+"]");
				if(fields.length > 1) {
					result = failureResult;
				}
			});
			return result;
		}
	};

	function loadModuleFields(entityName, fieldvaluemapping) {
		$("#save_fieldvaluemapping").html('');
		$("#save_fieldvaluemapping_add_wrapper").html('');
		validator.mandatoryFields = ['summary', 'entity_type'];
		validator.validateFieldData = {};
		if(entityName == '') return;
		$('#save_fieldvaluemapping_add-busyicon').show();

		var entityLabel = $('#entity_type option:selected').html();

		vtinst.describeObject(entityName, handleError(function(result){
			var fields = result['fields'];

			function filteredFields(fields){
				var filteredfields = filter(
					function(e){return !contains(['autogenerated', 'reference', 'password'], e.type.name);}, fields
				);
				// Added to filter non-editable fields for update
				return filter(
					function(e){return contains(['1'], e.editable);}, filteredfields
				);
			};

			function filterMandatoryFields(fields){
				var filteredfields = filter(
					function(e){return !contains(['autogenerated', 'reference', 'password'], e.type.name);}, fields
				);
				// Added to filter non-editable fields as well as non-mandatory fields
				return filter(
					function(e){return contains(['1'], e.editable) && contains(['1'], e.mandatory);}, filteredfields
				);
			};

			var entityFields = map(function(e){
										fieldname = e['name'];
										fieldlabel = e['label'];
										if(contains(['1'], e.mandatory)) fieldlabel = fieldlabel + ' *';
										return [fieldname,fieldlabel];
									}, filteredFields(fields));
			var entityFieldTypes = dict(map(function(e){return[e['name'],e['type']];}, filteredFields(fields)));
			var mandatoryFields = map(function(e){return e['name'];}, filterMandatoryFields(fields));
			var fieldLabels = dict(entityFields);

			function addFieldValueMapping(mappingno){
				$("#save_fieldvaluemapping").append(
					'<div id="save_fieldvalues_'+mappingno+'" style=\'margin-bottom: 5px\'> \
						<select id="save_fieldvalues_'+mappingno+'_fieldname" class="fieldname"></select> \
						<select id="save_fieldvalues_'+mappingno+'_modulename" class="modulename"></select> \
						<input type="hidden" id="save_fieldvalues_'+mappingno+'_value_type" class="type"> \
						<input type="text" id="save_fieldvalues_'+mappingno+'_value" class="expressionvalue" readonly > \
						<span id="save_fieldvalues_'+mappingno+'_remove" class="link remove-link"> \
						<img src="modules/com_vtiger_workflow/resources/remove.png"></span> \
					</div>'
				);
				var fe = $("#save_fieldvalues_"+mappingno+"_fieldname");
				var i = 1;
				fillOptions(fe, fieldLabels);

				var me = $("#save_fieldvalues_"+mappingno+"_modulename");
				me.html('');
				me.append(format('<option value="%s">%s</option>', moduleName, moduleLabel));
				me.append(format('<option value="%s">%s</option>', entityName, entityLabel));

				var fullFieldName = fe.attr("value");
				resetFields(entityFieldTypes[fullFieldName], fullFieldName, mappingno);
				var re = $("#save_fieldvalues_"+mappingno+"_remove");
				re.bind("click", function(){
					removeFieldValueMapping(mappingno);
				});
				fe.bind("change", function(){
					var select = $(this);
					var mappingno = select.attr("id").match(/save_fieldvalues_(\d+)_fieldname/)[1];
					var fullFieldName = $(this).attr('value');
					resetFields(entityFieldTypes[fullFieldName], fullFieldName, mappingno);
				});
			}

			var mappingno=0;
			if(fieldvaluemapping){
				$.each(fieldvaluemapping, function(i, fieldvaluemap){
					var fieldname = fieldvaluemap["fieldname"];
					addFieldValueMapping(mappingno);
					$(format("#save_fieldvalues_%s_fieldname", mappingno)).attr("value", fieldname);
					$(format("#save_fieldvalues_%s_modulename", mappingno)).attr("value", fieldvaluemap['modulename']);
					resetFields(entityFieldTypes[fieldname], fieldname, mappingno);
					$(format("#save_fieldvalues_%s_value_type", mappingno)).attr("value", fieldvaluemap['valuetype']);
					$('#dump').html(fieldvaluemap["value"]);
					var text = $('#dump').text();
					$(format("#save_fieldvalues_%s_value", mappingno)).attr("value", text);
					mappingno+=1;
				});
			}

			$.each(mandatoryFields, function(i, fieldname) {
				validator.mandatoryFields.push(fieldname);
				var mandatoryFieldElement = $('[name='+fieldname+']');
				if(mandatoryFieldElement.length == 0) {
					addFieldValueMapping(mappingno);
					$(format("#save_fieldvalues_%s_fieldname", mappingno)).attr("value", fieldname);
					resetFields(entityFieldTypes[fieldname], fieldname, mappingno);
					mappingno+=1;
				}
			});

			$("#save_fieldvaluemapping_add_wrapper").html(createEntityHeaderTemplate);
			$("#save_fieldvaluemapping_add").bind("click", function() {
				addFieldValueMapping(mappingno);
				mappingno+=1;
			});

			$('#save_fieldvaluemapping_add-busyicon').hide();

		}));
	}


	$(document).ready(function(){
        Drag.init(document.getElementById('editpopup_draghandle'), document.getElementById('editpopup'));
        editpopupobj = fieldExpressionPopup(moduleName, $);
        editpopupobj.setModule(moduleName);
		editpopupobj.close();
                
		validator.addValidator('validateDuplicateFields', validateDuplicateFields);
		validator.mandatoryFields = ['summary', 'entity_type'];

		vtinst.extendSession(handleError(function(result){

			$.get('index.php', {
					module:'com_vtiger_workflow',
					action:'com_vtiger_workflowAjax',
					file:'WorkflowComponents', ajax:'true',
					modulename:moduleName, mode:'getdependentfields'},
				function(result){
					result = JSON.parse(result);
					var entitytypes = $('#entity_type');
					var count = result['count'];
					var entities = result['entities'];

					if(count > 0) {
						$.each(entities, function(entityname, fielddetails){
							entitytypes.append(format('<option value="%s">%s</option>', entityname, fielddetails['modulelabel']));
						});
						$('#entity_type').bind('change', function(){
							if(this.value != '') {
								$('#reference_field').attr('value', entities[this.value]['fieldname']);
							}
							loadModuleFields(this.value, null);
						});

						if(selectedEntityType != "") {
							$('#entity_type').attr('value', selectedEntityType);
						}
						loadModuleFields($('#entity_type').attr('value'), fieldvaluemapping);
					}

					$('#entity_type-busyicon').hide();
					$('#entity_type').show();
				}
			);

			$("#save").bind("click", function(){
				var validateFieldValues = new Array();
				var fieldvaluemapping = [];
				$("#save_fieldvaluemapping").children().each(function(i){
					var fieldname = $(this).children(".fieldname").attr("value");
					var modulename = $(this).children(".modulename").attr("value");
					var type = $(this).children(".type").attr("value");
					var value = $(this).children(".expressionvalue").attr("value");
					var fieldvaluemap = {fieldname:fieldname, modulename:modulename, valuetype:type, value:value};
					fieldvaluemapping[i]=fieldvaluemap;

					if(type == "" || type == "rawtext") {
						validateFieldValues.push(fieldname);
					}
				});
				if(fieldvaluemapping.length==0){
				  var out = "";
				}else{
				  var out = JSON.stringify(fieldvaluemapping);
				}
				$("#save_fieldvaluemapping_json").attr("value", out);

				for(var fieldName in validator.validateFieldData) {
					if(validateFieldValues.indexOf(fieldName) < 0) {
						delete validator.validateFieldData[fieldName];
					}
				}
			});
		}));
	});
}

VTCreateEntityTask(jQuery, fieldvaluemapping);