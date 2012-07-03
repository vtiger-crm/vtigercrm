/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
var selTags=document.getElementsByTagName("SELECT")
function showSubMenu() {
	getObj("subMenuBg").style.display=getObj("subMenu").style.display="block"
	getObj("subMenuBg").style.top=getObj("subMenu").style.top=findPosY(getObj("showSubMenu"))+15
	getObj("subMenuBg").style.left=getObj("subMenu").style.left=findPosX(getObj("showSubMenu"))+getObj("showSubMenu").offsetWidth-getObj("subMenu").offsetWidth
	getObj("subMenuBg").style.width=getObj("subMenu").offsetWidth
	getObj("subMenuBg").style.height=getObj("subMenu").offsetHeight
	
	/*for (i=0;i<selTags.length;i++)
		selTags[i].style.visibility="hidden"*/
}
function hideSubMenu(ev) {
	if (!ev) var obj = window.event.srcElement;
	else var obj = ev.target;
	
	if (obj.id!="showSubMenu") {
		if (getObj("subMenu").style.display=="block") {
			getObj("subMenuBg").style.display="none"
			getObj("subMenu").style.display="none"
		}
	}
	
	for (i=0;i<selTags.length;i++)
		selTags[i].style.visibility="visible"
}
document.onclick=hideSubMenu;
