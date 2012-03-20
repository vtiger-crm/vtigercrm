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
{literal}
<style>
DIV.fixedLay{
	border:3px solid #CCCCCC;
	background-color:#FFFFFF;
	width:500px;
	position:fixed;
	left:250px;
	top:98px;
	display:block;
}
</style>
{/literal}
{literal}
<!--[if lte IE 6]>
<STYLE type=text/css>
DIV.fixedLay {
	POSITION: absolute;
}
</STYLE>
<![endif]-->

{/literal}
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>

	<div align=center>
			{include file="SetMenu.tpl"}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'shareaccess.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" width="48" height="48" border=0 title="{$MOD.LBL_USERS}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_SHARING_ACCESS} </b></td>
					<td rowspan=2 class="small" align=right>&nbsp;</td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_SHARING_ACCESS_DESCRIPTION}</td>
				</tr>
				</table>

				<br>
				<div class='helpmessagebox' style='margin-bottom: 4px;'>
					<b style='color: red;'>{$APP.NOTE}</b> {$MOD.LBL_SHARING_ACCESS_HELPNOTE}
				</div>				
			  	<!-- GLOBAL ACCESS MODULE -->
		  		<div id="globaldiv">
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<form action="index.php" method="post" name="new" id="orgSharingform" onsubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="module" value="Users">
				<input type="hidden" name="action" value="OrgSharingEditView">
				<input type="hidden" name="parenttab" value="Settings">
				<tr>
					<td class="big"><strong>1. {$CMOD.LBL_GLOBAL_ACCESS_PRIVILEGES}</strong></td>
					<td class="small" align=right>
						<input class="crmButton small cancel" title="{$CMOD.LBL_RECALCULATE_BUTTON}"  type="button" name="recalculate" value="{$CMOD.LBL_RECALCULATE_BUTTON}" onclick="return freezeBackground();">	
	&nbsp;<input class="crmButton small edit" type="submit" name="Edit" value="{$CMOD.LBL_CHANGE} {$CMOD.LBL_PRIVILEGES}" ></td>
					</td>
				</tr>
				</table>
				<table cellspacing="0" cellpadding="5" class="listTable" width="100%">
				{foreach item=module from=$DEFAULT_SHARING}	
				  {assign var="MODULELABEL" value=$module.0}
				  {if $APP[$module.0] neq ''}
					{assign var="MODULELABEL" value=$APP[$module.0]}
				  {/if}	
                  <tr>
                    <td width="20%" class="colHeader small" nowrap>{$MODULELABEL}</td>
                    <td width="30%" class="listTableRow small" nowrap>
					{if $module.1 neq 'Private' && $module.1 neq 'Hide Details'}
						<img src="{'public.gif'|@vtiger_imageurl:$THEME}" align="absmiddle">
					{else}
						<img src="{'private.gif'|@vtiger_imageurl:$THEME}" align="absmiddle">
					{/if}
						{$CMOD[$module.1]}
		    		</td>
                    <td width="50%" class="listTableRow small" nowrap>{$module.2}</td>
                  </tr>
		  		{/foreach}
		</form>	
              </table>
		</div>	
		  <!-- END OF GLOBAL -->
				<br><br>
		  <!-- Custom Access Module Display Table -->
		  <div id="customdiv">
			
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
					<td class="big"><strong>2. {$CMOD.LBL_CUSTOM_ACCESS_PRIVILEGES}</strong></td>
					<td class="small" align=right>&nbsp;</td>
				</tr>
				</table>
				<!-- Start of Module Display -->
				{foreach  key=modulename item=details from=$MODSHARING}
				{assign var="MODULELABEL" value=$modulename|@getTranslatedString:$modulename}
				{assign var="mod_display" value=$MODULELABEL}
				{if $mod_display eq $APP.Accounts}
					{assign var="xx" value=$APP.Contacts}
					{assign var="mod_display" value=$mod_display|cat:" & $xx"}
				{/if}
				{if $details.0 neq ''}
				<table width="100%" border="0" cellpadding="5" cellspacing="0" class="listTableTopButtons">
                  		<tr>
		                    <td  style="padding-left:5px;" class="big"><img src="{'arrow.jpg'|@vtiger_imageurl:$THEME}" width="19" height="21" align="absmiddle" />&nbsp; <b>{$mod_display}</b>&nbsp; </td>
                		    <td align="right">
					<input class="crmButton small save" type="button" name="Create" value="{$CMOD.LBL_ADD_PRIVILEGES_BUTTON}" onClick="callEditDiv(this,'{$modulename}','create','')">
				    </td>
                  		</tr>
			  	</table>
				<table width="100%" cellpadding="5" cellspacing="0" class="listTable" >
                    		<tr>
                    		<td width="7%" class="colHeader small" nowrap>{$CMOD.LBL_RULE_NO}</td>
                          	<td width="20%" class="colHeader small" nowrap>{$mod_display} {$CMOD.LBL_OF}</td>
                          	<td width="25%" class="colHeader small" nowrap>{$CMOD.LBL_CAN_BE_ACCESSED}</td>
                          	<td width="40%" class="colHeader small" nowrap>{$CMOD.LBL_PRIVILEGES}</td>
                          	<td width="8%" class="colHeader small" nowrap>{$APP.Tools}</td>
                        	</tr>
                        <tr >
			  {foreach key=sno item=elements from=$details}
                          <td class="listTableRow small">{$sno+1}</td>
                          <td class="listTableRow small">{$elements.1}</td>
                          <td class="listTableRow small">{$elements.2}</td>
                          <td class="listTableRow small">{$elements.3}</td>
                          <td align="center" class="listTableRow small">
				<a href="javascript:void(0);" onClick="callEditDiv(this,'{$modulename}','edit','{$elements.0}')"><img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" title='edit' align="absmiddle" border=0 style="padding-top:3px;"></a>&nbsp;|<a href='javascript:confirmdelete("index.php?module=Users&action=DeleteSharingRule&shareid={$elements.0}")'><img src="{'delete.gif'|@vtiger_imageurl:$THEME}" title='del' align="absmiddle" border=0></a></td>
                        </tr>

                     {/foreach} 
                    </table>
	<!-- End of Module Display -->
	<!-- Start FOR NO DATA -->

			<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
			<tr><td>&nbsp;</td></tr>
			</table>
		    {else}
                    <table width="100%" cellpadding="0" cellspacing="0" class="listTable"><tr><td>
		      <table width="100%" border="0" cellpadding="5" cellspacing="0" class="listTableTopButtons">
                      <tr>
                        <td  style="padding-left:5px;" class="big"><img src="{'arrow.jpg'|@vtiger_imageurl:$THEME}" width="19" height="21" align="absmiddle" />&nbsp; <b>{$mod_display}</b>&nbsp; </td>
                        <td align="right">
				<input class="crmButton small save" type="button" name="Create" value="{$APP.LBL_ADD_ITEM} {$CMOD.LBL_PRIVILEGES}" onClick="callEditDiv(this,'{$modulename}','create','')">
			</td>
                      </tr>
			<table width="100%" cellpadding="5" cellspacing="0">
			<tr>
			<td colspan="2"  style="padding:20px ;" align="center" class="small">
			   {$CMOD.LBL_CUSTOM_ACCESS_MESG} 
			   <a href="javascript:void(0);" onClick="callEditDiv(this,'{$modulename}','create','')">{$CMOD.LNK_CLICK_HERE}</a>
			   {$CMOD.LBL_CREATE_RULE_MESG}
			</td>
			</tr>
		    </table>
		    </table>	
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
			<tr><td>&nbsp;</td></tr>
			</table>
		    {/if}
		    {/foreach}			
		   </td></tr></table>
				<br>
		   </div>	
				<!-- Edit Button -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% >
				<tr><td class="small" ><div align=right><a href="#top">{$MOD.LBL_SCROLL}</a></div></td></tr>				</table>
		 </td>
                  </tr>
				
                </table>
			
			
			
			</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
		
	</div>
