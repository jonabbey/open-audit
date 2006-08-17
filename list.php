<?php
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
$time_start = microtime_float();

include_once("include.php");
$count_system_max="10000";

// If you would like to have a new View, you have to modify 3 parts:
// -> include_menu_array.php: $menue_array
// -> list_viewdef_X.php: "Table and fields to select and show"
// -> option: list.php: "Special field-converting"

//Include the view-definition
$include_filename="list_viewdef_".$_REQUEST["view"].".php";
if(is_file($include_filename)){
    include_once($include_filename);
    $viewdef_array=$query_array;
}else{
    die("File does not exists: ". $include_filename);
}


    //ORDER, SORT and LIMIT
    if ($_REQUEST['sort']!="") {$sort = $_REQUEST['sort'];} else {$sort = $query_array["sort"];}
    if ($_REQUEST['dir']!="")  {$dir = $_REQUEST['dir'];} else {$dir = $query_array["dir"];}
    if ($dir=="ASC")  { $new_dir = "DESC"; }else{ $new_dir = "ASC";}

    if ($_REQUEST["show_all"]!=""){ $count_system = $count_system_max; } else {}
    if (isset($_REQUEST["page_count"])){ $page_count = $_REQUEST["page_count"]; } else { $page_count = 0;}
    $page_prev = $page_count - 1;
    if ($page_prev < 0){ $page_prev = 0; } else {}
    $page_next = $page_count + 1;
    $page_current = $page_count;
    $page_count = $page_count * $count_system;


    //Preparing the Qeuery
    $sql_query=$query_array["sql"];
    //SORT
    $sql_sort=" ORDER BY " . $sort . " " . $dir;
    //LIMIT
    if($_REQUEST['show_all']!=1){
        $sql_limit=" LIMIT " . $page_count . "," . $count_system;
    }
    //WHERE
    if($_REQUEST["filter"]){
        if(!preg_match("/WHERE/i",$sql_query)){
            $sql_where.="WHERE 1 ";
        }
        $sql_where.=" AND ( 1 ";
        @reset($_REQUEST["filter"]);
        while (list ($filter_var, $filter_val) = @each ($_REQUEST["filter"])) {
            if($filter_val!=""){
                $sql_where.= " AND `".$filter_var."` LIKE '".$filter_val."%' ";
                $filter_query=1;
            }
        }
        $sql_where.=" )";
        //Extract GROUP BY from $sql_query
        if(ereg("GROUP BY",$sql_query)){
            $sql_query_tmp=explode("GROUP BY",$sql_query);
            $sql_query=$sql_query_tmp[0];
            $sql_groupby=" GROUP BY ".$sql_query_tmp[1];
        }
    }

    //Show Searchboxes, if search is used on the calling page
    if($filter_query==1){
        $style_searchboxes="display:inline;";
        $image_searchboxes="images/arrows_up.gif";
    }else{
        $style_searchboxes="display:none;";
        $image_searchboxes="images/arrows_down.gif";

    }

    //Executing the Qeuery
    $sql=$sql_query."\n".$sql_where."\n".$sql_groupby."\n".$sql_sort."\n".$sql_limit;
    $result = mysql_query($sql, $db);
    if(!$result) {die( "<br>".__("Fatal Error").":<br><br>".$sql."<br><br>".mysql_error()."<br><br>" );};
    $this_page_count = mysql_num_rows($result);

    //Getting the count of all available items
    $sql_all = $sql_query."\n".$sql_where."\n".$sql_groupby."\n".$sql_sort;
    $result_all = mysql_query($sql_all, $db);
    if(!$result_all) {die( "<br>".__("Fatal Error").":<br><br>".$sql."<br><br>".mysql_error()."<br><br>" );};
    $all_page_count = mysql_num_rows($result_all);

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";

