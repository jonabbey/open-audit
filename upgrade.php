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

$sql = "ALTER TABLE `network_card` ADD COLUMN `net_ip_enabled` varchar(10) NOT NULL default '' AFTER `net_uuid`,
                                       ADD COLUMN `net_index` varchar(10) NOT NULL default '' AFTER `net_ip_enabled`,
                                       ADD COLUMN `net_service_name` varchar(30) NOT NULL default '' AFTER `net_index`,
                                       ADD COLUMN `net_dhcp_lease_obtained` varchar(14) NOT NULL default '' AFTER `net_dhcp_server`,
                                       ADD COLUMN `net_dhcp_lease_expires` varchar(14) NOT NULL default '' AFTER `net_dhcp_lease_obtained`,
                                       ADD COLUMN `net_dns_server_3` varchar(30) NOT NULL default '' AFTER `net_dns_server_2`,
                                       ADD COLUMN `net_dns_domain` varchar(100) NOT NULL default '' AFTER `net_dns_server_3`,
                                       ADD COLUMN `net_dns_domain_suffix` varchar(100) NOT NULL default '' AFTER `net_dns_domain`,
                                       ADD COLUMN `net_dns_domain_suffix_2` varchar(100) NOT NULL default '' AFTER `net_dns_domain_suffix`,
                                       ADD COLUMN `net_dns_domain_suffix_3` varchar(100) NOT NULL default '' AFTER `net_dns_domain_suffix_2`,
                                       ADD COLUMN `net_dns_domain_reg_enabled` varchar(10) NOT NULL default '' AFTER `net_dns_domain_suffix_3`,
                                       ADD COLUMN `net_dns_domain_full_reg_enabled` varchar(10) NOT NULL default '' AFTER `net_dns_domain_reg_enabled`,
                                       ADD COLUMN `net_ip_address_2` varchar(30) NOT NULL default '' AFTER `net_ip_subnet`,
                                       ADD COLUMN `net_ip_subnet_2` varchar(30) NOT NULL default '' AFTER `net_ip_address_2`,
                                       ADD COLUMN `net_ip_address_3` varchar(30) NOT NULL default '' AFTER `net_ip_subnet_2`,
                                       ADD COLUMN `net_ip_subnet_3` varchar(30) NOT NULL default '' AFTER `net_ip_address_3`,
                                       ADD COLUMN `net_wins_lmhosts_enabled` varchar(10) NOT NULL default '' AFTER `net_wins_secondary`,
                                       ADD COLUMN `net_netbios_options` varchar(10) NOT NULL default '' AFTER `net_wins_lmhosts_enabled`,
                                       ADD COLUMN `net_connection_id` varchar(255) NOT NULL default '' AFTER `net_manufacturer`,
                                       ADD COLUMN `net_connection_status` varchar(30) NOT NULL default '' AFTER `net_connection_id`,
                                       ADD COLUMN `net_speed` varchar(10) NOT NULL default '' AFTER `net_connection_status`,
                                       ADD COLUMN `net_gateway_metric` varchar(10) NOT NULL default '' AFTER `net_gateway`,
                                       ADD COLUMN `net_gateway_2` varchar(100) NOT NULL default '' AFTER `net_gateway_metric`,
                                       ADD COLUMN `net_gateway_metric_2` varchar(10) NOT NULL default '' AFTER `net_gateway_2`,
                                       ADD COLUMN `net_gateway_3` varchar(100) NOT NULL default '' AFTER `net_gateway_metric_2`,
                                       ADD COLUMN `net_gateway_metric_3` varchar(10) NOT NULL default '' AFTER `net_gateway_3`,
                                       ADD COLUMN `net_ip_metric` varchar(10) NOT NULL default '' AFTER `net_gateway_metric_3`;";

upgrade ($version,"07.12.09", $sql);

$sql = "ALTER TABLE `system` ADD COLUMN `system_last_boot` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `date_virus_def` ;

ALTER TABLE `hard_drive` ADD COLUMN `hard_drive_status` VARCHAR( 10 ) NOT NULL DEFAULT '' AFTER `hard_drive_pnpid` ;

