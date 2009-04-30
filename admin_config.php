<?php
/**********************************************************************************************************
Module:	admin_config.php

Description:
	This page prvides the GUI and code to change the system configuration settings. It is comprised of three sections:
	1. Validation of $_POST values
	2. Creation of a new 'include_config.php' file based on the validated $_POST Settings
	3. HTML page content (mostly a FORM)

	Steps 1. and 2 are only executed when the HTML FORM has been POSTed to the page 
	(i.e. $_POST['submit_button'] is defined)
		
Recent Changes:
	
	[Nick Brown]	20/08/2008
	Original GUI and code redesigned.

	[Nick Brown]	02/03/2009
	Added $admin_list_post and $user_list support in "include_config.php" - there is no GUI for modifying these values yet
	Added an include for "include_config_defaults.php" in "include_config.php"
	
	[Nick Brown]	23/04/2009
	Added $human_readable_ldap_fields and  $image_link_ldap_attribute options to the GUI. Tidied up code to get closer to
	completely valid XHTML markup and make markup more readable.

	[Nick Brown]	29/04/2009
	Moved <link>s and <script>s into "include.php" <head>. XHTML markup is now valid. Added handling of cases where LDAP
	extension is not available.

**********************************************************************************************************/

$page = "admin";
include "include.php";
$break = false;

