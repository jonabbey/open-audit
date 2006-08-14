<?php
include_once "include_config.php";

//New Translatation-System
if($language=="") $GLOBALS["language"]="english";
$language_file="./lang/".$GLOBALS["language"].".inc";
if(is_file($language_file)){
    include($language_file);
}else{
    die("Language-File not found: ".$language_file);
}
//Old-Translation-System
include "include_lang_english.php";

//if ($language <> "english"){ include "include_lang_" . $language . ".php"; }
include_once "include_win_type.php";
include_once "include_win_img.php";
include_once "include_functions.php";
include_once "include_col_scheme.php";

$jscript_count = 0;
if ($show_other_discovered == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_system_discovered == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_systems_not_audited == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_partition_usage == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_software_detected == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_patches_not_detected == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_detected_servers == 'y'){ $jscript_count = $jscript_count + 5; }

if ($page == "add_pc"){$use_pass = "n";}

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

if ($use_https == "y") {
        if ($_SERVER["SERVER_PORT"]!=443){ header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']); exit(); }
} else { echo $use_https;}


ob_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Open-AudIT</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" type="text/css" href="default.css" />
    <script type="text/javascript">
      /*<![CDATA[*/
      function IEHoverPseudo() {
        var navItems = document.getElementById("primary-nav").getElementsByTagName("li");
        for (var i=0; i<navItems.length; i++) {
          if(navItems[i].className == "menuparent") {
            navItems[i].onmouseover=function() { this.className += " over"; }
            navItems[i].onmouseout=function() { this.className = "menuparent"; }
          }
        }
      }

          window.onload = IEHoverPseudo;

      /*]]>*/
    </script>

  <?php if ($page == NULL) { ?>
    <script type="text/javascript">
      <!--
      function switchUl(id){
        if(document.getElementById){
          a=document.getElementById(id);
          a.style.display=(a.style.display!="none")?"none":"block";
        }
      }
      // -->
    </script>

  <?php } else {} ?>
  </head>
  <body>
<?php

$sub = "0";
$pc = "";

if (isset($_GET['pc'])) { $pc = $_GET['pc'];   } else { }
if (isset($_GET['sub'])) { $sub = $_GET['sub']; } else { $sub="all"; }
if (isset($_GET['sort'])) { $sort = $_GET['sort']; } else { $sort="system_name"; }

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
<table width="100%" border="0">
  <tr>
        <td colspan="3" class="main_each"><a href="index.php"><img src="images/logo.png" width="300" height="48" alt="" border="0"/></a></td>
  </tr>

  <tr>
    <td width="170" rowspan="12" valign="top">
      <ul id="primary-nav">
        <li class="menuparent"><a href="index.php"><?php echo __("Home"); ?></a></li>


<?php
if ($pc > "0") {
  $sql = "SELECT system_uuid, system_timestamp, system_name, system.net_ip_address, net_domain FROM system, network_card WHERE system_uuid = '$pc' OR system_name = '$pc' OR (net_mac_address = '$pc' AND net_uuid = system_uuid)";
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
        "<a href=\"system.php?pc=$pc&view=summary\">".
        "<span>&gt;</span>".
        $name.
        "</a>\n";

   echo "<ul>\n";
    reset ($menue_array["machine"]);
    while (list ($key_1, $topic_item) = each ($menue_array["machine"])) {
        echo "<li class=\"".$topic_item["class"]."\">";
         echo "<a href=\"".$topic_item["link"]."\">";
         if(is_array($topic_item["childs"])){
             echo "<span><img src=\"images/spacer.gif\" height=\"16\" width=\"0\" alt=\"\" />&gt;</span>";
         }
         echo "<img src=\"".$topic_item["image"]."\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" />&nbsp;";
         echo __($topic_item["name"]);
         echo "</a>\n";


         echo "<ul>\n";
         @reset ($topic_item["childs"]);
         while (list ($key_2, $child_item) = @each ($topic_item["childs"])) {
             echo "<li><a href=\"".$child_item["link"]."\" title=\"".$topic_item["title"]."\"><img src=\"".$child_item["image"]."\"  width=\"16\" height=\"16\" border=\"0\" />&nbsp;";
             echo __($child_item["name"]);
             echo "</a></li>\n";
         }
         echo "</ul>\n";
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
         echo "<a href=\"".$topic_item["link"]."\" title=\"".$topic_item["title"]."\">";
          if(is_array($topic_item["childs"])){
              echo "<span>&gt;</span>";
          }
          if($topic_item["image"]!=""){
              echo "<img src=\"".$topic_item["image"]."\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" />&nbsp;";
          }
          echo __($topic_item["name"]);
         echo "</a>\n";
        echo "<ul>\n";
         @reset ($topic_item["childs"]);
         while (list ($key_2, $child_item) = @each ($topic_item["childs"])) {
             echo "<li>";
              echo "<a href=\"".$child_item["link"]."\" title=\"".$child_item["title"]."\">";
               echo "<img src=\"".$child_item["image"]."\"  width=\"16\" height=\"16\" border=\"0\" alt=\"\" />&nbsp;";
               echo __($child_item["name"]);
              echo "</a>";
             echo "</li>\n";
         }
         echo "</ul>\n";
        echo "</li>\n";
        unset($topic_item["title"]);
    }

?>
      </ul>
     </td>