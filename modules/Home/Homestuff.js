/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *******************************************************************************/

/**
 * this function is used to show hide the columns in the add widget div based on the option selected
 * @param string typeName - the selected option
 */
function chooseType(typeName){
	$('vtbusy_info').style.display="inline";
	$('stufftype_id').value=typeName;
	
	var typeLabel = typeName;
	if(alert_arr[typeName] != null && alert_arr[typeName] != "" && alert_arr[typeName] != 'undefined'){
		typeLabel = alert_arr[typeName];
	}
	$('divHeader').innerHTML="<b>"+alert_arr.LBL_ADD+typeLabel+"</b>";
	if(typeName=='Module'){
		$('moduleNameRow').style.display="block";
		$('moduleFilterRow').style.display="block";
		$('modulePrimeRow').style.display="block";
		$('showrow').style.display="block";
		$('rssRow').style.display="none";
		$('dashNameRow').style.display="none";
		$('dashTypeRow').style.display="none";
		$('StuffTitleId').style.display="block";
		//$('homeURLField').style.display = "none";
	}else if(typeName=='DashBoard'){
		$('moduleNameRow').style.display="none";
		$('moduleFilterRow').style.display="none";
		$('modulePrimeRow').style.display="none";
		$('rssRow').style.display="none";
		$('showrow').style.display="none";
		$('dashNameRow').style.display="block";
		$('dashTypeRow').style.display="block";
		$('StuffTitleId').style.display="block";
		//$('homeURLField').style.display = "none";
		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&dash=dashboard',
				onComplete: function(response){
					var responseVal=response.responseText;
					$('selDashName').innerHTML=response.responseText;
					show('addWidgetsDiv');
					placeAtCenter($('addWidgetsDiv'));
					$('vtbusy_info').style.display="none";
				}
			}
		);
	}else if(typeName=='RSS'){
		$('moduleNameRow').style.display="none";
		$('moduleFilterRow').style.display="none";
		$('modulePrimeRow').style.display="none";
		$('showrow').style.display="block";
		$('rssRow').style.display="block";
		$('dashNameRow').style.display="none";
		$('dashTypeRow').style.display="none";
		$('StuffTitleId').style.display="block";
		$('vtbusy_info').style.display="none";
		//$('homeURLField').style.display = "none";
	}else if(typeName=='Default'){
		$('moduleNameRow').style.display="none";
		$('moduleFilterRow').style.display="none";
		$('modulePrimeRow').style.display="none";
		$('showrow').style.display="none";
		$('rssRow').style.display="none";
		$('dashNameRow').style.display="none";
		$('dashTypeRow').style.display="none";
		$('StuffTitleId').style.display="none";
		$('url_id').style.display = "none";
	}else if(typeName == 'Notebook'){
		$('moduleNameRow').style.display="none";
		$('moduleFilterRow').style.display="none";
		$('modulePrimeRow').style.display="none";
		$('showrow').style.display="none";
		$('rssRow').style.display="none";
		$('dashNameRow').style.display="none";
		$('dashTypeRow').style.display="none";
		$('StuffTitleId').style.display="block";
		$('vtbusy_info').style.display="none";
		//$('homeURLField').style.display = "none";
	}
	/*else if(typeName == 'URL'){
		$('moduleNameRow').style.display="none";
		$('moduleFilterRow').style.display="none";
		$('modulePrimeRow').style.display="none";
		$('showrow').style.display="none";
		$('rssRow').style.display="none";
		$('dashNameRow').style.display="none";
		$('dashTypeRow').style.display="none";
		$('StuffTitleId').style.display="block";
		$('vtbusy_info').style.display="none";
		//$('homeURLField').style.display = "block";
	}*/
}

/**
 * this function is used to set the filter list when the module name is changed
 * @param string modName - the modula name for which you want the filter list
 */
function setFilter(modName){
	var modval=modName.value;
	document.getElementById('savebtn').disabled = true;
	if(modval!=""){
		new Ajax.Request(
       		'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&modname='+modval,
				onComplete: function(response){
					var responseVal=response.responseText;
					$('selModFilter_id').innerHTML=response.responseText;
					setPrimaryFld(document.getElementById('selFilterid'));
					show('addWidgetsDiv');
					placeAtCenter($('addWidgetsDiv'));
				}
			}
		);
	}
}

/**
 * this function is used to set the field list when the module name is changed
 * @param string modName - the modula name for which you want the field list
 */