if(isset($_POST['submit_button']))
{
	/**********************************************************************************************************
	$_POST['submit_button'] is defined - i.e. FORM has been submitted - check POSTed values
	**********************************************************************************************************/

	// *************** Check General settings ************************************************
	if ($_POST['mysql_server_post'] == "") 
	{
		echo "<font color=red>" . __("You must declare a MySQL Server") . ".</font>"; 
		$break = true; // database definition error
	} 
	if ($_POST['mysql_database_post'] == "") 
	{
		echo "<font color=red>" . __("You must declare a MySQL Database") . ".</font>"; 
		$break = true; // database definition error
	}
	if ($_POST['mysql_user_post'] == "")
	{
		echo "<font color=red>" . __("You must declare a MySQL Username") . ".</font>"; 
		$break = true; // database definition error
	}
	if ($_POST['mysql_password_post'] == "")
	{
		echo "<font color=red>" . __("You must declare a MySQL Password") . ".</font>"; 
		$break = true; // database definition error
	}
	$language_post = GetPOSTOrDefaultValue('language_post','en');

	// *************** Check Security settings ************************************************
	$use_https_post = GetPOSTOrDefaultValue('use_https_post','n');
	$iis_passwords_post = GetPOSTOrDefaultValue('iis_passwords_post','n');
	
	$username0 = GetPOSTOrDefaultValue('username0','');
	$password0 = GetPOSTOrDefaultValue('password0','');
	$username1 = GetPOSTOrDefaultValue('username1','');
	$password1 = GetPOSTOrDefaultValue('password1','');
	$username2 = GetPOSTOrDefaultValue('username2','');
	$password2 = GetPOSTOrDefaultValue('password2','');
	$username3 = GetPOSTOrDefaultValue('username3','');
	$password3 = GetPOSTOrDefaultValue('password3','');
	
	$use_ldap_integration_post = GetPOSTOrDefaultValue('use_ldap_integration_post','n');
	$ldap_base_dn_post = GetPOSTOrDefaultValue('ldap_base_dn_post','dc=mydomain,dc=local');
	$ldap_server_post = GetPOSTOrDefaultValue('ldap_server_post','myserver.mydomain.local');
	$ldap_user_post = GetPOSTOrDefaultValue('ldap_user_post','myusername@mydomain.local');
	$ldap_secret_post = GetPOSTOrDefaultValue('ldap_secret_post','');
	$full_details_post = GetPOSTOrDefaultValue('full_details_post','');
	$use_ldap_login_post = GetPOSTOrDefaultValue('use_ldap_login_post','');
	$human_readable_ldap_fields_post = GetPOSTOrDefaultValue('human_readable_ldap_fields_post','n');
	$image_link_ldap_attribute_post = GetPOSTOrDefaultValue('image_link_ldap_attribute_post','name');

	// *************** Check Homepage settings ************************************************
  $show_other_discovered_post = GetPOSTOrDefaultValue('show_other_discovered_post','n');
	$other_detected_post = GetPOSTOrDefaultValue('other_detected_post','1');
	$show_system_discovered_post = GetPOSTOrDefaultValue('show_system_discovered_post','n');
	$system_detected_post = GetPOSTOrDefaultValue('system_detected_post','1');
	$show_systems_not_audited_post = GetPOSTOrDefaultValue('show_systems_not_audited_post','n');
	$days_systems_not_audited_post = GetPOSTOrDefaultValue('days_systems_not_audited_post','7');
	$show_partition_usage_post = GetPOSTOrDefaultValue('show_partition_usage_post','n');
	$partition_free_space_post = GetPOSTOrDefaultValue('partition_free_space_post','500');
	$show_software_detected_post = GetPOSTOrDefaultValue('show_software_detected_post','n');
	$days_software_detected_post = GetPOSTOrDefaultValue('days_software_detected_post','1');
	$show_patches_not_detected_post = GetPOSTOrDefaultValue('show_patches_not_detected_post','n');
	$number_patches_not_detected_post = GetPOSTOrDefaultValue('number_patches_not_detected_post','5');
	$show_detected_servers_post = GetPOSTOrDefaultValue('show_detected_servers_post','n');

	$show_detected_xp_av = GetPOSTOrDefaultValue('show_detected_xp_av','n');
	$show_detected_rdp = GetPOSTOrDefaultValue('show_detected_rdp','n');
	$show_os_post = GetPOSTOrDefaultValue('show_os_post','n');
	$show_date_audited_post = GetPOSTOrDefaultValue('show_date_audited_post','n');
	$show_type_post = GetPOSTOrDefaultValue('show_type_post','n');
	$show_description_post = GetPOSTOrDefaultValue('show_description_post','n');
	$show_domain_post = GetPOSTOrDefaultValue('show_domain_post','n');
	$show_service_pack_post = GetPOSTOrDefaultValue('show_service_pack_post','n');
	$count_system_post = GetPOSTOrDefaultValue('count_system_post','');
	$vnc_type_post = GetPOSTOrDefaultValue('vnc_type_post','ultra');
	$decimalplaces_post = GetPOSTOrDefaultValue('decimalplaces_post','2');
	$management_domain_suffix_post = GetPOSTOrDefaultValue('management_domain_suffix_post','local');
	
	$show_ldap_changes_post = GetPOSTOrDefaultValue('show_ldap_changes_post','n');
	$ldap_changes_days_post = GetPOSTOrDefaultValue('ldap_changes_days_post','n');
	$show_systems_audited_graph_post = GetPOSTOrDefaultValue('show_systems_audited_graph_post','n');
	$systems_audited_days_post = GetPOSTOrDefaultValue('systems_audited_days_post','n');

	$admin_list_post = GetVarOrDefaultValue($admin_list, Array('Domain Admins')); 
	$user_list_post = GetVarOrDefaultValue($user_list, Array('Domain Users')); 
	
	if (!$break) // Check for error with database definition - continue if no error
	{
		/**********************************************************************************************************
			Create a new 'include_config.php' file based on the validated $_POST Settings
		**********************************************************************************************************/
	  $filename = 'include_config.php';
	  $content = "<?php\n";
		$content .= "// Ensures that all variables have a default value\n";
		$content .= "include_once \"include_config_defaults.php\";\n\n"; 

	  // ****************  General Settings *******************************************
	  $content .= "// ****************  General Settings *******************************************\n";
	  $content .= "\$language = '".$language_post."';\n";
		$content .= "\$mysql_server = '".$_POST['mysql_server_post']."';\n";
	  $content .= "\$mysql_user = '".$_POST['mysql_user_post']."';\n";
	  $content .= "\$mysql_password = '".$_POST['mysql_password_post']."';\n";
	  $content .= "\$mysql_database = '".$_POST['mysql_database_post']."';\n\n";

	  // ****************  Security Settings *******************************************
	  $content .= "// ****************  Security Settings *******************************************\n";
	  $content .= "\$use_https = '".$use_https_post."';\n";
	  $content .= "\$use_pass = '".$iis_passwords_post."';\n";
	  $content .= "// An array of allowed users and their passwords (set use_pass = \"n\" if you do not wish to use passwords)\n";
	  $content .= "\$users = array(\n";
	  if ($username0 != "") $content .= " '$username0' => '".(($password0 == "") ? $users[$username0] : md5($password0))."'";
	  if ($username1 != "") $content .= ",\n '$username1' => '".(($password1 == "") ? $users[$username1] : md5($password1))."'";
	  if ($username2 != "") $content .= ",\n '$username2' => '".(($password2 == "") ? $users[$username2] : md5($password2))."'";
	  if ($username3 != "") $content .= ",\n '$username3' => '".(($password3 == "") ? $users[$username3] : md5($password3))."'";
	  $content .= "\n);\n";
	  $content .= "\$use_ldap_integration= '".$use_ldap_integration_post. "';\n";
	  $content .= "\$ldap_base_dn= '".$ldap_base_dn_post."';\n";
	  $content .= "\$ldap_server = '".$ldap_server_post."';\n";
	  $content .= "\$ldap_user = '".$ldap_user_post."';\n";
	  $content .= "\$ldap_secret = '".$ldap_secret_post."';\n";
	  $content .= "\$use_ldap_login = '".$use_ldap_login_post."';\n";
	  $content .= "\$full_details = '".$full_details_post."';\n";
	  $content .= "\$human_readable_ldap_fields = '".$human_readable_ldap_fields_post."';\n";	// Added by Nick Brown	
	  $content .= "\$image_link_ldap_attribute = '".$image_link_ldap_attribute_post."';\n\n";	// Added by Nick Brown	

	  // ****************  Homepage Settings *******************************************
	  $content .= "// ****************  Homepage Settings *******************************************\n";
	  $content .= "\$show_other_discovered = '" . $show_other_discovered_post . "';\n";
	  $content .= "\$other_detected = '" . $other_detected_post . "';\n";
	  $content .= "\$show_system_discovered = '" . $show_system_discovered_post . "';\n";
	  $content .= "\$system_detected = '" . $system_detected_post . "';\n";
	  $content .= "\$show_systems_not_audited = '" . $show_systems_not_audited_post . "';\n";
	  $content .= "\$days_systems_not_audited = '" . $days_systems_not_audited_post . "';\n";
	  $content .= "\$show_partition_usage = '" . $show_partition_usage_post . "';\n";
	  $content .= "\$partition_free_space = '" . $partition_free_space_post . "';\n";
	  $content .= "\$show_software_detected = '" . $show_software_detected_post . "';\n";
	  $content .= "\$days_software_detected = '" . $days_software_detected_post . "';\n";
	  $content .= "\$show_patches_not_detected = '" . $show_patches_not_detected_post . "';\n";
	  $content .= "\$number_patches_not_detected = '" . $number_patches_not_detected_post . "';\n";
	  $content .= "\$show_detected_servers = '" . $show_detected_servers_post . "';\n";
	  $content .= "\$show_detected_xp_av = '" . $show_detected_xp_av . "';\n";
	  $content .= "\$show_detected_rdp = '" . $show_detected_rdp . "';\n";
	  $content .= "\$show_os = '" . $show_os_post . "';\n";
	  $content .= "\$show_date_audited = '" . $show_date_audited_post . "';\n";
	  $content .= "\$show_type = '" . $show_type_post . "';\n";
	  $content .= "\$show_description = '" . $show_description_post . "';\n";
	  $content .= "\$show_domain = '" . $show_domain_post . "';\n";
	  $content .= "\$show_service_pack = '" . $show_service_pack_post . "';\n";
	  $content .= "\$count_system = '" . $count_system_post . "';\n";
	  $content .= "\$vnc_type = '" . $vnc_type_post . "';\n";
	  $content .= "\$round_to_decimal_places = '" . $decimalplaces_post . "';\n";
	  $content .= "\$management_domain_suffix = '" . $management_domain_suffix_post . "';\n";
	  $content .= "\$show_systems_audited_graph = '".$show_systems_audited_graph_post."';\n";	// Added by Nick Brown	
	  $content .= "\$systems_audited_days = ".$systems_audited_days_post.";\n"; // Added by Nick Brown
	  $content .= "\$show_ldap_changes = '".$show_ldap_changes_post."';\n";	// Added by Nick Brown	
	  $content .= "\$ldap_changes_days = ".$ldap_changes_days_post.";\n\n";	// Added by Nick Brown	

	  // ****************  Settings that have no associated GUI *******************************************
	  $content .= "// ****************  Settings that have no associated GUI *******************************************\n";
		$admin_list_array = (count($admin_list_post) > 0) ? "'".implode("','",$admin_list_post)."'" : ""; // Added by Nick Brown
		$user_list_array = (count($user_list_post) > 0) ? "'".implode("','",$user_list_post)."'" : ""; // Added by Nick Brown
	  $content .= "\$admin_list = Array(".$admin_list_array.");\n"; // Added by Nick Brown
	  $content .= "\$user_list = Array(".$user_list_array.");\n"; // Added by Nick Brown

	  $content .= "?>";

		// Write $content to $filename
	  if (is_writable($filename)) {
	    if (!$handle = fopen($filename, 'w')) {
	      echo "Cannot open file ($filename)";
	      exit;
	    }
	    if (fwrite($handle, $content) === FALSE) {
	      echo "Cannot write to file ($filename)";
	      exit;
	    }
	    echo "<font color=blue>" . __("The Open-AudIT config has been updated") . ".</font>";
	    fclose($handle);
	  } else {
	    echo __("The file") . $filename . __("is not writable");
	  }
	}
}
// re include the config so the page displays the updated variables
include "include_config.php";

