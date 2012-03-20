{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}

<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<style type="text/css">
a.x {ldelim}
		color:black;
		text-align:center;
		text-decoration:none;
		padding:5px;
		font-weight:bold;
{rdelim}
	
a.x:hover {ldelim}
		color:#333333;
		text-decoration:underline;
		font-weight:bold;
{rdelim}

ul {ldelim}color:black;{rdelim}	 
	
.drag_Element{ldelim}
	position:relative;
	left:0px;
	top:0px;
	padding-left:5px;
	padding-right:5px;
	border:0px dashed #CCCCCC;
	visibility:hidden;
{rdelim}

#Drag_content{ldelim}
	position:absolute;
	left:0px;
	top:0px;
	padding-left:5px;
	padding-right:5px;
	background-color:#000066;
	color:#FFFFFF;
	border:1px solid #CCCCCC;
	font-weight:bold;
	display:none;
{rdelim}
</style>
<script>
 if(!e)
  window.captureEvents(Event.MOUSEMOVE);

//  window.onmousemove= displayCoords;
//  window.onclick = fnRevert;
  
   function displayCoords(event) 
	 {ldelim}
				var move_Element = document.getElementById('Drag_content').style;
				if(!event){ldelim}
						move_Element.left = e.pageX +'px' ;
						move_Element.top = e.pageY+10 + 'px';	
				{rdelim}
				else{ldelim}
						move_Element.left = event.clientX +'px' ;
					    move_Element.top = event.clientY+10 + 'px';	
				{rdelim}
	{rdelim}
  
	  function fnRevert(e)
	  {ldelim}
		  	if(e.button == 2){ldelim}
				document.getElementById('Drag_content').style.display = 'none';
				hideAll = false;
				parentId = "Head";
	    		parentName = "DEPARTMENTS";
			    childId ="NULL";
	    		childName = "NULL";
			{rdelim}
	{rdelim}

</script>

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<div align=center>
<br>
	
				{include file="SetMenu.tpl"}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'ico-roles.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_ROLES}" width="48" height="48" border=0 title="{$MOD.LBL_ROLES}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_ROLES}</b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_ROLE_DESCRIPTION}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$MOD.LBL_ROLE_HIERARCHY_TREE}</strong></td>
						<td class="small" align=right>&nbsp;</td>
					</tr>
					</table>

					<div id='RoleTreeFull'  onMouseMove="displayCoords(event)"> 
                			        {include file='RoleTree.tpl'}
		                	</div>

{*					<table border=0 cellspacing=0 cellpadding=20 width=100% >
					<tr>
						<td>
							<!-- Home node -->
							<table cellspacing=0 cellpadding=0 class="treeTable1"><tr><td><img src="images/treeHome.gif"></td><td class="small">&nbsp;<strong>Organization</strong></td><tr></table>
							<table cellspacing=0 cellpadding=0 class="treeTable1"><tr><td><img src="images/treeExSubNode.gif"><img src="images/treePaper.gif"></td><td class="small">Administrator</td><tr></table>
							<table cellspacing=0 cellpadding=0 class="treeTable1"><tr><td><img src="images/treeExSubNode.gif"><img src="images/treePaper.gif"></td><td class="small">CEO</td><tr></table>
							<table cellspacing=0 cellpadding=0 class="treeTable1"><tr><td><img src="images/treeNorthSouth.gif"><img src="images/treeExSubNode.gif"><img src="images/treePaper.gif"></td><td class="small">Manager - Sales</td><tr></table>
							<table cellspacing=0 cellpadding=0 class="treeTable1"><tr><td><img src="images/treeNorthSouth.gif"><img src="images/treeNorthSouth.gif"><img src="images/treeSubNode.gif"><img src="images/treePaper.gif"></td><td class="small">Asst Manager - Sales</td><tr></table>
							<table cellspacing=0 cellpadding=0 class="treeTable1"><tr><td><img src="images/treeNorthSouth.gif"><img src="images/treeExSubNode.gif"><img src="images/treePaper.gif"></td><td class="small">Manager - Products</td><tr></table>
							<table cellspacing=0 cellpadding=0 class="treeTable1"><tr><td><img src="images/treeNorthSouth.gif"><img src="images/treeNorthSouth.gif"><img src="images/treeSubNode.gif"><img src="images/treePaper.gif"></td><td class="small">Asst Manager - Products</td><tr></table>
							<table cellspacing=0 cellpadding=0 class="treeTable1"><tr><td><img src="images/treeNorthSouth.gif"><img src="images/treeSubNode.gif"><img src="images/treePaper.gif"></td><td class="small">Manager - Office</td><tr></table>
							<table cellspacing=0 cellpadding=0 class="treeTable1"><tr><td><img src="images/treeNorthSouth.gif"><img src="images/treeGap.gif"><img src="images/treeSubNode.gif"><img src="images/treePaper.gif"></td><td class="small">Asst Manager - Office</td><tr></table>
							<table cellspacing=0 cellpadding=0 class="treeTable1"><tr><td><img src="images/treeSubNode.gif"><img src="images/treePaper.gif"></td><td class="small">CFO</td><tr></table>
						</td>
					</tr>
					</table> *}
					
					
					
					
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr><td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
					</table>
				</td>
				</tr>
				</table>
			
			
			
			</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
		
	</div>

