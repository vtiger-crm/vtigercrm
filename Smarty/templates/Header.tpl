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
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>{$CURRENT_USER} - {$APP.$CATEGORY} - {$APP.$MODULE_NAME} - {$APP.LBL_BROWSER_TITLE}</title>
	<link REL="SHORTCUT ICON" HREF="themes/images/vtigercrm_icon.ico">	
	<style type="text/css">@import url("themes/{$THEME}/style.css");</style>
	<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
	{* vtlib customization: Inclusion of custom javascript and css as registered *}
	{if $HEADERCSS}
		<!-- Custom Header CSS -->
		{foreach item=HDRCSS from=$HEADERCSS}
			<link rel="stylesheet" type="text/css" href="{$HDRCSS->linkurl}"></script>
		{/foreach}
		<!-- END -->
	{/if}
	{* END *}
	<!-- ActivityReminder customization for callback -->
	{literal}
	<style type="text/css">div.fixedLay1 { position:fixed; }</style>
	<!--[if lte IE 6]>
	<style type="text/css">div.fixedLay { position:absolute; }</style>
	<![endif]-->
	{/literal}
	<!-- End -->
</head>
	<body leftmargin=0 topmargin=0 marginheight=0 marginwidth=0 class=small>
	<a name="top"></a>
	<!-- header -->
	<!-- header-vtiger crm name & RSS -->
	<script language="JavaScript" type="text/javascript" src="include/js/json.js"></script>
	<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
	<!-- vtlib customization: Javascript hook -->	
	<script language="JavaScript" type="text/javascript" src="include/js/vtlib.js"></script>
	<!-- END -->
	<script language="JavaScript" type="text/javascript" src="include/js/{php} echo $_SESSION['authenticated_user_language'];{/php}.lang.js?{php} echo $_SESSION['vtiger_version'];{/php}"></script>
	<script language="JavaScript" type="text/javascript" src="include/js/QuickCreate.js"></script>
	<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>
	<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
	<script language="JavaScript" type="text/javascript" src="include/calculator/calc.js"></script>
	<script language="JavaScript" type="text/javascript" src="modules/Calendar/script.js"></script>
	<script language="javascript" type="text/javascript" src="include/scriptaculous/dom-drag.js"></script>
	<script language="JavaScript" type="text/javascript" src="include/js/notificationPopup.js"></script>
        <script type="text/javascript" src="jscalendar/calendar.js"></script>
        <script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
        <script type="text/javascript" src="jscalendar/lang/calendar-{$APP.LBL_JSCALENDAR_LANG}.js"></script>
        
    <!-- asterisk Integration -->
    {if $USE_ASTERISK eq 'true'}
    	<script type="text/javascript" src="include/js/asterisk.js"></script>
    	<script type="text/javascript">
    	if(typeof(use_asterisk) == 'undefined') use_asterisk = true;
    	</script>
    {/if}
    <!-- END -->

	{* vtlib customization: Inclusion of custom javascript and css as registered *}
	{if $HEADERSCRIPTS}
		<!-- Custom Header Script -->
		{foreach item=HEADERSCRIPT from=$HEADERSCRIPTS}
			<script type="text/javascript" src="{$HEADERSCRIPT->linkurl}"></script>
		{/foreach}
		<!-- END -->
	{/if}
	{* END *}
	
	{* PREFECTHING IMAGE FOR BLOCKING SCREEN USING VtigerJS_DialogBox API *}
    <img src="{'layerPopupBg.gif'|@vtiger_imageurl:$THEME}" style="display: none;"/>
    {* END *}

	<TABLE border=0 cellspacing=0 cellpadding=0 width=100% class="hdrNameBg">
	<tr>
		<td valign=top><img src="{'vtiger-crm.gif'|@vtiger_imageurl:$THEME}" alt="vtiger CRM" title="vtiger CRM" border=0></td>
		<td width=100% align=center>
		{if $APP.$MODULE_NAME eq 'Dashboards'}
		<marquee id="rss" direction="left" scrolldelay="10" scrollamount="3" behavior="scroll" class="marStyle" onMouseOver="javascript:stop();" onMouseOut="javascript:start();">&nbsp;{$ANNOUNCEMENT|escape}</marquee>
		{else}
                <marquee id="rss" direction="left" scrolldelay="10" scrollamount="3" behavior="scroll" class="marStyle" onMouseOver="javascript:stop();" onMouseOut="javascript:start();">&nbsp;{$ANNOUNCEMENT}</marquee>
                {/if}		
		</td>
		<td class=small nowrap>
			<table border=0 cellspacing=0 cellpadding=0>
			 <tr>
			
			{* vtlib customization: Header links on the top panel *}
			{if $HEADERLINKS}
			<td style="padding-left:10px;padding-right:5px" class=small nowrap>
				<a href="javascript:;" onmouseover="fnvshobj(this,'vtlib_headerLinksLay');" onclick="fnvshobj(this,'vtlib_headerLinksLay');">{$APP.LBL_MORE}</a> <img src="{'arrow_down.gif'|@vtiger_imageurl:$THEME}" border=0>
				<div style="display: none; left: 193px; top: 106px;width:155px; position:absolute;" id="vtlib_headerLinksLay" 
					onmouseout="fninvsh('vtlib_headerLinksLay')" onmouseover="fnvshNrm('vtlib_headerLinksLay')">
					<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr><td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;"><b>{$APP.LBL_MORE}</b></td></tr>
					<tr>
						<td>
							{foreach item=HEADERLINK from=$HEADERLINKS}
								{assign var="headerlink_href" value=$HEADERLINK->linkurl}
								{assign var="headerlink_label" value=$HEADERLINK->linklabel}
								{if $headerlink_label eq ''}
									{assign var="headerlink_label" value=$headerlink_href}
								{else}
									{* Pickup the translated label provided by the module *}
									{assign var="headerlink_label" value=$headerlink_label|@getTranslatedString:$HEADERLINK->module()}
								{/if}
								<a href="{$headerlink_href}" class="drop_down">{$headerlink_label}</a>
							{/foreach}
						</td>
					</tr>
					</table>
				</div>
			</td>
			{/if}
			{* END *}
			
			<!-- gmailbookmarklet customization -->
			 <td style="padding-left:10px;padding-right:10px" class=small nowrap>
				{$GMAIL_BOOKMARKLET}
			 </td>
			 <!-- END -->
			 {if $ADMIN_LINK neq ''} {* Show links only for admin *}
			 <td style="padding-left:10px;padding-right:10px" class=small nowrap> <a href="javascript:void(0);" onclick="vtiger_news(this)">{$APP.LBL_VTIGER_NEWS}</a></td>
			 <td style="padding-left:10px;padding-right:10px" class=small nowrap> <a href="javascript:void(0);" onclick="vtiger_feedback();">{$APP.LBL_FEEDBACK}</a></td>
			 {/if}

			 <td style="padding-left:10px;padding-right:10px" class=small nowrap> <a href="index.php?module=Users&action=DetailView&record={$CURRENT_USER_ID}&modechk=prefview">{$APP.LBL_MY_PREFERENCES}</a></td>
			 <td style="padding-left:10px;padding-right:10px" class=small nowrap><a href="http://wiki.vtiger.com/index.php/Main_Page" target="_blank">{$APP.LNK_HELP}</a></td>
			 <td style="padding-left:10px;padding-right:10px" class=small nowrap><a href="javascript:;" onClick="openwin();">{$APP.LNK_WEARE}</a></td>
			 <td style="padding-left:10px;padding-right:10px" class=small nowrap> <a href="index.php?module=Users&action=Logout">{$APP.LBL_LOGOUT}</a> ({$CURRENT_USER})</td>
			 </tr>
			</table>
		</td>
	</tr>
	</TABLE>

