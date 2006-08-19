<?php

$query_array=array("headline"=>__("List Systems with BHO"),
                   "sql"=>"SELECT * FROM browser_helper_objects bho, system sys WHERE bho_program_file = '" . mysql_escape_string(urldecode($_GET["name"])) . "' and bho_uuid = system_uuid AND bho_timestamp = system_timestamp",
                   "sort"=>"system_name",
                   "dir"=>"ASC",
                   "get"=>array("file"=>"system.php",
                                "title"=>__("Go to System"),
                                "var"=>array("pc"=>"%system_uuid",
                                             "view"=>"summary",
                                            ),
                               ),
                   "fields"=>array("10"=>array("name"=>"system_uuid",
                                               "head"=>__("UUID"),
                                               "show"=>"n",
                                              ),
                                   "20"=>array("name"=>"net_ip_address",
                                               "head"=>__("IP"),
                                               "show"=>"y",
                                               "link"=>"y",
                                              ),
                                   "30"=>array("name"=>"system_name",
                                               "head"=>__("Hostname"),
                                               "show"=>"y",
                                               "link"=>"y",
                                              ),
                                   "40"=>array("name"=>"system_os_name",
                                               "head"=>__("OS"),
                                               "show"=>$show_os,
                                              ),
                                   "50"=>array("name"=>"system_timestamp",
                                               "head"=>__("Date Audited"),
                                               "show"=>$show_date_audited,
                                              ),
                                   "60"=>array("name"=>"system_system_type",
                                               "head"=>__("System Type"),
                                               "show"=>$show_type,
                                               "align"=>"center",
                                              ),
                                   "70"=>array("name"=>"system_description",
                                               "head"=>__("Description"),
                                               "show"=>$show_description,
                                              ),
                                   "80"=>array("name"=>"net_domain",
                                               "head"=>__("Domain"),
                                               "show"=>$show_domain,
                                              ),
                                   "90"=>array("name"=>"system_service_pack",
                                               "head"=>__("Servicepack"),
                                               "show"=>$show_service_pack,
                                              ),
                                  ),
                  );
?>
