/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function DisableSharing()
{

        x = document.SharedList.selected_id.length;
        idstring = "";
        xx = 0;
        if ( x == undefined)
        {

                if (document.SharedList.selected_id.checked)
                {
                        document.SharedList.idlist.value=document.SharedList.selected_id.value;
                }
                else
                {
                        alert(alert_arr.SELECT_ATLEAST_ONE_USER);
                        return false;
                }
        }
        else
        {
                for(i = 0; i < x ; i++)
                {
                        if(document.SharedList.selected_id[i].checked)
                        {
                                idstring = document.SharedList.selected_id[i].value +";"+idstring
                        xx++
                        }
                }
                if (xx != 0)
                {
                        document.SharedList.idlist.value=idstring;
                }
                else
                {
                        alert(alert_arr.SELECT_ATLEAST_ONE_USER);
                        return false;
                }
        }
        if(confirm(alert_arr.DISABLE_SHARING_CONFIRMATION+xx+alert_arr.USERS))
        {
                document.SharedList.action="index.php?module=Calendar&action=disable_sharing&return_module=Calendar&return_action=calendar_share";
        }
        else
        {
                return false;
        }
}



function showhide(argg)
{
	var x=document.getElementById(argg).style;
	if (x.display=="none") 
	{
		x.display="block"
	
	}
	else {
			x.display="none"
		  }
}


function showhideRepeat(argg1,argg2)
{
	var x=document.getElementById(argg2).style;
	var y=document.getElementById(argg1).checked;
	
	if (y)
	{
		x.display="block";
	}
	else {
		x.display="none";
	}
	
}



function gshow(argg1,type,startdate,enddate,starthr,startmin,startfmt,endhr,endmin,endfmt,viewOption,subtab)
{
	smin = parseInt(startmin,10);
        smin = smin - (smin%5);
	var y=document.getElementById(argg1).style;
	
		if(type != 'todo' && type!='')
		{
			for(var i=0;;i++){
				if( document.EditView.activitytype[i].value == type){
					document.EditView.activitytype[i].selected='yes';	
					break;
				}
			}
				
			smin = _2digit(smin);
			document.EditView.date_start.value = startdate;
			document.EditView.starthr.value = starthr;
			document.EditView.startmin.value = smin;
			document.EditView.startfmt.value = startfmt;
			document.EditView.viewOption.value = viewOption;
            document.EditView.subtab.value = subtab;
			calDuedatetime(type);
		}
		if(type == 'todo')
		{
                        smin = _2digit(smin);
			document.createTodo.task_date_start.value = startdate;
			document.createTodo.task_due_date.value = enddate;
			document.createTodo.starthr.value = starthr;
                        document.createTodo.startmin.value = smin;
                        document.createTodo.startfmt.value = startfmt;
			document.createTodo.viewOption.value = viewOption;
                        document.createTodo.subtab.value = subtab;
		}
		if (y.display=="none")
	        {
			y.display="block";
		}
}

function rptoptDisp(Opt){
	var currOpt = Opt.options[Opt.selectedIndex].value;
	if(currOpt == "Daily")
	{
		ghide('repeatWeekUI');
		ghide('repeatMonthUI');
	}
	else if(currOpt == "Weekly")
	{
		if(document.getElementById('repeatWeekUI').style.display == "none");
			document.getElementById('repeatWeekUI').style.display = "block";
		ghide('repeatMonthUI');
	}
	else if(currOpt == "Monthly")
	{
		ghide('repeatWeekUI');
		if(document.getElementById('repeatMonthUI').style.display == "none");
                        document.getElementById('repeatMonthUI').style.display = "block";
	}
	else if(currOpt == "Yearly")
	{
		ghide('repeatWeekUI');
                ghide('repeatMonthUI');
	}
}

function Taskshow(argg1,type,startdate,starthr,startmin,startfmt)
{
	var y=document.getElementById(argg1).style;
	if (y.display=="none")
        {
                document.EditView.date_start.value = startdate;
                document.EditView.starthr.value = starthr;
                document.EditView.startmin.value = startmin;
                document.EditView.startfmt.value = startfmt;
		y.display="block";
	}
}

function ghide(argg2)
{
	var z=document.getElementById(argg2).style;
	if (z.display=="block" ) 
	{
		z.display="none"
	
	}
}

 function moveMe(arg1) {
	var posx = 0;
	var posy = 0;
	var e=document.getElementById(arg1);
	
	if (!e) var e = window.event;
	
	if (e.pageX || e.pageY)
	{
		posx = e.pageX;
		posy = e.pageY;
	}
	else if (e.clientX || e.clientY)
	{
		posx = e.clientX + document.body.scrollLeft;
		posy = e.clientY + document.body.scrollTop;
	}
 }

function switchClass(myModule,toStatus) {
	var x=document.getElementById(myModule);
	if($(x))
	{
		if (toStatus=="on") {
			x.className="dvtSelectedCell";
		}
		if (toStatus=="off") {
			x.className="dvtUnSelectedCell";
		}
	}
		
}

