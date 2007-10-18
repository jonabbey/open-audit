<?php

    $query_array=array("headline"=>__("List Nmap discovered TCP ports with other systems"),
                       "sql"=>"SELECT nmap_port_number, nmap_port_name, other_id, other_network_name, other_ip_address, other_description, other_type
                                      FROM nmap_ports, other
                                      WHERE nmap_other_id = other_mac_address OR nmap_other_id = other_id",
                       "sort"=>"nmap_port_number, other_network_name",
                       "dir"=>"ASC",
                       "get"=>array("file"=>"system.php",
                                    "title"=>"Go to Other System",
                                    "var"=>array("other"=>"%other_id",
                                                 "view"=>"other_system",
                                                ),
                                   ),
                       "fields"=>array("10"=>array("name"=>"nmap_port_number",
                                                   "head"=>__("TCP Port number"),
                                                   "show"=>"y",
												   "link"=>"n",
                                                  ),
                                       "20"=>array("name"=>"nmap_port_name",
                                                   "head"=>__("Port name"),
                                                   "show"=>"y",
												   "link"=>"n",
                                                  ),
                                       "30"=>array("name"=>"other_network_name",
                                                   "head"=>__("Hostname"),
                                                   "show"=>"y",
                                                   "link"=>"y",
                                                  ),
                                       "40"=>array("name"=>"other_ip_address",
                                                   "head"=>__("IP address"),
                                                   "show"=>"y",
                                                   "link"=>"y",
                                                  ),
                                       "50"=>array("name"=>"other_description",
                                                   "head"=>__("Description"),
                                                   "show"=>"y",
                                                   "link"=>"n",
                                                  ),
                                       "60"=>array("name"=>"other_type",
                                                   "head"=>__("Type"),
                                                   "show"=>"y",
                                                   "link"=>"n",
                                                  ),                                             
                                      ),
                      );
?>