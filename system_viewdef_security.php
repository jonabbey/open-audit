<?php

$query_array=array("views"=>array("firewall_xpsp2"=>array(
                                                    "headline"=>__("XP SP2 Firewall"),
                                                    "sql"=>"SELECT * FROM system WHERE system_uuid = '" . $_GET["pc"] . "' AND system_timestamp = '".$GLOBAL["system_timestamp"]."' ",
                                                    "image"=>"./images/firewall_l.png",
                                                    "fields"=>array("10"=>array("name"=>"firewall_enabled_domain", "head"=>__("Domain: Firewall Enabled"),),
                                                                    "20"=>array("name"=>"firewall_disablenotifications_domain", "head"=>__("Domain: Notifications to User"),),
                                                                    "30"=>array("name"=>"firewall_donotallowexceptions_domain", "head"=>__("Domain: Exceptions Allowed"),),
                                                                    "40"=>array("name"=>"firewall_enabled_standard", "head"=>__("Standard: Firewall Enabled"),),
                                                                    "50"=>array("name"=>"firewall_disablenotifications_standard", "head"=>__("Standard: Notifications to User"),),
                                                                    "60"=>array("name"=>"firewall_donotallowexceptions_standard", "head"=>__("Standard: Exceptions Allowed"),),
                                                                    "60"=>array("name"=>"", "head"=>__(""),),
                                                                    "80"=>array("name"=>__("Port Exceptions"),
                                                                                "get"=>array("name"=>__("Click me!"),
                                                                                             "file"=>"list.php",
                                                                                             "title"=>__("Click me!"),
                                                                                             "var"=>array("pc"=>"%system_uuid",
                                                                                                          "view"=>"port_exceptions_for_system",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "90"=>array("name"=>__("Program Exceptions"),
                                                                                "get"=>array("name"=>__("Click me!"),
                                                                                             "file"=>"list.php",
                                                                                             "title"=>__("Click me!"),
                                                                                             "var"=>array("pc"=>"%system_uuid",
                                                                                                          "view"=>"program_exceptions_for_system",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                   ),
                                                    ),
                                   "firewall_other"=>array(
                                                    "headline"=>__("Other Firewalls"),
                                                    "sql"=>"SELECT * from software WHERE software_uuid = '" . $_GET["pc"] . "' AND software_timestamp = '".$GLOBAL["system_timestamp"]."' AND software_name LIKE '%ZoneAlarm%'",
                                                    "image"=>"./images/firewall_l.png",
                                                    "fields"=>array("10"=>array("name"=>"software_name", "head"=>__("Name"),),
                                                                   ),
                                                    ),
                                   "antivirus_xp"=>array(
                                                    "headline"=>__("In Windows registered Antivirus"),
                                                    "sql"=>"SELECT * from system WHERE system_uuid = '" . $_GET["pc"] . "' AND system_timestamp = '".$GLOBAL["system_timestamp"]."'  AND (virus_name <> '' OR virus_manufacturer <> '') ",
                                                    "image"=>"./images/antivirus_l.png",
                                                    "fields"=>array("10"=>array("name"=>"virus_name", "head"=>__("Name"),),
                                                                    "20"=>array("name"=>"virus_manufacturer", "head"=>__("Manufacturer"),),
                                                                    "30"=>array("name"=>"virus_version", "head"=>__("Version"),),
                                                                    "40"=>array("name"=>"virus_uptodate", "head"=>__("Up-to-date"),),
                                                                   ),
                                                    ),
                                   "antivirus_other"=>array(
                                                    "headline"=>__("Other Antivirus"),
                                                    "sql"=>"SELECT * FROM software WHERE software_uuid = '" . $_GET["pc"] . "' AND software_timestamp = '".$GLOBAL["system_timestamp"]."' AND software_name LIKE '%virus%' ",
                                                    "image"=>"./images/antivirus_l.png",
                                                    "fields"=>array("10"=>array("name"=>"software_name", "head"=>__("Name"),),
                                                                    "20"=>array("name"=>"software_publisher", "head"=>__("Manufacturer"),),
                                                                    "30"=>array("name"=>"software_version", "head"=>__("Version"),),
                                                                   ),
                                                    ),
                                   "nmap"=>array(
                                                    "headline"=>__("Nmap discovered on Audited PC"),
                                                    "sql"=>"SELECT * from nmap_ports WHERE nmap_other_id = '" . $_GET["pc"] . "' ",
                                                    "image"=>"./images/nmap_l.png",
                                                    "fields"=>array(
                                                                   "10"=>array("name"=>__("Port Scan"),
                                                                               "get"=>array("name"=>__("Click me!"),
                                                                                            "file"=>"list.php",
                                                                                            "title"=>__("Click me!"),
                                                                                            "var"=>array("pc"=>"%system_uuid",
                                                                                                         "view"=>"nmap_for_system",
                                                                                                        ),
                                                                                           ),
                                                                               ),
                                                                   ),
                                                    ),

                                 ),
                  );
?>
