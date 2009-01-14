/**********************************************************************************************************
Module Comments:
	
	[Nick Brown]	20/08/2008
	The code in this module is used by admin_config.php (which is the Admin -> Config page).
	It provides:
	- The functionality of the page navigation tab (to switch between config pages)
	- Functions that are called in response to GUI actions (popup menu, buttons etc)
	
**********************************************************************************************************/
//debugger; 

/**********************************************************************************************************
Function Name:
	SelectNavTab
Description:
	Called in response to one of the page navigation tabs being clicked. Ensures that the corresponding config page is 
	displayed.
Arguments:
	e	[IN] [object]	DOM object that fired the event
Returns:	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function SelectNavTab(e)
{			
	if(document.getElementById)
	{
		// Determine which tab has been selected
		var Id = new String(e.id); // e.id example npb_config_general_tab
		var aId = Id.split("_");
		var id = 'npb_config_' + aId[aId.length-2] + '_div'; // id of the DIV page to display e.g. config_general
		
		// Clear current tabs state
		// Get npb_tab_nav <UL> - get all ULs and loup thru
		oULs=document.getElementsByTagName("ul");
		for (var i = 0; i < oULs.length; i++)
		{if(oULs.item(i).className=='npb_tab_nav'){oUL=oULs.item(i)}}
		// Get all child <A> elements and set font to normal
		oAs=oUL.getElementsByTagName("a");
		for (var i = 0; i <oAs.length; i++){oAs.item(i).style.fontWeight='normal';};
		// Hide all config DIVS
		document.getElementById('npb_config_general_div').style.display="none";
		document.getElementById('npb_config_security_div').style.display="none";
		document.getElementById('npb_config_homepage_div').style.display="none";
		document.getElementById('npb_config_ldap_div').style.display="none";
		document.getElementById('npb_config_save_div').style.display="none";
		
		// Now display selected DIV and highlight tab
		if(e)
		{
			e.style.fontWeight='bold';
			// Hide the "Save" button when the LDAP page is selected
			if (id != 'npb_config_ldap_div') {document.getElementById('npb_config_save_div').style.display="block";}
			a=document.getElementById(id);
			a.style.display="block";
		}
	}
}

/**********************************************************************************************************

	GUI functions	- Functions that are called in response to GUI actions (popup menu, buttons etc)

**********************************************************************************************************/

// Define connection menu & path menu HTML 
connection_menu  = "<div id='popupmenu_id' style='display:none'>guid</div>";
connection_menu += "<a href='javascript://' OnClick='EditLdapConnection();'>Edit Connection</a>";
connection_menu += "<a href='javascript://' OnClick='DeleteLdapConnection();'>Delete Connection</a>";
connection_menu += "<a href='javascript://' OnClick='NewLdapPath();'>Add New Path</a>";

path_menu  = "<div id='popupmenu_id' style='display:none'>guid</div>";
path_menu += "<a href='javascript://' OnClick='EditLdapPath();'>Edit Path</a>";
path_menu += "<a href='javascript://' OnClick='DeleteLdapPath();'>Delete Path</a>";

