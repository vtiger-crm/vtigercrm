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
<script language="JavaScript" type="text/javascript" src="modules/Rss/Rss.js"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script>
{literal}

function GetRssFeedList(id)
{
	$("status").style.display="inline";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Rss&action=RssAjax&file=ListView&directmode=ajax&record='+id,
                        onComplete: function(response) {
                                $("status").style.display="none";
				$("rssfeedscont").innerHTML=response.responseText;
                        }
                }
        );
}

function DeleteRssFeeds(id)
{
   if(id != '')	
   {
	{/literal}
        if(confirm('{$APP.DELETE_RSSFEED_CONFIRMATION}'))
        {literal}
	{	
		show('status');	
		var feed = 'feed_'+id;
		$(feed).parentNode.removeChild($(feed));
		new Ajax.Request(
                	'index.php',
        	        {queue: {position: 'end', scope: 'command'},
                        	method: 'post',
	                        postBody: 'module=Rss&return_module=Rss&action=RssAjax&file=Delete&directmode=ajax&record='+id,
        	                onComplete: function(response) {
	        	                $("status").style.display="none";
                                	$("rssfeedscont").innerHTML=response.responseText;
					$("mysite").src = '';
					$("rsstitle").innerHTML = "&nbsp";
                        	}
                	}
        	);
	}
   }
   else
	alert(alert_arr.LBL_NO_FEEDS_SELECTED);	     	
}
function SaveRssFeeds()
{
	$("status").style.display="inline";
	var rssurl = $('rssurl').value;
	rssurl = rssurl.replace(/&/gi,"##amp##");
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Rss&action=RssAjax&file=Popup&directmode=ajax&rssurl='+rssurl, 
			onComplete: function(response) {
	
                                        $("status").style.display="none";
					if(isNaN(parseInt(response.responseText)))
        				{
				               var rrt = response.responseText;
						$("temp_alert").innerHTML = rrt;
						removeHTMLTags();	
				                $('rssurl').value = '';
					}
					else
        				{
				                GetRssFeedList(response.responseText);
				                getrssfolders();
				                $('rssurl').value = '';
				                $('PopupLay').hide();
        				}
                                }
                        }
                );
}
{/literal}
</script>

<!-- Contents -->
{include file="Buttons_List1.tpl"}
<div id="temp_alert" style="display:none"></div>
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
<tr>
	<td valign=top align=right width=8><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" valign="top" width="100%" align=center >	
		
			<!-- RSS Reader UI Starts here--><br>
				<table width="100%"  border="0" cellspacing="0" cellpadding="5" class="mailClient mailClientBg">
				<tr>
					<td align=left>
					
						<table width="100%"  border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width=95% align=left><img src='{'rssroot.gif'|@vtiger_imageurl:$THEME}' align='absmiddle'/><a href="javascript:;" onClick="fnvshobj(this,'PopupLay');$('rssurl').focus();" title='{$APP.LBL_ADD_RSS_FEEDS}'>{$MOD.LBL_ADD_RSS_FEED}</a></td>
							<td  class="componentName" nowrap>{$MOD.LBL_VTIGER_RSS_READER}</td>
						</tr>
						<tr>
							<td colspan="2">
								<table border=0 cellspacing=0 cellpadding=2 width=100%>
								<tr>
									<td width=30% valign=top>
									<!-- Feed Folders -->
										<table border=0 cellspacing=0 cellpadding=0 width=100%>
										<tr><td class="small mailSubHeader" height="25"><b>{$MOD.LBL_FEED_SOURCES}</b></td></tr>
										<tr><td class="hdrNameBg" bgcolor="#fff" height=225><div id="rssfolders" style="height:100%;overflow:auto;">{$RSSFEEDS}</div></td></tr>
										</table>
									</td>
									<td width=1%>&nbsp;</td>
									<td width=69% valign=top>
									<!-- Feed Header List -->
										<table border=0 cellspacing=0 cellpadding=0 width=100%>
										<tr>
											<td><div id="rssfeedscont">
											{include file='RssFeeds.tpl'}	
											</div>
											</td>
										</tr>
										</table>
									</td>
								</tr>
								</table>
								
							</td>
						</tr>
						<tr>		
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td height="5"></td>
						</tr>
						<tr>
							
							<td colspan="3" class="mailSubHeader" id="rsstitle">&nbsp;</td>
						</tr>
						<tr>
							<!-- RSS Display -->
							<td colspan="3" style="padding:2px">
							<iframe width="100%" height="250" frameborder="0" id="mysite" scrolling="auto" marginheight="0" marginwidth="0" style="background-color:#FFFFFF;"></iframe>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			<!-- RSS Reader UI ends here -->
	</td>
	<td valign=top align=right width=8><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>			
	</tr>
	</table>
	
	
	
	<div id="PopupLay" class="layerPopup">
	<form onSubmit="SaveRssFeeds(); return false;">
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
	<tr>
	<td class="layerPopupHeading" align="left"><img src="{'rssroot.gif'|@vtiger_imageurl:$THEME}" width="24" height="22" align="absmiddle" />&nbsp;{$MOD.LBL_ADD_RSS_FEED}</td>
	<td align="right"><a href="javascript:fninvsh('PopupLay');"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  align="absmiddle" /></a></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
	<tr>
		<td class=small >
		
			<!-- popup specific content fill in starts -->

			<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
				<tr>
					<td align="right" width="25%"><b>{$MOD.LBL_FEED}</b></td>
					<td align="left" width="75%"><input type="text" id="rssurl" class="txtBox" value=""/></td>
				</tr>
			</table>
			<!-- popup specific content fill in ends -->
		
		</td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
	<tr>
	<td align="center">
	<input type="submit" name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="crmbutton small save"/>&nbsp;&nbsp;
	</td>
	</tr>
	</table>
	</form>
	</div>

<script type="text/javascript" language="Javascript">
function makedefaultRss(id)
{ldelim}
	if(id != '')
	{ldelim}
		$("status").style.display="inline";
		new Ajax.Request(
                	'index.php',
	                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
        	                method: 'post',
                	        postBody:'module=Rss&action=RssAjax&file=Popup&directmode=ajax&record='+id, 
                        	onComplete: function(response) {ldelim}
                                	$("status").style.display="none";
        				getrssfolders();
        	               {rdelim}
                	{rdelim}
        	);
	{rdelim}
{rdelim}
function getrssfolders()
{ldelim}
	new Ajax.Request(
        	'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                	method: 'post',
                        postBody:'module=Rss&action=RssAjax&file=ListView&folders=true',
			onComplete: function(response) {ldelim}
                        		$("status").style.display="none";
					$("rssfolders").innerHTML=response.responseText;
                               {rdelim}
                        {rdelim}
                );
{rdelim}


function removeHTMLTags()
{ldelim}
 	if(document.getElementById && document.getElementById("temp_alert"))
	{ldelim}
 		var strInputCode = document.getElementById("temp_alert").innerHTML;
 		var strTagStrippedText = strInputCode.replace(/<\/?[^>]+(>|$)/g, "");
 		alert("Output Message:\n" + strTagStrippedText);	
 	{rdelim}	
{rdelim}


</script>
