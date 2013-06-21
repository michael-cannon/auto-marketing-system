var listing_feature_done = 0;

function PostData(strURL, strResultFunc)
{
	var xmlHttpReq = CreateReqInstance();

	xmlHttpReq.open('POST', strURL, true);
	xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xmlHttpReq.onreadystatechange = function() 
	{
		if (xmlHttpReq.readyState == 4) 
		{
			eval(strResultFunc + '(xmlHttpReq.responseText);');
		}
	}
	xmlHttpReq.send(null);
}

function CreateReqInstance()
{
	var xmlHttpReq = false;

	// Mozilla/Safari
	if(window.XMLHttpRequest)
	{
		xmlHttpReq = new XMLHttpRequest();
		xmlHttpReq.overrideMimeType('text/xml');
	}
	// IE
	else if(window.ActiveXObject)
	{
		xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
	}

	return xmlHttpReq;
}

function ParseXML(XMLString)
{
	// Check for IE not existing
	if(typeof DOMParser == "undefined")
	{
		DOMParser = function () {}

		DOMParser.prototype.parseFromString = function (str)
		{
			var d = new ActiveXObject("MSXML.DomDocument");
			d.loadXML(str);
			return d;
		}
	}

	// Mozilla-based, Safari, etc.
	var parser = new DOMParser();
	return parser.parseFromString(XMLString, "text/xml");
}

function verifyUserID(value)
{
	var regex = /\D/;
	var matches = value.toString().match(regex);
	if(matches)
	{
		alert("Please only insert numerical values in the User ID field");
	}
}

function sendUserId(value)
{
	PostData('index.php?a=200&action=update_username&user_id='+value, "updateUsername");
}

function updateUsername(Response)
{
	if(!Response)
		return;

	var xmlDoc = ParseXML(Response);

	var root = xmlDoc.getElementsByTagName("root")[0];

	// Check for errors
	if(root.getElementsByTagName("error")[0])
	{
		alert(root.getElementsByTagName("error")[0].firstChild.nodeValue);
	}
	else if(root.getElementsByTagName("username")[0])
	{
		document.getElementById('c[username]').value = root.getElementsByTagName("username")[0].firstChild.nodeValue;
	}
}

function sendUsername(value)
{
	PostData('index.php?a=200&action=update_user_id&username='+value, "updateUserId");
}

function updateUserId(Response)
{
	if(!Response)
		return;

	var xmlDoc = ParseXML(Response);

	var root = xmlDoc.getElementsByTagName("root")[0];

	// Check for errors
	if(root.getElementsByTagName("error")[0])
	{
		alert(root.getElementsByTagName("error")[0].firstChild.nodeValue);
	}
	else if(root.getElementsByTagName("user_id")[0])
	{
		document.getElementById('c[user_id]').value = root.getElementsByTagName("user_id")[0].firstChild.nodeValue;
	}
}

