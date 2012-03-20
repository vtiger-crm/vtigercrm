/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
// JavaScript Document
//Layer Function
/*  NEW SCRIPT FOR MENU SLIDE  */

var menu_i;
var menu_max;
var closeLimit;
var openLimit = 0; 

function fnSlide(obj,inner)
{
	var buff = document.getElementById(obj).width;
	closeLimit = buff.substring(0,buff.length-1);
	menu_max = eval(closeLimit);
	var tagName = document.getElementById(inner);
	document.getElementById(obj).style.width=0 + "%"; menu_i=0;
	if (tagName.style.display == 'none')
		fnexpanLay(obj,inner);
	else
		fncloseLay(obj,inner);
}

function fnexpanLay(obj,inner)
{
	var setText = eval(closeLimit) - 1;
	if (menu_i<=eval(closeLimit))
	{
		if (menu_i>setText){document.getElementById(inner).style.display='block';}
		document.getElementById(obj).style.width=menu_i+"%";
		setTimeout(function() { fnexpanLay(obj,inner); },5);
		menu_i=menu_i+1;   
	}
}

function fncloseLay(obj,inner)
{
	if (menu_max >= eval(openLimit))
	{
		if (menu_max<eval(closeLimit)){document.getElementById(inner).style.display='none';}
		document.getElementById(obj).style.width=menu_max +"%";
		setTimeout(function() { fncloseLay(obj,inner); }, 5);
		menu_max = menu_max -1;
	}
}

/*  NEW SCRIPT FOR MENU WIPE */

var wipe_i;
var wipe_max;
var closeLimit;
var openLimit = 0; 

function fnWipe(obj,inner)
{
	var buff = document.getElementById(inner).style.height;
	closeLimit = buff.substring(0,buff.length-2);
	wipe_max = eval(closeLimit);
	var tagName = document.getElementById(inner);
	document.getElementById(obj).style.height=0 + "px"; wipe_i=0;
	if (tagName.style.display == 'none')
		fnWipeLay(obj,inner);
	else
		fnUnWipeLay(obj,inner);
}

function fnWipeLay(obj,inner)
{
	var setText = eval(closeLimit) - 1;
	if (wipe_i<=eval(closeLimit))
	{
		if (wipe_i>setText){document.getElementById(inner).style.display='block';}
		document.getElementById(obj).style.height=wipe_i+"px";
		setTimeout(function() { fnWipeLay(obj,inner); },5);
		wipe_i = wipe_i + 5;   
	}
}

function fnUnWipeLay(obj,inner)
{
	if (wipe_max >= eval(openLimit))
	{
		if (wipe_max<eval(closeLimit)){document.getElementById(inner).style.display='none';}
		document.getElementById(obj).style.height = wipe_max + "px";
		setTimeout(function() { fnUnWipeLay(obj,inner); }, 5);
		wipe_max = wipe_max - 5;
	}
}



function fnSlide1(obj,inner)
{
  var buff = document.getElementById(obj).width;
  closeLimit = buff.substring(0,buff.length);
  menu_max = eval(closeLimit);
  var tagName = document.getElementById(inner);
  document.getElementById(obj).style.width=0 + "px"; menu_i=0;
  if (tagName.style.display == 'none')
	  fnexpanLay1(obj,inner);
  else
	fncloseLay1(obj,inner);
 }

function fnexpanLay1(obj,inner)
{
     document.getElementById(obj).style.display = 'block';
   var setText = eval(closeLimit) - 1;
   if (menu_i<=eval(closeLimit))
   {
	    if (menu_i>setText){document.getElementById(inner).style.display='block';}
       document.getElementById(obj).style.width=menu_i + "px";
	   setTimeout(function() { fnexpanLay1(obj,inner); },5);
        menu_i=menu_i+14;   
   }
}

 function fncloseLay1(obj,inner)
{
  if (menu_max >= eval(openLimit))
   {
	    if (menu_max<eval(closeLimit)){document.getElementById(inner).style.display='none';}
       document.getElementById(obj).style.width=menu_max +"px";
	  setTimeout(function() { fncloseLay1(obj,inner); }, 5);
       menu_max = menu_max -14;
   }
}
