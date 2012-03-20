/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

/**
 * Generic uitype popup selection handler
 */
function vtlib_setvalue_from_popup(recordid,value,target_fieldname) {
	if(window.opener.document.EditView) {
		var domnode_id = window.opener.document.EditView[target_fieldname];
		var domnode_display = window.opener.document.EditView[target_fieldname+'_display'];
		if(domnode_id) domnode_id.value = recordid;
		if(domnode_display) domnode_display.value = value;
		return true;
	} else if(window.opener.document.QcEditView) {
		var domnode_id = window.opener.document.QcEditView[target_fieldname];
		var domnode_display = window.opener.document.QcEditView[target_fieldname+'_display'];
		if(domnode_id) domnode_id.value = recordid;
		if(domnode_display) domnode_display.value = value;
		return true;
	} else {
		return false;
	}
}

/**
 * Show the vtiger field help if available.
 */
function vtlib_field_help_show(basenode, fldname) {
	var domnode = $('vtlib_fieldhelp_div');

	if(typeof(fieldhelpinfo) == 'undefined') return;

	var helpcontent = fieldhelpinfo[fldname];
	if(typeof(helpcontent) == 'undefined') return;

	if(!domnode) {
		domnode = document.createElement('div');
		domnode.id = 'vtlib_fieldhelp_div';
		domnode.className = 'dvtSelectedCell';
		domnode.style.position = 'absolute';
		domnode.style.width = '150px';
		domnode.style.padding = '4px';
		domnode.style.fontWeight = 'normal';
		document.body.appendChild(domnode);	

		domnode = $('vtlib_fieldhelp_div');	
		Event.observe(domnode, 'mouseover', function() { $('vtlib_fieldhelp_div').show(); });
		Event.observe(domnode, 'mouseout', vtlib_field_help_hide);
	}
	else {
		domnode.show();
	}
	domnode.innerHTML = helpcontent;
	fnvshobj(basenode,'vtlib_fieldhelp_div');
}
/**
 * Hide the vtiger field help
 */
function vtlib_field_help_hide(evt) {
	var domnode = $('vtlib_fieldhelp_div');
	if(domnode) domnode.hide();
}

/**
 * Listview Javascript Event handlers API
 * 
 * Example: 
 * vtlib_listview.register('cell.onmouseover', function(evtparams, moreparams) { console.log(evtparams); }, [10,20]);
 * vtlib_listview.register('cell.onmouseout', function(evtparams) {console.log(evtparams); });
 */
var vtlib_listview = {
	/**
	 * Callback function handlers that needs to be triggered for an event
	 * 
	 * _handlers = {
	 *     'event1' : [ [handlerfn11, handlerfn11_moreparams], [handlerfn2, handlerfn12_moreparams] ],
	 *     'event2' : [ [handlerfn21, handlerfn21_moreparams], [handlerfn2, handlerfn22_moreparams] ]
	 * }
	 */
	_handlers : {},
		
	/**
	 * Register handler function for the event
	 */
	register : function(evttype, handler, callback_params) {
		if(typeof(callback_params) == 'undefined') callback_params = false;
		if(typeof(vtlib_listview._handlers[evttype]) == 'undefined') {
			vtlib_listview._handlers[evttype] = [];
		}
		// Event handlerinfo is an array having (function, optional_more_parameters)
		vtlib_listview._handlers[evttype].push([handler, callback_params]);
	},

	/**
	 * Invoke handler function based on event type
	 */
	invoke_handler : function(evttype, event_params) {
		var evthandlers = vtlib_listview._handlers[evttype];
		if(typeof(evthandlers) == 'undefined') return;
		for(var index = 0; index < evthandlers.length; ++index) {
			var evthandlerinfo = evthandlers[index];
			// Event handlerinfo is an array having (function, optional_more_parameters)
			var evthandlerfn = evthandlerinfo[0];
			if(typeof(evthandlerfn) == 'function') {
				evthandlerfn(event_params, evthandlerinfo[1]);
			}
		}
	},
	
	/**
	 * Trigger handler function for the event
	 */
	trigger  : function(evttype, node) {
		if(evttype == 'cell.onmouseover' || evttype == 'cell.onmouseout') {
			// Catch hold of DOM element which has meta inforamtion.
			var innerNodes = node.getElementsByTagName('span');		
			if(typeof(innerNodes) != 'undefined') {
				var cellhandler = false;
				for(var index = 0; index < innerNodes.length; ++index) {
					var innerNodeAttrs = innerNodes[index].attributes;
					if(typeof(innerNodeAttrs) != 'undefined' && typeof(innerNodeAttrs.type) != 'undefined' && innerNodeAttrs['type'].nodeValue == 'vtlib_metainfo') {
						cellhandler = innerNodes[index];
						break;
					}
				}
				if(cellhandler == false) return;
				var event_params = {
					'event'  : evttype,
					'domnode': node,
					'module' : cellhandler.attributes['vtmodule'].nodeValue,
					'fieldname': cellhandler.attributes['vtfieldname'].nodeValue,
					'recordid': cellhandler.attributes['vtrecordid'].nodeValue
				}
				vtlib_listview.invoke_handler(evttype, event_params);
			}
		} 
	}
}
/** END **/

/** 
 * DetailView widget loader API
 */
function vtlib_loadDetailViewWidget(urldata, target, indicator) {

	if(typeof(target) == 'undefined') {
		target = false;
	} else {
		target = $(target);
	}
	if(typeof(indicator) == 'undefined') {
		indicator = false;
	} else {
		indicator = $(indicator);
	}
	
	if(indicator) {
		indicator.show();
	}
	
	new Ajax.Request('index.php',
	{	
		queue: {position: 'end', scope: 'command'},
        method: 'post',
        postBody:urldata,
        onComplete: function(response) {
        	if(target) {
        		target.innerHTML = response.responseText;
        		if(indicator) {
					indicator.hide();
				}
        	}
        }
	});	
	return false; // To stop event propogation
}
