<!--
var today = new Date();
var currMonth = today.getMonth();
var currYear = today.getFullYear();
var shownCalendarId = '';
var shownCalendarBtn = null;
var currField = null;
var currHiddenField = null;
var currDate = new Date();
var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
var monthNamesR = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
var weekdayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
var weekdayInits = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
function setDate(dateSet, monthSet, yearSet) {
var dateToSet = new Date(yearSet, monthSet, dateSet);
if (currField) currField.value = monthNamesR[monthSet] + ' ' + dateSet + ' ' + yearSet + ' ';
if (currHiddenField) currHiddenField.value = Date.parse(dateToSet.toString());
hideCurrCalendar();
}
function showCalendar(btnElem, leerId, ancName, fieldName) {
var currCalBtn = shownCalendarBtn;
if (shownCalendarId != '') hideCurrCalendar();
if (currCalBtn != btnElem) {
currField = btnElem.form.elements[fieldName];
currHiddenField = btnElem.form.elements[fieldName + 'Ms'];
if (currHiddenField && currHiddenField.value != '') currDate.setTime(currHiddenField.value);
else currDate = new Date();
shownCalendarBtn = btnElem;
shownCalendarId = leerId;
drawCalendar(leerId, ancName);
}
}

function setDate1(dateSet, monthSet, yearSet) {
var dateToSet = new Date(yearSet, monthSet, dateSet);

var dateSetConvert = dateSet + 8;
var dateSetEnd = dateSet + 11;

var dateToSetConvert = new Date(yearSet, monthSet, dateSetConvert);
var mc = dateToSetConvert.getMonth();
var dc = dateToSetConvert.getDate(); mc = monthNamesR[mc];
var yc = dateToSetConvert.getYear(); if(yc.length = 3) yc += 1900;
var dconvert = mc + ' ' + dc + ' ' + yc;

var dateToSetEnd = new Date(yearSet, monthSet, dateSetEnd);
var me = dateToSetEnd.getMonth(); me = monthNamesR[me];
var de = dateToSetEnd.getDate();
var ye = dateToSetEnd.getYear(); if(ye.length = 3) ye += 1900;
var dend = me + ' ' + de + ' ' + ye;

if (currField) currField.value = monthNamesR[monthSet] + ' ' + dateSet + ' ' + yearSet;

document.fls.convert.value = dconvert;
document.fls.end.value = dend;

//currFieldc.value = dconvert;
//currFielde.value = dend;
/*
btnElem.form.elements[convert].value = dateToSetConvert;
btnElem.form.elements[end].value = dateToSetEnd;
*/

if (currHiddenField) currHiddenField.value = Date.parse(dateToSet.toString());
hideCurrCalendar();
}
function showCalendar1(btnElem, leerId, ancName, fieldName) {
var currCalBtn = shownCalendarBtn;
if (shownCalendarId != '') hideCurrCalendar();
if (currCalBtn != btnElem) {
currField = btnElem.form.elements[fieldName];
currHiddenField = btnElem.form.elements[fieldName + 'Ms'];
if (currHiddenField && currHiddenField.value != '') currDate.setTime(currHiddenField.value);
else currDate = new Date();
shownCalendarBtn = btnElem;
shownCalendarId = leerId;
drawCalendar1(leerId, ancName);
}
}

