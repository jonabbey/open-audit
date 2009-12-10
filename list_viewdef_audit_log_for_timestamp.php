<?php
include "include_config.php";

mysql_connect($mysql_server,$mysql_user,$mysql_password) or die("Could not connect to DB<br>");
mysql_select_db($mysql_database);

$sql = "SELECT audit_schd_name FROM audit_schedules WHERE audit_schd_id = '{$_GET['schedule_id']}'";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);
$timestamp = date('d/m/y h:i:s a',$_GET['timestamp']);

    $query_array=array("headline"=> "Audit Log for Schedule \"{$row['audit_schd_name']}\" on {$timestamp}",
                       "sql"=>"SELECT * FROM audit_log
                               WHERE audit_log_schedule_id = '" . $_GET['schedule_id'] . "' AND
                                     audit_log_timestamp   = '" . $_GET['timestamp'  ] . "'",
                       "sort"=>"audit_log_id",
                       "dir"=>"ASC",
                       "get"=>array("file"=>"search.php",
                                    "title"=>__("Audit Log For Schedule on Timestamp"),
                                    "var"=>array( "search_field"=>"%audit_log_host" ),
                                   ),
                       "fields"=>array("10"=>array("name"=>"audit_log_message",
                                                   "head"=>__("Log Message"),
                                                   "show"=>"y",
                                                  ),
                                       "20"=>array("name"=>"audit_log_host",
                                                   "head"=>__("Hostname"),
                                                   "show"=>"y",
                                                   "link"=>"y",
                                                  ),
                                       "30"=>array("name"=>"audit_log_pid",
                                                   "head"=>__("PID"),
                                                   "show"=>"y",
                                                  ),
                                       "40"=>array("name"=>"audit_log_time",
                                                   "head"=>__("Date Audited"),
                                                   "show"=>"y",
                                                  ),
                                      ),
                      );
?>
