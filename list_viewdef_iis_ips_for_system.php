<?php

$query_array=array("headline"=>__("IIS IP Settings on Host"),
                   "sql"=>"SELECT iis_ip_ip_address, iis_ip_port FROM iis_ip where iis_ip_uuid = '".$_REQUEST["pc"]."' AND iis_ip_timestamp = '".$GLOBALS["timestamp"]."' AND iis_ip_site = '" . $_REQUEST["iis_site"] . "' ",
                   "sort"=>"iis_ip_site",
                   "dir"=>"ASC",
                   "fields"=>array("10"=>array("name"=>"iis_ip_ip_address",
                                               "head"=>__("IP"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "20"=>array("name"=>"iis_ip_port",
                                               "head"=>__("Name"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                  ),
                  );
?>
