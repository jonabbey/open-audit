<?php 
$page = "se";
include "include.php"; 
echo "<td valign=\"top\">\n";

echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
echo "<tr><td class=\"contenthead\" colspan=\"2\">$l_sed " . $name . "<br />&nbsp;</td></tr>\n";
echo "</table>";

echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";

if (($sub == "fw") or ($sub == "all")){
$opt_count = 0;
$SQL = "SELECT * FROM system WHERE system_uuid = '$pc' AND system_timestamp = '$timestamp'";
$result = mysql_query($SQL, $db);
$myrow = mysql_fetch_array($result);
if (substr_count($myrow["system_os_name"], "Microsoft Windows XP") > 0) {
  echo "<tr><td colspan=\"4\" class=\"contenthead\"><img src=\"images/firewall_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_xpf</td></tr>";
  echo "<tr><td colspan=\"4\"><b>General Settings - Domain.</b></td></tr>";
  if (($myrow["firewall_enabled_domain"] == "1") OR ($myrow["firewall_enabled_domain"] == "0")) {
    $enabled = $myrow["firewall_enabled_domain"];
    if ($enabled == "0") { $enabled = "No";} else { }
    if ($enabled == "1") { $enabled = "Yes";} else { }
    $notifications = $myrow["firewall_disablenotifications_domain"];
    if ($notifications == "0") { $notifications = "No"; } else {}
    if ($notifications == "1") { $notifications = "Yes"; } else {}
    $excep = $myrow["firewall_donotallowexceptions_domain"];
    if ($excep == "0") { $excep = "No"; } else {}
    if ($excep == "1") { $excep = "Yes"; } else {}
    echo "<tr bgcolor=\"$bg1\"><td width=\"25%\">$l_fiw:&nbsp;</td><td width=\"25%\">" . $enabled . "</td><td width=\"25%\"></td><td width=\"25%\"></td></tr>\n";
    echo "<tr><td>$l_not:&nbsp;</td><td>" . $notifications . "</td><td></td><td></td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_exp:&nbsp;</td><td>" . $excep . "</td><td></td><td></td></tr>\n";
  } else {
    echo "<tr bgcolor=\"$bg1\"><td colspan=\"2\">$l_doo.</td><td></td><td></td></tr>\n";
  }
  echo "<tr><td colspan=\"4\"><b>$l_ges.</b></td></tr>";
  $bgcolor = "#FFFFFF";
  if (($myrow["firewall_enabled_standard"] == "1") OR ($myrow["firewall_enabled_standard"] == "0")) {
    $enabled = $myrow["firewall_enabled_standard"];
    if ($enabled == "0") { $enabled = "No";} else { }
    if ($enabled == "1") { $enabled = "Yes";} else { }
    $notifications = $myrow["firewall_disablenotifications_standard"];
    if ($notifications == "0") { $notifications = "No"; } else {}
    if ($notifications == "1") { $notifications = "Yes"; } else {}
    $excep = $myrow["firewall_donotallowexceptions_standard"];
    if ($excep == "0") { $excep = "No"; } else {}
    if ($excep == "1") { $excep = "Yes"; } else {}
    echo "<tr bgcolor=\"$bg1\"><td>$l_fiw:&nbsp;</td><td>" . $enabled . "</td><td></td><td></td></tr>\n";
    echo "<tr><td>$l_not:&nbsp;</td><td>" . $notifications . "</td><td></td><td></td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_exp:&nbsp;</td><td>" . $excep . "</td><td></td><td></td></tr>\n";
  } else {
    echo "<tr bgcolor=\"$bg1\"><td colspan=\"2\">$l_gen.</td><td></td><td></td></tr>\n";
  }
  $SQL = "SELECT * FROM firewall_ports where port_uuid = '$pc' AND port_timestamp = '$timestamp' ORDER BY port_profile, port_number";
  $result = mysql_query($SQL, $db);
  $myrow = mysql_fetch_array($result);
  echo "<tr><td colspan=\"4\"><b>$l_pot.</b></td></tr>\n";
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = "#F1F1F1";
    do {
      echo "<tr bgcolor=\"$bgcolor\">\n";
      echo "  <td>" . $myrow["port_profile"] .  "</td><td>"  . $myrow["port_number"] . "</td>\n";
      echo "  <td>" . $myrow["port_protocol"] .  "</td><td>"  . $myrow["port_scope"] . "</td>\n";
      echo "</tr>\n";
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
  } else {}
  $SQL = "SELECT * FROM firewall_auth_app where firewall_app_uuid = '$pc' AND firewall_app_timestamp = '$timestamp'  ORDER BY firewall_app_profile, firewall_app_name";
  $result = mysql_query($SQL, $db);
  $myrow = mysql_fetch_array($result);
  if ($myrow = mysql_fetch_array($result)){
    echo "<tr><td colspan=\"4\"><b>$l_pos.</b></td></tr>\n";
    $bgcolor = "#F1F1F1";
    do {
      echo "<tr bgcolor=\"$bgcolor\">\n";
      echo "  <td>" . $myrow["firewall_app_profile"] . "</td><td>" . $myrow["firewall_app_name"] . "</td>\n";
      echo "  <td colspan=\"2\">" . $myrow["firewall_app_executable"] . "</td>\n";
      echo "</tr>\n";
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
  } else {}
  $opt_count = 0;
  $SQL = "SELECT * from software WHERE software_uuid = '$pc' AND software_timestamp = '$timestamp' AND software_name LIKE '%ZoneAlarm%'";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = "#F1F1F1";
    do {
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><img src=\"images/firewall_l.png\" width=\"48\" height=\"48\" alt=\"\" /> Other Firewall</td></tr>\n";
      echo "<tr bgcolor=\"$bgcolor\"><td>Enabled:</td><td colspan=\"3\">" . $myrow["software_name"] . "</td></tr>\n";
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
  } else {}
  } // End of Firewall
}


if (($sub == "vi") or ($sub == "all")){
$opt_count = 0;
$SQL = "SELECT * from system WHERE system_uuid = '$pc' AND system_timestamp = '$timestamp' AND (virus_name <> '' OR virus_manufacturer <> '') AND system_os_name LIKE '%Windows%'";
$result = mysql_query($SQL, $db);
if ($myrow = mysql_fetch_array($result)){
  echo "<tr><td class=\"contenthead\" colspan=\"4\"><br /><img src=\"images/antivirus_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_ant</td><td></td></tr>\n";
  $bgcolor = "#F1F1F1";
  do {
    echo "<tr bgcolor=\"$bgcolor\"><td>$l_nam:</td><td colspan=\"3\">" . $myrow["virus_name"] . "</td></tr>\n";
    echo "<tr><td>$l_mam:</td><td colspan=\"3\">" . $myrow["virus_manufacturer"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bgcolor\"><td>$l_ver:</td><td colspan=\"3\">" . $myrow["virus_version"] . "</td></tr>\n";
    echo "<tr><td>$l_def:</td><td colspan=\"3\">" . $myrow["virus_uptodate"] . "</td></tr>\n";
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
  } while ($myrow = mysql_fetch_array($result));
} else {
  // Check the software for possible installations.
  $sql = "SELECT * FROM software WHERE software_uuid = '$pc' AND software_timestamp = '$timestamp' AND software_name LIKE '%virus%'";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = "#F1F1F1";
    do {
      echo "<tr bgcolor=\"$bgcolor\"><td>$l_nam:</td><td colspan=\"3\">" . $myrow["software_name"] . "</td></tr>\n";
      echo "<tr><td>$l_mam:</td><td colspan=\"3\">" . $myrow["software_publisher"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bgcolor\"><td>$l_ver:</td><td colspan=\"3\">" . $myrow["software_version"] . "</td></tr>\n";
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
  } else {}
}
} // End of AntiVirus

if (($sub == "nm") or ($sub == "all")){
$opt_count = 0;
$SQL = "SELECT * from nmap_ports WHERE nmap_other_id = '" . $pc . "' ORDER BY nmap_port_number";
$result = mysql_query($SQL, $db);
echo "<tr><td class=\"contenthead\" colspan=\"4\"><br /><img src=\"images/nmap_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_nma</td></tr>\n";
if ($myrow = mysql_fetch_array($result)){
  $bgcolor = "#F1F1F1";
  do {
    echo "<tr bgcolor=\"$bgcolor\"><td>" . $myrow["nmap_port_number"] . "</td><td colspan=\"3\">" . $myrow["nmap_port_name"] . "</td></tr>\n";
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
  } while ($myrow = mysql_fetch_array($result));
} else {
  $bgcolor = "#F1F1F1";
  echo "<tr bgcolor=\"$bgcolor\"><td colspan=\"4\">$l_npt.</td></tr>\n";
}
} // End of nmap

echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