function enableCalstarttime()
{
	if(document.SharingForm.sttime_check.checked == true)
		document.SharingForm.start_hour.disabled = false;
	else	
		document.SharingForm.start_hour.disabled = true;
}
function check_form()
{
	formSelectColumnString('inviteesid','selectedusers');
        if(trim(document.EditView.subject.value) == "")
        {
                alert(alert_arr.MISSING_EVENT_NAME);
                document.EditView.subject.focus()
                return false;
        }
        else
        {
		if(document.EditView.record.value != '')
                {
                        document.EditView.mode.value = 'edit';
                }
		else
		{
			document.EditView.mode.value = 'create';
		}
		starthour = parseInt(document.EditView.starthr.value,10);
                startmin  = parseInt(document.EditView.startmin.value,10);
                startformat = document.EditView.startfmt.value;
                endhour = parseInt(document.EditView.endhr.value,10);
                endmin  = parseInt(document.EditView.endmin.value,10);
                endformat = document.EditView.endfmt.value;
		followupformat = document.EditView.followup_startfmt.value;
                followuphour = parseInt(document.EditView.followup_starthr.value,10);
                followupmin = parseInt(document.EditView.followup_startmin.value,10);
		if(startformat != '')
		{
			if(startformat == 'pm')
			{
				if(starthour == 12)
					starthour = 12;
				else
					starthour = starthour + 12;
			}
			else
			{
				if(starthour == 12)
                                	starthour = 0;
				else
					starthour = starthour;
			}
		}
		if(endformat != '')
		{
			if(endformat == 'pm')
                        {
				if(endhour == 12)
                                        endhour = 12;
                                else
                                        endhour = endhour + 12;
                        }
			else
			{
				if(endhour == 12)
					endhour = 0;
				else
					endhour = endhour;
			}
		}
		if(!dateValidate('date_start','Start date','OTH'))
		{
			return false;
		}
		if(!dateValidate('due_date','End date','OTH'))
		{
			return false;
		}
		if(dateComparison('due_date','End date','date_start','Start date','GE'))
		{
			var dateval1=getObj('date_start').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
        	var dateval2=getObj('due_date').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
			var dateval3=getObj('followup_date').value.replace(/^\s+/g, '').replace(/\s+$/g, '');

            var dateelements1=splitDateVal(dateval1)
        	var dateelements2=splitDateVal(dateval2)
			var dateelements3=splitDateVal(dateval3)

            dd1=dateelements1[0]
	        mm1=dateelements1[1]
        	yyyy1=dateelements1[2]

            dd2=dateelements2[0]
	        mm2=dateelements2[1]
        	yyyy2=dateelements2[2]

			dd3=dateelements3[0]
            mm3=dateelements3[1]
            yyyy3=dateelements3[2]

            var date1=new Date()
	        var date2=new Date()
			var date3=new Date()

        	date1.setYear(yyyy1)
            date1.setMonth(mm1-1)
	        date1.setDate(dd1)
			date1.setHours(starthour)
			date1.setMinutes(startmin)

	        date2.setYear(yyyy2)
            date2.setMonth(mm2-1)
        	date2.setDate(dd2)
			date2.setHours(endhour)
			date2.setMinutes(endmin)

			date3.setYear(yyyy3)
            date3.setMonth(mm3-1)
            date3.setDate(dd3)
 
			diff_ms = Math.abs(date2.getTime()-date1.getTime())/(1000*60);
			hour = Math.floor(diff_ms/(60));
			minute = Math.floor(diff_ms % 60)
			document.EditView.duration_hours.value = hour;
            document.EditView.duration_minutes.value = minute;
			
            event_starthour = _2digit(starthour);
            event_startmin = _2digit(startmin);
            event_endhour = _2digit(endhour);
            event_endmin = _2digit(endmin);
            document.EditView.time_start.value = event_starthour+':'+event_startmin;
            document.EditView.time_end.value = event_endhour+':'+event_endmin;
			// Added for Aydin Kurt-Elli requirement START -by Minnie
            if (document.EditView.followup.checked == true && document.getElementById('date_table_thirdtd').style.display == 'block')
            {
                    if(!dateValidate('followup_date','Followup Date','OTH'))
                    {
                            return false;
                    }
                    if(followupformat != '')
                    {
                            if(followupformat == 'pm')
                            {
                                    if(followuphour == 12)
                                            followuphour = 12;
                                    else
                                            followuphour = followuphour + 12;
                            }
                            else
                            {
                                    if(followuphour == 12)
                                            followuphour = 0;
                                    else
                                            followuphour = followuphour;
                            }
                    }
	
					if ( compareDates(date3,'Followup Date',date2,'End Date','GE'))
                    {
                            if (date3 <= date2)
                            {
                                    if((followuphour*60+followupmin) <= (endhour*60+endmin))
                                    {
                                            alert(alert_arr.FOLLOWUPTIME_GREATER_THAN_STARTTIME);
                                            document.EditView.followup_starthr.focus();
                                            return false;
                                    }
                            }
                    }
                    else return false;
                 //modified to set followup end date depends on the event or todo. If it is Event, the difference between followup start date and end date is 1hr. If it is todo then difference is 5mins.
                    date3.setMinutes(followupmin);
                    date3.setHours(followuphour);
                    if(document.EditView.activitytype[0].checked == true)
                    {
                            date3.setMinutes(parseInt(date3.getMinutes(),10)+5);
                    }
                    if(document.EditView.activitytype[1].checked == true)
                    {
                            date3.setMinutes(parseInt(date3.getMinutes(),10)+60);
                    }
					var tempdate = getdispDate(date3);

					followuphour = _2digit(followuphour);
			        followupmin = _2digit(followupmin);
					followupendhour = _2digit(date3.getHours());
			        followupendmin = _2digit(date3.getMinutes());		
			        document.EditView.followup_due_date.value = tempdate; 
                    document.EditView.followup_time_start.value = followuphour+':'+followupmin;
                    document.EditView.followup_time_end.value = followupendhour+':'+followupendmin;
					//end
            }
            // Added for Aydin Kurt-Elli requirement END -by Minnie -->

			//added to avoid db error while giving characters in the repeat "every n no of day in month" text box
                        if((getObj("recurringcheck")) && (document.EditView.recurringcheck.checked == true) && (document.EditView.recurringtype.value =="Monthly"))
                        {
				if((document.EditView.repeatMonth[0].checked == true) && ((parseInt(parseFloat(document.EditView.repeatMonth_date.value))!=document.EditView.repeatMonth_date.value) || document.EditView.repeatMonth_date.value=='' || parseInt(document.EditView.repeatMonth_date.value)>'31' || document.EditView.repeatMonth_date.value<='0'))
                                {
                                        alert(alert_arr.INVALID +' "'+document.EditView.repeatMonth_date.value+'" ');
                                        document.EditView.repeatMonth_date.focus();
                                        return false;
                                }
                        }
                        //end


                        //added to check Start Date & Time,if Activity Status is Planned.//start
                        if(document.EditView.eventstatus.value == "Planned")
                        {
                                var chkdate=new Date()
				chkdate.setMinutes(event_startmin)
				chkdate.setHours(event_starthour)
                                chkdate.setYear(yyyy1)
                                chkdate.setMonth(mm1-1)
                                chkdate.setDate(dd1)
				if(!comparestartdate(chkdate)) return false;

                        }

		}	
		else
			return false;
		if(getObj("recurringcheck") && document.EditView.recurringcheck.checked == false)
                {
                        document.EditView.recurringtype.value = '--None--';
                }
                return true;
        }
}

