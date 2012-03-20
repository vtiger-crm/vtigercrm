/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/


function fetchAddSite(id)
{
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Portal&action=PortalAjax&file=Popup&record='+id,
			onComplete: function(response) {
				$("status").style.display="none";
				$('editportal_cont').innerHTML = response.responseText;
			}
		}
	);
}

function fetchContents(mode)
{
	// Reloading the window is better, If not reloaded ... mysitesArray variable needs to be updated
	// using eval method on javascript. 
	if(mode == 'data') {
		window.location.href = 'index.php?module=Portal&action=ListView&parenttab=Tools';
		return;
	}
	$("status").style.display="inline";
	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody:'action=PortalAjax&mode=ajax&module=Portal&file=ListView&datamode='+mode,
                        onComplete: function(response) {
                                $("status").style.display="none";
                                $('portalcont').innerHTML = response.responseText;
                        }
                }
        );
}
function DeleteSite(id)
{
	if(confirm(alert_arr.SURE_TO_DELETE))
	{
		$("status").style.display="inline";
		new Ajax.Request(
          	      'index.php',
                	{queue: {position: 'end', scope: 'command'},
                        	method: 'post',
	                        postBody:'action=PortalAjax&mode=ajax&file=Delete&module=Portal&record='+id,
        	                onComplete: function(response) {
                	                $("status").style.display="none";
                        	        $('portalcont').innerHTML = response.responseText;
                        	}
                	}
        	);
	}
}
function SaveSite(id)
{
	if ($('portalurl').value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0) {
		alert(alert_arr.SITEURL_CANNOT_BE_EMPTY)
		return false;
	}
	if ($('portalname').value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0) {
		alert(alert_arr.SITENAME_CANNOT_BE_EMPTY)
		return false;
	}
	Effect.Puff('orgLay');	
	$("status").style.display="inline";
	var portalurl = document.getElementById('portalurl').value;
	var portalurl = portalurl.replace(/&/g, "#$#$#");
	var portalname = document.getElementById('portalname').value;
        new Ajax.Request(
        	'index.php',
                {queue: {position: 'end', scope: 'command'},
                	method: 'post',
                        postBody:'action=PortalAjax&mode=ajax&file=Save&module=Portal&portalname='+portalname+'&portalurl='+portalurl+'&record='+id,
                        onComplete: function(response) {
					if(response.responseText.indexOf(":#:FAILURE") > -1)
					{
						alert(alert_arr.VALID_DATA)
					}else
					{
						$('portalcont').innerHTML = response.responseText;
					}
					$("status").style.display="none";
                        }
                }
	);
}
function setSite(oUrllist)
{
	//var url = oUrllist.options[oUrllist.options.selectedIndex].value;
	var id = oUrllist.options[oUrllist.options.selectedIndex].value;
	document.getElementById('locatesite').src = mysitesArray[id];
}
//added as an enhancement to set default value
function defaultMysites(oSelectlist)
{
	var id = $("urllist").value;
	$("status").style.display="block";
	 new Ajax.Request(
        	'index.php',
                {queue: {position: 'end', scope: 'command'},
                	method: 'post',
                        postBody:'action=PortalAjax&mode=ajax&file=Save&module=Portal&check=true&passing_var='+id,
                        onComplete: function(response) {
					//alert(response.responseText);
					$("status").style.display="none";
                        }
                }
	);
	
}

var oRegex = new Object() ;
oRegex.UriProtocol = new RegExp('') ;
oRegex.UriProtocol.compile( '^(((http|https|ftp|news):\/\/)|mailto:)', 'gi' ) ;

oRegex.UrlOnChangeProtocol = new RegExp('') ;
oRegex.UrlOnChangeProtocol.compile( '^(http|https|ftp|news)://(?=.)', 'gi' ) ;

function OnUrlChange()
{
    var sUrl;
    var sProtocol;   
                   
          sUrl=document.getElementById("portalurl").value ;
          sProtocol=oRegex.UrlOnChangeProtocol.exec( sUrl ) ;
          if ( sProtocol )
          {
             sUrl = sUrl.substr( sProtocol[0].length ) ;
             document.getElementById("portalurl").value = sUrl ;
          }

        
}
