/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
var globaldtlviewspanid = "";
var globaleditareaspanid = ""; 
var globaltxtboxid = "";
var itsonview=false;
// to retain the old value if we cancel the ajax edit
var globaltempvalue = '';
var globaluitype = '';
function showHide(showId, hideId)
{
	show(showId);
	fnhide(hideId);
}

function hndCancel(valuespanid,textareapanid,fieldlabel)
{

  showHide(valuespanid,textareapanid);
  if(globaluitype == '56')
  {	  
	  if(globaltempvalue == 1)
	  	getObj(globaltxtboxid).checked = true; 
	  else		
	  	getObj(globaltxtboxid).checked = false; 
  }
  else if(globaluitype != '53' && globaluitype != '33')	  
	  getObj(globaltxtboxid).value = globaltempvalue; 
  globaltempvalue = '';
  itsonview=false;
  return false;
}

function hndMouseOver(uitype,fieldLabel)
{
      var mouseArea="";
      mouseArea="mouseArea_"+ fieldLabel;
      if(itsonview)
      {
            return;
      }
      
      show("crmspanid");
	  globaluitype = uitype;
      globaldtlviewspanid= "dtlview_"+ fieldLabel;//valuespanid;
      globaleditareaspanid="editarea_"+ fieldLabel;//textareapanid;
	  globalfieldlabel = fieldLabel;
	  if(globaluitype == 53)
	  {
		  if(typeof(document.DetailView.assigntype[0]) != 'undefined')
		  {
			  var assign_type_U = document.DetailView.assigntype[0].checked;
			  var assign_type_G = document.DetailView.assigntype[1].checked;
			  if(assign_type_U == true)
				  globaltxtboxid= 'txtbox_U'+fieldLabel;
			  else if(assign_type_G == true)
				  globaltxtboxid= 'txtbox_G'+fieldLabel;
		  }else
		  {
			  globaltxtboxid= 'txtbox_U'+fieldLabel;
		  }
	  }else
	  {
      	  globaltxtboxid="txtbox_"+ fieldLabel;//textboxpanid;
	  }
      divObj = getObj('crmspanid'); 
      crmy = findPosY(getObj(mouseArea));
      crmx = findPosX(getObj(mouseArea));
      if(document.all)
      {
          divObj.onclick=handleEdit;
      }
      else
      {
          divObj.setAttribute('onclick','handleEdit();');
      }
      divObj.style.left=(crmx+getObj(mouseArea).offsetWidth -divObj.offsetWidth)+"px";
      divObj.style.top=crmy+"px";
}

function handleEdit()
{
     show(globaleditareaspanid) ;
     fnhide(globaldtlviewspanid);
	 if(globaluitype != 53)
	 {
		globaltempvalue = getObj(globaltxtboxid).value;
		if(getObj(globaltxtboxid).type != 'hidden')
	     	 	getObj(globaltxtboxid).focus();
	 }
     fnhide('crmspanid');
     itsonview=true;
     return false;
}

//Asha: Function changed to trim both leading and trailing spaces.
function trim(str) {
	var s = str.replace(/\s+$/,'');
	s = s.replace(/^\s+/,'');
	return s;
}

var genUiType = "";
var genFldValue = "";