function task_check_form()
{
	starthour = parseInt(document.createTodo.starthr.value,10);
        startmin  = parseInt(document.createTodo.startmin.value,10);
        startformat = document.createTodo.startfmt.value;
	if(startformat != '')
	{
        	if(startformat == 'pm')
                {
			if(starthour == 12)
				starthour = 12;
			else
                		starthour = starthour + 12;

                }
                else
                {
			if(starthour == 12)
				starthour = 0;
			else
				starthour = starthour;
                }
        }
	starthour = _2digit(starthour);
        startmin = _2digit(startmin);
        document.createTodo.task_time_start.value = starthour+':'+startmin;
	if(document.createTodo.record.value != '')
        {
        	document.createTodo.mode.value = 'edit';
        }
        else
        {
        	document.createTodo.mode.value = 'create';
        }

}


function maintask_check_form()
{
	starthour = parseInt(document.EditView.starthr.value,10);
        startmin  = parseInt(document.EditView.startmin.value,10);
        startformat = document.EditView.startfmt.value;
        if(startformat != '')
        {
                if(startformat == 'pm')
                {
			if(starthour == 12)
				starthour = 12;
			else
				starthour = starthour + 12;
                }
                else
                {
			if(starthour == 12)
                                starthour = 0;
                        else
                                starthour = starthour;
                }
        }
	starthour = _2digit(starthour);
        startmin = _2digit(startmin);
        document.EditView.time_start.value = starthour+':'+startmin;
}


var moveupLinkObj,moveupDisabledObj,movedownLinkObj,movedownDisabledObj;

function userEventSharing(selectedusrid,selcolid)
{
        formSelectColumnString(selectedusrid,selcolid);
}


function incUser(avail_users,sel_users)
{
	availListObj=getObj(avail_users)
        selectedColumnsObj=getObj(sel_users)
        var selectlength=selectedColumnsObj.length
        var availlength=availListObj.length

	for (i=0;i<selectedColumnsObj.length;i++) 
	{
		selectedColumnsObj.options[i].selected=false
	}
	for (i=0;i<availListObj.length;i++) 
	{
		if (availListObj.options[i].selected==true) 
		{
			var rowFound = false;
			var existingObj = null;
			for (j=0;j<selectedColumnsObj.length;j++) 
			{
				if (selectedColumnsObj.options[j].value==availListObj.options[i].value) 
				{
					rowFound=true;
					existingObj=selectedColumnsObj.options[j]
					break
				}
			}
			if (rowFound!=true) 
			{
				var newColObj=document.createElement("OPTION")
					newColObj.value=availListObj.options[i].value
					if (browser_ie) newColObj.innerText=availListObj.options[i].innerText
					else if (browser_nn4 || browser_nn6) newColObj.text=availListObj.options[i].text
						selectedColumnsObj.appendChild(newColObj)
							availListObj.options[i].selected=false
							newColObj.selected=true
							rowFound=false
			}
			else 
			{
				if (existingObj != null) existingObj.selected=true
			}
		}
	}
}

function rmvUser(sel_users)
{
	selectedColumnsObj=getObj(sel_users)
        var selectlength=selectedColumnsObj.options.length
	for(i = 0; i <= selectlength; i++)
	{
		if(selectedColumnsObj.options.selectedIndex >= 0)
		selectedColumnsObj.remove(selectedColumnsObj.options.selectedIndex)	
	}
	
}


