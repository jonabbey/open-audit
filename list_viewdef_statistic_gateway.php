<?php

$query_array=array("headline"=>__("Statistic for Gateway"),
                   "sql"=>"
                           SELECT
                               net_gateway,
                               COUNT( * ) AS count_item,
                               ROUND( 100 / (
                                       SELECT count(*) FROM network_card, system
                                       WHERE
                                           net_uuid = system_uuid AND net_timestamp = system_timestamp
                                       )
                                 * COUNT( * )
                               , $round_to_decimal_places) AS percentage,net_gateway
                               FROM
                                   network_card, system
                               WHERE
                                    net_uuid = system_uuid AND
                                    net_timestamp = system_timestamp AND
                                    net_gateway != ''
                               GROUP BY net_gateway
                               ",
                   "sort"=>"count_item",
                   "dir"=>"DESC",
                   "get"=>array("file"=>"list.php",
                                "title"=>__("Hosts with this Gateway"),
                                "var"=>array("view"=>"systems_for_gateway",
                                             "net_gateway"=>"%net_gateway",
                                             "headline_addition"=>"%net_gateway",
                                            ),
                               ),
                   "fields"=>array("10"=>array("name"=>"net_gateway",
                                               "head"=>__("Gateway"),
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