function VTCreateTodoTask($){

	var map = fn.map;
	var dict = fn.dict;
	var filter = fn.filter;
	var reduceR = fn.reduceR;
	var parallelExecuter = fn.parallelExecuter;
	var contains = fn.contains;
	var concat = fn.concat;


	function parse12HoursTime(timeStr){
		var hours;

		var match = timeStr.match(/(\d\d):(\d\d)(am|pm)/);
		if(match[3]=='am'){
			hours = parseInt(match[1], 10) % 12;
		}else{
			hours = parseInt(match[1], 10) % 12 + 12;
		}
		return hours*60+parseInt(match[2], 10);
	}

	function errorDialog(message){
		alert(message);
	}

	function index(arr, field){
		return dict(map(function(e){return [e[field], e];}, arr));
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

	function fillPicklist(picklistId, fieldInfo, defaultValue){
		var values = fieldInfo['type']['picklistValues'];
		var select = $('#'+picklistId);
		$.each(values, function(i, v){
			select.append('<option value="'+v['value']+'">'+v['label']+'</option>');
		});
		if(defaultValue!=''){
			select.attr('value', defaultValue);
		} else{
			select.attr('value',values[0]['value']);
		}
	}

	var validateDateRange = {
		init: function(){

		},
		validator: function(){
			var result;
			var successResult = [true];
			var failureResult = [false, 'invalid_date_range_message', []];
			if(this.fieldValue('startDatefield') == this.fieldValue('endDatefield')){
				var startTime = this.fieldValue('startTime');
				var endTime = this.fieldValue('endTime');
				var startDays = parseInt(this.fieldValue('startDays'), 10);
				var endDays = parseInt(this.fieldValue('endDays'), 10);
				var startDirection = this.fieldValue('startDirection')=="After"?1:-1;
				var endDirection = this.fieldValue('endDirection')=="After"?1:-1;
				var dd = endDays*endDirection - startDays*startDirection;
				if(dd<0){
					result = failureResult;
				}else if(dd==0){
					if(parse12HoursTime(startTime)>=parse12HoursTime(startTime)){
						result = failureResult;
					}else{
						result = successResult;
					}
				}else{
					result = successResult;
				}
			}else{
				result = successResult;
			}
			return result;
		}
	};

	var vtinst = new VtigerWebservices("webservice.php");
	vtinst.extendSession(handleError(function(result){
		$(document).ready(function(){
			//Setup the validator
			validator.addValidator('validateDateRange', validateDateRange);
			validator.mandatoryFields.push('eventName');

			vtinst.describeObject('Events', handleError(function(result){
				var fields = result['fields'];
				var fieldsMap = index(fields, 'name');
				fillPicklist('event_status', fieldsMap['eventstatus'], eventStatus);
				$('#event_status_busyicon').hide();
				$('#event_status').show();
				
				fillPicklist('event_type', fieldsMap['activitytype'], eventType);
				$('#event_type_busyicon').hide();
				$('#event_type').show();
			}));
		});
	}));
}
VTCreateTodoTask(jQuery);