<?php

$query_array=array("headline"=>__("Ports on Host"),
                   "sql"=>"SELECT * from nmap_ports WHERE nmap_other_id = '" . $_REQUEST["pc"] . "' ",
                   "sort"=>"nmap_port_number",
                   "dir"=>"ASC",
                   "fields"=>array("10"=>array("name"=>"nmap_port_number",
                                               "head"=>__("Port"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "20"=>array("name"=>"nmap_port_name",
                                               "head"=>__("Port-Name"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                  ),
                  );
?>
