// Create HTTP request object
function HttpRequestor(ID)
{
  this.ElementID=ID;
}

// Class variables
HttpRequestor.prototype.XmlHttpObject=undefined;
HttpRequestor.prototype.ElementID="";

// Send HTTP request
HttpRequestor.prototype.send=function(sURL)
{
  this.XmlHttpObject=this.GetXmlHttpObject();
  var _this=this;
	this.XmlHttpObject.onreadystatechange=function(){_this.receive()};
	this.XmlHttpObject.open("GET",sURL,true);
	this.XmlHttpObject.send(null);
}

// receive HTTP Request response
HttpRequestor.prototype.receive=function()
{
	if (this.XmlHttpObject.readyState==4 || this.XmlHttpObject.readyState=="complete")
	{
	  document.getElementById(this.ElementID).innerHTML=this.XmlHttpObject.responseText;
	}
}

// Create XMLHttprequest object - browser specific
HttpRequestor.prototype.GetXmlHttpObject=function()
{ 
	var objXMLHttp=null;
	if (window.XMLHttpRequest) objXMLHttp=new XMLHttpRequest();
	else if (window.ActiveXObject) objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP");
	delete this.XmlHttpObject;
	return objXMLHttp;
}	

