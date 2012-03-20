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


<script type="text/javascript" src="include/js/general.js"></script>
<script type="text/javascript" src="include/js/Merge.js"></script>
<script>

function getImportSavedMap(impoptions)
{ldelim}
	var mapping = impoptions.options[impoptions.options.selectedIndex].value;

	//added to show the delete link
	if(mapping != -1)
		document.getElementById("delete_mapping").style.visibility = "visible";
	else
		document.getElementById("delete_mapping").style.visibility = "hidden";

	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=Import&mapping='+mapping+'&action=ImportAjax',
			onComplete: function(response) {ldelim}
					$("importmapform").innerHTML = response.responseText;
				{rdelim}
			{rdelim}
		);
{rdelim}
function deleteMapping()
{ldelim}
	var options_collection = document.getElementById("saved_source").options;
	var mapid = options_collection[options_collection.selectedIndex].value;

	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=Import&mapping='+mapid+'&action=ImportAjax&delete_map='+mapid,
			onComplete: function(response) {ldelim}
					$("importmapform").innerHTML = response.responseText;
				{rdelim}
			{rdelim}
		);

	//we have emptied the map name from the select list
	document.getElementById("saved_source").options[options_collection.selectedIndex] = null;
	document.getElementById("delete_mapping").style.visibility = "hidden";
	alert("{$APP.MAP_DELETED_INFO}");
{rdelim}
{literal}
function check_submit()
{
    var selectedColStr = "";
    if(document.getElementById("merge_check").checked == true)
    {
    	setObjects();
    	for (i=0;i<selectedColumnsObj.options.length;i++) 
	        selectedColStr += selectedColumnsObj.options[i].value + ",";
	    if (selectedColStr == "")
	    {
	    	{/literal}
	    	alert('{$APP.LBL_MERGE_SHOULDHAVE_INFO}');
	    	{literal}
	    	return false;
	    }
	    document.Import.selectedColumnsString.value = selectedColStr;
	}
	if(validate_import_map())
	{	
		if(document.getElementById("save_map").checked)
	        {
	                var name=document.getElementById("save_map_as").value
	                $("status").style.display="block";
	                new Ajax.Request(
	                'index.php',
	                {queue: {position: 'end', scope: 'command'},
	                method: 'post',
                        postBody: 'module=Import&name='+name+'&ajax_action=check_dup_map_name&action=ImportAjax',
                        onComplete: function(response) {

			if(response.responseText == 'true')
        	                document.Import.submit();
{/literal}              else
				if(confirm("{$APP.MAP_NAME_EXISTS}"))
{literal}					document.Import.submit();
	                	$("status").style.display="none";

                			                }
                        }
			                );


		}
		else
			document.Import.submit();
	}
}

//added for duplicate handling -srini
function show_option(obj)
{
	var sel_value = obj.value;
	if(sel_value == 'auto')
	{
		$("auto_option").innerHTML = $("option_div").innerHTML;	
	}
	else
		$("auto_option").innerHTML = "&nbsp;";
}

