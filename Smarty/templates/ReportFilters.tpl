{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$APP.LBL_JSCALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script language="JavaScript" type="text/javascript" src="include/calculator/calc.js"></script>

<script type="text/javascript">
    var advft_column_index_count = -1;
    var advft_group_index_count = 0;
    var column_index_array = [];
    var group_index_array = [];
	var rel_fields = {$REL_FIELDS};
</script>

<script language="JavaScript" type="text/JavaScript"> 
function addColumnConditionGlue(columnIndex) {ldelim}

	var columnConditionGlueElement = document.getElementById('columnconditionglue_'+columnIndex);
	
	if(columnConditionGlueElement) {ldelim}		
		columnConditionGlueElement.innerHTML = "<select name='fcon"+columnIndex+"' id='fcon"+columnIndex+"' class='detailedViewTextBox'>"+
													"<option value='and'>{$MOD.LBL_AND}</option>"+
													"<option value='or'>{$MOD.LBL_OR}</option>"+
												"</select>";
	{rdelim}
{rdelim}

function addConditionRow(groupIndex) {ldelim}
		
	var groupColumns = column_index_array[groupIndex];
	if(typeof(groupColumns) != 'undefined') {ldelim} 		
		for(var i=groupColumns.length - 1; i>=0; --i) {ldelim}
			var prevColumnIndex = groupColumns[i];
			if(document.getElementById('conditioncolumn_'+groupIndex+'_'+prevColumnIndex)) {ldelim}
				addColumnConditionGlue(prevColumnIndex);
				break;
			{rdelim}
		{rdelim}
	{rdelim}
	
	var columnIndex = advft_column_index_count+1;
	var nextNode = document.getElementById('groupfooter_'+groupIndex);
	
	var newNode = document.createElement('tr');
	newNodeId = 'conditioncolumn_'+groupIndex+'_'+columnIndex;
  	newNode.setAttribute('id',newNodeId);
  	newNode.setAttribute('name','conditionColumn');
	nextNode.parentNode.insertBefore(newNode, nextNode);
	
	node1 = document.createElement('td');
	node1.setAttribute('class', 'dvtCellLabel');
	node1.setAttribute('width', '25%');
	newNode.appendChild(node1);
	node1.innerHTML = '<select name="fcol'+columnIndex+'" id="fcol'+columnIndex+'" onchange="updatefOptions(this, \'fop'+columnIndex+'\');addRequiredElements('+columnIndex+');updateRelFieldOptions(this, \'fval_'+columnIndex+'\');" class="detailedViewTextBox">'+
							'<option value="">{$MOD.LBL_NONE}</option>'+
	        				'{$COLUMNS_BLOCK}'+
						'</select>';
	
	node2 = document.createElement('td');
	node2.setAttribute('class', 'dvtCellLabel');
	node2.setAttribute('width', '25%');
	newNode.appendChild(node2);
	node2.innerHTML = '<select name="fop'+columnIndex+'" id="fop'+columnIndex+'" class="repBox" style="width:100px;" onchange="addRequiredElements('+columnIndex+');">'+
							'<option value="">{$MOD.LBL_NONE}</option>'+
							'{$FOPTION}'+
						'</select>';
	
	node3 = document.createElement('td');
	node3.setAttribute('class', 'dvtCellLabel');
	newNode.appendChild(node3);
	node3.innerHTML = '<input name="fval'+columnIndex+'" id="fval'+columnIndex+'" class="repBox" type="text" value="">'+
						'<img height=20 width=20 align="absmiddle" style="cursor: pointer;" title="{$APP.LBL_FIELD_FOR_COMPARISION}" alt="{$APP.LBL_FIELD_FOR_COMPARISION}" src="themes/images/terms.gif" onClick="hideAllElementsByName(\'relFieldsPopupDiv\'); fnvshobj(this,\'show_val'+columnIndex+'\');"/>'+
						'<input type="image" align="absmiddle" style="cursor: pointer;" onclick="document.getElementById(\'fval'+columnIndex+'\').value=\'\';return false;" language="javascript" title="{$APP.LBL_CLEAR}" alt="{$APP.LBL_CLEAR}" src="themes/images/clear_field.gif"/>'+
						'<div class="layerPopup" id="show_val'+columnIndex+'" name="relFieldsPopupDiv" style="border:0; position: absolute; width:300px; z-index: 50; display: none;">'+
							'<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="mailClient mailClientBg">'+
								'<tr>'+
									'<td>'+
										'<table width="100%" cellspacing="0" cellpadding="0" border="0" class="layerHeadingULine">'+
											'<tr background="themes/images/qcBg.gif" class="mailSubHeader">'+
												'<td width=90% class="genHeaderSmall"><b>{$MOD.LBL_SELECT_FIELDS}</b></td>'+
												'<td align=right>'+
													'<img border="0" align="absmiddle" src="themes/images/close.gif" style="cursor: pointer;" alt="{$APP.LBL_CLOSE}" title="{$APP.LBL_CLOSE}" onclick="hideAllElementsByName(\'relFieldsPopupDiv\');"/>'+
												'</td>'+
											'</tr>'+
										'</table>'+
							
										'<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">'+
											'<tr>'+
												'<td>'+
													'<table width="100%" cellspacing="0" cellpadding="5" border="0" bgcolor="white" class="small">'+
														'<tr>'+
															'<td width="30%" align="left" class="cellLabel small">{$MOD.LBL_RELATED_FIELDS}</td>'+
															'<td width="30%" align="left" class="cellText">'+
																'<select name="fval_'+columnIndex+'" id="fval_'+columnIndex+'" onChange="AddFieldToFilter('+columnIndex+',this);" class="detailedViewTextBox">'+
																	'<option value="">{$MOD.LBL_NONE}</option>'+
													        		'{$REL_FIELDS}'+
												        		'</select>'+
															'</td>'+
														'</tr>'+
													'</table>'+	
													'<!-- save cancel buttons -->'+
													'<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">'+
														'<tr>'+
															'<td width="50%" align="center">'+
																'<input type="button" style="width: 70px;" value="{$APP.LBL_DONE}" name="button" onclick="hideAllElementsByName(\'relFieldsPopupDiv\');" class="crmbutton small create" accesskey="X" title="{$APP.LBL_DONE}"/>'+
															'</td>'+
														'</tr>'+
													'</table>'+	
												'</td>'+
											'</tr>'+
										'</table>'+
									'</td>'+
								'</tr>'+
							'</table>'+
						'</div>';
	
	node4 = document.createElement('td');
	node4.setAttribute('class', 'dvtCellLabel');
	node4.setAttribute('id', 'columnconditionglue_'+columnIndex);
	node4.setAttribute('width', '60px');
	newNode.appendChild(node4);
	
	node5 = document.createElement('td');
	node5.setAttribute('class', 'dvtCellLabel');
	node5.setAttribute('width', '30px');
	newNode.appendChild(node5);
	node5.innerHTML = '<a onclick="deleteColumnRow('+groupIndex+','+columnIndex+');" href="javascript:;">'+
							'<img src="themes/images/delete.gif" align="absmiddle" title="{$MOD.LBL_DELETE}..." border="0">'+
						'</a>';

	if(typeof(column_index_array[groupIndex]) == 'undefined') column_index_array[groupIndex] = [];
	column_index_array[groupIndex].push(columnIndex);
	advft_column_index_count++;
{rdelim}

function addGroupConditionGlue(groupIndex) {ldelim}
	
	var groupConditionGlueElement = document.getElementById('groupconditionglue_'+groupIndex);
	if(groupConditionGlueElement) {ldelim}
		groupConditionGlueElement.innerHTML = "<select name='gpcon"+groupIndex+"' id='gpcon"+groupIndex+"' class='small'>"+
												"<option value='and'>{$MOD.LBL_AND}</option>"+
												"<option value='or'>{$MOD.LBL_OR}</option>"+
											"</select>";
	{rdelim}
{rdelim}

function addConditionGroup(parentNodeId) {ldelim}
	
	for(var i=group_index_array.length - 1; i>=0; --i) {ldelim}
		var prevGroupIndex = group_index_array[i];
		if(document.getElementById('conditiongroup_'+prevGroupIndex)) {ldelim}
			addGroupConditionGlue(prevGroupIndex);
			break;
		{rdelim}
	{rdelim}
	
	var groupIndex = advft_group_index_count+1;
	var parentNode = document.getElementById(parentNodeId);	
	
	var newNode = document.createElement('div');
	newNodeId = 'conditiongroup_'+groupIndex;
  	newNode.setAttribute('id',newNodeId);
  	newNode.setAttribute('name','conditionGroup');
  	
  	newNode.innerHTML = "<table class='small crmTable' border='0' cellpadding='5' cellspacing='1' width='100%' valign='top' id='conditiongrouptable_"+groupIndex+"'>"+
							"<tr id='groupheader_"+groupIndex+"'>"+
								"<td colspan='5' align='right'>"+
									"<a href='javascript:void(0);' onclick='deleteGroup(\""+groupIndex+"\");'><img border=0 src={'close.gif'|@vtiger_imageurl:$THEME} alt='{$MOD.LBL_DELETE_GROUP}' title='{$MOD.LBL_DELETE_GROUP}'/></a>"+
								"</td>"+
							"</tr>"+
							"<tr id='groupfooter_"+groupIndex+"'>"+
								"<td colspan='5' align='left'>"+
									"<input type='button' class='crmbutton edit small' value='{$MOD.LBL_NEW_CONDITION}' onclick='addConditionRow(\""+groupIndex+"\")' />"+
								"</td>"+
							"</tr>"+
						"</table>"+
						"<table class='small' border='0' cellpadding='5' cellspacing='1' width='100%' valign='top'>"+
							"<tr><td align='center' id='groupconditionglue_"+groupIndex+"'>"+
							"</td></tr>"+
						"</table>";

	parentNode.appendChild(newNode);
	
	group_index_array.push(groupIndex);
	advft_group_index_count++;
{rdelim}

function addNewConditionGroup(parentNodeId) {ldelim}
	addConditionGroup(parentNodeId);	
	addConditionRow(advft_group_index_count);
{rdelim}

function deleteColumnRow(groupIndex, columnIndex) {ldelim}
	removeElement('conditioncolumn_'+groupIndex+'_'+columnIndex);
	
	var groupColumns = column_index_array[groupIndex];
	var keyOfTheColumn = groupColumns.indexOf(columnIndex);
	var isLastElement = true;
	
	for(var i=keyOfTheColumn; i<groupColumns.length; ++i) {ldelim}
		var nextColumnIndex = groupColumns[i];
		var nextColumnRowId = 'conditioncolumn_'+groupIndex+'_'+nextColumnIndex;
		if(document.getElementById(nextColumnRowId)) {ldelim}
			isLastElement = false;
			break;
		{rdelim}
	{rdelim}
	
	if(isLastElement) {ldelim}
		for(var i=keyOfTheColumn-1; i>=0; --i) {ldelim}
			var prevColumnIndex = groupColumns[i];
			var prevColumnGlueId = "fcon"+prevColumnIndex;
			if(document.getElementById(prevColumnGlueId)) {ldelim}
				removeElement(prevColumnGlueId);
				break;
			{rdelim}
		{rdelim}
	{rdelim}
{rdelim}

function deleteGroup(groupIndex) {ldelim}
	removeElement('conditiongroup_'+groupIndex);
	
	var keyOfTheGroup = group_index_array.indexOf(groupIndex);
	var isLastElement = true;
	
	for(var i=keyOfTheGroup; i<group_index_array.length; ++i) {ldelim}
		var nextGroupIndex = group_index_array[i];
		var nextGroupBlockId = "conditiongroup_"+nextGroupIndex;
		if(document.getElementById(nextGroupBlockId)) {ldelim}
			isLastElement = false;
			break;
		{rdelim}
	{rdelim}
	
	
	if(isLastElement) {ldelim}
		for(var i=keyOfTheGroup-1; i>=0; --i) {ldelim}
			var prevGroupIndex = group_index_array[i];
			var prevGroupGlueId = "gpcon"+prevGroupIndex;
			if(document.getElementById(prevGroupGlueId)) {ldelim}
				removeElement(prevGroupGlueId);
				break;
			{rdelim}
		{rdelim}
	{rdelim}
	
{rdelim}

function removeElement(elementId) {ldelim}
	var element = document.getElementById(elementId);
	if(element) {ldelim}
		var parent = element.parentNode;
		if(parent) {ldelim}
			parent.removeChild(element);
		{rdelim} else {ldelim}
			element.remove();
		{rdelim}
	{rdelim}
{rdelim}

function hideAllElementsByName(name) {ldelim}
	var allElements = document.getElementsByTagName('div');
	for(var i=0; i<allElements.length; ++i) {ldelim}
		var element = allElements[i];
		if (element.getAttribute('name') == name)
			element.style.display='none';
	{rdelim}
	return true;
{rdelim}

function addRequiredElements(columnindex) {ldelim}

	var colObj = document.getElementById('fcol'+columnindex);
	var opObj = document.getElementById('fop'+columnindex);
    var valObj = document.getElementById('fval'+columnindex);   
    
	var currField = colObj.options[colObj.selectedIndex];
    var currOp = opObj.options[opObj.selectedIndex];
    
    var fieldtype = null ;
    if(currField.value != null && currField.value.length != 0) {ldelim}
		fieldtype = trimfValues(currField.value);
		
		switch(fieldtype) {ldelim}
			case 'D':
			case 'T':	var dateformat = "{$JS_DATEFORMAT}";
						var timeformat = "%H:%M:%S";
						var showtime = true;
						if(fieldtype == 'D') {ldelim}
							timeformat = '';
							showtime = false;
						{rdelim}						
						
						if(!document.getElementById('jscal_trigger_fval'+columnindex)) {ldelim} 
							var node = document.createElement('img');
							node.setAttribute('src','{$IMAGE_PATH}btnL3Calendar.gif');
							node.setAttribute('id','jscal_trigger_fval'+columnindex);
							node.setAttribute('align','absmiddle');
							node.setAttribute('width','20');
							node.setAttribute('height','20');
							
	    					var parentObj = valObj.parentNode;						
	    					var nextObj = valObj.nextSibling;
							parentObj.insertBefore(node, nextObj);
						{rdelim}
						
						Calendar.setup ({ldelim}
							inputField : 'fval'+columnindex, ifFormat : dateformat+' '+timeformat, showsTime : showtime, button : "jscal_trigger_fval"+columnindex, singleClick : true, step : 1
                        {rdelim});
                                                
                        if(currOp.value == 'bw') {ldelim}
                        	if(!document.getElementById('fval_ext'+columnindex)) {ldelim} 
	                        	var fillernode = document.createElement('br');
	                        	
	                        	var node1 = document.createElement('input');
	                        	node1.setAttribute('class', 'repBox');
	                        	node1.setAttribute('type', 'text');
	                        	node1.setAttribute('id','fval_ext'+columnindex);
	                        	node1.setAttribute('name','fval_ext'+columnindex);
	                        	
	    						var parentObj = valObj.parentNode;
								parentObj.appendChild(fillernode);
								parentObj.appendChild(node1);
							{rdelim}
							
							if(!document.getElementById('jscal_trigger_fval_ext'+columnindex)) {ldelim}
								var node2 = document.createElement('img');
								node2.setAttribute('src','{$IMAGE_PATH}btnL3Calendar.gif');
								node2.setAttribute('id','jscal_trigger_fval_ext'+columnindex);
								node2.setAttribute('align','absmiddle');
								node2.setAttribute('width','20');
								node2.setAttribute('height','20');
							
	    						var parentObj = valObj.parentNode;
							 	parentObj.appendChild(node2);
							 {rdelim}
							
							Calendar.setup ({ldelim}
								inputField : 'fval_ext'+columnindex, ifFormat : dateformat+' '+timeformat, showsTime : showtime, button : "jscal_trigger_fval_ext"+columnindex, singleClick : true, step : 1
	                        {rdelim});	
                       	{rdelim} else {ldelim}
							if(document.getElementById('fval_ext'+columnindex)) document.getElementById('fval_ext'+columnindex).remove();
							if(document.getElementById('jscal_trigger_fval_ext'+columnindex)) document.getElementById('jscal_trigger_fval_ext'+columnindex).remove();
                       	{rdelim}              
                        
                        break;
						
			default	:	if(document.getElementById('jscal_trigger_fval'+columnindex)) document.getElementById('jscal_trigger_fval'+columnindex).remove();
						if(document.getElementById('fval_ext'+columnindex)) document.getElementById('fval_ext'+columnindex).remove();
						if(document.getElementById('jscal_trigger_fval_ext'+columnindex)) document.getElementById('jscal_trigger_fval_ext'+columnindex).remove();
		{rdelim}
	{rdelim}
{rdelim}

function showHideDivs(showdiv, hidediv) {ldelim}
	if(document.getElementById(showdiv)) document.getElementById(showdiv).style.display = "block";
	if(document.getElementById(hidediv)) document.getElementById(hidediv).style.display = "none";
{rdelim}
</script>

{$BLOCKJS_STD}

<input type="hidden" name="advft_criteria" id="advft_criteria" value="" />
<input type="hidden" name="advft_criteria_groups" id="advft_criteria_groups" value="" />

<table class="small" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" height="532" width="100%" valign="top">
	<tr>
		<td colspan="2">
			<span class="genHeaderGray">{$MOD.LBL_FILTERS}</span><br>
			{$MOD.LBL_SELECT_FILTERS_TO_STREAMLINE_REPORT_DATA}
			<hr>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="left">
			<span id='std_filter_div_show' name='std_filter_div_show'>
				<img border="0" align="absmiddle" src={'inactivate.gif'|@vtiger_imageurl:$THEME}  onclick="showHideDivs('std_filter_div','std_filter_div_show');" style="cursor:pointer;" />
				<b>{$MOD.LBL_SHOW_STANDARD_FILTERS}</b>
			</span>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div id='std_filter_div' name='std_filter_div' style="display:none;">
				<table class="small" border="0" cellpadding="5" cellspacing="0" width="100%">
					<tr>
						<td class="detailedViewHeader" colspan="4">
							<img border="0" align="absmiddle" src={'activate.gif'|@vtiger_imageurl:$THEME} onclick="showHideDivs('std_filter_div_show','std_filter_div');" style="cursor:pointer;" />
							<b>{$MOD.LBL_STANDARD_FILTER}</b>
						</td>
					</tr>
					<tr>
						<td class="dvtCellLabel" width="30%">{$MOD.LBL_SF_COLUMNS}:</td>
						<td class="dvtCellLabel" width="30%">&nbsp;</td>
						<td class="dvtCellLabel" width="20%">{$MOD.LBL_SF_STARTDATE}:</td>
						<td class="dvtCellLabel" width="20%">{$MOD.LBL_SF_ENDDATE}:</td>
					</tr>
					<tr>
						<td class="dvtCellInfo" width="60%">
							<select name="stdDateFilterField" class="detailedViewTextBox" onchange='standardFilterDisplay();'>
							{$BLOCK1_STD}
							</select>
						</td>
						<td class="dvtCellInfo" width="25%">
							<select name="stdDateFilter" id="stdDateFilter" onchange='showDateRange( this.options[ this.selectedIndex ].value )' class="repBox">
							{$BLOCKCRITERIA_STD}
							</select>
						</td>
						<td class="dvtCellInfo">
							<input name="startdate" id="jscal_field_date_start" style="border: 1px solid rgb(186, 186, 186);" size="10" maxlength="10" value="{$STARTDATE_STD}" type="text"><br>
							<img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_start" >
							<font size="1"><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
							<script type="text/javascript">
                                Calendar.setup ({ldelim}
                                inputField : "jscal_field_date_start", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1
                                {rdelim})
							</script>
						</td>
						<td class="dvtCellInfo">
							<input name="enddate" id="jscal_field_date_end" style="border: 1px solid rgb(186, 186, 186);" size="10" maxlength="10" value="{$ENDDATE_STD}" type="text"><br>
							<img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_end" >
							<font size="1"><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
			                <script type="text/javascript">
                                Calendar.setup ({ldelim}
                                inputField : "jscal_field_date_end", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_end", singleClick : true, step : 1
                                {rdelim})
			                </script>
						</td>
					</tr>					
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div style="overflow:auto;height:448px" id='adv_filter_div' name='adv_filter_div'>
				<table class="small" border="0" cellpadding="5" cellspacing="0" width="100%">
					<tr>
						<td class="detailedViewHeader"><b>{$MOD.LBL_ADVANCED_FILTER}</b></td>
					</tr>
					<tr>
						<td colspan="2" align="right">
							<input type="button" class="crmbutton create small" value="{$MOD.LBL_NEW_GROUP}" onclick="addNewConditionGroup('adv_filter_div')" />
						</td>
					</tr>
				</table>
				{foreach key=GROUP_ID item=GROUP_CRITERIA from=$CRITERIA_GROUPS}
					{assign var=GROUP_COLUMNS value=$GROUP_CRITERIA.columns}
					<script type="text/javascript">
						addConditionGroup('adv_filter_div');
					</script>
					{foreach key=COLUMN_INDEX item=COLUMN_CRITERIA from=$GROUP_COLUMNS}
					<script type="text/javascript">
						addConditionRow('{$GROUP_ID}');
						
						document.getElementById('fop'+advft_column_index_count).value = '{$COLUMN_CRITERIA.comparator}';
						var conditionColumnRowElement = document.getElementById('fcol'+advft_column_index_count);
						conditionColumnRowElement.value = '{$COLUMN_CRITERIA.columnname}';
						updatefOptions(conditionColumnRowElement, 'fop'+advft_column_index_count);
						addRequiredElements(advft_column_index_count);
						updateRelFieldOptions(conditionColumnRowElement, 'fval_'+advft_column_index_count);
						
						var columnvalue = '{$COLUMN_CRITERIA.value}';
						if('{$COLUMN_CRITERIA.comparator}' == 'bw' && columnvalue != '') {ldelim}
							var values = columnvalue.split(",");
							document.getElementById('fval'+advft_column_index_count).value = values[0];
							if(values.length == 2 && document.getElementById('fval_ext'+advft_column_index_count))
								document.getElementById('fval_ext'+advft_column_index_count).value = values[1];
						{rdelim} else {ldelim}
							document.getElementById('fval'+advft_column_index_count).value = columnvalue;
						{rdelim}
					</script>
					{/foreach}
					{foreach key=COLUMN_INDEX item=COLUMN_CRITERIA from=$GROUP_COLUMNS}
					<script type="text/javascript">				
						if(document.getElementById('fcon{$COLUMN_INDEX}')) document.getElementById('fcon{$COLUMN_INDEX}').value = '{$COLUMN_CRITERIA.column_condition}';
					</script>
					{/foreach}
				{foreachelse}
				<script type="text/javascript">
					addNewConditionGroup('adv_filter_div');
				</script>
				{/foreach}
				{foreach key=GROUP_ID item=GROUP_CRITERIA from=$CRITERIA_GROUPS}
				<script type="text/javascript">
					if(document.getElementById('gpcon{$GROUP_ID}')) document.getElementById('gpcon{$GROUP_ID}').value = '{$GROUP_CRITERIA.condition}';
				</script>
				{/foreach}
			</div>
		</td>
	</tr>
</table>
