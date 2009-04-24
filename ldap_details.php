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

	[Nick Brown]	17/04/2009
	Minor change to GetImage(). Added support for $image_link_ldap_attribute and $human_readable_ldap_fields config
	options. Now using DisplayError() from "include_functions.php".
	
	[Nick Brown]	24/04/2009
	Added utf8_encode() to LDAP search filter strings
	
**********************************************************************************************************/
require_once "include.php";

$ldap_info = GetLdapConnection();

// Didn't get LDAP connection -  alert user & done
if ($ldap_info === False)
	{DisplayError(__("Cannot retrieve LDAP details as you have no LDAP connection defined for this domain."));}

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
$sr = ldap_search($ldap, $ldap_info['nc'], utf8_encode($filter), $attributes);
$info = ldap_get_entries($ldap, $sr);

// Couldn't retrieve user or computer object from LDAP - alert user & done
if ($info == NULL)
	{DisplayError(__("Cannot retrieve LDAP details. The ").$_GET["record_type"].__(" object cannot be found in the LDAP source - ").$ldap_info["name"]);}

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
<img src='<?php echo GetImage($info[0][$image_link_ldap_attribute][0]);?>' 
alt='<?php echo $info[0][$image_link_ldap_attribute][0];?>'/>
<?php 
echo ($_GET["full_details"] == "y" ? __("Full") : __("Partial")) ;
echo __(' LDAP details for '). $info[0][$image_link_ldap_attribute][0]." [".$ldap_info["name"]."]";
?>
	<hr />
</div>
<!-- LDAP details table -->
<table>
<tr><th><?php echo __("Attribute");?></th><th><?php echo __("Value");?></th></tr>

<?php
// Dump LDAP data into table
foreach ($info[0] as $key => $value)
{
	if(!is_numeric($key) && ($key != "count") && ($key != "dn")) 
	{
		array_shift($value);
		$val = FormatLdapValue($key, $value);
		echo "<tr class='".alternate_tr_class($tr_class)."'>";
		$key = $human_readable_ldap_fields ? __($key) : $key;
		echo "<td>".$key."</td><td>$val</td></tr>";
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
	$name	[IN]	[STRING]	user or computer samaccountname value (or user full name [AJH]) 
Returns:
	Image file name/path	[STRING]
Change Log:
	06/04/2009			New function	[Nick Brown]
    15/04/2009          Updated. Made things a bit more defensive, added a default_file$ image.
                        Fixed up a PHP bug which could claim the image file existed even if it didn't.
                        Also defined exactly what the "record_type" was from $_GET as it assumed we
                        were assuming if not user, must be computer, however I managed to break this ;¬) 
                        Image for user is now back to "Full Name.jpg" was "firstame.jpg" but wont work for 
                        most users.  [AJH]
	17/04/2009			Minor change. Removed a few lines of redundant code.	[Nick Brown]
**********************************************************************************************************/
function GetImage($name)
{
	// First, assume we dont know what ldap object we have
	$default_file= 'images/o_unknown.png';
	if ($_GET["record_type"] == "computer")
	{
    // We have established it is a computer, so set the defaults for computers
    $default_file= 'images/o_terminal_server.png';
		$this_file = './images/equipment/'.$name.'.jpg';
		if (is_file($this_file)) {return $this_file;} 
  }
  else if ($_GET["record_type"] == "user")
  {
		// We have established object is a user, so set the defaults for user
    $default_file= 'images/groups_l.png';
		$this_file = './images/people/'.$name.'.jpg';
		if (is_file($this_file)) {return $this_file;} 
  }
  // Return default. 
	return $default_file;
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
	$known_binary_fields = Array("ciscoecsbuumlocationobjectid","msexchmailboxsecuritydescriptor","msexchrecordedname",
		"sidhistory","userparameters","logonhours","replicationsignature");
	if (preg_match("/(".implode("|",$known_binary_fields).")/i", $name)) {return "[Binary Data]";}
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
