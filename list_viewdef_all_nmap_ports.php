<?php
    $query_array=array("headline"=>__("List all Nmap discovered TCP ports"),
                        "sql"=>"SELECT SUM(nmap_count) AS sum_nmap_count, nmap_port_number, nmap_port_name
                                      FROM (SELECT count(DISTINCT nmap_id) AS nmap_count, nmap_port_number, nmap_port_name
                                                   FROM nmap_ports, system
                                                   WHERE nmap_other_id  = system_uuid
                                                   GROUP BY nmap_port_number
                                            UNION
                                            SELECT count(DISTINCT nmap_id) AS nmap_count, nmap_port_number, nmap_port_name
                                                   FROM nmap_ports, other
                                                   WHERE (nmap_other_id = other_mac_address OR nmap_other_id = other_id)
                                                   GROUP BY nmap_port_number)
                                            AS TempTable1
                                      GROUP BY nmap_port_number",
                       "sort"=>"nmap_port_number",
                       "dir"=>"ASC",
                       "get"=>array("file"=>"list.php",
                                    "title"=>__("Hosts with this Nmap discovered port"),
                                    "var"=>array("view"=>"hosts_for_nmap_port",
                                                 "name"=>"%nmap_port_number",
                                                 "headline_addition"=>"%nmap_port_number",
                                                 "headline_addition2"=>"%nmap_port_name",                                                 
                                                ),
                                   ),
                       "fields"=>array("10"=>array("name"=>"sum_nmap_count",
                                                   "head"=>__("Count"),
                                                   "show"=>"y",
                                                   "link"=>"y",
                                                  ),
                                       "20"=>array("name"=>"nmap_port_number",
                                                   "head"=>__("TCP Port number"),
                                                   "show"=>"y",
                                                   "link"=>"y",
                                                   "sort"=>"y",
                                                   "search"=>"y",
                                                  ),
                                       "30"=>array("name"=>"nmap_port_name",
                                                   "head"=>__("Port name"),
                                                   "show"=>"y",
                                                   "link"=>"n",
                                                   "sort"=>"y",
                                                   "search"=>"y",
                                                  ),
                                      ),
                      );
?>