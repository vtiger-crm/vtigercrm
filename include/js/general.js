/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

//Utility Functions

function c_toggleAssignType(currType){
		if (currType=="U")
		{
			document.getElementById("c_assign_user").style.display="block";
			document.getElementById("c_assign_team").style.display="none";
		}
		else
		{
			document.getElementById("c_assign_user").style.display="none";
			document.getElementById("c_assign_team").style.display="block";
		}
	}

var gValidationCall='';

if (document.all)

	var browser_ie=true

else if (document.layers)

	var browser_nn4=true

else if (document.layers || (!document.all && document.getElementById))

	var browser_nn6=true

var gBrowserAgent = navigator.userAgent.toLowerCase();

function hideSelect()
{
	var oselect_array = document.getElementsByTagName('SELECT');
	for(var i=0;i<oselect_array.length;i++)
	{
		oselect_array[i].style.display = 'none';
	}
}

function showSelect()
{
	var oselect_array = document.getElementsByTagName('SELECT');
	for(var i=0;i<oselect_array.length;i++)
	{
		oselect_array[i].style.display = 'block';
	}
}

function getObj(n,d) {

	var p,i,x;

	if(!d) {
		d=document;
	}

	if(n != undefined) {
		if((p=n.indexOf("?"))>0&&parent.frames.length) {
			d=parent.frames[n.substring(p+1)].document;
			n=n.substring(0,p);
		}
	}

	if(d.getElementById) {
		x=d.getElementById(n);
		// IE7 was returning form element with name = n (if there was multiple instance)
		// But not firefox, so we are making a double check
		if(x && x.id != n) x = false;
	}

	for(i=0;!x && i<d.forms.length;i++) {
		x=d.forms[i][n];
	}

	for(i=0; !x && d.layers && i<d.layers.length;i++) {
		x=getObj(n,d.layers[i].document);
	}

	if(!x && !(x=d[n]) && d.all) {
		x=d.all[n];
	}

	if(typeof x == 'string') {
		x=null;
	}

	return x;
}

function getOpenerObj(n) {

	return getObj(n,opener.document)

}



function findPosX(obj) {

	var curleft = 0;

	if (document.getElementById || document.all) {

		while (obj.offsetParent) {

			curleft += obj.offsetLeft

			obj = obj.offsetParent;

		}

	} else if (document.layers) {

		curleft += obj.x;

	}



	return curleft;

}



function findPosY(obj) {

	var curtop = 0;



	if (document.getElementById || document.all) {

		while (obj.offsetParent) {

			curtop += obj.offsetTop

			obj = obj.offsetParent;

		}

	}else if (document.layers) {

		curtop += obj.y;

	}



	return curtop;

}



function clearTextSelection() {

	if (browser_ie) document.selection.empty();

	else if (browser_nn4 || browser_nn6) window.getSelection().removeAllRanges();

}

// Setting cookies
function set_cookie ( name, value, exp_y, exp_m, exp_d, path, domain, secure )
{
	var cookie_string = name + "=" + escape ( value );

	if (exp_y) //delete_cookie(name)
	{
		var expires = new Date ( exp_y, exp_m, exp_d );
		cookie_string += "; expires=" + expires.toGMTString();
	}

	if (path) cookie_string += "; path=" + escape ( path );
	if (domain) cookie_string += "; domain=" + escape ( domain );
	if (secure) cookie_string += "; secure";

	document.cookie = cookie_string;
}

// Retrieving cookies
function get_cookie(cookie_name)
{
	var results = document.cookie.match(cookie_name + '=(.*?)(;|$)');
	if (results) return (unescape(results[1]));
	else return null;
}

// Delete cookies
function delete_cookie( cookie_name )
{
	var cookie_date = new Date ( );  // current date & time
	cookie_date.setTime ( cookie_date.getTime() - 1 );
	document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}
//End of Utility Functions



function emptyCheck(fldName,fldLabel, fldType) {
	var currObj = getObj(fldName);
	if (fldType=="text") {
		if (currObj.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0) {
			alert(fldLabel+alert_arr.CANNOT_BE_EMPTY)
			try {
				currObj.focus()
			} catch(error) {
			// Fix for IE: If element or its wrapper around it is hidden, setting focus will fail
			// So using the try { } catch(error) { }
			}
			return false
		}
		else{
			return true
		}
	}else if((fldType == "textarea")
		&& (typeof(CKEDITOR)!=='undefined' && CKEDITOR.intances[fldName] !== 'undefined')) {
		var textObj = CKEDITOR.intances[fldName];
		var textValue = textObj.getData();
		if (trim(textValue) == '' || trim(textValue) == '<br>') {
			alert(fldLabel+alert_arr.CANNOT_BE_NONE);
			return false;
		} else{
			return true;
		}
	}	else{
		if (trim(currObj.value) == '') {
			alert(fldLabel+alert_arr.CANNOT_BE_NONE)
			return false
		} else
			return true
	}
}



function patternValidate(fldName,fldLabel,type) {
	var currObj=getObj(fldName);

	if (type.toUpperCase()=="EMAIL") //Email ID validation
	{
		/*changes made to fix -- ticket#3278 & ticket#3461
		  var re=new RegExp(/^.+@.+\..+$/)*/
		//Changes made to fix tickets #4633, #5111  to accomodate all possible email formats
 	    var re=new RegExp(/^[a-zA-Z0-9]+([!"#$%&'()*+,./:;<=>?@\^_`{|}~-]?[a-zA-Z0-9])*@[a-zA-Z0-9]+([\_\-\.]?[a-zA-Z0-9]+)*\.([\-\_]?[a-zA-Z0-9])+(\.?[a-zA-Z0-9]+)?$/);
	}

	if (type.toUpperCase()=="DATE") {//DATE validation
		//YMD
		//var reg1 = /^\d{2}(\-|\/|\.)\d{1,2}\1\d{1,2}$/ //2 digit year
		//var re = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/ //4 digit year

		//MYD
		//var reg1 = /^\d{1,2}(\-|\/|\.)\d{2}\1\d{1,2}$/
		//var reg2 = /^\d{1,2}(\-|\/|\.)\d{4}\1\d{1,2}$/

		//DMY
		//var reg1 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{2}$/
		//var reg2 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/

		switch (userDateFormat) {
			case "yyyy-mm-dd" :
				var re = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/
				break;
			case "mm-dd-yyyy" :
			case "dd-mm-yyyy" :
				var re = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/
		}
	}

	if (type.toUpperCase()=="TIME") {//TIME validation
		var re = /^\d{1,2}\:\d{2}:\d{2}$|^\d{1,2}\:\d{2}$/
	}
	//Asha: Remove spaces on either side of a Email id before validating
	if (type.toUpperCase()=="EMAIL" || type.toUpperCase() == "DATE") currObj.value = trim(currObj.value);
	if (!re.test(currObj.value)) {
		alert(alert_arr.ENTER_VALID + fldLabel  + " ("+type+")");
		try {
			currObj.focus()
		} catch(error) {
		// Fix for IE: If element or its wrapper around it is hidden, setting focus will fail
		// So using the try { } catch(error) { }
		}
		return false
	}
	else return true
}

function splitDateVal(dateval) {
	var datesep;
	var dateelements = new Array(3);

	if (dateval.indexOf("-")>=0) datesep="-"
	else if (dateval.indexOf(".")>=0) datesep="."
	else if (dateval.indexOf("/")>=0) datesep="/"

	switch (userDateFormat) {
		case "yyyy-mm-dd" :
			dateelements[0]=dateval.substr(dateval.lastIndexOf(datesep)+1,dateval.length) //dd
			dateelements[1]=dateval.substring(dateval.indexOf(datesep)+1,dateval.lastIndexOf(datesep)) //mm
			dateelements[2]=dateval.substring(0,dateval.indexOf(datesep)) //yyyyy
			break;
		case "mm-dd-yyyy" :
			dateelements[0]=dateval.substring(dateval.indexOf(datesep)+1,dateval.lastIndexOf(datesep))
			dateelements[1]=dateval.substring(0,dateval.indexOf(datesep))
			dateelements[2]=dateval.substr(dateval.lastIndexOf(datesep)+1,dateval.length)
			break;
		case "dd-mm-yyyy" :
			dateelements[0]=dateval.substring(0,dateval.indexOf(datesep))
			dateelements[1]=dateval.substring(dateval.indexOf(datesep)+1,dateval.lastIndexOf(datesep))
			dateelements[2]=dateval.substr(dateval.lastIndexOf(datesep)+1,dateval.length)
	}

	return dateelements;
}

function compareDates(date1,fldLabel1,date2,fldLabel2,type) {
	var ret=true
	switch (type) {
		case 'L'	:
			if (date1>=date2) {//DATE1 VALUE LESS THAN DATE2
			alert(fldLabel1+ alert_arr.SHOULDBE_LESS +fldLabel2)
			ret=false
		}
		break;
		case 'LE'	:
			if (date1>date2) {//DATE1 VALUE LESS THAN OR EQUAL TO DATE2
			alert(fldLabel1+alert_arr.SHOULDBE_LESS_EQUAL+fldLabel2)
			ret=false
		}
		break;
		case 'E'	:
			if (date1!=date2) {//DATE1 VALUE EQUAL TO DATE
			alert(fldLabel1+alert_arr.SHOULDBE_EQUAL+fldLabel2)
			ret=false
		}
		break;
		case 'G'	:
			if (date1<=date2) {//DATE1 VALUE GREATER THAN DATE2
			alert(fldLabel1+alert_arr.SHOULDBE_GREATER+fldLabel2)
			ret=false
		}
		break;
		case 'GE'	:
			if (date1<date2) {//DATE1 VALUE GREATER THAN OR EQUAL TO DATE2
			alert(fldLabel1+alert_arr.SHOULDBE_GREATER_EQUAL+fldLabel2)
			ret=false
		}
		break;
	}

	if (ret==false) return false
	else return true
}

function dateTimeValidate(dateFldName,timeFldName,fldLabel,type) {
	if(patternValidate(dateFldName,fldLabel,"DATE")==false)
		return false;
	dateval=getObj(dateFldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '')

	var dateelements=splitDateVal(dateval)

	dd=dateelements[0]
	mm=dateelements[1]
	yyyy=dateelements[2]

	if (dd<1 || dd>31 || mm<1 || mm>12 || yyyy<1 || yyyy<1000) {
		alert(alert_arr.ENTER_VALID+fldLabel)
		try {
			getObj(dateFldName).focus()
		} catch(error) { }
		return false
	}

	if ((mm==2) && (dd>29)) {//checking of no. of days in february month
		alert(alert_arr.ENTER_VALID+fldLabel)
		try {
			getObj(dateFldName).focus()
		} catch(error) { }
		return false
	}

	if ((mm==2) && (dd>28) && ((yyyy%4)!=0)) {//leap year checking
		alert(alert_arr.ENTER_VALID+fldLabel)
		try {
			getObj(dateFldName).focus()
		} catch(error) { }
		return false
	}

	switch (parseInt(mm)) {
		case 2 :
		case 4 :
		case 6 :
		case 9 :
		case 11 :
			if (dd>30) {
			alert(alert_arr.ENTER_VALID+fldLabel)
			try {
				getObj(dateFldName).focus()
			} catch(error) { }
			return false
		}
	}

	if (patternValidate(timeFldName,fldLabel,"TIME")==false)
		return false

	var timeval=getObj(timeFldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '')
	var hourval=parseInt(timeval.substring(0,timeval.indexOf(":")))
	var minval=parseInt(timeval.substring(timeval.indexOf(":")+1,timeval.length))
	var currObj=getObj(timeFldName)

	if (hourval>23 || minval>59) {
		alert(alert_arr.ENTER_VALID+fldLabel)
		try {
			currObj.focus()
		} catch(error) { }
		return false
	}

	var currdate=new Date()
	var chkdate=new Date()

	chkdate.setYear(yyyy)
	chkdate.setMonth(mm-1)
	chkdate.setDate(dd)
	chkdate.setHours(hourval)
	chkdate.setMinutes(minval)

	if (type!="OTH") {
		if (!compareDates(chkdate,fldLabel,currdate,"current date & time",type)) {
			try {
				getObj(dateFldName).focus()
			} catch(error) { }
			return false
		} else return true;
	} else return true;
}

function dateTimeComparison(dateFldName1,timeFldName1,fldLabel1,dateFldName2,timeFldName2,fldLabel2,type) {
	var dateval1=getObj(dateFldName1).value.replace(/^\s+/g, '').replace(/\s+$/g, '')
	var dateval2=getObj(dateFldName2).value.replace(/^\s+/g, '').replace(/\s+$/g, '')

	var dateelements1=splitDateVal(dateval1)
	var dateelements2=splitDateVal(dateval2)

	dd1=dateelements1[0]
	mm1=dateelements1[1]
	yyyy1=dateelements1[2]

	dd2=dateelements2[0]
	mm2=dateelements2[1]
	yyyy2=dateelements2[2]

	var timeval1=getObj(timeFldName1).value.replace(/^\s+/g, '').replace(/\s+$/g, '')
	var timeval2=getObj(timeFldName2).value.replace(/^\s+/g, '').replace(/\s+$/g, '')

	var hh1=timeval1.substring(0,timeval1.indexOf(":"))
	var min1=timeval1.substring(timeval1.indexOf(":")+1,timeval1.length)

	var hh2=timeval2.substring(0,timeval2.indexOf(":"))
	var min2=timeval2.substring(timeval2.indexOf(":")+1,timeval2.length)

	var date1=new Date()
	var date2=new Date()

	date1.setYear(yyyy1)
	date1.setMonth(mm1-1)
	date1.setDate(dd1)
	date1.setHours(hh1)
	date1.setMinutes(min1)

	date2.setYear(yyyy2)
	date2.setMonth(mm2-1)
	date2.setDate(dd2)
	date2.setHours(hh2)
	date2.setMinutes(min2)

	if (type!="OTH") {
		if (!compareDates(date1,fldLabel1,date2,fldLabel2,type)) {
			try {
				getObj(dateFldName1).focus()
			} catch(error) { }
			return false
		} else return true;
	} else return true;
}

function dateValidate(fldName,fldLabel,type) {
	if(patternValidate(fldName,fldLabel,"DATE")==false)
		return false;
	dateval=getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '')

	var dateelements=splitDateVal(dateval)

	dd=dateelements[0]
	mm=dateelements[1]
	yyyy=dateelements[2]

	if (dd<1 || dd>31 || mm<1 || mm>12 || yyyy<1 || yyyy<1000) {
		alert(alert_arr.ENTER_VALID+fldLabel)
		try {
			getObj(fldName).focus()
		} catch(error) { }
		return false
	}

	if ((mm==2) && (dd>29)) {//checking of no. of days in february month
		alert(alert_arr.ENTER_VALID+fldLabel)
		try {
			getObj(fldName).focus()
		} catch(error) { }
		return false
	}

	if ((mm==2) && (dd>28) && ((yyyy%4)!=0)) {//leap year checking
		alert(alert_arr.ENTER_VALID+fldLabel)
		try {
			getObj(fldName).focus()
		} catch(error) { }
		return false
	}

	switch (parseInt(mm)) {
		case 2 :
		case 4 :
		case 6 :
		case 9 :
		case 11 :
			if (dd>30) {
			alert(alert_arr.ENTER_VALID+fldLabel)
			try {
				getObj(fldName).focus()
			} catch(error) { }
			return false
		}
	}

	var currdate=new Date()
	var chkdate=new Date()

	chkdate.setYear(yyyy)
	chkdate.setMonth(mm-1)
	chkdate.setDate(dd)

	if (type!="OTH") {
		if (!compareDates(chkdate,fldLabel,currdate,"current date",type)) {
			try {
				getObj(fldName).focus()
			} catch(error) { }
			return false
		} else return true;
	} else return true;
}

function dateComparison(fldName1,fldLabel1,fldName2,fldLabel2,type) {
	var dateval1=getObj(fldName1).value.replace(/^\s+/g, '').replace(/\s+$/g, '')
	var dateval2=getObj(fldName2).value.replace(/^\s+/g, '').replace(/\s+$/g, '')

	var dateelements1=splitDateVal(dateval1)
	var dateelements2=splitDateVal(dateval2)

	dd1=dateelements1[0]
	mm1=dateelements1[1]
	yyyy1=dateelements1[2]

	dd2=dateelements2[0]
	mm2=dateelements2[1]
	yyyy2=dateelements2[2]

	var date1=new Date()
	var date2=new Date()

	date1.setYear(yyyy1)
	date1.setMonth(mm1-1)
	date1.setDate(dd1)

	date2.setYear(yyyy2)
	date2.setMonth(mm2-1)
	date2.setDate(dd2)

	if (type!="OTH") {
		if (!compareDates(date1,fldLabel1,date2,fldLabel2,type)) {
			try {
				getObj(fldName1).focus()
			} catch(error) { }
			return false
		} else return true;
	} else return true
}

function timeValidate(fldName,fldLabel,type) {
	if (patternValidate(fldName,fldLabel,"TIME")==false)
		return false

	var timeval=getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '')
	var hourval=parseInt(timeval.substring(0,timeval.indexOf(":")))
	var minval=parseInt(timeval.substring(timeval.indexOf(":")+1,timeval.length))
	var secval=parseInt(timeval.substring(timeval.indexOf(":")+4,timeval.length))
	var currObj=getObj(fldName)

	if (hourval>23 || minval>59 || secval>59) {
		alert(alert_arr.ENTER_VALID+fldLabel)
		try {
			currObj.focus()
		} catch(error) { }
		return false
	}

	var currtime=new Date()
	var chktime=new Date()

	chktime.setHours(hourval)
	chktime.setMinutes(minval)
	chktime.setSeconds(secval)

	if (type!="OTH") {
		if (!compareDates(chktime,fldLabel,currtime,"current time",type)) {
			try {
				getObj(fldName).focus()
			} catch(error) { }
			return false
		} else return true;
	} else return true
}

function timeComparison(fldName1,fldLabel1,fldName2,fldLabel2,type) {
	var timeval1=getObj(fldName1).value.replace(/^\s+/g, '').replace(/\s+$/g, '')
	var timeval2=getObj(fldName2).value.replace(/^\s+/g, '').replace(/\s+$/g, '')

	var hh1=timeval1.substring(0,timeval1.indexOf(":"))
	var min1=timeval1.substring(timeval1.indexOf(":")+1,timeval1.length)

	var hh2=timeval2.substring(0,timeval2.indexOf(":"))
	var min2=timeval2.substring(timeval2.indexOf(":")+1,timeval2.length)

	var time1=new Date()
	var time2=new Date()

	//added to fix the ticket #5028
	if(fldName1 == "time_end" && (getObj("due_date") && getObj("date_start")))
	{
		var due_date=getObj("due_date").value.replace(/^\s+/g, '').replace(/\s+$/g, '')
		var start_date=getObj("date_start").value.replace(/^\s+/g, '').replace(/\s+$/g, '')
		dateval1 = splitDateVal(due_date);
		dateval2 = splitDateVal(start_date);

		dd1 = dateval1[0];
		mm1 = dateval1[1];
		yyyy1 = dateval1[2];

		dd2 = dateval2[0];
		mm2 = dateval2[1];
		yyyy2 = dateval2[2];

		time1.setYear(yyyy1)
		time1.setMonth(mm1-1)
		time1.setDate(dd1)

		time2.setYear(yyyy2)
		time2.setMonth(mm2-1)
		time2.setDate(dd2)

	}
	//end

	time1.setHours(hh1)
	time1.setMinutes(min1)

	time2.setHours(hh2)
	time2.setMinutes(min2)
	if (type!="OTH") {
		if (!compareDates(time1,fldLabel1,time2,fldLabel2,type)) {
			try {
				getObj(fldName1).focus()
			} catch(error) { }
			return false
		} else return true;
	} else return true;
}

function numValidate(fldName,fldLabel,format,neg) {
	var val=getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	if(typeof userCurrencySeparator != 'undefined' && userCurrencySeparator != '') {
		while(val.indexOf(userCurrencySeparator) != -1) {
			val = val.replace(userCurrencySeparator,'');
		}
	}
	if(typeof userDecimalSeparator != 'undefined' && userDecimalSeparator != '') {
		if(val.indexOf(userDecimalSeparator) != -1) {
			val = val.replace(userDecimalSeparator,'.');
		}
	}
	if (format!="any") {
		if (isNaN(val)) {
			var invalid=true
		} else {
			var format=format.split(",")
			var splitval=val.split(".")
			if (neg==true) {
				if (splitval[0].indexOf("-")>=0) {
					if (splitval[0].length-1>format[0])
						invalid=true
				} else {
					if (splitval[0].length>format[0])
						invalid=true
				}
			} else {
				if (val<0)
					invalid=true
				else if (format[0]==2 && splitval[0]==100 && (!splitval[1] || splitval[1]==0))
					invalid=false
				else if (splitval[0].length>format[0])
					invalid=true
			}
			if (splitval[1])
				if (splitval[1].length>format[1])
					invalid=true
		}
		if (invalid==true) {
			alert(alert_arr.INVALID+fldLabel)
			try {
				getObj(fldName).focus()
			} catch(error) { }
			return false
		}else return true
	} else {
		// changes made -- to fix the ticket#3272
		if(fldName == "probability" || fldName == "commissionrate")
		{
			var val=getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
			var splitval=val.split(".")
			var arr_len = splitval.length;
			var len = 0;

			if(arr_len > 1)
				len = splitval[1].length;
			if(isNaN(val))
			{
				alert(alert_arr.INVALID+fldLabel)
				try {
					getObj(fldName).focus()
				}catch(error) { }
				return false
			}
			else if(splitval[0] > 100 || len > 3 || (splitval[0] >= 100 && splitval[1] > 0))
			{
				alert( fldLabel + alert_arr.EXCEEDS_MAX);
				return false;
			}
		}
		else {
			var splitval=val.split(".")
			if(splitval[0]>18446744073709551615)
			{
				alert( fldLabel + alert_arr.EXCEEDS_MAX);
				return false;
			}
		}

		if (neg==true)
			var re=/^(-|)(\d)*(\.)?\d+(\.\d\d*)*$/
		else
			var re=/^(\d)*(\.)?\d+(\.\d\d*)*$/
	}

	//for precision check. ie.number must contains only one "."
	var dotcount=0;
	for (var i = 0; i < val.length; i++)
	{
		if (val.charAt(i) == ".")
			dotcount++;
	}

	if(dotcount>1)
	{
		alert(alert_arr.INVALID+fldLabel)
		try {
			getObj(fldName).focus()
		}catch(error) { }
		return false;
	}

	if (!re.test(val)) {
		alert(alert_arr.INVALID+fldLabel)
		try {
			getObj(fldName).focus()
		} catch(error) { }
		return false
	} else return true
}


function intValidate(fldName,fldLabel) {
	var val=getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	if(typeof userCurrencySeparator != 'undefined' && userCurrencySeparator != '') {
		while(val.indexOf(userCurrencySeparator) != -1) {
			val = val.replace(userCurrencySeparator,'');
		}
	}
	if (isNaN(val) || (val.indexOf(".")!=-1 && fldName != 'potential_amount' && fldName != 'list_price'))
	{
		alert(alert_arr.INVALID+fldLabel)
		try {
			getObj(fldName).focus()
		} catch(error) { }
		return false
	}
	else if((fldName != 'employees' || fldName != 'noofemployees') && (val < -2147483648 || val > 2147483647))
	{
		alert(fldLabel +alert_arr.OUT_OF_RANGE);
		return false;
	}
	else if((fldName == 'employees' || fldName != 'noofemployees') && (val < 0 || val > 2147483647))
	{
		alert(fldLabel +alert_arr.OUT_OF_RANGE);
		return false;
	}
	else
	{
		return true
	}
}

function numConstComp(fldName,fldLabel,type,constval) {
	var val=parseFloat(getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, ''))
	constval=parseFloat(constval)

	var ret=true
	switch (type) {
		case "L"  :
			if (val>=constval) {
			alert(fldLabel+alert_arr.SHOULDBE_LESS+constval)
			ret=false
		}
		break;
		case "LE" :
			if (val>constval) {
			alert(fldLabel+alert_arr.SHOULDBE_LESS_EQUAL+constval)
			ret=false
		}
		break;
		case "E"  :
			if (val!=constval) {
			alert(fldLabel+alert_arr.SHOULDBE_EQUAL+constval)
			ret=false
		}
		break;
		case "NE" :
			if (val==constval) {
			alert(fldLabel+alert_arr.SHOULDNOTBE_EQUAL+constval)
			ret=false
		}
		break;
		case "G"  :
			if (val<=constval) {
			alert(fldLabel+alert_arr.SHOULDBE_GREATER+constval)
			ret=false
		}
		break;
		case "GE" :
			if (val<constval) {
			alert(fldLabel+alert_arr.SHOULDBE_GREATER_EQUAL+constval)
			ret=false
		}
		break;
	}

	if (ret==false) {
		try {
			getObj(fldName).focus()
		} catch(error) { }
		return false
	} else return true;
}

