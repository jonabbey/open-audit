<?php

$query_array=array("headline"=>__("List Program-Exceptions for Host"),
                   "sql"=>"SELECT * FROM firewall_auth_app where firewall_app_uuid = '".$_REQUEST["pc"]."' AND firewall_app_timestamp = '".$GLOBALS["timestamp"]."' ",
                   "sort"=>"firewall_app_profile, firewall_app_name",
                   "dir"=>"ASC",
                   "fields"=>array("10"=>array("name"=>"firewall_app_profile",
                                               "head"=>__("Profile"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "20"=>array("name"=>"firewall_app_name",
                                               "head"=>__("Name"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "30"=>array("name"=>"firewall_app_executable",
                                               "head"=>__("File"),
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                  ),
                  );
?>
