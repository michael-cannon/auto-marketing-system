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