/**********************************************************************************************************
	Display HTML page content
**********************************************************************************************************/

?>
<div id="npb_popupmenu_div" onmouseover="ClearHideMenu(event);" onmouseout="DynamicHide(event);"></div>
<td class='CenterColumn'>
	<div class='npb_section_shadow'>
		<div class='npb_section_content'>
			<div class='npb_section_heading'>
<!-- Navigation Tab  -->
			<ul class='npb_tab_nav'>
					<li><a id='npb_config_general_tab' href='javascript://' onclick='SelectNavTab(this);'>General</a></li>
					<li><a id='npb_config_security_tab' href='javascript://' onclick='SelectNavTab(this);'>Security</a></li>
					<li><a id='npb_config_homepage_tab' href='javascript://' onclick='SelectNavTab(this);'>Homepage</a></li>
					<li><a id='npb_config_ldap_tab' href='javascript://' onclick='SelectNavTab(this);
					<?php	if (function_exists("ldap_search")) {echo "ListLdapConnections();";}?>'>LDAP</a></li>
				</ul>
			</div>
			
<?php

echo "<form method='post' action='" . $_SERVER["PHP_SELF"] . "' id='admin_config'>\n";

// ****************  Create DIV - General *******************************************
echo "<div id='npb_config_general_div' class='npb_section_data'>\n";
echo "<label>".__("Language").":</label><select size='1' name='language_post'>\n";

