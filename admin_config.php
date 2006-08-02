<?php 
$page = "admin";
include "include.php"; 
$break = "";
echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">\n";
echo "<p class=\"contenthead\">" . $l_swi . "</p>";

if(isset($_POST['submit_button'])) {
if (isset($_POST['language_post'])) {$language_post = $_POST['language_post'];} else { $language_post = "english";}
if ($_POST['mysql_server_post'] == "") {echo "<font color=red>" . $l_yms . ".</font>"; $break = "1";} else {}
if ($_POST['mysql_database_post'] == "") {echo "<font color=red>" . $l_ydb . ".</font>"; $break = "1";} else {}
if ($_POST['mysql_user_post'] == "") {echo "<font color=red>" . $l_ymu . ".</font>"; $break = "1";} else {}
if (isset($_POST['mysql_password_post'])) {$mysql_password_post = $_POST['mysql_password_post'];} else { $mysql_password_post = "";}
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
if (isset($_POST['show_os_post']))           {$show_os_post = $_POST['show_os_post'];}                     else { $show_os_post = "n";}
if (isset($_POST['show_date_audited_post'])) {$show_date_audited_post = $_POST['show_date_audited_post'];} else { $show_date_audited_post = "n";}
if (isset($_POST['show_type_post']))         {$show_type_post = $_POST['show_type_post'];}                 else { $show_type_post = "n";}
if (isset($_POST['show_description_post']))  {$show_description_post = $_POST['show_description_post'];}   else { $show_description_post = "n";}
if (isset($_POST['show_domain_post']))  {$show_domain_post = $_POST['show_domain_post'];}   else { $show_domain_post = "n";}
if (isset($_POST['show_service_pack_post']))  {$show_service_pack_post = $_POST['show_service_pack_post'];}   else { $show_service_pack_post = "n";}
if (isset($_POST['count_system_post'])) {$count_system_post = $_POST['count_system_post'];} else { $count_system_post = "";}
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
  $content .= "// An array of allowed users and their passwords\n";
  $content .= "// Make sure to set use_pass = \"n\" if you do not wish to use passwords\n";
  $content .= "\$use_pass = '" . $iis_passwords_post . "';\n";
  $content .= "\$users = array(\n";
  if ($username0 == "") {} else { $content .= "  '$username0' => '$password0'"; }
  if ($username1 == "") {} else { $content .= " ,\n  '$username1' => '$password1'"; }
  if ($username2 == "") {} else { $content .= " ,\n  '$username2' => '$password2'"; }
  if ($username3 == "") {} else { $content .= " ,\n  '$username3' => '$password3'"; }
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
    echo "<font color=blue>" . $l_twi . ".</font>";
    fclose($handle);
  } else {
    echo $l_tfi . $filename . $l_inw;
  }
  }
} 

// re include the config so the page displays the updated variables
include "include_config.php";
	
echo "<form method=\"post\" action=\"" . $_SERVER["PHP_SELF"] . "\" name=\"admin_config\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"content\">";
echo "<tr><td colspan=\"5\"><hr /></td></tr>";
echo "<tr>\n";
echo "<td>$l_lan ?</td>\n";
echo "<td><select size=\"1\" name=\"language_post\" class=\"for_forms\">\n";
echo "                <option value=\"english\" ";
     if ($language == "english"){ echo "selected";}
echo " >English</option>\n";
echo "                <option value=\"italian\" ";
     if ($language == "italian"){ echo "selected";}
echo " >Italian</option>\n";
echo "    </select></td>\n";
echo "</tr>\n";
echo "<tr><td>" . $l_mss . ":&nbsp;</td><td><input type=\"text\" name=\"mysql_server_post\" size=\"12\" value=\"" . $mysql_server . "\" class=\"for_forms\"/></td></tr>\n";
echo "<tr><td>" . $l_msu . ":&nbsp;</td><td><input type=\"text\" name=\"mysql_user_post\" size=\"12\" value=\"" . $mysql_user . "\" class=\"for_forms\" /></td></tr>\n";
echo "<tr><td>" . $l_msp . ":&nbsp;</td><td><input type=\"password\" name=\"mysql_password_post\" size=\"12\" value=\"" . $mysql_password . "\" class=\"for_forms\" /></td></tr>\n";
echo "<tr><td>" . $l_msd . ":&nbsp;</td><td><input type=\"text\" name=\"mysql_database_post\" size=\"12\" value=\"" . $mysql_database . "\" class=\"for_forms\" /></td></tr>\n";
echo "<tr><td colspan=\"5\"><hr /></td></tr>";
echo "<tr><td>" . $l_upi . ":&nbsp;</td><td><input type=\"checkbox\" name=\"iis_passwords_post\" class=\"for_forms\" value=\"y\""; if ($use_pass == "y"){ echo "checked=\"checked\"";}; echo "\" /></td></tr>";
  $count = 0; 
  while (list($key, $val) = each($users)) { 
  echo "<tr><td></td><td>Username: </td>";
  echo "<td><input type=\"text\" name=\"username$count\" size=\"12\" value=\"$key\" class=\"for_forms\" /></td>\n";
  echo "<td>Password: </td>";
  echo "<td><input type=\"password\" name=\"password$count\" size=\"12\" value=\"$val\" class=\"for_forms\" /></td></tr>\n"; 
  $count = $count + 1;}
