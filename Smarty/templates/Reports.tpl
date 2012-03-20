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

{*<!-- module header -->*}
<script language="JavaScript" type="text/javascript" src="modules/Reports/Reports.js"></script>

<!-- Toolbar -->
<TABLE border=0 cellspacing=0 cellpadding=0 width=100% class=small>
	<tr><td style="height:2px"></td></tr>
	<tr>
	<td class=small width="60%">

	<table border=0 cellspacing=0 cellpadding=0>
	<tr>
	<td>{include file="Buttons_List1.tpl"}</td>		
	<td style="width:20px">&nbsp;</td>
	<td>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td style="padding-right:5px"><a href="javascript:;" onclick="gcurrepfolderid=0;fnvshobj(this,'reportLay');"><img src="{'reportsCreate.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_CREATE_REPORT}..." title="{$MOD.LBL_CREATE_REPORT}..." border=0></a></td>
                        <td>&nbsp;</td>
            <td style="padding-right:5px"><a href="javascript:;" onclick="createrepFolder(this,'orgLay');"><img src="{'reportsFolderCreate.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.Create_New_Folder}..." title="{$MOD.Create_New_Folder}..." border=0></a></td>
                        <td>&nbsp;</td>
            <td style="padding-right:5px"><a href="javascript:;" onclick="fnvshobj(this,'folderLay');"><img src="{'reportsMove.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.Move_Reports}..." title="{$MOD.Move_Reports}..." border=0></a></td>
                        <td>&nbsp;</td>
            <td style="padding-right:5px"><a href="javascript:;" onClick="massDeleteReport();"><img src="{'reportsDelete.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_DELETE_FOLDER}..." title="{$MOD.Delete_Report}..." border=0></a></td>
			</tr>
		</table>
	</td>
	</tr>
	</table>

	</td>
	</tr>
	<tr><td style="height:2px"></td></tr>
</TABLE>


<div id="reportContents">
	{include file="ReportContents.tpl"}
</div>
<!-- Reports Table Ends Here -->

<!-- POPUP LAYER FOR CREATE NEW REPORT -->
<div style="display: none; left: 193px; top: 106px;width:155px;" id="reportLay" onmouseout="fninvsh('reportLay')" onmouseover="fnvshNrm('reportLay')">
<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody><tr><td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;"><b>{$MOD.LBL_CREATE_NEW} :</b></td></tr>
	<tr>
	<td>
	{foreach item=modules key=modulename from=$REPT_MODULES}
	<a href="javascript:CreateReport('{$modulename}');" class="drop_down">- {$modules}</a>
	{/foreach}
	</td>
	</tr>
	</tbody>
</table>
</div>
<!-- END OF POPUP LAYER -->

<!-- Add new Folder UI starts -->
<div id="orgLay" style="display:none;width:350px;" class="layerPopup">
	<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
	<tr>
		<td class="genHeaderSmall" nowrap align="left" width="30%" id="editfolder_info">{$MOD.LBL_ADD_NEW_GROUP}</td>
		<td align="right"><a href="javascript:;" onClick="closeEditReport();"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0"></a></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
	<tr>
		<td class="small">
			<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
			<tr>
				<td align="right" nowrap class="cellLabel small"><b>{$MOD.LBL_REP_FOLDER_NAME} </b></td>
				<td align="left">
				<input id="folder_id" name="folderId" type="hidden" value=''>
				<input id="fldrsave_mode" name="folderId" type="hidden" value='save'>
				<input id="folder_name" name="folderName"  type="text" width="100%" solid="#666666" font-family="Arial, Helvetica,sans-serif" font-size="11px">
				</td>
			</tr>
			<tr>
				<td class="cellLabel small" align="right" nowrap><b>{$MOD.LBL_REP_FOLDER_DESC} </b></td>
				<td class="cellText small" align="left"><input id="folder_desc" name="folderDesc"  type="text" width="100%" solid="#666666" font-family="Arial, Helvetica,sans-serif" font-size="11px"></td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
	<tr>
		<td class="small" align="center">
		<input name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="crmbutton small save" onClick="AddFolder();" type="button">&nbsp;&nbsp;
		<input name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" onclick="closeEditReport();" type="button">
		</td>
	</tr>
	</table>
</div>
<!-- Add new folder UI ends -->




