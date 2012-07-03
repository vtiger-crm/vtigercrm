<!-- Original:  Steve Dulaney -->
<!-- Web Site:  http://www.hmhd.com/steve -->

<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->

<!-- Begin
var Memory = 0;
var Number1 = "";
var Number2 = "";
var NewNumber = "blank";
var opvalue = "";

function Display(displaynumber) {
document.calculator.answer.value = displaynumber;
}

function MemoryClear() {
Memory = 0;
//document.calculator.mem.value = "";
document.calculator.mem.style.color = "#BBB";
}

function MemoryRecall(answer) {
if(NewNumber != "blank") {
Number2 += answer;
} else {
Number1 = answer;
}
NewNumber = "blank";
Display(answer);
}

function MemorySubtract(answer) {
Memory = Memory - eval(answer);
}

function MemoryAdd(answer) {
Memory = Memory + eval(answer);
document.calculator.mem.style.color = "#000";
//document.calculator.mem.value = "M";
NewNumber = "blank";
}

function ClearCalc() {
Number1 = "";
Number2 = "";
NewNumber = "blank";
Display("");
}

function Backspace(answer) {
answerlength = answer.length;
answer = answer.substring(0, answerlength - 1);
if (Number2 != "") {
Number2 = answer.toString();
Display(Number2);
} else {
Number1 = answer.toString();
Display(Number1);
   }
}

function CECalc() {
Number2 = "";
NewNumber = "yes";
Display("");
}

function CheckNumber(answer) {
if(answer == ".") {
Number = document.calculator.answer.value;
if(Number.indexOf(".") != -1) {
answer = "";
   }
}

if(NewNumber == "yes") {
Number2 += answer;
Display(Number2);
}
else {
if(NewNumber == "blank") {
Number1 = answer;
Number2 = "";
NewNumber = "no";
}
else {
Number1 += answer;
}
Display(Number1);
   }
}
function AddButton(x) {
if(x == 1) EqualButton();
if(Number2 != "") {
Number1 = parseFloat(Number1) + parseFloat(Number2);
}
NewNumber = "yes";
opvalue = '+';
Display(Number1);
}
function SubButton(x) {
if(x == 1) EqualButton();
if(Number2 != "") {
Number1 = parseFloat(Number1) - parseFloat(Number2);
}
NewNumber = "yes";
opvalue = '-';
Display(Number1);
}
function MultButton(x) {
if(x == 1) EqualButton();
if(Number2 != "") {
Number1 = parseFloat(Number1) * parseFloat(Number2);
}
NewNumber = "yes";
opvalue = '*';
Display(Number1);
}
function DivButton(x) {
if(x == 1) EqualButton();
if(Number2 != "") {
Number1 = parseFloat(Number1) / parseFloat(Number2);
}
NewNumber = "yes";
opvalue = '/';
Display(Number1);
}
function SqrtButton() {
Number1 = Math.sqrt(Number1);
NewNumber = "blank";
Display(Number1);
}
function PercentButton() {
if(NewNumber != "blank") {
Number2 = eval(Number1+opvalue+Number2);
Number2 = Number2 * .01;
NewNumber = "blank";
Display(Number2);
}
}
function RecipButton() {
Number1 = 1/Number1;
NewNumber = "blank";
Display(Number1);
}
function NegateButton() {
Number1 = parseFloat(-Number1);
NewNumber = "no";
Display(Number1);
}
function EqualButton(x) {
if(opvalue == '+') AddButton(0);
if(opvalue == '-') SubButton(0);
if(opvalue == '*') MultButton(0);
if(opvalue == '/') DivButton(0);
if (x==0) NewNumber="blank";
//if (typeof(x)!="undefined" && typeof(parentField)!="undefined")
//	if (x==0) if (getOpenerObj(parentField)) getOpenerObj(parentField).value=Number1;
Number2 = "";
opvalue = "";
}
//  End -->