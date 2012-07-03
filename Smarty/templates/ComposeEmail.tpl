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


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
<title>{$MOD.TITLE_COMPOSE_MAIL}</title>
<link REL="SHORTCUT ICON" HREF="include/images/vtigercrm_icon.ico">	
<style type="text/css">@import url("themes/{$THEME}/style.css");</style>
<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script src="include/js/general.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="include/js/{php} echo $_SESSION['authenticated_user_language'];{/php}.lang.js?{php} echo $_SESSION['vtiger_version'];{/php}"></script>
<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="modules/Products/multifile.js"></script>
</head>
<body marginheight="0" marginwidth="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
{literal}
<form name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php" onSubmit="if(email_validate(this.form,'')) { VtigerJS_DialogBox.block();} else { return false; }">
{/literal}
<input type="hidden" name="send_mail" >
<input type="hidden" name="contact_id" value="{$CONTACT_ID}">
<input type="hidden" name="user_id" value="{$USER_ID}">
<input type="hidden" name="filename" value="{$FILENAME}">
<input type="hidden" name="old_id" value="{$OLD_ID}">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{$MODE}">
<input type="hidden" name="action">
<input type="hidden" name="popupaction" value="create">
<input type="hidden" name="hidden_toid" id="hidden_toid">
<table class="small mailClient" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
   
   <tr>
	<td colspan=3 >
	<!-- Email Header -->
	<table border=0 cellspacing=0 cellpadding=0 width=100% class="mailClientWriteEmailHeader">
	<tr>
		<td >{$MOD.LBL_COMPOSE_EMAIL}</td>
	</tr>
	</table>
	
	
	</td>
