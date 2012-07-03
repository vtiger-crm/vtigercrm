/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
//added by raju for emails

function eMail(module,oButton) {
	var select_options = document.getElementById('allselectedboxes').value;
	var excludedRecords = document.getElementById("excludedRecords").value;
	var numOfRows = document.getElementById('numOfRows').value;
	var searchurl = document.getElementById('search_url').value;
	var viewid = getviewId();
	var url = "&viewname="+viewid+"&excludedRecords="+excludedRecords+"&searchurl="+searchurl;
	//Added to remove the semi colen ';' at the end of the string.done to avoid error.
	var idstring = "";

	if(select_options == 'all'){

        document.getElementById('idlist').value=idstring;
        allids = select_options;
		var count=numOfRows;

    } else {
		var x = select_options.split(";");
		var count=x.length;
		select_options=select_options.slice(0,(select_options.length-1));
   
		if (count > 1) {
			idstring=select_options.replace(/;/g,':')
			document.getElementById('idlist').value=idstring;
		}
		else {
			alert(alert_arr.SELECT);
			return false;
		}
		var allids = document.getElementById('idlist').value;
	}

	if(count > getMaxMassOperationLimit()) {
		var confirm_str = alert_arr.MORE_THAN_500;
		if(confirm(confirm_str)) {
			var confirm_status = true;
		} else {
			return false;
		}
	} else {
		confirm_status = true;
	}

	if(confirm_status){
		fnvshobj(oButton,'sendmail_cont');
		sendmail(module,allids,url);
	}
}


function massMail(module) {

	var select_options  =  document.getElementsByName('selected_id');
	x = select_options.length;
	var viewid =getviewId();		
	idstring = "";

	xx = 0;
	for(i = 0; i < x ; i++)
	{
		if(select_options[i].checked)
		{
			idstring = select_options[i].value +";"+idstring
				xx++
		}
	}
	if (xx != 0)
	{
		document.getElementById('idlist').value=idstring;
	}
	else
	{
		alert(alert_arr.SELECT);
		return false;
	}
	document.massdelete.action="index.php?module=CustomView&action=SendMailAction&return_module="+module+"&return_action=index&viewname="+viewid;
}

//added by rdhital for better emails
function set_return_emails(entity_id,email_id,parentname,emailadd,emailadd2,perm){
	if(perm == 0 || perm == 3) {
		if(emailadd2 == '')	{			
			alert(alert_arr.LBL_DONT_HAVE_EMAIL_PERMISSION);
			return false;
		}
		else
			emailadd = emailadd2;
	} else {
		if(emailadd == '')
			emailadd = emailadd2;
	}	
	if(emailadd != '') {
		window.opener.document.EditView.parent_id.value = window.opener.document.EditView.parent_id.value+entity_id+'@'+email_id+'|';
		window.opener.document.EditView.parent_name.value = window.opener.document.EditView.parent_name.value+parentname+'<'+emailadd+'>,';
		window.opener.document.EditView.hidden_toid.value = emailadd+','+window.opener.document.EditView.hidden_toid.value;
		window.close();
	} else {
		alert('"'+parentname+alert_arr.DOESNOT_HAVE_AN_MAILID);
		return false;
	}
}	
//added by raju for emails

function validate_sendmail(idlist,module){
	var j=0;
	if(idlist == 'all'){
		var viewid = $('viewid').value;
		var excludedRecords = $("excludedRecords").value;
		var numOfRows = $('numOfRows').value;
		var searchurl = $('search_url').value;
		var url1 = "&viewname="+viewid+"&excludedRecords="+excludedRecords+"&searchurl="+searchurl;
	} else if(idlist == 'relatedListSelectAll'){
		var recordid = $('recordid').value;
		var excludedRecords = $('excludedRecords').value;
		url1 = "&recordid="+recordid+"&excludedRecords="+excludedRecords;
	} else {
		url1 = '';
	}
	var chk_emails = document.SendMail.elements.length;
	var oFsendmail = document.SendMail.elements
	email_type = new Array();
	for(var i=0 ;i < chk_emails ;i++) {
		if(oFsendmail[i].type != 'button'){
			if(oFsendmail[i].checked != false){
				email_type [j++]= oFsendmail[i].value;
			}
		}
	}
	if(email_type != '') {
		var field_lists = email_type.join(':');
		var url= 'index.php?module=Emails&action=EmailsAjax&pmodule='+module+'&file=EditView&sendmail=true&idlist='+idlist+'&field_lists='+field_lists+url1;
		openPopUp('xComposeEmail',this,url,'createemailWin',820,689,'menubar=no,toolbar=no,location=no,status=no,resizable=no');
		fninvsh('roleLay');
		return true;
	} else {
		alert(alert_arr.SELECT_MAILID);
	}
}
function sendmail(module,idstrings,url) {
	new Ajax.Request(
		'index.php',
		{
			queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: "module=Emails&return_module="+module+"&action=EmailsAjax&file=mailSelect&idlist="+idstrings+url,
			onComplete: function(response) {
				if(response.responseText == "Mail Ids not permitted" || response.responseText == "No Mail Ids")
				{
					var url= 'index.php?module=Emails&action=EmailsAjax&pmodule='+module+'&file=EditView&sendmail=true';
					openPopUp('xComposeEmail',this,url,'createemailWin',820,689,'menubar=no,toolbar=no,location=no,status=no,resizable=no');
				}
				else
					getObj('sendmail_cont').innerHTML = response.responseText;
			}
		}
	);
}

function rel_eMail(module,oButton,relmod){
	var allids='';
	if($(module+'_'+relmod+'_selectallActivate').value == 'true'){
		var recordid = document.getElementById('recordid').value;
		var excludedRec = $(module+'_'+relmod+'_excludedRecords').value;
		allids = 'relatedListSelectAll';
		var url = "&parent_module=Campaigns&excludedRecords="+excludedRec+"&recordid="+recordid;
	} else {
		var select_options = '';
		var cookie_val = get_cookie(relmod+"_all");
		if(cookie_val != null)
			select_options = cookie_val;
		//Added to remove the semi colen ';' at the end of the string.done to avoid error.
		var x = select_options.split(";");
		var viewid = '';
		var count = x.length
		var idstring = "";
		select_options = select_options.slice(0,(select_options.length-1));

		if (count > 1){
			idstring=select_options.replace(/;/g,':')
			allids=idstring;
		} else {
			alert(alert_arr.SELECT);
			return false;
		}
	}
	fnvshobj(oButton,'sendmail_cont');
	sendmail(relmod,allids,url);
	set_cookie(relmod+"_all","");
}