// function to delete activity related contact in calendar
var del_ids = new Array();
function removeActContacts()
{
	var avail_contacts = getObj('parentid');
	// this block is to remove contacts and get deleted contact ids
	if(avail_contacts.options.selectedIndex > -1)
	{
		for(m = 0; m < avail_contacts.options.length; m++)
		{
			if(avail_contacts.options[m].selected == true)
			{
				del_ids.push(avail_contacts.options[m].value);
				avail_contacts.options[m] = null;
				removeActContacts();
			}
		}
	}
	document.EditView.deletecntlist.value = del_ids.join(";");
	
	// this block is to get available id list
	var avail_ids = new Array();
	for(n=0; n<avail_contacts.options.length;n++)
	{
		avail_ids.push(avail_contacts.options[n].value);	
	}
	document.EditView.contactidlist.value = avail_ids.join(";");
	
}
//end
function formSelectColumnString(usr,col)
{
	
	var selectedColumnsObj=getObj(col)
	usr_id = document.getElementById(usr);
	var selectedColStr = "";
        for (i=0;i<selectedColumnsObj.options.length;i++)
        {
        	selectedColStr += selectedColumnsObj.options[i].value + ";";
        }
	usr_id.value = selectedColStr;
}

function fnRedirect() {
        var OptionData = $('view_Option').options[$('view_Option').selectedIndex].value;
	if(OptionData == 'listview')
	{
		document.EventViewOption.action.value = "index";
		VtigerJS_DialogBox.block();
		window.document.EventViewOption.submit();
	}
	if(OptionData == 'hourview')
	{
		document.EventViewOption.action.value = "index";
		VtigerJS_DialogBox.block();
		window.document.EventViewOption.submit();
	}
}

function fnAddEvent(obj,CurrObj,start_date,end_date,start_hr,start_min,start_fmt,end_hr,end_min,end_fmt,viewOption,subtab,eventlist){
	var tagName = document.getElementById(CurrObj);
	var left_Side = findPosX(obj);
	var top_Side = findPosY(obj);
	tagName.style.left= left_Side  + 'px';
	tagName.style.top= top_Side + 22+ 'px';
	tagName.style.display = 'block';
	eventlist = eventlist.split(";");
	for(var i=0;i<(eventlist.length-1);i++){
		document.getElementById("add"+eventlist[i].toLowerCase()).href="javascript:gshow('addEvent','"+eventlist[i]+"','"+start_date+"','"+end_date+"','"+start_hr+"','"+start_min+"','"+start_fmt+"','"+end_hr+"','"+end_min+"','"+end_fmt+"','"+viewOption+"','"+subtab+"');fnRemoveEvent();";
	}
	document.getElementById("addtodo").href="javascript:gshow('createTodo','todo','"+start_date+"','"+end_date+"','"+start_hr+"','"+start_min+"','"+start_fmt+"','"+end_hr+"','"+end_min+"','"+end_fmt+"','"+viewOption+"','"+subtab+"');fnRemoveEvent();";
}
	
function fnRemoveEvent(){
	var tagName = document.getElementById('addEventDropDown').style.display = 'none';
}

function fnShowEvent(){
		var tagName = document.getElementById('addEventDropDown').style.display= 'block';
}

function getMiniCal(url){
	if(url == undefined)
		url = 'module=Calendar&action=ActivityAjax&type=minical&ajax=true';
	else
		 url = 'module=Calendar&action=ActivityAjax&'+url+'&type=minical&ajax=true';
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: url,
                        onComplete: function(response) {
                                $("miniCal").innerHTML=response.responseText;
                        }
                }

          );
}

function getCalSettings(url){
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Calendar&action=ActivityAjax&'+url+'&type=settings&ajax=true',
                        onComplete: function(response) {
                                $("calSettings").innerHTML=response.responseText;
                        }
                }

          );
}

function updateStatus(record,status,view,hour,day,month,year,type){
	if(type == 'event')
	{
		var OptionData = $('view_Option').options[$('view_Option').selectedIndex].value;
		
		new Ajax.Request(
                	'index.php',
                	{queue: {position: 'end', scope: 'command'},
                        	method: 'post',
                        	postBody: 'module=Calendar&action=ActivityAjax&record='+record+'&'+status+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&type=change_status&viewOption='+OptionData+'&subtab=event&ajax=true',
                        	onComplete: function(response) {
					if(OptionData == 'listview')
					{
						result = response.responseText.split('####');
                        $("total_activities").innerHTML = result[1];
                        //$("listView").innerHTML=result[0];
	                    document.EventViewOption.action.value = "index";
						window.document.EventViewOption.submit();
					}		
                   	if(OptionData == 'hourview')
					{
						result = response.responseText.split('####');
                        $("total_activities").innerHTML = result[1];
                        //$("hrView").innerHTML=result[0];
	                    document.EventViewOption.action.value = "index";
						window.document.EventViewOption.submit();             
					}
					
               }}
		);
	}
	if(type == 'todo')
        {
		new Ajax.Request(
                        'index.php',
			{queue: {position: 'end', scope: 'command'},
                                method: 'post',
				postBody: 'module=Calendar&action=ActivityAjax&record='+record+'&'+status+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&type=change_status&subtab=todo&ajax=true',
                                onComplete: function(response) {
					result = response.responseText.split('####');
					$("total_activities").innerHTML = result[1];
					$("mnuTab2").innerHTML=result[0];
                                }
                        }
                )
	}
}