{*<!-- Contents -->*}
{literal}
<script>
function createrepFolder(oLoc,divid)
{
	{/literal}
	$('editfolder_info').innerHTML=' {$MOD.LBL_ADD_NEW_GROUP} ';
	{literal}
	getObj('fldrsave_mode').value = 'save';	
	$('folder_id').value = '';
	$('folder_name').value = '';
	$('folder_desc').value='';
	fnvshobj(oLoc,divid);
}
function closeEditReport()
{
	$('folder_id').value = '';
	$('folder_name').value = '';
	$('folder_desc').value='';
	fninvsh('orgLay')
}
function DeleteFolder(id)
{
	var title = 'folder'+id;
	var fldr_name = getObj(title).innerHTML;
	{/literal}
        if(confirm("{$APP.DELETE_FOLDER_CONFIRMATION}"+fldr_name +"' ?"))
        {literal}
	{
		new Ajax.Request(
			'index.php',
	                {queue: {position: 'end', scope: 'command'},
        	                method: 'post',
                	        postBody: 'action=ReportsAjax&mode=ajax&file=DeleteReportFolder&module=Reports&record='+id,
                        	onComplete: function(response) {
							var item = trim(response.responseText);
							if(item.charAt(0)=='<')
						        getObj('customizedrep').innerHTML = item;
						    else
						    	alert(item);
                        	}
                	}
        	);
	}
	else
	{
		return false;
	}
}

