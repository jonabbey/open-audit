<?php

$query_array=array("headline"=>__("Statistic for Operating Systems"),
                   "sql"=>"SELECT
                               system_os_name,
                               COUNT( system_uuid ) AS count_item,
                               ( 100 / (SELECT count(system_uuid) FROM system  WHERE system_os_name != '') * COUNT( system_uuid ) ) AS percentage
                           FROM system
                           GROUP BY system_os_name",
                   "sort"=>"count_item",
                   "dir"=>"DESC",
                   "fields"=>array("10"=>array("name"=>"system_os_name",
                                               "head"=>__("OS"),
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