</tr>
	{foreach item=row from=$BLOCKS}
	{foreach item=elements from=$row}
	{if $elements.2.0 eq 'parent_id'}
   <tr>
	<td class="mailSubHeader" align="right"><font color="red">*</font><b>{$MOD.LBL_TO}</b></td>
	<td class="cellText" style="padding: 5px;">
 		<input name="{$elements.2.0}" id="{$elements.2.0}" type="hidden" value="{$IDLISTS}">
		<input type="hidden" name="saved_toid" value="{$TO_MAIL}">
		<input id="parent_name" name="parent_name" readonly class="txtBox" type="text" value="{$TO_MAIL}" style="width: 550px;">&nbsp;
	</td>
	<td class="cellText" style="padding: 5px;" align="left" nowrap>
		<select name="parent_type">
			{foreach key=labelval item=selectval from=$elements.1.0}
				{if $selectval eq selected}
					{assign var=selectmodule value="selected"}
				{else}
					{assign var=selectmodule value=""}
				{/if}
				<option value="{$labelval}" {$selectmodule}>{$APP[$labelval]}</option>
			{/foreach}
		</select>
		&nbsp;
		<span  class="mailClientCSSButton">
		<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&html=Popup_picker&form=HelpDeskEditView&popuptype=set_return_emails","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
		</span><span class="mailClientCSSButton" ><img src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="$('parent_id').value=''; $('hidden_toid').value='';$('parent_name').value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
		</span>
	</td>
   </tr>
	<tr>
	{if 'ccmail'|@emails_checkFieldVisiblityPermission:'readwrite' eq '0'}   
   	<td class="mailSubHeader" style="padding: 5px;" align="right">{$MOD.LBL_CC}</td>
	<td class="cellText" style="padding: 5px;">
		<input name="ccmail" id ="cc_name" class="txtBox" type="text" value="{$CC_MAIL}" style="width:99%">&nbsp;
	</td>
	{else}
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	{/if}
   <td valign="top" class="cellLabel" rowspan="4"><div id="attach_cont" class="addEventInnerBox" style="overflow:auto;height:100px;width:100%;position:relative;left:0px;top:0px;"></div>
   	</tr>
   {if 'bccmail'|@emails_checkFieldVisiblityPermission:'readwrite' eq '0'}   
   	<tr>
	<td class="mailSubHeader" style="padding: 5px;" align="right">{$MOD.LBL_BCC}</td>
	<td class="cellText" style="padding: 5px;">
		<input name="bccmail" id="bcc_name" class="txtBox" type="text" value="{$BCC_MAIL}" style="width:99%">&nbsp;
	</td>
   	</tr>
   	{/if}
	{elseif $elements.2.0 eq 'subject'}
   <tr>
	<td class="mailSubHeader" style="padding: 5px;" align="right" nowrap><font color="red">*</font>{$elements.1.0}  :</td>
        {if $WEBMAIL eq 'true' or $RET_ERROR eq 1}
                <td class="cellText" style="padding: 5px;"><input type="text" class="txtBox" name="{$elements.2.0}" value="{$SUBJECT}" id="{$elements.2.0}" style="width:99%"></td>
        {else}
                <td class="cellText" style="padding: 5px;"><input type="text" class="txtBox" name="{$elements.2.0}" value="{$elements.3.0}" id="{$elements.2.0}" style="width:99%"></td>
        {/if}
   </tr>
	{elseif $elements.2.0 eq 'filename'}

   <tr>
	<td class="mailSubHeader" style="padding: 5px;" align="right" nowrap>{$elements.1.0}  :</td>
	<td class="cellText" style="padding: 5px;">
		<!--<input name="{$elements.2.0}"  type="file" class="small txtBox" value="" size="78"/>-->
		<input name="del_file_list" type="hidden" value="">
					<div id="files_list" style="border: 1px solid grey; width: 500px; padding: 5px; background: rgb(255, 255, 255) none repeat scroll 0%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; font-size: x-small">{$APP.Files_Maximum_6}
						<input id="my_file_element" type="file" name="{$elements.2.0}" tabindex="7" onchange="validateFilename(this)" >
						<input type="hidden" name="{$elements.2.0}_hidden" value="" />
                        <span id="limitmsg" style= "color:red; display:'';">{'LBL_MAX_SIZE'|@getTranslatedString:$MODULE} {$UPLOADSIZE}{'LBL_FILESIZEIN_MB'|@getTranslatedString:$MODULE}</span>
                	</div>
					<script>
						var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), 6 );
						multi_selector.count = 0
						multi_selector.addElement( document.getElementById( 'my_file_element' ) );
					</script>
		<div id="attach_temp_cont" style="display:none;">
		<table class="small" width="100% ">
		
	{if $smarty.request.attachment != ''}
                <tr><td width="100%" colspan="2">{$smarty.request.attachment|@vtlib_purify}<input type="hidden" value="{$smarty.request.attachment|@vtlib_purify}" name="pdf_attachment"></td></tr>                                                                                                                                                                                      {else}   

		{foreach item="attach_files" key="attach_id" from=$elements.3}	
			<tr id="row_{$attach_id}"><td width="90%">{$attach_files}</td><td><img src="{'no.gif'|@vtiger_imageurl:$THEME}" onClick="delAttachments({$attach_id})" alt="{$APP.LBL_DELETE_BUTTON}" title="{$APP.LBL_DELETE_BUTTON}" style="cursor:pointer;"></td></tr>	
		{/foreach}
		<input type='hidden' name='att_id_list' value='{$ATT_ID_LIST}' />
	{/if}

		{if $WEBMAIL eq 'true'}
		{foreach item="attach_files" from=$webmail_attachments}
                <tr ><td width="90%">{$attach_files}</td></tr>
        {/foreach}	
		{/if}
		</table>	
		</div>	
		{$elements.3.0}
	</td>
   </tr>
   <tr>
	<td colspan="3" class="mailSubHeader" style="padding: 5px;" align="center">
		 <input title="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE}" accessKey="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_KEY}" class="crmbutton small edit" onclick="window.open('index.php?module=Users&action=lookupemailtemplates','emailtemplate','top=100,left=200,height=400,width=500,resizable=yes,scrollbars=yes,menubar=no,addressbar=no,status=yes')" type="button" name="button" value=" {$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL}  ">
		<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="return email_validate(this.form,'save');" type="button" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL} " >&nbsp;
		<input name="{$MOD.LBL_SEND}" value=" {$APP.LBL_SEND} " class="crmbutton small save" type="button" onclick="return email_validate(this.form,'send');">&nbsp;
		<input name="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" type="button" onClick="window.close()">
	</td>
    </tr>
	{elseif $elements.2.0 eq 'description'}
   <tr>
	<td colspan="3" align="center" valign="top" height="320">
        {if $WEBMAIL eq 'true' or $RET_ERROR eq 1}
		<input type="hidden" name="from_add" value="{$from_add}">
		<input type="hidden" name="att_module" value="Webmails">
		<input type="hidden" name="mailid" value="{$mailid}">
		<input type="hidden" name="mailbox" value="{$mailbox}">
                <textarea style="display: none;" class="detailedViewTextBox" id="description" name="description" cols="90" rows="8">{$DESCRIPTION}</textarea>
        {else}
                <textarea style="display: none;" class="detailedViewTextBox" id="description" name="description" cols="90" rows="16">{$elements.3.0}</textarea>        {/if}
	</td>
   </tr>
	{/if}
	{/foreach}
	{/foreach}

   <tr>
	<td colspan="3" class="mailSubHeader" style="padding: 5px;" align="center">
		 <input title="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE}" accessKey="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_KEY}" class="crmbutton small edit" onclick="window.open('index.php?module=Users&action=lookupemailtemplates','emailtemplate','top=100,left=200,height=400,width=500,menubar=no,addressbar=no,status=yes')" type="button" name="button" value=" {$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL}  ">
		<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="return email_validate(this.form,'save');" type="button" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL} " >&nbsp;
		<input name="{$MOD.LBL_SEND}" value=" {$APP.LBL_SEND} " class="crmbutton small save" type="button" onclick="return email_validate(this.form,'send');">&nbsp;
		<input name="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" type="button" onClick="window.close()">
	</td>
   </tr>
