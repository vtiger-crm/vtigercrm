/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
function load_webmail(mid,hasAttachment) {
        var node = $("row_"+mid);
	preview_id = mid;
	if(typeof($('fnt_subject_'+mid)) != "undefined" && $('fnt_subject_'+mid).color=="green")
	{
		$('fnt_subject_'+mid).color="";
		$('fnt_date_'+mid).color="";
		$('fnt_from_'+mid).color="";

	}
	if(node.className == "mailSelected") {
	var unread  = parseInt($(mailbox+"_unread").innerHTML);
	if(unread != 0)
	{
			var curUnread;
			curUnread = unread -1;
			if(curUnread == 0)
				$(mailbox+"_count").style.display="none";
			else
				$(mailbox+"_unread").innerHTML = curUnread;
		}
			

                $("unread_img_"+mid).removeChild($("unread_img_"+mid).firstChild);
                $("unread_img_"+mid).appendChild(Builder.node('a',
                        {href: 'javascript:;', onclick: 'OpenComposer('+mid+',\'reply\')'},
                        [Builder.node('img',{src: 'themes/images/openmail.gif', border: '0', width: '12', height: '12'})]
                ));
	}
        node.className='read_email';
	//Fix for webmails body display in IE - dartagnanlaf
	/*
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Webmails&action=body&mailid=' + mid + '&mailbox='+mailbox,
                        onComplete: function(response) {
                                document.getElementById("body_area").innerHTML=response.responseText;
                        }
                }
          );
	*/

	oiframe = $("email_description");
	oiframe.src = 'index.php?module=Webmails&action=body&theme='+theme+'&mailid='+mid+'&mailbox='+mailbox;
        //$("body_area").appendChild(Builder.node('iframe',{src: 'index.php?module=Webmails&action=body&mailid='+mid+'&mailbox='+mailbox, width: '100%', height: '210', frameborder: '0'},'You must enable iframes'));

        tmp = document.getElementsByClassName("previewWindow");
        for(var i=0;i<tmp.length;i++) {
                if(tmp[i].style.visibility === "hidden") {
                        tmp[i].style.visibility="visible";
                }
        }
	if($("preview1").style.visibility === "hidden" || $("preview2").style.visibility === "hidden") {
		$("preview1").style.visibility="visible";
		$("preview2").style.visibility="visible";
	}
	
        $("delete_button").removeChild($("delete_button").firstChild);
        $("delete_button").appendChild(Builder.node('input',{type: 'button', name: 'Button', value: alert_arr.LBL_DELETE_EMAIL, className: 'buttonok', onclick: 'runEmailCommand(\'delete_msg\','+mid+')'}));

        $("reply_button_all").removeChild($("reply_button_all").firstChild);
        $("reply_button_all").appendChild(Builder.node('input',{type: 'button', name: 'reply', value: alert_arr.LBL_REPLY_TO_ALL, className: 'buttonok', onclick: 'OpenComposer('+mid+',\'replyall\')'}));

        $("reply_button").removeChild($("reply_button").firstChild);
        $("reply_button").appendChild(Builder.node('input',{type: 'button', name: 'reply', value: alert_arr.LBL_REPLY_TO_SENDER, className: 'buttonok', onclick: 'OpenComposer('+mid+',\'reply\')'}));

        $("forward_button").removeChild($("forward_button").firstChild);
        $("forward_button").appendChild(Builder.node('input',{type: 'button', name: 'forward', value: alert_arr.LBL_FORWARD_EMAIL, className: 'buttonok', onclick: 'OpenComposer('+mid+',\'forward\')'}));

        $("qualify_button").removeChild($("qualify_button").firstChild);
	if(showQualify == 'yes')
        	$("qualify_button").appendChild(Builder.node('input',{type: 'button', name: 'Qualify2', value: alert_arr.LBL_QUALIFY_EMAIL, className: 'buttonok', onclick: 'showRelationships('+mid+')'}));
	else
		$("qualify_button").appendChild(Builder.node('input',{type: 'hidden',name: 'hide'}));
        $("download_attach_button").removeChild($("download_attach_button").firstChild);
        $("download_attach_button").appendChild(Builder.node('input',{type: 'button', name: 'download', value: ' Download Attachments ', className: 'buttonok', onclick: 'displayAttachments('+mid+')'}));

	makeSelected(node.id)
}
function displayAttachments(mid) {
        var url = "index.php?module=Webmails&action=dlAttachments&mailid="+mid+"&mailbox="+mailbox;
        window.open(url,"DownloadAttachments",'menubar=no,toolbar=no,location=no,status=no,resizable=no,width=450,height=450');
}
function OpenComposer(id,mode)
{
        switch(mode)
                {
                case 'edit':
                        url = 'index.php?module=Webmails&action=EditView&record='+id;
                        break;
                case 'create':
                        url = 'index.php?module=Emails&action=EmailsAjax&file=EditView';
                        break;
                case 'forward':
                        url = 'index.php?module=Emails&action=EmailsAjax&mailid='+id+'&forward=true&webmail=true&file=EditView&mailbox='+mailbox;
                        break;
                case 'reply':
                        url = 'index.php?module=Emails&action=EmailsAjax&mailid='+id+'&reply=single&webmail=true&file=EditView&mailbox='+mailbox;
                        break;
                case 'replyall':
                        url = 'index.php?module=Emails&action=EmailsAjax&mailid='+id+'&reply=all&webmail=true&file=EditView&mailbox='+mailbox;
                        break;
                case 'attachments':
                        url = 'index.php?module=Webmails&action=dlAttachments&mailid='+id+'&mailbox='+mailbox;
                        break;
                case 'full_view':
                        url = 'index.php?module=Webmails&action=DetailView&record='+id+'&mailid='+id+'&mailbox='+mailbox;
                        break;
                }
        openPopUp('xComposeEmail',this,url,'createemailWin',830,662,'menubar=no,toolbar=no,location=no,status=no,resizable=yes,scrollbars=yes');
}