/* To get only filename from a given complete file path */
function getFileNameOnly(filename) {
	var onlyfilename = filename;
	// Normalize the path (to make sure we use the same path separator)
	var filename_normalized = filename.replace(/\\/g, '/');
	if(filename_normalized.lastIndexOf("/") != -1) {
		onlyfilename = filename_normalized.substring(filename_normalized.lastIndexOf("/") + 1);
	}
	return onlyfilename;
}

/* Function to validate the filename */
function validateFilename(form_ele) {
	if (form_ele.value == '') return true;
	var value = getFileNameOnly(form_ele.value);

	// Color highlighting logic
	var err_bg_color = "#FFAA22";

	if (typeof(form_ele.bgcolor) == "undefined") {
		form_ele.bgcolor = form_ele.style.backgroundColor;
	}

	// Validation starts here
	var valid = true;

	/* Filename length is constrained to 255 at database level */
	if (value.length > 255) {
		alert(alert_arr.LBL_FILENAME_LENGTH_EXCEED_ERR);
		valid = false;
	}

	if (!valid) {
		form_ele.style.backgroundColor = err_bg_color;
		return false;
	}
	form_ele.style.backgroundColor = form_ele.bgcolor;
	form_ele.form[form_ele.name + '_hidden'].value = value;
	displayFileSize(form_ele);
	return true;
}

/* Function to validate the filsize */
function validateFileSize(form_ele,uploadSize) {
	if (form_ele.value == '') return true;
	var fileSize = form_ele.files[0].size;
	if(fileSize > uploadSize) {
		alert(alert_arr.LBL_SIZE_SHOULDNOTBE_GREATER + uploadSize/1000000+alert_arr.LBL_FILESIZEIN_MB);
		form_ele.value = '';
		document.getElementById('displaySize').innerHTML= '';
	} else {
		displayFileSize(form_ele);
	}
}

/* Function to Display FileSize while uploading */
function displayFileSize(form_ele) {
	var fileSize = form_ele.files[0].size;
	if (fileSize < 1024)
		document.getElementById('displaySize').innerHTML = fileSize + alert_arr.LBL_FILESIZEIN_B;
	else if (fileSize > 1024 && fileSize < 1048576)
		document.getElementById('displaySize').innerHTML = Math.round(fileSize / 1024, 2) + alert_arr.LBL_FILESIZEIN_KB;
	else if (fileSize > 1048576)
		document.getElementById('displaySize').innerHTML = Math.round(fileSize / (1024 * 1024), 2) + alert_arr.LBL_FILESIZEIN_MB;
}

function formValidate(){
	return doformValidation('');
}

function massEditFormValidate(){
	return doformValidation('mass_edit');
}

function doformValidation(edit_type) {
	//Validation for Portal User
	if(gVTModule == 'Contacts' && gValidationCall != 'tabchange')
	{
		//if existing portal value = 0, portal checkbox = checked, ( email field is not available OR  email is empty ) then we should not allow -- OR --
		//if existing portal value = 1, portal checkbox = checked, ( email field is available     AND email is empty ) then we should not allow
		if(edit_type=='')
		{
			if(getObj('existing_portal') != null
				&& ((getObj('existing_portal').value == 0 && getObj('portal').checked &&
					(getObj('email') == null || trim(getObj('email').value) == '')) ||
				(getObj('existing_portal').value == 1 && getObj('portal').checked &&
					getObj('email') != null && trim(getObj('email').value) == ''))) {

				alert(alert_arr.PORTAL_PROVIDE_EMAILID);
				return false;
			}
		}
		else
		{
			if(getObj('portal') != null && getObj('portal').checked && getObj('portal_mass_edit_check').checked && (getObj('email') == null || trim(getObj('email').value) == '' || getObj('email_mass_edit_check').checked==false))
			{
				alert(alert_arr.PORTAL_PROVIDE_EMAILID);
				return false;
			}
			if((getObj('email') != null && trim(getObj('email').value) == '' && getObj('email_mass_edit_check').checked) && !(getObj('portal').checked==false && getObj('portal_mass_edit_check').checked))
			{
				alert(alert_arr.EMAIL_CHECK_MSG);
				return false;
			}
		}
	}
	if(gVTModule == 'SalesOrder') {
		if(edit_type == 'mass_edit') {
			if (getObj('enable_recurring_mass_edit_check') != null
				&& getObj('enable_recurring_mass_edit_check').checked
				&& getObj('enable_recurring') != null) {
				if(getObj('enable_recurring').checked && (getObj('recurring_frequency') == null
					|| trim(getObj('recurring_frequency').value) == '--None--' || getObj('recurring_frequency_mass_edit_check').checked==false)) {
					alert(alert_arr.RECURRING_FREQUENCY_NOT_PROVIDED);
					return false;
				}
				if(getObj('enable_recurring').checked == false && getObj('recurring_frequency_mass_edit_check').checked
					&& getObj('recurring_frequency') != null && trim(getObj('recurring_frequency').value) !=  '--None--') {
					alert(alert_arr.RECURRING_FREQNECY_NOT_ENABLED);
					return false;
				}
			}
		} else if(getObj('enable_recurring') != null && getObj('enable_recurring').checked) {
			if(getObj('recurring_frequency') == null || getObj('recurring_frequency').value == '--None--') {
				alert(alert_arr.RECURRING_FREQUENCY_NOT_PROVIDED);
				return false;
			}
			var start_period = getObj('start_period');
			var end_period = getObj('end_period');
			if (trim(start_period.value) == '' || trim(end_period.value) == '') {
				alert(alert_arr.START_PERIOD_END_PERIOD_CANNOT_BE_EMPTY);
				return false;
			}
		}
	}
	for (var i=0; i<fieldname.length; i++) {
		if(edit_type == 'mass_edit') {
			if(fieldname[i]!='salutationtype')
				var obj = getObj(fieldname[i]+"_mass_edit_check");
			if(obj == null || obj.checked == false) continue;
		}
		if(getObj(fieldname[i]) != null)
		{
			var type=fielddatatype[i].split("~")
			if (type[1]=="M") {
				if (!emptyCheck(fieldname[i],fieldlabel[i],getObj(fieldname[i]).type))
					return false;
			}
			switch (type[0]) {
				case "O"  :
					break;
				case "V"  :
					break;
				case "C"  :
					break;
				case "DT" :
					if (getObj(fieldname[i]) != null && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)
					{
						if (type[1]=="M")
							if (!emptyCheck(fieldname[2],fieldlabel[i],getObj(type[2]).type))
								return false

						if(typeof(type[3])=="undefined") var currdatechk="OTH"
						else var currdatechk=type[3]

						if (!dateTimeValidate(fieldname[i],type[2],fieldlabel[i],currdatechk))
							return false
						if (type[4]) {
							if (!dateTimeComparison(fieldname[i],type[2],fieldlabel[i],type[5],type[6],type[4]))
								return false

						}
					}
					break;
				case "D"  :
					if (getObj(fieldname[i]) != null && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)
					{
						if(typeof(type[2])=="undefined") var currdatechk="OTH"
						else var currdatechk=type[2]

						if (!dateValidate(fieldname[i],fieldlabel[i],currdatechk))
							return false
						if (type[3]) {
							if(gVTModule == 'SalesOrder' && fieldname[i] == 'end_period'
								&& (getObj('enable_recurring') == null || getObj('enable_recurring').checked == false)) {
								continue;
							}
							if (!dateComparison(fieldname[i],fieldlabel[i],type[4],type[5],type[3]))
								return false
						}
					}
					break;
				case "T"  :
					if (getObj(fieldname[i]) != null && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)
					{
						if(typeof(type[2])=="undefined") var currtimechk="OTH"
						else var currtimechk=type[2]

						if (!timeValidate(fieldname[i],fieldlabel[i],currtimechk))
							return false
						if (type[3]) {
							if (!timeComparison(fieldname[i],fieldlabel[i],type[4],type[5],type[3]))
								return false
						}
					}
					break;
				case "I"  :
					if (getObj(fieldname[i]) != null && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)
					{
						if (getObj(fieldname[i]).value.length!=0)
						{
							if (!intValidate(fieldname[i],fieldlabel[i]))
								return false
							if (type[2]) {
								if (!numConstComp(fieldname[i],fieldlabel[i],type[2],type[3]))
									return false
							}
						}
					}
					break;
				case "N"  :
				case "NN" :
					if (getObj(fieldname[i]) != null && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)
					{
						if (getObj(fieldname[i]).value.length!=0)
						{
							if (typeof(type[2])=="undefined") var numformat="any"
							else var numformat=type[2]
							if(type[0]=="NN")
							{
								if (!numValidate(fieldname[i],fieldlabel[i],numformat,true))
									return false
							}
							else if (!numValidate(fieldname[i],fieldlabel[i],numformat))
								return false
							if (type[3]) {
								if (!numConstComp(fieldname[i],fieldlabel[i],type[3],type[4]))
									return false
							}
						}
					}
					break;
				case "E"  :
					if (getObj(fieldname[i]) != null && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0)
					{
						if (getObj(fieldname[i]).value.length!=0)
						{
							var etype = "EMAIL";
							if (!patternValidate(fieldname[i],fieldlabel[i],etype))
								return false;
						}
					}
					break;
			}
			//start Birth day date validation
			if(fieldname[i] == "birthday" && getObj(fieldname[i]).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length!=0 )
			{
				var now =new Date()
				var currtimechk="OTH"
				var datelabel = fieldlabel[i]
				var datefield = fieldname[i]
				var datevalue =getObj(datefield).value.replace(/^\s+/g, '').replace(/\s+$/g, '')
				if (!dateValidate(fieldname[i],fieldlabel[i],currdatechk))
				{
					try {
						getObj(datefield).focus()
					} catch(error) { }
					return false
				}
				else
				{
					datearr=splitDateVal(datevalue);
					dd=datearr[0]
					mm=datearr[1]
					yyyy=datearr[2]
					var datecheck = new Date()
					datecheck.setYear(yyyy)
					datecheck.setMonth(mm-1)
					datecheck.setDate(dd)
					if (!compareDates(datecheck,datelabel,now,"Current Date","L"))
					{
						try {
							getObj(datefield).focus()
						} catch(error) { }
						return false
					}
				}
			}
		//End Birth day
		}

	}
	if(gVTModule == 'Contacts')
	{
		if(getObj('imagename'))
		{
			if(getObj('imagename').value != '')
			{
				var image_arr = new Array();
				image_arr = (getObj('imagename').value).split(".");
				var image_arr_last_index = image_arr.length - 1;
				if(image_arr_last_index < 0) {
					alert(alert_arr.LBL_WRONG_IMAGE_TYPE);
					return false;
				}
				var image_ext = image_arr[image_arr_last_index].toLowerCase();
				if(image_ext ==  "jpeg" || image_ext ==  "png" || image_ext ==  "jpg" || image_ext ==  "pjpeg" || image_ext ==  "x-png" || image_ext ==  "gif")
				{
					return true;
				}
				else
				{
					alert(alert_arr.LBL_WRONG_IMAGE_TYPE);
					return false;
				}
			}
		}
	}

	//added to check Start Date & Time,if Activity Status is Planned.//start
	for (var j=0; j<fieldname.length; j++)
	{
		if(getObj(fieldname[j]) != null)
		{
			if(fieldname[j] == "date_start" || fieldname[j] == "task_date_start" )
			{
				var datelabel = fieldlabel[j]
				var datefield = fieldname[j]
				var startdatevalue = getObj(datefield).value.replace(/^\s+/g, '').replace(/\s+$/g, '')
			}
			if(fieldname[j] == "time_start" || fieldname[j] == "task_time_start")
			{
				var timelabel = fieldlabel[j]
				var timefield = fieldname[j]
				var timeval=getObj(timefield).value.replace(/^\s+/g, '').replace(/\s+$/g, '')
			}
			if(fieldname[j] == "eventstatus" || fieldname[j] == "taskstatus")
			{
				var statusvalue = getObj(fieldname[j]).value.replace(/^\s+/g, '').replace(/\s+$/g, '')
				var statuslabel = fieldlabel[j++]
			}
		}
	}
	if(statusvalue == "Planned")
	{
		var dateelements=splitDateVal(startdatevalue)

		var hourval=parseInt(timeval.substring(0,timeval.indexOf(":")))
		var minval=parseInt(timeval.substring(timeval.indexOf(":")+1,timeval.length))


		dd=dateelements[0]
		mm=dateelements[1]
		yyyy=dateelements[2]

		var chkdate=new Date()
		chkdate.setYear(yyyy)
		chkdate.setMonth(mm-1)
		chkdate.setDate(dd)
		chkdate.setMinutes(minval)
		chkdate.setHours(hourval)
		if(!comparestartdate(chkdate)) return false;
	}//end

	return true;
}

