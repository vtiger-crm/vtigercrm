{*<!--
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}

{*<!-- module header -->*}
<script language="JavaScript" type="text/javascript" src="include/js/ListView.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/search.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/Merge.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/dtlviewajax.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/FieldDependencies.js"></script>
<script type="text/javascript" src="modules/com_vtiger_workflow/resources/jquery-1.2.6.js"></script>
<script type="text/javascript">
	jQuery.noConflict();
</script>
<script language="javascript" type="text/javascript">
var typeofdata = new Array();
typeofdata['E'] = ['e','n','s','ew','c','k'];
typeofdata['V'] = ['e','n','s','ew','c','k'];
typeofdata['N'] = ['e','n','l','g','m','h'];
typeofdata['NN'] = ['e','n','l','g','m','h'];
typeofdata['T'] = ['e','n','l','g','m','h'];
typeofdata['I'] = ['e','n','l','g','m','h'];
typeofdata['C'] = ['e','n'];
typeofdata['DT'] = ['e','n','l','g','m','h'];
typeofdata['D'] = ['e','n','l','g','m','h'];
var fLabels = new Array();
fLabels['e'] = "{$APP.is}";
fLabels['n'] = "{$APP.is_not}";
fLabels['s'] = "{$APP.begins_with}";
fLabels['ew'] = "{$APP.ends_with}";
fLabels['c'] = "{$APP.contains}";
fLabels['k'] = "{$APP.does_not_contains}";
fLabels['l'] = "{$APP.less_than}";
fLabels['g'] = "{$APP.greater_than}";
fLabels['m'] = "{$APP.less_or_equal}";
fLabels['h'] = "{$APP.greater_or_equal}";
var noneLabel;
{literal}
function trimfValues(value)
{
    var string_array;
    string_array = value.split(":");
    return string_array[4];
}

function updatefOptions(sel, opSelName) {
    var selObj = document.getElementById(opSelName);
    var fieldtype = null ;

    var currOption = selObj.options[selObj.selectedIndex];
    var currField = sel.options[sel.selectedIndex];
    
    var fld = currField.value.split(":");
    var tod = fld[4];
    if(currField.value != null && currField.value.length != 0)
    {
	fieldtype = trimfValues(currField.value);
	fieldtype = fieldtype.replace(/\\'/g,'');
	ops = typeofdata[fieldtype];
	var off = 0;
	if(ops != null)
	{

		var nMaxVal = selObj.length;
		for(nLoop = 0; nLoop < nMaxVal; nLoop++)
		{
			selObj.remove(0);
		}
		for (var i = 0; i < ops.length; i++)
		{
			var label = fLabels[ops[i]];
			if (label == null) continue;
			var option = new Option (fLabels[ops[i]], ops[i]);
			selObj.options[i] = option;
			if (currOption != null && currOption.value == option.value)
			{
				option.selected = true;
			}
		}
	}
    }else
    {
	var nMaxVal = selObj.length;
	for(nLoop = 0; nLoop < nMaxVal; nLoop++)
	{
		selObj.remove(0);
	}
	selObj.options[0] = new Option ('None', '');
	if (currField.value == '') {
		selObj.options[0].selected = true;
	}
    }

}
{/literal}
</script>
<script language="JavaScript" type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<script language="javascript">
function checkgroup()
{ldelim}
  if($("group_checkbox").checked)
  {ldelim}
  document.change_ownerform_name.lead_group_owner.style.display = "block";
  document.change_ownerform_name.lead_owner.style.display = "none";
  {rdelim}
  else
  {ldelim}
  document.change_ownerform_name.lead_owner.style.display = "block";
  document.change_ownerform_name.lead_group_owner.style.display = "none";
  {rdelim}    
  
{rdelim}
function callSearch(searchtype)
{ldelim}
	for(i=1;i<=26;i++)
    	{ldelim}
        	var data_td_id = 'alpha_'+ eval(i);
        	getObj(data_td_id).className = 'searchAlph';
    	{rdelim}
    	gPopupAlphaSearchUrl = '';
	search_fld_val= $('bas_searchfield').options[$('bas_searchfield').selectedIndex].value;
	search_txt_val= encodeURIComponent(document.basicSearch.search_text.value);
        var urlstring = '';
        if(searchtype == 'Basic')
        {ldelim}
        		var p_tab = document.getElementsByName("parenttab");
                urlstring = 'search_field='+search_fld_val+'&searchtype=BasicSearch&search_text='+search_txt_val+'&';
                urlstring = urlstring + 'parenttab='+p_tab[0].value+ '&';
        {rdelim}
        else if(searchtype == 'Advanced')
        {ldelim}
        		checkAdvancedFilter();
				var advft_criteria = $('advft_criteria').value;
				var advft_criteria_groups = $('advft_criteria_groups').value;
				urlstring += '&advft_criteria='+advft_criteria+'&advft_criteria_groups='+advft_criteria_groups+'&';
				urlstring += 'searchtype=advance&'
        {rdelim}
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody:urlstring +'query=true&file=index&module={$MODULE}&action={$MODULE}Ajax&ajax=true&search=true',
			onComplete: function(response) {ldelim}
								$("status").style.display="none";
                                result = response.responseText.split('&#&#&#');
                                $("ListViewContents").innerHTML= result[2];
                                if(result[1] != '')
									alert(result[1]);
								$('basicsearchcolumns').innerHTML = '';
			{rdelim}
	       {rdelim}
        );
	return false
{rdelim}
function alphabetic(module,url,dataid)
{ldelim}
        for(i=1;i<=26;i++)
        {ldelim}
                var data_td_id = 'alpha_'+ eval(i);
                getObj(data_td_id).className = 'searchAlph';

        {rdelim}
        getObj(dataid).className = 'searchAlphselected';
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module='+module+'&action='+module+'Ajax&file=index&ajax=true&search=true&'+url,
			onComplete: function(response) {ldelim}
				$("status").style.display="none";
				result = response.responseText.split('&#&#&#');
				$("ListViewContents").innerHTML= result[2];
				if(result[1] != '')
			                alert(result[1]);
				$('basicsearchcolumns').innerHTML = '';
			{rdelim}
		{rdelim}
	);
{rdelim}

