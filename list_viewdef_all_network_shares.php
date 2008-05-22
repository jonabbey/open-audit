<?php
$query_array=array("headline"=>__("List all Windows Shares with hosts"),
                   "sql" => "SELECT system.net_domain, system.system_name, system.net_user_name, shares.shares_name, shares.shares_caption, shares.shares_path FROM system AS system, shares AS shares WHERE system.system_uuid = shares.shares_uuid ",
                   "sort"=>"system_name",
                   "dir"=>"ASC",
                   "get"=>array("file"=>"system.php",
                                "title"=>__("Go to System"),
                                "var"=>array("pc"=>"%shares_uuid ",
                                             "view"=>"summary",
                                            ),
                               ),
                   "fields"=>array("10"=>array("name"=>"shares_uuid ",
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
                                   "30"=>array("name"=>"shares_name",
                                               "head"=>__("Share Name"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "40"=>array("name"=>"shares_caption",
                                               "head"=>__("Share Description"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "50"=>array("name"=>"shares_path",
                                               "head"=>__("Share Path"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
 
                                  ),
                  );
?>