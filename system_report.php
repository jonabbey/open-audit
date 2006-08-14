<?php

if ($_GET['pc']){$pc = $_GET['pc'];} else {}
include "include_config.php";
include "include_functions.php";
include "include_lang_english.php";

$db = mysql_connect($mysql_server,$mysql_user,$mysql_password) or die('Could not connect: ' . mysql_error());
mysql_select_db($mysql_database,$db);

?>
<html>
<head>
<title>Open-AudIT - Report</title>
<style type="text/css">
body {
 font-family: verdana;
 font-size: 9pt;
}
h1,h2 {
 font-family: Trebuchet MS;
}
.content {
 position: relative;
 width: 600px;
 min-width: 700px;
 margin: 0 0px 10px 0px;
 border: 1px solid black;
 background-color: white;
 padding: 10px;
 z-index: 3;
 font-family: verdana;
 font-size: 9pt;
}
</style>
</head>
<body>
<?php
$sql = "SELECT * FROM system WHERE system_uuid = '$pc' OR system_name = '$pc'";
$result = mysql_query($sql, $db);
$myrow = mysql_fetch_array($result);
$pc = $myrow['system_uuid'];

$sql2 = "SELECT * FROM network_card WHERE net_uuid = '$pc' AND net_timestamp = '" . $myrow["system_timestamp"] . "'";
$result2 = mysql_query($sql2, $db);
$myrow2 = mysql_fetch_array($result2);

$sql3 = "SELECT * FROM bios WHERE bios_uuid = '$pc' AND bios_timestamp = '" . $myrow["system_timestamp"] . "'";
$result3 = mysql_query($sql3, $db);
$bios = mysql_fetch_array($result3);

$sql4 = "SELECT * FROM processor WHERE processor_uuid = '$pc' AND processor_timestamp = '" . $myrow["system_timestamp"] . "'";
$result4 = mysql_query($sql4, $db);

$sql5 = "SELECT * FROM video WHERE video_uuid = '$pc' AND video_timestamp = '" . $myrow["system_timestamp"] . "'";
$result5 = mysql_query($sql5, $db);

$sql6 = "SELECT * FROM monitor WHERE monitor_uuid = '$pc' AND monitor_timestamp = '" . $myrow["system_timestamp"] . "'";
$result6 = mysql_query($sql6, $db);

$sql7 = "SELECT * FROM hard_drive WHERE hard_drive_uuid = '$pc' AND hard_drive_timestamp = '" . $myrow["system_timestamp"] . "'";
$result7 = mysql_query($sql7, $db);

$sql8 = "SELECT * FROM optical_drive WHERE optical_drive_uuid = '$pc' AND optical_drive_timestamp = '" . $myrow["system_timestamp"] . "'";
$result8 = mysql_query($sql8, $db);

$sql9 = "SELECT * FROM keyboard WHERE keyboard_uuid = '$pc' AND keyboard_timestamp = '" . $myrow["system_timestamp"] . "'";
$result9 = mysql_query($sql9, $db);
$keyboard = mysql_fetch_array($result9);

$sql10 = "SELECT * FROM mouse WHERE mouse_uuid = '$pc' AND mouse_timestamp = '" . $myrow["system_timestamp"] . "'";
$result10 = mysql_query($sql10, $db);
$mouse = mysql_fetch_array($result10);

$sql11 = "SELECT * FROM sound WHERE sound_uuid = '$pc' AND sound_timestamp = '" . $myrow["system_timestamp"] . "'";
$result11 = mysql_query($sql11, $db);
$sound = mysql_fetch_array($result11);

