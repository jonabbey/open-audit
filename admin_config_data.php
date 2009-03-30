<?php
/**********************************************************************************************************
Module Comments:
	
	[Nick Brown]	10/09/2008
	Functions in this module are called by the AJAX objects (XMLRequestor & HTMLRequestor)  and return XML or HTML
	content back to the calling page.

	[Nick Brown]	19/09/2008
	Re-wrote main routine  to using a switch statement
	Made each separate function return a value rather than echoing a response directly

	[Nick Brown]	17/03/2009
	SaveLdapConnectionXml() & GetLdapConnectionXml now use GetAesKey()
	
**********************************************************************************************************/
set_time_limit(60);
header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
include "include_config.php";
include "include_lang.php";
include "include_functions.php";
include "include_col_scheme.php";
error_reporting(0);

// Set up SQL connection 
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
mysql_select_db($mysql_database,$db);

// Call data functions
switch($_GET["sub"])
{
	case "f1": exit(GetLdapConnectionsHtml($db));
	case "f2": exit(TestLdapConnectionHtml($db));
	case "f3": exit(SaveLdapConnectionXml($db));
	case "f4": exit(DeleteLdapConnectionHtml($db));
	case "f5": exit(GetLdapConnectionXml($db));
	case "f6": exit(GetDefaultNCXml($db));
	case "f7": exit(SaveLdapPathXml($db));
	case "f8": exit(GetLdapPathXml($db));
	case "f9": exit(DeleteLdapPathXml($db));
}

/**********************************************************************************************************
Function Name:
	GetLdapConnectionsHtml
Description:
	Retrieves configured LDAP connections and paths from the DB. Displays them as an HTML table.
Arguments:
	$db	[IN] [Resource]	DB connection	
Returns:		
	[String]	XML string containing the success status of the operation
Change Log:
	01/09/2008			New function	[Nick Brown]
	19/09/2008			Removed echo  statments and replaced with response string	[Nick Brown]
	22/09/2008			Renamed function [Nick Brown]
**********************************************************************************************************/
function GetLdapConnectionsHtml($db)
{	
	$sql  = "SELECT * FROM ldap_connections";	
	$result = mysql_query($sql, $db);
	
	// Display results table
	$response = "<table>";
	$response .= "<tr><th>LDAP Connections</th><th>LDAP Paths</th></tr>";
	if ($myrow = mysql_fetch_array($result))
	{
		do
		{
			$response .= "<tr>";
			$response .= "<td><a id='".$myrow['ldap_connections_id']."' href=\"#\" onMouseover=\"ShowMenu(event,connection_menu);\" onMouseout=\"DelayHideMenu(event)\">";
			$response .= "<img src=\"images/o_fileserver.png\" />".$myrow['ldap_connections_name']."</a></td>";
			$response .= "<td>".GetLdapPathsHtml($myrow['ldap_connections_id'],$db)."</td>";
			$response .= "</tr>";
		}	while ($myrow = mysql_fetch_array($result));
	}
	else
	{
		$response .= "<tr><td>No LDAP connections defined.</td><td>No LDAP paths defined.</td></tr>";
	}
	$response .= "</table>";

	return $response;
}

/**********************************************************************************************************
Function Name:
	GetLdapPathsHtml
Description:
	Retrieves all LDAP paths associated with the supplied LDAP connection.
	Returns them as an HTML list
Arguments:
	$ConnectionGuid	[IN] [String]		LDAP Connection GUID
	$db					[IN] [Resource]	DB connection	
Returns:		
	[String] HTML string containing the list of LDAP paths
Change Log:
	01/09/2008			New function	[Nick Brown]
	19/09/2008			Removed echo  statments and replaced with response string	[Nick Brown]
	22/09/2008			Function renamed	[Nick Brown]
**********************************************************************************************************/
function GetLdapPathsHtml($ConnectionGuid, $db)
{
	
	$sql  = "SELECT * FROM ldap_paths WHERE ldap_paths_connection_id='".$ConnectionGuid."'";	
	$result = mysql_query($sql, $db);
	
	// Display results table
	if ($myrow = mysql_fetch_array($result))
	{
		$response = "<ul>";
		do
		{
			$response .= "<li><a id='".$myrow['ldap_paths_id']."' href=\"#\" onMouseover=\"ShowMenu(event,path_menu);\" onMouseout=\"DelayHideMenu(event)\"><img src=\"images/ldap-path.jpg\" />".$myrow['ldap_paths_dn']."</a></li>";
		}	while ($myrow = mysql_fetch_array($result));
		$response .= "</ul>";
	}
	else {$response = "No LDAP paths defined - use \"Add New Path\" from the LDAP Connection drop-down menu on the left.";}

	return $response;
}