function clearId(fldName) {

	var currObj=getObj(fldName)

	currObj.value=""

}

function comparestartdate(chkdate) {
	var currdate = new Date();
	return compareDates(chkdate,alert_arr.START_DATE_TIME,currdate,alert_arr.DATE_SHOULDNOT_PAST,"GE");
}

function showCalc(fldName) {
	var currObj=getObj(fldName)
	openPopUp("calcWin",currObj,"/crm/Calc.do?currFld="+fldName,"Calc",170,220,"menubar=no,toolbar=no,location=no,status=no,scrollbars=no,resizable=yes")
}

function showLookUp(fldName,fldId,fldLabel,searchmodule,hostName,serverPort,username) {
	var currObj=getObj(fldName)

	//var fldValue=currObj.value.replace(/^\s+/g, '').replace(/\s+$/g, '')

	//need to pass the name of the system in which the server is running so that even when the search is invoked from another system, the url will remain the same

	openPopUp("lookUpWin",currObj,"/crm/Search.do?searchmodule="+searchmodule+"&fldName="+fldName+"&fldId="+fldId+"&fldLabel="+fldLabel+"&fldValue=&user="+username,"LookUp",500,400,"menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes")
}

function openPopUp(winInst,currObj,baseURL,winName,width,height,features) {
	var left=parseInt(findPosX(currObj))
	var top=parseInt(findPosY(currObj))

	if (window.navigator.appName!="Opera") top+=parseInt(currObj.offsetHeight)
	else top+=(parseInt(currObj.offsetHeight)*2)+10

	if (browser_ie)	{
		top+=window.screenTop-document.body.scrollTop
		left-=document.body.scrollLeft
		if (top+height+30>window.screen.height)
			top=findPosY(currObj)+window.screenTop-height-30 //30 is a constant to avoid positioning issue
		if (left+width>window.screen.width)
			left=findPosX(currObj)+window.screenLeft-width
	} else if (browser_nn4 || browser_nn6) {
		top+=(scrY-pgeY)
		left+=(scrX-pgeX)
		if (top+height+30>window.screen.height)
			top=findPosY(currObj)+(scrY-pgeY)-height-30
		if (left+width>window.screen.width)
			left=findPosX(currObj)+(scrX-pgeX)-width
	}

	features="width="+width+",height="+height+",top="+top+",left="+left+";"+features
	eval(winInst+'=window.open("'+baseURL+'","'+winName+'","'+features+'")')
}

var scrX=0,scrY=0,pgeX=0,pgeY=0;

if (browser_nn4 || browser_nn6) {
	document.addEventListener("click",popUpListener,true)
}

function popUpListener(ev) {
	if (browser_nn4 || browser_nn6) {
		scrX=ev.screenX
		scrY=ev.screenY
		pgeX=ev.pageX
		pgeY=ev.pageY
	}
}

function toggleSelect(state,relCheckName) {
	if (getObj(relCheckName)) {
		if (typeof(getObj(relCheckName).length)=="undefined") {
			getObj(relCheckName).checked=state
		} else {
			for (var i=0;i<getObj(relCheckName).length;i++)
				getObj(relCheckName)[i].checked=state
		}
	}
}

function toggleSelectAll(relCheckName,selectAllName) {
	if (typeof(getObj(relCheckName).length)=="undefined") {
		getObj(selectAllName).checked=getObj(relCheckName).checked
	} else {
		var atleastOneFalse=false;
		for (var i=0;i<getObj(relCheckName).length;i++) {
			if (getObj(relCheckName)[i].checked==false) {
				atleastOneFalse=true
				break;
			}
		}
		getObj(selectAllName).checked=!atleastOneFalse
	}
}
//added for show/hide 10July
function expandCont(bn)
{
	var leftTab = document.getElementById(bn);
	leftTab.style.display = (leftTab.style.display == "block")?"none":"block";
	img = document.getElementById("img_"+bn);
	img.src=(img.src.indexOf("images/toggle1.gif")!=-1)?"themes/images/toggle2.gif":"themes/images/toggle1.gif";
	set_cookie_gen(bn,leftTab.style.display)

}

function setExpandCollapse_gen()
{
	var x = leftpanelistarray.length;
	for (i = 0 ; i < x ; i++)
	{
		var listObj=getObj(leftpanelistarray[i])
		var tgImageObj=getObj("img_"+leftpanelistarray[i])
		var status = get_cookie_gen(leftpanelistarray[i])

		if (status == "block") {
			listObj.style.display="block";
			tgImageObj.src="themes/images/toggle2.gif";
		} else if(status == "none") {
			listObj.style.display="none";
			tgImageObj.src="themes/images/toggle1.gif";
		}
	}
}

function toggleDiv(id) {

	var listTableObj=getObj(id)

	if (listTableObj.style.display=="block")
	{
		listTableObj.style.display="none"
	}else{
		listTableObj.style.display="block"
	}
//set_cookie(id,listTableObj.style.display)
}

//Setting cookies
function set_cookie_gen ( name, value, exp_y, exp_m, exp_d, path, domain, secure )
{
	var cookie_string = name + "=" + escape ( value );

	if ( exp_y )
	{
		var expires = new Date ( exp_y, exp_m, exp_d );
		cookie_string += "; expires=" + expires.toGMTString();
	}

	if ( path )
		cookie_string += "; path=" + escape ( path );

	if ( domain )
		cookie_string += "; domain=" + escape ( domain );

	if ( secure )
		cookie_string += "; secure";

	document.cookie = cookie_string;
}

// Retrieving cookies
function get_cookie_gen ( cookie_name )
{
	var results = document.cookie.match ( cookie_name + '=(.*?)(;|$)' );

	if ( results )
		return ( unescape ( results[1] ) );
	else
		return null;
}

// Delete cookies
function delete_cookie_gen ( cookie_name )
{
	var cookie_date = new Date ( );  // current date & time
	cookie_date.setTime ( cookie_date.getTime() - 1 );
	document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}
//end added for show/hide 10July

/** This is Javascript Function which is used to toogle between
  * assigntype user and group/team select options while assigning owner to entity.
  */
function toggleAssignType(currType)
{
	if (currType=="U")
	{
		getObj("assign_user").style.display="block"
		getObj("assign_team").style.display="none"
	}
	else
	{
		getObj("assign_user").style.display="none"
		getObj("assign_team").style.display="block"
	}
}
//to display type of address for google map
function showLocateMapMenu()
{
	getObj("dropDownMenu").style.display="block"
	getObj("dropDownMenu").style.left=findPosX(getObj("locateMap"))
	getObj("dropDownMenu").style.top=findPosY(getObj("locateMap"))+getObj("locateMap").offsetHeight
}


function hideLocateMapMenu(ev)
{
	if (browser_ie)
		currElement=window.event.srcElement
	else if (browser_nn4 || browser_nn6)
		currElement=ev.target

	if (currElement.id!="locateMap")
		if (getObj("dropDownMenu").style.display=="block")
			getObj("dropDownMenu").style.display="none"
}
/*
* javascript function to display the div tag
* @param divId :: div tag ID
*/
function show(divId)
{
	if(getObj(divId))
	{
		var id = document.getElementById(divId);

		id.style.display = 'inline';
	}
}

/*
* javascript function to display the div tag
* @param divId :: div tag ID
*/
function showBlock(divId)
{
	var id = document.getElementById(divId);
	id.style.display = 'block';
}


/*
* javascript function to hide the div tag
* @param divId :: div tag ID
*/
function hide(divId)
{

	var id = document.getElementById(divId);

	id.style.display = 'none';

}
function fnhide(divId)
{

	var id = document.getElementById(divId);

	id.style.display = 'none';
}

function fnLoadValues(obj1,obj2,SelTab,unSelTab,moduletype,module){



	var oform = document.forms['EditView'];
	oform.action.value='Save';
	//global variable to check the validation calling function to avoid validating when tab change
	gValidationCall = 'tabchange';

	/*var tabName1 = document.getElementById(obj1);
	var tabName2 = document.getElementById(obj2);
	var tagName1 = document.getElementById(SelTab);
	var tagName2 = document.getElementById(unSelTab);
	if(tabName1.className == "dvtUnSelectedCell")
		tabName1.className = "dvtSelectedCell";
	if(tabName2.className == "dvtSelectedCell")
		tabName2.className = "dvtUnSelectedCell";

	tagName1.style.display='block';
	tagName2.style.display='none';*/
	gValidationCall = 'tabchange';

	// if((moduletype == 'inventory' && validateInventory(module)) ||(moduletype == 'normal') && formValidate())
	// if(formValidate())
	// {
	var tabName1 = document.getElementById(obj1);

	var tabName2 = document.getElementById(obj2);

	var tagName1 = document.getElementById(SelTab);

	var tagName2 = document.getElementById(unSelTab);

	if(tabName1.className == "dvtUnSelectedCell")

		tabName1.className = "dvtSelectedCell";

	if(tabName2.className == "dvtSelectedCell")

		tabName2.className = "dvtUnSelectedCell";
	tagName1.style.display='block';

	tagName2.style.display='none';
	// }

	gValidationCall = '';
}

function fnCopy(source,design){

	document.getElementById(source).value=document.getElementById(design).value;

	document.getElementById(source).disabled=true;

}

function fnClear(source){

	document.getElementById(source).value=" ";

	document.getElementById(source).disabled=false;

}

function fnCpy(){

	var tagName=document.getElementById("cpy");

	if(tagName.checked==true){
		fnCopy("shipaddress","address");

		fnCopy("shippobox","pobox");

		fnCopy("shipcity","city");

		fnCopy("shipcode","code");

		fnCopy("shipstate","state");

		fnCopy("shipcountry","country");

	}

	else{

		fnClear("shipaddress");

		fnClear("shippobox");

		fnClear("shipcity");

		fnClear("shipcode");

		fnClear("shipstate");

		fnClear("shipcountry");

	}

}
function fnDown(obj){
	var tagName = document.getElementById(obj);
	var tabName = document.getElementById("one");
	if(tagName.style.display == 'none'){
		tagName.style.display = 'block';
		tabName.style.display = 'block';
	}
	else{
		tabName.style.display = 'none';
		tagName.style.display = 'none';
	}
}

/*
* javascript function to add field rows
* @param option_values :: List of Field names
*/
var count = 0;
var rowCnt = 1;
function fnAddSrch(){

	var tableName = document.getElementById('adSrc');

	var prev = tableName.rows.length;

	var count = prev;

	var row = tableName.insertRow(prev);

	if(count%2)

		row.className = "dvtCellLabel";

	else

		row.className = "dvtCellInfo";

	var fieldObject = document.getElementById("Fields0");
	var conditionObject = document.getElementById("Condition0");
	var searchValueObject = document.getElementById("Srch_value0");

	var columnone = document.createElement('td');
	var colone = fieldObject.cloneNode(true);
	colone.setAttribute('id','Fields'+count);
	colone.setAttribute('name','Fields'+count);
	colone.setAttribute('value','');
	colone.onchange = function() {
		updatefOptions(colone, 'Condition'+count);
	}
	columnone.appendChild(colone);
	row.appendChild(columnone);

	var columntwo = document.createElement('td');
	var coltwo = conditionObject.cloneNode(true);
	coltwo.setAttribute('id','Condition'+count);
	coltwo.setAttribute('name','Condition'+count);
	coltwo.setAttribute('value','');
	columntwo.appendChild(coltwo);
	row.appendChild(columntwo);

	var columnthree = document.createElement('td');
	var colthree = searchValueObject.cloneNode(true);
	colthree.setAttribute('id','Srch_value'+count);
	colthree.setAttribute('name','Srch_value'+count);
	colthree.setAttribute('value','');
	colthree.value = '';
	columnthree.appendChild(colthree);
	row.appendChild(columnthree);

	updatefOptions(colone, 'Condition'+count);
}

function totalnoofrows()
{
	var tableName = document.getElementById('adSrc');
	document.basicSearch.search_cnt.value = tableName.rows.length;
}

/*
* javascript function to delete field rows in advance search
* @param void :: void
*/
function delRow()
{

	var tableName = document.getElementById('adSrc');

	var prev = tableName.rows.length;

	if(prev > 1)

		document.getElementById('adSrc').deleteRow(prev-1);

}

function fnVis(obj){

	var profTag = document.getElementById("prof");

	var moreTag = document.getElementById("more");

	var addrTag = document.getElementById("addr");


	if(obj == 'prof'){

		document.getElementById('mnuTab').style.display = 'block';

		document.getElementById('mnuTab1').style.display = 'none';

		document.getElementById('mnuTab2').style.display = 'none';

		profTag.className = 'dvtSelectedCell';

		moreTag.className = 'dvtUnSelectedCell';

		addrTag.className = 'dvtUnSelectedCell';

	}


	else if(obj == 'more'){

		document.getElementById('mnuTab1').style.display = 'block';

		document.getElementById('mnuTab').style.display = 'none';

		document.getElementById('mnuTab2').style.display = 'none';

		moreTag.className = 'dvtSelectedCell';

		profTag.className = 'dvtUnSelectedCell';

		addrTag.className = 'dvtUnSelectedCell';

	}


	else if(obj == 'addr'){

		document.getElementById('mnuTab2').style.display = 'block';

		document.getElementById('mnuTab').style.display = 'none';

		document.getElementById('mnuTab1').style.display = 'none';

		addrTag.className = 'dvtSelectedCell';

		profTag.className = 'dvtUnSelectedCell';

		moreTag.className = 'dvtUnSelectedCell';

	}

}

function fnvsh(obj,Lay){
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	tagName.style.left= leftSide + 175 + 'px';
	tagName.style.top= topSide + 'px';
	tagName.style.visibility = 'visible';
}

function fnvshobj(obj,Lay){
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	var widthM = maxW.substring(0,maxW.length-2);
	if(Lay == 'editdiv')
	{
		leftSide = leftSide - 225;
		topSide = topSide - 125;
	}else if(Lay == 'transferdiv')
	{
		leftSide = leftSide - 10;
		topSide = topSide;
	}
	var IE = document.all?true:false;
	if(IE)
	{
		if($("repposition1"))
		{
			if(topSide > 1200)
			{
				topSide = topSide-250;
			}
		}
	}

	var getVal = eval(leftSide) + eval(widthM);
	if(getVal  > document.body.clientWidth ){
		leftSide = eval(leftSide) - eval(widthM);
		tagName.style.left = leftSide + 34 + 'px';
	}
	else
		tagName.style.left= leftSide + 'px';
	tagName.style.top= topSide + 'px';
	tagName.style.display = 'block';
	tagName.style.visibility = "visible";
}

function posLay(obj,Lay){
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	var widthM = maxW.substring(0,maxW.length-2);
	var getVal = eval(leftSide) + eval(widthM);
	if(getVal  > document.body.clientWidth ){
		leftSide = eval(leftSide) - eval(widthM);
		tagName.style.left = leftSide + 'px';
	}
	else
		tagName.style.left= leftSide + 'px';
	tagName.style.top= topSide + 'px';
}

