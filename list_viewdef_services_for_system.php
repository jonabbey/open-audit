<?php

$query_array=array("headline"=>array("name"=>__("Services"),
                                     "sql"=>"SELECT `system_name` FROM `system` WHERE `system_uuid` = '" . $_REQUEST["pc"] . "'",
                                     ),
                   "sql"=>"SELECT * FROM service, service_details WHERE sd_display_name = service_display_name AND service_uuid = '".$_REQUEST["pc"]."' AND service_timestamp = '".$GLOBALS["timestamp"]."' ",
                   "sort"=>"service_display_name",
                   "dir"=>"ASC",
                   "get"=>array("file"=>"list.php",
                                "title"=>__("Hosts with this Service"),
                                "var"=>array("view"=>"systems_for_service",
                                             "name"=>"%service_display_name",
                                             "headline_addition"=>"%service_display_name",
                                            ),
                               ),
                   "fields"=>array("10"=>array("name"=>"service_display_name",
                                               "head"=>__("Display Name"),
                                               "show"=>"y",
                                               "link"=>"y",
                                               "help"=>"%service_path_name",
                                              ),
                                   "15"=>array("name"=>"service_name",
                                               "head"=>__("Service Name"),
                                               "show"=>"y",
                                               "link"=>"y",
                                              ),
                                   "20"=>array("name"=>"service_start_mode",
                                               "head"=>__("Start Mode"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "30"=>array("name"=>"service_state",
                                               "head"=>__("State"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "40"=>array("name"=>"service_started",
                                               "head"=>__("Started"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "50"=>array("name"=>"",
                                               "head"=>__("Description"),
                                               "show"=>"y",
                                               "link"=>"n",
                                               "sort"=>"n",
                                               "search"=>"n",
                                               "help"=>"%sd_description",
                                              ),
                                  ),
                  );
?>
