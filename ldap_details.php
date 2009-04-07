<?php
/**********************************************************************************************************
Module:	ldap_details.php

Description:
	This module displays the user or computer LDAP details - linked to from the system summary page. The following form 
	variables are expected to be supplied to the page:
	$_GET("record_type") - user or computer
	$_GET("full_details") - Y or N whether full or partial LDAP details are returned
	$_GET("uuid") - Open Audit ID of the system
	
Change Control:

	[Nick Brown]	03/04/2009
	Re-wrote module from scratch
	
**********************************************************************************************************/
require_once "include.php";

$ldap_info = GetLdapConnection();

// Didn't get LDAP connection -  alert user
if ($ldap_info === False)
{
	echo "<td><div class='ldap_details'>";
	echo "<img src='images/emblem_important.png'/>";
	echo __("Cannot retrieve LDAP details as you have no LDAP connection defined for this domain.");
	echo "</div></td>";
	include "include_right_column.php";
	die;
}

// Connect (authenticate) to LDAP
$upn = isEmailAddress($ldap_info['user']) ? $ldap_info['user'] : $ldap_info['user']."@".$ldap_info['fqdn'];
$ldap = ConnectToLdapServer($ldap_info['server'],$upn,$ldap_info['password']);

// Get LDAP info
if($_GET["record_type"] == "computer")
{
	$sam_account_name = $ldap_info['system_name']."$";
	$attributes = ($_GET["full_details"] == "y") ? Array() : $computer_ldap_attributes;
}
else
{
	// Get user account name - user name *may* be in DOMAIN\ACCOUNT format or may not :-)
	$sam_account_name =(stripos($ldap_info["net_user_name"],"\\") !== FALSE) ? array_pop(explode("\\",$ldap_info["net_user_name"])) : $ldap_info["net_user_name"];;
	$attributes = ($_GET["full_details"] == "y") ? Array() : $user_ldap_attributes;
}
$filter = "(&(objectClass=".$_GET["record_type"].")(sAMAccountName=".$sam_account_name."))";
$sr = ldap_search($ldap, $ldap_info['nc'], $filter, $attributes);
$info = ldap_get_entries($ldap, $sr);

// Couldn't retrieve user or computer object from LDAP - alert user
if ($info == NULL)
{
	echo "<td><div class='ldap_details'>";
	echo "<img src='images/emblem_important.png'/>";
	echo __("Cannot retrieve LDAP details. The ").$_GET["record_type"].__(" object cannot be found in the LDAP source - ").$ldap_info["name"];
	echo "</div></td>";
	include "include_right_column.php";
	die;
}

// ObjectSid is binary - need to use ldap_get_values_len() to ensure that it's correctly retrieved - only needed if retrieving full attributes
if ($_GET["full_details"] == "y")
{
	$entry = ldap_first_entry($ldap, $sr);
	$objectsid = ldap_get_values_len($ldap, $entry, "objectsid");
	$info[0]["objectsid"][0] = $objectsid[0];
}
// Sort by keys
ksort($info[0]);
?>

<!-- LDAP details header -->
<td>
<div class='ldap_details'>
<div>
	<img src='images/<?echo GetImage($sam_account_name);?>' alt='<?echo $sam_account_name;?>'/>
<?echo ($_GET["full_details"] == "y" ? "Full" : "Partial") ;?>
 LDAP details for 
<?echo $info[0]["name"][0]." [".$ldap_info["name"]."]";?>
	<hr />
</div>
<!-- LDAP details table -->
<table>
<tr><th><?echo __("Attribute");?></th><th><?echo __("Value");?></th></tr>

<?
// Dump LDAP data into table
foreach ($info[0] as $key => $value)
{
	if(!is_numeric($key) && ($key != "count") && ($key != "dn")) 
	{
		array_shift($value);
		$val = FormatLdapValue($key, $value);
		echo "<tr class='".alternate_tr_class($tr_class)."'>";
		echo "<td>$key</td><td>$val</td></tr>";
	}
}
echo "</table></div></td>";
include "include_right_column.php";

/**********************************************************************************************************
Function Name:
	GetImage
Description:
	Returns image file name - image to be displayed with the LDAP details. If image file exists that matches user account name,
	then this filname is returned else default user or computer account image filenames are used. 
Arguments:
	$name	[IN]	[STRING]	user or computer samaccountname value
Returns:
	Image file name/path	[STRING]
Change Log:
	06/04/2009			New function	[Nick Brown]
**********************************************************************************************************/
function GetImage($name)
{
	if ($_GET["record_type"] == "computer")
	{
		$file = "equipment/$name.jpg";
    if (file_exists("images/$file")) {return $file;} 
		else {return "o_terminal_server.png";}
  }
  else
	{
		$file = "people/$name.jpg";
    if (file_exists("images/$file")) {return $file;}
		else {return "groups_l.png";}
	}
}

/**********************************************************************************************************
Function Name:
	FormatLdapValue
Description:
	Applies formatting to specific LDAP values (or types of values)
Arguments:
	$name	[IN]	[STRING]		LDAP attribute name
	$value	[IN]	[VARIANT]		LDAP attribute value	
Returns:
	Formatted LDAP value string	[STRING]
Change Log:
	03/04/2009			New function	[Nick Brown]
**********************************************************************************************************/
function FormatLdapValue(&$name, &$value)
{
	if (preg_match("/guid$/i", $name)) {return formatGUID($value[0]);}
	if (preg_match("/sid$/i", $name)) {return ConvertBinarySidToSddl($value[0]);}
	if (count($value)>1) {return "<ul><li>".implode("</li><li>",$value)."</li></ul>";}
	return $value[0];
}

/**********************************************************************************************************
Function Name:
	GetLdapConnection
Description:
	Determine if we have an LDAP connection defined for the user or computer domain and return connection details
Arguments: None
Returns:
	LDAP connection details		[ARRAY]
Change Log:
	03/04/2009			New function	[Nick Brown]
**********************************************************************************************************/
function GetLdapConnection()
{
	$db = ConnectToOpenAuditDb();

	// Get domain that we need to connect to - user and computer may be different domains
	$sql = "SELECT system_name, net_domain, net_user_name FROM system WHERE system_uuid = '".$_GET["uuid"]."'";
	$result = mysql_query($sql, $db);
	$system = mysql_fetch_array($result);
	// Get user domain - user name *may* be in DOMAIN\ACCOUNT format or may not :-)
	if ($_GET["record_type"] == "user")
	{
		$domain =(stripos($system["net_user_name"],"\\") !== FALSE) ? array_shift(explode("\\",$system["net_user_name"])) : $system["net_domain"];;
	}
	else {$domain = $system["net_domain"];}
	
	// Now get ldap connection info for that domain, if any ...
	$aeskey = GetAesKey();	
	$sql = "SELECT ldap_connections_server as server, ldap_connections_nc as nc, 
					ldap_connections_fqdn  as fqdn, ldap_connections_name as name, 
					AES_DECRYPT(`ldap_connections_user`,'".$aeskey."') as user, 
					AES_DECRYPT(`ldap_connections_password`,'".$aeskey."') as password 
					FROM ldap_connections
					WHERE ldap_connections_fqdn = '$domain' OR ldap_connections_name = '$domain'";			
	$result = mysql_query($sql, $db);
	$ldap_info = (($ldap = mysql_fetch_array($result)) === FALSE) ? FALSE : array_merge($system, $ldap);
	mysql_close($db);
	
	return $ldap_info;
}
?>