function cal_navigation(type,urlstring,start)
{
	var url = urlstring;
	$("status").style.display="inline";
	if(type == 'event')
        {
			var users = document.getElementsByName('onlyforuser');
			var onlyforusers = users[0].value;
			var OptionData = $('view_Option').options[$('view_Option').selectedIndex].value;
	                new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'module=Calendar&action=CalendarAjax&file=ActivityAjax&ajax=true&n_type=nav&viewOption='+OptionData+url+start+'&subtab='+type+'&onlyforuser='+encodeURIComponent(onlyforusers),
                                onComplete: function(response) {
					if(OptionData == 'listview')
                                        {
                                                result = response.responseText.split('####');
                                                $("total_activities").innerHTML = result[1];
                                                $("listView").innerHTML=result[0];
						$("status").style.display="none";
                                        }
                                        if(OptionData == 'hourview')
					{
                                                result = response.responseText.split('####');
                                                $("total_activities").innerHTML = result[1];
                                                $("hrView").innerHTML=result[0];
						$("status").style.display="none";
                                        }
                                }
                        }
                );
        }
	if(type == 'todo')
        {
                new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'module=Calendar&action=CalendarAjax&file=ActivityAjax&ajax=true&n_type=nav'+url+start+'&subtab=todo',
                                onComplete: function(response) {
                                        result = response.responseText.split('####');
                                        $("total_activities").innerHTML = result[1];
                                        $("mnuTab2").innerHTML=result[0];
					$("status").style.display="none";
                                }
                        }
                );
        }	
}

function getcalAction(obj,Lay,id,view,hour,dateVal,type){
    var tagName = document.getElementById(Lay);
    var leftSide = findPosX(obj);
    var topSide = findPosY(obj);
    var maxW = tagName.style.width;
    var widthM = maxW.substring(0,maxW.length-2);
    var getVal = eval(leftSide) + eval(widthM);
    var vtDate = dateVal.split("-");
    var day = parseInt(vtDate[2],10);
    var month = parseInt(vtDate[1],10);
    var year = parseInt(vtDate[0],10);
    if(getVal  > window.innerWidth ){
        leftSide = eval(leftSide) - eval(widthM);
        tagName.style.left = leftSide + 'px';
    }
    else
        tagName.style.left= leftSide + 'px';
    tagName.style.top= topSide + 'px';
    tagName.style.display = 'block';
    tagName.style.visibility = "visible";
    if(type == 'event')
    {
	var heldstatus = "eventstatus=Held";
	var notheldstatus = "eventstatus=Not Held";
        var activity_mode = "Events";
	var complete = document.getElementById("complete");
	var pending = document.getElementById("pending");
	var postpone = document.getElementById("postpone");
	var actdelete =	document.getElementById("actdelete");
	var changeowner = document.getElementById("changeowner");
	var OptionData = document.getElementById('view_Option').options[document.getElementById('view_Option').selectedIndex].value;
	
    }
    if(type == 'todo')
    {
	var heldstatus = "status=Completed";
        var notheldstatus = "status=Deferred";
	var activity_mode = "Task";
	var complete = document.getElementById("taskcomplete");
        var pending = document.getElementById("taskpending");
        var postpone = document.getElementById("taskpostpone");
        var actdelete = document.getElementById("taskactdelete");
        var changeowner = document.getElementById("taskchangeowner");
	var OptionData = '';
    }
    document.getElementById("idlist").value = id;
    document.change_owner.hour.value = hour;
    document.change_owner.day.value = day;
    document.change_owner.view.value = view;
    document.change_owner.month.value = month;
    document.change_owner.year.value = year;
    document.change_owner.subtab.value = type;
    if(complete) complete.href="javascript:updateStatus("+id+",'"+heldstatus+"','"+view+"',"+hour+","+day+","+month+","+year+",'"+type+"')";
    if(pending) pending.href="javascript:updateStatus("+id+",'"+notheldstatus+"','"+view+"',"+hour+","+day+","+month+","+year+",'"+type+"')";

    if(postpone) postpone.href="index.php?module=Calendar&action=EditView&record="+id+"&return_action=index&activity_mode="+activity_mode+"&view="+view+"&hour="+hour+"&day="+day+"&month="+month+"&year="+year+"&viewOption="+OptionData+"&subtab="+type+"&maintab=Calendar";

    if(actdelete) actdelete.href="javascript:delActivity("+id+",'"+view+"',"+hour+","+day+","+month+","+year+",'"+type+"')";

    if(changeowner) changeowner.href="javascript:dispLayer('act_changeowner');";

}

function dispLayer(lay)
{
	var tagName = document.getElementById(lay);
	tagName.style.visibility = 'visible';
	tagName.style.display = 'block';
}

//check whether user form selected or group form selected
function checkgroup()
{
	if($("group_checkbox").checked)
	{
		  document.change_owner.lead_group_owner.style.display = "block";
	          document.change_owner.lead_owner.style.display = "none";
	}
	else
	{
		document.change_owner.lead_group_owner.style.display = "none";
                document.change_owner.lead_owner.style.display = "block";
	}
}

