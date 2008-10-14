<?php
/*
header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
*/
set_time_limit(60);

include "include_config.php";
include "include_lang.php";
include "include_functions.php";
include "include_col_scheme.php";

// Set up SQL connection 
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
mysql_select_db($mysql_database,$db);

// Get global variables
$sub = $_GET["sub"];

error_reporting(0);

// Call data functions
if ($sub == "f1") GetLdapConnections();
if ($sub == "f2") TestLdapConnection();
if ($sub == "f3") SaveLdapConnection();
if ($sub == "f4") DeleteLdapConnection();
if ($sub == "f5") GetLdapConnectionXml();
if ($sub == "f6") GetDefaultNCXml();
if ($sub == "f7") SaveLdapPath();
if ($sub == "f8") GetLdapPathXml();
if ($sub == "f9") DeleteLdapPathXml();

// ****** DeleteLdapPath **************************************************
function DeleteLdapPathXml()
{
	global $db;
	header("Content-type: text/xml");
	EventLog("DeleteLdapPath: Delete Path: ".$_GET["uid"]);

	echo "<DeleteLdapPath>";
	// Delete all users that are tied to this OU ID
	$sql  = "DELETE FROM ldap_users WHERE ou_id='".$_GET["uid"]."'";
	$result = mysql_query($sql, $db);
	echo "<delete_ldap_users>".$result."</delete_ldap_users>";
	
	// Delete all computers that are tied to this OU ID
	$sql  = "DELETE FROM ldap_computers WHERE ou_id='".$_GET["uid"]."'";
	$result = mysql_query($sql, $db);
	echo "<delete_ldap_computers>".$result."</delete_ldap_computers>";
	
	// Then delete path
	$sql  = "DELETE FROM ldap_paths WHERE ou_id='".$_GET["uid"]."'";
	$result = mysql_query($sql, $db);
	echo "<delete_ldap_paths>".$result."</delete_ldap_paths>";
	echo "</DeleteLdapPath>";
}


// ****** GetLdapPathXml **************************************************
function GetLdapPathXml()
{
	global $db;
	header("Content-type: text/xml");

	$sql  = "SELECT ou_dn, include_in_audit FROM ldap_paths WHERE ou_id=".$_GET["uid"];
	$result = mysql_query($sql, $db);
	
	// Return results  as xml
	echo "<paths>";
	if ($myrow = mysql_fetch_array($result))
	{
		do
		{
			echo "<path>";
			echo "<ldap_path_path>".$myrow['ou_dn']."</ldap_path_path>";
			echo "<ldap_path_audit>".$myrow['include_in_audit']."</ldap_path_audit>";
			echo "</path>";
		}	while ($myrow = mysql_fetch_array($result));
	}
	echo "</paths>";
}


// ****** SaveLdapPath **************************************************
function SaveLdapPath()
{
	global $db;
	if (isset($_GET["ldap_path_uid"]) and strlen($_GET["ldap_path_uid"]) > 0)
	{
		EventLog("SaveLdapPath: Edit Path: ".$_GET["ldap_path_uid"]);
		$sql = "UPDATE `ldap_paths` SET ou_dn='".$_GET["ldap_path_path"]."', include_in_audit=".$_GET["ldap_path_audit"]." WHERE ou_id=".$_GET["ldap_path_uid"];
		$result = mysql_query($sql, $db);
	}
	else
	{
		EventLog("SaveLdapPath: New Path: ".$_GET["ldap_path_path"]);
		$sql =  "INSERT INTO `ldap_paths` (`ou_dn`, `ou_domain_guid`, `include_in_audit`) ";
		$sql .= "VALUES ('".$_GET["ldap_path_path"]."','".$_GET["ldap_path_domain_guid"]."',".$_GET["ldap_path_audit"].")";
		$result = mysql_query($sql, $db);
	}
	echo "<SaveLdapPath><query>".$sql."</query><result>";
	echo mysql_error($db);
	echo "</result></SaveLdapPath>";
}

// ****** GetDefaultNCXml **************************************************
function GetDefaultNCXml()
{
	global $db;
	header("Content-type: text/xml");
	$sql  = "SELECT default_nc FROM ldap_connections WHERE guid='".$_GET["guid"]."'";
	$result = mysql_query($sql, $db);
	if ($myrow = mysql_fetch_array($result)){echo "<connection><domain_nc>".$myrow['default_nc']."</domain_nc></connection>";}
}

