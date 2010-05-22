<?php

$query_array=array("headline"=>__("Statistic for Printer models"),
                   "sql"=>"SELECT
                               DISTINCT other_description,
                               COUNT( * ) AS count_item,
                               round( 100 / (SELECT count(other_id) FROM other, system
											WHERE other_type = 'printer' AND (other_linked_pc = system_uuid OR other_linked_pc = '') AND other_timestamp = system_timestamp
											) * COUNT( * ),$round_to_decimal_places ) AS percentage
                               FROM other, system
                               WHERE other_type = 'printer' AND (other_linked_pc = system_uuid OR other_linked_pc = '') AND other_timestamp = system_timestamp
                               GROUP BY other_description",
                   "sort"=>"count_item",
                   "dir"=>"DESC",
                   "get"=>array("file"=>"list.php",
                                "title"=>__("Systems installed this Model of this Printer"),
                                "var"=>array("name"=>"%other_description",
                                             "version"=>"",
                                             "view"=>"systems_for_printer_version",
                                             "headline_addition"=>"%other_description",
                                            ),
                               ),
                   "fields"=>array("10"=>array("name"=>"other_description",
                                               "head"=>__("Model"),
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
