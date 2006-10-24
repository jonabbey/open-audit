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

?>
    <br /><?php echo __("Upgrade complete."); ?>
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