// ****** GetLdapConnectionConfig **************************************************
function GetLdapConnectionXml()
{
	global $db;
	header("Content-type: text/xml");

	$aes_key = GetVolumeLabel('c'); // 28D7-EBF9

	$sql  = "SELECT ldap_server, AES_DECRYPT(ldap_user,'".$aes_key."') AS ldap_user, AES_DECRYPT(ldap_password,'".$aes_key."') AS ldap_password FROM ldap_connections WHERE guid='".$_GET["guid"]."'";
	$result = mysql_query($sql, $db);
	
	// Return results  as xml
	echo "<connections>";
	if ($myrow = mysql_fetch_array($result))
	{
		do
		{
			echo "<connection>";
			echo "<server>".$myrow['ldap_server']."</server>";
			echo "<user>".$myrow['ldap_user']."</user>";
			echo "<password>".$myrow['ldap_password']."</password>";
			echo "</connection>";
		}	while ($myrow = mysql_fetch_array($result));
	}
	echo "</connections>";
}

// ****** DeleteLdapConnection **************************************************
function DeleteLdapConnection()
{
	global $db;
	EventLog("SaveLdapConnection: Delete Connection: ".$_GET["guid"]);

	echo $_GET["guid"]."<br />";
	// Delete all paths that are tied to this domain GUID
	$sql  = "DELETE FROM ldap_paths WHERE ou_domain_guid='".$_GET["guid"]."'";
	$result= mysql_query($sql, $db);
	echo $result;
	
	// Then delete connection
	$sql  = "DELETE FROM ldap_connections WHERE guid='".$_GET["guid"]."'";
	mysql_query($sql, $db);
	echo "LDAP connection deleted.";
}

// ****** SaveLdapConnection **************************************************
function SaveLdapConnection()
{
	global $db;
	// Connection to server
	$l = ldap_connect($_GET["ldap_server"]);
	$domain_nc = GetDefaultNC($l);
	$config_nc = GetConfigNC($l);
	$fqdn = implode(".",explode(",DC=",substr($domain_nc,3)));

	// Authenticate and get domain GUID and NetBIOS name
	ldap_set_option($l,LDAP_OPT_PROTOCOL_VERSION,3);
	ldap_set_option($l,LDAP_OPT_SIZELIMIT, 1000);
	ldap_set_option($l, LDAP_OPT_REFERRALS, 0);
	ldap_bind($l,$_GET["ldap_user"]."@".$fqdn,$_GET["ldap_password"]);
	$domain_guid = GetDomainGUID($l,$domain_nc);
	$domain_netbios = GetDomainNetbios($l,"CN=Partitions,".$config_nc,$domain_nc);
	ldap_unbind($l);
	
	$aes_key = GetVolumeLabel('c');

	$result = mysql_query("SELECT * FROM ldap_connections WHERE guid='".$domain_guid."'", $db);
	if (mysql_num_rows($result))
	{	
		// UPDATE query - connection already exists so modify
		EventLog("SaveLdapConnection: Edit Connection: ".$domain_netbios);
		$sql  = "UPDATE `ldap_connections` SET `guid`='".$domain_guid."',`default_nc`='".$domain_nc."',`fqdn`='".$fqdn."',";
		$sql .= "`ldap_server`='".$_GET["ldap_server"]."',`ldap_user`=AES_ENCRYPT('".$_GET["ldap_user"]."','".$aes_key."'),";
		$sql .= "`ldap_password`=AES_ENCRYPT('".$_GET["ldap_password"]."','".$aes_key."'),`netbios_name`='".$domain_netbios."' ";	
		$sql .= "WHERE guid='".$domain_guid."'";	
	}
	else
	{
		// INSERT query - new connection
		EventLog("SaveLdapConnection: New Connection: ".$domain_netbios);
		$sql  = "INSERT INTO `ldap_connections` (`guid`,`default_nc`,`fqdn`,`ldap_server`,`ldap_user`,`ldap_password`,`netbios_name`,`schema`) ";	
		$sql .= "VALUES ('".$domain_guid."','".$domain_nc."','".$fqdn."','".$_GET["ldap_server"]."',";
		$sql .= "AES_ENCRYPT('".$_GET["ldap_user"]."','".$aes_key."'),";
		$sql .= "AES_ENCRYPT('".$_GET["ldap_password"]."','".$aes_key."'),'".$domain_netbios."','AD')";
	}
	mysql_query($sql, $db);
}
 