// Get available languages - under "lang" directory - and populate dropdown
$handle=opendir('./lang/');
while ($file = readdir ($handle))
{
	if ($file != "." && $file != "..")
	{
		if(substr($file,strlen($file)-4)==".inc")
		{
			if($language == substr($file,0,strlen($file)-4)) $selected="selected='selected'"; else $selected="";
      echo "<option $selected>".substr($file,0,strlen($file)-4)."</option>\n";
		}
	}
}
closedir($handle);

echo "</select><br />\n";
echo "<label>MySQL ".__("Server").":</label><input type='text' name='mysql_server_post' size='12' value='".$mysql_server."'/><br />\n";
echo "<label>MySQL ".__("User").":</label><input type='text' name='mysql_user_post' size='12' value='".$mysql_user."'/><br />\n";
echo "<label>MySQL ".__("Password").":</label><input type='password' name='mysql_password_post' size='12' value='".$mysql_password."'/><br />\n";
echo "<label>MySQL ".__("Database").":</label><input type='text' name='mysql_database_post' size='12' value='".$mysql_database."'/><br />\n";

echo "</div>\n";


// ****************  Create DIV - Security *******************************************
echo "<div id='npb_config_security_div' class='npb_section_data'>\n";
echo "<fieldset><legend>Authentication</legend>\n";
echo "<label>".__("Use https://").":</label>\n<input type='checkbox' name='use_https_post' value='y'".CheckedIfYes($use_https);
echo "<br />\n";