function makeSelected(rowId)
{
        if(gselected_mail != '')
                $(gselected_mail).className = '';

        $(rowId).className = 'mailSelected_select';
        gselected_mail = rowId;
}

function showRelationships(mid) {
	// TODO: present the user with a simple DHTML div to
	// choose what type of relationship they would like to create
	// before creating it.
	if(confirm(alert_arr.WISH_TO_QUALIFY_MAIL_AS_CONTACT))
       		add_to_vtiger(mid);
}
function add_to_vtiger(mid) {
	// TODO: update this function to allow you to set what entity type
	// you would like to associate to
	var rowId = "row_"+mid;
	$(rowId).className = "qualify_email";
        $("status").style.display="block";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Webmails&action=Save&mailid='+mid+'&ajax=true'+'&mailbox='+mailbox,
                        onComplete: function(t) {
				setTimeout('makeSelected("'+rowId+'");',500);
				$("status").style.display="none";
                        }
                }
        );
}
function select_all() {
        var els = document.getElementsByClassName("msg_check");
	var id='';
        for(var i=0;i<els.length;i++) {
                id = els[i].name.substr((els[i].name.indexOf("_")+1),els[i].name.length);
		var tels = $("row_"+id);
		if(tels.className == "deletedRow") {
                        els[i].checked = false;
		} else {
                	if(els[i].checked)
                        	els[i].checked = false;
                	else 
                        	els[i].checked = true;
		}
        }
}
function check_in_all_boxes(mymbox) {
	// TODO: There is possibly still a bug in the mailbox counting code
	// check for NaN
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Webmails&action=WebmailsAjax&command=check_mbox_all&mailbox='+mymbox+'&ajax=true&file=ListView',
                        onComplete: function(t) {
				//alert(t.responseText);
				if(t.responseText != "") {
                                	var data = eval('(' + t.responseText + ')');
                                	for (var i=0;i<data.msgs.length;i++) {
                                        	var mbox = data.msgs[i].msg.box;
						if(mbox != mailbox) {
                                        		var numnew = parseInt(data.msgs[i].msg.newmsgs);

							var read  = parseInt($(mbox+"_read").innerHTML);
							$(mbox+"_read").innerHTML = (read+numnew);
							var unread  = parseInt($(mbox+"_unread").innerHTML);
							$(mbox+"_unread").innerHTML = (unread+numnew);
						}
					}
				}
        			$("status").style.display="none";
			}
		}
	);
}
function check_for_new_mail(mbox) {
	//window.location=window.location;
	if(degraded_service == 'true') {
		return;
	}
	mailbox = mbox;
	runEmailCommand("reload",0);
        $("status").style.display="block";
/*
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Webmails&action=WebmailsAjax&mailbox='+mbox+'&command=check_mbox&ajax=true&file=ListView',
                        onComplete: function(t) {
		             try {
				// TODO: replace this at some point with prototype JSON
				// tools
                                var data = eval('(' + t.responseText + ')');
				//var read  = parseInt($(mailbox+"_read").innerHTML);
				//$(mailbox+"_read").innerHTML = (read+data.mails.length);
				var unread  = parseInt($(mailbox+"_unread").innerHTML);
				$(mailbox+"_unread").innerHTML = (unread+data.mails.length);
                                for (var i=0;i<data.mails.length;i++) {
                                        var mailid = data.mails[i].mail.mailid;
                                        var date = data.mails[i].mail.date;
                                        var subject=data.mails[i].mail.subject;
                                        var attachments=data.mails[i].mail.attachments;
                                        var from=data.mails[i].mail.from;

                                        webmail[mailid] = new Array();
                                        webmail[mailid]["from"] = from;
                                        webmail[mailid]["to"] = data.mails[i].mail.to;
                                        webmail[mailid]["subject"] = subject;
                                        webmail[mailid]["date"] = date;

                                        // main row
                                        var tr = Builder.node(
                                                'tr',
                                                {id:'row_'+mailid, className: 'unread_email'}
                                        );

                                        // checkbox
                                        var check = Builder.node(
                                                'td',
                                                [ Builder.node(
                                                        'input',
 {type: 'checkbox', name: 'selected_id', value: mailid, className: 'msg_check'}
                                                       
                                                )]


                                        );

                                        tr.appendChild(check);
                                        // images
                                        // Attachment
                                        imgtd = Builder.node('td');
                                        if(attachments === "1")  {
                                            var attach = Builder.node('a',
                                                {href: 'javascript:;', onclick: 'displayAttachments('+mailid+')'},
                                                [ Builder.node('img',
                                                        {src: 'modules/Webmails/images/stock_attach.png', border: '0', width: '14px', height: '14px'}
                                                )]
                                            );
                                        } else {
                                            var attach = Builder.node('a',
                                                {src: 'modules/Webmails/images/blank.png', border: '0', width: '14px', height: '14px'}
                                            );
                                        }
                                        imgtd.appendChild(attach);
                                        imgtd.innerHTML += "&nbsp;";

                                        var unread = Builder.node('span',
                                                {id: 'unread_img_'+mailid},
                                                [ Builder.node('a',
                                                        {href: 'javascript:;', onclick: 'OpenCompose('+mailid+',\'reply\')'},
                                                        [ Builder.node('img',
                                                                {src: 'modules/Webmails/images/stock_mail-unread.png', border: '0', width: '10', height: '14'}
                                                        )]
                                                )]
                                        );
                                        imgtd.appendChild(unread);
                                        imgtd.innerHTML += "&nbsp;";

                                        var flag = Builder.node('span',
                                                {id: 'set_td_'+mailid},
                                                [ Builder.node('a',
                                                        {href: 'javascript:void(0);', onclick: 'runEmailCommand(\'set_flag\','+mailid+')'},
                                                        [ Builder.node('img',
                                                                {src: 'modules/Webmails/images/plus.gif', border: '0', width: '11px', height: '11px', id: 'set_flag_img_'+mailid}
                                                        )]
                                                )]
                                        );
                                        imgtd.appendChild(flag);
                                        tr.appendChild(imgtd);


                                        // MSG details
                                        tr.appendChild( Builder.node('td',
                                                [ Builder.node('a',
                                                        {href: 'javascript:;', onclick: 'load_webmail(\''+mailid+'\')', id: 'ndeleted_subject_'+mailid},
                                                        ''+subject+''
                                                )]
                                        ));
                                        tr.appendChild( Builder.node('td',
                                                {id: 'ndeleted_date_'+mailid},
                                                ''+date+''
                                        ));
                                        tr.appendChild( Builder.node('td',
                                                {id: 'ndeleted_from_'+mailid},
                                                ''+from+''
                                        ));

                                       var del = Builder.node('td',
                                                {align: 'center', id:'ndeleted_td_'+mailid},
                                                [ Builder.node('span',
                                                        {id: 'del_link_'+mailid},
                                                        [ Builder.node('a',
                                                                {href: 'javascript:;', onclick: 'runEmailCommand(\'delete_msg\','+mailid+')'},
                                                                [ Builder.node('img',
                                                                        {src: 'modules/Webmails/images/gnome-fs-trash-empty.png', border: '0', width: '14', height: '14', alt: 'del'}
                                                                )]
                                                        )]
                                                )]
                                        );
                                        tr.appendChild(del);

					// TODO: this is ugly, replace using prototype child walker tools
                                        tr.style.display='none';
                                        var tels = $("message_table").childNodes[1].childNodes;
                                        for(var j=0;j<tels.length;j++) {
					    try {
                                                if(tels[j].id.match(/row_/)) {
							//we are deleting the row and add it - AVOID THIS DELTE - MICKIE
                                                	//$("message_table").childNodes[1].deleteRow(tr,tels[j]);commented since header does not come when new mails arrive                                                
							$("message_table").childNodes[1].insertBefore(tr,tels[j]);
                                                        break;
                                        	}
					    }catch(f){}
                                        }
                                        new Effect.Appear("row_"+mailid);
                                }
                            }catch(e) {}
			    check_in_all_boxes(mailbox);
        		    //$("status").style.display="none";
                        }
                }
        );
	*/
}
function periodic_event() {
	// NOTE: any functions you put in here may race.  This could probably
	// be avoided by executing functions in a 0'ed timeout, or a prototype
	// enumerator
        check_for_new_mail(mailbox);
        window.setTimeout("periodic_event()",box_refresh);
}
function show_hidden() {
	// prototype uses enumerable lists to queue events for execution.
	// because of this, this function executes and returns imediately and
	// the status spinner is never seen.  The status spinner below is a hack
	// and doesn't even attempt to pretend like it knows the event is finished.
	// this cannot be fixed with the scriptaculous beforeStart and afterFinish
	// event hooks for some reason, maybe because the event duration is too quick?
	window.setTimeout(function() {
        	$("status").style.display="block";
		window.setTimeout(function() {
       			$("status").style.display="none";
		},2000);
	},0);
        var els = document.getElementsByClassName("deletedRow");
        for(var i=0;i<els.length;i++) {
                if(els[i].style.display == "none")
                        new Effect.Appear(els[i],{queue: {position: 'end', scope: 'show'}, duration: 0.2});
                else
                        new Effect.Fade(els[i],{queue: {position: 'end', scope: 'show'}, duration: 0.2});
        }
}
function mass_delete()
{
       var select_options  =  document.getElementsByName('selected_id');
       var x = select_options.length;
       var nids = "";
       var nid='';
       xx = 0;
        for(i = 0; i < x ; i++)
        {
            if(select_options[i].checked)
            {
                idvalue= select_options[i].value;
		nid += idvalue +":";
                xx++;
            }
        }
        if (xx != 0)
           nids=nid;
        else
        {
            alert(alert_arr.SELECT_ATLEAST_ONEMSG_TO_DEL);
            return false;
        }
        if(confirm(alert_arr.SURE_TO_DELETE))
		runEmailCommand("delete_multi_msg",nids);
}
function move_messages()
{
	var nid = '';
	var chkname=document.getElementsByName("selected_id");
	mvmbox = $("mailbox_select").value;
	var nid = Array();
	var i=0;
	move_mail = 1;
	for(var m=0;m<chkname.length;m++)
	{
		if(chkname[m].checked)
			nid[i++] =  chkname[m].value;
	}
	
	if(nid.length > 0)
	{
		$("status").style.display="block";
		new Ajax.Request(
				'index.php',
				{queue: {position: 'end', scope: 'command'},
					method: 'post',
					postBody: 'module=Webmails&action=WebmailsAjax&mailbox='+mailbox+'&start='+start+'&command=move_msg&ajax=true&mailid='+nid.join(":")+'&mvbox='+mvmbox,
					onComplete: function(t) {
						sh = $("show_msg");
						var leftSide = findPosX(sh);
					        var topSide = findPosY(sh);
					        sh.style.left= leftSide + 400+'px';
					        sh.style.top= topSide + 350 +'px';
						sh.innerHTML = "Moving mail(s) from "+mailbox+" folder to "+mvmbox+" folder";
                                                sh.style.display = "block";
						sh.classname = "delete_email";
                                                new Effect.Fade(sh,{queue: {position: 'end', scope: 'effect'},duration: '50'});
						for(i=0;i<nid.length;i++)
						{
							var oRow = $('row_'+nid[i]);
							new Effect.Fade(oRow,{queue: {position: 'end', scope: 'effect'},duration: '0.5'});
						}
						$("status").style.display = "none";
					        start = t.responseText;	
						runEmailCommand("reload",0);
					}
				}
			);
	}else
	{
		alert(alert_arr.SELECT_MAIL_MOVE);
	}
}

