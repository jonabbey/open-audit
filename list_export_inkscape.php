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
header("Content-Disposition: inline; filename=\"Open-Audit_".$_REQUEST["view"]."_network_inkscapegram.svg\"");

//
// Setup the format of the .inkscape page. This is VERY crude, we should create functions to allow proper control of all elements on the page,
// and a setup page to allow contorl over this ... OOPS (AJH)
//
$inkscape_page_setup_1 = '<?xml version="1.0" encoding="UTF-8"?>
<!-- Created with Inkscape (http://www.inkscape.org/) -->
<svg
   xmlns:dc="http://purl.org/dc/elements/1.1/"
   xmlns:cc="http://web.resource.org/cc/"
   xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns="http://www.w3.org/2000/svg"
   xmlns:xlink="http://www.w3.org/1999/xlink"
   xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
   xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
   width="1052.3622"
   height="744.09448"
   id="svg2"
   sodipodi:version="0.32"
   inkscape:version="0.45.1"
   version="1.0"
   sodipodi:docbase="W:\htdocs\openaudit\images"
   sodipodi:docname="OA Inkscape drawing.svg"
   inkscape:output_extension="org.inkscape.output.svg.inkscape">
  <defs
     id="defs4">
    <marker
       inkscape:stockid="inkscapemondS"
       orient="auto"
       refY="0.0"
       refX="0.0"
       id="inkscapemondS"
       style="overflow:visible">
      <path
         id="path3361"
         d="M 0,-7.0710768 L -7.0710894,0 L 0,7.0710589 L 7.0710462,0 L 0,-7.0710768 z "
         style="fill-rule:evenodd;stroke:#000000;stroke-width:1.0pt;marker-start:none"
         transform="scale(0.2)" />
    </marker>
  </defs>
    <sodipodi:namedview
     id="base"
     pagecolor="#ffffff"
     bordercolor="#666666"
     borderopacity="1.0"
     gridtolerance="10000"
     guidetolerance="10"
     objecttolerance="10"
     inkscape:pageopacity="0.0"
     inkscape:pageshadow="2"
     inkscape:zoom="8.3430265"
     inkscape:cx="49.967157"
     inkscape:cy="720.5933"
     inkscape:document-units="px"
     inkscape:current-layer="layer1"
     width="1052.3622px"
     height="744.09449px"
     showgrid="true"
     inkscape:object-bbox="true"
     inkscape:object-points="true"
     inkscape:object-nodes="true"
     inkscape:window-width="1280"
     inkscape:window-height="977"
     inkscape:window-x="-4"
     inkscape:window-y="-4" />
  <metadata
     id="metadata7">
    <rdf:RDF>
      <cc:Work
         rdf:about="">
        <dc:format>image/svg+xml</dc:format>
        <dc:type
           rdf:resource="http://purl.org/dc/dcmitype/StillImage" />
      </cc:Work>
    </rdf:RDF>
  </metadata>
  <g
     inkscape:label=/"Layer '.($inkscape_current_image_object_id+1).'/"
     inkscape:groupmode="layer"
     id="layer'.($inkscape_current_image_object_id+1).'">
  ';
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
           $inkscape_current_obj_text=$inkscape_current_obj_text."ip: ".$myrow["net_ip_address"]."\n";
            } else {}
            
            if ($inkscape_show_system_net_user_name== "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."User: ".$myrow["net_user_name"]."\n";
            } else {}
            
            if ($inkscape_show_system_net_domain == "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."Domain: ".$myrow["net_domain"]."\n";
            } else {}
            
            if ($inkscape_show_system_system_vendor == "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."Vendor: ".$myrow["system_vendor"]."\n";
            } else {}
            
            if ($inkscape_show_system_system_model == "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."Model: ".$myrow["system_model"]."\n";
            } else {}
            
            if ($inkscape_show_system_system_id_number == "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."Serial #: ".$myrow["system_id_number"]."\n";
            } else {}
            
            if ($inkscape_show_system_system_memory == "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."Memory: ".$myrow["system_memory"]." Mb \n";
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
           $inkscape_current_obj_text=$inkscape_current_obj_text."Name: ".$myrow["other_network_name"]."\n";
            } else {}

            if ($inkscape_show_system_net_ip_address == "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."ip: ".$myrow["other_ip_address"]."\n";
            } else {}
            
            if ($inkscape_show_other_mac_address== "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."MAC: ".$myrow["other_mac_address"]."\n";
            } else {}
            
            if ($inkscape_show_other_description== "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."Description: ".$myrow["other_description"]."\n";
            } else {}
            
            if ($inkscape_show_other_location== "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."Location: ".$myrow["other_location"]."\n";
            } else {}
            
            if ($inkscape_show_other_serial== "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."Serial: ".$myrow["other_serial"]."\n";
            } else {}
            
            if ($inkscape_show_other_model== "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."Model: ".$myrow["other_model"]."\n";
            } else {}
            
            if ($inkscape_show_other_type== "y"){
           $inkscape_current_obj_text=$inkscape_current_obj_text."Type: ".$myrow["other_type"]."\n";
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
            echo '         <g
       id="g'.($inkscape_current_image_object_id+2293).'"
       transform="translate(-20,-9)">
      <image
         xlink:href="W:\htdocs\OpenAudit\images\computer_2.png"
         sodipodi:absref="W:\htdocs\OpenAudit\images\computer_2.png"
         width="16"
         height="16"
         id="image'.($inkscape_current_image_object_id+2248).'"
         x="30"
         y="18.094482" />
      <text
         id="text2251"
         y="'.$inkscape_current_object_y.'"
         x="'.$inkscape_current_object_x.'"
         style="font-size:3px;text-align:center;text-anchor:middle"
         xml:space="preserve"><tspan
           y="'.$inkscape_current_object_y.'"
           x="'.$inkscape_current_object_x.'"
           id="tspan'.($inkscape_current_image_object_id+2253).'"
           sodipodi:role="line">Computer '.$inkscape_current_image_object_id.'</tspan><tspan
           id="tspan'.($inkscape_current_image_object_id+2255).'"
           y="'.$inkscape_current_object_y.'"
           x="'.$inkscape_current_object_x.'"
           sodipodi:role="line">Domain</tspan><tspan
           id="tspan'.($inkscape_current_image_object_id+2283).'"
           y="'.$inkscape_current_object_y.'"
           x="'.$inkscape_current_object_x.'"
           sodipodi:role="line">User</tspan><tspan
           id="tspan'.($inkscape_current_image_object_id+2257).'"
           y="'.$inkscape_current_object_y.'"
           x="'.$inkscape_current_object_x.'"
           sodipodi:role="line" /></text>
    </g>
    <path
       style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:0.30000001;stroke-linecap:round;stroke-linejoin:round;marker-start:url(#DiamondS);marker-mid:url(#DiamondS);marker-end:url(#DiamondS);stroke-miterlimit:4;stroke-dasharray:0.9, 0.3;stroke-dashoffset:0;stroke-opacity:1;display:inline"
       d="M 26,30.81839 L 34.999289,39.074548"
       id="path'.($inkscape_current_image_object_id+2301).'"
       inkscape:connector-type="polyline"
       inkscape:connection-start="#g'.($inkscape_current_image_object_id+2293).'" />
  </g>
  ';
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

/*
// Work in progress
//Looks to the image folder returns the files, no subdirectories. Creates an object per file.

$dh = opendir($inkscape_image_folder);
while (false !== ($file = readdir($dh))) {
//Don't list subdirectories
if (!is_dir("$dirpath/$file")) {
//Create a Text string to add to the object (truncate the file extension and capitalize the first letter)
$inkscape_object_text=  htmlspecialchars(ucfirst(preg_replace('/\..*$/', '', $file)));
}


*/

// Close Layer and Document
echo '  </svg>';
// Thats all folks


?>