echo "<label>".__("Use Passwords").":</label>\n<input type='checkbox' name='iis_passwords_post' value='y'".CheckedIfYes($use_pass);
echo "<br />\n";

$count = 0;
while (list($key, $val) = each($users))
{
  echo "<label>".__("Username").":</label>\n";
  echo "<input type='text' name='username$count' size='12' value='$key'/><br />\n";
  echo "<label>".__("Password").":</label>\n";
  echo "<input type='password' name='password$count' size='12' value=''/><br />\n";
  $count = $count + 1;
}
echo "</fieldset>\n";

echo "<fieldset><legend>LDAP</legend>\n";
echo "<label>".__("Use LDAP Integration").":</label>\n<input type='checkbox' name='use_ldap_integration_post' value='y'".CheckedIfYes($use_ldap_integration);
echo "&nbsp;(to display user details)<br />\n";

echo "<label>".__("LDAP Base DN").":</label>\n<input type='text' name='ldap_base_dn_post' size='24' value=\"$ldap_base_dn\"/><br />\n";
echo "<label>".__("LDAP Connection Server").":</label>\n<input type='text' name='ldap_server_post' size='24' value=\"$ldap_server\"/><br />\n";
echo "<label>".__("LDAP Connection User").":</label>\n<input type='text' name='ldap_user_post' size='24' value=\"$ldap_user\"/><br />\n";
echo "<label>".__("LDAP Connection Secret").":</label>\n<input type='password' name='ldap_secret_post' size='24' value=\"$ldap_secret\"/><br />\n";

echo "<label>".__("Use LDAP for Login").":</label>\n<input type='checkbox' name='use_ldap_login_post' value='y'".CheckedIfYes($use_ldap_login);
echo "<br />\n";
echo "<label>".__("Show Full LDAP details").":</label>\n<input type='checkbox' name='full_details_post' value='y'".CheckedIfYes($full_details);
echo "<br />\n";
echo "<label>".__("Show Friendly Field Names").":</label>\n<input type='checkbox' name='human_readable_ldap_fields_post' value='y'".CheckedIfYes($human_readable_ldap_fields);
echo "<br />\n";
echo "<label>".__("Attribute for Image Linking").":</label>\n<input type='text' name='image_link_ldap_attribute_post' size='24' value=\"$image_link_ldap_attribute\"/>";
echo "<br />\n";

echo "</fieldset></div>\n";


// ****************  Create DIV - Homepage *******************************************
echo "<div id='npb_config_homepage_div' class='npb_section_data'>\n";

echo "<label>".__("Display 'Other Items Discovered in the last' on homepage").":</label>\n";
echo "<input type='checkbox' name='show_other_discovered_post' value='y'".CheckedIfYes($show_other_discovered);
echo "<div class=\"npb_config_col\">".__("Days").":<input type='text' name='other_detected_post' size='4' value='$other_detected'/></div><br />\n";

echo "<label>".__("Display 'Systems discovered in the last' on homepage").":</label>\n";
echo "<input type='checkbox' name='show_system_discovered_post'  value='y'".CheckedIfYes($show_system_discovered);
echo "<div class=\"npb_config_col\">".__("Days").":<input type='text' name='system_detected_post' size='4' value='$system_detected'/></div><br />\n";