<div id='miniCal' style='width:300px; position:absolute; display:none; left:100px; top:100px; z-index:100000'>
</div>

{if $MODULE_NAME eq 'Calendar'}
<div id="CalExport" style="width:300px; position:absolute; display:none; left:500px; top:100px; z-index:100000" class="layerPopup">
	<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
	<tr>
		<td class="genHeaderSmall" nowrap align="left" width="30%" >{$APP.LBL_EXPORT} </td>
		<td align="right"><a href='javascript:ghide("CalExport");'><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="right" border="0"></a></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
	<tr>
		<td class="small">
			<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
			<tr>
				<td align="right" nowrap class="cellLabel small">
					<input class="small" type='radio' name='exportCalendar' value = 'iCal' onclick="$('ics_filename').removeAttribute('disabled');" checked /> iCal Format
				</td>
				<td align="left">
					<input class="small" type='text' name='ics_filename' id='ics_filename' size='25' value='vtiger.calendar'/>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
	<tr>
		<td class="small" align="center">
		<input type="button" onclick="return exportCalendar();" value="Export" class="crmbutton small edit" name="button"/>
		</td>
	</tr>
	</table>
</div>
<div id='CalImport' style='width:300px; position:absolute; display:none; left:500px; top:100px; z-index:100000' class="layerPopup">
	{assign var='label_filename' value='LBL_FILENAME'}
	<form name='ical_import' id='ical_import' onsubmit="VtigerJS_DialogBox.block();" enctype="multipart/form-data" action="index.php" method="POST">
	<input type='hidden' name='module' value=''>
	<input type='hidden' name='action' value=''>
		<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
			<tr>
				<td class="genHeaderSmall" nowrap align="left" width="30%" id="editfolder_info">{$APP.LBL_IMPORT}</td>
				<td align="right"><a href='javascript:ghide("CalImport");'><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0"></a></td>
			</tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
			<tr>
				<td class="small">
					<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
						<tr>
							<td align="right" nowrap class="cellLabel small"><b>{$label_filename|@getTranslatedString} </b></td>
							<td align="left">
								<input class="small" type='file' name='ics_file' id='ics_file'/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
			<tr>
				<td class="small" align="center">
				<input type="button" onclick="return importCalendar();" value="Import" class="crmbutton small edit" name="button"/>
				</td>
			</tr>
		</table>
	</form>
