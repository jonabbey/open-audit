<?php 
$page = "hardware";
include "include.php"; 
echo "<td valign=\"top\">\n";

echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
echo "<tr><td class=\"contenthead\" colspan=\"2\">Hardware for " . $name . "</td></tr>\n";



if (($sub == "hd") or ($sub == "all")){
  $SQL = "SELECT * FROM hard_drive WHERE hard_drive_uuid = '$pc' AND hard_drive_timestamp = '$timestamp' ORDER BY hard_drive_index";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
  do {
    echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/harddisk_l.png\" width=\"48\" height=\"48\" alt=\"\" />&nbsp;$l_fix #" . $myrow["hard_drive_index"] . "</td></tr>\n";
    echo "<tr><td>$l_mam:&nbsp;</td><td>" . $myrow["hard_drive_manufacturer"] . "</td><td></td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_typ:&nbsp;</td><td>" . $myrow["hard_drive_interface_type"] . "</td></tr>\n";
    echo "<tr><td>$l_mdl:&nbsp;</td><td>" . $myrow["hard_drive_model"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_pat:&nbsp;</td><td>" . $myrow["hard_drive_partitions"] . "</td></tr>\n";
    echo "<tr><td>$l_siz:&nbsp;</td><td>" . number_format($myrow["hard_drive_size"]) . " MB</td></tr>\n";
    if ($myrow["hard_drive_interface_type"] == "SCSI") { 
      echo "<tr bgcolor=\"$bg1\"><td>$l_scz:&nbsp;</td><td>" . $myrow["hard_drive_scsi_bus"] . "</td></tr>\n";
      echo "<tr><td>$l_scu:&nbsp;</td><td>" . $myrow["hard_drive_scsi_logical_unit"] . "</td></tr>";
      echo "<tr bgcolor=\"$bg1\"><td>$l_scp:&nbsp;</td><td>" . $myrow["hard_drive_scsi_port"] . "</td></tr\n";
    } else {}
    $SQL2 = "SELECT * FROM partition WHERE (partition_uuid = '$pc' && partition_timestamp = '$timestamp' && partition_disk_index = '" . $myrow["hard_drive_index"] . "') ORDER BY partition_caption ";
    $result2 = mysql_query($SQL2, $db);
    if ($myrow2 = mysql_fetch_array($result2)){ 
      do {
        $used = $myrow2["partition_size"] - $myrow2["partition_free_space"];
        echo "<tr><td class=\"contenthead\"><br /><img src=\"images/partition_l.png\" width=\"48\" height=\"48\" alt=\"\" />&nbsp;" . $myrow2["partition_device_id"] . "</td></tr>\n";
        echo "<tr><td>Drive Letter:&nbsp;</td><td>" . $myrow2["partition_caption"] . "</td></tr>\n";
        echo "<tr bgcolor=\"$bg1\"><td>Name:&nbsp;</td><td>" . $myrow2["partition_volume_name"] . "</td></tr>\n";
        echo "<tr><td>Boot Partition:&nbsp;</td><td>" . $myrow2["partition_boot_partition"] . "</td></tr>\n";
        echo "<tr bgcolor=\"$bg1\"><td>Bootable:&nbsp;</td><td>" . $myrow2["partition_bootable"] . "</td></tr>\n";
        echo "<tr><td>Size:&nbsp;</td><td>" . number_format($myrow2["partition_size"]) . " MB</td></tr>";
        echo "<tr bgcolor=\"$bg1\"><td>FileSystem:&nbsp;</td><td>" . $myrow2["partition_file_system"] . "</td></tr>\n";
        echo "<tr><td>Used:&nbsp;</td><td>" . number_format($used) . " MB</td></tr>";
        echo "<tr bgcolor=\"$bg1\"><td>Free:&nbsp;</td><td>" . number_format($myrow2["partition_free_space"]) . " MB</td></tr>\n";
      } while ($myrow2 = mysql_fetch_array($result2)); 
    } else {}
  } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


if (($sub == "sc") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM scsi_controller WHERE scsi_controller_uuid = '$pc' AND scsi_controller_timestamp = '$timestamp' ORDER BY scsi_controller_device_id";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      $opt_count = $opt_count + 1;
      echo "<tr><td colspan=\"2\" class=\"contenthead\"><br /><img src=\"images/scsi_l.png\" width=\"48\" height=\"48\" alt=\"\" />&nbsp;$l_scy #$opt_count</td><td></td></tr>\n";
      echo "<tr><td width=\"200\">$l_cap:</td><td>" . $myrow["scsi_controller_caption"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_man:</td><td>" . $myrow['scsi_controller_manufacturer'] . "</td></tr>";
      $sql2 = "SELECT tape_drive_caption FROM tape_drive, scsi_device WHERE scsi_device_uuid = '$pc' AND scsi_device_device = tape_drive_device_id ";
      $sql2 .= "AND scsi_device_controller = '" . STR_REPLACE("\\", "\\\\", $myrow["scsi_controller_device_id"]) . "'";
      $result2 = mysql_query($sql2, $db);
      if ($myrow2 = mysql_fetch_array($result2)){
        do {
          echo "<tr><td>$l_att: </td><td>$l_tdv: " . $myrow2["tape_drive_caption"] . "</td></tr>";
        } while ($myrow2 = mysql_fetch_array($result2));
      } else {}
      $sql2 = "SELECT hard_drive_caption, hard_drive_index FROM hard_drive, scsi_device WHERE scsi_device_uuid = '$pc' AND scsi_device_device = hard_drive_pnpid ";
      $sql2 .= "AND scsi_device_controller = '" . STR_REPLACE("\\", "\\\\", $myrow["scsi_controller_device_id"]) . "'";
      $result2 = mysql_query($sql2, $db);
      if ($myrow2 = mysql_fetch_array($result2)){
        do {
          echo "<tr><td>$l_att: </td><td>$l_fix #" . $myrow2["hard_drive_index"] . ": " . $myrow2["hard_drive_caption"] . "</td></tr>";
        } while ($myrow2 = mysql_fetch_array($result2));
      } else {}
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


if (($sub == "od") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM optical_drive WHERE optical_drive_uuid = '$pc' AND optical_drive_timestamp = '$timestamp' ORDER BY optical_drive_drive";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      $opt_count = $opt_count + 1;
      echo "<tr><td colspan=\"2\" class=\"contenthead\"><br /><img src=\"images/optical_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_ode #$opt_count</td><td></td></tr>\n";
      echo "<tr><td width=\"200\">$l_drv:</td><td>" . $myrow["optical_drive_drive"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_mdl:</td><td>" . $myrow['optical_drive_caption'] . "</td></tr>";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


if (($sub == "fd") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM floppy WHERE floppy_uuid = '$pc' AND floppy_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      $opt_count = $opt_count + 1;
      echo "<tr><td colspan=\"2\" class=\"contenthead\"><br /><img src=\"images/floppy_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_fde #$opt_count</td></tr>\n";
      echo "<tr><td>$l_cap:</td><td>" . $myrow["floppy_caption"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_des:</td><td>" . $myrow["floppy_description"] . "</td></tr>\n";
      echo "<tr><td>$l_man:</td><td>" . $myrow["floppy_$l_man"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


if (($sub == "td") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM tape_drive WHERE tape_drive_uuid = '$pc' AND tape_drive_timestamp = '$timestamp' ORDER BY tape_drive_id";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
    do {
      $opt_count = $opt_count + 1;
      echo "<tr><td class=\"contenthead\"><br /><img src=\"images/tape_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_tdv #$opt_count</td><td></td></tr>\n";
      echo "<tr><td>$l_man:</td><td>" . $myrow["tape_drive_$l_man"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_cap:</td><td>" . $myrow["tape_drive_caption"] . "</td></tr>\n";
      echo "<tr><td>$l_des:</td><td>" . $myrow["tape_drive_description"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


if (($sub == "pb") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM processor WHERE processor_uuid = '$pc' AND processor_timestamp = '$timestamp' ORDER BY processor_id";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      $opt_count = $opt_count + 1;
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/processor_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_pro #$opt_count</td><td></td></tr>\n";
      echo "<tr><td>$l_man:</td><td>" . $myrow["processor_manufacturer"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>Type:</td><td>" . $myrow["processor_caption"] . "</td></tr>\n";
      echo "<tr><td>Description:</td><td>" . $myrow["processor_name"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>Max Speed:</td><td>" . $myrow["processor_max_clock_speed"] . "</td></tr>\n";
      echo "<tr><td>Socket Designation:</td><td>" . $myrow["processor_socket_designation"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>Current Speed:</td><td>" . $myrow["processor_current_clock_speed"] . "</td></tr>\n";
      echo "<tr><td>External Clock:</td><td>" . $myrow["processor_ext_clock"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>Current Voltage:</td><td>" . $myrow["processor_current_voltage"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    $SQL = "SELECT * FROM bios WHERE bios_uuid = '$pc' AND bios_timestamp = '$timestamp'";
    $result = mysql_query($SQL, $db);
    if ($myrow = mysql_fetch_array($result)){
      do {
        echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/bios_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_bio</td><td></td></tr>\n";
        echo "<tr><td>$l_des:</td><td>" . $myrow["bios_description"] . "</td></tr>\n";
        echo "<tr bgcolor=\"$bg1\"><td>$l_man:</td><td>" . $myrow["bios_manufacturer"] . "</td></tr>\n";
        echo "<tr><td>Serial:</td><td>" . $myrow["bios_serial_number"] . "</td></tr>\n";
        echo "<tr bgcolor=\"$bg1\"><td>$l_ver:</td><td>" . $myrow["bios_version"] . "</td></tr>\n";
        echo "<tr><td>SM $l_ver:</td><td>" . $myrow["bios_sm_bios_version"] . "</td></tr>\n";
        echo "<tr bgcolor=\"$bg1\"><td>$l_ast:</td><td>" . $myrow["bios_asset_tag"] . "</td></tr>\n";
      } while ($myrow = mysql_fetch_array($result));
    } else {}
  } else {}
} else {}

if (($sub == "me") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM memory WHERE memory_uuid = '$pc' AND memory_timestamp = '$timestamp' ORDER BY memory_id";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      $opt_count = $opt_count + 1;
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/memory_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_men #$opt_count</td><td></td></tr>\n";
      echo "<tr><td>$l_mel:</td><td>" . $myrow["memory_bank"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_for:</td><td>" . $myrow["memory_form_factor"] . "</td></tr>\n";
      echo "<tr><td>$l_typ:</td><td>" . $myrow["memory_type"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_typ $l_dtt:</td><td>" . $myrow["memory_detail"] . "</td></tr>\n";
      echo "<tr><td>$l_spd:</td><td>" . $myrow["memory_speed"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_caa:</td><td>" . $myrow["memory_capacity"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  }
} else {}

if (($sub == "na") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM network_card WHERE net_uuid = '$pc' AND net_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
    $opt_count = $opt_count + 1;
    echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/network_device_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_nwa #$opt_count</td></tr>\n";
    echo "<tr><td>$l_typ:</td><td>" . $myrow["net_adapter_type"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_des:</td><td>" . $myrow["net_description"] . "</td></tr>\n";
    echo "<tr><td>$l_man:</td><td>" . $myrow["net_manufacturer"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_mac:</td><td>" . $myrow["net_mac_address"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


if (($sub == "vm") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM video WHERE video_uuid = '$pc' AND video_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      $opt_count = $opt_count + 1;
      $col_depth = "";
      if ($myrow["video_current_number_colours"] == "256") { $col_depth = "8 bit"; } else {}
      if ($myrow["video_current_number_colours"] == "65536") { $col_depth = "16 bit"; } else {}
      if ($myrow["video_current_number_colours"] == "16777216") { $col_depth = "24 bit"; } else {}
      if ($myrow["video_current_number_colours"] == "4294967296") { $col_depth = "32 bit"; } else {}
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/video_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_vid #$opt_count</td></tr>\n";
      echo "<tr><td>$l_des:</td><td>" . $myrow["video_caption"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_mem:</td><td>" . $myrow["video_adapter_ram"] . " MB</td></tr>\n";
      echo "<tr><td>$l_crr:</td><td>" . return_unknown($myrow["video_current_horizontal_res"]) . " x " . return_unknown($myrow["video_current_vertical_res"]) . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_crd:</td><td>" . return_unknown($col_depth) . "</td></tr>\n";
      echo "<tr><td>$l_ref:</td><td>" . return_unknown($myrow["video_current_refresh_rate"]) . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_dri:</td><td>" . $myrow["video_driver_version"] . "</td></tr>\n";
      echo "<tr><td>$l_drt:</td><td>" . $myrow["video_driver_date"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
  $SQL = "SELECT * FROM monitor WHERE monitor_uuid = '$pc' AND monitor_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/display_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_moo</td></tr>\n";
      echo "<tr><td>$l_moo $l_man:</td><td>" . $myrow["monitor_manufacturer"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_mdl:</td><td>" . $myrow["monitor_model"] . "</td></tr>\n";
      echo "<tr><td>$l_srl:</td><td>" . $myrow["monitor_serial"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_mdt:</td><td>" . $myrow["monitor_manufacture_date"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}

if (($sub == "so") or ($sub == "all")){
  $SQL = "SELECT * FROM sound WHERE sound_uuid = '$pc' AND sound_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  if (($myrow = mysql_fetch_array($result)) and ($myrow["sound_name"] <> "")){
    do {
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/audio_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_snd</td><td></td></tr>\n";
      echo "<tr><td>$l_des:</td><td>" . $myrow["sound_name"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_man:</td><td>" . $myrow["sound_manufacturer"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


if (($sub == "km") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM keyboard WHERE keyboard_uuid = '$pc' AND keyboard_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result) and $myrow["keyboard_caption"] <> ""){
    echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/keyboard_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_key</td></tr>";
    do {
      echo "<tr><td>$l_typ:</td><td>" . $myrow["keyboard_caption"] . "</td></tr>";
      echo "<tr bgcolor=\"$bg1\"><td>$l_des:</td><td>" . $myrow["keyboard_description"] . "</td></tr>";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
  $SQL = "SELECT * FROM mouse WHERE mouse_uuid = '$pc' AND mouse_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      $opt_count = $opt_count + 1;
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/mouse_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_mou #" . $opt_count . "</td></tr>\n";
      echo "<tr><td>$l_des:</td><td>" . $myrow["mouse_description"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_but:</td><td>" . $myrow["mouse_number_of_buttons"] . "</td></tr>\n";
      echo "<tr><td>$l_coo:</td><td>" . $myrow["mouse_port"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


if (($sub == "mo") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM modem WHERE modem_uuid = '$pc' AND modem_timestamp = '$timestamp' ORDER BY modem_attached_to";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
    $opt_count = $opt_count + 1;
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/modem_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_mod #$opt_count</td><td></td></tr>\n";
      echo "<tr><td>$l_typ:</td><td>" . $myrow["modem_device_type"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_des:</td><td>" . $myrow["modem_description"] . "</td></tr>\n";
      echo "<tr><td>$l_por:</td><td>" . $myrow["modem_attached_to"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_cou:</td><td>" . $myrow["modem_country_selected"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


if (($sub == "ba") or ($sub == "all")){
  $SQL = "SELECT * FROM battery WHERE battery_uuid = '$pc' AND battery_timestamp = '$timestamp'";
  $result = mysql_query($SQL, $db);
  if (($myrow = mysql_fetch_array($result)) and ($myrow["battery_description"] <> "")){
    do {
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/battery_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_bat</td></tr>\n";
      echo "<tr><td>$l_des:</td><td>" . $myrow["battery_description"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_dev:</td><td>" . $myrow["battery_device_id"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}



if (($sub == "us") or ($sub == "all")){
  $SQL = "SELECT * FROM usb WHERE usb_uuid = '$pc' AND usb_timestamp = '$timestamp' AND usb_manufacturer <> '(Standard system devices)' AND usb_caption <> 'HID-compliant consumer control device' AND usb_manufacturer <> '(Standard USB Host Controller)' ORDER BY usb_caption";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/";
      if (substr_count(strtolower($myrow["usb_caption"]), "ipod") > 0 ) {
        echo "ipod_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - iPod</td></tr>\n";
      } else {
        if (substr_count(strtolower($myrow["usb_caption"]), "mouse") > 0 ) {
          echo "mouse_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - Mouse</td></tr>\n";
        } else {      
          if ((substr_count(strtolower($myrow["usb_caption"]), "keyboard") > 0 ) OR 
            (substr_count(strtolower($myrow["usb_caption"]), "internet keys usb") > 0 )) {
            echo "keyboard_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - Keyboard</td></tr>\n";
          } else {      
            if ((substr_count(strtolower($myrow["usb_caption"]), "scanner") > 0 ) OR 
              (substr_count(strtolower($myrow["usb_caption"]), "scanjet") > 0 )) {
              echo "usb_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - Scanner</td></tr>\n";
            } else {    
              if ((substr_count(strtolower($myrow["usb_caption"]), "printer") > 0 ) OR 
                  (substr_count(strtolower($myrow["usb_caption"]), "laserjet") > 0) OR 
                  (substr_count(strtolower($myrow["usb_caption"]), "kyocera mita fs") > 0 ) OR 
                  (substr_count(strtolower($myrow["usb_caption"]), "kyocera fs") > 0 )){
                  echo "printer_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - Printer</td></tr>\n";
              } else {
                if (substr_count(strtolower($myrow["usb_caption"]), "camera") > 0 ) {
                  echo "camera_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - Camera</td></tr>\n";
                } else {
                    if ((substr_count(strtolower($myrow["usb_caption"]), "disk") > 0 ) OR (substr_count(strtolower($myrow["usb_description"]), "disk drive") > 0 )){
                      echo "harddisk_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - Disk</td></tr>\n";
                    } else {
                      if (substr_count(strtolower($myrow["usb_caption"]), "ipaq") > 0 ) {
                        echo "laptop_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - PDA</td></tr>\n";
                      } else {
                        if ((substr_count(strtolower($myrow["usb_caption"]), "modem") > 0 ) OR 
                            (substr_count(strtolower($myrow["usb_caption"]), "netcomm roadster") > 0 )) {
                          echo "modem_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - Modem</td></tr>\n";
                        } else {
                          if (substr_count(strtolower($myrow["usb_caption"]), "tv") > 0 ) {
                            echo "tv_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - TV Capture</td></tr>\n";
                          } else {
                            if (substr_count(strtolower($myrow["usb_description"]), "cd-rom") > 0 ) {
                              echo "optical_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - CD / DVD Drive</td></tr>\n";
                            } else {
                              if (substr_count(strtolower($myrow["usb_description"]), "audio") > 0 ) {
                                echo "audio_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - Audio</td></tr>\n";
                              } else {
                                if (substr_count(strtolower($myrow["usb_description"]), "touch screen") > 0 ) {
                                  echo "display_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - Touch Screen</td></tr>\n";
                                } else {
                                  if (substr_count(strtolower($myrow["usb_description"]), "blackberry") > 0 ) {
                                    echo "blackberry_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc - Blackberry</td></tr>\n";
                                  } else {
                                    echo "usb_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usc</td></tr>\n";
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
        
        echo "<tr><td>$l_cap:</td><td>" . $myrow["usb_caption"] . "</td></tr>\n";
        echo "<tr bgcolor=\"$bg1\"><td>$l_des:</td><td>" . $myrow["usb_description"] . "</td></tr>\n";
        echo "<tr><td>$l_man:</td><td>" . $myrow["usb_manufacturer"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


if (($sub == "pr") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM printer WHERE printer_uuid = '$pc' AND printer_timestamp = '$timestamp' AND printer_system_name = '" . $name . "' AND printer_port_name NOT LIKE '%IP%' AND printer_port_name NOT LIKE '\\\\%'";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
      $opt_count = $opt_count + 1;
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/printer_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_prm #$opt_count</td></tr>\n";
      echo "<tr><td>$l_des:</td><td>" . $myrow["printer_caption"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_loc:</td><td>" . $myrow["printer_location"] . "</td></tr>\n";
      echo "<tr><td>$l_por:</td><td>" . $myrow["printer_port_name"] . "</td></tr>\n";
      echo "<tr bgcolor=\"$bg1\"><td>$l_she:&nbsp;</td><td>" . $myrow["printer_shared"] . "</td></tr>\n";
      echo "<tr><td>$l_shf:</td><td>" . $myrow["printer_share_name"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}


echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
