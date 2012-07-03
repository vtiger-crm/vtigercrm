/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

function star(id, starred)
{
    location.href = "index.php?module=Rss&action=Star&record="+id+"&starred="+starred;

    var elem = document.getElementById("star-"+id);
    if(elem.src.indexOf("onstar.gif") != -1) {
        elem.src = "themes/images/offstar.gif";
    }else {
        elem.src = "themes/images/onstar.gif";
    }
}
function getRequest() {
        if ( !httpRequest ) {
            httpRequest = new XMLHttpRequest();    
        }
        return httpRequest;
    }
function makeRequest(targetUrl) {
        var httpRequest = getRequest();
        httpRequest.open("GET", targetUrl, false, false, false);
        httpRequest.send("");
        switch ( httpRequest.status ) {
            case 200:
                return httpRequest.responseText;
            break;
            default:
                alert(alert_arr.PROBLEM_ACCESSSING_URL+targetUrl+alert_arr.CODE+httpRequest.status);
                return null;
            break;
        }       
    }
function verify_data(form) {
        var isError = false;
        var errorMessage = "";
        if (trim(form.rssurl.value) == "") {
                isError = true;
                errorMessage += "\nRSS Feed URL";
        }
        // Here we decide whether to submit the form.
        if (isError == true) {
                alert(alert_arr.MISSING_REQUIRED_FIELDS + errorMessage);
                return false;
        }
        return true;
}
function display(url,id)
{
	document.getElementById('rsstitle').innerHTML = document.getElementById(id).innerHTML;
	document.getElementById('mysite').src = url;
}
