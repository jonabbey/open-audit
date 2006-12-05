<?php
include "include_config.php";
$query_array=array("name"=>array("name"=>__("Summary"),
                                 "sql"=>"SELECT `system_name` FROM `system` WHERE `system_uuid` = '" . $_GET["pc"] . "'",
                                ),
                   "views"=>array("summary"=>array(
                                                    "headline"=>__("System"),
                                                    "sql"=>"SELECT * FROM system
                                                            LEFT JOIN network_card ON (system_uuid=net_uuid AND system_timestamp=net_timestamp)
                                                            WHERE system_uuid = '" . $_REQUEST["pc"] . "' AND  system_timestamp = '".$GLOBAL["system_timestamp"]."'
                                                            LIMIT 0,1",
                                                    "image"=>"images/os_l.png",
                                                    "fields"=>array("10"=>array("name"=>"net_uuid",
                                                                                "show"=>"n",
                                                                               ),
                                                                    "20"=>array("name"=>"system_name", "head"=>__("System Name"),),
                                                                    // Include a blank entry as a place holder so we can add the LDAP details tab at the end  if required (AJH)
                                                                    "25"=>array("name"=>"", "head"=>__(""),),                                                                                 
                                                                    "30"=>array("name"=>"system_description", "head"=>__("Description"),),
                                                                    "40"=>array("name"=>"net_domain_role", "head"=>__("Domain Role"),),
                                                                    "50"=>array("name"=>"system_registered_user", "head"=>__("Registered User"),),
                                                                    "60"=>array("name"=>"net_user_name", "head"=>__("Current User"),),
                                                                    // Include a blank entry as a place holder so we can add the LDAP details tab at the end  if required (AJH)
                                                                    "65"=>array("name"=>"", "head"=>__(""),),                                                                                 
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
                                                    "sql"=>"SELECT system_name, net_domain FROM system WHERE system_uuid = '" . $_GET["pc"] . "' AND  system_timestamp = '".$GLOBAL["system_timestamp"]."'",
                                                    "image"=>"./images/display_l.png",
                                                    "print"=>"n",
                                                    "fields"=>array("10"=>array("name"=>"Explorer C",
                                                                                "head"=>__("Explorer C"),
                                                                                "get"=>array("head"=>"Explorer"." \\\\Client\\C\$-Session",
                                                                                             "file"=>"launch.php",
                                                                                             "title"=>__("Explorer"),
                                                                                             "image"=>"./images/shared_drive.png",
                                                                                             "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "domain"=>"%net_domain",
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
                                                                                             "image"=>"./images/o_load_balancer.png",
                                                                                             "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                         "domain"=>"%net_domain",
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
                                                                                            "image"=>"./images/o_load_balancer.png",                                                                                         
                                                                                            "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "domain"=>"%net_domain",
                                                                                                          "application"=>"$vnc_type"."_"."vnc",
                                                                                                          "ext"=>"vnc",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "40"=>array("name"=>"HTTP",
                                                                                "head"=>__("HTTP"),
                                                                                "get"=>array("head"=>"HTTP-Session",
                                                                                             "file"=>"launch.php",
                                                                                             "title"=>__("HTTP-Session"),
                                                                                            "image"=>"./images/os_l.png",                                                                                         
                                                                                            "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "target"=>"_BLANK",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "domain"=>"%net_domain",
                                                                                                           "application"=>"http",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "50"=>array("name"=>"HTTPS",
                                                                                "head"=>__("HTTPS"),
                                                                                "get"=>array("head"=>"HTTPS-Session",
                                                                                             "file"=>"launch.php",
                                                                                             "title"=>__("HTTPS-Session"),
                                                                                            "image"=>"./images/browser.png",                                                                                         
                                                                                            "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "target"=>"_BLANK",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "domain"=>"%net_domain",
                                                                                                          "application"=>"https",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "60"=>array("name"=>"FTP",
                                                                                "head"=>__("FTP"),
                                                                                "get"=>array("head"=>"FTP-Session",
                                                                                             "file"=>"launch.php",
                                                                                             "title"=>__("FTP-Session"),
                                                                                            "image"=>"./images/shared_drive_l.png",                                                                                         
                                                                                            "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "target"=>"_BLANK",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "domain"=>"%net_domain",
                                                                                                          "application"=>"ftp",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "70"=>array("name"=>"Manage",
                                                                                "head"=>__("Manage"),
                                                                                "get"=>array("head"=>"Manage",
                                                                                             "file"=>"launch.php",
                                                                                            "image"=>"./images/settings_2_l.png",                                                                                         
                                                                                            "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "title"=>__("Manage"),
                                                                                             "target"=>"_BLANK",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "domain"=>"%net_domain",
                                                                                                          "application"=>"manage",
                                                                                                          "ext"=>"vbs",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "80"=>array("name"=>"Services",
                                                                                "head"=>__("Services"),
                                                                                "get"=>array("head"=>"Services",
                                                                                             "file"=>"launch.php",
                                                                                            "image"=>"./images/services_l.png",                                                                                         
                                                                                            "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "title"=>__("Services"),
                                                                                             "target"=>"_BLANK",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "domain"=>"%net_domain",
                                                                                                          "application"=>"services",
                                                                                                          "ext"=>"vbs",
                                                                                                         ),
                                                                                            ),
                                                                                ),
  
                                                                    "90"=>array("name"=>"Reboot",
                                                                                "head"=>__("Reboot"),
                                                                                "get"=>array("head"=>"Reboot",
                                                                                             "file"=>"launch.php",
                                                                                            "image"=>"./images/emblem_important.png",                                                                                         
                                                                                            "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "title"=>__("Reboot"),
                                                                                             "target"=>"_BLANK",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "domain"=>"%net_domain",
                                                                                                          "application"=>"reboot",
                                                                                                          "ext"=>"vbs",
                                                                                                         ),
                                                                                            ),
                                                                                ),
                                                                    "100"=>array("name"=>"Wakeup",
                                                                                "head"=>__("Wakeup"),
                                                                                "get"=>array("head"=>"Wakeup",
                                                                                             "file"=>"wake_on_lan.php",
                                                                                            "image"=>"./images/tv_l.png",                                                                                         
                                                                                            "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "title"=>__("Wake on LAN (Note: WOL Wont work over different subnets)"),
                                                                                             "target"=>"_BLANK",
                                                                                             "var"=>array("hostname"=>"%system_name",
                                                                                                          "mac" =>"%net_mac_address",
                                                                                                          "socket_number"=>"9",
                                                                                                           ),
                                                                                            ),
                                                                                ),
                
                                                                  ),
                                                                  
                                                    ),

                                 ),
                  );
              
if ((isset($use_ldap_integration))and($use_ldap_integration == 'y')) {

    if ((isset($full_details))and ($full_details  == 'y')) {
    $query_array['views']['summary']['fields']['25']=array("name"=>"system_name", "head"=>__("Directory Info"),
                                                                        "get"=>array("head"=>__("Computer Details"),
                                                                                             "file"=>"ldap_details.php",
                                                                                             //"%net_user_name"
                                                                                             "title"=>__("Advanced Computer Details"),
                                                                                             //"name"=>"%net_user_name",                                                                                             
                                                                                             "image"=>"./images/o_terminal_server.png",
                                                                                             "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "var"=>array("name"=>"%system_name",
                                                                                                          "full_details"=> "y",
                                                                                                          "record_type" => "computer",
                                                                                                         ),
                                                                                            ),
                                                                              );
                                                                          
       $query_array['views']['summary']['fields']['65']=array("name"=>"net_user_name", "head"=>__("Directory Info"),
                                                                        "get"=>array("head"=>__("User Details"),
                                                                                             "file"=>"ldap_details.php",
                                                                                             //"%net_user_name"
                                                                                             "title"=>__("Advanced User Details"),
                                                                                             //"name"=>"%net_user_name",                                                                                             
                                                                                             "image"=>"./images/groups_l.png",
                                                                                             "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "var"=>array("name"=>"%net_user_name",
                                                                                                          "full_details"=> "y",
                                                                                                          "record_type" => "user",
                                                                                                         ),
                                                                                            ),
                                                                              );                                                                          
                                                                              
                                  } else {
   $query_array['views']['summary']['fields']['25']=array("name"=>"system_name", "head"=>__("Directory Info"),
                                                                        "get"=>array("head"=>__("Computer Details"),
                                                                                             "file"=>"ldap_details.php",
                                                                                             //"%net_user_name"
                                                                                             "title"=>__("Computer Details"),
                                                                                             //"name"=>"%net_user_name",                                                                                             
                                                                                             "image"=>"./images/o_terminal_server.png",
                                                                                             "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "var"=>array("name"=>"%system_name",
                                                                                                          "full_details"=> "n",
                                                                                                          "record_type" => "computer",
                                                                                                         ),
                                                                                            ),
                                                                              );                                      
    $query_array['views']['summary']['fields']['65']=array("name"=>"net_user_name", "head"=>__("Directory Info"),
                                                                        "get"=>array("head"=>__("User Details"),
                                                                                             "file"=>"ldap_details.php",
                                                                                             //"%net_user_name"
                                                                                             "title"=>__("User Details"),
                                                                                             //"name"=>"%net_user_name",                                                                                             
                                                                                             "image"=>"./images/groups_l.png",
                                                                                             "image_width"=>"16",
                                                                                             "image_height"=>"16",
                                                                                             "var"=>array("name"=>"%net_user_name",
                                                                                                         "full_details"=> "n",
                                                                                                          "record_type" => "user",
                                                                                                         ),
                                                                                            ),
                                                                              );                                  
                                }
                            }
?>
