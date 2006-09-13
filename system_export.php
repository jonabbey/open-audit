<?php
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
$time_start = microtime_float();

// If you would like to have a new View, you have to modify 3 parts:
// -> include_menu_array.php: $menue_array
// -> system_viewdef_X.php: "Table and fields to select and show"
// -> option: include_functions.php: special_field_converting()

include_once("include_config.php");
include_once("include_functions.php");
include_once("include_win_type.php");

//Include PDF-Libaries
define('FPDF_FONTPATH','./lib/fpdf/font/');
require('./lib/fpdf/fpdf.php');

//MySQL-Connect
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password) or die('Could not connect: ' . mysql_error());
mysql_select_db($mysql_database,$db);

//Col-Width and Height
$draw["col_width"][0]=50;
$draw["col_width"][1]=130;
$draw["col_width_horizontal"]=30;
$draw["col_height_1"]=4;
$draw["margin-left"]=15;
$draw["margin-top"]=10;
$draw["margin-right"]=10;
$draw["line-width"]=180;
$draw["font_size_headline_1"]=14;
$draw["font_size_headline_2"]=10;
$draw["font_size_body"]=10;
$draw["font_size_footer"]=6;

//Functions, for a better handling
function pdf_start(){
    //Create PDF
    $pdf=new FPDF();
    $pdf->Open();
    $pdf->SetCompression( TRUE );
    //Font-Color
    $pdf->SetTextColor(0,0,0);
    //Creator
    $pdf->SetCreator('Open-AudIT');
    //Display-Mode
    //$pdf->SetDisplayMode('fullpage');
    //Title
    $pdf->SetTitle('Report');
    //Auto-Page-Break
    $pdf->SetAutoPageBreak( TRUE );
    return $pdf;
}
function pdf_end($pdf){
    //Send PDF
    $pdf->Output();
    $pdf->Close();
    return $pdf;
}
function pdf_draw_headline_1($pdf, $draw, $show_value){
     $pdf->SetFont('Arial','B',$draw["font_size_headline_1"]);
     $pdf->Cell(50,7,$show_value,0,1,'L');
     $pdf->Ln();
     return $pdf;
}
function pdf_draw_headline_2($pdf, $draw, $show_value){
    $pdf->SetFont('Arial','B',$draw["font_size_headline_2"]);
    $pdf->Cell(50,7,$show_value,0,1,'L');
    return $pdf;
}
//Start new Page
function pdf_draw_new_page($pdf, $draw){
    $pdf->AddPage('P');
    $pdf->SetMargins($draw["margin-left"],$draw["margin-top"],$draw["margin-left"]);
    return $pdf;
}
function pdf_draw_footer($pdf, $draw){
    //Print Footer
    $pdf->SetFont('Arial','B',$draw["font_size_footer"]);
    $pdf->SetY(-15);
    $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $pdf->Write(5,date('l, dS \of F Y, h:i:s A'),$url,0,'L');
    $pdf->SetX(100);
    $pdf->Write(5,$pdf->PageNo());
    $pdf->SetX(170);
    $pdf->Write(5,'www.open-audit.org','http://www.open-audit.org');
    return $pdf;
}
function pdf_draw_row($pdf, $draw, $show_value, $width=0){
    $i=0;
    foreach($show_value as $value){
        if(isset($width) AND $width>0){
            $draw["col_width"][$i]=$draw["col_width_horizontal"];
        }
        $pdf->Cell($draw["col_width"][$i],$draw["col_height_1"],$value, '0', 0, 'L');
        $i++;
    }
    return $pdf;
}
function pdf_draw_nl($pdf){
    $pdf->Ln();
    return $pdf;
}
function pdf_draw_line($pdf, $draw){
    $pdf->Line($draw["margin-left"], $pdf->GetY(), ($draw["margin-left"]+$draw["line-width"]), $pdf->GetY());
    return $pdf;
}

//Get the pc's to display
//actually only one
if(isset($_REQUEST["pc"]) AND $_REQUEST["pc"]!=""){
  $pc=$_REQUEST["pc"];
  $_GET["pc"]=$_REQUEST["pc"];
  $sql = "SELECT system_uuid, system_timestamp, system_name FROM system WHERE system_uuid = '$pc' OR system_name = '$pc' ";
  $result = mysql_query($sql, $db);

  $i=0;
  if ($myrow = mysql_fetch_array($result)){
      do{
          $systems_array[$i]=array("pc"=>$myrow["system_uuid"],
                               "system_timestamp"=>$myrow["system_timestamp"],
                              );
          $i++;
      }while ($myrow = mysql_fetch_array($result));
  }
}else{
    $systems_array[0]=array("pc"=>"","system_timestamp"=>"",);
}

//Start PDF
$pdf=pdf_start();

