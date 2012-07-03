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
	var editpopupobj;

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
		var min = reduceR(function(a,b){
			return a<b?a:b;
		},lengths,lengths[0]);
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

	function center(el){
		el.css({
			position: 'absolute'
		});
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
			show:show,
			close:close
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
			close:close,
			show:show
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
			close:close,
			show:show
		};
	}

	var operations = function(){
		var op = {
			string:["is", "contains", "does not contain", "starts with",
						"ends with", "has changed"],
			number:["equal to", "less than", "greater than", "does not equal",
						"less than or equal to", "greater than or equal to", "has changed"],
			value:['is', 'is not', "has changed"]
		};
		var mapping = [
			['string', ['string', 'text', 'url', 'email', 'phone']],
			['number', ['integer', 'double', 'currency']],
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

	var format = fn.format;


	function fillOptions(el,options){
		el.empty();
		$.each(options, function(k, v){
			el.append('<option value="'+k+'">'+v+'</option>');
		});
	}

	function resetFields(opType, condno){
		var ops = $("#save_condition_"+condno+"_operation");
		var selOperations = operations[opType.name];
		var selectedOperations = new Array();

		// Remove 'has changed' operation for reference fields
		var fe = $("#save_condition_"+condno+"_fieldname");
		var fullFieldName = fe.attr('value');
		var group = fullFieldName.match(/(\w+) : \((\w+)\) (\w+)/);
		if(group != null){
			for(var i=0; i<selOperations.length; ++i) {
				if(selOperations[i] != 'has changed') {
					selectedOperations.push(selOperations[i]);
				}
			}
		} else {
			selectedOperations = selOperations;
		}

		var l = dict(zip(selectedOperations, selectedOperations));
		fillOptions(ops, l);
		defaultValue(opType.name)(opType, condno);
	}

	function defaultValue(fieldType){

		function forPicklist(opType, condno){
			var value = $("#save_condition_"+condno+"_value");
			var options = implode('',
				map(function (e){
					return '<option value="'+e.value+'">'+e.label+'</option>';
				},
				opType['picklistValues'])
				);
			value.replaceWith('<select id="save_condition_'+condno+'_value" class="expressionvalue">'+
				options+'</select>');
			$("#save_condition_"+condno+"_value_type").attr("value", "rawtext");
		}
		var functions = {
			string:function(opType, condno){
				var value = $(format("#save_condition_%s_value", condno));
				value.replaceWith(format('<input type="text" id="save_condition_%s_value" '+
					'value="" class="expressionvalue" readonly />', condno));

				var fv = $("#save_condition_"+condno+"_value");
				fv.bind("focus", function() {
					editFieldExpression($(this), opType);
				});
				fv.bind("click", function() {
					editFieldExpression($(this), opType);
				});
				fv.bind("keypress", function() {
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

	function removeCondition(groupno, condno){
		$(format("#save_condition_%s", condno)).remove();
		resetJoinCondition(groupno, condno);
	}

	function removeConditionGroup(groupno){
		$(format("#condition_group_%s", groupno)).remove();
		$(format("#condition_group_%s_joincondition", groupno)).remove();
		resetGroupJoinCondition(groupno);
	}

	function resetJoinCondition(groupno, condno) {

		var lastCondition = $("#save_condition_group_"+groupno+" div:last");
		lastCondition.children(".joincondition").css("visibility", "hidden");

		var previousLastCondition = lastCondition.prev();
		if(previousLastCondition.length > 0) {
			previousLastCondition.children(".joincondition").css("visibility", "visible");
		}

		var groupConditions = $(format("#save_condition_group_%s", groupno)).children();
		if(groupConditions.length <= 0) {
			removeConditionGroup(groupno);
		}
		resetGroupJoinCondition(groupno);
	}

	function resetGroupJoinCondition(groupno) {
		var firstChildNode = $("#save_conditions :first");
		if(firstChildNode.length > 0 && firstChildNode.attr('class').indexOf('condition_group_join_block') >= 0) {
			firstChildNode.remove();
		}
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
			var referenceFields = filter(function(e){
				return e['type']['name']=='reference';
			}, fields);
			var referenceFieldModules =
			map(function(e){
				return e['type']['refersTo'];
			},
			referenceFields
			);
			function union(a, b){
				var newfields = filter(function(e){
					return !contains(a, e);
				}, b);
				return a.concat(newfields);
			}
			var relatedModules = reduceR(union, referenceFieldModules, [parent['name']]);

			// Remove modules that is no longer accessible
			relatedModules = diff(accessibleModules, relatedModules);

			function executer(parameters){
				var failures = filter(function(e){
					return e[0]==false;
				}, parameters);
				if(failures.length!=0){
					var firstFailure = failures[0];
					callback(false, firstFailure[1]);
				}else{
					var moduleDescriptions = map(function(e){
						return e[1];
					}, parameters);
					var modules = dict(map(function(e){
						return [e['name'], referencify(e)];
					}, moduleDescriptions));
					callback(true, modules);
				}
			}
			var p = parallelExecuter(executer, relatedModules.length);
			$.each(relatedModules, function(i, v){
				p(function(callback){
					vtinst.describeObject(v, callback);
				});
			});
		}));
	}

	function editFieldExpression(fieldValueNode, fieldType) {
		editpopupobj.edit(fieldValueNode.attr('id'), fieldValueNode.attr('value'), fieldType);
	}

	$(document).ready(function(){
		fieldValidator = new VTFieldValidator($('#edit_workflow_form'));
		fieldValidator.mandatoryFields = ["description"];
		pageLoadingPopup.show();

		Drag.init(document.getElementById('editpopup_draghandle'), document.getElementById('editpopup'));
		editpopupobj = fieldExpressionPopup(moduleName, $)
		editpopupobj.setModule(moduleName);
		editpopupobj.close();

		vtinst.extendSession(handleError(function(result){
			vtinst.listTypes(handleError(function(accessibleModules) {
				getDescribeObjects(accessibleModules, moduleName, handleError(function(modules){
					var parent = modules[moduleName];
					function filteredFields(fields){
						return filter(
							function(e){
								return !contains(['autogenerated', 'reference', 'owner', 'multipicklist', 'password'], e.type.name);
							}, fields
							);
					};
					var parentFields = map(function(e){
						return[e['name'],e['label']];
					}, filteredFields(parent['fields']));
					var referenceFieldTypes = filter(function(e){
						return (e['type']['name']=='reference')
						}, parent['fields']);
					var moduleFieldTypes = {};
					$.each(modules, function(k, v){
						moduleFieldTypes[k] = dict(map(function(e){
							return [e['name'], e['type']];
						},
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
								return [name+' : '+'('+moduleName+') '+field['name'], label+' : '+'('+modules[moduleName]['label']+') '+field['label']];},
								filteredFields(modules[moduleName]['fields'])
							);
						}
						return reduceR(concat, map(forModule,referenceField['type']['refersTo']),[]);
					}

					var referenceFields = reduceR(concat, map(fieldReferenceNames, referenceFieldTypes), []);
					var fieldLabels = dict(parentFields.concat(referenceFields));

					function addCondition(groupid, condid){
						if($("#save_condition_group_"+groupid).length <= 0) {
							var group_condition_html = '';
							if($(".condition_group_block").length > 0) {
								group_condition_html = '<div class="condition_group_join_block" id="condition_group_'+groupid+'_joincondition" > \
                                                        <select id="save_condition_group_'+groupid+'_joincondition" class="joincondition"> \
                                                            <option value="and">and</option> \
                                                            <option value="or">or</option> \
                                                        </select> \
                                                    </div>';
							}
							$("#save_conditions").append(
								group_condition_html
								+ '<div id="condition_group_'+groupid+'" class="condition_group_block" > \
                                    <div style="float:right;"> \
                                        <span id="save_condition_group_'+groupid+'_remove" class="link remove-link"> \
                                        <img src="themes/images/close.gif"></span> \
                                    </div> \
                                    <div style="clear:both;"></div> \
                                    <div id="save_condition_group_'+groupid+'" class="save_condition_group"> \
                                    </div> \
                                    <div> \
                                        <input type="button" id="add_group_condition_'+groupid+'" value="New Condition" class="small edit" /> \
                                    </div> \
                                </div>'
								);
							var gpcond = $("#add_group_condition_"+groupid);
							gpcond.bind("click", function(){
								addCondition(groupid, condno++);
							});

							var rem_group_img = $("#save_condition_group_"+groupid+"_remove");
							rem_group_img.bind("click", function(){
								removeConditionGroup(groupid);
							});

							// First set groupno to highest groupid
							if(groupno < groupid) {
								groupno = groupid;
							}
							// Once groupno is same as highest groupid, increment it for next group usage
							if(groupid == groupno) {
								groupno += 1;
							}
						}

						$("#save_condition_group_"+groupid).append(
							'<div id="save_condition_'+condid+'" style=\'margin-bottom: 5px\'> \
								<input type="hidden" id="save_condition_'+condid+'_groupid" class="groupid" value="'+groupid+'" /> \
                                <select id="save_condition_'+condid+'_fieldname" class="fieldname"></select> \
								<select id="save_condition_'+condid+'_operation" class="operation"></select> \
                                <input type="hidden" id="save_condition_'+condid+'_value_type" class="expressiontype" /> \
                                <input type="text" id="save_condition_'+condid+'_value" class="expressionvalue" readonly /> \
								<select id="save_condition_'+condid+'_joincondition" class="joincondition"> \
                                    <option value="and">and</option> \
                                    <option value="or">or</option> \
                                </select> \
								<span id="save_condition_'+condid+'_remove" class="link remove-link"> \
								<img src="modules/com_vtiger_workflow/resources/remove.png"></span> \
                            </div>'
							);
						resetJoinCondition(groupid, condid);

						var fe = $("#save_condition_"+condid+"_fieldname");
						var i = 1;
						fillOptions(fe, fieldLabels);

						var fullFieldName = fe.attr("value");

						resetFields(getFieldType(fullFieldName), condid);

						var re = $("#save_condition_"+condid+"_remove");
						re.bind("click", function(){
							removeCondition(groupid, condid);
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

					var groupno=0;
					var condno=0;
					if(conditions){
						$.each(conditions, function(i, condition){
							var fieldname = condition["fieldname"];
							var groupid = condition["groupid"];
							if(groupid == '') groupid = 0;
							addCondition(groupid, condno);
							$(format("#save_condition_%s_fieldname", condno)).attr("value", fieldname);
							resetFields(getFieldType(fieldname), condno);
							$(format("#save_condition_%s_operation", condno)).attr("value", condition["operation"]);
							$('#dump').html(condition["value"]);
							var text = $('#dump').text();
							$(format("#save_condition_%s_value", condno)).attr("value", text);
							$(format("#save_condition_%s_value_type", condno)).attr("value", condition["valuetype"]);
							if(condition["joincondition"] != '') {
								$(format("#save_condition_%s_joincondition", condno)).attr("value", condition["joincondition"]);
							}
							if(condition["groupjoin"]  != '') {
								$(format("#save_condition_group_%s_joincondition", groupid)).attr("value", condition["groupjoin"]);
							}
							condno+=1;
						});
					}

					$("#save_conditions_add").bind("click", function(){
						addCondition(groupno, condno++);
					});

					$("#save_submit").bind("click", function(){
						var conditions = [];
						i=0;
						$("#save_conditions").children(".condition_group_block").each(function(j, conditiongroupblock){
							$(conditiongroupblock).children(".save_condition_group").each(function(k, conditiongroup){
								$(conditiongroup).children().each(function(l){
									var fieldname = $(this).children(".fieldname").attr("value");
									var operation = $(this).children(".operation").attr("value");
									var value = $(this).children(".expressionvalue").attr("value");
									var valuetype = $(this).children(".expressiontype").attr("value");
									var joincondition = $(this).children(".joincondition").attr("value");
									var groupid = $(this).children(".groupid").attr("value");
									var groupjoin = '';
									if(groupid != '') groupjoin = $('#save_condition_group_'+groupid+'_joincondition').attr("value");
									var condition = {
										fieldname:fieldname,
										operation:operation,
										value:value,
										valuetype:valuetype,
										joincondition:joincondition,
										groupid:groupid,
										groupjoin:groupjoin
									};
									conditions[i++]=condition;
								});
							});
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
					$('#new_task').show();
					$('#save_submit').show();
				}));
			}));
		}));
	});

}