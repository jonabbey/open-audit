<?php 
$mysql_server = 'localhost'; 
$mysql_database = 'openaudit'; 
$mysql_user = 'root'; 
$mysql_password = '301170'; 
 
// An array of allowed users and their passwords 
// Make sure to set use_pass = "n" if you do not wish to use passwords 
$use_pass = 'n'; 
$users = array( 
  'admin' => 'Open-AudIT' 

); 
 
 
// Config options for index.php 
$show_other_discovered = 'y'; 
$other_detected = '3'; 
 
$show_system_discovered = 'y'; 
$system_detected = '3'; 
 
$show_systems_not_audited = 'y'; 
$days_systems_not_audited = '3'; 
 
$show_partition_usage = 'y'; 
$partition_free_space = '1000'; 
 
$show_software_detected = 'y'; 
$days_software_detected = '5'; 
 
$show_patches_not_detected = 'y'; 
$number_patches_not_detected = '5'; 
 
$show_detected_servers = 'y'; 
 
$show_os = 'y'; 
$show_date_audited = 'y'; 
$show_type = 'y'; 
$show_description = 'n'; 
$show_domain = 'n'; 
$show_service_pack = 'n'; 
 
$count_system = '20'; 

$col = 'blue'; 
$pic_style = '_win'; 
 
?>