</script>

		{include file='Buttons_List.tpl'}
                                <div id="searchingUI" style="display:none;">
                                        <table border=0 cellspacing=0 cellpadding=0 width=100%>
                                        <tr>
                                                <td align=center>
                                                <img src="{'searching.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SEARCHING}"  title="{$APP.LBL_SEARCHING}">
                                                </td>
                                        </tr>
                                        </table>

                                </div>
                        </td>
                </tr>
                </table>
        </td>
</tr>
</table>

{*<!-- Contents -->*}
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
     <tr>
        <td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>

	<td class="showPanelBg" valign="top" width=100% style="padding:10px;">
	 <!-- SIMPLE SEARCH -->
<div id="searchAcc" style="display: block;position:relative;">
<form name="basicSearch" method="post" action="index.php" onSubmit="return callSearch('Basic');">
<table width="100%" cellpadding="5" cellspacing="0"  class="searchUIBasic small" align="center" border=0>
	<tr>
		<td class="searchUIName small" nowrap align="left">
		<span class="moduleName">{$APP.LBL_SEARCH}</span><br><span class="small"><a href="#" onClick="fnhide('searchAcc');show('advSearch');document.basicSearch.searchtype.value='advance';">{$APP.LBL_GO_TO} {$APP.LNK_ADVANCED_SEARCH}</a></span>
		<!-- <img src="themes/images/basicSearchLens.gif" align="absmiddle" alt="{$APP.LNK_BASIC_SEARCH}" title="{$APP.LNK_BASIC_SEARCH}" border=0>&nbsp;&nbsp;-->
		</td>
		<td class="small" nowrap align=right><b>{$APP.LBL_SEARCH_FOR}</b></td>
		<td class="small"><input type="text"  class="txtBox" style="width:120px" name="search_text"></td>
		<td class="small" nowrap><b>{$APP.LBL_IN}</b>&nbsp;</td>
		<td class="small" nowrap>
			<div id="basicsearchcolumns_real">
			<select name="search_field" id="bas_searchfield" class="txtBox" style="width:150px">
			 {html_options  options=$SEARCHLISTHEADER }
			</select>
			</div>
                        <input type="hidden" name="searchtype" value="BasicSearch">
                        <input type="hidden" name="module" value="{$MODULE}" id="curmodule">
						<input name="maxrecords" type="hidden" value="{$MAX_RECORDS}" id='maxrecords'>
                        <input type="hidden" name="parenttab" value="{$CATEGORY}">
			<input type="hidden" name="action" value="index">
                        <input type="hidden" name="query" value="true">
			<input type="hidden" name="search_cnt">
		</td>
		<td class="small" nowrap width=40% >
			  <input name="submit" type="button" class="crmbutton small create" onClick="callSearch('Basic');" value=" {$APP.LBL_SEARCH_NOW_BUTTON} ">&nbsp;
			  
		</td>
		<td class="small" valign="top" onMouseOver="this.style.cursor='pointer';" onclick="moveMe('searchAcc');searchshowhide('searchAcc','advSearch')">[x]</td>
	</tr>
	<tr>
		<td colspan="7" align="center" class="small">
			<table border=0 cellspacing=0 cellpadding=0 width=100%>
				<tr>
                                                {$ALPHABETICAL}
                                </tr>
                        </table>
		</td>
	</tr>
