/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

//to merge a list of acounts with a template
function massMerge(module)
{
       var select_options  =  document.getElementsByName('selected_id');
	var x = select_options.length;
	var viewid =getviewId();		
	idstring = "";

	if(getObj('mergefile').value == '')
	{
	         alert(alert_arr.SELECT_TEMPLATE_TO_MERGE);
           	 return false;   
        }
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
        
	document.massdelete.action.value = 'Merge';
	document.getElementById('massdelete').action="index.php?module="+module+"&action=Merge&return_module="+module+"&return_action=index";
}

function mergeshowhide(argg)
{
    var x=document.getElementById(argg).style
    if (x.display=="none")
    {
        x.display="block"
   
    }
    else 
	{
		x.display="none"
	}
}

function mergehide(argg)
{
    var x=document.getElementById(argg);
	if (x != null) x.style.display = "none";
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
