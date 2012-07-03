function init(){
	function XHR(type,url,params){
		this.xhr = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
		this.url = url;
		this.params = params;
		this.type = type;
	};
	XHR.prototype.load=function(async,callback){
		this.onload=callback;
		this.async=async;
		
		if(this.type.toLowerCase()=="get"){
			this.url = this.url+"?"+this.params;
		}
		
		this.xhr.open(this.type,this.url,this.async);
		try{
			if(async){
				var request = this;
				this.xhr.onreadystatechange=function(){
					request.readyStateChange();
				}
			}
			if(this.type.toLowerCase()=="post"){
				this.xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				this.xhr.setRequestHeader("Content-length", this.params.length);
			}
			this.xhr.send(this.params);
		}catch(e){
			alert("error: "+e);
		}
	};
	XHR.prototype.readyStateChange=function(){
		var ready=this.xhr.readyState;
		if(ready==4){
			var httpStatus=null;
			httpStatus=this.xhr.status;
			if(httpStatus==200 || httpStatus==0){
				if(this.async){
					this.onload.call(this,this.xhr);
				}
			}
		}
	};
	
	function VtigerWebservice(vtigerurl,username,accesskey){
		var vtigerURL=vtigerurl;
		var username=username;
		var accessKey=accesskey;
		var userId=null;
		var sessionId=null;
		
		function getResult(response){
			return response.result;
		}
		
		function getError(response){
			return response.error;
		}
		
		function extendsession(){
			var req = new XHR("post",vtigerURL,"operation=extendsession");
			req.load(false);
			var res = JSON.parse(req.xhr.responseText);
			if(res.success){
				var result = getResult(res);
				this.userId=result.userId;
				this.sessionId=result.sessionName;
			}
		}
		
		function create(object, objectType, callback){
			var objectJson = JSON.stringify(object);
			var req = new XHR("post",document.vtigerURL,("operation=create&elementType="+objectType+"&sessionName="+this.sessionId+
				"&element="+encodeURIComponent(objectJson)));
			req.load(true,callback);
		}
		
		function query(query, callback){
			var req = new XHR("get",document.vtigerURL,"operation=query&query="+encodeURIComponent(query)+"&sessionName="+this.sessionId);
			req.load(true,callback);
		}
		return {extendSession:extendsession,query:query,getResult:getResult,userId:userId,create:create,getError:getError};
	};
	if (!this.JSON) {
		JSON = function () {
			function f(n) {
				return n < 10 ? "0" + n : n;
			}
			Date.prototype.toJSON = function () {
				return this.getUTCFullYear()   + "-" +
					 f(this.getUTCMonth() + 1) + "-" +
					 f(this.getUTCDate())	  + "T" +
					 f(this.getUTCHours())	 + ":" +
					 f(this.getUTCMinutes())   + ":" +
					 f(this.getUTCSeconds())   + "Z";
			};
			var m = {
				"\b": "\\b",
				"\t": "\\t",
				"\n": "\\n",
				"\f": "\\f",
				"\r": "\\r",
				"\"" : "\\\"",
				"\\": "\\\\"
			};
			function stringify(value, whitelist) {
				var a,		  // The array holding the partial texts.
					i,		  // The loop counter.
					k,		  // The member key.
					l,		  // Length.
					r = /["\\\x00-\x1f\x7f-\x9f]/g,
					v;		  // The member value.
				switch (typeof value) {
				case "string":
					return r.test(value) ?
						"\"" + value.replace(r, function (a) {
							var c = m[a];
							if (c) {
								return c;
							}
							c = a.charCodeAt();
							return "\\u00" + Math.floor(c / 16).toString(16) +
													   (c % 16).toString(16);
						}) + "\"" :
						"\"" + value + "\"";
				case "number":
					return isFinite(value) ? String(value) : "null";
				case "boolean":
				case "null":
					return String(value);
				case "object":
					if (!value) {
						return "null";
					}
					if (typeof value.toJSON === "function") {
						return stringify(value.toJSON());
					}
					a = [];
					if (typeof value.length === "number" &&
							!(value.propertyIsEnumerable("length"))) {
						l = value.length;
						for (i = 0; i < l; i += 1) {
							a.push(stringify(value[i], whitelist) || "null");
						}
						return "[" + a.join(",") + "]";
					}
					if (whitelist) {
						l = whitelist.length;
						for (i = 0; i < l; i += 1) {
							k = whitelist[i];
							if (typeof k === "string") {
								v = stringify(value[k], whitelist);
								if (v) {
									a.push(stringify(k) + ":" + v);
								}
							}
						}
					} else {
						for (k in value) {
							if (typeof k === "string") {
								v = stringify(value[k], whitelist);
								if (v) {
									a.push(stringify(k) + ":" + v);
								}
							}
						}
					}
					return "{" + a.join(",") + "}";
				}
							return undefined;
			}
			return {
				stringify: stringify,
				parse: function (text, filter) {
					var j;
					function walk(k, v) {
						var i, n;
						if (v && typeof v === "object") {
							for (i in v) {
								if (Object.prototype.hasOwnProperty.apply(v, [i])) {
									n = walk(i, v[i]);
									if (n !== undefined) {
										v[i] = n;
									}
								}
							}
						}
						return filter(k, v);
					}
					if (/^[\],:{}\s]*$/.test(text.replace(/\\./g, "@").
	replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(:?[eE][+\-]?\d+)?/g, "]").
	replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) {
						j = eval("(" + text + ")");
						return typeof filter === "function" ? walk("", j) : j;
					}
					throw new SyntaxError("parseJSON");
				}
			};
		}();
	}
	
    function trim(str) {
		var s = str.replace(/\s+$/, "");
		s = s.replace(/^\s+/, "");
		return s;
	}
	
	function getGmailSubject() {
		var subject = top.document.title;
		var firstIndexOf = subject.indexOf("-");
		var lastIndexOf = subject.lastIndexOf("-");
		subject = subject.substring(firstIndexOf+1, lastIndexOf-1);
		return trim(subject);
	}
	
	function getVtigerBaseURL(){
		var url = document.location.href.substring(0,document.location.href.lastIndexOf('/')+1);
		if(url.length > 0){
			if(url.charAt(url.length-1) !="/"){
				url += "/";
			}
			return url;
		}
		return null;
	}
	
	function getVtigerURL(){
		if(document.vtigerBaseURL.length > 0){
			var url =document.vtigerBaseURL;
			url +="webservice.php";
			return url;
		}
		return null;
	}
	
	function getGmailURL() {
		var locationhref = location.href;
		if(locationhref.indexOf("?")) {
			var lsplits = locationhref.split("?");
			locationhref = lsplits[0];
		} else if(locationhref.indexOf("#")) {
			var lsplits = locationhref.split("#");
			locationhref = lsplits[0];
		}
		return locationhref;
	}
	
	var elementId = "__vtigerBookMarkletDiv__";
	var busyElementId = "__vtigerBookMarkletDivBusy__"
	function showBookMarkletUI(){
		var bookMarkletDiv = document.getElementById(elementId);
		if(bookMarkletDiv == null){ 
			bookMarkletDiv.style.display="block";
		}
	}
	
	function hideBookMarkletUI(){
		var bookMarkletDiv = document.getElementById(elementId);
		if(bookMarkletDiv != null){
			bookMarkletDiv.style.display="none";
		}
	}
	
	function destroyBookMarkletUI(){
		var closeElementId = '__vtigetGmailCloseElement';
		var parentLocation = location.href.split("location=");
		if(parentLocation.length>1){
			var closeElement = document.getElementById(closeElementId);
			if(closeElement==null){
				closeElement = document.createElement("iframe");
				closeElement.style.width="0px";
				closeElement.frameBorder="0px";
				closeElement.style.height="0px";
				closeElement.style.display="none";
				closeElement.id = closeElementId;
				closeElement.src = decodeURIComponent(parentLocation[1])+"#";
				document.body.appendChild(closeElement);
			}
			closeElement.onload=function(){
				eval('window.parent.parent.removeMe()');
			}
		}
	}
	
	function showBusy(){
		var bookMarkletDiv = document.getElementById(elementId);
		var busyElem = document.getElementById(busyElementId);
		if(busyElem==null){
			busyElem = document.createElement('div');
			busyElem.id = busyElementId;
			busyElem.innerHTML="Working...";
			busyElem.style.position="absolute";
			busyElem.style.top="5px";
			busyElem.style.right="5px";
			busyElem.style.color="white";
			busyElem.style.backgroundColor="#D75235";
			busyElem.style.padding="2px";
			bookMarkletDiv.appendChild(busyElem);
		}else{
			busyElem.style.display="block";
		}
		
	}
	
	function hideBusy(){
		var busyElem = document.getElementById(busyElementId);
		if(busyElem!=null){
			busyElem.style.display="none";
		}
	}
	
	if(typeof document.vtigerURL =="undefined" || document.vtigerURL == null || document.vtigerURL == ""){
		document.vtigerBaseURL = getVtigerBaseURL();
		if(document.vtigerBaseURL ==null){
			alert("Please Provide a Valid URL");
			return;
		}
		document.vtigerURL = getVtigerURL();
	}
	var client = new VtigerWebservice(document.vtigerURL,null,null);
	
	showBusy();
	client.extendSession();
	hideBusy();
	showBookMarkletUI();
	
	function onReady(id,callback){
		var interval = window.setInterval(function(){
			var elem = document.getElementById(id);
			if(elem != null && typeof elem != "undefined"){
				callback();
				window.clearInterval(interval);
			}
		},10);
	}
	
	function createBookMarkletUI(){
			
		onReady("__saveVtigerEmail__",function(){
			document.getElementById("__saveVtigerEmail__").onclick=function(){
				createEmail();
			}
		});
		onReady("parentType",function(){
			document.getElementById("parentName").innerHTML = "No "+document.getElementById("parentType").value+" Selected.";
			document.getElementById("parentType").onchange=function(){
				document.getElementById("parentName").innerHTML = "No "+this.value+" Selected.";
			}
		});
		
		function getQuery(searchValue){
			
			var moduleName = document.getElementById("parentType").value;
			var moduleDetails = JSON.parse(moduleNameFields);
			var entityNameFields = moduleDetails[moduleName];
			var selectFields = '';
			var whereFields = '';
			each(entityNameFields,function(k,v){
				if(selectFields.length > 0){
					selectFields +=',';
				}
				selectFields += v;
				if(whereFields.length > 0){
					whereFields +=" or ";
				}
				whereFields += v+" like '%"+searchValue+"%'";
			});

			var moduleEmailDetails = JSON.parse(moduleEmailFields);
			var entityEmailFields = moduleEmailDetails[moduleName];
			each(entityEmailFields, function(k,v){
				if(selectFields.length > 0) {
					selectFields +=',';
				}
				selectFields += v;
			});
			return "select "+selectFields+" from "+moduleName+" where "+whereFields+";";
		}
		
		onReady("__searchVtigerAccount__",function(){
			document.getElementById("__searchVtigerAccount__").onclick=function(e){
				var elem = document.getElementById("__vtigerAccountSearchList___");
				elem.style.display="";
				
				var accountName = document.getElementById("__searchaccount__").value;
				if(accountName.length < 1){
					alert("Please enter the search criteria");
					return;
				}
				showBusy();
				var q = getQuery(accountName);
				var moduleName = document.getElementById("parentType").value;
				var responseElem = document.getElementById("__vtigerAccountSearchResponse___");
				if(responseElem != null){
					responseElem.innerHTML = '';
				}
				client.query(q,function(response){
					var responseElem = document.getElementById("__vtigerAccountSearchResponse___");
					if(responseElem == null){
						var sibling = document.createElement("tr");
						var td = document.createElement("td");
						td.colSpan = "3";
						str = "<div id=\"__vtigerAccountSearchResponse___\" "+
								"style=\"width: 100%;overflow: auto;\"> </div>";
						td.innerHTML = str;
						sibling.appendChild(td);
						elem.parentNode.appendChild(sibling);
					}
					onReady("__vtigerAccountSearchResponse___",function(){
						displaySearchResult(moduleName,response,accountName);
					});
				});
			}
		});
	}
	
	function getSiblingByTagName(elem,tagName){
		var sibling = elem.nextSibling;
		while(sibling.nodeName.toLowerCase()!=tagName.toLowerCase()){
			sibling = sibling.nextSibling;
		}
		return sibling;
	}
	
	function each(object,callback){
		var name, i = 0, length = object.length;
		if ( length == undefined ) {
			for ( name in object )
				if ( callback.call( object[ name ], name, object[ name ] ) === false )
					break;
		} else
			for ( var value = object[0];
				i < length && callback.call( value, i, value ) !== false; value = object[++i] ){}
		return object;
	}
	
	function findObject(array,needle){
		var name, i = 0, length = array.length;
		var prospect = null;
		for (; i < length; ++i ){
			var object = array[i];
			for ( name in object ){
				if(object[name] === needle){
					prospect=object;
					break;
				}
			}
		}	
		return prospect;
	}
	
	function getEntityName(moduleName,row){
		var moduleDetails = JSON.parse(moduleNameFields);
		var entityNameFields = moduleDetails[moduleName];
		var entityName = '';
		each(entityNameFields,function(k,v){
			if(entityName.length>0){
				entityName += " ";
			}
			entityName +=row[v]; 
		});
		return entityName;
	}
	
	function getEntityEmail(moduleName,row){
		var moduleDetails = JSON.parse(moduleEmailFields);
		var entityEmailFields = moduleDetails[moduleName];
		var entityEmail = '';
		each(entityEmailFields,function(k,v){
			if(entityEmail.length > 0){
				return;
			}
			entityEmail +=row[v];
		});
		return entityEmail;
	}

	function displaySearchResult(moduleName,response,accountName){
		hideBusy();
		var queryResponse = JSON.parse(response.responseText);
		if(queryResponse.success == true){
			var queryResult = client.getResult(queryResponse);
			var str ="<ul class='searchResult'>";
			if(queryResult.length > 0){
				each(queryResult, function(i, row){
					var entityName = getEntityName(moduleName,row);
					str+="<li><a id=\""+row['id']+"\" class='small searchLinks'>"+
						entityName+"</a></li>";
				});
			}else{
				str +="<li>No Record Match \""+accountName+"\"</li>";
			}
			str += "</ul>";
			var elem = document.getElementById("__vtigerAccountSearchResponse___");
			elem.style.height="120px";
			elem.innerHTML = str;
			each(elem.getElementsByTagName("a"),function(i,v){
				v.onclick=function(){
					var elem = findObject(queryResult,this.id);
					var entityName = getEntityName(moduleName,elem);
					var entityEmail = getEntityEmail(moduleName, elem);
					if(entityEmail.length <=0) {
						alert("'"+entityName+"' has no email, please select another "+moduleName);
					}
					setDetails(elem.id,entityName, entityEmail);
					var wrap = document.getElementById("__vtigerAccountSearchList___");
					wrap.style.display="none";
					document.getElementById('__searchaccount__').value='';
				}
			});
		}else{
			var error = client.getError(queryResponse);
			alert("Vtiger returned Error: \nerrorCode: "+error.code+"\nerror Message: "+error.message);
		}
	}
	
	function waitForObject(obj,callback){
		var interval = window.setInterval(function(){
			if(typeof obj != "undefined"){
				callback();
				window.clearInterval(interval);
			}
		},10);
	}
	
	function setDetails(id, entityName, entityEmail){
		var elem = document.getElementById("parent_id");
		var elemName = document.getElementById("parentName");
		var elemEmail = document.getElementById("parentEmail");
		elem.value = id;
		elemName.innerHTML = entityName;
		elemEmail.value = entityEmail;
	}
	
	function closeOnSuccess(response){
		var createResponse = JSON.parse(response.responseText);
		if(createResponse.success == true){
			alert("Email added to vtigerCRM.");
		}else{
			document.getElementById("__saveVtigerEmail__").disabled=false;
			var error = client.getError(createResponse);
			alert("Error while creating: \nerrorCode: "+error.code+"\nerror Message: "+error.message);
		}
	}
	
	function getTodayDate(format){
		var date = new Date();
		return date.getDay()+"-"+date.getMonth()+"-"+date.getFullYear();
	}
	
	function createEmail(){
		var parent_id = document.getElementById("parent_id").value;
		var type = document.getElementById("parentType").value;
		var userEmail = document.getElementById("userEmail").value;
		var entityEmail = document.getElementById("parentEmail").value;
		
		if(parent_id.length < 1){
			alert("No "+type+" selected.");
			return ;
		}
		var subject = document.getElementById("subject").value;
		if(subject.length < 1){
			alert("Please provide a value for Subject");
			return;
		}
		
		var description = document.getElementById("description").value;
		if(description.length < 1){
			alert("Please provide a value for Body of the email.");
			return;
		}
		document.getElementById("__saveVtigerEmail__").disabled=true;
		var email ={"description":description,"subject":subject,
			"assigned_user_id":client.userId,"date_start":getTodayDate(),"activitytype":"Emails",
			"parent_id": parent_id,'from_email': userEmail,'saved_toid':entityEmail,
			'email_flag':'SAVED'};
		client.create(email,"Emails",closeOnSuccess) 
	}
	createBookMarkletUI();
}
