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
$dia_current_object_x=.5;
$dia_current_object_y=.5;
if ($myrow = mysql_fetch_array($result)){
    do{
        foreach($query_array["fields"] as $field){
            if($field["show"]!="n"){
            //
            $current_object_id= $current_object_id +1;
            //
            if (($field["head"]=="Hostname")or ($field["head"]=="Network Name")){
            //
           $dia_current_object_y = $dia_current_object_y +2;
           //
           
            if (!isset($dia_icon_folder) ){
            $dia_icon_folder = "W:\\htdocs\\openaudit\\images\\";
            } else{}
            
            if ($field["head"]=="Hostname") {
           
            $dia_icon_image = determine_dia_img($myrow["system_os_name"],$myrow[$field["name"]]);

            $dia_this_image = $dia_icon_folder.$dia_icon_image;
            }
            else 
            {
                       
            $dia_icon_image = determine_dia_img($myrow["other_type"],$myrow[$field["other_network_name"]]);

            $dia_this_image = $dia_icon_folder.$dia_icon_image;
            }
        
            echo '          <dia:group>
            <dia:object type="Standard - Image" version="0" id="O'.$current_object_id.'">
      <dia:attribute name="obj_pos">
        <dia:point val="1,1"/>
      </dia:attribute>
      <dia:attribute name="obj_bb">
        <dia:rectangle val="0.95,0.95;2.05,2.05"/>
      </dia:attribute>
      <dia:attribute name="elem_corner">
        <dia:point val="'.$dia_current_object_x.','.$dia_current_object_y.'"/>
      </dia:attribute>
      <dia:attribute name="elem_width">
        <dia:real val="1"/>
      </dia:attribute>
      <dia:attribute name="elem_height">
        <dia:real val="1"/>
      </dia:attribute>
      <dia:attribute name="draw_border">
        <dia:boolean val="false"/>
      </dia:attribute>
      <dia:attribute name="keep_aspect">
        <dia:boolean val="true"/>
      </dia:attribute>
      <dia:attribute name="file">
        <dia:string>#'.$dia_this_image.'#</dia:string>
      </dia:attribute>
    </dia:object>
   <dia:object type="Standard - Text" version="1" id="O'.($current_object_id+2).'">
      <dia:attribute name="obj_pos">
        <dia:point val="'.$dia_current_object_x.','.($dia_current_object_y + 1.5).'"/>
      </dia:attribute>
      <dia:attribute name="obj_bb">
        <dia:rectangle val="1,2.6875;3.225,3.2"/>
      </dia:attribute>
      <dia:attribute name="text">
        <dia:composite type="text">
          <dia:attribute name="string">
            <dia:string>#'.$myrow[$field["name"]].'#</dia:string>
          </dia:attribute>
          <dia:attribute name="font">
            <dia:font family="courier" style="80" name="Courier"/>
          </dia:attribute>
          <dia:attribute name="height">
            <dia:real val="0.40000000000000002"/>
          </dia:attribute>
          <dia:attribute name="pos">
            <dia:point val="'.$dia_current_object_x.','.($dia_current_object_y + 2.0).'"/>
          </dia:attribute>
          <dia:attribute name="color">
            <dia:color val="#a7a7a7"/>
          </dia:attribute>
          <dia:attribute name="alignment">
            <dia:enum val="0"/>
          </dia:attribute>
        </dia:composite>
      </dia:attribute>
      <dia:attribute name="valign">
        <dia:enum val="3"/>
      </dia:attribute>
    </dia:object> 
    </dia:group>
    <dia:object type="Standard - ZigZagLine" version="1" id="O'.($current_object_id+1).'">
      <dia:attribute name="obj_pos">
        <dia:point val="'.($dia_current_object_x + 1).','.($dia_current_object_y + 0.5).'"/>
      </dia:attribute>
      <dia:attribute name="obj_bb">
        <dia:rectangle val="2.04912,1.45;4.05,2.05"/>
      </dia:attribute>
      <dia:attribute name="orth_points">
        <dia:point val="'.($dia_current_object_x + 1).','.($dia_current_object_y + 0.5).'"/>
        <dia:point val="'.($dia_current_object_x + 2).','.($dia_current_object_y + 0.5).'"/>
        <dia:point val="'.($dia_current_object_x + 2).','.($dia_current_object_y + 1.5).'"/>
        <dia:point val="'.($dia_current_object_x + 3).','.($dia_current_object_y + 1.5).'"/>
      </dia:attribute>
      <dia:attribute name="orth_orient">
        <dia:enum val="0"/>
        <dia:enum val="1"/>
        <dia:enum val="0"/>
      </dia:attribute>
      <dia:attribute name="autorouting">
        <dia:boolean val="false"/>
      </dia:attribute>
      <dia:connections>
        <dia:connection handle="0" to="O'.$current_object_id.'" connection="8"/>
      </dia:connections>
    </dia:object>
    
';
            $current_object_id= $current_object_id +3;
 }                                                                       
           
                //echo $myrow[$field["name"]];
               
//                echo "\t";
            }
        }
//        echo "\r\n";
    }while ($myrow = mysql_fetch_array($result));
}
//
// Close Layer and Document
echo '  </dia:layer>
</dia:diagram>';
// Thats all folks


?>
