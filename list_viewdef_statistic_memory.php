<?php

$query_array=array("headline"=>__("Statistic for Physical Memory"),
                   "sql"=>"SELECT
                               memory_capacity AS system_memory, sum(memory_capacity),
                               COUNT(*) count_item,
                               ( 100 / (SELECT count(*) FROM memory WHERE memory_detail != 'Unknown') * COUNT( * ) ) AS percentage
                           FROM memory
                           WHERE (1)
                           GROUP BY memory_capacity
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