</div>
{/if}
<!-- header - master tabs -->
<TABLE border=0 cellspacing=0 cellpadding=0 width=100% class="hdrTabBg">
<tr>
	<td style="width:50px" class=small>&nbsp;</td>
	<td class=small nowrap> 
		<table border=0 cellspacing=0 cellpadding=0>

		<tr>
			<td class=tabSeperator><img src="{'spacer.gif'|@vtiger_imageurl:$THEME}" width=2px height=28px></td>		
			{foreach key=maintabs item=detail from=$HEADERS}
				{if $maintabs ne $CATEGORY}
				  <td class="tabUnSelected"  onmouseover="fnDropDown(this,'{$maintabs}_sub');" onmouseout="fnHideDrop('{$maintabs}_sub');" align="center" nowrap><a href="index.php?module={$detail[0]}&action=index&parenttab={$maintabs}">{$APP[$maintabs]}</a><img src="{'menuDnArrow.gif'|@vtiger_imageurl:$THEME}" border=0 style="padding-left:5px"></td>
				  <td class="tabSeperator"><img src="{'spacer.gif'|@vtiger_imageurl:$THEME}"></td>
				{else}
				  <td class="tabSelected"  onmouseover="fnDropDown(this,'{$maintabs}_sub');" onmouseout="fnHideDrop('{$maintabs}_sub');" align="center" nowrap><a href="index.php?module={$detail[0]}&action=index&parenttab={$maintabs}">{$APP[$maintabs]}</a><img src="{'menuDnArrow.gif'|@vtiger_imageurl:$THEME}" border=0 style="padding-left:5px"></td>
				  <td class="tabSeperator"><img src="{'spacer.gif'|@vtiger_imageurl:$THEME}"></td>
				{/if}
			{/foreach}
			<td style="padding-left:10px" nowrap>
				{if $CNT eq 1}
					<select class=small id="qccombo" style="width:110px"  onclick="QCreate(this);">
						<option value="none">{$APP.LBL_QUICK_CREATE}...</option>
                        {foreach  item=detail from=$QCMODULE}
                        <option value="{$detail.1}">{$APP.NEW}&nbsp;{$detail.0}</option>
                        {/foreach}
					</select>
				{else}
					<select class=small id="qccombo" style="width:110px"  onchange="QCreate(this);">
						<option value="none">{$APP.LBL_QUICK_CREATE}...</option>
                        {foreach  item=detail from=$QCMODULE}
                        <option value="{$detail.1}">{$APP.NEW}&nbsp;{$detail.0}</option>
                        {/foreach}
					</select>
				{/if}	
			</td>
		</tr>

		</table>
	</td>
	<td align=right style="padding-right:10px" nowrap >
		<table border=0 cellspacing=0 cellpadding=0 id="search" style="border:1px solid #999999;background-color:white">
		   <tr>
			<form name="UnifiedSearch" method="post" action="index.php" style="margin:0px" onsubmit="VtigerJS_DialogBox.block();">
			<td style="height:19px;background-color:#ffffef" nowrap>
				<a href='javascript:void(0);' onclick="UnifiedSearch_SelectModuleForm(this);"><img src="{'settings_top.gif'|@vtiger_imageurl:$THEME}" align='left' border=0></a>
				<input type="hidden" name="action" value="UnifiedSearch" style="margin:0px">
				<input type="hidden" name="module" value="Home" style="margin:0px">
				<input type="hidden" name="parenttab" value="{$CATEGORY}" style="margin:0px">
				<input type="hidden" name="search_onlyin" value="--USESELECTED--" style="margin:0px">
				<input type="text" name="query_string" value="{$QUERY_STRING}" class="searchBox" onFocus="this.value=''" >
			</td>
			<td style="background-color:#cccccc">
				<input type="submit" class="searchBtn" value="{$APP.LBL_FIND_BUTTON}" alt="{$APP.LBL_FIND}" title="{$APP.LBL_FIND}">
			</td>
			</form>
		   </tr>
		</table>
	</td>
