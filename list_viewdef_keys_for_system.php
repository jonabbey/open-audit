<?php

$query_array=array("headline"=>"List Keys on Host ",
                   "sql"=>"SELECT ms_keys_name, ms_keys_cd_key, system_name, net_ip_address, system_uuid FROM ms_keys, system WHERE system_uuid LIKE '".urldecode($_GET["pc"])."%' AND ms_keys_uuid = system_uuid AND ms_keys_timestamp = system_timestamp ",
                   "sort"=>"system_name",
                   "dir"=>"ASC",
                   "fields"=>array("10"=>array("name"=>"ms_keys_name",
                                               "head"=>"Software",
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "20"=>array("name"=>"ms_keys_cd_key",
                                               "head"=>"Key",
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                  ),
                  );
?>
