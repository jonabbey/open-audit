<?php
$page = "";
$extra = "";
$software = "";
$count = 0;
if (isset($_GET['software'])) {$software = $_GET['software'];} else {}
if (isset($_GET['sort'])) {$sort = $_GET['sort'];} else {$sort= "system_name";}
include "include.php"; 

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";


$title = "";
if (isset($_GET["show_all"])){ $count_system = '10000'; } else {}
if (isset($_GET["page_count"])){ $page_count = $_GET["page_count"]; } else { $page_count = 0;}
$page_prev = $page_count - 1;
if ($page_prev < 0){ $page_prev = 0; } else {}
$page_next = $page_count + 1;
$page_current = $page_count;
$page_count = $page_count * $count_system;

echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
echo " <tr>\n  <td align=\"left\" class=\"contenthead\" >$l_syc $l_res.<br />&nbsp;</td>\n";
//include "include_list_buttons.php";
echo " </tr>\n</table>\n";

echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
echo "<tr>\n";
echo "  <td align=\"center\">$l_ipa</td>\n";
echo "  <td align=\"center\">$l_sys</td>\n";
echo "  <td>Field</td>\n";
echo "  <td>Result</td>\n";
echo "</tr>";

$search = stripslashes($_POST["search_field"]);
$search = mysql_real_escape_string($search);
$search = strtoupper($search);

$sql  = "SELECT system_name, system_uuid, net_ip_address, bios_description, bios_manufacturer, bios_serial_number FROM system, bios WHERE ";
$sql .= "bios_uuid = system_uuid AND ";
$sql .= "bios_timestamp = system_timestamp AND (";
$sql .= "bios_description LIKE '%$search%' OR ";
$sql .= "bios_manufacturer LIKE '%$search%' OR ";
$sql .= "bios_serial_number LIKE '%$search%')";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  do {
    if (strpos(strtoupper($myrow["bios_description"]), $search) !== false){$search_field = "Bios Description"; $search_result = $myrow["bios_description"] . " - " . $myrow["software_name"];}
    if (strpos(strtoupper($myrow["bios_manufacturer"]), $search) !== false){$search_field = "Bios Manufacturer"; $search_result = $myrow["bios_manufacturer"];}
    if (strpos(strtoupper($myrow["bios_serial_number"]), $search) !== false){$search_field = "Bios Serial"; $search_result = $myrow["bios_serial_number"];}
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    $result_set[] = array($myrow["system_name"], $myrow["system_uuid"], ip_trans($myrow["net_ip_address"]), $search_field, $search_result);
  } while ($myrow = mysql_fetch_array($result));
} else {}

$sql  = "SELECT system_uuid, system_name, net_ip_address, net_domain, system_model, system_primary_owner_name, system_system_type, ";
$sql .= "system_id_number, system_vendor, time_caption, system_os_name, system_country_code, system_description, ";
$sql .= "system_organisation, system_registered_user, system_serial_number, system_version, system_windows_directory ";
$sql .= "FROM system WHERE ";
$sql .= "system_name LIKE '%$search%' OR ";
$sql .= "net_ip_address LIKE '%$search%' OR ";
$sql .= "net_domain LIKE '%$search%' OR ";
$sql .= "system_model LIKE '%$search%' OR ";
$sql .= "system_primary_owner_name LIKE '%$search%' OR ";
$sql .= "system_system_type LIKE '%$search%' OR ";
$sql .= "system_id_number LIKE '%$search%' OR ";
$sql .= "system_vendor LIKE '%$search%' OR ";
$sql .= "time_caption LIKE '%$search%' OR ";
$sql .= "system_os_name LIKE '%$search%' OR ";
$sql .= "system_country_code LIKE '%$search%' OR ";
$sql .= "system_description LIKE '%$search%' OR ";
$sql .= "system_organisation LIKE '%$search%' OR ";
$sql .= "system_registered_user LIKE '%$search%' OR ";
$sql .= "system_serial_number LIKE '%$search%' OR ";
$sql .= "system_version LIKE '%$search%' OR ";
$sql .= "system_windows_directory LIKE '%$search%' ";
$sql .= "ORDER BY system_name";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  do {
    if (strpos(strtoupper($myrow["system_name"]), $search) !== false){$search_field = "System Name"; $search_result = $myrow["system_name"];}
    if (strpos(strtoupper($myrow["net_ip_address"]), $search) !== false){$search_field = "IP Address"; $search_result = $myrow["net_ip_address"];}
    if (strpos(strtoupper($myrow["net_domain"]), $search) !== false){$search_field = "Domain"; $search_result = $myrow["net_domain"];}
    if (strpos(strtoupper($myrow["system_model"]), $search) !== false){$search_field = "System Model"; $search_result = $myrow["system_model"];}
    if (strpos(strtoupper($myrow["system_primary_owner_name"]), $search) !== false){$search_field = "Registered Owner"; $search_result = $myrow["system_primary_owner_name"];}
    if (strpos(strtoupper($myrow["system_system_type"]), $search) !== false){$search_field = "System Type"; $search_result = $myrow["system_system_type"];}
    if (strpos(strtoupper($myrow["system_id_number"]), $search) !== false){$search_field = "ID Number"; $search_result = $myrow["system_id_number"];}
    if (strpos(strtoupper($myrow["system_vendor"]), $search) !== false){$search_field = "System Manufacturer"; $search_result = $myrow["system_vendor"];}
    if (strpos(strtoupper($myrow["time_caption"]), $search) !== false){$search_field = "Time Zone"; $search_result = $myrow["time_caption"];}
    if (strpos(strtoupper($myrow["system_os_name"]), $search) !== false){$search_field = "Operating System"; $search_result = $myrow["system_os_name"];}
    if (strpos(strtoupper($myrow["system_country_code"]), $search) !== false){$search_field = "Country"; $search_result = $myrow["system_country_code"];}
    if (strpos(strtoupper($myrow["system_description"]), $search) !== false){$search_field = "Description"; $search_result = $myrow["system_description"];}
    if (strpos(strtoupper($myrow["system_organisation"]), $search) !== false){$search_field = "Registered Organisation"; $search_result = $myrow["system_organisation"];}
    if (strpos(strtoupper($myrow["system_registered_user"]), $search) !== false){$search_field = "Registered User"; $search_result = $myrow["system_registered_user"];}
    if (strpos(strtoupper($myrow["system_serial_number"]), $search) !== false){$search_field = "Serial Number"; $search_result = $myrow["system_serial_number"];}
    if (strpos(strtoupper($myrow["system_version"]), $search) !== false){$search_field = "System Version"; $search_result = $myrow["system_version"];}
    if (strpos(strtoupper($myrow["system_windows_directory"]), $search) !== false){$search_field = "Windows Directory"; $search_result = $myrow["system_windows_directory"];}
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    $result_set[] = array($myrow["system_name"], $myrow["system_uuid"], ip_trans($myrow["net_ip_address"]), $search_field, $search_result);
  } while ($myrow = mysql_fetch_array($result));
} else {}
$search_field = "";
$search_result = "";

