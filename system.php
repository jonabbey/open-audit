<?php
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
$time_start = microtime_float();

include_once("include.php");

// If you would like to have a new View, you have to modify 3 parts:
// -> include_menu_array.php: $menue_array
// -> system_viewdef_X.php: "Table and fields to select and show"
// -> option: include_functions.php: special_field_converting()

//Include the view-definition
if(isset($_REQUEST["view"]) AND $_REQUEST["view"]!=""){
    $include_filename = "system_viewdef_".$_REQUEST["view"].".php";
}else{
    $include_filename = "system_viewdef_summary.php";
}
if(is_file($include_filename)){
    include_once($include_filename);
    $viewdef_array=$query_array;
}else{
    die(__("FATAL: Could not find view")." ".$include_filename);
}

if(isset($_GET["pc"]) AND $_GET["pc"]!=""){
    $pc=$_GET["pc"];
}

//Convert GET[category] to an array
if(isset($_REQUEST["category"]) AND $_REQUEST["category"]!=""){
    $array_category=explode(",",$_REQUEST["category"]);
}

//If someone wants to edit Systems Manual-Data, one entry has to created IF there is none
//This is because the fields are only shown, if the sql-guery gets an result
if(isset($_REQUEST["pc"]) AND
   isset($_REQUEST["view"]) AND $_REQUEST["view"]=="summary" AND
   isset($_REQUEST["category"]) AND $_REQUEST["category"]=="manual" AND
   isset($_REQUEST["edit"]) AND $_REQUEST["edit"]=="1" )
   {
    $sql_man="SELECT system_man_id FROM `system_man` WHERE `system_man_uuid` = '".$_REQUEST["pc"]."'; ";
    $result_man=mysql_query($sql_man, $db);
    $man_count = mysql_num_rows($result_man);
    if($man_count<1){
        $sql_man="INSERT INTO `system_man` ( `system_man_uuid` ) VALUES ( '".$_REQUEST["pc"]."' ); ";
        $result_man=mysql_query($sql_man, $db);
        if(!$result_man) { echo "<br>".__("Fatal Error").":<br><br>".$sql_man."<br><br>".mysql_error()."<br><br>";
                       echo "<pre>";
                       print_r($_REQUEST);
                       die();
                     };
    }
}

echo "<td valign=\"top\">\n";
  echo "<div class=\"main_each\">";

  if(isset($query_array["name"]) AND $query_array["name"]!=""){
      echo "<table width=\"100%\" border=\"0\" style=\"height: 70px\"><tr><td style=\"width:60px;\">\n";
       echo "<span class=\"contenthead\">\n";
         echo "<span class=\"contenthead\">\n";
          echo htmlspecialchars($query_array["name"]);
          if(isset($_REQUEST["headline_addition"])) {echo htmlspecialchars($_REQUEST["headline_addition"]);}
         echo "</span>\n";
       echo "</span>\n";
      echo "</td></tr></table>\n";
 }

//Delete undisplayed categories from $query_array, if a certain category is given
if(isset($array_category) AND is_array($array_category) AND $_REQUEST["category"]!=""){
    reset($query_array["views"]);
    while (list ($viewname, $viewdef_array) = @each ($query_array["views"])) {
        if(!in_array($viewname, $array_category)){
            unset($query_array["views"][$viewname]);
        }
    }
}