</tbody>
</table>
</form>
</body>
<script>
var cc_err_msg = '{$MOD.LBL_CC_EMAIL_ERROR}';
var no_rcpts_err_msg = '{$MOD.LBL_NO_RCPTS_EMAIL_ERROR}';
var bcc_err_msg = '{$MOD.LBL_BCC_EMAIL_ERROR}';
var conf_mail_srvr_err_msg = '{$MOD.LBL_CONF_MAILSERVER_ERROR}';
{literal}
function email_validate(oform,mode)
{
	if(trim(mode) == '')
	{
		return false;
	}
	if(oform.parent_name.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{
		//alert('No recipients were specified');
		alert(no_rcpts_err_msg);
		return false;
	}
	//Changes made to fix tickets #4633, # 5111 to accomodate all possible email formats
	var email_regex = /^[a-zA-Z0-9]+([\_\-\.]*[a-zA-Z0-9]+[\_\-]?)*@[a-zA-Z0-9]+([\_\-]?[a-zA-Z0-9]+)*\.+([\_\-]?[a-zA-Z0-9])+(\.?[a-zA-Z0-9]+)*$/;
	
	if(document.EditView.ccmail != null){
		if(document.EditView.ccmail.value.length >= 1){
			var str = document.EditView.ccmail.value;
            arr = new Array();
            arr = str.split(",");
            var tmp;
	    	for(var i=0; i<=arr.length-1; i++){
	            tmp = arr[i];
	            if(tmp.match('<') && tmp.match('>')) {
                    if(!findAngleBracket(arr[i])) {
                        alert(cc_err_msg+": "+arr[i]);
                        return false;
                    }
            	}
				else if(trim(arr[i]) != "" && !(email_regex.test(trim(arr[i]))))
	            {
	                    alert(cc_err_msg+": "+arr[i]);
	                    return false;
	            }
			}
		}
	}	
	if(document.EditView.bccmail != null){
		if(document.EditView.bccmail.value.length >= 1){
			var str = document.EditView.bccmail.value;
			arr = new Array();
			arr = str.split(",");
			var tmp;
			for(var i=0; i<=arr.length-1; i++){
				tmp = arr[i];
				if(tmp.match('<') && tmp.match('>')) {
                    if(!findAngleBracket(arr[i])) {
                        alert(bcc_err_msg+": "+arr[i]);
                        return false;
                    }
            	} 
            	else if(trim(arr[i]) != "" && !(email_regex.test(trim(arr[i])))){
					alert(bcc_err_msg+": "+arr[i]);
					return false;	
				}
			}	
		}
	}	
	if(oform.subject.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{
		if(email_sub = prompt('You did not specify a subject from this email. If you would like to provide one, please type it now','(no-Subject)'))
		{
			oform.subject.value = email_sub;
		}else
		{
			return false;
		}
	}
	if(mode == 'send')
	{
		server_check()	
	}else if(mode == 'save')
	{
		oform.action.value='Save';
		oform.submit();
	}else
	{
		return false;
	}
}
//function to extract the mailaddress inside < > symbols.......for the bug fix #3752
function findAngleBracket(mailadd)
{
        var strlen = mailadd.length;
        var success = 0;
        var gt = 0;
        var lt = 0;
        var ret = '';
        for(i=0;i<strlen;i++){
                if(mailadd.charAt(i) == '<' && gt == 0){
                        lt = 1;
                }
                if(mailadd.charAt(i) == '>' && lt == 1){
                        gt = 1;
                }
                if(mailadd.charAt(i) != '<' && lt == 1 && gt == 0)
                        ret = ret + mailadd.charAt(i);

        }
        if(/^[a-z0-9]([a-z0-9_\-\.]*)@([a-z0-9_\-\.]*)(\.[a-z]{2,3}(\.[a-z]{2}){0,2})$/.test(ret)){
                return true;
        }
        else
                return false;

}
function server_check()
{
	var oform = window.document.EditView;
        new Ajax.Request(
        	'index.php',
                {queue: {position: 'end', scope: 'command'},
                	method: 'post',
                        postBody:"module=Emails&action=EmailsAjax&file=Save&ajax=true&server_check=true",
			onComplete: function(response) {
			if(response.responseText.indexOf('SUCCESS') > -1)
			{
				oform.send_mail.value='true';
				oform.action.value='Save';
				oform.submit();
			}else
			{
				//alert('Please Configure Your Mail Server');
				alert(conf_mail_srvr_err_msg);
				return false;
			}
               	    }
                }
        );
}
$('attach_cont').innerHTML = $('attach_temp_cont').innerHTML;
function delAttachments(id)
{
    new Ajax.Request(
        'index.php',
        {queue: {position: 'end', scope: 'command'},
            method: 'post',
            postBody: 'module=Contacts&action=ContactsAjax&file=DelImage&attachmodule=Emails&recordid='+id,
            onComplete: function(response)
            {
		Effect.Fade('row_'+id);	                
            }
        }
    );

}
{/literal}
</script>
<script type="text/javascript" defer="1">
	var textAreaName = 'description';
	CKEDITOR.replace( textAreaName,	{ldelim}
		extraPlugins : 'uicolor',
		uiColor: '#dfdff1'
	{rdelim} ) ;
	var oCKeditor = CKEDITOR.instances[textAreaName];
</script>
</html>
