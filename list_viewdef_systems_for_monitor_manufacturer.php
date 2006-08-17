<?php

$query_array=array("headline"=>"List Systems for Manufacturer",
                   "sql"=>"SELECT monitor_id, monitor_model, monitor_manufacturer, monitor_serial, system_name, system_uuid FROM monitor, system WHERE monitor_manufacturer = '".$_GET["manufacturer"]."' AND monitor_uuid = system_uuid AND monitor_timestamp = system_timestamp ",
                   "sort"=>"system_name",
                   "dir"=>"ASC",
                   "get"=>array("file"=>"system.php",
                                "title"=>"Go to System",
                                "var"=>array("monitor"=>"%monitor_id",
                                             "view"=>"monitor",
                                            ),
                               ),
                   "fields"=>array("10"=>array("name"=>"other_linked_pc",
                                               "head"=>$l_ipa,
                                               "show"=>"n",
                                              ),
                                   "20"=>array("name"=>"system_name",
                                               "head"=>"Hostname",
                                               "show"=>"y",
                                               "link"=>"y",
                                               "get"=>array("file"=>"system.php",
                                                            "var"=>array("pc"=>"%system_uuid",
                                                                         "view"=>"summary",
                                                                        ),
                                                           ),
                                              ),
                                   "30"=>array("name"=>"monitor_manufacturer",
                                               "head"=>"Manufacturer",
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "40"=>array("name"=>"monitor_model",
                                               "head"=>"Model",
                                               "show"=>"y",
                                               "link"=>"y",
                                               "get"=>array("file"=>"list.php",
                                                            "var"=>array("view"=>"systems_for_monitor_modell",
                                                                         "modell"=>"%monitor_model",
                                                                        ),
                                                           ),
                                              ),
                                   "50"=>array("name"=>"monitor_serial",
                                               "head"=>"Serial",
                                               "show"=>"y",
                                               "link"=>"y",
                                              ),
                                  ),
                  );
?>
