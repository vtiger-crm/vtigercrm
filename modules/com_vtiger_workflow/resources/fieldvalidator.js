function MessageBoxPopup(){
	function center(el){
		el.css({position: 'absolute'});
		el.width("400px");
		el.height("110px");
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
			close:close,show:show
	};
}


var validateMandatoryFields = {
	init: function(){
		this.mandatoryFields = [];
	},
	validator: function (){
		var emptyFields = [];
		var result;
		var mandatoryFields = this.mandatoryFields;
		for(var i = 0; i < mandatoryFields.length; i++){
			var fieldName = mandatoryFields[i];
			if(this.fieldValue(fieldName)==""){
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
}

VTFieldValidator.prototype = VTFieldValidatorPrototype;