foreach($systems_array as $system){

    //Workaround to get the queries in the viewdef-array get worked
    $_REQUEST["pc"]=$system["pc"];
    $pc=$system["pc"];
    $GLOBAL["system_timestamp"]=$system["system_timestamp"];

    //Include the view-definition
    if(isset($_REQUEST["view"]) AND $_REQUEST["view"]!=""){
        $include_filename = "system_viewdef_".$_REQUEST["view"].".php";
    }else{
        $include_filename = "system_viewdef_summary.php";
    }
    if(is_file($include_filename)){
        include($include_filename);
        $viewdef_array=$query_array;
    }else{
        die("FATAL: Could not find view $include_filename");
    }
    //Convert GET[category] to an array
    if(isset($_REQUEST["category"]) AND $_REQUEST["category"]!=""){
        $array_category=explode(",",$_REQUEST["category"]);
    }

    //Delete undisplayed categories from $query_array, if a certain category is given
    if(isset($array_category) AND is_array($array_category) AND $_REQUEST["category"]!=""){
        reset($query_array["views"]);
        while (list ($viewname, $viewdef_array) = @each ($query_array["views"])) {
            if(!in_array($viewname, $array_category)){
                unset($query_array["views"][$viewname]);
            }
        }
    }

     if(!isset($headline_addition) OR $headline_addition==""){
         $headline_addition="";
     }

     //Create new Page
     $pdf=pdf_draw_new_page($pdf, $draw);
     if(isset($query_array["name"]) AND $query_array["name"]!=""){
     }else{
         $query_array["name"]="";
     }
     //Headline on the first Page
     $pdf=pdf_draw_headline_1($pdf, $draw, $query_array["name"]." ".$headline_addition);

    //Show each Category
    reset($query_array["views"]);
    while (list ($viewname, $viewdef_array) = @each ($query_array["views"])) {
        if(!isset($viewdef_array["print"]) OR $viewdef_array["print"]!="n"){

            //Executing Query
            $sql=$viewdef_array["sql"];
            $result=mysql_query($sql, $db);
            if(!$result) { echo "<br>".__("Fatal Error").":<br><br>".$sql."<br><br>".mysql_error()."<br><br>";
                           echo "<pre>";
                           echo "REQUEST:<br>";
                           print_r($_REQUEST);
                           echo "VIEWDEF:<br>";
                           print_r($viewdef_array);
                           die();
                         };
            $this_page_count = mysql_num_rows($result);

            //Add new page, if there is not enought space to display the next category
            $needed_height=count($viewdef_array["fields"]) * $this_page_count * ($draw["col_height_1"]/10) + $pdf->GetY();
            if($needed_height>240){
                $pdf=pdf_draw_footer($pdf, $draw);
                $pdf=pdf_draw_new_page($pdf, $draw);
                $pdf=pdf_draw_headline_1($pdf, $draw, "");
            }

            //Headline of each category
            if(isset($viewdef_array["headline"]) AND $viewdef_array["headline"]!=""){
                $pdf=pdf_draw_headline_2($pdf, $draw, $viewdef_array["headline"]);
            }

            //Body
            $pdf->SetFont('Arial','',8);
            $pdf->SetDrawColor(200,200,200);

            //IF Horizontal Table-Layout
            if(isset($viewdef_array["table_layout"]) AND $viewdef_array["table_layout"]=="horizontal"){
                $pdf->SetFont('Arial','B',8);
                foreach($viewdef_array["fields"] as $field){
                    $pdf=pdf_draw_row($pdf, $draw, array($field["head"]), $draw["col_width_horizontal"]);
                }
                $pdf=pdf_draw_nl($pdf);
                $pdf->SetFont('Arial','',8);
            }

            if ($myrow = mysql_fetch_array($result)){
                do{
                    foreach($viewdef_array["fields"] as $field){
                        if( (!isset($field["show"]) OR $field["show"]!="n") AND (!isset($field["print"]) OR $field["print"]="n") ){
                            $show_value_2 = special_field_converting($myrow, $field, $db, "system");
                            $show_value_2 = html_entity_decode ($show_value_2);
                            $show_value_1 = $field["head"];

                            //IF Horizontal Table-Layout
                            if(isset($viewdef_array["table_layout"]) AND $viewdef_array["table_layout"]=="horizontal"){
                                $pdf=pdf_draw_row($pdf, $draw, array($show_value_2), $draw["col_width_horizontal"]);
                                $pdf=pdf_draw_line($pdf, $draw);
                            }else{
                                if(isset($field["head"]) AND $field["head"]!="") {$show_value_1.=":";}
                                $pdf=pdf_draw_row($pdf, $draw, array($show_value_1, $show_value_2));
                                $pdf=pdf_draw_line($pdf, $draw);
                                $pdf=pdf_draw_nl($pdf);
                            }
                        }
                    }
                    $pdf=pdf_draw_line($pdf, $draw);
                    $pdf=pdf_draw_nl($pdf);
                }while ($myrow = mysql_fetch_array($result));

                //IF Horizontal Table-Layout
                if(isset($viewdef_array["table_layout"]) AND $viewdef_array["table_layout"]=="horizontal"){
                    $pdf=pdf_draw_line($pdf, $draw);
                    $pdf=pdf_draw_nl($pdf);
                }
            }else{
                $pdf=pdf_draw_row($pdf, $draw, array(__("No Results"), ""));
                $pdf=pdf_draw_line($pdf, $draw);
                $pdf=pdf_draw_nl($pdf);
                $pdf=pdf_draw_line($pdf, $draw);
                $pdf=pdf_draw_nl($pdf);
            }
        }
    }
}

//Print Footer of last Page
$pdf=pdf_draw_footer($pdf, $draw);
$pdf=pdf_end($pdf);
?>