//Show each Category
reset($query_array["views"]);
while (list ($viewname, $viewdef_array) = @each ($query_array["views"])) {

    //Executing Query
    $sql=$viewdef_array["sql"];
    $result=mysql_query($sql, $db);
    if(!$result) { echo "<br>".__("Fatal Error").":<br><br>".$sql."<br><br>".mysql_error()."<br><br>";
                   echo "<pre>";
                   echo "REQUEST:<br>";
                   print_r($_REQUEST);
                   echo "VIEWDEF:<br>";
                   print_r($viewdef_array);
                   die();
                 };
    $this_page_count = mysql_num_rows($result);


        echo "<table border=\"0\" style=\"height: 70px\">\n";
         echo "<tr>\n";
          //Image
          if(isset($viewdef_array["image"]) AND $viewdef_array["image"]!=""){
              echo "<td style=\"width: 60px;\">\n";
               echo "<img src=\"" .$viewdef_array["image"]. "\" alt=\"\" border=\"0\" width=\"48\" height=\"48\"  />\n";
              echo "</td>\n";
          }
          //Headline
          if(isset($viewdef_array["headline"]) AND $viewdef_array["headline"]!=""){
              echo "<td>\n";
              echo "<span class=\"contentsubtitle\">\n";
              if(isset($_REQUEST["category"]) AND $_REQUEST["category"]==""){
                  echo "<a href=\"".$_SERVER["PHP_SELF"]."?pc=".$_REQUEST["pc"]."&amp;view=".$_REQUEST["view"]."&amp;category=".$viewname."\">";
              }
               echo $viewdef_array["headline"]."\n";
              if(isset($_REQUEST["category"]) AND $_REQUEST["category"]==""){
                  echo "</a>";
              }
              echo "</span>\n";
              echo "<td>\n";
          }
          echo "</tr>\n";
        echo "</table>\n";

    echo "<form id=\"v".$viewname."\" method=\"post\" action=\"system_post.php\">\n";

    if(isset($_REQUEST["pc"])){
        echo "<input type=\"hidden\" name=\"pc\" value=\"".$_REQUEST["pc"]."\" />";
    }
    if(isset($_REQUEST["category"])){
        echo "<input type=\"hidden\" name=\"category\" value=\"".$_REQUEST["category"]."\" />";
    }
    if(isset($_REQUEST["view"])){
        echo "<input type=\"hidden\" name=\"view\" value=\"".$_REQUEST["view"]."\" />";
    }
    if(isset($_REQUEST["other"])){
        echo "<input type=\"hidden\" name=\"other\" value=\"".$_REQUEST["other"]."\" />";
    }
    if(isset($_REQUEST["monitor"])){
        echo "<input type=\"hidden\" name=\"monitor\" value=\"".$_REQUEST["monitor"]."\" />";
    }

    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";

    //IF Horizontal Table-Layout
    if(isset($viewdef_array["table_layout"]) AND $viewdef_array["table_layout"]=="horizontal"){
        echo "<tr>\n";
        foreach($viewdef_array["fields"] as $field){
            echo "<td class=\"system_tablehead\">\n";
             echo $field["head"];
            echo "</td>\n";
        }
        echo "</tr>\n";
    }

    //Reset Background
    $bgcolor=$bg2;
    if ($myrow = mysql_fetch_array($result)){
        do{
            //Convert the array-values to local variables
            while (list ($key, $val) = each ($myrow)) {
                $$key=$val;
            }

            //IF Horizontal Table-Layout
            if(isset($viewdef_array["table_layout"]) AND $viewdef_array["table_layout"]=="horizontal"){
                $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
                echo "<tr>\n";
            }

            $edit_category="0";
            foreach($viewdef_array["fields"] as $field){
                if(!isset($field["show"]) OR $field["show"]!="n"){

                    //Generating the link, if its configured
                    if(isset($field["get"]["var"]) AND is_array($field["get"]["var"])){
                        unset($link_query);
                        $link_query = "";
                        @reset ($field["get"]["var"]);
                        while (list ($varname, $value) = @each ($field["get"]["var"])) {
                            if(substr($value,0,1)=="%"){
                                $value=substr($value,1);
                                $value2=$$value;
                            }else{
                                $value2=$value;
                            }
                            $link_query.= $varname."=".urlencode($value2)."&amp;";
                            //Don't show the link if a GET-variable is empty
                            if($value2==""){
                                unset($field["get"]);
                            }
                        }
                    }

                    $show_value = special_field_converting($myrow, $field, $db, "system");

                    //IF Horizontal Table-Layout
                    if(isset($viewdef_array["table_layout"]) AND $viewdef_array["table_layout"]=="horizontal"){
                        if(!isset($field["align"])) $field["align"]="left";
                        echo "<td bgcolor=\"" . $bgcolor . "\" align=\"".$field["align"]."\" class=\"system_tablebody_left\" >\n";
                         echo $show_value;
                        echo "</td>\n";
                    }else{
                        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
                        echo "<tr>\n";
                         if (!isset($field["align"])) { $field["align"] = "left"; }
                         echo "<td bgcolor=\"" . $bgcolor . "\" align=\"".$field["align"]."\" class=\"system_tablebody_left\" >";
                           echo $field["head"];
                           if($field["head"]!=""){
                               echo ":";
                           }else{
                               echo "&nbsp;";
                           }

                          echo "</td>\n";
                         echo "<td bgcolor=\"" . $bgcolor . "\" align=\"".$field["align"]."\" class=\"system_tablebody_right\">";
                           if(isset($field["get"]) AND is_array($field["get"])){
                               echo "<a href=\"".$field["get"]["file"]."?".$link_query."\" title=\"".$field["get"]["title"]."\"";
                               if(isset($field["get"]["target"])) {
                                 echo " target=\"" . $field["get"]["target"] . "\"";
                               }
                               echo ">";
                               echo $field["get"]["head"];
                               echo "</a>";
                           }else{
                               //Form-Fields
                               if(isset($field["edit"]) AND $field["edit"]=="y" AND isset($_REQUEST["edit"])){
                                   if(!isset($field["edit_type"])) $field["edit_type"]="text";
                                   SWITCH($field["edit_type"]){
                                       case "textarea":
                                           echo "<textarea name=\"".$field["name"]."\" style=\"width:300px\" class=\"for_forms\">".$show_value."</textarea>\n";
                                       break;
                                       case "select":
                                           echo "<select name=\"".$field["name"]."\" style=\"width:300px\" class=\"for_forms\">\n";
                                            echo "<option value=\"\">".__("None")."</option>\n";
                                            $result2 = mysql_query($field["edit_sql"], $db);
                                            if ($myrow2 = mysql_fetch_array($result2)){
                                                do {
                                                    if($myrow2[0]==$myrow[$field["name"]]) $selected="selected"; else $selected=" ";
                                                    echo "<option value=\"".$myrow2[0]."\" $selected>".$myrow2[1]."</option>\n";
                                                } while ($myrow2 = mysql_fetch_array($result2));
                                            }
                                           echo "</select>\n";
                                       break;
                                       case "text":
                                           echo "<input type=\"text\" style=\"width:300px\" name=\"".$field["name"]."\" value=\"".$show_value."\" class=\"for_forms\" />";
                                       break;
                                   }
                               }else{
                                   echo $show_value;
                               }
                           }
                         echo "</td>\n";
                        echo "</tr>\n";
                    }
                }
            }
            //IF Horizontal Table-Layout
            if(isset($viewdef_array["table_layout"]) AND $viewdef_array["table_layout"]=="horizontal"){
                echo "</tr>\n";
            }

            //Links to Manufacturer
            if(isset($myrow["system_vendor"]) AND $myrow["system_vendor"]!="" AND ($viewname=="summary" OR $viewname=="chassis")){
                $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
                echo "<tr>\n";
                 echo "<td bgcolor=\"" . $bgcolor . "\">\n";
                  echo __("Links to Manufacturer");
                  echo ": &nbsp;";
                  echo "<a href=\"#\" onClick=\"alert('".__("Regarding to Modell # and Serial #")."');\">?</a>";
                 echo "</td>\n";
                 echo "<td bgcolor=\"" . $bgcolor . "\">\n";

                     if ($myrow["system_vendor"] == "Dell Inc." || $myrow["system_vendor"] == "Dell Computer Corporation") {
                       if(isset($myrow["system_id_number"]) AND $myrow["system_id_number"]!=""){
                           echo " <a href='http://support.dell.com/support/topics/global.aspx/support/my_systems_info/en/details?c=us&amp;cs=usbsdt1&amp;servicetag=" . $myrow["system_id_number"] . "' target=_blank>".__("Warranty Information")."</a>";
                           echo " / ";
                           echo " <a href='http://support.dell.com/support/downloads/index.aspx?c=us&amp;l=en&amp;s=gen&amp;servicetag=" . $myrow["system_id_number"] . "' target=_blank>".__("Drivers &amp; Software")."</a>";
                           $links_to_manu=1;
                       }
                     } elseif ($myrow["system_vendor"] == "Compaq") {
                       if(isset($myrow["system_id_number"]) AND $myrow["system_id_number"]!=""){
                           echo " <a href='http://www4.itrc.hp.com/service/ewarranty/warrantyResults.do?BODServiceID=NA&amp;RegisteredPurchaseDate=&amp;country=GB&amp;productNumber=&amp;serialNumber1=" . $myrow["system_id_number"] . "' target=_blank>".__("Warranty Information")."</a>";
                           echo " / ";
                           echo " <a href='http://h20180.www2.hp.com/apps/Lookup?h_lang=en&amp;h_cc=uk&amp;cc=uk&amp;h_page=hpcom&amp;lang=en&amp;h_client=S-A-R135-1&amp;h_pagetype=s-002&amp;h_query=" . $myrow["system_id_number"] . "' target=_blank>".__("Drivers &amp; Software")."</a>";
                           $links_to_manu=1;
                       }
                     } elseif ($myrow["system_vendor"] == "IBM") {
                       if(isset($myrow["system_id_number"]) AND $myrow["system_id_number"]!=""){
                           echo " <a href='http://www-307.ibm.com/pc/support/site.wss/quickPath.do?quickPathEntry=" . $myrow["system_model"] . "' target=_blank>".__("Product Page")."</a>";
                           echo " / ";
                           $links_to_manu=1;
                       }
                       if(isset($myrow["system_model"]) AND $myrow["system_model"]!= "" AND isset($myrow["system_id_number"]) AND $myrow["system_id_number"]!=""){
                           echo " <a href='http://www-307.ibm.com/pc/support/site.wss/warrantyLookup.do?type=".substr($myrow["system_model"],0,4)."&amp;serial=".$myrow["system_id_number"]."&amp;country=897&amp;iws=off' target=_blank>".__("Warranty Information")."</a>";
                           $links_to_manu=1;
                       }
                     } elseif ($myrow["system_vendor"] == "Gateway") {
                       if(isset($myrow["system_id_number"]) AND $myrow["system_id_number"]!=""){
                           echo " <a href='http://support.gateway.com/support/allsysteminfo.asp?sn=" . $myrow["system_id_number"] . "' target=_blank>".__("Support Page")."</a>";
                           $links_to_manu=1;
                       }
                     }
                     if(!isset($links_to_manu)){
                       echo __("No Links configured for this Manufacturer");
                     }

                 echo "</td>\n";
                echo "</tr>\n";
            }
            //IF Horizontal Table-Layout
            if(isset($viewdef_array["table_layout"]) AND $viewdef_array["table_layout"]=="horizontal"){}else{
                $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
                echo "<tr><td bgcolor=\"$bgcolor\" class=\"system_tablebody_right\" colspan=\"2\">&nbsp;</td></tr>\n";
            }
        }while ($myrow = mysql_fetch_array($result));
    } else {
        echo "<tr>\n";
         echo "<td bgcolor=\"$bg1\" style=\"padding-right:10px;\" colspan=\"2\">";
          echo __("No Results");
         echo "</td>\n";
        echo "</tr>\n";

        echo "<tr><td bgcolor=\"$bg2\" style=\"padding-right:10px;\" colspan=\"2\">&nbsp;</td></tr>\n";
    }

     //Edit- and Submit-Button
     if(isset($viewdef_array["edit"]) AND $viewdef_array["edit"]=="y"){
         echo "<tr>\n";
          echo "<td>\n";
           if(isset($_REQUEST["edit"]) AND $_REQUEST["edit"]==1){
        echo "<input type=\"submit\" name=\"save\" value=\"".__("Save")."\" />";
           }else{
        echo "<input type=\"button\" name=\"edit\" value=\"Edit\"";
        echo "onClick=\"window.location.href='".$_SERVER["PHP_SELF"]."?";
        if(isset($_REQUEST["pc"])){
            echo "pc=".$_REQUEST["pc"]."&amp;";
        }elseif(isset($_REQUEST["other"])){
            echo "other=".$_REQUEST["other"]."&amp;";
        }elseif(isset($_REQUEST["monitor"])){
            echo "monitor=".$_REQUEST["monitor"]."&amp;";
        }else{
            die(__("FATAL: There's no ID-variable to identify the item. I.e pc or other"));
        }
        echo "view=".$_REQUEST["view"]."&amp;category=".$viewname."&amp;edit=1';\" />";
           }
          echo "</td>\n";
         echo "</tr>\n";

         echo "<tr><td class=\"system_tablebody_right\" colspan=\"2\">&nbsp;</td></tr>\n";
    }

    //IF Horizontal Table-Layout
    if(isset($viewdef_array["table_layout"]) AND $viewdef_array["table_layout"]=="horizontal"){
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        echo "<tr><td bgcolor=\"$bgcolor\" class=\"system_tablebody_right\" colspan=\"10\">&nbsp;</td></tr>\n";
    }

    echo "</table>";
    echo "</form>\n";
}

    echo "<form method=\"post\" name=\"form_export\" action=\"system_export.php\">\n";
    echo "<input type=\"hidden\" name=\"view\" value=\"".$_REQUEST["view"]."\" />\n";
    if(isset($_REQUEST["category"])){
        echo "<input type=\"hidden\" name=\"category\" value=\"".$_REQUEST["category"]."\" />\n";
    }
    if(isset($_REQUEST["pc"])){
        echo "<input type=\"hidden\" name=\"pc\" value=\"".$_REQUEST["pc"]."\" />\n";
    }
    if(isset($_REQUEST["other"])){
        echo "<input type=\"hidden\" name=\"other\" value=\"".$_REQUEST["other"]."\" />\n";
    }
    if(isset($_REQUEST["monitor"])){
        echo "<input type=\"hidden\" name=\"monitor\" value=\"".$_REQUEST["monitor"]."\" />\n";
    }
    echo "<br><a href=\"#\" onClick=\"document.form_export.submit();\">".__("Export this Page to PDF")." (BETA)</a>\n";
    echo "</form>\n";


  echo "</div>\n";

  echo __("This Page was generated in")." ".number_format((microtime_float()-$time_start),2)." ". __("seconds").".";

 echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>