function hideCurrCalendar() {
if (shownCalendarId != '') hideLeer(shownCalendarId);
if (shownCalendarBtn != null && shownCalendarBtn.style) shownCalendarBtn.style.borderStyle = 'outset';
shownCalendarId = '';
shownCalendarBtn = null;
currField = null;
currHiddenField = null;
}
function drawCalendar(leerId, ancName, showYear, showMonth) {
// insetting the button
if (shownCalendarBtn != null) {
if (shownCalendarBtn.style) shownCalendarBtn.style.borderStyle = 'inset';
}
var month = new Date();
if (showMonth != null) month.setMonth(showMonth, 1);
else month.setMonth(currDate.getMonth());
if (showYear != null) month.setYear(showYear);
else month.setYear(currDate.getFullYear());
var thisMonth = month.getMonth();
var nextMonth = (thisMonth == 11)? 0 : thisMonth + 1;
var prevMonth = (thisMonth == 0)? 11 : thisMonth - 1;
var thisYear = month.getFullYear();
var nextYear = (thisMonth == 11)? thisYear + 1 : thisYear;
var prevYear = (thisMonth == 0)? thisYear - 1 : thisYear;
var isThisMonth = (month.getFullYear() == currDate.getFullYear() && month.getMonth() == currDate.getMonth())? true : false;
// table starts
var calendarHTML = '<table cellpadding="1" cellspacing="0" border="0" width="130"><tr bgcolor="#000000"><td><table cellpadding="1" cellspacing="0" border="0" width="100%"><tr bgcolor="#e9ebf1">';
// link back
calendarHTML += '<td class="calFont" style="border-top: 1px solid #fff; border-left: 1px solid #fff; padding-bottom: 2px; "><a href=""' +
'onClick="drawCalendar(\'' + leerId + '\', \'' + ancName + '\', ' + prevYear + ', ' + prevMonth +
'); return false;"><img src="images/arr-prev.gif" width="13" height="13" border="0" /><\/a><\/td>';
// month, year row
calendarHTML += '<td align="center" class="calFont" style="border-top: 1px solid #fff; padding-bottom: 2px;" nowrap="nowrap">' + monthNames[month.getMonth()] + ', ' + month.getFullYear() + '<\/td>';
// link fwd
calendarHTML += '<td class="calFont" align="right" style="border-top: 1px solid #fff; padding-bottom: 2px;"><a href="" onClick="drawCalendar(\'' +
leerId + '\', \'' + ancName + '\', ' + nextYear + ', ' +
nextMonth + '); return false;"><img src="images/arr-next.gif" width="13" height="13" border="0" /><\/a><\/td><\/tr>' +
'<tr><td colspan="3" align="center" bgcolor="#ffffff" style="padding: 0 10px; border-top: 1px solid #808080;">';
// starting the calendar table...
calendarHTML += '<table cellpadding="2" cellspacing="0" border="0" style="border-bottom: 1px solid #fff;"><tr align="right">'
// appending day initials
for (var i = 0; i < weekdayInits.length; i++) calendarHTML += '<td class="calFont" style="border-bottom: 1px solid #808080;"><small><small>' + weekdayInits[i] + '</small></small><\/td>'
calendarHTML += '<tr align="right">'
// getting the first day of the month
month.setDate(1);///
var daysToStart = (month.getDay() == 0)? 0 : month.getDay();
// drawing empty cells
for (var i = 0; i < daysToStart; i++) calendarHTML += '<td class="calFont"><br /><\/td>';
// drawing the calendar itself
for (var i = 1; i < 33; i++)
{
    month.setDate(i);
    if (month.getMonth() == thisMonth)
    {
        if (isThisMonth && currDate.getDate() == i) calendarHTML += '<td class="calFont" style="color: #0077cc;" bgcolor="#CCCCFF"><small><a href="" onClick="setDate( ' + i + ', + ' + thisMonth + ', + ' + thisYear + '); return false;">' + i + '<\/a></small><\/td>'; //calendarHTML += '<td class="calFont" style="color: #ffffff;" bgcolor="#ff7f00"><small>' + i + '</small><\/td>';
        else calendarHTML += '<td class="calFont"><small><a href="" onClick="setDate( ' + i + ', + ' + thisMonth + ', + ' + thisYear + '); return false;">' + i + '<\/a></small><\/td>';
    }
    else break;
    if (month.getDay() == 6) calendarHTML += '<\/tr><tr align="right">';
}
// drawing empty cells if any
if (month.getDay() != 1) {
var finalDay = (month.getDay() == 0)? 7 : month.getDay();
var daysToEnd = 8 - finalDay;
for (var i = 0; i < daysToEnd; i++) calendarHTML += '<td class="calFont"><br /><\/td>';
}
// tables ends
calendarHTML += '<\/tr><\/table><\/td><\/tr><\/table><\/td><\/tr><\/table>';
var leerPos = new getCalendarPosition(ancName);
if (document.getElementById) {
var leerElem = document.getElementById(leerId);
leerElem.innerHTML = calendarHTML;
leerElem.style.left = leerPos.x;
leerElem.style.top = leerPos.y;
leerElem.style.visibility = 'visible';
} else if (document.all) {
var leerElem = document.all[leerId];
leerElem.innerHTML = calendarHTML;
leerElem.style.left = leerPos.x;
leerElem.style.top = leerPos.y;
leerElem.style.visibility = 'visible';
} else if (document.layers) {
document.layers[leerId].left = leerPos.x;
document.layers[leerId].top = leerPos.y;
document.layers[leerId].document.open();
document.layers[leerId].document.write(calendarHTML);
document.layers[leerId].document.close();
document.layers[leerId].visibility = 'show';
}
}