</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</table>
<div id="tempdiv" style="display:block;position:absolute;width:400px;"></div>

<!-- For Disabling Window -->
<div id="confId"  class='veil_new small' style="display:none;">
<table class="options small" border="0" cellpadding="18" cellspacing="0">
<tr>
	<td align="center" nowrap style="color:#FFFFFF;font-size:15px;">
		<b>{$CMOD.LBL_RECALC_MSG}</b>
	</td>
	<br>
	<tr>
		<td align="center"><input type="button" value="{$CMOD.LBL_YES}" onclick="return disableStyle('confId');">&nbsp;&nbsp;<input type="button" value="&nbsp;{$CMOD.LBL_NO}&nbsp;" onclick="showSelect();$('confId').style.display='none';document.body.removeChild($('freeze'));"></td>
	</tr>
</tr>
</table>
</div>

<div id="divId" class="veil_new" style="position:absolute;width:100%;display:none;top:0px;left:0px;">
<table border="5" cellpadding="0" cellspacing="0" align="center" style="vertical-align:middle;width:100%;height:100%;">
<tbody><tr>
		<td class="big" align="center">
		    <img src="{'plsWaitAnimated.gif'|@vtiger_imageurl:$THEME}">
		</td>
	</tr>
</tbody>
</table>
</div>