function fninvsh(Lay){
	var tagName = document.getElementById(Lay);
	tagName.style.visibility = 'hidden';
	tagName.style.display = 'none';
}

function fnvshNrm(Lay){
	var tagName = document.getElementById(Lay);
	tagName.style.visibility = 'visible';
	tagName.style.display = 'block';
}

function cancelForm(frm)
{
	window.history.back();
}

function trim(str)
{
	var s = str.replace(/\s+$/,'');
	s = s.replace(/^\s+/,'');
	return s;
}

function clear_form(form)
{
	for (j = 0; j < form.elements.length; j++)
	{
		if (form.elements[j].type == 'text' || form.elements[j].type == 'select-one')
		{
			form.elements[j].value = '';
		}
	}
}

function ActivateCheckBox()
{
	var map = document.getElementById("saved_map_checkbox");
	var source = document.getElementById("saved_source");

	if(map.checked == true)
	{
		source.disabled = false;
	}
	else
	{
		source.disabled = true;
	}
}

//wipe for Convert Lead

function fnSlide2(obj,inner)
{
	var buff = document.getElementById(obj).height;
	closeLimit = buff.substring(0,buff.length);
	menu_max = eval(closeLimit);
	var tagName = document.getElementById(inner);
	document.getElementById(obj).style.height=0 + "px";
	menu_i=0;
	if (tagName.style.display == 'none')
		fnexpanLay2(obj,inner);
	else
		fncloseLay2(obj,inner);
}

function fnexpanLay2(obj,inner)
{
	// document.getElementById(obj).style.display = 'run-in';
	var setText = eval(closeLimit) - 1;
	if (menu_i<=eval(closeLimit))
	{
		if (menu_i>setText){
			document.getElementById(inner).style.display='block';
		}
		document.getElementById(obj).style.height=menu_i + "px";
		setTimeout(function() {
			fnexpanLay2(obj,inner);
		},5);
		menu_i=menu_i+5;
	}
}

function fncloseLay2(obj,inner)
{
	if (menu_max >= eval(openLimit))
	{
		if (menu_max<eval(closeLimit)){
			document.getElementById(inner).style.display='none';
		}
		document.getElementById(obj).style.height=menu_max +"px";
		setTimeout(function() {
			fncloseLay2(obj,inner);
		}, 5);
		menu_max = menu_max -5;
	}
}

function addOnloadEvent(fnc){
	if ( typeof window.addEventListener != "undefined" )
		window.addEventListener( "load", fnc, false );
	else if ( typeof window.attachEvent != "undefined" ) {
		window.attachEvent( "onload", fnc );
	}
	else {
		if ( window.onload != null ) {
			var oldOnload = window.onload;
			window.onload = function ( e ) {
				oldOnload( e );
				window[fnc]();
			};
		}
		else
			window.onload = fnc;
	}
}
function InternalMailer(record_id,field_id,field_name,par_module,type) {
	var url;
	switch(type) {
		case 'record_id':
			url = 'index.php?module=Emails&action=EmailsAjax&internal_mailer=true&type='+type+'&field_id='+field_id+'&rec_id='+record_id+'&fieldname='+field_name+'&file=EditView&par_module='+par_module;//query string field_id added for listview-compose email issue
			break;
		case 'email_addy':
			url = 'index.php?module=Emails&action=EmailsAjax&internal_mailer=true&type='+type+'&email_addy='+record_id+'&file=EditView';
			break;

	}

	var opts = "menubar=no,toolbar=no,location=no,status=no,resizable=yes,scrollbars=yes";
	openPopUp('xComposeEmail',this,url,'createemailWin',830,662,opts);
}

function fnHide_Event(obj){
	document.getElementById(obj).style.visibility = 'hidden';
}
function ReplyCompose(id,mode)
{
	url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&record='+id+'&reply=true';

	openPopUp('xComposeEmail',this,url,'createemailWin',820,689,'menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes');
}
function OpenCompose(id,mode)
{
	switch(mode)
	{
		case 'edit':
			url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&record='+id;
			break;
		case 'create':
			url = 'index.php?module=Emails&action=EmailsAjax&file=EditView';
			break;
		case 'forward':
			url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&record='+id+'&forward=true';
			break;
		case 'Invoice':
			url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&attachment='+mode+'_'+id+'.pdf';
			break;
		case 'PurchaseOrder':
			url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&attachment='+mode+'_'+id+'.pdf';
			break;
		case 'SalesOrder':
			url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&attachment='+mode+'_'+id+'.pdf';
			break;
		case 'Quote':
			url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&attachment='+mode+'_'+id+'.pdf';
			break;
		case 'Documents':
			url = 'index.php?module=Emails&action=EmailsAjax&file=EditView&attachment='+id+'';
			break;
		case 'print':
			url = 'index.php?module=Emails&action=EmailsAjax&file=PrintEmail&record='+id+'&print=true';
	}
	openPopUp('xComposeEmail',this,url,'createemailWin',820,689,'menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes');
}

//Function added for Mass select in Popup - Philip
function SelectAll(mod,parmod)
{
	if(document.selectall.selected_id != undefined)
	{
		x = document.selectall.selected_id.length;
		var y=0;
		if(parmod != 'Calendar')
		{
			var module = window.opener.document.getElementById('RLreturn_module').value
			var entity_id = window.opener.document.getElementById('RLparent_id').value
			var parenttab = window.opener.document.getElementById('parenttab').value
		}
		idstring = "";
		namestr = "";

		if ( x == undefined)
		{

			if (document.selectall.selected_id.checked)
			{
				idstring = document.selectall.selected_id.value;
				if(parmod == 'Calendar')
					namestr = document.getElementById('calendarCont'+idstring).innerHTML;
				y=1;
			}
			else
			{
				alert(alert_arr.SELECT);
				return false;
			}
		}
		else
		{
			y=0;
			for(i = 0; i < x ; i++)
			{
				if(document.selectall.selected_id[i].checked)
				{
					idstring = document.selectall.selected_id[i].value +";"+idstring;
					if(parmod == 'Calendar')
					{
						idval = document.selectall.selected_id[i].value;
						namestr = document.getElementById('calendarCont'+idval).innerHTML+"\n"+namestr;
					}
					y=y+1;
				}
			}
		}
		if (y != 0)
		{
			document.selectall.idlist.value=idstring;
		}
		else
		{
			alert(alert_arr.SELECT);
			return false;
		}
		if(confirm(alert_arr.ADD_CONFIRMATION+y+alert_arr.RECORDS))
		{
			if(parmod == 'Calendar')
			{
				//this blcok has been modified to provide delete option for contact in Calendar
				idval = window.opener.document.EditView.contactidlist.value;
				if(idval != '')
				{
					var avalIds = new Array();
					avalIds = idstring.split(';');

					var selectedIds = new Array();
					selectedIds = idval.split(';');

					for(i=0; i < (avalIds.length-1); i++)
					{
						var rowFound=false;
						for(k=0; k < selectedIds.length; k++)
						{
							if (selectedIds[k]==avalIds[i])
							{
								rowFound=true;
								break;
							}

						}
						if(rowFound != true)
						{
							idval = idval+';'+avalIds[i];
							window.opener.document.EditView.contactidlist.value = idval;
							var str=document.getElementById('calendarCont'+avalIds[i]).innerHTML;
							window.opener.addOption(avalIds[i],str);
						}
					}
				}
				else
				{
					window.opener.document.EditView.contactidlist.value = idstring;
					var temp = new Array();
					temp = namestr.split('\n');

					var tempids = new Array();
					tempids = idstring.split(';');

					for(k=0; k < temp.length; k++)
					{
						window.opener.addOption(tempids[k],temp[k]);
					}
				}
			//end
			}
			else
			{
				opener.document.location.href="index.php?module="+module+"&parentid="+entity_id+"&action=updateRelations&destination_module="+mod+"&idlist="+idstring+"&parenttab="+parenttab;
			}
			self.close();
		}
		else
		{
			return false;
		}
	}
}
function ShowEmail(id)
{
	url = 'index.php?module=Emails&action=EmailsAjax&file=DetailView&record='+id;
	openPopUp('xComposeEmail',this,url,'createemailWin',820,695,'menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes');
}

var bSaf = (navigator.userAgent.indexOf('Safari') != -1);
var bOpera = (navigator.userAgent.indexOf('Opera') != -1);
var bMoz = (navigator.appName == 'Netscape');
function execJS(node) {
	var st = node.getElementsByTagName('SCRIPT');
	var strExec;
	for(var i=0;i<st.length; i++) {
		if (bSaf) {
			strExec = st[i].innerHTML;
		}
		else if (bOpera) {
			strExec = st[i].text;
		}
		else if (bMoz) {
			strExec = st[i].textContent;
		}
		else {
			strExec = st[i].text;
		}
		try {
			eval(strExec);
		} catch(e) {
			alert(e);
		}
	}
}

//Function added for getting the Tab Selected Values (Standard/Advanced Filters) for Custom View - Ahmed
function fnLoadCvValues(obj1,obj2,SelTab,unSelTab){

	var tabName1 = document.getElementById(obj1);

	var tabName2 = document.getElementById(obj2);

	var tagName1 = document.getElementById(SelTab);

	var tagName2 = document.getElementById(unSelTab);

	if(tabName1.className == "dvtUnSelectedCell")

		tabName1.className = "dvtSelectedCell";

	if(tabName2.className == "dvtSelectedCell")

		tabName2.className = "dvtUnSelectedCell";
	tagName1.style.display='block';

	tagName2.style.display='none';

}


// Drop Dwon Menu


function fnDropDown(obj,Lay){
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	var widthM = maxW.substring(0,maxW.length-2);
	var getVal = eval(leftSide) + eval(widthM);
	if(getVal  > document.body.clientWidth ){
		leftSide = eval(leftSide) - eval(widthM);
		tagName.style.left = leftSide + 34 + 'px';
	}
	else
		tagName.style.left= leftSide + 'px';
		tagName.style.top= topSide + 28 +'px';
		tagName.style.display = 'block';
}

function fnShowDrop(obj){
	document.getElementById(obj).style.display = 'block';
}

function fnHideDrop(obj){
	document.getElementById(obj).style.display = 'none';
}

function getCalendarPopup(imageid,fieldid,dateformat)
{
	Calendar.setup ({
		inputField : fieldid,
		ifFormat : dateformat,
		showsTime : false,
		button : imageid,
		singleClick : true,
		step : 1
	});
}

//Added to check duplicate account creation

function AjaxDuplicateValidate(module,fieldname,oform)
{
	var fieldvalue = encodeURIComponent(trim(getObj(fieldname).value));
	var recordid = getObj('record').value;
	if(fieldvalue == '')
	{
		alert(alert_arr.ACCOUNTNAME_CANNOT_EMPTY);
		return false;
	}
	VtigerJS_DialogBox.block();

	var url = "module="+module+"&action="+module+"Ajax&file=Save&"+fieldname+"="+fieldvalue+"&dup_check=true&record="+recordid;
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody:url,
			onComplete: function(response) {
				var str = response.responseText
				if(str.indexOf('SUCCESS') > -1)
				{
					oform.submit();
				}else
				{
					VtigerJS_DialogBox.unblock();
					alert(str);
					return false;
				}
			}
		}
		);
}

/**to get SelectContacts Popup
check->to check select options enable or disable
*type->to differentiate from task
*frmName->form name*/

function selectContact(check,type,frmName)
{
	var record = document.getElementsByName("record")[0].value;
	if($("single_accountid"))
	{
		var potential_id = '';
		if($("potential_id"))
			potential_id = frmName.potential_id.value;
		account_id = frmName.account_id.value;
		if(potential_id != '')
		{
			record_id = potential_id;
			module_string = "&parent_module=Potentials";
		}
		else
		{
			record_id = account_id;
			module_string = "&parent_module=Accounts";
		}
		if(record_id != '')
			window.open("index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form=EditView"+module_string+"&relmod_id="+record_id,"test","width=640,height=602,resizable=0,scrollbars=0");
		else
			window.open("index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form=EditView","test","width=640,height=602,resizable=0,scrollbars=0");
	}
	else if(($("parentid")) && type != 'task')
	{
		if(getObj("parent_type")){
			rel_parent_module = frmName.parent_type.value;
			record_id = frmName.parent_id.value;
			module = rel_parent_module.split("&");
			if(record_id != '' && module[0] == "Leads")
			{
				alert(alert_arr.CANT_SELECT_CONTACTS);
			}
			else
			{
				if(check == 'true')
					search_string = "&return_module=Calendar&select=enable&popuptype=detailview&form_submit=false";
				else
					search_string="&popuptype=specific";
				if(record_id != '')
					window.open("index.php?module=Contacts&action=Popup&html=Popup_picker&form=EditView"+search_string+"&relmod_id="+record_id+"&parent_module="+module[0],"test","width=640,height=602,resizable=0,scrollbars=0");
				else
					window.open("index.php?module=Contacts&action=Popup&html=Popup_picker&form=EditView"+search_string,"test","width=640,height=602,resizable=0,scrollbars=0");

			}
		}else{
			window.open("index.php?module=Contacts&action=Popup&html=Popup_picker&return_module=Calendar&select=enable&popuptype=detailview&form=EditView&form_submit=false","test","width=640,height=602,resizable=0,scrollbars=0");
		}
	}
	else if(($("contact_name")) && type == 'task')
	{
		var formName = frmName.name;
		var task_recordid = '';
		if(formName == 'EditView')
		{
			if($("parent_type"))
			{
				task_parent_module = frmName.parent_type.value;
				task_recordid = frmName.parent_id.value;
				task_module = task_parent_module.split("&");
				popuptype="&popuptype=specific";
			}
		}
		else
		{
			if($("task_parent_type"))
			{
				task_parent_module = frmName.task_parent_type.value;
				task_recordid = frmName.task_parent_id.value;
				task_module = task_parent_module.split("&");
				popuptype="&popuptype=toDospecific";
			}
		}
		if(task_recordid != '' && task_module[0] == "Leads" )
		{
			alert(alert_arr.CANT_SELECT_CONTACTS);
		}
		else
		{
			if(task_recordid != '')
				window.open("index.php?module=Contacts&action=Popup&html=Popup_picker"+popuptype+"&form="+formName+"&task_relmod_id="+task_recordid+"&task_parent_module="+task_module[0],"test","width=640,height=602,resizable=0,scrollbars=0");
			else
				window.open("index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form="+formName,"test","width=640,height=602,resizable=0,scrollbars=0");
		}

	}
	else
	{
		window.open("index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form=EditView&recordid="+record,"test","width=640,height=602,resizable=0,scrollbars=0");
	}
}
//to get Select Potential Popup
function selectPotential()
{
	// To support both B2B and B2C model
	var record_id = '';
	var parent_module = '';
	var acc_element = document.EditView.account_id;
	var cnt_element = document.EditView.contact_id;
	if (acc_element != null) {
		record_id= acc_element.value;
		parent_module = 'Accounts';
	} else if (cnt_element != null) {
		record_id= cnt_element.value;
		parent_module = 'Contacts';
	}
	if(record_id != '')
		window.open("index.php?module=Potentials&action=Popup&html=Popup_picker&popuptype=specific_potential_account_address&form=EditView&relmod_id="+record_id+"&parent_module="+parent_module,"test","width=640,height=602,resizable=0,scrollbars=0");
	else
		window.open("index.php?module=Potentials&action=Popup&html=Popup_picker&popuptype=specific_potential_account_address&form=EditView","test","width=640,height=602,resizable=0,scrollbars=0");
}
//to select Quote Popup
function selectQuote()
{
	// To support both B2B and B2C model
	var record_id = '';
	var parent_module = '';
	var acc_element = document.EditView.account_id;
	var cnt_element = document.EditView.contact_id;
	if (acc_element != null) {
		record_id= acc_element.value;
		parent_module = 'Accounts';
	} else if (cnt_element != null) {
		record_id= cnt_element.value;
		parent_module = 'Contacts';
	}
	if(record_id != '')
		window.open("index.php?module=Quotes&action=Popup&html=Popup_picker&popuptype=specific&form=EditView&relmod_id="+record_id+"&parent_module="+parent_module,"test","width=640,height=602,resizable=0,scrollbars=0");

	else
		window.open("index.php?module=Quotes&action=Popup&html=Popup_picker&popuptype=specific&form=EditView","test","width=640,height=602,resizable=0,scrollbars=0");
}
//to get select SalesOrder Popup
function selectSalesOrder()
{
	// To support both B2B and B2C model
	var record_id = '';
	var parent_module = '';
	var acc_element = document.EditView.account_id;
	var cnt_element = document.EditView.contact_id;
	if (acc_element != null) {
		record_id= acc_element.value;
		parent_module = 'Accounts';
	} else if (cnt_element != null) {
		record_id= cnt_element.value;
		parent_module = 'Contacts';
	}
	if(record_id != '')
		window.open("index.php?module=SalesOrder&action=Popup&html=Popup_picker&popuptype=specific&form=EditView&relmod_id="+record_id+"&parent_module="+parent_module,"test","width=640,height=602,resizable=0,scrollbars=0");
	else
		window.open("index.php?module=SalesOrder&action=Popup&html=Popup_picker&popuptype=specific&form=EditView","test","width=640,height=602,resizable=0,scrollbars=0");
}

function checkEmailid(parent_module,emailid,secondaryemail)
{
	var check = true;
	if(emailid == '' && secondaryemail == '')
	{
		alert(alert_arr.LBL_THIS+parent_module+alert_arr.DOESNOT_HAVE_MAILIDS);
		check=false;
	}
	return check;
}