</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</table>
	<div id="Drag_content">&nbsp;</div>

<script language="javascript" type="text/javascript">
	var hideAll = false;
	var parentId = "";
	var parentName = "";
	var childId ="NULL";
	var childName = "NULL";

		
	
	 function get_parent_ID(obj,currObj)
	 {ldelim}
			var leftSide = findPosX(obj);
    			var topSide = findPosY(obj);
			var move_Element = document.getElementById('Drag_content');
		 	childName  = document.getElementById(currObj).innerHTML;
			childId = currObj;
			move_Element.innerHTML = childName;
			move_Element.style.left = leftSide + 15 + 'px';
			move_Element.style.top = topSide + 15+ 'px';
			move_Element.style.display = 'block';
			hideAll = true;	
	{rdelim}
	
	function put_child_ID(currObj)
	{ldelim}
			var move_Element = $('Drag_content');
	 		parentName  = $(currObj).innerHTML;
			parentId = currObj;
			move_Element.style.display = 'none';
			hideAll = false;	
			if(childId == "NULL")
			{ldelim}
//				alert("Please Select the Node");
				parentId = parentId.replace(/user_/gi,'');
				window.location.href="index.php?module=Settings&action=RoleDetailView&parenttab=Settings&roleid="+parentId;
			{rdelim}
			else
			{ldelim}
				childId = childId.replace(/user_/gi,'');
				parentId = parentId.replace(/user_/gi,'');
				new Ajax.Request(
  					'index.php',
				        {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
					        method: 'post',
					        postBody: 'module=Users&action=UsersAjax&file=RoleDragDrop&ajax=true&parentId='+parentId+'&childId='+childId,
						onComplete: function(response) {ldelim}
							if(response.responseText != alert_arr.ROLE_DRAG_ERR_MSG)
							{ldelim}
						                $('RoleTreeFull').innerHTML=response.responseText;
						                hideAll = false;
							        parentId = "";
						                parentName = "";
						                childId ="NULL";
								childName = "NULL";
						        {rdelim}
						        else
						                alert(response.responseText);
			                        {rdelim}
				        {rdelim}
				);
			{rdelim}
	{rdelim}

	function fnVisible(Obj)
	{ldelim}
			if(!hideAll)
				document.getElementById(Obj).style.visibility = 'visible';
	{rdelim}

	function fnInVisible(Obj)
	{ldelim}
		document.getElementById(Obj).style.visibility = 'hidden';
	{rdelim}

	


function showhide(argg,imgId)
{ldelim}
	var harray=argg.split(",");
	var harrlen = harray.length;	
	var i;
	for(i=0; i<harrlen; i++)
	{ldelim}
		var x=document.getElementById(harray[i]).style;
        	if (x.display=="none")
        	{ldelim}
           		x.display="block";
			document.getElementById(imgId).src="{'minus.gif'|@vtiger_imageurl:$THEME}";
         	{rdelim}
        	else
		{ldelim}
			x.display="none";
			document.getElementById(imgId).src="{'plus.gif'|@vtiger_imageurl:$THEME}";
		{rdelim}
	{rdelim}
{rdelim}



</script>
