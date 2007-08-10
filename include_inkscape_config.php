<?php
//
/**
*
* @version $Id: include_inkscape_config.php  17th July 2007
*
* @author The Open Audit Developer Team (Andrew Hull)
* @objective Configuration File for inkscape inkscapegram Creator Page for Open Audit.
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

// Note  Some objects are specified as for example 4.0 rather than 4 in order to ensure that PHP uses the correct type 
// i.e. A Number rather than a text string. 

// $inkscape_image_folder
// Set this to a valid location for your *** Workstation*** and NOT the OA server as it will be the Workstation that open the inkscapegram!
//
// Try $inkscape_image_folder=".\\images" then place a copy of the images folder in the same folder as the inkscapegram, or 
//
// Try \\home\\myuser\\myinkscapegrams or similar on Linix
//
// or for multiple Windows Workstations set to a shared location for example 
//
//$inkscape_image_folder='W:\\htdocs\\openaudit\\images\\';
//
// For Xampp standard installation path, try 
//
//$inkscape_image_folder='C:\\Program Files\\xampp\\htdocs\\openaudit\\images\\';
//
$inkscape_image_folder='W:\\htdocs\\openaudit\\images\\';

// Start offset x/y for first object
$inkscape_object_start_x = 30.5;
$inkscape_object_start_y = 20.5;

// Start Object id best left as zero 
// settype($inkscape_object_start_id,int);
$inkscape_object_start_id = 0;

// Object Spacing incriments (set one or other of x/y to a positive value, to space out horizontaly or vertically)
$inkscape_object_spacing_x=50;
$inkscape_object_spacing_y=40;
//$inkscape_object_spacing_y=2;
// Config for starting point for first object
$inkscape_object_num_columns=1;
$inkscape_newline= "\n";
//
$inkscape_text_other_object_text= '"$myrow[$field[\"name\"]].\"\n\".$myrow[\"other_ip_address\"].\"\n\".$myrow[\"other_description\"]\"';



// Page Setup next... (FIXME this should probably be an array (AJH))
//
$inkscape_background_name="background";
$inkscape_background_colour="#FFFFFF";
// 
$inkscape_pagebreak_colour="#000099";
$inkscape_paper="paper";
$inkscape_paper_name="#A4#";
$inkscape_tmargin="2.8";
$inkscape_bmargin="2.8";
$inkscape_lmargin="2.8";
$inkscape_rmargin="2.8";
$inkscape_is_portrait="false";

//
// Set up page layout for spread of items across and down page.
if ($inkscape_is_portrait == "true") {
        $inkscape_num_across_page= 4.0;
        $inkscape_num_down_page= 6.0;
        } else {
        $inkscape_num_across_page= 6.0;
        $inkscape_num_down_page= 4.0;
        }
// 
$inkscape_grouped_objects= 3.0;


$inkscape_scaling="1";
$inkscape_fitto="false";
$inkscape_grid="grid";
$inkscape_grid_type="grid";
$inkscape_grid_width_x="1";
$inkscape_grid_width_y="1";
$inkscape_grid_visible_x="1";
$inkscape_grid_visible_y="1";
$inkscape_grid_lines_colour="#D8E5E5";
$inkscape_guides="guides";
$inkscape_guides_hguides="hguides";
$inkscape_guides_vguides="vguides";
$inkscape_background_name="Background";
$inkscape_background_visible="true";
// End of Page Setup Settings

//Config for Image 0 Equipmnet Objects (FIXME this should be an array (AJH))
//
$inkscape_obj_image_0_type="Standard - Image";
$inkscape_obj_image_0_version="0";
// Set an ID, this will be controlled programatically, as each element is created.
// *** Caution *** The Object ID Starts with a capital "O" and not a Zero so the following line is OHH ZERO 
// (I spent ages trying to track this foible down (AJH))
$inkscape_obj_image_0_id="O0";
//
$inkscape_obj_image_default_image="laptop_l.png";
// Position (this will also be controlled programatically, so these are just the defaults)
$inkscape_obj_image_0_pos_x=0.3;
$inkscape_obj_image_0_pos_y=1.3;
// Blob Size 
$inkscape_obj_image_0_bb_x1=0.9;
$inkscape_obj_image_0_bb_y1=1.05;
$inkscape_obj_image_0_bb_x2=3.0;
$inkscape_obj_image_0_bb_y2=2.9;
// Corners
$inkscape_obj_image_0_elem_corner_x=0.95;
$inkscape_obj_image_0_elem_corner_y=1.1;
// Width & Height
$inkscape_obj_image_0_elem_width=16;
$inkscape_obj_image_0_elem_height=16;
// Properties
$inkscape_obj_image_0_draw_border="false";
$inkscape_obj_image_0_keep_aspect="true";
// End of Image  0 Settings

//Config for Text Element 0 Label under device
//
$inkscape_obj_text_0_type="Standard - Text";
$inkscape_obj_text_0_version="1";
// *** Caution *** The Object ID Starts with a capital "O" (for Object presumably) and not a Zero so the following line is OHH ONE and not Zero One 
// (I spent ages trying to track this foible down (AJH))
$inkscape_obj_text_0_id="O1";
//
$inkscape_obj_text_0_pos_x=0.85;
$inkscape_obj_text_0_pos_y=3.85;
//
$inkscape_obj_text_0_pos_x_offset=10.2;
$inkscape_obj_text_0_pos_y_offset=20.8;
//
$inkscape_obj_text_0_bb_x1=0.85;
$inkscape_obj_text_0_bb_y1=3.20;
$inkscape_obj_text_0_bb_x2=4.89;
$inkscape_obj_text_0_bb_y2=4.29;
//
$inkscape_obj_text_0_text="text";
$inkscape_obj_text_0_string="#DEFAULT #";
$inkscape_obj_text_0_font="font";
$inkscape_obj_text_0_font_family="arial";
$inkscape_obj_text_0_font_style=0;
$inkscape_obj_text_0_font_name="Helvitica";
$inkscape_obj_text_0_font_height=3;
// 
$inkscape_obj_text_0_font_pos="pos";
$inkscape_obj_text_0_font_pos_x=0.85;
$inkscape_obj_text_0_font_pos_y=0.85;
//
$inkscape_obj_text_0_font_colour="#000000";
//
$inkscape_obj_text_0_font_alignment=1;
$inkscape_obj_text_0_font_valign="3";
// End Config Text Element 0

// What system text fields do we show, 
$inkscape_show_system_net_ip_address = "y";
$inkscape_show_system_net_user_name = "y";
$inkscape_show_system_net_domain = "y";
$inkscape_show_system_system_vendor = "y";
$inkscape_show_system_system_model = "y";
$inkscape_show_system_system_id_number = "y"; 
$inkscape_show_system_system_memory = "y";
// What other item  text fields do we show, 
$inkscape_show_other_network_name = "n";
$inkscape_show_other_ip_address = "y";
$inkscape_show_other_mac_address = "y";
$inkscape_show_other_description = "n";
$inkscape_show_other_serial = "n";
$inkscape_show_other_model = "y";
$inkscape_show_other_type = "n";
$inkscape_show_other_location = "y";
$inkscape_show_other_p_port_name = "y";
$inkscape_show_other_p_share_name = "y";


// Config for Line Element 0 Zig Zag Line Connector
//
$inkscape_obj_line_0_length_x=10;
$inkscape_obj_line_0_length_y=20;


$inkscape_obj_line_0_type="Standard - ZigZagLine";
$inkscape_obj_line_0_version="1";
//
// *** Caution *** Remember the Object ID Starts with a capital "O" for Object and not a Zero so the following line is OHH TWO 
// 
$inkscape_obj_line_0_id="O2";
// Start
$inkscape_obj_line_0_pos_x=3.05;
$inkscape_obj_line_0_pos_y=1.97;
// Blob (Box)
$inkscape_obj_line_0_bb_x1=3.0;
$inkscape_obj_line_0_bb_y1=1.475;
$inkscape_obj_line_0_bb_x2=6.95;
$inkscape_obj_line_0_bb_y2=3.45;
//
// Orth Points
$inkscape_obj_line_0_orth_points_x1=0.70;
$inkscape_obj_line_0_orth_points_y1=0.45;
$inkscape_obj_line_0_orth_points_x2=1.1;
$inkscape_obj_line_0_orth_points_y2=0.75;
$inkscape_obj_line_0_orth_points_x3=1.55;
$inkscape_obj_line_0_orth_points_y3=0.75;
$inkscape_obj_line_0_orth_points_x4=1.55;
$inkscape_obj_line_0_orth_points_y4=2.0;
$inkscape_obj_line_0_orth_points_x5=2.5;
$inkscape_obj_line_0_orth_points_y5=2.0;
$inkscape_obj_line_0_orth_points_x6=2.6;
$inkscape_obj_line_0_orth_points_y6=2.0;
//
// Orth Orientation
$inkscape_obj_line_0_orth_orient_1=1;
$inkscape_obj_line_0_orth_orient_2=0;
$inkscape_obj_line_0_orth_orient_3=1;
$inkscape_obj_line_0_orth_orient_4=0;
$inkscape_obj_line_0_orth_orient_5=1;

//
// Autorouting
$inkscape_obj_line_0_autorouting="true";
//
$inkscape_obj_line_0_line_width=0.00;
// Dot Dash line
$inkscape_obj_line_0_line_style=3;
// Start Arrow
$inkscape_obj_line_0_start_arrow=13;
$inkscape_obj_line_0_start_arrow_length=0.2;
$inkscape_obj_line_0_start_arrow_width=0.2;
//
// End Arrow
$inkscape_obj_line_0_end_arrow=13;
$inkscape_obj_line_0_end_arrow_length=0.2;
$inkscape_obj_line_0_end_arrow_width=0.2;
// Dot-dash line dash length
$inkscape_obj_line_0_dashlength=0.2;
//
// Connection properties
$inkscape_obj_line_0_connection_handle="0";
// Remember OHH Zero not Zero Zero (AJH)
$inkscape_obj_line_0_connection_handle_to="O0";
$inkscape_obj_line_0_connection_handle_connection="8";
//
// End of Line Element 0 config



//
?>