echo "<h1>$l_rep " . $myrow['system_name'] . "</h1>\n";
echo "<div id=\"content\">\n";
echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" class=\"content\">\n";
echo "  <tr><td colspan=\"2\"><b>$l_syw</b></td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td width=\"250\">$l_usn: </td><td>" . $myrow['net_user_name'] . "</td></tr>\n";
echo "  <tr><td>$l_dau: </td><td>" . return_date_time($myrow['system_timestamp']) . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_tim: </td><td>" . $myrow['time_caption'] . "</td></tr>\n";
echo "  <tr><td width=\"250\">$l_reg: </td><td>" . $myrow['system_primary_owner_name'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_uui: </td><td>" . $myrow['system_uuid'] . "</td></tr>\n";
echo "  <tr><td>$l_mdl: </td><td>" . $myrow['system_model'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_srl: </td><td>" . $myrow['system_id_number'] . "</td></tr>\n";
echo "  <tr><td>$l_mam: </td><td>" . $myrow['system_vendor'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_cha: </td><td>" . $myrow['system_system_type'] . "</td></tr>\n";
echo "</table>\n";
echo "</div>\n";
echo "<br />\n";

echo "<div id=\"content\">\n";
echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" class=\"content\">\n";
echo "  <tr><td colspan=\"2\"><b>$l_wii</b></td></tr>\n";
echo "  <tr><td width=\"250\">$l_osy: </td><td>" . $myrow['system_os_name'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_osz: </td><td>" . $myrow['date_system_install'] . "</td></tr>\n";
echo "  <tr><td>$l_reg: </td><td>" . $myrow['system_registered_user'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_reo: </td><td>" . $myrow['system_organisation'] . "</td></tr>\n";
echo "  <tr><td>$l_cou: </td><td>" . $myrow['system_country_code'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_lan: </td><td>" . $myrow['system_language'] . "</td></tr>\n";
echo "  <tr><td>$l_srl: </td><td>" . $myrow['system_serial_number'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_sep: </td><td>" . $myrow['system_service_pack'] . "</td></tr>\n";
echo "  <tr><td>$l_win: </td><td>" . $myrow['system_windows_directory'] . "</td></tr>\n";
echo "</table>\n";
echo "</div>\n";
echo "<br />\n";

echo "<div id=\"content\">\n";
echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" class=\"content\">\n";
echo "  <tr><td colspan=\"2\"><b>$l_nws</b></td></tr>\n";
echo "  <tr><td width=\"250\">$l_sys: </td><td>" . $myrow['system_name'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_des: </td><td>" . $myrow['system_description'] . "</td></tr>\n";
echo "  <tr><td>$l_mac: </td><td>" . $myrow2['net_mac_address'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_ipa: </td><td>" . $myrow['net_ip_address'] . "</td></tr>\n";
echo "  <tr><td>$l_sub: </td><td>" . $myrow2['net_ip_subnet'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_dhe: </td><td>" . $myrow2['net_dhcp_enabled'] . "</td></tr>\n";
echo "  <tr><td>$l_dhs: </td><td>" . $myrow2['net_dhcp_server'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_wns: </td><td>" . $myrow2['net_wins_primary'] . "</td></tr>\n";
echo "  <tr><td>$l_dnv: </td><td>" . $myrow2['net_dns_server'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_mam: </td><td>" . $myrow2['net_manufacturer'] . "</td></tr>\n";
echo "  <tr><td>$l_des: </td><td>" . $myrow2['net_description'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_don: </td><td>" . $myrow['net_domain_role'] . "</td></tr>\n";
echo "  <tr><td>$l_dom: </td><td>" . $myrow['net_domain'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_dsn: </td><td>" . $myrow['net_client_site_name'] . "</td></tr>\n";
echo "  <tr><td>$l_doc: </td><td>" . $myrow['net_domain_controller_name'] . "</td></tr>\n";
echo "</table>\n";
echo "</div>\n";

echo "<br style=\"page-break-before:always;\" />\n";

echo "<div id=\"content\">\n";
echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" class=\"content\">\n";
echo "  <tr><td colspan=\"2\"><b>$l_hwd</b></td></tr>\n";
echo "  <tr><td width=\"250\">$l_bio $l_man: </td><td>" .  $bios['bios_manufacturer'] . "</td></tr>\n";
echo "  <tr bgcolor=\"#F1F1F1\"><td>$l_bio $l_ver: </td><td>" .  $bios['bios_version'] . "</td></tr>\n";

if (($processor = mysql_fetch_array($result4))){
  do { 
    echo "<tr><td width=\"250\">$l_pro: </td><td>" . $processor['processor_name'] . "</td></tr>";
    echo "<tr bgcolor=\"#F1F1F1\"><td>$l_pro $l_spd: </td><td>" . number_format($processor['processor_max_clock_speed']) . " Mhz</td></tr>";
  } while ($processor = mysql_fetch_array($result4));
} else {}
echo "<tr><td>$l_mem: </td><td>" . number_format($myrow['system_memory']) . " MB</td></tr>";
if (($video = mysql_fetch_array($result5))){
  do { 
    echo "<tr bgcolor=\"#F1F1F1\"><td>$l_vid &amp; $l_mem: </td><td>" . $video['video_caption'] . " - " . $video['video_adapter_ram'] . " MB</td></tr>";
    echo "<tr><td>$l_vdr $l_dat &amp; $l_ver: </td><td>" . $video['video_driver_date'] . " - " . $video['video_driver_version'] . "</td></tr>";
  } while ($video = mysql_fetch_array($result5));
} else {}
if (($monitor = mysql_fetch_array($result6))){
  do { 
    echo "<tr bgcolor=\"#F1F1F1\"><td>$l_moo $l_mam: </td><td>" . $monitor['monitor_manufacturer'] . "</td></tr>";
    echo "<tr><td>$l_moo $l_mdl: </td><td>" . $monitor['monitor_model'] . "</td></tr>";
  } while ($monitor = mysql_fetch_array($result6));
} else {}
if (($hard_drive = mysql_fetch_array($result7))){
  do { 
    echo "<tr bgcolor=\"#F1F1F1\"><td>$l_hde &amp; $l_mdl: </td><td>" . $hard_drive['hard_drive_interface_type'] . " - " . $hard_drive['hard_drive_model'] . "</td></tr>";
    echo "<tr><td>$l_hde $l_siz &amp $l_pau: </td><td>" . number_format($hard_drive['hard_drive_size']) . " MB - " . $hard_drive['hard_drive_partitions'] . "</td></tr>";
    $sql_partition = "SELECT * FROM partition WHERE (partition_uuid = '$pc' && partition_timestamp = '" . $myrow['system_timestamp'] . "' && partition_disk_index = '" . $hard_drive["hard_drive_index"] . "') ORDER BY partition_caption ";
    $sql_partition_result = mysql_query($sql_partition, $db);
    if ($partition = mysql_fetch_array($sql_partition_result)){ 
      do {
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_pau $l_drv &amp; $l_fmt: </td><td>" . $partition['partition_caption'] . " - " . $partition['partition_file_system'] . "</td></tr>";
        echo "<tr><td>$l_pau $l_siz &amp; $l_fre: </td><td>" . number_format($partition['partition_size']) . " MB - ". number_format($partition['partition_free_space']) . " MB</td></tr>";
      } while ($partition = mysql_fetch_array($sql_partition_result));
    } else {}
  } while ($hard_drive = mysql_fetch_array($result7));
} else {}
if (($optical_drive = mysql_fetch_array($result8))){
  do {
    echo "<tr bgcolor=\"#F1F1F1\"><td>$l_ode: </td><td>" . $optical_drive['optical_drive_drive'] . "</td></tr>";
    echo "<tr><td>$l_ode $l_cap: </td><td>" . $optical_drive['optical_drive_caption'] . "</td></tr>";
  } while ($optical_drive = mysql_fetch_array($result8));
} else {}
echo "<tr bgcolor=\"#F1F1F1\"><td>$l_key $l_des: </td><td>" . $keyboard['keyboard_caption'] . "</td></tr>";
echo "<tr><td>$l_mou $l_des: </td><td>" . $mouse['mouse_description'] . "</td></tr>";
echo "<tr bgcolor=\"#F1F1F1\"><td>$l_snd: </td><td>" . $sound['sound_name'] . "</td></tr>";
echo "<br />\n";
echo "<br />\n";
echo "</table>\n";
echo "</div>\n";
?>
