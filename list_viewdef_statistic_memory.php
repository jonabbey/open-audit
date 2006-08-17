<?php

$query_array=array("headline"=>__("Statistic for Physical Memory"),
                   "sql"=>"SELECT
                               system_memory,
                               COUNT(*) count_item,
                               ( 100 / (SELECT count(*) FROM system WHERE system_memory != '') * COUNT( * ) ) AS percentage
                           FROM system
                           WHERE (1)
                           GROUP BY system_memory
                           ",
                   "sort"=>"count_item",
                   "dir"=>"DESC",
                   "fields"=>array("10"=>array("name"=>"system_memory",
                                               "head"=>__("Physical Memory"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "20"=>array("name"=>"count_item",
                                               "head"=>__("Count"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "30"=>array("name"=>"percentage",
                                               "head"=>__("Percentage"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                  ),
                  );
?>
