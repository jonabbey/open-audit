<?php

include_once("include_config.php");
include_once("include_functions.php");
include_once("include_lang.php");
include_once("include_dia_config.php");

//$time_start = microtime_float();


//MySQL-Connect
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password) or die('Could not connect: ' . mysql_error());
mysql_select_db($mysql_database,$db);

//Some Config fcr Layout


//MySQL-Connect
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password) or die('Could not connect: ' . mysql_error());
mysql_select_db($mysql_database,$db);

//Include the view-definition
if(isset($_REQUEST["view"]) AND $_REQUEST["view"]!=""){
    $include_filename = "list_viewdef_".$_REQUEST["view"].".php";
}else{
    $include_filename = "list_viewdef_all_systems.php";
}
if(is_file($include_filename)){
    include_once($include_filename);
    $viewdef_array=$query_array;
}else{
    die("FATAL: Could not find view $include_filename");
}

    //Executing the Qeuery
    $sql=urldecode($_REQUEST["sql"]);
    $result = mysql_query($sql, $db);
    if(!$result) {die( "<br>".__("Fatal Error").":<br><br>".$sql."<br><br>".mysql_error()."<br><br>" );};
    $this_page_count = mysql_num_rows($result);

$current_object_id = 0 ;
header("Content-Type: application/vnd.dia-win-remote");
header("Content-Disposition: inline; filename=\"Open-Audit_".$_REQUEST["view"]."_network_diagram.dia\"");

//
// Setup Page  this is VERY crude, we should create functions to allow proper control of all elements on the page,
// and a setup page to allow contorl over this ... OOPS (AJH)
//
$dia_page_setup_1 = '<?xml version="1.0" encoding="UTF-8"?>
<dia:diagram xmlns:dia="http://www.lysator.liu.se/~alla/dia/">
  <dia:diagramdata>
    <dia:attribute name="'.$dia_background_name.'">
      <dia:color val="'.$dia_background_colour.'"/>
    </dia:attribute>
    <dia:attribute name="pagebreak">
      <dia:color val="'.$dia_pagebreak_colour.'"/>
    </dia:attribute>
    <dia:attribute name="paper">
      <dia:composite type="'.$dia_paper.'">
        <dia:attribute name="name">
          <dia:string>'.$dia_paper_name.'</dia:string>
        </dia:attribute>
        <dia:attribute name="tmargin">
          <dia:real val="'.$dia_tmargin.'"/>
        </dia:attribute>
        <dia:attribute name="bmargin">
          <dia:real val="'.$dia_bmargin.'"/>
        </dia:attribute>
        <dia:attribute name="lmargin">
          <dia:real val="'.$dia_lmargin.'"/>
        </dia:attribute>
        <dia:attribute name="rmargin">
          <dia:real val="'.$dia_lmargin.'"/>
        </dia:attribute>
        <dia:attribute name="is_portrait">
          <dia:boolean val="'.$dia_is_portrait.'"/>
        </dia:attribute>
        <dia:attribute name="scaling">
          <dia:real val="'.$dia_scaling.'"/>
        </dia:attribute>
        <dia:attribute name="fitto">
          <dia:boolean val="'.$dia_fitto.'"/>
        </dia:attribute>
      </dia:composite>
    </dia:attribute>
    <dia:attribute name="'.$dia_grid.'">
      <dia:composite type="'.$dia_grid_type.'">
        <dia:attribute name="width_x">
          <dia:real val="'.$dia_grid_width_x.'"/>
        </dia:attribute>
        <dia:attribute name="width_y">
          <dia:real val="'.$dia_grid_width_y.'"/>
        </dia:attribute>
        <dia:attribute name="visible_x">
          <dia:int val="'.$dia_grid_visible_x.'"/>
        </dia:attribute>
        <dia:attribute name="visible_y">
          <dia:int val="'.$dia_grid_visible_x.'"/>
        </dia:attribute>
        <dia:composite type="color"/>
      </dia:composite>
    </dia:attribute>
    <dia:attribute name="color">
      <dia:color val="'.$dia_grid_lines_colour.'"/>
    </dia:attribute>
    <dia:attribute name="guides">
      <dia:composite type="'.$dia_guides.'">
        <dia:attribute name="'.$dia_guides_hguides.'"/>
        <dia:attribute name="'.$dia_guides_vguides.'"/>
      </dia:composite>
    </dia:attribute>
  </dia:diagramdata>
  <dia:layer name="'.$dia_background_name.'" visible="'.$dia_background_visible.'">
  ';
  echo $dia_page_setup_1;

