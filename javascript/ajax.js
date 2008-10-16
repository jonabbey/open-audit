/**********************************************************************************************************
Module Comments:
	
	[Nick Brown]	20/08/2008
	The code in this module provides AJAX-style functionality through two objects:
	HttpRequestor - Uses XMLHttpRequest object to retrieve HTML content from URL
	XmlRequestor - Uses XmlDom object to retrive XML content from URL
	
**********************************************************************************************************/

/**********************************************************************************************************
Function Name:
	HttpRequestor
Description:
	Class wrapper for the XMLHttpRequest object. Send function can be invoked. Returned HTML is then written back to the 
	HTML DOM object whose ID matches ID argument.
Arguments:
	ID		[IN] [string]	ID of DOM object that will receive the returned HTML string
	Async	[IN] [string]	Async flag - not currently used.
Returns:	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function HttpRequestor(ID, Async)
{
  this.ElementID = ID;
	this.XmlHttpObject = undefined;
	if (typeof Async == 'undefined') Async = 0;
	this.Async = Async;

	/*******************************************************************************************************
	Function Name: send
	Description:	Invokes the send method of XmlHttpObject using the supplied URL.
	Arguments:
		ID		[IN] [string]	URL to request
	Returns:	None
	Change Log:
		20/08/2008			New function	[Nick Brown]
	*******************************************************************************************************/
	this.send = function(sURL)
	{
	  this.XmlHttpObject = GetXmlHttpObject();
	  var _this = this;
		this.XmlHttpObject.onreadystatechange = function(){_this.receive()};
		this.XmlHttpObject.open("GET",sURL,true);
		this.XmlHttpObject.send(null);
	}

	/******************************************************************************************************
	Function Name:	receive
	Description:
		Called when data is returned from the requested URL. The HTML string is written back to the HTML DOM object 
		defined by the ElementID property.
	Arguments:	None
	Returns:	None
	Change Log:
		20/08/2008			New function	[Nick Brown]
	******************************************************************************************************/
	this.receive = function()
	{
		if (this.Async == 1)
		{	
			document.getElementById(this.ElementID).innerHTML = this.XmlHttpObject.responseText;
			return;
		}
		if (this.XmlHttpObject.readyState == 4 || this.XmlHttpObject.readyState == "complete")
		{
		  document.getElementById(this.ElementID).innerHTML = this.XmlHttpObject.responseText;
		}
	}
}

