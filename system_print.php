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

//Include the view-definition
if(isset($_REQUEST["view"]) AND $_REQUEST["view"]!=""){
    $include_filename = "system_viewdef_".$_REQUEST["view"].".php";
}else{
    $include_filename = "system_viewdef_summary.php";
}
if(is_file($include_filename)){
    include_once($include_filename);
    $viewdef_array=$query_array;
}else{
    die("FATAL: Could not find view $include_filename");
}

//Set GET[pc] to a local variable
if(isset($_GET["pc"]) AND $_GET["pc"]!=""){
    $pc=$_GET["pc"];
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

//Col-Width and Hight
$draw["col_width_1"]=40;
$draw["col_width_2"]=130;
$draw["col_height_1"]=4;
$draw["font_size_headline_1"]=14;
$draw["font_size_headline_2"]=10;
$draw["font_size_body"]=10;
$draw["font_size_footer"]=6;

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
    $pdf->SetMargins(15,10,0);
    return $pdf;
}
function pdf_draw_footer($pdf, $draw){
    //Print Footer
    $pdf->SetFont('Arial','B',$draw["font_size_footer"]);
    $pdf->SetY(-15);
    $pdf->Write(5,date("Y-m-d, H:i"),0,0,'L');
    $pdf->SetX(100);
    $pdf->Write(5,$pdf->PageNo());
    $pdf->SetX(170);
    $pdf->Write(5,'www.open-audit.org','http://www.open-audit.org');
    return $pdf;
}
function pdf_draw_row($pdf, $draw, $show_value_1, $show_value_2){
    $pdf->Cell($draw["col_width_1"],$draw["col_height_1"],$show_value_1,'B',0,'L');
    $pdf->Cell($draw["col_width_2"],$draw["col_height_1"],$show_value_2,'B',1,'L');
    return $pdf;
}
function pdf_draw_ln($pdf){
    $pdf->Ln();
    return $pdf;
}
//Start PDF
$pdf=pdf_start();

if(1==1){

     if(!isset($headline_addition) OR $headline_addition==""){
         $headline_addition="";
     }

     //Create new Page
     $pdf=pdf_draw_new_page($pdf, $draw);
     //Headline on the first Page
     $pdf=pdf_draw_headline_1($pdf, $draw, $query_array["name"]." ".$headline_addition);

    //Show each Category
    reset($query_array["views"]);
    while (list ($viewname, $viewdef_array) = @each ($query_array["views"])) {

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
        if($needed_height>260){
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
        if ($myrow = mysql_fetch_array($result)){
            do{

                foreach($viewdef_array["fields"] as $field){
                    if(!isset($field["show"]) OR $field["show"]!="n"){

                        $show_value = special_field_converting($myrow, $field, $db, "system");
                        $pdf=pdf_draw_row($pdf, $draw, $field["head"].":", $show_value);
                    }
                }
                $pdf=pdf_draw_ln($pdf);
            }while ($myrow = mysql_fetch_array($result));
        }else{
            $pdf=pdf_draw_row($pdf, $draw, __("No Results"), "");
        }
    }
}

//Print Footer of last Page
$pdf=pdf_draw_footer($pdf, $draw);
$pdf=pdf_end($pdf);
?>