</td>
</tr>
</TABLE>
<!-- - level 2 tabs starts-->
<TABLE border=0 cellspacing=0 cellpadding=2 width=100% class="level2Bg" >
<tr>
	<td >
		<table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<!-- ASHA: Avoid using this as it gives module name instead of module label. 
			Now Using the same array QUICKACCESS that is used to show drop down menu
			(which gives both module name and module label)-->
			<!--{foreach  key=maintabs item=detail from=$HEADERS}
				{if $maintabs eq $CATEGORY}
					{foreach  key=number item=module from=$detail}
						{assign var="modulelabel" value=$module}
      					{if $APP.$module} 
      						{assign var="modulelabel" value=$APP.$module} 
      					{/if}
						{if $module eq $MODULE_NAME}
							<td class="level2SelTab" nowrap><a href="index.php?module={$module}&action=index&parenttab={$maintabs}">{$modulelabel}</a></td>
						{else}
							<td class="level2UnSelTab" nowrap> <a href="index.php?module={$module}&action=index&parenttab={$maintabs}">{$modulelabel}</a> </td>
						{/if}	
					{/foreach}
				{/if}
			{/foreach}-->
			
			{foreach key=maintabs item=details from=$QUICKACCESS}
				{if $maintabs eq $CATEGORY}
					{foreach  key=number item=modules from=$details}
						{assign var="modulelabel" value=$modules[1]|@getTranslatedString:$modules[0]}
	   					
	   					{* Use Custom module action if specified *}
						{assign var="moduleaction" value="index"}
	   					{if isset($modules[2])}
	   						{assign var="moduleaction" value=$modules[2]}
	   					{/if}		
	   									
						{if $modules.0 eq $MODULE_NAME}
							<td class="level2SelTab" nowrap><a href="index.php?module={$modules.0}&action={$moduleaction}&parenttab={$maintabs}">{$modulelabel}</a></td>
						{else}
							<td class="level2UnSelTab" nowrap> <a href="index.php?module={$modules.0}&action={$moduleaction}&parenttab={$maintabs}">{$modulelabel}</a> </td>
						{/if}	
					{/foreach}
				{/if}
			{/foreach}
		</tr>
		</table>
	</td>
