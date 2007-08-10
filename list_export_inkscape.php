<?php

include_once("include_config.php");
include_once("include_functions.php");
include_once("include_lang.php");
include_once("include_inkscape_config.php");

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
    
$inkscape_x_offset = 0;
$inkscape_y_offset = 0;

$inkscape_current_object_id = 0 ;
header("Content-Type: application/vnd.inkscape");
header("Content-Disposition: inline; filename=\"Open-Audit_".$_REQUEST["view"]."_network_inkscape.svg\"");

//
// Setup the format of the .inkscape page. This is VERY crude, we should create functions to allow proper control of all elements on the page,
// and a setup page to allow contorl over this ... OOPS (AJH)
//
$inkscape_page_setup_1 = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<!-- Created with Inkscape (http://www.inkscape.org/) -->
<svg
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns="http://www.w3.org/2000/svg"
   xmlns:xlink="http://www.w3.org/1999/xlink"
   xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
   version="1.0"
   width="1052.3622"
   height="744.09448"
   id="svg2">
  <defs
     id="defs4">
    <marker
       refX="0"
       refY="0"
       orient="auto"
       style="overflow:visible"
       id="DiamondS">
      <path
         d="M 0,-7.0710768 L -7.0710894,0 L 0,7.0710589 L 7.0710462,0 L 0,-7.0710768 z "
         transform="scale(0.2,0.2)"
         style="fill-rule:evenodd;stroke:#000000;stroke-width:1pt;marker-start:none"
         id="path3361" />
    </marker>
  </defs>
  <g
     id="layer1">';
  echo $inkscape_page_setup_1;

//Create Objects


//Table body. This section creates a list of network objects and distributes them across the page
// The exact number across each page depends on the page size and layout.
//

// First we start at the top left of the page,
//
$inkscape_current_object_x = $inkscape_object_start_x;
$inkscape_current_object_y = $inkscape_object_start_y;
// Set the first object ID. 
$inkscape_current_object_id = $inkscape_object_start_id;
//
// Now we create our list from the list on the page that brought us here.
// This is why we need to expand the list 
if ($myrow = mysql_fetch_array($result)){
    do{
        foreach($query_array["fields"] as $field){
            if($field["show"]!="n"){
            //
            $inkscape_current_obj_text='';
            $inkscape_current_obj_text1='';
            $inkscape_current_obj_text2='';
            $inkscape_current_obj_text3='';
            $inkscape_current_obj_text4='';
            $inkscape_current_obj_text5='';
            $inkscape_current_obj_text6='';
            $inkscape_current_obj_text7='';
            //
            if (($field["head"]=="Hostname")or ($field["head"]=="Network Name")){

            if (!isset($inkscape_image_folder) ){
            $inkscape_image_folder = ".\\";
            } else{}
            //
            if ($field["head"]=="Hostname") {
            //
            $inkscape_image_icon = determine_inkscape_img($myrow["system_os_name"],$myrow[$field["name"]]);
            //
            $inkscape_this_image = $inkscape_image_folder.$inkscape_image_icon;
                       //
            $inkscape_current_obj_text="  ".$myrow[$field["name"]]."\n\n";
           // $inkscape_current_obj_text="  ".$myrow[$field["name"]]."\n\n"."ip: ".$myrow["net_ip_address"]."\n"."\n"."User: ".$myrow["net_user_name"]."\n"."Domain: ".$myrow["net_domain"]."\n"."Vendor: ".$myrow["system_vendor"]."\n"."Model: ".$myrow["system_model"]."\n"."Memory: ".$myrow["system_memory"]." Mb";
           
           if ($inkscape_show_system_net_ip_address == "y"){
           $inkscape_current_obj_text1="ip: ".$myrow["net_ip_address"]."\n";
            } else {}
            
            if ($inkscape_show_system_net_user_name== "y"){
           $inkscape_current_obj_text2="User: ".$myrow["net_user_name"]."\n";
            } else {}
            
            if ($inkscape_show_system_net_domain == "y"){
           $inkscape_current_obj_text3="Domain: ".$myrow["net_domain"]."\n";
            } else {}
            
            if ($inkscape_show_system_system_vendor == "y"){
           $inkscape_current_obj_text4="Vendor: ".$myrow["system_vendor"]."\n";
            } else {}
            
            if ($inkscape_show_system_system_model == "y"){
           $inkscape_current_obj_text5="Model: ".$myrow["system_model"]."\n";
            } else {}
            
            if ($inkscape_show_system_system_id_number == "y"){
           $inkscape_current_obj_text6="Serial #: ".$myrow["system_id_number"]."\n";
            } else {}
            
            if ($inkscape_show_system_system_memory == "y"){
           $inkscape_current_obj_text7="Memory: ".$myrow["system_memory"]." Mb \n";
            } else {}
            
            
            }
            else 
            {
            //         
            $inkscape_image_icon = determine_inkscape_img($myrow["other_type"],$myrow["other_type"]);
            //
            $inkscape_this_image = $inkscape_image_folder.$inkscape_image_icon;
            // 
            //."\n".$myrow["other_ip_address"]."\n".$myrow["other_description"];
            $inkscape_current_obj_text=$myrow[$field["name"]]."\n\n";
            //
            
            if ($inkscape_show_other_network_name== "y"){
           $inkscape_current_obj_text1=$inkscape_current_obj_text."Name: ".$myrow["other_network_name"]."\n";
            } else {}

            if ($inkscape_show_system_net_ip_address == "y"){
           $inkscape_current_obj_text2="ip: ".$myrow["other_ip_address"]."\n";
            } else {}
            
            if ($inkscape_show_other_mac_address== "y"){
           $inkscape_current_obj_text3="MAC: ".$myrow["other_mac_address"]."\n";
            } else {}
            
            if ($inkscape_show_other_description== "y"){
           $inkscape_current_obj_text4="Description: ".$myrow["other_description"]."\n";
            } else {}
            
            if ($inkscape_show_other_location== "y"){
           $inkscape_current_obj_text5="Location: ".$myrow["other_location"]."\n";
            } else {}
            
            if ($inkscape_show_other_serial== "y"){
           $inkscape_current_obj_text6="Serial: ".$myrow["other_serial"]."\n";
            } else {}
            
            if ($inkscape_show_other_model== "y"){
           $inkscape_current_obj_text7="Model: ".$myrow["other_model"]."\n";
            } else {}
            
            if ($inkscape_show_other_type== "y"){
           $inkscape_current_obj_text8="Type: ".$myrow["other_type"]."\n";
            } else {}
            
// If its a printer or print server, show the port and share info
            
            if ( ($myrow["other_type"] == "printer") or ($myrow["other_type"] == "print server")) {
            
                 if ($inkscape_show_other_p_port_name== "y"){
                $inkscape_current_obj_text=$inkscape_current_obj_text."Printer Port Name: ".$myrow["other_p_port_name"]."\n";
                    } else {}
            
                 if ($inkscape_show_other_p_share_name== "y"){
                $inkscape_current_obj_text=$inkscape_current_obj_text."Printer Share Name: ".$myrow["other_p_share_name"]."\n";
                    } else {}
            } else {}
            
            
            
            //$inkscape_current_obj_text=eval($inkscape_text_other_object_text);
            
            }
            $inkscape_current_image_object_id = $inkscape_current_object_id;        
            echo '      <g
       transform="translate(-20,-9)"
       id="g'.(($inkscape_current_image_object_id *10)+2).'">
      <image
         xlink:href="'.$inkscape_this_image .'"
         x="'.$inkscape_current_object_x.'"
         y="'.$inkscape_current_object_y.'"
         width="'.$inkscape_obj_image_0_elem_width.'"
         height="'.$inkscape_obj_image_0_elem_height.'"
         id="'.(($inkscape_current_image_object_id *10)+3).'" />
      <text
         x="'.$inkscape_current_object_x.'"
         y="'.$inkscape_current_object_y.'"
         style="font-size:'.$inkscape_obj_text_0_font_height.'px;text-align:center;text-anchor:middle"
         id="text'.(($inkscape_current_image_object_id *10)+4).'"
         xml:space="preserve"><tspan
           x="'.($inkscape_current_object_x+$inkscape_obj_text_0_pos_x_offset).'"
           y="'.($inkscape_current_object_y+$inkscape_obj_text_0_pos_y_offset).'"
           id="tspan'.(($inkscape_current_image_object_id *10)+5).'">'.$inkscape_current_obj_text.'</tspan></text>
    </g>
    <path
       d="M '.$inkscape_current_object_x.','.$inkscape_current_object_y.' L '.($inkscape_current_object_x+$inkscape_obj_line_0_length_x).','.($inkscape_current_object_y+$inkscape_obj_line_0_length_y).'"
       inkscape:connection-start="#g'.(($inkscape_current_image_object_id *10)+2).'"
       style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:0.30000001;stroke-linecap:round;stroke-linejoin:round;marker-start:url(#DiamondS);marker-mid:url(#DiamondS);marker-end:url(#DiamondS);stroke-miterlimit:4;stroke-dasharray:0.9, 0.3;stroke-dashoffset:0;stroke-opacity:1;display:inline"
       id="path'.(($inkscape_current_image_object_id *10)+1).'" />';
/*
<tspan
           x="'.$inkscape_current_object_x.'"
           y="'.$inkscape_current_object_y.'"
           id="tspan'.(($inkscape_current_image_object_id *10)+5).'">Domain</tspan><tspan
           x="'.$inkscape_current_object_x.'"
           y="'.$inkscape_current_object_y.'"
           id="tspan'.(($inkscape_current_image_object_id *10)+6).'">User</tspan><tspan
           x="'.$inkscape_current_object_x.'"
           y="'.$inkscape_current_object_y.'"
           id="tspan'.(($inkscape_current_image_object_id *10)+7).'" />
           */
       
       
       
// Next Object  
 $inkscape_current_object_id += 1;
//    
           
 $inkscape_current_object_id += 1;

 $inkscape_current_object_id += 1;
           
                }                                                                       
//           $inkscape_current_object_id += 4.0;
            }
        }
        // Space out the objects

        //
        $inkscape_x_offset = (($inkscape_current_object_id / $inkscape_grouped_objects) % $inkscape_num_across_page ) ;
        if ($inkscape_x_offset == 0 )  {
        $inkscape_y_offset = $inkscape_y_offset + 1;
        }
        
        /*
        //        Test code block to output a few vars
      echo $inkscape_current_object_id.','  ;
      echo $inkscape_x_offset.','.$inkscape_y_offset;
        //
        */        
                

          $inkscape_current_object_x = $inkscape_object_start_x + ($inkscape_x_offset * $inkscape_object_spacing_x); 
          $inkscape_current_object_y = $inkscape_object_start_y + ($inkscape_y_offset * $inkscape_object_spacing_y);
        //
        //  $inkscape_current_object_x += $inkscape_object_spacing_x; 
        //  $inkscape_current_object_y += $inkscape_object_spacing_y;
        //
    }while ($myrow = mysql_fetch_array($result));

}

// Close Layer and Document
echo '      </g>
</svg>';
// Thats all folks


?>
