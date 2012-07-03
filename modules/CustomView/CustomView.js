 /*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/

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