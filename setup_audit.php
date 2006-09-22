<?php
$page = "setup";
include "include.php";

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";

echo "<p class=\"contenthead\">" . __("Audit.vbs Configuration") . "</p>";

if(!(isset($_POST['submit']))){
  echo "<form name=\"setup\" action=\"setup_audit.php\" method=\"post\">\n";
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
  echo "  <tr><td colspan=\"2\"><hr /></td></tr>\n";
  echo "  <tr>\n";
  echo "    <td width=\"50%\">".__("Verbose Console Output ?")."<br />&nbsp;</td>\n";
  echo "    <td width=\"50%\" valign=\"top\"><select size=\"1\" name=\"verbose\">\n";
  echo "        <option value=\"y\">".__("Yes")."</option>\n";
  echo "        <option value=\"n\">".__("No")."</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  <tr>\n";
  echo "    <td>".__("Use what for the unique identifier (UUID)")."<br />&nbsp;</td>\n";
  echo "    <td valign=\"top\"><select size=\"1\" name=\"uuid_type\">\n";
  echo "        <option value=\"uuid\">".__("UUID")."</option>\n";
  echo "        <option value=\"mac\" >".__("MAC-Adress")."</option>\n";
  echo "        <option value=\"name\">".__("System Name & Domain")."</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  <tr>\n";
  echo "    <td>".__("Do you wish IE to be visible when running audits ?")."<br />&nbsp;</td>\n";
  echo "    <td valign=\"top\"><select size=\"1\" name=\"ie_visible\">\n";
  echo "        <option value=\"n\">".__("No")."</option>\n";
  echo "        <option value=\"y\">".__("Yes")."</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>".__("Should the form data be auto-submitted ?")."<br />&nbsp;</td>\n";
  echo "    <td valign=\"top\"><select size=\"1\" name=\"ie_auto_submit\">\n";
  echo "        <option value=\"y\">".__("Yes")."</option>\n";
  echo "        <option value=\"n\">".__("No")."</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>".__("Verbose IE output ?")."<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><select size=\"1\" name=\"audit_ie_verbose\">\n";
  echo "        <option value=\"y\">".__("Yes")."</option>\n";
  echo "        <option value=\"n\">".__("No")."</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  if (isset($_SERVER["COMPUTERNAME"])){ $name = $_SERVER["COMPUTERNAME"]; } else {}
  if (isset($name)){} else { $name = $_SERVER["SERVER_NAME"]; }
  if (isset($name)){} else { $name = "localhost"; }
  echo "    <td>".__("What is the name of the server and directory to submit to ?")."<br>".__("NOTE - If you are running this audit from remote machines, your server name should NOT be localhost.")."&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"ie_form_page\" value=\"http://$name/openaudit/\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>".__("Do you wish to have a default system to audit ?")."<br />".__("If no command line arguement is given, audit.vbs audits the local machine ?")."<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><select size=\"1\" name=\"strcomputer\">\n";
  echo "        <option value=\".\">".__("Yes")."</option>\n";
  echo "        <option value=\"\">".__("No")."</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>".__("Do you wish to audit the domain ?")."<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><select size=\"1\" name=\"domain_audit\">\n";
  echo "        <option value=\"n\">".__("No")."</option>\n";
  echo "        <option value=\"y\">".__("Yes")."</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>".__("What is the name of the AD-Domain ?")."<br />Standard LDAP format.<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"domain\" value=\"\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "     <td>".__("What is the name of the text file for non-domain PCs (if any) ?")."<br>".__("Make sure to remove this if you don't use it.")."<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"input_file\" value=\"\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>".__("How many simultaneous audits do you wish to run ?")."<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"number_of_audits\" value=\"20\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>".__("Who should get an email of failed audits ?")." <br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"email_to\" value=\"\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>".__("Who should the email come from ?")."<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"email_from\" value=\"\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>".__("What is the email server address ?")."<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"email_server\" value=\"\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>".__("Do you wish run hfnetchk ?")."<br>".__("Remember you must have hfnetchk downloaded.")."<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><select size=\"1\" name=\"hfnet\">\n";
  echo "        <option value=\"n\">".__("No")."</option>\n";
  echo "        <option value=\"y\">".__("Yes")."</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr><td colspan=\"2\"><hr /></td></tr>\n";
  echo "  <tr>\n";
  echo "    <td>".__("Click Submit whem you are done.")."</td>\n";
  echo "    <td><input type=\"submit\" name=\"submit\" value=\"".__("Save")."\" /></td>\n";
  echo "  </tr>\n";
  echo "</table>\n";
  echo "</form>\n";

} else {
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";
  $filename = "scripts/audit.config";
  $content  = "audit_location = \"l\" \r\n";
  $content .= "verbose = \"" . $_POST['verbose'] . "\" \r\n";
  $content .= "online = \"yesxml\" \r\n";
  $content .= "strComputer = \"" . $_POST['strcomputer'] . "\" \r\n";
  $content .= "ie_visible = \"" . $_POST['ie_visible'] . "\" \r\n";
  $content .= "ie_auto_submit = \"" . $_POST['ie_auto_submit'] . "\" \r\n";
  $content .= "ie_submit_verbose = \"" . $_POST['audit_ie_verbose'] . "\" \r\n";
  $content .= "ie_form_page = \"" . $_POST['ie_form_page'] . "admin_pc_add_1.php\" \r\n";
  $content .= "non_ie_page = \"" . $_POST['ie_form_page'] . "admin_pc_add_2.php\" \r\n";
  $content .= "input_file = \"" . $_POST['input_file'] . "\" \r\n";
  $content .= "email_to = \"" . $_POST['email_to'] . "\" \r\n";
  $content .= "email_from = \"" . $_POST['email_from'] . "\" \r\n";
  $content .= "email_server = \"" . $_POST['email_server'] . "\" \r\n";
  $content .= "audit_local_domain = \"" . $_POST['domain_audit'] . "\" \r\n";
  $content .= "local_domain = \"LDAP://" . $_POST['domain'] . "\" \r\n";
  $content .= "hfnet = \"" . $_POST['hfnet'] . "\" \r\n";
  $content .= "Count = 0 \r\n";
  $content .= "number_of_audits = 20 \r\n";
  $content .= "script_name = \"audit.vbs\" \r\n";
  $content .= "monitor_detect = \"y\" \r\n";
  $content .= "printer_detect = \"y\" \r\n";
  $content .= "software_audit = \"y\" \r\n";
  $content .= "uuid_type = \"" . $_POST['uuid_type'] . "\" \r\n";
  if (is_writable($filename)) {
    if (!$handle = fopen($filename, 'w')) {
      echo "</tr><tr><td><h2>".__("Cannot open file")." ($filename)</h2></td></tr>\n";
      exit;
    }
    if (fwrite($handle, $content) === FALSE) {
      echo "</tr><tr><td><h2>".__("Cannot write to file")." ($filename)</h2></td></tr>\n";
      exit;
    } else {
      echo "<td>".__("Success").".&nbsp;&nbsp;&nbsp;&nbsp;<img src=\"images/button_ok.png\" width=\"16\" height=\"16\" /></td></tr>\n";
    }
    fclose($handle);
  } else {
    echo "</tr><tr><td><h2>".__("The file")." $filename ".__("is not writable")."</h2></td></tr>\n";
  }
  echo "<tr><td>".__("Done").".</td></tr>\n";
  echo "<tr><td><br />".__("Now make sure you go and download the following:");

  echo "<br><br>";
  echo "Shavlivk HFNetchk 3.86 command line tool - <a href=\"http://hfnetchk.shavlik.com/hfreadme.asp\">Link</a><br />\n";
  echo "Shavlik patches file - <A href=\"http://xml.shavlik.com/mssecure.cab\">Link</a><br />";
  echo "PSTools Suite - <a href=\"http://www.sysinternals.com/ntw2k/freeware/pstools.shtml\">Link</a><br />\n";
  echo "NMap command line - <a href=\"http://www.insecure.org/nmap/nmap_download.html\">Link</a><br />\n";
  echo "WinPcap for Windows - <a href=\"http://winpcap.polito.it/\">Link</a><br />\n";


  echo "<br />";
  echo __("Extract hfnetchk.exe and put it in your scripts directory.");
  echo "<br />";
  echo __("Extract MSSecure.XML from mssecure.cab, and put it in your scripts directory.");
  echo "<br />";
  echo __("Extract the pstools .exe's and put them in your web root.");
  echo "<br />";
  echo __("Install NMap, and make sure it's in your command path.");
  echo "<br />";
  echo __("Install WinPcap (for NMap to use)");
  echo "<br />";
  echo "<br />";
  echo __("START AUDITING !!!");

  echo "</td></tr>\n";
  echo "</table>";
}

echo "</div>";
echo "</td>\n";
include "include_right_column.php";
echo "</body>";
echo "</html> ";
include "include_png_replace.php"
?>
