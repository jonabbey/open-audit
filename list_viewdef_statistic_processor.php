<?php

$query_array=array("headline"=>__("Statistic for Processors"),
                   "sql"=>"
                           SELECT
                               processor_name,
                               COUNT(*) count_item,
                               ( 100 / (
                                       SELECT count(*)
                                       FROM  processor INNER JOIN system ON
                                           system_uuid=processor_uuid AND system_timestamp=processor_timestamp
                                       )
                                 * COUNT(*)
                               ) AS percentage
                           FROM  processor INNER JOIN system ON
                           system_uuid=processor_uuid AND system_timestamp=processor_timestamp
                           GROUP BY processor_name
                           ",
                   "sort"=>"count_item",
                   "dir"=>"DESC",
                   "fields"=>array("10"=>array("name"=>"processor_name",
                                               "head"=>__("Processor"),
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
