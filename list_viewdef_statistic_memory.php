<?php

$query_array=array("headline"=>"Statistic for Physical Memory",
                   "sql"=>"SELECT
                               system_memory,
                               COUNT(*) count_item,
                               ( 100 / (SELECT count(*) FROM system WHERE system_memory != '') * COUNT( * ) ) AS percentage
                           FROM system
                           GROUP BY system_memory
                           ",
                   "sort"=>"count_item",
                   "dir"=>"DESC",
                   "fields"=>array("10"=>array("name"=>"system_memory",
                                               "head"=>"Physical Memory",
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "20"=>array("name"=>"count_item",
                                               "head"=>"Count",
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "30"=>array("name"=>"percentage",
                                               "head"=>"Percentage",
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                  ),
                  );
?>