function calQCduedatetime()
{
	var datefmt = document.QcEditView.dateFormat.value;
	var type = document.QcEditView.activitytype.value;
	var dateval1=getObj('date_start').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	var dateelements1=splitDateVal(dateval1);
	dd1=parseInt(dateelements1[0],10);
	mm1=dateelements1[1];
	yyyy1=dateelements1[2];
	var date1=new Date();
	date1.setYear(yyyy1);
	date1.setMonth(mm1-1,dd1+1);
	var yy = date1.getFullYear();
	var mm = date1.getMonth() + 1;
	var dd = date1.getDate();
	var date = document.QcEditView.date_start.value;
	var starttime = document.QcEditView.time_start.value;
	if (!timeValidate('time_start',' Start Date & Time','OTH'))
		return false;
	var timearr = starttime.split(":");
	var hour = parseInt(timearr[0],10);
	var min = parseInt(timearr[1],10);
	dd = _2digit(dd);
	mm = _2digit(mm);
	var tempdate = yy+'-'+mm+'-'+dd;
	if(datefmt == '%d-%m-%Y')
		var tempdate = dd+'-'+mm+'-'+yy;
	else if(datefmt == '%m-%d-%Y')
		var tempdate = mm+'-'+dd+'-'+yy;
	if(type == 'Meeting')
	{
		hour = hour + 1;
		if(hour == 24)
		{
			hour = 0;
			date =  tempdate;
		}
		hour = _2digit(hour);
		min = _2digit(min);
		document.QcEditView.due_date.value = date;
		document.QcEditView.time_end.value = hour+':'+min;
	}
	if(type == 'Call')
	{
		if(min >= 55)
		{
			min = min%55;
			hour = hour + 1;
		}else min = min + 5;
		if(hour == 24)
		{
			hour = 0;
			date =  tempdate;
		}
		hour = _2digit(hour);
		min = _2digit(min);
		document.QcEditView.due_date.value = date;
		document.QcEditView.time_end.value = hour+':'+min;
	}

}

function _2digit( no ){
	if(no < 10) return "0" + no;
	else return "" + no;
}

function confirmdelete(url)
{
	if(confirm(alert_arr.ARE_YOU_SURE))
	{
		document.location.href=url;
	}
}

//function modified to apply the patch ref : Ticket #4065
function valid(c,type)
{
	if(type == 'name')
	{
		return (((c >= 'a') && (c <= 'z')) ||((c >= 'A') && (c <= 'Z')) ||((c >= '0') && (c <= '9')) || (c == '.') || (c == '_') || (c == '-') || (c == '@') );
	}
	else if(type == 'namespace')
	{
		return (((c >= 'a') && (c <= 'z')) ||((c >= 'A') && (c <= 'Z')) ||((c >= '0') && (c <= '9')) || (c == '.')||(c==' ') || (c == '_') || (c == '-') );
	}
}
//end

function CharValidation(s,type)
{
	for (var i = 0; i < s.length; i++)
	{
		if (!valid(s.charAt(i),type))
		{
			return false;
		}
	}
	return true;
}


/** Check Upload file is in specified format(extension).
  * @param fldName -- name of the file field
  * @param filter -- List of file extensions to allow. each extension must be seperated with a | sybmol.
  * Example: upload_filter("imagename","Image", "jpg|gif|bmp|png")
  * @returns true -- if the extension is IN  specified extension.
  * @returns false -- if the extension is NOT IN specified extension.
  *
  * NOTE: If this field is mandatory,  please call emptyCheck() function before calling this function.
 */

function upload_filter(fldName, filter)
{
	var currObj=getObj(fldName)
	if(currObj.value !="")
	{
		var file=currObj.value;
		var type=file.toLowerCase().split(".");
		var valid_extn=filter.toLowerCase().split("|");

		if(valid_extn.indexOf(type[type.length-1]) == -1)
		{
			alert(alert_arr.PLS_SELECT_VALID_FILE+valid_extn)
			try {
				currObj.focus()
			} catch(error) {
			// Fix for IE: If element or its wrapper around it is hidden, setting focus will fail
			// So using the try { } catch(error) { }
			}
			return false;
		}
	}
	return true

}

function validateUrl(name)
{
	var Url = getObj(name);
	var wProtocol;

	var oRegex = new Object();
	oRegex.UriProtocol = new RegExp('');
	oRegex.UriProtocol.compile( '^(((http):\/\/)|mailto:)', 'gi' );
	oRegex.UrlOnChangeProtocol = new RegExp('') ;
	oRegex.UrlOnChangeProtocol.compile( '^(http)://(?=.)', 'gi' );

	wUrl = Url.value;
	wProtocol=oRegex.UrlOnChangeProtocol.exec( wUrl ) ;
	if ( wProtocol )
	{
		wUrl = wUrl.substr( wProtocol[0].length );
		Url.value = wUrl;
	}
}

function LTrim( value )
{

	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");

}

function selectedRecords(module,category)
{
	var allselectedboxes = document.getElementById("allselectedboxes");
	var idstring  =  (allselectedboxes == null)? '' : allselectedboxes.value;
	var viewid = getviewId();
	var url = '&viewname='+viewid;
	if(document.getElementById('excludedRecords') != null && typeof(document.getElementById('excludedRecords')) != 'undefined') {
		var excludedRecords = $('excludedRecords').value;
		var searchurl = document.getElementById('search_url').value;
		url = url+searchurl+'&excludedRecords='+excludedRecords;
	}
	if(idstring != '')
		window.location.href="index.php?module="+module+"&action=ExportRecords&parenttab="+category+"&idstring="+idstring+url;
	else
		window.location.href="index.php?module="+module+"&action=ExportRecords&parenttab="+category;
	return false;
}

function record_export(module,category,exform,idstring)
{
	var searchType = document.getElementsByName('search_type');
	var exportData = document.getElementsByName('export_data');
	for(i=0;i<2;i++){
		if(searchType[i].checked == true)
			var sel_type = searchType[i].value;
	}
	for(i=0;i<3;i++){
		if(exportData[i].checked == true)
			var exp_type = exportData[i].value;
	}
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: "module="+module+"&action=ExportAjax&export_record=true&search_type="+sel_type+"&export_data="+exp_type+"&idstring="+idstring,
			onComplete: function(response) {
				if(response.responseText == 'NOT_SEARCH_WITHSEARCH_ALL')
				{
					$('not_search').style.display = 'block';
					$('not_search').innerHTML="<font color='red'><b>"+alert_arr.LBL_NOTSEARCH_WITHSEARCH_ALL+" "+module+"</b></font>";
					setTimeout(hideErrorMsg1,6000);

					exform.submit();
				}
				else if(response.responseText == 'NOT_SEARCH_WITHSEARCH_CURRENTPAGE')
				{
					$('not_search').style.display = 'block';
					$('not_search').innerHTML="<font color='red'><b>"+alert_arr.LBL_NOTSEARCH_WITHSEARCH_CURRENTPAGE+" "+module+"</b></font>";
					setTimeout(hideErrorMsg1,7000);

					exform.submit();
				}
				else if(response.responseText == 'NO_DATA_SELECTED')
				{
					$('not_search').style.display = 'block';
					$('not_search').innerHTML="<font color='red'><b>"+alert_arr.LBL_NO_DATA_SELECTED+"</b></font>";
					setTimeout(hideErrorMsg1,3000);
				}
				else if(response.responseText == 'SEARCH_WITHOUTSEARCH_ALL')
				{
					if(confirm(alert_arr.LBL_SEARCH_WITHOUTSEARCH_ALL))
					{
						exform.submit();
					}
				}
				else if(response.responseText == 'SEARCH_WITHOUTSEARCH_CURRENTPAGE')
				{
					if(confirm(alert_arr.LBL_SEARCH_WITHOUTSEARCH_CURRENTPAGE))
					{
						exform.submit();
					}
				}
				else
				{
					exform.submit();
				}
			}
		}
		);

}


function hideErrorMsg1()
{
	$('not_search').style.display = 'none';
}

// Replace the % sign with %25 to make sure the AJAX url is going wel.
function escapeAll(tagValue)
{
	//return escape(tagValue.replace(/%/g, '%25'));
	if(default_charset.toLowerCase() == 'utf-8')
		return encodeURIComponent(tagValue.replace(/%/g, '%25'));
	else
		return escape(tagValue.replace(/%/g, '%25'));
}

function removeHTMLFormatting(str) {
	str = str.replace(/<([^<>]*)>/g, " ");
	str = str.replace(/&nbsp;/g, " ");
	return str;
}
function get_converted_html(str)
{
	var temp = str.toLowerCase();
	if(temp.indexOf('<') != '-1' || temp.indexOf('>') != '-1')
	{
		str = str.replace(/</g,'&lt;');
		str = str.replace(/>/g,'&gt;');
	}
	if( temp.match(/(script).*(\/script)/))
	{
		str = str.replace(/&/g,'&amp;');
	}
	else if(temp.indexOf('&') != '-1')
	{
		str = str.replace(/&/g,'&amp;');
	}
	return str;
}
//To select the select all check box(if all the items are selected) when the form loads.
function default_togglestate(obj_id,elementId)
{
	var all_state=true;
	var groupElements = document.getElementsByName(obj_id);
	for (var i=0;i<groupElements.length;i++) {
		var state=groupElements[i].checked;
		if (state == false)
		{
			all_state=false;
			break;
		}
	}
	if(typeof elementId=='undefined'){
		elementId = 'selectall';
	}
	if(getObj(elementId)) {
		getObj(elementId).checked=all_state;
	}
}

//for select  multiple check box in multiple pages for Campaigns related list:

function rel_check_object(sel_id,module)
{
	var selected;
	var select_global = new Array();
	var cookie_val = get_cookie(module+"_all");
	if(cookie_val == null)
		selected = sel_id.value+";";
	else
		selected = trim(cookie_val);
	select_global = selected.split(";");
	var box_value = sel_id.checked;
	var id = sel_id.value;
	var duplicate = select_global.indexOf(id);
	var size = select_global.length-1;
	var result = "";
	var currentModule = $('return_module').value;
	var excluded = $(currentModule+'_'+module+'_excludedRecords').value;
	if(box_value == true)
	{
		if($(currentModule+'_'+module+'_selectallActivate').value == 'true') {
			$(currentModule+'_'+module+'_excludedRecords').value = excluded.replace(excluded.match(id+";"),'');
		} else {
			if(duplicate == "-1")
			{
				select_global[size]=id;
			}

			size = select_global.length-1;
			var i=0;
			for(i=0;i<=size;i++) {
				if(trim(select_global[i])!='')
					result = select_global[i]+";"+result;
			}
		}
		rel_default_togglestate(module);
	}
	else
	{
		if($(currentModule+'_'+module+'_selectallActivate').value == 'true'){
			$(currentModule+'_'+module+'_excludedRecords').value= id+";"+excluded;
		}
		if(duplicate != "-1")

			select_global.splice(duplicate,1)

		size=select_global.length-1;
		var i=0;
		for(i=size;i>=0;i--) {
			if(trim(select_global[i])!='')
				result=select_global[i]+";"+result;
		}
		getObj(module+"_selectall").checked=false;

	}
	set_cookie(module+"_all",result);
}

//Function to select all the items in the current page for Campaigns related list:.
function rel_toggleSelect(state,relCheckName,module) {
	var obj = document.getElementsByName(relCheckName);
	if (obj) {
		for (var i=0;i<obj.length;i++) {
			obj[i].checked = state;
			rel_check_object(obj[i],module);
		}
	}
	var current_module = $('return_module').value;
	if(current_module == 'Campaigns') {
		if(state == true) {
			var count = $(current_module+'_'+module+'_numOfRows').value;
			if(count == '')	{
				getNoOfRelatedRows(current_module,module);
			}
			if(parseInt($('maxrecords').value) < parseInt(count)) {
				$(current_module+'_'+module+'_linkForSelectAll').show();
			}
		} else {
			if($(current_module+'_'+module+'_selectallActivate').value == 'true'){
				$(current_module+'_'+module+'_linkForSelectAll').show();
			} else {
				$(current_module+'_'+module+'_linkForSelectAll').hide();
			}
		}
	}
}
//To select the select all check box(if all the items are selected) when the form loads for Campaigns related list:.
function rel_default_togglestate(module)
{
	var all_state=true;
	var currentModule = $('return_module').value;
	if(currentModule == 'Campaigns'){
		var groupElements = document.getElementsByName(currentModule+'_'+module+"_selected_id");
	} else {
		var groupElements = document.getElementsByName(module+"_selected_id");
	}
	if(typeof(groupElements) == 'undefined') return;

	for (var i=0;i<groupElements.length;i++) {
		var state=groupElements[i].checked;
		if (state == false)
		{
			all_state=false;
			break;
		}
	}
	if(getObj(module+"_selectall")) {
		getObj(module+"_selectall").checked=all_state;
	}
}
//To clear all the checked items in all the pages for Campaigns related list:
function clear_checked_all(module)
{
	var cookie_val=get_cookie(module+"_all");
	if(cookie_val != null)
		delete_cookie(module+"_all");
	//Uncheck all the boxes in current page..
	var obj = document.getElementsByName(module+"_selected_id");
	if (obj) {
		for (var i=0;i<obj.length;i++) {
			obj[i].checked=false;
		}
	}
	if(getObj(module+"_selectall")) {
		getObj(module+"_selectall").checked=false;
	}
}
//groupParentElementId is added as there are multiple groups in Documents listview.
function toggleSelect_ListView(state,relCheckName,groupParentElementId) {
	var obj = document.getElementsByName(relCheckName);
	if (obj) {
		for (var i=0;i<obj.length;i++) {
			obj[i].checked=state;
			if(typeof(check_object) == 'function') {
				// This function is defined in ListView.js (check for existence)
				check_object(obj[i],groupParentElementId);
			}
		}
	}
	if($('curmodule') != undefined && $('curmodule').value == 'Documents') {
		if(state==true)	{
			var count = $('numOfRows_'+groupParentElementId).value;
			if(count == '') {
				getNoOfRows(groupParentElementId);
			}
			if(parseInt($('maxrecords').value) < parseInt(count)) {
				$('linkForSelectAll_'+groupParentElementId).show();
			}

		} else {
			if($('selectedboxes_'+groupParentElementId).value == 'all') {
				$('linkForSelectAll_'+groupParentElementId).show();
			} else {
				$('linkForSelectAll_'+groupParentElementId).hide();
			}
		}
	} else {
		if(state==true)	{
			var count = $('numOfRows').value;
			if(count == '')	{
				getNoOfRows();
			}
			if(parseInt($('maxrecords').value) < parseInt(count)) {
				$('linkForSelectAll').show();
			}

		} else {
			if($('allselectedboxes').value == 'all') {
				$('linkForSelectAll').show();
			} else {
				$('linkForSelectAll').hide();
			}
		}
	}
}

function gotourl(url)
{
	document.location.href=url;
}

// Function to display the element with id given by showid and hide the element with id given by hideid
function toggleShowHide(showid, hideid)
{
	var show_ele = document.getElementById(showid);
	var hide_ele = document.getElementById(hideid);
	if(show_ele != null)
		show_ele.style.display = "inline";
	if(hide_ele != null)
		hide_ele.style.display = "none";
}

// Refactored APIs from DisplayFiels.tpl
function fnshowHide(currObj,txtObj) {
	if(currObj.checked == true)
		document.getElementById(txtObj).style.visibility = 'visible';
	else
		document.getElementById(txtObj).style.visibility = 'hidden';
}

function fntaxValidation(txtObj) {
	if (!numValidate(txtObj,"Tax","any"))
		document.getElementById(txtObj).value = 0;
}

function fnpriceValidation(txtObj) {
	if (!numValidate(txtObj,"Price","any"))
		document.getElementById(txtObj).value = 0;
}

function delimage(id) {
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'module=Contacts&action=ContactsAjax&file=DelImage&recordid='+id,
			onComplete: function(response) {
				if(response.responseText.indexOf("SUCCESS")>-1)
					$("replaceimage").innerHTML=alert_arr.LBL_IMAGE_DELETED;
				else
					alert(alert_arr.ERROR_WHILE_EDITING);
			}
		}
		);
}

function delUserImage(id) {
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'module=Users&action=UsersAjax&file=Save&deleteImage=true&recordid='+id,
			onComplete: function(response) {
				if(response.responseText.indexOf("SUCCESS")>-1)
					$("replaceimage").innerHTML=alert_arr.LBL_IMAGE_DELETED;
				else
					alert(alert_arr.ERROR_WHILE_EDITING);
			}
		}
		);
}

// Function to enable/disable related elements based on whether the current object is checked or not
function fnenableDisable(currObj,enableId) {
	var disable_flag = true;
	if(currObj.checked == true)
		disable_flag = false;

	document.getElementById('curname'+enableId).disabled = disable_flag;
	document.getElementById('cur_reset'+enableId).disabled = disable_flag;
	document.getElementById('base_currency'+enableId).disabled = disable_flag;
}

// Update current value with current value of base currency and the conversion rate
function updateCurrencyValue(currObj,txtObj,base_curid,conv_rate) {
	var unit_price = $(base_curid).value;

	if(typeof userCurrencySeparator != 'undefined') {
		while(unit_price.indexOf(userCurrencySeparator) != -1) {
			unit_price = unit_price.replace(userCurrencySeparator,'');
		}
	}
	if(typeof userDecimalSeparator != 'undefined') {
		if(unit_price.indexOf(userDecimalSeparator) != -1) {
			unit_price = unit_price.replace(userDecimalSeparator,'.');
		}
	}
	document.getElementById(txtObj).value = unit_price * conv_rate;
}