function setPrimaryFld(Primeval){
	primecvid=Primeval.value;
	var fldmodule = $('selmodule_id').options[$('selmodule_id').selectedIndex].value;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
		method: 'post',
		postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&primecvid='+primecvid+'&fieldmodname='+fldmodule,
		onComplete: function(response){
			var responseVal=response.responseText;
			$('selModPrime_id').innerHTML=response.responseText;
			$('selPrimeFldid').selectedIndex = 0;
			$('vtbusy_info').style.display="none";
			document.getElementById('savebtn').disabled = false;
		}
	}
	);
}

/**
 * this function displays the div for selecting the number of rows in a widget
 * @param string sid - the id of the widget for which the div is being displayed
 */
function showEditrow(sid){
	$('editRowmodrss_'+sid).className="show_tab";
}

/**
 * this function is used to hide the div for selecting the number of rows in a widget
 * @param string editRow - the id of the div
 */
function cancelEntries(editRow){
	$(editRow).className="hide_tab";
}

/**
 * this function is used to save the maximum entries that a widget can display
 * @param string selMaxName - the widget name
 */
function saveEntries(selMaxName){
	sidarr=selMaxName.split("_");
	sid=sidarr[1];
	$('refresh_'+sid).innerHTML=$('vtbusy_homeinfo').innerHTML;
	cancelEntries('editRowmodrss_'+sid)
	showmax=$(selMaxName).value;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&showmaxval='+showmax+'&sid='+sid,
			onComplete: function(response){
				var responseVal=response.responseText;
				eval(response.responseText);
				$('refresh_'+sid).innerHTML='';
			}
		}
	);
}

/**
 * this function is used to save the dashboard values
 */
function saveEditDash(dashRowId){
	$('refresh_'+dashRowId).innerHTML=$('vtbusy_homeinfo').innerHTML;
	cancelEntries('editRowmodrss_'+dashRowId);
	var dashVal='';
	var iter=0;
	for(iter=0;iter<3;iter++){
		if($('dashradio_'+[iter]).checked)
			dashVal=$('dashradio_'+[iter]).value;
	}
	did=dashRowId;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&dashVal='+dashVal+'&did='+did,
			onComplete: function(response){
				var responseVal=response.responseText;
				eval(response.responseText);
				$('refresh_'+did).innerHTML='';
			}
		}
	);
}

/**
 * this function is used to delete widgets form the home page
 * @param string sid - the stuffid of the widget
 */
function DelStuff(sid){
	if(confirm(alert_arr.SURE_TO_DELETE)){
		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&homestuffid='+sid,
				onComplete: function(response){
					var responseVal=response.responseText;
					if(response.responseText.indexOf('SUCCESS') > -1){
						var delchild = $('stuff_'+sid);
						odeletedChild = $('MainMatrix').removeChild(delchild);
						$('seqSettings').innerHTML= '<table cellpadding="10" cellspacing="0" border="0" width="100%" class="vtResultPop small"><tr><td align="center">Widget deleted sucessfully.</td></tr></table>';
						$('seqSettings').style.display = 'block';
						$('seqSettings').style.display = 'none';
						placeAtCenter($('seqSettings'));
						Effect.Appear('seqSettings');
						setTimeout(hideSeqSettings,3000);
					}else{
						alert(alert_arr.ERROR_DELETING_TRY_AGAIN)
					}
				}
			}
		);
	}
}

/**
 * this function loads the newly added div to the home page
 * @param string stuffid - the id of the newly created div
 * @param string stufftype - the stuff type for the new div (for e.g. rss)
 */
function loadAddedDiv(stuffid,stufftype){
	gstuffId = stuffid;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=NewBlock&stuffid='+stuffid+'&stufftype='+stufftype,
			onComplete: function(response){
				var responseVal=response.responseText;
				$('MainMatrix').style.display= 'none';
				$('MainMatrix').innerHTML = response.responseText + $('MainMatrix').innerHTML;
				positionDivInAccord('stuff_'+gstuffId,'',stufftype);
				initHomePage();
				loadStuff(stuffid,stufftype);
				$('MainMatrix').style.display='block';
			}
		}
	);
}

/**
 * this function is used to reload a widgets' content based on its id and type
 * @param string stuffid - the widget id
 * @param string stufftype - the type of the widget
 */
