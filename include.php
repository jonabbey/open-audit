<?php
/**********************************************************************************************************
Module:	include.php

Description:
	This module is included by "index.php". Verifies authentication to the system and HTML to display the application header 
	and menu.

Recent Changes:

	[Nick Brown]	02/03/2009	Only a minor change - the "logout" link in the top right of the page now displays the user's 
	role (admin/user) as well as their name.
	[Nick Brown]	17/04/2009	Minor improvement to SQL query that retrieves audited system from DB
	[Nick Brown]	29/04/2009	Moved <link>s and <script>s into <head> from "admin_config.php". Minor changes to ensure 
	valid XHTML markup. Moved javascript functions into external file "include.js"
	[Nick Brown]	01/05/2009	Incldued "application_class.php" to provide access to the global $TheApp object. 
	[Chad Sikorra]	20/11/2009	Check the filename of the current page to determine what css/js to include

**********************************************************************************************************/
include_once "application_class.php";
include_once "include_config.php";
include_once "include_lang.php";
include_once "include_functions.php";
include "include_dell_warranty_functions.php"; // Added by Andrew Hull to allow us to grab Dell Warranty details from the Dell website
include_once "include_col_scheme.php";

//die(var_dump($TheApp));

$page = GetVarOrDefaultValue($page);

