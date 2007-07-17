<?php
//
/**
*
* @version $Id: list_export_dia.php  14th July 2007
*
* @author The Open Audit Developer Team
* @objective DIA Diagram Creator Page for Open Audit.
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
//
include_once("include_config.php");
include_once("include_functions.php");
include_once("include_lang.php");
$time_start = microtime_float();


//MySQL-Connect
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password) or die('Could not connect: ' . mysql_error());
mysql_select_db($mysql_database,$db);

//

//Some Config for Layout 
//
$dia_image_folder='W:\\htdocs\\openaudit\\images\\';
//
// Page Setup  (FIXME this should be an array (AJH))
//
$dia_background_name="background";
$dia_background_colour="#FFFFFF";
// 
$dia_pagebreak_colour="#000099";
$dia_paper="paper";
$dia_paper_name="#A4#";
$dia_tmargin="2.8";
$dia_bmargin="2.8";
$dia_lmargin="2.8";
$dia_rmargin="2.8";
$dia_is_portrait="true";
$dia_scaling="1";
$dia_fitto="false";
$dia_grid="grid";
$dia_grid_type="grid";
$dia_grid_width_x="1";
$dia_grid_width_y="1";
$dia_grid_visible_x="1";
$dia_grid_visible_y="1";
$dia_grid_lines_colour="#D8E5E5";
$dia_guides="guides";
$dia_guides_hguides="hguides";
$dia_guides_vguides="vguides";
$dia_background_name="Background";
$dia_background_visible="true";
// End of Page Setup Settings

//Config for Image 0 Equipmnet Objects (FIXME this should be an array (AJH))
//
$dia_obj_image_0_type="Standard - Image";
$dia_obj_image_0_version="0";
// Set an ID, this will be controlled programatically, as each element is created.
// *** Caution *** The Object ID Starts with a capital "O" and not a Zero so the following line is OHH ZERO 
// (I spent ages trying to track this foible down (AJH))
$dia_obj_image_0_id="O0";
//
$dia_obj_image_default_image="laptop_l.png";
// Position (this will also be controlled programatically, so these are just the defaults)
$dia_obj_image_0_pos_x=0.95;
$dia_obj_image_0_pos_y=1.1;
// Blob Size 
$dia_obj_image_0_bb_x1=0.9;
$dia_obj_image_0_bb_y1=1.05;
$dia_obj_image_0_bb_x2=3.0;
$dia_obj_image_0_bb_y2=2.9;
// Corners
$dia_obj_elem_corner_x=0.95;
$dia_obj_elem_corner_y=1.1;
// Width & Height
$dia_obj_elem_width=2;
$dia_obj_elem_height=1.75;
// Properties
$dia_obj_draw_border="false";
$dia_obj_keep_aspect="true";
// End of Image  0 Settings

//Config for Text Element 0 Label under device
//
$dia_obj_text_0_type="Standard - Text";
$dia_obj_text_0_version="1";
// *** Caution *** The Object ID Starts with a capital "O" and not a Zero so the following line is OHH ONE and not Zero One 
// (I spent ages trying to track this foible down (AJH))
$dia_obj_text_0_id="O1";
//
$dia_obj_text_0_pos_x=0.85;
$dia_obj_text_0_pos_y=3.85;
//
$dia_obj_text_0_bb_x1=0.85;
$dia_obj_text_0_bb_y1=3.20;
$dia_obj_text_0_bb_x2=4.89;
$dia_obj_text_0_bb_y2=4.29;
//
$dia_obj_text_0_text="text";
$dia_obj_text_0_string="#DEFAULT #";
$dia_obj_text_0_font="font";
$dia_obj_text_0_font_family="courier";
$dia_obj_text_0_font_style=40;
$dia_obj_text_0_font_name="Courier";
$dia_obj_text_0_font_height=0.4;
// 
$dia_obj_text_0_font_pos="pos";
$dia_obj_text_0_font_pos_x=0.85;
$dia_obj_text_0_font_pos_y=0.85;
//
$dia_obj_text_0_font_colour="#000000";
//
$dia_obj_text_0_font_alignment=0;
$dia_obj_text_0_valign="3";
// End Config Text Element 0


// Config for Line Element 0 Zig Zag Line Connector
//
$dia_obj_line_0_type="Standard - ZigZagLine";
$dia_obj_line_0_version="1";
//
// *** Caution *** The Object ID Starts with a capital "O" and not a Zero so the following line is OHH TWO 
// (I spent ages trying to track this foible down (AJH))
$dia_obj_line_0_id="O2";
// Start
$dia_obj_line_0_pos_x=3.05;
$dia_obj_line_0_pos_y=1.97;
// Blob (Box)
$dia_obj_line_0_bb_x1=3.0;
$dia_obj_line_0_bb_y1=1.475;
$dia_obj_line_0_bb_x2=6.95;
$dia_obj_line_0_bb_y2=3.45;
//
// Orth Points
$dia_obj_line_0_orth_points_x1=3.05;
$dia_obj_line_0_orth_points_y1=1.975;
$dia_obj_line_0_orth_points_x2=4.975;
$dia_obj_line_0_orth_points_y2=1.975;
$dia_obj_line_0_orth_points_x3=4.975;
$dia_obj_line_0_orth_points_y3=2.95;
$dia_obj_line_0_orth_points_x4=6.9;
$dia_obj_line_0_orth_points_y4=2.95;
//
// Orth Orientation
$dia_obj_line_0_orth_orient_1=0;
$dia_obj_line_0_orth_orient_2=1;
$dia_obj_line_0_orth_orient_3=0;
//
// Autorouting
$dia_obj_line_0_autorouting="true";
//
// Start Arrow
$dia_obj_line_0_start_arrow=5;
$dia_obj_line_0_start_arrow_length=0.5;
$dia_obj_line_0_start_arrow_width=0.5;
//
// End Arrow
$dia_obj_line_0_end_arrow=5;
$dia_obj_line_0_end_arrow_length=0.5;
$dia_obj_line_0_end_arrow_width=0.5;
//
// Connection properties
$dia_obj_line_0_connection_handle="0";
// Remember OHH Zero not Zero Zero (AJH)
$dia_obj_line_0_connection_handle_to="O0";
$dia_obj_line_0_connection_handle_connection="8";
//
// End of Line Element 0 config

// Config for starting point for first object
$dia_current_object_number=0;
$dia_current_object_x=0.5;
$dia_current_object_y=0.5;
$dia_object_spacing_x=0;
$dia_object_spacing_y=0;
$dia_object_num_columns=1;
$dia_newline= "\n";

//
// Begin OA stuff to make list and create diagram from list

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

// Set the mime header for a Diagram
//
header("Content-Type: application/vnd.dia-win-remote");
header("Content-Disposition: inline; filename=\"Open-Audit_".$_REQUEST["view"]."_network_diagram.dia\"");

// FIXME (AJH)
// The next section uses inline text, but would read better as a set of echo statements, but this broke the parser, I will need to investigate later.
//

//
// String to setup the page
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
  <dia:group>
  ';
  
  //
  // Setup the image 
  //
  
  $dia_page_image_1 ='      <dia:object type="'.$dia_obj_image_0_type.'" version="'.$dia_obj_image_0_version.'" id="'.$dia_obj_image_0_id.'">
        <dia:attribute name="obj_pos">
          <dia:point val="'.$dia_obj_image_0_pos_x.','.$dia_obj_image_0_pos_y.'"/>
        </dia:attribute>
        <dia:attribute name="obj_bb">
          <dia:rectangle val="'.$dia_obj_image_0_bb_x1.','.$dia_obj_image_0_bb_y1.';'.$dia_obj_image_0_bb_x2.','.$dia_obj_image_0_bb_y2.'"/>
        </dia:attribute>
        <dia:attribute name="elem_corner">
          <dia:point val="'.$dia_obj_elem_corner_x.','.$dia_obj_elem_corner_y.'"/>
        </dia:attribute>
        <dia:attribute name="elem_width">
          <dia:real val="'.$dia_obj_elem_width.'"/>
        </dia:attribute>
        <dia:attribute name="elem_height">
          <dia:real val="'.$dia_obj_elem_height.'"/>
        </dia:attribute>
        <dia:attribute name="draw_border">
          <dia:boolean val="'.$dia_obj_draw_border.'"/>
        </dia:attribute>
        <dia:attribute name="keep_aspect">
          <dia:boolean val="'.$dia_obj_keep_aspect.'"/>
        </dia:attribute>
        <dia:attribute name="file">
          <dia:string>#'.$dia_image_folder.$dia_obj_image_default_image.'#</dia:string>
        </dia:attribute>
      </dia:object>
      ';
      
// 
// Set up the text label
//
   $dia_page_text_1 = '      <dia:object type="'.$dia_obj_text_0_type.'" version="'.$dia_obj_text_0_version.'" id="'.$dia_obj_text_0_id.'">
        <dia:attribute name="obj_pos">
          <dia:point val="'.$dia_obj_text_0_pos_x.','.$dia_obj_text_0_pos_y.'"/>
        </dia:attribute>
        <dia:attribute name="obj_bb">
          <dia:rectangle val="'.$dia_obj_text_0_bb_x1.','.$dia_obj_text_0_bb_y1.';'.$dia_obj_text_0_bb_x2.','.$dia_obj_text_0_bb_y2.'"/>
        </dia:attribute>
        <dia:attribute name="'.$dia_obj_text_0_text.'">
          <dia:composite type="text">
            <dia:attribute name="string">
              <dia:string>#'.$dia_obj_text_0_string.'#</dia:string>
            </dia:attribute>
            <dia:attribute name="'.$dia_obj_text_0_font.'">
              <dia:font family="'.$dia_obj_text_0_failiky.'" style="'.$dia_obj_text_0_font_style.'" name="'.$dia_obj_text_0_font_name.'"/>
            </dia:attribute>
            <dia:attribute name="height">
              <dia:real val="'.$dia_obj_text_0_font_height.'"/>
            </dia:attribute>
            <dia:attribute name="'.$dia_obj_text_0_font_pos.'">
              <dia:point val="'.$dia_obj_text_0_font_pos_x.','.$dia_obj_text_0_font_pos_y.'"/>
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
    ';

//
// Set up the zigzag line
//
    
    $dia_page_line_1 = '    <dia:object type="'.$dia_obj_line_0_type.'" version="'.$dia_obj_line_0_version.'" id="'.$dia_obj_line_0_id.'">
      <dia:attribute name="obj_pos">
        <dia:point val="'.$dia_obj_line_0_pos_x.','.$dia_obj_line_0_pos_y.'"/>
      </dia:attribute>
      <dia:attribute name="obj_bb">
        <dia:rectangle val="'.$dia_obj_line_0_bb_x1.','.$dia_obj_line_0_bb_y1.';'.$dia_obj_line_0_bb_x2.','.$dia_obj_line_0_bb_y2.'"/>
      </dia:attribute>
      <dia:attribute name="orth_points">
        <dia:point val="'.$dia_obj_line_0_orth_points_x1.','.$dia_obj_line_0_orth_points_y1.'"/>
        <dia:point val="'.$dia_obj_line_0_orth_points_x2.','.$dia_obj_line_0_orth_points_y2.'"/>
        <dia:point val="'.$dia_obj_line_0_orth_points_x3.','.$dia_obj_line_0_orth_points_y3.'"/>
        <dia:point val="'.$dia_obj_line_0_orth_points_x4.','.$dia_obj_line_0_orth_points_y4.'"/>
      </dia:attribute>
      <dia:attribute name="orth_orient">
        <dia:enum val="'.$dia_obj_line_0_orth_orient_1.'"/>
        <dia:enum val="'.$dia_obj_line_0_orth_orient_2.'"/>
        <dia:enum val="'.$dia_obj_line_0_orth_orient_3.'"/>
      </dia:attribute>
      <dia:attribute name="autorouting">
        <dia:boolean val="'.$dia_obj_line_0_autorouting.'"/>
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
      <dia:connections>
        <dia:connection handle="'.$dia_obj_line_0_connection_handle.'" to="'.$dia_obj_line_0_connection_handle_to.'" connection="'.$dia_obj_line_0_connection_handle_connection.'"/>
      </dia:connections>
    </dia:object>
    ';
//        
$dia_page_close_1 = '  </dia:layer>
</dia:diagram>
';

// Set up the page
echo $dia_page_setup_1;

//Create Objects

//Table body
$dia_current_object_x=.5;
$dia_current_object_y=.5;
if ($myrow = mysql_fetch_array($result)){
    do{
        foreach($query_array["fields"] as $field){
            if($field["show"]!="n"){
            //
            if ($field["head"]=="Hostname"){
            $dia_current_object_y++;
            $dia_current_object_y++; 
            
  $dia_page_image_1 ='      <dia:object type="'.$dia_obj_image_0_type.'" version="'.$dia_obj_image_0_version.'" id="'.$dia_obj_image_0_id.'">
        <dia:attribute name="obj_pos">
          <dia:point val="'.$dia_obj_image_0_pos_x.','.$dia_obj_image_0_pos_y.'"/>
        </dia:attribute>
        <dia:attribute name="obj_bb">
          <dia:rectangle val="'.$dia_obj_image_0_bb_x1.','.$dia_obj_image_0_bb_y1.';'.$dia_obj_image_0_bb_x2.','.$dia_obj_image_0_bb_y2.'"/>
        </dia:attribute>
        <dia:attribute name="elem_corner">
          <dia:point val="'.$dia_obj_elem_corner_x.','.$dia_obj_elem_corner_y.'"/>
        </dia:attribute>
        <dia:attribute name="elem_width">
          <dia:real val="'.$dia_obj_elem_width.'"/>
        </dia:attribute>
        <dia:attribute name="elem_height">
          <dia:real val="'.$dia_obj_elem_height.'"/>
        </dia:attribute>
        <dia:attribute name="draw_border">
          <dia:boolean val="'.$dia_obj_draw_border.'"/>
        </dia:attribute>
        <dia:attribute name="keep_aspect">
          <dia:boolean val="'.$dia_obj_keep_aspect.'"/>
        </dia:attribute>
        <dia:attribute name="file">
          <dia:string>#'.$dia_image_folder.$dia_obj_image_default_image.'#</dia:string>
        </dia:attribute>
      </dia:object>
      ';
      
// 
// Set up the text label
//
   $dia_page_text_1 = '      <dia:object type="'.$dia_obj_text_0_type.'" version="'.$dia_obj_text_0_version.'" id="'.$dia_obj_text_0_id.'">
        <dia:attribute name="obj_pos">
          <dia:point val="'.$dia_obj_text_0_pos_x.','.$dia_obj_text_0_pos_y.'"/>
        </dia:attribute>
        <dia:attribute name="obj_bb">
          <dia:rectangle val="'.$dia_obj_text_0_bb_x1.','.$dia_obj_text_0_bb_y1.';'.$dia_obj_text_0_bb_x2.','.$dia_obj_text_0_bb_y2.'"/>
        </dia:attribute>
        <dia:attribute name="'.$dia_obj_text_0_text.'">
          <dia:composite type="text">
            <dia:attribute name="string">
              <dia:string>#'.$dia_obj_text_0_string.'#</dia:string>
            </dia:attribute>
            <dia:attribute name="'.$dia_obj_text_0_font.'">
              <dia:font family="'.$dia_obj_text_0_failiky.'" style="'.$dia_obj_text_0_font_style.'" name="'.$dia_obj_text_0_font_name.'"/>
            </dia:attribute>
            <dia:attribute name="height">
              <dia:real val="'.$dia_obj_text_0_font_height.'"/>
            </dia:attribute>
            <dia:attribute name="'.$dia_obj_text_0_font_pos.'">
              <dia:point val="'.$dia_obj_text_0_font_pos_x.','.$dia_obj_text_0_font_pos_y.'"/>
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
    ';

//
// Set up the zigzag line
//
    
    $dia_page_line_1 = '    <dia:object type="'.$dia_obj_line_0_type.'" version="'.$dia_obj_line_0_version.'" id="'.$dia_obj_line_0_id.'">
      <dia:attribute name="obj_pos">
        <dia:point val="'.$dia_obj_line_0_pos_x.','.$dia_obj_line_0_pos_y.'"/>
      </dia:attribute>
      <dia:attribute name="obj_bb">
        <dia:rectangle val="'.$dia_obj_line_0_bb_x1.','.$dia_obj_line_0_bb_y1.';'.$dia_obj_line_0_bb_x2.','.$dia_obj_line_0_bb_y2.'"/>
      </dia:attribute>
      <dia:attribute name="orth_points">
        <dia:point val="'.$dia_obj_line_0_orth_points_x1.','.$dia_obj_line_0_orth_points_y1.'"/>
        <dia:point val="'.$dia_obj_line_0_orth_points_x2.','.$dia_obj_line_0_orth_points_y2.'"/>
        <dia:point val="'.$dia_obj_line_0_orth_points_x3.','.$dia_obj_line_0_orth_points_y3.'"/>
        <dia:point val="'.$dia_obj_line_0_orth_points_x4.','.$dia_obj_line_0_orth_points_y4.'"/>
      </dia:attribute>
      <dia:attribute name="orth_orient">
        <dia:enum val="'.$dia_obj_line_0_orth_orient_1.'"/>
        <dia:enum val="'.$dia_obj_line_0_orth_orient_2.'"/>
        <dia:enum val="'.$dia_obj_line_0_orth_orient_3.'"/>
      </dia:attribute>
      <dia:attribute name="autorouting">
        <dia:boolean val="'.$dia_obj_line_0_autorouting.'"/>
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
      <dia:connections>
        <dia:connection handle="'.$dia_obj_line_0_connection_handle.'" to="'.$dia_obj_line_0_connection_handle_to.'" connection="'.$dia_obj_line_0_connection_handle_connection.'"/>
      </dia:connections>
    </dia:object>
    ';
//   





/*
echo $dia_page_setup_1;
echo $dia_page_image_1;
echo $dia_page_text_1;
echo $dia_page_line_1;
echo $dia_page_close_1;
*/
 }                                                                       
           
                //echo $myrow[$field["name"]];
               
//                echo "\t";
            }
        }
//        echo "\r\n";
    }while ($myrow = mysql_fetch_array($result));
} 

?>