</table>
</form><br>
</div>
<!-- ADVANCED SEARCH -->
<div id="advSearch" style="display:none;">
<form name="advSearch" method="post" action="index.php" onSubmit="return callSearch('Advanced');">
	<table  cellspacing=0 cellpadding=5 width=100% class="searchUIAdv1 small" align="center" border=0>
		<tr>
			<td class="searchUIName small" nowrap align="left"><span class="moduleName">{$APP.LBL_SEARCH}</span><br><span class="small"><a href="#" onClick="show('searchAcc');fnhide('advSearch')">{$APP.LBL_GO_TO} {$APP.LNK_BASIC_SEARCH}</a></span></td>
			<td class="small" align="right" valign="top" onMouseOver="this.style.cursor='pointer';" onclick="moveMe('searchAcc');searchshowhide('searchAcc','advSearch')">[x]</td>
		</tr>
	</table>
	<table cellpadding="2" cellspacing="0" width="100%" align="center" class="searchUIAdv2 small" border=0>
		<tr>
			<td align="center" class="small" width=90%>
				{include file='AdvanceFilter.tpl' SOURCE='customview' COLUMNS_BLOCK=$FIELDNAMES}
			</td>
		</tr>
	</table>
		
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="searchUIAdv3 small" align="center">
		<tr>
			<td align="center" class="small"><input type="button" class="crmbutton small create" value=" {$APP.LBL_SEARCH_NOW_BUTTON} " onClick="callSearch('Advanced');">
			</td>
		</tr>
	</table>
</form><br>
</div>
</div>		
{*<!-- Searching UI -->*}

<div id="mergeDup" style="z-index:1;display:none;position:relative;">
	{include file="MergeColumns.tpl"}
</div>	 
	   <!-- PUBLIC CONTENTS STARTS-->
	  <div id="ListViewContents" class="small" style="width:100%;">
	  {if $MODULE neq "Documents"}
			{include file="ListViewEntries.tpl"}
	  {else}
			{include file="DocumentsListViewEntries.tpl"}
	  {/if}
	</div>

     </td>
        <td valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</table>

<!-- MassEdit Feature -->
<div id="massedit" class="layerPopup" style="display:none;width:80%;">
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="layerHeadingULine">
<tr>
	<td class="layerPopupHeading" align="left" width="60%">{$APP.LBL_MASSEDIT_FORM_HEADER}</td>
	<td>&nbsp;</td>
	<td align="right" width="40%"><img onClick="fninvsh('massedit');" title="{$APP.LBL_CLOSE}" alt="{$APP.LBL_CLOSE}" style="cursor:pointer;" src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0"></td>
</tr>
</table>
<div id="massedit_form_div"></div>