/**********************************************************************************************************
Function Name:
	RefreshLdapConnectionsList
Description:
	Bit of a kludge to refresh connection list after an update - can't think of a better solution right now
Called by: DeleteLdapPath, SaveLdapPath, DeleteLdapConnection, SaveLdapPath, DeleteLdapConnection, SaveLdapConnection
Arguments:	None
Returns:	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function RefreshLdapConnectionsList()
{setTimeout('ListLdapConnections()', 1000);}

/**********************************************************************************************************
Function Name:
	ListLdapConnections
Description:
	Displays list of LDAP connections and associated LDAP paths. The 'npb_ldap_connections_div' DIV is populated with HTML returned
	from the server by the HttpRequestor object
Called by: RefreshLdapConnectionsList
Arguments:	None
Returns:	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function ListLdapConnections()
{
	var LdapConnections=new HttpRequestor('npb_ldap_connections_div');
	LdapConnections.send('admin_config_data.php?sub=f1');
}

/**********************************************************************************************************
Function Name:
	DeleteLdapPath
Description:
	Deletes the selected LDAP path. The uid of the path to delete is retrieved from the hidden 'uid' DIV. This is passed to the 
	server using the XmlRequestor object.
	
	Called in response to the "Delete Path" option on the LDAP path menu
Arguments:	None
Returns:	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function DeleteLdapPath()
{
	document.getElementById("npb_ldap_path_config_div").style.display = 'none';	
	document.getElementById("npb_ldap_connection_config_div").style.display = 'none';
	var ldap_path_id = document.getElementById("popupmenu_id").innerHTML;
	var pathxml = new XmlRequestor('admin_config_data.php?sub=f9&ldap_path_id=' + ldap_path_id);
	RefreshLdapConnectionsList();
}

/**********************************************************************************************************
Function Name:
	NewLdapPath
Description:
	Adds a new LDAP path to the selected LDAP connection. The uid of the selected connection is retrieved from the hidden 'uid' 
	DIV. This is passed to the server using the XmlRequestor object. The domain default NC retrieved from the returned XML.
	The 'npb_ldap_path_config_div' DIV is displayed and the path input is populated witht the domain default NC.
	
	Called in response to the "Add New Path" option on the LDAP connection menu
Arguments:	None
Returns: 	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function NewLdapPath()
{
	document.getElementById("npb_ldap_path_config_div").style.display = 'block';	
	document.getElementById("npb_ldap_connection_config_div").style.display = 'none';	
	var ldap_path_connection_id = document.getElementById("popupmenu_id").innerHTML;
	var domainxml = new XmlRequestor('admin_config_data.php?sub=f6&ldap_connection_id=' + ldap_path_connection_id);
	document.getElementById("ldap_path_connection_id").value = ldap_path_connection_id;
	document.getElementById("ldap_path_dn").value = domainxml.GetValue("domain_nc");
	document.getElementById("ldap_path_audit").checked = true;
}

/**********************************************************************************************************
Function Name:
	SaveLdapPath
Description:
	Saves the LDAP path details to the db. Retrieves the settings from the HTML form and uses a XmlRequestor object to 
	post them to the server. The 'npb_ldap_path_config_div' DIV is then hidden.
	
	Called in response to the "Save" button on the 'npb_ldap_path_config_div' DIV
Arguments:	None
Returns: 	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function SaveLdapPath()
{
	var ldap_params = '&ldap_path_connection_id=' + document.getElementById("ldap_path_connection_id").value;
	ldap_params += '&ldap_path_id='+ document.getElementById("ldap_path_id").value;
	ldap_params += '&ldap_path_dn=' + escape(document.getElementById("ldap_path_dn").value);
	var ldap_path_audit_value = document.getElementById("ldap_path_audit").checked ? "1" : "0";
	ldap_params += '&ldap_path_audit=' + ldap_path_audit_value;
	var xmlpath = new XmlRequestor('admin_config_data.php?sub=f7' + ldap_params);
	document.getElementById('npb_ldap_path_config_div').style.display = 'none';
	RefreshLdapConnectionsList();
}

/**********************************************************************************************************
Function Name:
	EditLdapPath
Description:
	Displays the 'npb_ldap_path_config_div' DIV to edit the selected LDAP path. The uid of the selected path is retrieved from the 
	hidden 'uid' DIV. This uid is passed to an XmlRequestor object to retrieve the path settings from the server as XML string.
	
	Called in response to the "Edit Path" option on the LDAP path menu
Arguments:	None
Returns: 	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function EditLdapPath()
{
	document.getElementById("npb_ldap_path_config_div").style.display = 'block';	
	document.getElementById("npb_ldap_connection_config_div").style.display = 'none';
	var ldap_path_id = document.getElementById("popupmenu_id").innerHTML;
	var pathxml = new XmlRequestor('admin_config_data.php?sub=f8&ldap_path_id=' + ldap_path_id);
	document.getElementById("ldap_path_id").value = ldap_path_id;
	document.getElementById("ldap_path_dn").value = pathxml.GetValue("ldap_path_dn");
	document.getElementById("ldap_path_audit").checked = (pathxml.GetValue("ldap_path_audit") == "1") ? true : false;
}

/**********************************************************************************************************
Function Name:
	DeleteLdapConnection
Description:
	Deletes the selected LDAP connection. The uid of the selected connection is retrieved from the hidden 'uid' DIV. 
	This uid is passed to the server using an HttpRequestor object.
	
	Called in response to the "Delete Connection" option on the LDAP connection menu
Arguments:	None
Returns: 	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function DeleteLdapConnection()
{
	document.getElementById("npb_ldap_path_config_div").style.display = 'none';	
	document.getElementById("npb_ldap_connection_config_div").style.display = 'none';
	var ldap_connection_id = document.getElementById("popupmenu_id").innerHTML;
	var LdapDelete=new HttpRequestor('ldap_connection_results');
	LdapDelete.send('admin_config_data.php?sub=f4&ldap_connection_id=' + ldap_connection_id);
	RefreshLdapConnectionsList();
}

/**********************************************************************************************************
Function Name:
	EditLdapConnection
Description:
	Displays the 'npb_ldap_connection_config_div' DIV to edit the selected LDAP connection. The uid of the selected connection is 
	retrieved from the hidden 'uid' DIV.  This uid is passed to the server using an XmlRequestor object. The current conenection
	settings are extracted from the returned XML data.

	Called in response to the "Edit Connection" option on the LDAP connection menu
Arguments:	None
Returns: 	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function EditLdapConnection()
{
	document.getElementById("npb_ldap_path_config_div").style.display = 'none';	
	document.getElementById("npb_ldap_connection_config_div").style.display = 'block';
	var ldap_connection_id = document.getElementById("popupmenu_id").innerHTML;
	var xmlconfig = new XmlRequestor('admin_config_data.php?sub=f5&ldap_connection_id=' + ldap_connection_id);
	document.getElementById("ldap_connection_results").innerHTML = '';
	document.getElementById("ldap_connection_id").value = ldap_connection_id;
	document.getElementById("ldap_connection_server").value = xmlconfig.GetValue("ldap_connection_server");
	document.getElementById("ldap_connection_user").value = xmlconfig.GetValue("ldap_connection_user");
	document.getElementById("ldap_connection_password").value = xmlconfig.GetValue("ldap_connection_password");
}

/**********************************************************************************************************
Function Name:
	NewLdapConnection
Description:
	Displays the 'npb_ldap_connection_config_div' DIV to enter the LDAP connection settings.
	Called in response to the "New Connection" button
Arguments:	None
Returns: 	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function NewLdapConnection()
{
	document.getElementById("npb_ldap_path_config_div").style.display = 'none';	
	document.getElementById("npb_ldap_connection_config_div").style.display = 'block';
	document.getElementById("ldap_connection_results").innerHTML = '';
	document.getElementById("ldap_connection_server").value = 'LDAP Server FQDN';
	document.getElementById("ldap_connection_user").value = 'LDAP user account';
	document.getElementById("ldap_connection_password").value = 'LDAP password';	
}

/**********************************************************************************************************
Function Name:
	SaveLdapConnection
Description:
	Uses a XmlRequestor object to post the values from the'npb_ldap_connection_config_div' DIV form controls to the server. 
	The returned XML contains a <result> element and a <html> element
	If result is "true" then hides the 'npb_ldap_connection_config_div' DIV.
	Else the text from the <html> element is displayed in the results pane 
	Called in response to the "Save" button on the 'npb_ldap_connection_config_div' DIV
Arguments:	None
Returns: 	None
Change Log:
	20/08/2008			New function	[Nick Brown]
	10/09/2008			Now uses XmlRequestor instead of HttpRequestor	[Nick Brown]
**********************************************************************************************************/
function SaveLdapConnection()
{
	var ldap_params = '&ldap_connection_server=' + document.getElementById("ldap_connection_server").value;
	ldap_params += '&ldap_connection_user=' + document.getElementById("ldap_connection_user").value;
	ldap_params += '&ldap_connection_password=' + document.getElementById("ldap_connection_password").value;
	ldap_params += '&ldap_connection_id=' + document.getElementById("ldap_connection_id").value;
	var LdapSave = new XmlRequestor('admin_config_data.php?sub=f3' + ldap_params);
	if(LdapSave.ParseError != '')
	{
		document.getElementById("ldap_connection_results").innerHTML = LdapSave.ParseError.replace(/</g, "&lt;");
	}
	else
	{
		// Check returned XML for result
		if(LdapSave.GetValue("result")=="false")
		{
			// Failed - Get returned HTML from XML doc and display failure info
			html = new String(LdapSave.SerializeXmlNode(LdapSave.GetNode("html")));
			html = html.substring(6,html.length-7);
			document.getElementById("ldap_connection_results").innerHTML = html;
		}
		else
		{
			// Success - hide connection config div and refresh list
			document.getElementById('npb_ldap_connection_config_div').style.display = 'none';
			RefreshLdapConnectionsList();
		}
	}
}

/**********************************************************************************************************
Function Name:
	TestLdapConnection
Description:
	Uses a HttpRequestor object to post the values from the'npb_ldap_connection_config_div' DIV form controls to the server. Which 
	tests the LDAP credentials and returns the results to the 'ldap_connection_results' DIV as HTML.

	Called in response to the "Test Connection" button on the 'npb_ldap_connection_config_div' DIV
Arguments:	None
Returns: 	None
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function TestLdapConnection()
{
	document.getElementById("ldap_connection_results").innerHTML = 'Testing connection ...';
	var LdapTest=new HttpRequestor('ldap_connection_results');
	var ldap_params = '&ldap_connection_server=' + document.getElementById("ldap_connection_server").value;
	ldap_params += '&ldap_connection_user=' + document.getElementById("ldap_connection_user").value;
	ldap_params += '&ldap_connection_password=' + document.getElementById("ldap_connection_password").value;
	LdapTest.send('admin_config_data.php?sub=f2' + ldap_params);
}
