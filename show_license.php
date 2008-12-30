<?php
/**
*
* @version $Id: show_license.php  30th Dec 2008
*
* @author The Open Audit Developer Team
* @objective Show License Page for Open Audit.
* @package open-audit (www.open-audit.org)
* @copyright Copyright (C) open-audit.org All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see ../gpl.txt
* Open-Audit is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See www.open-audit.org for further copyright notices and details.
*
*/ 

$page = "";
$extra = "";
$software = "";
$count = 0;
$total_rows = 0;
$latest_version = "08.12.10";

// Check for config, otherwise run setup
//@(include_once "include_config.php") OR die(header("Location: setup.php"));  // Modified by Nick Brown - don't want to actually include the file yet
if(!file_exists("include_config.php"))exit(header("Location: setup.php")); // Nick Brown - alternative method
include "include.php";



$software = GetGETOrDefaultValue("software","");
$sort = GetGETOrDefaultValue("sort","system_name");
$validate = GetGETOrDefaultValue("validate","n");


echo "<td id='CenterColumn' style='display:block'>\n";

// Now show the specified License in an iframe.

if(isset($license_text) AND $license_text!="") {
// We can alter things here if the file doesn't exist or whatever
// currently do nothing
} else {
// If no license specified, use the gpl.txt file. 

$license_text = "gpl.txt";
// echo "<img src=\"images/gplv3-88x31.png\" alt=\"\" style=\"border:0px;\" width=\"48\" height=\"48\"  />\n";              
}
echo "<center><h4><a href=\"index.php\">Click to close.</a></h4></center>";

echo "<iframe SRC=\"".$license_text."\" width=\"90%\" height=\"400\" 
framespacing=0 frameborder=no border=0 scrolling=auto name=license_frame longdesc=\"http://www.gnu.org/licenses/licenses.html#GPL\"></iframe>";

echo "<br><center>Open Audit License (".$license_text.") </center><br>";

if(isset($license_text) AND $license_text="gpl.txt") {
echo "<img src=\"images/gplv3-88x31.png\" alt=\"\" style=\"border:0px;\" width=\"88\" height=\"31\"  />\n";              
// We can alter things here if the file doesn't exist or whatever
// currently do nothing
} else {
 //
}




//gplv3-88x31.png
echo "</td>\n";
//
// Now put in the RH menu.
include "include_right_column.php";
?>