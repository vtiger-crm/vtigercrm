/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/

//Added after 504 for renaming a folder
function UpdateAjaxSave(label,fid,fldname,fileOrFolder)
{
        fldVal=$('txtbox_'+label).value;
	if(fldVal.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
        {
                alert(alert_arr.FOLDERNAME_EMPTY);
                return false;
        }
        if(fldVal.replace(/^\s+/g, '').replace(/\s+$/g, '').length>=21)
        {
                alert(alert_arr.FOLDER_NAME_TOO_LONG);
                return false;
        }
	if(fldVal.match(/['"\\%+?]/))
        {
                alert(alert_arr.NO_SPECIAL_CHARS_DOCS);
                return false;
        }
        if(fileOrFolder == 'file')
                var url='action=DocumentsAjax&mode=ajax&file=Save&module=Documents&fileid='+fid+'&fldVal='+fldVal+'&fldname='+fldname+'&act=ajaxEdit';
        else
	{
                var foldername = encodeURIComponent(fldVal);
				foldername = foldername.replace(/^\s+/g, '').replace(/\s+$/g, '');
                foldername = foldername.replace(/&/gi,'*amp*');		
                var url='action=DocumentsAjax&mode=ajax&file=SaveFolder&module=Documents&record='+fid+'&foldername='+fldVal+'&savemode=Save';
	}
	if(fldname == 'status')
        {
                fldVal = $('txtbox_'+label).options[$('txtbox_'+label).options.selectedIndex].text;
                gtempselectedIndex = $('txtbox_'+label).options.selectedIndex;
        }
	$('status').style.display="block";
        new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: url,
                        onComplete: function(response) {
                        		var item = response.responseText;
					$('status').style.display="none";
					if(item.indexOf("Failure") > -1 )
		                        {
                		                $("lblError").innerHTML="<table cellpadding=0 cellspacing=0 border=0 width=100%><tr><td class=small bgcolor=red><font color=white size=2><b>"+alert_arr.LBL_UNABLE_TO_UPDATE+"</b></font></td></tr></table>";
		                                setTimeout(hidelblError,3000);
                		        }
					else if(item.indexOf('DUPLICATE_FOLDERNAME') > -1)
					{
						alert(alert_arr.DUPLICATE_FOLDER_NAME);
					}
                                        else
					{
						$('dtlview_'+label).innerHTML = fldVal;
                                        	eval("hndCancel('dtlview_"+label+"','editarea_"+label+"','"+label+"')");
						if(fldname == 'status')
                        	                        $('txtbox_'+label).selectedIndex = gtempselectedIndex;
						else
                        	                        $('txtbox_'+label).value = fldVal;
						eval(item);

					}
					
                                }
                        }
                );

}

function closeFolderCreate()
{
        $('folder_id').value = '';
        $('folder_name').value = '';
        $('folder_desc').value='';
        fninvsh('orgLay')
}

function AddFolder()
{
        var fldrname=getObj('folder_name').value;
		var fldrdesc=getObj('folder_desc').value;
        if(fldrname.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
        {
                alert(alert_arr.FOLDERNAME_EMPTY);
                return false;
        }
        if(fldrname.replace(/^\s+/g, '').replace(/\s+$/g, '').length>=21)
        {
                alert(alert_arr.FOLDER_NAME_TOO_LONG);
                return false;
        }
        if(fldrdesc.replace(/^\s+/g, '').replace(/\s+$/g, '').length>=51)
        {
                alert(alert_arr.FOLDER_DESCRIPTION_TOO_LONG);
                return false;
        }
	if(fldrname.match(/['"\\%+]/) || fldrdesc.match(/['"\\%+]/))
        {
                alert(alert_arr.NO_SPECIAL_CHARS_DOCS+alert_arr.NAME_DESC);
                return false;
        }	
	if(fldrname.match(/[?]+$/) || fldrname.match(/[?]+/))
	{
		alert(alert_arr.NO_SPECIAL_CHARS_DOCS);
		return false;
	}
                fninvsh('orgLay');
                var foldername = encodeURIComponent(getObj('folder_name').value);
                var folderdesc = encodeURIComponent(getObj('folder_desc').value);
		foldername = foldername.replace(/^\s+/g, '').replace(/\s+$/g, '');
                foldername = foldername.replace(/&/gi,'*amp*');
                folderdesc = folderdesc.replace(/^\s+/g, '').replace(/\s+$/g, '');
                folderdesc = folderdesc.replace(/&/gi,'*amp*');
                getObj('folder_name').value = '';
                getObj('folder_desc').value = '';
                var mode = getObj('fldrsave_mode').value;
                if(mode == 'save')
                {
                        url ='&savemode=Save&foldername='+foldername+'&folderdesc='+folderdesc;
                }
                getObj('fldrsave_mode').value = 'save';
		$('status').style.display = 'block';
                new Ajax.Request(
                                'index.php',
                                {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'action=DocumentsAjax&mode=ajax&file=SaveFolder&module=Documents'+url,
                                onComplete: function(response) {
					var item = response.responseText;
					$('status').style.display = 'none';
					if(item.indexOf('Failure') > -1)
					{
						$('lblError').innerHTML = "<table cellpadding=0 cellspacing=0 border=0 width=100%><tr><td class=small bgcolor=red><font color=white size=2><b>"+alert_arr.LBL_UNABLE_TO_ADD_FOLDER+"</b></font></td></tr></table>";
						setTimeOutFn();	
					}
					else if(item.indexOf('DUPLICATE_FOLDERNAME') > -1)
					{
						alert(alert_arr.DUPLICATE_FOLDER_NAME);
					}
					else
					{
						getObj("ListViewContents").innerHTML = item;
					}
                                }
                        }
                );
}


function DeleteFolderCheck(folderId)
{
        gtempfolderId = folderId;
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: "module=Documents&action=DocumentsAjax&mode=ajax&file=DeleteFolder&deletechk=true&folderid="+folderId,
                        onComplete: function(response) {
                        var item = response.responseText;
                       	if(item.indexOf("NOT_PERMITTED") > -1) {
							alert(alert_arr.NOT_PERMITTED);
							return false;
						}
						else if(item.indexOf("FAILURE") > -1)
                        {
                                alert(alert_arr.LBL_FOLDER_SHOULD_BE_EMPTY);
                        }
						else {
                            if(confirm(alert_arr.LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE_FOLDER))
                            {
                                DeleteFolder(gtempfolderId);                            
                            }
                            }
                        }
                }
        );
}

function DeleteFolder(folderId)
{
	$('status').style.display = "block";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: "module=Documents&action=DocumentsAjax&mode=ajax&file=DeleteFolder&folderid="+folderId,
                        onComplete: function(response) {
                                        var item = response.responseText;
										$('status').style.display = "none";
                                        if(item.indexOf("FAILURE") > -1)
                                 			alert(alert_arr.LBL_ERROR_WHILE_DELETING_FOLDER);
                                        else
											$('ListViewContents').innerHTML = item;
		               }
                }
        );
}

function MoveFile(id,foldername)
{
		fninvsh('movefolderlist');
        var select_options  =  document.getElementById('allselectedboxes').value;
        var x = select_options.split(";");
	    var searchurl= document.getElementById('search_url').value;
        var count=x.length
        var viewid =getviewId();
        var idstring = "";
        if (count > 1)
        {
            document.getElementById('idlist').value=select_options;
            idstring = select_options;
        }
        else
        {
            alert(alert_arr.SELECT);
            return false;
        }

    if(idstring != '')
	{
		if(confirm(alert_arr.LBL_ARE_YOU_SURE_TO_MOVE_TO + foldername + alert_arr.LBL_FOLDER))
        	{
			$('status').style.display = "block";
			new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'action=DocumentsAjax&file=MoveFile&from_folderid=0&module=Documents&folderid='+id+'&idlist='+idstring,
                                onComplete: function(response) {
						var item = response.responseText;
						$('status').style.display = "none";
						if(item.indexOf("NOT_PERMITTED") > -1 )                                                                     							{
							$("lblError").innerHTML="<table cellpadding=0 cellspacing=0 border=0 width=100%><tr><td class=small bgcolor=red><font color=white size=2><b>"+alert_arr.NOT_PERMITTED+"</b></font></td></tr></table>";
							setTimeout(hidelblError,3000);
						}
						else
							getObj('ListViewContents').innerHTML = item;
           				}

					}
			);
		}else{
			return false;
		}
			
	}else
	{
		alert(alert_arr.LBL_SELECT_ONE_FILE);
		return false;
	}
}