/**********************************************************************************************************
Function Name:
	GetXmlHttpObject
Description:
	Create XMLHttprequest object - browser specific. Mozilla browsers have XMLHttpRequest object built in. IE uses activex 
	objects of which there are many versions. 
Arguments:	None
Returns:	XMLHttprequest object
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function GetXmlHttpObject()
{
	// Firefox, Mozilla, Opera, etc.
	if (window.XMLHttpRequest) return new XMLHttpRequest();
	else if (window.ActiveXObject) 
	// IE only
	{
		var progids = ["MSXML2.ServerXMLHTTP","Msxml2.XMLHTTP.5.0", "Msxml2.XMLHTTP.4.0", "MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP", "Microsoft.XMLHTTP"];
    var obj;
		for(var i = 0; i < progids.length; i++)
    {
      try
      {
				obj = new ActiveXObject(progids[i]);
        return obj;
			}
	    catch (e){}
		}
	}
}

/**********************************************************************************************************
Function Name:
	XmlRequestor
Description:
	Class wrapper for the XML DOM object. Returns an XML DOM object  & XML string in response to supplied URL
Arguments:
	url		[IN] [string]	Optional url from which XML string is returned
Returns:	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function XmlRequestor(url)
{
	this.XmlString = '';
	this.XmlDomObject = undefined;

	this.IE = (window.ActiveXObject) ? true : false;
	if (!this.IE)
	{
		this.XmlSerial = new XMLSerializer();
		this.XmlParser = new DOMParser();
	}

	/******************************************************************************************************
	Function Name:	GetXMLDocFromUrl
	Description:
		Loads an XML file from supplied URL into XML DOM and stores XML string in this.XmlString
	Arguments:
		url	[IN] [string]	url from which XML DOM is returned
	Returns:	XML DOM object
	Change Log:
		20/08/2008			New function	[Nick Brown]
	******************************************************************************************************/
	this.GetXMLDocFromUrl = function(url)
	{
		this.XmlString = '';
	  this.XmlDomObject = GetXmlDomObject();
		this.XmlDomObject.load(url);
		this.XmlString = (this.IE == true) ? this.XmlDomObject.xml : this.XmlSerial.serializeToString(this.XmlDomObject);
		return this.XmlDomObject;
	}

	/******************************************************************************************************
	Function Name:	SerializeXmlNode
	Description:
		Returns a serialized XML string representation of an XML node
	Arguments:
		url	[IN] [node object]		XML DOM node
	Returns:	[string]  XML string
	Change Log:
		10/09/2008			New function	[Nick Brown]
	******************************************************************************************************/
	this.SerializeXmlNode = function (node)
	{
		XmlString = (this.IE == true) ? node.xml : this.XmlSerial.serializeToString(node);
		return XmlString;
	}

	/******************************************************************************************************
	Function Name:	GetXMLDocFromString
	Description:
		Loads an XML file from supplied string into XML DOM (and stores XML string in this.XmlString)
	Arguments:
		url	[IN] [string]	url from which XML DOM is returned
	Returns:	XML DOM object
	Change Log:
		10/09/2008			New function	[Nick Brown]
	******************************************************************************************************/
	this.GetXMLDocFromString = function(XmlString)
	{
		this.XmlString = str;
		if (this.IE) 
		{
			this.XmlDomObject = GetXmlDomObject();
			this.XmlDomObject.loadXML(XmlString);
		}
		else 
    {
			this.XmlDomObject = parser.parseFromString(XmlString,"text/xml");
		}
    return(this.XmlDomObject);
	}
	
	// If constructor was passed a URL, invoke GetXMLDocFromUrl to load XML file immediately
	if (url != undefined) {this.GetXMLDocFromUrl(url);}
	
	/******************************************************************************************************
	Function Name:	GetNode
	Description:
		Returns the *first* XML node whose tag matches the supplied Tag name
	Arguments:
		TagName	[IN] [string]	Name of node tag whose value is to be returned
	Returns:	
		[node object]	The node within the XML doc
	Change Log:
		10/09/2008			New function	[Nick Brown]
	******************************************************************************************************/
	this.GetNode = function(TagName)
	{
		var nodes = this.XmlDomObject.documentElement.getElementsByTagName(TagName);
		if(nodes.length == 0){return "";}
		return nodes[0];
	}

	/******************************************************************************************************
	Function Name:	GetValue
	Description:
		Returns the value from the *first* XML node whose tag matches the supplied Tag name
	Arguments:
		TagName	[IN] [string]	Name of node tag whose value is to be returned
	Returns:	
		[String]	The value contained within the XML tag
	Change Log:
		20/08/2008			New function	[Nick Brown]
	******************************************************************************************************/
	this.GetValue = function(TagName)
	{
		//if(nodes.length == 0){return "";}
		return this.GetNode(TagName).firstChild.nodeValue;
	}
}

/**********************************************************************************************************
Function Name:
	GetXmlDomObject
Description:
	Create XML DOM object - browser specific. Mozilla browsers have DOMParser object built in. IE uses activex 
	objects of which there are many versions. 
Arguments:	None
Returns:	XMLHttprequest object
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function GetXmlDomObject()
{
  var oXmlDoc = undefined;
	if (window.ActiveXObject)
	{
		// IE Only
		var progids = ["Msxml2.DOMDocument.6.0", "Msxml2.DOMDocument.4.0", "Msxml2.DOMDocument.3.0", "Msxml2.DOMDocument", "Msxml.DOMDocument"];
		for(var i = 0; i < progids.length; i++)
    {
      try
      {
				oXmlDoc = new ActiveXObject(progids[i]);
				oXmlDoc.setProperty("SelectionLanguage", "XPath");
			}
	    catch (e){}
		}
	}
	else
	// Firefox, Mozilla, Opera, etc.
	{
		oXmlDoc = document.implementation.createDocument("","",null);
	}

	oXmlDoc.async = false;
	return oXmlDoc;
}

