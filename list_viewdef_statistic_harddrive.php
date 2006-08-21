<?php

$query_array=array("headline"=>__("Statistic for Hard Disks"),
                   "sql"=>"
                           SELECT
                               hard_drive_size,
                               COUNT(*) AS count_item,
                               ( 100 / (
                                       SELECT count(*)
                                       FROM hard_drive, system WHERE
                                           system_uuid=hard_drive_uuid AND
                                           system_timestamp=hard_drive_timestamp
                                       )
                                 * COUNT(*)
                               ) AS percentage
                           FROM hard_drive, system
                           WHERE
                               system_uuid=hard_drive_uuid AND
                               system_timestamp=hard_drive_timestamp
                           GROUP BY hard_drive_size
                           ",
                   "sort"=>"count_item",
                   "dir"=>"DESC",
                   "fields"=>array("10"=>array("name"=>"hard_drive_size",
                                               "head"=>__("Hard Drive Size"),
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
