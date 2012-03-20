if (document.getElementById){

fCol='#000000'; //face/number colour.
dCol='#cccccc'; //dot colour.
hCol='#000000'; //hours colour.
mCol='#000000'; //minutes colour.
sCol='#ff0000'; //seconds colour.
cCol='#000000'; //date colour.
aCol='#999999'; //am-pm colour.
bCol='#ffffff'; //select/form background colour.
tCol='#000000'; //select/form text colour.

//Alter nothing below! Alignments will be lost!
y=87;
xpos=60;
h=4;
m=5;
s=6;
cf=new Array();
cd=new Array();
ch=new Array();
cm=new Array();
cs=new Array();
face="3 4 5 6 7 8 9 10 11 12 1 2";
face=face.split(" ");
n=face.length;
e=360/n;
hDims=7;
zone=0;
isItLocal=true;
ampm="";
daysInMonth=31;
todaysDate="";
var addHours;
var oddMinutes;
var getOddMinutes;
var addOddMinutes;
plusMinus=false;

var mon=new Array("January","February","March","April","May","June","July","August","September","October","November","December");

document.write('<div id="theDate" class="datestyle" style="color:'+cCol+'">\!<\/div>');
document.write('<div id="amOrPm" class="ampmstyle" style="color:'+aCol+'">\!<\/div>');
for (i=0; i < n; i++){
 document.write('<div id="theFace'+i+'" class="facestyle" style="color:'+fCol+'">'+face[i]+'<\/div>');

 cf[i]=document.getElementById("theFace"+i).style;
 cf[i].top=y-6+30*1.4*Math.sin(i*e*Math.PI/180)+"px";
 cf[i].left=xpos-6+30*1.4*Math.cos(i*e*Math.PI/180)+"px";
}
for (i=0; i < n; i++){}
for (i=0; i < h; i++){
 document.write('<div id="H'+i+'" class="handsanddotsstyle" style="background-color:'+hCol+'"><\/div>');
 ch[i]=document.getElementById("H"+i).style;
}
for (i=0; i < m; i++){
 document.write('<div id="M'+i+'" class="handsanddotsstyle" style="background-color:'+mCol+'"><\/div>');
 cm[i]=document.getElementById("M"+i).style;
}
for (i=0; i < s; i++){
 document.write('<div id="S'+i+'" class="handsanddotsstyle" style="background-color:'+sCol+'"><\/div>');
 cs[i]=document.getElementById("S"+i).style;
}

var dsp1=document.getElementById("amOrPm").style;
var dsp2=document.getElementById("theCities").style;
var dsp3=document.getElementById("theDate").style;
//var dsp4=document.getElementById("city").style;
var dsp5=document.getElementById("theClockLayer").style;
dsp1.top=y+"px";
dsp1.left=xpos-8+"px";
dsp2.top=y-80+"px";
dsp2.left=xpos-55+"px";
dsp3.top=y+55+"px";
dsp3.left=xpos-60+"px";

dsp5.backgroundImage="url(themes/images/clock_bg.gif)"
dsp5.backgroundRepeat="no-repeat"
dsp5.backgroundPosition="4px 38px"

function lcl(currIndex,localState){
	zone=document.frmtimezone.clockcity.options[currIndex].value;
	isItLocal=localState;
	plusMinus=(zone.charAt(0) == "-")?true:false;
	oddMinutes=(zone.indexOf(".") != -1)?true:false;
	if (oddMinutes){
	 getOddMinutes=zone.substring(zone.indexOf(".")+1,zone.length)
	}
	
	addHours=(oddMinutes)?parseInt(zone.substring(0,zone.indexOf("."))):parseInt(zone)
	if (plusMinus){
	 addOddMinutes=(oddMinutes)?parseInt(-getOddMinutes):0;
	} else{
	 addOddMinutes=(oddMinutes)?parseInt(getOddMinutes):0;
	}
	
	set_cookie("timezone",currIndex)
}

function ClockAndAssign(){
	hourAdjust=0;
	dayAdjust=0;
	monthAdjust=0;
	now=new Date();
	
	secs=now.getSeconds();
	sec=Math.PI*(secs-15)/30;
	
	mins=(isItLocal)?now.getMinutes():now.getUTCMinutes();
	if (oddMinutes){ 
	 mins=eval(mins+addOddMinutes);
	}
	min=Math.PI*(mins-15)/30;
	if (mins<0){
	 mins+=60;hourAdjust=-1;
	}
	if (mins>59){
	 mins-=60;hourAdjust=1;
	}
	
	hr=(isItLocal)?now.getHours()+hourAdjust:now.getUTCHours()+addHours+hourAdjust
	hrs=Math.PI*(hr-3)/6+Math.PI*parseInt(now.getMinutes())/360;

	if (!isItLocal){
	  if (addHours<0){
		if(now.getUTCHours()+parseInt(addHours)<0)
		  dayAdjust-=1
	  } else{
		if(now.getUTCHours()+parseInt(addHours)>23)
		  dayAdjust+=1
	  }
	}
	
	day=now.getDate()+dayAdjust;
	
	if (day<1){
	 day+=daysInMonth; 
	 monthAdjust=-1;
	}
	if (day>daysInMonth){
	 day-=daysInMonth; 
	 monthAdjust=1;
	}
	
	month=parseInt(now.getMonth()+1+monthAdjust);
	
	if (month==2){
	 daysInMonth=28;
	}
	year=now.getYear();
	if (year<2000){
	 year=year+1900;
	}
	leap_year=(eval(year%4)==0)?true:false;
	if (leap_year&&month==2){
	 daysInMonth=29;
	}
	if (month<1){
	 month+=12;
	 year--;
	}
	if (month>12){
	 month-=12;
	 year++;
	}
	todaysDate=mon[month-1]+" "+day+", "+year;
	
	if (hr<0) hr+=24;
	if (hr>23) hr-=24;
	
	ampm=(hr>11)?"PM":"AM";
	
	for (i=0;i<s;i++){
	 cs[i].top=y+(i*hDims)*Math.sin(sec)+"px";
	 cs[i].left=xpos+(i*hDims)*Math.cos(sec)+"px";
	}
	for (i=0;i<m;i++){
	 cm[i].top=y+(i*hDims)*Math.sin(min)+"px";
	 cm[i].left=xpos+(i*hDims)*Math.cos(min)+"px";
	}
	for (i=0;i<h;i++){
	 ch[i].top=y+(i*hDims)*Math.sin(hrs)+"px";
	 ch[i].left=xpos+(i*hDims)*Math.cos(hrs)+"px";
	}
	
	document.getElementById("amOrPm").firstChild.data=ampm;
	
	if (hr==0)
	{
		 hr=12
	}
	else if (hr>12)
	{
		 hr-=12;
	}

	
	if (mins.toString().length==1) mins="0"+mins;
	
	document.getElementById("theDate").firstChild.data=todaysDate+" "+hr+":"+mins+" "+ampm;
	setTimeout('ClockAndAssign()',100);
	}
	ClockAndAssign();
}


if (get_cookie("timezone")==null || get_cookie("timezone")==false || get_cookie("timezone")<0 || get_cookie("timezone")=="1") {
	lcl(0,true)
} else {
	lcl(get_cookie("timezone"),false)
	document.frmtimezone.clockcity.options[get_cookie("timezone")].selected=true
}