// Synchronize between Unit price and Base currency value.
function updateUnitPrice(from_cur_id, to_cur_id) {
	var from_ele = document.getElementById(from_cur_id);
	if (from_ele == null) return;

	var to_ele = document.getElementById(to_cur_id);
	if (to_ele == null) return;

	to_ele.value = from_ele.value;
}

// Update hidden base currency value, everytime the base currency value is changed in multi-currency UI
function updateBaseCurrencyValue() {
	var cur_list = document.getElementsByName('base_currency_input');
	if (cur_list == null) return;

	var base_currency_ele = document.getElementById('base_currency');
	if (base_currency_ele == null) return;

	for(var i=0; i<cur_list.length; i++) {
		var cur_ele = cur_list[i];
		if (cur_ele != null && cur_ele.checked == true)
			base_currency_ele.value = cur_ele.value;
	}
}
// END

/******************************************************************************/
/* Activity reminder Customization: Setup Callback */
function ActivityReminderProgressIndicator(show) {
	if(show) $("status").style.display = "inline";
	else $("status").style.display = "none";
}

function ActivityReminderSetupCallback(cbmodule, cbrecord) {
	if(cbmodule && cbrecord) {

		ActivityReminderProgressIndicator(true);
		new Ajax.Request(
			'index.php',
			{
				queue: {
					position: 'end',
					scope: 'command'
				},
				method: 'post',
				postBody:"module=Calendar&action=CalendarAjax&ajax=true&file=ActivityReminderSetupCallbackAjax&cbmodule="+
				encodeURIComponent(cbmodule) + "&cbrecord=" + encodeURIComponent(cbrecord),
				onComplete: function(response) {
					$("ActivityReminder_callbacksetupdiv").innerHTML=response.responseText;

					ActivityReminderProgressIndicator(false);

				}
			});
}
}

function ActivityReminderSetupCallbackSave(form) {
	var cbmodule = form.cbmodule.value;
	var cbrecord = form.cbrecord.value;
	var cbaction = form.cbaction.value;

	var cbdate   = form.cbdate.value;
	var cbtime   = form.cbhour.value + ":" + form.cbmin.value;

	if(cbmodule && cbrecord) {
		ActivityReminderProgressIndicator(true);

		new Ajax.Request("index.php",
		{
			queue:{
				position:"end",
				scope:"command"
			},
			method:"post",
			postBody:"module=Calendar&action=CalendarAjax&ajax=true&file=ActivityReminderSetupCallbackAjax" +
			"&cbaction=" + encodeURIComponent(cbaction) +
			"&cbmodule="+ encodeURIComponent(cbmodule) +
			"&cbrecord=" + encodeURIComponent(cbrecord) +
			"&cbdate=" + encodeURIComponent(cbdate) +
			"&cbtime=" + encodeURIComponent(cbtime),
			onComplete:function (response) {
				ActivityReminderSetupCallbackSaveProcess(response.responseText);
			}
		});
}
}
function ActivityReminderSetupCallbackSaveProcess(message) {
	ActivityReminderProgressIndicator(false);
	$('ActivityReminder_callbacksetupdiv_lay').style.display='none';
}

function ActivityReminderPostponeCallback(cbmodule, cbrecord, cbreminderid) {
	if(cbmodule && cbrecord) {

		ActivityReminderProgressIndicator(true);
		new Ajax.Request("index.php",
		{
			queue:{
				position:"end",
				scope:"command"
			},
			method:"post",
			postBody:"module=Calendar&action=CalendarAjax&ajax=true&file=ActivityReminderSetupCallbackAjax&cbaction=POSTPONE&cbmodule="+
			encodeURIComponent(cbmodule) + "&cbrecord=" + encodeURIComponent(cbrecord) + "&cbreminderid=" + encodeURIComponent(cbreminderid),
			onComplete:function (response) {
				ActivityReminderPostponeCallbackProcess(response.responseText);
			}
		});
}
}
function ActivityReminderPostponeCallbackProcess(message) {
	ActivityReminderProgressIndicator(false);
}
function ActivityReminderRemovePopupDOM(id) {
	if($(id)) $(id).remove();
}
/* END */

/* ActivityReminder Customization: Pool Callback */
var ActivityReminder_regcallback_timer;

var ActivityReminder_callback_delay = 40 * 1000; // Milli Seconds
var ActivityReminder_autohide = false; // If the popup should auto hide after callback_delay?

var ActivityReminder_popup_maxheight = 75;

var ActivityReminder_callback;
var ActivityReminder_timer;
var ActivityReminder_progressive_height = 2; // px
var ActivityReminder_popup_onscreen = 2 * 1000; // Milli Seconds (should be less than ActivityReminder_callback_delay)

var ActivityReminder_callback_win_uniqueids = new Object();

function ActivityReminderCallback() {
	if(typeof(Ajax) == 'undefined') {
		return;
	}
	if(ActivityReminder_regcallback_timer) {
		window.clearTimeout(ActivityReminder_regcallback_timer);
		ActivityReminder_regcallback_timer = null;
	}
	new Ajax.Request("index.php",
	{
		queue:{
			position:"end",
			scope:"command"
		},
		method:"post",
		postBody:"module=Calendar&action=CalendarAjax&file=ActivityReminderCallbackAjax&ajax=true",
		onComplete:function (response) {
			ActivityReminderCallbackProcess(response.responseText);
		}
	});
}
function ActivityReminderCallbackProcess(message) {
	ActivityReminder_callback = document.getElementById("ActivityRemindercallback");
	if(ActivityReminder_callback == null) return;
	ActivityReminder_callback.style.display = 'block';

	var winuniqueid = 'ActivityReminder_callback_win_' + (new Date()).getTime();
	if(ActivityReminder_callback_win_uniqueids[winuniqueid]) {
		winuniqueid += "-" + (new Date()).getTime();
	}
	ActivityReminder_callback_win_uniqueids[winuniqueid] = true;

	var ActivityReminder_callback_win = document.createElement("span");
	ActivityReminder_callback_win.id  = winuniqueid;
	ActivityReminder_callback.appendChild(ActivityReminder_callback_win);

	$(ActivityReminder_callback_win).update(message);
	ActivityReminder_callback_win.style.height = "0px";
	ActivityReminder_callback_win.style.display = "";

	var ActivityReminder_Newdelay_response_node = '_vtiger_activityreminder_callback_interval_';
	if($(ActivityReminder_Newdelay_response_node)) {
		var ActivityReminder_Newdelay_response_value = parseInt($(ActivityReminder_Newdelay_response_node).innerHTML);
		if(ActivityReminder_Newdelay_response_value > 0) {
			ActivityReminder_callback_delay = ActivityReminder_Newdelay_response_value;
		}
		// We don't need the no any longer, it will be sent from server for next Popup
		$(ActivityReminder_Newdelay_response_node).remove();
	}
	if(message == '' || trim(message).indexOf('<script') == 0) {
		// We got only new dealay value but no popup information, let us remove the callback win created
		$(ActivityReminder_callback_win.id).remove();
		ActivityReminder_callback_win = false;
		message = '';
	}

	if(message != "") ActivityReminderCallbackRollout(ActivityReminder_popup_maxheight, ActivityReminder_callback_win);
	else {
		ActivityReminderCallbackReset(0, ActivityReminder_callback_win);
	}
}
function ActivityReminderCallbackRollout(z, ActivityReminder_callback_win) {
	ActivityReminder_callback_win = $(ActivityReminder_callback_win);

	if (ActivityReminder_timer) {
		window.clearTimeout(ActivityReminder_timer);
	}
	if (ActivityReminder_callback_win && parseInt(ActivityReminder_callback_win.style.height) < z) {
		ActivityReminder_callback_win.style.height = parseInt(ActivityReminder_callback_win.style.height) + ActivityReminder_progressive_height + "px";
		ActivityReminder_timer = setTimeout("ActivityReminderCallbackRollout(" + z + ",'" + ActivityReminder_callback_win.id + "')", 1);
	} else {
		ActivityReminder_callback_win.style.height = z + "px";
		if(ActivityReminder_autohide) ActivityReminder_timer = setTimeout("ActivityReminderCallbackRollin(1,'" + ActivityReminder_callback_win.id + "')", ActivityReminder_popup_onscreen);
		else ActivityReminderRegisterCallback(ActivityReminder_callback_delay);
	}
}
function ActivityReminderCallbackRollin(z, ActivityReminder_callback_win) {
	ActivityReminder_callback_win = $(ActivityReminder_callback_win);

	if (ActivityReminder_timer) {
		window.clearTimeout(ActivityReminder_timer);
	}
	if (parseInt(ActivityReminder_callback_win.style.height) > z) {
		ActivityReminder_callback_win.style.height = parseInt(ActivityReminder_callback_win.style.height) - ActivityReminder_progressive_height + "px";
		ActivityReminder_timer = setTimeout("ActivityReminderCallbackRollin(" + z + ",'" + ActivityReminder_callback_win.id + "')", 1);
	} else {
		ActivityReminderCallbackReset(z, ActivityReminder_callback_win);
	}
}
function ActivityReminderCallbackReset(z, ActivityReminder_callback_win) {
	ActivityReminder_callback_win = $(ActivityReminder_callback_win);

	if(ActivityReminder_callback_win) {
		ActivityReminder_callback_win.style.height = z + "px";
		ActivityReminder_callback_win.style.display = "none";
	}
	if(ActivityReminder_timer) {
		window.clearTimeout(ActivityReminder_timer);
		ActivityReminder_timer = null;
	}
	ActivityReminderRegisterCallback(ActivityReminder_callback_delay);
}
function ActivityReminderRegisterCallback(timeout) {
	if(timeout == null) timeout = 1;
	if(ActivityReminder_regcallback_timer == null) {
		ActivityReminder_regcallback_timer = setTimeout("ActivityReminderCallback()", timeout);
	}
}

//added for finding duplicates
function movefields()
{
	availListObj=getObj("availlist")
	selectedColumnsObj=getObj("selectedCol")
	for (i=0;i<selectedColumnsObj.length;i++)
	{

		selectedColumnsObj.options[i].selected=false
	}

	movefieldsStep1();
}

function movefieldsStep1()
{

	availListObj=getObj("availlist")
	selectedColumnsObj=getObj("selectedCol")
	document.getElementById("selectedCol").style.width="164px";
	var count=0;
	for(i=0;i<availListObj.length;i++)
	{
		if (availListObj.options[i].selected==true)
		{
			count++;
		}

	}
	var total_fields=count+selectedColumnsObj.length;
	if (total_fields >4 )
	{
		alert(alert_arr.MAX_RECORDS)
		return false
	}
	if (availListObj.options.selectedIndex > -1)
	{
		for (i=0;i<availListObj.length;i++)
		{
			if (availListObj.options[i].selected==true)
			{
				var rowFound=false;
				for (j=0;j<selectedColumnsObj.length;j++)
				{
					selectedColumnsObj.options[j].value==availListObj.options[i].value;
					if (selectedColumnsObj.options[j].value==availListObj.options[i].value)
					{
						var rowFound=true;
						var existingObj=selectedColumnsObj.options[j];
						break;
					}
				}

				if (rowFound!=true)
				{
					var newColObj=document.createElement("OPTION")
					newColObj.value=availListObj.options[i].value
					if (browser_ie) newColObj.innerText=availListObj.options[i].innerText
					else if (browser_nn4 || browser_nn6) newColObj.text=availListObj.options[i].text
					selectedColumnsObj.appendChild(newColObj)
					newColObj.selected=true
				}
				else
				{
					existingObj.selected=true
				}
				availListObj.options[i].selected=false
				movefieldsStep1();
			}
		}
	}
}

function selectedColClick(oSel)
{
	if (oSel.selectedIndex == -1 || oSel.options[oSel.selectedIndex].disabled == true)
	{
		alert(alert_arr.NOT_ALLOWED_TO_EDIT);
		oSel.options[oSel.selectedIndex].selected = false;
	}
}

function delFields()
{
	selectedColumnsObj=getObj("selectedCol");
	selected_tab = $("dupmod").value;
	if (selectedColumnsObj.options.selectedIndex > -1)
	{
		for (i=0;i < selectedColumnsObj.options.length;i++)
		{
			if(selectedColumnsObj.options[i].selected == true)
			{
				if(selected_tab == 4)
				{
					if(selectedColumnsObj.options[i].innerHTML == "Last Name")
					{
						alert(alert_arr.DEL_MANDATORY);
						del = false;
						return false;
					}
					else
						del = true;

				}
				else if(selected_tab == 7)
				{
					if(selectedColumnsObj.options[i].innerHTML == "Last Name" || selectedColumnsObj.options[i].innerHTML == "Company")
					{
						alert(alert_arr.DEL_MANDATORY);
						del = false;
						return false;
					}
					else
						del = true;
				}
				else if(selected_tab == 6)
				{
					if(selectedColumnsObj.options[i].innerHTML == "Account Name")
					{
						alert(alert_arr.DEL_MANDATORY);
						del = false;
						return false;
					}
					else
						del = true;
				}
				else if(selected_tab == 14)
				{
					if(selectedColumnsObj.options[i].innerHTML == "Product Name")
					{
						alert(alert_arr.DEL_MANDATORY);
						del = false;
						return false;
					}
					else
						del = true;
				}
				if(del == true)
				{
					selectedColumnsObj.remove(i);
					delFields();
				}
			}
		}
	}
}

function moveFieldUp()
{
	selectedColumnsObj=getObj("selectedCol")
	var currpos=selectedColumnsObj.options.selectedIndex
	var tempdisabled= false;
	for (i=0;i<selectedColumnsObj.length;i++)
	{
		if(i != currpos)
			selectedColumnsObj.options[i].selected=false
	}
	if (currpos>0)
	{
		var prevpos=selectedColumnsObj.options.selectedIndex-1

		if (browser_ie)
		{
			temp=selectedColumnsObj.options[prevpos].innerText
			tempdisabled = selectedColumnsObj.options[prevpos].disabled;
			selectedColumnsObj.options[prevpos].innerText=selectedColumnsObj.options[currpos].innerText
			selectedColumnsObj.options[prevpos].disabled = false;
			selectedColumnsObj.options[currpos].innerText=temp
			selectedColumnsObj.options[currpos].disabled = tempdisabled;
		}
		else if (browser_nn4 || browser_nn6)
		{
			temp=selectedColumnsObj.options[prevpos].text
			tempdisabled = selectedColumnsObj.options[prevpos].disabled;
			selectedColumnsObj.options[prevpos].text=selectedColumnsObj.options[currpos].text
			selectedColumnsObj.options[prevpos].disabled = false;
			selectedColumnsObj.options[currpos].text=temp
			selectedColumnsObj.options[currpos].disabled = tempdisabled;
		}
		temp=selectedColumnsObj.options[prevpos].value
		selectedColumnsObj.options[prevpos].value=selectedColumnsObj.options[currpos].value
		selectedColumnsObj.options[currpos].value=temp
		selectedColumnsObj.options[prevpos].selected=true
		selectedColumnsObj.options[currpos].selected=false
	}

}

function moveFieldDown()
{
	selectedColumnsObj=getObj("selectedCol")
	var currpos=selectedColumnsObj.options.selectedIndex
	var tempdisabled= false;
	for (i=0;i<selectedColumnsObj.length;i++)
	{
		if(i != currpos)
			selectedColumnsObj.options[i].selected=false
	}
	if (currpos<selectedColumnsObj.options.length-1)
	{
		var nextpos=selectedColumnsObj.options.selectedIndex+1

		if (browser_ie)
		{
			temp=selectedColumnsObj.options[nextpos].innerText
			tempdisabled = selectedColumnsObj.options[nextpos].disabled;
			selectedColumnsObj.options[nextpos].innerText=selectedColumnsObj.options[currpos].innerText
			selectedColumnsObj.options[nextpos].disabled = false;
			selectedColumnsObj.options[nextpos];

			selectedColumnsObj.options[currpos].innerText=temp
			selectedColumnsObj.options[currpos].disabled = tempdisabled;
		}
		else if (browser_nn4 || browser_nn6)
		{
			temp=selectedColumnsObj.options[nextpos].text
			tempdisabled = selectedColumnsObj.options[nextpos].disabled;
			selectedColumnsObj.options[nextpos].text=selectedColumnsObj.options[currpos].text
			selectedColumnsObj.options[nextpos].disabled = false;
			selectedColumnsObj.options[nextpos];
			selectedColumnsObj.options[currpos].text=temp
			selectedColumnsObj.options[currpos].disabled = tempdisabled;
		}
		temp=selectedColumnsObj.options[nextpos].value
		selectedColumnsObj.options[nextpos].value=selectedColumnsObj.options[currpos].value
		selectedColumnsObj.options[currpos].value=temp

		selectedColumnsObj.options[nextpos].selected=true
		selectedColumnsObj.options[currpos].selected=false
	}
}

function lastImport(module,req_module)
{
	var module_name= module;
	var parent_tab= document.getElementById('parenttab').value;
	if(module == '')
	{
		return false;
	}
	else

		//alert("index.php?module="+module_name+"&action=lastImport&req_mod="+req_module+"&parenttab="+parent_tab);
		window.open("index.php?module="+module_name+"&action=lastImport&req_mod="+req_module+"&parenttab="+parent_tab,"lastImport","width=750,height=602,menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes");
}

