<?php
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Open-AudIT Upgrade</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" type="text/css" href="default.css" />
  </head>
  <body>
<?php
@(include "include_config.php") OR die("include_config.php missing");
@(include "include_functions.php") OR die("include_functions.php missing");
@(include "include_lang.php") OR die("include_lang.php missing");

$db = mysql_connect($mysql_server,$mysql_user,$mysql_password) or die('Could not connect: ' . mysql_error());
mysql_select_db($mysql_database,$db);

$version = get_config("version");

if ($version == "") {
  $version = "0.0.0";
}
// 
// Currently we only run an upgrade if there are SQL table alterations. 
// Code alterations are not covered by this script (yet... watch this space).. 
// Add in a sql statement and an upgrade ($version, "newversion_number", $sql) for each version change...
// Only alter the older version changes if you absolutely must, as this would break existing installed users!


$sql = "ALTER TABLE `system` CHANGE `system_country_code` `system_country_code` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
        ALTER TABLE `network_card` CHANGE `net_description` `net_description` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
        ALTER TABLE `software` CHANGE `software_uninstall` `software_uninstall` MEDIUMTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL; 
        ALTER TABLE `other` CHANGE `other_p_port_name` `other_p_port_name` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
        ALTER TABLE `software` CHANGE `software_install_date` `software_install_date` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
        ALTER TABLE `system_man` ADD COLUMN `system_man_picture` varchar(100)  NOT NULL DEFAULT '' AFTER `system_man_terminal_number`;
        DROP TABLE IF EXISTS `auth`;
        CREATE TABLE `auth` (
        `auth_id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `auth_username` VARCHAR( 25 ) NOT NULL ,
        `auth_hash` VARCHAR( 49 ) NOT NULL ,
        `auth_realname` VARCHAR( 255 ) NOT NULL ,
        `auth_enabled` BOOL NOT NULL DEFAULT '1' ,
        `auth_admin` BOOL NOT NULL DEFAULT '0' ,
        UNIQUE (
        `auth_username`
        )
        ) ENGINE = MYISAM DEFAULT CHARSET=latin1;";
        
        


upgrade($version, "06.08.30", $sql);

$sql = "ALTER TABLE `memory` CHANGE `memory_capacity` `memory_capacity` INT( 11 ) NOT NULL ";

upgrade($version, "06.09.29", $sql);

// Upgrade to version 06.09.31 Upgraded network table to include gateway AJH 24th May 2007
// Thanks to "Scott" for the idea. 

$sql = "ALTER TABLE `network_card` ADD COLUMN `net_gateway` varchar(100)  NOT NULL DEFAULT '' AFTER `net_manufacturer`";

upgrade ($version,"06.09.31", $sql);

$sql = "ALTER TABLE `software` CHANGE `software_name` `software_name` VARCHAR( 256 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL";

upgrade ($version,"07.08.01", $sql);


$sql = "ALTER TABLE `software_licenses` MODIFY COLUMN `license_purchase_number` INTEGER  NOT NULL DEFAULT 0,
 MODIFY COLUMN `license_purchase_date` DATE  NOT NULL DEFAULT '0000-00-00',
 DROP COLUMN `license_mac_address`;";
 
upgrade ($version,"07.08.28", $sql);


$sql = "CREATE TABLE `scan_type` (
  `scan_type_id` int  NOT NULL AUTO_INCREMENT,
  `scan_type_uuid` varchar(100)  NOT NULL,
  `scan_type_ip_address` varchar(16)  NOT NULL,
  `scan_type` varchar(10)  NOT NULL,
  `scan_type_detail` VARCHAR(100)  NOT NULL,
  `scan_type_frequency` TINYINT  NOT NULL,
  PRIMARY KEY(`scan_type_id`)
)
ENGINE = MYISAM;
CREATE TABLE `scan_log` (
  `scan_log_id` int  NOT NULL AUTO_INCREMENT,
  `scan_log_uuid` varchar(100)  NOT NULL,
  `scan_log_ip_address` varchar(16)  NOT NULL,
  `scan_log_type` varchar(10)  NOT NULL,
  `scan_log_detail` VARCHAR(100)  NOT NULL,
  `scan_log_frequency` TINYINT  NOT NULL,
  `scan_log_date_time` datetime  NOT NULL,
  `scan_log_result` varchar(20)  NOT NULL,
  `scan_log_success` varchar(2)  NOT NULL,
  PRIMARY KEY(`scan_log_id`)
)
ENGINE = MYISAM;
CREATE TABLE `scan_latest` (
  `scan_latest_id` int  NOT NULL AUTO_INCREMENT,
  `scan_latest_uuid` varchar(100)  NOT NULL,
  `scan_latest_ip_address` varchar(16)  NOT NULL,
  `scan_latest_type` varchar(10)  NOT NULL,
  `scan_latest_detail` VARCHAR(100)  NOT NULL,
  `scan_latest_frequency` TINYINT  NOT NULL,
  `scan_latest_date_time` datetime  NOT NULL,
  `scan_latest_result` varchar(20)  NOT NULL,
  `scan_latest_success` varchar(2)  NOT NULL,
  PRIMARY KEY(`scan_latest_id`)
)
ENGINE = MYISAM;";

upgrade ($version,"07.10.25", $sql);

$sql = "ALTER TABLE `nmap_ports` ADD COLUMN `nmap_port_proto` varchar(10) NOT NULL default '' AFTER `nmap_port_number`, 
                                 ADD COLUMN `nmap_port_version` varchar(100) NOT NULL default '' AFTER `nmap_port_name`, 
                                 ADD KEY `id3` (`nmap_port_proto`);";
 
upgrade ($version,"07.11.15", $sql);


?>
    <br />Upgrade complete.
    <br /><br /><a href="index.php" alt=""><?php echo __("Return to Index"); ?></a>
  </body>
</html>

<?php
function upgrade($version, $latestversion, $sql) {
  if (versionCheck($version, $latestversion)) {
    echo __("Upgrading to") . " " . $latestversion;

    $sql2 = explode(";", $sql);
    foreach ($sql2 as $sql3) {
      if ($sql3 != "") {
        echo ".";
        $result = mysql_query($sql3 . ";") OR die('Query failed: ' . $sql3 . '<br />' . mysql_error());
      }
    }

    modify_config("version", $latestversion);

    echo "<br />";
  }
}
