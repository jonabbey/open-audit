<?php
    
include "include_config.php";

    if ($_GET['confirm']=1) {

    $link = mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
    mysql_select_db("$mysql_database") or die("Could not select database");

    $query = "DELETE FROM battery";
    $result = mysql_query($query)  or die("Query failed at insert stage. browser_helper_objects");

    $query = "DELETE FROM bios";
    $result = mysql_query($query)  or die("Query failed at insert stage. browser_bios");

    $query = "DELETE FROM browser_helper_objects";
    $result = mysql_query($query)  or die("Query failed at insert stage. browser_helper_objects");
    
    $query = "DELETE FROM firewall_auth_app";
    $result = mysql_query($query)  or die("Query failed at insert stage. firewall_auth_app");    
    
    $query = "DELETE FROM firewall_ports";
    $result = mysql_query($query)  or die("Query failed at insert stage. firewall_ports");
    
    $query = "DELETE FROM firewire";
    $result = mysql_query($query)  or die("Query failed at insert stage. firewire");

    $query = "DELETE FROM floppy";
    $result = mysql_query($query)  or die("Query failed at insert stage. floppy");

    $query = "DELETE FROM graphs_disk";
    $result = mysql_query($query)  or die("Query failed at insert stage. graphs_disk");

    $query = "DELETE FROM groups";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups");

    $query = "DELETE FROM groups_details";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups_details");

    $query = "DELETE FROM group_members";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups");

    $query = "DELETE FROM hard_drive";
    $result = mysql_query($query)  or die("Query failed at insert stage. hard_drive");

    $query = "DELETE FROM hotfix";
    $result = mysql_query($query)  or die("Query failed at insert stage. hotfixes");

    $query = "DELETE FROM iis";
    $result = mysql_query($query)  or die("Query failed at insert stage. iis");

    $query = "DELETE FROM iis_ip";
    $result = mysql_query($query)  or die("Query failed at insert stage. iis_ip");

    $query = "DELETE FROM iis_vd";
    $result = mysql_query($query)  or die("Query failed at insert stage. iis_vd");

    $query = "DELETE FROM invoice";
    $result = mysql_query($query)  or die("Query failed at insert stage. invoice");

    $query = "DELETE FROM keyboard";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups");

    $query = "DELETE FROM manual_software";
    $result = mysql_query($query)  or die("Query failed at insert stage. manual_software");

    $query = "DELETE FROM mapped";
    $result = mysql_query($query)  or die("Query failed at insert stage. mapped");

    $query = "DELETE FROM media";
    $result = mysql_query($query)  or die("Query failed at insert stage. media");

    $query = "DELETE FROM memory";
    $result = mysql_query($query)  or die("Query failed at insert stage. memory");

    $query = "DELETE FROM modem";
    $result = mysql_query($query)  or die("Query failed at insert stage. modem");

    $query = "DELETE FROM monitor";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups");

    $query = "DELETE FROM mouse";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups");

    $query = "DELETE FROM ms_keys";
    $result = mysql_query($query)  or die("Query failed at insert stage. ms_keys");

    $query = "DELETE FROM network_card";
    $result = mysql_query($query)  or die("Query failed at insert stage. network_card");

    $query = "DELETE FROM nmap_ports";
    $result = mysql_query($query)  or die("Query failed at insert stage. nmap_other_ports");

    $query = "DELETE FROM notes";
    $result = mysql_query($query)  or die("Query failed at insert stage. notes");

    $query = "DELETE FROM optical_drive";
    $result = mysql_query($query)  or die("Query failed at insert stage. optical_drive");

    $query = "DELETE FROM partition";
    $result = mysql_query($query)  or die("Query failed at insert stage. partition");

    $query = "DELETE FROM passwords";
    $result = mysql_query($query)  or die("Query failed at insert stage. passwords");

    $query = "DELETE FROM printer";
    $result = mysql_query($query)  or die("Query failed at insert stage. printer");

    $query = "DELETE FROM processor";
    $result = mysql_query($query)  or die("Query failed at insert stage. processor");

    $query = "DELETE FROM scsi_controller";
    $result = mysql_query($query)  or die("Query failed at insert stage. scsi_controller");

    $query = "DELETE FROM scsi_device";
    $result = mysql_query($query)  or die("Query failed at insert stage. scsi_device");

    $query = "DELETE FROM service";
    $result = mysql_query($query)  or die("Query failed at insert stage. services");

    $query = "DELETE FROM service_details";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups");

    $query = "DELETE FROM shares";
    $result = mysql_query($query)  or die("Query failed at insert stage. shares");

    $query = "DELETE FROM software";
    $result = mysql_query($query)  or die("Query failed at insert stage. software");

    $query = "DELETE FROM sound";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups");

    $query = "DELETE FROM startup";
    $result = mysql_query($query)  or die("Query failed at insert stage. startup");

    $query = "DELETE FROM system";
    $result = mysql_query($query)  or die("Query failed at insert stage. system");

    $query = "DELETE FROM system_audits";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups");

    $query = "DELETE FROM system_change";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups");

    $query = "DELETE FROM system_change_log";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups");

    $query = "DELETE FROM system_man";
    $result = mysql_query($query)  or die("Query failed at insert stage. system_man");

    $query = "DELETE FROM system_security";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups");

    $query = "DELETE FROM system_security_bulletins";
    $result = mysql_query($query)  or die("Query failed at insert stage. groups");

    $query = "DELETE FROM tape_drive";
    $result = mysql_query($query)  or die("Query failed at insert stage. tape_drive");

    $query = "DELETE FROM usb";
    $result = mysql_query($query)  or die("Query failed at insert stage. usb");

    $query = "DELETE FROM users";
    $result = mysql_query($query)  or die("Query failed at insert stage. users");

    $query = "DELETE FROM users_detail";
    $result = mysql_query($query)  or die("Query failed at insert stage. users_detail");

    $query = "DELETE FROM video";
    $result = mysql_query($query)  or die("Query failed at insert stage. video");    
    
    header("Location: index.php");
    } else {}

?>
