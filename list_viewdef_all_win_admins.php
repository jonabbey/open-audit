<?php

$query_array=array("headline"=>__("List all Windows Administrators by Hosts"),
                   "sql"=>"SELECT * FROM groups, system WHERE groups_SID = 'S-1-5-32-544' AND groups_uuid = system_uuid AND groups_timestamp = system_timestamp ",
                   "sort"=>"system_name",
                   "dir"=>"ASC",
                   "get"=>array("file"=>"system.php",
                                "title"=>__("Go to System"),
                                "var"=>array("pc"=>"%system_uuid",
                                             "view"=>"summary",
                                            ),
                               ),
                   "fields"=>array("10"=>array("name"=>"system_uuid",
                                               "head"=>__("UUID"),
                                               "show"=>"n",
                                              ),
                                   "20"=>array("name"=>"system_name",
                                               "head"=>__("Hostname"),
                                               "show"=>"y",
                                               "link"=>"y",
                                               "get"=>array("file"=>"system.php",
                                                            "title"=>__("Go to System"),
                                                            "var"=>array("pc"=>"%system_uuid",
                                                                         "view"=>"os",
                                                                        ),
                                                           ),
                                              ),
                                   "30"=>array("name"=>"groups_members",
                                               "head"=>__("Administrators"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                  ),
                  );
?>