function Submit()
{
	var iframe = document.getElementById('preview_block_iframe').contentDocument ? document.getElementById('preview_block_iframe').contentDocument : document.getElementById('preview_block_iframe').contentWindow.document;
	var query_string = "";
	// Do some error checking first
	// Check the durations
	var duration_fixed = document.getElementById('duration_fixed');
	var duration_variable = document.getElementById('duration_variable');
	if(duration_fixed && duration_variable)
	{
		if(!duration_fixed.checked && !duration_variable.checked)
		{
			alert('Please select a duration or start and end time before continuing.');
			return false;
		}
	}
	else
		// We were unable to get the variables
		return false;

	// Check the column values
	var i = 0;
	while(field = iframe.getElementById('bottom'+i))
	{
		i++;
	}
	// Send values from preview iframe
	// Also check for no columns selected
	var i = 0;
	var flag = 0;
	while(field = iframe.getElementById('bottom'+i))
	{
		if(field.value != -1)
			flag = 1;
		query_string += 'bottom['+i+']='+field.value+'&';
		i++;
	}
	if(!flag)
	{
		alert('Please use at least one column from \'Fields Not Used\'');
		return false;
	}

	// Build the prevalue array
	for(i = 1; i < 6; i++)
	{
		if(!document.getElementById("c[listing_features_bottom"+i+"1]") || !document.getElementById("c[listing_features_bottom"+i+"2]"))
			continue;

		var field1 = document.getElementById("c[listing_features_bottom"+i+"1]").value;
		var field2 = document.getElementById("c[listing_features_bottom"+i+"2]").value;

		if(field1 != -1)
			var value1 = document.getElementById("c[listing_features_input"+i+"1]").value;
		else
			value1 = "";
		if(field2 != -1)
			var value2 = document.getElementById("c[listing_features_input"+i+"2]").value;
		else
			value2 = "";

		query_string += 'prevalue[listing_features_bottom'+i+'1]='+escape(field1);
		query_string += '&';
		query_string += 'prevalue[listing_features_bottom'+i+'2]='+escape(field2);
		query_string += '&';
		query_string += 'prevalue[listing_features_input'+i+'1]='+escape(value1);
		query_string += '&';
		query_string += 'prevalue[listing_features_input'+i+'2]='+escape(value2);
		query_string += '&';
	}

	// Submit the start and end dates
	var fixed = document.getElementById("duration_fixed");
	var variable = document.getElementById("duration_variable");
	if(fixed.checked)
	{
		query_string += "duration[duration_value]="+document.getElementById("duration_value").value;
	}
	else if(variable.checked)
	{
		query_string += "duration[start_day]="+document.getElementById("start_day").value+'&';
		query_string += "duration[start_month]="+document.getElementById("start_month").value+'&';
		query_string += "duration[start_year]="+document.getElementById("start_year").value+'&';
		query_string += "duration[start_hour]="+document.getElementById("start_hour").value+'&';
		query_string += "duration[start_minute]="+document.getElementById("start_minute").value+'&';

		query_string += "duration[end_day]="+document.getElementById("end_day").value+'&';
		query_string += "duration[end_month]="+document.getElementById("end_month").value+'&';
		query_string += "duration[end_year]="+document.getElementById("end_year").value+'&';
		query_string += "duration[end_hour]="+document.getElementById("end_hour").value+'&';
		query_string += "duration[end_minute]="+document.getElementById("end_minute").value+'&';
	}

	// Post the data
	PostData('index.php?a=200&action=submit_iframe&'+query_string, "SubmitComplete");
}

function SubmitComplete(response)
{
	var xmlDoc = ParseXML(response);

	// If error it needs to be alerted to the user
	if(xmlDoc.getElementsByTagName("error")[0])
	{
		var error = xmlDoc.getElementsByTagName("error")[0];
		alert(error.firstChild.nodeValue);
		return false;
	}

	document.getElementById('page_body').style.display = 'none';

	var post_string = 'index.php?a=200&action=run_queries&start=0';

	// Get the title field
	var title = document.getElementById('c[build_title]');
	if(title.value)
	{
		title_string = "&";
		for(i = 0; i < 4; i++)
		{
			var element = document.getElementById('bottom_select'+i);
			title_string += 'title[bottom_select'+i+']='+element.value;
			if(i != 3)
				title_string += "&";
		}
	}
	else
		title_string = "";

	post_string += title_string;

	// Put on the user_id
	var user_id = document.getElementById('user_id');
	post_string += "&user_id="+user_id.value;

	// Get the category id
	var category = document.getElementById('category_id');
	post_string += '&category_id='+category.value;

	// Add the listing_type to the post string
	if(document.getElementById('listing_type'))
	{
		var listing_type = document.getElementById('listing_type');
		post_string += '&listing_type='+listing_type.value;
	}
	
	if(document.getElementById('bolding').checked) {
		post_string += '&bolding=1';
	}
	if(document.getElementById('better_placement').checked) {
		post_string += '&better_placement=1';
	}
	if(document.getElementById('featured_ad').checked) {
		post_string += '&featured_ad=1';
	}
	if(document.getElementById('attention_getter').checked) {
		post_string += '&attention_getter=1&attention_getter_url='+document.getElementById('attention_getter_url').options[document.getElementById('attention_getter_url').selectedIndex].value;
	}
	
	post_string += '&delimeter='+document.getElementById('delimeter').value+'&encapsulation='+document.getElementById('encapsulation').value;
	// Post the data
	PostData(post_string, "RunQueries");
}