function merge_fields(selectedNames,module,parent_tab)
{

	var select_options=document.getElementsByName(selectedNames);
	var x= select_options.length;
	var req_module=module;
	var num_group=$("group_count").innerHTML;
	var pass_url="";
	var flag=0;
	//var i=0;
	var xx = 0;
	for(i = 0; i < x ; i++)
	{
		if(select_options[i].checked)
		{
			pass_url = pass_url+select_options[i].value +","
			xx++
		}
	}
	var tmp = 0
	if ( xx != 0)
	{

		if(xx > 3)
		{
			alert(alert_arr.MAX_THREE)
			return false;
		}
		if(xx > 0)
		{
			for(j=0;j<num_group;j++)
			{
				flag = 0
				var group_options=document.getElementsByName("group"+j);
				for(i = 0; i < group_options.length ; i++)
				{
					if(group_options[i].checked)
					{
						flag++
					}
				}
				if(flag > 0)
					tmp++;
			}
			if (tmp > 1)
			{
				alert(alert_arr.SAME_GROUPS)
				return false;
			}
			if(xx <2)
			{
				alert(alert_arr.ATLEAST_TWO)
				return false;
			}

		}

		window.open("index.php?module="+req_module+"&action=ProcessDuplicates&mergemode=mergefields&passurl="+pass_url+"&parenttab="+parent_tab,"Merge","width=750,height=602,menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes");
	}
	else
	{
		alert(alert_arr.ATLEAST_TWO);
		return false;
	}
}

function delete_fields(module)
{
	var select_options=document.getElementsByName('del');
	var x=select_options.length;
	var xx=0;
	url_rec="";

	for(var i=0;i<x;i++)
	{
		if(select_options[i].checked)
		{
			url_rec=url_rec+select_options[i].value +","
			xx++
		}
	}
	if($("current_action"))
		cur_action = $("current_action").innerHTML
	if (xx == 0)
	{
		alert(alert_arr.SELECT);
		return false;
	}
	var alert_str = alert_arr.DELETE + xx +alert_arr.RECORDS;
	if(module=="Accounts")
		alert_str = alert_arr.DELETE_ACCOUNT + xx +alert_arr.RECORDS;
	if(confirm(alert_str))
	{
		$("status").style.display="inline";
		new Ajax.Request(
			'index.php',
			{
				queue: {
					position: 'end',
					scope: 'command'
				},
				method: 'post',
				postBody:"module="+module+"&action="+module+"Ajax&file=FindDuplicateRecords&del_rec=true&ajax=true&return_module="+module+"&idlist="+url_rec+"&current_action="+cur_action+"&"+dup_start,
				onComplete: function(response) {
					$("status").style.display="none";
					$("duplicate_ajax").innerHTML= response.responseText;
				}
			}
			);
	}
	else
		return false;
}


function validate_merge(module)
{
	var check_var=false;
	var check_lead1=false;
	var check_lead2=false;

	var select_parent=document.getElementsByName('record');
	var len = select_parent.length;
	for(var i=0;i<len;i++)
	{
		if(select_parent[i].checked)
		{
			var check_parentvar=true;
		}
	}
	if (check_parentvar!=true)
	{
		alert(alert_arr.Select_one_record_as_parent_record);
		return false;
	}
	return true;
}

function select_All(fieldnames,cnt,module)
{
	var new_arr = Array();
	new_arr = fieldnames.split(",");
	var len=new_arr.length;
	for(i=0;i<len;i++)
	{
		var fld_names=new_arr[i]
		var value=document.getElementsByName(fld_names)
		var fld_len=document.getElementsByName(fld_names).length;
		for(j=0;j<fld_len;j++)
		{
			value[cnt].checked='true'
		//	alert(value[j].checked)
		}

	}
}

function selectAllDel(state,checkedName)
{
	var selectedOptions=document.getElementsByName(checkedName);
	var length=document.getElementsByName(checkedName).length;
	if(typeof(length) == 'undefined')
	{
		return false;
	}
	for(var i=0;i<length;i++)
	{
		selectedOptions[i].checked=state;
	}
}

function selectDel(ThisName,CheckAllName)
{
	var ThisNameOptions=document.getElementsByName(ThisName);
	var CheckAllNameOptions=document.getElementsByName(CheckAllName);
	var len1=document.getElementsByName(ThisName).length;
	var flag = true;
	if (typeof(document.getElementsByName(ThisName).length)=="undefined")
	{
		flag=true;
	}
	else
	{
		for (var j=0;j<len1;j++)
		{
			if (ThisNameOptions[j].checked==false)
			{
				flag=false
				break;
			}
		}
	}
	CheckAllNameOptions[0].checked=flag
}

// Added for page navigation in duplicate-listview
var dup_start = "";
function getDuplicateListViewEntries_js(module,url)
{
	dup_start = url;
	$("status").style.display="block";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody:"module="+module+"&action="+module+"Ajax&file=FindDuplicateRecords&ajax=true&"+dup_start,
			onComplete: function(response) {
				$("status").style.display="none";
				$("duplicate_ajax").innerHTML = response.responseText;
			}
		}
		);
}

function getUnifiedSearchEntries_js(search,module,url){
	var qryStr = document.getElementsByName('search_criteria')[0].value;
	$("status").style.display="block";
	var recordCount = document.getElementById(module+'RecordCount').value;
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody:"module="+module+"&search_tag="+search+"&action="+module+"Ajax&file=UnifiedSearch&ajax=true&"+url+
			'&query_string='+qryStr+'&search_onlyin='+encodeURIComponent('--USESELECTED--')+'&recordCount='+recordCount,
			onComplete: function(response) {
				$("status").style.display="none";
				$('global_list_'+module).innerHTML = response.responseText;
			}
		}
		);
}

/* End */

//Added after 5.0.4 for Documents Module
function positionDivToCenter(targetDiv)
{
	//Gets the browser's viewport dimension
	getViewPortDimension();
	//Gets the Target DIV's width & height in pixels using parseInt function
	divWidth =(parseInt(document.getElementById(targetDiv).style.width))/2;
	divHeight=(parseInt(document.getElementById(targetDiv).style.height))/2;
	//calculate horizontal and vertical locations relative to Viewport's dimensions
	mx = parseInt(XX/2)-parseInt(divWidth);
	my = parseInt(YY/3)-parseInt(divHeight);
	//Prepare the DIV and show in the center of the screen.
	document.getElementById(targetDiv).style.left=mx+"px";
	document.getElementById(targetDiv).style.top=my+"px";
}

function getViewPortDimension()
{
	if(!document.all)
	{
		XX = self.innerWidth;
		YY = self.innerHeight;
	}
	else if(document.all)
	{
		XX = document.documentElement.clientWidth;
		YY = document.documentElement.clientHeight;
	}
}

function toggleTable(id) {

	var listTableObj=getObj(id);
	if(listTableObj.style.display=="none")
	{
		listTableObj.style.display="";
	}
	else
	{
		listTableObj.style.display="none";
	}
//set_cookie(id,listTableObj.style.display)
}

function FileAdd(obj,Lay,return_action){
	fnvshobj(obj,Lay);
	window.frames['AddFile'].document.getElementById('divHeader').innerHTML="Add file";
	window.frames['AddFile'].document.FileAdd.return_action.value=return_action;
	positionDivToCenter(Lay);
}

function dldCntIncrease(fileid)
{
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=DocumentsAjax&mode=ajax&file=SaveFile&module=Documents&file_id='+fileid+"&act=updateDldCnt",
			onComplete: function(response) {
			}
		}
		);
}
//End Documents Module

//asterisk integration :: starts

/**
 * this function accepts a node and puts it at the center of the screen
 * @param object node - the dom object which you want to set in the center
 */
function placeAtCenter(node){
	var centerPixel = getViewPortCenter();
	node.style.position = "absolute";
	var point = getDimension(node);


	var topvalue = (centerPixel.y - point.y/2) ;
	var rightvalue = (centerPixel.x - point.x/2);

	//to ensure that values will not be negative
	if(topvalue<0) topvalue = 0;
	if(rightvalue < 0) rightvalue = 0;

	node.style.top = topvalue + "px";
	node.style.right =rightvalue + "px";
	node.style.left = '';
	node.style.bottom = '';

}

/**
 * this function gets the dimension of a node
 * @param node - the node whose dimension you want
 * @return height and width in array format
 */
function getDimension(node){
	var ht = node.offsetHeight;
	var wdth = node.offsetWidth;
	var nodeChildren = node.getElementsByTagName("*");
	var noOfChildren = nodeChildren.length;
	for(var index =0;index<noOfChildren;++index){
		ht = Math.max(nodeChildren[index].offsetHeight, ht);
		wdth = Math.max(nodeChildren[index].offsetWidth,wdth);
	}
	return {
		x: wdth,
		y: ht
	};
}

/**
 * this function returns the center co-ordinates of the viewport as an array
 */
function getViewPortCenter(){
	var height;
	var width;

	if(typeof window.pageXOffset != "undefined"){
		height = window.innerHeight/2;
		width = window.innerWidth/2;
		height +=window.pageYOffset;
		width +=window.pageXOffset;
	}else if(document.documentElement && typeof document.documentElement.scrollTop != "undefined"){
		height = document.documentElement.clientHeight/2;
		width = document.documentElement.clientWidth/2;
		height += document.documentElement.scrollTop;
		width += document.documentElement.scrollLeft;
	}else if(document.body && typeof document.body.clientWidth != "undefined"){
		height = window.screen.availHeight/2;
		width = window.screen.availWidth/2;
		height += document.body.clientHeight;
		width += document.body.clientWidth;
	}
	return {
		x: width,
		y: height
	};
}

/**
 * this function accepts a number and displays a div stating that there is an outgoing call
 * then it calls the number
 * @param number - the number to be called
 */
function startCall(number, recordid){
	div = document.getElementById('OutgoingCall').innerHTML;
	outgoingPopup = _defPopup();
	outgoingPopup.content = div;
	outgoingPopup.displayPopup(outgoingPopup.content);

	//var ASTERISK_DIV_TIMEOUT = 6000;
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: 'action=PBXManagerAjax&mode=ajax&file=StartCall&ajax=true&module=PBXManager&number='+encodeURIComponent(number)+'&recordid='+recordid,
			onComplete: function(response) {
				if(response.responseText == ''){
				//successfully called
				}else{
					alert(response.responseText);
				}
			}
		}
		);
}
//asterisk integration :: ends

//added for tooltip manager
function ToolTipManager(){
	var state = false;
	/**
	 * this function creates the tooltip div and adds the information to it
	 * @param string text - the text to be added to the tooltip
	 */
	function tip(node, text,id,fieldname){
		state=true;
		var divName = getDivId(id,fieldname);
		var div = document.getElementById(divName)
		if(!div){
			div = document.createElement('div');
			div.id = divName;
			div.style.position = 'absolute';
			if(typeof div.style.opacity == "string"){
				div.style.opacity = 0.8;
			}
			div.className = "tooltipClass";
		}

		div.innerHTML = text;
		document.body.appendChild(div);
		div.style.display = "block";
		positionTooltip(node, divName);
	}

	function getDivId(id,fieldname){
		return '__VT_tooltip_'+id+'_'+fieldname;
	}

	function exists(id,fieldname){
		return (typeof document.getElementById(getDivId(id,fieldname)) != 'undefined' &&
			document.getElementById(getDivId(id,fieldname)) != null);
	}

	function show(node,id,fieldname){
		var div = document.getElementById(getDivId(id,fieldname));
		if(typeof div !='undefined' && div != null){
			div.style.display = '';
			positionTooltip(node, getDivId(id,fieldname));
		}
	}

	/**
	 * this function removes the tooltip div
	 */
	function unTip(nodelay,id,fieldname){
		state=false;
		var divName = getDivId(id,fieldname);
		var div = document.getElementById(divName);
		if(typeof div != 'undefined' && div != null ){
			if(typeof nodelay != 'undefined' && nodelay != null){
				div.style.display = "none";
			}else{
				setTimeout(function(){
					if(!state){
						div.style.display = "none";
					}
				}, 700);
			}
		}
	}

	/**
	 * this function is used to position the tooltip div
	 * @param string obj - the id of the element where the div has to appear
	 * @param object div - the div which contains the info
	 */
	function positionTooltip(obj,div){
		var tooltip = document.getElementById(div);
		var leftSide = findPosX(obj);
		var topSide = findPosY(obj);
		var dimensions = getDimension(tooltip);
		var widthM = dimensions.x;
		var getVal = eval(leftSide) + eval(widthM);
		var tooltipDimensions = getDimension(obj);
		var tooltipWidth = tooltipDimensions.x;

		if(getVal  > document.body.clientWidth ){
			leftSide = eval(leftSide) - eval(widthM);
		}else{
			leftSide = eval(leftSide) + (eval(tooltipWidth)/2);
		}
		if(leftSide < 0) {
			leftSide = findPosX(obj) + tooltipWidth;
		}
		tooltip.style.left = leftSide + 'px';

		var heightTooltip = dimensions.y;
		var bottomSide = eval(topSide) + eval(heightTooltip);
		if(bottomSide > document.body.clientHeight){
			topSide = topSide - (bottomSide - document.body.clientHeight) - 10;
			if(topSide < 0 ){
				topSide = 10;
			}
		}else{
			topSide = eval(topSide) - eval(heightTooltip)/2;
			if(topSide<0){
				topSide = 10;
			}
		}
		tooltip.style.top= topSide + 'px';
	}

	return {
		tip:tip,
		untip:unTip,
		'exists': exists,
		'show': show,
		'getDivId':getDivId
	};
}
if(!tooltip){
	var tooltip = ToolTipManager();
}
//tooltip manager changes end

function submitFormForActionWithConfirmation(formName, action, confirmationMsg) {
	if (confirm(confirmationMsg)) {
		return submitFormForAction(formName, action);
	}
	return false;
}

function submitFormForAction(formName, action) {
	var form = document.forms[formName];
	if (!form) return false;
	form.action.value = action;
	form.submit();
	return true;
}

/** Javascript dialog box utility functions **/
VtigerJS_DialogBox = {
	_olayer : function(toggle) {
		var olayerid = "__vtigerjs_dialogbox_olayer__";
		VtigerJS_DialogBox._removebyid(olayerid);

		if(typeof(toggle) == 'undefined' || !toggle) return;

		var olayer = document.getElementById(olayerid);
		if(!olayer) {
			olayer = document.createElement("div");
			olayer.id = olayerid;
			olayer.className = "small veil";
			olayer.style.zIndex = (new Date()).getTime();

			// Avoid zIndex going beyond integer max
			// http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/7146#comment:1
			olayer.style.zIndex = parseInt((new Date()).getTime() / 1000);

			// In case zIndex goes to negative side!
			if(olayer.style.zIndex < 0) olayer.style.zIndex *= -1;
			if (browser_ie) {
				olayer.style.height = document.body.offsetHeight + (document.body.scrollHeight - document.body.offsetHeight) + "px";
			} else if (browser_nn4 || browser_nn6) {
				olayer.style.height = document.body.offsetHeight + "px";
			}
			olayer.style.width = "100%";
			document.body.appendChild(olayer);

			var closeimg = document.createElement("img");
			closeimg.src = 'themes/images/popuplay_close.png';
			closeimg.alt = 'X';
			closeimg.style.right= '10px';
			closeimg.style.top  = '5px';
			closeimg.style.position = 'absolute';
			closeimg.style.cursor = 'pointer';
			closeimg.onclick = VtigerJS_DialogBox.unblock;
			olayer.appendChild(closeimg);
		}
		if(olayer) {
			if(toggle) olayer.style.display = "block";
			else olayer.style.display = "none";
		}
		return olayer;
	},
	_removebyid : function(id) {
		if($(id)) $(id).remove();
	},
	unblock : function() {
		VtigerJS_DialogBox._olayer(false);
	},
	block : function(opacity) {
		if(typeof(opactiy)=='undefined') opacity = '0.3';
		var olayernode = VtigerJS_DialogBox._olayer(true);
		olayernode.style.opacity = opacity;
	},
	hideprogress : function() {
		VtigerJS_DialogBox._olayer(false);
		VtigerJS_DialogBox._removebyid('__vtigerjs_dialogbox_progress_id__');
	},
	progress : function(imgurl) {
		VtigerJS_DialogBox._olayer(true);
		if(typeof(imgurl) == 'undefined') imgurl = 'themes/images/plsWaitAnimated.gif';

		var prgbxid = "__vtigerjs_dialogbox_progress_id__";
		var prgnode = document.getElementById(prgbxid);
		if(!prgnode) {
			prgnode = document.createElement("div");
			prgnode.id = prgbxid;
			prgnode.className = 'veil_new';
			prgnode.style.position = 'absolute';
			prgnode.style.width = '100%';
			prgnode.style.top = '0';
			prgnode.style.left = '0';
			prgnode.style.display = 'block';

			document.body.appendChild(prgnode);

			prgnode.innerHTML =
			'<table border="5" cellpadding="0" cellspacing="0" align="center" style="vertical-align:middle;width:100%;height:100%;">' +
			'<tr><td class="big" align="center"><img src="'+ imgurl + '"></td></tr></table>';

		}
		if(prgnode) prgnode.style.display = 'block';
	},
	hideconfirm : function() {
		VtigerJS_DialogBox._olayer(false);
		VtigerJS_DialogBox._removebyid('__vtigerjs_dialogbox_alert_boxid__');
	},
	confirm : function(msg, onyescode) {
		VtigerJS_DialogBox._olayer(true);

		var dlgbxid = "__vtigerjs_dialogbox_alert_boxid__";
		var dlgbxnode = document.getElementById(dlgbxid);
		if(!dlgbxnode) {
			dlgbxnode = document.createElement("div");
			dlgbxnode.style.display = 'none';
			dlgbxnode.className = 'veil_new small';
			dlgbxnode.id = dlgbxid;
			dlgbxnode.innerHTML =
			'<table cellspacing="0" cellpadding="18" border="0" class="options small">' +
			'<tbody>' +
			'<tr>' +
			'<td nowrap="" align="center" style="color: rgb(255, 255, 255); font-size: 15px;">' +
			'<b>'+ msg + '</b></td>' +
			'</tr>' +
			'<tr>' +
			'<td align="center">' +
			'<input type="button" style="text-transform: capitalize;" onclick="$(\''+ dlgbxid + '\').hide();VtigerJS_DialogBox._olayer(false);VtigerJS_DialogBox._confirm_handler();" value="'+ alert_arr.YES + '"/>' +
			'<input type="button" style="text-transform: capitalize;" onclick="$(\''+ dlgbxid + '\').hide();VtigerJS_DialogBox._olayer(false)" value="' + alert_arr.NO + '"/>' +
			'</td>'+
			'</tr>' +
			'</tbody>' +
			'</table>';
			document.body.appendChild(dlgbxnode);
		}
		if(typeof(onyescode) == 'undefined') onyescode = '';
		dlgbxnode._onyescode = onyescode;
		if(dlgbxnode) dlgbxnode.style.display = 'block';
	},
	_confirm_handler : function() {
		var dlgbxid = "__vtigerjs_dialogbox_alert_boxid__";
		var dlgbxnode = document.getElementById(dlgbxid);
		if(dlgbxnode) {
			if(typeof(dlgbxnode._onyescode) != 'undefined' && dlgbxnode._onyescode != '') {
				eval(dlgbxnode._onyescode);
			}
		}
	}
}