function calendarChangeOwner()
{
	var idlist = document.change_owner.idlist.value;
        var view   = document.change_owner.view.value;
        var day    = document.change_owner.day.value;
        var month  = document.change_owner.month.value;
        var year   = document.change_owner.year.value;
        var hour   = document.change_owner.hour.value;
        var subtab = document.change_owner.subtab.value;

	//var checked = document.change_owner.user_lead_owner[0].checked;
	if($("user_checkbox").checked)
	{
		var user_id = document.getElementById('lead_owner').options[document.getElementById('lead_owner').options.selectedIndex].value;
		var url = 'module=Users&action=updateLeadDBStatus&return_module=Calendar&return_action=ActivityAjax&owner_id='+user_id+'&idlist='+idlist+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&type=change_owner&owner_type=User';
	}
	else
	{
		var group_id = document.getElementById('lead_group_owner').options[document.getElementById('lead_group_owner').options.selectedIndex].value;
		var url = 'module=Users&action=updateLeadDBStatus&return_module=Calendar&return_action=ActivityAjax&owner_id='+group_id+'&idlist='+idlist+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&type=change_owner&owner_type=Group';
	}
		
	if(subtab == 'event')
	{
		var OptionData = $('view_Option').options[$('view_Option').selectedIndex].value;
		var eventurl = url+'&viewOption='+OptionData+'&subtab=event&ajax=true';
		
	 	new Ajax.Request(
                	'index.php',
                	{queue: {position: 'end', scope: 'command'},
                        	method: 'post',
                        	postBody: eventurl,
                        	onComplete: function(response) {
					if(OptionData == 'listview')
					{
						result = response.responseText.split('####');
                        $("total_activities").innerHTML = result[1];
                        $("listView").innerHTML=result[0];
						document.EventViewOption.action.value = "index";
						window.document.EventViewOption.submit();
					}
					if(OptionData == 'hourview')
					{
						result = response.responseText.split('####');
                        $("total_activities").innerHTML = result[1];
                        $("hrView").innerHTML=result[0];
						document.EventViewOption.action.value = "index";
						window.document.EventViewOption.submit();
					}
                        	}
                	}
		);
	}
	if(subtab == 'todo')
        {
		
		var todourl = url+'&subtab=todo&ajax=true';
                new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: todourl,
                                onComplete: function(response) {
					result = response.responseText.split('####');
					$("total_activities").innerHTML = result[1];
					$("mnuTab2").innerHTML=result[0];
                               }
                        }
                );
        }
}

function delActivity(id,view,hour,day,month,year,subtab)
{
	if(subtab == 'event')
	{
		var OptionData = $('view_Option').options[$('view_Option').selectedIndex].value;
         	new Ajax.Request(
	                	'index.php',
	                	{queue: {position: 'end', scope: 'command'},
	                        	method: 'post',
	                        	postBody: 'module=Users&action=massdelete&return_module=Calendar&return_action=ActivityAjax&idlist='+id+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&type=activity_delete&viewOption='+OptionData+'&subtab=event&ajax=true',
	                        	onComplete: function(response) {
								if(OptionData == 'listview')
								{
									result = response.responseText.split('####');
	                                $("total_activities").innerHTML = result[1];
	                                $("listView").innerHTML=result[0];
								}
								if(OptionData == 'hourview')
								{
									result = response.responseText.split('####');
									$("total_activities").innerHTML = result[1];
									$("hrView").innerHTML=result[0];
								}
                        	}
                	}
		);
	}
	if(subtab == 'todo')
        {
                new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'module=Users&action=massdelete&return_module=Calendar&return_action=ActivityAjax&idlist='+id+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&type=activity_delete&subtab=todo&ajax=true',
                                onComplete: function(response) {
					result = response.responseText.split('####');
					$("total_activities").innerHTML = result[1];
					$("mnuTab2").innerHTML=result[0];
                               }
                        }
                );
        }
}


/*
* javascript function to display the div tag
* @param divId :: div tag ID
*/
function cal_show(divId)

{

    var id = document.getElementById(divId);

    id.style.visibility = 'visible';

}

function fnAssignTo(){
		var option_Box = document.getElementById('parent_type');
		var option_select = option_Box.options[option_Box.selectedIndex].value;
		if(option_select == "Leads" || option_select == "Leads&action=Popup")
		{
			document.getElementById('leadLay').style.visibility = 'visible';
		}
		else if(option_select == "Accounts" || option_select == "Accounts&action=Popup")
		{
			document.getElementById('leadLay').style.visibility = 'visible';
		}
		else if(option_select == "Potentials" || option_select == "Potentials&action=Popup")
		{
			document.getElementById('leadLay').style.visibility = 'visible';
		}
		else if(option_select == "Quotes&action=Popup" || option_select == "Quotes&action=Popup")
                {
                        document.getElementById('leadLay').style.visibility = 'visible';
                }
		else if(option_select == "PurchaseOrder" || option_select == "PurchaseOrder&action=Popup")
                {
                        document.getElementById('leadLay').style.visibility = 'visible';
                }
		else if(option_select == "SalesOrder" || option_select == "SalesOrder&action=Popup")
                {
                        document.getElementById('leadLay').style.visibility = 'visible';
                }
		else if(option_select == "Invoice" || option_select == "Invoice&action=Popup")
                {
                        document.getElementById('leadLay').style.visibility = 'visible';
                }
		else if(option_select == "Campaigns" || option_select == "Campaigns&action=Popup")
                {
                        document.getElementById('leadLay').style.visibility = 'visible';
                }
		else{
			document.getElementById('leadLay').style.visibility = 'hidden';
		}
	}
	