function RunQueries(response)
{
	var xmlDoc = ParseXML(response);

	var root = xmlDoc.getElementsByTagName("root")[0];
	var page_body = document.getElementById('page_body');

	// Check for errors
	if(root.getElementsByTagName("error")[0])
	{
		alert(root.getElementsByTagName("error")[0].firstChild.nodeValue);
		page_body.innerHTML = "An Error has occured.<br><a href=\"index.php?a=200\">Restart the Bulk Upload</a>";
		page_body.style.display = 'block';
	}

	// Check for completion
	if(!root.getElementsByTagName("complete")[0])
	{
		var start = root.getElementsByTagName("start")[0].firstChild.nodeValue;

		page_body.innerHTML = "Please wait, running queries.  This may take a few minutes.";
		page_body.style.display = 'block';
	
		var post_string = 'index.php?a=200&action=run_queries&start='+start;

		// Get the title field
		var title = root.getElementsByTagName('title');
		if(title[0].firstChild.nodeValue)
		{
			title_string = unescape(title[0].firstChild.nodeValue);
		}
		else
			title_string = "";

		post_string += title_string;

		// Put on the user_id
		var user_id = root.getElementsByTagName("user_id")[0].firstChild.nodeValue;
		post_string += "&user_id="+user_id;

		// Get the category id
		var category = root.getElementsByTagName("category_id")[0].firstChild.nodeValue;
		post_string += '&category_id='+category;

		// Add the listing_type to the post string
		var listing_type = root.getElementsByTagName("listing_type")[0].firstChild.nodeValue;
		post_string += '&listing_type='+listing_type;

		PostData(post_string, "RunQueries");
	}
	else
	{
		// Complete
		page_body.innerHTML = "Complete<br><a href=\"index.php?a=200\">Bulk Uploader Homepage</a>";
		page_body.style.display = 'block';
	}
}

function getFileBlockCode()
{
	// Do verification before showing user the file block
	if(!VerifyForFileBlock())
		return false;

	// Get the code from PHP
	PostData('index.php?a=200&action=show_file_block', "ShowFileBlock");
}

function ShowFileBlock(Response)
{
	// Hide the text and button
	var div = document.getElementById('file_warning');
	div.style.display = 'none';

	// Show the file block itself
	var div = document.getElementById('file_block_div');
	div.innerHTML = Response;
}

function VerifyForFileBlock()
{
	var errors = 0;

	// Check user id block
	if(!document.getElementById('c[user_id]').value && !document.getElementById('c[username]').value)
	{
		alert('Please enter a Username or User ID before continuing.');
		errors++;
	}

	// Check for delimeter
	if(!document.getElementById('c[delimeter]').value)
	{
		alert('Please enter a delimeter before continuing.');
		errors++;
	}

	// Check for type
	if(document.getElementById('c[listing_type]'))
	{
		// It is classauctions so lets check for listing type being selected
		if(document.getElementById('c[listing_type]').value == -1)
		{
			alert('Please choose a listing type before continuing.');
			errors++;
		}
	}

	if(errors)
		return false;
	else
		return true;
}

function LoadProfile()
{
	return;
	if(document.getElementById('profile_name'))
	{
		if(document.getElementById('profile_name').value == 0)
			return;

		var URL = 'index.php?a=200&action=LoadProfile&profile=' + document.getElementById('profile_name').value;
	}
	else
	{
		var data = document.getElementById('c[profile_name]');
		if(!data)
			var data = this.parent.document.getElementById('c[profile_name]');
	
		if(data.value == 0)
		{
			// Reset the data on the form
			var form = document.getElementById('bulk_form');
			if(form)
				form.reset();
			return;
		}

		var URL = 'index.php?a=200&action=LoadProfile&profile=' + data.value;
	}

	PostData(URL, "UpdateFormProfile");
}

