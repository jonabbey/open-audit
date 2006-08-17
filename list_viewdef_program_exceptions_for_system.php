<?php

$query_array=array("headline"=>"List Program-Exceptions for Host",
                   "sql"=>"SELECT * FROM firewall_auth_app where firewall_app_uuid = '".$_REQUEST["pc"]."' AND firewall_app_timestamp = '".$GLOBALS["timestamp"]."' ",
                   "sort"=>"firewall_app_profile, firewall_app_name",
                   "dir"=>"ASC",
                   "fields"=>array("10"=>array("name"=>"firewall_app_profile",
                                               "head"=>"Profile",
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "20"=>array("name"=>"firewall_app_name",
                                               "head"=>"Name",
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "30"=>array("name"=>"firewall_app_executable",
                                               "head"=>"File",
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                  ),
                  );
?>
