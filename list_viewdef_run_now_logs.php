<?php
include "include_config.php";

mysql_connect($mysql_server,$mysql_user,$mysql_password) or die("Could not connect to DB<br>");
mysql_select_db($mysql_database);

$results = mysql_query($sql);
    $query_array=array("headline"=>__("Run Now Audit Logs"),
                       "sql"=>"SELECT * FROM audit_log, audit_configurations
                               WHERE audit_log_schedule_id = 'none' AND audit_log_config_id = audit_cfg_id
                               GROUP BY audit_log_config_id",
                       "sort"=>"audit_log_config_id",
                       "dir"=>"DESC",
                       "get"=>array("file"=>"list.php",
                                    "title"=>__("Run Now Audit Logs"),
                                    "var"=>array("view"=>"audit_logs_for_run_now",
                                                 "config_id"=>"%audit_log_config_id",
                                                 "name"=>"%audit_cfg_name",
                                                ),
                                   ),
                       "fields"=>array("5"=>array("name"=>"audit_log_config_id",
                                                   "head"=>__("Configuration ID"),
                                                   "show"=>"y",
                                                   "link"=>"y",
                                                  ),
                                       "10"=>array("name"=>"audit_cfg_name",
                                                   "head"=>__("Configuration Name"),
                                                   "show"=>"y",
                                                   "link"=>"y",
                                                  ),
                                      ),
                      );
?>