$sql  = "SELECT system_name, system_uuid, net_ip_address, monitor_manufacturer, monitor_model, monitor_serial FROM system, monitor WHERE ";
$sql .= "monitor_uuid = system_uuid AND ";
$sql .= "monitor_timestamp = system_timestamp AND (";
$sql .= "monitor_manufacturer LIKE '%$search%' OR ";
$sql .= "monitor_model LIKE '%$search%' OR ";
$sql .= "monitor_serial LIKE '%$search%')";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  do {
    if (strpos(strtoupper($myrow["monitor_manufacturer"]), $search) !== false){$search_field = "Monitor Manufacturer"; $search_result = $myrow["monitor_manufacturer"];}
    if (strpos(strtoupper($myrow["monitor_model"]), $search) !== false){$search_field = "Monitor Model"; $search_result = $myrow["monitor_model"];}
    if (strpos(strtoupper($myrow["monitor_serial"]), $search) !== false){$search_field = "Monitor Serial"; $search_result = $myrow["monitor_serial"];}
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    $result_set[] = array($myrow["system_name"], $myrow["system_uuid"], ip_trans($myrow["net_ip_address"]), $search_field, $search_result);
  } while ($myrow = mysql_fetch_array($result));
} else {}

$sql  = "SELECT system_name, system_uuid, net_ip_address, software_name, software_publisher, software_version FROM system, software WHERE ";
$sql .= "software_uuid = system_uuid AND ";
$sql .= "software_timestamp = system_timestamp AND (";
$sql .= "software_name LIKE '%$search%' OR ";
$sql .= "software_publisher LIKE '%$search%' OR ";
$sql .= "software_version LIKE '%$search%')";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  do {
    if (strpos(strtoupper($myrow["software_publisher"]), $search) !== false){$search_field = "Software Publisher"; $search_result = $myrow["software_publisher"] . " - " . $myrow["software_name"];}
    if (strpos(strtoupper($myrow["software_name"]), $search) !== false){$search_field = "Software Name"; $search_result = $myrow["software_name"];}
    if (strpos(strtoupper($myrow["software_version"]), $search) !== false){$search_field = "Software Version"; $search_result = $myrow["software_version"];}
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    $result_set[] = array($myrow["system_name"], $myrow["system_uuid"], ip_trans($myrow["net_ip_address"]), $search_field, $search_result);
  } while ($myrow = mysql_fetch_array($result));
} else {}

$sql  = "SELECT system_name, system_uuid, net_ip_address, usb_description FROM system, usb WHERE ";
$sql .= "usb_uuid = system_uuid AND ";
$sql .= "usb_timestamp = system_timestamp AND (";
$sql .= "usb_description LIKE '%$search%')";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  do {
    if (strpos(strtoupper($myrow["usb_description"]), $search) !== false){$search_field = "USB Description"; $search_result = $myrow["usb_description"] . " - " . $myrow["software_name"];}
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    $result_set[] = array($myrow["system_name"], $myrow["system_uuid"], ip_trans($myrow["net_ip_address"]), $search_field, $search_result);
  } while ($myrow = mysql_fetch_array($result));
} else {}

$sql  = "SELECT system_name, system_uuid, net_ip_address, video_description FROM system, video WHERE ";
$sql .= "video_uuid = system_uuid AND ";
$sql .= "video_timestamp = system_timestamp AND (";
$sql .= "video_description LIKE '%$search%')";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  do {
    if (strpos(strtoupper($myrow["video_description"]), $search) !== false){$search_field = "Video Description"; $search_result = $myrow["video_description"] . " - " . $myrow["software_name"];}
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    $result_set[] = array($myrow["system_name"], $myrow["system_uuid"], ip_trans($myrow["net_ip_address"]), $search_field, $search_result);
  } while ($myrow = mysql_fetch_array($result));
} else {}



sort($result_set);
$count = count ($result_set);
for ($i=0; $i<$count; $i++){
  $countmore=count($result_set[0]);
  echo "<tr>";
  echo "<td align=\"center\">" . $result_set[$i][2] . "</td>";
  echo "<td align=\"center\"><a href=\"system_summary.php?pc=" . $result_set[$i][1] . "\">" . $result_set[$i][0] . "</a></td>";
  echo "<td>" . $result_set[$i][3] . "</td>";
  echo "<td>" . $result_set[$i][4] . "</td>";
  echo "</tr>\n";
}

echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