function drawCalendar1(leerId, ancName, showYear, showMonth) {
// insetting the button
if (shownCalendarBtn != null) {
if (shownCalendarBtn.style) shownCalendarBtn.style.borderStyle = 'inset';
}
var month = new Date();
if (showMonth != null) month.setMonth(showMonth, 1);
else month.setMonth(currDate.getMonth());
if (showYear != null) month.setYear(showYear);
else month.setYear(currDate.getFullYear());
var thisMonth = month.getMonth();
var nextMonth = (thisMonth == 11)? 0 : thisMonth + 1;
var prevMonth = (thisMonth == 0)? 11 : thisMonth - 1;
var thisYear = month.getFullYear();
var nextYear = (thisMonth == 11)? thisYear + 1 : thisYear;
var prevYear = (thisMonth == 0)? thisYear - 1 : thisYear;
var isThisMonth = (month.getFullYear() == currDate.getFullYear() && month.getMonth() == currDate.getMonth())? true : false;
// table starts
var calendarHTML = '<table cellpadding="1" cellspacing="0" border="0" width="130"><tr bgcolor="#000000"><td><table cellpadding="1" cellspacing="0" border="0" width="100%"><tr bgcolor="#e9ebf1">';
// link back
calendarHTML += '<td class="calFont" style="border-top: 1px solid #fff; border-left: 1px solid #fff; padding-bottom: 2px; "><a href=""' +
'onClick="drawCalendar1(\'' + leerId + '\', \'' + ancName + '\', ' + prevYear + ', ' + prevMonth +
'); return false;"><img src="images/arr-prev.gif" width="13" height="13" border="0" /><\/a><\/td>';
// month, year row
calendarHTML += '<td align="center" class="calFont" style="border-top: 1px solid #fff; padding-bottom: 2px;" nowrap="nowrap">' + monthNames[month.getMonth()] + ', ' + month.getFullYear() + '<\/td>';
// link fwd
calendarHTML += '<td class="calFont" align="right" style="border-top: 1px solid #fff; padding-bottom: 2px;"><a href="" onClick="drawCalendar1(\'' +
leerId + '\', \'' + ancName + '\', ' + nextYear + ', ' +
nextMonth + '); return false;"><img src="images/arr-next.gif" width="13" height="13" border="0" /><\/a><\/td><\/tr>' +
'<tr><td colspan="3" align="center" bgcolor="#ffffff" style="padding: 0 10px; border-top: 1px solid #808080;">';
// starting the calendar table...
calendarHTML += '<table cellpadding="2" cellspacing="0" border="0" style="border-bottom: 1px solid #fff;"><tr align="right">'
// appending day initials
for (var i = 0; i < weekdayInits.length; i++) calendarHTML += '<td class="calFont" style="border-bottom: 1px solid #808080;"><small><small>' + weekdayInits[i] + '</small></small><\/td>'
calendarHTML += '<tr align="right">'
// getting the first day of the month
month.setDate(1);///
var daysToStart = (month.getDay() == 0)? 0 : month.getDay();
// drawing empty cells
for (var i = 0; i < daysToStart; i++) calendarHTML += '<td class="calFont"><br /><\/td>';
// drawing the calendar itself
for (var i = 1; i < 33; i++)
{
    month.setDate(i);
    if (month.getMonth() == thisMonth)
    {
        if (isThisMonth && currDate.getDate() == i) calendarHTML += '<td class="calFont" style="color: #0077cc;" bgcolor="#CCCCFF"><small><a href="" onClick="setDate1( ' + i + ', + ' + thisMonth + ', + ' + thisYear + '); return false;">' + i + '<\/a></small><\/td>'; //calendarHTML += '<td class="calFont" style="color: #ffffff;" bgcolor="#ff7f00"><small>' + i + '</small><\/td>';
        else calendarHTML += '<td class="calFont"><small><a href="" onClick="setDate1( ' + i + ', + ' + thisMonth + ', + ' + thisYear + '); return false;">' + i + '<\/a></small><\/td>';
    }
    else break;
    if (month.getDay() == 6) calendarHTML += '<\/tr><tr align="right">';
}
// drawing empty cells if any
if (month.getDay() != 1) {
var finalDay = (month.getDay() == 0)? 7 : month.getDay();
var daysToEnd = 8 - finalDay;
for (var i = 0; i < daysToEnd; i++) calendarHTML += '<td class="calFont"><br /><\/td>';
}
// tables ends
calendarHTML += '<\/tr><\/table><\/td><\/tr><\/table><\/td><\/tr><\/table>';
var leerPos = new getCalendarPosition(ancName);
if (document.getElementById) {
var leerElem = document.getElementById(leerId);
leerElem.innerHTML = calendarHTML;
leerElem.style.left = leerPos.x;
leerElem.style.top = leerPos.y;
leerElem.style.visibility = 'visible';
} else if (document.all) {
var leerElem = document.all[leerId];
leerElem.innerHTML = calendarHTML;
leerElem.style.left = leerPos.x;
leerElem.style.top = leerPos.y;
leerElem.style.visibility = 'visible';
} else if (document.layers) {
document.layers[leerId].left = leerPos.x;
document.layers[leerId].top = leerPos.y;
document.layers[leerId].document.open();
document.layers[leerId].document.write(calendarHTML);
document.layers[leerId].document.close();
document.layers[leerId].visibility = 'show';
}
}