</tr>
</TABLE>		
<!-- Level 2 tabs ends -->
<div id="calculator_cont" style="position:absolute; z-index:10000" ></div>
	{include file="Clock.tpl"}

<div id="qcform" style="position:absolute;width:700px;top:80px;left:450px;z-index:100000;"></div>

<!-- Unified Search module selection feature -->
<div id="UnifiedSearch_moduleformwrapper" style="position:absolute;width:400px;z-index:100002;display:none;"></div>
<script type='text/javascript'>
{literal}
function UnifiedSearch_SelectModuleForm(obj) {
	if($('UnifiedSearch_moduleform')) {
		// If we have loaded the form already.
		UnifiedSearch_SelectModuleFormCallback(obj);
	} else {
		$('status').show();
		new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Home&action=HomeAjax&file=UnifiedSearchModules&ajax=true',
			onComplete: function(response) {
				$('status').hide();
				$('UnifiedSearch_moduleformwrapper').innerHTML = response.responseText;
				UnifiedSearch_SelectModuleFormCallback(obj);
			}
		});
	}
}
function UnifiedSearch_SelectModuleFormCallback(obj) {
	fnvshobj(obj, 'UnifiedSearch_moduleformwrapper');
}
function UnifiedSearch_SelectModuleToggle(flag) {
	Form.getElements($('UnifiedSearch_moduleform')).each(
		function(element) {
			if(element.type == 'checkbox') {
				element.checked = flag;
			}
		}
	);
}
function UnifiedSearch_SelectModuleCancel() {
	$('UnifiedSearch_moduleformwrapper').hide();
}
function UnifiedSearch_SelectModuleSave() {
	var UnifiedSearch_form = document.forms.UnifiedSearch;
	UnifiedSearch_form.search_onlyin.value = Form.serialize($('UnifiedSearch_moduleform')).replace(/search_onlyin=/g, '').replace(/&/g,',');
	UnifiedSearch_SelectModuleCancel();
}
{/literal}
</script>
<!-- End -->

<script>
var gVTModule = '{$smarty.request.module|@vtlib_purify}';
function fetch_clock()
{ldelim}
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=Utilities&action=UtilitiesAjax&file=Clock',
			onComplete: function(response)
				    {ldelim}
					$("clock_cont").innerHTML=response.responseText;
					execJS($('clock_cont'));
				    {rdelim}
		{rdelim}
	);

{rdelim}

function fetch_calc()
{ldelim}
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=Utilities&action=UtilitiesAjax&file=Calculator',
			onComplete: function(response)
					{ldelim}
						$("calculator_cont").innerHTML=response.responseText;
						execJS($('calculator_cont'));
					{rdelim}
		{rdelim}
	);
{rdelim}
</script>

<script>
{literal}
function QCreate(qcoptions){
	var module = qcoptions.options[qcoptions.options.selectedIndex].value;
	if(module != 'none'){
		$("status").style.display="inline";
		if(module == 'Events'){
			module = 'Calendar';
			var urlstr = '&activity_mode=Events';
		}else if(module == 'Calendar'){
			module = 'Calendar';
			var urlstr = '&activity_mode=Task';
		}else{
			var urlstr = '';
		}
		new Ajax.Request(
			'index.php',
				{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody: 'module='+module+'&action='+module+'Ajax&file=QuickCreate'+urlstr,
				onComplete: function(response){
					$("status").style.display="none";
					$("qcform").style.display="inline";
					$("qcform").innerHTML = response.responseText;
					// Evaluate all the script tags in the response text.
					var scriptTags = $("qcform").getElementsByTagName("script");
					for(var i = 0; i< scriptTags.length; i++){
						var scriptTag = scriptTags[i];
						eval(scriptTag.innerHTML);
					}
                    eval($("qcform"));
                    posLay(qcoptions, "qcform");
				}
			}
		);
	}else{
		hide('qcform');
	}
}

