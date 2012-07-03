/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

/**
 * Utility classes for prototype 1.4
 */
// Based on https://gist.github.com/145466 (MIT) - modified to suit Prototype v1.4

vtiger_help_jsonp = Class.create();
vtiger_help_jsonp.prototype = Object.extend(Ajax.Base, (function() {
    var id = 0;
    head = document.getElementsByTagName('head')[0];
    return {
        initialize: function(url, options) {
            this.options = options;
            if (!this.options.callbackName) {
                this.options.callbackName = 'callback';
            }
            this.request(url);
        },
        request: function(url) {
            var callbackName = '_prototypeJSONPCallback_' + (id++),
            self = this,
            script;

            url += (url.indexOf('?') != -1 ? '&': '?') + this.options.parameters +
            '&' + this.options.callbackName + '=' + encodeURIComponent(callbackName);

            window[callbackName] = function(json) {
                try {
                    script.remove();
                    script = null;
                } catch(error) { /* IE might complain about failure */ }

                window[callbackName] = undefined;
                if (self.options.onComplete) {
                    self.options.onComplete.call(self, json);
                }
            }
            script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = url;
            head.appendChild(script);
        }
    };
})());

// Based on http://ejohn.org/blog/javascript-micro-templating/
var vtiger_help_tmpl_cache = [];
vtiger_help_tmpl = function(str, data) {
    // Figure out if we're getting a template, or if we need to
    // load the template - and be sure to cache the result.
    var fn = !/\W/.test(str) ?
    vtiger_help_tmpl_cache[str] = vtiger_help_tmpl_cache[str] ||
    vtiger_help_tmpl(document.getElementById(str).innerHTML) :

    // Generate a reusable function that will serve as a template
    // generator (and which will be cached).
    new Function("obj",
        "var p=[],print=function(){p.push.apply(p,arguments);};" +

        // Introduce the data as local variables using with(){}
        "with(obj){p.push('" +

        // Convert the template into pure JavaScript
        str
        .replace(/[\r\t\n]/g, " ")
        .split("<%").join("\t")
        .replace(/((^|%>)[^\t]*)'/g, "$1\r")
        .replace(/\t=(.*?)%>/g, "',$1,'")
        .split("\t").join("');")
        .split("%>").join("p.push('")
        .split("\r").join("\\'")
        + "');}return p.join('');");

    // Provide some basic currying to the user
    return data ? fn(data) : fn;
};

/**
 * Vtiger Help Controller
 */
vtiger_help_controller = function() {

    /**
	 * API for quick help
	 */
    this.apiURL = function() {
        return 'http://help.vtiger.com/helpmenow/api.php';
    }

    /**
	 * Runtime variables
	 */
    this.runtimeVars = function(uri) {

        if (typeof uri == 'undefined') uri = self.location.href;

        var userid = $('_my_preferences_')? $('_my_preferences_').getAttribute('href').match(/record=([^&]+)/)[1] : 0;
        var module = uri.match(/module=([^&]+)/)[1];
        var action = uri.match(/action=([^&]+)/)[1];
        if(action=='ListView'){
            action='index';
        }
        return {
            USERID: userid,
            MODULE:module,
            ACTION:action
        };
    }

    /**
	 * Extract keyword based on context for getting help information
	 */
    this.extractKeyword = function(uri) {
        // Build the keyword (module-action) pair
        var runvars = this.runtimeVars(uri);
        return runvars['MODULE'] + '-' + runvars['ACTION'];
    }

    /**
	 * Initialize the DOM
	 */
    this.init = function() {
        this.initHandler();
        this.css();
        this.popup();
        return this;
    }
    /**
     * Initialize the handler to trigger help.
     */
    this.initHandler = function() {
        var linkDiv = document.createElement('div');
        var link = document.createElement('a');
        document.body.appendChild(linkDiv);
        linkDiv.appendChild(link);
        link.id='helpLink';
        linkDiv.id='helpButton';
        link.setAttribute('class', 'helpTipsHandler');
        link.style.visibility = 'visible';
        //link.setAttribute('onclick', "vtiger_help($('helpTipsHandlerPin'));");
        link.onclick=function(){
            vtiger_help($('helpTipsHandlerPin'));
            return false;
        }
        var themePath = vtlib_vtiger_imageurl(gVTTheme);
        link.setAttribute('href',"javascript:void(0);");
        link.innerHTML="<span id='helpTipsHandlerPin'>&nbsp;</span><img src='"+themePath+"/help_sidebar.png' border=0 align='absmiddle' title="+getTranslatedString("LBL_HELP_TITLE")+">";

    }


    /**
	 * Widget styling...
	 */
    this.css = function() {
        if ($('vtigerHelpStyling')) return;
    /* BETTER TO BE MOVED INTO themes style.css */
    /*
        var rules = '.vtigerHelpPopupLay { font-size:12px; border: 1px solid #F5F5F5; }' +
                '.vtigerHelpPopupLay hr { padding: 0; border-top: 0; border-left: 0; border-right: 0; }' +
                '.vtigerHelpPopupLay .button { background: #CCC; color: black; padding: 2px 5px; text-decoration: none; }' +
                '.vtigerHelpPopupLay .button:hover { background: #F5F5F5; }' +
                '.vtigerHelpPopupLay .helppages { border-bottom: 1px solid #F5F5F5; padding-bottom: 5px; }' +
                '.vtigerHelpPopupLay .helppage .header { font-weight: bold; color: #0070BA; }' +
                '.vtigerHelpPopupLay .helppage .content { }'+
                '.vtigerHelpPopupLay .helppage .footer { color: gray; text-align: right; padding: 0 5px; }'+
                '.vtigerHelpPopupLay .helppage-tr {display: none;}'+
                '.vtigerHelpPopupLay .goog-te-sectional-gadget-link-text { font-weight: normal; color: #CCC; }'+
                '.vtigerHelpPopupLay #vtigerHelpGTranslateEl { bottom: 0; }';

        var style = document.createElement('style');
        style.id = 'vtigerHelpStyling';
        style.type = 'text/css';
        style.appendChild(document.createTextNode(rules));
        document.head.appendChild(style);
        */
    }

    /**
     * Widget popup
     */
    this.popup = function() {
        if (!$('vtigerHelpPopupLay')) {
            var vtigerHelpPopupLay = document.createElement('div');
            vtigerHelpPopupLay.id = 'vtigerHelpPopupLay';
            vtigerHelpPopupLay.className = 'lvtCol fixedLay1 vtigerHelpPopupLay';
            vtigerHelpPopupLay.style.display = 'none';
            vtigerHelpPopupLay.style.width = '310px';
            vtigerHelpPopupLay.style.bottom = '2px';
            vtigerHelpPopupLay.style.fontWeight = 'normal';
            vtigerHelpPopupLay.style.visibility = 'visible';
            vtigerHelpPopupLay.style.margin="-1% 0 0 0";
            vtigerHelpPopupLay.innerHTML = '<div id="vtigerHelpPopupLayContainer"></div><div id="vtigerHelpGTranslateEl" ></div>';

            document.getElementById('helpButton').appendChild(vtigerHelpPopupLay);
        }

        return $('vtigerHelpPopupLay');
    }

    /**
     * Widget popup container
     */
    this.popupContainer = function() {
        this.popup();
        return $('vtigerHelpPopupLayContainer');
    }

    /**
     * Find closest parent having the given className
     */
    this.closest = function(node, className) {
        while (node != document.body) {
            node = node.parentNode;
            if (node.className == className) break;
        }
        return node;
    }

    /**
     * Set / Get data attribute value of the node.
     */
    this.data = function(node, key, value) {
        if (typeof value == 'undefined') {
            return node.getAttribute('data-'+key);
        }
        node.setAttribute('data-'+key, value);
    }

    /**
     * Add new help page (hidding existing ones)
     */
    this.pushPage = function(node) {
        var pages = this.closest(node, 'helppages');

        for (var index=0, len=pages.children.length; index < len; ++index) {
            pages.children[index].style.display = 'none';
        }

        var page = document.createElement('div');
        page.className = 'helppage';
        pages.appendChild(page);

        return page;
    }

    /**
     * Remove last help page.
     */
    this.popPage = function(node) {
        var pages = this.closest(node, 'helppages');

        var lastPage = null;
        var totalPage = pages.children.length;
        if (totalPage > 0) {
            lastPage = pages.children[pages.children.length-1];
            pages.removeChild(lastPage);

            if (totalPage > 1) {
                lastPage = pages.children[pages.children.length-1];
                lastPage.style.display = 'block';
            }
        }

        return lastPage;
    }

    /**
     * Handle helpMeNow request
     */
    this.helpMeNow = function(obj, title, uri, embed, callback) {
        var thisInstance = this;

        if (typeof uri == 'undefined') {
            uri = self.location.href;
        }

        if (typeof title == 'undefined') {
            var handler = this.closest(obj, 'helpTipsHandler');
            if (handler) title = handler.innerHTML;
        }

        // Home page / POST method visit
        if (uri.indexOf('?') == -1) {
            return;
        }

        var q = this.extractKeyword(uri);
        var qtype = 'partial';

        // Transform the keyword for tag-search
        var m = q.match(/([^-]+)/);
        switch (m[1]) {
            case 'Home': case 'Settings':
            case 'Administration':
                break;
            default:
                // For other modules pull generic & specific help-topics
                q = q.replace(/([^-]+)-(.*)/, '($1|*)[-]$2');
                qtype = 'regex';
        }

        var v = $('_vtiger_product_version_')? $('_vtiger_product_version_').innerHTML : '';

        $('status').style.display = 'inline';

        new vtiger_help_jsonp(this.apiURL(), {
            parameters: 'operation=find&q=' + encodeURIComponent(q) + '&qtype=' + encodeURIComponent(qtype) + '&v=' + encodeURIComponent(v),
            onComplete: function(data) {
                $('status').style.display = 'none';

                var records = (data.success) ? data.result.records: false;
                if (records) {
                    if (typeof embed == 'undefined') {
                        var container = thisInstance.popupContainer();
                        container.innerHTML = thisInstance.recordsUI(records, title);
                        // fnvshobj(obj, "vtigerHelpPopupLay");
                        var popUpLay=document.getElementById('vtigerHelpPopupLayContainer');
                        var popUpLayBlock=document.getElementById('vtigerHelpPopupLay');
                        popUpLay.innerHTML=container.innerHTML;
                        document.getElementById('helpLink').style.display='none';
                        popUpLayBlock.style.display='block';
                        //Drag funtion for help
                        Drag.init(document.getElementById('helpHandle'),document.getElementById('vtigerHelpPopupLay'),-1100,0,-100,1000);
                        // Trigger translation service
                        thisInstance.translate();
                    } else {
                        obj.style.display = 'block';
                        obj.innerHTML = thisInstance.recordsUI(records, '', true);
                    }
                }

                if (typeof callback == 'function') {
                    callback(data.success);
                }
            }
        });
    }

    /**
     * Handle helpMeNow navigation request.
     */
    this.helpMeNowNavigate = function(obj) {
        var thisInstance = this;
        var parentid = this.data(obj, 'parentid');
        var id = this.data(obj, 'id');
        var navtype = this.data(obj, 'navtype');

        // TODO Optimize loads: Instead of pop can we enable page navigations
        // by toggling the display style? Load the next page if not yet done.
        if (navtype == 'prev') {
            this.popPage(obj);
            return;
        }

        var container = this.pushPage(obj);
        container.innerHTML = '<img src="themes/images/vtbusy.gif" border="0">';
        new vtiger_help_jsonp(this.apiURL(), {
            parameters: 'operation=find&id=' + encodeURIComponent(id),
            onComplete: function(data) {
                if (data.success) {
                    container.innerHTML = thisInstance.recordUI(data.result.record, parentid);

                    // Trigger translation service
                    thisInstance.translate();
                }
            }
        });
    }

    /**
     * Handle multi-record display.
     */
    this.recordsUI = function(records, headerLabel, skipHeaderFooter) {
        if (typeof headerLabel == 'undefined') headerLabel = '';
        if (typeof skipHeaderFooter == 'undefined') skipHeaderFooter = false;

        // NOTE update recordUI API below to match the helppage node-structure...
        var tpl =
        '<% if (SKIP_HEADER_FOOTER != true) {%><div id="helpHandle"  style="cursor:move;">'+
        '<table class="layerHeadingULine"  border="0" cellpadding="0" cellspacing="0" width="100%" style="padding:3px 0px 0px 0px;height:30px;">' +
        '	<tr valign=top>' +
        '		<td align="left"   valign="middle"><b><label  style="margin-left:6px;">'+getTranslatedString("LBL_HELP_TITLE")+'</label><b></td>' +
        '		<td align="center" valign="middle"></td>' +
        '		<td align="right"  valign="middle">' +
        '                       <a  href="https://help.vtiger.com/faqs" target="_blank"><img src="themes/images/faq.png" align="absmiddle" border="0" class="links" title='+getTranslatedString("LBL_FAQ_TITLE")+'></a>' +
        '                       <a  href="https://wiki.vtiger.com" target="_blank"><img src="themes/images/manuals.png" align="absmiddle" border="0"  class="links" title='+getTranslatedString("LBL_WIKI_TITLE")+'></a>' +
        '                       <a href="http://www.vtiger.com/crm/player/" target="_blank"><img src="themes/images/videos.png" align="absmiddle" border="0"  class="links"  title='+getTranslatedString("LBL_VIDEO_TITLE")+'></a>' +
        '			<a style="margin-right: 6px;margin-left:11px" href="javascript:;" onclick="closePopup()"><img src="themes/images/help_close_black.png" align="absmiddle" border="0" width="13px" height="13px" style="margin-top:3px;" title='+getTranslatedString("LBL_CLOSE_TITLE")+'></a>' +
        '		</td>' +
        '	</tr>' +

        '<% } %>' +
        '</table>' +
        '</div>'+
        '<div id="contentOfHelp"  style="padding:6px;">'+
        '	<% if (RECORDS.length == 0) {%>' +
        '	<div class="helppage">' +
        '		No quick help found. Try <a href="http://wiki.vtiger.com" target="_blank">wiki.vtiger.com</a>' +
        ' 		<div class="helppage-tr"></div>'+
        '	</div>' +
        '	<% } else { %>' +
        '   <%  for (var index=0, len=RECORDS.length; index < len; ++index) { var RECORD = RECORDS[index]; %>' +
        '	<div class="helppages">' +
        '		<div class="helppage">'+
        '                       <% if (RECORDS.length == 1) { %>'+
        '				 <div class="header" ><%= RECORD.title %></div>'+
        '				<div class="content" style="display: block;margin-left:0px;"><%= fnDataMerge(RECORD.content, runtimeVars) %>'+
        '			<% } else { %>'+
        '				 <div class="header" onclick="vtiger_help_toggle(this)" style="cursor:pointer"><label class="helpArrow" ><img src="themes/images/arrow_right_black.png" width="6px" heigth="8px" class="arrowRight" ></img></label><%= RECORD.title %></div>'+
        '                                <div class="content" style="display: none;"><%= fnDataMerge(RECORD.content, runtimeVars) %>'+
        '                       <% } %>'+
        '			<div class="footer"><% if (RECORD.nextid > 0) {%> <a class="button" href="javascript:;" onclick="vtiger_help_nav(this);" data-parentid="<%= RECORD.id %>" data-id="<%= RECORD.nextid %>">Next</a> <% }%></div></div>'+
        '			<div class="helppage-tr"></div>'+
        '               </div>' +
        '       </div>' +
        '   <%   } %>' +
        '	<% } %>' +
        '</div>'+
        '</div>';

        return vtiger_help_tmpl(tpl, {
            SKIP_HEADER_FOOTER: skipHeaderFooter,
            HEADER_LABEL: headerLabel,
            RECORDS: records,
            fnDataMerge: this.dataMerge,
            runtimeVars: this.runtimeVars()
        });
    }

    /**
     * Merge data with given context (variable map)
     */
    this.dataMerge = function (data, context) {
        for (var k in context) {
            // Replace Meta variables {$VARIABLE} | %7B$VARIABLE%7D
            // TODO Escape regex - k
            data = data.replace(new RegExp("{\\$"+k+"}", "ig"), context[k]).replace(new RegExp("%7B\\$"+k+"%7D", "ig"), context[k]);
        }
        return data;
    }

    /**
     * Handle single record display
     */
    this.recordUI = function(record, parentid) {
        var tpl =
        '	<div class="helppage">' +
        '		 <div class="header" onclick="vtiger_help_toggle(this)" style="cursor:pointer"><label class="helpArrow" ><img src="themes/images/arrow_down_black.png" width="8px" heigth="6px" class="arrowDown" ></img></label><%= RECORD.title %></div>'+
        '		<div class="content"><%= fnDataMerge(RECORD.content, runtimeVars) %>'+
        '		<div class="footer">'+
        '		<% if (PARENTID) {%> <a class="button" href="javascript:;" onclick="vtiger_help_nav(this);" data-id="<%= PARENTID %>" data-navtype="prev">Prev</a> <%}%>'+
        '		<% if (RECORD.nextid > 0) {%> <a class="button" href="javascript:;" onclick="vtiger_help_nav(this);" data-parentid="<%= PARENTID %>" data-id="<%= RECORD.nextid %>" data-navtype="next">Next</a> <% }%>'+
        '	</div></div>'+
        '	<div class="helppage-tr"></div>'+
        '   </div>';

        return vtiger_help_tmpl(tpl, {
            RECORD: record,
            PARENTID: parentid,
            fnDataMerge: this.dataMerge,
            runtimeVars: this.runtimeVars()
        });
    },

    /**
     * Translate the helppage content using Google Translate Gadget.
     * Reference: http://translate.google.com/translate_tools
     */
    this.translatorInstance = false;
    this.translate = function(containerid) {
        var thisInstance = this;
        var singleton = true;
        if (typeof containerid == 'undefined') {
            containerid = 'vtigerHelpGTranslateEl';
        } else {
            singleton = false;
        }

        var lang = this.currentLanguage()[0];
        if (lang == 'en'){
            document.getElementById('vtigerHelpGTranslateEl').style.display='none';
            return;
        }
        function tr() {

            // Subsequent entries
            if (singleton && thisInstance.translatorInstance) {
                thisInstance.translatorInstance.update();
                return;
            }

            var trInstance = new google.translate.SectionalElement({
                sectionalNodeClassName: 'helppage',
                controlNodeClassName: 'helppage-tr',
                background: 'inherit'
            }, containerid);

            /**
	     * Singleton support does not work as expected.
	     * Google Section Translate is unique for a page.
	     */
            if (singleton) {
                thisInstance.translatorInstance = trInstance;
            }

        }

        if (typeof google == 'undefined' || typeof google.translate == 'undefined' || typeof google.translate.SectionalElement == 'undefined') {
            var url = '//translate.google.com/translate_a/element.js';
            new vtiger_help_jsonp(url, {
                parameters: 'ug=section&hl=' + encodeURIComponent(lang),
                callbackName: 'cb',
                onComplete: tr
            });
        } else {
            tr();
        }
    }

    /**
     * Determine the current language of user.
     */
    this._language = false;
    this.currentLanguage = function() {
        if (!this._language) {
            this._language = 'en_us';
            if ($('_current_language_')) {
                this._language = $('_current_language_').getAttribute('src').match(/include\/js\/([^.]+)/)[1];
            }
        }
        return this._language.split('_');
    }
}

/**
 * Singleton for use
 */
vtiger_help_controller_singleton =  {
    instance: false,
    get: function() {
        if (!vtiger_help_controller_singleton.instance) {
            vtiger_help_controller_singleton.instance = new vtiger_help_controller();
            vtiger_help_controller_singleton.instance.init();
        }
        return vtiger_help_controller_singleton.instance;
    }
}

/**
 * Export the functions.
 */
function vtiger_help(obj, title, uri) {
    vtiger_help_controller_singleton.get().helpMeNow(obj, title, uri);
    document.getElementById('vtigerHelpPopupLay').style.left= '0px';
    document.getElementById('vtigerHelpPopupLay').style.top= '-2px';
}

function vtiger_help_nav(obj) {
    vtiger_help_controller_singleton.get().helpMeNowNavigate(obj);
}
/**
 *funtion to close the popup
 */
function closePopup(){
    document.getElementById('vtigerHelpPopupLay').style.display='none';
    document.getElementById('helpLink').style.display='block';

}

/**
 *funtion to have a toggle action in the popup
 */

function vtiger_help_toggle(header){
    var heading=header.parentNode;
    var arrow = vtlib_getElementsByClassName(header,'helpArrow','label');
    var content =vtlib_getElementsByClassName(heading,'content','div');
    var visible=content[0].style.display;
    if(visible=='none'){
        content[0].style.display="block";
        arrow[0].innerHTML="<img src='themes/images/arrow_down_black.png' class='arrowDown' width='8px' heigth='6px'></img>";
    }
    else{
        content[0].style.display="none";
        arrow[0].innerHTML="<img src='themes/images/arrow_right_black.png' class='arrowRight' width='6px' heigth='8px'></img>";
    }
}

/*
 * getElementsByClassName fix for I.E 8
 */
function vtiger_help_getElementsByClassName(obj,className,tagName){
    //Use getElementsByClassName if it is supported
    if ( typeof(obj.getElementsByClassName) != 'undefined' ) {
        return obj.getElementsByClassName(className);
    }

    // Otherwise search for all tags of type tagname with class "className"
    var returnList = new Array();
    var nodes = obj.getElementsByTagName(tagName);
    var max = nodes.length;
    for ( var i = 0; i < max; i++ ) {
        if ( nodes[i].className == className ) {
            returnList[returnList.length] = nodes[i];
        }
    }
    return returnList;
}

function vtiger_help_welcome(obj, container) {
    VtigerJS_DialogBox.block();
    obj.style.zIndex = parseInt((+new Date())/1000)+5; // To ensure z-Index is higher than the popup block
    vtiger_help_controller_singleton.get().helpMeNow(container, '', '?module=Home&action=welcome', true, function(){
        obj.style.display = 'block';
    // NOTE: Google translate handle works one-per page
    // Need more investigation on the failure on other helpmenow page if this is enabled.
    /*
        vtiger_help_controller_singleton.get().translate('vtigerHelpWelcomeGTranslateEl');
        */
    });
}
/**
 * Trigger init of help controller on page load.
 */
Event.observe(window, 'load', function(){
    vtiger_help_controller_singleton.get();
});