echo "<tr><td colspan=\"5\"><hr /></td></tr>";
echo "<tr><td>" . $l_dod . ":&nbsp;</td><td><input type=\"checkbox\" name=\"show_other_discovered_post\"  value=\"y\"";
  if ($show_other_discovered == "y"){ echo "checked=\"checked\"";} 
echo "/></td>";
echo "<td>" . $l_day . ":&nbsp;</td><td><input type=\"text\" name=\"other_detected_post\" size=\"4\" value=\"$other_detected\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>$l_dsd:&nbsp;</td><td><input type=\"checkbox\" name=\"show_system_discovered_post\"  value=\"y\"";
  if ($show_system_discovered == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
echo "<td>$l_day:&nbsp;</td><td><input type=\"text\" name=\"system_detected_post\" size=\"4\" value=\"$system_detected\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>$l_dns:&nbsp;</td><td><input type=\"checkbox\" name=\"show_systems_not_audited_post\"  value=\"y\"";
  if ($show_systems_not_audited == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
echo "<td>$l_day:&nbsp;</td><td><input type=\"text\" name=\"days_systems_not_audited_post\" size=\"4\" value=\"$days_systems_not_audited\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>$l_dpu:&nbsp;</td><td><input type=\"checkbox\" name=\"show_partition_usage_post\"   value=\"y\"";
  if ($show_partition_usage == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
echo "<td>$l_mby:&nbsp;</td><td><input type=\"text\" name=\"partition_free_space_post\" size=\"4\" value=\"$partition_free_space\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>$l_dso:&nbsp;</td><td><input type=\"checkbox\" name=\"show_software_detected_post\" value=\"y\"";
  if ($show_software_detected == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
echo "<td>$l_day:&nbsp;</td><td><input type=\"text\" name=\"days_software_detected_post\" size=\"4\" value=\"$days_software_detected\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>$l_dmp:&nbsp;</td><td><input type=\"checkbox\" name=\"show_patches_not_detected_post\" value=\"y\"";
  if ($show_patches_not_detected == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
echo "<td>$l_nop:&nbsp;</td><td><input type=\"text\" name=\"number_patches_not_detected_post\" size=\"4\" value=\"$number_patches_not_detected\" class=\"for_forms\" /></td></tr>";
echo "<tr><td>Show Detected Servers:&nbsp;</td><td><input type=\"checkbox\" name=\"show_detected_servers_post\" value=\"y\"";
  if ($show_detected_servers == "y"){ echo "checked=\"checked\"";}
  echo "/></td>";
echo "<td><td>";
echo "<tr><td colspan=\"5\"><hr /></td></tr>";
echo "<tr><td>$l_dos:&nbsp;</td><td><input type=\"checkbox\" name=\"show_os_post\" value=\"y\"";
  if ($show_os == "y"){ echo "checked=\"checked\"";}
  echo "/></td>\n";
echo "<tr><td>Display 'Date Audited' column in system list:&nbsp;</td><td><input type=\"checkbox\" name=\"show_date_audited_post\"  value=\"y\"";
  if ($show_date_audited == "y"){ echo "checked=\"checked\"";}
  echo "/></td>\n";
echo "<tr><td>Display 'Type' column in system list:&nbsp;</td><td><input type=\"checkbox\" name=\"show_type_post\" value=\"y\"";
  if ($show_type == "y"){ echo "checked=\"checked\"";}
  echo "/></td>\n";
echo "<tr><td>Display 'Description' column in system list:&nbsp;</td><td><input type=\"checkbox\" name=\"show_description_post\" value=\"y\"";
  if ($show_description == "y"){ echo "checked=\"checked\"";}
  echo "/></td>\n";
echo "<tr><td>Display 'Domain' column in system list:&nbsp;</td><td><input type=\"checkbox\" name=\"show_domain_post\" value=\"y\"";
  if ($show_domain == "y"){ echo "checked=\"checked\"";}
  echo "/></td>\n";
echo "<tr><td>Display 'Service Pack' column in system list:&nbsp;</td><td><input type=\"checkbox\" name=\"show_service_pack_post\" value=\"y\"";
  if ($show_service_pack == "y"){ echo "checked=\"checked\"";}
  echo "/></td>\n";
echo "<tr><td>Number of Systems to display:&nbsp;</td><td><input type=\"text\" name=\"count_system_post\" size=\"12\" value=\"$count_system\" class=\"for_forms\" /></td></tr>";
echo "<tr><td colspan=\"5\"><hr /></td></tr>\n";
echo "<tr><td><input type=\"submit\" value=\"Submit\" name=\"submit_button\" /></td></tr>\n";
echo "</table>\n";
echo "</form>\n";
echo "</div>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?> 
