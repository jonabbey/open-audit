<?php
include "include_config.php";

mysql_connect($mysql_server,$mysql_user,$mysql_password) or die("Could not connect to DB<br>");
mysql_select_db($mysql_database);

$results = mysql_query($sql);
    $query_array=array("headline"=>__("Audit Logs"),
                       "sql"=>"SELECT * FROM audit_log, audit_schedules
                               WHERE audit_log_schedule_id = audit_schd_id
                               GROUP BY audit_log_schedule_id",
                       "sort"=>"audit_log_schedule_id",
                       "dir"=>"DESC",
                       "get"=>array("file"=>"list.php",
                                    "title"=>__("Audit Logs By Schedule"),
                                    "var"=>array("view"=>"audit_logs_for_schedule",
                                                 "schedule_id"=>"%audit_log_schedule_id",
                                                 "name"=>"%audit_schd_name",
                                                ),
                                   ),
                       "fields"=>array("5"=>array("name"=>"audit_schd_id",
                                                   "head"=>__("Schedule ID"),
                                                   "show"=>"y",
                                                   "link"=>"y",
                                                  ),
                                       "10"=>array("name"=>"audit_schd_name",
                                                   "head"=>__("Schedule Name"),
                                                   "show"=>"y",
                                                   "link"=>"y",
                                                  ),
                                      ),
                      );
?>