echo "<form method=\"post\" name=\"form_nav\" action=\"".htmlentities($_SERVER["REQUEST_URI"])."\">\n";

  //Calculating the page-count-vars in headline
  if( ($page_count+$count_system)>$all_page_count ){
      $show_page_count_to=$all_page_count;
  }else{
      $show_page_count_to=$page_count+$count_system;
  }

  echo "<table width=\"100%\" border=\"0\" style=\"height: 70px\"><tr><td>\n";
   echo "<span class=\"contenthead\"><b>";
   echo __($query_array["headline"])." ".htmlspecialchars($_REQUEST["headline_addition"]);
   echo " (".($page_count+1)."-".$show_page_count_to."/".$all_page_count.")</b></span>\n";
  echo "</td><td align=\"right\" nowrap>\n";

  //Navigation-buttons
  //Previous
  if($page_count!=0){
      echo "<a href=\"#\" onClick=\"set_form_field('page_count', '".$page_prev."'); submit_form();\">";
        echo "<img src=\"images/go-prev.png\" alt=\"".__("Previous").": $count_system\" title=\"".__("Previous").": $count_system\" border=\"0\" width=\"16\" height=\"16\" />";
      echo "</a>\n";
  }else{
    echo "<img src=\"images/go-prev-disabled.png\" alt=\"".__("Disabled")."\" title=\"".__("Disabled")."\" border=\"0\" width=\"16\" height=\"16\" />\n";
  }

  //All
  if($all_page_count>=$count_system OR $count_system==$count_system_max ){
      if($_REQUEST['show_all']!=1){
          echo "<a href=\"#\" onClick=\"set_form_field('show_all', '1'); set_form_field('page_count', '0'); submit_form();\">";
            echo "<img src=\"images/go-all.png\" alt=\"\" title=\"".__("All")."\" border=\"0\" width=\"16\" height=\"16\" />";
          echo "</a>\n";
      }else{
          echo "<a href=\"#\" onClick=\"set_form_field('show_all', ''); set_form_field('page_count', '0'); submit_form();\">";
            echo "<img src=\"images/go-less.png\" alt=\"".__("By Page")."\" title=\"".__("By Page")."\" border=\"0\" width=\"16\" height=\"16\" />";
          echo "</a>\n";
      }
  }else{
      echo "<img src=\"images/go-all-disabled.png\" alt=\"".__("Disabled")."\"border=\"0\" width=\"16\" height=\"16\" />\n";
  }
  //Next
  if(($page_count+$count_system)<=$all_page_count){
      echo "<a href=\"#\" onClick=\"set_form_field('page_count', '".$page_next."'); submit_form();\">";
        echo "<img src=\"images/go-next.png\" alt=\"".__("Next")."$next_page_count\" title=\"".__("Next")."$next_page_count\" border=\"0\" width=\"16\" height=\"16\" />";
      echo "</a>\n";
  }else{
    echo "<img src=\"images/go-next-disabled.png\" alt=\"".__("Disabled")."\" title=\"".__("Disabled")."\" border=\"0\" width=\"16\" height=\"16\" />\n";
  }

  echo "<p style=\"height:10px; margin:0px;\"></p>";

  //Direct jumping to pages
  if($all_page_count>$count_system){
      for ($i = 0; $i <= $all_page_count; $i=$i+$count_system) {

          if( ($i<=($count_system*4)) OR ($i>=($all_page_count-($count_system*3))) ){
              if($i==$page_count){ $style_for_direct_jump="color:red;";}else{$style_for_direct_jump="";};
              $goto_page=($i/$count_system+1);
              echo "&nbsp;<a href=\"#\" onClick=\"set_form_field('page_count', '".($i/$count_system)."'); submit_form();\" style=\"$style_for_direct_jump\" title=\"".__("Go to Page")." ".$goto_page."\">";
              echo $goto_page;
              echo "</a>";
          }else{
              if($dots_for_direct_jump_is_sown!=1){
                  $dots_for_direct_jump_is_sown=1;
                  echo "...";
              }
          }
      }
      unset($style_for_direct_jump);
  }
  echo "&nbsp;&nbsp;\n";
  echo "<input type=\"text\" name=\"page_count_tmp\" value=\"".($page_current+1)."\" style=\"width:16px;\" />\n";
  echo "<input type=\"button\" name=\"tmp_submit\" value=\">\" style=\"width:16px;\" onClick=\"set_form_field('page_count', (document.forms['form_nav'].elements['page_count_tmp'].value-1)); submit_form();\" />\n";
  echo "</td></tr></table>\n";

  echo "<div style=\"margin: 5px;\"></div>";

