<?php
$page = "setup";
include "include.php"; 

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";

if(!(isset($_POST['submit']))){
  echo "<form name=\"setup\" action=\"setup_audit.php\" method=\"post\">\n";
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
  echo "  <tr><td colspan=\"2\" class=\"contenthead\">$l_s21</td></tr>\n";
  echo "  <tr><td colspan=\"2\"><hr /></td></tr>\n";
  echo "  <tr>\n";
  echo "    <td width=\"50%\">$l_s22<br />&nbsp;</td>\n";
  echo "    <td width=\"50%\" valign=\"top\"><select size=\"1\" name=\"verbose\">\n";
  echo "        <option value=\"y\">Yes</option>\n";
  echo "        <option value=\"n\">No</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s23<br />&nbsp;</td>\n";
  echo "    <td valign=\"top\"><select size=\"1\" name=\"uuid_type\">\n";
  echo "        <option value=\"uuid\">$l_uui</option>\n";
  echo "        <option value=\"mac\" >$l_mac</option>\n";
  echo "        <option value=\"name\">$l_sys &amp; $l_dom</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s24 ?<br />&nbsp;</td>\n";
  echo "    <td valign=\"top\"><select size=\"1\" name=\"ie_visible\">\n";
  echo "        <option value=\"n\">$l_noo</option>\n";
  echo "        <option value=\"y\">$l_yes</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s25 ?<br />&nbsp;</td>\n";
  echo "    <td valign=\"top\"><select size=\"1\" name=\"ie_auto_submit\">\n";
  echo "        <option value=\"y\">$l_yes</option>\n";
  echo "        <option value=\"n\">$l_noo</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s26 ?<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><select size=\"1\" name=\"audit_ie_verbose\">\n";
  echo "        <option value=\"y\">$l_yes</option>\n";
  echo "        <option value=\"n\">$l_noo</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  if (isset($_SERVER["COMPUTERNAME"])){ $name = $_SERVER["COMPUTERNAME"]; } else {}
  if (isset($name)){} else { $name = $_SERVER["SERVER_NAME"]; }
  if (isset($name)){} else { $name = "localhost"; }
  echo "    <td>$l_s27 ?<br />$l_s28.<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"ie_form_page\" value=\"http://$name/openaudit/\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s29 ?<br />$l_s30.<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><select size=\"1\" name=\"strcomputer\">\n";
  echo "        <option value=\".\">$l_yes</option>\n";
  echo "        <option value=\"\">$l_noo</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s31 ?<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><select size=\"1\" name=\"domain_audit\">\n";
  echo "        <option value=\"n\">$l_noo</option>\n";
  echo "        <option value=\"y\">$l_yes</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s32 ?<br />Standard LDAP format.<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"domain\" value=\"\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "     <td>$l_s33.<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"input_file\" value=\"\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s34.<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"number_of_audits\" value=\"20\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s35 ?<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"email_to\" value=\"\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s36 ?<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"email_from\" value=\"\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s37 ?<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><input size=\"25\" name=\"email_server\" value=\"\" /><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s38.<br />&nbsp;</td>\n";
  echo "    <td valign=\"bottom\"><select size=\"1\" name=\"hfnet\">\n";
  echo "        <option value=\"n\">$l_noo</option>\n";
  echo "        <option value=\"y\">$l_yes</option>\n";
  echo "        </select><br />&nbsp;</td>\n";
  echo "  </tr>\n";
  echo "  <tr><td colspan=\"2\"><hr /></td></tr>\n";
  echo "  <tr>\n";
  echo "    <td>$l_s39.</td>\n";
  echo "    <td><input type=\"submit\" name=\"submit\" value=\"$l_sut\" /></td>\n";
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
      echo "</tr><tr><td><h2>Cannot open file ($filename)</h2></td></tr>\n";
      exit;
    }
    if (fwrite($handle, $content) === FALSE) {
      echo "</tr><tr><td><h2>Cannot write to file ($filename)</h2></td></tr>\n";
      exit;
    } else {
      echo "<td>Success.&nbsp;&nbsp;&nbsp;&nbsp;<img src=\"images/button_ok.png\" width=\"16\" height=\"16\" /></td></tr>\n";
    }
    fclose($handle);
  } else {
    echo "</tr><tr><td><h2>The file $filename is not writable</h2></td></tr>\n";
  }
  echo "<tr><td>$l_doe.</td></tr>\n";
  echo "<tr><td><br />$l_s40";
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