echo "<label>".__("Display 'Systems Not Audited' on homepage").":</label>\n";
echo "<input type='checkbox' name='show_systems_not_audited_post' value='y'".CheckedIfYes($show_systems_not_audited);
echo "<div class=\"npb_config_col\">".__("Days").":<input type='text' name='days_systems_not_audited_post' size='4' value='$days_systems_not_audited'/></div><br />\n";

echo "<label>".__("Display 'Partition Usage' on homepage").":</label>\n<input type='checkbox' name='show_partition_usage_post' value='y'".CheckedIfYes($show_partition_usage);
echo "<div class=\"npb_config_col\">".__("MB").":<input type='text' name='partition_free_space_post' size='4' value='$partition_free_space'/></div><br />\n";

echo "<label>".__("Display 'New Software' on homepage").":</label>\n<input type='checkbox' name='show_software_detected_post' value='y'".CheckedIfYes($show_software_detected);
echo "<div class=\"npb_config_col\">".__("Days").":<input type='text' name='days_software_detected_post' size='4' value='$days_software_detected'/></div><br />\n";

echo "<label>".__("Display 'Missing Patches' on homepage").":</label>\n<input type='checkbox' name='show_patches_not_detected_post' value='y'".CheckedIfYes($show_patches_not_detected);
echo "<div class=\"npb_config_col\">".__("# of Patches").":<input type='text' name='number_patches_not_detected_post' size='4' value='$number_patches_not_detected'/></div><br />\n";

echo "<label>".__("Show Detected Servers on homepage").":</label>\n<input type='checkbox' name='show_detected_servers_post' value='y'".CheckedIfYes($show_detected_servers);
echo "<br />\n";

//Added Show Terminal Servers and RDP Machines AJH
echo "<label>".__("Show Terminal Servers and Remote Desktops on homepage").":</label>\n<input type='checkbox' name='show_detected_rdp' value='y'".CheckedIfYes($show_detected_rdp);
echo "<br />\n";

//Added Show XP  Missing AntiVirus AJH
echo "<label>".__("Show XP Missing AntiVirus on homepage").":</label>\n<input type='checkbox' name='show_detected_xp_av' value='y'".CheckedIfYes($show_detected_xp_av);
echo "<br />\n";
echo "<label>".__("Display 'OS' column in system list").":</label>\n<input type='checkbox' name='show_os_post' value='y'".CheckedIfYes($show_os);
echo "<br />\n";
echo "<label>".__("Display 'Date Audited' column in system list").":</label>\n<input type='checkbox' name='show_date_audited_post' value='y'".CheckedIfYes($show_date_audited);
echo "<br />\n";
echo "<label>".__("Display 'Type' column in system list").":</label>\n<input type='checkbox' name='show_type_post' value='y'".CheckedIfYes($show_type);
echo "<br />\n";
echo "<label>".__("Display 'Description' column in system list").":</label>\n<input type='checkbox' name='show_description_post' value='y'".CheckedIfYes($show_description);
echo "<br />\n";
echo "<label>".__("Display 'Domain' column in system list").":</label>\n<input type='checkbox' name='show_domain_post' value='y'".CheckedIfYes($show_domain);
echo "<br />\n";
echo "<label>".__("Display 'Service Pack' column in system list").":</label>\n<input type='checkbox' name='show_service_pack_post' value='y'".CheckedIfYes($show_service_pack);
echo "<br />\n";
echo "<label>".__("Number of Systems to display").":</label>\n<input type='text' name='count_system_post' size='12' value='$count_system'/><br />\n";
echo "<label>".__("VNC Type 'real' or 'ultra' ").":</label>\n<input type='text' name='vnc_type_post' size='12' value='$vnc_type'/><br />\n";
echo "<label>".__("Number of decimal places to display").":</label>\n<input type='text' name='decimalplaces_post' size='12' value='$round_to_decimal_places'/><br />\n";
echo "<label>".__("FQDN Domain Suffix for Management Utilities").":</label>\n<input type='text' name='management_domain_suffix_post' size='24' value='$management_domain_suffix'/><br />\n";