function validateInputData(value, fieldLabel, typeofdata) {

	var typeinfo = typeofdata.split('~');
	var type = typeinfo[0];

	if(type == 'T') {
		if(!re_patternValidate(value,fieldLabel+" (Time)","TIMESECONDS"))
			return false;
	} else if(type == 'D' || type == 'DT') {
		if(!re_dateValidate(value,fieldLabel+" (Current User Date Format)","OTH"))
			return false
	} else if(type == 'I') {
		if(isNaN(value) || value.indexOf(".")!=-1) {
			alert(alert_arr.INVALID+fieldLabel);
			return false
		}
	} else if(type == 'N' || type == 'NN') {

		if(typeof(typeinfo[2]) == "undefined") {
			var numformat = "any";
		} else {
			var numformat = typeinfo[2]
		}

		if(type == 'NN') {
			var negativeallowed = true;
		} else {
			var negativeallowed = false;
		}

		if(numformat != "any") {
			if (isNaN(value)) {
				var invalid=true
			} else {
				var format = numformat.split(",")
				var splitval = value.split(".")

				if (negativeallowed == true) {
					if (splitval[0].indexOf("-") >= 0) {
						if (splitval[0].length-1 > format[0]) {
							invalid=true
						}
					} else {
						if (splitval[0].length > format[0]) {
							invalid=true
						}
					}
				} else {
					if (value < 0) {
						invalid=true
					} else if (format[0] == 2 && splitval[0] == 100 && (!splitval[1] || splitval[1]==0)) {
						invalid=false
					} else if (splitval[0].length > format[0]) {
						invalid=true
					}
				}

				if (splitval[1]) {
					if (splitval[1].length > format[1]) {
						invalid=true
					}
				}
			}

			if (invalid==true) {
				alert(alert_arr.INVALID + fieldLabel)
				return false;
			} else {
				return true;
			}
		} else {
			var splitval = value.split(".")
			var arr_len = splitval.length;
			var len = 0;
			if(splitval[0] > 18446744073709551615) {
				alert( fieldLabel + alert_arr.EXCEEDS_MAX);
				return false;
			}
			if(negativeallowed == true) {
				var re=/^(-|)(\d)*(\.)?\d+(\.\d\d*)*$/
			} else {
				var re=/^(\d)*(\.)?\d+(\.\d\d*)*$/
			}
		}

		//for precision check. ie.number must contains only one "."
		var dotcount=0;
		for (var i = 0; i < value.length; i++) {
			if (value.charAt(i) == ".")
				dotcount++;
		}

		if(dotcount>1) {
			alert(alert_arr.INVALID+fieldLabel)
			return false;
		}

		if (!re.test(value)) {
			alert(alert_arr.INVALID+fieldLabel)
			return false
		}
	} else if(type == 'E') {
		if (!re_patternValidate(value,fieldLabel+" (Email Id)","EMAIL"))
			return false
	}

	return true;
}

function re_dateValidate(fldval,fldLabel,type) {
	if(re_patternValidate(fldval,fldLabel,"DATE")==false)
		return false;
	dateval=fldval.replace(/^\s+/g, '').replace(/\s+$/g, '')

	var dateelements=splitDateVal(dateval)

	dd=dateelements[0]
	mm=dateelements[1]
	yyyy=dateelements[2]

	if (dd<1 || dd>31 || mm<1 || mm>12 || yyyy<1 || yyyy<1000) {
		alert(alert_arr.ENTER_VALID+fldLabel)
		return false
	}

	if ((mm==2) && (dd>29)) {//checking of no. of days in february month
		alert(alert_arr.ENTER_VALID+fldLabel)
		return false
	}

	if ((mm==2) && (dd>28) && ((yyyy%4)!=0)) {//leap year checking
		alert(alert_arr.ENTER_VALID+fldLabel)
		return false
	}

	switch (parseInt(mm)) {
		case 2 :
		case 4 :
		case 6 :
		case 9 :
		case 11 :
			if (dd>30) {
			alert(alert_arr.ENTER_VALID+fldLabel)
			return false
		}
	}

	var currdate=new Date()
	var chkdate=new Date()

	chkdate.setYear(yyyy)
	chkdate.setMonth(mm-1)
	chkdate.setDate(dd)

	if (type!="OTH") {
		if (!compareDates(chkdate,fldLabel,currdate,"current date",type)) {
			return false
		} else return true;
	} else return true;
}

//Copied from general.js and altered some lines. becos we cant send vales to function present in general.js. it accept only field names.
function re_patternValidate(fldval,fldLabel,type) {

	if (type.toUpperCase()=="EMAIL") {
		/*changes made to fix -- ticket#3278 & ticket#3461
		  var re=new RegExp(/^.+@.+\..+$/)*/
		//Changes made to fix tickets #4633, #5111  to accomodate all possible email formats
		var re=new RegExp(/^[a-zA-Z0-9]+([\_\-\.]*[a-zA-Z0-9]+[\_\-]?)*@[a-zA-Z0-9]+([\_\-]?[a-zA-Z0-9]+)*\.+([\-\_]?[a-zA-Z0-9])+(\.?[a-zA-Z0-9]+)*$/)
	}

	if (type.toUpperCase()=="DATE") {//DATE validation

		switch (userDateFormat) {
			case "yyyy-mm-dd" :
				var re = /^\d{4}(-)\d{1,2}\1\d{1,2}$/
				break;
			case "mm-dd-yyyy" :
			case "dd-mm-yyyy" :
				var re = /^\d{1,2}(-)\d{1,2}\1\d{4}$/
		}
	}


	if (type.toUpperCase()=="TIMESECONDS") {//TIME validation
		var re = new RegExp("^([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$|^([0-1][0-9]|[2][0-3]):([0-5][0-9])$");
	}
	if (!re.test(fldval)) {
		alert(alert_arr.ENTER_VALID + fldLabel)
		return false
	}
	else return true
}

function getTranslatedString(key, alertArray){
	if(alertArray != undefined) {
		if(alertArray[key] != undefined) {
			return alertArray[key];
		}
	}
    if(alert_arr[key] != undefined) {
        return alert_arr[key];
    }
    else {
        return key;
	}
}

function copySelectedOptions(source, destination) {

	var srcObj = $(source);
	var destObj = $(destination);

	if(typeof(srcObj) == 'undefined' || typeof(destObj) == 'undefined') return;

	for (i=0;i<srcObj.length;i++) {
		if (srcObj.options[i].selected==true) {
			var rowFound=false;
			var existingObj=null;
			for (j=0;j<destObj.length;j++) {
				if (destObj.options[j].value==srcObj.options[i].value) {
					rowFound=true
					existingObj=destObj.options[j]
					break
				}
			}

			if (rowFound!=true) {
				var newColObj=document.createElement("OPTION")
				newColObj.value=srcObj.options[i].value
				if (browser_ie) newColObj.innerText=srcObj.options[i].innerText
				else if (browser_nn4 || browser_nn6) newColObj.text=srcObj.options[i].text
				destObj.appendChild(newColObj)
				srcObj.options[i].selected=false
				newColObj.selected=true
				rowFound=false
			} else {
				if(existingObj != null) existingObj.selected=true
			}
		}
	}
}

function removeSelectedOptions(objName) {
	var obj = getObj(objName);
	if(obj == null || typeof(obj) == 'undefined') return;

	for (i=obj.options.length-1;i>=0;i--) {
		if (obj.options[i].selected == true) {
			obj.options[i] = null;
		}
	}
}

function convertOptionsToJSONArray(objName,targetObjName) {
	var obj = $(objName);
	var arr = [];
	if(typeof(obj) != 'undefined') {
		for (i=0; i<obj.options.length; ++i) {
			arr.push(obj.options[i].value);
		}
	}
	if(targetObjName != 'undefined') {
		var targetObj = $(targetObjName);
		if(typeof(targetObj) != 'undefined') targetObj.value = JSON.stringify(arr);
	}
	return arr;
}

function fnvshobjMore(obj,Lay,announcement){
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	var widthM = tagName.offsetWidth;
	if(Lay == 'editdiv') {
		leftSide = leftSide - 225;
		topSide = topSide - 225;
	} else if(Lay == 'transferdiv') {
		leftSide = leftSide - 10;
		topSide = topSide;
	}
	var IE = document.all?true:false;
	if(IE) {
		if($("repposition1")) {
			if(topSide > 1200) {
				topSide = topSide-250;
			}
		}
	}

	if((leftSide > 100) && (leftSide < 500)){
		tagName.style.left= leftSide -50 + 'px';
	} else if((leftSide >= 500) && (leftSide < 800)){
		tagName.style.left= leftSide -150 + 'px';
	} else if((leftSide >= 800) && (leftSide < 1400)){
		if((widthM > 100) && (widthM < 250)) {
			tagName.style.left= leftSide- 100  + 'px';
		} else if((widthM >= 250) && (widthM < 350)) {

			tagName.style.left= leftSide- 200  + 'px';
		}
		else if((widthM >= 350) && (widthM < 500)) {
			console.log(widthM);
			tagName.style.left= leftSide- 300  + 'px';
		}
		else {
			tagName.style.left= leftSide -550 + 'px';
		}
	} else {
		tagName.style.left= leftSide  + 5 +'px';
	}
	if(announcement){
		tagName.style.top = 110+'px';
	}else{
		tagName.style.top = 76+'px';
	}
	tagName.style.display = 'block';
	tagName.style.visibility = "visible";

}

function fnvshobjsearch(obj,Lay){
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	var widthM = maxW.substring(0,maxW.length-2);
	if(Lay == 'editdiv')
	{
		leftSide = leftSide - 225;
		topSide = topSide - 125;
	}else if(Lay == 'transferdiv')
	{
		leftSide = leftSide - 10;
		topSide = topSide;
	}
	var IE = document.all?true:false;
	if(IE) {
		if($("repposition1")) {
			if(topSide > 1200) {
				topSide = topSide-250;
			}
		}
	}

	var getVal = eval(leftSide) + eval(widthM);
	if(getVal  > document.body.clientWidth ) {
		leftSide = eval(leftSide) - eval(widthM);
		tagName.style.left = leftSide + 91 + 'px';
	} else {
		tagName.style.left= leftSide - 324 + 'px';
	}
	tagName.style.top= topSide + 33 + 'px';
	tagName.style.display = 'block';
	tagName.style.visibility = "visible";
}
function fnDropDownUser(obj,Lay){
	var tagName = document.getElementById(Lay);
	var leftSide = findPosX(obj);
	var topSide = findPosY(obj);
	var maxW = tagName.style.width;
	var widthM = maxW.substring(0,maxW.length-2);
	var getVal = eval(leftSide) + eval(widthM);
	if(getVal  > document.body.clientWidth ){
		leftSide = eval(leftSide) - eval(widthM);
		tagName.style.left = leftSide + 34 + 'px';
	}
	else
		tagName.style.left= leftSide  - 50 + 'px';
		tagName.style.top= topSide + 28 +'px';
		tagName.style.display = 'block';
}

//select the records across the pages
function toggleSelectAll_Records(module,state,relCheckName) {

	toggleSelect_ListView(state,relCheckName);
	if(state == true) {
		$('allselectedboxes').value = 'all';
		$('selectAllRec').style.display = 'none';
		$('deSelectAllRec').style.display = 'inline';
	} else {
		$('allselectedboxes').value = '';
		$('excludedRecords').value = '';
		$('selectCurrentPageRec').checked = false;
		$('selectAllRec').style.display = 'inline';
		$('deSelectAllRec').style.display = 'none';
		$('linkForSelectAll').hide();
	}
}

function toggleSelectDocumentRecords(module,state,relCheckName,parentEleId) {

	toggleSelect_ListView(state,relCheckName,parentEleId);
	if(state == true) {
		$('selectedboxes_'+parentEleId).value = 'all';
		$('selectAllRec_'+parentEleId).style.display = 'none';
		$('deSelectAllRec_'+parentEleId).style.display = 'inline';
	} else {
		$('selectedboxes_'+parentEleId).value = '';
		$('excludedRecords_'+parentEleId).value = '';
		$('currentPageRec_'+parentEleId).checked = false;
		$('selectAllRec_'+parentEleId).style.display = 'inline';
		$('deSelectAllRec_'+parentEleId).style.display = 'none';
		$('linkForSelectAll_'+parentEleId).hide();
	}
}

//Compute the number of rows in the current module
function getNoOfRows(id){
	var module = $('curmodule').value;
	var searchurl = $('search_url').value;
	var viewid = getviewId();
	var url = "module="+module+"&action="+module+"Ajax&file=ListViewCount&viewname="+viewid+searchurl;
	if(module == 'Documents') {
		var folderid = $('folderid_'+id).value;
		url = url+"&folderidstring="+folderid;
	}
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody:url,
			onComplete: function(response) {
				if(module != 'Documents') {
					$('numOfRows').value = response.responseText;
					$('count').innerHTML = response.responseText;
					if(parseInt($('maxrecords').value) < parseInt(response.responseText)){
						$('linkForSelectAll').show();
					}
				} else {
					$('numOfRows_'+id).value = response.responseText;
					$('count_'+id).innerHTML = response.responseText;
					if(parseInt($('maxrecords').value) < parseInt(response.responseText)){
						$('linkForSelectAll_'+id).show();
					}
				}
			}
		}
	);
}

//select all function for related list of campaign module
function rel_toggleSelectAll_Records(module,relmodule,state,relCheckName) {

	 rel_toggleSelect(state,relCheckName,relmodule);
	 if(state == true) {
		$(module+'_'+relmodule+'_selectallActivate').value = 'true';
		$(module+'_'+relmodule+'_selectAllRec').style.display = 'none';
		$(module+'_'+relmodule+'_deSelectAllRec').style.display = 'inline';
	} else {
		$(module+'_'+relmodule+'_selectallActivate').value = 'false';
		$(module+'_'+relmodule+'_excludedRecords').value = '';
		$(module+'_'+relmodule+'_selectCurrentPageRec').checked = false;
		$(module+'_'+relmodule+'_selectAllRec').style.display = 'inline';
		$(module+'_'+relmodule+'_deSelectAllRec').style.display = 'none';
		$(module+'_'+relmodule+'_linkForSelectAll').hide();
	}
}

// Compute the number of records related to capmaign record
function getNoOfRelatedRows(current_module,related_module){
	var recordid = document.getElementById('recordid').value;
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody:"module="+current_module+"&related_module="+related_module+"&action="+current_module+"Ajax&idlist="+recordid+"&file=ListViewCount&mode=relatedlist",
			onComplete: function(response) {
				$(current_module+'_'+related_module+'_numOfRows').value = response.responseText;
				$(related_module+'_count').innerHTML = response.responseText;
				if(parseInt($('maxrecords').value) < parseInt(response.responseText)){
					$(current_module+'_'+related_module+'_linkForSelectAll').show();
				}
			}
		}
	);
}

function updateParentCheckbox(obj,id){
	var parentCheck=true;
	if (obj) {
		for (var i=0; i<obj.length; ++i) {
			if(obj[i].checked != true){
				var parentCheck=false;
			}
		}
	}
	if(parentCheck){
		$(id+'_selectCurrentPageRec').checked=parentCheck;
	}
}

function showSelectAllLink(obj,exculdedArray){
	var viewForSelectLink = true;
	for (var i=0; i<obj.length; ++i) {
		obj[i].checked = true;
		for(var j=0; j<exculdedArray.length; ++j) {
			if(exculdedArray[j] == obj[i].value) {
				obj[i].checked = false;
				viewForSelectLink = false;
			}
		}
	}
	return viewForSelectLink;
}

function getMaxMassOperationLimit() {
	return 500;
}

function getviewId()
{
	if(document.getElementById("viewname") != null && typeof(document.getElementById("viewname")) != 'undefined') {
		var oViewname = document.getElementById("viewname");
		var viewid = oViewname.options[oViewname.selectedIndex].value;
	} else {
		var viewid = '';
	}
	return viewid;
}