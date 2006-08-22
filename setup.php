<?php
$page = "setup";
include "include.php";
echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">\n";

if(!(isset($_POST['submit']))){
  echo "<p><b>".__("IMPORTANT")."</b></p>\n";
  echo "<p>".__("Due to the way MySQL permissions work, the setup routine has changed")."<br />\n";
  echo __("Please do the following BEFORE proceeding")." - <br />\n";
  echo " - ".__("Create a MySQL user, and assign a password").".<br />\n";
  echo " - ".__("Create a database for Open-AudIT (openaudit is a good name)").".<br />\n";
  echo " - ".__("Assign all rights on that database to the created user").".<br />\n";
  echo " - ".__("Make sure the 'Use Old Passwords' option is set (for PHP4 users)").".<br />\n";
  echo __("Please do this BEFORE running the next step of this install").".<br />\n";
  echo __("If you are not sure how to complete these tasks, please check the")."<a href=\"http://www.open-audit.org/phpbb2/\">".__("Forums")."</a> ".__("in the FAQ section").".<br />\n";
  echo "</p>\n";
  echo "        <form name=\"setup\" action=\"setup.php\" method=\"post\" >\n";
  echo "        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
  echo "          <tr>\n";
  echo "            <td class=\"contenthead\" colspan=\"2\">".__("Hi and Welcome to the Open Audit setup page")."</td>\n";
  echo "          </tr>\n";
  echo "          <tr>\n";
  echo "            <td colspan=\"2\"><hr /></td>\n";
  echo "          </tr>\n";
  echo "          <tr>\n";
  echo "            <td>".__("Language")."?</td>\n";

  if(!isset($language)) $language="en";
  echo "<td><select size=\"1\" name=\"language\" class=\"for_forms\">\n";
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

  echo "          </tr>\n";
  echo "          <tr>\n";
  echo "            <td>".__("What is your MySQL host name")." ?</td>\n";
  echo "            <td><input type=\"text\" size=\"20\" name=\"mysql_host\" value=\"localhost\" class=\"for_forms\" /></td>\n";
  echo "          </tr>\n";
  echo "          <tr>\n";
  echo "            <td>".__("What is your MySQL user name")." ?</td>\n";
  echo "            <td><input type=\"text\" size=\"20\" name=\"mysql_user\" value=\"root\" class=\"for_forms\" /></td>\n";
  echo "          </tr>\n";
  echo "          <tr>\n";
  echo "            <td>".__("What is your MySQL password")." ?</td>\n";
  echo "            <td><input type=\"password\" size=\"20\" name=\"mysql_pass\" class=\"for_forms\" /></td>\n";
  echo "          </tr>\n";
  echo "          <tr>\n";
  echo "            <td>".__("What is your MySQL database name")." ?</td>\n";
  echo "            <td><input type=\"text\" size=\"20\" name=\"mysql_data\" value=\"openaudit\" class=\"for_forms\" /></td>\n";
  echo "          </tr>\n";

  echo "          <tr>\n";
  echo "            <td>".__("Use HTTPS://")." ?</td>\n";
  echo "            <td><select size=\"1\" name=\"use_https\" class=\"for_forms\">\n";
  echo "                <option value=\"n\" selected=\"selected\">No</option>\n";
  echo "                <option value=\"y\">Yes</option>\n";
  echo "                </select></td>\n";
  echo "          </tr>\n";

  echo "          <tr>\n";
  echo "            <td>".__("Do you wish to use authentication")." ?</td>\n";
  echo "            <td><select size=\"1\" name=\"usernames\" class=\"for_forms\">\n";
  echo "                <option value=\"n\" selected=\"selected\">No</option>\n";
  echo "                <option value=\"y\">Yes</option>\n";
  echo "                </select></td>\n";
  echo "          </tr>\n";
  echo "          <tr>\n";
  echo "            <td>".__("Username").":</td>\n";
  echo "            <td><input type=\"text\" size=\"20\" name=\"username\" value=\"admin\" class=\"for_forms\" /></td>\n";
  echo "          </tr>\n";
  echo "          <tr>\n";
  echo "            <td>".__("Password").":</td>\n";
  echo "            <td><input type=\"password\" size=\"20\" name=\"password\" value=\"Open-AudIT\" class=\"for_forms\" /></td>\n";
  echo "          </tr>\n";
  echo "          <tr><td colspan=\"2\"><hr /></td></tr>\n";
  echo "          <tr>\n";
  echo "            <td>".__("Click Submit whem you are done")."</td>\n";
  echo "            <td><input type=\"submit\" name=\"submit\" value=\"".__("Save")."\" /></td>\n";
  echo "          </tr>\n";
  echo "        </table>\n";
  echo "        </form>\n";

} else {

  // New install script
  echo "        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
  echo "          <tr>\n";
  echo "            <td class=\"contenthead\">".__("Setting up a new instance of Open Audit").".</td>\n";
  echo "          </tr>\n";
  echo "          <tr>\n";
  echo "            <td colspan=\"3\"><hr /></td>\n";
  echo "          </tr>";
  echo "          <tr>\n";
  echo "            <td>".__("Writing Config file.").".</td>\n";
  $filename = 'include_config.php';
  $content = "<";
  $content .= "?";
  $content .= "php \r\n";
  $content .= "\$mysql_server = '" . $_POST['mysql_host'] . "'; \r\n";
  $content .= "\$mysql_database = '" . $_POST['mysql_data'] . "'; \r\n";
  $content .= "\$mysql_user = '" . $_POST['mysql_user'] . "'; \r\n";
  $content .= "\$mysql_password = '" . $_POST['mysql_pass'] . "'; \r\n";
  $content .= " \r\n";
  $content .= "\$use_https = '" . $_POST['use_https'] . "'; \r\n";
  $content .= "// An array of allowed users and their passwords \r\n";
  $content .= "// Make sure to set use_pass = \"n\" if you do not wish to use passwords \r\n";
  $content .= "\$use_pass = '" . $_POST['usernames'] . "'; \r\n";
  $content .= "\$users = array( \r\n";
  $content .= "  '" . $_POST['username'] . "' => '" . $_POST['password'] . "' \r\n";
  $content .= "\n); \r\n";
  $content .= " \r\n";
  $content .= " \r\n";
  $content .= "// Config options for index.php \r\n";
  $content .= "\$show_other_discovered = 'y'; \r\n";
  $content .= "\$other_detected = '3'; \r\n";
  $content .= " \r\n";
  $content .= "\$show_system_discovered = 'y'; \r\n";
  $content .= "\$system_detected = '3'; \r\n";
  $content .= " \r\n";
  $content .= "\$show_systems_not_audited = 'y'; \r\n";
  $content .= "\$days_systems_not_audited = '3'; \r\n";
  $content .= " \r\n";
  $content .= "\$show_partition_usage = 'y'; \r\n";
  $content .= "\$partition_free_space = '1000'; \r\n";
  $content .= " \r\n";
  $content .= "\$show_software_detected = 'y'; \r\n";
  $content .= "\$days_software_detected = '5'; \r\n";
  $content .= " \r\n";
  $content .= "\$show_patches_not_detected = 'y'; \r\n";
  $content .= "\$number_patches_not_detected = '5'; \r\n";
  $content .= " \r\n";
  $content .= "\$show_detected_servers = 'y'; \r\n";
  $content .= " \r\n";
  $content .= "\$show_os = 'y'; \r\n";
  $content .= "\$show_date_audited = 'y'; \r\n";
  $content .= "\$show_type = 'y'; \r\n";
  $content .= "\$show_description = 'n'; \r\n";
  $content .= "\$show_domain = 'n'; \r\n";
  $content .= "\$show_service_pack = 'n'; \r\n";
  $content .= " \r\n";
  $content .= "\$count_system = '20'; \r\n";
  $content .= "\r\n";
  $content .= "\$col = 'blue'; \r\n";
  $content .= "\$pic_style = '_win'; \r\n";
  $content .= " \r\n";
  $content .= "\$language = '" . $_POST["language"] . "'; \r\n";
  $content .= "\r\n";
  $content .= "?";
  $content .= ">";
  if (is_writable($filename)) {
    if (!$handle = fopen($filename, 'w')) {
      echo "</tr><tr><td><h2>".("Cannot open file")." ($filename)</h2></td></tr>";
      exit;
    }
    if (fwrite($handle, $content) === FALSE) {
      echo "</tr><tr><td><h2>".__("Cannot write to file")." ($filename)</h2></td></tr>";
      exit;
    } else {
      echo "            <td>".__("Success").".</td>\n";
      echo "            <td><img src=\"images/button_ok.png\" width=\"16\" height=\"16\" /></td>\n";
      echo "          </tr>\n";
    }
    fclose($handle);
  } else {
    echo "          </tr><tr><td><h2>".__("The file")." $filename ".__("is not writable")."</h2></td></tr>";
  }
  echo "<tr><td>".__("Connecting to")." " . $_POST['mysql_host'] . " ".__("as")." " . $_POST['mysql_user'] . ".</td>\n";
  mysql_connect($_POST['mysql_host'], $_POST['mysql_user'], $_POST['mysql_pass']) or die(__("Could not connect"));
  echo "<td>".__("Connected").".</td><td><img src=\"images/button_ok.png\" width=\"16\" height=\"16\" /></td></tr>\n";
  echo "<tr><td>".__("Opening MySQL Dump file and reading contents").".</td>\n";
  $filename = "scripts/open_audit.sql";
  $handle = fopen($filename, "rb");
  $contents = fread($handle, filesize($filename));
  fclose($handle);
  echo "<td>".__("Done").".</td><td><img src=\"images/button_ok.png\" width=\"16\" height=\"16\" /></td></tr>\n";
  echo "<tr><td>".__("Creating database").".</td>\n";
  mysql_query("CREATE DATABASE /*!32312 IF NOT EXISTS*/ " . $_POST['mysql_data']) or die ("<tr><td><h1>" .__("Could not create database").".</h1></td></tr>\n");
  // mysql_query("SET PASSWORD FOR " . $_POST['mysql_user'] . "@localhost = OLD_PASSWORD('" . $_POST['mysql_pass'] . "');") or die ("Could not set password=old");
  // mysql_query("FLUSH PRIVILEGES;") or die ("Could not flush privileges");
  mysql_query("USE " . $_POST['mysql_data']) or die (__("Could not USE") ." ". $_POST['mysql_data']);
  echo "<td>".__("Done").".</td><td><img src=\"images/button_ok.png\" width=\"16\" height=\"16\" /></td></tr>\n";
  $sql = stripslashes($contents);
  $sql2 = explode(";", $sql);
  echo "<tr><td>".__("Running SQL upload").".</td>";
  foreach ($sql2 as $sql3) {
  //echo "<tr><td>" . $sql3 . "</td></tr>";
  $result = mysql_query($sql3 . ";");// or die ("</tr><tr><td><font color=\"red\">" . $sql3 . "</font></td></tr><tr><td colspan=\"2\"><h3>MySQL Error:</h3> " . mysql_error() . "</td></tr><tr>\n");
  }
  echo "<td>".__("Done").".</td></tr>\n";
  echo "<tr><td><br />".__("Click")." <a href=\"setup_2.php\">".__("here")."</a> ".__("to continue").".</td></tr>\n";
  echo "</table>\n";
}

echo "</div>";
echo "</td>\n";
include "include_right_column.php";
echo "</body>";
echo "</html> ";
include "include_png_replace.php"
?>