//Table header
foreach($viewdef_array["fields"] as $field) {
    if($field["show"]=="y"){
        $field_width = "";
        $field_height = "";
        if ($field["width"] <> "") {$field_width = " width=\"".$field["width"]."\"";}
        if ($field["height"] <> "") {$field_height = " height=\"".$field["height"]."\"";}
        $headline_1 .= "<td nowrap style=\"padding-right:10px; font-weight:bold; border-bottom: 1px solid #000000;\">";
        if($field["sort"]!="n"){
            $headline_1 .= "<a href=\"#\" onClick=\"set_form_field('sort', '".$field["name"]."'); set_form_field('dir', '".$new_dir."'); set_form_field('page_count', '0'); submit_form();\" title=\"".__("Sort by").": ".__($field["head"]).", ".__("Direction").": ".__($new_dir)."\">";
        }
        $headline_1 .= __($field["head"]);
        if($field["sort"]!="n"){
            $headline_1 .= "</a>\n";
        }
        if($sort==$field["name"]){
            $headline_1 .= "<img src=\"images/".strtolower($dir).".png\" style=\"padding-bottom:3px;\" alt=\"\" />";
        }
        $headline_1 .= "</td>\n";

        $headline_2 .= "<td style=\"padding-top:5px; padding-bottom:5px;\">\n";
         if($field["search"]!="n"){
             $count_searchboxes++;
             $headline_2 .= "<div id=\"searchboxes_".$count_searchboxes."\" style=\"$style_searchboxes\">";
             $headline_2 .= "<input type=\"text\" name=\"filter[".$field["name"]."]\" value=\"".$_POST["filter"][$field["name"]]."\" style=\"width:90%;\" />\n";
             $headline_2 .= "</div>";
         }
        $headline_2 .= "</td>\n";
    }
}

 //Button to Show and Hide the searchboxes
 $headline_1 .= "<td width=\"20\" style=\"border-bottom: 1px solid #000000;\">";
 $headline_1 .= "<a href=\"#\" onClick=\"show_searchboxes();\" id=\"link_searchboxes\"><img src=\"$image_searchboxes\" id=\"arrows_searchboxes\" border=\"0\" width=\"15\" height=\"21\" alt=\"\" /></a>";
 $headline_1 .= "</td>";

 $count_searchboxes++;
 $headline_2 .= "<td style=\"padding-top:5px; padding-bottom:5px;\">\n";
 $headline_2 .= "<div id=\"searchboxes_".$count_searchboxes."\" style=\"$style_searchboxes\">";
 $headline_2 .= "<input type=\"submit\" name=\"filter_submit\" value=\">\" style=\"width:16px;\" onClick=\"set_form_field('page_count', '0');\" />\n";
 $headline_2 .= "</div>";
 $headline_2 .= "</td>\n";

