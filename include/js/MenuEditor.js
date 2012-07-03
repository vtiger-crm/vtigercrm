/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

MenuEditorJs = {
	moveUp : function(){
		var elem = document.getElementById('selectedColumns');
		if(elem.options.selectedIndex>=0){
			for (var i=1;i<elem.options.length;i++){
				if(elem.options[i].selected == true){
					//swap with one up
					var first = elem.options[i-1];
					var second = elem.options[i];
					var temp = new Array();

					temp.value = first.value;
					temp.innerHTML = first.innerHTML;

					first.value = second.value;
					first.innerHTML = second.innerHTML;

					second.value = temp.value;
					second.innerHTML = temp.innerHTML;

					first.selected = true;
					second.selected = false;
				}
			}
		}
	},

/**
 * this function is used to move the selected option down in the assigned picklist
 */
	moveDown : function(){
		var elem = document.getElementById('selectedColumns');
		if(elem.options.selectedIndex>=0){
			for (var i=elem.options.length-2;i>=0;i--){
				if(elem.options[i].selected == true){
					//swap with one down
					var first = elem.options[i+1];
					var second = elem.options[i];
					var temp = new Array();

					temp.value = first.value;
					temp.innerHTML = first.innerHTML;

					first.value = second.value;
					first.innerHTML = second.innerHTML;

					second.value = temp.value;
					second.innerHTML = temp.innerHTML;

					first.selected = true;
					second.selected = false;
				}
			}
		}
	},

	saveAssignedValues : function(){
		var node = document.getElementById('selectedColumns');
		if(node.length == 0){
			alert(alert_arr.LBL_DELETE_ALL_WARNING);
			return false;
		}
		var arr = new Array();
		for(var i=0;i<node.length;i++){
		var tabid = node[i].value;
        var name = node[i].innerHTML;
		arr[i] = Array(tabid,i+1,name);
		}
		node = document.getElementById('roleselect');
		var otherRoles = new Array();
		if(node != null){
			if(node.selectedIndex > -1){
				for(var i=0,j=0; i<node.options.length; i++){
					if(node.options[i].selected == true){
						otherRoles[j++] = node.options[i].value;
					}
				}
			}
		}
		otherRoles = JSON.stringify(otherRoles);

		var values = JSON.stringify(arr);
		$("status").style.display="inline";
		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'action=SettingsAjax&module=Settings&file=MenuSaveAssignedValue&values='+encodeURIComponent(values),
			onComplete: function(response) {
	            $("status").style.display="none";
			}
			}
		);
	}
}