/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

// MassEdit Feature
function massedit_togglediv(curTabId,total){

   for(var i=0;i<total;i++){
	tagName = $('massedit_div'+i);
	tagName1 = $('tab'+i)
	tagName.style.display = 'none';
	tagName1.className = 'dvtUnSelectedCell';
   }

   tagName = $('massedit_div'+curTabId);
   tagName.style.display = 'block';
   tagName1 = $('tab'+curTabId)
   tagName1.className = 'dvtSelectedCell';
}

function massedit_initOnChangeHandlers() {
	var form = document.getElementById('massedit_form');
	// Setup change handlers for input boxes
	var inputs = form.getElementsByTagName('input');
	for(var index = 0; index < inputs.length; ++index) {
		var massedit_input = inputs[index];
		// TODO Onchange on readonly and hidden fields are to be handled later.
		massedit_input.onchange = function() {
			var checkbox = document.getElementById(this.name + '_mass_edit_check');
			if(checkbox) checkbox.checked = true;
		}
	}
	// Setup change handlers for select boxes
	var selects = form.getElementsByTagName('select');
	for(var index = 0; index < selects.length; ++index) {
		var massedit_select = selects[index];
		massedit_select.onchange = function() {
			var checkbox = document.getElementById(this.name + '_mass_edit_check');
			if(checkbox) checkbox.checked = true;
		}
	}
}

function mass_edit(obj,divid,module,parenttab) {
	var select_options = document.getElementById('allselectedboxes').value;
	var numOfRows = document.getElementById('numOfRows').value;
	var excludedRecords = $('excludedRecords').value;
	if(select_options=='all') {
		var idstring = select_options;
		var skiprecords = excludedRecords.split(";");
		var count = skiprecords.length;
		if(count > 1) {
			count = numOfRows - count + 1;
		} else {
			count = numOfRows;
		}
		if(count > getMaxMassOperationLimit()) {
			var confirm_str = alert_arr.MORE_THAN_500;
			if(confirm(confirm_str)) {
				var confirm_status = true;
			} else {
				return false;
			}
		} else {
			confirm_status = true;
		}

		if(confirm_status) {
			mass_edit_formload(idstring,module,parenttab);
		}
	} else {
		var x = select_options.split(';');
		var count = x.length;
		if(count > 1) {
			idstring = select_options;
			if(count > getMaxMassOperationLimit()) {
				confirm_str = alert_arr.MORE_THAN_500;
				if(confirm(confirm_str)) {
					confirm_status = true;
				} else {
					return false;
				}
			} else {
				confirm_status = true;
			}

			if(confirm_status) {
				mass_edit_formload(idstring,module,parenttab);
			}
		} else {
			alert(alert_arr.SELECT);
			return false;
		}
	}
	fnvshobj(obj, divid);
}
function mass_edit_formload(idstring,module,parenttab) {
	if(typeof(parenttab) == 'undefined') parenttab = '';
	var excludedRecords=document.getElementById("excludedRecords").value;
	var viewid =getviewId();
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody:"module="+encodeURIComponent(module)+"&action="+encodeURIComponent(module+'Ajax')+"&parenttab="+encodeURIComponent(parenttab)+"&file=MassEdit&mode=ajax&idstring="+idstring+"&viewname="+viewid+"&excludedRecords="+excludedRecords,
			onComplete: function(response) {
				$("status").style.display="none";
				var result = response.responseText;
				$("massedit_form_div").update(result);
				$("massedit_form")["massedit_recordids"].value = $("massedit_form")['idstring'].value;
				$("massedit_form")["massedit_module"].value = module;
			}
		}
		);
}
function mass_edit_fieldchange(selectBox) {
	var oldSelectedIndex = selectBox.oldSelectedIndex;
	var selectedIndex = selectBox.selectedIndex;

	if($('massedit_field'+oldSelectedIndex)) $('massedit_field'+oldSelectedIndex).style.display='none';
	if($('massedit_field'+selectedIndex)) $('massedit_field'+selectedIndex).style.display='block';

	selectBox.oldSelectedIndex = selectedIndex;
}