function dtlViewAjaxSave(fieldLabel,module,uitype,tableName,fieldName,crmId)
{
	var dtlView = "dtlview_"+ fieldLabel;
	var editArea = "editarea_"+ fieldLabel;
	var groupurl = "";
	
	if(globaluitype == 53)
	{
		if(typeof(document.DetailView.assigntype[0]) != 'undefined')
		{
			var assign_type_U = document.DetailView.assigntype[0].checked;
			var assign_type_G = document.DetailView.assigntype[1].checked;
		}else
		{
			var assign_type_U = document.DetailView.assigntype.checked;
		}
		if(assign_type_U == true)
		{
			var txtBox= 'txtbox_U'+fieldLabel;
		}
		else if(assign_type_G == true)
		{
			var txtBox= 'txtbox_G'+fieldLabel;
			var group_id = encodeURIComponent($(txtBox).options[$(txtBox).selectedIndex].text); 
			var groupurl = "&assigned_group_id="+group_id+"&assigntype=T"
		}

	}
	else if(uitype == 15 || uitype == 16)
	{	
		var txtBox= "txtbox_"+ fieldLabel;
		var not_access =document.getElementById(txtBox);
                 pickval = not_access.options[not_access.selectedIndex].value;
			if(pickval == alert_arr.LBL_NOT_ACCESSIBLE)
			{
				document.getElementById(editArea).style.display='none';
				document.getElementById(dtlView).style.display='block';
     				itsonview=false; //to show the edit link again after hiding the editdiv.
				return false;
			}
	}
	else if(globaluitype == 33)
	{
	  var txtBox= "txtbox_"+ fieldLabel;
	  var oMulSelect = $(txtBox);
	  var r = new Array();
	  var notaccess_label = new Array();
	  for (iter=0;iter < oMulSelect.options.length ; iter++)
	  {
      	      if (oMulSelect.options[iter].selected)
		{
			r[r.length] = oMulSelect.options[iter].value;
			notaccess_label[notaccess_label.length] = oMulSelect.options[iter].text;
		}
      	  }
	}else
	{
		var txtBox= "txtbox_"+ fieldLabel;
	}
	
	var popupTxt= "popuptxt_"+ fieldLabel;      
	var hdTxt = "hdtxt_"+ fieldLabel;

	if(formValidate() == false)
	{
		return false;
	}


	$("vtbusy_info").style.display="inline";
	var isAdmin = document.getElementById("hdtxt_IsAdmin").value; 


	//overriden the tagValue based on UI Type for checkbox 
	if(uitype == '56')
	{
		if(document.getElementById(txtBox).checked == true)
		{
			if(module == "Contacts")
			{
				var obj = getObj("email");
				if((fieldName == "portal") && (obj == null || obj.value == ''))
				{
					tagValue = "0";
					alert(alert_arr.PORTAL_PROVIDE_EMAILID);
					return false;
				}
				else
					tagValue = "1";

			}
			else
				tagValue = "1";
		}else
		{
			tagValue = "0";
		}
	}else	if(uitype == '156')
	{
		if(document.getElementById(txtBox).checked == true)
		{
			tagValue = "on";
		}else
		{
			tagValue = "off";
		}
	}else if(uitype == '33')
	{
		tagValue = r.join(" |##| ");
  	}else if(uitype == '24' || uitype == '21')
        {
                tagValue = document.getElementById(txtBox).value.replace(/<br\s*\/>/g, " ");

        }else
	{
		tagValue = trim(document.getElementById(txtBox).value);
		if(module == "Contacts")
                {
			if(getObj('portal'))
                        {
                                var port_obj = getObj('portal').checked;
                                if(fieldName == "email" && tagValue == '' && port_obj == true)
                                {
                                        alert(alert_arr.PORTAL_PROVIDE_EMAILID);
                                        return false;
                                }
                        }
                }
	}

	var data = "file=DetailViewAjax&module=" + module + "&action=" + module + "Ajax&record=" + crmId+"&recordid=" + crmId ;
	data = data + "&fldName=" + fieldName + "&fieldValue=" + escapeAll(tagValue) + "&ajxaction=DETAILVIEW"+groupurl;
	if(module == 'Users') {
		data += "&form_token=" + (document.getElementsByName('form_token')[0].value);
	}
	new Ajax.Request(
		'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: data,
                        onComplete: function(response) {
							if(response.responseText.indexOf(":#:FAILURE")>-1) {
	          					alert(alert_arr.ERROR_WHILE_EDITING);
	          				}
	          				else if(response.responseText.indexOf(":#:ERR")>-1) {
								alert_str = response.responseText.replace(":#:ERR","");
	          					alert(alert_str);
	           					$("vtbusy_info").style.display="none";
	          				}
	          				else if(response.responseText.indexOf(":#:SUCCESS")>-1) {
								//For HD & FAQ - comments, we should empty the field value
								if((module == "HelpDesk" || module == "Faq") && fieldName == "comments") {
									var comments = response.responseText.replace(":#:SUCCESS","");
									if(getObj("comments_div") != null) getObj("comments_div").innerHTML = comments;
									if(getObj(dtlView) != null) getObj(dtlView).innerHTML = "";
									if(getObj("comments") != null) getObj("comments").value = "";
								}
	           					$("vtbusy_info").style.display="none";
							}
						}
                }
	);
	tagValue = get_converted_html(tagValue);
	if(uitype == '13' || uitype == '104')
	{
		var temp_fieldname = 'internal_mailer_'+fieldName;
		if($(temp_fieldname))
		{
			var mail_chk_arr = $(temp_fieldname).innerHTML.split("####");
			var fieldId = mail_chk_arr[0];
			var internal_mailer_flag = mail_chk_arr[1];
			if(internal_mailer_flag == 1)
				var email_link = "<a href=\"javascript:InternalMailer("+crmId+","+fieldId+",'"+fieldName+"','"+module+"','record_id');\">"+tagValue+"&nbsp;</a>";
			else
				var email_link = "<a href=\"mailto:"+ tagValue+"\" target=\"_blank\">"+tagValue+"&nbsp;</a>";
		}
		
		getObj(dtlView).innerHTML = email_link;
		if(fieldName == "email" || fieldName == "email1"){
			var priEmail = getObj("pri_email");
			if(priEmail)
				priEmail.value = tagValue;
		}else{
			var secEmail = getObj("sec_email");
			if(secEmail)
                	        secEmail.value = tagValue;
		}
	}else if(uitype == '11'){
		if(typeof(use_asterisk) != 'undefined' && use_asterisk == true){
			getObj(dtlView).innerHTML = "<a href=\"javascript:;\" onclick=\"startCall('"+tagValue+"','"+crmId+"')\">"+tagValue+"</a>";
		}else{
			getObj(dtlView).innerHTML = tagValue;
		}
	}else if(uitype == '17')
	{
		var matchPattern = /^[\w]+:\/\//;
        if(tagValue.match(matchPattern)){
            getObj(dtlView).innerHTML = "<a href=\""+ tagValue+"\" target=\"_blank\">"+tagValue+"&nbsp;</a>";
        }else{
            getObj(dtlView).innerHTML = "<a href=\"http://"+ tagValue+"\" target=\"_blank\">"+tagValue+"&nbsp;</a>";
        }	
	}else if(uitype == '85')
        {
                getObj(dtlView).innerHTML = "<a href=\"skype://"+ tagValue+"?call\">"+tagValue+"&nbsp;</a>";
        }else if(uitype == '53')
	{
		var hdObj = getObj(hdTxt);
		if(typeof(document.DetailView.assigntype[0]) != 'undefined')
        {
			var assign_type_U = document.DetailView.assigntype[0].checked;
			var assign_type_G = document.DetailView.assigntype[1].checked;
		}else
		{
			var assign_type_U = document.DetailView.assigntype.checked;
		}
		if(isAdmin == "0")
		{
			getObj(dtlView).innerHTML = hdObj.value;
		}else if(isAdmin == "1" && assign_type_U == true)
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Users&action=DetailView&record="+tagValue+"\">"+hdObj.value+"&nbsp;</a>";
		}else if(isAdmin == "1" && assign_type_G == true)
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Settings&action=GroupDetailView&groupId="+tagValue+"\">"+hdObj.value+"&nbsp;</a>";
		}
	}
	else if(uitype == '52' || uitype == '77')
	{
		if(isAdmin == "1")
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Users&action=DetailView&record="+tagValue+"\">"+document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text+"&nbsp;</a>";
		else
			getObj(dtlView).innerHTML = document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text;
	}
	else if(uitype == '56')
	{
		if(tagValue == '1')
		{
			getObj(dtlView).innerHTML = alert_arr.YES;
		}else
		{
			getObj(dtlView).innerHTML = alert_arr.NO;
		}

	}else if(uitype == 116 || uitype == 117)
	{
			getObj(dtlView).innerHTML = document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text; 
	}
	else if(getObj(popupTxt))
	{
		var popObj = getObj(popupTxt);
		if(uitype == '50' || uitype == '73' || uitype == '51')
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Accounts&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";
		}
		else if(uitype == '57')
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Contacts&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";
		}
		else if(uitype == '59')
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Products&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";
		}
		else if(uitype == '75' || uitype == '81' )
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Vendors&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";

		}
		else if(uitype == '76')
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Potentials&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";
		}
		else if(uitype == '78')
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Quotes&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";
		}
		else if(uitype == '80')
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=SalesOrder&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";
		}
		else if(uitype == '53')
		{
			var hdObj = getObj(hdTxt);
			if(isAdmin == "0")
			{
				getObj(dtlView).innerHTML = hdObj.value;
			}else if(isAdmin == "1")
			{
				getObj(dtlView).innerHTML = "<a href=\"index.php?module=Users&action=DetailView&record="+tagValue+"\">"+hdObj.value+"&nbsp;</a>";;
			}
		}
		else if(uitype == '56')
		{
			if(tagValue == '1')
			{
				getObj(dtlView).innerHTML = alert_arr.YES;
			}else
			{
				getObj(dtlView).innerHTML = "";
			}

		}
		else
		{
			getObj(dtlView).innerHTML = popObj.value;
		}
	}
	else if(uitype == '15' || uitype == '16' || uitype == '31' || uitype == '32')
        {
                        var notaccess =document.getElementById(txtBox);
                        tagValue = notaccess.options[notaccess.selectedIndex].text;
			if(tagValue == alert_arr.LBL_NOT_ACCESSIBLE)
				getObj(dtlView).innerHTML = "<font color='red'>"+get_converted_html(tagValue)+"</font>";
			else
				getObj(dtlView).innerHTML = get_converted_html(tagValue);
        }
	else if(uitype == '33')
  	{
		/* Wordwrap a long list of multi-select combo box items at the
                 * item separator string */
                var DETAILVIEW_WORDWRAP_WIDTH = "70"; // must match value in DetailViewUI.tpl.

                var lineLength = 0;
                for(var i=0; i < notaccess_label.length; i++) {
                        lineLength += notaccess_label[i].length + 2; // + 2 for item separator string
                        /*if(lineLength > DETAILVIEW_WORDWRAP_WIDTH && i > 0) {
                                lineLength = notaccess_label[i].length + 2; // reset.
                            	notaccess_label[i] = '<br/>&nbsp;' + notaccess_label[i]; // prepend newline.
                        }*/
			notaccess_label[i] = get_converted_html(notaccess_label[i]);
                        // Prevent a browser splitting multiword items:
                        //notaccess_label[i] = notaccess_label[i].replace(/ /g, '&nbsp;');
                        notaccess_label[i] = notaccess_label[i].replace(alert_arr.LBL_NOT_ACCESSIBLE,"<font color='red'>"+alert_arr.LBL_NOT_ACCESSIBLE+"</font>"); // for Not accessible label.
                }
                /* Join items with item separator string (which must match string in DetailViewUI.tpl,
                 * EditViewUtils.php and CRMEntity.php)!!
                 */
       		getObj(dtlView).innerHTML = notaccess_label.join(", ");
	}else if(uitype == '19'){
		var desc = tagValue.replace(/(^|[\n ])([\w]+?:\/\/.*?[^ \"\n\r\t<]*)/g, "$1<a href=\"$2\" target=\"_blank\">$2</a>");
		desc = desc.replace(/(^|[\n ])((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:\/[^ \"\t\n\r<]*)?)/g, "$1<a href=\"http://$2\" target=\"_blank\">$2</a>");
		desc = desc.replace(/(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)/i, "$1<a href=\"mailto:$2@$3\">$2@$3</a>");
		desc = desc.replace(/,\"|\.\"|\)\"|\)\.\"|\.\)\"/, "\"");
		desc = desc.replace(/[\n\r]+/g, "<br>&nbsp;");
		getObj(dtlView).innerHTML = desc;
	}
	else
	{
		getObj(dtlView).innerHTML = tagValue.replace(/[\n\r]+/g, "<br>&nbsp;");
	}
	showHide(dtlView,editArea);  //show,hide
	itsonview=false;
}

function SaveTag(tagfield,crmId,module)
{
	var tagValue = $(tagfield).value;
	tagValue = encodeURIComponent(tagValue);
	$("vtbusy_info").style.display="inline";
	new Ajax.Request(
		'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                       postBody: "file=TagCloud&module=" + module + "&action=" + module + "Ajax&recordid=" + crmId + "&ajxaction=SAVETAG&tagfields=" +tagValue,
                       onComplete: function(response) {
					if(response.responseText.indexOf(":#:FAILURE") > -1)
					{
						alert(alert_arr.VALID_DATA)
					}else{
				        	getObj('tagfields').innerHTML = response.responseText;
						$(tagfield).value = '';
					}
					$("vtbusy_info").style.display="none";
                        }
                }
        );
    
}
function setSelectValue(fieldLabel)
{
	if(globaluitype == 53)
	{
		if(typeof(document.DetailView.assigntype[0]) != 'undefined')
		{
			var assign_type_U = document.DetailView.assigntype[0].checked;
			var assign_type_G = document.DetailView.assigntype[1].checked;
			if(assign_type_U == true)
				var selCombo= 'txtbox_U'+fieldLabel;
			else if(assign_type_G == true)	
				var selCombo= 'txtbox_G'+fieldLabel;
		}else
		{
			var selCombo= 'txtbox_U'+fieldLabel;
		}
	}else
	{
			var selCombo= 'txtbox_'+fieldLabel;
	}
	var hdTxtBox = 'hdtxt_'+fieldLabel;
	var oHdTxtBox = document.getElementById(hdTxtBox);
	var oSelCombo = document.getElementById(selCombo);

	oHdTxtBox.value = oSelCombo.options[oSelCombo.selectedIndex].text;
}

//Added to ajax edit the folder name in Documents Module
function hndMouseClick(fieldLabel)
{
	var mouseArea="";
	mouseArea="mouseArea_"+ fieldLabel;
	if(itsonview)
	{
		return;
	}
	globaldtlviewspanid= "dtlview_"+ fieldLabel;//valuespanid;
	globaleditareaspanid="editarea_"+ fieldLabel;//textareapanid;
	globalfieldlabel = fieldLabel;
	globaltxtboxid="txtbox_"+ fieldLabel;//textboxpanid;
	$(globaltxtboxid).value = $(globaldtlviewspanid).innerHTML;
	handleEdit();
	$(globaltxtboxid).select();

}
