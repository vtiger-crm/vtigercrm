/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
function searchshowhide(argg,argg2)
{
	var ele_x = document.getElementById(argg);
	var ele_y = document.getElementById(argg2);
	var x, y;
    if (ele_x != null) x = ele_x.style;
    if (ele_y != null) y = ele_y.style;
    if (x != null && x.display=="none" && (y == null || y.display=="none"))
    {
        x.display="block"
   
    }
    else {
	    if (y != null) y.display="none";
        if (x != null) x.display="none";
    }
}

function searchhide(argg,argg2)
{
	var ele_x = document.getElementById(argg);
	var ele_y = document.getElementById(argg2);
	var x, y;
    if (ele_x != null) x = ele_x.style;
    if (ele_y != null) y = ele_y.style;
	if (y != null) y.display="none";
    if (x != null) x.display="none";
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