function mass_edit_save(){
	var masseditform = $("massedit_form");
	var module = masseditform["massedit_module"].value;
	var viewid = document.getElementById("viewname").options[document.getElementById("viewname").options.selectedIndex].value;
	var searchurl = document.getElementById("search_url").value;

	var urlstring =
		"module="+encodeURIComponent(module)+"&action="+encodeURIComponent(module+'Ajax')+
		"&return_module="+encodeURIComponent(module)+"&return_action=ListView"+
		"&mode=ajax&file=MassEditSave&viewname=" + viewid ;//+"&"+ searchurl;

	fninvsh("massedit");

	new Ajax.Request(
		"index.php",
		{queue:{position:"end", scope:"command"},
			method:"post",
			postBody:urlstring,
			onComplete:function (response) {
				$("status").style.display = "none";
				var result = response.responseText.split("&#&#&#");
				$("ListViewContents").innerHTML = result[2];
				if (result[1] != "") {
					alert(result[1]);
				}
				$("basicsearchcolumns").innerHTML = "";
			}
		}
	);

}
function ajax_mass_edit() {
	alert();
	$("status").style.display = "inline";

	var masseditform = $("massedit_form");
	var module = masseditform["massedit_module"].value;

	var viewid = document.getElementById("viewname").options[document.getElementById("viewname").options.selectedIndex].value;
	var idstring = masseditform["massedit_recordids"].value;
	var searchurl = document.getElementById("search_url").value;
	var tplstart = "&";
	if (gstart != "") {tplstart = tplstart + gstart;}

	var masseditfield = masseditform['massedit_field'].value;
	var masseditvalue = masseditform['massedit_value_'+masseditfield].value;

	var urlstring = 
		"module="+encodeURIComponent(module)+"&action="+encodeURIComponent(module+'Ajax')+
		"&return_module="+encodeURIComponent(module)+
		"&mode=ajax&file=MassEditSave&viewname=" + viewid + 
		"&massedit_field=" + encodeURIComponent(masseditfield) +
		"&massedit_value=" + encodeURIComponent(masseditvalue) +
	   	"&idlist=" + idstring + searchurl;

	fninvsh("massedit");

	new Ajax.Request(
		"index.php", 
		{queue:{position:"end", scope:"command"}, 
			method:"post", 
			postBody:urlstring, 
			onComplete:function (response) {
				$("status").style.display = "none";
				var result = response.responseText.split("&#&#&#");
				$("ListViewContents").innerHTML = result[2];
				if (result[1] != "") {
					alert(result[1]);
				}
				$("basicsearchcolumns").innerHTML = "";
			}
		}
	); 
}
	
// END