//Create Objects

/*
foreach($viewdef_array["fields"] as $field) {
    if($field["show"]!="n"){
   
        echo $field["head"];
        echo "\t";
    }
}
echo "\r\n";

*/
//Table body
//$dia_object_spacing_y=2;
$dia_current_object_x = $dia_object_start_x;
$dia_current_object_y = $dia_object_start_y;
$dia_current_object_id = $dia_object_start_id;
//
if ($myrow = mysql_fetch_array($result)){
    do{
        foreach($query_array["fields"] as $field){
            if($field["show"]!="n"){
            //
            $dia_current_object_id = $dia_current_object_id + 1;
            //
            if (($field["head"]=="Hostname")or ($field["head"]=="Network Name")){

            if (!isset($dia_icon_folder) ){
            $dia_icon_folder = ".\\";
            } else{}
            //
            if ($field["head"]=="Hostname") {
            //
            $dia_icon_image = determine_dia_img($myrow["system_os_name"],$myrow[$field["name"]]);
            //
            $dia_this_image = $dia_icon_folder.$dia_icon_image;
                       //
           $dia_current_obj_text=$myrow[$field["name"]]."\n".$myrow["net_ip_address"];
           
            }
            else 
            {
            //         
            $dia_icon_image = determine_dia_img($myrow["other_type"],$myrow["other_type"]);
            //
            $dia_this_image = $dia_icon_folder.$dia_icon_image;
            // 
            //$dia_current_obj_text=$myrow[$field["name"]]."\n".$myrow["other_ip_address"]."\n".$myrow["other_description"];
            $dia_current_obj_text=eval($dia_text_other_object_text);
            
            }
        
            echo '          <dia:group>
            <dia:object type="'.$dia_obj_image_0_type.'" version="'.$dia_obj_image_0_version.'" id="O'.($dia_current_object_id).'">
        <dia:attribute name="obj_pos">
          <dia:point val="'.$dia_obj_image_0_pos_x.','.$dia_obj_image_0_pos_y.'"/>
        </dia:attribute>
        <dia:attribute name="obj_bb">
          <dia:rectangle val="'.$dia_obj_image_0_bb_x1.','.$dia_obj_image_0_bb_y1.';'.$dia_obj_image_0_bb_x2.','.$dia_obj_image_0_bb_y2.'"/>
        </dia:attribute>
        <dia:attribute name="elem_corner">
        <dia:point val="'.$dia_current_object_x.','.$dia_current_object_y.'"/>
        </dia:attribute>
        <dia:attribute name="elem_width">
          <dia:real val="'.$dia_obj_image_0_elem_width.'"/>
        </dia:attribute>
        <dia:attribute name="elem_height">
          <dia:real val="'.$dia_obj_image_0_elem_height.'"/>
        </dia:attribute>
        <dia:attribute name="draw_border">
          <dia:boolean val="'.$dia_obj_image_0_draw_border.'"/>
        </dia:attribute>
        <dia:attribute name="keep_aspect">
          <dia:boolean val="'.$dia_obj_image_0_keep_aspect.'"/>
        </dia:attribute>
        <dia:attribute name="file"> 
        <dia:string>#'.$dia_this_image.'#</dia:string>
      </dia:attribute>
    </dia:object>
   <dia:object type="'.$dia_obj_text_0_type.'" version="'.$dia_obj_text_0_version.'" id="O'.($dia_current_object_id+2).'">
      <dia:attribute name="obj_pos">
        <dia:point val="'.$dia_current_object_x.','.($dia_current_object_y + 1.5).'"/>
        </dia:attribute>
        <dia:attribute name="obj_bb">
          <dia:rectangle val="'.$dia_obj_text_0_bb_x1.','.$dia_obj_text_0_bb_y1.';'.$dia_obj_text_0_bb_x2.','.$dia_obj_text_0_bb_y2.'"/>
        </dia:attribute>
        <dia:attribute name="'.$dia_obj_text_0_text.'">
          <dia:composite type="text">
            <dia:attribute name="string">
            <dia:string>#'.$dia_current_obj_text.'#</dia:string>
          </dia:attribute>
            <dia:attribute name="'.$dia_obj_text_0_font.'">
              <dia:font family="'.$dia_obj_text_0_failiky.'" style="'.$dia_obj_text_0_font_style.'" name="'.$dia_obj_text_0_font_name.'"/>
            </dia:attribute>
            <dia:attribute name="height">
              <dia:real val="'.$dia_obj_text_0_font_height.'"/>
            </dia:attribute>
            <dia:attribute name="'.$dia_obj_text_0_font_pos.'">
            <dia:point val="'.$dia_current_object_x.','.($dia_current_object_y + 2.0).'"/>
          </dia:attribute>
            <dia:attribute name="color">
              <dia:color val="'.$dia_obj_text_0_font_colour.'"/>
            </dia:attribute>
            <dia:attribute name="alignment">
              <dia:enum val="'.$dia_obj_text_0_font_alignment.'"/>
            </dia:attribute>
          </dia:composite>
        </dia:attribute>
        <dia:attribute name="valign">
          <dia:enum val="'.$dia_obj_text_0_font_valign.'"/>
        </dia:attribute>
      </dia:object>
    </dia:group>
    <dia:object type="'.$dia_obj_line_0_type.'" version="'.$dia_obj_line_0_version.'" id="O'.($dia_current_object_id + 1.0).'">
      <dia:attribute name="obj_pos">
        <dia:point val="'.($dia_current_object_x + 1).','.($dia_current_object_y + 0.5).'"/>
      </dia:attribute>
        <dia:attribute name="obj_bb">
        <dia:rectangle val="'.$dia_obj_line_0_bb_x1.','.$dia_obj_line_0_bb_y1.';'.$dia_obj_line_0_bb_x2.','.$dia_obj_line_0_bb_y2.'"/>
      </dia:attribute>
      <dia:attribute name="orth_points">
        <dia:point val="'.($dia_current_object_x + 1).','.($dia_current_object_y + 0.5).'"/>
        <dia:point val="'.($dia_current_object_x + 2).','.($dia_current_object_y + 0.5).'"/>
        <dia:point val="'.($dia_current_object_x + 2).','.($dia_current_object_y + 1.5).'"/>
        <dia:point val="'.($dia_current_object_x + 3).','.($dia_current_object_y + 1.5).'"/>
      </dia:attribute>
      <dia:attribute name="orth_orient">
        <dia:enum val="'.$dia_obj_line_0_orth_orient_1.'"/>
        <dia:enum val="'.$dia_obj_line_0_orth_orient_2.'"/>
        <dia:enum val="'.$dia_obj_line_0_orth_orient_3.'"/>
      </dia:attribute>
      <dia:attribute name="autorouting">
        <dia:boolean val="'.$dia_obj_line_0_autorouting.'"/>
      </dia:attribute>
      <dia:attribute name="line_width">
        <dia:real val="'.$dia_obj_line_0_line_width.'"/>
      </dia:attribute>
      <dia:attribute name="line_style">
        <dia:enum val="'.$dia_obj_line_0_line_style.'"/>        
      </dia:attribute>
      <dia:attribute name="start_arrow">
        <dia:enum val="'.$dia_obj_line_0_start_arrow.'"/>
      </dia:attribute>
      <dia:attribute name="start_arrow_length">
        <dia:real val="'.$dia_obj_line_0_start_arrow_length.'"/>
      </dia:attribute>
      <dia:attribute name="start_arrow_width">
        <dia:real val="'.$dia_obj_line_0_start_arrow_width.'"/>
      </dia:attribute>
      <dia:attribute name="end_arrow">
        <dia:enum val="'.$dia_obj_line_0_end_arrow.'"/>
      </dia:attribute>
      <dia:attribute name="end_arrow_length">
        <dia:real val="'.$dia_obj_line_0_end_arrow_length.'"/>
      </dia:attribute>
      <dia:attribute name="end_arrow_width">
        <dia:real val="'.$dia_obj_line_0_end_arrow_width.'"/>
      </dia:attribute>
      <dia:attribute name="dashlength">
        <dia:real val="'.$dia_obj_line_0_dashlength.'"/>
      </dia:attribute>
      <dia:connections>
        <dia:connection handle="'.$dia_obj_line_0_connection_handle.'" to="O'.$dia_current_object_id.'" connection="'.$dia_obj_line_0_connection_handle_connection.'"/>
      </dia:connections>
    </dia:object>
';
           $dia_current_object_id = $dia_current_object_id + 3;
                }                                                                       
           
            }
        }
        // Space out the objects
           $dia_current_object_x += $dia_object_spacing_x; 
           $dia_current_object_y += $dia_object_spacing_y;
        //
    }while ($myrow = mysql_fetch_array($result));

}
//
// Close Layer and Document
echo '  </dia:layer>
</dia:diagram>';
// Thats all folks


?>
