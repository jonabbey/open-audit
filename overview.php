<?php
include_once("include.php");

require("overview_viewdef.php");

echo "<td valign=\"top\">\n";
  echo "<div class=\"main_each\">";

//Table header
foreach($query_array["views"] as $view) {
    if($view["show"]=="y"){

        $view_count++;

        //Executing the Qeuery
        $sql=$view["sql"]."ORDER BY ".$view["sort"];
        $result = mysql_query($sql, $db);
        if(!$result) {die( "<br>".__("Fatal Error").":<br><br>".$sql."<br><br>".mysql_error()."<br><br>" );};
        $this_page_count = mysql_num_rows($result);

        //Headline
        echo "<div class=\"main_each\">";

        echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";
         echo "<tr\n>";
          echo "<td class=\"contenthead\" width=\"450\">\n";
           echo "<a href=\"javascript://\" onclick=\"switchUl('f".$view_count."');\">\n";
            echo __($view["headline"])."\n";
           echo "</a>\n";
          echo "</td\n>";
          echo "<td class=\"contenthead\" width=\"30\" align=\"right\">\n";
           echo $this_page_count;
          echo "</td\n>";
          echo "<td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f".$view_count."');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td\n>";
         echo "</tr\n>";
        echo "</table\n>";

        echo "<div style=\"display:none;\" id=\"f".$view_count."\">\n";

        //Table header
        echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
        echo " <tr>\n";
        foreach($view["fields"] as $field) {
            echo "<td nowrap style=\"padding-right:10px; font-weight:bold; border-bottom: 1px solid #000000;\">";
             echo __($field["name"]);
            echo " </th>\n";;
        }
        echo " </tr>\n";

        //Table body
        if ($myrow = mysql_fetch_array($result)){
            do{

                //Convert the array-values to local variables
                while (list ($key, $val) = each ($myrow)) {
                    $$key=$val;
                }

                $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
                echo " <tr>\n";
                foreach($view["fields"] as $field) {

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
                    if($field["name"]=="other_ip_address"){
                        if($myrow[$field["name"]]==""){
                            $myrow[$field["name"]]="Not-Networked";
                        }
                    }elseif($field["name"]=="system_first_timestamp"){
                        $myrow[$field["name"]]=return_date_time($myrow[$field["name"]]);
                    }
                    echo " <td bgcolor=\"" . $bgcolor . "\" style=\"padding-right:10px;\">\n";
                     if($field["link"]=="y"){
                         echo "<a href=\"".$link_uri."\"target=\"".$get_array["target"]."\" title=\"".__($get_array["title"])."\" onClick=\"".$get_array["onClick"]."\">\n";
                     }
                     echo $myrow[$field["name"]];
                     if($field["link"]=="y"){
                         echo " </a>\n";
                     }
                    echo " </td>\n";
                }
                echo " </tr>\n";
            }while ($myrow = mysql_fetch_array($result));
        }
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        echo "<tr>\n";
         echo "<td bgcolor=\"" . $bgcolor . "\"$field_align style=\"padding-right:10px;\" colspan=\"10\">\n";
          echo "&nbsp;";
         echo "</td>\n";
        echo "<tr>\n";

        echo "</table>\n";
        echo "</div>";
        echo "</div>";
    }
}



  echo "</div>\n";
 echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