function change(obj,divid)
{
	var excludedRecords = document.getElementById("excludedRecords").value;
	var select_options  =  document.getElementById('allselectedboxes').value;
	//Added to remove the semi colen ';' at the end of the string.done to avoid error.
	var searchurl = document.getElementById('search_url').value;
	var numOfRows = document.getElementById('numOfRows').value;
	var viewid = getviewId();
	var idstring = "";
	if(select_options == 'all'){
		idstring = select_options;
		document.getElementById('idlist').value = idstring;
		var count = numOfRows;
	} else {
		var x = select_options.split(";");
		var count = x.length;

		if (count > 1) {
			idstring = select_options;
			document.getElementById('idlist').value = idstring;
		} else {
			alert(alert_arr.SELECT);
			return false;
		}
	}

	if(count > getMaxMassOperationLimit()) {
		var confirm_str = alert_arr.MORE_THAN_500;
		if(confirm(confirm_str)) var confirm_status = true;
		else return false;
	}
	else confirm_status = true;

	if(confirm_status){
		fnvshobj(obj,divid);
	}
}
var gstart='';
function massDelete(module)
{
	var searchurl = $('search_url').value;
	var viewid = getviewId();
	var idstring = "";
	if(module != 'Documents'){
		var select_options = $('allselectedboxes').value;
		var excludedRecords = $('excludedRecords').value;
		var numOfRows = $('numOfRows').value;
		if(select_options == 'all'){
			document.getElementById('idlist').value = select_options;
			idstring = select_options;
			var skiprecords = excludedRecords.split(";");
			var count = skiprecords.length;
			if(count > 1){
				count = numOfRows - count + 1;
			} else {
				count = numOfRows;
			}
		} else {
			var x = select_options.split(";");
			count = x.length;
			if (count > 1) {
				document.getElementById('idlist').value = select_options;
				idstring = select_options;
			} else {
				alert(alert_arr.SELECT);
				return false;
			}
			//we have to decrese the count value by 1 because when we split with semicolon we will get one extra count
			count = count - 1;
		}
	} else {
		select_options = '';
		excludedRecords = '';
		var obj = document.getElementsByName('folderidVal');
		var folderid = '0';
		var numOfRows = 0;
		var activation = 'false';
		if(obj){
			for(var i=0;i<obj.length;i++){
				var id = obj[i].value;
				if($('selectedboxes_selectall'+id).value == 'all'){
					var rows = $('numOfRows_selectall'+id).value;
					numOfRows = numOfRows+parseInt(rows);
					excludedRecords = excludedRecords + $('excludedRecords_selectall'+id).value;
					folderid = id+';'+folderid;
					activation = 'true';
				} else {
					 select_options = select_options + $('selectedboxes_selectall'+id).value;
				}
			}
		}
		x = select_options.split(";");
		var count = x.length;
		numOfRows = numOfRows + count-1;
		if(activation == 'true'){
			document.getElementById('idlist').value=select_options;
			idstring = select_options;
			skiprecords = excludedRecords.split(";");
			var excount = skiprecords.length;
			if(excount > 1){
				count = numOfRows - excount+1;
			} else {
				count = numOfRows;
			}
		} else {
			if (count > 1) {
				document.getElementById('idlist').value = select_options;
				idstring = select_options;
			} else {
				alert(alert_arr.SELECT);
				return false;
			}
			//we have to decrese the count value by 1 because when we split with semicolon we will get one extra count
			count = count - 1;
		}
	}

	if(count > getMaxMassOperationLimit()) {
		var confirm_str = alert_arr.MORE_THAN_500;
		if(confirm(confirm_str)) var confirm_status = true;
		else return false;
	}
	else confirm_status = true;

	if(confirm_status){
		var alert_str = alert_arr.DELETE + count +alert_arr.RECORDS;

		if(module == "Accounts")
			alert_str = alert_arr.DELETE_ACCOUNT +count+alert_arr.RECORDS;
		else if(module == "Vendors")
			alert_str = alert_arr.DELETE_VENDOR+count+alert_arr.RECORDS;

		if(confirm(alert_str)) {
			$("status").style.display="inline";
			var url = "&excludedRecords="+excludedRecords;
			if(module=='Documents'){
				var url = url+"&folderidstring="+folderid+"&selectallmode="+activation;
			}

			new Ajax.Request(
				'index.php',
				{
					queue: {
						position: 'end',
						scope: 'command'
					},
					method: 'post',
					postBody:"module=Users&action=massdelete&return_module="+module+"&"+gstart+"&viewname="+viewid+"&idlist="+idstring+searchurl+url,
					onComplete: function(response) {
						$("status").style.display="none";
						result = response.responseText.split('&#&#&#');
						$("ListViewContents").innerHTML= result[2];
						if(result[1] != '')
							alert(result[1]);
						$('basicsearchcolumns').innerHTML = '';
						$('allselectedboxes').value='';
						$('excludedRecords').value='';
					}
				}
				);
		} else {
			return false;
		}
	}
}

function showDefaultCustomView(selectView,module,parenttab)
{
	$("status").style.display="inline";
	var viewName = encodeURIComponent(selectView.options[selectView.options.selectedIndex].value);
	new Ajax.Request(
               	'index.php',
                {queue: {position: 'end', scope: 'command'},
                       	method: 'post',
                        postBody:"module="+module+"&action="+module+"Ajax&file=ListView&ajax=true&start=1&viewname="+viewName+"&parenttab="+parenttab,
                        onComplete: function(response) {
                        $("status").style.display="none";
                        result = response.responseText.split('&#&#&#');
                        $("ListViewContents").innerHTML= result[2];
                        if(result[1] != '')
                               	alert(result[1]);
			$('basicsearchcolumns_real').innerHTML = $('basicsearchcolumns').innerHTML
			$('basicsearchcolumns').innerHTML = '';
			document.basicSearch.search_text.value = '';
                        }
                }
	);
}