function getFormValidate(divValidate)
{
  var st = document.getElementById('qcvalidate');
  eval(st.innerHTML);
  for (var i=0; i<qcfieldname.length; i++) {
		var curr_fieldname = qcfieldname[i];	
		if(window.document.QcEditView[curr_fieldname] != null)
		{
			var type=qcfielddatatype[i].split("~")
			var input_type = window.document.QcEditView[curr_fieldname].type;	
			if (type[1]=="M") {
					if (!qcemptyCheck(curr_fieldname,qcfieldlabel[i],input_type))
						return false
				}
			switch (type[0]) {
				case "O"  : break;
				case "V"  : break;
				case "C"  : break;
				case "DT" :
					if (window.document.QcEditView[curr_fieldname] != null && window.document.QcEditView[curr_fieldname].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)
					{	 
						if (type[1]=="M")
							if (!qcemptyCheck(type[2],qcfieldlabel[i],getObj(type[2]).type))
								return false
						if(typeof(type[3])=="undefined") var currdatechk="OTH"
						else var currdatechk=type[3]

						if (!qcdateTimeValidate(curr_fieldname,type[2],qcfieldlabel[i],currdatechk))
							return false
						if (type[4]) {
							if (!dateTimeComparison(curr_fieldname,type[2],qcfieldlabel[i],type[5],type[6],type[4]))
								return false

						}
					}		
				break;
				case "D"  :
					if (window.document.QcEditView[curr_fieldname] != null && window.document.QcEditView[curr_fieldname].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)
					{	
						if(typeof(type[2])=="undefined") var currdatechk="OTH"
						else var currdatechk=type[2]

							if (!qcdateValidate(curr_fieldname,qcfieldlabel[i],currdatechk))
								return false
									if (type[3]) {
										if (!qcdateComparison(curr_fieldname,qcfieldlabel[i],type[4],type[5],type[3]))
											return false
									}
					}	
				break;
				case "T"  :
					if (window.document.QcEditView[curr_fieldname] != null && window.document.QcEditView[curr_fieldname].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)
					{	 
						if(typeof(type[2])=="undefined") var currtimechk="OTH"
						else var currtimechk=type[2]

							if (!timeValidate(curr_fieldname,qcfieldlabel[i],currtimechk))
								return false
									if (type[3]) {
										if (!timeComparison(curr_fieldname,qcfieldlabel[i],type[4],type[5],type[3]))
											return false
									}
					}
				break;
				case "I"  :
					if (window.document.QcEditView[curr_fieldname] != null && window.document.QcEditView[curr_fieldname].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)
					{	
						if (window.document.QcEditView[curr_fieldname].value.length!=0)
						{
							if (!qcintValidate(curr_fieldname,qcfieldlabel[i]))
								return false
							if (type[2]) {
								if (!qcnumConstComp(curr_fieldname,qcfieldlabel[i],type[2],type[3]))
									return false
							}
						}
					}
				break;
				case "N"  :
					case "NN" :
					if (window.document.QcEditView[curr_fieldname] != null && window.document.QcEditView[curr_fieldname].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)
					{
						if (window.document.QcEditView[curr_fieldname].value.length!=0)
						{
							if (typeof(type[2])=="undefined") var numformat="any"
							else var numformat=type[2]

								if (type[0]=="NN") {

									if (!numValidate(curr_fieldname,qcfieldlabel[i],numformat,true))
										return false
								} else {
									if (!numValidate(curr_fieldname,qcfieldlabel[i],numformat))
										return false
								}
							if (type[3]) {
								if (!numConstComp(curr_fieldname,qcfieldlabel[i],type[3],type[4]))
									return false
							}
						}
					}
				break;
				case "E"  :
					if (window.document.QcEditView[curr_fieldname] != null && window.document.QcEditView[curr_fieldname].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)
					{
						if (window.document.QcEditView[curr_fieldname].value.length!=0)
						{
							var etype = "EMAIL"
								if (!qcpatternValidate(curr_fieldname,qcfieldlabel[i],etype))
									return false
						}
					}
				break;
			}
		}
	}
       //added to check Start Date & Time,if Activity Status is Planned.//start
        for (var j=0; j<qcfieldname.length; j++)
		{
			curr_fieldname = qcfieldname[j];
			if(window.document.QcEditView[curr_fieldname] != null)
			{
				if(qcfieldname[j] == "date_start")
				{
					var datelabel = qcfieldlabel[j]
						var datefield = qcfieldname[j]
						var startdatevalue = window.document.QcEditView[datefield].value.replace(/^\s+/g, '').replace(/\s+$/g, '')
				}
				if(qcfieldname[j] == "time_start")
				{
					var timelabel = qcfieldlabel[j]
						var timefield = qcfieldname[j]
						var timeval=window.document.QcEditView[timefield].value.replace(/^\s+/g, '').replace(/\s+$/g, '')
				}
				if(qcfieldname[j] == "eventstatus" || qcfieldname[j] == "taskstatus")
				{
					var statusvalue = window.document.QcEditView[curr_fieldname].options[window.document.QcEditView[curr_fieldname].selectedIndex].value.replace(/^\s+/g, '').replace(/\s+$/g, '')
					var statuslabel = qcfieldlabel[j++]
				}
			}
		}
	if(statusvalue == "Planned")
        {
               var dateelements=splitDateVal(startdatevalue)
	       var hourval=parseInt(timeval.substring(0,timeval.indexOf(":")))
               var minval=parseInt(timeval.substring(timeval.indexOf(":")+1,timeval.length))
               var dd=dateelements[0]
               var mm=dateelements[1]
               var yyyy=dateelements[2]

               var chkdate=new Date()
               chkdate.setYear(yyyy)
               chkdate.setMonth(mm-1)
               chkdate.setDate(dd)
	       chkdate.setMinutes(minval)
               chkdate.setHours(hourval)
		if(!comparestartdate(chkdate)) return false;
		

	 }//end
	return true;
}
</SCRIPT>
{/literal}