/**********************************************************************************************************
Function Name:
	TestLdapConnectionHtml
Description:
	Attenpts to bind and authenticate to LDAP host using $_GET["ldap_connection_server"], $_GET["ldap_connection_user"] 
	and $_GET["ldap_connection_password"]	- Returns progress/result as HTML
Arguments:
	$db	[IN] [Resource]	DB connection	
Returns:		
	[String] HTML string containing the connection result
Change Log:
	01/09/2008			New function	[Nick Brown]
	19/09/2008			Removed echo  statments and replaced with response string	[Nick Brown]
	22/09/2008			Function renamed	[Nick Brown]
**********************************************************************************************************/
function TestLdapConnectionHtml($db)
{	
	// Connect anonymously to get default domain NC & config NC
	$l = ConnectToLdapServer($_GET["ldap_connection_server"]);
	if (is_array($l))
	{
		$response .=  "!! Unable to bind to server !!<br />";
		$response .=  "Err Number: ".$l["number"]."<br />";
		$response .=  "Err String: ".$l["string"]."<br />";
		$response .=  "Check that server name is correct";
		return $response;
	}	
	$response .=  "Server connection successful<br />";
	$domain_nc = GetDefaultNC($l);
	$response .=  "Default Naming Context: ".$domain_nc."<br />";
	$user_dns_suffix = implode(".",explode(",DC=",substr($domain_nc,3)));
	$response .=  "User DNS Suffix: ".$user_dns_suffix."<br />";
	ldap_unbind($l);
	
	// Try to bind using supplied credentials
	$ldap_user = isEmailAddress($_GET["ldap_connection_user"]) ? $_GET["ldap_connection_user"] : $_GET["ldap_connection_user"]."@".$user_dns_suffix;
	$l = ConnectToLdapServer($_GET["ldap_connection_server"],$ldap_user,$_GET["ldap_connection_password"]);
	if (is_array($l))
	{
		$response .=  "!! Unable to bind to server !!<br />";
		$response .=  "Err Number: ".$l["number"]."<br />";
		$response .=  "Err String: ".$l["string"]."<br />";
		$response .=  "Check that credentials are correct";
		return $response;
	}	
	ldap_unbind($l);
	$response .=  "LDAP bind successful<br />";

	return $response;
}

/**********************************************************************************************************
Function Name:
	GetDefaultNC
Description:
	Reads and returns the DefaultNamingContext attribute from RootDSE of the LDAP server
Arguments:
	&$ldap	[IN] [RESOURCE]	LDAP resource link
Returns:
	[String]	defaultnamingcontext attribute value
Change Log:
	25/04/2008			New function	[Nick Brown]
**********************************************************************************************************/
function GetDefaultNC(&$ldap)
{
	$sr = ldap_read($ldap,null,"(defaultnamingcontext=*)",array("defaultnamingcontext"));
	$entries = ldap_get_entries($ldap, $sr);
	$DefaultNC = $entries[0]["defaultnamingcontext"][0];
	return $DefaultNC;
}

