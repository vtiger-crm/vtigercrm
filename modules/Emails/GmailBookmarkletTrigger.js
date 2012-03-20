javascript:(function(){var doc = top.document;
var elementId="__vtigerBookMarkletDivWrap__";
if(navigator.userAgent.toLowerCase().indexOf("msie") == -1){
	var bookMarkletDiv = doc.getElementById(elementId);
	if(bookMarkletDiv == null){
		bookMarkletDiv = doc.createElement("div");
		bookMarkletDiv.style.border = "2px solid #73ABFA";
		bookMarkletDiv.style.backgroundColor = "white";
		bookMarkletDiv.innerHTML = "";
		bookMarkletDiv.id = elementId;
		bookMarkletDiv.style.position="absolute";
		bookMarkletDiv.style.top="5px";
		bookMarkletDiv.style.right="35px";
		bookMarkletDiv.style.width="562px";
		bookMarkletDiv.style.height="450px";
		bookMarkletDiv.style.zIndex="100000";
		bookMarkletDiv.innerHTML="&nbsp;";
		bookMarkletDiv.style.overflow = 'hidden';
		bookMarkletDiv.style.fontSize = '11px';
		bookMarkletDiv.style.fontFamily = 'Arial, sans-serif';
		
		var closeMe = doc.createElement("span");
		closeMe.style.position="absolute";
		closeMe.style.top="5px";
		closeMe.style.right="5px";
		closeMe.style.color="#0070BA";
		closeMe.style.textDecoration="underline";
		closeMe.style.width="100%";
		closeMe.style.textAlign="right";
		closeMe.style.cursor="pointer";
		closeMe.innerHTML = "<img src='"+ doc.vtigerURL + "themes/images/close.gif' title='Close Window' " +
				"alt='Close Window' border=0>";
		closeMe.onclick = function(){
			window.removeMe();
		}
		bookMarkletDiv.appendChild(closeMe);
		
		var popUp = doc.createElement("iframe");
		popUp.id="__vtigerBookmarkletFrame__";
		popUp.name="__vtigerBookmarkletFrame__";
		popUp.style.border="0px";
		popUp.frameBorder="0px";
		popUp.marginHeight="0px";
		popUp.marginWidth="0px";
		popUp.style.width="100%";
		popUp.style.height="100%";
		popUp.src = getTargetURL();
		bookMarkletDiv.appendChild(popUp);
		doc.body.appendChild(bookMarkletDiv);
	}else{
		bookMarkletDiv.style.display="block";
	}
}else{
	var vtigerGmailBookmarkletWindow = window.open(getTargetURL(),elementId,
		"scrollbars=1,top=40,left=40,resizable=1,height=450,width=562");
}

function getTargetURL(){
	var threadid = top.location.hash.split("/");
	var mailId = threadid[threadid.length-1];
	var description = encodeURIComponent(getGmailURL()+"?fs=1&tf=1&source=atom&view=cv&search=all&shva=1&th="+mailId);
	return doc.vtigerURL+"index.php?module=Emails&action=SaveBookmarklet&subject="+
			encodeURIComponent(getGmailSubject())+"&description="+description+
			"&location="+encodeURIComponent(getGmailURL());
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

function getGmailURL() {
	var locationhref = location.href;
	if(locationhref.indexOf("?")!==-1) {
		var lsplits = locationhref.split("?");
		locationhref = lsplits[0];
	} else if(locationhref.indexOf("#")!==-1) {
		var lsplits = locationhref.split("#");
		locationhref = lsplits[0];
	}
	return locationhref;
}
window.top.removeMe = function(){
	if(navigator.userAgent.toLowerCase().indexOf("msie") == -1){
		doc.body.removeChild(bookMarkletDiv);
	}else{
		vtigerGmailBookmarkletWindow.close();
	}
}
})();