function dldCntIncrease(fileid)
{
	new Ajax.Request(
            'index.php',
            {queue: {position: 'end', scope: 'command'},
             method: 'post',
             postBody: 'action=DocumentsAjax&mode=ajax&file=SaveFile&module=Documents&file_id='+fileid+"&act=updateDldCnt",
             onComplete: function(response) {
                }
                }
                );
}


function checkFileIntegrityDetailView(noteid)
{
	$('vtbusy_integrity_info').style.display='';
	new Ajax.Request(
            'index.php',
            {queue: {position: 'end', scope: 'command'},
             method: 'post',
             postBody: 'module=Documents&action=DocumentsAjax&mode=ajax&file=SaveFile&act=checkFileIntegrityDetailView&noteid='+noteid,
             onComplete: function(response) {
             	var item = response.responseText;
				if(item.indexOf('file_available') > -1)
				{
							$('vtbusy_integrity_info').style.display='none';
							$('integrity_result').innerHTML='<br><br>&nbsp;&nbsp;&nbsp;<font style=color:green>'+alert_arr.LBL_FILE_CAN_BE_DOWNLOAD+'</font>';
							$('integrity_result').style.display='';
							setTimeout(hideresult,4000);
				}
				else if(item.indexOf('file_not_available') > -1)
				{
							$('vtbusy_integrity_info').style.display='none';
							$('integrity_result').innerHTML='<br><br>&nbsp;&nbsp;&nbsp;<font style=color:red>'+alert_arr.LBL_DOCUMENT_NOT_AVAILABLE+'</font>';
							$('integrity_result').style.display='';	
							setTimeout(hideresult,6000);	
				}
				else if(item.indexOf('lost_integrity') > -1)
				{
							$('vtbusy_integrity_info').style.display='none';
							$('integrity_result').innerHTML='<br><br>&nbsp;&nbsp;&nbsp;<font style=color:red>'+alert_arr.LBL_DOCUMENT_LOST_INTEGRITY+'</font>';
							$('integrity_result').style.display='';	
							setTimeout(hideresult,6000);	
				}
			}
		}
	);
}

function hideresult()
{
	$('integrity_result').style.display = 'none';
}

function add_data_to_relatedlist(entity_id,recordid) {

        opener.document.location.href="index.php?module={RETURN_MODULE}&action=updateRelations&smodule={SMODULE}&destination_module=Products&entityid="+entity_id+"&parentid="+recordid;
}

