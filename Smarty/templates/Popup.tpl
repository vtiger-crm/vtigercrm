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
<script>
var image_pth = '{$IMAGE_PATH}';

function showAllRecords()
{ldelim}
        modname = document.getElementById("relmod").name;
        idname= document.getElementById("relrecord_id").name;
        var locate = location.href;
        url_arr = locate.split("?");
        emp_url = url_arr[1].split("&");
        for(i=0;i< emp_url.length;i++)
        {ldelim}
                if(emp_url[i] != '')
                {ldelim}
                        split_value = emp_url[i].split("=");
                        if(split_value[0] == modname || split_value[0] == idname )
                                emp_url[i]='';
                        else if(split_value[0] == "fromPotential" || split_value[0] == "acc_id")
                                emp_url[i]='';

                {rdelim}
        {rdelim}
        correctUrl =emp_url.join("&");
        Url = "index.php?"+correctUrl;
        return Url;
{rdelim}

//function added to get all the records when parent record doesn't relate with the selection module records while opening/loading popup.
function redirectWhenNoRelatedRecordsFound()
{ldelim}
        var loadUrl = showAllRecords();
        window.location.href = loadUrl;
{rdelim}
</script>
<link rel="stylesheet" type="text/css" href="{$THEME_PATH}style.css">
<script language="JavaScript" type="text/javascript" src="include/js/ListView.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/Inventory.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/json.js"></script>
<!-- vtlib customization: Javascript hook -->
<script language="JavaScript" type="text/javascript" src="include/js/vtlib.js"></script>
<!-- END -->
<script language="JavaScript" type="text/javascript" src="include/js/{php} echo $_SESSION['authenticated_user_language'];{/php}.lang.js?{php} echo $_SESSION['vtiger_version'];{/php}"></script>
<script language="JavaScript" type="text/javascript" src="modules/{$RETURN_MODULE}/{$RETURN_MODULE}.js"></script>
<script language="JavaScript" type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>
<script type='text/javascript' src='modules/com_vtiger_workflow/resources/jquery-1.2.6.js'></script>
<script type='text/javascript'>
	jQuery.noConflict();
</script>
<script type="text/javascript">
{literal}
function add_data_to_relatedlist(entity_id,recordid,mod, popupmode, callback) {
	var return_module = document.getElementById('return_module').value;
	if(popupmode == 'ajax') {
		VtigerJS_DialogBox.block();
		new Ajax.Request(
            'index.php',
            {queue: {position: 'end', scope: 'command'},
             method: 'post',
             postBody: "module="+return_module+"&action="+return_module+"Ajax&file=updateRelations&destination_module="+mod+"&entityid="+entity_id+"&parentid="+recordid+"&mode=Ajax",
             onComplete: function(response) {
					VtigerJS_DialogBox.unblock();
					var res = JSON.parse(response.responseText);
					if(typeof callback == 'function') {
						callback(res);
					}
                }
			}
		);
		return false;
	} else {
		{/literal}
        opener.document.location.href="index.php?module={$RETURN_MODULE}&action=updateRelations&destination_module="+mod+"&entityid="+entity_id+"&parentid="+recordid+"&return_module={$RETURN_MODULE}&return_action={$RETURN_ACTION}&parenttab={$CATEGORY}";
		window.close();
		{literal}
	}
}
{/literal}
function set_focus() {ldelim}
	$('search_txt').focus();
{rdelim}
</script>