function UpdateFormProfile(Response)
{
	var xmlDoc = ParseXML(Response);

	var error = xmlDoc.getElementsByTagName("error")[0];
	if(error)
	{
		alert(error.firstChild.nodeValue);
		return;
	}

	nodes = xmlDoc.documentElement.childNodes;
	// Below is the tag name
	//node1 = nodes.item(0).tagName;

	// Below is the node value
	//node1 = nodes.item(0).firstChild.nodeValue;

	// Below is the NodeList length
	//nodes.length
	//alert(nodes.length+' is the length');

	// I used element_number because i is used in a function below and the scoping
	// of javascript is screwy and was giving me a horrid time
	for(element_number = 1; element_number < nodes.length; element_number++)
	{
		//alert(nodes.item(element_number).nodeName);

		if(!nodes.item(element_number))
			continue;

		//alert(nodes.item(element_number).tagName);
		var element = document.getElementById('c['+nodes.item(element_number).tagName+']');
		if(element)
		{
			// Check for field being empty
			if(nodes.item(element_number).firstChild)
			{
				element.setAttribute('value', nodes.item(element_number).firstChild.nodeValue);
			}

			continue;
		}

		// Deal with duration type fields
		if(nodes.item(element_number).tagName == "duration_type")
		{
			//alert(nodes.item(i).firstChild.nodeValue);
			var element = document.getElementById(nodes.item(element_number).firstChild.nodeValue);
			if(element)
			{
				element.checked = true;
				if(nodes.item(element_number).firstChild.nodeValue == "duration_fixed")
					ShowFixedDuration();
				else
					ShowVariableDuration();
			}

			continue;
		}

		// Deal with the title fields building
		if(nodes.item(element_number).nodeName == "title")
		{
			// Check if its the first page or not, if it is bail
			if(!document.getElementById('c[build_title]'))
				continue;

			var title_nodes = nodes.item(element_number).childNodes;
			var updated = 0;

			if(title_nodes.item(0).firstChild)
			{
				top = document.getElementById(title_nodes.item(0).tagName);
				alert(title_nodes.item(1).textContent);
				document.getElementById('c[build_title]').checked = true;
				top.selectedIndex = top.options;
				MakeFieldsAppear('c[build_title]', 'bottom_dropdowns');
				SendDropDownChange(title_nodes.item(0).tagName, title_nodes.item(1).tagName, "bottom_div0");
			}

			if(title_nodes.item(2).firstChild)
			{
				document.getElementById('c[build_title]').checked = true;
				alert(title_nodes.item(3).textContent);
				MakeFieldsAppear('c[build_title]', 'bottom_dropdowns');
				SendDropDownChange(title_nodes.item(2).tagName, title_nodes.item(3).tagName, "bottom_div1");
			}

			if(title_nodes.item(4).firstChild)
			{
				document.getElementById('c[build_title]').checked = true;
				alert(title_nodes.item(5).textContent);
				MakeFieldsAppear('c[build_title]', 'bottom_dropdowns');
				SendDropDownChange(title_nodes.item(4).tagName, title_nodes.item(5).tagName, "bottom_div2");
			}
			
			if(title_nodes.item(6).firstChild)
			{
				document.getElementById('c[build_title]').checked = true;
				alert(title_nodes.item(7).textContent);
				MakeFieldsAppear('c[build_title]', 'bottom_dropdowns');
				SendDropDownChange(title_nodes.item(6).tagName, title_nodes.item(7).tagName, "bottom_div3");
			}

			continue;
		}

		// Deal with the prevalues
		if(nodes.item(element_number).nodeName == "prevalues")
		{
			// Check if its the first page or not, if it is bail
			if(!document.getElementById('c[listing_features_checkbox]'))
				continue;

			var prevalue_nodes = nodes.item(element_number).childNodes;

			var row_column = 11;
			for(field = 0; field < 30; field += 3)
			{
				if(prevalue_nodes.item(field).firstChild.nodeValue != -1)
				{
					document.getElementById('c[listing_features_checkbox]').checked = true;
					ShowListingFeatures();
					document.getElementById('c['+prevalue_nodes.item(field).tagName+']').value = prevalue_nodes.item(field).firstChild.nodeValue;
					SendDropDownChange('c['+prevalue_nodes.item(field).tagName+']','c['+prevalue_nodes.item(field+1).tagName+']', "listing_features"+row_column);
					if(document.getElementById('c['+prevalue_nodes.item(field+2).tagName+']'))
						document.getElementById('c['+prevalue_nodes.item(field+2).tagName+']').value = prevalue_nodes.item(field+2).firstChild.nodeValue;
					else
						document.getElementById('c['+prevalue_nodes.item(field+2).tagName+']').value = '';

					// Create next value for the row and column chooser
					if(row_column % 2 == 1)
						row_column++;
					else
						row_column += 9;
				}
			}

			continue;
		}

		if(nodes.item(element_number).nodeName == "field_mappings")
		{
			// Check if its the first page or not, if it is bail
			if(!document.getElementById('preview_button'))
				continue;

			// Show the iframe
			ShowResults(nodes.item(element_number).firstChild.nodeValue);
		}

		if(nodes.item(element_number).nodeName == "display_size")
		{
			if(!document.getElementById('preview_block_iframe'))
				continue;
			
			var iframe = document.getElementById('preview_block_iframe');
			// TODO finish this part
			//iframe.contentDocument.getElementById('choose_preview_select').value = nodes.item(element_number).firstChild.nodeValue;
			//SendResultSize(iframe.contentDocument);
		}
	}
}

function UpdatePreviewWindow(profile_data)
{
	// After this eval profile_text becomes an array
	// of each value that will be put in the dropdowns
	eval(profile_data);

	// Note that this function runs inside the iframe

	for(dropdown_count = 0, iterator = 0; iterator < profile_data.length; dropdown_count++, iterator += 2)
	{
		// TODO
		// Here we need to iterate across all values in the array and the dropdowns and then set those values in the top dropdown
		// and then send changes to the dropdowns
		// SendDropDownChange('top1', 'bottom1', 'div1')

		// iterator goes across the array
		// dropdown_count goes through the dropdown numbers

		var dropdown = document.getElementById('top'+dropdown_count);

		// Check for invalid fields
		if(!dropdown)
			continue;

		dropdown.value = profile_text[iterator];

		if(profile_text[iterator+1])
			SendDropDownChange('top'+dropdown_count, 'bottom'+dropdown_count, 'div'+dropdown_count, profile_text[iterator+1]);
		else
			SendDropDownChange('top'+dropdown_count, 'bottom'+dropdown_count, 'div'+dropdown_count);
	}
}

