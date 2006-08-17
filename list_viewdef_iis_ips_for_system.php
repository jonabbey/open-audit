<?php

$query_array=array("headline"=>__("IIS IP Settings on Host"),
                   "sql"=>"SELECT * from iis_ip where iis_ip_uuid = '".$_REQUEST["pc"]."' AND iis_ip_timestamp = '".$GLOBALS["timestamp"]."' AND iis_ip_site = '" . $myrow["iis_site"] . "' ",
                   "sort"=>"iis_ip_site",
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
