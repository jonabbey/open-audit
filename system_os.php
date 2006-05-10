<?php 
$page = "os";
include "include.php"; 

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"  width=\"100%\">";
echo "<tr>";
echo "<td align=\"left\" class=\"contenthead\" colspan=\"3\">Operating System Settings for " . $name . "<br />&nbsp;</td>";
echo "</tr>";


if (($sub == "su") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM system WHERE system_uuid = '$pc' AND system_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/summary_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_sum</td></tr>\n";
      echo "<tr><td>$l_sys:</td><td>" . $myrow["system_name"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_des:</td><td>" . $myrow["system_description"] . "</td></tr>\n";
      echo "<tr><td>$l_reg:</td><td>" . $myrow["system_registered_user"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_osy:</td><td>" . $myrow["system_os_name"] . "</td></tr>\n";
      echo "<tr><td>$l_mam:</td><td>" . $myrow["system_vendor"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_mdl / $l_srl:</td><td>" . $myrow["system_model"] . " / " . $myrow["system_id_number"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}

if (($sub == "os") or ($sub == "all")){
  $SQL = "SELECT software_version FROM software WHERE software_name = 'Internet Explorer' AND software_uuid = '$pc' AND software_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  $myrow = mysql_fetch_array($result);
  $version_ie = $myrow["software_version"];
  $SQL = "SELECT software_version FROM software WHERE software_name LIKE 'DirectX%' AND software_uuid = '$pc' AND software_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  $myrow = mysql_fetch_array($result);
  $version_dx = $myrow["software_version"];
  $SQL = "SELECT software_version FROM software WHERE software_name = 'Windows Media Player' AND software_uuid = '$pc' AND software_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  $myrow = mysql_fetch_array($result);
  $version_wmp = $myrow["software_version"];
  $opt_count = 0;
  $SQL = "SELECT * FROM system WHERE system_uuid = '$pc' AND system_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/os_l.png\" width=\"48\" height=\"48\" alt=\"\" /> OS Information</td></tr>\n";
      echo "<tr><td>$l_osy:</td><td>" . $myrow["system_os_name"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_reg:</td><td>" . $myrow["system_registered_user"] . "</td></tr>\n";
      echo "<tr><td>$l_reo:</td><td>" . $myrow["system_organisation"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_osa $l_ver / $l_sep:</td><td>" . $myrow["system_build_number"] . " / " . $myrow["system_service_pack"] . "</td></tr>\n";
      echo "<tr><td>$l_win:</td><td>" . $myrow["system_windows_directory"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_wis:</td><td>" . $myrow["system_serial_number"] . "</td></tr>\n";
      echo "<tr><td>$l_osz:</td><td>" . $myrow["date_system_install"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_lan:</td><td>" . $myrow["system_language"] . "</td></tr>\n";
      echo "<tr><td>$l_tim:</td><td>" . $myrow["time_caption"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_dal:</td><td>" . $myrow["time_daylight"] . "</td></tr>\n";
      echo "<tr><td>$l_drx $l_ver:</td><td>$version_dx</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_wmp $l_ver:</td><td>$version_wmp</td></tr>\n";
      echo "<tr><td>$l_ine $l_ver:</td><td>$version_ie</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


if (($sub == "ne") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM network_card WHERE net_uuid = '$pc' AND net_timestamp = '$timestamp' AND net_ip_address <> '000.000.000.000'";
  $result = mysql_query($SQL, $db);
  echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/network_device_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_nws</td></tr>";
  if ($myrow = mysql_fetch_array($result)){
    echo "<tr><td>$l_ipa / $l_sub:</td><td>" . ip_trans($myrow["net_ip_address"]) . " / " . $myrow["net_ip_subnet"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_dhe / $l_dhs:</td><td>" . $myrow["net_dhcp_enabled"] . " / " . $myrow["net_dhcp_server"] . "</td></tr>\n";
    echo "<tr><td>$l_dnv:</td><td>" . $myrow["net_dns_server"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_wns:</td><td>" . $myrow["net_wins_primary"] . "</td></tr>\n";
  } else {}
  $SQL = "SELECT * FROM system WHERE system_uuid = '$pc' AND system_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      echo "<tr><td>$l_dom / $l_dsn:</td><td>" . $myrow["net_domain"] . " / " . $myrow["net_client_site_name"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_doc:</td><td>" . $myrow["net_domain_controller_name"] . "</td></tr>\n";
      echo "<tr><td>$l_cus:</td><td>" . $myrow["net_user_name"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


if ($sub == "sh") {
  $opt_count = 0;
  $SQL = "SELECT * FROM shares WHERE shares_uuid = '$pc' AND shares_timestamp = '$timestamp' ORDER BY shares_path, shares_name";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    echo "<tr><td class=\"contenthead\" colspan=\"3\"><br /><img src=\"images/shared_drive_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_shd</td></tr>";
    echo "<tr><td>$l_paa</td><td>$l_nam</td><td>$l_des</td></tr>\n";
    do {
      echo "<tr bgcolor=\"$bgcolor\"><td>" . $myrow["shares_name"] . "</td>";
      echo "<td>" . $myrow["shares_caption"] . "</td>";
      echo "<td>" . str_replace("&","&amp;",$myrow["shares_path"]) . "</td></tr>\n";
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}

echo "<tr><td>&nbsp;</td></tr>";
echo "</table>";
echo "</div>";
echo "</td>\n";
include "include_right_column.php";
echo "</body>";
echo "</html> ";
include "include_png_replace.php"
?>
