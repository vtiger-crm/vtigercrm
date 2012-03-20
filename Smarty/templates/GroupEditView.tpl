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
<script language="javascript">

function dup_validation()
{ldelim}
	var mode = getObj('mode').value;
	var groupname = $('groupName').value;
	var groupid = getObj('groupId').value;
	if(mode == 'edit')
		var reminstr = '&mode='+mode+'&groupName='+groupname+'&groupid='+groupid;
	else
		var reminstr = '&groupName='+groupname;
	VtigerJS_DialogBox.block();
	//var status = CharValidation(groupname,'namespace');
	//if(status)
	//{ldelim}
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=Users&action=UsersAjax&file=SaveGroup&ajax=true&dup_check=true'+reminstr,
			onComplete: function(response) {ldelim}
				if(response.responseText.indexOf('SUCCESS') >-1)
					document.newGroupForm.submit();
				else {ldelim}
					VtigerJS_DialogBox.unblock();
					alert(response.responseText);
				{rdelim}
			{rdelim}
		{rdelim}
		);
	//{rdelim}
	//else
	//	alert(alert_arr.NO_SPECIAL+alert_arr.IN_GROUPNAME)
{rdelim}
var constructedOptionValue;
var constructedOptionName;

var roleIdArr=new Array({$ROLEIDSTR});
var roleNameArr=new Array({$ROLENAMESTR});
var userIdArr=new Array({$USERIDSTR});
var userNameArr=new Array({$USERNAMESTR});
var grpIdArr=new Array({$GROUPIDSTR});
var grpNameArr=new Array({$GROUPNAMESTR});

function showOptions()
{ldelim}
	var selectedOption=document.newGroupForm.memberType.value;
	//Completely clear the select box
	document.forms['newGroupForm'].availList.options.length = 0;

	if(selectedOption == 'groups')
	{ldelim}
		constructSelectOptions('groups',grpIdArr,grpNameArr);		
	{rdelim}
	else if(selectedOption == 'roles')
	{ldelim}
		constructSelectOptions('roles',roleIdArr,roleNameArr);		
	{rdelim}
	else if(selectedOption == 'rs')
	{ldelim}
	
		constructSelectOptions('rs',roleIdArr,roleNameArr);	
	{rdelim}
	else if(selectedOption == 'users')
	{ldelim}
		constructSelectOptions('users',userIdArr,userNameArr);		
	{rdelim}
{rdelim}

function constructSelectOptions(selectedMemberType,idArr,nameArr)
{ldelim}
	var i;
	var findStr=document.newGroupForm.findStr.value;
	if(findStr.replace(/^\s+/g, '').replace(/\s+$/g, '').length !=0)
	{ldelim}
		
		var k=0;
		for(i=0; i<nameArr.length; i++)
		{ldelim}
			if(nameArr[i].indexOf(findStr) ==0)
			{ldelim}
				constructedOptionName[k]=nameArr[i];
				constructedOptionValue[k]=idArr[i];
				k++;			
			{rdelim}
		{rdelim}
	{rdelim}
	else
	{ldelim}
		constructedOptionValue = idArr;
		constructedOptionName = nameArr;	
	{rdelim}
	
	//Constructing the selectoptions
	var j;
	var nowNamePrefix;	
	for(j=0;j<constructedOptionName.length;j++)
	{ldelim}
		if(selectedMemberType == 'roles')
		{ldelim}
			nowNamePrefix = 'Roles::'
		{rdelim}
		else if(selectedMemberType == 'rs')
		{ldelim}
			nowNamePrefix = 'RoleAndSubordinates::'
		{rdelim}
		else if(selectedMemberType == 'groups')
		{ldelim}
			nowNamePrefix = 'Group::'
		{rdelim}
		else if(selectedMemberType == 'users')
		{ldelim}
			nowNamePrefix = 'User::'
		{rdelim}

		var nowName = nowNamePrefix + constructedOptionName[j];
		var nowId = selectedMemberType + '::'  + constructedOptionValue[j]
		document.forms['newGroupForm'].availList.options[j] = new Option(nowName,nowId);	
	{rdelim}
	//clearing the array
	constructedOptionValue = new Array();
        constructedOptionName = new Array();	
				