/**********************************************************************************************************
Function Name:
	SaveLdapConnectionXml
Description:
	Writes the supplied LDAP connection settings to the DB. Performs a test first to check if settings are valid:
	HTML reponse is stored in the <html> XML element
	Result status is stored in <result> XML element
Arguments:
	$db	[IN] [RESOURCE]	DB resource
Returns:
	[String]	XML string containing the HTML response and the test conenction status
Change Log:
	25/04/2008			New function	[Nick Brown]
	19/09/2008			Removed echo  statments and replaced with response string	[Nick Brown]
	17/03/2009			Using GetAesKey() instead of GetVolumeLabel()
**********************************************************************************************************/
function SaveLdapConnectionXml($db)
{
	header("Content-type: text/xml");

	// Validate supplied details
	$html = TestLdapConnectionHtml();
	$testresult = (strpos($html,"LDAP bind successful") === false) ? "false" : "true";
	if($testresult != "true") return "<SaveLdapConnection><html>".$html."</html><result>".$testresult."</result></SaveLdapConnection>";
	
	// Connect anonymously to get default domain NC & config NC
	$l = ConnectToLdapServer($_GET["ldap_connection_server"]);
	$domain_nc = GetDefaultNC($l);
	$config_nc = GetConfigNC($l);
	$fqdn = implode(".",explode(",DC=",substr($domain_nc,3)));
	ldap_unbind($l);

	// Authenticate and get domain GUID and NetBIOS name
	$ldap_user = isEmailAddress($_GET["ldap_connection_user"]) ? $_GET["ldap_connection_user"] : $_GET["ldap_connection_user"]."@".$fqdn;
	$l = ConnectToLdapServer($_GET["ldap_connection_server"],$ldap_user,$_GET["ldap_connection_password"]);
	$ldap_connection_name = GetDomainNetbios($l,"CN=Partitions,".$config_nc,$domain_nc);
	ldap_unbind($l);
	
	$aes_key = GetAesKey();
	if (isset($_GET["ldap_connection_id"]) and strlen($_GET["ldap_connection_id"]) > 0)
	{
		// UPDATE query - connection already exists so modify
		LogEvent("admin_config_data.php","SaveLdapConnectionXml","Edit Connection: ".$ldap_connection_name);
		$sql  = "UPDATE `ldap_connections` SET `ldap_connections_nc`='".$domain_nc."',`ldap_connections_fqdn`='".$fqdn."',";
		$sql .= "`ldap_connections_server`='".$_GET["ldap_connection_server"]."',`ldap_connections_user`=AES_ENCRYPT('".$_GET["ldap_connection_user"]."','".$aes_key."'),";
		$sql .= "`ldap_connections_password`=AES_ENCRYPT('".$_GET["ldap_connection_password"]."','".$aes_key."'),`ldap_connections_name`='".$ldap_connection_name."' ";	
		$sql .= "WHERE ldap_connections_id='".$_GET["ldap_connection_id"]."'";	
	}
	else
	{
		// INSERT query - new connection
		LogEvent("admin_config_data.php","SaveLdapConnectionXml","New Connection: ".$ldap_connection_name);
		$sql  = "INSERT INTO `ldap_connections` (`ldap_connections_nc`,`ldap_connections_fqdn`,`ldap_connections_server`,`ldap_connections_user`,`ldap_connections_password`,`ldap_connections_name`,`ldap_connections_schema`) ";	
		$sql .= "VALUES ('".$domain_nc."','".$fqdn."','".$_GET["ldap_connection_server"]."',";
		$sql .= "AES_ENCRYPT('".$_GET["ldap_connection_user"]."','".$aes_key."'),";
		$sql .= "AES_ENCRYPT('".$_GET["ldap_connection_password"]."','".$aes_key."'),'".$ldap_connection_name."','AD')";
	}
	
	mysql_query($sql, $db);
	//return "<SaveLdapConnection><html>".$html."</html><sql_query>".$sql."</sql_query><result>".$testresult."</result></SaveLdapConnection>";
	return "<SaveLdapConnection><html>".$html."</html><result>".$testresult."</result></SaveLdapConnection>";
}

/**********************************************************************************************************
Function Name:
	GetConfigNC
Description:
	Reads and returns the ConfigurationNamingContext attribute from RootDSE of the LDAP server
Arguments:
	&$ldap	[IN] [RESOURCE]	LDAP resource link
Returns:
	[String]	configurationnamingcontext attribute value
Change Log:
	25/04/2008			New function	[Nick Brown]
**********************************************************************************************************/
function GetConfigNC(&$ldap)
{
	$sr = ldap_read($ldap,null,"(configurationnamingcontext=*)",array("configurationnamingcontext"));
	$entries = ldap_get_entries($ldap, $sr);
	$ConfigNC = $entries[0]["configurationnamingcontext"][0];
	return $ConfigNC;
}