</div>
<!-- END -->
{if $MODULE eq 'Leads' or $MODULE eq 'Contacts' or $MODULE eq 'Accounts' or $MODULE eq 'Vendors'}
<form name="SendMail"><div id="sendmail_cont" style="z-index:100001;position:absolute;"></div></form>
{/if}

<script>
{literal}

function ajaxChangeStatus(statusname)
{
	$("status").style.display="inline";
	var viewid = document.getElementById('viewname').options[document.getElementById('viewname').options.selectedIndex].value;
	var idstring = document.getElementById('idlist').value;
	var searchurl= document.getElementById('search_url').value;
	var tplstart='&';
	if(gstart!='')
	{
		tplstart=tplstart+gstart;
	}
	if(statusname == 'status')
	{
		fninvsh('changestatus');
		var url='&leadval='+document.getElementById('lead_status').options[document.getElementById('lead_status').options.selectedIndex].value;
		var urlstring ="module=Users&action=updateLeadDBStatus&return_module=Leads"+tplstart+url+"&viewname="+viewid+"&idlist="+idstring+searchurl;
	}
	else if(statusname == 'owner')
	{
		if($("user_checkbox").checked)
		{
		    fninvsh('changeowner');
		    var url='&owner_id='+document.getElementById('lead_owner').options[document.getElementById('lead_owner').options.selectedIndex].value;
		    {/literal}
		        var urlstring ="module=Users&action=updateLeadDBStatus&return_module={$MODULE}"+tplstart+url+"&viewname="+viewid+"&idlist="+idstring+searchurl;
		    {literal}
		} else {
			fninvsh('changeowner');
			var url='&owner_id='+document.getElementById('lead_group_owner').options[document.getElementById('lead_group_owner').options.selectedIndex].value;
	      	{/literal}
		        var urlstring ="module=Users&action=updateLeadDBStatus&return_module={$MODULE}"+tplstart+url+"&viewname="+viewid+"&idlist="+idstring+searchurl;
        	{literal}
    	}
	}
	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: urlstring,
                        onComplete: function(response) {
                                $("status").style.display="none";
                                result = response.responseText.split('&#&#&#');
                                $("ListViewContents").innerHTML= result[2];
                                if(result[1] != '')
                                        alert(result[1]);
				$('basicsearchcolumns').innerHTML = '';
                        }
                }
        );
	
}
</script>
{/literal}

{if $MODULE eq 'Contacts'}
{literal}
<script>
function modifyimage(imagename)
{
	imgArea = getObj('dynloadarea');
        if(!imgArea)
        {
                imgArea = document.createElement("div");
                imgArea.id = 'dynloadarea';
                imgArea.setAttribute("style","z-index:100000001;");
                imgArea.style.position = 'absolute';
                imgArea.innerHTML = '<img width="260" height="200" src="'+imagename+'" class="thumbnail">';
		document.body.appendChild(imgArea);
        }
	PositionDialogToCenter(imgArea.id);
}

function PositionDialogToCenter(ID)
{
       var vpx,vpy;
       if (self.innerHeight) // Mozilla, FF, Safari and Opera
       {
               vpx = self.innerWidth;
               vpy = self.innerHeight;
       }
       else if (document.documentElement && document.documentElement.clientHeight) //IE

       {
               vpx = document.documentElement.clientWidth;
               vpy = document.documentElement.clientHeight;
       }
       else if (document.body) // IE
       {
               vpx = document.body.clientWidth;
               vpy = document.body.clientHeight;
       }

       //Calculate the length from top, left
       dialogTop = (vpy/2 - 280/2) + document.documentElement.scrollTop;
       dialogLeft = (vpx/2 - 280/2);

       //Position the Dialog to center
       $(ID).style.top = dialogTop+"px";
       $(ID).style.left = dialogLeft+"px";
       $(ID).style.display="block";
}

function removeDiv(ID){
        var node2Rmv = getObj(ID);
        if(node2Rmv){node2Rmv.parentNode.removeChild(node2Rmv);}
}

</script>
{/literal}
{/if}