function onLoadActions(page_number)
{
	if(page_number == 1)
	{
		var filesystem = document.getElementById('filesystem_text_row');
		if(filesystem)
			filesystem.style.display = 'none';

		// Hide the undo and bulk upload parts
		var undo_field = document.getElementById('undo_hidden_row');
		if(undo_field)
			undo_field.style.display = 'none';
	}
	else if(page_number == 2)
	{
		var title = document.getElementById('bottom_dropdowns');
		if(title)
			title.style.display = 'none';

		var table = document.getElementById("listing_features_table");
		if(table)
			table.style.display = 'none';

		// Load the profile that was selected
		LoadProfile();
		
		// Show the Results
		ShowResults();
	}
}

function SendDropDownChange(top_dropdown_name, bottom_dropdown_name, div_name, second_value)
{
	var select = document.getElementById(top_dropdown_name);
	var field = select.value;

	var listing = document.getElementById("listing_type");
	var listing_type = listing.value;

	if(document.getElementById("category_id"))
		var category_id = document.getElementById("category_id").value;
	else if(this.parent.document.getElementById("category_id"))
		var category_id = this.parent.document.getElementById("category_id").value;

	// Check if in iframe or not
	var loc = new String(this.location);
	if(loc.search(/index.php/) != -1)
		var URL = "index.php?a=200&action=change_dropdown&fields="+field+"&bottom="+bottom_dropdown_name+"&div="+div_name+"&listing="+listing_type+"&category_id="+category_id;
	else
		var URL = "../index.php?a=200&action=change_dropdown&fields="+field+"&bottom="+bottom_dropdown_name+"&div="+div_name+"&listing="+listing_type+"&category_id="+category_id

	if(second_value)
		URL += '&second_value='+second_value;

	PostData(URL, "ChangeSecondDropDown");
}

function ChangeSecondDropDown(response)
{
	//alert(response);
	var xmlDoc = ParseXML(response);
	var root = xmlDoc.getElementsByTagName("root")[0];
	var div_name = root.getElementsByTagName("div_name")[0].firstChild.nodeValue;
	var second_value = root.getElementsByTagName("second_value")[0];
	
	// Had to do an eval because the normal getElementById was not working
	var select = eval("document.getElementById("+div_name+")");

	// Undo the urlencode
	var code = unescape(root.getElementsByTagName("code")[0].firstChild.nodeValue);

	// Put the html into the div
	select.innerHTML = code;

	if(second_value)
	{
		//alert(root.getElementsByTagName("second_value")[0].firstChild.nodeValue);
		//select.value = second_value.firstChild.nodeValue;
	}
}

function MakeFieldsAppear(checkbox_name, div_name)
{
	var checkbox = document.getElementById(checkbox_name);
	var appear = document.getElementById(div_name);

	if(appear)
	{
		if(checkbox.checked)
			appear.style.display = "block";
		else
			appear.style.display = "none";
	}
}

function ShowListingFeatures()
{
	var table = document.getElementById("listing_features_table");
	if(table)
	{
		// Check whether to display table or not
		var checkbox = document.getElementById("c[listing_features_checkbox]");
		if(checkbox)
		{
			if(checkbox.checked)
			{
				table.style.display = 'block';
			}
			else
			{
				table.style.display = 'none';
			}
		}
	}
}

function UpdateListingFeatures(Response)
{
	var table = document.getElementById("listing_features_table");
	var xmlDoc = ParseXML(Response);

	var root = xmlDoc.getElementsByTagName("root")[0];
	for(i = 1; i < 6; i++)
	{
		var row = root.getElementsByTagName("row"+i)[0];
		var new_row = table.insertRow(table.rows.length);

		new_row.className = row.getElementsByTagName("class")[0].firstChild.nodeValue;
		new_row.innerHTML = unescape(row.getElementsByTagName("code")[0].firstChild.nodeValue);
	}

	alert(table.rows.length);
}