function showMergeOptions(curObj, arg)
{
	var ele = curObj;
	var mergeoptions = document.getElementsByName('dup_type');
	if (mergeoptions != null && ele != null) {
		if (ele.checked == true) {
			mergeoptions[0].checked = true;
			mergeoptions[1].checked = false;
		} else {
			mergeoptions[0].checked = false;
			mergeoptions[1].checked = false;
		}
		mergeshowhide(arg);
	}
}
{/literal}
      var moveupLinkObj,moveupDisabledObj,movedownLinkObj,movedownDisabledObj;
        function setObjects() 
        {ldelim}
            availListObj=getObj("availList")
            selectedColumnsObj=getObj("selectedColumns")

        {rdelim}

        function addColumn() 
        {ldelim}
        setObjects();
            for (i=0;i<selectedColumnsObj.length;i++) 
            {ldelim}
                selectedColumnsObj.options[i].selected=false
            {rdelim}

            for (i=0;i<availListObj.length;i++) 
            {ldelim}
                if (availListObj.options[i].selected==true) 
                {ldelim}            	
                	var rowFound=false;
                	var existingObj=null;
                    for (j=0;j<selectedColumnsObj.length;j++) 
                    {ldelim}
                        if (selectedColumnsObj.options[j].value==availListObj.options[i].value) 
                        {ldelim}
                            rowFound=true
                            existingObj=selectedColumnsObj.options[j]
                            break
                        {rdelim}
                    {rdelim}

                    if (rowFound!=true) 
                    {ldelim}
                        var newColObj=document.createElement("OPTION")
                        newColObj.value=availListObj.options[i].value
                        if (browser_ie) newColObj.innerText=availListObj.options[i].innerText
                        else if (browser_nn4 || browser_nn6) newColObj.text=availListObj.options[i].text
                        selectedColumnsObj.appendChild(newColObj)
                        availListObj.options[i].selected=false
                        newColObj.selected=true
                        rowFound=false
                    {rdelim} 
                    else 
                    {ldelim}
                        if(existingObj != null) existingObj.selected=true
                    {rdelim}
                {rdelim}
            {rdelim}
        {rdelim}

        function delColumn() 
        {ldelim}
        setObjects();
            for (i=selectedColumnsObj.options.length;i>0;i--) 
            {ldelim}
                if (selectedColumnsObj.options.selectedIndex>=0)
                selectedColumnsObj.remove(selectedColumnsObj.options.selectedIndex)
            {rdelim}
        {rdelim}
                        
        function formSelectColumnString()
        {ldelim}
            var selectedColStr = "";
            setObjects();
            for (i=0;i<selectedColumnsObj.options.length;i++) 
            {ldelim}
                selectedColStr += selectedColumnsObj.options[i].value + ",";
            {rdelim}
            if (selectedColStr == "")
            {ldelim}
            	alert('{$APP.LBL_MERGE_SHOULDHAVE_INFO}');
            	return false;
            {rdelim}
            document.Import.selectedColumnsString.value = selectedColStr;
            return;
        {rdelim}
	setObjects();	
</script>
<!-- header - level 2 tabs -->
{include file='Buttons_List1.tpl'}	

