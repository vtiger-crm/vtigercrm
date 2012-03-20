/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/

function splitValues(object) {
        var picklistObj=object;
        var pickListContent=picklistObj.value
        var pickListAry=new Array()
        var i=0;

        //Splitting up of Values
        if (pickListContent.indexOf("\n")!=-1) {
                while(pickListContent.length>0) {
                        if(pickListContent.indexOf("\n")!=-1) {
                                if (pickListContent.replace(/^\s+/g, '').replace(/\s+$/g, '').length>0) {
                                        pickListAry[i]=pickListContent.substr(0,pickListContent.indexOf("\n")).replace(/^\s+/g, '').replace(/\s+$/g, '')
                                        pickListContent=pickListContent.substr(pickListContent.indexOf("\n")+1,pickListContent.length)
                                        i++
                                } else break;
                        } else {
                                pickListAry[i]=pickListContent.substr(0,pickListContent.length)
                                break;
                        }
                }
        } else if (pickListContent.replace(/^\s+/g, '').replace(/\s+$/g, '').length>0) {
                pickListAry[0]=pickListContent.replace(/^\s+/g, '').replace(/\s+$/g, '')
        }

        return pickListAry;
}


function validate(blockid) {
		var nummaxlength = 255;
        var fieldtype = document.getElementById('selectedfieldtype_'+blockid).value;
		var mode = document.getElementById('cfedit_mode').value;
        if(fieldtype == "" && mode != 'edit')
		{
			alert(alert_arr.FIELD_TYPE_NOT_SELECTED);
				return false;
		}
		lengthLayer=document.getElementById("lengthdetails_"+blockid)
        decimalLayer=document.getElementById("decimaldetails_"+blockid)
        var pickListLayer=document.getElementById("fldPickList_"+blockid);
        var fldlbl = document.getElementById("fldLabel_"+blockid);
        var str = fldlbl.value;
		if (!emptyCheck("fldLabel_"+blockid,"Label","text"))
                return false
	     var re2=/[&\<\>\:\'\"\,\_]/
        if (re2.test(str))
        {
                alert(alert_arr.SPECIAL_CHARACTERS+" & < > ' \" : , _ "+alert_arr.NOT_ALLOWED)
                return false;
        }
        var fieldlength = document.getElementById('fldLength_'+blockid);
		if (lengthLayer != null && lengthLayer.style.visibility=="visible") {
                if (!emptyCheck('fldLength_'+blockid,"Length"))
                        return false

                if (!intValidate('fldLength_'+blockid,"Length"))
                        return false

                if (!numConstComp('fldLength_'+blockid,"Length","G",0))
                        return false

        }

        if (decimalLayer != null && decimalLayer.style.visibility=="visible") {
                if (document.getElementById("fldDecimal_"+blockid).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length>0)
                        if (!intValidate("fldDecimal_"+blockid,"Decimal"))
                               return false
                if (!numConstComp("fldDecimal_"+blockid,"Decimal","GE",0))
                        return false

                if (!numConstComp("fldDecimal_"+blockid,"Decimal","LE",30))
                        return false
        }
		var decimallength = '';
		if (decimalLayer != null && decimalLayer.style.visibility=="visible" && document.getElementById('fldDecimal_'+blockid) != null)
	      	decimallength = document.getElementById("fldDecimal_"+blockid).value;
        
		if(fieldValueArr[fieldtype] == 'Percent' || fieldValueArr[fieldtype] == 'Currency' || fieldValueArr[fieldtype] == 'Number')
        {
                if(decimallength == '')
            		decimallength = 0;
            	nummaxlength = 65 - (eval(decimallength) + 1);
        }
		var lengthObj = document.getElementById("lengthdetails_"+blockid);
        if ( lengthObj != null && lengthObj.style.visibility == "visible" && !numConstComp('fldLength_'+blockid,"Length","LE",nummaxlength))
                return false
		var picklistObj=document.getElementById("fldPickList_"+blockid)
        if (pickListLayer != null && getObj("picklistdetails_"+blockid).style.visibility=="visible") {
                var pickListAry=new Array();
                pickListAry=splitValues(pickListLayer);
				if (emptyCheck("fldPickList_"+blockid,"Picklist values"))        {

                        //Empty Check validation
                        for (i=0;i<pickListAry.length;i++) {
                                if (pickListAry[i]=="") {
                          			alert(alert_arr.PICKLIST_CANNOT_BE_EMPTY);
                          			picklistObj.focus();
                          			return false
                                }
                        }

                        //Duplicate Values' Validation
                        for (i=0;i<pickListAry.length;i++) {
                   			for (j=i+1;j<pickListAry.length;j++) {
                 				if (trim(pickListAry[i].toUpperCase())== trim(pickListAry[j].toUpperCase())) {
                  					alert(alert_arr.DUPLICATE_VALUES_FOUND)
                 						picklistObj.focus();
                        					return false
                                        }
                                }
                        }

                        return true
                } else return false
        }
		return true;
}
var fieldValueArr=new Array('Text','Number','Percent','Currency','Date','Email','Phone','Picklist','URL','Checkbox','TextArea','MultiSelectCombo','Skype');
var fieldTypeArr=new Array('text','number','percent','currency','date','email','phone','picklist','url','checkbox','textarea','multiselectcombo','skype');
var currFieldIdx=0,totFieldType;
var focusFieldType;

/*function init() {
        lengthLayer=getObj("lengthdetails")
        decimalLayer=getObj("decimaldetails")
        pickListLayer=getObj("picklist")
        totFieldType=fieldTypeArr.length-1
}*/


function setVisible() {
        if (focusFieldType==true) {
                var selFieldType=fieldLayer.getObj("field"+currFieldIdx)
                var height=findPosY(selFieldType)+selFieldType.offsetHeight

                if (currFieldIdx==0) {
                        fieldLayer.document.body.scrollTop=0
                } else if (height>220) {
                        fieldLayer.document.body.scrollTop+=height-220
                } else {
                        fieldLayer.document.body.scrollTop-=220-height
                }

                if (window.navigator.appName.toUpperCase()=="OPERA") {
                                var newDiv=fieldLayer.document.createElement("DIV")
                                newDiv.style.zIndex="-1"
                                newDiv.style.position="absolute"
                                newDiv.style.top=findPosY(selFieldType)+"px"
                                newDiv.style.left="25px"

                                var newObj=fieldLayer.document.createElement("INPUT")
                                newObj.type="text"

                                fieldLayer.document.body.appendChild(newDiv)
                                newDiv.appendChild(newObj)
                                newObj.focus()

                                fieldLayer.document.body.removeChild(newDiv)
                }
        }
}

function selFieldType(id,scrollLayer,bool,blockid) {
        currFieldIdx=id
        var type=fieldTypeArr[id]
	var lengthLayer=document.getElementById("lengthdetails_"+blockid);
	var decimalLayer=document.getElementById("decimaldetails_"+blockid);
	var pickListLayer=document.getElementById("picklistdetails_"+blockid);
        if (type=='text') {
                lengthLayer.style.visibility="visible"
                decimalLayer.style.visibility="hidden"
                pickListLayer.style.visibility="hidden"
        } else if (type=='date' || type=='percent' || type=='email' || type=='phone' || type=='url' || type=='checkbox' || type=='textarea' || type=='skype') {
               	document.getElementById("lengthdetails_"+blockid).style.visibility="hidden"
                decimalLayer.style.visibility="hidden"
                pickListLayer.style.visibility="hidden"
        } else if (type=='number' || type=='currency') {
                lengthLayer.style.visibility="visible"
                decimalLayer.style.visibility="visible"
                pickListLayer.style.visibility="hidden"
        } else if (type=='picklist' || type=='multiselectcombo') {
                lengthLayer.style.visibility="hidden"
                decimalLayer.style.visibility="hidden"
                pickListLayer.style.visibility="visible"
        }
		document.getElementById("fieldType_"+blockid).value = fieldValueArr[id];
}

function srchFieldType(ev) {
        if (browser_ie) {
                var keyCode=window.fieldLayer.event.keyCode
                var currElement=window.fieldLayer.event.srcElement
                if (currElement.id.indexOf("field")>=0) var doSearch=true
                else var doSearch=false
                window.fieldLayer.event.cancelBubble=true
        } else if (browser_nn4 || browser_nn6) {
                var keyCode=ev.which
                var currElement=ev.target
                if (currElement.type) doSearch=false
                else doSearch=true
        }

        if (doSearch==true) {
                switch (keyCode) {
                        case 9  : //Reset Field Type
                                                resetFieldTypeHilite();break;
                        case 33 : //Page Up
                        case 36 : //Home
                                                selFieldType(0);break;
                        case 34 : //Page Down
                        case 35 : //End
                                                selFieldType(totFieldType);break;
                        case 38 : //Up
                                                if (currFieldIdx!=0)
                                                        selFieldType(currFieldIdx-1);
                                                else
                                                        selFieldType(totFieldType,"yes");
                                                break;
                        case 40 : //Down
                                                if (currFieldIdx!=totFieldType)
                                                        selFieldType(currFieldIdx+1);
                                                else
                                                        selFieldType(0,"yes");
default : //Character Search
                                                if (keyCode>=65 && keyCode<=90) {
                                                        var srchChar=String.fromCharCode(keyCode)
                                                        if (currFieldIdx==totFieldType) var startIdx=0
                                                        else var startIdx=currFieldIdx+1

                                                        var loop=1
                                                        for (i=startIdx;i<=totFieldType;) {
                                                                currFieldStr=fieldLayer.getObj("field"+i).innerHTML
                                                                currFieldStr=currFieldStr.replace(/^\s+/g, '').replace(/\s+$/g, '').substr(0,1)
                                                                if (currFieldStr==srchChar) {
                                                                        selFieldType(i,"yes")
                                                                        i++
                                                                } else if (i==totFieldType && loop<=2) {
                                                                        i=0
                                                                        loop++
                                                                } else i++
                                                        }
                                           }
                }
        }
}
function resetFieldTypeHilite() {
        fieldLayer.getObj("field"+currFieldIdx).className="fieldType sel"
}
function validateCustomFieldAccounts()
        {
                var obj=document.getElementsByTagName("SELECT");
                var i,j=0,k=0,l=0;
                var n=obj.length;
                account = new Array;
                contact =  new Array;
                potential = new Array;
                for( i = 0; i < n; i++)
                {
                        if(obj[i].name.indexOf("_account")>0)
                        {
                                account[j]=obj[i].value;
                                j++;
                        }
                        if(obj[i].name.indexOf("_contact")>0)
                        {
                                contact[k]=obj[i].value;
                                k++;
                        }
                        if(obj[i].name.indexOf("_potential")>0)
                        {
                                potential[l]=obj[i].value;
                                l++;
                        }
                }
                for( i = 0; i < account.length; i++)
                {
                        for(j=i+1; j<account.length; j++)
                        {
                                if( account[i] == account[j] && account[i]!="None" && account[j] !="None")
                                {
                                        alert(alert_arr.DUPLICATE_MAPPING_ACCOUNTS);
                                        return false;
                                }
                        }
                }
for( i = 0; i < contact.length; i++)
                {
                        for(k=i+1; k< contact.length; k++)
                        {
                                if( contact[i] == contact[k] && contact[i]!="None" && contact[k]!="None")
                                {
                                        alert(alert_arr.DUPLICATE_MAPPING_CONTACTS);
                                        return false;
                                }
                        }
                }
                for( i = 0; i < potential.length; i++)
                {
                        for(l=i+1; l<potential.length; l++)
                        {
                                if( potential[i] == potential[l] && potential[i]!="None" && potential[l]!="None")
                                {
                                        alert(alert_arr.DUPLICATE_MAPPING_POTENTIAL);
                                        return false;
                                }
                        }

                }
        }


function gotourl(url)
{
                document.location.href=url;
}

function validateTypeforCFMapping(leadtype,leadtypeofdata,field_name,cf_form)
{
	var alertmessage = new Array(alert_arr.LBL_TYPEALERT_1,alert_arr.LBL_WITH,alert_arr.LBL_TYPEALERT_2,alert_arr.LBL_LENGTHALERT,alert_arr.LBL_DECIMALALERT);
	var combo_val = cf_form.options[cf_form.selectedIndex].value;
	if(combo_val != 'None')
	{
		var type = document.getElementById(combo_val+"_type").value;
		var typeofdata = document.getElementById(combo_val+"_typeofdata").value;
		if(leadtype == type)
		{
			if(leadtypeofdata == typeofdata)
			{
				return true;
			}
			else
			{
				var lead_tod = leadtypeofdata.split("~");
				var tod = typeofdata.split("~");
				switch (lead_tod[0]) {
                	                case "V"  :
						if(lead_tod[3] <= tod[3])
							return true;
						else
						{
							alert(alertmessage[3]);
							document.getElementById(field_name).value = 'None';
							return false;
						}
						break;
					case "N"  :
						if(lead_tod[2].indexOf(",")>0)
						{
							var lead_dec = lead_tod[2].split(",");
							var dec = tod[2].split(",");
						
						}
						else
						{
							var lead_dec = lead_tod[2].split("~");
                	                                var dec = tod[2].split("~");
						}
						if(lead_dec[0] <= dec[0])
						{
							if(lead_dec[1] <= dec[1])
								return true;
							else
							{
								alert(alertmessage[4]);
								document.getElementById(field_name).value = 'None';
								return false;
							}
						}
						else
						{
							alert(alertmessage[3]);
							document.getElementById(field_name).value = 'None';
							return false;
						}
						break;
				}	
			}
		}
		else
		{
			alert(alertmessage[0]+" "+leadtype+" "+alertmessage[1]+" "+type+" "+alertmessage[2]);
			document.getElementById(field_name).value = 'None';
			return false;
		}
	}
}