echo "<label>".__("Display 'LDAP Directory changes' on homepage").":</label>\n";
echo "<input type='checkbox' name='show_ldap_changes_post' value='y'".CheckedIfYes($show_ldap_changes);

echo "<div class=\"npb_config_col\">".__("Days").":<input type='text' name='ldap_changes_days_post' size='4' value='$ldap_changes_days'/></div><br />\n";

echo "<label>".__("Display 'Systems Audited' graph on homepage").":</label>\n";
echo "<input type='checkbox' name='show_systems_audited_graph_post' value='y'".CheckedIfYes($show_systems_audited_graph);
echo "<div class=\"npb_config_col\">".__("Days").":<input type='text' name='systems_audited_days_post' size='4' value='$systems_audited_days'/></div><br />\n";

echo "</div>\n";
echo "<div id='npb_config_save_div'>\n<input type='submit' value='".__("Save")."' name='submit_button'/>\n</div>\n";
echo "</form>\n";

/**********************************************************************************************************
Function Name:
	CheckedIfYes
Description:
	For checkbox control - Checks supplied variable - if it is defined and 'y' then returns HTML string to display checkbox 
	as checked
Arguments:
	&$var	[IN] 	variable to check
Returns:	[String]	HTML string
Change Log:
	20/08/2008			New function	[Nick Brown]
**********************************************************************************************************/
function CheckedIfYes(&$var)
{if (isset($var) && $var == "y" ) return " checked='checked'/>"; else return "/>\n";}

?>

<!--   DIV - LDAP -->

			<div id='npb_config_ldap_div' class='npb_section_data'>
				<div id='npb_ldap_connections_div'>
<?php	
if (function_exists("ldap_search")) {echo "</div><button onclick=\"NewLdapConnection()\">New Connection</button>";}
else {echo "<p>You do not have the LDAP extension enabled in your PHP configuration. Please refer to your PHP documentation.<p></div>";}  
?>				
				<!-- LDAP Connection Config -->
				<div id='npb_ldap_connection_config_div'>
					<fieldset><legend>LDAP Connection Details</legend>
						<input type="hidden" id="ldap_connection_id" />
						<label for="ldap_connection_server">LDAP Server Name:</label>
						<input type='text' id='ldap_connection_server' size='50'/><br />
						<label for="ldap_connection_user">LDAP User Name:</label>
						<input type='text' id='ldap_connection_user' size='20'/><br />
						<label for="ldap_connection_password">LDAP Password:</label>
						<input type='password' id='ldap_connection_password' size='20'/><br />
						<!--<label for="ldap_connection_use_ssl">Use secure connection:</label>
						<input type='checkbox' id='ldap_connection_use_ssl'/>&nbsp;&nbsp;(requires independent configuration of OpenLDAP/OpenSSL)<br />-->
						<button type="button" onclick="TestLdapConnection();">Test Connection</button>
						<button type="button" onclick="SaveLdapConnection();">Save</button>
						<button type="button" onclick="document.getElementById('npb_ldap_connection_config_div').style.display = 'none';">Cancel</button>
					</fieldset>	
					<fieldset><legend>Connection Results</legend><p id="ldap_connection_results"></p></fieldset>
				</div>

				<!-- LDAP Path Config -->
				<div id='npb_ldap_path_config_div'>
					<fieldset><legend>LDAP Path</legend>
						<input type="hidden" id="ldap_path_connection_id"/>
						<input type="hidden" id="ldap_path_id"/>
						<label for="ldap_path_dn">LDAP Path:</label>
						<input type="text" id='ldap_path_dn' size='50'/><br />
						<label for="ldap_path_audit">Include in audit:</label>
						<input type="checkbox" id='ldap_path_audit'/><br />
						<button type="button" onclick="SaveLdapPath();">Save</button>
						<button type="button" onclick="document.getElementById('npb_ldap_path_config_div').style.display = 'none';">Cancel</button>
					</fieldset>	
				</div>
				
			</div>
		
		</div>
	</div>
</td>

<?php
include "include_right_column.php";
?>
</body>
</html>