function fnShowPopup(){
	document.getElementById('popupLay').style.display = 'block';
}
	
function fnHidePopup(){
	document.getElementById('popupLay').style.display = 'none';
}

function getValidationarr(id,activity_mode,opmode,subtab,viewOption)
{
	 new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'module=Calendar&action=ActivityAjax&record='+id+'&activity_mode='+activity_mode+'&ajax=true&type=view&file=DetailView',
                                onComplete: function(response) {
                                        $("dataArray").innerHTML=response.responseText;
					setFieldvalues(opmode,subtab,viewOption);
                                }
                        }
                );

}

function setFieldvalues(opmode,subtab,viewOption)
{
	var st = document.getElementById('activity_cont');
	eval(st.innerHTML);
	if(activity_type == 'Events')
	{
		document.EditView.viewOption.value = viewOption;
                document.EditView.subtab.value = subtab;
		for(x=0;x<key.length;x++)
		{	
			if(document.EditView[key[x]] != undefined)
			{

				if(key[x] == 'visibility' && data[x] == 'Public')
					document.EditView.visibility.checked = true;
				if(key[x] == 'visibility' && data[x] == 'Private')
					document.EditView.visibility.checked = false;
				if(key[x] == 'activitytype' && data[x] == 'Call')
				{
					document.EditView.activitytype[0].checked = true;
				}
				else
				{
					document.EditView.activitytype[1].checked = true;
				}
				if(key[x] == 'set_reminder' && data[x] == 'Yes')
				{
					document.EditView.remindercheck.checked = true;
					document.getElementById('reminderOptions').style.display = 'block';
				}
				if(key[x] == 'recurringcheck' && data[x] == 'on')
				{
					document.EditView.recurringcheck.checked = true;
					document.getElementById('repeatOptions').style.display = 'block';
				}
				if(key[x] == 'recurringtype')
				{	
					if(data[x] == 'Weekly')
						document.getElementById('repeatWeekUI').style.display = 'block';
					else
						document.getElementById('repeatWeekUI').style.display = 'none';
					if(data[x] == 'Monthly')
						document.getElementById('repeatMonthUI').style.display = 'block';
					else
						document.getElementById('repeatMonthUI').style.display = 'none';
				}
				if(key[x] == 'parent_name')
				{
					if(data[x] != '')
						document.getElementById('leadLay').style.visibility = 'visible';
					else
						document.getElementById('leadLay').style.display = 'hidden';
				}
				document.EditView[key[x]].value = data[x];
			//}	
			}
		}
		document.getElementById('addEvent').style.display = 'block';
	}
	else
	{
		document.createTodo.viewOption.value = viewOption;
                document.createTodo.subtab.value = subtab;
		for(x=0;x<key.length;x++)
                {
			if(document.createTodo[key[x]] != undefined)
			{
                                document.createTodo[key[x]].value = data[x];
			}
		}
		document.getElementById('createTodo').style.display = 'block';
	}
}

function doNothing()
{
}

/** This is Javascript Function which is used to toogle between
  * assigntype user and group/team select options while assigning owner to Task.
  */
function toggleTaskAssignType(currType)
{
        if (currType=="U")
        {
                getObj("task_assign_user").style.display="block"
                getObj("task_assign_team").style.display="none"
        }
        else
        {
                getObj("task_assign_user").style.display="none"
                getObj("task_assign_team").style.display="block"
        }
}

function dochange(start,end)
{
        var startdate = document.getElementById(start);
        document.getElementById(end).value = startdate.value;
}

function getSelectedStatus()
{
        var chosen = document.EditView.eventstatus.value;
        if(chosen == "Held")
        {
                document.getElementById('date_table_firsttd').style.width = "33%";
                document.getElementById('date_table_secondtd').style.width = "33%";
                document.getElementById('date_table_thirdtd').style.display = 'block';
        }
        else
        {
                document.getElementById('date_table_firsttd').style.width = "50%";
                document.getElementById('date_table_secondtd').style.width = "50%";
                document.getElementById('date_table_thirdtd').style.display = 'none';
        }

}

function changeEndtime_StartTime(type)
{
	calDuedatetime(type);
	return true;
}
 
