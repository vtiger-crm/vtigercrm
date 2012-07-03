{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
-->*}

<script type="text/javascript" charset="utf-8">
var moduleName = '{$entityName}';
var methodName = '{$task->methodName}';
{literal}
	function entityMethodScript($){
		
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
		
		
		$(document).ready(function(){
			jsonget('entitymethodjson', {module_name:moduleName}, function(result){
				$('#method_name_select_busyicon').hide();
				if(result.length==0){
					$('#method_name_select').hide();
					$('#message_text').show();
				}else{					
					$('#method_name_select').show();
					$('#message_text').hide();
					$.each(result, function(i, v){
						var optionText = '<option value="'+v+'" '+(v==methodName?'selected':'')+'>'+v+'</option>';
						$('#method_name_select').append(optionText);
					});
				}
			});
		});
	}
{/literal}
entityMethodScript(jQuery);
</script>

<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
	<tr valign="top">
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>Method Name: </b></td>
		<td class='dvtCellInfo'>
			<span id="method_name_select_busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select name="methodName" id="method_name_select" class="small" style="display: none;"></select>
			<span id="message_text" style="display: none;">No method is available for this module.</sspan>
		</td>
	</tr>
</table> 