<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%" class="small">
<tbody>
   <tr>
	<td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}" /></td>
	<td class="showPanelBg" valign="top" width="100%">
		<table  cellpadding="0" cellspacing="0" width="100%" class="small">
		   <tr>
			<td width="75%" valign=top>
				<form enctype="multipart/form-data" name="Import" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="module" value="{$MODULE}">
				<input type="hidden" name="action" value="Import">
				<input type="hidden" name="step" value="3">
				<input type="hidden" name="has_header" value="{$HAS_HEADER}">
				<input type="hidden" name="source" value="{$SOURCE}">
				<input type="hidden" name="delimiter" value="{$DELIMITER}">
				<input type="hidden" name="tmp_file" value="{$TMP_FILE}">
				<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
				<input type="hidden" name="return_id" value="{$RETURN_ID}">
				<input type="hidden" name="return_action" value="{$RETURN_ACTION}">
				<input type="hidden" name="parenttab" value="{$CATEGORY}">

				<!-- IMPORT LEADS STARTS HERE  -->
				<br />
				<table align="center" cellpadding="5" cellspacing="0" width="95%" class="mailClient importLeadUI small">
				   <tr>
					<td class="mailClientBg genHeaderSmall" height="50" valign="middle" align="left" >{$MOD.LBL_MODULE_NAME} {$MODULELABEL}</td>
				   </tr>
				   <tr>
					<td>&nbsp;</td>
				   </tr>
				   <tr>
					<td align="left"  style="padding-left:40px;">
							<span class="genHeaderGray">{$MOD.LBL_STEP_2_4}</span>&nbsp;
						<span class="genHeaderSmall">{$MODULELABEL} {$MOD.LBL_LIST_MAPPING} </span>
					</td>
				   </tr>
				   <tr>
					<td align="left" style="padding-left:40px;"> 
					   {$MOD.LBL_STEP_2_MSG} {$MODULELABEL} {$MOD.LBL_STEP_2_MSG1} 
					   {$MOD.LBL_STEP_2_TXT} {$MODULELABEL}. 
					</td>
				   </tr>
				   <tr>
					<td>&nbsp;</td>
				   </tr>
				   <tr>
					<td align="left" style="padding-left:40px;" >
						<input type="checkbox" name="use_saved_mapping" id="saved_map_checkbox" onclick="ActivateCheckBox()" />&nbsp;&nbsp;
						{$MOD.LBL_USE_SAVED_MAPPING}&nbsp;&nbsp;&nbsp;{$SAVED_MAP_LISTS}
					</td>
				   </tr>
				   <tr>
					<td  align="left"style="padding-left:40px;padding-right:40px;">
						<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="100%" >
						   <tr bgcolor="white">
							<td width="25%" class="lvtCol" align="center"><b>{$MOD.LBL_MAPPING}</b></td>
							{if $HASHEADER eq 1}
							<td width="25%" bgcolor="#E1E1E1"  ><b>{$MOD.LBL_HEADERS}</b></td>
							<td width="25%" ><b>{$MOD.LBL_ROW} 1</b></td>
							<td width="25%" ><b>{$MOD.LBL_ROW} 2</b></td>
							{else}
							<td width="25%" ><b>{$MOD.LBL_ROW} 1</b></td>
							<td width="25%" ><b>{$MOD.LBL_ROW} 2</b></td>
							<td width="25%" ><b>{$MOD.LBL_ROW} 3</b></td>
							{/if}
						   </tr>
						</table>
						{assign var="Firstrow" value=$FIRSTROW}
						{assign var="Secondrow" value=$SECONDROW}
						{assign var="Thirdrow" value=$THIRDROW}				
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
						   <tr>
							<td width="25%" valign="top">
								<div id="importmapform">
									{include file="ImportMap.tpl"}
								</div>
							</td>	
							<td valign="top">
								<table border="0" cellpadding="0" cellspacing="1" width="100%" valign="top"  class="small">
								   {foreach name=iter item=row1 from=$Firstrow}
									{assign var="counter" value=$smarty.foreach.iter.iteration}
									{math assign="num" equation="x - y" x=$counter y=1}	
								   <tr bgcolor="white" >
									{if $HASHEADER eq 1}
										<td bgcolor="#E1E1E1" width="33%" height="30">&nbsp;{$row1}</td>
										<td width="34%">&nbsp;{$Secondrow[$num]}</td>
										<td>&nbsp;{$Thirdrow[$num]}</td>
									{else}
										<td width="31%" height="30">&nbsp;{$row1}</td>
										<td width="30%">&nbsp;{$Secondrow[$num]}</td>
										<td>&nbsp;{$Thirdrow[$num]}</td>
									{/if}	
								   </tr>
								   {/foreach}
								</table>
							</td>
						   </tr>
						</table>	
					</td>
				   </tr>
				   <tr>
					<td align="left" style="padding-left:40px;" >
						<input type="checkbox" name="save_map" id="save_map" onclick="set_readonly(this.form)" />&nbsp;&nbsp;
						{$MOD.LBL_SAVE_AS_CUSTOM} &nbsp;&nbsp;&nbsp;
						<input type="text" readonly name="save_map_as" id="save_map_as" value="" class="importBox" >
					</td>
				   </tr>
				   <!-- added for duplicate handling -srini -->
						<tr>
							<td>&nbsp;</td>
					   	</tr>
					   	<tr>
					   		<td align="left" style="padding-left:40px;" >
					   		{if $DUPLICATESHANDLING eq 'DuplicatesHandling'}
					   			<input id="merge_check" type="checkbox" onclick="showMergeOptions(this, 'importMergeDup')"/>
								<span class="genHeaderGray">{$MOD.LBL_STEP_3_4} </span>
								<span class="genHeaderSmall">{$APP.LBL_DUPLICATE_MERGING} </span>
								<span>({$APP.LBL_SELECT_TO_ENABLE_MERGING})</span> 
							{else}	
					   			<input id="merge_check" type="checkbox" disabled="true" onclick="mergeshowhide(this, 'importMergeDup')"/>
								<span class="genHeaderGray">{$MOD.LBL_STEP_3_4} </span>
								<span class="genHeaderSmall">{$APP.LBL_DUPLICATE_MERGING} </span>
								<span style="color:red;">({$APP.LBL_PERMISSION})</span>
					   		{/if}
							</td>
					   	</tr>
					   	<tr>
	                   		<td align="left"  style="padding-left:40px;">
							<div id="importMergeDup" style="z-index:1;display:none;position:relative;">
		                        
		                        <span  style="padding-left:40px;font-weight:bold;">{$MOD.Select_Criteria_For_Duplicate}</span>
	                         
								<table align="middle" border=0 width=100%>
					           		<tr>
		                           		<td align="left" style="padding-left:50px;">
		                                	<input name="dup_type" value="manual" type="radio" onClick="show_option(this);">{$MOD.Manual_Merging}<br>
											<input name="dup_type" value="auto" type="radio" onClick="show_option(this);">{$MOD.Auto_Merging}
		                                </td>
		                            </tr>
		                            <tr>
										<td id='auto_option' align="left" style="padding-left:50px;">&nbsp;</td>
						   			</tr>
								</table>
									<div id='option_div' style="display:none;">
										&nbsp;&nbsp;&nbsp;&nbsp;<input name="auto_type" value="ignore" type="radio" checked>{$MOD.Ignore_Duplicate}<br>
	                                                        &nbsp;&nbsp;&nbsp;&nbsp;<input name="auto_type" value="overwrite" type="radio">{$MOD.Overwrite_Duplicate}
									</div>
								<input type="hidden" name="selectedColumnsString"/>
								<table class="searchUIBasic small" border="0" cellpadding="5" cellspacing="0" width="80%" height="10" align="center">
									<tbody>
										<tr class="lvtCol" style="Font-Weight: normal"><br>
											<td colspan="3">
												<span class="moduleName">{$APP.LBL_SELECT_MERGECRITERIA_HEADER}</span><br>
												<span font-weight:normal>{$APP.LBL_SELECT_MERGECRITERIA_TEXT}</span>
											</td>
				   						   </tr>
				   						   <tr><td colspan="3"></td></tr>
										   <tr>
											<td><b>{$APP.LBL_AVAILABLE_FIELDS}</b></td>
											<td></td>
											<td><b>{$APP.LBL_SELECTED_FIELDS}</b></td>
										   </tr>
										   <tr>
											<td width=47%>
												<select id="availList" multiple size="10" name="availList" class="txtBox" Style="width: 100%">{$AVALABLE_FIELDS}</select>
											</td>
											<td width="6%">
												<div align="center">
													<input type="button" name="Button" value="&nbsp;&rsaquo;&rsaquo;&nbsp;" onClick="addColumn()" class="crmButton small" width="100%" /><br /><br />
													<input type="button" name="Button1" value="&nbsp;&lsaquo;&lsaquo;&nbsp;" onClick="delColumn()" class="crmButton small" width="100%" /><br /><br />
												</div>
											</td>
											<td width="47%">
												<select id="selectedColumns" size="10" name="selectedColumns" multiple class="txtBox" Style="width: 100%">{$FIELDS_TO_MERGE}</select>
											</td>
										   </tr>
									</tbody>
								</table>
							</div>
							</td>
					   	</tr>
				<!-- duplicate handling ends -->
				   <tr>
					<td align="right" style="padding-right:40px;" class="reportCreateBottom" >
						<input type="submit" name="button"  value=" &nbsp;&lsaquo; {$MOD.LBL_BACK} &nbsp; " class="crmbutton small cancel" onclick="this.form.action.value='Import';this.form.step.value='1'; return true;" />
						&nbsp;&nbsp;
						<input type="button" name="button"  value=" &nbsp; {$MOD.LBL_IMPORT_NOW} &rsaquo; &nbsp; " class="crmbutton small save" onclick="this.form.action.value='Import';this.form.step.value='3'; check_submit();" />
					</td>
				   </tr>
				  </table>
				</form>
				<!-- IMPORT LEADS ENDS HERE -->	
			</td>
		   </tr>
		</table>
	</td>
   </tr>
</table>