function calDuedatetime(type)
{
        var dateval1=getObj('date_start').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
        var dateelements1=splitDateVal(dateval1);
        dd1=parseInt(dateelements1[0],10);
        mm1=dateelements1[1];
        yyyy1=dateelements1[2];
        var date1=new Date();
        //date1.setDate(dd1+1);
        date1.setYear(yyyy1);
        date1.setMonth(mm1-1,dd1+1);
	var tempdate = getdispDate(date1);
        var date = document.EditView.date_start.value;
        var hour = parseInt(document.EditView.starthr.value,10);
        var min = parseInt(document.EditView.startmin.value,10);
        var fmt = document.EditView.startfmt.value;
	if(type != 'Call')
        {
                if(fmt == 'pm')
                {
                        if(hour == 11)
                        {
                                date = tempdate;
                                hour = 12;
                                min = min;
                                fmt = 'am';

                        }else if(hour == 12)
                        {
                                hour = 1;
                                min = min;
                                fmt = 'pm';
			}
                	else hour = hour + 1;
			hour = _2digit(hour);
                        min = _2digit(min);
			document.EditView.due_date.value = date;
			document.EditView.endhr.value = hour;
			document.EditView.endmin.value = min;
			document.EditView.endfmt.value = fmt;
			document.EditView.followup_date.value = date;
                        document.EditView.followup_starthr.value = hour;
                        document.EditView.followup_startmin.value = min;
                        document.EditView.followup_startfmt.value = fmt;
		}else if(fmt == 'am')
		{
                        if(hour == 11)
                        {
                                hour = 12; min = min; fmt = 'pm';
                        }else if(hour == 12)
                        {
                                hour = 1; min = min; fmt = 'am';
                        }
                        else hour = hour + 1;
			hour = _2digit(hour);
                        min = _2digit(min);
                        document.EditView.due_date.value = date;
                        document.EditView.endhr.value = hour;
                        document.EditView.endmin.value = min;
                        document.EditView.endfmt.value = fmt;
			document.EditView.followup_date.value = date;
                        document.EditView.followup_starthr.value = hour;
                        document.EditView.followup_startmin.value = min;
                        document.EditView.followup_startfmt.value = fmt;
                }else
		{
                        hour = hour + 1;
                        if(hour == 24)
                        {
                                hour = 0;
                                date =  tempdate;
                        }
			hour = _2digit(hour);
                        min = _2digit(min);
                        document.EditView.due_date.value = date;
                        document.EditView.endhr.value = hour;
                        document.EditView.endmin.value = min;
			document.EditView.followup_date.value = date;
                        document.EditView.followup_starthr.value = hour;
                        document.EditView.followup_startmin.value = min;
                }
        }
	if(type == 'Call')
        {
                if(fmt == 'pm')
                {
                        if(hour == 11 && min == 55)
                        {
                                hour = 12; min = 0; fmt = 'am';
                                date = tempdate;
                        }
			else if(hour == 12 && min == 55)
			{
				hour = 1; min = 0; fmt = 'pm';
			}
                        else
                        {
                                if(min == 55)
                                {
                                        min = 0;
                                        hour = hour + 1;
                                }
                                else    min = min + 5;
                        }
			hour = _2digit(hour);
                        min = _2digit(min);
                        document.EditView.due_date.value = date;
                        document.EditView.endhr.value = hour;
                        document.EditView.endmin.value = min;
                        document.EditView.endfmt.value = fmt;
			document.EditView.followup_date.value = date;
                        document.EditView.followup_starthr.value = hour;
                        document.EditView.followup_startmin.value = min;
                        document.EditView.followup_startfmt.value = fmt;
                }else if(fmt == 'am')
		{
                        if(hour == 11 && min == 55)
                        {
                                hour = 12;
                                min = 0;
                                fmt = 'pm';
                        }
			else if(hour == 12 && min == 55)
			{
				hour = 1;
                                min = 0;
				fmt = 'am';
			}
                        else
                        {
                                if(min == 55)
                                {
                                        min = 0;
                                        hour = hour + 1;
                                }
                                else    min = min + 5;
                        }
			hour = _2digit(hour);
                        min = _2digit(min);
                        document.EditView.due_date.value = date;
                        document.EditView.endhr.value = hour;
                        document.EditView.endmin.value = min;
                        document.EditView.endfmt.value = fmt;
			document.EditView.followup_date.value = date;
                        document.EditView.followup_starthr.value = hour;
                        document.EditView.followup_startmin.value = min;
                        document.EditView.followup_startfmt.value = fmt;
                }
                else
		{
                        if(min == 55)
                        {
                                min = 0;
                                hour = hour + 1;
                        }else min = min + 5;
                        if(hour == 24)
                        {
                                hour = 0;
                                date =  tempdate;
                        }
			hour = _2digit(hour);
                        min = _2digit(min);
                        document.EditView.due_date.value = date;
                        document.EditView.endhr.value = hour;
                        document.EditView.endmin.value = min;
			document.EditView.followup_date.value = date;
                        document.EditView.followup_starthr.value = hour;
                        document.EditView.followup_startmin.value = min;
                }
        }
}

function cal_fnvshobj(obj,Lay){
    var tagName = document.getElementById(Lay);
    var leftSide = findPosX(obj);
    var topSide = findPosY(obj);
    tagName.style.left = 550 + 'px';
    tagName.style.top= (topSide - 100) + 'px';
    tagName.style.display = 'block';
    tagName.style.visibility = "visible";
}

/**this is for to add a option element while selecting contact in add event page
   lvalue ==> is a contact id
   ltext ==> is a contact name
**/
function addOption(lvalue,ltext)	
{
	var optObj = document.createElement('OPTION')
	if (browser_ie) optObj.innerText = ltext;
        else if(browser_nn4 || browser_nn6) optObj.text = ltext;
	else optObj.text = ltext;
	optObj.value = lvalue;
	document.getElementById('parentid').appendChild(optObj);
}

function getdispDate(tempDate)
{
	var datefmt = document.EditView.dateformat.value;
        var dd = _2digit(parseInt(tempDate.getDate(),10));
        var mm = _2digit(parseInt(tempDate.getMonth(),10)+1);
	var yy = tempDate.getFullYear();
	if(datefmt == '%d-%m-%Y') return dd+'-'+mm+'-'+yy;
	else if(datefmt == '%m-%d-%Y') return mm+'-'+dd+'-'+yy;
	else return yy+'-'+mm+'-'+dd;
}