function ShowFixedDuration()
{
	// Make sure the correct button is checked
	if(!document.getElementById('duration_fixed').checked)
		return false;

	// Hide the other radio buttons field
	var td = document.getElementById('duration_variable_hidden');
	td.innerHTML = "";

	var body = "Please select a duration: ";
	body += "<select id=\"duration_value\">\n\t";
	body += "<option value=\"1\">1 day</option>\n";
	for(i = 2; i < 366; i++)
	{
		body += "<option value=\""+i+"\">"+i+" days</option>\n";
	}
	body += "</option>";

	td = document.getElementById('duration_fixed_hidden');
	td.innerHTML = body;
}

function ShowVariableDuration()
{
	// Make sure the correct button is checked
	if(!document.getElementById('duration_variable').checked)
		return false;

	// Hide the other radio buttons field
	var td = document.getElementById('duration_fixed_hidden');
	td.innerHTML = "";

	var body = "Please select a start time:<br>";
	body += date_select("start_year", "start_month", "start_day", "start_hour", "start_minute");

	body += "<br>";

	body += "Please select an end time:<br>";
	body += date_select("end_year", "end_month", "end_day", "end_hour", "end_minute");

	td = document.getElementById('duration_variable_hidden');
	td.innerHTML = body;
}

function date_select(year_name, month_name, day_name, hour_name, minute_name)
{
	var d = new Date();
	var body = "";

	var year_value = d.getFullYear();
	var month_value = d.getMonth();
	var day_value = d.getDay();
	var hour_value = d.getHours();
	var minute_value = d.getMinutes();

	body += "Day <select id="+day_name+">\n\t\t";
	for(i=1; i < 32; i++)
	{
		body += "<option";
		if (day_value == i)
			body += " selected";
		body += ">"+i+"</option>\n\t\t";
	}
	body += "</select>\n\t\t";

	body += " Month <select id="+month_name+">\n\t\t";
	for (i=1; i < 13; i++)
	{
		body += "<option";
		if (month_value == i)
			body += " selected";
		body += ">"+i+"</option>\n\t\t";
	}
	body += "</select>\n\t\t";

	body += "Year <select id="+year_name+">\n\t\t";
	for (i = year_value; i <= (year_value+10); i++)
	{
		body += "<option";
		if (year_value == i)
			body += " selected";
		body += ">"+i+"</option>\n\t\t";
	}
	body += "</select>\n\t\t";

	body += "Hour <select id="+hour_name+">\n\t\t";
	for (i = 0; i <= 23; i++)
	{
		body += "<option";
		if (hour_value == i)
			body += " selected";
		body += ">"+i+"</option>\n\t\t";
	}
	body += "</select>\n\t\t";

	body += "Minute <select id="+minute_name+">\n\t\t";
	for(i = 0; i <= 59; i++)
	{
		body += "<option";
		if (minute_value == i)
			body += " selected";
		body += ">"+i+"</option>\n\t\t";
	}
	body += "</select>\n\t\t</font>";

	return body;
}

function ShowResults(profile_text)
{
	// Make the button dissapear
	document.getElementById("preview_button").style.display = 'none';

	// Build the query string for the src file
	var query_string = '?category_id='+document.getElementById("category_id").value;
	query_string += '&delimeter='+escape(document.getElementById("delimeter").value);
	query_string += '&encapsulation='+escape(document.getElementById("encapsulation").value);
	query_string += '&listing_type='+escape(document.getElementById("listing_type").value);

	// Check for listing_type
	var listing_type = document.getElementById("listing_type").value;
	query_string += "&listing_type="+listing_type;

	// Add the category_id onto the query string
	var category_id = document.getElementById("category_id").value;
	query_string += "&category_id="+category_id;

	// Check if we need to pass in a profile_id also
	if(profile_text)
		query_string += "&profile_text="+profile_text;

	var iframe = document.getElementById("preview_block_iframe");
	iframe.style.margin = "0px";
	iframe.style.padding = "0px";
	iframe.height = 250;
	iframe.width = 800;
	iframe.src = "bulk_uploader/preview_window.php"+query_string;
	iframe.style.overflowX = "scroll";
	iframe.style.overflowY = "scroll";
}

function SendResultSize()
{
	var table = document.getElementById("results_table");
	var dropdown = document.getElementById("choose_preview_select");
	var current_size = table.rows.length;
	var new_size = dropdown.value;

	// Check for same sizes
	if(current_size == new_size)
		return true;

	// Check for new size being bigger than current size
	if(new_size > current_size)
	{
		// New size is bigger than old size
		// Send Request so the php can return the inner HTML
		PostData("../index.php?a=200&action=update_results&new_size="+new_size+"&old_size="+current_size, "UpdateResultTable");
	}
	else
	{
		// New size is smaller than old size
		// So we need to trim down the table to the correct size
		while((table.rows.length-1) > new_size)
		{
			table.deleteRow(table.rows.length-1);
		}
	}
}

