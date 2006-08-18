<?php

$query_array=array("name"=>__("Other System"),
                   "image"=>"images/os_l.png",
                   "views"=>array("summary"=>array(
                                                    "headline"=>__("Summary"),
                                                    "sql"=>"SELECT * FROM other WHERE other_id = '".$_GET['other']."'",
                                                    "image"=>"./images/summary_l.png",
                                                    "fields"=>array(
                                                                    "10"=>array("name"=>"other_description", "head"=>__("Name"),),
                                                                    "20"=>array("name"=>"other_type", "head"=>__("Type"),),
                                                                    "30"=>array("name"=>"other_network_name", "head"=>__("Attached Device"),),
                                                                    "40"=>array("name"=>"system_registered_user", "head"=>__("User"),),
                                                                    "50"=>array("name"=>"other_ip_address", "head"=>__("IP"),),
                                                                    "60"=>array("name"=>"other_mac_address", "head"=>__("MAC Adress"),),
                                                                    "70"=>array("name"=>"other_timestamp", "head"=>__("Date Last Audited"),),
                                                                    "80"=>array("name"=>"other_manufacturer", "head"=>__("Manufacturer"),),
                                                                    "90"=>array("name"=>"other_model", "head"=>__("Model"),),
                                                                    "100"=>array("name"=>"other_serial", "head"=>__("Serial"),),
                                                                    "110"=>array("name"=>"other_location", "head"=>__("Location"),),
                                                                    "120"=>array("name"=>"other_date_purchased", "head"=>__("Date Purchased"),),
                                                                    "130"=>array("name"=>"other_value", "head"=>__("Other Value"),),
                                                                    "140"=>array("name"=>"", "head"=>__(""),),
                                                                   ),
                                                    ),
                                   "nmap"=>array(
                                                    "headline"=>__("Nmap discovered on Audited PC"),
                                                    "sql"=>"SELECT * from nmap_ports WHERE nmap_other_id = '" . $_REQUEST["other"] . "' ",
                                                    "table_layout"=>"horizontal",
                                                    "image"=>"./images/nmap_l.png",
                                                    "fields"=>array("10"=>array("name"=>"nmap_port_number", "head"=>__("Port"),),
                                                                    "20"=>array("name"=>"nmap_port_name", "head"=>__("Port-Name"),),
                                                                   ),
                                                    ),

                                ),
                  );
?>