/**********************************************************************************************************
Function Name:
	GetDomainGUID
Description:
	Reads and returns the objectGuid attribute of the domain object
Arguments:
	&$ldap			[IN] [RESOURCE]	LDAP resource link
	$DomainDN	[IN] [String]			Domain distinguished name
Returns:
	[String]	objectguid attribute value
Change Log:
	25/04/2008			New function	[Nick Brown]
**********************************************************************************************************/
function GetDomainGUID(&$ldap, $DomainDN)
{
	$sr = ldap_read($ldap,$DomainDN,"(objectClass=domain)",array("objectguid"));
	$entries = ldap_get_entries($ldap, $sr);
	$guid = formatGUID($entries[0]["objectguid"][0]);
	return $guid;
}

/**********************************************************************************************************
Function Name:
	GetDomainNetbios
Description:
	Reads and returns the NetBIOS name of the domain from within the configuration naming context container
Arguments:
	&$ldap			[IN] [RESOURCE]	LDAP resource link
	$ConfigNC		[IN] [String]				Domain distinguished name
	$DomainDN	[IN] [String]			Domain distinguished name
Returns:
	[String]	nETBIOSName attribute value
Change Log:
	25/04/2008			New function	[Nick Brown]
**********************************************************************************************************/
function GetDomainNetbios(&$ldap,$ConfigNC,$DomainDN)
{
	$sr = ldap_search($ldap,$ConfigNC,"(nCName=$DomainDN)",array("nETBIOSName"));
	$entries = ldap_get_entries($ldap, $sr);
	$netbios = $entries[0]["netbiosname"][0];
	return $netbios;
}

/**********************************************************************************************************
Function Name:
	DeleteLdapConnectionHtml
Description:
	Deletes all references to LDAP connection from DB, including realated LDAP paths, users and computers
Arguments:
	$db	[IN] [RESOURCE]	DB resource
Returns:
	[String]	HTML string 
Change Log:
	25/04/2008			New function	[Nick Brown]
	19/09/2008			Removed echo  statments and replaced with response string	[Nick Brown]
	25/09/2008			Added queries to ensure that related LDAP users and computers are deleted [Nick Brown]
**********************************************************************************************************/
function DeleteLdapConnectionHtml($db)
{
	LogEvent("admin_config_data.php","DeleteLdapConnectionHtml","Delete Connection: ".$_GET["ldap_connection_id"]);

	$response = $_GET["ldap_connection_id"]."<br />";

	// Delete LDAP users that are related to this connection  GUID
	$sql  = "DELETE ldap_users 
	FROM ldap_connections, ldap_paths, ldap_users
	WHERE ldap_paths.ldap_paths_connection_id=ldap_connections.ldap_connections_id
	AND ldap_users.ou_id=ldap_paths.ldap_paths_id
	AND ldap_connections.ldap_connections_id='".$_GET["ldap_connection_id"]."'";
	$result= mysql_query($sql, $db);
	
	// Delete LDAP computers that are related to this connection GUID
	$sql  = "DELETE ldap_computers 
	FROM ldap_connections, ldap_paths, ldap_computers 
	WHERE ldap_paths.ldap_paths_connection_id=ldap_connections.ldap_connections_id
	AND ldap_computers.ldap_computers_path_id=ldap_paths.ldap_paths_id
	AND ldap_connections.ldap_connections_id='".$_GET["ldap_connection_id"]."'";
	$result= mysql_query($sql, $db);

	// Delete LDAP paths related to this connection GUID
	$sql  = "DELETE ldap_connections, ldap_paths 
	FROM ldap_connections, ldap_paths 
	WHERE ldap_paths.ldap_paths_connection_id=ldap_connections.ldap_connections_id
	AND ldap_connections.ldap_connections_id='".$_GET["ldap_connection_id"]."'";
	$result= mysql_query($sql, $db);
	
	// Delete LDAP connection
	$sql  = "DELETE FROM ldap_connections 
	WHERE ldap_connections.ldap_connections_id='".$_GET["ldap_connection_id"]."'";
	$result= mysql_query($sql, $db);

	$response .= "LDAP connection deleted.";
	return $response;
}

