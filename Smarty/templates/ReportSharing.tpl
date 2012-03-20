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
{$BLOCKJS_STD}
<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" height='530' width="100%">
	<tbody>
	<tr valign='top'>
		<td colspan="2">
			<span class="genHeaderGray">{$MOD.LBL_SHARING_TYPE}</span><br>
			{$MOD.LBL_SELECT_REPORT_TYPE_TO_CONTROL_ACCESS}
			<hr>
		</td>
	</tr>
	
	<tr>
	<td colspan="2">
	<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tbody>
		<tr>
			<td colspan="4">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
					<tbody>
						<tr>
					    	<td nowrap="" align="center" id="mi" style="width: 100px;" colspan="2" class="detailedViewHeader"><!--onclick="fnLoadRepValues('mi','pi','mnuTab2','mnuTab')"-->
					     	<b>{$MOD.LBL_SHARING}</b>
					    	</td>
						</tr>
					</tbody>
				</table>		
			</td>
		</tr>
		<tr>
			<td colspan=4>
				<table width="100%" cellspacing="0" cellpadding="0" class="small" height="190px">
					<tr valign=top><td colspan="2">
						<table width="100%" border="0" cellpadding="5" class="small" cellspacing="0" align="center">
							<tr>
								<td align="right" class="dvtCellLabel" width="50%">{$MOD.SELECT_FILTER_TYPE} :</td>
								<td class="dvtCellInfo" width="50%" align="left">
									<select name="stdtypeFilter" id="stdtypeFilter" class="small" onchange='toggleAssignType(this.options[this.selectedIndex].value );'>
										{foreach item=visible from=$VISIBLECRITERIA}
											<option {$visible.selected} value={$visible.value}>{$visible.text}</option>
										{/foreach}
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="assign_team" style="display:none">
										<table width="100%" border="0" cellpadding="0" class="small" cellspacing="0" align="center">
											<tr>
												<td align=left colspan=2 class='dvtCellLabel' valign=top>
													<select id="memberType" name="memberType" class="small" onchange="show_Options()">
													<option value="groups" selected>{$MOD.LBL_GROUPS}</option>
													<option value="users">{$MOD.LBL_USERS}</option>
													</select>
													<input type="hidden" name="findStr" class="small">&nbsp;
												</td>
												<td align=right colspan=1 class='dvtCellLabel' valign=top>
													<b>{$MOD.LBL_MEMBERS}</b>
												</td>
											</tr>
											<tr>
												<td valign=top width=45%>
														<select id="availableList" name="availableList" multiple size="5" class="small crmFormList"></select>
														<input type="hidden" name="selectedColumnsStr"/>
												</td>
												<td width="10%">
													<div align="center">
														<input type="button" name="Button" value="&nbsp;&rsaquo;&rsaquo;&nbsp;" onClick="addColumns()" class="crmButton small"/><br /><br />
														<input type="button" name="Button1" value="&nbsp;&lsaquo;&lsaquo;&nbsp;" onClick="removeColumns()" class="crmButton small"/>
													</div>
												</td>
												<td class="small" width="45%" align='right' valign=top> 
													<select id="columnsSelected" name="columnsSelected" multiple size="5" class="small crmFormList">
														{foreach item=element from=$MEMBER}
															<option value="{$element.id}">{$element.name}</option>
														{/foreach}
													</select>
												</td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
					</table>
					</td></tr>
				</table>
			</td>
			</tr>
		</tbody>
	</table>
	</td></tr>
	<tr><td colspan="2" height="205">&nbsp;</td></tr>
	</tbody>
</table>
<script>
var userIdArr=new Array({$USERIDSTR});
var userNameArr=new Array({$USERNAMESTR});
var grpIdArr=new Array({$GROUPIDSTR});
var grpNameArr=new Array({$GROUPNAMESTR});

</script>
{literal}
<script>
stdfilterTypeDisplay();
function stdfilterTypeDisplay(){
	if(document.getElementById('stdtypeFilter').value == 'Shared'){
		document.getElementById("assign_team").style.display = 'block';
	} else {
		document.getElementById("assign_team").style.display = 'none';
	}
}

