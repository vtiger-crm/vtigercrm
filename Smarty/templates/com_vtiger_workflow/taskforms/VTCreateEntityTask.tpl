<script type="text/javascript" charset="utf-8">
{literal}
	function editexpressionscript($){
		function errorDialog(message){
			alert(message);
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

		format = fn.format;
		var moduleName
		function editpopup(){
				function close(){
					$('#editpopup').css('display', 'none');
					$('#editpopup_expression').text('');
				}

				function show(module){
					$('#editpopup').css('display', 'block');
				}

				$('#editpopup_close').bind('click', close);	
				$('#editpopup_save').bind('click', function(){
					var expression = $('#editpopup_expression').attr('value');
					var fieldName = $('#editpopup_field').attr('value');
					var moduleName = $('#pick_module').attr('value');
					$.get('index.php', {
							module:'com_vtiger_autofillfields', 
							action:'com_vtiger_autofillfieldsAjax',	
							file:'saveexpressionjson', ajax:'true',
							modulename: moduleName, fieldname:fieldName,
							expression:expression
							}, 
						function(result){
							var parsed = JSON.parse(result);
							if(parsed.status=='success'){
								//
							}else{
								errorDialog('save failed because '+parsed.message);
							}
						});
					close();
				});

				$('#editpopup_cancel').bind('click', close);

				$('#editpopup_fieldnames').bind('change', function(){
					var textarea = $('#editpopup_expression').get(0);
					var value = $(this).attr('value');
					//http://alexking.org/blog/2003/06/02/inserting-at-the-cursor-using-javascript
					if (document.selection) {
						textarea.focus();
						var sel = document.selection.createRange();
						sel.text = value;
						textarea.focus();
					}else if (textarea.selectionStart || textarea.selectionStart == '0') {
						var startPos = textarea.selectionStart;
						var endPos = textarea.selectionEnd;
						var scrollTop = textarea.scrollTop;
						textarea.value = textarea.value.substring(0, startPos)
											+ value
											+ textarea.value.substring(endPos,
												textarea.value.length);
						textarea.focus();
						textarea.selectionStart = startPos + value.length;
						textarea.selectionEnd = startPos + value.length;
						textarea.scrollTop = scrollTop;
					}	else {
						textarea.value += value;
						textarea.focus();
					}

				});

				return {
					create: show,
					edit: function(field, expression){
						$("#editpopup_field").attr('value', field);
						$("#editpopup_expression").attr('value', expression);
						show();
					},
					close:close,
					changeModule: function(moduleName, exprFields, moduleFields){
						var field = $('#editpopup_field');
						field.children().remove();
						$.each(exprFields, function(fieldName, fieldLabel){
							field.append(format('<option value="%s">%s</option>', fieldName, fieldLabel));
						})

						var fieldNames = $('#editpopup_fieldnames');
						fieldNames.children().remove();
						$.each(moduleFields, function(fieldName, fieldLabel){
							fieldNames.append(format('<option value="%s">%s</option>', fieldName, fieldLabel));
						});
					}
				}
		}

		$(document).ready(function(){
			var ep = editpopup();

			function setModule(moduleName){
				$('#module_info').text(format('Map to "%s" Module',moduleName));
				$.get('index.php', {
						module:'com_vtiger_autofillfields', 
						action:'com_vtiger_autofillfieldsAjax',	
						file:'getfieldsjson', ajax:'true', 
						modulename:moduleName}, 
					function(result){
						var parsed = JSON.parse(result);
						ep.changeModule($(this).attr("value"), parsed['exprFields'], parsed['moduleFields']);
						if(parsed['exprFields'].length!=0){
						    $('#new_field_expression').attr('disabled', false);
							$('#new_field_expression').bind('click', function(){
								ep.create();
							});
							$('#status_message').html('');
						}else{
						    $('#new_field_expression').attr('disabled', true);
							$('#new_field_expression').unbind('click');
							$('#status_message').html('You need to add a <a href="">Custom Field</a>');
						}
				});
				jsonget('getexpressionlistjson', 
					{modulename:moduleName}, 
					function(result){
						$('.expressionlistrow').remove();
						$.each(result, function(fieldName, expression){
							editLink = format('<img border="0" title="Edit" alt="Edit" \
													style="cursor: pointer;" id="expressionlist_editlink_%s" \
													src="{'editfield.gif'|@vtiger_imageurl:$THEME}"/>', fieldName);
							deleteLink = format('<img border="0" title="Delete" alt="Delete"\
							 					src="{'delete.gif'|@vtiger_imageurl:$THEME}" \
												style="cursor: pointer;" id="expressionlist_deletelink_%s"/>', fieldName);
							row = format('<tr class="expressionlistrow" id="expressionlistrow_%s"> \
										<td class="listTableRow small" valign="top" nowrap="">%s</td>\
										<td class="listTableRow small" valign="top" nowrap="">%s</td>\
										<td class="listTableRow small" valign="top" nowrap="">%s | %s</td>\
									</tr>', fieldName, fieldName, expression, editLink, deleteLink);
							$('#expressionlist').append(row);
							$(format('#expressionlist_deletelink_%s', fieldName)).click(function(){
								jsonget('deleteexpressionjson', 
									{modulename:moduleName, fieldname:fieldName},
									function(result){
										if(result.status=='success'){
											$(format('expressionlist_deletelink_%s', fieldName)).remove();
										}else{
											errorDialog(result.message);
										}
									}
								);
							});
							$(format('#expressionlist_editlink_%s', fieldName)).click(function(){
								ep.edit(fieldName, expression);
							});
						});
					}
				);
				ep.close();


			}

			$('#pick_module').bind('change', function(){
				var moduleName =  $(this).attr("value");
				setModule(moduleName);
			});
			setModule($('#pick_module').attr('value'));



		});
	}
	editexpressionscript(jQuery);
{/literal}
</script>


<div id='editpopup' class='editpopup' style='display:none;'>
	<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerHeadingULine">
		<tr>
			<td width="60%" align="left" class="layerPopupHeading">
				Create Custom Field
				</td>
			<td width="40%" align="right">
				<a href="javascript:void;" id="editpopup_close">
					<img border="0" align="absmiddle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/>
				</a>
			</td>
		</tr>
	</table>
	<table width="100%" bgcolor="white" align="center" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td>
				<p>
					Field: 
					<select id='editpopup_field'>

						<option></option>

					</select>
				</p>
				<p>Expression:</p>
				<textarea name="Name" rows="8" cols="40" id='editpopup_expression'></textarea>
			</td>
			<td width="50%">
				<table width="100%" border="0" cellspacing="0" cellpadding="5">
					<tr>
						<td class="datalabel" nowrap="nowrap" align="right">
							<b>Fields: </b>
						</td>
						<td align="left">
							<select id='editpopup_fieldnames'></select>
						</td>
					</tr>
					<tr>
						<td class="datalabel" nowrap="nowrap" align="right">
							<b>Functions: </b>
						</td>
						<td align="left">
							<select></select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">
		<tr><td align="center">
			<input type="button" class="crmButton small save" value="Save" name="save" id='editpopup_save'/> 
			<input type="button" class="crmButton small cancel" value="Cancel " name="cancel" id='editpopup_cancel'/>
		</td></tr>
	</table>
</div>
<div id="view">
	<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td class="big" nowrap="">
				<strong><span id="module_info"></span></strong>
			</td>
			<td class="small" align="right">
				<b>Select Module: </b>
				<select class="importBox" name="pick_module" id='pick_module'>
{foreach  item=MODULE from=$MODULES}
					<option value="{$MODULE}" >
						{$APP.$MODULE}
					</option>
{/foreach}
				</select>
			</td>
		</tr>
	</table>
	<table class="listTableTopButtons" width="100%" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td class="small"> <span id="status_message"></span> </td>
			<td class="small" align="right">
				<input type="button" class="crmButton create small" 
					value="New Field Expression" id='new_field_expression'/>
			</td>
		</tr>
	</table>
	<table class="listTable" width="100%" border="0" cellspacing="0" cellpadding="5" id='expressionlist'>
		<tr>
			<td class="colHeader small" width="20%">
				Field
			</td>
			<td class="colHeader small" width="65">
				Expression
			</td>
			<td class="colHeader small" width="15%">
				Tools
			</td>
		</tr>
	</table>
</div>