<body  onload=set_focus() class="small" marginwidth=0 marginheight=0 leftmargin=0 topmargin=0 bottommargin=0 rightmargin=0>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="mailClient mailClientBg">
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					{if $recid_var_value neq ''}
                            <td class="moduleName" width="80%" style="padding-left:10px;">{$APP[$MODULE]}&nbsp;{$APP.LBL_RELATED_TO}&nbsp;{$APP[$PARENT_MODULE]}</td>
                    {else}
                            {if $RECORD_ID}
	                            <td class="moduleName" width="80%" style="padding-left:10px;"><a href="javascript:;" onclick="window.history.back();">{$APP[$MODULE]}</a> > {$PRODUCT_NAME}</td>
							{else}
	                            <td class="moduleName" width="80%" style="padding-left:10px;">{$APP[$MODULE]}</td>
							{/if}
                    {/if}
					<td  width=30% nowrap class="componentName" align=right>{$APP.VTIGER}</td>
				</tr>
			</table>
			<div id="status" style="position:absolute;display:none;right:135px;top:15px;height:27px;white-space:nowrap;"><img src="{'status.gif'|@vtiger_imageurl:$THEME}"></div>
			<table width="100%" cellpadding="5" cellspacing="0" border="0"  class="homePageMatrixHdr">
				<tr>
					<td style="padding:10px;" >
						<form name="basicSearch" action="index.php" onsubmit="callSearch('Basic');return false;">
						<table width="100%" cellpadding="5" cellspacing="0">
						<tr>
							<td width="20%" class="dvtCellLabel"><img src="{'basicSearchLens.gif'|@vtiger_imageurl:$THEME}"></td>
							<td width="30%" class="dvtCellLabel"><input type="text" name="search_text" id="search_txt" class="txtBox"> </td>
							<td width="30%" class="dvtCellLabel"><b>{$APP.LBL_IN}</b>&nbsp;
								<select name ="search_field" class="txtBox">
											 {html_options  options=$SEARCHLISTHEADER }
								</select>
								<input type="hidden" name="searchtype" value="BasicSearch">
								<input type="hidden" name="module" value="{$MODULE}">
								<input type="hidden" name="action" value="Popup">
								<input type="hidden" name="query" value="true">
								<input type="hidden" name="select_enable" id="select_enable" value="{$SELECT}">
								<input type="hidden" name="curr_row" id="curr_row" value="{$CURR_ROW}">
								<input type="hidden" name="fldname_pb" value="{$FIELDNAME}">
								<input type="hidden" name="productid_pb" value="{$PRODUCTID}">
								<input name="popuptype" id="popup_type" type="hidden" value="{$POPUPTYPE}">
								<input name="recordid" id="recordid" type="hidden" value="{$RECORDID}">
								<input name="record_id" id="record_id" type="hidden" value="{$RECORD_ID}">
								<input name="return_module" id="return_module" type="hidden" value="{$RETURN_MODULE}">
								<input name="from_link" id="from_link" type="hidden" value="{$smarty.request.fromlink.value|@vtlib_purify}">
								<input name="maintab" id="maintab" type="hidden" value="{$MAINTAB}">
								<input type="hidden" id="relmod" name="{$mod_var_name}" value="{$mod_var_value}">
                                <input type="hidden" id="relrecord_id" name="{$recid_var_name}" value="{$recid_var_value}">
								<input name="form"  id="popupform" type="hidden" value="{$smarty.request.form|@vtlib_purify}">
								{* vtlib customization: For uitype 10 popup during paging *}
								{if $smarty.request.form eq 'vtlibPopupView'}
									<input name="forfield"  id="forfield" type="hidden" value="{$smarty.request.forfield|@vtlib_purify}">
									<input name="srcmodule"  id="srcmodule" type="hidden" value="{$smarty.request.srcmodule|@vtlib_purify}">
									<input name="forrecord"  id="forrecord" type="hidden" value="{$smarty.request.forrecord|@vtlib_purify}">
								{/if}
								{if $smarty.request.currencyid neq ''}
									<input type="hidden" name="curr_row" id="currencyid" value="{$smarty.request.currencyid|@vtlib_purify}">
								{/if}
								{* END *}
							</td>
							<td width="20%" class="dvtCellLabel">
								<input type="button" name="search" value=" &nbsp;{$APP.LBL_SEARCH_NOW_BUTTON}&nbsp; " onClick="callSearch('Basic');" class="crmbutton small create">
							</td>
						</tr>
						 <tr>
							<td colspan="4" align="center">
								<table width="100%" class="small">
								<tr>	
									{$ALPHABETICAL}
								</tr>
								</table>
							</td>
						</tr>
						</table>
						</form>
					</td>
				</tr>
				{if $recid_var_value neq ''}
                                <tr>
                                        <td align="right"><input id="all_contacts" alt="{$APP.LBL_SELECT_BUTTON_LABEL} {$APP.$MODULE}" title="{$APP.LBL_SELECT_BUTTON_LABEL} {$APP.$MODULE}" accessKey="" class="crmbutton small edit" value="{$APP.SHOW_ALL}&nbsp;{$APP.$MODULE}" LANGUAGE=javascript onclick="window.location.href=showAllRecords();" type="button"  name="button"></td>
                                </tr>
                                {/if}
			</table>

			<div id="ListViewContents">
				{include file="PopupContents.tpl"}
			</div>
		</td>
	</tr>
	