function loadStuff(stuffid,stufftype){
	$('refresh_'+stuffid).innerHTML=$('vtbusy_homeinfo').innerHTML;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
		    postBody:'module=Home&action=HomeAjax&file=HomeBlock&homestuffid='+stuffid+'&blockstufftype='+stufftype,
		    onComplete: function(response){
				var responseVal=response.responseText;
				$('stuffcont_'+stuffid).innerHTML=response.responseText;
				if(stufftype=="Module"){
					if($('more_'+stuffid).value != null && $('more_'+stuffid).value != '')
						$('a_'+stuffid).href = "index.php?module="+$('more_'+stuffid).value+"&action=ListView&viewname="+$('cvid_'+stuffid).value;
				}
				if(stufftype=="Default" && typeof($('a_'+stuffid)) != 'undefined'){
					if($('more_'+stuffid).value != ''){
						$('a_'+stuffid).style.display = 'block';
						var url = "index.php?module="+$('more_'+stuffid).value+"&action=index";
						if($('search_qry_'+stuffid)!=''){
							url += $('search_qry_'+stuffid).value;
						} 
						$('a_'+stuffid).href = url;
					}else{
						$('a_'+stuffid).style.display = 'none';
					}	
				}
				if(stufftype=="RSS"){
					$('a_'+stuffid).href = $('more_'+stuffid).value;
				}
				if(stufftype=="DashBoard"){
					$('a_'+stuffid).href = "index.php?module=Dashboard&action=index&type="+$('more_'+stuffid).value;
				}	
				$('refresh_'+stuffid).innerHTML='';	
		    }
		}
	);
}

function loadAllWidgets(widgetInfoList, batchSize){
	var batchWidgetInfoList = [];
	var widgetInfo = {};
	for(var index =0 ; index < widgetInfoList.length;++index) {
		var widgetId = widgetInfoList[index].widgetId;
		var widgetType = widgetInfoList[index].widgetType;
		widgetInfo[widgetId] = widgetType;
		$('refresh_'+widgetId).innerHTML=$('vtbusy_homeinfo').innerHTML;
		batchWidgetInfoList.push(widgetInfoList[index]);
		if((index > 0 && (index+1) % batchSize == 0) || index+1 == widgetInfoList.length) {
			new Ajax.Request(
				'index.php',{
					queue: {position: 'end', scope: 'command'},
					method: 'post',
					postBody:'module=Home&action=HomeAjax&file=HomeWidgetBlockList&widgetInfoList='
						+ JSON.stringify(batchWidgetInfoList),
					onComplete: function(response){
						var responseVal=JSON.parse(response.responseText);
						for(var widgetId in responseVal) {
							if(responseVal.hasOwnProperty(widgetId)) {
								$('stuffcont_'+widgetId).innerHTML = responseVal[widgetId];
								$('refresh_'+widgetId).innerHTML='';
								var widgetType = widgetInfo[widgetId];
								if(widgetType=="Module" && $('more_'+widgetId).value != null &&
										$('more_'+widgetId).value != '') {
									$('a_'+widgetId).href = "index.php?module="+
									$('more_'+widgetId).value+"&action=ListView&viewname="+
									$('cvid_'+widgetId).value;
								} else if(widgetType == "Default" && typeof($('a_'+widgetId)) !=
										'undefined'){
									if(typeof $('more_'+widgetId) != 'undefined' &&
											$('more_'+widgetId).value != ''){
										$('a_'+widgetId).style.display = 'block';
										var url = "index.php?module="+$('more_'+widgetId).value+
											"&action=index";
										if($('search_qry_'+widgetId)!=''){
											url += $('search_qry_'+widgetId).value;
										}
										$('a_'+widgetId).href = url;
									}else{
										$('a_'+widgetId).style.display = 'none';
									}
								} else if(widgetType=="RSS"){
									$('a_'+widgetId).href = $('more_'+widgetId).value;
								} else if(widgetType=="DashBoard"){
									$('a_'+widgetId).href = "index.php?module=Dashboard&action="+
										"index&type="+$('more_'+stuffid).value;
								}
							}
						}
					}
				}
			);
			batchWidgetInfoList = [];
		}
	}
}

/**
 * this function validates the form for creating a new widget
 */
