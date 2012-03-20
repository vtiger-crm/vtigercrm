/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

var gFolderid = 1;
var gselectedrowid = 0;
function gotoWebmail()
{
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
                	method: 'post',
			postBody: "module=Webmails&action=WebmailsAjax&config_chk=true",
			onComplete: function(response) {
				if(response.responseText.indexOf('SUCCESS') > -1)
					window.location.href = "index.php?module=Webmails&action=index&parenttab=My Home Page";
				else
					$('mailconfchk').style.display = 'block';
					
			}
		}
	);

}

function getEmailContents(id)
{
	$("status").style.display="inline";
	var rowid = 'row_'+id;
	getObj(rowid).className = 'emailSelected';
	if(gselectedrowid != 0 && gselectedrowid != id)
	{
		var prev_selected_rowid = 'row_'+gselectedrowid;
		getObj(prev_selected_rowid).className = 'prvPrfHoverOff';
	}
	gselectedrowid = id;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Emails&action=EmailsAjax&file=DetailView&mode=ajax&record='+id,
			onComplete: function(response) {
						$("status").style.display="none";
						$("EmailDetails").innerHTML = response.responseText;
					}
			}
		);
}

function getListViewEntries_js(module,url)
{
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
                	method: 'post',
			postBody: "module="+module+"&action="+module+"Ajax&file=ListView&ajax=true&"+url,
			onComplete: function(response) {
				$("status").style.display="none";
				$("email_con").innerHTML=response.responseText;
				execJS(document.getElementById('email_con'));
			}
		}
	);

}

function massDelete()
{
		var delete_selected_row = false;
		//added to fix the issue 4295
		var select_options  =  document.getElementsByName('selected_id');
		var x = select_options.length;

	idstring = "";
        xx = 0;
        for(i = 0; i < x ; i++)
        {
            if(select_options[i].checked)
            {
            	idstring = select_options[i].value +";"+idstring
		if(select_options[i].value == gselectedrowid)
		{
			gselectedrowid = 0;
			delete_selected_row = true;						
		}
                xx++
            }
        }
                if (xx > 0)
                {
                        document.massdelete.idlist.value=idstring;
                }
                else
                {
                        alert(alert_arr.SELECT);
                        return false;
                }
		if(confirm(alert_arr.DELETE + xx + alert_arr.RECORDS))
		{	
			getObj('search_text').value = '';
			show("status");
			if(!delete_selected_row)
			{
				new Ajax.Request(
						'index.php',
						{queue: {position: 'end', scope: 'command'},
						method: 'post',
						postBody: "module=Users&action=massdelete&folderid="+gFolderid+"&return_module=Emails&idlist="+idstring,
						onComplete: function(response) {
						$("status").style.display="none";
						$("email_con").innerHTML=response.responseText;
						execJS(document.getElementById('email_con'));
						}
						}
						);
			}
			else	
			{
				new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: "module=Users&action=massdelete&folderid="+gFolderid+"&return_module=Emails&idlist="+idstring,
                                onComplete: function(response) {
                                                $("status").style.display="none";
						$("email_con").innerHTML=response.responseText;
                                                execJS($('email_con'));
                                                $('EmailDetails').innerHTML = '<table valign="top" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="forwardBg"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="padding-top: 10px;" bgcolor="#ffffff" height="300" valign="top"></td></tr></tbody></table>';
                                                $("subjectsetter").innerHTML='';
                                }
                        }
                );
			}
		}
		else
		{
			return false;
		}
}

function DeleteEmail(id)
{
	if(confirm(alert_arr.SURE_TO_DELETE))
	{	
		getObj('search_text').value = '';
		gselectedrowid = 0;
		$("status").style.display="inline";
                new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: "module=Users&action=massdelete&return_module=Emails&folderid="+gFolderid+"&idlist="+id,
                                onComplete: function(response) {
                                                $("status").style.display="none";
						$("email_con").innerHTML=response.responseText;
						execJS($('email_con'));
                                                $('EmailDetails').innerHTML = '<table valign="top" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="forwardBg"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="padding-top: 10px;" bgcolor="#ffffff" height="300" valign="top"></td></tr></tbody></table>';
                                                $("subjectsetter").innerHTML='';
                                }
                        }
                );
	}
	else
	{
		return false;
	}
}
function Searchfn()
{
	gselectedrowid = 0;
	var osearch_field = document.getElementById('search_field');
	var search_field = osearch_field.options[osearch_field.options.selectedIndex].value;
	var search_text = document.getElementById('search_text').value;
	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: "module=Emails&action=EmailsAjax&ajax=true&file=ListView&folderid="+gFolderid+"&search=true&search_field="+search_field+"&search_text="+search_text,
                        onComplete: function(response) {
                        		$("email_con").innerHTML=response.responseText;
			                $("status").style.display="none";
                                        $('EmailDetails').innerHTML = '<table valign="top" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="forwardBg"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="padding-top: 10px;" bgcolor="#ffffff" height="300" valign="top"></td></tr></tbody></table>';
                                        $("subjectsetter").innerHTML='';
                                        execJS($('email_con'));
                        }
                }
        );
}

