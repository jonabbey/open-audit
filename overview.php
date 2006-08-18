<?php
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
$time_start = microtime_float();

include_once("include.php");

require("overview_viewdef.php");

echo "<td valign=\"top\">\n";
  echo "<div class=\"main_each\">";

//Table header
foreach($query_array as $view_master) {

    $view_count++;

    $count_items_category=0;;
    foreach($view_master["views"] as $view) {

        if($view["show"]=="y"){

            //Executing the Qeuery
            $sql=$view["sql"]."ORDER BY ".$view["sort"];
            $result = mysql_query($sql, $db);
            if(!$result) {die( "<br>".__("Fatal Error").":<br><br>".$sql."<br><br>".mysql_error()."<br><br>" );};
            $this_page_count = mysql_num_rows($result);

            //Table body
            if ($myrow = mysql_fetch_array($result)){

                //Table header
                unset($td_width);
                $body .= $view["headline"];
                $body .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
                $body .= " <tr>\n";

                //Generating Colgroup
                $i=0;
                if(isset($view_master["td_width"]) AND is_array($view_master["td_width"])){
                    $body .= "<colgroup>\n";
                    foreach($view["fields"] as $field) {
                        if(isset($view_master["td_width"][$i])) {
                            $body .= "<col width=\"".$view_master["td_width"][$i]."\">\n";
                            $i++;
                        }
                    }
                    $body .= "</colgroup>\n";
                }

                foreach($view["fields"] as $field) {
                    if($field["show"]!="n"){
                        $body .= "<td nowrap class=\"views_tablehead\">";
                         $body .= $field["head"];
                        $body .= " </th>\n";;
                    }
                }
                $body .= "<td nowrap class=\"views_tablehead\">&nbsp;</th>\n";;

                $body .= " </tr>\n";

                do{
                    $count_items_category++;

                    //Convert the array-values to local variables
                    while (list ($key, $val) = each ($myrow)) {
                        $$key=$val;
                    }

                    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
                    $body .= " <tr>\n";
                    foreach($view["fields"] as $field) {
                            if($field["show"]!="n"){

                            //Generating the link
                            //Does the field has an own link? Otherwise take the standard-link of the view
                            if($field["get"]["file"]!=""){
                                $get_array=$field["get"];
                            }else{
                                $get_array=$view["get"];
                            }

                            if(substr($get_array["file"],0,1)=="%"){
                                $value=substr($get_array["file"],1);
                                $link_file=$$value;
                            }else{
                                $link_file=$get_array["file"];
                            }
                            //Don't show the link if it's empty
                            if($link_file==""){
                                $field["link"]="n";
                            }
                            if($field["link"]=="y"){
                                unset($link_query);
                                @reset ($get_array["var"]);
                                while (list ($varname, $value) = @each ($get_array["var"])) {
                                    if(substr($value,0,1)=="%"){
                                        $value=substr($value,1);
                                        $value2=$$value;
                                    }else{
                                        $value2=$value;
                                    }
                                    $link_query.= $varname."=".urlencode($value2)."&amp;";
                                    //Don't show the link if a GET-variable is empty
                                    if($value2==""){
                                        $field["link"]="n";
                                    }
                                }
                            }
                            if($link_query!=""){
                                $url=parse_url($get_array["file"]);
                                if($url["query"]!=""){
                                    $link_separator="&amp;";
                                }else{
                                    $link_separator="?";
                                }
                                $link_uri=$link_file.$link_separator.$link_query;
                            }else{
                                $link_uri=$link_file;
                            }

                            //Special field-converting
                            unset($show_value);
                            if($field["name"]=="system_os_name"){
                                $show_value=determine_os($myrow[$field["name"]]);
                            }elseif($field["name"]=="system_timestamp"){
                                $show_value=return_date($myrow[$field["name"]]);
                            }elseif($field["name"]=="software_first_timestamp" OR
                                $field["name"]=="software_timestamp" OR
                                $field["name"]=="system_first_timestamp" OR
                                $field["name"]=="system_audits_timestamp"){
                                $show_value=return_date($myrow[$field["name"]]);
                            }elseif($field["name"]=="system_system_type"){
                                $show_value=determine_img($myrow["system_os_name"],$myrow[$field["name"]]);
                            }elseif($field["name"]=="other_ip_address"){
                                if($myrow[$field["name"]]==""){
                                    $show_value="Not-Networked";
                                }else{
                                    $show_value=$myrow[$field["name"]];
                                }
                            }elseif($field["name"]=="partition_free_space" OR
                                    $field["name"]=="partition_size"){
                                $show_value=$myrow[$field["name"]]." ".__("MB");
                            }else{
                                $show_value=$myrow[$field["name"]];
                            }

                            $body .= " <td bgcolor=\"" . $bgcolor . "\" style=\"padding-right:10px;\" align=\"".$field["align"]."\">\n";
                             if($field["link"]=="y"){
                                 $body .= "<a href=\"".$link_uri."\"target=\"".$get_array["target"]."\" title=\"".$get_array["title"]."\" onClick=\"".$get_array["onClick"]."\">\n";
                             }
                             $body .= $show_value;
                             if($field["link"]=="y"){
                                 $body .= " </a>\n";
                             }
                            $body .= " </td>\n";
                        }
                    }
                    $body .= " <td bgcolor=\"" . $bgcolor . "\">&nbsp;</td>\n";
                    $body .= " </tr>\n";
                }while ($myrow = mysql_fetch_array($result));

                $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
                $body .= "<tr>\n";
                 $body .= "<td bgcolor=\"" . $bgcolor . "\" $field_align style=\"padding-right:10px;\" colspan=\"10\">\n";
                  $body .= "&nbsp;";
                 $body .= "</td>\n";
                $body .= "</tr>\n";
                $body .= "</table>\n";
            }
        }
    }

    //Headline
    $buffer = "<div class=\"main_each\">";

    $buffer .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";
     $buffer .= "<tr\n>";
      $buffer .= "<td class=\"contenthead\" width=\"450\">\n";
       $buffer .= "<a href=\"javascript://\" onclick=\"switchUl('f".$view_count."');\">\n";
        $buffer .= $view_master["headline"]."\n";
       $buffer .= "</a>\n";
      $buffer .= "</td\n>";
      $buffer .= "<td class=\"contenthead\" width=\"30\" align=\"right\">\n";
       $buffer .= $count_items_category;
      $buffer .= "</td\n>";
      $buffer .= "<td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f".$view_count."');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td\n>";
     $buffer .= "</tr\n>";
    $buffer .= "</table\n>";

    $buffer .= "<div style=\"display:none;\" id=\"f".$view_count."\">\n";

    $buffer .= $body;

    $buffer .= "</div>";
    $buffer .= "</div>";

    echo $buffer;

    unset($body);
    unset($buffer);

}

  echo "</div>\n";

  echo __("This Page was generated in")." ".number_format((microtime_float()-$time_start),2)." ". __("Seconds").".";

 echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
