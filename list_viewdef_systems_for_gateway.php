<?php

$query_array=array("headline"=>__("List Systems with Gateway"),
                   "sql"=>"SELECT * FROM system, network_card
                           WHERE net_uuid  = system_uuid
                           AND system_timestamp  = net_timestamp
                           AND net_gateway = '" . $_GET["net_gateway"] . "'  ",
                   "sort"=>"system_name",
                   "dir"=>"ASC",
                   "get"=>array("file"=>"system.php",
                                "title"=>"Go to System",
                                "var"=>array("pc"=>"%system_uuid",
                                             "view"=>"summary",
                                            ),
                               ),
                   "fields"=>array("10"=>array("name"=>"system_uuid",
                                               "head"=>__("UUID"),
                                               "show"=>"n",
                                              ),
                                   "20"=>array("name"=>"net_ip_address",
                                               "head"=>__("IP"),
                                               "show"=>"y",
                                               "link"=>"y",
                                              ),
                                   "30"=>array("name"=>"system_name",
                                               "head"=>__("Hostname"),
                                               "show"=>"y",
                                               "link"=>"y",
                                              ),
                                   "40"=>array("name"=>"net_gateway",
                                               "head"=>__("Gateway"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                  ),
                  );
?> 