function frmValidate(){
	if(trim($('stufftitle_id').value)==""){
		alert(alert_arr.LBL_ENTER_WINDOW_TITLE);
		$('stufftitle_id').focus();
		return false;
	}
	if($('stufftype_id').value=="RSS"){			
		if($('txtRss_id').value==""){
			alert(alert_arr.LBL_ENTER_RSS_URL);
			$('txtRss_id').focus();
			return false;
		}
	}
	/*if($('stufftype_id').value=="URL"){			
		if($('url_id').value==""){
			alert("Please enter URL");
			$('url_id').focus();
			return false;
		}
	}*/
	if($('stufftype_id').value=="Module"){
		var selLen;
		var fieldval=new Array();
		var cnt=0;
		selVal=document.Homestuff.PrimeFld;
		for(k=0;k<selVal.options.length;k++){
			if(selVal.options[k].selected){
				fieldval[cnt]=selVal.options[k].value;
				cnt= cnt+1;
			}
		}
		if(cnt>2){
			alert(alert_arr.LBL_SELECT_ONLY_FIELDS);
			selVal.focus();
			return false;
		}else{
			document.Homestuff.fldname.value=fieldval;
		}
	}
	var stufftype=$('stufftype_id').value;
	var stufftitle=$('stufftitle_id').value;
	$('stufftitle_id').value = '';
	var selFiltername='';
	var fldname='';
	var selmodule='';
	var maxentries='';
	var txtRss='';
	var seldashbd='';
	var seldashtype='';
	var seldeftype='';
	//var txtURL = '';

	if(stufftype=="Module"){
		selFiltername =document.Homestuff.selFiltername[document.Homestuff.selFiltername.selectedIndex].value;
		fldname = fieldval;
		selmodule =$('selmodule_id').value;
		maxentries =$('maxentryid').value;
	}else if(stufftype=="RSS"){
		txtRss=$('txtRss_id').value;
		maxentries =$('maxentryid').value;
	}/*else if(stufftype=="URL"){
		txtURL=$('url_id').value;
	}*/else if(stufftype=="DashBoard"){
		seldashbd=$('seldashbd_id').value;
		seldashtype=$('seldashtype_id').value;
	}else if(stufftype=="Default"){
		seldeftype=document.Homestuff.seldeftype[document.Homestuff.seldeftype.selectedIndex].value;
	}

	var url="stufftype="+stufftype+"&stufftitle="+stufftitle+"&selmodule="+selmodule+"&maxentries="+maxentries+"&selFiltername="+selFiltername+"&fldname="+encodeURIComponent(fldname)+"&txtRss="+txtRss+"&seldashbd="+seldashbd+"&seldashtype="+seldashtype+"&seldeftype="+seldeftype;//+'&txtURL='+txtURL;
	var stuffarr=new Array();
	$('vtbusy_info').style.display="inline";	

	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=Homestuff&'+url,
			onComplete: function(response){
				var responseVal=response.responseText;
				if(!response.responseText){
					alert(alert_arr.LBL_ADD_HOME_WIDGET);
					$('vtbusy_info').style.display="none";
					$('stufftitle_id').value='';
					$('txtRss_id').value='';
					return false;
				}else{
					hide('addWidgetsDiv');
					$('vtbusy_info').style.display="none";
					$('stufftitle_id').value='';
					$('txtRss_id').value='';
					eval(response.responseText);
				}
			}
		}
	);
}

/**
 * this function is used to hide the default widgets
 * @param string sid - the id of the widget
 */
function HideDefault(sid){
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&stuffid='+sid+"&act=hide",
	        onComplete: function(response){
				var responseVal=response.responseText;
				if(response.responseText.indexOf('SUCCESS') > -1){
					var delchild = $('stuff_'+sid);
					odeletedChild = $('MainMatrix').removeChild(delchild);
					$('seqSettings').innerHTML= '<table cellpadding="10" cellspacing="0" border="0" width="100%" class="vtResultPop small"><tr><td align="center">'+alert_arr.LBL_WIDGET_HIDDEN+'.'+alert_arr.LBL_RESTORE_FROM_PREFERENCES+'.</td></tr></table>';
					$('seqSettings').style.display = 'block';
					$('seqSettings').style.display = 'none';
					placeAtCenter($('seqSettings'));
					Effect.Appear('seqSettings');
					setTimeout(hideSeqSettings,3000);
				}else{
					alert(alert_arr.ERR_HIDING + '.'+ alert_arr.MSG_TRY_AGAIN + '.');
				}
	        }
		}
	);
}


/**
 * this function removes the widget dropdown window
 */
function fnRemoveWindow(){
	var tagName = document.getElementById('addWidgetDropDown').style.display= 'none';
}

/**
 * this function displays the widget dropdown window
 */
function fnShowWindow(){
	var tagName = document.getElementById('addWidgetDropDown').style.display= 'block';
}

/**
 * this function is used to postion the widgets on home on page resize
 * @param string targetDiv - the id of the target widget
 * @param string stufftitle - the title of the target widget
 * @param string stufftype - the type of the target widget
 */
