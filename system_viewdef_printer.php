<?php

$query_array=array("name"=>__("Printer"),
                   "image"=>"images/os_l.png",
                   "views"=>array("summary"=>array(
                                                    "headline"=>__("Summary"),
                                                    "sql"=>"SELECT * FROM other, system WHERE system_uuid=other_linked_pc AND other_id = '".$_REQUEST['other']."'",
                                                    "image"=>"./images/printer_l.png",
                                                    "fields"=>array(

                                                                    "10"=>array("name"=>"other_description", "head"=>__("Description"), "edit"=>"y", "edit_type"=>"textarea",),
                                                                    "20"=>array("name"=>"other_p_port_name", "head"=>__("Port-Name"), "edit"=>"y",),
                                                                    "30"=>array("name"=>"other_linked_pc",
                                                                                "head"=>__("Attached Device"),
                                                                                "edit"=>"y",
                                                                                "head"=>__("Associate with System"),
                                                                                "edit"=>"y",
                                                                                "edit_type"=>"select",
                                                                                "edit_sql"=>"SELECT system_uuid, system_name FROM system WHERE system_uuid!='' ",
                                                                                ),
                                                                    "35"=>array("name"=>"", "head"=>__(""),),
                                                                    "40"=>array("name"=>"other_network_name", "head"=>__("Name"), "edit"=>"y",),
                                                                    "50"=>array("name"=>"other_type", "head"=>__("Type"), "edit"=>"y",),
                                                                    "60"=>array("name"=>"other_ip_address", "head"=>__("IP Address"), "edit"=>"y",),
                                                                    "70"=>array("name"=>"other_mac_address", "head"=>__("MAC Address"), "edit"=>"y",),
                                                                    "80"=>array("name"=>"other_first_timestamp", "head"=>__("Date First Audited"), "edit"=>"n",),
                                                                    "90"=>array("name"=>"other_timestamp", "head"=>__("Date Last Audited"), "edit"=>"n",),
                                                                    "100"=>array("name"=>"other_manufacturer", "head"=>__("Manufacturer"), "edit"=>"y",),
                                                                    "110"=>array("name"=>"other_model", "head"=>__("Model"), "edit"=>"y",),
                                                                    "120"=>array("name"=>"other_serial", "head"=>__("Serial"), "edit"=>"y",),
                                                                    "130"=>array("name"=>"other_location", "head"=>__("Location"), "edit"=>"y",),
                                                                    "140"=>array("name"=>"other_date_purchased", "head"=>__("Date of Purchase"), "edit"=>"y",),
                                                                    "150"=>array("name"=>"other_value", "head"=>__("Dollar Value"), "edit"=>"y",),
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
