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
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/JavaScript">    
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
            document.mergeDuplicates.selectedColumnsString.value = selectedColStr;
            return;
        {rdelim}
	setObjects();		
</script>	

<form enctype="multipart/form-data" name="mergeDuplicates" method="post" action="index.php?module={$MODULE}&action=FindDuplicateRecords" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="module" value="{$MODULE}">
	<input type="hidden" name="parenttab" value="{$CATEGORY}">
	<input type="hidden" name="action" value="FindDuplicateRecords">
	<input type="hidden" name="selectedColumnsString"/>
	<table class="searchUIBasic small" border="0" cellpadding="5" cellspacing="0" width="80%" height="10" align="center">
		<tbody><tr class="lvtCol" style="Font-Weight: normal"><br>
					<td colspan="2">
						<span class="moduleName">{$APP.LBL_SELECT_MERGECRITERIA_HEADER}</span><br>
						<span font-weight:normal>{$APP.LBL_SELECT_MERGECRITERIA_TEXT}</span>
					</td>
					<td valign="top" align="right" class="small">
						<span>&nbsp;</span>
						<span align="right" onClick="mergeshowhide('mergeDup')" onmouseover="this.style.cursor='pointer';"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"></span><br>
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
						<select id="availList" multiple size="10" name="availList" class="txtBox" Style="width: 100%">
						{$AVALABLE_FIELDS}
						</select>
					</td>
					<td width="6%">
						<div align="center">
							<input type="button" name="Button" value="&nbsp;&rsaquo;&rsaquo;&nbsp;" onClick="addColumn()" class="crmButton small" width="100%" /><br /><br />
							<input type="button" name="Button1" value="&nbsp;&lsaquo;&lsaquo;&nbsp;" onClick="delColumn()" class="crmButton small" width="100%" /><br /><br />
						</div>
					</td>
					<td width="47%">
						<select id="selectedColumns" size="10" name="selectedColumns" multiple class="txtBox" Style="width: 100%">
						{$FIELDS_TO_MERGE}
						</select>
					</td>
				</tr> 
				<tr>
					<td colspan="3" align="center">
					<input type="submit" name="save&merge" value="{$APP.LBL_SAVE_MERGE_BUTTON_TITLE}" class="crmbutton small edit" onClick="return formSelectColumnString()"/>
					<input type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmbutton small cancel" type="button" onClick="mergeshowhide('mergeDup');">
					</td>
				</tr>
		</tbody>
	</table>
</form>

