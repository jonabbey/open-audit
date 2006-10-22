<?php
if (isset($_POST['language_post'])) $GLOBALS["language"] = $_POST['language_post'];
include_once "include_lang.php";
 ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Open-AudIT Setup</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" type="text/css" href="default.css" />
  </head>
  <body>
  <div class="main_each">
    <img src="images/logo.png" width="300" height="48" alt="" border="0"/>
  </div>
  <div style="float: left; width: 200px">
  <div class="main_each">
<?php
  if(!isset($_POST['step']) or ($_POST['step'] == 1)) {
    echo "  <b>" . __("1. Choose language") . "</b><br />\n";
  } else {
    echo "  " . __("1. Choose language") . "<br />\n";
  }

  if (isset($_POST['step']) and $_POST['step'] == 2) {
    echo "  <b>" . __("2. Check Prerequisites") . "</b><br />\n";
  } else {
    echo "  " . __("2. Check Prerequisites") . "<br />\n";
  }

  if (isset($_POST['step']) and (($_POST['step'] == 3) or ($_POST['step'] == 3.5))) {
    echo "  <b>" . __("3. Setup database") . "</b><br />\n";
  } else {
    echo "  " . __("3. Setup database") . "<br />\n";
  }

  if (isset($_POST['step']) and $_POST['step'] == 4) {
    echo "  <b>" . __("4. Setup Completed") . "</b><br />\n";
  } else {
    echo "  " . __("4. Setup Completed") . "<br />\n";
  }
?>
  </div>
  </div>
  <div style="padding-left: 200px; padding-top: 1px;"><div class="main_each"><div style="width: 550px">
<?php
// Content below
  if(!isset($_POST['step']) or ($_POST['step'] == 1)) {
    step1ChooseLanguage();
  } else if ($_POST['step'] == 2) {
    step2CheckPrereq();
  } else if ($_POST['step'] == 3) {
    step3SetupDB();
  } else if ($_POST['step'] == 3.5) {
    step35SetupDB();
  } else if ($_POST['step'] == 4) {
    step4Finish();
  }
?>
  </div></div></div>
<?php
include "include_png_replace.php";
?>
</body>
</html>

<?php

// STEP 1
function step1ChooseLanguage() {
?>
  <span class="contenthead"><?php __("Setup") ?></span>
  <p><?php echo __("Welcome to the setup for Open-AudIT!") ?></p>
  <form method="post" action="setup.php" name="admin_config">
  <table border="0" cellpadding="0" cellspacing="0" class="content">
  <tr>
    <td><?php echo __("Choose your language:") ?></td>
    <td><select size="1" name="language_post" class="for_forms">
<?php
// Check for available languages
$handle=opendir('./lang/');
while ($file = readdir ($handle)) {
    if ($file != "." && $file != "..") {
        if(substr($file,strlen($file)-4)==".inc"){
            if($GLOBALS["language"] == substr($file,0,strlen($file)-4) ) $selected="selected"; else $selected="";
            echo "<option $selected>".substr($file,0,strlen($file)-4)."</option>\n";
        }
    }
}
closedir($handle);
?>
    </select></td>
  </tr>
  </table>
  <br />
  <input type="hidden" name="step" value="2" />
  <input type="submit" value="<?php echo __("Submit Language Choice") ?>" name="submit_button" />
  </form>
  <br />
<?php
}


// STEP 2
function step2CheckPrereq() {
  $failed = 0; // number of fails
  echo "<span class=\"contenthead\">" . __("Setup") . "</span>";
  echo "<p>" . __("Checking that the following files are writeable:") . "</p>";
  echo "<ul>";
  echo "<li>include_config.php ... ";
  $filename = "include_config.php";
  if (!file_exists($filename) or is_writable($filename)) {
    if (@fopen($filename, 'w')) {
      @fclose($handle);
      echo __("Success!") . "<br />";
    } else {
      $failed += 1;
      echo __("Failed.") . "<br />";
    }
  } else {
      $failed += 1;
    echo __("Failed.") . "<br />";
  }

  echo "<li>scripts/audit.config ... ";
  $filename = "scripts/audit.config";
  if (!file_exists($filename) or is_writable($filename)) {
    if (@fopen($filename, 'w')) {
      @fclose($handle);
      echo __("Success!") . "<br />";
    } else {
      $failed += 1;
      echo __("Failed.") . "<br />";
    }
  } else {
      $failed += 1;
    echo __("Failed.") . "<br />";
  }

  echo "</ul>";

  // Check for success
  if($failed == 0) {
?>
  <form method="post" action="setup.php" name="admin_config">
  <input type="hidden" name="language_post" value="<?php echo $_POST['language_post']; ?>" />
  <input type="hidden" name="step" value="3" />
  <input type="submit" value="<?php echo __("Continue") ?> >>" name="submit_button" />
  </form>
<?
  } else {
?>
  <p><?php echo __("For each failed file, check the permissions on the file. For linux, chmod them with permissions 646. You will need to create the file if it does not exist. When this is completed, press retry to verify the changes.") ?></p>
  <form method="post" action="setup.php" name="admin_config">
  <input type="hidden" name="language_post" value="<?php echo $_POST['language_post']; ?>" />
  <input type="hidden" name="step" value="2" />
  <input type="submit" value="<?php echo __("Retry") ?>" name="submit_button" />
  </form>
<?
  }  
}