<script>
function callEditDiv(obj,modulename,mode,id)
{ldelim}
        $("status").style.display="inline";
        new Ajax.Request(
                'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                        method: 'post',
                        postBody: 'module=Settings&action=SettingsAjax&orgajax=true&mode='+mode+'&sharing_module='+modulename+'&shareid='+id,
                        onComplete: function(response) {ldelim}
                                $("status").style.display="none";
                                $("tempdiv").innerHTML=response.responseText;
				fnvshobj(obj,"tempdiv");
                                if(mode == 'edit')
                                {ldelim}
                                        setTimeout("",10000);
                                        var related = $('rel_module_lists').value;
                                        fnwriteRules(modulename,related);
                                {rdelim}
                        {rdelim}
                {rdelim}
        );
{rdelim}

function fnwriteRules(module,related)
{ldelim}
		var modulelists = new Array();
		modulelists = related.split('###');
		var relatedstring ='';
		var relatedtag;
		var relatedselect;
		var modulename;
		for(i=0;i < modulelists.length-1;i++)
		{ldelim}
			modulename = modulelists[i]+"_accessopt";
			relatedtag = document.getElementById(modulename);
			relatedselect = relatedtag.options[relatedtag.selectedIndex].text;
			relatedstring += modulelists[i]+':'+relatedselect+' ';
		{rdelim}	
		var tagName = document.getElementById(module+"_share");
		var tagName2 = document.getElementById(module+"_access");
		var tagName3 = document.getElementById('share_memberType');
		var soucre =  document.getElementById("rules");
		var soucre1 =  document.getElementById("relrules");
		var select1 = tagName.options[tagName.selectedIndex].text;
		var select2 = tagName2.options[tagName2.selectedIndex].text;
		var select3 = tagName3.options[tagName3.selectedIndex].text;

		if(module == '{$APP.Accounts}')
		{ldelim}
			module = '{$APP.Accounts} & {$APP.Contacts}';	
		{rdelim}

		soucre.innerHTML = module +" {$APP.LBL_LIST_OF} <b>\"" + select1 + "\"</b> {$CMOD.LBL_CAN_BE_ACCESSED} <b>\"" +select2 + "\"</b> {$CMOD.LBL_IN_PERMISSION} "+select3;
		soucre1.innerHTML = "<b>{$CMOD.LBL_RELATED_MODULE_RIGHTS}</b> " + relatedstring;
{rdelim}


		function confirmdelete(url)
		{ldelim}
			if(confirm("{$APP.ARE_YOU_SURE}"))
			{ldelim}
				document.location.href=url;
			{rdelim}
		{rdelim}
	
	function disableStyle(id)
	{ldelim}
			$('orgSharingform').action.value = 'RecalculateSharingRules';
			$('orgSharingform').submit();
 			$(id).style.display = 'none';

			if(browser_ie && (gBrowserAgent.indexOf("msie 7.")!=-1))//for IE 7
                        {ldelim}
                                document.body.removeChild($('freeze'));
                        {rdelim}else if(browser_ie)
                        {ldelim}
                             var oDivfreeze = $('divId');
                             oDivfreeze.style.height = document.documentElement['clientHeight'] + 'px';

                        {rdelim}
                        $('divId').style.display = 'block';
	{rdelim}

	function freezeBackground()
	{ldelim}
	    var oFreezeLayer = document.createElement("DIV");
	    oFreezeLayer.id = "freeze";
	    oFreezeLayer.className = "small veil";

	     if (browser_ie) oFreezeLayer.style.height = (document.body.offsetHeight + (document.body.scrollHeight - document.body.offsetHeight)) + "px";
	     else if (browser_nn4 || browser_nn6) oFreezeLayer.style.height = document.body.offsetHeight + "px";

	    oFreezeLayer.style.width = "100%";
	    document.body.appendChild(oFreezeLayer);
	    document.getElementById('confId').style.display = 'block';
	    hideSelect();
	{rdelim}

</script>