function toggleAssignType(id){
	if(id =='Shared'){
		document.getElementById("assign_team").style.display = 'block';
	} else {
		document.getElementById("assign_team").style.display = 'none';
	}
}
function show_Options() {
	var selectedOption=document.NewReport.memberType.value;
	
	//Completely clear the select box
	document.forms['NewReport'].availableList.options.length = 0;

	if(selectedOption == 'groups') {
		constructSelectOptions('groups',grpIdArr,grpNameArr);		
	} else if(selectedOption == 'users') {
		constructSelectOptions('users',userIdArr,userNameArr);		
	}
}

function constructSelectOptions(selectedMemberType,idArr,nameArr)
{
	var i;
	var findStr=document.NewReport.findStr.value;
	if(findStr.replace(/^\s+/g, '').replace(/\s+$/g, '').length !=0)
	{
		var k=0;
		for(i=0; i<nameArr.length; i++)
		{
			if(nameArr[i].indexOf(findStr) ==0)
			{
				constructedOptionName[k]=nameArr[i];
				constructedOptionValue[k]=idArr[i];
				k++;			
			}
		}
	}
	else
	{
		constructedOptionValue = idArr;
		constructedOptionName = nameArr;	
	}
	
	//Constructing the selectoptions
	var j;
	var nowNamePrefix;	
	for(j=0;j<constructedOptionName.length;j++)
	{
		if(selectedMemberType == 'groups') {
			nowNamePrefix = 'Group::'
		} else if(selectedMemberType == 'users') {
			nowNamePrefix = 'User::'
		}

		var nowName = nowNamePrefix + constructedOptionName[j];
		var nowId = selectedMemberType + '::'  + constructedOptionValue[j]
		document.forms['NewReport'].availableList.options[j] = new Option(nowName,nowId);	
	}
	
	//clearing the array
	constructedOptionValue = new Array();
    constructedOptionName = new Array();	
}

function set_Objects() {
	availableListObj=getObj("availableList")
	columnsSelectedObj=getObj("columnsSelected")
}

function addColumns() 
        {
            for (i=0;i<columnsSelectedObj.length;i++) 
            {
                columnsSelectedObj.options[i].selected=false
            }

            for (i=0;i<availableListObj.length;i++) 
            {
                if (availableListObj.options[i].selected==true) 
                {            	
                	var rowFound=false;
                	var existingObj=null;
                    for (j=0;j<columnsSelectedObj.length;j++) 
                    {
                        if (columnsSelectedObj.options[j].value==availableListObj.options[i].value) 
                        {
                            rowFound=true
                            existingObj=columnsSelectedObj.options[j]
                            break
                        }
                    }

                    if (rowFound!=true) 
                    {
                        var newColObj=document.createElement("OPTION")
                        newColObj.value=availableListObj.options[i].value
                        if (browser_ie) newColObj.innerText=availableListObj.options[i].innerText
                        else if (browser_nn4 || browser_nn6) newColObj.text=availableListObj.options[i].text
                        columnsSelectedObj.appendChild(newColObj)
                        availableListObj.options[i].selected=false
                        newColObj.selected=true
                        rowFound=false
                    } 
                    else 
                    {
                        availableListObj.options[i].selected=false
                        if(existingObj != null) existingObj.selected=true
                    }
                }
            }
        }

function removeColumns() 
{
	for (i=columnsSelectedObj.options.length;i>0;i--) 
	{
		if (columnsSelectedObj.options.selectedIndex>=0)
			columnsSelectedObj.remove(columnsSelectedObj.options.selectedIndex)
	}
}

function formSelectedColumnString()
{
	var selectedColStr = "";
	for (i=0;i<columnsSelectedObj.options.length;i++) 
	{
		selectedColStr += columnsSelectedObj.options[i].value + ";";
	}
	document.NewReport.selectedColumnsStr.value = selectedColStr;
}

set_Objects();
show_Options();
</script>

<script language="JavaScript" type="text/JavaScript">    
var moveupLinkObj,moveupDisabledObj,movedownLinkObj,movedownDisabledObj;

</script>
{/literal}

