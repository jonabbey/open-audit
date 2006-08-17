<?php

$query_array=array("headline"=>"List Software on Host",
                   "sql"=>"SELECT software_name, software_version, software_publisher, software_url FROM software, system WHERE system_uuid = '".$_REQUEST["pc"]."' AND software_name NOT LIKE '%hotfix%' AND software_name NOT LIKE '%update%' AND software_name NOT LIKE '%Service Pack%' AND software_uuid = system_uuid AND software_timestamp = system_timestamp GROUP BY software_name, software_version ",
                   "sort"=>"software_name",
                   "dir"=>"ASC",
                   "get"=>array("file"=>"list.php",
                                "title"=>"Systems installed this Version of this Software",
                                "var"=>array("name"=>"%software_name",
                                             "version"=>"%software_version",
                                             "view"=>"systems_for_software_version",
                                             "headline_addition"=>"%software_name",
                                            ),
                               ),
                   "fields"=>array("10"=>array("name"=>"software_name",
                                               "head"=>"Software Name",
                                               "show"=>"y",
                                               "link"=>"y",
                                               "get"=>array("file"=>"list.php",
                                                            "title"=>"Systems installed this Software",
                                                            "var"=>array("name"=>"%software_name",
                                                                         "view"=>"systems_for_software",
                                                                         "headline_addition"=>"%software_name",
                                                                        ),
                                                           ),
                                              ),
                                   "20"=>array("name"=>"software_version",
                                               "head"=>"Version",
                                               "show"=>"y",
                                               "link"=>"y",
                                              ),

                                   "30"=>array("name"=>"software_publisher",
                                               "head"=>"Publisher",
                                               "show"=>"y",
                                               "link"=>"y",
                                               "get"=>array("file"=>"%software_url",
                                                            "title"=>"External Link",
                                                           ),
                                              ),
                                  ),
                  );
?>