function hideLeer(leerId) {
if (document.layers) {
document.layers[leerId].visibility = 'hide';
} else if (document.getElementById) {
document.getElementById(leerId).style.visibility = 'hidden';
} else if (document.all) {
document.all[leerId].style.visibility = 'hidden';
}
}
function ancPosX(anchorPtr) {
if (document.layers) {
return anchorPtr.x;
} else if (document.getElementById || document.all) {
var pos = anchorPtr.offsetLeft;
while (anchorPtr.offsetParent != null) {
anchorPtr = anchorPtr.offsetParent;
pos += anchorPtr.offsetLeft;
} return pos;
}
}
function ancPosY(anchorPtr) {
if (document.layers) {
return anchorPtr.y;
} else if (document.getElementById || document.all) {
var pos = anchorPtr.offsetTop;
while (anchorPtr.offsetParent != null) {
anchorPtr = anchorPtr.offsetParent;
pos += anchorPtr.offsetTop;
} return pos;
}
}
function getCalendarPosition(ancName) {
for (var i = 0; i < document.anchors.length; i++) {
if (document.anchors[i].name == ancName) {
this.x = ancPosX(document.anchors[i]);
this.y = ancPosY(document.anchors[i]);
return this;
}
}
}
function isParent(elemPtr, parentId) {
if (document.getElementById) {
//    while (elemPtr.parentNode != null) {
//    if //    }
}
return false;
}
if (document.layers) {
origWidth = window.innerWidth;
origHeight = window.innerHeight;
}
function resizing() {
if (document.layers) {
if (window.innerWidth != origWidth || window.innerHeight != origHeight) location.reload();
} else hideCurrCalendar();
}
window.onresize = resizing;
// -->