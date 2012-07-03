/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
/**
 * Usage:
 * 
 * (new FieldDependencies(datasource)).init(document.forms['EditView']); // Default form EditView in case not provided.
 * 
 * datasource Format:
 * 
 * datasource = { 
 * 		"sourcefieldname1" : {
 * 
 * 			"sourcevalue1" : {
 * 				"targetfieldname" : ["targetvalue1", "targetvalue2"]
 *	 		},
 * 			"sourcevalue2" : {
 * 				"targetfieldname" : ["targetvalue3", "targetvalue4"]
 * 			},
 * 
 * 			"sourcevalue3" : {
 * 				"targetfieldname" : false // This will enable all the values in the target fieldname
 * 			},
 * 
 * 			// NOTE: All source values (option) needs to be mapped in the datasource
 * 
 * 		},
 * 
 * 		"sourcefieldname2" : {
 * 			// ...
 * 		} 			
 * }
 * 
 * NOTE: Event.fire(targetfieldnode, 'dependent:change'); is triggered on the field value changes.
 * 
 */
 
/**
 * Class FieldDependencies
 * 
 * @param datasource
 */
function FieldDependencies(datasource) {
	this.baseform = false;	
	this.DS = {};
	
	this.initDS(datasource);
}

/**
 * Initialize the data source
 */
FieldDependencies.prototype.initDS = function(datasource) {
	if(typeof(datasource) != 'undefined') {
		this.DS = datasource;
	}
}

/**
 * Initialize the form fields (setup onchange and dependent:change) listeners
 * and trigger the onchange handler for the loaded select fields.
 *
 * NOTE: Only select fields are supported.
 *
 */
FieldDependencies.prototype.setup = function(sourceform, datasource) {
	var thisContext = this;

	if(typeof(sourceform) == 'undefined') {
		this.baseform = document.forms['EditView'];
	} else {
		this.baseform = sourceform;
	}

	this.initDS(datasource);

	if(!this.baseform) return;

	jQuery('select', this.baseform).
		bind('change', function(ev){thisContext.actOnSelectChange(ev);});
}

/**
 * Initialize the form fields (setup onchange and dependent:change) listeners
 * 
 * NOTE: Only select fields are supported.
 * 
 */
FieldDependencies.prototype.init = function(sourceform, datasource) {	
	this.setup(sourceform, datasource);

	for(var sourcename in this.DS) {
		jQuery('[name="'+sourcename+'"]', this.baseform).trigger('change');
	}
}

/**
 * On Change handler for select box.
 */
FieldDependencies.prototype.actOnSelectChange = function(event) {
	var sourcenode = event.target;		
	var sourcename = sourcenode.name;
	var sourcevalue = sourcenode.value;	
	this.fieldValueChange(sourcename, sourcevalue);
};

/**
 * Core function to handle the state of field value changes and 
 * trigger dependent:change event if (Event.fire API is available - Prototype 1.6)
 */
FieldDependencies.prototype.fieldValueChange = function(sourcename, sourcevalue) {
	var targetinfo = null;
	if(typeof(this.DS[sourcename]) != 'undefined') {
		if(typeof(this.DS[sourcename][sourcevalue])  != 'undefined' ) {
			targetinfo = this.DS[sourcename][sourcevalue];
		} else {
			targetinfo = this.DS[sourcename]['__DEFAULT__'];
		}
	}
	
	if(targetinfo != null) {
		for(var targetname in targetinfo) {
			var targetnode = jQuery('[name="'+targetname+'"]', this.baseform);
			var selectedtargetvalue = targetnode.val();
			var targetvalues = targetinfo[targetname];
			
			// In IE we cannot hide the options!, the only way to achieve this effect is
			// recreating the options list again. 
			//
			// To maintain implementation consistency, let us keep copy of options in select node and use it for re-creation
			if(typeof(targetnode.data('allOptions')) == 'undefined') {
				 var allOptions = [];
				jQuery('option', targetnode).each(function(index,option) {
					allOptions.push(option);
				});
				targetnode.data('allOptions', allOptions);
			}
			var targetoptions = targetnode.data('allOptions');

			// Remove the existing options nodes from the target selection
			jQuery('option', targetnode).remove();

			for(var index = 0; index < targetoptions.length; ++index) {
				var targetoption = jQuery(targetoptions[index]);
				// Show the option if field mapping matches the option value or there is not field mapping available.
				if(targetvalues == false || targetvalues.indexOf(targetoption.val()) != -1) {
					var optionNode = jQuery(document.createElement('option'));
					targetnode.append(optionNode);
					optionNode.text(targetoption.text());
					optionNode.val(targetoption.val());
				}
			}
			targetnode.val(selectedtargetvalue);
			targetnode.trigger('change');
		}
	}
}