echo "<script type=\"text/javascript\">\n";
 echo "<!--\n";
  echo "function set_form_field(var_name, var_val){
        document.forms['form_nav'].elements[var_name].value = var_val;
        }\n";
  echo "function submit_form(var_name, var_val){
            document.form_nav.submit();
        }\n";
  echo "function show_searchboxes(){
            if(document.getElementById(\"searchboxes_1\").style.display == 'none'){
                action='inline';
                var img_src='images/arrows_up.gif'
                //Show Boxes
                for (var i = 1 ; i < ".($count_searchboxes+1)."; i++){
                    document.getElementById(\"searchboxes_\"+i).style.display = action;
                }
                //Show Image
                document.getElementById('arrows_searchboxes').src=img_src;
            }else{
                document.getElementById(\"link_searchboxes\").href='".$_SERVER["REQUEST_URI"]."';
            }
        }\n";
 echo "//-->\n";
echo "</script>\n";

 echo "<input type=\"hidden\" name=\"dir\" value=\"".$dir."\" />\n";
 echo "<input type=\"hidden\" name=\"sort\" value=\"".$sort."\" />\n";
 echo "<input type=\"hidden\" name=\"page_count\" value=\"".$page_count."\" />\n";
 echo "<input type=\"hidden\" name=\"show_all\" value=\"".$show_all."\" />\n";
 echo "<input type=\"hidden\" name=\"headline_addition\" value=\"".$_REQUEST["headline_addition"]."\" />\n";

echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";

echo "<tr>\n";
 echo $headline_1;
echo "</tr>\n";

  echo "<tr style=\"width:100%\">\n";
   echo $headline_2;
  echo "</tr>\n";


//Table body
if ($myrow = mysql_fetch_array($result)){
    do{
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        echo " <tr>\n";
        foreach($query_array["fields"] as $field){

            if($field["show"]!="n"){
               $a_misc = "";

               //Convert the array-values to local variables
               while (list ($key, $val) = each ($myrow)) {
                   $$key=$val;
               }

                //Generating the link
                //Does the field has an own link? Otherwise take the standard-link of the view
                if($field["get"]["file"]!=""){
                    $get_array=$field["get"];
                }else{
                    $get_array=$query_array["get"];
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
                $field_align = "";
                if ($field["align"] <> "") { $field_align = " align=\"".$field["align"]."\""; }
                echo "  <td bgcolor=\"" . $bgcolor . "\"$field_align style=\"padding-right:10px;\">";
                //Special field-converting
                if($field["name"]=="system_os_name"){
                    $show_value=determine_os($myrow[$field["name"]]);
                }elseif($field["name"]=="system_timestamp"){
                    $show_value=return_date($myrow[$field["name"]]);
                }elseif($field["name"]=="software_first_timestamp" OR
                        $field["name"]=="software_timestamp" OR
                        $field["name"]=="system_audits_timestamp"){
                    $show_value=return_date_time($myrow[$field["name"]]);
                }elseif($field["name"]=="system_system_type"){
                    $show_value=determine_img($myrow["system_os_name"],$myrow[$field["name"]]);
                }elseif($field["name"]=="other_type"){
                    $show_value="<img src=\"images/o_" .$myrow[$field["name"]]. ".png\" alt=\"\" border=\"0\" width=\"16\" height=\"16\"  />";
                }elseif($field["name"]=="other_ip_address"){
                    $show_value=ip_trans($myrow[$field["name"]]);
                }elseif($field["name"]=="delete"){
                    $show_value="<img src=\"images/button_delete_out.png\" name=\"button" . $myrow["other_id"] . "\" width=\"58\" height=\"22\" border=\"0\" alt=\"\" />";
                    $a_misc=" onmouseover=\"document.button" . $myrow["other_id"] . ".src='images/button_delete_over.png'\" ";
                    $a_misc.=" onmousedown=\"document.button" . $myrow["other_id"] . ".src='images/button_delete_down.png'\"";
                    $a_misc.=" onmouseout=\"document.button" . $myrow["other_id"] . ".src='images/button_delete_out.png'\"";
                }elseif($field["name"]=="startup_location"){
                    if (substr($myrow[$field["name"]],0,2) == "HK"){
                        $show_value = "Registry";
                    }
                }elseif($field["name"]=="percentage"){
                    $show_value=$myrow[$field["name"]]." %";
                }else{
                    $show_value=$myrow[$field["name"]];
                }

                if($field["link"]=="y"){
                    $field_target = "";
                    if ($get_array["target"] <> "") { $field_target = " target=\"".$get_array["target"]."\""; }
                    echo "<a href=\"".$link_uri."\"$field_target title=\"".__($get_array["title"])."\" onClick=\"".$get_array["onClick"]."\" $a_misc>";
                }
                if($field["image"]!=""){
                    echo "<img src=\"".$field["image"]."\" border=\"0\" alt=\"\" />";
                }else{
                    echo $show_value;
                }
                if($field["link"]=="y"){
                    echo "</a>\n";
                }
                //Is there a help entry?
                if($field["help"]!=""){
                    if(substr($field["help"],0,1)=="%"){
                        $value=substr($field["help"],1);
                        $help=$$value;
                    }else{
                        $help=$field["help"];
                    }
                    echo "&nbsp;<a href=\"#\" onClick=\"alert('".addslashes(str_replace("\"","",__($help)))."')\">?</a>";
                }
                echo "</td>\n";
            }
        }
        echo "<td bgcolor=\"" . $bgcolor . "\" >\n";
        echo "</td>\n";
        echo " </tr>\n";
    }while ($myrow = mysql_fetch_array($result));

} else {
  echo "<tr><td colspan=\"4\">".__("No Results")."</td></tr>\n";
}

echo "</table>\n";

echo "</form>\n";
echo "</div>\n";

echo __("This Page was generated in")." ".number_format((microtime_float()-$time_start),2)." ". __("Seconds").".";

echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