/**********************************************************************************************************
Function Name:
	GetLdapConnectionXml
Description:
	Gets LDAP connection details from the DB for an LDAP connection defined by $_GET["ldap_connection_id"]
	Returns an XML string containing the info	
Arguments:
	$db	[IN] [RESOURCE]	DB resource
Returns:
	[String]	XML string containing connection details 
Change Log:
	25/04/2008			New function	[Nick Brown]
	19/09/2008			Removed echo  statments and replaced with response string	[Nick Brown]
	17/03/2009			Using GetAesKey() instead of GetVolumeLabel()	[Nick Brown]
**********************************************************************************************************/
function GetLdapConnectionXml($db)
{
	header("Content-type: text/xml");
	$aes_key = GetAesKey();

	$sql = "SELECT ldap_connections_server, AES_DECRYPT(ldap_connections_user,'".$aes_key."') 
	AS ldap_user, AES_DECRYPT(ldap_connections_password,'".$aes_key."') AS ldap_password FROM ldap_connections 
	WHERE ldap_connections_id='".$_GET["ldap_connection_id"]."'";
	$result = mysql_query($sql, $db);
	
	// Return results  as xml
	$response = "<connections>";
	if ($myrow = mysql_fetch_array($result))
	{
		do
		{
			$response .= "<connection>";
			$response .= "<ldap_connection_server>".$myrow['ldap_connections_server']."</ldap_connection_server>";
			$response .= "<ldap_connection_user>".$myrow['ldap_user']."</ldap_connection_user>";
			$response .= "<ldap_connection_password>".$myrow['ldap_password']."</ldap_connection_password>";
			$response .= "</connection>";
		}	while ($myrow = mysql_fetch_array($result));
	}
	$response .= "</connections>";

	return $response;
}

/**********************************************************************************************************
Function Name:
	GetDefaultNCXml
Description:
	Gets the "ldap_connections_nc" value from the DB for an LDAP connection defined by $_GET["ldap_connection_id"]
	Returns an XML string containing the requested info
Arguments:
	$db	[IN] [Resource]	DB connection	
Returns:		
	[String]	XML string containing the requested info
Change Log:
	01/09/2008			New function	[Nick Brown]
	19/09/2008			Removed echo  statments and replaced with response string	[Nick Brown]
**********************************************************************************************************/
function GetDefaultNCXml($db)
{
	header("Content-type: text/xml");
	$sql  = "SELECT ldap_connections_nc FROM ldap_connections WHERE ldap_connections_id='".$_GET["ldap_connection_id"]."'";
	$result = mysql_query($sql, $db);
	if ($myrow = mysql_fetch_array($result)){$response = "<connection><domain_nc>".$myrow['ldap_connections_nc']."</domain_nc></connection>";}

	return $response;
}

/**********************************************************************************************************
Function Name:
	SaveLdapPathXml
Description:
	Saves LDAP path info defined by $_GET["ldap_path_id"]) to the DB
Arguments:
	$db	[IN] [Resource]	DB connection	
Returns:		
	[String]	XML string containing the success status of the operation
Change Log:
	01/09/2008			New function	[Nick Brown]
	19/09/2008			Removed echo  statments and replaced with response string	[Nick Brown]
**********************************************************************************************************/
function SaveLdapPathXml($db)
{
	if (isset($_GET["ldap_path_id"]) and strlen($_GET["ldap_path_id"]) > 0)
	{
		LogEvent("admin_config_data.php","SaveLdapPathXml","Edit Path: ".$_GET["ldap_path_id"]);
		$sql = "UPDATE `ldap_paths` SET ldap_paths_dn='".$_GET["ldap_path_dn"]."', ldap_paths_audit=".$_GET["ldap_path_audit"]." WHERE ldap_paths_id=".$_GET["ldap_path_id"];
		$result = mysql_query($sql, $db);
	}
	else
	{
		LogEvent("admin_config_data.php","SaveLdapPathXml", "New Path: ".$_GET["ldap_path_dn"]);
		$sql =  "INSERT INTO `ldap_paths` (`ldap_paths_dn`, `ldap_paths_connection_id`, `ldap_paths_audit`) ";
		$sql .= "VALUES ('".$_GET["ldap_path_dn"]."','".$_GET["ldap_path_connection_id"]."',".$_GET["ldap_path_audit"].")";
		$result = mysql_query($sql, $db);
	}
	$response =  "<SaveLdapPath><query>".$sql."</query><result>";
	$response .=  mysql_error($db);
	$response .=  "</result></SaveLdapPath>";

	return $response;
}

