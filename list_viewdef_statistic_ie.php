<?php

$query_array=array("headline"=>__("Statistic for Internet Explorer Versions"),
                   "sql"=>"
                           SELECT
                               DISTINCT software_version,
                               COUNT( * ) AS count_item,
                               ( 100 / (
                                       SELECT count(software_uuid) FROM software, system
                                       WHERE
                                           software_name = 'Internet Explorer' AND
                                           software_timestamp=system_timestamp AND
                                           software_uuid=system_uuid
                                       )
                                 * COUNT( * )
                               ) AS percentage
                               FROM
                                   software, system
                               WHERE
                                    software_name='Internet Explorer' AND
                                    software_timestamp=system_timestamp AND
                                    software_uuid=system_uuid
                               GROUP BY software_version
                               ",
                   "sort"=>"count_item",
                   "dir"=>"DESC",
                   "fields"=>array("10"=>array("name"=>"software_version",
                                               "head"=>__("Version"),
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
