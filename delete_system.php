<?php

include "include_config.php";

if (isset($_GET['pc'])) {
  $link = mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
  mysql_select_db("$mysql_database") or die("Could not select database");
  $query = "select system_name from system where system_uuid='" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at retrieve system name stage.");
  $myrow = mysql_fetch_array($result);
  $name = $myrow['system_name'];

  $query = "DELETE FROM battery WHERE battery_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. battery");

  $query = "DELETE FROM bios WHERE bios_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. browser_helper_objects");

  $query = "DELETE FROM browser_helper_objects WHERE bho_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. browser_helper_objects");

  $query = "DELETE FROM firewall_auth_app WHERE firewall_app_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. firewall_auth_app");

  $query = "DELETE FROM firewall_ports WHERE port_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. firewall_ports");

  $query = "DELETE FROM firewire WHERE fw_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. firewire");

  $query = "DELETE FROM floppy WHERE floppy_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. floppy");

  $query = "DELETE FROM graphs_disk WHERE disk_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. graphs_disk");

  $query = "DELETE FROM groups WHERE groups_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. groups");

  $query = "DELETE FROM hard_drive WHERE hard_drive_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. hard_drive");

  $query = "DELETE FROM iis WHERE iis_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. iis");

  $query = "DELETE FROM iis_ip WHERE iis_ip_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. iis_ip");

  $query = "DELETE FROM iis_vd WHERE iis_vd_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. iis_vd");

  $query = "DELETE FROM invoice WHERE invoice_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. invoice");

  $query = "DELETE FROM keyboard WHERE keyboard_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. keyboard");

  $query = "DELETE FROM manual_software WHERE man_soft_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. manual_software");

  $query = "DELETE FROM mapped WHERE mapped_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. mapped");

  $query = "DELETE FROM media WHERE media_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. media");

  $query = "DELETE FROM memory WHERE memory_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. memory");

  $query = "DELETE FROM modem WHERE modem_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. modem");

  $query = "DELETE FROM monitor WHERE monitor_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. modem");

  $query = "DELETE FROM mouse WHERE mouse_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. modem");

  $query = "DELETE FROM ms_keys WHERE ms_keys_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. ms_keys");

  $query = "DELETE FROM network_card WHERE net_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. network_card");

  $query = "DELETE FROM nmap_ports WHERE nmap_other_id = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. nmap_other_ports");

  $query = "DELETE FROM notes WHERE notes_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. notes");

  $query = "DELETE FROM optical_drive WHERE optical_drive_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. optical_drive");

  $query = "DELETE FROM partition WHERE partition_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. partition");

  $query = "DELETE FROM passwords WHERE passwords_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. passwords");

  $query = "DELETE FROM processor WHERE processor_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. processor");

  $query = "DELETE FROM scsi_controller WHERE scsi_controller_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. scsi_controller");

  $query = "DELETE FROM scsi_device WHERE scsi_device_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. scsi_device");

  $query = "DELETE FROM service WHERE service_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. services");

  $query = "DELETE FROM shares WHERE shares_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. shares");

  $query = "DELETE FROM software WHERE software_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. software");

  $query = "DELETE FROM sound WHERE sound_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. sound");

  $query = "DELETE FROM startup WHERE startup_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. startup");

  $query = "DELETE FROM system WHERE system_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. system");

  $query = "DELETE FROM system_audits WHERE system_audits_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. system_audits");

  $query = "DELETE FROM system_man WHERE system_man_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. system_man");

  $query = "DELETE FROM system_security WHERE ss_uuid = '" . $name . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. system_security");

  $query = "DELETE FROM tape_drive WHERE tape_drive_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. tape_drive");

  $query = "DELETE FROM usb WHERE usb_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. usb");

  $query = "DELETE FROM users WHERE users_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. users");

  $query = "DELETE FROM video WHERE video_uuid = '" . mysql_real_escape_string($_GET['pc']) . "'";
  $result = mysql_query($query)  or die("Query failed at insert stage. video");

  header("Location: ./list.php?view=delete_system");
} else { }