{* Quick Access Functionality *}
<div id="allMenu" onmouseout="fninvsh('allMenu');" onMouseOver="fnvshNrm('allMenu');" style="width:650px;z-index: 10000001;display:none;">
	<table border=0 cellpadding="5" cellspacing="0" class="allMnuTable" >
	<tr>
		<td valign="top">
		{assign var="parentno" value=0}
		{foreach name=parenttablist key=parenttab item=details from=$QUICKACCESS}
			<span class="allMnuHdr">{$APP[$parenttab]}</span>
			{foreach name=modulelist item=modules from=$details}
       		{math assign="num" equation="x + y" x=$parentno y=1}
			{math assign="loopvalue" equation="x % y" x=$num y=15}
			{assign var="parentno" value=$num}
			{if $loopvalue eq '0'}
				</td><td valign="top">
			{/if}
			{assign var="modulelabel" value=$modules[1]|@getTranslatedString:$modules[0]}
			<a href="index.php?module={$modules.0}&action=index&parenttab={$parenttab}" class="allMnu">{$modulelabel}</a>
			{/foreach}
		{/foreach}
		</td>
	</tr>
</table>
</div>

<!-- Drop Down Menu in the Main Tab -->
{foreach name=parenttablist key=parenttab item=details from=$QUICKACCESS}
<div class="drop_mnu" id="{$parenttab}_sub" onmouseout="fnHideDrop('{$parenttab}_sub')" onmouseover="fnShowDrop('{$parenttab}_sub')">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		{foreach name=modulelist item=modules from=$details}
		{assign var="modulelabel" value=$modules[1]|@getTranslatedString:$modules[0]}
		
		{* Use Custom module action if specified *}
		{assign var="moduleaction" value="index"}
	   	{if isset($modules[2])}
	   		{assign var="moduleaction" value=$modules[2]}
	   	{/if}
		
		<tr><td><a href="index.php?module={$modules.0}&action={$moduleaction}&parenttab={$parenttab}" class="drop_down">{$modulelabel}</a></td></tr>
		{/foreach}
	</table>
</div>
{/foreach}