{rdelim}

function validate()
{ldelim}
	formSelectColumnString();
	if( !emptyCheck( "groupName", "Group Name","text" ) )
		return false;
	//check to restrict the & < > , characters
	var str = $("groupName").value;
	var re1=/[&\<\>\,]/
        if (re1.test(str))
        {ldelim}
                alert(alert_arr.SPECIAL_CHARACTERS+" & < > , "+alert_arr.NOT_ALLOWED)
                return false;
        {rdelim}
	
	if(document.newGroupForm.selectedColumnsString.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{ldelim}

		alert('{$APP.GROUP_SHOULDHAVE_ONEMEMBER_INFO}');
		return false;
	{rdelim}
	dup_validation();return false;	
{rdelim}
</script>

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>

	<div align=center>
		{include file='SetMenu.tpl'}
		<!-- DISPLAY -->
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
		{literal}
		<form name="newGroupForm" action="index.php" method="post" onSubmit="if(validate()) { VtigerJS_DialogBox.block();} else { return false; }">
		{/literal}
		<input type="hidden" name="module" value="Users">
		<input type="hidden" name="action" value="SaveGroup">
		<input type="hidden" name="mode" value="{$MODE}">
		<input type="hidden" name="parenttab" value="Settings">
		<input type="hidden" name="groupId" value="{$GROUPID}">
		<input type="hidden" name="returnaction" value="{$RETURN_ACTION}">
			<tr>
				<td width=50 rowspan=2 valign=top><img src="{'ico-groups.gif'|@vtiger_imageurl:$THEME}" alt="{$CMOD.LBL_GROUPS}" title="{$CMOD.LBL_GROUPS}" width="48" height="48" border=0 ></td>
				{if $MODE eq 'edit'}
				<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=listgroups&parenttab=Settings">{$CMOD.LBL_GROUPS}</a> &gt; {$MOD.LBL_EDIT} &quot;{$GROUPNAME}&quot; </b></td>
				{else}	
				<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=listgroups&parenttab=Settings">{$CMOD.LBL_GROUPS}</a> &gt; {$CMOD.LBL_CREATE_NEW_GROUP}</b></td>
				{/if}
			</tr>
			<tr>
				{if $MODE eq 'edit'}
				<td valign=top class="small">{$MOD.LBL_EDIT} {$CMOD.LBL_PROPERTIES} &quot;{$GROUPNAME}&quot; {$CMOD.LBL_GROUP}</td>
				{else}
				<td valign=top class="small">{$CMOD.LBL_NEW_GROUP}</td>
				{/if}
			</tr>
		</table>
		
		<br>
		<table border=0 cellspacing=0 cellpadding=10 width=100% >
			<tr>
				<td valign=top>
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
						<tr>
							{if $MODE eq 'edit'}
							<td class="big"><strong>{$CMOD.LBL_PROPERTIES} &quot;{$GROUPNAME}&quot; </strong></td>
							{else}
							<td class="big"><strong>{$CMOD.LBL_NEW_GROUP}</strong></td>
							{/if}
							<td>
								<div align="right">
								{if $MODE eq 'edit'}
								<input type="submit" class="crmButton small save" name="add" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " onClick="return validate()">
								{else}
								<input type="submit" class="crmButton create small" name="add" value="{$CMOD.LBL_ADD_GROUP_BUTTON}" onClick="return validate()">
								{/if}
								&nbsp;
								<input type="button" class="crmButton cancel small" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onClick="window.history.back()">
								</div>
							</td>
					  	</tr>
					</table>
					<table width="100%"  border="0" cellspacing="0" cellpadding="5">
                      				<tr class="small">
							<td width="15%" class="small cellLabel"><font color="red">*</font><strong>{$CMOD.LBL_GROUP_NAME}</strong></td>
							<td width="85%" class="cellText" ><input id="groupName" name="groupName" type="text" value="{$GROUPNAME}" class="detailedViewTextBox"></td>
						</tr>
						<tr class="small">
							<td class="small cellLabel"><strong>{$CMOD.LBL_DESCRIPTION}</strong></td>
							<td class="cellText"><input name="description" type="text" value="{$DESCRIPTION}" class="detailedViewTextBox"> </td>
						</tr>
                      				<tr class="small">
							<td colspan="2" valign=top class="cellLabel"><strong>{$CMOD.LBL_MEMBER}</strong></td>
                      				</tr>
						<tr class="small">
							<td colspan="2" valign=top class="cellText">
							<br>
								<table width="95%"  border="0" align="center" cellpadding="5" cellspacing="0">
								<tr>
									<td width="40%" valign=top class="cellBottomDotLinePlain small"><strong>{$CMOD.LBL_MEMBER_AVLBL}</strong></td>
									<td width="10%">&nbsp;</td>
									<td width="40%" class="cellBottomDotLinePlain small"><strong>{$CMOD.LBL_MEMBER_SELECTED}</strong></td>
								</tr>
								<tr>
									<td valign=top class="small">
										{$CMOD.LBL_ENTITY}:&nbsp;
										<select id="memberType" name="memberType" class="small" onchange="showOptions()">
										<option value="groups" selected>{$CMOD.LBL_GROUPS}</option>
										<option value="roles">{$CMOD.LBL_ROLES}</option>
										<option value="rs">{$CMOD.LBL_ROLES_SUBORDINATES}</option>
										<option value="users">{$MOD.LBL_USERS}</option>
										</select>
										<input type="hidden" name="findStr" class="small">&nbsp;
									</td>
									<td width="50">&nbsp;</td>
									<td class="small">&nbsp;</td>
								</tr>
                          					<tr class=small>
									<td valign=top>{$CMOD.LBL_MEMBER} {$CMOD.LBL_OF} {$CMOD.LBL_ENTITY}<br>
										<select id="availList" name="availList" multiple size="10" class="small crmFormList"></select>
										<input type="hidden" name="selectedColumnsString"/>
							
									</td>
									<td width="50">
										<div align="center">
											<input type="button" name="Button" value="&nbsp;&rsaquo;&rsaquo;&nbsp;" onClick="addColumn()" class="crmButton small"/><br /><br />
											<input type="button" name="Button1" value="&nbsp;&lsaquo;&lsaquo;&nbsp;" onClick="delColumn()" class="crmButton small"/>
										</div>
									</td>
									<td class="small" style="background-color:#ddFFdd" valign=top>{$CMOD.LBL_MEMBER} {$CMOD.LBL_OF} &quot;{$GROUPNAME}&quot; <br>
										<select id="selectedColumns" name="selectedColumns" multiple size="10" class="small crmFormList">
										{foreach item=element from=$MEMBER}
										<option value="{$element.0}">{$element.1}</option>
										{/foreach}
										</select>
									</td>
								</tr>
								<tr>
									<td colspan=3>
										<ul class=small>
											<li>{$CMOD.LBL_GROUP_MESG1}</li>
											<li>{$CMOD.LBL_GROUP_MESG2}</li>
											<li>{$CMOD.LBL_GROUP_MESG3}</li>
										</ul>
									</td>
								</tr>
								</table>
							</td>
						</tr>
					</table>
					<br>
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr><td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
					</table>
				</td></tr></table>
				</td></tr></table>
				</td>
			</tr>
		</form>
		</table>
	</div>

	</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
</tr>
</table>
<script language="JavaScript" type="text/JavaScript">    
var moveupLinkObj,moveupDisabledObj,movedownLinkObj,movedownDisabledObj;
function setObjects() 
{ldelim}
availListObj=getObj("availList")
selectedColumnsObj=getObj("selectedColumns")

{rdelim}

function addColumn() 
        {ldelim}
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
for (i=selectedColumnsObj.options.length;i>0;i--) 
{ldelim}
	if (selectedColumnsObj.options.selectedIndex>=0)
selectedColumnsObj.remove(selectedColumnsObj.options.selectedIndex)
{rdelim}
{rdelim}

function formSelectColumnString()
{ldelim}
var selectedColStr = "";
for (i=0;i<selectedColumnsObj.options.length;i++) 
{ldelim}
selectedColStr += selectedColumnsObj.options[i].value + ";";
{rdelim}
document.newGroupForm.selectedColumnsString.value = selectedColStr;
{rdelim}
setObjects();
showOptions();
</script>