function UpdateResultTable(Response)
{
	// This is only ran when the new size is larger than the current
	// Parse the XML
	var xmlDoc = ParseXML(Response);

	var table = document.getElementById("results_table");
	var root = xmlDoc.getElementsByTagName("root")[0];
	var size = root.getElementsByTagName("size")[0].firstChild.nodeValue;

	for(i = 1; i < size+1; i++)
	{
		var current = root.getElementsByTagName("line"+(i-1))[0];
		if(!current)
			continue;

		var new_row = table.insertRow(table.rows.length);
		if(i % 2)
			new_row.className = "odd_result_row";
		else
			new_row.className = "even_result_row";
		new_row.innerHTML = unescape(current.firstChild.nodeValue);

		// Put the CSS on the listing counter cell
		new_row.cells[0].className = "listing_counter";
	}
}

function save_new_profile()
{
	// Get the profile name from the user
	while(1)
	{
		if(!document.getElementById('duration_value'))
		{
			alert('Please select a duration or start/end time.')
			return false;
		}
		var value = prompt("Please enter a profile name:");
		if(!value)
			return false;

		if(confirm("Is this profile name correct: "+value+"? "))
			break;
	}

	// Build the post query
	post_query = "&action=save_new_profile&c[profile_name]="+value;
	post_query += "&c[user_id]="+document.getElementById('user_id').value;
	if(document.getElementById('listing_type'))
		post_query += "&c[listing_type]="+document.getElementById('listing_type').value;
	post_query += "&c[category_id]="+document.getElementById('category_id').value;
	post_query += "&c[delimeter]="+document.getElementById('delimeter').value;
	post_query += "&c[encapsulation]="+document.getElementById('encapsulation').value;
	if(document.getElementById('duration_fixed'))
	{
		if(document.getElementById('duration_fixed').value)
		{
			post_query += "&c[duration_type]=duration_fixed";
			post_query += "&c[fixed_duration_value]="+document.getElementById('duration_value').value;
		}
		else
			post_query += "&c[duration_type]=duration_variable";
	}
	else
		post_query += "&c[duration_type]=duration_variable";

	// Build the prevalue part of the post query
	if(document.getElementById('c[listing_features_checkbox]'))
	{
		if(document.getElementById('c[listing_features_checkbox]').checked)
		{
			var prevalue = "";
			for(i = 1; i < 6; i++)
			{
				prevalue += document.getElementById('c[listing_features_top'+i+'1]').value+"|"+document.getElementById('c[listing_features_bottom'+i+'1]').value;
				prevalue += "|";
				if(document.getElementById('c[listing_features_input'+i+'1]'))
					prevalue += document.getElementById('c[listing_features_input'+i+'1]').value;
				prevalue += "|";
				prevalue += document.getElementById('c[listing_features_top'+i+'2]').value+"|"+document.getElementById('c[listing_features_bottom'+i+'2]').value;
				prevalue += "|";
				if(document.getElementById('c[listing_features_input'+i+'2]'))
					prevalue += document.getElementById('c[listing_features_input'+i+'2]').value;
				prevalue += '|';
			}

			post_query += "&c[prevalues]="+escape(prevalue);
		}
	}

	// Build the title part of the post query
	if(document.getElementById('c[build_title]'))
	{
		if(document.getElementById('c[build_title]').checked)
		{
			var title = "";
			for(i = 0; i < 4; i++)
			{
				title += document.getElementById('top_select'+i).value+"|";
				title += document.getElementById('bottom_select'+i).value+"|";
			}

			post_query += "&c[title_fields]="+escape(title);
		}
	}

	var iframe = document.getElementById('preview_block_iframe');

	// Build the field values part of the post query
	var i = 0;
	var fields = "";
	while(iframe.contentDocument.getElementById('top'+i))
	{
		var top = iframe.contentDocument.getElementById('top'+i);
		var bottom = iframe.contentDocument.getElementById('bottom'+i);

		fields += top.value+"|"+bottom.value+"|";

		i++;
	}
	post_query += "&c[field_mappings]="+escape(fields);

	post_query += "&c[display_size]="+iframe.contentDocument.getElementById('choose_preview_select').value;

	PostData('index.php?a=200'+post_query, "NewProfileSaved");
}

function NewProfileSaved(Response)
{
	var xmlDoc = ParseXML(Response);
	var error = xmlDoc.getElementsByTagName("error")[0];
	if(error)
	{
		alert(error.firstChild.nodeValue);
		return;
	}
	alert("Saved profile successfully.");

	return;
}