function getListViewEntries_js(module,url)
{
	if(module!='Documents'){
		var excludedRecords = $('excludedRecords').value;
		var all_selected = $("allselectedboxes").value;
		var count = $('numOfRows').value;
	} else {
		var obj = document.getElementsByName('folderidVal');
		var selected = '';
		var selectedRecords = new Array();
		var excludedRecords = new Array();
		var numOfRows = new Array();
		for(var i=0;i<obj.length;i++){
			var id = obj[i].value;
			excludedRecords[i] = $('excludedRecords_selectall'+id).value;
			selectedRecords[i] = $('selectedboxes_selectall'+id).value;
			numOfRows[i] = $('numOfRows_selectall'+id).value;
		}
		var urlArray= url.split('&');
		var folderid;
		for(var i=0;i<urlArray.length;i++){
			var getId = urlArray[i].split('=');
			if(getId[0] == 'folderid'){
				folderid = parseInt(getId[1]);
				all_selected = $('selectedboxes_selectall'+folderid).value;
			}
		}
	}

	var select_options  =  document.getElementsByName('selected_id');
	var x = select_options.length;
	var viewid = getviewId();
	var idstring = "";

	xx = 0;
	for(i = 0; i < x ; i++)
	{
		if(select_options[i].checked){
			idstring = select_options[i].value +";"+idstring
			xx++
		}
	}

	$("status").style.display="inline";
	if(typeof $('search_url') != 'undefined' && $('search_url').value!='')
		var urlstring = $('search_url').value;
	else
		urlstring = '';

	gstart = url;
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody:"module="+module+"&action="+module+"Ajax&file=ListView&ajax=true&allselobjs="+all_selected+"&selobjs="+idstring+"&"+url+urlstring,
			onComplete: function(response) {
				$("status").style.display="none";
				var result = response.responseText.split('&#&#&#');
				$("ListViewContents").innerHTML= result[2];

				if(module == 'Documents') {
					obj = document.getElementsByName('folderidVal');
					for(var i=0;i<obj.length;i++){
						var id = obj[i].value;
						$('excludedRecords_selectall'+id).value = $('excludedRecords_selectall'+id).value + excludedRecords[i];
						$('selectedboxes_selectall'+id).value = $('selectedboxes_selectall'+id).value + selectedRecords[i];
						$('numOfRows_selectall'+id).value = numOfRows[i];
						$('count_selectall'+id).innerHTML = numOfRows[i];
						if(selectedRecords[i] == 'all'){
							$('linkForSelectAll_selectall'+id).show();
							$('selectAllRec_selectall'+id).style.display='none';
							$('deSelectAllRec_selectall'+id).style.display='inline';
							var exculdedArray = excludedRecords[i].split(';');
							var selectedobj = document.getElementsByName('selected_id'+id);
							var viewForSelectLink = showSelectAllLink(selectedobj,exculdedArray);
							$('currentPageRec_selectall'+id).checked = viewForSelectLink;
						} else {
							if(selectedRecords[i] != ''){
								selected = selectedRecords[i].split(';');
								selected.splice(selected.indexOf(''),1);
								for(var j=0;j<selected.length;j++){
									if($(selected[j])){
										$(selected[j]).checked = true;
									}
								}
							}
						}
						default_togglestate('selected_id'+id,'selectall'+id);
					}
				} else {
					$('numOfRows').value = count;
					$("count").innerHTML = count;
					if(all_selected == 'all'){
						$('linkForSelectAll').show();
						$('selectAllRec').style.display = 'none';
						$('deSelectAllRec').style.display = 'inline';
						exculdedArray=excludedRecords.split(';');
						obj = document.getElementsByName('selected_id');
						viewForSelectLink = showSelectAllLink(obj,exculdedArray);
						$('selectCurrentPageRec').checked = viewForSelectLink;
						$('allselectedboxes').value = 'all';
						$('excludedRecords').value = $('excludedRecords').value+excludedRecords;
					}else{
						$('linkForSelectAll').hide();
						update_selected_checkbox();
					}
				}
				if(result[1] != '')
					alert(result[1]);
				$('basicsearchcolumns').innerHTML = '';
			}
		}
		);
}
//for multiselect check box in list view:

