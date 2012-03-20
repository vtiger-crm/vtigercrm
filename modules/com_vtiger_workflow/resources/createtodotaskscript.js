function VTCreateTodoTask($){

	var map = fn.map;
	var dict = fn.dict;
	var filter = fn.filter;
	var reduceR = fn.reduceR;
	var parallelExecuter = fn.parallelExecuter;
	var contains = fn.contains;
	var concat = fn.concat;

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
		}
	}

	var vtinst = new VtigerWebservices("webservice.php");
	vtinst.extendSession(handleError(function(result){
		$(document).ready(function(){
			//Setup the validator
			validator.mandatoryFields.push('todo');

			vtinst.describeObject('Calendar', handleError(function(result){
				var fields = result['fields'];
				var fieldsMap = index(fields, 'name');
				var eventStatusType = fieldsMap['taskstatus'];
				var eventStatusValues = eventStatusType['type']['picklistValues'];

				var taskPriorityType = fieldsMap['taskpriority'];
				var taskPriorityValues = taskPriorityType['type']['picklistValues'];

				var status = $('#task_status');
				$.each(eventStatusValues, function(i, v){
					status.append('<option value="'+v['value']+'">'+v['label']+'</option>');
				});
				if(taskStatus!=''){
					status.attr('value', taskStatus);
				} else {
					status.attr('value',eventStatusValues[0]['value']);
				}
				$('#task_status_busyicon').hide();
				$('#task_status').show();
				
				var priority = $('#task_priority');
				$.each(taskPriorityValues, function(i, v){
					priority.append('<option value="'+v['value']+'">'+v['label']+'</option>');
				});
				if(taskPriority!=''){
					priority.attr('value', taskPriority);
				} else {
					priority.attr('value',taskPriorityValues[0]['value']);
				}
				$('#task_priority_busyicon').hide();
				$('#task_priority').show();
			}));
		});
	}));
}
VTCreateTodoTask(jQuery);