</table>
</body>
<script>
var gPopupAlphaSearchUrl = '';
var gsorder ='';
var gstart ='';
function callSearch(searchtype)
{ldelim}
    gstart='';
    for(i=1;i<=26;i++)
    {ldelim}
        var data_td_id = 'alpha_'+ eval(i);
        getObj(data_td_id).className = 'searchAlph';
    {rdelim}
    gPopupAlphaSearchUrl = '';
    search_fld_val= document.basicSearch.search_field[document.basicSearch.search_field.selectedIndex].value;
    search_txt_val= encodeURIComponent(document.basicSearch.search_text.value.replace(/\'/,"\\'"));
    var urlstring = '';
    if(searchtype == 'Basic')
    {ldelim}
	urlstring = 'search_field='+search_fld_val+'&searchtype=BasicSearch&search_text='+search_txt_val;
    {rdelim}
	popuptype = $('popup_type').value;
	act_tab = $('maintab').value;
	urlstring += '&popuptype='+popuptype;
	urlstring += '&maintab='+act_tab;
	urlstring = urlstring +'&query=true&file=Popup&module={$MODULE}&action={$MODULE}Ajax&ajax=true&search=true';
	urlstring +=gethiddenelements();
	record_id = document.basicSearch.record_id.value;

	//support for popupmode and callback
	urlstring += "&popupmode={$POPUPMODE}";
	urlstring += "&callback={$CALLBACK}";

	if(record_id!='')
		urlstring += '&record_id='+record_id;
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
				method: 'post',
				postBody: urlstring,
				onComplete: function(response) {ldelim}
					$("status").style.display="none";
					$("ListViewContents").innerHTML= response.responseText;
				{rdelim}
			{rdelim}
		);
{rdelim}	
function alphabetic(module,url,dataid)
{ldelim}
    gstart='';
    document.basicSearch.search_text.value = '';	
    for(i=1;i<=26;i++)
    {ldelim}
	var data_td_id = 'alpha_'+ eval(i);
	getObj(data_td_id).className = 'searchAlph';
    {rdelim}
    getObj(dataid).className = 'searchAlphselected';
    gPopupAlphaSearchUrl = '&'+url;	
    var urlstring ="module="+module+"&action="+module+"Ajax&file=Popup&ajax=true&search=true&"+url;
    urlstring +=gethiddenelements();
	record_id = document.basicSearch.record_id.value;
	if(record_id!='')
		urlstring += '&record_id='+record_id;
    $("status").style.display="inline";
    new Ajax.Request(
                'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                                method: 'post',
                                postBody: urlstring,
                                onComplete: function(response) {ldelim}
                                	$("status").style.display="none";
									$("ListViewContents").innerHTML= response.responseText;
				{rdelim}
			{rdelim}
		);
{rdelim}
function gethiddenelements()
{ldelim}
	gstart='';
	var urlstring=''	
	if(getObj('select_enable').value != '')
		urlstring +='&select=enable';	
	if(document.getElementById('curr_row').value != '')
		urlstring +='&curr_row='+document.getElementById('curr_row').value;	
	if(getObj('fldname_pb').value != '')
		urlstring +='&fldname='+getObj('fldname_pb').value;	
	if(getObj('productid_pb').value != '')
		urlstring +='&productid='+getObj('productid_pb').value;	
	if(getObj('recordid').value != '')
		urlstring +='&recordid='+getObj('recordid').value;
	if(getObj('relmod').value != '')
                urlstring +='&'+getObj('relmod').name+'='+getObj('relmod').value;
    if(getObj('relrecord_id').value != '')
            urlstring +='&'+getObj('relrecord_id').name+'='+getObj('relrecord_id').value;
	
	// vtlib customization: For uitype 10 popup during paging
	if(document.getElementById('popupform'))
		urlstring +='&form='+encodeURIComponent(getObj('popupform').value);
	if(document.getElementById('forfield'))
		urlstring +='&forfield='+encodeURIComponent(getObj('forfield').value);
	if(document.getElementById('srcmodule'))
		urlstring +='&srcmodule='+encodeURIComponent(getObj('srcmodule').value);
	if(document.getElementById('forrecord'))
		urlstring +='&forrecord='+encodeURIComponent(getObj('forrecord').value);
	// END
		
	if(document.getElementById('currencyid') != null && document.getElementById('currencyid').value != '')
		urlstring +='&currencyid='+document.getElementById('currencyid').value;

	var return_module = document.getElementById('return_module').value;
	if(return_module != '')
		urlstring += '&return_module='+return_module;
	return urlstring;
{rdelim}
																									
function getListViewEntries_js(module,url)
{ldelim}
	gstart="&"+url;

	popuptype = document.getElementById('popup_type').value;
        var urlstring ="module="+module+"&action="+module+"Ajax&file=Popup&ajax=true&"+url;
    	urlstring +=gethiddenelements();
	
	{if !$RECORD_ID}
		search_fld_val= document.basicSearch.search_field[document.basicSearch.search_field.selectedIndex].value;
		search_txt_val=document.basicSearch.search_text.value;
		if(search_txt_val != '')
			urlstring += '&query=true&search_field='+search_fld_val+'&searchtype=BasicSearch&search_text='+search_txt_val;
	{/if}
	if(gPopupAlphaSearchUrl != '')
		urlstring += gPopupAlphaSearchUrl;	
	else
		urlstring += '&popuptype='+popuptype;	
	
	record_id = document.basicSearch.record_id.value;
	if(record_id!='')
		urlstring += '&record_id='+record_id;

	urlstring += (gsorder !='') ? gsorder : '';
	var return_module = document.getElementById('return_module').value;
	if(module == 'Documents' && return_module == 'MailManager')
	{ldelim}
		urlstring += '&callback=MailManager.add_data_to_relatedlist';
		urlstring += '&popupmode=ajax';
		urlstring += '&srcmodule=MailManager';
	{rdelim}

	$("status").style.display = "";
	new Ajax.Request(
                'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                                method: 'post',
                                postBody: urlstring,
                                onComplete: function(response) {ldelim}
                                        $("ListViewContents").innerHTML= response.responseText;
									$("status").style.display = "none";
				{rdelim}
			{rdelim}
		);
{rdelim}

function getListViewSorted_js(module,url)
{ldelim}
	gsorder=url;
        var urlstring ="module="+module+"&action="+module+"Ajax&file=Popup&ajax=true"+url;
	record_id = document.basicSearch.record_id.value;
	if(record_id!='')
		urlstring += '&record_id='+record_id;
	urlstring += (gstart !='') ? gstart : '';
	$("status").style.display = "";
	new Ajax.Request(
                'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                                method: 'post',
                                postBody: urlstring,
                                onComplete: function(response) {ldelim}
                                        $("ListViewContents").innerHTML= response.responseText;
									$("status").style.display = "none";
				{rdelim}
			{rdelim}
		);
{rdelim}

var product_labelarr = {ldelim}
	CLEAR_COMMENT:'{$APP.LBL_CLEAR_COMMENT}',
	DISCOUNT:'{$APP.LBL_DISCOUNT}',
	TOTAL_AFTER_DISCOUNT:'{$APP.LBL_TOTAL_AFTER_DISCOUNT}',
	TAX:'{$APP.LBL_TAX}',
	ZERO_DISCOUNT:'{$APP.LBL_ZERO_DISCOUNT}',
	PERCENT_OF_PRICE:'{$APP.LBL_OF_PRICE}',
	DIRECT_PRICE_REDUCTION:'{$APP.LBL_DIRECT_PRICE_REDUCTION}'
{rdelim};

</script>