/*function move_messages() {
        $("status").style.display="block";
        var els = document.getElementsByTagName("INPUT");
        var cnt = (els.length-1);
        for(var i=cnt;i>0;i--) {
                if(els[i].type == "checkbox" && els[i].name.indexOf("_")) {
                        if(els[i].checked) {
                                var nid = els[i].name.substr((els[i].name.indexOf("_")+1),els[i].name.length);
                                var mvmbox = $("mailbox_select").value;
                                var row = $("row_"+nid);
                                new Effect.Fade(row,{queue: {position: 'end', scope: 'effect'},duration: '0.5'});
                                new Ajax.Request(
                                        'index.php',
                                        {queue: {position: 'end', scope: 'command'},
                                                method: 'post',
                                                postBody: 'module=Webmails&action=WebmailsAjax&file=ListView&mailbox='+gCurrentFolder+'&command=move_msg&ajax=true&mailid='+nid+'&mvbox='+mvmbox,
                                              onComplete: function(t) {
                                                        //alert(t.responseText);
                                                }
                                        }
                                );
                        }
                }
        }
	$('mailbox_select').selectedIndex=0;
        //runEmailCommand('expunge','');
        $("status").style.display="none";
}*/
function search_emails() {
	// TODO: find a way to search in degraded functionality mode.
        var search_query = $("search_input").value;
        var search_type = $("search_type").value;
        window.location = "index.php?module=Webmails&action=index&search=true&search_type="+search_type+"&search_input="+search_query+'&mailbox='+mailbox;
}
function runEmailCommand(com,id) {
        command=com;
        id=id;
	gselected_mail = '';
	if(com == 'delete_msg')
	{
		if(!confirm(alert_arr.DELETE+" "+alert_arr.MAIL+" ?"))
			return;
	}
	if(com=="reload")
                var file="ListViewAjax";
        else
                var file="";
	
	if(move_mail == 1){
		var qry_str = "&mvbox="+mvmbox;
		move_mail = 0;
	}
	else
		qry_str = "";
        
	$("status").style.display="block";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Webmails&action=WebmailsAjax&start='+start+'&command='+command+'&mailid='+id+'&file='+file+'&mailbox='+mailbox+qry_str+'&search_type='+$("search_type").value+'&search_input='+$("search_input").value,
                        onComplete: function(t) {
                                resp = t.responseText;
				id=resp;
                                if(resp.match(/ajax failed/)) {return;}
                                switch(command) {
				    case 'reload':
					$("rssScroll").innerHTML = resp;
					var unread_count = parseInt($(mailbox+"_tempcount").innerHTML);
					if(unread_count > 0) {
						$(mailbox+"_unread").innerHTML = unread_count;
					}
					else{
						$(mailbox+"_count").innerHTML = "";
					}
					$("nav").innerHTML = $("navTemp").innerHTML;
					$("box_list").innerHTML = $("temp_boxlist").innerHTML;
					$("move_pane").innerHTML = $("temp_movepane").innerHTML;
					$("temp_boxlist").innerHTML = "";
					$("temp_movepane").innerHTML = "";
					$("navTemp").innerHTML = '';
					$(mailbox+"_tempcount").innerHTML = "";
					break;
                                    case 'expunge':
                                        // NOTE: we either have to reload the page or count up from the messages that
                                        // are deleted and moved or we introduce a bug from invalid mail ids
                                        //window.location = window.location;
					start = resp;
					runEmailCommand("reload",0);
                                    break;
                                    case 'delete_multi_msg':
					var ids;
					eval(resp);
					var rows = ids.split(":");
					for(i=0;i<rows.length;i++)  {
						var id = rows[i];
                                        	var row = $("row_"+id);
						if(row.className == "mailSelected") {
							var unread  = parseInt($(mailbox+"_unread").innerHTML);
							$(mailbox+"_unread").innerHTML = (unread-1);
						}
                                        	row.className = "delete_email";

                                        	Try.these (
							function() {
                                                		$("ndeleted_subject_"+id).innerHTML = "<s>"+$("ndeleted_subject_"+id).innerHTML+"</s>";
                                                		$("ndeleted_date_"+id).innerHTML = "<s>"+$("ndeleted_date_"+id).innerHTML+"</s>";
                                                		$("ndeleted_from_"+id).innerHTML = "<s>"+$("ndeleted_from_"+id).innerHTML+"</s>";
							},
							function() {
                                                		$("deleted_subject_"+id).innerHTML = "<s>"+$("deleted_subject_"+id).innerHTML+"</s>";
                                                		$("deleted_date_"+id).innerHTML = "<s>"+$("deleted_date_"+id).innerHTML+"</s>";
                                                		$("deleted_from_"+id).innerHTML = "<s>"+$("deleted_from_"+id).innerHTML+"</s>";
							}
                                        	);

					try {
                                        	$("del_link_"+id).innerHTML = '<a href="javascript:void(0);" onclick="runEmailCommand(\'undelete_msg\','+id+');"><img src="modules/Webmails/images/gnome-fs-trash-full.png" border="0" width="14" height="14" alt="del"></a>';

                                        	new Effect.Fade(row,{queue: {position: 'end', scope: 'effect'},duration: '0.5'});
                                        	tmp = document.getElementsByClassName("previewWindow");
                                              //  tmp[0].style.visibility="hidden";
					}catch(g){}
					if(preview_id == id){
					//	alert(preview_id + id);
						$("preview1").style.visibility="hidden";
                                                $("preview2").style.visibility="hidden";
					}
                                        /*for(var i=0;i<tmp.length;i++) {
                                                if(tmp[i].style.visibility === "visible") {
                                                        tmp[i].style.visibility="hidden";
                                                }
                                        }*/

                                	$("status").style.display="none";
						if(i == ((rows.length)-2)){ 
							runEmailCommand("reload",0);
						}
					}
                                    break;
                                    case 'delete_msg':
					//id=resp;
					eval(resp);
				if($("row_"+id))
				{
                                        var row = $("row_"+id);
					if(row.className == "unread_email") {
						var unread  = parseInt($(mailbox+"_unread").innerHTML);
						$(mailbox+"_unread").innerHTML = (unread-1);
					}
					row.className = 'delete_email';
                                       // row.className = "deletedRow";

                                        Try.these (
						function() {
                                                	$("ndeleted_subject_"+id).innerHTML = "<s>"+$("ndeleted_subject_"+id).innerHTML+"</s>";
                                                	$("ndeleted_date_"+id).innerHTML = "<s>"+$("ndeleted_date_"+id).innerHTML+"</s>";
                                                	$("ndeleted_from_"+id).innerHTML = "<s>"+$("ndeleted_from_"+id).innerHTML+"</s>";
						},
						function() {
                                                	$("deleted_subject_"+id).innerHTML = "<s>"+$("deleted_subject_"+id).innerHTML+"</s>";
                                                	$("deleted_date_"+id).innerHTML = "<s>"+$("deleted_date_"+id).innerHTML+"</s>";
                                                	$("deleted_from_"+id).innerHTML = "<s>"+$("deleted_from_"+id).innerHTML+"</s>";
						}
                                        );

                                        $("del_link_"+id).innerHTML = '<a href="javascript:void(0);" onclick="runEmailCommand(\'undelete_msg\','+id+');"><img src="modules/Webmails/images/gnome-fs-trash-full.png" border="0" width="14" height="14" alt="del"></a>';
	                                        new Effect.Fade(row,{queue: {position: 'end', scope: 'effect'},duration: '1.0'});
				}
					
									
					if(preview_id == id){
                                        //      alert(preview_id + id);
                                                $("preview1").style.visibility="hidden";
                                                $("preview2").style.visibility="hidden";
                                        }

					runEmailCommand("reload",0);
                                    break;
                                    case 'undelete_msg':
					id=resp;
                                        var node = $("row_"+id);
                                        node.className='';
                                        node.style.display = '';
                                        var newhtml = remove(remove(node.innerHTML,'<s>'),'</s>');
                                        node.innerHTML=newhtml;
                                        $("del_link_"+id).innerHTML = '<a href="javascript:void(0);" onclick="runEmailCommand(\'delete_msg\','+id+');"><img src="modules/Webmails/images/gnome-fs-trash-empty.png" border="0" width="14" height="14" alt="del"></a>';
                                	$("status").style.display="none";
                                    break;
                                    case 'clear_flag':
                                        var nm = "clear_td_"+id;
                                        var el = $(nm);
                                        var tmp = el.innerHTML;
                                        el.innerHTML ='<a href="javascript:void(0);" onclick="runEmailCommand(\'set_flag\','+id+');"><img src="themes/images/important2.gif" border="0" width="11" height="11" id="set_flag_img_'+id+'"></a>';
                                        el.id = "set_td_"+id;
                                    break;
                                    case 'set_flag':
                                        var nm = "set_td_"+id;
                                        var el = $(nm);
                                        var tmp = el.innerHTML;
                                        el.innerHTML ='<a href="javascript:void(0);" onclick="runEmailCommand(\'clear_flag\','+id+');"><img src="themes/images/important1.gif" border="0" width="11" height="11" id="clear_flag_img'+id+'"></a>';
                                        el.id = "clear_td_"+id;
                                    break;

                                }
                                $("status").style.display="none";
                        }
                }
        );
}
function cal_navigation(box,page){
	start = page;
	mailbox = box;
	runEmailCommand("reload",0);
}
function remove(s, t) {
  /*
  **  Remove all occurrences of a token in a string
  **    s  string to be processed
  **    t  token to be removed
  **  returns new string
  */
  i = s.indexOf(t);
  r = "";
  if (i == -1) return s;
  r += s.substring(0,i) + remove(s.substring(i + t.length), t);
  return r;
}
function changeMbox(box) {
	mailbox=box;
	start = 0;	
	change_box=1;
	runEmailCommand("reload",0);
        //location.href = "index.php?module=Webmails&action=index&mailbox="+box;
}
// TODO: these two functions should be tied into a mailbox management panel of some kind.
// could be a DHTML div with AJAX calls to execute the commands on the mailbox.  
function show_addfolder() {
        var fldr = $("folderOpts");
        if(fldr.style.display == 'none')
                $("folderOpts").style.display="";
        else
                $("folderOpts").style.display="none";
}
function show_remfolder(mb) {
        var fldr = $("remove_"+mb);
  if(typeof(fldr) != "undefined")
	{
        if(fldr.style.display == 'none')
                fldr.style.display="";
        else
                fldr.style.display="none";
}
}
