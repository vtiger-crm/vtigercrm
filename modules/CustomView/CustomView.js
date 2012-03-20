 /*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
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
    if(fld[4] == 'D' || fld[4] == 'DT')
    {
	    $("and"+sel.id).innerHTML =  "";
	    if(sel.id != "fcol5")
		    $("and"+sel.id).innerHTML =  "<em old='(yyyy-mm-dd)'>("+$("user_dateformat").value+")</em>&nbsp;"+alert_arr.LBL_AND;
	    else
		    $("and"+sel.id).innerHTML =  "<em old='(yyyy-mm-dd)'>("+$("user_dateformat").value+")</em>&nbsp;";
    }
    else if(fld[4] == 'T' && fld[1] != 'time_start' && fld[1] != 'time_end')
    {
	    $("and"+sel.id).innerHTML =  "";
	    if(sel.id != "fcol5")
		    $("and"+sel.id).innerHTML =  "<em old='(yyyy-mm-dd)'>("+$("user_dateformat").value+" hh:mm:ss)</em>&nbsp;"+alert_arr.LBL_AND;
	    else
		    $("and"+sel.id).innerHTML =  "<em old='(yyyy-mm-dd)'>("+$("user_dateformat").value+" hh:mm:ss)</em>&nbsp;";
    }

else if(fld[4] == 'I' && fld[1] == 'time_start' ||  fld[1] == 'time_end')
    {
            $("and"+sel.id).innerHTML =  "";
            if(sel.id != "fcol5")
                    $("and"+sel.id).innerHTML =  "hh:mm&nbsp;"+alert_arr.LBL_AND;
            else
                    $("and"+sel.id).innerHTML = "hh:mm";
    }

    else if(fld[4] == 'T' && fld[1] == 'time_start' ||  fld[1] == 'time_end')
    {
            $("and"+sel.id).innerHTML =  "";
            if(sel.id != "fcol5")
                    $("and"+sel.id).innerHTML =  "hh:mm&nbsp;"+alert_arr.LBL_AND;
            else
                    $("and"+sel.id).innerHTML = "hh:mm";
    }


    else if(fld[4] == 'C')
    {
	    $("and"+sel.id).innerHTML =  "";
	    if(sel.id != "fcol5")
		    $("and"+sel.id).innerHTML =  "( Yes / No )&nbsp;"+alert_arr.LBL_AND;
	    else
		    $("and"+sel.id).innerHTML =  "( Yes / No )&nbsp;";
    } 
    else {
	    $("and"+sel.id).innerHTML =  "";
	    if(sel.id != "fcol5")
		    $("and"+sel.id).innerHTML =  "&nbsp;"+alert_arr.LBL_AND;
	    else
		    $("and"+sel.id).innerHTML =  "&nbsp;";
    } 	

    if(currField.value != null && currField.value.length != 0)
    {
	fieldtype = trimfValues(currField.value);
	ops = typeofdata[fieldtype];
	var off = 0;
	if(ops != null)
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
		off = 1;
		for (var i = 0; i < ops.length; i++)
		{
			var label = fLabels[ops[i]];
			if (label == null) continue;
			var option = new Option (fLabels[ops[i]], ops[i]);
			selObj.options[i + off] = option;
			if (currOption != null && currOption.value == option.value)
			{
				option.selected = true;
			}
		}
	}
    }else
    {
		if (currField.value == '') {
			selObj.options[0].selected = true;
		}
    }

}
function verify_data() {
	var isError = false;
	var errorMessage = "";
	if (trim(document.CustomView.viewName.value) == "") {
		isError = true;
		errorMessage += "\nView Name";
	}
	// Here we decide whether to submit the form.
	if (isError == true) {
		alert(alert_arr.MISSING_REQUIRED_FIELDS + errorMessage);
		return false;
	}
	//return true;
}


function CancelForm()
{
var cvmodule = document.templatecreate.cvmodule.value;
var viewid = document.templatecreate.cvid.value;
document.location.href = "index.php?module="+cvmodule+"&action=index&viewname="+viewid;
}


function check4null(form)
{
        var isError = false;
        var errorMessage = "";
        // Here we decide whether to submit the form.
        if (trim(form.subject.value) =='') {
                isError = true;
                errorMessage += "\n subject";
                form.subject.focus();
        }

        // Here we decide whether to submit the form.
        if (isError == true) {
                alert(alert_arr.MISSING_REQUIRED_FIELDS + errorMessage);
                return false;
        }
 return true;
}

// Added for Custom View Advance Filter validation
function checkval()
{
	var value,option,arr,dttime,sep;
	for(var i=1;i<=5;i++)
	{
		value=trim(getObj("fval"+i).value);
		option=getObj("fcol"+i).value;
		fopvalue=trim(getObj("fop"+i).value);
		if(option !="" && value !="")
		{
			if(getObj("fop"+i).selectedIndex == 0)
				{
					alert(alert_arr.LBL_SELECT_CRITERIA);
		        	        return false;	
				}
			arr=option.split(":");
			if(arr[4] == "N" || arr[4] == "I" || arr[4] == "NN")
			{
				sep=value.split(",");
				for(var j=0;j<sep.length;j++)
				{
					if(arr[3] == "Calendar_Start_Time" || arr[3] == "Calendar_End_Time")
					{
						if(!cv_patternValidate(sep[j],"Time","TIME"))
						{
							getObj("fval"+i).select();
							return false;
						}
					}
					else if(isNaN(sep[j]))
					{
						alert(alert_arr.LBL_ENTER_VALID_NO);
						getObj("fval"+i).select();
						return false;
					}
				
	
				}
			}
			if(arr[4] == "D")
			{

				sep=value.split(",");
                                for(var j=0;j<sep.length;j++)
                                {
					if(!cv_dateValidate(trim(sep[j]),"Date","OTH"))
					{
						getObj("fval"+i).select();
						return false;
					}
				}
			}	
			if(arr[4] == "T")
			{

				sep=value.split(",");
				for(var j=0;j<sep.length;j++)
				{
					var dttime=trim(sep[j]).split(" ");

					if(arr[3] == "Calendar_Time_Start" || arr[3] == "Calendar_End_Time")
                                        {
                                                if(!cv_patternValidate(sep[j],"Time","TIME"))
                                                {
                                                        getObj("fval"+i).select();
                                                        return false;
                                                }
                                        }
                                        else if(!cv_dateValidate(dttime[0],"Date","OTH"))
					{
						getObj("fval"+i).select();
						return false;
					}
					if(dttime.length > 1)
					{
						if(!cv_patternValidate(dttime[1],"Time","TIMESECONDS"))
						{
							getObj("fval"+i).select();
							return false;
						}
					}
				}

			}	
			if(arr[4] == "C")
			{
					if(value == "1")
					{
						getObj("fval"+i).value= "yes";
						continue;
					}
					else if(value == "0")
					{
						getObj("fval"+i).value= "no";
						continue;						
					}
					if(value.toLowerCase() != "yes") if(value.toLowerCase() != "no") 
					{
						alert(alert_arr.LBL_PROVIDE_YES_NO);
						getObj("fval"+i).select();
						return false;
					}
			}	
		}
		else if (!(option =="" && fopvalue == "" && value == "")) 
		{
			if(option =="")
			{
				alert(alert_arr.LBL_SELECT_COLUMN);
				return false;
			}
			if(fopvalue == "")
			{
				alert(alert_arr.LBL_SELECT_CRITERIA);
				return false;
			}
		}
	}
return true;
}

//Added for Custom view validation
//Copied from general.js and altered some lines. becos we cant send vales to function present in general.js. it accept only field names.
function cv_dateValidate(fldval,fldLabel,type) {
	if(cv_patternValidate(fldval,fldLabel,"DATE")==false)
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
		case 11 :	if (dd>30) {
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

//Added for Custom view validation
//Copied from general.js and altered some lines. becos we cant send vales to function present in general.js. it accept only field names.
function cv_patternValidate(fldval,fldLabel,type) {
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
	

	if (type.toUpperCase()=="TIMESECONDS") 
	{
		//TIME validation.optional hour, min and seconds
		//var re = new RegExp("^([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$");
		var re = new RegExp("^(([0-1]?[0-9])|([2][0-3]))(:([0-5]?[0-9]))?(:([0-5]?[0-9]))?$");
	}
	else if (type.toUpperCase()=="TIME") 
	{
		//TIME validation. optional hours and minutes only. dont accept second. added for calendar start and end time field.
		var re = new RegExp("^(([0-1]?[0-9])|([2][0-3]))(:([0-5]?[0-9]))$");
	}
	if (!re.test(fldval)) {
		alert(alert_arr.ENTER_VALID + fldLabel)
		return false
	}
	else return true




}
//added to hide date selection option, if a user doesn't have permission for not permitter standard filter column
//added to fix the ticket #5117
function standardFilterDisplay()
{
	if(getObj("stdDateFilterField"))
	{
		if(document.CustomView.stdDateFilterField.selectedIndex > -1 && document.CustomView.stdDateFilterField.options[document.CustomView.stdDateFilterField.selectedIndex].value == "not_accessible")
		{
			getObj('stdDateFilter').disabled = true;
			getObj('startdate').disabled = true;                                                                                         getObj('enddate').disabled = true;
			getObj('jscal_trigger_date_start').style.visibility="hidden";
			getObj('jscal_trigger_date_end').style.visibility="hidden";
		}
		else
		{
			getObj('stdDateFilter').disabled = false;
			getObj('startdate').disabled = false;
			getObj('enddate').disabled = false;
			getObj('jscal_trigger_date_start').style.visibility="visible";
			getObj('jscal_trigger_date_end').style.visibility="visible";
		}
	}
}

