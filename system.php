<?php
include_once("include.php");
$count_system_max="10000";

// If you would like to have a new View, you have to modify 3 parts:
// -> include_menu_array.php: $menue_array
// -> system_viewdef_X.php: "Table and fields to select and show"
// -> option: system.php: "Special field-converting"

//Include the view-definition
$include_filename="system_viewdef_".$_REQUEST["view"].".php";
if(is_file($include_filename)){
    include_once($include_filename);
}else{
    die("File does not exists: ". $include_filename);
}
//Only one category?
if($_REQUEST["category"]!=""){
    $query_array["views"]=array($query_array["views"][$_REQUEST["category"]]);
}else{
}


echo "<td valign=\"top\">\n";
  echo "<div class=\"main_each\">";

  if($query_array["name"]!=""){
      echo "<table width=\"100%\" border=\"0\" height=\"70\"><tr><td width=\"60;\">\n";
       echo "<span class=\"contenthead\">\n";
        echo "<a href=\"".$_SERVER["PHP_SELF"]."?pc=".$_REQUEST["pc"]."&amp;view=".$_REQUEST["view"]."\">";
         echo "<b>".__($query_array["name"])."</b>\n";
        echo "</a>";
       echo "</span>\n";
      echo "</td></tr></table>\n";
 }

//Show each block
while (list ($viewname, $viewdef_array) = @each ($query_array["views"])) {

    $sql=$viewdef_array["sql"];
    $result=mysql_query($sql, $db);
    if(!$result) echo $sql;
    $this_page_count = mysql_num_rows($result);


        echo "<table width=\"100%\" border=\"0\" height=\"70\"><tr><td width=\"60;\">\n";
          echo "<img src=\"" .$viewdef_array["image"]. "\" alt=\"\" border=\"0\" width=\"48\" height=\"48\"  />\n";
         echo "</td><td>\n";
          echo "<span class=\"contenthead\">\n";
          if($_REQUEST["category"]==""){
              echo "<a href=\"".$_SERVER["PHP_SELF"]."?pc=".$_REQUEST["pc"]."&amp;view=".$_REQUEST["view"]."&amp;category=".$viewname."\">";
          }
           echo "<b>".__($viewdef_array["headline"])."</b>\n";
          if($_REQUEST["category"]==""){
              echo "</a>";
          }
          echo "</span>\n";
        echo "</td></tr></table>\n";

    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
    if ($myrow = mysql_fetch_array($result)){
        do{
            //Reset Variables
            $bgcolor=$bg2;
            //Convert the array-values to local variables
            while (list ($key, $val) = each ($myrow)) {
                $$key=$val;
            }

            foreach($viewdef_array["fields"] as $field){
                if($field["show"]!="n"){

                    //Generating the link, if its configured
                    if(is_array($field["get"]["var"])){
                        unset($link_query);
                        @reset ($field["get"]["var"]);
                        while (list ($varname, $value) = @each ($field["get"]["var"])) {
                            if(substr($value,0,1)=="%"){
                                $value=substr($value,1);
                                $value2=$$value;
                            }else{
                                $value2=$value;
                            }
                            $link_query.= $varname."=".urlencode($value2)."&";
                            //Don't show the link if a GET-variable is empty
                            if($value2==""){
                                unset($field["get"]);
                            }
                        }
                    }

                    //Special field-converting
                    SWITCH($field["name"]){
                        case "net_dhcp_server":
                            if($myrow[$field["name"]]=="none"){
                                $show_value=__("No");
                            }else{
                                $show_value=__("Yes")." / ".$myrow[$field["name"]];
                            }
                        break;
                        case "system_first_timestamp":
                        case "system_timestamp":
                        case "other_timestamp":
                            $show_value=return_date_time($myrow[$field["name"]]);
                        break;
                        case "system_memory":
                        case "video_adapter_ram":
                        case "hard_drive_size":
                        case "partition_size":
                            $show_value=number_format($myrow[$field["name"]])." MB";
                        break;
                        case "video_current_number_colours":
                            $show_value=(strlen(decbin($myrow[$field["name"]]))+1)." Bit";
                        break;
                        case "video_current_refresh_rate":
                            $show_value=$myrow[$field["name"]]." Hz";
                        break;
                        case "firewall_enabled_domain":
                        case "firewall_enabled_standard":
                        case "firewall_disablenotifications_standard":
                        case "firewall_donotallowexceptions_standard":
                        case "firewall_disablenotifications_domain":
                        case "firewall_donotallowexceptions_domain":
                            if($myrow[$field["name"]]=="1" OR $myrow[$field["name"]]=="0"){
                                if($myrow[$field["name"]]=="1"){
                                    $show_value=__("Yes");
                                }elseif($myrow[$field["name"]]=="0"){
                                    $show_value=__("No");
                                }
                            }else{
                                $show_value="Profile Not Detected";
                            }
                        break;
                        case "hard_drive_index":
                        default:
                            $show_value=$myrow[$field["name"]];
                        break;
                    }

                    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
                    echo "<tr>\n";
                     echo "<td bgcolor=\"" . $bgcolor . "\" align=\"".$field["align"]."\" style=\"padding-right:10px;\" width=\"200\">";
                       echo __($field["name"]);
                       if($field["name"]!=""){
                           echo ":";
                       }else{
                           echo "&nbsp;";
                       }
                      echo "</td>\n";
                     echo "<td bgcolor=\"" . $bgcolor . "\" align=\"".$field["align"]."\" style=\"padding-right:10px;\">";
                       if(is_array($field["get"])){
                           echo "<a href=\"".$field["get"]["file"]."?".$link_query."\" title=\"".$field["get"]["title"]."\" target=\"".$field["get"]["target"]."\">";
                           if($field["get"]["head"]==""){
                               echo $field["get"]["name"];
                           }else{
                               echo $field["get"]["head"];
                           }
                           echo "</a>";
                       }else{
                           echo $show_value;
                       }
                     echo "</td>\n";
                    echo "</tr>\n";

                }
            }
            $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
            echo "<tr><td bgcolor=\"" . $bgcolor . "\" style=\"padding-right:10px;\" colspan=\"2\">&nbsp;</td></tr>\n";
        }while ($myrow = mysql_fetch_array($result));
    } else {
        echo "<tr>\n";
         echo "<td bgcolor=\"#F1F1F1\" style=\"padding-right:10px;\" colspan=\"2\">";
          echo __("No Results");
         echo "</td>\n";
        echo "</tr>\n";
    }
    echo "</table>";

}

  echo "</div>\n";
 echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>