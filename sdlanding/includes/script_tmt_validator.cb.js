/** 
* Copyright 2006 Peimic.com
* @author      Michael Cannon <michael@peimic.com>
* @version     $Id: script_tmt_validator.cb.js,v 1.1.1.1 2010/04/15 09:42:35 peimic.comprock Exp $
 */

// This global objects store all the RegExp patterns for strings
// tmt_globalPatterns.email = new RegExp("^[\\w\\.=-]+@[\\w\\.-]+\\.[\\w\\.-]{2,4}$");
// tmt_globalPatterns.lettersonly = new RegExp("^[a-zA-Z]*$");
// tmt_globalPatterns.filepath = new RegExp("\\\\[\\w_]*\\.\\w{3}$");
tmt_globalPatterns.usPhone = new RegExp("^([1-9]\\d{2}-?){2}\\d{4}$");
tmt_globalPatterns.ssn = new RegExp("^\\d{3}-\\d{2}-\\d{4}$");
tmt_globalPatterns.money = new RegExp("^(((\\d{1,3},)+\\d{3})|\\d+)(\.\\d{2})?$");

// This global objects store all the info required for filters
// tmt_globalFilters.ltrim = tmt_filterInfo("^(\\s*)(\\b[\\w\\W]*)$", "$2");
// tmt_globalFilters.commastodots = tmt_filterInfo(",", ".");

// By defining a callback function a developer can display errors the way he see
// fit

// This function deliberately avoid using innerHTML (a non-standard shortcut)
// since we want to have the sample working even if served with a xml mime-type. 
// So we stick with 100% standard DOM
function displayError(formNode, validators){
	var listNode = document.createElement("ul");
	for(var i=0;i<validators.length;i++){
	   var itemNode = document.createElement("li");
	   itemNode.appendChild(document.createTextNode(validators[i].message));
	   listNode.appendChild(itemNode);
	}
	var displayNode = document.getElementById("errorDisplay");
	displayNode.style.display = "block";
	displayNode.replaceChild(listNode, displayNode.firstChild);
}