<div id="status" style="position:absolute;display:none;left:850px;top:95px;height:27px;white-space:nowrap;"><img src="{'status.gif'|@vtiger_imageurl:$THEME}"></div>
<script>
function openwin()
{ldelim}
            window.open("index.php?module=Users&action=about_us","aboutwin","height=520,width=515,top=200,left=300")
{rdelim}

</script>


<div id="tracker" style="display:none;position:absolute;z-index:100000001;" class="layerPopup">

	<table border="0" cellpadding="5" cellspacing="0" width="200">
	<tr style="cursor:move;">
		<td colspan="2" class="mailClientBg small" id="Track_Handle"><strong>{$APP.LBL_LAST_VIEWED}</strong></td>
		<td align="right" style="padding:5px;" class="mailClientBg small">
		<a href="javascript:;"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  onClick="fninvsh('tracker')" hspace="5" align="absmiddle"></a>
		</td></tr>
	</table>
	<table border="0" cellpadding="5" cellspacing="0" width="200" class="hdrNameBg">
	{foreach name=trackinfo item=trackelements from=$TRACINFO}
	<tr>
		<td class="trackerListBullet small" align="center" width="12">{$smarty.foreach.trackinfo.iteration}</td>
		<td class="trackerList small"> <a href="index.php?module={$trackelements.module_name}&action=DetailView&record={$trackelements.crmid}&parenttab={$CATEGORY}">{$trackelements.item_summary}</a> </td><td class="trackerList small">&nbsp;</td></tr>
	{/foreach}
	</table>
</div>
	
<script>
	var THandle = document.getElementById("Track_Handle");
	var TRoot   = document.getElementById("tracker");
	Drag.init(THandle, TRoot);
</script>		

<!-- vtiger Feedback -->
<script type="text/javascript">
{literal}
function vtiger_feedback() {
	window.open("http://www.vtiger.com/products/crm/feedback.php?uid={/literal}{php}global $application_unique_key; echo $application_unique_key;{/php}{literal}","feedbackwin","height=300,width=515,top=200,left=300")
}
{/literal}
</script>
<!-- vtiger news -->
<script type="text/javascript">
{literal}
function vtiger_news(obj) {
	$('status').style.display = 'inline';
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Home&action=HomeAjax&file=HomeNews',
			onComplete: function(response) {
				$("vtigerNewsPopupLay").innerHTML=response.responseText;
				fnvshobj(obj, 'vtigerNewsPopupLay');
				$('status').style.display = 'none';
			}
		}
	);
		
}
{/literal}
</script>
<div class="lvtCol fixedLay1" id="vtigerNewsPopupLay" style="display: none; height: 250px; bottom: 2px; padding: 2px; z-index: 12; font-weight: normal;" align="left">
</div>
<!-- END -->

<!-- ActivityReminder Customization for callback -->
<div class="lvtCol fixedLay1" id="ActivityRemindercallback" style="border: 0; right: 0px; bottom: 2px; display:none; padding: 2px; z-index: 10; font-weight: normal;" align="left">
</div>
<!-- End -->

<!-- divs for asterisk integration -->
<div class="lvtCol fixedLay1" id="notificationDiv" style="float: right;  padding-right: 5px; overflow: hidden; border-style: solid; right: 0px; border-color: rgb(141, 141, 141); bottom: 0px; display: none; padding: 2px; z-index: 10; font-weight: normal;" align="left">
</div>

<div id="OutgoingCall" style="display: none;position: absolute;z-index:200;" class="layerPopup">
	<table  border='0' cellpadding='5' cellspacing='0' width='100%'>
		<tr style='cursor:move;' >
			<td class='mailClientBg small' id='outgoing_handle'>
				<b>{$APP.LBL_OUTGOING_CALL}</b>
			</td>
		</tr>
	</table>
	<table  border='0' cellpadding='0' cellspacing='0' width='100%' class='hdrNameBg'>
		</tr>
		<tr><td style='padding:10px;' colspan='2'>
			{$APP.LBL_OUTGOING_CALL_MESSAGE}
		</td></tr>
	</table>
</div>
<!-- divs for asterisk integration :: end-->
