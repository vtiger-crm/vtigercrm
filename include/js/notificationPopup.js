
/**
 * this function takes a node and displays it as a popup in the right-hand side corner of the window
 *
 * @param node - the node to display as popup
 */
function _defPopup(){
	var maxheight = 75;	//maximum height of the popup
	var incrementHeight = 2; //incremental height of the popup
	var remainOnScreen = 10 * 1000; //the time for which the popup remains on screen
	var randomID = Math.floor(Math.random()*10001);

	var popupDiv = document.createElement('div');
	var parentDiv = document.getElementById('notificationDiv');
	parentDiv.appendChild(popupDiv);
	popupDiv.id = randomID;
	popupDiv.className = "lvtCol";
	popupDiv.style.float="right"; 
	popupDiv.style.paddingRight="5px";
	popupDiv.style.overflow="hidden";
	popupDiv.style.right="0px";
	popupDiv.style.bottom="0px";
	popupDiv.style.borderColor="rgb(141, 141, 141)";
	popupDiv.style.borderTop="1px black solid";
	popupDiv.style.borderBottom="1px black solid";
	popupDiv.style.padding="2px";
	popupDiv.style.zIndex=10;
	popupDiv.style.fontWeight="normal";
	popupDiv.align="left";	//the popup to be displayed on screen
	var node;
	
	/**
	 * this function creates a popup div and displays in on the screen
	 * after a timeinterval of time seconds the popup is hidden
	 *
	 * @param node - the node to display
	 * @param height - the maximum height of the popup
	 * @param time - the time for which it is displayed
	 */
	function CreatePopup(node, time){
		parentDiv.style.display = "block";
		if(time != undefined && time != ""){
			remainOnScreen = time * 1000;
		}
		popupDiv.innerHTML = node; 
		popupDiv.style.display = "block";
		popupDiv.style.display = "";
		var dimension = getDimension(popupDiv);
		maxheight = dimension.y;
		
		popupDiv.style.height = "0px";
		ShowPopup(); 
	}
	
	/**
	 * this function is used to display the popup on screen
	 */
	function ShowPopup(){
		var height = popupDiv.style.height.substring(0,popupDiv.style.height.indexOf("px"));
		if (parseInt(height) < maxheight) { 
			height = parseInt(height) + incrementHeight;
			if(height > maxheight){
				height = maxheight;
			}
			popupDiv.style.height = height + "px"; 
			setTimeout(ShowPopup, 1); 
		} else { 
			popupDiv.style.height = maxheight + "px"; 
			setTimeout(HidePopup, remainOnScreen);
		} 
	}
	
	/**
	 * this function is used to hide the popup from screen
	 */
	function HidePopup(){
		var height = popupDiv.style.height.substring(0,popupDiv.style.height.indexOf("px"));
		if (parseInt(height) > 0) { 
			height = parseInt(height) - incrementHeight;
			if(height<0){
				height=0;
			}
			popupDiv.style.height = height+"px";
			setTimeout(HidePopup, 1); 
		} else { 
			ResetPopup();
		} 
	}
	
	/**
	 * this function is used to reset the popup
	 */
	function ResetPopup(){
		popupDiv.innerHTML = "";
		popupDiv.style.height = "0px"; 
		popupDiv.style.display = "none";
		parentDiv.style.display = "none";
	}
	
	return {
		displayPopup: CreatePopup,
		content: node
	};
}