// ****** TestLdapConnection **************************************************
function TestLdapConnection()
{	
	// Check connection to server
	$l = ldap_connect($_GET["ldap_server"]);
	if (!$l) 
	{
		echo "!! Unable to connect to server - check server name";
		return;
	}
	echo "Server connection successful<br />";
	
	$domain_nc = GetDefaultNC($l);
	$config_nc = GetConfigNC($l);
	echo "Default Naming Context: ".$domain_nc."<br />";
	echo "Configuration Naming Context: ".$config_nc."<br />";
	$user_dns_suffix = implode(".",explode(",DC=",substr($domain_nc,3)));
	echo "User DNS Suffix: ".$user_dns_suffix."<br />";

	// Authenticate
	ldap_set_option($l,LDAP_OPT_PROTOCOL_VERSION,3);
	ldap_set_option($l,LDAP_OPT_SIZELIMIT, 1000);
	ldap_set_option($l, LDAP_OPT_REFERRALS, 0);
	if(!ldap_bind($l,$_GET["ldap_user"]."@".$user_dns_suffix,$_GET["ldap_password"]))
	{
		echo "!! Unable to bind to server - check credentials";
		return;
	}
	echo "LDAP bind successful<br />";
	
	$domain_guid =  GetDomainGUID($l,$domain_nc);
	echo "Domain GUID: ".$domain_guid."<br />";
	$domain_netbios = GetDomainNetbios($l,"CN=Partitions,".$config_nc,$domain_nc);
	echo "Domain NetBIOS Name: ".$domain_netbios."<br />";
	ldap_unbind($l);
}

// ****** GetDomainNetbios ***********************************************************************************
function GetDomainNetbios(&$ldap,$ConfigNC,$DomainNC)
{
	$sr = ldap_search($ldap,$ConfigNC,"(nCName=$DomainNC)",array("nETBIOSName"));
	$entries = ldap_get_entries($ldap, $sr);
	$netbios = $entries[0]["netbiosname"][0];
	return $netbios;
}

// ****** GetConfigNC ***********************************************************************************
function GetConfigNC(&$ldap)
{
	ldap_bind($ldap);
	$sr = ldap_read($ldap,null,"(configurationnamingcontext=*)",array("configurationnamingcontext"));
	$entries = ldap_get_entries($ldap, $sr);
	$ConfigNC = $entries[0]["configurationnamingcontext"][0];
	return $ConfigNC;
}

// ****** GetDomainGUID ***********************************************************************************
function GetDomainGUID(&$ldap,&$DomainNC)
{
	$sr = ldap_read($ldap,$DomainNC,"(objectClass=domain)",array("objectguid"));
	$entries = ldap_get_entries($ldap, $sr);
	$guid = formatGUID($entries[0]["objectguid"][0]);
	return $guid;
}

/**********************************************************************************************************
Function Name:
	GetDefaultNC
Description:
	Reads and returns the DefaultNamingContext attribute from RootDSE of the LDAP server
Arguments:
	&$ldap_server			[IN] [RESOURCE]	LDAP resource link
Returns:
	defaultnamingcontext attribute value	[INTEGER]
Change Log:
	25/04/2008			New function	[Nick Brown]
**********************************************************************************************************/
function GetDefaultNC(&$ldap)
{
	ldap_bind($ldap);
	$sr = ldap_read($ldap,null,"(defaultnamingcontext=*)",array("defaultnamingcontext"));
	$entries = ldap_get_entries($ldap, $sr);
	$DefaultNC = $entries[0]["defaultnamingcontext"][0];
	return $DefaultNC;
}

// ****** GetLdapConnections **************************************************
function GetLdapConnections()
{	
	global $db;

	$sql  = "SELECT * FROM ldap_connections";	
	$result = mysql_query($sql, $db);
	
	// Display results table
	echo "<table>";
	echo "<tr><th>LDAP Connections</th><th>LDAP Paths</th></tr>";
	if ($myrow = mysql_fetch_array($result))
	{
		do
		{
			echo "<tr>";
			echo "<td><a id='".$myrow['guid']."' href=\"#\" onMouseover=\"ShowMenu(event,connection_menu);\" onMouseout=\"DelayHideMenu(event)\">";
			echo "<img src=\"images/o_fileserver.png\" />".$myrow['netbios_name']."</a></td>";
			echo "<td>".GetLdapSearchPaths($myrow['guid'])."</td>";
			echo "</tr>";
		}	while ($myrow = mysql_fetch_array($result));
	}
	else
	{
		echo "<tr><td>No LDAP connections defined.</td><td>No LDAP paths defined.</td></tr>";
	}
	echo "</table>";
}

// ****** GetLdapSearchPaths **************************************************
function GetLdapSearchPaths($ConnectionId)
{
	global $db;
	
	$sql  = "SELECT * FROM ldap_paths WHERE ou_domain_guid='".$ConnectionId."'";	
	$result = mysql_query($sql, $db);
	
	// Display results table
	if ($myrow = mysql_fetch_array($result))
	{
		$ReturnHtml = "<ul>";
		do
		{
			$ReturnHtml .= "<li><a id='".$myrow['ou_id']."' href=\"#\" onMouseover=\"ShowMenu(event,path_menu);\" onMouseout=\"DelayHideMenu(event)\"><img src=\"images/ldap-path.jpg\" />".$myrow['ou_dn']."</a></li>";
		}	while ($myrow = mysql_fetch_array($result));
		$ReturnHtml .= "</ul>";
	}
	else {$ReturnHtml = "No LDAP paths defined.";}
	return $ReturnHtml;
}
?>