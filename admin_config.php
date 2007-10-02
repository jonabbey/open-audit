<?php
$page = "admin";
include "include.php";
$break = "";
echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">\n";
echo "<p class=\"contenthead\">" . __("Setup Open-AudIT") . "</p>";

if(isset($_POST['submit_button'])) {
if (isset($_POST['language_post'])) {$language_post = $_POST['language_post'];} else { $language_post = "en";}
if ($_POST['mysql_server_post'] == "") {echo "<font color=red>" . __("You must declare a MySQL Server") . ".</font>"; $break = "1";} else {}
if ($_POST['mysql_database_post'] == "") {echo "<font color=red>" . __("You must declare a MySQL Database") . ".</font>"; $break = "1";} else {}
if ($_POST['mysql_user_post'] == "") {echo "<font color=red>" . __("You must declare a MySQL Username") . ".</font>"; $break = "1";} else {}
if (isset($_POST['mysql_password_post'])) {$mysql_password_post = $_POST['mysql_password_post'];} else { $mysql_password_post = "";}
if (isset($_POST['use_https_post']))  {$use_https_post = $_POST['use_https_post'];}  else { $use_https_post = "n";}
if (isset($_POST['iis_passwords_post']))  {$iis_passwords_post = $_POST['iis_passwords_post'];}  else { $iis_passwords_post = "n";}
if (isset($_POST['username0'])) {$username0 = $_POST['username0'];} else { $username0 = "";}
if (isset($_POST['password0'])) {$password0 = $_POST['password0'];} else { $password0 = "";}
if (isset($_POST['username1'])) {$username1 = $_POST['username1'];} else { $username1 = "";}
if (isset($_POST['password1'])) {$password1 = $_POST['password1'];} else { $password1 = "";}
if (isset($_POST['username2'])) {$username2 = $_POST['username2'];} else { $username2 = "";}
if (isset($_POST['password2'])) {$password2 = $_POST['password2'];} else { $password2 = "";}
if (isset($_POST['username3'])) {$username3 = $_POST['username3'];} else { $username3 = "";}
if (isset($_POST['password3'])) {$password3 = $_POST['password3'];} else { $password3 = "";}
if (isset($_POST['show_other_discovered_post'])) {$show_other_discovered_post = $_POST['show_other_discovered_post'];} else { $show_other_discovered_post = "n";}
if (isset($_POST['other_detected_post'])) {$other_detected_post = $_POST['other_detected_post'];} else { $other_detected_post = "1";}
if (isset($_POST['show_system_discovered_post'])) {$show_system_discovered_post = $_POST['show_system_discovered_post'];} else { $show_system_discovered_post = "n";}
if (isset($_POST['system_detected_post'])) {$system_detected_post = $_POST['system_detected_post'];} else { $system_detected_post = "1";}
if (isset($_POST['show_systems_not_audited_post'])) {$show_systems_not_audited_post = $_POST['show_systems_not_audited_post'];} else { $show_systems_not_audited_post = "n";}
if (isset($_POST['days_systems_not_audited_post'])) {$days_systems_not_audited_post = $_POST['days_systems_not_audited_post'];} else { $days_systems_not_audited_post = "1";}
if (isset($_POST['show_partition_usage_post'])) {$show_partition_usage_post = $_POST['show_partition_usage_post'];} else { $show_partition_usage_post = "n";}
if (isset($_POST['partition_free_space_post'])) {$partition_free_space_post = $_POST['partition_free_space_post'];} else { $partition_free_space_post = "95";}
if (isset($_POST['show_software_detected_post'])) {$show_software_detected_post = $_POST['show_software_detected_post'];} else { $show_software_detected_post = "n";}
if (isset($_POST['days_software_detected_post'])) {$days_software_detected_post = $_POST['days_software_detected_post'];} else { $days_software_detected_post = "1";}
if (isset($_POST['show_patches_not_detected_post'])) {$show_patches_not_detected_post = $_POST['show_patches_not_detected_post'];} else { $show_patches_not_detected_post = "n";}
if (isset($_POST['number_patches_not_detected_post'])) {$number_patches_not_detected_post = $_POST['number_patches_not_detected_post'];} else { $number_patches_not_detected_post = "5";}
if (isset($_POST['show_detected_servers_post'])) {$show_detected_servers_post = $_POST['show_detected_servers_post'];} else { $show_detected_servers_post = "n";}
// Added Show missing xp av AJH
if (isset($_POST['show_detected_xp_av'])) {$show_detected_xp_av = $_POST['show_detected_xp_av'];} else { $show_detected_xp_av = "n";}
//
// Added Show Terminal Servers and Remote Desktops AJH
if (isset($_POST['show_detected_rdp'])) {$show_detected_rdp = $_POST['show_detected_rdp'];} else { $show_detected_rdp = "n";}
//
if (isset($_POST['show_os_post']))           {$show_os_post = $_POST['show_os_post'];}                     else { $show_os_post = "n";}
if (isset($_POST['show_date_audited_post'])) {$show_date_audited_post = $_POST['show_date_audited_post'];} else { $show_date_audited_post = "n";}
if (isset($_POST['show_type_post']))         {$show_type_post = $_POST['show_type_post'];}                 else { $show_type_post = "n";}
if (isset($_POST['show_description_post']))  {$show_description_post = $_POST['show_description_post'];}   else { $show_description_post = "n";}
if (isset($_POST['show_domain_post']))  {$show_domain_post = $_POST['show_domain_post'];}   else { $show_domain_post = "n";}
if (isset($_POST['show_service_pack_post']))  {$show_service_pack_post = $_POST['show_service_pack_post'];}   else { $show_service_pack_post = "n";}
if (isset($_POST['count_system_post'])) {$count_system_post = $_POST['count_system_post'];} else { $count_system_post = "";}
if (isset($_POST['vnc_type_post'])) {$vnc_type_post = $_POST['vnc_type_post'];} else { $vnc_type_post = "ultra";}
if (isset($_POST['decimalplaces_post'])) {$decimalplaces_post = $_POST['decimalplaces_post'];} else { $decimalplaces_post = "2";}

// Added to preset domain suffix for management vbs scripts AJH
if (isset($_POST['management_domain_suffix_post'])) {$management_domain_suffix_post = $_POST['management_domain_suffix_post'];} else { $management_domain_suffix_post = "local";}

// Added for ldap integration AJH
if (isset($_POST['use_ldap_integration_post'])) {$use_ldap_integration_post = $_POST['use_ldap_integration_post'];} else { $use_ldap_integration_post = "n";}
//
if (isset($_POST['ldap_base_dn_post'])) {$ldap_base_dn_post = $_POST['ldap_base_dn_post'];} else { $ldap_base_dn_post = "dc=mydomain,dc=local";}
if (isset($_POST['ldap_server_post'])) {$ldap_server_post = $_POST['ldap_server_post'];} else { $ldap_server_post = "myserver.mydomain.local";}
if (isset($_POST['ldap_user_post'])) {$ldap_user_post = $_POST['ldap_user_post'];} else { $ldap_user_post = "myusername@mydomain.local";}
if (isset($_POST['ldap_secret_post'])) {$ldap_secret_post = $_POST['ldap_secret_post'];} else { $ldap_secret_post = "";}
if (isset($_POST['full_details_post'])) {$full_details_post = $_POST['full_details_post'];} else { $full_details_post = "";}
if (isset($_POST['use_ldap_login_post'])) {$use_ldap_login_post = $_POST['use_ldap_login_post'];} else { $use_ldap_login_post = "";}


if (isset($_POST['col_post'])) {$col_post = $_POST['col_post'];} else { $col_post = "blue";}
if (isset($_POST['pic_style_post'])) {$pic_style_post = $_POST['pic_style_post'];} else { $pic_style_post = "_win";}

  if ($break == "1") {} else {
  $filename = 'include_config.php';
  $content = "<";
  $content .= "?";
  $content .= "php\n";
  $content .= "\$mysql_server = '" . $_POST['mysql_server_post'] . "';\n";
  $content .= "\$mysql_database = '" . $_POST['mysql_database_post'] . "';\n";
  $content .= "\$mysql_user = '" . $_POST['mysql_user_post'] . "';\n";
  $content .= "\$mysql_password = '" . $mysql_password_post . "';\n";
  $content .= "\n";
  $content .= "\$use_https = '" . $use_https_post . "';\n";
  $content .= "// An array of allowed users and their passwords\n";
  $content .= "// Make sure to set use_pass = \"n\" if you do not wish to use passwords\n";
  $content .= "\$use_pass = '" . $iis_passwords_post . "';\n";
  $content .= "\$users = array(\n";
  if ($username0 == "") {} else { if ($password0 == "") {$thepassword = $users[$username0];} else {$thepassword = md5($password0);} $content .= " '$username0' => '$thepassword'";}
  if ($username1 == "") {} else { if ($password1 == "") {$thepassword = $users[$username1];} else {$thepassword = md5($password1);} $content .= " ,\n'$username1' => '$thepassword'";}
  if ($username2 == "") {} else { if ($password2 == "") {$thepassword = $users[$username2];} else {$thepassword = md5($password2);} $content .= " ,\n'$username2' => '$thepassword'";}
  if ($username3 == "") {} else { if ($password3 == "") {$thepassword = $users[$username3];} else {$thepassword = md5($password3);} $content .= " ,\n'$username3' => '$thepassword'";}
  $content .= "\n);\n";
  $content .= "\n";
  $content .= "\n";
  $content .= "// Config options for index.php\n";
  $content .= "\$show_other_discovered = '" . $show_other_discovered_post . "';\n";
  $content .= "\$other_detected = '" . $other_detected_post . "';\n";
  $content .= "\n";
  $content .= "\$show_system_discovered = '" . $show_system_discovered_post . "';\n";
  $content .= "\$system_detected = '" . $system_detected_post . "';\n";
  $content .= "\n";
  $content .= "\$show_systems_not_audited = '" . $show_systems_not_audited_post . "';\n";
  $content .= "\$days_systems_not_audited = '" . $days_systems_not_audited_post . "';\n";
  $content .= "\n";
  $content .= "\$show_partition_usage = '" . $show_partition_usage_post . "';\n";
  $content .= "\$partition_free_space = '" . $partition_free_space_post . "';\n";
  $content .= "\n";
  $content .= "\$show_software_detected = '" . $show_software_detected_post . "';\n";
  $content .= "\$days_software_detected = '" . $days_software_detected_post . "';\n";
  $content .= "\n";
  $content .= "\$show_patches_not_detected = '" . $show_patches_not_detected_post . "';\n";
  $content .= "\$number_patches_not_detected = '" . $number_patches_not_detected_post . "';\n";
  $content .= "\n";
  $content .= "\$show_detected_servers = '" . $show_detected_servers_post . "';\n";
  // Added show_detected_xp_av AJH
  $content .= "\$show_detected_xp_av = '" . $show_detected_xp_av . "';\n";
  //
    // Added show_detected_rdp AJH
  $content .= "\$show_detected_rdp = '" . $show_detected_rdp . "';\n";
  //
  $content .= "\n";
  $content .= "\$show_os = '" . $show_os_post . "';\n";
  $content .= "\$show_date_audited = '" . $show_date_audited_post . "';\n";
  $content .= "\$show_type = '" . $show_type_post . "';\n";
  $content .= "\$show_description = '" . $show_description_post . "';\n";
  $content .= "\$show_domain = '" . $show_domain_post . "';\n";
  $content .= "\$show_service_pack = '" . $show_service_pack_post . "';\n";
  $content .= "\n";
  $content .= "\$count_system = '" . $count_system_post . "';\n";
  $content .= "\n";
  $content .= "\$vnc_type = '" . $vnc_type_post . "';\n";
  $content .= "\n";
  $content .= "\$round_to_decimal_places = '" . $decimalplaces_post . "';\n";
  $content .= "\n";
  
  $content .= "\$management_domain_suffix = '" . $management_domain_suffix_post . "';\n";
  $content .= "\n";
  
  $content .= "\$use_ldap_integration= '" . $use_ldap_integration_post. "';\n";
  $content .= "\n";

  
  $content .= "\$ldap_base_dn= '" . $ldap_base_dn_post. "';\n";
  $content .= "\n";

  $content .= "\$ldap_server = '" . $ldap_server_post . "';\n";
  $content .= "\n";

  $content .= "\$ldap_user = '" . $ldap_user_post . "';\n";
  $content .= "\n";
 /*  
  if ($ldap_secret_post == "") {$thepassword = $ldap_secret_post;} else {$thepassword = md5($ldap_secret_post);}
  
  $content .= "\$ldap_secret = '" . $thepassword. "';\n";
  $content .= "\n";
*/  
  
  $content .= "\$ldap_secret = '" . $ldap_secret_post. "';\n";
  $content .= "\n";
  
  $content .= "\$full_details = '" . $full_details_post. "';\n";
  $content .= "\n";
  
    $content .= "\$use_ldap_login = '" . $use_ldap_login_post. "';\n";
  $content .= "\n";
  
  $content .= "\$language = '" . $language_post . "';\n";
  $content .= "\n";
  $content .= "?";
  $content .= ">";


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

echo "<form method=\"post\" action=\"" . $_SERVER["PHP_SELF"] . "\" name=\"admin_config\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"content\">";
echo "<tr><td colspan=\"5\"><hr /></td></tr>";
echo "<tr>\n";
echo "<td>".__("Language").":</td>\n";
echo "<td><select size=\"1\" name=\"language_post\" class=\"for_forms\">\n";

$handle=opendir('./lang/');
while ($file = readdir ($handle)) {
    if ($file != "." && $file != "..") {
        if(substr($file,strlen($file)-4)==".inc"){
            if($language == substr($file,0,strlen($file)-4) ) $selected="selected"; else $selected="";
            echo "<option $selected>".substr($file,0,strlen($file)-4)."</option>\n";
        }
    }
}
closedir($handle);

echo "    </select></td>\n";
echo "</tr>\n";
echo "<tr><td>MySQL ".__("Server").":&nbsp;</td><td><input type=\"text\" name=\"mysql_server_post\" size=\"12\" value=\"" . $mysql_server . "\" class=\"for_forms\"/></td></tr>\n";
echo "<tr><td>MySQL ".__("User").":&nbsp;</td><td><input type=\"text\" name=\"mysql_user_post\" size=\"12\" value=\"" . $mysql_user . "\" class=\"for_forms\" /></td></tr>\n";
echo "<tr><td>MySQL ".__("Password").":&nbsp;</td><td><input type=\"password\" name=\"mysql_password_post\" size=\"12\" value=\"" . $mysql_password . "\" class=\"for_forms\" /></td></tr>\n";
echo "<tr><td>MySQL ".__("Database").":&nbsp;</td><td><input type=\"text\" name=\"mysql_database_post\" size=\"12\" value=\"" . $mysql_database . "\" class=\"for_forms\" /></td></tr>\n";
echo "<tr><td colspan=\"5\"><hr /></td></tr>";

echo "<tr><td>" . __("Use https://") . ":&nbsp;</td><td><input type=\"checkbox\" name=\"use_https_post\" class=\"for_forms\" value=\"y\""; if (isset($use_https) AND $use_https == "y"){ echo "checked=\"checked\"";}; echo "\" /></td></tr>";

echo "<tr><td>" . __("Use Passwords") . ":&nbsp;</td><td><input type=\"checkbox\" name=\"iis_passwords_post\" class=\"for_forms\" value=\"y\""; if (isset($use_pass) AND $use_pass == "y"){ echo "checked=\"checked\"";}; echo "\" /></td></tr>";
  $count = 0;
  while (list($key, $val) = each($users)) {
  echo "<tr><td></td><td>".__("Username").": </td>";
  echo "<td><input type=\"text\" name=\"username$count\" size=\"12\" value=\"$key\" class=\"for_forms\" /></td>\n";
  echo "<td>".__("Password").": </td>";
  echo "<td><input type=\"password\" name=\"password$count\" size=\"12\" value=\"\" class=\"for_forms\" /></td></tr>\n";
  $count = $count + 1;}

//".__("")."
echo "<tr><td colspan=\"5\"><hr /></td></tr>";
echo "<tr><td>".__("Display 'Other Items Discovered in the last' on homepage").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_other_discovered_post\"  value=\"y\"";
  if (isset($show_other_discovered) AND $show_other_discovered == "y"){ echo "checked=\"checked\"";}
echo "/></td>";
echo "<td>".__("Days").":&nbsp;</td><td><input type=\"text\" name=\"other_detected_post\" size=\"4\" value=\"$other_detected\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>".__("Display 'Systems discovered in the last' on homepage").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_system_discovered_post\"  value=\"y\"";
  if (isset($show_system_discovered) AND $show_system_discovered == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
echo "<td>".__("Days").":&nbsp;</td><td><input type=\"text\" name=\"system_detected_post\" size=\"4\" value=\"$system_detected\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>".__("Display 'Systems Not Audited' on homepage").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_systems_not_audited_post\"  value=\"y\"";
  if (isset($show_systems_not_audited) AND $show_systems_not_audited == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
echo "<td>".__("Days").":&nbsp;</td><td><input type=\"text\" name=\"days_systems_not_audited_post\" size=\"4\" value=\"$days_systems_not_audited\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>".__("Display 'Partition Usage' on homepage").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_partition_usage_post\"   value=\"y\"";
  if (isset($show_partition_usage) AND $show_partition_usage == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
echo "<td>".__("MB").":&nbsp;</td><td><input type=\"text\" name=\"partition_free_space_post\" size=\"4\" value=\"$partition_free_space\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>".__("Display 'New Software' on homepage").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_software_detected_post\" value=\"y\"";
  if (isset($show_software_detected) AND $show_software_detected == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
echo "<td>".__("Days").":&nbsp;</td><td><input type=\"text\" name=\"days_software_detected_post\" size=\"4\" value=\"$days_software_detected\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>".__("Display 'Missing Patches' on homepage").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_patches_not_detected_post\" value=\"y\"";
  if (isset($show_patches_not_detected) AND $show_patches_not_detected == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
echo "<td>".__("# of Patches").":&nbsp;</td><td><input type=\"text\" name=\"number_patches_not_detected_post\" size=\"4\" value=\"$number_patches_not_detected\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>".__("Show Detected Servers on homepage").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_detected_servers_post\" value=\"y\"";
  if (isset($show_detected_servers) AND $show_detected_servers == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
    //Added Show Terminal Servers and RDP Machines AJH
echo "<tr><td>".__("Show Terminal Servers and Remote Desktops on homepage").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_detected_rdp\" value=\"y\"";
  if (isset($show_detected_rdp) AND $show_detected_rdp == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
  //
  //Added Show XP  Missing AntiVirus AJH
echo "<tr><td>".__("Show XP Missing AntiVirus on homepage").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_detected_xp_av\" value=\"y\"";
  if (isset($show_detected_xp_av) AND $show_detected_xp_av == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
  //
echo "<td><td>";
echo "<tr><td colspan=\"5\"><hr /></td></tr>";
echo "<tr><td>".__("Display 'OS' column in system list").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_os_post\" value=\"y\"";
  if (isset($show_os) AND $show_os == "y"){ echo "checked=\"checked\"";}
  echo "/></td>\n";
echo "<tr><td>".__("Display 'Date Audited' column in system list").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_date_audited_post\"  value=\"y\"";
  if (isset($show_date_audited) AND $show_date_audited == "y"){ echo "checked=\"checked\"";}
  echo "/></td>\n";
echo "<tr><td>".__("Display 'Type' column in system list").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_type_post\" value=\"y\"";
  if (isset($show_type) AND $show_type == "y"){ echo "checked=\"checked\"";}
  echo "/></td>\n";
echo "<tr><td>".__("Display 'Description' column in system list").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_description_post\" value=\"y\"";
  if (isset($show_description) AND $show_description == "y"){ echo "checked=\"checked\"";}
  echo "/></td>\n";
echo "<tr><td>".__("Display 'Domain' column in system list").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_domain_post\" value=\"y\"";
  if (isset($show_domain) AND $show_domain == "y"){ echo "checked=\"checked\"";}
  echo "/></td>\n";
echo "<tr><td>".__("Display 'Service Pack' column in system list").":&nbsp;</td><td><input type=\"checkbox\" name=\"show_service_pack_post\" value=\"y\"";
  if (isset($show_service_pack) AND $show_service_pack == "y"){ echo "checked=\"checked\"";}
  echo "/></td>\n";
echo "<tr><td>".__("Number of Systems to display").":&nbsp;</td><td><input type=\"text\" name=\"count_system_post\" size=\"12\" value=\"$count_system\" class=\"for_forms\" /></td></tr>";

echo "<tr><td>".__("VNC Type 'real' or 'ultra' ").":&nbsp;</td><td><input type=\"text\" name=\"vnc_type_post\" size=\"12\" value=\"$vnc_type\" class=\"for_forms\" /></td></tr>";

echo "<tr><td>".__("Number of decimal places to display").":&nbsp;</td><td><input type=\"text\" name=\"decimalplaces_post\" size=\"12\" value=\"$round_to_decimal_places\" class=\"for_forms\" /></td></tr>";
echo "<tr><td colspan=\"5\"><hr /></td></tr>\n";

echo "<tr><td>".__("FQDN Domain Suffix for Management Utilities").":&nbsp;</td><td><input type=\"text\" name=\"management_domain_suffix_post\" size=\"10\" value=\"$management_domain_suffix\" class=\"for_forms\" /></td></tr>";
echo "<tr><td colspan=\"5\"><hr /></td></tr>\n";

if (function_exists('ldap_connect')){
} else {
        echo "<tr bgcolor=\"" . $bgcolor . "\"><td><b>".__("LDAP connectivity is not available, please check php.ini ")."</b></td></tr>";
}


echo "<tr><td>".__("Use LDAP Integration to display user details").":&nbsp;</td><td><input type=\"checkbox\" name=\"use_ldap_integration_post\" value=\"y\"";
if (isset($use_ldap_integration) AND $use_ldap_integration == "y"){ echo "checked=\"checked\"";}



  echo "/></td>\n";
echo "<tr><td>".__("LDAP Base DN").":&nbsp;</td><td><input type=\"text\" name=\"ldap_base_dn_post\" size=\"24\" value=\"$ldap_base_dn\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>".__("LDAP Connection Server").":&nbsp;</td><td><input type=\"text\" name=\"ldap_server_post\" size=\"24\" value=\"$ldap_server\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>".__("LDAP Connection User").":&nbsp;</td><td><input type=\"text\" name=\"ldap_user_post\" size=\"24\" value=\"$ldap_user\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>".__("LDAP Connection Secret").":&nbsp;</td><td><input type=\"password\" name=\"ldap_secret_post\" size=\"24\" value=\"$ldap_secret\" class=\"for_forms\" /></td></tr>";

echo "<tr><td>".__("Use LDAP for Open Audit Login").":&nbsp;</td><td><input type=\"checkbox\" name=\"use_ldap_login_post\" value=\"y\"";
if (isset($use_ldap_login) AND $use_ldap_login == "y"){ echo "checked=\"checked\"";}


echo "<tr><td>".__("Show Full LDAP details").":&nbsp;</td><td><input type=\"checkbox\" name=\"full_details_post\" value=\"y\"";
if (isset($full_details) AND $full_details == "y"){ echo "checked=\"checked\"";}


echo "<tr><td><input type=\"submit\" value=\"".__("Save")."\" name=\"submit_button\" /></td></tr>\n";
echo "</table>\n";
echo "</form>\n";
echo "</div>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
?>