function AddFolder()
{
	if(getObj('folder_name').value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{
		{/literal}
                alert('{$APP.FOLDERNAME_CANNOT_BE_EMPTY}');
                return false;
                {literal}
	}
	else if(getObj('folder_name').value.replace(/^\s+/g, '').replace(/\s+$/g, '').length > 20 )
	{
		{/literal}
                alert('{$APP.FOLDER_NAME_ALLOW_20CHARS}');
                return false;
                {literal}
	}
	else if((getObj('folder_name').value).match(/['"<>/\+]/) || (getObj('folder_desc').value).match(/['"<>/\+]/))
    {
            alert(alert_arr.SPECIAL_CHARS+' '+alert_arr.NOT_ALLOWED+alert_arr.NAME_DESC);
            return false;
    }	
	/*else if((!CharValidation(getObj('folder_name').value,'namespace')) || (!CharValidation(getObj('folder_desc').value,'namespace')))
	{
			alert(alert_arr.NO_SPECIAL +alert_arr.NAME_DESC);
			return false;
	}*/
	else
	{
		var foldername = encodeURIComponent(getObj('folder_name').value);
		new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'action=ReportsAjax&mode=ajax&file=CheckReport&module=Reports&check=folderCheck&folderName='+foldername,
                                onComplete: function(response) {
				var folderid = getObj('folder_id').value;
				var resresult =response.responseText.split("::");
				var mode = getObj('fldrsave_mode').value;
				if(resresult[0] != 0 &&  mode =='save' && resresult[0] != 999)
				{
					{/literal}
					alert("{$APP.FOLDER_NAME_ALREADY_EXISTS}");
					return false;
					{literal}
				}
				else if(((resresult[0] != 1 && resresult[0] != 0) || (resresult[0] == 1 && resresult[0] != 0 && resresult[1] != folderid )) &&  mode =='Edit' && resresult[0] != 999)
					{
						{/literal}
                                                alert("{$APP.FOLDER_NAME_ALREADY_EXISTS}");
                                                return false;
                                                {literal}
					}
				else if(response.responseText == 999) // 999 check for special chars
					{
                                                {/literal}
                                                alert("{$APP.SPECIAL_CHARS_NOT_ALLOWED}");
                                                return false;
                                                {literal}
					}
				else
					{
						fninvsh('orgLay');
						var folderdesc = encodeURIComponent(getObj('folder_desc').value);
						getObj('folder_name').value = '';
						getObj('folder_desc').value = '';
						foldername = foldername.replace(/^\s+/g, '').replace(/\s+$/g, '');
                                                foldername = foldername.replace(/&/gi,'*amp*');
                                                folderdesc = folderdesc.replace(/^\s+/g, '').replace(/\s+$/g, '');
                                                folderdesc = folderdesc.replace(/&/gi,'*amp*');
						if(mode == 'save')
						{
							url ='&savemode=Save&foldername='+foldername+'&folderdesc='+folderdesc;
						}
						else
						{
							var folderid = getObj('folder_id').value;
							url ='&savemode=Edit&foldername='+foldername+'&folderdesc='+folderdesc+'&record='+folderid;
						}
						getObj('fldrsave_mode').value = 'save';
						new Ajax.Request(
				                        'index.php',
				                        {queue: {position: 'end', scope: 'command'},
			                                method: 'post',
			                                postBody: 'action=ReportsAjax&mode=ajax&file=SaveReportFolder&module=Reports'+url,
			                                onComplete: function(response) {
			                                        var item = response.responseText;
                        			                getObj('customizedrep').innerHTML = item;
			                                }
						}
			                      
				                );
					}
				}
			}
			);
		
	}
}


function EditFolder(id,name,desc)
{
{/literal}
	$('editfolder_info').innerHTML= ' {$MOD.LBL_RENAME_FOLDER} '; 	
{literal}
	getObj('folder_name').value = name;
	getObj('folder_desc').value = desc;
	getObj('folder_id').value = id;
	getObj('fldrsave_mode').value = 'Edit';
}
function massDeleteReport()
{
	var folderids = getObj('folder_ids').value;
	var folderid_array = folderids.split(',')
	var idstring = '';
	var count = 0;
	for(i=0;i < folderid_array.length;i++)
	{
		var selectopt_id = 'selected_id'+folderid_array[i];
		var objSelectopt = getObj(selectopt_id);
		if(objSelectopt != null)
		{
			var length_folder = getObj(selectopt_id).length;
			if(length_folder != undefined)
			{
				var cur_rep = getObj(selectopt_id);
				for(row = 0; row < length_folder ; row++)
				{
					var currep_id = cur_rep[row].value;
					if(cur_rep[row].checked)
					{
						count++;
						idstring = currep_id +':'+idstring;
					}
				}
			}else
			{	
				if(getObj(selectopt_id).checked)
				{
					count++;
					idstring = getObj(selectopt_id).value +':'+idstring;
				}
			}
		}
	}
	if(idstring != '')
	{	{/literal}
                if(confirm("{$APP.DELETE_CONFIRMATION}"+count+"{$APP.RECORDS}"))
                {literal}
       		{
			new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'action=ReportsAjax&mode=ajax&file=Delete&module=Reports&idlist='+idstring,
                                onComplete: function(response) {
                                        var item = response.responseText;
                                        	getObj('customizedrep').innerHTML = item;
                                }
                        }
                );
		}else
		{
			return false;
		}
			
	}else
	{
		{/literal}
                alert('{$APP.SELECT_ATLEAST_ONE_REPORT}');
                return false;
                {literal}
	}
}
function DeleteReport(id)
{
	{/literal}
        if(confirm("{$APP.DELETE_REPORT_CONFIRMATION}"))
        {literal}
	{
		new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'action=ReportsAjax&file=Delete&module=Reports&record='+id,
                                onComplete: function(response) {
					getObj('reportContents').innerHTML = response.responseText;
                                }
                        }
                );
	}else
	{
		return false;
	}
}
function MoveReport(id,foldername)
{
	fninvsh('folderLay');
	var folderids = getObj('folder_ids').value;
	var folderid_array = folderids.split(',')
	var idstring = '';
	var count = 0;
	for(i=0;i < folderid_array.length;i++)
	{
		var selectopt_id = 'selected_id'+folderid_array[i];
		var objSelectopt = getObj(selectopt_id);
		if(objSelectopt != null)
		{
			var length_folder = getObj(selectopt_id).length;
			if(length_folder != undefined)
			{
				var cur_rep = getObj(selectopt_id);
				for(row = 0; row < length_folder ; row++)
				{
					var currep_id = cur_rep[row].value;
					if(cur_rep[row].checked)
					{
						count++;
						idstring = currep_id +':'+idstring;
					}
				}
			}else
			{	
				if(getObj(selectopt_id).checked)
				{
					count++;
					idstring = getObj(selectopt_id).value +':'+idstring;
				}
			}
		}
	}
	if(idstring != '')
	{
		{/literal}
                if(confirm("{$APP.MOVE_REPORT_CONFIRMATION}"+foldername+"{$APP.FOLDER}"))
                {literal}
        	{
			new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'action=ReportsAjax&file=ChangeFolder&module=Reports&folderid='+id+'&idlist='+idstring,
                                onComplete: function(response) {
                                	        getObj('reportContents').innerHTML = response.responseText;
                	                }
                        	}
	                );
		}else
		{
			return false;
		}
			
	}else
	{
		{/literal}
                alert('{$APP.SELECT_ATLEAST_ONE_REPORT}');
                return false;
                {literal}
	}
}
</script>
{/literal}
