<?php

$query_array=array("headline"=>__("Statistic for Physical Memory"),
                   "sql"=>"SELECT system_memory,
                               COUNT(*) count_item,
                               ( 100 / (SELECT count(*) FROM memory WHERE memory_detail != 'Unknown') * COUNT( * ) ) AS percentage
                           FROM (select *, sum(memory_capacity) AS system_memory FROM memory GROUP BY memory_uuid) AS full_system_memory
                           WHERE (1)
                           GROUP BY system_memory
                           ",
                   "sort"=>"count_item",
                   "dir"=>"DESC",
                   "get"=>array("file"=>"list.php",
                                "title"=>__("Hosts with this Memory"),
                                "var"=>array("view"=>"systems_for_memory",
                                             "name"=>"%system_memory",
                                             "headline_addition"=>"%system_memory",
                                            ),
                               ),
                   "fields"=>array("10"=>array("name"=>"system_memory",
                                               "head"=>__("Physical Memory"),
                                               "show"=>"y",
                                               "link"=>"y",
                                              ),
                                   "20"=>array("name"=>"count_item",
                                               "head"=>__("Count"),
                                               "show"=>"y",
                                               "link"=>"n",
                                               "search"=>"n",
                                              ),
                                   "30"=>array("name"=>"percentage",
                                               "head"=>__("Percentage"),
                                               "show"=>"y",
                                               "link"=>"n",
                                               "search"=>"n",
                                              ),
                                  ),
                  );
?>