// STEP 3
function step3SetupDB() {
?>
  <span class="contenthead"><?php echo __("Setup") ?></span>
  <p><?php echo __("To perform an automated setup, enter the details on the root account below. This user must have the privileges to create and modify users and databases.") ?></p>
  <p><?php echo __("By clicking 'Submit Credentials,' you are allowing Open-AudIT to create a user and database for use with Open-AudIT. This is the recommended configuration, as Open-AudIT does not need root privileges after installation.") ?></p>
  <hr />
  <?php echo __("Root User Credentials") ?>:<br />
  <form method="post" action="setup.php" name="admin_config">
  <table border="0" cellpadding="0" cellspacing="0" class="content">
  <tr><td><?php echo __("MySQL Server") ?>:&nbsp;</td><td><input type="text" name="mysql_server_post" size="12" value="localhost" class="for_forms"/></td></tr>
  <tr><td><?php echo __("MySQL Username") ?>:&nbsp;</td><td><input type="text" name="mysql_user_post" size="12" value="root" class="for_forms" /></td></tr>
  <tr><td><?php echo __("MySQL Password") ?>:&nbsp;</td><td><input type="password" name="mysql_password_post" size="12" value="" class="for_forms" /></td></tr>
  </table>
  <hr />
  <?php echo __("Database for Open-AudIT") ?>:<br />
  <table border="0" cellpadding="0" cellspacing="0" class="content">
  <tr><td><?php echo __("Database Name") ?>:&nbsp;</td><td><input type="text" name="mysql_new_db" size="12" value="openaudit" class="for_forms" /></td></tr>
  </table>
  <hr />
  <?php echo __("Credentials for Open-AudIT database") ?>:<br />
  <table border="0" cellpadding="0" cellspacing="0" class="content">
  <tr><td><?php echo __("New Username") ?>:&nbsp;</td><td><input type="text" name="mysql_new_user" maxlength="16" size="12" value="openaudit" class="for_forms" /></td></tr>
  <tr><td><?php echo __("New Password") ?>:&nbsp;</td><td><input type="password" name="mysql_new_pass" size="12" value="" class="for_forms" /></td></tr>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="content">
  <tr><td><input type="checkbox" name="bindlocal" value="y" checked="checked" /></td><td><?php echo __("Bind user to localhost.") ?></td></tr>
  <tr><td>&nbsp;</td><td><?php echo __("This option will allow this user to only connect from the localhost. It is recommended that you leave this checked unless your MySQL server is not on the same server as your web server.") ?></td></tr>
  </table>
  <br />
  <input type="hidden" name="language_post" value="<?php echo $_POST['language_post']; ?>" />
  <input type="hidden" name="step" value="3.5" />
  <input type="submit" value="<?php echo __("Submit Credentials") ?>" name="submit_button" />
  </form>
  <br />
<?php
}

// STEP 3.5
function step35SetupDB() {
    echo __("Connecting to the MySQL Server... ");
    $db = @mysql_connect($_POST['mysql_server_post'],$_POST['mysql_user_post'],$_POST['mysql_password_post']) or die('Could not connect: ' . mysql_error());
    echo __("Success!") . "<br />";
    $sql = "CREATE DATABASE `" . $_POST['mysql_new_db'] . "` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci";
    echo __("Creating the database... ");
    $result = mysql_query($sql, $db) or die('Could not create db: ' . mysql_error());
    echo __("Success!") ."<br />";
    $sql = "CREATE USER '" . $_POST['mysql_new_user'] . "'@";
    if ($_POST['bindlocal'] = 'y') {
      $sql .= "'localhost' ";
    } else {
      $sql .= "'%' ";
    }
    $sql .= "IDENTIFIED BY '" . $_POST['mysql_new_pass'] . "'";
    echo __("Creating the user... ");
    $result = mysql_query($sql, $db) or die('Could not create the user: ' . mysql_error());
    echo __("Success!") . "<br />";
    $sql = "GRANT SELECT , INSERT , UPDATE , DELETE , CREATE , DROP , INDEX , ALTER , CREATE TEMPORARY TABLES";
    $sql .= " , CREATE VIEW , SHOW VIEW , CREATE ROUTINE, ALTER ROUTINE, EXECUTE ";
    $sql .= "ON `" . $_POST['mysql_new_db'] . "`.* TO '" . $_POST['mysql_new_user'] . "'@";
    if ($_POST['bindlocal'] = 'y') {
      $sql .= "'localhost'";
    } else {
      $sql .= "'%'";
    }
    echo __("Granting user priveleges... ");
    $result = mysql_query($sql, $db) or die('Could not grant priveleges: ' . mysql_error());
    echo __("Success!") . "<br />";
    echo __("Switching connection to new user... ");
    mysql_close($db);
    $db = @mysql_connect($_POST['mysql_server_post'],$_POST['mysql_new_user'],$_POST['mysql_new_pass']) or die('Could not connect: ' . mysql_error());
    mysql_select_db($_POST['mysql_new_db']);
    echo __("Success!") . "<br />";

    // Load SQL contents to write to server
    echo __("Creating tables... ");
    $filename = "scripts/open_audit.sql";
    $handle = fopen($filename, "rb");
    $contents = fread($handle, filesize($filename));
    fclose($handle);
    $sql = stripslashes($contents);
    $sql2 = explode(";", $sql);
    foreach ($sql2 as $sql3) {
      $result = mysql_query($sql3 . ";");
    }
    echo __("Success!") . "<br />";
    mysql_close($db);

    // Write configuration file
    echo __("Writing configuration file... ");
    $filename = 'include_config.php';
    $content = returnConfig();
    if (!file_exists($filename) or is_writable($filename)) {
      $handle = @fopen($filename, 'w') or die(writeConfigHtml());
      @fwrite($handle, $content) or die(writeConfigHtml());
      @fclose($handle);
      echo __("Success!") . "<br />";
    } else {
      writeConfigHtml();
    }
?>
    <br /><br /><?php echo __("Setup has completed creating your database. Continue on to finish setup.") ?><br /><br />
    <form method="post" action="setup.php" name="options">
    <input type="hidden" name="step" value="4" />
    <input type="submit" value="<?php echo __("Finish Setup") ?> >>" name="submit_button" />
    </form>
<?php
}

