<div id="error_message_box" class="layerPopup"  style="display:none; z-index: 100000220">
	<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerHeadingULine">
		<tr>
			<td width="60%" align="left" class="layerPopupHeading">
				{$MOD.LBL_VALIDATION_ERROR}
			</td>
			<td width="40%" align="right">
				<a href="javascript:void(0);" id="error_message_box_close">
					<img border="0" align="middle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/>
				</a>
			</td>
		</tr>
	</table>
	<div class="popup_content" style='text-align: left;'>		
		<ol style="list-style-type: none;">
			<li id="empty_fields_message" style="display:none">
				<b><font color="red">*</font> {$MOD.LBL_VALIDATION_MISSING_MANDATORY_FIELDS}</b>
			</li>
			<li id="invalid_date_range_message" style="display:none">
				<b><font color="red">*</font> {$MOD.LBL_VALIDATION_INVALID_DATE_RANGE}</b>
			</li>
			<li id="duplicate_fields_selected_message" style="display:none">
				<b><font color="red">*</font> {$MOD.LBL_VALIDATION_DUPLICATE_FIELDNAMES}</b>
			</li>
			<li id="invalid_field_values_message" style="display:none">
				<b><font color="red">*</font> {$MOD.LBL_VALIDATION_INVALID_FIELD_VALUES}</b>
				<ul id="invalid_field_values_fieldlist"></ul>
			</li>
		</ol>
	</div>
</div>
	
