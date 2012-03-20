
/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function set_values(form) {

	if (form.duedate_flag.checked) {

		form.duedate_flag.value='on';

		form.duedate.value="";

		form.duetime.value="";

		form.duedate.readOnly=true;

		form.duetime.readOnly=true;

		document.images.jscal_trigger.width = 0;

		document.images.jscal_trigger.height = 0;

	}

	else {

		form.duedate_flag.value='off';

		form.duedate.readOnly=false;

		form.duetime.readOnly=false;

		if (form.duetime.readonly) alert (alert_arr.READONLY);

		document.images.jscal_trigger.width = 16;

		document.images.jscal_trigger.height = 16;

	}

}
function toggleTime()
{
	if(getObj("notime").checked)
	{
		getObj("notime").value = 'on';
		getObj("duration_hours").disabled = true;
		getObj("duration_minutes").disabled = true;
	}
	else
	{
		getObj("notime").value = 'off';
        getObj("duration_minutes").disabled = false;
		getObj("duration_hours").disabled = false;
	}
}

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

function showActivityView(selectactivity_view)
{
	//script to reload the page with the view type when the combo values are changed
	View_name = selectactivity_view.options[selectactivity_view.options.selectedIndex].value;
	document.frmOpenLstView.action = "index.php?module=Home&action=index&activity_view="+View_name;
	document.frmOpenLstView.submit();
}	

function exportCalendar(){
	if(document.getElementsByName('exportCalendar')[0].value == 'iCal'){
		var filename = $('ics_filename').value;
		VtigerJS_DialogBox.block();
	
      	var url = "index.php?module=Calendar&action=iCalExport&filename="+filename;
		location.href = url;
        VtigerJS_DialogBox.unblock();
		ghide('CalExport')
	}
}

function importCalendar(){
	var file = document.getElementById('ics_file').value;
	if (file != '') {
		if (file.indexOf('.ics') != (file.length - 4)) {
			alert(alert_arr.PLS_SELECT_VALID_FILE+".ics")
		}
		else {
			document.ical_import.action.value='iCalImport';
			document.ical_import.module.value='Calendar';
			document.ical_import.submit();
		}
	}
}