CREATE TABLE `scheduled_task` (
  `sched_task_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `sched_task_uuid` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `sched_task_name` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `sched_task_next_run` VARCHAR( 50 ) NOT NULL DEFAULT '',
  `sched_task_status` VARCHAR( 50 ) NOT NULL DEFAULT '',
  `sched_task_last_run` VARCHAR( 50 ) NOT NULL DEFAULT '',
  `sched_task_last_result` VARCHAR( 50 ) NOT NULL DEFAULT '',
  `sched_task_creator` VARCHAR( 50 ) NOT NULL DEFAULT '',
  `sched_task_schedule` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `sched_task_task` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `sched_task_state` VARCHAR( 10 ) NOT NULL DEFAULT '',
  `sched_task_runas` VARCHAR( 50 ) NOT NULL DEFAULT '',
  `sched_task_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  `sched_task_first_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`sched_task_id`),
  KEY `id` (`sched_task_uuid`),
  KEY `id2` (`sched_task_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `environment_variable` (
  `env_var_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `env_var_uuid` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `env_var_name` VARCHAR( 50 ) NOT NULL DEFAULT '',
  `env_var_value` VARCHAR( 250 ) NOT NULL DEFAULT '',
  `env_var_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  `env_var_first_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`env_var_id`),
  KEY `id` (`env_var_uuid`),
  KEY `id2` (`env_var_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `event_log` (
  `evt_log_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `evt_log_uuid` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `evt_log_name` VARCHAR( 50 ) NOT NULL DEFAULT '',
  `evt_log_file_name` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `evt_log_file_size` INT( 11 ) NOT NULL DEFAULT '0',
  `evt_log_max_file_size` INT( 11 ) NOT NULL DEFAULT '0',
  `evt_log_overwrite` VARCHAR( 30 ) NOT NULL DEFAULT '',
  `evt_log_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  `evt_log_first_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`evt_log_id`),
  KEY `id` (`evt_log_uuid`),
  KEY `id2` (`evt_log_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `ip_route` (
  `ip_route_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `ip_route_uuid` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `ip_route_destination` VARCHAR( 20 ) NOT NULL DEFAULT '',
  `ip_route_mask` VARCHAR( 20 ) NOT NULL DEFAULT '',
  `ip_route_metric` VARCHAR( 10 ) NOT NULL DEFAULT '',
  `ip_route_next_hop` VARCHAR( 20 ) NOT NULL DEFAULT '',
  `ip_route_protocol` VARCHAR( 10 ) NOT NULL DEFAULT '',
  `ip_route_type` VARCHAR( 10 ) NOT NULL DEFAULT '',
  `ip_route_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  `ip_route_first_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`ip_route_id`),
  KEY `id` (`ip_route_uuid`),
  KEY `id2` (`ip_route_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `pagefile` (
  `pagefile_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `pagefile_uuid` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `pagefile_name` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `pagefile_initial_size` INT( 11 ) NOT NULL DEFAULT '0',
  `pagefile_max_size` INT( 11 ) NOT NULL DEFAULT '0',
  `pagefile_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  `pagefile_first_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`pagefile_id`),
  KEY `id` (`pagefile_uuid`),
  KEY `id2` (`pagefile_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `motherboard` (
  `motherboard_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `motherboard_uuid` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `motherboard_manufacturer` VARCHAR( 50 ) NOT NULL DEFAULT '',
  `motherboard_product` VARCHAR( 50 ) NOT NULL DEFAULT '',
  `motherboard_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  `motherboard_first_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`motherboard_id`),
  KEY `id` (`motherboard_uuid`),
  KEY `id2` (`motherboard_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `onboard_device` (
  `onboard_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `onboard_uuid` VARCHAR( 100 ) NOT NULL DEFAULT '',
  `onboard_description` VARCHAR( 50 ) NOT NULL DEFAULT '',
  `onboard_type` VARCHAR( 20 ) NOT NULL DEFAULT '',
  `onboard_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  `onboard_first_timestamp` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`onboard_id`),
  KEY `id` (`onboard_uuid`),
  KEY `id2` (`onboard_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

upgrade ($version,"08.02.01", $sql);

$sql = "ALTER TABLE `system` ADD COLUMN `iis_version` varchar(10) NOT NULL default '' AFTER `date_system_install`;

        ALTER TABLE `iis` ADD COLUMN `iis_site_state` varchar(20) NOT NULL default '' AFTER `iis_secure_port`,
                          ADD COLUMN `iis_site_app_pool` varchar(100) NOT NULL default '' AFTER `iis_site_state`,
                          ADD COLUMN `iis_site_anonymous_user` varchar(100) NOT NULL default '' AFTER `iis_site_app_pool`,
                          ADD COLUMN `iis_site_anonymous_auth` varchar(10) NOT NULL default '' AFTER `iis_site_anonymous_user`,
                          ADD COLUMN `iis_site_basic_auth` varchar(10) NOT NULL default '' AFTER `iis_site_anonymous_auth`,
                          ADD COLUMN `iis_site_ntlm_auth` varchar(10) NOT NULL default '' AFTER `iis_site_basic_auth`,
                          ADD COLUMN `iis_site_ssl_en` varchar(10) NOT NULL default '' AFTER `iis_site_ntlm_auth`,
                          ADD COLUMN `iis_site_ssl128_en` varchar(10) NOT NULL default '' AFTER `iis_site_ssl_en`;

        CREATE TABLE `iis_web_ext` (
          `iis_web_ext_id` int(10) unsigned NOT NULL auto_increment,
          `iis_web_ext_uuid` varchar(100) NOT NULL default '',
          `iis_web_ext_path` varchar(100) NOT NULL default '',
          `iis_web_ext_desc` varchar(100) NOT NULL default '',
          `iis_web_ext_access` varchar(20) NOT NULL default '',
          `iis_web_ext_timestamp` bigint(20) unsigned NOT NULL default '0',
          `iis_web_ext_first_timestamp` bigint(20) unsigned NOT NULL default '0',
          PRIMARY KEY  (`iis_web_ext_id`),
          KEY `id` (`iis_web_ext_uuid`),
          KEY `id2` (`iis_web_ext_timestamp`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

        CREATE TABLE `auto_updating` (
          `au_id` int(10) unsigned NOT NULL auto_increment,
          `au_uuid` varchar(100) NOT NULL default '',
          `au_gpo_configured` varchar(10) NOT NULL default '',
          `au_enabled` varchar(10) NOT NULL default '',
          `au_behaviour` varchar(100) NOT NULL default '',
          `au_sched_install_day` varchar(20) NOT NULL default '',
          `au_sched_install_time` varchar(10) NOT NULL default '',
          `au_use_wuserver` varchar(10) NOT NULL default '',
          `au_wuserver` varchar(100) NOT NULL default '',
          `au_wustatusserver` varchar(100) NOT NULL default '',
          `au_target_group` varchar(100) NOT NULL default '',
          `au_elevate_nonadmins` varchar(10) NOT NULL default '',
          `au_auto_install` varchar(10) NOT NULL default '',
          `au_detection_frequency` varchar(10) NOT NULL default '',
          `au_reboot_timeout` varchar(10) NOT NULL default '',
          `au_noautoreboot` varchar(10) NOT NULL default '',
          `au_timestamp` bigint(20) unsigned NOT NULL default '0',
          `au_first_timestamp` bigint(20) unsigned NOT NULL default '0',
          PRIMARY KEY  (`au_id`),
          KEY `id` (`au_uuid`),
          KEY `id2` (`au_timestamp`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";


upgrade ($version,"08.04.15", $sql);

$sql = "ALTER TABLE `software_licenses` CHANGE `license_purchase_number` `license_purchase_number` INT( 10 ) NOT NULL DEFAULT '0';";

upgrade ($version,"08.05.02", $sql);

$sql = "ALTER TABLE `network_card` ADD COLUMN `net_driver_provider` varchar(100) NOT NULL default '' AFTER `net_ip_metric`,
                                   ADD COLUMN `net_driver_version` varchar(20) NOT NULL default '' AFTER `net_driver_provider`,
                                   ADD COLUMN `net_driver_date` varchar(10) NOT NULL default '' AFTER `net_driver_version`;";

upgrade ($version,"08.05.19", $sql);

$sql ="DROP TABLE IF EXISTS `ad_computers`;
        CREATE TABLE `ad_computers` (
        `guid` varchar(45) NOT NULL,	# Computer object GUID from AD as a string
        `cn` varchar(45) NOT NULL,		# Computer object CN value from AD
          `audit_timestamp` varchar(45) NOT NULL,	# last audit timestamp
          `usnchanged` int(10) unsigned NOT NULL,	# Computer object usnchanged value from AD
          `first_audit_timestamp` varchar(45) NOT NULL, # First audit timestamp
          `ou_id` varchar(45) NOT NULL,	# Reference to ad_ous.ou_id (the OU that owns this computer account)
          `description` varchar(45) default NULL,	# Computer object description value from AD
          `os` varchar(45) default NULL,	# Computer object operatingsystem value from AD
          `service_pack` varchar(45) default NULL,	# Computer object operatingsystemservicepack value from AD
          `dn` varchar(255) NOT NULL,	# Computer object distinguishedname value from AD
          PRIMARY KEY  (`guid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;


        DROP TABLE IF EXISTS `ad_domains`;
        CREATE TABLE  `ad_domains` (
          `guid` varchar(45) NOT NULL,	# Unique ID for the domain (intend to use the domain AD GUID at some point)
          `default_nc` varchar(45) NOT NULL,	# Domain defaultnamingcontext
          `fqdn` varchar(45) NOT NULL,	# Domain FQDN
          `ldap_server` varchar(45) NOT NULL,	# LDAP host server
          `ldap_user` varchar(45) NOT NULL,	# LDAP login (AD Account)
          `ldap_password` varchar(45) NOT NULL,	# LDAP password 
          `netbios_name` varchar(45) NOT NULL,	# Domain NetBIOS name
          PRIMARY KEY  (`guid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;


        DROP TABLE IF EXISTS `ad_ous`;
        CREATE TABLE  `ad_ous` (
          `ou_id` varchar(45) NOT NULL,	# Unique ID for the OU (intend to use the OU AD GUID at some point)
          `ou_dn` varchar(255) default NULL,	# OU object distinguished name
          `ou_domain_guid` varchar(45) default NULL,	# Reference to ad_domains.guid (the domain that owns this OU)
          `ou_audit_timestamp` varchar(45) default NULL,	# Audit timestamp for this OU
          `include_in_audit` tinyint(1) default NULL,	# Flag to include/exclude OU from audit
          PRIMARY KEY  (`ou_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;


        DROP TABLE IF EXISTS `ad_users`;
        CREATE TABLE  `ad_users` (
          `guid` varchar(45) NOT NULL,
          `cn` varchar(45) NOT NULL,
          `audit_timestamp` varchar(45) NOT NULL,
          `usnchanged` int(10) unsigned NOT NULL,
          `first_audit_timestamp` varchar(45) NOT NULL,
          `ou_id` varchar(45) NOT NULL,
          `description` varchar(45) default NULL,
          `department` varchar(45) default NULL,
          `users_dn` varchar(255) NOT NULL,
          PRIMARY KEY  (`guid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;";

upgrade ($version,"08.05.21", $sql);

$sql = "ALTER TABLE `mapped` ADD COLUMN `mapped_username` varchar(100) NOT NULL default '' AFTER `mapped_size`,
                             ADD COLUMN `mapped_connect_as` varchar(100) NOT NULL default '' AFTER `mapped_username`;

        ALTER TABLE `motherboard` ADD COLUMN `motherboard_cpu_sockets` INT( 10 ) NOT NULL DEFAULT '0' AFTER `motherboard_product`,
                                  ADD COLUMN `motherboard_memory_slots` INT( 10 ) NOT NULL DEFAULT '0' AFTER `motherboard_cpu_sockets`;

        ALTER TABLE `groups` CHANGE `groups_members` `groups_members` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";

upgrade ($version,"08.06.06", $sql);

$sql = "ALTER TABLE `memory` ADD COLUMN `memory_tag` varchar(256) NOT NULL default '' AFTER `memory_speed`";

upgrade ($version,"08.07.23", $sql);

$sql = "DROP TABLE IF EXISTS `ad_computers`;
        DROP TABLE IF EXISTS `ad_domains`;
        DROP TABLE IF EXISTS `ad_ous`;
        DROP TABLE IF EXISTS `ad_users`;

        DROP TABLE IF EXISTS `ldap_computers`;
        CREATE TABLE  `ldap_computers` (
          `ldap_computers_guid` varchar(45) NOT NULL,
          `ldap_computers_cn` varchar(255) NOT NULL,
          `ldap_computers_timestamp` varchar(45) NOT NULL,
          `ldap_computers_first_timestamp` varchar(45) NOT NULL,
          `ldap_computers_path_id` varchar(45) NOT NULL,
          `ldap_computers_description` varchar(255) default NULL,
          `ldap_computers_os` varchar(255) default NULL,
          `ldap_computers_service_pack` varchar(255) default NULL,
          `ldap_computers_dn` varchar(255) NOT NULL,
          PRIMARY KEY  (`ldap_computers_guid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

        DROP TABLE IF EXISTS `ldap_connections`;
        CREATE TABLE  `ldap_connections` (
          `ldap_connections_id` int(10) unsigned NOT NULL auto_increment,
          `ldap_connections_nc` varchar(255) NOT NULL,
          `ldap_connections_fqdn` varchar(255) NOT NULL,
          `ldap_connections_server` varchar(255) NOT NULL,
          `ldap_connections_user` varchar(45) NOT NULL,
          `ldap_connections_password` varchar(45) NOT NULL,
          `ldap_connections_name` varchar(45) NOT NULL,
          `ldap_connections_schema` varchar(45) NOT NULL,
          PRIMARY KEY  (`ldap_connections_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

        DROP TABLE IF EXISTS `ldap_paths`;
        CREATE TABLE  `ldap_paths` (
          `ldap_paths_id` int(10) unsigned NOT NULL auto_increment,
          `ldap_paths_dn` varchar(255) default NULL,
          `ldap_paths_connection_id` varchar(45) default NULL,
          `ldap_paths_timestamp` varchar(45) default NULL,
          `ldap_paths_audit` tinyint(1) default NULL,
          PRIMARY KEY  (`ldap_paths_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

        DROP TABLE IF EXISTS `ldap_users`;
        CREATE TABLE  `ldap_users` (
          `ldap_users_guid` varchar(45) NOT NULL,
          `ldap_users_cn` varchar(255) NOT NULL,
          `ldap_users_timestamp` varchar(45) NOT NULL,
          `ldap_users_first_timestamp` varchar(45) NOT NULL,
          `ldap_users_path_id` varchar(45) NOT NULL,
          `ldap_users_description` varchar(255) default NULL,
          `ldap_users_department` varchar(255) default NULL,
          `ldap_users_dn` varchar(255) NOT NULL,
          PRIMARY KEY  (`ldap_users_guid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

        DROP TABLE IF EXISTS `log`;
        CREATE TABLE  `log` (
          `log_id` int(10) unsigned NOT NULL auto_increment,
          `log_timestamp` varchar(45) NOT NULL,
          `log_message` varchar(1024) NOT NULL,
          `log_severity` int(10) unsigned NOT NULL,
          `log_module` varchar(128) NOT NULL,
          `log_function` varchar(128) NOT NULL,
          PRIMARY KEY  (`log_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

upgrade ($version,"08.10.08", $sql);

// Add indexes to improve performance of queries used by index.php - this can take longer than standard script timeout
set_time_limit (300);
$sql = "ALTER TABLE `software` ADD INDEX `Index3`(`software_first_timestamp`);
        ALTER TABLE `software` ADD INDEX `Index4`(`software_name`);
        ALTER TABLE `system` ADD INDEX `Index3`(`system_first_timestamp`);";

upgrade ($version,"08.10.09", $sql);

set_time_limit (30);

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