if ($page == "add_pc")
{
	$use_pass = "n";
	$_SESSION["username"] = "Anonymous";
	$_SESSION["role"] = "none";
}
else
{
	if (GetVarOrDefaultValue($use_https) == "y")
	{
		if ($_SERVER["SERVER_PORT"]!=443){RedirectToUrl("https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);}
	}
  if (GetVarOrDefaultValue($use_ldap_login) == 'y') {include "include_ldap_login.php";}
}

if ($use_pass != "n") {
  // If there's no Authentication header, exit
  if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="PHP Secured"');
    exit('This page requires authentication');
  }
  // If the user name doesn't exist, exit
  if (!isset($users[$_SERVER['PHP_AUTH_USER']])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="PHP Secured"');
    exit('Unauthorized!');
  }
  // Is the password doesn't match the username, exit
  if ($users[$_SERVER['PHP_AUTH_USER']] != md5($_SERVER['PHP_AUTH_PW']))
  {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="PHP Secured"');
    exit('Unauthorized!');
  }
} else {}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Open-AudIT</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/> 
    <!--<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link media="screen" rel="stylesheet" type="text/css" href="default.css" />
    <link media="print" rel="stylesheet" type="text/css" href="defaultprint.css" />
		<!--[if lt IE 7]><link media="screen" rel="stylesheet" type="text/css" href="iefix.css" /><![endif]-->
		<!--[if lt IE 7]><link media="print" rel="stylesheet" type="text/css" href="iefix.css" /><![endif]-->
		<script type='text/javascript' src="javascript/ajax.js"></script>
		<script type='text/javascript' src="javascript/include.js"></script>

    <?php 
      // Used to only included pieces of jquery/jquery ui that the page needs.
      if(isset($JQUERY_UI)){
        echo '<script type="text/javascript" src="javascript/jquery/jquery.js"></script>'."\n";
        echo '<script type="text/javascript" src="javascript/jquery/jquery-bgiframe.js"></script>'."\n";
        echo '<link media="screen" rel="stylesheet" type="text/css" href="jquery-ui-theme.css" />'."\n";
        foreach($JQUERY_UI as $script) {
          echo '<script type="text/javascript" src="javascript/jquery/jquery-ui-'.$script.'.js"></script>'."\n";
          if ( file_exists('jquery-ui-'.$script.'.css') ) {
            echo '<link media="screen" rel="stylesheet" type="text/css" href="jquery-ui-'.$script.'.css" />'."\n";
          }
        }
      }

      // Only include certain files if it's a page that needs it.
      switch(basename($_SERVER["PHP_SELF"])){
        case 'list.php':
        case 'system.php':
        case 'system_graphs.php':
          echo '<script type="text/javascript" src="javascript/list-system.js"></script>'."\n";
          break;
        case 'admin_config.php':
          echo '<script type="text/javascript" src="javascript/PopupMenu.js"></script>'."\n".
               '<script type="text/javascript" src="javascript/admin_config.js"></script>'."\n".
               '<link media="screen" rel="stylesheet" type="text/css" href="admin_config.css" />'."\n";
          break;
      }
    ?>

  </head>
  <body onload="IEHoverPseudo();">
<?php

$pc = GetGETOrDefaultValue("pc", "");
$sub = GetGETOrDefaultValue("sub", "all");
$sort = GetGETOrDefaultValue("sort", "system_name");
$mac = $pc;

if ($page <> "setup"){
  $db = mysql_connect($mysql_server,$mysql_user,$mysql_password) or die('Could not connect: ' . mysql_error());
  mysql_select_db($mysql_database,$db);
  $SQL = "SELECT config_value FROM config WHERE config_name = 'version'";
  $result = mysql_query($SQL, $db);

  if ($myrow = mysql_fetch_array($result)){
    $version = $myrow["config_value"];
  } else {}
} else {
  $version = "0.1.00";
}
?>
<table width="100%">
  <tr>
    <td colspan="3" class="main_each"><a href="index.php"><img class="logo" src="images/logo.png" alt=""/></a>
<?php
	if (isset($use_ldap_login) and ($use_ldap_login == 'y')) 
	{echo "<a class='npb_ldap_logout' href=\"ldap_logout.php\">".__("Logout ").$_SESSION["username"]." [".$_SESSION["role"]."]</a>";}
?>		
		</td>
  </tr>

  <tr>
    <td style="width:170px;" rowspan="12" valign="top" id="nav">
      <ul id="primary-nav">
        <li><a href="index.php"><?php echo __("Home"); ?></a></li>
        

<?php
if ($pc > "0") {
	// This query has less joins and is syntactically simpler than previous - 17/04/2009	[Nick Brown]
	$sql = "SELECT system_uuid, system_timestamp, system_name, system.net_ip_address, net_domain
					FROM system 
					JOIN network_card ON net_uuid = system_uuid
					WHERE (
					net_mac_address ='$pc'
					OR system_uuid = '$pc'
					OR system_name = '$pc'
					)
					LIMIT 1";
					
  $result = mysql_query($sql, $db);
  $myrow = mysql_fetch_array($result);
  $timestamp = $myrow["system_timestamp"];
  $GLOBAL["system_timestamp"]=$timestamp;
  $pc = $myrow["system_uuid"];
  $ip = $myrow["net_ip_address"];
  $name = $myrow['system_name'];
  $domain = $myrow['net_domain'];

  //Menu-Entries for the selected PC
  
  require_once("include_menu_array.php");
  echo "<li class=\"menuparent\">".
        "<a href=\"system.php?pc=$pc&amp;view=summary\">".
        "<span>&gt;</span>".
        $name.
        "</a>\n";

   echo "<ul>\n";
    reset ($menue_array["machine"]);
    while (list ($key_1, $topic_item) = each ($menue_array["machine"])) {
        if (isset($topic_item["class"])) {
          echo "<li class=\"".$topic_item["class"]."\">";
        } else {
          echo "<li>";
        }

        echo "<a href=\"".$topic_item["link"]."\"";
        if (isset($topic_item["css-class"])) {
          echo " class=\"".$topic_item["css-class"]."\"";
        }
        echo ">";
        if(isset($topic_item["childs"]) AND is_array($topic_item["childs"])){
          echo "<span><img src=\"images/spacer.gif\" height=\"16\" width=\"0\" alt=\"\" />&gt;</span>";
        }
        echo "<img src=\"".$topic_item["image"]."\" style=\"border:0px;\" alt=\"\" />&nbsp;";
        echo __($topic_item["name"]);
        echo "</a>\n";

        if(isset($topic_item["childs"]) AND is_array($topic_item["childs"])){
          echo "<ul>\n";
          @reset ($topic_item["childs"]);
          while (list ($key_2, $child_item) = @each ($topic_item["childs"])) {
            echo "<li><a href=\"".$child_item["link"]."\"";
            if (isset($topic_item["title"])) {
              echo " title=\"".$topic_item["title"]."\"";
            }
            echo "><img src=\"".$child_item["image"]."\" style=\"border:0px;\" alt=\"\" />&nbsp;";
            echo __($child_item["name"]);
            echo "</a></li>\n";
          }
          echo "</ul>\n";
        }
        echo "</li>\n";
    
    }
    
   echo "</ul>\n";
  echo "</li>\n";
}
    //Normal Menu-Entries
    require_once("include_menu_array.php");
    reset ($menue_array["misc"]);
    while (list ($key_1, $topic_item) = each ($menue_array["misc"])) {
        echo "<li class=\"".$topic_item["class"]."\">";
         echo "<a href=\"".$topic_item["link"]."\"";
          if(isset($topic_item["title"])) {
            echo " title=\"".$topic_item["title"]."\"";
          }
         echo ">";
          if(is_array($topic_item["childs"])){
              echo "<span>&gt;</span>";
          }
          if(isset($topic_item['image']) AND $topic_item["image"]!=""){
              echo "<img src=\"".$topic_item["image"]."\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" />&nbsp;";
          }
          echo __($topic_item["name"]);
         echo "</a>";
        echo "<ul>\n";

        if(is_array($topic_item["childs"])){
            @reset ($topic_item["childs"]);
            while (list ($key_2, $child_item) = @each ($topic_item["childs"])) {

                echo "<li>";
                 echo "<a href=\"".$child_item["link"]."\" title=\"".$child_item["title"]."\">";
                  if(isset($child_item["childs"]) AND is_array($child_item["childs"])){
                      echo "<span>&gt;</span>";
                  }
                  echo "<img src=\"".$child_item["image"]."\"  width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" />&nbsp;";
                  echo __($child_item["name"]);
                 echo "</a>";

                 if(isset($child_item["childs"]) AND is_array($child_item["childs"])){
                    echo "<ul>\n";
                    @reset ($child_item["childs"]);
                    while (list ($key_3, $child_item_2) = @each ($child_item["childs"])) {
                        echo "<li>";
                         echo "<a href=\"".$child_item_2["link"]."\" title=\"".$child_item_2["title"]."\">";
                          echo "<img src=\"".$child_item_2["image"]."\"  width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" />&nbsp;";
                          echo __($child_item_2["name"]);
                         echo "</a>";
                        echo "</li>\n";
                    }
                    echo "</ul>\n";
                 }
                echo "</li>\n";
            }
        }

         echo "</ul>\n";
        echo "</li>\n";
    unset($topic_item["title"]);
    }
 echo "</ul>\n";
  echo "</td>\n";
?>
     