function positionDivInAccord(targetDiv,stufftitle,stufftype){
	var layout=$('homeLayout').value;
	var widgetWidth;
	var dashWidth;
	
	switch(layout){
		case '2':
			widgetWidth = 49;
			dashWidth = 98.6;
			break;
		case '3':
			widgetWidth = 31;
			dashWidth = 64;
			break;
		case '4':
			widgetWidth = 24;
			dashWidth = 48.6;
			break;
		default:
			widgetWidth = 24;
			dashWidth = 48.6;
			break;
	}
	var mainX = parseInt(document.getElementById("MainMatrix").style.width);
	if(stufftitle != vtdashboard_defaultDashbaordWidgetTitle && stufftype != "DashBoard"){
		var dx = mainX *  widgetWidth/ 100;
	}else{
		var dx = mainX * dashWidth / 100;
	}
	document.getElementById(targetDiv).style.width=dx + "%";
}

/**
 * this function hides the seqSettings div
 */
function hideSeqSettings(){
	Effect.Fade('seqSettings');
}

/**
 * this function fetches the homepage dashboard
 * @param string stuffid - the id of the dashboard widget
 */
function fetch_homeDB(stuffid){
	$('refresh_'+stuffid).innerHTML=$('vtbusy_homeinfo').innerHTML;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Dashboard&action=DashboardAjax&file=HomepageDB',
			onComplete: function(response){
				$('stuffcont_'+stuffid).style.display = 'none';
				$('stuffcont_'+stuffid).innerHTML=response.responseText;
				$('refresh_'+stuffid).innerHTML='';
				Effect.Appear('stuffcont_'+stuffid);
			}
		}
	);
}

/**
 * this function initializes the homepage
 */
initHomePage = function(){
	Sortable.create(
		"MainMatrix",
		{
			constraint:false,tag:'div',overlap:'Horizontal',handle:'headerrow',
			onUpdate:function(){
				matrixarr = Sortable.serialize('MainMatrix').split("&");
				matrixseqarr=new Array();
				seqarr=new Array();
				for(x=0;x<matrixarr.length;x++){
					matrixseqarr[x]=matrixarr[x].split("=")[1];
				}
				BlockSorting(matrixseqarr);	
			}
		}
	);
}

/**
 * this function is used to save the sorting order of elements when they are moved around on the home page
 * @param array matrixseqarr - the array containing the sequence of the widgets
 */
function BlockSorting(matrixseqarr){
	var sequence = matrixseqarr.join("_");
	new Ajax.Request('index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&matrixsequence='+sequence,
			onComplete: function(response){
				$('seqSettings').innerHTML=response.responseText;
				$('seqSettings').style.display = 'block';
				$('seqSettings').style.display = 'none';
				placeAtCenter($('seqSettings'));
				Effect.Appear('seqSettings');
				setTimeout(hideSeqSettings,3000);
			}
		}
	);
}


/**
 * this function checks if the current browser is IE or not
 */
function isIE(){
	return navigator.userAgent.indexOf("MSIE") !=-1;
}



/**
 * this function adds a notebook widget to the homepage
 */
function addNotebookWidget(){
	new Ajax.Request('index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&matrixsequence='+sequence,
			onComplete: function(response){
				$('seqSettings').innerHTML=response.responseText;
				$('seqSettings').style.display = 'block';
				$('seqSettings').style.display = 'none';
				placeAtCenter($('seqSettings'));
				Effect.Appear('seqSettings');
				setTimeout(hideSeqSettings,3000);
			}
		}
	);
	loadAddedDiv(stuffid,stufftype);
}


/**
 * this function takes a widget id and adds scrolling property to it
 */
function addScrollBar(id){
	$('stuff_'+id).style['overflowX'] = "scroll";
	$('stuff_'+id).style['overflowY'] = "scroll";
}

/**
 * this function will display the node passed to it in the center of the screen
 */
function showOptions(id){
	var node = $(id);
	node.style.display='block';
	placeAtCenter(node);
}

/**
 * this function will hide the node passed to it
 */
function hideOptions(id){
	Effect.Fade(id);
}

/**
 * this function will be used to save the layout option
 */
function saveLayout(){
	$('status').show();
	hideOptions('changeLayoutDiv');
	var sel = $('layoutSelect');
	var layout = sel.options[sel.selectedIndex].value;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&layout='+layout,
			onComplete: function(response){
				var responseVal=response.responseText;
				window.location.href = window.location.href;
			}
		}
	);
}
