/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function editworkflowscript($, conditions){
	var vtinst = new VtigerWebservices("webservice.php");
	var fieldValidator;
	var desc = null;

	function id(v){
		return v;
	}

	function map(fn, list){
		var out = [];
		$.each(list, function(i, v){
			out[out.length]=fn(v);
		});
		return out;
	}

	function field(name){
		return function(object){
			if(typeof(object) != 'undefined') {
				return object[name];
			}
		};
	}

	function zip(){
		var out = [];

		var lengths = map(field('length'), arguments);
		var min = reduceR(function(a,b){return a<b?a:b;},lengths,lengths[0]);
		for(var i=0; i<min; i++){
			out[i]=map(field(i), arguments);
		}
		return out;
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
	
	function diff(reflist, list) {
		var out = [];
		$.each(list, function(i, v) {
			if(contains(reflist, v)) {
				out.push(v);
			}
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

	function concat(lista,listb){
		return lista.concat(listb);
	}

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

	function mergeObjects(obj1, obj2){
		var res = {};
		for(var k in obj1){
			res[k] = obj1[k];
		}
		for(var k in obj2){
			res[k] = obj2[k];
		}
		return res;
	}

	function jsonget(operation, params, callback){
		var obj = {
				module:'com_vtiger_workflow',
				action:'com_vtiger_workflowAjax',
				file:operation, ajax:'true'};
		$.each(params,function(key, value){
			obj[key] = value;
		});
		$.get('index.php', obj,
			function(result){
				var parsed = JSON.parse(result);
				callback(parsed);
		});
	}




	function center(el){
		el.css({position: 'absolute'});
		el.width("400px");
		el.height("125px");
		placeAtCenter(el.get(0));
	}


	function PageLoadingPopup(){
		function show(){
			$('#workflow_loading').css('display', 'block');
			//center($('#workflow_loading'));
		}
		function close(){
			$('#workflow_loading').css('display', 'none');
		}
		return {
			show:show, close:close
		};
	}
	var pageLoadingPopup = PageLoadingPopup();

	function NewTemplatePopup(){
		function close(){
			$('#new_template_popup').css('display', 'none');
		}

		function show(module){
			$('#new_template_popup').css('display', 'block');
			center($('#new_template_popup'));
		}
		
		
		
		$('#new_template_popup_save').click(function(){
			var messageBoxPopup = MessageBoxPopup();
		
			if(trim(this.form.title.value) == '') {
				messageBoxPopup.show();
				$('#'+ 'empty_fields_message').show();
				return false;
			}
		});

		$('#new_template_popup_close').click(close);
		$('#new_template_popup_cancel').click(close);
		return {
			close:close,show:show
		};
	}
	var newTemplatePopup = NewTemplatePopup();

	function NewTaskPopup(){
		function close(){
			$('#new_task_popup').css('display', 'none');
		}

		function show(module){
			$('#new_task_popup').css('display', 'block');
			center($('#new_task_popup'));
		}

		$('#new_task_popup_close').click(close);
		$('#new_task_popup_cancel').click(close);
		return {
			close:close,show:show
		};
	}

	var operations = function(){
		var op = {
			string:["is", "contains", "does not contain", "starts with", "ends with"],
			number:["equal to", "less than", "greater than", "does not equal",
							"less than or equal to", "greater than or equal to"],
			value:['is']
		};
		var mapping = [
			['string', ['string', 'text', 'url', 'email', 'phone']],
			['number', ['integer', 'double']],
			['value', ['reference', 'picklist', 'multipicklist', 'datetime',
								 'time', 'date', 'boolean']]
		];


	  	var out = {};
		$.each(mapping, function(i, v){
			var opName = v[0];
			var types = v[1];
			$.each(types, function(i, v){
				out[v] = op[opName];
			});
		});
		return out;
	}();

	function defaultValue(fieldType){

		function forPicklist(opType, condno){
			var value = $("#save_condition_"+condno+"_value");
			var options = implode('',
				map(function (e){return '<option value="'+e.value+'">'+e.label+'</option>';},
					opType['picklistValues'])
			);
			value.replaceWith('<select id="save_condition_'+condno+'_value" class="value">'+
												options+'</select>');
		}

		function forInteger(opType, condno){
			var value = $(format("#save_condition_%s_value", condno));
			value.replaceWith(format('<input type="text" id="save_condition_%s_value" '+
															 'value="0" class="value">', condno));
		}
		var functions = {
			string:function(opType, condno){
				var value = $(format("#save_condition_%s_value", condno));
				value.replaceWith(format('<input type="text" id="save_condition_%s_value" '+
																 'value="" class="value">', condno));
			},
			'boolean': function(opType, condno){
				var value = $("#save_condition_"+condno+"_value");
				value.replaceWith(
					'<select id="save_condition_'+condno+'_value" value="true" class="value"> \
						<option value="true:boolean">True</option>\
						<option value="false:boolean">False</option>\
					</select>');
			},
			integer: forInteger,
			picklist:forPicklist,
			multipicklist:forPicklist
		};
		var ret = functions[fieldType];
		if(ret==null){
			ret = functions['string'];
		}
		return ret;
	}

	var format = fn.format;


	function fillOptions(el,options){
		el.empty();
		$.each(options, function(k, v){
			el.append('<option value="'+k+'">'+v+'</option>');
		});
	}

	function resetFields(opType, condno){
		var ops = $("#save_condition_"+condno+"_operation");
		var selectedOperations = operations[opType.name];
		var l = dict(zip(selectedOperations, selectedOperations));
		fillOptions(ops, l);
		defaultValue(opType.name)(opType, condno);
	}


	function removeCondition(condno){
	  $(format("#save_condition_%s", condno)).remove();
	}

	//Convert user type into reference for consistency in describe objects
	//This is done inplace
	function referencify(desc){
	  var fields = desc['fields'];
	  for(var i=0; i<fields.length; i++){
		var field = fields[i];
		var type = field['type'];
		if(type['name']=='owner'){
		  type['name']='reference';
		  type['refersTo']=['Users'];
		}
	  }
	  return desc;
	}

	function getDescribeObjects(accessibleModules, moduleName, callback){
		vtinst.describeObject(moduleName, handleError(function(result){
			var parent = referencify(result);
			var fields = parent['fields'];
			var referenceFields = filter(function(e){return e['type']['name']=='reference';}, fields);
			var referenceFieldModules =
				map(function(e){ return e['type']['refersTo'];},
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
				  var moduleDescriptions = map(function(e){return e[1];}, parameters);
					var modules = dict(map(function(e){return [e['name'], referencify(e)];}, moduleDescriptions));
					callback(true, modules);
				}
			}
			var p = parallelExecuter(executer, relatedModules.length);
			$.each(relatedModules, function(i, v){
				p(function(callback){vtinst.describeObject(v, callback);});
			});
		}));
	}

	$(document).ready(function(){
		fieldValidator = new VTFieldValidator($('#edit_workflow_form'));
		fieldValidator.mandatoryFields = ["description"];
		pageLoadingPopup.show();
		vtinst.extendSession(handleError(function(result){
			vtinst.listTypes(handleError(function(accessibleModules) {
				getDescribeObjects(accessibleModules, moduleName, handleError(function(modules){
					var parent = modules[moduleName];
					function filteredFields(fields){
						return filter(
				  			function(e){return !contains(['autogenerated', 'reference', 'owner', 'multipicklist', 'password'], e.type.name);}, fields
				 		);
					};
					var parentFields = map(function(e){return[e['name'],e['label']];}, filteredFields(parent['fields']));
					var referenceFieldTypes = filter(function(e){return (e['type']['name']=='reference')}, parent['fields']);
					var moduleFieldTypes = {};
					$.each(modules, function(k, v){
						moduleFieldTypes[k] = dict(map(function(e){return [e['name'], e['type']];},
							filteredFields(v['fields'])));
					});
	
					function getFieldType(fullFieldName){
						var group = fullFieldName.match(/(\w+) : \((\w+)\) (\w+)/);
						if(group==null){
							var fieldModule = moduleName;
							var fieldName = fullFieldName;
						}else{
							var fieldModule = group[2];
							var fieldName = group[3];
						}
						return moduleFieldTypes[fieldModule][fieldName];
					}
	
					function fieldReferenceNames(referenceField){
						var name = referenceField['name'];
						var label = referenceField['label'];
						function forModule(moduleName){
							// If module is not accessible return no field information
							if(!contains(accessibleModules, moduleName)) return [];
				
							return map(function(field){
								return [name+' : '+'('+moduleName+') '+field['name'], label+' : '+'('+moduleName+') '+field['label']];},
								filteredFields(modules[moduleName]['fields'])
							);
						}
						return reduceR(concat, map(forModule,referenceField['type']['refersTo']),[]);
					}
	
	
					var referenceFields = reduceR(concat, map(fieldReferenceNames, referenceFieldTypes), []);
					var fieldLabels = dict(parentFields.concat(referenceFields));
	
					function addCondition(condno){
						$("#save_conditions").append(
							'<div id="save_condition_'+condno+'" style=\'margin-bottom: 5px\'> \
								<select id="save_condition_'+condno+'_fieldname" class="fieldname"></select> \
								<select id="save_condition_'+condno+'_operation" class="operation"></select> \
								<input type="text" id="save_condition_'+condno+'_value" class="value"> \
								<span id="save_condition_'+condno+'_remove" class="link remove-link"> \
								<img src="modules/com_vtiger_workflow/resources/remove.png"></span> \
							</div>'
						);
						var fe = $("#save_condition_"+condno+"_fieldname");
						var i = 1;
						fillOptions(fe, fieldLabels);
	
	
						var fullFieldName = fe.attr("value");
	
						resetFields(getFieldType(fullFieldName), condno);
	
						var re = $("#save_condition_"+condno+"_remove");
						re.bind("click", function(){
							removeCondition(condno);
						});
	
						fe.bind("change", function(){
							var select = $(this);
							var condNo = select.attr("id").match(/save_condition_(\d+)_fieldname/)[1];
							var fullFieldName = $(this).attr('value');
							resetFields(getFieldType(fullFieldName), condNo);
						});
					}
	
					var newTaskPopup = NewTaskPopup();
					$("#new_task").click(function(){
						newTaskPopup.show();
					});
	
					var newTemplatePopup = NewTemplatePopup();
					$("#new_template").click(function(){
						newTemplatePopup.show();
					});
	
					var condno=0;
					if(conditions){
						$.each(conditions, function(i, condition){
							var fieldname = condition["fieldname"];
							addCondition(condno);
							$(format("#save_condition_%s_fieldname", condno)).attr("value", fieldname);
							resetFields(getFieldType(fieldname), condno);
							$(format("#save_condition_%s_operation", condno)).attr("value", condition["operation"]);
							$('#dump').html(condition["value"]);
							var text = $('#dump').text();
							$(format("#save_condition_%s_value", condno)).attr("value", text);
							condno+=1;
						});
					}
	
					$("#save_conditions_add").bind("click", function(){
						addCondition(condno++);
					});
	
					$("#save_submit").bind("click", function(){
						var conditions = [];
						$("#save_conditions").children().each(function(i){
							var fieldname = $(this).children(".fieldname").attr("value");
							var operation = $(this).children(".operation").attr("value");
							var value = $(this).children(".value").attr("value");
							var condition = {fieldname:fieldname, operation:operation, value:value};
							conditions[i]=condition;
						});
						if(conditions.length==0){
						  var out = "";
						}else{
						  var out = JSON.stringify(conditions);
						}
						$("#save_conditions_json").attr("value", out);
					});
					pageLoadingPopup.close();
					$('#save_conditions_add').show();
				}));
			}));
		}));
	});

}