<?php

$query_array=array("views"=>array("summary"=>array(
                                                    "headline"=>__("Summary"),
                                                    "sql"=>"SELECT * FROM system
                                                            LEFT JOIN network_card ON (system_uuid=net_uuid AND system_timestamp=net_timestamp)
                                                            WHERE system_uuid = '" . $_GET["pc"] . "' AND  system_timestamp = '".$GLOBAL["system_timestamp"]."'
                                                            LIMIT 0,1",
                                                    "image"=>"images/os_l.png",
                                                    "fields"=>array("10"=>array("name"=>"net_uuid",
                                                                                "show"=>"n",
                                                                               ),
                                                                    "20"=>array("name"=>"system_name", "head"=>__("System Name"),),
                                                                    "30"=>array("name"=>"system_description", "head"=>__("Description"),),
                                                                    "40"=>array("name"=>"net_domain_role", "head"=>__("Domain Role"),),
                                                                    "50"=>array("name"=>"system_registered_user", "head"=>__("Registered User"),),
                                                                    "60"=>array("name"=>"net_user_name", "head"=>__("Current User"),),
                                                                    "70"=>array("name"=>"net_domain", "head"=>__("Domain"),),
                                                                    "80"=>array("name"=>"system_system_type", "head"=>__("Chassis Type"),),
                                                                    "90"=>array("name"=>"system_model", "head"=>__("Model #"),),
                                                                    "100"=>array("name"=>"system_id_number", "head"=>__("Serial #"),),
                                                                    "110"=>array("name"=>"system_vendor", "head"=>__("Manufacturer"),),
                                                                    "120"=>array("name"=>"system_os_name", "head"=>__("Operating System"),),
                                                                    "130"=>array("name"=>"system_build_number", "head"=>__("Build Number"),),
                                                                    "140"=>array("name"=>"system_uuid", "head"=>__("UUID"),),
                                                                    "150"=>array("name"=>"date_system_install", "head"=>__("OS Installed Date"),),
                                                                    "160"=>array("name"=>"net_ip_address", "head"=>__("IP"),),
                                                                    "170"=>array("name"=>"net_ip_subnet", "head"=>__("Subnet"),),
                                                                    "180"=>array("name"=>"net_dhcp_server", "head"=>__("DHCP"),),
                                                                    "190"=>array("name"=>"system_first_timestamp", "head"=>__("Date First Audited"),),
                                                                    "200"=>array("name"=>"system_timestamp", "head"=>__("Date Last Audited"),),
                                                                    "210"=>array("name"=>"system_memory", "head"=>__("Memory"),),
                                                                   ),
                                                ),

                                   "manual"=>array(
                                                    "headline"=>__("Manual Data"),
                                                    "sql"=>"SELECT * FROM system_man WHERE system_man_uuid = '" . $_GET["pc"] . "' ",
                                                    "image"=>"images/notes_l.png",
                                                    "edit"=>"y",
                                                    "fields"=>array("10"=>array("name"=>"system_man_id",
                                                                                "show"=>"n",
                                                                               ),
                                                                    "20"=>array("name"=>"system_man_location", "head"=>__("Location"), "edit"=>"y",),
                                                                    "30"=>array("name"=>"system_man_date_of_purchase", "head"=>__("Date of Purchase"), "edit"=>"y",),
                                                                    "40"=>array("name"=>"system_man_value", "head"=>__("Dollar Value"), "edit"=>"y",),
                                                                    "50"=>array("name"=>"system_man_serial_number", "head"=>__("Asset Tag"), "edit"=>"y",),
                                                                    "60"=>array("name"=>"system_man_description", "head"=>__("Description"), "edit"=>"y","edit_type"=>"textarea",),
                                                                   ),
                                                ),
                                   "management"=>array(
                                                    "headline"=>__("Remote Management"),
                                                    "sql"=>"SELECT system_name FROM system WHERE system_uuid = '" . $_GET["pc"] . "' AND  system_timestamp = '".$GLOBAL["system_timestamp"]."'",
                                                    "image"=>"./images/display_l.png",
                                                    "print"=>"n",
                                                    "fields"=>array("10"=>array("name"=>"Explorer C",
                                                                                "head"=>__("Explorer C"),
                                                                                "get"=>array("head"=>"Explorer"." \\\\Client\\C\$-Session",
                                                                                             "file"=>"launch.php",
                                                                                             "title"=>__("Explorer"),
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "application"=>"explorer_c",
                                                                                                          "ext"=>"vbs",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "20"=>array("name"=>"RDP",
                                                                                "head"=>__("RDP"),
                                                                                "get"=>array("head"=>"RDP-Session",
                                                                                             "file"=>"launch.php",
                                                                                             "title"=>__("RDP-Session"),
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "application"=>"rdp",
                                                                                                          "ext"=>"rdp",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "30"=>array("name"=>"VNC",
                                                                                "head"=>__("VNC"),
                                                                                "get"=>array("head"=>"VNC-Session",
                                                                                             "file"=>"launch.php",
                                                                                             "title"=>__("VNC-Session"),
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "application"=>"vnc",
                                                                                                          "ext"=>"vnc",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "40"=>array("name"=>"HTTP",
                                                                                "head"=>__("HTTP"),
                                                                                "get"=>array("head"=>"HTTP-Session",
                                                                                             "file"=>"launch.php",
                                                                                             "title"=>__("HTTP-Session"),
                                                                                             "target"=>"_BLANK",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "application"=>"http",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "50"=>array("name"=>"HTTPS",
                                                                                "head"=>__("HTTPS"),
                                                                                "get"=>array("head"=>"HTTPS-Session",
                                                                                             "file"=>"launch.php",
                                                                                             "title"=>__("HTTPS-Session"),
                                                                                             "target"=>"_BLANK",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "application"=>"https",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "60"=>array("name"=>"FTP",
                                                                                "head"=>__("FTP"),
                                                                                "get"=>array("head"=>"FTP-Session",
                                                                                             "file"=>"launch.php",
                                                                                             "title"=>__("FTP-Session"),
                                                                                             "target"=>"_BLANK",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "application"=>"ftp",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "70"=>array("name"=>"Reboot",
                                                                                "head"=>__("Reboot"),
                                                                                "get"=>array("head"=>"Reboot",
                                                                                             "file"=>"launch.php",
                                                                                             "title"=>__("Reboot"),
                                                                                             "target"=>"_BLANK",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "application"=>"Reboot",
                                                                                                          "ext"=>"vbs",
                                                                                                         ),
                                                                                            ),
                                                                                ),

                                                                   ),
                                                    ),

                                 ),
                  );
?>
