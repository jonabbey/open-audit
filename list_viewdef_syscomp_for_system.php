<?php

$query_array=array("headline"=>array("name"=>__("System Components "),
                                     "sql"=>"SELECT `system_name` FROM `system` WHERE `system_uuid` = '" . $_REQUEST["pc"] . "'",
                                     ),
                   "sql"=>"SELECT software_name, software_version, software_url, software_publisher FROM software, system WHERE software_uuid = '".$_REQUEST["pc"]."' AND software_uuid = system_uuid AND software_timestamp = system_timestamp AND software_name NOT LIKE '%codec%' AND software_name NOT LIKE '%hotfix%' AND software_name NOT LIKE '%update%' AND software_name NOT LIKE '%Service Pack%' AND software_system_component <> '1' ",
                   "sort"=>"software_name",
                   "dir"=>"ASC",
                   "get"=>array("file"=>"list.php",
                                "title"=>__("Systems installed this Version of this Software"),
                                "var"=>array("name"=>"%software_name",
                                             "version"=>"%software_version",
                                             "headline_addition"=>"%software_name",
                                             "view"=>"systems_for_software_version",
                                            ),
                               ),
                   "fields"=>array("10"=>array("name"=>"software_name",
                                               "head"=>__("Software Name"),
                                               "show"=>"y",
                                               "link"=>"y",
                                               "get"=>array("file"=>"list.php",
                                                            "title"=>__("Systems installed this Software"),
                                                            "var"=>array("name"=>"%software_name",
                                                                         "headline_addition"=>"%software_name",
                                                                         "view"=>"systems_for_software",
                                                                        ),
                                                           ),
                                              ),
                                   "20"=>array("name"=>"software_version",
                                               "head"=>__("Version"),
                                               "show"=>"y",
                                               "link"=>"y",
                                              ),

                                   "30"=>array("name"=>"software_publisher",
                                               "head"=>__("Publisher"),
                                               "show"=>"y",
                                               "link"=>"y",
                                               "get"=>array("file"=>"%software_url",
                                                            "title"=>__("External Link"),
                                                            "target"=>"_BLANK",
                                                           ),
                                              ),
                                  ),
                  );
?>