/**********************************************************************************************************
Function Name:
	GetLdapPathXml
Description:
	Obtains LDAP path info defined by $_GET["ldap_path_id"]) from the DB
Arguments:
	$db	[IN] [Resource]	DB connection	
Returns:		
	[String]	XML string containing the success status of the operation
Change Log:
	01/09/2008			New function	[Nick Brown]
	19/09/2008			Removed echo  statments and replaced with response string	[Nick Brown]
**********************************************************************************************************/
function GetLdapPathXml($db)
{
	header("Content-type: text/xml");

	$sql  = "SELECT ldap_paths_dn, ldap_paths_audit FROM ldap_paths WHERE ldap_paths_id=".$_GET["ldap_path_id"];
	$result = mysql_query($sql, $db);
	
	// Return results  as xml
	$response =  "<paths>";
	if ($myrow = mysql_fetch_array($result))
	{
		do
		{
			$response .=  "<path>";
			$response .=  "<ldap_path_dn>".$myrow['ldap_paths_dn']."</ldap_path_dn>";
			$response .=  "<ldap_path_audit>".$myrow['ldap_paths_audit']."</ldap_path_audit>";
			$response .=  "</path>";
		}	while ($myrow = mysql_fetch_array($result));
	}
	$response .=  "</paths>";

	return $response;
}

/**********************************************************************************************************
Function Name:
	DeleteLdapPathXml
Description:
	Deletes the LDAP path defined by $_GET["ldap_path_id"]) from the DB
	Also deletes users and computers owned by the LDAP path
Arguments:
	$db	[IN] [Resource]	DB connection	
Returns:		
	[String]	XML string containing the success status of the operation
Change Log:
	01/09/2008			New function	[Nick Brown]
	19/09/2008			Removed echo  statments and replaced with response string	[Nick Brown]
**********************************************************************************************************/
function DeleteLdapPathXml($db)
{
	header("Content-type: text/xml");
	LogEvent("admin_config_data.php","DeleteLdapPathXml", "Path: ".$_GET["ldap_path_id"]);

	$response = "<DeleteLdapPath><result>";

	// Delete LDAP users that are related to this connection  GUID
	$sql  = "DELETE ldap_users 
	FROM ldap_paths, ldap_users
	WHERE ldap_users.ou_id=ldap_paths.ldap_paths_id
	AND ldap_paths.ldap_paths_id='".$_GET["ldap_path_id"]."'";
	$result = mysql_query($sql, $db);
	
	// Delete LDAP computers that are related to this connection GUID
	$sql  = "DELETE ldap_computers 
	FROM ldap_paths, ldap_computers
	WHERE ldap_computers.ldap_computers_path_id=ldap_paths.ldap_paths_id
	AND ldap_paths.ldap_paths_id='".$_GET["ldap_path_id"]."'";
	$result = mysql_query($sql, $db);

	// Delete LDAP path defined by $_GET["uid"]
	$sql  = "DELETE FROM ldap_paths	WHERE ldap_paths.ldap_paths_id='".$_GET["ldap_path_id"]."'";
	$result = mysql_query($sql, $db);
	
	$response .= $result."</result></DeleteLdapPath>";
	return $response;
}

?>