function check_object(sel_id,groupParentElementId)
{
	if($('curmodule') != undefined && $('curmodule').value == 'Documents') {
		var selected = trim($('selectedboxes_'+groupParentElementId).value);
		var skip = $('excludedRecords_'+groupParentElementId).value;
	} else {
		selected = trim($("allselectedboxes").value);
		skip = $("excludedRecords").value;
	}
	var select_global = new Array();
	select_global = selected.split(";");
	var box_value = sel_id.checked;
	var id = sel_id.value;
	var duplicate = select_global.indexOf(id);
	var size = select_global.length-1;
	var result = "";
	if(box_value == true)
	{
		if($('curmodule') != undefined && $('curmodule').value == 'Documents' && $('selectedboxes_'+groupParentElementId).value == 'all'){
			$('excludedRecords_'+groupParentElementId).value = skip.replace(skip.match(id+";"),'');
			$('selectedboxes_'+groupParentElementId).value = 'all';
		} else if($("allselectedboxes").value == 'all'){
			$("excludedRecords").value = skip.replace(skip.match(id+";"),'');
			$("allselectedboxes").value = 'all';
		} else {
			if(duplicate == "-1")
				select_global[size] = id;

			size=select_global.length-1;
			for(i=0;i<=size;i++) {
				if(trim(select_global[i])!='')
					result=select_global[i]+";"+result;
			}
			//default_togglestate(sel_id.name,groupParentElementId);
			if($('curmodule') != undefined && $('curmodule').value == 'Documents') {
				$('selectedboxes_'+groupParentElementId).value = result;
			} else {
				$('allselectedboxes').value = result;
			}
		}
		default_togglestate(sel_id.name,groupParentElementId);
	} else {
		if($('curmodule') != undefined && $('curmodule').value == 'Documents' && $('selectedboxes_'+groupParentElementId).value == 'all'){
			$('excludedRecords_'+groupParentElementId).value = id+";"+skip;
			$('selectedboxes_'+groupParentElementId).value = 'all';
		} else if($("allselectedboxes").value == 'all') {
			$("excludedRecords").value = id+";"+skip;
			$("allselectedboxes").value = 'all';
		} else {
			if(duplicate != "-1")
				select_global.splice(duplicate,1);

			size = select_global.length-1;
			var i=0;
			for(i=size;i>=0;i--) {
				if(trim(select_global[i])!='')
					result = select_global[i]+";"+result;
			}
			default_togglestate(sel_id.name,groupParentElementId);
			if($('curmodule') != undefined && $('curmodule').value == 'Documents'){
				$('selectedboxes_'+groupParentElementId).value = result;
			} else {
				$("allselectedboxes").value = result;
			}
		}
		if($('curmodule') != undefined && $('curmodule').value == 'Documents'){
			$('currentPageRec_'+groupParentElementId).checked = false;
		} else {
			$('selectCurrentPageRec').checked = false;
		}
	}
}

function update_selected_checkbox()
{
	var cur = document.getElementById('current_page_boxes').value;
	var tocheck = document.getElementById('allselectedboxes').value;
	var cursplit = new Array();
	cursplit = cur.split(";");

	var selsplit = new Array();
	selsplit = tocheck.split(";");

	//	var n=selsplit.length;
	var selectCurrentPageRecCheckValue = true;
	for(var j=0;j<cursplit.length;j++){
		if(selsplit.indexOf(cursplit[j])!= "-1"){
			document.getElementById(cursplit[j]).checked = 'true';
		}
		else{
			selectCurrentPageRecCheckValue = false;
		}
	}
	if(selectCurrentPageRecCheckValue && cursplit.length>0){
		document.getElementById('selectCurrentPageRec').checked = 'true';
	}
}

//Function to Set the status as Approve/Deny for Public access by Admin
function ChangeCustomViewStatus(viewid,now_status,changed_status,module,parenttab)
{
	$('status').style.display = 'block';
	new Ajax.Request(
       		'index.php',
               	{queue: {position: 'end', scope: 'command'},
               		method: 'post',
                    postBody:'module=CustomView&action=CustomViewAjax&file=ChangeStatus&dmodule='+module+'&record='+viewid+'&status='+changed_status,
					onComplete: function(response) 
					{
			        	var responseVal=response.responseText;
						if(responseVal.indexOf(':#:FAILURE') > -1) {
							alert('Failed');
						} else if(responseVal.indexOf(':#:SUCCESS') > -1) {
							var values = responseVal.split(':#:');
							var module_name = values[2];
							var customview_ele = $('viewname');
							showDefaultCustomView(customview_ele, module_name, parenttab);
						} else {
							$('ListViewContents').innerHTML = responseVal;
						}
						$('status').style.display = 'none';
					} 
				}
	);
}

