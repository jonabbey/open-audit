<?php

$query_array=array("name"=>__("OS Summary"),
                   "image"=>"images/os_l.png",
                   "views"=>array( "os"=>array(
                                                    "headline"=>__("OS"),
                                                    "sql"=>"SELECT * FROM system WHERE system_uuid = '" . $_GET["pc"] . "' AND system_timestamp = '".$GLOBAL["system_timestamp"]."' ",
                                                    "image"=>"images/os_l.png",
                                                    "fields"=>array("10"=>array("name"=>"system_os_name", "head"=>__("Operating System"),),
                                                                    "20"=>array("name"=>"system_registered_user", "head"=>__("Registered User"),),
                                                                    "30"=>array("name"=>"system_organisation", "head"=>__("Registered Organisation"),),
                                                                    "40"=>array("name"=>"system_build_number", "head"=>__("OS Version"),),
                                                                    "50"=>array("name"=>"system_service_pack", "head"=>__("Service Pack"),),
                                                                    "60"=>array("name"=>"system_windows_directory", "head"=>__("Windows Directory"),),
                                                                    "70"=>array("name"=>"system_serial_number", "head"=>__("Windows Serial"),),
                                                                    "80"=>array("name"=>"date_system_install", "head"=>__("OS Installed On"),),
                                                                    "90"=>array("name"=>"system_language", "head"=>__("Language"),),
                                                                    "100"=>array("name"=>"time_caption", "head"=>__("Time Zone"),),
                                                                    "110"=>array("name"=>"time_daylight", "head"=>__("Daylight Savings Zone"),),
                                                                   ),
                                                    ),
                                   "software"=>array(
                                                    "headline"=>__("Software"),
                                                    "sql"=>"SELECT software_name, software_version, software_publisher, software_url
                                                            FROM software, system
                                                            WHERE system_uuid = '".$_REQUEST["pc"]."' AND software_uuid = system_uuid AND software_timestamp = system_timestamp
                                                            AND (software_name LIKE 'Internet%' OR software_name LIKE 'DirectX%' OR software_name = 'Windows Media Player')
                                                              ",
                                                    "image"=>"images/software_l.png",
                                                    "fields"=>array("10"=>array("name"=>"software_name", "head"=>__("Name"),),
                                                                    "20"=>array("name"=>"software_version", "head"=>__("Version"),),
                                                                   ),
                                                    ),
                                   "shares"=>array(
                                                    "headline"=>__("Shared Drives"),
                                                    "sql"=>"SELECT * FROM shares WHERE shares_uuid = '".$_REQUEST["pc"]."' AND shares_timestamp = '".$GLOBAL["system_timestamp"]."' ORDER BY shares_path, shares_name",
                                                    "image"=>"images/shared_drive_l.png",
                                                    "fields"=>array("10"=>array("name"=>"shares_name", "head"=>__("Name"),),
                                                                    "20"=>array("name"=>"shares_caption", "head"=>__("Description"),),
                                                                    "30"=>array("name"=>"shares_path", "head"=>__("Local Path"),),
                                                                   ),
                                                    ),
                                   "network_card"=>array(
                                                    "headline"=>__("Network Card"),
                                                    "sql"=>"SELECT * FROM network_card WHERE net_uuid = '" . $_GET["pc"] . "' AND net_timestamp = '".$GLOBAL["system_timestamp"]."'",
                                                    "image"=>"images/network_device_l.png",
                                                    "fields"=>array("10"=>array("name"=>"net_adapter_type", "head"=>__("Type"),),
                                                                    "20"=>array("name"=>"net_description", "head"=>__("Description"),),
                                                                    "30"=>array("name"=>"net_manufacturer", "head"=>__("Manufacturer"),),
                                                                    "40"=>array("name"=>"net_mac_address", "head"=>__("MAC Address"),),
                                                                    "50"=>array("name"=>"net_ip_address", "head"=>__("IP"),),
                                                                    "60"=>array("name"=>"net_ip_subnet", "head"=>__("Subnet"),),
                                                                    "70"=>array("name"=>"net_dns_server", "head"=>__("Primary DNS"),),
                                                                    "90"=>array("name"=>"net_dns_server_2", "head"=>__("Secondary DNS"),),
                                                                    "100"=>array("name"=>"net_wins_primary", "head"=>__("Primary WINS"),),
                                                                    "110"=>array("name"=>"net_wins_secondary", "head"=>__("Secondary WINS"),),
                                                                    "120"=>array("name"=>"net_dhcp_enabled", "head"=>__("DHCP"),),
                                                                    "130"=>array("name"=>"net_dhcp_server", "head"=>__("DHCP Server"),),
                                                                   ),
                                                    ),
                                ),
                  );
?>
