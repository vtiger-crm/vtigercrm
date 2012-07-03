/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function MessageBoxPopup(){
	function center(el){
		el.css({
			position: 'absolute'
		});
		el.width("400px");
		placeAtCenter(el.get(0));
	}

	function close(){
		jQuery('#error_message_box').css('display', 'none');
	}

	function show(module){
		if(typeof('VtigerJS_DialogBox') != 'undefined') VtigerJS_DialogBox.unblock();
		jQuery('#error_message_box').css('display', 'block');
		center(jQuery('#error_message_box'));
	}

	jQuery('#error_message_box_close').click(close);
	jQuery('#error_message_box_cancel').click(close);
	return {
		close:close,
		show:show
	};
}


var validateMandatoryFields = {
	init: function(){
		this.mandatoryFields = [];
	},
	validator: function (){
		var emptyFields = [];
		jQuery('#empty_fields_message').css('display', 'none');
		var result;
		var mandatoryFields = this.mandatoryFields;
		for(var i = 0; i < mandatoryFields.length; i++){
			var fieldName = mandatoryFields[i];
			if(typeof this.fieldValue(fieldName) == 'undefined' || this.fieldValue(fieldName)==""){
				emptyFields.push(fieldName);
			}
		}
		if(emptyFields.length!=0){
			result =  [false, 'empty_fields_message', emptyFields];
		}else{
			result =  [true];
		}
		return result;
	}
};

var validateFieldData = {
	init: function(){
		this.validateFieldData = {};

		this.validateDateTime= function (value){
			value = trim(value);
			var dateTimeElements = value.split(' ');
			var datePart = dateTimeElements[0];
			var timePart = dateTimeElements[1];
			if(this.validateDate(datePart) == false) {
				return false;
			}
			if(this.validateTime(timePart) == false) {
				return false;
			}
			return true;
		};
		this.validateDate = function (value) {
			value = trim(value);
			var dateParts = value.split('-');
			var yyyy = dateParts[0];
			var mm = dateParts[1];
			var dd = dateParts[2];

			if (dd<1 || dd>31 || mm<1 || mm>12 || yyyy<1 || yyyy<1000) {
				return false
			}

			if ((mm==2) && (dd>29)) {
				return false
			}

			if ((mm==2) && (dd>28) && ((yyyy%4)!=0)) {
				return false
			}

			switch (parseInt(mm)) {
				case 2 :
				case 4 :
				case 6 :
				case 9 :
				case 11 :
					if (dd>30) {
						return false
					}
			}

			return true;
		};
		this.validateTime = function (value) {
			value = trim(value);
			var re = /^\d{1,2}\:\d{2}:\d{2}$|^\d{1,2}\:\d{2}$/ 
			if (!re.test(value)) {
				return false;
			}
			var timeParts = value.split(':');
			var hourval = timeParts[0];
			var minval = timeParts[1];

			if (hourval>23 || minval>59) {
				return false
			}
			return true;
		};
		this.validateInteger= function (value) {
			value = trim(value);
			if (isNaN(value) || value.indexOf(".")!=-1) {
				return false;
			}
			if(value < -2147483648 || value > 2147483647) {
				return false;
			}
			return true;
		};
		this.validateNumeric= function (value) {
			value = trim(value);
			if (isNaN(value)) {
				return false;
			}
			return true;
		},
		this.validateEmail= function (value) {
			value = trim(value);
			var re=new RegExp(/^[a-zA-Z0-9]+([\_\-\.]*[a-zA-Z0-9]+[\_\-]?)*@[a-zA-Z0-9]+([\_\-]?[a-zA-Z0-9]+)*\.+([\-\_]?[a-zA-Z0-9])+(\.?[a-zA-Z0-9]+)*$/);
			if (!re.test(value)) {
				return false;
			}
			return true;
		}
	},
	validator: function (){
		var invalidFieldValues = {};
		jQuery('#invalid_field_values_message').css('display', 'none');
		var result =  [true];
		var validationFailed = false;
		var fieldInfo = this.validateFieldData;
		for(var fieldName in fieldInfo){
			var fieldDetails = fieldInfo[fieldName];
			var fieldType = fieldDetails['type'];
			var fieldValue = this.fieldValue(fieldName);
			
			if(typeof fieldValue == 'undefined' || fieldValue == ''
				|| fieldValue.replace(/^\s+/g, '').replace(/\s+$/g, '').length == 0) {
			// Empty value, no value validation required.
			} else if(fieldType == 'email') {
				if(!this.validateEmail(fieldValue)) {
					invalidFieldValues[fieldName] = fieldDetails;
					validationFailed = true;
				}
			} else if(fieldType == 'integer') {
				if(!this.validateInteger(fieldValue)) {
					invalidFieldValues[fieldName] = fieldDetails;
					validationFailed = true;
				}
			} else if(fieldType == 'double' || fieldType == 'currency') {
				if(!this.validateNumeric(fieldValue)) {
					invalidFieldValues[fieldName] = fieldDetails;
					validationFailed = true;
				}
			} else if(fieldType == 'datetime') {
				if(!this.validateDateTime(fieldValue)) {
					invalidFieldValues[fieldName] = fieldDetails;
					validationFailed = true;
				}
			} else if(fieldType == 'date') {
				if(!this.validateDate(fieldValue)) {
					invalidFieldValues[fieldName] = fieldDetails;
					validationFailed = true;
				}
			} else if(fieldType == 'time') {
				if(!this.validateTime(fieldValue)) {
					invalidFieldValues[fieldName] = fieldDetails;
					validationFailed = true;
				}
			}
		}

		if(validationFailed == true) {
			var errorMessageDetails = '';
			for(fieldName in invalidFieldValues){
				errorMessageDetails += '<li>' + invalidFieldValues[fieldName]['label'] + ' (' + invalidFieldValues[fieldName]['type'] + ') </li>';
			}
			var errorMessageElement =  jQuery('#invalid_field_values_fieldlist');
			errorMessageElement.html(errorMessageDetails);
			result =  [false, 'invalid_field_values_message', invalidFieldValues];
		}
		return result;
	}
}

var VTFieldValidatorPrototype = {
	validate: function(){
		var isValid = true;
		var validators = this.validators;
		for(var i = 0; i < validators.length; i++){
			var validator = validators[i];
			var result = validator.call(this);
			if(result[0]==false){
				jQuery('#'+result[1]).css('display', 'block');
				isValid = false;
			}
		}
		if(!isValid){
			this.messageBoxPopup.show();
		}
		return isValid;
	},
	addValidator: function(name, validator){
		validator.init.call(this);
		this.validators.push(validator.validator);
	},
	fieldValue: function(fieldName){
		return this.form.find('[name='+fieldName+']').val();
	}
};

function VTFieldValidator(form){
	var _this = this;
	_this.form = form;
	_this.messageBoxPopup = MessageBoxPopup();
	form.submit(function(){
		return _this.validate();
	});
	_this.validators = [];
	_this.addValidator('mandatoryFields', validateMandatoryFields);
	_this.addValidator('validateFieldData', validateFieldData);
}

VTFieldValidator.prototype = VTFieldValidatorPrototype;