function step4Finish() {
  echo "<span class=\"contenthead\">" . __("Setup Completed") . "</span>";
  echo "<p>" . __("Setup has completed. Please configure the audits and web interface through the Admin menu.") . "</p>";
  echo "<p>" . __("It is recommended that you setup usernames and passwords in the user management section of the admin menu.") . " (" . __("Coming Soon") . ")</p>";
  echo "<p>" . __("This file, setup.php, is no longer needed. Remove it to secure your installation of Open-AudIT.") . "</p>";
  echo "<a href=\"index.php\">" . __("Click here to enter Open-AudIT!") . "</a>";
}


// Write error message and give include_config.php details
function writeConfigHtml() {
  echo __("Failed.") . "<br /><br />";
  echo "<b>" . __("ERROR:") . "</b> " . __("Config file could not be written.") . "<br /><br />" . __("Please create a file named \"include_config.php\" in the openaudit directory with the contents below.  If on linux, set permissions of this file to 646.") . "<br /><br />";
  echo "<textarea rows=\"10\" name=\"add\" cols=\"60\" readonly=\"readonly\">";
  echo returnConfig();
  echo "</textarea><br /><br />";
  echo __("When this file is created, continue by clicking the button below.") . "<br /><br />\n";
?>
    <form method="post" action="setup.php" name="options">
    <input type="hidden" name="step" value="4" />
    <input type="submit" value="<?php echo __("Finish Setup") ?> >>" name="submit_button" />
    </form>
<?php
  echo "</div></div></div>\n";
  include "include_png_replace.php";
  echo "</body></html>";
  exit;
}

function returnConfig() {
  $content = "<";
  $content .= "?";
  $content .= "php \n";
  $content .= "\$mysql_server = '" . $_POST['mysql_server_post'] . "'; \n";
  $content .= "\$mysql_database = '" . $_POST['mysql_new_db'] . "'; \n";
  $content .= "\$mysql_user = '" . $_POST['mysql_new_user'] . "'; \n";
  $content .= "\$mysql_password = '" . $_POST['mysql_new_pass'] . "'; \n";
  $content .= "\$use_https = '';
// An array of allowed users and their passwords
// Make sure to set use_pass = \"n\" if you do not wish to use passwords
\$use_pass = 'n';
\$users = array(
  'admin' => 'Open-AudIT'
);\n";
  $content .= "// Config options for index.php
\$show_other_discovered = 'y';
\$other_detected = '3';

\$show_system_discovered = 'y';
\$system_detected = '3';

\$show_systems_not_audited = 'y';
\$days_systems_not_audited = '3';

\$show_partition_usage = 'y';
\$partition_free_space = '1000';

\$show_software_detected = 'y';
\$days_software_detected = '1';

\$show_patches_not_detected = 'y';
\$number_patches_not_detected = '5';

\$show_detected_servers = 'y';
\$show_detected_xp_av = 'y';
\$show_detected_rdp = 'y';

\$show_os = 'y';
\$show_date_audited = 'y';
\$show_type = 'y';
\$show_description = 'n';
\$show_domain = 'n';
\$show_service_pack = 'n';

\$count_system = '30';

\$round_to_decimal_places = '2';
 
\$language = '";
  $content .= $_POST['language_post'];
  $content .= "'; ";
  $content .= "?";
  $content .= ">\n\n";

  return $content;
}
?>