function getListViewCount(module,element,parentElement,url){
	if(module != 'Documents'){
		var elementList = document.getElementsByName(module+'_listViewCountRefreshIcon');
		for(var i=0;i<elementList.length;++i){
			elementList[i].style.display = 'none';
		}
	}else{
		element.style.display = 'none';
	}
	var elementList = document.getElementsByName(module+'_listViewCountContainerBusy');
	for(var i=0;i<elementList.length;++i){
		elementList[i].style.display = '';
	}
	var element = document.getElementsByName('search_url')[0];
	var searchURL = '';
	if(typeof element !='undefined'){
		searchURL = element.value;
	}else if(typeof document.getElementsByName('search_text')[0] != 'undefined'){
		element = document.getElementsByName('search_text')[0];
		var searchField = document.getElementsByName('search_field')[0];
		if(element.value.length > 0) {
			searchURL = '&query=true&searchtype=BasicSearch&search_field='+
				encodeURIComponent(searchField.value)+'&search_text='+encodeURIComponent(element.value);
		}
	}else if(document.getElementById('globalSearchText') != null &&
			typeof document.getElementById('globalSearchText') != 'undefined'){
            var searchText = document.getElementById('globalSearchText').value;
            searchURL = '&query=true&globalSearch=true&globalSearchText='+encodeURIComponent(searchText);
            if(document.getElementById('tagSearchText') != null && typeof document.getElementById('tagSearchText') != 'undefined'){
                var tagSearch = document.getElementById('tagSearchText').value;
                searchURL = '&query=true&globalSearch=true&globalSearchText='+encodeURIComponent(searchText)+'&tagSearchText='+encodeURIComponent(tagSearch);
            }
	}
	if(module != 'Documents'){
		searchURL += (url);
	}
	// Url parameters to carry forward the Alphabetical search in Popups,
	// which is stored in the global variable gPopupAlphaSearchUrl
	if(typeof gPopupAlphaSearchUrl != 'undefined' && gPopupAlphaSearchUrl != '')
		searchURL += gPopupAlphaSearchUrl;

	new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody:"module="+module+"&action="+module+"Ajax&file=ListViewPagging&ajax=true"+searchURL,
				onComplete: function(response) {
					var elementList = document.getElementsByName(module+'_listViewCountContainerBusy');
					for(var i=0;i<elementList.length;++i){
						elementList[i].style.display = 'none';
					}
					elementList = document.getElementsByName(module+'_listViewCountRefreshIcon');
					if(module != 'Documents' && typeof parentElement != 'undefined' && elementList.length !=0){
						for(i=0;i<=elementList.length;){
							//No need to increment the count, as the element will be eliminated in the next step.
							elementList[i].parentNode.innerHTML = response.responseText;
						}
					}else{
						parentElement.innerHTML = response.responseText;
					}
				}
			}
	);
}

function VT_disableFormSubmit(evt) {
	var evt = (evt) ? evt : ((event) ? event : null);
	var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
	if ((evt.keyCode == 13) && (node.type=='text')) {
		node.onchange();
		return false;
	}
	return true;
}
var statusPopupTimer = null;
function closeStatusPopup(elementid)
{
	statusPopupTimer = setTimeout("document.getElementById('" + elementid + "').style.display = 'none';", 50);
}

function updateCampaignRelationStatus(relatedmodule, campaignid, crmid, campaignrelstatusid, campaignrelstatus)
{
	$("vtbusy_info").style.display="inline";
	document.getElementById('campaignstatus_popup_' + crmid).style.display = 'none';
	var data = "action=updateRelationsAjax&module=Campaigns&relatedmodule=" + relatedmodule + "&campaignid=" + campaignid + "&crmid=" + crmid + "&campaignrelstatusid=" + campaignrelstatusid;
	new Ajax.Request(
		'index.php',
			{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: data,
			onComplete: function(response) {
				if(response.responseText.indexOf(":#:FAILURE")>-1)
				{
					alert(alert_arr.ERROR_WHILE_EDITING);
				}
				else if(response.responseText.indexOf(":#:SUCCESS")>-1)
				{
					document.getElementById('campaignstatus_' + crmid).innerHTML = campaignrelstatus;
					$("vtbusy_info").style.display="none";
				}
			}
		}
	);
}

function loadCvList(type,id) {
	var element = type+"_cv_list";
	var value = document.getElementById(element).value;        

	var filter = $(element)[$(element).selectedIndex].value	;
	if(filter=='None')return false;
	if(value != '') {
		$("status").style.display="inline";
		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody: 'module=Campaigns&action=CampaignsAjax&file=LoadList&ajax=true&return_action=DetailView&return_id='+id+'&list_type='+type+'&cvid='+value,
				onComplete: function(response) {
					$("status").style.display="none";
					$("RLContents").update(response.responseText);
				}
			}
		);
	}
}

// mailer_export
function mailer_export() {
	var module = document.getElementById('curmodule').value;
	document.massdelete.action.value = "MailerExport";
	document.massdelete.step.value = "ask";
	new Ajax.Request(
		'index.php',
		{
			queue: {
				position: 'end',
				scope: 'command'
			},
			method: 'post',
			postBody: "module="+module+"&action=MailerExport&from="+module+"&step=ask"
		}
		);
}
// end of mailer export