function save_profile_changes()
{
	if(!confirm("Are you sure you want to save these changes?"))
		return false;

	// Build the post query
	var profile_id = document.getElementById('profile_name').value;
	if(profile_id == 0)
		alert('Cannot save to the \'None\' profile.');
	post_query = "&action=save_profile_changes&c[profile_id]="+profile_id;
	post_query += "&c[user_id]="+document.getElementById('user_id').value;
	if(document.getElementById('listing_type'))
		post_query += "&c[listing_type]="+document.getElementById('listing_type').value;
	post_query += "&c[category_id]="+document.getElementById('category_id').value;
	post_query += "&c[delimeter]="+document.getElementById('delimeter').value;
	post_query += "&c[encapsulation]="+document.getElementById('encapsulation').value;
	if(document.getElementById('duration_fixed'))
	{
		if(document.getElementById('duration_fixed').value)
		{
			post_query += "&c[duration_type]=duration_fixed";
			post_query += "&c[fixed_duration_value]="+document.getElementById('duration_value').value;
		}
		else
			post_query += "&c[duration_type]=duration_variable";
	}
	else
		post_query += "&c[duration_type]=duration_variable";

	// Build the prevalue part of the post query
	if(document.getElementById('c[listing_features_checkbox]'))
	{
		if(document.getElementById('c[listing_features_checkbox]').checked)
		{
			var prevalue = "";
			for(i = 1; i < 6; i++)
			{
				prevalue += document.getElementById('c[listing_features_top'+i+'1]').value+"|"+document.getElementById('c[listing_features_bottom'+i+'1]').value;
				prevalue += "|";
				if(document.getElementById('c[listing_features_input'+i+'1]'))
					prevalue += document.getElementById('c[listing_features_input'+i+'1]').value;
				prevalue += "|";
				prevalue += document.getElementById('c[listing_features_top'+i+'2]').value+"|"+document.getElementById('c[listing_features_bottom'+i+'2]').value;
				prevalue += "|";
				if(document.getElementById('c[listing_features_input'+i+'2]'))
					prevalue += document.getElementById('c[listing_features_input'+i+'2]').value;
				prevalue += '|';
			}

			post_query += "&c[prevalues]="+escape(prevalue);
		}
	}

	// Build the title part of the post query
	if(document.getElementById('c[build_title]'))
	{
		if(document.getElementById('c[build_title]').checked)
		{
			var title = "";
			for(i = 0; i < 4; i++)
			{
				title += document.getElementById('top_select'+i).value+"|";
				title += document.getElementById('bottom_select'+i).value+"|";
			}

			post_query += "&c[title_fields]="+escape(title);
		}
	}

	var iframe = document.getElementById('preview_block_iframe');

	// Build the field values part of the post query
	var i = 0;
	var fields = "";
	while(iframe.contentDocument.getElementById('top'+i))
	{
		var top = iframe.contentDocument.getElementById('top'+i);
		var bottom = iframe.contentDocument.getElementById('bottom'+i);

		fields += top.value+"|"+bottom.value+"|";

		i++;
	}
	post_query += "&c[field_mappings]="+escape(fields);

	post_query += "&c[display_size]="+iframe.contentDocument.getElementById('choose_preview_select').value;

	PostData('index.php?a=200'+post_query, "ProfileChangesSaved");
}

function ProfileChangesSaved(Response)
{
	var xmlDoc = ParseXML(Response);
	var error = xmlDoc.getElementsByTagName("error")[0];
	if(error)
	{
		alert(error.firstChild.nodeValue);
		return;
	}
	alert("Saved profile changes successfully.");

	return;
}

function CheckPage1()
{
	var file = document.getElementById('file_name');
	if(!file.value)
	{
		alert('Please select a file before continuing');
		return false;
	}
}

function showHideField(show_field, hide_field)
{
	var field = document.getElementById(show_field);
	if(field)
		field.style.display = '';

	field = document.getElementById(hide_field);
	if(field)
		field.style.display = 'none';
}
function checkOtherColumns(selectBoxId, selectedHTML)
{
	columnForm = document.getElementById("iframe_form");
	for(i=0;i<columnForm.elements.length;i++)
	{
		if(columnForm.elements[i].options)
		{
			if(columnForm.elements[i].options[columnForm.elements[i].selectedIndex].innerHTML==selectedHTML&&columnForm.elements[i].getAttribute('id')!=selectBoxId)
			{
				alert('The field you have selected is used else where.  The upload will fail if you do not change it');
			}
		}
	}
}