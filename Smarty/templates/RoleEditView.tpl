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
	var rolename = $('rolename').value;
	var mode = getObj('mode').value;
	var roleid = getObj('roleid').value;
	if(mode == 'edit')
		var urlstring ="&mode="+mode+"&roleName="+rolename+"&roleid="+roleid;
	else
		var urlstring ="&roleName="+rolename;
	//var status = CharValidation(rolename,'namespace');
	//if(status)
	//{ldelim}
	new Ajax.Request(
                'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                                method: 'post',
                                postBody: 'module=Settings&action=SettingsAjax&file=SaveRole&ajax=true&dup_check=true'+urlstring,
                                onComplete: function(response) {ldelim}
					if(response.responseText.indexOf('SUCCESS') > -1)
						document.newRoleForm.submit();
					else
						alert(response.responseText);
                                {rdelim}
                        {rdelim}
                );
	//{rdelim}
	//else
	//	alert(alert_arr.NO_SPECIAL+alert_arr.IN_ROLENAME)

{rdelim}
function validate()
{ldelim}
	formSelectColumnString();
	if( !emptyCheck("roleName", "Role Name", "text" ) )
		return false;

	if(document.newRoleForm.selectedColumnsString.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{ldelim}

		alert('{$APP.ROLE_SHOULDHAVE_INFO}');
		return false;
	{rdelim}
	dup_validation();return false
{rdelim}
</script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>

	<div align=center>
			{include file='SetMenu.tpl'}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				{literal}
				<form name="newRoleForm" action="index.php" method="post" onSubmit="if(validate()) { VtigerJS_DialogBox.block();} else { return false;} ">
				{/literal}
				<input type="hidden" name="module" value="Settings">
				<input type="hidden" name="action" value="SaveRole">
				<input type="hidden" name="parenttab" value="Settings">
				<input type="hidden" name="returnaction" value="{$RETURN_ACTION}">
				<input type="hidden" name="roleid" value="{$ROLEID}">
				<input type="hidden" name="mode" value="{$MODE}">
				<input type="hidden" name="parent" value="{$PARENT}">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'ico-roles.gif'|@vtiger_imageurl:$THEME}" alt="{$CMOD.LBL_ROLES}" width="48" height="48" border=0 title="{$CMOD.LBL_ROLES}"></td>
					{if $MODE eq 'edit'}
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=listroles&parenttab=Settings">{$CMOD.LBL_ROLES}</a> &gt; {$MOD.LBL_EDIT} &quot;{$ROLENAME}&quot; </b></td>
					{else}	
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=listroles&parenttab=Settings">{$CMOD.LBL_ROLES}</a> &gt; {$CMOD.LBL_CREATE_NEW_ROLE}</b></td>
					{/if}
				</tr>
				<tr>
					{if $MODE eq 'edit'}
					<td valign=top class="small">{$MOD.LBL_EDIT} {$CMOD.LBL_PROPERTIES} &quot;{$ROLENAME}&quot; {$MOD.LBL_LIST_CONTACT_ROLE}</td>
					{else}
					<td valign=top class="small">{$CMOD.LBL_NEW_ROLE}</td>
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
						<td class="big"><strong>{$CMOD.LBL_PROPERTIES} &quot;{$ROLENAME}&quot; </strong></td>
						{else}
						<td class="big"><strong>{$CMOD.LBL_NEW_ROLE}</strong></td>
						{/if}
						<td><div align="right">
							<input type="button" class="crmButton small save" name="add" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " onClick="return validate()">
						
						<input type="button" class="crmButton cancel small" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onClick="window.history.back()">
						</div></td>
					  </tr>
					</table>
					<table width="100%"  border="0" cellspacing="0" cellpadding="5">
                      <tr class="small">
                        <td width="15%" class="small cellLabel"><font color="red">*</font><strong>{$CMOD.LBL_ROLE_NAME}</strong></td>
                        <td width="85%" class="cellText" ><input id="rolename" name="roleName" type="text" value="{$ROLENAME}" class="detailedViewTextBox"></td>
                      </tr>
                      <tr class="small">
                        <td class="small cellLabel"><strong>{$CMOD.LBL_REPORTS_TO}</strong></td>
                        <td class="cellText">{$PARENTNAME}</td>
                      </tr>
                      <tr class="small">
                        <td colspan="2" valign=top class="cellLabel"><strong>{$CMOD.LBL_PROFILE_M}</strong>						</td>
                      </tr>
                      <tr class="small">
                        <td colspan="2" valign=top class="cellText"> 
						<br>
						<table width="95%"  border="0" align="center" cellpadding="5" cellspacing="0">
                          <tr>
                            <td width="40%" valign=top class="cellBottomDotLinePlain small"><strong>{$CMOD.LBL_PROFILES_AVLBL}</strong></td>
                            <td width="10%">&nbsp;</td>
                            <td width="40%" class="cellBottomDotLinePlain small"><strong>{$CMOD.LBL_ASSIGN_PROFILES}</strong></td>
                          </tr>

			<tr class=small>
	 	               <td valign=top>{$CMOD.LBL_PROFILES_M} {$CMOD.LBL_MEMBER} <br>
				<select multiple id="availList" name="availList" class="small crmFormList" size=10 >
																				{foreach item=element from=$PROFILELISTS}
																					<option value="{$element.0}">{$element.1}</option>
																				{/foreach}
				</select>
				</td>
                        	<td width="50"><div align="center">
																				<input type="hidden" name="selectedColumnsString"/>
																					<input name="Button" value="&nbsp;&rsaquo;&rsaquo;&nbsp;" type="button" class="crmButton small" style="width:100%" onClick="addColumn()">
                                  <br>
                                  <br>
																					<input type="button" name="Button1" value="&nbsp;&lsaquo;&lsaquo;&nbsp;" class="crmButton small" onClick="delColumn()" style="width:100%">
				  <br>
				  <br>
                            	</div></td>
                            <td class="small" style="background-color:#ddFFdd" valign=top>{$CMOD.LBL_MEMBER} of &quot;{$ROLENAME}&quot; <br>
                             <select multiple id="selectedColumns" name="selectedColumns" class="small crmFormList" size=10 >
																			{foreach item=element from=$SELPROFILELISTS}
																				<option value="{$element.0}">{$element.1}</option>
																			{/foreach}
                	    </select></td>
                       </tr>
						  
                        </table>
						
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
					
					
				</td>
				</tr>
				<tr>
				  <td valign=top>&nbsp;</td>
				  </tr>
				</table>
			
			
			
			</td>
			</tr>
			</table>
		</td>
	</tr>
	</form>
	</table>
		
	</div>

</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
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
            document.newRoleForm.selectedColumnsString.value = selectedColStr;
        {rdelim}
	setObjects();			
</script>	
